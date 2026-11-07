<?php
/**
 * REST API Endpoints for NBDesigner S3 Offload Admin
 *
 * @package NBD_S3_Offload
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NBD_S3_REST_API {

    public const NAMESPACE = 'nbd-s3-offload/v1';

    /**
     * Register REST routes.
     */
    public static function register_routes(): void {
        register_rest_route( self::NAMESPACE, '/settings', array(
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array( __CLASS__, 'get_settings' ),
                'permission_callback' => array( __CLASS__, 'check_permission' ),
            ),
            array(
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => array( __CLASS__, 'save_settings' ),
                'permission_callback' => array( __CLASS__, 'check_permission' ),
            ),
        ) );

        register_rest_route( self::NAMESPACE, '/test-connection', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'test_connection' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::NAMESPACE, '/fonts/purge-resync', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'purge_resync_fonts' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::NAMESPACE, '/stats', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_stats' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::NAMESPACE, '/sweep-now', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'trigger_cold_sweep' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::NAMESPACE, '/whitelist/restore-all', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'restore_whitelisted_templates' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::NAMESPACE, '/explorer/folders', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_explorer_folders' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::NAMESPACE, '/explorer/test-batch', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'test_batch_offload' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::NAMESPACE, '/explorer/offload-single', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'offload_single_folder' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::NAMESPACE, '/explorer/restore-single', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'restore_single_folder' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );
    }

    /**
     * Permission check: Admin or WooCommerce Shop Manager.
     */
    public static function check_permission( \WP_REST_Request $request ): bool {
        return current_user_can( 'manage_woocommerce' ) || current_user_can( 'manage_options' );
    }

    /**
     * GET /settings
     */
    public static function get_settings( \WP_REST_Request $request ): \WP_REST_Response {
        return new \WP_REST_Response( array(
            'success'  => true,
            'settings' => NBD_S3_Settings::get_for_client(),
        ), 200 );
    }

    /**
     * POST /settings
     */
    public static function save_settings( \WP_REST_Request $request ): \WP_REST_Response {
        $params  = $request->get_json_params() ?: array();
        $updated = NBD_S3_Settings::update( $params );

        return new \WP_REST_Response( array(
            'success'  => $updated,
            'message'  => $updated ? __( 'Settings saved successfully.', 'nbd-s3-offload' ) : __( 'No changes detected or update failed.', 'nbd-s3-offload' ),
            'settings' => NBD_S3_Settings::get_for_client(),
        ), 200 );
    }

    /**
     * POST /test-connection
     */
    public static function test_connection( \WP_REST_Request $request ): \WP_REST_Response {
        $params = $request->get_json_params() ?: array();

        // If credentials are supplied in the test request, use them; otherwise use saved
        $current = NBD_S3_Settings::get_all();

        $secret = $params['secret_key'] ?? '';
        if ( empty( $secret ) || str_contains( $secret, '••••' ) ) {
            $secret = $current['secret_key'];
        }

        $client = new NBD_S3_Client( array(
            'access_key'     => $params['access_key'] ?? $current['access_key'],
            'secret_key'     => $secret,
            'region'         => $params['region'] ?? $current['region'],
            'bucket'         => $params['bucket'] ?? $current['bucket'],
            'endpoint'       => $params['endpoint'] ?? $current['endpoint'],
            'use_path_style' => $params['use_path_style'] ?? $current['use_path_style'],
        ) );

        $result = $client->test_connection();

        return new \WP_REST_Response( $result, 200 );
    }

    /**
     * POST /fonts/purge-resync
     */
    public static function purge_resync_fonts( \WP_REST_Request $request ): \WP_REST_Response {
        $result = NBD_S3_Tier_Lifecycle::purge_and_resync_fonts();
        return new \WP_REST_Response( $result, 200 );
    }

    /**
     * POST /sweep-now
     */
    public static function trigger_cold_sweep( \WP_REST_Request $request ): \WP_REST_Response {
        NBD_S3_Tier_Lifecycle::run_cold_sweep();
        return new \WP_REST_Response( array(
            'success' => true,
            'message' => __( 'Cold storage sweep task executed.', 'nbd-s3-offload' ),
        ), 200 );
    }

    /**
     * GET /stats
     */
    public static function get_stats( \WP_REST_Request $request ): \WP_REST_Response {
        global $wpdb;

        $designs_dir = defined( 'NBDESIGNER_CUSTOMER_DIR' ) ? NBDESIGNER_CUSTOMER_DIR : ( WP_CONTENT_DIR . '/uploads/nbdesigner/designs' );
        $fonts_dir   = defined( 'NBDESIGNER_FONT_DIR' ) ? NBDESIGNER_FONT_DIR : ( WP_CONTENT_DIR . '/uploads/nbdesigner/fonts' );

        $local_designs_count = 0;
        $local_designs_bytes = 0;
        if ( is_dir( $designs_dir ) ) {
            $dirs = glob( rtrim( $designs_dir, '/' ) . '/*', GLOB_ONLYDIR );
            $local_designs_count = is_array( $dirs ) ? count( $dirs ) : 0;
        }

        $local_fonts_count = 0;
        $local_fonts_bytes = 0;
        if ( is_dir( $fonts_dir ) ) {
            $font_files = glob( rtrim( $fonts_dir, '/' ) . '/*' );
            if ( is_array( $font_files ) ) {
                $local_fonts_count = count( $font_files );
                foreach ( $font_files as $f ) {
                    if ( is_file( $f ) ) {
                        $local_fonts_bytes += (int) filesize( $f );
                    }
                }
            }
        }

        // Count offloaded order items
        $offloaded_orders_count = (int) $wpdb->get_var( "SELECT COUNT(DISTINCT order_item_id) FROM {$wpdb->prefix}woocommerce_order_itemmeta WHERE meta_key = '_nbd_s3_offloaded' AND meta_value = '1'" );

        // Count templates
        $templates_table = $wpdb->prefix . 'nbdesigner_templates';
        $templates_count = 0;
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$templates_table}'" ) === $templates_table ) {
            $templates_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$templates_table}" );
        }

        return new \WP_REST_Response( array(
            'success' => true,
            'stats'   => array(
                'local_designs_count'    => $local_designs_count,
                'local_fonts_count'      => $local_fonts_count,
                'local_fonts_size'       => size_format( $local_fonts_bytes ),
                'offloaded_orders_count' => $offloaded_orders_count,
                'templates_count'        => $templates_count,
            ),
        ), 200 );
    }

    /**
     * POST /whitelist/restore-all
     */
    public static function restore_whitelisted_templates( \WP_REST_Request $request ): \WP_REST_Response {
        $result = NBD_S3_Tier_Lifecycle::restore_all_whitelisted_templates();
        return new \WP_REST_Response( array(
            'success'       => true,
            'message'       => sprintf( 'Whitelisted templates restored to local SSD: %d downloaded from S3, %d already present locally.', $result['restored'], $result['already_local'] ),
            'total'         => $result['total'],
            'restored'      => $result['restored'],
            'already_local' => $result['already_local'],
        ), 200 );
    }

    /**
     * GET /explorer/folders
     */
    public static function get_explorer_folders( \WP_REST_Request $request ): \WP_REST_Response {
        $limit  = (int) ( $request->get_param( 'limit' ) ?: 20 );
        $offset = (int) ( $request->get_param( 'offset' ) ?: 0 );
        $filter = (string) ( $request->get_param( 'filter' ) ?: 'all' );

        $data = NBD_S3_Tier_Lifecycle::get_design_folders_explorer( $limit, $offset, $filter );
        return new \WP_REST_Response( array(
            'success' => true,
            'total'   => $data['total'],
            'folders' => $data['folders'],
        ), 200 );
    }

    /**
     * POST /explorer/test-batch
     */
    public static function test_batch_offload( \WP_REST_Request $request ): \WP_REST_Response {
        $count  = (int) ( $request->get_param( 'count' ) ?: 3 );
        $result = NBD_S3_Tier_Lifecycle::test_batch_offload( $count );
        return new \WP_REST_Response( $result, 200 );
    }

    /**
     * POST /explorer/offload-single
     */
    public static function offload_single_folder( \WP_REST_Request $request ): \WP_REST_Response {
        $folder = sanitize_file_name( (string) $request->get_param( 'folder' ) );
        if ( empty( $folder ) ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'Missing folder parameter' ), 400 );
        }

        $res = NBD_S3_Tier_Lifecycle::process_single_offload( $folder );
        return new \WP_REST_Response( $res, 200 );
    }

    /**
     * POST /explorer/restore-single
     */
    public static function restore_single_folder( \WP_REST_Request $request ): \WP_REST_Response {
        $folder = sanitize_file_name( (string) $request->get_param( 'folder' ) );
        if ( empty( $folder ) ) {
            return new \WP_REST_Response( array( 'success' => false, 'message' => 'Missing folder parameter' ), 400 );
        }

        $res = NBD_S3_Tier_Lifecycle::hydrate_design_folder( $folder );
        if ( $res ) {
            return new \WP_REST_Response( array(
                'success' => true,
                'message' => sprintf( 'Folder "%s" successfully downloaded and restored back to local NVMe storage.', $folder ),
            ), 200 );
        }

        return new \WP_REST_Response( array(
            'success' => false,
            'message' => sprintf( 'Failed to restore folder "%s" from S3.', $folder ),
        ), 400 );
    }
}

