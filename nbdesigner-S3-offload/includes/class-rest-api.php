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

        register_rest_route( self::NAMESPACE, '/saved-designs/stats', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_saved_designs_stats' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::NAMESPACE, '/saved-designs/purge-now', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'purge_saved_designs_now' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::NAMESPACE, '/drafts/cleanup-now', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'trigger_draft_cleanup' ),
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

        register_rest_route( self::NAMESPACE, '/explorer/test-batch-candidates', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_test_batch_candidates' ),
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

        register_rest_route( self::NAMESPACE, '/sync/toggle-pause', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'toggle_sync_pause' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::NAMESPACE, '/connection-status', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_connection_status' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::NAMESPACE, '/logs', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( __CLASS__, 'get_logs' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::NAMESPACE, '/logs/clear', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'clear_logs' ),
            'permission_callback' => array( __CLASS__, 'check_permission' ),
        ) );

        register_rest_route( self::NAMESPACE, '/logs/record', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => array( __CLASS__, 'record_log' ),
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

        // Reset client and reschedule Action Scheduler if schedule changed
        NBD_S3_Tier_Lifecycle::reset_client();
        NBD_S3_Tier_Lifecycle::reschedule_tasks();

        NBD_S3_Logger::log( 'SETTINGS_UPDATE', __( 'Plugin settings updated by admin.', 'nbd-s3-offload' ), array(), 'info' );

        return new \WP_REST_Response( array(
            'success'           => $updated,
            'message'           => $updated ? __( 'Settings saved successfully.', 'nbd-s3-offload' ) : __( 'No changes detected or update failed.', 'nbd-s3-offload' ),
            'settings'          => NBD_S3_Settings::get_for_client(),
            'sync_status'       => NBD_S3_Tier_Lifecycle::get_sync_status(),
            'bucket_connection' => NBD_S3_Tier_Lifecycle::get_bucket_connection_status( true ),
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
        $settings = NBD_S3_Settings::get_all();
        if ( ! empty( $settings['sync_paused'] ) ) {
            return new \WP_REST_Response( array(
                'success'         => false,
                'message'         => __( 'Cannot start Cold Sweep: Sync is Paused (Safe Mode is Active). Please resume sync in the top header first.', 'nbd-s3-offload' ),
                'processed_count' => 0,
            ), 400 );
        }

        $is_already_running = function_exists( 'as_has_scheduled_action' ) && (
            as_has_scheduled_action( 'nbd_s3_async_offload_item' ) ||
            as_has_scheduled_action( 'nbd_s3_chain_cold_sweep' )
        );

        if ( $is_already_running ) {
            return new \WP_REST_Response( array(
                'success'         => true,
                'message'         => __( 'Cold sweep is already actively running in the background with auto-chaining.', 'nbd-s3-offload' ),
                'processed_count' => 0,
            ), 200 );
        }

        $queued = NBD_S3_Tier_Lifecycle::run_cold_sweep();
        $msg = function_exists( 'as_enqueue_async_action' )
            ? sprintf( __( 'Cold storage sweep initiated with auto-chaining. Batch of %d design items queued; background runner will automatically continue until all aged items are offloaded.', 'nbd-s3-offload' ), $queued )
            : sprintf( __( 'Cold storage sweep executed. %d design items processed.', 'nbd-s3-offload' ), $queued );

        return new \WP_REST_Response( array(
            'success'         => true,
            'message'         => $msg,
            'processed_count' => $queued,
        ), 200 );
    }

    /**
     * POST /drafts/cleanup-now
     */
    public static function trigger_draft_cleanup( \WP_REST_Request $request ): \WP_REST_Response {
        $settings = NBD_S3_Settings::get_all();
        if ( ! empty( $settings['sync_paused'] ) ) {
            return new \WP_REST_Response( array(
                'success'         => false,
                'message'         => __( 'Cannot start Draft Cleanup: Sync is Paused (Safe Mode is Active). Please resume sync in the top header first.', 'nbd-s3-offload' ),
                'processed_count' => 0,
            ), 400 );
        }

        $is_already_running = function_exists( 'as_has_scheduled_action' ) && as_has_scheduled_action( 'nbd_s3_chain_cleanup_drafts' );

        if ( $is_already_running ) {
            return new \WP_REST_Response( array(
                'success'         => true,
                'message'         => __( 'Abandoned draft cleanup is already actively running in the background with auto-chaining.', 'nbd-s3-offload' ),
                'processed_count' => 0,
            ), 200 );
        }

        $deleted = NBD_S3_Tier_Lifecycle::run_draft_cleanup( 500 );
        $msg = ( $deleted >= 500 && function_exists( 'as_schedule_single_action' ) )
            ? sprintf( __( 'Abandoned draft cleanup initiated with auto-chaining. First batch of %d folders purged; background runner will continue automatically in 10-second intervals until all abandoned drafts are cleared.', 'nbd-s3-offload' ), $deleted )
            : sprintf( __( 'Abandoned draft cleanup completed. %d folders purged.', 'nbd-s3-offload' ), $deleted );

        return new \WP_REST_Response( array(
            'success'         => true,
            'message'         => $msg,
            'processed_count' => $deleted,
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

        // Count offloaded design folders (from persistent offload registry + WooCommerce order item meta)
        $registry_folders = array_keys( NBD_S3_Tier_Lifecycle::get_offloaded_registry() );

        $order_meta_table = $wpdb->prefix . 'woocommerce_order_itemmeta';
        $order_offloaded_folders = array();
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$order_meta_table}'" ) === $order_meta_table ) {
            $order_offloaded_folders = $wpdb->get_col(
                "SELECT DISTINCT m1.meta_value 
                 FROM {$order_meta_table} m1 
                 INNER JOIN {$order_meta_table} m2 ON m1.order_item_id = m2.order_item_id 
                 WHERE (m1.meta_key = '_nbd' OR m1.meta_key = '_nbd_item_key') 
                   AND m2.meta_key = '_nbd_s3_offloaded' 
                   AND m2.meta_value = '1'"
            );
        }

        $all_offloaded_folders  = array_unique( array_merge( $registry_folders, (array) $order_offloaded_folders ) );
        $offloaded_orders_count = count( $all_offloaded_folders );

        // Count templates
        $templates_table = $wpdb->prefix . 'nbdesigner_templates';
        $templates_count = 0;
        if ( $wpdb->get_var( "SHOW TABLES LIKE '{$templates_table}'" ) === $templates_table ) {
            $templates_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$templates_table}" );
        }

        $force_refresh      = ! empty( $request->get_param( 'force' ) );
        $tier2_active_count = NBD_S3_Tier_Lifecycle::get_tier2_active_count( $force_refresh );

        return new \WP_REST_Response( array(
            'success' => true,
            'stats'   => array(
                'total_local_folders'    => $local_designs_count,
                'local_designs_count'    => $local_designs_count,
                'tier2_active_count'     => $tier2_active_count,
                'active_orders_count'    => $tier2_active_count,
                'local_fonts_count'      => $local_fonts_count,
                'local_fonts_size'       => size_format( $local_fonts_bytes ),
                'offloaded_orders_count' => $offloaded_orders_count,
                'offloaded_count'        => $offloaded_orders_count,
                'templates_count'        => $templates_count,
                'saved_designs'          => NBD_S3_Tier_Lifecycle::get_saved_designs_stats(),
            ),
            'sync_status'       => NBD_S3_Tier_Lifecycle::get_sync_status(),
            'bucket_connection' => NBD_S3_Tier_Lifecycle::get_bucket_connection_status(),
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
        $limit   = (int) ( $request->get_param( 'limit' ) ?: 20 );
        $offset  = (int) ( $request->get_param( 'offset' ) ?: 0 );
        $filter  = sanitize_text_field( $request->get_param( 'filter' ) ?: 'all' );
        $refresh = (bool) $request->get_param( 'refresh' );
        $search  = sanitize_text_field( $request->get_param( 'search' ) ?: '' );

        $data = NBD_S3_Tier_Lifecycle::get_design_folders_explorer( $limit, $offset, $filter, $refresh, $search );
        return new \WP_REST_Response( array(
            'success' => true,
            'total'   => $data['total'],
            'folders' => $data['folders'],
        ), 200 );
    }

    /**
     * GET /explorer/test-batch-candidates
     */
    public static function get_test_batch_candidates( \WP_REST_Request $request ): \WP_REST_Response {
        $count   = (int) ( $request->get_param( 'count' ) ?: 3 );
        $folders = NBD_S3_Tier_Lifecycle::get_eligible_batch_folders( $count );

        return new \WP_REST_Response( array(
            'success' => true,
            'total'   => count( $folders ),
            'folders' => $folders,
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

        // Explicit admin manual offload via Explorer or Sandbox Trial
        $res = NBD_S3_Tier_Lifecycle::process_single_offload( $folder, 0, true );
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
            $settings = NBD_S3_Settings::get_all();
            $msg      = ! empty( $settings['delete_remote_on_restore'] )
                ? sprintf( 'Folder "%s" successfully restored to local NVMe storage and cleaned up from S3.', $folder )
                : sprintf( 'Folder "%s" successfully downloaded and restored back to local NVMe storage.', $folder );
            return new \WP_REST_Response( array(
                'success' => true,
                'message' => $msg,
            ), 200 );
        }

        return new \WP_REST_Response( array(
            'success' => false,
            'message' => sprintf( 'Failed to restore folder "%s" from S3.', $folder ),
        ), 400 );
    }

    /**
     * POST /sync/toggle-pause
     */
    public static function toggle_sync_pause( \WP_REST_Request $request ): \WP_REST_Response {
        $settings = NBD_S3_Settings::get_all();
        $params   = $request->get_json_params() ?: array();

        $new_pause_state = isset( $params['paused'] ) ? (bool) $params['paused'] : ! ( $settings['sync_paused'] ?? true );
        NBD_S3_Settings::update( array( 'sync_paused' => $new_pause_state ) );

        // If pausing (Safe Mode active), immediately unschedule any queued offload tasks and clear active indicator
        if ( $new_pause_state ) {
            if ( function_exists( 'as_unschedule_all_actions' ) ) {
                as_unschedule_all_actions( 'nbd_s3_async_offload_item' );
                as_unschedule_all_actions( 'nbd_s3_chain_cold_sweep' );
                as_unschedule_all_actions( 'nbd_s3_chain_cleanup_drafts' );
            }
            delete_transient( 'nbd_s3_sync_status' );
        }

        $log_action = $new_pause_state ? 'SYNC_PAUSED' : 'SYNC_RESUMED';
        $log_msg    = $new_pause_state
            ? __( 'Automated S3 sync and purges paused by admin (Safe Mode active).', 'nbd-s3-offload' )
            : __( 'Automated S3 sync and purges resumed by admin.', 'nbd-s3-offload' );
        NBD_S3_Logger::log( $log_action, $log_msg, array( 'paused' => $new_pause_state ), $new_pause_state ? 'warning' : 'info' );

        return new \WP_REST_Response( array(
            'success'     => true,
            'paused'      => $new_pause_state,
            'message'     => $log_msg,
            'sync_status' => NBD_S3_Tier_Lifecycle::get_sync_status(),
        ), 200 );
    }

    /**
     * GET /connection-status
     */
    public static function get_connection_status( \WP_REST_Request $request ): \WP_REST_Response {
        $force  = (bool) $request->get_param( 'force' );
        $status = NBD_S3_Tier_Lifecycle::get_bucket_connection_status( $force );
        return new \WP_REST_Response( array(
            'success' => true,
            'status'  => $status,
        ), 200 );
    }

    /**
     * GET /logs
     */
    public static function get_logs( \WP_REST_Request $request ): \WP_REST_Response {
        $limit = max( 10, min( 500, (int) ( $request->get_param( 'limit' ) ?: 100 ) ) );
        return new \WP_REST_Response( array(
            'success' => true,
            'logs'    => NBD_S3_Logger::get_logs( $limit ),
        ), 200 );
    }

    /**
     * POST /logs/clear
     */
    public static function clear_logs( \WP_REST_Request $request ): \WP_REST_Response {
        NBD_S3_Logger::clear_logs();
        return new \WP_REST_Response( array(
            'success' => true,
            'message' => __( 'Activity log cleared.', 'nbd-s3-offload' ),
            'logs'    => array(),
        ), 200 );
    }

    /**
     * POST /logs/record
     * Record a client-driven activity audit log entry (e.g. batch trial start/end).
     */
    public static function record_log( \WP_REST_Request $request ): \WP_REST_Response {
        $action  = sanitize_text_field( (string) $request->get_param( 'action' ) );
        $message = sanitize_text_field( (string) $request->get_param( 'message' ) );
        $details = (array) ( $request->get_param( 'details' ) ?: array() );
        $status  = sanitize_text_field( (string) ( $request->get_param( 'status' ) ?: 'info' ) );

        if ( ! empty( $action ) && ! empty( $message ) ) {
            NBD_S3_Logger::log( $action, $message, $details, $status );
        }

        return new \WP_REST_Response( array(
            'success' => true,
            'logs'    => NBD_S3_Logger::get_logs( 150 ),
        ), 200 );
    }

    /**
     * GET /saved-designs/stats
     */
    public static function get_saved_designs_stats( \WP_REST_Request $request ): \WP_REST_Response {
        $stats = NBD_S3_Tier_Lifecycle::get_saved_designs_stats();
        return new \WP_REST_Response( array(
            'success' => true,
            'stats'   => $stats,
        ), 200 );
    }

    /**
     * POST /saved-designs/purge-now
     * Dedicated manual purge button action for customer saved designs older than X days.
     */
    public static function purge_saved_designs_now( \WP_REST_Request $request ): \WP_REST_Response {
        $result = NBD_S3_Tier_Lifecycle::run_saved_designs_purge( true );
        return new \WP_REST_Response( array(
            'success'      => $result['success'] ?? true,
            'message'      => $result['message'] ?? '',
            'purged_count' => $result['purged_count'] ?? 0,
            'stats'        => NBD_S3_Tier_Lifecycle::get_saved_designs_stats(),
        ), 200 );
    }
}

