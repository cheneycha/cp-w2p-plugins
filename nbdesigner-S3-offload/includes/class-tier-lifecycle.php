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
        self::maybe_predefine_cdn_urls();

        // Output buffer to intercept editor HTML and rewrite font URLs to CDN
        add_action( 'template_redirect', array( __CLASS__, 'start_font_url_buffer' ), 1 );

        // Modern studio editor script injection hook (fires right before app-modern.min.js)
        add_action( 'nbd_extra_js', array( __CLASS__, 'inject_editor_font_override' ), 1 );

        // Script and style loader src filters for enqueued fonts
        add_filter( 'style_loader_src', array( __CLASS__, 'filter_font_loader_src' ), 999 );
        add_filter( 'script_loader_src', array( __CLASS__, 'filter_font_loader_src' ), 999 );

        // On-demand hydration interception before design downloads & order views
        add_action( 'init', array( __CLASS__, 'intercept_download_hydration' ), 1 );
        add_action( 'init', array( __CLASS__, 'intercept_template_hydration' ), 1 );
        add_action( 'admin_init', array( __CLASS__, 'intercept_admin_order_view_hydration' ), 1 );

        // Action Scheduler hooks for background tasks
        add_action( 'nbd_s3_cron_cold_sweep', array( __CLASS__, 'run_cold_sweep' ) );
        add_action( 'nbd_s3_chain_cold_sweep', array( __CLASS__, 'run_cold_sweep' ), 10, 1 );
        add_action( 'nbd_s3_async_offload_item', array( __CLASS__, 'process_single_offload' ), 10, 2 );
        add_action( 'nbd_s3_cron_cleanup_drafts', array( __CLASS__, 'run_draft_cleanup' ) );
        add_action( 'nbd_s3_chain_cleanup_drafts', array( __CLASS__, 'run_draft_cleanup' ), 10, 2 );
        add_action( 'nbd_s3_cron_saved_designs_lifecycle', array( __CLASS__, 'run_saved_designs_lifecycle' ) );

        // Customer Saved Designs Frontend UI hook (Loco Translate compatible)
        add_action( 'nb_before_show_design', array( __CLASS__, 'render_saved_designs_frontend_notices' ) );

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
     * Resolve the public CDN / Spaces base URL from settings with intelligent fallbacks.
     */
    public static function get_public_cdn_base_url(): string {
        $settings = NBD_S3_Settings::get_all();
        $cdn_url  = trim( (string) ( $settings['cdn_url'] ?? '' ) );

        if ( ! empty( $cdn_url ) ) {
            if ( ! preg_match( '#^https?://#i', $cdn_url ) ) {
                $cdn_url = 'https://' . $cdn_url;
            }
            $cdn_url = rtrim( $cdn_url, '/' );
            // Strip trailing /fonts if user accidentally included it in their CDN root setting
            if ( str_ends_with( strtolower( $cdn_url ), '/fonts' ) ) {
                $cdn_url = substr( $cdn_url, 0, -6 );
            }
            return rtrim( $cdn_url, '/' );
        }

        // Fallback to S3 endpoint if bucket and endpoint are configured
        $bucket   = trim( (string) ( $settings['bucket'] ?? '' ) );
        $endpoint = trim( (string) ( $settings['endpoint'] ?? '' ) );

        if ( ! empty( $bucket ) && ! empty( $endpoint ) ) {
            $endpoint = preg_replace( '#^https?://#i', '', rtrim( $endpoint, '/' ) );
            if ( ! empty( $settings['use_path_style'] ) ) {
                return 'https://' . $endpoint . '/' . $bucket;
            } else {
                if ( preg_match( '#^' . preg_quote( $bucket, '#' ) . '\.#i', $endpoint ) ) {
                    return 'https://' . $endpoint;
                }
                return 'https://' . $bucket . '.' . $endpoint;
            }
        }

        return '';
    }

    /**
     * Pre-define CDN URLs before NBDesigner defines local defaults.
     */
    public static function maybe_predefine_cdn_urls(): void {
        $settings = NBD_S3_Settings::get_all();
        if ( empty( $settings['enable_font_offload'] ) ) {
            return;
        }

        $cdn_base = self::get_public_cdn_base_url();
        if ( empty( $cdn_base ) ) {
            return;
        }

        // If font offload is enabled, point NBDESIGNER_FONT_URL to CDN /fonts
        if ( ! defined( 'NBDESIGNER_FONT_URL' ) ) {
            define( 'NBDESIGNER_FONT_URL', $cdn_base . '/fonts' );
        }
    }

    /**
     * Start output buffering on template_redirect to rewrite font URLs in editor HTML.
     */
    public static function start_font_url_buffer(): void {
        $settings = NBD_S3_Settings::get_all();
        if ( empty( $settings['enable_font_offload'] ) ) {
            return;
        }

        $cdn_base = self::get_public_cdn_base_url();
        if ( empty( $cdn_base ) ) {
            return;
        }

        if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
            return;
        }

        ob_start( array( __CLASS__, 'rewrite_font_urls_in_html' ) );
    }

    /**
     * Output buffer callback to rewrite local NBDesigner font URLs to CDN URLs.
     */
    public static function rewrite_font_urls_in_html( string $buffer ): string {
        if ( false === strpos( $buffer, '/nbdesigner/fonts' ) ) {
            return $buffer;
        }

        $settings = NBD_S3_Settings::get_all();
        if ( empty( $settings['enable_font_offload'] ) ) {
            return $buffer;
        }

        $cdn_base = self::get_public_cdn_base_url();
        if ( empty( $cdn_base ) ) {
            return $buffer;
        }

        $cdn_fonts_url = $cdn_base . '/fonts';

        $upload_dir     = wp_upload_dir();
        $local_font_url = untrailingslashit( $upload_dir['baseurl'] ) . '/nbdesigner/fonts';

        $targets = array(
            $local_font_url,
            str_replace( 'https://', 'http://', $local_font_url ),
            str_replace( 'http://', 'https://', $local_font_url ),
            preg_replace( '#^https?:#i', '', $local_font_url ),
        );

        $site_uploads = untrailingslashit( site_url() ) . '/wp-content/uploads/nbdesigner/fonts';
        $targets[]    = $site_uploads;
        $targets[]    = preg_replace( '#^https?:#i', '', $site_uploads );

        $targets = array_unique( array_filter( $targets ) );

        foreach ( $targets as $target ) {
            // Standard slash format
            $buffer = str_replace( $target, $cdn_fonts_url, $buffer );

            // Escaped slash format (for localized JSON data in scripts)
            $target_json = str_replace( '/', '\/', $target );
            $cdn_json    = str_replace( '/', '\/', $cdn_fonts_url );
            $buffer      = str_replace( $target_json, $cdn_json, $buffer );
        }

        return $buffer;
    }

    /**
     * Inject runtime JS config override into modern studio editor right before app-modern.min.js.
     */
    public static function inject_editor_font_override(): void {
        $settings = NBD_S3_Settings::get_all();
        if ( empty( $settings['enable_font_offload'] ) ) {
            return;
        }

        $cdn_base = self::get_public_cdn_base_url();
        if ( empty( $cdn_base ) ) {
            return;
        }

        $cdn_font_url = $cdn_base . '/fonts';
        ?>
        <script type="text/javascript" id="nbd-s3-font-override">
            /* NBDesigner S3 Offload: Enforce CDN Font URL for Studio Canvas */
            (function() {
                var cdnFontUrl = <?php echo json_encode( $cdn_font_url ); ?>;
                if (typeof NBDESIGNCONFIG !== 'undefined') {
                    NBDESIGNCONFIG.font_url = cdnFontUrl;
                    if (Array.isArray(NBDESIGNCONFIG.fonts)) {
                        NBDESIGNCONFIG.fonts.forEach(function(item) {
                            if (item && item.file && typeof item.file === 'object') {
                                Object.keys(item.file).forEach(function(k) {
                                    if (typeof item.file[k] === 'string' && item.file[k].indexOf('/nbdesigner/fonts/') > -1) {
                                        item.file[k] = item.file[k].replace(/^https?:\/\/[^\/]+\/wp-content\/uploads\/nbdesigner\/fonts/i, cdnFontUrl);
                                    }
                                });
                            }
                            if (item && typeof item.url === 'string' && item.url.indexOf('/nbdesigner/fonts/') > -1) {
                                item.url = item.url.replace(/^https?:\/\/[^\/]+\/wp-content\/uploads\/nbdesigner\/fonts/i, cdnFontUrl);
                            }
                        });
                    }
                }
            })();
        </script>
        <?php
    }

    /**
     * Filter WordPress style & script loader URLs to ensure enqueued font stylesheets resolve via CDN.
     */
    public static function filter_font_loader_src( string $src ): string {
        if ( false !== strpos( $src, '/nbdesigner/fonts/' ) ) {
            $settings = NBD_S3_Settings::get_all();
            if ( ! empty( $settings['enable_font_offload'] ) ) {
                $cdn_base = self::get_public_cdn_base_url();
                if ( ! empty( $cdn_base ) ) {
                    $cdn_font_url = $cdn_base . '/fonts';
                    $src          = preg_replace( '#^https?://[^/]+/wp-content/uploads/nbdesigner/fonts#i', $cdn_font_url, $src );
                }
            }
        }
        return $src;
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

        $downloaded_keys = array();
        $download_failed = 0;

        foreach ( $objects as $obj ) {
            $s3_key   = $obj['key'];
            $rel_path = substr( $s3_key, strlen( $prefix ) );
            if ( empty( $rel_path ) ) {
                continue;
            }

            $target_file = $local_dir . '/' . $rel_path;
            $res = $client->get_object( $s3_key, $target_file );
            if ( is_wp_error( $res ) || ! file_exists( $target_file ) ) {
                $download_failed++;
                continue;
            }

            $downloaded_keys[] = $s3_key;

            if ( ! empty( $obj['last_modified'] ) ) {
                $file_ts = strtotime( (string) $obj['last_modified'] );
                if ( $file_ts > 0 ) {
                    @touch( $target_file, $file_ts );
                }
            }
        }

        if ( $download_failed > 0 || empty( $downloaded_keys ) ) {
            NBD_S3_Logger::log( 'RESTORE_WARN', sprintf( 'Incomplete restoration for %s: %d files failed to download. S3 remote files preserved intact.', $item_key, $download_failed ), array( 'folder' => $item_key, 'failed' => $download_failed ), 'warning' );
            return false;
        }

        // Safe S3 Remote Cleanup: Delete from S3 only if all files verified locally and toggle is active
        $settings = NBD_S3_Settings::get_all();
        $deleted_s3_count = 0;
        if ( ! empty( $settings['delete_remote_on_restore'] ) ) {
            foreach ( $downloaded_keys as $s3_key ) {
                $del_res = $client->delete_object( $s3_key );
                if ( ! is_wp_error( $del_res ) ) {
                    $deleted_s3_count++;
                }
            }
            NBD_S3_Logger::log( 'RESTORE_S3_PURGE', sprintf( 'Cleaned up %d remote S3 files for %s after successful local restoration.', $deleted_s3_count, $item_key ), array( 'folder' => $item_key, 'deleted_s3_files' => $deleted_s3_count ), 'info' );
        }

        delete_transient( 'nbd_s3_tier2_active_count' );
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

        delete_transient( 'nbd_s3_tier2_active_count' );
        NBD_S3_Logger::log( 'WHITELIST_RESTORE', sprintf( 'Restored %d whitelisted admin templates to local SSD (%d already local).', $restored, $already_local ), array( 'restored' => $restored, 'already_local' => $already_local ), 'info' );
        return array(
            'total'         => count( $folders ),
            'restored'      => $restored,
            'already_local' => $already_local,
        );
    }

    /**
     * Safely flip a flat array of values into keys for O(1) hash lookups.
     * Sanitizes inputs to ensure only valid non-empty scalar strings/integers are indexed,
     * completely eliminating PHP Warnings: "array_flip(): Can only flip string and integer values".
     *
     * @param array|null $items Array of raw values from database column.
     * @return array<string, true>
     */
    private static function safe_array_flip( ?array $items ): array {
        if ( empty( $items ) ) {
            return array();
        }

        $clean = array();
        foreach ( $items as $item ) {
            if ( is_scalar( $item ) ) {
                $str = trim( (string) $item );
                if ( '' !== $str ) {
                    $clean[ $str ] = true;
                }
            }
        }

        return $clean;
    }

    /**
     * Get count of Tier 2 active local design orders (< retention threshold).
     */
    public static function get_tier2_active_count( bool $force_refresh = false ): int {
        if ( ! $force_refresh ) {
            $cached_count = get_transient( 'nbd_s3_tier2_active_count' );
            if ( false !== $cached_count ) {
                return (int) $cached_count;
            }
        }

        $res   = self::get_design_folders_explorer( 0, 0, 'active', $force_refresh, '' );
        $count = (int) ( $res['total'] ?? 0 );

        set_transient( 'nbd_s3_tier2_active_count', $count, 300 );
        return $count;
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
                $template_lookup = self::safe_array_flip( $tpl_col );
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
                   AND m1.meta_value IS NOT NULL AND m1.meta_value != ''
                   AND m2.meta_key = '_nbd_s3_offloaded' 
                   AND m2.meta_value = '1'"
            );
            if ( ! empty( $off_col ) ) {
                $offloaded_lookup = self::safe_array_flip( $off_col );
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
        $all_cart_folders  = array();

        $needs_order_lookup = in_array( $filter, array( 'drafts', 'active', 'aged', 'orders' ), true );
        $needs_saved_lookup = in_array( $filter, array( 'drafts', 'saved' ), true );
        $needs_cart_lookup  = in_array( $filter, array( 'cart', 'drafts' ), true );

        if ( $needs_order_lookup ) {
            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$order_meta_table}'" ) === $order_meta_table ) {
                $order_cols = $wpdb->get_col( "SELECT DISTINCT meta_value FROM {$order_meta_table} WHERE (meta_key = '_nbd' OR meta_key = '_nbd_item_key') AND meta_value IS NOT NULL AND meta_value != ''" );
                if ( ! empty( $order_cols ) ) {
                    $all_order_folders = self::safe_array_flip( $order_cols );
                }
            }
        }

        if ( $needs_saved_lookup ) {
            $mydesigns_table = $wpdb->prefix . 'nbdesigner_mydesigns';
            if ( $wpdb->get_var( "SHOW TABLES LIKE '{$mydesigns_table}'" ) === $mydesigns_table ) {
                $saved_cols = $wpdb->get_col( "SELECT DISTINCT folder FROM {$mydesigns_table} WHERE folder IS NOT NULL AND folder != ''" );
                if ( ! empty( $saved_cols ) ) {
                    $all_saved_folders = self::safe_array_flip( $saved_cols );
                }
            }
        }

        if ( $needs_cart_lookup ) {
            $all_cart_folders = self::get_all_active_cart_folders();
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
            if ( 'active' === $filter && ( ! isset( $all_order_folders[ $folder ] ) || $is_offloaded || $is_tpl || $is_aged ) ) {
                continue;
            }
            if ( 'aged' === $filter && ( ! isset( $all_order_folders[ $folder ] ) || ! $is_aged || $is_tpl || $is_offloaded ) ) {
                continue;
            }
            if ( 'offloaded' === $filter && ! $is_offloaded ) {
                continue;
            }
            if ( 'local' === $filter && $is_offloaded ) {
                continue;
            }
            if ( 'orders' === $filter && ! isset( $all_order_folders[ $folder ] ) ) {
                continue;
            }
            if ( 'saved' === $filter && ! isset( $all_saved_folders[ $folder ] ) ) {
                continue;
            }
            if ( 'cart' === $filter && ! isset( $all_cart_folders[ $folder ] ) ) {
                continue;
            }
            if ( 'drafts' === $filter ) {
                if ( ! empty( $settings['protect_active_carts'] ) && isset( $all_cart_folders[ $folder ] ) ) {
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

            // Accurate order, saved, cart, draft determination
            $has_order = isset( $all_order_folders[ $folder ] );
            if ( ! $has_order && ! $needs_order_lookup ) {
                if ( $wpdb->get_var( "SHOW TABLES LIKE '{$order_meta_table}'" ) === $order_meta_table ) {
                    $has_order = (bool) $wpdb->get_var( $wpdb->prepare( "SELECT order_item_id FROM {$order_meta_table} WHERE (meta_key = '_nbd' OR meta_key = '_nbd_item_key') AND meta_value = %s LIMIT 1", $folder ) );
                }
            }

            $is_saved = isset( $all_saved_folders[ $folder ] );
            if ( ! $is_saved && ! $needs_saved_lookup ) {
                $mydesigns_table = $wpdb->prefix . 'nbdesigner_mydesigns';
                if ( $wpdb->get_var( "SHOW TABLES LIKE '{$mydesigns_table}'" ) === $mydesigns_table ) {
                    $is_saved = (bool) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$mydesigns_table} WHERE folder = %s LIMIT 1", $folder ) );
                }
            }

            $is_in_cart = isset( $all_cart_folders[ $folder ] );
            if ( ! $is_in_cart && ! $needs_cart_lookup ) {
                $is_in_cart = self::is_in_active_cart( $folder );
            }

            $is_draft = ( ! $item['is_template'] && ! $has_order && ! $is_saved );

            $folders[] = array(
                'folder'         => $folder,
                'is_template'    => $item['is_template'],
                'is_aged'        => $item['is_aged'],
                'is_offloaded'   => $is_offloaded,
                'has_order'      => $has_order,
                'is_saved'       => $is_saved,
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
     * Check if an Action Scheduler action is currently queued or running, regardless of argument payload.
     *
     * @param string          $hook     Action hook name.
     * @param string|string[] $statuses Statuses to match (defaults to pending and in-progress).
     */
    public static function has_action_in_queue( string $hook, $statuses = array( 'pending', 'in-progress' ) ): bool {
        if ( ! function_exists( 'as_get_scheduled_actions' ) ) {
            return false;
        }
        $actions = as_get_scheduled_actions(
            array(
                'hook'     => $hook,
                'status'   => (array) $statuses,
                'per_page' => 1,
            ),
            'ids'
        );
        return ! empty( $actions );
    }

    /**
     * Get eligible aged non-template folders for batch testing or sweep queuing.
     * Queries un-offloaded order items directly from database with disk age verification.
     */
    public static function get_eligible_batch_folders( int $count = 50 ): array {
        @set_time_limit( 300 );
        @ini_set( 'max_execution_time', '300' );

        global $wpdb;

        $settings  = NBD_S3_Settings::get_all();
        $retention = (int) ( $settings['cold_retention_days'] ?? 14 );
        $cutoff    = time() - ( $retention * DAY_IN_SECONDS );

        $base_dir = defined( 'NBDESIGNER_CUSTOMER_DIR' ) ? NBDESIGNER_CUSTOMER_DIR : ( WP_CONTENT_DIR . '/uploads/nbdesigner/designs' );
        if ( ! is_dir( $base_dir ) ) {
            return array();
        }

        // Whitelisted admin templates
        $template_table  = $wpdb->prefix . 'nbdesigner_templates';
        $template_lookup = array();
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$template_table}'" ) === $template_table ) {
            $tpl_col = $wpdb->get_col( "SELECT DISTINCT folder FROM {$template_table} WHERE folder IS NOT NULL AND folder != ''" );
            if ( ! empty( $tpl_col ) ) {
                $template_lookup = self::safe_array_flip( $tpl_col );
            }
        }

        $registry_lookup = self::get_offloaded_registry();
        $failed_map      = get_transient( 'nbd_s3_failed_folders' ) ?: array();

        $order_meta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$order_meta_table}'" ) !== $order_meta_table ) {
            return array();
        }

        // Direct database candidate discovery:
        // Query distinct order folders that are not yet marked as offloaded in WooCommerce order itemmeta.
        // Group by folder and order by oldest order item first to process chronologically.
        $candidates = $wpdb->get_col(
            "SELECT m1.meta_value 
             FROM {$order_meta_table} m1 
             WHERE (m1.meta_key = '_nbd' OR m1.meta_key = '_nbd_item_key') 
               AND m1.meta_value IS NOT NULL AND m1.meta_value != ''
               AND NOT EXISTS (
                   SELECT 1 FROM {$order_meta_table} m2 
                   WHERE m2.order_item_id = m1.order_item_id 
                     AND m2.meta_key = '_nbd_s3_offloaded' 
                     AND m2.meta_value = '1'
               )
             GROUP BY m1.meta_value
             ORDER BY MIN(m1.order_item_id) ASC
             LIMIT 1000"
        );

        if ( empty( $candidates ) ) {
            return array();
        }

        $eligible = array();
        foreach ( $candidates as $folder ) {
            $folder = sanitize_file_name( $folder );
            if ( empty( $folder ) ) {
                continue;
            }

            // Shield 1: Skip whitelisted admin templates
            if ( isset( $template_lookup[ $folder ] ) || self::is_admin_template( $folder ) ) {
                continue;
            }

            // Skip if already in persistent S3 offload registry
            if ( isset( $registry_lookup[ $folder ] ) ) {
                continue;
            }

            // Skip if failed 3+ times
            if ( ! empty( $failed_map[ $folder ] ) && $failed_map[ $folder ] >= 3 ) {
                continue;
            }

            $local_dir = rtrim( $base_dir, '/' ) . '/' . $folder;

            // If folder doesn't exist locally, record offloaded to avoid re-querying dead items
            if ( ! is_dir( $local_dir ) ) {
                self::record_folder_offloaded( $folder );
                $item_ids = $wpdb->get_col( $wpdb->prepare(
                    "SELECT DISTINCT order_item_id FROM {$order_meta_table} WHERE (meta_key = '_nbd' OR meta_key = '_nbd_item_key') AND meta_value = %s",
                    $folder
                ) );
                if ( ! empty( $item_ids ) ) {
                    foreach ( $item_ids as $id ) {
                        wc_update_order_item_meta( $id, '_nbd_s3_offloaded', 1 );
                    }
                }
                continue;
            }

            // If already marked as offloaded locally via marker file
            if ( file_exists( $local_dir . '/.s3_offloaded' ) ) {
                self::record_folder_offloaded( $folder );
                $item_ids = $wpdb->get_col( $wpdb->prepare(
                    "SELECT DISTINCT order_item_id FROM {$order_meta_table} WHERE (meta_key = '_nbd' OR meta_key = '_nbd_item_key') AND meta_value = %s",
                    $folder
                ) );
                if ( ! empty( $item_ids ) ) {
                    foreach ( $item_ids as $id ) {
                        wc_update_order_item_meta( $id, '_nbd_s3_offloaded', 1 );
                    }
                }
                continue;
            }

            // Shield 2: Enforce age cutoff (> 14 days old). Recent active orders (< 14d) are protected.
            $mtime = (int) @filemtime( $local_dir );
            if ( $mtime >= $cutoff ) {
                continue;
            }

            $eligible[] = $folder;
            if ( count( $eligible ) >= $count ) {
                break;
            }
        }

        return $eligible;
    }

    /**
     * Batch test offload on a specific number of aged folders.
     */
    public static function test_batch_offload( int $count = 3 ): array {
        @set_time_limit( 300 );
        @ini_set( 'max_execution_time', '300' );

        $eligible = self::get_eligible_batch_folders( $count );

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
            $res = self::process_single_offload( $folder, 0, true );
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
     *
     * @param bool $force Whether this sweep was manually forced (bypasses cron toggle check).
     */
    public static function run_cold_sweep( bool $force = false ): int {
        @set_time_limit( 300 );
        @ini_set( 'max_execution_time', '300' );

        $settings = NBD_S3_Settings::get_all();

        // Safety Kill-Switch: Skip if syncing is paused by admin
        if ( ! empty( $settings['sync_paused'] ) ) {
            NBD_S3_Logger::log( 'SWEEP', __( 'Automated Cold Sweep skipped because sync is paused.', 'nbd-s3-offload' ), array(), 'warning' );
            return 0;
        }

        if ( ! $force && empty( $settings['enable_cold_order_offload'] ) ) {
            return 0;
        }

        $use_async = function_exists( 'as_enqueue_async_action' );

        // If using Action Scheduler, check if previous batch of offload items is still processing
        if ( $use_async && self::has_action_in_queue( 'nbd_s3_async_offload_item', array( 'pending', 'in-progress' ) ) ) {
            // Queue still has pending items. Re-check in 15 seconds to continue auto-chaining.
            if ( ! self::has_action_in_queue( 'nbd_s3_chain_cold_sweep', array( 'pending' ) ) ) {
                as_schedule_single_action( time() + 15, 'nbd_s3_chain_cold_sweep', array( $force ) );
                if ( function_exists( 'spawn_cron' ) ) {
                    spawn_cron();
                }
            }
            return 0;
        }

        // Set running state indicator
        set_transient( 'nbd_s3_sync_status', array(
            'running'    => true,
            'action'     => 'Cold Sweep',
            'started_at' => time(),
        ), 1800 );

        $processed = 0;
        try {
            // 1. Direct candidate discovery from aged candidate pool (matches Explorer Tier 3 Candidate filter)
            $batch_limit = 50;
            $candidates  = self::get_eligible_batch_folders( $batch_limit );

            if ( ! empty( $candidates ) ) {
                foreach ( $candidates as $item_key ) {
                    if ( $use_async ) {
                        as_enqueue_async_action( 'nbd_s3_async_offload_item', array( $item_key, 0 ) );
                        $processed++;
                    } else {
                        @set_time_limit( 120 );
                        $res = self::process_single_offload( $item_key, 0, false );
                        if ( ! empty( $res['success'] ) ) {
                            $processed++;
                        }
                    }
                }

                // AUTO-CHAINING: Enqueue follow-up action to automatically process the next batch
                if ( $use_async && function_exists( 'as_schedule_single_action' ) ) {
                    if ( ! self::has_action_in_queue( 'nbd_s3_chain_cold_sweep', array( 'pending' ) ) ) {
                        as_schedule_single_action( time() + 15, 'nbd_s3_chain_cold_sweep', array( $force ) );
                        if ( function_exists( 'spawn_cron' ) ) {
                            spawn_cron();
                        }
                    }
                }
            } else {
                // No candidate folders remaining: sweep is 100% complete!
                NBD_S3_Logger::log( 'SWEEP', __( 'Cold storage sweep complete! All aged design folders have been successfully offloaded to S3.', 'nbd-s3-offload' ), array(), 'success' );
            }

            if ( ! empty( $use_async ) && $processed > 0 ) {
                NBD_S3_Logger::log( 'SWEEP', sprintf( 'Cold Sweep auto-chain: Queued batch of %d design items to Action Scheduler.', $processed ), array( 'queued' => $processed ), 'info' );
            } elseif ( $processed > 0 ) {
                NBD_S3_Logger::log( 'SWEEP', sprintf( 'Cold Sweep completed. Offloaded %d design items.', $processed ), array( 'processed' => $processed ), 'success' );
            }
        } finally {
            delete_transient( 'nbd_s3_sync_status' );
        }

        return $processed;
    }

    /**
     * Process offload for a single order item key: upload to S3, verify, and purge local files.
     *
     * @param string $item_key  Design folder key / item ID.
     * @param int    $order_id  Associated WooCommerce order ID.
     * @param bool   $is_manual Whether this offload was explicitly requested by an admin in the UI.
     */
    public static function process_single_offload( string $item_key, int $order_id = 0, bool $is_manual = false ): array {
        @set_time_limit( 120 );
        @ini_set( 'max_execution_time', '120' );

        // Safety Kill-Switch: Skip automated background tasks if syncing is paused by admin.
        // Manual tests initiated in the Safe Trial Zone or Explorer by an admin are permitted.
        $settings = NBD_S3_Settings::get_all();
        if ( ! $is_manual && ! empty( $settings['sync_paused'] ) ) {
            return array( 'success' => false, 'message' => 'Skipped: Sync is paused by admin (Safe Mode active)' );
        }

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
            // Self-heal: If folder does not exist locally, mark offloaded in registry and order meta
            self::record_folder_offloaded( $item_key );
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
                    }
                }
            }
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
            $failed_map = get_transient( 'nbd_s3_failed_folders' ) ?: array();
            $failed_map[ $item_key ] = ( $failed_map[ $item_key ] ?? 0 ) + 1;
            set_transient( 'nbd_s3_failed_folders', $failed_map, DAY_IN_SECONDS );

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

        delete_transient( 'nbd_s3_tier2_active_count' );

        $failed_map = get_transient( 'nbd_s3_failed_folders' );
        if ( ! empty( $failed_map[ $item_key ] ) ) {
            unset( $failed_map[ $item_key ] );
            set_transient( 'nbd_s3_failed_folders', $failed_map, DAY_IN_SECONDS );
        }

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
     * Get all design folder keys currently residing in active guest sessions or registered user carts.
     *
     * @return array<string, bool> Map of folder => true
     */
    public static function get_all_active_cart_folders(): array {
        global $wpdb;
        $cart_folders = array();

        // 1. Usermeta persistent carts
        $user_carts = $wpdb->get_col( "SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key LIKE '_woocommerce_persistent_cart_%' AND meta_value != ''" );
        if ( ! empty( $user_carts ) ) {
            foreach ( $user_carts as $cart_raw ) {
                if ( preg_match_all( '/"(?:nbd_item_key|nbd|folder)";s:\d+:"([a-zA-Z0-9_\-]+)"/', $cart_raw, $matches ) ) {
                    foreach ( $matches[1] as $k ) {
                        $cart_folders[ $k ] = true;
                    }
                }
            }
        }

        // 2. Active guest sessions
        $table_sessions = $wpdb->prefix . 'woocommerce_sessions';
        if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $table_sessions ) ) === $table_sessions ) {
            $sessions = $wpdb->get_col( $wpdb->prepare(
                "SELECT session_value FROM {$table_sessions} WHERE session_expiry > %d",
                time()
            ) );
            if ( ! empty( $sessions ) ) {
                foreach ( $sessions as $sess_raw ) {
                    if ( preg_match_all( '/"(?:nbd_item_key|nbd|folder)";s:\d+:"([a-zA-Z0-9_\-]+)"/', $sess_raw, $matches ) ) {
                        foreach ( $matches[1] as $k ) {
                            $cart_folders[ $k ] = true;
                        }
                    }
                }
            }
        }

        return $cart_folders;
    }

    /**
     * Daily cleanup for abandoned guest draft folders older than threshold.
     * Batched with Action Scheduler auto-chaining to safely process large archives (e.g. 50,000+ folders).
     *
     * @param int $batch_size Maximum folders to delete in this execution run. Defaults to 500.
     * @return int Number of folders deleted in this run.
     */
    /**
     * Run batched cleanup of unclaimed abandoned guest drafts (> 7 days, no order, no user account, not in active cart).
     * Processes in safe batches (default 500) and automatically self-chains via Action Scheduler.
     *
     * @param int  $batch_size Number of draft folders to purge in this chunk.
     * @param bool $force      Whether this cleanup was manually forced (bypasses cron toggle check).
     */
    public static function run_draft_cleanup( int $batch_size = 500, bool $force = false ): int {
        global $wpdb;
        $settings = NBD_S3_Settings::get_all();

        // Safety Kill-Switch: Skip if syncing is paused by admin or cleanup disabled
        if ( ! empty( $settings['sync_paused'] ) ) {
            NBD_S3_Logger::log( 'DRAFT_CLEANUP', __( 'Draft cleanup skipped because sync is paused (Safe Mode active).', 'nbd-s3-offload' ), array(), 'warning' );
            return 0;
        }

        if ( ! $force && empty( $settings['cleanup_abandoned_drafts'] ) ) {
            return 0;
        }

        @set_time_limit( 300 );
        @ini_set( 'max_execution_time', '300' );

        $days      = (int) ( $settings['abandoned_drafts_days'] ?? 7 );
        $cutoff_ts = time() - ( $days * DAY_IN_SECONDS );
        $base_dir  = defined( 'NBDESIGNER_CUSTOMER_DIR' ) ? NBDESIGNER_CUSTOMER_DIR : ( WP_CONTENT_DIR . '/uploads/nbdesigner/designs' );

        if ( ! is_dir( $base_dir ) ) {
            return 0;
        }

        // Set running state indicator
        set_transient( 'nbd_s3_sync_status', array(
            'running'    => true,
            'action'     => 'Draft Cleanup',
            'started_at' => time(),
        ), 1800 );

        // 1. Bulk Lookups: Pre-load O(1) hash maps for all protected assets
        // A. Whitelisted admin templates
        $template_table  = $wpdb->prefix . 'nbdesigner_templates';
        $template_lookup = array();
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$template_table}'" ) === $template_table ) {
            $tpl_col = $wpdb->get_col( "SELECT DISTINCT folder FROM {$template_table} WHERE folder IS NOT NULL AND folder != ''" );
            if ( ! empty( $tpl_col ) ) {
                $template_lookup = self::safe_array_flip( $tpl_col );
            }
        }

        // B. Active WooCommerce order item designs
        $order_meta_table  = $wpdb->prefix . 'woocommerce_order_itemmeta';
        $all_order_folders = array();
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$order_meta_table}'" ) === $order_meta_table ) {
            $order_cols = $wpdb->get_col( "SELECT DISTINCT meta_value FROM {$order_meta_table} WHERE (meta_key = '_nbd' OR meta_key = '_nbd_item_key') AND meta_value IS NOT NULL AND meta_value != ''" );
            if ( ! empty( $order_cols ) ) {
                $all_order_folders = self::safe_array_flip( $order_cols );
            }
        }

        // C. Customer saved designs
        $mydesigns_table   = $wpdb->prefix . 'nbdesigner_mydesigns';
        $all_saved_folders = array();
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$mydesigns_table}'" ) === $mydesigns_table ) {
            $saved_cols = $wpdb->get_col( "SELECT DISTINCT folder FROM {$mydesigns_table} WHERE folder IS NOT NULL AND folder != ''" );
            if ( ! empty( $saved_cols ) ) {
                $all_saved_folders = self::safe_array_flip( $saved_cols );
            }
        }

        // D. Active Cart Protection Shield: Guest sessions & registered persistent carts
        $all_cart_folders = array();
        if ( ! empty( $settings['protect_active_carts'] ) ) {
            $all_cart_folders = self::get_all_active_cart_folders();
        }

        // E. Failed delete skip list to prevent infinite loop on permission-locked directories
        $failed_deletes = get_transient( 'nbd_s3_failed_draft_deletes' );
        if ( ! is_array( $failed_deletes ) ) {
            $failed_deletes = array();
        }

        // F. Directory inventory: leverage explorer cache if present, or scan base_dir
        $cache_key   = 'nbd_s3_explorer_dirs_v2';
        $cached_dirs = get_transient( $cache_key );

        if ( false === $cached_dirs || ! is_array( $cached_dirs ) ) {
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

            usort( $cached_dirs, function( $a, $b ) {
                return $b['mtime'] <=> $a['mtime'];
            } );

            set_transient( $cache_key, $cached_dirs, 300 );
        }

        $deleted_count = 0;
        $failed_count  = 0;

        try {
            foreach ( $cached_dirs as $d ) {
                $folder = $d['folder'];
                $mtime  = $d['mtime'];

                // Skip known undeletable directories
                if ( isset( $failed_deletes[ $folder ] ) ) {
                    continue;
                }

                // Check file modification time against grace period
                if ( $mtime >= $cutoff_ts ) {
                    continue; // Still active or created within retention window
                }

                // Shield 1: Whitelisted admin templates
                if ( isset( $template_lookup[ $folder ] ) || self::is_admin_template( $folder ) ) {
                    continue;
                }

                // Shield 2: Customer saved designs
                if ( isset( $all_saved_folders[ $folder ] ) ) {
                    continue;
                }

                // Shield 3: Attached to any WooCommerce order item
                if ( isset( $all_order_folders[ $folder ] ) ) {
                    continue;
                }

                // Shield 4: Held in any live guest or customer cart
                if ( ! empty( $settings['protect_active_carts'] ) && isset( $all_cart_folders[ $folder ] ) ) {
                    continue;
                }

                $dir_path = rtrim( $base_dir, '/' ) . '/' . $folder;
                if ( ! is_dir( $dir_path ) ) {
                    continue;
                }

                // Unclaimed orphan draft older than cutoff -> purge
                if ( self::delete_directory( $dir_path ) ) {
                    $deleted_count++;
                } else {
                    $failed_count++;
                    $failed_deletes[ $folder ] = true;
                }

                if ( $deleted_count >= $batch_size ) {
                    break;
                }
            }
        } catch ( \Throwable $e ) {
            NBD_S3_Logger::log( 'DRAFT_CLEANUP', sprintf( 'Error during draft cleanup: %s', $e->getMessage() ), array( 'exception' => $e->getMessage() ), 'error' );
        } finally {
            if ( ! empty( $failed_deletes ) ) {
                set_transient( 'nbd_s3_failed_draft_deletes', $failed_deletes, 3600 );
            }
            delete_transient( 'nbd_s3_sync_status' );
        }

        // Invalidate directory cache only when sweep finishes or when all drafts cleared,
        // avoiding expensive 75k-item disk rescans between chained batches
        if ( $deleted_count < $batch_size ) {
            delete_transient( 'nbd_s3_explorer_dirs_v2' );
        }

        // AUTO-CHAINING: If we filled a full batch, schedule next chunk in 10 seconds
        if ( $deleted_count >= $batch_size && function_exists( 'as_schedule_single_action' ) ) {
            if ( ! self::has_action_in_queue( 'nbd_s3_chain_cleanup_drafts', array( 'pending' ) ) ) {
                as_schedule_single_action( time() + 10, 'nbd_s3_chain_cleanup_drafts', array( $batch_size, $force ) );
                if ( function_exists( 'spawn_cron' ) ) {
                    spawn_cron();
                }
            }
            NBD_S3_Logger::log(
                'DRAFT_CLEANUP',
                sprintf( __( 'Draft cleanup batch complete: purged %d abandoned guest drafts. Next batch auto-scheduled in 10 seconds...', 'nbd-s3-offload' ), $deleted_count ),
                array( 'batch_deleted' => $deleted_count ),
                'info'
            );
        } elseif ( $deleted_count > 0 ) {
            NBD_S3_Logger::log(
                'DRAFT_CLEANUP',
                sprintf( __( 'Draft cleanup complete! Purged final %d abandoned guest drafts. All abandoned drafts cleared.', 'nbd-s3-offload' ), $deleted_count ),
                array( 'total_deleted' => $deleted_count ),
                'success'
            );
        } else {
            NBD_S3_Logger::log(
                'DRAFT_CLEANUP',
                __( 'Draft cleanup check complete. No abandoned guest drafts found exceeding retention threshold.', 'nbd-s3-offload' ),
                array(),
                'info'
            );
        }

        return $deleted_count;
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
            as_unschedule_all_actions( 'nbd_s3_cron_saved_designs_lifecycle' );
        }
        as_schedule_recurring_action( $start_ts, DAY_IN_SECONDS, 'nbd_s3_cron_cold_sweep' );
        as_schedule_recurring_action( $start_ts + 300, DAY_IN_SECONDS, 'nbd_s3_cron_saved_designs_lifecycle' );
    }

    /**
     * Register recurring Action Scheduler tasks.
     */
    public static function register_schedules(): void {
        if ( function_exists( 'as_schedule_recurring_action' ) ) {
            $settings = NBD_S3_Settings::get_all();
            $target_time = $settings['sync_schedule_time'] ?? '03:00';
            $start_ts = self::calculate_next_schedule_timestamp( $target_time );

            if ( ! as_has_scheduled_action( 'nbd_s3_cron_cold_sweep' ) ) {
                as_schedule_recurring_action( $start_ts, DAY_IN_SECONDS, 'nbd_s3_cron_cold_sweep' );
            }
            if ( ! as_has_scheduled_action( 'nbd_s3_cron_saved_designs_lifecycle' ) ) {
                as_schedule_recurring_action( $start_ts + 300, DAY_IN_SECONDS, 'nbd_s3_cron_saved_designs_lifecycle' );
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
        $settings  = NBD_S3_Settings::get_all();
        $transient = get_transient( 'nbd_s3_sync_status' );
        $is_running = ! empty( $transient ) && ! empty( $transient['running'] );

        // Also check if Action Scheduler has pending async offload items or active auto-chaining sweep/cleanup
        $has_pending_items = self::has_action_in_queue( 'nbd_s3_async_offload_item', array( 'pending', 'in-progress' ) )
            || self::has_action_in_queue( 'nbd_s3_chain_cold_sweep', array( 'pending', 'in-progress' ) );
        $has_pending_draft_cleanup = self::has_action_in_queue( 'nbd_s3_chain_cleanup_drafts', array( 'pending', 'in-progress' ) );

        if ( ( $has_pending_items || $has_pending_draft_cleanup ) && empty( $settings['sync_paused'] ) ) {
            $is_running = true;
        }

        $next_run_ts = function_exists( 'as_next_scheduled_action' ) ? as_next_scheduled_action( 'nbd_s3_cron_cold_sweep' ) : null;
        $next_run_formatted = $next_run_ts && function_exists( 'wp_date' ) ? wp_date( 'Y-m-d H:i:s T', $next_run_ts ) : null;

        $tz_string = function_exists( 'wp_timezone_string' ) ? wp_timezone_string() : 'UTC';

        $action_label = 'Idle';
        if ( ! empty( $settings['sync_paused'] ) ) {
            $action_label = 'Paused (Safe Mode)';
        } elseif ( $is_running ) {
            if ( $has_pending_draft_cleanup ) {
                $action_label = 'Draft Cleanup (Auto-Chaining Active)';
            } elseif ( $has_pending_items ) {
                $action_label = 'Cold Sweep (Auto-Chaining Active)';
            } else {
                $action_label = $transient['action'] ?? 'Running';
            }
        }

        return array(
            'is_running'         => $is_running,
            'is_paused'          => ! empty( $settings['sync_paused'] ),
            'current_action'     => $action_label,
            'started_at'         => $is_running ? ( $transient['started_at'] ?? time() ) : null,
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

    /**
     * =========================================================================
     * Customer Saved Designs Lifecycle & Frontend Notices (Loco Translate Ready)
     * =========================================================================
     */

    /**
     * Render frontend expiration warning banners and badges in customer "Saved Designs" area.
     * Hooked to NBDesigner action: 'nb_before_show_design' (zero core edits).
     *
     * @param mixed $designs Array of customer saved design objects.
     */
    public static function render_saved_designs_frontend_notices( mixed $designs ): void {
        $settings = NBD_S3_Settings::get_all();
        if ( empty( $settings['enable_saved_designs_lifecycle'] ) ) {
            return;
        }

        if ( empty( $designs ) || ! is_array( $designs ) ) {
            return;
        }

        $retention_days = max( 1, (int) ( $settings['saved_designs_retention_days'] ?? 90 ) );
        $warning_days   = max( 1, (int) ( $settings['saved_designs_warning_days'] ?? 7 ) );
        $now_ts         = time();

        $badge_map      = array();
        $expiring_count = 0;

        foreach ( $designs as $d ) {
            if ( ! is_object( $d ) || empty( $d->id ) ) {
                continue;
            }

            $created_ts = ! empty( $d->created_date ) ? strtotime( $d->created_date ) : $now_ts;
            $age_days   = (int) floor( ( $now_ts - $created_ts ) / DAY_IN_SECONDS );
            $days_left  = max( 0, $retention_days - $age_days );
            $is_warning = ( $days_left <= $warning_days );

            if ( $is_warning ) {
                $expiring_count++;
            }

            if ( 0 === $days_left ) {
                $label = __( '⚠️ Expiring today!', 'nbd-s3-offload' );
            } elseif ( $is_warning ) {
                $label = sprintf(
                    /* translators: %d: number of days */
                    _n( '⚠️ Expiring in %d day', '⚠️ Expiring in %d days', $days_left, 'nbd-s3-offload' ),
                    $days_left
                );
            } else {
                $label = sprintf(
                    /* translators: %d: number of days */
                    _n( 'Expires in %d day', 'Expires in %d days', $days_left, 'nbd-s3-offload' ),
                    $days_left
                );
            }

            $badge_map[ (string) $d->id ] = array(
                'days_left'  => $days_left,
                'is_warning' => $is_warning,
                'label'      => $label,
            );
        }

        ?>
        <div class="nbd-s3-saved-designs-banner" style="margin-bottom: 24px; font-family: inherit;">
            <?php if ( $expiring_count > 0 ) : ?>
                <div class="woocommerce-message woocommerce-message--warning nbd-s3-notice-warning" style="background: #fffbeb; border: 1px solid #fde68a; border-left: 5px solid #d97706; padding: 14px 18px; border-radius: 8px; color: #92400e; box-shadow: 0 1px 3px rgba(0,0,0,0.04);">
                    <div style="font-weight: 700; font-size: 15px; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;">
                        <span>⚠️</span> <?php esc_html_e( 'Action Needed: Some of your saved designs will expire soon!', 'nbd-s3-offload' ); ?>
                    </div>
                    <div style="font-size: 13.5px; line-height: 1.5;">
                        <?php
                        printf(
                            esc_html(
                                _n(
                                    'You have %1$d saved design that will be automatically deleted in less than %2$d days. Please complete your order or use your design before it expires.',
                                    'You have %1$d saved designs that will be automatically deleted in less than %2$d days. Please complete your order or use your designs before they expire.',
                                    $expiring_count,
                                    'nbd-s3-offload'
                                )
                            ),
                            $expiring_count,
                            $warning_days
                        );
                        ?>
                    </div>
                    <div style="font-size: 12px; margin-top: 6px; color: #b45309;">
                        ℹ️ <?php printf( esc_html__( 'Storage Policy: Saved designs are automatically kept for %d days from the date they are saved.', 'nbd-s3-offload' ), $retention_days ); ?>
                    </div>
                </div>
            <?php else : ?>
                <div class="woocommerce-info nbd-s3-notice-info" style="background: #f8fafc; border: 1px solid #e2e8f0; border-left: 5px solid #3b82f6; padding: 12px 16px; border-radius: 8px; color: #334155; font-size: 13px; line-height: 1.5;">
                    ℹ️ <strong><?php esc_html_e( 'Design Storage Notice:', 'nbd-s3-offload' ); ?></strong> <?php printf( esc_html__( 'Designs saved in your account are automatically stored for %d days. Any design expiring soon will show an expiration warning.', 'nbd-s3-offload' ), $retention_days ); ?>
                </div>
            <?php endif; ?>
        </div>

        <style>
            .nbd-s3-countdown-badge {
                display: inline-block;
                margin-top: 6px;
                padding: 3px 8px;
                font-size: 11px;
                font-weight: 600;
                border-radius: 12px;
                line-height: 1.2;
                letter-spacing: 0.02em;
            }
            .nbd-s3-badge-normal {
                background-color: #f1f5f9;
                color: #475569;
                border: 1px solid #e2e8f0;
            }
            .nbd-s3-badge-warning {
                background-color: #fef3c7;
                color: #92400e;
                border: 1px solid #fde68a;
                animation: nbdS3Pulse 2.5s infinite;
            }
            @keyframes nbdS3Pulse {
                0%, 100% { opacity: 1; }
                50% { opacity: 0.75; }
            }
        </style>

        <script>
        (function() {
            var badgeMap = <?php echo wp_json_encode( $badge_map ); ?>;
            if (!badgeMap || Object.keys(badgeMap).length === 0) return;

            function injectExpiryBadges() {
                var rows = document.querySelectorAll('.container-design table tr.order');
                if (!rows.length) return;

                rows.forEach(function(row) {
                    var idCell = row.querySelector('td[data-title="Design ID"]') || row.cells[0];
                    if (!idCell) return;
                    var rawId = idCell.textContent.trim();
                    var badgeInfo = badgeMap[rawId];
                    if (!badgeInfo) return;

                    if (row.querySelector('.nbd-s3-countdown-badge')) return;

                    var badge = document.createElement('div');
                    badge.className = 'nbd-s3-countdown-badge ' + (badgeInfo.is_warning ? 'nbd-s3-badge-warning' : 'nbd-s3-badge-normal');
                    badge.textContent = badgeInfo.label;

                    var dateCell = row.querySelector('td[data-title="Date"]') || row.cells[2] || idCell;
                    dateCell.appendChild(badge);
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', injectExpiryBadges);
            } else {
                injectExpiryBadges();
            }
        })();
        </script>
        <?php
    }

    /**
     * Get live statistics for Customer Saved Designs.
     *
     * @return array
     */
    public static function get_saved_designs_stats(): array {
        global $wpdb;
        $settings       = NBD_S3_Settings::get_all();
        $table          = $wpdb->prefix . 'nbdesigner_mydesigns';
        $retention_days = max( 1, (int) ( $settings['saved_designs_retention_days'] ?? 90 ) );
        $warning_days   = max( 1, (int) ( $settings['saved_designs_warning_days'] ?? 7 ) );

        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
            return array(
                'total'          => 0,
                'expiring_soon'  => 0,
                'expired'        => 0,
                'retention_days' => $retention_days,
                'warning_days'   => $warning_days,
                'enabled'        => ! empty( $settings['enable_saved_designs_lifecycle'] ),
            );
        }

        $total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table}" );

        // Expired designs: created_date <= NOW() - retention_days
        $expired = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE created_date <= DATE_SUB(NOW(), INTERVAL %d DAY)",
            $retention_days
        ) );

        // Expiring soon: within warning window
        $warning_threshold = max( 0, $retention_days - $warning_days );
        $expiring_soon = (int) $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE created_date <= DATE_SUB(NOW(), INTERVAL %d DAY) AND created_date > DATE_SUB(NOW(), INTERVAL %d DAY)",
            $warning_threshold,
            $retention_days
        ) );

        return array(
            'total'          => $total,
            'expiring_soon'  => $expiring_soon,
            'expired'        => $expired,
            'retention_days' => $retention_days,
            'warning_days'   => $warning_days,
            'enabled'        => ! empty( $settings['enable_saved_designs_lifecycle'] ),
        );
    }

    /**
     * Daily background scheduled task: purges expired customer saved designs and dispatches warning emails.
     */
    public static function run_saved_designs_lifecycle(): void {
        global $wpdb;
        $settings = NBD_S3_Settings::get_all();

        if ( ! empty( $settings['sync_paused'] ) || empty( $settings['enable_saved_designs_lifecycle'] ) ) {
            return;
        }

        $table = $wpdb->prefix . 'nbdesigner_mydesigns';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
            return;
        }

        $retention_days = max( 1, (int) ( $settings['saved_designs_retention_days'] ?? 90 ) );
        $warning_days   = max( 1, (int) ( $settings['saved_designs_warning_days'] ?? 7 ) );
        $now_ts         = time();

        // 1. Purge fully expired designs
        self::run_saved_designs_purge( false );

        // 2. Dispatch expiration warning emails (if enabled)
        if ( ! empty( $settings['saved_designs_send_warning_email'] ) ) {
            $warning_threshold = max( 0, $retention_days - $warning_days );

            $warning_rows = $wpdb->get_results( $wpdb->prepare(
                "SELECT * FROM {$table} WHERE created_date <= DATE_SUB(NOW(), INTERVAL %d DAY) AND created_date > DATE_SUB(NOW(), INTERVAL %d DAY) LIMIT 50",
                $warning_threshold,
                $retention_days
            ) );

            if ( ! empty( $warning_rows ) ) {
                $warnings_sent_log = get_option( 'nbd_s3_saved_design_warnings_sent', array() );
                if ( ! is_array( $warnings_sent_log ) ) {
                    $warnings_sent_log = array();
                }

                $sent_any = false;
                foreach ( $warning_rows as $row ) {
                    $did = (int) $row->id;
                    // Anti-spam check: send only once per design
                    if ( isset( $warnings_sent_log[ $did ] ) ) {
                        continue;
                    }

                    $created_ts = strtotime( $row->created_date );
                    $age_days   = (int) floor( ( $now_ts - $created_ts ) / DAY_IN_SECONDS );
                    $days_left  = max( 1, $retention_days - $age_days );

                    $sent = self::send_saved_design_warning_email( $row, $days_left );
                    if ( $sent ) {
                        $warnings_sent_log[ $did ] = $now_ts;
                        $sent_any = true;
                        NBD_S3_Logger::log(
                            'SAVED_DESIGN_WARN',
                            'INFO',
                            sprintf(
                                /* translators: 1: design ID, 2: days left, 3: user ID */
                                __( 'Sent %2$d-day expiration warning email to User #%3$d for saved design #%1$s.', 'nbd-s3-offload' ),
                                (string) $row->id,
                                $days_left,
                                (int) $row->user_id
                            )
                        );
                    }
                }

                if ( $sent_any ) {
                    update_option( 'nbd_s3_saved_design_warnings_sent', $warnings_sent_log, false );
                }
            }
        }
    }

    /**
     * Purge expired customer saved designs.
     * Can be invoked manually from admin dashboard or automatically by lifecycle scheduler.
     *
     * @param bool $is_manual Whether triggered manually by admin click.
     * @return array
     */
    public static function run_saved_designs_purge( bool $is_manual = false ): array {
        global $wpdb;
        $settings = NBD_S3_Settings::get_all();

        if ( ! $is_manual && ! empty( $settings['sync_paused'] ) ) {
            return array(
                'success'      => false,
                'message'      => __( 'Skipped: Sync is paused by admin (Safe Mode active).', 'nbd-s3-offload' ),
                'purged_count' => 0,
            );
        }

        if ( ! $is_manual && empty( $settings['enable_saved_designs_lifecycle'] ) ) {
            return array(
                'success'      => false,
                'message'      => __( 'Saved designs lifecycle policy is disabled.', 'nbd-s3-offload' ),
                'purged_count' => 0,
            );
        }

        $table = $wpdb->prefix . 'nbdesigner_mydesigns';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$table}'" ) !== $table ) {
            return array(
                'success'      => false,
                'message'      => __( 'Table nbdesigner_mydesigns does not exist.', 'nbd-s3-offload' ),
                'purged_count' => 0,
            );
        }

        $retention_days = max( 1, (int) ( $settings['saved_designs_retention_days'] ?? 90 ) );

        $expired_rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$table} WHERE created_date <= DATE_SUB(NOW(), INTERVAL %d DAY) ORDER BY created_date ASC LIMIT 100",
            $retention_days
        ) );

        if ( empty( $expired_rows ) ) {
            return array(
                'success'      => true,
                'purged_count' => 0,
                'message'      => __( 'No expired customer saved designs found to purge.', 'nbd-s3-offload' ),
            );
        }

        $purged_count = 0;
        foreach ( $expired_rows as $row ) {
            $ok = self::purge_single_saved_design( (int) $row->id, $row );
            if ( $ok ) {
                $purged_count++;
            }
        }

        if ( $purged_count > 0 && $is_manual ) {
            NBD_S3_Logger::log(
                'SAVED_DESIGN_PURGE',
                'SUCCESS',
                sprintf(
                    /* translators: %d: number of purged designs */
                    _n(
                        'Manual purge completed: Successfully deleted %d expired customer saved design and notified customer.',
                        'Manual purge completed: Successfully deleted %d expired customer saved designs and notified customers.',
                        $purged_count,
                        'nbd-s3-offload'
                    ),
                    $purged_count
                )
            );
        }

        return array(
            'success'      => true,
            'purged_count' => $purged_count,
            'message'      => sprintf(
                /* translators: %d: count */
                _n(
                    'Successfully purged %d expired saved design.',
                    'Successfully purged %d expired saved designs.',
                    $purged_count,
                    'nbd-s3-offload'
                ),
                $purged_count
            ),
        );
    }

    /**
     * Permanently purge a single saved design: remove local files, remove remote S3 copy, delete DB row, and email user.
     *
     * @param int          $design_id Primary key in nbdesigner_mydesigns.
     * @param ?object      $design    Optional pre-fetched row object.
     * @return bool
     */
    public static function purge_single_saved_design( int $design_id, ?object $design = null ): bool {
        global $wpdb;
        $table = $wpdb->prefix . 'nbdesigner_mydesigns';

        if ( null === $design ) {
            $design = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE id = %d", $design_id ) );
        }

        if ( ! $design || empty( $design->folder ) ) {
            return false;
        }

        $folder = sanitize_file_name( $design->folder );
        $upload_dir = wp_upload_dir();
        $base_dir = defined( 'NBDESIGNER_CUSTOMER_DIR' ) ? NBDESIGNER_CUSTOMER_DIR : trailingslashit( $upload_dir['basedir'] ) . 'nbdesigner';
        $folder_path = trailingslashit( $base_dir ) . $folder;

        // 1. Delete local files and directory
        if ( is_dir( $folder_path ) ) {
            self::delete_directory( $folder_path );
        }

        // 2. Delete remote S3 objects if present
        try {
            $client = self::get_client();
            $client->delete_folder( 'designs/' . $folder );
        } catch ( \Throwable $e ) {
            // S3 failure non-fatal to DB deletion
        }

        // 3. Unregister from offload registry
        self::unregister_offloaded_folder( $folder );

        // 4. Delete row from database
        $wpdb->delete( $table, array( 'id' => $design_id ) );

        // 5. Clean up anti-spam warning tracking
        $warnings_sent_log = get_option( 'nbd_s3_saved_design_warnings_sent', array() );
        if ( is_array( $warnings_sent_log ) && isset( $warnings_sent_log[ $design_id ] ) ) {
            unset( $warnings_sent_log[ $design_id ] );
            update_option( 'nbd_s3_saved_design_warnings_sent', $warnings_sent_log, false );
        }

        // 6. Send notification email to customer
        self::send_saved_design_purge_email( $design );

        // 7. Write to Activity Audit Log
        NBD_S3_Logger::log(
            'SAVED_DESIGN_PURGE',
            'SUCCESS',
            sprintf(
                /* translators: 1: design ID, 2: folder key, 3: user ID */
                __( 'Purged expired saved design #%1$s (Folder: %2$s) belonging to User #%3$d. Local files wiped and customer notified.', 'nbd-s3-offload' ),
                (string) $design_id,
                $folder,
                (int) $design->user_id
            )
        );

        return true;
    }

    /**
     * Send expiration warning email to customer (Loco Translate ready).
     *
     * @param object $design    Design record.
     * @param int    $days_left Number of days remaining before deletion.
     * @return bool
     */
    public static function send_saved_design_warning_email( object $design, int $days_left ): bool {
        $settings = NBD_S3_Settings::get_all();
        if ( empty( $settings['saved_designs_send_warning_email'] ) ) {
            return false;
        }

        $user = get_userdata( (int) $design->user_id );
        if ( ! $user || empty( $user->user_email ) ) {
            return false;
        }

        $customer_email = $user->user_email;
        $customer_name  = ! empty( $user->display_name ) ? $user->display_name : ( ! empty( $user->first_name ) ? $user->first_name : $user->user_login );

        $product_title = get_the_title( (int) $design->product_id );
        if ( empty( $product_title ) ) {
            $product_title = sprintf( __( 'Custom Design #%s', 'nbd-s3-offload' ), (string) $design->id );
        }

        $site_name = get_bloginfo( 'name' );
        $subject   = sprintf(
            /* translators: 1: site name, 2: days count */
            _n(
                '[%1$s] Action Required: Your saved design will expire in %2$d day',
                '[%1$s] Action Required: Your saved design will expire in %2$d days',
                $days_left,
                'nbd-s3-offload'
            ),
            $site_name,
            $days_left
        );

        $heading = __( 'Your Saved Design is Expiring Soon', 'nbd-s3-offload' );

        $account_url = function_exists( 'wc_get_endpoint_url' ) && function_exists( 'wc_get_page_permalink' )
            ? wc_get_endpoint_url( 'my-designs', '', wc_get_page_permalink( 'myaccount' ) )
            : home_url( '/my-account/' );

        $created_date = ! empty( $design->created_date ) ? $design->created_date : __( 'N/A', 'nbd-s3-offload' );

        $content  = '<p>' . sprintf( esc_html__( 'Hello %s,', 'nbd-s3-offload' ), esc_html( $customer_name ) ) . '</p>';
        $content .= '<p>' . sprintf(
            /* translators: 1: design ID, 2: product title, 3: created date, 4: days remaining */
            esc_html( _n(
                'This is a friendly reminder that your saved design #%1$s ("%2$s"), created on %3$s, is scheduled to be automatically deleted in %4$d day under our storage retention policy.',
                'This is a friendly reminder that your saved design #%1$s ("%2$s"), created on %3$s, is scheduled to be automatically deleted in %4$d days under our storage retention policy.',
                $days_left,
                'nbd-s3-offload'
            ) ),
            '<strong>' . esc_html( $design->id ) . '</strong>',
            '<strong>' . esc_html( $product_title ) . '</strong>',
            esc_html( $created_date ),
            $days_left
        ) . '</p>';
        $content .= '<p>' . esc_html__( 'If you would like to complete your purchase or review your design before it is permanently removed, please visit your account:', 'nbd-s3-offload' ) . '</p>';
        $content .= '<p style="margin: 25px 0;"><a href="' . esc_url( $account_url ) . '" style="background-color: #4f46e5; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">' . esc_html__( 'View Your Saved Designs', 'nbd-s3-offload' ) . '</a></p>';
        $content .= '<p style="color: #64748b; font-size: 13px;">' . sprintf( esc_html__( 'Thank you for designing with %s.', 'nbd-s3-offload' ), esc_html( $site_name ) ) . '</p>';

        return self::deliver_html_email( $customer_email, $subject, $heading, $content );
    }

    /**
     * Send purge notification email to customer after design deletion (Loco Translate ready).
     *
     * @param object $design Design record.
     * @return bool
     */
    public static function send_saved_design_purge_email( object $design ): bool {
        $settings = NBD_S3_Settings::get_all();
        if ( empty( $settings['saved_designs_send_purge_email'] ) ) {
            return false;
        }

        $user = get_userdata( (int) $design->user_id );
        if ( ! $user || empty( $user->user_email ) ) {
            return false;
        }

        $customer_email = $user->user_email;
        $customer_name  = ! empty( $user->display_name ) ? $user->display_name : ( ! empty( $user->first_name ) ? $user->first_name : $user->user_login );

        $product_title = get_the_title( (int) $design->product_id );
        if ( empty( $product_title ) ) {
            $product_title = sprintf( __( 'Custom Design #%s', 'nbd-s3-offload' ), (string) $design->id );
        }

        $site_name = get_bloginfo( 'name' );
        $subject   = sprintf(
            /* translators: 1: site name, 2: design ID */
            __( '[%1$s] Notice: Your saved design #%2$s has expired and was removed', 'nbd-s3-offload' ),
            $site_name,
            (string) $design->id
        );

        $heading = __( 'Saved Design Expired and Removed', 'nbd-s3-offload' );

        $shop_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' );
        $retention_days = (int) ( $settings['saved_designs_retention_days'] ?? 90 );
        $created_date = ! empty( $design->created_date ) ? $design->created_date : __( 'N/A', 'nbd-s3-offload' );

        $content  = '<p>' . sprintf( esc_html__( 'Hello %s,', 'nbd-s3-offload' ), esc_html( $customer_name ) ) . '</p>';
        $content .= '<p>' . sprintf(
            /* translators: 1: design ID, 2: product title, 3: created date, 4: retention days */
            esc_html__( 'Your saved design #%1$s ("%2$s"), created on %3$s, has reached our %4$d-day storage retention limit and has now been automatically removed from our system.', 'nbd-s3-offload' ),
            '<strong>' . esc_html( $design->id ) . '</strong>',
            '<strong>' . esc_html( $product_title ) . '</strong>',
            esc_html( $created_date ),
            $retention_days
        ) . '</p>';
        $content .= '<p>' . esc_html__( 'You are always welcome to start a fresh design whenever you are ready to create your next project!', 'nbd-s3-offload' ) . '</p>';
        $content .= '<p style="margin: 25px 0;"><a href="' . esc_url( $shop_url ) . '" style="background-color: #4f46e5; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;">' . esc_html__( 'Start a New Design', 'nbd-s3-offload' ) . '</a></p>';
        $content .= '<p style="color: #64748b; font-size: 13px;">' . sprintf( esc_html__( 'Thank you for choosing %s.', 'nbd-s3-offload' ), esc_html( $site_name ) ) . '</p>';

        return self::deliver_html_email( $customer_email, $subject, $heading, $content );
    }

    /**
     * Deliver HTML email wrapped with WooCommerce template or clean fallback.
     *
     * @param string $to      Recipient email address.
     * @param string $subject Subject line.
     * @param string $heading Heading title.
     * @param string $content Body content HTML.
     * @return bool
     */
    private static function deliver_html_email( string $to, string $subject, string $heading, string $content ): bool {
        $headers = array( 'Content-Type: text/html; charset=UTF-8' );

        if ( function_exists( 'WC' ) && WC()->mailer() ) {
            $mailer = WC()->mailer();
            $wrapped = $mailer->wrap_message( $heading, $content );
            return (bool) $mailer->send( $to, $subject, $wrapped, $headers );
        }

        $html  = '<!DOCTYPE html><html><body style="font-family: -apple-system, BlinkMacSystemFont, Segoe UI, Roboto, Helvetica, Arial, sans-serif; line-height: 1.6; color: #334155; padding: 20px;">';
        $html .= '<div style="max-width: 600px; margin: 0 auto; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 28px;">';
        $html .= '<h2 style="color: #1e293b; margin-top: 0;">' . esc_html( $heading ) . '</h2>';
        $html .= $content;
        $html .= '</div></body></html>';

        return (bool) wp_mail( $to, $subject, $html, $headers );
    }
}

