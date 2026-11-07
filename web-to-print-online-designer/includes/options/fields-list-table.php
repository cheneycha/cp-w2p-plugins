<?php if (!defined('ABSPATH')) exit;
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
#[\AllowDynamicProperties]
class NBD_Options_List_Table extends WP_List_Table {
    public function __construct() {
        parent::__construct(array(
            'singular'  => __('Printing option', 'web-to-print-online-designer'),
            'plural'    => __('Printing options', 'web-to-print-online-designer'),
            'ajax'      => false 
        ));
    }
    public function prepare_items() {
        $columns    = $this->get_columns();
        $hidden     = array();
        $sortable   = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        /** Process bulk action */
        $this->process_bulk_action();
        $per_page       = $this->get_items_per_page('options_per_page', 10);
        $current_page   = $this->get_pagenum();
        $total_items    = self::record_count();
        $this->set_pagination_args(array(
            'total_items'   => $total_items, 
            'per_page'      => $per_page 
        ));
        $this->items = self::get_options($per_page, $current_page);
    }  
    function get_columns() {
        $columns = array(
            'cb'            => '<input type="checkbox" />',
            'title'         => __('Title', 'web-to-print-online-designer'),
            'published'     => __('Status', 'web-to-print-online-designer'),
            'priority'      => __('Priority', 'web-to-print-online-designer'),
            'apply_for'     => __('Applied for', 'web-to-print-online-designer'),
            'product_ids'   => __('Products', 'web-to-print-online-designer'),
            'product_cats'  => __('Categories', 'web-to-print-online-designer'),
            'date'          => __('Date', 'web-to-print-online-designer')
        );
        return $columns;
    }    
    public function get_sortable_columns() {
        $sortable_columns = array(
            'priority' => array('priority', true)
        );
        return $sortable_columns;
    }
    public static function record_count() {
        global $wpdb;
        $sql = "SELECT COUNT(*) FROM {$wpdb->prefix}nbdesigner_options";
        //filter
        return $wpdb->get_var($sql);
    }
    public function get_options($per_page = 10, $page_number = 1){
        global $wpdb;
        $sql  = "SELECT * FROM {$wpdb->prefix}nbdesigner_options";
        $sql .= " ORDER BY modified DESC LIMIT $per_page";
        $sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;
        $result = $wpdb->get_results($sql, 'ARRAY_A');
        return $result;
    }
    public function process_bulk_action() {
        if ( 'delete' === $this->current_action() ) {
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );
            if ( !wp_verify_nonce( $nonce, 'nbd_options_nonce' ) ) {
                die('Go get a life script kiddies');
            }
            $this->delete_option( absint( $_GET['id'] ) );
            wp_redirect( esc_url_raw( add_query_arg( array( 'paged' => $this->get_pagenum() ), admin_url( 'admin.php?page=nbd_printing_options' ) ) ) );
            exit;
        }
        if ( 'copy' === $this->current_action() ) {
            $nonce = esc_attr( $_REQUEST['_wpnonce'] );
            if ( !wp_verify_nonce( $nonce, 'nbd_options_nonce' ) ) {
                die('Go get a life script kiddies');
            }
            $this->copy_options( absint( $_GET['id'] ) );
            do_action('update_listing_custom');
            wp_redirect( esc_url_raw( admin_url( 'admin.php?page=nbd_printing_options' ) ) );
            exit;
        }
        if ( ( isset($_POST['action']) && $_POST['action'] == 'bulk-publish' ) || ( isset($_POST['action2']) && $_POST['action2'] == 'bulk-publish' ) ) {
            if( isset( $_POST['bulk-delete'] ) ){
                $bulk_ids = esc_sql($_POST['bulk-delete']);
                foreach ( $bulk_ids as $id ) {
                    $this->publish_option( $id );
                }
            }
            wp_redirect( esc_url_raw( add_query_arg( '' , '' ) ) );
        }
        if ( ( isset($_POST['action']) && $_POST['action'] == 'bulk-unpublish' ) || ( isset($_POST['action2']) && $_POST['action2'] == 'bulk-unpublish' ) ) {
            if( isset( $_POST['bulk-delete'] ) ){
                $bulk_ids = esc_sql( $_POST['bulk-delete'] );
                foreach ($bulk_ids as $id) {
                    $this->unpublish_option( $id );
                }
            }
            wp_redirect( esc_url_raw( add_query_arg( '', '' ) ) );
        }
        if ( ( isset($_POST['action']) && $_POST['action'] == 'bulk-delete' ) || ( isset($_POST['action2']) && $_POST['action2'] == 'bulk-delete' ) ) {
            if( isset( $_POST['bulk-delete'] ) ){
                $bulk_ids = esc_sql($_POST['bulk-delete']);
                foreach ($bulk_ids as $id) {
                    $this->delete_option( $id );
                }
            }
            wp_redirect( esc_url_raw( add_query_arg( '', '' ) ) );
        }
    }
    public function delete_option( $id ){
        global $wpdb;
        $sql = "DELETE FROM {$wpdb->prefix}nbdesigner_options";
        $sql .= " WHERE id = " . esc_sql($id);
        $result = $wpdb->query( $sql );
        if( $result ) $this->clear_transients();
    }
    public function unpublish_option( $id ){
        global $wpdb;
        $result = $wpdb->update($wpdb->prefix . 'nbdesigner_options', array(
            'published' => 0
        ), array( 'id' => esc_sql($id))); 
        if( $result ) $this->clear_transients();
    }
    public function publish_option( $id ){
        global $wpdb;
        $result = $wpdb->update($wpdb->prefix . 'nbdesigner_options', array(
            'published' => 1
        ), array( 'id' => esc_sql($id))); 
        if( $result ) $this->clear_transients();
    }
    public function copy_options( $id ){
        global $wpdb;
        $sql    = "SELECT * FROM {$wpdb->prefix}nbdesigner_options";
        $sql   .= " WHERE id = " . esc_sql( $id );
        $result = $wpdb->get_results( $sql, 'ARRAY_A' );
        if( count( $result[0] ) ){
            $res            = $result[0];
            $modified_date  = new DateTime();
            $arr            = array(
                'title'         => $res['title'],
                'published'     => $res['published'],
                'priority'      => $res['priority'],
                'date_from'     => $res['date_from'],
                'date_to'       => $res['date_to'],
                'apply_for'     => $res['apply_for'],
                'product_cats'  => $res['product_cats'],
                'product_ids'   => $res['product_ids'],
                'modified'      => $modified_date->format('Y-m-d H:i:s'),
                'fields'        => $res['fields'],
                'builder'       => $res['builder'],
                'created'       => $modified_date->format('Y-m-d H:i:s'),
                'created_by'    => wp_get_current_user()->ID
            );
            $in_res = $wpdb->insert("{$wpdb->prefix}nbdesigner_options", $arr);
            if( $in_res ){
                $this->clear_transients();
                return $in_res;
            }
        }
        return false;
    }
    private function clear_transients(){
        global $wpdb;
        $sql = "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_nbo_product_%' OR option_name LIKE '_transient_timeout_nbo_product_%'";   
        $wpdb->query( $sql );
    }
    public function get_bulk_actions() {
        $actions = array(
            'bulk-delete'       => __('Delete', 'web-to-print-online-designer'),
            'bulk-publish'      => __('Publish', 'web-to-print-online-designer'),
            'bulk-unpublish'    => __('Unpublish', 'web-to-print-online-designer'),
        );
        return $actions;
    }
    public function no_items() {
        _e( 'No options avaliable.', 'web-to-print-online-designer' );
    }
    function column_title( $item ) {
        $title      = $item['title'];
        $_nonce     = wp_create_nonce('nbd_options_nonce');
        $actions    = array(
            'edit' => sprintf('<a href="?page=%s&action=%s&id=%s&paged=%s&_wpnonce=%s">'.__( 'Edit', 'web-to-print-online-designer' ).'</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['id'] ), $this->get_pagenum(), $_nonce ),
            'copy' => sprintf('<a href="?page=%s&action=%s&id=%s&paged=%s&_wpnonce=%s">'.__( 'Copy', 'web-to-print-online-designer' ).'</a>', esc_attr( $_REQUEST['page'] ), 'copy', absint( $item['id'] ), $this->get_pagenum(), $_nonce )
        );
        return $title . $this->row_actions($actions);
    } 
    function column_published( $item ){
        return $item['published'] == 1 ? __('Publish', 'web-to-print-online-designer') : __('Unpublish', 'web-to-print-online-designer');
    }
    function column_date( $item ){
        return (!empty($item['modified']) && $item['modified'] != '0000-00-00 00:00:00') ? $item['modified'] : $item['created'];
    }
    function column_apply_for( $item ) {
        return '<span class="nbo_color_emerald">' . ($item['apply_for'] == 'p' ? __('Products', 'web-to-print-online-designer') : __('Categories', 'web-to-print-online-designer')) . '</span>';
    }
    function column_product_ids( $item ) {
        if($item['apply_for'] == 'c') return '<span class="nbo_color_pomegranate">'. __('Disabled', 'web-to-print-online-designer') .'</span>';
        $return = __('None', 'web-to-print-online-designer');
        if( !$item['product_ids'] ) return $return;
        $products = unserialize( $item['product_ids'] );
        if( count( $products ) ){
            $links = array();
            foreach ( $products as $pid ) {
                $title      = get_the_title( $pid );
                $links[]    = '<a title="' . esc_attr( $title ) . '" href="' . esc_url( admin_url( 'post.php?action=edit&post=' . $pid ) ) . '" rel="tag">' . $title . '</a>';
            }
            $return = implode( ' , ', $links ); 
        }
        return $return;
    }
    function column_product_cats($item) {
        if($item['apply_for'] == 'p') return '<span class="nbo_color_pomegranate">'. __('Disabled', 'web-to-print-online-designer') .'</span>';
        $return = __('None', 'web-to-print-online-designer');
        if( !$item['product_cats'] ) return $return;
        $cats = unserialize($item['product_cats']);
        if( count($cats) ){
            $links = array();
            foreach ( $cats as $cat_id ) {
                $category   = get_term_by( 'id', $cat_id, 'product_cat' );
                $link       = get_term_link( $category, 'product_cat' );
                if ( !is_wp_error( $link ) ) {
                    $links[] = '<a href="' . esc_url( $link ) . '" rel="tag">' . $category->name . '</a>';
                }
            }
            $sep    = ' , ';
            $return = join( $sep, $links );
        }
        return $return;
    }
    function column_default($item, $column_name){
        return $item[$column_name];
    }
    function column_cb($item) {
        return sprintf( '<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['id'] );
    }
    function extra_tablenav( $which ) {
        
    }
    function save_option(){
        ob_start();
        var_dump($_POST);
        error_log(ob_get_clean());
    }
}