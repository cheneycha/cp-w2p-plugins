<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$order              = wc_get_order( $order_id );
if( !$order ) {
    _e( 'Order not exist', 'web-to-print-online-designer' );
    return;
}
$order_date         = $order->get_date_created();
$order_status       = $order->get_status();
$customer_message   = $order->get_meta('_raq_customer_message');
$customer_email     = $order->get_meta('_raq_customer_email');
$admin_message      = $order->get_meta('_raq_admin_message');
$expired            = $order->get_meta('_raq_expired');
$raq_pay            = $order->get_meta('_raq_pay');
$accept_button_text = $order_status != 'nbdq-pending' ? __( 'Checkout', 'web-to-print-online-designer' ) : __( 'Accept', 'web-to-print-online-designer' );
$pdf_file           = false;
$accept_url         = nbd_is_true( $raq_pay ) ? nbd_get_quote_action_url( 'accept', $order_id, $customer_email, $order->get_checkout_payment_url( false ) ) : nbd_get_quote_action_url( 'accept', $order_id, $customer_email );
$reject_url         = nbd_get_quote_action_url( 'reject', $order_id, $customer_email );
$pdf_url            = NBDESIGNER_DATA_URL .'/quotes/quote_'. $order_id .'.pdf';
do_action( 'woocommerce_account_navigation' );
?>
<div class="woocommerce-MyAccount-content">
    <p>
        <strong><?php _e( 'Request date', 'web-to-print-online-designer' ) ?></strong>: <?php echo date_i18n( get_option( 'date_format' ), strtotime( $order_date ) ); ?>
    </p>
    <?php if ( $order->has_status( 'nbdq-rejected' ) && $order->get_customer_note() ): ?>
    <p>
        <strong><?php echo __( 'Customer reason:', 'web-to-print-online-designer' ) ?></strong> <?php echo $order->get_customer_note() ?>
    </p>
    <?php endif; ?>
    
    <?php if ( in_array( $order_status, array( 'nbdq-pending', 'nbdq-accepted', 'pending' ) ) ): ?>
    <p class="nbdq-buttons">
        <a class="button accept" href="<?php echo $accept_url; ?>"><?php echo $accept_button_text; ?></a>
        <?php if ( $order_status == 'nbdq-pending' ): ?>
        <a class="button reject" href="<?php echo $reject_url; ?>"><?php _e( 'Reject', 'web-to-print-online-designer' ); ?></a>
            <?php if( nbdesigner_get_option('nbdesigner_quote_allow_download_pdf', 'no') == 'yes' ): ?>
            <a class="button" href="<?php echo $pdf_url; ?>" target="_blank"><?php _e( 'Quote PDF', 'web-to-print-online-designer' ); ?></a>
            <?php endif; ?>
        <?php endif; ?>
    </p>
    <?php elseif( $order_status == 'nbdq-expired' ): ?>
    <p>
        <strong><?php echo __( 'Order Status:', 'web-to-print-online-designere' ) ?></strong> <?php _e( 'Expired Quote', 'web-to-print-online-designer' ); ?>
    </p>
    <?php endif; ?>
    
    <h2><?php _e( 'Quote Details', 'web-to-print-online-designer' ); ?></h2>
    <?php if ( $expired != '' ): ?>
    <p>
        <strong><?php _e( 'Expiration date', 'web-to-print-online-designer' ) ?></strong>: <?php echo date_i18n( wc_date_format(), strtotime( $expired ) ) ?>
    </p>
    <?php endif ?>
    <table class="shop_table order_details">
        <thead>
            <tr>
                <th class="product-name"><?php _e( 'Product', 'web-to-print-online-designer' ); ?></th>
                <th class="product-total"><?php _e( 'Total', 'web-to-print-online-designer' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                if ( sizeof( $order->get_items() ) > 0 ) {
                    foreach ( $order->get_items() as $item_id => $item ) {
                        $product = $item->get_product();
            ?>
            <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_order_item_class', 'woocommerce-table__line-item order_item', $item, $order ) ); ?>">
                <td class="woocommerce-table__product-name product-name">
                    <?php
                        $is_visible        = $product && $product->is_visible();
                        $product_permalink = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );
                        echo apply_filters( 'woocommerce_order_item_name', $product_permalink ? sprintf( '<a href="%s">%s</a>', $product_permalink, $item->get_name() ) : $item->get_name(), $item, $is_visible );
                        echo apply_filters( 'woocommerce_order_item_quantity_html', ' <strong class="product-quantity">' . sprintf( '&times; %s', $item->get_quantity() ) . '</strong>', $item );
                        do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );
                        wc_display_item_meta( $item );
                        do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
                    ?>
                </td>
                <td class="woocommerce-table__product-total product-total">
                    <?php echo $order->get_formatted_line_subtotal( $item ); ?>
                </td>
            </tr>
            <?php }} ?>
        </tbody>
        <tfoot>
            <?php
                foreach ( $order->get_order_item_totals() as $key => $total ) {
                    ?>
                    <tr>
                        <th scope="row"><?php echo $total['label']; ?></th>
                        <td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : $total['value']; ?></td>
                    </tr>
                    <?php
                }
            ?>
            <?php if ( $order->get_customer_note() ) : ?>
                <tr>
                    <th><?php _e( 'Note:', 'woocommerce' ); ?></th>
                    <td><?php echo wptexturize( $order->get_customer_note() ); ?></td>
                </tr>
            <?php endif; ?>
        </tfoot>
    </table>
    <h2><?php _e( 'Customer\'s details', 'web-to-print-online-designer' ); ?></h2>
    <?php 
        $show_shipping     = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address(); 
        $billing_email     = $order->get_billing_email();
        $billing_email     = empty( $billing_email ) ? $customer_email : $billing_email;
    ?>
    <section class="woocommerce-customer-details">
        <?php if ( $show_shipping ) : ?>
        <section class="woocommerce-columns woocommerce-columns--2 woocommerce-columns--addresses col2-set addresses">
            <div class="woocommerce-column woocommerce-column--1 woocommerce-column--billing-address col-1">
        <?php endif; ?>
            <h2 class="woocommerce-column__title"><?php esc_html_e( 'Billing address', 'woocommerce' ); ?></h2>
            <address>
                <?php echo wp_kses_post( $order->get_formatted_billing_address( __( 'N/A', 'woocommerce' ) ) ); ?>
                <?php if ( $order->get_billing_phone() ) : ?>
                    <p class="woocommerce-customer-details--phone"><?php echo esc_html( $order->get_billing_phone() ); ?></p>
                <?php endif; ?>
                <?php if ( $billing_email ) : ?>
                    <p class="woocommerce-customer-details--email"><?php echo esc_html( $billing_email ); ?></p>
                <?php endif; ?>
            </address>
        <?php if ( $show_shipping ) : ?>
            </div><!-- /.col-1 -->
            <div class="woocommerce-column woocommerce-column--2 woocommerce-column--shipping-address col-2">
                <h2 class="woocommerce-column__title"><?php esc_html_e( 'Shipping address', 'woocommerce' ); ?></h2>
                <address>
                    <?php echo wp_kses_post( $order->get_formatted_shipping_address( __( 'N/A', 'woocommerce' ) ) ); ?>
                </address>
            </div><!-- /.col-2 -->
        </section><!-- /.col2-set -->
        <?php endif; ?>
        <?php do_action( 'woocommerce_order_details_after_customer_details', $order ); ?>
    </section>
    <?php if( $admin_message != '' || $customer_message != '' ): ?>
     <h2><?php _e( 'Additional Information', 'web-to-print-online-designer' ); ?></h2>
    <table class="shop_table shop_table_responsive customer_details">
        <?php if ( '' != $customer_message ) { ?>
        <tr>
            <th scope="row"><?php _e( 'Customer\'s Message:', 'web-to-print-online-designer' ); ?></th>
            <td data-title="<?php _e( 'Customer\'s Message', 'web-to-print-online-designer' ); ?>"><?php echo wptexturize( $customer_message ); ?></td>
        </tr>
        <?php } ?>
        <?php if ( '' != $admin_message ) { ?>
        <tr>
            <th scope="row"><?php _e( 'Administrator\'s Message:', 'web-to-print-online-designer' ); ?></th>
            <td data-title="<?php _e( 'Administrator\'s Message', 'web-to-print-online-designer' ); ?>"><?php echo wptexturize( $admin_message ); ?></td>
        </tr>
        <?php } ?>
    </table>
    <?php endif; ?>
</div>
<div class="clear"></div>