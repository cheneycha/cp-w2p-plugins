<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

echo "= " . esc_attr( $email_heading ) . " =\n\n";
?>
<?php esc_attr_e( 'Hi,', 'web-to-print-online-designer' );  echo " \n";?>

<?php esc_attr_e( 'A new withdraw request has been made by - '.$data['username'], 'web-to-print-online-designer' );  echo " \n";?>

<?php esc_attr_e( 'Request Amount : '.$data['amount'], 'web-to-print-online-designer' );  echo " \n";?>

<?php esc_attr_e( 'Username : '.$data['username'], 'web-to-print-online-designer' );  echo " \n";?>
<?php esc_attr_e( 'Profile : '.$data['profile_url'], 'web-to-print-online-designer' );  echo " \n";?>

<?php esc_attr_e( 'You can approve or deny it by going here : '.$data['withdraw_page'], 'web-to-print-online-designer' );  echo " \n";?>

<?php
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );