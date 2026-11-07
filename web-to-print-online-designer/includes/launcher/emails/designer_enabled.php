<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'NBDL_Designer_Enabled' ) ) {

    #[\AllowDynamicProperties]
    class NBDL_Designer_Enabled extends WC_Email {
        public $enable_bcc;
        public $designer;
        public function __construct() {
            $this->id               = 'nbdl_email_designer_enable';
            $this->title            = __( 'Designer Enable', 'web-to-print-online-designer' );
            $this->description      = __( 'This email is sent to a designer when his/her designer account enabled from admin settings', 'web-to-print-online-designer' );
            $this->heading          = __( 'Your designer account is activated', 'web-to-print-online-designer' );
            $this->subject          = __( '[{site_name}] Your account is activated', 'web-to-print-online-designer' );
            $this->template_html    = 'launcher/emails/designer_enabled.php';
            $this->template_plain   = 'launcher/emails/plain/designer_enabled.php';
            $this->template_base    = NBDESIGNER_PLUGIN_DIR.'/templates/';

            if( $this->enabled == 'no'){
                return;
            }

            add_action( 'nbdl_designer_enabled', array( $this, 'trigger' ), 15, 1 );
            $this->customer_email = true;
            parent::__construct();
            $this->enable_bcc = $this->get_option( 'enable_bcc' );
            $this->enable_bcc = $this->enable_bcc == 'yes';
        }
        public function trigger( $designer_id ) {
            $this->setup_locale();

            $this->designer     = get_user_by( 'ID', $designer_id );
            $this->recipient    = $this->designer->user_email;
            if ( version_compare( WC()->version, '3.2.0', '<' ) ) {
                $this->find['site_name']        = '{site_name}';
                $this->find['display_name']     = '{display_name}';
                $this->replace['site_name']     = $this->get_from_name();
                $this->replace['display_name']  = $this->designer->display_name;
            }else{
                $this->placeholders['{site_name}']      = $this->get_from_name();
                $this->placeholders['{display_name}']   = $this->designer->display_name;
            }

            $this->send( $this->recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

            $this->restore_locale();
        }
        public function get_content_html() {
            ob_start();
            wc_get_template($this->template_html, array(
                'email_heading'     => $this->get_heading(),
                'email_title'       => str_replace( '{display_name}', $this->designer->display_name, $this->get_option( 'email-title' )),
                'email_description' => $this->get_option( 'email-description' ),
                'sent_to_admin'     => false,
                'plain_text'        => false,
                'email'             => $this
            ), '', $this->template_base );
            return ob_get_clean();
        }
        function get_content_plain() {
            ob_start();
            wc_get_template($this->template_plain, array(
                'email_heading'     => $this->get_heading(),
                'email_title'       => str_replace( '{display_name}', $this->designer->display_name, $this->get_option( 'email-title' )),
                'email_description' => $this->get_option( 'email-description' ),
                'sent_to_admin'     => false,
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
                    'title'       => __( 'Enable/Disable', 'web-to-print-online-designer' ),
                    'type'        => 'checkbox',
                    'label'       => __( 'Enable this email notification', 'web-to-print-online-designer' ),
                    'default'     => 'yes',
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
                    'description' => sprintf( __( 'This field lets you modify the email subject line. Leave it blank to use the default subject text: <code>%s</code>. You can use {site_name} as a placeholder that will show the your site address.', 'web-to-print-online-designer' ), $this->subject ),
                    'placeholder' => $this->subject,
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
                    'placeholder' => $this->heading,
                    'default'     => ''
                ),
                'email-title'    => array(
                    'title'       => __( 'Email Title', 'web-to-print-online-designer' ),
                    'type'        => 'text',
                    'placeholder' => __( 'Congratulations {display_name}!', 'web-to-print-online-designer' ),
                    'description' => __( 'This field lets you change the main title in email notification. Available placeholders: <code>{display_name}!</code>.', 'web-to-print-online-designer' ),
                    'default'     => __( 'Congratulations {display_name}!', 'web-to-print-online-designer' )
                ),
                'email-description'    => array(
                    'title'       => __( 'Email Description', 'web-to-print-online-designer' ),
                    'type'        => 'textarea',
                    'css'         => 'width:400px; height: 75px;',
                    'placeholder' => $this->description,
                    'default'     => ''
                ),
                'email_type' => array(
                    'title'         => __( 'Email type', 'web-to-print-online-designer' ),
                    'type'          => 'select',
                    'description'   => __( 'Choose email format.', 'web-to-print-online-designer' ),
                    'default'       => 'html',
                    'class'         => 'email_type wc-enhanced-select',
                    'options'       => $this->get_email_type_options()
                )
            );
        }
    }

}

return new NBDL_Designer_Enabled();