<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<!DOCTYPE html>
<?php
    $hide_on_mobile = nbdesigner_get_option('nbdesigner_disable_on_smartphones');
    $lang_code = str_replace('-', '_', get_locale());
    $locale = substr($lang_code, 0, 2);
    $product_id = (isset($_GET['product_id']) &&  $_GET['product_id'] != '') ? absint($_GET['product_id']) : 0;
    $variation_id = (isset($_GET['variation_id']) &&  $_GET['variation_id'] != '') ? absint($_GET['variation_id']) : nbd_get_default_variation_id( $product_id ); 
    $ui_mode = is_nbd_design_page() ? 2 : 1;/*1: iframe popup, 2: custom page, 3: studio*/
    if( !nbd_is_product($product_id) ){
        echo sprintf('<p>%s, <a href="%s">%s</a></p>', 
                __('No product has been selected', 'web-to-print-online-designer'),
                esc_url( home_url( '/' ) ),
                __('Back', 'web-to-print-online-designer') );
        die();
    }
    if(wp_is_mobile() && $hide_on_mobile == 'yes'):
    nbdesigner_get_template('mobile.php', array('lang_code' => $lang_code));       
    else:
    if( !nbd_check_permission() ):
    nbdesigner_get_template('permission.php');
    else:
?>
<html lang="<?php echo $lang_code; ?>">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
        <title><?php echo get_bloginfo( 'name' ); ?> - Online Designer</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1, minimum-scale=0.5, maximum-scale=1.0"/>
        <meta content="Online Designer - HTML5 Designer - Online Print Solution" name="description" />
        <meta content="Online Designer" name="keywords" />
        <meta content="Netbaseteam" name="author">
        
        <?php
            if( nbdesigner_get_option('nbdesigner_share_design') == 'yes' && isset( $_GET['nbd_share_id'] ) && $_GET['nbd_share_id'] != '' ):
                $folder = $_GET['nbd_share_id'];
                $path = NBDESIGNER_CUSTOMER_DIR . '/' . $folder ;
                $images = Nbdesigner_IO::get_list_images($path, 1);
                $product = wc_get_product( $variation_id ? $variation_id : $product_id );
                if( count($images) ){
                    $image_url = Nbdesigner_IO::wp_convert_path_to_url( reset($images) );            
                }      
                if( isset( $_GET['nbd_share_id'] ) && $_GET['nbd_share_id'] != '' ){
                    $url = add_query_arg(
                        array(
                            't'    => $_GET['t'], 
                            'product_id'    =>  $product_id,
                            'variation_id'   =>  $variation_id,
                            'reference'   =>  $_GET['nbd_share_id'],
                            'nbd_share_id'  =>  $_GET['nbd_share_id']),
                        getUrlPageNBD('create'));  
                }
        ?>
        <meta property="og:locale" content="<?php echo $lang_code; ?>">
        <meta property="og:type" content="article">
        <meta property="og:title" content="<?php echo $product->get_name(); ?>">
        <meta property="og:description" content="<?php echo get_bloginfo( 'description' ); ?>">        
        <meta property="og:url" content="<?php echo $url; ?>">
        <meta property="og:site_name" content="<?php echo get_bloginfo( 'name' ); ?>">
        <meta property="og:image" content="<?php echo $image_url; ?>" />
        <meta property="og:image:width" content="500">
        <meta property="og:image:height" content="400">
        <?php endif; ?>
        
        <link type="text/css" href="<?php echo NBDESIGNER_PLUGIN_URL .'assets/css/jquery-ui.min.css'; ?>" rel="stylesheet" media="all" />
        <link type="text/css" href="<?php echo NBDESIGNER_PLUGIN_URL .'assets/css/font-awesome.min.css'; ?>" rel="stylesheet" media="all" />
        <link href='https://fonts.googleapis.com/css?family=Poppins:400,100,300italic,300' rel='stylesheet' type='text/css'>
        <link type="text/css" href="<?php echo NBDESIGNER_PLUGIN_URL .'assets/css/bootstrap.min.css'; ?>" rel="stylesheet" media="all"/>
        <link type="text/css" href="<?php echo NBDESIGNER_PLUGIN_URL .'assets/css/bundle.css'; ?>" rel="stylesheet" media="all"/>
        <link type="text/css" href="<?php echo NBDESIGNER_PLUGIN_URL .'assets/css/tooltipster.bundle.min.css'; ?>" rel="stylesheet" media="all"/>
        <link type="text/css" href="<?php echo NBDESIGNER_PLUGIN_URL .'assets/css/style.min.css'; ?>" rel="stylesheet" media="all">
        <?php $custom_css_url = file_exists( NBDESIGNER_DATA_DIR . '/custom.css' ) ? NBDESIGNER_DATA_URL .'/custom.css' : NBDESIGNER_PLUGIN_URL .'assets/css/custom.css'; ?>
        <link type="text/css" href="<?php echo $custom_css_url; ?>" rel="stylesheet" media="all">
        <link type="text/css" href="<?php echo NBDESIGNER_PLUGIN_URL .'assets/css/spectrum.css'; ?>" rel="stylesheet" media="all">
        <?php if(is_rtl()): ?>
        <link type="text/css" href="<?php echo NBDESIGNER_PLUGIN_URL .'assets/css/nbdesigner-rtl.css'; ?>" rel="stylesheet" media="all">
        <?php endif; ?>
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->	
        <?php 
            $enableColor = nbdesigner_get_option('nbdesigner_show_all_color'); 
            $enable_upload_multiple = nbdesigner_get_option('nbdesigner_upload_multiple_images'); 
            $task = (isset($_GET['task']) &&  $_GET['task'] != '') ? $_GET['task'] : 'new';
            $task2 = (isset($_GET['task2']) &&  $_GET['task2'] != '') ? $_GET['task2'] : '';
            $design_type = (isset($_GET['design_type']) &&  $_GET['design_type'] != '') ? $_GET['design_type'] : '';
            $nbd_item_key = (isset($_GET['nbd_item_key']) &&  $_GET['nbd_item_key'] != '') ? $_GET['nbd_item_key'] : '';
            $nbu_item_key = (isset($_GET['nbu_item_key']) &&  $_GET['nbu_item_key'] != '') ? $_GET['nbu_item_key'] : '';
            $cart_item_key = (isset($_GET['cik']) &&  $_GET['cik'] != '') ? $_GET['cik'] : '';
            $order_id = (isset($_GET['oid']) &&  $_GET['oid'] != '') ? $_GET['oid'] : '';
            $reference = (isset($_GET['reference']) &&  $_GET['reference'] != '') ? $_GET['reference'] : ''; 
            $redirect_url = (isset($_GET['rd']) &&  $_GET['rd'] != '') ? nbd_get_redirect_url() : (($task == 'new' && $ui_mode == 2) ? wc_get_cart_url() : '');
            //$redirect_url = (isset($_GET['rd']) &&  $_GET['rd'] != '') ? $_GET['rd'] : (($task == 'new' && $ui_mode == 2) ? wc_get_cart_url() : '');
            $_enable_upload = ($task == 'new' && $task2 == 'update' ) ? false : get_post_meta($product_id, '_nbdesigner_enable_upload', true);  
            $_enable_upload_without_design = ( isset($_GET['task2']) && $_GET['task2'] == 'add_file' ) ? true : get_post_meta($product_id, '_nbdesigner_enable_upload_without_design', true);  
            $enable_upload = $_enable_upload ? 2 : 1;
            $enable_upload_without_design = $_enable_upload_without_design ? 2 : 1;
            $_product = wc_get_product( $product_id );
            $product_type = $_product->get_type();
            $show_variation = ( (!isset($_GET['variation_id']) || $_GET['variation_id'] == '') && $product_type == 'variable' && $ui_mode == 2 && $task == 'new' ) ? 1 : 0;
            if( $task == 'reup' ){
                $list_file_upload = nbd_get_upload_files_from_session( $nbu_item_key );
            }else {
                $list_file_upload = '';
            }
            $home_url = $icl_home_url = untrailingslashit(get_option('home'));
            $is_wpml = 0;
            $font_url = NBDESIGNER_FONT_URL;
            if ( class_exists( 'SitePress' ) ) {
                if ( function_exists( 'icl_get_home_url' ) ) {
                    $icl_home_url = untrailingslashit(icl_get_home_url());
                    $is_wpml = 1;
                    $font_url = str_replace(untrailingslashit(get_option('home')), untrailingslashit(icl_get_home_url()), $font_url);
                }
            }
            $product_data = nbd_get_product_info( $product_id, $variation_id, $nbd_item_key, $task, $task2, $reference );
            if( isset($product_data['option']['use_all_color']) ) $enableColor = $product_data['option']['use_all_color'] == 1 ? 'yes' : 'no';
        ?>
        <script type="text/javascript">
            var NBDESIGNCONFIG = {
                lang_code   :   "<?php echo $lang_code; ?>",
                lang_rtl    :   "<?php if(is_rtl()){ echo 'rtl'; } else {  echo 'ltr';  } ?>",
                is_mobile   :   "<?php echo wp_is_mobile(); ?>",
                ui_mode   :   "<?php echo $ui_mode; ?>",
                show_variation   :   "<?php echo $show_variation; ?>",
                enable_upload   :   "<?php echo $enable_upload; ?>",
                enable_upload_without_design   :   "<?php echo $enable_upload_without_design; ?>",
                stage_dimension :   {'width' : 500, 'height' : 500},
                nbd_content_url    :   "<?php echo NBDESIGNER_DATA_URL; ?>",
                font_url    :   "<?php echo $font_url; ?>",
                art_url    :   "<?php echo NBDESIGNER_ART_URL; ?>",
                is_designer :  <?php if(current_user_can('edit_nbd_template')) echo 1; else echo 0; ?>,
                assets_url  :   "<?php echo NBDESIGNER_PLUGIN_URL . 'assets/'; ?>",
                ajax_url    : "<?php echo admin_url('admin-ajax.php'); ?>",
                nonce   :   "<?php echo wp_create_nonce('save-design'); ?>",
                nonce_get   :   "<?php echo wp_create_nonce('nbdesigner-get-data'); ?>",
                instagram_redirect_uri    : "<?php echo NBDESIGNER_PLUGIN_URL.'includes/auth-instagram.php'; ?>",
                dropbox_redirect_uri    : "<?php echo NBDESIGNER_PLUGIN_URL.'includes/auth-dropbox.php'; ?>",
                cart_url    :   "<?php echo esc_url( wc_get_cart_url() ); ?>",
                task    :   "<?php echo $task; ?>",
                task2    :   "<?php echo $task2; ?>",
                design_type    :   "<?php echo $design_type; ?>",
                product_id  :   "<?php echo $product_id; ?>",
                variation_id  :   "<?php echo $variation_id; ?>",
                product_type  :   "<?php echo $product_type; ?>",
                redirect_url    :   "<?php echo $redirect_url; ?>",
                nbd_item_key    :   "<?php echo $nbd_item_key; ?>",
                nbu_item_key    :   "<?php echo $nbu_item_key; ?>",
                cart_item_key    :   "<?php echo $cart_item_key; ?>",
                order_id    :   "<?php echo $order_id; ?>",
                home_url    :   "<?php echo $home_url; ?>",
                icl_home_url    :   "<?php echo $icl_home_url; ?>",
                is_logged    :   <?php echo nbd_user_logged_in(); ?>,
		is_wpml	:	<?php echo $is_wpml; ?>,     
		enable_upload_multiple	:   "<?php echo $enable_upload_multiple; ?>",   
                login_url   :   "<?php echo esc_url( wp_login_url( getUrlPageNBD('redirect') ) ); ?>",  
                list_file_upload    :   <?php echo json_encode($list_file_upload); ?>,
                product_data  :   <?php echo json_encode($product_data); ?>,
                subsets: <?php echo json_encode(nbd_font_subsets()); ?>
            };
            NBDESIGNCONFIG['default_variation_id'] = NBDESIGNCONFIG['variation_id'];
            <?php 
                $settings = nbdesigner_get_all_frontend_setting();
                foreach ($settings as $key => $val):
                    if(is_numeric($val)):
            ?>
                NBDESIGNCONFIG['<?php echo $key; ?>'] = <?php echo $val; ?>;
                <?php else: ?>
                NBDESIGNCONFIG['<?php echo $key; ?>'] = "<?php echo $val; ?>";    
                <?php endif; ?>    
            <?php endforeach; ?>
            <?php 
            if( isset($product_data['option']['color_cats']) ):
                $cats = $product_data['option']['color_cats'];
                $colors = Nbdesigner_IO::read_json_setting(NBDESIGNER_DATA_DIR . '/colors.json');
                $colors = array_filter($colors, function ($val) use ($cats){
                    $check = false;
                    if( sizeof($val->cat) == 0 ){
                        if( in_array('0', $cats) ) $check = true;
                    }else{
                        $intercept = array_intersect($val->cat, $cats);
                        if( count($intercept) == count($val->cat) )  $check = true;
                    }
                    return $check;
                });
                $list_color = [];
                foreach( $colors as $color ){
                    $list_color[] = $color->hex;
                }
                $list_color = array_unique($list_color);
            ?>
            var  colorPalette = [], row = [], __colorPalette = [], color = '';
                <?php foreach($list_color as $cindex => $color): ?>
                    color = "<?php echo $color; ?>";
                    row.push(color);
                    <?php if( $cindex % 10 == 9 ): ?>
                        colorPalette.push(row);
                        row = [];
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php elseif( isset($product_data['option']['list_color']) ): ?>
            var  colorPalette = [], row = [], __colorPalette = [], color = '';
                <?php foreach($product_data['option']['list_color'] as $cindex => $color): ?>
                    color = "<?php echo $color['code']; ?>";
                    row.push(color);
                    <?php if( $cindex % 10 == 9 ): ?>
                        colorPalette.push(row);
                        row = [];
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
            var _colors = NBDESIGNCONFIG['nbdesigner_hex_names'].split(','),
            colorPalette = [], row = [];
            for(var i=0; i < _colors.length; ++i) {
                var color = _colors[i].split(':')[0];
                row.push(color);
                if(i % 10 == 9){
                    colorPalette.push(row);
                    row = [];
                }               
            }
            <?php endif; ?>
            row.push(NBDESIGNCONFIG['nbdesigner_default_color']);
            colorPalette.push(row);                                  
            <?php if($ui_mode == 1): ?>
                nbd_window = window.parent;
            <?php else: ?>      
                nbd_window = window;
            <?php endif; ?>  
            var NBDESIGNLANG = <?php echo json_encode(nbd_get_language( $lang_code ));  ?>  
        </script>
    </head>
    <body ng-app="app" class="nbd-mode-<?php echo $ui_mode; ?>">
        <div style="width: 100%; height: 100%;overflow: hidden;" ng-controller="DesignerController" ng-cloak>
            <div id="design-container" class="design-mode" ng-class="designMode == 'custom' ? 'active' : ''">
                <div class="container-fluid" id="designer-controller">
                    <?php
                    include_once('components/menu.php');
                    include_once('components/design_area.php');
                    include_once('components/info.php');
                    ?>
                </div>
                <div id="od_modal">
                    <?php
                    include_once('components/modal_clipart.php');
                    include_once('components/modal_upload.php');
                    include_once('components/modal_qrcode.php');
                    include_once('components/modal_preview.php');
                    include_once('components/modal_pattern.php');
                    include_once('components/modal_fonts.php');
                    include_once('components/modal_crop_image.php');
                    include_once('components/modal_config_art.php');
                    include_once('components/modal_share.php');		
                    include_once('components/modal_expand_feature.php');		
                    include_once('components/modal_products.php');		
                    include_once('components/modal-custom-dimension.php');		
                    include_once('components/modal_bleed_tip.php');		
                    include_once('components/modal_product_info.php');		
                    include_once('components/modal_product_info_preview.php');		
                    //include_once('components/popover_preset_fields.php');		
                    ?>
                </div>
                <div id="od_config" ng-class="modeMobile ? 'mobile' : 'modepc'">	
                    <?php
                    include_once('components/config_text.php');
                    include_once('components/config_clipart.php');
                    include_once('components/config_image.php');
                    include_once('components/config_draw.php');
                    ?>
                    <span class="hide-config fa fa-chevron-down e-shadow e-hover-shadow item-config" ng-show="modeMobile"></span>
                    <span class="hide-tool-config fa fa-chevron-down e-shadow e-hover-shadow item-config" ng-hide="modeMobile" ng-style="{'display' : (pop.text == 'block' || pop.art == 'block' || pop.qrcode == 'block' || pop.clipArt == 'block' || pop.draw == 'block') ? 'block' : 'none'}"></span>
                </div>
                <?php
                if(NBDESIGNER_MODE_DEV){
                    include_once('components/config_style.php');           
                }
                include_once('components/popover_layer.php');
                include_once('components/popover_tools.php');
                include_once('components/popover_color.php');
                include_once('components/popover_layer_color.php');
                include_once('components/tool_top.php');
                include_once('components/helpdesk.php');
                ?>
            </div> 
            <div id="upload-container" ng-class="designMode == 'upload' ? 'active' : ''" class="design-mode upload-container">
                <div class="inner">
                    <?php include_once('components/upload-design.php'); ?>
                </div>    
            </div>
            <div class="od_processing">
                <?php include_once('components/loading.php'); ?>
            </div>
            <?php if( $reference == '' ): ?>
            <div class="design-options" id="design-options" ng-show="settings['enable_upload'] == '2' && settings['enable_upload_without_design'] == '1' && settings['task'] == 'new'">
                <div class="inner">
                    <div>
                        <div class="option shasow" ng-click="changeDesignMode('upload')"><i class="fa fa-cloud-upload" aria-hidden="true"></i>{{(langs['UPLOAD__DESIGN']) ? langs['UPLOAD__DESIGN'] : "Upload Design"}}</div>
                        <div class="option shasow" ng-click="changeDesignMode('custom')"><i class="fa fa-paint-brush" aria-hidden="true"></i>{{(langs['CUSTOM_DESIGN']) ? langs['CUSTOM_DESIGN'] : "Custom Design"}}</div>
                    </div>
                </div>
            </div> 
            <?php endif; ?>
            <?php if(!NBDESIGNER_MODE_DEV): ?>
            <script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
            <?php else: ?>
            <script type='text/javascript' src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/libs/jquery.min.js'; ?>"></script>
            <?php endif; ?>
            <?php if(!NBDESIGNER_MODE_DEV): ?>
            <script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
            <?php else: ?>
            <script type='text/javascript' src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/libs/jquery-ui.min.js'; ?>"></script>
            <?php endif; ?>
            <script type="text/javascript" src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/js/touch.js'; ?>"></script>
            <?php if(!NBDESIGNER_MODE_DEV): ?>
            <script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
            <?php else: ?>
            <script type='text/javascript' src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/libs/bootstrap.min.js'; ?>"></script>
            <?php endif; ?>
            <?php if(!NBDESIGNER_MODE_DEV): ?>
            <script type='text/javascript' src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.0-rc.2/angular.min.js"></script>
            <?php else: ?>
            <script type='text/javascript' src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/libs/angular.min.js'; ?>"></script>
            <?php endif; ?>
            <?php if(!NBDESIGNER_MODE_DEV): ?>
            <script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/2.4.1/lodash.js"></script>
            <?php else: ?>
            <script type='text/javascript' src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/libs/lodash.js'; ?>"></script>
            <?php endif; ?>
            <script type="text/javascript" src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/js/bundle.min.js'; ?>"></script>
            <script type="text/javascript" src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/js/fabric.curvedText.js'; ?>"></script>
            <script type="text/javascript" src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/js/fabric.removeColor.js'; ?>"></script>
            <script type="text/javascript" src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/js/_layout.js'; ?>"></script>
            <script type="text/javascript" src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/js/spectrum.js'; ?>"></script>
            <script type="text/javascript" src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/js/qrcode.js'; ?>"></script>
            <script type="text/javascript" src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/js/add-to-cart-variation.js'; ?>"></script>
            <script type="text/javascript" src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
            <!-- <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/spectrum/1.3.0/js/spectrum.min.js"></script>    -->
            <script type="text/javascript" src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/js/designer.min.js'; ?>"></script>	
        </div>
    </body>
</html>
<?php endif; endif;?>