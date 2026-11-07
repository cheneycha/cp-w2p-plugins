<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div class="nbd-preview-product-head">
    <p>
        <a href="javascript:void(0)" onclick="showPopupCreateTemplate()" class="nbd-back-to-list-pp-products" title="<?php esc_html_e('Back to list', 'web-to-print-online-designer'); ?>">
            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                <title><?php esc_html_e('Back to list', 'web-to-print-online-designer'); ?></title>
                <path fill="#6d6d6d" d="M21 11.016v1.969h-14.156l3.563 3.609-1.406 1.406-6-6 6-6 1.406 1.406-3.563 3.609h14.156z"></path>
            </svg>
        </a>&nbsp;&nbsp;<?php esc_html_e( $name ); ?>
    </p>
    <img src="<?php echo esc_url( $image ); ?>"/>
</div>
<?php if($type == 'variable'): ?>
<div class="nbd-preview-product-variation">
    <?php if( count($variations) ): ?>
    <label><?php esc_html_e('Choose variation', 'web-to-print-online-designer'); ?></label>
    <select class="nbd-select" onchange="switchNBDProductVariation(this)">
        <?php foreach( $variations as $variation ): ?>
        <option value="<?php echo( $variation['id'] ); ?>"><?php esc_html_e( $variation['name'] ); ?></option>
        <?php endforeach; ?>
    </select>
    <?php 
        else:
            $variations[0]['id'] = 0;
        endif; ?>
</div>
<?php endif; ?>
<div class="nbd-preview-product-action">
    <a class="nbd-popup-start-design" id="nbd-popup-link-create-template" href="<?php echo esc_url( $link_create_template ); ?><?php if($type == 'variable') echo '&variation_id='.$variations[0]['id']; ?>" data-href="<?php echo esc_url( $link_create_template ); ?>"><?php esc_html_e('Create template', 'web-to-print-online-designer'); ?></a>
</div>