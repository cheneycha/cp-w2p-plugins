<?php
function cod_zip_files( $file_names, $archive_file_name, $nameZip, $option_name = array() ) {
    if(file_exists($archive_file_name)) {
        unlink($archive_file_name);
    }

    if ( class_exists( 'ZipArchive' ) ) {
        $zip = new ZipArchive();
        if ( $zip->open( $archive_file_name, ZIPARCHIVE::CREATE ) !== TRUE ) {
          exit( "cannot open <$archive_file_name>\n" );
        }

        if ( !empty($file_names) && is_array($file_names) ) {
            foreach( $file_names as $key => $file ) {
                if( !empty( $option_name ) && is_array( $option_name ) ){
                    $file_name  = pathinfo( $file, PATHINFO_FILENAME );
                    $file_ext   = pathinfo( $file, PATHINFO_EXTENSION );
                    foreach ( $option_name as $key => $val ){
                        if( is_int( strpos( $file_name, $key ) ) ) $file_name = $val;
                    }
                    $name = $file_name .'.'. $file_ext;
                }else{
                    $path_arr = explode( '/', str_replace( '\\', '/', $file ) );
                    $cnt = count($path_arr);
                    $name = ( $cnt >= 2 ? $path_arr[$cnt - 2] . '_' : '' ) . basename( $file );
                }
                $zip->addFile( $file, $name );
            }
        }
        $zip->close();
    }
}

function cod_extra_settings () {
    return [
        [
            'title' => 'Auto generation',
            'id' => 'nbdesigner_codgf_enable_auto',
            'default' => 'off',
            'type' => 'radio',
            'options' => array(
                'off' => 'Off',
                'on' => 'On'
            )
        ],
        [
            'title' => 'PNG generate',
            'id' => 'nbdesigner_codgf_enable_png',
            'default' => 'off',
            'type' => 'radio',
            'options' => array(
                'off' => 'Off',
                'on' => 'On'
            )
        ],
        [
            'title' => 'JPG generate',
            'id' => 'nbdesigner_codgf_enable_jpg',
            'default' => 'off',
            'type' => 'radio',
            'options' => array(
                'off' => 'Off',
                'on' => 'On'
            )
        ],
        array(
            'title'         => 'Set file generation time',
            'id'            => 'nbdesigner_codgf_time_generate',
            'description'   => 'minute',
            'default'       => '',
            'type'          => 'number',
            'input_attrs'   => array(
                'min'   => 5,
                'max'   => 4,
                'step'  => 1,
            ),
        ),
        array(
            'title'         => 'Set Y(min) to auto clean up files after generating or regenerating. Set 0 means never.',
            'id'            => 'nbdesigner_codgf_time_clear',
            'description'   => 'Minutes',
            'default'       => 0,
            'type'          => 'number',
            'input_attrs'   => array(
                'min'   => 5,
                'max'   => 4,
                'step'  => 1,
            ),
        ),
    ];
}

function cod_time() {
    $timezone = get_option('gmt_offset');
    return time() + 3600*($timezone+date("0"));
}

function cod_delete_queue() {
    global $wpdb;
}