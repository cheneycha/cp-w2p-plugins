<?php
if ( !defined( 'ABSPATH' ) ) exit;
$product_id     = $product->get_id();
$product_id     = get_wpml_original_id( $product_id );
$url            = esc_url( get_permalink( $product->get_id() ) );
$layout         = nbd_get_product_layout( $product_id );
$upload_design  = get_post_meta( $product_id, '_nbdesigner_enable_upload', true );
$without_design = get_post_meta( $product_id, '_nbdesigner_enable_upload_without_design', true );
$class          = isset( $class ) ? $class : 'button';
if( $upload_design && !$without_design ){
    $class      = apply_filters( 'nbd_loop_start_design_btn_class', $class );
}

if( $layout != 'v' ){
    $url = add_query_arg( array(
        'product_id'    => $product_id,
        'view'          => $layout
    ),  getUrlPageNBD( 'create' ) );
}

$option         = unserialize( get_post_meta( $product_id, '_nbdesigner_upload', true ) );
$upload_style   = nbdesigner_get_option( 'nbdesigner_upload_popup_style', 's' );
if( $upload_style == 'a' && isset( $option['advanced_upload'] ) && $option['advanced_upload'] == 1 ){
    $class .= ' nbau';
}

if( $without_design ){
    $label          = esc_html__( 'Upload design', 'web-to-print-online-designer' );
    if( $upload_style == 'a' && isset( $option['advanced_upload'] ) && $option['advanced_upload'] == 1 ){
        $url = add_query_arg( array(
            'product_id'    =>  $product_id
        ),  getUrlPageNBD( 'advanced_upload' ) );
    }else{
        $url = add_query_arg( array(
            'product_id'    => $product_id
        ),  getUrlPageNBD( 'simple_upload' ) );
    }
}

echo sprintf( '<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" data-layout="%s" class="%s %s">%s</a>',
    $url,
    esc_attr( isset( $quantity ) ? $quantity : 1 ),
    esc_attr( $product->get_id() ),
    esc_attr( $product->get_sku() ),
    esc_attr( $layout ),
    esc_attr( $class ),
    nbdesigner_get_option( 'nbdesigner_class_design_button_catalog' ),
    esc_html( $label )
);