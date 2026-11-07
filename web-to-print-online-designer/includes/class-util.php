<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
use setasign\Fpdi;
/**
 * Necessary I/O functions
 * 
 */
class Nbdesigner_IO {
    public function __construct() {
        //TODO
    }
    /**
     * Get all images in folder by level
     * 
     * @param string $path path folder
     * @param int $level level scan dir
     * @return array Array path images in folder
     */
    public static function get_list_images( $path, $level = 100 ){
        $list       = array();
        $_list      = self::get_list_files($path, $level);
        $list       = preg_grep('/\.(jpg|jpeg|png|gif)(?:[\?\#].*)?$/i', $_list);
        return $list;
    }
    public static function get_list_files_by_type($path, $level = 100, $type = ''){
        $list       = array();
        $_list      = self::get_list_files($path, $level);
        $list       = preg_grep('/\.(' . $type . ')(?:[\?\#].*)?$/i', $_list);
        return $list;
    }
    public static function get_list_files( $folder = '', $levels = 100 ) {
        if ( empty( $folder ) ) return false;
        if ( !$levels ) return false;
        $files = array();
        if ( $dir = @opendir( $folder ) ) {
            while ( ( $file = readdir( $dir ) ) !== false ) {
                if ( in_array( $file, array( '.', '..' ) ) )
                    continue;
                if ( is_dir( $folder . '/' . $file ) ) {
                    $files2 = self::get_list_files( $folder . '/' . $file, $levels - 1 );
                    if ( $files2 )
                        $files = array_merge( $files, $files2 );
                    else
                        $files[] = $folder . '/' . $file . '/';
                } else {
                    $files[] = $folder . '/' . $file;
                }
            }
        }
        @closedir( $dir );
        return $files;
    }
    public static function get_list_folder( $folder = '', $levels = 100 ){
        if ( empty( $folder ) ) return false;
        if ( !$levels ) return false;
        $folders = array();
        if ( $dir = @opendir( $folder ) ) {
            while ( ( $file = readdir( $dir ) ) !== false ) {
                if ( in_array( $file, array( '.', '..' ) ) )
                    continue;
                if ( is_dir( $folder . '/' . $file ) ) {
                    $folders2 = self::get_list_folder( $folder . '/' . $file, $levels - 1 );
                    if ( $folders2 ){
                        $folders = array_merge( $folders, $folders2 );
                    }else {
                        $folders[] = $folder . '/' . $file . '/';
                    }
                }    
            }
        }
        @closedir( $dir );
        return $folders;
    }
    public static function delete_folder( $path ) {
        if ( is_dir( $path ) === true ) {
            $files = array_diff( scandir( $path ), array( '.', '..' ) );
            foreach ( $files as $file ) {
                self::delete_folder( realpath( $path ) . '/' . $file );
            }
            return rmdir( $path );
        } else if ( is_file( $path ) === true ) {
            return unlink( $path );
        }
        return false;
    } 
    public static function copy_dir( $src, $dst ) {
        if ( file_exists( $dst ) ) self::delete_folder( $dst );
        if ( is_dir( $src ) ) {
            wp_mkdir_p( $dst );
            $files = scandir( $src );
            foreach ( $files as $file ){
                if ( $file != "." && $file != ".." ) self::copy_dir( "$src/$file", "$dst/$file" );
            }
        } else if ( file_exists( $src ) ) copy( $src, $dst );
    } 
    public static function mkdir( $dir ){
        if (!file_exists($dir)) {
            wp_mkdir_p($dir);
        }
    }
    public static function clear_file( $path ){
        $f = @fopen( $path, "r+" );
        if ( $f !== false ) {
            ftruncate( $f, 0 );
            fclose( $f );
        }
    }
    public static function create_index_html( $path ){
        if (!file_exists($path)) {
            $content = __('Silence is golden.', 'web-to-print-online-designer');
            file_put_contents($path, $content);
        }
    }
    public static function create_file_path( $upload_path, $filename, $ext='', $force_override = false ){
        $date_path = '';
        if (!file_exists($upload_path)) mkdir($upload_path);
        $year = !@date('Y') ? gmdate('Y') : date('Y');
        $date_path .= '/' . $year . '/';
        if (!file_exists($upload_path . $date_path)) mkdir($upload_path . $date_path);
        $month = !@date('m') ? gmdate('m') : date('m');
        $date_path     .= $month . '/';
        if (!file_exists($upload_path . $date_path)) mkdir($upload_path . $date_path);
        $day = !@date('d') ? gmdate('d') : date('d');
        $date_path     .= $day . '/';
        if (!file_exists($upload_path . $date_path)) mkdir($upload_path . $date_path);
        $file_path      = $upload_path . $date_path . $filename;
        $file_counter   = 1;
        $real_filename  = $filename;
        if($force_override){
            if(file_exists($file_path . '.' . $ext) ) unlink($file_path . '.' . $ext);
        }else{
            while (file_exists($file_path . '.' . $ext)) {
                $real_filename  = $file_counter . '-' . $filename;
                $file_path      = $upload_path . $date_path . $real_filename;
                $file_counter++;
            }
        }
        return array(
            'full_path' => $file_path,
            'date_path' => $date_path . $real_filename
        );
    }
    public static function secret_image_url( $file_path ){
        $type       = pathinfo( $file_path, PATHINFO_EXTENSION );
        $data       = file_get_contents( $file_path );
        $base64     = 'data:image/' . $type . ';base64,' . base64_encode( $data );
        return $base64;
    }
    /**
     * @deprecated 1.7.0 <br />
     * From 1.7.0 alternate by function wp_convert_path_to_url( $path )
     * @param type $path
     * @return string url
     */
    public static function convert_path_to_url( $path ){
        $upload_dir = wp_upload_dir();
        $basedir    = $upload_dir['basedir'];
        $arr        = explode('/', $basedir);
        $upload     = $arr[count($arr) - 1];
        if( is_multisite() && !is_main_site() ) $upload = $arr[count($arr) - 3].'/'.$arr[count($arr) - 2].'/'.$arr[count($arr) - 1];
        return content_url( substr( $path, strrpos( $path, '/' . $upload . '/nbdesigner' ) ) );
    }
    /**
     * @deprecated 1.7.0
     * From 1.7.0 alternate by WP function wp_make_link_relative( $url )
     * @param type $url
     * @return path
     */
    public static function convert_url_to_path( $url ){
        $upload_dir     = wp_upload_dir();
        $basedir        = $upload_dir['basedir'];
        $arr            = explode( '/', $basedir );
        $upload         = $arr[count($arr) - 1];
        if( is_multisite() && !is_main_site() ) $upload = $arr[count($arr) - 3].'/'.$arr[count($arr) - 2].'/'.$arr[count($arr) - 1];
        $arr_url = explode('/'.$upload, $url);
        if( isset( $arr_url[1] ) ){
            if( count( $arr_url ) == 2 ){
                return $basedir.$arr_url[1];
            } else {
                return $basedir.$arr_url[1] . '/' . $upload . $arr_url[2];
            }
        }else{
            $path = str_replace(
                site_url(),
                wp_normalize_path( untrailingslashit( ABSPATH ) ),
                wp_normalize_path( $url )
            );
            return $path;
        }
    }
    public static function wp_convert_path_to_url( $path = '' ){
        $url = str_replace(
            wp_normalize_path( untrailingslashit( ABSPATH ) ),
            site_url(),
            wp_normalize_path( $path )
        );
        return esc_url_raw( $url );
//        $url = self::convert_path_to_url($path);
//        return $url;
    }
    public static function save_data_to_file( $path, $data ){
        if ( !$fp = fopen( $path, 'w' ) ) {
            return FALSE;
        }
        flock( $fp, LOCK_EX );
        fwrite( $fp, $data );
        flock( $fp, LOCK_UN );
        fclose( $fp );
        return TRUE;
    }
    public static function checkFileType( $file_name, $arr_mime ) {
        $check      = false;
        $filetype   = explode( '.', $file_name );
        $file_exten = $filetype[count( $filetype ) - 1];
        if ( in_array( strtolower( $file_exten ), $arr_mime ) ) $check = true;
        return $check;
    }
    public static function get_thumb_file( $ext, $path = '' ){
        $thumb = '';
        switch ( $ext ) {
            case 'jpg':
            case 'jpeg':
                $thumb = NBDESIGNER_ASSETS_URL . 'images/file_type/jpg.png';
                break;
            case 'png':
                $thumb = NBDESIGNER_ASSETS_URL . 'images/file_type/png.png';
                break;
            case 'psd':
                $thumb = NBDESIGNER_ASSETS_URL . 'images/file_type/psd.png';
                break;
            case 'pdf':
                $thumb = NBDESIGNER_ASSETS_URL . 'images/file_type/pdf.png';
                break;
            case 'ai':
                $thumb = NBDESIGNER_ASSETS_URL . 'images/file_type/ai.png';
                break;
            case 'eps':
                $thumb = NBDESIGNER_ASSETS_URL . 'images/file_type/eps.png';
                break;
            case 'zip':
                $thumb = NBDESIGNER_ASSETS_URL . 'images/file_type/zip.png';
                break; 
            case 'svg':
                $thumb = NBDESIGNER_ASSETS_URL . 'images/file_type/svg.png';
                break;
            default: 
                $thumb = NBDESIGNER_ASSETS_URL . 'images/file_type/file.png';
                break;
        }
        return $thumb;
    }
    public static function read_json_setting( $fullname ) {
        if ( file_exists( $fullname ) ) {
            $list = json_decode( file_get_contents( $fullname ) );
        } else {
            $list = '[]';
            file_put_contents( $fullname, $list );
        }
        return $list;
    }
    public static function delete_json_setting( $fullname, $id, $reindex = true ) {
        $list = self::read_json_setting( $fullname );
        if ( is_array( $list ) ) {
            array_splice( $list, $id, 1 );
            if ( $reindex ) {
                $key = 0;
                foreach ( $list as $val ) {
                    $val->id = (string) $key;
                    $key++;
                }
            }
        }
        $res = json_encode( $list );
        file_put_contents( $fullname, $res );
    }
    public static function update_json_setting( $fullname, $data, $id ) {
        $list = self::read_json_setting( $fullname );
        if ( is_array( $list ) )
            $list[$id] = $data;
        else {
            $list = array();
            $list[] = $data;
        }
        $_list = array();
        foreach ( $list as $val ) {
            $_list[] = $val;
        }
        $res = json_encode( $_list );
        file_put_contents( $fullname, $res );
    }
    public static function update_json_setting_depend( $fullname, $id ) {
        $list = self::read_json_setting( $fullname );
        if ( !is_array( $list ) ) return;
        foreach ( $list as $val ) {
            if ( !( ( sizeof( $val ) > 0 ) ) ) continue;
            foreach ( $val->cat as $k => $v ) {
                if ( $v == $id ) {
                    array_splice( $val->cat, $k, 1 );
                    break;
                }
            }
            foreach ( $val->cat as $k => $v ) {
                if ( $v > $id ) {
                    $new_v = (string) --$v;
                    unset( $val->cat[$k] );
                    array_splice( $val->cat, $k, 0, $new_v );
                }
            }
        }
        $res = json_encode( $list );
        file_put_contents( $fullname, $res );
    }
}
class NBD_Image {
    public static function nbdesigner_resize_imagepng( $file, $w, $h, $path = '' ){
        list($width, $height)   = getimagesize( $file );
        if( $path != '' ) $h    = (int) round( $w / $width * $height );
        $w   = (int) round( $w );
        $h   = (int) round( $h );
        $src = imagecreatefrompng( $file );
        $dst = imagecreatetruecolor( $w, $h );
        imagesavealpha( $dst, true );
        $color = imagecolorallocatealpha( $dst, 255, 255, 255, 127 );
        imagefill( $dst, 0, 0, $color );
        imagecopyresampled( $dst, $src, 0, 0, 0, 0, $w, $h, (int) $width, (int) $height );
        imagedestroy( $src );
        if( $path == '' ){
            return $dst;
        } else{
            imagepng( $dst, $path );
            imagedestroy( $dst );
        }
    }
    public static function nbdesigner_resize_imagejpg( $file, $w, $h, $path = '' ) {
        if( is_available_imagick() && $path != '' ){
            self::imagick_resize_image($file, $path, (int) round($w), (int) round($h) );
        } else {
            list($width, $height) = getimagesize($file);
            if( $path != '' ) $h = (int) round( $w / $width * $height );
            $w   = (int) round( $w );
            $h   = (int) round( $h );
            $src = imagecreatefromjpeg($file);
            $dst = imagecreatetruecolor($w, $h);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, $w, $h, (int) $width, (int) $height);
            imagedestroy($src);
            if( $path == '' ){
                return $dst;
            } else{
                imagejpeg($dst, $path );
                imagedestroy($dst);
            }
        }
    }
    public static function crop_and_resize_png_image( $file, $w, $h, $top = 0, $left = 0, $src_w = null, $src_h = null ){
        list( $width, $height ) = getimagesize( $file );
        $src_w  = is_null( $src_w ) ? $width : $src_w;
        $src_h  = is_null( $src_h ) ? $width : $src_h;
        $w      = (int) round( $w );
        $h      = (int) round( $h );
        $left   = (int) round( $left );
        $top    = (int) round( $top );
        $src    = imagecreatefrompng( $file );
        $dst    = imagecreatetruecolor( $w, $h );
        imagesavealpha( $dst, true );
        $color = imagecolorallocatealpha( $dst, 255, 255, 255, 127 );
        imagefill( $dst, 0, 0, $color );
        imagecopyresampled( $dst, $src, 0, 0, $left, $top, $w, $h, (int) $width, (int) $height );
        imagedestroy( $src );
        return $dst;
    }
    public static function crop_and_resize_jpg_image( $file, $w, $h, $top = 0, $left = 0, $src_w = null, $src_h = null ){
        list( $width, $height ) = getimagesize( $file );
        $src_w  = is_null( $src_w ) ? $width : $src_w;
        $src_h  = is_null( $src_h ) ? $width : $src_h;
        $w      = (int) round( $w );
        $h      = (int) round( $h );
        $left   = (int) round( $left );
        $top    = (int) round( $top );
        $src    = imagecreatefromjpeg($file);
        $dst    = imagecreatetruecolor( $w, $h );
        imagecopyresampled( $dst, $src, 0, 0, $left, $top, $w, $h, (int) $width, (int) $height );
        imagedestroy($src);
        return $dst;
    }
    public static function imagick_resize_image( $src, $dst, $width, $height ){
        $image = new Imagick( $src );
        $image->resizeImage( $width, $height, imagick::FILTER_LANCZOS, 1, true);
        $image->writeImage( $dst );
        $image->clear();
        $image->destroy();
    }
    public static function rotate_image( $src, $deg ){
        if( is_available_imagick() ){
            $img = new Imagick( $src );
            $img->rotateImage(new \ImagickPixel(), $deg);
            $img->writeImage( $src );
            $img->destroy();
        } else {
            $img = imagecreatefromjpeg( $src );
            if ( $deg ) {
                $img = imagerotate( $img, $deg, 0 );
                imagejpeg( $img, $src, 100 );
                imagedestroy( $img );
            }
        }
    }
    public static function strip_exif_orientation( $src, $orientation ){
        $deg = 0;
        $is_available_imagick = is_available_imagick();
        switch ( $orientation ) {
            case 3:
                $deg = 180;
                break;
            case 6:
                $deg = $is_available_imagick ? 90 : 270;
                break;
            case 8:
                $deg = $is_available_imagick ? 270 : 90;
                break;
        }
        if( $is_available_imagick ){
            $img = new Imagick( $src );
            $img->stripImage();
            $img->rotateImage( new \ImagickPixel(), $deg );
            $img->writeImage( $src );
            $img->destroy();
        } else {
            $img = imagecreatefromjpeg( $src );
            $img = imagerotate( $img, $deg, 0 );
            imagejpeg( $img, $src, 100 );
            imagedestroy( $img );
        }
    }
    public static function convert_png_to_jpg($input_file){
        $output_file            = pathinfo($input_file) . '/'. basename($filename, '.png') . ".jpeg";
        $input                  = imagecreatefrompng($input_file);
        list($width, $height)   = getimagesize($input_file);
        $output                 = imagecreatetruecolor($width, $height);
        $white                  = imagecolorallocate($output,  255, 255, 255);
        imagefilledrectangle($output, 0, 0, $width, $height, $white);
        imagecopy($output, $input, 0, 0, 0, 0, $width, $height);
        imagejpeg($output, $output_file);
    }
    public function resample($image, $height, $width, $format = 'jpeg', $dpi = 300){
        if (!$image) {
            throw new \Exception('Attempting to resample an empty image');
        }
        if (gettype($image) !== 'resource') {
            throw new \Exception('Attempting to resample something which is not a resource');
        }
        //Use truecolour image to avoid any issues with colours changing
        $tmp_img = imagecreatetruecolor($width, $height);
        //Resample the image to be ready for print
        if (!imagecopyresampled($tmp_img, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image))) {
            throw new \Exception("Unable to resample image");
        }
        //Massive hack to get the image as a jpeg string but there is no php function which converts
        //a GD image resource to a JPEG string
        ob_start();
        imagejpeg($tmp_img, null, 100);
        $image = ob_get_contents();
        ob_end_clean();
        //change the JPEG header to 300 pixels per inch
        $image = substr_replace($image, pack("Cnn", 0x01, $dpi, $dpi), 13, 5);
        return $image;
    }
    public static function gd_resample( $input_file, $ouput_file, $dpi ){
        $source = imagecreatefromjpeg($input_file);
        list($width, $height) = getimagesize($filename);
        $image = self::resample( $source, $height, $width, $dpi );
        file_put_contents( $ouput_file, $image );
    }
    public static function imagick_add_white_bg( $input_file, $ouput_file ){
        try {
            $image  = new Imagick( $input_file );
            $bg     = new IMagick();
            $bg->newImage($image->getImageWidth(), $image->getImageHeight(), new ImagickPixel("white"));
            $bg->setImageBackgroundColor('#FFFFFF');
            $bg->compositeImage($image, IMagick::COMPOSITE_DEFAULT, 0, 0);
            $bg->writeImage( $ouput_file );  
            $image->destroy(); 
            $bg->destroy(); 
        } catch (Exception $e) {
            die('Error when creating a thumbnail: ' . $e->getMessage());
        }
    }
    public static function imagick_convert_png_to_jpg( $input_file, $ouput_file ){
        try {
            $image = new Imagick( $input_file );
            $flattened = new IMagick();
            $flattened->newImage($image->getImageWidth(), $image->getImageHeight(), new ImagickPixel("white"));
            $flattened->compositeImage($image, IMagick::COMPOSITE_OVER, 0, 0);
            $flattened->setImageFormat("jpg");
            $flattened->writeImage( $ouput_file );
            $image->destroy();
            $flattened->destroy();
        } catch( Exception $e ){
            die('Error when creating a thumbnail: ' . $e->getMessage());
        }  
    }
    public static function imagick_convert_png2jpg_without_bg( $input_file, $ouput_file ){
        try {
            $image = new Imagick( $input_file );
            $image->setImageFormat("jpg");
            $image->writeImage( $ouput_file );
            $image->destroy();
        } catch (Exception $e) {
            die('Error when creating a thumbnail: ' . $e->getMessage());
        }
    }
    public static function imagick_convert_rgb_to_cymk( $input_file, $ouput_file ){
        try {
            $image = new Imagick( $input_file );
            $image->stripImage();
            $image->transformimagecolorspace(\Imagick::COLORSPACE_CMYK);
            $image->writeImage( $ouput_file );
            $image->destroy(); 
        } catch( Exception $e ){
            die('Error when creating a thumbnail: ' . $e->getMessage());
        }
    }
    public static function imagick_resample( $input_file, $ouput_file, $dpi ){
        try {
            $image = new Imagick();
            //$image->setResolution($dpi,$dpi);
            $image->readImage($input_file);
            $image->setImageUnits(imagick::RESOLUTION_PIXELSPERINCH);
            $image->setImageResolution($dpi,$dpi);
            $image->writeImage($ouput_file);
            $image->destroy(); 
        } catch( Exception $e ){
            die('Error when creating a thumbnail: ' . $e->getMessage());
        }
    }
    public static function imagick_change_icc_profile( $input_file, $ouput_file, $icc ){
        try {
            $image          = new Imagick( $input_file );
            $image->stripImage ();
            $icc_profile    = file_get_contents( $icc );
            $image->profileImage( 'icc', $icc_profile );
            unset( $icc_profile ); 
            $image->writeImage( $ouput_file );
            $image->destroy();
        } catch( Exception $e ){
            die( 'Error when creating a thumbnail: ' . $e->getMessage() );
        }
    }
    public static function imagick_png2cymkjpg( $input_file, $ouput_file, $dpi ){
        try {
            $image = new Imagick( $input_file );
            $image->setImageUnits( imagick::RESOLUTION_PIXELSPERINCH );
            $image->setImageResolution( $dpi, $dpi );

            if( method_exists( $im, 'setImageAlphaChannel' ) && null !== Imagick::ALPHACHANNEL_REMOVE ){
                $image->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
            }else{
                $image->setImageBackgroundColor('white');
            }
            $profiles           = $image->getImageProfiles('*', false);
            $has_icc_profile    = (array_search('icc', $profiles) !== false);
            if ( $has_icc_profile === false ) {
                $icc_rgb = file_get_contents( NBDESIGNER_PLUGIN_DIR . 'data/icc/RGB/sRGB.icc' ); 
                $image->profileImage('icc', $icc_rgb);
                unset($icc_rgb);
            }
            $list_icc       = nbd_get_icc_cmyk_list_file();
            $default_icc    = nbdesigner_get_option( 'nbdesigner_default_icc_profile', 1 );
            if( $list_icc[$default_icc] != '' ){
                $icc = NBDESIGNER_PLUGIN_DIR . 'data/icc/CMYK/' . $list_icc[$default_icc];
                if( file_exists($icc) ){
                    $icc_profile = file_get_contents( $icc );
                    $image->profileImage('icc', $icc_profile);
                    unset($icc_profile);
                }
            }
            $image->stripImage();

            $image->writeImage( $ouput_file );
            $image->destroy(); 
        } catch (Exception $e) {
            die('Error when creating a thumbnail: ' . $e->getMessage());
        }
    }
    public static function imagick_convert_pdf_to_jpg( $input_file, $ouput_file ){
        try {
            $image = new Imagick();
            $image->setResolution(72,72);
            $image->readimage( $input_file.'[0]' );
            $image->setImageFormat('jpeg');
            $image->writeImage( $ouput_file );
            $image->clear(); 
            $image->destroy();
        } catch( Exception $e ){
            die('Error when creating a thumbnail: ' . $e->getMessage());
        }
    }
    public static function crop_image( $input_file, $ouput_file, $startX, $startY, $width, $height, $ext ){
        if( is_available_imagick() ){
            try {
                $image = new Imagick( $input_file );
                $image->cropImage($width, $height, $startX, $startY);
                //$image->setImagePage($width, $height, 0, 0);
                $image->writeImage( $ouput_file );
                $image->clear(); 
                $image->destroy();
            } catch( Exception $e ){
                die('Error when creating a thumbnail: ' . $e->getMessage());
            }
        }else{
            $src = $ext == 'png' ? imagecreatefrompng($input_file) : imagecreatefromjpeg($input_file);
            $dst = imagecrop($src, ['x' => $startX, 'y' => $startY, 'width' => $width, 'height' => $height]);
            if ($dst !== FALSE) {
                if($ext == 'png'){
                    imagepng($dst, $ouput_file );
                }else{
                    imagejpeg($dst, $ouput_file );
                }
            }
            imagedestroy($dst); 
            imagedestroy($src);
        }
    }
    public static function pdf2image( $path, $dpi = 300, $type = 'png' ){
        if( nbdesigner_get_option( 'nbdesigner_enable_pdf2img_cloud2print_api', 'no' ) == 'yes' ){
            self::cloud_pdf2image( $path, $dpi, $type );
            return;
        }

        if( !is_available_imagick() ){
            self::cloudinary_pdf2image( $path, $dpi, $type );
            return;
        }

        try {
            $ext            = $type == 'png' ? 'png32' : 'jpg';
            $document       = new Imagick( $path );
            $number_pages   = $document->getNumberImages();
            for( $i = 0; $i < $number_pages; $i++ ){
                $im = new Imagick();
                $im->setResolution( $dpi, $dpi );
                $im->readImage( $path . '[' . $i . ']' );
                $im->setImageFormat( $type );
                $im->setImageUnits( defined('imagick::RESOLUTION_PIXELSPERINCH') ? imagick::RESOLUTION_PIXELSPERINCH : 1 );
                $im->setResolution( $dpi, $dpi );
                if( $type == 'jpg' ){
                    if( method_exists( $im, 'setImageAlphaChannel' ) && defined('Imagick::ALPHACHANNEL_REMOVE') && null !== Imagick::ALPHACHANNEL_REMOVE ){
                        $im->setImageAlphaChannel( Imagick::ALPHACHANNEL_REMOVE );
                    }else if( method_exists( $im, 'setImageBackgroundColor' ) ){
                        $im->setImageBackgroundColor( 'white' );
                    }
                    $profiles = $im->getImageProfiles( '*', false );
                    $has_icc_profile = ( is_array($profiles) && array_search( 'icc', $profiles ) !== false );
                    if ( $has_icc_profile === false ) {
                        $icc_rgb_file = NBDESIGNER_PLUGIN_DIR . 'data/icc/RGB/sRGB.icc';
                        if( file_exists( $icc_rgb_file ) ){
                            $icc_rgb = file_get_contents( $icc_rgb_file ); 
                            $im->profileImage( 'icc', $icc_rgb );
                            unset( $icc_rgb );
                        }
                    }
                    $list_icc       = nbd_get_icc_cmyk_list_file();
                    $default_icc    = nbdesigner_get_option( 'nbdesigner_default_icc_profile', 1 );
                    if( isset( $list_icc[$default_icc] ) && $list_icc[$default_icc] != '' ){
                        $icc = NBDESIGNER_PLUGIN_DIR . 'data/icc/CMYK/' . $list_icc[$default_icc];
                        if( file_exists( $icc ) ){
                            $icc_profile = file_get_contents( $icc );
                            $im->profileImage( 'icc', $icc_profile );
                            unset( $icc_profile );
                        }
                    }
                }
                // this will drop down the size of the image dramatically (removes all profiles) 
                //$im->stripImage();
                $im->writeImage( str_replace( ".pdf", "_". $i . "." . $type, $path ) );
                $im->clear();
                $im->destroy();
            }
            $document->clear();
            $document->destroy();
        } catch ( \Throwable $e ) {
            error_log( 'NBD pdf2image error: ' . $e->getMessage() );
        }
    }
    public static function cloud_pdf2image( $path, $dpi = 300, $type = 'png' ){
        $url            = Nbdesigner_IO::convert_path_to_url( $path );
        $url_segment    = urlencode( $url );
        $format         = $type == 'jpg' ? 'jpeg' : $type;
        $default_icc    = nbdesigner_get_option( 'nbdesigner_default_icc_profile', 1 );
        $icc            = $default_icc == '' ? 0 : $default_icc;
        $request        = 'https://y6jd2rrsxbilia3k2qkp2lrusm0adnoy.lambda-url.ap-southeast-1.on.aws/?url=' . $url_segment . '&dpi=' . $dpi . '&format=' . $format . '&icc=' . $icc;
        $bucket         = 'charmingprint';

        $response       = wp_remote_get( $request, array( 'timeout' => 30 ) );

        if ( is_wp_error( $response ) ){
            $request    = 'https://pdf2img.cloud2print.net/' . $url_segment . '/' . $dpi . '/' . $format . '/' . $icc;
            $bucket     = 'pdf2img';
            $response   = wp_remote_get( $request, array( 'timeout' => 30 ) );
        }

        if ( !is_wp_error( $response ) ){
            if( isset( $response['response'] ) && isset( $response['response']['code'] ) && $response['response']['code'] == '200' ){
                $res        = $response['body'];
                $res_arr    = explode( '|', $res );
                if( isset( $res_arr[1] ) ){
                    for( $i = 1; $i <= $res_arr[1]; $i++ ){
                        $img_path   = str_replace( ".pdf", "_" . $i . "." . $type, $path );
                        if( file_exists( $img_path ) ){
                            unlink( $img_path );
                        }
                        //$cloud_link = 'https://' . $bucket . '.s3.amazonaws.com/' . $res_arr[0] . '/page-' . $i . '.' . $format;
                        //fix download hires jpg
                        $cloud_link = 'https://' . $bucket . '.s3.amazonaws.com/' . $res_arr[0] . '/page-' . $i . '.' . $type;
                        nbd_download_remote_file( $cloud_link, $img_path );
                    }
                }
            }
        }
    }
    public static function cloudinary_pdf2image( $path, $dpi = 300, $type = 'png' ){
        $new_name   = '/cloud_' . time() . '.pdf';
        $new_path   = NBDESIGNER_TEMP_DIR . $new_name;
        $re         = copy( $path, $new_path );
        if( $re ){
            $url    = NBDESIGNER_TEMP_URL . $new_name;
            require_once(NBDESIGNER_PLUGIN_DIR.'includes/tcpdf/tcpdf.php');
            require_once(NBDESIGNER_PLUGIN_DIR.'includes/fpdi/autoload.php');
            $pdf    = new Fpdi\TcpdfFpdi();
            $number_pages = $pdf->setSourceFile( $path );
            for( $i = 1; $i <= $number_pages; $i++ ){
                $cloud_link = 'https://res.cloudinary.com/demo/image/fetch/';
                $option = $type == 'png' ? 'f_png' : 'f_jpg,cs_cmyk';
                $cloud_link .= $option;
                $cloud_link .= ',pg_' . $i . ',dn_' . $dpi . '/' . $url;
                $img_path = str_replace( ".pdf", "_". $i . "." . $type, $path );
                if( file_exists( $img_path ) ){
                    unlink( $img_path );
                }
                nbd_download_remote_file( $cloud_link, $img_path );
            }
        }
    }
}
class NBD_Download {
    public static function init() {
        add_action( 'nocache_headers', array( __CLASS__, 'ie_nocache_headers_fix' ) );
    }
    /**
     * Force download - this is the default method.
     * @param  string $file_path
     * @param  string $filename
     */
    public static function download_file( $file_path, $filename ) {
        $parsed_file_path = self::parse_file_path( $file_path );
        self::download_headers( $file_path, $filename );
        if ( ! self::readfile_chunked( $parsed_file_path['file_path'] ) ) {
            if ( $parsed_file_path['remote_file'] ) {
                self::download_file_redirect( $file_path );
            } else {
                self::download_error( esc_html__( 'File not found', 'web-to-print-online-designer' ) );
            }
        }
        exit;
    }
    /**
     * Redirect to a file to start the download.
     * @param  string $file_path
     * @param  string $filename
     */
    public static function download_file_redirect( $file_path, $filename = '' ) {
        header( 'Location: ' . $file_path );
        exit;
    }
    /**
     * Parse file path and see if its remote or local.
     * @param  string $file_path
     * @return array
     */
    public static function parse_file_path($file_path) {
        $wp_uploads = wp_upload_dir();
        $wp_uploads_dir = $wp_uploads['basedir'];
        $wp_uploads_url = $wp_uploads['baseurl'];
        /**
         * Replace uploads dir, site url etc with absolute counterparts if we can.
         * Note the str_replace on site_url is on purpose, so if https is forced
         * via filters we can still do the string replacement on a HTTP file.
         */
        $replacements = array(
            $wp_uploads_url => $wp_uploads_dir,
            network_site_url('/', 'https') => ABSPATH,
            str_replace('https:', 'http:', network_site_url('/', 'http')) => ABSPATH,
            site_url('/', 'https') => ABSPATH,
            str_replace('https:', 'http:', site_url('/', 'http')) => ABSPATH,
        );
        $file_path = str_replace(array_keys($replacements), array_values($replacements), $file_path);
        $parsed_file_path = parse_url($file_path);
        $remote_file = true;

        // See if path needs an abspath prepended to work
        if (file_exists(ABSPATH . $file_path)) {
            $remote_file = false;
            $file_path = ABSPATH . $file_path;
        } elseif ('/wp-content' === substr($file_path, 0, 11)) {
            $remote_file = false;
            $file_path = realpath(WP_CONTENT_DIR . substr($file_path, 11));
            // Check if we have an absolute path
        } elseif ((!isset($parsed_file_path['scheme']) || !in_array($parsed_file_path['scheme'], array('http', 'https', 'ftp')) ) && isset($parsed_file_path['path']) && file_exists($parsed_file_path['path'])) {
            $remote_file = false;
            $file_path = $parsed_file_path['path'];
        }
        return array(
            'remote_file' => $remote_file,
            'file_path' => $file_path,
        );
    }

