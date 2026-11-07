<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if( !class_exists('NBD_Install') ) {
    class NBD_Install{
        public function __construct() {
            //todo something when initial class
        }
        public static function create_pages(){
            /* Create Studio page */
            $studio_page_id = nbd_get_page_id( 'studio' );
            if ( $studio_page_id == -1 || !get_post($studio_page_id) ){
                $post = array(
                    'post_name'         => NBDESIGNER_PAGE_STUDIO,
                    'post_status'       => 'publish',
                    'post_title'        => __('Studio', 'web-to-print-online-designer'),
                    'post_type'         => 'page',
                    'post_author'       => 1,
                    'post_content'      => '[nbdesigner_studio]',
                    'comment_status'    => 'closed',
                    'post_date'         => date('Y-m-d H:i:s')
                );      
                $studio_page_id = wp_insert_post($post, false);
                update_option( 'nbdesigner_studio_page_id', $studio_page_id );
            }
            
            /* Create design your own page */
            $create_your_own_page_id = nbd_get_page_id( 'create_your_own' );
            if ( $create_your_own_page_id == -1|| !get_post($create_your_own_page_id) ){        
                $post = array(
                    'post_name'         => NBDESIGNER_PAGE_CREATE_YOUR_OWN,
                    'post_status'       => 'publish',
                    'post_title'        => __('Create your own', 'web-to-print-online-designer'),
                    'post_type'         => 'page',
                    'post_author'       => 1,
                    'comment_status'    => 'closed',
                    'post_date'         => date('Y-m-d H:i:s')
                );
                $create_your_own_page_id = wp_insert_post($post, false);	
                update_option( 'nbdesigner_create_your_own_page_id', $create_your_own_page_id );    
            }      

            /* Create redirect login page */  
            $nbd_redirect_logged_page_id = nbd_get_page_id( 'logged' );
            if ( $nbd_redirect_logged_page_id == -1 || !get_post($nbd_redirect_logged_page_id) ){        
                $post = array(
                    'post_name'         => 'nbd-logged',
                    'post_status'       => 'publish',
                    'post_title'        => __('Welcome to NBDesigner', 'web-to-print-online-designer'),
                    'post_type'         => 'page',
                    'post_author'       => 1,
                    'post_content'      => '[nbd_loggin_redirect]',
                    'comment_status'    => 'closed',
                    'post_date'         => date('Y-m-d H:i:s')
                );      
                $nbd_redirect_logged_page_id = wp_insert_post($post, false);
                update_option( 'nbdesigner_logged_page_id', $nbd_redirect_logged_page_id ); 
            }  

            /* Create gallery page */  
            $nbd_gallery_page_id = nbd_get_page_id( 'gallery' );
            if ( $nbd_gallery_page_id == -1 || !get_post($nbd_gallery_page_id) ){
                $post = array(
                    'post_name'         => 'templates',
                    'post_status'       => 'publish',
                    'post_title'        => __('Gallery', 'web-to-print-online-designer'),
                    'post_type'         => 'page',
                    'post_author'       => 1,
                    'post_content'      => '[nbdesigner_gallery row="6" pagination="true" per_row="5" ][/nbdesigner_gallery]',
                    'comment_status'    => 'closed',
                    'post_date'         => date('Y-m-d H:i:s')
                );
                $nbd_gallery_page_id = wp_insert_post($post, false);	
                update_option( 'nbdesigner_gallery_page_id', $nbd_gallery_page_id ); 
            }

            /* Create designer page */  
            $nbd_designer_page_id = nbd_get_page_id( 'designer' );
            if ( $nbd_designer_page_id == -1 || !get_post($nbd_designer_page_id) ){
                $post = array(
                    'post_name'         => 'designer',
                    'post_status'       => 'publish',
                    'post_title'        => __('Designer', 'web-to-print-online-designer'),
                    'post_type'         => 'page',
                    'post_author'       => 1,
                    'post_content'      => '',
                    'comment_status'    => 'closed',
                    'post_date'         => date('Y-m-d H:i:s')
                );
                $nbd_designer_page_id = wp_insert_post($post, false);	
                update_option( 'nbdesigner_designer_page_id', $nbd_designer_page_id );
            }
            
            /* Create product builder page */  
            $nbd_product_builder_page_id = nbd_get_page_id( 'product_builder' );
            if ( $nbd_product_builder_page_id == -1 || !get_post($nbd_product_builder_page_id) ){
                $post = array(
                    'post_name'         => 'product-builder',
                    'post_status'       => 'publish',
                    'post_title'        => __('Product Builder', 'web-to-print-online-designer'),
                    'post_type'         => 'page',
                    'post_author'       => 1,
                    'post_content'      => '',
                    'comment_status'    => 'closed',
                    'post_date'         => date('Y-m-d H:i:s')
                );
                $nbd_product_builder_page_id = wp_insert_post($post, false);	
                update_option( 'nbdesigner_product_builder_page_id', $nbd_product_builder_page_id );
            }
            
            do_action('nbd_create_pages');
        }
        public static function create_tables(){
            global $wpdb;
            $collate = '';
            if ( $wpdb->has_cap( 'collation' ) ) {
                $collate = $wpdb->get_charset_collate();
            } 
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            if (NBDESIGNER_VERSION != get_option("nbdesigner_version_plugin")) {
                //PRIMARY KEY must have 2 spaces before for dbDelta to work
                $tables =  "
CREATE TABLE {$wpdb->prefix}nbdesigner_templates ( 
 id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
 product_id BIGINT(20) UNSIGNED NOT NULL,
 variation_id BIGINT(20) NULL, 
 folder varchar(255) NOT NULL,
 user_id BIGINT(20) NULL, 
 created_date DATETIME NOT NULL default '0000-00-00 00:00:00',
 publish TINYINT(1) NOT NULL default 1,
 private TINYINT(1) NOT NULL default 0,
 priority  TINYINT(1) NOT NULL default 0,
 hit BIGINT(20) NULL, 
 sales INT(10) NOT NULL default 0,
 vote INT(10) NOT NULL default 0,
 name varchar(255) NULL,
 type varchar(255) NULL,
 resource varchar(255) NULL,
 tags varchar(255) NULL,
 colors varchar(255) NULL,
 thumbnail INT(10) NULL,
 PRIMARY KEY  (id) 
) $collate;
CREATE TABLE {$wpdb->prefix}nbdesigner_mydesigns (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT(20) NOT NULL, 
  folder varchar(255) NOT NULL,
  product_id BIGINT(20) UNSIGNED NOT NULL,
  variation_id BIGINT(20) NULL,   
  price varchar(255) NOT NULL default '0',
  selling TINYINT(1) NOT NULL default 0,
  vote INT(10) NOT NULL default 0,
  publish TINYINT(1) NOT NULL default 1,
  created_date DATETIME NOT NULL default '0000-00-00 00:00:00',
  hit INT(10) NOT NULL default 0,
  sales INT(10) NOT NULL default 0,
  PRIMARY KEY  (id)
) $collate;  
CREATE TABLE {$wpdb->prefix}nbdesigner_user_designs (
  id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id BIGINT(20) NOT NULL, 
  folder varchar(255) NOT NULL,
  created_date DATETIME NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id)
) $collate; 
                ";
                @dbDelta($tables);
                
                do_action('nbd_create_tables');
            }
            return true;
        }
        public static function insert_default_files(){
            $default_background = get_option('nbdesigner_default_background' );
            $default_overlay = get_option('nbdesigner_default_overlay' );
            if( !$default_background || !wp_get_attachment_url($default_background) || !file_exists( get_attached_file($default_background) ) ) {
                $background_file = NBDESIGNER_PLUGIN_DIR . 'assets/images/default.png';
                $bg_id = nbd_add_attachment( $background_file );
                update_option('nbdesigner_default_background', $bg_id );
            }
            if( !$default_overlay || !wp_get_attachment_url($default_overlay) || !file_exists( get_attached_file($default_overlay) ) ) {
                $overlay_file = NBDESIGNER_PLUGIN_DIR . 'assets/images/overlay.png';
                $ol_id = nbd_add_attachment( $overlay_file );
                update_option('nbdesigner_default_overlay', $ol_id );
            }
            do_action('nbd_insert_default_files');
        } 
        public static function init_files_and_folders(){
            Nbdesigner_IO::mkdir(NBDESIGNER_TEMP_DIR);
            Nbdesigner_IO::mkdir(NBDESIGNER_UPLOAD_DIR);
            Nbdesigner_IO::mkdir(NBDESIGNER_DOWNLOAD_DIR);
            Nbdesigner_IO::mkdir(NBDESIGNER_FONT_DIR);
            Nbdesigner_IO::mkdir(NBDESIGNER_ART_DIR);
            Nbdesigner_IO::mkdir(NBDESIGNER_DATA_CONFIG_DIR . '/language');
            Nbdesigner_IO::mkdir(NBDESIGNER_SUGGEST_DESIGN_DIR);
            Nbdesigner_IO::mkdir(NBDESIGNER_LOG_DIR);
            Nbdesigner_IO::mkdir(NBDESIGNER_CUSTOMER_DIR);
            if( nbdesigner_get_option('nbdesigner_redefine_K_PATH_FONTS') == 'yes' ){
                Nbdesigner_IO::mkdir(K_PATH_FONTS);
            }
            Nbdesigner_IO::create_index_html(NBDESIGNER_DATA_DIR . '/index.html');
            copy(NBDESIGNER_PLUGIN_DIR.'includes/tcpdf/fonts/helvetica.php', K_PATH_FONTS. 'helvetica.php');
            copy(NBDESIGNER_PLUGIN_DIR.'includes/tcpdf/fonts/tahoma.ctg.z', K_PATH_FONTS. 'tahoma.ctg.z');
            copy(NBDESIGNER_PLUGIN_DIR.'includes/tcpdf/fonts/tahoma.php', K_PATH_FONTS. 'tahoma.php');
            copy(NBDESIGNER_PLUGIN_DIR.'includes/tcpdf/fonts/tahoma.z', K_PATH_FONTS. 'tahoma.z');
            do_action('nbd_init_files_and_folders');
        }
    }
}