<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'NBDL_Withdraw_Cancelled' ) ) {

    #[\AllowDynamicProperties]
    class NBDL_Withdraw_Cancelled  extends WC_Email {

        public function __construct() {
            $this->id               = 'nbdl_email_withdraw_cancelled';
            $this->title            = __( 'NBDesigner Withdraw cancelled', 'web-to-print-online-designer' );
            $this->description      = __( 'These emails are sent to designer when a designer withdraw request is cancelled', 'web-to-print-online-designer' );
            $this->template_html    = 'launcher/emails/withdraw_cancelled.php';
            $this->template_plain   = 'launcher/emails/plain/withdraw_cancelled.php';
            $this->template_base    = NBDESIGNER_PLUGIN_DIR.'/templates/';

            add_action( 'nbdl_withdraw_request_cancelled', array( $this, 'trigger' ), 30, 2 );

            $this->customer_email = true;
            parent::__construct();
        }

        public function get_default_subject() {
            return __( '[{site_name}] Your withdrawal request was cancelled', 'web-to-print-online-designer' );
        }

        public function get_default_heading() {
            return __( 'Withdrawal request for {amount} is cancelled', 'web-to-print-online-designer' );
        }

        public function trigger( $user_id, $withdraw ) {
            if ( ! $this->is_enabled() ) {
                return;
            }
    
            $designer                    = get_user_by( 'id', $user_id );
            $this->object                = $designer;
            $this->find['username']      = '{user_name}';
            $this->find['amount']        = '{amount}';
            $this->find['profile_url']   = '{profile_url}';
            $this->find['withdraw_page'] = '{withdraw_page}';
            $this->find['site_name']     = '{site_name}';
            $this->find['site_url']      = '{site_url}';
            $this->find['note']          = '{note}';
    
            $this->replace['username']      = $designer->user_login;
            $this->replace['amount']        = wc_price( $withdraw->amount );
            $this->replace['profile_url']   = wc_get_endpoint_url( 'my-store', '', wc_get_page_permalink( 'myaccount' ) );
            $this->replace['withdraw_page'] = add_query_arg( array( 'tab' => 'withdraw' ), wc_get_endpoint_url( 'my-store', '', wc_get_page_permalink( 'myaccount' ) ) );
            $this->replace['site_name']     = $this->get_from_name();
            $this->replace['site_url']      = site_url();
            $this->replace['note']          = $withdraw->note;
    
            $this->setup_locale();
            $this->send( $designer->user_email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
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

return new NBDL_Withdraw_Cancelled();