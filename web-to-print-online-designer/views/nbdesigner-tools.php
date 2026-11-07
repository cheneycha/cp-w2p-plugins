<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div class="nbdesign-migrate nbd-tool-section nbd-hide-deprecated">
    <h2><?php  esc_html_e('Migrate website domain', 'web-to-print-online-designer'); ?></h2>
    <p><?php  esc_html_e('Update url, path: cliparts, fonts...', 'web-to-print-online-designer'); ?></p>
    <div>
        <table class="form-table" id="nbdesigner-migrate-info">
            <?php wp_nonce_field('nbdesigner-migrate-key', '_nbdesigner_migrate_nonce'); ?>
            <tr valign="top" class="" >
                <th scope="row" class="titledesc"><?php  esc_html_e("Old domain", 'web-to-print-online-designer'); ?> </th>
                <td class="forminp-text">
                    <input type="email" class="regular-text" name="old_domain" placeholder="http://old-domain.com"/>
                    <div class="description">
                        <small id="nbdesigner_key_mes"><?php esc_html_e('Fill your old domain, example: "http://old-domain.com".', 'web-to-print-online-designer'); ?></small>
                    </div>
                </td>
            </tr>
            <tr valign="top" class="" > 
                <th scope="row" class="titledesc"><?php  esc_html_e("New domain", 'web-to-print-online-designer'); ?> </th>
                <td class="forminp-text">
                    <input type="email" class="regular-text" name="new_domain" placeholder="http://new-domain.com"/>
                    <div class="description">
                        <small id="nbdesigner_key_mes"><?php esc_html_e('Fill your new domain, example: "http://new-domain.com".', 'web-to-print-online-designer'); ?></small>
                    </div>
                </td>
            </tr>
        </table>
        <p class="submit">
            <button class="button-primary" id="nbdesigner_update_data_migrate" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php  esc_html_e("Update", 'web-to-print-online-designer'); ?></button>
            <button class="button-primary" id="nbdesigner_resote_data_migrate" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php  esc_html_e("Restore", 'web-to-print-online-designer'); ?></button>
            <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-tool-img-loading" id="nbdesigner_migrate_loading" />
        </p>
    </div>
</div>
<div class="nbdesign-migrate nbd-tool-section nbd-hide-deprecated">
    <h2><?php  esc_html_e('Theme check', 'web-to-print-online-designer'); ?></h2>
    <div id="nbdesign-theme-check">
        <?php wp_nonce_field('nbdesigner-check-theme-key', '_nbdesigner_check_theme_nonce'); ?>
        <button class="button-primary" id="nbdesigner_check_theme"><?php  esc_html_e("Start check", 'web-to-print-online-designer'); ?></button>
        <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-tool-img-loading" id="nbdesigner_check_theme_loading" />
        <div class="theme_check_note"></div>
    </div>
    <div id="nbdesigner-result-check-theme" class="nbd-admin-tool-margin-bottom-15"></div>
