<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( !class_exists( 'NBD_Quote_Status' ) ) {
    #[\AllowDynamicProperties]
    class NBD_Quote_Status extends WC_Email {
        public $enable_cc;
        public $status;
        public $order;
        public $reason;
        public function __construct() {
            $this->id          = 'nbdraq_quote_status';
            $this->title       = __( 'Accepted/rejected Quote', 'web-to-print-online-designer' );
            $this->description = __( 'This email is sent when a user clicks on "Accept/Reject" button in a Proposal', 'web-to-print-online-designer' );
            $this->heading = __( 'Request a quote', 'web-to-print-online-designer' );
            $this->subject = __( '[Answer to quote request]', 'web-to-print-online-designer' );
            $this->template_html  = 'quote/emails/change-status.php';
            $this->template_plain  = 'quote/emails/plain/change-status.php';
            $this->template_base = NBDESIGNER_PLUGIN_DIR . 'templates/';
            parent::__construct();
            if( $this->enabled == 'no'){
                return;
            }
            add_action( 'change_raq_status_mail_notification', array( $this, 'trigger' ), 15, 1 );
            $this->recipient = $this->get_option( 'recipient' );
            if ( !$this->recipient ) {
                $this->recipient = get_option( 'admin_email' );
            }
            $this->enable_cc = $this->get_option( 'enable_cc' );
            $this->enable_cc = $this->enable_cc == 'yes';
        }
        public function trigger( $args ) {
            if( $this->settings['email_from_email'] == 'no'){
                return;
            }
            $this->status = $args['status'];
            $this->order  = $args['order'];
            $this->reason = isset( $args['reason'] ) ? $args['reason'] : '';
            $order_id = $this->order->get_id();
            if ( version_compare( WC()->version, '3.2.0', '<' ) ) {
                $this->find['quote-number']    = '{quote_number}';
                $this->replace['quote-number'] = $order_id;
            }else{
                $this->placeholders['{quote_number}'] = apply_filters( 'nbdraq_quote_number', $order_id );
            }
            $this->object = $this;
            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments( ) );
        }
        public function get_headers() {
            $headers = '';
            if ( $this->enable_cc ) {
                $user_email = $this->order->get_billing_email();
                $headers .= "Cc: " . $user_email . "\r\n";
            }
            $headers .= "Content-Type: " . $this->get_content_type() . "\r\n";
            return apply_filters( 'woocommerce_email_headers', $headers, $this->id, $this->object );
        }
        public function get_content_html() {
            ob_start();
            wc_get_template($this->template_html, array(
                    'order'         => $this->order,
                    'email_heading' => $this->get_heading(),
                    'status'        => $this->status,
                    'reason'        => $this->reason,
                    'sent_to_admin' => true,
                    'plain_text'    => false,
                    'email'         => $this
            ), '', $this->template_base );
            return ob_get_clean();
        }
        function get_content_plain() {
            ob_start();
            wc_get_template($this->template_plain, array(
                'order'         => $this->order,
                'email_heading' => $this->get_heading(),
                'status'        => $this->status,
                'reason'        => $this->reason,
                'sent_to_admin' => true,
                'plain_text'    => true,
                'email'         => $this
            ), '', $this->template_base );
            return ob_get_clean();
        }
        public function get_attachments( ){
            $attachments = array();
            return apply_filters( 'woocommerce_email_attachments', $attachments, $this->id, $this->object );
        }
        public function get_from_name( $from_name = '' ) {
            $email_from_name = ( isset($this->settings['email_from_name']) && $this->settings['email_from_name'] != '' ) ? $this->settings['email_from_name'] : '';
            return wp_specialchars_decode( esc_html( $email_from_name ), ENT_QUOTES );
        }
        public function get_from_address( $from_email = '' ) {
            $email_from_email = ( isset($this->settings['email_from_email']) && $this->settings['email_from_email'] != '' ) ? $this->settings['email_from_email'] : '';
            return sanitize_email( $email_from_email );
        }
        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title'         => __( 'Enable/Disable', 'web-to-print-online-designer' ),
                    'type'          => 'checkbox',
                    'label'         => __( 'Enable this email notification', 'web-to-print-online-designer' ),
                    'default'       => 'yes'
                ),
                'email_from_name'    => array(
                    'title'       => __( '"From" Name', 'web-to-print-online-designer' ),
                    'type'        => 'text',
                    'description' => '',
                    'placeholder' => '',
                    'default'     => get_option( 'woocommerce_email_from_name' )
                ),
                'email_from_email'    => array(
                    'title'       => __( '"From" Email Address', 'web-to-print-online-designer' ),
                    'type'        => 'text',
                    'description' => '',
                    'placeholder' => '',
                    'default'     => get_option( 'woocommerce_email_from_address' )
                ),
                'subject'    => array(
                    'title'       => __( 'Subject', 'web-to-print-online-designer' ),
                    'type'        => 'text',
                    'description' => sprintf( __( 'This field lets you edit email subject line. Leave it blank to use default subject text: <code>%s</code>. You can use {quote_number} as a placeholder that will show the quote number in the quote.', 'web-to-print-online-designer' ), $this->subject ),
                    'placeholder' => '',
                    'default'     => ''
                ),
                'recipient'  => array(
                    'title'       => __( 'Recipient(s)', 'web-to-print-online-designer' ),
                    'type'        => 'text',
                    'description' => sprintf( __( 'Enter recipients (separated by commas) for this email. Defaults to <code>%s</code>', 'web-to-print-online-designer' ), esc_attr( get_option( 'admin_email' ) ) ),
                    'placeholder' => '',
                    'default'     => ''
                ),
                'enable_cc'  => array(
                    'title'       => __( 'Send CC copy', 'web-to-print-online-designer' ),
                    'type'        => 'checkbox',
                    'description' => __( 'Send a carbon copy to the user', 'web-to-print-online-designer' ),
                    'default'     => 'no'
                ),
                'heading'    => array(
                    'title'       => __( 'Email Heading', 'web-to-print-online-designer' ),
                    'type'        => 'text',
                    'description' => sprintf( __( 'This field lets you change the main heading in email notification. Leave it blank to use default heading type: <code>%s</code>.', 'web-to-print-online-designer' ), $this->heading ),
                    'placeholder' => '',
                    'default'     => ''
                ),
                'email-description'    => array(
                    'title'       => __( 'Email Description', 'web-to-print-online-designer' ),
                    'type'        => 'textarea',
                    'placeholder' => '',
                    'default'     =>  __( 'You have received a request for a quote. The request is the following:', 'web-to-print-online-designer')
                ),
                'email_type' => array(
                    'title' 		=> __( 'Email type', 'web-to-print-online-designer' ),
                    'type' 		=> 'select',
                    'description' 	=> __( 'Choose email format.', 'web-to-print-online-designer' ),
                    'default' 		=> 'html',
                    'class'		=> 'email_type wc-enhanced-select',
                    'options'		=> $this->get_email_type_options()
                ),
            );
        }
    }
}
return new NBD_Quote_Status();