<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use setasign\Fpdi;

if( !class_exists('Nbdesigner_Output') ){
    class Nbdesigner_Output{
        public static function export_pdfs( $nbd_item_key, $watermark = false, $force = false, $showBleed = 'no', $extra = null, $need_pw = false ){
            $path           = NBDESIGNER_CUSTOMER_DIR .'/' . $nbd_item_key;
            $type = isset($_GET['type']) && $_GET['type'] ? $_GET['type'] : "";
            if($type == 'png') {
                $folder         = $path . '/customer-png';
            } else if($type == 'jpg') {
                $folder         = $path . '/customer-jpg';
            }
            else {
                $folder         = $path . '/customer-pdfs';
            }
            $result         = array();
            $pages          = array();

            if( !file_exists( $folder ) ) {
                wp_mkdir_p( $folder );
            }

            $product_path   = $path . '/product.json';
            $datas          = file_exists( $product_path ) ? @unserialize( file_get_contents( $product_path ) ) : false;
            if ( !is_array( $datas ) ) {
                $datas = array();
            }
            $option_path    = $path . '/option.json';
            $option         = file_exists( $option_path ) ? @unserialize( file_get_contents( $option_path ) ) : false;
            if ( !is_array( $option ) ) {
                $option = array();
            }
            $config_path    = $path . '/config.json';
            $config         = file_exists( $config_path ) ? json_decode( file_get_contents( $config_path ) ) : null;
            $dpi            = isset( $option['dpi'] ) ? (float)$option['dpi'] : 300;
            $dpi            = $dpi > 0 ? $dpi : 300;
            $unit           = isset( $option['unit'] ) ? $option['unit'] : nbdesigner_get_option( 'nbdesigner_dimensions_unit', 'cm' );
            $unit_ratio     = self::get_unit_ratio( $dpi, $unit );

            if( isset( $config->product ) && ( is_array( $config->product ) || is_object( $config->product ) ) && count( (array)$config->product ) ){
                $datas = array();
                foreach( $config->product as $side ){
                    $datas[] = (array)$side;
                }
            };

            $used_font_path = $path . '/used_font.json';
            $used_fonts     = file_exists( $used_font_path ) ? json_decode( file_get_contents( $used_font_path ) ) : array();
            $font_css       = self::build_font_css( $used_fonts );
            $requests       = array();
            $need_pdf_bg    = false;
            $has_raw_pdf    = false;
            
            foreach( $datas as $key => $data ){
                $page_settings = array(
                    'width'         => $data['product_width'] * $unit_ratio . 'in',
                    'height'        => $data['product_height'] * $unit_ratio . 'in',
                    'design_width'  => $data['real_width'] * $unit_ratio . 'in',
                    'design_height' => $data['real_height'] * $unit_ratio . 'in',
                    'design_top'    => $data['real_top'] * $unit_ratio . 'in',
                    'design_left'   => $data['real_left'] * $unit_ratio . 'in',
                    'include_bg'    => false,
                    'include_ov'    => false,
                    'crop_mark'     => false,
                    'watermark'     => $watermark
                );

                $pages[$key] = array(
                    'width'         => $data['product_width'] * $unit_ratio,
                    'height'        => $data['product_height'] * $unit_ratio,
                    'design_top'    => $data['real_top'] * $unit_ratio,
                    'design_left'   => $data['real_left'] * $unit_ratio,
                    'has_raw_pdf'   => false
                );
                // custom charmingprint: include background for the design download Front end
                if( ( isset($_GET['codeblue']) && $_GET['codeblue'] ) || ( isset( $_GET['download_nbd_design_file'] ) && isset( $_GET['order_id'] ) && isset( $_GET['order_item_id'] ) )  ) {
                    $data['include_background'] = 1;
                }
                $include_bg = isset( $data['include_background'] ) ? $data['include_background'] : 1;
                $include_bg = ( $data['bg_type'] == 'image' ) ? $include_bg : 1;
                if( isset( $data['origin_bg_pdf'] ) && $data['origin_bg_pdf'] != '' && $data['bg_type'] == 'image' ){
                    if( $include_bg ){
                        $pages[$key]['origin_bg_pdf']   = NBDESIGNER_TEMP_DIR . $data['origin_bg_pdf'];
                        $need_pdf_bg                    = true;
                    }
                    $include_bg = 0;
                }else{
                    if( !$include_bg && $data['bg_type'] == 'image' ){
                        $page_settings['width']         = $page_settings['design_width'];
                        $page_settings['height']        = $page_settings['design_height'];
                        $pages[$key]['width']           = $data['real_width'] * $unit_ratio;
                        $pages[$key]['height']          = $data['real_height'] * $unit_ratio;
                        $page_settings['design_top']    = 0;
                        $page_settings['design_left']   = 0;
                    }
                }

                if( isset( $data['bg_type'] ) && $data['bg_type'] == 'color' ){
                    $need_bg_color = true;

                    $shapes = isset( $config->areaDesignShapes ) ? (array)$config->areaDesignShapes : array();
                    if( !empty( $shapes[$key] ) ){
                        $need_bg_color  = false;
                    }
                    if( isset( $data['show_overlay'] ) && $data['show_overlay'] == 1 && isset( $data['include_overlay'] ) && $data['include_overlay'] == 1 ){
                        $need_bg_color  = true;
                    }

                    if( $need_bg_color ){
                        $page_settings['include_bg']    = true;
                        $page_settings['bg_type']       = 'color';
                        $page_settings['bg_color']      = isset( $data['bg_color_value'] ) ? $data['bg_color_value'] : '';
                    }
                }

                $allow_exts     = array( 'jpg', 'jpeg', 'png', 'svg' );

                if( $include_bg && $data['bg_type'] == 'image' ){
                    $product_bg     = is_numeric( $data['img_src'] ) ? wp_get_attachment_url( $data['img_src'] ) : $data['img_src'];
                    if( Nbdesigner_IO::checkFileType( basename( $product_bg ), $allow_exts ) ){
                        $page_settings['include_bg']    = true;
                        $page_settings['bg_type']       = 'image';
                        $page_settings['bg_src']        = $product_bg;
                    }
                }

                if( $data['show_overlay'] == 1 && $data['include_overlay'] == 1 ){
                    $overlay = is_numeric( $data['img_overlay'] ) ?  wp_get_attachment_url( $data['img_overlay'] ) : $data['img_overlay'];
                    if( Nbdesigner_IO::checkFileType( basename( $overlay ), $allow_exts ) ){
                        $page_settings['include_ov']    = true;
                        $page_settings['ov_src']        = $overlay;
                    }
                }

                if( $watermark ){
                    $watermark_type                     = nbdesigner_get_option( 'nbdesigner_pdf_watermark_type' );
                    $page_settings['watermark_type']    = $watermark_type;
                    if( $watermark_type == 1 ){
                        $watermark_image    = nbdesigner_get_option( 'nbdesigner_pdf_watermark_image', '' );
                        $watermark_url      = wp_get_attachment_url( $watermark_image );
                        if( $watermark_url ){
                            $page_settings['wm_src']        = $watermark_url;
                        }else{
                            $page_settings['watermark'] = false;
                        }
                    } else {
                        $default_text = get_bloginfo( 'name' );
                        $page_settings['wm_text'] = nbdesigner_get_option( 'nbdesigner_pdf_watermark_text', $default_text );
                    }
                }

                if( isset( $config->contour ) ){
                    $page_settings['contour'] = $config->contour;
                }

                $pages[$key]['page_settings'] = $page_settings;

                $svg_path = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key . '/frame_' . $key . '_svg.svg';
                if( file_exists( $svg_path ) ){
                    $html_url           = self::build_html_page( $nbd_item_key, $key, $svg_path, $page_settings, $font_css );
                    $url_segment        = urlencode( $html_url );
                    $settings_segment   = base64_encode( json_encode( array(
                        'width'         => $data['product_width'] * $unit_ratio . 'in',
                        'height'        => $data['product_height'] * $unit_ratio . 'in'
                    ) ) );

                    $requests[] = array(
                        'index'         => $key,
                        // 'url'           => 'https://v6xgbzye7mxpovavhitcsq7smy0rniff.lambda-url.ap-southeast-1.on.aws?url=' . $url_segment . '&settings=' . urlencode($settings_segment),
						'url'           => 'https://fk829rc3gf.execute-api.ap-southeast-1.amazonaws.com/dev/' . $url_segment . '/' . $settings_segment,
                        'part_index'    => false
                    );
                }

                $_has_raw_pdf   = false;
                if( isset( $config->originPDFs ) && isset( $config->originPDFs[$key] ) && isset( $config->pdfStacks ) ){
                    $resource_pdfs = (array)$config->originPDFs[$key];
                    if( count( $resource_pdfs ) && $config->pdfStacks[$key] != '' ){
                        $has_raw_pdf                    = true;
                        $pages[$key]['has_raw_pdf']     = true;
                        $stack                          = explode( '_', $config->pdfStacks[$key] );
                        $pdf_index                      = 0;
                        $pages[$key]['stack']           = array();
                        $part_folder                    = $folder . '/part/';
                        $_page_settings                 = $page_settings;
                        $_page_settings['include_bg']   = false;

                        if( !file_exists( $part_folder ) ) {
                            wp_mkdir_p( $part_folder );
                        }

                        foreach( $stack as $pos ){
                            if( $pos == 'P' ){
                                $resource_pdf   = (array)$resource_pdfs[$pdf_index];
                                $pages[$key]['stack'][] = array(
                                    'top'       => $pages[$key]['design_top'] + floatval( $resource_pdf['top'] ) * $unit_ratio,
                                    'left'      => $pages[$key]['design_left'] + floatval( $resource_pdf['left'] ) * $unit_ratio,
                                    'width'     => floatval( $resource_pdf['width'] ) * $unit_ratio,
                                    'height'    => floatval( $resource_pdf['height'] ) * $unit_ratio,
                                    'src'       => NBDESIGNER_TEMP_DIR . $resource_pdf['origin_pdf'],
                                    'raw'       => true
                                );
                                $pdf_index++;
                            } else {
                                $svg_path           = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key. '/part/frame_'. $key . '_svg_part_' . $pos . '.svg';
                                $html_url           = self::build_html_page( $nbd_item_key, $key .'_part_' . $pos, $svg_path, $_page_settings, $font_css );
                                $url_segment        = urlencode( $html_url );

                                $settings_segment   = base64_encode( json_encode( array(
                                    'width'         => $data['product_width'] * $unit_ratio . 'in',
                                    'height'        => $data['product_height'] * $unit_ratio . 'in'
                                ) ) );
            
                                $requests[] = array(
                                    'index'         => $key,
                                    // 'url'           => 'https://v6xgbzye7mxpovavhitcsq7smy0rniff.lambda-url.ap-southeast-1.on.aws?url=' . $url_segment . '&settings=' . urlencode($settings_segment),
									'url'           => 'https://fk829rc3gf.execute-api.ap-southeast-1.amazonaws.com/dev/' . $url_segment . '/' . $settings_segment,
                                    'part_index'    => $pos
                                );

                                $pages[$key]['stack'][] = array(
                                    'src'       => $part_folder . $key . '_part_' . $pos . '.pdf',
                                    'raw'       => false
                                );
                            }
                        }
                    }
                }
            }

            $pdfs = self::request_create_pdf( $requests, $folder, $nbd_item_key );
            foreach( $pdfs as $key => $pdf ){
                $pages[$key]['file'] = $pdf;
            }

            if( $force || $need_pw || $need_pdf_bg || $has_raw_pdf ){
                self::merge_pdf( $pages, $folder . '/' . $nbd_item_key . '.pdf', $force, $need_pw );
            }

            $result = Nbdesigner_IO::get_list_files( $folder );
            return $result;
        }
        public static function custom_export_pdfs( $nbd_item_key, $watermark = false, $force = false, $showBleed = 'no', $extra = null, $need_pw = false , $color = '', $background = false ){
            $path           = NBDESIGNER_CUSTOMER_DIR .'/' . $nbd_item_key;
            $type = isset($_GET['type']) && $_GET['type'] ? $_GET['type'] : "";
            if($type == 'png') {
                $folder         = $path . '/customer-png';
            } else if($type == 'jpg') {
                $folder         = $path . '/customer-jpg';
            }
            else {
                $folder         = $path . '/customer-pdfs';
            }
            $result         = array();
            $pages          = array();

            if( !file_exists( $folder ) ) {
                wp_mkdir_p( $folder );
            }

            $product_path   = $path . '/product.json';
            $datas          = file_exists( $product_path ) ? @unserialize( file_get_contents( $product_path ) ) : false;
            if ( !is_array( $datas ) ) {
                $datas = array();
            }
            $option_path    = $path . '/option.json';
            $option         = file_exists( $option_path ) ? @unserialize( file_get_contents( $option_path ) ) : false;
            if ( !is_array( $option ) ) {
                $option = array();
            }
            $config_path    = $path . '/config.json';
            $config         = file_exists( $config_path ) ? json_decode( file_get_contents( $config_path ) ) : null;
            $design_path    = $path . '/design.json';
            $design         = file_exists( $design_path ) ? json_decode( file_get_contents( $design_path ) ) : null;
            $dpi            = isset( $option['dpi'] ) ? (float)$option['dpi'] : 300;
            $dpi            = $dpi > 0 ? $dpi : 300;
            $unit           = isset( $option['unit'] ) ? $option['unit'] : nbdesigner_get_option( 'nbdesigner_dimensions_unit', 'cm' );
            $unit_ratio     = self::get_unit_ratio( $dpi, $unit );

            if( isset( $config->product ) && ( is_array( $config->product ) || is_object( $config->product ) ) && count( (array)$config->product ) ){
                $datas = array();
                foreach( $config->product as $side ){
                    $datas[] = (array)$side;
                }
            };

            $used_font_path = $path . '/used_font.json';
            $used_fonts     = file_exists( $used_font_path ) ? json_decode( file_get_contents( $used_font_path ) ) : array();
            $font_css       = self::build_font_css( $used_fonts );
            $requests       = array();
            $need_pdf_bg    = false;
            $has_raw_pdf    = false;

            foreach( $datas as $key => $data ){
                $page_settings = array(
                    'width'         => $data['product_width'] * $unit_ratio . 'in',
                    'height'        => $data['product_height'] * $unit_ratio . 'in',
                    'design_width'  => $data['real_width'] * $unit_ratio . 'in',
                    'design_height' => $data['real_height'] * $unit_ratio . 'in',
                    'design_top'    => $data['real_top'] * $unit_ratio . 'in',
                    'design_left'   => $data['real_left'] * $unit_ratio . 'in',
                    'include_bg'    => false,
                    'include_ov'    => false,
                    'crop_mark'     => false,
                    'watermark'     => $watermark
                );

                $pages[$key] = array(
                    'width'         => $data['product_width'] * $unit_ratio,
                    'height'        => $data['product_height'] * $unit_ratio,
                    'design_top'    => $data['real_top'] * $unit_ratio,
                    'design_left'   => $data['real_left'] * $unit_ratio,
                    'has_raw_pdf'   => false
                );
                // custom charmingprint: include background for the design download Front end
                if( ( isset($_GET['codeblue']) && $_GET['codeblue'] ) || ( isset( $_GET['download_nbd_design_file'] ) && isset( $_GET['order_id'] ) && isset( $_GET['order_item_id'] ) )  ) {
                    $data['include_background'] = 1;
                }
                $include_bg = isset( $data['include_background'] ) ? $data['include_background'] : 1;
                $include_bg = ( $data['bg_type'] == 'image' ) ? $include_bg : 1;
                if( isset( $data['origin_bg_pdf'] ) && $data['origin_bg_pdf'] != '' && $data['bg_type'] == 'image' ){
                    if( $include_bg ){
                        $pages[$key]['origin_bg_pdf']   = NBDESIGNER_TEMP_DIR . $data['origin_bg_pdf'];
                        $need_pdf_bg                    = true;
                    }
                    $include_bg = 0;
                }else{
                    if( !$include_bg && $data['bg_type'] == 'image' ){
                        $page_settings['width']         = $page_settings['design_width'];
                        $page_settings['height']        = $page_settings['design_height'];
                        $pages[$key]['width']           = $data['real_width'] * $unit_ratio;
                        $pages[$key]['height']          = $data['real_height'] * $unit_ratio;
                        $page_settings['design_top']    = 0;
                        $page_settings['design_left']   = 0;
                    }
                }

                if( isset( $data['bg_type'] ) && $data['bg_type'] == 'color' ){
                    $need_bg_color = true;

                    $shapes = isset( $config->areaDesignShapes ) ? (array)$config->areaDesignShapes : array();
                    if( !empty( $shapes[$key] ) ){
                        $need_bg_color  = false;
                    }
                    if( isset( $data['show_overlay'] ) && $data['show_overlay'] == 1 && isset( $data['include_overlay'] ) && $data['include_overlay'] == 1 ){
                        $need_bg_color  = true;
                    }

                    if( $need_bg_color ){
                        $page_settings['include_bg']    = true;
                        $page_settings['bg_type']       = 'color';
                        $page_settings['bg_color']      = isset( $data['bg_color_value'] ) ? $data['bg_color_value'] : '';
                    }
                }

                $allow_exts     = array( 'jpg', 'jpeg', 'png', 'svg' );

                if( $include_bg && $data['bg_type'] == 'image' ){
                    $product_bg     = is_numeric( $data['img_src'] ) ? wp_get_attachment_url( $data['img_src'] ) : $data['img_src'];
                    if( Nbdesigner_IO::checkFileType( basename( $product_bg ), $allow_exts ) ){
                        $page_settings['include_bg']    = true;
                        $page_settings['bg_type']       = 'image';
                        $page_settings['bg_src']        = $product_bg;
                    }
                }

                if( $data['show_overlay'] == 1 && $data['include_overlay'] == 1 && $custom_download ){
                    $overlay = is_numeric( $data['img_overlay'] ) ?  wp_get_attachment_url( $data['img_overlay'] ) : $data['img_overlay'];
                    if( Nbdesigner_IO::checkFileType( basename( $overlay ), $allow_exts ) ){
                        $page_settings['include_ov']    = true;
                        $page_settings['ov_src']        = $overlay;
                    }
                }
                // custom charmingprint
                if($background) {
                     $page_settings['include_ov']    = false;
                }

                if( $watermark ){
                    $watermark_type                     = nbdesigner_get_option( 'nbdesigner_pdf_watermark_type' );
                    $page_settings['watermark_type']    = $watermark_type;
                    if( $watermark_type == 1 ){
                        $watermark_image    = nbdesigner_get_option( 'nbdesigner_pdf_watermark_image', '' );
                        $watermark_url      = wp_get_attachment_url( $watermark_image );
                        if( $watermark_url ){
                            $page_settings['wm_src']        = $watermark_url;
                        }else{
                            $page_settings['watermark'] = false;
                        }
                    } else {
                        $default_text = get_bloginfo( 'name' );
                        $page_settings['wm_text'] = nbdesigner_get_option( 'nbdesigner_pdf_watermark_text', $default_text );
                    }
                }

                if( isset( $config->contour ) ){
                    $page_settings['contour'] = $config->contour;
                }

                $pages[$key]['page_settings'] = $page_settings;
                // custom charmingprint
                $color_name = $color ? '_'.str_replace( '#' , '' ,$color) : '';
                $_svg       = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/svgpath/frame_' . $key . '_svg'. $color_name .'.svg';
                $custom_include_bg    = true;
                if($color && $background) {
                    if( isset( $data['include_background'] ) && $data['include_background'] ) {
                         $page_settings['include_bg']    = true;
                         nbd_convert_svg_url( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/', 'frame_' . $key . '_svg.svg' );
                         $_svg = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key . '/frame_' . $key . '_svg.svg';
                    } else {
                        $page_settings['include_bg']    = false;
                        $custom_include_bg    = false;
                        nbd_custom_convert_svg_url( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/', 'frame_' . $key . '_svg.svg' , $color , false);
                    }
                } else {
                    if($background) {
                        $_svg   = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/svgpath/frame_' . $key . '_svg_background.svg';
                    }
                    nbd_custom_convert_svg_url( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/', 'frame_' . $key . '_svg.svg' , $color , $background);
                }
                $frame = 'frame_'.$key;
                $background_color = false;
                if(isset($design->$frame) && isset($design->$frame->background) && $background && $color == '' && $custom_include_bg ) {
                    $page_settings['bg_type']       = 'color';
                    $page_settings['bg_color']        = $design->$frame->background;
                    $background_color = true;
                    $_svg = '';
                }

                // $svg_path = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key . '/frame_' . $key . '_svg.svg';
                $svg_path = $_svg;
                if( $background_color || file_exists( $svg_path ) ){
                    $html_url           = self::build_html_page( $nbd_item_key, $key, $svg_path, $page_settings, $font_css );
                    $url_segment        = urlencode( $html_url );
                    $settings_segment   = base64_encode( json_encode( array(
                        'width'         => $data['product_width'] * $unit_ratio . 'in',
                        'height'        => $data['product_height'] * $unit_ratio . 'in'
                    ) ) );

                    $requests[] = array(
                        'index'         => $key,
                        // 'url'           => 'https://v6xgbzye7mxpovavhitcsq7smy0rniff.lambda-url.ap-southeast-1.on.aws?url=' . $url_segment . '&settings=' . urlencode($settings_segment),
						'url'           => 'https://fk829rc3gf.execute-api.ap-southeast-1.amazonaws.com/dev/' . $url_segment . '/' . $settings_segment,
                        'part_index'    => false
                    );
                }

                $_has_raw_pdf   = false;
                if( isset( $config->originPDFs ) && isset( $config->originPDFs[$key] ) && isset( $config->pdfStacks ) ){
                    $resource_pdfs = (array)$config->originPDFs[$key];
                    if( count( $resource_pdfs ) && $config->pdfStacks[$key] != '' ){
                        $has_raw_pdf                    = true;
                        $pages[$key]['has_raw_pdf']     = true;
                        $stack                          = explode( '_', $config->pdfStacks[$key] );
                        $pdf_index                      = 0;
                        $pages[$key]['stack']           = array();
                        $part_folder                    = $folder . '/part/';
                        $_page_settings                 = $page_settings;
                        $_page_settings['include_bg']   = false;

                        if( !file_exists( $part_folder ) ) {
                            wp_mkdir_p( $part_folder );
                        }

                        foreach( $stack as $pos ){
                            if( $pos == 'P' ){
                                $resource_pdf   = (array)$resource_pdfs[$pdf_index];
                                $pages[$key]['stack'][] = array(
                                    'top'       => $pages[$key]['design_top'] + floatval( $resource_pdf['top'] ) * $unit_ratio,
                                    'left'      => $pages[$key]['design_left'] + floatval( $resource_pdf['left'] ) * $unit_ratio,
                                    'width'     => floatval( $resource_pdf['width'] ) * $unit_ratio,
                                    'height'    => floatval( $resource_pdf['height'] ) * $unit_ratio,
                                    'src'       => NBDESIGNER_TEMP_DIR . $resource_pdf['origin_pdf'],
                                    'raw'       => true
                                );
                                $pdf_index++;
                            } else {
                                $svg_path           = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key. '/part/frame_'. $key . '_svg_part_' . $pos . '.svg';
                                $html_url           = self::build_html_page( $nbd_item_key, $key .'_part_' . $pos, $svg_path, $_page_settings, $font_css );
                                $url_segment        = urlencode( $html_url );

                                $settings_segment   = base64_encode( json_encode( array(
                                    'width'         => $data['product_width'] * $unit_ratio . 'in',
                                    'height'        => $data['product_height'] * $unit_ratio . 'in'
                                ) ) );
            
                                $requests[] = array(
                                    'index'         => $key,
                                    // 'url'           => 'https://v6xgbzye7mxpovavhitcsq7smy0rniff.lambda-url.ap-southeast-1.on.aws?url=' . $url_segment . '&settings=' . urlencode($settings_segment),
									'url'           => 'https://fk829rc3gf.execute-api.ap-southeast-1.amazonaws.com/dev/' . $url_segment . '/' . $settings_segment,
                                    'part_index'    => $pos
                                );

                                $pages[$key]['stack'][] = array(
                                    'src'       => $part_folder . $key . '_part_' . $pos . '.pdf',
                                    'raw'       => false
                                );
                            }
                        }
                    }
                }
            }

            $pdfs = self::custom_request_create_pdf( $requests, $folder, $nbd_item_key , $color , $background );
            foreach( $pdfs as $key => $pdf ){
                $pages[$key]['file'] = $pdf;
            }

            if( $force || $need_pw || $need_pdf_bg || $has_raw_pdf ){
                self::merge_pdf( $pages, $folder . '/' . $nbd_item_key . '.pdf', $force, $need_pw );
            }

            $result = Nbdesigner_IO::get_list_files( $folder );
            return $result;
        }
        public static function get_unit_ratio( $dpi, $unit ){
            switch ($unit) {
                case 'mm':
                    $unit_ratio = 1 / 25.4;
                    break;
                case 'in':
                    $unit_ratio = 1;
                    break;
                case 'ft':
                    $unit_ratio = 1 / 12;
                    break;
                case 'px':
                    $unit_ratio = 1 / $dpi;
                    break;
                default:
                    $unit_ratio = 1 / 2.54;
                    break;
            }
            return $unit_ratio;
        }
        public static function build_font_css( $fonts ){
            $google_font_link = '';
            if( empty( $fonts ) || ( !is_array( $fonts ) && !is_object( $fonts ) ) ){
                return array(
                    'google_font_link'  => '',
                    'custom_font_style' => ''
                );
            }

            foreach( $fonts as $font ){
                if( !is_object( $font ) && !is_array( $font ) ) continue;
                $font_obj = (object)$font;
                $font_name = isset( $font_obj->name ) ? str_replace( ' ', '+', $font_obj->name ) : '';

                if( isset( $font_obj->type ) && $font_obj->type == 'google' && $font_name ){
                    $google_font_link .= '<link rel="stylesheet" href="//fonts.googleapis.com/css?family=' . $font_name . ':400,400i,700,700i" />';
                }
            }

            $custom_font_style = '<style type="text/css">';
            foreach( $fonts as $font ){
                if( !is_object( $font ) && !is_array( $font ) ) continue;
                $font_obj = (object)$font;
                $font_name = isset( $font_obj->name ) ? str_replace( ' ', '+', $font_obj->name ) : '';
                
                if( !isset( $font_obj->type ) || $font_obj->type != 'google' ){
                    $alias                  = isset( $font_obj->alias ) ? $font_obj->alias : '';
                    $custom_font            = $alias ? nbd_get_font_by_alias( $alias ) : null;
                    $custom_font_variations = array();
                    if( !empty( $custom_font ) && !empty( $custom_font->file ) && ( is_array( $custom_font->file ) || is_object( $custom_font->file ) ) ){
                        foreach( $custom_font->file as $key => $custom_font_url ){
                            $custom_font_variations[$key] = NBDESIGNER_FONT_URL . $custom_font_url;
                            //$custom_font_variations[$key] = 'https://charmingprint.s3.ap-southeast-1.amazonaws.com' . $custom_font_url;
                        }
                    }

                    foreach( $custom_font_variations as $key => $custom_font_variation ){
                        $font_style     = 'normal';
                        $font_weight    = 'normal';
                        switch( $key ){
                            case 'b':
                                $font_weight    = 'bold';
                                break;
                            case 'i':
                                $font_style     = 'italic';
                                break;
                            case 'bi':
                                $font_weight    = 'bold';
                                $font_style     = 'italic';
                                break;
                        }
                        $custom_font_style .= "@font-face {font-family: '" . $alias . "';src: url('" . $custom_font_variation . "') format('truetype');font-weight: " . $font_weight . ";font-style: " . $font_style . ";}";
                    }
                }
            }
            $custom_font_style .= '</style>';

            return array(
                'google_font_link'  => $google_font_link,
                'custom_font_style' => $custom_font_style
            );
        }
        public static function build_html_page( $nbd_item_key, $key, $svg_path, $page_settings, $font_css ){
            $pdf_temp_path = NBDESIGNER_TEMP_DIR . '/pdf-templates';
            if( !file_exists( $pdf_temp_path ) ) {
                wp_mkdir_p( $pdf_temp_path );
            }

            $temp_path  = $pdf_temp_path . '/' . $nbd_item_key . '/';
            $html_path  =  $temp_path . $key .'.html';
            $html_url   = NBDESIGNER_TEMP_URL . '/pdf-templates/' . $nbd_item_key . '/' . $key .'.html';
            if( !file_exists( $temp_path ) ) {
                wp_mkdir_p( $temp_path );
            }

            $svg_string = file_get_contents( $svg_path );
            $svg_string = preg_replace( "/<(?:\?xml|!DOCTYPE).*?>/", "", $svg_string );

            ob_start();
            include NBDESIGNER_PLUGIN_DIR . 'views/pdf-template.php'; 
            $template    = ob_get_clean();

            file_put_contents( $html_path, $template );
            return $html_url;
        }
        public static function merge_pdf( $pages, $output_file, $force, $need_pw ){
            if( !class_exists('TCPDF') ){
                require_once( NBDESIGNER_PLUGIN_DIR.'includes/tcpdf/tcpdf.php' );
            }
            require_once( NBDESIGNER_PLUGIN_DIR.'includes/fpdi/autoload.php' );

            $pdf_format     = array( $pages[0]['width'], $pages[0]['height'] );
            $orientation    = $pages[0]['width'] > $pages[0]['height'] ? "L" : "P";
            $pdf            = new Fpdi\TcpdfFpdi( $orientation, 'in', $pdf_format, true, 'UTF-8', false );

            $pdf->SetMargins( 0, 0, 0, true );
            $pdf->SetCreator( get_site_url() );
            $pdf->SetTitle( get_bloginfo( 'name' ) );
            $pdf->setPrintHeader( false );
            $pdf->setPrintFooter( false );
            $pdf->SetAutoPageBreak( TRUE, 0 );

            foreach( $pages as $key => $page ){
                $pdf_format     = array( $page['width'], $page['height'] );
                $orientation    = $page['width'] > $page['height'] ? "L" : "P";

                if( !$force ){
                    $pdf        = new Fpdi\TcpdfFpdi( $orientation, 'in', $pdf_format, true, 'UTF-8', false );

                    $pdf->SetMargins( 0, 0, 0, true );
                    $pdf->SetCreator( get_site_url() );
                    $pdf->SetTitle( get_bloginfo( 'name' ) );
                    $pdf->setPrintHeader( false );
                    $pdf->setPrintFooter( false );
                    $pdf->SetAutoPageBreak( TRUE, 0 );
                }

                if( isset( $page['file'] ) ){

                    $pdf->AddPage( $orientation, $pdf_format );

                    if( isset( $page['origin_bg_pdf'] ) ){
                        $number_pages = $pdf->setSourceFile( $page['origin_bg_pdf'] );

                        $page_index = 1;
                        if( ( $number_pages - 1 ) >= $key ){
                            $page_index = $key + 1;
                        }

                        $pdf->tplId = $pdf->importPage( $page_index );
                        $pdf->useImportedPage( $pdf->tplId, 0, 0, $page['width'], $page['height'] );
                    }

                    if( !$page['has_raw_pdf'] ){
                        $pdf->setSourceFile( $page['file'] );
                        $pdf->tplId = $pdf->importPage( 1 );
                        $pdf->useImportedPage( $pdf->tplId, 0, 0, $page['width'] );
                    } else {
                        if( $page['page_settings']['include_bg'] ){
                            if( $page['page_settings']['bg_type'] == 'image' ){
                                $path_bg = Nbdesigner_IO::convert_url_to_path( $page['page_settings']['bg_src'] );
                                $pdf->Image( $path_bg, 0, 0, $page['width'], $page['height'], '', '', '', false, '' );
                            }else{
                                $pdf->Rect( 0, 0, $page['width'], $page['height'], 'F', '', hex_code_to_rgb( $page['page_settings']['bg_color'] ) );
                            }
                        }
                        foreach( $page['stack'] as $part ){
                            if( file_exists( $part['src'] ) ){
                                $pdf->setSourceFile( $part['src'] );
                                $tplId = $pdf->importPage( 1 );
                                if( $part['raw'] ){
                                    $pdf->useImportedPage( $tplId, $part['left'], $part['top'], $part['width'], $part['height'] );
                                }else{
                                    $pdf->useImportedPage( $tplId, 0, 0, $page['width'] );
                                }
                            }
                        }
                    }

                    if( $need_pw ){
                        $password = nbdesigner_get_option( 'nbdesigner_pdf_password', '' );
                        if( $password != '' ){
                            $pdf->SetProtection( array( 'print', 'copy', 'modify' ), "", $password, 0, null );
                        }
                    }

                    if( !$force ) $pdf->Output( $page['file'], 'F' );
                }
            }

            if( $force ){
                $pdf->Output( $output_file, 'F' );

                foreach( $pages as $key => $page ){
                    if( isset( $page['file'] ) && file_exists( $page['file'] ) ){
                        unlink( $page['file'] );
                    }
                }
            }
        }
        public static function request_create_pdf( $requests, $folder, $nbd_item_key ){
            $result     = array();
            $mh         = curl_multi_init();
            $multiCurl  = array();

            foreach( $requests as $i => $request ){
                $multiCurl[$i] = curl_init();
                curl_setopt( $multiCurl[$i], CURLOPT_URL, $request['url'] );
                curl_setopt( $multiCurl[$i], CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt( $multiCurl[$i], CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)" );
                curl_setopt( $multiCurl[$i], CURLOPT_TIMEOUT, 30 );
                curl_setopt( $multiCurl[$i], CURLOPT_HEADER, 0 );
                curl_multi_add_handle( $mh, $multiCurl[$i] );
            }

            $index = null;
            do {
                curl_multi_exec( $mh, $index );
            } while( $index > 0 );

            foreach( $multiCurl as $k => $ch ) {
                $res            = curl_multi_getcontent( $ch);
                $return         = true;
                if( $requests[$k]['part_index'] === false ){
                    $output_file    = $folder . '/' . $nbd_item_key . '_' . $requests[$k]['index'] . '.pdf';
                }else{
                    $output_file    = $folder . '/part/' . $requests[$k]['index'] . '_part_' . $requests[$k]['part_index'] . '.pdf';
                    $return         = false;
                }
                $download       = nbd_download_remote_file( $res, $output_file );
                if( $download && $return ){
                    $result[$requests[$k]['index']] = $output_file;
                }
            }

            return $result;
        }
        public static function custom_request_create_pdf( $requests, $folder, $nbd_item_key , $color , $background ){
            $result     = array();
            $mh         = curl_multi_init();
            $multiCurl  = array();
            $color_name     = $color ? '_'.str_replace( '#' , '' ,$color) : ''; // custom charmingprint
            foreach( $requests as $i => $request ){
                $multiCurl[$i] = curl_init();
                curl_setopt( $multiCurl[$i], CURLOPT_URL, $request['url'] );
                curl_setopt( $multiCurl[$i], CURLOPT_RETURNTRANSFER, 1 );
                curl_setopt( $multiCurl[$i], CURLOPT_USERAGENT, "Mozilla/4.0 (compatible;)" );
                curl_setopt( $multiCurl[$i], CURLOPT_TIMEOUT, 30 );
                curl_setopt( $multiCurl[$i], CURLOPT_HEADER, 0 );
                curl_multi_add_handle( $mh, $multiCurl[$i] );
            }

            $index = null;
            do {
                curl_multi_exec( $mh, $index );
            } while( $index > 0 );

            foreach( $multiCurl as $k => $ch ) {
                $res            = curl_multi_getcontent( $ch);
                $return         = true;
                if( $requests[$k]['part_index'] === false ){
                    // $output_file    = $folder . '/' . $nbd_item_key . '_' . $requests[$k]['index'] . '.pdf';
                    $output_file    = $folder .'/'. $nbd_item_key. '_' . $requests[$k]['index'] .$color_name.'.pdf';
                    if($background) {
                        $output_file    = $folder .'/'. $nbd_item_key. '_' . $requests[$k]['index'].'_background.pdf';
                    }
                }else{
                    $output_file    = $folder . '/part/' . $requests[$k]['index'] . '_part_' . $requests[$k]['part_index'] . $color_name . '.pdf';
                    if($_background) {
                        $output_file    = $folder . '/part/' . $requests[$k]['index'] . '_part_' . $requests[$k]['part_index'] . '_background.pdf';
                    }
                    $return         = false;
                }
                $download       = nbd_download_remote_file( $res, $output_file );
                if( $download && $return ){
                    $result[$requests[$k]['index']] = $output_file;
                }
            }

            return $result;
        }
    }
}