<?php
function get_list_files( $folder ) {
    $files = array();
    if ( $handle = opendir( $folder ) ) {
        while ( false !== ( $entry = readdir( $handle ) ) ) {
            if ( $entry != "." && $entry != ".." ) {
                $files[] = $folder . '/' . $entry;
            }
        }
        closedir($handle);
    }
    return $files;
}
function get_gallery( $images ){
    $gallery = array();
    ksort( $images );
    foreach($images as $key => $image){
        list( $width, $height ) = getimagesize( $image );
        $gallery[] = array(
            'src'   => basename( $image ),
            'width' => $width,
            'height' => $height,
            'title' => '',
            'sizes' => $width . 'x' . $height
        );
    }
    return $gallery;
}
$path = urldecode( base64_decode( $_GET['path'] ) );
$folder = $_GET['folder'];
$data = array(
    'flag' => 0
);
if( file_exists( $path . '/' . $folder ) ){
    $data['images'] = get_gallery( get_list_files( $path . '/' . $folder ) );
    $data['flag']   = 1;
}
header('Content-Type: application/json');
echo json_encode( $data );
die();