</div>
<div class="nbdesigner-editor nbd-tool-section">
    <h2>
        <?php  esc_html_e('Edit custom CSS for NBDesigner frontend', 'web-to-print-online-designer'); ?>
        <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-tool-img-loading" id="nbdesigner_custom_css_loading" />
    </h2>
    <div id="nbdesigner_custom_css_con">
        <?php wp_nonce_field('nbdesigner-custom-css', '_nbdesigner_custom_css'); ?>
        <textarea cols="70" rows="30" name="nbdsigner_custom_css" id="nbdsigner_custom_css" ><?php echo esc_html( $custom_css ); ?></textarea>
    </div>
    <div class="nbd-admin-tool-margin-top-15">
        <button class="button-primary" id="nbdesigner_custom_css" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php esc_html_e('Update Custom CSS', 'web-to-print-online-designer') ?></button>
        <small><?php esc_html_e('Using bad CSS code could break the appearance of your plugin', 'web-to-print-online-designer') ?></small>
    </div>
    <script language="javascript">
            jQuery( document ).ready( function($) {
                var editorCodeMirror = CodeMirror.fromTextArea( document.getElementById( "nbdsigner_custom_css" ), {lineNumbers: true, lineWrapping: true} );
                $('#nbdesigner_custom_css').on('click', function(e){
                    var formdata = jQuery('#nbdesigner_custom_css_con').find('input').serialize();
                    var content = editorCodeMirror.getValue();
                    formdata = formdata + '&action=nbdesigner_custom_css&content=' + encodeURIComponent( content );
                    jQuery('#nbdesigner_custom_css_loading').removeClass('nbdesigner_loaded');
                    jQuery.post(admin_nbds.url, formdata, function(_data){
                        jQuery('#nbdesigner_custom_css_loading').addClass('nbdesigner_loaded');
                        var data = JSON.parse(_data);
                        if (data.flag == 1) {
                            swal(admin_nbds.nbds_lang.complete, data.mes, "success");
                        }else{
                            swal({
                                title: "Oops!",
                                text: data.mes,
                                imageUrl: admin_nbds.assets_images + "dinosaur.png"
                            });
                        }
                    });
                });
            });
    </script>
</div>
<div class="nbdesigner-editor nbd-tool-section">
    <h2>
        <?php  esc_html_e('Edit custom JS for NBDesigner frontend', 'web-to-print-online-designer'); ?>
        <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-tool-img-loading" id="nbdesigner_custom_js_loading" />
    </h2>
    <div id="nbdesigner_custom_js_con">
        <?php wp_nonce_field('nbdesigner-custom-css', '_nbdesigner_custom_css'); ?>
        <textarea cols="70" rows="30" name="nbdsigner_custom_js" id="nbdsigner_custom_js" ><?php echo esc_html( $custom_js ); ?></textarea>
    </div>
    <div class="nbd-admin-tool-margin-top-15">
        <button class="button-primary" id="nbdesigner_custom_js" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php esc_html_e('Update Custom JS', 'web-to-print-online-designer') ?></button>
        <small><?php esc_html_e('Using bad JS code could break the appearance of your plugin', 'web-to-print-online-designer') ?></small>
    </div>
    <script language="javascript">
            jQuery( document ).ready( function($) {
                var editorCodeMirrorJS = CodeMirror.fromTextArea( document.getElementById( "nbdsigner_custom_js" ), {lineNumbers: true, lineWrapping: true} );
                $('#nbdesigner_custom_js').on('click', function(e){
                    var formdata = jQuery('#nbdesigner_custom_js_con').find('input').serialize();
                    var content = editorCodeMirrorJS.getValue();
                    formdata = formdata + '&action=nbdesigner_custom_js&content=' + encodeURIComponent( content );
                    jQuery('#nbdesigner_custom_js_loading').removeClass('nbdesigner_loaded');
                    jQuery.post(admin_nbds.url, formdata, function(_data){
                        jQuery('#nbdesigner_custom_js_loading').addClass('nbdesigner_loaded');
                        var data = JSON.parse(_data);
                        if (data.flag == 1) {
                            swal(admin_nbds.nbds_lang.complete, data.mes, "success");
                        }else{
                            swal({
                                title: "Oops!",
                                text: data.mes,
                                imageUrl: admin_nbds.assets_images + "dinosaur.png"
                            });
                        }
                    });
                });
            });
    </script>
</div>
<div class="update-setting-data nbd-tool-section  nbd-hide-deprecated" >
    <h2><?php  esc_html_e('Update product design setting data', 'web-to-print-online-designer'); ?></h2>
    <div>
        <?php wp_nonce_field('nbdesigner-update-product', '_nbdesigner_update_product'); ?>
        <button class="button nbdesigner-delete" id="nbdesigner_update_product" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php  esc_html_e("Update v1.7.0", 'web-to-print-online-designer'); ?></button>
        <button class="button nbdesigner-delete" id="nbdesigner_update_variation_v180" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php  esc_html_e("Update v1.8.0", 'web-to-print-online-designer'); ?></button>
        <!--<button class="button nbdesigner-delete" id="nbdesigner_update_template" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php  esc_html_e("Update templates", 'web-to-print-online-designer'); ?></button>-->
        <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-tool-img-loading" id="nbdesigner_update_product_loading" />        
        <p><small><?php esc_html_e('Make sure backup data before update avoid lost data!', 'web-to-print-online-designer') ?></small></p>
    </div>
