<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( !class_exists( 'NBD_Send_Email_Request_Quote' ) ) {
    #[\AllowDynamicProperties]
    class NBD_Send_Email_Request_Quote extends WC_Email {
        public $enable_cc;
        public $raq;
        public function __construct() {
            $this->id          = 'nbdraq_email';
            $this->title       = __( 'Email to request a quote', 'web-to-print-online-designer' );
            $this->description = __( 'This email is sent when a user clicks on "Request a quote" button', 'web-to-print-online-designer' );
            $this->heading = __( 'Request a quote', 'web-to-print-online-designer' );
            $this->subject = __( '[Request a quote]', 'web-to-print-online-designer' );
            $this->template_html  = 'quote/emails/request-quote.php';
            $this->template_plain  = 'quote/emails/plain/request-quote.php';
            $this->template_base = NBDESIGNER_PLUGIN_DIR . 'templates/';
            add_action( 'send_raq_mail_notification', array( $this, 'trigger' ), 15, 1 );
            parent::__construct();
            if( $this->enabled == 'no'){
                return;
            }
            $this->recipient = $this->get_option( 'recipient' );
            if ( !$this->recipient ) {
                $this->recipient = get_option( 'admin_email' );
            }
            $this->enable_cc = $this->get_option( 'enable_cc' );
            $this->enable_cc = $this->enable_cc == 'yes';
        }
        public function trigger( $args ) {
            $new_order  = WC()->session->raq_new_order;
            $this->raq  = $args;
            $this->raq['order_id'] = is_null( $new_order ) ? 0 : $new_order;
            if ( version_compare( WC()->version, '3.2.0', '<' ) ) {
                $this->find['quote-number']    = '{quote_number}';
                $this->replace['quote-number'] = $this->raq['order_id'];

                $this->find['quote-user']    = '{quote_user}';
                $this->replace['quote-user'] = $this->raq['user_name'];

                $this->find['quote-email']    = '{quote_email}';
                $this->replace['quote-email'] = $this->raq['user_email'];
            } else {
                $this->placeholders['{quote_number}'] = $this->raq['order_id'];
                $this->placeholders['{quote_user}']   = $this->raq['user_name'];
                $this->placeholders['{quote_email}']  = $this->raq['user_email'];
            }
            $this->object = $this;
            $recipients = (array) $this->get_recipient();
            if ( $this->enable_cc ) {
                $recipients[] = $this->raq['user_email'];
            }
            $recipients = implode( ',', $recipients );
            $recipients = str_replace( ' ', '', $recipients );
            $return = $this->send( $recipients, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
        }
        public function get_headers() {
            $headers = "Reply-to: " . $this->raq['user_email'] . "\r\n";
            if ( $this->enable_cc ) {
                $headers .= "Cc: " . $this->raq['user_email'] . "\r\n";
            }
            $headers .= "Content-Type: " . $this->get_content_type() . "\r\n";
            return apply_filters( 'woocommerce_email_headers', $headers, $this->id, $this->object );
        }
        public function get_content_html() {
            ob_start();
            wc_get_template($this->template_html, array(
                'raq_data'          => $this->raq,
                'email_heading'     => $this->get_heading(),
                'email_description' => $this->get_option( 'email-description' ),
                'sent_to_admin'     => true,
                'plain_text'        => false,
                'email'             => $this
            ), '', $this->template_base );
            return ob_get_clean();
        }
        function get_content_plain() {
            ob_start();
            wc_get_template($this->template_plain, array(
                'raq_data'          => $this->raq,
                'email_heading'     => $this->get_heading(),
                'email_description' => $this->get_option( 'email-description' ),
                'sent_to_admin'     => true,
                'plain_text'        => true,
                'email'             => $this
            ), '', $this->template_base );
            return ob_get_clean();
        }
        public function get_attachments(){
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
                    'description' => sprintf( __( 'This field lets you edit email subject line. Leave it blank to use default subject text: <code>%s</code>. You can use {quote_number} as a placeholder that will show the quote number in the quote,<br>{quote_user} to show the customer\'s name, {quote_email} to show the customer\'s email', 'web-to-print-online-designer' ), $this->subject ),
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
                'quote_detail_link'               => array(
                    'title'    => __( 'Link to quote request details to be shown in "Request a Quote" email', 'web-to-print-online-designer' ),
                    'description'    => '',
                    'id'      => 'nbdraq_quote_detail_link',
                    'class'			=> 'email_type wc-enhanced-select',
                    'type'    => 'select',
                    'options' => array(
                        'myaccount' => __( 'Quote request details', 'web-to-print-online-designer' ),
                        'editor'    => __( 'Quote creation page (admin)', 'web-to-print-online-designer' ),
                    ),
                    'default' => 'myaccount'
                ),
                'email_type' => array(
                    'title' 		=> __( 'Email type', 'web-to-print-online-designer' ),
                    'type' 			=> 'select',
                    'description' 	=> __( 'Choose email format.', 'web-to-print-online-designer' ),
                    'default' 		=> 'html',
                    'class'			=> 'email_type wc-enhanced-select',
                    'options'		=> $this->get_email_type_options()
                ),

            );
        }
    }
}
return new NBD_Send_Email_Request_Quote();