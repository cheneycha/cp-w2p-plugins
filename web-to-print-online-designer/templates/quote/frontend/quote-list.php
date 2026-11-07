<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$text_align = is_rtl() ? 'right' : 'left';
$quote_orders = get_posts(array(
    'numberposts' => 15,
    'meta_query'  => array(
        array(
            'key'     => '_raq_request',
            'compare' => 'EXISTS',
        ),
        array(
            'key'   => '_customer_user',
            'value' => get_current_user_id()
        ),
    ),
    'post_type'   => 'shop_order',
    'post_status' => array_keys( wc_get_order_statuses() )
));
?>
<?php if ( $quote_orders ) : ?>
<h2><?php _e( 'Recent Quotes', 'web-to-print-online-designer' ); ?></h2>
<table class="shop_table shop_table_responsive my_account_quotes my_account_orders">
    <thead>
        <tr>
            <th class="order-status"><span class="nobr"><?php _e( 'Status', 'web-to-print-online-designer' ); ?></span></th>
            <th class="order-number"><span class="nobr"><?php _e( 'Quote', 'web-to-print-online-designer' ); ?></span></th>
            <th class="order-date"><span class="nobr"><?php _e( 'Date', 'web-to-print-online-designer' ); ?></span></th>
            <th class="order-actions">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php
            foreach ( $quote_orders as $quote_order ) {
                $order_id   = $quote_order->ID;
                $order      = wc_get_order( $order_id );
                $order_date = $quote_order->post_date;
                $email      = $order->get_meta('_raq_customer_email');
                $raq_pay    = $order->get_meta('_raq_pay');
                $quote_url  = wc_get_endpoint_url( 'view-quote', $order_id, wc_get_page_permalink( 'myaccount' ) );
                $actions    = array();
                if ( in_array( $order->get_status(), array( 'pending', 'nbdq-accepted' ) ) ) {
                    $actions['accept'] = array(
                        'url'  => nbd_is_true($raq_pay ) ? nbd_get_quote_action_url( 'accept', $order_id, $email, $order->get_checkout_payment_url( false ) ) : nbd_get_quote_action_url( 'accept', $order_id, $email ),
                        'name' => __( 'Checkout', 'web-to-print-online-designer' )
                    );
                }
                if ( in_array( $order->get_status(), array( 'nbdq-pending' ) ) ) {
                    $actions['accept'] = array(
                        'url'  => nbd_is_true($raq_pay ) ? nbd_get_quote_action_url( 'accept', $order_id, $email, $order->get_checkout_payment_url( false ) ) : nbd_get_quote_action_url( 'accept', $order_id, $email ),
                        'name' => __( 'Accept', 'web-to-print-online-designer' )
                    );
                    $actions['reject'] = array(
                        'url'  => nbd_get_quote_action_url( 'reject', $order_id, $email ),
                        'name' => __( 'Reject', 'web-to-print-online-designer' )
                    );
                }
                $actions['view'] = array(
                    'url'  => $quote_url,
                    'name' => __( 'View', 'web-to-print-online-designer' )
                );
        ?>
        <tr class="quotes">
            <td class="quotes-status" data-title="<?php _e( 'Status', 'web-to-print-online-designer' ); ?>" style="text-align:<?php echo $text_align ?>; white-space:nowrap;">
		<?php nbdq_get_order_status_tag( $order->get_status() ); ?>
            </td>
            <td class="quotes-number" data-title="<?php _e( 'Order Number', 'web-to-print-online-designer' ); ?>">
                <a href="<?php echo $quote_url; ?>">
                    #<?php echo $order->get_order_number(); ?>
                </a>
            </td>
            <td class="quotes-date" data-title="<?php _e( 'Date', 'web-to-print-online-designer' ); ?>">
                <time datetime="<?php echo date( 'Y-m-d', strtotime( $order_date ) ); ?>" title="<?php echo esc_attr( strtotime( $order_date ) ); ?>"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order_date ) ); ?></time>
            </td>
            <td class="quotes-actions" data-order_id="<?php echo $order_id ?>">
                <?php
                    if ( $actions ) {
                        foreach ( $actions as $key => $action ) {
                            echo '<a href="' . esc_url( $action['url'] ) . '" class="button ' . sanitize_html_class( $key ) . '">' . esc_html( $action['name'] ) . '</a>';
                        }
                    }
                ?>
            </td>
        </tr>
        <?php }; ?>
    </tbody>
</table>
<?php endif;