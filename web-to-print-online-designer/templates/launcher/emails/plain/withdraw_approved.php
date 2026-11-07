<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
echo "= " . esc_html( $email_heading ) . " =\n\n";
?>

<?php esc_html_e( 'Hi '. $data['username'], 'web-to-print-online-designer' ); echo " \n";?>

<?php esc_html_e( 'Your withdraw request has been approved, congrats!', 'web-to-print-online-designer' ); echo " \n";?>

<?php esc_html_e( 'You sent a withdraw request of:', 'web-to-print-online-designer' );  echo " \n";?>

<?php esc_html_e( 'Amount : '.$data['amount'], 'web-to-print-online-designer' ); echo " \n";?>

<?php esc_html_e( 'We\'ll transfer this amount to your preferred destination shortly.', 'web-to-print-online-designer' ); echo " \n";?>

<?php esc_html_e( 'Thanks for being with us.', 'web-to-print-online-designer' );  echo " \n";?>

<?php
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );