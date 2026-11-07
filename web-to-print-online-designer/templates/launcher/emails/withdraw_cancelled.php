<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p>
    <?php esc_html_e( 'Hi '.$data['username'], 'web-to-print-online-designer' ); ?>
</p>
<p>
    <?php esc_html_e( 'Your withdraw request was cancelled!', 'web-to-print-online-designer' ); ?>
</p>
<p>
    <?php esc_html_e( 'You sent a withdraw request of:', 'web-to-print-online-designer' ); ?>
    <br>
    <?php esc_html_e( 'Amount : ', 'dweb-to-print-online-designer' ); ?>
    <?php echo $data['amount']; ?>
</p>
<p>
    <?php esc_html_e( 'Here\'s the reason, why : ', 'web-to-print-online-designer' ); ?>
    <br>
    <i><?php echo esc_html( $data['note'] ); ?></i>
</p>

<?php
do_action( 'woocommerce_email_footer', $email );