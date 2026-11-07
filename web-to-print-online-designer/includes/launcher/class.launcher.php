<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class NBD_Design_Launcher{
    protected static $instance;
    public $query_vars = array(
        'my_store'    => 'my-store'
    );
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function __construct(){
    }
    public function init(){
        /* Settings */
        add_action( 'nbdesigner_include_settings', array( $this, 'include_settings' ) );
        add_filter( 'nbdesigner_settings_tabs', array( $this, 'settings_tabs' ), 20, 1 );
        add_filter( 'nbdesigner_settings_blocks', array( $this, 'settings_blocks' ), 20, 1 );
        add_filter( 'nbdesigner_settings_options', array( $this, 'settings_options' ), 20, 1 );
        add_filter( 'nbdesigner_default_settings', array( $this, 'default_settings' ), 20, 1 );
        add_filter( 'nbd_multicheckbox_settings', array( $this, 'multicheckbox_settings' ), 20, 1 );
        
        add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hidden_order_itemmeta' ) );

        /* Init database */
        add_action( 'nbd_create_tables', array( $this, 'create_tables' ) );
        
        if( nbdesigner_get_option( 'nbdesigner_enable_designer_store', 'no' ) == 'yes' ){
            add_action( 'nbd_menu', array( $this, 'add_sub_menu' ), 191 );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 30, 1 );
            add_action( 'plugins_loaded', array( $this, 'add_designer_role' ) );
            add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
            $this->ajax();

            add_filter( 'nbd_admin_pages', array( $this, 'admin_pages' ), 20, 1 );
        
            add_action( 'woocommerce_before_my_account', array( $this, 'show_link_become_designer' ), 1 );
            add_filter( 'get_avatar_url', array( $this, 'get_avatar_url' ), 100, 3 );

            add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'update_designer_balance_and_order' ), 20 );
            add_action( 'woocommerce_order_status_changed', array( $this, 'on_order_status_change' ), 10, 4 );

            add_filter( 'woocommerce_email_classes', array( $this, 'add_emails_classes' ) );
            add_filter( 'woocommerce_email_actions' , array( $this, 'add_email_actions' ) );

            add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue_scripts' ) );
            add_action( 'wp_footer', array( $this, 'print_launcher_popup' ) );

            add_action( 'nbd_after_option_product_design', array( $this, 'upload_solid_design_option' ), 20, 2 );

            add_action( 'woocommerce_before_single_product_summary', array( $this, 'add_hook_change_image_id' ), 1 );
            add_action( 'woocommerce_after_single_product_summary', array( $this, 'remove_hook_change_image_id' ), 1 );
            add_action( 'woocommerce_single_product_summary', array( $this, 'show_design_author' ), 6 );
            add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'nbdl_before_add_to_cart_button' ) );
            add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 20, 1 );
            add_filter( 'nbo_artwork_action', array( $this, 'nbo_artwork_action' ), 20, 2 );
            add_filter( 'nbd_conditional_show_design_btn', array( $this, 'hide_design_btn' ), 20, 1 );
            add_filter( 'nbo_field_class', array( $this, 'nbo_field_class' ), 20, 2 );

            add_filter( 'woocommerce_cart_item_permalink', array( $this, 'cart_item_permalink' ), 100, 3 );
            add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'cart_item_thumbnail' ), 100, 3 );

            add_filter( 'woocommerce_order_item_permalink', array( $this, 'order_item_permalink' ), 100, 3 );
            add_filter( 'woocommerce_admin_order_item_thumbnail', array( $this, 'admin_order_item_thumbnail' ), 60, 3 );
            add_action( 'woocommerce_after_order_itemmeta', array( $this, 'print_download_design_button' ), 30, 3 );

            /* Storefronts */
            add_action( 'init', array( $this, 'add_endpoints' ) );
            add_filter( 'query_vars', array( $this, 'add_query_vars' ), 0 );
            add_filter( 'the_title', array( $this, 'endpoint_title' ) );
            add_filter( 'woocommerce_account_menu_items', array( $this, 'new_menu_items' ) );
            foreach ( $this->query_vars as $key => $var ){
                add_action( 'woocommerce_account_' . $var . '_endpoint', array( $this, 'page_'.$key . '_content' ), 10, 1 );
            }

            if( nbdesigner_get_option( 'nbdesigner_auto_generate_color_product_preview', 'no' ) == 'yes' ){
                add_filter( 'nbo_product_options', array( $this, 'override_product_image_by_design_preview' ), 10, 2 );
            }

            add_shortcode( 'nbl_designers', array( $this,'nbl_designers_func' ) );
        }
        add_action( 'delete_user', array( $this, 're_assign_templates' ), 10, 2 );
    }
    public function add_endpoints() {
        foreach ( $this->query_vars as $var ){
            add_rewrite_endpoint($var, EP_ROOT | EP_PAGES);
        }
    }
    public function add_query_vars($vars) {
        foreach ( $this->query_vars as $var ){
            $vars[] = $var;
        }
        return $vars;
    }
    public function endpoint_title($title) {
        global $wp_query;
        foreach ( $this->query_vars as $var ){
            $is_endpoint = isset($wp_query->query_vars[$var]);
            if ($is_endpoint && !is_admin() && is_main_query() && in_the_loop() && is_account_page()) {
                switch ( $var ) {
                    case 'my-store':
                        $title = esc_html__('My store', 'web-to-print-online-designer');
                        break;
                }
                remove_filter('the_title', array($this, 'endpoint_title'));
            }
        }
        return $title;
    }
    public function new_menu_items($items) {
        $user_id = get_current_user_id();
        if( !nbdl_is_designer_enabled( $user_id ) ){
            return $items;
        }

        // Remove the logout menu item.
        $logout = $items['customer-logout'];
        unset($items['customer-logout']);

        // Insert your custom endpoint.
        $items['my-store'] = esc_html__('Design store', 'web-to-print-online-designer');

        // Insert back the logout item.
        $items['customer-logout'] = $logout;

        return $items;
    }
    public function page_my_store_content(){
        $user_id    = get_current_user_id();

        if( !nbdl_is_designer_enabled( $user_id ) ){
            return;
        }

        $tabs           = array( 'dashboard', 'withdraw', 'settings', 'designs' );
        $tab            = ( isset( $_GET['tab'] ) && in_array( $_GET['tab'], $tabs ) ) ? $_GET['tab'] : 'dashboard';
        $designer_id    = get_current_user_id();
        $data = array(
            'tab'           => $tab,
            'designer_id'   => $designer_id
        );
        $function   = "get_store_{$tab}_data";
        $data       = $this->$function( $data );

        ob_start();
        nbdesigner_get_template("launcher/store/tabs.php", $data);
        nbdesigner_get_template("launcher/store/{$tab}.php", $data);
        $content = ob_get_clean();
        echo $content;
    }
    public function get_store_dashboard_data( $data ){
        $designer_id        = $data['designer_id'];
        $data['designs']    = nbdl_get_design_status_count( $designer_id );
        $data['sales']      = nbdl_get_sale_status_count( $designer_id );

        $labels         = array();
        $design_counts  = array();
        $sale_counts    = array();
        $start_date     = new DateTime( 'first day of this month' );
        $end_date       = new DateTime();
        $design_data    = nbdl_get_design_report( 'day', $start_date->format( 'Y-m-d' ), $end_date->format( 'Y-m-d' ), $designer_id );
        $sale_data      = nbdl_get_sale_report( 'day', $start_date->format( 'Y-m-d' ), $end_date->format( 'Y-m-d' ), $designer_id );

        for ( $i = $start_date; $i <= $end_date; $i->modify( '+1 day' ) ){
            $date                     = $i->format( 'Y-m-d' );
            $labels[ $date ]          = $date;
            $design_counts[ $date ]   = 0;
            $sale_counts[ $date ]     = 0;
        }

        foreach ( $design_data as $row ) {
            $date                   = date( 'Y-m-d', strtotime( $row->created_date ) );
            $design_counts[ $date ] = (int) $row->total;
        }

        foreach ( $sale_data as $row ) {
            $date                   = date( 'Y-m-d', strtotime( $row->created_date ) );
            $sale_counts[ $date ]   = (int) $row->total;
        }

        $data['report'] = array(
            'labels'   => array_values( $labels ),
            'datasets' => array(
                array(
                    'label'           => __( 'Created designs', 'web-to-print-online-designer' ),
                    'borderColor'     => '#3498db',
                    'fill'            => false,
                    'data'            => array_values( $design_counts ),
                    'tooltipLabel'    => __( 'Total', 'web-to-print-online-designer' )
                ),
                array(
                    'label'           => __( 'Sold designs', 'web-to-print-online-designer' ),
                    'borderColor'     => '#1abc9c',
                    'fill'            => false,
                    'data'            => array_values( $sale_counts ),
                    'tooltipLabel'    => __( 'Total', 'web-to-print-online-designer' )
                )
            )
        );

        return $data;
    }
    public function get_store_withdraw_data( $data ){
        $data['balance']                = nbdl_get_designer_balance( $data['designer_id'], false );
        $data['balance_display']        = wc_price( $data['balance'] );
        $data['min_withdraw']           = wc_price( nbdesigner_get_option( 'nbdesigner_minimum_withdraw', 0 ) );
        $data['has_withdraw_balance']   = NBDL_Withdraw()->has_withdraw_balance( $data['designer_id'] );
        $data['has_pending_request']    = NBDL_Withdraw()->has_pending_request( $data['designer_id'] );
        $data['pending_requests']       = NBDL_Withdraw()->get_withdraw_requests( $data['designer_id'] );
        $data['approved_requests']      = NBDL_Withdraw()->get_withdraw_requests( $data['designer_id'], 1 );
        $data['cancelled_requests']     = NBDL_Withdraw()->get_withdraw_requests( $data['designer_id'], 2 );
        return $data;
    }
    public function get_store_settings_data( $data ){
        global $current_user;

        $designer                           = new NBD_Designer( $data['designer_id'] );
        $data['user_info']                  = $designer->get_store_info();
        $data['user_info']['gravatar_url']  = $designer->get_avatar();
        $data['banner_width']               = absint( nbdesigner_get_option( 'nbdesigner_designer_banner_width', 1050 ) );
        $data['banner_height']              = absint( nbdesigner_get_option( 'nbdesigner_designer_banner_height', 200 ) );

        return $data;
    }
    public function get_store_designs_data( $data ){
        global $wp;
        
        $limit              = 20;
        $counts             = nbdl_get_design_status_count( $data['designer_id'] );
        $current_page       = absint( $wp->query_vars['my-store'] );
        $current_page       = $current_page ? $current_page : 1;
        $offset             = ( $current_page - 1 ) * $limit;
        $max_page           = ceil( $counts['all'] / $limit );
        $dashboard_url      = wc_get_endpoint_url( 'my-store', '', wc_get_page_permalink( 'myaccount' ) );
        $designs            = nbdl_get_designs( '', $limit, $offset, $data['designer_id'] );
        if( $max_page <= 1 ){
            $next_page = $prev_page = '';
        }else{
            $prev_page = 1 != $current_page ? add_query_arg( array( 'tab' => 'designs' ), wc_get_endpoint_url( 'my-store', $current_page - 1, wc_get_page_permalink( 'myaccount' ) ) ) : '';
            $next_page = $max_page != $current_page ? add_query_arg( array( 'tab' => 'designs' ), wc_get_endpoint_url( 'my-store', $current_page + 1, wc_get_page_permalink( 'myaccount' ) ) ) : '';
        }
        
        $data['designs']    = array();
        foreach( $designs as $key => $design ){
            $data['designs'][$key] = array(
                'id'            => $design->id,
                'user'          => nbdl_get_designer_data( $design->user_id ),
                'product'       => nbdl_get_product_data( $design->product_id, $design->variation_id ),
                'previews'      => nbdl_get_design_preview( $design->folder ),
                'date'          => $design->created_date,
                'folder'        => $design->folder,
                'type'          => $design->type,
                'status'        => (int) $design->publish
            );
        }

        $data['counts']         = $counts;
        $data['next_page']      = $next_page;
        $data['prev_page']      = $prev_page;
        $data['current_page']   = $current_page;

        return $data;
    }
    public function create_tables(){
        global $wpdb;
        $collate = '';
        if ( $wpdb->has_cap( 'collation' ) ) {
            $collate = $wpdb->get_charset_collate();
        } 
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $tables = "CREATE TABLE `{$wpdb->prefix}nbdesigner_withdraw` (
               `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
               `user_id` bigint(20) unsigned NOT NULL,
               `amount` float(11) NOT NULL,
               `date` timestamp NOT NULL,
               `status` int(1) NOT NULL,
               `method` varchar(30) NULL,
               `note` text NULL,
               `ip` varchar(50) NULL,
              PRIMARY KEY  (id)
            ) $collate;
            CREATE TABLE `{$wpdb->prefix}nbdesigner_balance` (
               `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
               `user_id` bigint(20) unsigned NOT NULL,
               `transaction_id` bigint(20) unsigned NOT NULL,
               `transaction_type` varchar(30) NOT NULL,
               `note` text NULL,
               `debit` float(11) NOT NULL,
               `credit` float(11) NOT NULL,
               `status` varchar(30) DEFAULT NULL,
               `transaction_date` timestamp NOT NULL,
               `balance_date` timestamp NOT NULL,
              PRIMARY KEY  (id)
            ) $collate;
            CREATE TABLE `{$wpdb->prefix}nbdesigner_orders` (
               `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
               `design_id` varchar(30) NOT NULL,
               `transaction_id` bigint(20) unsigned NOT NULL,
               `qty` bigint(20) unsigned NOT NULL,
               `status` varchar(30) DEFAULT NULL,
               `transaction_date` timestamp NOT NULL,
              PRIMARY KEY  (id)
            ) $collate;";
        
        @dbDelta( $tables );
    }
    public function ajax(){
        $ajax_events = array(
            'nbdl_update_designer_status'   => true,
            'nbdl_get_product_info'         => true,
            'nbdl_get_related_products'     => true,
            'nbdl_submit_product'           => true
        );
        foreach ( $ajax_events as $ajax_event => $nopriv ) {
            add_action( 'wp_ajax_' . $ajax_event, array( $this, $ajax_event ) );
            if ( $nopriv ) {
                add_action( 'wp_ajax_nopriv_' . $ajax_event, array( $this, $ajax_event ) );
            }
        }
    }
    public function admin_pages( $pages ){
        $pages[] = 'nbdesigner_page_nbd_designers';
        return $pages;
    }
    public function include_settings(){
        require_once(NBDESIGNER_PLUGIN_DIR . 'includes/settings/launcher.php');
    }
    public function settings_tabs( $tabs ){
        $tabs['designers'] = '<span class="dashicons dashicons-groups"></span> '. __('Designers', 'web-to-print-online-designer');
        return $tabs;
    }
    public function settings_blocks( $blocks ){
        $blocks['designers'] = array(
            'general-designers' => __('General', 'web-to-print-online-designer'),
            'for-admin'         => __('Admin', 'web-to-print-online-designer'),
            'for-designer'      => __('Designer', 'web-to-print-online-designer'),
            'design'            => __('Design', 'web-to-print-online-designer')
        );
        return $blocks;
    }
    public function settings_options( $options ){
        $launcher_options               = Nbdesigner_Launcher::get_options();
        $options['general-designers']   = $launcher_options['general'];
        $options['for-admin']           = $launcher_options['admin'];
        $options['for-designer']        = $launcher_options['designer'];
        $options['design']              = $launcher_options['design'];
        return $options;
    }
    public function default_settings( $settings ){
        $settings['nbdesigner_enable_designer_store']                   = 'no';
        $settings['nbdesigner_commission_type']                         = 'percentage';
        $settings['nbdesigner_default_commission']                      = 0;
        $settings['nbdesigner_default_commission2']                     = '0|0';
        $settings['nbdesigner_designer_banner_width']                   = 1050;
        $settings['nbdesigner_designer_banner_height']                  = 200;
        $settings['nbdesigner_minimum_withdraw']                        = 0;
        $settings['nbdesigner_withdraw_threshold']                      = 0;
        $settings['nbdesigner_order_status_for_withdraw_wc-completed']  = 1;
        $settings['nbdesigner_order_status_for_withdraw_wc-processing'] = 0;
        $settings['nbdesigner_order_status_for_withdraw_wc-on-hold']    = 0;
        $settings['nbdesigner_auto_generate_color_product_preview']     = 'no';
        
        return $settings;
    }
    public function multicheckbox_settings( $settings ){
        $settings['nbdesigner_order_status_for_withdraw_wc-completed']  = 1;
        $settings['nbdesigner_order_status_for_withdraw_wc-processing'] = 0;
        $settings['nbdesigner_order_status_for_withdraw_wc-on-hold']    = 0;
        
        return $settings;
    }
    public function register_rest_routes(){
        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/launcher/api/designer.php' );
        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/launcher/api/withdraw.php' );
        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/launcher/api/design.php' );
        require_once( NBDESIGNER_PLUGIN_DIR . 'includes/launcher/api/report.php' );
        $designer_api   = new NBD_Designer_API();
        $withdraw_api   = new NBD_Withdraw_API();
        $design_api     = new NBD_Design_API();
        $report_api     = new NBD_Report_API();
        $designer_api->register_rest_routes();
        $withdraw_api->register_rest_routes();
        $design_api->register_rest_routes();
        $report_api->register_rest_routes();
    }
    public function add_sub_menu() {
        if( current_user_can( 'manage_nbd_tool' ) ){
            add_submenu_page(
                'nbdesigner', __( 'Design Launcher', 'web-to-print-online-designer'), __( 'Design Launcher', 'web-to-print-online-designer' ), 'manage_nbd_tool', 'nbd_designers', array( $this, 'manage_designers' )
            );
        }
    }
    public function manage_designers(){
        include_once( NBDESIGNER_PLUGIN_DIR . 'views/launcher/admin.php' );
    }
    public function add_emails_classes( $emails ){
        $emails['NBDL_Designer_Enabled']        = include( NBDESIGNER_PLUGIN_DIR . 'includes/launcher/emails/designer_enabled.php' );
        $emails['NBDL_Designer_Disabled']       = include( NBDESIGNER_PLUGIN_DIR . 'includes/launcher/emails/designer_disabled.php' );
        $emails['NBDL_Withdraw_Request']        = include( NBDESIGNER_PLUGIN_DIR . 'includes/launcher/emails/withdraw_request.php' );
        $emails['NBDL_Withdraw_Approved']       = include( NBDESIGNER_PLUGIN_DIR . 'includes/launcher/emails/withdraw_approved.php' );
        $emails['NBDL_Withdraw_Cancelled']      = include( NBDESIGNER_PLUGIN_DIR . 'includes/launcher/emails/withdraw_cancelled.php' );
        return $emails;
    }
    public function add_email_actions( $actions ){
        $actions[] = 'nbdl_designer_enabled';
        $actions[] = 'nbdl_designer_disabled';
        $actions[] = 'nbdl_after_withdraw_request';
        $actions[] = 'nbdl_withdraw_request_approved';
        $actions[] = 'nbdl_withdraw_request_cancelled';
        return $actions;
    }
    public function admin_enqueue_scripts( $hook ) {
        if( $hook == 'nbdesigner_page_nbd_designers' ){
            wp_enqueue_media();
            wp_register_script( 'nbd-admin-launcher', NBDESIGNER_PLUGIN_URL . 'views/launcher/dist/app.js', array('jquery', 'accounting', 'wc-enhanced-select'), NBDESIGNER_VERSION, true );
            wp_enqueue_script( 'nbd-admin-launcher' );
            wp_register_style( 'nbd-admin-launcher-css', NBDESIGNER_PLUGIN_URL . 'views/launcher/dist/style.css', array('woocommerce_admin_styles'), NBDESIGNER_VERSION );
            wp_enqueue_style( array('nbd-admin-launcher-css') );
            wp_localize_script( 'nbd-admin-launcher', 'nbdl', array(
                'ajax_url'              => admin_url('admin-ajax.php'),
                'rest_url'              => esc_url_raw( rest_url() ) . 'nbdl/v1/',
                'nonce'                 => wp_create_nonce( 'wp_rest' ),
                'designer_url'          => getUrlPageNBD('designer'),
                'assets_images_url'     => NBDESIGNER_PLUGIN_URL . 'assets/images/',
                'banner_width'          => nbdesigner_get_option( 'nbdesigner_designer_banner_width', 1050 ),
                'banner_height'         => nbdesigner_get_option( 'nbdesigner_designer_banner_height', 200 ),
                'langs'                 => $this->i18n(),
                'edit_user_link'        => esc_url( add_query_arg('wp_http_referer', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), self_admin_url( 'user-edit.php?user_id=replace_user_id' ) ) ),
                'edit_design_link'      => esc_url( add_query_arg(array( 'rd' => 'admin_templates', 'design_type' => 'template', 'task' => 'edit'), getUrlPageNBD('create') ) ),
                'download_design_link'  => NBDESIGNER_CUSTOMER_URL )
            );
        }
    }
    public function frontend_enqueue_scripts(){
        wp_register_style( 'nbd_launcher', NBDESIGNER_CSS_URL . 'launcher.css', array(), NBDESIGNER_VERSION );
        wp_register_script( 'nbd_launcher', NBDESIGNER_JS_URL . 'launcher.js', array('jquery', 'selectWoo'), NBDESIGNER_VERSION );

        if ( is_account_page() ) {
            wp_enqueue_style( 'nbd_launcher' );
            wp_enqueue_script( 'nbd_launcher' );

            wp_localize_script( 'nbd_launcher', 'nbdl', array(
                'ajax_url'                  => admin_url('admin-ajax.php'),
                'nonce'                     => wp_create_nonce( 'nbd_launcher_nonce' ),
                'create_design_url'         => add_query_arg(array('task'  => 'create','rd' => 'my_store_design'), getUrlPageNBD('create')),
                'msg_alert_missing_file'    => __('Please choose all necessary files!', 'web-to-print-online-designer'),
                'max_preview_dimension'     => apply_filters( 'nbdl_max_preview_dimension', 1000 )
            ));
        }

        nbdesigner_get_template("gallery/search-bar.php", array());
    }
    public function upload_solid_design_option( $post_id, $option ){
        $enable_upload_solid_design = isset( $option['upload_solid_design'] ) ? $option['upload_solid_design'] : 0;
        ?>
        <div  id="nbd-upload_solid_design" class="nbdesigner-opt-inner" >
            <label for="_nbdesigner_option[upload_solid_design]" class="nbdesigner-option-label">
                <?php esc_html_e('Allow designer upload and sell solid design', 'web-to-print-online-designer'); ?>
            </label>
            <input type="hidden" value="0" name="_nbdesigner_option[upload_solid_design]"/>
            <input type="checkbox" value="1" name="_nbdesigner_option[upload_solid_design]" id="_nbdesigner_option[upload_solid_design]" <?php checked( $enable_upload_solid_design ); ?> class="short" />
        </div>
        <?php
    }
    public function print_launcher_popup(){
        if ( is_account_page() ) {

            $products           = nbd_get_products_has_design( true );
            $template_tags      = get_terms( 'template_tag', 'hide_empty=0' );
            $tags               = array();
            if ( ! empty( $template_tags ) && ! is_wp_error( $template_tags ) ){
                foreach( $template_tags as $tag ){
                    $tags[] = array(
                        'term_id'   =>  $tag->term_id,
                        'name'      =>  $tag->name
                    );
                }
            }

            ob_start();
            nbdesigner_get_template("launcher/store/popup.php", array(
                'products'  => $products,
                'tags'      => $tags
            ));
            $content = ob_get_clean();
            echo $content;
        }
    }
    public function add_designer_role(){
        $capabilities = array(
            0 => 'sell_nbd_design',
            1 => 'become_designer'
        );
        $capabilities = apply_filters( 'nbd_designer_cap', $capabilities );
        $desinger_role = get_role( 'designer' );
        if( null === $desinger_role ){
            add_role( 'designer', __( 'Designer', 'web-to-print-online-designer' ), array(
                'sell_nbd_design'   => true,
                'become_designer'   => true,
                'upload_files'      => true
            ) );
        }
        $admin_role = get_role( 'administrator' );
        if (null != $admin_role) {
            foreach( $capabilities as $cap ){
                $admin_role->add_cap( $cap );
            }
        }
        $shop_manager_role = get_role( 'shop_manager' );
        if (null != $shop_manager_role) {
            foreach( $capabilities as $cap ){
                $shop_manager_role->add_cap( $cap );
            }
        }
    }
    public function get_avatar_url( $url, $id_or_email, $args ){
        if ( is_numeric( $id_or_email ) ) {
            $user = get_user_by( 'id', $id_or_email );
        } elseif ( is_object( $id_or_email ) ) {
            if ( $id_or_email->user_id != '0' ) {
                $user = get_user_by( 'id', $id_or_email->user_id );
            } else {
                return $url;
            }
        } else {
            $user = get_user_by( 'email', $id_or_email );
        }
        
        if ( ! $user ) {
            return $url;
        }
        
        $designer       = new NBD_Designer( $user->ID );
        $gravatar_id    = $designer->get_avatar_id();
        
        if ( ! $gravatar_id ) {
            return $url;
        }
        
        $avatar_url = wp_get_attachment_thumb_url( $gravatar_id );

        if ( empty( $avatar_url ) ) {
            return $url;
        }

        return esc_url( $avatar_url );
    }
    public function show_link_become_designer(){
        $user_id    = get_current_user_id();
        $link       = wc_get_endpoint_url( 'artist-info', $user_id, wc_get_page_permalink( 'myaccount' ) );
        if( !nbdl_is_user_designer( $user_id ) ):
        ?>
        <div class="nbdl-become-designer">
            <p><?php _e( 'You want to sell your designs to earn commission?', 'web-to-print-online-designer' ); ?></p>
            <a class="button" href="<?php echo $link; ?>"><?php _e( 'Become designer', 'web-to-print-online-designer' ); ?></a>
        </div>
        <?php
        endif;
    }
    public function nbdl_update_designer_status(){
        $user_id    = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
        $type       = isset( $_POST['type'] ) ? $_POST['type'] : '';
        $value      = isset( $_POST['value'] ) ? $_POST['value'] : '';
        $result     = array(
            'flag'  => 0
        );
        
        if( $user_id == 0 || $type == '' ){
            echo json_encode( $result );
            wp_die();
        }
        
        if( $type == 'enabled' ){
            $res = update_user_meta( $user_id, 'nbd_sell_design', $value );
            $result['message'] = $value == 'on' ? __( 'Designer has been enabled', 'web-to-print-online-designer' ) : __( 'Designer has been disabled', 'web-to-print-online-designer' );
        }
        if( $type == 'featured' ){
            $res = update_user_meta( $user_id, 'nbd_feature_designer', $value );
            $result['message'] = $value == 'on' ? __( 'Featured designer has been enabled', 'web-to-print-online-designer' ) : __( 'Featured designer has been disabled', 'web-to-print-online-designer' );
        }
        
        if( $res !== false ){
            $result['flag'] = 1;
        }
        
        echo json_encode( $result );
        wp_die();
    }
    public function update_designer_balance_and_order( $order_id ){
        global $wpdb;
        $order  = wc_get_order( $order_id );

        if ( !empty( $order->post_parent ) ){
            return;
        }
        
        $designers      = array();
        $designs        = array();
        $order_status   = $order->get_status();
        $_designers     = nbd_get_designers_by( $order );
        if ( stripos( $order_status, 'wc-' ) === false ) {
            $order_status = 'wc-' . $order_status;
        }
        foreach( $_designers as $designer_id => $_designer ){
            foreach( $_designer as $design_id => $items ){
                if( !isset( $designers[$designer_id] ) ) $designers[$designer_id] = array();
                $designers[$designer_id] = array_merge( $designers[$designer_id], $items );
                $designs[$design_id] = count( $items );
            }
        }

        foreach( $designers as $designer_id => $items ){
            $designer_earning   = $this->get_designer_earning( $order, $designer_id, $items );
            $threshold_day      = absint( nbdesigner_get_option( 'nbdesigner_withdraw_threshold', 0 ) );
            
            $wpdb->insert( $wpdb->prefix . 'nbdesigner_balance',
                array(
                    'user_id'           => $designer_id,
                    'transaction_id'    => $order_id,
                    'transaction_type'  => 'new_order',
                    'note'              => 'New order',
                    'debit'             => $designer_earning,
                    'credit'            => 0,
                    'status'            => $order_status,
                    'transaction_date'  => current_time( 'mysql' ),
                    'balance_date'      => date( 'Y-m-d h:i:s', strtotime( current_time( 'mysql' ) . ' + '.$threshold_day.' days' ) )
                ),
                array(
                    '%d',
                    '%d',
                    '%s',
                    '%s',
                    '%f',
                    '%f',
                    '%s',
                    '%s',
                    '%s',
                )
            );
        }

        foreach( $designs as $design_id => $qty ){
            $wpdb->insert( $wpdb->prefix . 'nbdesigner_orders',
                array(
                    'design_id'         => $design_id,
                    'transaction_id'    => $order_id,
                    'qty'               => $qty,
                    'status'            => $order_status,
                    'transaction_date'  => current_time( 'mysql' )
                ),
                array(
                    '%s',
                    '%d',
                    '%d',
                    '%s',
                    '%s'
                )
            );
        }
    }
    public function get_designer_earning( $order, $designer_id, $items ){
        $earning    = 0;
        $commission = $this->get_designer_commision( $designer_id );
        foreach ( $items as $item ) {
            $refund     = $order->get_total_refunded_for_item( $item->get_id() );
            if ( !$refund ) {
                $total  = $item->get_total();
                $earning += $commission['flat'] + $commission['percentage'] * $total / 100;
            }
        }
        return $earning;
    }
    public function get_designer_commision( $designer_id ){
        $designer           = new NBD_Designer( $designer_id );
        $commission_type    = $designer->get_artist_commission_type();
        if( $commission_type != 'combine' ){
            $commission_value = (float)$designer->get_artist_commission();
            $commission = array(
                'flat'          => $commission_type == 'flat' ? $commission_value : 0,
                'percentage'    => $commission_type == 'percentage' ? $commission_value : 0
            );
        }else{
            $commission_value   = $designer->get_artist_commission2();
            $commission = array(
                'flat'          => isset( $commission_value[1] ) ? (float)$commission_value[1] : 0,
                'percentage'    => isset( $commission_value[0] ) ? (float)$commission_value[0] : 0
            );
        }
        return $commission;
    }
    public function hidden_order_itemmeta( $order_items ){
        $order_items[] = '_nbd_design_id';
        return $order_items;
    }
    function on_order_status_change( $order_id, $old_status, $new_status, $order ) {
        global $wpdb;

        if ( stripos( $new_status, 'wc-' ) === false ) {
            $new_status = 'wc-' . $new_status;
        }

        $wpdb->update( $wpdb->prefix . 'nbdesigner_orders',
            array( 'status' => $new_status ),
            array( 'transaction_id' => $order_id ),
            array( '%s' ),
            array( '%d' )
        );

        $wpdb->update( $wpdb->prefix . 'nbdesigner_balance',
            array( 'status' => $new_status ),
            array( 'transaction_id' => $order_id, 'transaction_type' => 'new_order' ),
            array( '%s' ),
            array( '%d', '%s' )
        );
    }
    public function nbdl_get_product_info(){
        if (!wp_verify_nonce( $_POST['nonce'], 'nbd_launcher_nonce' ) && NBDESIGNER_ENABLE_NONCE) {
            die('Security error');
        }

        $product_id = absint( $_POST['product_id'] );
        $data       = array(
            'flag'  => 1
        );

        if( $product_id == 0 ){
            $data['message']    = __('Please select a product before upload design!', 'web-to-print-online-designer');
            $data['flag']       = 0;
            echo json_encode( $data );
            wp_die();
        }

        $data['setting']    = unserialize( get_post_meta( $product_id, '_designer_setting', true ) );
        foreach( $data['setting'] as $key => $side ){
            $data['setting'][$key]['img_src']       = is_numeric( $side['img_src'] ) ? wp_get_attachment_url( $side['img_src'] ) : $side['img_src'];
            $data['setting'][$key]['img_overlay']   = is_numeric( $side['img_overlay'] ) ? wp_get_attachment_url( $side['img_overlay'] ) : $side['img_overlay'];
        }

        $guideline_files    = unserialize( get_post_meta( $product_id, '_nbdg_files', true ) );
        if( $guideline_files ){
            $data['guidelines'] = $guideline_files;
        }

        if( isset( $_POST['task'] ) && $_POST['task'] == 'edit' ){
            $design_id  = wc_clean( $_POST['design_id'] );
            $design     = nbd_get_design( $design_id );
            if( !empty( $design ) ){
                $design_previews            = Nbdesigner_IO::get_list_images( NBDESIGNER_CUSTOMER_DIR . '/' . $design['resource'], 1 );
                $design['side_previews']    = array();
                foreach( $design_previews as $design_preview ){
                    $filename   = pathinfo( $design_preview, PATHINFO_FILENAME );
                    $arr        = explode( '_', $filename );
                    if( isset( $arr[1] ) ){
                        $index      = $arr[1];
                        $design['side_previews'][$index] = Nbdesigner_IO::convert_path_to_url( $design_preview );
                    }
                }

                $data['design']     = $design;
            }
        }

        echo json_encode( $data );
        wp_die();
    }
    public function nbdl_get_related_products(){
        if (!wp_verify_nonce( $_POST['nonce'], 'nbd_launcher_nonce' ) && NBDESIGNER_ENABLE_NONCE) {
            die('Security error');
        }

        $product_id         = absint( $_POST['product_id'] );
        $products           = nbd_get_products_has_design( true );
        $cats               = $this->get_product_categories( $product_id );
        $data['products']   = array();
        $related_number     = apply_filters( 'nbdl_number_of_related_product', 20 );
        $count              = 0;

        foreach( $products as $key => $product ){
            if( $product['allow_upload_solid'] != 0 && $product['product_id'] != $product_id ){
                $product_cats = $this->get_product_categories( $product['product_id'] );
                if( count( array_intersect( $cats, $product_cats ) ) ){
                    $count++;
                    $products[$key]['seleted']  = true;
                    $data['products'][]         = $product;
                    if( $count >= $related_number ) break;
                }
            }
        }

        if( $count < $related_number ){
            foreach( $products as $key => $product ){
                if( $product['allow_upload_solid'] != 0 && !isset( $products[$key]['seleted'] )  && $product['product_id'] != $product_id ){
                    $count++;
                    $data['products'][] = $product;
                    if( $count >= $related_number ) break;
                }
            }
        }

        foreach( $data['products'] as $key => $product ){
            $setting                = unserialize( get_post_meta( $product['product_id'], '_designer_setting', true ) );
            $side                   = $setting[0];
            $side['img_src']        = is_numeric( $side['img_src'] ) ? wp_get_attachment_url( $side['img_src'] ) : $side['img_src'];
            $side['img_overlay']    = is_numeric( $side['img_overlay'] ) ? wp_get_attachment_url( $side['img_overlay'] ) : $side['img_overlay'];
            $data['products'][$key]['setting']  = $side;
        }

        echo json_encode( $data );
        wp_die();
    }
    public function get_product_categories( $product_id ){
        $terms  = get_the_terms( $product_id, 'product_cat' );
        $cats   =  array();
        foreach ($terms as $term) {
            $cats[] = $term->term_id;
        }
        return $cats;
    }
    public function nbdl_submit_product(){
        if (!wp_verify_nonce( $_POST['nonce'], 'nbd_launcher_nonce' ) && NBDESIGNER_ENABLE_NONCE) {
            die('Security error');
        }

        $result = array(
            'flag'      => 1,
            'message'   => '',
            'templates' => array()
        );
        $products               = array();
        $product_id             = absint( $_POST['product_id'] );
        $related_product_ids    = wc_clean( $_POST['related_product_ids'] );
        $tags                   = wc_clean( $_POST['tags'] );
        $name                   = stripslashes( wc_clean( $_POST['name'] ) );
        $task                   = isset( $_POST['task'] ) ? wc_clean( $_POST['task'] ) : 'new';
        $folder                 = isset( $_POST['folder'] ) ? wc_clean( $_POST['folder'] ) : substr(md5(uniqid()),0,5).rand(1,100).time();
        $path                   = NBDESIGNER_CUSTOMER_DIR . '/' . $folder;
        $max_upload_size        = absint( nbdesigner_get_option( 'nbdesigner_maxsize_upload_file', nbd_get_max_upload_default() ) );
        $max_size_in_byte       = $max_upload_size * 1024 * 1024;
        $content_designs        = array();

        if( !empty( $related_product_ids ) ){
            $products = explode( ',', $related_product_ids );
        }
        $products[]     = $product_id;

        if ( wp_mkdir_p( $path ) ) {
            foreach( $_FILES as $key => $file ){
                if( $file['error'] ){
                    $result['flag']     = 0;
                    $result['message']  = __('Upload file error!', 'web-to-print-online-designer');
                    break;
                }else{
                    if( $file['size'] > $max_size_in_byte ){
                        $result['flag']     = 0;
                        $result['message']  = __('File size too big', 'web-to-print-online-designer');
                        break;
                    }
                    
                    $type           = $file["type"];
                    $ext            = pathinfo( $file["name"], PATHINFO_EXTENSION );
                    $image_type     = array( 'image/jpeg', 'image/jpg', 'image/png' );

                    if( $key == 'product_preview' ){
                        if( !in_array( $type, $image_type ) ){
                            $result['flag']     = 0;
                            $result['message']  = __('Only alllow image for product preview.', 'web-to-print-online-designer');
                            break;
                        }
                        $thumb_id = nbd_upload_media( $file );
                        if( !is_numeric( $thumb_id ) ){
                            $result['flag']     = 0;
                            $result['message']  = __('Fail to create design thumbnail.', 'web-to-print-online-designer');
                            break;
                        }
                    } elseif ( $key == 'design' ){
                        if( $ext != 'zip' ) {
                            $result['flag']     = 0;
                            $result['message']  = __('Only alllow zip extension for design file.', 'web-to-print-online-designer');
                            break;
                        }
                        $full_name = $path . '/design.' . $ext;
                    } else {
                        if( !in_array( $type, $image_type ) ){
                            $result['flag']     = 0;
                            $result['message']  = __('Only alllow image for content design preview.', 'web-to-print-online-designer');
                            break;
                        }

                        $arr                        = explode('__', $key);
                        $index                      = $arr[1];
                        $full_name                  = $path . '/side_' . $index . '.' . $ext;
                        $content_designs[$index]    = $full_name;
                    }
                    if( $key != 'product_preview' ){
                        if ( !move_uploaded_file( $file["tmp_name"], $full_name ) ) {
                            $result['flag']     = 0;
                            $result['message']  = __('Upload file error!', 'web-to-print-online-designer');
                            break;
                        }
                    }
                }
            }

            if( $task == 'edit' ){
                $design_id  = wc_clean( $_POST['design_id'] );
                $design     = nbd_get_design( $design_id );
                if( !empty( $design ) ){
                    $resource_folder = isset( $_POST['design'] ) ? wc_clean( $_POST['design'] ) : '';
                    if( $resource_folder != '' ){
                        $resource_path  = NBDESIGNER_CUSTOMER_DIR . '/' . $resource_folder;
                        $folder         = $resource_folder;
                    }

                    if( isset( $_POST['product_preview'] ) && absint( $_POST['product_preview'] ) > 0 ){
                        $thumb_id = absint( $_POST['product_preview'] );
                    }

                    if( $resource_folder == '' ){
                        $design_previews        = Nbdesigner_IO::get_list_images( NBDESIGNER_CUSTOMER_DIR . '/' . $design['resource'], 1 );

                        foreach( $_POST as $key => $post ){
                            if( false !== strpos( $key, 'side_previews' ) ){
                                $arr    = explode('__', $key);
                                $index  = $arr[1];
                                foreach( $design_previews as $design_preview ){
                                    $path_parts = pathinfo( $design_preview );
                                    if( $path_parts['filename'] == 'side_' . $index ){
                                        $dst = NBDESIGNER_CUSTOMER_DIR . '/' . $folder . '/' . $path_parts['basename'];
                                        if( copy( $design_preview, $dst ) ){
                                            $content_designs[$index]    = $dst;
                                        }else{
                                            $result['flag']     = 0;
                                            $result['message']  = __('Fail to edit exist design preview image!', 'web-to-print-online-designer');
                                            break;
                                        }
                                    }
                                }
                                if( $result['flag'] == 0 ) break;
                            }
                        }
                    }else{
                        foreach( $content_designs as $key => $content_design ){
                            $basename   = pathinfo( $content_design, PATHINFO_BASENAME );
                            $dst        = $resource_path . '/' . $basename;
                            if( !copy( $content_design, $dst ) ){
                                $result['flag']     = 0;
                                $result['message']  = __('Fail to edit exist design preview image!', 'web-to-print-online-designer');
                                break;
                            }
                        }

                        $design_previews = Nbdesigner_IO::get_list_images( $resource_path, 1 );

                        if( $result['flag'] == 1 ){
                            foreach( $design_previews as $design_preview ){
                                $filename   = pathinfo( $design_preview, PATHINFO_FILENAME );
                                $arr        = explode('_', $filename);
                                if( isset( $arr[1] ) ){
                                    $index      = $arr[1];
                                    $content_designs[$index]    = $design_preview;
                                }
                            }
                        }
                    }
                }else{
                    $result['flag']     = 0;
                    $result['message']  = __('Design does not exist!', 'web-to-print-online-designer');
                }
            }

            if( $result['flag'] == 1 ){
                $user_id                = wp_get_current_user()->ID;
                $publish                = nbd_check_publish_design_permission( $user_id ) ? 1 : 0;
                $approved               = array();
                $need_generate_preview  = nbdesigner_get_option( 'nbdesigner_auto_generate_color_product_preview', 'no' ) == 'yes' ? $publish : 0;

                foreach( $products as $pid ){
                    $setting = unserialize( get_post_meta( $pid, '_designer_setting', true ) );
                    $p_folder = $this->create_side_preview( $setting, $content_designs );
                    if( $p_folder != false ){
                        $data = array(
                            'product_id'    => $pid,
                            'variation_id'  => 0,
                            'folder'        => $p_folder,
                            'user_id'       => $task == 'edit' ? $design['user_id'] : $user_id,
                            'created_date'  => current_time( 'mysql' ),
                            'publish'       => $publish,
                            'private'       => 0,
                            'priority'      => 0,
                            'type'          => 'solid',
                            'resource'      => $folder
                        );

                        if( $pid == $product_id && isset( $thumb_id ) ){
                            $data['thumbnail'] = $thumb_id;
                        }

                        if( !empty( $tags ) && !is_null( $tags ) ){
                            $data['tags'] = $tags;
                        }

                        if( !empty( $name ) ){
                            $data['name'] = $name;
                        }

                        if( $task == 'edit' && $pid == $product_id ){
                            $res = $this->update_solid_template( $design_id, $data );

                            if( $res && $need_generate_preview ){
                                $approved[] = $design_id;
                            }
                        }else{
                            $res = $this->insert_solid_template( $data );

                            if( $res && $need_generate_preview ){
                                $last_design = $this->get_last_template();
                                if( $last_design ){
                                    $approved[] = $last_design->id;
                                }
                            }
                        }
                        if( $res ){
                            $p_path             = NBDESIGNER_CUSTOMER_DIR . '/' . $p_folder;
                            $p_preview_path     = $p_path . '/preview';
                            $images             = Nbdesigner_IO::get_list_images( $p_preview_path, 1 );
                            ksort( $images );
                            $result['templates'][$pid] = array();
                            foreach( $images as $image ){
                                $result['templates'][$pid][] = Nbdesigner_IO::wp_convert_path_to_url( $image );
                            }
                        }
                    }
                }

                if( $need_generate_preview && count( $approved ) > 0 ){
                    nbdl_generate_color_product_design( $approved );
                }
            }
        }else{
            $result['flag']     = 0;
            $result['message']  = __('Can not create design folder!', 'web-to-print-online-designer');
        }

        echo json_encode( $result );
        wp_die();
    }
    public function insert_solid_template( $data ){
        global $wpdb;
        $table_name = $wpdb->prefix . 'nbdesigner_templates';
        return $wpdb->insert( $table_name, $data );
    }
    public function update_solid_template( $id, $data ){
        global $wpdb;
        $table_name = $wpdb->prefix . 'nbdesigner_templates';
        return $wpdb->update("{$wpdb->prefix}nbdesigner_templates", $data, array( 'id' => $id) );
    }
    public function get_last_template(){
        global $wpdb;
        $table_name = $wpdb->prefix . 'nbdesigner_templates';
        $sql        = "SELECT * FROM {$wpdb->prefix}nbdesigner_templates ORDER BY created_date DESC";
        $data       = $wpdb->get_row( $sql );
        return $data;
    }
    public function calc_design_position( $design_width, $design_height, $area_width, $area_height ){
        $position = array(
            'left'      => 0,
            'top'       => 0,
            'width'     => $area_width,
            'height'    => $area_height,
            'ratio'     => 1
        );
        if( $area_width /  $area_height > $design_width / $design_height ){
            $ratio              = $area_height / $design_height;
            $new_width          = $design_width * $ratio;
            $position['left']   = ( $area_width - $new_width ) / 2;
            $position['width']  = $new_width;
            $position['ratio']  = $ratio;
        }else{
            $ratio              = $area_width / $design_width;
            $new_height         = $design_height * $ratio;
            $position['top']    = ( $area_height - $new_height ) / 2;
            $position['height'] = $new_height;
            $position['ratio']  = $ratio;
        }
        return $position;
    }
    private function create_side_preview( $setting, $content_designs ){
        $preview_width  = absint( apply_filters( 'nbdl_solid_design_preview_width', 500 ) );
        $scale          = $preview_width / 500;
        $folder         = substr( md5( uniqid() ), 0, 5 ).rand( 1,100 ).time();
        $path           = NBDESIGNER_CUSTOMER_DIR . '/' . $folder;
        $preview_path   = $path . '/preview';

        if( wp_mkdir_p( $path ) ){
            if( wp_mkdir_p( $preview_path ) ){
                foreach( $content_designs as $key => $design ){
                    if( isset( $setting[ $key ] ) ){
                        $side       = $setting[ $key ];
                        $bg         = is_numeric( $side['img_src'] ) ? get_attached_file( $side['img_src'] ) : $side['img_src'];
                        $overlay    = is_numeric( $side['img_overlay'] ) ? get_attached_file( $side['img_overlay'] ) : $side['img_overlay'];
                        $bg_width   = $side["img_src_width"] * $scale;
                        $bg_height  = $side["img_src_height"] * $scale;
                        $ds_width   = $side["area_design_width"] * $scale;
                        $ds_height  = $side["area_design_height"] * $scale;

                        $image = imagecreatetruecolor( $bg_width, $bg_height );
                        imagesavealpha( $image, true );
                        $color = imagecolorallocatealpha( $image, 255, 255, 255, 127 );
                        imagefill( $image, 0, 0, $color );

                        list( $width, $height ) = getimagesize( $design );
                        $position   = $this->calc_design_position( $width, $height, $ds_width, $ds_height );
                        $ds_ext     = strtolower( pathinfo( $design, PATHINFO_EXTENSION ) );
                        if( $ds_ext == 'png' ){
                            $image_design = NBD_Image::crop_and_resize_png_image( $design, $position['width'],  $position['height'] );
                        }else{
                            $image_design = NBD_Image::crop_and_resize_jpg_image( $design, $position['width'],  $position['height'] );
                        }
                        $ds_left    = ( $side["area_design_left"] - $side["img_src_left"] ) * $scale + $position['left'];
                        $ds_top     = ( $side["area_design_top"] - $side["img_src_top"] ) * $scale + $position['top'];

                        if( $side["bg_type"] == 'image'){
                            $bg_ext     = strtolower( pathinfo( $bg, PATHINFO_EXTENSION ) );
                            if( $bg_ext == 'png' ){
                                $image_product = NBD_Image::nbdesigner_resize_imagepng($bg, $bg_width, $bg_height);
                            }else{
                                $image_product = NBD_Image::nbdesigner_resize_imagejpg($bg, $bg_width, $bg_height);
                            }
                            imagecopy( $image, $image_product, 0, 0, 0, 0, $bg_width, $bg_height );
                        }elseif( $side["bg_type"] == 'color' ){
                            $_color = hex_code_to_rgb( $side["bg_color_value"] );
                            $color  = imagecolorallocate( $image, $_color[0], $_color[1], $_color[2] );
                            imagefilledrectangle( $image, 0, 0, $bg_width, $bg_height, $color );
                        }

                        imagecopy( $image, $image_design, $ds_left, $ds_top, 0, 0, $position['width'], $position['height'] );

                        if( $side["show_overlay"] == '1' ){
                            $overlay_ext     = pathinfo( $overlay, PATHINFO_EXTENSION );
                            if( $overlay_ext == "png" ){
                                $image_overlay = NBD_Image::nbdesigner_resize_imagepng( $overlay, $bg_width, $bg_height );
                            }else if($over_ext == "jpg" || $over_ext == "jpeg"){
                                $image_overlay = NBD_Image::nbdesigner_resize_imagejpg( $overlay, $bg_width, $bg_height );
                            }
                            imagecopy( $image, $image_overlay, 0, 0, 0, 0, $bg_width, $bg_height );
                        }

                        imagepng( $image, $preview_path. '/frame_' . $key . '.png');
                    }
                }
                return $folder;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    public function add_hook_change_image_id(){
        add_filter( 'woocommerce_product_get_image_id', array( $this, 'product_get_image_id' ), 10, 1 );
    }
    public function remove_hook_change_image_id(){
        remove_filter( 'woocommerce_product_get_image_id', array( $this, 'product_get_image_id' ), 10 );
    }
    public function product_get_image_id( $value ){
        if( $this->is_solid_design() ){
            $product_id     = get_the_ID();
            $design_code    = wc_clean( $_GET['design_id'] );
            $design_id      = nbd_decode_design_id( $design_code );
            $design         = nbd_get_design( $design_id, $product_id );
            if( is_array( $design ) && isset( $design['thumbnail'] ) ){
                return absint( $design['thumbnail'] );
            }
        }
        return $value;
    }
    public function nbdl_before_add_to_cart_button(){
        if( $this->is_solid_design() ){
            $design_code    = wc_clean( $_GET['design_id'] );
            $design_id      = nbd_decode_design_id( $design_code );
            ?>
            <input type="hidden" name="nbd_solid_design_id" value="<?php echo sanitize_text_field( $design_id ); ?>"/>
            <?php
        }
    }
    public function is_solid_design(){
        if( is_singular( 'product' ) ){
            if( isset( $_GET['design_id'] ) ){
                $design_code    = wc_clean( $_GET['design_id'] );
                $design_id      = nbd_decode_design_id( $design_code );
                if( $design_id ) return true;
            }
        }
        return false;
    }
    public function show_design_author(){
        $html = '';
        if( $this->is_solid_design() ){
            $product_id     = get_the_ID();
            $design_code    = wc_clean( $_GET['design_id'] );
            $design_id      = nbd_decode_design_id( $design_code );
            $design         = nbd_get_design( $design_id, $product_id );
            if( is_array( $design ) && isset( $design['user_id'] ) && absint( $design['user_id'] ) != 0 ){
                $designer   = new NBD_Designer( $design['user_id'] );
                $infos      = $designer->to_array();
                $name       = isset( $infos['artist_name'] ) && $infos['artist_name'] != '' ? $infos['artist_name'] : $infos['first_name'] . ' ' . $infos['last_name'];
                $store_url  = add_query_arg( array('id' => $design['user_id']), getUrlPageNBD( 'designer' ) );
                $html       = '<h2 class="nbdl-author">' . __( "Designed by", 'w2p-printshop' ) . ' <a href="' . $store_url . '" target="_blank">' . $name . '</a></h2>';
            }
        }
        echo $html;
    }
    public function add_cart_item_data( $cart_item_data ){
        $post_data = $_POST;
        if( isset( $post_data['nbd_solid_design_id'] ) ){
            $design = nbd_get_design( absint( $post_data['nbd_solid_design_id'] ) );
            if( is_array( $design ) && isset( $design['folder'] ) ){
                $cart_item_data['nbd_design_id'] = $design['folder'];
            }
        }
        return $cart_item_data;
    }
    public function nbo_artwork_action( $action, $field ){
        if( $this->is_solid_design() ){
            $action_val = 'n';
            foreach( $field['general']['attributes']["options"] as $k => $option ){
                if( $option['action'] == $action_val ){
                    $action = $k;
                }
            }
        }
        return $action;
    }
    public function hide_design_btn( $check ){
        if( $this->is_solid_design() ) return false;
        return $check;
    }
    public function nbo_field_class( $class, $field ){
        if( $this->is_solid_design() ){
            if( isset($field['nbe_type']) && $field['nbe_type'] == 'actions' && $field['general']['enabled'] == 'y' ){
                $class .= ' nbo-hidden';
            }
        }
        return $class;
    }
    public function cart_item_permalink( $permalink, $cart_item, $cart_item_key ){
        if( $permalink != '' && isset( $cart_item['nbd_design_id'] ) ){
            $design = nbd_get_design_by_folder( $cart_item['nbd_design_id'] );
            if( is_array( $design ) && isset( $design['type'] ) && $design['type'] == 'solid' ) {
                $permalink = add_query_arg(array(
                    'design_id' => nbd_encode_design_id( $design['id'] )
                ), $permalink);
            }
        }
        return $permalink;
    }
    public function cart_item_thumbnail( $image, $cart_item, $cart_item_key ){
        if( isset( $cart_item['nbd_design_id'] ) ){
            $design = nbd_get_design_by_folder( $cart_item['nbd_design_id'] );
            if( is_array( $design ) && isset( $design['type'] ) && $design['type'] == 'solid' && isset( $design['thumbnail'] ) ) {
                $thumbnail  = absint( $design['thumbnail'] );
                $image      = wp_get_attachment_image( $thumbnail, 'woocommerce_thumbnail', false );
            }
        }
        return $image;
    }
    public function order_item_permalink( $permalink, $item, $order ){
        if( $permalink != '' && isset( $item["item_meta"]['_nbd_design_id'] ) ){
            $design = nbd_get_design_by_folder( $item["item_meta"]['_nbd_design_id'] );
            if( is_array( $design ) && isset( $design['type'] ) && $design['type'] == 'solid' ) {
                $permalink = add_query_arg(array(
                    'design_id' => nbd_encode_design_id( $design['id'] )
                ), $permalink);
            }
        }
        return $permalink;
    }
    public function admin_order_item_thumbnail( $image = "", $item_id = "", $item = "" ){
        $order = nbd_get_order_object();
        $item_meta = function_exists( 'wc_get_order_item_meta' ) ? wc_get_order_item_meta( $item_id, '', FALSE ) : $order->get_item_meta( $item_id ); 
        if( isset( $item_meta['_nbd_design_id'] ) ){
            $design = nbd_get_design_by_folder( $item_meta['_nbd_design_id'][0] );
            if( is_array( $design ) && isset( $design['type'] ) && $design['type'] == 'solid' && isset( $design['thumbnail'] ) ) {
                $thumbnail  = absint( $design['thumbnail'] );
                $image      = wp_get_attachment_image( $thumbnail, 'woocommerce_thumbnail', false );
            }
        }
        return $image;
    }
    public function print_download_design_button( $item_id, $item, $product ){
        global $post;
        if( ! $item->is_type('line_item') ) return;
        $design_id = wc_get_order_item_meta( $item_id, '_nbd_design_id', true );
        if( $design_id ){
            $design = nbd_get_design_by_folder( $design_id );
            if( is_array( $design ) && isset( $design['type'] ) && $design['type'] == 'solid' && isset( $design['resource'] ) ) {
                $link_download_design = NBDESIGNER_CUSTOMER_URL . '/' . $design['resource'] . '/design.zip';
                $html = '<a class="button" download href="'. $link_download_design .'">'. __('Download design resource', 'web-to-print-online-designer') .'</a>';
                echo $html;
            }
        }
    }
    public function override_product_image_by_design_preview( $options, $product_id ){
        if( $this->is_solid_design() ){
            $design_code    = wc_clean( $_GET['design_id'] );
            $design_id      = nbd_decode_design_id( $design_code );

            foreach ($options['fields'] as $key => $field){
                if( isset( $field['nbd_type'] ) && $field['nbd_type'] == 'color' && $field['appearance']['change_image_product'] == 'y' ){
                    if( isset( $field['general']['attributes']['bg_type'] ) && $field['general']['attributes']['bg_type'] == 'c' ){
                        if( isset( $field['general']['attributes']['options'] ) && count( $field['general']['attributes']['options'] ) > 0 ){
                            foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                                $kolor          = str_replace( '#', '', $option['bg_color'] );
                                $preview_path   = NBDESIGNER_DATA_DIR . '/previews/' . $product_id . '/' . $design_id . '/0_' . $kolor . '.png';
                                if( file_exists( $preview_path ) ){
                                    $image_link = NBDESIGNER_DATA_URL . '/previews/' . $product_id . '/' . $design_id . '/0_' . $kolor . '.png';
                                    list( $width, $height ) = getimagesize( $preview_path );
                                    $options['fields'][$key]['general']['attributes']['options'][$op_index] = array_replace_recursive($options['fields'][$key]['general']['attributes']['options'][$op_index], array(
                                        'imagep'        => 'y',
                                        'image_link'    => $image_link,
                                        'image_title'   => __( 'Design', 'web-to-print-online-designer' ),
                                        'image_alt'     => __( 'Design', 'web-to-print-online-designer' ),
                                        'image_srcset'  => $image_link,
                                        'image_sizes'   => sprintf( '(max-width: %1$dpx) 100vw, %1$dpx', $width ),
                                        'image_caption' => '',
                                        'full_src'      => $image_link,
                                        'full_src_w'    => $width,
                                        'full_src_h'    => $height
                                    ));
                                }
                            }
                        }
                    }
                }
            }
        }
        return $options;
    }
    public function re_assign_templates( $user_id, $reassign ){
        global $wpdb;

        $args   = array(
            'role__in'   => array( 'administrator', 'shop_manager' ),
            'number'     => 10,
            'offset'     => 0,
            'orderby'    => 'registered',
            'order'      => 'ASC',
            'status'     => 'all',
            'featured'   => '',
            'meta_query' => array(),
        );
        
        $user_query     = new WP_User_Query( $args );
        $users          = $user_query->get_results();

        if( $users ){
            $new_user_id = $users[0]->ID;
            $wpdb->update( 
                $wpdb->prefix . 'nbdesigner_templates',
                array(
                    'user_id'   => $new_user_id
                ),
                array(
                    'user_id'   => $user_id
                )
            );
        }else{
            $wpdb->update( 
                $wpdb->prefix . 'nbdesigner_templates',
                array(
                    'publish'   => 0
                ),
                array(
                    'user_id'   => $user_id
                )
            );
        }
    }
    public function nbl_designers_func( $atts ){
        $atts = shortcode_atts( array(
            'number'    => 4
        ), $atts );

        $designers  = array();
        $_designers = nbdl_get_designers( array(
            'number'    => $atts['number'],
            'status'    => 'approved',
            'orderby'   => 'ID',
            'order'     => 'DESC',
            'featured'  => 'yes'
        ), true );

        foreach( $_designers['designers'] as $designer ){
            $designers[] = $designer->to_array();
        }

        ob_start();
        nbdesigner_get_template('launcher/featured-designers-shortcode.php', array(
            'designers' => $designers
        ));
        return ob_get_clean();
    }
    public function i18n(){
        return array(
            'actions'                       => __( 'Actions', 'web-to-print-online-designer' ),
            'add_note'                      => __( 'Add Note', 'web-to-print-online-designer' ),
            'all'                           => __( 'All', 'web-to-print-online-designer' ),
            'all_user'                      => __( 'All user', 'web-to-print-online-designer' ),
            'amount'                        => __( 'Amount', 'web-to-print-online-designer' ),
            'approved'                      => __( 'Approved', 'web-to-print-online-designer' ),
            'approve_request'               => __( 'Approve Request', 'web-to-print-online-designer' ),
            'auto_publish_new_design'       => __( 'Auto publish new design', 'web-to-print-online-designer' ),
            'artist_name'                   => __( 'Artist name', 'web-to-print-online-designer' ),
            'at_a_glance'                   => __( 'At a Glance', 'web-to-print-online-designer' ),
            'apply'                         => __( 'Apply', 'web-to-print-online-designer' ),
            'artist_name'                   => __( 'Artist name', 'web-to-print-online-designer' ),
            'address'                       => __( 'Address', 'web-to-print-online-designer' ),
            'awaiting_approval'             => __( 'awaiting approval', 'web-to-print-online-designer' ),
            'bulk_actions'                  => __( 'Bulk actions', 'web-to-print-online-designer' ),
            'cancel'                        => __( 'Cancel', 'web-to-print-online-designer' ),
            'cancel_request'                => __( 'Cancel request', 'web-to-print-online-designer' ),
            'cancelled'                     => __( 'Cancelled', 'web-to-print-online-designer' ),
            'combine'                       => __( 'Combine', 'web-to-print-online-designer' ),
            'combine_text'                  => __( '% + ', 'web-to-print-online-designer' ),
            'confirm_delete_withdraw'       => __( 'Do you want to delete this withdraw?', 'web-to-print-online-designer' ),
            'confirm_delete_design'         => __( 'Do you want to delete this design?', 'web-to-print-online-designer' ),
            'change_designer_avatar'        => __( 'Change Designer Avatar', 'web-to-print-online-designer' ),
            'change_banner'                 => __( 'Change banner', 'web-to-print-online-designer' ),
            'created_this_month'            => __( 'created this month', 'web-to-print-online-designer' ),
            'created_this_period'           => __( 'created this period', 'web-to-print-online-designer' ),
            'current_balance'               => __( 'Current balance', 'web-to-print-online-designer' ),
            'custom'                        => __( 'Custom', 'web-to-print-online-designer' ),
            'dashboard'                     => __( 'Dashboard', 'web-to-print-online-designer' ),
            'date'                          => __( 'Date', 'web-to-print-online-designer' ),
            'delete'                        => __( 'Delete', 'web-to-print-online-designer' ),
            'designer'                      => __( 'Designer', 'web-to-print-online-designer' ),
            'designers'                     => __( 'Designers', 'web-to-print-online-designer' ),
            'designs'                       => __( 'Designs', 'web-to-print-online-designer' ),
            'designer_commission_type'      => __( 'Designer Commission Type', 'web-to-print-online-designer' ),
            'designer_commission'           => __( 'Designer Commission', 'web-to-print-online-designer' ),
            'designer_launcher'             => __( 'Design Launcher', 'web-to-print-online-designer' ),
            'designs_sold'                  => __( 'Designs sold', 'web-to-print-online-designer' ),
            'disabled'                      => __( 'Disabled', 'web-to-print-online-designer' ),
            'download_resource'             => __( 'Download resource', 'web-to-print-online-designer' ),
            'email'                         => __( 'E-mail', 'web-to-print-online-designer' ),
            'edit'                          => __( 'Edit', 'web-to-print-online-designer' ),
            'editable'                      => __( 'Editable', 'web-to-print-online-designer' ),
            'enabled'                       => __( 'Enabled', 'web-to-print-online-designer' ),
            'enable_selling_designs'        => __( 'Enable Selling Designs', 'web-to-print-online-designer' ),
            'error_title'                   => __( 'Error!', 'web-to-print-online-designer' ),
            'flat'                          => __( 'Flat', 'web-to-print-online-designer' ),
            'featured'                      => __( 'Featured', 'web-to-print-online-designer' ),
            'filter'                        => __( 'Filter', 'web-to-print-online-designer' ),
            'flickr'                        => __( 'Flickr', 'web-to-print-online-designer' ),
            'facebook'                      => __( 'Facebook', 'web-to-print-online-designer' ),
            'filter_by_user'                => __( 'Filter by registered customer', 'web-to-print-online-designer' ),
            'filter_by_product'             => __( 'Filter by product', 'web-to-print-online-designer' ),
            'from'                          => __( 'From', 'web-to-print-online-designer' ),
            'item'                          => __( 'item', 'web-to-print-online-designer' ),
            'items'                         => __( 'items', 'web-to-print-online-designer' ),
            'instagram'                     => __( 'Instagram', 'web-to-print-online-designer' ),
            'last_month'                    => __( 'Last month', 'web-to-print-online-designer' ),
            'linkedin'                      => __( 'LinkedIn', 'web-to-print-online-designer' ),
            'make_mesigner_featured'        => __( 'Make Designer Featured', 'web-to-print-online-designer' ),
            'manage_designers'              => __( 'Manage Designers', 'web-to-print-online-designer' ),
            'message'                       => __( 'Message', 'web-to-print-online-designer' ),
            'no_name'                       => __( '( no name )', 'web-to-print-online-designer' ),
            'note'                          => __( 'Note', 'web-to-print-online-designer' ),
            'no_designer_found'             => __( 'No designer found.', 'web-to-print-online-designer' ),
            'no_transaction_found'          => __( 'No transaction found.', 'web-to-print-online-designer' ),
            'no_design_found'               => __( 'No design found.', 'web-to-print-online-designer' ),
            'of'                            => __( 'of', 'web-to-print-online-designer' ),
            'others'                        => __( 'Others', 'web-to-print-online-designer' ),
            'overview'                      => __( 'Overview', 'web-to-print-online-designer' ),
            'pending'                       => __( 'Pending', 'web-to-print-online-designer' ),
            'pending_request'               => __( 'Pending request', 'web-to-print-online-designer' ),
            'percentage'                    => __( 'Percentage', 'web-to-print-online-designer' ),
            'payment'                       => __( 'Payment', 'web-to-print-online-designer' ),
            'payment_info'                  => __( 'Payment information', 'web-to-print-online-designer' ),
            'paypal_email'                  => __( 'PayPal Email', 'web-to-print-online-designer' ),
            'phone_number'                  => __( 'Phone Number', 'web-to-print-online-designer' ),
            'publish'                       => __( 'Publish', 'web-to-print-online-designer' ),
            'publish_designs'               => __( 'Publish designs', 'web-to-print-online-designer' ),
            'preview'                       => __( 'Preview', 'web-to-print-online-designer' ),
            'private'                       => __( 'Private', 'web-to-print-online-designer' ),
            'product'                       => __( 'Product', 'web-to-print-online-designer' ),
            'registered'                    => __( 'Registered', 'web-to-print-online-designer' ),
            'revenue'                       => __( 'Revenue', 'web-to-print-online-designer' ),
            'registered_since'              => __( 'Registered since', 'web-to-print-online-designer' ),
            're_generate_preview'           => __( 'Re-generate design preview', 'web-to-print-online-designer' ),
            'save_changes'                  => __( 'Save Changes', 'web-to-print-online-designer' ),
            'select_image'                  => __( 'Select Image', 'web-to-print-online-designer' ),
            'select_crop_image'             => __( 'Select & Crop Image', 'web-to-print-online-designer' ),
            'select_bulk_action'            => __( 'Select bulk action', 'web-to-print-online-designer' ),
            'send_email'                    => __( 'Send Email', 'web-to-print-online-designer' ),
            'signup_this_month'             => __( 'signup this month', 'web-to-print-online-designer' ),
            'signup_this_period'            => __( 'signup this period', 'web-to-print-online-designer' ),
            'show'                          => __( 'Show', 'web-to-print-online-designer' ),
            'social_information'            => __( 'Social information', 'web-to-print-online-designer' ),
            'sold_this_month'               => __( 'sold this month', 'web-to-print-online-designer' ),
            'sold_this_period'              => __( 'sold this period', 'web-to-print-online-designer' ),
            'solid'                         => __( 'Solid', 'web-to-print-online-designer' ),
            'status'                        => __( 'Status', 'web-to-print-online-designer' ),
            'success_title'                 => __( 'Success!', 'web-to-print-online-designer' ),
            'subject'                       => __( 'Subject', 'web-to-print-online-designer' ),
            'this_month'                    => __( 'This month', 'web-to-print-online-designer' ),
            'to'                            => __( 'To', 'web-to-print-online-designer' ),
            'toggle_panel'                  => __( 'Toggle panel', 'web-to-print-online-designer' ),
            'total_designs'                 => __( 'Total designs', 'web-to-print-online-designer' ),
            'total_earning'                 => __( 'Total earning', 'web-to-print-online-designer' ),
            'twitter'                       => __( 'Twitter', 'web-to-print-online-designer' ),
            'upload_image'                  => __( 'Upload Image', 'web-to-print-online-designer' ),
            'update_note'                   => __( 'Update Note', 'web-to-print-online-designer' ),
            'view'                          => __( 'View', 'web-to-print-online-designer' ),
            'view_gallery'                  => __( 'View gallery', 'web-to-print-online-designer' ),
            'withdraw'                      => __( 'Withdraw', 'web-to-print-online-designer' ),
            'withdrawals'                   => __( 'Withdrawals', 'web-to-print-online-designer' ),
            'year'                          => __( 'Year', 'web-to-print-online-designer' ),
            'youtube'                       => __( 'Youtube', 'web-to-print-online-designer' )
        );
    }
}
function NBD_Design_Launcher(){
    return NBD_Design_Launcher::get_instance();
}
NBD_Design_Launcher()->init();