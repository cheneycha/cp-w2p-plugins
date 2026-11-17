<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if( !class_exists( 'NBD_FRONTEND_PRINTING_OPTIONS' ) ){
    class NBD_FRONTEND_PRINTING_OPTIONS {
        protected static $instance;
        public $is_edit_mode = FALSE;
        /** Holds the cart key when editing a product in the cart **/
        public $cart_edit_key = NULL;
        /** Edit option in cart helper **/
        public $new_add_to_cart_key = FALSE;
        public function __construct() {
            if ( isset( $_REQUEST['nbo_cart_item_key'] ) && $_REQUEST['nbo_cart_item_key'] != '' ){
                $this->is_edit_mode = true;
                $this->cart_edit_key = $_REQUEST['nbo_cart_item_key'];
            }
        }
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        public function init(){
            add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'show_option_fields' ) );
            add_filter( 'nbd_js_object', array($this, 'nbd_js_object') );
            add_action('wp_enqueue_scripts', array($this, 'wp_enqueue_scripts'));
            
            /* Edit cart item */
            // handle customer input as order item meta
            add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 10, 2 );
            // Alters add to cart text when editing a product
            add_filter( 'woocommerce_product_single_add_to_cart_text', array( $this, 'add_to_cart_text' ), 9999, 1 );
            // Remove product from cart when editing a product
            add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'remove_previous_product_from_cart' ), 99999, 6 );
            // Alters the cart item key when editing a product
            add_action( 'woocommerce_add_to_cart', array( $this, 'add_to_cart' ), 10, 6 );
            // Change quantity value when editing a cart item
            add_filter( 'woocommerce_quantity_input_args', array( $this, 'quantity_input_args' ), 9999, 2 );
            // Redirect to cart when updating information for a cart item
            add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'add_to_cart_redirect' ), 9999, 1 );
            // Calculate totals on remove from cart/update
            //add_action( 'woocommerce_before_calculate_totals', array( $this, 'on_calculate_totals' ), 1, 1 );
            add_action( 'woocommerce_cart_loaded_from_session', array( $this, 're_calculate_price' ), 1, 1 );
            // Add meta to order
            add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'order_line_item' ), 50, 3 );
            // Change option independent quantity prices to cart fee
            add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_cart_fee' ), 1, 1 );
            // Gets saved option when using the order again function
            add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 'order_again_cart_item_data' ), 50, 3 );
                
            // Alter the product thumbnail in cart
            add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'cart_item_thumbnail' ), 50, 2 );
            // Remove item quantity in checkout
            add_filter( 'woocommerce_checkout_cart_item_quantity', array($this, 'remove_cart_item_quantity'), 10, 3);
            // Adds edit link on product title in cart and item quantity
            add_filter( 'woocommerce_cart_item_name', array( $this, 'cart_item_name' ), 50, 3 );
            
            // Add item data to the cart
            add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 10, 4 );
            
            // persist the cart item data, and set the item price (when needed) first, before any other plugins
            add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 1, 2 );

            // on add to cart set the price when needed, and do it first, before any other plugins
            add_filter( 'woocommerce_add_cart_item', array($this, 'set_product_prices'), 1, 1 );
            // Validate upon adding to cart
            add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 1, 6 );
            /** Force Select Options **/
            if( nbdesigner_get_option('nbdesigner_force_select_options', 'no') == 'yes' ){
                add_filter( 'woocommerce_product_add_to_cart_url', array( $this, 'add_to_cart_url' ), 50, 1 );
                //add_action( 'woocommerce_product_add_to_cart_text', array( $this, 'add_to_cart_text' ), 10, 1 );
            }
            add_filter( 'woocommerce_cart_redirect_after_error', array( $this, 'cart_redirect_after_error' ), 50, 2 );
            
            /* Disables persistent cart **/
            if( nbdesigner_get_option('nbdesigner_turn_off_persistent_cart', 'no') == 'yes' ){
                add_filter( 'get_user_metadata', array( $this, 'turn_off_persistent_cart' ), 10, 3 );
                add_filter( 'update_user_metadata', array( $this, 'turn_off_persistent_cart' ), 10, 3 );
                add_filter( 'add_user_metadata', array( $this, 'turn_off_persistent_cart' ), 10, 3 );
                add_filter( 'woocommerce_persistent_cart_enabled', '__return_false' );
            }
            
            /** Empty cart button **/
            if( nbdesigner_get_option('nbdesigner_enable_clear_cart_button', 'no') == 'yes' ){
                add_action( 'woocommerce_cart_actions', array( $this, 'add_empty_cart_button' ) );
                // check for empty-cart get param to clear the cart
                add_action( 'init', array( $this, 'clear_cart' ) );
            }
            
            /* Bulk order */
            if ( isset( $_POST['nbb-fields'] ) && ( ! defined('DOING_AJAX') || ! DOING_AJAX ) ) {
                add_action( 'wp_loaded', array( $this, 'bulk_order' ), 20 );
            }
            add_action( 'nbdq_bulk_order', array( $this, 'bulk_order' ), 20 );
            
            /* Quick view */
            add_action( 'woocommerce_api_nbo_quick_view', array( $this, 'quick_view' ) );
            add_action( 'woocommerce_before_variations_form', array( $this, 'action_woocommerce_before_variations_form'), 10, 0 ); 
            
            if( nbdesigner_get_option('nbdesigner_change_base_price_html', 'no') == 'yes' ){
                add_filter( 'woocommerce_get_price_html', array( $this, 'change_product_price_display'), 10, 2 );
            }
            /* Compatible Autoptimize */
            add_filter( 'option_autoptimize_js_exclude', array( $this, 'autoptimize_js_exclude') );
            
            /* AJAX */
            $this->ajax();
            if( nbdesigner_get_option('nbdesigner_enable_ajax_cart', 'no') == 'yes' ){
                add_action('wp_footer', array($this, 'print_popup_ajax_cart'));
                add_filter( 'woocommerce_loop_add_to_cart_link', array(&$this, 'add_to_cart_shop_link'), 20, 2 );
                add_filter( 'nbd_depend_js', array($this, 'nbd_depend_js') );
            }
            
            /* Printing information tab */
            add_filter( 'woocommerce_product_tabs', array( $this, 'printing_tab' ) );
            
            /* Show option in archives */
            if( nbdesigner_get_option('nbdesigner_show_options_in_archive_pages', 'no') == 'yes' ){
                add_action( 'woocommerce_after_shop_loop_item', array(&$this, 'show_options_in_archive_pages'), 10 );
            }

            /* Rich snippet price */
            if( nbdesigner_get_option('nbdesigner_enbale_rich_snippet_price', 'no') == 'yes' ){
                add_filter( 'woocommerce_structured_data_product_offer', array($this, 'update_rich_snippet_price'), 10, 2 );
            }

            if( nbdesigner_get_option('nbdesigner_enable_map_print_options', 'no') == 'yes' ){
                add_action( 'woocommerce_delete_product_transients', array($this, 'on_delete_product_transients'), 10, 1 );
                add_filter('woocommerce_dropdown_variation_attribute_options_args', array( $this, 'nbo_dropdown_variation_attribute_options_args' ), 20, 1 );
            }

            if( nbdesigner_get_option('nbdesigner_enable_favicon_badge', 'no') == 'yes' ){
                add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'add_to_cart_fragments' ) );
            }
        }
        public function ajax(){
            $ajax_events = array(
                'nbo_ajax_cart'                 => true,
                'nbo_get_product_variations'    => true
            );
            foreach ($ajax_events as $ajax_event => $nopriv) {
                add_action('wp_ajax_' . $ajax_event, array($this, $ajax_event));
                if ($nopriv) {
                    add_action('wp_ajax_nopriv_' . $ajax_event, array($this, $ajax_event));
                }
            }
        }
        public function add_to_cart_shop_link( $handler, $product ){
            $product_id = $product->get_id();
            $type       = $product->get_type();
            $need_qv    = false;
            if( $type != 'simple' && $type != 'variable' ){
                return $handler;
            }
            if( $type == 'variable' ){
                $need_qv    = true;
            }else{
                $option_id = $this->get_product_option($product_id);
                if($option_id){
                    $need_qv    = true;
                }
            }
            ob_start();
            nbdesigner_get_template( 'loop/ajax-add-to-cart-button.php', array( 'need_qv' => $need_qv, 'product' => $product, 'product_id' => $product_id ) );
            $button = ob_get_clean();
            return $button;
        }
        public function printing_tab( $tabs ){
            global $post;
            $nbpt_content = $this->get_printing_tab_content();
            if ( strlen( $nbpt_content ) > 0 ) {
                $nbpt_title         = get_post_meta($post->ID, '_nbpt_title', true);
                $tabs['size_chart'] = array(
                    'title'    => $nbpt_title,
                    'priority' => 50,
                    'callback' => array( $this, 'printing_tab_content' )
                );
            }
            return $tabs;
        }
        public function printing_tab_content(){
            echo $this->get_printing_tab_content();
        }
        public function get_printing_tab_content(){
            global $post;
            return htmlspecialchars_decode(get_post_meta( $post->ID, '_nbpt_content', true ));
        }
        public function print_popup_ajax_cart(){
            ob_start();
            nbdesigner_get_template( 'ajax-cart-alert.php', array() );
            nbdesigner_get_template( 'quick-view-popup.php', array() );
            $content = ob_get_clean();
            echo $content;
        }
        public function show_options_in_archive_pages(){
            global $product;
            $product_id = $product->get_id();
            $transient  = 'nbo_archive_options_' . $product_id;
            if( false === ( $archive_options = get_transient( $transient ) ) ){
                $option_id = $this->get_product_option( $product_id );
                if( $option_id ){
                    $_options = $this->get_option( $option_id );
                    $archive_options = array();
                    if( $_options ){
                        $options = unserialize( $_options['fields'] );
                        if( isset( $options['fields'] ) ){
                            $options['fields'] = $this->recursive_stripslashes( $options['fields'] );
                            foreach ( $options['fields'] as $key => $field ){
                                if( isset($field['general']['attributes']) ){
                                    if( isset( $field['appearance']['show_in_archives'] ) && $field['appearance']['show_in_archives'] == 'y'){
                                        $swatch = array();
                                        foreach ( $field['general']['attributes']['options'] as $op_index => $option ){
                                            $swatch[$op_index]          = array(
                                                'name'          =>  $option['name'],
                                                'preview_type'  =>  $option['preview_type']
                                            );
                                            $option['product_image']    = isset($option['product_image']) ? $option['product_image'] : 0;
                                            $attachment_id              = absint( $option['product_image'] );
                                            if( $attachment_id != 0 ){
                                                $swatch[$op_index]['srcset']    = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $attachment_id, 'shop_catalog' ) : FALSE;
                                                $swatch[$op_index]['src']       = wp_get_attachment_url($attachment_id);
                                            }
                                            if( $field['appearance']['display_type'] == 's' ){
                                                $swatch[$op_index]['display_type'] = 's';
                                                if( $option['preview_type'] == 'i' ){
                                                    $swatch[$op_index]['preview'] = nbd_get_image_thumbnail( $option['image'] );
                                                }else{
                                                    $swatch[$op_index]['color'] = $option['color'];
                                                    if( isset( $option['color2'] ) ) $swatch[$op_index]['color2'] = $option['color2'];
                                                }
                                            }else{
                                                $swatch[$op_index]['display_type'] = 'l';
                                                $swatch[$op_index]['des'] = $option['des'];
                                            }
                                        }
                                        $archive_options[$key] = $swatch;
                                    }
                                }
                            }
                        }
                    }
                }
                if( $archive_options != false ) set_transient( $transient, $archive_options );
            }
            if( is_array( $archive_options ) && count( $archive_options ) > 0 ){
                ob_start();            
                nbdesigner_get_template( 'loop/swatches.php', array( 'archive_options' => $archive_options ) );
                $html = ob_get_clean();
                echo $html;
            }
        }
        public function action_woocommerce_before_variations_form(){
            if( isset($_REQUEST['wc-api']) && $_REQUEST['wc-api'] == 'NBO_Quick_View'){
                $nbd_qv_type = nbdesigner_get_option('nbdesigner_display_product_option');
                if( !( isset($_REQUEST['mode']) && $_REQUEST['mode'] == 'catalog' ) ){
                    if( $nbd_qv_type == '2' ) echo '<div class="nbo-wc-options">'. __('Options', 'web-to-print-online-designer') .'</div>';
                }
            }
        }
        public function autoptimize_js_exclude( $js ){
            if( false === strpos($js, 'angular') ) $js .= ', angular';
            return $js;
        }
        public function change_product_price_display( $price, $product ){
            $option_id = $this->get_product_option($product->get_id());
            $class = $product->get_type() == 'simple' ? 'nbo-base-price-html' : 'nbo-base-price-html-var';
            if( $option_id ){
                $price = '<span class="'. $class .'">'. __('From', 'web-to-print-online-designer') .'</span> ' . $price;
            }
            return $price;
        }
        public function add_empty_cart_button(){
            echo '<input type="submit" class="nbo-clear-cart-button button" name="nbo_empty_cart" value="' . __('Empty cart', 'web-to-print-online-designer') . '" />';
        }
        public function clear_cart(){
            if ( isset( $_POST['nbo_empty_cart'] ) ) {
                if ( !isset( WC()->cart ) || WC()->cart == '' ) {
                    WC()->cart = new WC_Cart();
                }
                WC()->cart->empty_cart( TRUE );
                do_action('nbo_clear_cart');
            }
        }
        public function turn_off_persistent_cart( $value, $id, $key ){
            $blog_id = get_current_blog_id();
            if ($key == '_woocommerce_persistent_cart' || $key == '_woocommerce_persistent_cart_' . $blog_id) {
                return FALSE;
            }
            return $value;
        }
        public function cart_redirect_after_error( $url = "", $product_id="" ){
            $option_id = $this->get_product_option($product_id);
            if($option_id){
                $url = get_permalink( $product_id );
            }
            return $url;
        }
        public function catalog_add_to_cart_text( $text = "" ){
            return $text;
        }
        public function add_to_cart_url( $url = "" ){
            global $product;
            if(!is_product() && is_object( $product ) && property_exists( $product, 'id' )){
                $product_id = $product->get_id();
                $option_id = $this->get_product_option($product_id);
                if($option_id){
                    $url = get_permalink( $product_id );
                }
            }
            return $url;
        }
        public function add_to_cart_validation( $passed, $product_id, $qty, $variation_id = '', $variations = array(), $cart_item_data = array() ){
            // Remove session design if the customer unselect all sides/pages
            $this->remove_session_design( $product_id, $variation_id );
            if( is_ajax() && !isset($_REQUEST['nbo-add-to-cart']) ){
                $option_id = $this->get_product_option($product_id);
                if($option_id){
                    $_options = $this->get_option($option_id);
                    if($_options){
                        $options = unserialize($_options['fields']);
                        $valid_fields = $this->get_default_option($options);
                        $required_option = false;
                        foreach($valid_fields as $field){
                            if( $field['enable'] && $field['required'] ){
                                $required_option = true;
                                wc_add_notice( sprintf( __( '"%s" is a required field.', 'web-to-print-online-designer' ), $field['title'] ), 'error' );
                            }
                        }
                        if( $required_option ){
                            return FALSE;
                        }
                    }
                }
            }else{
                // Try to validate uploads before they happen
                $option_id = $this->get_product_option($product_id);
                if($option_id){
                    $_options = $this->get_option($option_id);
                    if($_options){
                        $options = unserialize($_options['fields']);
                        $valid_fields = $this->get_default_option($options);
                        $required_upload = false;
                        foreach($valid_fields as $field_id => $field){
                            if( $field['is_upload'] ){
                                if( !empty($_FILES) && isset($_FILES["nbd-field"]) && isset($_FILES["nbd-field"]["name"][$field_id]) && $_FILES["nbd-field"]["error"][$field_id] == 0 ) {
                                    $origin_field = $this->get_field_by_id( $options, $field_id );
                                    $min_size = $origin_field['general']['upload_option']['min_size'];
                                    $max_size = $origin_field['general']['upload_option']['max_size'];
                                    $allow_type = $origin_field['general']['upload_option']['allow_type'];
                                    $file_info = pathinfo($_FILES["nbd-field"]["name"][$field_id]);
                                    $name = $file_info['filename'];
                                    $ext = strtolower( $file_info['extension'] );
                                    $size = $_FILES["nbd-field"]["size"][$field_id];
                                    if( $allow_type != '' ){
                                        $allow_type_arr = explode(',', strtolower( trim($allow_type ) ));
                                        $check_type = false;
                                        foreach($allow_type_arr as $type){
                                            if($ext == $type) $check_type = true;
                                        }
                                        if( !$check_type ){
                                            wc_add_notice( __( "Sorry, this file type is not permitted for security reasons.", 'web-to-print-online-designer' ) . ' (' . $ext . ')', 'error' );
                                            $passed = false;
                                        }
                                    }
                                    if( $min_size != '' ){
                                        $_min_size = intval($min_size) * 1024 * 1024;
                                        if( $_min_size > $size ){
                                            wc_add_notice( __( "Sorry, file is too small ( min size: ", 'web-to-print-online-designer' ) . $min_size . __( " MB )", 'web-to-print-online-designer' ), 'error' );
                                            $passed = false;
                                        }
                                    }
                                    if( $max_size != '' ){
                                        $_max_size = intval($max_size) * 1024 * 1024;
                                        if( $_max_size < $size ){
                                            wc_add_notice( __( "Sorry, file is too big ( max size: ", 'web-to-print-online-designer' ) . $max_size . __( " MB )", 'web-to-print-online-designer' ), 'error' );
                                            $passed = false;
                                        }
                                    }
                                }else{
                                    if( $field['enable'] && $field['required'] ){
                                        wc_add_notice( __( "Upload file is required.", 'web-to-print-online-designer' ), 'error' );
                                        $passed = false;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $passed;
        }
        public function remove_session_design(  $product_id, $variation_id ){
            if( isset( $_REQUEST['nbo-ignore-design'] ) && $_REQUEST['nbo-ignore-design'] == '1' ){
                $variation_id = $variation_id != '' ? $variation_id : 0;
                $product_id = get_wpml_original_id($product_id);
                $recent_variation_id = $variation_id;
                $variation_id = get_wpml_original_id($variation_id);
                $nbd_item_cart_key = ($variation_id > 0) ? $product_id . '_' . $variation_id : $product_id; 
                $recent_nbd_item_cart_key = ($recent_variation_id > 0) ? $product_id . '_' . $recent_variation_id : $product_id;
                WC()->session->__unset('nbd_item_key_'.$nbd_item_cart_key);
                WC()->session->__unset('nbd_item_key_'.$recent_nbd_item_cart_key);
                WC()->session->__unset('nbu_item_key_'.$nbd_item_cart_key);
                WC()->session->__unset('nbu_item_key_'.$recent_nbd_item_cart_key);
            }
        }
        public function get_default_option($options){
            $fields = array();
            if( !isset($options['fields']) ) return $fields;
            foreach ($options['fields'] as $field){
                if($field['general']['enabled'] == 'y'){
                    $fields[$field['id']] = array(
                        'title' =>  $field['general']['title'],
                        'enable'    =>  true,
                        'required'    =>  $field['general']['required'] == 'y' ? true : false,
                        'is_upload'  =>  ( $field['general']['data_type'] == 'i' && $field['general']['input_type'] == 'u') ? true : false
                    );
                    if($field['general']['data_type'] == 'i'){
                        $fields[$field['id']]['value'] = $field['general']['input_type'] != 't' ? ( $field['general']['input_option']['min'] != '' ? $field['general']['input_option']['min'] : 0 ) : '';
                    }else{
                        $fields[$field['id']]['value'] = 0;
                        foreach ($field['general']['attributes']['options'] as $key => $op){
                            if( isset($op['selected']) && $op['selected'] == 'on' ) $fields[$field['id']]['value'] = $key;
                        }
                    }
                }
            }
            $valid_fields = $this->validate_field_option($options, $fields);
            return $valid_fields;
        }
        public function validate_field_option( $origin_fields, $fields ){
            foreach( $fields as $field_id => $f ){
                $field = $this->get_field_by_id($origin_fields, $field_id);
                $check = array();
                if( $field['conditional']['enable'] == 'n' || !isset($field['conditional']['depend']) || count($field['conditional']['depend']) == 0 ){
                    continue;
                }
                $show = $field['conditional']['show'];
                $logic = $field['conditional']['logic'];
                $total_check = $logic == 'a' ? true : false;
                foreach($field['conditional']['depend'] as $key => $con){
                    $check[$key] = true;
                    if( $con['id'] != '' ){
                        switch( $con['operator'] ){
                            case 'i':
                                $check[$key] = $fields[$con['id']]['value'] == $con['val'] ? true : false;
                                break;
                            case 'n':
                                $check[$key] = $fields[$con['id']]['value'] != $con['val'] ? true : false;
                                break;
                            case 'e':
                                $check[$key] = $fields[$con['id']]['value'] == '' ? true : false;
                                break;
                            case 'ne':
                                $check[$key] = $fields[$con['id']]['value'] != '' ? true : false;
                                break;
                        }
                    }
                }
                foreach ($check as $c){
                    $total_check = $logic == 'a' ? ($total_check && $c) : ($total_check || $c);
                }
                $fields[$field_id]['enable'] = $show == 'y' ? $total_check : !$total_check;
            }
            return $fields;
        }
        public function order_again_cart_item_data( $arr, $item, $order ){
            remove_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart_validation' ), 1, 6 );
            $order_items = $order->get_items();
            foreach( $order_items as $order_item_id => $order_item ){
                if( $item->get_id() == $order_item_id ){
                    if( $option_price = wc_get_order_item_meta($order_item_id, '_nbo_option_price') ){
                        $arr['nbo_meta']['option_price'] = $option_price;
                    }
                    if( $field = wc_get_order_item_meta($order_item_id, '_nbo_field') ){
                        $arr['nbo_meta']['field'] = $field;
                    }
                    if( $options = wc_get_order_item_meta($order_item_id, '_nbo_options') ){
                        $arr['nbo_meta']['options'] = $options;
                    }
                    if( $original_price = wc_get_order_item_meta($order_item_id, '_nbo_original_price') ){
                        $arr['nbo_meta']['original_price'] = $original_price;
                        $arr['nbo_meta']['price'] = $this->format_price($original_price + $option_price['total_price'] - $option_price['discount_price']);
                    }
                }
            }
            return $arr;
        }
        public function add_cart_fee( $cart_object ){
            if ( is_array( $cart_object->cart_contents ) ) {
                foreach ( $cart_object->cart_contents as $key => $value ) {
                    if( isset($value['nbo_meta']) && isset( $value['nbo_meta']['option_price']['cart_item_fee'] ) && $value['nbo_meta']['option_price']['cart_item_fee']['value'] != 0 ){
                        $fees       = array();
                        if ( is_object( $cart_object ) && is_callable( array( $cart_object, "get_fees" ) ) ) {
                            $fees = $cart_object->get_fees();
                        }else{
                            $fees = $cart_object->fees;
                        }
                        foreach( $value['nbo_meta']['option_price']['cart_item_fee']['fields'] as $field ){
                            //$cart_item_fee  = $value['nbo_meta']['option_price']['cart_item_fee'];
                            //$fee_name       = __('Extra fee ', 'web-to-print-online-designer') . strtoupper( substr( $key,0,7 ) ) . ': ' . $cart_item_fee['name'];
                            $fee_name       = $field['name'] . ' - ' . strtoupper( substr( $key,0,7 ) );
                            $product        = $value["data"];
                            $tax_class      = $product->get_tax_class();
                            $tax_status     = $product->get_tax_status();
                            if ( get_option( 'woocommerce_calc_taxes' ) == "yes" && $tax_status == "taxable" ) {
                                $tax = TRUE;
                            } else {
                                $tax = FALSE;
                            }
                            //$fee_price = $this->cacl_fee_price( $cart_item_fee['value'], $product );
                            $fee_price  = $this->cacl_fee_price( $field['price'], $product );
                            $can_add    = TRUE;
                            if ( is_array( $fees ) ) {
                                foreach ( $fees as $fee ) {
                                    if ( $fee->id == sanitize_title( $fee_name ) ) {
                                        $fee->amount = (float) $fee_price;
                                        $can_add     = FALSE;
                                        break;
                                    }
                                }
                            }
                            if( $can_add ){
                                $cart_object->add_fee( $fee_name, $fee_price, $tax, $tax_status );
                            }
                        }
                    }
                }
            }
        }
        public function cacl_fee_price( $price = "", $product = "" ){
            global $woocommerce;
            $taxable    = $product->is_taxable();
            $tax_class  = $product->get_tax_class();
            if ( $taxable ) {
                if ( get_option( 'woocommerce_prices_include_tax' ) === 'yes' ) {
                    $tax_rates  = WC_Tax::get_base_tax_rates( $tax_class );
                    $taxes      = WC_Tax::calc_tax( $price, $tax_rates, TRUE );
                    $price      = WC_Tax::round( $price - array_sum( $taxes ) );
                }
                return $price;
            }
            return $price;
        }
        public function remove_cart_item_quantity( $quantity_html, $cart_item, $cart_item_key ){
            if( isset($cart_item['nbo_meta']) ) $quantity_html = '';
            return $quantity_html;
        }
        public function order_line_item( $item, $cart_item_key, $values ){
            if ( isset( $values['nbo_meta'] ) ) {
                $decimals = nbdesigner_get_option('nbdesigner_number_of_decimals', 2);
                $hide_option_price = nbdesigner_get_option('nbdesigner_hide_option_price_in_order', 'no');
                foreach ($values['nbo_meta']['option_price']['fields'] as $field) {
                    if( !isset( $field['published'] ) || $field['published'] == 'y' ){
                        $price = floatval($field['price']) >= 0 ? '+' . wc_price($field['price'], array( 'decimals' => $decimals )) : wc_price($field['price'], array( 'decimals' => $decimals ));
                        if( isset($field['is_upload']) ){
                            if (strpos($field['val'], 'http') !== false) {
                                $file_url = $field['val'];
                            }else{
                                $file_url = Nbdesigner_IO::wp_convert_path_to_url( NBDESIGNER_UPLOAD_DIR . '/' .$field['val'] );
                            }
                            $field['value_name'] = '<a href="' . $file_url . '">' . $field['value_name'] . '</a>';
                        }
                        $post_fix = '';
                        if( isset($field['ind_qty']) ){
                            $post_fix = '<small>'. __('( cart fee )', 'web-to-print-online-designer') .'</small>';
                        }
                        if( isset( $field['fixed_amount'] ) ){
                            $post_fix = '<small>'. __('( for all items )', 'web-to-print-online-designer') .'</small>';
                        }
                        $display_price = $hide_option_price == 'no' ? $price.$post_fix : '';
                        $item->add_meta_data( $field['name'], $field['value_name']. '&nbsp;&nbsp;' . $display_price );
                    }
                }
                if( floatval( $values['nbo_meta']['option_price']['discount_price'] ) > 0 ){
                    $item->add_meta_data( __('Quantity Discount', 'web-to-print-online-designer'), '-' . wc_price( $values['nbo_meta']['option_price']['discount_price'], array( 'decimals' => $decimals ) ) );      
                }
                $item->add_meta_data('_nbo_option_price', $values['nbo_meta']['option_price']);
                $item->add_meta_data('_nbo_field', $values['nbo_meta']['field']);
                $item->add_meta_data('_nbo_options', wp_slash( $values['nbo_meta']['options'] ));
                $item->add_meta_data('_nbo_original_price', $values['nbo_meta']['original_price']);
            }
        }
        public function cart_item_thumbnail( $image = "", $cart_item = array() ){
            if( isset( $cart_item['nbo_meta'] ) && $cart_item['nbo_meta']['option_price']['cart_image'] != '' ){
                $size = 'shop_thumbnail';
                $dimensions = wc_get_image_size( $size ); 
                $image = '<img src="' . $cart_item['nbo_meta']['option_price']['cart_image']
                        . '" width="' . esc_attr( $dimensions['width'] )
                        . '" height="' . esc_attr( $dimensions['height'] )
                        . '" class="nbo-thumbnail woocommerce-placeholder wp-post-image" />';
            }
            $image = apply_filters('nbo_cart_item_thumbnail', $image, $cart_item);
            return $image;
        }
        public function re_calculate_price( $cart ){
            foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {
                if( isset( $cart_item['nbo_meta'] ) ){
                    //$product = $cart_item['data'];
                    $variation_id   = $cart_item['variation_id'];
                    $product_id     = $cart_item['product_id'];
                    $product        = $variation_id ? wc_get_product( $variation_id ) : wc_get_product( $product_id );
                    $options        = $cart_item['nbo_meta']['options'];
                    $fields         = $cart_item['nbo_meta']['field'];
                    //$original_price = $this->format_price( $product->get_price('edit') );
                    $original_price = (float)$product->get_price( 'edit' );
                    $quantity       = $cart_item['quantity'];
                    $option_price   = $this->option_processing( $options, $original_price, $fields, $quantity, $cart_item_key, $product );
                    if( $option_price['manual_pm'] ){
                        $original_price   = $option_price['original_price'];
                    }
                    if( isset( $cart_item['nbo_meta']['nbdpb'] ) ){
                        $path   = NBDESIGNER_CUSTOMER_DIR . '/' . $cart_item['nbo_meta']['nbdpb'] . '/preview';
                        $images = Nbdesigner_IO::get_list_images( $path, 1 );
                        if( count( $images ) ){
                            ksort( $images );
                            $option_price['cart_image'] = Nbdesigner_IO::wp_convert_path_to_url( end( $images ) );
                        }
                    }
                    $adjusted_price = $original_price + $option_price['total_price'] - $option_price['discount_price'];
                    $adjusted_price = $adjusted_price > 0 ? $adjusted_price : 0;
                    //$adjusted_price = $this->format_price($adjusted_price);
                    WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta']['option_price'] = $option_price;
                    $adjusted_price = apply_filters( 'nbo_adjusted_price', $adjusted_price, $cart_item );
                    WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta']['price'] = $adjusted_price;
                    $needed_change  = apply_filters( 'nbo_need_change_cart_item_price', true, WC()->cart->cart_contents[ $cart_item_key ] );
                    if( $needed_change ) WC()->cart->cart_contents[ $cart_item_key ]['data']->set_price( $adjusted_price ); 
                }
            }
        }
        public function remove_previous_product_from_cart( $passed, $product_id, $qty, $variation_id = '', $variations = array(), $cart_item_data = array() ){
            if ( $this->cart_edit_key ) {
                $cart_item_key = $this->cart_edit_key;
                if ( isset( $this->new_add_to_cart_key ) ) {
                    if ( $this->new_add_to_cart_key == $cart_item_key && isset( $_POST['quantity'] ) ) {
                        WC()->cart->set_quantity( $this->new_add_to_cart_key, $_POST['quantity'], TRUE );
                    } else {
                        $nbd_session = WC()->session->get($cart_item_key. '_nbd');
                        if( $nbd_session ){
                            WC()->session->set( 'nbd_session_removed', $nbd_session );
                            WC()->session->__unset( $cart_item_key. '_nbd' );

                            if( isset( WC()->cart->cart_contents[ $cart_item_key ]['nbd_design_id'] ) ){
                                $design_id = WC()->cart->cart_contents[ $cart_item_key ]['nbd_design_id'];
                                WC()->session->set( 'nbd_session_design_id_removed', $design_id );
                            }
                        }
                        WC()->cart->remove_cart_item( $cart_item_key );
                        unset( WC()->cart->removed_cart_contents[ $cart_item_key ] );
                    }
                }
            }
            return $passed;
        }
        public function add_to_cart_redirect( $url = "" ){
            if ( ( empty( $_REQUEST['add-to-cart'] ) || !is_numeric( $_REQUEST['add-to-cart'] ) ) && ( empty( $_REQUEST['nbo-add-to-cart'] ) || !is_numeric( $_REQUEST['nbo-add-to-cart'] ) ) ) {
                return $url;
            }
            if ( $this->cart_edit_key || isset( $_REQUEST['submit_form_mode2'] ) ) {
                $url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : WC()->cart->get_cart_url();
            }
            if( isset( $_REQUEST['submit_form_from_w2p'] ) ){
                $url = add_query_arg(
                    array(
                        'action'    => 'w2p_printshop_redirect'
                    ),
                    site_url()
                );
            }
            return $url;
        }
        public function quantity_input_args( $args = "", $product = "" ){
            if ( $this->cart_edit_key ) {
                $cart_item_key = $this->cart_edit_key;
                $cart_item = WC()->cart->get_cart_item( $cart_item_key );
                if ( isset( $cart_item["quantity"] ) ) {
                    $args["input_value"] = $cart_item["quantity"];
                }
            }
            return $args;
        }
        public function add_to_cart_text($var){
            if( $this->is_edit_mode ){
                return esc_attr__( 'Update cart', 'woocommerce' );
            }
            return $var;
        }
        public function update_rich_snippet_price( $markup_offer, $product ){
            $post_id        = $product->get_id();
            $snippet_price  = get_post_meta($post_id, '_nbo_snippet_price', true);
            $snippet_price  = $snippet_price ? $snippet_price : '';
            if( $snippet_price != '' ){
                $new_price = wc_format_decimal( $snippet_price, wc_get_price_decimals() );
                if( isset( $markup_offer['price'] ) ){
                    $markup_offer['price'] = $new_price;
                }
                if( isset( $markup_offer['priceSpecification'] ) && isset( $markup_offer['priceSpecification']['price'] ) ){
                    $markup_offer['priceSpecification']['price'] = $new_price;
                }
                if( isset( $markup_offer['lowPrice'] ) ){
                    $markup_offer['lowPrice'] = $markup_offer['highPrice'] = $new_price;
                }
            }
            return $markup_offer;
        }
        public function add_to_cart( $cart_item_key = "", $product_id = "", $quantity = "", $variation_id = "", $variation = "", $cart_item_data = "" ){
            if ( $this->cart_edit_key ) {
                $this->new_add_to_cart_key = $cart_item_key;
                $nbd_session = WC()->session->get('nbd_session_removed');
                if( $nbd_session ){
                    WC()->session->set($cart_item_key. '_nbd', $nbd_session);
                    if( !isset(WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds']) ) WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds'] = array();
                    WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds']['nbd'] = $nbd_session;
                    WC()->session->__unset('nbd_session_removed');
                    
                    $design_id = WC()->session->get('nbd_session_design_id_removed');
                    if( $design_id ){
                        WC()->cart->cart_contents[ $cart_item_key ]['nbd_design_id'] = $design_id;
                        WC()->session->__unset('nbd_session_design_id_removed');
                    }
                }
            }else{
                if (is_array($cart_item_data) && isset($cart_item_data['nbo_meta'])) {
                    $cart_contents = WC()->cart->cart_contents;
                    if (
                        is_array($cart_contents) &&
                        isset($cart_contents[$cart_item_key]) &&
                        !empty($cart_contents[$cart_item_key]) &&
                        !isset($cart_contents[$cart_item_key]['nbo_cart_item_key'])) {
                        WC()->cart->cart_contents[$cart_item_key]['nbo_cart_item_key'] = $cart_item_key;
                    }
                }
            }
        }
        public function cart_item_name($title = "", $cart_item = array(), $cart_item_key = ""){
            if ( !(is_cart() || is_checkout()) ){
                return $title;
            }
            if ( !isset( $cart_item['nbo_meta'] ) ) {
                return $title;
            }
            if( is_checkout() ){
                $title .= ' &times; <strong>' . $cart_item['quantity'] .'</strong>';
            }
            $product = $cart_item['data'];
            $link = add_query_arg(
                array(
                    'nbo_cart_item_key' => $cart_item_key,
                    'task'              => 'edit'
                )
                , $product->get_permalink( $cart_item ) );
            $link = wp_nonce_url( $link, 'nbo-edit' );
            $show_edit_link = apply_filters('nbo_show_edit_option_link_in_cart', true, $cart_item);
            if( $show_edit_link ) $title .= '<br /><a class="nbo-edit-option-cart" href="' . $link . '" class="nbo-cart-edit-options">' . __( 'Edit options', 'web-to-print-online-designer' ) . '</a><br />';
            return apply_filters( 'nbo_cart_item_name', $title, $cart_item, $cart_item_key);
        }
        public function get_item_data( $item_data, $cart_item ){
            if ( isset( $cart_item['nbo_meta'] ) ) {
                $hide_zero_price = nbdesigner_get_option('nbdesigner_hide_zero_price');
                $num_decimals = absint( wc_get_price_decimals() );
                if( nbdesigner_get_option('nbdesigner_hide_options_in_cart') != 'yes' ){
                    $hide_option_price = nbdesigner_get_option('nbdesigner_hide_option_price_in_cart', 'no');
                    $decimals = nbdesigner_get_option('nbdesigner_number_of_decimals', 2);
                    foreach ($cart_item['nbo_meta']['option_price']['fields'] as $field) {
                        if( !isset( $field['published'] ) || $field['published'] == 'y' ){
                            $price = floatval($field['price']) >= 0 ? '+' . wc_price( $field['price'], array( 'decimals' =>  $decimals ) ) : wc_price($field['price'], array( 'decimals' => $decimals ));
                            if( $hide_zero_price == 'yes' && round($field['price'], $num_decimals) == 0 ) $price = '';
                            if( isset($field['is_upload']) ){
                                if (strpos($field['val'], 'http') !== false) {
                                    $file_url = $field['val'];
                                }else{
                                    $file_url = Nbdesigner_IO::wp_convert_path_to_url( NBDESIGNER_UPLOAD_DIR . '/' .$field['val'] );
                                }
                                $field['value_name'] = '<a href="' . $file_url . '">' . $field['value_name'] . '</a>';
                            }
                            $post_fix = '';
                            if( isset($field['ind_qty']) ){
                                $post_fix = '<small>'. __('( cart fee )', 'web-to-print-online-designer') .'</small>';
                            }
                            if( isset( $field['fixed_amount'] ) ){
                                $post_fix = '<small>'. __('( for all items )', 'web-to-print-online-designer') .'</small>';
                            }
                            $item_data[] = array(
                                'name'      => $field['name'],
                                'display'   => $hide_option_price == 'yes' ? $field['value_name'] : $field['value_name']. '&nbsp;&nbsp;' . $price .$post_fix,
                                'hidden'    => false
                            );
                        }
                    }
                    if( floatval( $cart_item['nbo_meta']['option_price']['discount_price'] ) > 0 ){
                        $item_data[] = array(
                            'name'      => __('Quantity Discount', 'web-to-print-online-designer'),
                            'display'   => '-' . wc_price( $cart_item['nbo_meta']['option_price']['discount_price'], array( 'decimals' => $decimals ) ),
                            'hidden'    => false
                        );
                    }
					// custom quantity rule and breaks
					if (isset($cart_item['nbo_meta']['quantity_enable']) && $cart_item['nbo_meta']['quantity_enable'] === "y"){
						if (isset($cart_item['nbo_meta']['quantity_breaks'])) {
							$item_data[] = array(
								'name' => 'qty_break_minh',
								'display' => implode(',', $cart_item['nbo_meta']['quantity_breaks']),
								'hidden' => false,
							);
						}
					}
                }
            }
            return $item_data;
        }
        public function get_cart_item_from_session( $cart_item, $values ){
            if ( isset( $values['nbo_meta'] ) ) {
                $cart_item['nbo_meta'] = $values['nbo_meta'];
                // set the product price (if needed)
                $cart_item = $this->set_product_prices( $cart_item );
            }
            return $cart_item;
        }
        public function add_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity = 1 ){
            $post_data = $_POST;
            $option_id = $this->get_product_option($product_id);
            if( !$option_id ){
                return $cart_item_data;
            }
            if( isset($post_data['nbd-field']) || isset($post_data['nbo-add-to-cart']) ){
                $options        = $this->get_option( $option_id );

                //custom charmingprint 
                if( isset($post_data['nbd-options-color']) ) {
                    $options['color_selected'] = explode( ',' , $post_data['nbd-options-color'] );
                    $options['pt_color'] = $post_data['nbd-options-pt-color'];
                }
                
                $option_fields  = unserialize( $options['fields'] );
                $nbd_field      = isset( $post_data['nbd-field'] ) ? $post_data['nbd-field'] : array();
                if( isset($cart_item_data['nbd-field']) ){
                    /* Bulk variation */
                    $nbd_field = $cart_item_data['nbd-field'];
                    $nbd_field = $this->validate_before_processing( $option_fields, $nbd_field );
                    unset($cart_item_data['nbd-field']);
                }else{
                    if( !empty($_FILES) && isset($_FILES["nbd-field"]) ) {
                        foreach( $_FILES["nbd-field"]['name'] as $field_id => $file ){
                            if( !isset($nbd_field[$field_id]) ){
                                $nbd_upload_field = $this->upload_file( $_FILES["nbd-field"], $field_id );
                                if( !empty($nbd_upload_field) ){
                                    $nbd_field[$field_id] = $nbd_upload_field[$field_id];
                                }
                            }
                        }
                    }
                }
                $product        = $variation_id ? wc_get_product( $variation_id ) : wc_get_product( $product_id );
                $original_price = (float)$product->get_price('edit');
                $option_price   = $this->option_processing( $options, $original_price, $nbd_field, $quantity, null, $product );
                if( $option_price['manual_pm'] ){
                    $original_price   = $option_price['original_price'];
                }
                if( isset($post_data['nbdpb-folder']) ){
                    $cart_item_data['nbo_meta']['nbdpb'] = $post_data['nbdpb-folder'];
                    $path   = NBDESIGNER_CUSTOMER_DIR . '/' . $post_data['nbdpb-folder'] . '/preview';
                    $images = Nbdesigner_IO::get_list_images($path, 1);
                    if( count($images) ){
                        ksort( $images );
                        $option_price['cart_image'] = Nbdesigner_IO::wp_convert_path_to_url(end($images));
                    }
                }

                $option_fields = unserialize($options['fields']);
				// custom quantity rule and breaks
				$cart_item_data['nbo_meta']['quantity_enable'] = $option_fields['quantity_enable'];
                $min = $option_fields['quantity_min'];
                $max = $option_fields['quantity_max'];
                $step = $option_fields['quantity_step'];
                $resultArray = $this->generateArray($min, $max, $step);
				
                $option_fields = $this->recursive_stripslashes($option_fields);
                if (count($option_fields['quantity_breaks']) > 0) {
                    $arr_temp_qty = array();
                    foreach ($option_fields['quantity_breaks'] as $break) {
                        $arr_temp_qty[] = absint($break['val']);
                    }
                    sort($arr_temp_qty);
					// custom quantity rule and breaks
					if($option_fields['quantity_type'] === 'r'){
                        $cart_item_data['nbo_meta']['quantity_breaks'] = $resultArray;
                    }else{
                        $cart_item_data['nbo_meta']['quantity_breaks'] = $arr_temp_qty;
                    }
                }
				$cart_item_data['nbo_meta']['quantity_enable'] = $option_fields['quantity_enable'];
                $options['fields']                              = base64_encode( $options['fields'] );
                $cart_item_data['nbo_meta']['option_price']     = $option_price;
                $cart_item_data['nbo_meta']['field']            = $nbd_field;
                $cart_item_data['nbo_meta']['options']          = $options;
                $cart_item_data['nbo_meta']['original_price']   = $original_price;
                $cart_item_data['nbo_meta']['price']            = $original_price + $option_price['total_price'] - $option_price['discount_price'];
            }
            return $cart_item_data;
        }
        public function upload_file( $files, $field_id ){
            $nbd_upload_fields = array();
            global $woocommerce;
            $user_folder = md5( $woocommerce->session->get_customer_id() );
            $file = $files['name'][$field_id];
            if( $files['error'][$field_id] == 0 ){
                $ext = pathinfo( $file, PATHINFO_EXTENSION );
                $new_name = strtotime("now").substr(md5(rand(1111,9999)),0,8).'.'.$ext;
                $new_path = NBDESIGNER_UPLOAD_DIR . '/' .$user_folder . '/' .$new_name;
                $mkpath = wp_mkdir_p( NBDESIGNER_UPLOAD_DIR . '/' .$user_folder);
                if( $mkpath ){
                    if (move_uploaded_file($files['tmp_name'][$field_id], $new_path)) {
                        $nbd_upload_fields[$field_id] = $user_folder . '/' .$new_name;
                    }else{
                        //todo
                    }
                }
            }
            return $nbd_upload_fields;
        }
        public function format_price( $price ){
            //$decimal_separator = stripslashes( wc_get_price_decimal_separator() );
            //$thousand_separator = stripslashes( wc_get_price_thousand_separator() );
            $num_decimals = wc_get_price_decimals();
            //$price = str_replace($decimal_separator, '.', $price);
            //$price = str_replace($thousand_separator, '', $price);
            $price = round($price, $num_decimals);
            return $price;
        }
        public function get_field_by_id( $option_fields, $field_id ){
            foreach($option_fields['fields'] as $key => $field){
                if( $field['id'] == $field_id ) return $field;
            }
        }
        public function validate_before_processing($option_fields, $nbd_field){
            $new_fields = $nbd_field;
            foreach($nbd_field as $field_id => $field){
                $origin_field = $this->get_field_by_id( $option_fields, $field_id );
                if( $origin_field['conditional']['enable'] == 'n' || !isset($origin_field['conditional']['depend']) || count($origin_field['conditional']['depend']) == 0  ) continue;
                $show = $origin_field['conditional']['show'];
                $logic = $origin_field['conditional']['logic'];
                $total_check = $logic == 'a' ? true : false;
                $check = array();
                foreach($origin_field['conditional']['depend'] as $key => $con){
                    $check[$key] = true;
                    if( $con['id'] != '' ){
                        if( !isset($new_fields[$con['id']]) ){
                            $check[$key] = true;
                        }else{
                            switch( $con['operator'] ){
                                case 'i':
                                    $check[$key] = $nbd_field[$con['id']] == $con['val'] ? true : false;
                                    break;
                                case 'n':
                                    $check[$key] = $nbd_field[$con['id']] != $con['val'] ? true : false;
                                    break;
                                case 'e':
                                    $check[$key] = $nbd_field[$con['id']] == '' ? true : false;
                                    break;
                                case 'ne':
                                    $check[$key] = $nbd_field[$con['id']] != '' ? true : false;
                                    break;
                            }
                        }
                    }
                }
                foreach ($check as $c){
                    $total_check = $logic == 'a' ? ($total_check && $c) : ($total_check || $c);
                }
                $enable = $show == 'y' ? $total_check : !$total_check;
                if( !$enable ) unset($new_fields[$field_id]);
            }
            return $new_fields;
        }
        public static function recursive_stripslashes( $fields ){
            $valid_fields = array();
            foreach($fields as $key => $field){
                if(is_array($field) ){
                    $valid_fields[$key] = self::recursive_stripslashes($field);
                }else if(!is_null($field)){
                    $valid_fields[$key] = stripslashes($field);
                }
            }
            return $valid_fields;
        }
        //custom charmingprint
        public function nb_get_price_by_qty( $array , $qty) {
            usort( $array, array( $this , 'custom_sort_qty' ) );
            $price = 0;
            if( isset($array[0]) && $qty <= $array[0]['qty'] ) {
                return (float) $array[0]['price'];
            } else {
                foreach( $array as $key => $value ) {
                    if( isset( $array[$key + 1]) ) {
                        if( (int) $value['qty'] <= $qty && $qty < (int)$array[$key + 1]['qty']) {
                            return (float) $value['price'];
                        }
                    } else {
                        return (float) $value['price'];
                    }
                }
            }
            return $price;
        }
        public function custom_sort_qty( $a , $b ) {
            if ($a['qty'] == $b['qty']) {
                return 0;
            }
            return ($a['qty'] < $b['qty']) ? -1 : 1;
        }
        public function option_processing( $options, $original_price, $fields, $quantity, $cart_item_key = null, $product = null ){
            if( nbd_is_base64_string( $options['fields'] ) ){
                $options['fields'] = base64_decode( $options['fields'] );
            }
            $option_fields  = unserialize( $options['fields'] );
            $option_fields  = $this->recursive_stripslashes( $option_fields );
            $quantity_break = $this->get_quantity_break( $option_fields, $quantity );
            $xfactor        = 1;
            $total_price    = 0;
            $discount_price = 0;
            $_fields        = array();
            $cart_image     = '';
            $cart_item_fee  = 0;
            $line_price     = array(
                'fixed'     => 0,
                'percent'   => 0,
                'xfactor'   => 1
            );
            $fixed_amount   = 0;
            $manual_pm      = false;

            if( $option_fields['display_type'] == 2 && isset( $option_fields['manual_build_pm'] ) && $option_fields['manual_build_pm'] == 'on' 
                && isset( $option_fields['manual_pm'] ) && $option_fields['manual_pm'] != '' ){
                $pm_parts       = explode( '|', $option_fields['manual_pm'] );
                $pm_hoz         = explode( ',', $pm_parts[0] );
                $pm_ver         = explode( ',', $pm_parts[1] );
                $mpm_prices     = explode( ',', $pm_parts[2] );

                $pm_hoz_fields  = array();
                $pm_ver_fields  = array();
                $pm_num_col     = 1;
                $pm_num_row     = 1;

                foreach( $pm_hoz as $index => $field_id ){
                    $origin_field       = $this->get_field_by_id( $option_fields, $field_id );
                    $pm_num_col        *= count( $origin_field['general']['attributes']['options'] );

                    $colspan            = 1;
                    foreach( $pm_hoz as $_index => $_field_id ){
                        $_origin_field       = $this->get_field_by_id( $option_fields, $_field_id );
                        if( $_field_id > $field_id ){
                            $colspan    *= count( $_origin_field['general']['attributes']['options'] );
                        }
                    }

                    $pm_hoz_fields[]    = array(
                        'field_id'  => $field_id,
                        'colspan'   => $colspan,
                        'value'     => intval( $fields[ $field_id ] )
                    );
                }
                foreach( $pm_ver as $index => $field_id ){
                    $origin_field       = $this->get_field_by_id( $option_fields, $field_id );
                    $pm_num_row        *= count( $origin_field['general']['attributes']['options'] );

                    $rowspan            = 1;
                    foreach( $pm_ver as $_index => $_field_id ){
                        $_origin_field       = $this->get_field_by_id( $option_fields, $_field_id );
                        if( $_field_id > $field_id ){
                            $rowspan    *= count( $_origin_field['general']['attributes']['options'] );
                        }
                    }

                    $pm_ver_fields[]    = array(
                        'field_id'  => $field_id,
                        'rowspan'   => $rowspan,
                        'value'     => intval( $fields[ $field_id ] )
                    );
                }

                $i = 0;
                foreach( $pm_hoz_fields as $index => $pm_hoz_field ){
                    $i += $pm_hoz_field['value'] * $pm_hoz_field['colspan'];
                }
                $j = 0;
                foreach( $pm_ver_fields as $index => $pm_ver_field ){
                    $j += $pm_ver_field['value'] * $pm_ver_field['rowspan'];
                }

                $pm_index = $i * $pm_num_col + $j;
                if( isset( $mpm_prices[$pm_index] ) && $mpm_prices[$pm_index] != '' ){
                    $original_price = floatval( $mpm_prices[$pm_index] );
                }else{
                    $original_price = 0;
                }

                $manual_pm  = true;
            }

            foreach( $fields as $key => $val ){
                if( $manual_pm ){
                    if( in_array( $key, $pm_hoz ) || in_array( $key, $pm_ver ) ){
                        continue;
                    }
                }

                $origin_field = $this->get_field_by_id( $option_fields, $key );
                $published    = isset( $origin_field['general']['published'] ) ? $origin_field['general']['published'] : 'y';

                // custom charmingprint
                if( isset($origin_field['nbd_type']) && $origin_field['nbd_type'] == 'pt_color' ){
                    $color_price = (int) $origin_field['general']['attributes']['options'][$val]['price'][0];
                    $option_color = $origin_field['general']['attributes']['options'][$val];
                    if( $option_color['key'] == 'pt_color1' || $option_color['key'] == 'pt_color3') {
                        $price = 0;
                        $price = $this->nb_get_price_by_qty( $option_color['pt_color']['sg_color']['qty_price'] , $quantity );
                        $color_price += $price;
                    } 
                    if( $option_color['key'] == 'pt_color2' ) {
                        if( count( $option_color['pt_color']['mul_color'] ) > 0 && isset( $options['color_selected'] ) && count( $options['color_selected'] ) > 0 ) {
                            foreach( $option_color['pt_color']['mul_color'] as $k1 => $v1 ) {
                                foreach( $options['color_selected'] as $v2 ) {
                                    if( $v1['color'] == $v2 ) {
                                        $price = 0;
                                        $price = $this->nb_get_price_by_qty( $v1['qty_price'] , $quantity );
                                        $color_price += $price;
                                    }
                                }
                            }
                        }
                        
                    } 
                    $origin_field['general']['attributes']['options'][$val]['price'][0] = $color_price;
                }

                if( isset($origin_field['nbe_type']) && $origin_field['nbe_type'] == 'delivery' ){
                    $turnaround_matrix = $this->build_turnaround_matrix( $option_fields, $origin_field );
                    $position = $quantity_break['index'];
                    $__val = is_array($val) ? ( isset($val['value']) ? $val['value'] : $val[0] ) : $val;
                    if( $turnaround_matrix[ $position ][ $__val ] == 0 ){
                        for ($i = 0; $i < count( $origin_field['general']['attributes']['options'] ); $i++) {
                            if( $turnaround_matrix[ $position ][ $i ] == 1 ){
                                $val = '' . $i;
                                if( !is_null( $cart_item_key ) ){
                                    if( isset( WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta'] ) ){
                                        $nbd_field = WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta']['field'];
                                        $nbd_field[ $key ] = $val;
                                        WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta']['field'] = $nbd_field;
                                    }
                                }
                                break;
                            }
                        }
                    }
                }
                $_fields[$key] = array(
                    'name'          => $origin_field['general']['title'],
                    'val'           => $val,
                    'value_name'    => $val,
                    'published'     => $published
                );
                if( $origin_field['general']['data_type'] == 'i' ){
                    if($origin_field['general']['depend_quantity'] == 'n'){
                        if( $origin_field['general']['price_type'] == 'mf' ){
                            $factor = $this->eval_price( $origin_field['general']['price'], $origin_field, $quantity, $fields, $original_price, $option_fields, $_fields, $product );
                        }else{
                            $factor = $origin_field['general']['price'];
                        }
                    }else{
                        if( $origin_field['general']['price_type'] == 'mf' ){
                            $factor = $this->eval_price( $origin_field['general']['price_breaks'][$quantity_break['index']], $origin_field, $quantity, $fields, $original_price, $option_fields, $_fields, $product );
                        }else{
                            $factor = $origin_field['general']['price_breaks'][$quantity_break['index']];
                        }
                    }
                    if( isset($origin_field['nbd_type']) && $origin_field['nbd_type'] == 'dimension' && $origin_field['general']['mesure'] == 'y' && isset($origin_field['general']['mesure_range']) && count($origin_field['general']['mesure_range']) > 0 ){
                        $dimension = explode("x",$val);
                        $factor = $this->calculate_price_base_measurement( $origin_field, $dimension[0], $dimension[1], $quantity );
                        if( ($origin_field['general']['price_type'] == 'f' || $origin_field['general']['price_type'] == 'c') && $origin_field['general']['mesure_base_pages'] == 'y' ){
                            $no_page = 1;
                            foreach($fields as $_key => $_val){
                                $_origin_field = $this->get_field_by_id( $option_fields, $_key );
                                if( isset($_origin_field['nbd_type']) && ( $_origin_field['nbd_type'] == 'page' || $_origin_field['nbd_type'] == 'page1' ) ){
                                    if( $_origin_field['general']['data_type'] == 'i' ){
                                        $no_page = $_val;
                                    }else{
                                        //$no_page = count($_val);
                                    }
                                }
                            }
                            $factor *= floor( ($no_page + 1) / 2 );
                        }
                    }
                    if( $origin_field['general']['input_type'] == 'u' ){
                        $file_name = explode('/', $val);
                        $_fields[$key]['value_name']    = $file_name[ count($file_name) - 1 ];
                        $_fields[$key]['is_upload']     = 1;
                    }
                    if( isset($origin_field['nbd_type']) && $origin_field['nbd_type'] == 'page1'
                            && isset($origin_field['general']['price_depend_no']) && $origin_field['general']['price_depend_no'] == 'y'
                            && isset($origin_field['general']['price_no_range']) && count( $origin_field['general']['price_no_range'] ) > 0 ){
                        $default = 0;
                        if( isset( $origin_field['general']['input_option']['default'] ) && $origin_field['general']['input_option']['default'] != '' ){
                            $default = absint( $origin_field['general']['input_option']['default'] );
                        }
                        $current_val    = absint( $val );
                        $current_val   -= $default;
                        $current_val    = $current_val > 0 ? $current_val : 0;
                        $price_no_range = $origin_field['general']['price_no_range'];
                        foreach( $price_no_range as $range ){
                            $qty = absint( $range[0] );
                            if( $current_val >= $qty ){
                                $factor = floatval( $range[1] );
                            }
                        }
                    }
                }else{
                    $select_val = is_array($val) ? ( isset($val['value']) ? $val['value'] : $val[0] ) : $val;
                    $option = $origin_field['general']['attributes']['options'][$select_val];
                    $has_subattr = false;
                    if( isset($option['enable_subattr']) && $option['enable_subattr'] == 'on' && isset($option['sub_attributes']) && count($option['sub_attributes']) > 0 ){
                        $has_subattr = true;
                    }
                    if( !$has_subattr && isset( $val['sub_value'] ) ){
                        unset( $val['sub_value'] );
                    }
                    $_fields[$key]['value_name'] = $option['name']; 
                    if(is_array($val)){
                        if( $has_subattr ){
                            $_fields[$key]['value_name'] .= ' - ' . $option['sub_attributes'][$val['sub_value']]['name'];
                        }else{
                            $_fields[$key]['value_name'] = '';
                            foreach($val as $k => $v){
                                $_fields[$key]['value_name'] .= ($k == 0 ? '' : ', ') . $origin_field['general']['attributes']['options'][$v]['name'];
                            }
                        }
                    }
                    if($origin_field['general']['depend_quantity'] == 'n'){
                        if( $origin_field['general']['price_type'] == 'mf' ){
                            $factor = $this->eval_price( $option['price'][0], $origin_field, $quantity, $fields, $original_price, $option_fields, $_fields, $product );
                        }else{
                            $factor = floatval( $option['price'][0] );
                        }
                    }else{
                        if( $origin_field['general']['price_type'] == 'mf' ){
                            $factor = $this->eval_price( $option['price'][$quantity_break['index']], $origin_field, $quantity, $fields, $original_price, $option_fields, $_fields, $product );
                        }else{
                            $factor = floatval( $option['price'][$quantity_break['index']] );
                        }
                    }
                    if( $has_subattr ){
                        $soption = $option['sub_attributes'][$val['sub_value']];
                        if($origin_field['general']['depend_quantity'] == 'n'){
                            if( $origin_field['general']['price_type'] == 'mf' ){
                                $factor += $this->eval_price( $soption['price'][0], $origin_field, $quantity, $fields, $original_price, $option_fields, $_fields, $product );
                            }else{
                                $factor += floatval( $soption['price'][0] );
                            }
                        }else{
                            if( $origin_field['general']['price_type'] == 'mf' ){
                                $factor += $this->eval_price( $soption['price'][$quantity_break['index']], $origin_field, $quantity, $fields, $original_price, $option_fields, $_fields, $product );
                            }else{
                                $factor += floatval( $soption['price'][$quantity_break['index']] );
                            }
                        }
                    }
                    if($origin_field['appearance']['change_image_product'] == 'y' && isset($option['product_image']) && $option['product_image'] > 0){
                        $image = wp_get_attachment_image_src( $option['product_image'], 'thumbnail' );
                        if($image){
                            $cart_image = $image[0];
                        }else{
                            $cart_image = wp_get_attachment_url($option['product_image']);
                        }
                    }
                }
                $_fields[$key]['is_pp'] = 0;
                if( isset($origin_field['nbd_type']) && $origin_field['nbd_type'] == 'dimension' && $origin_field['general']['price_type'] == 'c' ){
                    $origin_field['general']['price_type'] == 'f';
                }
                if( isset($origin_field['nbd_type']) && ( $origin_field['nbd_type'] == 'page' || $origin_field['nbd_type'] == 'page2' ) && $origin_field['general']['data_type'] == 'm' ){
                    $factor = array();
                    foreach($val as $k => $v){
                        $option = $origin_field['general']['attributes']['options'][$v];
                        if($origin_field['general']['depend_quantity'] == 'n'){
                            if( $origin_field['general']['price_type'] == 'mf' ){
                                $factor[$k] = $this->eval_price( $option['price'][0], $origin_field, $quantity, $fields, $original_price, $option_fields, $_fields, $product );
                            }else{
                                $factor[$k] = $option['price'][0];
                            }
                        }else{
                            if( $origin_field['general']['price_type'] == 'mf' ){
                                $factor[$k] = $this->eval_price( $option['price'][$quantity_break['index']], $origin_field, $quantity, $fields, $original_price, $option_fields, $_fields, $product );
                            }else{
                                $factor[$k] = $option['price'][$quantity_break['index']];
                            }
                        }
                    }
                    $_fields[$key]['price'] = 0;
                    $xfac = 0; $_xfac = 0;
                    foreach($factor as $fac){
                        $fac = floatval($fac);
                        $_fac = $fac;
                        if( $this->is_independent_qty( $origin_field ) ){
                            $fac =  0;
                            $_fields[$key]['ind_qty'] = 1;
                        }
                        if( $this->is_fixed_amount( $origin_field ) ){
                            $fac /= $quantity;
                            $_fields[$key]['fixed_amount'] = 1;
                        }
                        switch ($origin_field['general']['price_type']){
                            case 'f':
                            case 'mf':
                                $_fields[$key]['price'] += $_fac;
                                $total_price += $fac;
                                if( $this->is_independent_qty( $origin_field ) ){
                                    $line_price['fixed'] += $_fac;
                                }
                                break;
                            case 'p':
                                $_fields[$key]['price'] += $original_price * $_fac / 100;
                                $total_price += $original_price * $fac / 100;
                                if( $this->is_independent_qty( $origin_field ) ){
                                    $line_price['percent'] += $_fac;
                                }
                                break;
                            case 'p+':
                                $_fields[$key]['price'] += $fac / 100;
                                $_fields[$key]['_price'] += $_fac / 100;
                                $_fields[$key]['is_pp'] = 1;
                                $xfac += $fac / 100;
                                $_xfac += $_fac / 100;
                                break;
                        }
                    }
                    if( $origin_field['general']['price_type'] == 'p+' ){
                        $xfactor *= (1 + $xfac / 100);
                        if( $this->is_independent_qty( $origin_field ) ){
                            $line_price['xfactor'] *= (1 + $_xfac / 100);
                        }
                    }
                }else{
                    $factor = floatval($factor);
                    $_factor = $factor;
                    if( $this->is_independent_qty( $origin_field ) ){
                        $factor = 0;
                        $_fields[$key]['ind_qty'] = 1;
                    }
                    if( $this->is_fixed_amount( $origin_field ) ){
                        $factor /= $quantity;
                        $_fields[$key]['fixed_amount'] = 1;
                    }
                    switch ($origin_field['general']['price_type']){
                        case 'f':
                        case 'mf':
                            $_fields[$key]['price'] = $_factor;
                            $total_price += $factor;
                            if( $this->is_independent_qty( $origin_field ) ){
                                $line_price['fixed'] += $_factor;
                            }
                            break;
                        case 'p':
                            $_fields[$key]['price'] = $original_price * $_factor / 100;
                            $total_price += $original_price * $factor / 100;
                            if( $this->is_independent_qty( $origin_field ) ){
                                $line_price['percent'] += $_factor;
                            }
                            break;
                        case 'p+':
                            $_fields[$key]['price'] = $factor / 100;
                            $_fields[$key]['_price'] = $_factor / 100;
                            $_fields[$key]['is_pp'] = 1;
                            $xfactor *= (1 + $factor / 100);
                            if( $this->is_independent_qty( $origin_field ) ){
                                $line_price['xfactor'] *= (1 + $_factor / 100);
                            }
                            break;
                        case 'c':
                            $current_val = absint( $val );
                            if( ( isset($origin_field['nbd_type']) && ( ( $origin_field['nbd_type'] == 'page' && $origin_field['general']['data_type'] == 'i' ) || ( $origin_field['nbd_type'] == 'page1' ) ) ) || ( isset( $origin_field['nbe_type'] ) && $origin_field['nbe_type'] == 'number_file' ) ){
                                $default = 0;
                                if( isset( $origin_field['general']['input_option']['default'] ) && $origin_field['general']['input_option']['default'] != '' ){
                                    $default = absint( $origin_field['general']['input_option']['default'] );
                                }
                                $current_val -= $default;
                            }
                            $current_val = $current_val > 0 ? $current_val : 0;
                            $_fields[$key]['price'] = $_factor * $current_val;
                            $total_price += $factor * $current_val;
                            if( $this->is_independent_qty( $origin_field ) ){
                                $line_price['fixed'] += $_factor * $current_val;
                            }
                            break;
                        case 'cp':
                            $_fields[$key]['price'] = $_factor * absint( strlen( $val ) );
                            $total_price += $factor * absint( strlen( $val ) );
                            if( $this->is_independent_qty( $origin_field ) ){
                                $line_price['fixed'] += $_factor * absint( strlen( $val ) );
                            }
                            break;
                    }
                }
            }
            $total_price += ( ($original_price + $total_price ) * ($xfactor - 1 ) );
            foreach($_fields as $key => $val){
                if( $_fields[$key]['is_pp'] == 1 ) {
                    $_fields[$key]['price'] = $_fields[$key]['price'] * ($original_price + $total_price ) / ( $_fields[$key]['price'] + 1 );
                }
            }
            if( $quantity_break['index'] == 0 && $quantity_break['oparator'] == 'lt' ){
                $qty_factor = '';
            }else{
                $qty_factor = $option_fields['quantity_breaks'][$quantity_break['index']]['dis'];
            }
            $qty_factor = floatval($qty_factor);
            $discount_price = $option_fields['quantity_discount_type'] == 'f' ? $qty_factor : ($original_price + $total_price ) * $qty_factor / 100;
            $total_cart_price = ( $original_price + $total_price - $discount_price ) * $quantity;
            $cart_item_fee = array(
                'value'   => 0,
                'name'    => '',
                'id'      => '',
                'fields'  => array()
            );
            if( $line_price['fixed'] != 0 || $line_price['xfactor'] != 1 || $line_price['percent'] != 0 ){
                $_total_cart_price = $total_cart_price;
                if( $line_price['fixed'] != 0 ){
                    $total_cart_price += $line_price['fixed'];
                }
                if( $line_price['percent'] != 0 ){
                    $total_cart_price += ($original_price * $line_price['percent'] / 100);
                }
                if( $line_price['xfactor'] != 1 ){
                    $total_cart_price += ( $total_cart_price * ( $line_price['xfactor'] - 1 ) );
                    foreach($_fields as $key => $val){
                        if( $val['is_pp'] == 1 && isset($val['ind_qty']) && $val['ind_qty'] == 1  ) {
                            $_fields[$key]['price'] = $val['_price'] * $total_cart_price / ( $val['_price'] + 1 );
                        }
                    }
                }
                foreach($_fields as $key => $val){
                    if( isset($val['ind_qty']) && $val['ind_qty'] == 1 ){
                        $cart_item_fee['name'] .= $val['name'] . ', ';
                        $cart_item_fee['fields'][] = array(
                            'name'  => $val['name'] . ': ' . $val['value_name'],
                            'price' => $val['price']
                        );
                    }
                }
                if( $cart_item_fee['name'] != '' ){
                    $cart_item_fee['name'] = substr($cart_item_fee['name'], 0, strlen($cart_item_fee['name']) - 2);
                }
                $cart_item_fee['value'] = $total_cart_price - $_total_cart_price;
            }
            return array(
                'total_price'       => $total_price,
                'cart_item_fee'     => $cart_item_fee,
                'discount_price'    => $discount_price,
                'fields'            => $_fields,
                'cart_image'        => $cart_image,
                'manual_pm'         => $manual_pm,
                'original_price'    => $original_price
            );
        }
        public function eval_price( $formula, $origin_field, $qty, $fields, $original_price, $option_fields, $_fields, $product ){
            require_once NBDESIGNER_PLUGIN_DIR . 'lib/eval-math/EvalMath.php';

            $formula = str_replace( "{quantity}", $qty, $formula );
            $formula = str_replace( "{price}", $original_price, $formula );

            $area = $this->calculate_product_area( $fields, $option_fields, $product );
            $formula = str_replace( "{area}", $area, $formula );

            $value      = 0;
            $value_len  = 0;
            if( $origin_field['general']['data_type'] == 'i' ){
                if( $origin_field['general']['input_type'] == 'n' || $origin_field['general']['input_type'] == 'r' ){
                    $value = $fields[$origin_field['id']];
                }
                if( $origin_field['general']['input_type'] == 't' || $origin_field['general']['input_type'] == 'a' ){
                    $value_len =  strlen( $fields[$origin_field['id']] );
                }
            }

            $formula = str_replace( "{this.value}", $value, $formula );
            $formula = str_replace( "{this.value_length}", $value_len, $formula );

            preg_match_all( '/\{(\s)*?field\.([^}]*)}/', $formula, $matches );
            if ( is_array( $matches ) && isset( $matches[2] ) && is_array( $matches[2] ) ) {
                foreach ( $matches[2] as $matchkey => $match ) {
                    $val = 0;
                    $pos = strrpos( $match, "." );
                    if ( $pos !== FALSE ) {
                        $field_id   = substr( $match, 0, $pos );
                        $type       = substr( $match, $pos + 1 );

                        $value          = 0;
                        $value_len      = 0;
                        $_origin_field  = $this->get_field_by_id( $option_fields, $field_id );

                        if( $_origin_field['general']['data_type'] == 'i' ){
                            if( $_origin_field['general']['input_type'] == 'n' || $_origin_field['general']['input_type'] == 'r' ){
                                $value = $fields[$field_id];
                            }
                            if( $_origin_field['general']['input_type'] == 't' || $_origin_field['general']['input_type'] == 'a' ){
                                $value_len =  strlen( $fields[$field_id] );
                            }
                        }

                        switch ( $type ) {
                            case 'price':
                                $val = ( isset( $_fields[$field_id] ) && isset( $_fields[$field_id]['price'] ) ) ? $_fields[$field_id]['price'] : 0;
                                break;
                            case 'value':
                                $val = $value;
                                break;
                            case 'value_length':
                                $val = $value_len;
                                break;
                            case 'implicit_value':
                                if( $_origin_field['general']['data_type'] == 'm' ){
                                    $field_value = $fields[$field_id];
                                    $select_val = is_array( $field_value ) ? ( isset( $field_value['value'] ) ? $field_value['value'] : $field_value[0] ) : $field_value;
                                    if( array_key_exists( $select_val, $_origin_field['general']['attributes']['options'] ) ){
                                        $option = $_origin_field['general']['attributes']['options'][$select_val];
                                        $val = isset( $option['implicit_value'] ) ? $option['implicit_value'] : 0;
                                    }
                                }
                                break;
                            case 'sub_implicit_value':
                                if( $_origin_field['general']['data_type'] == 'm' ){
                                    $field_value = $fields[$field_id];
                                    $select_val = is_array( $field_value ) ? ( isset( $field_value['value'] ) ? $field_value['value'] : $field_value[0] ) : $field_value;
                                    if( array_key_exists( $select_val, $_origin_field['general']['attributes']['options'] ) ){
                                        $option = $_origin_field['general']['attributes']['options'][$select_val];
                                        if( isset($option['enable_subattr']) && $option['enable_subattr'] == 'on' && isset($option['sub_attributes']) && count($option['sub_attributes']) > 0 ){
                                            if( is_array( $field_value ) && isset( $field_value['sub_value'] ) && array_key_exists( $field_value['sub_value'], $option['sub_attributes'] ) ){
                                                $soption = $option['sub_attributes'][$field_value['sub_value']];
                                                $val = isset( $soption['implicit_value'] ) ? $soption['implicit_value'] : 0;
                                            }
                                        }
                                    }
                                }
                                break;
                        }
                    }

                    $formula = str_replace( $matches[0][ $matchkey ], $val, $formula );
                }
            }

            $formula = preg_replace( '/\s+/', '', $formula );
            $formula = rtrim( ltrim( $formula, "\t\n\r\0\x0B+*/" ), "\t\n\r\0\x0B+-*/" );

            $eval_math = new EvalMath();
            $price = $formula ? $eval_math->evaluate( $formula ) : 0;
            return $price;
        }
        public function build_turnaround_matrix( $options, $field ){
            $turnaround_matrix  = array();
            $quantity_breaks    = array();
            foreach( $options['quantity_breaks'] as $break ){
                $quantity_breaks[] = absint($break['val']);
            }
            foreach( $quantity_breaks as $key => $break ){
                $turnaround_matrix[ $key ] = array();
                $qty = absint( $break );
                foreach( $field['general']['attributes']['options'] as $k => $option ){
                    $active  = 0;
                    $max_qty = absint( $option['max_qty'] );
                    if( $option['max_qty'] == '' || $max_qty >= $qty ) $active = 1;
                    $turnaround_matrix[ $key ][ $k ] = $active;
                }
            }
            return $turnaround_matrix;
        }
        public function is_independent_qty( $field ){
            if( isset( $field['general']['depend_qty'] ) && $field['general']['depend_qty'] == 'n' ){
                return true;
            }else{
                return false;
            }
        }
        public function is_fixed_amount( $field ){
            if( isset( $field['general']['depend_qty'] ) && $field['general']['depend_qty'] == 'n2' ){
                return true;
            }else{
                return false;
            }
        }
        public function calculate_price_base_measurement( $origin_field, $width, $height, $qty){
            $mesure_range   = $origin_field['general']['mesure_range'];
            $area           = floatval( $width ) * floatval( $height );
            $_area          = $area;

            if( isset( $origin_field['general']['mesure_base_qty'] ) && $origin_field['general']['mesure_base_qty'] == 'y' ){
                $area      *= $qty;
            }

            if( isset( $origin_field['general']['mesure_min_area'] ) ){
                $min_area   = floatval( $origin_field['general']['mesure_min_area'] );
                $area      -= $min_area;
                $_area     -= $min_area;
            }
            $area           = $area > 0 ? $area : 0;
            $_area          = $_area > 0 ? $_area : 0;

            if( isset( $origin_field['general']['mesure_type'] ) && $origin_field['general']['mesure_type'] == 'ur' ){
                $measurement_price  = 0;
                $infinity_end_range = false;
                $prev_end_range     = 0;

                foreach( $mesure_range as $key => $range ){
                    $start_range    = floatval( isset( $range[0] ) ? $range[0] : 0 );
                    $end_range      = floatval( isset( $range[1] ) ? $range[1] : 0 );
                    $price_range    = floatval( isset( $range[2] ) ? $range[2] : 0 );

                    $start_range    = $start_range > 0 ? $start_range : 0;
                    $end_range      = $end_range > 0 ? $end_range : 0;
                    $price_range    = $price_range > 0 ? $price_range : 0;

                    if( !$infinity_end_range && $area >= $start_range && ( $end_range > $start_range || $end_range == 0 ) ){
                        $area_in_range      = ( $area >= $end_range && $end_range != 0 ) ? ( $end_range - $prev_end_range ) : ( $area - $prev_end_range );
                        $measurement_price += $area_in_range * $price_range;
                        $prev_end_range     = $end_range;
                    }

                    if( $end_range == 0 ) $infinity_end_range = true;
                }
                if( isset( $origin_field['general']['mesure_base_qty'] ) && $origin_field['general']['mesure_base_qty'] == 'y' ){
                    $measurement_price  /= $qty;
                }
                return $measurement_price;
            }

            $price_per_unit = $start_range = $end_range = $price_range = 0;
            foreach( $mesure_range as $key => $range ){
                $start_range    = floatval( isset( $range[0] ) ? $range[0] : 0 );
                $end_range      = floatval( isset( $range[1] ) ? $range[1] : 0 );
                $price_range    = floatval( isset( $range[2] ) ? $range[2] : 0 );
                if( $start_range <= $area && ( $area <= $end_range || $end_range == 0 ) ){
                    $price_per_unit = $price_range;
                }
                if( $start_range <= $area && $key == ( count( $mesure_range ) - 1 ) && $area > $end_range  ){
                    $price_per_unit = $price_range;
                }
            }
            if( isset( $origin_field['general']['mesure_type'] ) && $origin_field['general']['mesure_type'] == 'r' ) return $price_per_unit;
            return $_area * $price_per_unit;
        }
        public function calculate_product_area( $fields, $option_fields, $product ){
            $has_dim    = false;
            $area       = 0;

            foreach( $fields as $key => $val ){
                $origin_field = $this->get_field_by_id( $option_fields, $key );
                if( $origin_field['general']['data_type'] == 'i' ){
                    if( isset($origin_field['nbd_type']) && $origin_field['nbd_type'] == 'dimension' && $origin_field['general']['mesure'] == 'y' && isset($origin_field['general']['mesure_range']) && count($origin_field['general']['mesure_range']) > 0 ){
                        $dimension  = explode( "x", $val );
                        $area       = floatval( $dimension[0] ) * floatval( $dimension[1] );
                        if( isset( $origin_field['general']['mesure_min_area'] ) ){
                            $min_area = floatval( $origin_field['general']['mesure_min_area'] );
                            $area  -= $min_area;
                        }
                        $has_dim    = true;
                    }
                }
            }

            if( !$has_dim ){
                $width      = $product->get_width();
                $height     = $product->get_length();
                $area       = floatval( $width ) * floatval( $height );
            }

            return $area > 0 ? $area : 0;
        }
        public function get_quantity_break( $options, $quantity ){
            $quantity_break     = array('index' =>  0, 'oparator' => 'gt');
            $quantity_breaks    = array();
            foreach( $options['quantity_breaks'] as $break ){
                $quantity_breaks[] = absint($break['val']);
            }
            foreach($quantity_breaks as $key => $break){
                if( $key == 0 && $quantity < $break){
                    $quantity_break = array('index' =>  0, 'oparator' => 'lt');
                }
                if( $quantity >= $break && $key < ( count( $quantity_breaks ) - 1 ) ){
                    $quantity_break = array('index' =>  $key, 'oparator' => 'bw');
                }
                if( $key == ( count( $quantity_breaks ) - 1 ) && $quantity >= $break){
                    $quantity_break = array('index' =>  $key, 'oparator' => 'gt');
                }
            }
            return $quantity_break;
        }
        public function set_product_prices( $cart_item ){
            if ( isset( $cart_item['nbo_meta'] )){
                $new_price = (float) $cart_item['nbo_meta']['price'];
                $needed_change = apply_filters('nbo_need_change_cart_item_price', true, $cart_item);
                if( $needed_change ) $cart_item['data']->set_price( $new_price );
            }
            return $cart_item;
        }
        public function nbd_js_object( $args ){
            $args['wc_currency_format_num_decimals'] = wc_get_price_decimals();
            $args['currency_format_num_decimals'] = nbdesigner_get_option( 'nbdesigner_number_of_decimals', 4 );
            $args['currency_format_symbol'] = get_woocommerce_currency_symbol();
            $args['currency_format_decimal_sep'] = stripslashes( wc_get_price_decimal_separator() );
            $args['currency_format_thousand_sep'] = stripslashes( wc_get_price_thousand_separator() );
            $args['currency_format'] = esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format()) );
            $args['nbdesigner_hide_add_cart_until_form_filled'] = nbdesigner_get_option('nbdesigner_hide_add_cart_until_form_filled');
            $args['total'] = __('Total', 'web-to-print-online-designer');
            $args['check_invalid_fields'] = __('Please check invalid fields and quantity input!', 'web-to-print-online-designer');
            $args['ajax_cart'] = nbdesigner_get_option('nbdesigner_enable_ajax_cart', 'no');
            $args['nbo_qv_url'] = add_query_arg(
                urlencode_deep( array(
                    'wc-api'    => 'NBO_Quick_View',
                    'mode'      => 'catalog'
                ) ),
                home_url( '/' )
            );
            return $args;
        }
        public function nbd_depend_js( $depends ){
            $depends[] = 'wc-add-to-cart-variation';
            return $depends;
        }
        public function wp_enqueue_scripts(){
            wp_register_script('angularjs', NBDESIGNER_PLUGIN_URL . 'assets/libs/angular-1.6.9.min.js', array('jquery', 'accounting'), '1.6.9');
            if(nbdesigner_get_option('nbdesigner_enable_angular_js') == 'yes'){
                wp_enqueue_script(array('angularjs'));
            }
            if( nbdesigner_get_option('nbdesigner_enable_ajax_cart', 'no') == 'yes' ){
                wp_enqueue_script( 'wc-add-to-cart-variation' );
            }
        }
        public function show_option_fields(){
            global $product;
            $product_id = $product->get_id();
            $option_id = $this->get_product_option( $product_id );
            if( $option_id ){
                $_options = $this->get_option( $option_id );
                if( $_options ){
                    $options = unserialize($_options['fields']);
                    if( !isset($options['fields']) ){
                        $options['fields'] = array();
                    }
                    $options['fields'] = $this->recursive_stripslashes( $options['fields'] );
                    foreach ( $options['fields'] as $key => $field ){
                        if( !isset( $field['general']['attributes'] ) ){
                            $field['general']['attributes'] = array();
                            $field['general']['attributes']['options'] = array();
                            $options['fields'][$key]['general']['attributes'] = array();
                            $options['fields'][$key]['general']['attributes']['options'] = array();
                        }
                        if( $field['appearance']['change_image_product'] == 'y' ){
                            foreach ( $field['general']['attributes']['options'] as $op_index => $option ){
                                $option['product_image'] = isset($option['product_image']) ? $option['product_image'] : 0;
                                $attachment_id = absint( $option['product_image'] );
                                if( $attachment_id != 0 ){
                                    $image_link         = wp_get_attachment_url( $attachment_id );
                                    $attachment_object  = get_post( $attachment_id );
                                    $full_src           = wp_get_attachment_image_src( $attachment_id, 'large' );
                                    $image_title        = get_the_title( $attachment_id );
                                    $image_alt          = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', TRUE ) ) );
                                    $image_srcset       = function_exists( 'wp_get_attachment_image_srcset' ) ? wp_get_attachment_image_srcset( $attachment_id, 'shop_single' ) : FALSE;
                                    $image_sizes        = function_exists( 'wp_get_attachment_image_sizes' ) ? wp_get_attachment_image_sizes( $attachment_id, 'shop_single' ) : FALSE;
                                    $image_caption      = $attachment_object->post_excerpt;
                                    $options['fields'][$key]['general']['attributes']['options'][$op_index] = array_replace_recursive($options['fields'][$key]['general']['attributes']['options'][$op_index], array(
                                        'imagep'        => 'y',
                                        'image_link'    => $image_link,
                                        'image_title'   => $image_title,
                                        'image_alt'     => $image_alt,
                                        'image_srcset'  => $image_srcset,
                                        'image_sizes'   => $image_sizes,
                                        'image_caption' => $image_caption,
                                        'full_src'      => $full_src[0],
                                        'full_src_w'    => $full_src[1],
                                        'full_src_h'    => $full_src[2]
                                    ));
                                }else{
                                    $options['fields'][$key]['general']['attributes']['options'][$op_index]['imagep'] = 'n';
                                }
                            }
                        }
                        if( isset($field['nbpb_type']) && $field['nbpb_type'] == 'nbpb_com' ){
                            if( isset($field['general']['pb_config']) ){
                                foreach( $field['general']['pb_config'] as $a_index => $attr ){
                                    foreach( $attr as $s_index => $sattr ){
                                        foreach( $sattr['views'] as $v_index => $view ){
                                            $pb_image_obj = wp_get_attachment_url( absint($view['image']) );
                                            $options['fields'][$key]['general']['pb_config'][$a_index][$s_index]['views'][$v_index]['image_url'] =  $pb_image_obj ? $pb_image_obj : NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
                                        }
                                    }
                                }
                            }else{
                                $field['general']['pb_config'] = array();
                            }
                            foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                                if( isset($option['enable_subattr']) && $option['enable_subattr'] == 'on' && isset($option['sub_attributes']) && count($option['sub_attributes']) > 0 ){
                                    foreach( $option['sub_attributes'] as $sa_index => $sattr ){
                                        $options['fields'][$key]['general']['attributes']['options'][$op_index]['sub_attributes'][$sa_index]['image_url'] = nbd_get_image_thumbnail( $sattr['image'] );
                                    }
                                }else{
                                    $options['fields'][$key]['general']['attributes']['options'][$op_index]['image_url'] = nbd_get_image_thumbnail( $option['image'] );
                                }
                            };
                            $options['fields'][$key]['general']['component_icon_url'] = nbd_get_image_thumbnail( $field['general']['component_icon'] );
                        }
                        if( isset($field['general']['attributes']['bg_type']) && $field['general']['attributes']['bg_type'] == 'i' ){
                            foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                                foreach( $option['bg_image'] as $bg_index => $bg ){
                                    $bg_obj = wp_get_attachment_url( absint( $bg ) );
                                    $options['fields'][$key]['general']['attributes']['options'][$op_index]['bg_image_url'][$bg_index] = $bg_obj ? $bg_obj : NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
                                }
                            };
                        }
                        if( isset( $field['nbd_type'] ) && $field['nbd_type'] == 'overlay' ){
                            foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                                foreach( $option['overlay_image'] as $ov_index => $ov ){
                                    $ov_obj = wp_get_attachment_url( absint($ov) );
                                    $options['fields'][$key]['general']['attributes']['options'][$op_index]['overlay_image_url'][$ov_index] = $ov_obj ? $ov_obj : '';
                                }
                            };
                        }
                        if( isset( $field['nbe_type'] ) && $field['nbe_type'] == 'frame' ){
                            foreach ($field['general']['attributes']['options'] as $op_index => $option ){
                                $options['fields'][$key]['general']['attributes']['options'][$op_index]['image_url'] = nbd_get_image_thumbnail( $option['image'] );
                                $fr_obj = wp_get_attachment_url( absint($option['frame_image']) );
                                $options['fields'][$key]['general']['attributes']['options'][$op_index]['frame_image_url'] = $fr_obj ? $fr_obj : NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
                            };
                        }
                    }
                    if( isset( $options['views'] ) ){
                        foreach ($options['views'] as $vkey => $view){
                            $view['base'] = isset($view['base']) ? $view['base'] : 0;
                            $options['views'][$vkey]['base'] = $view['base'];
                            $view_bg_obj = wp_get_attachment_url( absint($view['base']) );
                            $options['views'][$vkey]['base_url'] = $view_bg_obj ? $view_bg_obj : NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
                        }
                    }
                    $product        = wc_get_product( $product_id );
                    $type           = $product->get_type();
                    $variations     = array();
                    $dimensions     = array();
                    $form_values    = array();
                    $cart_item_key  = '';
                    $quantity       = 1;
                    $nbu_item_key   = '';
                    $nbau           = '';
                    $nbdpb_enable   = get_post_meta($product_id, '_nbdpb_enable', true);
                    if($options['quantity_enable'] == 'y'){
                        $quantity = absint($options['quantity_breaks'][0]['val']);
                        foreach( $options['quantity_breaks'] as $break){
                            if( isset( $break['default'] ) && $break['default'] == 'on' ){
                                $quantity = $break['val'];
                            }
                        }
                    }

                    if( isset($_POST['nbd-field']) ){
                        $form_values = $_POST['nbd-field'];
                        if( isset($_POST["nbo-quantity"]) ){
                            $quantity = $_POST["nbo-quantity"];
                        }
                    }else if( isset($_GET['nbo_cart_item_key']) && $_GET['nbo_cart_item_key'] != '' ){
                        $cart_item_key  = $_GET['nbo_cart_item_key'];
                        $cart_item      = WC()->cart->get_cart_item( $cart_item_key );
                        if( isset($cart_item['nbo_meta']) ){
                            $form_values = $cart_item['nbo_meta']['field'];
                        }
                        if ( isset( $cart_item["quantity"] ) ) {
                            $quantity = $cart_item["quantity"];
                        }
                        if( isset( $cart_item['nbau'] ) ){
                            $nbau           = stripslashes( $cart_item['nbau'] );
                            $nbu_item_key   = $cart_item["nbd_item_meta_ds"]["nbu"];
                        }
                    }

                    if( isset( $_GET['nbo_values'] ) ){
                        $params     = array();
                        $value_str  = base64_decode( wc_clean( $_GET['nbo_values'] ) );
                        parse_str( $value_str, $params );
                        if( isset( $params['nbd-field'] ) ){
                            $form_values = $params['nbd-field'];
                        }
                        if ( isset( $params["qty"] ) ) {
                            $quantity = $params["qty"];
                        }
                    }

                    if( $type == 'variable' ){
                        $all = get_posts( array(
                            'post_parent' => $product_id,
                            'post_type'   => 'product_variation',
                            'orderby'     => array( 'menu_order' => 'ASC', 'ID' => 'ASC' ),
                            'post_status' => 'publish',
                            'numberposts' => -1,
                        ));
                        foreach ( $all as $child ) {
                            $vid                = $child->ID;
                            $variation          = wc_get_product( $vid );
                            $variations[$vid]   = $variation->get_price( 'edit' );

                            $width = $height = '';
                            $dimensions[$vid]   = array(
                                'width'     => $variation->get_width(),
                                'height'    => $variation->get_length()
                            );
                        }
                    }
                    $width = $height = '';
                    if( $type != 'variable' ){
                        $width  = $product->get_width();
                        $height = $product->get_length();
                    }

                    $options = apply_filters( 'nbo_product_options', $options, $product_id );

                    ob_start();
                    nbdesigner_get_template('single-product/option-builder.php', array(
                        'product_id'            => $product_id,
                        'options'               => $options,
                        'type'                  => $type,
                        'quantity'              => $quantity,
                        'width'                 => $width,
                        'height'                => $height,
                        'nbdpb_enable'          => $nbdpb_enable,
                        'price'                 => $product->get_price( 'edit' ),
                        'is_sold_individually'  => $product->is_sold_individually(),
                        'variations'            => json_encode( (array) $variations ),
                        'dimensions'            => json_encode( (array) $dimensions ),
                        'form_values'           => $form_values,
                        'cart_item_key'         => $cart_item_key,
                        'nbau'                  => $nbau,
                        'nbu_item_key'          => $nbu_item_key,
                        'change_base'           => nbdesigner_get_option( 'nbdesigner_change_base_price_html', 'no' ),
                        'tooltip_position'      => nbdesigner_get_option( 'nbdesigner_tooltip_position', 'top' ),
                        'hide_zero_price'       => nbdesigner_get_option( 'nbdesigner_hide_zero_price', 'no' )
                    ));
                    $options_form = ob_get_clean();
                    echo $options_form;
                }
            }
        }
        public static function get_product_option( $product_id ){
            $enable = get_post_meta( $product_id, '_nbo_enable', true );
            if( !$enable ) return false;
            $option_id = get_transient( 'nbo_product_'.$product_id );
            if( false === $option_id ){
                global $wpdb;
                $sql = "SELECT id, priority, apply_for, product_ids, product_cats, date_from, date_to FROM {$wpdb->prefix}nbdesigner_options WHERE published = 1";
                $options = $wpdb->get_results($sql, 'ARRAY_A');
                if($options){
                    $_options = array();
                    foreach( $options as $option ){
                        $execute_option = true;
                        $from_date = false;
                        if( isset($option['date_from']) ){
                            $from_date = empty( $option['date_from'] ) ? false : strtotime( date_i18n( 'Y-m-d 00:00:00', strtotime( $option['date_from'] ), false ) );
                        }
                        $to_date = false;
                        if( isset($option['date_to']) ){
                            $to_date = empty( $option['date_to'] ) ? false : strtotime( date_i18n( 'Y-m-d 00:00:00', strtotime( $option['date_to'] ), false ) );
                        }
                        $now  = current_time( 'timestamp' );
                        if ( $from_date && $to_date && !( $now >= $from_date && $now <= $to_date ) ) {
                            $execute_option = false;
                        } elseif ( $from_date && !$to_date && !( $now >= $from_date ) ) {
                            $execute_option = false;
                        } elseif ( $to_date && !$from_date && !( $now <= $to_date ) ) {
                            $execute_option = false;
                        }
                        if( $execute_option ){
                            if( $option['apply_for'] == 'p' ){
                                $products = unserialize($option['product_ids']);
                                $execute_option = in_array($product_id, $products) ? true : false;
                            }else {
                                $categories = $option['product_cats'] ? unserialize($option['product_cats']) : array();
                                $product = wc_get_product($product_id);
                                $product_categories = $product->get_category_ids();
                                $intersect = array_intersect($product_categories, $categories);
                                $execute_option = ( count($intersect) > 0 ) ? true : false;
                            }
                        }
                        if( $execute_option ){
                            $_options[] = $option;
                        }
                    }
                    $_options = array_reverse( $_options );
                    $option_priority = 0;
                    foreach( $_options as $_option ){
                        if( $_option['priority'] > $option_priority ){
                            $option_priority = $_option['priority'];
                            $option_id = $_option['id'];
                        }
                    }
                    if( $option_id ){
                        set_transient( 'nbo_product_'.$product_id , $option_id );
                        
                        $is_artwork_action = get_transient( 'nbo_action_'.$product_id );
                        if( false === $is_artwork_action ){
                            $_selected_options  = self::get_option( $option_id );
                            $selected_options   = unserialize( $_selected_options['fields'] );
                            if ( isset( $selected_options['fields'] ) ) {
                                foreach ($selected_options['fields'] as $key => $field) {
                                    if ( $field['general']['enabled'] == 'y' && isset( $field['nbe_type'] ) && $field['nbe_type'] == 'actions' ) {
                                        $is_artwork_action = true;
                                    }
                                }
                            }
                            if( $is_artwork_action ){
                                set_transient( 'nbo_action_'.$product_id , '1' );
                            }
                        }
                    }
                }
            }
            return $option_id;
        }
        public static function get_option( $id ){
            global $wpdb;
            $sql = "SELECT * FROM {$wpdb->prefix}nbdesigner_options";
            $sql .= " WHERE id = " . esc_sql($id);
            $result = $wpdb->get_results($sql, 'ARRAY_A');
            return count($result[0]) ? $result[0] : false;
        }
        public function bulk_order( $ajax = false, $return = false, $prevent_redirect = false ){
            $bulk_fields = $_REQUEST['nbb-fields'];
            if( !is_array($bulk_fields) ) return false;
            $nbd_field      = isset( $_REQUEST['nbd-field'] ) ? $_REQUEST['nbd-field'] : array();
            $qtys           = $_REQUEST['nbb-qty-fields'];
            $first_field    = reset($bulk_fields);
            // Gather bulk form fields.
            $nbb_fields = array();
            for( $i=0; $i < count($first_field); $i++ ){
                $arr = array();
                foreach($nbd_field as $field_id => $field_value){
                    if( !isset($bulk_fields[$field_id]) ){
                        $arr[$field_id] = $field_value;
                    }
                }
                foreach($bulk_fields as $field_id => $bulk_field){
                    $arr[$field_id] = $bulk_field[$i];
                }
                $nbb_fields[] = $arr;
            }
            $product_id         = isset($_REQUEST['product_id']) ? $_REQUEST['product_id'] : $_REQUEST['nbo-add-to-cart']; 
            $added_count        = 0;
            $failed_count       = 0;
            $success_message    = '';
            $error_message      = '';
            $adding_to_cart     = wc_get_product( $product_id );
            if ( ! $adding_to_cart ) {
                return false;
            }   
            $option_id = $this->get_product_option($product_id);
            if( !$option_id ) return false;
            $variation_id = isset($_REQUEST['variation_id']) ? $_REQUEST['variation_id'] : 0;
            $product_type = $adding_to_cart->get_type();
            $uploaded = false;
            $upload_fields = array();
            /* Gather online design data */
            $nbd_item_cart_key = ($variation_id > 0) ? $product_id . '_' . $variation_id : $product_id;
            $nbd_session = WC()->session->get('nbd_item_key_'.$nbd_item_cart_key);
            $nbu_session = WC()->session->get('nbu_item_key_'.$nbd_item_cart_key);
            $added_cart_item_keys = array();
            if( $product_type == 'variable' ){
                if( $variation_id > 0 ){
                    $missing_attributes = array();
                    $variations = array();
                    try {
                        // Gather posted attributes.
                        $posted_attributes = array();
                        foreach ($adding_to_cart->get_attributes() as $attribute) {
                            if (!$attribute['is_variation']) {
                                continue;
                            }
                            $attribute_key = 'attribute_' . sanitize_title($attribute['name']);
                            if (isset($_REQUEST[$attribute_key])) {
                                if ($attribute['is_taxonomy']) {
                                    // Don't use wc_clean as it destroys sanitized characters.
                                    $value = sanitize_title(wp_unslash($_REQUEST[$attribute_key]));
                                } else {
                                    $value = html_entity_decode(wc_clean(wp_unslash($_REQUEST[$attribute_key])), ENT_QUOTES, get_bloginfo('charset'));
                                }
                                $posted_attributes[$attribute_key] = $value;
                            }
                        }

                        // Check the data we have is valid.
                        $variation_data = wc_get_product_variation_attributes( $variation_id );
                        foreach ( $adding_to_cart->get_attributes() as $attribute ) {
                            if ( ! $attribute['is_variation'] ) {
                                continue;
                            }
                            // Get valid value from variation data.
                            $attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
                            $valid_value   = isset( $variation_data[ $attribute_key ] ) ? $variation_data[ $attribute_key ]: '';
                            /**
                             * If the attribute value was posted, check if it's valid.
                             *
                             * If no attribute was posted, only error if the variation has an 'any' attribute which requires a value.
                             */
                            if ( isset( $posted_attributes[ $attribute_key ] ) ) {
                                $value = $posted_attributes[ $attribute_key ];
                                // Allow if valid or show error.
                                if ( $valid_value === $value ) {
                                    $variations[ $attribute_key ] = $value;
                                } elseif ( '' === $valid_value && in_array( $value, $attribute->get_slugs() ) ) {
                                    // If valid values are empty, this is an 'any' variation so get all possible values.
                                    $variations[ $attribute_key ] = $value;
                                } else {
                                    throw new Exception( sprintf( __( 'Invalid value posted for %s', 'woocommerce' ), wc_attribute_label( $attribute['name'] ) ) );
                                }
                            } elseif ( '' === $valid_value ) {
                                $missing_attributes[] = wc_attribute_label( $attribute['name'] );
                            }
                        }
                        if ( ! empty( $missing_attributes ) ) {
                            throw new Exception( sprintf( _n( '%s is a required field', '%s are required fields', count( $missing_attributes ), 'woocommerce' ), wc_format_list_of_items( $missing_attributes ) ) );
                        }
                    } catch ( Exception $e ) {
                        wc_add_notice( $e->getMessage(), 'error' );
                        return false;
                    }
                    foreach($nbb_fields as $index => $nbb_field){
                        /* Add online design data */
                        if( $nbd_session && ! WC()->session->get('nbd_item_key_'.$nbd_item_cart_key) ) WC()->session->set('nbd_item_key_'.$nbd_item_cart_key, $nbd_session);
                        if( $nbu_session && ! WC()->session->get('nbu_item_key_'.$nbd_item_cart_key) ) WC()->session->set('nbu_item_key_'.$nbd_item_cart_key, $nbu_session);
                        $quantity = $qtys[$index];
                        $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );
                        if( $quantity > 0){
                            if ( $passed_validation ) {
                                if( !$uploaded ){
                                    if( !empty($_FILES) && isset($_FILES["nbd-field"]) ) {
                                        foreach( $_FILES["nbd-field"]['name'] as $field_id => $file ){
                                            if( !isset($nbd_field[$field_id]) ){
                                                $nbd_upload_field = $this->upload_file( $_FILES["nbd-field"], $field_id );
                                                if( !empty($nbd_upload_field) ){
                                                    $upload_fields[$field_id] = $nbd_upload_field[$field_id];
                                                }
                                            }
                                        }
                                    }
                                    $uploaded = true;
                                }
                                $nbb_field = array_merge($nbb_field, $upload_fields);
                                $cart_item_data['nbd-field'] = $nbb_field;
                                $added = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations, $cart_item_data );
                                if ( $added ) {
                                    $added_count ++;
                                    $added_cart_item_keys[] = $added;
                                } else {
                                    $failed_count ++;
                                }
                            }else{
                                $failed_count++;
                            }
                        }else{
                            //$failed_count++;
                            continue;
                        }
                    }
                } else {
                    return false;
                }
            }else{
                foreach($nbb_fields as $index => $nbb_field){
                    /* Add online design data */
                    if( $nbd_session && ! WC()->session->get('nbd_item_key_'.$nbd_item_cart_key) ) WC()->session->set('nbd_item_key_'.$nbd_item_cart_key, $nbd_session);
                    if( $nbu_session && ! WC()->session->get('nbu_item_key_'.$nbd_item_cart_key) ) WC()->session->set('nbu_item_key_'.$nbd_item_cart_key, $nbu_session);
                    $quantity = $qtys[$index];
                    $passed_validation 	= apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
                    if( $quantity > 0){
                        if ( $passed_validation ) {
                            if( !$uploaded ){
                                if( !empty($_FILES) && isset($_FILES["nbd-field"]) ) {
                                    foreach( $_FILES["nbd-field"]['name'] as $field_id => $file ){
                                        if( !isset($nbd_field[$field_id]) ){
                                            $nbd_upload_field = $this->upload_file( $_FILES["nbd-field"], $field_id );
                                            if( !empty($nbd_upload_field) ){
                                                $upload_fields[$field_id] = $nbd_upload_field[$field_id];
                                            }
                                        }
                                    }
                                }
                                $uploaded = true;
                            }
                            $nbb_field = array_merge($nbb_field, $upload_fields);
                            $cart_item_data['nbd-field'] = $nbb_field;
                            $added = WC()->cart->add_to_cart( $product_id, $quantity, 0, array(), $cart_item_data );
                            if ( $added ) {
                                $added_count ++;
                                $added_cart_item_keys[] = $added;
                            } else {
                                $failed_count ++;
                            }
                        }else{
                            $failed_count++;
                        }
                    }else{
                        //$failed_count++;
                        continue;
                    }
                }
            }
            WC()->session->__unset('nbd_item_key_'.$nbd_item_cart_key);
            WC()->session->__unset('nbu_item_key_'.$nbd_item_cart_key);
            if ( $added_count ) {
                nbd_bulk_variations_add_to_cart_message( $added_count );
                $cart_item_keys = implode( '|', $added_cart_item_keys );
                WC()->session->set( 'nbd_cart_item_keys', $cart_item_keys );
            }
            if ( $failed_count ) {
                wc_add_notice( sprintf( __( 'Unable to add %s to the cart.  Please check your quantities and make sure the item is available and in stock', 'web-to-print-online-designer' ), $failed_count ), 'error' );
            }  
            if ( ! $added_count && ! $failed_count ) {
                wc_add_notice( __( 'No product quantities entered.', 'web-to-print-online-designer' ), 'error' );
            }
            if ( $failed_count === 0 && wc_notice_count( 'error' ) === 0 ) {
                if( !$prevent_redirect ){
                    if ( $url = apply_filters( 'woocommerce_add_to_cart_redirect', false ) ) {
                        wp_safe_redirect( $url );
                        exit;
                    } elseif ( get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes' ) {
                        wp_safe_redirect( wc_get_cart_url() );
                        exit;
                    }
                }
            } 
            if( $return ){
                return $added_count;
            }
        }
        public function nbo_ajax_cart( $ajax = true ){
            $posted         = $_POST;
            $results        = array();
            $added_count    = 0;
            if ( isset( $posted['nbb-fields'] ) ){
                $added_count = $this->bulk_order(true, true, false);
            }else{
                $variation_id       = (isset($posted['variation_id']) && $posted['variation_id'] !== 'undefined') ? $posted['variation_id'] : 0;
                $product_id         = isset($posted['product_id']) ? $posted['product_id'] : ( isset($posted['nbd-add-to-cart']) ? $posted['nbd-add-to-cart'] : ( isset($posted['nbo-add-to-cart']) ? $posted['nbo-add-to-cart'] : 0 ) );
                $quantity           = isset($posted['quantity']) ? $posted['quantity'] : 1;
                $adding_to_cart     = wc_get_product( $product_id );
                $product_type       = $adding_to_cart->get_type();
                if( $product_type == 'variable' ){
                    if( $variation_id > 0 ){
                        $missing_attributes = array();
                        $variations = array();
                        try {
                            $posted_attributes = array();
                            foreach ($adding_to_cart->get_attributes() as $attribute) {
                                if (!$attribute['is_variation']) {
                                    continue;
                                }
                                $attribute_key = 'attribute_' . sanitize_title($attribute['name']);
                                if (isset($_REQUEST[$attribute_key])) {
                                    if ($attribute['is_taxonomy']) {
                                        $value = sanitize_title(wp_unslash($_REQUEST[$attribute_key]));
                                    } else {
                                        $value = html_entity_decode(wc_clean(wp_unslash($_REQUEST[$attribute_key])), ENT_QUOTES, get_bloginfo('charset'));
                                    }
                                    $posted_attributes[$attribute_key] = $value;
                                }
                            }
                            $variation_data = wc_get_product_variation_attributes( $variation_id );
                            foreach ( $adding_to_cart->get_attributes() as $attribute ) {
                                if ( ! $attribute['is_variation'] ) {
                                    continue;
                                }
                                $attribute_key = 'attribute_' . sanitize_title( $attribute['name'] );
                                $valid_value   = isset( $variation_data[ $attribute_key ] ) ? $variation_data[ $attribute_key ]: '';                       
                                if ( isset( $posted_attributes[ $attribute_key ] ) ) {
                                    $value = $posted_attributes[ $attribute_key ];
                                    if ( $valid_value === $value ) {
                                        $variations[ $attribute_key ] = $value;
                                    } elseif ( '' === $valid_value && in_array( $value, $attribute->get_slugs() ) ) {
                                        $variations[ $attribute_key ] = $value;
                                    } else {
                                        throw new Exception( sprintf( __( 'Invalid value posted for %s', 'woocommerce' ), wc_attribute_label( $attribute['name'] ) ) );
                                    }
                                } elseif ( '' === $valid_value ) {
                                    $missing_attributes[] = wc_attribute_label( $attribute['name'] );
                                }
                            }
                            if ( ! empty( $missing_attributes ) ) {
                                throw new Exception( sprintf( _n( '%s is a required field', '%s are required fields', count( $missing_attributes ), 'woocommerce' ), wc_format_list_of_items( $missing_attributes ) ) );
                            }
                        } catch ( Exception $e ) {
                            wc_add_notice( $e->getMessage(), 'error' );
                        }
                        $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id, $variations );
                        if ($quantity > 0) {
                            if ($passed_validation) {
                                $added = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variations, array());
                                if ( $added ) $added_count ++;
                            }
                        }
                    }
                }else{
                    $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
                    if ($passed_validation) {
                        $added = WC()->cart->add_to_cart( $product_id, $quantity, 0, array() );
                        if ( $added ) $added_count ++;
                    }
                }
            }
            if( $added_count > 0 ){
                $results = array(
                    'result'   => 'success',
                    'messages' => sprintf( __( '%s products successfully added to your cart.', 'web-to-print-online-designer' ), $added_count ),
                );
            }else{
                $results = array(
                    'result'   => 'failure',
                    'messages' => __('No product has been added.', 'web-to-print-online-designer'),
                );
            }
            if( $ajax !== false ){
                ob_start();
                woocommerce_mini_cart();
                $mini_cart = ob_get_clean();
                $results['data'] = array(
                    'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array(
                        'div.widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>',
                    )),
                    'cart_hash' => apply_filters('woocommerce_add_to_cart_hash', WC()->cart->get_cart_for_session() ? md5(json_encode(WC()->cart->get_cart_for_session())) : '', WC()->cart->get_cart_for_session()),
                );
                if ( $url = apply_filters( 'woocommerce_add_to_cart_redirect', false ) ) {
                    $results['redirect'] = $url;
                } elseif ( get_option( 'woocommerce_cart_redirect_after_add' ) === 'yes' ) {
                    $results['redirect'] = $url;
                }
                wp_send_json( $results );
                exit();
            }else{
                return $added_count;
            }
        }
        public function quick_view(){
            global $woocommerce, $post;
            $product_id = absint( $_GET['product'] );
            $mode       = isset( $_GET['mode'] ) ? $_GET['mode'] : 'editor';
            if ( $product_id ) {
                $post = get_post($product_id);
                $type = nbdesigner_get_option('nbdesigner_display_product_option');
                setup_postdata($post);
                if($type == '1' || $mode == 'catalog' ){
                    nbdesigner_get_template('quick-view.php', array());
                }else{
                    nbdesigner_get_template('quick-view-tab.php', array());
                }
                exit;
            }
            exit;
        }
        public function nbo_get_product_variations(){
            if ( !isset( $_POST['product_id'] ) || empty( $_POST['product_id'] ) ) {
                wp_send_json_error();
                die();
            }
    
            $product    = wc_get_product( $_POST['product_id'] );
            $variations = $this->get_available_variations( $product );
    
            wp_send_json_success( $variations );
            die();
        }
        private function get_available_variations( $product ) {
            global $wpdb;
            
            $transient_name = 'nbo_available_variations_' . $product->get_id();
            $transient_data = get_transient($transient_name);
            if ( !empty( $transient_data ) ){
                return $transient_data;
            }
            
            $available_variations = array();
    
            //Get the children all in one call.
            //This will prime the WP_Post cache so calls to get_child are much faster. 
    
            $args = array(
                'post_parent'       => $product->get_id(),
                'post_type'         => 'product_variation',
                'orderby'           => 'menu_order',
                'order'             => 'ASC',
                'post_status'       => 'publish',
                'numberposts'       => -1,
                'no_found_rows'     => true
            );
            $children = get_posts( $args );
    
            foreach ( $children as $child ) {
                $variation = wc_get_product( $child );
    
                // Hide out of stock variations if 'Hide out of stock items from the catalog' is checked
                $id = $variation->get_id();
                if ( empty( $id ) || ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) && !$variation->is_in_stock() ) ) {
                    continue;
                }
    
                // Filter 'woocommerce_hide_invisible_variations' to optionally hide invisible variations (disabled variations and variations with empty price)
                if ( apply_filters( 'woocommerce_hide_invisible_variations', false, $product->get_id(), $variation ) && !$variation->variation_is_visible() ) {
                    continue;
                }
    
                $available_variations[] = array(
                    'variation_id'          => $variation->get_id(),
                    'variation_is_active'   => $variation->variation_is_active(),
                    'attributes'            => $variation->get_variation_attributes(),
                );
            }
            set_transient( $transient_name, $available_variations, DAY_IN_SECONDS * 30 );
            return $available_variations;
        }
        public function on_delete_product_transients( $product_id ){
            delete_transient( 'nbo_available_variations_' . $product_id );
        }
        public function nbo_dropdown_variation_attribute_options_args( $args ){
            if( $args['attribute'] && $args['product'] instanceof WC_Product ) {
                $product            = $args['product'];
                $attribute          = $args['attribute'];
                $nbo_enable_mapping = $product->get_meta('_enable_nbo_mapping', true);
                $nbo_maps           = $product->get_meta('_nbo_maps', true);

                if( !isset( $args['class'] ) ) $args['class'] = '';

                if( $nbo_enable_mapping && !empty( $nbo_maps ) ){
                    $st_name        = sanitize_title( $attribute );
                    $hashed_name    = md5( $st_name );

                    if( isset( $nbo_maps[ $hashed_name ] ) && $nbo_maps[ $hashed_name ] != '' ){
                        $args['class'] .= ' nbo-mapping-select nbo_field_id-' . $nbo_maps[ $hashed_name ];
                    }else{
                        $args['class'] .= ' nbo-default-select';
                    }
                }else{
                    $args['class'] .= ' nbo-default-select';
                }
            }
            return $args;
        }
        public function add_to_cart_fragments( $fragments ){
            $fragments['nbo_cart_item_count'] = WC()->cart->get_cart_contents_count();
            return $fragments;
        }
		//custom quantity rule and breaks
		private function generateArray($min, $max, $step) {
			$result = array();
			for ($i = 0; $min + ($step * $i) <= $max; $i++) {
				$result[] = $min + ($step * $i);
			}
			return $result;
        }
    }
}
$nbd_fontend_printing_options = NBD_FRONTEND_PRINTING_OPTIONS::instance();
$nbd_fontend_printing_options->init();