</div>
<div id="nbd-clear-transients-con" class="nbd-tool-section">
    <h2><?php  esc_html_e('Clear transients', 'web-to-print-online-designer'); ?></h2>
    <div>
        <?php wp_nonce_field('nbd-clear-transients', '_nbdesigner_cupdate_product'); ?>
        <button class="button nbdesigner-delete" id="nbd-clear-transients" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php  esc_html_e("Clear transients", 'web-to-print-online-designer'); ?></button>
        <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-tool-img-loading" id="nbdesigner_clear_transients_loading" />   
        <p><small><?php esc_html_e('This tool will clear the NBD product transients cache!', 'web-to-print-online-designer'); ?></small></p>
    </div>
</div>
<div id="nbd-setup-wizard" class="nbd-tool-section">
    <h2><?php esc_html_e('Create default NBDesigner pages', 'web-to-print-online-designer'); ?></h2>
    <?php wp_nonce_field('nbd-create-pages', '_nbdesigner_update_product'); ?>
    <button class="button-primary" id="nbd-create-pages" <?php if(!current_user_can('update_nbd_data')) echo "disabled"; ?>><?php esc_html_e('Create pages', 'web-to-print-online-designer'); ?></button>
    <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-tool-img-loading" id="nbdesigner_create_pages_loading" />   
    <p><strong class="nbd-admin-tool-note"><?php esc_html_e('Note', 'web-to-print-online-designer'); ?>: </strong><?php esc_html_e('This tool will install all the missing NBDesigner pages. Pages already defined and set up will not be replaced.', 'web-to-print-online-designer'); ?></p>
</div>
<div id="nbd-logs" class="nbd-tool-section">
    <h2><?php esc_html_e('Logs', 'web-to-print-online-designer'); ?><a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'remove_log' ), admin_url( 'admin.php?page=nbdesigner_tools' ) ), 'remove_log' ) ); ?>" class="button-primary nbd-admin-tool-delete-log" ><?php esc_html_e('Delete log', 'web-to-print-online-designer'); ?></a></h2>
    <form action="<?php echo admin_url( 'admin.php?page=wc-status&tab=logs' ); ?>" method="post">

    </form>
    <div class="nbd-admin-tool-debug-log">
        <?php if(file_exists(NBDESIGNER_LOG_DIR . '/debug.log')): ?>
        <pre><?php echo esc_html( file_get_contents( NBDESIGNER_LOG_DIR . '/debug.log' ) ); ?></pre>
        <?php endif; ?>
    </div>
