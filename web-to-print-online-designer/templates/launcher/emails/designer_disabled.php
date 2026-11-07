<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<p>
    <?php printf( __( 'Hello %s', 'web-to-print-online-designerr' ), $data['display_name'] ); ?>
</p>
<p>
    <?php _e( 'Sorry, your designer account is deactivated.', 'web-to-print-online-designerr' ); ?>
</p>
<p>
    <?php _e( 'You can\'t sell or upload design anymore. To activate your designer account please contact with the admin.', 'web-to-print-online-designerr' ); ?>
</p>
<?php
do_action( 'woocommerce_email_footer', $email );