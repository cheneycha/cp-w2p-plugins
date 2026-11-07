<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
do_action( 'woocommerce_email_header', $email_heading, $email );
?>
<h2><?php printf( __( '%s n. %s', 'web-to-print-online-designer' ), $email_title, $raq_data['order-number'] ) ?></h2>
<p><?php echo $email_description; ?></p>
<p><strong><?php _e( 'Request date', 'web-to-print-online-designer' ) ?></strong>: <?php echo $raq_data['order-date'] ?></p>
<?php if ( $raq_data['expiration_data'] != '' ): ?>
<p><strong><?php _e( 'Expiration date', 'web-to-print-online-designer' ) ?></strong>: <?php echo $raq_data['expiration_data'] ?></p>
<?php endif ?>
<?php if ( ! empty( $raq_data['admin_message'] ) ): ?>
    <p><?php echo $raq_data['admin_message'] ?></p>
<?php endif ?>
<?php
nbdesigner_get_template('quote/emails/request-quote-items.php', array(
    'order'      => $order,
    'order_id'   => $raq_data['order-id']
));
$accepted_quote_page = nbd_get_quote_action_url( 'accept', $raq_data['order-id'], $raq_data['user_email'] );
$rejected_quote_page = nbd_get_quote_action_url( 'reject', $raq_data['order-id'], $raq_data['user_email'] );
?>
<p></p>
<p>
    <a href="<?php echo esc_url( $accepted_quote_page  ) ?>"><?php _e( 'Accept', 'web-to-print-online-designer' ); ?></a> | <a href="<?php echo esc_url( $rejected_quote_page ) ?>"><?php _e( 'Reject', 'web-to-print-online-designer' ); ?></a>
</p>
<p><?php echo $raq_data['order-id']; ?></p>
<?php
$formatted_address = $order->get_formatted_billing_address();
$billing_surname   = $order->get_billing_first_name();
$billing_phone     = $order->get_billing_phone();
$billing_vat       = '';
?>
<h2><?php _e( 'Customer\'s details', 'web-to-print-online-designer' ); ?></h2>
<?php if( empty( $raq_data['user_name'] ) && empty( $billing_surname ) ): ?>
<p><strong><?php _e( 'Name:', 'web-to-print-online-designer' ); ?></strong> <?php echo $raq_data['user_name'] ?></p>
<?php endif; ?>
<p><?php echo $formatted_address ?></p>
    <p><strong><?php _e( 'Email:', 'web-to-print-online-designer' ); ?></strong> <a href="mailto:<?php echo $raq_data['user_email']; ?>"><?php echo $raq_data['user_email']; ?></a>
</p>
<?php if ( $billing_vat != '' ): ?>
<p><strong><?php _e( 'Billing VAT:', 'web-to-print-online-designer' ); ?></strong> <?php echo $billing_vat ?></p>
<?php endif; ?>
<?php if ( $billing_phone != '' ): ?>
<p><strong><?php _e( 'Billing Phone:', 'web-to-print-online-designer' ); ?></strong> <?php echo $billing_phone ?></p>
<?php endif;
do_action( 'woocommerce_email_footer', $email );