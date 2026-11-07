<?php
if (!defined('ABSPATH')) exit;
echo sprintf( '<a rel="nofollow" href="#" data-quantity="%s" data-product_id="%s" class="%s %s">%s</a>',
    esc_attr( isset( $quantity ) ? $quantity : 1 ),
    $product_id,
    esc_attr( $need_qv ? 'button nbo_ajax_add_to_cart nbo_need_qv' : 'button nbo_ajax_add_to_cart' ),
    nbdesigner_get_option('nbdesigner_class_design_button_catalog'),
    $need_qv ? esc_html__('Select options', 'web-to-print-online-designer') : esc_html__('Add to cart', 'web-to-print-online-designer')
);