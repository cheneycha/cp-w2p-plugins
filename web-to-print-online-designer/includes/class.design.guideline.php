<?php 
if (!defined('ABSPATH')) exit;
class NBDesigner_Design_Guideline {
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
        add_action( 'nbo_options_meta_box_tabs', array( $this, 'design_guideline_tab' ) );
        add_action( 'nbo_options_meta_box_panels', array( $this, 'design_guideline_panel' ) );
        add_action( 'nbo_save_options', array( $this, 'save_design_guideline' ), 20, 1 );
        add_filter( 'upload_mimes', array( $this, 'upload_mimes' ) );
        add_action( 'nbd_request_design_after_product_image', array( $this, 'show_design_guideline' ) );
        add_shortcode( 'nbd_guideline', array( $this,'nbd_guideline_func' ) );
        add_filter( 'woocommerce_product_tabs', array( $this, 'design_guideline_product_tab' ) );
    }
    public function design_guideline_tab(){
        ?>
        <li><a href="#nbd-design-guideline"><span class="dashicons dashicons-book"></span> <?php _e('Design Guideline', 'web-to-print-online-designer'); ?></a></li>
        <?php
    }
    public function design_guideline_panel(){
        $post_id = get_the_ID();
        $guideline_files    = unserialize( get_post_meta($post_id, '_nbdg_files', true) );
        $nbdg_tab_content   = get_post_meta($post_id, '_nbdg_tab_content', true);
        $nbdg_tab_enable    = get_post_meta($post_id, '_nbdg_tab_enable', true);
        include_once(NBDESIGNER_PLUGIN_DIR .'views/metaboxes/design-guideline-panel.php');
    }
    public function save_design_guideline( $post_id ){
        $guideline_files = $this->prepare_guidelines(
            isset( $_POST['_nbdg_file_names'] ) ? wp_unslash( $_POST['_nbdg_file_names'] ) : array(),
            isset( $_POST['_nbdg_file_exts'] ) ? wp_unslash( $_POST['_nbdg_file_exts'] ) : array(),
            isset( $_POST['_nbdg_file_urls'] ) ? wp_unslash( $_POST['_nbdg_file_urls'] ) : array()
        );
        $nbdg_tab_content   = $_POST['_nbdg_tab_content'];
        $nbdg_tab_enable    = $_POST['_nbdg_tab_enable'];
        update_post_meta($post_id, '_nbdg_files', serialize( $guideline_files ) );
        update_post_meta($post_id, '_nbdg_tab_enable', $nbdg_tab_enable );
        update_post_meta($post_id, '_nbdg_tab_content', htmlspecialchars( $nbdg_tab_content ) );
    }
    private function prepare_guidelines($file_names, $file_exts, $file_urls) {
        $guideline_files = array();
        if (!empty($file_urls)) {
            $file_url_size = count($file_urls);
            for ($i = 0; $i < $file_url_size; $i ++) {
                if (!empty($file_urls[$i])) {
                    $guideline_files[] = array(
                        'name' => wc_clean($file_names[$i]),
                        'ext'  => wc_clean($file_exts[$i]),
                        'file' => wp_unslash(trim($file_urls[$i]))
                    );
                }
            }
        }
        return $guideline_files;
    }
    public function upload_mimes( $mimes ){
        $a = array( 0 => array(), 1 => array());
        $default = serialize( $a );
        $dg_mimes = unserialize( get_option( 'nbdesigner_guideline_mimes', $default ) );
        if(is_array( $dg_mimes ) ){
            foreach ( $dg_mimes[0] as $key => $mime ){
                if( $mime != '' && $dg_mimes[1][$key] != '' ) $mimes[$mime] = trim( $dg_mimes[1][$key] );
            }
        }
        return $mimes;
    }
    public function show_design_guideline(){
        echo do_shortcode( '[nbd_guideline]' );
    }
    public function nbd_guideline_func( $atts, $content = null ){
        global $product;
        $product_id = 0;
        if( is_object( $product ) && property_exists( $product, 'id' ) ){
            $product_id = $product->get_id();
        }
        $atts = shortcode_atts(array(
            'product_id' => $product_id 
        ), $atts);
        if( absint( $product_id ) == 0 ){
            return '';
        }
        $guideline_files        = unserialize( get_post_meta( $product_id, '_nbdg_files', true ) );
        $nbdg_tab_content       = get_post_meta( $product_id, '_nbdg_tab_content', true);
        $atts['description']    = $nbdg_tab_content;
        $atts['files']          = $guideline_files;
        ob_start();
        nbdesigner_get_template( 'single-product/design-guideline.php', $atts );
        $content = ob_get_clean();
        return $content;
    }
    public function design_guideline_product_tab( $tabs ){
        global $post;
        $nbdg_tab_enable    = get_post_meta($post->ID, '_nbdg_tab_enable', true);
        if( $nbdg_tab_enable ){
            $nbdg_tab_content = $this->get_design_guideline_tab_content();
            if ( strlen( $nbdg_tab_content ) > 0 ) {
                $tabs['design_guideline'] = array(
                    'title'    => __( 'Design Guidelines', 'web-to-print-online-designer' ),
                    'priority' => 60,
                    'callback' => array( $this, 'design_guideline_tab_content' )
                );
            }
        }
        return $tabs;
    }
    public function design_guideline_tab_content(){
        echo $this->get_design_guideline_tab_content();
    }
    public function get_design_guideline_tab_content(){
        global $post;
        return do_shortcode( '[nbd_guideline]' );
    }
}
$nbd_design_guideline = NBDesigner_Design_Guideline::instance();
$nbd_design_guideline->init();