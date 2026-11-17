<?php
class COD_Generate_File {
    private $order;
    private $has_design = false;
    private $has_upload = false;
    private $only_run = false;

    function __construct( $order ) {
        $this->order = $order;
    }

    /**
     * Run generate file
     * @since 22/04/2022
     */
    public function run($run = false) {
        $this->only_run = $run;

        $generate_time = get_post_meta($this->order->get_id(), 'cod_generate_time', true);
        $generate_time = empty($generate_time) ? array() : $generate_time;

        $is_change = false;
        $products = $this->order->get_items();
        foreach( $products AS $order_item_id => $product ) {
            $nbd_item_key = wc_get_order_item_meta( $order_item_id, '_nbd' );

            if( ! empty($nbd_item_key) ) { 
                $path           = NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key;
                $multil_color   = false; // custom charming print
                $item_id = $order_item_id;

                if( empty($generate_time[$nbd_item_key]) ) {

                    if($item_id) {
                        $order_item = wc_get_order_item_meta( $item_id , '_nbo_options' );
                        $color_select = isset($order_item['color_selected']) ? $order_item['color_selected'] : '';
                        if( is_array($color_select) && isset($color_select[0]) &&  $color_select[0] ) {
                            $multil_color = true;
                        }
                    }
                    if( isset($order_item['pt_color']) && ( $order_item['pt_color'] == 'pt_color1' || $order_item['pt_color'] == 'pt_color3' ) ) {
                        $multil_color = false;
                    }
    
                    $config     = nbd_get_data_from_json($path . '/config.json');
                    // custom charming print
                    if( $multil_color ) {
                        foreach( $color_select as $key => $color ) {
                                nbd_custom_export_pdfs( $nbd_item_key, false, false, 'no' , $color , false);
                        }     
                    }
            
                    $dowload_default = true;
                    if(isset($order_item['pt_color']) && $order_item['pt_color'] == 'pt_color3') {
                        nbd_custom_export_pdfs( $nbd_item_key, false, false, 'no' , '' , true );
                        if(isset($color_select[0])) {
                            nbd_custom_export_pdfs( $nbd_item_key, false, false, 'no' , $color_select[0] , true );
                        }
                        $dowload_default = false;
                    }
                    // end
                    if($dowload_default) {
                        nbd_export_pdfs( $nbd_item_key, false, false, 'no' );
                    }
                    $pdf_path   = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/customer-pdfs';
                    $list_pdf   = Nbdesigner_IO::get_list_files_by_type( $pdf_path, 1, 'pdf' );
                    
                    if( !isset( $config->dpi ) ){
                        $opt_file = $path . '/option.json';
                        $option   = file_exists( $opt_file ) ? @unserialize( file_get_contents( $opt_file ) ) : false;
                        $dpi      = ( is_array( $option ) && isset( $option['dpi'] ) ) ? $option['dpi'] : 300;
                    }else{
                        $dpi = $config->dpi;
                    }
                    if( ! empty( $list_pdf ) && is_array( $list_pdf ) ){
                        foreach( $list_pdf as $key => $pdf ){
                            try {
                                NBD_Image::pdf2image( $pdf, $dpi, 'png' );
                                NBD_Image::pdf2image( $pdf, $dpi, 'jpg' );
                            } catch ( \Throwable $e ) {
                                error_log( 'COD_Generate_File pdf2image error: ' . $e->getMessage() );
                            }
                        }
                    
                        $files = $list_pdf;
                        $path = $pdf_path;
                    }
    
                    $generate_time[$nbd_item_key] = [
                        'svg' => cod_time(),
                        'png' => 0,
                        'jpg' => 0,
                        'pdf' => cod_time()
                    ];
    
                    $settings = get_option('cod_generate_settings');
    
        
                    if( ! empty($settings['nbdesigner_codgf_enable_jpg']) && $settings['nbdesigner_codgf_enable_jpg'] == 'on' ) {
                        $generate_time[$nbd_item_key]['jpg'] = cod_time();
                        $this->get_jpg($path);
                    }
    
                    if( ! empty($settings['nbdesigner_codgf_enable_png']) && $settings['nbdesigner_codgf_enable_png'] == 'on' ) {
                        $generate_time[$nbd_item_key]['png'] = cod_time();
                        $this->get_png($path);
                    }
    
                    $this->get_svg( $multil_color, $path, $nbd_item_key );
                    $this->get_pdfs( $nbd_item_key );

                    $this->update_cronjob($this->order->get_id(), $nbd_item_key);
        
                }

                $is_change = true;
            }
        }


        if( ! empty($is_change) ) {
            update_post_meta($this->order->get_id(), 'cod_generate_time', $generate_time);
        }

        $this->set_design_approve( $this->order->get_items(), $this->order->get_id() );
    }

    private function get_svg( $multil_color, $path, $nbd_item_key ) {
        if($multil_color) {
            $svg_path = $path . '/svgpath';
            if( !file_exists( $svg_path ) ){
                 $this->convert_svg_embed( $path );
             }
             $files = Nbdesigner_IO::get_list_files_by_type( $svg_path, 1, 'svg' );
         } else {

             if( file_exists( NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key .'/product.json' ) ){
                 $product_data = unserialize(file_get_contents(NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key .'/product.json'));
             }
             $path = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key;
             $svg_path = $path . '/svg';
             if (!file_exists($svg_path)) {
                 nbd_convert_svg_embed($path);
             }
             $svg_path_fe = $path . '/svg_fe/';
             if( !file_exists($svg_path_fe) ){
                 Nbdesigner_IO::mkdir($svg_path_fe);
             }else{
                 Nbdesigner_IO::delete_folder($svg_path_fe);
                 Nbdesigner_IO::mkdir($svg_path_fe);
             }

             $files = Nbdesigner_IO::get_list_files_by_type($svg_path, 1, 'svg');

             if( isset($product_data) ) {
                 foreach ($product_data as $key => $value) {
                     $file_name = 'frame_' . $key . '_svg.svg';
                     $file_name_convert = 'frame_cv_' . $key . '_svg.svg';
                     if ($value['include_background'] == 1) {
                         $contentFile = file_get_contents($svg_path . '/' . $file_name);
                         
                         cod_write_log('BUG TODAY');
                         cod_write_log($contentFile);
                        
                         $urlBg = wp_get_attachment_url($value['img_src']);
                         $img = "<image href='$urlBg' width='100%' />";
                         if( $value['img_src_height'] == $value['area_design_height'] && $value['img_src_width'] == $value['area_design_width'] ) {
                             $insertImage = str_replace("</defs>", "</defs> $img", $contentFile);
                             file_put_contents( $svg_path_fe . $file_name_convert , $insertImage );
                         } else {
                             $xdoc = new DomDocument;
                             $xdoc->Load($svg_path . '/' . $file_name);
                             $svg = $xdoc->getElementsByTagName('svg')->item(0);
                             
                         
                         cod_write_log('Bsvgsvgsvg');
                         cod_write_log($svg);
                             $svg->setAttribute('width', $value['area_design_width']);
                             $svg->setAttribute('height', $value['area_design_height']);
                             $svg->setAttribute('x', $value['area_design_left'] - $value['img_src_left'] );
                             $svg->setAttribute('y', $value['area_design_top'] - $value['img_src_top'] );
                             $svg->removeAttribute('xmlns');
                             $svg->removeAttribute('xmlns:xlink');
                             $svg->removeAttribute('xml:space');
                             $svg->removeAttribute('version');
                             $convert_svg = $xdoc->saveXML();
                             $convert_svg = str_replace('<?xml version="1.0" encoding="UTF-8" standalone="no"?>' , '' , $convert_svg);
                             $convert_svg = str_replace('<!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd">' , '' , $convert_svg);
                             $svg1 = '<?xml version="1.0" encoding="UTF-8" standalone="no"?><!DOCTYPE svg PUBLIC "-//W3C//DTD SVG 1.1//EN" "http://www.w3.org/Graphics/SVG/1.1/DTD/svg11.dtd"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" width="'.$value["img_src_width"].'" height="'.$value["img_src_height"].'" viewBox="0 0 '.$value["img_src_width"].' '.$value["img_src_height"].'" xml:space="preserve">';
                             $svg1 .= "<image href='$urlBg' width='100%'/>";
                             $new_svg = $svg1.$convert_svg.'</svg>';
                             file_put_contents( $svg_path_fe . $file_name_convert , $new_svg);  
                         }
                         $files[$key] = $svg_path_fe . $file_name_convert;
                     } else {
                         $files[$key] = $svg_path . '/' . $file_name;
                     }
                 }
             }
        }

        return $files;
    }

