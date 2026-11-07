<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$order_id          = $raq_data['order_id'];
$order             = new WC_Order( $order_id );
do_action( 'woocommerce_email_header', $email_heading, $email );
?>
<p><?php echo $email_description ?></p>
<?php
if( !( nbdesigner_get_option('nbdesigner_quote_attach_pdf', 'no') == 'yes' && nbdesigner_get_option('nbdesigner_quote_remove_list_in_email', 'no') == 'yes' ) ){
    nbdesigner_get_template('quote/emails/request-quote-items.php', array(
        'order'     => $order,
        'order_id'  => $order_id
    ));
}
?>
<p></p>
<?php if ( ! empty( $raq_data['user_message'] ) ): ?>
    <h2><?php _e( 'Customer\'s message', 'web-to-print-online-designer' ); ?></h2>
    <p><?php echo $raq_data['user_message'] ?></p>
<?php endif ?>
<h2><?php _e( 'Customer\'s details', 'web-to-print-online-designer' ); ?></h2>
<?php
if( !isset(  $raq_data['from_checkout'] )  ) {
    $country_code = isset( $raq_data['user_country'] ) ? $raq_data['user_country'] : '';
    foreach ( $raq_data as $key => $field ) {
        if ( ! isset( $field['id'] ) ) {
            continue;
        };
        $avoid_key = array('customer_id', 'raq_content', 'user_country', 'user_message', 'user_email', 'user_name', 'order_id', 'message');
        if ( in_array( $key, $avoid_key ) ) {
            continue;
        }
        $field_type = strtolower( $field['type'] );
        switch ( $field_type ) {
            case 'nbdq_heading':
                ?><h3><?php echo $field['label'] ?></h3><?php
                break;
            case 'email':
                ?><p><strong><?php echo $field['label']; ?></strong>: <a href="mailto:<?php echo $field['value']; ?>"><?php echo $field['value']; ?></a></p><?php
                break;
            case 'country':
                $countries = WC()->countries->get_countries();
                ?><p><strong><?php echo $field['label']; ?></strong>: <?php echo $countries[ $country_code ] ?></p><?php
                break;
            case 'state':
                $states = WC()->countries->get_states( $country_code );
                $state = isset( $states[ $field['value'] ] ) ? $states[ $field['value'] ] : ''; ?><p><strong><?php echo $field['label']; ?></strong>: <?php echo( $state == '' ? $field['value'] : $state ) ?></p><?php
                break;
            case 'nbdq_multiselect':
                ?><p><strong><?php echo $field['label']; ?></strong>: <?php echo implode( ', ', $field['value'] ); ?></p><?php
                break;
            default:
                ?><p><strong><?php echo $field['label']; ?></strong>: <?php echo $field['value']; ?></p><?php
                break;
        }
    }
}else{
    do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );
}
do_action( 'woocommerce_email_footer', $email );