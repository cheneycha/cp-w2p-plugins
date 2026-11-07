<?php
if (!function_exists('add_action')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
    exit;
}
use setasign\Fpdi;
use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;
class Nbdesigner_Plugin {
    public $textdomain;
    public $plugin_id;
    public $activedomain;
    public $removedomain;
    public function __construct() {
        $this->plugin_id    = 'nbdesigner';
        $this->activedomain = 'activedomain/netbase/';
        $this->removedomain = 'removedomain/netbase/';
    }
    public function init(){
        $this->hook();
        $this->schehule();
        $this->nbdesigner_lincense_notices();
        add_action( 'nbdesigner_lincense_event', array( $this, 'nbdesigner_lincense_event_action' ) ); 
        if ( in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) || is_plugin_active_for_network( 'woocommerce/woocommerce.php' ) ) {
            $this->woocommerce_hook();
        }
        if (is_admin()) {
            $this->admin_hook();
            $this->ajax();
        } else {
            $this->frontend_hook();
        }
        add_action( 'wpmu_new_blog', array( $this, 'install_in_new_blog' ), 10, 6 );
    }
    public function ajax(){
        // Nbdesigner_EVENT => nopriv
        $ajax_events = array(
            'nbdesigner_add_font_cat'                   => false,
            'nbdesigner_add_art_cat'                    => false,
            'nbdesigner_add_google_font'                => false,
            'nbdesigner_delete_font_cat'                => false,
            'nbdesigner_delete_art_cat'                 => false,
            'nbdesigner_delete_font'                    => false,
            'nbdesigner_delete_art'                     => false,
            'nbdesigner_get_product_info'               => true,
            'nbdesigner_get_qrcode'                     => true,
            'nbdesigner_get_facebook_photo'             => true,
            'nbdesigner_get_art'                        => true,
            'nbdesigner_design_approve'                 => false,
            'nbdesigner_design_order_email'             => false,
            'nbdesigner_customer_upload'                => true,
            'nbd_upload_design_file'                    => true,
            'nbdesigner_get_font'                       => true,
            'nbdesigner_get_pattern'                    => true,
            'nbdesigner_get_info_license'               => false,
            'nbdesigner_remove_license'                 => false,
            'nbdesigner_get_license_key'                => false,
            'nbdesigner_get_language'                   => true,
            'nbdesigner_save_language'                  => false,
            'nbdesigner_create_language'                => false,
            'nbdesigner_make_primary_design'            => false,
            'nbdesigner_load_admin_design'              => true,
            'nbdesigner_save_webcam_image'              => true,
            'nbdesigner_migrate_domain'                 => false,
            'nbdesigner_restore_data_migrate_domain'    => false,
            'nbdesigner_theme_check'                    => false,
            'nbdesigner_custom_css'                     => false,
            'nbdesigner_custom_js'                      => false,
            'nbdesigner_copy_image_from_url'            => true,
            'nbdesigner_get_suggest_design'             => true,
            'nbdesigner_save_design_to_pdf'             => false,
            'nbdesigner_save_design_to_pdf_with_layout' => false,
            'nbdesigner_delete_language'                => false,
            'nbdesigner_update_all_product'             => false,
            'nbd_save_customer_design'                  => true,
            'nbd_remove_design_and_file'                => true,
            'nbd_save_draft_design'                     => true,
            'nbd_update_customer_upload'                => true,
            'nbd_save_cart_design'                      => true,
            'nbd_get_nbd_products'                      => true,
            'nbd_remove_cart_design'                    => true,
            'nbd_get_product_description'               => true,
            'nbd_convert_files'                         => true,
            'nbd_frontend_download_pdf'                 => true,
            'nbd_frontend_download_jpeg'                => true,
            'nbd_download_final_pdf'                    => true,
            'nbdesigner_update_variation_v180'          => false,
            'nbdesigner_update_all_template'            => false,
            'nbd_clear_transients'                      => false,
            'nbd_create_pages'                          => false,
            'nbd_get_product_config'                    => true,
            'nbd_delete_order_design'                   => false,
            'nbd_check_use_logged_in'                   => true,
            'nbd_crop_image'                            => true,
            'nbd_fix_pdf_font'                          => true,
            'nbd_upload_pdf_as_bg_image'                => true,
            'nbd_import_images'                         => true,
            'check_flysystem_connected'                 => true,
            'nbd_get_instagram_token'                   => true
        );
        foreach( $ajax_events as $ajax_event => $nopriv ) {
            add_action( 'wp_ajax_' . $ajax_event, array( $this, $ajax_event ) );
            if ( $nopriv ) {
                add_action( 'wp_ajax_nopriv_' . $ajax_event, array( $this, $ajax_event ) );
            }
        }
    }
    public function hook(){
        add_action( 'plugins_loaded', array( $this, 'translation_load_textdomain' ) );
        add_filter( 'cron_schedules', array( $this, 'set_schedule' ) );
        add_filter( 'query_vars', array( $this, 'nbdesigner_add_query_vars_filter' ) );
        $position = nbdesigner_get_option( 'nbdesigner_position_button_product_detail', 1 );
        if( $position == 4 ) add_shortcode( 'nbdesigner_button', array( $this,'nbdesigner_button' ) );
        add_shortcode( 'nbd_loggin_redirect', array( $this,'nbd_loggin_redirect_func' ) );
        add_action( 'template_redirect', array( $this, 'nbdesigner_editor_html' ) );
        add_action( 'admin_head', array( $this, 'nbdesigner_add_tinymce_editor' ) );
        add_action( 'woocommerce_init', array( $this, 'init_session' ) );
        
        add_filter( 'body_class', array( $this, 'add_body_class' ), 10, 1 );
        add_filter( 'display_post_states', array( $this, 'add_display_post_states' ), 10, 2 );
        
        /* Force login */
        if( 'yes' == nbdesigner_get_option( 'nbdesigner_site_force_login', 'no' ) ){
            add_action( 'template_redirect', array( $this, 'force_login' ), 1 );
            add_filter( 'rest_authentication_errors', 'forcelogin_rest_access', 99 );
        }
        
        /* Cron job flush W3 Total cache */
        if( 'yes' == nbdesigner_get_option( 'nbdesigner_cron_job_clear_w3_cache', 'no' ) ){
            add_action( 'wp', array( $this, 'w3tc_cache_flush' ) );
            add_action( 'nbd_w3_flush_cache', array( $this, 'w3_flush_cache' ) ); 
        }
    }
    public function w3_flush_cache(){
        if ( function_exists( 'w3tc_flush_all' ) ){
            w3tc_flush_all();
        }
    }
    public function w3tc_cache_flush(){
        if ( ! wp_next_scheduled( 'nbd_w3_flush_cache' ) ) {
            wp_schedule_event( current_time( 'timestamp' ), 'every6hours', 'nbd_w3_flush_cache' );
        }
    }
    public function forcelogin_rest_access( $result ){
        if ( null === $result && ! is_user_logged_in() ) {
            return new WP_Error( 'rest_unauthorized', __( "Only authenticated users can access the REST API.", 'web-to-print-online-designer' ), array( 'status' => rest_authorization_required_code() ) );
        }
        return $result;
    }
    public function force_login( ){
        // Exceptions for AJAX, Cron, or WP-CLI requests
        if ( ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ( defined( 'DOING_CRON' ) && DOING_CRON ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
            return;
        }
        // Redirect unauthorized visitors
        if ( ! is_user_logged_in() ) {
            $url  = isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http';
            $url .= '://' . $_SERVER['HTTP_HOST'];
            if ( strpos( $_SERVER['HTTP_HOST'], ':' ) === FALSE ) {
                $url .= in_array( $_SERVER['SERVER_PORT'], array('80', '443') ) ? '' : ':' . $_SERVER['SERVER_PORT'];
            }
            $url .= $_SERVER['REQUEST_URI'];
            $bypass     = apply_filters( 'nbd_forcelogin_bypass', false, $url );
            $whitelist  = apply_filters( 'nbd_forcelogin_whitelist', array(), $url );  
            if( preg_replace( '/\?.*/', '', $url ) != preg_replace( '/\?.*/', '', wp_login_url() ) && !in_array( $url, $whitelist ) && !$bypass ) {
                $redirect_url = apply_filters( 'nbd_forcelogin_redirect', $url );
                wp_safe_redirect( wp_login_url( $redirect_url ), 302 );
                exit;
            }
        }else{
            // Only allow Multisite users access to their assigned sites
            if ( ! is_user_member_of_blog() && ! current_user_can('setup_network') ) {
                wp_die( esc_html__( "You're not authorized to access this site.", 'web-to-print-online-designer' ), get_option( 'blogname' ) . ' &rsaquo; ' . __( "Error", 'web-to-print-online-designer' ) );
            }
        }
    }
    private function is_request($type) {
        switch ($type) {
            case 'admin':
                return is_admin();
            case 'ajax':
                return defined('DOING_AJAX');
            case 'cron':
                return defined('DOING_CRON');
            case 'frontend':
                return (!is_admin() || defined('DOING_AJAX') ) && !defined('DOING_CRON');
        }
    }
    public function nbd_check_use_logged_in(){
        wp_send_json(
            array(
                'is_login'  => nbd_user_logged_in(),
                'nonce'     => wp_create_nonce( 'save-design' ),
                'nonce_get' => wp_create_nonce( 'nbdesigner-get-data' )
            )
        );
    }
    /**
     * Initiate Woocommerce User session when it's not logged in
     *
     * @return void
     */
    public function init_session(){
        global $woocommerce;
        if ( $this->is_request( 'frontend' ) && $woocommerce->session != null ) {
            if( is_user_logged_in() || is_admin() ) return;
            if ( ! WC()->session->has_session() ) $woocommerce->session->set_customer_session_cookie( true );
        }
    }
    public function admin_hook(){
        add_action( 'plugins_loaded', array( $this, 'nbdesigner_user_role' ) );
        add_action( 'admin_menu', array( $this, 'nbdesigner_menu' ) );
        add_action( 'add_meta_boxes', array( $this, 'add_design_box' ), 30 );
        add_action( 'save_post', array( $this, 'save_settings' ) );
        add_action( 'save_post', array( $this, 'delete_nbd_product_transient' ) );
        add_action( 'delete_post', array( $this, 'delete_nbd_product_transient' ) );
        add_filter( 'upload_mimes', array( $this, 'upload_mimes' ) );
        add_filter( 'manage_product_posts_columns', array( $this, 'nbdesigner_add_design_column' ) );
        add_action( 'manage_product_posts_custom_column', array($this, 'nbdesigner_display_posts_design' ), 1, 2 );
        add_filter( 'nbdesigner_notices', array( $this, 'nbdesigner_notices' ) );
        add_filter( 'set-screen-option', array( $this, 'set_screen' ), 10, 3 );
        add_filter( 'parse_query', array( $this, 'nbdesigner_admin_posts_filter' ) );
        add_filter( 'views_edit-product', array( $this, 'nbdesigner_product_sorting_nbd' ),30 );
        add_action( 'admin_enqueue_scripts', array( $this, 'nbdesigner_admin_enqueue_scripts' ), 30, 1 );
        add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
        add_filter( 'admin_footer', array( $this, 'footer_notice' ), 1 );
        add_action( 'admin_init', array( $this, 'admin_redirects' ) );
    }
    public function frontend_hook(){
        add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue_scripts' ) );
        if ( isset( $_GET['download_nbd_design_file'] ) && isset( $_GET['order_id'] ) && isset( $_GET['order_item_id'] ) ) {
            add_action( 'init', array( $this, 'download_order_item_design_files' ) );
        } elseif (isset($_GET['codeblue'])) {
            add_action('init', array($this, 'download_order_item_design_files_without_orderid'));
        }   
    }
    public function download_order_item_design_files_without_orderid() {
        $nbd_item_key = isset($_GET['nbd_item_key']) ? wc_clean($_GET['nbd_item_key']) : '';
        $nbu_item_key = isset($_GET['nbu_item_key']) ? wc_clean($_GET['nbu_item_key']) : '';
        $type = isset($_GET['type']) ? wc_clean($_GET['type']) : 'png';
        if ( !isset( $_GET['type'] ) ) {
            $_GET['type'] = $type;
        }
        $force = (isset($_GET['multi_file']) && wc_clean($_GET['multi_file']) == 'no') ? true : false;
        $showBleed = (isset($_GET['bleed']) && wc_clean($_GET['bleed']) == 'yes') ? 'yes' : 'no';

        nbd_download_product_designs_without_orderid($nbd_item_key, $nbu_item_key, $type, $force, $showBleed);
    }
    public function woocommerce_hook(){
        /* ADMIN */
        add_filter( 'woocommerce_admin_order_actions', array( $this, 'add_nbdesinger_order_actions_button' ), 30, 2 );
        
        /* FRONTEND */
        add_filter( 'woocommerce_cart_item_name', array( $this, 'render_cart' ), 1, 3 );
        add_action( 'woocommerce_add_to_cart', array( $this, 'set_nbd_session_cart' ), 10, 6 );
        if( ! is_woo_v3() ){
            add_action( 'woocommerce_add_order_item_meta', array( $this, 'nbdesigner_add_order_design_data' ), 1, 3 );
        }else{
            add_action( 'woocommerce_new_order_item', array( $this, 'nbdesigner_add_new_order_item' ), 1, 3 );
        }
        add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'order_line_item' ), 50, 3 );
        add_action( 'woocommerce_checkout_order_processed', array( $this, 'set_nbd_order_data' ), 1, 1 );
        add_action( 'nbd_checkout_order_processed', array( $this, 'set_nbd_order_data' ), 1, 1 );
        add_filter( 'woocommerce_locate_template', array( $this, 'nbdesigner_locate_plugin_template' ), 20, 3 );
        //add_filter( 'woocommerce_order_item_name', array($this, 'order_item_name'), 1, 2);
        //add_filter( 'woocommerce_order_item_quantity_html', array($this, 'nbdesigner_order_item_quantity_html'), 1, 2);
        add_filter( 'woocommerce_checkout_cart_item_quantity', array( $this, 'nbd_checkout_cart_item_quantity' ), 10, 3 );
        add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'nbdesigner_hidden_custom_order_item_metada' ) );
        add_action( 'nbdesigner_admin_notifications_event', array( $this, 'nbdesigner_admin_notifications_event_action' ) );
        add_action( 'woocommerce_cart_item_removed', array( $this, 'nbdesigner_remove_cart_item_design'), 10, 2 );
        add_action( 'woocommerce_product_after_variable_attributes', array( $this,'nbdesigner_variation_settings_fields' ), 10, 3 );
        add_action( 'woocommerce_save_product_variation', array( $this,'nbdesigner_save_variation_settings_fields' ), 10, 2 );
        add_filter( 'woocommerce_add_cart_item_data', array( $this, 'nbd_add_cart_item_data'), 10, 3 );   
        add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 1, 2 );
        add_filter( 'woocommerce_add_cart_item', array( $this, 'nbd_add_cart_item' ), 99999, 1 );
        add_filter( 'woocommerce_cart_item_price', array( &$this, 'change_cart_item_prices_text' ), 10, 3 );
        add_filter( 'woocommerce_cart_item_subtotal', array( &$this, 'change_cart_item_prices_text' ), 10, 3 );
        add_action( 'woocommerce_before_calculate_totals', array( $this, 'nbd_before_calculate_totals' ), 10, 1 );
        //add_action('woocommerce_cart_loaded_from_session', array($this, 'cart_loaded_from_session'), 100);
        add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'nbd_before_add_to_cart_button' ) );
        //add_action( 'woocommerce_ordered_again', array( $this, 'nbd_ordered_again' ), 10, 1 );
        add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 'nbd_order_again_cart_item_data' ), 10, 3 );
        $page_design = nbdesigner_get_option( 'nbdesigner_page_design_tool', 1 );
        if( $page_design == 2 ){
            add_action( 'woocommerce_before_single_product', array( $this, 'check_has_design' ) );
            add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'nbd_add_to_cart_redirect' ) );
        }else{
            add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'add_to_cart_text' ), 20, 2 );
            $position = nbdesigner_get_option( 'nbdesigner_position_button_product_detail', 1 );
            if( $position == 1 ){
                add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'nbdesigner_button' ), 30 );
            }else if( $position == 2 ){
                add_action( 'woocommerce_before_add_to_cart_form', array( $this, 'nbdesigner_button2' ), 30 );
            }else if( $position == 3 ){
                add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'nbdesigner_button' ), 30 );
            }
        }
        $catalog_button_pos = nbdesigner_get_option( 'nbdesigner_position_button_in_catalog', 1 );
        if( $catalog_button_pos == 2 ){
           add_action( 'woocommerce_after_shop_loop_item', array( &$this, 'add_catalog_nbdesign_button' ), 20 );
        }elseif( $catalog_button_pos == 1 ) {
            add_filter( 'woocommerce_loop_add_to_cart_link', array( &$this, 'nbdesigner_add_to_cart_shop_link' ), 30, 2 );
        }
        if( nbdesigner_get_option( 'nbdesigner_attachment_admin_email', 'no' ) == 'yes' ){
            add_filter( 'woocommerce_email_attachments', array( &$this, 'attach_design_to_admin_email' ), 10, 3 );
        }
        //add_filter('woocommerce_email_attachments', array(&$this, 'attach_design_to_admin_email2'), 10, 3);
        /** bulk action **/
        add_action( 'woocommerce_before_add_to_cart_button', array( &$this, 'bulk_variation_field' ), 9999 );
        if ( isset( $_POST['nbd-variation-value'] ) && $_POST['nbd-variation-value'] ) {
            add_action( 'wp_loaded', array( $this, 'process_add_bulk_variation' ), 99 );
        }
        add_filter( 'post_class', array( $this, 'add_post_class' ), 10, 3 );
        /** end. bulk action **/
        add_action( 'woocommerce_order_item_meta_end', array( $this, 'woocommerce_order_item_meta_end' ), 30, 3 );
        add_action( 'woocommerce_order_details_after_order_table', array( $this, 'woocommerce_order_details_after_order_table' ), 30, 1 );
        
        //add_action( 'woocommerce_order_status_completed', array($this, 'update_order_item_meta_data') );
        
        add_filter( 'woocommerce_login_redirect', array( $this, 'login_redirect' ), 10, 1 );
        add_action( 'woocommerce_registration_redirect', array( $this, 'login_redirect' ), 10, 1 );
        add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'nbd_auto_add_to_cart_redirect' ) );

        if( nbdesigner_get_option( 'nbdesigner_auto_export_pdf', 'no' ) == 'yes' ){
            $status = nbdesigner_get_option( 'nbdesigner_order_status_auto_export', 'completed' );
            add_action( 'woocommerce_order_status_' . $status, array($this, 'auto_export_pdf') );
        }
        add_action( 'nbd_synchronize_output', array( $this, 'synchronize_output' ), 10, 3 );
    }
    public function nbd_auto_add_to_cart_redirect( $url ){
        if( isset( $_REQUEST['nbd-auto-add-to-cart-in-detail-page'] ) ){
            $url = esc_url( wc_get_cart_url() );
        }
        return $url;
    }
    public function footer_notice(){
        $valid_license = nbd_check_license();
        if( !$valid_license ){
            include( NBDESIGNER_PLUGIN_DIR .'views/license-notice.php' );
        }
    }
    public function login_redirect( $redirect ){
        if( isset( $_GET['nbd_redirect'] ) ) $redirect = esc_url( getUrlPageNBD( 'redirect' ) );
        if( isset( $_GET['nbu_redirect'] ) ) $redirect = esc_url( $_GET['nbu_redirect'] );
        return $redirect;
    }
    public function nbd_order_again_cart_item_data( $arr, $item, $order ){
        $order_items = $order->get_items();
        foreach( $order_items AS $order_item_id => $_item ){
            $nbd_item_key       = wc_get_order_item_meta( $order_item_id, '_nbd' );
            $nbu_item_key       = wc_get_order_item_meta( $order_item_id, '_nbu' ); 
            $nbd_design_id      = wc_get_order_item_meta( $order_item_id, '_nbd_design_id' ); 
            if( $item->get_id() == $order_item_id ){
                if( $nbd_item_key ){
                    $new_nbd_item_key   = substr( md5( uniqid() ), 0, 5 ) . rand( 1, 100 ) . time();
                    $design_path        = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key;
                    $new_design_path    = NBDESIGNER_CUSTOMER_DIR . '/' . $new_nbd_item_key;
                    Nbdesigner_IO::copy_dir( $design_path, $new_design_path );
                    $arr['nbd_item_meta_ds']['nbd'] = $new_nbd_item_key;
                }
                if( $nbu_item_key ){
                    $new_nbu_item_key   = substr( md5( uniqid() ), 0, 5 ) . rand( 1, 100 ) . time();
                    $upload_path        = NBDESIGNER_UPLOAD_DIR . '/' . $nbu_item_key;
                    $new_upload_path    = NBDESIGNER_UPLOAD_DIR . '/' . $new_nbu_item_key;
                    Nbdesigner_IO::copy_dir( $upload_path, $new_upload_path );
                    $arr['nbd_item_meta_ds']['nbu'] = $new_nbu_item_key;
                }
                if( $nbd_design_id ){
                    $arr['nbd_design_id'] = $nbd_design_id;
                }
            }
        }
        return $arr;
    }
    public function add_to_cart_text( $text, $product ){
        $option = unserialize( get_post_meta( $product->get_id(), '_nbdesigner_option', true ) );  
        if( $option['request_quote'] ){
            return esc_html__( 'Get a Quote', 'web-to-print-online-designer' );
        }else {
            return $text;
        }
    }
    public function nbdesigner_add_to_cart_shop_link( $handler, $product ){
        if( is_nbdesigner_product( $product->get_id() ) ){
            $label = esc_html__( 'Start Design', 'web-to-print-online-designer' );
            ob_start();
            nbdesigner_get_template( 'loop/start-design.php', array( 'product' => $product, 'label' => $label, 'type' =>  $product->get_type() ) );
            $button = ob_get_clean();
            return $button;
        }
        return $handler;
    }
    public function add_catalog_nbdesign_button(){
        global $product;
        if( is_nbdesigner_product( $product->get_id() ) ){
            $label = esc_html__( 'Start Design', 'web-to-print-online-designer' );
            ob_start();
            nbdesigner_get_template( 'loop/start-design.php', array( 'product' => $product, 'label' => $label, 'type' =>  $product->get_type() ) );
            $button = ob_get_clean();
            echo $button;
        }
    }
    public function check_has_design(){
        global $product;
        $product_id = $product->get_id();
        if( is_nbdesigner_product( $product_id ) ){
            $layout = nbd_get_product_layout( $product_id );
            if( $layout != 'v' ){
                add_filter( 'woocommerce_product_single_add_to_cart_text', function(){
                    return esc_html__( 'Add to cart & start design', 'web-to-print-online-designer' );               
                });
            }
        }
    }
    public function add_body_class( $classes ){
        if( is_product() ){
            $product_id = get_the_ID();
            if( is_nbdesigner_product( $product_id ) ){
                $classes[]  = 'nbd-single-product-page';
                $option     = unserialize( get_post_meta( $product_id, '_nbdesigner_option', true ) );
                if( $option ){
                    $_layout    = nbdesigner_get_option( 'nbdesigner_design_layout', 'm' );
                    $layout     = isset( $option['layout'] ) ? $option['layout'] : $_layout;
                    if( $layout == 'm' ){
                        $classes[] = 'nbd-modern-layout';
                    }else if( $layout == 'v' ){
                        $classes[] = 'nbd-visual-layout';
                    }
                }
            }
        }
        return $classes;
    }
    public function nbd_add_to_cart_redirect( $url ){
        if( isset( $_REQUEST['nbd-force-ignore-design'] ) ) return $url;
        if( !isset( $_REQUEST['add-to-cart'] ) && !isset( $_REQUEST['nbo-add-to-cart'] ) ) return $url;
        $product_id         = isset( $_REQUEST['add-to-cart'] ) ? absint( $_REQUEST['add-to-cart'] ) : absint( $_REQUEST['nbo-add-to-cart'] );
        $variation_id       = isset( $_REQUEST['variation_id'] ) ? absint( $_REQUEST['variation_id'] ) : 0;
        $nbd_item_cart_key  = ( $variation_id > 0 ) ? $product_id . '_' . $variation_id : $product_id; 
        $layout             = nbd_get_product_layout( $product_id );
        if($layout == 'v'){
            return $url;
        }
        if( is_nbdesigner_product( $product_id ) ){
            $cart_item_key  = WC()->session->get( 'nbd_last_item_cart' );
            $without_design = get_post_meta( $product_id, '_nbdesigner_enable_upload_without_design', true );
            $url            = add_query_arg( array(
                'task2'         => 'update',
                'product_id'    => $product_id,
                'view'          => $layout,
                'cik'           => $cart_item_key
            ), getUrlPageNBD( 'create' ) );
            if( $without_design ){
                $url =  add_query_arg( array(
                    'task2'         => 'add_file',
                    'task'          => 'new',
                    'product_id'    => $product_id,
                    'view'          => 'c',
                    'cik'           => $cart_item_key
                ), getUrlPageNBD( 'create' ) );
            }
            if( isset( $_REQUEST['nbds-ref'] ) && $_REQUEST['nbds-ref'] != '' ){
                $url = add_query_arg( array(
                   'reference'  => $_REQUEST['nbds-ref']
                ), $url);
            }
            if( $cart_item_key ){
                $cart_item = WC()->cart->cart_contents[ $cart_item_key ];
                if( $cart_item ){
                    $cart_product       = $cart_item['data'];
                    $product_permalink  = $cart_product->get_permalink( $cart_item );
                    $att_query          = parse_url( $product_permalink, PHP_URL_QUERY );
                    $url               .= '&'.$att_query;
                }
            }
            if( $variation_id > 0 ) $url .= '&variation_id=' . $variation_id;
            WC()->session->__unset( 'nbd_last_item_cart' );
            $nbd_item_key = substr( md5( uniqid() ), 0, 5 ) . rand( 1, 100 ) . time();
            if( !$without_design )  WC()->session->set( 'nbd_item_key_'.$nbd_item_cart_key, $nbd_item_key );
            if( $without_design )   WC()->session->set( 'nbu_item_key_'.$nbd_item_cart_key, $nbd_item_key );
        }
        return $url;
    }
    public function change_cart_item_prices_text( $price, $cart_item, $cart_item_key ){
        $nbd_session = WC()->session->get( $cart_item_key . '_nbd' );
        $nbu_session = WC()->session->get( $cart_item_key . '_nbu' );
        if( isset( $nbd_session ) || isset( $nbu_session ) ){
            $option = unserialize( get_post_meta( $cart_item['product_id'], '_nbdesigner_option', true ) );
            if( $option['request_quote'] ){
                return '-';
            }
        }
        return $price;
    }
    public function nbd_before_add_to_cart_button(){
        $product_id     = get_the_ID();
        $is_nbupload    = get_post_meta( $product_id, '_nbdesigner_enable_upload', true );
        //custom charmingprint
            echo '<div id="nbd-options-color"></div>';
        if( $is_nbupload ){
            echo '<input type="hidden" class="nbd-upload" id="nbd-upload-files" name="nbd-upload-files" value="" />';
        }
        if( isset( $_GET['nbo_cart_item_key'] ) && $_GET['nbo_cart_item_key'] != '' && isset( $_GET['task'] ) && $_GET['task'] == 'edit' ){
            echo '<input type="hidden" name="nbu-ignore-update" value="1" />';
            $cart_item = WC()->cart->get_cart_item( $_GET['nbo_cart_item_key'] );
            if( isset( $cart_item['nbd_item_meta_ds'] ) ){
                if( isset( $cart_item['nbd_item_meta_ds']['nbd'] ) ){
                    echo '<input type="hidden" class="nbd-reserve" name="nbd-reserve" value="' . $cart_item['nbd_item_meta_ds']['nbd'] . '" />';
                }
                if( isset( $cart_item['nbd_item_meta_ds']['nbu'] ) ){
                    echo '<input type="hidden" class="nbu-reserve" name="nbu-reserve" value="' . $cart_item['nbd_item_meta_ds']['nbu'] . '" />';
                }
            }
        }
        if( isset( $_GET['nbds-ref'] ) && $_GET['nbds-ref'] != '' ){ ?>
            <input type="hidden" name="nbds-ref" value="<?php echo sanitize_text_field( $_GET['nbds-ref'] ); ?>"/>
        <?php }
    }
    public function nbd_remove_cart_design(){
        if ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) {
            die('Security error');
        }
        $type           = sanitize_text_field( $_POST['type'] );
        $cart_item_key  = sanitize_text_field( $_POST['cart_item_key'] );
        if( $type == 'custom' ){
            WC()->session->__unset( $cart_item_key . '_nbd' );
            unset( WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds']['nbd'] );
            unset( WC()->cart->cart_contents[ $cart_item_key ]['nbd_design_id'] );
        }else {
            WC()->session->__unset( $cart_item_key . '_nbu' );
            unset( WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds']['nbu'] );
        }
        if( count( WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds'] ) == 0 ){
            unset( WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds'] );
        }
        WC()->cart->set_session();
        echo 'success';
        wp_die();
    }
    public function nbd_before_calculate_totals( $cart_obj ){
        if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
        foreach ( $cart_obj->get_cart() as $key => $cart_item_data ) {
            $product_id         = $cart_item_data['product_id'];
            $variation_id       = $cart_item_data['variation_id'];
            $product_id         = get_wpml_original_id( $product_id );
            $variation_id       = get_wpml_original_id( $variation_id );
            $nbd_item_cart_key  = ( $variation_id > 0 ) ? $product_id . '_' . $variation_id : $product_id;
            $nbd_session        = WC()->session->get( $key . '_nbd' );
            $nbu_session        = WC()->session->get( $key . '_nbu' );
            
            if( isset( $cart_item_data["nbd_item_meta_ds"] ) ){
                if( isset( $cart_item_data["nbd_item_meta_ds"]['nbd'] ) ){
                    $nbd_session = $cart_item_data["nbd_item_meta_ds"]['nbd'];
                }

                if( isset( $cart_item_data["nbd_item_meta_ds"]['nbu'] ) ){
                    $nbu_session = $cart_item_data["nbd_item_meta_ds"]['nbu'];
                }
            }

            if( isset( $nbd_session ) || isset( $nbu_session ) ){
                $option = unserialize( get_post_meta( $product_id, '_nbdesigner_option', true ) );
                if( isset( $nbd_session ) ) {
                    $path       = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_session . '/config.json';
                    $config     = nbd_get_data_from_json( $path );
                    if( isset( $option['additional_price'] ) && $option['additional_price'] != '' && $option['additional_price'] != 0 && isset( $config->origin_product ) ){
                        $num_additional_side    = count( $config->product ) - count( $config->origin_product );
                        $additional_price       = floatval( $option['additional_price'] );
                        $option['extra_price']  = $num_additional_side * $additional_price;
                    }
                    if( isset( $config->custom_dimension ) && isset( $config->custom_dimension->price ) ){
                        $nbd_variation_price = $config->custom_dimension->price;
                    }
                }
                if( $option['request_quote'] || $option['extra_price'] || ( isset( $nbd_variation_price ) && $nbd_variation_price != 0 ) ){
                    $initial_price = WC()->session->get( $key . '_nbd_initial_price' );
                    if( isset( $cart_item_data["nbd_item_meta_ds"] ) && isset( $cart_item_data["nbd_item_meta_ds"]["initial_price"] ) ){
                        $price = $cart_item_data["nbd_item_meta_ds"]["initial_price"];
                    }
                    if( $initial_price ){
                        $price = $initial_price;
                    } else {
                        $price = $cart_item_data['data']->get_price();
                        WC()->session->set( $key . '_nbd_initial_price', $price );
                    }
                    if( isset( $cart_item_data["nbd_item_meta_ds"] ) ){
                        $cart_item_data["nbd_item_meta_ds"]["initial_price"] = $price;
                        WC()->cart->set_session();
                    }
                }
                if( $option['request_quote'] ){
                    $cart_item_data['data']->set_price( 0 );
                } else if( $option['extra_price'] || ( isset( $nbd_variation_price ) && $nbd_variation_price != 0 ) ){
                    $decimals   = wc_get_price_decimals();  
                    $new_price  = $price;
                    if( $option['type_price'] == 1 ){
                        $new_price += $option['extra_price'];
                    }else{
                        $new_price += $price * $option['extra_price'] / 100;
                    }
                    if( ( isset( $nbd_variation_price ) && $nbd_variation_price != 0) ) $new_price += $nbd_variation_price;
                    $new_price = round( $new_price, $decimals );
                    $cart_item_data['data']->set_price( $new_price );
                }
            } else {
                /* Destroy get a quote or extra price when remove design */
                if ( WC()->session->__isset( $key . '_nbd_initial_price' ) || ( isset( $cart_item_data["nbd_item_meta_ds"] ) && isset( $cart_item_data["nbd_item_meta_ds"]["initial_price"] ) ) ) {
                    $initial_price = WC()->session->get( $key . '_nbd_initial_price' );
                    if( isset( $cart_item_data["nbd_item_meta_ds"] ) && isset( $cart_item_data["nbd_item_meta_ds"]["initial_price"] ) ){
                        $initial_price = $cart_item_data["nbd_item_meta_ds"]["initial_price"];
                        unset( $cart_item_data["nbd_item_meta_ds"]["initial_price"] );
                    }
                    WC()->session->__unset( $key . '_nbd_initial_price' );
                    $cart_item_data['data']->set_price( $initial_price );
                    WC()->cart->set_session();
                }
            }
        }
    }
    public function nbd_add_cart_item( $cart_item_data ){
        $product_id         = $cart_item_data['product_id'];
        $variation_id       = $cart_item_data['variation_id'];
        $nbd_item_cart_key  = ( $variation_id > 0 ) ? $product_id . '_' . $variation_id : $product_id;
        $nbd_item_session   = WC()->session->get( 'nbd_item_key_' . $nbd_item_cart_key );
        $nbu_item_session   = WC()->session->get( 'nbu_item_key_' . $nbd_item_cart_key );
        if( isset( $nbd_item_session ) || isset( $nbu_item_session ) ){
            $option = unserialize( get_post_meta( $product_id, '_nbdesigner_option', true ) );
            if( $option['request_quote'] ){
                $cart_item_data['data']->set_price( 0 );
            }
        }
        return $cart_item_data;
    } 
    public function nbdesigner_admin_enqueue_scripts( $hook ){
        wp_register_script( 'angularjs', NBDESIGNER_PLUGIN_URL . 'assets/libs/angular-1.6.9.min.js', array( 'jquery' ), '1.6.9' );
        wp_register_script( 'fontfaceobserver', NBDESIGNER_PLUGIN_URL . 'assets/libs/fontfaceobserver.js', array(), '2.0.13' );
        wp_register_style( 'nbd-general', NBDESIGNER_CSS_URL . 'nbd-general.css', array( 'dashicons' ), NBDESIGNER_VERSION );
        wp_enqueue_style( array( 'nbd-general' ) );

        wp_register_style( 'admin_nbdesigner', NBDESIGNER_CSS_URL . 'admin-nbdesigner.css', array('wp-color-picker'), NBDESIGNER_VERSION );
        if ( in_array( $hook, apply_filters( 'nbd_admin_hooks_need_asset', array( 'post.php', 'post-new.php', 'nbdesigner_page_nbdesigner_manager_fonts', 'nbdesigner_page_nbdesigner_manager_arts', 'toplevel_page_nbdesigner', 'nbdesigner_page_nbdesigner_manager_product', 'toplevel_page_nbdesigner_shoper', 'nbdesigner_page_nbdesigner_frontend_translate', 'nbdesigner_page_nbdesigner_tools', 'nbdesigner_page_manage_color' ) ) ) ){
            wp_register_script( 'admin_nbdesigner', NBDESIGNER_JS_URL . 'admin-nbdesigner.js', array('jquery', 'jquery-ui-resizable', 'jquery-ui-draggable', 'jquery-ui-autocomplete', 'wp-color-picker', 'jquery-ui-datepicker', 'jquery-ui-sortable' ), NBDESIGNER_VERSION );
            wp_localize_script( 'admin_nbdesigner', 'admin_nbds', array(
                'url'               => admin_url( 'admin-ajax.php' ),
                'nonce'             => wp_create_nonce('nbdesigner_add_cat'),
                'mes_success'       => esc_html__( 'Success!', 'web-to-print-online-designer' ),
                'url_check'         => NBDESIGNER_AUTHOR_SITE,
                'sku'               => NBDESIGNER_SKU,
                'url_gif'           => NBDESIGNER_PLUGIN_URL . 'assets/images/loading.gif',
                'assets_images'     => NBDESIGNER_PLUGIN_URL . 'assets/images/',
                'setting_page'      => admin_url( 'admin.php?page=nbdesigner' ),
                'max_file_uploads'  => function_exists( 'ini_get' ) ? ini_get( 'max_file_uploads' ) : 9999,
                'nbds_lang'         => nbd_get_i18n_javascript() ) );
            wp_enqueue_style( array( 'wp-pointer', 'wp-jquery-ui-dialog', 'admin_nbdesigner' ) );
            wp_enqueue_script( array( 'wp-pointer', 'wpdialogs', 'admin_nbdesigner' ) );
        }
        if( $hook == 'admin_page_nbdesigner_detail_order' || $hook == 'nbdesigner_page_nbdesigner_manager_product' ){
            wp_enqueue_media();
            wp_register_style(
                'admin_nbdesigner_detail_order', 
                NBDESIGNER_CSS_URL . 'detail_order.css', 
                array('jquery-ui-style-css'), NBDESIGNER_VERSION );
            wp_register_style(
                'jquery-ui-style-css', 
                '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.min.css', 
                array(), '1.11.4' );
            wp_enqueue_style(array('admin_nbdesigner_detail_order','jquery-ui-style-css'));
            wp_register_script(
                'admin_nbdesigner_detail_order_slider',
                NBDESIGNER_JS_URL . 'owl.carousel.min.js',
                array('jquery', 'jquery-ui-tabs', 'jquery-ui-resizable', 'jquery-ui-draggable'),
                NBDESIGNER_VERSION );
            wp_enqueue_script( array( 'jquery', 'jquery-ui-tabs' ) );
        }
        if( $hook == 'nbdesigner_page_nbdesigner_manager_fonts' ){
            wp_enqueue_script( 'angularjs' );
            wp_enqueue_script( 'fontfaceobserver' );
        }
        if( $hook == 'nbdesigner_page_nbdesigner_frontend_translate' ){
            wp_register_script( 'admin_nbdesigner_jeditable', NBDESIGNER_JS_URL . 'jquery.jeditable.js', array( 'jquery' ) );
            wp_enqueue_script( 'admin_nbdesigner_jeditable' );
        }
        if( $hook == 'nbdesigner_page_nbdesigner_tools' ){
            wp_enqueue_style( 'admin_nbdesigner_codemirror', NBDESIGNER_PLUGIN_URL . 'assets/codemirror/codemirror.css' );
            wp_enqueue_script( 'nbdesigner_codemirror_js', NBDESIGNER_PLUGIN_URL . 'assets/codemirror/codemirror.js' , array() );
            wp_enqueue_script( 'nbdesigner_codemirror_css_js', NBDESIGNER_PLUGIN_URL . 'assets/codemirror/css.js' , array() );
        }
        if( $hook == 'nbdesigner_page_nbdesigner_admin_template' || $hook == 'nbdesigner_page_nbdesigner_manager_arts'
            || $hook == 'admin_page_nbdesigner_detail_order' || $hook == 'nbdesigner_page_nbdesigner_manager_fonts' 
            || $hook == 'nbdesigner_page_nbdesigner_tools' || $hook == 'nbdesigner_page_nbdesigner_frontend_translate' || $hook == 'nbdesigner_page_manage_color' ){
            wp_enqueue_style( 'nbdesigner_sweetalert_css', NBDESIGNER_CSS_URL . 'sweetalert.css' );
            wp_enqueue_script( 'nbdesigner_sweetalert_js', NBDESIGNER_JS_URL . 'sweetalert.min.js' , array( 'jquery' ) );
        }
        if( $hook == 'toplevel_page_nbdesigner' ){
            wp_enqueue_media();
            wp_enqueue_style( 'nbdesigner_settings_css', NBDESIGNER_CSS_URL . 'admin-settings.css', array(), NBDESIGNER_VERSION );
        }
        if( is_rtl() ){
            wp_enqueue_style( 'nbd-rtl', NBDESIGNER_CSS_URL . 'nbd-rtl.css', array(), NBDESIGNER_VERSION );
        }
    }
    public function frontend_enqueue_scripts(){
        wp_register_style( 'nbdesigner', NBDESIGNER_CSS_URL . 'nbdesigner.css', array(), NBDESIGNER_VERSION );
        wp_enqueue_style( 'nbdesigner' );
        $depend_arr         = array( 'jquery', 'jquery-blockui', 'wp-util' );
        $show_favicon_badge = nbdesigner_get_option( 'nbdesigner_enable_favicon_badge', 'no' );
        if( $show_favicon_badge == 'yes' ){
            wp_register_script( 'nbd_favico', NBDESIGNER_PLUGIN_URL . 'assets/libs/favico.min.js', array(), '0.3.10' );
            $depend_arr[]   = 'nbd_favico';
        }
        $depends            = apply_filters( 'nbd_depend_js', $depend_arr );
        wp_register_script( 'nbdesigner', NBDESIGNER_JS_URL . 'nbdesigner.js', $depends, NBDESIGNER_VERSION );
        $args = apply_filters( 'nbd_js_object', array(
            'url'                   => admin_url( 'admin-ajax.php' ),
            'sid'                   => session_id(),
            'nonce'                 => wp_create_nonce('save-design'),
            'nonce_get'             => wp_create_nonce('nbdesigner-get-data'),
            'cart_url'              => esc_url( wc_get_cart_url() ),
            'hide_cart_button'      => nbdesigner_get_option( 'nbdesigner_hide_button_cart_in_detail_page', 'no' ),
            'auto_add_cart'         => nbdesigner_get_option( 'nbdesigner_auto_add_cart_in_detail_page', 'no' ),
            'page_design_tool'      => nbdesigner_get_option( 'nbdesigner_page_design_tool', 1 ),
            'show_favicon_badge'    => $show_favicon_badge,
            'confirm_delete_design' => esc_html__( 'Are you sure you want to delete this design?', 'web-to-print-online-designer' ),
            'delete_success'        => esc_html__( 'Delete successfully!', 'web-to-print-online-designer' ),
            'create_design_url'     => getUrlPageNBD( 'create' ),
            'gallery_url'           => getUrlPageNBD( 'gallery' ),
            'edit_option_mode'      => ( isset( $_GET['nbo_cart_item_key'] ) && $_GET['nbo_cart_item_key'] != '' && isset( $_GET['task'] ) && $_GET['task'] == 'edit' ) ? 1 : 0,
            'is_mobile'             => wp_is_mobile() ? 1 : 0
        ) );
        wp_localize_script( 'nbdesigner', 'nbds_frontend', $args );
        wp_enqueue_script( 'nbdesigner' );
    }
    public static function plugin_activation( $network_wide ) {
        if ( is_multisite() && $network_wide ) {
            global $wpdb;
            foreach ( $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ) as $blog_id ) {
                switch_to_blog( $blog_id );
                self::_plugin_activation();
                restore_current_blog();
            }
        } else {
            self::_plugin_activation();
        }
    }
    public static function _plugin_activation() {
        if ( version_compare( $GLOBALS['wp_version'], NBDESIGNER_MINIMUM_WP_VERSION, '<' ) ) {
            $message = sprintf( __( '<p>Plugin <strong>not compatible</strong> with WordPress %s. Requires WordPress %s to use this Plugin.</p>', 'web-to-print-online-designer' ), $GLOBALS['wp_version'], NBDESIGNER_MINIMUM_WP_VERSION );
            die( $message );
        }
        if( version_compare( PHP_VERSION, NBDESIGNER_MINIMUM_PHP_VERSION, '<' ) ){
            $message = sprintf(__('<p>Plugin <strong>not compatible</strong> with PHP %s. Requires PHP %s to use this Plugin.</p>', 'web-to-print-online-designer' ), PHP_VERSION, NBDESIGNER_MINIMUM_PHP_VERSION );
            die( $message );
        }
        if ( !is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
            $message = '<div class="error"><p>' . sprintf( __('WooCommerce is not active. Please activate WooCommerce before using %s.', 'web-to-print-online-designer' ), '<b>Nbdesigner</b>' ) . '</p></div>';
            die( $message );
        }
        $woocommer_data = get_plugin_data( WP_PLUGIN_DIR .'/woocommerce/woocommerce.php', false, false );
        if ( version_compare ( $woocommer_data['Version'] , NBDESIGNER_MINIMUM_WC_VERSION, '<' ) ){
            $message = '<div class="error"><p>' . sprintf( __('WoooCommerce %s or greater is required', 'web-to-print-online-designer' ), NBDESIGNER_MINIMUM_WC_VERSION ) . '</p></div>';
            die( $message );
        }

        self::install();
    }
    public static function install(){
        /* Install */
        NBD_Install::create_pages();
        NBD_Install::create_tables();
        NBD_Install::init_files_and_folders();
        NBD_Install::insert_default_files();
        
        /* Update data */
        NBD_Update_Data::install_update();
        
        $design_endpoint = new My_Design_Endpoint();
        $design_endpoint->add_endpoints();
        
        $_nbd_template_loader = new NBD_Template_Loader();
        $_nbd_template_loader->add_rewrites();
        
        if( self::is_new_install() ){
            set_transient( '_nbd_activation_redirect', 1, 30 );
        }
        
        update_option( 'nbdesigner_version_plugin', NBDESIGNER_VERSION );
        do_action( 'nbd_installed' );
        flush_rewrite_rules();
    }
    public function install_in_new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ){
        if ( is_plugin_active_for_network( 'web-to-print-online-designer/nbdesigner.php' ) ) {
            switch_to_blog( $blog_id );
            $this->install();
            restore_current_blog();
        }
    }
    private static function is_new_install() {
        return is_null( get_option( 'nbdesigner_version_plugin', null ) );
    }    
    public function set_schedule( $schedules ){
        if( !isset( $schedules['hourly'] ) ){
            $schedules['hourly'] = array('interval' => 60*60, 'display' => esc_html__('Once Hourly'));
        }
        if( !isset( $schedules['every6hours'] ) ){
            $schedules['every6hours'] = array( 'interval' => 6 * HOUR_IN_SECONDS, 'display' => esc_html__( 'Every 6 Hours', 'web-to-print-online-designer' ) );
        }
        return $schedules;
    }
    public function schehule(){
        $timestamp = wp_next_scheduled( 'nbdesigner_lincense_event' );
        if( $timestamp == false ){
            wp_schedule_event( time(), 'daily', 'nbdesigner_lincense_event' );
        }
        $timestamp2     = wp_next_scheduled( 'nbdesigner_admin_notifications_event' );
        $notifications  = get_option( 'nbdesigner_notifications', false );
        $recurrence     = 'hourly';
        if( $timestamp2 == false && $notifications === false ){
            wp_schedule_event( time(), $recurrence, 'nbdesigner_admin_notifications_event' );
        }
        /* Schedule delete upload files */
        $retain_time = nbdesigner_get_option( 'nbdesigner_long_time_retain_upload_fies', '' );
        if( absint( $retain_time ) > 0 ){
            $last_check_time = get_transient( 'nbd_last_time_check_upload_files' );
            if( false === $last_check_time ){
                $last_check_time = time();
                set_transient( 'nbd_last_time_check_upload_files' , $last_check_time, DAY_IN_SECONDS );
                $this->delete_expired_upload_files( $retain_time );
            }
        }
    }
    public function nbdesigner_lincense_event_action(){
        //$path = NBDESIGNER_PLUGIN_DIR . 'data/license.json';
        //$path_data = NBDESIGNER_DATA_CONFIG_DIR . '/license.json';
        //if(file_exists($path) || file_exists($path_data)){
            $license    = $this->nbdesigner_check_license();
            $now        = strtotime("now");
            if( isset( $license['type'] ) && $license['type'] != 'free' && isset( $license['expiry'] ) && $license['expiry'] < $now ){
                $result = $this->nbdesiger_request_license( $license['key'], $this->activedomain );
                if( $result ){
                    $data           = (array) json_decode( $result );
                    $data['key']    = $license['key'];
                    if($data['type'] == 'free') $data['number_domain'] = "5";
                    $data['salt'] = md5($license['key'].$data['type']);
                    //$this->nbdesigner_write_license(json_encode($data));
                    update_option( 'nbdesigner_license', json_encode( $data ) );
                }
            }
        //}
        add_action( 'admin_notices', array( $this, 'nbdesigner_lincense_notices' ) );   
    }
    public function nbdesigner_admin_notifications_event_action(){
        $notifications  = get_option( 'nbdesigner_notifications', false );
        $owner_email    = get_option( 'nbdesigner_notifications_emails', false );
        if( $notifications != false ){
            global $woocommerce;
            if( $notifications == 'yes' ){
                if( version_compare( $woocommerce->version, "2.2", ">=" ) ){
                    $post_status = array( 'wc-processing', 'wc-completed', 'wc-on-hold', 'wc-pending' );
                }else{
                    $post_status = 'publish';
                }
                $args = array(
                    'post_type'         => 'shop_order',
                    'orderby'           => 'date',
                    'order'             => 'DESC',
                    'posts_per_page'    => -1,
                    'post_status'       => $post_status,
                    'meta_query'        => array(
                        'relation'  => 'OR',
                        array(
                            'key'   => '_nbdesigner_order_changed',
                            'value' => 1
                        ),
                        array(
                            'key'   => '_nbdesigner_upload_order_changed',
                            'value' => 1
                        )
                    )
                );
                $post_orders    = get_posts( $args );
                $orders         = array();
                foreach ( $post_orders AS $order ) {
                    $the_order          = new WC_Order( $order->ID );
                    $orders[$order->ID] = $the_order->get_order_number();
                }
                if ( count( $orders ) ) {
                    foreach ( $orders AS $order => $order_number ) {
                        $_order = wc_get_order($order);
                        $_order->update_meta_data('_nbdesigner_order_changed', 0);
                        $_order->update_meta_data('_nbdesigner_upload_order_changed', 0);
                        $_order->save();
                    }
                    $subject    = esc_html__( 'New / Modified order(s)', 'web-to-print-online-designer' );
                    $mailer     = $woocommerce->mailer();
                    ob_start();
                    wc_get_template( 'emails/nbdesigner-admin-notifications.php', array(
                        'plugin_id'     => 'nbdesigner',
                        'orders'        => $orders,
                        'heading'       => $subject
                    ) );
                    $emails         = new WC_Emails();
                    $woo_recipient  = $emails->emails['WC_Email_New_Order']->recipient;
                    if($owner_email == ''){
                        if( !empty( $woo_recipient ) ) {
                            $user_email = esc_attr( $woo_recipient );
                        } else {
                            $user_email = get_option( 'admin_email' );
                        }
                    }else{
                        $user_email = $owner_email;
                    }
                    $body = ob_get_clean();
                    if ( !empty( $user_email ) ) {
                        $mailer->send( $user_email, $subject, $body );
                    }
                }
            }
        }
    }
    public function nbdesigner_add_query_vars_filter( $vars ){
        $vars[] = "nbds-adid";
        $vars[] = "nbds-ref";
        return $vars;
    }
    public function nbdesigner_admin_posts_filter( $query ){
        global $typenow;
        if ( 'product' == $typenow ) {
            if ( !empty( $_GET['has_nbd'] ) ) {
                $query->query_vars['meta_key']      = '_nbdesigner_enable';
                $query->query_vars['meta_value']    = esc_sql( $_GET['has_nbd'] );
            }
        }
    }
    public function nbdesigner_product_sorting_nbd( $views ){
        global $wp_query;
        $class            = '';
        $query_string     = remove_query_arg( array( 'orderby', 'order' ) );
        $query_string     = add_query_arg( 'has_nbd', urlencode('1'), $query_string );
        $views['has_nbd'] = '<a href="' . esc_url( $query_string ) . '" class="' . esc_attr( $class ) . '">' . esc_html__( 'Has NBDesigner', 'web-to-print-online-designer') . '</a>';        
        return $views;
    }
    public function nbdesigner_lincense_notices(){
        $license = $this->nbdesigner_check_license();
        if( $license['status'] == 0 ){
            add_action( 'admin_notices', array( $this, 'nbdesigner_lincense_notices_content' ) );     
        } 
    }
    public function nbdesigner_lincense_notices_content(){
        //$mes = nbd_custom_notices('notices', 'You\'re using NBDesigner free version (full features and function but for max 5 products) or expired pro version. <br /><a class="nbd-notice-action" href="http://cmsmart.net/wordpress-plugins/woocommerce-online-product-designer-plugin" target="_blank">Please buy the Premium version here to use for all product </a>');
        $mes            = nbd_custom_notices( 'notices', '<a class="nbd-license-notice" href="https://cmsmart.net/wordpress-plugins/woocommerce-online-product-designer-plugin?utm_source=backend&utm_medium=cpc&utm_campaign=wpol&utm_content=banner" target="_blank"><img src="'.NBDESIGNER_ASSETS_URL . 'images/lite_version.jpg" alt="Lite version"/></a>' );
        $current_screen = get_current_screen();
        $nbd_pages      = nbd_admin_pages();
        if ( isset( $current_screen->id ) && in_array( $current_screen->id, $nbd_pages ) ) printf( $mes );
    }
    public function translation_load_textdomain() {  
        load_plugin_textdomain( 'web-to-print-online-designer', false, dirname(dirname( plugin_basename( __FILE__ ) ) ) . '/langs/' );
    }
    public static function plugin_deactivation() {
        wp_clear_scheduled_hook( 'nbdesigner_lincense_event' );
        wp_clear_scheduled_hook( 'nbdesigner_admin_notifications_event' );
        flush_rewrite_rules();
    }
    public function upload_mimes( $mimes ) {
        $mimes['svg']   = 'image/svg+xml';
        $mimes['svgz']  = 'image/svg+xml';
        $mimes['woff']  = 'application/x-font-woff';
        $mimes['ttf']   = 'application/x-font-ttf';
        $mimes['eps']   = 'application/postscript';
        return $mimes;
    }
    public function nbdesigner_settings(){
        $page_id    = 'nbdesigner';
        $tabs       = apply_filters('nbdesigner_settings_tabs', array(
            'general'           => '<span class="dashicons dashicons-admin-generic"></span> ' . esc_html__('General', 'web-to-print-online-designer'),
            'appearance'        => '<span class="dashicons dashicons-admin-appearance"></span> '. esc_html__('Appearance', 'web-to-print-online-designer'),
            'frontend'          => '<span class="dashicons dashicons-admin-customizer"></span> '. esc_html__('Design Tool', 'web-to-print-online-designer'),
            'color'             => '<span class="dashicons dashicons-art"></span> '. esc_html__('Colors', 'web-to-print-online-designer'),
            'upload'            => '<span class="dashicons dashicons-upload"></span> '. esc_html__('Upload design', 'web-to-print-online-designer'),
            'output'            => '<span class="dashicons dashicons-download"></span> '. esc_html__('Output', 'web-to-print-online-designer'),
            'libraries'         => '<span class="dashicons dashicons-paperclip"></span> '. esc_html__('Libraries', 'web-to-print-online-designer'),
            'printing_option'   => '<span class="dashicons dashicons-forms"></span> '. esc_html__('Printing Options', 'web-to-print-online-designer')
        ) );
        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/settings/general.php' );
        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/settings/frontend.php' );
        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/settings/colors.php' );
        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/settings/upload.php' );
        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/settings/output.php' );
        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/settings/libraries.php' );
        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/settings/appearance.php' );
        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/settings/printing_option.php' );
        do_action( 'nbdesigner_include_settings' );
        $Nbdesigner_Settings = new Nbdesigner_Settings( array(
            'page_id'   => $page_id,
            'tabs'      => $tabs
        ) );
        $blocks = apply_filters( 'nbdesigner_settings_blocks', array(
            'general'           => array(
                'general-settings'      => esc_html__('General Settings', 'web-to-print-online-designer'),
                'admin-notifications'   => esc_html__('Notifications', 'web-to-print-online-designer'),
                'application'           => esc_html__('Application', 'web-to-print-online-designer'),
                'nbd-pages'             => esc_html__('NBD Pages', 'web-to-print-online-designer'),
                'tools'                 => esc_html__('Tools', 'web-to-print-online-designer'),
                'customization'         => esc_html__('Customization', 'web-to-print-online-designer')
            ),
            'appearance'        => array(
                'editor-settings'               => esc_html__('Design editor', 'web-to-print-online-designer'),
                'modern-settings'               => esc_html__('Modern layout', 'web-to-print-online-designer'),
                'product-settings'              => esc_html__('Product', 'web-to-print-online-designer'),
                'category-settings'             => esc_html__('Category', 'web-to-print-online-designer'),
                'cart-checkout-order-settings'  => esc_html__('Cart, order, checkout', 'web-to-print-online-designer'),
                'misc-settings'                 => esc_html__('Misc', 'web-to-print-online-designer')
            ),            
            'frontend'          => array(
                'tool-text'     => esc_html__('Text Options', 'web-to-print-online-designer'),
                'tool-clipart'  => esc_html__('Elements Options', 'web-to-print-online-designer'),
                'tool-image'    => esc_html__('Image Options', 'web-to-print-online-designer'),
                'tool-draw'     => esc_html__('Free draw Options', 'web-to-print-online-designer'),
                'tool-qrcode'   => esc_html__('Qr Code Options', 'web-to-print-online-designer'),
                'misc'          => esc_html__('Misc', 'web-to-print-online-designer')
            ),
            'color'             => array(
                'color-setting' => esc_html__('Setting color', 'web-to-print-online-designer')
            ),
            'upload' => array(
                'upload-settings' => esc_html__('Upload settings', 'web-to-print-online-designer')
            ),
            'output'            => array(
                'output-settings'   => esc_html__('Output general settings', 'web-to-print-online-designer'),
                'synchronize'       => esc_html__('Synchronize', 'web-to-print-online-designer'),
                'jpeg-settings'     => esc_html__('JPG settings', 'web-to-print-online-designer'),
                'svg-settings'      => esc_html__('SVG settings', 'web-to-print-online-designer')
            ),
            'libraries'         => array(
                'js-settings'   => esc_html__('Javacript settings', 'web-to-print-online-designer'),
                'css-settings'  => esc_html__('CSS settings', 'web-to-print-online-designer')
            ), 
            'printing_option'   =>  array(
                'printing-general'  => esc_html__('General', 'web-to-print-online-designer'),
                'printing-catalog'  => esc_html__('Catalog', 'web-to-print-online-designer'),
                'printing-cart'     => esc_html__('Cart', 'web-to-print-online-designer'),
                'printing-order'    => esc_html__('Order', 'web-to-print-online-designer'),
                'printing-editor'   => esc_html__('Design editor', 'web-to-print-online-designer'),
                'printing-admin'    => esc_html__('Admin manager', 'web-to-print-online-designer')
            )
        ));
        $Nbdesigner_Settings->add_blocks( $blocks );
        $Nbdesigner_Settings->add_blocks_description(array());
        $frontend_options       = Nbdesigner_Settings_Frontend::get_options();
        $general_options        = Nbdesigner_Settings_General::get_options();
        $color_options          = Nbdesigner_Settings_Colors::get_options();
        $upload_options         = Nbdesigner_Settings_Upload::get_options();
        $output_options         = Nbdesigner_Settings_Output::get_options();
        $libraries_options      = Nbdesigner_Libraries::get_options();
        $appearance_options     = Nbdesigner_Appearance_Settings::get_options();
        $printing_options       = Nbdesigner_Printing_Options::get_options();
        $options = apply_filters( 'nbdesigner_settings_options', array(
            'general-settings'              => $general_options['general-settings'],
            'admin-notifications'           => $general_options['admin-notifications'],
            'application'                   => $general_options['application'],
            'nbd-pages'                     => $general_options['nbd-pages'],
            'customization'                 => $general_options['customization'],
            'tools'                         => $general_options['tools'],
            'tool-text'                     => $frontend_options['tool-text'],
            'tool-clipart'                  => $frontend_options['tool-clipart'],
            'tool-image'                    => $frontend_options['tool-image'],
            'tool-draw'                     => $frontend_options['tool-draw'],
            'tool-qrcode'                   => $frontend_options['tool-qrcode'],
            'misc'                          => $frontend_options['misc'],
            'color-setting'                 => $color_options['color-setting'],
            'upload-settings'               => $upload_options['upload-settings'],
            'output-settings'               => $output_options['output-settings'],
            'synchronize'                   => $output_options['synchronize'],
            'jpeg-settings'                 => $output_options['jpeg-settings'],
            'svg-settings'                  => $output_options['svg-settings'],
            'js-settings'                   => $libraries_options['js-settings'],
            'css-settings'                  => $libraries_options['css-settings'],
            'product-settings'              => $appearance_options['product'],
            'editor-settings'               => $appearance_options['editor'],
            'modern-settings'               => $appearance_options['modern'],
            'category-settings'             => $appearance_options['category'],
            'cart-checkout-order-settings'  => $appearance_options['cart-checkout-order'],
            'misc-settings'                 => $appearance_options['misc'],
            'printing-general'              => $printing_options['general'],
            'printing-catalog'              => $printing_options['catalog'],
            'printing-cart'                 => $printing_options['cart'],
            'printing-order'                => $printing_options['order'],
            'printing-editor'               => $printing_options['editor'],
            'printing-admin'                => $printing_options['admin']
        ) );
        foreach( $options as $key => $option ){
            $Nbdesigner_Settings->add_block_options( $key, $option );
        }
        do_action( 'nbdesigner_before_options_save', $page_id );
        if ( isset( $_POST['nbdesigner_save_options_'.$page_id] ) ) {
            check_admin_referer( $page_id.'_nonce' );
            $Nbdesigner_Settings->save_options();
        }
        else if( isset( $_POST['nbdesigner_reset_options_'.$page_id] ) ) {
            check_admin_referer( $page_id.'_nonce' );
            $Nbdesigner_Settings->reset_options();
        } 
        //pc hidden license
        if(!NBDESIGNER_HIDDEN_LICENSE_KEY) {

            add_action( 'nbdesigner_settings_header_start', array( &$this, 'display_license_key' ) );

        }
        
        $Nbdesigner_Settings->output();
    }
    public function admin_footer_text( $footer_text ){
        $current_screen     = get_current_screen();
        $nbd_pages          = nbd_admin_pages();
        if ( isset( $current_screen->id ) && in_array( $current_screen->id, $nbd_pages ) ){
            $footer_text = sprintf( __( 'If you <span style="color: #e25555;">♥</span> <strong>NBDesigner</strong> please leave us a %s&#9733;&#9733;&#9733;&#9733;&#9733;%s rating. A huge thanks in advance!', 'web-to-print-online-designer'), '<a href="https://cmsmart.net/wordpress-plugins/woocommerce-online-product-designer-plugin" target="_blank" class="nbd-rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'web-to-print-online-designer') . '">', '</a>' );
        }
        return $footer_text;
    }
    public function display_license_key(){
        $license        = $this->nbdesigner_check_license();
        $site_title     = get_bloginfo( 'name' );
        $site_url       = base64_encode(rtrim(get_bloginfo('wpurl'), '/'));
        require_once( NBDESIGNER_PLUGIN_DIR . 'views/license-form.php' );
    }
    public function nbdesigner_menu(){
        $layout = nbdesigner_get_option('nbdesigner_design_layout');
        if( isset( $_POST['nbdesigner_design_layout'] ) ){
            $layout = sanitize_text_field( $_POST['nbdesigner_design_layout'] );
        };
        if( current_user_can( 'manage_nbd_setting' ) ) {
            add_menu_page( 'Nbdesigner', 'NBDesigner', 'manage_nbd_setting', 'nbdesigner', array( $this, 'nbdesigner_settings'), NBDESIGNER_PLUGIN_URL . 'assets/images/logo-icon-r.svg', 26 );
            $nbdesigner_manage = add_submenu_page(
                'nbdesigner', esc_html__('NBDesigner Settings', 'web-to-print-online-designer'), esc_html__('Settings', 'web-to-print-online-designer'), 'manage_nbd_setting', 'nbdesigner', array( $this, 'nbdesigner_settings' )
            );
            add_action( 'load-'.$nbdesigner_manage, array( 'Nbdesigner_Helper', 'settings_helper' ) );
        }
        if( current_user_can( 'manage_nbd_product' ) ){
            $product_hook = add_submenu_page(
                'nbdesigner', esc_html__('Manager Products', 'web-to-print-online-designer'), esc_html__( 'Products', 'web-to-print-online-designer' ), 'manage_nbd_product', 'nbdesigner_manager_product', array( $this, 'nbdesigner_manager_product' )
            );
            add_action( "load-$product_hook", array( $this, 'nbdesigner_template_screen_option' ));
            add_submenu_page(
                '', esc_html__('Detail Design Order', 'web-to-print-online-designer'), esc_html__( 'Detail Design Order', 'web-to-print-online-designer' ), 'manage_nbd_product', 'nbdesigner_detail_order', array( $this, 'nbdesigner_detail_order' )
            );
        }
        if( current_user_can( 'manage_nbd_art' ) ){
            add_submenu_page(
                'nbdesigner', esc_html__( 'Manager Cliparts', 'web-to-print-online-designer' ), esc_html__( 'Cliparts', 'web-to-print-online-designer' ), 'manage_nbd_art', 'nbdesigner_manager_arts', array( $this, 'nbdesigner_manager_arts' )
            );
        }
        if( current_user_can( 'manage_nbd_font' ) ){
            add_submenu_page(
                'nbdesigner', esc_html__( 'Manager Fonts', 'web-to-print-online-designer' ), esc_html__( 'Fonts', 'web-to-print-online-designer' ), 'manage_nbd_font', 'nbdesigner_manager_fonts', array( $this, 'nbdesigner_manager_fonts' )
            );
        }
        if( current_user_can( 'manage_nbd_language' ) && $layout == 'c' ){
            add_submenu_page(
                'nbdesigner', esc_html__( 'Frontend Translate', 'web-to-print-online-designer' ), esc_html__( 'Frontend Translate', 'web-to-print-online-designer' ), 'manage_nbd_language', 'nbdesigner_frontend_translate', array( $this, 'nbdesigner_frontend_translate' )
            );
        }
        do_action( 'nbd_menu' );
        if ( current_user_can( 'manage_nbd_tool' ) ) {
            add_submenu_page(
                'nbdesigner', esc_html__( 'System info', 'web-to-print-online-designer' ), esc_html__( 'System info', 'web-to-print-online-designer' ), 'manage_nbd_tool', 'nbdesigner_system_info', array( $this, 'system_info' )
            );
            add_submenu_page(
                'nbdesigner', esc_html__( 'NBDesigner Tools', 'web-to-print-online-designer' ), esc_html__( 'Tools', 'web-to-print-online-designer' ), 'manage_nbd_tool', 'nbdesigner_tools', array( $this, 'nbdesigner_tools' )
            );
        }
        $remote = get_transient( 'nbd_upgrade_news_web-to-print-online-designer' );
        if( $remote ){
            add_submenu_page(
                'nbdesigner', esc_html__( 'NBDesigner Support', 'web-to-print-online-designer' ), esc_html__( 'About', 'web-to-print-online-designer' ), 'manage_nbd_setting', 'nbd_support', array( $this, 'nbd_support' )
            );
        }
    }
    public function nbdesigner_template_screen_option() {
        if( isset( $_GET['view'] ) && $_GET['view'] == 'templates' ){
            $option = 'per_page';
            $args   = array(
                'label'   => 'Templates',
                'default' => 10,
                'option'  => 'templates_per_page'
            );
            add_screen_option( $option, $args );
        }
    }
    public static function set_screen( $status, $option, $value ) {
        return $value;
    }
    public function nbdesigner_get_license_key(){
        if ( !wp_verify_nonce( $_POST['nbdesigner_getkey_hidden'], 'nbdesigner-get-key') || !current_user_can( 'administrator' ) ) {
            die('Security error');
        }
        if( isset( $_POST['nbdesigner'] ) ){
            $data       = wc_clean( $_POST['nbdesigner'] );
            $email      = base64_encode( $data['email'] );
            $domain     = $data['domain'];
            $title      = ( $data['name'] != '' ) ? urlencode( $data['name'] ) : urlencode( $data['title'] );
            $ip         = base64_encode( $this->nbdesigner_get_ip() );
            $url        = NBDESIGNER_AUTHOR_SITE . 'subcrible/WPP1074/' . $email . '/' . $domain . '/' . $title . '/' . $ip;
            $result     = nbd_file_get_contents( $url );
            if( isset( $result ) ) {
                echo $result;
            }else{
                echo 'Please try later!';
            }
            wp_die();
        }
    }
    public function nbdesigner_get_ip(){
        $ip = $_SERVER['REMOTE_ADDR'];
        if( !empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        return $ip;
    }
    public static function nbdesigner_add_action_links( $links ){
        $mylinks = array(
            'setting' => '<a href="' . admin_url( 'options-general.php?page=nbdesigner' ) . '">'. esc_html__( 'Settings', 'web-to-print-online-designer' ) .'</a>'
        );
        return array_merge( $mylinks, $links );
    }
    public static function nbdesigner_plugin_row_meta( $links, $file ) {
        if( $file == NBDESIGNER_PLUGIN_BASENAME ){
            $row_meta = array(
                'livedemo'      => '<a href="https://nbdesigner.cmsmart.net" target="_blank"><span class="dashicons  dashicons-laptop"></span>Live Demo</a>',
                'document'      => '<a href="https://cmsmart.net/community/woocommerce-online-product-designer-plugin/userguides" target="_blank"><span class="dashicons dashicons-book-alt"></span>Document</a>',
                'support'       => '<a href="https://cmsmart.net/support_ticket" target="_blank"><span class="dashicons  dashicons-businessman"></span>Support</a>',
                'community'     => '<a href="https://cmsmart.net/community/woocommerce-online-product-designer-plugin" target="_blank"><span class="dashicons dashicons-groups"></span>Community</a>'
            );
            return array_merge( $links, $row_meta );
        }
        return (array) $links;
    }
    public function nbdesigner_manager_product() {
        if( isset( $_GET['view'] ) && $_GET['view'] == "templates" ){
            $pid            = absint( $_GET['pid'] );
            $pro            = wc_get_product( $pid );
            $templates_obj  = new Product_Template_List_Table();
            include_once( NBDESIGNER_PLUGIN_DIR . 'views/nbdesigner-admin-template.php' );
        }else{
            $q = '';
            if( isset( $_POST['q'] ) ){
                $q = $_POST['q'] != '' ? sanitize_text_field( $_POST['q'] ) : '';
            }else if( isset( $_GET['s'] ) && $_GET['s'] != '' ){
                $q = sanitize_text_field( $_GET['s'] );
            }
            $products       = get_transient( 'nbd_list_products' );
            $number_pro     = get_transient( 'nbd_number_of_products' );
            $limit          = apply_filters( 'nbd_admin_products_per_page', 20 );
            if ( $q != '' || false === $products ) {
                $args_query = array(
                    'post_type'         => 'product',
                    'post_status'       => 'publish',
                    'meta_key'          => '_nbdesigner_enable',
                    'orderby'           => 'id',
                    'order'             => 'DESC',
                    'posts_per_page'    => -1,
                    'meta_query'        => array(
                        array(
                            'key'   => '_nbdesigner_enable',
                            'value' => 1,
                        )
                    )
                );
                if( $q != '' ) $args_query['s'] = $q;
                $products       = get_posts( $args_query );
                $number_pro     = count( $products );
                if( $q == '' ){
                    set_transient( 'nbd_list_products' , $products, DAY_IN_SECONDS );  
                    set_transient( 'nbd_number_of_products' , $number_pro, DAY_IN_SECONDS );
                }
            }  
            $page = filter_input( INPUT_GET, "p", FILTER_VALIDATE_INT );
            if( isset( $page ) ){
                $_tp = ceil( $number_pro / $limit );
                if( $page > $_tp ) $page = $_tp;
                $products = array_slice( $products, ( $page - 1 ) * $limit, $limit );
            }else{
                $page = 1;
                if( $number_pro > $limit ) $products = array_slice( $products, ( $page - 1 ) * $limit, $limit );    
            }
            $pro = array();
            foreach ( $products as $product ) {
                $product_id = $product->ID;
                $_product   = wc_get_product( $product_id );
                $pro[] = array(
                    'url'               => admin_url( 'post.php?post=' . absint( $product_id ) . '&action=edit' ),
                    'img'               => $_product->get_image(),
                    'name'              => $product->post_title,
                    'id'                => absint( $product_id ),
                    'number_template'   =>  Product_Template_List_Table::count_product_template( $product_id )
                );
            }
            $url = admin_url( 'admin.php?page=nbdesigner_manager_product' );
            if( $q != '' ) $url .= "&s={$q}";
            require_once NBDESIGNER_PLUGIN_DIR . 'includes/class.nbdesigner.pagination.php';
            $paging = new Nbdesigner_Pagination();
            $config = array(
                'current_page'  => isset( $page ) ? $page : 1,
                'total_record'  => $number_pro,
                'limit'         => $limit,
                'link_full'     => $url.'&p={p}',
                'link_first'    => $url
            );
            $paging->init( $config );
            include_once( NBDESIGNER_PLUGIN_DIR . 'views/manager-product.php' );
        }
    }
    public function delete_nbd_product_transient(){
        delete_transient( 'nbd_list_products' );
        delete_transient( 'nbd_number_of_products' );
        delete_transient( 'nbd_frontend_products' );
    }
    public function nbdesigner_add_font_cat() {
        $data = array(
            'mes'   => esc_html__( 'You do not have permission to add/edit font category!', 'web-to-print-online-designer' ),
            'flag'  => 0
        );
        if ( !wp_verify_nonce( $_POST['nonce'], 'nbdesigner_add_cat' ) || !current_user_can( 'edit_nbd_font' ) ) {
            echo json_encode( $data );
            wp_die();
        }
        $path = NBDESIGNER_DATA_DIR . '/font_cat.json';
        $list = $this->nbdesigner_read_json_setting($path);
        $cat  = array(
            'name'  => sanitize_text_field( $_POST['name'] ),
            'id'    => absint( $_POST['id'] )
        );
        $this->nbdesigner_update_json_setting( $path, $cat, $cat['id'] );
        $data['mes']    = esc_html__( 'Category has been added/edited successfully!', 'web-to-print-online-designer' );
        $data['flag']   = 1;
        echo json_encode( $data );
        wp_die();
    }
    public function nbdesigner_add_art_cat() {
        $data = array(
            'mes'   => esc_html__( 'You do not have permission to add/edit clipart category!', 'web-to-print-online-designer' ),
            'flag'  => 0
        );
        if ( !wp_verify_nonce( $_POST['nonce'], 'nbdesigner_add_cat' ) || !current_user_can( 'edit_nbd_art' ) ) {
            echo json_encode( $data );
            wp_die();
        }
        $path   = NBDESIGNER_DATA_DIR . '/art_cat.json';
        $cat    = array(
            'name'  => sanitize_text_field($_POST['name']),
            'id'    => absint( $_POST['id'] )
        );
        $this->nbdesigner_update_json_setting( $path, $cat, $cat['id'] );
        $data['mes']    = esc_html__( 'Category has been added/edited successfully!', 'web-to-print-online-designer' );
        $data['flag']   = 1;
        echo json_encode( $data );
        wp_die();
    }
    public function nbdesigner_delete_font_cat() {
        $data = array(
            'mes'   => esc_html__( 'You do not have permission to delete font category!', 'web-to-print-online-designer' ),
            'flag'  => 0
        );
        if ( !wp_verify_nonce( $_POST['nonce'], 'nbdesigner_add_cat' ) || !current_user_can( 'edit_nbd_font' ) ) {
            echo json_encode( $data );
            wp_die();
        }
        $path   = NBDESIGNER_DATA_DIR . '/font_cat.json';
        $id     = absint( $_POST['id'] );
        $this->nbdesigner_delete_json_setting( $path, $id, true );
        $font_path  = NBDESIGNER_DATA_DIR . '/fonts.json';
        $this->nbdesigner_update_json_setting_depend( $font_path, $id );
        $data['mes']    = esc_html__( 'Category has been delete successfully!', 'web-to-print-online-designer' );
        $data['flag']   = 1;
        echo json_encode( $data );
        wp_die();
    }
    public function nbdesigner_delete_art_cat() {
        $data = array(
            'mes'   => esc_html__( 'You do not have permission to delete clipart category!', 'web-to-print-online-designer' ),
            'flag'  => 0
        );
        if (!wp_verify_nonce($_POST['nonce'], 'nbdesigner_add_cat') || !current_user_can('delete_nbd_art')) {
            echo json_encode( $data );
            wp_die();
        }
        $path   = NBDESIGNER_DATA_DIR . '/art_cat.json';
        $id     = $_POST['id'];
        $this->nbdesigner_delete_json_setting( $path, $id, true );
        $art_path = NBDESIGNER_DATA_DIR . '/arts.json';
        $this->nbdesigner_update_json_setting_depend( $art_path, $id );
        $data['mes']    = esc_html__( 'Category has been delete successfully!', 'web-to-print-online-designer' );
        $data['flag']   = 1;
        echo json_encode( $data );
        wp_die();
    }
    public function nbdesigner_get_list_google_font() {
        $path = NBDESIGNER_PLUGIN_DIR . 'data/listgooglefonts.json';
        $data = (array) $this->nbdesigner_read_json_setting($path);
        return json_encode($data);
    }
    public function nbdesigner_add_google_font() {
        $data = array(
            'mes'   => esc_html__( 'You do not have permission to add font!', 'web-to-print-online-designer' ),
            'flag'  => 0
        );
        if ( !wp_verify_nonce( $_POST['nonce'], 'nbdesigner_add_cat' ) || !current_user_can( 'edit_nbd_font' ) ) {
            die('Security error');
        }
        $gg_fonts = array();
        if( !isset( $_POST['fonts'] ) ){
            die( 'Empty data' );
        }else{
            $all_fonts  = json_decode( file_get_contents( NBDESIGNER_PLUGIN_DIR. '/data/google-fonts-ttf.json' ) )->items;
            $fonts      = json_decode( stripslashes( $_POST['fonts'] ) );
            foreach( $fonts as $key => $font ){
                $subset = 'all';
                $file   = array( 'r' => 1 );
                foreach( $all_fonts as $f ){
                    if( $font->name == $f->family ){
                        $subset = $f->subsets[0];
                        if( isset( $f->files->regular ) ){
                            $file['r'] = $f->files->regular;
                        } else {
                            $file['r'] = reset( $f->files );
                        }
                        if( isset($f->files->italic) ){
                            $file['i'] = $f->files->italic;
                        }
                        if( isset($f->files->{"700"}) ){
                            $file['b'] = $f->files->{"700"};
                        }
                        if( isset($f->files->{"700italic"}) ){
                            $file['bi'] = $f->files->{"700italic"};
                        }
                        break;
                    }
                }
                $gg_fonts[] = array(
                    "id"        => $key,
                    "name"      => $font->name,
                    "alias"     => $font->name,
                    "type"      => "google", 
                    "subset"    => $subset, 
                    "file"      => $file, 
                    "cat"       => array("99")
                );
            }
        }
        $path_font      = NBDESIGNER_DATA_DIR . '/googlefonts.json';
        file_put_contents( $path_font, json_encode( $gg_fonts ) );
        $data['mes']    = esc_html__( 'The google fonts have been added successfully!', 'web-to-print-online-designer' );
        $data['flag']   = 1;
        echo json_encode( $data );
        wp_die();
    }
    public function nbdesigner_manager_fonts() {
        $notice                 = '';
        $font_id                = 0;
        $cats                   = array("0");
        $current_font_cat_id    = 0;
        $update                 = false;
        $list                   = $this->nbdesigner_read_json_setting(NBDESIGNER_DATA_DIR . '/fonts.json'); 
        $cat                    = $this->nbdesigner_read_json_setting(NBDESIGNER_DATA_DIR . '/font_cat.json');
        $data_font_google       = $this->nbdesigner_read_json_setting(NBDESIGNER_DATA_DIR . '/googlefonts.json');
        $list_all_google_font   = $this->nbdesigner_get_list_google_font();
        $current_cat            = filter_input(INPUT_GET, "cat_id", FILTER_VALIDATE_INT);
        $font = array(
            'file' => array()
        );
        if (is_array($cat))
            $current_font_cat_id = sizeof( $cat );
        if ( isset( $_GET['id'] ) ) {
            $font_id = $_GET['id'];
            $update = true;
            if ( isset( $list[$font_id] ) ) {
                $font_data      = $list[$font_id];
                $cats           = $font_data->cat;
                $font['url']    = $font_data->url;
                $font['id']     = $font_data->id;
                $font['type']   = $font_data->type;
                $font['alias']  = $font_data->alias;
                $font['subset'] = $font_data->subset;
                $font['file']   = (array)$font_data->file;
            }
        }
        if ( isset( $_POST[$this->plugin_id . '_hidden'] ) && wp_verify_nonce( $_POST[$this->plugin_id . '_hidden'], $this->plugin_id ) && current_user_can( 'edit_nbd_font' ) ) {
            $font['name']   = esc_html($_POST['nbdesigner_font_name']);
            $font['subset'] = $_POST['subset'];
            $font['alias']  = isset($font['alias']) ? $font['alias'] : 'nbfont' . substr( md5( rand( 0, 999999 ) ), 0, 10 );
            $font['cat']    = $cats;
            if ( isset( $_POST['nbdesigner_font_cat'] ) ) $font['cat'] = $_POST['nbdesigner_font_cat'];
            $check = true;
            if ( isset( $_FILES['font']) && array_filter( $_FILES['font']['name'] ) && ( !empty($font['file']['r']) || $_FILES['font']['name'][0] != '' ) && ( $_POST['nbdesigner_font_name'] != '' ) ) {
                foreach( $_FILES['font']['name'] as $key => $font_name ){
                    if( $font_name != '' ){
                        $uploaded_file_name = basename( $font_name );
                        $allowed_file_types = array('ttf');
                        $font['type']       = $this->nbdesigner_get_extension( $uploaded_file_name );
                        if ( Nbdesigner_IO::checkFileType( $uploaded_file_name, $allowed_file_types ) ) {
                            switch ($key) {
                                case 1:
                                    $f_index = 'i';
                                    break;
                                case 2:
                                    $f_index = 'b';
                                    break;
                                case 3:
                                    $f_index = 'bi';
                                    break;
                                default:
                                    $f_index = 'r';
                            }
                            $prefix         = $key ? $f_index : '';
                            $new_path_font  = Nbdesigner_IO::create_file_path( NBDESIGNER_FONT_DIR, $font['alias'].$prefix, $font['type'], true );
                            if( move_uploaded_file( $_FILES['font']["tmp_name"][$key], $new_path_font['full_path'].'.'.$font['type'] ) ){
                                if( !$key ){
                                    $font['url'] = $new_path_font['date_path'] . '.' . $font['type'];
                                }
                                $font['file'][$f_index] = $new_path_font['date_path'] . '.' . $font['type'];
                            }else{
                                $check  = false;
                                $notice = apply_filters( 'nbdesigner_notices', nbd_custom_notices( 'error', esc_html__( 'Error while upload font.', 'web-to-print-online-designer' ) ) );
                                break;
                            }
                        }else{
                            $check = false;
                            $notice = apply_filters( 'nbdesigner_notices', nbd_custom_notices( 'error', esc_html__( 'Incorrect file extensions.', 'web-to-print-online-designer' ) ) );
                            break;
                        }
                    }
                }
                if( $check ){
                    if ($update) {
                        $this->nbdesigner_update_list_fonts( $font, $font_id );
                    } else {
                        $this->nbdesigner_update_list_fonts( $font );
                    }
                    $notice = apply_filters( 'nbdesigner_notices', nbd_custom_notices( 'success', esc_html__( 'Your font has been saved.', 'web-to-print-online-designer' ) ) );
                }
            } else if($update && ($_POST['nbdesigner_font_name'] != '')){
                $font_data->name    = $_POST['nbdesigner_font_name'];
                $font_data->subset  = $_POST['subset'];
                $font_data->cat     = $font['cat'];
                if( isset($_POST['display_name']) ){
                    $font_data->display_name = $_POST['display_name'];
                }
                $this->nbdesigner_update_list_fonts($font_data, $font_id);
                $notice = apply_filters('nbdesigner_notices', nbd_custom_notices('success', esc_html__('Your font has been saved.', 'web-to-print-online-designer' ) ) );
            } else {
                $notice = apply_filters( 'nbdesigner_notices', nbd_custom_notices( 'warning', esc_html__( 'Please choose font file or font name.', 'web-to-print-online-designer' ) ) );
            }
            $list = $this->nbdesigner_read_json_setting( NBDESIGNER_DATA_DIR . '/fonts.json' );
            $cats = $font['cat'];
        }
        $current_cat_id = 0;
        if( isset( $current_cat ) ){
            $current_cat_id = $current_cat;
            $new_list       = array();
            foreach( $list as $art ){
                if( in_array( (string)$current_cat, $art->cat ) ) $new_list[] = $art;
            }
            foreach($cat as $c){
                if($c->id == $current_cat){
                    $name_current_cat = $c->name;
                    break;
                } 
                $name_current_cat = 'uploaded';
            }
            $list = $new_list;
        }else{
            $name_current_cat = 'uploaded';
        }
        $total = sizeof($list);
        //NBD_Update_Data::update_fonts();
        include_once( NBDESIGNER_PLUGIN_DIR . 'views/nbdesigner-manager-fonts.php' );
    }
    /**
     * Analytics and statistics customer used product design
     * @since 1.5
     * 
    */
    public function nbdesigner_analytics() {
        //TODO something analytics
        include_once( NBDESIGNER_PLUGIN_DIR . 'views/nbdesigner-analytics.php' );
    }
    public function nbdesigner_update_list_fonts( $font, $id = null ) {
        if (isset($id)) {
            $this->nbdesigner_update_font($font, $id);
            return;
        }
        $list_font  = array();
        $path       = NBDESIGNER_DATA_DIR . '/fonts.json';
        $list       = $this->nbdesigner_read_json_setting($path);
        if (is_array($list)) {
            $list_font  = $list;
            $id         = sizeOf($list_font);
            $font['id'] = (string) $id;
        }
        $list_font[]    = $font;
        $res            = json_encode( $list_font );
        file_put_contents( $path, $res );
    }
    public function nbdesigner_update_list_arts($art, $id = null) {
        $path = NBDESIGNER_DATA_DIR . '/arts.json';
        if ( isset( $id ) ) {
            $this->nbdesigner_update_json_setting( $path, $art, $id );
            return;
        }
        $list_art = array();
        $list = $this->nbdesigner_read_json_setting($path);
        if (is_array($list)) {
            $list_art   = $list;
            $id         = sizeOf($list_art);
            $art['id']  = (string) $id;
        }
        $list_art[] = $art;
        $res = json_encode($list_art);
        file_put_contents($path, $res);
    }
    public function nbdesigner_read_json_setting($fullname) {
        if (file_exists($fullname)) {
            $list = json_decode(file_get_contents($fullname));
        } else {
            $list = '[]';
            file_put_contents($fullname, $list);
            $list = array();
        }
        return $list;
    }
    public function nbdesigner_delete_json_setting($fullname, $id, $reindex = true) {
        $list = $this->nbdesigner_read_json_setting($fullname);
        if (is_array($list)) {
            array_splice($list, $id, 1);
            if ($reindex) {
                $key = 0;
                foreach ($list as $val) {
                    $val->id = (string) $key;
                    $key++;
                }
            }
        }
        $res = json_encode($list);
        file_put_contents($fullname, $res);
    }
    public function nbdesigner_update_json_setting($fullname, $data, $id) {
        $list = $this->nbdesigner_read_json_setting($fullname);
        if (is_array($list))
            $list[$id] = $data;
        else {
            $list = array();
            $list[] = $data;
        }
        $_list = array();
        foreach ($list as $val) {
            $_list[] = $val;
        }
        $res = json_encode($_list);
        file_put_contents($fullname, $res);
    }
    public function nbdesigner_update_json_setting_depend($fullname, $id) {
        $list = $this->nbdesigner_read_json_setting($fullname);
        if (!is_array($list)) return;
        foreach ($list as $val) {
            if (!((sizeof($val) > 0))) continue;
            //if (!sizeof($val->cat)) break;
            foreach ($val->cat as $k => $v) {
                if ($v == $id) {
                    array_splice($val->cat, $k, 1);
                    break;
                }
            }
            foreach ($val->cat as $k => $v) {
                if ($v > $id) {
                    $new_v = (string) --$v;
                    unset($val->cat[$k]);
                    array_splice($val->cat, $k, 0, $new_v);
                    //$val->cat[$k] = (string)--$v;
                }
            }
        }
        $res = json_encode($list);
        file_put_contents($fullname, $res);
    }
    public function nbdesigner_delete_font() {
        $data = array(
            'mes'   => esc_html__('You do not have permission to delete font!', 'web-to-print-online-designer'),
            'flag'  => 0
        );
        if (!wp_verify_nonce($_POST['nonce'], 'nbdesigner_add_cat') || !current_user_can('delete_nbd_font')) {
            echo json_encode($data);
            wp_die();
        }
        $id = $_POST['id'];
        $type = $_POST['type'];
        if ($type == 'custom') {
            $path = NBDESIGNER_DATA_DIR . '/fonts.json';
            $list = $this->nbdesigner_read_json_setting($path);
        } else
            $path = NBDESIGNER_DATA_DIR . '/googlefonts.json';
        $this->nbdesigner_delete_json_setting($path, $id);
        $data['mes'] = esc_html__('The font has been deleted successfully!', 'web-to-print-online-designer');
        $data['flag'] = 1;
        echo json_encode($data);
        wp_die();
    }
    public function nbdesigner_delete_art() {
        $data = array(
            'mes'   =>  esc_html__('You do not have permission to delete clipart!', 'web-to-print-online-designer'),
            'flag'  => 0
        );
        if (!wp_verify_nonce($_POST['nonce'], 'nbdesigner_add_cat') || !current_user_can('delete_nbd_art')) {
            echo json_encode($data);
            wp_die();
        }
        $id = $_POST['id'];
        $path = NBDESIGNER_DATA_DIR . '/arts.json';
        $list = $this->nbdesigner_read_json_setting($path);
        $file_art = $list[$id]->file;
        if(file_exists($file_art)){
            unlink($file_art);
        }else{
            $file_art = NBDESIGNER_ART_DIR . $list[$id]->file;
            unlink($file_art);
        }
        $this->nbdesigner_delete_json_setting($path, $id);
        $data['mes'] = esc_html__('Clipart has been deleted successfully!', 'web-to-print-online-designer');
        $data['flag'] = 1;
        echo json_encode($data);
        wp_die();
    }
    public function nbdesigner_update_font($font, $id) {
        $path = NBDESIGNER_DATA_DIR . '/fonts.json';
        $this->nbdesigner_update_json_setting($path, $font, $id);
    }
    public function nbdesigner_notices($value = '') {
        return $value;
    }
    public function nbdesigner_custom_notices($command, $mes) {
        switch ($command) {
            case 'success':
                if (!isset($mes)) $mes = esc_html__('Your settings have been saved.', 'web-to-print-online-designer');
                $notice = '<div class="updated notice notice-success is-dismissible">
                                <p>' . $mes . '</p>
                                <button type="button" class="notice-dismiss">
                                    <span class="screen-reader-text">'. esc_html__('Dismiss this notice.', 'web-to-print-online-designer') .'</span>
                                </button>
                            </div>';
                break;
            case 'error':
                if (!isset($mes)) $mes = esc_html__('Irks! An error has occurred.', 'web-to-print-online-designer');
                $notice = '<div class="notice notice-error is-dismissible">
                                <p>' . $mes . '</p>
                                <button type="button" class="notice-dismiss">
                                    <span class="screen-reader-text">'. esc_html__('Dismiss this notice.', 'web-to-print-online-designer') .'</span>
                                </button>
                            </div>';
                break;
            case 'notices':
                if (!isset($mes)) $mes = esc_html__('Irks! An error has occurred.', 'web-to-print-online-designer');
                $notice = '<div class="notice notice-warning">
                                <p>' . $mes . '</p>
                            </div>';
                break;
            case 'warning':
                if (!isset($mes)) $mes = esc_html__('Warning.', 'web-to-print-online-designer');
                $notice = '<div class="notice notice-warning is-dismissible">
                                <p>' . $mes . '</p>
                                <button type="button" class="notice-dismiss">
                                    <span class="screen-reader-text">'. esc_html__('Dismiss this notice.', 'web-to-print-online-designer') .'</span>
                                </button>
                            </div>';
                break;
            default:
                $notice = '';
        }
        return $notice;
    }
    public function nbdesigner_manager_arts() {
        $notice             = '';
        $current_art_cat_id = 0;
        $art_id             = 0;
        $update             = false;
        $cats               = array("0");
        $list               = $this->nbdesigner_read_json_setting( NBDESIGNER_DATA_DIR . '/arts.json' );
        $cat                = $this->nbdesigner_read_json_setting( NBDESIGNER_DATA_DIR . '/art_cat.json' );
        $total              = sizeof($list);
        $limit              = 40;
        if ( is_array( $cat ) )
            $current_art_cat_id = sizeof( $cat );
        if ( isset( $_GET['id'] ) ) {
            $art_id = absint( $_GET['id'] );
            $update = true;
            if ( isset( $list[$art_id] ) ) {
                $art_data   = $list[$art_id];
                $cats       = $art_data->cat;
            }
        }
        $page           = filter_input( INPUT_GET, "p", FILTER_VALIDATE_INT );
        $current_cat    = filter_input( INPUT_GET, "cat_id", FILTER_VALIDATE_INT );

        if ( isset( $_POST[$this->plugin_id . '_hidden'] ) && wp_verify_nonce( $_POST[$this->plugin_id . '_hidden'], $this->plugin_id ) && current_user_can( 'edit_nbd_art' ) ) {
            $art        = array();
            $art['id']  = absint( $_POST['nbdesigner_art_id'] );
            $art['cat'] = $cats;
            if ( isset( $_POST['nbdesigner_art_cat'] ) ) $art['cat'] = $_POST['nbdesigner_art_cat'];
            if ( isset( $_FILES['svg'] ) ) {
                $files = $_FILES['svg'];
                foreach ( $files['name'] as $key => $value ) {
                    $file = array(
                      'name'     => $files['name'][$key],
                      'type'     => $files['type'][$key],
                      'tmp_name' => $files['tmp_name'][$key],
                      'error'    => $files['error'][$key],
                      'size'     => $files['size'][$key]
                    );
                    $uploaded_file_name = sanitize_file_name( basename($file['name']) );
                    $allowed_file_types = array( 'svg', 'png', 'jpg', 'jpeg' );
                    if ( Nbdesigner_IO::checkFileType( $uploaded_file_name, $allowed_file_types ) ) {
                        $upload_overrides   = array( 'test_form' => false );
                        $uploaded_file      = wp_handle_upload( $file, $upload_overrides );
                        if ( isset( $uploaded_file['url'] ) ) {
                            $new_path_art   = Nbdesigner_IO::create_file_path( NBDESIGNER_ART_DIR, $uploaded_file_name );
                            $art['file']    = $uploaded_file['file'];
                            $art['url']     = $uploaded_file['url'];
                            $art['name']    = pathinfo( $art['file'], PATHINFO_FILENAME );
                            if ( !copy( $art['file'], $new_path_art['full_path'] ) ) {
                                $notice = apply_filters( 'nbdesigner_notices', nbd_custom_notices( 'error', esc_html__( 'Failed to copy.', 'web-to-print-online-designer' ) ) );
                            }else{
                                $art['file']    = $new_path_art['date_path'];
                                $art['url']     = $new_path_art['date_path'];
                                //$art['url'] = NBDESIGNER_ART_URL . $new_path_art['date_path'];
                            }
                            if ( $update ) {
                                $this->nbdesigner_update_list_arts( $art, $art_id );
                            } else {
                                $this->nbdesigner_update_list_arts( $art );
                            }
                            $notice = apply_filters( 'nbdesigner_notices', nbd_custom_notices( 'success', esc_html__( 'Your art has been saved.', 'web-to-print-online-designer' ) ) );
                        } else {
                            $notice = apply_filters( 'nbdesigner_notices', nbd_custom_notices( 'error', sprintf( __( 'Error while upload art, please try again! <a target="_blank" href="%s">Force upload SVG</a>', 'web-to-print-online-designer' ), esc_url( admin_url( 'admin.php?page=nbdesigner&tab=general#nbdesigner_option_download_type' ) ) ) ) );
                        }
                    } else {
                        $notice = apply_filters('nbdesigner_notices', nbd_custom_notices('error', esc_html__( 'Incorrect file extensions.', 'web-to-print-online-designer' ) ) );
                    }
                }
            }
            $list   = $this->nbdesigner_read_json_setting( NBDESIGNER_DATA_DIR . '/arts.json' );
            $cats   = $art['cat'];
            $total  = sizeof( $list );
            
        }
        $current_cat_id     = 0;
        $name_current_cat   = 'uploaded';
        if( $total ){
            if( isset( $current_cat ) ){
                $current_cat_id = $current_cat;
                $new_list       = array();
                foreach( $list as $art ){
                    if( in_array( (string)$current_cat, $art->cat ) ) $new_list[] = $art;
                    if( ( $current_cat == 0 ) && sizeof( $art->cat ) == 0) $new_list[] = $art;
                }
                foreach( $cat as $c ){
                    if( $c->id == $current_cat ){
                        $name_current_cat = $c->name;
                        break;
                    }
                    $name_current_cat = 'uploaded';
                }
                $list = $new_list;
                $total = sizeof( $list );
            }else{
                $name_current_cat = 'uploaded';
            }
            if( isset( $page ) ){
                $_tp = ceil( $total / $limit );
                if($page > $_tp) $page = $_tp;
                $_list = array_slice( $list, ( $page-1 ) * $limit, $limit );
            }else{
                $_list = $list;
                if($total > $limit) $_list = array_slice( $list, 0, $limit );
            }
        } else{
            $_list = array();
        }
        if( isset( $current_cat ) ){
            $url = add_query_arg( array( 'cat_id' => $current_cat ), admin_url( 'admin.php?page=nbdesigner_manager_arts' ) );
        }else{
            $url = admin_url( 'admin.php?page=nbdesigner_manager_arts' );
        }
        require_once NBDESIGNER_PLUGIN_DIR . 'includes/class.nbdesigner.pagination.php';
        $paging = new Nbdesigner_Pagination();
        $config = array(
            'current_page'  => isset( $page ) ? $page : 1,
            'total_record'  => $total,
            'limit'         => $limit,
            'link_full'     => $url.'&p={p}',
            'link_first'    => $url
        );
        $paging->init( $config );
        include_once( NBDESIGNER_PLUGIN_DIR . 'views/nbdesigner-manager-arts.php' );
    }
    public function admin_success() {
        if ( isset( $_POST[$this->plugin_id . '_hidden'] ) && wp_verify_nonce( $_POST[$this->plugin_id . '_hidden'], $this->plugin_id ) ){
            echo '<div class="updated notice notice-success is-dismissible">
                        <p>' . esc_html__('Your settings have been saved.', 'web-to-print-online-designer') . '</p>
                        <button type="button" class="notice-dismiss">
                            <span class="screen-reader-text">'. esc_html__('Dismiss this notice.', 'web-to-print-online-designer') .'</span>
                        </button>
                  </div>';
        }
    }
    public function add_design_box() {
        add_meta_box( 'nbdesigner_setting', esc_html__( 'Setting NBDesigner', 'web-to-print-online-designer' ), array( $this, 'setting_design' ), 'product', 'normal', 'high' );
      
$screen = class_exists( '\Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController' ) && wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()

        ? wc_get_page_screen_id( 'shop-order' )

        : 'shop_order';

        add_meta_box( 'nbdesigner_order', esc_html__( 'Customer Design', 'web-to-print-online-designer' ), array( $this, 'order_design' ), $screen, 'side', 'default' );
      
    }
    public function nbdesigner_detail_order() {
        if( isset( $_GET['order_id'] ) ){
        
            $order_id   = absint( $_GET['order_id'] );
            $order      = new WC_Order( $order_id );
            $zip_files  = array();
            $files      = array();
            if( isset( $_GET['download-type'] ) && ( $_GET['download-type'] == 'png-hires' || $_GET['download-type'] == 'jpg-hires' || $_GET['download-type'] == 'pdf' || $_GET['download-type'] == 'png' || $_GET['download-type'] == 'jpg' || $_GET['download-type'] == 'cmyk' || $_GET['download-type'] == 'svg' || $_GET['download-type'] == 'ibg' ) ){
                $type           = sanitize_text_field( $_GET['download-type'] );
                $product_id     = absint( $_GET['product_id'] );
                $variation_id   = absint( $_GET['variation_id'] );
                $nbd_item_key   = sanitize_text_field( $_GET['nbd_item_key'] );
                $path           = NBDESIGNER_CUSTOMER_DIR .'/'. $_GET['nbd_item_key'];
                $multil_color   = false; // custom charming print
                $item_id = isset( $_GET['item_id'] ) ? $_GET['item_id'] : 0;
                if($item_id) {
                    $order_item = wc_get_order_item_meta( $item_id , '_nbo_options' );
                    $color_select = isset($order_item['color_selected']) ? $order_item['color_selected'] : '';
                    if( is_array($color_select) && isset($color_select[0]) &&  $color_select[0] ) {
                        $multil_color = true;
                    }
                }
                if( isset($order_item['pt_color']) && ( $order_item['pt_color'] == 'pt_color1' || $order_item['pt_color'] == 'pt_color3' ) ) {
                    $multil_color = false;
                }
                if( $type != 'png' && $type != 'svg' && $type != 'ibg' && $type != 'png-hires' && $type != 'jpg-hires' && $type != 'pdf' ) $path = $path . '/' .$type;
                if( $type == 'ibg' || $type == 'png-hires' || $type == 'jpg-hires' || $type == 'pdf' || $type == 'svg' ){
                    $config     = nbd_get_data_from_json($path . '/config.json');
                    // custom charming print
                    if( $multil_color ) {
                        foreach( $color_select as $key => $color ) {
                             nbd_custom_export_pdfs( $nbd_item_key, false, false, 'no' , $color , false);
                        }     
                    }
                    $dowload_default = true;
                    if(isset($order_item['pt_color']) && $order_item['pt_color'] == 'pt_color3') {
                        nbd_custom_export_pdfs( $nbd_item_key, false, false, 'no' , '' , true );
                        if(isset($color_select[0])) {
                            nbd_custom_export_pdfs( $nbd_item_key, false, false, 'no' , $color_select[0] , true );
                        }
                        $dowload_default = false;
                    }
                    // end
                    if($dowload_default) {
                        nbd_export_pdfs( $nbd_item_key, false, false, 'no' );
                    }
                    $pdf_path   = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/customer-pdfs';
                    $list_pdf   = Nbdesigner_IO::get_list_files_by_type( $pdf_path, 1, 'pdf' );
                    if( $type != 'svg' ) {
                        if( !isset( $config->dpi ) ){
                            $option = unserialize( file_get_contents( $path . '/option.json' ) );
                            $dpi    = $option['dpi'];
                        }else{
                            $dpi = $config->dpi;
                        }
                        if( count( $list_pdf ) ){
                            foreach( $list_pdf as $key => $pdf ){
                                if( $type == 'ibg' ){
                                    NBD_Image::pdf2image( $pdf, $dpi, 'png' );
                                    NBD_Image::pdf2image( $pdf, $dpi, 'jpg' );
                                }else if( $type == 'png-hires' ){
                                    NBD_Image::pdf2image( $pdf, $dpi, 'png' );
                                }else if( $type == 'jpg-hires' ){
                                    NBD_Image::pdf2image( $pdf, $dpi, 'jpg' );
                                }
                            }
                            if( $type == 'pdf' ){
                                $files = $list_pdf;
                            }
                            $path = $pdf_path;
                        }
                    }
                }
                if( $type == 'svg' ){
                    if($multil_color) {
                       $svg_path = $path . '/svgpath';
                       if( !file_exists( $svg_path ) ){
                            $this->convert_svg_embed( $path );
                        }
                        $files = Nbdesigner_IO::get_list_files_by_type( $svg_path, 1, 'svg' );
                    } else {
                        if( file_exists( NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key .'/product.json' ) ){
                            $product_data = unserialize(file_get_contents(NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key .'/product.json'));
                        }
                        $path = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key;
                        $svg_path = $path . '/svg';
                        if (!file_exists($svg_path)) {
                            nbd_convert_svg_embed($path);
                        }
                        $svg_path_fe = $path . '/svg_fe/';
                        if( !file_exists($svg_path_fe) ){
                            Nbdesigner_IO::mkdir($svg_path_fe);
                        }else{
                            Nbdesigner_IO::delete_folder($svg_path_fe);
                            Nbdesigner_IO::mkdir($svg_path_fe);
                        }
                        $files = Nbdesigner_IO::get_list_files_by_type($svg_path, 1, 'svg');
                        if( isset($product_data) ) {
                            foreach ($product_data as $key => $value) {
                                $file_name = 'frame_' . $key . '_svg.svg';
                                $file_name_convert = 'frame_cv_' . $key . '_svg.svg';
                                if ($value['include_background'] == 1) {
                                    $contentFile = file_get_contents($svg_path . '/' . $file_name);
                                    $urlBg = wp_get_attachment_url($value['img_src']);
                                    $img = "<image href='$urlBg' width='100%' />";
                                    if( $value['img_src_height'] == $value['area_design_height'] && $value['img_src_width'] == $value['area_design_width'] ) {
                                        $insertImage = str_replace("</defs>", "</defs> $img", $contentFile);
                                        file_put_contents( $svg_path_fe . $file_name_convert , $insertImage );
                                    } else {
                                        $xdoc = new DomDocument;
                                        $xdoc->Load($svg_path . '/' . $file_name);
                                        $svg = $xdoc->getElementsByTagName('svg')->item(0);
                                        $svg->setAttribute('width', $value['area_design_width']);
                                        $svg->setAttribute('height', $value['area_design_height']);
                                        $svg->setAttribute('x', $value['area_design_left'] - $value['img_src_left'] );
                                        $svg->setAttribute('y', $value['area_design_top'] - $value['img_src_top'] );
                                        $svg->removeAttribute('xmlns');
                                        $svg->removeAttribute('xmlns:xlink');
                                        $svg->removeAttribute('xml:space');
                                        $svg->removeAttribute('version');
                                        $convert_svg = $xdoc->saveXML();
                                        $convert_svg = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>' , '' , $convert_svg);
                                        $convert_svg = str_replace('<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">' , '' , $convert_svg);
                                        $svg1 = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="'.$value["img_src_width"].'" height="'.$value["img_src_height"].'" viewBox="0 0 '.$value["img_src_width"].' '.$value["img_src_height"].'" xml:space="preserve">';
                                        $svg1 .= "<image href='$urlBg' width='100%'/>";
                                        $new_svg = $svg1.$convert_svg.'</svg>';
                                        file_put_contents( $svg_path_fe . $file_name_convert , $new_svg);  
                                    }
                                    $files[$key] = $svg_path_fe . $file_name_convert;
                                } else {
                                    $files[$key] = $svg_path . '/' . $file_name;
                                }
                            }
                        }
                    }
                }else if( $type != 'pdf' ){
                    $files = Nbdesigner_IO::get_list_images( $path, 1 );
                    if( $type == 'jpg-hires' ){
                        $files = Nbdesigner_IO::get_list_files_by_type( $path, 1, 'jpg|jpeg' );
                    }
                    if( $type == 'png-hires' ){
                        $files = Nbdesigner_IO::get_list_files_by_type( $path, 1, 'png' );
                    }
                }
                if( count( $files ) > 0 ){
                    foreach( $files as $key => $file ){
                        $zip_files[] = $file;
                    }
                } else{
                    $arr = array( 'nbd_item_key' => $nbd_item_key, 'order_id' => $order_id, 'product_id' => $product_id, 'variation_id' => $variation_id );
                    $url = add_query_arg( $arr, admin_url( 'admin.php?page=nbdesigner_detail_order' ) );
                    wp_redirect( esc_url( $url ) );
                    exit();
                }
                $pathZip = NBDESIGNER_DATA_DIR . '/download/customer-design-' . $_GET['nbd_item_key'] . '-' . $type . '.zip';
                $nameZip = 'customer-design-' . $_GET['nbd_item_key'] . '-' . $type . '.zip';
                nbd_zip_files_and_download( $zip_files, $pathZip, $nameZip );
                exit();
            }
            if( isset( $_GET['download-all'] ) && ( $_GET['download-all'] == 'true' ) ){
                $products = $order->get_items();
                foreach( $products AS $order_item_id => $product ){
                    if( wc_get_order_item_meta( $order_item_id, '_nbd' ) || wc_get_order_item_meta( $order_item_id, '_nbu' ) ){
                        $nbd_item_key = wc_get_order_item_meta( $order_item_id, '_nbd' );
                        $nbu_item_key = wc_get_order_item_meta( $order_item_id, '_nbu' );
                        if( $nbd_item_key ){
                            $list_images = Nbdesigner_IO::get_list_images( NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key, 1 );
                            if( count( $list_images ) > 0 ){
                                foreach( $list_images as $key => $image ){
                                    $zip_files[] = $image;
                                }
                            }
                        }
                        if( $nbu_item_key ){
                            $files = Nbdesigner_IO::get_list_files( NBDESIGNER_UPLOAD_DIR .'/'. $nbu_item_key );
                            $files = apply_filters( 'nbu_download_upload_files', $files, $product );
                            if( count( $files ) > 0 ){
                                foreach( $files as $key => $file ){
                                    $zip_files[] = $file;
                                }
                            }
                        }
                    }
                }
                if( !count( $zip_files ) ){
                    exit();
                }else{
                    $pathZip = NBDESIGNER_DATA_DIR . '/download/customer-design-' . $_GET['order_id'] . '.zip';
                    $nameZip = 'customer-design-' . $_GET['order_id'] . '.zip';
                    nbd_zip_files_and_download( $zip_files, $pathZip, $nameZip );
                }
            }
            if( isset( $_GET['nbd_item_key'] ) ){
                $license        = $this->nbdesigner_check_license();
                $path           = NBDESIGNER_CUSTOMER_DIR .'/'. $_GET['nbd_item_key'];
                $list_images    = Nbdesigner_IO::get_list_images( $path, 1 );
                $_list_pdfs     = file_exists( $path . '/pdfs' ) ? Nbdesigner_IO::get_list_files($path . '/pdfs', 2) : array();
                $list_design    = array();
                $list_pdfs      = array();
                $datas          = unserialize( file_get_contents( $path . '/product.json' ) );
                $option         = unserialize( file_get_contents( $path . '/option.json' ) );
                $path_config    = $path . '/config.json';
                $resources      = (array)json_decode( file_get_contents( $path. '/design.json' ) );
                $fonts          = array();
                if(file_exists( NBDESIGNER_DATA_DIR . '/fonts.json') ){
                    $fonts = (array)json_decode( file_get_contents( NBDESIGNER_DATA_DIR . '/fonts.json' ) );
                }
                $config = nbd_get_data_from_json( $path_config );
                if( isset( $config->product ) && count( $config->product ) ){
                    $datas = array();
                    foreach( $config->product as $side ){
                        $datas[] = (array)$side;
                    }
                };
                if( isset( $config->custom_dimension ) ){
                    $custom_side    = absint( $config->custom_dimension->side );
                    $custom_width   = $config->custom_dimension->width;
                    $custom_height  = $config->custom_dimension->height;
                    $datas          = $this->merge_product_config( $datas, $custom_width, $custom_height, $custom_side );
                }
                foreach ( $_list_pdfs as $_pdf ){
                    if( is_dir( $_pdf)  ) continue;
                    $list_pdfs[] = array(
                        'title' => basename( $_pdf ),
                        'url'   => Nbdesigner_IO::wp_convert_path_to_url( $_pdf )
                    );
                }
                foreach ($list_images as $img){
                    $name                   = basename($img);
                    $arr                    = explode('.', $name);
                    $_frame                 = explode('_', $arr[0]);
                    $frame                  = $_frame[1];
                    $list_design[$frame]    = Nbdesigner_IO::wp_convert_path_to_url($img);
                }
            }
        }
        require_once NBDESIGNER_PLUGIN_DIR .'views/detail-order.php';
    }
    public function setting_design() {
        $current_screen         = get_current_screen();
        $post_id                = get_the_ID();
        $_designer_setting      = unserialize( get_post_meta( $post_id, '_designer_setting', true ) );
        $enable                 = get_post_meta( $post_id, '_nbdesigner_enable', true );
        $enable_upload          = get_post_meta( $post_id, '_nbdesigner_enable_upload', true );
        $upload_without_design  = get_post_meta( $post_id, '_nbdesigner_enable_upload_without_design', true );
        if ( isset( $_designer_setting[0] ) ){
            foreach ($_designer_setting as $key => $set ){
                $_designer_setting[$key] = array_merge( nbd_default_product_setting(), $set );
            }
            $designer_setting = $_designer_setting;
            if(! isset( $designer_setting[0]['version'] ) || $_designer_setting[0]['version'] < 160) {
                $designer_setting = $this->update_config_product_160( $designer_setting );
            }
            if(! isset( $designer_setting[0]['version'] ) || $_designer_setting[0]['version'] < 180) {
                $designer_setting = NBD_Update_Data::nbd_update_media_v180( $designer_setting );
            }
        }else {
            $designer_setting       = array();
            $designer_setting[0]    = nbd_default_product_setting();
        }
        $option     = unserialize( get_post_meta( $post_id, '_nbdesigner_option', true ) );
        $_option    = nbd_get_default_product_option();
        if( !is_array( $option ) ){
            $option = array();
        }
        $option             = array_merge( $_option, $option );
        $unit               = isset( $option['unit'] ) ? $option['unit'] : nbdesigner_get_option( 'nbdesigner_dimensions_unit', 'cm' );
        $upload_setting     = unserialize( get_post_meta( $post_id, '_nbdesigner_upload', true ) );
        $_upload_setting    = nbd_get_default_upload_setting();
        if( !is_array( $upload_setting ) ){
            $upload_setting = array();
        }
        $upload_setting     = array_merge( $_upload_setting, $upload_setting );
        $designer_setting   = nbd_update_config_default( $designer_setting );
        include_once( NBDESIGNER_PLUGIN_DIR . 'views/metabox-design-setting.php' );
    }
    public function update_config_product_160( $designer_setting ){
        $newSetting = array();
        $default    = nbd_default_product_setting();
        foreach ($designer_setting as $key => $setting){
            $setting160 = array();
            $scale = 1;
            if(($setting['img_src_width'] > $setting['img_src_height']) && ($setting['img_src_width'] < 300)) $scale = 300/ $setting['img_src_width'];
            if(($setting['img_src_width'] < $setting['img_src_height']) && ($setting['img_src_height'] < 300)) $scale = 300/ $setting['img_src_height'];
            $ratio150           = 300 / $setting['area_design_width'] / $scale * $setting['real_width'];
            $product_width      = round( $ratio150 * $setting['img_src_width'] * $scale / 300, 2 );
            $product_height     = round( $ratio150 * $setting['img_src_height'] * $scale / 300, 2 );
            $area_design_width  = round( $setting['area_design_width'] * $scale * 5/3 );
            $area_design_height = round( $setting['area_design_height'] * $scale * 5/3 );
            $old_real_top       = $ratio150 * $setting['area_design_top'] * $scale / 300;
            $old_real_left      = $ratio150 * $setting['area_design_left'] * $scale / 300;
            $ratio              = 500 / $product_height;
            $img_src_top        = 0;
            $img_src_left       = round(($product_height - $product_width) * $ratio / 2, 2);
            $img_src_height     = 500;
            $img_src_width      = round( $product_width * $ratio );
            $real_top           = round( $old_real_top, 2 );
            $real_left          = round( $old_real_left - ( $product_height - $product_width) / 2, 2 );
            if( $product_width > $product_height ){
                $img_src_top        = round( ( $product_width - $product_height ) * $ratio / 2, 2 );
                $img_src_left       = 0;
                $img_src_width      = 500;
                $img_src_height     = round( $product_height * $ratio );
                $real_left          = round( $old_real_left, 2 );
                $real_top           = round( $old_real_top - ( $product_width - $product_height ) / 2, 2 );
            }
            $area_design_left                   = round( $setting['area_design_left'] * $scale * 5/3 );
            $area_design_top                    = round( $setting['area_design_top'] * $scale * 5/3 );
            $setting160['product_width']        = $product_width;
            $setting160['product_height']       = $product_height;
            $setting160['real_width']           = $setting['real_width'];
            $setting160['real_height']          = $setting['real_height'];
            $setting160['real_left']            = $real_left;
            $setting160['real_top']             = $real_top;
            $setting160['area_design_left']     = $area_design_left;
            $setting160['area_design_top']      = $area_design_top;
            $setting160['area_design_width']    = $area_design_width;
            $setting160['area_design_height']   = $area_design_height;
            $setting160['img_src_top']          = $img_src_top;
            $setting160['img_src_left']         = $img_src_left;
            $setting160['img_src_height']       = $img_src_height;
            $setting160['img_src_width']        = $img_src_width;
            $setting160['img_src']              = $setting['img_src'];
            $setting160['orientation_name']     = $setting['orientation_name'];   
            $setting160                         = array_merge( $default, $setting160 );
            $newSetting[$key]                   = $setting160;
        }
        return $newSetting;
    }
    public function nbdesigner_update_all_product(){
        if ( !wp_verify_nonce( $_POST['_nbdesigner_update_product'], 'nbdesigner-update-product' ) || !current_user_can( 'administrator' ) ) {
            die( 'Security error' );
        }
        $args_query = array(
            'post_type'         => 'product',
            'post_status'       => 'publish',
            'meta_key'          => '_nbdesigner_enable',
            'orderby'           => 'date',
            'order'             => 'DESC',
            'posts_per_page'    => -1,
            'meta_query'        => array(
                array(
                    'key'   => '_nbdesigner_enable',
                    'value' => 1,
                )
            )
        ); 
        $posts  = get_posts( $args_query );
        $result = array('flag' => 1);
        if( is_array( $posts ) ){
            foreach ( $posts as $post ){
                $product = wc_get_product( $post->ID );
                if( $product->is_type( 'variable' ) ) {
                    $variations = $product->get_available_variations( false );
                    foreach ( $variations as $variation ){
                        $vid = $variation['variation_id'];
                        $designer_setting = unserialize( get_post_meta( $vid, '_designer_setting'.$vid, true ) );
//                        if(! isset($designer_setting[0]['version']) || $designer_setting[0]['version'] < 160) {
//                            $newSetting = $this->update_config_product_160($designer_setting);
//                            update_post_meta($vid, '_designer_setting'.$vid, serialize($newSetting));
//                        } 
                        if(! isset( $designer_setting[0]['version'] ) || $designer_setting[0]['version'] < 170 ) {
                            $setting_design = $this->update_config_product_170( $designer_setting );
                            update_post_meta( $vid, '_designer_variation_setting', serialize( $setting_design ) );
                        }
                    }
                }
                $pid = $post->ID;
                $designer_setting = unserialize( get_post_meta( $pid, '_designer_setting', true ) );
//                if(! isset($designer_setting[0]['version']) || $designer_setting[0]['version'] < 160) {
//                    $newSetting = $this->update_config_product_160($designer_setting);
//                    update_post_meta($pid, '_designer_setting', serialize($newSetting));
//                }  
                if( !isset( $designer_setting[0]['version'] ) || $designer_setting[0]['version'] < 170 ) {
                    $dpi            = get_post_meta( $pid, '_nbdesigner_dpi', true );
                    $option         = nbd_get_default_product_option();
                    $option['dpi']  = $dpi;
                    update_post_meta( $pid, '_nbdesigner_option', serialize( $option ) );
                    $setting_upload = nbd_get_default_upload_setting();
                    update_post_meta( $pid, '_nbdesigner_upload', $setting_upload );
                    $setting_design = $this->update_config_product_170( $designer_setting );
                    update_post_meta( $pid, '_designer_setting', serialize( $setting_design ) );
                }
            }
        }
        echo json_encode( $result );
        wp_die();
    }
    public function update_config_product_170( $designer_setting ){
        foreach( $designer_setting as $key => $val ){
            $designer_setting[$key] = array_merge( nbd_default_product_setting(), $val );
        }
        return $designer_setting;
    }
    public function order_design($post_or_order_object) {
        $order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
        $order_id = $order->get_id();
        $products       = $order->get_items();
        $_data_designs  = maybe_unserialize( $order->get_meta( '_nbdesigner_design_file', true ) );
        if( isset( $_data_designs ) && is_array( $_data_designs ) ) $data_designs = $_data_designs;
        $_data_uploads = maybe_unserialize( $order->get_meta( '_nbdesigner_upload_file', true ) );
        if( isset( $_data_uploads ) && is_array( $_data_uploads ) ) $data_uploads = $_data_uploads;
        $has_design = $order->get_meta( '_nbd', true );
        $has_upload = $order->get_meta( '_nbu', true );
        wp_enqueue_style( 'admin_nbdesigner', NBDESIGNER_CSS_URL . 'admin-nbdesigner.css' );
        wp_enqueue_script( 'admin_nbdesigner', NBDESIGNER_JS_URL . 'admin-nbdesigner.js' );
                    wp_localize_script( 'admin_nbdesigner', 'admin_nbds', array(
                'url'               => admin_url( 'admin-ajax.php' ),
                'nonce'             => wp_create_nonce('nbdesigner_add_cat'),
                'mes_success'       => esc_html__( 'Success!', 'web-to-print-online-designer' ),
                'url_check'         => NBDESIGNER_AUTHOR_SITE,
                'sku'               => NBDESIGNER_SKU,
                'url_gif'           => NBDESIGNER_PLUGIN_URL . 'assets/images/loading.gif',
                'assets_images'     => NBDESIGNER_PLUGIN_URL . 'assets/images/',
                'setting_page'      => admin_url( 'admin.php?page=nbdesigner' ),
                'max_file_uploads'  => function_exists( 'ini_get' ) ? ini_get( 'max_file_uploads' ) : 9999,
                'nbds_lang'         => nbd_get_i18n_javascript() ) );
        include_once( NBDESIGNER_PLUGIN_DIR . 'views/box-order-metadata.php' );
    }
    public function nbd_delete_order_design(){
        check_admin_referer( 'approve-design-email', 'nonce' );
        $result = array(
            'flag'  => false
        );
        $order_id   = wc_clean( $_POST['order_id'] );
        $order      = wc_get_order( $order_id );
        foreach( $order->get_items() as $item_id => $item_product ){
            $result['flag'] = $result['flag'] || wc_delete_order_item_meta($item_id, '_nbd');
            $result['flag'] = $result['flag'] || wc_delete_order_item_meta($item_id, '_nbu');
        }
        $order->delete_meta_data( '_nbd' );
        $order->delete_meta_data( '_nbu' );
        $order->save();
        $has_design = $order->get_meta( '_nbd', true );
        $has_upload = $order->get_meta( '_nbu', true );
        $result['flag'] = $result['flag'] || !$has_design  || !$has_upload;
        wp_send_json( $result );
    }
    public function nbdesigner_allow_create_product( $id ){
        if($id) {
            return true; // custom charming print disable license
        }
        $args = array(
            'post_type'     => 'product',
            'meta_key'      => '_nbdesigner_enable',
            'meta_value'    => 1
        );
        $query = new WP_Query( $args );
        $pros = get_posts( $args );
        $list = array();
        foreach ( $pros as $pro ){
            $list[] = $pro->ID;
        }
        $count      = $query->found_posts;
        $license    = $this->nbdesigner_check_license();
        if( !isset( $license['key'] ) ) return false;
        if( in_array( $id, $list ) ) return true;
        $salt       = md5( $license['key'].'pro' );
        if( ( $salt  != $license['salt'] ) && ( $count > 500 ) ) return false;
        return true;
    }
    public function nbdesigner_get_info_license(){
        if ( !wp_verify_nonce( $_POST['_nbdesigner_license_nonce'], 'nbdesigner-active-key' ) || !current_user_can( 'administrator' ) ) {
            die( 'Security error' );
        } 
        $result = array();
        if( isset( $_POST['nbdesigner']['license'] ) ) {
            $license            = sanitize_text_field( $_POST['nbdesigner']['license'] );
            $result_from_json   = $this->nbdesiger_request_license( $license, $this->activedomain );
            $data               = (array)json_decode( $result_from_json );
            if( isset( $data ) ) {
                switch ( $data["code"] ) {
                    case -1 :
                        $result['mes']  = esc_html__('Missing necessary information!', 'web-to-print-online-designer');
                        $result['flag'] = 0;
                        break;
                    case 0 :
                        $result['mes']  = esc_html__('Incorrect information, check again license key', 'web-to-print-online-designer');
                        $result['flag'] = 0;
                        break;     
                    case 1 :
                        $result['mes']  = esc_html__('Incorrect License key', 'web-to-print-online-designer');
                        $result['flag'] = 0;
                        break;
                    case 2 :
                        $result['mes']  = esc_html__('License key is locked ', 'web-to-print-online-designer');
                        $result['flag'] = 0;
                        break; 
                    case 3 :
                        $result['mes']  = esc_html__('License key have expired', 'web-to-print-online-designer');
                        $result['flag'] = 0;
                        break;
                    case 4 :
                        $result['mes']  = esc_html__('Link your website incorrect', 'web-to-print-online-designer');
                        $result['flag'] = 0;
                        break;     
                    case 5 :
                        $result['mes']  = esc_html__('License key can using', 'web-to-print-online-designer');
                        $result['flag'] = 1;
                        break;
                    case 6 :
                        $result['mes']  = esc_html__('Domain has been added successfully', 'web-to-print-online-designer');
                        $result['flag'] = 1;
                        break;     
                    case 7 :
                        $result['mes']  = esc_html__('Exceed your number of domain license', 'web-to-print-online-designer');
                        $result['flag'] = 0;
                        break;
                    case 8 :
                        $result['mes']  = esc_html__('Unsuccessfully active license key', 'web-to-print-online-designer');
                        $result['flag'] = 0;
                        break;
                }
                $data['key']    = $license;
                $data['salt']   = md5($license.$data['type']);
                if( $data['type'] == 'free' ) $data['number_domain'] = "5";
                if( ( $data["code"] == 5 ) || ( $data["code"] == 6 ) ){
                    //$this->nbdesigner_write_license(json_encode($data));
                    update_option( 'nbdesigner_license', json_encode( $data ) );
                }
            }else{
                $result['mes'] = esc_html__('Try again later!', 'web-to-print-online-designer');
            }
        }else {
            $result['mes'] = esc_html__('Please fill your license!', 'web-to-print-online-designer');
        }
        echo json_encode( $result );
        wp_die();
    }
    public function nbdesigner_remove_license(){
        if( !wp_verify_nonce( $_POST['_nbdesigner_license_nonce'], 'nbdesigner-active-key' ) || !current_user_can( 'administrator' ) ) {
            die('Security error');
        }
        $result         = array();
        $result['flag'] = 0;
        $path           = NBDESIGNER_PLUGIN_DIR . 'data/license.json';
        $path_data      = NBDESIGNER_DATA_CONFIG_DIR . '/license.json';
        if( file_exists( $path_data ) ) {
            $path = $path_data;
        }
        $license    = $this->nbdesigner_check_license();
        $key        = ( isset($license['key'] ) ) ? $license['key'] : '';
        $_request   = $this->nbdesiger_request_license( $key, $this->removedomain );
        if( isset( $_request ) ){
            $request = (array)json_decode( $_request );
            switch ($request["code"]) {
                case -1:
                    $result['mes'] = esc_html__( 'Missing necessary information', 'web-to-print-online-designer' );
                    break;
                case 0:
                    $result['mes'] = esc_html__( 'Incorrect information', 'web-to-print-online-designer' );
                    break;
                case 1:
                    $result['mes'] = esc_html__( 'Incorrect License key', 'web-to-print-online-designer' );
                    break;
                case 2: 
                    if( file_exists( $path ) ) unlink( $path );
                    $result['mes']  = esc_html__( 'Remove license key Successfully', 'web-to-print-online-designer' );
                    $result['flag'] = 1;
                    delete_option( 'nbdesigner_license' );
                    break;
                case 3:
                    $result['mes'] = esc_html__( 'Remove license key Unsuccessfully!', 'web-to-print-online-designer' );
                    break;
            }
        }     
        echo json_encode( $result );
        wp_die();
    }
    private function nbdesiger_request_license( $license, $task ){
        $url_root   = base64_encode( rtrim(get_bloginfo( 'wpurl' ), '/' ) );
        $url        = NBDESIGNER_AUTHOR_SITE . $task . NBDESIGNER_SKU . '/' . $license . '/' . $url_root;
        return nbd_file_get_contents( $url );
    }
    private function nbdesigner_write_license( $license ){
        $path_data = NBDESIGNER_DATA_CONFIG_DIR . '/license.json';
        if ( !file_exists( NBDESIGNER_DATA_CONFIG_DIR ) ) {
            wp_mkdir_p( NBDESIGNER_DATA_CONFIG_DIR );
        }
        file_put_contents( $path_data, $license );
        update_option( 'nbdesigner_license', $license );
    }
    private function nbdesigner_check_license(){
        $result = array();
        $result['status'] = 1;
        
        $data = nbd_get_license_key();
        
        if( $data['key'] == '' ){
            $result['status'] = 0;
        }else{
            $code = ( isset($data["code"] ) ) ? $data["code"] : 10;
            if( ( $code != 5 ) && ( $code != 6 ) ){
                $result['status'] = 0;
            }
            $now                = strtotime("now");
            $expiry_date        = (isset($data["expiry-date"])) ? $data["expiry-date"] : 0;
            $result['expiry']   = $expiry_date;
            if( $expiry_date < $now ){
                $result['status'] = 0;
            }
            if( isset( $data['key'] ) ) $result['key'] = $data['key'];
            if( isset( $data['type'] ) ) {
                $result['type'] = $data['type'];
                $license        = ( isset( $data['key'] ) ) ? $data['key'] : 'somethingiswrong';
                $salt           = ( isset( $data['salt'] ) ) ? $data['salt'] : 'somethingiswrong';
                $new_salt       = md5( $license . 'pro' );
                if( $salt == $new_salt ){
                    $result['type'] = $data['type'];
                }else{
                    $result['type'] = 'free';
                    $result['status'] = 0;
                } 
                $result['salt'] = $salt;
            }
        }
        return $result;
    }
    public function save_settings( $post_id ) {
        if ( !isset($_POST['nbdesigner_setting_box_nonce'] ) || !wp_verify_nonce( $_POST['nbdesigner_setting_box_nonce'], 'nbdesigner_setting_box' )
            || !( current_user_can( 'administrator' ) || current_user_can( 'shop_manager' ) ) ) {
            return $post_id;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
        if ( 'page' == $_POST['post_type'] ) {
            if ( !current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {
            if ( !current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
        $post                   = $_POST;
        $post                   = apply_filters( 'nbd_post_settings', $post );
        $enable                 = $post['_nbdesigner_enable'];
        $enable_upload          = $post['_nbdesigner_enable_upload'];
        $upload_without_design  = $post['_nbdesigner_enable_upload_without_design'];
        $option                 = serialize( $post['_nbdesigner_option'] );
        $setting_design         = serialize( $post['_designer_setting'] );
        $setting_upload         = serialize( $post['_designer_upload'] );
        if( !$this->nbdesigner_allow_create_product( $post_id ) ) return;
        update_post_meta( $post_id, '_designer_setting', $setting_design );
        update_post_meta( $post_id, '_nbdesigner_option', $option );
        update_post_meta( $post_id, '_nbdesigner_upload', $setting_upload );
        update_post_meta( $post_id, '_nbdesigner_enable_upload_without_design', $upload_without_design );
        update_post_meta( $post_id, '_nbdesigner_enable', $enable );
        update_post_meta( $post_id, '_nbdesigner_enable_upload', $enable_upload );
    }
    public function nbdesigner_get_extension( $file_name ) {
        $filetype   = explode( '.', $file_name );
        $file_exten = $filetype[count( $filetype ) - 1];
        if( false !== strpos( $file_name, '.jpg?' ) || false !== strpos( $file_name, '.jpeg?' ) || false !== strpos( $file_name, 'fm=jpg' ) ) $file_exten = 'jpg';
        return strtolower( $file_exten );
    }
    public function nbdesigner_button2() {
        global $product;
        if( $product->get_type() == 'variable' ){
            $position   = nbdesigner_get_option( 'nbdesigner_position_button_product_detail', 1 );
            if( $position == 2 ){
                add_action( 'woocommerce_before_variations_form', array( $this, 'nbdesigner_button' ), 30 );
            }else{
                $this->nbdesigner_button();
            }
        }else{
            $this->nbdesigner_button();
        }
    }
    public function nbdesigner_button() {
        global $wp_query;
        if( isset( $wp_query->query_vars['request-design'] ) || isset( $wp_query->query_vars['upload-design'] ) ){
            return '';
        }

        $check_conditional_show_btn = apply_filters( 'nbd_conditional_show_design_btn', true );
        if( !$check_conditional_show_btn ) return '';

        /* Quick view */
        if( isset($_REQUEST['wc-api']) && $_REQUEST['wc-api'] == 'NBO_Quick_View' ){
            if( !isset( $_REQUEST['mode'] ) || $_REQUEST['mode'] != 'catalog' ){
                return '';
            }
        }
        if( ( isset( $_REQUEST['wc-api'] ) && ( $_REQUEST['wc-api'] == 'WC_Quick_View' || $_REQUEST['wc-api'] == 'NBO_Quick_View' ) ) || ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'yith_load_product_quick_view' ) ){
            global $product;
            $pid            = $product->get_id();
            $is_nbdesign    = get_post_meta( $pid, '_nbdesigner_enable', true );
            if( $is_nbdesign ){
                $url            = esc_url( get_permalink( $pid ) );
                $type           = $product->get_type();
                $_enable_upload = get_post_meta( $pid, '_nbdesigner_enable_upload', true );
                $_enable_upload_without_design = get_post_meta( $pid, '_nbdesigner_enable_upload_without_design', true );
                $label = apply_filters( 'nbd_start_design_label', esc_html__( 'Start Design', 'web-to-print-online-designer' ) );
                if( $_enable_upload ){
                    $label = apply_filters( 'nbd_start_design_and_upload_label', esc_html__( 'Start and upload design', 'web-to-print-online-designer' ) );
                }
                if( $_enable_upload_without_design ){
                    $label = apply_filters( 'nbd_upload_design_label', esc_html__( 'Upload Design', 'web-to-print-online-designer' ) );
                } 
                $option = unserialize( get_post_meta( $pid, '_nbdesigner_option', true ) );
                $layout = isset( $option['layout'] ) ? $option['layout'] : 'm';
                if( $layout == 'v' ){
                    $url = $product->get_permalink();
                } else {
                    $url = add_query_arg( array(
                        'product_id'    => $pid,
                        'view'          => $layout
                    ), getUrlPageNBD( 'create' ) );
                    if( $_enable_upload_without_design ){
                        $upload_option  = unserialize( get_post_meta( $pid, '_nbdesigner_upload', true ) );
                        $upload_style   = nbdesigner_get_option( 'nbdesigner_upload_popup_style', 's' );
                        if( $upload_style == 'a' && isset( $upload_option['advanced_upload'] ) && $upload_option['advanced_upload'] == 1 ){
                            $url = add_query_arg( array(
                                'product_id'    => $pid
                            ), getUrlPageNBD( 'advanced_upload' ) );
                        } else {
                            $url = add_query_arg( array(
                                'product_id'    => $pid
                            ), getUrlPageNBD( 'simple_upload' ) );
                        }
                    }
                }
                ?>
                <p>
                    <a class="button alt nbdesign-button" href="<?php echo esc_url( $url ); ?>" >
                        <?php esc_html_e( $label ); ?>
                    </a>
                </p>
        <?php
            }
            return;
        }
        if(isset($_POST['action'])) return ''; /*Hidden button Start Design on third-party plugin as Quick view*/
        /* Hidden button Start Design on upload page */
        if( is_page( nbd_get_page_id( 'advanced_upload' ) ) || is_page( nbd_get_page_id( 'simple_upload' ) ) ){
            return '';
        }
        $pid            = get_the_ID();
        $pid            = get_wpml_original_id( $pid );
        $is_nbdesign    = get_post_meta( $pid, '_nbdesigner_enable', true ); 
        $variation_id   = 0;
        if ( $is_nbdesign ) {
            $product    = wc_get_product( $pid );
            $type       = $product->get_type();
            $option     = unserialize( get_post_meta( $pid, '_nbdesigner_option', true ) );
            $layout     = isset( $option['layout'] ) ? $option['layout'] : 'm';
            if( $layout == 'v' ) {
                $_enable_upload_without_design = get_post_meta( $pid, '_nbdesigner_enable_upload_without_design', true );
                if( !$_enable_upload_without_design ) return;
            }
            /* Multi language with WPML */
            if( count( $_REQUEST ) ){
                $attributes = array();
                $layout     = nbdesigner_get_option( 'nbdesigner_design_layout' );
                if( $layout == "c" ){
                    foreach ( $_REQUEST as $key => $value ){
                        if ( strpos( $key, 'attribute_' ) === 0 ) {
                            $attributes[$key] = $value;
                        }
                    }
                    if( count( $attributes ) ){
                        if ( class_exists( 'WC_Data_Store' ) ) {
                            $data_store     = WC_Data_Store::load( 'product' );
                            $variation_id   = $data_store->find_matching_product_variation( $product, $attributes );
                        }else{
                            $variation_id = $product->get_matching_variation( $attributes );
                        }
                    }    
                }
            }
            $site_url = site_url();
            if ( class_exists( 'SitePress' ) ) {
                $site_url = home_url();
            }
            $src = add_query_arg( array( 'action' => 'nbdesigner_editor_html', 'product_id' => $pid ), $site_url . '/' );
            if( isset( $_POST['variation_id'] ) &&  $_POST['variation_id'] != '' ){
                $src .= '&variation_id='. absint( $_POST['variation_id'] );
            }
            if( isset( $_GET['nbds-ref'] ) ){
                $src .= '&reference='. $_GET['nbds-ref'];
            }
            if( isset( $_GET['nbo_cart_item_key'] ) && $_GET['nbo_cart_item_key'] !='' ){
                $nbd_item_key = WC()->session->get( $_GET['nbo_cart_item_key'] . '_nbd' );
                if( $nbd_item_key ) {
                    $src .= '&task=edit&nbd_item_key=' . $nbd_item_key . '&cik=' . $_GET['nbo_cart_item_key'];
                }
            }
            if( $variation_id != 0 ){
                $src .= '&variation_id='. $variation_id;
            }
            if( $type == 'variable' && isset( $option['bulk_variation'] ) && $option['bulk_variation'] == 1 ){
                $src .= '&variation_id=0';
            }
            if( isset( $_GET['nbo_cart_item_key'] ) && $_GET['nbo_cart_item_key'] != '' ){
                $src .= '&nbo_cart_item_key=' . $_GET['nbo_cart_item_key'];
            }
            $extra_price = '';
            if( $option['extra_price'] && ! $option['request_quote'] ){
                $extra_price = $option['type_price'] == 1 ? wc_price( $option['extra_price'] ) : $option['extra_price'] . ' %';
            }
            ob_start();
            nbdesigner_get_template( 'single-product/nbd-section.php', array( 'src' => $src, 'extra_price' => $extra_price, 'pid' =>  $pid, 'option' => $option ) );
            $content    = ob_get_clean();
            $position   = nbdesigner_get_option( 'nbdesigner_position_button_product_detail', 1 );
            if( $position == 4 ){
                return $content;
            } else {
                echo $content;
            }
        }
    }
    public function nbd_loggin_redirect_func(){
        if( is_admin() ) return;
        ob_start();
        nbdesigner_get_template( 'auth-wp.php' );
        $content = ob_get_clean();
        echo $content;
    }
    public function nbd_get_nbd_products(){
        if ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) {
            die( 'Security error' );
        }
        $result         = array();
        $nbd_products   = get_transient( 'nbd_frontend_products' );
        if( false === $nbd_products ){
            $products = $this->get_all_product_has_design();
            foreach ( $products as $pro ){
                $product    = wc_get_product( $pro->ID );
                $type       = $product->get_type();
                $image      = get_the_post_thumbnail_url( $pro->ID, 'post-thumbnail' );
                if( !$image ) $image = wc_placeholder_img_src();
                $result[] = array(
                    'product_id'    => $pro->ID,
                    'name'          => $pro->post_title,
                    'src'           => $image,
                    'type'          => $type,
                    'url'           => get_permalink( $pro->ID )
                );
            }
            set_transient( 'nbd_frontend_products' , $result );
        }else{
            $result = $nbd_products;
        }
        echo json_encode( $result );
        wp_die();
    }
    public function nbd_get_product_description(){
        if ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) {
            die( 'Security error' );
        }
        $product_id = $_POST['product_id'];
        $result     = $this->_get_product_description( $product_id );
        wp_send_json( $result );
    }
    public function _get_product_description( $product_id ){
        $product = wc_get_product( $product_id );
        ob_start();
        $image = get_the_post_thumbnail_url($product_id, 'post-thumbnail');
        $image = $image ? $image : wc_placeholder_img_src();  
        nbdesigner_get_template('product-info.php', array(
            'id'                    => $product_id,
            'title'                 => $product->get_title(),
            'description'           => $product->get_description(),
            'short_description'     => $product->get_short_description(),
            'price'                 => $product->get_price(),
            'permalink'             => $product->get_permalink(),
            'image'                 => $image
        ));
        $html = ob_get_clean();
        $result = array(
            'product_id'    => $product_id,
            'html'          => $html,
            'type'          => $product->get_type()
        );
        return $result;
    }
    public function get_all_product_has_design(){
        $args_query = array(
            'post_type'         => 'product',
            'post_status'       => 'publish',
            'meta_key'          => '_nbdesigner_enable',
            'orderby'           => 'date',
            'order'             => 'DESC',
            'posts_per_page'    => -1,
            'meta_query'        => array(
                array(
                    'key'   => '_nbdesigner_enable',
                    'value' => 1,
                ),
                array(
                    'key'   => '_nbdesigner_enable_upload_without_design',
                    'value' => 0,
                )
            )
        ); 
        $posts = get_posts( $args_query );
        return $posts;
    }
    public function nbd_get_variation_form( $product_id ){
        $_product               = wc_get_product( $product_id );
        $attributes             = $_product->get_variation_attributes();
        $available_variations   = $_product->get_available_variations();
        $selected_attributes    = $_product->get_default_attributes();
        $attribute_keys         = array_keys( $attributes );  
        ob_start();
        nbdesigner_get_template('product-variation-form.php', array(
            'attributes'            => $attributes,
            'available_variations'  => $available_variations,
            'selected_attributes'   => $selected_attributes,
            'attribute_keys'        => $attribute_keys,
            '_product'              => $_product
         ));
        $html = ob_get_clean();
        return $html;
    }
    public function nbdesigner_get_product_info() {
        if (!wp_verify_nonce($_POST['nonce'], 'save-design') && NBDESIGNER_ENABLE_NONCE) {
            die('Security error');
        }
        $data               = array();
        $product_id         = (isset($_POST['product_id']) &&  $_POST['product_id'] != '') ? absint($_POST['product_id']) : '';
        $variation_id       = (isset($_POST['variation_id']) &&  $_POST['variation_id'] != '') ? absint($_POST['variation_id']) : 0;
        $template_id        = (isset($_POST['template_id']) &&  $_POST['template_id'] != '') ? $_POST['template_id'] : '';
        $need_templates     = (isset($_POST['need_templates']) &&  $_POST['need_templates'] != '') ? true : false;
        if( $template_id != '' ){
            $path                   = NBDESIGNER_CUSTOMER_DIR . '/' . $template_id;
            $data['fonts']          = nbd_get_data_from_json( $path . '/used_font.json' );
            $data['design']         = nbd_get_data_from_json( $path . '/design.json' );
            $data['config']         = nbd_get_data_from_json( $path . '/config.json' );
            $data['design_id']      = $template_id;
            nbd_update_hit_template( false, $template_id );
        }else {
            if($product_id) $product_id = get_wpml_original_id($product_id);
            if($variation_id) $variation_id = get_wpml_original_id($variation_id);
            $data = nbd_get_product_info( $product_id, $variation_id, '', 'new', '', '', $need_templates );  
        } 
        if( isset( $_POST['attach_product_info'] ) && $_POST['attach_product_info'] == 1 ){
            if( isset( $_POST['product_type'] ) && $_POST['product_type'] == 'variable' ){
                $data['variation_form'] = $this->nbd_get_variation_form($product_id);
            }
            $data['product_info'] = $this->_get_product_description($product_id);
        }
        echo json_encode($data);
        wp_die();
    }
    public function nbdesigner_save_partial_customer_design(){
        if ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) {
            die( 'Security error' );
        } 
        $result             = array();
        $force_new_folder   = true;
        $result['flag']     = 0;
        $sid                = esc_html( $_POST['sid'] );
        $pid                = absint( $_POST['product_id'] );
        $flag               = absint( $_POST['flag'] );
        $order              = 'nb_order';
        if( !$flag ){
            $config = str_replace( '\"', '"', $_POST['config'] );
            $fonts  = str_replace( '\"', '"', $_POST['fonts'] );
        }
        if( isset( $_POST['image'] ) ){
            $data = $_POST['image']['img'];
            if( !$flag ){
                $json = str_replace( '\"', '"', $_POST['image']['json'] );
                $json = str_replace( '\\\\', '\\', $json );
            }
        } else{
            die( 'Incorect data!' );
        }
        $uid = get_current_user_id(); 
        $iid = $sid;
        if($uid > 0) $iid = $uid;
        $path       = NBDESIGNER_CUSTOMER_DIR . '/' . $iid . '/' . $order . '/' . $pid;
        $path_thumb = $path . '/thumbs';
        if(!$flag){      
            $json_file          = $path . '/design.json';
            $json_config        = $path . '/config.json';
            $json_used_font     = $path . '/used_font.json';
            file_put_contents( $json_file, $json );
            file_put_contents( $json_config, $config );
            file_put_contents( $json_used_font, $fonts );
        }
        if( file_exists( $path ) && !$flag ){
            Nbdesigner_IO::delete_folder( $path );
        }    
        if ( !file_exists( $path ) ) {
            if ( wp_mkdir_p( $path ) ) {
                if ( !file_exists( $path_thumb ) )
                    if ( !wp_mkdir_p( $path_thumb ) ) {
                        $result['mes'] = esc_html__( 'Your server not allow creat folder', 'web-to-print-online-designer' );
                    }
            } else {
                $result['mes'] = esc_html__( 'Your server not allow creat folder', 'web-to-print-online-designer' );
            }
        }
        $configs        = unserialize( get_post_meta( $pid, '_designer_setting', true ) );
        $thumb_width    = nbdesigner_get_option( 'nbdesigner_thumbnail_width' );
        $thumb_height   = nbdesigner_get_option( 'nbdesigner_thumbnail_height' );
        $thumb_quality  = nbdesigner_get_option( 'nbdesigner_thumbnail_quality' );
        if( $configs[$flag]["area_design_width"] > $configs[$flag]["area_design_height"] ){
            $thumb_height = round( $thumb_width * $configs[$flag]['area_design_height'] / $configs[$flag]['area_design_width'] );
        }else {
            $thumb_width = round( $thumb_height * $configs[$flag]['area_design_width'] / $configs[$flag]['area_design_height'] );
        }
        foreach ( $data as $key => $val ) {
            $temp       = explode( ';base64,', $val );
            $buffer     = base64_decode( $temp[1] );
            $full_name  = $path . '/' . $key . '.png';
            if ( Nbdesigner_IO::save_data_to_file( $full_name, $buffer ) ) {
                $image = wp_get_image_editor( $full_name );
                if ( !is_wp_error( $image ) ) {
                    $thumb_file = $path_thumb . '/' . $key . '.png';
                    $image->resize( $thumb_width, $thumb_height, 1 );
                    $image->set_quality( $thumb_quality );
                    if ( $image->save( $thumb_file, 'image/png' ) ) $result['link'] = Nbdesigner_IO::secret_image_url( $thumb_file );
                }
            } else {
                $result['mes'] = esc_html__( 'Your server not allow writable file', 'web-to-print-online-designer' );
            }
        }
        $result['flag'] = "success";
        echo json_encode( $result );
        wp_die();
    }
    private function store_design_data( $nbd_item_key, $data, $product_config, $product_option, $product_upload ){
        $path = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key;
        if( file_exists( $path . '_old' ) ) Nbdesigner_IO::delete_folder( $path . '_old' );
        if( file_exists( $path ) ) rename( $path, $path . '_old' );
        if ( wp_mkdir_p( $path ) ) {
            // custom charmingprint
            // var_dump(file_exists( $path . '_old/od_option.json'));
            if( file_exists( $path . '_old/od_option.json') ) {
                file_put_contents( $path . '/od_option.json' , file_get_contents( $path . '_old/od_option.json' ) );
            }
            
            file_put_contents( $path . '/product.json', serialize( $product_config ) );
            file_put_contents( $path . '/option.json', $product_option );
            file_put_contents( $path . '/upload.json', $product_upload );

            $partial_path = $path . '/part';
            wp_mkdir_p( $partial_path );

            foreach ( $data as $key => $val ) {
                if( $key == 'design' ){
                    $full_name = $path . '/design.json';
                }else if( $key == 'used_font' ){
                    $full_name = $path . '/used_font.json';
                }else if( $key == 'config' ){
                    $full_name = $path . '/config.json';
                }else if( $key != 'template_thumb' ){
                    if( strpos( $key, '_svg' ) > 0 ){
                        $ext = 'svg';
                    }else {
                        $ext = explode( '/', $val["type"] )[1];
                    }
                    if( strpos( $key, '_svg_part' ) > 0 ){
                        $full_name = $partial_path . '/' . $key . '.' . $ext;
                    } else {
                        $full_name = $path . '/' . $key . '.' . $ext;
                    }
                }
                if( $key != 'template_thumb' ){
                    if ( !move_uploaded_file( $val["tmp_name"], $full_name ) ) {
                        return false;
                    }
                }
            }
        } else {
            Nbdesigner_DebugTool::wirite_log( 'Your server not allow creat folder', 'save design' );
            rename( $path.'_old', $path );
            return false;
        }
        return true;
    }
    private function create_preview_design( $path_src, $path_dst, $product_config, $width, $height, $limit = false ){
        if( !file_exists( $path_dst ) ){
            wp_mkdir_p( $path_dst );
        }
        $scale = $width / 500;
        $count = 0;
        foreach ( $product_config as $key => $val ){
            $p_img          = $path_src . '/frame_' . $key . '.png';
            $design_format  = 'png';
            if( file_exists( $path_src . '/frame_' . $key . '.jpeg' ) ){
                $p_img          = $path_src . '/frame_' . $key . '.jpeg';
                $design_format  = 'jpeg';
            }
            if( file_exists( $p_img ) ){
                $bg_width   = (int) round( $val["img_src_width"] * $scale );
                $bg_height  = (int) round( $val["img_src_height"] * $scale );
                $ds_width   = (int) round( $val["area_design_width"] * $scale );
                $ds_height  = (int) round( $val["area_design_height"] * $scale );
                if( $design_format == 'png' ){
                    $image_design = NBD_Image::nbdesigner_resize_imagepng( $p_img, $ds_width, $ds_height );
                }else{
                    $image_design = NBD_Image::nbdesigner_resize_imagejpg( $p_img, $ds_width, $ds_height );
                }
                $image_product_ext = pathinfo( $val["img_src"] );
                if( $val["bg_type"] == 'image' ){
                    $path_img_src  = Nbdesigner_IO::convert_url_to_path( $val["img_src"] );
                    if( $image_product_ext['extension'] == "png" ){
                        $image_product = NBD_Image::nbdesigner_resize_imagepng( $path_img_src, $bg_width, $bg_height );
                    }else{
                        $image_product = NBD_Image::nbdesigner_resize_imagejpg( $path_img_src, $bg_width, $bg_height );
                    }
                }
                if( $val["show_overlay"] == '1' ){
                    $path_img_overlay   = Nbdesigner_IO::convert_url_to_path( $val["img_overlay"] );
                    $overlay_ext        = pathinfo( $val["img_overlay"] );
                    $over_ext           = strtolower( $overlay_ext['extension'] );
                    if( $over_ext == "png" ){
                        $image_overlay = NBD_Image::nbdesigner_resize_imagepng( $path_img_overlay, $bg_width, $bg_height );
                    }else if( $over_ext == "jpg" || $over_ext == "jpeg" ){
                        $image_overlay = NBD_Image::nbdesigner_resize_imagejpg( $path_img_overlay, $bg_width, $bg_height );
                    }else{
                        $val["show_overlay"] = '0';
                    }
                }
                if( $design_format == 'png' ){
                    $image = imagecreatetruecolor( $bg_width, $bg_height );
                    imagesavealpha( $image, true );
                    $color = imagecolorallocatealpha( $image, 255, 255, 255, 127 );
                    imagefill( $image, 0, 0, $color );
                }else{
                    $image  = imagecreatetruecolor($bg_width, $bg_height);
                    $bg     = imagecolorallocate( $image, 255, 255, 255 );
                    imagefilledrectangle($image,0,0,$bg_width,$bg_height,$bg);
                }
                if( $val["bg_type"] == 'image' ){
                    imagecopy( $image, $image_product, 0, 0, 0, 0, $bg_width, $bg_height );
                } else if( $val["bg_type"] == 'color' ){
                    $show_bg_color = true;
                    if( isset( $val["areaDesignShape"] ) && $val["areaDesignShape"] ){
                        $show_bg_color = false;
                    }
                    if( $val["show_overlay"] == '1' && $image_overlay ){
                        $show_bg_color = true;
                    }
                    if( $show_bg_color ){
                        $_color = hex_code_to_rgb( $val["bg_color_value"] );
                        $color  = imagecolorallocate( $image, $_color[0], $_color[1], $_color[2] );
                        imagefilledrectangle( $image, 0, 0, $bg_width, $bg_height, $color );
                    }
                } else if( $design_format == 'jpeg' ) {
                    $color = imagecolorallocate( $image, 255, 255, 255 );
                    imagefilledrectangle( $image, 0, 0, $bg_width, $bg_height, $color );
                }
                imagecopy( $image, $image_design, (int) round( ( $val["area_design_left"] - $val["img_src_left"] ) * $scale ), (int) round( ( $val["area_design_top"] - $val["img_src_top"] ) * $scale ), 0, 0, $ds_width, $ds_height );
                if( $val["show_overlay"] == '1' && $image_overlay ){
                    imagecopy( $image, $image_overlay, 0, 0, 0, 0, $bg_width, $bg_height );
                }
                if( $design_format == 'png' ){
                    imagepng( $image, $path_dst . '/frame_' . $key . '.png' );
                }else{
                    imagejpeg( $image, $path_dst . '/frame_' . $key . '.jpeg' );
                }
                imagedestroy( $image );
                imagedestroy( $image_design );
                $count++;
            }
            if( $limit && $count == $limit ) break;
        }
    }
    private function create_preview_design2($path_src, $path_dst, $product_config, $width, $height, $limit = false) {
        if (!file_exists($path_dst)) {
            wp_mkdir_p($path_dst);
        }
        $scale = 900 / 500;
        $count = 0;
        foreach ($product_config as $key => $val) {
            $p_img = $path_src . '/frame_' . $key . '.png';
            $design_format = 'png';
            if (file_exists($path_src . '/frame_' . $key . '.jpeg')) {
                $p_img = $path_src . '/frame_' . $key . '.jpeg';
                $design_format = 'jpeg';
            }
            if (file_exists($p_img)) {
                $bg_width = (int) round($val["img_src_width"] * $scale);
                $bg_height = (int) round($val["img_src_height"] * $scale);
                $ds_width = (int) round($val["area_design_width"] * $scale);
                $ds_height = (int) round($val["area_design_height"] * $scale);
                if ($design_format == 'png') {
                    $image_design = NBD_Image::nbdesigner_resize_imagepng($p_img, $ds_width, $ds_height);
                } else {
                    $image_design = NBD_Image::nbdesigner_resize_imagejpg($p_img, $ds_width, $ds_height);
                }
                $image_product_ext = pathinfo($val["img_src"]);
                if ($val["bg_type"] == 'image') {
                    $path_img_src = Nbdesigner_IO::convert_url_to_path($val["img_src"]);
                    if ($image_product_ext['extension'] == "png") {
                        $image_product = NBD_Image::nbdesigner_resize_imagepng($path_img_src, $bg_width, $bg_height);
                    } else {
                        $image_product = NBD_Image::nbdesigner_resize_imagejpg($path_img_src, $bg_width, $bg_height);
                    }
                }
                if ($val["show_overlay"] == '1') {
                    $path_img_overlay = Nbdesigner_IO::convert_url_to_path($val["img_overlay"]);
                    $overlay_ext = pathinfo($val["img_overlay"]);
                    $over_ext = strtolower($overlay_ext['extension']);
                    if ($over_ext == "png") {
                        $image_overlay = NBD_Image::nbdesigner_resize_imagepng($path_img_overlay, $ds_width, $ds_height);
                    } else if ($over_ext == "jpg" || $over_ext == "jpeg") {
                        $image_overlay = NBD_Image::nbdesigner_resize_imagejpg($path_img_overlay, $ds_width, $ds_height);
                    } else {
                        $val["show_overlay"] = '0';
                    }
                }
                if ($design_format == 'png') {
                    $image = imagecreatetruecolor($bg_width, $bg_height);
                    imagesavealpha($image, true);
                    $color = imagecolorallocatealpha($image, 255, 255, 255, 127);
                    imagefill($image, 0, 0, $color);
                } else {
                    $image = imagecreatetruecolor($bg_width, $bg_height);
                    $bg = imagecolorallocate($image, 255, 255, 255);
                    imagefilledrectangle($image, 0, 0, $bg_width, $bg_height, $bg);
                }
                if ($val["bg_type"] == 'image') {
                    $val["include_background"] == 1 ? imagecopy($image, $image_product, 0, 0, 0, 0, $bg_width, $bg_height) : "";
                } else if ($val["bg_type"] == 'color') {
                    $_color = hex_code_to_rgb($val["bg_color_value"]);
                    $color = imagecolorallocate($image, $_color[0], $_color[1], $_color[2]);
                    imagefilledrectangle($image, 0, 0, $bg_width, $bg_height, $color);
                } else if ($design_format == 'jpeg') {
                    $color = imagecolorallocate($image, 255, 255, 255);
                    imagefilledrectangle($image, 0, 0, $bg_width, $bg_height, $color);
                }
                imagecopy($image, $image_design, (int) round(($val["area_design_left"] - $val["img_src_left"]) * $scale), (int) round(($val["area_design_top"] - $val["img_src_top"]) * $scale), 0, 0, $ds_width, $ds_height);
                if ($val["show_overlay"] == '1' && $image_overlay) {
                    imagecopy($image, $image_overlay, (int) round(($val["area_design_left"] - $val["img_src_left"]) * $scale), (int) round(($val["area_design_top"] - $val["img_src_top"]) * $scale), 0, 0, $ds_width, $ds_height);
                }
                if ($design_format == 'png') {
                    imagepng($image, $path_dst . '/frame_' . $key . '.png');
                } else {
                    imagejpeg($image, $path_dst . '/frame_' . $key . '.jpeg');
                }
                imagedestroy($image);
                imagedestroy($image_design);
                $count++;
            }
            if ($limit && $count == $limit) {
                break;
            }

        }
    }
    public function create_mockup_preview( $path, $option, $product_config ){
        $mockup_images = array();
        if( isset( $option['mockup_preview'] ) ){
            $mockup_path = $path . '/mockup_preview';
            if( !file_exists( $mockup_path ) ){
                wp_mkdir_p( $mockup_path );
            }
            $preview_images = Nbdesigner_IO::get_list_images( $path.'/preview', 1 );
            foreach( $option['mockup_preview'] as $key => $mockup ){
                $src = get_attached_file( $mockup['sid'] );
                if( $src ){
                    $name       = sanitize_file_name($mockup['name']);
                    $m_path     = $mockup_path . '/' . $name . '.png';
                    $image      = imagecreatetruecolor( 1000, 1000 );
                    $color      = imagecolorallocatealpha( $image, 255, 255, 255, 127 );
                    imagefill( $image, 0, 0, $color );
                    $ext = strtolower( pathinfo($src, PATHINFO_EXTENSION ) );
                    if( $ext == 'jpg' || $ext == 'jpeg' ){
                        $s_image = imagecreatefromjpeg( $src );
                    }else{
                        $s_image = imagecreatefrompng( $src );
                    }
                    list($width, $height) = getimagesize( $src );
                    imagecopyresized($image, $s_image, 2 * $mockup['s_left'], 2 * $mockup['s_top'], 0, 0, 2 * $mockup['s_width'], 2 * $mockup['s_height'], $width, $height);
                    list($p_width, $p_height) = getimagesize($preview_images[0]);
                    $scaleX = 2 * $mockup['width'] / $p_width;
                    $scaleY = 2 * $mockup['height'] / $p_height;
                    if( ($p_width / $p_height) > ($mockup['width'] / $mockup['height']) ){
                        $new_width = 2 * $mockup['width'];
                        $new_height = $p_height * $scaleX;
                    }else{
                        $new_height = 2 * $mockup['height'];
                        $new_width = $p_width * $scaleY;
                    }
                    switch( $mockup['anchor'] ){
                        case 'nw':
                            $new_top = 2 * $mockup['top'];
                            $new_left = 2 * $mockup['left'];
                            break;
                        case 'n':
                            $new_top = 2 * $mockup['top'];
                            $new_left = 2 * $mockup['left'] + $mockup['width'] - $new_width / 2;
                            break;
                        case 'ne':
                            $new_top = 2 * $mockup['top'];
                            $new_left = 2 * $mockup['left'] + 2 * $mockup['width'] - $new_width;
                            break;
                        case 'w':
                            $new_left = 2 * $mockup['left'];
                            $new_top = 2 * $mockup['top'] + $mockup['height'] - $new_height / 2;
                            break;
                        case 'c':
                            $new_left = 2 * $mockup['left'] + $mockup['width'] - $new_width / 2;
                            $new_top = 2 * $mockup['top'] + $mockup['height'] - $new_height / 2;
                            break;
                        case 'e':
                            $new_left = 2 * $mockup['left'] + 2 * $mockup['width'] - $new_width;
                            $new_top = 2 * $mockup['top'] + $mockup['height'] - $new_height / 2;
                            break;
                        case 'sw':
                            $new_left = 2 * $mockup['left'];
                            $new_top = 2 * $mockup['top'] + 2 * $mockup['height'] - $new_height;
                            break;
                        case 's':
                            $new_left = 2 * $mockup['left'] + $mockup['width'] - $new_width / 2;
                            $new_top = 2 * $mockup['top'] + 2 * $mockup['height'] - $new_height;
                            break;
                        case 'se':
                            $new_left = 2 * $mockup['left'] + 2 * $mockup['width'] - $new_width;
                            $new_top = 2 * $mockup['top'] + 2 * $mockup['height'] - $new_height;
                            break;
                    }
                    $p_image = imagecreatefrompng($preview_images[0]);
                    imagecopyresized($image, $p_image, $new_left, $new_top, 0, 0, $new_width, $new_height, $p_width, $p_height);
                    imagepng($image, $m_path );
                    imagedestroy($p_image);
                    imagedestroy($image);
                    imagedestroy($s_image);
                    $mockup_images[] = array(
                        'name'  => $mockup['name'],
                        'src'   => Nbdesigner_IO::wp_convert_path_to_url( $m_path ),
                        'path'  => 'mockup_preview/' . $name . '.png'
                    );
                }
            }
            foreach($preview_images as $image){
                $path_parts = pathinfo($image);
                $key = substr($path_parts['filename'], 6);
                $mockup_images[] = array(
                    'name'  => $product_config[$key]['orientation_name'],
                    'src'   => Nbdesigner_IO::wp_convert_path_to_url( $image ),
                    'path'  => 'preview/' . $path_parts['basename']
                );
            }
        }
        return $mockup_images;
    }
    /**
     * Save design, product configuration into NBDESIGNER_CUSTOMER_DIR <br />
     * Create folder based on the microtime or replace old folder when edit design <br />
     * Store folder name into WC()->session  
     */
    public function nbd_save_cart_design(){
        if ( ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) || !isset( $_POST['product_id'] ) ) {
            die( 'Security error' );
        }
        $result = array(
            'flag'  => 'success'
        );
        do_action( 'before_nbd_save_cart_design', $_POST );
        $product_id         = absint( $_POST['product_id'] );
        $variation_id       = ( isset( $_POST['variation_id'] ) && $_POST['variation_id'] != '' ) ? absint( $_POST['variation_id'] ) : 0;  
        $enable_upload_without_design = ( isset( $_POST['enable_upload_without_design'] ) && $_POST['enable_upload_without_design'] != '') ? absint( $_POST['enable_upload_without_design'] ) : 1;
        /* Save custom design */
        $nbd_item_cart_key  = ( $variation_id > 0 ) ? $product_id . '_' . $variation_id : $product_id;
        $nbd_item_session   = WC()->session->get( 'nbd_item_key_' . $nbd_item_cart_key );
        $nbd_item_key       = isset( $nbd_item_session ) ? $nbd_item_session : substr( md5( uniqid() ), 0, 5 ) . rand( 1, 100 ) . time();
        $path               = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key;
        if( $variation_id > 0 ){
            $product_config     = unserialize( get_post_meta($variation_id, '_designer_variation_setting', true ) );
            $enable_variation   = get_post_meta( $variation_id, '_nbdesigner_variation_enable', true );
            if ( !( $enable_variation && isset( $product_config[0] ) ) ){
                $product_config = unserialize( get_post_meta( $product_id, '_designer_setting', true ) ); 
            }
        } else {
            $product_config = unserialize( get_post_meta( $product_id, '_designer_setting', true ) ); 
        } 
        $product_option     = get_post_meta( $product_id, '_nbdesigner_option', true ); 
        $product_upload     = get_post_meta( $product_id, '_nbdesigner_upload', true ); 
        $add_file           = false;
        $design_id          = ( isset( $_POST['design_id'] ) && $_POST['design_id'] != '' ) ? sanitize_text_field( $_POST['design_id'] ) : '';  
        if( isset( $_POST['task2'] ) && $_POST['task2'] == 'add_file' ) {
            $enable_upload_without_design = 2;
            $add_file = true;
        }
        if( $enable_upload_without_design == 1 ){
            $save_status = $this->store_design_data( $nbd_item_key, $_FILES, $product_config, $product_option, $product_upload );
            $width = absint( nbdesigner_get_option( 'nbdesigner_thumbnail_width', 300 ) ) ? absint( nbdesigner_get_option( 'nbdesigner_thumbnail_width', 300 ) ) : 300;
            if( false != $save_status ){
                $path_config = $path . '/config.json';
                $config = nbd_get_data_from_json( $path_config );
                if( count( $config->product ) ){
                    $product_config = array();
                    foreach( $config->product as $side ){
                        $product_config[] = (array)$side;
                    }
                }; 
                if( isset( $config->custom_dimension ) ){
                    $custom_side        = absint( $config->custom_dimension->side );
                    $custom_width       = $config->custom_dimension->width;
                    $custom_height      = $config->custom_dimension->height;
                    $product_config     = $this->merge_product_config( $product_config, $custom_width, $custom_height, $custom_side );
                }
                if( isset( $config->areaDesignShapes ) ){
                    foreach ( $product_config as $key => $_config ){
                        if( $config->areaDesignShapes[$key] ) $product_config[$key]["areaDesignShape"] = true;
                    }
                }
                $this->create_preview_design( $path, $path.'/preview', $product_config, $width, $width );
                $this->create_preview_design2($path, $path . '/preview2/thumb', $product_config, $width, $width);
                do_action( 'after_nbd_create_preview_design', $nbd_item_key );
                WC()->session->set( 'nbd_item_key_'.$nbd_item_cart_key, $nbd_item_key ); 
            } else {
                $result['flag'] = 'failure';
            }
        }
        /* Save upload design */
        if( isset( $_POST['nbd_file'] ) && $_POST['nbd_file'] != '' ){
            $nbu_item_session = WC()->session->get( 'nbu_item_key_' . $nbd_item_cart_key );
            if( $nbu_item_session ){
                $this->update_files_upload( $_POST['nbd_file'], $nbu_item_session );
//                if( !$add_file ){
//                    WC()->session->set('nbu_item_key_'.$nbd_item_cart_key, $nbu_item_session);
//                }
            }
        }
        if( isset( $_POST['task2'] ) && $_POST['task2'] != '' ){
            /* Destroy nbd_item session and init cart session */
            if( $_POST['task2'] != 'cuz' ){
                $cart_item_key = $_POST['cart_item_key'];
                WC()->session->__unset( 'nbd_item_key_' . $nbd_item_cart_key );
                WC()->session->__unset( 'nbu_item_key_' . $nbd_item_cart_key );
                if( !isset( WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds'] ) ) WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds'] = array();
                if( !$add_file ){
                    WC()->session->set( $cart_item_key . '_nbd', $nbd_item_key );
                    WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds']['nbd'] = $nbd_item_key;
                    if( $design_id ){
                        WC()->cart->cart_contents[ $cart_item_key ]['nbd_design_id'] = $design_id;
                    }
                }
                if( isset( $_POST['nbd_file'] ) && $_POST['nbd_file'] != '' ){
                    WC()->session->set( $cart_item_key . '_nbu', $nbu_item_session );
                    WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds']['nbu'] = $nbu_item_session;

                    $cart_item_keys = WC()->session->get( 'nbd_cart_item_keys' );
                    if( $cart_item_keys ){
                        $cart_item_keys_arr = explode( '|', $cart_item_keys );
                        if( in_array( $cart_item_key, $cart_item_keys_arr ) ){
                            foreach( $cart_item_keys_arr as $cik ){
                                WC()->session->set( $cik . '_nbu', $nbu_item_session );
                                if( !isset( WC()->cart->cart_contents[ $cik ]['nbd_item_meta_ds'] ) ) WC()->cart->cart_contents[ $cik ]['nbd_item_meta_ds'] = array();
                                WC()->cart->cart_contents[ $cik ]['nbd_item_meta_ds']['nbu'] = $nbu_item_session;
                            }
                            WC()->session->__unset( 'nbd_cart_item_keys' );
                        }
                    }
                }
                WC()->cart->set_session();
            }
        }else {
            /* Add to cart directly in custom page */
            $quantity       = ( isset( $_POST['qty'] ) && absint( $_POST['qty'] ) > 0 ) ? absint( $_POST['qty'] ) : 1;
            $product_status = get_post_status( $product_id );
            if ( false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id ) && 'publish' === $product_status ) {
                do_action( 'woocommerce_ajax_added_to_cart', $product_id );
            } else {
                $result['flag'] = 'failure';
                $result['mes']  = __( 'Error adding to the cart', 'web-to-print-online-designer' );
            }
        }
        do_action( 'after_nbd_save_cart_design', $_POST, $result );
        echo json_encode( $result );
        wp_die();
    }
    public function nbd_save_draft_design(){
        if ( ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) || !isset( $_POST['product_id'] ) ) {
            die( 'Security error' );
        }
        $result         = array(
            'flag'  => 0
        );
        $stored         = false;
        $product_id     = absint( $_POST['product_id'] );
        $variation_id   = ( isset($_POST['variation_id'] ) && $_POST['variation_id'] != '') ? absint( $_POST['variation_id'] ) : 0;
        $product_option = get_post_meta( $product_id, '_nbdesigner_option', true ); 
        $draft_folder   = substr( md5( uniqid() ), 0, 5 ) . rand( 1, 100 ) . time();
        if( isset( $_POST['draft_folder'] ) && $_POST['draft_folder'] != '' ){
            $draft_folder   = $_POST['draft_folder'];
            $stored         = true;
        }
        if( $variation_id > 0 ){
            $product_config     = unserialize( get_post_meta( $variation_id, '_designer_variation_setting', true ) );
            $enable_variation   = get_post_meta( $variation_id, '_nbdesigner_variation_enable', true );
            if ( !( $enable_variation && isset( $product_config[0] ) ) ){
                $product_config = unserialize( get_post_meta( $product_id, '_designer_setting', true ) );
            }
        } else {
            $product_config = unserialize( get_post_meta( $product_id, '_designer_setting', true ) );
        }
        $product_upload = get_post_meta( $product_id, '_nbdesigner_upload', true );
        $save_status    = $this->store_design_data( $draft_folder, $_FILES, $product_config, $product_option, $product_upload ); 
        if( $save_status ){
            $path   = NBDESIGNER_CUSTOMER_DIR . '/' . $draft_folder;
            $width  = absint( nbdesigner_get_option( 'nbdesigner_thumbnail_width', 300 ) ) ? absint( nbdesigner_get_option( 'nbdesigner_thumbnail_width', 300 ) ) : 300;
            $this->create_preview_design( $path, $path . '/preview', $product_config, $width, $width );
            if( $stored ){
                $result['flag']         = 'success';
                $result['draft_folder'] = $draft_folder;
            }else{
                global $wpdb;
                $created_date   = new DateTime();
                $user_id        = wp_get_current_user()->ID;
                $table_name     = $wpdb->prefix . 'nbdesigner_mydesigns';
                $re             = $wpdb->insert($table_name, array(
                    'product_id'    => $product_id,
                    'variation_id'  => $variation_id,
                    'price'         => 0,
                    'folder'        => $draft_folder,
                    'user_id'       => $user_id,
                    'publish'       => 1,
                    'created_date'  => $created_date->format( 'Y-m-d H:i:s' )
                ));
                if( $re ){
                    $result['flag']         = 'success';
                    $result['draft_folder'] = $draft_folder;
                }
            }
        }
        wp_send_json($result);
    }
    public function nbd_save_customer_design(){
        if ( ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) || !isset( $_POST['product_id'] ) ) {
            die( 'Security error' );
        }
        $result = array(
            'flag'  => 'failure',
            'link'  => ''
        );
        do_action( 'before_nbd_save_customer_design' );
        /* Get data from $_POST */
        $product_id         = absint( $_POST['product_id'] );
        $product            = wc_get_product( $product_id );
        $product_option     = get_post_meta( $product_id, '_nbdesigner_option', true ); 
        $variation_id       = ( isset( $_POST['variation_id'] ) && $_POST['variation_id'] != '' ) ? absint($_POST['variation_id']) : 0;
        $cart_item_key      = ( isset( $_POST['cart_item_key'] ) && $_POST['cart_item_key'] != '' ) ? sanitize_text_field( $_POST['cart_item_key'] ) : '';
        $task               = ( isset( $_POST['task'] ) && $_POST['task'] != '' ) ? sanitize_text_field( $_POST['task'] ) : 'new';
        $task2              = ( isset( $_POST['task2'] ) && $_POST['task2'] != '' ) ? sanitize_text_field( $_POST['task2'] ) : '';
        $auto_add_to_cart   = ( isset( $_POST['auto_add_to_cart'] ) && $_POST['auto_add_to_cart'] != '' ) ? $_POST['auto_add_to_cart'] : 'no';
        $design_type        = ( isset( $_POST['design_type'] ) && $_POST['design_type'] != '' ) ? wc_clean( $_POST['design_type'] ) : '';
        $generate_mockup    = ( isset( $_POST['generate_mockup'] ) && $_POST['generate_mockup'] != '' ) ? true : false;
        $ui_mode            = ( isset( $_POST['ui_mode'] ) && $_POST['ui_mode'] != '' ) ? wc_clean( $_POST['ui_mode'] ) : '1';
        $nbd_item_cart_key  = ( $variation_id > 0 ) ? $product_id . '_' . $variation_id : $product_id; 
        $type               = $product->get_type();
        $option             = unserialize( $product_option );
        $design_id          = ( isset( $_POST['design_id'] ) && $_POST['design_id'] != '' ) ? sanitize_text_field( $_POST['design_id'] ) : '';
        $switched_product   = ( isset( $_POST['switched_product'] ) && $_POST['switched_product'] != '' ) ? absint( $_POST['switched_product'] ) : '';
        if( $type == 'variable' && isset( $option['bulk_variation'] ) && $option['bulk_variation'] == 1 ){
            $nbd_item_cart_key = $product_id . '_0';
        }
        $result[ 'product_id' ]     = $product_id;
        $result[ 'variation_id' ]   = $variation_id;
        if( isset( $_POST['nbd_item_key'] ) && $_POST['nbd_item_key'] != '' ){
            /* Edit design 
             * In case edit template, $design_type = 'template'
             */
            $nbd_item_key = $_POST['nbd_item_key'];
            if( ( $design_type == '' ) && !nbd_check_cart_item_exist( $cart_item_key ) ) {
                $result['mes'] = esc_html__('Item not exist in cart', 'web-to-print-online-designer');
                nbd_die( $result );
            }
        }else {
            /* Create new design */
            $nbd_item_session = WC()->session->get( 'nbd_item_key_' . $nbd_item_cart_key );
            if( ($task == 'new' && $task2 == 'update' ) || $task == 'create' || !isset( $nbd_item_session ) ){
                $nbd_item_key = substr( md5( uniqid() ), 0, 5 ) . rand( 1, 100 ) . time();
            }else {
                $nbd_item_key = $nbd_item_session;
            }
        }
        $path = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key;
        if( $task == 'new' || $task == 'create' ){
            if( $variation_id > 0 ){
                $product_config     = unserialize( get_post_meta( $variation_id, '_designer_variation_setting', true ) );
                $enable_variation   = get_post_meta( $variation_id, '_nbdesigner_variation_enable', true );
                if ( !( $enable_variation && isset( $product_config[0] ) ) ){
                    $product_config = unserialize( get_post_meta( $product_id, '_designer_setting', true ) ); 
                }
            }else {
                $product_config = unserialize( get_post_meta( $product_id, '_designer_setting', true ) );
            } 

        }else{
            $product_config = unserialize( file_get_contents( $path . '/product.json' ) );
        }
        $att_swatch         = (isset($_POST['att_swatch']) && $_POST['att_swatch'] != '') ? $_POST['att_swatch'] : '';
        $product_config     = apply_filters( 'nbd_save_customer_design_product_config', $product_config, $product_option, $att_swatch );
        $product_upload     = get_post_meta( $product_id, '_nbdesigner_upload', true );
        $save_status        = $this->store_design_data( $nbd_item_key, $_FILES, $product_config, $product_option, $product_upload );
        $width              = absint( nbdesigner_get_option( 'nbdesigner_thumbnail_width' ) ) ? absint( nbdesigner_get_option( 'nbdesigner_thumbnail_width', 300 ) ) : 300;
        // custom charmingprint
        if( $task == 'new' || $task == 'create' ){
            if( isset( $_POST['od_option'] ) ) {
                $od_option = json_decode(stripslashes( $_POST['od_option']) );
                file_put_contents( $path.'/od_option.json', serialize( $od_option ) );
            }
        }
        $result['task'] = $task; // custom charmingpront 22/12
        if( $task == 'create' || $design_type == 'template' ){
            $width = absint( nbdesigner_get_option( 'nbdesigner_template_width', 500 ) ) ? absint( nbdesigner_get_option( 'nbdesigner_template_width', 500 ) ) : 500;
        }
        if( false != $save_status ){
            /* todo edit $product_config if has custom dimension */
            $path_config    = $path . '/config.json';
            $config         = nbd_get_data_from_json( $path_config );
            if( count( $config->product ) ){
                $product_config = array();
                foreach( $config->product as $side ){
                    $product_config[] = (array)$side;
                }
            };
            if( isset( $config->custom_dimension ) ){
                $custom_side        = absint( $config->custom_dimension->side );
                $custom_width       = $config->custom_dimension->width;
                $custom_height      = $config->custom_dimension->height;
                $product_config     = $this->merge_product_config( $product_config, $custom_width, $custom_height, $custom_side );
            }
            if( isset( $config->areaDesignShapes ) ){
                foreach ( $product_config as $key => $_config ){
                    if( $config->areaDesignShapes[$key] ) $product_config[$key]["areaDesignShape"] = true;
                }
            }
            $this->create_preview_design( $path, $path.'/preview', $product_config, $width, $width );
            $this->create_preview_design2($path, $path . '/preview2/thumb', $product_config, $width, $width);
            if( isset( $_POST['share'] ) ){
                Nbdesigner_IO::copy_dir( $path, $path . 's' );
                $result['sfolder'] = $nbd_item_key . 's';
            }
            if( isset( $_POST['switched_product'] ) && $switched_product > 0 ){
                Nbdesigner_IO::copy_dir( $path, $path . 'r' );
                $layout         = nbd_get_product_layout( $switched_product );
                $redirect_url   =  add_query_arg( array(
                    'product_id'    => $switched_product,
                    'view'          => $layout,
                    'reference'     => $nbd_item_key . 'r'
                ),  getUrlPageNBD( 'create' ) );
                $result['redirect_url'] = $redirect_url;
            }
            if( $generate_mockup ){
                $result['mockups'] = $this->create_mockup_preview( $path, $option, $product_config );
            }
            $result['image']    = array();
            $images             = Nbdesigner_IO::get_list_images( $path.'/preview', 1 );
            $images             = nbd_sort_file_by_side( $images );
            foreach( $images as $image ){
                $filename = pathinfo( $image, PATHINFO_FILENAME );
                $result['image'][$filename] = Nbdesigner_IO::wp_convert_path_to_url( $image );
            }
            $result['flag']     = 'success';
            $result['folder']   = $nbd_item_key;
            if( $ui_mode == '1' && nbdesigner_get_option( 'nbdesigner_enable_gallery_api', 'no' ) == 'yes' ){
                $result['gallery']  = array();
                $g_images           = $images;
                ksort( $g_images );
                foreach( $g_images as $key => $image ){
                    list( $width, $height ) = getimagesize( $image );
                    $result['gallery'][] = array(
                        'src'       => Nbdesigner_IO::wp_convert_path_to_url( $image ),
                        'width'     => $width,
                        'height'    => $height,
                        'title'     => isset( $product_config[$key] ) ? $product_config[$key]['orientation_name'] : '',
                        'sizes'     => $width . 'x' . $height
                    );
                }
            }
            if( $task == 'new' ){
                if( $task2 == 'update' ){
                    WC()->session->set( $cart_item_key. '_nbd', $nbd_item_key );
                    if( !isset(WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds']) ) WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds'] = array();
                    WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds']['nbd'] = $nbd_item_key;
                    if( $design_id ){
                        WC()->cart->cart_contents[ $cart_item_key ]['nbd_design_id'] = $design_id;
                    }
                    
                    $cart_item_keys = WC()->session->get( 'nbd_cart_item_keys' );
                    if( $cart_item_keys ){
                        $cart_item_keys_arr = explode( '|', $cart_item_keys );
                        if( in_array( $cart_item_key, $cart_item_keys_arr ) ){
                            foreach( $cart_item_keys_arr as $cik ){
                                WC()->session->set( $cik . '_nbd', $nbd_item_key );
                                if( !isset( WC()->cart->cart_contents[ $cik ]['nbd_item_meta_ds'] ) ) WC()->cart->cart_contents[ $cik ]['nbd_item_meta_ds'] = array();
                                WC()->cart->cart_contents[ $cik ]['nbd_item_meta_ds']['nbd'] = $nbd_item_key;
                                if( $design_id ){
                                    WC()->cart->cart_contents[ $cik ]['nbd_design_id'] = $design_id;
                                }
                            }
                            WC()->session->__unset( 'nbd_cart_item_keys' );
                        }
                    }
                    
                    WC()->cart->set_session();
                    WC()->session->__unset( 'nbd_item_key_' . $nbd_item_cart_key );
                }else{
                    WC()->session->set( 'nbd_item_key_' . $nbd_item_cart_key, $nbd_item_key );
                    if( $design_id ){
                        WC()->session->set( 'nbd_design_id_' . $nbd_item_cart_key, $design_id );
                    }
                }
            }
            if( $task == 'create' ){
                if( $design_type == 'art' ){
                    My_Design_Endpoint::nbdesigner_insert_table_my_design( $product_id, $variation_id, $nbd_item_key );
                } else {
                    if( !can_edit_nbd_template() ){
                        $result['mes'] = esc_html__( 'You have not permission to create or edit template', 'web-to-print-online-designer' ); 
                        echo json_encode( $result );
                        wp_die();
                    }else{
                        $this->nbdesigner_insert_table_templates( $product_id, $variation_id, $nbd_item_key, 0, 1, 0 );
                    }
                }
            }
            if( $task == 'new' && $auto_add_to_cart == 'yes' ){
                if( nbd_add_to_cart( $product_id, $variation_id, 1 ) ){
                    $result['added'] = 1;
                }
            }
            if( $task == 'edit' && isset( $_POST['order_id'] ) && $design_type == 'edit_order' ){
                $order_id = absint( $_POST['order_id'] );
                $order = wc_get_order($order_id);
                if($order) {
                    $order->update_meta_data('_nbdesigner_order_changed', 1);
                    $order->save();
                }
            }
            if( $task == 'edit' && ( isset( $_POST['cart_item_key'] ) && $_POST['cart_item_key'] != '' ) ){
                if( isset( $_POST['qty'] ) && absint( $_POST['qty'] ) > 0 ){
                    global $woocommerce;
                    $woocommerce->cart->set_quantity( $cart_item_key, absint( $_POST['qty'] ) );
                }
            }
            if( isset($_POST['color_selected']) ) {
                WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta']['options']['color_selected'] = $_POST['color_selected'];
                WC()->cart->set_session();
            }
        }
        do_action( 'after_nbd_save_customer_design', $result );
        $result = apply_filters( 'filter_after_nbd_save_customer_design', $result );
        echo json_encode( $result );
        wp_die();
    }
    public function nbd_remove_design_and_file(){
        if ( ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) || !isset( $_POST['product_id'] ) ) {
            die( 'Security error' );
        }
        $product_id         = absint( $_POST['product_id'] );
        $variation_id       = ( isset( $_POST['variation_id'] ) && $_POST['variation_id'] != '' ) ? absint( $_POST['variation_id'] ) : 0; 
        $nbd_item_cart_key  = ( $variation_id > 0 ) ? $product_id . '_' . $variation_id : $product_id; 
        WC()->session->__unset( 'nbd_item_key_'.$nbd_item_cart_key );
        WC()->session->__unset( 'nbu_item_key_'.$nbd_item_cart_key );
        echo 'success';
        wp_die(); 
    }
    public function merge_product_config( $product_config, $width, $height, $number_side ){
        foreach ( $product_config as $key => $side ) {
            $product_config[$key]["real_width"]     = $product_config[$key]["product_width"] = $width;
            $product_config[$key]["real_height"]    = $product_config[$key]["product_height"] = $height;
            if( $width >= $height ) {
                $ratio = 500 / $width;
                $product_config[$key]["img_src_width"]          = $product_config[$key]["area_design_width"]    = 500;
                $product_config[$key]["img_src_left"]           = $product_config[$key]["area_design_left"]     = 0;
                $product_config[$key]["area_design_height"]     = $product_config[$key]["img_src_height"]       = round( $ratio * $height );
                $product_config[$key]["img_src_top"]            = $product_config[$key]["area_design_top"]      = round( 250 - $product_config[$key]["area_design_height"] / 2 );
            }else {
                $ratio = 500 / $height;
                $product_config[$key]["img_src_height"]     = $product_config[$key]["area_design_height"]   = 500;
                $product_config[$key]["img_src_top"]        = $product_config[$key]["area_design_top"]      = 0;
                $product_config[$key]["area_design_width"]  = $product_config[$key]["img_src_width"]        = round( $ratio * $width );
                $product_config[$key]["img_src_left"]       = $product_config[$key]["area_design_left"]     = round( 250 - $product_config[$key]["area_design_width"] / 2 );
            }
        }
        $len = count( $product_config );
        if( $len >= $number_side ){
            array_splice($product_config, $number_side, $len - $number_side);
        }else {
            for( $i = $len; $i < $number_side; $i++ ){
                $product_config[$i] = $product_config[0];
            }
        } 
        return $product_config;
    }
    public function nbdesigner_user_role(){
        $capabilities = array(
            2    => 'manage_nbd_art',
            1    => 'manage_nbd_tool',
            3    => 'manage_nbd_language',
            4    => 'manage_nbd_template',
            5    => 'manage_nbd_product',
            6    => 'manage_nbd_analytic',
            7    => 'manage_nbd_design_store',
            8    => 'manage_nbd_font',
            9    => 'export_nbd_font',
            10   => 'export_nbd_art',
            11   => 'export_nbd_product',
            12   => 'export_nbd_language',
            13   => 'import_nbd_font',
            14   => 'import_nbd_art',
            15   => 'import_nbd_product',
            16   => 'import_nbd_language',
            17   => 'edit_nbd_font',
            18   => 'edit_nbd_art',
            19   => 'edit_nbd_language',
            20   => 'edit_nbd_template',
            21   => 'delete_nbd_font',
            22   => 'delete_nbd_art',
            23   => 'delete_nbd_language',
            24   => 'delete_nbd_template',
            25   => 'sell_nbd_design',
            26   => 'update_nbd_data',
            27   => 'manage_nbd_setting'
        );
        $admin_role = get_role('administrator');
        if (null != $admin_role) {
            foreach ($capabilities as $cap){
                $admin_role->add_cap($cap);
            }
        }
        $shop_manager_role = get_role('shop_manager');
        if (null != $shop_manager_role) {
            foreach ($capabilities as $cap){
                $shop_manager_role->add_cap($cap);
            }
        }
        $shop_capabilities = $shop_manager_role->capabilities;
        $shop_capabilities['export_nbd_font']       = false;
        $shop_capabilities['export_nbd_art']        = false;
        $shop_capabilities['export_nbd_product']    = false;
        $shop_capabilities['export_nbd_language']   = false;
        $shop_capabilities['import_nbd_font']       = false;
        $shop_capabilities['import_nbd_art']        = false;
        $shop_capabilities['import_nbd_product']    = false;
        $shop_capabilities['import_nbd_language']   = false;
        $shop_capabilities['edit_nbd_font']         = false;
        $shop_capabilities['edit_nbd_language']     = false;
        $shop_capabilities['edit_nbd_template']     = false;
        $shop_capabilities['edit_nbd_art']          = false;
        $shop_capabilities['delete_nbd_font']       = false;
        $shop_capabilities['delete_nbd_art']        = false;
        $shop_capabilities['delete_nbd_language']   = false;
        $shop_capabilities['delete_nbd_template']   = false;
        $shop_capabilities['update_nbd_data']       = false;
        $nbd_viewer = add_role('nbdesigner_viewer', 'NBDesigner Viewer', $shop_manager_role->capabilities);
        if ( null === $nbd_viewer ) {
            $nbd_viewer = get_role('nbdesigner_viewer');
            $nbd_viewer->remove_cap('export_nbd_font');
            $nbd_viewer->remove_cap('export_nbd_art');
            $nbd_viewer->remove_cap('export_nbd_product');
            $nbd_viewer->remove_cap('export_nbd_language');
            $nbd_viewer->remove_cap('import_nbd_font');
            $nbd_viewer->remove_cap('import_nbd_art');
            $nbd_viewer->remove_cap('import_nbd_product');
            $nbd_viewer->remove_cap('import_nbd_language');
            $nbd_viewer->remove_cap('edit_nbd_font');
            $nbd_viewer->remove_cap('edit_nbd_art');
            $nbd_viewer->remove_cap('edit_nbd_language');
            $nbd_viewer->remove_cap('edit_nbd_template');
            $nbd_viewer->remove_cap('delete_nbd_font');
            $nbd_viewer->remove_cap('delete_nbd_art');
            $nbd_viewer->remove_cap('delete_nbd_language');
            $nbd_viewer->remove_cap('delete_nbd_template');
            $nbd_viewer->remove_cap('update_nbd_data');
        }
    }
    /**
     * Insert table templates
     * @since 1.5.0
     * 
     * @global type $wpdb
     * @param int $product_id
     * @param varchar $folder
     * @param int $priority
     * @param int $publish
     * @param int $private
     */
    private function nbdesigner_insert_table_templates( $product_id, $variation_id, $folder, $priority, $publish = 1, $private = 0 ){
        global $wpdb;
        $created_date   = new DateTime();
        $user_id        = wp_get_current_user()->ID;
        $table_name     = $wpdb->prefix . 'nbdesigner_templates';
        $publish        = !nbd_check_publish_design_permission( $user_id ) ? 0 : $publish;
        $wpdb->insert( $table_name, array(
            'product_id'    => $product_id,
            'variation_id'  => $variation_id,
            'folder'        => $folder,
            'user_id'       => $user_id,
            'created_date'  => current_time( 'mysql' ),
            'publish'       => $publish,
            'private'       => $private,
            'priority'      => $priority
        ) );
        return true;
    }
    public function nbdesigner_save_webcam_image(){
        if ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) {
            die( 'Security error' );
        }
        $result     = array();
        $img        = $_POST['image'];
        $data       = base64_decode( $img );
        $full_name  = NBDESIGNER_DATA_DIR . '/temp/' . time() .'.png';
        $success    = file_put_contents( $full_name, $data );
        if($success){
            $result['flag'] = 'success';
            $result['url']  = Nbdesigner_IO::wp_convert_path_to_url( $full_name );
        }else{
            $result['flag'] = 'false';
        }
        echo json_encode( $result );
        wp_die();
    }
    private function nbdesigner_create_thumbnail_design( $from_path, $to_path, $pid, $vid = 0, $_width = 500, $_height = 500 ){
        if($vid > 0){
            $configs = unserialize( get_post_meta( $vid, '_designer_variation_setting', true ) );
            if( !isset( $configs[0] ) ){
                $configs = unserialize( get_post_meta( $pid, '_designer_setting', true ) );
            }
        }else {
            $configs = unserialize( get_post_meta( $pid, '_designer_setting', true ) );
        }
        $path_preview = $to_path;
        if( !file_exists( $path_preview ) ){
            wp_mkdir_p( $path_preview );
        }
        foreach ( $configs as $key => $val ){
            $p_img = $from_path . '/frame_' . $key . '.png';
            if( file_exists( $p_img ) ){
                $image_design       = NBD_Image::nbdesigner_resize_imagepng( $p_img, $val["area_design_width"], $val["area_design_height"] );
                $image_product_ext  = pathinfo( $val["img_src"] );
                if( $val["bg_type"] == 'image' ){
                    if( $image_product_ext['extension'] == "png" ){
                        $image_product = NBD_Image::nbdesigner_resize_imagepng( $val["img_src"], $val["img_src_width"], $val["img_src_height"] );
                    }else{
                        $image_product = NBD_Image::nbdesigner_resize_imagejpg( $val["img_src"], $val["img_src_width"], $val["img_src_height"] );
                    }
                }
                $image = imagecreatetruecolor( $_width, $_height );
                imagesavealpha( $image, true );
                $color = imagecolorallocatealpha( $image, 255, 255, 255, 127 );
                imagefill( $image, 0, 0, $color );
                if($val["bg_type"] == 'image'){
                    imagecopy( $image, $image_product, $val["img_src_left"], $val["img_src_top"], 0, 0, $val["img_src_width"], $val["img_src_height"] );
                } else if( $val["bg_type"] == 'color' ){
                    $_color = hex_code_to_rgb( $val["bg_color_value"] );
                    $color  = imagecolorallocate( $image, $_color[0], $_color[1], $_color[2] );
                    imagefilledrectangle( $image, $val["img_src_left"], $val["img_src_top"], $val["img_src_left"] + $val["img_src_width"], $val["img_src_top"] + $val["img_src_height"], $color );
                }
                imagecopy( $image, $image_design, $val["area_design_left"], $val["area_design_top"], 0, 0, $val["area_design_width"], $val["area_design_height"] );
                imagepng( $image, $path_preview . '/frame_' . $key . '.png' );
                imagedestroy( $image );
                imagedestroy( $image_design );
            }
        }
    }
    public function set_nbd_order_data( $order_id ) {
        global $woocommerce;
        $items      = $woocommerce->cart->get_cart();
        $has_nbd    = false;
        $has_nbu    = false;
        foreach( $items as $item => $values ){
            /* custom design */
            $nbd_session = WC()->session->get( $item . '_nbd' );
            // if( !$nbd_session ){
            //     if( isset( $values['nbd_item_meta_ds'] ) && isset( $values['nbd_item_meta_ds']['nbd'] ) ){
            //         $nbd_session = $values['nbd_item_meta_ds']['nbd'];
            //     }
            // }
            if( $nbd_session ){
                WC()->session->__unset( $item . '_nbd' ); 
                $src = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_session;
                $dst = NBDESIGNER_CUSTOMER_DIR . '/' . $order_id . '_' . $nbd_session;
                if( $nbd_session != '' && file_exists( $src ) ){
                    Nbdesigner_IO::copy_dir( $src, $dst );
                    $has_nbd = true;
                }
            }
            /* upload design */
            $nbu_session = WC()->session->get( $item . '_nbu' );
            // if( !$nbu_session ){
            //     if( isset( $values['nbd_item_meta_ds'] ) && isset( $values['nbd_item_meta_ds']['nbu'] ) ){
            //         $nbu_session = $values['nbd_item_meta_ds']['nbu'];
            //     }
            // }
            if( isset( $nbu_session ) ){
                WC()->session->__unset( $item . '_nbu' ); 
                $has_nbu = true;
            }
            WC()->session->__unset( $item . '_nbd_initial_price' );
        }
        $need_save = false;
        $order = wc_get_order($order_id);
        if( $has_nbd ) {
            $order->add_meta_data('_nbdesigner_order_changed', 1);
            $order->add_meta_data('_nbd', 1);
            $need_save = true;
        }
        if( $has_nbu ) {
            $order->add_meta_data('_nbdesigner_upload_order_changed', 1);
            $order->add_meta_data('_nbu', 1);
            $need_save = true;
        }
        if($need_save) {
            $order->save();
        }
    }
    public function nbdesigner_display_posts_design( $column, $post_id ) {
        if ( $column == 'design' ) {
            $is_design = get_post_meta( $post_id, '_nbdesigner_enable', true );
            echo '<input type="checkbox" disabled', ( $is_design ? ' checked' : '' ), '/>';
        }
    }
    public function nbdesigner_add_design_column( $columns ) {
        return array_merge( $columns, array( 'design' => esc_html__( 'Design', 'web-to-print-online-designer' ) ) );
    }
    public function render_cart( $title = null, $cart_item = null, $cart_item_key = null ) {
        if ($cart_item_key && ( is_cart() || is_checkout() )) {
            $nbd_session = WC()->session->get($cart_item_key . '_nbd');
            $nbu_session = WC()->session->get($cart_item_key . '_nbu');
            if( isset($cart_item['nbd_item_meta_ds']) ){
                if( isset($cart_item['nbd_item_meta_ds']['nbd']) ) $nbd_session = $cart_item['nbd_item_meta_ds']['nbd'];
                if( isset($cart_item['nbd_item_meta_ds']['nbu']) ) $nbu_session = $cart_item['nbd_item_meta_ds']['nbu'];
            }
            $_show_design                   = nbdesigner_get_option('nbdesigner_show_in_cart', 'yes');
            $_show_design                   = apply_filters( 'nbd_show_design_section_in_cart', $_show_design, $cart_item );
            $enable_edit_design             = nbdesigner_get_option('nbdesigner_show_button_edit_design_in_cart', 'yes') == 'yes' ? true : false;
            $show_edit_link                 = apply_filters('nbd_show_edit_design_link_in_cart', $enable_edit_design, $cart_item);
            $product_id                     = $cart_item['product_id'];
            $variation_id                   = $cart_item['variation_id'];
            $product_id                     = get_wpml_original_id( $product_id );
            $is_nbdesign                    = get_post_meta($product_id, '_nbdesigner_enable', true);
            $_enable_upload                 = get_post_meta($product_id, '_nbdesigner_enable_upload', true);
            $_enable_upload_without_design  = get_post_meta($product_id, '_nbdesigner_enable_upload_without_design', true);
            $_product                       = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            $product_permalink              = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );            
            if ( $is_nbdesign && $_show_design == 'yes' ) {
                if($nbd_session || $nbu_session){
                    $html = is_checkout() ? $title . ' &times; <strong>' . $cart_item['quantity'] .'</strong>' : $title;
                }else{
                    $html = $title;
                }
                $layout = nbd_get_product_layout( $product_id );
                if( isset( $nbd_session ) ){
                    $id             = 'nbd' . $cart_item_key;
                    $redirect       = is_cart() ? 'cart' : 'checkout';
                    $html          .= '<div id="' . $id . '" class="nbd-custom-dsign nbd-cart-item-design">';
                    $remove_design  = is_cart() ? '<a class="remove nbd-remove-design nbd-cart-item-remove-design" href="#" data-type="custom" data-cart-item="' . $cart_item_key . '">&times;</a>' : '';
                    $html          .= '<p>' . esc_html__('Custom design', 'web-to-print-online-designer') . $remove_design . '</p>';
                    $list           = Nbdesigner_IO::get_list_images( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_session . '/preview' );
                    $list           = nbd_sort_file_by_side( $list );
                    foreach ( $list as $img ) {
                        $src    = Nbdesigner_IO::convert_path_to_url( $img ) . '?&t=' . round( microtime( true ) * 1000 );
                        $html  .= '<img class="nbd_cart_item_design_preview" src="' . $src . '"/>';
                    }
                    if( $show_edit_link ){
                        $link_edit_design = add_query_arg(
                            array(
                                'task'          => 'edit',
                                'product_id'    => $product_id,
                                'nbd_item_key'  => $nbd_session,
                                'cik'           => $cart_item_key,
                                'view'          => $layout,
                                'rd'            => $redirect),
                            getUrlPageNBD('create'));
                        if( $product_permalink ){
                            $att_query = parse_url( $product_permalink, PHP_URL_QUERY );
                            $link_edit_design .= '&'.$att_query;
                        }    
                        if( $layout == 'v' ){
                            $link_edit_design = add_query_arg(
                                array(
                                    'nbdv-task'     => 'edit',
                                    'task'          => 'edit',
                                    'product_id'    => $product_id,
                                    'nbd_item_key'  => $nbd_session,
                                    'cik'           => $cart_item_key,
                                    'rd'            => $redirect),
                                $product_permalink );
                        }
                        if($cart_item['variation_id'] > 0){
                            $link_edit_design .= '&variation_id=' . $cart_item['variation_id'];
                        }
                        $html .= '<br /><a class="button nbd-edit-design" href="'.$link_edit_design.'">'. esc_html__('Edit design', 'web-to-print-online-designer') .'</a>';
                    }
                    $html .= '</div>';
                }else if( $is_nbdesign && !$_enable_upload_without_design && $show_edit_link ){
                    $id = 'nbd' . $cart_item_key; 
                    $redirect = is_cart() ? 'cart' : 'checkout';
                    $link_create_design = add_query_arg(
                        array(
                            'task'          => 'new',
                            'task2'         => 'update',
                            'product_id'    => $product_id,
                            'variation_id'  => $variation_id,
                            'cik'           => $cart_item_key,
                            'view'          => $layout,
                            'rd'            => $redirect),
                        getUrlPageNBD('create'));
                    if( $layout == 'v' ){
                        $link_create_design = add_query_arg(
                            array(
                                'nbdv-task'     => 'new',
                                'task'          => 'new',
                                'task2'         => 'update',
                                'product_id'    => $product_id,
                                'variation_id'  => $variation_id,
                                'cik'           => $cart_item_key,
                                'view'          => $layout,
                                'rd'            => $redirect),
                            $product_permalink );
                    }
                    if( $product_permalink ){
                        $att_query = parse_url( $product_permalink, PHP_URL_QUERY );
                        $link_create_design .= '&'.$att_query;
                    }
                    $html .= '<div class="nbd-cart-upload-file nbd-cart-item-add-design">';
                    $html .=    '<a class="button nbd-create-design" href="' . $link_create_design . '">'. esc_html__('Add design', 'web-to-print-online-designer') .'</a>';
                    $html .= '</div>';
                }
                if( isset( $nbu_session ) ){
                    $id             = 'nbu' . $cart_item_key; 
                    $redirect       = is_cart() ? 'cart' : 'checkout';
                    $html          .= '<div id="'.$id.'" class="nbd-cart-upload-file nbd-cart-item-upload-file">';
                    $remove_upload  = is_cart() ? '<a class="remove nbd-cart-item-remove-file" href="#" data-type="upload" data-cart-item="' . $cart_item_key . '">&times;</a>' : '';
                    $html          .= '<p>' . esc_html__('Upload file', 'web-to-print-online-designer') . $remove_upload . '</p>';
                    $files          = Nbdesigner_IO::get_list_files( NBDESIGNER_UPLOAD_DIR . '/' . $nbu_session );
                    $create_preview = nbdesigner_get_option('nbdesigner_create_preview_image_file_upload');
                    $upload_html    = '';
                    foreach ( $files as $file ) {
                        $ext        = pathinfo( $file, PATHINFO_EXTENSION );
                        $src        = Nbdesigner_IO::get_thumb_file( pathinfo( $file, PATHINFO_EXTENSION ), '');
                        $file_url   = Nbdesigner_IO::wp_convert_path_to_url( $file );
                        if(  $create_preview == 'yes' && ( $ext == 'png' || $ext == 'jpg' || $ext == 'pdf' ) ){
                            $dir        = pathinfo( $file, PATHINFO_DIRNAME );
                            $filename   = pathinfo( $file, PATHINFO_BASENAME );
                            if( file_exists($dir.'_preview/'.$filename) ){
                                $src = Nbdesigner_IO::wp_convert_path_to_url( $dir.'_preview/'.$filename );
                            }else if( $ext == 'pdf' && file_exists($dir.'_preview/'.$filename.'.jpg' ) ){
                                $src = Nbdesigner_IO::wp_convert_path_to_url( $dir.'_preview/'.$filename.'.jpg' );
                            }else{
                                $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
                            }
                        }else {
                            $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
                        }
                        $upload_html .= '<div class="nbd-cart-item-upload-preview-wrap"><a target="_blank" href='.$file_url.'><img class="nbd-cart-item-upload-preview" src="' . $src . '"/></a><p class="nbd-cart-item-upload-preview-title">'. basename($file).'</p></div>';
                    }
                    $upload_html = apply_filters('nbu_cart_item_html', $upload_html, $cart_item, $nbu_session);
                    $html .= $upload_html;
                    if( $show_edit_link ){
                        $link_reup_design = add_query_arg(
                            array(
                                'task'          => 'reup',
                                'product_id'    => $product_id,
                                'nbu_item_key'  => $nbu_session,
                                'cik'           => $cart_item_key,
                                'rd'            => $redirect),
                            getUrlPageNBD('create'));
                        if($cart_item['variation_id'] > 0){
                            $link_reup_design .= '&variation_id=' . $cart_item['variation_id'];
                        }
                        $link_reup_design = apply_filters( 'nbu_cart_item_reup_link', $link_reup_design, $cart_item, $cart_item_key, $redirect );
                        $html .= '<br /><a class="button nbd-reup-design" href="' . $link_reup_design . '">'. esc_html__('Reupload design', 'web-to-print-online-designer') .'</a>';
                    }
                    $html .= '</div>';
                }else if( $_enable_upload && $show_edit_link ){
                    $id = 'nbd' . $cart_item_key;     
                    $redirect = is_cart() ? 'cart' : 'checkout';
                    $link_create_design = add_query_arg(
                        array(
                            'task'          => 'new',
                            'task2'         => 'add_file',
                            'product_id'    => $product_id,
                            'variation_id'  => $variation_id,
                            'cik'           => $cart_item_key,
                            'rd'            => $redirect),
                        getUrlPageNBD('create'));
                    $link_upload_design = apply_filters('nbu_cart_item_upload_link', $link_create_design, $cart_item, $cart_item_key, $redirect);
                    $html .= '<div class="nbd-cart-upload-file nbd-cart-item-upload-file">';
                    $html .=    '<a class="button nbd-upload-design" href="' . $link_upload_design . '">' . esc_html__('Upload design', 'web-to-print-online-designer') . '</a>';
                    $html .= '</div>';
                }
                $option = unserialize(get_post_meta($product_id, '_nbdesigner_option', true)); 
                if( isset($nbd_session) ) {
                    $path = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_session . '/config.json';
                    $config = nbd_get_data_from_json($path);
                    if( isset( $config->custom_dimension ) && isset( $config->custom_dimension->price ) ){
                        $nbd_variation_price = $config->custom_dimension->price;
                    }
                }
                if( ( ( isset( $nbd_variation_price ) && $nbd_variation_price != 0 ) || $option['extra_price'] ) && ! $option['request_quote'] ){
                    $decimals = wc_get_price_decimals();
                    $extra_price = $option['extra_price'] ? $option['extra_price'] : 0;
                    if( (isset($nbd_variation_price) && $nbd_variation_price != 0) ) {
                        $extra_price = $option['type_price'] == 1 ? wc_price($extra_price + $nbd_variation_price) : $extra_price . ' % + ' . wc_price($nbd_variation_price);
                    }else {
                        $extra_price = $option['type_price'] == 1 ? wc_price($extra_price) : $extra_price . '%';
                    }
                    $html .= '<p id="nbx'.$cart_item_key.'">' . esc_html__('Extra price for design','web-to-print-online-designer') . ' + ' .  $extra_price . '</p>';
                }
                return $html;
            } else {
                return $title;
            }
        }else{
            return $title;
        }
    }
    /**
     * Set session cart if manual add to cart <br />
     * Store last cart_item_key into session if add to cart before start design
     * @since 1.7.0
     * 
     * @param text $cart_item_key 
     * @param int $product_id
     * @param int $quantity
     * @param int $variation_id
     */
    public function set_nbd_session_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
        $product_id                 = get_wpml_original_id( $product_id );
        $recent_variation_id        = $variation_id;
        $variation_id               = get_wpml_original_id( $variation_id );
        $nbd_item_cart_key          = ($variation_id > 0) ? $product_id . '_' . $variation_id : $product_id; 
        $recent_nbd_item_cart_key   = ($recent_variation_id > 0) ? $product_id . '_' . $recent_variation_id : $product_id;
        /* add to cart in custom design page */
        if( is_nbdesigner_product( $product_id ) ){
            WC()->session->set( 'nbd_last_item_cart', $cart_item_key );
        }
        /* custom design */
        $nbd_session        = WC()->session->get( 'nbd_item_key_'.$nbd_item_cart_key );
        $recent_nbd_session = WC()->session->get( 'nbd_item_key_'.$recent_nbd_item_cart_key );
        if( $recent_nbd_session ){
            $nbd_session = $recent_nbd_session;
        }
        $_enable_upload_without_design = get_post_meta( $product_id, '_nbdesigner_enable_upload_without_design', true );
        if( isset( $nbd_session ) && $nbd_session != '' ){
            WC()->session->set( $cart_item_key. '_nbd', $nbd_session );
            WC()->session->__unset( 'nbd_item_key_' . $nbd_item_cart_key );
            if( $_enable_upload_without_design ) WC()->session->__unset( $cart_item_key . '_nbd' );
        }else{
//            $nbd_session2 = WC()->session->get('nbd_item_key_'.$product_id . '_0');
//            if(isset($nbd_session2) && $nbd_session2 != ''){
//                if( !$_enable_upload_without_design ){
//                    WC()->session->set($cart_item_key. '_nbd', $nbd_session2);
//                    WC()->session->__unset('nbd_item_key_'.$product_id . '_0');
//                }
//            }
        }
        /* up design */
        $nbu_session = WC()->session->get( 'nbu_item_key_' . $nbd_item_cart_key );
        if( isset( $nbu_session ) && $nbu_session != '' ){
            WC()->session->set( $cart_item_key. '_nbu', $nbu_session );
            WC()->session->__unset( 'nbu_item_key_' . $nbd_item_cart_key );

            $_enable_upload = get_post_meta( $product_id, '_nbdesigner_enable_upload', true );
            if( !$_enable_upload ) WC()->session->__unset( $cart_item_key . '_nbu' );
        }
        /* order again */
        if( isset( $cart_item_data['_nbd'] ) ){
            WC()->session->set( $cart_item_key . '_nbd', $cart_item_data['_nbd'] );
        }
        if( isset( $cart_item_data['_nbu'] ) ){
            WC()->session->set( $cart_item_key . '_nbu', $cart_item_data['_nbu'] );
        }
    }
    public function nbdesigner_remove_cart_item_design( $removed_cart_item_key, $instance ){
        //$line_item = $instance->removed_cart_contents[ $removed_cart_item_key ];
        /* custom design */
        $nbd_session = WC()->session->get( $removed_cart_item_key . '_nbd' );
        if($nbd_session){  
            WC()->session->__unset( $removed_cart_item_key . '_nbd' );
        }  
        /* up design */
        $nbu_session = WC()->session->get( $removed_cart_item_key . '_nbu' );
        if($nbu_session){
            WC()->session->__unset( $removed_cart_item_key . '_nbu' );
        }
        /* remove extra price session */
        if( WC()->session->__isset( $removed_cart_item_key . '_nbd_initial_price' ) ){
            WC()->session->__unset( $removed_cart_item_key . '_nbd_initial_price' );
        }
    }
    public function order_line_item( $item, $cart_item_key, $values ){
        if ( isset( $values['nbd_item_meta_ds'] ) ) {
            if ( isset( $values['nbd_item_meta_ds']['nbd'] ) ) $item->add_meta_data( '_nbd', $values['nbd_item_meta_ds']['nbd'] );
            if ( isset( $values['nbd_item_meta_ds']['nbu'] ) ) $item->add_meta_data( '_nbu', $values['nbd_item_meta_ds']['nbu'] );
        }
        if ( isset( $values['nbd_design_id'] ) ) {
            $item->add_meta_data( '_nbd_design_id', $values['nbd_design_id'] );
        }
    }
    public function nbdesigner_add_new_order_item($item_id, $item, $order_id){
        if ( isset( $item->legacy_cart_item_key ) ){
            if( is_null( WC()->session ) ) return;
            /* custom design */
            if ( WC()->session->__isset( $item->legacy_cart_item_key . '_nbd' ) || isset( $item->legacy_values["nbd_item_meta_ds"]["nbd"] ) ) {
                $nbd_session = WC()->session->get( $item->legacy_cart_item_key . '_nbd' );
                if( isset( $item->legacy_values["nbd_item_meta_ds"]["nbd"] ) ) $nbd_session = $item->legacy_values["nbd_item_meta_ds"]["nbd"];
                wc_add_order_item_meta( $item_id, "_nbd", $nbd_session );
            }
            /* up design */
            if ( WC()->session->__isset( $item->legacy_cart_item_key . '_nbu' ) || isset( $item->legacy_values["nbd_item_meta_ds"]["nbu"] ) ) {
                $nbu_session = WC()->session->get($item->legacy_cart_item_key . '_nbu');
                if( isset( $item->legacy_values["nbd_item_meta_ds"]["nbu"] ) ) $nbu_session = $item->legacy_values["nbd_item_meta_ds"]["nbu"];
                wc_add_order_item_meta( $item_id, "_nbu", $nbu_session );
            }
            if( WC()->session->__isset( $item->legacy_cart_item_key . '_nbd_initial_price' ) || isset( $item->legacy_values["nbd_item_meta_ds"]["initial_price"] ) ){
                $product_id = $item->legacy_values['product_id'];
                $product_id = get_wpml_original_id( $product_id );
                $option     = unserialize( get_post_meta( $product_id, '_nbdesigner_option', true ) );
                if( isset( $nbd_session ) ) {
                    $path   = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_session . '/config.json';
                    $config = nbd_get_data_from_json( $path );
                    if( isset( $config->custom_dimension ) && isset( $config->custom_dimension->price ) ){
                        $nbd_variation_price = $config->custom_dimension->price;
                    }
                }
                if( ( ( isset( $nbd_variation_price ) && $nbd_variation_price != 0 ) || $option['extra_price'] ) && ! $option['request_quote'] ){
                    $extra_price = $option['extra_price'] ? $option['extra_price'] : 0;
                    if( ( isset( $nbd_variation_price ) && $nbd_variation_price != 0 ) ) {
                        $extra_price = $option['type_price'] == 1 ? wc_price( $extra_price + $nbd_variation_price ) : $extra_price . '% + ' . wc_price( $nbd_variation_price );
                    }else {
                        $extra_price = wc_price( $extra_price );
                    }
                    wc_add_order_item_meta( $item_id, "_nbd_extra_price", $extra_price );
                }
            }
        }
    }
    public function nbdesigner_add_order_design_data( $item_id, $values, $cart_item_key ) {
        /* custom design */
        if (WC()->session->__isset($cart_item_key . '_nbd')) {
            wc_add_order_item_meta($item_id, "_nbd", WC()->session->get($cart_item_key . '_nbd'));
        }
        /* up design */
        if (WC()->session->__isset($cart_item_key . '_nbu')) {
            wc_add_order_item_meta($item_id, "_nbu", WC()->session->get($cart_item_key . '_nbu'));
        }
    }
    public function nbdesigner_design_approve(){
        check_admin_referer( 'approve-designs', '_nbdesigner_approve_nonce' );
        $order_id           = absint( $_POST['nbdesigner_design_order_id'] );
        $_design_action     = sanitize_text_field( $_POST['nbdesigner_order_file_approve'] );
        $response['mes']    = '';
        if (is_numeric( $order_id ) && ( isset($_POST['_nbdesigner_design_file'] ) && is_array( $_POST['_nbdesigner_design_file'] ) || isset( $_POST['_nbdesigner_upload_file'] ) && is_array( $_POST['_nbdesigner_upload_file'] ) ) ) {
            $has_design = $has_upload = $update_design = $update_upload = false;
            if( isset( $_POST['_nbdesigner_design_file'] ) ){
                $_design_file   = wc_clean( $_POST['_nbdesigner_design_file'] );
                $has_design     = true;
            }
            if( isset( $_POST['_nbdesigner_upload_file'] ) ){
                $_upload_file   = wc_clean( $_POST['_nbdesigner_upload_file'] );
                $has_upload     = true;
            }
            $need_save = false;
            $order = wc_get_order($order_id);
            $design_data = maybe_unserialize( $order->get_meta( '_nbdesigner_design_file', true ) );
            $upload_data = maybe_unserialize( $order->get_meta( '_nbdesigner_upload_file', true ) );
            if( $has_design ){
                if( is_array( $design_data ) ){
                    foreach( $_design_file as $val ){
                        $check = false;
                        foreach( $design_data as $key => $status ){
                            $_key = str_replace( 'nbds_', '', trim( $key ) );
                            if($_key == $val){
                                $design_data[$key]  = $_design_action;
                                $check              = true;
                            }
                        }
                        if( !$check ) $design_data['nbds_'.$val] = $_design_action;
                    }
                }else{
                    $design_data = array();
                    foreach ( $_design_file as $val ){
                        $design_data['nbds_'.$val] = $_design_action;
                    }
                }
                $design_data    = serialize( $design_data );
                $order->update_meta_data( '_nbdesigner_design_file', $design_data );
                $need_save = true;
                $update_design = true;
            }
            if( $has_upload ){
                if( is_array( $upload_data ) ){
                    foreach( $_upload_file as $val ){
                        $check = false;
                        foreach( $upload_data as $key => $status ){
                            $_key = str_replace( 'nbds_', '', trim( $key ) );
                            if($_key == $val){
                                $upload_data[$key]  = $_design_action;
                                $check              = true;
                            }
                        }
                        if( !$check ) $upload_data['nbds_'.$val] = $_design_action;
                    }
                }else{
                    $upload_data = array();
                    foreach ( $_upload_file as $val ){
                        $upload_data['nbds_'.$val] = $_design_action;
                    }
                } 
                $upload_data    = serialize( $upload_data );
                $order->update_meta_data( '_nbdesigner_upload_file', $upload_data );
                $need_save = true;
                $update_upload  = true;
            }
            if ( $update_design || $update_upload ){
                $order->update_meta_data( '_nbdesigner_order_changed', 0 );
                $need_save = true;
                $response['mes'] = 'success';
            }else{
                $order->update_meta_data( '_nbdesigner_order_changed', 0 );
                $need_save = true;
                $response['mes'] = esc_html__( 'You don\'t change anything? Or an error occured saving the data, please refresh this page and check if changes took place.', 'web-to-print-online-designer' );
            }
            if($need_save) {
                $order->save();
            }
        } else {
            $response['mes'] = esc_html__( 'You haven\'t chosen a item.', 'web-to-print-online-designer' );
        }
        echo json_encode( $response );
        wp_die();
    }
    public function nbdesigner_design_order_email(){
        check_admin_referer( 'approve-design-email', '_nbdesigner_design_email_nonce' );
        $response['success'] = 0;
        if ( empty( $_POST['nbdesigner_design_email_order_content'] ) ) {
            $response['error'] = esc_html__( 'The reason cannot be empty', 'web-to-print-online-designer' );
        } elseif ( !is_numeric( $_POST['nbdesigner_design_email_order_id'] ) ) {
            $response['error'] = esc_html__( 'Error while sending mail', 'web-to-print-online-designer' );
        }
        if ( empty( $response['error'] ) ) {
            $message    = sanitize_text_field( $_POST['nbdesigner_design_email_order_content'] );
            $order      = new WC_Order( $_POST['nbdesigner_design_email_order_id'] );
            $reason     = ( $_POST['nbdesigner_design_email_reason'] == 'approved' ) ? esc_html__( 'Your design accepted', 'web-to-print-online-designer' ) : esc_html__( 'Your design rejected', 'web-to-print-online-designer' );
            $send_email = $this->nbdesigner_send_email( $order, $reason, $message );
            if ( $send_email )
                $response['success'] = 1;
            else
                $response['error'] = esc_html__( 'Error while sending mail', 'web-to-print-online-designer' );
        }
        echo json_encode( $response );
        wp_die();
    }
    public function nbdesigner_send_email( $order, $reason, $message ){
        global $woocommerce;
        $user_email = $order->get_billing_email();
        if ( !empty( $user_email ) ) {
            $mailer = $woocommerce->mailer();
            ob_start();
            wc_get_template('emails/nbdesigner-approve-order-design.php', array(
                'plugin_id'     => 'nbdesigner',
                'order'         => $order,
                'reason'        => $reason,
                'message'       => $message,
                'my_order_url'  => $order->get_view_order_url(),
            ));
            $body       = ob_get_clean();
            $subject    = $reason . '-'.__('Order','web-to-print-online-designer').'#' . $order->get_order_number();
            $mailer->send($user_email, $subject, $body);
            
            //Send mail to admin
            $notifications = get_option('nbdesigner_enable_send_mail_when_approve', false);
            if( $notifications == 'yes' && $reason == 'Your design accepted' ){
                ob_start();
                wc_get_template('emails/notify-admin-when-approve-design.php', array(
                    'order_number'  => $order->get_order_number(),
                    'order_id'      => $order->get_id()
                )); 
                $subject = esc_html__('Approve designs - Order ','web-to-print-online-designer') . $order->get_order_number();
                $owner_email = get_option('nbdesigner_admin_emails', false);
                $emails = new WC_Emails();
                $woo_recipient = $emails->emails['WC_Email_New_Order']->recipient;
                if($owner_email == ''){
                    if(!empty($woo_recipient)) {
                        $user_email = esc_attr($woo_recipient);
                    } else {
                        $user_email = get_option( 'admin_email' );
                    }
                }else{
                    $user_email = $owner_email;
                }
                $body       = ob_get_clean();
                 
                $products   = $order->get_items();
                $attachment = array();
                if( get_option('nbdesigner_attachment_admin_email', false) == 'yes' ){
                    foreach($products AS $order_item_id => $product){
                        $nbd_item_key = wc_get_order_item_meta($order_item_id, '_nbd');
                        if( $nbd_item_key ){
                            $list_images = Nbdesigner_IO::get_list_images(NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key, 1);
                            foreach ($list_images as $image) {
                                $attachment[] = $image;
                            }
                        }
                    }
                }
                if (!empty($user_email)) {
                    $mailer->send($user_email, $subject, $body, '', $attachment);
                }
            }
            return true;
        } else {
            return false;
        }
    }
    public function update_order_item_meta_data( $order_id ){
        $order          = new WC_Order( $order_id );
        $order_items    = $order->get_items();
        foreach( $order_items AS $order_item_id => $order_item ){
            $nbd_item_key = wc_get_order_item_meta( $order_item_id, '_nbd' );
            if( $nbd_item_key ){
                $watermark  = false;
                $showBleed  = false;
                $force      = true;
                $files      = nbd_export_pdfs( $nbd_item_key, $watermark, $force, $showBleed );
                $urls       = '';
                foreach( $files as $key => $file ){
                    $urls .= ($key > 0 ? '|' : '') . Nbdesigner_IO::wp_convert_path_to_url( $file );
                };
                wc_update_order_item_meta( $order_item_id, '_pdfs', $urls );
            }
            $nbu_item_key = wc_get_order_item_meta( $order_item_id, '_nbu' );
            if( $nbu_item_key ){
                $files = Nbdesigner_IO::get_list_files( NBDESIGNER_UPLOAD_DIR . '/' . $nbu_item_key );
                foreach( $files as $key => $file ){
                    $urls .= ($key > 0 ? '|' : '') . Nbdesigner_IO::wp_convert_path_to_url( $file );
                };
                wc_update_order_item_meta( $order_item_id, '_nbd_upload_files', $urls );
            }
        }
    }
    public function attach_design_to_admin_email2( $attachments, $type, $object ){
        if( 'new_order' === $type ){
            $products   = $object->get_items();
            $has_design = false;
            foreach( $products AS $order_item_id => $product ){
                $nbd_item_key = wc_get_order_item_meta( $order_item_id, '_nbd' );
                if( $nbd_item_key ){
                    $watermark  = false;
                    $showBleed  = true;
                    $force      = true;
                    $files      = nbd_export_pdfs( $nbd_item_key, $watermark, $force, $showBleed );
                    foreach( $files as $key => $file ){
                        $zip_files[] = $file;
                    };
                    $has_design = true;
                }
            }
            if( $has_design ){
                $pathZip        = NBDESIGNER_DATA_DIR . '/download/customer-design-' . time() . '.zip';
                nbd_zip_files_and_download( $zip_files, $pathZip, 'Canvas.zip', array(), false );
                $attachments[]  = $pathZip;
            }
        }
        return $attachments;
    }
    public function attach_design_to_admin_email( $attachments, $type, $object ){
        //if( 'new_order' === $type || $type == 'customer_completed_order' ){
        if( 'new_order' === $type ){
            $products = $object->get_items();
            foreach( $products AS $order_item_id => $product ){
                $nbd_item_key = wc_get_order_item_meta( $order_item_id, '_nbd' );
                if( $nbd_item_key ){
                    $list_images = array();
                    if( nbdesigner_get_option( 'nbdesigner_attach_design_svg', 0 ) == 1 ){
                        $list_images = Nbdesigner_IO::get_list_files_by_type( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key, 1, 'svg' );
                    }
                    if( nbdesigner_get_option( 'nbdesigner_attach_design_png', 1 ) == 1 ){
                        $list_images = array_merge( Nbdesigner_IO::get_list_images( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key, 1 ), $list_images );
                    }
                    asort( $list_images );
                    foreach ( $list_images as $image ) {
                        $attachments[] = $image;
                    }
                }
            }
        }
        return $attachments;
    }
    public function nbdesigner_locate_plugin_template( $template, $template_name, $template_path ){
        global $woocommerce;
        $_template = $template;
        if ( ! $template_path ) $template_path = $woocommerce->template_url;
        $plugin_path  = NBDESIGNER_PLUGIN_DIR . 'templates/';
        $template = locate_template(array(
            $template_path . $template_name,
            $template_name
        ));
        if ( ! $template && file_exists( $plugin_path . $template_name ) )
            $template = $plugin_path . $template_name;
        if ( ! $template )
          $template = $_template;
        return $template;
    }
    public function nbdesigner_order_item_quantity_html( $strong, $item ){
        $show_design_in_order = nbdesigner_get_option( 'nbdesigner_show_in_order', 'yes' );
        if( $show_design_in_order == 'yes' ){
            if( isset( $item["item_meta"]["_nbd"] ) || isset( $item["item_meta"]["_nbu"] ) ) return '';
        }
        return ' <strong class="product-quantity">' . sprintf( '&times; %s', $item['qty'] ) . '</strong>';
    }
    public function nbd_checkout_cart_item_quantity( $quantity_html, $cart_item, $cart_item_key ){
        $show_design_in_cart = nbdesigner_get_option( 'nbdesigner_show_in_cart' );
        if( $show_design_in_cart == 'yes' ){
            if( WC()->session->__isset( $cart_item_key . '_nbd' ) || WC()->session->__isset( $cart_item_key . '_nbu' ) ) return '';
        }
        return $quantity_html;
    }
    public function nbdesigner_hidden_custom_order_item_metada( $order_items ){
        $order_items[] = '_nbdesigner_has_design';
        $order_items[] = '_nbdesigner_folder_design';
        $order_items[] = '_nbdesigner_version_design';
        $order_items[] = '_nbdesign_order';
        $order_items[] = '_nbdesign_order_item_id';
        $order_items[] = '_nbd';
        $order_items[] = '_nbu';
        $order_items[] = '_nbd_extra_price';
        $order_items[] = '_pdfs';
        $order_items[] = '_nbd_synchronized';
        $order_items[] = '_nbd_upload_files';
        return $order_items;
    }
    public function nbd_upload_design_file(){
        if ( ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) || !isset( $_FILES ) ) {
            die( 'Security error' );
        }
        $result = array(
            'flag'  => 0,
            'mes'   => '',
            'src'   => ''
        );
        $product_id         = absint( $_POST['product_id'] );
        $variation_id       = absint( $_POST['variation_id'] );
        $task               = sanitize_text_field( $_POST['task'] );
        $first_time         = absint( $_POST['first_time'] );
        $nbd_item_cart_key  = ( $variation_id > 0 ) ? $product_id . '_' . $variation_id : $product_id;
        $upload_setting     = unserialize( get_post_meta( $product_id, '_nbdesigner_upload', true ) );
        if( $upload_setting['allow_type'] == '' ) $upload_setting['allow_type'] = nbdesigner_get_option( 'nbdesigner_allow_upload_file_type', '' );
        if( $upload_setting['disallow_type'] == '' ) $upload_setting['disallow_type'] = nbdesigner_get_option( 'nbdesigner_disallow_upload_file_type', '' );
        if( $upload_setting['maxsize'] == '' ) $upload_setting['maxsize'] = nbdesigner_get_option( 'nbdesigner_maxsize_upload_file', nbd_get_max_upload_default() );
        if( $upload_setting['minsize'] == '' ) $upload_setting['minsize'] = nbdesigner_get_option( 'nbdesigner_minsize_upload_file', 0 );
        $allow_ext          = explode( ',', preg_replace('/\s+/', '', strtolower( $upload_setting['allow_type'] ) ) );
        $disallow_ext       = explode( ',', preg_replace('/\s+/', '', strtolower( $upload_setting['disallow_type'] ) ) );
        $ext                = strtolower( $this->nbdesigner_get_extension( $_FILES['file']["name"] ) );
        $ext                = $ext == 'jpeg' ? 'jpg' : $ext;
        $max_size           = $upload_setting['maxsize'] * 1024 * 1024;
        $minsize            = $upload_setting['minsize'] * 1024 * 1024;
        $checkSize          = $checkExt = $checkDPI = false;
        if( $upload_setting['mindpi'] > 0 && ( $ext == 'jpg' || $ext == 'jpeg' ) ){
            $dpi = nbd_get_dpi( $_FILES['file']["tmp_name"] );
            if( $dpi['x'] < $upload_setting['mindpi'] ) $checkDPI = true;
        }
        if( ( count( $allow_ext ) && $allow_ext[0] != '' && !in_array( strtolower( $ext ), $allow_ext ) ) 
                || ( count( $disallow_ext ) && $disallow_ext[0] != '' && in_array( strtolower( $ext ), $disallow_ext ) ) ) {
            $checkExt = true;
        }
        if( $minsize > $_FILES['file']["size"] || ( $max_size != 0 && $max_size < $_FILES['file']["size"] ) ){
            $checkSize = true;
        }
        if( $checkSize || $checkExt || $checkDPI ){
            if( $checkSize ) $result['mes'] = esc_html__( 'File size too small or large!', 'web-to-print-online-designer' );
            if( $checkExt ) $result['mes']  = esc_html__( 'Extension not allowed!', 'web-to-print-online-designer' );
            if( $checkDPI ) $result['mes']  = esc_html__( 'Min DPI required for JPG', 'web-to-print-online-designer' ) . ' ' . $upload_setting['mindpi'];
        } else {
            if( isset( $_POST['nbu_item_key'] ) && $_POST['nbu_item_key'] != ''){
                /* reup */
                $nbu_item_key = sanitize_text_field( $_POST['nbu_item_key'] );
            } else {
                $nbu_item_session   = WC()->session->get( 'nbu_item_key_' . $nbd_item_cart_key );
                $nbu_item_key       = isset( $nbu_item_session ) ? $nbu_item_session : substr( md5( uniqid() ), 0, 5 ) . rand( 1, 100 ) . time();
            }
            $path_dir   = NBDESIGNER_UPLOAD_DIR . '/' . $nbu_item_key; 
            $new_name   = time() . '_' . sanitize_file_name( $_FILES['file']["name"] );
            $new_name   = nbd_alias( $new_name );
            $path       = $path_dir . '/' . $new_name;
            if ( !file_exists( $path_dir ) ) wp_mkdir_p( $path_dir );
            $image_exts = array(
                0   => 'jpg',
                1   => 'jpeg',
                2   => 'png'
            );
            if( $task == 'reup' && $first_time == 1 ){
                if( file_exists( $path_dir.'_old' ) ) Nbdesigner_IO::delete_folder( $path_dir . '_old' );
                if( file_exists( $path_dir ) ){
                    rename( $path_dir, $path_dir.'_old' );
                    wp_mkdir_p( $path_dir );
                }
                //maybe implement other method
            }
            if( move_uploaded_file( $_FILES['file']["tmp_name"], $path ) ){
                $result['mes'] = esc_html__( 'Upload success !', 'web-to-print-online-designer' );
                /** Allow create preview for images **/
                if( nbdesigner_get_option( 'nbdesigner_create_preview_image_file_upload' ) == 'yes' && !isset( $_POST['nbu_adu'] ) ){
                    $path_preview = $path_dir . '_preview/';
                    $preview_file = Nbdesigner_IO::wp_convert_path_to_url( $path_preview . $new_name );
                    if( !file_exists( $path_preview ) ){
                        wp_mkdir_p( $path_preview );
                    }
                    $preview_width = nbdesigner_get_option( 'nbdesigner_file_upload_preview_width' );
                    if( $ext == 'png' ){
                        NBD_Image::nbdesigner_resize_imagepng( $path, $preview_width, $preview_width, $path_preview . $new_name );
                    }else if( $ext == 'jpg' ){
                        NBD_Image::nbdesigner_resize_imagejpg( $path, $preview_width, $preview_width, $path_preview . $new_name );
                    }else if( is_available_imagick() && $ext == 'pdf' ){
                        $jpg_preview = $path_preview . $new_name . '.jpg';
                        NBD_Image::imagick_convert_pdf_to_jpg( $path, $jpg_preview );
                        if( file_exists( $jpg_preview ) ){
                            $preview_file = Nbdesigner_IO::wp_convert_path_to_url( $jpg_preview );
                        }else{
                            $preview_file = Nbdesigner_IO::get_thumb_file( $ext, $path );
                        }
                    } else{
                        $preview_file = Nbdesigner_IO::get_thumb_file( $ext, $path );
                    }
                    $src = $preview_file;
                }else{
                    $src = Nbdesigner_IO::get_thumb_file( $ext, $path );
                }
                $result['src']  = $src;
                $result['name'] = $new_name;
                $result['flag'] = 1;
                if( $task == 'new' && $first_time == 1 ) WC()->session->set( 'nbu_item_key_' . $nbd_item_cart_key, $nbu_item_key );
            }else{
                $result['mes'] = esc_html__( 'Error occurred with file upload!', 'web-to-print-online-designer' );
            }
        }
        do_action( 'nbu_upload_design_file', $nbu_item_key, $path, $result );
        echo json_encode( $result );
        wp_die();
    }
    public function nbdesigner_customer_upload(){
        if ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) {
            die('Security error');
        }

        if( !isset( $_FILES['file'] ) ){
            $res['flag']    = 0;
            $res['mes']     = esc_html__( 'Please select at least one image!', 'web-to-print-online-designer' );
            echo json_encode( $res );
            wp_die();
        }

        $allow_extension    = array( 'jpg', 'jpeg', 'png', 'svg' );
        $max_size           = nbdesigner_get_option( 'nbdesigner_maxsize_upload' );
        $background_settings = unserialize( get_option('_nb_background_settings') ); //custom charmingprint
        if( isset($background_settings['max_size']) ) {
           $max_size =  $background_settings['max_size'];
        }
        $min_dpi            = nbdesigner_get_option( 'nbdesigner_mindpi_upload', 0 );
        $allow_max_size     = $max_size * 1024 * 1024;
        $result             = true;
        $res                = array();
        $size               = $_FILES['file']["size"];
        $name               = $_FILES['file']["name"];
        $ext                = $this->nbdesigner_get_extension( $name );
        $new_name           = strtotime( "now" ) . substr( md5( rand( 1111, 9999 ) ), 0, 8 ) . '.' . $ext;

        if( is_available_imagick() || nbdesigner_get_option( 'nbdesigner_enable_pdf2img_cloud2print_api', 'no' ) == 'yes' ){
            $allow_extension    = array( 'jpg', 'jpeg', 'png', 'svg', 'pdf' );
        }

        if( empty( $name ) ) {
            $result         = false;
            $res['mes']     = esc_html__( 'Error occurred with file upload!', 'web-to-print-online-designer' );
        }

        if( $size > $allow_max_size ){
            $result         = false;
            $res['mes']     = esc_html__( 'Too large file!', 'web-to-print-online-designer' );
        }

        $check = Nbdesigner_IO::checkFileType( $name, $allow_extension );
        if( !$check ){
            $result         = false;
            $res['mes']     = esc_html__( 'Invalid file format!', 'web-to-print-online-designer' );
        }

        if( ( $ext != 'svg' && $ext != 'pdf' ) && $min_dpi && $min_dpi > 0 ) {
            $dpi = nbd_get_dpi( $_FILES['file']["tmp_name"] );
            if( $dpi['x'] < $min_dpi ) {
                $result     = nbdesigner_get_option( 'nbdesigner_enable_low_resolution_image', 'no' ) == 'yes' ? true : false;
                $res['mes'] = esc_html__( 'Image resolution too low!', 'web-to-print-online-designer' );
                $res['ilr'] = 1;
            }
        }

        $path = Nbdesigner_IO::create_file_path( NBDESIGNER_TEMP_DIR, $new_name );
        if( $result ){
            if( move_uploaded_file( $_FILES['file']["tmp_name"], $path['full_path'] ) ){
                $res['mes'] = esc_html__( 'Upload success !', 'web-to-print-online-designer' );
            }else{
                $result     = false;
                $res['mes'] = esc_html__( 'Error occurred with file upload!', 'web-to-print-online-designer' );
            }
        }

        if( $result ){
            $res['src']     = NBDESIGNER_TEMP_URL . $path['date_path'];
            $res['flag']    = 1;

            if( $ext == 'pdf' ){
                $_dpi       = 72;
                $new_name   = str_replace( ".pdf", ".png", $new_name );

                if( is_available_imagick() && nbdesigner_get_option( 'nbdesigner_enable_pdf2img_cloud2print_api', 'no' ) == 'no' ){
                    try {
                        $im = new Imagick();
                        $im->setResolution( $_dpi, $_dpi );
                        $im->readImage( $path['full_path'] . '[0]' );
                        $im->setImageFormat( 'png32' );
                        $im->setImageUnits( imagick::RESOLUTION_PIXELSPERINCH );
                        $im->setResolution( $_dpi, $_dpi );
                        $path['full_path']  = str_replace( ".pdf", ".png", $path['full_path'] );
                        $im->writeImage( $path['full_path'] );
                        $im->clear();
                        $im->destroy();
                        $ext                    = 'png';
                        $res['origin_pdf']      = $path['date_path'];
                        $res['src']             = str_replace( ".pdf", ".png", $res['src'] );
                    } catch( Exception $e ) {
                        $res['flag']    = 0;
                        $res['mes']     = esc_html__( 'Error occurred when convert pdf!', 'web-to-print-online-designer' );
                    }
                } else {
                    NBD_Image::cloud_pdf2image( $path['full_path'], $_dpi, 'png' );
                    $img_path   = str_replace( ".pdf", "_1.png", $path['full_path'] );
                    if( file_exists( $img_path ) ){
                        $path['full_path']      = str_replace( ".pdf", ".png", $path['full_path'] );
                        $ext                    = 'png';
                        $res['origin_pdf']      = $path['date_path'];
                        $res['src']             = str_replace( ".pdf", ".png", $res['src'] );
                        copy( $img_path, $path['full_path'] );
                    }else{
                        $res['flag']    = 0;
                        $res['mes']     = esc_html__( 'Error occurred when convert pdf!!', 'web-to-print-online-designer' );
                    }
                }
            }

            if( ( $ext == 'jpg' || $ext == 'jpeg' ) && ( extension_loaded( 'exif' ) && function_exists( 'exif_read_data' ) ) ){
                $exif = @exif_read_data( $path['full_path'] );

                if( $exif && isset( $exif['Orientation'] ) ) {
                    $orientation = $exif['Orientation'];
                    if( $orientation != 1 ){
                        NBD_Image::strip_exif_orientation( $path['full_path'], $orientation );
                    }
                }
            }

            if( nbdesigner_get_option( 'nbdesigner_enable_generate_photo_thumb', 'no' ) == 'yes' ){
                $resizable_extensions = array( 'jpg', 'jpeg', 'png' );
                if( in_array( $ext, $resizable_extensions ) ){
                    $preview_size               = apply_filters( 'nbd_max_photo_thumb_size', 800 );
                    list( $width, $height )     = getimagesize( $path['full_path'] );
                    if( $width > $preview_size || $height > $preview_size ){
                        $infos          = pathinfo( $path['full_path'] );
                        $path_preview   = $infos['dirname'] . '/' . $infos['filename'] . '_preview.' . $infos['extension'];
                        if( $ext == 'png' ){
                            NBD_Image::nbdesigner_resize_imagepng( $path['full_path'], $preview_size, $preview_size, $path_preview );
                        } else {
                            NBD_Image::nbdesigner_resize_imagejpg( $path['full_path'], $preview_size, $preview_size, $path_preview );
                        }

                        if( file_exists( $path_preview ) ){
                            $res['origin_url']  = $res['src'];
                            $res['src']         = Nbdesigner_IO::wp_convert_path_to_url( $path_preview );
                            $res['width']       = $width;
                            $res['height']      = $height;
                        }
                    }
                }
            }
        } else {
            $res['flag'] = 0;
        }
        echo json_encode( $res );
        wp_die();
    }
    public function nbdesigner_get_qrcode(){
        $result         = array();
        $result['flag'] = 0;
        if ( !wp_verify_nonce( $_REQUEST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) {
            die( 'Security error' );
        } 
        if( isset( $_REQUEST['data'] ) ){
            $content    = sanitize_text_field( $_REQUEST['data'] );
            include_once NBDESIGNER_PLUGIN_DIR . 'includes/class-qrcode.php';
            $qr         = new Nbdesigner_Qrcode();
            $qr->setText( $content );
            $image      = $qr->getImage( 500 );
            $file_name  = strtotime( "now" ) . '.png';
            $full_name  = NBDESIGNER_DATA_DIR . '/temp/' . $file_name;
            if( Nbdesigner_IO::save_data_to_file( $full_name, $image ) ){
                $result['flag'] = 1;
                $result['src']  =  content_url() . '/uploads/nbdesigner/temp/' . $file_name;
            }
        }
        echo json_encode( $result );
        wp_die();
    }
    public function nbdesigner_get_facebook_photo(){
        if (!wp_verify_nonce($_POST['nonce'], 'save-design') && NBDESIGNER_ENABLE_NONCE) {
            die('Security error');
        }
        $result         = array();
        $_accessToken   = $_POST['accessToken'];
        require_once NBDESIGNER_PLUGIN_DIR.'includes/class.nbdesigner.facebook.php';
        echo json_encode( $result );
        wp_die();
    }
    public function _nbdesigner_get_art(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], 'nbdesigner-get-data' ) && NBDESIGNER_ENABLE_NONCE ) {
            die('Security error');
        }   
        $result = array();
        $path   = NBDESIGNER_DATA_DIR . '/cliparts';
        $cats   = Nbdesigner_IO::get_list_folder( $path, 1 );
        foreach ( $cats as $key => $cat ){
            $result['cat'][] = array(
                'name'  => basename( $cat ),
                'id'    => $key
            );
            $list = Nbdesigner_IO::get_list_files( $path . '/' . basename( $cat ), 1 );
            $arts = preg_grep( '/\.(svg)(?:[\?\#].*)?$/i', $list );
            foreach( $arts as $k => $art ) {
                $result['arts'][] = array(
                    'name'  => basename( $art ),
                    'id'    => $k,
                    'cat'   => array( $key ),
                    'file'  => '',
                    'url'   => Nbdesigner_IO::wp_convert_path_to_url( $art )
                );
            }
        }
        $result['flag'] = 1;
  
        echo json_encode( $result );
        wp_die();
    }
    public function nbdesigner_get_art(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], 'nbdesigner-get-data' ) && NBDESIGNER_ENABLE_NONCE ) {
            die( 'Security error' );
        }
        $result         = array();
        $path_cat       = NBDESIGNER_DATA_DIR . '/art_cat.json';
        $path_art       = NBDESIGNER_DATA_DIR . '/arts.json';
        $result['flag'] = 1;
        $result['cat']  = $this->nbdesigner_read_json_setting( $path_cat );
        $result['arts'] = $this->nbdesigner_read_json_setting( $path_art );
        echo json_encode( $result );
        wp_die();
    }
    public function _nbdesigner_get_font(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], 'nbdesigner-get-data' ) && NBDESIGNER_ENABLE_NONCE ) {
            die('Security error');
        }
        $result = array();
        $path   = NBDESIGNER_DATA_DIR . '/fonts';
        $cats   = Nbdesigner_IO::get_list_folder( $path, 1 );
        foreach ( $cats as $key => $cat ){
            $result['cat'][] = array(
                'name'  => basename( $cat ),
                'id'    => $key
            );
            $list = Nbdesigner_IO::get_list_files( $path . '/' . basename( $cat ), 1 );
            $arts = preg_grep( '/\.(ttf|woff)(?:[\?\#].*)?$/i', $list );
            foreach( $arts as $k => $art ) {
                $result['fonts'][] = array(
                    'name'  => pathinfo( $art, PATHINFO_FILENAME ),
                    'id'    => $k,
                    'cat'   => array( $key ),
                    'alias' => 'nbfont' . substr( md5( rand( 0, 999999 ) ), 0, 10),
                    'file'  => '',
                    'url'   => Nbdesigner_IO::wp_convert_path_to_url( $art )
                );
            }
        }
        $path_google_font       = NBDESIGNER_DATA_DIR . '/googlefonts.json';
        $result['google_font']  = $this->nbdesigner_read_json_setting( $path_google_font );
        $result['flag']         = 1;
        echo json_encode( $result );
        wp_die();
    }    
    public function nbdesigner_get_font(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], 'nbdesigner-get-data' ) && NBDESIGNER_ENABLE_NONCE ) {
            die('Security error');
        }
        $result                 = array();
        $path_cat               = NBDESIGNER_DATA_DIR . '/font_cat.json';
        $path_font              = NBDESIGNER_DATA_DIR . '/fonts.json';
        $path_google_font       = NBDESIGNER_DATA_DIR . '/googlefonts.json';
        $result['flag']         = 1;
        $result['cat']          = $this->nbdesigner_read_json_setting( $path_cat );
        $result['fonts']        = $this->nbdesigner_read_json_setting( $path_font );
        $result['google_font']  = $this->nbdesigner_read_json_setting( $path_google_font );
        echo json_encode( $result );
        wp_die();
    }
    public function nbdesigner_get_pattern(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], 'nbdesigner-get-data' ) && NBDESIGNER_ENABLE_NONCE ) {
            die( 'Security error' );
        }
        $result         = array();
        $path           = NBDESIGNER_PLUGIN_DIR . 'data/pattern.json';
        $result['flag'] = 1;
        $result['data'] = $this->nbdesigner_read_json_setting( $path );
        echo json_encode( $result );
        wp_die();
    }
    public function add_display_post_states( $post_states, $post ){
        if ( nbd_get_page_id( 'gallery' ) === $post->ID ) {
            $post_states['nbd_gallery_page'] = esc_html__( 'NBD Gallery Page', 'web-to-print-online-designer' );
        }
        if ( nbd_get_page_id( 'create_your_own' ) === $post->ID ) {
            $post_states['nbd_create_your_own_page'] = esc_html__( 'NBD Design Editor Page', 'web-to-print-online-designer' );
        }
        if ( nbd_get_page_id( 'designer' ) === $post->ID ) {
            $post_states['nbd_designer_page'] = esc_html__( 'NBD Designer gallery Page', 'web-to-print-online-designer' );
        }   
        if ( nbd_get_page_id( 'product_builder' ) === $post->ID ) {
            $post_states['nbd_product_builder_page'] = esc_html__( 'NBD Product builder Page', 'web-to-print-online-designer' );
        }
        if ( nbd_get_page_id( 'studio' ) === $post->ID ) {
            $post_states['nbd_studio_page'] = esc_html__( 'NBD Studio Page', 'web-to-print-online-designer' );
        }
        return $post_states;
    }
    public function nbdesigner_frontend_translate(){
        require_once ABSPATH . 'wp-admin/includes/translation-install.php';
        $languages  = wp_get_available_translations();
        $path       = NBDESIGNER_PLUGIN_DIR . 'data/language.json';
        $path_data  = NBDESIGNER_DATA_CONFIG_DIR . '/language.json';
        if( file_exists( $path_data ) ) $path = $path_data;
        $path_lang      = NBDESIGNER_PLUGIN_DIR . 'data/language/en_US.json';
        $path_data_lang = NBDESIGNER_DATA_CONFIG_DIR . '/language/en_US.json';
        if( file_exists( $path_data_lang ) ) $path_lang = $path_data_lang;
        $list = json_decode( file_get_contents( $path ) );
        $lang = json_decode( file_get_contents( $path_lang ) );
        if( is_array( $lang ) ){
            $langs = (array)$lang[0];
            asort( $langs );
        }
        require( NBDESIGNER_PLUGIN_DIR . 'views/nbdesigner-translate.php' );
    }
    public function nbdesigner_save_language(){
        $data = array(
            'mes'   => esc_html__('You do not have permission to edit language!', 'web-to-print-online-designer'),
            'flag'  => 0
        );
        if ( !wp_verify_nonce( $_POST['nonce'], 'nbdesigner_add_cat' ) || !current_user_can( 'edit_nbd_language' ) ) {
            echo json_encode( $data );
            wp_die();
        }
        if( isset( $_POST['langs'] ) ){
            $langs      = array();
            $langs[0]   = $_POST['langs'];
        }
        if( isset( $_POST['code'] ) ){
            $code       = $_POST['code'];
        } 
        if( isset( $langs ) && isset( $code ) ){
            $path_lang = NBDESIGNER_PLUGIN_DIR . 'data/language/' . $code . '.json';
            $path_data = NBDESIGNER_DATA_CONFIG_DIR . '/language/' . $code . '.json';
            if( file_exists( $path_data ) ){
                $path_lang = $path_data;
            }else{
                if( $code = "en_US" ) {
                    $path_lang = NBDESIGNER_DATA_CONFIG_DIR . '/language/en_US.json';
                }
            } 
            foreach ( $langs[0] as $key => $lang ){
                //$lang = preg_replace('#\\\\{2,}#','\\', $lang);
                //$lang = str_replace('\\\\','\\', $lang);
                $lang           = stripslashes( $lang );
                $langs[0][$key] = strip_tags( $lang );
            }
            $res            = json_encode( $langs );
            file_put_contents( $path_lang, $res );
            $data['mes']    = esc_html__( 'Update language success!', 'web-to-print-online-designer' );
            $data['flag']   = 1;
        }else{
            $data['mes'] = esc_html__( 'Update language failed!', 'web-to-print-online-designer' );
        }
        echo json_encode( $data );
        wp_die();
    }
    public function nbdesigner_get_language( $code ){
        if ( !( wp_verify_nonce( $_POST['nonce'], 'nbdesigner_add_cat' ) || wp_verify_nonce( $_POST['nonce'], 'save-design' ) ) && NBDESIGNER_ENABLE_NONCE ) {
            die( 'Security error' );
        }
        if( !isset( $code ) ){
            $code = "en";
        }else if( isset( $_POST['code'] ) ) {
            $code = $_POST['code'];
        }
        $data = nbd_get_language( $code );
        echo json_encode( $data );
        wp_die();
    }
    public function nbdesigner_delete_language(){
        $data = array(
            'mes'   =>  esc_html__( 'You do not have permission to delete language!', 'web-to-print-online-designer' ),
            'flag'  => 0
        );
        if ( !wp_verify_nonce( $_POST['nonce'], 'nbdesigner_add_cat' ) || !current_user_can( 'edit_nbd_language' ) ) {
            echo json_encode( $data );
            wp_die();
        }
        $code                   = sanitize_text_field( $_POST['code'] );
        $index                  = absint( $_POST['index'] );
        $path_lang              = NBDESIGNER_DATA_CONFIG_DIR . '/language/' . $code . '.json';
        $path_data_cat_lang     = NBDESIGNER_DATA_CONFIG_DIR . '/language.json';
        $cats                   = json_decode(file_get_contents( $path_data_cat_lang ) );
        $primary_lang_code      = $cats[0]->code;
        $path_primary_lang      = NBDESIGNER_DATA_CONFIG_DIR . '/language/'.$primary_lang_code . '.json';
        if( !file_exists( $path_primary_lang ) ){
            $path_primary_lang = NBDESIGNER_PLUGIN_DIR . 'data/language/'.$primary_lang_code . '.json';
        }
        if( $index != 0 ){
            if( unlink( $path_lang ) ) {
                $data['flag'] = 1;
                $this->nbdesigner_delete_json_setting( $path_data_cat_lang, $index );
                $data['mes']    = esc_html__( 'Delete language success!', 'web-to-print-online-designer' );
                $langs          = json_decode( file_get_contents( $path_primary_lang ) ); 
                $data['langs']  = (array)$langs[0];
            }
        }else {
            $data['mes'] = esc_html__( 'Oops! Can not delete primary language!', 'web-to-print-online-designer' );
        }
        echo json_encode( $data );
        wp_die();
    }
    public function nbdesigner_create_language(){
        $data = array(
            'mes'   =>  esc_html__( 'You do not have permission to create language!', 'web-to-print-online-designer' ),
            'flag'  => 0
        );
        if ( !wp_verify_nonce( $_POST['nbdesigner_newlang_hidden'], 'nbdesigner-new-lang' ) || !current_user_can( 'edit_nbd_language' ) ) {
            echo json_encode( $data );
            wp_die();
        } 
        if( isset( $_POST['nbdesigner_codelang'] ) && isset( $_POST['nbdesigner_namelang'] ) ){
            $code                       = sanitize_text_field( $_POST['nbdesigner_codelang'] );
            $path_lang                  = NBDESIGNER_DATA_CONFIG_DIR . '/language/' . $code . '.json';
            $path_original_lang         = NBDESIGNER_PLUGIN_DIR . 'data/language/en_US.json';
            $path_original_data_lang    = NBDESIGNER_DATA_CONFIG_DIR . '/language/en_US.json';
            if( file_exists( $path_original_data_lang ) ) $path_original_lang = $path_original_data_lang;
            $path_cat_lang      = NBDESIGNER_PLUGIN_DIR . 'data/language.json';
            $path_data_cat_lang = NBDESIGNER_DATA_CONFIG_DIR . '/language.json';
            if( file_exists( $path_data_cat_lang ) ) $path_cat_lang = $path_data_cat_lang;
            $cats       = json_decode( file_get_contents( $path_cat_lang ) );
            $lang       = json_decode( file_get_contents( $path_original_lang ) );
            $_cat       = array();
            $_cat['id'] = 1;
            if( is_array( $cats ) ){
                foreach( $cats as $cat ){
                    if( $cat->code == $code ){
                        $code .=  rand( 1, 100 );
                    }
                    $_cat['id'] = $cat->id;
                }
                $_cat['id'] += 1;
            }else{
                $data['mes'] = 'error';
                echo json_encode( $data );
                wp_die();
            } 
            if( is_array( $lang ) ){
                $data['langs']  = (array)$lang[0];
                $data['code']   = $code;
                $_cat['code']   = $code;
                $data['name']   = sanitize_text_field( $_POST['nbdesigner_namelang'] );
                $_cat['name']   = $data['name'];
                if( !copy( $path_original_lang, $path_lang ) ) {
                    $data['mes'] = 'error';
                }else{
                    array_push( $cats, $_cat );
                    file_put_contents( $path_cat_lang, json_encode( $cats ) );
                    file_put_contents( $path_data_cat_lang, json_encode( $cats ) );
                }
                $data['mes']    = esc_html__( 'Your language has been created successfully!', 'web-to-print-online-designer' );
                $data['flag']   = 1;
            }else{
                $data['mes'] = 'error';
            }
        }
        echo json_encode( $data );
        wp_die();
    }
    public function nbdesigner_editor_html(){
        if( isset( $_GET['nbd-route'] ) && $_REQUEST['nbd-route'] == 'nbd-3d-preview' ){
            $path = NBDESIGNER_PLUGIN_DIR . 'views/3d-preview.php';
            include( $path ); exit();
            return;
        }
        $task       = (isset($_GET['task']) && $_GET['task'] != '' ) ? $_GET['task'] : '';
        $task2      = (isset($_GET['task2']) && $_GET['task2'] != '' ) ? $_GET['task2'] : '';
        $product_id = (isset($_GET['product_id']) && absint($_GET['product_id']) != 0 ) ? $_GET['product_id'] : 0;
        if( $product_id == 0 ) return;
        $layout = nbd_get_product_layout( $product_id );
        $view   = (isset($_GET['view']) && $_GET['view'] != '' ) ? $_GET['view'] : $layout;
        if( $view == 'm' || $view == 'v' ){
            $path = NBDESIGNER_PLUGIN_DIR . 'views/nbdesigner-frontend-modern.php';
        } else {
            $path = NBDESIGNER_PLUGIN_DIR . 'views/nbdesigner-frontend-template.php';
        }
        if($task == 'reup' || $task2 == 'add_file' || $view == 'c'){
            $path = NBDESIGNER_PLUGIN_DIR . 'views/nbdesigner-frontend-template.php';
        }
        if( is_nbd_design_page() ){
            include( $path ); exit();
        }else{
            if ( ( ! defined('DOING_AJAX') || ! DOING_AJAX ) && ( ! isset( $_REQUEST['action'] ) || $_REQUEST['action'] != 'nbdesigner_editor_html' ) ) return;
            include( $path ); exit();
        }
    }
    public function nbdesigner_make_primary_design(){
        if (!wp_verify_nonce($_POST['nonce'], 'nbdesigner_template_nonce') || !current_user_can('administrator')) {
            die('Security error');
        }
        $result = array();
        if(isset($_POST['id']) && isset($_POST['folder']) && isset($_POST['task'])){
            $pid    = absint( $_POST['id'] );
            $folder = sanitize_text_field( $_POST['folder'] );
            $task   = sanitize_text_field( $_POST['task'] );
            $check  = true;
            if($task == 'primary'){
                $path_primary = NBDESIGNER_DATA_DIR . '/admindesign/' . $pid . '/primary'; 
                $path_primary_old = NBDESIGNER_DATA_DIR . '/admindesign/' . $pid . '/primary_old'; 
                $path_primary_new = NBDESIGNER_DATA_DIR . '/admindesign/' . $pid . '/' .$folder; 
                if(!rename($path_primary, $path_primary_old)) $check = false; 
                if(!rename($path_primary_new, $path_primary)) $check = false; 
                if(!rename($path_primary_old, $path_primary_new)) $check = false;
            }
            if( $check ) $result['mes'] = 'success'; else $result['mes'] = 'error';
        }else{
            $result['mes'] = esc_html__('Invalid data', 'web-to-print-online-designer');
        }  
        echo json_encode($result);
        wp_die();
    }
    public function nbdesigner_load_admin_design(){
        if ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) {
            die( 'Security error' );
        }
        $result = array();
        if( isset( $_POST['product_id'] ) ){
            $product_id     = absint($_POST['product_id']);
            $variation_id   = absint($_POST['variation_id']);
            $list_design    = array();
            $templates      = nbd_get_templates( $product_id, $variation_id );
            foreach ( $templates as $tem ){
                $path_preview   = NBDESIGNER_CUSTOMER_DIR . '/' . $tem['folder'] . '/preview';
                $listThumb      = Nbdesigner_IO::get_list_images( $path_preview );
                $image          = '';
                if( count( $listThumb ) ){
                    $image = Nbdesigner_IO::wp_convert_path_to_url( end( $listThumb ) );
                    $list_design[] = array(
                        'id'    => $tem['folder'],
                        'src'   => $image
                    );
                }
            }
            $result['data']     = $list_design;
            $result['mes']      = 'success';
        }else{
            $result['mes'] = esc_html__( 'Invalid data', 'web-to-print-online-designer' );
        }
        echo json_encode($result);
        wp_die();
    }
    public function nbdesigner_tools(){
        $custom_css = Nbdesigner_DebugTool::get_custom_css();
        $custom_js  = Nbdesigner_DebugTool::get_custom_js();
        if( isset( $_REQUEST['action']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'remove_log' ) ){
            unlink( NBDESIGNER_LOG_DIR . '/debug.log' );
            wp_safe_redirect( esc_url_raw( admin_url( 'admin.php?page=nbdesigner_tools' ) ) );
            exit();
        }
        include_once( NBDESIGNER_PLUGIN_DIR . 'views/nbdesigner-tools.php' );
    }
    public function system_info(){
        if( isset( $_GET['mode'] ) && $_GET['mode'] == 'all' ){
            include_once( NBDESIGNER_PLUGIN_DIR . 'views/system-info-all.php' );
        }else{
            include_once( NBDESIGNER_PLUGIN_DIR . 'views/system-info.php' );
        }
    }
    public function nbd_support(){
        $remote     = get_transient( 'nbd_upgrade_news_web-to-print-online-designer' );
        $nbd_news   = json_decode( $remote['body'] );
        include_once( NBDESIGNER_PLUGIN_DIR . 'views/support.php' );
    }
    public function nbdesigner_variation_settings_fields( $loop, $variation_data, $variation ){
        $vid                    = $variation->ID;
        $parent_id              = $variation->post_parent;
        $option                 = unserialize( get_post_meta( $parent_id, '_nbdesigner_option', true ) );
        $enable                 = get_post_meta( $vid, '_nbdesigner_variation_enable', true );
        $default                = nbd_default_product_setting();
        $designer_setting       = array();
        $designer_setting[0]    = $default;
        $dpi                    = get_post_meta( $vid, '_nbdesigner_dpi', true );
        if( $dpi == "" ) $dpi   = nbdesigner_get_option( 'nbdesigner_default_dpi' );
        $unit                   = isset( $option['unit'] ) ? $option['unit'] : nbdesigner_get_option( 'nbdesigner_dimensions_unit', 'cm' );
        $_designer_setting      = unserialize( get_post_meta( $vid, '_designer_variation_setting', true ) );
        if ( isset( $_designer_setting[0] ) ){
            $designer_setting   = $_designer_setting;
            if(! isset( $designer_setting[0]['version'] ) || $_designer_setting[0]['version'] < 160 ) {
                $designer_setting = $this->update_config_product_160( $designer_setting );
            }
            if( !isset( $designer_setting[0]['version'] ) || $_designer_setting[0]['version'] < 180 ) {
                $designer_setting = NBD_Update_Data::nbd_update_media_v180( $designer_setting );
            }
        }
        $designer_setting = nbd_update_config_default( $designer_setting );
        include( NBDESIGNER_PLUGIN_DIR . 'views/nbdesigner-box-design-setting-variation.php' );
    }
    public function nbdesigner_save_variation_settings_fields( $post_id ){
        if( !( current_user_can( 'administrator' ) || current_user_can( 'shop_manager' ) ) || !isset( $_POST['_nbdesigner_enable' . $post_id] ) ){
            return $post_id;
        }
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }
        $var        = get_post( $post_id );
        $enable     = wc_clean( $_POST['_nbdesigner_enable'.$post_id] );
        $setting    = serialize( $_POST['_designer_setting'.$post_id] );
        if( !$this->nbdesigner_allow_create_product( $var->post_parent ) ) return;
        update_post_meta( $post_id, '_nbdesigner_variation_enable', $enable );
        update_post_meta( $post_id, '_designer_variation_setting', $setting );
    }
    public function nbdesigner_copy_image_from_url(){
        if ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) {
            die( 'Security error' );
        }
        $url = $_POST['url'];
        $ext = $this->nbdesigner_get_extension( $url );
        if( isset( $_POST['gapi'] ) && isset( $_POST['gapi']['name'] ) ){
            $ext = $this->nbdesigner_get_extension( $_POST['gapi']['name'] );
        }
        $allow_extension = array( 'jpg', 'jpeg', 'png', 'gif' );
        if( !in_array( strtolower( $ext ), $allow_extension ) ) $ext = 'png';
        $new_name       = strtotime( "now" ) . substr( md5( rand( 1111,9999 ) ), 0, 8 ) . '.' . $ext;
        $path           = Nbdesigner_IO::create_file_path( NBDESIGNER_TEMP_DIR, $new_name );
        $res['flag']    = 0;
        $res['src']     = NBDESIGNER_TEMP_URL . $path['date_path'];
        if( isset( $_POST['gapi'] ) && strpos( $url, 'drive.google.com/file' ) ){
            $param          = wc_clean( $_POST['gapi'] );
            $oAuthToken     = $param['oAuthToken'];
            $fileId         = $param['fileId'];
            $new_name       = strtotime( "now" ) . substr( md5( rand( 1111, 9999 ) ), 0, 8 ) . '_' . $param['name']; 
            $path           = Nbdesigner_IO::create_file_path( NBDESIGNER_TEMP_DIR, $new_name );
            $getUrl         = 'https://www.googleapis.com/drive/v2/files/' . $fileId . '?alt=media';
            $authHeader     = 'Authorization: Bearer ' . $oAuthToken;
            $ch             = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $getUrl);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array($authHeader));
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $data   = curl_exec($ch);
            $error  = curl_error($ch);
            curl_close($ch);
            if( !$error ){
                file_put_contents( $path['full_path'], $data );
                $res['src']     = NBDESIGNER_TEMP_URL . $path['date_path'];
                $res['flag']    = 1;
            }
        }else{
            if( @copy( $url, $path['full_path'] ) ){
                $res['flag'] = 1;
            }else{
                $response = wp_remote_get( $url, array( 'timeout' => 30 ) );
                if ( is_wp_error( $response ) ){
                    echo json_encode( $res );
                    wp_die();
                }else{
                    $result = @file_put_contents( $path['full_path'], $response['body'] );
                    unset( $response['body'] );
                    if ( $result === false ) {
                        $res['flag'] = 0;
                    }else{
                        $res['flag'] = 1;
                    }
                }
            }
        }
        do_action( 'nbu_upload_image_from_url', $path, $res );

        if( $res['flag'] == 1 && !isset( $_POST['nbu_adu'] ) && nbdesigner_get_option( 'nbdesigner_enable_generate_photo_thumb', 'no' ) == 'yes' ){
            $resizable_extensions = array( 'jpg', 'jpeg', 'png' );
            if( in_array( $ext, $resizable_extensions ) ){
                $full_path                  = is_array( $path ) ? $path['full_path'] : $path;
                $preview_size               = apply_filters( 'nbd_max_photo_thumb_size', 800 );
                list( $width, $height )     = getimagesize( $full_path );
                if( $width > $preview_size || $height > $preview_size ){
                    $infos          = pathinfo( $full_path );
                    $path_preview   = $infos['dirname'] . '/' . $infos['filename'] . '_preview.' . $infos['extension'];
                    if( $ext == 'png' ){
                        NBD_Image::nbdesigner_resize_imagepng( $full_path, $preview_size, $preview_size, $path_preview );
                    } else {
                        NBD_Image::nbdesigner_resize_imagejpg( $full_path, $preview_size, $preview_size, $path_preview );
                    }

                    if( file_exists( $path_preview ) ){
                        $res['origin_url']  = $res['src'];
                        $res['src']         = Nbdesigner_IO::wp_convert_path_to_url( $path_preview );
                        $res['width']       = $width;
                        $res['height']      = $height;
                    }
                }
            }
        }
        do_action('nbu_upload_image_from_url', $path, $res);
        echo json_encode( $res );
        wp_die();
    }
    public function nbdesigner_add_tinymce_editor(){
        global $typenow;
        // check user permissions
        if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) return;
        $post_types = get_post_types();
        if ( !is_array( $post_types ) ) $post_types = array( 'post', 'page' );
        // verify the post type
        if ( !in_array( $typenow, $post_types ) ) return;
        // check if WYSIWYG is enabled
        if ( get_user_option( 'rich_editing') == 'true' ){
            add_filter( 'mce_external_plugins', array( $this, 'nbdesigner_add_tinymce_shortcode_editor_plugin' ) );
            add_filter( 'mce_buttons', array( $this, 'nbdesigner_add_tinymce_shortcode_editor_button' ) );
        }
        add_action( 'in_admin_footer', array( $this, 'nbdesigner_add_tiny_mce_shortcode_dialog' ) );
    }   
    public function nbdesigner_add_tinymce_shortcode_editor_plugin( $plugin_array ){
        $plugin_array['nbdesigner_button'] = NBDESIGNER_JS_URL . 'nbdesigner-tinymce-shortcode.js';
        return $plugin_array;
    }
    public function nbdesigner_add_tinymce_shortcode_editor_button( $buttons ){
        array_push( $buttons, "nbdesigner_button" );
        return $buttons;
    }    
    public function nbdesigner_add_tiny_mce_shortcode_dialog(){
        include ( NBDESIGNER_PLUGIN_DIR . 'views/nbdesigner-shortcode-dialog.php' );
    }
    public function nbdesigner_get_suggest_design(){
        if ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ){
            die( 'Security error' );
        }
        $result = array(
            'mes'   => 'success',
            'flag'  => 1
        );
        if( isset( $_POST['product_id'] ) && isset( $_POST['products'] ) && isset( $_POST['variation_id'] ) ){
            $product_id         = absint( $_POST['product_id'] );
            $products           = wc_clean( $_POST['products'] );
            $variation_id       = absint( $_POST['variation_id'] );
            $nbd_item_cart_key  = ( $variation_id > 0 ) ? $product_id . '_' . $variation_id : $product_id;
            $nbd_item_key       = WC()->session->get( 'nbd_item_key_' . $nbd_item_cart_key );
            $path_src           = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key;
            if( isset( $nbd_item_key ) && is_array( $products ) ){
                $result['nbd_item_key'] = $nbd_item_key;
                foreach ( $products as $tg_product_id ){
                    $tg_variation_id = nbd_get_default_variation_id( $tg_product_id );
                    if($tg_variation_id > 0){
                        $product_config     = unserialize( get_post_meta( $tg_variation_id, '_designer_variation_setting', true ) );
                        $enable_variation   = get_post_meta( $tg_variation_id, '_nbdesigner_variation_enable', true );
                        if ( !( $enable_variation && isset( $product_config[0] ) ) ){
                            $product_config = unserialize( get_post_meta( $tg_product_id, '_designer_setting', true ) );
                        }
                    }else {
                        $product_config = unserialize( get_post_meta( $tg_product_id, '_designer_setting', true ) );
                    }
                    $ref_product_config = unserialize( file_get_contents( $path_src . '/product.json' ) );
                    foreach ( $product_config as $key => $_config ){
                        if( isset( $ref_product_config[$key] ) ){
                            $product_config[$key]['img_src']    = is_numeric( $product_config[$key]['img_src'] ) ? wp_get_attachment_url( $product_config[$key]['img_src'] ) : $product_config[$key]['img_src'];
                            $ref_width                          = $ref_product_config[$key]['area_design_width'];
                            $ref_height                         = $ref_product_config[$key]['area_design_height'];
                            $_width                             = $product_config[$key]['area_design_width'];
                            $_height                            = $product_config[$key]['area_design_height'];
                            if( $ref_width / $ref_height > $_width / $_height ){
                                $product_config[$key]['area_design_height'] = round( $_width * $ref_height / $ref_width );
                                $product_config[$key]['area_design_top']    = round( 250 - $product_config[$key]['area_design_height'] / 2 );
                            } else {
                                $product_config[$key]['area_design_width']  = round( $_height * $ref_width / $ref_height );
                                $product_config[$key]['area_design_left']   = round( 250 - $product_config[$key]['area_design_width'] / 2 );
                            }
                        }
                    }
                    $path_dst       = NBDESIGNER_SUGGEST_DESIGN_DIR . '/' . substr( md5( uniqid() ), 0, 10 );
                    $product_config = nbd_get_media_for_data_product( $product_config );
                    $this->create_preview_design( $path_src, $path_dst, $product_config, 300, 300, 1 );
                    $list = Nbdesigner_IO::get_list_images( $path_dst, $level = 1 );
                    $result['images'][$tg_product_id][] = Nbdesigner_IO::wp_convert_path_to_url( $list[0] );
                }
            }else{
                $result['mes']  = esc_html__( 'Missing product!', 'web-to-print-online-designer' );
                $result['flag'] = 0;
            }
        }else{
            $result['mes']  = esc_html__( 'Missing information!', 'web-to-print-online-designer' );
            $result['flag'] = 0;
        }
        echo json_encode( $result );
        wp_die();
    }
    /**
     * Allow multi design.
     * Add Product To Cart Multiple Times But As Different items.
     * since 1.5.0
     * 
     */
    public function nbd_add_cart_item_data( $cart_item_data, $product_id,  $variation_id ) {
        /* Force individual cart item */
        $nbd_item_cart_key  = ( $variation_id > 0 ) ? $product_id . '_' . $variation_id : $product_id;
        $nbd_session        = WC()->session->get( 'nbd_item_key_'.$nbd_item_cart_key );
        $nbu_session        = WC()->session->get( 'nbu_item_key_'.$nbd_item_cart_key );

        if( !isset( $_REQUEST['nbd-force-ignore-design'] ) ){
            if( isset( $_POST['nbd-reserve'] ) &&  $_POST['nbd-reserve'] != '' ){
                $nbd_session = wc_clean( $_POST['nbd-reserve'] );
            }
            if( isset( $_POST['nbu-reserve'] ) &&  $_POST['nbu-reserve'] != '' ){
                $nbu_session = wc_clean( $_POST['nbu-reserve'] );
            }
        }

        if ( isset( $nbd_session ) || ( !isset( $nbd_session ) && isset( $nbu_session ) ) ) {
            $unique_cart_item_key           = md5( microtime() . rand() );
            $cart_item_data['unique_key']   = $unique_cart_item_key;
        }
        /* Update list files upload */
        //if( $nbu_session && isset($_POST['nbd-upload-files']) && $_POST['nbd-upload-files'] != '' ){
        if( $nbu_session && isset( $_POST['nbd-upload-files'] ) ){
            $files = wc_clean( $_POST['nbd-upload-files'] );
            $this->update_files_upload( $files, $nbu_session );
        }
        if( isset( $_REQUEST['submit_form_mode2'] ) ){
            $nbu_session2       = WC()->session->get( 'nbu_item_key_' . $product_id );
            if( isset( $nbu_session2 ) ){
                $nbu_session = $nbu_session2;
            }
        }
        if( isset( $nbd_session ) ) $cart_item_data['nbd_item_meta_ds']['nbd'] = $nbd_session;
        if( isset( $nbu_session ) ) $cart_item_data['nbd_item_meta_ds']['nbu'] = $nbu_session;
        
        $design_id = WC()->session->get( 'nbd_design_id_'.$nbd_item_cart_key );
        if( $design_id ){
            $cart_item_data['nbd_design_id'] = $design_id;
        }
        return $cart_item_data;
    }
    public function get_cart_item_from_session( $cart_item, $values ){
        if ( isset( $values['nbd_item_meta_ds'] ) ) {
            $cart_item['nbd_item_meta_ds'] = $values['nbd_item_meta_ds'];

            if( isset( $values['nbd_item_meta_ds']['initial_price'] ) ){
                $product_id     = $cart_item['product_id'];
                $option         = unserialize( get_post_meta( $product_id, '_nbdesigner_option', true ) );
                if( $option['extra_price'] ){
                    $decimals   = wc_get_price_decimals();
                    $price      = $price = $cart_item["nbd_item_meta_ds"]["initial_price"];
                    $new_price  = $price;
                    if( $option['type_price'] == 1 ){
                        $new_price += $option['extra_price'];
                    }else{
                        $new_price += $price * $option['extra_price'] / 100;
                    }
                    $new_price = round( $new_price, $decimals );
                    $cart_item['data']->set_price( $new_price );
                }
            }
        }
        if ( isset( $values['nbd_design_id'] ) ) {
            $cart_item['nbd_design_id'] = $values['nbd_design_id'];
        }
        return $cart_item;
    }
    public function update_files_upload( $files, $nbu_session = '' ){
        if( isset( $_POST['nbu-ignore-update'] ) ) return;
        $files          = explode( '|', $files );
        $path           = NBDESIGNER_UPLOAD_DIR . '/' . $nbu_session;
        $list_files     = Nbdesigner_IO::get_list_files( $path );
        foreach ( $list_files as $file ){
            $filename = basename( $file );
            if( !in_array( $filename, $files ) ){
                unlink( $path . '/' . $filename );
            }
        }
    }
    public function nbd_update_customer_upload(){
        if ( ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) || !isset( $_POST['nbd_file'] ) ) {
            die( 'Security error' );
        }
        $cart_item_key  = wc_clean( $_POST['cart_item_key'] );
        $design_type    = ( isset( $_POST['design_type'] ) && $_POST['design_type'] != '' ) ? $_POST['design_type'] : '';

        if( $design_type == '' && !nbd_check_cart_item_exist( $cart_item_key ) ) {
            esc_html_e( 'Item not exist in cart', 'web-to-print-online-designer' );
            wp_die();
        }
        $this->update_files_upload( $_POST['nbd_file'], $_POST['nbu_item_key'] );
        if( isset( $_POST['order_id'] ) && $design_type == 'edit_order' ){
            $order_id = absint( $_POST['order_id'] );
            $order = wc_get_order($order_id);
            $order->update_meta_data( '_nbdesigner_upload_order_changed', 1 );
            $order->save();
        }
        do_action( 'nbu_update_upload_files' );
        echo 'success';
        wp_die();
    }
    public function nbdesigner_migrate_domain(){
        Nbdesigner_DebugTool::update_data_migrate_domain();
    }
    public function nbdesigner_restore_data_migrate_domain(){
        Nbdesigner_DebugTool::restore_data_migrate_domain();
    }
    public function nbdesigner_theme_check(){
        Nbdesigner_DebugTool::theme_check_hook();
    }
    public function nbdesigner_custom_css(){
        Nbdesigner_DebugTool::save_custom_css();
    }   
    public function nbdesigner_custom_js(){
        Nbdesigner_DebugTool::save_custom_js();
    }
    public function nbd_fix_pdf_font(){
        Nbdesigner_DebugTool::nbd_fix_pdf_font();
    }
    public function nbdesigner_save_design_to_pdf(){
        if ( !wp_verify_nonce( $_POST['_wpnonce'], 'nbdesigner_pdf_nonce' ) && NBDESIGNER_ENABLE_NONCE ) {
            die( 'Security error' );
        }

        if( !class_exists( 'TCPDF' ) ){
            require_once( NBDESIGNER_PLUGIN_DIR.'includes/tcpdf/tcpdf.php' );
        }
        require_once( NBDESIGNER_PLUGIN_DIR.'includes/fpdi/autoload.php' );

        $pdfs               = wc_clean( $_POST['pdf'] );
        $force              = wc_clean( $_POST['force_same_format'] );
        $from_type          = wc_clean( $_POST['from_type'] );
        $order_id           = absint( $_POST['order_id'] );
        $nbd_item_key       = wc_clean( $_POST['nbd_item_key'] );
        $dpi                = absint( $_POST['dpi'] );
        $img_ext            = array( 'jpg', 'jpeg', 'png' );
        $svg_ext            = array( 'svg' );
        $eps_ext            = array( 'eps', 'ai' );
        $pdf_ext            = array( 'pdf' );
        /* Add custom font */
        $used_font_path     = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/used_font.json';
        $used_font          = json_decode( file_get_contents( $used_font_path ) );
        $datas              = unserialize( file_get_contents( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key .'/product.json' ) );
        $option             = unserialize( file_get_contents( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key .'/option.json' ) );
        $config             = nbd_get_data_from_json( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/config.json' );
        $layout             = isset( $option['layout'] ) ? $option['layout'] : 'm';
        $has_custom_font    = false;
        if( $from_type == 3 ){
            /* From svg */
            $path_font = array();
            foreach( $used_font as $font ){
                $font_name = $font->name;
                if( $font->type == 'google' ){
                    $path_font = nbd_download_google_font( $font_name );
                }else{
                    $has_custom_font = true;
                    $_font = nbd_get_font_by_alias( $font->alias );
                    if( $_font ){
                        foreach( $_font->file as $key => $font_file ){
                            $path_font[$key] = NBDESIGNER_FONT_DIR . '/' . $font_file;
                        }
                    }
                }
                $true_type = nbd_get_truetype_fonts();
                if ( in_array( $font_name, $true_type ) ) {
                    foreach( $path_font as $pfont ){
                        $fontname = TCPDF_FONTS::addTTFfont( $pfont, 'TrueType', '', 32 );
                    }
                }else{
                    foreach( $path_font as $pfont ){
                        $fontname = TCPDF_FONTS::addTTFfont( $pfont, '', '', 32 );
                    }
                }
            } 
            if( $has_custom_font ) {
                //todo something to change font-family
            }
        }
        if( !is_array( $pdfs ) ) die( 'Security error' );
        $result = array();
        if( $force ){
            $mTop           = $pdfs[0]["margin-top"];
            $mBottom        = $pdfs[0]["margin-bottom"];
            $pdf_format     = $pdfs[0]["format"];
            $mLeft          = $pdfs[0]["margin-left"];
            $mRight         = $pdfs[0]["margin-right"];
            $bgWidth        = $pdfs[0]['product-width'];
            $bgHeight       = $pdfs[0]['product-height'];
            $orientation    = "";
            $include_bg     = isset( $pdfs[0]['include_background'] )  ? $pdfs[0]['include_background'] : 1;
            if( $include_bg == 0 ){
                $bgWidth    = $pdfs[0]['cd-width'];
                $bgHeight   = $pdfs[0]['cd-height'];
                $mLeft      = $mRight = $mBottom = $mTop = 0;
            }
            
            if( $pdf_format == '-1' ){
                $pWidth         = $bgWidth + $mLeft + $mRight;
                $pHeight        = $bgHeight + $mTop + $mBottom;
                $pdf_format     = array( $pWidth, $pHeight );
                if($pWidth > $pHeight){
                    $orientation = "L";
                }else {
                    $orientation = "P";
                }
            }

            $pdf = new Fpdi\TcpdfFpdi( $orientation, 'mm', $pdf_format, true, 'UTF-8', false );

            $pdf->SetMargins( $mLeft, $mTop, $mRight, true );
            $pdf->SetCreator( get_site_url() );
            $pdf->SetTitle( get_bloginfo( 'name' ) );
            $pdf->setPrintHeader( false );
            $pdf->setPrintFooter( false );
            $pdf->SetAutoPageBreak( TRUE, 0 );
            $pdf->SetFont( 'helvetica', '', 14, '', false );
        }
        foreach( $pdfs as $key => $_pdf ){
            $customer_design    = $_pdf['customer-design'];
            $bTop               = (float)$_pdf['bleed-top'];
            $bLeft              = (float)$_pdf['bleed-left'];
            $bRight             = (float)$_pdf['bleed-right'];
            $bBottom            = (float)$_pdf['bleed-bottom'];
            $bgWidth            = (float)$_pdf['product-width'];
            $bgHeight           = (float)$_pdf['product-height'];
            $showBleed          = $_pdf['show-bleed-line'];
            $orientation        = $_pdf['orientation'];
            $mTop               = (float)$_pdf["margin-top"];
            $mLeft              = (float)$_pdf["margin-left"];
            $mRight             = (float)$_pdf["margin-right"];
            $mBottom            = (float)$_pdf["margin-bottom"];
            $cdTop              = (float)$_pdf["cd-top"];
            $cdLeft             = (float)$_pdf["cd-left"];
            $cdWidth            = (float)$_pdf["cd-width"];
            $cdHeight           = (float)$_pdf["cd-height"];
            $background         = $_pdf['background'];
            $pdf_format         = $_pdf['format'];
            $bg_type            = $_pdf['bg_type'];
            $bg_color_value     = $_pdf['bg_color_value'];
            $includeBg          = isset( $_pdf['include_background'] ) ? $_pdf['include_background'] : 1;
            $includeBg          = ( $bg_type == 'image' ) ? $includeBg : 1;
            if( $includeBg == 0 ){
                $bgWidth    = $cdWidth;
                $bgHeight   = $cdHeight;
                $mLeft = $mRight = $mBottom = $mTop = $cdTop = $cdLeft = 0;
            }
            if( $bg_type == 'image' ){
                $path_bg = Nbdesigner_IO::convert_url_to_path( $background );
            }
            if( $customer_design != '' ){
                $path_cd = Nbdesigner_IO::convert_url_to_path( $customer_design );
            }
            if( $pdf_format == '-1' ){
                $pWidth     = $bgWidth + $mLeft + $mRight;
                $pHeight    = $bgHeight + $mTop + $mBottom;
                $pdf_format = array( $pWidth, $pHeight );
                if( $pWidth > $pHeight ){
                    $orientation = "L";
                }else {
                    $orientation = "P";
                }
            }
            if( !$force ){
                $pdf = new Fpdi\TcpdfFpdi( $orientation, 'mm', $pdf_format, true, 'UTF-8', false );

                $pdf->SetMargins( $mLeft, $mTop, $mRight, true );
                $pdf->SetCreator( get_site_url() );
                $pdf->SetTitle( get_bloginfo( 'name' ) );
                $pdf->setPrintHeader( false );
                $pdf->setPrintFooter( false );
                $pdf->SetAutoPageBreak( TRUE, 0 );
                $pdf->SetFont( 'helvetica', '', 14, '', false );
            }
            $pdf->AddPage( $orientation, $pdf_format );
            
            if( $bg_type == 'image' ){
                if( $includeBg ){
                    $check_img  = Nbdesigner_IO::checkFileType( basename( $path_bg ), $img_ext );
                    $check_svg  = Nbdesigner_IO::checkFileType( basename( $path_bg ), $svg_ext );
                    $check_eps  = Nbdesigner_IO::checkFileType( basename( $path_bg ), $eps_ext );
                    $check_pdf  = Nbdesigner_IO::checkFileType( basename( $path_bg ), $pdf_ext );
                    $ext        = pathinfo( $path_bg );
                    if( $check_img && $_pdf['origin_bg_pdf'] == ''){
                        $pdf->Image( $path_bg,$mLeft, $mTop, $bgWidth, $bgHeight, '', '', '', false, '' );
                    }
                    if( $check_svg && $_pdf['origin_bg_pdf'] == ''){
                        $pdf->ImageSVG( $path_bg, $mLeft,$mTop, $bgWidth, $bgHeight, '', '', '', 0, true );
                    }     
                    if( $check_eps && $_pdf['origin_bg_pdf'] == ''){
                       $pdf->ImageEps( $path_bg, $mLeft,$mTop, $bgWidth, $bgHeight, '', true, '', '', 0, true );
                    }  
                    if( $check_pdf || $_pdf['origin_bg_pdf'] != '' ){
                        $pdf_path = $path_bg;
                        if( $_pdf['origin_bg_pdf'] != '' ){
                            $pdf_path = NBDESIGNER_TEMP_DIR . $_pdf['origin_bg_pdf'];
                        }
                        
                        $number_pages = $pdf->setSourceFile( $pdf_path );
    
                        $page_index = 1;
                        if( $_pdf['origin_bg_pdf'] != '' && ( $number_pages - 1 ) >= $key ){
                            $page_index = $key + 1;
                        }
    
                        $pdf->tplId = $pdf->importPage( $page_index );
                        $pdf->useImportedPage( $pdf->tplId, 0, 0, $pWidth );
                    }
                }
            }elseif($bg_type == 'color') {
                $need_bg_color = true;

                if( isset( $config->areaDesignShapes ) && $config->areaDesignShapes[$key] ){
                    $need_bg_color  = false;
                }
                if( $datas[$key]['show_overlay'] == 1 && $datas[$key]['include_overlay'] == 1 ){
                    $need_bg_color  = true;
                }

                if( $need_bg_color ){
                    $pdf->Rect( $mLeft, $mTop,  $bgWidth, $bgHeight, 'F', '', hex_code_to_rgb( $bg_color_value ) );
                }
            }
            if( $showBleed == 'yes' && nbdesigner_get_option( 'nbdesigner_bleed_stack', 1 ) == 1 ){
                $pdf->Line(0, $mTop + $bTop, $mLeft + $bLeft, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line(0, $mTop + $bgHeight - $bBottom, $mLeft + $bLeft, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($bgWidth + $mLeft - $bRight, $mTop + $bTop, $bgWidth + $mLeft + $mRight, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($bgWidth + $mLeft - $bRight, $mTop + $bgHeight - $bBottom, $bgWidth + $mLeft + $mRight, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bLeft, 0, $mLeft + $bLeft, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bLeft, $mTop + $bgHeight - $bBottom, $mLeft + $bLeft, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bgWidth - $bRight, 0, $mLeft + $bgWidth - $bRight, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bgWidth - $bRight, $mTop + $bgHeight - $bBottom, $mLeft + $bgWidth - $bRight, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
            }
            if( $customer_design != '' ){
                if( $from_type != 3 ){
                    $_path_cd   = $path_cd;
                    $image_name = pathinfo( $path_cd, PATHINFO_FILENAME );
                    if( $from_type == 1 ) $_path_cd = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/jpg/' . $image_name . '.jpg';
                    if( $from_type == 2 ) $_path_cd = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/cmyk/' . $image_name . '.jpg';
                    $pdf->Image( $_path_cd, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth, $cdHeight, '', '', '', false, '' );
                } else if( $from_type == 3 ){
                    $include_pdf = false;
                    if( isset( $config->originPDFs ) && isset( $config->originPDFs[$key] ) && isset( $config->pdfStacks ) ){
                        $resource_pdfs = (array)$config->originPDFs[$key];
                        if( count( $resource_pdfs ) && $config->pdfStacks[$key] != '' ){
                            $include_pdf    = true;
                            $stack          = explode( '_', $config->pdfStacks[$key] );
                            $pdf_index      = 0;
                            foreach( $stack as $pos ){
                                if( $pos == 'P' ){
                                    $resource_pdf   = (array)$resource_pdfs[$pdf_index];
                                    $pdf_path       = NBDESIGNER_TEMP_DIR . $resource_pdf['origin_pdf'];
                                    $pdf_top        = $resource_pdf['top'] * $unitRatio + $mTop + $cdTop;
                                    $pdf_left       = $resource_pdf['left'] * $unitRatio + $mLeft + $cdLeft;
                                    $pdf_width      = $resource_pdf['width'] * $unitRatio;
                                    $pdf_height     = $resource_pdf['height'] * $unitRatio;
    
                                    $pdf->setSourceFile( $pdf_path );
                                    $tplId = $pdf->importPage( 1 );
                                    $pdf->useImportedPage( $tplId, $pdf_left, $pdf_top, $pdf_width, $pdf_height );
    
                                    $pdf_index++;
                                }else{
                                    $svg = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/part/svgpath/frame_' . $key . '_svg_part_' . $pos . '.svg';
                                    nbd_convert_svg_url( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/part/', 'frame_' . $key . '_svg_part_' . $pos . '.svg' );
                                    $pdf->ImageSVG( $svg, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth, $cdHeight, '', '', '', 0, true );
                                }
                            }
                        }
                    }
                    if( !$include_pdf ){
                        $svg = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/svgpath/frame_' . $key . '_svg.svg';
                        nbd_convert_svg_url( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/', 'frame_' . $key . '_svg.svg' );
                        $pdf->ImageSVG( $svg, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth, $cdHeight, '', '', '', 0, true );
                    }
                }
            }
            if($showBleed == 'yes' && nbdesigner_get_option('nbdesigner_bleed_stack') == 2){
//                $pdf->Line(0, $mTop + $bTop, $mLeft, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
//                $pdf->Line(0, $mTop + $bgHeight - $bBottom, $mLeft, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
//                $pdf->Line($bgWidth + $mLeft, $mTop + $bTop, $bgWidth + $mLeft + $mRight, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
//                $pdf->Line($bgWidth + $mLeft, $mTop + $bgHeight - $bBottom, $bgWidth + $mLeft + $mRight, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
//                $pdf->Line($mLeft + $bLeft, 0, $mLeft + $bLeft, $mTop, array('color' => array(0,0,0), 'width' => 0.05));
//                $pdf->Line($mLeft + $bLeft, $mTop + $bgHeight, $mLeft + $bLeft, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
//                $pdf->Line($mLeft + $bgWidth - $bRight, 0, $mLeft + $bgWidth - $bRight, $mTop, array('color' => array(0,0,0), 'width' => 0.05));
//                $pdf->Line($mLeft + $bgWidth - $bRight, $mTop + $bgHeight, $mLeft + $bgWidth - $bRight, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));

                $offset = 0;
                $pdf->Line(0, $mTop + $bTop, $mLeft + $bLeft - $offset, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line(0, $mTop + $bgHeight - $bBottom, $mLeft + $bLeft - $offset, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($bgWidth + $mLeft - $bRight + $offset, $mTop + $bTop, $bgWidth + $mLeft + $mRight, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($bgWidth + $mLeft - $bRight + $offset, $mTop + $bgHeight - $bBottom, $bgWidth + $mLeft + $mRight, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bLeft, 0, $mLeft + $bLeft, $mTop + $bTop - $offset, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bLeft, $mTop + $bgHeight - $bBottom + $offset, $mLeft + $bLeft, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bgWidth - $bRight, 0, $mLeft + $bgWidth - $bRight, $mTop + $bTop - $offset, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bgWidth - $bRight, $mTop + $bgHeight - $bBottom + $offset, $mLeft + $bgWidth - $bRight, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
            }
            if( $datas[$key]['show_overlay'] == 1 && $datas[$key]['include_overlay'] == 1 && $layout != 'c' ){
                $overlay = get_attached_file( $datas[$key]['img_overlay'] );
                if( $overlay ){
                    $check_over_img = Nbdesigner_IO::checkFileType( basename( $overlay ), $img_ext );
                    if( $check_over_img ){
                        //$pdf->Image($overlay, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth,$cdHeight, '', '', '', false, '');
                        $pdf->Image($overlay, $mLeft, $mTop,  $bgWidth, $bgHeight, '', '', '', false, '');
                    }
                }
            }
            if( isset( $config->contour ) ){
                $pdf->ImageSVG( '@' . $config->contour, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth, $cdHeight, '', '', '', 0, true );
            }
            if( !$force ){
                $folder = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/pdfs';
                if( !file_exists( $folder ) ){
                    wp_mkdir_p( $folder );
                }
                $output_file = $folder .'/' . sanitize_file_name($_pdf['name']) . $key . '.pdf';
                $pdf->Output( $output_file, 'F' );
                $result[] = array(
                    'link'  => Nbdesigner_IO::wp_convert_path_to_url( $output_file ) . '?t=' . time(),
                    'title' => $_pdf['name']
                );
            }
        }
        if( $force ){
            $folder = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key. '/pdfs';
            if(!file_exists($folder)){
                wp_mkdir_p($folder);
            }
            $output_file = $folder .'/'. $order_id .'_all' .'.pdf';
            $pdf->Output($output_file, 'F');
            $result[] = array(
                'link' => Nbdesigner_IO::wp_convert_path_to_url($output_file) . '?t=' . time(),
                'title' => $order_id
            );
        }
        echo json_encode( $result );
        wp_die();
    }
    public function nbdesigner_save_design_to_pdf_with_layout(){
        if ( !wp_verify_nonce( $_POST['_wpnonce'], 'nbdesigner_pdf_nonce' ) && NBDESIGNER_ENABLE_NONCE ) {
            die( 'Security error' );
        }

        $order_id               = absint( $_POST['layout']['order_id'] );
        $nbd_item_key           = wc_clean( $_POST['layout']['nbd_item_key'] );
        $paper_width            = (float)$_POST['layout']['size']['width'];
        $paper_height           = (float)$_POST['layout']['size']['height'];
        $paper_padding_top      = (float)$_POST['layout']['padding']['top'];
        $paper_padding_right    = (float)$_POST['layout']['padding']['right'];
        $paper_padding_bottom   = (float)$_POST['layout']['padding']['bottom'];
        $paper_padding_left     = (float)$_POST['layout']['padding']['left'];
        $page_margin_top        = (float)$_POST['layout']['margin']['top'];
        $page_margin_right      = (float)$_POST['layout']['margin']['right'];
        $page_margin_bottom     = (float)$_POST['layout']['margin']['bottom'];
        $page_margin_left       = (float)$_POST['layout']['margin']['left'];
        $order                  = wc_clean( $_POST['layout']['order'] );
        $divisor                = $order == 'sequentially' ? 1 : 2;

        nbd_export_pdfs( $nbd_item_key, false, false, 'no' );
        $pdf_path   = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/customer-pdfs';
        $list_pdf   = Nbdesigner_IO::get_list_files_by_type( $pdf_path, 1, 'pdf' );
        $list_pdf   = nbd_sort_file_by_side( $list_pdf );

        if( !class_exists('TCPDF') ){
            require_once( NBDESIGNER_PLUGIN_DIR.'includes/tcpdf/tcpdf.php' );
        }
        require_once( NBDESIGNER_PLUGIN_DIR.'includes/fpdi/autoload.php' );

        $pdf_format     = array( $paper_width, $paper_height );
        $orientation    = $paper_width > $paper_height ? "L" : "P";
        $pdf            = new Fpdi\TcpdfFpdi( $orientation, 'mm', $pdf_format, true, 'UTF-8', false );

        $pdf->SetMargins( 0, 0, 0, true );
        $pdf->SetCreator( get_site_url() );
        $pdf->SetTitle( get_bloginfo( 'name' ) );
        $pdf->setPrintHeader( false );
        $pdf->setPrintFooter( false );
        $pdf->SetAutoPageBreak( TRUE, 0 );
        $pdf->SetFont( 'helvetica', '', 14, '', false );
        $pdf->AddPage( $orientation, $pdf_format );

        $left   = $paper_padding_left + $page_margin_left;
        $top    = $paper_padding_top + $page_margin_top;

        foreach( $list_pdf as $key => $file ){
            if( $key % $divisor != 0 ) continue;
            $pdf->setSourceFile( $file );
            $tplIdx = $pdf->importPage( 1 );
            $size   = $pdf->getTemplateSize( $tplIdx );
            $width  = $size['width'];
            $height = $size['height'];

            if( ( $left + $width + $page_margin_right + $paper_padding_right ) > $paper_width && abs( ( $left + $width + $page_margin_right + $paper_padding_right ) - $paper_width ) > 0.1 ){
                $left   = $paper_padding_left + $page_margin_left;
                $top   += $height + $page_margin_bottom + $page_margin_top;
                if( ( $top + $height + $page_margin_bottom ) > $paper_height && abs( ( $top + $height + $page_margin_bottom ) - $paper_height ) > 0.1 ){
                    $top    = $paper_padding_top + $page_margin_top;
                    $pdf->AddPage( $orientation, $pdf_format );
                }
            }
            $pdf->useImportedPage( $tplIdx, $left, $top, $width, $height );
            $left   += $width + $page_margin_right + $page_margin_left;
        }
        if( $order == 'offset' ){
            $right  = $paper_width - $paper_padding_right - $page_margin_right;
            $top    = $paper_padding_top + $page_margin_top;
            $pdf->AddPage( $orientation, $pdf_format );
            foreach( $list_pdf as $key => $file ){
                if( $key % $divisor == 0 ) continue;
                $pdf->setSourceFile( $file );
                $tplIdx = $pdf->importPage( 1 );
                $size   = $pdf->getTemplateSize( $tplIdx );
                $width  = $size['width'];
                $height = $size['height'];
    
                if( ( $right - $width - $page_margin_left - $paper_padding_left ) < 0 && abs( $right - $width - $page_margin_left - $paper_padding_left ) > 0.1 ){
                    $right  = $paper_width - $paper_padding_right - $page_margin_right;
                    $top   += $height + $page_margin_bottom + $page_margin_top;
                    if( ( $top + $height + $page_margin_bottom ) > $paper_height && abs( ( $top + $height + $page_margin_bottom ) - $paper_height ) > 0.1 ){
                        $top    = $paper_padding_top + $page_margin_top;
                        $pdf->AddPage( $orientation, $pdf_format );
                    }
                }
                $pdf->useImportedPage( $tplIdx, $right - $width, $top, $width, $height );
                $right   -= ( $width + $page_margin_right + $page_margin_left );
            }
        }
        //$pdf->Text( 2, 2 , $order_id);

        $output_file = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key. '/design.pdf';
        $pdf->Output( $output_file, 'F' );

        $result = array(
            'link' => Nbdesigner_IO::wp_convert_path_to_url( $output_file ) . '?t=' . time(),
            'title' => $order_id
        );

        echo json_encode( $result );
        wp_die();
    }
    public function nbd_frontend_download_pdf( $final = false, $nbd_item_key = '', $return = false ){
        if ( !$final && ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) ) {
            die( 'Security error' );
        }
        if( !class_exists('TCPDF') ){
            require_once(NBDESIGNER_PLUGIN_DIR.'includes/tcpdf/tcpdf.php');
        }
        require_once( NBDESIGNER_PLUGIN_DIR.'includes/fpdi/autoload.php' );
        //require_once( NBDESIGNER_PLUGIN_DIR . 'includes/tcpdi/tcpdi.php' );

        $order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : 0;
        if( $order_id ) {
            $order = wc_get_order( $order_id );
            if( $order->get_status() == 'completed' ) $final = true;
        }
        $nbd_item_key       = $nbd_item_key == '' ? wc_clean( $_POST['nbd_item_key'] ) : $nbd_item_key;
        $enable_watermark   = nbdesigner_get_option( 'nbdesigner_enable_pdf_watermark', 'yes' );

        $folder = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key. '/customer-pdfs';
        $output_file = $final ? $folder .'/'. $nbd_item_key .'_final.pdf' : $folder .'/'. $nbd_item_key .'.pdf';

        if( nbdesigner_get_option( 'nbdesigner_enable_cloud2print_api', 'no' ) == 'yes' ){
            $need_watermark = ( ( $enable_watermark == 'yes' || $enable_watermark == 'before' ) && !$final ) ? true : false;
            $need_pw        = ( nbdesigner_get_option( 'nbdesigner_enable_pdf_password', 'no' ) == 'yes' && !$final ) ? true : false;

            nbd_cloud_export_pdfs( $nbd_item_key, $need_watermark, true, 'no', null, $need_pw );
            $output_file    = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key. '/customer-pdfs' .'/'. $nbd_item_key .'.pdf';
        } else {
            $path       = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key;
            $datas      = unserialize(file_get_contents( $path .'/product.json' ) );
            $option     = unserialize(file_get_contents( $path .'/option.json' ) );
            $config     = json_decode(file_get_contents( $path .'/config.json' ) );
            $layout     = isset( $option['layout'] ) ? $option['layout'] : 'm';
            if( count( $config->product ) ){
                $product_config = array();
                foreach($config->product as $key => $side){
                    $product_config[] = (array)$side;
                }
                $datas = $product_config;
            };
            $used_font_path = $path. '/used_font.json';
            $used_font = json_decode( file_get_contents( $used_font_path ) );
            $path_font = array();
            foreach( $used_font as $font ){
                $font_name = $font->name;
                if( $font->type == 'google' ){
                    $path_font = nbd_download_google_font($font_name);
                }else{
                    $has_custom_font = true;
                    $_font = nbd_get_font_by_alias($font->alias);
                    if( $_font->file ){
                        foreach( $_font->file as $key => $font_file ){
                            $path_font[$key] = NBDESIGNER_FONT_DIR . '/' . $font_file;
                        }
                    }
                }
                $true_type = nbd_get_truetype_fonts();
                if (in_array($font_name, $true_type)) {
                    foreach($path_font as $pfont){
                        $fontname = TCPDF_FONTS::addTTFfont($pfont, 'TrueType', '');
                    }
                }else{
                    foreach($path_font as $pfont){
                        $fontname = TCPDF_FONTS::addTTFfont($pfont, '', '');
                    }
                }
            }
            $dpi        = (float)$option['dpi'];
            $dpi        = $dpi > 0 ? $dpi : 300;
            $pdfs       = array();
            $unit       = isset( $option['unit'] ) ? $option['unit'] : nbdesigner_get_option( 'nbdesigner_dimensions_unit', 'cm' );
            $unitRatio  = 10;
            $cm2Px      = 37.795275591;
            $mm2Px      = $cm2Px / 10;
            switch ($unit) {
                case 'mm':
                    $unitRatio = 1;
                    break;
                case 'in':
                    $unitRatio = 25.4;
                    break;
                case 'ft':
                    $unitRatio = 304.8;
                    break;
                case 'px':
                    $unitRatio = 25.4 / $dpi;
                    break;
                default:
                    $unitRatio = 10;
                    break;
            }
            $list_images = Nbdesigner_IO::get_list_images($path, 1);
            foreach ($list_images as $img){
                $name                   = basename($img);
                $arr                    = explode('.', $name);
                $_frame                 = explode('_', $arr[0]);
                $frame                  = $_frame[1];
                $list_design[$frame]    = $img;
            }
            foreach( $datas as $key => $data ){
                $contentImage = '';
                if(isset($list_design[$key])) $contentImage = $list_design[$key];
                $proWidth   = $data['product_width'];
                $proHeight  = $data['product_height'];
                $bgTop      = 0;
                $bgLeft     = 0;
                $includeBg  = isset($data['include_background']) ? $data['include_background'] : 1;
                if( $includeBg == 0 ){
                    $proWidth           = $data['real_width'];
                    $proHeight          = $data['real_height'];
                    $data['real_top']   = 0;
                    $data['real_left']  = 0;
                }

                $pdfs[$key]['background']           = $data['img_src'];
                $pdfs[$key]['bg_type']              = $data['bg_type'];
                $pdfs[$key]['bg_color_value']       = $data['bg_color_value'];
                $pdfs[$key]['cd-top']               = $data['real_top'] * $unitRatio;
                $pdfs[$key]['cd-left']              = $data['real_left'] * $unitRatio;
                $pdfs[$key]['cd-width']             = $data['real_width'] * $unitRatio;
                $pdfs[$key]['cd-height']            = $data['real_height'] * $unitRatio;
                $pdfs[$key]['customer-design']      = $contentImage;
                $pdfs[$key]['product-width']        = round($proWidth * $unitRatio, 2);
                $pdfs[$key]['product-height']       = round($proHeight * $unitRatio, 2);
                $pdfs[$key]['margin-top']           = $pdfs[$key]['margin-right'] = $pdfs[$key]['margin-bottom'] = $pdfs[$key]['margin-left'] = 0;
                $pdfs[$key]['bleed-top']            = $pdfs[$key]['bleed-bottom'] = $unitRatio * $data['bleed_top_bottom'];
                $pdfs[$key]['bleed-left']           = $pdfs[$key]['bleed-right'] = $unitRatio * $data['bleed_left_right'];
                $pdfs[$key]['include_background']   = $includeBg;
                $pdfs[$key]['origin_bg_pdf']        = isset( $data['origin_bg_pdf'] ) ? $data['origin_bg_pdf'] : '';
            }
            $mTop           = $pdfs[0]["margin-top"];
            $mBottom        = $pdfs[0]["margin-bottom"];
            $mLeft          = $pdfs[0]["margin-left"];
            $mRight         = $pdfs[0]["margin-right"];
            $bgWidth        = $pdfs[0]['product-width'];
            $bgHeight       = $pdfs[0]['product-height'];
            $pWidth         = $bgWidth + $mLeft + $mRight;
            $pHeight        = $bgHeight + $mTop + $mBottom;
            $pdf_format     = array( $pWidth, $pHeight );

            if($pWidth > $pHeight){
                $orientation = "L";
            }else {
                $orientation = "P";
            }

            $pdf = new Fpdi\TcpdfFpdi( $orientation, 'mm', $pdf_format, true, 'UTF-8', false );
            //$pdf = new TCPDI( $orientation, 'mm', $pdf_format, true, 'UTF-8', false );

            $pdf->SetMargins( $mLeft, $mTop, $mRight, true );
            $pdf->SetCreator( get_site_url() );
            $pdf->SetTitle( get_bloginfo( 'name' ) );
            $pdf->setPrintHeader( false );
            $pdf->setPrintFooter( false );
            $pdf->SetAutoPageBreak( TRUE, 0 );

            if( nbdesigner_get_option( 'nbdesigner_enable_pdf_password', 'no' ) == 'yes' && !$final ){
                $password = nbdesigner_get_option( 'nbdesigner_pdf_password', '' );
                if( $password != '' ){
                    $pdf->SetProtection( array( 'print', 'copy', 'modify' ), "", $password, 0, null );
                }
            }
            foreach( $pdfs as $key => $_pdf ){
                $customer_design    = $_pdf['customer-design'];
                $bTop               = (float)$_pdf['bleed-top'];
                $bLeft              = (float)$_pdf['bleed-left'];
                $bRight             = (float)$_pdf['bleed-right'];
                $bBottom            = (float)$_pdf['bleed-bottom'];
                $showBleed          = 'no'; 
                $cdTop              = (float)$_pdf["cd-top"];
                $cdLeft             = (float)$_pdf["cd-left"];
                $cdWidth            = (float)$_pdf["cd-width"];
                $cdHeight           = (float)$_pdf["cd-height"];
                $background         = $_pdf['background'];
                $bg_type            = $_pdf['bg_type'];
                $bg_color_value     = $_pdf['bg_color_value'];
                if( $bg_type == 'image' ){
                    $path_bg = ( absint($background) > 0 ) ? get_attached_file($background) : Nbdesigner_IO::convert_url_to_path( $background );
                }

                $mTop           = $_pdf["margin-top"];
                $mBottom        = $_pdf["margin-bottom"];
                $mLeft          = $_pdf["margin-left"];
                $mRight         = $_pdf["margin-right"];
                $bgWidth        = $_pdf['product-width'];
                $bgHeight       = $_pdf['product-height'];
                $pWidth         = $bgWidth + $mLeft + $mRight;
                $pHeight        = $bgHeight + $mTop + $mBottom;
                $pdf_format     = array( $pWidth, $pHeight );
                if( $pWidth > $pHeight ){
                    $orientation = "L";
                }else {
                    $orientation = "P";
                }

                $pdf->AddPage( $orientation, $pdf_format );
                if( $bg_type == 'image' && $path_bg && $_pdf['include_background'] == 1 ){
                    $img_ext    = array( 'jpg', 'jpeg', 'png' );
                    $svg_ext    = array( 'svg' );
                    $eps_ext    = array( 'eps', 'ai' );
                    $pdf_ext    = array( 'pdf' );

                    $check_img = Nbdesigner_IO::checkFileType(basename($path_bg), $img_ext);
                    $check_svg = Nbdesigner_IO::checkFileType(basename($path_bg), $svg_ext);
                    $check_eps = Nbdesigner_IO::checkFileType(basename($path_bg), $eps_ext);
                    $check_pdf  = Nbdesigner_IO::checkFileType( basename( $path_bg ), $pdf_ext );

                    $ext = pathinfo($path_bg);
                    if( $check_img && $_pdf['origin_bg_pdf'] == '' ){
                        $pdf->Image($path_bg,$mLeft, $mTop, $bgWidth, $bgHeight, '', '', '', false, '');
                    }
                    if( $check_svg && $_pdf['origin_bg_pdf'] == '' ){
                        $pdf->ImageSVG($path_bg, $mLeft,$mTop, $bgWidth, $bgHeight, '', '', '', 0, true);
                    }
                    if( $check_eps && $_pdf['origin_bg_pdf'] == '' ){
                       $pdf->ImageEps($path_bg, $mLeft,$mTop, $bgWidth, $bgHeight, '', true, '', '', 0, true);
                    }
                    if( $check_pdf || $_pdf['origin_bg_pdf'] != '' ){
                        $pdf_path = $path_bg;
                        if( $_pdf['origin_bg_pdf'] != '' ){
                            $pdf_path = NBDESIGNER_TEMP_DIR . $_pdf['origin_bg_pdf'];
                        }
                        
                        $number_pages = $pdf->setSourceFile( $pdf_path );
    
                        $page_index = 1;
                        if( $_pdf['origin_bg_pdf'] != '' && ( $number_pages - 1 ) >= $key ){
                            $page_index = $key + 1;
                        }
    
                        $pdf->tplId = $pdf->importPage( $page_index );
                        $pdf->useImportedPage( $pdf->tplId, 0, 0, $pWidth );
                    }
                }elseif( $bg_type == 'color' ) {
                    $need_bg_color = true;

                    if( isset( $config->areaDesignShapes ) && $config->areaDesignShapes[$key] ){
                        $need_bg_color  = false;
                    }
                    if( $datas[$key]['show_overlay'] == 1 && $datas[$key]['include_overlay'] == 1 ){
                        $need_bg_color  = true;
                    }

                    if( $need_bg_color ){
                        $pdf->Rect( $mLeft, $mTop,  $bgWidth, $bgHeight, 'F', '', hex_code_to_rgb( $bg_color_value ) );
                    }
                }
                if( $showBleed == 'yes' && nbdesigner_get_option( 'nbdesigner_bleed_stack', 1 ) == 1 ){
                    $pdf->Line(0, $mTop + $bTop, $mLeft + $bLeft, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                    $pdf->Line(0, $mTop + $bgHeight - $bBottom, $mLeft + $bLeft, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
                    $pdf->Line($bgWidth + $mLeft - $bRight, $mTop + $bTop, $bgWidth + $mLeft + $mRight, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                    $pdf->Line($bgWidth + $mLeft - $bRight, $mTop + $bgHeight - $bBottom, $bgWidth + $mLeft + $mRight, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
                    $pdf->Line($mLeft + $bLeft, 0, $mLeft + $bLeft, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                    $pdf->Line($mLeft + $bLeft, $mTop + $bgHeight - $bBottom, $mLeft + $bLeft, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
                    $pdf->Line($mLeft + $bgWidth - $bRight, 0, $mLeft + $bgWidth - $bRight, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                    $pdf->Line($mLeft + $bgWidth - $bRight, $mTop + $bgHeight - $bBottom, $mLeft + $bgWidth - $bRight, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
                }
                if( $customer_design != '' ){
                    $include_pdf = false;
                    if( isset( $config->originPDFs ) && isset( $config->originPDFs[$key] ) && isset( $config->pdfStacks ) ){
                        $resource_pdfs = (array)$config->originPDFs[$key];
                        if( count( $resource_pdfs ) && $config->pdfStacks[$key] != '' ){
                            $include_pdf    = true;
                            $stack          = explode( '_', $config->pdfStacks[$key] );
                            $pdf_index      = 0;
                            foreach( $stack as $pos ){
                                if( $pos == 'P' ){
                                    $resource_pdf   = (array)$resource_pdfs[$pdf_index];
                                    $pdf_path       = NBDESIGNER_TEMP_DIR . $resource_pdf['origin_pdf'];
                                    $pdf_top        = $resource_pdf['top'] * $unitRatio + $mTop + $cdTop;
                                    $pdf_left       = $resource_pdf['left'] * $unitRatio + $mLeft + $cdLeft;
                                    $pdf_width      = $resource_pdf['width'] * $unitRatio;
                                    $pdf_height     = $resource_pdf['height'] * $unitRatio;
    
                                    $pdf->setSourceFile( $pdf_path );
                                    $tplId = $pdf->importPage( 1 );
                                    $pdf->useImportedPage( $tplId, $pdf_left, $pdf_top, $pdf_width, $pdf_height );
                                    //$pdf->useTemplate( $tplId, $pdf_left, $pdf_top, $pdf_width, $pdf_height );
    
                                    $pdf_index++;
                                }else{
                                    $svg = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/part/svgpath/frame_' . $key . '_svg_part_' . $pos . '.svg';
                                    nbd_convert_svg_url( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/part/', 'frame_' . $key . '_svg_part_' . $pos . '.svg' );
                                    $pdf->ImageSVG( $svg, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth, $cdHeight, '', '', '', 0, true );
                                }
                            }
                        }
                    }
                    if( !$include_pdf ){
                        $svg = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/svgpath/frame_' . $key . '_svg.svg';
                        nbd_convert_svg_url( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/', 'frame_' . $key . '_svg.svg' );
                        $pdf->ImageSVG( $svg, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth, $cdHeight, '', '', '', 0, true );
                    }
                }
                if( $showBleed == 'yes' && nbdesigner_get_option( 'nbdesigner_bleed_stack', 1 ) == 2 ){
                    $pdf->Line(0, $mTop + $bTop, $mLeft + $bLeft, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                    $pdf->Line(0, $mTop + $bgHeight - $bBottom, $mLeft + $bLeft, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
                    $pdf->Line($bgWidth + $mLeft - $bRight, $mTop + $bTop, $bgWidth + $mLeft + $mRight, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                    $pdf->Line($bgWidth + $mLeft - $bRight, $mTop + $bgHeight - $bBottom, $bgWidth + $mLeft + $mRight, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
                    $pdf->Line($mLeft + $bLeft, 0, $mLeft + $bLeft, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                    $pdf->Line($mLeft + $bLeft, $mTop + $bgHeight - $bBottom, $mLeft + $bLeft, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
                    $pdf->Line($mLeft + $bgWidth - $bRight, 0, $mLeft + $bgWidth - $bRight, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                    $pdf->Line($mLeft + $bgWidth - $bRight, $mTop + $bgHeight - $bBottom, $mLeft + $bgWidth - $bRight, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
                }
                if( $datas[$key]['show_overlay'] == 1 && $datas[$key]['include_overlay'] == 1 && $layout != 'c' ){
                    $overlay = Nbdesigner_IO::convert_url_to_path( $datas[$key]['img_overlay'] );
                    $check_over_img = Nbdesigner_IO::checkFileType(basename($overlay), array('jpg','jpeg','png'));
                    if( $check_over_img ){
                        //$pdf->Image($overlay, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth,$cdHeight, '', '', '', false, '');
                        $pdf->Image($overlay, $mLeft, $mTop,  $bgWidth, $bgHeight, '', '', '', false, '');
                    }
                }

                if( ($enable_watermark == 'yes' || $enable_watermark == 'before')  && !$final ){
                    $watermark_type = nbdesigner_get_option( 'nbdesigner_pdf_watermark_type', 1 );
                    if($watermark_type == 1){
                        $watermark_image = nbdesigner_get_option( 'nbdesigner_pdf_watermark_image', ' ');
                        $watermark_file = get_attached_file( $watermark_image );
                        if( $watermark_file ){
                            $myPageWidth = $pdf->getPageWidth();
                            $myPageHeight = $pdf->getPageHeight();
                            list($watermark_width, $watermark_height) = getimagesize($watermark_file);
                            $watermark_width = $watermark_width/72*25.4;
                            $watermark_height = $watermark_height/72*25.4;
                            $myX = ( $myPageWidth - $watermark_width )/2 > 0 ? ( $myPageWidth - $watermark_width )/2 : 0;
                            $myY = ( $myPageHeight - $watermark_height )/2 > 0 ? ( $myPageHeight - $watermark_height )/2 : 0;
                            $pdf->SetAlpha(0.2);
                            $pdf->StartTransform();
                            $pdf->Rotate(45, $myPageWidth / 2, $myPageHeight / 2);
                            $pdf->Image($watermark_file, $myX, $myY, '', '', '', '', '', false);
                            $pdf->StopTransform();
                            $pdf->SetAlpha(1);
                        }
                    }else{
                        $watermark_text = nbdesigner_get_option('nbdesigner_pdf_watermark_text');
                        $vfont = "helvetica";
                        $vfontsize = 20;
                        $vfontbold = "B";
                        $widthtext = $pdf->GetStringWidth(trim($watermark_text), $vfont, $vfontbold, $vfontsize, false );
                        $widthtextcenter = round(($widthtext * sin(deg2rad(45))) / 2 ,0);
                        $myPageWidth = $pdf->getPageWidth();
                        $myPageHeight = $pdf->getPageHeight();
                        $myX = ( $myPageWidth - $widthtext )/2 > 0 ? ( $myPageWidth - $widthtext )/2 : 0;
                        $myY = $myPageHeight / 2;
                        $pdf->SetAlpha(0.2);
                        $pdf->StartTransform();
                        $pdf->Rotate(45, $myPageWidth / 2, $myPageHeight / 2);
                        $pdf->SetFont($vfont, $vfontbold, $vfontsize);
                        $pdf->Text($myX, $myY ,trim($watermark_text));
                        $pdf->StopTransform();
                        $pdf->SetAlpha(1);
                    }
                }

                if( isset( $config->contour ) ){
                    $pdf->ImageSVG( '@' . $config->contour, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth, $cdHeight, '', '', '', 0, true );
                }
            }
            if(!file_exists($folder)){
                wp_mkdir_p($folder);
            }
            $pdf->Output($output_file, 'F');
        }

        $result[] = array(
            'link'  => Nbdesigner_IO::wp_convert_path_to_url( $output_file ),
            'title' => $nbd_item_key,
            'flag'  => 1
        );

        if( $return ){
            return $result;
        } else {
            echo json_encode( $result );
            wp_die();
        }
    }
    public function nbd_frontend_download_jpeg(){
        $result[] = array(
            0 => array(
                'link' => '',
                'flag' => 0
            )
        );
        $nbd_item_key = $_POST['nbd_item_key'];
        if( $nbd_item_key == '' ){
            echo json_encode( $result );
            wp_die();
        }
        $this->nbd_frontend_download_pdf( false, $nbd_item_key, true );
        $pdf_path   = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key . '/customer-pdfs';
        $config     = nbd_get_data_from_json( NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key . '/config.json' );
        $pdf        = $pdf_path .'/'. $nbd_item_key .'.pdf';
        NBD_Image::pdf2image( $pdf, $config->dpi, 'jpg' );
        $files      = Nbdesigner_IO::get_list_files_by_type( $pdf_path, 1, 'jpg|jpeg' );
        $path_file  = '/download/customer-design-' . $nbd_item_key . '-' . time() . '.zip';
        $pathZip    = NBDESIGNER_DATA_DIR . $path_file;
        nbd_zip_files_and_download( $files, $pathZip, 'customer-design.zip', array(), false );
        $result[0]['link'] = NBDESIGNER_DATA_URL . $path_file;
        $result[0]['flag'] = 1;
        echo json_encode( $result );
        wp_die();
    }
    /**
     * @deprecated 1.9.0
     * @since 1.8.0
     */
    public function nbd_download_final_pdf(){
        if (!wp_verify_nonce( $_POST['nonce'], 'save-design') && NBDESIGNER_ENABLE_NONCE ) {
            die('Security error');
        }
        $nbd_item_key = $_POST['nbd_item_key'];
        $folder = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key .'/pdfs';
        $result = array(
            'flag'  =>  1
        );
        if( file_exists( $folder ) ) {
            $list_file  = Nbdesigner_IO::get_list_files( $folder );
            $list       = preg_grep( '/\.(pdf)(?:[\?\#].*)?$/i', $list_file );
            foreach ( $list as $pdf ){
                $result['pdf'][] = Nbdesigner_IO::wp_convert_path_to_url( $pdf );
            }
        }else{
            $pdfs = $this->nbd_frontend_download_pdf( true, $nbd_item_key, true );
            foreach ($pdfs as $pdf){
                $result['pdf'][] = $pdf['link'];
            }
        }
        echo json_encode( $result );
        wp_die();
    }
    public function convert_svg_embed( $path ){
        $svgs       = Nbdesigner_IO::get_list_files_by_type( $path, 1, 'svg' );
        $svg_path   = $path . '/svg';
        if( !file_exists( $svg_path ) ) wp_mkdir_p( $svg_path );
        foreach ( $svgs as $svg ){
            $svg_name = pathinfo( $svg, PATHINFO_BASENAME );
            $new_svg_path = $svg_path . '/' . $svg_name;
            $xdoc = new DomDocument;
            $xdoc->Load($svg);
            /* Embed images */
            $images = $xdoc->getElementsByTagName('image');
            for ($i = 0; $i < $images->length; $i++) {
                $tagName = $xdoc->getElementsByTagName('image')->item($i);
                $attribNode = $tagName->getAttributeNode('xlink:href');
                $img_src = $attribNode->value;
                if( strpos( $img_src, "data:image" ) !== FALSE )
                continue;
                if( strpos( $img_src, "data:img" ) !== FALSE )
                continue;
                $type = pathinfo($img_src, PATHINFO_EXTENSION);
                $type = ($type =='svg' ) ? 'svg+xml' : $type;
                $path_image = Nbdesigner_IO::convert_url_to_path($img_src);
                $data = nbd_file_get_contents($path_image);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                $tagName->setAttribute('xlink:href', $base64);
            }
            /* Embed fonts */
            $text_elements = $xdoc->getElementsByTagName('text');
            for ($i = 0; $i < $text_elements->length; $i++) {
                $tagName = $xdoc->getElementsByTagName('text')->item($i);
                $attribNode = $tagName->getAttributeNode('font-family');
                $font_family = $attribNode->value;
                $font = nbd_get_font_by_alias($font_family);
                if( $font ){
                    $tagName->setAttribute('font-family', $font->name);
                }
            }
            $new_svg = $xdoc->saveXML();
            file_put_contents($new_svg_path, $new_svg);
        }
    }
    public function convert_svg_url($path, $file){
        $svg_path = $path . '/svgpath';
        if( !file_exists($svg_path) ) wp_mkdir_p($svg_path);
        $new_svg_path = $svg_path.'/'.$file;
        $xdoc = new DomDocument;
        $xdoc->Load($path.$file);
        $images = $xdoc->getElementsByTagName('image');
        for ($i = 0; $i < $images->length; $i++) {
            $tagName = $xdoc->getElementsByTagName('image')->item($i);
            $attribNode = $tagName->getAttributeNode('xlink:href');
            $img_src = $attribNode->value;
            if(strpos($img_src, "data:image")!==FALSE)
            continue;
            $type = pathinfo($img_src, PATHINFO_EXTENSION);
            $type = ($type =='svg' ) ? 'svg+xml' : $type;
            $path_image = Nbdesigner_IO::convert_url_to_path($img_src);
            $tagName->setAttribute('xlink:href', $path_image);
        }
        $new_svg = $xdoc->saveXML();
        file_put_contents($new_svg_path, $new_svg); 
    }
    public function nbd_convert_files(){
        if (!wp_verify_nonce($_POST['_wpnonce'], 'nbd_jpg_nonce') && !wp_verify_nonce($_POST['_wpnonce'], 'nbd_cmyk_nonce')) {
            die('Security error');
        }
        $type = wc_clean( $_POST['type'] );
        $nbd_item = wc_clean( $_POST['nbd_item'] );
        $result = array();
        $path = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item;
        $path_jpg = $path . '/jpg/';
        if( $type == 'jpg'){
            $dpi = $_POST['jpg_dpi'];
            $path_png = $path . '/png/';
            if( !file_exists($path_png) ){
                Nbdesigner_IO::mkdir($path_png);
            }else{
                Nbdesigner_IO::delete_folder($path_png);
                Nbdesigner_IO::mkdir($path_png);
            }
            if( !file_exists($path_jpg) ){
                Nbdesigner_IO::mkdir($path_jpg);
            }else{
                Nbdesigner_IO::delete_folder($path_jpg);
                Nbdesigner_IO::mkdir($path_jpg);
            }
            $list =  Nbdesigner_IO::get_list_images($path, 1);
            foreach ($list as $image){
                $image_name = pathinfo($image, PATHINFO_FILENAME);
                $png_with_white_bg = $path_png . $image_name .'.png';
                $jpg = $path_jpg . $image_name .'.jpg';
                NBD_Image::imagick_add_white_bg($image, $png_with_white_bg);
                NBD_Image::imagick_convert_png2jpg_without_bg($png_with_white_bg, $jpg);
                NBD_Image::imagick_resample($jpg, $jpg, $dpi);
            }  
        }else if( $type == 'cmyk'){
            $path_cmyk = $path . '/cmyk/';
            if( !file_exists($path_cmyk) ){
                Nbdesigner_IO::mkdir($path_cmyk);
            }else{
                Nbdesigner_IO::delete_folder($path_cmyk);
                Nbdesigner_IO::mkdir($path_cmyk);
            }
            $icc_index = $_POST['icc'];
            $list_icc = nbd_get_icc_cmyk_list_file();
            $icc = $list_icc[$icc_index];
            $icc_file = NBDESIGNER_PLUGIN_DIR . 'data/icc/CMYK/' . $icc;
            $list =  Nbdesigner_IO::get_list_images($path_jpg, 1);
            foreach ($list as $image){
                $image_name = pathinfo($image, PATHINFO_FILENAME);
                $cmyk = $path_cmyk . $image_name .'.jpg';
                NBD_Image::imagick_convert_rgb_to_cymk($image, $cmyk);
                if($icc_index){
                    NBD_Image::imagick_change_icc_profile($cmyk, $cmyk, $icc_file);
                }
            }
        }
        $result['flag'] = 1;
        echo json_encode( $result );
        wp_die();
    }
    public function add_nbdesinger_order_actions_button($actions, $the_order){
        $id = is_woo_v3() ?  $the_order->get_id() : $the_order->id ;
        $order = wc_get_order($id);
        $has_design = $order->get_meta( '_nbd', true );
        if($has_design && is_admin()){
            $actions['view_nbd'] = array(
                'url'       => admin_url( 'post.php?post='.$id.'&action=edit' ).'#nbdesigner_order',
                'name'      => esc_html__( 'Has Design', 'web-to-print-online-designer' ),
                'action'    => "has-nbd"
            );
        }
        return $actions;
    }
    public function nbdesigner_update_variation_v180(){
        NBD_Update_Data::update_vatiation_config_v180();
    }
    public function nbd_clear_transients(){
        Nbdesigner_Helper::clear_transients();
    }
    public function nbd_create_pages(){
        if ( !wp_verify_nonce( $_POST['_nbdesigner_update_product'], 'nbd-create-pages' ) || !current_user_can( 'administrator' ) ) {
            die('Security error');
        }
        NBD_Install::create_pages();
        NBD_Install::create_tables();
        NBD_Install::insert_default_files();
        wp_send_json(
            array(
                'flag'  =>  1
            )
        ); 
    }
    /** bulk action **/
    public function add_post_class( $classes, $class, $post_id ){
        if ( is_admin() ) {
            return $classes;
        }
        $product_id     = get_the_ID();
        $product_id     = get_wpml_original_id( $product_id );
        $is_nbdesign    = get_post_meta( $product_id, '_nbdesigner_enable', true );
        if( $is_nbdesign ){
            $layout = nbdesigner_get_option('nbdesigner_design_layout');
            if($layout == 'c'){
                $option     = unserialize( get_post_meta( $product_id, '_nbdesigner_option', true ) );
                $product    = wc_get_product( $product_id );
                $type       = $product->get_type();
                if( $type == 'variable' && isset( $option['bulk_variation'] ) && $option['bulk_variation'] == 1 ){
                    $classes[] = 'nbd_bulk_variation';
                }
            }
        }
        return $classes;
    }
    public function bulk_variation_field(){
        $product_id     = get_the_ID();
        $product_id     = get_wpml_original_id( $product_id );
        $is_nbdesign    = get_post_meta( $product_id, '_nbdesigner_enable', true );
        if( !$is_nbdesign ){
            return;
        } else {
            $layout = nbdesigner_get_option( 'nbdesigner_design_layout' );
            if( $layout == 'c' ){
                $option     = unserialize( get_post_meta( $product_id, '_nbdesigner_option', true ) );
                $product    = wc_get_product( $product_id );
                $type       = $product->get_type();
                if( $type == 'variable' && isset( $option['bulk_variation'] ) && $option['bulk_variation'] == 1 ){
                    $variations = get_nbd_variations( $product_id, true );
                    if( count($variations) ){
                        $atts = array(
                            'variations'    => $variations
                        );
                        ob_start();
                        nbdesigner_get_template( 'single-product/variation-bulk.php', $atts );
                        $bulk_variation_form = ob_get_clean();
                        echo $bulk_variation_form;
                    }
                }
            }
        }       
    }
    public function process_add_bulk_variation(){
        if ( empty( $_REQUEST['nbd-variation-value'] ) && $_REQUEST['nbd-variation-value'] == '' ) {
            return;
        }
        $product_id             = absint( $_POST['product_id'] );
        $added_count            = 0;
        $failed_count           = 0;
        $success_message        = '';
        $error_message          = '';
        $_variations            = explode('|', wc_clean( $_REQUEST['nbd-variation-value'] ) );
        $adding_to_cart         = wc_get_product( $product_id );
        $added_variations       = array();
        $nbd_item_session_key   = $product_id . '_0';
        $nbd_session            = WC()->session->get( 'nbd_item_key_'.$nbd_item_session_key );
        $nbu_session            = WC()->session->get( 'nbu_item_key_'.$nbd_item_session_key );
        foreach ( $_variations as $_variation ) {
            $var = explode('_', $_variation);
            $variation_id   = absint( $var[0] );
            $quantity       = absint( $var[1] );
            $variations     = array();
            if( $quantity > 0 && $variation_id > 0) {
                $variation_data = wc_get_product_variation_attributes( $variation_id );
                $attributes     = $adding_to_cart->get_attributes();
                foreach ( $attributes as $attribute ) {
                    if ( ! $attribute['is_variation'] ) {
                        continue;
                    }
                    $taxonomy                   = 'attribute_' . sanitize_title( $attribute['name'] );
                    $valid_value                = isset( $variation_data[ $taxonomy ] ) ? $variation_data[ $taxonomy ] : '';
                    $variations[ $taxonomy ]    = $valid_value;
                }
                $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id,$quantity, $variation_id, $variations );
                if ( $passed_validation ) {
                    $nbd_variation_session_key = $product_id . '_' . $variation_id;
                    if(isset($nbd_session)){
                        WC()->session->set( 'nbd_item_key_' . $nbd_variation_session_key, $nbd_session );
                    }
                    if(isset($nbu_session)){
                        WC()->session->set( 'nbu_item_key_' . $nbd_variation_session_key, $nbu_session );
                    }
                    $added = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations );
                }
                if ( $added ) {
                    $added_count ++;
                    $added_variations[] = $variation_id;
                } else {
                    $failed_count ++;
                }
            }else{
                $failed_count++;
                continue;
            }
        }
        if( isset( $nbd_session ) ){
            WC()->session->__unset( 'nbd_item_key_'.$nbd_item_session_key );
        }
        if( isset( $nbu_session ) ){
            WC()->session->__unset( 'nbu_item_key_'.$nbd_item_session_key );
        }
        if ( $added_count ) {
            nbd_bulk_variations_add_to_cart_message( $added_count );
        }
        if ( $failed_count ) {
            wc_add_notice( sprintf( esc_html__( 'Unable to add %s to the cart.  Please check your quantities and make sure the item is available and in stock', 'web-to-print-online-designer' ), $failed_count ), 'error' );
        }
        if ( ! $added_count && ! $failed_count ) {
            wc_add_notice( esc_html__( 'No product quantities entered.', 'web-to-print-online-designer' ), 'error' );
        }
        if ( $failed_count === 0 && wc_notice_count( 'error' ) === 0 ) {
            if ( $url = apply_filters( 'woocommerce_add_to_cart_redirect', false ) ) {
                wp_safe_redirect( esc_url( $url ) );
                exit;
            } elseif ( get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes' ) {
                wp_safe_redirect( wc_get_cart_url() );
                exit;
            }
        }
    }
    public function woocommerce_order_item_meta_end( $item_id, $item, $order ){
        $nbd_item_key = wc_get_order_item_meta( $item_id, '_nbd' );
        $nbu_item_key = wc_get_order_item_meta( $item_id, '_nbu' );
        if( $nbd_item_key || $nbu_item_key ){
            $show_design_in_order = nbdesigner_get_option( 'nbdesigner_show_in_order', 'yes' );
            if( ( isset( $item["item_meta"]["_nbd"] ) || isset( $item["item_meta"]["_nbu"] ) ) && $show_design_in_order == 'yes' ){
                $html = '';
                if( isset( $item["item_meta"]["_nbd"] ) ){
                    $nbd_item_key       = $item["item_meta"]["_nbd"]; 
                    $images             = Nbdesigner_IO::get_list_images( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/preview', 1 );
                    $approve_status     = unserialize( get_post_meta( $item['order_id'], '_nbdesigner_design_file', true ) );
                    $index              = 'nbds_' . $item->get_id();
                    $notice             = '';
                    $id                 = 'nbd' . $item->get_id();
                    $redirect_url       = wc_get_endpoint_url( 'view-order', $item['order_id'], wc_get_page_permalink( 'myaccount' ) ) . '#' . $id;
                    $product_id         = $item['product_id'];
                    $product_id         = get_wpml_original_id( $product_id ); 
                    $layout             = nbd_get_product_layout( $product_id );
                    $redesign_link      = add_query_arg(
                        array(
                            'task'          => 'edit',
                            'product_id'    => $product_id,
                            'view'          => $layout,
                            'oid'           => $item['order_id'],
                            'item_id'       => $id,
                            'design_type'   => 'edit_order',
                            'rd'            => 'order',
                            'nbd_item_key'  => $nbd_item_key ),
                        getUrlPageNBD( 'create' ) );
                    if( $layout == 'v' ){
                        $redesign_link = add_query_arg(
                            array(
                                'nbdv-task'     => 'edit',
                                'task'          => 'edit',
                                'product_id'    => $product_id,
                                'oid'           => $item['order_id'],
                                'item_id'       => $id,
                                'design_type'   => 'edit_order',
                                'rd'            => 'order',
                                'nbd_item_key'  =>  $nbd_item_key),
                            get_permalink( $product_id ) );
                    }
                    if( $item['variation_id'] > 0 ){
                        $redesign_link .= '&variation_id=' . $item['variation_id'];
                    }
                    $html .= '<div id="'.$id.'" class="nbd-order-upload-file">';
                    $html .= '<p>' . esc_html__('Custom design', 'web-to-print-online-designer') . '</p>';
                    asort( $images );
                    foreach ( $images as $img ){
                        $src    = Nbdesigner_IO::convert_path_to_url( $img ) . '?&t=' . round( microtime( true ) * 1000 );
                        $html  .= '<img class="nbd_order_item_design_preview" src="' . $src . '" />';
                    }
                    if( isset( $approve_status[$index] ) && $approve_status[$index] == "decline" ){
                        $notice = "<small class='nbd_order_item_design_reject' >" . esc_html__( '(Rejected! Click ', 'web-to-print-online-designer' ) . "<a href='" . $redesign_link ."' target='_blank'>" . esc_html__('here ', 'web-to-print-online-designer'). "</a>" . esc_html__(' to design again', 'web-to-print-online-designer')."!)</small>";
                    }
                    if( isset( $approve_status[$index] ) && $approve_status[$index] == "accept" ){
                        $notice = '<small> ' . esc_html__( '(Approved!)', 'web-to-print-online-designer' ) . ' </small>';
                    }
                    $html .= '<p>'.$notice.'</p>';
                    if( nbdesigner_get_option( 'allow_customer_redesign_after_order', 'yes' ) == 'yes' && ( !isset( $approve_status[$index] ) || ( isset($approve_status[$index] ) && $approve_status[$index] != "accept" ) ) ){
                        $show_edit_link = apply_filters( 'nbd_show_edit_design_link_in_pay_for_order', true, $item, $item_id, $order );
                        if( $show_edit_link ) $html .= '<p><a class="button" href="' . $redesign_link .'">' . esc_html__('Edit design', 'web-to-print-online-designer') . '</a></p>';
                    }
                    $html .= '<div>';
                }
                if( isset( $item["item_meta"]["_nbu"] ) ){
                    $nbu_item_key       = $item["item_meta"]["_nbu"]; 
                    $files              = Nbdesigner_IO::get_list_files( NBDESIGNER_UPLOAD_DIR .'/'. $nbu_item_key );
                    $notice             = '';
                    $id                 = 'nbu' . $item->get_id();
                    $approve_status     = unserialize(get_post_meta($item['order_id'], '_nbdesigner_upload_file', true));
                    $index              = 'nbds_'.$item->get_id();
                    $redirect_url       = wc_get_endpoint_url( 'view-order', $item['order_id'], wc_get_page_permalink( 'myaccount' ) ) . '#' . $id;
                    $product_id         = $item['product_id'];
                    $product_id         = get_wpml_original_id( $product_id );
                    $reup_link          = add_query_arg(
                        array(
                            'task'          =>  'reup',
                            'product_id'    =>  $product_id,
                            'design_type'   =>  'edit_order',
                            'oid'           =>  $item['order_id'],
                            'item_id'       =>  $id,
                            'rd'            => 'order',
                            'nbu_item_key'  =>  $nbu_item_key ),
                        getUrlPageNBD( 'create' ) );
                    if( $item['variation_id'] > 0 ){
                        $reup_link .= '&variation_id=' . $item['variation_id'];
                    }
                    $reup_link          = apply_filters( 'nbu_order_item_reup_link', $reup_link, $item, $item_id, $product_id );
                    $html              .= '<div id="' . $id . '" class="nbd-order-upload-file">';
                    $html              .= '<p>' . esc_html__( 'Upload file', 'web-to-print-online-designer' ).'</p>';
                    $create_preview     = nbdesigner_get_option( 'nbdesigner_create_preview_image_file_upload', 'no' );
                    $upload_html        = '';
                    foreach ( $files as $file ){
                        $ext = pathinfo( $file, PATHINFO_EXTENSION );
                        $src = Nbdesigner_IO::get_thumb_file( pathinfo( $file, PATHINFO_EXTENSION ), '');
                        if(  $create_preview == 'yes' && ( $ext == 'png' || $ext == 'jpg' || $ext == 'pdf' ) ){
                            $dir = pathinfo( $file, PATHINFO_DIRNAME );
                            $filename = pathinfo( $file, PATHINFO_BASENAME );
                            if( file_exists($dir . '_preview/' . $filename ) ){
                                $src = Nbdesigner_IO::wp_convert_path_to_url( $dir.'_preview/'.$filename );
                            }else if( $ext == 'pdf' && file_exists($dir.'_preview/'.$filename.'.jpg' ) ){
                                $src = Nbdesigner_IO::wp_convert_path_to_url( $dir.'_preview/'.$filename.'.jpg' );
                            }else{
                                $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
                            }
                        }else {
                            $src = Nbdesigner_IO::get_thumb_file( $ext, '' );
                        }
                        $upload_html .= '<div class="nbd-order-item-upload"><img class="nbd_order_item_upload_preview" src="' . $src . '"/><p class="nbd-order-item-upload-name">'. basename($file).'</p></div>';
                    } 
                    $upload_html = apply_filters( 'nbu_order_item_html', $upload_html, $item, $item_id, $nbu_item_key );
                    $html       .= $upload_html;
                    if( isset( $approve_status[$index] ) && $approve_status[$index] == "decline" ){
                        $notice = "<small class='nbd_order_item_design_reject'>" . esc_html__( '(Rejected! Click ', 'web-to-print-online-designer' ) . "<a href='" . $reup_link . "' target='_blank'>" . esc_html__( 'here ', 'web-to-print-online-designer' ) . "</a>" . esc_html__( ' to design again', 'web-to-print-online-designer' )."!)</small>";
                    }
                    if( isset( $approve_status[$index] ) && $approve_status[$index] == "accept" ){
                        $notice = '<small> ' . esc_html__( '(Approved!)', 'web-to-print-online-designer' ) . ' </small>';
                    }
                    $html .= '<p>' . $notice . '</p>';
                    if( nbdesigner_get_option( 'allow_customer_redesign_after_order' ) == 'yes' ){
                        $show_edit_link = apply_filters( 'nbd_show_edit_design_link_in_pay_for_order', true, $item, $item_id, $order );
                        if( $show_edit_link ) $html .= '<br /><a class="button" href="' . $reup_link . '">' . esc_html__('Reupload design', 'web-to-print-online-designer') . '</a>';
                    }
                    $html .= '<div>';
                }
                if( isset( $item["item_meta"]["_nbd_extra_price"] ) ){
                    $html .= '<p>' . esc_html__( 'Extra price for design','web-to-print-online-designer' ) . ' + '. $item["item_meta"]["_nbd_extra_price"] . '</p>';
                }
            }
            echo $html;
            /* Allow the customer download design files */
            if( nbdesigner_get_option( 'allow_customer_download_after_complete_order', 'no' ) == 'yes' || nbdesigner_get_option( 'allow_customer_download_after_complete_order', 'no' ) == 'complete' ){
                $order_status = $order->get_status();
                if( $order_status != 'completed' && nbdesigner_get_option( 'allow_customer_download_after_complete_order', 'no' ) == 'complete' ){
                    echo '';
                }else{
                    ob_start();
                    nbdesigner_get_template( 'order/download-section.php', array(
                        'item_id'       => $item_id,
                        'nbd_item_key'  => $nbd_item_key,
                        'nbu_item_key'  => $nbu_item_key,
                        'order_id'      => $order->get_id()
                    ) );
                    echo ob_get_clean();
                }
            } 
        }
    }
    public function woocommerce_order_details_after_order_table( $order ){
        ob_start();
        nbdesigner_get_template( 'order/download-pdf-option.php', array() );
        echo ob_get_clean();
    }
    public function download_order_item_design_files(){
        $order_id       = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
        $order_item_id  = isset( $_GET['order_item_id'] ) ? absint( $_GET['order_item_id'] ) : 0;
        $nbd_item_key   = isset( $_GET['nbd_item_key'] ) ? wc_clean( $_GET['nbd_item_key'] ) : '';
        $nbu_item_key   = isset( $_GET['nbu_item_key'] ) ? wc_clean( $_GET['nbu_item_key'] ) : '';
        $type           = isset( $_GET['type'] ) ? wc_clean( $_GET['type'] ) : 'png';
        if ( !isset( $_GET['type'] ) ) {
            $_GET['type'] = $type;
        }
        $force          = ( isset($_GET['multi_file'] ) && wc_clean( $_GET['multi_file'] ) == 'no' )  ? true : false;
        $showBleed      = ( isset($_GET['bleed'] ) && wc_clean( $_GET['bleed'] ) == 'yes' ) ? 'yes' : 'no';
        if( nbd_check_order_permission( $order_id ) ){
            nbd_download_product_designs( $order_id, $order_item_id, $nbd_item_key, $nbu_item_key, $type, $force, $showBleed );
        }else{
            wp_redirect( wc_get_endpoint_url( 'view-order', $order_id, wc_get_page_permalink( 'myaccount' ) ) );
            exit();
        }
    }
    public function admin_redirects(){
        if ( get_transient( '_nbd_activation_redirect' ) ) {
            delete_transient('_nbd_activation_redirect');
            if ( ( !empty( $_GET['page']) && in_array($_GET['page'], array('nbd-setup')) ) || is_network_admin() ) {
                return;
            }
            wp_safe_redirect( admin_url( 'index.php?page=nbd-setup' ) );
            exit;
        }
    }
    public function nbd_get_product_config(){
        echo 'config';
        wp_die();
    }
    public function nbd_crop_image(){
        if ( !wp_verify_nonce($_POST['nonce'], 'save-design') && NBDESIGNER_ENABLE_NONCE ) {
            die('Security error');
        }
        $url        = wc_clean( $_POST['url'] );
        $startX     = wc_clean( $_POST['startX'] );
        $startY     = wc_clean( $_POST['startY'] );
        $width      = wc_clean( $_POST['width'] );
        $height     = wc_clean( $_POST['height'] );
        $crop_dir   = ( isset( $_POST['crop_dir'] ) && $_POST['crop_dir'] != '' ) ? wc_clean( $_POST['crop_dir'] ) : '';
        $path       = Nbdesigner_IO::convert_url_to_path( $url );
        $path_parts = pathinfo( $path );
        if( $crop_dir == '' ){
            $new_path = $path_parts['dirname'] . '/' . $path_parts['filename'].'_c'.time() . '.' . $path_parts['extension'];
        } else {
            $new_path = $path_parts['dirname'] . $crop_dir . '/' . $path_parts['basename'];
            if( !file_exists( $path_parts['dirname'] . $crop_dir ) ){
                wp_mkdir_p( $path_parts['dirname'] . $crop_dir );
            }
        }
        NBD_Image::crop_image( $path, $new_path, $startX, $startY, $width, $height, strtolower( $path_parts['extension'] ) );
        if( file_exists( $new_path ) ){
            wp_send_json(
                array(
                    'flag'  => 1,
                    'url'   => Nbdesigner_IO::convert_path_to_url( $new_path )
                )
            );
        }else{
            wp_send_json(
                array(
                    'flag'  => 0
                )
            );
        }
    }
    public function delete_expired_upload_files( $days ){
        $retain_time    = $days * 60 * 60 * 24;
        $folders        = array_diff( scandir( NBDESIGNER_UPLOAD_DIR ), array( '.', '..' ) );
        foreach ( $folders as $folder ) {
            $path = NBDESIGNER_UPLOAD_DIR . '/' . $folder;
            if ( is_dir( $path ) === true ) {
                if( ( time() - filemtime( $path ) ) > $retain_time ){
                    Nbdesigner_IO::delete_folder( $path );
                }
            }
        }
    }
    public function auto_export_pdf( $order_id ){
        global $nbd_processors;

        $nbd_processors['export_pdf']->push_to_queue( $order_id );
        $nbd_processors['export_pdf']->save()->dispatch();
    }
    public function synchronize_output( $nbd_item_key, $order_id, $order_item_id ){
        $place  = nbdesigner_get_option( 'nbdesigner_synchronize_to', 'no' );
        if( $place == 'no' ) return;

        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-synchronize.php' );
        $synchronize    = new NBD_Synchronize_Output( $place );
        $synchronize->get_upload_files( $nbd_item_key, $order_id, $order_item_id );
        $synchronize->upload();
    }
    public function check_flysystem_connected(){
        $place  = wc_clean( $_POST['place'] );

        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-synchronize.php' );
        $synchronize    = new NBD_Synchronize_Output( $place );
        $connection     = $synchronize->is_connected();

        $result = array(
            'is_connected'  => $connection ? 1 : 0
        );
        wp_send_json( $result );
    }
    public function nbd_upload_pdf_as_bg_image(){
        $data = array(
            'mes'       => esc_html__( 'You do not have permission to do this action!', 'web-to-print-online-designer' ),
            'flag'      => 0,
            'images'    => array()
        );
        if ( !wp_verify_nonce( $_POST['nonce'], 'nbdesigner_add_cat' ) || !current_user_can( 'delete_nbd_art' ) ) {
            echo json_encode( $data );
            wp_die();
        }

        $file       = $_FILES['file'];
        $path       = $file['name'];
        $ext        = strtolower( pathinfo( $path, PATHINFO_EXTENSION ) );
        $new_name   = strtotime( "now" ) . substr( md5( rand( 1111, 9999 ) ), 0, 8 ) . '.' . $ext;
        if( $ext == 'pdf' ){
            $path = Nbdesigner_IO::create_file_path( NBDESIGNER_TEMP_DIR, $new_name );
            if( move_uploaded_file( $file["tmp_name"], $path['full_path'] ) ){
                $document       = new Imagick( $path['full_path'] );
                $number_pages   = $document->getNumberImages();
                $dpi            = 300;
                $type           = 'png';
                $res            = true;
                for( $i = 0; $i < $number_pages; $i++ ){
                    try{
                        $im = new Imagick();
                        $im->setResolution( $dpi, $dpi );
                        $im->readImage( $path['full_path'] . '['. $i .']' );
                        $im->setImageFormat( $type );
                        $im->setImageUnits( imagick::RESOLUTION_PIXELSPERINCH );
                        $im->setResolution( $dpi, $dpi );

                        $img_path = str_replace( ".pdf", "_" . $i . "." . $type, $path['full_path'] );
                        $im->writeImage( $img_path );
                        $im->clear();
                        $im->destroy();

                        $attachment_id  = nbd_attach_image_to_media( $img_path );

                        if( $attachment_id ){
                            $data['images'][] = array(
                                'id'            => $attachment_id,
                                'src'           => wp_get_attachment_url( $attachment_id ),
                                'origin_pdf'    => $path['date_path']
                            );
                        }else{
                            $data['mes'] = esc_html__( 'Error occurred when update media attachment!', 'web-to-print-online-designer' );
                        }
                    } catch (Exception $e) {
                        $data['mes']    = esc_html__( 'Error occurred when convert pdf!', 'web-to-print-online-designer' );
                        $res            = false;
                        break;
                    }
                }

                if( $res ){
                    $data['flag']   = 1;
                    $data['mes']    = esc_html__( 'Success!', 'web-to-print-online-designer' );
                }
            }else{
                $data['mes'] = esc_html__( 'Error occurred with file upload!', 'web-to-print-online-designer' );
            }
        } else {
            $data['mes'] = esc_html__( 'Invalid file format!', 'web-to-print-online-designer' );
        }

        echo json_encode( $data );
        wp_die();
    }
    public function nbd_import_images(){
        if ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) {
            die('Security error');
        }
        $result = array(
            'flag'      => 1,
            'images'    => array()
        );

        foreach( $_POST['images'] as $key => $image ){
            if ( filter_var( $image, FILTER_VALIDATE_URL ) ) { 
                $ext        = pathinfo( $image, PATHINFO_EXTENSION );
                $new_name   = strtotime( "now" ) . substr( md5( rand( 1111, 9999 ) ), 0, 8 ) . '.' . $ext;
                $path       = Nbdesigner_IO::create_file_path( NBDESIGNER_TEMP_DIR, $new_name );
                $res        = nbd_download_remote_file( $image, $path['full_path'] );
                if( $res ){
                    $result['images'][$key] = NBDESIGNER_TEMP_URL . $path['date_path']; 
                }else{
                    $result['flag'] = 0;
                    break;
                }
            }
        }

        wp_send_json( $result );
    }
    public function nbd_get_instagram_token(){
        if ( !wp_verify_nonce( $_REQUEST['nonce'], 'save-design') && NBDESIGNER_ENABLE_NONCE ) {
            die('Security error');
        }
        $result = array(
            'flag'          => 0,
            'access_token'  => '',
            'user_id'       => ''
        );

        $response = wp_remote_post( 'https://api.instagram.com/oauth/access_token',
            array(
                'timeout'   => 120,
                'body'      => array(
                    'client_id'     => nbdesigner_get_option( 'nbdesigner_instagram_app_id', '' ),
                    'client_secret' => nbdesigner_get_option( 'nbdesigner_instagram_app_secret', '' ),
                    'grant_type'    => 'authorization_code',
                    'redirect_uri'  => NBDESIGNER_PLUGIN_URL . 'includes/auth-instagram.php',
                    'code'          => $_POST['code']
                )
            )
        );

        if ( !is_wp_error( $response ) ) {
            $body = json_decode( $response['body'], true );
            if( isset( $body['access_token'] ) && isset( $body['user_id'] ) ){
                $result = array(
                    'flag'          => 1,
                    'access_token'  => $body['access_token'],
                    'user_id'       => $body['user_id']
                );
            }
        }

        wp_send_json( $result );
    }
}