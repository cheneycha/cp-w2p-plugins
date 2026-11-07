<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action('woocommerce_email_header', $reason); ?>

<p><?php echo sprintf(__( "Your order with order number %s now has the following status: %s", 'web-to-print-online-designer'),$order->get_order_number(),'<b>' . __( $reason, 'web-to-print-online-designer').'</b>'); ?></p>

<p><?php esc_html_e( $message ); ?></p>

<?php if ($reason == 'Your design rejected'): ?>
    <p><?php esc_html_e('Please log into your account and design again. Go to product detail page, redesign and save your design (haven\'t to add to cart!)', 'web-to-print-online-designer'); ?></p>
    <p><a href="<?php echo esc_url( $my_order_url ); ?>"><?php esc_html_e( 'Login to design again.', 'web-to-print-online-designer'); ?></a></p>
<?php endif; ?>

<?php do_action('woocommerce_email_footer'); ?>