<?php 
if (!defined('ABSPATH')) exit;
//use Dompdf\Dompdf;
class NBDesigner_Advanced_Upload {
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
        if( nbdesigner_get_option( 'nbdesigner_upload_popup_style', 's' ) == 'a' ){
            add_action( 'nbu_after_upload_settings',  array($this, 'advanced_upload_settings'), 10, 3 );
            add_action( 'nbu_upload_image_from_url', array($this, 'adu_process_image_from_url'), 10, 2 );
            add_action( 'nbu_upload_design_file', array($this, 'get_extra_upload_file_info'), 10, 3 );
            $this->ajax();
            add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 30, 4 );
            add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 30, 2 );
            add_filter( 'woocommerce_order_again_cart_item_data', array( $this, 'order_again_cart_item_data' ), 30, 3 );
            add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'order_line_item' ), 30, 3 );
            add_filter( 'nbu_cart_item_html', array( $this, 'nbu_cart_item_html' ), 10, 3 );
            add_filter( 'nbu_cart_item_reup_link', array( $this, 'nbu_cart_item_reup_link' ), 10, 4 );
            add_filter( 'nbu_cart_item_upload_link', array( $this, 'nbu_cart_item_upload_link' ), 10, 4 );
            add_filter( 'nbu_order_item_reup_link', array( $this, 'nbu_order_item_reup_link' ), 10, 4 );
            add_filter( 'nbu_order_item_html', array( $this, 'nbu_order_item_html' ), 10, 4 );
            add_action( 'nbu_update_upload_files', array( $this, 'nbu_update_upload_files' ) );
            add_action( 'nbu_download_upload_files', array( $this, 'nbu_download_upload_files' ), 10, 2 );
            add_filter( 'woocommerce_hidden_order_itemmeta', array($this, 'hidden_order_itemmeta'));
            //add_filter( 'woocommerce_email_attachments', array(&$this, 'attach_pdf_to_admin_email'), 20, 3 );
        }
        add_action( 'template_redirect', array( $this, 'template_redirect' ) );
        add_filter( 'nbdesigner_default_settings', array( $this, 'default_settings' ), 20, 1 );
        add_filter( 'nbdesigner_general_settings', array( $this, 'setting_advanced_upload_page' ), 20, 1 );
        add_filter( 'display_post_states', array( $this, 'add_display_post_states' ), 20, 2 );
        add_action('nbd_create_pages', array($this, 'create_pages'));
        if( nbdesigner_get_option( 'nbdesigner_show_popup_od_option_in_cat', 'no' ) == 'yes' ){
            add_action('wp_footer', array( $this, 'print_archive_popup_option' ) );
            add_filter( 'nbd_loop_start_design_btn_class', function( $class ){
                $class .= ' nbu_arc_trigger_pop';
                return $class;
            });
        }
    }
    public function ajax(){
        $ajax_events = array(
            'nbu_crop_image'        => true,
            'nbu_crop_images'       => true,
            'nbu_save_upload_files' => true
        );
        foreach ($ajax_events as $ajax_event => $nopriv) {
            add_action('wp_ajax_' . $ajax_event, array($this, $ajax_event));
            if ($nopriv) {
                add_action('wp_ajax_nopriv_' . $ajax_event, array($this, $ajax_event));
            }
        }
    }
    public function default_settings( $settings ){
        $settings['nbdesigner_advanced_upload_page_id']         = 0;
        $settings['nbdesigner_simple_upload_page_id']           = 0;
        $settings['nbdesigner_show_popup_od_option_in_cat']     = 'no';
        return $settings;
    }
    public function setting_advanced_upload_page( $settings ){
        $settings['nbd-pages'][] = array(
            'title'             => __( 'Advanced upload page', 'web-to-print-online-designer'),
            'description'       => __( 'Choose advanced upload page.', 'web-to-print-online-designer'),
            'id'                => 'nbdesigner_advanced_upload_page_id',
            'type'              => 'select',
            'default'           => nbd_get_page_id( 'advanced_upload' ),
            'options'           => nbd_get_pages()
        );
        $settings['nbd-pages'][] = array(
            'title'             => __( 'Upload design file page', 'web-to-print-online-designer'),
            'description'       => __( 'Choose simple upload page.', 'web-to-print-online-designer'),
            'id'                => 'nbdesigner_simple_upload_page_id',
            'type'              => 'select',
            'default'           => nbd_get_page_id( 'simple_upload' ),
            'options'           => nbd_get_pages()
        );
        return $settings;
    }
    public function create_pages(){
        $nbu_upload_page_id = nbd_get_page_id( 'advanced_upload' );
        if ( $nbu_upload_page_id == -1 || !get_post( $nbu_upload_page_id ) ){
            $post = array(
                'post_name'         => 'upload-design-file',
                'post_status'       => 'publish',
                'post_title'        => __('Upload design file', 'web-to-print-online-designer'),
                'post_type'         => 'page',
                'post_author'       => 1,
                'post_content'      => '',
                'comment_status'    => 'closed',
                'post_date' => date('Y-m-d H:i:s')
            );
            $nbu_upload_page_id = wp_insert_post($post, false);	
            update_option( 'nbdesigner_advanced_upload_page_id', $nbu_upload_page_id );         
        }
        $nbu_simple_upload_page_id = nbd_get_page_id( 'simple_upload' );
        if ( $nbu_simple_upload_page_id == -1 || !get_post( $nbu_simple_upload_page_id ) ){
            $post = array(
                'post_name'         => 'upload-your-file',
                'post_status'       => 'publish',
                'post_title'        => __('Upload design file', 'web-to-print-online-designer'),
                'post_type'         => 'page',
                'post_author'       => 1,
                'post_content'      => '',
                'comment_status'    => 'closed',
                'post_date' => date('Y-m-d H:i:s')
            );      
            $nbu_simple_upload_page_id = wp_insert_post($post, false);	
            update_option( 'nbdesigner_simple_upload_page_id', $nbu_simple_upload_page_id );         
        }
    }
    public function add_display_post_states( $post_states, $post ){
        if ( nbd_get_page_id( 'advanced_upload' ) === $post->ID ) {
            $post_states['nbd_upload_page'] = __( 'NBD Advanced upload Page', 'web-to-print-online-designer' );
        }
        if ( nbd_get_page_id( 'simple_upload' ) === $post->ID ) {
            $post_states['nbd_simple_upload_page'] = __( 'NBD upload file Page', 'web-to-print-online-designer' );
        }
        return $post_states; 
    }
    public function print_archive_popup_option(){
        ob_start();
        nbdesigner_get_template( 'od-options-popup.php', array() );
        $content = ob_get_clean();
        echo $content;
    }
    public function template_redirect(){
        if( is_page( nbd_get_page_id( 'advanced_upload' ) ) ){
            include(NBDESIGNER_PLUGIN_DIR . 'views/upload/advanced-upload-page.php');exit();
        }
        if( is_page( nbd_get_page_id( 'simple_upload' ) ) ){
            include(NBDESIGNER_PLUGIN_DIR . 'views/upload/simple-upload-page.php');exit();
        }
    }
    public function adu_process_image_from_url( $path, $result ){
        if( $result['flag'] == 1 && isset( $_POST['nbu_adu'] ) && $_POST['nbu_adu'] == 1 && isset( $_POST['product_id'] ) && $_POST['product_id'] != '' ){
            $path                   = is_array( $path ) ? $path['full_path'] : $path;
            $dpi                    = nbd_get_dpi( $path );
            list($width, $height)   = getimagesize( $path );
            $first_time             = isset( $_POST['first_time'] ) ? $_POST['first_time'] : 1;
            $variation_id           = isset( $_POST['variation_id'] ) ? absint( $_POST['variation_id'] ) : 0;
            $product_id             = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0;
            $nbd_item_cart_key      = ($variation_id > 0) ? $product_id . '_' . $variation_id : $product_id;
            $task                   = $_POST['task'];
            if(isset($_POST['nbu_item_key']) && $_POST['nbu_item_key'] != ''){
                $nbu_item_key = $_POST['nbu_item_key'];
            }else {   
                $nbu_item_session = WC()->session->get('nbu_item_key_'.$nbd_item_cart_key);
                $nbu_item_key = isset($nbu_item_session) ? $nbu_item_session : substr(md5(uniqid()),0,5).rand(1,100).time();
            }
            $prefix             = substr(md5(uniqid()),0,5).rand(1,100).time();
            $name               = pathinfo( $path, PATHINFO_BASENAME );
            $ext                = pathinfo( $path, PATHINFO_EXTENSION );
            $name               = $prefix.$name;
            $new_path_dir       = NBDESIGNER_UPLOAD_DIR . '/' .$nbu_item_key;
            $new_path           = $new_path_dir . '/' . $name;
            $new_path_preview   = $new_path_dir . '_preview/' . $name;
            $preview_url        = Nbdesigner_IO::wp_convert_path_to_url( $new_path_preview );
            if( $task == 'new' && $first_time == 1 ){
                if( file_exists( $new_path_dir.'_old' ) ) Nbdesigner_IO::delete_folder( $new_path_dir.'_old' );
                if( file_exists( $new_path_dir ) ){
                    rename( $new_path_dir, $new_path_dir.'_old' );
                    wp_mkdir_p( $new_path_dir );
                }else{
                    wp_mkdir_p( $new_path_dir );
                }
            }
            if ( copy( $path, $new_path ) ) {
                if( !file_exists( $new_path_dir . '_preview/' ) ){
                    wp_mkdir_p( $new_path_dir . '_preview/' );
                }
                $preview_width  = 500;
                if( $ext == 'png' ){
                    NBD_Image::nbdesigner_resize_imagepng( $new_path, $preview_width, $preview_width, $new_path_preview );
                }else if( $ext == 'jpg' ){
                    $exif = @exif_read_data( $new_path );
                    if( $exif && isset( $exif['Orientation'] ) ) {
                        $orientation = $exif['Orientation'];
                        if( $orientation != 1 ){
                            NBD_Image::strip_exif_orientation( $new_path, $orientation );
                        }
                    }
                    NBD_Image::nbdesigner_resize_imagejpg( $new_path, $preview_width, $preview_width, $new_path_preview );
                }
                $result['origin']           = Nbdesigner_IO::wp_convert_path_to_url( $new_path );
                $result['src']              = $preview_url;
                $result['dpi']              = $dpi;
                $result['width']            = $width;
                $result['height']           = $height;
                $result['name']             = $name;
                $result['nbu_item_key']     = $nbu_item_key;
            } else {
                $result['flag'] = 0;
            }
            if( $result['flag'] == 1 ){
                if( $task == 'new' && $first_time == 1 ) WC()->session->set('nbu_item_key_'.$nbd_item_cart_key, $nbu_item_key);
                if( isset( $_POST['task'] ) && $_POST['task'] == 'upload' && isset( $_POST['cart_item_key'] ) && $_POST['cart_item_key'] != '' && isset( $_POST['first_time'] ) && $_POST['first_time'] == '1' ){
                    $cart_item_key = $_POST['cart_item_key'];
                    WC()->session->set( $cart_item_key. '_nbu', $nbu_item_key );
                    if( !isset( WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds'] ) ) WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds'] = array();
                    WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds']['nbu'] = $nbu_item_key;
                    WC()->cart->set_session();
                }
                echo json_encode( $result );
                wp_die();
            }
        }
    }
    public function get_extra_upload_file_info( $nbu_item_key, $path, $result ){
        if( $result['flag'] == 1 && isset( $_POST['nbu_adu'] ) && $_POST['nbu_adu'] == 1 ){
            $dpi            = nbd_get_dpi( $path );
            $path_dir       = NBDESIGNER_UPLOAD_DIR . '/' .$nbu_item_key;
            $path_preview   = $path_dir . '_preview/';
            $name           = pathinfo( $path, PATHINFO_BASENAME );
            $ext            = pathinfo( $path, PATHINFO_EXTENSION );
            $preview_url    = Nbdesigner_IO::wp_convert_path_to_url( $path_preview . $name );
            $preview_width  = 500;

            if( !file_exists( $path_preview ) ){
                wp_mkdir_p( $path_preview );
            }

            if( $ext == 'png' ){
                NBD_Image::nbdesigner_resize_imagepng( $path, $preview_width, $preview_width, $path_preview . $name );
            }else if( $ext == 'jpg' || $ext == 'jpeg' ){
                $exif = @exif_read_data( $path );
                if( $exif && isset( $exif['Orientation'] ) ) {
                    $orientation = $exif['Orientation'];
                    if( $orientation != 1 ){
                        NBD_Image::strip_exif_orientation( $path, $orientation );
                    }
                }
                NBD_Image::nbdesigner_resize_imagejpg( $path, $preview_width, $preview_width, $path_preview . $name );
            }
            $result['origin']       = Nbdesigner_IO::wp_convert_path_to_url( $path );
            $result['dpi']          = $dpi;
            $result['src']          = $preview_url;
            list($width, $height)   = getimagesize( $path );
            $result['width']        = $width;
            $result['height']       = $height;
            $result['nbu_item_key'] = $nbu_item_key;
            /* Upload design file from cart */
            if( isset( $_POST['task'] ) && $_POST['task'] == 'upload' && isset( $_POST['cart_item_key'] ) && $_POST['cart_item_key'] != '' && isset( $_POST['first_time'] ) && $_POST['first_time'] == '1' ){
                $cart_item_key = $_POST['cart_item_key'];
                WC()->session->set( $cart_item_key. '_nbu', $nbu_item_key );
                if( !isset( WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds'] ) ) WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds'] = array();
                WC()->cart->cart_contents[ $cart_item_key ]['nbd_item_meta_ds']['nbu'] = $nbu_item_key;
                WC()->cart->set_session();
            }
            echo json_encode( $result );
            wp_die();
        }
    }
    public function advanced_upload_settings( $post_id, $upload_setting, $unit ){
        include( NBDESIGNER_PLUGIN_DIR . 'views/upload/advanced-upload-settings.php' );
    }
    public function nbu_crop_image(){
        if ( !wp_verify_nonce($_POST['nonce'], 'save-design') && NBDESIGNER_ENABLE_NONCE ) {
            die('Security error');
        }
        $result = array(
            'flag'  => 1
        );
        $url            = $_POST['url'];
        $startX         = (float)$_POST['startX'];
        $startY         = (float)$_POST['startY'];
        $width          = (float)$_POST['width'];
        $height         = (float)$_POST['height'];
        $previewRatio   = (float)$_POST['previewRatio'];
        $path           = Nbdesigner_IO::convert_url_to_path($url);
        $path_parts     = pathinfo($path);
        $new_path       = $path_parts['dirname'] . '_final/' . $path_parts['basename'];
        if( !file_exists( $path_parts['dirname'] . '_final' ) ){
            wp_mkdir_p( $path_parts['dirname'] . '_final' );
        }
        NBD_Image::crop_image( $path, $new_path, $startX, $startY, $width, $height, strtolower( $path_parts['extension'] ) );
        if( file_exists( $new_path ) ){
            $preview_path = $path_parts['dirname'] . '_preview/' . $path_parts['basename'];
            if( file_exists( $preview_path ) ){
                $new_preview_path = $path_parts['dirname'] . '_preview_final/' . $path_parts['basename'];
                if( !file_exists( $path_parts['dirname'] . '_preview_final' ) ){
                    wp_mkdir_p( $path_parts['dirname'] . '_preview_final' );
                }
                NBD_Image::crop_image($preview_path, $new_preview_path, $startX / $previewRatio, $startY / $previewRatio, $width / $previewRatio, $height / $previewRatio, strtolower($path_parts['extension']));
                if( file_exists( $new_preview_path ) ){
                    $result['url']          = Nbdesigner_IO::convert_path_to_url($new_path);
                    $result['preview_url']  = Nbdesigner_IO::convert_path_to_url($new_preview_path);
                } else {
                    $result['flag'] = 0;
                }
            }else{
                $result['flag'] = 0;
            }
        }else{
            $result['flag'] = 0;
        }
        echo json_encode( $result );
        wp_die();
    }
    public function nbu_crop_images(){
        if ( !wp_verify_nonce($_POST['nonce'], 'save-design') && NBDESIGNER_ENABLE_NONCE ) {
            die('Security error');
        }
        $result = array(
            'flag'  => 1,
            'files' => array()
        );
        $files = ( isset( $_POST['files'] ) && is_array( $_POST['files'] ) ) ? $_POST['files'] : array();
        foreach( $files as $file ){
            $url            = $file['url'];
            $startX         = (float)$file['startX'];
            $startY         = (float)$file['startY'];
            $width          = (float)$file['width'];
            $height         = (float)$file['height'];
            $key            = (float)$file['key'];
            $previewRatio   = (float)$file['previewRatio'];
            $path = Nbdesigner_IO::convert_url_to_path($url);
            $path_parts = pathinfo($path);
            $new_path = $path_parts['dirname'] . '_final/' . $path_parts['basename'];
            if( !file_exists( $path_parts['dirname'] . '_final' ) ){
                wp_mkdir_p( $path_parts['dirname'] . '_final' );
            }
            NBD_Image::crop_image($path, $new_path, $startX, $startY, $width, $height, strtolower($path_parts['extension']));
            if( file_exists( $new_path ) ){
                $preview_path = $path_parts['dirname'] . '_preview/' . $path_parts['basename'];
                if( file_exists( $preview_path ) ){
                    $new_preview_path = $path_parts['dirname'] . '_preview_final/' . $path_parts['basename'];
                    if( !file_exists( $path_parts['dirname'] . '_preview_final' ) ){
                        wp_mkdir_p( $path_parts['dirname'] . '_preview_final' );
                    }
                    NBD_Image::crop_image($preview_path, $new_preview_path, $startX / $previewRatio, $startY / $previewRatio, $width / $previewRatio, $height / $previewRatio, strtolower($path_parts['extension']));
                    if( file_exists( $new_preview_path ) ){
                        $result['files'][] = array(
                            'key'           => $key,
                            'url'           => Nbdesigner_IO::convert_path_to_url($new_path),
                            'preview_url'   => Nbdesigner_IO::convert_path_to_url($new_preview_path)
                        );
                    } else {
                        $result['flag'] = 0;
                    }
                }else{
                    $result['flag'] = 0;
                }
            }else{
                $result['flag'] = 0;
            }
        }
        echo json_encode( $result );
        wp_die();
    }
    public function add_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity ){
        $post_data = $_POST;
        if( isset( $post_data['nbu_advanced_upload_data'] ) ){
            $cart_item_data['nbau'] = $post_data['nbu_advanced_upload_data'];
        }
        return $cart_item_data;
    }
    public function get_cart_item_from_session( $cart_item, $values ){
        if ( isset( $values['nbau'] ) ) {
            $cart_item['nbau'] = $values['nbau'];
        }
        return $cart_item;
    }
    public function order_line_item( $item, $cart_item_key, $values ){
        if ( isset( $values['nbau'] ) ) {
            $item->add_meta_data('_nbau', $values['nbau']);
        }
    }
    public function order_again_cart_item_data( $arr,  $item,  $order ){
        $order_items = $order->get_items();
        foreach( $order_items AS $order_item_id => $_item ){
            if( $item->get_id() == $order_item_id ){
                if( $nbau = wc_get_order_item_meta($order_item_id, '_nbau') ){
                    $arr['nbau'] = $nbau;
                }
            }
        }
        return $arr;
    }
    public function nbu_cart_item_html( $upload_html, $cart_item, $nbu_session ){
        if( isset( $cart_item['nbau'] ) ){
            $upload_html    = '';
            $upload_datas   = (array)json_decode( stripslashes( $cart_item['nbau'] ) );
            $upload_html   .= '<div class="nbu-cart-item-uploaded-wrap">';
            foreach ($upload_datas as $data) {
                $file          = NBDESIGNER_UPLOAD_DIR . '/' . $nbu_session . '/' . $data->name;
                $file_url       = Nbdesigner_IO::wp_convert_path_to_url( $file );
                $path_parts     = pathinfo( $file );
                $preview_path   = $path_parts['dirname'] . '_preview_final/' . $path_parts['basename'];
                $preview_url    = Nbdesigner_IO::wp_convert_path_to_url( $preview_path );
                $upload_html   .= '<div class="nbu-cart-item-uploaded-image"><a target="_blank" href='.$file_url.'><img style="max-width: 100%;" src="' . $preview_url . '"/></a><p style="width: 100%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; height: 30px; line-height: 30px;">'. $path_parts['basename'].'</p></div>';
            }
            $upload_html   .= '</div>';
        }
        return $upload_html;
    }
    public function nbu_order_item_html( $upload_html, $item, $item_id, $nbu_item_key ){
        if( isset( $item["item_meta"]["_nbau"] ) ){
            $upload_html    = '';
            $upload_datas   = (array)json_decode( stripslashes( $item["item_meta"]["_nbau"] ) );
            $upload_html   .= '<div class="nbu-order-item-uploaded-wrap">';
            foreach ( $upload_datas as $data ) {
                $file           = NBDESIGNER_UPLOAD_DIR . '/' . $nbu_item_key . '/' . $data->name;
                $file_url       = Nbdesigner_IO::wp_convert_path_to_url( $file );
                $path_parts     = pathinfo( $file );
                $preview_path   = $path_parts['dirname'] . '_preview_final/' . $path_parts['basename'];
                $preview_url    = Nbdesigner_IO::wp_convert_path_to_url( $preview_path );
                $upload_html   .= '<div class="nbu-order-item-uploaded-image"><img style="max-width: 100%;" src="' . $preview_url . '"/><p style="width: 100%; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; height: 30px; line-height: 30px;">'. $path_parts['basename'].'</p></div>';
            }
            $upload_html   .= '</div>';
        }
        return $upload_html;
    }
    public function nbu_cart_item_reup_link( $link_reup_design, $cart_item, $cart_item_key, $redirect ){
        if( isset( $cart_item['nbau'] ) ){
            $link_reup_design = add_query_arg(
                array(
                    'cik'   => $cart_item_key,
                    'rd'    => $redirect,
                    'task'  => 'reup' ),
                getUrlPageNBD( 'advanced_upload' )
            );
        }
        return $link_reup_design;
    }
    public function nbu_cart_item_upload_link( $link_upload_design, $cart_item, $cart_item_key, $redirect ){
        $product_id = $cart_item['product_id'];
        $option     = unserialize( get_post_meta( $product_id, '_nbdesigner_upload', true ) );
        if( isset( $option['advanced_upload'] ) && $option['advanced_upload'] == 1 ){
            $link_upload_design = add_query_arg(
                array(
                    'cik'   => $cart_item_key,
                    'rd'    => $redirect,
                    'task'  => 'upload' ),
                getUrlPageNBD( 'advanced_upload' )
            );
        }
        return $link_upload_design;
    }
    public function nbu_order_item_reup_link( $reup_link, $item, $item_id, $product_id ){
        if( isset( $item["item_meta"]["_nbau"] ) ){
            $nbu_item_key   = $item["item_meta"]["_nbu"]; 
            $option         = unserialize( get_post_meta( $product_id, '_nbdesigner_upload', true ) );
            if( isset( $option['advanced_upload'] ) && $option['advanced_upload'] == 1 ){
                $reup_link = add_query_arg(
                    array(
                        'oid'           => $item['order_id'],
                        'item_id'       => $item_id,
                        'rd'            => 'order',
                        'task'          => 'reup' ),
                    getUrlPageNBD( 'advanced_upload' )
                );
            }
        }
        return $reup_link;
    }
    public function nbu_update_upload_files(){
        if( isset( $_POST['nbau'] ) ){
            $design_type    = ( isset( $_POST['design_type'] ) && $_POST['design_type'] != '' ) ? $_POST['design_type'] : '';
            $cart_item_key  = $_POST['cart_item_key'];
            if( isset( $_POST['order_id'] ) && $design_type == 'edit_order' && isset( $_POST['order_item_id'] ) && $_POST['order_item_id'] != '' ){
                $order_item_id = $_POST['order_item_id'];
                wc_update_order_item_meta( $order_item_id, '_nbau', $_POST['nbau'] );
            } else if( $cart_item_key != '' ){
                WC()->cart->cart_contents[ $cart_item_key ]['nbau'] = $_POST['nbau'];
                $upload_datas   = (array)json_decode( stripslashes( $_POST['nbau'] ) );
                if( isset( WC()->cart->cart_contents[ $cart_item_key ] ) && isset( WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta'] ) ){
                    $nbd_field          = WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta']['field'];
                    $options            = WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta']['options'];
                    if( nbd_is_base64_string( $options['fields'] ) ){
                        $options['fields']  = base64_decode( $options['fields'] );
                    }
                    $option_fields      = unserialize( $options['fields'] );
                    $must_update_cart   = false;
                    foreach( $nbd_field as $k => $f ){
                        $op_field = array();
                        foreach( $option_fields['fields'] as $key => $field ){
                            if( $field['id'] == $k ){
                                $op_field = $field;
                            }
                        }
                        if( isset( $op_field['nbe_type'] ) && $op_field['nbe_type'] == 'number_file' ){
                            $nbd_field[ $k ]    = count( $upload_datas );
                            $must_update_cart   = true;
                        }
                    }
                    if( $must_update_cart ){
                        WC()->cart->cart_contents[ $cart_item_key ]['nbo_meta']['field'] = $nbd_field;
                        WC()->cart->calculate_totals();
                    }
                }
                WC()->cart->set_session();
            }
        }
    }
    public function update_files_upload( $files, $nbu_session = '' ){
        $files      = explode( '|', $files );
        $path       = NBDESIGNER_UPLOAD_DIR . '/' . $nbu_session;
        $list_files = Nbdesigner_IO::get_list_files( $path );
        foreach ( $list_files as $file ){
            $filename = basename( $file );
            if( !in_array( $filename, $files ) ){
                unlink( $path . '/' . $filename );
            }
        }
    }
    public function nbu_save_upload_files(){
        if ( !wp_verify_nonce( $_POST['nonce'], 'save-design' ) && NBDESIGNER_ENABLE_NONCE ) {
            die('Security error');
        }
        $files          = $_POST['nbd_file'];
        $nbu_item_key   = $_POST['nbu_item_key'];
        $this->update_files_upload( $files, $nbu_item_key );
        $this->nbu_update_upload_files();
        echo 'success';
        wp_die();
    }
    public function nbu_download_upload_files( $files, $item ){
        if( isset( $item["item_meta"]["_nbau"] ) ){
            $nbu_item_key = $item["item_meta"]["_nbu"];
            $upload_datas   = (array)json_decode( stripslashes( $item["item_meta"]["_nbau"] ) );
            $upload_path    = NBDESIGNER_UPLOAD_DIR . '/' . $nbu_item_key;
            $files          = array();
            foreach( $upload_datas as $key => $data ){
                $data       = (array)$data;
                $final_file = $upload_path . '_final/' . $data['name'];
                $files[]    = $final_file;
            }
        }
        return $files;
    }
    public function hidden_order_itemmeta($order_items){
        $order_items[] = '_nbau';
        return $order_items; 
    }
    public function attach_pdf_to_admin_email( $attachments, $type, $order ){
        if( 'customer_completed_order' === $type ){
            $items = $order->get_items();
            $has_advanced_upload_data = false;
            foreach( $items as $order_item_id => $item ){
                $nbd_item_key = wc_get_order_item_meta($order_item_id, '_nbu');
                $upload_datas = wc_get_order_item_meta($order_item_id, '_nbau');
                if( $nbd_item_key && $upload_datas ){
                    $has_advanced_upload_data = true;
                }
            }
            if( $has_advanced_upload_data ){
                $output_file = $this->create_invoice( $order );
                if(file_exists($output_file) ){
                    $attachments[] = $output_file;
                }
            }
        }
        return $attachments;
    }
    public function create_invoice( $order ){
        require_once(NBDESIGNER_PLUGIN_DIR.'lib/dompdf/autoload.inc.php');
        $dompdf = new DOMPDF();
        ob_start();
        nbdesigner_get_template( 'order-invoice.php', array('order' => $order) );
        $html = ob_get_contents();
        ob_end_clean();
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4');
        $dompdf->set_option('defaultMediaType', 'all');
        $dompdf->set_option('isFontSubsettingEnabled', true);
        $dompdf->render();
        $output_file = NBDESIGNER_DATA_DIR .'/invoices/invoice_'. $order->get_id() .'.pdf';
        $output = $dompdf->output();
        file_put_contents($output_file, $output);
        return $output_file;
    }
}
$nbd_advanced_upload = NBDesigner_Advanced_Upload::instance();
$nbd_advanced_upload->init();