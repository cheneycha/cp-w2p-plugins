<?php if (!defined('ABSPATH')) exit;
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $logo = get_attached_file( $custom_logo_id  );
    if( !$logo ){
        $logo_option = nbdesigner_get_option('nbdesigner_editor_logo');
        $logo = get_attached_file( $logo_option  );
    }
    $order_id   = $order->get_id();
    $order = wc_get_order($order_id);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <!-- No inline scripts or styles unless dynamic. -->
        <style type="text/css">
            @font-face {
                font-family: 'Poppins';
                font-style: normal;
                font-weight: normal;
                src: local('Poppins'), url(<?php echo NBDESIGNER_PLUGIN_DIR.'lib/dompdf/lib/fonts'; ?>/Poppins-Regular.ttf) format('truetype');
            }
            @font-face {
                font-family: 'Poppins';
                font-style: normal;
                font-weight: bold;
                src: local('Poppins Bold'), url(<?php echo NBDESIGNER_PLUGIN_DIR.'lib/dompdf/lib/fonts'; ?>/Poppins-Bold.ttf) format('truetype');
            }
            @font-face {
                font-family: 'Poppins';
                font-style: italic;
                font-weight: normal;
                src: local('Poppins Italic'), url(<?php echo NBDESIGNER_PLUGIN_DIR.'lib/dompdf/lib/fonts'; ?>/Poppins-RegularItalic.ttf) format('truetype');
            }
            @font-face {
                font-family: 'Poppins';
                font-style: italic;
                font-weight: bold;
                src: local('Poppins Bold Italic'), url(<?php echo NBDESIGNER_PLUGIN_DIR.'lib/dompdf/lib/fonts'; ?>/Poppins-BoldItalic.ttf) format('truetype');
            }
            @page { margin: 0px; }
            body {
                font-family: 'Poppins', "DejaVu Sans", "DejaVu Sans Mono", "DejaVu", sans-serif, monospace;
                font-size:11px;
                background-color: #f2f2f2;
                color: #6d6d6d;
                margin: 0px;
                padding: 50px;
            }
            body a {
                color: #6d6d6d;
                text-decoration: none;
            }
            #content-wrap {
                background-color: #ffffff; 
                margin: 0 auto; 
                padding: 50px;
            }
            img.tile {
                margin-right: 10px;
            }
        </style>
    </head>
    <body>
        <div id="content-wrap">
            <table table width="100%; margin-bottom: 30px;">
                <tr>
                    <td valign="top" width="50%" id="logo">
                        <img style="max-width: 150px" src="<?php echo( $logo ); ?>" />
                    </td>
                    <td valign="top" width="50%" id="company-info"><?php echo get_bloginfo( 'name' ); ?><br /><?php echo get_bloginfo( 'description' ); ?><br /></td>
                </tr>
            </table>
            <table table width="100%; margin-bottom: 30px;">
                <tr>
                    <td width="20%" valign="top"><?php esc_html_e('Invoice No. :', 'web-to-print-online-designer'); ?></td>
                    <td width="30%" valign="top">#<?php echo( $order->get_id() ); ?></td>
                    <td width="20%" valign="top"><?php esc_html_e('Order No. :', 'web-to-print-online-designer'); ?></td>
                    <td width="30%" valign="top">#<?php echo( $order->get_id() ); ?></td>
                </tr>
                <tr>
                    <td valign="top"><?php esc_html_e( 'Invoice Date :', 'web-to-print-online-designer' ); ?></td>
                    <td valign="top"><?php echo date_i18n( 'F d, Y', current_time( 'timestamp', 0 ) ); ?></td>
                    <td valign="top"><?php esc_html_e( 'Order Date :', 'web-to-print-online-designer' ); ?></td>
                    <td valign="top"><?php echo date_i18n( 'F d, Y', strtotime( $order->get_date_created() ) ); ?></td>
                </tr>
                <tr>
                    <td valign="top"><?php esc_html_e('Payment Method :', 'web-to-print-online-designer'); ?></td>
                    <td valign="top"><?php echo ucwords( $order->get_payment_method_title() ); ?></td>
                    <td valign="top"><?php esc_html_e('Shipping Method :', 'web-to-print-online-designer'); ?></td>
                    <td valign="top"><?php echo ucwords( $order->get_shipping_method() ); ?></td>
                </tr>
                <tr>
                    <td valign="top" colspan="2">
                        <h3><?php echo apply_filters('pdf_template_billing_details_text', esc_html__('Billing Details', 'web-to-print-online-designer')); ?></h3>
                        <?php echo( $order->get_formatted_billing_address() ); ?><br />
                        <?php echo( $order->get_billing_phone() ); ?><br />
                        <?php echo( $order->get_billing_email() ); ?>
                        <?php if ( $order->get_meta( 'VAT Number',TRUE ) || $order->get_meta( 'vat_number',TRUE ) ): 
                                $vat_number = $order->get_meta( 'VAT Number',TRUE ) ? $order->get_meta( 'VAT Number',TRUE ) : $order->get_meta( 'vat_number',TRUE );
                                echo( $vat_number ); ?>
                        <?php endif; ?>
                    </td>
                    <td valign="top" colspan="2">
                        <h3><?php esc_html_e('Shipping Details', 'web-to-print-online-designer'); ?></h3>
                        <?php echo( $order->get_formatted_shipping_address() ); ?>
                    </td>
                </tr>

            </table>
            <table table width="100%;">
                <tr>
                    <td style="font-weight: bold;"><h3><?php esc_html_e('Summary items', 'web-to-print-online-designer'); ?></h3></td>
                </tr>
            </table>
            <table table width="100%; margin-bottom: 30px;">
                <?php 
                    $items = $order->get_items();
                    foreach( $items as $order_item_id => $item ):
                        $nbu_item_key = $item["item_meta"]["_nbu"];
                        $upload_datas = (array)json_decode( stripslashes( $item["item_meta"]["_nbau"] ) );                  
                        $files = array();
                        if( count( $upload_datas ) > 0 ){
                            $upload_path    = NBDESIGNER_UPLOAD_DIR . '/' . $nbu_item_key;
                            foreach( $upload_datas as $key => $data ){
                                $data       = (array)$data;
                                $final_file = $upload_path . '_preview_final/' . $data['name'];
                                $files[]    = $final_file;
                            }
                        }
                        $product    = $item->get_product();
                        $quantity   = $item->get_quantity();
                ?>
                <tr>
                    <td width="60%" valign="top">
                        <div style="margin-bottom: 20px;"><?php echo( $product->get_title() ); ?></div>
                        <div style="text-align:center">
                            <?php foreach( $files as $file ): ?>
                            <img style="boder: 1px solid #ddd; " class="tile" src="<?php echo( $file ); ?>" width="100" />
                            <?php endforeach; ?>
                        </div>
                    </td>
                    <td width="40%" valign="top" align="left">
                        <?php echo( $quantity ); ?> &times; <?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td width="60%" valign="top">
                        <h3><?php esc_html_e('Note:', 'web-to-print-online-designer'); ?></h3>
                        <?php echo( $order->get_customer_note() ); ?>
                    </td>
                    <td width="40%" valign="top" align="right" style="border-top: 1px solid #ddd; ">
                        <table width="100%">
                        <?php 
                            $totals = $order->get_order_item_totals(); 
                            if ( $totals ) {
                                foreach ( $totals as $total ) {
                        ?>
                            <tr>
                                <td valign="top"><?php echo wp_kses_post( $total['label'] ); ?></td>
                                <td valign="top"><?php echo wp_kses_post( $total['value'] ); ?></td>
                            </tr>
                        <?php
                                }
                            } 
                        ?>
                        </table>
                    </td>
                </tr>
            </table>
            <div>
                <div><?php esc_html_e('If you have any questions, visit our support site at', 'web-to-print-online-designer'); ?></div>
                <div><a href="<?php echo get_bloginfo('url'); ?>"><?php echo get_bloginfo('url'); ?></a> <?php esc_html_e('contact us at', 'web-to-print-online-designer'); ?> <?php echo get_bloginfo('admin_email'); ?></div>
            </div>
        </div>
    </body> 
</html>