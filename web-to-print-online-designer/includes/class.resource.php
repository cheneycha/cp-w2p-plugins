<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if(!class_exists('NBD_RESOURCE')){
    class NBD_RESOURCE {
        protected static $instance;
        public function __construct() {
            //todo something
        }
	public static function instance() {
            if ( is_null( self::$instance ) ) {
                    self::$instance = new self();
            }
            return self::$instance;
	}        
        public function init(){
            if (is_admin()) {
                $this->ajax();
            }
        }   
        public function ajax(){
            $ajax_events = array(
                'nbd_get_resource'    => true
            );
            foreach ($ajax_events as $ajax_event => $nopriv) {
                add_action('wp_ajax_' . $ajax_event, array($this, $ajax_event));
                if ($nopriv) {
                    add_action('wp_ajax_nopriv_' . $ajax_event, array($this, $ajax_event));
                }
            }        
        }
        public function nbd_get_resource(){
            $flag = 1;
            $data = array();
            if (!wp_verify_nonce($_REQUEST['nonce'], 'nbdesigner-get-data') && NBDESIGNER_ENABLE_NONCE) {
                //todo something
            }else{     
                $rq_type = wc_clean( $_REQUEST['type'] );
                switch ($rq_type) {
                    case 'typography':
                        $path = $_REQUEST['task'] == 'typography' ? NBDESIGNER_PLUGIN_DIR . '/data/typography/typography.json' : NBDESIGNER_PLUGIN_DIR . '/data/typography/typo.json';
                        if(file_exists($path) ) $data = json_decode( file_get_contents($path) );
                        break;  
                    case 'get_typo':
                        $path = NBDESIGNER_PLUGIN_DIR . '/data/typography/store/'.$_REQUEST['folder'];
                        $data['font'] = json_decode( file_get_contents($path.'/used_font.json') );
                        $data['design'] = json_decode( file_get_contents($path.'/design.json') );
                        break;
                    case 'clipart':
                        $path_cat = NBDESIGNER_DATA_DIR . '/art_cat.json';
                        $path_art = NBDESIGNER_DATA_DIR . '/arts.json'; 
                        $data['cat'] = $data['arts'] = array();
                        if( file_exists($path_cat) ){
                            $_cat = file_get_contents($path_cat);
                            $data['cat'] = $_cat == '' ? array() : json_decode($_cat);
                        }
                        if( file_exists($path_art) ){
                            $_art = file_get_contents($path_art);
                            $data['arts'] = $_art == '' ? array() : json_decode($_art);
                        }
                        break;
                    case 'save_typography':
                        $path = NBDESIGNER_PLUGIN_DIR . 'data/typography/typo.json';
                        $folder = substr(md5(uniqid()),0,5).rand(1,100).time();
                        $store_path = NBDESIGNER_PLUGIN_DIR . 'data/typography/store/' . $folder;
                        if( !file_exists($store_path) ) wp_mkdir_p ($store_path);
                        $design_path = $store_path . '/design.json';
                        $used_font_path = $store_path . '/used_font.json';
                        $preview_path = $store_path . '/preview.png';
                        $list_typo = array();
                        if(file_exists($path) ) $list_typo = json_decode(file_get_contents ($path));
                        foreach ($_FILES as $key => $val) {
                            switch($key){
                                case 'design':
                                    $full_name = $design_path;
                                    break;
                                case 'used_font':
                                    $full_name = $used_font_path;
                                    break;
                                case 'frame_0':
                                    $full_name = $preview_path;
                                    break;
                            };
                            if ( !move_uploaded_file($val["tmp_name"],$full_name) ) {
                                $flag = 0;
                            }
                        };
                        if( $flag ){
                            $id = isset($_REQUEST['id']) ? absint($_REQUEST['id'] - 1) : count($list_typo);
                            $new_typo = array(
                                'id'        => $id,
                                'folder'    => $folder
                            );
                            $exist_id = -1;
                            foreach ($list_typo as $index => $typo){
                                if( $typo->id == $id ){
                                    $exist_id = $index; break;
                                }
                            }
                            if($exist_id > -1){
                                $list_typo[$exist_id] = $new_typo;
                            }else{
                                $list_typo[] = $new_typo;
                            }
                            file_put_contents($path, json_encode($list_typo));
                            $data['typo'] = $list_typo;
                        }
                        break;
                    case 'google_font':
                        $all_gg_fonts = json_decode(file_get_contents(NBDESIGNER_PLUGIN_DIR. '/data/google-fonts-ttf.json'))->items;
                        $font_name = $_REQUEST['font_name'];
                        $subset = 'all';
                        $file = array('r' => 1);
                        $flag = 0;
                        foreach( $all_gg_fonts as $f ){
                            if( $font_name == $f->family || str_replace(" ","",$font_name) == $f->family ){
                                if( str_replace(" ","",$font_name) == $f->family ) $font_name = str_replace(" ","",$font_name);
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
                                $flag = 1;
                                break;
                            }
                        }
                        $data = array(
                            "id"        => 99,
                            "name"      => $font_name,
                            "alias"     => $font_name,
                            "type"      => "google", 
                            "subset"    => $subset, 
                            "file"      => $file, 
                            "cat"       => array("99")
                        );
                        break;
                    case 'save_user_design':
                        global $wpdb;
                        $created_date = new DateTime();
                        $user_id = wp_get_current_user()->ID;
                        $table_name =  $wpdb->prefix . 'nbdesigner_user_designs';
                        $folder = substr(md5(uniqid()),0,5).rand(1,100).time();
                        $store_path = NBDESIGNER_DATA_DIR . '/designs/' . $folder;
                        if( !file_exists($store_path) ) wp_mkdir_p ($store_path);
                        $design_path = $store_path . '/design.json';
                        $preview_path = $store_path . '/preview.png';
                        foreach ($_FILES as $key => $val) {
                            switch($key){
                                case 'design': 
                                    $full_name = $design_path;
                                    break; 
                                case 'preview': 
                                    $full_name = $preview_path;
                                    break;
                            };
                            if ( !move_uploaded_file($val["tmp_name"],$full_name) ) {      
                                $flag = 0;
                            }
                        }
                        if( $flag ){
                            $rerult = $wpdb->insert($table_name, array(
                                'folder'        => $folder,
                                'user_id'       => $user_id,
                                'created_date'  => $created_date->format('Y-m-d H:i:s')
                            ));
                            if($rerult){
                                $data['preview'] = Nbdesigner_IO::wp_convert_path_to_url( $preview_path );
                                $data['id'] = $wpdb->insert_id;
                            }else{
                                $flag = 0;
                            }
                        }
                        break;
                    case 'load_user_designs':
                        global $wpdb;
                        $user_id = wp_get_current_user()->ID;
                        $table_name =  $wpdb->prefix . 'nbdesigner_user_designs';
                        $templates = $wpdb->get_results( "SELECT * FROM {$table_name} WHERE user_id = {$user_id} ORDER BY created_date DESC", 'ARRAY_A' );
                        if( $templates ){
                            $data['user_designs'] = array();
                            foreach ($templates as $template){
                                $data['user_designs'][] = array(
                                    'id'        => $template['id'],
                                    'preview'   => Nbdesigner_IO::wp_convert_path_to_url( NBDESIGNER_DATA_DIR . '/designs/' . $template['folder'] . '/preview.png' )
                                );
                            }
                        }else{
                            $flag = 0;
                        }
                        break;
                    case 'load_user_design':
                        global $wpdb;
                        $template_id = absint($_POST['template_id']);
                        $user_id = wp_get_current_user()->ID;
                        $table_name =  $wpdb->prefix . 'nbdesigner_user_designs';
                        $template = $wpdb->get_row( "SELECT * FROM {$table_name} WHERE id = {$template_id}" );
                        if( $template ){
                            $store_path = NBDESIGNER_DATA_DIR . '/designs/' . $template->folder;
                            $design_path = $store_path . '/design.json';
                            $data['design'] = json_decode( file_get_contents($design_path) );
                        }else{
                            $flag = 0;
                        }
                        break;
                    case 'delete_user_design':
                        global $wpdb;
                        $template_id = absint($_POST['template_id']);
                        $table_name =  $wpdb->prefix . 'nbdesigner_user_designs';
                        $result = $wpdb->delete("$table_name", array('id' => $template_id));
                        if( !$result ) $flag = 0;
                        break;
                    case 'get_mockup':
                        $_mockups = $_REQUEST['mockups'];
                        $folder = $_REQUEST['folder'];
                        $mockups = explode('|', $_mockups);
                        $path = $path = NBDESIGNER_CUSTOMER_DIR . '/' . $folder . '/';
                        $files = array();
                        foreach($mockups as $mockup){
                            if(file_exists($path.$mockup)){
                                $files[] = $path.$mockup;
                            }
                        }
                        $pathZip = NBDESIGNER_DATA_DIR.'/download/customer-design-'.time().'.zip';
                        if(count($files)){
                            nbd_zip_files_and_download($files, $pathZip, 'mockups.zip', array(), false);
                            $data['url'] = Nbdesigner_IO::wp_convert_path_to_url( $pathZip );
                        }else{
                            $flag = 0;
                        }
                        break;
                    case 'get_flaticon_token':
                        $flaticon_token = get_transient( 'nbd_flaticon_token' );
                        if( false === $flaticon_token ){
                            $apikey = nbdesigner_get_option( 'nbdesigner_flaticon_api_key', '' );
                            $url    = 'https://api.flaticon.com/v2/app/authentication';
                            $headers = array(
                                'accept'        => 'application/json'
                            );
                            $payload = array(
                                'timeout'   => 30,
                                'headers'   => $headers,
                                'body'      => array(
                                    'apikey'    => $apikey
                                )
                            );
                            $response   = wp_remote_post( $url, $payload );
                            $data       = array();
                            if ( !is_wp_error( $response ) ) {
                                $res                = json_decode($response['body'])->data;
                                $data['token']      = $res->token;
                                $data['expires']    = (int)$res->expires;
                                $flag               = 1;
                                set_transient( 'nbd_flaticon_token', $data, $data['expires'] - time() );
                            }else{
                                $flag = 0;
                            }
                        } else {
                            $flag = 1;
                            $data = $flaticon_token;
                        }
                        break;
                }
                //custom charmingprint
                /*NBDesigner Advanced*/

                //$return = apply_filters('nbod_get_resource_before_send',array('flag' => $flag,'data'=>$data),$rq_type);
                /*CODTeam task copy/paste*/
                $return = apply_filters('nbod_get_resource_before_send',array('flag' => $flag,'data'=>$data),$rq_type,$_REQUEST,$_FILES);
                $flag = $return['flag'];
                $data = $return['data'];
            }
            wp_send_json(
                array( 
                    'flag' =>  $flag, 
                    'data'  =>  $data
                )
            );
        }
    }
}
$nbd_resource = NBD_RESOURCE::instance();
$nbd_resource->init();