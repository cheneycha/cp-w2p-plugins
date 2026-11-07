<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p>
    <?php esc_html_e( 'Hi,', 'web-to-print-online-designer' ); ?>
</p>
<p>
    <?php esc_html_e( 'A new withdraw request has been made by', 'web-to-print-online-designer' ); ?> <?php echo esc_attr( $data ['username'] ); ?>.
</p>
<hr>
<ul>
    <li>
        <strong>
            <?php esc_html_e( 'Username : ', 'web-to-print-online-designer' ); ?>
        </strong>
        <?php
        printf( '<a href="%s">%s</a>', esc_attr( $data['profile_url'] ), esc_attr( $data['username'] ) ); ?>
    </li>
    <li>
        <strong>
            <?php esc_html_e( 'Request Amount:', 'web-to-print-online-designer' ); ?>
        </strong>
        <?php echo $data['amount']; ?>
    </li>
</ul>

<?php echo sprintf( __( 'You can approve or deny it by going <a href="%s"> here </a>', 'web-to-print-online-designer' ), esc_attr( $data['withdraw_page'] ) ); ?>

<?php

do_action( 'woocommerce_email_footer', $email );