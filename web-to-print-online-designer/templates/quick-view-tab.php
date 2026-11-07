<?php
/**
 * NBO Quick view template
 */
if (!defined('ABSPATH')) {
    exit;
}
global $product, $post, $woocommerce;
do_action('nbo_quick_view_tab_before_single_product');
?>
<div class="woocommerce quick-view">
    <div class="product">
        <a class="quick-view-title" target="_blank" href="<?php echo get_permalink($product->get_id()); ?>" title="">
            <?php woocommerce_template_single_title(); ?>
        </a>
        <?php woocommerce_template_single_price(); ?>
        <?php woocommerce_template_single_excerpt(); ?>
        <div class="quick-view-content entry-summary">
            <?php woocommerce_template_single_add_to_cart(); ?>
        </div>
    </div>
</div>
