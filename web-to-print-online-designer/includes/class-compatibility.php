<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<?php
class Nbdesigner_Compatibility {
    public function __construct(){
        //toto init something
    }
    public function init(){
        if( is_dokan() ){
            $this->compatibility_dokan();
        }
    }
    public function compatibility_dokan(){
        if( !is_admin() ){
            if( class_exists('Dokan_Pro') ){
//                add_filter( 'dokan_product_data_tabs', array(&$this, 'nbd_dokan_product_data_tabs'), 10, 1 );
//                add_action( 'dokan_product_tab_content', array(&$this, 'box_config_product'), 10, 2 );
                add_action( 'dokan_product_edit_after_inventory_variants', array(&$this, 'box_config_product'), 10, 2 );
            }else{
                add_action( 'dokan_product_edit_after_inventory_variants', array(&$this, 'box_config_product'), 10, 2 );
            }
            add_action( 'woocommerce_before_order_itemmeta', array(&$this, 'nbd_before_order_itemmeta'), 10, 2 );
            add_action( 'dokan_product_updated', array(&$this, 'product_updated'), 10, 1 );
            add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );
            add_action( 'wp_enqueue_scripts', array($this, 're_enqueue_scripts'),30 );
            add_action('wp_ajax_download_dokan_product_pdfs', array($this, 'download_dokan_product_pdfs'));
            add_action('wp_ajax_nopriv_download_dokan_product_pdfs', array($this, 'download_dokan_product_pdfs'));
            if ( isset( $_GET['download_nbd_file'] ) && isset( $_GET['order_id'] ) && isset( $_GET['order_item_id'] ) ) {
                add_action( 'init', array( $this, 'download_dokan_product_designs' ) );
            }
            add_action( 'dokan_new_seller_created', array($this, 'enable_designer_role'), 10, 1 );
            add_action( 'dokan_enqueue_scripts', array($this, 'remove_chart_js') );
        }
        add_action('dokan_register_scripts', array($this,'remove_vue_vendor_js'));
    }
    public function remove_vue_vendor_js($hook){
        global $pagenow;
        if($pagenow === 'post.php'){
            wp_deregister_script( 'dokan-vue-vendor' );
        }
    }
    public function re_enqueue_scripts(){
        if(dokan_is_seller_dashboard( ) ){
            wp_enqueue_script(
                'wp-color-picker-dk',
                admin_url( 'js/color-picker.min.js' ),
                array( 'jquery' ),
                false,
                1
            );
        }
    }
    public function remove_chart_js(){
        if( (get_query_var( 'edit' ) && is_singular( 'product' )) || ( dokan_is_seller_dashboard() && isset($_GET['product_id']) && (isset($_GET['action']) && $_GET['action'] == 'edit') ) ){
            wp_dequeue_script('dokan-chart');
        }
    }
    public function enable_designer_role( $user_id ){
        $allowed_roles = array( 'customer', 'seller' );
        $role = ( isset( $_POST['role'] ) && in_array( $_POST['role'], $allowed_roles ) ) ? $_POST['role'] : 'customer';
        if ( $role == 'seller' ) {	
            update_user_meta( $user_id, 'nbd_create_design', 'on' );
            $wp_user_infos = get_user_by('id', $user_id);
            update_user_meta( $user_id, 'nbd_artist_name', $wp_user_infos->display_name );            
        }
    }
    public function nbd_dokan_product_data_tabs( $tabs ){
        $tabs['nbdesigner'] = array(
            'label'  => __( 'NBDesigner Options', 'web-to-print-online-designer' ),
            'target' => 'nbd-config',
            'class'  => array('nbd-config'),
        );  
        return $tabs;
    }
    public function box_config_product( $post, $post_or_seller_id ){
        $post_id                = $post->ID;
        $pro_class              = class_exists( 'Dokan_Pro' ) ? 'dokan_pro' : '';
        $enable                 = get_post_meta( $post_id, '_nbdesigner_enable', true );
        $_designer_setting      = unserialize( get_post_meta( $post_id, '_designer_setting', true ) );
        $enable_upload          = get_post_meta( $post_id, '_nbdesigner_enable_upload', true );
        $upload_without_design  = get_post_meta( $post_id, '_nbdesigner_enable_upload_without_design', true );
        $option                 = unserialize( get_post_meta( $post_id, '_nbdesigner_option', true ) );
        $unit                   = isset( $option['unit'] ) ? $option['unit'] : nbdesigner_get_option( 'nbdesigner_dimensions_unit', 'cm' );
        if ( isset( $_designer_setting[0] ) ){
            foreach ( $_designer_setting as $key => $set ){
                $_designer_setting[$key] = array_merge( nbd_default_product_setting(), $set );
            }
            $designer_setting = $_designer_setting;
            if(! isset($designer_setting[0]['version']) || $_designer_setting[0]['version'] < 160) {
                //$designer_setting = $this->update_config_product_160( $designer_setting );
            }
            if(! isset($designer_setting[0]['version']) || $_designer_setting[0]['version'] < 180) {
                $designer_setting = NBD_Update_Data::nbd_update_media_v180( $designer_setting );
            }
        }else {
            $designer_setting       = array();
            $designer_setting[0]    = nbd_default_product_setting();
        }
        $_option = nbd_get_default_product_option();
        if( !is_array( $option ) ){
            $option = array();
        }
        $option             = array_merge( $_option, $option );
        $upload_setting     = unserialize( get_post_meta( $post_id, '_nbdesigner_upload', true ) );
        $_upload_setting    = nbd_get_default_upload_setting();
        if( !is_array( $upload_setting ) ){
            $upload_setting = array();
        }
        $upload_setting     = array_merge( $_upload_setting, $upload_setting );
        $designer_setting   = nbd_update_config_default( $designer_setting );
        $atts = array(
            'post_id'               => $post_id,
            'enable'                => $enable,
            'enable_upload'         => $enable_upload,
            'upload_without_design' => $upload_without_design,
            'designer_setting'      => $designer_setting,
            'upload_setting'        => $upload_setting,
            'option'                => $option,
            'unit'                  => $unit,
            'pro_class'             => $pro_class
        );
        ob_start();
        nbdesigner_get_template( 'compatibility/dokan/product_setting.php', $atts );
        $nbd_box = ob_get_clean();
        echo $nbd_box;
    }
    public function enqueue_scripts(){
        if(dokan_is_seller_dashboard() || ( get_query_var( 'edit' ) && is_singular( 'product' ) )){
            wp_register_script('nbd-dokan-product', NBDESIGNER_JS_URL . 'dokan.js', array( 'jquery-ui-resizable', 'jquery-ui-draggable' ));
            wp_enqueue_script( array( 'jquery-ui-core', 'nbd-dokan-product' ) );
            wp_enqueue_script(
                'iris',
                admin_url( 'js/iris.min.js' ),
                array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
                false,
                1
            );
            wp_enqueue_script(
                'wp-color-picker',
                admin_url( 'js/color-picker.min.js' ),
                array( 'iris' ),
                false,
                1
            );
            $colorpicker_l10n = array(
                'clear' => __( 'Clear', 'web-to-print-online-designer' ),
                'defaultString' => __( 'Default', 'web-to-print-online-designer' ),
                'pick' => __( 'Select Color', 'web-to-print-online-designer' ),
                'current' => __( 'Current Color', 'web-to-print-online-designer' ),
            );
            wp_localize_script( 'wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n ); 
            $wp_scripts = wp_scripts();
            wp_enqueue_style(
                'jquery-ui-nbd-dokan', sprintf('https://ajax.googleapis.com/ajax/libs/jqueryui/%s/themes/smoothness/jquery-ui.css', $wp_scripts->registered['jquery-ui-core']->ver)
            );         
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style('nbd-dokan-product', NBDESIGNER_CSS_URL . 'dokan.css');
        }        
    }
    public function product_updated( $post_id ){
        $enable                 = wc_clean( $_POST['_nbdesigner_enable'] ); 
        $enable_upload          = wc_clean( $_POST['_nbdesigner_enable_upload'] ); 
        $upload_without_design  = wc_clean( $_POST['_nbdesigner_enable_upload_without_design'] ); 
        $option                 = serialize($_POST['_nbdesigner_option']); 
        $setting_design         = serialize($_POST['_designer_setting']);  
        $setting_upload         = serialize($_POST['_designer_upload']);  
        //todo check license here
        update_post_meta($post_id, '_designer_setting', $setting_design);
        update_post_meta($post_id, '_nbdesigner_option', $option);
        update_post_meta($post_id, '_nbdesigner_upload', $setting_upload);
        update_post_meta($post_id, '_nbdesigner_enable_upload_without_design', $upload_without_design);
        update_post_meta($post_id, '_nbdesigner_enable', $enable);
        update_post_meta($post_id, '_nbdesigner_enable_upload', $enable_upload);        
    }   
    public function nbd_before_order_itemmeta( $item_id, $item ){
        $cutom_design_html  = '';
        $nbd_item_key       = wc_get_order_item_meta($item_id, '_nbd');
        $nbu_item_key       = wc_get_order_item_meta($item_id, '_nbu');
        $order_id           = $item['order_id'];
        $data_nbd_item_key  = $data_nbu_item_key = '';
        if( $nbd_item_key ){
            $list_images = Nbdesigner_IO::get_list_images(NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key .'/preview', 1);  
            if( count($list_images) ){
                $cutom_design_html .= '<div>';
                $cutom_design_html .=   '<p>' . esc_html__('Custom designs', 'web-to-print-online-designer').'</p>';
                foreach ( $list_images as $image ){
                    $src = Nbdesigner_IO::convert_path_to_url($image); 			
                    $cutom_design_html .= '<img class="nbd-dokan-custom-design nbd-dokan-custom-design-img" src="'.$src.'" />';
                }
                $cutom_design_html .= '</div>';                
            }
            $data_nbd_item_key = $nbd_item_key;
        }
        if( $nbu_item_key ){
            $files = Nbdesigner_IO::get_list_files( NBDESIGNER_UPLOAD_DIR .'/'. $nbu_item_key ); 
            if( count($files) ){
                $cutom_design_html .= '<div>';
                $cutom_design_html .=   '<p>' . esc_html__('Upload files', 'web-to-print-online-designer').'</p>';
                $cutom_design_html .=   '<p>';
                foreach ( $files as $key => $file ){
                    $src = Nbdesigner_IO::convert_path_to_url($image); 			
                    if($key > 0) $cutom_design_html .= '|';
                    $cutom_design_html .= basename($file);
                }
                $cutom_design_html .=   '</p>';
                $cutom_design_html .= '</div>';                
            }
            $data_nbu_item_key = $nbu_item_key;
        }    
        if( $nbd_item_key || $nbu_item_key ){
            $link_download = add_query_arg(array(
                    'download_nbd_file' => 1,
                    'order_id'          => $order_id,
                    'order_item_id'     => $item_id,
                    'nbd_item_key'      => $data_nbd_item_key,
                    'nbu_item_key'      => $data_nbu_item_key
                ), site_url());                     
            $cutom_design_html .= '<div class="nbd-dokan-download-wrap">';
            $cutom_design_html .=   '<p>' . esc_html__('Download', 'web-to-print-online-designer').'</p>';
            $cutom_design_html .=   '<p>';
            $cutom_design_html .=       '<select class="dokan-form-control dokan-form-control-select" onchange="NBDESIGNERPRODUCT.change_nbd_dokan_format( this )">';
            if( $nbd_item_key ){
            $cutom_design_html .=           '<option value="png">' . esc_html__('Download designs: PNG', 'web-to-print-online-designer').'</option>';
            $cutom_design_html .=           '<option value="pdf">' . esc_html__('Download designs: PDF', 'web-to-print-online-designer').'</option>';
            $cutom_design_html .=           '<option value="svg">' . esc_html__('Download designs: SVG', 'web-to-print-online-designer').'</option>';
            if( is_available_imagick() ){
            $cutom_design_html .=           '<option value="jpg">' . esc_html__('Download designs: JPG', 'web-to-print-online-designer').'</option>';
            $cutom_design_html .=           '<option value="jpg_cmyk">' . esc_html__('Download designs: CMYK - JPG', 'web-to-print-online-designer').'</option>';
            }
            }
            if( $nbu_item_key ){
            $cutom_design_html .=           '<option value="files">' . esc_html__('Download upload files', 'web-to-print-online-designer').'</option>';    
            }
            $cutom_design_html .=       '<select>';
            $cutom_design_html .=   '</p>';
            $cutom_design_html .=   '<p>';
            $cutom_design_html .=       '<a class="button nbd-dokan-download" href="'. $link_download .'&type=png" data-href="'.$link_download.'">' . esc_html__('Download', 'web-to-print-online-designer').'</a><br />';
            $cutom_design_html .=   '</p>';
            $cutom_design_html .= '</div>';
        }
        echo $cutom_design_html;
    }
    public function download_dokan_product_designs(){
        $order_id       = isset( $_GET['order_id'] ) ? absint( $_GET['order_id'] ) : 0;
        $order_item_id  = isset( $_GET['order_item_id'] ) ? absint( $_GET['order_item_id'] ) : 0;
        $nbd_item_key   = isset( $_GET['nbd_item_key'] ) ? wc_clean( $_GET['nbd_item_key'] ) : '';
        $nbu_item_key   = isset( $_GET['nbu_item_key'] ) ? wc_clean( $_GET['nbu_item_key'] ) : '';
        $type           = isset( $_GET['type'] ) ? wc_clean( $_GET['type'] ) : 'png';
        if ( !isset( $_GET['type'] ) ) {
            $_GET['type'] = $type;
        }
        /*todo check permission */
        nbd_download_product_designs( $order_id, $order_item_id, $nbd_item_key, $nbu_item_key, $type  );
    }
}