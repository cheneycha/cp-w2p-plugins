<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $_product->get_id() ); ?>" data-product_variations="<?php echo htmlspecialchars( wp_json_encode( $available_variations ) ) ?>">
    <table class="variations" cellspacing="0">
        <tbody>
            <?php foreach ($attributes as $attribute_name => $options) : ?>
                <tr>
                    <td class="label"><label for="<?php echo sanitize_title($attribute_name); ?>"><?php echo wc_attribute_label($attribute_name); ?></label></td>
                    <td class="value">
                        <?php
                        $selected = isset($_REQUEST['attribute_' . sanitize_title($attribute_name)]) ? wc_clean(stripslashes(urldecode($_REQUEST['attribute_' . sanitize_title($attribute_name)]))) : $_product->get_variation_default_attribute($attribute_name);
                        wc_dropdown_variation_attribute_options(array('options' => $options, 'attribute' => $attribute_name, 'product' => $_product, 'selected' => $selected));
                        echo end($attribute_keys) === $attribute_name ? apply_filters('woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . esc_html__('Clear', 'woocommerce') . '</a>') : '';
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <input type="hidden" name="variation_id" class="variation_id" value="0" />
    <div class="woocommerce-variation-price" style="display: none;"></div>
</form> 