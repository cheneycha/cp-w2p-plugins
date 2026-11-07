<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists('Nbdesigner_Live_Chat') ){

    class Nbdesigner_Live_Chat {
        protected static $instance;
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        public function __construct() {
            //todo
        }
        public function init(){
            /* Settings */
            add_action( 'nbdesigner_include_settings', array( $this, 'include_settings' ) );
            add_filter( 'nbdesigner_settings_tabs', array( $this, 'settings_tabs' ), 30, 1 );
            add_filter( 'nbdesigner_settings_blocks', array( $this, 'settings_blocks' ), 30, 1 );
            add_filter( 'nbdesigner_settings_options', array( $this, 'settings_options' ), 30, 1 );
            add_filter( 'nbdesigner_default_settings', array( $this, 'default_settings' ), 30, 1 );
            add_action( 'init', array( $this, 'add_macro_post_type' ) );

            if( nbdesigner_get_option( 'nbdesigner_enable_live_chat', 'no' ) == 'yes' ){
                $this->ajax();
                add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 40, 1 );
                add_filter( 'nbd_admin_pages', array( $this, 'admin_pages' ), 20, 1 );
                add_action( 'nbd_menu', array( $this, 'add_sub_menu'), 192 );
                add_filter( 'nbc_macros', array( $this, 'nbc_macros' ), 20, 1 );
                add_action( 'init', array( $this, 'init_cookie' ) );
                add_action( 'init', array( $this, 'init_chat_frontend' ) );

                remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
                remove_action( 'wp_print_styles', 'print_emoji_styles' );
            }
        }
        function include_settings(){
            require_once(NBDESIGNER_PLUGIN_DIR . 'includes/settings/live-chat.php');
        }
        public function settings_tabs( $tabs ){
            $tabs['live-chat'] = '<span class="dashicons dashicons-format-chat"></span> '. __('Live chat & Design monitor', 'web-to-print-online-designer');
            return $tabs;
        }
        public function settings_blocks( $blocks ){
            $blocks['live-chat'] = array(
                'livechat-general'      => __('General', 'web-to-print-online-designer'),
                'messages'              => __('Default Messages', 'web-to-print-online-designer'),
                'design-monitor'        => __('Design Monitor', 'web-to-print-online-designer'),
                'facebook-chat'         => __('Facebook Page', 'web-to-print-online-designer'),
                'whatsapp-chat'         => __('WhatsApp', 'web-to-print-online-designer'),
                'send-mail'             => __('Send Email', 'web-to-print-online-designer'),
                'faqs'                  => __('FAQs - Helper', 'web-to-print-online-designer'),
                'livechat-appearance'   => __('Appearance', 'web-to-print-online-designer')
            );
            return $blocks;
        }
        public function settings_options( $options ){
            $live_chat_options              = Nbdesigner_Live_Chat_Settings::get_options();
            $options['livechat-general']    = $live_chat_options['general'];
            $options['messages']            = $live_chat_options['messages'];
            $options['design-monitor']      = $live_chat_options['design-monitor'];
            $options['facebook-chat']       = $live_chat_options['facebook-chat'];
            $options['whatsapp-chat']       = $live_chat_options['whatsapp'];
            $options['send-mail']           = $live_chat_options['send-mail'];
            $options['faqs']                = $live_chat_options['faqs'];
            $options['livechat-appearance'] = $live_chat_options['appearance'];
            return $options;
        }
        public function default_settings( $settings ){
            $settings['nbdesigner_enable_live_chat']                    = 'no';
            $settings['nbdesigner_live_chat_firebase_project_id']       = '';
            $settings['nbdesigner_live_chat_firebase_api_key']          = '';
            $settings['nbdesigner_live_chat_firebase_private_key']      = '';
            $settings['nbdesigner_live_chat_max_guest']                 = '';

            $settings['nbdesigner_live_chat_facebook_page_messenger']   = 'no';
            $settings['nbdesigner_live_chat_facebook_page_url']         = '';
            $settings['nbdesigner_live_chat_facebook_page_chat']        = 'no';
            $settings['nbdesigner_live_chat_facebook_page_id']          = '';
            $settings['nbdesigner_live_chat_facebook_login_greeting']   = esc_html__( 'Hi! How can we help you?', 'web-to-print-online-designer' );
            $settings['nbdesigner_live_chat_facebook_logout_greeting']  = esc_html__( 'Goodbye!', 'web-to-print-online-designer' );
            $settings['nbdesigner_live_chat_facebook_theme_color']      = '#404762';

            $settings['nbdesigner_live_chat_popup_title']               = esc_html__( 'Team ', 'web-to-print-online-designer' ) . get_bloginfo( 'name' );
            $settings['nbdesigner_live_chat_welcome_msg']               = esc_html__( 'Ask us anything. We will reply as soon as possible.', 'web-to-print-online-designer' );
            $settings['nbdesigner_live_chat_greeting']                  = esc_html__( 'Hi 👋👋. How can we help you?', 'web-to-print-online-designer' );
            $settings['nbdesigner_live_chat_end_msg']                   = esc_html__( 'This chat session has ended.', 'web-to-print-online-designer' );

            $settings['nbdesigner_live_chat_design_monitor']            = 'no';

            $settings['nbdesigner_live_chat_send_email']                = 'no';
            $settings['nbdesigner_live_chat_recipient_mails']           = '';
            $settings['nbdesigner_enable_recaptcha_live_chat']          = 'no';
            $settings['nbdesigner_v3_recaptcha_key']                    = '';
            $settings['nbdesigner_v3_recaptcha_secret_key']             = '';

            $settings['nbdesigner_live_chat_helper']                    = 'yes';

            $settings['nbdesigner_live_chat_whatsapp']                  = 'no';
            $settings['nbdesigner_live_chat_whatsapp_phone']            = '';

            $settings['nbdesigner_live_chat_hide_on_mobile']            = 'yes';
            $settings['nbdesigner_live_chat_enable_giphy']              = 'yes';
            $settings['nbdesigner_live_chat_giphy_app_key']             = 'fEm1RvxSzG7IGQcw5XUZKcjSY6zzl8Ir';
            $settings['nbdesigner_live_chat_default_avatar']            = '';

            return $settings;
        }
        public function ajax(){
            $ajax_events = array(
                'nbd_get_user_token'    => true,
                'nblc_send_mail'        => true,
                'nbc_get_macros'        => true,
                'nbc_update_macro'      => true,
                'nbc_delete_macros'     => true
            );
            foreach ( $ajax_events as $ajax_event => $nopriv ) {
                add_action( 'wp_ajax_' . $ajax_event, array( $this, $ajax_event ) );
                if ( $nopriv ) {
                    add_action( 'wp_ajax_nopriv_' . $ajax_event, array( $this, $ajax_event ) );
                }
            }
        }
        public function add_sub_menu() {
            if( current_user_can( 'manage_nbd_tool' ) ){
                add_submenu_page(
                    'nbdesigner', __( 'Live Chat & Design Monitor', 'web-to-print-online-designer'), __( 'Live Chat & Design Monitor', 'web-to-print-online-designer' ), 'manage_nbd_tool', 'nbd_live_chat', array( $this, 'chat_console' )
                );
            }
        }
        public function admin_pages( $pages ){
            $pages[] = 'nbdesigner_page_nbd_live_chat';
            return $pages;
        }
        public function chat_console(){
            include_once( NBDESIGNER_PLUGIN_DIR . 'views/live-chat/admin-console.php' );
        }
        public function admin_enqueue_scripts( $hook ) {
            if( $hook == 'nbdesigner_page_nbd_live_chat' ){
                $suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
                $depend_jss  = array(
                    'select2'     => array(
                        'src'     => WC()->plugin_url() . '/assets/js/select2/select2.full' . $suffix . '.js',
                        'deps'    => array( 'jquery' ),
                        'version' => '4.0.3'
                    )
                );
                $depend_csss = array(
                    'select2' => array(
                        'src'     => WC()->plugin_url() . '/assets/css/select2.css',
                        'deps'    => array(),
                        'version' => WC_VERSION
                    )
                );
                foreach ($depend_jss as $key => $js){
                    if( ! wp_script_is( $key, 'registered' ) ){
                        wp_register_script($key, $js['src'], $js['deps'], $js['version']);
                    }
                    if( ! wp_script_is( $key, 'enqueued' ) ){
                        wp_enqueue_script( $key );
                    }
                }
                foreach ($depend_csss as $key => $css){
                    if( ! wp_style_is( $key, 'registered' ) ){
                        wp_register_style($key, $css['src'], $css['deps'], $css['version']);
                    }
                    if( ! wp_style_is( $key, 'enqueued' ) ){
                        wp_enqueue_style( $key );
                    }
                }

                wp_register_style( 'nbd-perfect_scrollbar-css', NBDESIGNER_CSS_URL . 'perfect-scrollbar.min.css', array(), '0.8.1' );
                wp_enqueue_style( array('nbd-perfect_scrollbar-css') );
                
                wp_register_style( 'nbd-admin-chat-css', NBDESIGNER_CSS_URL . 'admin-live-chat.css', array('dashicons'), NBDESIGNER_VERSION );
                wp_enqueue_style( array('nbd-admin-chat-css') );

                wp_register_script( 'firebase-app', 'https://www.gstatic.com/firebasejs/7.14.5/firebase-app.js', array(), '7.14.5', true );
                wp_register_script( 'firebase-auth', 'https://www.gstatic.com/firebasejs/7.14.5/firebase-auth.js', array( 'firebase-app' ), '7.14.5', true );
                wp_register_script( 'firebase-database', 'https://www.gstatic.com/firebasejs/7.14.5/firebase-database.js', array( 'firebase-app' ), '7.14.5', true );
                wp_register_script( 'nbc_design_bundle', NBDESIGNER_JS_URL . 'bundle-modern.min.js', array( 'jquery' ), NBDESIGNER_VERSION );
                wp_register_script( 'nbc_admin', NBDESIGNER_JS_URL . 'live-chat-admin.js', array( 'nbc_design_bundle' ), NBDESIGNER_VERSION );
                
                $depend_arr = array( 'jquery', 'angularjs', 'firebase-app', 'firebase-auth', 'firebase-database', 'selectWoo', 'nbc_admin' );

                wp_register_script( 'nbd-perfect_scrollbar', NBDESIGNER_ASSETS_URL . 'libs/perfect-scrollbar.min.js', array('jquery'), '0.8.1', true );
                $depend_arr[] = 'nbd-perfect_scrollbar';

                wp_register_script( 'nbd_live_chat', NBDESIGNER_JS_URL . 'live-chat.js', $depend_arr, NBDESIGNER_VERSION );
                wp_localize_script( 'nbc_design_bundle', 'nbd_live_chat', $this->get_js_options( true, false ) );
                wp_enqueue_script( array( 'nbd_live_chat' ) );
            }
        }
        public function frontend_enqueue_scripts(){
            wp_register_style( 'nbd-chat-css', NBDESIGNER_CSS_URL . 'live-chat.css', array('dashicons'), NBDESIGNER_VERSION );
            wp_enqueue_style( array('nbd-chat-css') );

            wp_register_script( 'firebase-app', 'https://www.gstatic.com/firebasejs/7.14.5/firebase-app.js', array(), '7.14.5', true );
            wp_register_script( 'firebase-auth', 'https://www.gstatic.com/firebasejs/7.14.5/firebase-auth.js', array( 'firebase-app' ), '7.14.5', true );
            wp_register_script( 'firebase-database', 'https://www.gstatic.com/firebasejs/7.14.5/firebase-database.js', array( 'firebase-app' ), '7.14.5', true );

            $depend_arr         = array( 'jquery', 'angularjs', 'firebase-app', 'firebase-auth', 'firebase-database' );

            wp_register_script( 'nbd-perfect_scrollbar', NBDESIGNER_ASSETS_URL . 'libs/perfect-scrollbar.min.js', array('jquery'), '0.8.1', true );
            $depend_arr[] = 'nbd-perfect_scrollbar';

            wp_register_script( 'nbd_live_chat', NBDESIGNER_JS_URL . 'live-chat.js', $depend_arr, NBDESIGNER_VERSION );
            wp_localize_script( 'nbd_live_chat', 'nbd_live_chat', $this->get_js_options( false, false ) );

            wp_register_style( 'nbd-perfect_scrollbar-css', NBDESIGNER_CSS_URL . 'perfect-scrollbar.min.css', array(), '0.8.1' );
            wp_enqueue_style( array('nbd-perfect_scrollbar-css') );
            wp_enqueue_script( array( 'nbd_live_chat' ) );
        }
        public function get_i18n_javascript(){
            $lang = array(
                'chat'                          => esc_html__( 'chat', 'web-to-print-online-designer' ),
                'new_message_from'              => esc_html__( 'New message from:', 'web-to-print-online-designer' ),
                'apply_macro'                   => esc_html__( 'Apply macro', 'web-to-print-online-designer' ),
                'customer_stop_share_desgin'    => esc_html__( 'Customer stop share the desgin!', 'web-to-print-online-designer' ),
                'frequently_used'               => esc_html__( 'Frequently used', 'web-to-print-online-designer' ),
                'greeting'                      => stripslashes( nbdesigner_get_option( 'nbdesigner_live_chat_greeting', 'Hi 👋👋. How can we help you?' ) ),
                'confirm_delete_macro'          => esc_html__( 'Are you sure you want to delete selected macros.', 'web-to-print-online-designer' )
            );
            return $lang;
        }
        public function get_js_options( $is_admin = false, $in_editor = false ){
            return array(
                'is_admin'              => $is_admin,
                'in_editor'             => $in_editor,
                'ajax_url'              => admin_url( 'admin-ajax.php' ),
                'nonce'                 => wp_create_nonce('nbd_live_chat'),
                'project_id'            => nbdesigner_get_option( 'nbdesigner_live_chat_firebase_project_id', '' ),
                'api_key'               => nbdesigner_get_option( 'nbdesigner_live_chat_firebase_api_key', '' ),
                'enable_giphy'          => nbdesigner_get_option( 'nbdesigner_live_chat_enable_giphy', 'yes' ),
                'enable_emoji'          => nbdesigner_get_option( 'nbdesigner_live_chat_enable_emoji', 'yes' ),
                'giphy_app_key'         => nbdesigner_get_option( 'nbdesigner_live_chat_giphy_app_key', 'fEm1RvxSzG7IGQcw5XUZKcjSY6zzl8Ir' ),
                'max_guest'             => nbdesigner_get_option( 'nbdesigner_live_chat_max_guest', '' ),
                'assets_url'            => NBDESIGNER_ASSETS_URL,
                'font_url'              => NBDESIGNER_FONT_URL,
                'default_avatar'        => $this->get_default_avatar(),
                'langs'                 => $this->get_i18n_javascript(),
                'user'                  => $this->get_user_data()
            );
        }
        public function get_default_avatar(){
            $default_avatar         = nbdesigner_get_option( 'nbdesigner_live_chat_default_avatar', '' );

            if( $default_avatar ){
                return wp_get_attachment_url( $default_avatar );
            }

            return NBDESIGNER_ASSETS_URL . 'images/avatar.png';
        }
        public function nbd_get_user_token(){
            if (!wp_verify_nonce($_POST['nonce'], 'nbd_live_chat') && NBDESIGNER_ENABLE_NONCE) {
                die('Security error');
            }

            $result = array(
                'flag'  => 1
            );

            if( !$this->is_valid_setting() ){
                $result['flag']    = 0;
                wp_send_json( $result );
            }

            $token              = $this->user_auth();
            $result['token']    = $token;
            $result['user_id']  = $this->get_user_id();

            wp_send_json( $result );
        }
        public function is_valid_setting(){
            $result         = true;
            $project_id     = nbdesigner_get_option( 'nbdesigner_live_chat_firebase_project_id', '' );
            $api_key        = nbdesigner_get_option( 'nbdesigner_live_chat_firebase_api_key', '' );
            $private_key    = nbdesigner_get_option( 'nbdesigner_live_chat_firebase_private_key', '' );

            if( $project_id == '' || $api_key == '' ||  $private_key == '' ){
                $result = false;
            }else{
                $private_key_decode = json_decode( stripslashes( $private_key ) );
                if( !isset( $private_key_decode->client_email ) || !isset( $private_key_decode->private_key ) ){
                    $result = false;
                }
            }

            return $result;
        }
        public function user_auth(){
            if ( ! class_exists( 'JWT' ) ) {
                require_once NBDESIGNER_PLUGIN_DIR . 'lib/jwt.php';
            }

            $now_seconds    = time();
            $user_id        = $this->get_user_id();
            $is_admin       = current_user_can( 'manage_options' ) ? true : false;
            $is_mod         = ( current_user_can( 'shop_manager' ) || current_user_can( 'manage_options' ) ) ? true : false;

            $private_key    = json_decode( stripslashes( nbdesigner_get_option( 'nbdesigner_live_chat_firebase_private_key', '' ) ) );
            $credentials    = array(
                'service_account' => $private_key->client_email,
                'private_key'     => $private_key->private_key
            );
            
            $payload  = array(
                'iss'    => $credentials['service_account'],
                'sub'    => $credentials['service_account'],
                'aud'    => 'https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit',
                'iat'    => $now_seconds,
                'exp'    => $now_seconds + ( 60 * 60 ),
                'uid'    => $user_id,
                'claims' => array(
                    'admin' => $is_admin,
                    'mod'   => $is_mod,
                    'uid'   => $user_id
                ),
            );

            $key      = $credentials['private_key'];
            $encoding = 'RS256';

            return JWT::encode( $payload, $key, $encoding );
        }
        public function init_chat_frontend(){
            $is_mobile      = wp_is_mobile();
            $hide_on_mobile = nbdesigner_get_option( 'nbdesigner_live_chat_hide_on_mobile', 'yes' );

            if( !$is_mobile || ( $is_mobile && $hide_on_mobile == 'no' ) ){
                add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue_scripts' ) );
                add_action( 'wp_footer', array( $this, 'frontend_chat_wrap' ) );

                if( !$is_mobile ){
                    add_action( 'nbd_extra_css', array( $this, 'nbc_editor_css'), 20, 1 );
                    add_action( 'nbd_extra_js', array( $this, 'nbc_editor_js'), 20, 1 );
                    add_action( 'nbd_editor_extra_section', array( $this, 'nbc_editor_section'), 20, 1 );
                    add_action( 'nbd_js_config', array( $this, 'nbc_js_config' ) );
                }
            }
        }
        public function init_cookie(){
            if( !is_user_logged_in() ){
                $user_id = isset( $_COOKIE['nbc_user_session'] ) ? $_COOKIE['nbc_user_session'] : '';
                if ( empty( $user_id ) ) {
                    $user_id = uniqid( rand(), false );
                    @setcookie( 'nbc_user_session', $user_id, time() + ( 3600 * 24 ), '/' );
                }
            }
        }
        public function get_user_id(){
            if( is_user_logged_in() ){
                $current_user   = wp_get_current_user();
                $user_id        = 'user-' . $current_user->ID;
            }else{
                $user_id = isset( $_COOKIE['nbc_user_session'] ) ? $_COOKIE['nbc_user_session'] : '';
            }
            return $user_id;
        }
        public function get_user_data(){
            $display_name   = '';
            $user_email     = '';
            $logged         = false;
            $user_id        = '';

            if( is_user_logged_in() ){
                $current_user = wp_get_current_user();
                $display_name = $current_user->display_name;
                $user_email   = $current_user->user_email;
                $logged       = true;
                $user_id      = $current_user->ID;
            }

            return array(
                'id'            => $user_id,
                'name'          => $display_name,
                'email'         => $user_email,
                'is_mod'        => ( current_user_can( 'shop_manager' ) || current_user_can( 'manage_options' ) ) ? 1 : 0,
                'logged'        => $logged,
                'avatar'        => $logged ? get_avatar_url( $user_email, 96 ) : '',
                'ip'            => nbd_get_client_ip(),
                'current_page'  => nbd_get_current_page()
            );
        }
        public function frontend_chat_wrap( $in_editor = null ){
            ob_start();

            $enable_fb = false;
            if( nbdesigner_get_option( 'nbdesigner_live_chat_facebook_page_chat', 'no' ) == 'yes' ){
                $nbc_fb_page_id                 = nbdesigner_get_option( 'nbdesigner_live_chat_facebook_page_id', '' );
                $nbc_fb_page_login_greeting     = nbdesigner_get_option( 'nbdesigner_live_chat_facebook_login_greeting', esc_html__( 'Hi! How can we help you?', 'web-to-print-online-designer' ) );
                $nbc_fb_page_logout_greeting    = nbdesigner_get_option( 'nbdesigner_live_chat_facebook_logout_greeting', esc_html__( 'Goodbye!', 'web-to-print-online-designer' ) );
                $nbc_fb_page_theme_color        = nbdesigner_get_option( 'nbdesigner_live_chat_facebook_theme_color', '#404762' );

                if( $nbc_fb_page_id != '' ){
                    include_once( NBDESIGNER_PLUGIN_DIR . 'views/live-chat/frontend/facebook-chat.php' );
                    $enable_fb = true;
                }
            }

            if( $this->is_valid_setting() ){
                $show_fb_msg        = nbdesigner_get_option( 'nbdesigner_live_chat_facebook_page_messenger', 'no' );
                $fb_page_url        = nbdesigner_get_option( 'nbdesigner_live_chat_facebook_page_url', 'no' );
                $show_fb_msg        = $fb_page_url != '' ? $show_fb_msg : 'no';

                $show_whatsapp_msg  = nbdesigner_get_option( 'nbdesigner_live_chat_whatsapp', 'no' );
                $whatsapp_phone     = nbdesigner_get_option( 'nbdesigner_live_chat_whatsapp_phone', '' );
                $show_whatsapp_msg  = $whatsapp_phone != '' ? $show_whatsapp_msg : 'no';

                $show_send_mail     = nbdesigner_get_option( 'nbdesigner_live_chat_send_email', 'no' );

                include_once( NBDESIGNER_PLUGIN_DIR . 'views/live-chat/frontend/wrap.php' );
            }

            $content = ob_get_clean();
            echo $content;
        }
        public function nblc_send_mail(){
            if (!wp_verify_nonce($_POST['nonce'], 'nbd_live_chat') && NBDESIGNER_ENABLE_NONCE) {
                die('Security error');
            }
            global $woocommerce;

            $name       = wc_clean( $_POST['name'] );
            $email      = filter_var( $_POST['email'], FILTER_SANITIZE_EMAIL );
            $message    = wc_clean( $_POST['message'] );
            $result     = array(
                'flag'  => 1
            );

            if( isset( $_POST['token'] ) ){
                $token              = wc_clean( $_POST['token'] );
                $recaptcha_key      = nbdesigner_get_option( 'nbdesigner_v3_recaptcha_key', '' );
                $recaptcha_secret   = nbdesigner_get_option( 'nbdesigner_v3_recaptcha_secret_key', '' );
                if( nbdesigner_get_option( 'nbdesigner_enable_recaptcha_live_chat', 'no' ) == 'yes' && $recaptcha_key != '' && $recaptcha_secret != '' ){
                    $error      = false;
                    if ( ! $token ) {
                        $error  = true;
                    }else {
                        $response   = wp_remote_get( "https://www.google.com/recaptcha/api/siteverify?secret=" . $recaptcha_secret . "&response=" . $token );
                        if ( is_wp_error( $response ) || ! isset( $response['body'] ) ) {
                            $error  = true;
                        } else {
                            $responseKeys = json_decode( $response['body'], true );
                            if ( intval( $responseKeys["success"] ) !== 1 ) {
                                $error  = true;
                            }
                        }
                    }

                    if( $error ){
                        $result['flag'] = 0;
                        wp_send_json( $result );
                    }
                }
            }

            if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                $mailer = $woocommerce->mailer();

                ob_start();
                wc_get_template( 'emails/nbc-chat-offline-message.php', array(
                    'email'     => $email,
                    'name'      => $name,
                    'message'   => $message
                ) );
                $body   = ob_get_clean();

                $recipient_mails    = nbdesigner_get_option( 'nbdesigner_live_chat_recipient_mails', '' );
                if( $recipient_mails == '' ){
                    $emails         = new WC_Emails();
                    $woo_recipient  = $emails->emails['WC_Email_New_Order']->recipient;

                    if( !empty( $woo_recipient ) ) {
                        $recipient_mails = esc_attr( $woo_recipient );
                    } else {
                        $recipient_mails = get_option( 'admin_email' );
                    }
                }

                $subject    = esc_html__( 'The customer message', 'web-to-print-online-designer' );
                $mailer->send( $recipient_mails, $subject, $body );
            }else{
                $result['flag'] = 0;
            }

            wp_send_json( $result );
        }
        public function nbc_editor_css( $ui_mode ){
            if( $ui_mode == 2 ){
            ?>
            <link type="text/css" href="<?php echo NBDESIGNER_PLUGIN_URL .'assets/css/live-chat.css'; ?>" rel="stylesheet" media="all">
            <?php
            }
        }
        public function nbc_editor_js( $ui_mode ){
            if( $ui_mode == 2 ){
                $nbd_live_chat = $this->get_js_options( false, true );
            ?>
            <script type='text/javascript' src="https://www.gstatic.com/firebasejs/7.14.5/firebase-app.js"></script>
            <script type='text/javascript' src="https://www.gstatic.com/firebasejs/7.14.5/firebase-auth.js"></script>
            <script type='text/javascript' src="https://www.gstatic.com/firebasejs/7.14.5/firebase-database.js"></script>
            <script type="text/javascript" src="<?php echo NBDESIGNER_ASSETS_URL .'libs/perfect-scrollbar.min.js'; ?>"></script>
            <script type="text/javascript">
                var nbd_live_chat = <?php echo json_encode( $nbd_live_chat ); ?>;
            </script>
            <script type="text/javascript" src="<?php echo NBDESIGNER_JS_URL .'live-chat.js'; ?>"></script>
            <?php
            }
        }
        public function nbc_js_config(){
            ?>
            NBDESIGNCONFIG.enable_live_chat = true;
            <?php
        }
        public function nbc_editor_section( $ui_mode ){
            if( $ui_mode == 2 ) $this->frontend_chat_wrap( true );
        }
        public function add_macro_post_type(){
            $labels = array(
                'name'               => _x( 'Chat Macros', 'Post Type General Name', 'web-to-print-online-designer' ),
                'singular_name'      => _x( 'Chat Macro', 'Post Type Singular Name', 'web-to-print-online-designer' )
            );

            $args = array(
                'labels'              => $labels,
                'supports'            => array( 'title', 'editor' ),
                'public'              => false,
                'show_ui'             => false,
                'show_in_menu'        => false,
                'show_in_nav_menus'   => false,
                'show_in_admin_bar'   => false,
                'exclude_from_search' => true,
                'rewrite'             => false,
                'publicly_queryable'  => false,
                'query_var'           => false
            );

            register_post_type( 'nbc-macro', $args );
        }
        public function nbc_get_macros( $return = false ){
            if ( $return == false && !wp_verify_nonce($_POST['nonce'], 'nbd_live_chat') && NBDESIGNER_ENABLE_NONCE) {
                die('Security error');
            }

            $result     = array(
                'flag'  => 1,
                'list'  => array()
            );

            $posts = get_posts( array(
                'post_type'         => 'nbc-macro',
                'orderby'           => 'ID',
                'order'             => 'DESC',
                'posts_per_page'    => -1,
            ) );

            if ( $posts ) {
                foreach( $posts as $post ){
                    $result['list'][] = array(
                        'id'        => $post->ID,
                        'title'     => $post->post_title,
                        'content'   => $post->post_content
                    );
                }
            }

            if( $return ){
                return $result['list'];
            }else{
                wp_send_json( $result );
            }
        }
        public function nbc_update_macro(){
            if (!wp_verify_nonce($_POST['nonce'], 'nbd_live_chat') && NBDESIGNER_ENABLE_NONCE) {
                die('Security error');
            }
            $user_id = get_current_user_id();

            $id         = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
            $title      = $_POST['title'];
            $content    = $_POST['content'];

            $result     = array(
                'flag'      => 1
            );

            $new_post = array(
                'ID'            => $id,
                'post_title'    => $title,
                'post_content'  => $content,
                'post_status'   => 'publish',
                'post_date'     => date('Y-m-d H:i:s'),
                'post_author'   => $user_id,
                'post_type'     => 'nbc-macro'
            );
            $post_id = wp_insert_post( $new_post );

            if( is_wp_error( $post_id ) ){
                $result['flag'] = 0;
            }else{
                $result['id']  = $post_id;
                $result['list']     = $this->nbc_get_macros( true );
            }

            wp_send_json( $result );
        }
        public function nbc_delete_macros(){
            if (!wp_verify_nonce($_POST['nonce'], 'nbd_live_chat') && NBDESIGNER_ENABLE_NONCE) {
                die('Security error');
            }

            $result     = array(
                'flag'      => 1
            );

            $ids = is_array( $_POST['ids'] ) ? $_POST['ids'] : array();
            foreach( $ids as $id ){
                wp_delete_post( $id, true );
            }

            $result['list']     = $this->nbc_get_macros( true );
            wp_send_json( $result );
        }
        public function nbc_macros( $macro_html ){
            $macros = $this->nbc_get_macros( true );
            foreach( $macros as $macro ){
                $macro_html .= '<option value="'. esc_attr__( $macro['content'] ) .'">'. esc_html__( $macro['content'] ) .'</option>';
            }
            return $macro_html;
        }
    }
}
$nbd_live_chat = Nbdesigner_Live_Chat::instance();
$nbd_live_chat->init();