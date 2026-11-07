if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

echo "= " . $email_heading . " =\n\n";
?>

------------------------------------------------------------

<?php printf( __( 'Hello %s', 'web-to-print-online-designerr' ), $data['display_name'] ); echo " \n\n";  ?>

------------------------------------------------------------

<?php 
_e( 'Sorry, your designer account is deactivated.', 'web-to-print-online-designerr' ); 
echo " \n\n";
_e( 'You can\'t sell or upload design anymore. To activate your designer account please contact with the admin.', 'web-to-print-online-designerr' );
echo " \n\n";

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );