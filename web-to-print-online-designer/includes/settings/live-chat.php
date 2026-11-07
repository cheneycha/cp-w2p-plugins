<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( !class_exists('Nbdesigner_Live_Chat_Settings') ) {
    class Nbdesigner_Live_Chat_Settings{
        public static function get_options() {
            return apply_filters('nbdesigner_live_chat_settings', array(
                'general' => array(
                    array(
                        'title'         => __( 'Enable live chat and design monitor', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_live_chat',
                        'description'   => __('Allow the customer live chat and share real time design with shop admin.', 'web-to-print-online-designer') . sprintf(__( ' View document <a href="%s" target="_blank">here</a>', 'web-to-print-online-designer'), 'https://nbdesigner.cmsmart.net/configure-firebase-for-live-chat-design-monitor/'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Firebase Realtime Database ID', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_firebase_project_id',
                        'description'   => sprintf(__( 'Get a free Firebase application here <a href="%s">here</a>', 'web-to-print-online-designer'), 'https://console.firebase.google.com/'),
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'Firebase API Key', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_firebase_api_key',
                        'description'   => __('API Key of your Firebase application', 'web-to-print-online-designer'),
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'Firebase Private Key', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Private Key of your Firebase application', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_firebase_private_key',
                        'class'         => 'regular-text',
                        'placeholder'   => '',
                        'css'           => 'height: 10em;',
                        'default'       => '',
                        'type'          => 'textarea'
                    ),
                    array(
                        'title'         => __( 'Maximum Connected Guests', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_max_guest',
                        'default'       => '',
                        'description'   => __( 'Leave empty this input to set no limit', 'web-to-print-online-designer'),
                        'type'          => 'text',
                        'css'           => 'width: 85px',
                        'class'         => 'regular-text'
                    )
                ),
                'facebook-chat' => array(
                    array(
                        'title'         => __( 'Enable Facebook Page Messenger in main live chat', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_facebook_page_messenger',
                        'description'   => '',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Facebook Page Messenger URL', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_facebook_page_url',
                        'description'   => '',
                        'default'       => '',
                        'placeholder'   => 'm.me/101388428xxxxxx',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => __( 'Show Facebook Page Chat as separate live chat', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_facebook_page_chat',
                        'description'   => '',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Facebook Page ID', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_facebook_page_id',
                        'description'   => '',
                        'default'       => '',
                        'placeholder'   => '101388428xxxxxx',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'Facebook Page Chat Logged In Greeting', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_facebook_login_greeting',
                        'description'   => '',
                        'default'       => 'Hi! How can we help you?',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'Facebook Page Chat Logged Out Greeting', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_facebook_logout_greeting',
                        'description'   => '',
                        'default'       => 'Goodbye!',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'Facebook Page Chat Theme Color', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_facebook_theme_color',
                        'description'   => '',
                        'default'       => '#404762',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    )
                ),
                'messages' => array(
                    array(
                        'title'         => esc_html__( 'Chat Popup Title', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_popup_title',
                        'description'   => esc_html__( 'This text will appear in the chat popup title.', 'web-to-print-online-designer'),
                        'default'       => 'Team ' . get_bloginfo( 'name' ),
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'Chat Welcome Message', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_welcome_msg',
                        'description'   => esc_html__( 'This text will appear in the chat button.', 'web-to-print-online-designer'),
                        'default'       => 'Ask us anything. We will reply as soon as possible.',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'Chat Greeting', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_greeting',
                        'description'   => esc_html__( 'This text will appear when the chat starts.', 'web-to-print-online-designer'),
                        'default'       => 'Hi 👋👋. How can we help you?',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'Closing Chat Message', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_end_msg',
                        'description'   => esc_html__( 'This text will appear at the end of the chat.', 'web-to-print-online-designer'),
                        'default'       => 'This chat session has ended.',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    )
                ),
                'design-monitor' => array(
                    array(
                        'title'         => __( 'Show design monitor button in live chat', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_design_monitor',
                        'description'   => '',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    )
                ),
                'whatsapp' => array(
                    array(
                        'title'         => __( 'Enable WhatsApp in main live chat', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_whatsapp',
                        'description'   => '',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'WhatsApp phone number', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_whatsapp_phone',
                        'description'   => esc_html__( 'Include country code, remove all plus sign and spaces.', 'web-to-print-online-designer'),
                        'default'       => '',
                        'placeholder'   => '31850013030',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    )
                ),
                'send-mail' => array(
                    array(
                        'title'         => __( 'Allow the customer send email to shop admin', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_send_email',
                        'description'   => '',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Recipients', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_recipient_mails',
                        'description'   => esc_html__( 'Enter recipients (comma separated) for this email. Defaults to', 'web-to-print-online-designer') . '<code>' . get_option( 'admin_email' ) . '</code>',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => __( 'Enable reCAPTCHA in the send mail form', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_recaptcha_live_chat',
                        'description'   => sprintf(__( 'To start using reCAPTCHA V3, you need to sign up for an <a target="_blank" href="%s"> API key pair for your site</a>', 'web-to-print-online-designer'), esc_url( 'https://www.google.com/recaptcha/admin' )),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => __( 'reCAPTCHA site key', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_v3_recaptcha_key',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text'
                    ),
                    array(
                        'title'         => __( 'reCAPTCHA secret key', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_v3_recaptcha_secret_key',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text'
                    )
                ),
                'faqs' => array(
                    array(
                        'title'         => __( 'Show FAQs as helper in live chat', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_helper',
                        'description'   => '',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    )
                ),
                'appearance' => array(
                    array(
                        'title'         => __( 'Hide on mobile devices', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_hide_on_mobile',
                        'description'   => '',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => __( 'Allow use GIPHY gifs in live chat', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_enable_giphy',
                        'description'   => '',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'GIPHY API Key', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_giphy_app_key',
                        'description'   => sprintf(__( 'Use default API key or get your own key <a href="%s" target="_blank">here</a>', 'web-to-print-online-designer'), 'https://developers.giphy.com/docs/api/'),
                        'default'       => 'fEm1RvxSzG7IGQcw5XUZKcjSY6zzl8Ir',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => __( 'Allow use Emoji in live chat', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_enable_emoji',
                        'description'   => '',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Default avatar', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_live_chat_default_avatar',
                        'description'   => '',
                        'default'       => '',
                        'type'          => 'nbd-media',
                        'local'         => false,
                    )
                )
            ));
        }
    }
}