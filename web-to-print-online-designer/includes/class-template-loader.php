<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
class NBD_Template_Loader {
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
    public function init() {
        add_filter( 'template_include', array( $this, 'template_loader' ) );
        add_action( 'init', array( $this, 'add_rewrites' ) );
        add_action( 'template_redirect', array( $this, 'template_redirect' ) );
        //add_filter( 'request', array( $this, 'filter_request' ) );
    }
    public function template_loader( $template ) {
        if ( $default_file = self::get_template_loader_default_file() ) {
            $template = nbdesigner_locate_template($default_file);
        }
        return $template;
    }
    private function get_template_loader_default_file() {
        $default_file = '';
        if ( is_nbd_designer_page() ) {
            $default_file = 'gallery/artist.php';
        }
        return $default_file;
    }
    public function filter_request( $vars ){
        if( isset( $vars['request-design'] ) ) $vars['request-design'] = true;
        if( isset( $vars['upload-design'] ) ) $vars['upload-design'] = true;
        return $vars;
    }
    public static function add_rewrites(){
        add_rewrite_endpoint( 'request-design', EP_PERMALINK | EP_PAGES );
        add_rewrite_endpoint( 'upload-design', EP_PERMALINK | EP_PAGES );
    }
    public function template_redirect(){
        global $wp_query, $post;
        if( is_singular() && get_post_type() == 'product' ){
            if( isset( $wp_query->query_vars['request-design'] ) || isset( $wp_query->query_vars['upload-design'] ) ){
                global $product;
                $product = wc_get_product( $post->ID );
                $template = nbdesigner_locate_template( 'single-product/artwork-actions.php' );
                include( $template );
                exit();
            }
        }
    }
}
$nbd_template_loader = NBD_Template_Loader::instance();
$nbd_template_loader->init();