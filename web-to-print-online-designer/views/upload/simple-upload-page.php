<?php if (!defined('ABSPATH')) exit;
    $product_id         = ( isset( $_GET['product_id'] ) && $_GET['product_id'] != '' ) ? absint( $_GET['product_id'] ) : 0;
    $variation_id       = ( isset( $_GET['variation_id'] ) && $_GET['variation_id'] != '' ) ? absint( $_GET['variation_id'] ) : 0;
    $nbu_ui_mode        = 2;
    $error_redirec      = false;
    $option_id          = false;
    if( $product_id == 0 ){
        $error_redirec  = true;
    }else{
        global $product;
        $product        = wc_get_product( $product_id );
        if( is_object( $product ) ){
            $product_type   = $product->get_type();  
            $enable = get_post_meta($product_id, '_nbo_enable', true);
            if( $enable ){
                $option_id = get_transient( 'nbo_product_'.$product_id );
            }
            $show_nbo_option = ( $option_id || $product_type == 'variable' ) ? true : false;
            if( $show_nbo_option ){
                $wc_add_to_cart_params = array(
                    'wc_ajax_url'                      => WC_AJAX::get_endpoint( '%%endpoint%%' ),
                    'i18n_no_matching_variations_text' => esc_attr__( 'Sorry, no products matched your selection. Please choose a different combination.', 'woocommerce' ),
                    'i18n_make_a_selection_text'       => esc_attr__( 'Please select some product options before adding this product to your cart.', 'woocommerce' ),
                    'i18n_unavailable_text'            => esc_attr__( 'Sorry, this product is unavailable. Please choose a different combination.', 'woocommerce' )
                );
                $nbds_frontend = array(
                    'currency_format_num_decimals'                  =>  wc_get_price_decimals(),
                    'currency_format_symbol'                        =>  html_entity_decode( (string) get_woocommerce_currency_symbol(), ENT_QUOTES, 'UTF-8'),
                    'currency_format_decimal_sep'                   =>  stripslashes( wc_get_price_decimal_separator() ),
                    'currency_format_thousand_sep'                  =>  stripslashes( wc_get_price_thousand_separator() ),
                    'currency_format'                               =>  esc_attr( str_replace( array( '%1$s', '%2$s' ), array( '%s', '%v' ), get_woocommerce_price_format()) ),
                    'nbdesigner_hide_add_cart_until_form_filled'    =>  nbdesigner_get_option('nbdesigner_hide_add_cart_until_form_filled')
                );
            }
            $option     = unserialize( get_post_meta( $product_id, '_nbdesigner_upload', true ) );
        }else{
            $error_redirec  = true;
        }
    }
    if( !$error_redirec ){
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Poppins:400,400i,700,700i' rel='stylesheet' type='text/css'>
        <script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script type='text/javascript' src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
        <title><?php echo get_bloginfo( 'name' ); ?> - <?php esc_html_e('Upload photos', 'web-to-print-online-designer'); ?></title>
        <style type="text/css" >
            body, html {
                font-family: 'Poppins', sans-serif;
                overflow: hidden !important;
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
            }
            .nbd-m-upload-design-wrap {
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                width: 100vw;
                height: 100vh;
            }
            .nbu-inputfile {
                width: 0.1px;
                height: 0.1px;
                opacity: 0;
                overflow: hidden;
                position: absolute;
                z-index: -1;
            }    
            .nbu-inputfile + label {
                width: 320px;
                flex-direction: column;
                display: flex;
                text-align: center;
                justify-content: center;
                align-items: center;
                border: 2px dashed #ddd;
                border-radius: 4px;
                color: #394264;
                cursor: pointer;
                padding: 10px;
                margin: 0 auto;
            } 
            .nbu-inputfile + label.highlight {
                border-color: #394264;
            }
            .nbu-inputfile + label svg {
                width: 2em;
                height: 2em;
                vertical-align: middle;
                fill: currentColor;
                margin-top: -0.25em;
                margin-right: 0.25em;
            }
            .nbu-upload-zone span {
                display: block;
                line-height: 12px;
            }
            .nbd-upload-items {
                width: 150px;
                height: 150px;
                display: inline-block;
                margin: 15px;
            }
            .nbd-upload-items-inner {
                display: flex;
                align-items: flex-end;
                justify-content: center;
                width: 100%;
                height: 100%;
                text-align: center;
                position: relative;
                overflow: hidden;
            } 
            .nbd-upload-item {
                max-width: 100%;
                max-height: 100%;
            }
            .nbd-upload-item-title {
                position: absolute;
                border: 0;
                background: #fff;
                width: 100%;
                height: 30px;
                line-height: 30px;
                text-overflow: ellipsis;
                overflow: hidden;
                padding: 0 5px;
                white-space: nowrap;
                font-weight: bold;
                background: rgba(255, 255, 255, 0.75);
                border-bottom-right-radius: 3px;
                border-bottom-left-radius: 3px;  
                margin: 0;
            }
            .nbd-upload-items-inner span {
                position: absolute;
                z-index: 2;
                width: 30px;
                height: 30px;
                cursor: pointer;
                background: #fff;
                line-height: 30px;
                -webkit-transform: translateY(30px);
                -moz-transform: translateY(30px);
                transform: translateY(30px);
                -webkit-transition: all 0.4s;
                -moz-transition: all 0.4s;
                transition: all 0.4s;
                border-radius: 50%;
                font-size: 20px;
                color: #cc324b;
            }
            .nbd-upload-items-inner:hover span {
                -webkit-transform: translateY(-10px);
                -moz-transform: translateY(-10px);
                transform: translateY(-10px);
            }
            .upload-design-preview {
                margin: 15px;
                max-height: 300px;
                max-width: 720px;
                position: relative;
                overflow: hidden;
            }    
            .submit-upload-design:hover {
                box-shadow: 0 11px 15px -7px rgba(0,0,0,.2), 0 24px 38px 3px rgba(0,0,0,.14), 0 9px 46px 8px rgba(0,0,0,.12);
            }
            .submit-upload-design {
                height: 40px;
                border-radius: 20px;
                background: #fff;
                padding: 0 15px;
                color: #394264;
                text-transform: uppercase;
                font-weight: bold;
                line-height: 40px;
                cursor: pointer;
                display: inline-block;
                margin-top: 15px;
                box-shadow: 0 5px 6px -3px rgba(0,0,0,.2), 0 9px 12px 1px rgba(0,0,0,.14), 0 3px 16px 2px rgba(0,0,0,.12);
            }    
            .nbu-upload-zone {
                position: relative;
            }
            .nbu-upload-zone .nbd-upload-loading {
                position: absolute;
                top: 50%;
                left: 50%;
                -webkit-transform: translate(-50%, -50%);
                -moz-transform: translate(-50%, -50%);
                transform: translate(-50%, -50%);
                z-index: -1;
                visibility: hidden;
                opacity: 0;
            }
            .nbu-upload-zone .nbd-upload-loading.is-visible {
                visibility: visible;
                z-index: 2;
                opacity: 1;
            }
            .nbu-inputfile + label.is-loading {
                opacity: 0.75;
            }
            /* NBO */
            #nbu-upload-nbo-options {
                background-color: #fff !important;
            }
            form.cart {
                overflow: scroll;
                height: 100%;
                text-align: left;
            }
            select {
                font-family: 'Poppins', sans-serif;
            }
            .nbd-option-wrapper label {
                box-sizing: border-box;
            }
            .nbu-options-nbo-wrapper {
                height: calc(100% - 75px);
                margin-top: 15px;
                font-size: 14px;
            }
            .nbd-option-wrapper {
                padding: 15px;
            }
            .nbu-options-nbo-wrapper table {
                border-collapse: collapse;
                border-spacing: 0;
            }
            .nbu-options-nbo-wrapper th, .nbu-options-nbo-wrapper td {
                padding: 1em;
                text-align: left;
                vertical-align: top;
            }
            .nbu-options-nbo-wrapper table tbody tr:nth-child(2n) td {
                background-color: #fbfbfb;
            }
            .screen-reader-text {
                display: none;
            }
            .button {
                background-color: #eeeeee;
                color: #333333;
                cursor: pointer;
                padding: .6180469716em 1.41575em;
                text-decoration: none;
                font-weight: 600;
                text-shadow: none;
                display: inline-block;
                -webkit-appearance: none;
                border-radius: 0;
                -webkit-transition: all 0.4s;
                -moz-transition: all 0.4s;
                transition: all 0.4s;
            }
            .single_add_to_cart_button{
                background-color: #0c8ea7;
                color: #ffffff;
                height: 40px;
                border: none;
            }
            .button:hover {
                -webkit-box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
                -moz-box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
                -ms-box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
                box-shadow: 0 3px 10px 0 rgba(75,79,84,.3);
            }
            .quantity {
                float: left;
                margin-right: .875em;
                margin-left: 15px;
            }
            .quantity input {
                height: 40px;
                margin-bottom: 20px;
                box-sizing: border-box;
                padding: 0 10px;
                width: 100px;
                border: 1px solid #EEE;
            }
            #nbu-upload-nbo-options {
                position: absolute;
                left: 0;
                right: 0;
                top: 15px;
                height: auto;
                max-height: 100%;
                width: 750px;
                height: 600px;
                background: #fff;
                -webkit-transition: opacity 225ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
                -moz-transition: opacity 225ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
                transition: transform 225ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
                -webkit-transform: translate(0px, 50px);
                -moz-transform: translate(0px, 50px);
                transform: translate(0px, 50px);
                opacity: 0;
                z-index: -1;
                box-shadow: 0px 11px 15px -7px rgba(0,0,0,0.2), 0px 24px 38px 3px rgba(0,0,0,0.14), 0px 9px 46px 8px rgba(0,0,0,0.12);
                border-radius: 10px;
                margin-right: auto;
                margin-left: auto;
                background-color: #f5f5f5;
            }
            #nbu-upload-nbo-options.active{
                -webkit-transform: translate(0px, 0px);
                -webkit-transform: translate(0px, 0px);
                transform: translate(0px, 0px);
                opacity: 1;
                z-index: 1;
            }
            .nbu-options-header {
                box-shadow: 0 0 10px rgba(0,0,0,.3);
                background-color: #fff;
                height: 50px;
                display: flex;
                justify-content: space-between;
                align-content: center;
                text-align: center;
                align-items: center;
                border-top-left-radius: 10px;
                border-top-right-radius: 10px;
                flex-direction: row;
                padding: 0 15px;
            }
            .nbo-options-overlay {
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                position: fixed;
                display: block;
                z-index: -1;
                opacity: 0;
            }
            .nbo-options-overlay.active {
                z-index: 1;
                opacity: 1;
            }
            .reset_variations {
                background-color: #2c2d33;
                color: #ffffff;
                height: 40px;
                border: none;
                display: inline-block;
                vertical-align: top;
                line-height: 40px;
                padding: 0 15px;
                text-decoration: none;
            }
            .variations select {
                height: 40px;
            }
            @media (max-width: 768px){
                #nbu-upload-nbo-options {
                    width: calc(100% - 30px);
                    height: calc(100% - 60px);
                    top: 45px;
                }
            }
        </style>
    </head>
    <body>
        <div class="nbd-m-upload-design-wrap">
            <?php include NBDESIGNER_PLUGIN_DIR.'templates/single-product/simple-upload.php'; ?>
        </div>
        <div class="nbo-options-overlay"></div>
        <div id="nbu-upload-nbo-options">
            <div class="nbu-options-header">
                <span><b><?php esc_html_e('Choose options', 'web-to-print-online-designer'); ?></b></span>
            </div>
            <div class="nbu-options-nbo-wrapper">
                <?php woocommerce_template_single_add_to_cart(); ?>
            </div>
        </div>
        <?php if( $show_nbo_option ): ?>
            <?php wc_get_template( 'single-product/add-to-cart/variation.php' ); ?>
            <script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.10/lodash.js"></script>
            <script type='text/javascript' src="<?php echo WC()->plugin_url().'/assets/js/accounting/accounting.min.js'; ?>"></script>
            <script type='text/javascript' src="<?php echo WC()->plugin_url().'/assets/js/frontend/add-to-cart.min.js'; ?>"></script>
            <?php if( $product_type == 'variable' ): ?>
                <script type='text/javascript'>
                    window.wp = window.wp || {};
                    wp.template = _.memoize(function ( id ) {
                        var compiled,
                        options = {
                            evaluate:    /<#([\s\S]+?)#>/g,
                            interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
                            escape:      /\{\{([^\}]+?)\}\}(?!\})/g,
                            variable:    'data'
                        };
                        return function ( data ) {
                            compiled = compiled || _.template( jQuery( '#tmpl-' + id ).html(),  options );
                            return compiled( data );
                        };
                    });
                </script>
                <script type='text/javascript' src="<?php echo WC()->plugin_url().'/assets/js/jquery-blockui/jquery.blockUI.min.js'; ?>"></script>
                <script type='text/javascript' src="<?php echo WC()->plugin_url().'/assets/js/frontend/add-to-cart-variation.min.js'; ?>"></script>
            <?php endif; ?>
        <?php endif; ?>
        <script type="text/javascript">
            <?php if( $show_nbo_option ): ?>
                var wc_add_to_cart_variation_params = <?php echo json_encode( $wc_add_to_cart_params ); ?>;
                var nbds_frontend = <?php echo json_encode( $nbds_frontend ); ?>;
                product_type = "<?php echo( $product_type ); ?>";
            <?php endif; ?>
            var nbd_allow_type  = "<?php echo( $option['allow_type'] ); ?>",
            product_id          = "<?php echo( $product_id ); ?>",
            nbd_disallow_type   = "<?php echo( $option['disallow_type'] ); ?>",
            nbd_number          = parseInt(<?php echo( $option['number'] ); ?>),
            nbd_minsize         = parseInt(<?php echo( $option['minsize'] ); ?>),
            nbd_maxsize         = parseInt(<?php echo( $option['maxsize'] ); ?>),
            nonce               = "<?php echo wp_create_nonce('save-design'); ?>",
            ajax_url            = "<?php echo admin_url('admin-ajax.php'); ?>";
            
            jQuery(document).ready(function(){
                /* Drag & Drop uplod file */
                var nbdDropArea = jQuery('label[for="nbd-file-upload"]'),
                nbdInput = jQuery('#nbd-file-upload');
                var listFileUpload = [];
                ['dragenter', 'dragover'].forEach(function(eventName){
                    nbdDropArea.on(eventName, highlight)
                });
                ['dragleave', 'drop'].forEach(function(eventName){
                    nbdDropArea.on(eventName, unhighlight)
                });
                function highlight(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    nbdDropArea.addClass('highlight');
                };
                function unhighlight(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    nbdDropArea.removeClass('highlight');
                };
                nbdDropArea.on('drop', handleDrop);
                function handleDrop(e) {
                    if( jQuery('#accept-term').length && !jQuery('#accept-term').is(':checked') ) {
                        alert(NBDESIGNCONFIG.nbdlangs.alert_upload_term);
                        return;
                    }else{
                        if(e.originalEvent.dataTransfer){
                            if(e.originalEvent.dataTransfer.files.length) {
                                e.preventDefault();
                                e.stopPropagation();
                                handleFiles(e.originalEvent.dataTransfer.files);
                            }
                        }
                    }
                };
                nbdInput.on('click', function(e){
                    e.stopPropagation();
                });
                nbdInput.on('change', function(){
                    handleFiles(this.files);
                });
                function resetUploadInput(){
                    nbdInput.val('');
                }
                function handleFiles(files) {
                    if(files.length > 0) uploadFile(files);
                }
                function uploadFile(files){
                    var file = files[0],
                    type = file.type.toLowerCase();
                    if( listFileUpload.length > (nbd_number-1) ) {
                        alert('Exceed number of upload files!');
                        return;
                    }
                    if( type == '' ){
                        type = file.name.substring(file.name.lastIndexOf('.')+1).toLowerCase();
                    }
                    type = type == 'image/jpeg' ? 'image/jpg' : type;
                    if( nbd_disallow_type != '' ){
                        var nbd_disallow_type_arr = nbd_disallow_type.toLowerCase().split(',');
                        var check = false;
                        nbd_disallow_type_arr.forEach(function(value){
                            value = value == 'jpeg' ? 'jpg' : value;
                            if( type.indexOf(value) > -1 ){
                                check = true;
                            }
                        });
                        if( check ){
                            resetUploadInput();
                            alert('Disallow extensions: ' + nbd_disallow_type);
                            return;
                        }
                    }
                    if( nbd_allow_type != '' ){
                        var nbd_allow_type_arr = nbd_allow_type.toLowerCase().split(',');
                        var check = false;
                        nbd_allow_type_arr.forEach(function(value){
                            value = value == 'jpeg' ? 'jpg' : value;
                            if( type.indexOf(value) > -1 ){
                                check = true;
                            }
                        });
                        if( !check ){
                            resetUploadInput();
                            alert('Only support: ' + nbd_allow_type);
                            return;
                        }
                    }
                    if (file.size > nbd_maxsize * 1024 * 1024 ) {
                        alert('Max file size' + nbd_maxsize + " MB");
                        resetUploadInput();
                        return;
                    }else if(file.size < nbd_minsize * 1024 * 1024){
                        alert('Min file size' + nbd_minsize + " MB");
                        resetUploadInput();
                        return;
                    };
                    var formData = new FormData;
                    formData.append('file', file);
                    jQuery('.nbd-upload-loading').addClass('is-visible');
                    jQuery('.nbu-upload-zone label').addClass('is-loading');
                    jQuery('.nbd-m-upload-design-wrap').addClass('is-loading');
                    var first_time = listFileUpload.length > 0 ? 2 : 1;
                    var variation_id = 0;
                    formData.append('first_time', first_time);
                    formData.append('action', 'nbd_upload_design_file');
                    formData.append('task', 'new');
                    formData.append('product_id', product_id);
                    formData.append('variation_id', variation_id);
                    formData.append('nonce', nonce);
                    jQuery.ajax({
                        url: ajax_url,
                        method: "POST",
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        complete: function() {
                            jQuery('.nbd-upload-loading').removeClass('is-visible');
                            jQuery('.nbu-upload-zone label').removeClass('is-loading');
                            jQuery('.nbd-m-upload-design-wrap').removeClass('is-loading');
                        },
                        success: function(data) {
                            if( data.flag == 1 ){
                                listFileUpload.push( { src : data.src, name : data.name } );
                                buildPreviewUpload();
                            }else{
                                alert(data.mes);
                            }
                            resetUploadInput();
                        }
                    });
                }
                window.removeUploadFile = function(index){
                    listFileUpload.splice(index, 1);
                    resetUploadInput();
                    buildPreviewUpload();
                };
                function buildPreviewUpload(){
                    update_nbu_value(listFileUpload); 
                    var html = '';
                    listFileUpload.forEach(function(file, index){
                        html += '<div class="nbd-upload-items"><div class="nbd-upload-items-inner"><img src="'+file.src+'" class="shadow nbd-upload-item"/><p class="nbd-upload-item-title">'+file.name+'</p><span class="shadow" onclick="removeUploadFile('+index+')" >&times;</span></div></div>';
                    });
                    jQuery('.upload-design-preview').html(html);
                }
                function update_nbu_value( arr ){
                    var files = '';
                    jQuery.each(arr, function (key, val) {
                        files += key == 0 ? val.name : '|' + val.name;
                    });
                    if( jQuery('form.cart, form.variations_form').find('input[name="nbd-upload-files"]').length == 0 ){
                        jQuery('form.cart, form.variations_form').append('<input name="nbd-upload-files" type="hidden" value="" />');
                    }
                    jQuery('input[name="nbd-upload-files"]').val( files );
                }
                /* submit upload files */
                window.hideUploadFrame = function(){
                    jQuery('form.cart, form.variations_form').append('<input name="submit_form_mode2" type="hidden" value="1" />');
                    jQuery('form.cart').append('<input name="add-to-cart" type="hidden" value="' + product_id + '" />');
                    if( typeof product_type != 'undefined' ){
                        showOptions();
                    }else{
                        jQuery('.variations_form, form.cart').submit();
                    }
                };
                function showOptions(){
                    jQuery('.nbo-options-overlay').addClass('active');
                    jQuery('#nbu-upload-nbo-options').addClass('active');
                }
                jQuery('.nbo-options-overlay').on('click', function(){
                    jQuery('#nbu-upload-nbo-options').removeClass('active');
                    jQuery('.nbo-options-overlay').removeClass('active');
                });
            });
        </script>
    </body>
</html>
<?php  
} else {
    wp_redirect( get_permalink( wc_get_page_id( 'shop' ) ) );
}