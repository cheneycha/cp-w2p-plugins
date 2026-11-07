<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if(!class_exists('Nbdesigner_Product_Builder')) {
    class Nbdesigner_Product_Builder{
        protected static $instance;
        protected $isDesign = false;
        public function __construct(){
            //TODO
        }
        public static function instance(){
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        public function init(){
            $this->frontend_enqueue_scripts();
            add_action('woocommerce_before_single_product', array(&$this, 'before_product_container'), 1);
            if (is_admin()) {
                $this->ajax();
            }
            add_action( 'template_redirect', array( $this, 'template_redirect' ) );
        }
        public function ajax(){
            $ajax_events = array(
                'nbd_save_product_builder_design'   => true
            );
            foreach ($ajax_events as $ajax_event => $nopriv) {
                add_action('wp_ajax_' . $ajax_event, array($this, $ajax_event));
                if ($nopriv) {
                    // NBDesigner AJAX can be used for frontend ajax requests
                    add_action('wp_ajax_nopriv_' . $ajax_event, array($this, $ajax_event));
                }
            }
        }
        public function template_redirect(){
            if( is_nbd_product_builder_page() && ( current_user_can('editor') || current_user_can('administrator') ) ){
                include(NBDESIGNER_PLUGIN_DIR . 'views/product-builder/index.php');exit();
            }
        }
        public function nbd_save_product_builder_design(){
            if ( !wp_verify_nonce($_POST['nonce'], 'save-design') && NBDESIGNER_ENABLE_NONCE ) {
                die('Security error');
            }
            $result = array(
                'flag'  =>  'failure',
                'link'  =>  '',
                'folder' => ''
            );
            do_action('before_nbd_save_product_builder_design');
            $nbd_item_pb_key = (isset($_POST['nbd_item_pb_key']) && $_POST['nbd_item_pb_key'] != '') ? $_POST['nbd_item_pb_key'] : substr(md5(uniqid()),0,5).rand(1,100).time();
            $is_creating_task = (isset($_POST['is_creating_task']) && $_POST['is_creating_task'] != '') ? $_POST['is_creating_task'] :  '0';
            $oid = (isset($_POST['oid']) && $_POST['oid'] != '') ? absint($_POST['oid']) :  0;
            $path = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_pb_key;
            $save_status = $this->store_product_builder_design_data($nbd_item_pb_key, $_FILES);
            if( false != $save_status  ){
                $result['image'] = $this->create_preview( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_pb_key );
                asort($result['image']);
                $result['flag'] = 'success';
                $result['folder'] = $nbd_item_pb_key;
                if ($is_creating_task == '1' && $oid != 0 ) {
                    global $wpdb;
                    $arr = array(
                        'builder'   =>  $nbd_item_pb_key
                    );
                    $result_update = $wpdb->update("{$wpdb->prefix}nbdesigner_options", $arr, array( 'id' => $oid) );
                }
            }
            do_action('after_nbd_save_product_builder_design', $result);
            echo json_encode($result);
            wp_die();
        }
        private function create_preview( $path ){
            $config = json_decode( file_get_contents($path . '/config.json') );
            $images = array();
            if( wp_mkdir_p($path . '/preview') ){
                foreach($config->views as $index => $view){
                    $design_path = $path . '/frame_' . $index . '.png';
                    if(file_exists($design_path) ){
                        list($width, $height) = getimagesize($design_path);
                        $width = intval($width);
                        $height = intval($height);
                        $base_img_path = Nbdesigner_IO::convert_url_to_path( $view->base_url );
                        if( is_file($base_img_path) ){
                            $base_img_info = pathinfo($base_img_path);
                            if($base_img_info['extension'] == "png"){
                                $base_img = NBD_Image::nbdesigner_resize_imagepng($base_img_path, $width, $height);
                            }else{
                                $base_img = NBD_Image::nbdesigner_resize_imagejpg($base_img_path, $width, $height);
                            }
                            $design = imagecreatefrompng($design_path);
                            imagecopy($base_img, $design, 0, 0, 0, 0, $width, $height);
                            imagepng($base_img, $path . '/preview/' . $index . '.png');
                            imagedestroy($base_img);
                            imagedestroy($design);
                        }else{
                            copy($design_path, $path . '/preview/' . $index . '.png');
                        }
                        $images[] = Nbdesigner_IO::wp_convert_path_to_url( $path . '/preview/' . $index . '.png' );
                    }
                }
            };
            return $images;
        }
        private function store_product_builder_design_data($nbd_item_pb_key, $data){
            $path = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_pb_key;
            if(file_exists($path.'_old')) Nbdesigner_IO::delete_folder($path.'_old');
            if(file_exists($path)) rename($path, $path.'_old');
            if ( wp_mkdir_p($path) ) {
                foreach ($data as $key => $val) {
                    if($key == 'design'){
                        $full_name = $path . '/design.json';
                    }else if($key == 'config'){
                        $full_name = $path . '/config.json';
                    }else{
                        $ext = explode('/', $val["type"])[1];
                        $full_name = $path . '/' . $key . '.' .$ext;
                    }
                    if ( !move_uploaded_file($val["tmp_name"], $full_name) ) {
                        return false;
                    }
                }
            } else {
                Nbdesigner_DebugTool::wirite_log('Your server not allow creat folder', 'save design');
                rename($path.'_old', $path);
                return false;
            }
            return true;
        }
        public function before_product_container(){
            $pid = get_the_ID();
            if (is_nbd_product_builder($pid)) {
                add_action('nbo_after_default_options', array(&$this, 'product_builder_html'), 1);
                add_action('wp_footer', array(&$this, 'nbd_modal_product_builder'), 1);
            }
        }
        public function frontend_enqueue_scripts(){
            add_action('wp_enqueue_scripts', function() {
                $js_libs = array(
                    'fontfaceobserver' => array(
                        'link' => NBDESIGNER_PLUGIN_URL . 'assets/libs/fontfaceobserver.js',
                        'version'   => '2.0.13',
                        'depends'  => array()
                    ),
                    'spectrum' => array(
                        'link' => NBDESIGNER_PLUGIN_URL . 'assets/js/spectrum.js',
                        'version'   => '1.8.0',
                        'depends'  => array()
                    ),
                    'fabricjs' => array(
                        'link' => NBDESIGNER_PLUGIN_URL . 'assets/libs/fabric.2.6.0.min.js',
                        'version'   => '2.6.0',
                        'depends'  => array()
                    ),
                    'product-builder' => array(
                        'link' => NBDESIGNER_ASSETS_URL.'js/app-product-builder.js',
                        'version'   => NBDESIGNER_VERSION,
                        'depends'  => array('jquery', 'underscore', 'angularjs', 'fabricjs', 'fontfaceobserver', 'spectrum')
                    )
                );
                $js_libs_with_design = array(
                    'product-builder-wd' => array(
                        'link' => NBDESIGNER_ASSETS_URL.'js/app-product-builder.js',
                        'version'   => NBDESIGNER_VERSION,
                        'depends'  => array('nbd-bundle')
                    )
                );
                $css_libs = array(
                    'spectrum' => array(
                        'link'  => NBDESIGNER_ASSETS_URL.'css/spectrum.css',
                        'version'   => '1.8.0',
                        'depends'  =>  array()
                    ),
                    'product-builder' => array(
                        'link'  => NBDESIGNER_ASSETS_URL.'css/app-product-builder.css',
                        'version'   => NBDESIGNER_VERSION,
                        'depends'  =>  array('spectrum')
                    ),
                );
                foreach ($css_libs as $key => $css){
                    $link = $css['link'];
                    wp_register_style($key, $link, $css['depends'], $css['version']);
                }
                $pid = get_the_ID();
                if( is_singular( 'product' ) && is_nbd_product_builder($pid) ){
                    wp_enqueue_style( 'product-builder');
                    if( is_nbd_product_with_vista_layout( $pid ) ){
                        foreach ($js_libs_with_design as $key => $js){
                            $link = $js['link'];
                            wp_register_script($key, $link, $js['depends'], $js['version'],false);
                        }
                        wp_enqueue_script('product-builder-wd');
                    }else{
                        foreach ($js_libs as $key => $js){
                            $link = $js['link'];
                            wp_register_script($key, $link, $js['depends'], $js['version'],false);
                        }
                        wp_enqueue_script('product-builder');
                    }
                }
            });
        }
        public function product_builder_html(){
            include(NBDESIGNER_PLUGIN_DIR . 'views/product-builder/customize-btn.php');
        }
        public function nbd_modal_product_builder(){
            $product_id = get_the_ID();
            $option_id = get_transient( 'nbo_product_'.$product_id );
            if (is_nbd_product_builder($product_id)) {
                include(NBDESIGNER_PLUGIN_DIR . 'views/product-builder/wrapper.php');
            }
        }
    }
}
$nbd_product_builder = Nbdesigner_Product_Builder::instance();
$nbd_product_builder->init();