    /**
     * Get content type of a download.
     * @param  string $file_path
     * @return string
     * @access private
     */
    private static function get_download_content_type($file_path) {
        $file_extension = strtolower(substr(strrchr($file_path, "."), 1));
        $ctype = "application/force-download";
        foreach (get_allowed_mime_types() as $mime => $type) {
            $mimes = explode('|', $mime);
            if (in_array($file_extension, $mimes)) {
                $ctype = $type;
                break;
            }
        }
        return $ctype;
    }
    /**
     * Set headers for the download.
     * @param  string $file_path
     * @param  string $filename
     * @access private
     */
    private static function download_headers($file_path, $filename) {
        self::check_server_config();
        self::clean_buffers();
        nocache_headers();
        header("X-Robots-Tag: noindex, nofollow", true);
        header("Content-Type: " . self::get_download_content_type($file_path));
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=\"" . $filename . "\";");
        header("Content-Transfer-Encoding: binary");
        if ($size = @filesize($file_path)) {
            header("Content-Length: " . $size);
        }
    }
    /**
     * Check and set certain server config variables to ensure downloads work as intended.
     */
    private static function check_server_config() {
        wc_set_time_limit(0);
        if (PHP_VERSION_ID < 50400 && function_exists('get_magic_quotes_runtime') && @call_user_func('get_magic_quotes_runtime')) {
            @call_user_func('set_magic_quotes_runtime', 0);
        }
        if (function_exists('apache_setenv')) {
            @apache_setenv('no-gzip', 1);
        }
        @ini_set('zlib.output_compression', 'Off');
        @session_write_close();
    }
    /**
     * Clean all output buffers.

     * Can prevent errors, for example: transfer closed with 3 bytes remaining to read.
     *
     * @access private
     */
    private static function clean_buffers() {
        if (ob_get_level()) {
            $levels = ob_get_level();
            for ($i = 0; $i < $levels; $i++) {
                @ob_end_clean();
            }
        } else {
            @ob_end_clean();
        }
    }
    /**
     * readfile_chunked.
     *
     * Reads file in chunks so big downloads are possible without changing PHP.INI - http://codeigniter.com/wiki/Download_helper_for_large_files/.
     *
     * @param   string $file
     * @return  bool Success or fail
     */
    public static function readfile_chunked($file) {
        $chunksize = 1024 * 1024;
        $handle = @fopen($file, 'r');

        if (false === $handle) {
            return false;
        }
        while (!@feof($handle)) {
            echo @fread($handle, $chunksize);

            if (ob_get_length()) {
                ob_flush();
                flush();
            }
        }
        return @fclose($handle);
    }
    public static function ie_nocache_headers_fix( $headers ) {
        if ( is_ssl() && !empty( $GLOBALS['is_IE'] ) ) {
            $headers['Cache-Control'] = 'private';
            unset( $headers['Pragma'] );
        }
        return $headers;
    }
    /**
     * Die with an error message if the download fails.
     * @param  string $message
     * @param  string  $title
     * @param  integer $status
     * @access private
     */
    private static function download_error( $message, $title = '', $status = 404 ) {
        if ( !strstr( $message, '<a ' ) ) {
            $message .= ' <a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '" class="wc-forward">' . esc_html__( 'Go to shop', 'woocommerce' ) . '</a>';
        }
        wp_die( $message, $title, array( 'response' => $status ) );
    }
}
NBD_Download::init();
function nbd_alias( $value ) {
    $arr = array(
        'a' => 'à|ả|ã|á|ạ|ă|ằ|ẳ|ẵ|ắ|ặ|â|ầ|ẩ|ẫ|ấ|ậ',
        'd' => 'đ',
        'e' => 'è|ẻ|ẽ|é|ẹ|ê|ề|ể|ễ|ế|ệ',
        'i' => 'ì|ỉ|ĩ|í|ị',
        'o' => 'ò|ỏ|õ|ó|ọ|ô|ồ|ổ|ỗ|ố|ộ|ơ|ờ|ở|ỡ|ớ|ợ',
        'u' => 'ù|ủ|ũ|ú|ụ|ư|ừ|ử|ữ|ứ|ự',
        'y' => 'ỳ|ỷ|ỹ|ý|ỵ',
    );
    $newValue = mb_strtolower( trim( $value ), 'utf-8' );
    foreach ( $arr as $key => $val ) {
        $pattern    = '#(' . $val . ')#imu';
        $newValue   = preg_replace( $pattern, $key, $newValue );
    }
    $newValue = preg_replace( '#[^0-9a-zA-Z\s\-.]#i', '', $newValue );
    $newValue = preg_replace( '#(\s)+#im', '-', $newValue );
    return $newValue;
}
function is_available_imagick(){
    if( !( extension_loaded( 'imagick' ) || class_exists( "Imagick" ) ) ) return false;
    return true;
}
function nbd_get_icc_cmyk_list(){
    return array(
        0    => 'Don\'t Color Manage',
        1    => 'Coated FOGRA27',
        2    => 'Coated FOGRA39',
        3    => 'Coated GRACoL 2006',
        4    => 'Japan Color 2001 Coated',
        5    => 'Japan Color 2001 Uncoated',
        6    => 'Japan Color 2002 Newspaper',
        7    => 'Japan Color 2003 Web Coated',
        8    => 'Japan Web Coated',
        9    => 'Uncoated FOGRA29',
        10   => 'US Web Coated SWOP',
        11   => 'US Web Uncoated',
        12   => 'Web Coated FOGRA28',
        13   => 'Web Coated SWOP 2006 Grade 3',
        14   => 'Web Coated SWOP 2006 Grade 5'
    );
}
function nbd_get_icc_cmyk_list_file(){
    return array(
        0    => '',
        1    => 'CoatedFOGRA27.icc',
        2    => 'CoatedFOGRA39.icc',
        3    => 'CoatedGRACoL2006.icc',
        4    => 'JapanColor2001Coated.icc',
        5    => 'JapanColor2001Uncoated.icc',
        6    => 'JapanColor2002Newspaper.icc',
        7    => 'JapanColor2003WebCoated.icc',
        8    => 'JapanWebCoated.icc',
        9    => 'UncoatedFOGRA29.icc',
        10   => 'USWebCoatedSWOP.icc',
        11   => 'USWebUncoated.icc',
        12   => 'WebCoatedFOGRA28.icc',
        13   => 'WebCoatedSWOP2006Grade3.icc',
        14   => 'WebCoatedSWOP2006Grade5.icc'
    );
}
function nbd_file_get_contents( $url ){
    $response = wp_remote_get( $url );
    if ( is_array( $response ) && ! is_wp_error( $response ) ) {
        $result   = trim($response['body']);
        return $result;
    }
    if( ini_get( 'allow_url_fopen' ) ){
        $checkPHP = version_compare( PHP_VERSION, '5.6.0', '>=' );
        if ( is_ssl() && $checkPHP ) {
            $result = file_get_contents( $url, false, stream_context_create( array('ssl' => array( 'verify_peer' => false, 'verify_peer_name' => false ) ) ) ); 
        }else{
            $result = file_get_contents( $url );
        }
    }else{
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_SSLVERSION, 3 ); 
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 ); 
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        $result = curl_exec( $ch );
        curl_close( $ch );
        if( false === $result ){
            $ch     = curl_init();
            curl_setopt( $ch, CURLOPT_URL, $url ); 
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 ); 
            $result = curl_exec( $ch );
            curl_close( $ch );
        }
    }
    return $result;
}

// custom charmingprint : convert color
// input rgb(a,b,c);
function rgb_to_hex_code( $rgb ){
    preg_match( '/^rgb?[\s+]?\([\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?,[\s+]?(\d+)[\s+]?/i', $rgb, $by_color );
    return sprintf( '#%02x%02x%02x', $by_color[1], $by_color[2], $by_color[3] );
}
function hex_code_to_rgb( $code ){
    list( $r, $g, $b )  = sscanf( $code, "#%02x%02x%02x" );
    $rgb                = array( $r, $g, $b );
    return $rgb;
}
function is_nbdesigner_product( $id ){
    $id     = get_wpml_original_id( $id );
    $check  = get_post_meta( $id, '_nbdesigner_enable', true );
    if( $check ) return true;
    return false;
}
function is_nbd_product_builder( $id ){
    $id         = get_wpml_original_id( $id );
    $check_nbo  = get_post_meta( $id, '_nbo_enable', true );
    if( !$check_nbo ) return false;
    $check = get_post_meta( $id, '_nbdpb_enable', true );
    if( $check ) return true;
    return false;
}
function nbd_check_availaible_option( $layout_depend = 'c' ){
    $layout = nbdesigner_get_option( 'nbdesigner_design_layout', 'm' );
    if( $layout_depend == $layout ) return true;
    return false;
}
function nbdesigner_get_option( $key, $default = null ){
    if( !is_null( $default ) ){
        return get_option( $key, $default );
    }
    $option = get_option( $key, false );
    if(false === $option) return nbdesigner_get_default_setting( $key );
    return $option;
}
function nbdesigner_get_all_setting(){
    $default = get_transient( 'nbd_all_settings' );

    if( false === $default ){
        $default = nbdesigner_get_default_setting();
        foreach( $default as $key => $val ){
            $default[$key] = nbdesigner_get_option( $key );
        }

        set_transient( 'nbd_all_settings', $default );
    }

    return $default;
}
function nbdesigner_get_all_frontend_setting(){
    $default = get_transient( 'nbd_all_frontend_settings' );

    if( false === $default ){
        $default = default_frontend_setting();
        foreach ( $default as $key => $val ){
            $default[$key] = nbdesigner_get_option( $key );
        }

        set_transient( 'nbd_all_frontend_settings', $default );
    }

    return $default;
}
function nbdesigner_get_default_setting( $key = false ){
    $frontend       = default_frontend_setting();
    $nbd_setting    = apply_filters( 'nbdesigner_default_settings', array_merge(array(
        'nbdesigner_design_layout'                      => 'm',
        'nbdesigner_position_button_in_catalog'         => 1,
        'nbdesigner_position_button_product_detail'     => 1,
        'nbdesigner_separate_design_buttons'            => 'no',
        'nbdesigner_position_pricing_in_detail_page'    => 1,
        'nbdesigner_quantity_pricing_description'       => '',
        'nbdesigner_thumbnail_width'                    => 300,
        'nbdesigner_template_width'                     => 500,
        'nbdesigner_default_dpi'                        => 96,
        'nbdesigner_default_dpi_fe'                     => 96, // custom DPI Download FE
        'nbdesigner_show_in_cart'                       => 'yes',
        'nbdesigner_show_button_edit_design_in_cart'    => 'yes',
        'nbdesigner_auto_add_cart_in_detail_page'       => 'no',
        'nbdesigner_class_design_button_catalog'        => '',
        'nbdesigner_class_design_button_detail'         => '',
        'nbdesigner_show_in_order'                      => 'yes',
        'nbdesigner_disable_on_smartphones'             => 'no',
        'nbdesigner_notifications'                      => 'yes',
        'nbdesigner_enable_send_mail_when_approve'      => 'no',
        'nbdesigner_attachment_admin_email'             => 'no',
        'nbdesigner_notifications_recurrence'           => 'hourly',
        'nbdesigner_notifications_emails'               => '',
        'nbdesigner_admin_emails'                       => '',
        'allow_customer_redesign_after_order'           => 'yes',
        'nbd_force_upload_svg'                          => 'no',
        'nbdesigner_mindpi_upload'                      => 0,
        'nbdesigner_hide_button_cart_in_detail_page'    =>  'no',
        'nbdesigner_printful_key'                       => '',
        'nbdesigner_google_api_key'                     => '',
        'nbdesigner_google_client_id'                   => '',
        'nbdesigner_enable_log'                         => 'no',
        'nbdesigner_cron_job_clear_w3_cache'            => 'no',
        'nbdesigner_page_design_tool'                   => 1,
        'nbdesigner_upload_term'                        => esc_html__('Your term', 'web-to-print-online-designer'),
        'nbdesigner_create_your_own_page_id'            => nbd_get_page_id( 'create_your_own' ),
        'nbdesigner_designer_page_id'                   => nbd_get_page_id( 'designer' ),
        'nbdesigner_gallery_page_id'                    => nbd_get_page_id( 'gallery' ),
        
        'nbdesigner_mindpi_upload_file'                 => 0,
        'nbdesigner_allow_upload_file_type'             => '',
        'nbdesigner_disallow_upload_file_type'          => '',
        'nbdesigner_number_file_upload'                 => 1,
        'nbdesigner_min_file_upload'                    => 0,
        'nbdesigner_upload_popup_style'                 => 's',
        'nbdesigner_enable_facebook_file_upload'        => 'yes',
        'nbdesigner_enable_instagram_file_upload'       => 'yes',
        'nbdesigner_enable_drive_file_upload'           => 'yes',
        'nbdesigner_enable_dropbox_file_upload'         => 'yes',
        'nbdesigner_maxsize_upload_file'                => nbd_get_max_upload_default(),
        'nbdesigner_minsize_upload_file'                => 0,
        'nbdesigner_max_res_upload_file'                => '',
        'nbdesigner_min_res_upload_file'                => '',
        'nbdesigner_allow_download_file_upload'         => 'no',
        'nbdesigner_create_preview_image_file_upload'   => 'no',
        'nbdesigner_file_upload_preview_width'          => 200,
        'nbdesigner_long_time_retain_upload_fies'       => '',
        
        'nbdesigner_enable_download_pdf_before'         => 'no',
        'nbdesigner_enable_download_pdf_after'          => 'no',
        'nbdesigner_enable_pdf_watermark'               => 'yes',
        'nbdesigner_pdf_watermark_type'                 => 1,
        'nbdesigner_pdf_watermark_image'                => '',
        'nbdesigner_enable_pdf_password'                => 'no',
        'nbdesigner_pdf_password'                       => '',
        'nbdesigner_editor_logo'                        => '',
        'nbdesigner_truetype_fonts'                     => '',
        'nbdesigner_default_icc_profile'                => 1,
        'nbdesigner_pdf_watermark_text'                 => get_bloginfo( 'name' ),
        'nbdesigner_bleed_stack'                        => 1,
        'nbdesigner_auto_export_pdf'                    => 'no',
        'nbdesigner_order_status_auto_export'           => 'completed',
        'nbdesigner_synchronize_to'                     => 'no',
        'nbdesigner_dropbox_token'                      => '',
        'nbdesigner_dropbox_directory_path'             => '',
        'nbdesigner_ftp_host'                           => '',
        'nbdesigner_ftp_username'                       => '',
        'nbdesigner_ftp_password'                       => '',
        'nbdesigner_ftp_remote_path'                    => '',
        'nbdesigner_ftp_passive_mode'                   => '',
        'nbdesigner_sftp_host'                          => '',
        'nbdesigner_sftp_port'                          => '22',
        'nbdesigner_sftp_username'                      => '',
        'nbdesigner_sftp_password'                      => '',
        'nbdesigner_sftp_key'                           => '',
        'nbdesigner_sftp_remote_path'                   => '',
        'nbdesigner_awss3_credentials_key'              => '',
        'nbdesigner_awss3_credentials_secret'           => '',
        'nbdesigner_awss3_bucket'                       => '',
        'nbdesigner_awss3_region'                       => '',
        'nbdesigner_awss3_directory_path'               => '',
        'nbdesigner_gcs_project_id'                     => '',
        'nbdesigner_gcs_bucket'                         => '',
        'nbdesigner_gcs_keyfile'                        => '',
        'nbdesigner_gcs_directory_path'                 => '',
        
        'nbdesigner_enable_perfect_scrollbar_js'        => 'no',
        'nbdesigner_enable_angular_js'                  => 'yes',
        'nbdesigner_enable_perfect_scrollbar_css'       => 'no',
        
        'nbdesigner_turn_off_persistent_cart'           => 'no',
        'nbdesigner_enable_ajax_cart'                   => 'no',
        'nbdesigner_option_display'                     => '1',
        'nbdesigner_enbale_rich_snippet_price'          => 'no',
        'nbdesigner_hide_add_cart_until_form_filled'    => 'no',
        'nbdesigner_enable_clear_cart_button'           => 'no',
        'nbdesigner_force_select_options'               => 'no',
        'nbdesigner_show_options_in_archive_pages'      => 'no',
        'nbdesigner_hide_table_pricing'                 => 'no',
        'nbdesigner_table_pricing_type'                 => '1',
        'nbdesigner_number_of_decimals'                 => function_exists( 'wc_get_price_decimals' ) ? wc_get_price_decimals() : 2,
        'nbdesigner_hide_option_swatch_label'           => 'yes',
        'nbdesigner_change_base_price_html'             => 'no',
        'nbdesigner_hide_zero_price'                    => 'no',
        'nbdesigner_ad_sublist_position'                => 'b',
        'nbdesigner_tooltip_position'                   => 'top',
        'nbdesigner_hide_summary_options'               => 'no',
        'nbdesigner_float_summary_options'              => 'no',
        'nbdesigner_hide_options_in_cart'               => 'no',
        'nbdesigner_hide_option_price_in_cart'          => 'no',
        'nbdesigner_hide_option_price_in_order'         => 'no',
        'nbdesigner_selector_increase_qty_btn'          => '',
        'nbdesigner_site_force_login'                   => 'no',
        'nbdesigner_fix_lost_pdf_image'                 => 'no',
        'nbdesigner_enable_pdf2img_cloud2print_api'     => 'no',
        'nbdesigner_redefine_K_PATH_FONTS'              => 'yes',
        'nbdesigner_disable_nonce'                      => 'no',
        'nbdesigner_button_hire_designer'               => 'no',
        'nbdesigner_show_popup_design_option'           => 'no',
        'nbdesigner_instagram_app_secret'               => '',
    ), $frontend ) );
    if( !$key ) return $nbd_setting;
    return $nbd_setting[$key];
}
function default_frontend_setting(){
    return apply_filters( 'nbdesigner_default_frontend_settings', array(
        'nbdesigner_fix_domain_changed'             => 'no',
        'nbdesigner_enable_text'                    => 'yes',
        'nbdesigner_detect_emoji'   => esc_html__( 'Not support Emoji, please dont use it for design', 'web-to-print-online-designer' ),
        'nbdesigner_detect_low_dimension'   => esc_html__( 'Warning Lower Dimension Image', 'web-to-print-online-designer' ),
        'nbdesigner_text_change_font'               => 1,
        'nbdesigner_text_italic'                    => 1,
        'nbdesigner_text_bold'                      => 1,
        'nbdesigner_text_underline'                 => 0,
        'nbdesigner_text_through'                   => 0,
        'nbdesigner_text_overline'                  => 0,
        'nbdesigner_text_case'                      => 1,
        'nbdesigner_text_align_left'                => 1,
        'nbdesigner_text_align_right'               => 1,
        'nbdesigner_text_align_center'              => 1,
        'nbdesigner_text_color'                     => 1,
        'nbdesigner_text_background'                => 1,
        'nbdesigner_text_shadow'                    => 0,
        'nbdesigner_text_line_height'               => 1,
        'nbdesigner_text_spacing'                   => 1,
        'nbdesigner_text_font_size'                 => 1,
        'nbdesigner_text_opacity'                   => 1,
        'nbdesigner_text_outline'                   => 1,
        'nbdesigner_text_proportion'                => 1,
        'nbdesigner_text_rotate'                    => 1,
        'nbdesigner_default_text'                   => esc_html__( 'Text here', 'web-to-print-online-designer' ),
        'nbdesigner_default_font_subset'            => 'all',
        'nbdesigner_enable_text_check_lang'         => 'no',
        'nbdesigner_enable_curvedtext'              => 'yes',
        'nbdesigner_enable_text_free_transform'     => 'no',
        'nbdesigner_default_font_sizes'             => '6,8,10,12,14,16,18,21,24,28,32,36,42,48,56,64,72,80,88,96,104,120,144,288,576,1152',
        'nbdesigner_min_font_size'                  =>  '',
        'nbdesigner_max_font_size'                  =>  '',
        'nbdesigner_force_min_font_size'            => 'no',
        'nbdesigner_force_max_font_size'            => 'no',
        'nbdesigner_force_font_size_list'           => 'no',
        'nbdesigner_enable_textpattern'             => 'no',
        
        'nbdesigner_enable_clipart'                 => 'yes',
        'nbdesigner_enable_shapes'                  => 'yes',
        'nbdesigner_enable_icons'                   => 'yes',
        'nbdesigner_enable_flaticon'                => 'yes',
        'nbdesigner_enable_storyset'                => 'yes',
        'nbdesigner_enable_frame'                   => 'yes',
        'nbdesigner_enable_photo_frame'             => 'yes',
        'nbdesigner_enable_google_maps'             => 'no',
        'nbdesigner_clipart_change_path_color'      => 1,
        'nbdesigner_clipart_rotate'                 => 1,
        'nbdesigner_clipart_opacity'                => 1,

        'nbdesigner_enable_image'                   => 'yes',
        'nbdesigner_image_unlock_proportion'        => 1,
        'nbdesigner_image_shadow'                   => 0,
        'nbdesigner_image_opacity'                  => 1,
        'nbdesigner_image_grayscale'                => 1,
        'nbdesigner_image_invert'                   => 1,
        'nbdesigner_image_sepia'                    => 1,
        'nbdesigner_image_sepia2'                   => 1,
        'nbdesigner_image_remove_white'             => 1,
        'nbdesigner_image_transparency'             => 1,
        'nbdesigner_image_tint'                     => 1,
        'nbdesigner_image_blend'                    => 1,
        'nbdesigner_image_brightness'               => 1,
        'nbdesigner_image_noise'                    => 1,
        'nbdesigner_image_pixelate'                 => 1,
        'nbdesigner_image_multiply'                 => 1,
        'nbdesigner_image_blur'                     => 1,
        'nbdesigner_image_sharpen'                  => 1,
        'nbdesigner_image_emboss'                   => 1,
        'nbdesigner_image_edge_enhance'             => 1,
        'nbdesigner_image_rotate'                   => 1,
        'nbdesigner_image_crop'                     => 1,
        'nbdesigner_image_shapecrop'                => 1,
        'nbdesigner_facebook_app_id'                => '',
        'nbdesigner_instagram_app_id'               => '',
        'nbdesigner_dropbox_app_id'                 => '',
        'nbdesigner_enable_upload_image'            => 'yes',
        'nbdesigner_enable_auto_fit_image'          => 'no',
        'nbdesigner_upload_designs_php_logged_in'   => 'no',
        'nbdesigner_upload_multiple_images'         => 'no',
        'nbdesigner_maxsize_upload'                 => nbd_get_max_upload_default(),
        'nbdesigner_minsize_upload'                 => 0,
        'nbdesigner_enable_image_url'               => 'yes',
        'nbdesigner_enable_low_resolution_image'    => 'no',
        'nbdesigner_enable_image_webcam'            => 'yes',
        'nbdesigner_enable_facebook_photo'          => 'yes',
        'nbdesigner_enable_instagram_photo'         => 'no',
        'nbdesigner_enable_dropbox_photo'           => 'yes',
        'nbdesigner_enable_google_drive'            => 'yes',
        'nbdesigner_modern_layout_image_filter'     => 'yes',
        'nbdesigner_enable_svg_code'                => 'no',
        'nbdesigner_enable_generate_photo_thumb'    => 'no',
        'nbdesigner_upload_show_term'               => 'no',
        'nbdesigner_max_upload_files_at_once'       => 5,
        
        'nbdesigner_enable_draw'                    => 'yes',
        'nbdesigner_draw_brush'                     => 1,
        'nbdesigner_draw_brush_pencil'              => 1,
        'nbdesigner_draw_brush_circle'              => 1,
        'nbdesigner_draw_brush_spray'               => 1,
        'nbdesigner_draw_brush_pattern'             => 0,
        'nbdesigner_draw_brush_hline'               => 0,
        'nbdesigner_draw_brush_vline'               => 0,
        'nbdesigner_draw_brush_square'              => 0,
        'nbdesigner_draw_brush_diamond'             => 0,
        'nbdesigner_draw_brush_texture'             => 0,
        'nbdesigner_draw_shape'                     => 1, 
        'nbdesigner_draw_shape_rectangle'           => 1, 
        'nbdesigner_draw_shape_circle'              => 1, 
        'nbdesigner_draw_shape_triangle'            => 1, 
        'nbdesigner_draw_shape_line'                => 1, 
        'nbdesigner_draw_shape_polygon'             => 1, 
        'nbdesigner_draw_shape_hexagon'             => 1, 
        
        'nbdesigner_enable_qrcode'                  => 'yes',
        'nbdesigner_hide_element_tab'               => 'no',
        'nbdesigner_hide_template_tab'              => 'no',
        'nbdesigner_hide_typo_section'              => 'no',
        'nbdesigner_display_product_option'         => '1',
        'nbdesigner_enable_map_print_options'       => 'no',
        'nbdesigner_enable_favicon_badge'           => 'no',
        'nbdesigner_hide_layer_tab'                 => 'no',
        'nbdesigner_show_all_template_sides'        => 'no',
        'nbdesigner_display_template_mode'          => '1',
        'nbdesigner_button_link_product_template'   => 'no',
        'nbdesigner_show_button_change_product'     => 'no',
        'nbdesigner_default_qrcode'                 => esc_html__( 'example.com', 'web-to-print-online-designer' ),
        
        'nbdesigner_dimensions_unit'                => 'cm',
        'nbdesigner_show_all_color'                 => 'yes',
        'nbdesigner_enable_eyedropper'              => 'no',
        'nbdesigner_default_color'                  => '#cc324b',
        'nbdesigner_hex_names'                      => '',
        'nbdesigner_save_latest_design'             => 'yes',
        'nbdesigner_save_for_later'                 => 'yes',
        'nbdesigner_share_design'                   => 'yes',
        'nbdesigner_cache_uploaded_image'           => 'yes',
        
        'nbdesigner_upload_file_php_logged_in'      => 'no',
        
        'nbdesigner_auto_add_cart_in_detail_page'   => 'no',
        
        'allow_customer_download_after_complete_order'  => 'no',
        'nbdesigner_download_design_png'            => 0,
        'nbdesigner_download_design_pdf'            => 0,
        'nbdesigner_download_design_svg'            => 0,
        'nbdesigner_download_design_jpg'            => 0,
        'nbdesigner_download_design_jpg_cmyk'       => 0,
        'nbdesigner_download_design_upload_file'    => 0,
        'nbdesigner_attach_design_png'              => 1,
        'nbdesigner_attach_design_svg'              => 0,
        
        'allow_customer_download_design_in_editor'  => 'no',
        'nbdesigner_download_design_in_editor_png'  => 0,
        'nbdesigner_download_design_in_editor_pdf'  => 0,
        'nbdesigner_download_design_in_editor_jpg'  => 0,
        'nbdesigner_download_design_in_editor_svg'  => 0,
        
        'nbdesigner_pixabay_api_key'                => '27347-23fd1708b1c4f768195a5093b',
        'nbdesigner_unsplash_api_key'               => '5746b12f75e91c251bddf6f83bd2ad0d658122676e9bd2444e110951f9a04af8',
        'nbdesigner_pexels_api_key'                 => '563492ad6f91700001000001a626f8ddac7d48a88fc0856cb7622195',
        'nbdesigner_flaticon_api_key'               => '',
        'nbdesigner_static_map_api_key'             => '',
        'nbdesigner_enable_pixabay'                 => 'yes',
        'nbdesigner_enable_unsplash'                => 'yes',
        'nbdesigner_enable_pexels'                  => 'yes',
        'nbdesigner_enable_freepik'                 => 'yes',
        
        'nbdesigner_show_grid'                      => 'no',
        'nbdesigner_show_ruler'                     => 'no',
        'nbdesigner_show_product_dimensions'        => 'no',
        'nbdesigner_show_bleed'                     => 'no',
        'nbdesigner_show_layer_size'                => 'no',
        'nbdesigner_show_warning_oos'               => 'no',
        'nbdesigner_show_warning_ilr'               => 'no',
        'nbdesigner_show_design_border'             => 'no',
        'nbdesigner_hide_print_option_in_editor'    => 'no',
        'nbdesigner_print_option_large_amount'      => 'no',
        'nbdesigner_object_center_scaling'          => 'no',
        'nbdesigner_always_show_layer_action'       => 'no',
        'nbdesigner_disable_auto_load_template'     => 'no',
        'nbdesigner_lazy_load_template'             => 'no',
        'nbdesigner_auto_fill_template_masks'       => 'no',
        'nbdesigner_limit_photo_by_masks'           => 'no',
        'nbdesigner_prevent_delete_template_layer'  => 'no',
        'nbdesigner_prevent_add_more_layer'         => 'no',
        'nbdesigner_boosting_load_template'         => 'no',
        'nbdesigner_mask_layer'                     => 'yes',
        'nbdesigner_alert_mask_layer_outside'       => esc_html__( 'Layer not inside', 'web-to-print-online-designer' ),
        'nbdesigner_alert_mask_layer_invalid'       => esc_html__( 'Layer can not mask', 'web-to-print-online-designer' ),

        'nbdesigner_enable_cloud2print_api'         => 'no',
        'nbdesigner_enable_font_to_outlines'        => 'no'
    ));
}
function nbd_multicheckbox_settings(){
    return apply_filters( 'nbd_multicheckbox_settings', array());
}
function nbd_get_value_from_serialize_data( $str, $key ){
    $arr    = array();
    $value  = 0;
    parse_str( $str, $arr );
    if( isset( $arr[$key] ) ) $value = $arr[$key];
    return $value;
}
function nbd_not_empty($value) {
    return $value == '0' || !empty($value);
}
function nbd_default_product_setting(){
    return apply_filters('nbdesigner_default_product_setting', array(
        'orientation_name'      => 'Side 1',
        'img_src'               => get_option('nbdesigner_default_background'),
        'img_overlay'           => get_option('nbdesigner_default_overlay'),
        'real_width'            => 8,
        'real_height'           => 6,
        'real_left'             => 1,
        'real_top'              => 1,
        'area_design_top'       => 100,
        'area_design_left'      => 50,
        'area_design_width'     => 400,
        'area_design_height'    => 300,
        'img_src_top'           => 50,
        'img_src_left'          => 0,
        'img_src_width'         => 500,
        'img_src_height'        => 400,
        'product_width'         => 10,
        'product_height'        => 8,
        'show_bleed'            => 0,
        'bleed_top_bottom'      => 0.3,
        'bleed_left_right'      => 0.3,
        'show_safe_zone'        => 0,
        'margin_top_bottom'     => 0.3,
        'margin_left_right'     => 0.3,
        'bg_type'               => 'image',
        'bg_color_value'        => "#ffffff",
        'show_overlay'          => 0,
        'include_overlay'       => 1,
        'area_design_type'      => 1,
        'origin_bg_pdf'         => '',
        'version'               => NBDESIGNER_NUMBER_VERSION
    )); 
}
function nbd_get_default_product_option(){
    return apply_filters( 'nbdesigner_default_product_option', array(
        'admindesign'               => 0,
        'global_template'           => 0,
        'global_template_cat'       => 0,
        'dpi'                       => nbdesigner_get_option( 'nbdesigner_default_dpi', 96 ),
        'dpi_fe'                    => 0, // custom DPI Download FE
        'request_quote'             => 0,
        'allow_specify_dimension'   => 0,
        'min_width'                 => 0,
        'max_width'                 => 0,
        'min_height'                => 0,
        'max_height'                => 0,
        'extra_price'               => 0,
        'type_price'                => 1,
        'type_dimension'            => 1,
        'dynamic_side'              => 0,
        'defined_dimension'         => array( array( 'width' => 10, 'height' => 8, 'price' => 0 ) ),
        'unit'                      => nbdesigner_get_option( 'nbdesigner_dimensions_unit', 'cm' ),
        'additional_price'          => 0
    ));
}
function nbd_get_default_upload_setting(){
    return apply_filters('nbdesigner_default_product_upload', array(
        'number'            => nbdesigner_get_option('nbdesigner_number_file_upload'),
        'allow_type'        => nbdesigner_get_option('nbdesigner_allow_upload_file_type'),
        'disallow_type'     => nbdesigner_get_option('nbdesigner_disallow_upload_file_type'),
        'maxsize'           => nbdesigner_get_option('nbdesigner_maxsize_upload_file'),
        'minsize'           => nbdesigner_get_option('nbdesigner_minsize_upload_file'),
        'mindpi'            => nbdesigner_get_option('nbdesigner_mindpi_upload_file')
    ));
}
function nbd_get_global_template_cat(){
    $cats = get_transient( 'nbd_global_template_cat' );
    if( false === $cats ){
        $response = wp_remote_post( 'https://studio.cmsmart.net/v1/template',
            array(
                'timeout'   => 120,
                'body'      => array(
                    'type'  => 'get_template_cat'
                )
            )
        );
        $cats = array();
        if ( !is_wp_error( $response ) ) {
            $cats = json_decode($response['body'])->data;
        }
        set_transient( 'nbd_global_template_cat' , $cats, DAY_IN_SECONDS );
    }
    return $cats;
}
function nbd_update_config_default( $designer_setting ) {
    $default =  nbd_default_product_setting();
    foreach ( $designer_setting as $key => $setting ){
        $designer_setting[$key] = array_merge( $default, $setting );
    }
    return $designer_setting;
}
function getUrlPageNBD( $page ){
    global $wpdb;
    switch ( $page ) {
        case 'studio':
            $post = nbd_get_page_id( 'studio' );
            break;
        case 'create':
            $post = nbd_get_page_id( 'create_your_own' );
            break;
        case 'redirect':
            $post = nbd_get_page_id( 'logged' );
            break;
        case 'designer':
            $post = nbd_get_page_id( 'designer' );
            break;
        case 'gallery':
            $post = nbd_get_page_id( 'gallery' );
            break;
        case 'product_builder':
            $post = nbd_get_page_id( 'product_builder' );
            break;
        default :
            $post = nbd_get_page_id( $page );
            break;
    }
    return get_post( $post ) ? get_page_link( $post ) : '#';
}
function nbd_update_hit_template( $template_id = false, $folder = '' ){
    global $wpdb;
    $table_name = $wpdb->prefix . 'nbdesigner_templates';
    if( $template_id ){
        $tem = $wpdb->get_row( "SELECT * FROM {$table_name} WHERE id = {$template_id}" );
    }else if($folder != '') {
        $tem = $wpdb->get_row( "SELECT * FROM {$table_name} WHERE folder = '{$folder}'" );
        if( $tem ){
            $template_id = $tem->id;
        }
    }
    if( $template_id ){
        $hit    = $tem->hit ? $tem->hit + 1 : 1;
        $re     = $wpdb->update( $table_name, array(
            'hit' => $hit
        ), array( 'id' => $template_id ) );
    }
}
function nbd_count_total_template( $product_id, $variation_id ){
    global $wpdb;
    $table_name = $wpdb->prefix . 'nbdesigner_templates';
    $sql = "SELECT count(*) as total FROM $table_name WHERE product_id = '$product_id' AND ( variation_id = '$variation_id' || variation_id = 0 ) ORDER BY created_date DESC";
    $results = $wpdb->get_results($sql, 'ARRAY_A');
    return $results[0]["total"];
}
function nbd_get_templates( $product_id, $variation_id, $template_id = '', $priority = false, $limit = false, $start = false, $type = null ){
    global $wpdb;
    $table_name = $wpdb->prefix . 'nbdesigner_templates';
    $type_query = is_null( $type ) ? "type IS NULL" : ( $type == 'all' ? "1 = 1" : "type = $type" );
    if( $template_id != '' ){
        $sql = "SELECT * FROM $table_name WHERE id = $template_id";
    }else {
        if($priority) {
            $sql = "SELECT * FROM $table_name WHERE product_id = '$product_id' AND ( variation_id = '$variation_id' || variation_id = 0 ) AND priority = 1 AND publish = 1 AND $type_query";
        }else {
            if( $limit ){
                $sql = "SELECT * FROM $table_name WHERE product_id = '$product_id' AND ( variation_id = '$variation_id' || variation_id = 0 ) AND publish = 1 AND $type_query ORDER BY created_date DESC LIMIT $limit";
            }else{
                $sql = "SELECT * FROM $table_name WHERE product_id = '$product_id' AND ( variation_id = '$variation_id' || variation_id = 0 ) AND publish = 1 AND $type_query ORDER BY created_date DESC";
            }
        }
    }
    if( $start ){
        $sql .= ' OFFSET ' . $start;
    }
    $results = $wpdb->get_results( $sql, 'ARRAY_A' );
    if( $priority && count( $results ) == 0 ) {
        $sql = "SELECT * FROM $table_name WHERE product_id = '$product_id' AND ( variation_id = '$variation_id' || variation_id = 0 ) AND publish = 1 AND $type_query ORDER BY created_date DESC LIMIT 1";
        $results = $wpdb->get_results( $sql, ARRAY_A );
    }
    return $results;
}
function nbd_get_design( $design_id, $product_id = false ){
    global $wpdb;
    $table_name = $wpdb->prefix . 'nbdesigner_templates';
    $sql        = "SELECT * FROM $table_name WHERE id = $design_id";
    if( $product_id ){
        $sql   .= " AND product_id = $product_id";
    }
    $result     = $wpdb->get_row( $sql, 'ARRAY_A' );
    return $result;
}
function nbd_get_design_by_folder( $folder ){
    global $wpdb;
    $table_name = $wpdb->prefix . 'nbdesigner_templates';
    $sql        = "SELECT * FROM $table_name WHERE folder = '$folder'";
    $result     = $wpdb->get_row( $sql, 'ARRAY_A' );
    return $result;
}
function nbd_get_designer_by_design( $folder ){
    global $wpdb;
    $table_name     = $wpdb->prefix . 'nbdesigner_templates';
    $sql            = "SELECT * FROM $table_name WHERE folder = '$folder'";
    $results        = $wpdb->get_results( $sql, ARRAY_A );
    if( count( $results ) > 0 ){
        return $results[0]['user_id'];
    }
    return false;
}
function nbd_get_resource_templates( $product_id, $variation_id, $limit = 20, $start = 0, $tags = false ){
    $data       = array();
    $templates  = nbd_get_templates( $product_id, $variation_id, '', false, $limit, $start );
    foreach ( $templates as $tem ){
        $path_preview   = NBDESIGNER_CUSTOMER_DIR . '/' . $tem['folder'] . '/preview';
        $listThumb      = Nbdesigner_IO::get_list_images( $path_preview );
        $listThumb      = nbd_sort_file_by_side( $listThumb );
        if( count( $listThumb ) ){
            $_temp          = array();
            $_temp['id']    = $tem['folder'];
            foreach( $listThumb as $img ){
                $_temp['src'][] = Nbdesigner_IO::wp_convert_path_to_url( $img );
            }
            if( isset( $tem['thumbnail'] ) && $tem['thumbnail'] ){
                $_temp['thumbnail'] = wp_get_attachment_url( $tem['thumbnail'] );
            }else{
                $_temp['thumbnail'] = $_temp['src'][0];
            }
            $data[] = $_temp;
        }
    }
    if( !$tags ) return apply_filters( 'nbd_product_templates_without_tag', $data, $templates );
    return apply_filters( 'nbd_product_templates', $data, $templates );
}
function nbd_get_template_by_folder( $folder ){
    $data           = array();
    $path           = NBDESIGNER_CUSTOMER_DIR . '/' . $folder;
    $data['fonts']  = nbd_get_data_from_json( $path . '/used_font.json' );
    $data['design'] = nbd_get_data_from_json( $path . '/design.json' ); 
    $data['config'] = nbd_get_data_from_json( $path . '/config.json' );
    return $data;
}
function nbd_get_product_info( $product_id, $variation_id, $nbd_item_key = '', $task = '', $task2 = '', $reference = '', $need_templates = false, $cart_item_key = '' ){
    $data = array();
    if($variation_id > 0){
        $variation_id = get_wpml_original_id($variation_id);
    }
    $nbd_item_cart_key = ($variation_id > 0) ? $product_id . '_' . $variation_id : $product_id; 
    $_nbd_item_key = WC()->session->get('nbd_item_key_'.$nbd_item_cart_key);  
    if( $_nbd_item_key && $task2 == '' && $nbd_item_key == '' && $task != 'create' ) $nbd_item_key = $_nbd_item_key;
//    if( $cart_item_key != '' && WC()->session->get($cart_item_key . '_nbd') ) {
//        $nbd_item_key = WC()->session->get($cart_item_key . '_nbd');
//    }
    $lazy_load_default_template = nbdesigner_get_option( 'nbdesigner_lazy_load_template' );
    $path = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key;
    /* Path not exist in case add to cart before design, session has been init */  
    if( $nbd_item_key == '' || !file_exists( $path ) ){
        $data['upload'] = unserialize( get_post_meta( $product_id, '_nbdesigner_upload', true ) );
        $data['option'] = unserialize( get_post_meta( $product_id, '_nbdesigner_option', true ) );
        if( $variation_id > 0 ){
            $enable_variation   = get_post_meta( $variation_id, '_nbdesigner_variation_enable', true );
            $data['product']    = unserialize( get_post_meta( $variation_id, '_designer_variation_setting', true ) );
            if ( !( $enable_variation && isset( $data['product'][0] ) ) ){
                $data['product'] = unserialize( get_post_meta( $product_id, '_designer_setting', true ) );
            }
        }else {
            $data['product'] = unserialize( get_post_meta( $product_id, '_designer_setting', true ) );
        }
    }else {
        $data['product']    = unserialize( file_get_contents( $path . '/product.json' ) );
        $data['od_option']  = file_exists( $path . '/od_option.json.json' ) ? unserialize( file_get_contents( $path . '/od_option.json' ) ) : '';
        $data['option']     = unserialize( file_get_contents( $path . '/option.json' ) );
        if( file_exists( $path . '/upload.json' ) ){
            $data['upload'] = unserialize( file_get_contents( $path . '/upload.json' ) );
        }else{
            $data['upload'] = unserialize( get_post_meta( $product_id, '_nbdesigner_upload', true ) );
        }
        $data['fonts']  = nbd_get_data_from_json( $path . '/used_font.json' );
        $data['config'] = nbd_get_data_from_json( $path . '/config.json' );
        if( isset( $data['config']->product ) ){
            $data['product'] = $data['config']->product;
        }
        if( $lazy_load_default_template == 'yes' ){
            $data['lazy_load_design_folder'] = $nbd_item_key;
        }else{
            $data['design'] = nbd_get_data_from_json( $path . '/design.json' );
        }
    }
    $disable_auto_load_template = nbdesigner_get_option( 'nbdesigner_disable_auto_load_template', 'no' );
    $nbls_enable                = get_post_meta( $product_id, '_nbls_enable', true );
    if( $nbls_enable ){
        $local_settings = get_post_meta( $product_id, '_nbls_settings', true );
        if( $local_settings ){
            $local_settings = unserialize( $local_settings );
        } else {
            $local_settings = array();
        }
        if( isset( $local_settings['nbdesigner_disable_auto_load_template'] ) ){
            $disable_auto_load_template = $local_settings['nbdesigner_disable_auto_load_template'];
        }
    }
    if( $data['option']['admindesign'] && $task == 'new' && $disable_auto_load_template != 'yes' ) {
        /* Get primary template or latest template for new design */
        $template = nbd_get_templates( $product_id, $variation_id, '', 1 );
        if( isset( $template[0] ) ){
            $template_path  = NBDESIGNER_CUSTOMER_DIR . '/' . $template[0]['folder'];
            $data['fonts']  = nbd_get_data_from_json( $template_path . '/used_font.json' );
            $data['config'] = nbd_get_data_from_json( $template_path . '/config.json' );
            if( $lazy_load_default_template == 'yes' ){
                $data['lazy_load_design_folder'] = $template[0]['folder'];
            } else {
                $data['design']         = nbd_get_data_from_json( $template_path . '/design.json' );
                $data['design_id']      = $template[0]['folder'];
            }
        }
        $data['is_template'] = 1;
    }
    if( $reference != '' ){
        /* Get reference design, font and reference product setting */
        $ref_path = NBDESIGNER_CUSTOMER_DIR . '/' . $reference;
        if($lazy_load_default_template == 'yes'){
            $data['lazy_load_design_folder'] = $reference;
        }else{
            $data['design'] = nbd_get_data_from_json( $ref_path . '/design.json' );
        }
        $data['fonts']          = nbd_get_data_from_json( $ref_path . '/used_font.json' );
        $data['ref']            = unserialize( file_get_contents( $ref_path . '/product.json' ) );
        $data['config_ref']     = nbd_get_data_from_json( $ref_path . '/config.json' );
        $data['is_reference']   = 1;
        nbd_update_hit_template( false, $reference );
    }
    if( $data['upload']['allow_type'] == '' ) $data['upload']['allow_type']         = nbdesigner_get_option( 'nbdesigner_allow_upload_file_type' );
    if( $data['upload']['disallow_type'] == '' ) $data['upload']['disallow_type']   = nbdesigner_get_option( 'nbdesigner_disallow_upload_file_type' );
    $data['upload']['allow_type']       = preg_replace('/\s+/', '', strtolower( $data['upload']['allow_type'] ) );
    $data['upload']['disallow_type']    = preg_replace('/\s+/', '', strtolower( $data['upload']['disallow_type'] ) );
    $data['product']                    = nbd_get_media_for_data_product( $data['product'] );
    if( $need_templates ){
        $templates = nbd_get_resource_templates( $product_id, $variation_id );
        if( count( $templates ) ){
            $data['templates'] = $templates;
        }
    }
    // custom charmingprint 30/11
    global $nbd_printing_options;
    $_option_id = $nbd_printing_options->get_product_option( $product_id );
    if( $_option_id ){
        $_options = $nbd_printing_options->get_option( $_option_id );
        if( $_options && isset($_options['fields']) ){
            $data['printing_options'] = unserialize($_options['fields']);
        }
    }
    $data = apply_filters( 'nbd_product_info', $data );
    return $data;
}
function nbd_get_product_pre_builder( $option_id, $nbo_cart_item_key ){
    $data = array();
    if( $nbo_cart_item_key != '' ){
        $cart_item = WC()->cart->get_cart_item( $nbo_cart_item_key );
        if( isset( $cart_item['nbo_meta'] ) ){
            $builder_folder = $cart_item['nbo_meta']['nbdpb'];
            $path           = NBDESIGNER_CUSTOMER_DIR . '/' . $builder_folder;
            $data['config'] = nbd_get_data_from_json( $path . '/config.json' );
            $data['design'] = nbd_get_data_from_json( $path . '/design.json' );
        }
    }else{
        global $wpdb;
        $sql = "SELECT builder FROM {$wpdb->prefix}nbdesigner_options WHERE id = {$option_id}";
        $options = $wpdb->get_results( $sql, 'ARRAY_A' );
        if( isset( $options[0] ) ){
            $builder_folder = $options[0]['builder'];
            if( $builder_folder ){
                $path = NBDESIGNER_CUSTOMER_DIR . '/' . $builder_folder;
                $data['config'] = nbd_get_data_from_json( $path . '/config.json' );
                $data['design'] = nbd_get_data_from_json( $path . '/design.json' );
            }
        }
    }
    return $data;
}
function nbd_get_media_for_data_product( $data_product ){
    foreach ( $data_product as $key => $data ){
        $data_product[$key] = $_data = (array) $data;
        $data_product[$key]['img_src'] = is_numeric( $_data['img_src'] ) ? wp_get_attachment_url( $_data['img_src'] ) : $_data['img_src'];
        $data_product[$key]['img_overlay'] = is_numeric( $_data['img_overlay'] ) ? wp_get_attachment_url( $_data['img_overlay'] ) : $_data['img_overlay'];
    }
    return $data_product;
}
function nbd_add_attachment( $file ){
    $filename       = basename( $file );
    $checkPHP       = version_compare( PHP_VERSION, '5.6.0', '>=' );
    if ( is_ssl() && $checkPHP && filter_var( $file, FILTER_VALIDATE_URL ) ) {
        $contents = file_get_contents( $file, false, stream_context_create( array('ssl' => array( 'verify_peer' => false, 'verify_peer_name' => false ) ) ) ); 
    }else{
        $contents = file_get_contents( $file );
    }
    $upload_file    = wp_upload_bits( $filename, null, $contents );
    if ( !$upload_file['error'] ) {
        $wp_filetype    = wp_check_filetype( $filename, null );
        $attachment     = array(
            'post_mime_type'    => $wp_filetype['type'],
            'post_title'        => preg_replace( '/\.[^.]+$/', '', $filename ),
            'post_content'      => '',
            'post_status'       => 'inherit'
        );
        $attachment_id = wp_insert_attachment( $attachment, $upload_file['file'] );
        if ( !is_wp_error( $attachment_id ) ) {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
            wp_update_attachment_metadata( $attachment_id, $attachment_data );
            return $attachment_id;
        }
        return 0;
    }
    return 0;
}
/**
 * Attach file from $_FILES to WP Media
 *
 * @param $_FILES $file
 * @return integer
 */