    private function get_png( $path ) {
        $files = Nbdesigner_IO::get_list_files_by_type( $path, 1, 'png' );
    }


    private function get_jpg( $path ) {
        $files = Nbdesigner_IO::get_list_files_by_type( $path, 1, 'jpg|jpeg' );
    }

    private function get_pdfs($nbd_item_key) {
        $pdf_path   = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/customer-pdfs';
        $files   = Nbdesigner_IO::get_list_files_by_type( $pdf_path, 1, 'pdf' );
    }

    private function set_design_approve( $products, $order_id ) {
        $_design_action   = 'accept';
        $design_data = unserialize( get_post_meta( $order_id, '_nbdesigner_design_file', true ) );
        $upload_data = unserialize( get_post_meta( $order_id, '_nbdesigner_upload_file', true ) );

        if( empty($design_data) || ! is_array($design_data) ) {
            $design_data = array();
        }

        $update_design = $update_upload = false;

        foreach( $products AS $order_item_id => $product ) {
            if( wc_get_order_item_meta( $order_item_id, '_nbd' ) ){
                $nbd_item_key = wc_get_order_item_meta( $order_item_id, '_nbd' );

                if( ! empty($nbd_item_key) ) {
                    if( is_array( $design_data ) ) {
                        $check = false;
                        foreach( $design_data as $key => $status ){
                            $_key = str_replace( 'nbds_', '', trim( $key ) );
                            if($_key == $order_item_id){
                                $design_data[$key]  = $_design_action;
                                $check              = true;
                            }
                        }
                        if( !$check ) $design_data['nbds_'.$order_item_id] = $_design_action;
                    }else{
                        $design_data['nbds_'.$order_item_id] = $_design_action;
                    }
                }
            }
        }

        if( ! empty($design_data) ) {
            $design_data    = serialize( $design_data );
            $update_design  = update_post_meta( $order_id, '_nbdesigner_design_file', $design_data );
        }



        if ( $update_design ){
            update_post_meta( $order_id, '_nbdesigner_order_changed', 0 );
        }else{
            update_post_meta( $order_id, '_nbdesigner_order_changed', 0 );
        }
    }

    private function update_cronjob($order_id, $item_key) {
        global $wpdb;

        $check = $wpdb->get_var( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cod_auto_generates WHERE item_key = %s",
            $item_key
        ));

        
        $settings = get_option('cod_generate_settings');

        if( ! empty($settings['nbdesigner_codgf_enable_auto']) && ! empty($settings['nbdesigner_codgf_time_clear']) ) {
            $days = absint($settings['nbdesigner_codgf_time_clear']);
            $seconds = $days * COD_GENFILE_DELETE;

            if( empty($check) ) {
                $wpdb->insert($wpdb->prefix . 'cod_auto_generates', array(
                    'status' => 2,
                    'order_id' => $order_id,
                    'item_key' => $item_key,
                    'timestamp' => cod_time(),
                    'created_at' => date('Y-m-d h:i:s', cod_time()),
                    'generated_timestamp' => cod_time() + $seconds,
                    'generated_at' => date('Y-m-d h:i:s', cod_time() + $seconds),
                ), array(
                    '%d',
                    '%d',
                    '%s',
                    '%d',
                    '%s',
                    '%d',
                    '%s'
                ));
            }else {
                $wpdb->update( $wpdb->prefix . 'cod_auto_generates', array(
                    'status' => 2,
                    'generated_timestamp' => cod_time() + $seconds,
                    'generated_at' => date('Y-m-d h:i:s', cod_time() + $seconds)
                ), array(
                    'item_key' => $item_key,
                ) );
            }
        }
    }
}