<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
    $link_download = add_query_arg(array(
            'download_nbd_design_file'  => 1,
            'order_id'                  => $order_id,
            'order_item_id'             => $item_id,
            'nbd_item_key'              => $nbd_item_key,
            'nbu_item_key'              => $nbu_item_key
        ), site_url()); 
    $type = array();
    if( $nbd_item_key ){
        if( nbdesigner_get_option('nbdesigner_download_design_png') == 1 ) $type['png'] = 'PNG';
        if( nbdesigner_get_option('nbdesigner_download_design_svg') == 1 ) $type['svg'] = 'SVG';
        if( nbdesigner_get_option('nbdesigner_download_design_pdf') == 1 ) $type['pdf'] = 'PDF';
        if( is_available_imagick() ){
            //if( nbdesigner_get_option('nbdesigner_download_design_jpg') == 1 ) $type['jpg'] = 'JPG';
            if( nbdesigner_get_option('nbdesigner_download_design_jpg_cmyk') == 1 ) $type['jpg_cmyk'] = 'JPG';
        }
    }
    if( $nbu_item_key ){
        if( nbdesigner_get_option('nbdesigner_download_design_upload_file') == 1 ) $type['files'] = esc_html__('Upload files', 'web-to-print-online-designer');
    }
?>
<div style="display: none;" class="nbd-order-item-download-section" data-href="<?php echo esc_url( $link_download );?>" data-type='<?php echo json_encode($type); ?>' data-title="<?php esc_html_e('Download', 'web-to-print-online-designer'); ?>"></div>