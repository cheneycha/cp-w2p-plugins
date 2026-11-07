<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
echo $email_heading . "\n\n";
$order_id = $order->get_id();
if ( $status == 'accepted' ):
    printf( __( 'The Proposal #%d has been accepted', 'web-to-print-online-designer' ), $order_id );
else:
    printf( __( 'The Proposal #%d has been rejected. %s', 'web-to-print-online-designer' ), $order_id, $reason );
endif;
echo "\n----------------------------------------------------\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );

