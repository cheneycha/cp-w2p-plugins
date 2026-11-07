<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<?php 
    $heading = __('The customer message', 'web-to-print-online-designer');
    do_action( 'woocommerce_email_header', $heading ); 
?>
<p><?php esc_html_e( $message ); ?></p>
<p><?php esc_html_e( 'From:', 'web-to-print-online-designer' ); ?> <?php echo $name; ?></p>
<p><?php esc_html_e( 'Email:', 'web-to-print-online-designer' ); ?> <?php echo $email; ?></p>
<?php do_action( 'woocommerce_email_footer' );