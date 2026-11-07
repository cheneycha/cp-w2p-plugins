<?php
if (!defined('ABSPATH')) exit;

class NBDL_Design{
    protected static $instance;

    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init(){
        add_action( 'template_redirect', array( $this, 'handle_designs' ) );
    }

    public function handle_designs(){
        $request_data       = wp_unslash( $_REQUEST );
        $need_redirect      = false;

        if(isset( $request_data['action'] ) && $request_data['action'] == 'nbdl_delete_design' ){
            $this->delete_design();
            $need_redirect = true;
        }
        if( $need_redirect ){
            wp_redirect( add_query_arg( array( 'tab' => 'designs' ), wc_get_endpoint_url( 'my-store', '', wc_get_page_permalink( 'myaccount' ) ) ) );
        }
    }

    public function delete_design(){
        global $wpdb, $current_user;

        if (!wp_verify_nonce($_REQUEST['_wpnonce'], 'nbdl_delete_design') && NBDESIGNER_ENABLE_NONCE) {
            die('Security error');
        }

        $get_data   = wp_unslash( $_GET );
        $row_id     = absint( $get_data['id'] );

        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}nbdesigner_templates WHERE id = %d AND user_id = %d", $row_id, $current_user->ID ) );
    }
}

function NBDL_Design(){
    return NBDL_Design::get_instance();
}
NBDL_Design()->init();