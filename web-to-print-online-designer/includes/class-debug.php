<?php
if ( ! defined( 'ABSPATH' ) ) exit;
class Nbdesigner_DebugTool {
    /**
     * Before use log() enable config log in wp-config.php in root folder
     * If can't modified wp-config.php use function wirite_log() or manual_write_debug()
     * @param type $data
     */
    static private $_path = NBDESIGNER_LOG_DIR;
    public function __construct($path = ''){
        if($path != ''){
            self::$_path = $path;
        }else{
            self::$_path = NBDESIGNER_PLUGIN_DIR;
        }
    }
    public static function log($data){
        if(NBDESIGNER_MODE_DEBUG){
            ob_start();
            var_dump($data);
            error_log(ob_get_clean());
        }else{
            return FALSE;
        }
    }
    public static function wirite_log($data, $title){
        if(nbdesigner_get_option('nbdesigner_enable_log' == 'yes')){
            error_reporting( E_ALL );
            ini_set('log_errors', 1);
            ini_set('error_log', self::$_path . '/debug.log');
            error_log('Start debug - '. $title);
            ob_start();
            var_dump($data);
            error_log(ob_get_clean());
            error_log('End debug - '. $title);
        }else{
            return FALSE;
        }
    }
    public static function manual_write_debug($data){
        $path = self::$_path . '/debug.log';
        $data = print_r($data, true);
        if (NBDESIGNER_MODE_DEBUG) {
            if (!$fp = fopen($path, 'w')) {
                return FALSE;
            }
            flock($fp, LOCK_EX);
            fwrite($fp, $data);
            flock($fp, LOCK_UN);
            fclose($fp);
            return TRUE;
        } else {
            return FALSE;
        }
    }
    public static function manual_write_debug2($data){
        $data = print_r($data, true);
        $path = self::$_path . '/debug.txt';
        file_put_contents( $path, $data . "\n\n", FILE_APPEND );
    }
    public static function console_log($data){
        echo '<script>';
        echo 'console.log('. json_encode( $data ) .')';
        echo '</script>';
    }
    public static function theme_check_hook(){
        if (!wp_verify_nonce($_POST['_nbdesigner_check_theme_nonce'], 'nbdesigner-check-theme-key') || !current_user_can('administrator')) {
            die('Security error');
        }
        $result = array();
        $theme_path = get_template_directory();
        $theme = wp_get_theme();
        $result['html'] = '';
        $list_filter = array(
            'woocommerce_before_add_to_cart_button' => '/single-product/add-to-cart/grouped.php', 
            'woocommerce_before_add_to_cart_button' => '/single-product/add-to-cart/external.php', 
            'woocommerce_before_add_to_cart_button' => '/single-product/add-to-cart/simple.php', 
            'woocommerce_before_add_to_cart_button' => '/single-product/add-to-cart/variable.php', 
            'woocommerce_cart_item_name'            => '/cart/cart.php', 
            'woocommerce_order_item_name'           => '/order/order-details-item.php', 
            'woocommerce_order_item_quantity_html'  => '/order/order-details-item.php');
        $folder_woo = $theme_path . '/woocommerce';
        if(!file_exists($folder_woo)){
            $result['flag'] = 'ok';
            $result['html'] .= '<p class="nbd-debug-theme-ok">' . esc_html__('Your theme (', 'web-to-print-online-designer') . esc_html( $theme['Name']) . esc_html__(') compatible with plugin.', 'web-to-print-online-designer') .'</p>';
        }else{
            $result['flag'] = 'ok';
            $result['html'] .= '<h3>' . esc_html__('Your theme "', 'web-to-print-online-designer') . esc_html( $theme['Name']). '"</h3>';
            foreach ($list_filter as $key => $val){
                $path = $folder_woo . $val;
                if(file_exists($path)){
                    $fp = fopen( $path, 'r' );
                    $file_data = fread($fp, filesize($path));
                    fclose( $fp );
                    $pattern = '/'.$key.'/';
                    if ( preg_match($pattern, $file_data, $match)){
                        $result['html'] .= '<p class="nbd-debug-theme-found-p"><span class="nbd-debug-theme-found-span">'.$key.'</span>' . esc_html__(' was found', 'web-to-print-online-designer') . '</p>';
                    }else{
                        $result['html'] .= '<div class="nbd-debug-theme-missing-div"><p><span class="nbd-debug-theme-missing-span">'.$key.'</span>' . esc_html__(' is missing', 'web-to-print-online-designer') . '</p>';
                        $result['html'] .= esc_html__('The ', 'web-to-print-online-designer') . $val . esc_html__(' in the woocommerce templates of your theme does not include the required action/filter: ', 'web-to-print-online-designer') . $key . '<p></p></div>';
                    }
                }
            }
        }
        echo json_encode($result);
        wp_die();   
    }
    public static function update_data_migrate_domain(){
        $result = array(
            'mes'   =>  esc_html__('You do not have permission to update data!', 'web-to-print-online-designer'),
            'flag'  => 0
        );	        
        if (!wp_verify_nonce($_POST['_nbdesigner_migrate_nonce'], 'nbdesigner-migrate-key') || !current_user_can('update_nbd_data')) {
            echo json_encode($data);
            wp_die();
        } 
        if(isset($_POST['old_domain']) && $_POST['old_domain'] != '' && isset($_POST['new_domain']) && $_POST['new_domain'] != ''){
            $old_domain         = rtrim($_POST['old_domain'], '/');
            $new_domain         = rtrim($_POST['new_domain'], '/');
            $upload_dir         = wp_upload_dir();
            $path               = $upload_dir['basedir'] . '/nbdesigner/';            
            $files              = array("arts", "fonts");
            $path_backup_folder = $path . 'backup';
            if(!file_exists($path_backup_folder)) wp_mkdir_p ($path_backup_folder);
            $_files = glob($path_backup_folder.'/*');
            foreach($_files as $file){
                if(is_file($file)) unlink($file); 
            }   
            $result['flag'] = 1;
            $result['mes']  = esc_html__("Success!", 'web-to-print-online-designer');
            foreach ($files as $file){
                $fullname = $path . $file . '.json';
                if (file_exists($fullname)) {
                    $backup_file = $path_backup_folder . '/' . $file . '.json';
                    if(copy($fullname,$backup_file)){
                        $list = json_decode(file_get_contents($fullname));
                        foreach ($list as $l){
                            $name_arr       = explode('/uploads/', $l->file);
                            $new_file_name  = $upload_dir['basedir'] . '/' . $name_arr[1];
                            $new_url        = str_replace($old_domain, $new_domain, $l->url);
                            $l->file        = $new_file_name;
                            $l->url         = $new_url;
                        }
                        if(!file_put_contents($fullname, json_encode($list))){
                            $result['flag'] = 0;
                            $result['mes']  = esc_html__("Erorr write data!", 'web-to-print-online-designer');
                        }
                    }else{
                        $result['flag'] = 0;
                        $result['mes']  = esc_html__("Erorr backup!", 'web-to-print-online-designer');
                    }
                }
            }
        }else{
            $result['flag'] = 0;
            $result['mes']  = esc_html__("Invalid info!", 'web-to-print-online-designer');
        }
        echo json_encode($result);
        wp_die();
    }
    public static function restore_data_migrate_domain(){
        $result = array(
            'mes'   =>  esc_html__('You do not have permission to update data!', 'web-to-print-online-designer'),
            'flag'  => 0
        );	         
        if (!wp_verify_nonce($_POST['nonce'], 'nbdesigner_add_cat') || !current_user_can('update_nbd_data')) {
            echo json_encode($result);
            wp_die();
        } 
        $result         = array();
        $result['flag'] = 1;
        $result['mes']  = esc_html__("Restore success!", 'web-to-print-online-designer');
        $upload_dir     = wp_upload_dir();
        $path           = $upload_dir['basedir'] . '/nbdesigner/';          
        $files          = array("arts", "fonts");
        foreach ($files as $file){
            $fullname   = $path . $file . '.json';
            $backup     = $path .'backup/'. $file . '.json';
            if (file_exists($fullname) && file_exists($backup)) {
                if(unlink($fullname)){
                    copy($backup,$fullname);
                }
            }else{
                $result['flag'] = 0;
                $result['mes'] = esc_html__("Files not exist!", 'web-to-print-online-designer');
            }
        }
        echo json_encode($result);
        wp_die();        
    }
    public static function save_custom_css(){
        $result = array(
            'mes'   => esc_html__('You do not have permission to update data!', 'web-to-print-online-designer'),
            'flag'  => 0
        );
        if (!wp_verify_nonce($_POST['_nbdesigner_custom_css'], 'nbdesigner-custom-css') || !current_user_can('administrator')) {
            echo json_encode($result);
            wp_die();   
        } 
        $custom_css = '';
        $path = NBDESIGNER_DATA_DIR .'/custom.css';
        if(isset($_POST['content'])){
            $custom_css     = stripslashes( $_POST['content'] );
            $fp             = fopen($path, "w");
            fwrite($fp, $custom_css);
            fclose($fp);
            $result['flag'] = 1;
            $result['mes']  = esc_html__('Your CSS has been saved!', 'web-to-print-online-designer');
        }
        echo json_encode($result);
        wp_die();
    }
    public static function save_custom_js(){
        $result = array(
            'mes'   =>  esc_html__('You do not have permission to update data!', 'web-to-print-online-designer'),
            'flag'  => 0
        );
        if (!wp_verify_nonce($_POST['_nbdesigner_custom_css'], 'nbdesigner-custom-css') || !current_user_can('administrator')) {
            echo json_encode($result);
            wp_die();   
        } 
        $custom_js  = '';
        $path       = NBDESIGNER_DATA_DIR .'/custom.js';
        if(isset($_POST['content'])){
            $custom_js  = stripslashes( $_POST['content'] );
            $fp         = fopen($path, "w");
            fwrite($fp, $custom_js);
            fclose($fp);
            $result['flag'] = 1;
            $result['mes']  = esc_html__('Your JS has been saved!', 'web-to-print-online-designer');
        }
        echo json_encode($result);
        wp_die();
    }
    public static function get_custom_css(){
        $custom_css = '';
        $path = file_exists( NBDESIGNER_DATA_DIR . '/custom.css' ) ? NBDESIGNER_DATA_DIR .'/custom.css' : NBDESIGNER_PLUGIN_DIR .'assets/css/custom.css';
        if(file_exists($path)){
            $fp = fopen( $path, 'r' );
            if( filesize($path) ){
                $custom_css = fread($fp, filesize($path));
                fclose( $fp );
            }
        }
        return $custom_css;
    }
    public static function get_custom_js(){
        $custom_js = '';
        $path = file_exists( NBDESIGNER_DATA_DIR . '/custom.js' ) ? NBDESIGNER_DATA_DIR .'/custom.js' : NBDESIGNER_PLUGIN_DIR .'assets/js/custom.js';
        if(file_exists($path)){
            $fp = fopen( $path, 'r' );
            if( filesize($path) ){
                $custom_js = fread($fp, filesize($path));
                fclose( $fp );
            }
        }
        return $custom_js;
    }
    public static function nbd_var_dump($param, $force_die = false){
        echo __FILE__;
        echo '<pre>';
        var_dump($param);
        echo '</pre>';
        if( $force_die ) die('~End~');
    }
    public static function nbd_fix_pdf_font(){
        $alias      = wc_clean( $_POST['alias'] );
        $type       = wc_clean( $_POST['type'] );
        $result     = true;
        if( $type == 'ttf' ){
            $font = nbd_get_font_by_alias( $alias );
            foreach( $font->file as $key => $font_file ){
                $path_font[$key] = NBDESIGNER_FONT_DIR . '/' . $font_file;
            }
        } else {
            $path_font = nbd_download_google_font( $alias );
        }
        foreach( $path_font as $pfont ){
            $r = ( new self )->upload_pdf_font( $pfont );
            $result = $r && $result;
        }
        wp_send_json( array(
            'flag'  =>  $result ? 1 : 0
        ) );
    }
    public function upload_pdf_font( $font_file ){
        $result = false;
        $url = "http://fonts.snm-portal.com/upload.php";
        $headers = array("Content-Type:multipart/form-data");
        $ch = curl_init();
        $cfile = new CURLFile(realpath($font_file));
        $postfields = array(
            'files' => $cfile,
            'type' => 'ttf,otf',
            'crop' => 'true',
            'quality' => 60
        );
        $options = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $postfields,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => "Mozilla/4.0 (compatible;)"
        );
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            if ($info['http_code'] == 200) {
                $data = json_decode($response);
                if( isset( $data[0] ) && isset( $data[0]->error ) && $data[0]->error != 1 ){
                    $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";
                    if ( preg_match_all( "/$regexp/siU", $data[0]->error, $matches ) ) {
                        if ( isset( $matches[2] ) ) {
                            $result = true;
                            foreach ( $matches[2] as $link ) {
                                $filename = basename( $link );
                                if( strpos( $link, '.php' ) !== false ){
                                    $filename = strtolower( pathinfo( $font_file, PATHINFO_FILENAME ) );
                                    $filename = str_replace(' ', '', $filename) . '.php';
                                }
                                $link = "http://fonts.snm-portal.com/" . $link;
                                $path = K_PATH_FONTS . $filename;
                                $r = nbd_download_remote_file( $link, $path );
                                $result = $r && $result;
                            }
                        }
                    }
                }
            }
        } else {
            $errmsg = curl_error($ch);
            echo $errmsg;
        }
        curl_close($ch);
        return $result;
    }
}