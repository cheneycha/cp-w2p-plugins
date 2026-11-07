<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
echo "= " . esc_attr( $email_heading ) . " =\n\n";
?>

<?php esc_attr_e( 'Hi '. $data['username'], 'web-to-print-online-designer' ); echo " \n";?>

<?php esc_attr_e( 'Your withdraw request was cancelled', 'web-to-print-online-designer' ); echo " \n";?>

<?php esc_attr_e( 'You sent a withdraw request of:', 'web-to-print-online-designer' );  echo " \n";?>

<?php esc_attr_e( 'Amount : '.$data['amount'], 'web-to-print-online-designer' ); echo " \n";?>

<?php esc_attr_e( 'Here\'s the reason, why : ', 'web-to-print-online-designer' ); echo " \n";?>
<?php echo esc_html( $data['note'] ); echo " \n";?>
<?php
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html( apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) ) );