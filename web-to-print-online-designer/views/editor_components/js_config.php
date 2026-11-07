<?php if ( ! defined( 'ABSPATH' ) ) { exit;}
    
    $product_id = $_GET['product_id'] ? $_GET['product_id'] : $product_id; 
    $global_min_dpi = nbdesigner_get_option( 'nbdesigner_mindpi_upload', 0 );
    $product_cats_ids = wc_get_product_term_ids( $product_id, 'product_cat' );
    $cate_min_dpi = 0;
    $arr_cate_min_dpi =[0];
    foreach( $product_cats_ids as $cat_id ) {
        $term_meta = get_option("taxonomy_" . $cat_id);
        if( $term_meta && isset($term_meta['min_dpi_cate']) ) {
            array_push($arr_cate_min_dpi,$term_meta['min_dpi_cate']);
        }
    }
    $cate_min_dpi = max($arr_cate_min_dpi);
    $product_min_dpi = get_post_meta($product_id, '_min_dpi', true) ? get_post_meta($product_id, '_min_dpi', true) : 0;
    $min_dpi = 0;
    if($global_min_dpi > 0 && $cate_min_dpi == 0 && $product_min_dpi == 0){
        $min_dpi = $global_min_dpi;
    }elseif ($cate_min_dpi > 0 && $product_min_dpi == 0) {
        $min_dpi = $cate_min_dpi;
    }elseif ($product_min_dpi > 0) {
        $min_dpi = $product_min_dpi;
    }
?>
<script type="text/javascript">
    var NBDESIGNCONFIG = {
        lang_code: "<?php echo $lang_code; ?>",
        lang_rtl: "<?php if(is_rtl()){ echo 'rtl'; } else {  echo 'ltr';  } ?>",
        is_mobile: "<?php echo wp_is_mobile(); ?>",
        ui_mode: "<?php echo $ui_mode; ?>",
        min_dpi:  "<?php echo  $min_dpi; ?>",
        layout: "<?php echo $layout; ?>",
        show_nbo_option: "<?php echo $show_nbo_option; ?>",
        edit_option_mode: <?php if( isset($_GET['nbo_cart_item_key']) && $_GET['nbo_cart_item_key'] != '' ) echo '1'; else echo '0'; ?>,
        enable_upload: "<?php echo $enable_upload; ?>",
        enable_upload_without_design: "<?php echo $enable_upload_without_design; ?>",
        nbd_content_url: "<?php echo NBDESIGNER_DATA_URL; ?>",
        font_url: "<?php echo $font_url; ?>",
        art_url: "<?php echo NBDESIGNER_ART_URL; ?>",
        assets_url: "<?php echo NBDESIGNER_PLUGIN_URL . 'assets/'; ?>",
        plg_url: "<?php echo NBDESIGNER_PLUGIN_URL; ?>",
        ajax_url: "<?php echo admin_url('admin-ajax.php'); ?>",
        nonce: "<?php echo wp_create_nonce('save-design'); ?>",
        nonce_get: "<?php echo wp_create_nonce('nbdesigner-get-data'); ?>",
        instagram_redirect_uri: "<?php echo NBDESIGNER_PLUGIN_URL . 'includes/auth-instagram.php'; ?>",
        dropbox_redirect_uri: "<?php echo NBDESIGNER_PLUGIN_URL . 'includes/auth-dropbox.php'; ?>",
        cart_url: "<?php echo wc_get_cart_url(); ?>",
        task: "<?php echo $task; ?>",
        task2: "<?php echo $task2; ?>",
        design_type: "<?php echo $design_type; ?>",
        product_id: "<?php echo $product_id; ?>",
        variation_id: "<?php echo $variation_id; ?>",
        product_type: "<?php echo $product_type; ?>",
        redirect_url: "<?php echo $redirect_url; ?>",
        nbd_item_key: "<?php echo $nbd_item_key; ?>",
        nbu_item_key: "<?php echo $nbu_item_key; ?>",
        cart_item_key: "<?php echo $cart_item_key; ?>",
        home_url: "<?php echo $home_url; ?>",
        icl_home_url: "<?php echo $icl_home_url; ?>",
        is_logged: <?php echo nbd_user_logged_in(); ?>,
        is_wpml: <?php echo $is_wpml; ?>,
        enable_upload_multiple:   "<?php echo $enable_upload_multiple; ?>",
        //login_url: "<?php //echo wp_login_url( getUrlPageNBD('redirect') ); ?>",
        login_url: "<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ).'?nbd_redirect=1'; ?>",
        product_data: <?php echo json_encode($product_data); ?>,
        fonts: <?php echo nbd_get_fonts(); ?>,
        subsets: <?php echo json_encode(nbd_font_subsets()); ?>,
        fbID: "<?php echo $fbID; ?>",
        nbd_create_own_page: "<?php echo getUrlPageNBD('create'); ?>",
        link_get_options: "<?php echo $link_get_options; ?>",
        valid_license: "<?php echo $valid_license ? 1 : 0; ?>",
        enable_dropbox: false,
        is_available_imagick: "<?php echo is_available_imagick() ? 1 : 0; ?>",
        background_settings: <?php echo json_encode(unserialize( get_option('_nb_background_settings'))); ?>,  // custom background settings
        /* customize */
        //user_infos: <?php //echo json_encode(nbd_get_user_information()); ?>,
        //contact_sheets: <?php //echo json_encode(nbd_get_user_contact_sheet()); ?>,
        default_font: <?php echo $default_font; ?>,
        templates: <?php echo json_encode($templates); ?>,
        page_type: "<?php echo $page_type; ?>", // custom 30/11
        nbdlangs: {
            cliparts: "<?php esc_html_e('Cliparts', 'web-to-print-online-designer'); ?>",
            alert_upload_term: "<?php esc_html_e('Please accept the upload term conditions', 'web-to-print-online-designer'); ?>",
            path: "<?php esc_html_e('Vector', 'web-to-print-online-designer'); ?>",
            image: "<?php esc_html_e('Image', 'web-to-print-online-designer'); ?>",
            rect: "<?php esc_html_e('Rectangle', 'web-to-print-online-designer'); ?>",
            triangle: "<?php esc_html_e('Triangle', 'web-to-print-online-designer'); ?>",
            line: "<?php esc_html_e('Line', 'web-to-print-online-designer'); ?>",
            polygon: "<?php esc_html_e('Polygon', 'web-to-print-online-designer'); ?>",
            circle: "<?php esc_html_e('Circle', 'web-to-print-online-designer'); ?>",
            ellipse: "<?php esc_html_e('Ellipse', 'web-to-print-online-designer'); ?>",
            group: "<?php esc_html_e('Group', 'web-to-print-online-designer'); ?>",
            pro_license_alert: "<?php esc_html_e('This item is not available in Lite version!', 'web-to-print-online-designer'); ?>",
            confirm_delete_design: "<?php esc_html_e('Do you want to delete this design?', 'web-to-print-online-designer'); ?>",
            my_design: "<?php esc_html_e('My design', 'web-to-print-online-designer'); ?>",
            cover: "<?php esc_html_e('Cover', 'web-to-print-online-designer'); ?>",
            front_cover: "<?php esc_html_e('Front cover', 'web-to-print-online-designer'); ?>",
            back_cover: "<?php esc_html_e('Back cover', 'web-to-print-online-designer'); ?>",
            page: "<?php esc_html_e('Page', 'web-to-print-online-designer'); ?>",
            pages: "<?php esc_html_e('Pages', 'web-to-print-online-designer'); ?>",
            'image-layer': "<?php esc_html_e('Group', 'web-to-print-online-designer'); ?>",
            mask: "<?php esc_html_e('Mask', 'web-to-print-online-designer'); ?>",
            iosPlaceholderText: "<?php esc_html_e('Click here to open camera.', 'web-to-print-online-designer'); ?>",
            templates: "<?php esc_html_e('Templates', 'web-to-print-online-designer'); ?>",
            supported_extensions: "<?php esc_html_e('Supported extensions: PNG, JPE, JPEG, SVG', 'web-to-print-online-designer'); ?>",
            supported_extensions2: "<?php esc_html_e('Supported extensions: PNG, JPE, JPEG, SVG, PDF', 'web-to-print-online-designer'); ?>",
            min_file_size: "<?php esc_html_e('Min file size: ', 'web-to-print-online-designer'); ?>",
            max_file_size: "<?php esc_html_e('Max file size: ', 'web-to-print-online-designer'); ?>",
            wrong_to_convert_outline_font: "<?php esc_html_e('Wrong to convert font to outlines!', 'web-to-print-online-designer'); ?>",
            warningDPI: "<?php esc_html_e('Resizing your image may lower your image quality!', 'web-to-print-online-designer'); ?>",
            backgrounds: "<?php esc_html_e('Backgrounds', 'web-to-print-online-designer'); ?>",
            warningLayerRanger: "<?php echo nbdesigner_get_option('nbdesigner_alert_mask_layer_outside'); ?>",
            warningLayerType: "<?php echo nbdesigner_get_option('nbdesigner_alert_mask_layer_invalid'); ?>",
        },
        is_admin: <?php echo current_user_can('administrator') ? 1 : 0; ?>,
    };
    NBDESIGNCONFIG['default_variation_id'] = NBDESIGNCONFIG['variation_id'];
    NBDESIGNCONFIG['template_tags'] = <?php echo json_encode( $template_data['template_tags'] ); ?>;
    <?php 
        if( isset( $force_hide_print_option ) ){
            ?>
            NBDESIGNCONFIG['force_hide_print_option'] = 1;
            <?php
        }
        $settings       = nbdesigner_get_all_frontend_setting();
        $nbls_enable    = get_post_meta( $product_id, '_nbls_enable', true );
        if( $nbls_enable ){
            $local_settings = get_post_meta( $product_id, '_nbls_settings', true );
            if( $local_settings ){
                $local_settings = unserialize( $local_settings );
            } else {
                $local_settings = array();
            }
            $settings = array_merge( $settings, $local_settings );
        }
        $nbes_settings              = get_post_meta( $product_id, '_nbes_settings', true );
        $nbes_enable_settings       = get_post_meta( $product_id, '_nbes_enable_settings', true );
        $design_option              = unserialize( get_post_meta( $product_id, '_nbdesigner_option', true ) );
        $custom_background_colors      = unserialize( get_option('_nb_background_colors') ); // custom charmingprint: background colors
        if( $custom_background_colors ){
            $settings['custom_background_colors'] = $custom_background_colors;
        }
        if( $nbes_settings ){
            $settings['nbes_settings'] = unserialize( $nbes_settings );
        }
        if( $nbes_enable_settings ){
            $settings['nbes_enable_settings'] = unserialize( $nbes_enable_settings );
        }
        if( isset( $design_option['unit'] ) ){
            $settings['nbdesigner_dimensions_unit'] = $design_option['unit'];
        }
        foreach ($settings as $key => $val):
            if(is_numeric($val)):
    ?>
        NBDESIGNCONFIG['<?php echo $key; ?>'] = <?php echo $val; ?>;
        <?php elseif(is_array($val) ): ?>
        NBDESIGNCONFIG['<?php echo $key; ?>'] = <?php echo json_encode($val); ?>;
        <?php else: ?>
        NBDESIGNCONFIG['<?php echo $key; ?>'] = "<?php echo $val; ?>";
        <?php endif; ?>
    <?php endforeach; ?>
    <?php if( isset($product_data['option']['use_all_color']) ): ?>
        NBDESIGNCONFIG['nbdesigner_show_all_color'] = "<?php echo $product_data['option']['use_all_color'] == 1 ? 'yes' : 'no'; ?>";
    <?php endif; ?>
    var  colorPalette = [], row = [], __colorPalette = [], color = '';
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
        <?php foreach($list_color as $cindex => $color): ?>
            color = "<?php echo $color; ?>";
            row.push(color);
            <?php if( $cindex % 10 == 9 ): ?>
                colorPalette.push(row);
                row = [];
            <?php endif; ?>
            __colorPalette.push(color);
        <?php endforeach; ?>
    <?php elseif( isset($product_data['option']['list_color']) ): ?>
        <?php foreach($product_data['option']['list_color'] as $cindex => $color): ?>
            color = "<?php echo $color['code']; ?>";
            row.push(color);
            <?php if( $cindex % 10 == 9 ): ?>
                colorPalette.push(row);
                row = [];
            <?php endif; ?>
            __colorPalette.push(color);
        <?php endforeach; ?>
    <?php 
        elseif( isset( $settings['nbes_enable_settings'] ) && ( $settings['nbes_enable_settings']['combination'] == 1 || $settings['nbes_enable_settings']['foreground'] == 1 ) ): 
            $show_all_color = 'yes';
            if( $settings['nbes_enable_settings']['combination'] == 1 ){
                $show_all_color = 'no';
                $settings['nbes_enable_settings']['foreground'] = 0;
                $settings['nbes_enable_settings']['background'] = 0; 
                if( count( $settings['nbes_settings']['combination_colors']['fg_codes']) > 0 ){
            ?>
                NBDESIGNCONFIG.nbdesigner_default_color = "<?php echo $settings['nbes_settings']['combination_colors']['fg_codes'][0]; ?>";
                __colorPalette = [NBDESIGNCONFIG.nbdesigner_default_color], colorPalette = [ __colorPalette ];
                NBDESIGNCONFIG.forceForeground = true;
            <?php    
                }
            } elseif ( $settings['nbes_enable_settings']['foreground'] == 1 ){
                $show_all_color = 'no'; ?>
                <?php foreach( $settings['nbes_settings']['foreground_colors']['codes'] as $index => $color): ?> 
                    color = "<?php echo $color; ?>";
                    row.push(color);
                    <?php if( $index % 10 == 9 ): ?>
                        colorPalette.push(row);
                        row = [];
                    <?php endif; ?>
                    __colorPalette.push(color);
                <?php
                    endforeach; 
                    if( count( $settings['nbes_settings']['foreground_colors']['codes']) > 0 ){
                ?>
                NBDESIGNCONFIG.nbdesigner_default_color = "<?php echo $settings['nbes_settings']['foreground_colors']['codes'][0]; ?>";
                <?php
                    }
            }
    ?>
        NBDESIGNCONFIG['nbdesigner_show_all_color'] = "<?php echo $show_all_color; ?>";
    <?php else: ?>
    var _colors = NBDESIGNCONFIG['nbdesigner_hex_names'].split(',');
    for( var i=0; i < _colors.length; ++i ) {
        color = _colors[i].split(':')[0];
        row.push(color);
        if(i % 10 == 9){
            colorPalette.push(row);
            row = [];
        }
        __colorPalette.push(color);
    }
    row.push(NBDESIGNCONFIG['nbdesigner_default_color']);
    colorPalette.push(row); 
    <?php endif; ?>
    <?php if($ui_mode == 1): ?>
        nbd_window = window.parent;
    <?php else: ?>
        nbd_window = window;
    <?php endif; ?>
    <?php if( $layout == 'visual' ): ?>
        window.preventSubmitFormCart = true;
    <?php endif; ?>
    <?php do_action('nbd_js_config'); ?>
</script>