<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if( !class_exists('Nbdesigner_FAQ') ){

    class Nbdesigner_FAQ {
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
            $this->ajax();

            add_action( 'init', array( $this, 'register_post_type' ) );
            add_filter( 'parent_file', array( $this, 'set_current_menu' ) );
            add_filter( 'nbd_admin_pages', array( $this, 'admin_pages' ), 20, 1 );
            add_action( 'nbd_menu', array( $this, 'add_sub_menu'), 193 );
            add_action( 'admin_head', array( $this, 'custom_css' ) );
            add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_faqs' ) );
            add_filter( 'manage_nbd_faq_posts_columns', array( $this, 'posts_columns' ) );
            add_action( 'manage_nbd_faq_posts_custom_column', array( $this, 'posts_custom_column' ), 10, 2 );
            add_filter( 'manage_edit-nbd_faq_sortable_columns', array( $this, 'sortable_columns' ) );
            add_filter( 'parse_query', array( $this, 'parse_query' ) );
            add_filter( 'posts_clauses', array( $this, 'posts_clauses' ), 10, 2 );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 50, 1 );

            if( nbdesigner_get_option( 'nbdesigner_live_chat_helper', 'yes' ) == 'yes' ){
                add_action( 'nbc_extra_nav', array( $this, 'nbc_extra_nav'), 10, 1 );
                add_action( 'nbc_extra_panel', array( $this, 'nbc_extra_panel'), 10, 1 );
            }

            add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
            add_action( 'save_post', array( $this, 'save_settings' ) );

            add_action( 'nbo_options_meta_box_tabs', array( $this, 'faq_tab' ) );
            add_action( 'nbo_options_meta_box_panels', array( $this, 'faq_panel' ) );
            add_action( 'nbo_save_options', array( $this, 'save_faq_settings' ), 30, 1 );

            add_filter( 'woocommerce_product_tabs', array( $this, 'product_faq_tab' ) );
        }
        public function ajax(){
            $ajax_events = array(
                'nbf_get_live_chat_helper'      => true,
                'nbf_get_faq_content'           => true,
                'nbf_get_faqs_of_category'      => true,
                'nbf_update_live_chat_helper'   => true,
                'nbf_vote_faq'                  => true
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
                    'nbdesigner', __( 'Printing FAQs', 'web-to-print-online-designer'), __( 'Printing FAQs', 'web-to-print-online-designer' ), 'manage_nbd_tool', 'nbd_faq', array( $this, 'faqs_manage' )
                );
                add_submenu_page(
                    'nbdesigner', esc_html__('Printing FAQs', 'web-to-print-online-designer'), esc_html__('Printing FAQs', 'web-to-print-online-designer'), 'manage_nbd_tool', 'edit.php?post_type=nbd_faq', null
                );
            }
        }
        public function admin_pages( $pages ){
            $pages[] = 'nbdesigner_page_nbd_faq';
            return $pages;
        }
        public function faqs_manage(){
            $faqs           = get_posts( array( "numberposts" => -1, "post_type" => 'nbd_faq' ) );
            $new_faqs       = get_posts( array( "numberposts" => 10, "post_type" => 'nbd_faq', 'orderby' => 'ID', 'order' => 'DESC' ) );
            $categories     = get_terms( array( 'taxonomy' => 'nbd_faq_category' ) );
            $selected_faqs  = array();
            $live_chat_faqs = get_option( 'nbd_live_chat_faqs' );
            if( $live_chat_faqs ){
                $_faqs = unserialize( $live_chat_faqs );

                foreach( $_faqs as $_faq ){
                    $arr    = explode( '_', $_faq );
                    $exist  = true;
                    $faq    = array(
                        'id'        => $arr[1],
                        'type'      => $arr[0],
                        'type_name' => $arr[0] == 'cat' ? __( 'Category', 'web-to-print-online-designer' ) : __( 'FAQ', 'web-to-print-online-designer' )
                    );
                    if( $arr[0] == 'cat' ){
                        $url            = 'term.php?taxonomy=nbd_faq_category&amp;post_type=nbd_faq&amp;tag_ID=' . $arr[1] . '&amp;wp_http_referer=' . urlencode( admin_url( 'edit-tags.php?taxonomy=nbd_faq_category&post_type=nbd_faq' ) );
                        $faq['url']     = admin_url( $url );
                        $term           = get_term( $arr[1] );
                        if( $term ){
                            $faq['name']    = $term->name;
                        }else{
                            $exist  = false;
                        }
                    }else{
                        $faq['url']     = admin_url( 'post.php?post=' . $arr[1] . '&action=edit' );
                        $post           = get_post( $arr[1] );
                        if( $post ){
                            $faq['name']    = $post->post_title;
                        }else{
                            $exist  = false;
                        }
                    }
                    if( $exist ) $selected_faqs[] = $faq;
                }
            }
            include_once( NBDESIGNER_PLUGIN_DIR . 'views/faq/dashboard.php' );
        }
        public function set_current_menu( $parent_file ){
            global $submenu_file, $current_screen, $pagenow;
            if ( $current_screen->post_type == 'nbd_faq' ) {
                $submenu_file   = 'nbd_faq';
                $parent_file    = 'nbdesigner';
            }
            return $parent_file;
        }
        public function custom_css(){
            ?>
            <style>
                #adminmenu li a[href="edit.php?post_type=nbd_faq"] {
                    display: none !important;
                }
            </style>
            <?php
        }
        public function admin_enqueue_scripts( $hook ) {
            if( $hook == 'nbdesigner_page_nbd_faq' ){
                wp_enqueue_script( array( 'jquery-ui-sortable' ) );
                wp_enqueue_style( array( 'admin_nbdesigner' ) );
            }
        }
        public function register_post_type(){
            $labels = array(
                'name'                  => __( 'FAQs', 'web-to-print-online-designer' ),
                'singular_name'         => __( 'FAQ', 'web-to-print-online-designer' ),
                'menu_name'             => __( 'FAQs', 'web-to-print-online-designer' ),
                'add_new_item'          => __( 'Add New FAQ', 'web-to-print-online-designer' ),
                'edit_item'             => __( 'Edit FAQ', 'web-to-print-online-designer' ),
                'new_item'              => __( 'New FAQ', 'web-to-print-online-designer' ),
                'view_item'             => __( 'View FAQ', 'web-to-print-online-designer' ),
                'search_items'          => __( 'Search FAQs', 'web-to-print-online-designer' ),
                'parent_item_colon'     => ''
            );

            $args = array(
                'labels'                => $labels,
                'public'                => false,
                'show_ui'               => true,
                'show_in_menu'          => false,
                'show_in_nav_menus'     => false,
                'show_in_admin_bar'     => false,
                'query_var'             => false,
                'publicly_queryable'    => false,
                'has_archive'           => true,
                'exclude_from_search'   => true,
                'rewrite'               => false,
                'capability_type'       => 'post',
                'supports'              => array( 'title','editor' ),
                'show_in_rest'          => false
            ); 

            register_post_type( 'nbd_faq' , $args );

            register_taxonomy('nbd_faq_category', 'nbd_faq', array(
                'hierarchical'  => true,
                'public'        => false,
                'rewrite'       => false,
                'query_var'     => true,
                'show_ui'       => true,
                'show_in_menu'  => false,
                'show_in_rest'  => false,
                'show_tagcloud' => false,
                'labels'        => array(
                    'name'              => __( 'FAQ Categories', 'web-to-print-online-designer' ),
                    'singular_name'     => __( 'FAQ Category', 'web-to-print-online-designer' ),
                    'search_items'      => __( 'Search FAQ Categories', 'web-to-print-online-designer' ),
                    'all_items'         => __( 'All FAQ Categories', 'web-to-print-online-designer' ),
                    'parent_item'       => __( 'Parent FAQ Category', 'web-to-print-online-designer' ),
                    'parent_item_colon' => __( 'Parent FAQ Category:', 'web-to-print-online-designer' ),
                    'edit_item'         => __( 'Edit FAQ Category', 'web-to-print-online-designer' ),
                    'update_item'       => __( 'Update FAQ Category', 'web-to-print-online-designer' ),
                    'add_new_item'      => __( 'Add New FAQ Category', 'web-to-print-online-designer' ),
                    'new_item_name'     => __( 'New FAQ Category Name', 'web-to-print-online-designer' ),
                    'menu_name'         => __( 'FAQ Categories', 'web-to-print-online-designer' )
                )
            ) );
        }
        public function restrict_manage_faqs(){
            global $typenow;
            global $wp_query;
            if ( $typenow == 'nbd_faq' ) {
                $taxonomy       = 'nbd_faq_category';
                $faq_taxonomy   = get_taxonomy( $taxonomy );
                $selected = isset( $wp_query->query['nbd_faq_category'] ) ? $wp_query->query['nbd_faq_category'] : '';
                wp_dropdown_categories( array(
                    'show_option_all' => __( "Show All {$faq_taxonomy->label}" ),
                    'taxonomy'        => $taxonomy,
                    'name'            => 'nbd_faq_category',
                    'orderby'         => 'name',
                    'selected'        => $selected,
                    'hierarchical'    => true,
                    'show_count'      => true,
                    'hide_empty'      => true
                ));
            }
        }
        public function posts_columns( $defaults ){
            unset( $defaults['date'] );
            $defaults['nbf_categories'] = __( 'Categories' );
            $defaults['date']           = __( 'Date' );
            return $defaults;
        }
        public function sortable_columns( $column ){
            $column['nbf_categories'] = 'nbf_categories';
            return $column;
        }
        public function posts_custom_column( $column_name, $post_ID ){
            if ( $column_name == 'nbf_categories' ) {
                echo get_the_term_list( $post_ID, 'nbd_faq_category', '', ', ', '' ) . PHP_EOL;;
            }
        }
        public function parse_query( $query ){
            global $pagenow;
            $post_type  = 'nbd_faq';
            $taxonomy   = 'nbd_faq_category';
            $q_vars     = $query->query_vars;

            if( $pagenow == 'edit.php' && isset( $q_vars['post_type'] ) && $q_vars['post_type'] == $post_type 
                && isset( $q_vars[$taxonomy] ) && is_numeric( $q_vars[$taxonomy] ) && $q_vars[$taxonomy] != 0 ) {
                $term   = get_term_by( 'id', $q_vars[$taxonomy], $taxonomy );
                $query->query_vars[$taxonomy]   = $term->slug;
            }
            return $query;
        }
        public function posts_clauses( $clauses, $wp_query ){
            global $wpdb;
            if( isset( $wp_query->query['orderby'] ) && $wp_query->query['orderby'] == 'nbf_categories' ){
                $clauses['join'] .= <<<SQL
LEFT OUTER JOIN {$wpdb->term_relationships} ON {$wpdb->posts}.ID={$wpdb->term_relationships}.object_id
LEFT OUTER JOIN {$wpdb->term_taxonomy} USING (term_taxonomy_id)
LEFT OUTER JOIN {$wpdb->terms} USING (term_id)
SQL;
                $clauses['where']  .= "AND (taxonomy = 'nbd_faq_category' OR taxonomy IS NULL)";
                $clauses['groupby'] = "object_id";
                $clauses['orderby'] = "GROUP_CONCAT({$wpdb->terms}.name ORDER BY name ASC)";
                if( strtoupper( $wp_query->get( 'order' ) ) == 'ASC'){
                    $clauses['orderby'] .= 'ASC';
                } else {
                    $clauses['orderby'] .= 'DESC';
                }
            }
            return $clauses;
        }
        public function add_meta_boxes(){
            add_meta_box('nbdf_setting', esc_html__('Settings', 'web-to-print-online-designer'), array( $this, 'extra_setting' ), 'nbd_faq', 'normal', 'high');
        }
        public function extra_setting(){
            $post_id    = get_the_ID();
            $_nbf       = get_post_meta( $post_id, '_nbf', true );
            if( $_nbf ){
                $nbf = unserialize( $_nbf );
            }else{
                $nbf = array(
                    'up_vote'   => '',
                    'down_vote' => ''
                );
            }
            include_once( NBDESIGNER_PLUGIN_DIR . 'views/faq/metabox.php' );
        }
        public function save_settings( $post_id ){
            if ( !isset( $_POST['nbf_setting_box_nonce'] ) || !wp_verify_nonce( $_POST['nbf_setting_box_nonce'], 'nbf_setting_box' )
                || !( current_user_can( 'administrator' ) || current_user_can( 'shop_manager' ) ) ) {
                return $post_id;
            }
            if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
                return $post_id;
            }
            if ( 'page' == $_POST['post_type'] ) {
                if ( !current_user_can( 'edit_page', $post_id ) ) {
                    return $post_id;
                }
            } else {
                if ( !current_user_can( 'edit_post', $post_id ) ) {
                    return $post_id;
                }
            }

            $nbf = serialize( $_POST['_nbf'] );
            update_post_meta( $post_id, '_nbf', $nbf );
        }
        public function faq_tab(){
            ?>
            <li><a href="#nbd-faq"><span class="dashicons dashicons-format-chat"></span> <?php _e('Printing FAQ', 'web-to-print-online-designer'); ?></a></li>
            <?php
        }
        public function faq_panel(){
            $post_id    = get_the_ID();
            $nbd_faq    = unserialize( get_post_meta( $post_id, '_nbd_faq', true ) );
            if( !$nbd_faq ){
                $nbd_faq = array(
                    'enable'    => 0,
                    'faqs'      => array()
                );
            }
            if( !isset( $nbd_faq['faqs'] ) ) $nbd_faq['faqs'] = array();

            $faqs           = get_posts( array( "numberposts" => -1, "post_type" => 'nbd_faq', 'post_status'=> 'publish' ) );
            $categories     = get_terms( array( 'taxonomy' => 'nbd_faq_category' ) );
            $selected_faqs  = array();

            foreach( $nbd_faq['faqs'] as $_faq ){
                $exist          = true;
                $post           = get_post( $_faq );
                $faq            = array(
                    'id'    => $_faq,
                    'url'   => admin_url( 'post.php?post=' . $_faq . '&action=edit' )
                );
                if( $post ){
                    $faq['name']    = $post->post_title;
                }else{
                    $exist  = false;
                }
                if( $exist ) $selected_faqs[] = $faq;
            }

            include_once( NBDESIGNER_PLUGIN_DIR . 'views/faq/panel.php' );
        }
        public function save_faq_settings( $post_id ){
            $nbd_faq    = $_POST['_nbd_faq'];
            update_post_meta( $post_id, '_nbd_faq', serialize( $nbd_faq ) );
        }
        public function nbc_extra_nav(){
            ?>
                <div class="nbc-popup-nav-item" ng-click="activeTab('faq'); initHelper();" ng-class="frontendLayout.activeNav == 'faq' ? 'active' : ''">
                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0z" fill="none"/><path fill="#999" d="M19 2H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h4l3 3 3-3h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-6 16h-2v-2h2v2zm2.07-7.75l-.9.92C13.45 11.9 13 12.5 13 14h-2v-.5c0-1.1.45-2.1 1.17-2.83l1.24-1.26c.37-.36.59-.86.59-1.41 0-1.1-.9-2-2-2s-2 .9-2 2H8c0-2.21 1.79-4 4-4s4 1.79 4 4c0 .88-.36 1.68-.93 2.25z"/></svg>
                </div>
            <?php
        }
        public function nbc_extra_panel(){
            include_once( NBDESIGNER_PLUGIN_DIR . 'views/live-chat/frontend/faq-panel.php' );
        }
        public function nbf_get_live_chat_helper(){
            if (!wp_verify_nonce($_POST['nonce'], 'nbd_live_chat') && NBDESIGNER_ENABLE_NONCE) {
                die('Security error');
            }

            $result     = array(
                'flag'  => 1
            );

            $categories = get_transient( 'nbd_live_chat_helper' );
            if( false === $categories ){
                $categories     = array();
                $live_chat_faqs = get_option( 'nbd_live_chat_faqs' );
                if( $live_chat_faqs ){
                    $_faqs  = unserialize( $live_chat_faqs );
                    $ids    = array();
    
                    foreach( $_faqs as $_faq ){
                        $arr    = explode( '_', $_faq );
                        $exist  = true;
                        $faq    = array(
                            'id'        => $arr[1],
                            'type'      => $arr[0],
                            'desc'      => ''
                        );

                        if( $arr[0] == 'cat' ){
                            $term           = get_term( $arr[1] );
                            if( $term ){
                                $faq['title']   = $term->name;
                                $faq['parent']  = $term->parent;
                            } else {
                                $exist  = false;
                            }

                            $child_cats = get_terms( array(
                                'taxonomy'      => 'nbd_faq_category',
                                'child_of'      => absint( $arr[1] ),
                                'hide_empty'    => true
                            ) );

                            if( is_array( $child_cats ) ){
                                foreach( $child_cats as $child_cat ){
                                    $term_id    = $child_cat->term_id;
                                    $id         = 'cat_' . $term_id;
                                    if( array_search( $_faq, $ids ) === false ){
                                        $ids[]          = $id;
                                        $child_faq      = array(
                                            'id'        => $term_id,
                                            'type'      => 'cat',
                                            'desc'      => '',
                                            'title'     => $child_cat->name,
                                            'parent'    => $child_cat->parent
                                        );
                                        $categories[]   = $child_faq;

                                        $args = array(
                                            'post_type'         => 'nbd_faq',
                                            'numberposts'       => -1,
                                            'post_status'       => 'publish',
                                            'tax_query'         => array(
                                                'relation'      => 'AND',
                                                array(
                                                    'taxonomy'          => 'nbd_faq_category',
                                                    'field'             => 'term_id',
                                                    'terms'             => $term_id,
                                                    'include_children'  => false
                                                )
                                            )
                                        );

                                        $posts = get_posts( $args );
                                        foreach( $posts as $post ){
                                            $post_id    = $post->ID;
                                            $id         = 'faq_' . $post_id;
                                            if( array_search( $_faq, $ids ) === false ){
                                                $ids[]          = $id;
                                                $child_faq      = array(
                                                    'id'        => $post_id,
                                                    'type'      => 'faq',
                                                    'desc'      => '',
                                                    'title'     => $post->post_title,
                                                    'parent'    => $term_id
                                                );
                                                $categories[]   = $child_faq;
                                            }
                                        }
                                    }
                                }
                            }

                            $args = array(
                                'post_type'         => 'nbd_faq',
                                'numberposts'       => -1,
                                'post_status'       => 'publish',
                                'tax_query'         => array(
                                    'relation'      => 'AND',
                                    array(
                                        'taxonomy'          => 'nbd_faq_category',
                                        'field'             => 'term_id',
                                        'terms'             => $arr[1],
                                        'include_children'  => false
                                    )
                                )
                            );
                            $posts = get_posts( $args );
                            foreach( $posts as $post ){
                                $post_id    = $post->ID;
                                $id         = 'faq_' . $post_id;
                                if( array_search( $_faq, $ids ) === false ){
                                    $ids[]          = $id;
                                    $child_faq      = array(
                                        'id'        => $post_id,
                                        'type'      => 'faq',
                                        'desc'      => '',
                                        'title'     => $post->post_title,
                                        'parent'    => $arr[1]
                                    );
                                    $categories[]   = $child_faq;
                                }
                            }
                        }else{
                            $post           = get_post( $arr[1] );
                            if( $post ){
                                $faq['title']   = $post->post_title;
                                $terms          = get_the_terms( $arr[1], 'nbd_faq_category' );
                                if( is_array( $terms ) && count( $terms ) ){
                                    $faq['parent']  = $terms[0]->term_id;
                                }else{
                                    $faq['parent']  = 0;
                                }
                            }else{
                                $exist  = false;
                            }
                        }
                        if( $exist ) {
                            if( array_search( $_faq, $ids ) === false ){
                                $ids[]          = $_faq;
                                $categories[]   = $faq;
                            }
                        }
                    }
                }

                set_transient( 'nbd_live_chat_helper' , $categories, DAY_IN_SECONDS );
            }

            $result['categories'] = $categories;

            wp_send_json( $result );
        }
        public function nbf_get_faq_content(){
            if (!wp_verify_nonce($_POST['nonce'], 'nbd_live_chat') && NBDESIGNER_ENABLE_NONCE) {
                die('Security error');
            }

            $result = array(
                'flag'  => 0
            );

            $fid    = absint( $_POST['fid'] );
            $faq    = get_post( $fid );

            if( $faq ){
                $result['flag']     = 1;
                $result['content']  = wpautop( $faq->post_content );
            }

            wp_send_json( $result );
        }
        public function nbf_vote_faq(){
            if (!wp_verify_nonce($_POST['nonce'], 'nbd_live_chat') && NBDESIGNER_ENABLE_NONCE) {
                die('Security error');
            }

            $result = array(
                'flag'  => 0
            );

            $fid    = absint( $_POST['fid'] );
            $type   = wc_clean( $_POST['type'] );

            if( $fid > 0 && ( $type == 'up' || $type == 'down' ) ){
                $_nbf       = get_post_meta( $fid, '_nbf', true );
                if( $_nbf ){
                    $nbf = unserialize( $_nbf );
                }else{
                    $nbf = array(
                        'up_vote'   => '',
                        'down_vote' => ''
                    );
                }
                
                if( $type == 'up' ){
                    $nbf['up_vote']     = $nbf['up_vote'] == '' ? 1 : ( absint( $nbf['up_vote'] ) + 1 );
                }else{
                    $nbf['down_vote']   = $nbf['down_vote'] == '' ? 1 : ( absint( $nbf['down_vote'] ) + 1 );
                }

                update_post_meta( $fid, '_nbf', serialize( $nbf ) );
                $result['flag'] = 1;
            }

            wp_send_json( $result );
        }
        public function nbf_get_faqs_of_category(){
            $cat_id = sanitize_text_field( $_POST['cat_id'] );
            $args   = array( "numberposts" => -1, "post_type" => 'nbd_faq' );

            if ( $cat_id != "" ) {
                $args['tax_query'] = array( array(
                    'taxonomy'  => 'nbd_faq_category',
                    'terms'     => $cat_id
                ) );
            }

            $faqs = get_posts( $args );

            ob_start();
            include( NBDESIGNER_PLUGIN_DIR . 'views/faq/faq-table.php' );
            $content = ob_get_clean();

            echo $content;
            die();
        }
        public function nbf_update_live_chat_helper(){
            $_ids   = stripcslashes( $_POST['ids'] );
            $ids    = json_decode( $_ids, true );

            $result = array(
                'flag'  => 1
            );

            if( !update_option( 'nbd_live_chat_faqs', serialize( $ids ) ) ){
                $result['flag'] = 0;
            }
            delete_transient( 'nbd_live_chat_helper' );

            wp_send_json( $result );
        }
        public function product_faq_tab( $tabs ){
            global $post;
            $_nbd_faq       = get_post_meta( $post->ID, '_nbd_faq', true );
            if( $_nbd_faq ){
                $nbd_faq = unserialize( $_nbd_faq );
                if( isset( $nbd_faq['enable'] ) && $nbd_faq['enable'] == '1' && isset( $nbd_faq['faqs'] ) && count( $nbd_faq['faqs'] ) ){
                    $tabs['nbd_faq'] = array(
                        'title'    => __( 'FAQs', 'web-to-print-online-designer' ),
                        'priority' => 70,
                        'callback' => array( $this, 'faq_tab_content' )
                    );
                }
            }
            return $tabs;
        }
        public function faq_tab_content(){
            global $product;

            $product_id = $product->get_id();
            $_nbd_faq   = get_post_meta( $product_id, '_nbd_faq', true );
            $nbd_faq    = unserialize( $_nbd_faq );
            $faqs       = array();

            foreach( $nbd_faq['faqs'] as $_faq ){
                $exist          = true;
                $post           = get_post( $_faq );
                $faq            = array();
                if( $post ){
                    $faq['title']   = $post->post_title;
                    $faq['content'] = wpautop( $post->post_content );
                }else{
                    $exist  = false;
                }
                if( $exist ) $faqs[] = $faq;
            }

            ob_start();
            nbdesigner_get_template( 'single-product/faq.php', array(
                'faqs'  => $faqs
            ) );
            $content = ob_get_clean();
            echo $content;
        }
    }
}

$nbd_fags = Nbdesigner_FAQ::instance();
$nbd_fags->init();