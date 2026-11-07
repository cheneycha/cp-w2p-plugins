<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'NBDL_Withdraw_Request' ) ) {

    #[\AllowDynamicProperties]
    class NBDL_Withdraw_Request  extends WC_Email {

        public function __construct() {
            $this->id               = 'nbdl_email__withdraw_request';
            $this->title            = __( 'NBDesigner Withdrawal Request', 'web-to-print-online-designer' );
            $this->description      = __( 'These emails are sent to chosen recipient(s) when a designer send request to withdraw', 'web-to-print-online-designer' );
            $this->template_html    = 'launcher/emails/withdraw_request.php';
            $this->template_plain   = 'launcher/emails/plain/withdraw_request.php';
            $this->template_base    = NBDESIGNER_PLUGIN_DIR.'/templates/';
    
            add_action( 'nbdl_after_withdraw_request', array( $this, 'trigger' ), 30, 3 );
    
            parent::__construct();
    
            $this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
        }

        public function get_default_subject() {
            return __( '[{site_name}] A New withdrawal request is made by {user_name}', 'web-to-print-online-designer' );
        }

        public function get_default_heading() {
            return __( 'New Withdraw Request from - {user_name}', 'web-to-print-online-designer' );
        }

        public function trigger( $current_user, $amount ) {

            if ( ! $this->is_enabled() ) {
                return;
            }
    
            $designer                    = get_user_by( 'id', $current_user->ID );
            $this->object                = $current_user;
            $this->find['username']      = '{user_name}';
            $this->find['amount']        = '{amount}';
            $this->find['profile_url']   = '{profile_url}';
            $this->find['withdraw_page'] = '{withdraw_page}';
            $this->find['site_name']     = '{site_name}';
            $this->find['site_url']      = '{site_url}';
    
            $this->replace['username']      = $designer->user_login;
            $this->replace['amount']        = wc_price( $amount );
            $this->replace['profile_url']   = wc_get_endpoint_url( 'my-store', '', wc_get_page_permalink( 'myaccount' ) );
            $this->replace['withdraw_page'] = add_query_arg( array( 'tab' => 'withdraw' ), wc_get_endpoint_url( 'my-store', '', wc_get_page_permalink( 'myaccount' ) ) );
            $this->replace['site_name']     = $this->get_from_name();
            $this->replace['site_url']      = site_url();
    
            $this->setup_locale();
            $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
            $this->restore_locale();
    
        }

        public function get_content_html() {
            ob_start();
    
            wc_get_template( $this->template_html, array(
                'designer'      => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => true,
                'plain_text'    => false,
                'email'         => $this,
                'data'          => $this->replace
            ), '', $this->template_base );
    
            return ob_get_clean();
        }

        public function get_content_plain() {
            ob_start();
    
            wc_get_template( $this->template_html, array(
                'designer'      => $this->object,
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => true,
                'plain_text'    => true,
                'email'         => $this,
                'data'          => $this->replace
            ), '', $this->template_base );
    
            return ob_get_clean();
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title'         => __( 'Enable/Disable', 'web-to-print-online-designer' ),
                    'type'          => 'checkbox',
                    'label'         => __( 'Enable this email notification', 'web-to-print-online-designer' ),
                    'default'       => 'yes',
                ),
                'recipient' => array(
                    'title'         => __( 'Recipient(s)', 'web-to-print-online-designer' ),
                    'type'          => 'text',
                    'description'   => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'web-to-print-online-designer' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
                    'placeholder'   => '',
                    'default'       => '',
                    'desc_tip'      => true,
                ),
                'subject' => array(
                    'title'         => __( 'Subject', 'web-to-print-online-designer' ),
                    'type'          => 'text',
                    'desc_tip'      => true,
                    'description'   => sprintf( __( 'Available placeholders: %s', 'web-to-print-online-designer' ), '<code>{site_name},{amount},{user_name}</code>' ),
                    'placeholder'   => $this->get_default_subject(),
                    'default'       => '',
                ),
                'heading' => array(
                    'title'         => __( 'Email heading', 'web-to-print-online-designer' ),
                    'type'          => 'text',
                    'desc_tip'      => true,
                    'description'   => sprintf( __( 'Available placeholders: %s', 'web-to-print-online-designer' ), '<code>{site_name},{amount},{user_name}</code>' ),
                    'placeholder'   => $this->get_default_heading(),
                    'default'       => '',
                ),
                'email_type' => array(
                    'title'         => __( 'Email type', 'web-to-print-online-designer' ),
                    'type'          => 'select',
                    'description'   => __( 'Choose which format of email to send.', 'web-to-print-online-designer' ),
                    'default'       => 'html',
                    'class'         => 'email_type wc-enhanced-select',
                    'options'       => $this->get_email_type_options(),
                    'desc_tip'      => true,
                ),
            );
        }
    }

}

return new NBDL_Withdraw_Request();