function nbd_upload_media( $file ){
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
        'guid'              => $wp_upload_dir['url'] . '/' . basename( $file_path ),
        'post_mime_type'    => $mime_type,
        'post_title'        => preg_replace( '/\.[^.]+$/', '', basename( $file_path ) ),
        'post_content'      => '',
        'post_status'       => 'inherit'
    );
    $attachment_id      = wp_insert_attachment( $attachment_data, $file_path );
    if ( !$attachment_id ) {
        return false;
    }
    $_file_path         = get_attached_file( $attachment_id );
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $attach_data        = wp_generate_attachment_metadata( $attachment_id, $_file_path );
    wp_update_attachment_metadata( $attachment_id, $attach_data );
    return $attachment_id;
}
function nbd_attach_image_to_media( $path ){
    $mime_type = mime_content_type( $path );

    $file = array(
        'name'     => basename( $path ),
        'type'     => $mime_type,
        'tmp_name' => $path,
        'error'    => 0,
        'size'     => filesize( $path ),
    );

    return nbd_upload_media( $file );
}
function nbd_get_upload_files_from_session( $nbu_item_key ){
    $path           = NBDESIGNER_UPLOAD_DIR . '/' . $nbu_item_key;
    $list_files     = Nbdesigner_IO::get_list_files($path);
    $list_files     = nbd_get_array_name_from_arry_path($list_files); 
    $files          = array();
    foreach ($list_files as $file ){
        $ext = pathinfo($file, PATHINFO_EXTENSION);
        $files[] = array(
            'src'   => Nbdesigner_IO::get_thumb_file($ext, '' ),
            'name'  =>  $file
        );
    }
    return $files;
}
function nbd_get_array_name_from_arry_path( $arr_path ){
    $arr_names = array();
    foreach ($arr_path as $path ){
        $arr_names[] = basename($path);
    }
    return $arr_names;
}
function nbd_get_data_from_json($path = ''){
    $content = file_exists($path) ? file_get_contents($path) : '';
    return json_decode($content) ;
}
function nbd_update_config_product_160($settings){
    return $settings;
}
function nbd_get_i18n_javascript(){
    $lang = array(
        'error'                             => esc_html__('Oops! Try again later!', 'web-to-print-online-designer'),
        'complete'                          => esc_html__('Complete!', 'web-to-print-online-designer'),
        'are_you_sure'                      => esc_html__('Are you sure?', 'web-to-print-online-designer'),
        'warning_mes_delete_file'           => esc_html__('You will not be able to recover this file!', 'web-to-print-online-designer'),
        'warning_mes_delete_category'       => esc_html__('You will not be able to recover this category!', 'web-to-print-online-designer'),
        'warning_mes_fill_category_name'    => esc_html__('Please fill category name!', 'web-to-print-online-designer'),
        'warning_mes_backup_data'           => esc_html__('Restore your last data!', 'web-to-print-online-designer'),
        'warning_mes_delete_lang'           => esc_html__('You will not be able to recover this language!', 'web-to-print-online-designer'),
        'alert_choose_setting'              => esc_html__('Please choose a setting!', 'web-to-print-online-designer'),
        'less_setting'                      => esc_html__('Less setting', 'web-to-print-online-designer'),
        'more_setting'                      => esc_html__('More setting', 'web-to-print-online-designer')
    );
    return $lang;
}
if ( ! function_exists( 'is_nbd_product_builder_page' ) ) {
    function is_nbd_product_builder_page(){
        return is_page( nbd_get_page_id( 'product_builder' ) );
    }
}
if ( ! function_exists( 'is_nbd_studio' ) ) {
    function is_nbd_studio(){
        return is_page( nbd_get_page_id( 'studio' ) );
    }
}
if ( ! function_exists( 'is_nbd_design_page' ) ) {
    function is_nbd_design_page(){
        return is_page( nbd_get_page_id( 'create_your_own' ) );
    }
}
if ( ! function_exists( 'is_nbd_gallery_page' ) ) {
    function is_nbd_gallery_page(){
        return is_page( nbd_get_page_id( 'gallery' ) );
    }
}
if ( ! function_exists( 'is_nbd_designer_page' ) ) {
    function is_nbd_designer_page(){
        return is_page( nbd_get_page_id( 'designer' ) );
    }
}
if( !function_exists( 'nbd_get_page_id' ) ){
    function nbd_get_page_id( $page ){
        $page = apply_filters( 'nbdesigner_' . $page . '_page_id', get_option( 'nbdesigner_' . $page . '_page_id' ) );
        if ( class_exists( 'SitePress' ) ) {
            $page = icl_object_id( $page,'page',false );
        }  
        return $page ? absint( $page ) : -1;
    }
}
function nbd_get_woo_version(){
    if ( ! function_exists( 'get_plugins' ) ) require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    $plugin_folder  = get_plugins( '/' . 'woocommerce' );
    $plugin_file    = 'woocommerce.php';
    if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
        return $plugin_folder[$plugin_file]['Version'];
    } else {
        return 0;
    }
}
function is_woo_v3(){
    $woo_ver = nbd_get_woo_version(); 
    if( version_compare( $woo_ver, "3.0", "<" )) return false;
    return true;
}
function is_woo_v305(){
    $woo_ver = nbd_get_woo_version(); 
    if( version_compare( $woo_ver, "3.0.5", "<" )) return false;
    return true;
}
if( !function_exists( 'nbd_check_woo_version' ) ){
    function nbd_check_woo_version( $version ){
        $woo_ver = nbd_get_woo_version(); 
        if( version_compare( $woo_ver, $version, "<" ) ) return false;
        return true;
    }
}
function is_dokan(){
    if(class_exists('WeDevs_Dokan') ){
        return true;
    }
    return false;
}
function nbd_get_dpi( $filename ){
    if( class_exists( 'Imagick' ) ){
        $image          = new Imagick( $filename );
        $resolutions    = $image->getImageResolution();
        $units          = $image->getImageUnits();
        if( $units == 2 ){
            if (!empty( $resolutions['y'] )) {
                $resolutions['y'] = round( $resolutions['y'] * 2.54, 2 );
            }
            if (!empty($resolutions['x'])) {
                $resolutions['x'] = round( $resolutions['x'] * 2.54, 2 );
            }
        }
    }else{
        $a      = fopen( $filename, 'r' );
        $string = fread( $a, 20 );
        fclose($a);

        $data           = bin2hex( substr( $string, 14, 4 ) );
        $x              = substr( $data, 0, 4 );
        $y              = substr( $data, 4, 4 );
        $resolutions    = array( 'x' => hexdec( $x ), 'y' => hexdec( $y ) );
    }
    $resolutions['x'] = $resolutions['x'] != 0 ? $resolutions['x'] : 72;
    $resolutions['y'] = $resolutions['y'] != 0 ? $resolutions['y'] : 72;
    return $resolutions;
}
/**
 * Locate template.
 *
 * Locate the called template.
 * Search Order:
 * 1. /themes/theme/web-to-print-online-designer/$template_name
 * 2. /themes/theme/$template_name
 * 3. /plugins/web-to-print-online-designer/templates/$template_name.
 *
 * @since 1.3.1
 *
 * @param   string  $template_name          Template to load.
 * @param   string  $string $template_path          Path to templates.
 * @param   string  $default_path           Default path to template files.
 * @return  string                  Path to the template file.
 */    
function nbdesigner_locate_template($template_name, $template_path = '', $default_path = '') {
    // Set variable to search in web-to-print-online-designer folder of theme.
    if (!$template_path) :
        $template_path = 'web-to-print-online-designer/';
    endif;
    // Set default plugin templates path.
    if (!$default_path) :
        $default_path = NBDESIGNER_PLUGIN_DIR . 'templates/'; // Path to the template folder
    endif;
    // Search template file in theme folder.
    $template = locate_template(array(
        $template_path . $template_name,
        $template_name
    ));
    // Get plugins template file.
    if (!$template) :
        $template = $default_path . $template_name;
    endif;
    return apply_filters('nbdesigner_locate_template', $template, $template_name, $template_path, $default_path);
}
/**
 * Get template.
 *
 * Search for the template and include the file.
 *
 * @since 1.3.1
 *
 * @see wcpt_locate_template()
 *
 * @param string    $template_name          Template to load.
 * @param array     $args               Args passed for the template file.
 * @param string    $string $template_path          Path to templates.
 * @param string    $default_path           Default path to template files.
 */
