<?php if (!defined('ABSPATH')) exit;
$cart_item_key      = ( isset( $_GET['cik'] ) && $_GET['cik'] != '' ) ? wc_clean( $_GET['cik'] ) : '';
$order_id           = ( isset( $_GET['oid'] ) && $_GET['oid'] != '' ) ? absint( $_GET['oid'] ) : '';
$product_id         = ( isset( $_GET['product_id'] ) && $_GET['product_id'] != '' ) ? absint( $_GET['product_id'] ) : 0;
$variation_id       = ( isset( $_GET['variation_id'] ) && $_GET['variation_id'] != '' ) ? absint( $_GET['variation_id'] ) : 0;
$nonce              = wp_create_nonce('save-design');
$nbu_item_key       = '';
$order_item_id      = '';
$upload_files       = array();
$task               = ( isset( $_GET['task'] ) && $_GET['task'] != '' ) ? wc_clean( $_GET['task'] ) : 'new';
$redirect_url       = '';
$error_redirec      = false;
$design_type        = false;
$upload_datas       = array();
$nbu_ui_mode        = 2;
$show_nbo_option    = false;
$frame_image_url    = '';
if( $cart_item_key != '' || $order_id != '' ){
    $redirect_url   = nbd_get_redirect_url();
}
if( $cart_item_key != '' ){
    $cart_item      = WC()->cart->get_cart_item( $cart_item_key );
    if( isset( $cart_item['product_id'] ) ){
        $product_id     = $cart_item['product_id'];
        $variation_id   = $cart_item['variation_id'];
        if( $task == 'reup' ){
            if( isset( $cart_item['nbau'] ) && isset( $cart_item["nbd_item_meta_ds"] ) && isset( $cart_item["nbd_item_meta_ds"]["nbu"] ) ){
                $nbu_item_key   = $cart_item["nbd_item_meta_ds"]["nbu"];
                $upload_datas   = (array)json_decode( stripslashes( $cart_item['nbau'] ) );
            }
        }
        if( $task == 'upload' ){
            $nbu_item_key   = substr( md5( uniqid() ), 0, 5 ) . rand( 1, 100 ) . time();
        }
        if( isset ( $cart_item['nbo_meta'] ) ){
            $nbd_field          = $cart_item['nbo_meta']['field'];
            $options            = $cart_item['nbo_meta']['options'];
            if( nbd_is_base64_string( $options['fields'] ) ){
                $options['fields'] = base64_decode( $options['fields'] );
            }
            $option_fields      = unserialize( $options['fields'] );
            foreach( $nbd_field as $k => $f ){
                $op_field = array();
                foreach( $option_fields['fields'] as $key => $field ){
                    if( $field['id'] == $k ){
                        $op_field = $field;
                    }
                }
                if( isset( $op_field['nbe_type'] ) && $op_field['nbe_type'] == 'frame' ){
                    $fr_obj             = wp_get_attachment_url( absint( $op_field['general']['attributes']['options'][$f]['frame_image'] ) );
                    $frame_image_url    = $fr_obj ? $fr_obj : NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
                }
            }
        }
    } else {
        $error_redirec = true;
    }
} else if( $order_id != '' ){
    $order = wc_get_order( $order_id );
    $order_items = $order->get_items();
    $order_item_id = $_GET['item_id'];
    if( isset( $order_items[ $order_item_id ] ) ){
        $item  = $order_items[ $order_item_id ];
        if( isset( $item["item_meta"]["_nbu"] ) ){
            $nbu_item_key   = $item["item_meta"]["_nbu"];
            $design_type    = 'edit_order';
            $product_id     = $item["product_id"];
            $variation_id   = $item["variation_id"];
            if( isset( $item["item_meta"]["_nbau"] ) ){
                $upload_datas   = (array)json_decode( stripslashes( $item["item_meta"]["_nbau"] ) );
            }
        } else {
            $error_redirec = false;
        }
    } else {
        $error_redirec = false;
    }
} else if( $task == 'new' ){
    global $product, $in_nbau_mode_2;
    $option_id      = false;
    $in_nbau_mode_2 = true;
    $product        = wc_get_product( $product_id );
    $product_type   = '';
    if( is_object( $product ) ){
        $product_type   = $product->get_type();  
        $enable = get_post_meta($product_id, '_nbo_enable', true);
        if( $enable ){
            $option_id = get_transient( 'nbo_product_'.$product_id );
        }
        $show_nbo_option = ( $option_id || $product_type == 'variable' ) ? true : false;
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
    } else {
        $error_redirec = true;
    }
}
$nbau_task = $task;
if( !$error_redirec ){ ?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://fonts.googleapis.com/css?family=Poppins:400,400i,700,700i' rel='stylesheet' type='text/css'>
        <script type='text/javascript' src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script type='text/javascript' src="//ajax.googleapis.com/ajax/libs/angularjs/1.6.9/angular.min.js"></script>
        <title><?php echo get_bloginfo( 'name' ); ?> - <?php _e('Upload photos', 'web-to-print-online-designer'); ?></title>
        <style type="text/css" >
            body, html {
                font-family: 'Poppins', sans-serif;
                overflow: hidden !important;
                width: 100%;
                height: 100%;
                margin: 0;
                padding: 0;
            }
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
        </style>
    </head>
    <body>
            <?php
                $pid            = $product_id;
                $option         = unserialize( get_post_meta( $product_id, '_nbdesigner_upload', true ) );
                $design_option  = unserialize( get_post_meta( $product_id, '_nbdesigner_option', true ) );
                if( count( $upload_datas ) > 0 ){
                    $upload_path    = NBDESIGNER_UPLOAD_DIR . '/' . $nbu_item_key;
                    foreach( $upload_datas as $key => $data ){
                        $data                       = (array)$data;
                        $file                       = $upload_path . '/' . $data['name'];
                        $preview_file               = $upload_path . '_preview/' . $data['name'];
                        $final_file                 = $upload_path . '_final/' . $data['name'];
                        $preview_final_file         = $upload_path . '_preview_final/' . $data['name'];
                        list( $width, $height )     = getimagesize( $file );
                        list( $p_width, $p_height ) = getimagesize( $preview_file );
                        $cropped_preview_url        = Nbdesigner_IO::wp_convert_path_to_url( $preview_final_file );
                        $upload_files[] = array(
                            'name'                  => $data['name'],
                            'origin'                => Nbdesigner_IO::wp_convert_path_to_url( $file ),
                            'preview'               => $cropped_preview_url,
                            'src'                   => Nbdesigner_IO::wp_convert_path_to_url( $preview_file ),
                            'width'                 => $width,
                            'height'                => $height,
                            'cropWidth'             => $data['width'],
                            'cropHeight'            => $data['height'],
                            'cropLeft'              => $data['startX'],
                            'cropTop'               => $data['startY'],
                            'cropped_url'           => Nbdesigner_IO::wp_convert_path_to_url( $final_file ),
                            'cropped_preview_url'   => $cropped_preview_url,
                            'zoom'                  => $data['zoom'],
                            'previewOriginWidth'    => $p_width,
                            'previewOriginHeight'   => $p_height,
                            'previewRatio'          => $width / $p_width,
                            'productWidth'          => $data['productWidth'],
                            'productHeight'         => $data['productHeight']
                        );
                    }
                }
                include( NBDESIGNER_PLUGIN_DIR . 'templates/single-product/advanced-upload.php' ); 
            ?>
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
                var nbauObject = {
                    product_id: "<?php echo $product_id; ?>",
                    variation_id: "<?php echo $variation_id; ?>",
                    cart_item_key: "<?php echo $cart_item_key; ?>",
                    order_id: "<?php echo $order_id; ?>",
                    order_item_id: "<?php echo $order_item_id; ?>",
                    nbu_item_key: "<?php echo $nbu_item_key; ?>",
                    redirect_url: "<?php echo $redirect_url; ?>",
                    nonce: "<?php echo $nonce; ?>",
                    task: "<?php echo $task; ?>",
                    design_type: "<?php echo $design_type; ?>",
                    frame_image_url: "<?php echo $frame_image_url; ?>",
                    ajax_url: "<?php echo admin_url('admin-ajax.php'); ?>",
                    upload_datas: JSON.parse('<?php echo json_encode( $upload_files ); ?>')
                };
                <?php if( $show_nbo_option ): ?>
                    var wc_add_to_cart_variation_params = <?php echo json_encode( $wc_add_to_cart_params ); ?>;
                    var nbds_frontend = <?php echo json_encode( $nbds_frontend ); ?>;
                    nbauObject.product_type = "<?php echo $product_type; ?>";
                <?php endif; ?>
            </script>
    </body>
</html>
<?php
} else { 
    wp_redirect( get_permalink( wc_get_page_id( 'shop' ) ) );
}