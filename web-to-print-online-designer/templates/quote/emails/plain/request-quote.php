<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
echo $email_heading . "\n\n";
echo $email_description . "\n\n";
if( !( nbdesigner_get_option('nbdesigner_quote_attach_pdf', 'no') == 'yes' && nbdesigner_get_option('nbdesigner_quote_remove_list_in_email', 'no') == 'yes' ) ){
    nbdesigner_get_template('quote/emails/plain/request-quote-items.php', array(
        'raq_data' => $raq_data
    ));
}
if ( ! empty( $raq_data['user_message'] ) ) {
    echo __( 'Customer\'s message', 'web-to-print-online-designer' ) . "\n";
    echo $raq_data['user_message'] . "\n\n";
}
echo __( 'Customer\'s details', 'web-to-print-online-designer' ) . "\n";
echo __( 'Name:', 'web-to-print-online-designer' );
echo $raq_data['user_name'] . "\n";
echo __( 'Email:', 'web-to-print-online-designer' );
echo $raq_data['user_email'] . "\n";
$country_code = isset( $raq_data['user_country'] ) ? $raq_data['user_country'] : '';

foreach ( $raq_data as $key => $field ) {
    $avoid_key = array('customer_id', 'raq_content', 'user_country', 'user_message', 'user_email', 'user_name', 'order_id', 'message');
    if (in_array($key, $avoid_key)) {
        continue;
    }
    switch ($field['type']) {
        case 'nbdq_heading':
            echo '-- ' . $field['label'] . ' --';
            break;
        case 'country':
            $countries = WC()->countries->get_countries();
            echo $field['label'] . ': ' . $countries[$country_code] . "\n";
            break;
        case 'state':
            $states = WC()->countries->get_states($country_code);
            $state = $states[$field['value']];
            echo $field['label'] . ': ' . ( $state == '' ? $field['value'] : $state ) . "\n";
            break;
        case 'checkbox':
            echo $field['label'] . ': ' . ( $field['value'] == 1 ? __('Yes', 'web-to-print-online-designer') : __('No', 'web-to-print-online-designer') ) . "\n";
            break;
        case 'nbdq_multiselect':
            echo $field['label'] . ': ' . implode(', ', $field['value']) . "\n";
            break;
        default:
            echo $field['label'] . ': ' . $field['value'] . "\n";
    }
}
echo "\n----------------------------------------------------\n\n";
echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );