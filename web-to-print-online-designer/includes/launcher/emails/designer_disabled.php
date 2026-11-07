<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( !class_exists( 'NBDL_Designer_Disabled' ) ) {

    #[\AllowDynamicProperties]
    class NBDL_Designer_Disabled extends WC_Email {

        public function __construct() {
            $this->id               = 'nbdl_email_designer_disable';
            $this->title            = __( 'Designer Disable', 'web-to-print-online-designer' );
            $this->description      = __( 'This email is sent to a designer when his/her designer account is deactivated by admin', 'web-to-print-online-designer' );
            $this->template_html    = 'launcher/emails/designer_disabled.php';
            $this->template_plain   = 'launcher/emails/plain/designer_disabled.php';
            $this->template_base    = NBDESIGNER_PLUGIN_DIR.'/templates/';

            add_action( 'nbdl_designer_disabled', array( $this, 'trigger' ) );

            $this->customer_email = true;
            parent::__construct();
        }

        public function get_default_subject() {
            return __( '[{site_name}] Your account is deactivated', 'web-to-print-online-designer' );
        }

        public function get_default_heading() {
            return __( 'Your designer account is deactivated', 'web-to-print-online-designer' );
        }

        public function trigger( $designer_id ) {
            if ( ! $this->is_enabled() ) {
                return;
            }

            $this->setup_locale();

            $designer       = get_user_by( 'ID', $designer_id );
            $designer_email = $designer->user_email;

            $this->find['site_name']        = '{site_name}';
            $this->find['first_name']       = '{first_name}';
            $this->find['last_name']        = '{last_name}';
            $this->find['display_name']     = '{display_name}';

            $this->replace['site_name']     = $this->get_from_name();
            $this->replace['first_name']    = $designer->first_name;
            $this->replace['last_name']     = $designer->last_name;
            $this->replace['display_name']  = $designer->display_name;

            $this->send( $designer_email, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );

            $this->restore_locale();
        }

        public function get_content_html() {
            ob_start();
            wc_get_template( $this->template_html, array(
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => false,
                'email'         => $this,
                'data'          => $this->replace
            ), '', $this->template_base );
            return ob_get_clean();
        }

        public function get_content_plain() {
            ob_start();
            wc_get_template( $this->template_html, array(
                'email_heading' => $this->get_heading(),
                'sent_to_admin' => false,
                'plain_text'    => true,
                'email'         => $this,
                'data'          => $this->replace
            ), '', $this->template_base );
            return ob_get_clean();
        }

        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title'         => __( 'Enable/Disable', 'web-to-print-online-designerr' ),
                    'type'          => 'checkbox',
                    'label'         => __( 'Enable this email notification', 'web-to-print-online-designerr' ),
                    'default'       => 'yes',
                ),

                'subject' => array(
                    'title'         => __( 'Subject', 'web-to-print-online-designerr' ),
                    'type'          => 'text',
                    'desc_tip'      => true,
                    /* translators: %s: list of placeholders */
                    'description'   => sprintf( __( 'Available placeholders: %s', 'web-to-print-online-designerr' ), '<code>{title}, {message}, {site_name}</code>' ),
                    'placeholder'   => $this->get_default_subject(),
                    'default'       => '',
                ),
                'heading' => array(
                    'title'         => __( 'Email heading', 'web-to-print-online-designerr' ),
                    'type'          => 'text',
                    'desc_tip'      => true,
                    /* translators: %s: list of placeholders */
                    'description'   => sprintf( __( 'Available placeholders: %s', 'web-to-print-online-designerr' ), '<code>{title}, {message}, {site_name}</code>' ),
                    'placeholder'   => $this->get_default_heading(),
                    'default'       => '',
                ),
                'email_type' => array(
                    'title'         => __( 'Email type', 'web-to-print-online-designerr' ),
                    'type'          => 'select',
                    'description'   => __( 'Choose which format of email to send.', 'web-to-print-online-designerr' ),
                    'default'       => 'html',
                    'class'         => 'email_type wc-enhanced-select',
                    'options'       => $this->get_email_type_options(),
                    'desc_tip'      => true,
                ),
            );
        }
    }

}

return new NBDL_Designer_Disabled();