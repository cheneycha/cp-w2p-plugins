<?php
defined( 'ABSPATH' ) || exit;
class NBD_API {
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
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ), 10 );
        add_action( 'nbd_init_files_and_folders', array( &$this, 'create_gallery_folder' ) );
        //add_filter( 'query_vars', array( &$this, 'query_vars' ) );
        //add_filter( 'generate_rewrite_rules', array( &$this, 'rewrite_rules' ) );
        //add_action( 'template_redirect', array( &$this, 'template_redirect' ) );
        add_filter( 'nbdesigner_appearance_settings', array($this, 'api_settings'), 20, 1 );
        add_filter( 'nbdesigner_default_settings', array( $this, 'default_settings' ), 20, 1 );
        if( nbdesigner_get_option( 'nbdesigner_enable_gallery_api', 'no' ) == 'yes' ){
            add_action( 'woocommerce_before_single_product', array( $this, 'maybe_chagne_product_gallery' ) );
            add_action( 'woocommerce_before_shop_loop', array( $this, 'maybe_chagne_product_gallery' ) );
            add_action( 'save_post', array( $this, 'maybe_clear_galleries' ) );
            add_action( 'nbo_save_print_option', array( $this, 'clear_galleries_after_save_option' ), 10, 1 );
            add_action( 'after_nbd_save_customer_design', array( $this, 'clear_galleries_after_save_design' ), 30, 1 );
        }
    }
    public function create_gallery_folder() {
        Nbdesigner_IO::mkdir( NBDESIGNER_DATA_DIR . '/gallery' );
    }
    public function api_settings( $settings ) {return $settings;
        $settings['misc'][] = array(
            'title'         => __( 'Enable generate gallery API', 'web-to-print-online-designer'),
            'description'   => __( 'This option will generate and update product gallery base on NBD template when the customer change product options.', 'web-to-print-online-designer'),
            'id'            => 'nbdesigner_enable_gallery_api',
            'default'       => 'no',
            'type'          => 'radio',
            'options'       => array(
                'yes'    => __('Yes', 'web-to-print-online-designer'),
                'no'     => __('No', 'web-to-print-online-designer'),
            )
        );
        return $settings;
    }
    public function default_settings( $settings ) {
        $settings['nbdesigner_enable_gallery_api'] = '';
        return $settings;
    }
    public function query_vars( $wp_vars ) {
        $wp_vars[] = 'nbd_api';
        $wp_vars[] = 'api_request';
        return $wp_vars;
    }
    public function rewrite_rules( $wp_rewrite ) {
        $wp_rewrite->rules = array_merge(
            ['nbd_api/gallery/(.+)/?$' => 'index.php?nbd_api=gallery_generate&api_request=$matches[1]'],
            $wp_rewrite->rules
        );
    }
    public function template_redirect() {
        global $wp_query;
        if( isset( $wp_query->query_vars['nbd_api'] ) ){
            $api_type       = $wp_query->query_vars['nbd_api']; 
            $api_request    = $wp_query->query_vars['api_request'];
            switch ( $api_type ){
                case 'gallery_generate':
                    $this->get_gallery_image( $api_request );
                    break;
            }
            exit;
        }
    }
    public function register_rest_routes() {
        register_rest_route( 'nbd/v1/gallery', '/generate' , array(
            array(
                'methods'   => 'GET',
                'callback'  => array( $this, 'generate_gallery' ),
                'permission_callback' => '__return_true'
            ),
        ) );
    }
    public function generate_gallery( WP_REST_Request $request ){
        $api_request    = $request->get_param( 'request' );
        $stored         = $request->get_param( 'stored' );
        $folder         = $request->get_param( 'folder' );
        $data = array( 'flag'   =>  0 );
        if( $api_request != '' ){
            $gallery_path = ( $stored == '1' ) ? NBDESIGNER_CUSTOMER_DIR . '/' . $folder . '/preview' : NBDESIGNER_DATA_DIR . '/gallery/' . $api_request;
            $this->generate_gallery_images( $api_request, $stored, $folder );
            $images = Nbdesigner_IO::get_list_files_by_type( $gallery_path, 1, 'png' );
            if( count( $images ) ){
                $data['flag'] = 1;
                ksort( $images );
                foreach($images as $key => $image){
                    list( $width, $height ) = getimagesize( $image );
                    $gallery[] = array(
                        'src'   => Nbdesigner_IO::wp_convert_path_to_url( $image ),
                        'width' => $width,
                        'height' => $height,
                        'title' => '',
                        'sizes' => $width . 'x' . $height
                    );
                }
                $data['gallery'] = $gallery;
            }
        }
        wp_send_json( $data );
    }
    public function generate_gallery_images( $api_request, $stored, $folder ){
        $requests = explode( '|', base64_decode( $api_request ) );
        $requests_arr = array();
        $product_id = 0;
        $template_folder = '';
        foreach ( $requests as $request ){
            $arr = explode( ',', $request);
            if( $arr[0] == 'product_id' ){
                $product_id = $arr[1];
            } else if( $arr[0] == 'template' ){
                $template_folder = $arr[1];
            } else {
                $requests_arr[ $arr[0] ] = array(
                    $arr[1] => $arr[2]
                );
            }
        }
        if( $product_id == 0 || $template_folder == '' ) return false;
        $option_id = get_transient( 'nbo_product_'.$product_id );
        if( $option_id ){
            $_options = $this->get_option($option_id);
            if( $_options ){
                $options = unserialize( $_options['fields'] );
                if( isset( $options['fields'] ) ){
                    $option_fields      = $options['fields'];
                    $template_path      = NBDESIGNER_CUSTOMER_DIR . '/' . $template_folder;
                    $nbd_settings       = array();
                    if( $stored == '1' ){
                        $setting_path   = $template_path . '/config.json';
                        $config         = json_decode( file_get_contents( $setting_path ) );
                        foreach ( $config->product as $side ) {
                            $nbd_settings[] = (array)$side;
                        }
                    } else {
                        $nbd_settings   = unserialize(get_post_meta($product_id, '_designer_setting', true));
                    }
                    $template_images    = Nbdesigner_IO::get_list_files_by_type( $template_path, 1, 'png' );
                    $path_dst           = ( $stored == '1' ) ? NBDESIGNER_CUSTOMER_DIR . '/' . $template_folder . '/preview' : NBDESIGNER_DATA_DIR . '/gallery/' . $api_request;
                    ksort( $template_images );
                    $template_images = array_values( $template_images );
                    if( file_exists( $path_dst ) ){
                        Nbdesigner_IO::delete_folder( $path_dst );
                    }
                    wp_mkdir_p( $path_dst );
                    foreach( $requests_arr as $key => $req ){
                        if( $key == 'color' ){
                            $field_val      = reset( $req );
                            $field_id       = key( $req );
                            $origin_field   = $this->get_field_by_id( $option_fields, $field_id );
                            $bg_images      = $origin_field['general']['attributes']['options'][$field_val]['bg_image'];
                            $width = nbdesigner_get_option( 'nbdesigner_thumbnail_width', 500 );
                            foreach( $bg_images as $k => $bg_image ){
                                if( $bg_image != 0 && isset( $nbd_settings[ $k ] ) && isset( $template_images[ $k ] ) ){
                                    $bg_image_path = get_attached_file( $bg_image );
                                    if( $stored == '1' ) $config->product[ $k ]->img_src = wp_get_attachment_url( $bg_image );
                                    $this->create_gallery_image( $nbd_settings[ $k ], $template_images[ $k ], $bg_image_path, $width, $path_dst );
                                }
                            }
                        }
                    }
                    if( $stored == '1' ){
                        file_put_contents( $setting_path, stripslashes( json_encode( $config ) ) );
                    }
                }
            }
        }
    }
    public function create_gallery_image( $setting, $design, $bg_image_path, $width, $path_dst ){
        $scale              = $width / 500;
        $bg_width           = $setting["img_src_width"] * $scale;
        $bg_height          = $setting["img_src_height"] * $scale;
        $ds_width           = $setting["area_design_width"] * $scale;
        $ds_height          = $setting["area_design_height"] * $scale;
        $image_design       = NBD_Image::nbdesigner_resize_imagepng($design, $ds_width, $ds_height);
//        imagealphablending($image_design, false);
//        imagefilter($image_design, IMG_FILTER_COLORIZE, 0,0,0,127*0.7);
        
        $image_product_ext  = pathinfo( $bg_image_path );
        if($image_product_ext['extension'] == "png"){
            $image_product = NBD_Image::nbdesigner_resize_imagepng( $bg_image_path, $bg_width, $bg_height );
        } else {
            $image_product = NBD_Image::nbdesigner_resize_imagejpg( $bg_image_path, $bg_width, $bg_height );
        }
        if( $setting["show_overlay"] == '1' ){
            $overlay_path   = is_numeric( $setting['img_overlay'] ) ? get_attached_file( $setting['img_overlay'] ) : Nbdesigner_IO::convert_url_to_path( $setting['img_overlay'] );
            $overlay_ext    = strtolower( pathinfo( $setting["img_overlay"] )['extension'] );
            if( $overlay_ext == "png" ){
                $image_overlay = NBD_Image::nbdesigner_resize_imagepng($overlay_path, $ds_width, $ds_height);
            } else if( $overlay_ext == "jpg" || $overlay_ext == "jpeg" ){
                $image_overlay = NBD_Image::nbdesigner_resize_imagejpg($overlay_path, $ds_width, $ds_height);
            } else {
                $setting["show_overlay"] = '0';
            }
        }
        $image = imagecreatetruecolor( $bg_width, $bg_height );
        imagesavealpha( $image, true );
        $color = imagecolorallocatealpha( $image, 255, 255, 255, 127 );
        imagefill( $image, 0, 0, $color );
        imagecopy( $image, $image_product, 0, 0, 0, 0, $bg_width, $bg_height );
        imagecopy( $image, $image_design, ( $setting["area_design_left"] - $setting["img_src_left"] ) * $scale, ( $setting["area_design_top"] - $setting["img_src_top"] ) * $scale, 0, 0, $ds_width, $ds_height );
        if( $setting["show_overlay"] == '1' ){
            imagecopy( $image, $image_overlay, ( $setting["area_design_left"] - $setting["img_src_left"] ) * $scale, ( $setting["area_design_top"] - $setting["img_src_top"] ) * $scale, 0, 0, $ds_width, $ds_height );
        }
        imagepng( $image, $path_dst. '/' . basename( $design ) );
        imagedestroy( $image );
        imagedestroy( $image_design );
        imagedestroy( $image_product );
    }
    public function maybe_clear_galleries( $post_id ){
        if (!isset($_POST['nbdesigner_setting_box_nonce']) || !wp_verify_nonce($_POST['nbdesigner_setting_box_nonce'], 'nbdesigner_setting_box')
            || !(current_user_can('administrator') || current_user_can('shop_manager'))) {
            return $post_id;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return $post_id;
        }
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id)) {
                return $post_id;
            }
        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return $post_id;
            }
        }
        $this->clear_galleries( $post_id );
    }
    public function clear_galleries_after_save_option( $arr ){
        $product_ids = unserialize( $arr['product_ids'] );
        foreach( $product_ids as $product_id ){
            $this->clear_galleries( $product_id );
        }
    }
    public function clear_galleries_after_save_design( $result ){
        $task = ( isset( $_POST['task'] ) && $_POST['task'] != '' ) ? $_POST['task'] : 'new';
        $design_type = ( isset( $_POST['design_type'] ) && $_POST['design_type'] != '' ) ? $_POST['design_type'] : '';  
        if( ( $task == 'create' && $design_type != 'art' ) || ( $task == 'edit' && $design_type == 'template' ) ){
            $product_id = $result['product_id'];
            $folder = $result['folder'];
            $default_folder = '';
            $template = nbd_get_templates( $product_id, 0, '', true );
            if( isset( $template[0] ) ){
                $default_folder = $template[0]['folder'];
            }
            if( $default_folder == $folder ){
                $this->clear_galleries( $product_id );
            }
        }
    }
    public function clear_galleries( $product_id ){
        if( is_nbdesigner_product( $product_id ) ){
            $template = nbd_get_templates( $product_id, 0, '', true );
            if( isset( $template[0] ) ){
                $tem = $template[0]['folder'];
                if( $tem != '' ){
                    $option_id = get_transient( 'nbo_product_'.$product_id );
                    if( $option_id ){
                        $_options = $this->get_option($option_id);
                        if( $_options ){
                            $options = unserialize( $_options['fields'] );
                            if( isset( $options['fields'] ) ){
                                $option_fields = $options['fields'];
                                foreach ($option_fields as $field){
                                    if( isset( $field['nbd_type'] ) && $field['nbd_type'] == 'color' ){
                                        if( count( $field['general']['attributes']['options'] ) > 0 ){
                                            foreach ( $field['general']['attributes']['options'] as $key => $opt ){
                                                $folder = base64_encode( 'product_id,' . $product_id . '|template,' . $tem . '|color,' . $field['id'] . ',' . $key );
                                                $path = NBDESIGNER_DATA_DIR . '/gallery/' . $folder;
                                                if( file_exists( $path ) ){
                                                    Nbdesigner_IO::delete_folder( $path );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    public function get_option( $id ){
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}nbdesigner_options";
        $sql .= " WHERE id = " . esc_sql($id);
        $result = $wpdb->get_results($sql, 'ARRAY_A');
        return count($result[0]) ? $result[0] : false;
    }
    public function get_field_by_id( $option_fields, $field_id ){
        foreach($option_fields as $key => $field){
            if( $field['id'] == $field_id ) return $field;
        }
    }
    private function display_gallery_image( $image_path ){
        $handle = fopen($image_path, "rb");
        $contents = fread($handle, filesize($image_path));
        fclose($handle);
        header("content-type: image/png");
        echo $contents;
    }
    public function get_gallery_image( $api_request ){
        //todo
    }
    public function get_gallery_folder( $product_id ){
        $tem = '';
        if( isset( $_GET['nbo_cart_item_key'] ) ){
            $cart_item_key = $_GET['nbo_cart_item_key'];
            if( isset( WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds'] ) ){
                $tem = WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds']['nbd'];
                return $tem;
            }
        }
        $template = nbd_get_templates( $product_id, 0, '', true );
        if( isset( $template[0] ) ){
            $tem = $template[0]['folder'];
        }
        return $tem;
    }
    public function maybe_chagne_product_gallery(){
        if( is_singular( 'product' ) ){
            $product_id = get_the_ID();
            if( is_nbdesigner_product( $product_id ) ){
                $tem = $this->get_gallery_folder( $product_id );
                if( $tem != '' ){
                    global $product;
                    if( $product->get_image_id() ){
                        remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
                        add_action( 'woocommerce_product_thumbnails', array( $this, 'show_product_thumbnails' ), 20 );
                    }
                }
            }
            remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
            add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'shop_loop_product_thumbnail' ), 10 );
        } else if( is_product_category() ){
            remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
            add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'shop_loop_product_thumbnail' ), 10 );
        }
    }
    function shop_loop_product_thumbnail(){
        global $product;
        $product_id = $product->get_id();
        if( is_nbdesigner_product( $product_id ) ){
            $template = nbd_get_templates( $product_id, 0, '', true );
            if( isset( $template[0] ) ){
                $template_folder = $template[0]['folder'];
                $template_path = NBDESIGNER_CUSTOMER_DIR . '/' . $template_folder . '/preview';
                $images = Nbdesigner_IO::get_list_images( $template_path, 1 );
                ksort($images);
                if( count( $images ) ){
                    $image = $images[0];
                    list($width, $height)   = getimagesize( $image );
                    $full_src               = $thumbnail_src = Nbdesigner_IO::wp_convert_path_to_url( $image );
                    $image_html = '<img class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail" src="' . $full_src . '" />';
                    echo $image_html;
                }else{
                    echo woocommerce_get_product_thumbnail();
                }
            }else{
                echo woocommerce_get_product_thumbnail();
            }
        }else{
            echo woocommerce_get_product_thumbnail();
        }
    }
    public function show_product_thumbnails(){
        global $product;
        $product_id         = $product->get_id();
        $template_folder    = $this->get_gallery_folder( $product_id );
        $template_path      = NBDESIGNER_CUSTOMER_DIR . '/' . $template_folder . '/preview';
        $images             = Nbdesigner_IO::get_list_images( $template_path, 1 );
        ksort($images);
        $nbd_settings       = unserialize(get_post_meta($product_id, '_designer_setting', true));
        $html               = '';
        $thumbnail_size     = $image_size = $full_size = nbdesigner_get_option( 'nbdesigner_template_width', 500 );
        foreach($images as $key => $image_path){
            list($width, $height)   = getimagesize( $image_path );
            $full_src               = $thumbnail_src = Nbdesigner_IO::wp_convert_path_to_url( $image_path );
            $image_caption          = isset( $nbd_settings[$key] ) ? $nbd_settings[$key]['orientation_name'] : '';
            $attr                   = array(
                'title'                     => $image_caption,
                'data-caption'              => $image_caption,
                'data-src'                  => esc_url( $full_src ),
                'data-large_image'          => esc_url( $full_src ),
                'data-large_image_width'    => $width,
                'data-large_image_height'   => $height,
                'class'                     => '',
            );
            $size_class   = $width . 'x' . $height;
            $default_attr = array(
                'src'   => $full_src,
                'class' => "attachment-$size_class size-$size_class",
                'alt'   => $image_caption
            );
            $attr       = wp_parse_args( $attr, $default_attr );
            $attr       = array_map( 'esc_attr', $attr );
            $hwstring   = image_hwstring( $width, $height );
            $image_html = rtrim( "<img $hwstring" );
            foreach ( $attr as $name => $value ) {
                $image_html .= " $name=" . '"' . $value . '"';
            }
            $image_html .= ' />';
            $html       .= '<div data-thumb="' . esc_url( $thumbnail_src ) . '" data-thumb-alt="' . esc_attr( $image_caption ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( $full_src ) . '">' . $image_html . '</a></div>';
        }
        echo $html;
    }
}
$nbd_api = NBD_API::instance();
$nbd_api->init();