<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div class="nbd-sidebar-con-inner nbd-popup-list-product <?php if( count($products) > 19 ) echo 'has-scroll'; ?>">
    <ul>
    <?php foreach($products as $product): ?>
        <li class="nbd-tem-list-product"><a href="javascript:void(0)" onclick="previewNBDProduct(<?php echo( $product['product_id'] ); ?>)" ><span><?php esc_html_e( $product['name'] ); ?></span></a></li>
    <?php endforeach; ?>
    </ul>
</div>