</div>
<div id="nbd-fix-pdf-font" class="nbd-tool-section" style="display: none;">
    <h2>
        <?php  esc_html_e('Fix pdf font', 'web-to-print-online-designer'); ?>
        <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-tool-img-loading" id="nbdesigner_fix_font_loading" />
    </h2>
    <div id="nbdesigner_custom_css_con">
        <?php wp_nonce_field('nbdesigner-fix-font', '_nbdesigner_fix_font'); ?>
        <input id="pdf_font" />
    </div>
    <?php
        $custom_fonts = array();
        if(file_exists( NBDESIGNER_DATA_DIR . '/fonts.json') ){
            $custom_fonts = (array)json_decode( file_get_contents( NBDESIGNER_DATA_DIR . '/fonts.json' ) );
        } 
        $google_fonts = array();
        if(file_exists( NBDESIGNER_DATA_DIR . '/googlefonts.json') ){
            $google_fonts = (array)json_decode( file_get_contents( NBDESIGNER_DATA_DIR . '/googlefonts.json' ) );
        }
        $fonts = array_merge($google_fonts, $custom_fonts);
    ?>
    <script language="javascript">
        jQuery( document ).ready( function($) {
            var fonts = <?php echo json_encode( $fonts ); ?>,
            source = fonts.map(function( font ){
                return {
                    label: font.name,
                    value: font.alias,
                    type: font.type
                };
            });
            $( "#pdf_font" ).autocomplete({
                source: source,
                select: function( event, ui ) {
                    event.preventDefault();
                    $("#pdf_font").val(ui.item.label);
                    fix_pdf_font(ui);
                },
                focus: function(event, ui) {
                    event.preventDefault();
                    $("#pdf_font").val(ui.item.label);
                    //fix_pdf_font(ui);
                }
            });
            function fix_pdf_font( ui ){
                var formdata = new FormData();
                formdata.append('alias', ui.item.value);
                formdata.append('type', ui.item.type.toLowerCase());
                formdata.append('action', 'nbd_fix_pdf_font');
                jQuery('#nbdesigner_fix_font_loading').removeClass('nbdesigner_loaded');
                jQuery('#nbd-fix-pdf-font').addClass('nbd-loading');
                $.ajax({
                    url: admin_nbds.url,
                    data: formdata,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function(data){
                        jQuery('#nbdesigner_fix_font_loading').addClass('nbdesigner_loaded');
                        jQuery('#nbd-fix-pdf-font').removeClass('nbd-loading');
                        if(parseInt(data.flag) == 1){
                            alert('Success!');
                        }else {
                            alert('Oops! Try again!');
                        }
                    }
                });
            };
        });
    </script>
