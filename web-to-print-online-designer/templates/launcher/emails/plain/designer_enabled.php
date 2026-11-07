<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

echo $email_heading . "\n\n";
echo $email_title . "\n\n";
echo $email_description . "\n\n";
echo "\n----------------------------------------------------\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );