<?php if (!defined('ABSPATH')) { exit; }; ?>
<h3><?php esc_html_e('Select a product', 'web-to-print-online-designer'); ?></h3>
<div class="studio-widget-product-wrap">
    <?php foreach( $products as $product ): ?>
    <div class="studio-widget-product" data-id="<?php echo( $product['id'] ); ?>">
        <div class="studio-widget-product-inner">
            <div class="studio-widget-img-wrap">
                <img class="product-img" src="<?php echo esc_url( $product['src'] ); ?>" alt="<?php echo( $product['name'] ); ?>" />
            </div>
            <a href="<?php echo esc_url( $product['url'] ); ?>" target="_blank"><?php echo( $product['name'] ); ?></a>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<div class="studio-widget-templates-wrap">
    <div class="loading-wrap">
        <div class="loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"></circle>
            </svg>
        </div>
    </div>
    <div class="studio-product-templates-inner">
        <h3><?php esc_html_e('Select a template', 'web-to-print-online-designer'); ?></h3>
        <div>
            <p class="no-template"><?php esc_html_e('No template', 'web-to-print-online-designer'); ?></p>
            <div class="studio-widget-templates"></div>
        </div>
    </div>
</div>