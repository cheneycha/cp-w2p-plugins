<?php 
if (!defined('ABSPATH')) exit;
class NBDesigner_Artwork_Actions {
    protected static $instance;
    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    public function __construct() {
        //todo
    }
    public function init(){
        if( nbdesigner_get_option( 'nbdesigner_show_popup_design_option', 'no' ) == 'yes' ){
            add_action( 'wp_footer', array( $this, 'show_catalog_popup' ) );
            add_filter( 'woocommerce_post_class', array( $this, 'woocommerce_post_class' ), 10, 2 );
        }
        if( nbdesigner_get_option( 'nbdesigner_button_hire_designer', 'no' ) == 'yes' ){
            add_filter( 'nbo_field_class', array( $this, 'nbo_field_class' ), 10, 2 );
            add_filter( 'nbo_artwork_action', array( $this, 'nbo_artwork_action' ), 10, 2 );
            add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'add_to_cart_text' ), 9998, 1 );
            add_action( 'woocommerce_after_cart_item_name', array( $this, 'add_request_design_action' ), 10, 2 );
        }
        add_filter( 'nbd_show_design_section_in_cart', array( $this, 'maybe_hide_cart_item_design_section' ), 10, 2 );
        $this->ajax();
    }
    public function ajax(){
        $ajax_events = array(
            'nbo_update_request_design' => true
        );
        foreach ($ajax_events as $ajax_event => $nopriv) {
            add_action('wp_ajax_' . $ajax_event, array($this, $ajax_event));
            if ($nopriv) {
                add_action('wp_ajax_nopriv_' . $ajax_event, array($this, $ajax_event));
            }
        }
    }
    public function show_catalog_popup(){
        //if( is_product_category() ){
            ob_start();
            nbdesigner_get_template( 'catalog-options-popup.php', array() );
            $content = ob_get_clean();
            echo $content;
        //}
    }
    public function woocommerce_post_class( $classes, $product ){
        global $nbd_fontend_printing_options;
        $product_id = $product->get_id();
        $artwork_action = get_transient( 'nbo_action_'.$product_id );
        if( false === $artwork_action ){
            if( is_nbdesigner_product( $product_id ) ){
                $option_id = $nbd_fontend_printing_options->get_product_option( $product_id );
                $is_artwork_action = false;
                if ($option_id) {
                    $_options = $nbd_fontend_printing_options->get_option( $option_id );
                    if ($_options) {
                        $options = unserialize($_options['fields']);
                        if (isset($options['fields'])) {
                            foreach ($options['fields'] as $key => $field) {
                                if ($field['general']['enabled'] == 'y' && isset($field['nbe_type']) && $field['nbe_type'] == 'actions') {
                                    $is_artwork_action = true;
                                }
                            }
                        }
                    }
                }
                if( $is_artwork_action ){
                    $classes[] = 'nbd-catalog-option';
                    set_transient( 'nbo_action_'.$product_id , '1' );
                }
            }
        } else {
            $classes[] = 'nbd-catalog-option';
        }
        return $classes;
    }
    public function nbo_field_class( $class, $field ){
        global $wp_query;
        if( isset( $wp_query->query_vars['request-design'] ) || isset( $wp_query->query_vars['upload-design'] ) ){
            if( isset($field['nbe_type']) && $field['nbe_type'] == 'actions' && $field['general']['enabled'] == 'y' ){
                $class .= ' nbo-hidden';
            }
        }
        return $class;
    }
    public function add_to_cart_text( $text ){
        global $wp_query;
        if( isset( $wp_query->query_vars['request-design'] ) || isset( $wp_query->query_vars['upload-design'] ) ){
            return esc_attr__( 'Submit', 'woocommerce' );
        }
        return $text;
    }
    public function nbo_artwork_action( $action, $field ){
        global $wp_query;
        if( isset( $wp_query->query_vars['request-design'] ) || isset( $wp_query->query_vars['upload-design'] ) ){
            $action_val = isset( $wp_query->query_vars['request-design'] ) ? 'h' : 'u';
            foreach( $field['general']['attributes']["options"] as $k => $option ){
                if( $option['action'] == $action_val ){
                    $action = $k;
                }
            }
        }
        return $action;
    }
    public function maybe_hide_cart_item_design_section( $_show_design, $cart_item ){
        if( isset( $cart_item['nbo_meta'] ) ){
            $options                = $cart_item['nbo_meta']['options'];
            if( nbd_is_base64_string( $options['fields'] ) ){
                $options['fields']  = base64_decode( $options['fields'] );
            }
            $option_fields          = unserialize( $options['fields'] );
            $is_artwork_action      = false;
            foreach ( $option_fields['fields'] as $key => $field ) {
                if ($field['general']['enabled'] == 'y' && isset($field['nbe_type']) && $field['nbe_type'] == 'actions') {
                    $is_artwork_action  = true;
                    $artwork_field      = $field;
                }
            }
            if( $is_artwork_action ){
                $fields = $cart_item['nbo_meta']['field'];
                if( isset( $artwork_field['general']['attributes']["options"] ) ){
                    $_val    = isset( $fields[ $artwork_field['id'] ] ) ? $fields[ $artwork_field['id'] ] : '';
                    $val = is_array( $_val ) ? $_val["value"] : $_val;
                    if( $val != '' && isset( $artwork_field['general']['attributes']["options"] ) ){
                        $action = $artwork_field['general']['attributes']["options"][ $val ]['action'];
                    }
                    if( $action == 'h' ){
                        $_show_design = false;
                    }
                }
            }
        }

        return $_show_design;
    }
    public function add_request_design_action( $cart_item, $cart_item_key ){
        if( isset( $cart_item['nbo_meta'] ) ){
            $options                = $cart_item['nbo_meta']['options'];
            if( nbd_is_base64_string( $options['fields'] ) ){
                $options['fields']  = base64_decode( $options['fields'] );
            }
            $option_fields          = unserialize( $options['fields'] );
            $is_artwork_action      = false;
            $request_val            = '';
            $no_request_val         = '';
            $design_request_val     = '';
            $upload_request_val     = '';
            foreach ( $option_fields['fields'] as $key => $field ) {
                if ($field['general']['enabled'] == 'y' && isset($field['nbe_type']) && $field['nbe_type'] == 'actions') {
                    $is_artwork_action = true;
                    $artwork_field = $field;
                    if( isset( $artwork_field['general']['attributes']["options"] ) ){
                        foreach( $artwork_field['general']['attributes']["options"] as $key => $option ){
                            if( $option['action'] == 'h' ) $request_val         = $key;
                            if( $option['action'] == 'n' ) $no_request_val      = $key;
                            if( $option['action'] == 'c' ) $design_request_val  = $key;
                            if( $option['action'] == 'u' ) $upload_request_val  = $key;
                        }
                    }
                }
            }
            if( isset( $cart_item['nbd_item_meta_ds'] ) ){
                if( isset( $cart_item['nbd_item_meta_ds']['nbd'] ) ){
                    $no_request_val = $design_request_val;
                }else if( isset( $cart_item['nbd_item_meta_ds']['nbu'] ) ){
                    $no_request_val = $upload_request_val;
                }
            }
            $html = '';
            if( $is_artwork_action ){
                $fields = $cart_item['nbo_meta']['field'];
                if( isset( $artwork_field['general']['attributes']["options"] ) ){
                    $val    = isset( $fields[ $artwork_field['id'] ] ) ? $fields[ $artwork_field['id'] ] : '';
                    $action = $val != '' ? $artwork_field['general']['attributes']["options"][ $val ]['action'] : '';
                    if( $request_val != '' ){
                        $product = $cart_item['data'];
                        $link = add_query_arg(
                            array(
                                'nbo_cart_item_key'  => $cart_item_key,
                            ), $product->get_permalink( $cart_item ) . 'request-design'
                        ); 
                        $html .= '<div class="nbdo-cart-item"><input '. checked( 'h', $action, false ) .' onchange="NBDESIGNERPRODUCT.update_request_design(this)" class="nbo_request_design_checkbox" data-field="'. $artwork_field['id'] .'" data-no-request="'. $no_request_val .'" data-request="'. $request_val .'" data-cart-item="'. $cart_item_key  .'" id="nbor-'. $cart_item_key  .'" type="checkbox" /><label for="nbor-'. $cart_item_key  .'">'. __('Design for me', 'web-to-print-online-designer') .'</label><br />';
                        if( $action == 'h' ) $html .= '<a class="nbo_request_design_link" href="'. $link .'">'. __('Request your design', 'web-to-print-online-designer') .'</a>';
                        $html .= '</div>';
                    }
                }
            }
            echo $html;
        }
    }
    public function nbo_update_request_design(){
        if ( !wp_verify_nonce($_POST['nonce'], 'save-design') && NBDESIGNER_ENABLE_NONCE ) {
            die('Security error');
        }
        $cart_item_key  = $_POST['cart_item_key'];
        $request_val    = $_POST['request_val'];
        $field_id       = $_POST['field_id'];
        if( isset( WC()->cart->cart_contents[ $cart_item_key ] ) && isset( WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta'] ) ){
            $nbd_field = WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta']['field'];
            if( $request_val != '' ){
                $nbd_field[ $field_id ] = $request_val;
            }else{
                unset( $nbd_field[ $field_id ] );
            }
            WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta']['field'] = $nbd_field;
            WC()->cart->calculate_totals();
        }
        wp_send_json( array('flag' => 1) );
    }
}
$nbd_artwork_actions = NBDesigner_Artwork_Actions::instance();
$nbd_artwork_actions->init();