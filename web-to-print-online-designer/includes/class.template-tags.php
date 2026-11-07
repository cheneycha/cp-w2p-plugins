<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if(!class_exists('NBD_Template_Tag')) {
    class NBD_Template_Tag{
        protected static $instance;
        public static function get_instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        public function __construct(){
            add_action( 'nbd_menu', array($this, 'add_sub_menu'), 90 );
            add_action( 'woocommerce_register_taxonomy', array( $this, 'init_taxonomy' ) );
            add_filter( 'parent_file', array( $this, 'set_current_menu' ) );
            add_action( 'template_tag_add_form_fields', array( $this, 'add_form_fields' ) );
            add_action( 'template_tag_edit_form_fields', array( $this, 'edit_form_fields' ), 10, 2 );
            add_action( 'created_term', array( $this, 'save_extra_fields' ), 10, 3 );
            add_action( 'edit_term', array( $this, 'save_extra_fields' ), 10, 3 );
            add_filter( 'manage_template_tag_custom_column', array( $this, 'template_tag_column' ), 10, 3 );
            add_filter( 'manage_edit-template_tag_columns', array( $this, 'template_tag_columns' ) );
            add_action( 'after_nbd_save_customer_design', array( $this, 'update_template' ), 10, 1 );
            add_filter( 'nbd_product_info', array( $this, 'template_info' ), 10, 1 );
            add_filter( 'nbd_product_templates', array( $this, 'templates_info' ), 10, 2 );
            add_action( 'nbd_before_gallery_sidebar', array( $this, 'gallery_sidebar_tag_list' ) );
            add_action( 'nbd_gallery_filter', array( $this, 'gallery_tag_filter' ) );
            $this->ajax();
        }
        public function ajax(){
            $ajax_events = array(
                'nbd_get_template_tags' => true,
            );
            foreach ( $ajax_events as $ajax_event => $nopriv ) {
                add_action( 'wp_ajax_' . $ajax_event, array( $this, $ajax_event ) );
                if  ( $nopriv ) {
                    add_action( 'wp_ajax_nopriv_' . $ajax_event, array( $this, $ajax_event ) );
                }
            }
        }
        public function add_sub_menu(){
            if( current_user_can( 'manage_nbd_tool' ) ){
                add_submenu_page(
                    'nbdesigner', esc_html__('Template Tags', 'web-to-print-online-designer'), esc_html__('Template Tags', 'web-to-print-online-designer'), 'manage_nbd_tool', 'edit-tags.php?taxonomy=template_tag&post_type=product', null
                );
                add_submenu_page(
                    'nbd_faq', esc_html__('Printing FAQs', 'web-to-print-online-designer'), esc_html__('Printing FAQs', 'web-to-print-online-designer'), 'manage_nbd_tool', 'edit.php?post_type=nbd-faq', null
                );
            }
        }
        public function init_taxonomy( ){
            register_taxonomy('template_tag', array('product'),
                apply_filters('register_taxonomy_template_tag', array(
                    'hierarchical'  => false,
                    'public'        => false,
                    'rewrite'       => false,
                    'show_ui'       => true,
                    'show_in_menu'  => false,
                    'show_tagcloud' => false,
                    'meta_box_cb'   => false,
                    'label'         => esc_html__('Template tags', 'web-to-print-online-designer'),
                    'labels'        => array(
                        'name'              => esc_html__('Template tags', 'web-to-print-online-designer'),
                        'singular_name'     => esc_html__('Template tag', 'web-to-print-online-designer'),
                        'search_items'      => esc_html__('Search Template tag', 'web-to-print-online-designer'),
                        'all_items'         => esc_html__('All Template tag', 'web-to-print-online-designer'),
                        'edit_item'         => esc_html__('Edit Template tag', 'web-to-print-online-designer'),
                        'update_item'       => esc_html__('Update Template tag', 'web-to-print-online-designer'),
                        'add_new_item'      => esc_html__('Add New Template tag', 'web-to-print-online-designer'),
                        'new_item_name'     => esc_html__('New Template tag', 'web-to-print-online-designer')
                    ),
                    
                ))
            );
        }
        public function set_current_menu( $parent_file ){
            global $submenu_file, $current_screen, $pagenow;
            if ( $current_screen->post_type == 'product' && $current_screen->taxonomy == 'template_tag' ) {
                if ( $pagenow == 'edit-tags.php' || $pagenow == 'term.php' ) {
                    $submenu_file = 'edit-tags.php?taxonomy=template_tag&post_type=' . $current_screen->post_type;
                }
                $parent_file = 'nbdesigner';
            }
            return $parent_file;
        }
        public function add_form_fields(){
            $image          = NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
            $thumbnail_id   = 0;
            $featured       = '';
            include_once( NBDESIGNER_PLUGIN_DIR . 'views/template/tags-extra-fields.php' );
        }
        public function edit_form_fields( $term, $taxonomy ){
            $thumbnail_id   = absint( get_term_meta( $term->term_id, 'thumbnail_id', true ) );
            $featured       = get_term_meta($term->term_id, 'featured', true);
            $image          = nbd_get_image_thumbnail( $thumbnail_id );
            include_once(NBDESIGNER_PLUGIN_DIR . 'views/template/tags-extra-fields.php');
        }
        public function save_extra_fields( $term_id, $tt_id, $taxonomy ){
            if ( $taxonomy == 'template_tag' && isset($_POST['template_tag_thumbnail_id'])){
                if( nbd_check_woo_version( '3.6' ) ){
                    update_term_meta( $term_id, 'thumbnail_id', absint( $_POST['template_tag_thumbnail_id'] ) );
                }else{
                    update_woocommerce_term_meta( $term_id, 'thumbnail_id', absint( $_POST['template_tag_thumbnail_id'] ) );
                }
            }
        }
        public function template_tag_columns( $columns ){
            $new_columns                = array();
            if ( isset( $columns['cb'] ) ) {
                $new_columns['cb']          = $columns['cb'];
                unset( $columns['cb'] );
            }
            $new_columns['thumb']       = esc_html__('Image', 'web-to-print-online-designer');
            $new_columns['name']        = esc_html__('Name', 'web-to-print-online-designer');
            $new_columns['featured']    = esc_html__('Featured', 'web-to-print-online-designer');
            $columns['handle']          = '';
            if ( isset( $columns['posts'] ) ) {
                unset( $columns['posts'] );
            }
            return array_merge($new_columns, $columns);
        }
        public function template_tag_column( $columns, $column, $id ){
            if ( $column == 'thumb' ) {
                $image          = NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
                $thumbnail_id   = absint( get_term_meta( $id, 'thumbnail_id', true ) );
                $image          = nbd_get_image_thumbnail( $thumbnail_id );
                $columns       .= '<img src="' . esc_url($image) . '" alt="Thumbnail" class="wp-post-image" height="48" width="48" />';
            }
            if ( $column == "featured" ) {
                $featured       = get_term_meta($id, 'featured', true);
                if ( $featured == "1" ){
                    $columns   .= 'yes';
                }else{
                    $columns   .= 'no';
                }
            }
            return $columns;
        }
        public function nbd_get_template_tags(){
            if ( !wp_verify_nonce($_REQUEST['nonce'], 'save-design') && NBDESIGNER_ENABLE_NONCE ) {
                die('Security error');
            }
            $template_tags = get_terms( 'template_tag', 'hide_empty=0' );
            $result = array(
                'flag'  =>  0
            );
            if ( ! empty( $template_tags ) && ! is_wp_error( $template_tags ) ){
                $tags           = array();
                foreach( $template_tags as $tag ){
                    $tags[] = array(
                        'term_id'   =>  $tag->term_id,
                        'name'      =>  $tag->name
                    );
                }
                $result['tags'] = $tags;
                $result['flag'] = 1;
            }
            wp_send_json($result);
        }
        public function update_template( $result ){
            $task            = (isset($_POST['task']) && $_POST['task'] != '') ? wc_clean( $_POST['task'] ) : 'new';
            $design_type     = (isset($_POST['design_type']) && $_POST['design_type'] != '') ? wc_clean( $_POST['design_type'] ) : '';
            $info            = array();
            if( $task == 'create' || ( $task == 'edit' && $design_type == 'template' ) ){
                $info['name']        = (isset($_POST['template_name']) && $_POST['template_name'] != '') ? wc_clean( $_POST['template_name'] ) : '';
                $type                = (isset($_POST['template_type']) && $_POST['template_type'] != '') ? wc_clean( $_POST['template_type'] ) : '';
                $info['tags']        = (isset($_POST['template_tags']) && $_POST['template_tags'] != '') ? wc_clean( $_POST['template_tags'] ) : '';
                $info['colors']      = (isset($_POST['template_colors']) && $_POST['template_colors'] != '') ? wc_clean( $_POST['template_colors'] ) : '';
                if( $type == '2' && isset( $_FILES['template_thumb'] ) ){
                    $thumb   = $_FILES['template_thumb'];
                    if( $thumb['error'] == 0 ){
                        $attachment_id = $this->upload_template_thumb( $thumb );
                        if( $attachment_id ){
                            $info['thumbnail']  = $attachment_id;
                        }
                    }
                }
                $templates = $this->get_template_by_folder( $result['folder'] );
                if( is_array($templates) && isset( $templates[0] ) ){
                    $tid = $templates[0]['id'];
                    $this->update_template_info( $tid, $info );
                }
            }
        }
        private function upload_template_thumb( $file ){
            $overrides          = array(
                'test_form'     => false,
                'test_size'     => true,
                'test_upload'   => true
            );
            $file_attributes = wp_handle_sideload( $file, $overrides );
            if ( isset($file_attributes['error']) ) {
                return false;
            }
            $file_path          = $file_attributes['file'];
            $mime_type          = $file_attributes['type'];
            $wp_upload_dir      = wp_upload_dir();
            $attachment_data    = array(
                'guid'              => $wp_upload_dir['url'] . '/' . basename($file_path),
                'post_mime_type'    => $mime_type,
                'post_title'        => preg_replace('/\.[^.]+$/', '', basename($file_path)),
                'post_content'      => '',
                'post_status'       => 'inherit'
            );
            $attachment_id      = wp_insert_attachment( $attachment_data, $file_path );
            if ( !$attachment_id ) {
                return;
            }
            $_file_path         = get_attached_file( $attachment_id );
            require_once ABSPATH . 'wp-admin/includes/image.php';
            $attach_data        = wp_generate_attachment_metadata( $attachment_id, $_file_path );
            wp_update_attachment_metadata( $attachment_id, $attach_data );
            return $attachment_id;
        }
        private function get_template_by_folder( $folder ){
            global $wpdb;
            $sql         = "SELECT * FROM {$wpdb->prefix}nbdesigner_templates";
            if ( !empty($folder) ) {
                $sql    .= " WHERE folder = '" . esc_sql( $folder ) . "'";
            }
            $result      = $wpdb->get_results($sql, 'ARRAY_A');
            return $result;
        }
        private function update_template_info( $tid, $info ){
            global $wpdb;
            $wpdb->update("{$wpdb->prefix}nbdesigner_templates", $info, array( 'id' => $tid ) );
        }
        public function template_info( $data ){
            $task            = (isset($_REQUEST['task']) && $_REQUEST['task'] != '') ? $_REQUEST['task'] : 'new';
            $design_type     = (isset($_REQUEST['design_type']) && $_REQUEST['design_type'] != '') ? wc_clean( $_REQUEST['design_type'] ) : '';
            if( $task == 'edit' && $design_type == 'template' ){
                $folder         = wc_clean( $_GET['nbd_item_key'] );
                $templates      = $this->get_template_by_folder( $folder );
                if( is_array($templates) && isset( $templates[0] ) ){
                    $template               = $templates[0];
                    $data['template']       = array(
                        'tags'     => is_null( $template['tags'] ) ? '' : $template['tags'],
                        'colors'   => is_null( $template['colors'] ) ? '' : $template['colors'],
                        'name'     => $template['name']
                    );
                }
            }
            return $data;
        }
        public function templates_info( $data, $templates ){
            $template_data                  = array();
            $template_data['templates']     = $data;
            $template_data['template_tags'] = array();
            $tags                           = array();
            $template_tags                  = array();
            $un_tag                         = array(
                'id'        => 0,
                'name'      => esc_html__('Templates', 'web-to-print-online-designer'),
                'thumb'     => NBDESIGNER_ASSETS_URL . 'images/template.png',
                'templates' => array()
            );
            if( is_array( $templates ) ){
                foreach( $templates as $key => $template ){
                    if( !is_null( $template['tags'] ) && $template['tags'] != '' ){
                        $templates[$key]['tags_arr'] = explode(',', $template['tags']);
                        $tags                        = array_merge( $tags, $templates[$key]['tags_arr'] );
                    }else{
                        $un_tag['templates'][] = $this->filter_template_by_folder( $data, $template['folder'] );
                    }
                }
                $tags = array_unique( $tags );
            }
            if( count( $un_tag['templates'] ) > 0 ){
                $template_tags[] = $un_tag;
            }
            foreach( $tags as $tag_id ){
                $thumbnail_id       = absint( get_term_meta( $tag_id, 'thumbnail_id', true ) );
                $term               = get_term_by( 'id', $tag_id, 'template_tag' );
                if ( $term && ! is_wp_error( $term ) ) {
                    $template_tag = array(
                        'id'        => $tag_id,
                        'name'      => $term->name,
                        'thumb'     => nbd_get_image_thumbnail( $thumbnail_id ),
                        'templates' => array()
                    );
                    foreach( $templates as $template ){
                        if( $template['tags'] != '' ){
                            if(in_array( $tag_id, $template['tags_arr']) ){
                                $template_tag['templates'][] = $this->filter_template_by_folder( $data, $template['folder'] );
                            }
                        }
                    }
                    if( count( $template_tag['templates'] ) > 0 ){
                        $template_tags[] = $template_tag;
                    }
                }
            }
            if( ( count( $template_tags ) > 1 && count( $un_tag['templates'] ) > 0 ) || ( count( $template_tags ) > 0 && count( $un_tag['templates'] ) == 0 ) ){
                usort($template_tags, function( $t1, $t2 ){
                    $id1 = (int) $t1['id'];
                    $id2 = (int) $t2['id'];
                    return $id1 - $id2;
                });
                $template_data['template_tags'] = $template_tags;
            }
            return $template_data;
        }
        function filter_template_by_folder( $data, $folder ){
            $template = array();
            foreach( $data as $tem ){
                if( $tem['id'] == $folder ){
                    $template = $tem;
                }
            }
            return $template;
        }
        public function gallery_sidebar_tag_list(){
            $filter_term_ids    = isset( $_GET['tag'] ) ? wc_clean( $_GET['tag'] ) : '';
            $filter_tags        = $filter_term_ids != '' ? explode(',', $filter_term_ids) : array();
            $filter_colors_str  = isset( $_GET['color'] ) ? wc_clean( $_GET['color'] ) : '';
            $filter_colors      = $filter_colors_str != '' ? explode(',', $filter_colors_str) : array();
            $template_tags      = get_terms( 'template_tag', 'hide_empty=0' );
            $tags               = array();
            $colors             = array();
            if ( ! empty( $template_tags ) && ! is_wp_error( $template_tags ) ){
                foreach( $template_tags as $tag ){
                    $tags[] = array(
                        'term_id'   =>  $tag->term_id,
                        'name'      =>  $tag->name
                    );
                }
            }
            $colors = $this->get_color_list();
            ob_start();
            nbdesigner_get_template( 'gallery/tag-list.php', array( 'tags' => $tags, 'filter_tags' => $filter_tags, 'colors' => $colors, 'filter_colors' => $filter_colors  ) );
            echo ob_get_clean();
        }
        public function gallery_tag_filter(){
            $term_ids   = isset( $_GET['tag'] ) ? wc_clean( $_GET['tag'] ) : '';
            $colors     = isset( $_GET['color'] ) ? wc_clean( $_GET['color'] ) : '';
            $search     = isset( $_GET['search'] ) ? wc_clean( $_GET['search'] ) : '';
            $tags       = array();

            if( $term_ids != '' ){
                $term_ids_arr = explode(',', trim($term_ids));
                foreach ( $term_ids_arr as $term_id ){
                    $tag = get_term( $term_id, 'template_tag' );
                    if( ! is_wp_error( $tag ) ){
                        $tags[] = $tag;
                    }
                }
            }

            if( $search != '' ){
                ?>
                <span class="nbd-gallery-filter-tag">
                    <span class="nbd-filter-tag-name"><?php echo( $search ); ?></span>
                    <span class="nbd-filter-tag-remove" data-type="search" >
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path d="M18.984 6.422l-5.578 5.578 5.578 5.578-1.406 1.406-5.578-5.578-5.578 5.578-1.406-1.406 5.578-5.578-5.578-5.578 1.406-1.406 5.578 5.578 5.578-5.578z"></path>
                        </svg>
                    </span>
                </span>
                <?php
            }

            if( count( $tags ) ){
                foreach( $tags as $_tag ):
                ?>
                <span class="nbd-gallery-filter-tag">
                    <span class="nbd-filter-tag-name"><?php echo( $_tag->name ); ?></span>
                    <span class="nbd-filter-tag-remove" data-type="tag" data-value="<?php echo( $_tag->term_id ); ?>">
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path d="M18.984 6.422l-5.578 5.578 5.578 5.578-1.406 1.406-5.578-5.578-5.578 5.578-1.406-1.406 5.578-5.578-5.578-5.578 1.406-1.406 5.578 5.578 5.578-5.578z"></path>
                        </svg>
                    </span>
                </span>
                <?php
                endforeach;
            }

            if( $colors != '' ){
                $color_arr = explode(',', trim( $colors ));
                foreach ( $color_arr as $_color ):
                ?>
                <span class="nbd-gallery-filter-tag">
                    <span class="nbd-filter-color" style="background: #<?php echo( $_color ); ?>;"></span>
                    <span class="nbd-filter-tag-remove" data-type="color" data-value="<?php echo( $_color ); ?>">
                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path d="M18.984 6.422l-5.578 5.578 5.578 5.578-1.406 1.406-5.578-5.578-5.578 5.578-1.406-1.406 5.578-5.578-5.578-5.578 1.406-1.406 5.578 5.578 5.578-5.578z"></path>
                        </svg>
                    </span>
                </span>
                <?php
                endforeach;
            }
        }
        public function get_color_list(){
            global $wpdb;
            $color_arr  = array();
            $sql        = "SELECT GROUP_CONCAT(colors SEPARATOR ',') AS color_str FROM {$wpdb->prefix}nbdesigner_templates WHERE colors != '' AND colors IS NOT NULL";
            $result     = $wpdb->get_results($sql, 'ARRAY_A');
            if( !empty( $result[0]['color_str'] ) ){
                $color_arr  = explode(',', $result[0]['color_str']);
                $color_arr  = array_unique( $color_arr );
            }
            return $color_arr;
        }
    }
}
function NBD_Template_Tag(){
    return NBD_Template_Tag::get_instance();
}
NBD_Template_Tag();