<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class NBD_Update_Data{
    public static function install_update(){
        $version = get_option("nbdesigner_version_plugin", null);
        if (!is_null($version) && version_compare($version, "1.5.0", '<')) {    
            self::update_data_150();
        }
        if (!is_null($version) && version_compare($version, "1.9.0", '<')) {    
            self::update_fonts();
        }        
        if (!is_null($version) && version_compare($version, "2.3.0", '<')) {
            self::remove_license_file();
        }
    }
    public function ajax(){
        $ajax_events = array(
            'nbd_update_all_template' => false
        );
	foreach ($ajax_events as $ajax_event => $nopriv) {
            add_action('wp_ajax_' . $ajax_event, array($this, $ajax_event));
            if ($nopriv) {
                // NBDesigner AJAX can be used for frontend ajax requests
                add_action('wp_ajax_nopriv_' . $ajax_event, array($this, $ajax_event));
            }
        }        
    }    
    public static function remove_license_file(){
        $license = nbd_get_license_key();
        if( $license['key'] != '' ){
            $path = NBDESIGNER_DATA_CONFIG_DIR . '/license.json';
            if( file_exists($path) ){
                unlink($path);
                $_license = get_option('nbdesigner_license');
                if( !$_license ){
                    update_option('nbdesigner_license', json_encode($license));
                }
            }
        }
    }
    public static function update_vatiation_config_v180(){
        if (!wp_verify_nonce($_POST['_nbdesigner_update_product'], 'nbdesigner-update-product') || !current_user_can('administrator')) {
            die('Security error');
        }         
        $args_query = array(
            'post_type'         => 'product',
            'post_status'       => 'publish',
            'meta_key'          => '_nbdesigner_enable',
            'orderby'           => 'date',
            'order'             => 'DESC',
            'posts_per_page'    => -1,
            'meta_query'        => array(
                array(
                    'key' => '_nbdesigner_enable',
                    'value' => 1,
                )
            )
        ); 
        $posts = get_posts($args_query);  
        $result = array('flag' => 1);
        if(is_array($posts)){    
            foreach ($posts as $post){
                $pid        = get_wpml_original_id( $post->ID );
                $product    = wc_get_product( $pid );
                if( $pid != $post->ID ) continue;
                if( $product->is_type( 'variable' ) ) {
                    $variations = $product->get_available_variations( false );
                    foreach ($variations as $variation){
                        $vid                = $variation['variation_id'];
                        $designer_enable    = get_post_meta($vid, '_nbdesigner_enable'.$vid, true);
                        $_designer_enable   = get_post_meta($vid, '_nbdesigner_variation_enable'.$vid, true);
                        if( $_designer_enable ) continue;
                        $designer_setting   = unserialize(get_post_meta($vid, '_designer_setting'.$vid, true));
                        if( $designer_enable ) {
                            update_post_meta($vid, '_designer_variation_setting', serialize($designer_setting));
                            update_post_meta($vid, '_nbdesigner_variation_enable', $designer_enable);
                        }
                    }
                }
            }
        }
        echo json_encode($result);
        wp_die();
    }
    /* Fix missing folder templates after update verion 1.6.2 to 1.7 */
    public function nbd_update_all_template() {
        if (!wp_verify_nonce($_POST['_nbdesigner_update_product'], 'nbdesigner-update-product') || !current_user_can('administrator')) {
            die('Security error');
        } 
        global $wpdb;
        $sql = "SELECT * FROM {$wpdb->prefix}nbdesigner_templates";
        $templates = $wpdb->get_results($sql, 'ARRAY_A');
        foreach ($templates as $template) {
            $nb_item_key = substr(md5(uniqid()), 0, 10);
            $src_path = NBDESIGNER_ADMINDESIGN_DIR . '/' . $template['product_id'] . '/' . $template['folder'];
            $dist_path = NBDESIGNER_CUSTOMER_DIR . '/' . $nb_item_key;
            Nbdesigner_IO::copy_dir($src_path, $dist_path);
            $id = $template['id'];
            $product_id = $template['product_id'];
            $product_option = get_post_meta($product_id, '_nbdesigner_option', true);
            $product_config = get_post_meta($product_id, '_designer_setting', true);
            file_put_contents($dist_path . '/option.json', $product_option);
            file_put_contents($dist_path . '/product.json', $product_config);
            $arr = array('variation_id' => 0, 'folder' => $nb_item_key);
            $wpdb->update("{$wpdb->prefix}nbdesigner_templates", $arr, array('id' => $id));
        } 
        $result = array('flag' => 1);
        echo json_encode($result);
        wp_die();
    }
    public static function nbd_update_media_v180( $designer_setting ){
        $default_background = get_option('nbdesigner_default_background' );
        $default_overlay = get_option('nbdesigner_default_overlay' );
        foreach( $designer_setting as $key => $value ){
            if( $default_background && strpos( $value['img_src'], 'assets/images/default.png' ) !== false ) {
                $designer_setting[$key]['img_src'] = $default_background;
            }
            if( $default_overlay && strpos( $value['img_overlay'], 'assets/images/overlay.png' ) !== false ) {
                $designer_setting[$key]['img_overlay'] = $default_overlay;
            }            
        }
        return $designer_setting;  
    }
    /**
     * Update data admin templates in older version (before 1.5.0)
     * @since 1.5.0
     * 
     */
    public static function update_data_150(){
        global $wpdb;
        $origin_path    = NBDESIGNER_ADMINDESIGN_DIR . '/';
        $listTemplates  = array();
        $args = array(
            'post_type'         => 'product',
            'meta_key'          => '_nbdesigner_admintemplate_primary',
            'orderby'           => 'date',
            'order'             => 'DESC',
            'posts_per_page'    => -1,
            'meta_query'        => array(
                array(
                    'key'   => '_nbdesigner_admintemplate_primary',
                    'value' => 1,
                )
            )
        );   
        $posts = get_posts($args); 
        foreach ($posts as $p){
            $pro = wc_get_product($p->ID);
            $list_folder = array();
            $path = $origin_path . $p->ID;
            if ($dir = @opendir($path)) {
                while (($file = readdir($dir) ) !== false) {
                    if (in_array($file, array('.', '..')))
                        continue;
                    if (is_dir($path . '/' . $file)) {
                        $list_folder[] =  $file;
                    }
                }
            }
            @closedir($dir);
            if(is_array($list_folder)){
                foreach($list_folder as $folder){
                    $listTemplates[] = array('product_id' => $p->ID, 'folder' => $folder);
                }
            }
        }
        if(is_array($listTemplates)){
            foreach($listTemplates as $temp){
                $created_date = new DateTime();
                $user_id = wp_get_current_user()->ID;
                $table_name =  $wpdb->prefix . 'nbdesigner_templates';
                $priority = 0;
                if($temp['folder'] == 'primary') $priority = 1;
                $wpdb->insert($table_name, array(
                    'product_id'    => $temp['product_id'],
                    'folder'        => $temp['folder'],
                    'user_id'       => $user_id,
                    'created_date'  => $created_date->format('Y-m-d H:i:s'),
                    'publish'       => 1,
                    'private'       => 0,
                    'priority'      => $priority
                ));  
            }
        }
    } 
    /**
     * Update variations and subset fonts
     * @since 1.9.0
     * 
     */
    public static function update_fonts(){
        /* Update custom fonts */
        $path = NBDESIGNER_DATA_DIR . '/fonts.json';
        $list = Nbdesigner_IO::read_json_setting( $path );
        $new_list = array();
        if( count($list) ){
            foreach( $list as $key => $font ){
                $new_list[$key] = (array)$font;
                if( !is_object($font->file) ){
                    $new_list[$key]['file'] = array();
                    $new_list[$key]['file']['r'] = $font->file;
                }
                if( !isset($font->subset) ) $new_list[$key]['subset'] = 'all';
            }
            $res = json_encode($new_list);
            file_put_contents($path, $res);
        }
        /* Update google fonts */
        $gg_fonts = array();
        $path_gg_font = NBDESIGNER_DATA_DIR. '/googlefonts.json';
        $all_gg_fonts = json_decode(file_get_contents(NBDESIGNER_PLUGIN_DIR. '/data/google-fonts-ttf.json'))->items;
        $gg_fonts_bf = json_decode(file_get_contents($path_gg_font)); 
        foreach($gg_fonts_bf as $key => $font){
            $subset = 'all';
            $file = array('r' => 1);
            foreach( $all_gg_fonts as $f ){
                if( $font->name == $f->family ){
                    $subset = $f->subsets[0];
                    if( isset($f->files->italic) ){
                        $file['i'] = 1;
                    }
                    if( isset($f->files->{"700"}) ){
                        $file['b'] = 1;
                    }
                    if( isset($f->files->{"700italic"}) ){
                        $file['bi'] = 1;
                    }
                    break;
                }
            }
            $gg_fonts[] = array(
                "id"        => $key,
                "name"      => $font->name,
                "alias"     => $font->name,
                "type"      => "google", 
                "subset"    => $subset, 
                "file"      => $file, 
                "cat"       => array("99")
            );
        };
        //must add default font for each subset
        file_put_contents($path_gg_font, json_encode($gg_fonts));
    }
}