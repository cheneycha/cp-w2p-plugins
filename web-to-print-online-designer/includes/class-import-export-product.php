<?php
if ( ! defined( 'ABSPATH' ) ) exit;
if( !class_exists( 'NBD_IMPORT_EXPORT_PRODUCT' ) ){
    class NBD_IMPORT_EXPORT_PRODUCT {
        protected static $instance;
        public static function instance() {
            if ( is_null( self::$instance ) ) {
                self::$instance = new self();
            }
            return self::$instance;
        }
        public function init(){
            $this->ajax();
        }
        public function ajax(){
            $ajax_events = array(
                'nbd_import_product'   => false,
                'nbd_export_product'   => false
            );
            foreach ( $ajax_events as $ajax_event => $nopriv ) {
                add_action( 'wp_ajax_' . $ajax_event, array( $this, $ajax_event ) );
                if ( $nopriv ) {
                    add_action( 'wp_ajax_nopriv_' . $ajax_event, array( $this, $ajax_event ) );
                }
            }
        }
        public function nbd_export_product(){
            $result     = array(
                'flag'  => 1
            );
            $product_id             = $_POST['product_id'];
            $product                = wc_get_product( $product_id) ;

            $enable_design          = get_post_meta( $product_id, '_nbdesigner_enable', true );
            $enable_upload          = get_post_meta( $product_id, '_nbdesigner_enable_upload', true );
            $upload_without_design  = get_post_meta( $product_id, '_nbdesigner_enable_upload_without_design', true );
            $setting_design         = get_post_meta( $product_id, '_designer_setting', true );
            $setting_upload         = get_post_meta( $product_id, '_nbdesigner_upload', true );
            $option                 = get_post_meta( $product_id, '_nbdesigner_option', true );
            $nbo_enable             = get_post_meta( $product_id, '_nbo_enable', true );

            $product_config         = unserialize( $setting_design );
            foreach ( $product_config as $key => $_config ){
                $product_config[$key]['img_src']        = wp_get_attachment_url( $product_config[$key]['img_src'] );
                $product_config[$key]['img_overlay']    = wp_get_attachment_url( $product_config[$key]['img_overlay'] );
            }
            $setting_design         = serialize( $product_config );

            $data                   = array(
                'name'                  => $product->get_name(),
                'description'           => $product->get_description(),
                'short_description'     => $product->get_short_description(),
                'regular_price'         => $product->get_regular_price(),
                'sale_price'            => $product->get_sale_price(),
                'image'                 => $this->get_product_image( $product ),

                'enable_design'         => $enable_design,
                'enable_upload'         => $enable_upload,
                'upload_without_design' => $upload_without_design,
                'setting_design'        => $setting_design,
                'setting_upload'        => $setting_upload,
                'option'                => $option,
                'nbo_enable'            => $nbo_enable
            );

            $export_path            = NBDESIGNER_DATA_DIR . '/export/';
            $product_export_path    = $export_path . $product_id . '/';
            $settings_path          = $product_export_path . 'settings.txt';
            $print_options_path     = $product_export_path . 'print_options.txt';
            $templates_path         = $product_export_path . 'templates.json';

            if( !file_exists( $export_path ) ){
                wp_mkdir_p( $export_path );
            }
            if( !file_exists( $product_export_path ) ){
                wp_mkdir_p( $product_export_path );
            }

            $demo_data_path         = NBDESIGNER_PLUGIN_DIR . 'data/demo_datas.json';
            if( file_exists( $demo_data_path ) ){
                $demo_data = json_decode( file_get_contents( $demo_data_path ), true );
            } else {
                $demo_data = array();
            }

            file_put_contents( $settings_path, serialize( $data ) );
            $result['settings']             = NBDESIGNER_DATA_URL . '/export/' . $product_id . '/settings.txt';
            $demo_data['p'. $product_id]    = array(
                'name'      => $data['name'],
                'image'     => $data['image'],
                'settings'  => $result['settings']
            );

            if( $nbo_enable ){
                global $nbd_fontend_printing_options;
                $print_option_id    = $nbd_fontend_printing_options->get_product_option( $product_id );
                $options            = $nbd_fontend_printing_options->get_option( $print_option_id );
                $option_fields      = unserialize( $options['fields'] );
                $media_objects      = array();

                foreach ( $option_fields['fields'] as $key => $field ){
                    if( !isset( $field['general']['attributes'] ) ){
                        $field['general']['attributes'] = array();
                        $field['general']['attributes']['options'] = array();
                        $option_fields['fields'][$key]['general']['attributes'] = array();
                        $option_fields['fields'][$key]['general']['attributes']['options'] = array();
                    }

                    foreach ( $field['general']['attributes']['options'] as $op_index => $option ){
                        $option['product_image'] = isset($option['product_image']) ? $option['product_image'] : 0;
                        $attachment_id = absint( $option['product_image'] );
                        if( $attachment_id != 0 ){
                            $product_img = wp_get_attachment_url( $attachment_id );
                            if( $product_img ){
                                $mkey                    = 'fields-' . $key . '-general-attributes-options-' . $op_index . '-product_image';
                                $media_objects[$mkey]    = $product_img;
                            }else{
                                $option_fields['fields'][$key]['general']['attributes']['options'][$op_index]['product_image'] = 0;
                            }
                        }

                        if( $option['preview_type'] == 'i' ){
                            $preview_img = wp_get_attachment_url( $option['image'] );
                            if( $preview_img ){
                                $mkey                    = 'fields-' . $key . '-general-attributes-options-' . $op_index . '-image';
                                $media_objects[$mkey]    = $preview_img;
                            } else {
                                $option_fields['fields'][$key]['general']['attributes']['options'][$op_index]['image'] = 0;
                            }
                        }

                        if( isset( $option['enable_subattr'] ) && isset( $option['sub_attributes'] ) && count( $option['sub_attributes'] ) ){
                            foreach( $option['sub_attributes'] as $sak => $sa ){
                                if( $sa['preview_type'] == 'i' ){
                                    $sa_preview_img = wp_get_attachment_url( $sa['image'] );
                                    if( $sa_preview_img ){
                                        $mkey                    = 'fields-' . $key . '-general-attributes-options-' . $op_index . '-sub_attributes-' . $sak . '-image';
                                        $media_objects[$mkey]    = $sa_preview_img;
                                    } else {
                                        $option_fields['fields'][$key]['general']['attributes']['options'][$op_index]['sub_attributes'][$sak]['image'] = 0;
                                    }
                                }
                            }
                        }

                        if( isset( $field['general']['attributes']['bg_type'] ) ){
                            if( $field['general']['attributes']['bg_type'] == 'i' && count( $option['bg_image'] ) ){
                                foreach( $option['bg_image'] as $k => $bg ){
                                    $bg_image = wp_get_attachment_url( $bg );
                                    if( $bg_image ){
                                        $mkey                    = 'fields-' . $key . '-general-attributes-options-' . $op_index . '-bg_image-' . $k;
                                        $media_objects[$mkey]    = $bg_image;
                                    } else {
                                        $option_fields['fields'][$key]['general']['attributes']['options'][$op_index]['bg_image'][$k] = 0;
                                    }
                                }
                            }else{
                                $options[$key]['bg_image']      = array();
                                $options[$key]['bg_image_url']  = array();
                            }
                        }

                        if( isset( $option['overlay_image'] ) ){
                            foreach( $option['overlay_image'] as $k => $ov ){
                                $overlay_img = wp_get_attachment_url( $ov );
                                if( $overlay_img ){
                                    $mkey                    = 'fields-' . $key . '-general-attributes-options-' . $op_index . '-overlay_image-' . $k;
                                    $media_objects[$mkey]    = $overlay_img;
                                } else {
                                    $option_fields['fields'][$key]['general']['attributes']['options'][$op_index]['overlay_image'][$k] = 0;
                                }
                            }
                        }

                        if( isset( $option['frame_image'] ) ){
                            $frame_img = wp_get_attachment_url( $option['frame_image'] );
                            if( $frame_img ){
                                $mkey                    = 'fields-' . $key . '-general-attributes-options-' . $op_index . '-frame_image';
                                $media_objects[$mkey]    = $frame_img;
                            } else {
                                $option_fields['fields'][$key]['general']['attributes']['options'][$op_index]['frame_image'] = 0;
                            }
                        }
                    }

                    if( isset( $field['general']['component_icon'] ) ){
                        $component_icon_img = wp_get_attachment_url( $field['general']['component_icon'] );
                        if( $component_icon_img ){
                            $mkey                    = 'fields-' . $key . '-general-component_icon';
                            $media_objects[$mkey]    = $component_icon_img;
                        } else {
                            $option_fields['fields'][$key]['general']['component_icon'] = 0;
                        }
                    }

                    if( isset( $field['general']['pb_config'] ) ){
                        foreach( $field['general']['pb_config'] as $pb_key => $o_config ){
                            foreach( $o_config as $pbs_key => $so_config ){
                                foreach( $so_config['views'] as $vkey => $view){
                                    $v_img = wp_get_attachment_url( $view['image'] );
                                    if( $v_img ){
                                        $mkey                    = 'fields-' . $key . '-general-pb_config-' . $pb_key . '-' . $pbs_key . '-views-' . $vkey . '-image';
                                        $media_objects[$mkey]    = $v_img;
                                    } else {
                                        $option_fields['fields'][$key]['general']['pb_config'][$pb_key][$pbs_key]['views'][$vkey]['image'] = 0;
                                    }
                                }
                            }
                        }
                    }
                }

                if( isset( $option_fields['groups'] ) ){
                    foreach ( $option_fields['groups'] as $gkey => $group ){
                        $group_img = wp_get_attachment_url( $group['image'] );
                        if( $group_img ){
                            $mkey                    = 'groups-' . $gkey . '-image';
                            $media_objects[$mkey]    = $group_img;
                        } else {
                            $option_fields['groups'][$gkey]['image'] = 0;
                        }
                    }
                }

                if( isset( $option_fields['views'] ) ){
                    foreach ( $option_fields['views'] as $vkey => $view ){
                        $view_img = wp_get_attachment_url( $view['base'] );
                        if( $view_img ){
                            $mkey                    = 'views-' . $vkey . '-base';
                            $media_objects[$mkey]    = $view_img;
                        } else {
                            $option_fields['views'][$vkey]['base'] = 0;
                        }
                    }
                }

                $options['fields']          = serialize( $option_fields );
                $options['media_objects']   = serialize( $media_objects );

                file_put_contents( $print_options_path, serialize( $options ) );
                $result['print_options']    = NBDESIGNER_DATA_URL . '/export/' . $product_id . '/print_options.txt';
                $demo_data['p'. $product_id]['print_options'] = $result['print_options'];
            }

            $templates = $this->get_templates( $product_id );
            if( count( $templates ) ){
                file_put_contents( $templates_path, json_encode( $templates ) );
                $result['templates']        = NBDESIGNER_DATA_URL . '/export/' . $product_id . '/templates.json';
                $demo_data['p'. $product_id]['templates'] = $result['templates'];
            }

            file_put_contents( $demo_data_path, json_encode( $demo_data ) );

            wp_send_json( $result );
        }
        function get_product_image( $product ){
            $image_id  = $product->get_image_id();
            $image_url = wp_get_attachment_image_url( $image_id, 'full' );
            return $image_url;
        }
        public function nbd_import_product(){
            $result     = array(
                'flag'          => 1,
                'total_steps'   => 1,
                'current_step'  => 1,
                'error_mgs'     => ''
            );
            $status     = array();
            $product_id = absint( $_POST['product_id'] );
            $step       = absint( $_POST['step'] );


            $demo_data_path         = NBDESIGNER_PLUGIN_DIR . 'data/demo_datas.json';
            $demo_datas             = json_decode( file_get_contents( $demo_data_path ), true );

            $import_path            = NBDESIGNER_DATA_DIR . '/import/';
            $product_import_path    = $import_path . $product_id . '/';
            $settings_path          = $product_import_path . 'settings.txt';
            $print_options_path     = $product_import_path . 'print_options.txt';
            $templates_path         = $product_import_path . 'templates.json';
            $status_path            = $product_import_path . 'status.json';

            if( !file_exists( $import_path ) ){
                wp_mkdir_p( $import_path );
            }
            if( !file_exists( $product_import_path ) ){
                wp_mkdir_p( $product_import_path );
            }

            if( $step == 1 ){
                if( file_exists( $status_path ) ){
                    $status         = json_decode( file_get_contents( $status_path ), true );
                    $new_product_id = $status['new_product_id'];
                    $step           = $status['current_step'];
                    $settings_str   = file_get_contents( $settings_path );
                    $data           = unserialize( $settings_str );
                } else {
                    $settings_str               = nbd_file_get_contents( $demo_datas[ 'p' . $product_id ]['settings'] );
                    file_put_contents( $settings_path, $settings_str );
                    $data                       = unserialize( $settings_str );
                    $new_product_id             = $this->add_product( $data );
                    $status['new_product_id']   = $new_product_id;
                    $status['current_step']     = 1;
                }
            } else {
                $status                 = json_decode( file_get_contents( $status_path ), true );
                $new_product_id         = $status['new_product_id'];
                $settings_str           = file_get_contents( $settings_path );
                $data                   = unserialize( $settings_str );
            }

            if( $result['flag'] ){
                if( $data['nbo_enable'] && isset( $demo_datas[ 'p' . $product_id ]['print_options'] ) ){
                    if( $step == 1 ){
                        $print_options_str  = nbd_file_get_contents( $demo_datas[ 'p' . $product_id ]['print_options'] );
                        file_put_contents( $print_options_path, $print_options_str );
                    }else{
                        if( file_exists( $print_options_path ) ){
                            $print_options_str  = file_get_contents( $print_options_path );
                        } else {
                            $result['flag']         = 0;
                            $result['error_mgs']    = 'error: get print options';
                        }
                    }
                    $print_options_data = unserialize( $print_options_str );
                    if( $step > 1 ){
                        if( isset( $status['pot_step'] ) && ( $status['pot_step'] + 1 ) >= $step  ){
                            $this->create_or_update_print_option( $product_id, $new_product_id, $print_options_data );
                        }
                    } else {
                        $status['pot_step'] = count( unserialize( $print_options_data['media_objects'] ) ) + 1;
                    }
                } else {
                    $status['pot_step'] = 0;
                }

                if( $result['flag'] ) $result['total_steps'] += $status['pot_step'];
            }

            if( $result['flag'] ){
                if( isset( $demo_datas[ 'p' . $product_id ]['templates'] ) ){
                    if( $step == 1 ){
                        $templates_str      = nbd_file_get_contents( $demo_datas[ 'p' . $product_id ]['templates'] );
                        file_put_contents( $templates_path, $templates_str );
                    }else{
                        if( file_exists( $templates_path ) ){
                            $templates_str  = file_get_contents( $templates_path );
                        } else {
                            $result['flag']         = 0;
                            $result['error_mgs']    = 'error: get templates';
                        }
                    }

                    if( $result['flag'] ){
                        $templates = json_decode( $templates_str, true );

                        if( $step > 1 ){
                            $pot_step = isset( $status['pot_step'] ) ? $status['pot_step'] : 0;
                            if( $step > ( $pot_step + 1 ) ){
                                $this->add_templates( $templates, $product_id, $new_product_id );
                            }
                        }
                        if( $result['flag'] ) {
                            if( $step == 1 ) $status['tem_step'] = count( $templates );
                            $result['total_steps'] += $status['tem_step'];
                        }
                    }
                }
            }

            $status['total_steps']  = $result['total_steps'];
            $status['current_step'] = $step;
            $result['current_step'] = $step;
            file_put_contents( $status_path, json_encode( $status ) );

            wp_send_json( $result );
        }
        function add_product( $data ){
            $product    = new WC_Product();

            $product->set_name( $data['name'] );
            $product->set_description( $data['description'] );
            $product->set_regular_price( $data['regular_price'] );
            $product->set_sale_price( $data['sale_price'] );
            $product->set_status( "publish" );
            $product->set_catalog_visibility( "visible" );
            $product->set_stock_status( "instock" );

            if( $data['image'] ){
                $media_id = nbd_add_attachment( $data['image'] );
                if( $media_id ){
                    $product->set_image_id( $media_id );
                }
            }

            $product_id = $product->save();

            update_post_meta( $product_id, '_nbdesigner_enable', $data['enable_design'] );
            update_post_meta( $product_id, '_nbdesigner_enable_upload', $data['enable_upload'] );
            update_post_meta( $product_id, '_nbdesigner_enable_upload_without_design', $data['upload_without_design'] );
            update_post_meta( $product_id, '_nbo_enable', $data['nbo_enable'] );

            if( $data['setting_upload'] ){
                update_post_meta( $product_id, '_nbdesigner_upload', $data['setting_upload'] );
            }

            if( $data['option'] ){
                update_post_meta( $product_id, '_nbdesigner_option', $data['option'] );
            }

            if( $data['setting_design'] ){
                $product_config = unserialize( $data['setting_design'] );
                $default_bg_id  = get_option('nbdesigner_default_background');
                $default_ov_id  = get_option('nbdesigner_default_overlay');
                foreach ( $product_config as $key => $_config ){
                    $im_id = nbd_add_attachment( $_config['img_src'] );
                    $product_config[$key]['img_src'] = $im_id ? $im_id : $default_bg_id;

                    $ov_id = nbd_add_attachment( $_config['img_overlay'] );
                    $product_config[$key]['img_overlay'] = $ov_id ? $ov_id : $default_ov_id;
                }

                $setting_design = serialize( $product_config );
                update_post_meta( $product_id, '_designer_setting', $setting_design );
            }

            return $product_id;
        }
        function create_or_update_print_option( $product_id, $new_product_id, $data ){
            $print_options_path = NBDESIGNER_DATA_DIR . '/import/' . $product_id . '/print_options.txt';
            $media_objects      = unserialize( $data['media_objects'] );
            if( count( $media_objects ) ){
                $media          = array_splice( $media_objects, 0, 1 );
                $key            = array_key_first( $media );
                $key_arr        = explode( '-', $key );
                $url            = $media[ $key ];
                $uploaded_id    = nbd_add_attachment( $url );
                $option_fields  = unserialize( $data['fields'] );

                $reference      = &$option_fields;
                foreach( $key_arr as $k ) {
                    if ( !array_key_exists( $k, $reference ) ) {
                        $reference[$k] = [];
                    }
                    $reference = &$reference[$k];
                }
                $reference      = $uploaded_id;
                unset( $reference );

                $data['fields']         = serialize( $option_fields );
                $data['media_objects']  = serialize( $media_objects );
                file_put_contents( $print_options_path, serialize( $data ) );
            } else {
                if( isset( $data['media_objects'] ) ){
                    $this->save_print_option( $product_id, $new_product_id, $data );
                }
            }
        }
        public function save_print_option( $product_id, $new_product_id, $data ){
            global $wpdb;

            unset( $data['media_objects'] );
            unset( $data['id'] );
            $date                   = new DateTime();
            $data['modified']       = $date->format( 'Y-m-d H:i:s' );
            $data['created']        = $date->format( 'Y-m-d H:i:s' );
            $data['created_by']     = wp_get_current_user()->ID;
            $data['product_cats']   = serialize( array() );
            $data['product_ids']    = serialize(array( $new_product_id ));

            $wpdb->insert( "{$wpdb->prefix}nbdesigner_options", $data );
            $option_id              = $wpdb->insert_id;
            set_transient( 'nbo_product_' . $new_product_id , $option_id );
        }
        private function get_templates( $product_id ){
            global $wpdb;
            $templates  = array();
            $tems       = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}nbdesigner_templates WHERE product_id = {$product_id} AND publish = 1 AND ( ISNULL(type) OR type != 'solid' )", 'ARRAY_A' );
            
            foreach ( $tems as $tem ){
                unset( $tem['id'] );
                unset( $tem['tags'] );
                unset( $tem['colors'] );
                unset( $tem['thumbnail'] );

                $design_path        = NBDESIGNER_CUSTOMER_DIR . '/' . $tem['folder'];
                $design_zip_path    = NBDESIGNER_DATA_DIR . '/export/' . $product_id . '/' . $tem['folder'] . '.zip';
                $tem['temp_url']    = NBDESIGNER_DATA_URL . '/export/' . $product_id . '/' . $tem['folder'] . '.zip';
                if( $this->zip_folder( $design_path, $design_zip_path ) ){
                    $templates[]    = $tem;
                }
            }

            return $templates;
        }
        function zip_folder( $source, $destination ){
            if ( !extension_loaded( 'zip' ) || !file_exists( $source ) ) {
                return false;
            }

            $zip = new ZipArchive();
            if ( !$zip->open( $destination, ZIPARCHIVE::CREATE ) ) {
                return false;
            }

            $source = str_replace( '\\', '/', realpath( $source ) );

            if ( is_dir($source) === true ){
                $files = new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $source ), RecursiveIteratorIterator::SELF_FIRST );

                foreach ( $files as $file ) {
                    $file = str_replace( '\\', '/', realpath( $file ) );

                    if( in_array( substr( $file, strrpos( $file, '/' ) + 1 ), array( '.', '..' ) ) || $file == str_replace( '\\', '/', realpath( NBDESIGNER_CUSTOMER_DIR ) ) )
                        continue;

                    if ( is_dir( $file ) === true ) {
                        $zip->addEmptyDir( str_replace( $source . '/', '', $file . '/' ) );
                    } else if ( is_file( $file ) === true ) {
                        $zip->addFile( $file, str_replace( $source . '/', '', $file ) );
                    }
                }
            } else if( is_file( $source ) === true ) {
                $zip->addFile( $source, basename( $source ) );
            }

            return $zip->close();
        }
        function add_templates( $templates, $product_id, $new_product_id ){
            global $wpdb;
            if ( !extension_loaded( 'zip' ) ) {
                return false;
            }

            $tems           = array_splice( $templates, 0, 1 );
            if( isset( $tems[0] ) ){
                $tem        = $tems[0];
                $temp_name  = substr( md5( uniqid() ), 0, 5 ) . rand( 1, 100 ) . time();
                $temp_path  = NBDESIGNER_DATA_DIR . '/import/' . $product_id . '/' . $tem['folder'] . '.zip';
                $temp_dir   = NBDESIGNER_CUSTOMER_DIR . '/' . $temp_name;
                nbd_download_remote_file( $tem['temp_url'], $temp_path );

                $zip = new ZipArchive();
                if ( !$zip->open( $temp_path, ZIPARCHIVE::CREATE ) ) {
                    return false;
                }

                $zip->extractTo( $temp_dir );
                $zip->close();

                unset( $tem['temp_url'] );
                $tem['product_id']      = $new_product_id;
                $tem['variation_id']    = 0;
                $tem['folder']          = $temp_name;
                $user_id                = wp_get_current_user()->ID;
                $tem['user_id']         = $user_id;
                $date                   = new DateTime();
                $tem['created_date']    = $date->format( 'Y-m-d H:i:s' );

                $wpdb->insert( "{$wpdb->prefix}nbdesigner_templates", $tem );

                $templates_path = NBDESIGNER_DATA_DIR . '/import/' . $product_id . '/templates.json';
                file_put_contents( $templates_path, json_encode( $templates ) );
            }
            return true;
        }
    }
}
$nbd_import_export_product = NBD_IMPORT_EXPORT_PRODUCT::instance();
$nbd_import_export_product->init();