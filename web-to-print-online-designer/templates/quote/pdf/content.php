<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$order             = new WC_Order( $order_id );
$user_name         = $order->get_meta('_raq_customer_name');
$user_email        = $order->get_meta('_raq_customer_email');
$expired           = $order->get_meta('_raq_expired');
$items             = $order->get_items();
$align             = is_rtl() ? 'right' : 'left';
?>
<style>
    ul, ul li{
        list-style: none; 
        font-size: 10pt;
        margin: 0;
        padding: 0;
    }
    td, table, tr {
        margin: 0;
        padding: 0;
    }
</style>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td><b><?php _e('Customer: ', 'web-to-print-online-designer'); ?></b></td>
        <td>
            <?php echo $user_name; ?><br>
            <?php echo $user_email; ?>
        </td>
    </tr>
    <?php if( $expired != '' ): ?>
    <tr>
        <td><b><?php echo __( 'Expiration date: ', 'web-to-print-online-designer' ) ?></b></td>
        <td>
            <?php echo $expired ?>
        </td>
    </tr>
    <tr><td><br/></td><td><br/></td></tr>
    <?php endif; ?>
</table>
<table cellspacing="0" cellpadding="6" style="border: 1px solid #eee;border-collapse: collapse;">
    <thead>
        <tr>
            <th style="text-align: center; border: 1px solid #eee; font-weight: bold; width: 50%;"><?php _e( 'Product', 'web-to-print-online-designer' ); ?></th>
            <th style="text-align: center; border: 1px solid #eee; font-weight: bold; width: 25%;"><?php _e( 'Quantity', 'web-to-print-online-designer' ); ?></th>
            <th style="text-align: center; border: 1px solid #eee; font-weight: bold; width: 25%;"><?php _e( 'Subtotal', 'web-to-print-online-designer' ); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ( $items as $item_id => $item ):
                $product    = $item->get_product();
                $quantity   = $item->get_quantity();
                $sku        = $product->get_sku();
        ?>
        <tr>
            <td style="width: 50%;">
                <b><?php 
                    echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) ); 
                    if( $sku ) echo wp_kses_post( ' (#' . $sku . ')' );
                ?></b>
                <?php 
                    wc_display_item_meta(
                        $item,
                        array(
                            'label_before' => '<strong class="wc-item-meta-label" style="float: ' . esc_attr( $align ) . '; margin-' . esc_attr( $align ) . ': .25em; clear: both">',
                        )
                    );
                ?>
            </td>
            <td style="text-align: center; border: 1px solid #eee;width: 25%;"><?php echo $quantity; ?></td>
            <td class="td" style="text-align: center; border: 1px solid #eee; width: 25%;">
                <?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    <tfoot>
        <?php
        $totals = $order->get_order_item_totals();
        if ( $totals ) {
            $i = 0;
            foreach ( $totals as $total ) {
                $i++;
                ?>
                <tr>
                    <th class="td" colspan="2" style="text-align:<?php echo esc_attr( $align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['label'] ); ?></th>
                    <td class="td" style="text-align: center; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['value'] ); ?></td>
                </tr>
                <?php
            }
        }
        ?>
    </tfoot>
</table>
<?php 
    $note = nbdesigner_get_option('nbdesigner_quote_pdf_note', ''); 
    if( $note != '' ):
?>
<p style="margin-top: 15pt;">
    <b><?php echo __( 'Note: ', 'web-to-print-online-designer' ) ?></b>
    <?php echo $note; ?>
</p>
<?php endif; ?>