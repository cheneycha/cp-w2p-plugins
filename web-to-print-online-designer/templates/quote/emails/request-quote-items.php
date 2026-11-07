<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$order_number      = $order->get_order_number();
$order_number      = ! empty( $order_number ) ? $order_number : $order_id;
$text_align        = is_rtl() ? 'right' : 'left';
$margin_side       = is_rtl() ? 'left' : 'right';
$items             = $order->get_items();
$image_size        = array( 32, 32 );
$show_price        = nbdesigner_get_option('nbdesigner_quote_hide_price_in_email', 'no') == 'no' ? true : false;
?>
<h2><?php printf( __( 'Request a Quote #%s', 'web-to-print-online-designer' ), $order_number ) ?></h2>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;border-collapse: collapse;">
    <thead>
    <tr>
        <th scope="col" style="text-align:<?php echo $text_align ?>; border: 1px solid #eee;"><?php _e( 'Preview', 'web-to-print-online-designer' ); ?></th>
        <th scope="col" style="text-align:<?php echo $text_align ?>; border: 1px solid #eee;"><?php _e( 'Product', 'web-to-print-online-designer' ); ?></th>
        <th scope="col" style="text-align:<?php echo $text_align ?>; border: 1px solid #eee;"><?php _e( 'Quantity', 'web-to-print-online-designer' ); ?></th>
        <th scope="col" style="text-align:<?php echo $text_align ?>; border: 1px solid #eee;"><?php _e( 'Subtotal', 'web-to-print-online-designer' ); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php
        foreach ( $items as $item_id => $item ):
            $product    = $item->get_product();
            $quantity   = $item->get_quantity();
            $image      = $product->get_image( $image_size );
            $sku        = $product->get_sku();
    ?>
        <tr>
            <td scope="col" class="td" style="text-align:center;border: 1px solid #eee; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
            <?php
                echo wp_kses_post( apply_filters( 'woocommerce_order_item_thumbnail', $image, $item ) );
            ?>
            </td>
            <td scope="col" class="td" style="text-align:<?php echo $text_align ?>;border: 1px solid #eee; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                <a href="<?php echo $product->get_permalink() ?>">
                    <?php 
                        echo wp_kses_post( apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false ) ); 
                        if( $sku ) echo wp_kses_post( ' (#' . $sku . ')' );
                    ?>
                </a>
                <?php 
                    do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );
                    wc_display_item_meta(
                        $item,
                        array(
                            'label_before' => '<strong class="wc-item-meta-label" style="float: ' . esc_attr( $text_align ) . '; margin-' . esc_attr( $margin_side ) . ': .25em; clear: both">',
                        )
                    );
                    do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
                ?>
            </td>
            <td scope="col" style="text-align:<?php echo $text_align ?>;border: 1px solid #eee;"><?php echo $quantity; ?></td>
            <td scope="col" class="td" style="text-align:<?php echo $text_align ?>;border: 1px solid #eee; vertical-align:middle; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;">
                <?php if( $show_price ) echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <?php if( $show_price ): ?>
    <tfoot>
            <?php
            $totals = $order->get_order_item_totals();
            if ( $totals ) {
                $i = 0;
                foreach ( $totals as $total ) {
                    $i++;
                    ?>
                    <tr>
                        <th class="td" scope="row" colspan="3" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['label'] ); ?></th>
                        <td class="td" style="text-align:<?php echo esc_attr( $text_align ); ?>; <?php echo ( 1 === $i ) ? 'border-top-width: 4px;' : ''; ?>"><?php echo wp_kses_post( $total['value'] ); ?></td>
                    </tr>
                    <?php
                }
            }
            ?>
    </tfoot>
    <?php endif; ?>
</table>