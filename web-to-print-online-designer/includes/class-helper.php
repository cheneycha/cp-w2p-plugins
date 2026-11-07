<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if(!class_exists('Nbdesigner_Helper')){
    class Nbdesigner_Helper{
        public static function settings_helper(){
            $screen = get_current_screen();
            $screen->add_help_tab( array(
                'id'        => 'facebook',
                'title'     => __('Facebook API', 'web-to-print-online-designer'),
                'content'   =>
                    '<h2>'. esc_html__('How to get Facebook API ID', 'web-to-print-online-designer') .'</h3>'
                    . __('<span style="color:#CB4B16;">Application</span> Key and Secret (also sometimes referred as <span style="color:#CB4B16;">Consumer</span> key and secret or <span style="color:#CB4B16;">Client</span> id and secret) are what we call an application credentials.', 'web-to-print-online-designer')
                    . __('This application will link your website ','web-to-print-online-designer').'<code>'.$_SERVER["SERVER_NAME"].'</code> '. __('to <code>Facebook API</code> and these credentials are needed in order for <b>NBDesigner</b> users to use they Facebook photos.','web-to-print-online-designer')
                    .'<br />'. __("These credentials may also differ in format, name and content depending on the social network.", 'web-to-print-online-designer')
                    .'<br /><br />'. __('To enable authentication with this provider and to register a new <b>Facebook API Application</b>, follow the steps', 'web-to-print-online-designer') .':<br />'
                    .'<div style="margin-left:40px;"><p><b>1. </b>'. esc_html__('First go to: ', 'web-to-print-online-designer').'<a href="https://developers.facebook.com/apps">https://developers.facebook.com/apps</a></p>'
                    .'<p><b>2. </b>'. __('Select <b>Add a New App</b> from the <b>Apps</b> menu at the top', 'web-to-print-online-designer') . '</p>'
                    .'<p><b>3. </b>'. __('Fill out Display Name, Namespace, choose a category and click <b>Create App</b>', 'web-to-print-online-designer') .  '</p>'
                    .'<p><b>4. </b>' . __('Go to Settings page and click on <b>Add Platform</b>. Choose website and enter in the new screen your website url in <b>App Domains</b> and <b>Site URL</b> fields', 'web-to-print-online-designer')
                    . esc_html__('They should match with the current hostname', 'web-to-print-online-designer') .' <em style="color:#CB4B16;">'.$_SERVER["SERVER_NAME"]. '</em>.</p>'
                    .'<p><b>5. </b>' . __('Go to the <b>App Review</b> page and choose <b>yes</b> where it says <b>Do you want to make this app and all its live features available to the general public?</b>', 'web-to-print-online-designer')
                    . __('In section "Submit Items for Approval" click <b>Start a Submission</b>, in popup check "user_photos" and complete all steps', 'web-to-print-online-designer') . '</p>'
                    .'<p><b>6. </b>'. __('Go back to the <b>Dashboard</b> page and past the created application credentials (APP ID and Secret) into the boxes above', 'web-to-print-online-designer').'</p>'
                    . '</div><hr /><div><p><b>'. esc_html__("And that's it! ", 'web-to-print-online-designer')
                    . __( 'If for some reason you still can\'t manage to create an application for Facebook, first try to <a href="https://www.google.com/search?q=Facebook API create application" target="_blank">Google it</a>, then check it on <a href="http://www.youtube.com/results?search_query=Facebook API create application " target="_blank">Youtube</a>, and if nothing works <a href="https://cmsmart.net/support_ticket">ask for support</a>', 'web-to-print-online-designer')
                    . '</p></b></div>'
            ));
            $screen->add_help_tab( array(
                'id'        => 'google_drive',
                'title'     => esc_html__('Google Drive API', 'web-to-print-online-designer'),
                'content'   =>
                    '<h2>' . esc_html__('How to get Google Drive API', 'web-to-print-online-designer') . '</h2>' .
                    '<p>'.esc_html__('Go to', 'web-to-print-online-designer').': <a href="https://console.developers.google.com" target="_blank" >Google API Console</a></p>'.
                    '<p><b>'.esc_html__('To get API key', 'web-to-print-online-designer').'</b></p>'.
                    '<p>'.__('Click <b>Create credentials > API key</b>. Next, look for your API key in the <b>API keys</b> section.', 'web-to-print-online-designer') . '</p>'.
                    '<p><b>'.__('To get Client ID', 'web-to-print-online-designer').'</b></p>'.
                    '<p>'.__('Click <b>Create credentials > OAuth client ID</b>. After you have created the credentials, you can see your client ID on the <b>Credentials</b> page.', 'web-to-print-online-designer') . '</p>'                
            ));  
            $screen->add_help_tab( array(
                'id'        => 'setup_wizard',
                'title'     => __('Setup wizard', 'web-to-print-online-designer'),
                'content'   =>
                    '<h2>' . __('Setup wizard', 'web-to-print-online-designer') . '</h2>' .
                    '<p>'.__('If you need to access the setup wizard again, please click on the button below.', 'web-to-print-online-designer') . '</p>'.
                    '<p>'. sprintf(__('<a class="button-primary" href="%s">Setup wizard</a>', 'web-to-print-online-designer'), admin_url( 'index.php?page=nbd-setup' )) . '</p>'                
            ));
            $screen->set_help_sidebar(
                '<p><strong>' . esc_html__('For more information', 'web-to-print-online-designer') . ':</strong></p>' .
                '<p>' . __('<a class="button" href="https://cmsmart.net/support_ticket" target="_blank">Support ticket</a>') . '</p>' .
                '<p>' . __('<a class="button" href="https://cmsmart.net/community/woocommerce-online-product-designer-plugin" target="_blank">Forum</a>') . '</p>'. 
                '<p>' . __('<a class="button" href="https://cmsmart.net/community/woocommerce-online-product-designer-plugin/userguides" target="_blank">User guide</a>') . '</p>' .
                '<p>' . __('<a class="button" href="https://vimeo.com/album/5056824" target="_blank">Video guide</a>') . '</p>' 
            );
        }
        public static function setting_product_helper(){
            //TODO
        }
        public static function clear_transients(){
            if (!wp_verify_nonce($_POST['_nbdesigner_cupdate_product'], 'nbd-clear-transients') || !current_user_can('administrator')) {
                die('Security error');
            } 
            global $wpdb;
            $result = array(
                'flag'    =>  1
            );
            delete_transient( 'nbd_list_products' );
            delete_transient( 'nbd_number_of_products' );
            //delete_transient( 'nbd_design_category' );
            delete_transient( 'nbd_designers' );
            $sql = "DELETE a, b FROM $wpdb->options a, $wpdb->options b
                    WHERE a.option_name LIKE %s
                    AND a.option_name NOT LIKE %s
                    AND b.option_name = CONCAT( '_transient_timeout_nbd_', SUBSTRING( a.option_name, 16 ) )
                    AND b.option_value < %d";
            $rows = $wpdb->query( $wpdb->prepare( $sql, $wpdb->esc_like( '_transient_nbd_' ) . '%', $wpdb->esc_like( '_transient_timeout_nbd_' ) . '%', time() ) );
            $sql = "DELETE a, b FROM $wpdb->options a, $wpdb->options b
                    WHERE a.option_name LIKE %s
                    AND a.option_name NOT LIKE %s
                    AND b.option_name = CONCAT( '_site_transient_timeout_nbd_', SUBSTRING( a.option_name, 21 ) )
                    AND b.option_value < %d";
            $rows2 = $wpdb->query( $wpdb->prepare( $sql, $wpdb->esc_like( '_site_transient_nbd_' ) . '%', $wpdb->esc_like( '_site_transient_timeout_nbd_' ) . '%', time() ) );            
            echo json_encode($result);
            wp_die();
        }
    }
}