<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$order_id = $order->get_id();
do_action( 'woocommerce_email_header', $email_heading, $email );
if( $status == 'accepted'): ?>
    <p><?php printf( __('The Proposal #%s has been accepted', 'web-to-print-online-designer'), $order_id ) ?></p>
<?php else: ?>
    <p><?php printf( __('The Proposal #%s has been rejected.', 'web-to-print-online-designer'), $order_id ) ?></p>
    <?php  echo '"'.stripcslashes( $reason ).'"' ?>
<?php endif ?>
    <p></p>
    <p><?php printf( __( 'You can see details here: <a href="%s">#%s</a>', 'web-to-print-online-designer' ),  admin_url( 'post.php?post='.$order_id.'&action=edit'), $order_id ) ?></p>
<?php
do_action( 'woocommerce_email_footer', $email );