</div>
<div id="nbd-import-export-demo" class="nbd-tool-section">
    <style>
        .nbp-loading-wrap {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            background: rgba(255,255,255,0.85);
            display: table;
            opacity: 0;
            visibility: hidden;
            z-index: -1;
        }
        .nbp-loading-spinner {
            width: 300px;
            height: 50px;
            position: absolute;
            top: 0;
            bottom: 0;
            right: 0;
            left: 0;
            margin: auto;
            text-align: center;
        }
        .nbp-loading-ball {
            width: 20px;
            height: 20px;
            background-color: #444;
            border-radius: 50%;
            display: inline-block;
            -webkit-animation: nbpLoading 3s cubic-bezier(0.77,0,0.175,1) infinite;
            animation: nbpLoading 3s cubic-bezier(0.77,0,0.175,1) infinite;
        }
        .nbp-loading-wrap.nbp-show {
            opacity: 1 !important;
            z-index: 9999;
            visibility: visible;
        }
        .nbix-pseudo-dropdown {
            position: relative;
            widh: 300px;
        }
        .nbix-pseudo-list {
            position: absolute;
            top: 36px;
            width: 300px;
            -webkit-box-shadow: 0 3px 10px 0 rgb(75 79 84 / 30%);
            -moz-box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
            -ms-box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
            box-shadow: 0 3px 10px 0 rgb(75 79 84 / 30%);
            background: #fff;
            z-index: 99;
            cursor: pointer;
            display: none;
            opacity: 0;
        }
        .nbix-pseudo-list.active {
            display: block;
            opacity: 1;
        }
        .nbix-pseudo-list-item {
            display: flex;
            padding: 5px 10px;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #ddd;
            cursor: pointer;
        }
        .nbix-pseudo-list-item img{
            width: 50px;
            height: 50px;
        }
        .nbix-pseudo-result-name {
            flex-basis: calc(100% - 30px);
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            line-height: 30px;
        }
        .nbix-pseudo-result {
            height: 36px;
            padding: 3px 8px;
            border: 1px solid #eee;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            box-sizing: border-box;
            width: 300px;
        }
        @keyframes nbpLoading{
            0% {
                -webkit-transform: translateX(0) scale(1);
                -webkit-transform: translateX(0) scale(1);
                -ms-transform: translateX(0) scale(1);
                transform: translateX(0) scale(1);
            }
            25% {
                -webkit-transform: translateX(-50px) scale(0.3);
                -webkit-transform: translateX(-50px) scale(0.3);
                -ms-transform: translateX(-50px) scale(0.3);
                transform: translateX(-50px) scale(0.3);
            }
            50% {
                -webkit-transform: translateX(0) scale(1);
                -webkit-transform: translateX(0) scale(1);
                -ms-transform: translateX(0) scale(1);
                transform: translateX(0) scale(1);
            }
            75% {
                -webkit-transform: translateX(50px) scale(0.3);
                -webkit-transform: translateX(50px) scale(0.3);
                -ms-transform: translateX(50px) scale(0.3);
                transform: translateX(50px) scale(0.3);
            }
            100% {
                -webkit-transform: translateX(0) scale(1);
                -webkit-transform: translateX(0) scale(1);
                -ms-transform: translateX(0) scale(1);
                transform: translateX(0) scale(1);
            }
        }
        @-webkit-keyframes nbpLoading{
            0% {
                -webkit-transform: translateX(0) scale(1);
                -webkit-transform: translateX(0) scale(1);
                -ms-transform: translateX(0) scale(1);
                transform: translateX(0) scale(1);
            }
            25% {
                -webkit-transform: translateX(-50px) scale(0.3);
                -webkit-transform: translateX(-50px) scale(0.3);
                -ms-transform: translateX(-50px) scale(0.3);
                transform: translateX(-50px) scale(0.3);
            }
            50% {
                -webkit-transform: translateX(0) scale(1);
                -webkit-transform: translateX(0) scale(1);
                -ms-transform: translateX(0) scale(1);
                transform: translateX(0) scale(1);
            }
            75% {
                -webkit-transform: translateX(50px) scale(0.3);
                -webkit-transform: translateX(50px) scale(0.3);
                -ms-transform: translateX(50px) scale(0.3);
                transform: translateX(50px) scale(0.3);
            }
            100% {
                -webkit-transform: translateX(0) scale(1);
                -webkit-transform: translateX(0) scale(1);
                -ms-transform: translateX(0) scale(1);
                transform: translateX(0) scale(1);
            }
        }
    </style>
    <div id="nbd-export-demo" style="display: none;">
        <h2><?php esc_html_e('Export product data', 'web-to-print-online-designer'); ?></h2>
        <div>
            <select class="nbie-export-product">
                <?php
                    $products = nbd_get_all_product_has_design();
                    foreach ( $products as $product ):
                ?>
                <option value="<?php echo $product->ID; ?>"><?php echo $product->post_title; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="margin-top: 10px">
            <button class="button nbix-export-btn" ><?php _e("Export data", 'web-to-print-online-designer'); ?></button>
        </div>
    </div>
    <div id="nbd-import-demo">
        <h2><?php esc_html_e('Import product data', 'web-to-print-online-designer'); ?></h2>
        <div class="nbie-import-product-options">
            <select class="nbie-import-product" style="display: none;">
                <?php
                    $demo_data_path = NBDESIGNER_PLUGIN_DIR . 'data/demo_datas.json';
                    $products       = json_decode( file_get_contents( $demo_data_path ), true );
                    foreach ( $products as $key => $product ):
                ?>
                <option value="<?php echo substr( $key, 1 ); ?>"><?php echo $product['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <div class="nbix-pseudo-dropdown">
                <div class="nbix-pseudo-result">
                    <span class="nbix-pseudo-result-name"><?php _e("-- Select product --", 'web-to-print-online-designer'); ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
                        <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
                    </svg>
                </div>
                <div class="nbix-pseudo-list">
                    <?php foreach ( $products as $key => $product ): ?>
                    <div class="nbix-pseudo-list-item" data-id="<?php echo substr( $key, 1 ); ?>" data-name="<?php echo $product['name']; ?>" >
                        <span class="nbix-pseudo-list-name"><?php echo $product['name']; ?></span>
                        <img src="<?php echo $product['image']; ?>" />
                    </div>
                    <?php endforeach; ?>
                </div>
            </di>
        </div>
        <div style="margin-top: 10px">
            <button class="button nbix-import-btn" ><?php _e("Import data", 'web-to-print-online-designer'); ?></button>
        </div>
    </div>
    <div class="nbp-loading-wrap">
        <div class="nbp-loading-spinner">
            <div class="nbp-loading-ball"></div>
            <p id="nbp-processing" style="display: none;font-weight: bold;white-space: nowrap;"><?php _e("Processing ", 'web-to-print-online-designer'); ?><span id="nbp-process-loaded"></span> / <span id="nbp-process-total"></span></p>
            <p><?php _e("Please do not close or deactive this tab!", 'web-to-print-online-designer'); ?></p>
        </div>
    </div>
    <script language="javascript">
        jQuery( document ).ready( function($) {
            function export_product_data(){
                var product_id = $('.nbie-export-product').val();
                var formdata = new FormData();
                formdata.append('product_id', product_id);
                formdata.append('action', 'nbd_export_product');
                $.ajax({
                    url: admin_nbds.url,
                    data: formdata,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function(data){
                        if(parseInt(data.flag) == 1){
                            alert('Success!');
                        }else {
                            alert('Oops! Try again!');
                        }
                    }
                });
            }

            var currentImportStep = 1, totalStep;

            function import_product_data(){
                var product_id = $('.nbie-import-product').val();
                if( !product_id ) return;
                var formdata = new FormData();
                formdata.append('product_id', product_id);
                formdata.append('step', currentImportStep);
                formdata.append('action', 'nbd_import_product');
                jQuery('.nbp-loading-wrap').addClass('nbp-show');
                $.ajax({
                    url: admin_nbds.url,
                    data: formdata,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    success: function(data){
                        if( parseInt( data.flag ) == 1 ){
                            totalStep = data.total_steps;
                            jQuery('#nbp-processing').show();
                            $('#nbp-process-loaded').html(currentImportStep);
                            $('#nbp-process-total').html(totalStep);
                            if( data.total_steps > data.current_step ){
                                currentImportStep = data.current_step * 1 + 1;
                                import_product_data();
                            }else{
                                jQuery('.nbp-loading-wrap').removeClass('nbp-show');
                                jQuery('#nbp-processing').hide();
                            }
                        }else {
                            alert('Oops! Try again!');
                        }
                    }
                });
            }

            jQuery('.nbix-export-btn').on('click', function(){
                export_product_data();
            });

            jQuery('.nbix-import-btn').on('click', function(){
                import_product_data();
            });

            jQuery('.nbix-pseudo-result').on('click', function(){
                jQuery(this).parents('.nbix-pseudo-dropdown').find('.nbix-pseudo-list').toggleClass('active');
            });

            jQuery('.nbix-pseudo-list-item, .nbix-pseudo-list-item svg, .nbix-pseudo-result-name').on('click', function(){
                var id = jQuery(this).hasClass('nbix-pseudo-list-item') ? jQuery(this).attr('data-id') : jQuery(this).parent('.nbix-pseudo-list-item').attr('data-id'),
                name = jQuery(this).hasClass('nbix-pseudo-list-item') ? jQuery(this).attr('data-name') : jQuery(this).parent('.nbix-pseudo-list-item').attr('data-name');
                jQuery(this).parents('.nbie-import-product-options').find('.nbie-import-product').val( id );
                jQuery(this).parents('.nbix-pseudo-list').removeClass('active');
                jQuery(this).parents('.nbie-import-product-options').find('.nbix-pseudo-result-name').html( name );
            });

            jQuery(document).on('click', function(event){
                var wrapEl = jQuery('.nbie-import-product-options');
                if( wrapEl.has(event.target).length == 0 && !wrapEl.is(event.target) ){
                    jQuery('.nbix-pseudo-list').removeClass('active');
                }
            });
        });
    </script>
</div>
<?php do_action( 'nbd_after_admin_tools' );