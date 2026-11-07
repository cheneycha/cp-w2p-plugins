<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( !class_exists( 'NBD_Send_Quote' ) ) {
    #[\AllowDynamicProperties]
    class NBD_Send_Quote extends WC_Email {
        public $enable_bcc;
        public $order_id;
        public $order;
        public $raq;
        public function __construct() {
            $this->id               = 'nbdraq_send_quote';
            $this->title            = __( 'Email with Quote', 'web-to-print-online-designer' );
            $this->description      = __( 'This email is sent when an administrator performs the action "Send the quote" from Order Editor', 'web-to-print-online-designer' );
            $this->heading          = __( 'Our Proposal', 'web-to-print-online-designer' );
            $this->subject          = __( '[Quote]', 'web-to-print-online-designer' );
            $this->template_html    = 'quote/emails/quote.php';
            $this->template_plain   = 'quote/emails/plain/quote.php';
            $this->template_base    = NBDESIGNER_PLUGIN_DIR . 'templates/';
            if( $this->enabled == 'no'){
                return;
            }
            add_action( 'send_quote_mail_notification', array( $this, 'trigger' ), 15, 1 );
            $this->customer_email = true;
            parent::__construct();
            $this->enable_bcc = $this->get_option( 'enable_bcc' );
            $this->enable_bcc = $this->enable_bcc == 'yes';
        }
        public function trigger( $order_id ) {
            $this->order_id = $order_id;
            if ( $order_id ) {
                $order                         = wc_get_order( $order_id );
                $order_date                    = $order->get_date_created();
                $expired                       = $order->get_meta('_raq_expired');
                $order_number                  = $order->get_order_number();
                $this->order                   = $order;
                $this->raq['customer_message'] = $order->get_customer_note();
                $this->raq['admin_message']    = nl2br( $order->get_meta('_raq_admin_message') );
                $this->raq['user_email']       = $order->get_meta('_raq_customer_email');
                $this->raq['user_name']        = $order->get_meta('_raq_customer_name');
                $this->raq['expiration_data']  = ( $expired != '' ) ? date_i18n( wc_date_format(), strtotime( $expired ) ) : '';
                $this->raq['order-date']       = date_i18n( wc_date_format(), strtotime( $order_date ) );
                $this->raq['order-id']         = $order_id;
                $this->raq['order-number']     = ! empty( $order_number ) ? $order_number : $order_id;
                $this->recipient               = $this->raq['user_email'];
                if ( version_compare( WC()->version, '3.2.0', '<' ) ) {
                    $this->find['quote-number']    = '{quote_number}';
                    $this->replace['quote-number'] = $this->raq['order-number'];
                }else{
                    $this->placeholders['{quote_number}'] = $this->raq['order-id'];
                }
                $this->send( $this->recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
            }
        }
        public function get_attachments() {
            $order_id    = $this->order_id;
            $attachments = array();
            if( nbdesigner_get_option('nbdesigner_quote_attach_pdf', 'no') == 'yes' ){
                $file = NBDESIGNER_DATA_DIR .'/quotes/quote_'. $order_id .'.pdf';
                if(file_exists($file) ){
                    $attachments[] = $file;
                }
            }
            return apply_filters( 'woocommerce_email_attachments', $attachments, $this->id, $this->object );
        }
        function get_headers() {
            $cc = ( isset( $this->settings['recipient'] ) && $this->settings['recipient'] != '' ) ? $this->settings['recipient'] : get_option( 'admin_email' );
            $headers = array();
            if ( get_option( 'woocommerce_email_from_address' ) != '' ) {
                $headers[] = "Reply-To: " . $this->get_from_address();
            }
            if ( $this->enable_bcc ) {
                $headers[] = "Bcc: " . $cc . "\r\n";
            }
            $headers[] = "Content-Type: " . $this->get_content_type();
            return apply_filters( 'woocommerce_email_headers', $headers, $this->id, $this->object );
        }
        public function get_content_html() {
            ob_start();
            wc_get_template($this->template_html, array(
                'order'             => $this->order,
                'email_heading'     => $this->get_heading(),
                'raq_data'          => $this->raq,
                'email_title'       => $this->get_option( 'email-title' ),
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
                'order'             => $this->order,
                'raq_data'          => $this->raq,
                'email_title'       => $this->get_option( 'email-title' ),
                'email_description' => $this->get_option( 'email-description' ),
                'sent_to_admin'     => true,
                'plain_text'        => true,
                'email'             => $this
            ), '', $this->template_base );
            return ob_get_clean();
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
                    'description' => sprintf( __( 'This field lets you modify the email subject line. Leave it blank to use the default subject text: <code>%s</code>. You can use {quote_number} as a placeholder that will show the quote number in the quote.', 'web-to-print-online-designer' ), $this->subject ),
                    'placeholder' => '',
                    'default'     => ''
                ),
                'recipient'  => array(
                    'title'       => __( 'Bcc Recipient(s)', 'web-to-print-online-designer' ),
                    'type'        => 'text',
                    'description' => __( 'Enter futher recipients (separated by commas) for this email. By default email to the customer', 'web-to-print-online-designer' ),
                    'placeholder' => '',
                    'default'     => ''
                ),
                'enable_bcc'  => array(
                    'title'       => __( 'Send BCC copy', 'web-to-print-online-designer' ),
                    'type'        => 'checkbox',
                    'description' => __( 'Send a blind carbon copy to the administrator', 'web-to-print-online-designer' ),
                    'default'     => 'no'
                ),
                'heading'    => array(
                    'title'       => __( 'Email Heading', 'web-to-print-online-designer' ),
                    'type'        => 'text',
                    'description' => sprintf( __( 'This field lets you change the main heading in email notification. Leave it blank to use default heading type: <code>%s</code>.', 'web-to-print-online-designer' ), $this->heading ),
                    'placeholder' => '',
                    'default'     => ''
                ),
                'email-title'    => array(
                    'title'       => __( 'Email Title', 'web-to-print-online-designer' ),
                    'type'        => 'text',
                    'placeholder' => '',
                    'default'     =>  __( 'Proposal', 'web-to-print-online-designer' )
                ),
                'email-description'    => array(
                    'title'       => __( 'Email Description', 'web-to-print-online-designer' ),
                    'type'        => 'textarea',
                    'placeholder' => '',
                    'default'     =>  __( 'You have received this email because you sent a quote request to our shop. The response to your request is the following:', 'web-to-print-online-designer' )
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
return new NBD_Send_Quote();