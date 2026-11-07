<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p>
    <?php esc_html_e( 'Hi '.$data['username'], 'web-to-print-online-designer' ); ?>
</p>
<p>
    <?php esc_html_e( 'Your withdraw request has been approved, congrats!', 'web-to-print-online-designer' ); ?>
</p>
<p>
    <?php esc_html_e( 'You sent a withdraw request of:', 'web-to-print-online-designer' ); ?>
    <br>
    <?php esc_html_e( 'Amount : ', 'web-to-print-online-designer' ); ?>
    <?php echo $data['amount']; ?>
</p>
<p>
    <?php esc_html_e( 'We\'ll transfer this amount to your preferred payment method shortly.', 'web-to-print-online-designer' ); ?>

    <?php esc_html_e( 'Thanks for being with us.', 'dweb-to-print-online-designer' ); ?>
</p>

<?php
do_action( 'woocommerce_email_footer', $email );