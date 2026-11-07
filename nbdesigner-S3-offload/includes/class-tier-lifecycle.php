<?php
/**
 * Tiered Storage Lifecycle & On-Demand Hydration Engine for NBDesigner
 *
 * @package NBD_S3_Offload
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NBD_S3_Tier_Lifecycle {

    private static ?NBD_S3_Client $client = null;

    /**
     * Initialize hooks and schedules.
     */
    public static function init(): void {
        // Pre-define font URL to CDN if offloading is active
        add_action( 'plugins_loaded', array( __CLASS__, 'maybe_predefine_cdn_urls' ), 1 );

        // On-demand hydration interception before design downloads & order views
        add_action( 'init', array( __CLASS__, 'intercept_download_hydration' ), 1 );
        add_action( 'init', array( __CLASS__, 'intercept_template_hydration' ), 1 );
        add_action( 'admin_init', array( __CLASS__, 'intercept_admin_order_view_hydration' ), 1 );

        // Action Scheduler hooks for background tasks
        add_action( 'nbd_s3_cron_cold_sweep', array( __CLASS__, 'run_cold_sweep' ) );
        add_action( 'nbd_s3_async_offload_item', array( __CLASS__, 'process_single_offload' ), 10, 2 );
        add_action( 'nbd_s3_cron_cleanup_drafts', array( __CLASS__, 'run_draft_cleanup' ) );

        // Register recurring schedules if Action Scheduler is available
        add_action( 'init', array( __CLASS__, 'register_schedules' ) );
    }

    /**
     * Get or instantiate the S3 client using saved settings.
     */
    public static function get_client(): NBD_S3_Client {
        if ( null === self::$client ) {
            $settings = NBD_S3_Settings::get_all();
            self::$client = new NBD_S3_Client( array(
                'access_key'     => $settings['access_key'],
                'secret_key'     => $settings['secret_key'],
                'region'         => $settings['region'],
                'bucket'         => $settings['bucket'],
                'endpoint'       => $settings['endpoint'],
                'use_path_style' => $settings['use_path_style'],
            ) );
        }
        return self::$client;
    }

    /**
     * Reset cached S3 client and connection transient.
     */
    public static function reset_client(): void {
        self::$client = null;
        delete_transient( 'nbd_s3_bucket_connection' );
    }

    /**
     * Pre-define CDN URLs before NBDesigner defines local defaults.
     */
    public static function maybe_predefine_cdn_urls(): void {
        $settings = NBD_S3_Settings::get_all();
        if ( empty( $settings['cdn_url'] ) ) {
            return;
        }

        $cdn_base = rtrim( $settings['cdn_url'], '/' );

        // If font offload is enabled, point NBDESIGNER_FONT_URL to CDN
        if ( ! empty( $settings['enable_font_offload'] ) && ! defined( 'NBDESIGNER_FONT_URL' ) ) {
            define( 'NBDESIGNER_FONT_URL', $cdn_base . '/fonts' );
        }
    }

    /**
     * Intercept front-end or admin file download requests to hydrate archived design folders.
     */
    public static function intercept_download_hydration(): void {
        // Check for download query flags used by NBDesigner
        $item_key = '';
        if ( isset( $_GET['download_nbd_design_file'] ) && ! empty( $_GET['nbd_item_key'] ) ) {
            $item_key = sanitize_text_field( wp_unslash( $_GET['nbd_item_key'] ) );
        } elseif ( isset( $_GET['codeblue'] ) ) {
            // codeblue is base64 encoded item_key or folder
            $decoded = base64_decode( sanitize_text_field( wp_unslash( $_GET['codeblue'] ) ) );
            if ( ! empty( $decoded ) && preg_match( '/^[a-zA-Z0-9_\-]+$/', $decoded ) ) {
                $item_key = $decoded;
            }
        }

        if ( ! empty( $item_key ) ) {
            self::ensure_design_hydrated( $item_key );
        }
    }

    /**
     * Intercept admin detail order screen to hydrate files if viewing an archived order.
     */
    public static function intercept_admin_order_view_hydration(): void {
        if ( isset( $_GET['page'] ) && 'nbdesigner_detail_order' === $_GET['page'] && ! empty( $_GET['nbd_item_key'] ) ) {
            $item_key = sanitize_text_field( wp_unslash( $_GET['nbd_item_key'] ) );
            self::ensure_design_hydrated( $item_key );
        }
    }

    /**
     * Intercept template editing/rendering to ensure template folder is hydrated locally.
     */
    public static function intercept_template_hydration(): void {
        global $wpdb;
        $folder = '';

        if ( isset( $_GET['template_id'] ) ) {
            $tid = absint( $_GET['template_id'] );
            $folder = (string) $wpdb->get_var( $wpdb->prepare( "SELECT folder FROM {$wpdb->prefix}nbdesigner_templates WHERE id = %d LIMIT 1", $tid ) );
        } elseif ( isset( $_GET['task'] ) && 'edit_template' === $_GET['task'] && isset( $_GET['id'] ) ) {
            $tid = absint( $_GET['id'] );
            $folder = (string) $wpdb->get_var( $wpdb->prepare( "SELECT folder FROM {$wpdb->prefix}nbdesigner_templates WHERE id = %d LIMIT 1", $tid ) );
        } elseif ( isset( $_GET['reference'] ) ) {
            $candidate = sanitize_file_name( wp_unslash( $_GET['reference'] ) );
            if ( self::is_admin_template( $candidate ) ) {
                $folder = $candidate;
            }
        } elseif ( isset( $_GET['rd'] ) && 'template' === $_GET['rd'] && isset( $_GET['id'] ) ) {
            $tid = absint( $_GET['id'] );
            $folder = (string) $wpdb->get_var( $wpdb->prepare( "SELECT folder FROM {$wpdb->prefix}nbdesigner_templates WHERE id = %d LIMIT 1", $tid ) );
        }

        if ( ! empty( $folder ) ) {
            self::ensure_design_hydrated( $folder );
        }
    }

    /**
     * Check if design folder exists locally; if cold/purged, download from S3.
     */
    public static function ensure_design_hydrated( string $item_key ): bool {
        $item_key = sanitize_file_name( $item_key );
        if ( empty( $item_key ) ) {
            return false;
        }

        $base_dir  = defined( 'NBDESIGNER_CUSTOMER_DIR' ) ? NBDESIGNER_CUSTOMER_DIR : ( WP_CONTENT_DIR . '/uploads/nbdesigner/designs' );
        $local_dir = rtrim( $base_dir, '/' ) . '/' . $item_key;

        // If local folder exists and has product.json or customer-pdfs, it's already hydrated
        if ( is_dir( $local_dir ) && ( file_exists( $local_dir . '/product.json' ) || is_dir( $local_dir . '/customer-pdfs' ) ) ) {
            return true;
        }

        // Hydrate from S3
        return self::hydrate_design_folder( $item_key );
    }

    /**
     * Hydrate a design folder by streaming all files from S3 back to local disk.
     */
    public static function hydrate_design_folder( string $item_key ): bool {
        $item_key = sanitize_file_name( $item_key );
        $client   = self::get_client();
        $prefix   = 'designs/' . $item_key . '/';

        $objects = $client->list_objects( $prefix, 500 );
        if ( is_wp_error( $objects ) || empty( $objects ) ) {
            // Fallback: check if objects were offloaded under nested bucket prefix
            $settings = NBD_S3_Settings::get_all();
            $bucket   = (string) ( $settings['bucket'] ?? '' );
            if ( ! empty( $bucket ) ) {
                $nested_prefix  = $bucket . '/designs/' . $item_key . '/';
                $nested_objects = $client->list_objects( $nested_prefix, 500 );
                if ( ! is_wp_error( $nested_objects ) && ! empty( $nested_objects ) ) {
                    $prefix  = $nested_prefix;
                    $objects = $nested_objects;
                }
            }
        }

        if ( is_wp_error( $objects ) || empty( $objects ) ) {
            return false;
        }

        $base_dir  = defined( 'NBDESIGNER_CUSTOMER_DIR' ) ? NBDESIGNER_CUSTOMER_DIR : ( WP_CONTENT_DIR . '/uploads/nbdesigner/designs' );
        $local_dir = rtrim( $base_dir, '/' ) . '/' . $item_key;

        // Preserve original folder modification timestamp
        $preserved_mtime = is_dir( $local_dir ) ? (int) @filemtime( $local_dir ) : 0;
        if ( empty( $preserved_mtime ) && ! empty( $objects ) ) {
            foreach ( $objects as $obj ) {
                if ( ! empty( $obj['last_modified'] ) ) {
                    $ts = strtotime( (string) $obj['last_modified'] );
                    if ( $ts > 0 && ( empty( $preserved_mtime ) || $ts < $preserved_mtime ) ) {
                        $preserved_mtime = $ts;
                    }
                }
            }
        }

        if ( ! is_dir( $local_dir ) ) {
            wp_mkdir_p( $local_dir );
        }

        foreach ( $objects as $obj ) {
            $s3_key   = $obj['key'];
            $rel_path = substr( $s3_key, strlen( $prefix ) );
            if ( empty( $rel_path ) ) {
                continue;
            }

            $target_file = $local_dir . '/' . $rel_path;
            $res = $client->get_object( $s3_key, $target_file );
            if ( is_wp_error( $res ) ) {
                // Log and continue attempting remaining files
                continue;
            }

            if ( ! empty( $obj['last_modified'] ) ) {
                $file_ts = strtotime( (string) $obj['last_modified'] );
                if ( $file_ts > 0 ) {
                    @touch( $target_file, $file_ts );
                }
            }
        }

        delete_transient( 'nbd_s3_explorer_dirs_v2' );
        self::record_folder_restored( $item_key );

        if ( is_dir( $local_dir ) && file_exists( $local_dir . '/.s3_offloaded' ) ) {
            @unlink( $local_dir . '/.s3_offloaded' );
        }

        // Restore preserved folder modification time so folder age is retained
        if ( $preserved_mtime > 0 && is_dir( $local_dir ) ) {
            @touch( $local_dir, $preserved_mtime );
        }

        global $wpdb;
        $order_meta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$order_meta_table}'" ) === $order_meta_table ) {
            $order_item_ids = $wpdb->get_col( $wpdb->prepare(
                "SELECT DISTINCT order_item_id FROM {$order_meta_table} 
                 WHERE (meta_key = '_nbd' OR meta_key = '_nbd_item_key') AND meta_value = %s",
                $item_key
            ) );
            if ( ! empty( $order_item_ids ) ) {
                foreach ( $order_item_ids as $oi_id ) {
                    wc_delete_order_item_meta( $oi_id, '_nbd_s3_offloaded' );
                    wc_delete_order_item_meta( $oi_id, '_nbd_s3_offloaded_time' );
                }
            }
        }

        NBD_S3_Logger::log( 'RESTORE', sprintf( 'Restored folder %s from S3 to local SSD.', $item_key ), array( 'folder' => $item_key ), 'success' );
        return true;
    }

    /**
     * Get offloaded folders registry.
     */
    public static function get_offloaded_registry(): array {
        return (array) get_option( 'nbd_s3_offloaded_registry', array() );
    }

    /**
     * Record a folder as offloaded in the registry.
     */
    public static function record_folder_offloaded( string $folder ): void {
        $registry = self::get_offloaded_registry();
        $registry[ $folder ] = time();
        update_option( 'nbd_s3_offloaded_registry', $registry, 'no' );
    }

    /**
     * Remove a folder from offloaded registry upon restoration.
     */
    public static function record_folder_restored( string $folder ): void {
        $registry = self::get_offloaded_registry();
        if ( isset( $registry[ $folder ] ) ) {
            unset( $registry[ $folder ] );
            update_option( 'nbd_s3_offloaded_registry', $registry, 'no' );
        }
    }

    /**
     * Check if a folder belongs to Tier 1 Admin Templates Whitelist.
     */
    public static function is_admin_template( string $folder ): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'nbdesigner_templates';
        $found = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE folder = %s LIMIT 1", $folder ) );
        return ! empty( $found );
    }

    /**
     * Scan all whitelisted admin templates and ensure all files are restored locally from S3.
     */
    public static function restore_all_whitelisted_templates(): array {
        global $wpdb;
        $table   = $wpdb->prefix . 'nbdesigner_templates';
        $folders = $wpdb->get_col( "SELECT DISTINCT folder FROM {$table} WHERE folder IS NOT NULL AND folder != ''" );
        if ( empty( $folders ) ) {
            return array(
                'total'         => 0,
                'restored'      => 0,
                'already_local' => 0,
            );
        }

        $base_dir      = defined( 'NBDESIGNER_CUSTOMER_DIR' ) ? NBDESIGNER_CUSTOMER_DIR : ( WP_CONTENT_DIR . '/uploads/nbdesigner/designs' );
        $restored      = 0;
        $already_local = 0;

        foreach ( $folders as $folder ) {
            $folder = sanitize_file_name( $folder );
            if ( empty( $folder ) ) {
                continue;
            }
            $local_dir = rtrim( $base_dir, '/' ) . '/' . $folder;

            // If folder is missing or missing product.json, hydrate from S3
            if ( ! is_dir( $local_dir ) || ! file_exists( $local_dir . '/product.json' ) ) {
                if ( self::hydrate_design_folder( $folder ) ) {
                    $restored++;
                }
            } else {
                $already_local++;
            }
        }

        delete_transient( 'nbd_s3_explorer_dirs_v2' );
        NBD_S3_Logger::log( 'WHITELIST_RESTORE', sprintf( 'Restored %d whitelisted admin templates to local SSD (%d already local).', $restored, $already_local ), array( 'restored' => $restored, 'already_local' => $already_local ), 'info' );
        return array(
            'total'         => count( $folders ),
            'restored'      => $restored,
            'already_local' => $already_local,
        );
    }

    /**
     * Get design folders for admin sandbox explorer.
     */
    public static function get_design_folders_explorer( int $limit = 20, int $offset = 0, string $filter = 'all', bool $force_refresh = false, string $search = '' ): array {
        global $wpdb;
        $base_dir = defined( 'NBDESIGNER_CUSTOMER_DIR' ) ? NBDESIGNER_CUSTOMER_DIR : ( WP_CONTENT_DIR . '/uploads/nbdesigner/designs' );
        if ( ! is_dir( $base_dir ) ) {
            return array( 'total' => 0, 'folders' => array() );
        }

        $cache_key   = 'nbd_s3_explorer_dirs_v2';
        $cached_dirs = $force_refresh ? false : get_transient( $cache_key );

        if ( false === $cached_dirs ) {
            $cached_dirs = array();
            try {
                $iterator = new \DirectoryIterator( $base_dir );
                foreach ( $iterator as $item ) {
                    if ( $item->isDot() || ! $item->isDir() ) {
                        continue;
                    }
                    $cached_dirs[] = array(
                        'folder' => $item->getFilename(),
                        'mtime'  => $item->getMTime(),
                    );
                }
            } catch ( \Throwable $e ) {
                $dirs = glob( rtrim( $base_dir, '/' ) . '/*', GLOB_ONLYDIR );
                if ( ! empty( $dirs ) ) {
                    foreach ( $dirs as $d ) {
                        $cached_dirs[] = array(
                            'folder' => basename( $d ),
                            'mtime'  => (int) @filemtime( $d ),
                        );
                    }
                }
            }

            // Sort by mtime descending (newest first)
            usort( $cached_dirs, function( $a, $b ) {
                return $b['mtime'] <=> $a['mtime'];
            } );

            set_transient( $cache_key, $cached_dirs, 300 ); // 5 minutes cache
        }

        if ( empty( $cached_dirs ) ) {
            return array( 'total' => 0, 'folders' => array() );
        }

        // Single batch query: whitelisted admin templates
        $template_table  = $wpdb->prefix . 'nbdesigner_templates';
        $template_lookup = array();
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$template_table}'" ) === $template_table ) {
            $tpl_col = $wpdb->get_col( "SELECT DISTINCT folder FROM {$template_table} WHERE folder IS NOT NULL AND folder != ''" );
            if ( ! empty( $tpl_col ) ) {
                $template_lookup = array_flip( $tpl_col );
            }
        }

        // Single batch query: offloaded WooCommerce order items
        $order_meta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
        $offloaded_lookup = array();
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$order_meta_table}'" ) === $order_meta_table ) {
            $off_col = $wpdb->get_col(
                "SELECT DISTINCT m1.meta_value 
                 FROM {$order_meta_table} m1 
                 INNER JOIN {$order_meta_table} m2 ON m1.order_item_id = m2.order_item_id 
                 WHERE (m1.meta_key = '_nbd' OR m1.meta_key = '_nbd_item_key') 
                   AND m2.meta_key = '_nbd_s3_offloaded' 
                   AND m2.meta_value = '1'"
            );
            if ( ! empty( $off_col ) ) {
                $offloaded_lookup = array_flip( $off_col );
            }
        }

        $registry_lookup = self::get_offloaded_registry();

        $settings     = NBD_S3_Settings::get_all();
        $retention    = (int) ( $settings['cold_retention_days'] ?? 14 );
        $cutoff       = time() - ( $retention * DAY_IN_SECONDS );
        $draft_days   = (int) ( $settings['abandoned_drafts_days'] ?? 7 );
        $draft_cutoff = time() - ( $draft_days * DAY_IN_SECONDS );

        $all_order_folders = array();
        $all_saved_folders = array();
        if ( 'drafts' === $filter ) {
            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$order_meta_table}'" ) === $order_meta_table ) {
                $order_cols = $wpdb->get_col( "SELECT DISTINCT meta_value FROM {$order_meta_table} WHERE meta_key = '_nbd' OR meta_key = '_nbd_item_key'" );
                if ( ! empty( $order_cols ) ) {
                    $all_order_folders = array_flip( $order_cols );
                }
            }
            $mydesigns_table = $wpdb->prefix . 'nbdesigner_mydesigns';
            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$mydesigns_table}'" ) === $mydesigns_table ) {
                $saved_cols = $wpdb->get_col( "SELECT DISTINCT folder FROM {$mydesigns_table} WHERE folder IS NOT NULL AND folder != ''" );
                if ( ! empty( $saved_cols ) ) {
                    $all_saved_folders = array_flip( $saved_cols );
                }
            }
        }

        $matched_items = array();

        foreach ( $cached_dirs as $d ) {
            $folder       = $d['folder'];
            $mtime        = $d['mtime'];
            $is_tpl       = isset( $template_lookup[ $folder ] );
            $is_aged      = ( $mtime < $cutoff );
            $is_offloaded = isset( $offloaded_lookup[ $folder ] ) || isset( $registry_lookup[ $folder ] );

            // Search filter
            if ( '' !== $search && false === stripos( $folder, $search ) ) {
                continue;
            }

            // Category filter
            if ( 'templates' === $filter && ! $is_tpl ) {
                continue;
            }
            if ( 'active' === $filter && ( $is_offloaded || $is_tpl || $is_aged ) ) {
                continue;
            }
            if ( 'aged' === $filter && ( ! $is_aged || $is_tpl ) ) {
                continue;
            }
            if ( 'offloaded' === $filter && ! $is_offloaded ) {
                continue;
            }
            if ( 'local' === $filter && $is_offloaded ) {
                continue;
            }
            if ( 'drafts' === $filter ) {
                if ( ! empty( $settings['protect_active_carts'] ) && self::is_in_active_cart( $folder ) ) {
                    continue;
                }
                if ( $is_tpl || $mtime >= $draft_cutoff || isset( $all_order_folders[ $folder ] ) || isset( $all_saved_folders[ $folder ] ) ) {
                    continue;
                }
            }

            $matched_items[] = array(
                'folder'       => $folder,
                'mtime'        => $mtime,
                'is_template'  => $is_tpl,
                'is_aged'      => $is_aged,
                'is_offloaded' => $is_offloaded,
            );
        }

        $total = count( $matched_items );
        $slice = array_slice( $matched_items, $offset, $limit );

        // Deep inspection only for the paged subset (max 20-30 items)
        $folders = array();
        foreach ( $slice as $item ) {
            $folder    = $item['folder'];
            $local_dir = rtrim( $base_dir, '/' ) . '/' . $folder;
            $count     = 0;
            $bytes     = 0;
            $has_heavy = false;

            if ( is_dir( $local_dir ) ) {
                $files = self::get_recursive_files( $local_dir );
                $count = count( $files );
                foreach ( $files as $f ) {
                    $bytes += (int) @filesize( $f );
                    $ext    = strtolower( pathinfo( $f, PATHINFO_EXTENSION ) );
                    if ( in_array( $ext, array( 'pdf', 'png', 'jpg', 'jpeg', 'tif', 'tiff' ), true ) ) {
                        $has_heavy = true;
                    }
                }
            }

            $is_offloaded = $item['is_offloaded'];
            if ( ! $is_offloaded && is_dir( $local_dir ) && file_exists( $local_dir . '/.s3_offloaded' ) ) {
                $is_offloaded = true;
                self::record_folder_offloaded( $folder );
            }
            if ( ! $is_offloaded && ! $has_heavy && is_dir( $local_dir ) && ( file_exists( $local_dir . '/product.json' ) || file_exists( $local_dir . '/design.json' ) ) && $count > 0 && $count <= 3 ) {
                $is_offloaded = true;
                self::record_folder_offloaded( $folder );
            }

            $mtime = $item['mtime'];

            // Fast draft check for the 25 paged items
            $is_draft   = false;
            $is_in_cart = false;
            if ( ! $item['is_template'] && ! $is_offloaded ) {
                if ( 'drafts' === $filter ) {
                    $is_draft = true;
                } else {
                    $has_order = $wpdb->get_var( $wpdb->prepare( "SELECT order_item_id FROM {$order_meta_table} WHERE (meta_key = '_nbd' OR meta_key = '_nbd_item_key') AND meta_value = %s LIMIT 1", $folder ) );
                    if ( ! $has_order ) {
                        $mydesigns_table = $wpdb->prefix . 'nbdesigner_mydesigns';
                        $has_saved = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$mydesigns_table} WHERE folder = %s LIMIT 1", $folder ) );
                        if ( ! $has_saved ) {
                            $is_draft = true;
                        }
                    }
                }
                if ( $is_draft ) {
                    $is_in_cart = self::is_in_active_cart( $folder );
                }
            }

            $folders[] = array(
                'folder'         => $folder,
                'is_template'    => $item['is_template'],
                'is_aged'        => $item['is_aged'],
                'is_offloaded'   => $is_offloaded,
                'is_draft'       => $is_draft,
                'is_in_cart'     => $is_in_cart,
                'files_count'    => $count,
                'size_bytes'     => $bytes,
                'size_formatted' => size_format( $bytes ),
                'modified_time'  => $mtime,
                'modified_date'  => wp_date( 'Y-m-d H:i', $mtime ),
                'age_days'       => (int) round( ( time() - $mtime ) / DAY_IN_SECONDS ),
            );
        }

        return array(
            'total'   => $total,
            'folders' => $folders,
        );
    }

    /**
     * Batch test offload on a specific number of aged folders.
     */
    public static function test_batch_offload( int $count = 3 ): array {
        $explorer = self::get_design_folders_explorer( 100, 0, 'aged' );
        $eligible = array();

        foreach ( $explorer['folders'] as $item ) {
            if ( ! $item['is_template'] && ! $item['is_offloaded'] ) {
                $eligible[] = $item['folder'];
                if ( count( $eligible ) >= $count ) {
                    break;
                }
            }
        }

        if ( empty( $eligible ) ) {
            return array(
                'success' => false,
                'message' => 'No eligible aged non-template folders found to test offload.',
                'results' => array(),
            );
        }

        $results = array();
        $total_uploaded = 0;

        foreach ( $eligible as $folder ) {
            $res = self::process_single_offload( $folder );
            $results[] = array(
                'folder'  => $folder,
                'success' => $res['success'] ?? false,
                'message' => $res['message'] ?? '',
            );
            if ( ! empty( $res['success'] ) ) {
                $total_uploaded++;
            }
        }

        $msg = sprintf( 'Tested offload on %d folders: %d succeeded.', count( $eligible ), $total_uploaded );
        NBD_S3_Logger::log( 'TRIAL_BATCH', $msg, array( 'tested' => count( $eligible ), 'succeeded' => $total_uploaded ), $total_uploaded > 0 ? 'success' : 'warning' );

        return array(
            'success'        => true,
            'message'        => $msg,
            'total_tested'   => count( $eligible ),
            'total_uploaded' => $total_uploaded,
            'results'        => $results,
        );
    }

    /**
     * Run daily sweep for cold orders eligible for S3 offload.
     */
    public static function run_cold_sweep(): void {
        $settings = NBD_S3_Settings::get_all();

        // Safety Kill-Switch: Skip if syncing is paused by admin
        if ( ! empty( $settings['sync_paused'] ) ) {
            NBD_S3_Logger::log( 'SWEEP', __( 'Automated Cold Sweep skipped because sync is paused.', 'nbd-s3-offload' ), array(), 'warning' );
            return;
        }

        if ( empty( $settings['enable_cold_order_offload'] ) ) {
            return;
        }

        // Set running state indicator
        set_transient( 'nbd_s3_sync_status', array(
            'running'    => true,
            'action'     => 'Cold Sweep',
            'started_at' => time(),
        ), 1800 );

        $processed = 0;
        try {
            $days       = $settings['cold_retention_days'];
            $cutoff_ts  = time() - ( $days * DAY_IN_SECONDS );
            $cutoff_str = gmdate( 'Y-m-d H:i:s', $cutoff_ts );

            // Query completed orders older than retention threshold
            $orders = wc_get_orders( array(
                'status'        => array( 'wc-completed' ),
                'date_created'  => '<' . $cutoff_ts,
                'limit'         => 30,
                'return'        => 'ids',
            ) );

            if ( ! empty( $orders ) ) {
                foreach ( $orders as $order_id ) {
                    $order = wc_get_order( $order_id );
                    if ( ! $order ) {
                        continue;
                    }

                    foreach ( $order->get_items() as $item_id => $item ) {
                        $item_key = $item->get_meta( '_nbd' ) ?: $item->get_meta( '_nbd_item_key' );

                        if ( ! empty( $item_key ) ) {
                            $already_offloaded = $item->get_meta( '_nbd_s3_offloaded' );
                            if ( empty( $already_offloaded ) ) {
                                $res = self::process_single_offload( $item_key, $order_id );
                                if ( ! empty( $res['success'] ) ) {
                                    $processed++;
                                }
                            }
                        }
                    }
                }
            }

            NBD_S3_Logger::log( 'SWEEP', sprintf( 'Automated Cold Sweep completed. Offloaded %d design items.', $processed ), array( 'processed' => $processed ), 'success' );
        } finally {
            delete_transient( 'nbd_s3_sync_status' );
        }
    }

    /**
     * Process offload for a single order item key: upload to S3, verify, and purge local files.
     */
    public static function process_single_offload( string $item_key, int $order_id = 0 ): array {
        $item_key = sanitize_file_name( $item_key );
        if ( empty( $item_key ) ) {
            return array( 'success' => false, 'message' => 'Empty item key' );
        }

        // Tier 1 Whitelist protection: Never offload/delete admin templates!
        if ( self::is_admin_template( $item_key ) ) {
            return array( 'success' => false, 'message' => 'Skipped: Whitelisted Admin Template' );
        }

        $base_dir  = defined( 'NBDESIGNER_CUSTOMER_DIR' ) ? NBDESIGNER_CUSTOMER_DIR : ( WP_CONTENT_DIR . '/uploads/nbdesigner/designs' );
        $local_dir = rtrim( $base_dir, '/' ) . '/' . $item_key;

        if ( ! is_dir( $local_dir ) ) {
            return array( 'success' => false, 'message' => 'Local directory not found' );
        }

        $original_mtime = (int) @filemtime( $local_dir );

        $client   = self::get_client();
        $files    = self::get_recursive_files( $local_dir );
        $uploaded   = 0;
        $failed     = 0;
        $settings   = NBD_S3_Settings::get_all();
        $object_acl = $settings['default_object_acl'] ?? 'public-read';

        foreach ( $files as $file_path ) {
            $rel_path = ltrim( substr( $file_path, strlen( $local_dir ) ), '/\\' );
            $s3_key   = 'designs/' . $item_key . '/' . str_replace( '\\', '/', $rel_path );

            $put_res = $client->put_object( $s3_key, $file_path, '', array(), true, $object_acl );
            if ( is_wp_error( $put_res ) ) {
                $failed++;
            } else {
                $uploaded++;
            }
        }

        if ( $failed > 0 || 0 === $uploaded ) {
            $err_msg = sprintf( 'Failed to offload %d files to S3 for %s (%d uploaded).', $failed, $item_key, $uploaded );
            NBD_S3_Logger::log( 'OFFLOAD_ERROR', $err_msg, array( 'folder' => $item_key, 'failed' => $failed, 'uploaded' => $uploaded ), 'error' );
            return array(
                'success'  => false,
                'message'  => sprintf( 'Failed to offload %d files to S3', $failed ),
                'uploaded' => $uploaded,
            );
        }

        // Verification step: verify files exist on S3 before deleting local copy
        $head_res = $client->head_object( 'designs/' . $item_key . '/' . ( file_exists( $local_dir . '/product.json' ) ? 'product.json' : 'design.json' ) );
        if ( is_wp_error( $head_res ) ) {
            NBD_S3_Logger::log( 'OFFLOAD_ERROR', sprintf( 'Integrity verification failed on S3 for %s.', $item_key ), array( 'folder' => $item_key ), 'error' );
            return array( 'success' => false, 'message' => 'Integrity verification failed on S3' );
        }

        // Purge local files based on setting
        $settings   = NBD_S3_Settings::get_all();
        $purge_mode = $settings['purge_mode'] ?? 'smart';

        if ( 'full' === $purge_mode ) {
            self::delete_directory( $local_dir );
        } else {
            // Smart purge: delete heavy print files, retain small metadata JSONs
            self::smart_purge_directory( $local_dir );
            // Mark folder as offloaded to S3 locally
            @file_put_contents( $local_dir . '/.s3_offloaded', wp_json_encode( array(
                'time'  => time(),
                'mode'  => $purge_mode,
                'files' => $uploaded,
            ) ) );
            // Preserve original directory modification time so folder age does not reset to 0
            if ( $original_mtime > 0 ) {
                @touch( $local_dir, $original_mtime );
            }
        }

        // Record offload in persistent registry
        self::record_folder_offloaded( $item_key );

        // Record order item metadata across all linked orders
        global $wpdb;
        $order_meta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$order_meta_table}'" ) === $order_meta_table ) {
            $order_item_ids = $wpdb->get_col( $wpdb->prepare(
                "SELECT DISTINCT order_item_id FROM {$order_meta_table} 
                 WHERE (meta_key = '_nbd' OR meta_key = '_nbd_item_key') AND meta_value = %s",
                $item_key
            ) );
            if ( ! empty( $order_item_ids ) ) {
                foreach ( $order_item_ids as $oi_id ) {
                    wc_update_order_item_meta( $oi_id, '_nbd_s3_offloaded', 1 );
                    wc_update_order_item_meta( $oi_id, '_nbd_s3_offloaded_time', time() );
                    wc_update_order_item_meta( $oi_id, '_nbd_s3_purge_mode', $purge_mode );
                }
            }
        }

        // Also record order item metadata if order_id is provided directly
        if ( $order_id > 0 ) {
            $order = wc_get_order( $order_id );
            if ( $order ) {
                foreach ( $order->get_items() as $item ) {
                    $key = $item->get_meta( '_nbd' ) ?: $item->get_meta( '_nbd_item_key' );
                    if ( $key === $item_key ) {
                        $item->update_meta_data( '_nbd_s3_offloaded', 1 );
                        $item->update_meta_data( '_nbd_s3_offloaded_time', time() );
                        $item->update_meta_data( '_nbd_s3_purge_mode', $purge_mode );
                        $item->save();
                    }
                }
            }
        }

        delete_transient( 'nbd_s3_explorer_dirs_v2' );
        NBD_S3_Logger::log( 'OFFLOAD', sprintf( 'Offloaded %s (%d files) to S3.', $item_key, $uploaded ), array( 'folder' => $item_key, 'files' => $uploaded, 'purge_mode' => $purge_mode ), 'success' );

        return array(
            'success'  => true,
            'message'  => sprintf( 'Successfully offloaded %d files to S3 and applied %s purge.', $uploaded, $purge_mode ),
            'uploaded' => $uploaded,
        );
    }

    /**
     * Purge remote fonts on S3 and resync all local font files.
     */
    public static function purge_and_resync_fonts(): array {
        $font_dir = defined( 'NBDESIGNER_FONT_DIR' ) ? NBDESIGNER_FONT_DIR : ( WP_CONTENT_DIR . '/uploads/nbdesigner/fonts' );
        if ( ! is_dir( $font_dir ) ) {
            return array(
                'success' => false,
                'message' => __( 'Local font directory does not exist.', 'nbd-s3-offload' ),
            );
        }

        $client   = self::get_client();
        $settings = NBD_S3_Settings::get_all();
        $max_age  = $settings['font_cache_max_age'] ?? 2592000;

        $files    = self::get_recursive_files( $font_dir );
        $uploaded = 0;
        $failed   = 0;
        $bytes    = 0;

        foreach ( $files as $file_path ) {
            $rel_path = ltrim( substr( $file_path, strlen( $font_dir ) ), '/\\' );
            $s3_key   = 'fonts/' . str_replace( '\\', '/', $rel_path );

            $headers = array(
                'Cache-Control'               => 'public, max-age=' . $max_age . ', immutable',
                'Access-Control-Allow-Origin' => '*',
            );

            // Public read ACL required for DigitalOcean Spaces / S3 font loading in customer browser
            $font_acl = ( 'none' === ( $settings['default_object_acl'] ?? '' ) ) ? 'none' : 'public-read';
            $put_res  = $client->put_object( $s3_key, $file_path, '', $headers, true, $font_acl );
            if ( is_wp_error( $put_res ) ) {
                $failed++;
            } else {
                $uploaded++;
                $bytes += (int) filesize( $file_path );
            }
        }

        $msg = sprintf( __( 'Font sync complete. Uploaded %1$d files (%2$s) with 30-day immutable cache.', 'nbd-s3-offload' ), $uploaded, size_format( $bytes ) );
        NBD_S3_Logger::log( 'FONT_SYNC', $msg, array( 'uploaded' => $uploaded, 'failed' => $failed, 'bytes' => $bytes ), 0 === $failed ? 'success' : 'warning' );

        return array(
            'success'  => 0 === $failed && $uploaded > 0,
            'message'  => $msg,
            'uploaded' => $uploaded,
            'failed'   => $failed,
            'bytes'    => $bytes,
        );
    }

    /**
     * Check if a design folder is currently held in any active guest session or registered user cart.
     */
    public static function is_in_active_cart( string $folder ): bool {
        global $wpdb;

        if ( empty( $folder ) ) {
            return false;
        }

        $folder_like = '%' . $wpdb->esc_like( $folder ) . '%';

        // 1. Check registered users' persistent carts in usermeta
        $in_user_cart = $wpdb->get_var( $wpdb->prepare(
            "SELECT umeta_id FROM {$wpdb->usermeta} WHERE meta_key LIKE '_woocommerce_persistent_cart_%' AND meta_value LIKE %s LIMIT 1",
            $folder_like
        ) );
        if ( ! empty( $in_user_cart ) ) {
            return true;
        }

        // 2. Check active WooCommerce sessions (guests & active shoppers)
        $table_sessions = $wpdb->prefix . 'woocommerce_sessions';
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_sessions ) ) === $table_sessions ) {
            $in_session = $wpdb->get_var( $wpdb->prepare(
                "SELECT session_id FROM {$table_sessions} WHERE session_expiry > %d AND session_value LIKE %s LIMIT 1",
                time(),
                $folder_like
            ) );
            if ( ! empty( $in_session ) ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Daily cleanup for abandoned guest draft folders older than threshold.
     */
    public static function run_draft_cleanup(): void {
        global $wpdb;
        $settings = NBD_S3_Settings::get_all();
        if ( ! empty( $settings['sync_paused'] ) || empty( $settings['cleanup_abandoned_drafts'] ) ) {
            return;
        }

        $days      = $settings['abandoned_drafts_days'] ?? 7;
        $cutoff_ts = time() - ( $days * DAY_IN_SECONDS );
        $base_dir  = defined( 'NBDESIGNER_CUSTOMER_DIR' ) ? NBDESIGNER_CUSTOMER_DIR : ( WP_CONTENT_DIR . '/uploads/nbdesigner/designs' );

        if ( ! is_dir( $base_dir ) ) {
            return;
        }

        $dirs = glob( rtrim( $base_dir, '/' ) . '/*', GLOB_ONLYDIR );
        if ( empty( $dirs ) ) {
            return;
        }

        $deleted_count = 0;
        foreach ( $dirs as $dir ) {
            $folder = basename( $dir );
            $mtime  = filemtime( $dir );

            if ( $mtime >= $cutoff_ts ) {
                continue; // Still active / within grace period
            }

            // Check if template
            if ( self::is_admin_template( $folder ) ) {
                continue;
            }

            // Check if user saved design
            $saved = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}nbdesigner_mydesigns WHERE folder = %s LIMIT 1", $folder ) );
            if ( $saved ) {
                continue;
            }

            // Check if attached to order item
            $order_item = $wpdb->get_var( $wpdb->prepare( "SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE (meta_key = '_nbd' OR meta_key = '_nbd_item_key') AND meta_value = %s LIMIT 1", $folder ) );
            if ( $order_item ) {
                continue;
            }

            // Active Cart Protection Shield: Protect if design is inside any live cart
            if ( ! empty( $settings['protect_active_carts'] ) && self::is_in_active_cart( $folder ) ) {
                continue;
            }

            // Unclaimed orphan draft older than cutoff -> purge
            if ( self::delete_directory( $dir ) ) {
                $deleted_count++;
            }
        }

        if ( $deleted_count > 0 ) {
            NBD_S3_Logger::log( 'DRAFT_CLEANUP', sprintf( 'Cleaned up %d abandoned guest drafts.', $deleted_count ), array( 'deleted' => $deleted_count ), 'info' );
        }
    }

    /**
     * Smart purge: removes heavy print files and subdirectories, retaining lightweight JSONs.
     */
    private static function smart_purge_directory( string $dir ): void {
        $heavy_subdirs = array( 'customer-pdfs', 'customer-png', 'customer-jpg', 'customer-pdfs-watermark', 'svg', 'part' );
        foreach ( $heavy_subdirs as $subdir ) {
            $target = $dir . '/' . $subdir;
            if ( is_dir( $target ) ) {
                self::delete_directory( $target );
            }
        }

        // Delete standalone heavy files in root of design folder
        $heavy_extensions = array( 'pdf', 'png', 'jpg', 'jpeg', 'zip' );
        $files = glob( $dir . '/*' );
        if ( ! empty( $files ) ) {
            foreach ( $files as $file ) {
                if ( is_file( $file ) ) {
                    $ext = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
                    if ( in_array( $ext, $heavy_extensions, true ) ) {
                        @unlink( $file );
                    }
                }
            }
        }
    }

    /**
     * Recursive file scanner.
     */
    private static function get_recursive_files( string $dir ): array {
        $results = array();
        $files   = scandir( $dir );
        if ( false === $files ) {
            return $results;
        }

        foreach ( $files as $value ) {
            if ( '.' === $value || '..' === $value || '.' === $value[0] ) {
                continue;
            }
            $path = $dir . '/' . $value;
            if ( is_dir( $path ) ) {
                $results = array_merge( $results, self::get_recursive_files( $path ) );
            } else {
                $results[] = $path;
            }
        }

        return $results;
    }

    /**
     * Recursive directory deletion.
     */
    private static function delete_directory( string $dir ): bool {
        if ( ! is_dir( $dir ) ) {
            return false;
        }

        $files = array_diff( scandir( $dir ) ?: array(), array( '.', '..' ) );
        foreach ( $files as $file ) {
            $path = $dir . '/' . $file;
            is_dir( $path ) ? self::delete_directory( $path ) : @unlink( $path );
        }

        return @rmdir( $dir );
    }

    /**
     * Calculate unix timestamp for next scheduled sync time.
     */
    public static function calculate_next_schedule_timestamp( string $time_str = '03:00' ): int {
        $tz = function_exists( 'wp_timezone' ) ? wp_timezone() : new DateTimeZone( 'UTC' );
        $now = new DateTimeImmutable( 'now', $tz );
        $parts = explode( ':', trim( $time_str ) );
        $hour = isset( $parts[0] ) ? (int) $parts[0] : 3;
        $minute = isset( $parts[1] ) ? (int) $parts[1] : 0;

        $scheduled = $now->setTime( $hour, $minute, 0 );
        if ( $scheduled <= $now ) {
            $scheduled = $scheduled->modify( '+1 day' );
        }
        return $scheduled->getTimestamp();
    }

    /**
     * Reschedule Action Scheduler background sweep to match sync_schedule_time.
     */
    public static function reschedule_tasks(): void {
        if ( ! function_exists( 'as_schedule_recurring_action' ) ) {
            return;
        }

        $settings = NBD_S3_Settings::get_all();
        $target_time = $settings['sync_schedule_time'] ?? '03:00';
        $start_ts = self::calculate_next_schedule_timestamp( $target_time );

        if ( function_exists( 'as_unschedule_all_actions' ) ) {
            as_unschedule_all_actions( 'nbd_s3_cron_cold_sweep' );
        }
        as_schedule_recurring_action( $start_ts, DAY_IN_SECONDS, 'nbd_s3_cron_cold_sweep' );
    }

    /**
     * Register recurring Action Scheduler tasks.
     */
    public static function register_schedules(): void {
        if ( function_exists( 'as_schedule_recurring_action' ) ) {
            if ( ! as_has_scheduled_action( 'nbd_s3_cron_cold_sweep' ) ) {
                $settings = NBD_S3_Settings::get_all();
                $target_time = $settings['sync_schedule_time'] ?? '03:00';
                $start_ts = self::calculate_next_schedule_timestamp( $target_time );
                as_schedule_recurring_action( $start_ts, DAY_IN_SECONDS, 'nbd_s3_cron_cold_sweep' );
            }
            if ( ! as_has_scheduled_action( 'nbd_s3_cron_cleanup_drafts' ) ) {
                as_schedule_recurring_action( time() + 3600, DAY_IN_SECONDS, 'nbd_s3_cron_cleanup_drafts' );
            }
        }
    }

    /**
     * Get live sync running and pause status.
     */
    public static function get_sync_status(): array {
        $settings = NBD_S3_Settings::get_all();
        $transient = get_transient( 'nbd_s3_sync_status' );
        $is_running = ! empty( $transient ) && ! empty( $transient['running'] );

        $next_run_ts = function_exists( 'as_next_scheduled_action' ) ? as_next_scheduled_action( 'nbd_s3_cron_cold_sweep' ) : null;
        $next_run_formatted = $next_run_ts && function_exists( 'wp_date' ) ? wp_date( 'Y-m-d H:i:s T', $next_run_ts ) : null;

        $tz_string = function_exists( 'wp_timezone_string' ) ? wp_timezone_string() : 'UTC';

        return array(
            'is_running'         => $is_running,
            'is_paused'          => ! empty( $settings['sync_paused'] ),
            'current_action'     => $is_running ? ( $transient['action'] ?? 'Running' ) : 'Idle',
            'started_at'         => $is_running ? ( $transient['started_at'] ?? null ) : null,
            'server_time'        => function_exists( 'wp_date' ) ? wp_date( 'Y-m-d H:i:s' ) : gmdate( 'Y-m-d H:i:s' ),
            'server_timezone'    => $tz_string,
            'sync_schedule_time' => $settings['sync_schedule_time'] ?? '03:00',
            'next_scheduled_run' => $next_run_formatted,
        );
    }

    /**
     * Check live S3 bucket connection status.
     */
    public static function get_bucket_connection_status( bool $force = false ): array {
        $transient_key = 'nbd_s3_bucket_connection';
        if ( ! $force ) {
            $cached = get_transient( $transient_key );
            if ( is_array( $cached ) ) {
                return $cached;
            }
        }

        $settings = NBD_S3_Settings::get_all();
        $bucket   = trim( $settings['bucket'] ?? '' );
        $key      = trim( $settings['access_key'] ?? '' );
        $secret   = trim( $settings['secret_key'] ?? '' );

        if ( empty( $bucket ) || empty( $key ) || empty( $secret ) ) {
            $res = array(
                'connected'  => false,
                'message'    => __( 'Credentials or bucket not configured.', 'nbd-s3-offload' ),
                'bucket'     => $bucket ?: null,
                'latency_ms' => 0,
                'tested_at'  => time(),
            );
            set_transient( $transient_key, $res, 60 );
            return $res;
        }

        $start = microtime( true );
        try {
            $client = self::get_client();
            // Lightweight probe: list max 1 object
            $client->list_objects( '', 1 );
            $latency = round( ( microtime( true ) - $start ) * 1000 );

            $res = array(
                'connected'  => true,
                'message'    => sprintf( __( 'Connected to "%s" (%d ms)', 'nbd-s3-offload' ), $bucket, $latency ),
                'bucket'     => $bucket,
                'latency_ms' => $latency,
                'tested_at'  => time(),
            );
            set_transient( $transient_key, $res, 120 ); // Cache for 2 minutes
            return $res;
        } catch ( \Throwable $e ) {
            $res = array(
                'connected'  => false,
                'message'    => $e->getMessage(),
                'bucket'     => $bucket,
                'latency_ms' => 0,
                'tested_at'  => time(),
            );
            set_transient( $transient_key, $res, 30 ); // Short cache for failures
            return $res;
        }
    }
}

