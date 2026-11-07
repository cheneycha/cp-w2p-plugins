<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if ( ! class_exists( 'WP_Async_Request', false ) ) {
    include_once dirname( WC_PLUGIN_FILE ) . '/includes/libraries/wp-async-request.php';
}

if ( ! class_exists( 'WP_Background_Process', false ) ) {
    include_once dirname( WC_PLUGIN_FILE ) . '/includes/libraries/wp-background-process.php';
}

class NBDL_Generate_Preview_Process extends WP_Background_Process {

    protected $action = 'nbdl_generate_design_previews';

    public function __construct() {
        parent::__construct();
    }

    protected function task( $design_id ) {
        global $wpdb, $nbd_fontend_printing_options;

        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}nbdesigner_templates WHERE id=%d", $design_id
            )
        );

        if ( !empty( $result->id ) && $result->type == 'solid' ) {
            $option_id = $nbd_fontend_printing_options->get_product_option( $result->product_id );
            if( $option_id ){
                $_options = $nbd_fontend_printing_options->get_option( $option_id );
                if( $_options ){
                    $options = unserialize($_options['fields']);
                    if( !isset($options['fields']) ){
                        $options['fields'] = array();
                    }

                    $product_id     = $result->product_id;
                    $resource       = $result->resource;
                    $need_generate  = false;
                    $colors         = array();

                    foreach ($options['fields'] as $key => $field){
                        if( isset( $field['nbd_type'] ) && $field['nbd_type'] == 'color' ){
                            if( isset( $field['general']['attributes']['bg_type'] ) && $field['general']['attributes']['bg_type'] == 'c' ){
                                if( isset( $field['general']['attributes']['options'] ) && count( $field['general']['attributes']['options'] ) > 0 ){
                                    $need_generate  = true;
                                    foreach( $field['general']['attributes']['options'] as $key => $op ){
                                        $colors[$key] = $op['bg_color'];
                                    }
                                    break;
                                }
                            }
                        }
                    }

                    if( $need_generate ){
                        $this->create_folder( $product_id, $design_id );
                        $design_path = NBDESIGNER_DATA_DIR . '/previews/' . $product_id . '/' . $design_id;

                        if( file_exists( $design_path ) ){
                            $this->generate_preview( $resource, $product_id, $colors, $design_path );
                        }
                    }
                }
            }
        }

        return false;
    }

    protected function generate_preview( $resource, $product_id, $colors, $design_path ) {
        $setting            = unserialize( get_post_meta( $product_id, '_designer_setting', true ) );
        $design_previews    = Nbdesigner_IO::get_list_images( NBDESIGNER_CUSTOMER_DIR . '/' . $resource, 1 );
        $preview_width      = absint( apply_filters( 'nbdl_solid_design_preview_width', 500 ) );
        $scale              = $preview_width / 500;

        foreach( $design_previews as $design ){
            $filename   = pathinfo( $design, PATHINFO_FILENAME );
            $arr        = explode('_', $filename);
            if( isset( $arr[1] ) ){
                $key  = $arr[1];
                if( isset( $setting[ $key ] ) && $setting[ $key ]["bg_type"] == 'color' ){
                    $side       = $setting[ $key ];
                    $bg         = is_numeric( $side['img_src'] ) ? get_attached_file( $side['img_src'] ) : $side['img_src'];
                    $overlay    = is_numeric( $side['img_overlay'] ) ? get_attached_file( $side['img_overlay'] ) : $side['img_overlay'];
                    $bg_width   = $side["img_src_width"] * $scale;
                    $bg_height  = $side["img_src_height"] * $scale;
                    $ds_width   = $side["area_design_width"] * $scale;
                    $ds_height  = $side["area_design_height"] * $scale;
                    list( $width, $height ) = getimagesize( $design );
                    $position   = $this->calc_design_position( $width, $height, $ds_width, $ds_height );
                    $ds_left    = ( $side["area_design_left"] - $side["img_src_left"] ) * $scale + $position['left'];
                    $ds_top     = ( $side["area_design_top"] - $side["img_src_top"] ) * $scale + $position['top'];
                    $ds_ext     = pathinfo( $design, PATHINFO_EXTENSION );

                    foreach( $colors as $kolor ){
                        $image = imagecreatetruecolor( $bg_width, $bg_height );
                        imagesavealpha( $image, true );
                        $color = imagecolorallocatealpha( $image, 255, 255, 255, 127 );
                        imagefill( $image, 0, 0, $color );

                        if( $ds_ext == 'png' ){
                            $image_design = NBD_Image::crop_and_resize_png_image( $design, $position['width'],  $position['height'] );
                        }else{
                            $image_design = NBD_Image::crop_and_resize_jpg_image( $design, $position['width'],  $position['height'] );
                        }

                        $_color = hex_code_to_rgb( $kolor );
                        $color  = imagecolorallocate( $image, $_color[0], $_color[1], $_color[2] );
                        imagefilledrectangle( $image, 0, 0, $bg_width, $bg_height, $color );

                        imagecopy( $image, $image_design, $ds_left, $ds_top, 0, 0, $position['width'], $position['height'] );

                        if( $side["show_overlay"] == '1' ){
                            $overlay_ext     = pathinfo( $overlay, PATHINFO_EXTENSION );
                            if( $overlay_ext == "png" ){
                                $image_overlay = NBD_Image::nbdesigner_resize_imagepng( $overlay, $bg_width, $bg_height );
                            }else if($over_ext == "jpg" || $over_ext == "jpeg"){
                                $image_overlay = NBD_Image::nbdesigner_resize_imagejpg( $overlay, $bg_width, $bg_height );
                            }
                            imagecopy( $image, $image_overlay, 0, 0, 0, 0, $bg_width, $bg_height );
                        }

                        $path = $design_path . '/' . $key . '_' . str_replace( '#', '', $kolor ) . '.png';
                        imagepng( $image, $path );
                        imagedestroy( $image );
                    }
                }
            }
        }
    }

    protected function create_folder( $product_id, $design_id ){
        $previews_path = NBDESIGNER_DATA_DIR . '/previews';
        if( !file_exists( $previews_path ) ){
            if( !wp_mkdir_p( $previews_path ) ){
                return false;
            }
        }

        $product_path = $previews_path . '/' . $product_id;
        if( !file_exists( $product_path ) ){
            if( !wp_mkdir_p( $product_path ) ){
                return false;
            }
        }

        $design_path = $product_path . '/' . $design_id;
        if( !file_exists( $design_path ) ){
            wp_mkdir_p( $design_path );
        }
    }

    protected function calc_design_position( $design_width, $design_height, $area_width, $area_height ){
        $position = array(
            'left'      => 0,
            'top'       => 0,
            'width'     => $area_width,
            'height'    => $area_height,
            'ratio'     => 1
        );
        if( $area_width /  $area_height > $design_width / $design_height ){
            $ratio              = $area_height / $design_height;
            $new_width          = $design_width * $ratio;
            $position['left']   = ( $area_width - $new_width ) / 2;
            $position['width']  = $new_width;
            $position['ratio']  = $ratio;
        }else{
            $ratio              = $area_width / $design_width;
            $new_height         = $design_height * $ratio;
            $position['top']    = ( $area_height - $new_height ) / 2;
            $position['height'] = $new_height;
            $position['ratio']  = $ratio;
        }
        return $position;
    }

    protected function complete() {
        parent::complete();
    }
}