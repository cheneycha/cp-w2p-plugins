<?php
/**
 * Temp Folder Lifecycle & Reference-Aware Orphan Sweeper
 *
 * Scans remaining design JSON files (Templates, Active Orders, Cold Orders, Active Saved Designs)
 * into an indexed MySQL table (`wp_nbd_s3_temp_refs`) and safely purges unreferenced old files
 * from `wp-content/uploads/nbdesigner/temp/` via Action Scheduler auto-chaining.
 *
 * @package NBD_S3_Offload
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NBD_S3_Temp_Lifecycle {

    public const TABLE_SUFFIX = 'nbd_s3_temp_refs';
    public const DB_VERSION   = '1.1.0';

    private static bool $table_checked = false;

    /**
     * Initialize hooks and Action Scheduler callbacks.
     */
    public static function init(): void {
        add_action( 'nbd_s3_cron_temp_cleanup', array( __CLASS__, 'run_scheduled_temp_cleanup' ) );
        add_action( 'nbd_s3_chain_temp_index', array( __CLASS__, 'run_temp_index' ), 10, 4 );
        add_action( 'nbd_s3_chain_temp_sweep', array( __CLASS__, 'run_temp_sweep' ), 10, 1 );
    }

    /**
     * Get the full table name for temp file references.
     */
    public static function get_table_name(): string {
        global $wpdb;
        return $wpdb->prefix . self::TABLE_SUFFIX;
    }

    /**
     * Ensure the dedicated temp reference index table exists.
     */
    public static function maybe_create_table(): void {
        if ( self::$table_checked ) {
            return;
        }

        $installed_ver = get_option( 'nbd_s3_temp_refs_db_ver', '' );
        $table_name    = self::get_table_name();

        if ( self::DB_VERSION === $installed_ver ) {
            self::$table_checked = true;
            return;
        }

        global $wpdb;

        // Upgrade migration from v1.0.1-v1.0.4 (before ref_hash column was introduced in v1.0.5)
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}'" ) === $table_name ) {
            $has_ref_hash = $wpdb->get_var( "SHOW COLUMNS FROM {$table_name} LIKE 'ref_hash'" );
            if ( empty( $has_ref_hash ) ) {
                $wpdb->query( "ALTER TABLE {$table_name} ADD COLUMN ref_hash CHAR(32) NOT NULL DEFAULT '' AFTER id" );
                $wpdb->query( "UPDATE {$table_name} SET ref_hash = MD5(CONCAT(LOWER(temp_path), '|', design_folder))" );
                $wpdb->query( "DELETE t1 FROM {$table_name} t1 INNER JOIN {$table_name} t2 ON t1.ref_hash = t2.ref_hash AND t1.id > t2.id" );
                $has_uniq_ref = $wpdb->get_var( "SHOW INDEX FROM {$table_name} WHERE Key_name = 'uniq_ref'" );
                if ( ! empty( $has_uniq_ref ) ) {
                    $wpdb->query( "ALTER TABLE {$table_name} DROP INDEX uniq_ref" );
                }
            }
        }

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            ref_hash CHAR(32) NOT NULL,
            temp_path VARCHAR(512) NOT NULL,
            design_folder VARCHAR(128) NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY uniq_ref_hash (ref_hash),
            KEY idx_temp_path (temp_path(191)),
            KEY idx_design_folder (design_folder(64))
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        update_option( 'nbd_s3_temp_refs_db_ver', self::DB_VERSION );
        self::$table_checked = true;
    }

    /**
     * Get temp directory base path.
     */
    public static function get_temp_base_dir(): string {
        if ( defined( 'NBDESIGNER_TEMP_DIR' ) ) {
            return rtrim( str_replace( '\\', '/', NBDESIGNER_TEMP_DIR ), '/' );
        }
        return rtrim( str_replace( '\\', '/', WP_CONTENT_DIR ), '/' ) . '/uploads/nbdesigner/temp';
    }

    /**
     * Get designs directory base path.
     */
    public static function get_designs_base_dir(): string {
        if ( defined( 'NBDESIGNER_CUSTOMER_DIR' ) ) {
            return rtrim( str_replace( '\\', '/', NBDESIGNER_CUSTOMER_DIR ), '/' );
        }
        return rtrim( str_replace( '\\', '/', WP_CONTENT_DIR ), '/' ) . '/uploads/nbdesigner/designs';
    }

    /**
     * Scheduled daily temp cleanup entry point (incremental index -> sweep).
     */
    public static function run_scheduled_temp_cleanup(): void {
        $settings = NBD_S3_Settings::get_all();
        if ( ! empty( $settings['sync_paused'] ) || empty( $settings['enable_temp_cleanup'] ) ) {
            return;
        }

        delete_transient( 'nbd_s3_temp_sweep_cancelled' );
        self::run_temp_index( 0, false, true, false );
    }

    /**
     * Index all `temp/` references for a single design folder immediately (e.g. right before S3 local purge).
     */
    public static function index_single_design_dir( string $design_folder, string $folder_path ): void {
        if ( '' === $design_folder || ! is_dir( $folder_path ) ) {
            return;
        }
        self::maybe_create_table();
        $extracted = self::extract_temp_refs_from_design_dir( $folder_path );

        // Mark folder as inspected prior to offload so full-purge remote fallback never needs to re-fetch it
        $remote_indexed = (array) get_option( 'nbd_s3_temp_remote_indexed', array() );
        if ( ! isset( $remote_indexed[ $design_folder ] ) ) {
            $remote_indexed[ $design_folder ] = time();
            update_option( 'nbd_s3_temp_remote_indexed', $remote_indexed, 'no' );
        }

        if ( empty( $extracted ) ) {
            return;
        }
        $now_mysql      = current_time( 'mysql', true );
        $rows_to_insert = array();
        foreach ( $extracted as $temp_rel ) {
            $rows_to_insert[] = array( $temp_rel, $design_folder, $now_mysql );
            if ( count( $rows_to_insert ) >= 200 ) {
                self::bulk_insert_refs( $rows_to_insert );
                $rows_to_insert = array();
            }
        }
        if ( ! empty( $rows_to_insert ) ) {
            self::bulk_insert_refs( $rows_to_insert );
        }
    }

    /**
     * Remove all indexed temp references for a specific design folder (e.g. when a non-order saved design is purged).
     */
    public static function delete_refs_for_folder( string $design_folder ): void {
        if ( '' === $design_folder ) {
            return;
        }
        self::maybe_create_table();
        global $wpdb;

        // Safety seatbelt: never remove refs if this folder is also attached to any WooCommerce order item
        $order_meta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$order_meta_table}'" ) === $order_meta_table ) {
            $has_order = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT order_item_id FROM {$order_meta_table} WHERE meta_key IN ('_nbd', '_nbd_item_key') AND meta_value = %s LIMIT 1",
                    $design_folder
                )
            );
            if ( ! empty( $has_order ) ) {
                return;
            }
        }

        $table = self::get_table_name();
        $wpdb->delete( $table, array( 'design_folder' => $design_folder ), array( '%s' ) );
    }

    /**
     * Stage 1: Scan remaining `designs/` JSON files in 500-folder batches and update `wp_nbd_s3_temp_refs`.
     * Supports both full baseline rebuild and fast incremental daily updates.
     */
    public static function run_temp_index( int $offset = 0, bool $full_rebuild = false, bool $chain_sweep = false, bool $is_manual = false ): array {
        @set_time_limit( 300 );
        @ini_set( 'max_execution_time', '300' );

        $settings = NBD_S3_Settings::get_all();

        if ( $is_manual && get_transient( 'nbd_s3_temp_sweep_cancelled' ) ) {
            delete_transient( 'nbd_s3_sync_status' );
            return array( 'success' => false, 'message' => __( 'Temp index cancelled by Pause.', 'nbd-s3-offload' ) );
        }

        if ( ! $is_manual && ! empty( $settings['sync_paused'] ) ) {
            return array( 'success' => false, 'message' => __( 'Skipped: Sync is paused (Safe Mode active).', 'nbd-s3-offload' ) );
        }

        if ( ! $is_manual && empty( $settings['enable_temp_cleanup'] ) ) {
            return array( 'success' => false, 'message' => __( 'Temp folder lifecycle cleanup is disabled.', 'nbd-s3-offload' ) );
        }

        self::maybe_create_table();
        global $wpdb;
        $table       = self::get_table_name();
        $designs_dir = self::get_designs_base_dir();

        if ( ! is_dir( $designs_dir ) ) {
            if ( $chain_sweep ) {
                self::schedule_temp_sweep( $is_manual );
            }
            return array( 'success' => true, 'indexed_folders' => 0, 'indexed_refs' => 0 );
        }

        set_transient( 'nbd_s3_sync_status', array(
            'running'    => true,
            'action'     => 'Temp Reference Indexer (Auto-Chaining Active)',
            'started_at' => time(),
        ), 600 );

        $last_scan_ts = (int) get_option( 'nbd_s3_temp_last_scan_ts', 0 );
        $use_incr     = ( ! $full_rebuild && $last_scan_ts > 0 );
        $incr_cutoff  = max( 0, $last_scan_ts - HOUR_IN_SECONDS ); // 1h overlap buffer

        // On fresh full rebuild, clear stale index entries while preserving pre-purge refs of S3-offloaded orders
        if ( 0 === $offset && $full_rebuild ) {
            self::reset_index_preserving_offloaded();
            delete_transient( 'nbd_s3_temp_index_dirs' );
        } elseif ( 0 === $offset ) {
            delete_transient( 'nbd_s3_temp_index_dirs' );
        }

        // On offset 0, record snapshot start timestamp and index product/variation/options/order/auxiliary design refs
        if ( 0 === $offset ) {
            set_transient( 'nbd_s3_temp_scan_start_ts', time(), 6 * HOUR_IN_SECONDS );
            self::index_postmeta_temp_refs();
            self::index_order_itemmeta_temp_refs();
            self::index_cart_session_temp_refs();
            self::index_nbdesigner_options_temp_refs();
            self::index_auxiliary_design_dirs();
        }

        $folder_list = get_transient( 'nbd_s3_temp_index_dirs' );
        if ( false === $folder_list || ! is_array( $folder_list ) ) {
            $folder_list = array();
            try {
                $iterator = new \DirectoryIterator( $designs_dir );
                foreach ( $iterator as $item ) {
                    if ( $item->isDot() || ! $item->isDir() ) {
                        continue;
                    }
                    if ( $use_incr && ! self::is_design_dir_modified_since( $item->getPathname(), $item->getMTime(), $incr_cutoff ) ) {
                        continue;
                    }
                    $folder_list[] = $item->getFilename();
                }
            } catch ( \Throwable $e ) {
                // Fallback if DirectoryIterator fails
                $dirs = glob( $designs_dir . '/*', GLOB_ONLYDIR );
                if ( is_array( $dirs ) ) {
                    foreach ( $dirs as $d ) {
                        if ( $use_incr && ! self::is_design_dir_modified_since( $d, (int) @filemtime( $d ), $incr_cutoff ) ) {
                            continue;
                        }
                        $folder_list[] = basename( $d );
                    }
                }
            }

            sort( $folder_list );

            // Also include any historical full-purged S3 orders missing locally and not yet inspected remotely
            $offloaded_lookup = self::get_offloaded_folders_lookup();
            if ( ! empty( $offloaded_lookup ) ) {
                $remote_indexed = (array) get_option( 'nbd_s3_temp_remote_indexed', array() );
                $s3_settings    = NBD_S3_Settings::get_all();
                $s3_configured  = ! empty( $s3_settings['bucket'] ) && ( ! empty( $s3_settings['secret_key'] ) || ! empty( $s3_settings['access_key'] ) );
                if ( $s3_configured ) {
                    foreach ( $offloaded_lookup as $off_folder => $_ ) {
                        if ( ! isset( $remote_indexed[ $off_folder ] ) && ! is_dir( $designs_dir . '/' . $off_folder ) ) {
                            $folder_list[] = 's3:' . $off_folder;
                        }
                    }
                }
            }
        }

        // Refresh 6h TTLs on every batch so large multi-batch baseline scans never expire mid-way
        set_transient( 'nbd_s3_temp_index_dirs', $folder_list, 6 * HOUR_IN_SECONDS );
        $existing_start = (int) get_transient( 'nbd_s3_temp_scan_start_ts' );
        if ( $existing_start > 0 ) {
            set_transient( 'nbd_s3_temp_scan_start_ts', $existing_start, 6 * HOUR_IN_SECONDS );
        }

        $total_folders = count( $folder_list );
        $first_item    = $folder_list[ $offset ] ?? '';
        $batch_size    = str_starts_with( (string) $first_item, 's3:' ) ? 25 : 500;
        $batch         = array_slice( $folder_list, $offset, $batch_size );

        $refs_found             = 0;
        $now_mysql              = current_time( 'mysql', true );
        $rows_to_insert         = array();
        $remote_indexed         = null;
        $remote_indexed_updated = false;

        foreach ( $batch as $folder_entry ) {
            if ( $is_manual && get_transient( 'nbd_s3_temp_sweep_cancelled' ) ) {
                delete_transient( 'nbd_s3_temp_index_dirs' );
                delete_transient( 'nbd_s3_sync_status' );
                return array( 'success' => false, 'message' => __( 'Temp index stopped by Pause.', 'nbd-s3-offload' ) );
            }

            if ( str_starts_with( (string) $folder_entry, 's3:' ) ) {
                $folder_name = substr( (string) $folder_entry, 3 );
                $remote_res  = self::extract_temp_refs_from_remote_s3_design( $folder_name );
                $extracted   = $remote_res['refs'];
                if ( $remote_res['inspected'] ) {
                    if ( null === $remote_indexed ) {
                        $remote_indexed = (array) get_option( 'nbd_s3_temp_remote_indexed', array() );
                    }
                    $remote_indexed[ $folder_name ] = time();
                    $remote_indexed_updated         = true;
                }
            } else {
                $folder_name = (string) $folder_entry;
                $folder_path = $designs_dir . '/' . $folder_name;
                $extracted   = self::extract_temp_refs_from_design_dir( $folder_path );
            }

            if ( ! empty( $extracted ) ) {
                foreach ( $extracted as $temp_rel ) {
                    $rows_to_insert[] = array( $temp_rel, $folder_name, $now_mysql );
                    $refs_found++;
                }
            }

            if ( count( $rows_to_insert ) >= 200 ) {
                self::bulk_insert_refs( $rows_to_insert );
                $rows_to_insert = array();
            }
        }

        if ( $remote_indexed_updated && is_array( $remote_indexed ) ) {
            update_option( 'nbd_s3_temp_remote_indexed', $remote_indexed, 'no' );
        }

        if ( ! empty( $rows_to_insert ) ) {
            self::bulk_insert_refs( $rows_to_insert );
        }

        $next_offset = $offset + count( $batch );
        if ( $next_offset < $total_folders && count( $batch ) > 0 ) {
            if ( function_exists( 'as_schedule_single_action' ) ) {
                if ( ! NBD_S3_Tier_Lifecycle::has_action_in_queue( 'nbd_s3_chain_temp_index', array( 'pending' ) ) ) {
                    as_schedule_single_action( time() + 5, 'nbd_s3_chain_temp_index', array( $next_offset, $full_rebuild, $chain_sweep, $is_manual ) );
                    if ( function_exists( 'spawn_cron' ) ) {
                        spawn_cron();
                    }
                }
            }
            return array(
                'success'         => true,
                'indexed_folders' => count( $batch ),
                'indexed_refs'    => $refs_found,
                'remaining'       => $total_folders - $next_offset,
            );
        }

        // Indexing complete: commit the snapshot start timestamp so concurrent mid-scan edits are never skipped next run
        $scan_started_ts = (int) get_transient( 'nbd_s3_temp_scan_start_ts' );
        delete_transient( 'nbd_s3_temp_scan_start_ts' );
        delete_transient( 'nbd_s3_temp_index_dirs' );
        update_option( 'nbd_s3_temp_last_scan_ts', $scan_started_ts > 0 ? $scan_started_ts : time() );

        $mode_label = $use_incr ? 'Incremental' : 'Full';
        NBD_S3_Logger::log(
            'TEMP_INDEX',
            sprintf( '%s temp reference index complete (%d folders scanned, %d refs in final batch).', $mode_label, $total_folders, $refs_found ),
            array( 'folders' => $total_folders, 'mode' => $mode_label ),
            'info'
        );

        if ( $chain_sweep ) {
            delete_transient( 'nbd_s3_temp_sweep_cursor' );
            self::schedule_temp_sweep( $is_manual );
        } else {
            delete_transient( 'nbd_s3_sync_status' );
        }

        return array(
            'success'         => true,
            'indexed_folders' => $total_folders,
            'indexed_refs'    => $refs_found,
        );
    }

    /**
     * Schedule Stage 2 (`nbd_s3_chain_temp_sweep`).
     */
    private static function schedule_temp_sweep( bool $is_manual ): void {
        if ( function_exists( 'as_schedule_single_action' ) ) {
            if ( ! NBD_S3_Tier_Lifecycle::has_action_in_queue( 'nbd_s3_chain_temp_sweep', array( 'pending' ) ) ) {
                as_schedule_single_action( time() + 5, 'nbd_s3_chain_temp_sweep', array( $is_manual ) );
                if ( function_exists( 'spawn_cron' ) ) {
                    spawn_cron();
                }
            }
        } else {
            self::run_temp_sweep( $is_manual );
        }
    }

    /**
     * Stage 2: Sweep `wp-content/uploads/nbdesigner/temp/` in safe, cursor-tracked batches.
     * Deletes files older than `temp_retention_days` ONLY if they are NOT in `wp_nbd_s3_temp_refs`.
     * Never touches `pdf-templates/`.
     */
    public static function run_temp_sweep( bool $is_manual = false ): array {
        @set_time_limit( 300 );
        @ini_set( 'max_execution_time', '300' );

        $settings = NBD_S3_Settings::get_all();

        if ( $is_manual && get_transient( 'nbd_s3_temp_sweep_cancelled' ) ) {
            delete_transient( 'nbd_s3_sync_status' );
            delete_transient( 'nbd_s3_temp_sweep_cursor' );
            return array( 'success' => false, 'message' => __( 'Temp sweep stopped by Pause.', 'nbd-s3-offload' ), 'purged_count' => 0 );
        }

        if ( ! $is_manual && ! empty( $settings['sync_paused'] ) ) {
            return array( 'success' => false, 'message' => __( 'Skipped: Sync is paused (Safe Mode active).', 'nbd-s3-offload' ), 'purged_count' => 0 );
        }

        if ( ! $is_manual && empty( $settings['enable_temp_cleanup'] ) ) {
            return array( 'success' => false, 'message' => __( 'Temp folder lifecycle cleanup is disabled.', 'nbd-s3-offload' ), 'purged_count' => 0 );
        }

        $temp_dir = self::get_temp_base_dir();
        if ( ! is_dir( $temp_dir ) ) {
            delete_transient( 'nbd_s3_sync_status' );
            return array( 'success' => true, 'purged_count' => 0, 'message' => __( 'Temp directory does not exist.', 'nbd-s3-offload' ) );
        }

        self::maybe_create_table();

        // Hard safety guard: refuse to sweep if reference index has never completed or is unexpectedly empty
        global $wpdb;
        $table        = self::get_table_name();
        $last_scan_ts = (int) get_option( 'nbd_s3_temp_last_scan_ts', 0 );
        $ref_count    = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
        if ( $last_scan_ts <= 0 || $ref_count <= 0 ) {
            delete_transient( 'nbd_s3_sync_status' );
            $msg = __( 'Temp sweep aborted by safety guard: reference index is empty or incomplete. Run Rebuild Index first.', 'nbd-s3-offload' );
            NBD_S3_Logger::log( 'TEMP_PURGE', $msg, array( 'last_scan_ts' => $last_scan_ts, 'ref_count' => $ref_count ), 'warning' );
            return array( 'success' => false, 'purged_count' => 0, 'message' => $msg );
        }

        $retention_days = max( 1, (int) ( $settings['temp_retention_days'] ?? 30 ) );
        $cutoff_ts      = time() - ( $retention_days * DAY_IN_SECONDS );

        set_transient( 'nbd_s3_sync_status', array(
            'running'    => true,
            'action'     => 'Temp Folder Purge (Auto-Chaining Active)',
            'started_at' => time(),
        ), 600 );

        // Build ordered list of leaf subdirectories (e.g. 2019/01, 2019/02, ...) or reuse cursor
        $cursor = get_transient( 'nbd_s3_temp_sweep_cursor' );
        if ( ! is_array( $cursor ) || empty( $cursor['dirs'] ) ) {
            $dirs = self::discover_temp_leaf_dirs( $temp_dir );
            $cursor = array(
                'dirs'          => $dirs,
                'dir_idx'       => 0,
                'total_purged'  => 0,
                'total_kept'    => 0,
                'bytes_freed'   => 0,
            );
        }

        $dirs         = $cursor['dirs'];
        $dir_idx      = (int) ( $cursor['dir_idx'] ?? 0 );
        $total_dirs   = count( $dirs );
        $batch_purged = 0;
        $batch_kept   = 0;
        $batch_bytes  = 0;
        $checked      = 0;

        $max_checks_per_batch = 2000;
        $max_deletes_per_batch = 1000;

        while ( $dir_idx < $total_dirs ) {
            if ( $is_manual && get_transient( 'nbd_s3_temp_sweep_cancelled' ) ) {
                delete_transient( 'nbd_s3_temp_sweep_cursor' );
                delete_transient( 'nbd_s3_sync_status' );
                return array( 'success' => false, 'message' => __( 'Temp sweep stopped by Pause.', 'nbd-s3-offload' ), 'purged_count' => $batch_purged );
            }

            $rel_dir = $dirs[ $dir_idx ];
            $abs_dir = '' === $rel_dir ? $temp_dir : ( $temp_dir . '/' . $rel_dir );

            if ( ! is_dir( $abs_dir ) ) {
                $dir_idx++;
                continue;
            }

            $candidates = array();
            try {
                $it = new \DirectoryIterator( $abs_dir );
                foreach ( $it as $item ) {
                    if ( $item->isDot() || ! $item->isFile() ) {
                        continue;
                    }
                    $name = $item->getFilename();
                    if ( in_array( $name, array( '.htaccess', 'index.php', 'index.html' ), true ) ) {
                        continue;
                    }
                    if ( $item->getMTime() >= $cutoff_ts ) {
                        continue;
                    }
                    $rel_file = '' === $rel_dir ? $name : ( $rel_dir . '/' . $name );
                    $candidates[] = array(
                        'rel'  => $rel_file,
                        'abs'  => $item->getPathname(),
                        'size' => (int) $item->getSize(),
                    );
                }
            } catch ( \Throwable $e ) {
                $dir_idx++;
                continue;
            }

            if ( ! empty( $candidates ) ) {
                // Check references in chunks of 500 via indexed SQL (also checking parent .pdf stem for multi-page _N.png files)
                foreach ( array_chunk( $candidates, 500 ) as $chunk ) {
                    $query_paths = array();
                    foreach ( $chunk as $file ) {
                        $query_paths[ $file['rel'] ] = true;
                        if ( preg_match( '#^(.+)_[0-9]+\.(?:png|jpg|jpeg)$#i', $file['rel'], $pm ) ) {
                            $query_paths[ $pm[1] . '.pdf' ] = true;
                        }
                    }
                    $ref_map = self::lookup_referenced_paths( array_keys( $query_paths ) );

                    foreach ( $chunk as $file ) {
                        $checked++;
                        $rel_key    = self::norm_rel_key( $file['rel'] );
                        $parent_key = null;
                        if ( preg_match( '#^(.+)_[0-9]+\.(?:png|jpg|jpeg)$#i', $file['rel'], $pm ) ) {
                            $parent_key = self::norm_rel_key( $pm[1] . '.pdf' );
                        }
                        if ( isset( $ref_map[ $rel_key ] ) || ( null !== $parent_key && isset( $ref_map[ $parent_key ] ) ) ) {
                            $batch_kept++;
                            continue;
                        }

                        // Belt-and-suspenders containment & pdf-templates guard before deletion
                        $abs_norm = str_replace( '\\', '/', $file['abs'] );
                        if ( ! str_starts_with( $abs_norm, $temp_dir . '/' ) || str_starts_with( strtolower( $file['rel'] ), 'pdf-templates/' ) ) {
                            $batch_kept++;
                            continue;
                        }

                        if ( @unlink( $file['abs'] ) ) {
                            $batch_purged++;
                            $batch_bytes += $file['size'];
                        }
                    }
                }

                // Clean up empty month/year directory if nothing remains
                if ( '' !== $rel_dir ) {
                    self::cleanup_empty_parents( $abs_dir, $temp_dir );
                }
            }

            $dir_idx++;

            if ( $batch_purged >= $max_deletes_per_batch || $checked >= $max_checks_per_batch ) {
                break;
            }
        }

        $cursor['dir_idx']      = $dir_idx;
        $cursor['total_purged'] = (int) ( $cursor['total_purged'] ?? 0 ) + $batch_purged;
        $cursor['total_kept']   = (int) ( $cursor['total_kept'] ?? 0 ) + $batch_kept;
        $cursor['bytes_freed']  = (int) ( $cursor['bytes_freed'] ?? 0 ) + $batch_bytes;

        if ( $dir_idx < $total_dirs ) {
            set_transient( 'nbd_s3_temp_sweep_cursor', $cursor, 6 * HOUR_IN_SECONDS );

            if ( $batch_purged > 0 || $batch_kept > 0 ) {
                NBD_S3_Logger::log(
                    'TEMP_PURGE',
                    sprintf( 'Temp sweep batch: purged %d orphaned files (%s freed), kept %d referenced files. Continuing...', $batch_purged, size_format( $batch_bytes ), $batch_kept ),
                    array( 'purged' => $batch_purged, 'kept' => $batch_kept, 'dir_idx' => $dir_idx, 'total_dirs' => $total_dirs ),
                    'info'
                );
            }

            if ( function_exists( 'as_schedule_single_action' ) ) {
                if ( ! NBD_S3_Tier_Lifecycle::has_action_in_queue( 'nbd_s3_chain_temp_sweep', array( 'pending' ) ) ) {
                    as_schedule_single_action( time() + 10, 'nbd_s3_chain_temp_sweep', array( $is_manual ) );
                    if ( function_exists( 'spawn_cron' ) ) {
                        spawn_cron();
                    }
                }
            }

            return array(
                'success'      => true,
                'purged_count' => $batch_purged,
                'kept_count'   => $batch_kept,
                'chaining'     => true,
            );
        }

        // Sweep finished
        delete_transient( 'nbd_s3_temp_sweep_cursor' );
        delete_transient( 'nbd_s3_sync_status' );

        NBD_S3_Logger::log(
            'TEMP_PURGE',
            sprintf(
                'Temp folder sweep complete! Purged %d orphaned files (%s freed), protected %d referenced files.',
                $cursor['total_purged'],
                size_format( $cursor['bytes_freed'] ),
                $cursor['total_kept']
            ),
            array(
                'total_purged' => $cursor['total_purged'],
                'total_kept'   => $cursor['total_kept'],
                'bytes_freed'  => $cursor['bytes_freed'],
            ),
            'success'
        );

        return array(
            'success'      => true,
            'purged_count' => $cursor['total_purged'],
            'kept_count'   => $cursor['total_kept'],
            'bytes_freed'  => $cursor['bytes_freed'],
            'chaining'     => false,
        );
    }

    /**
     * Check if a design directory or any of its core JSON files were modified since `$cutoff`.
     * Protects against in-place overwrites of `design.json` that do not update parent directory mtime.
     */
    private static function is_design_dir_modified_since( string $folder_path, int $dir_mtime, int $cutoff ): bool {
        if ( $dir_mtime >= $cutoff ) {
            return true;
        }
        foreach ( array( 'design.json', 'config.json', 'product.json', 'option.json', 'part', 'part/svgpath' ) as $f ) {
            $fm = (int) @filemtime( $folder_path . '/' . $f );
            if ( $fm >= $cutoff ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Extract relative `temp/` paths from a single design folder's JSON and SVG files.
     */
    private static function extract_temp_refs_from_design_dir( string $folder_path ): array {
        $target_files = array(
            $folder_path . '/design.json',
            $folder_path . '/config.json',
            $folder_path . '/product.json',
            $folder_path . '/option.json',
        );

        // Also include any SVG frame files in the design folder or part/ subfolder
        $svg_files = array_merge(
            glob( $folder_path . '/*.svg' ) ?: array(),
            glob( $folder_path . '/part/*.svg' ) ?: array(),
            glob( $folder_path . '/part/svgpath/*.svg' ) ?: array()
        );
        foreach ( $svg_files as $svg_file ) {
            $target_files[] = $svg_file;
        }

        $found = array();

        foreach ( $target_files as $file ) {
            if ( ! is_file( $file ) || ! is_readable( $file ) ) {
                continue;
            }
            $size = @filesize( $file );
            if ( false === $size || 0 === $size || $size > 50 * 1024 * 1024 ) {
                continue;
            }
            $content = @file_get_contents( $file );
            if ( false === $content ) {
                continue;
            }

            foreach ( self::extract_temp_refs_from_string( $content ) as $rel ) {
                $found[ $rel ] = true;
            }
        }

        return array_keys( $found );
    }

    /**
     * Normalize relative path key for case-insensitive UTF-8 lookup matching MySQL collation.
     */
    private static function norm_rel_key( string $path ): string {
        return function_exists( 'mb_strtolower' ) ? mb_strtolower( $path, 'UTF-8' ) : strtolower( $path );
    }

    /**
     * Decode JSON `\uXXXX` Unicode escape sequences (including UTF-16 surrogate pairs) into UTF-8.
     */
    private static function decode_unicode_escapes( string $str ): string {
        if ( ! str_contains( $str, '\\u' ) && ! str_contains( $str, '\\U' ) ) {
            return $str;
        }
        $decoded = preg_replace_callback(
            '/\\\\[uU]([dD][89abAB][0-9a-fA-F]{2})\\\\[uU]([dD][c-fC-F][0-9a-fA-F]{2})|\\\\[uU]([0-9a-fA-F]{4})/',
            static function ( array $m ): string {
                $json_str = '"' . str_replace( '\\U', '\\u', $m[0] ) . '"';
                $val      = json_decode( $json_str );
                return is_string( $val ) ? $val : $m[0];
            },
            $str
        );
        return is_string( $decoded ) ? $decoded : $str;
    }

    /**
     * Extract all `temp/` relative paths from a JSON/serialized/SVG string, including:
     * - Structured JSON (`json_decode`) and PHP-serialized (`unserialize`) values with `\uXXXX` Traditional Chinese / emojis decoded
     * - Full/partial URLs containing `nbdesigner/temp/...` (with spaces, `%20`, `%E5...`, or `\uXXXX`)
     * - `origin_bg_pdf` and `origin_pdf` fields stored relative to `NBDESIGNER_TEMP_DIR`
     * - Paired companion files (`_preview.<ext>` <-> `.<ext>`, `.pdf` <-> `.png` / `_1.png` / `_0.png`, preserving both exact and lowercase extensions)
     */
    private static function extract_temp_refs_from_string( string $content ): array {
        if ( '' === $content ) {
            return array();
        }

        $found = array();

        $store_one = static function ( string $path ) use ( &$found ): void {
            if ( '' === $path ) {
                return;
            }
            $char_len = function_exists( 'mb_strlen' ) ? mb_strlen( $path, 'UTF-8' ) : strlen( $path );
            if ( $char_len <= 512 ) {
                $found[ $path ] = true;
            }
        };

        $add_with_companions = static function ( string $clean ) use ( $store_one ): void {
            $store_one( $clean );

            // Locale-independent UTF-8-safe split (avoids C-locale pathinfo() stripping leading Chinese characters)
            $slash_pos  = strrpos( $clean, '/' );
            $dir_prefix = ( false === $slash_pos ) ? '' : substr( $clean, 0, $slash_pos + 1 );
            $filename   = ( false === $slash_pos ) ? $clean : substr( $clean, $slash_pos + 1 );
            $dot_pos    = strrpos( $filename, '.' );

            if ( false === $dot_pos || 0 === $dot_pos ) {
                return;
            }

            $base     = substr( $filename, 0, $dot_pos );
            $orig_ext = substr( $filename, $dot_pos + 1 );
            $ext      = strtolower( $orig_ext );

            if ( '' === $base || '' === $ext ) {
                return;
            }

            $ext_variants = ( $orig_ext !== $ext ) ? array( $orig_ext, $ext ) : array( $ext );

            // 1. Photo thumbnail companion: `<name>_preview.<ext>` <-> `<name>.<ext>`
            if ( str_ends_with( $base, '_preview' ) ) {
                $orig_base = substr( $base, 0, -8 );
                if ( '' !== $orig_base ) {
                    foreach ( $ext_variants as $ev ) {
                        $store_one( $dir_prefix . $orig_base . '.' . $ev );
                    }
                    $store_one( $dir_prefix . $orig_base . '.pdf' );
                }
            } else {
                foreach ( $ext_variants as $ev ) {
                    $store_one( $dir_prefix . $base . '_preview.' . $ev );
                }
            }

            // 2. Uploaded PDF <-> rasterized PNG companions (`<name>.pdf` <-> `<name>.png`, `<name>_1.png`, `<name>_0.png`)
            if ( 'pdf' === $ext ) {
                $store_one( $dir_prefix . $base . '.png' );
                $store_one( $dir_prefix . $base . '_1.png' );
                $store_one( $dir_prefix . $base . '_0.png' );
            } elseif ( 'png' === $ext || 'jpg' === $ext || 'jpeg' === $ext ) {
                $store_one( $dir_prefix . $base . '.pdf' );
                if ( preg_match( '/^(.+)_[0-9]+$/', $base, $m ) ) {
                    $store_one( $dir_prefix . $m[1] . '.pdf' );
                    $store_one( $dir_prefix . $m[1] . '.png' );
                }
            }
        };

        $add_ref = static function ( string $raw ) use ( $add_with_companions ): void {
            $clean = self::decode_unicode_escapes( $raw );
            $clean = str_replace( array( '\\/', '\\' ), '/', $clean );
            $clean = (string) preg_replace( '/[?#].*$/s', '', $clean );
            $clean = ltrim( $clean, '/' );

            if ( '' === $clean || str_starts_with( strtolower( $clean ), 'pdf-templates/' ) ) {
                return;
            }
            $add_with_companions( $clean );

            $decoded = rawurldecode( $clean );
            if ( '' !== $decoded && $decoded !== $clean && ! str_starts_with( strtolower( $decoded ), 'pdf-templates/' ) ) {
                $add_with_companions( $decoded );
            }

            // Protect against macOS NFD vs NFC Unicode filename normalization differences
            $target_utf8 = ( '' !== $decoded ) ? $decoded : $clean;
            if ( class_exists( '\Normalizer' ) && preg_match( '/[^\x00-\x7F]/', $target_utf8 ) ) {
                $nfc = \Normalizer::normalize( $target_utf8, \Normalizer::FORM_C );
                if ( is_string( $nfc ) && '' !== $nfc && $nfc !== $target_utf8 ) {
                    $add_with_companions( $nfc );
                }
                $nfd = \Normalizer::normalize( $target_utf8, \Normalizer::FORM_D );
                if ( is_string( $nfd ) && '' !== $nfd && $nfd !== $target_utf8 ) {
                    $add_with_companions( $nfd );
                }
            }
        };

        $scan_plain_string = static function ( string $str ) use ( $add_ref ): void {
            if ( stripos( $str, 'temp' ) !== false ) {
                if ( preg_match_all( '#nbdesigner(?:\\\\/|/|%2f)temp(?:\\\\/|/|%2f)((?:\\\\/|\\\\[uU][0-9a-fA-F]{4}|[^"\'<>\\\\\r\n])+)#i', $str, $m ) ) {
                    foreach ( $m[1] as $raw_rel ) {
                        $add_ref( $raw_rel );
                        // Handle unquoted CSS `url(https://.../temp/foo.png);` trailing `)`
                        if ( str_contains( $raw_rel, ')' ) && ! str_contains( $raw_rel, '(' ) ) {
                            $add_ref( (string) preg_replace( '/\).*$/s', '', $raw_rel ) );
                        }
                    }
                }
            }
        };

        // Pass A: Structured decode for JSON or PHP-serialized payloads (decodes \uXXXX, \/, nested JSON strings, and origin_pdf keys cleanly)
        $has_hint = ( stripos( $content, 'temp' ) !== false || stripos( $content, 'origin_bg_pdf' ) !== false || stripos( $content, 'origin_pdf' ) !== false );
        if ( $has_hint ) {
            $trimmed   = ltrim( $content );
            $first_chr = $trimmed[0] ?? '';
            $decoded_struct = null;

            if ( '{' === $first_chr || '[' === $first_chr ) {
                $decoded_struct = json_decode( $trimmed, true );
            } elseif ( preg_match( '/^[aOs]:\d+:/', $trimmed ) ) {
                $unser = @unserialize( $trimmed, array( 'allowed_classes' => array( 'stdClass' ) ) );
                if ( false !== $unser ) {
                    $decoded_struct = $unser;
                }
            }

            if ( null !== $decoded_struct ) {
                $walk = null;
                $walk = static function ( mixed $node, int $depth = 0 ) use ( &$walk, $add_ref, $scan_plain_string ): void {
                    if ( $depth > 14 ) {
                        return;
                    }
                    if ( is_object( $node ) ) {
                        $node = get_object_vars( $node );
                    }
                    if ( is_array( $node ) ) {
                        foreach ( $node as $k => $v ) {
                            if ( is_string( $k ) && ( 'origin_bg_pdf' === $k || 'origin_pdf' === $k ) && is_string( $v ) && '' !== $v ) {
                                $add_ref( $v );
                            }
                            $walk( $v, $depth + 1 );
                        }
                    } elseif ( is_string( $node ) && '' !== $node ) {
                        $scan_plain_string( $node );
                        // Handle double-encoded JSON strings nested inside serialized/JSON payloads
                        if ( $depth < 6 && ( stripos( $node, 'temp' ) !== false || stripos( $node, 'origin_' ) !== false ) ) {
                            $sub_trim = ltrim( $node );
                            $sub_chr  = $sub_trim[0] ?? '';
                            if ( '{' === $sub_chr || '[' === $sub_chr ) {
                                $sub_json = json_decode( $sub_trim, true );
                                if ( is_array( $sub_json ) ) {
                                    $walk( $sub_json, $depth + 1 );
                                }
                            }
                        }
                    }
                };
                $walk( $decoded_struct, 0 );
            }

            // Pass B: Raw regex pass (covers SVG XML, partial/non-JSON strings, and belt-and-suspenders `\uXXXX` extraction)
            $scan_plain_string( $content );

            if ( stripos( $content, 'origin_bg_pdf' ) !== false || stripos( $content, 'origin_pdf' ) !== false ) {
                if ( preg_match_all( '#"(?:origin_bg_pdf|origin_pdf)"(?:\s*:\s*|;s:\d+:)"((?:\\\\[uU][0-9a-fA-F]{4}|\\\\/|[^"])+)"#i', $content, $pdf_matches ) ) {
                    foreach ( $pdf_matches[1] as $raw_rel ) {
                        $add_ref( $raw_rel );
                    }
                }
            }
        }

        return array_keys( $found );
    }

    /**
     * Clear non-offloaded index rows on a full rebuild while preserving pre-purge rows of S3-offloaded orders.
     */
    private static function reset_index_preserving_offloaded(): void {
        global $wpdb;
        $table            = self::get_table_name();
        $offloaded_lookup = self::get_offloaded_folders_lookup();

        // Prune remote_indexed cache to only currently offloaded folders
        $remote_indexed = (array) get_option( 'nbd_s3_temp_remote_indexed', array() );
        if ( ! empty( $remote_indexed ) ) {
            $kept_remote = array();
            foreach ( $remote_indexed as $folder => $ts ) {
                if ( isset( $offloaded_lookup[ $folder ] ) ) {
                    $kept_remote[ $folder ] = $ts;
                }
            }
            update_option( 'nbd_s3_temp_remote_indexed', $kept_remote, 'no' );
        }

        if ( empty( $offloaded_lookup ) ) {
            $wpdb->query( "TRUNCATE TABLE {$table}" );
            return;
        }

        $existing_rows = $wpdb->get_results(
            "SELECT temp_path, design_folder, updated_at FROM {$table} WHERE design_folder NOT LIKE 'post\_%' AND design_folder NOT LIKE 'order\_item\_%' AND design_folder NOT LIKE 'cart\_%' AND design_folder NOT LIKE 'nbd\_opt\_%' AND design_folder NOT LIKE 'wp\_opt\_%' AND design_folder NOT LIKE 'aux\_%'",
            ARRAY_A
        );

        $preserved = array();
        if ( ! empty( $existing_rows ) && is_array( $existing_rows ) ) {
            foreach ( $existing_rows as $r ) {
                $df = (string) ( $r['design_folder'] ?? '' );
                if ( '' !== $df && isset( $offloaded_lookup[ $df ] ) ) {
                    $preserved[] = array( (string) $r['temp_path'], $df, (string) $r['updated_at'] );
                }
            }
        }

        $wpdb->query( "TRUNCATE TABLE {$table}" );

        if ( ! empty( $preserved ) ) {
            foreach ( array_chunk( $preserved, 200 ) as $chunk ) {
                self::bulk_insert_refs( $chunk );
            }
        }
    }

    /**
     * Build O(1) lookup map of all design folders offloaded to S3/Bunny Storage.
     */
    private static function get_offloaded_folders_lookup(): array {
        $lookup = array();
        if ( class_exists( 'NBD_S3_Tier_Lifecycle' ) && method_exists( 'NBD_S3_Tier_Lifecycle', 'get_offloaded_registry' ) ) {
            foreach ( NBD_S3_Tier_Lifecycle::get_offloaded_registry() as $folder => $_ ) {
                $folder = (string) $folder;
                if ( '' !== $folder ) {
                    $lookup[ $folder ] = true;
                }
            }
        }

        global $wpdb;
        $order_meta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$order_meta_table}'" ) === $order_meta_table ) {
            $db_folders = $wpdb->get_col(
                "SELECT DISTINCT m1.meta_value FROM {$order_meta_table} m1 INNER JOIN {$order_meta_table} m2 ON m1.order_item_id = m2.order_item_id WHERE m1.meta_key IN ('_nbd', '_nbd_item_key') AND m1.meta_value IS NOT NULL AND m1.meta_value != '' AND m2.meta_key = '_nbd_s3_offloaded' AND m2.meta_value = '1'"
            );
            if ( is_array( $db_folders ) ) {
                foreach ( $db_folders as $f ) {
                    $f = trim( (string) $f );
                    if ( '' !== $f ) {
                        $lookup[ $f ] = true;
                    }
                }
            }
        }

        return $lookup;
    }

    /**
     * Inspect remote S3/Bunny JSON metadata for historical full-purged orders missing from local disk.
     *
     * @return array{refs: array<int, string>, inspected: bool}
     */
    private static function extract_temp_refs_from_remote_s3_design( string $folder_name ): array {
        if ( '' === $folder_name || ! class_exists( 'NBD_S3_Tier_Lifecycle' ) ) {
            return array( 'refs' => array(), 'inspected' => false );
        }

        $found         = array();
        $any_succeeded = false;
        $had_net_error = false;

        try {
            $client = NBD_S3_Tier_Lifecycle::get_client();
            foreach ( array( 'design.json', 'config.json', 'product.json', 'option.json' ) as $json_name ) {
                $body = $client->get_object( 'designs/' . $folder_name . '/' . $json_name );
                if ( is_wp_error( $body ) ) {
                    $err_msg = $body->get_error_message();
                    if ( ! str_contains( $err_msg, '404' ) && ! str_contains( $err_msg, 'NoSuchKey' ) ) {
                        $had_net_error = true;
                    }
                    continue;
                }
                if ( is_string( $body ) && '' !== $body ) {
                    $any_succeeded = true;
                    foreach ( self::extract_temp_refs_from_string( $body ) as $rel ) {
                        $found[ $rel ] = true;
                    }
                }
            }
        } catch ( \Throwable $e ) {
            return array( 'refs' => array_keys( $found ), 'inspected' => false );
        }

        return array(
            'refs'      => array_keys( $found ),
            'inspected' => ( $any_succeeded || ! $had_net_error ),
        );
    }

    /**
     * Index auxiliary NBDesigner directories (`admindesign`, `suggest_designs`, `data`) if present.
     */
    private static function index_auxiliary_design_dirs(): void {
        $base_nbd = dirname( self::get_temp_base_dir() );
        $aux_dirs = array(
            'admindesign'     => defined( 'NBDESIGNER_ADMINDESIGN_DIR' ) ? rtrim( str_replace( '\\', '/', NBDESIGNER_ADMINDESIGN_DIR ), '/' ) : ( $base_nbd . '/admindesign' ),
            'suggest_designs' => defined( 'NBDESIGNER_SUGGEST_DESIGN_DIR' ) ? rtrim( str_replace( '\\', '/', NBDESIGNER_SUGGEST_DESIGN_DIR ), '/' ) : ( $base_nbd . '/suggest_designs' ),
            'data'            => defined( 'NBDESIGNER_DATA_CONFIG_DIR' ) ? rtrim( str_replace( '\\', '/', NBDESIGNER_DATA_CONFIG_DIR ), '/' ) : ( $base_nbd . '/data' ),
        );

        $now_mysql      = current_time( 'mysql', true );
        $rows_to_insert = array();

        foreach ( $aux_dirs as $label => $dir_path ) {
            if ( ! is_dir( $dir_path ) ) {
                continue;
            }
            try {
                $it = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator( $dir_path, \FilesystemIterator::SKIP_DOTS ),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );
                foreach ( $it as $file_info ) {
                    if ( ! $file_info->isFile() ) {
                        continue;
                    }
                    $ext = strtolower( (string) $file_info->getExtension() );
                    if ( ! in_array( $ext, array( 'json', 'svg' ), true ) ) {
                        continue;
                    }
                    $size = $file_info->getSize();
                    if ( $size <= 0 || $size > 50 * 1024 * 1024 ) {
                        continue;
                    }
                    $content = @file_get_contents( $file_info->getPathname() );
                    if ( false === $content ) {
                        continue;
                    }
                    foreach ( self::extract_temp_refs_from_string( $content ) as $temp_rel ) {
                        $rows_to_insert[] = array( $temp_rel, 'aux_' . $label, $now_mysql );
                        if ( count( $rows_to_insert ) >= 200 ) {
                            self::bulk_insert_refs( $rows_to_insert );
                            $rows_to_insert = array();
                        }
                    }
                }
            } catch ( \Throwable $e ) {
                continue;
            }
        }

        if ( ! empty( $rows_to_insert ) ) {
            self::bulk_insert_refs( $rows_to_insert );
        }
    }

    /**
     * Index product, variation (`_designer_variation_setting`, `_designer_setting{vid}`), and attachment `temp/` references in `wp_postmeta`.
     */
    private static function index_postmeta_temp_refs(): void {
        global $wpdb;
        $rows = $wpdb->get_results(
            "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_value LIKE '%nbdesigner%temp%' OR meta_value LIKE '%origin_bg_pdf%' OR meta_value LIKE '%origin_pdf%'",
            ARRAY_A
        );

        if ( empty( $rows ) || ! is_array( $rows ) ) {
            return;
        }

        $now_mysql      = current_time( 'mysql', true );
        $rows_to_insert = array();

        foreach ( $rows as $row ) {
            $post_key  = 'post_' . (int) $row['post_id'];
            $extracted = self::extract_temp_refs_from_string( (string) $row['meta_value'] );
            foreach ( $extracted as $temp_rel ) {
                $rows_to_insert[] = array( $temp_rel, $post_key, $now_mysql );
                if ( count( $rows_to_insert ) >= 200 ) {
                    self::bulk_insert_refs( $rows_to_insert );
                    $rows_to_insert = array();
                }
            }
        }

        if ( ! empty( $rows_to_insert ) ) {
            self::bulk_insert_refs( $rows_to_insert );
        }
    }

    /**
     * Index order-item-level `temp/` references stored in `wp_woocommerce_order_itemmeta`.
     */
    private static function index_order_itemmeta_temp_refs(): void {
        global $wpdb;
        $order_meta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$order_meta_table}'" ) !== $order_meta_table ) {
            return;
        }

        $rows = $wpdb->get_results(
            "SELECT order_item_id, meta_value FROM {$order_meta_table} WHERE meta_value LIKE '%nbdesigner%temp%' OR meta_value LIKE '%origin_bg_pdf%' OR meta_value LIKE '%origin_pdf%'",
            ARRAY_A
        );

        if ( empty( $rows ) || ! is_array( $rows ) ) {
            return;
        }

        $now_mysql      = current_time( 'mysql', true );
        $rows_to_insert = array();

        foreach ( $rows as $row ) {
            $item_key  = 'order_item_' . (int) $row['order_item_id'];
            $extracted = self::extract_temp_refs_from_string( (string) $row['meta_value'] );
            foreach ( $extracted as $temp_rel ) {
                $rows_to_insert[] = array( $temp_rel, $item_key, $now_mysql );
                if ( count( $rows_to_insert ) >= 200 ) {
                    self::bulk_insert_refs( $rows_to_insert );
                    $rows_to_insert = array();
                }
            }
        }

        if ( ! empty( $rows_to_insert ) ) {
            self::bulk_insert_refs( $rows_to_insert );
        }
    }

    /**
     * Index active WooCommerce cart sessions and persistent customer carts for pending designs.
     */
    private static function index_cart_session_temp_refs(): void {
        global $wpdb;
        $now_mysql      = current_time( 'mysql', true );
        $rows_to_insert = array();

        // 1. Active guest/user cart sessions in woocommerce_sessions
        $sess_table = $wpdb->prefix . 'woocommerce_sessions';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$sess_table}'" ) === $sess_table ) {
            $sess_rows = $wpdb->get_results(
                "SELECT session_id, session_value FROM {$sess_table} WHERE session_value LIKE '%nbdesigner%temp%' OR session_value LIKE '%origin_bg_pdf%' OR session_value LIKE '%origin_pdf%'",
                ARRAY_A
            );

            if ( ! empty( $sess_rows ) && is_array( $sess_rows ) ) {
                foreach ( $sess_rows as $row ) {
                    $sess_key  = 'cart_sess_' . (int) $row['session_id'];
                    $extracted = self::extract_temp_refs_from_string( (string) $row['session_value'] );
                    foreach ( $extracted as $temp_rel ) {
                        $rows_to_insert[] = array( $temp_rel, $sess_key, $now_mysql );
                        if ( count( $rows_to_insert ) >= 200 ) {
                            self::bulk_insert_refs( $rows_to_insert );
                            $rows_to_insert = array();
                        }
                    }
                }
            }
        }

        // 2. Persistent carts stored in usermeta for registered customers
        $user_rows = $wpdb->get_results(
            "SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key LIKE '_woocommerce_persistent_cart%' AND (meta_value LIKE '%nbdesigner%temp%' OR meta_value LIKE '%origin_bg_pdf%' OR meta_value LIKE '%origin_pdf%')",
            ARRAY_A
        );

        if ( ! empty( $user_rows ) && is_array( $user_rows ) ) {
            foreach ( $user_rows as $row ) {
                $user_key  = 'cart_user_' . (int) $row['user_id'];
                $extracted = self::extract_temp_refs_from_string( (string) $row['meta_value'] );
                foreach ( $extracted as $temp_rel ) {
                    $rows_to_insert[] = array( $temp_rel, $user_key, $now_mysql );
                    if ( count( $rows_to_insert ) >= 200 ) {
                        self::bulk_insert_refs( $rows_to_insert );
                        $rows_to_insert = array();
                    }
                }
            }
        }

        if ( ! empty( $rows_to_insert ) ) {
            self::bulk_insert_refs( $rows_to_insert );
        }
    }

    /**
     * Index `wp_nbdesigner_options` (Printing Options `fields` / `builder`) and non-transient `wp_options` for `temp/` references.
     */
    private static function index_nbdesigner_options_temp_refs(): void {
        global $wpdb;
        $now_mysql      = current_time( 'mysql', true );
        $rows_to_insert = array();

        $nbd_opt_table = $wpdb->prefix . 'nbdesigner_options';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$nbd_opt_table}'" ) === $nbd_opt_table ) {
            $opt_rows = $wpdb->get_results(
                "SELECT id, fields, builder FROM {$nbd_opt_table} WHERE fields LIKE '%nbdesigner%temp%' OR fields LIKE '%origin_bg_pdf%' OR fields LIKE '%origin_pdf%' OR builder LIKE '%nbdesigner%temp%' OR builder LIKE '%origin_bg_pdf%' OR builder LIKE '%origin_pdf%'",
                ARRAY_A
            );
            if ( ! empty( $opt_rows ) && is_array( $opt_rows ) ) {
                foreach ( $opt_rows as $r ) {
                    $key = 'nbd_opt_' . (int) $r['id'];
                    foreach ( array( 'fields', 'builder' ) as $col ) {
                        if ( ! empty( $r[ $col ] ) ) {
                            foreach ( self::extract_temp_refs_from_string( (string) $r[ $col ] ) as $temp_rel ) {
                                $rows_to_insert[] = array( $temp_rel, $key, $now_mysql );
                            }
                        }
                    }
                }
            }
        }

        $wp_opt_rows = $wpdb->get_results(
            "SELECT option_id, option_value FROM {$wpdb->options} WHERE option_name NOT LIKE '\_transient%' AND option_name NOT LIKE '\_site\_transient%' AND (option_value LIKE '%nbdesigner%temp%' OR option_value LIKE '%origin_bg_pdf%' OR option_value LIKE '%origin_pdf%')",
            ARRAY_A
        );
        if ( ! empty( $wp_opt_rows ) && is_array( $wp_opt_rows ) ) {
            foreach ( $wp_opt_rows as $r ) {
                $key = 'wp_opt_' . (int) $r['option_id'];
                foreach ( self::extract_temp_refs_from_string( (string) $r['option_value'] ) as $temp_rel ) {
                    $rows_to_insert[] = array( $temp_rel, $key, $now_mysql );
                }
            }
        }

        if ( ! empty( $rows_to_insert ) ) {
            foreach ( array_chunk( $rows_to_insert, 200 ) as $chunk ) {
                self::bulk_insert_refs( $chunk );
            }
        }
    }

    /**
     * Bulk INSERT IGNORE reference rows into `wp_nbd_s3_temp_refs`.
     */
    private static function bulk_insert_refs( array $rows ): void {
        if ( empty( $rows ) ) {
            return;
        }
        global $wpdb;
        $table        = self::get_table_name();
        $placeholders = array();
        $values       = array();

        foreach ( $rows as $row ) {
            $placeholders[] = '(%s, %s, %s, %s)';
            $values[]       = md5( self::norm_rel_key( (string) $row[0] ) . '|' . (string) $row[1] );
            $values[]       = $row[0];
            $values[]       = $row[1];
            $values[]       = $row[2];
        }

        $sql = "INSERT IGNORE INTO {$table} (ref_hash, temp_path, design_folder, updated_at) VALUES " . implode( ', ', $placeholders );
        $wpdb->query( $wpdb->prepare( $sql, $values ) );
    }

    /**
     * Query `wp_nbd_s3_temp_refs` for a batch of relative paths in one indexed query.
     * Returns normalized lowercase UTF-8 keys so `.JPG` vs `.jpg` matches consistently between MySQL and PHP.
     */
    private static function lookup_referenced_paths( array $rel_paths ): array {
        if ( empty( $rel_paths ) ) {
            return array();
        }
        global $wpdb;
        $table   = self::get_table_name();
        $unique  = array_values( array_unique( $rel_paths ) );
        $chunks  = array_chunk( $unique, 500 );
        $map     = array();

        foreach ( $chunks as $chunk ) {
            if ( empty( $chunk ) ) {
                continue;
            }
            $placeholders = implode( ',', array_fill( 0, count( $chunk ), '%s' ) );
            $sql          = $wpdb->prepare( "SELECT DISTINCT temp_path FROM {$table} WHERE temp_path IN ({$placeholders})", $chunk );
            $matched      = $wpdb->get_col( $sql );

            if ( ! empty( $matched ) && is_array( $matched ) ) {
                foreach ( $matched as $m ) {
                    $map[ self::norm_rel_key( (string) $m ) ] = true;
                }
            }
        }

        return $map;
    }

    /**
     * Discover subdirectories inside `temp/` (including root `''`), protecting `pdf-templates`.
     */
    private static function discover_temp_leaf_dirs( string $temp_dir ): array {
        $dirs = array( '' ); // Include root temp/ for any loose files
        try {
            $it = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator( $temp_dir, \FilesystemIterator::SKIP_DOTS ),
                \RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ( $it as $item ) {
                if ( ! $item->isDir() ) {
                    continue;
                }
                $full = str_replace( '\\', '/', $item->getPathname() );
                $rel  = ltrim( substr( $full, strlen( $temp_dir ) ), '/' );
                if ( '' === $rel ) {
                    continue;
                }
                // Unconditionally protect pdf-templates directory
                $rel_lower = strtolower( $rel );
                if ( 'pdf-templates' === $rel_lower || str_starts_with( $rel_lower, 'pdf-templates/' ) ) {
                    continue;
                }
                $dirs[] = $rel;
            }
        } catch ( \Throwable $e ) {
            // Keep root only on iterator error
        }

        sort( $dirs );
        return $dirs;
    }

    /**
     * Remove empty month/year directories up to (but never including) `$temp_dir`.
     */
    private static function cleanup_empty_parents( string $dir, string $temp_dir ): void {
        $current = rtrim( str_replace( '\\', '/', $dir ), '/' );
        $root    = rtrim( str_replace( '\\', '/', $temp_dir ), '/' );

        while ( '' !== $current && $current !== $root && str_starts_with( $current, $root . '/' ) ) {
            if ( ! is_dir( $current ) ) {
                break;
            }
            $files = @scandir( $current );
            if ( false === $files || count( $files ) > 2 ) {
                break; // Not empty (contains more than . and ..)
            }
            if ( ! @rmdir( $current ) ) {
                break;
            }
            $current = dirname( $current );
        }
    }

    /**
     * Get lightweight stats for the Admin UI (zero disk scanning).
     */
    public static function get_stats(): array {
        self::maybe_create_table();
        global $wpdb;
        $table        = self::get_table_name();
        $indexed_refs = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );
        $last_scan_ts = (int) get_option( 'nbd_s3_temp_last_scan_ts', 0 );

        $is_indexing = NBD_S3_Tier_Lifecycle::has_action_in_queue( 'nbd_s3_chain_temp_index', array( 'pending', 'in-progress' ) );
        $is_sweeping = NBD_S3_Tier_Lifecycle::has_action_in_queue( 'nbd_s3_chain_temp_sweep', array( 'pending', 'in-progress' ) );

        return array(
            'indexed_refs'   => $indexed_refs,
            'last_scan_ts'   => $last_scan_ts,
            'last_scan_text' => $last_scan_ts > 0 && function_exists( 'wp_date' ) ? wp_date( 'Y-m-d H:i:s', $last_scan_ts ) : __( 'Never', 'nbd-s3-offload' ),
            'is_indexing'    => $is_indexing,
            'is_sweeping'    => $is_sweeping,
        );
    }
}

