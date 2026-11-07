<?php
if ( ! defined( 'ABSPATH' ) ) { exit;}
if(!class_exists('NBDESIGNER_VISTA_LAYOUT')) {
    class NBDESIGNER_VISTA_LAYOUT{
        protected static $instance;
        public function __construct(){}
        public static function instance(){
            if (is_null(self::$instance)) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        public function init(){
            $this->frontend_enqueue_scripts();
            add_action('woocommerce_before_single_product', array(&$this, 'before_product_container'), 1);
        }
        public function before_product_container(){
            $pid = get_the_ID();
            if( $this->is_vista_layout($pid) ){
                add_action('woocommerce_before_single_product_summary', array(&$this, 'design_editor'), 1);
                add_action('woocommerce_after_add_to_cart_button', array(&$this, 'add_to_cart_input'), 1);
                remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash');
                remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
                remove_all_actions('woocommerce_single_product_media');
                if( isset($_GET['nbdv-task']) && $_GET['nbdv-task'] != '' ){
                    remove_all_actions('woocommerce_before_single_product_summary');
                    remove_all_actions('woocommerce_after_single_product_summary');
                    remove_all_actions('woocommerce_single_product_summary');
                    remove_all_actions('woocommerce_breadcrumb');
                    remove_all_actions('woocommerce_after_main_content');
                    add_action('woocommerce_before_single_product_summary', array(&$this, 'design_editor'), 1);
                }
            }
        }
        public function is_vista_layout($pid){
            $is_vista = false;
            if (is_nbdesigner_product($pid)) {
                $option = unserialize(get_post_meta($pid, '_nbdesigner_option', true));
                $without_design = get_post_meta( $pid, '_nbdesigner_enable_upload_without_design', true );
                if( $without_design ) return false;
                if( isset($option['layout']) && $option['layout'] == 'v' ){
                    $is_vista = true;
                }
            }
            return $is_vista;
        }
        public function frontend_enqueue_scripts(){
            add_action('wp_enqueue_scripts', function() {
                $js_libs = array(
                    'jquery-ui-vista' => array(
                        'link' => NBDESIGNER_PLUGIN_URL . 'assets/libs/jquery-ui.min.js',
                        'version'   => NBDESIGNER_VERSION,
                        'depends'   => array('jquery'),
                        'in_footer' => true
                    ),
                    'nbd-vista' => array(
                        'link'      => NBDESIGNER_ASSETS_URL .'js/vista.js',
                        'version'   => NBDESIGNER_VERSION,
                        'depends'   => array('jquery', 'jquery-ui-vista'),
                        'in_footer' => true
                    ),
                    'app-modern' => array(
                        'link'      => NBDESIGNER_ASSETS_URL.'js/app-modern.min.js',
                        'version'   => NBDESIGNER_VERSION,
                        'depends'   => array('jquery', 'nbd-vista', 'nbd-bundle'),
                        'in_footer' => true
                    ),
                    'nbd-bundle' => array(
                        'link'      => NBDESIGNER_ASSETS_URL.'js/bundle-modern.min.js',
                        'version'   => NBDESIGNER_VERSION,
                        'depends'   => array('jquery', 'underscore', 'angularjs', 'jquery-ui-vista'),
                        'in_footer' => true
                    )
                );
                $css_libs = array(
                    'vista' => array(
                        'link'      => NBDESIGNER_ASSETS_URL.'css/vista.css',
                        'version'   => NBDESIGNER_VERSION,
                        'depends'   =>  array()
                    ),
                    'vista-rtl' => array(
                        'link'      => NBDESIGNER_ASSETS_URL.'vista/assets/css/vista-rtl.css',
                        'version'   => NBDESIGNER_VERSION,
                        'depends'   =>  array()
                    ),
                );
                foreach ($js_libs as $key => $js){
                    wp_register_script($key, $js['link'], $js['depends'], $js['version'],$js['in_footer']);
                }
                foreach ($css_libs as $key => $css){
                    wp_register_style($key, $css['link'], $css['depends'], $css['version']);
                }
                if( is_singular( 'product' ) ){
                    $pid = get_the_ID();
                    if( $this->is_vista_layout($pid) ){
                        wp_enqueue_style( 'vista');
                        wp_enqueue_script('app-modern');
                        if (is_rtl()) {
                            wp_enqueue_style('vista-rtl');
                        }
                        $default_font = nbd_get_default_font();
                        $_default_font = str_replace(" ", "+", json_decode($default_font)->alias);
                        wp_enqueue_style( 'nbd-default-font', 'https://fonts.googleapis.com/css?family='.$_default_font.':400,400i,700,700i', false );
                    }
                }
            });
        }
        public function design_editor(){
            include(NBDESIGNER_PLUGIN_DIR . 'views/vista/vista.php');
        }
        public function add_to_cart_input(){
            global $product;
            $pid = $product->get_id();
            if( $this->is_vista_layout($pid) ){
            ?>
            <input name="add-to-cart" type="hidden" value="<?php echo $pid; ?>" />
            <?php
            }
        }
    }
}
$nbd_vista = NBDESIGNER_VISTA_LAYOUT::instance();
$nbd_vista->init();