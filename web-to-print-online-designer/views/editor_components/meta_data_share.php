<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<?php
    $folder     = wc_clean( $_GET['nbd_share_id'] );
    $path       = NBDESIGNER_CUSTOMER_DIR . '/' . $folder . '/preview';
    $images     = Nbdesigner_IO::get_list_images( $path, 1 );
    $images     = nbd_sort_file_by_side( $images );
    $product    = wc_get_product( $variation_id ? $variation_id : $product_id );
    if( count( $images ) ){
        $image_url = Nbdesigner_IO::wp_convert_path_to_url( reset( $images ) );
    }
    if( isset( $_GET['nbd_share_id'] ) && $_GET['nbd_share_id'] != '' ){
        $url = add_query_arg(
            array(
                't'                 => isset( $_GET['t'] ) ? $_GET['t'] : time(),
                'product_id'        => $product_id,
                'variation_id'      => $variation_id,
                'reference'         => $_GET['nbd_share_id'],
                'nbd_share_id'      => $_GET['nbd_share_id']),
            getUrlPageNBD('create'));
    }
?>
<meta property="og:locale" content="<?php echo( $lang_code ); ?>">
<meta property="og:type" content="article">
<meta property="og:title" content="<?php echo( $product->get_name() ); ?>">
<meta property="og:description" content="<?php echo get_bloginfo( 'description' ); ?>">
<meta property="og:url" content="<?php echo esc_url( $url ); ?>">
<meta property="og:site_name" content="<?php echo get_bloginfo( 'name' ); ?>">
<meta property="og:image" content="<?php echo esc_url( $image_url ); ?>" />
<meta property="og:image:width" content="500">
<meta property="og:image:height" content="400">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo( $product->get_name() ); ?>">
<meta name="twitter:description" content="<?php echo get_bloginfo( 'description' ); ?>">
<meta name="twitter:site" content="@<?php echo get_bloginfo( 'name' ); ?>">
<meta name="twitter:image" content="<?php echo esc_url( $image_url ); ?>" />