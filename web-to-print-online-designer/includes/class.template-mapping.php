<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if(!class_exists('NBD_Template_Field_Mapping')) {
    class NBD_Template_Field_Mapping{
        protected static $instance;
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        public function __construct(){
            add_action( 'nbd_menu', array($this, 'add_sub_menu'), 90 );
            add_filter( 'nbd_admin_pages', array( $this, 'admin_pages' ), 20, 1 );
            add_filter( 'nbd_admin_hooks_need_asset', array( $this, 'admin_hook_need_asset' ), 20, 1 );
            add_filter( 'nbdesigner_design_tool_settings', array( $this, 'design_tool_setting' ), 20, 1 );
            add_filter( 'nbdesigner_default_frontend_settings', array( $this, 'default_settings' ), 20, 1 );
            add_action( 'nbd_js_config', array( $this, 'js_config' ) );
            add_action( 'nbd_modern_extra_popup', array( $this, 'template_fields_popup' ), 20, 2 );
        }
        public function admin_pages( $pages ){
            $pages[] = 'nbdesigner_page_nbd_template_mapping';
            return $pages;
        }
        public function admin_hook_need_asset( $hooks ){
            $hooks[] = 'nbdesigner_page_nbd_template_mapping';
            return $hooks;
        }
        public function add_sub_menu(){
            if(current_user_can('manage_nbd_tool')){
                add_submenu_page(
                    'nbdesigner', esc_html__('Template Field Mapping', 'web-to-print-online-designer'), esc_html__('Template Fields', 'web-to-print-online-designer'), 'manage_nbd_tool', 'nbd_template_mapping', array($this, 'template_mapping')
                );
            }
        }
        public function design_tool_setting( $settings ){
            $settings['misc'][] = array(
                'title'         => esc_html__( 'Enable template mapping', 'web-to-print-online-designer'),
                'id' 		=> 'nbdesigner_enable_template_mapping',
                'description'   => sprintf( wp_kses(
                                            __('Check this option if you want that the template fields( layers ) will be filled automatically. <a href="%s" target="_blank">Manage template fields mapping</a>', 'web-to-print-online-designer'),
                                            array( 'a' => array('href' => array(),'target' => array()) )), 
                                        esc_url(admin_url('admin.php?page=nbd_template_mapping')) ),
                'default'	=> 'yes',
                'type' 		=> 'radio',
                'options'       => array(
                    'yes'    => esc_html__('Yes', 'web-to-print-online-designer'),
                    'no'     => esc_html__('No', 'web-to-print-online-designer')
                ) 
            );
            $settings['misc'][] = array(
                'title'         => esc_html__( 'Enable vCard', 'web-to-print-online-designer'),
                'id' 		=> 'nbdesigner_enable_vcard',
                'description'   => '',
                'default'	=> 'yes',
                'type' 		=> 'radio',
                'options'       => array(
                    'yes'    => esc_html__('Yes', 'web-to-print-online-designer'),
                    'no'     => esc_html__('No', 'web-to-print-online-designer')
                ) 
            );
            return $settings;
        }
        public function default_settings( $settings ){
            $settings['nbdesigner_enable_template_mapping'] = 'yes';
            $settings['nbdesigner_enable_vcard']            = 'yes';
            return $settings;
        }
        public function template_mapping(){
            if( isset( $_POST['nbtn-admin-action'] ) && $_POST['nbtn-admin-action'] == 'fields-save' ){
                $names      = isset( $_POST['field_name'] ) ? $_POST['field_name'] : array();
                if ( empty( $names ) ) {
                    return;
                }
                $max        = max( array_map( 'absint', array_keys( $names ) ) );
                $new_fields = array();
                for ( $i = 0; $i <= $max; $i ++ ) {
                    $name = strtolower( wc_clean( stripslashes( $names[ $i ] ) ) );
                    $name = str_replace( ' ', '_', $name );
                    $name = $name != '' ? $name : time();
                    if( isset( $new_fields[ $name ] ) ) $name .= time();
                    $new_fields[ $name ]    = array(
                        'name'          =>  $_POST['field_name'][ $i ] != '' ? $_POST['field_name'][ $i ] : esc_html__('Field Name', 'web-to-print-online-designer'),
                        'connect_to'    =>  $_POST['field_connect_to'][ $i ]
                    );
                }
                if ( ! empty( $new_fields ) ) {
                    update_option( 'nbdesigner_template_mapping_fields', $new_fields );
                }
            } else if( isset( $_POST['nbtn-admin-action'] ) && $_POST['nbtn-admin-action'] == 'vcard-save' ) {
                $new_fields = $_POST;
                unset( $new_fields['nbtn-admin-action'] );
                update_option( 'nbdesigner_template_vcard_fields', $new_fields );
            }
            $fields              = get_option( 'nbdesigner_template_mapping_fields', array() );
            $connect_fields      = $this->get_connect_fields();
            $vcard_fields        = get_option( 'nbdesigner_template_vcard_fields', array() );
            $vcard_field_options = $this->get_vcard_field_options();
            include_once( NBDESIGNER_PLUGIN_DIR . 'views/template/field-mapping.php' );
        }
        public function get_vcard_field_options(){
            $vcard_field_options = array(
                'first_name' =>  array(
                    'label'     =>  esc_html__('First name', 'web-to-print-online-designer'),
                    'options'   => $this->get_vcard_first_name_option()
                ),
                'last_name' =>  array(
                    'label'     =>  esc_html__('Last name', 'web-to-print-online-designer'),
                    'options'   => $this->get_vcard_last_name_option()
                ),
                'address' =>  array(
                    'label'     =>  esc_html__('Address', 'web-to-print-online-designer'),
                    'options'   => $this->get_vcard_address_option()
                ),
                'city' =>  array(
                    'label'     =>  esc_html__('City', 'web-to-print-online-designer'),
                    'options'   => $this->get_vcard_city_option()
                ),
                'postcode' =>  array(
                    'label'     =>  esc_html__('Postcode', 'web-to-print-online-designer'),
                    'options'   => $this->get_vcard_postcode_option()
                ),
                'country' =>  array(
                    'label'     =>  esc_html__('Country', 'web-to-print-online-designer'),
                    'options'   => $this->get_vcard_country_option()
                ),
                'phone' =>  array(
                    'label'     =>  esc_html__('Phone', 'web-to-print-online-designer'),
                    'options'   => $this->get_vcard_phone_option()
                ),
                'email' =>  array(
                    'label'     =>  esc_html__('Email', 'web-to-print-online-designer'),
                    'options'   => $this->get_vcard_email_option()
                ),
                'website' =>  array(
                    'label'     =>  esc_html__('Website', 'web-to-print-online-designer'),
                    'options'   => $this->get_vcard_website_option()
                )
            );
            return apply_filters( 'nbd_template_vcard_field_options', $vcard_field_options );
        }
        public function get_connect_fields(){
            $fields             = array( 
                ''              => array(
                    "label" =>  esc_html__('Custom field', 'web-to-print-online-designer')
                ),
                'user_firstname'    =>  array(
                    "label" =>  esc_html__('Wordpress - First name', 'web-to-print-online-designer')
                ),
                'user_lastname'     =>  array(
                    "label" =>  esc_html__('Wordpress - Last name', 'web-to-print-online-designer')
                ),
                'user_fullname'     =>  array(
                    "label" =>  esc_html__('Wordpress - Full name', 'web-to-print-online-designer')
                ),
                'display_name'   =>  array(
                    "label" =>  esc_html__('Wordpress - Display name', 'web-to-print-online-designer')
                ),
                'user_email'          =>  array(
                    "label" =>  esc_html__('Wordpress - Email', 'web-to-print-online-designer')
                ),
                'user_url'            =>  array(
                    "label" =>  esc_html__('Wordpress - Website', 'web-to-print-online-designer')
                ),
                'user_description'    =>  array(
                    "label" =>  esc_html__('Wordpress - Biographical Info', 'web-to-print-online-designer')
                )       
            );
            $billing_fields     = WC()->countries->get_address_fields('', 'billing_');
            $billing_fields     = is_array( $billing_fields ) ? $billing_fields : array();
            foreach( $billing_fields as $key => $billing_field ){
                if( $key == 'billing_address_2' ){
                    $billing_fields[ $key ][ 'label' ] = esc_html__('Woocommerce Billing - Street address 2', 'web-to-print-online-designer');
                } else {
                    $billing_fields[ $key ][ 'label' ] = esc_html__('Woocommerce Billing - ', 'web-to-print-online-designer') . $billing_field['label'];
                }
            }
            $shipping_fields    = WC()->countries->get_address_fields('', 'shipping_');
            $shipping_fields    = is_array( $shipping_fields ) ? $shipping_fields : array();
            foreach( $shipping_fields as $key => $shipping_field ){
                if( $key == 'shipping_address_2' ){
                    $shipping_fields[ $key ][ 'label' ] = esc_html__('Woocommerce Shipping - Street address 2', 'web-to-print-online-designer');
                } else {
                    $shipping_fields[ $key ][ 'label' ] = esc_html__('Woocommerce Shipping - ', 'web-to-print-online-designer') . $shipping_field['label'];
                }
            }
            $fields             = array_merge($fields, $billing_fields, $shipping_fields);
            return apply_filters( 'nbd_template_mapping_fields', $fields );
        }
        public function get_template_field_values(){
            $fields      = get_option( 'nbdesigner_template_mapping_fields', array() );
            $new_fields  = array();
            $current_user   = wp_get_current_user();
            foreach ( $fields as $key => $field ) {
                $input = isset( $field['connect_to'] ) ? $field['connect_to'] : '';
                $value = '';
                if( $input != '' ){
                    if ( 0 != $current_user->ID ) {
                        if( false === strpos($input, 'billing_') && false === strpos($input, 'shipping_') ){
                            if( isset( $current_user->{"$input"} ) ){
                                $value = $current_user->{"$input"};
                            }else if( $input == 'user_fullname' ){
                                $value = $current_user->user_firstname . ' ' . $current_user->user_lastname;
                            }
                        } else {
                            if ( is_callable( array( WC()->customer, "get_$input" ) ) ) {                    
                                $value = WC()->customer->{"get_$input"}() ? WC()->customer->{"get_$input"}() : null;
                            } elseif ( WC()->customer->meta_exists( $input ) ) {
                                $value = WC()->customer->get_meta( $input, true );
                            }
                        }
                    }
                }
                $new_fields[]         = array_merge( $field, array(
                    'value' =>  is_null( $value ) ? '' : $value,
                    'key'   =>  $key
                ) );
            }
            return apply_filters( 'nbd_template_mapping_field_values', $new_fields );
        }
        public function get_vcard_first_name_option(){
            $fields = array(
                'user_firstname'    =>  array(
                    "label" =>  esc_html__('Wordpress - First name', 'web-to-print-online-designer')
                ),
                'billing_first_name'    =>  array(
                    "label" =>  esc_html__('Woocommerce Billing - First name', 'web-to-print-online-designer')
                ),
                'shipping_first_name'    =>  array(
                    "label" =>  esc_html__('Woocommerce Shipping - First name', 'web-to-print-online-designer')
                )
            );
            return apply_filters( 'nbd_template_vcard_first_name', $fields );
        }
        public function get_vcard_last_name_option(){
            $fields = array(
                'user_lastname'    =>  array(
                    "label" =>  esc_html__('Wordpress - Last name', 'web-to-print-online-designer')
                ),
                'billing_last_name'    =>  array(
                    "label" =>  esc_html__('Woocommerce Billing - Last name', 'web-to-print-online-designer')
                ),
                'shipping_last_name'    =>  array(
                    "label" =>  esc_html__('Woocommerce Shipping - Last name', 'web-to-print-online-designer')
                )
            );
            return apply_filters( 'nbd_template_vcard_last_name', $fields );
        }
        public function get_vcard_address_option(){
            $fields = array(
                'billing_address_1'    =>  array(
                    "label" =>  esc_html__('Woocommerce Billing - Address', 'web-to-print-online-designer')
                ),
                'shipping_address_1'    =>  array(
                    "label" =>  esc_html__('Woocommerce Shipping - Address', 'web-to-print-online-designer')
                )
            );
            return apply_filters( 'nbd_template_vcard_address', $fields );
        }
        public function get_vcard_city_option(){
            $fields = array(
                'billing_city'    =>  array(
                    "label" =>  esc_html__('Woocommerce Billing - City', 'web-to-print-online-designer')
                ),
                'shipping_city'    =>  array(
                    "label" =>  esc_html__('Woocommerce Shipping - City', 'web-to-print-online-designer')
                )
            );
            return apply_filters( 'nbd_template_vcard_city', $fields );
        }
        public function get_vcard_postcode_option(){
            $fields = array(
                'billing_postcode'    =>  array(
                    "label" =>  esc_html__('Woocommerce Billing - Postcode', 'web-to-print-online-designer')
                ),
                'shipping_postcode'    =>  array(
                    "label" =>  esc_html__('Woocommerce Shipping - Postcode', 'web-to-print-online-designer')
                )
            );
            return apply_filters( 'nbd_template_vcard_postcode', $fields );
        }
        public function get_vcard_country_option(){
            $fields = array(
                'billing_country'    =>  array(
                    "label" =>  esc_html__('Woocommerce Billing - Country', 'web-to-print-online-designer')
                ),
                'shipping_country'    =>  array(
                    "label" =>  esc_html__('Woocommerce Shipping - Country', 'web-to-print-online-designer')
                )
            );
            return apply_filters( 'nbd_template_vcard_country', $fields );
        }
        public function get_vcard_phone_option(){
            $fields = array(
                'billing_phone'    =>  array(
                    "label" =>  esc_html__('Woocommerce Billing - Phone', 'web-to-print-online-designer')
                )
            );
            return apply_filters( 'nbd_template_vcard_phone', $fields );
        }
        public function get_vcard_email_option(){
            $fields = array(
                'user_email'    =>  array(
                    "label" =>  esc_html__('Wordpress - Email', 'web-to-print-online-designer')
                ),
                'billing_email'    =>  array(
                    "label" =>  esc_html__('Woocommerce Billing - Email', 'web-to-print-online-designer')
                )
            );
            return apply_filters( 'nbd_template_vcard_email', $fields );
        }
        public function get_vcard_website_option(){
            $fields = array(
                'user_url'    =>  array(
                    "label" =>  esc_html__('Wordpress - Website', 'web-to-print-online-designer')
                )
            );
            return apply_filters( 'nbd_template_vcard_website', $fields );
        }
        public function get_vcard_values(){
            $fields              = get_option( 'nbdesigner_template_vcard_fields', array() );
            $vcard_field_options = $this->get_vcard_field_options();
            $new_fields          = array();
            $current_user        = wp_get_current_user();
            foreach ( $fields as $key => $input ) {
                $value = '';
                if( $input != '' ){
                    if ( 0 != $current_user->ID ) {
                        if( false === strpos($input, 'billing_') && false === strpos($input, 'shipping_') ){
                            if( isset( $current_user->{"$input"} ) ) $value = $current_user->{"$input"};
                        } else {
                            if ( is_callable( array( WC()->customer, "get_$input" ) ) ) {                    
                                $value = WC()->customer->{"get_$input"}() ? WC()->customer->{"get_$input"}() : null;
                            } elseif ( WC()->customer->meta_exists( $input ) ) {
                                $value = WC()->customer->get_meta( $input, true );
                            }
                        }
                    }
                }
                $new_fields[] = array(
                    'key'    => $key,
                    'name'   =>  $vcard_field_options[$key]['label'],
                    'value'  =>  $value
                );
            }
            return apply_filters( 'nbd_template_vcard_field_values', $new_fields );
        }
        public function js_config(){
            if( nbdesigner_get_option( 'nbdesigner_enable_template_mapping', 'yes' ) == 'yes' ){
                ?>
                NBDESIGNCONFIG.template_fields  = <?php echo json_encode( $this->get_template_field_values() ); ?>;
                <?php
            }
            if( nbdesigner_get_option( 'nbdesigner_enable_vcard', 'yes' ) == 'yes' ){
                ?>
                NBDESIGNCONFIG.vcard_fields     = <?php echo json_encode( $this->get_vcard_values() ); ?>;
                <?php
            }
        }
        public function template_fields_popup( $task, $settings ){
            if( $task == 'new' && $settings['nbdesigner_enable_template_mapping'] == 'yes' ){
                include_once(NBDESIGNER_PLUGIN_DIR . 'views/modern/popup-contents/template-fields.php');
            }
        }
    }
}
function NBD_Template_Field_Mapping(){
    return NBD_Template_Field_Mapping::get_instance();
}
NBD_Template_Field_Mapping();