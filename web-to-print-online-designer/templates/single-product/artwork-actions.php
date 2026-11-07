<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
get_header();
global $product, $post, $woocommerce, $wp_query;
$class = isset( $wp_query->query_vars['request-design'] ) ? 'nbd-request-design' : 'nbd-upload-design';
$title = isset( $wp_query->query_vars['request-design'] ) ? __('Request for design', 'web-to-print-online-designer') : __('Upload your design', 'web-to-print-online-designer');
?>
<div class="woocommerce <?php echo $class; ?>">
    <div class="product" id="product-<?php echo $post->ID; ?>">
        <div class="quick-view-image images">
            <div class="nbd-design-action-info">
                <?php woocommerce_template_single_title(); ?>
                <?php woocommerce_template_single_price(); ?>
                <?php woocommerce_template_single_excerpt(); ?>
            </div>
            <div class="woocommerce-product-gallery__image nbd-artwork-action-image">
                <?php if (has_post_thumbnail()) : ?>
                    <?php echo get_the_post_thumbnail($post->ID, apply_filters('single_product_large_thumbnail_size', 'shop_single')) ?>
                <?php else : ?>
                    <img src="<?php echo wc_placeholder_img_src(); ?>" alt="<?php _e('Placeholder', 'web-to-print-online-designer'); ?>" />
                <?php endif; ?>
            </div>
            <?php do_action('nbd_request_design_after_product_image'); ?>
        </div>
        <div class="quick-view-content entry-summary summary">
            <h3><?php echo $title; ?></h3>
            <?php woocommerce_template_single_add_to_cart(); ?>
        </div>
    </div>
</div>
<?php
get_footer();