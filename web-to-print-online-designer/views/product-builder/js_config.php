<?php
$nbo_cart_item_key  = ( isset( $_GET['nbo_cart_item_key'] ) &&  $_GET['nbo_cart_item_key'] != '' ) ? $_GET['nbo_cart_item_key'] : '';
$oid                = ( isset( $_GET['oid'] ) && $_GET['oid'] != '' ) ? absint( $_GET['oid'] ) :  0;
$redirect_url       = ( isset( $_GET['rd'] ) && $_GET['rd'] != '' ) ? nbd_get_redirect_url( $_GET['rd'] ) :  '';
if( $is_creating_task == 0 ){
    $oid = $option_id;
}else if( $oid == 0 ){
    global $wp_query;
    $wp_query->set_404();
    status_header( 404 );
    get_template_part( 404 ); exit();
}
$fonts = array();
$custom_fonts = array();
if( file_exists( NBDESIGNER_DATA_DIR . '/fonts.json' ) ){
    $custom_fonts = (array)json_decode( file_get_contents( NBDESIGNER_DATA_DIR . '/fonts.json' ) );
}
$google_fonts = array();
if( file_exists( NBDESIGNER_DATA_DIR . '/googlefonts.json' ) ){
    $google_fonts = (array)json_decode( file_get_contents( NBDESIGNER_DATA_DIR . '/googlefonts.json' ) );
}
$fonts      = array_merge( $custom_fonts, $google_fonts );
$font_url   = NBDESIGNER_FONT_URL;
if ( function_exists( 'icl_get_home_url' ) ) {
    $icl_home_url = untrailingslashit( icl_get_home_url() );
    if ( class_exists( 'SitePress' ) ) {
        global $sitepress;
        if( $sitepress ){
            $wpml_language_negotiation_type = $sitepress->get_setting( 'language_negotiation_type' );
            if( $wpml_language_negotiation_type == 2 ){
                $is_wpml = 1;
                $font_url = str_replace( untrailingslashit( get_option( 'home' ) ), untrailingslashit( icl_get_home_url() ), $font_url );
            }
        }
    }
};
?>
<script type="text/javascript">
    var NBPBCONFIG = {
        is_mobile           : "<?php echo wp_is_mobile(); ?>",
        is_creating_task    : "<?php echo $is_creating_task;?>",
        assets_url          : "<?php echo NBDESIGNER_PLUGIN_URL . 'assets/'; ?>",
        plg_url             : "<?php echo NBDESIGNER_PLUGIN_URL; ?>",
        ajax_url            : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
        nonce               : "<?php echo wp_create_nonce( 'save-design' ); ?>",
        nbo_cart_item_key   : "<?php echo $nbo_cart_item_key; ?>",
        oid                 : "<?php echo $oid; ?>",
        redirect_url        : "<?php echo $redirect_url; ?>",
        custom_fonts        : <?php echo json_encode( $custom_fonts ); ?>,
        google_fonts        : <?php echo json_encode( $google_fonts ); ?>,
        pre_builder         : <?php echo json_encode( nbd_get_product_pre_builder( $oid, $nbo_cart_item_key ) ); ?>,
        fonts               : <?php echo json_encode( $fonts ); ?>,
        font_url            : "<?php echo $font_url; ?>",
        i18n                : <?php echo json_encode( array(
            'only_support_image'    => __( 'Only support image!', 'web-to-print-online-designer' ),
            'max_file_size'         => __( 'Max file size', 'web-to-print-online-designer' ),
            'min_file_size'         => __( 'Min file size', 'web-to-print-online-designer' ),
            'confirm_delete_image'  => __( 'Are you sure you want to delete this image?', 'web-to-print-online-designer' ),
            'confirm_delete_text'   => __( 'Are you sure you want to delete this text?', 'web-to-print-online-designer' ),
            'can_not_save_design'   => __( 'Oops! Design has not been saved!', 'web-to-print-online-designer' ),
            'choose'                => __( 'Choose', 'web-to-print-online-designer' ),
            'cancel'                => __( 'Cancel', 'web-to-print-online-designer' )
        ) ); ?>
    };
</script>