function nbdesigner_get_template($template_name, $args = array(), $tempate_path = '', $default_path = '') {
    if (is_array($args) && isset($args)) :
        extract($args);
    endif;
    $template_file = nbdesigner_locate_template($template_name, $tempate_path, $default_path);
    if (!file_exists($template_file)) :
        _doing_it_wrong(__FUNCTION__, sprintf('<code>%s</code> does not exist.', $template_file), '1.3.1');
        return;
    endif;
    include $template_file;
}
function nbdesigner_render_template( $template_name, $args = array() ){
    ob_start();
    nbdesigner_get_template($template_name, $args);
    $content = ob_get_clean();
    echo $content;
}
function nbd_get_language($code){
    $data                   = array();
    $data['mes']            = 'success';
    $path                   = NBDESIGNER_PLUGIN_DIR . 'data/language.json';
    $path_data              = NBDESIGNER_DATA_DIR . '/data/language.json';
    if( file_exists( $path_data ) ) $path = $path_data;
    $list                   = json_decode( file_get_contents( $path ) ); 
    $path_lang              = NBDESIGNER_PLUGIN_DIR . 'data/language/' . $code . '.json';
    $path_data_lang         = NBDESIGNER_DATA_DIR . '/data/language/'.$code.'.json';
    if( file_exists( $path_data_lang ) ) $path_lang = $path_data_lang;
    $path_original_lang     = NBDESIGNER_PLUGIN_DIR . 'data/language/en_US.json';
    if( !file_exists( $path_lang ) ) $path_lang = $path_original_lang;
    $lang_original          = json_decode( file_get_contents( $path_original_lang ) ); 
    $lang                   = json_decode( file_get_contents( $path_lang ) );
    if( is_array( $lang ) ){
        $data_langs = (array)$lang[0];
        if( is_array( $lang_original ) ){
            $data_langs_origin  = (array)$lang_original[0];
            $data_langs         = array_merge( $data_langs_origin, $data_langs );
        }
        $data['langs']      = $data_langs;
        if( is_array( $data['langs'] ) ){
            asort( $data['langs'] );
        }
        $data['code'] = $code;
    }else{
        $data['mes'] = 'error';
    }
    if( is_array( $list ) ){
        $data['cat'] = $list;
    }else{
        $data['mes'] = 'error';
    }
    return $data;
}
function nbd_get_license_key(){
    //pc hidden license
    if(NBDESIGNER_HIDDEN_LICENSE_KEY) {
        return array(
            'key' => 'baf8a5ad99b188ee599512346d9dea19',
            'salt' => '14cf5c658b9496477bfc7b42c38629ff',
            'code' => 6,
            'expiry-date' => 9999999999999999,
        );
    }
    $_license = get_option( 'nbdesigner_license' );
    if( $_license ){
        $license = (array) json_decode( $_license );
    }else{
        $path = NBDESIGNER_DATA_CONFIG_DIR . '/license.json';
        if( file_exists( $path ) ){
            $license = (array) json_decode( file_get_contents( $path ) );
        }
    }
    return $license;
}
function nbd_check_license(){
    $license = nbd_get_license_key();
    $result = false;
    if( $license['key'] != '' ){
        $code = ( isset( $license["code"] ) ) ? $license["code"] : 10;
        if( ( $code == 5 ) || ( $code == 6 ) ){
            $now            = strtotime("now - 2days");
            $expiry_date    = ( isset( $license["expiry-date"] ) ) ? $license["expiry-date"] : 0;
            if( $expiry_date > $now ){
                $salt       = ( isset( $license['salt'] ) ) ? $license['salt'] : 'somethingiswrong';
                $new_salt   = md5( $license['key'].'pro' );
                if( $salt == $new_salt ) $result = true;
            }
        }
    }
    return $result;
}
function nbd_active_domain( $license_key ){
    $url    = 'https://cmsmart.net/activedomain/netbase/WPP1074/' . $license_key . '/' . base64_encode( rtrim( get_bloginfo( 'wpurl' ), '/' ) );
    $result = nbd_file_get_contents( $url );
    if( $result ){
        $path_data = NBDESIGNER_DATA_CONFIG_DIR . '/license.json';
        if( !file_exists( NBDESIGNER_DATA_CONFIG_DIR ) ) {
            wp_mkdir_p( NBDESIGNER_DATA_CONFIG_DIR );
        }
        $data           = (array) json_decode( $result );
        $data['key']    = $license_key;
        if( $data['type'] == 'free' ) $data['number_domain'] = "5";
        $data['salt']   = md5( $license_key . $data['type'] );
        $license        = json_encode( $data );
        update_option( 'nbdesigner_license', $license );
    }
}
function nbd_is_product( $id ){
    $product = wc_get_product( $id );
    if( $product ) return true;
    return false;
}
function is_nbd_product( $pid ){
    if( !nbd_is_product( $pid ) ) return false;
    $pid            = get_wpml_original_id($pid);
    $is_nbdesign    = get_post_meta( $pid, '_nbdesigner_enable', true ); 
    if ( $is_nbdesign ) {
        return true;
    }
    return false;
}
function nbd_get_default_variation_id( $product_id ){
    $variation_id = 0;
    if( !$product_id ) return $variation_id;
    $product = wc_get_product( $product_id );
    if( $product && is_a( $product, 'WC_Product' ) && $product->is_type( 'variable' ) ){
        $available_variations = $product->get_available_variations();
        if( is_woo_v3() ){
            $default_attributes = $product->get_default_attributes();
        }else{
            $default_attributes = $product->get_variation_default_attributes();
        } 
        foreach ( $default_attributes as $key => $value ) {
            if (strpos($key, 'attribute_') === 0) {
                continue;
            }
            unset( $default_attributes[$key] );
            $default_attributes[sprintf( 'attribute_%s', $key )] = $value;
        }
        if ( class_exists( 'WC_Data_Store' ) ) {
            $data_store = WC_Data_Store::load( 'product' );
            return $data_store->find_matching_product_variation( $product, $default_attributes );
        } else {
            return $product->get_matching_variation( $default_attributes );
        }
    }
    return $variation_id;
}
function nbd_get_default_variation_id2( $product_id ){
    $variation_id = 0;
    $variations     = wc_get_products( array(
        'status'         => array( 'private', 'publish' ),
        'type'           => 'variation',
        'parent'         => $product_id,
        'limit'          => $per_page,
        'page'           => -1,
        'orderby'        => array(
                'menu_order' => 'ASC',
                'ID'         => 'DESC',
        ),
        'return'         => 'objects',
    ) ); 
    if ( $variations ) {
        foreach ( $variations as $variation_object ) {
            $vid   = $variation_object->get_id();
            $variation_data = array_merge( array_map( 'maybe_unserialize', get_post_custom( $vid ) ), wc_get_product_variation_attributes( $vid ) );
        }
    }
}
function nbd_check_permission(){
    if( isset($_GET['cik']) ){ return true;
        if( (isset($_GET['task']) && $_GET['task'] == 'new') || (isset($_GET['task2']) && $_GET['task2'] == 'update') ) return true;
        if( !(isset($_GET['task2']) && $_GET['task2'] != '') && !WC()->session->get($_GET['cik'] . '_nbd') && !WC()->session->get($_GET['cik'] . '_nbu')) return false;
        if( !(isset($_GET['task']) && $_GET['task'] == 'edit') && !WC()->session->get($_GET['cik'] . '_nbd') && !WC()->session->get($_GET['cik'] . '_nbu')) return false;
    }
    if( isset($_GET['oid']) ){
        $order = wc_get_order(absint($_GET['oid']) );
        $uid = get_current_user_id();
        if ($order->get_user_id() != $uid) return false;
    }
    if( isset($_GET['task']) && $_GET['task'] == "create" ){
        if( !can_edit_nbd_template() ) return false;
    }
    return true;
}
function can_edit_nbd_template(){
    if( current_user_can('edit_nbd_template') ) return true;
    $is_nbdesigner = get_user_meta( get_current_user_id(), 'nbd_create_design', true );
    if( $is_nbdesigner == 'on' ) return true;
    return false;
}
function nbd_check_order_permission( $order_id ){
    $order  = wc_get_order(absint( $order_id ) ); 
    $uid    = get_current_user_id();
    if ($order->get_user_id() != $uid) return false;
    return true;
}
function get_nbd_variations( $product_id, $include_price = false ){
    $product = wc_get_product( $product_id );
    $variations = array();
    if( $product && is_a( $product, 'WC_Product' ) && $product->is_type( 'variable' ) ) {
        $available_variations = $product->get_available_variations();
        foreach ($available_variations as $variation){
            $enable = get_post_meta($variation['variation_id'], '_nbdesigner_variation_enable', true);
            if($enable){
                if( is_array( $variation['attributes'] ) ){
                    $new_name = '';
                    $count_empty = 0;
                    foreach ( $variation['attributes'] AS $name => $value ) {
                        //if ( !empty( $value ) ) $new_name .= ucfirst($value).', ';
                        if ( !empty( $value ) ){
                            $taxonomy = esc_attr( str_replace( 'attribute_', '', $name ) );
                            if( taxonomy_exists( $taxonomy ) ){
                                $terms = wc_get_product_terms( $product_id, $taxonomy, array( 'fields' => 'all' ) );
                                foreach( $terms as $term ){
                                    if( $term->slug == $value ) {
                                        $value = $term->name;
                                    }
                                }
                            }
                            $new_name .= ucfirst($value).', ';
                        }else{
                            $count_empty++;
                        }
                    }
                    $new_name = substr($new_name, 0, -2);
                    if( $count_empty ==  count($variation['attributes']) ) $new_name = __( 'Any option', 'web-to-print-online-designer' );
                }   
                $var = array(
                    'id'    =>  $variation['variation_id'],
                    'name'  =>  $new_name
                );
                if( $include_price ){
                    $product        = wc_get_product( $variation['variation_id'] );
                    $var['price']   = ( $product && is_a( $product, 'WC_Product' ) ) ? $product->get_price() : 0;
                }
                $variations[] = $var;
            }
        }   
    }
    return $variations;
}
function nbd_get_max_upload_default(){
    if( function_exists ( 'wp_max_upload_size' ) ){
        return round(wp_max_upload_size() / 1024 / 1024);
    }else{
        return abs( intval( ini_get( 'post_max_size' ) ) );
    }
}
function nbd_get_max_input_var(){
    return abs( intval( ini_get( 'max_input_vars' ) ) );
}
function nbd_check_cart_item_exist( $cart_item_key ){
    global $woocommerce;
    $check = false;
    foreach( $woocommerce->cart->get_cart() as $key => $val ) {
        if( $cart_item_key ==  $key )  $check = true;
    }
    return $check;
}
function nbd_die( $result ){
    echo json_encode( $result );
    wp_die();
}
function nbd_exec($cmd) {
    $output = array();
    exec("$cmd 2>&1", $output);
    return $output;
}
function get_wpml_original_id( $id, $type = 'post', $current_lang = false ){
    if (class_exists('SitePress')) {
        global $sitepress;
        $langcode = $sitepress->get_default_language();
        if( $current_lang ){
            $langcode = $sitepress->get_current_language();
        }
        $id = icl_object_id($id, $type, true, $langcode);
    }
    return $id;
}
function get_wpml_current_id( $id, $type = 'post' ){
    if ( class_exists('SitePress') ) {
        return icl_object_id($id,'post',false);
    } 
    return $id;
}
function nbd_get_artist_info( $user_id ){
    $infos = array();
    if( absint( $user_id ) == 0 ) return $infos;
    $infos['nbd_artist_name']           = get_the_author_meta( 'nbd_artist_name', $user_id );
    $infos['nbd_artist_phone']          = get_the_author_meta( 'nbd_artist_phone', $user_id );
    $infos['nbd_sell_design']           = get_the_author_meta( 'nbd_sell_design', $user_id );
    $infos['nbd_create_design']         = get_the_author_meta( 'nbd_create_design', $user_id );
    $infos['nbd_auto_approve_design']   = get_the_author_meta( 'nbd_auto_approve_design', $user_id );
    $infos['nbd_feature_designer']      = get_the_author_meta( 'nbd_feature_designer', $user_id );
    $infos['nbd_artist_banner']         = get_the_author_meta( 'nbd_artist_banner', $user_id );
    $infos['nbd_artist_address']        = get_the_author_meta( 'nbd_artist_address', $user_id );
    $infos['nbd_artist_facebook']       = get_the_author_meta( 'nbd_artist_facebook', $user_id );
    $infos['nbd_artist_twitter']        = get_the_author_meta( 'nbd_artist_twitter', $user_id );
    $infos['nbd_artist_linkedin']       = get_the_author_meta( 'nbd_artist_linkedin', $user_id );
    $infos['nbd_artist_youtube']        = get_the_author_meta( 'nbd_artist_youtube', $user_id );
    $infos['nbd_artist_instagram']      = get_the_author_meta( 'nbd_artist_instagram', $user_id );
    $infos['nbd_artist_flickr']         = get_the_author_meta( 'nbd_artist_flickr', $user_id );
    $infos['nbd_artist_commission']     = get_the_author_meta( 'nbd_artist_commission', $user_id );
    $infos['nbd_artist_description']    = get_the_author_meta( 'nbd_artist_description', $user_id );
    $infos['nbd_payment']               = get_the_author_meta( 'nbd_payment', $user_id );
    $infos['banner_width']              = absint( nbdesigner_get_option( 'nbdesigner_designer_banner_width', 1050 ) );
    $infos['banner_height']             = absint( nbdesigner_get_option( 'nbdesigner_designer_banner_height', 200 ) );
    return $infos;
}
function nbd_user_logged_in(){
    return is_user_logged_in() ? 1 : 0;
}
function nbd_get_pages(){
    $pages = get_pages();
    $_pages = array(
        '0' => 'Default'
    );
    foreach($pages as $page) { 
        $id = $page->ID;
        $_pages[$id] = $page->post_title;
    }
    return $_pages;
}
function is_nbd_designer( $user_id ){
    $can_edit_template = get_user_meta( $user_id, 'nbd_create_design');
    return ( $can_edit_template[0] == 'on' ) ? true : false;
}
function user_can_edit_template( $user_id, $template_id = 0 ){
    $current_user_id = get_current_user_id();
    return ( ($user_id == $current_user_id) && is_nbd_designer($user_id) ) ? true : false;
}
function nbd_get_font_by_alias( $alias ){
    $fonts = array();
    if(file_exists( NBDESIGNER_DATA_DIR . '/fonts.json') ){
        $fonts = (array)json_decode( file_get_contents( NBDESIGNER_DATA_DIR . '/fonts.json' ) );
    }
    foreach ($fonts as $font) {
        if ($font->alias == $alias) {
            return $font;
        }
    }
    return false;
}
function nbd_get_font_by_name( $name ){
    $fonts = array();
    if(file_exists( NBDESIGNER_DATA_DIR . '/fonts.json') ){
        $fonts = (array)json_decode( file_get_contents( NBDESIGNER_DATA_DIR . '/fonts.json' ) );
    }
    foreach ($fonts as $font) {
        if ($font->name == $name) {
            return $font;
        }
    }
    return false;
}
function nbd_get_products_has_design( $get_for_launcher = false ){
    if( !is_dokan() || $get_for_launcher ){
        $nbd_products = get_transient( 'nbd_frontend_products' );
        if( false === $nbd_products ){
            $products = nbd_get_all_product_has_design();
            foreach ($products as $pro){
                $without_design         = get_post_meta( $pro->ID, '_nbdesigner_enable_upload_without_design', true );
                if( !$without_design ){
                    $product                = wc_get_product( $pro->ID );
                    $type                   = $product->get_type();
                    $image                  = get_the_post_thumbnail_url( $pro->ID, 'post-thumbnail' );
                    if( !$image ) $image    = wc_placeholder_img_src();
                    $allow_upload_solid     = false;
                    $option = unserialize( get_post_meta( $product->get_id(), '_nbdesigner_option', true ) );
                    $allow_upload_solid = isset( $option['upload_solid_design'] ) ? $option['upload_solid_design'] : 0;

                    $result[] = array(
                        'product_id'            => $pro->ID,
                        'name'                  => $pro->post_title,
                        'src'                   => $image,
                        'type'                  => $type,
                        'url'                   => get_permalink($pro->ID),
                        'allow_upload_solid'    => $allow_upload_solid
                    );
                }
            }
            set_transient( 'nbd_frontend_products' , $result );
        }else{
            $result = $nbd_products;
        }
    }else{
        $result     = array();
        $products   = nbd_get_all_product_of_vendor_has_design();
        foreach ( $products as $pro ){
            $product    = wc_get_product( $pro->ID );
            $type       = $product->get_type();
            $image      = get_the_post_thumbnail_url($pro->ID, 'post-thumbnail');
            if( !$image ) $image = wc_placeholder_img_src();
            $result[] = array(
                'product_id'    => $pro->ID,
                'name'          => $pro->post_title,
                'src'           => $image,
                'type'          => $type,
                'url'           => get_permalink( $pro->ID )
            );
        }
    }
    return $result;
}
function nbd_get_all_product_of_vendor_has_design(){
    $args_query = array(
        'post_type'         => 'product',
        'post_status'       => 'publish',
        'meta_key'          => '_nbdesigner_enable',
        'orderby'           => 'date',
        'order'             => 'DESC',
        'posts_per_page'    => -1,
        'author'            => get_current_user_id(),
        'meta_query'        => array(
            array(
                'key'   => '_nbdesigner_enable',
                'value' => 1,
            )
        )
    ); 
    $posts = get_posts( $args_query );
    return $posts;
}
function nbd_get_all_product_has_design(){
    $args_query = array(
        'post_type'         => 'product',
        'post_status'       => 'publish',
        'meta_key'          => '_nbdesigner_enable',
        'orderby'           => 'date',
        'order'             => 'DESC',
        'posts_per_page'    => -1,
        'meta_query'        => array(
            array(
                'key'   => '_nbdesigner_enable',
                'value' => 1,
            )
        )
    );
    $posts = get_posts($args_query);
    return $posts;
}
function nbd_bulk_variations_add_to_cart_message( $count ) {
    if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) :
        $return_to = ( wp_get_referer() ) ? wp_get_referer() : home_url();
        $message   = sprintf( '<a href="%s" class="button">%s</a> %s', $return_to, esc_html__( 'Continue Shopping &rarr;', 'web-to-print-online-designer' ), sprintf( esc_html__( '%s products successfully added to your cart.', 'web-to-print-online-designer' ), $count ) );
    else :
        $message = sprintf( '<a href="%s" class="button">%s</a> %s', get_permalink( wc_get_page_id( 'cart' ) ), esc_html__( 'View Cart &rarr;', 'woocommerce' ), sprintf( esc_html__( '%s products successfully added to your cart.', 'web-to-print-online-designer' ), $count ) );
    endif;
    wc_add_notice( $message );
}
function nbd_zip_files_and_download( $file_names, $archive_file_name, $nameZip, $option_name = array(), $download = true ){
    if ( empty( $file_names ) || !is_array( $file_names ) ) {
        return;
    }
    if(file_exists($archive_file_name)){
        unlink($archive_file_name);
    }
    if ( class_exists( 'ZipArchive' ) ) {
        $zip = new ZipArchive();
        if ( $zip->open( $archive_file_name, ZIPARCHIVE::CREATE ) !== TRUE ) {
          exit( "cannot open <$archive_file_name>\n" );
        }
        foreach( $file_names as $key => $file ) {
            if ( !file_exists( $file ) ) {
                continue;
            }
            if( !empty( $option_name ) && is_array( $option_name ) ){
                $file_name  = pathinfo( $file, PATHINFO_FILENAME );
                $file_ext   = pathinfo( $file, PATHINFO_EXTENSION );
                foreach ( $option_name as $k => $val ){
                    if( is_int( strpos( $file_name, $k ) ) ) $file_name = $val;
                }
                $name = $file_name .'.'. $file_ext;
            }else{
                $path_arr = explode( '/', str_replace( '\\', '/', $file ) );
                $cnt = count($path_arr);
                $name = ($cnt >= 2 ? $path_arr[$cnt - 2] . '_' : '') . basename( $file );
            }
            $zip->addFile( $file, $name );
        }
        $zip->close();
    }else{
        require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
        $archive = new PclZip($archive_file_name);
        foreach($file_names as $file){
            if ( !file_exists( $file ) ) {
                continue;
            }
            $path_arr = explode('/', str_replace( '\\', '/', $file ));
            $dir = dirname($file).'/';
            $cnt = count($path_arr);
            $add_path = $cnt >= 2 ? $path_arr[$cnt - 2] : '';
            $archive->add($file, PCLZIP_OPT_REMOVE_PATH, $dir, PCLZIP_OPT_ADD_PATH, $add_path);
        }
    }
    if( $download ){
        if ( !is_file( $archive_file_name ) ){
            header( (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1') .' 404 Not Found' );
            exit;
        } elseif ( !is_readable( $archive_file_name ) ){
            header( (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1') .' 403 Forbidden' );
            exit;
        } else {
            NBD_Download::download_file( $archive_file_name, $nameZip );
            exit;
        }
    }
}
function nbd_convert_svg_embed( $path ){
    $svgs = Nbdesigner_IO::get_list_files_by_type($path, 1, 'svg');
    $svg_path = $path . '/svg';
    if( !file_exists( $svg_path ) ) wp_mkdir_p( $svg_path );
    foreach ( $svgs as $svg ){
        $svg_name = pathinfo( $svg, PATHINFO_BASENAME );
        $new_svg_path = $svg_path.'/'.$svg_name;
        $xdoc = new DomDocument;
        $content_xml = preg_replace ('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', ' ', file_get_contents($svg) );
        $xdoc->LoadXML($content_xml);
        /* Embed images */
        $images = $xdoc->getElementsByTagName('image');
        for ($i = 0; $i < $images->length; $i++) {
            $tagName = $xdoc->getElementsByTagName('image')->item($i);
            $attribNode = $tagName->getAttributeNode('xlink:href');
            $img_src = $attribNode->value;
            if(strpos($img_src, "data:image")!==FALSE)
            continue;
            if(strpos($img_src, "data:img")!==FALSE)
            continue;
            $type = pathinfo($img_src, PATHINFO_EXTENSION);
            $type = ($type =='svg' ) ? 'svg+xml' : $type;
            $data = nbd_file_get_contents($img_src);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $tagName->setAttribute('xlink:href', $base64);
        }
        /* Embed fonts */
        $text_elements = $xdoc->getElementsByTagName('text');
        for ($i = 0; $i < $text_elements->length; $i++) {
            $tagName = $xdoc->getElementsByTagName('text')->item($i);
            $attribNode = $tagName->getAttributeNode('font-family');
            $font_family = $attribNode->value;
            $font = nbd_get_font_by_alias($font_family);
            if( $font ){
                $tagName->setAttribute('font-family', $font->name);
            }
        }
        $new_svg = $xdoc->saveXML();
        file_put_contents($new_svg_path, $new_svg);
    }
}
function nbd_export_pdfs( $nbd_item_key, $watermark = true, $force = false, $showBleed = 'no', $extra = null ){
    if( !class_exists('TCPDF') ){
        require_once( NBDESIGNER_PLUGIN_DIR.'includes/tcpdf/tcpdf.php' );
    }
    require_once( NBDESIGNER_PLUGIN_DIR . 'includes/fpdi/autoload.php' );

    $path           = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key;
    $_watermark     = $watermark ? '-watermark' : '';
    $_force         = $force ? '-force' : '';
    $_showBleed     = $showBleed == 'yes' ? '-bleed' : '';
    $folder         = $path . '/customer-pdfs' . $_watermark . $_force . $_showBleed;
    $output_file    = $folder .'/'. $nbd_item_key . '.pdf';
    $result         = array();
    if( !file_exists( $folder ) ) {
        wp_mkdir_p( $folder );
    }

    if( nbdesigner_get_option( 'nbdesigner_enable_cloud2print_api', 'no' ) == 'yes' ){
        nbd_cloud_export_pdfs( $nbd_item_key, $watermark, $force, $showBleed, null, false );
        $output_file    = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key. '/customer-pdfs' . '/' . $nbd_item_key . '.pdf';
        if( $force ){
            $result[] = $output_file;
        } else {
            $result = Nbdesigner_IO::get_list_files_by_type( NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key. '/customer-pdfs', 1, 'pdf' );
        }
    } else {
        $datas      = unserialize( file_get_contents( $path .'/product.json' ) );
        $option     = unserialize( file_get_contents( $path .'/option.json' ) );
        $config     = nbd_get_data_from_json( $path . '/config.json' );
        if( isset( $config->product ) && count( $config->product ) ){
            $datas = array();
            foreach( $config->product as $side ){
                $datas[] = (array)$side;
            }
        };
        $layout         = isset( $option['layout'] ) ? $option['layout'] : 'm';
        $used_font_path = $path . '/used_font.json';
        $used_font      = json_decode( file_get_contents( $used_font_path ) );
        $path_font      = array();
        foreach( $used_font as $font ){
            $font_name = $font->name;
            if( $font->type == 'google' ){
                $path_font = nbd_download_google_font($font_name);;
            }else{
                $has_custom_font = true;
                $_font = nbd_get_font_by_alias($font->alias);
                foreach( $_font->file as $key => $font_file ){
                    $path_font[$key] = NBDESIGNER_FONT_DIR . '/' . $font_file;
                }
            }
            $true_type = nbd_get_truetype_fonts();
            if (in_array($font_name, $true_type)) {
                foreach($path_font as $pfont){
                    $fontname = TCPDF_FONTS::addTTFfont($pfont, 'TrueType', '', 32);
                }
            }else{
                foreach($path_font as $pfont){
                    $fontname = TCPDF_FONTS::addTTFfont($pfont, '', '', 32);
                }
            }
        }
        $pdfs       = array();
        $unit       = isset( $option['unit'] ) ? $option['unit'] : nbdesigner_get_option( 'nbdesigner_dimensions_unit', 'cm' );
        $unitRatio  = 10;
        $cm2Px      = 37.795275591;
        $mm2Px      = $cm2Px / 10;
        $dpi        = (float)$option['dpi'];
        $dpi        = $dpi > 0 ? $dpi : 300;
        switch ($unit) {
            case 'mm':
                $unitRatio = 1;
                break;
            case 'in':
                $unitRatio = 25.4;
                break;
            case 'ft':
                $unitRatio = 304.8;
                break;
            case 'px':
                $unitRatio = 25.4 / $dpi;
                break;
            default:
                $unitRatio = 10;
                break;
        }
        $list_images = Nbdesigner_IO::get_list_images( $path, 1 );
        foreach ( $list_images as $img ){
            $name                   = basename($img);
            $arr                    = explode('.', $name);
            $_frame                 = explode('_', $arr[0]);
            $frame                  = $_frame[1];
            $list_design[$frame]    = $img;
        }
        foreach( $datas as $key => $data ){
            $contentImage = '';
            if( isset( $list_design[$key] ) ) $contentImage = $list_design[$key];
            $proWidth   = $data['product_width'];
            $proHeight  = $data['product_height'];

            $pdfs[$key]['background']           = $data['img_src'];
            $pdfs[$key]['bg_type']              = $data['bg_type'];
            $pdfs[$key]['bg_color_value']       = $data['bg_color_value'];
            $pdfs[$key]['cd-top']               = $data['real_top'] * $unitRatio;
            $pdfs[$key]['cd-left']              = $data['real_left'] * $unitRatio;
            $pdfs[$key]['cd-width']             = $data['real_width'] * $unitRatio;
            $pdfs[$key]['cd-height']            = $data['real_height'] * $unitRatio;
            $pdfs[$key]['customer-design']      = $contentImage;
            $pdfs[$key]['product-width']        = round( $proWidth * $unitRatio, 2 );
            $pdfs[$key]['product-height']       = round( $proHeight * $unitRatio, 2 );
            $pdfs[$key]['margin-top']           = $pdfs[$key]['margin-right'] = $pdfs[$key]['margin-bottom'] = $pdfs[$key]['margin-left'] = 0;
            $pdfs[$key]['bleed-top']            = $pdfs[$key]['bleed-bottom'] = $unitRatio * $data['bleed_top_bottom'];
            $pdfs[$key]['bleed-left']           = $pdfs[$key]['bleed-right'] = $unitRatio * $data['bleed_left_right'];
            $pdfs[$key]['origin_bg_pdf']        = isset( $data['origin_bg_pdf'] ) ? $data['origin_bg_pdf'] : '';

            $includeBg                          = isset( $data['include_background'] ) ? $data['include_background'] : 1;

            $pdfs[$key]['include_background']   = $includeBg;
            if( $includeBg == 0 && $data['bg_type'] == 'image' ){
                $pdfs[$key]['product-width']        = $pdfs[$key]['cd-width'];
                $pdfs[$key]['product-height']       = $pdfs[$key]['cd-height'];
                $pdfs[$key]['cd-left']              = $pdfs[$key]['cd-top'] = 0;
                $pdfs[$key]['include_background']   = 0;
            }
        }
        $mTop       = $pdfs[0]["margin-top"];
        $mBottom    = $pdfs[0]["margin-bottom"];
        $mLeft      = $pdfs[0]["margin-left"];
        $mRight     = $pdfs[0]["margin-right"];
        $bgWidth    = $pdfs[0]['product-width'];
        $bgHeight   = $pdfs[0]['product-height'];
        
        $pWidth         = $bgWidth + $mLeft + $mRight;
        $pHeight        = $bgHeight + $mTop + $mBottom;
        $pdf_format     = array( $pWidth, $pHeight );
        if($pWidth > $pHeight){
            $orientation = "L";
        }else {
            $orientation = "P";
        }

        $pdf = new Fpdi\TcpdfFpdi( $orientation, 'mm', $pdf_format, true, 'UTF-8', false );

        $pdf->SetMargins( $mLeft, $mTop, $mRight, true );
        $pdf->SetCreator( get_site_url() );
        $pdf->SetTitle( get_bloginfo( 'name' ) );
        $pdf->setPrintHeader( false );
        $pdf->setPrintFooter( false );
        $pdf->SetAutoPageBreak( TRUE, 0 );

        foreach( $pdfs as $key => $_pdf ){
            $customer_design    = $_pdf['customer-design'];
            $bTop               = (float)$_pdf['bleed-top'];
            $bLeft              = (float)$_pdf['bleed-left'];
            $bRight             = (float)$_pdf['bleed-right'];
            $bBottom            = (float)$_pdf['bleed-bottom']; 
            $cdTop              = (float)$_pdf["cd-top"];
            $cdLeft             = (float)$_pdf["cd-left"];
            $cdWidth            = (float)$_pdf["cd-width"];
            $cdHeight           = (float)$_pdf["cd-height"];
            $background         = $_pdf['background'];
            $bg_type            = $_pdf['bg_type'];
            $bg_color_value     = $_pdf['bg_color_value'];
            $includeBg          = isset( $_pdf['include_background'] ) ? $_pdf['include_background'] : 1;
            $includeBg          = ( $bg_type == 'image' ) ? $includeBg : 1;

            if($bg_type == 'image'){
                $path_bg = ( absint($background) > 0 ) ? get_attached_file($background) : Nbdesigner_IO::convert_url_to_path( $background );
            }
            if( !$force ){
                $bgWidth    = (float)$_pdf['product-width'];
                $bgHeight   = (float)$_pdf['product-height'];
                $mTop       = (float)$_pdf["margin-top"];
                $mLeft      = (float)$_pdf["margin-left"];
                $mRight     = (float)$_pdf["margin-right"];
                $mBottom    = (float)$_pdf["margin-bottom"];

                if( $includeBg == 0 ){
                    $bgWidth    = $cdWidth;
                    $bgHeight   = $cdHeight;
                    $mLeft      = $mRight = $mBottom = $mTop = $cdTop = $cdLeft = 0;
                }
                $pWidth         = $bgWidth + $mLeft + $mRight;
                $pHeight        = $bgHeight + $mTop + $mBottom;
                $pdf_format     = array( $pWidth, $pHeight );
                $pdf_format     = apply_filters( 'nbd_format_pdf', $pdf_format, $_pdf, $nbd_item_key, $extra );
                if( $pWidth > $pHeight ){
                    $orientation = "L";
                } else {
                    $orientation = "P";
                }

                $pdf = new Fpdi\TcpdfFpdi( $orientation, 'mm', $pdf_format, true, 'UTF-8', false );

                $pdf->SetMargins( $mLeft, $mTop, $mRight, true );
                $pdf->SetCreator( get_site_url() );
                $pdf->SetTitle( get_bloginfo( 'name' ) );
                $pdf->setPrintHeader( false );
                $pdf->setPrintFooter( false );
                $pdf->SetAutoPageBreak( TRUE, 0 );
            }
            $pdf->AddPage();
            if( $bg_type == 'image' && $path_bg && $_pdf['include_background'] ){
                $img_ext    = array( 'jpg', 'jpeg', 'png' );
                $svg_ext    = array( 'svg' );
                $eps_ext    = array( 'eps', 'ai' );
                $pdf_ext    = array( 'pdf' );

                $check_img  = Nbdesigner_IO::checkFileType( basename( $path_bg ), $img_ext );
                $check_svg  = Nbdesigner_IO::checkFileType( basename( $path_bg ), $svg_ext );
                $check_eps  = Nbdesigner_IO::checkFileType( basename( $path_bg ), $eps_ext );
                $check_pdf  = Nbdesigner_IO::checkFileType( basename( $path_bg ), $pdf_ext );

                $ext        = pathinfo( $path_bg );
                if( $check_img && $_pdf['origin_bg_pdf'] == '' ){
                    $pdf->Image( $path_bg, $mLeft, $mTop, $bgWidth, $bgHeight, '', '', '', false, '' );
                }
                if( $check_svg && $_pdf['origin_bg_pdf'] == '' ){
                    $pdf->ImageSVG( $path_bg, $mLeft, $mTop, $bgWidth, $bgHeight, '', '', '', 0, true );
                }
                if( $check_eps && $_pdf['origin_bg_pdf'] == '' ){
                   $pdf->ImageEps( $path_bg, $mLeft,$mTop, $bgWidth, $bgHeight, '', true, '', '', 0, true );
                }
                if( $check_pdf || $_pdf['origin_bg_pdf'] != '' ){
                    $pdf_path = $path_bg;
                    if( $_pdf['origin_bg_pdf'] != '' ){
                        $pdf_path = NBDESIGNER_TEMP_DIR . $_pdf['origin_bg_pdf'];
                    }
                    
                    $number_pages = $pdf->setSourceFile( $pdf_path );

                    $page_index = 1;
                    if( $_pdf['origin_bg_pdf'] != '' && ( $number_pages - 1 ) >= $key ){
                        $page_index = $key + 1;
                    }

                    $pdf->tplId = $pdf->importPage( $page_index );
                    $pdf->useImportedPage( $pdf->tplId, 0, 0, $pWidth );
                }
            }elseif($bg_type == 'color') {
                $need_bg_color = true;

                if( isset( $config->areaDesignShapes ) && $config->areaDesignShapes[$key] ){
                    $need_bg_color  = false;
                }
                if( $datas[$key]['show_overlay'] == 1 && $datas[$key]['include_overlay'] == 1 ){
                    $need_bg_color  = true;
                }

                if( $need_bg_color ){
                    $pdf->Rect( $mLeft, $mTop, $bgWidth, $bgHeight, 'F', '', hex_code_to_rgb( $bg_color_value ) );
                }
            }
            if( $showBleed == 'yes' && nbdesigner_get_option( 'nbdesigner_bleed_stack', 1 ) == 1 ){
                $pdf->Line(0, $mTop + $bTop, $mLeft + $bLeft, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line(0, $mTop + $bgHeight - $bBottom, $mLeft + $bLeft, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($bgWidth + $mLeft - $bRight, $mTop + $bTop, $bgWidth + $mLeft + $mRight, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($bgWidth + $mLeft - $bRight, $mTop + $bgHeight - $bBottom, $bgWidth + $mLeft + $mRight, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bLeft, 0, $mLeft + $bLeft, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bLeft, $mTop + $bgHeight - $bBottom, $mLeft + $bLeft, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bgWidth - $bRight, 0, $mLeft + $bgWidth - $bRight, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bgWidth - $bRight, $mTop + $bgHeight - $bBottom, $mLeft + $bgWidth - $bRight, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
            }
            if( $customer_design != '' ){
                $include_pdf = false;
                if( isset( $config->originPDFs ) && isset( $config->originPDFs[$key] ) && isset( $config->pdfStacks ) ){
                    $resource_pdfs = (array)$config->originPDFs[$key];
                    if( count( $resource_pdfs ) && $config->pdfStacks[$key] != '' ){
                        $include_pdf    = true;
                        $stack          = explode( '_', $config->pdfStacks[$key] );
                        $pdf_index      = 0;
                        foreach( $stack as $pos ){
                            if( $pos == 'P' ){
                                $resource_pdf   = (array)$resource_pdfs[$pdf_index];
                                $pdf_path       = NBDESIGNER_TEMP_DIR . $resource_pdf['origin_pdf'];
                                $pdf_top        = $resource_pdf['top'] * $unitRatio + $mTop + $cdTop;
                                $pdf_left       = $resource_pdf['left'] * $unitRatio + $mLeft + $cdLeft;
                                $pdf_width      = $resource_pdf['width'] * $unitRatio;
                                $pdf_height     = $resource_pdf['height'] * $unitRatio;

                                $pdf->setSourceFile( $pdf_path );
                                $tplId = $pdf->importPage( 1 );
                                $pdf->useImportedPage( $tplId, $pdf_left, $pdf_top, $pdf_width, $pdf_height );

                                $pdf_index++;
                            }else{
                                $svg = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/part/svgpath/frame_' . $key . '_svg_part_' . $pos . '.svg';
                                nbd_convert_svg_url( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/part/', 'frame_' . $key . '_svg_part_' . $pos . '.svg' );
                                $pdf->ImageSVG( $svg, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth, $cdHeight, '', '', '', 0, true );
                            }
                        }
                    }
                }
                if( !$include_pdf ){
                    $svg = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/svgpath/frame_' . $key . '_svg.svg';
                    nbd_convert_svg_url( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/', 'frame_' . $key . '_svg.svg' );
                    $pdf->ImageSVG( $svg, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth, $cdHeight, '', '', '', 0, true );
                }
            }
            if($showBleed == 'yes' && nbdesigner_get_option( 'nbdesigner_bleed_stack', 1 ) == 2){
                $pdf->Line(0, $mTop + $bTop, $mLeft, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line(0, $mTop + $bgHeight - $bBottom, $mLeft, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($bgWidth + $mLeft, $mTop + $bTop, $bgWidth + $mLeft + $mRight, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($bgWidth + $mLeft, $mTop + $bgHeight - $bBottom, $bgWidth + $mLeft + $mRight, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bLeft, 0, $mLeft + $bLeft, $mTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bLeft, $mTop + $bgHeight, $mLeft + $bLeft, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bgWidth - $bRight, 0, $mLeft + $bgWidth - $bRight, $mTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bgWidth - $bRight, $mTop + $bgHeight, $mLeft + $bgWidth - $bRight, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
            }
            if( $datas[$key]['show_overlay'] == 1 && $datas[$key]['include_overlay'] == 1 && $layout != 'c' ){
                $overlay = Nbdesigner_IO::convert_url_to_path( $datas[$key]['img_overlay'] );
                $check_over_img = Nbdesigner_IO::checkFileType(basename($overlay), array('jpg','jpeg','png'));
                if( $check_over_img ){
                    //$pdf->Image($overlay, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth,$cdHeight, '', '', '', false, '');
                    $pdf->Image($overlay, $mLeft, $mTop,  $bgWidth, $bgHeight, '', '', '', false, '');
                }
            }
            if( $watermark ){
                $watermark_type = nbdesigner_get_option( 'nbdesigner_pdf_watermark_type', 1 );
                if($watermark_type == 1){
                    $watermark_image = nbdesigner_get_option( 'nbdesigner_pdf_watermark_image', '' );
                    $watermark_file = get_attached_file( $watermark_image );
                    if( $watermark_file ){
                        $myPageWidth        = $pdf->getPageWidth();
                        $myPageHeight       = $pdf->getPageHeight();
                        list($watermark_width, $watermark_height) = getimagesize($watermark_file);
                        $watermark_width    = $watermark_width/72*25.4;
                        $watermark_height   = $watermark_height/72*25.4;
                        $myX                = ( $myPageWidth - $watermark_width )/2 > 0 ? ( $myPageWidth - $watermark_width )/2 : 0;
                        $myY                = ( $myPageHeight - $watermark_height )/2 > 0 ? ( $myPageHeight - $watermark_height )/2 : 0;
                        $pdf->SetAlpha(0.2);
                        $pdf->StartTransform();
                        $pdf->Rotate(45, $myPageWidth / 2, $myPageHeight / 2);
                        $pdf->Image($watermark_file, $myX, $myY, '', '', '', '', '', false);
                        $pdf->StopTransform();
                        $pdf->SetAlpha(1);
                    }
                }else{
                    $watermark_text     = nbdesigner_get_option('nbdesigner_pdf_watermark_text');
                    $vfont              = "helvetica";
                    $vfontsize          = 20;
                    $vfontbold          = "B";

                    $widthtext = $pdf->GetStringWidth(trim($watermark_text), $vfont, $vfontbold, $vfontsize, false );
                    $widthtextcenter    = round(($widthtext * sin(deg2rad(45))) / 2 ,0);
                    $myPageWidth        = $pdf->getPageWidth();
                    $myPageHeight       = $pdf->getPageHeight();
                    $myX                = ( $myPageWidth - $widthtext )/2 > 0 ? ( $myPageWidth - $widthtext )/2 : 0;
                    $myY                = $myPageHeight / 2;
                    $pdf->SetAlpha(0.2);
                    $pdf->StartTransform();
                    $pdf->Rotate(45, $myPageWidth / 2, $myPageHeight / 2);
                    $pdf->SetFont($vfont, $vfontbold, $vfontsize);
                    $pdf->Text($myX, $myY ,trim($watermark_text));
                    $pdf->StopTransform();
                    $pdf->SetAlpha(1);
                }
            }
            if( isset( $config->contour ) ){
                $pdf->ImageSVG( '@' . $config->contour, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth, $cdHeight, '', '', '', 0, true );
            }
            do_action( 'after_nbd_pdf', $pdf, $_pdf, $nbd_item_key, $extra );
            if( !$force ){
                $output_file = $folder .'/'. $nbd_item_key .'_'.$key.'.pdf';
                $pdf->Output($output_file, 'F');
                $result[] = $output_file;
            }
        }
        if( $force ){
            $pdf->Output( $output_file, 'F' );
            $result[] = $output_file;
        }
    }

    return $result;
}
//custom charmingprint: export pdf
function nbd_custom_export_pdfs( $nbd_item_key, $watermark = true, $force = false, $showBleed = 'no', $color = '' , $_background = false ,  $extra = null ){
    if( !class_exists('TCPDF') ){
        require_once( NBDESIGNER_PLUGIN_DIR.'includes/tcpdf/tcpdf.php' );
    }
    require_once( NBDESIGNER_PLUGIN_DIR . 'includes/fpdi/autoload.php' );

    $path           = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key;
    $_watermark     = $watermark ? '-watermark' : '';
    $_force         = $force ? '-force' : '';
    $_showBleed     = $showBleed == 'yes' ? '-bleed' : '';
    $folder         = $path . '/customer-pdfs' . $_watermark . $_force . $_showBleed;
    $color_name     = $color ? '_'.str_replace( '#' , '' ,$color) : '';
    $output_file    = $folder .'/'. $nbd_item_key .$color_name.'.pdf';
    if($_background) {
        $output_file    = $folder .'/'. $nbd_item_key.'_background.pdf';
    }
    $result         = array();
    if( !file_exists( $folder ) ) {
        wp_mkdir_p( $folder );
    }
    if( nbdesigner_get_option( 'nbdesigner_enable_cloud2print_api', 'no' ) == 'yes' ){
        // custom charmingprint
        nbd_custom_cloud_export_pdfs( $nbd_item_key, $watermark, $force, $showBleed, null, false , $color , $_background );
        // $output_file    = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key. '/customer-pdfs' . '/' . $nbd_item_key . '.pdf';
        $output_file    = $folder .'/'. $nbd_item_key .$color_name.'.pdf';
        if($_background) {
            $output_file    = $folder .'/'. $nbd_item_key.'_background.pdf';
        }
        if( $force ){
            $result[] = $output_file;
        } else {
            $result = Nbdesigner_IO::get_list_files_by_type( NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key. '/customer-pdfs', 1, 'pdf' );
        }
    } else {
        $datas      = unserialize( file_get_contents( $path .'/product.json' ) );
        $option     = unserialize( file_get_contents( $path .'/option.json' ) );
        $config     = nbd_get_data_from_json( $path . '/config.json' );
        if( isset( $config->product ) && count( $config->product ) ){
            $datas = array();
            foreach( $config->product as $side ){
                $datas[] = (array)$side;
            }
        };
        $layout         = isset( $option['layout'] ) ? $option['layout'] : 'm';
        $used_font_path = $path . '/used_font.json';
        $used_font      = json_decode( file_get_contents( $used_font_path ) );
        $path_font      = array();
        foreach( $used_font as $font ){
            $font_name = $font->name;
            if( $font->type == 'google' ){
                $path_font = nbd_download_google_font($font_name);;
            }else{
                $has_custom_font = true;
                $_font = nbd_get_font_by_alias($font->alias);
                foreach( $_font->file as $key => $font_file ){
                    $path_font[$key] = NBDESIGNER_FONT_DIR . '/' . $font_file;
                }
            }
            $true_type = nbd_get_truetype_fonts();
            if (in_array($font_name, $true_type)) {
                foreach($path_font as $pfont){
                    $fontname = TCPDF_FONTS::addTTFfont($pfont, 'TrueType', '', 32);
                }
            }else{
                foreach($path_font as $pfont){
                    $fontname = TCPDF_FONTS::addTTFfont($pfont, '', '', 32);
                }
            }
        }
        $pdfs       = array();
        $unit       = isset( $option['unit'] ) ? $option['unit'] : nbdesigner_get_option( 'nbdesigner_dimensions_unit', 'cm' );
        $unitRatio  = 10;
        $cm2Px      = 37.795275591;
        $mm2Px      = $cm2Px / 10;
        $dpi        = (float)$option['dpi'];
        $dpi        = $dpi > 0 ? $dpi : 300;
        switch ($unit) {
            case 'mm':
                $unitRatio = 1;
                break;
            case 'in':
                $unitRatio = 25.4;
                break;
            case 'ft':
                $unitRatio = 304.8;
                break;
            case 'px':
                $unitRatio = 25.4 / $dpi;
                break;
            default:
                $unitRatio = 10;
                break;
        }
        $list_images = Nbdesigner_IO::get_list_images( $path, 1 );
        foreach ( $list_images as $img ){
            $name                   = basename($img);
            $arr                    = explode('.', $name);
            $_frame                 = explode('_', $arr[0]);
            $frame                  = $_frame[1];
            $list_design[$frame]    = $img;
        }
        foreach( $datas as $key => $data ){
            $contentImage = '';
            if( isset( $list_design[$key] ) ) $contentImage = $list_design[$key];
            $proWidth   = $data['product_width'];
            $proHeight  = $data['product_height'];

            $pdfs[$key]['background']           = $data['img_src'];
            $pdfs[$key]['bg_type']              = $data['bg_type'];
            $pdfs[$key]['bg_color_value']       = $data['bg_color_value'];
            $pdfs[$key]['cd-top']               = $data['real_top'] * $unitRatio;
            $pdfs[$key]['cd-left']              = $data['real_left'] * $unitRatio;
            $pdfs[$key]['cd-width']             = $data['real_width'] * $unitRatio;
            $pdfs[$key]['cd-height']            = $data['real_height'] * $unitRatio;
            $pdfs[$key]['customer-design']      = $contentImage;
            $pdfs[$key]['product-width']        = round( $proWidth * $unitRatio, 2 );
            $pdfs[$key]['product-height']       = round( $proHeight * $unitRatio, 2 );
            $pdfs[$key]['margin-top']           = $pdfs[$key]['margin-right'] = $pdfs[$key]['margin-bottom'] = $pdfs[$key]['margin-left'] = 0;
            $pdfs[$key]['bleed-top']            = $pdfs[$key]['bleed-bottom'] = $unitRatio * $data['bleed_top_bottom'];
            $pdfs[$key]['bleed-left']           = $pdfs[$key]['bleed-right'] = $unitRatio * $data['bleed_left_right'];
            $pdfs[$key]['origin_bg_pdf']        = isset( $data['origin_bg_pdf'] ) ? $data['origin_bg_pdf'] : '';

            $includeBg                          = isset( $data['include_background'] ) ? $data['include_background'] : 1;

            $pdfs[$key]['include_background']   = $includeBg;
            if( $includeBg == 0 && $data['bg_type'] == 'image' ){
                $pdfs[$key]['product-width']        = $pdfs[$key]['cd-width'];
                $pdfs[$key]['product-height']       = $pdfs[$key]['cd-height'];
                $pdfs[$key]['cd-left']              = $pdfs[$key]['cd-top'] = 0;
                $pdfs[$key]['include_background']   = 0;
            }
        }
        $mTop       = $pdfs[0]["margin-top"];
        $mBottom    = $pdfs[0]["margin-bottom"];
        $mLeft      = $pdfs[0]["margin-left"];
        $mRight     = $pdfs[0]["margin-right"];
        $bgWidth    = $pdfs[0]['product-width'];
        $bgHeight   = $pdfs[0]['product-height'];
        
        $pWidth         = $bgWidth + $mLeft + $mRight;
        $pHeight        = $bgHeight + $mTop + $mBottom;
        $pdf_format     = array( $pWidth, $pHeight );
        if($pWidth > $pHeight){
            $orientation = "L";
        }else {
            $orientation = "P";
        }

        $pdf = new Fpdi\TcpdfFpdi( $orientation, 'mm', $pdf_format, true, 'UTF-8', false );

        $pdf->SetMargins( $mLeft, $mTop, $mRight, true );
        $pdf->SetCreator( get_site_url() );
        $pdf->SetTitle( get_bloginfo( 'name' ) );
        $pdf->setPrintHeader( false );
        $pdf->setPrintFooter( false );
        $pdf->SetAutoPageBreak( TRUE, 0 );

        foreach( $pdfs as $key => $_pdf ){
            $customer_design    = $_pdf['customer-design'];
            $bTop               = (float)$_pdf['bleed-top'];
            $bLeft              = (float)$_pdf['bleed-left'];
            $bRight             = (float)$_pdf['bleed-right'];
            $bBottom            = (float)$_pdf['bleed-bottom']; 
            $cdTop              = (float)$_pdf["cd-top"];
            $cdLeft             = (float)$_pdf["cd-left"];
            $cdWidth            = (float)$_pdf["cd-width"];
            $cdHeight           = (float)$_pdf["cd-height"];
            $background         = $_pdf['background'];
            $bg_type            = $_pdf['bg_type'];
            $bg_color_value     = $_pdf['bg_color_value'];
            $includeBg          = isset( $_pdf['include_background'] ) ? $_pdf['include_background'] : 1;
            $includeBg          = ( $bg_type == 'image' ) ? $includeBg : 1;

            if($bg_type == 'image'){
                $path_bg = ( absint($background) > 0 ) ? get_attached_file($background) : Nbdesigner_IO::convert_url_to_path( $background );
            }
            if( !$force ){
                $bgWidth    = (float)$_pdf['product-width'];
                $bgHeight   = (float)$_pdf['product-height'];
                $mTop       = (float)$_pdf["margin-top"];
                $mLeft      = (float)$_pdf["margin-left"];
                $mRight     = (float)$_pdf["margin-right"];
                $mBottom    = (float)$_pdf["margin-bottom"];

                if( $includeBg == 0 ){
                    $bgWidth    = $cdWidth;
                    $bgHeight   = $cdHeight;
                    $mLeft      = $mRight = $mBottom = $mTop = $cdTop = $cdLeft = 0;
                }
                $pWidth         = $bgWidth + $mLeft + $mRight;
                $pHeight        = $bgHeight + $mTop + $mBottom;
                $pdf_format     = array( $pWidth, $pHeight );
                $pdf_format     = apply_filters( 'nbd_format_pdf', $pdf_format, $_pdf, $nbd_item_key, $extra );
                if( $pWidth > $pHeight ){
                    $orientation = "L";
                } else {
                    $orientation = "P";
                }

                $pdf = new Fpdi\TcpdfFpdi( $orientation, 'mm', $pdf_format, true, 'UTF-8', false );

                $pdf->SetMargins( $mLeft, $mTop, $mRight, true );
                $pdf->SetCreator( get_site_url() );
                $pdf->SetTitle( get_bloginfo( 'name' ) );
                $pdf->setPrintHeader( false );
                $pdf->setPrintFooter( false );
                $pdf->SetAutoPageBreak( TRUE, 0 );
            }
            $pdf->AddPage();
            if( $bg_type == 'image' && $path_bg && $_pdf['include_background'] ){
                $img_ext    = array( 'jpg', 'jpeg', 'png' );
                $svg_ext    = array( 'svg' );
                $eps_ext    = array( 'eps', 'ai' );
                $pdf_ext    = array( 'pdf' );

                $check_img  = Nbdesigner_IO::checkFileType( basename( $path_bg ), $img_ext );
                $check_svg  = Nbdesigner_IO::checkFileType( basename( $path_bg ), $svg_ext );
                $check_eps  = Nbdesigner_IO::checkFileType( basename( $path_bg ), $eps_ext );
                $check_pdf  = Nbdesigner_IO::checkFileType( basename( $path_bg ), $pdf_ext );

                $ext        = pathinfo( $path_bg );
                if( $check_img && $_pdf['origin_bg_pdf'] == '' ){
                    $pdf->Image( $path_bg, $mLeft, $mTop, $bgWidth, $bgHeight, '', '', '', false, '' );
                }
                if( $check_svg && $_pdf['origin_bg_pdf'] == '' ){
                    $pdf->ImageSVG( $path_bg, $mLeft, $mTop, $bgWidth, $bgHeight, '', '', '', 0, true );
                }
                if( $check_eps && $_pdf['origin_bg_pdf'] == '' ){
                   $pdf->ImageEps( $path_bg, $mLeft,$mTop, $bgWidth, $bgHeight, '', true, '', '', 0, true );
                }
                if( $check_pdf || $_pdf['origin_bg_pdf'] != '' ){
                    $pdf_path = $path_bg;
                    if( $_pdf['origin_bg_pdf'] != '' ){
                        $pdf_path = NBDESIGNER_TEMP_DIR . $_pdf['origin_bg_pdf'];
                    }
                    
                    $number_pages = $pdf->setSourceFile( $pdf_path );

                    $page_index = 1;
                    if( $_pdf['origin_bg_pdf'] != '' && ( $number_pages - 1 ) >= $key ){
                        $page_index = $key + 1;
                    }

                    $pdf->tplId = $pdf->importPage( $page_index );
                    $pdf->useImportedPage( $pdf->tplId, 0, 0, $pWidth );
                }
            }elseif($bg_type == 'color') {
                $need_bg_color = true;

                if( isset( $config->areaDesignShapes ) && $config->areaDesignShapes[$key] ){
                    $need_bg_color  = false;
                }
                if( $datas[$key]['show_overlay'] == 1 && $datas[$key]['include_overlay'] == 1 ){
                    $need_bg_color  = true;
                }

                if( $need_bg_color ){
                    $pdf->Rect( $mLeft, $mTop, $bgWidth, $bgHeight, 'F', '', hex_code_to_rgb( $bg_color_value ) );
                }
            }
            if( $showBleed == 'yes' && nbdesigner_get_option( 'nbdesigner_bleed_stack', 1 ) == 1 ){
                $pdf->Line(0, $mTop + $bTop, $mLeft + $bLeft, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line(0, $mTop + $bgHeight - $bBottom, $mLeft + $bLeft, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($bgWidth + $mLeft - $bRight, $mTop + $bTop, $bgWidth + $mLeft + $mRight, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($bgWidth + $mLeft - $bRight, $mTop + $bgHeight - $bBottom, $bgWidth + $mLeft + $mRight, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bLeft, 0, $mLeft + $bLeft, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bLeft, $mTop + $bgHeight - $bBottom, $mLeft + $bLeft, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bgWidth - $bRight, 0, $mLeft + $bgWidth - $bRight, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bgWidth - $bRight, $mTop + $bgHeight - $bBottom, $mLeft + $bgWidth - $bRight, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
            }
            if( $customer_design != '' ){
                $include_pdf = false;
                if( isset( $config->originPDFs ) && isset( $config->originPDFs[$key] ) && isset( $config->pdfStacks ) ){
                    $resource_pdfs = (array)$config->originPDFs[$key];
                    if( count( $resource_pdfs ) && $config->pdfStacks[$key] != '' ){
                        $include_pdf    = true;
                        $stack          = explode( '_', $config->pdfStacks[$key] );
                        $pdf_index      = 0;
                        foreach( $stack as $pos ){
                            if( $pos == 'P' ){
                                $resource_pdf   = (array)$resource_pdfs[$pdf_index];
                                $pdf_path       = NBDESIGNER_TEMP_DIR . $resource_pdf['origin_pdf'];
                                $pdf_top        = $resource_pdf['top'] * $unitRatio + $mTop + $cdTop;
                                $pdf_left       = $resource_pdf['left'] * $unitRatio + $mLeft + $cdLeft;
                                $pdf_width      = $resource_pdf['width'] * $unitRatio;
                                $pdf_height     = $resource_pdf['height'] * $unitRatio;

                                $pdf->setSourceFile( $pdf_path );
                                $tplId = $pdf->importPage( 1 );
                                $pdf->useImportedPage( $tplId, $pdf_left, $pdf_top, $pdf_width, $pdf_height );

                                $pdf_index++;
                            }else{
                                $svg = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/part/svgpath/frame_' . $key . '_svg_part_' . $pos . '.svg';
                                nbd_convert_svg_url( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/part/', 'frame_' . $key . '_svg_part_' . $pos . '.svg' );
                                $pdf->ImageSVG( $svg, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth, $cdHeight, '', '', '', 0, true );
                            }
                        }
                    }
                }
                if( !$include_pdf ){
                    $svg = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/svgpath/frame_' . $key . '_svg'. $color_name .'.svg';
                    if( $_background) {
                        $svg = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/svgpath/frame_' . $key . '_svg_background.svg';
                    }
                    nbd_custom_convert_svg_url( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/', 'frame_' . $key . '_svg.svg' , $color , $_background);
                    $pdf->ImageSVG( $svg, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth, $cdHeight, '', '', '', 0, true );
                }
            }
            if($showBleed == 'yes' && nbdesigner_get_option( 'nbdesigner_bleed_stack', 1 ) == 2){
                $pdf->Line(0, $mTop + $bTop, $mLeft, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line(0, $mTop + $bgHeight - $bBottom, $mLeft, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($bgWidth + $mLeft, $mTop + $bTop, $bgWidth + $mLeft + $mRight, $mTop + $bTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($bgWidth + $mLeft, $mTop + $bgHeight - $bBottom, $bgWidth + $mLeft + $mRight, $mTop + $bgHeight - $bBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bLeft, 0, $mLeft + $bLeft, $mTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bLeft, $mTop + $bgHeight, $mLeft + $bLeft, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bgWidth - $bRight, 0, $mLeft + $bgWidth - $bRight, $mTop, array('color' => array(0,0,0), 'width' => 0.05));
                $pdf->Line($mLeft + $bgWidth - $bRight, $mTop + $bgHeight, $mLeft + $bgWidth - $bRight, $mTop + $bgHeight + $mBottom, array('color' => array(0,0,0), 'width' => 0.05));
            }
            if( $datas[$key]['show_overlay'] == 1 && $datas[$key]['include_overlay'] == 1 && $layout != 'c' ){
                $overlay = Nbdesigner_IO::convert_url_to_path( $datas[$key]['img_overlay'] );
                $check_over_img = Nbdesigner_IO::checkFileType(basename($overlay), array('jpg','jpeg','png'));
                if( $check_over_img ){
                    //$pdf->Image($overlay, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth,$cdHeight, '', '', '', false, '');
                    $pdf->Image($overlay, $mLeft, $mTop,  $bgWidth, $bgHeight, '', '', '', false, '');
                }
            }
            if( $watermark ){
                $watermark_type = nbdesigner_get_option( 'nbdesigner_pdf_watermark_type', 1 );
                if($watermark_type == 1){
                    $watermark_image = nbdesigner_get_option( 'nbdesigner_pdf_watermark_image', '' );
                    $watermark_file = get_attached_file( $watermark_image );
                    if( $watermark_file ){
                        $myPageWidth        = $pdf->getPageWidth();
                        $myPageHeight       = $pdf->getPageHeight();
                        list($watermark_width, $watermark_height) = getimagesize($watermark_file);
                        $watermark_width    = $watermark_width/72*25.4;
                        $watermark_height   = $watermark_height/72*25.4;
                        $myX                = ( $myPageWidth - $watermark_width )/2 > 0 ? ( $myPageWidth - $watermark_width )/2 : 0;
                        $myY                = ( $myPageHeight - $watermark_height )/2 > 0 ? ( $myPageHeight - $watermark_height )/2 : 0;
                        $pdf->SetAlpha(0.2);
                        $pdf->StartTransform();
                        $pdf->Rotate(45, $myPageWidth / 2, $myPageHeight / 2);
                        $pdf->Image($watermark_file, $myX, $myY, '', '', '', '', '', false);
                        $pdf->StopTransform();
                        $pdf->SetAlpha(1);
                    }
                }else{
                    $watermark_text     = nbdesigner_get_option('nbdesigner_pdf_watermark_text');
                    $vfont              = "helvetica";
                    $vfontsize          = 20;
                    $vfontbold          = "B";

                    $widthtext = $pdf->GetStringWidth(trim($watermark_text), $vfont, $vfontbold, $vfontsize, false );
                    $widthtextcenter    = round(($widthtext * sin(deg2rad(45))) / 2 ,0);
                    $myPageWidth        = $pdf->getPageWidth();
                    $myPageHeight       = $pdf->getPageHeight();
                    $myX                = ( $myPageWidth - $widthtext )/2 > 0 ? ( $myPageWidth - $widthtext )/2 : 0;
                    $myY                = $myPageHeight / 2;
                    $pdf->SetAlpha(0.2);
                    $pdf->StartTransform();
                    $pdf->Rotate(45, $myPageWidth / 2, $myPageHeight / 2);
                    $pdf->SetFont($vfont, $vfontbold, $vfontsize);
                    $pdf->Text($myX, $myY ,trim($watermark_text));
                    $pdf->StopTransform();
                    $pdf->SetAlpha(1);
                }
            }
            if( isset( $config->contour ) ){
                $pdf->ImageSVG( '@' . $config->contour, $mLeft + $cdLeft, $mTop + $cdTop, $cdWidth, $cdHeight, '', '', '', 0, true );
            }
            do_action( 'after_nbd_pdf', $pdf, $_pdf, $nbd_item_key, $extra );
            if( !$force ){
                $output_file = $folder .'/'. $nbd_item_key .'_'.$key.$color_name.'.pdf';
                if($_background) {
                    $output_file = $folder .'/'. $nbd_item_key .'_'.$key.'_background.pdf';
                }
                $pdf->Output($output_file, 'F');
                $result[] = $output_file;
            }
        }
        if( $force ){
            $pdf->Output( $output_file, 'F' );
            $result[] = $output_file;
        }
    }

    return $result;
}
// custom charmingprint : convert SVG to color
function nbd_custom_convert_svg_url( $path, $file , $color = '' , $background = false ){
    $color_name = $color ? '_'.str_replace( '#' , '' ,$color) : '';
    $file_new = str_replace( '.' , $color_name.'.' , $file);
    if($background) {
        $file_new = str_replace( '.' , '_background.' , $file);
    }
    $svg_path = $path . '/svgpath';
    if( !file_exists( $svg_path ) ) wp_mkdir_p( $svg_path );
    $new_svg_path = $svg_path . '/' . $file_new;
    $xdoc = new DomDocument;
    $xdoc->Load( $path.$file );
    if( nbdesigner_get_option( 'nbdesigner_fix_lost_pdf_image', 'no' ) == 'yes' ){
        /* image path */
        $images = $xdoc->getElementsByTagName( 'image' );
        for ( $i = 0; $i < $images->length; $i++ ) {
            $tagName        = $xdoc->getElementsByTagName( 'image' )->item( $i );
            $attribNode     = $tagName->getAttributeNode( 'xlink:href' );
            $img_src        = $attribNode->value;
            if( strpos( $img_src, "data:image" ) !== FALSE )
            continue;
            if( strpos( $img_src, "data:img" ) !== FALSE )
            continue;
            $type           = strtolower( pathinfo( $img_src, PATHINFO_EXTENSION ) );
            $type           = ( $type =='svg' ) ? 'svg+xml' : $type;

            $path_image     = Nbdesigner_IO::convert_url_to_path( $img_src );
            //$tagName->setAttribute( 'xlink:href', $path_image );

            $data   = nbd_file_get_contents( $path_image );
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode( $data );
            $tagName->setAttribute( 'xlink:href', $base64 );
        }
    }

    // custom charmingprint get color & remove element if different the color
    $list_remove = array();
    $_paths  = $xdoc->getElementsByTagName('path');
    $_rects  = $xdoc->getElementsByTagName('rect');
    $_texts  = $xdoc->getElementsByTagName('text');
    $_imgs   = $xdoc->getElementsByTagName('image');
    $gs      = $xdoc->getElementsByTagName('g');
    $color_select = 0;
    // foreach( $_paths as $_path ) {
    //     $fill_hex = '';
    //     $attr_style     =  $_path->getAttribute('style');
    //     $fill_hex = nbd_get_color_by_style_attr($attr_style);
    //     if( $background ) {
    //         $list_remove[] = $_path;
    //     } else {
    //         if($fill_hex != $color) {
    //             $list_remove[] = $_path;
    //         } else {
    //             $color_select++;
    //         }
    //     }
    // }
    foreach( $_rects as $_rect ) {
        $fill_hex = '';
        $attr_style     =  $_rect->getAttribute('style');
        $fill_hex = nbd_get_color_by_style_attr($attr_style);
        if( $background ) {
            $list_remove[] = $_rect;
        } else {
            if($fill_hex != $color) {
                $list_remove[] = $_rect;
            } else {
                $color_select++;
            }
        }
    }
    // foreach( $_texts as $_text ) {
    //     $fill_hex = '';
    //     $attr_style     =  $_text->getAttribute('style');
    //     $fill_hex = nbd_get_color_by_style_attr($attr_style);
    //     if( $background ) {
    //         $list_remove[] = $_text;
    //     } else {
    //         if($fill_hex != $color) {
    //             $list_remove[] = $_text;
    //         } else {
    //             $color_select++;
    //         }
    //     }
    // }
    foreach($gs as $node) {  
        if( $node->childNodes->item(1) && $node->childNodes->item(1)->nodeName != 'g' ) {
            $fill_hex = '';
            $attr_style     =  $node->childNodes->item(1)->getAttribute('style');
            $fill_hex = nbd_get_color_by_style_attr($attr_style);
            if( $background ) {
                $list_remove[] = $node;
            } else {
                if($fill_hex != $color) {
                    $list_remove[] = $node;
                } else {
                    $color_select++;
                }
            }
        }
    }
    if( !$background && $color_select == 0 ) return;
    foreach( $_imgs as $_img ) {
        if( !$background ) {
            $list_remove[] = $_img;
        }
    }
    if(count($list_remove) > 0) {
        foreach($list_remove as $g ) {
            $_parentGroup   = $g->parentNode;
            $_parentGroup->removeChild( $g );
        }  
    }

    //process clipPath 
    $defsEl  = $xdoc->getElementsByTagName('defs')->item(0);
    $cpEls   = $xdoc->getElementsByTagName('clipPath');

    $has_flag = false;
    foreach ($cpEls as $cpEl) {
        $grandParentGroup   = $cpEl->parentNode->parentNode;
        if( $grandParentGroup->hasAttribute('flag-clip') ) {
            $has_flag = true;
            break;
        }
    }

    foreach ($cpEls as $cpEl) {
        $cloneCp            = $cpEl;
        $parentGroup        = $cpEl->parentNode;
        $grandParentGroup   = $cpEl->parentNode->parentNode;
        
        if( $parentGroup->tagName == 'defs' ){
            continue;
        }

        if( strpos( $cloneCp->getAttribute('id'), 'imageCrop' ) !== FALSE ){
            continue;
        }

        if( $cloneCp->childNodes->item(1)->nodeName != 'path' ){
            continue;
        }

        if( $has_flag && !$grandParentGroup->hasAttribute('flag-clip') ){
            continue;
        }

        $cpTm       = $cloneCp->childNodes->item(1)->getAttribute('transform');
        $parentGroup->removeChild( $cpEl );
        $defsEl->appendChild( $cloneCp );
        $tm         = $parentGroup->getAttribute('transform');
        $cpMtArr    = nbd_get_transform_arr( $cpTm, 'matrix' );
        $cpTrArr    = nbd_get_transform_arr( $cpTm, 'translate' );

        $sx         = 1 / $cpMtArr[0];https://cloodo.com/login#
        $sy         = 1 / $cpMtArr[3];
        $tx         = - $cpMtArr[4] - $cpTrArr[0] * $cpMtArr[0];
        $ty         = - $cpMtArr[5] - $cpTrArr[1] * $cpMtArr[3];
        $newTm      = 'scale( ' . $sx . ', ' . $sy . ' ) translate(' . $tx . ', ' . $ty . ') ' . $tm;
        $parentGroup->setAttribute( 'transform', $newTm );
    }
    $new_svg = $xdoc->saveXML();
    file_put_contents( $new_svg_path, $new_svg );
}
// custom charmingprint : get color by style
function nbd_get_color_by_style_attr($attr_style) {
    $fill_hex = '';
    $attr_style_a   = explode(';' , $attr_style);
    if( is_array($attr_style_a) ) {
        foreach( $attr_style_a as $attr ) {
            if( explode(': ', $attr)[0] == 'fill' || explode(': ', $attr)[0] == ' fill' ) {
                $fill_hex = rgb_to_hex_code(explode(': ', $attr)[1]);
            }
        }
    }
    return $fill_hex;
}

function nbd_cloud_export_pdfs( $nbd_item_key, $watermark = true, $force = false, $showBleed = 'no', $extra = null, $need_pw = false ){
    require_once( NBDESIGNER_PLUGIN_DIR.'includes/class-output.php' );
    Nbdesigner_Output::export_pdfs( $nbd_item_key, $watermark, $force, $showBleed, $extra, $need_pw );
}
// custom charimingprint; overide function above
function nbd_custom_cloud_export_pdfs( $nbd_item_key, $watermark = true, $force = false, $showBleed = 'no', $extra = null, $need_pw = false , $color = '' , $background = false){
    require_once( NBDESIGNER_PLUGIN_DIR.'includes/class-output.php' );
    Nbdesigner_Output::custom_export_pdfs( $nbd_item_key, $watermark, $force, $showBleed, $extra, $need_pw , $color , $background );
}

function nbd_convert_files( $nbd_item, $type = 'jpg', $dpi = 300 ){
    $path = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item;
    $path_jpg = $path . '/jpg/';
    if( $type == 'jpg'){
        $path_png = $path . '/png/';
        if( !file_exists($path_png) ){
            Nbdesigner_IO::mkdir($path_png);
        }else{
            Nbdesigner_IO::delete_folder($path_png);
            Nbdesigner_IO::mkdir($path_png);
        }
        if( !file_exists($path_jpg) ){
            Nbdesigner_IO::mkdir($path_jpg);
        }else{
            Nbdesigner_IO::delete_folder($path_jpg);
            Nbdesigner_IO::mkdir($path_jpg);  
        }     
        $list =  Nbdesigner_IO::get_list_images($path, 1);
        foreach ($list as $image){
            $image_name = pathinfo($image, PATHINFO_FILENAME);
            $png_with_white_bg = $path_png . $image_name .'.png';
            $jpg = $path_jpg . $image_name .'.jpg';
            NBD_Image::imagick_add_white_bg($image, $png_with_white_bg);
            NBD_Image::imagick_convert_png2jpg_without_bg($png_with_white_bg, $jpg);
            NBD_Image::imagick_resample($jpg, $jpg, $dpi);
        }
    }else if( $type == 'cmyk'){
        $path_cmyk = $path . '/cmyk/';
        if( !file_exists($path_cmyk) ){
            Nbdesigner_IO::mkdir($path_cmyk);
        }else{
            Nbdesigner_IO::delete_folder($path_cmyk);
            Nbdesigner_IO::mkdir($path_cmyk);
        }
        $icc_index  = $_POST['icc'];
        $list_icc   = nbd_get_icc_cmyk_list_file();
        $icc        = $list_icc[$icc_index];
        $icc_file   = NBDESIGNER_PLUGIN_DIR . 'data/icc/CMYK/' . $icc;
        $list       = Nbdesigner_IO::get_list_images($path_jpg, 1);
        foreach ($list as $image){
            $image_name = pathinfo($image, PATHINFO_FILENAME);
            $cmyk       = $path_cmyk . $image_name .'.jpg';
            NBD_Image::imagick_convert_rgb_to_cymk($image, $cmyk);
            if($icc_index){
                NBD_Image::imagick_change_icc_profile($cmyk, $cmyk, $icc_file);
            }
        }
    }
}
function nbd_add_to_cart( $product_id, $variation_id, $quantity ){
    if( $variation_id > 0) {
        $adding_to_cart = wc_get_product( $product_id );
        $variation_data = wc_get_product_variation_attributes( $variation_id );
        $attributes     = $adding_to_cart->get_attributes();    
        foreach ( $attributes as $attribute ) {
            if ( ! $attribute['is_variation'] ) {
                continue;
            }    
            $taxonomy = 'attribute_' . sanitize_title( $attribute['name'] );
            $valid_value = isset( $variation_data[ $taxonomy ] ) ? $variation_data[ $taxonomy ] : '';
            $variations[ $taxonomy ] = $valid_value;
        }
        $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id,$quantity, $variation_id, $variations );
        if ( $passed_validation ) {
            $added = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variations );
        }
    }else{
        $added = WC()->cart->add_to_cart( $product_id, $quantity );
    }
    return $added;
}
function nbd_download_product_designs( $order_id, $order_item_id, $nbd_item_key, $nbu_item_key, $type, $force = false, $showBleed = 'no' ){
    $option_name = array();
    $zip_files   = array();
    $files       = array();
    if( $type != 'files' && !empty($nbd_item_key) ){
        $product_file = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key .'/product.json';
        if ( file_exists($product_file) ) {
            $product_data = @unserialize(file_get_contents($product_file));
            if ( !empty($product_data) && is_array($product_data) ) {
                foreach ($product_data as $key => $side){
                    $side_name = isset($side['orientation_name']) ? $side['orientation_name'] : '';
                    $option_name['frame_'.$key] = ($key + 1) .'_'. $side_name;
                }
            } else {
                $product_data = array();
            }
        } else {
            $product_data = array();
        }
    } else {
        $product_data = array();
    }
    $enable_watermark = nbdesigner_get_option('nbdesigner_enable_pdf_watermark');
    $watermark = $enable_watermark == 'yes' ? true : false;
    if( $enable_watermark == 'before' && !empty($order_id) ){
        $order = wc_get_order($order_id);
        if( $order && method_exists($order, 'get_status') && $order->get_status() != 'completed' ) $watermark = true;
    }
    $path = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key;
    $path_in = $path . '/preview';
    $path_in_new = $path . '/preview2/thumb';
    if (file_exists($path_in_new)) {
        $path_in = $path_in_new;
    }
    $path_out = $path . '/preview/minh';
    $config_file = $path . '/config.json';
    $config = file_exists($config_file) ? nbd_get_data_from_json( $config_file ) : null;
    // custom DPI Download FE
    $dpi = 0;
    if (!isset($config->dpi_fe)) {
        $option_file = $path . '/option.json';
        if ( file_exists($option_file) ) {
            $option = @unserialize(file_get_contents($option_file));
            if( is_array($option) && isset( $option['dpi_fe'] ) ) {
                $dpi = $option['dpi_fe'];
            }
        }
    } else {
        $dpi = $config->dpi_fe;
    }
    if( $dpi == 0 ) {
        $dpi = nbdesigner_get_option( 'nbdesigner_default_dpi_fe', 96 );
    }

    $pdf_path = $path . '/customer-pdfs' . ( $watermark ? '-watermark' : '' );
    switch ($type) {
        case 'jpg':
        case 'jpg_cmyk':
            $path_jpg = $path . '/jpg';
            if (!file_exists($path_jpg)) {
                nbd_convert_files($nbd_item_key, 'jpg', $dpi);
            }
            nbd_create_preview_design3($nbd_item_key, $path_in, $path_out . '/jpg', 'jpg');
            // $files = Nbdesigner_IO::get_list_images($path_jpg . '/minh', 1);
            $files = Nbdesigner_IO::get_list_images($path_out . '/jpg', 1);
            if (empty($files) && file_exists($path_jpg)) {
                $files = Nbdesigner_IO::get_list_images($path_jpg, 1);
            }
            if (empty($files) && file_exists($path . '/preview')) {
                $files = Nbdesigner_IO::get_list_images($path . '/preview', 1);
            }
            break;

        case 'svg':
            $path = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key;
            $svg_path = $path . '/svg';
            $svg_path_fe = $path . '/svg_fe/';
            if( !file_exists($svg_path_fe) ){
                Nbdesigner_IO::mkdir($svg_path_fe);
            }else{
                Nbdesigner_IO::delete_folder($svg_path_fe);
                Nbdesigner_IO::mkdir($svg_path_fe);
            }
            if (!file_exists($svg_path)) {
                nbd_convert_svg_embed($path);
            }
            $files = Nbdesigner_IO::get_list_files_by_type($svg_path, 1, 'svg');
            if ( !empty($product_data) && is_array($product_data) ) {
                foreach ($product_data as $key => $value) {
                    $file_name = 'frame_' . $key . '_svg.svg';
                    $file_name_convert = 'frame_cv_' . $key . '_svg.svg';
                    if (!file_exists($svg_path . '/' . $file_name)) {
                        continue;
                    }
                    $contentFile = file_get_contents($svg_path . '/' . $file_name);
                    $urlBg = isset($value['img_src']) ? wp_get_attachment_url($value['img_src']) : '';
                    $img = "<image href='$urlBg' width='100%' />";
                    if( isset($value['img_src_height'], $value['area_design_height'], $value['img_src_width'], $value['area_design_width']) &&
                        $value['img_src_height'] == $value['area_design_height'] && $value['img_src_width'] == $value['area_design_width'] ) {
                        $insertImage = str_replace("</defs>", "</defs> $img", $contentFile);
                        file_put_contents( $svg_path_fe . $file_name_convert , $insertImage );
                    } else {
                        $xdoc = new DomDocument;
                        $xdoc->Load($svg_path . '/' . $file_name);
                        $svg_items = $xdoc->getElementsByTagName('svg');
                        if ($svg_items->length > 0) {
                            $svg = $svg_items->item(0);
                            if (isset($value['area_design_width'])) $svg->setAttribute('width', $value['area_design_width']);
                            if (isset($value['area_design_height'])) $svg->setAttribute('height', $value['area_design_height']);
                            $left = (isset($value['area_design_left']) ? $value['area_design_left'] : 0) - (isset($value['img_src_left']) ? $value['img_src_left'] : 0);
                            $top = (isset($value['area_design_top']) ? $value['area_design_top'] : 0) - (isset($value['img_src_top']) ? $value['img_src_top'] : 0);
                            $svg->setAttribute('x', $left);
                            $svg->setAttribute('y', $top);
                            $svg->removeAttribute('xmlns');
                            $svg->removeAttribute('xmlns:xlink');
                            $svg->removeAttribute('xml:space');
                            $svg->removeAttribute('version');
                            $convert_svg = $xdoc->saveXML();
                            $convert_svg = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>' , '' , $convert_svg);
                            $convert_svg = str_replace('<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">' , '' , $convert_svg);
                            $w = isset($value['img_src_width']) ? $value['img_src_width'] : '';
                            $h = isset($value['img_src_height']) ? $value['img_src_height'] : '';
                            $svg1 = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="'.$w.'" height="'.$h.'" viewBox="0 0 '.$w.' '.$h.'" xml:space="preserve">';
                            $svg1 .= "<image href='$urlBg' width='100%'/>";
                            $new_svg = $svg1.$convert_svg.'</svg>';
                            file_put_contents( $svg_path_fe . $file_name_convert , $new_svg);  
                        }
                    }
                    if (file_exists($svg_path_fe . $file_name_convert)) {
                        $files[$key] = $svg_path_fe . $file_name_convert;
                    }
                }
            }
            break;

        case 'pdf':
            $enable_watermark = nbdesigner_get_option('nbdesigner_enable_pdf_watermark');
            $watermark = $enable_watermark == 'yes' ? true : false;
            if ($enable_watermark == 'before' && !empty($order_id)) {
                $order = wc_get_order($order_id);
                if ($order && method_exists($order, 'get_status') && $order->get_status() != 'completed') {
                    $watermark = true;
                }
            }
            $files = nbd_export_pdfs($nbd_item_key, $watermark, $force, $showBleed, 1);
            if (empty($files) || !is_array($files)) {
                $pdf_folder = $path . '/customer-pdfs' . ($watermark ? '-watermark' : '') . ($force ? '-force' : '') . ($showBleed == 'yes' ? '-bleed' : '');
                if (file_exists($pdf_folder)) {
                    $files = Nbdesigner_IO::get_list_files_by_type($pdf_folder, 1, 'pdf');
                }
                if (empty($files) && file_exists($path . '/customer-pdfs')) {
                    $files = Nbdesigner_IO::get_list_files_by_type($path . '/customer-pdfs', 1, 'pdf');
                }
            }
            break;

        case 'files':
            if ($nbu_item_key != '') {
                $files = Nbdesigner_IO::get_list_files(NBDESIGNER_UPLOAD_DIR . '/' . $nbu_item_key);
            }
            break;

        default:
            if ($nbd_item_key != '') {
                $files = array();
                if (!isset($_GET['type'])) {
                    $_GET['type'] = 'png';
                }
                nbd_export_pdfs($nbd_item_key, false, false, 'no');

                $search_dirs = array(
                    $path . '/customer-png',
                    $path . '/customer-pdfs',
                    $path . '/customer-pdfs-watermark',
                    $path
                );
                $list_pdf = array();
                $found_pdf_dir = '';
                foreach ($search_dirs as $sdir) {
                    if (file_exists($sdir)) {
                        $pdfs = Nbdesigner_IO::get_list_files_by_type($sdir, 1, 'pdf');
                        if (!empty($pdfs) && is_array($pdfs)) {
                            $list_pdf = $pdfs;
                            $found_pdf_dir = $sdir;
                            break;
                        }
                    }
                }

                if (!empty($list_pdf)) {
                    foreach ($list_pdf as $key => $pdf) {
                        try {
                            NBD_Image::pdf2image($pdf, $dpi, 'png');
                        } catch (\Throwable $e) {
                            error_log('pdf2image failed: ' . $e->getMessage());
                        }
                    }
                    if ($found_pdf_dir != '') {
                        $files = Nbdesigner_IO::get_list_files_by_type($found_pdf_dir, 1, 'png');
                    }
                }

                if (empty($files)) {
                    $files = Nbdesigner_IO::get_list_files_by_type($path . '/customer-png', 1, 'png');
                }
                if (empty($files)) {
                    $files = Nbdesigner_IO::get_list_files_by_type($path . '/customer-pdfs', 1, 'png');
                }
                if (empty($files)) {
                    $files = Nbdesigner_IO::get_list_files_by_type($path, 1, 'png');
                }
                // Fallbacks to design preview images
                if (empty($files) && file_exists($path_in)) {
                    $files = Nbdesigner_IO::get_list_files_by_type($path_in, 1, 'png');
                }
                if (empty($files) && file_exists($path . '/preview')) {
                    $files = Nbdesigner_IO::get_list_files_by_type($path . '/preview', 1, 'png');
                }
                if (empty($files) && file_exists($path . '/preview2/thumb')) {
                    $files = Nbdesigner_IO::get_list_files_by_type($path . '/preview2/thumb', 1, 'png');
                }
                // If still empty but PDFs were generated, provide the PDF files
                if (empty($files) && !empty($list_pdf)) {
                    $files = $list_pdf;
                }
            }
            break;
    }

    if (!empty($files) && is_array($files)) {
        foreach ($files as $key => $file) {
            $zip_files[] = $file;
        }
        $pathZip = NBDESIGNER_DATA_DIR . '/download/customer-design-' . $order_id . '-' . $order_item_id . '.zip';
        nbd_zip_files_and_download($zip_files, $pathZip, 'customer-design.zip', $option_name);
        exit();
    } else {
        $message = ' <a href="' . esc_url(wc_get_page_permalink('shop')) . '" class="wc-forward">' . esc_html__('Go to shop', 'woocommerce') . '</a>';
        wp_die($message, __('No file defined', 'web-to-print-online-designer'), array('response' => 404));
    }
}
function nbd_create_preview_design($nbd_item_key, $path_in, $path_out, $type) {
    if (!file_exists($path_out)) {
        wp_mkdir_p($path_out);
    }
    $files = Nbdesigner_IO::get_list_images($path_in, 1);
    if (!empty($files) && is_array($files)) {
        foreach ($files as $key => $file) {
            $image_name = pathinfo($file, PATHINFO_FILENAME);
            $png = $path_in . '/' . $image_name . '.' . $type;
            if ( !file_exists($png) ) continue;
            $size = @getimagesize($png);
            $width = absint(nbdesigner_get_option('nbdesigner_thumbnail_width')) ? absint(nbdesigner_get_option('nbdesigner_thumbnail_width')) : 300;
            if (is_array($size) && count($size) >= 2) {
                $w = $size[0];
                $h = $size[1];
                if ($w != $h && $size[0] > 0) {
                    if ($w > $h) {
                        $w = $width;
                        $h = (int) round($w / $size[0] * $size[1]);
                    } else {
                        $h = $width;
                        $w = (int) round($h * $size[0] / $size[1]);
                    }
                }
                if ($type == 'jpg') {
                    NBD_Image::nbdesigner_resize_imagejpg($png, (int) round($w), (int) round($h), $path_out . '/' . $image_name . '.' . $type);
                } else {
                    NBD_Image::nbdesigner_resize_imagepng($png, (int) round($w), (int) round($h), $path_out . '/' . $image_name . '.' . $type);
                }
                NBD_Image::imagick_resample($path_out . '/' . $image_name . '.' . $type, $path_out . '/' . $image_name . '.' . $type, 72);
            }
        }
    }
}
function nbd_create_preview_design2($nbd_item_key, $path_in, $path_out, $type) {
    $path = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key;
    if (is_available_imagick()) {
        $config = nbd_get_data_from_json($path . '/config.json');
        nbd_export_pdfs($nbd_item_key, false, false, 'no');
        $pdf_path = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/customer-pdfs/minh';
        $list_pdf = Nbdesigner_IO::get_list_files_by_type($pdf_path, 1, 'pdf');
        $dpi = 300;
        if (!isset($config->dpi)) {
            $opt_file = $path . '/option.json';
            if (file_exists($opt_file)) {
                $option = @unserialize(file_get_contents($opt_file));
                if (is_array($option) && isset($option['dpi'])) {
                    $dpi = $option['dpi'];
                }
            }
        } else {
            $dpi = $config->dpi;
        }
        if (!empty($list_pdf) && is_array($list_pdf)) {
            foreach ($list_pdf as $key => $pdf) {
                try {
                    NBD_Image::pdf2image($pdf, $dpi, $type);
                } catch (\Throwable $e) {
                    error_log('pdf2image failed: ' . $e->getMessage());
                }
            }
        }
        $size = array();
        $list_png = Nbdesigner_IO::get_list_files_by_type($pdf_path, 1, 'png');
        if (!empty($list_png) && is_array($list_png)) {
            foreach ($list_png as $key => $png) {
                if (file_exists($png)) {
                    $sz = @getimagesize($png);
                    if (is_array($sz)) {
                        $size = $sz;
                    }
                }
            }
        }
        if (is_array($size) && count($size) >= 2) {
            $w = $size[0];
            $h = $size[1];
            if (!file_exists($path_out)) {
                wp_mkdir_p($path_out);
            }
            $files = Nbdesigner_IO::get_list_images($path_in, 1);
            if (!empty($files) && is_array($files)) {
                foreach ($files as $key => $file) {
                    $image_name = pathinfo($file, PATHINFO_FILENAME);
                    $png = $path_in . '/' . $image_name . '.png';
                    $jpg = $path_in . '/' . $image_name . '.jpg';
                    if ($type == 'jpg') {
                        NBD_Image::imagick_convert_png_to_jpg($png, $jpg);
                        NBD_Image::nbdesigner_resize_imagejpg($jpg, (int) round($w), (int) round($h), $path_out . '/' . $image_name . '.' . $type);
                    } else {
                        NBD_Image::nbdesigner_resize_imagepng($png, (int) round($w), (int) round($h), $path_out . '/' . $image_name . '.' . $type);
                    }
                    NBD_Image::imagick_resample($path_out . '/' . $image_name . '.' . $type, $path_out . '/' . $image_name . '.' . $type, 72);
                }
            }
        }
    }
}
function nbd_create_preview_design3($nbd_item_key, $path_in, $path_out, $type) {
    if (!file_exists($path_out)) {
        wp_mkdir_p($path_out);
    }
    $path = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key;
    $cm2Px = 0.03937007*120*10;
    $prod_file = $path .'/product.json';
    if (!file_exists($prod_file)) {
        return;
    }
    $datas = @unserialize(file_get_contents($prod_file));
    if (empty($datas) || !is_array($datas)) {
        return;
    }
    $files = Nbdesigner_IO::get_list_files_by_type($path_in, 1, 'png');
    if (!empty($files) && is_array($files) && count($files) == count($datas)) {
        $d = 0;
        foreach ($files as $key => $file) {
            if(isset($datas[$d]['product_width']) && isset($datas[$d]['product_height'])) {
                $w = (int) round($datas[$d]['product_width']*$cm2Px);
                $h = (int) round($datas[$d]['product_height']*$cm2Px);
                $image_name = pathinfo($file, PATHINFO_FILENAME);
                $png = $path_in . '/' . $image_name . '.png';
                $jpg = $path_in . '/' . $image_name . '.jpg';
                if ($type == 'jpg') {
                    NBD_Image::imagick_convert_png_to_jpg($png, $jpg);
                    NBD_Image::nbdesigner_resize_imagejpg($jpg, $w, $h, $path_out . '/' . $image_name . '.' . $type);
                } else {
                    NBD_Image::nbdesigner_resize_imagepng($png, $w, $h, $path_out . '/' . $image_name . '.' . $type);
                }
                NBD_Image::imagick_resample($path_out . '/' . $image_name . '.' . $type, $path_out . '/' . $image_name . '.' . $type, 120);
            }
            $d++;
        }
    }
}
function nbd_download_product_designs_without_orderid($nbd_item_key, $nbu_item_key, $type, $force = false, $showBleed = 'no') {
    $option_name = array();
    $zip_files   = array();
    $files       = array();
    $path = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key;
    $path_in = $path . '/preview';
    $path_in_new = $path . '/preview2/thumb';
    if (file_exists($path_in_new)) {
        $path_in = $path_in_new;
    }
    $path_out = $path . '/preview/minh';
    if ($type != 'files' && !empty($nbd_item_key)) {
        $product_file = $path . '/product.json';
        if (file_exists($product_file)) {
            $product_data = @unserialize(file_get_contents($product_file));
            if (!empty($product_data) && is_array($product_data)) {
                foreach ($product_data as $key => $side) {
                    $side_name = isset($side['orientation_name']) ? $side['orientation_name'] : '';
                    $option_name['frame_' . $key] = ($key + 1) . '_' . $side_name;
                }
            } else {
                $product_data = array();
            }
        } else {
            $product_data = array();
        }
    } else {
        $product_data = array();
    }
    $config_file = $path . '/config.json';
    $config = file_exists($config_file) ? nbd_get_data_from_json($config_file) : null;
    $dpi = 0;
    if (!isset($config->dpi_fe)) {
        $option_file = $path . '/option.json';
        if (file_exists($option_file)) {
            $option = @unserialize(file_get_contents($option_file));
            if (is_array($option) && isset($option['dpi_fe'])) {
                $dpi = $option['dpi_fe'];
            }
        }
    } else {
        $dpi = $config->dpi_fe;
    }
    if ($dpi == 0) {
        $dpi = nbdesigner_get_option('nbdesigner_default_dpi_fe', 96);
    }

    switch ($type) {
        case 'jpg':
        case 'jpg_cmyk':
            if (!isset($_GET['type'])) {
                $_GET['type'] = 'jpg';
            }
            nbd_export_pdfs($nbd_item_key, false, false, 'no');
            $search_dirs = array($path . '/customer-jpg', $path . '/customer-pdfs', $path);
            $list_pdf = array();
            $found_pdf_dir = '';
            foreach ($search_dirs as $sdir) {
                if (file_exists($sdir)) {
                    $pdfs = Nbdesigner_IO::get_list_files_by_type($sdir, 1, 'pdf');
                    if (!empty($pdfs) && is_array($pdfs)) {
                        $list_pdf = $pdfs;
                        $found_pdf_dir = $sdir;
                        break;
                    }
                }
            }

            if (!empty($list_pdf)) {
                foreach ($list_pdf as $key => $pdf) {
                    try {
                        NBD_Image::pdf2image($pdf, $dpi, 'jpg');
                    } catch (\Throwable $e) {
                        error_log('pdf2image failed: ' . $e->getMessage());
                    }
                }
                if ($found_pdf_dir != '') {
                    $files = Nbdesigner_IO::get_list_files_by_type($found_pdf_dir, 1, 'jpg');
                }
            }
            if (empty($files)) {
                $files = Nbdesigner_IO::get_list_files_by_type($path . '/customer-jpg', 1, 'jpg');
            }
            if (empty($files)) {
                $files = Nbdesigner_IO::get_list_files_by_type($path . '/customer-pdfs', 1, 'jpg');
            }
            if (empty($files) && file_exists($path . '/preview')) {
                $files = Nbdesigner_IO::get_list_images($path . '/preview', 1);
            }
            if (empty($files) && !empty($list_pdf)) {
                $files = $list_pdf;
            }
            break;

        case 'svg':
            $svg_path = $path . '/svg';
            $svg_path_fe = $path . '/svg_fe/';
            if (!file_exists($svg_path_fe)) {
                Nbdesigner_IO::mkdir($svg_path_fe);
            } else {
                Nbdesigner_IO::delete_folder($svg_path_fe);
                Nbdesigner_IO::mkdir($svg_path_fe);
            }
            if (!file_exists($svg_path)) {
                nbd_convert_svg_embed($path);
            }
            $files = Nbdesigner_IO::get_list_files_by_type($svg_path, 1, 'svg');
            if (!empty($product_data) && is_array($product_data)) {
                foreach ($product_data as $key => $value) {
                    $file_name = 'frame_' . $key . '_svg.svg';
                    $file_name_convert = 'frame_cv_' . $key . '_svg.svg';
                    if (!file_exists($svg_path . '/' . $file_name)) {
                        continue;
                    }
                    $contentFile = file_get_contents($svg_path . '/' . $file_name);
                    $urlBg = isset($value['img_src']) ? wp_get_attachment_url($value['img_src']) : '';
                    $img = "<image href='$urlBg' width='100%' />";
                    if (isset($value['img_src_height'], $value['area_design_height'], $value['img_src_width'], $value['area_design_width']) &&
                        $value['img_src_height'] == $value['area_design_height'] && $value['img_src_width'] == $value['area_design_width']) {
                        $insertImage = str_replace("</defs>", "</defs> $img", $contentFile);
                        file_put_contents($svg_path_fe . $file_name_convert, $insertImage);
                    } else {
                        $xdoc = new DomDocument;
                        $xdoc->Load($svg_path . '/' . $file_name);
                        $svg_items = $xdoc->getElementsByTagName('svg');
                        if ($svg_items->length > 0) {
                            $svg = $svg_items->item(0);
                            if (isset($value['area_design_width'])) $svg->setAttribute('width', $value['area_design_width']);
                            if (isset($value['area_design_height'])) $svg->setAttribute('height', $value['area_design_height']);
                            $left = (isset($value['area_design_left']) ? $value['area_design_left'] : 0) - (isset($value['img_src_left']) ? $value['img_src_left'] : 0);
                            $top = (isset($value['area_design_top']) ? $value['area_design_top'] : 0) - (isset($value['img_src_top']) ? $value['img_src_top'] : 0);
                            $svg->setAttribute('x', $left);
                            $svg->setAttribute('y', $top);
                            $svg->removeAttribute('xmlns');
                            $svg->removeAttribute('xmlns:xlink');
                            $svg->removeAttribute('xml:space');
                            $svg->removeAttribute('version');
                            $convert_svg = $xdoc->saveXML();
                            $convert_svg = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>', '', $convert_svg);
                            $convert_svg = str_replace('<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">', '', $convert_svg);
                            $w = isset($value['img_src_width']) ? $value['img_src_width'] : '';
                            $h = isset($value['img_src_height']) ? $value['img_src_height'] : '';
                            $svg1 = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="' . $w . '" height="' . $h . '" viewBox="0 0 ' . $w . ' ' . $h . '" xml:space="preserve">';
                            $svg1 .= "<image href='$urlBg' width='100%'/>";
                            $new_svg = $svg1 . $convert_svg . '</svg>';
                            file_put_contents($svg_path_fe . $file_name_convert, $new_svg);
                        }
                    }
                    if (file_exists($svg_path_fe . $file_name_convert)) {
                        $files[$key] = $svg_path_fe . $file_name_convert;
                    }
                }
            }
            break;

        case 'pdf':
            $enable_watermark = nbdesigner_get_option('nbdesigner_enable_pdf_watermark');
            $watermark = $enable_watermark == 'yes' ? true : false;
            $files = nbd_export_pdfs($nbd_item_key, $watermark, $force, $showBleed, 1);
            if (empty($files) || !is_array($files)) {
                $pdf_folder = $path . '/customer-pdfs' . ($watermark ? '-watermark' : '') . ($force ? '-force' : '') . ($showBleed == 'yes' ? '-bleed' : '');
                if (file_exists($pdf_folder)) {
                    $files = Nbdesigner_IO::get_list_files_by_type($pdf_folder, 1, 'pdf');
                }
                if (empty($files) && file_exists($path . '/customer-pdfs')) {
                    $files = Nbdesigner_IO::get_list_files_by_type($path . '/customer-pdfs', 1, 'pdf');
                }
            }
            break;

        case 'files':
            if ($nbu_item_key != '') {
                $files = Nbdesigner_IO::get_list_files(NBDESIGNER_UPLOAD_DIR . '/' . $nbu_item_key);
            }
            break;

        default:
            if ($nbd_item_key != '') {
                $files = array();
                if (!isset($_GET['type'])) {
                    $_GET['type'] = 'png';
                }
                nbd_export_pdfs($nbd_item_key, false, false, 'no');
                $search_dirs = array($path . '/customer-png', $path . '/customer-pdfs', $path . '/customer-pdfs-watermark', $path);
                $list_pdf = array();
                $found_pdf_dir = '';
                foreach ($search_dirs as $sdir) {
                    if (file_exists($sdir)) {
                        $pdfs = Nbdesigner_IO::get_list_files_by_type($sdir, 1, 'pdf');
                        if (!empty($pdfs) && is_array($pdfs)) {
                            $list_pdf = $pdfs;
                            $found_pdf_dir = $sdir;
                            break;
                        }
                    }
                }

                if (!empty($list_pdf)) {
                    foreach ($list_pdf as $key => $pdf) {
                        try {
                            NBD_Image::pdf2image($pdf, $dpi, 'png');
                        } catch (\Throwable $e) {
                            error_log('pdf2image failed: ' . $e->getMessage());
                        }
                    }
                    if ($found_pdf_dir != '') {
                        $files = Nbdesigner_IO::get_list_files_by_type($found_pdf_dir, 1, 'png');
                    }
                }
                if (empty($files)) {
                    $files = Nbdesigner_IO::get_list_files_by_type($path . '/customer-png', 1, 'png');
                }
                if (empty($files)) {
                    $files = Nbdesigner_IO::get_list_files_by_type($path . '/customer-pdfs', 1, 'png');
                }
                if (empty($files)) {
                    $files = Nbdesigner_IO::get_list_files_by_type($path, 1, 'png');
                }
                // Fallbacks to design preview images
                if (empty($files) && file_exists($path_in)) {
                    $files = Nbdesigner_IO::get_list_files_by_type($path_in, 1, 'png');
                }
                if (empty($files) && file_exists($path . '/preview')) {
                    $files = Nbdesigner_IO::get_list_files_by_type($path . '/preview', 1, 'png');
                }
                if (empty($files) && file_exists($path . '/preview2/thumb')) {
                    $files = Nbdesigner_IO::get_list_files_by_type($path . '/preview2/thumb', 1, 'png');
                }
                if (empty($files) && !empty($list_pdf)) {
                    $files = $list_pdf;
                }
            }
            break;
    }

    if (!empty($files) && is_array($files)) {
        foreach ($files as $key => $file) {
            $zip_files[] = $file;
        }
        $pathZip = NBDESIGNER_DATA_DIR . '/download/customer-design-' . $nbd_item_key . '.zip';
        nbd_zip_files_and_download($zip_files, $pathZip, $type . '-customer-design.zip', $option_name);
        exit();
    } else {
        $message = ' <a href="' . esc_url(wc_get_page_permalink('shop')) . '" class="wc-forward">' . esc_html__('Go to shop', 'woocommerce') . '</a>';
        wp_die($message, __('No file defined', 'web-to-print-online-designer'), array('response' => 404));
    }
}
function nbd_download_google_font( $font_name = ''){
    $path_dst = array(
        'r' =>  NBDESIGNER_FONT_DIR . '/' . $font_name . '.ttf'
    );
    $google_font_path = NBDESIGNER_PLUGIN_DIR . '/data/google-fonts-ttf.json';
    if( !file_exists($google_font_path) ){
        return $path_dst;
    }
    $fonts = json_decode( file_get_contents($google_font_path) );
    $items = ( isset($fonts->items) && (is_array($fonts->items) || is_object($fonts->items)) ) ? $fonts->items : array();
    $font = null;
    foreach ( $items as $item ){
        if( isset($item->family) && $item->family == $font_name ){
            $font = isset($item->files) ? $item->files : null;
            break;
        }
    }
    if( empty($font) ){
        return $path_dst;
    }
    $font_arr = (array)$font;
    $path_src = isset($font->regular) ? $font->regular : ( !empty($font_arr) ? reset($font_arr) : '' );
    if( !empty($path_src) && !file_exists($path_dst['r']) ){
        @copy($path_src, $path_dst['r']);
    }
    if( isset($font->italic) ){
        $path_dst['i'] = NBDESIGNER_FONT_DIR . '/' . $font_name . 'i.ttf';
        if( !file_exists($path_dst['i']) ){
            @copy($font->italic, $path_dst['i']);
        }
    }
    if( isset($font->{"700"}) ){
        $path_dst['b'] = NBDESIGNER_FONT_DIR . '/' . $font_name . 'b.ttf';
        if( !file_exists($path_dst['b']) ){
            @copy($font->{"700"}, $path_dst['b']);
        }
    }    
    if( isset($font->{"700italic"}) ){
        $path_dst['bi'] = NBDESIGNER_FONT_DIR . '/' . $font_name . 'bi.ttf';
        if( !file_exists($path_dst['bi']) ){
            @copy($font->{"700italic"}, $path_dst['bi']);
        }
    }
    return $path_dst;
}
function nbd_custom_notices($command, $mes) {
    switch ($command) {
        case 'success':
            if (!isset($mes))
                $mes = esc_html__('Your settings have been saved.', 'web-to-print-online-designer');
            $notice = '<div class="updated notice notice-success is-dismissible">
                            <p>' . $mes . '</p>
                            <button type="button" class="notice-dismiss">
                                <span class="screen-reader-text">'. esc_html__('Dismiss this notice.', 'web-to-print-online-designer') .'</span>
                            </button>
                        </div>';
            break;
        case 'error':
            if (!isset($mes))
                $mes = esc_html__('Irks! An error has occurred.', 'web-to-print-online-designer');
            $notice = '<div class="notice notice-error is-dismissible">
                            <p>' . $mes . '</p>
                            <button type="button" class="notice-dismiss">
                                <span class="screen-reader-text">'. esc_html__('Dismiss this notice.', 'web-to-print-online-designer') .'</span>
                            </button>
                        </div>';
            break;
        case 'notices':
            if (!isset($mes))
                $mes = esc_html__('Irks! An error has occurred.', 'web-to-print-online-designer');
            $notice = '<div class="notice notice-warning">
                            <p>' . $mes . '</p>
                        </div>';
            break;
        case 'warning':
            if (!isset($mes))
                $mes = esc_html__('Warning.', 'web-to-print-online-designer');
            $notice = '<div class="notice notice-warning is-dismissible">
                            <p>' . $mes . '</p>
                            <button type="button" class="notice-dismiss">
                                <span class="screen-reader-text">'. esc_html__('Dismiss this notice.', 'web-to-print-online-designer') .'</span>
                            </button>
                        </div>';
            break;
        default:
            $notice = '';
    }
    return $notice;
}
function nbd_font_subsets(){
    return array(
        'all'   =>  array(
            'name'  =>  'All language',
            'preview_text'  =>  'Abc Xyz',
            'default_font'  =>  'Roboto'
        ),  
        'arabic'   =>  array(
            'name'  =>  'Arabic',
            'preview_text'  =>  'ءيوهن',
            'default_font'  =>  'Cairo'
        ),
        'bengali'   =>  array(
            'name'  =>  'Bengali',
            'preview_text'  =>  'অআইঈউ',
            'default_font'  =>  'Hind Siliguri'
        ),
        'cyrillic'   =>  array(
            'name'  =>  'Cyrillic',
            'preview_text'  =>  'БВГҐД',
            'default_font'  =>  'Roboto'
        ),
        'cyrillic-ext'   =>  array(
            'name'  =>  'Cyrillic Extended',
            'preview_text'  =>  'БВГҐД',
            'default_font'  =>  'Roboto'
        ),
        'chinese-simplified'   =>  array(
            'name'  =>  'Chinese (Simplified)',
            'preview_text'  =>  '一二三四五',
            'default_font'  =>  'ZCOOL XiaoWei'
        ), 
        'devanagari'   =>  array(
            'name'  =>  'Devanagari',
            'preview_text'  =>  'आईऊऋॠ',
            'default_font'  =>  'Noto Sans'
        ),
        'greek'   =>  array(
            'name'  =>  'Greek',
            'preview_text'  =>  'αβγδε',
            'default_font'  =>  'Roboto'
        ),
        'greek-ext'   =>  array(
            'name'  =>  'Greek Extended',
            'preview_text'  =>  'αβγδε',
            'default_font'  =>  'Roboto'
        ), 
        'gujarati'   =>  array(
            'name'  =>  'Gujarati',
            'preview_text'  =>  'આઇઈઉઊ',
            'default_font'  =>  'Shrikhand'
        ),
        'gurmukhi'   =>  array(
            'name'  =>  'Gurmukhi',
            'preview_text'  =>  'ਆਈਊਏਐ',
            'default_font'  =>  'Baloo Paaji'
        ),
        'hebrew'   =>  array(
            'name'  =>  'Hebrew',
            'preview_text'  =>  'אבגדה',
            'default_font'  =>  'Arimo'
        ),
        'japanese'   =>  array(
            'name'  =>  'Japanese',
            'preview_text'  =>  '一二三四五',
            'default_font'  =>  'Sawarabi Mincho'
        ),
        'kannada'   =>  array(
            'name'  =>  'Kannada',
            'preview_text'  =>  'ಅಆಇಈಉ',
            'default_font'  =>  'Baloo Tamma'
        ),   
        'khmer'   =>  array(
            'name'  =>  'Khmer',
            'preview_text'  =>  'កខគឃង',
            'default_font'  =>  'Hanuman'
        ),
        'korean'   =>  array(
            'name'  =>  'Korean',
            'preview_text'  =>  '가개갸거게',
            'default_font'  =>  'Nanum Gothic'
        ),
        'latin'   =>  array(
            'name'  =>  'Latin',
            'preview_text'  =>  'Abc Xyz',
            'default_font'  =>  'Roboto'
        ),
        'latin-ext'   =>  array(
            'name'  =>  'Latin Extended',
            'preview_text'  =>  'Abc Xyz',
            'default_font'  =>  'Roboto'
        ), 
        'malayalam'   =>  array(
            'name'  =>  'Malayalam',
            'preview_text'  =>  'അആഇഈഉ',
            'default_font'  =>  'Baloo Chettan'
        ),
        'myanmar'   =>  array(
            'name'  =>  'Myanmar',
            'preview_text'  =>  'ကခဂဃင',
            'default_font'  =>  'Padauk'
        ),
        'oriya'   =>  array(
            'name'  =>  'Oriya',
            'preview_text'  =>  'ଅଆଇଈଉ',
            'default_font'  =>  'Baloo Bhaina'
        ),
        'sinhala'   =>  array(
            'name'  =>  'Sinhala',
            'preview_text'  =>  'අආඇඈඉ',
            'default_font'  =>  'Abhaya Libre'
        ),
        'tamil'   =>  array(
            'name'  =>  'Tamil',
            'preview_text'  =>  'க்ங்ச்ஞ்ட்',
            'default_font'  =>  'Catamaran'
        ), 
        'telugu'   =>  array(
            'name'  =>  'Telugu',
            'preview_text'  =>  'అఆఇఈఉ',
            'default_font'  =>  'Gurajada'
        ),
        'thai'   =>  array(
            'name'  =>  'Thai',
            'preview_text'  =>  'กขคฆง',
            'default_font'  =>  'Kanit'
        ),
        'vietnamese'   =>  array(
            'name'  =>  'Vietnamese',
            'preview_text'  =>  'Abc Xyz',
            'default_font'  =>  'Roboto'
        )
    );
}
function _nbd_font_subsets(){
    $subsets = array();
    foreach(nbd_font_subsets() as $key => $subset){
        $subsets[$key] = $subset['name'];
    }
    return $subsets;
}
function nbd_get_default_font(){
    $default_fonts  = json_decode( file_get_contents(NBDESIGNER_PLUGIN_DIR . '/data/default-font.json'));
    $subset         = nbdesigner_get_option('nbdesigner_default_font_subset');
    $subsets        = nbd_font_subsets();
    $font           = 'Roboto';
    foreach($subsets as $key => $sub){
        if( $key == $subset ) $font = $sub['default_font'];
    }
    foreach( $default_fonts as $f ){
        if( $f->name == $font ) {
            $default_font = $f;
            break;
        }
    }

    $google_font_path   = NBDESIGNER_PLUGIN_DIR . '/data/google-fonts-ttf.json';
    $fonts              = json_decode( file_get_contents( $google_font_path ) );
    $items              = $fonts->items;
    foreach ( $items as $item ){
        if( $item->family == $font ){
            $gg_font = $item->files;
            break;
        }
    }

    $default_font->file->r = isset( $gg_font->regular ) ? $gg_font->regular : reset( $gg_font );
    if( isset( $gg_font->italic ) ){
        $default_font->file->i = $gg_font->italic;
    }else{
        unset( $default_font->file->i );
    }
    if( isset( $gg_font->{"700"} ) ){
        $default_font->file->b = $gg_font->{"700"};
    }else{
        unset( $default_font->file->b );
    }
    if( isset( $gg_font->{"700italic"} ) ){
        $default_font->file->bi = $gg_font->{"700italic"};
    }else{
        unset( $default_font->file->bi );
    }

    return json_encode( $default_font );
}
function nbd_get_fonts( $return = false ){
    $gg_fonts       = array();
    $custom_fonts   = array();
    if( file_exists( NBDESIGNER_DATA_DIR . '/googlefonts.json' ) ) {
        $_gg_fonts  = file_get_contents( NBDESIGNER_DATA_DIR . '/googlefonts.json' );
        $_gg_fonts  = $_gg_fonts != '' ? $_gg_fonts : '[]';
        $gg_fonts   = json_decode( $_gg_fonts );
    }
    if( file_exists( NBDESIGNER_DATA_DIR . '/fonts.json' ) ) {
        $_custom_fonts  = file_get_contents( NBDESIGNER_DATA_DIR . '/fonts.json' );
        $_custom_fonts  = $_custom_fonts != '' ? $_custom_fonts : '[]';
        $custom_fonts   = json_decode( $_custom_fonts );
    }
    $fonts = array_merge( $gg_fonts, $custom_fonts );
    if( $return ) return $fonts;
    echo json_encode($fonts);
}
function nbd_get_order_object() {
    global $thepostid, $theorder;
    if (!is_object($theorder)) {
        $theorder = wc_get_order($thepostid);
    }
    if (!$theorder && isset($_POST['order_id'])) {
        $order_id = absint($_POST['order_id']);
        $order = wc_get_order($order_id);
        return $order;
    } elseif (!$theorder && isset($_POST['post_ID'])) {
        $order_id = absint($_POST['post_ID']);
        $order = wc_get_order($order_id);
        return $order;
    }
    if (!$theorder) {
        global $post;
        if ($post) {
            $theorder = wc_get_order($post->ID);
        }
    }
    return $theorder;
}
function nbd_get_image_thumbnail( $id, $size = 'thumbnail' ){
    if( absint($id) != 0 ){
        $image = wp_get_attachment_image_src( $id, $size );
        if(!$image){
            $image_url = wp_get_attachment_url($id);
        }else{
            $image_url = $image[0];
        }
    }else{
        $image_url = NBDESIGNER_ASSETS_URL . 'images/placeholder.png';
    }
    return $image_url;
}
function nbd_get_truetype_fonts(){
    $true_type = array('Bruum FY', 'CitadelScriptStd', 'DIN Next LT Pro Light', 'DIN Next LT Pro Medium', 'DIN Next LT Pro Regular', 'Gudea', 'Abel', 'Abril Fatface', 'Acme', 'Advent Pro', 'Aguafina Script', 'Aladin', 'Allura', 'Almendra', 'Almendra Display', 'Almendra SC', 'Amiri', 'Antic', 'Antic Didone', 'Anonymous Pro', 'Antic Slab', 'Arbutus', 'Architects Daughter', 'Aref Ruqaa', 'Arizonia', 'Asset', 'Asul', 'Average', 'Average Sans', 'Averia Gruesa Libre', 'Averia Libre', 'Averia Sans Libre', 'Averia Serif Libre', 'Bad Script', 'Balthazar', 'Belgrano', 'Bilbo', 'Bilbo Swash', 'Boogaloo', 'Bowlby One', 'Bree Serif', 'Bubblegum Sans', 'Bubbler One', 'Buenard', 'Butcherman', 'Cagliostro', 'Cambo', 'Cantarell', 'Cardo', 'Caudex', 'Ceviche One', 'Changa One', 'Chango', 'Chau Philomene One', 'Chela One', 'Cherry Swash', 'Chicle', 'Cinzel', 'Cinzel Decorative', 'Coiny', 'Condiment', 'Contrail One', 'Convergence', 'Cookie', 'Corben', 'Covered By Your Grace', 'Creepster', 'Crete Round', 'Croissant One', 'Damion', 'Dawning of a New Day', 'Days One', 'Delius', 'Delius Swash Caps', 'Delius Unicase', 'Della Respira', 'Devonshire', 'Diplomata', 'Diplomata SC', 'Dorsa', 'Dr Sugiyama', 'Economica', 'Enriqueta', 'Erica One', 'Esteban', 'Euphoria Script', 'Ewert', 'Exo', 'Fanwood Text', 'Farsan', 'Faster One', 'Fauna One', 'Fenix', 'Felipa', 'Fjord One', 'Flamenco', 'Fredericka the Great', 'Fredoka One', 'Fresca', 'Fugaz One', 'Gafata', 'Galdeano', 'Geostar', 'Geostar Fill', 'Germania One', 'Glass Antiqua', 'Goblin One', 'Graduate', 'Gravitas One', 'Great Vibes', 'Handlee', 'Harmattan', 'Herr Von Muellerhoff', 'Holtwood One SC', 'IM Fell DW Pica', 'IM Fell DW Pica SC', 'IM Fell Double Pica', 'IM Fell Double Pica SC', 'IM Fell English', 'IM Fell English SC', 'IM Fell French Canon', 'IM Fell French Canon SC', 'IM Fell Great Primer', 'IM Fell Great Primer SC', 'Imprima', 'Inika', 'Italiana', 'Italianno', 'Jockey One', 'omhuria', 'Joti One', 'Jomhuria', 'Julee', 'Just Me Again Down Here', 'Katibeh', 'Kavivanar', 'Keania One', 'Kelly Slab', 'Kite One', 'Knewave', 'Kotta One', 'Kreon', 'Krona One', 'Leckerli One', 'Ledger', 'Lekton', 'Lemon', 'Lilita One', 'Lily Script One', 'Linden Hill', 'Love Ya Like A Sister ', 'Lovers Quarrel', 'Lusitana', 'Lustria', 'Macondo', 'Macondo Swash Caps', 'Magra', 'Marck Script', 'Marko One', 'Marvel', 'Mate', 'Mate SC', 'Medula One', 'Meera Inimai', 'Merienda', 'Merienda One', 'Mina', 'Mirza', 'Miss Fajardose', 'Modern Antiqua', 'Monofett', 'Monoton', 'Monsieur La Doulaise', 'Montaga', 'Montserrat', 'Montserrat Subrayada', 'Mountains of Christmas', 'Mr Bedfort', 'Mr Dafoe', 'Mr De Haviland', 'Mrs Saint Delafield', 'Mrs Sheppards', 'Niconne', 'Nixie One', 'Nobile', 'Norican', 'Nosifer', 'Offside', 'Oldenburg', 'Oleo Script', 'Oleo Script Swash Caps', 'Orbitron', 'Overlock', 'Overlock SC', 'Ovo', 'Paprika', 'Passero One', 'Passion One', 'Pathway Gothic One', 'Piedra', 'Pinyon Script', 'Pirata One', 'Playball', 'Poiret One', 'Poller One', 'Poly', 'Pompiere', 'Poppins', 'Port Lligat Sans', 'Port Lligat Slab', 'Preahvihear', 'Qwigley', 'Rambla', 'Ranga', 'Reem Kufi', 'Rammetto One', 'Ribeye Marrow', 'Righteous', 'Rochester', 'Rosarivo', 'Rouge Script', 'Ruda', 'Rufina', 'Ruge Boogie', 'Ruluko', 'Ruslan Display', 'Russo One', 'Ruthie', 'Sail A', 'Salsa', 'Sanchez', 'Sancreek', 'Sarina', 'Shadows Into Light Two', 'Short Stack', 'Signika Negative', 'Sintony', 'Smokum', 'Snippet', 'Sofia', 'Sonsie One', 'Sorts Mill Goudy', 'Spirax', 'Squada One', 'Strait', 'Sunflower', 'Swanky and Moo Moo', 'Text Me One', 'Tinyhust', 'The Girl Next Door', 'Titan One', 'Trochut', 'Trykker', 'Tulpen One', 'Unica One', 'Unlock', 'Vast Shadow', 'Viga', 'Voltaire', 'Wellfleet', 'Wendy One', 'Zeyada', 'Yellowtail');
    $true_type_setting = nbdesigner_get_option('nbdesigner_truetype_fonts');
    if( $true_type_setting != '' ){
        $true_type_setting = preg_split('/\r\n|[\r\n]/', $true_type_setting);
    }else{
        $true_type_setting = array();
    }
    return array_merge($true_type, $true_type_setting);
}
function nbd_admin_pages(){
    return apply_filters( 'nbd_admin_pages', array(
        'toplevel_page_nbdesigner', 
        'nbdesigner_page_nbdesigner_manager_product',
        'toplevel_page_nbdesigner_shoper',
        'nbdesigner_page_nbdesigner_frontend_translate',
        'nbdesigner_page_nbdesigner_tools',
        'nbdesigner_page_nbdesigner_system_info',
        'nbdesigner_page_nbdesigner_manager_arts',
        'nbdesigner_page_nbdesigner_manager_fonts',
        'admin_page_nbdesigner_detail_order',
        'nbdesigner_page_nbd_support',
        'nbdesigner_page_nbd_printing_options',
        'edit-template_tag'
    ));
}
function nbd_convert_svg_url( $path, $file ){
    $svg_path = $path . '/svgpath';
    if( !file_exists( $svg_path ) ) wp_mkdir_p( $svg_path );
    $new_svg_path = $svg_path . '/' . $file;
    $xdoc = new DomDocument;
    $xdoc->Load( $path.$file );
    if( nbdesigner_get_option( 'nbdesigner_fix_lost_pdf_image', 'no' ) == 'yes' ){
        /* image path */
        $images = $xdoc->getElementsByTagName( 'image' );
        for ( $i = 0; $i < $images->length; $i++ ) {
            $tagName        = $xdoc->getElementsByTagName( 'image' )->item( $i );
            $attribNode     = $tagName->getAttributeNode( 'xlink:href' );
            $img_src        = $attribNode->value;
            if( strpos( $img_src, "data:image" ) !== FALSE )
            continue;
            if( strpos( $img_src, "data:img" ) !== FALSE )
            continue;
            $type           = strtolower( pathinfo( $img_src, PATHINFO_EXTENSION ) );
            $type           = ( $type =='svg' ) ? 'svg+xml' : $type;

            $path_image     = Nbdesigner_IO::convert_url_to_path( $img_src );
            //$tagName->setAttribute( 'xlink:href', $path_image );

            $data   = nbd_file_get_contents( $path_image );
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode( $data );
            $tagName->setAttribute( 'xlink:href', $base64 );
        }
    }
    //process clipPath 
    $defsEl = $xdoc->getElementsByTagName('defs')->item(0);
    $cpEls  = $xdoc->getElementsByTagName('clipPath');

    $has_flag = false;
    foreach ($cpEls as $cpEl) {
        $grandParentGroup   = $cpEl->parentNode->parentNode;
        if( $grandParentGroup->hasAttribute('flag-clip') ) {
            $has_flag = true;
            break;
        }
    }

    foreach ($cpEls as $cpEl) {
        $cloneCp            = $cpEl;
        $parentGroup        = $cpEl->parentNode;
        $grandParentGroup   = $cpEl->parentNode->parentNode;
        
        if( $parentGroup->tagName == 'defs' ){
            continue;
        }

        if( strpos( $cloneCp->getAttribute('id'), 'imageCrop' ) !== FALSE ){
            continue;
        }

        if( $cloneCp->childNodes->item(1)->nodeName != 'path' ){
            continue;
        }

        if( $has_flag && !$grandParentGroup->hasAttribute('flag-clip') ){
            continue;
        }

        $cpTm       = $cloneCp->childNodes->item(1)->getAttribute('transform');
        $parentGroup->removeChild( $cpEl );
        $defsEl->appendChild( $cloneCp );
        $tm         = $parentGroup->getAttribute('transform');
        $cpMtArr    = nbd_get_transform_arr( $cpTm, 'matrix' );
        $cpTrArr    = nbd_get_transform_arr( $cpTm, 'translate' );

        $sx         = 1 / $cpMtArr[0];
        $sy         = 1 / $cpMtArr[3];
        $tx         = - $cpMtArr[4] - $cpTrArr[0] * $cpMtArr[0];
        $ty         = - $cpMtArr[5] - $cpTrArr[1] * $cpMtArr[3];
        $newTm      = 'scale( ' . $sx . ', ' . $sy . ' ) translate(' . $tx . ', ' . $ty . ') ' . $tm;
        $parentGroup->setAttribute( 'transform', $newTm );
    }
    $new_svg = $xdoc->saveXML();
    file_put_contents( $new_svg_path, $new_svg );
}
function nbd_get_transform_arr( $attribute, $type ){
    $transform = array();
    preg_match_all( '/('. $type .')[\s]*\(([^\)]+)\)/si', $attribute, $transform, PREG_SET_ORDER );
    foreach ($transform as $key => $data) {
        switch ( $type ) {
            case 'matrix':
                $r      = array(1, 0, 0, 1, 0, 0);
                $regs   = array();
                if (preg_match('/([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)/si', $data[2], $regs)) {
                    $r[0] = $regs[1];
                    $r[1] = $regs[2];
                    $r[2] = $regs[3];
                    $r[3] = $regs[4];
                    $r[4] = $regs[5];
                    $r[5] = $regs[6];
                }
                break;
            case 'translate':
                $r      = array(0, 0);
                $regs   = array();
                if (preg_match('/([a-z0-9\-\.]+)[\,\s]+([a-z0-9\-\.]+)/si', $data[2], $regs)) {
                    $r[0] = $regs[1];
                    $r[1] = $regs[2];
                }
                break;
        }
    }
    return $r;
}
function nbd_get_product_layout($pid){
    $layout = nbdesigner_get_option('nbdesigner_design_layout');
    $option = unserialize(get_post_meta($pid, '_nbdesigner_option', true));
    $layout =  isset($option['layout']) ? $option['layout'] : $layout;
    return $layout;
}
function is_nbd_product_with_vista_layout( $pid ){
    $is_vista = false;
    if (is_nbdesigner_product($pid)) {
        $option = unserialize(get_post_meta($pid, '_nbdesigner_option', true));
        if( isset($option['layout']) && $option['layout'] == 'v' ){
            $is_vista = true;
        }
    }
    return $is_vista;
}
function nbd_get_user_information( $id = false ){
    $infos = array();
    $current_user = wp_get_current_user();
    $id = $id ? $id : $current_user->ID;
    if( 0 != $id ){
        $customer = new WC_Customer( $id );
        $customer_data = array(
            'email'            => $customer->get_email(),
            'first_name'       => $customer->get_first_name(),
            'last_name'        => $customer->get_last_name(),
            'username'         => $customer->get_username(),
            'avatar_url'       => $customer->get_avatar_url(),
            'website'          => get_the_author_meta( 'url', $id ),
            'billing_address'  => array(
                'first_name' => $customer->get_billing_first_name(),
                'last_name'  => $customer->get_billing_last_name(),
                'company'    => $customer->get_billing_company(),
                'address_1'  => $customer->get_billing_address_1(),
                'address_2'  => $customer->get_billing_address_2(),
                'city'       => $customer->get_billing_city(),
                'state'      => $customer->get_billing_state(),
                'postcode'   => $customer->get_billing_postcode(),
                'country'    => $customer->get_billing_country(),
                'email'      => $customer->get_billing_email(),
                'phone'      => $customer->get_billing_phone(),
            ),
            'shipping_address' => array(
                'first_name' => $customer->get_shipping_first_name(),
                'last_name'  => $customer->get_shipping_last_name(),
                'company'    => $customer->get_shipping_company(),
                'address_1'  => $customer->get_shipping_address_1(),
                'address_2'  => $customer->get_shipping_address_2(),
                'city'       => $customer->get_shipping_city(),
                'state'      => $customer->get_shipping_state(),
                'postcode'   => $customer->get_shipping_postcode(),
                'country'    => $customer->get_shipping_country(),
            ),
            'title'     =>  get_the_author_meta( 'acf[field_5b98eb8deeedc]', $id ),
            'mobile'    =>  get_the_author_meta( 'acf[field_5b98eceeac859]', $id )
        );
        $customer_data = apply_filters('nbd_customer_data', $customer_data);
        /* Validate data */
        $infos['full_name'] = array(
            'title' => esc_html__('Full name', 'web-to-print-online-designer'),
            'value' => ($customer_data['shipping_address']['first_name'] == '' ? ($customer_data['billing_address']['first_name'] != '' ? $customer_data['billing_address']['first_name'] : $customer_data['first_name']) : $customer_data['shipping_address']['first_name']) . ' ' . ($customer_data['shipping_address']['last_name'] == '' ? ($customer_data['billing_address']['last_name'] != '' ? $customer_data['billing_address']['last_name'] : $customer_data['last_name']) : $customer_data['shipping_address']['last_name'])
        );
        $infos['first_name'] = array(
            'title' => esc_html__('First name', 'web-to-print-online-designer'),
            'value' => $customer_data['shipping_address']['first_name'] == '' ? ($customer_data['billing_address']['first_name'] != '' ? $customer_data['billing_address']['first_name'] : $customer_data['first_name']) : $customer_data['shipping_address']['first_name']
        );
        $infos['last_name'] = array(
            'title' => esc_html__('Last name', 'web-to-print-online-designer'),
            'value' => $customer_data['shipping_address']['last_name'] == '' ? ($customer_data['billing_address']['last_name'] != '' ? $customer_data['billing_address']['last_name'] : $customer_data['last_name']) : $customer_data['shipping_address']['last_name']
        );
        $infos['company'] = array(
            'title' => esc_html__('Company', 'web-to-print-online-designer'),
            'value' => $customer_data['shipping_address']['company'] == '' ? ($customer_data['billing_address']['company'] != '' ? $customer_data['billing_address']['company'] : esc_html__('Company', 'web-to-print-online-designer')) : $customer_data['shipping_address']['company']
        );
        $infos['address'] = array(
            'title' => esc_html__('Address', 'web-to-print-online-designer'),
            'value' => $customer_data['shipping_address']['address_1'] == '' ? ($customer_data['billing_address']['address_1'] != '' ? $customer_data['billing_address']['address_1'] : esc_html__('Address', 'web-to-print-online-designer')) : $customer_data['shipping_address']['address_1']
        );
        $infos['postcode'] = array(
            'title' => esc_html__('Postcode', 'web-to-print-online-designer'),
            'value' => $customer_data['shipping_address']['postcode'] == '' ? ($customer_data['billing_address']['postcode'] != '' ? $customer_data['billing_address']['postcode'] : esc_html__('12345', 'web-to-print-online-designer')) : $customer_data['shipping_address']['postcode']
        );
        $infos['city'] = array(
            'title' => esc_html__('City', 'web-to-print-online-designer'),
            'value' => $customer_data['shipping_address']['city'] == '' ? ($customer_data['billing_address']['city'] != '' ? $customer_data['billing_address']['city'] : esc_html__('City Name', 'web-to-print-online-designer')) : $customer_data['shipping_address']['city']
        );
        $infos['phone'] = array(
            'title' => esc_html__('Phone', 'web-to-print-online-designer'),
            'value' => $customer_data['billing_address']['phone'] != '' ? $customer_data['billing_address']['phone'] : esc_html__('+012 346 6789', 'web-to-print-online-designer')
        );
        $infos['email'] = array(
            'title' =>  esc_html__('Email', 'web-to-print-online-designer'),
            'value' => $customer_data['billing_address']['email'] != '' ? $customer_data['billing_address']['email'] : esc_html__('example@website.com', 'web-to-print-online-designer')
        );
        $infos['mobile'] = array(
            'title' => esc_html__('Mobile', 'web-to-print-online-designer'),
            'value' => $customer_data['mobile'] != '' ? $customer_data['mobile'] : esc_html__('+012 346 6789', 'web-to-print-online-designer')
        );
        $infos['website'] = array(
            'title' => esc_html__('Website', 'web-to-print-online-designer'),
            'value' => $customer_data['website'] != '' ? $customer_data['website'] : esc_html__('website.com', 'web-to-print-online-designer')
        );
        $infos['title'] = array(
            'title' => esc_html__('Title', 'web-to-print-online-designer'),
            'value' => $customer_data['title'] != '' ? $customer_data['title'] : esc_html__('Title', 'web-to-print-online-designer')
        ); 
        $infos['country'] = array(
            'title' => esc_html__('Country', 'web-to-print-online-designer'),
            'value' => $customer_data['shipping_address']['country'] == '' ? ($customer_data['billing_address']['country'] != '' ? $customer_data['billing_address']['country'] : esc_html__('Country', 'web-to-print-online-designer')) : $customer_data['shipping_address']['country']
        );
        $infos = apply_filters('nbd_customer_infos', $infos);
    }
    return $infos;
}
function nbd_get_redirect_url(){
    $rd                 = wc_clean( $_GET['rd'] );
    switch( $rd ){
        case 'cart':
            $cart_item_key      = wc_clean( $_GET['cik'] );
            $id                 = 'nbd' . $cart_item_key;
            $redirect_url       = wc_get_cart_url().'#'.$id;
            break;
        case 'checkout':
            $cart_item_key      = wc_clean( $_GET['cik'] );
            $id                 = 'nbd' . $cart_item_key;
            $redirect_url       = wc_get_checkout_url().'#'.$id;
            break;
        case 'order':
            $order_id           = absint( $_GET['oid'] );
            $item_id            = absint( $_GET['item_id'] );
            $redirect_url       = wc_get_endpoint_url( 'view-order', $order_id, wc_get_page_permalink( 'myaccount' ) ) . '#' . $item_id;
            break;
        case 'designer':
            $aid                = wc_clean( $_GET['aid'] );
            $redirect_url       = add_query_arg(array('id' => $aid), getUrlPageNBD('designer'));
            break;
        case 'designer_template':
            $aid                = wc_clean( $_GET['aid'] );
            $template_id        = wc_clean( $_GET['template_id'] );
            $redirect_url       = add_query_arg(array('id' => $aid, 'template_id' => $template_id), getUrlPageNBD('designer'));
            break;
        case 'admin_templates':
            $product_id         = absint( $_GET['product_id'] );
            $redirect_url       = add_query_arg(array('pid' => $product_id, 'view' => 'templates'), admin_url('admin.php?page=nbdesigner_manager_product'));
            break;
        case 'admin_order':
            $arr                = array('nbd_item_key' => wc_clean( $_GET['nbd_item_key'] ), 'order_id' => absint( $_GET['order_id'] ), 'product_id'  => absint( $_GET['product_id'] ), 'variation_id'  => absint( $_GET['variation_id'] ), 'item_id' => wc_clean( $_GET['item_id'] ));
            $redirect_url       = add_query_arg($arr, admin_url('admin.php?page=nbdesigner_detail_order'));
            break;
        case 'my_design':
            $current_page       = isset( $_GET['current_page'] ) ? absint( $_GET['current_page'] ) : 1;
            $redirect_url       = wc_get_endpoint_url( 'my-designs', $current_page, wc_get_page_permalink( 'myaccount' ) );
            break;
        case 'my_store_design':
            $current_page       = isset( $_GET['current_page'] ) ? absint( $_GET['current_page'] ) : 1;
            $redirect_url       = add_query_arg( array( 'tab' => 'designs' ), wc_get_endpoint_url( 'my-store', $current_page, wc_get_page_permalink( 'myaccount' ) ) );
            break;
        case 'my_design_detail':
            $design_id          = wc_clean( $_GET['design_id'] );
            $redirect_url       = wc_get_endpoint_url( 'view-design', $design_id, wc_get_page_permalink( 'myaccount' ) );
            break;
        case 'print_option':
            $get                = array(
                'action'    => 'edit',
                'id'        => absint( $_GET['oid'] ),
                'paged'     => absint( $_GET['paged'] )
            );
            $redirect_url       = add_query_arg($get, admin_url('admin.php?page=nbd_printing_options'));
            break;
        default:
            $redirect_url       = $rd;
            break;
    }
    return apply_filters('nbd_redirect_url', $redirect_url );
}
function nbd_download_remote_file( $url, $path ){
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)" );
/*     curl_setopt( $ch, CURLOPT_SSLVERSION, 3 );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 ); 
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 2 );  */
    curl_setopt( $ch, CURLOPT_TIMEOUT, 30 );
    curl_setopt( $ch, CURLOPT_HEADER, 0 );
    $data = curl_exec ( $ch );
    $info = curl_getinfo( $ch );
    if ( $info['http_code'] == 200 ){
        if( $data ){
            $file = fopen( $path, "w+" );
            fputs( $file, $data );
            fclose( $file );
            return true;
        }
        return false;
    }
    curl_close( $ch );
    return false;
}
function nbd_rgb2cmyk( $rgb ){
    $r = isset( $rgb['R'] ) ? $rgb['R'] : $rgb[0];
    $g = isset( $rgb['G'] ) ? $rgb['G'] : $rgb[1];
    $b = isset( $rgb['B'] ) ? $rgb['B'] : $rgb[2];
    $c = (255 - $r) / 255.0 * 100;
    $m = (255 - $g) / 255.0 * 100;
    $y = (255 - $b) / 255.0 * 100;
    $b = min(array($c, $m, $y));
    $c = $c - $b;
    $m = $m - $b;
    $y = $y - $b;
    $cmyk = array($c, $m, $y, $b);
    return $cmyk;
}
function nbd_get_extension($file_name) {
    $filetype   = explode('.', $file_name);
    $file_exten = $filetype[count($filetype) - 1];
    if( false !== strpos($file_name, '.jpg?') || false !== strpos($file_name, '.jpeg?') || false !== strpos($file_name, 'fm=jpg') ) $file_exten = 'jpg';
    return $file_exten;
}
function nbd_get_client_ip() {
    $ipaddress = '';
    $_server   = $_SERVER;

    if ( isset( $_server['HTTP_CLIENT_IP'] ) ) {
        $ipaddress = $_server['HTTP_CLIENT_IP'];
    } else if ( isset( $_server['HTTP_X_FORWARDED_FOR'] ) ) {
        $ipaddress = $_server['HTTP_X_FORWARDED_FOR'];
    } else if ( isset( $_server['HTTP_X_FORWARDED'] ) ) {
        $ipaddress = $_server['HTTP_X_FORWARDED'];
    } else if ( isset( $_server['HTTP_FORWARDED_FOR'] ) ) {
        $ipaddress = $_server['HTTP_FORWARDED_FOR'];
    } else if ( isset( $_server['HTTP_FORWARDED'] ) ) {
        $ipaddress = $_server['HTTP_FORWARDED'];
    } else if ( isset( $_server['REMOTE_ADDR'] ) ) {
        $ipaddress = $_server['REMOTE_ADDR'];
    } else {
        $ipaddress = 'UNKNOWN';
    }

    return $ipaddress;
}
function nbd_get_server_ip(){
    $output_ip          = 'unknown';

    if ( function_exists( 'curl_init' ) ) {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, 'https://ifconfig.co/ip' );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
        $data = curl_exec( $ch );
        curl_close( $ch );
        $output_ip = $data != '' ? $data : 'unknown';
    }
    
    return $output_ip;
}
function nbd_get_current_page(){
    global $pagenow;
    if ( ! empty( $_GET['page'] ) ) {
        return $_GET['page'];
    } else {
        if( $pagenow != 'index.php' ) return $pagenow;
        return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }
}
function nbd_format_time( $datetime ) {
    $timestamp   = strtotime( $datetime );
    $date_format = get_option( 'date_format' );
    $time_format = get_option( 'time_format' );

    return date_i18n( $date_format . ' ' . $time_format, $timestamp );
}
function nbd_check_publish_design_permission( $user_id ){
    if( 'on' == get_user_meta( $user_id, 'nbd_auto_approve_design', true ) ) return true;
    $user           = new \WP_User( $user_id );
    $user_meta      = get_userdata( $user_id );
    $user_roles     = $user_meta->roles;
    $manager_roles  = array( 'administrator', 'shop_manager' );
    $is_manager     = false;
    foreach( $user_roles as $role ){
        if( in_array( $role, $manager_roles ) ){
            $is_manager = true;
            break;
        }
    }
    return $is_manager;
}
function nbd_encode_design_id( $design_id ){
    $design_id  = absint( $design_id );
    $design_id += 2020;
    return base64_encode( $design_id );
}
function nbd_decode_design_id( $design_code ){
    $design_id  = absint( base64_decode( $design_code ) );
    $design_id -= 2020;
    return $design_id;
}
function nbd_is_base64_string( $s ) {
    if ( ($b = base64_decode( $s, TRUE ) ) === FALSE) {
      return FALSE;
    }
    $e = mb_detect_encoding( $b );
    if ( in_array( $e, array( 'UTF-8', 'ASCII' ) ) ) {
        return TRUE;
    } else {
        return FALSE;
    }
}
function nbd_memory_size_to_num( $memory_size ) {
    $unit = strtoupper( substr( $memory_size, - 1 ) );
    $size = substr( $memory_size, 0, - 1 );

    $multiplier = array(
        'P' => 5,
        'T' => 4,
        'G' => 3,
        'M' => 2,
        'K' => 1,
    );

    if ( isset( $multiplier[ $unit ] ) ) {
        for ( $i = 1; $i <= $multiplier[ $unit ]; $i ++ ) {
            $size *= 1024;
        }
    }

    return $size;
}
function nbd_get_nbdesigner_dir_size(){
    $upload_dir = wp_get_upload_dir();
    if ( function_exists( 'ini_get' ) ) {
        $max_execution_time = ini_get( 'max_execution_time' );
    }
    if ( empty( $max_execution_time ) ) {
        $max_execution_time = 30;
    }
    if ( $max_execution_time > 20 ) {
        $max_execution_time -= 2;
    }
    $paths = array(
        'nbdesigner'    => NBDESIGNER_DATA_DIR,
        'designs'       => NBDESIGNER_CUSTOMER_DIR,
        'uploads'       => NBDESIGNER_UPLOAD_DIR,
        'temp'          => NBDESIGNER_TEMP_DIR
    );

    $exclude = $paths;
    unset( $exclude['nbdesigner'] );
    $exclude = array_values( $exclude );

    $size_total = 0;
    $all_sizes  = array();

    foreach ( $paths as $name => $path ) {
        $dir_size = null;
        $results  = array(
            'path' => $path,
            'raw'  => 0,
        );

        if ( microtime( true ) - WP_START_TIMESTAMP < $max_execution_time ) {
            if ( 'nbdesigner' === $name ) {
                $dir_size = recurse_dirsize( $path, $exclude, $max_execution_time );
            } else {
                $dir_size = recurse_dirsize( $path, null, $max_execution_time );
            }
        }

        if ( false === $dir_size ) {
            // Error reading.
            $results['size']  = __( 'The size cannot be calculated. The directory is not accessible. Usually caused by invalid permissions.' );
            $results['debug'] = 'not accessible';

            // Stop total size calculation.
            $size_total = null;
        } elseif ( null === $dir_size ) {
            // Timeout.
            $results['size']  = __( 'The directory size calculation has timed out. Usually caused by a very large number of sub-directories and files.' );
            $results['debug'] = 'timeout while calculating size';

            // Stop total size calculation.
            $size_total = null;
        } else {
            if ( null !== $size_total ) {
                $size_total += $dir_size;
            }

            $results['raw']   = $dir_size;
            $results['size']  = size_format( $dir_size, 2 );
            $results['debug'] = $results['size'] . " ({$dir_size} bytes)";
        }

        $all_sizes[ $name ] = $results;
    }

    if ( null !== $size_total ){
        $size_total_mb  = size_format( $size_total, 2 );
        $all_sizes['total_size'] = array(
            'raw'   => $size_total,
            'size'  => $size_total_mb,
            'debug' => $size_total_mb . " ({$size_total} bytes)"
        );
    }else{
        $all_sizes['total_size'] = array(
            'size'  => __( 'Total size is not available. Some errors were encountered when determining the size of your installation.' ),
            'debug' => 'n/a'
        );
    }

    return $all_sizes;
}
function nbd_is_exif_enabled(){
    try {
        if ( extension_loaded( 'exif' ) && function_exists( 'exif_read_data' ) ) {
            return true;
        }
        
        return false;
    } catch ( \Exception $e ) {
        return false;
    }
}
function nbd_get_system_info(){
    if ( function_exists( 'curl_version' ) ){
        $curl           = curl_version();
        $curl_version   = $curl['version'] . ' ' . $curl['ssl_version'];
    }else{
        $curl_version = 'n/a';
    }

    preg_match( "#^\d+(\.\d+)*#", PHP_VERSION, $match );
    $php_version = $match[0];

    if ( class_exists( 'Imagick' ) && is_callable( array( 'Imagick', 'getVersion' ) ) ) {
        $imagick_version_string = Imagick::getVersion()['versionString'];
        preg_match( "/([0-9]+\.[0-9]+\.[0-9]+)/", $imagick_version_string, $imatch );
        $imagick_version = $imatch[0];
    }else{
        $imagick_version        = 'n/a';
        $imagick_version_string = 'n/a';
    }

    if ( function_exists( 'exec' ) ) {
        $gs = exec( 'gs --version' );
        if ( empty( $gs ) ) {
            $ghostscript    = 'n/a';
        } else {
            $ghostscript    = $gs;
        }
    }else{
        $ghostscript = 'unknown';
    }

    if ( function_exists( 'php_uname' ) ) {
        $server_architecture = sprintf( '%s %s %s', php_uname( 's' ), php_uname( 'r' ), php_uname( 'm' ) );
    } else {
        $server_architecture = 'unknown';
    }

    if( isset( $_SERVER['SERVER_SOFTWARE'] ) ){
        $httpd_software = $_SERVER['SERVER_SOFTWARE'];
    }else{
        $httpd_software = 'unknown';
    }

    if ( function_exists( 'ini_get' ) ) {
        $memory_limit           = ini_get( 'memory_limit' );
        $max_input_vars         = ini_get( 'max_input_vars' );
        $max_execution_time     = ini_get( 'max_execution_time' );
        $upload_max_filesize    = ini_get( 'upload_max_filesize' );
        $post_max_size          = ini_get( 'post_max_size' );
        $allow_url_fopen        = ini_get( 'allow_url_fopen' );
        $max_file_uploads       = ini_get( 'max_file_uploads' );
    }else{
        $memory_limit           = 'unknown';
        $max_input_vars         = 'unknown';
        $max_execution_time     = 'unknown';
        $upload_max_filesize    = 'unknown';
        $post_max_size          = 'unknown';
        $allow_url_fopen        = 'unknown';
        $max_file_uploads       = 'unknown';
    }

    if ( function_exists( 'gd_info' ) ) {
        $gd         = gd_info();
        $gd_version = $gd['GD Version'];
    } else {
        $gd_version = 'n/a';
    }

    $wp_cron    = ! ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON );
    $wp_version = get_bloginfo( 'version' );
    $wc_version = function_exists( 'WC' ) ? WC()->version : 'n/a';

    $wp_debug_log_value = __( 'Disabled' );
    if ( is_string( WP_DEBUG_LOG ) ) {
        $wp_debug_log_value = WP_DEBUG_LOG;
    } elseif ( WP_DEBUG_LOG ) {
        $wp_debug_log_value = __( 'Enabled' );
    }

    $exif_enabled = nbd_is_exif_enabled();

    $upload_dir                 = wp_upload_dir();
    $is_writable_upload_dir     = wp_is_writable( $upload_dir['basedir'] );
    $is_writable_wp_plugin_dir  = wp_is_writable( WP_PLUGIN_DIR );
    $domdocument                = class_exists( 'DOMDocument' ) ? __( 'Enabled' ) : 'n/a';

    $requirements = array(
        'wp_version'    => '4.9',
        'wc_version'    => '3.0',
        'php_version'   => '5.6',
        'memory_limit'  => '1024M'
    );

    $post_response_successful   = null;
    $post_response_code         = null;
    $post_response_code         = get_transient( 'woocommerce_test_remote_post' );
    if ( false === $post_response_code || is_wp_error( $post_response_code ) ) {
        $response = wp_safe_remote_post(
            'https://www.paypal.com/cgi-bin/webscr',
            array(
                'timeout'     => 10,
                'user-agent'  => 'WooCommerce/' . WC()->version,
                'httpversion' => '1.1',
                'body'        => array(
                    'cmd' => '_notify-validate',
                ),
            )
        );
        if ( ! is_wp_error( $response ) ) {
            $post_response_code = $response['response']['code'];
        }
        set_transient( 'woocommerce_test_remote_post', $post_response_code, HOUR_IN_SECONDS );
    }
    $post_response_successful = ! is_wp_error( $post_response_code ) && $post_response_code >= 200 && $post_response_code < 300;

    $get_response_successful = null;
    $get_response_code       = null;
    $get_response_code = get_transient( 'woocommerce_test_remote_get' );
    if ( false === $get_response_code || is_wp_error( $get_response_code ) ) {
        $response = wp_safe_remote_get( 'https://woocommerce.com/wc-api/product-key-api?request=ping&network=' . ( is_multisite() ? '1' : '0' ) );
        if ( ! is_wp_error( $response ) ) {
            $get_response_code = $response['response']['code'];
        }
        set_transient( 'woocommerce_test_remote_get', $get_response_code, HOUR_IN_SECONDS );
    }
    $get_response_successful = ! is_wp_error( $get_response_code ) && $get_response_code >= 200 && $get_response_code < 300;

    $all_sizes  = nbd_get_nbdesigner_dir_size();
    if( $all_sizes['total_size']['debug'] != 'n/a' ){
        $nbdesigner_dir_size = __( '<b>Total size</b>: ', 'web-to-print-online-designer' ) . $all_sizes['total_size']['size'];
        foreach( $all_sizes as $name => $res ){
            if( $name != 'total_size' && $name != 'nbdesigner' ){
                $nbdesigner_dir_size .= ' | <b>' . $name . '</b> dir: ' . $res['size'];
            }
        }
    }

    return array(
        'server_ip'                 => array(
            'label' => __('Server IP Address', 'web-to-print-online-designer'),
            'value' => nbd_get_server_ip(),
            'class' => ''
        ),
        'wp_version'                => array(
            'label' => __('Wordpress version', 'web-to-print-online-designer'),
            'value' => get_bloginfo( 'version' ),
            'class' => version_compare( $wp_version, $requirements['wp_version'], '>=' ) ? 'good' : 'bad'
        ),
        'wc_version'                => array(
            'label' => __('Woocommerce version', 'web-to-print-online-designer'),
            'value' => $wc_version,
            'class' => ( $wc_version != 'n/a' && version_compare( $wc_version, $requirements['wc_version'], '>=' ) ) ? 'good' : 'bad'
        ),
        'server_architecture'       => array(
            'label' => __('Server architecture', 'web-to-print-online-designer'),
            'value' => $server_architecture,
            'class' => ''
        ),
        'httpd_software'            => array(
            'label' => __('Web server', 'web-to-print-online-designer'),
            'value' => $httpd_software,
            'class' => ''
        ),
        'php_version'               => array(
            'label' => __('PHP version', 'web-to-print-online-designer'),
            'value' => $php_version,
            'class' => version_compare( $php_version, $requirements['php_version'], '>=' ) ? 'good' : 'bad'
        ),
        'max_input_vars'            => array(
            'label' => __('PHP max input variables', 'web-to-print-online-designer'),
            'value' => $max_input_vars,
            'class' => ''
        ),
        'memory_limit'              => array(
            'label' => __('PHP memory limit', 'web-to-print-online-designer'),
            'value' => $memory_limit,
            'class' => nbd_memory_size_to_num( $memory_limit ) >= nbd_memory_size_to_num( $requirements['memory_limit'] ) ? 'good' : 'bad'
        ),
        'wp_memory_limit'           => array(
            'label' => __('WP_MEMORY_LIMIT', 'web-to-print-online-designer'),
            'value' => WP_MEMORY_LIMIT,
            'class' => ''
        ),
        'max_execution_time'        => array(
            'label' => __('PHP time limit', 'web-to-print-online-designer'),
            'value' => $max_execution_time,
            'class' => ''
        ),
        'upload_max_filesize'       => array(
            'label' => __( 'Upload max filesize', 'web-to-print-online-designer' ),
            'value' => $upload_max_filesize,
            'class' => ''
        ),
        'max_file_uploads'          => array(
            'label' => __( 'Maximum number of files allowed to be uploaded', 'web-to-print-online-designer' ),
            'value' => $max_file_uploads,
            'class' => ''
        ),
        'post_max_size'             => array(
            'label' => __( 'PHP post max size', 'web-to-print-online-designer' ),
            'value' => $post_max_size,
            'class' => ''
        ),
        'curl_version'              => array(
            'label' => __( 'cURL version', 'web-to-print-online-designer' ),
            'value' => $curl_version,
            'class' => $curl_version == 'n/a' ? 'bad' : 'good'
        ),
        'allow_url_fopen'           => array(
            'label' => __( 'URL fopen', 'web-to-print-online-designer' ),
            'value' => ( $allow_url_fopen == '1' || $allow_url_fopen == 'On' ) ? __( 'Enabled' ) : __( 'Disabled' ),
            'class' => ( $allow_url_fopen == '1' || $allow_url_fopen == 'On' ) ? 'good' : 'bad'
        ),
        'exif'                      => array(
            'label' => __( 'Exif extension is enabled', 'web-to-print-online-designer' ),
            'value' => $exif_enabled ? __( 'Enabled' ) : __( 'Disabled' ),
            'class' => $exif_enabled ? 'good' : 'bad'
        ),
        'imagick_version'           => array(
            'label' => __( 'ImageMagick version', 'web-to-print-online-designer' ),
            'value' => $imagick_version,
            'class' => $imagick_version == 'n/a' ? 'bad' : 'good'
        ),
        'imagick_version_string'    => array(
            'label' => __( 'ImageMagick version string', 'web-to-print-online-designer' ),
            'value' => $imagick_version_string,
            'class' => $imagick_version_string == 'n/a' ? 'bad' : 'good'
        ),
        'ghostscript'               => array(
            'label' => __( 'Ghostscript version', 'web-to-print-online-designer' ),
            'value' => $ghostscript,
            'class' => $ghostscript == 'n/a' ? 'bad' : 'good'
        ),
        'gd_version'                => array(
            'label' => __( 'GD version', 'web-to-print-online-designer' ),
            'value' => $gd_version,
            'class' => $gd_version == 'n/a' ? 'bad' : 'good'
        ),
        'domdocument'               => array(
            'label' => __( 'DOMDocument', 'web-to-print-online-designer' ),
            'value' => $domdocument,
            'class' => $domdocument == 'n/a' ? 'bad' : 'good'
        ),

        'ABSPATH'                   => array(
            'label' => __( 'WordPress directory location ( ABSPATH )', 'web-to-print-online-designer' ),
            'value' => untrailingslashit( ABSPATH ),
            'class' => ''
        ),
        'WP_DEBUG'                  => array(
            'label' => 'WP_DEBUG',
            'value' => WP_DEBUG ? __( 'Enabled' ) : __( 'Disabled' ),
            'class' => ''
        ),
        'WP_DEBUG_LOG'              => array(
            'label' => 'WP_DEBUG_LOG',
            'value' => $wp_debug_log_value,
            'class' => ''
        ),
        'WP_DEBUG_DISPLAY'          => array(
            'label' => 'WP_DEBUG_DISPLAY',
            'value' => WP_DEBUG_DISPLAY ? __( 'Enabled' ) : __( 'Disabled' ),
            'class' => ''
        ),
        'uploads'                   => array(
            'label' => __( 'The uploads directory' ),
            'value' => ( $is_writable_upload_dir ? __( 'Writable' ) : __( 'Not writable' ) ),
            'class' => ( $is_writable_upload_dir ? 'good' : 'bad' ),
        ),
        'plugins'                   => array(
            'label' => __( 'The plugins directory' ),
            'value' => ( $is_writable_wp_plugin_dir ? __( 'Writable' ) : __( 'Not writable' ) ),
            'class' => ''
        ),
        'wp_cron'                   => array(
            'label' => __( 'WordPress Cron', 'web-to-print-online-designer' ),
            'value' => $wp_cron ? __( 'Enable', 'web-to-print-online-designer' ) : __( 'Disable', 'web-to-print-online-designer' ),
            'class' => $wp_cron ? 'good' : 'bad'
        ),
        'remote_post'               => array(
            'label' => __( 'Remote post', 'web-to-print-online-designer' ),
            'value' => $post_response_successful ? __( 'Enable', 'web-to-print-online-designer' ) : ( is_wp_error( $post_response_code ) ? $post_response_code->get_error_message() : $post_response_code ),
            'class' => $post_response_successful ? 'good' : 'bad'
        ),
        'remote_get'                   => array(
            'label' => __( 'Remote get', 'web-to-print-online-designer' ),
            'value' => $get_response_successful ? __( 'Enable', 'web-to-print-online-designer' ) : ( is_wp_error( $get_response_code ) ? $get_response_code->get_error_message() : $get_response_code ),
            'class' => $get_response_successful ? 'good' : 'bad'
        ),
        'nbdesigner_dir_size'       => array(
            'label' => __( 'NBDesigner directory size', 'web-to-print-online-designer' ),
            'value' => $nbdesigner_dir_size,
            'class' => ''
        )
    );
}

function nbd_sort_file_by_side_callback( $file1, $file2 ){
    $base_name1 = pathinfo( $file1, PATHINFO_FILENAME );
    $base_name2 = pathinfo( $file2, PATHINFO_FILENAME );
    $index1     = 0;
    $index2     = 0;

    if( preg_match_all('/_\d+/', $base_name1, $matches) ){
        $arr1   = explode( '_', $base_name1 );
        $index1 = absint( $arr1[ count( $arr1 ) - 1 ] );
    }

    if( preg_match_all('/_\d+/', $base_name1, $matches) ){
        $arr2   = explode( '_', $base_name2 );
        $index2 = absint( $arr2[ count( $arr2 ) - 1 ] );
    }

    if ( $index1 == $index2 ) return 0;
    return ( $index1 < $index2 ) ? -1 : 1;
}
function nbd_sort_file_by_side( $files ){
    uasort( $files, "nbd_sort_file_by_side_callback" );
    return $files;
}