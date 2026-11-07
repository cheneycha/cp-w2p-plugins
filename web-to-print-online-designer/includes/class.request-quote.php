<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

require_once(NBDESIGNER_PLUGIN_DIR . 'includes/quote/functions.php');
if(!class_exists('NBD_Request_Quote')) {
    class NBD_Request_Quote{
        protected $quote_updated = false;
        protected static $instance;
        private $args_message = array();
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        public function __construct(){
            $this->ajax();
            
            //request quote emails
            add_filter( 'woocommerce_email_classes', array( $this, 'add_woocommerce_emails' ) );
            add_action( 'woocommerce_init', array( $this, 'load_wc_mailer' ) );
            
            //request quote setting
            add_action( 'nbdesigner_include_settings', array( $this, 'include_settings' ) );
            add_filter('nbdesigner_settings_tabs', array( $this, 'settings_tabs' ), 10, 1 );
            add_filter('nbdesigner_settings_blocks', array( $this, 'settings_blocks' ), 10, 1 );
            add_filter('nbdesigner_settings_options', array( $this, 'settings_options' ), 10, 1 );
            add_filter('nbdesigner_default_settings', array( $this, 'default_settings' ), 10, 1 );
            add_filter('nbd_admin_pages', array( $this, 'admin_pages' ), 10, 1 );
            add_action( 'nbo_options_meta_box_panels', array( $this, 'quote_option_panel' ), 10, 1 );
            add_action( 'nbo_options_meta_box_tabs', array( $this, 'quote_option_tab' ), 10 );
            add_action('nbo_save_options', array($this, 'save_quote_option'), 10, 1);
            add_filter( 'display_post_states', array( $this, 'display_post_states' ), 10, 2 );
            
            //add NBD submenu
            add_action('nbd_menu', array($this, 'add_sub_menu'), 100);
            add_action('nbd_init_files_and_folders', array($this, 'create_quote_pdf_folder'));
            
            //request form builder
            add_action( 'admin_footer', array( $this, 'add_edit_fields_form' ), 30 );
            //enqueue scripts
            add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 30, 1);
                        
            //add essential pages
            add_action('nbd_create_pages', array($this, 'create_pages'));
            add_filter('nbdesigner_general_settings', array( $this, 'setting_quote_page' ), 10, 1 );
            
            //frontend request form
            add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue_scripts'));
            add_filter('script_loader_tag', array( $this, 'add_async_attribute'), 10, 2);
            add_action('woocommerce_before_single_product', array(&$this, 'print_get_quote_component'), 1);
            add_filter( 'woocommerce_form_field_nbdq_multiselect', array( $this, 'multiselect_type' ), 10, 4 );
            add_filter( 'woocommerce_form_field_nbdq_datepicker', array( $this, 'datepicker_type' ), 10, 4 );
            add_filter( 'woocommerce_form_field_nbdq_heading', array( $this, 'heading_type' ), 10, 4 );
            add_filter( 'woocommerce_form_field_nbdq_timepicker', array( $this, 'timepicker_type' ), 10, 4 );
            add_filter( 'woocommerce_form_field_nbdq_acceptance', array( $this, 'acceptance_type' ), 10, 4 );
            
            //frontend request effect
            add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'hide_add_to_cart_loop' ), 99, 2);
            add_action( 'nbdq_raq_message', array( $this, 'print_message' ), 10 );
            add_shortcode( 'nbdq_request_quote', array( $this, 'request_quote_page' ) );
            if( nbdesigner_get_option('nbdesigner_quote_hide_price', 'no') == 'yes' ){
                add_filter( 'woocommerce_get_price_html', array( $this, 'hide_product_price'), 10, 2 );
            }
            add_filter( 'nbd_show_edit_design_link_in_cart', array( $this, 'hide_edit_design_link' ), 20, 2 );
            add_filter( 'nbo_show_edit_option_link_in_cart', array( $this, 'hide_edit_design_link' ), 20, 2 );
            add_filter( 'nbd_show_edit_design_link_in_pay_for_order', array( $this, 'hide_edit_design_link_in_pay_for_order' ), 20, 4 );
            
            //quote order
            add_action( 'init', array( $this, 'register_order_status' ) );
            add_filter( 'wc_order_statuses', array( $this, 'add_custom_status_to_order_statuses' ) );
            add_filter( 'wc_order_is_editable', array( $this, 'order_is_editable' ), 10, 2 );
            //Update customer user for last quote order
            add_action( 'woocommerce_created_customer', array( $this, 'add_quote_order_to_new_customer' ), 10, 3 );
            //Metabox order quote setting
            add_action('add_meta_boxes', array($this, 'quote_order_metabox'), 30);
            add_action( 'woocommerce_before_order_object_save', array( $this, 'save_quote_data' ) );
            add_action( 'wp_insert_post', array( $this, 'raq_order_action' ), 100, 2 );
            
            //Quote list
            add_action( 'woocommerce_before_my_account', array( $this, 'my_quotes' ) );
            add_action( 'init', array( $this, 'add_endpoint' ) );
            add_action( 'nbd_installed', array( $this, 'add_endpoint' ) );
            add_action( 'template_redirect', array( $this, 'load_view_quote_page' ) );
            //Exclude quote orders in the customer order list
            add_filter( 'woocommerce_my_account_my_orders_query', array( $this, 'my_account_my_orders_query' ) );
            
            //process quote
            add_action( 'wp_loaded', array( $this, 'process_quote' ) );
            //check if a customer is paying a quote
            add_action( 'wp_loaded', array( $this, 'check_quote_in_cart' ) );
            add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_cart_fee' ) );
            add_action( 'woocommerce_cancelled_order', array( $this, 'empty_cart' ) );
            add_filter( 'nbo_need_change_cart_item_price', array( $this, 'prevent_change_product_price' ), 20, 2 );
            add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'set_new_product_price' ), 2, 2 );
            add_filter( 'woocommerce_add_cart_item', array($this, 'set_product_prices'), 2, 1 );
            //remove meta of quote after order processed
            add_action( 'woocommerce_checkout_order_processed', array( $this, 'raq_processed' ), 10, 2 );
            add_action('nbo_clear_cart', array($this, 'clear_session'));
            //add the cart_hash as post meta of order to process the same order
            add_filter( 'woocommerce_create_order', array( $this, 'set_cart_hash' ), 1 );
            if( nbdesigner_get_option('nbdesigner_quote_checkout_button', 'no') == 'yes' ){
                add_action( 'woocommerce_review_order_before_submit', array( $this, 'show_button_on_checkout') );
            }
            add_action( 'woocommerce_checkout_order_processed', array( $this, 'save_order_as_quote') );
            //pay for order
            add_filter( 'woocommerce_order_needs_payment', array( $this, 'set_quote_ready_for_pay_now' ), 10, 2 );
            add_filter( 'woocommerce_order_has_status', array( $this, 'set_quote_ready_for_pay_now' ), 10, 3 );
            add_filter( 'option_autoptimize_js_exclude', array( $this, 'autoptimize_js_exclude') );
        }
        public function ajax(){
            $ajax_events = array(
                'nbdq_save_raq_form'    => true,
                'nbdq_submit_raq_form'  => true
            );
            foreach ($ajax_events as $ajax_event => $nopriv) {
                add_action('wp_ajax_' . $ajax_event, array($this, $ajax_event));
                if ($nopriv) {
                    add_action('wp_ajax_nopriv_' . $ajax_event, array($this, $ajax_event));
                }
            }
        }
        public function autoptimize_js_exclude( $js ){
            if( false === strpos($js, 'quote.js') ) $js .= ', quote.js';
            return $js;
        }
        public function include_settings(){
            require_once(NBDESIGNER_PLUGIN_DIR . 'includes/settings/request-quote.php');
        }
        public function settings_tabs( $tabs ){
            $tabs['reques_qoute'] = '<span class="dashicons dashicons-cart"></span> '. esc_html__('Request Quote', 'web-to-print-online-designer');
            return $tabs;
        }
        public function settings_blocks( $blocks ){
            $blocks['reques_qoute'] = array(
                'general-qoute' => esc_html__('General', 'web-to-print-online-designer'),
                'request-form'  => esc_html__('Form', 'web-to-print-online-designer'),
                'pdf-quote'     => esc_html__('PDF quote', 'web-to-print-online-designer')
            );
            return $blocks;
        }
        public function settings_options( $options ){
            $request_options            = Nbdesigner_Request_quote::get_options();
            $options['general-qoute']   = $request_options['general'];
            $options['request-form']    = $request_options['request-form'];
            $options['pdf-quote']       = $request_options['pdf-quote'];
            return $options;
        }
        public function admin_pages( $pages ){
            $pages[] = 'nbdesigner_page_nbdesigner_get_quote';
            return $pages;
        }
        public function default_settings( $settings ){
            $settings['nbdesigner_quote_hide_add_to_cart']      = 'no';
            $settings['nbdesigner_quote_hide_price']            = 'no';
            $settings['nbdesigner_quote_hide_price_in_email']   = 'no';
            $settings['nbdesigner_quote_allow_out_of_stock']    = 'no';
            $settings['nbdesigner_quote_checkout_button']       = 'no';
            $settings['nbdesigner_quote_enable_registration']   = 'no';
            $settings['nbdesigner_enable_recaptcha_quote']      = 'no';
            $settings['nbdesigner_recaptcha_key']               = '';
            $settings['nbdesigner_recaptcha_secret_key']        = '';
            $settings['nbdesigner_quote_autocomplete_form']     = 'no';
            $settings['nbdesigner_quote_allow_download_pdf']    = 'no';
            $settings['nbdesigner_quote_attach_pdf']            = 'no';
            $settings['nbdesigner_quote_remove_list_in_email']  = 'no';
            $settings['nbdesigner_quote_pdf_logo']              = '';
            $settings['nbdesigner_quote_pdf_note']              = '';
            return $settings;
        }
        public function quote_option_panel( $post_id ){
            $_nbdq_enable = get_post_meta($post_id, '_nbdq_enable', true);
            ?>
            <div class="nbo_options_panel" id="nbrq-options" style="display: none;">
                <p class="nbo-form-field">
                    <label for="_nbdq_enable"><?php esc_html_e('Enable request quote', 'web-to-print-online-designer'); ?></label>
                    <span class="nbo-option-val">
                        <input type="hidden" value="0" name="_nbdq_enable"/>
                        <input type="checkbox" value="1" name="_nbdq_enable" id="_nbdq_enable" <?php checked($_nbdq_enable); ?> class="short" />
                    </span>
                </p>
            </div>
            <?php
        }
        public function quote_option_tab(){
            ?>
            <li><a href="#nbrq-options"><span class="dashicons dashicons-cart"></span> <?php esc_html_e('Request Quote', 'web-to-print-online-designer'); ?></a></li>
            <?php
        }
        public function save_quote_option( $post_id ){
            $enable = wc_clean( $_POST['_nbdq_enable'] ); 
            update_post_meta($post_id, '_nbdq_enable', $enable);
        }
        public function display_post_states( $post_states, $post ){
            if ( nbd_get_page_id( 'raq' ) === $post->ID ) {
                $post_states['nbd_raq_page'] = esc_html__( 'NBD Request a quote Page', 'web-to-print-online-designer' );
            }
            return $post_states;
        }
        public function load_wc_mailer() {
            add_action( 'send_raq_mail', array( 'WC_Emails', 'send_transactional_email' ), 10 );
            add_action( 'send_quote_mail', array( 'WC_Emails', 'send_transactional_email' ), 10 );
            add_action( 'change_raq_status_mail', array( 'WC_Emails', 'send_transactional_email' ), 10 );
        }
        public function add_woocommerce_emails( $emails ) {
            $emails['NBD_Send_Email_Request_Quote'] = include( NBDESIGNER_PLUGIN_DIR . 'includes/quote/emails/class.send-email-request-quote.php' );
            $emails['NBD_Quote_Status'] = include( NBDESIGNER_PLUGIN_DIR . 'includes/quote/emails/class.quote-status.php' );
            $emails['NBD_Send_Quote'] = include( NBDESIGNER_PLUGIN_DIR . 'includes/quote/emails/class.send-quote.php' );
            return $emails;
        }
        public function is_product_quote( $product_id ){
            $_nbdq_enable = get_post_meta($product_id, '_nbdq_enable', true);
            if($_nbdq_enable) return true;
            return false;
        }
        public function admin_enqueue_scripts( $hook ){
            if( $hook == 'nbdesigner_page_nbdesigner_get_quote' ){
                global $woocommerce;
                $woocommerce_version = function_exists( 'WC' ) ? WC()->version : $woocommerce->version;
                $css_libs = array(
                    'nbd-quote-admin' => array(
                        'link'      => NBDESIGNER_ASSETS_URL.'css/admin-quote.css',
                        'version'   => NBDESIGNER_VERSION,
                        'depends'   =>  array()
                    ),
                    'woocommerce_admin_styles' => array(
                        'link'      => $woocommerce->plugin_url() . '/assets/css/admin.css',
                        'version'   => $woocommerce_version,
                        'depends'   =>  array()
                    )
                );
                $js_libs = array(
                    'nbd-quote-admin'   => array(
                        'link'          => NBDESIGNER_ASSETS_URL .'js/admin-quote.js',
                        'version'       => NBDESIGNER_VERSION,
                        'depends'       => array('jquery', 'jquery-ui-dialog', 'jquery-ui-sortable'),
                        'in_footer'     => true
                    ),
                );
                foreach ($css_libs as $key => $css){
                    wp_register_style($key, $css['link'], $css['depends'], $css['version']);
                }
                foreach ($js_libs as $key => $js){
                    wp_register_script($key, $js['link'], $js['depends'], $js['version'],$js['in_footer']);
                }
                wp_enqueue_style( 'nbd-quote-admin');
                wp_enqueue_style( 'woocommerce_admin_styles');
                wp_enqueue_style( 'wp-jquery-ui-dialog');
                wp_enqueue_script('jquery-ui-dialog');
                wp_enqueue_script('jquery-ui-sortable');
                wp_enqueue_script('nbd-quote-admin');

                if ( ! wp_script_is( 'selectWoo' ) ) {
                    wp_enqueue_script( 'selectWoo' );
                    wp_enqueue_style( 'select2' );
                }
                wp_localize_script( 'nbd-quote-admin', 'nbdq_admin', array(
                    'popup_add_title'           => esc_html__( 'Add new field', 'web-to-print-online-designer' ),
                    'popup_edit_title'          => esc_html__( 'Edit field', 'web-to-print-online-designer' ),
                    'default_form_submit_label' => esc_html__( 'Set', 'web-to-print-online-designer' ),
                    'duplicate'                 => esc_html__( 'Duplicate field name', 'web-to-print-online-designer' ),
                    'enabled'                   => '<span class="status-enabled tips" data-tip="' . esc_html__( 'Yes', 'web-to-print-online-designer' ) . '"></span>',
                    'ajax_url'                  => admin_url( 'admin-ajax.php' ),
                    'nbdq_multiselect'          => esc_html__( 'Multi select', 'web-to-print-online-designer' ),
                    'nbdq_datepicker'           => esc_html__( 'Date', 'web-to-print-online-designer' ),
                    'nbdq_timepicker'           => esc_html__( 'Time', 'web-to-print-online-designer' ),
                    'nbdq_acceptance'           => esc_html__( 'Acceptance', 'web-to-print-online-designer' ),
                    'nbdq_heading'              => esc_html__( 'Heading', 'web-to-print-online-designer' )
                ) );
            }
        }
        public function add_sub_menu(){
            if(current_user_can('manage_nbd_tool')){
                add_submenu_page(
                    'nbdesigner', esc_html__('Get Quote', 'web-to-print-online-designer'), esc_html__('Get Quote', 'web-to-print-online-designer'), 'manage_nbd_tool', 'nbdesigner_get_quote', array($this, 'manageget_quote_form')
                );
            }
        }
        public function create_quote_pdf_folder(){
            Nbdesigner_IO::mkdir(NBDESIGNER_DATA_DIR . '/quotes');
        }
        public function manageget_quote_form(){
            if( isset( $_POST['field_name'] ) ){
                $this->nbdq_save_raq_form();
            }
            $fields             = $this->get_form_fields( true );
            $default_fields_key = nbdq_get_default_form_fields();
            include_once(NBDESIGNER_PLUGIN_DIR . 'views/quote/admin/default-form.php');
        }
        public function get_form_fields( $validate = false ){
            $fields = get_option( 'nbdesigner_raq_form', array() );
            if ( empty( $fields ) ) {
                $fields = nbdq_get_default_form_fields();
            }
            if( $validate ){
                $fields = $this->validate_form_fields_option( $fields );
            }
            return $fields;
        }
        public function validate_form_fields_option( $fields ){
            if ( empty( $fields ) ) {
                return array();
            }
            foreach( $fields as &$field ) {
                !isset($field['type']) && $field['type'] = 'text';
                !isset($field['label']) && $field['label'] = '';
                !isset($field['placeholder']) && $field['placeholder'] = '';
                $options = '';
                if (isset($field['options']) && is_array($field['options'])) {
                    foreach ($field['options'] as $key => $value) {
                        $key = urldecode($key);
                        $value = urldecode($value);
                        if (!$key && !$value) {
                            continue;
                        }
                        $options .= $key . '::' . $value;
                        if (key(array_slice($field['options'], -1, 1, TRUE)) != $key) {
                            $options .= '|';
                        }
                    }
                }
                $field['options'] = $options;
                if (isset($field['class']) && is_array($field['class'])) {
                    $positions = nbdq_get_array_positions_form_field();
                    foreach ($field['class'] as $key => $single_class) {
                        if (is_array($positions) && array_key_exists($single_class, $positions)) {
                            $field['position'] = $single_class;
                            unset($field['class'][$key]);
                            break;
                        }
                    }
                    $field['class'] = implode(',', $field['class']);
                }
                !isset($field['position']) && $field['position'] = '';
                $field['label_class'] = ( isset($field['label_class']) && is_array($field['label_class']) ) ? implode(',', $field['label_class']) : '';
                $field['validate'] = ( isset($field['validate']) && is_array($field['validate']) ) ? implode(',', $field['validate']) : '';
                $field['connect_to_field'] = ( isset($field['connect_to_field']) && $field['connect_to_field'] ) ? $field['connect_to_field'] : '';
                $field['required'] = (!isset($field['required']) || !$field['required'] ) ? '0' : '1';
                $field['clear'] = (!isset($field['clear']) || !$field['clear'] ) ? '0' : '1';
                $field['enabled'] = ( isset($field['enabled']) && !$field['enabled'] ) ? '0' : '1';
                $field['show_in_email'] = ( isset($field['show_in_email']) && !$field['show_in_email'] ) ? '0' : '1';
                $field['show_in_order'] = ( isset($field['show_in_order']) && !$field['show_in_order'] ) ? '0' : '1';
                $field['show_in_account'] = ( isset($field['show_in_account']) && !$field['show_in_account'] ) ? '0' : '1';
            }
            return $fields;
        }
        public function add_edit_fields_form(){
            if ( isset( $_GET['page'] ) && $_GET['page'] == 'nbdesigner_get_quote' ){
                $validation = array(
                    ''         => esc_html__( 'No validation', 'web-to-print-online-designer' ),
                    'phone'    => esc_html__( 'Phone', 'web-to-print-online-designer' ),
                    'email'    => esc_html__( 'Email', 'web-to-print-online-designer' ),
                );
                $field_types = nbdq_get_form_field_type();
                $connect_to_fields = nbdq_get_connect_fields();
                $positions = nbdq_get_array_positions_form_field();
                include_once( NBDESIGNER_PLUGIN_DIR . 'views/quote/admin/fields-edit.php' );
            }
        }
        public function nbdq_save_raq_form(){
            $names = isset( $_POST['field_name'] ) ? wc_clean( $_POST['field_name'] ) : array();
            if ( empty( $names ) ) {
                return;
            }
            $max        = max( array_map( 'absint', array_keys( $names ) ) );
            $new_fields = array();
            for ( $i = 0; $i <= $max; $i ++ ) {
                $name = wc_clean( stripslashes( $names[ $i ] ) );
                $name = str_replace( ' ', '_', $name );
                if ( ! empty( $_POST['field_deleted'][ $i ] ) ) {
                    $this->save_ordermeta( $name );
                    continue;
                }
                $new_fields[ $name ] = array();
                $new_fields[ $name ]['type']                      = ! empty( $_POST['field_type'][ $i ] ) ? sanitize_text_field( $_POST['field_type'][ $i ] ) : 'text';
                $new_fields[ $name ]['label']                     = ! empty( $_POST['field_label'][ $i ] ) ? stripslashes( $_POST['field_label'][ $i ] ) : '';
                $new_fields[ $name ]['placeholder']               = ! empty( $_POST['field_placeholder'][ $i ] ) ? stripslashes( $_POST['field_placeholder'][ $i ] ) : '';
                $new_fields[ $name ]['options']                   = ! empty( $_POST['field_options'][ $i ] ) ? $this->create_options_array( $_POST['field_options'][ $i ], $new_fields[ $name ]['type'] ) : array();
                $new_fields[ $name ]['class']                     = ! empty( $_POST['field_class'][ $i ] ) ? array_map( 'wc_clean', explode( ',', sanitize_text_field( $_POST['field_class'][ $i ]) ) ) : array();
                $new_fields[ $name ]['label_class']               = ! empty( $_POST['field_label_class'][ $i ] ) ? array_map( 'wc_clean', explode( ',', sanitize_text_field( $_POST['field_label_class'][ $i ] ) ) ) : '';
                $new_fields[ $name ]['validate']                  = ! empty( $_POST['field_validate'][ $i ] ) ? explode( ',', sanitize_text_field( $_POST['field_validate'][ $i ] ) ) : '';
                $new_fields[ $name ]['connect_to_field']          = ! empty( $_POST['field_connect_to_field'][ $i ] ) ? sanitize_text_field( $_POST['field_connect_to_field'][ $i ] ) : '';
                $new_fields[ $name ]['required']                  = ( ! empty( $_POST['field_required'][ $i ] ) && $new_fields[ $name ]['type'] != 'nbdq_heading' ) ? true : false;
                $new_fields[ $name ]['description']               = ( ! empty( $_POST['field_description'][ $i ] ) && $new_fields[ $name ]['type'] != 'nbdq_heading' ) ? sanitize_text_field( $_POST['field_description'][ $i ] ) : '';
                $new_fields[ $name ]['enabled']                   = ! empty( $_POST['field_enabled'][ $i ] ) ? true : false;
                $new_fields[ $name ] ['id']                       = ( ! empty( $_POST['field_id'][ $i ] ) && 'state' == $new_fields[ $name ]['type'] ) ? sanitize_text_field( $_POST['field_id'][ $i ] ) : $name;
                if ( ( $_POST['bulk_action'] || $_POST['bulk_action_bottom'] ) && isset( $_POST['select_field'][ $i ] ) ) {
                    $new_fields[ $name ]['enabled'] = ( $_POST['bulk_action'] == 'enable' || $_POST['bulk_action_bottom'] == 'enable' ) ? true : false;
                }
                if( $name == 'email' ) $new_fields[ $name ]['enabled'] = true;
                $new_fields[ $name ]['custom_attributes'] = array();
                if ( ! empty( $_POST['field_position'][ $i ] ) ) {
                    array_push( $new_fields[ $name ]['class'], sanitize_text_field( $_POST['field_position'][ $i ] ) );
                }
            }
            if ( ! empty( $new_fields ) ) {
                update_option( 'nbdesigner_raq_form', $new_fields );
            }
        }
        protected function create_options_array( $options, $type = '' ) {
            $options_array = array();
            $options = array_map( 'wc_clean', explode( '|', $options ) );
            $options = array_unique( $options );
            if ( $type == 'select' ) {
                $options_array[''] = '';
            }
            foreach ( $options as $option ) {
                $has_key = strpos( $option, '::' );
                if ( $has_key ) {
                    list( $key, $option ) = explode( '::', $option );
                } else {
                    $key = $option;
                }
                $key                   = sanitize_title_with_dashes( $key );
                $options_array[ $key ] = stripslashes( $option );
            }
            return $options_array;
        }
        protected function save_ordermeta( $field ) {
            global $wpdb;
            $query = $wpdb->prepare( "UPDATE $wpdb->postmeta SET meta_key = %s WHERE meta_key LIKE %s", $field, '_' . $field );
            $wpdb->query( $query );
        }
        public function create_pages(){
            $nbd_raq_page_id = nbd_get_page_id( 'raq' );
            if ( $nbd_raq_page_id == -1 || !get_post($nbd_raq_page_id) ){
                $post = array(
                    'post_name'         => 'raq',
                    'post_status'       => 'publish',
                    'post_title'        => esc_html__('Request a quote', 'web-to-print-online-designer'),
                    'post_type'         => 'page',
                    'post_author'       => 1,
                    'post_content'      => '[nbdq_request_quote]',
                    'comment_status'    => 'closed',
                    'post_date' => date('Y-m-d H:i:s')
                );
                $nbd_raq_page_id = wp_insert_post($post, false);	
                update_option( 'nbdesigner_raq_page_id', $nbd_raq_page_id );
            }
        }
        public function setting_quote_page( $settings ){
            $settings['nbd-pages'][] = array(
                'title'         => esc_html__( 'Request a quote page', 'web-to-print-online-designer'),
                'description'   => esc_html__( 'Choose request a quote page.', 'web-to-print-online-designer'),
                'id'            => 'nbdesigner_raq_page_id',
                'type'          => 'select',
                'default'       => nbd_get_page_id( 'raq' ),
                'options'       =>  nbd_get_pages()
            );
            return $settings;
        }
        public function frontend_enqueue_scripts(){
            $js_libs = array(
                'nbd-quote'     => array(
                    'link'      => NBDESIGNER_PLUGIN_URL . 'assets/js/quote.js',
                    'version'   => NBDESIGNER_VERSION,
                    'depends'   => array('jquery', 'jquery-ui-datepicker'),
                    'in_footer' => true
                ),
            );
            $css_libs = array(
                'nbd-quote'      => array(
                    'link'       => NBDESIGNER_ASSETS_URL.'css/quote.css',
                    'version'    => NBDESIGNER_VERSION,
                    'depends'    =>  array()
                ),
            );
            foreach ($js_libs as $key => $js){
                wp_register_script($key, $js['link'], $js['depends'], $js['version'],$js['in_footer']);
            }
            foreach ($css_libs as $key => $css){
                wp_register_style($key, $css['link'], $css['depends'], $css['version']);
            }
            if( is_singular( 'product' ) ){
                $product_id = get_the_ID();
                if( $this->is_product_quote( $product_id ) ){
                    $fields = $this->get_form_fields();
                    $need_country_js = false;
                    foreach( $fields as $field ){
                        if( $field['type'] == 'country' || $field['type'] == 'state' ) $need_country_js = true;
                    }
                    if( $need_country_js ){
                        $suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
                        $country_jss  = array(
                            'select2'     => array(
                                'src'     => WC()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js',
                                'deps'    => array( 'jquery' ),
                                'version' => '4.0.3'
                            ),
                            'selectWoo'   => array(
                                'src'     => WC()->plugin_url() . '/assets/js/selectWoo/selectWoo.full' . $suffix . '.js',
                                'deps'    => array( 'jquery' ),
                                'version' => '1.0.4'
                            ),
                            'wc-country-select'          => array(
                                'src'     => WC()->plugin_url() . '/assets/js/frontend/country-select' . $suffix . '.js',
                                'deps'    => array( 'jquery' ),
                                'version' => WC_VERSION
                            ),
                            'wc-address-i18n'            => array(
                                'src'     => WC()->plugin_url() . '/assets/js/frontend/address-i18n' . $suffix . '.js',
                                'deps'    => array( 'jquery', 'wc-country-select' ),
                                'version' => WC_VERSION
                            )
                        );
                        $scountry_csss = array(
                            'select2'                     => array(
                                'src'     => WC()->plugin_url() . '/assets/css/select2.css',
                                'deps'    => array(),
                                'version' => WC_VERSION
                            )
                        );
                        foreach ($country_jss as $key => $js){
                            if( ! wp_script_is( $key, 'registered' ) ){
                                wp_register_script($key, $js['src'], $js['deps'], $js['version']);
                            }
                            if( ! wp_script_is( $key, 'enqueued' ) ){
                                wp_enqueue_script( $key );
                            }
                        }
                        foreach ($scountry_csss as $key => $css){
                            if( ! wp_style_is( $key, 'registered' ) ){
                                wp_register_style($key, $css['src'], $css['deps'], $css['version']);
                            }
                            if( ! wp_style_is( $key, 'enqueued' ) ){
                                wp_enqueue_style( $key );
                            }
                        }
                    }
                    $form_localize_args = array(
                        'ajaxurl'                   => admin_url('admin-ajax.php'),
                        'err_msg'                   => esc_html__( 'This is a required field.', 'web-to-print-online-designer' ),
                        'err_msg_mail'              => esc_html__( 'The mail you have entered seems to be wrong.', 'web-to-print-online-designer' ),
                        'time_format'               => true,
                        'loading_img'               =>  NBDESIGNER_PLUGIN_URL . 'assets/images/loading.gif',
                        'show_popup'                => '1',
                    );
                    wp_enqueue_style( 'nbd-quote');
                    wp_enqueue_script('nbd-quote');
                    wp_localize_script( 'nbd-quote', 'nbdq_form_obj', $form_localize_args );
                    if( nbdesigner_get_option('nbdesigner_enable_recaptcha_quote', 'no') == 'yes' ){
                        wp_enqueue_script( 'nbdq_recaptcha', '//www.google.com/recaptcha/api.js?onload=nbdq_recaptcha&render=explicit', array('nbd-quote'));
                    }
                }
            }
        }
        public function add_async_attribute($tag, $handle) {
            if ( 'nbdq_recaptcha' !== $handle ) return $tag;
            return str_replace( ' src', ' async="async" defer="defer" src', $tag );
        }
        public function print_get_quote_component(){
            if( is_singular( 'product' ) ){
                global $product;
                if( $this->is_product_quote( $product->get_id() ) ){
                    if( $product->is_in_stock() && $product->get_price() !== '' ){
                        if( $product->is_type('variable')  ){
                            add_action( 'woocommerce_after_single_variation', array(  $this, 'quote_button' ),15 );
                        }else{
                            add_action( 'woocommerce_after_add_to_cart_button', array(  $this, 'quote_button' ),15 );
                        }
                    }else{
                        add_action( 'woocommerce_single_product_summary', array( $this, 'quote_button' ), 35 );
                    }
                    add_action('wp_footer', array($this, 'print_popup_get_quote'));
                    add_action('wp_footer', array($this, 'print_alert_quote'));
                }
            }
        }
        public function quote_button(){
            global $product;
            include_once(NBDESIGNER_PLUGIN_DIR . 'views/quote/frontend/quote-button.php');
        }
        public function print_popup_get_quote(){
            $fields = $this->get_form_fields();
            $enable_registration = nbdesigner_get_option('nbdesigner_quote_enable_registration', 'no');
            if( $enable_registration == 'yes' ){
                $checkout       = WC_Checkout::instance();
                $account_fields = $checkout->get_checkout_fields( 'account' );
            }
            include_once(NBDESIGNER_PLUGIN_DIR . 'views/quote/frontend/popup-wrap.php');
        }
        public function print_alert_quote(){
            include_once(NBDESIGNER_PLUGIN_DIR . 'views/quote/frontend/alert.php');
        }
        public function hide_add_to_cart_loop( $link , $product ) {
            if ( nbdesigner_get_option( 'nbdesigner_quote_hide_add_to_cart', 'no' ) == 'yes'){
                if( $this->is_product_quote( $product->get_id() ) ){
                    if( ! $product->is_type( array( 'external', 'grouped', 'variable' ) ) ) {
                        if( apply_filters( 'hide_add_to_cart_loop', true, $link, $product ) ) {
                            $link = '';
                        }
                    }
                }
            }
            return $link;
        }
        public function wrap_field( $content, $args ){
            $container_id = esc_attr($args['id']) . '_field';
            $container_class = !empty($args['class']) ? 'form-row ' . esc_attr(implode(' ', $args['class'])) : '';
            $after = !empty($args['clear']) ? '<div class="clear"></div>' : '';
            return '<p class="' . $container_class . '" id="' . $container_id . '">' . $content . '</p>' . $after;
        }
        public function multiselect_type($field, $key, $args, $value) {
            $required = $args['required'] ? ' <abbr class="required" title="' . esc_attr__('required', 'web-to-print-online-designer') . '">*</abbr>' : '';
            $value = is_string($value) ? explode(', ', $value) : $value;
            ob_start();
            ?>
            <label for="<?php esc_attr($args['id']) ?>" class="<?php echo esc_attr(implode(' ', $args['label_class'])) ?>"><?php echo esc_html($args['label']) . $required ?></label>
            <select name="<?php echo esc_attr($key) ?>[]" id="<?php echo esc_attr($args['id']) ?>" class="nbd-multiselect wc-enhanced-select" multiple="multiple" data-placeholder="<?php echo esc_attr($args['placeholder']) ?>">
            <?php foreach ($args['options'] as $key => $option) : ?>
                <option value="<?php echo( $key ); ?>" <?php echo in_array($key, $value) ? 'selected=selected' : ''; ?>><?php echo( $option ); ?></option>
            <?php endforeach; ?>
            </select>
            <?php
            $field = ob_get_clean();
            return $this->wrap_field($field, $args);
        }
        public function datepicker_type($field, $key, $args, $value){
            $required = $args['required'] ? ' <abbr class="required" title="' . esc_attr__('required', 'web-to-print-online-designer') . '">*</abbr>' : '';
            $format = 'mm/dd/yy';
            ob_start();
            ?>
            <label for="<?php esc_attr($args['id']) ?>" class="<?php echo esc_attr(implode(' ', $args['label_class'])) ?>"><?php echo esc_html($args['label']) . $required ?></label>
            <input name="<?php echo esc_attr($key) ?>" id="<?php echo esc_attr($args['id']) ?>" type="text" class="nbd-datepicker" value="<?php echo( $value ); ?>" placeholder="<?php echo esc_attr($args['placeholder']) ?>" data-format="<?php echo( $format ); ?>">
            <?php
            $field = ob_get_clean();
            return $this->wrap_field($field, $args);
        }
        public function timepicker_type($field, $key, $args, $value) {
            $required = $args['required'] ? ' <abbr class="required" title="' . esc_attr__('required', 'web-to-print-online-designer') . '">*</abbr>' : '';
            ob_start();
            ?>
            <label for="<?php esc_attr($args['id']) ?>" class="<?php echo esc_attr(implode(' ', $args['label_class'])) ?>"><?php echo esc_html($args['label']) . $required ?></label>
            <input name="<?php echo esc_attr($key) ?>" id="<?php echo esc_attr($args['id']) ?>" type="text" class="nbd-timepicker" value="<?php echo( $value ); ?>" placeholder="<?php echo esc_attr($args['placeholder']) ?>">
            <?php
            $field = ob_get_clean();
            return $this->wrap_field($field, $args);
        }
        public function heading_type( $field, $key, $args, $value ){
            $container_class = ! empty( $args['class'] ) ? 'form-row ' . esc_attr( implode( ' ', $args['class'] ) ) : '';
            $field = '<h3 class="'.$container_class. '">'. $args['label'].'</h3>';
            return $field;
        }
        public function acceptance_type( $field, $key, $args, $value ) {
            $required = $args['required'] ? ' <abbr class="required" title="' . esc_attr__( 'required', 'web-to-print-online-designer'  ) . '">*</abbr>' : '';
            ob_start();
            ?>
            <span class="nbdq_acceptance_description"><?php echo nbd_replace_policy_page_link_placeholders( $args['description'] ) ?></span>
            <input type="checkbox" name="<?php echo  esc_attr( $key ) ?>" id="<?php echo esc_attr( $args['id'] ) ?>" <?php echo  $args['required'] ? 'required': '' ?>>
            <label for="<?php echo esc_attr( $key ) ?>" class="nbdq_acceptance_label <?php echo esc_attr( implode( ' ', $args['label_class'] ) ) ?>">
            <?php echo esc_html( $args['label'] ) . $required ?></label>
            <?php
            $field = ob_get_clean();
            return $this->wrap_field( $field, $args ) ;
        }
        public static function get_form_value( $key, $field ){
            $value = '';
            if ( ! empty( $_POST[ $key ] ) ) {
                return wc_clean( $_POST[ $key ] );
            }else{
                if ( 'yes' == nbdesigner_get_option('nbdesigner_quote_autocomplete_form') ) {
                    $input = isset( $field['connect_to_field'] ) ? $field['connect_to_field'] : '';
                    if ( is_callable( array( WC()->customer, "get_$input" ) ) ) {
                        $value = WC()->customer->{"get_$input"}() ? WC()->customer->{"get_$input"}() : null;
                    } elseif ( WC()->customer->meta_exists( $input ) ) {
                        $value = WC()->customer->get_meta( $input, true );
                    }
                }
            }
            return $value;
        }
        public function validate_field( $posted, $key, $field ){
            $message = '';
            if( ( $field['required'] && $field['type'] != 'state' ) && ( ! isset( $posted[ $key ] ) || $posted[ $key ] == '' ) ){
                $message .= sprintf( esc_html__( '%s is required.', 'web-to-print-online-designer' ), '<strong>' . $field['label'] . '</strong>' );
            }
            if ( ! empty( $field['validate'] ) && is_array( $field['validate'] ) ) {
                foreach ( $field['validate'] as $rule ) {
                    switch ( $rule ) {
                        case 'email':
                            $email = strtolower( $posted[ $key ] );
                            if ( ! is_email( $email ) ) {
                                $message .= sprintf( esc_html__( ' %s is not a valid email address.', 'web-to-print-online-designer' ), '<strong>' . $field['label'] . '</strong>' );
                            }
                            break;
                        case 'phone' :
                            if ( ! WC_Validation::is_phone( $posted[ $key ] ) ) {
                                $message .= sprintf( esc_html__( ' %s is not a valid phone number.', 'web-to-print-online-designer' ), '<strong>' . $field['label'] . '</strong>' );
                            }
                            break;
                        default:
                            $message .= '';
                            break;
                    }
                }
            }
            return ltrim( $message );
        }
        private function get_customer_id( $posted, $enable_registration, $filled_form_fields ){
            $customer_id = 0;
            if ( is_user_logged_in() ) {
                $customer_id = get_current_user_id();
            } else{
                $current_customer = get_user_by( 'email', $posted['email'] );
                if( is_object( $current_customer ) ){
                    $customer_id = $current_customer->ID;
                }else{
                    if( $enable_registration && ! empty( $posted['createaccount'] ) ){
                        $username    = ! empty( $posted['account_username'] ) ? $posted['account_username'] : '';
                        $password    = ! empty( $posted['account_password'] ) ? $posted['account_password'] : '';
                        $customer_id = wc_create_new_customer( $posted['email'], $username, $password );
                        if ( is_wp_error( $customer_id ) ) {
                            throw new Exception( $customer_id->get_error_message() );
                        }
                        wp_set_current_user( $customer_id );
                        wc_set_customer_auth_cookie( $customer_id );
                        if( $customer_id ){
                            $customer = new WC_Customer( $customer_id );
                            $filled_form_fields_keys = array_keys( $filled_form_fields);
                            $search_index_key = array_search( 'billing_first_name', array_column( $filled_form_fields, 'connect_to_field' ) );
                            $billing_first_name = ( false !== $search_index_key ) ? $filled_form_fields[ $filled_form_fields_keys[ $search_index_key ] ]['value'] : '';
                            $search_index_key = array_search( 'billing_last_name', array_column( $filled_form_fields, 'connect_to_field' ) );
                            $billing_last_name = (false !== $search_index_key) ? $filled_form_fields[ $filled_form_fields_keys[ $search_index_key ] ]['value'] : '';
                            if ( ! empty( $billing_first_name ) ) {
                                $customer->set_first_name( $billing_first_name );
                            }
                            if ( ! empty( $billing_last_name ) ) {
                                $customer->set_last_name( $billing_last_name );
                            }
                            if ( is_email( $customer->get_display_name() ) ) {
                                $customer->set_display_name( $billing_first_name . ' ' . $billing_last_name );
                            }
                            foreach ( $filled_form_fields as $key => $value ) {
                                $connected = $value['connect_to_field'];
                                if ( is_callable( array( $customer, "set_{$connected}" ) ) ) {
                                    $customer->{"set_{$connected}"}( $value['value'] );
                                } elseif ( 0 === stripos( $connected, 'billing_' ) || 0 === stripos( $connected, 'shipping_' ) ) {
                                    $customer->update_meta_data( $connected, $value['value'] );
                                }
                            }
                        }
                        $customer->save();
                    }
                }
            }
            return $customer_id;
        }
        public function nbdq_submit_raq_form(){
            if ( ! isset( $_POST['nbdq_mail_wpnonce'] ) ) {
                return;
            }
            $posted = $_POST;
            $errors = array();
            $form_fields = $this->get_form_fields();
            $filled_form_fields = array();
            $quantity = isset($posted['quantity']) ? $posted['quantity'] : 1;
            $enable_registration = ( !is_user_logged_in() && nbdesigner_get_option('nbdesigner_quote_enable_registration') == 'yes' );
            /* Validate and get data */
            foreach ( $form_fields as $name => $form_field ) {
                if ( ! $form_field['enabled'] ) {
                    continue;
                }
                $filled_form_fields[ $name ] = array(
                    'id'               => $form_field['id'],
                    'type'             => $form_field['type'],
                    'label'            => $form_field['label'],
                    'connect_to_field' => isset( $form_field['connect_to_field'] ) ? $form_field['connect_to_field'] : '',
                    'value'            => ''
                );
                $error = $this->validate_field( $posted, $name, $form_field );
                if ( $error ) {
                    $errors[] = $error;
                } else {
                    $filled_form_fields[ $name ]['value'] = isset( $posted[ $name ] ) ? $posted[ $name ] : '';
                    if ( $form_field['type'] == 'country' ) {
                        $filled_form_fields['user_country'] = isset( $posted[ $name ] ) ? $posted[ $name ] : '';
                    }
                }
            }
            /* Validate recaptcha */
            if( nbdesigner_get_option('nbdesigner_enable_recaptcha_quote', 'no') == 'yes' && nbdesigner_get_option('nbdesigner_recaptcha_key', '') != '' && nbdesigner_get_option('nbdesigner_recaptcha_secret_key', '') != '' ){
                $captcha_error_string = sprintf( '<p>%s</p>', esc_html__( 'Please check the the captcha form.', 'web-to-print-online-designer' ) );
                if ( isset( $posted['g-recaptcha-response'] ) ) {
                    $captcha = $posted['g-recaptcha-response'];
                }
                if ( ! $captcha ) {
                    $errors[] = $captcha_error_string;
                } else {
                    $secretKey = nbdesigner_get_option('nbdesigner_recaptcha_secret_key');
                    $response  = wp_remote_get( "https://www.google.com/recaptcha/api/siteverify?secret=" . $secretKey . "&response=" . $captcha );
                    if ( is_wp_error( $response ) || ! isset( $response['body'] ) ) {
                        $errors[] = $captcha_error_string;
                    } else {
                        $responseKeys = json_decode( $response['body'], true );
                        if ( intval( $responseKeys["success"] ) !== 1 ) {
                            $errors[] = $captcha_error_string;
                        }
                    }
                }
            }
            if ( $errors ) {
                $results = array(
                    'result'   => 'failure',
                    'messages' => implode( ', ', $errors ),
                );
            }else{
                try{
                    $filled_form_fields['customer_id'] = $this->get_customer_id( $posted, $enable_registration, $filled_form_fields );
                    $username = esc_html__('Customer', 'web-to-print-online-designer');
                    if ( isset( $posted['first_name'] ) ) {
                        $username = $posted['first_name'];
                    }
                    if ( isset( $posted['last_name'] ) ) {
                        $username .= ' ' . $posted['last_name'];
                    }
                    $filled_form_fields['user_name']    = $username ? trim( $username ) : '';
                    $filled_form_fields['user_email']   = $posted['email'];
                    $filled_form_fields['user_message'] = isset( $posted['message']) ? $posted['message'] : '';
                    $order_id = $this->create_raq_order( $filled_form_fields, $posted );
                    if( $order_id != 0 ){
                        do_action('send_raq_mail', $filled_form_fields);
                        $results = array(
                            'result'   => 'success',
                            'messages' => esc_html__( 'Successfully!', 'web-to-print-online-designer' ),
                        );
                        if( is_user_logged_in() ){
                            $results['redirect'] = wc_get_endpoint_url( 'view-quote', $order_id, wc_get_page_permalink( 'myaccount' ) );
                        }
                    }else{
                        $results = array(
                            'result'   => 'failure',
                            'messages' => esc_html__( 'Unable to create the quote. Please try again.', 'web-to-print-online-designer' ),
                        );
                    }
                }catch( Exception $e  ){
                    $results = array(
                        'result'   => 'failure',
                        'messages' => $e->getMessage(),
                    );
                }
            }
            wp_send_json( $results );
            exit();
        }
        public function create_raq_order( $raq, $posted ){
            if ( class_exists( 'WC_Subscriptions_Coupon' ) ) {
                remove_filter( 'woocommerce_get_discounted_price', 'WC_Subscriptions_Coupon::apply_subscription_discount_before_tax', 10 );
                remove_filter( 'woocommerce_get_discounted_price', 'WC_Subscriptions_Coupon::apply_subscription_discount', 10 );
            }
            WC()->shipping();
            if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
                define( 'WOOCOMMERCE_CHECKOUT', true );
            }
            $order = wc_create_order( $args = array(
                'status'      => 'wc-nbdq-new',
                'customer_id' => $raq['customer_id']
            ));
            $order_id = 0;
            if( ! is_wp_error( $order ) ){
                $order_id = $order->get_id();
                $this->add_order_meta( $order, $raq );
                $current_cart = WC()->session->get( 'cart' );
                $new_cart = WC()->cart;
                if ( ! is_null( $new_cart ) && ! $new_cart->is_empty() ) {
                    $new_cart->empty_cart( true );
                }
                if( 'yes' == nbdesigner_get_option('nbdesigner_quote_allow_out_of_stock', 'no') ){
                    add_filter( 'woocommerce_variation_is_in_stock', '__return_true' );
                    add_filter( 'woocommerce_product_is_in_stock', '__return_true' );
                    add_filter( 'woocommerce_product_backorders_allowed', '__return_true' );
                }
                add_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 99 );

                //Add product to cart
                global $nbd_fontend_printing_options;
                $nbd_fontend_printing_options->nbo_ajax_cart( false );
                //Remove added notice
                wc_clear_notices();

                remove_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 99 );
                $new_cart->calculate_totals(); 
                foreach ( $new_cart->get_cart() as $cart_item_key => $values ) {
                    $args['variation'] = ( ! empty( $values['variation'] ) ) ? $values['variation'] : array();
                    if ( isset( $values['line_subtotal'] ) ) {
                        $args['totals']['subtotal'] = $values['line_subtotal'];
                    }
                    if ( isset( $values['line_total'] ) ) {
                        $args['totals']['total'] = $values['line_total'];
                    }
                    if ( isset( $values['line_subtotal_tax'] ) ) {
                        $args['totals']['subtotal_tax'] = $values['line_subtotal_tax'];
                    }
                    if ( isset( $values['line_tax'] ) ) {
                        $args['totals']['tax'] = $values['line_tax'];
                    }
                    if ( isset( $values['line_tax_data'] ) ) {
                        $args['totals']['tax_data'] = $values['line_tax_data'];
                    }
                    $values['quantity'] = ( $values['quantity'] <= 0 ) ? 1 : $values['quantity'];
                    $order_item_id = $order->add_product(
                        $values['data'],
                        $values['quantity'],
                        $args
                    );
                    //Add NBO data
                    if ( isset( $values['nbo_meta'] ) ) {
                        foreach ($values['nbo_meta']['option_price']['fields'] as $field) {
                            $price = floatval($field['price']) >= 0 ? '+' . wc_price($field['price']) : wc_price($field['price']);
                            if( isset($field['is_upload']) ){
                                if (strpos($field['val'], 'http') !== false) {
                                    $file_url = $field['val'];
                                }else{
                                    $file_url = Nbdesigner_IO::wp_convert_path_to_url( NBDESIGNER_UPLOAD_DIR . '/' .$field['val'] );
                                }
                                $field['value_name'] = '<a href="' . $file_url . '">' . $field['value_name'] . '</a>';
                            }
                            wc_add_order_item_meta($order_item_id, $field['name'], $field['value_name']. '&nbsp;&nbsp;' .$price);
                        }
                        wc_add_order_item_meta($order_item_id, esc_html__('Quantity Discount', 'web-to-print-online-designer'), '-' . wc_price($values['nbo_meta']['option_price']['discount_price']));
                        wc_add_order_item_meta($order_item_id, "_nbo_option_price", $values['nbo_meta']['option_price']);
                        wc_add_order_item_meta($order_item_id, "_nbo_field", $values['nbo_meta']['field']);
                        wc_add_order_item_meta($order_item_id, "_nbo_options", wp_slash( $values['nbo_meta']['options'] ));
                        wc_add_order_item_meta($order_item_id, "_nbo_original_price", $values['nbo_meta']['original_price']);
                    }
                    //Add NBD data
                    $item = apply_filters( 'woocommerce_checkout_create_order_line_item_object', new WC_Order_Item_Product(), $cart_item_key, $values, $order );
                    $item->legacy_values        = $values;
                    $item->legacy_cart_item_key = $cart_item_key;
                    do_action( 'woocommerce_new_order_item', $order_item_id, $item, $order_id );
                }
                // Trigger to add NBD data and unset NBD session
                do_action( 'nbd_checkout_order_processed', $order_id );
                if ( $new_cart->needs_shipping() ) {
                    $new_cart->calculate_shipping();
                }
                $order->save();
                $order->calculate_taxes();
                $order->calculate_totals();
                $new_cart->empty_cart( true );
                WC()->cart->set_session();
                WC()->session->set( 'cart', $current_cart );
                WC()->cart->get_cart_from_session();
                WC()->cart->set_session();
                WC()->session->set( 'raq_new_order', $order_id );
            }
            return $order_id;
        }
        public function add_order_meta( $order, $raq ){
            $attr                           = array();
            $order_id                       = $order->get_id();
            $attr['_raq_request']           = $raq;
            $attr['_raq_customer_name']     = $raq['user_name'];
            $attr['_raq_customer_email']    = $raq['user_email'];
            $attr['_raq_customer_message']  = $raq['user_message'];
            $attr['_raq_status']            = 'new';
            foreach ( $attr as $key => $item ) {
                $order->update_meta_data( $key, $item );
            }
            $order->save();
        }
        public function is_purchasable() {
            return true;
        }
        public function register_order_status() {
            register_post_status( 'wc-nbdq-new', array(
                'label'                     => esc_html__( 'New Quote Request', 'web-to-print-online-designer' ),
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( 'New Quote Request <span class="count">(%s)</span>', 'New Quote Requests <span class="count">(%s)</span>', 'web-to-print-online-designer' )
            ) );
            register_post_status( 'wc-nbdq-pending', array(
                'label'                     => esc_html__( 'Pending Quote', 'web-to-print-online-designer' ),
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( 'Pending Quote <span class="count">(%s)</span>', 'Pending Quote <span class="count">(%s)</span>', 'web-to-print-online-designer' )
            ) );
            register_post_status( 'wc-nbdq-expired', array(
                'label'                     => esc_html__( 'Expired Quote', 'web-to-print-online-designer' ),
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( 'Expired Quote <span class="count">(%s)</span>', 'Expired Quotes <span class="count">(%s)</span>', 'web-to-print-online-designer' )
            ) );

            register_post_status( 'wc-nbdq-accepted', array(
                'label'                     => esc_html__( 'Accepted Quote', 'web-to-print-online-designer' ),
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( 'Accepted Quote <span class="count">(%s)</span>', 'Accepted Quote <span class="count">(%s)</span>', 'web-to-print-online-designer' )
            ) );
            register_post_status( 'wc-nbdq-rejected', array(
                'label'                     => esc_html__( 'Rejected Quote', 'web-to-print-online-designer' ),
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( 'Rejected Quote <span class="count">(%s)</span>', 'Rejected Quote <span class="count">(%s)</span>', 'web-to-print-online-designer' )
            ) );
        }
        public function add_custom_status_to_order_statuses( $order_statuses_old ) {
            $order_statuses['wc-nbdq-new']      = esc_html__( 'New Quote Request', 'web-to-print-online-designer' );
            $order_statuses['wc-nbdq-pending']  = esc_html__( 'Pending Quote', 'web-to-print-online-designer' );
            $order_statuses['wc-nbdq-expired']  = esc_html__( 'Expired Quote', 'web-to-print-online-designer' );
            $order_statuses['wc-nbdq-accepted'] = esc_html__( 'Accepted Quote', 'web-to-print-online-designer' );
            $order_statuses['wc-nbdq-rejected'] = esc_html__( 'Rejected Quote', 'web-to-print-online-designer' );
            if ( isset( $_REQUEST['new_quote'] ) && $_REQUEST['new_quote'] && $_REQUEST['post_type'] == 'shop_order' ) {
                $new_status = array_merge( $order_statuses, $order_statuses_old );
            } else {
                $new_status = array_merge( $order_statuses_old, $order_statuses );
            }
            return $new_status;
        }
        public function get_quote_order_status() {
            return array(
                'wc-nbdq-new'      => esc_html__( 'New Quote Request', 'web-to-print-online-designer' ),
                'wc-nbdq-pending'  => esc_html__( 'Pending Quote', 'web-to-print-online-designer' ),
                'wc-nbdq-expired'  => esc_html__( 'Expired Quote', 'web-to-print-online-designer' ),
                'wc-nbdq-accepted' => esc_html__( 'Accepted Quote', 'web-to-print-online-designer' ),
                'wc-nbdq-rejected' => esc_html__( 'Rejected Quote', 'web-to-print-online-designer' )
            );
        }
        public function my_account_my_orders_query( $args ) {
            $args['status'] = array_keys( array_diff( wc_get_order_statuses(), $this->get_quote_order_status() ) );
            return $args;
        }
        public function order_is_editable( $editable, $order ) {
            $accepted_statuses = array('nbdq-new', 'nbdq-accepted', 'nbdq-pending', 'nbdq-expired', 'nbdq-rejected');
            if ( in_array( $order->get_status(), $accepted_statuses ) ) {
                return true;
            }
            return $editable;
        }
        public function add_quote_order_to_new_customer( $customer_id, $new_customer_data, $password_generated ){
            if( empty( $new_customer_data['user_email'] ) ){
                return;
            }
            global $wpdb;
            $query = "SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key = '_raq_customer_email' AND meta_value LIKE '%" . $new_customer_data['user_email'] . "%'";
            $ids = $wpdb->get_col( $query );
            if( empty( $ids ) ) {
                return;
            }
            foreach( $ids as $id ) {
                update_post_meta( $id, '_customer_user', $customer_id );
            }
        }
        public function is_quote( $order_id ) {
            $is_quote       = false;
            $order          = wc_get_order( $order_id );
            $raq_request    = $order->get_meta( '_raq_request' );
            if( $raq_request != '' ) $is_quote = true;
            return $is_quote;
        }
        public function is_expired( $order_id ) {
            $order = wc_get_order($order_id);
            if (!$order) {
                return false;
            }
            $current_status = $order->get_status();
            $ex_opt = $order->get_meta('_raq_expired');
            if ($current_status == 'nbdq-expired') {
                return true;
            }
            if ($ex_opt != '') {
                $expired_data = strtotime($ex_opt) + ( 24 * 60 * 60 ) - 1;
                if ($expired_data < time()) {
                    $order->update_status('nbdq-expired');
                    return true;
                }
            }
            return false;
        }
        public function quote_order_metabox(){
            $screen = class_exists('CustomOrdersTableController') && wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled()
            ? wc_get_page_screen_id( 'shop-order' )
            : 'shop_order';

            add_meta_box('quote_order', esc_html__('Request a Quote', 'web-to-print-online-designer'), array($this, 'quote_order_setting'), $screen, 'normal', 'default');
        }
        public function quote_order_setting($post_or_order_object){
            $order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
            $post = isset( $_REQUEST['post'] ) ? $_REQUEST['post'] : ( isset( $_REQUEST['post_ID'] ) ? $_REQUEST['post_ID'] : 0 );
            $order = wc_get_order( $post );
            $raq_request = $order->get_meta('_raq_request', true);
            if( $raq_request != '' ){
                $customer_name      = $order->get_meta('_raq_customer_name');
                $customer_message   = $order->get_meta('_raq_customer_message');
                $customer_email     = $order->get_meta('_raq_customer_email');
                $admin_message      = $order->get_meta('_raq_admin_message');
                $expired            = $order->get_meta('_raq_expired');
                $raq_pay            = $order->get_meta('_raq_pay');
                include_once( NBDESIGNER_PLUGIN_DIR . 'views/quote/admin/order-quote-meta-box.php' );
            }
        }
        public function save_quote_data( $order ){
            if( !isset( $_POST['nbdq'] ) ){
                return;
            }
            $datas = $_REQUEST['nbdq'];
            foreach( $datas as $key => $data ){
                $order->update_meta_data( $key, $data );
            }
        }
        public function raq_order_action($post_id, $post) {
            if ($this->quote_updated || !isset($_POST['nbdq']) || !isset($_POST['nbdq']['action']) || empty($_POST['nbdq']['action'])) {
                return;
            }
            $order = wc_get_order($post_id);
            if (!$order) {
                return;
            }
            $this->quote_updated = true;
            if( nbdesigner_get_option('nbdesigner_quote_allow_download_pdf', 'no') == 'yes' ){
                $this->create_pdf($post_id);
            }
            $order->update_status( 'nbdq-pending' );
            $order->update_meta_data('_raq_status', 'pending');
            do_action('send_quote_mail', $post_id);
        }
        public function create_pdf( $post_id ){
            $pdf        = nbdq_pdf();
            $creator    = get_bloginfo('name');
            $order      = wc_get_order($post_id);
            $title      = esc_html__( 'Quote #', 'web-to-print-online-designer' ) . $order->get_id();
            $font       = json_decode(nbd_get_default_font())->name;

            $path_font = nbd_download_google_font($font);
            $true_type = nbd_get_truetype_fonts();
            if (in_array($font, $true_type)) {
                foreach($path_font as $pfont){
                    $fontname = TCPDF_FONTS::addTTFfont($pfont, 'TrueType', '', 32);
                }
            }else{
                foreach($path_font as $pfont){
                    $fontname = TCPDF_FONTS::addTTFfont($pfont, '', '', 32);
                }
            }

            $pdf->SetCreator( $creator );
            $pdf->SetAuthor( $creator );
            $pdf->SetTitle( $title );
            $pdf->SetSubject( esc_html__( 'Proposal', 'web-to-print-online-designer' ) );
            $logo = nbdesigner_get_option('nbdesigner_quote_pdf_logo', 0);
            if( $logo != 0 ){
                $logo_path = get_attached_file( $logo );
                $pdf->SetHeaderData( $logo_path, 30, $title, get_option( 'blogdescription' ) );
            }
            $pdf->setHeaderFont(array($fontname, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(array($fontname, '', 8));
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->SetFont($fontname, '', 12, '', true);
            $pdf->AddPage();
            ob_start();
            nbdesigner_get_template( 'quote/pdf/content.php', array('order_id' => $post_id) );
            $html = ob_get_contents();
            ob_end_clean();
            $pdf->writeHTML($html, true, false, true, false, '');
            $output_file = NBDESIGNER_DATA_DIR .'/quotes/quote_'. $post_id .'.pdf';
            $pdf->Output($output_file, 'F');
        }
        public function hide_product_price( $price, $product ){
            if( $this->is_product_quote( $product->get_id() ) ){
                $price = '';
            }
            return $price;
        }
        public function my_quotes(){
            ob_start();
            nbdesigner_get_template( 'quote/frontend/quote-list.php', array() );
            $content = ob_get_clean();
            echo( $content );
        }
        public function add_endpoint() {
            $do_flush   = get_option( 'nbdq-flush-rewrite-rules', 1 );
            add_rewrite_endpoint( 'view-quote', EP_ROOT | EP_PAGES );
            if ( $do_flush ) {
                update_option( 'nbdq-flush-rewrite-rules', 0 );
                flush_rewrite_rules();
            }
        }
        public function load_view_quote_page() {
            global $wp, $post;
            $view_quote = 'view-quote';
            if ( ! is_page( wc_get_page_id( 'myaccount' ) ) || ! isset( $wp->query_vars[ $view_quote ] ) ) {
                return;
            }
            $order_id           = $wp->query_vars[ $view_quote ];
            $post->post_title   = sprintf( esc_html__( 'Quote #%s', 'web-to-print-online-designer' ), $order_id );
            $post->post_content = WC_Shortcodes::shortcode_wrapper( array( $this, 'view_quote' ) );
            remove_filter( 'the_content', 'wpautop' );
        }
        public function view_quote(){
            global $wp;
            if ( ! is_user_logged_in() ) {
                wc_get_template( 'myaccount/form-login.php' );
            } else {
                $order_id   = $wp->query_vars[ 'view-quote' ];
                nbdesigner_get_template( 'quote/frontend/quote-detail.php', array(
                    'order_id'     => $order_id,
                    'current_user' => get_user_by( 'id', get_current_user_id() )
                ) );
            }
        }
        public function process_quote(){
            if ( ! isset( $_REQUEST['quote_id'] ) || ! isset( $_REQUEST['action'] ) || ! isset( $_REQUEST['raq_nonce'] ) ) {
                return;
            }
            $action         = $_REQUEST['action'];
            $order_id       = $_REQUEST['quote_id'];
            $token          = $_REQUEST['raq_nonce'];
            $order          = wc_get_order( $order_id );
            if ( ! $order ) {
                return;
            }
            $email          = $order->get_meta('_raq_customer_email');
            $is_expired = $this->is_expired( $order_id );
            $args = array(
                'message' => '',
            );
            $current_status = $order->get_status();
            if( !nbdq_verify_token( $token, $action, $order_id, $email ) ){
                return;
            }
            if ( $action == 'accept' ){
                if ( isset( $_REQUEST['pay_for_order'] ) && $_REQUEST['pay_for_order'] && in_array( $current_status, array( 'nbdq-pending', 'pending', 'nbdq-accepted' ) ) ){
                    $raq_status = $order->get_meta( '_raq_status' );
                    if( $raq_status != 'accept' ){
                        do_action( 'change_raq_status_mail', array( 'order' => $order, 'status' => 'accepted' ) );
                        $order->update_meta_data('_raq_status', 'accept');
                        $order->save();
                    }
                    return;
                }
                if ( in_array( $current_status, array( 'nbdq-pending', 'pending', 'nbdq-accepted' ) ) ) {
                    $this->accept_order( $order_id );
                    $redirect = get_permalink( wc_get_page_id( 'checkout' ) );
                    wp_safe_redirect( $redirect );
                    exit;
                }else{
                    switch ( $current_status ) {
                        case 'nbdq-rejected':
                            $args['message'] = sprintf( esc_html__( 'Quote n. %d has been rejected and is not available', 'web-to-print-online-designere' ), $order_id );
                            break;
                        case 'nbdq-expired':
                            $args['message'] = sprintf( esc_html__( 'Quote n. %d has expired and is not available', 'web-to-print-online-designere' ), $order_id );
                            break;
                    }
                }
            }else if( $action == 'reject' ){
                if( $current_status == 'nbdq-rejected' && $action == 'reject' ) {
                    $args['message'] = sprintf( esc_html__( 'Quote n. %d has been rejected', 'web-to-print-online-designere' ), $order_id );
                } elseif ( $current_status == 'nbdq-expired' ){
                    $args['message'] = sprintf( esc_html__( 'Quote n. %d has expired and is not available', 'web-to-print-online-designere' ), $order_id );
                } elseif( $current_status != 'nbdq-pending' && $current_status != 'pending' ){
                    $args['message'] = sprintf( esc_html__( 'Quote n. %d can\'t be rejected because its status is: %s', 'web-to-print-online-designere' ), $order_id, $current_status );
                }else{
                    if( ! isset( $_REQUEST['raq_confirm'] ) && ! isset( $_REQUEST['confirm'] ) ){
                        $args = array(
                            'action'        => 'reject',
                            'raq_nonce'     => $token,
                            'quote_id'      => $order_id,
                            'raq_confirm'   => 'no'
                        );
                        $url = add_query_arg($args, getUrlPageNBD('raq'));
                        wp_safe_redirect( $url );
                        exit;
                    }else{
                        if( ! isset( $_REQUEST['confirm'] ) ) {
                            $args = array(
                                'action'    => 'reject',
                                'raq_nonce' => $token,
                                'quote_id'  => $order_id,
                                'confirm'   => 'no'
                            );
                        }else{
                            $this->reject_order( $order_id );
                            $args['message'] = sprintf( esc_html__( 'The quote n. %d has been rejected', 'web-to-print-online-designere' ), $order_id );
                        }
                    }
                }
            }
            $this->args_message = $args;
        }
        public function reject_order( $order_id ){
            $order = wc_get_order( $order_id );
            $order->update_meta_data( '_raq_status', 'reject' );
            if ( $order->get_status() == 'nbdq-rejected' ) {
                return;
            }
            $order->update_status( 'nbdq-rejected' );
            $args = array(
                'order'  => $order,
                'status' => 'rejected'
            );
            if ( isset( $_REQUEST['reason'] ) ) {
                $reason = wc_clean( $_REQUEST['reason'] );
                $order->set_customer_note( $reason );
                $args['reason'] = $reason;
            }
            do_action( 'change_raq_status_mail', $args );
            $order->save();
        }
        public function accept_order( $order_id ){
            if ( ! defined( 'WOOCOMMERCE_CHECKOUT' ) ) {
                define( 'WOOCOMMERCE_CHECKOUT', true );
            }
            // Clear current cart
            WC()->cart->empty_cart( true );
            WC()->cart->get_cart_from_session();
            WC()->session->set( 'order_awaiting_payment', $order_id );
            WC()->cart->set_session();
            // Load the previous order - Stop if the order does not exist
            $order = wc_get_order( $order_id );
            if ( ! $order ) {
                return;
            }
            $order->update_status( 'pending' );
            add_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 99 );
            $order_items = array();
            foreach ( $order->get_items() as $item ) {
                $product_id   = (int) apply_filters( 'woocommerce_add_to_cart_product_id', $item->get_product_id() );
                $order_items[] = $product_id;
                $quantity     = $item->get_quantity();
                $variation_id = (int) $item->get_variation_id();
                $variations   = array();
                foreach ( $item['item_meta'] as $meta_name => $meta_value ) {
                    if ( taxonomy_is_product_attribute( $meta_name ) ) {
                        $variations[ $meta_name ] = $meta_value;
                    } elseif ( is_array($meta_value) && isset($meta_value[0]) && meta_is_product_attribute( $meta_name, $meta_value[0], $product_id ) ) {
                        $variations[ $meta_name ] = $meta_value;
                    }
                }
                $cart_item_data = apply_filters( 'woocommerce_order_again_cart_item_data', array(), $item, $order );
                // Remove Add to cart validation
                remove_filter( 'woocommerce_add_to_cart_validation', array( $this, 'cart_validation' ), 10 );
                if ( ! apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations, $cart_item_data ) ) {
                    continue;
                }
                $meta_data = $item->get_meta_data();
                if ( ! empty( $meta_data ) ) {
                    foreach ( $meta_data as $meta ) {
                        $cart_item_data['meta'][] = $meta->get_data();
                    }
                }
                if ( $quantity ) {
                    if ( get_option( 'woocommerce_prices_include_tax', 'no' ) == 'yes' ) {
                        $price = ( $item['line_subtotal'] + $item['line_subtotal_tax'] ) / $quantity;
                    }else{
                        $price = $item['line_subtotal'] / $quantity;
                    }
                    $cart_item_data['raq']['price'] = $price;
                }
                WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations, $cart_item_data );
            }
            $fees = $order->get_fees();
            WC()->session->set( 'request_quote_fee', $fees );
            $tax_display    = $order->get_prices_include_tax( 'edit' );
            $order_discount = $order->get_total_discount( ! $tax_display );
            if ( $order_discount > 0 ) {
                $coupon = new WC_Coupon('QD_' . $order_id);
                if (version_compare(WC()->version, '3.2.0', '>=')) {
                    $wc_discounts = new WC_Discounts($order);
                    $valid = $wc_discounts->is_coupon_valid($coupon);
                    $valid = is_wp_error($valid) ? false : $valid;
                } else {
                    $valid = $coupon->is_valid();
                }
                if ($valid) {
                    $coupon->set_amount($order_discount);
                    $coupon->set_product_ids($order_items);
                } else {
                    $args = array(
                        'id'                => false,
                        'discount_type'     => 'fixed_cart',
                        'amount'            => $order_discount,
                        'individual_use'    => false,
                        'usage_limit'       => '1',
                        'product_ids'       => $order_items
                    );
                    $coupon->read_manual_coupon('QD_' . $order_id, $args);
                }
                $coupon->save();
                WC()->session->set('request_quote_discount', $coupon);
                WC()->cart->add_discount('QD_' . $order_id);
            }
            remove_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 99 );
            $raq_status = $order->get_meta( '_raq_status' );
            if( $raq_status != 'accept' ){
                do_action( 'change_raq_status_mail', array( 'order' => $order, 'status' => 'accepted' ) );
            }
            WC()->cart->calculate_totals();
            $order->update_meta_data('_raq_status', 'accept');
            $order->save();
        }
	public function set_quote_ready_for_pay_now( $response, $order, $status = '' ) {
            if ( nbd_is_true( $order->get_meta('_raq_pay') ) && isset( $_GET['pay_for_order'] ) &&
                in_array( $order->get_status(), array(
                    'nbdq-pending',
                    'pending',
                    'nbdq-accepted'
                ) ) ) {
                $response = true;
            }
            return $response;
	}
        public function prevent_change_product_price( $need, $cart_item ){
            if ( isset( $cart_item['raq'] )){
                $need = false;
            }
            return $need;
        }
        public function hide_edit_design_link( $need, $cart_item ){
            $order_id = $this->get_current_order_id();
            if( $order_id ){
                return false;
            }
            return $need;
        }
        public function hide_edit_design_link_in_pay_for_order( $need, $cart_item, $cart_item_id, $order ){
            if( isset( $_GET['pay_for_order'] ) ){
                if( $this->is_quote( $order->get_id() ) ){
                    return false;
                }
            }
            return $need;
        }
        public function set_new_product_price( $cart_item, $values ){
            if ( isset( $values['raq'] ) ) {
                $cart_item['raq'] = $values['raq'];
                $cart_item = $this->set_product_prices( $cart_item );
            }
            return $cart_item;
        }
        public function set_product_prices( $cart_item ){
            if ( isset( $cart_item['raq'] )){
                $new_price = (float) $cart_item['raq']['price'];
                $cart_item['data']->set_price( $new_price );
            }
            return $cart_item;
        }
        public function check_quote_in_cart(){
            $order = $this->get_current_order_id();
            if ( $order ) {
                add_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 99 );
                add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'cart_validation' ), 10, 2 );
                add_filter( 'woocommerce_update_cart_validation', array( $this, 'cart_update_validation' ), 10, 2 );
                add_filter( 'woocommerce_product_is_in_stock', '__return_true', 99 );
            } else {
                remove_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 99 );
                remove_filter( 'woocommerce_add_to_cart_validation', array( $this, 'cart_validation' ), 10 );
                remove_filter( 'woocommerce_update_cart_validation', array( $this, 'cart_update_validation' ), 10 );
                remove_filter( 'woocommerce_product_is_in_stock', '__return_true', 99 );
            }
        }
        public function get_current_order_id() {
            if( !is_callable( 'WC' ) || is_null( WC()->session ) ) {
                return 0;
            }
            $order_id = absint(WC()->session->get('order_awaiting_payment'));
            if ( $order_id && !$this->is_quote( $order_id ) ) {
                return false;
            }
            return $order_id;
        }
        /**
         * Disallow the add to cart when the order-quote is in the cart
         */
        public function cart_validation( $result, $product_id ){
            $order = $this->get_current_order_id();
            if ( $order ) {
                $result = false;
                wc_add_notice( esc_html__( 'It\'s not possible to add products to the cart since you have already accepted a quote.', 'web-to-print-online-designer' ), 'error' );
            }
            return $result;
        }
        /**
         * Disallow change the quantity in the cart when the order-quote is in the cart
         */
	public function cart_update_validation($result, $cart_item_key) {
            $order = $this->get_current_order_id();
            if ( $order ) {
                $result = false;
                wc_add_notice(esc_html__('It\'s not possible to add products to the cart since you have already accepted a quote.', 'web-to-print-online-designer'), 'error');
            }
            return $result;
        }
        /**
         * Add fee into cart after that the request was accepted
         */
	public function add_cart_fee() {
            $fees = WC()->session->get('request_quote_fee');
            if ($fees) {
                foreach ($fees as $fee) {
                    WC()->cart->add_fee($fee->get_name(), $fee->get_total(), (bool) $fee->get_tax_status(), $fee->get_tax_class());
                }
            }
        }
        public function empty_cart( $order_id ){
            if ( $this->is_quote( $order_id ) && ! is_admin() ) {
                WC()->cart->empty_cart();
                $order = wc_get_order( $order_id );
                if ( $order && $this->is_quote( $order_id ) ) {
                    $order->update_status( 'nbdq-accepted' );
                }
                $this->clear_session();
            }
        }
        public function raq_processed( $order_id ){
            $order = wc_get_order( $order_id );
            $order->delete_meta_data('_raq_status');
            $this->clear_session();
        }
        public function clear_session(){
            if( isset( WC()->session->request_quote_fee ) ) unset( WC()->session->request_quote_fee );
            if( isset( WC()->session->order_awaiting_payment ) ) unset( WC()->session->order_awaiting_payment );
        }
        public function set_cart_hash( $value ){
            $order_id = $this->get_current_order_id();
            $order    = wc_get_order( $order_id );
            if ( $order_id && $this->is_quote( $order_id ) ) {
                $cart_hash = md5( wp_json_encode( wc_clean( WC()->cart->get_cart_for_session() ) ) . WC()->cart->total );
                $order->set_cart_hash( $cart_hash );
                $order->save();
            }
            return $value;
        }
        public function print_message(){
            ob_start();
            nbdesigner_get_template( 'quote/frontend/quote-message.php', $this->args_message);
            echo ob_get_clean(); 
        }
        public function request_quote_page(){
            ob_start();
            nbdesigner_get_template( 'quote/frontend/quote-page.php', array());
            return ob_get_clean(); 
        }
        public function show_button_on_checkout() {
            $order_payment =  WC()->session->get( 'order_awaiting_payment' );
            if( $order_payment  ){
                return;
            }
            $label      = esc_html__('Request a Quote', 'web-to-print-online-designer');
            $button     = '<input type="hidden" id="nbrq_checkout_quote" name="nbrq_checkout_quote" value=""/>';
            $button    .= '<button type="submit" class="button alt nbrq_checkout_quote_btn" id="nbrq_checkout_quote_btn" data-value="'. $label .'" value="'. $label .'">' . $label . '</button>';
            echo( $button );
        }
        public function save_order_as_quote( $order_id ){
            if ( isset( $_REQUEST['nbrq_checkout_quote'] ) && $_REQUEST['nbrq_checkout_quote'] == '1' ) {
                $order = wc_get_order( $order_id );
                $raq = array(
                    'user_name'     => trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ),
                    'user_email'    => $order->get_billing_email(),
                    'user_message'  => $order->get_customer_note(),
                    'from_checkout' => 'yes'
                );
                $order->update_meta_data( '_raq_request', $raq );
                $order->update_meta_data( '_raq_customer_name', $raq['user_name'] );
                $order->update_meta_data( '_raq_customer_email', $raq['user_email'] );
                $order->update_meta_data( '_raq_customer_message', $raq['user_message'] );
                $order->update_meta_data( '_raq_from_checkout', 1 );
                $order->update_meta_data( '_raq_pay', 1 );
                $order->set_status( 'nbdq-new' );
                WC()->session->set( 'raq_new_order', $order_id);
                $order->save();
                do_action( 'send_raq_mail', $raq );
                WC()->cart->empty_cart(1);
                $order->add_order_note(esc_html__("This quote has been submitted from the checkout page.", 'web-to-print-online-designer') );
                $redirect = is_user_logged_in() ? wc_get_endpoint_url( 'view-quote', $order_id, wc_get_page_permalink( 'myaccount' ) ) : get_permalink( wc_get_page_id( 'shop' ) );
                if ( ! is_ajax() ) {
                    wp_safe_redirect(
                        apply_filters( 'woocommerce_checkout_no_payment_needed_redirect', $redirect, $order )
                    );
                    exit;
                }
                wp_send_json(
                    array(
                        'result'   => 'success',
                        'redirect' => apply_filters( 'woocommerce_checkout_no_payment_needed_redirect', $redirect, $order ),
                    )
                );
                exit;
            }
        }
    }
}
function NBD_Request_Quote(){
    return NBD_Request_Quote::get_instance();
}
NBD_Request_Quote();