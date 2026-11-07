<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="nbdq-add-a-quote">
    <button data-id="<?php echo $product->get_id(); ?>" class="nbdq-add-a-quote-button button alt" id="nbdq-quote-btn"><span><?php _e( 'Add a quote', 'web-to-print-online-designer' ); ?></span></button>
</div>
<?php if ( nbdesigner_get_option( 'nbdesigner_quote_hide_add_to_cart', 'no' ) == 'yes'): ?>
<style type="text/css">
    .single_variation_wrap .variations_button button{
        display:none!important;
    }
    .cart button.single_add_to_cart_button{
        display:none!important;
    }
    .single-product div.product form.cart .quantity {
        float: none;
    }
</style>
<?php endif;