<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$order_id = $raq_data['order_id'];
$order = wc_get_order( $order_id );
$user_id = $order->get_user_id();
$items             = $order->get_items();
$show_price        = nbdesigner_get_option('nbdesigner_quote_hide_price_in_email', 'no') == 'no' ? true : false;
echo "----------------------------------------------------\n\n\n";
if ( ! empty( $items ) ):
    foreach ( $items as $item ):
        $product = $item->get_product();
        echo apply_filters( 'woocommerce_order_item_name', $item->get_name(), $item, false );
        if ( $product->get_sku() ) {
            echo ' (#' . $product->get_sku() . ')';
        }
        echo ' X ' . apply_filters( 'woocommerce_email_order_item_quantity', $item->get_quantity(), $item );
        if( $show_price ) echo ' = ' . $order->get_formatted_line_subtotal( $item ) . "\n";
        echo strip_tags( wc_display_item_meta( $item, array(
                'before'    => "\n- ",
                'separator' => "\n- ",
                'after'     => "",
                'echo'      => false,
                'autop'     => false,
        ) ) );
        echo "\n";
    endforeach;
endif;
echo "\n----------------------------------------------------\n\n";
if( $show_price ){
    $totals = $order->get_order_item_totals();
    if ($totals) {
        foreach ($totals as $total) {
            echo wp_kses_post($total['label'] . "\t " . $total['value']) . "\n";
        }
    }
}