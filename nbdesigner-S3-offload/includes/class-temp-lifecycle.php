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
    public const DB_VERSION   = '1.0.0';

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
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table_name} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            temp_path VARCHAR(255) NOT NULL,
            design_folder VARCHAR(128) NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY  (id),
            UNIQUE KEY uniq_ref (temp_path(191), design_folder(64)),
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
     * Remove all indexed temp references for a specific design folder (e.g. when purged).
     */
    public static function delete_refs_for_folder( string $design_folder ): void {
        if ( '' === $design_folder ) {
            return;
        }
        self::maybe_create_table();
        global $wpdb;
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

        // On fresh full rebuild, clear old index table so deleted drafts/orders don't leave stale refs
        if ( 0 === $offset && $full_rebuild ) {
            $wpdb->query( "TRUNCATE TABLE {$table}" );
            delete_transient( 'nbd_s3_temp_index_dirs' );
        } elseif ( 0 === $offset ) {
            delete_transient( 'nbd_s3_temp_index_dirs' );
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
                    if ( $use_incr && $item->getMTime() < $incr_cutoff ) {
                        continue;
                    }
                    $folder_list[] = $item->getFilename();
                }
            } catch ( \Throwable $e ) {
                // Fallback if DirectoryIterator fails
                $dirs = glob( $designs_dir . '/*', GLOB_ONLYDIR );
                if ( is_array( $dirs ) ) {
                    foreach ( $dirs as $d ) {
                        if ( $use_incr && (int) @filemtime( $d ) < $incr_cutoff ) {
                            continue;
                        }
                        $folder_list[] = basename( $d );
                    }
                }
            }
            set_transient( 'nbd_s3_temp_index_dirs', $folder_list, 1800 );
        }

        $total_folders = count( $folder_list );
        $batch_size    = 500;
        $batch         = array_slice( $folder_list, $offset, $batch_size );

        $refs_found = 0;
        $now_mysql  = current_time( 'mysql', true );
        $rows_to_insert = array();

        foreach ( $batch as $folder_name ) {
            if ( $is_manual && get_transient( 'nbd_s3_temp_sweep_cancelled' ) ) {
                delete_transient( 'nbd_s3_temp_index_dirs' );
                delete_transient( 'nbd_s3_sync_status' );
                return array( 'success' => false, 'message' => __( 'Temp index stopped by Pause.', 'nbd-s3-offload' ) );
            }

            $folder_path = $designs_dir . '/' . $folder_name;
            $extracted   = self::extract_temp_refs_from_design_dir( $folder_path );

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

        // Indexing complete
        delete_transient( 'nbd_s3_temp_index_dirs' );
        update_option( 'nbd_s3_temp_last_scan_ts', time() );

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
                // Check references in chunks of 500 via indexed SQL
                foreach ( array_chunk( $candidates, 500 ) as $chunk ) {
                    $rel_paths = array_column( $chunk, 'rel' );
                    $ref_map   = self::lookup_referenced_paths( $rel_paths );

                    foreach ( $chunk as $file ) {
                        $checked++;
                        if ( isset( $ref_map[ $file['rel'] ] ) ) {
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
            set_transient( 'nbd_s3_temp_sweep_cursor', $cursor, 3600 );

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
     * Extract relative `temp/` paths from a single design folder's JSON config files.
     */
    private static function extract_temp_refs_from_design_dir( string $folder_path ): array {
        $json_files = array( 'design.json', 'config.json', 'product.json' );
        $found      = array();

        foreach ( $json_files as $filename ) {
            $file = $folder_path . '/' . $filename;
            if ( ! is_file( $file ) || ! is_readable( $file ) ) {
                continue;
            }
            $size = @filesize( $file );
            if ( false === $size || 0 === $size || $size > 15 * 1024 * 1024 ) {
                continue;
            }
            $content = @file_get_contents( $file );
            if ( false === $content || ! str_contains( $content, 'temp' ) ) {
                continue;
            }

            // Matches both normal `/nbdesigner/temp/...` and JSON-escaped `\/nbdesigner\/temp\/...`
            if ( preg_match_all( '#nbdesigner(?:\\\\/|/)temp(?:\\\\/|/)([^"\'\s<>\\\\]+(?:(?:\\\\/|/)[^"\'\s<>\\\\]+)*)#i', $content, $matches ) ) {
                foreach ( $matches[1] as $raw_rel ) {
                    $clean = str_replace( array( '\\/', '\\' ), '/', $raw_rel );
                    $clean = preg_replace( '#\?.*$#', '', $clean );
                    $clean = ltrim( (string) $clean, '/' );

                    if ( '' === $clean || str_starts_with( strtolower( $clean ), 'pdf-templates/' ) ) {
                        continue;
                    }
                    if ( strlen( $clean ) <= 255 ) {
                        $found[ $clean ] = true;
                    }
                }
            }
        }

        return array_keys( $found );
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
            $placeholders[] = '(%s, %s, %s)';
            $values[]       = $row[0];
            $values[]       = $row[1];
            $values[]       = $row[2];
        }

        $sql = "INSERT IGNORE INTO {$table} (temp_path, design_folder, updated_at) VALUES " . implode( ', ', $placeholders );
        $wpdb->query( $wpdb->prepare( $sql, $values ) );
    }

    /**
     * Query `wp_nbd_s3_temp_refs` for a batch of relative paths in one indexed query.
     */
    private static function lookup_referenced_paths( array $rel_paths ): array {
        if ( empty( $rel_paths ) ) {
            return array();
        }
        global $wpdb;
        $table        = self::get_table_name();
        $placeholders = implode( ',', array_fill( 0, count( $rel_paths ), '%s' ) );
        $sql          = $wpdb->prepare( "SELECT DISTINCT temp_path FROM {$table} WHERE temp_path IN ({$placeholders})", $rel_paths );
        $matched      = $wpdb->get_col( $sql );

        if ( empty( $matched ) || ! is_array( $matched ) ) {
            return array();
        }
        return array_fill_keys( $matched, true );
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
                if ( 'pdf-templates' === $rel || str_starts_with( strtolower( $rel ), 'pdf-templates/' ) ) {
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

