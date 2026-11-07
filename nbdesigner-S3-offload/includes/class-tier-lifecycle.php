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
            return false;
        }

        $base_dir  = defined( 'NBDESIGNER_CUSTOMER_DIR' ) ? NBDESIGNER_CUSTOMER_DIR : ( WP_CONTENT_DIR . '/uploads/nbdesigner/designs' );
        $local_dir = rtrim( $base_dir, '/' ) . '/' . $item_key;

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
        }

        return true;
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

        return array(
            'total'         => count( $folders ),
            'restored'      => $restored,
            'already_local' => $already_local,
        );
    }

    /**
     * Get design folders for admin sandbox explorer.
     */
    public static function get_design_folders_explorer( int $limit = 20, int $offset = 0, string $filter = 'all' ): array {
        global $wpdb;
        $base_dir = defined( 'NBDESIGNER_CUSTOMER_DIR' ) ? NBDESIGNER_CUSTOMER_DIR : ( WP_CONTENT_DIR . '/uploads/nbdesigner/designs' );
        if ( ! is_dir( $base_dir ) ) {
            return array( 'total' => 0, 'folders' => array() );
        }

        $dirs = glob( rtrim( $base_dir, '/' ) . '/*', GLOB_ONLYDIR );
        if ( empty( $dirs ) ) {
            return array( 'total' => 0, 'folders' => array() );
        }

        // Sort by filemtime descending
        usort( $dirs, function( $a, $b ) {
            return filemtime( $b ) <=> filemtime( $a );
        } );

        $settings  = NBD_S3_Settings::get_all();
        $retention = (int) ( $settings['cold_retention_days'] ?? 14 );
        $cutoff    = time() - ( $retention * DAY_IN_SECONDS );

        $all_items = array();

        foreach ( $dirs as $d ) {
            $folder  = basename( $d );
            $mtime   = filemtime( $d );
            $is_tpl  = self::is_admin_template( $folder );
            $is_aged = ( $mtime < $cutoff );

            // Check files inside folder
            $files     = self::get_recursive_files( $d );
            $count     = count( $files );
            $bytes     = 0;
            $has_heavy = false;
            foreach ( $files as $f ) {
                $sz = (int) filesize( $f );
                $bytes += $sz;
                $ext = strtolower( pathinfo( $f, PATHINFO_EXTENSION ) );
                if ( in_array( $ext, array( 'pdf', 'png', 'jpg', 'jpeg', 'tif', 'tiff' ), true ) ) {
                    $has_heavy = true;
                }
            }

            // Check if offloaded
            $is_offloaded = false;
            if ( ! $has_heavy && ( file_exists( $d . '/product.json' ) || file_exists( $d . '/design.json' ) ) && $count > 0 && $count <= 3 ) {
                $is_offloaded = true;
            } else {
                $offloaded_meta = $wpdb->get_var( $wpdb->prepare(
                    "SELECT m2.meta_value FROM {$wpdb->prefix}woocommerce_order_itemmeta m1
                     INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta m2 ON m1.order_item_id = m2.order_item_id
                     WHERE (m1.meta_key = '_nbd' OR m1.meta_key = '_nbd_item_key') AND m1.meta_value = %s
                     AND m2.meta_key = '_nbd_s3_offloaded' LIMIT 1",
                    $folder
                ) );
                if ( ! empty( $offloaded_meta ) ) {
                    $is_offloaded = true;
                }
            }

            // Filter check
            if ( 'templates' === $filter && ! $is_tpl ) continue;
            if ( 'aged' === $filter && ( ! $is_aged || $is_tpl ) ) continue;
            if ( 'offloaded' === $filter && ! $is_offloaded ) continue;
            if ( 'local' === $filter && $is_offloaded ) continue;

            $all_items[] = array(
                'folder'         => $folder,
                'is_template'    => $is_tpl,
                'is_aged'        => $is_aged,
                'is_offloaded'   => $is_offloaded,
                'files_count'    => $count,
                'size_bytes'     => $bytes,
                'size_formatted' => size_format( $bytes ),
                'modified_time'  => $mtime,
                'modified_date'  => wp_date( 'Y-m-d H:i', $mtime ),
                'age_days'       => (int) round( ( time() - $mtime ) / DAY_IN_SECONDS ),
            );
        }

        $total = count( $all_items );
        $slice = array_slice( $all_items, $offset, $limit );

        return array(
            'total'   => $total,
            'folders' => $slice,
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

        return array(
            'success'        => true,
            'message'        => sprintf( 'Tested offload on %d folders: %d succeeded.', count( $eligible ), $total_uploaded ),
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
        if ( empty( $settings['enable_cold_order_offload'] ) ) {
            return;
        }

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

        if ( empty( $orders ) ) {
            return;
        }

        foreach ( $orders as $order_id ) {
            $order = wc_get_order( $order_id );
            if ( ! $order ) {
                continue;
            }

            foreach ( $order->get_items() as $item_id => $item ) {
                $item_key = $item->get_meta( '_nbd' );
                if ( empty( $item_key ) ) {
                    $item_key = $item->get_meta( '_nbd_item_key' );
                }

                if ( ! empty( $item_key ) ) {
                    $already_offloaded = $item->get_meta( '_nbd_s3_offloaded' );
                    if ( empty( $already_offloaded ) ) {
                        // Queue or process single offload
                        self::process_single_offload( $item_key, $order_id );
                    }
                }
            }
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

        $client   = self::get_client();
        $files    = self::get_recursive_files( $local_dir );
        $uploaded = 0;
        $failed   = 0;

        foreach ( $files as $file_path ) {
            $rel_path = ltrim( substr( $file_path, strlen( $local_dir ) ), '/\\' );
            $s3_key   = 'designs/' . $item_key . '/' . str_replace( '\\', '/', $rel_path );

            $put_res = $client->put_object( $s3_key, $file_path, '', array(), true );
            if ( is_wp_error( $put_res ) ) {
                $failed++;
            } else {
                $uploaded++;
            }
        }

        if ( $failed > 0 || 0 === $uploaded ) {
            return array(
                'success'  => false,
                'message'  => sprintf( 'Failed to offload %d files to S3', $failed ),
                'uploaded' => $uploaded,
            );
        }

        // Verification step: verify files exist on S3 before deleting local copy
        $head_res = $client->head_object( 'designs/' . $item_key . '/' . ( file_exists( $local_dir . '/product.json' ) ? 'product.json' : 'design.json' ) );
        if ( is_wp_error( $head_res ) ) {
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
        }

        // Record order item metadata if order_id is provided
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

            $put_res = $client->put_object( $s3_key, $file_path, '', $headers, true );
            if ( is_wp_error( $put_res ) ) {
                $failed++;
            } else {
                $uploaded++;
                $bytes += (int) filesize( $file_path );
            }
        }

        return array(
            'success'  => 0 === $failed && $uploaded > 0,
            'message'  => sprintf( __( 'Font sync complete. Uploaded %1$d files (%2$s) with 30-day immutable cache.', 'nbd-s3-offload' ), $uploaded, size_format( $bytes ) ),
            'uploaded' => $uploaded,
            'failed'   => $failed,
            'bytes'    => $bytes,
        );
    }

    /**
     * Daily cleanup for abandoned guest draft folders older than threshold.
     */
    public static function run_draft_cleanup(): void {
        global $wpdb;
        $settings = NBD_S3_Settings::get_all();
        if ( empty( $settings['cleanup_abandoned_drafts'] ) ) {
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

            // Unclaimed orphan draft older than cutoff -> purge
            self::delete_directory( $dir );
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
            if ( '.' === $value || '..' === $value ) {
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
     * Register recurring Action Scheduler tasks.
     */
    public static function register_schedules(): void {
        if ( function_exists( 'as_schedule_recurring_action' ) ) {
            if ( ! as_has_scheduled_action( 'nbd_s3_cron_cold_sweep' ) ) {
                as_schedule_recurring_action( time() + 300, DAY_IN_SECONDS, 'nbd_s3_cron_cold_sweep' );
            }
            if ( ! as_has_scheduled_action( 'nbd_s3_cron_cleanup_drafts' ) ) {
                as_schedule_recurring_action( time() + 600, DAY_IN_SECONDS, 'nbd_s3_cron_cleanup_drafts' );
            }
        }
    }
}

