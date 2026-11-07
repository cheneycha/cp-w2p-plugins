<?php
/**
 * NBO Quick view template
 */
if (!defined('ABSPATH')) {
    exit;
}
global $product, $post, $woocommerce;
do_action('nbo_quick_view_before_single_product');
?>
<div class="woocommerce quick-view">
    <div class="product" id="product-<?php echo $post->ID; ?>">
        <div class="quick-view-image images">
            <div class="woocommerce-product-gallery__image">
                <?php if (has_post_thumbnail()) : ?>
                    <?php echo get_the_post_thumbnail($post->ID, apply_filters('single_product_large_thumbnail_size', 'shop_single')) ?>
                <?php else : ?>
                    <img src="<?php echo wc_placeholder_img_src(); ?>" alt="<?php _e('Placeholder', 'web-to-print-online-designer'); ?>" />
                <?php endif; ?>
            </div>
            <a class="quick-view-detail-button button" target="_blank" href="<?php echo get_permalink($product->get_id()); ?>"><?php _e('View Full Details', 'web-to-print-online-designer'); ?></a>
        </div>
        <div class="quick-view-content entry-summary summary">
            <?php woocommerce_template_single_title(); ?>
            <?php woocommerce_template_single_price(); ?>
            <?php woocommerce_template_single_excerpt(); ?>
            <?php woocommerce_template_single_add_to_cart(); ?>
            <hr />
            <?php woocommerce_template_single_meta(); ?>
        </div>
    </div>
</div>