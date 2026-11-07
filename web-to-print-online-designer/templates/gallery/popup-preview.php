<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<p class="nbd-popup-design-name">
    <?php esc_html_e( $name ); ?>
    &nbsp;<span class="nbd-popup-design-by"><i><?php esc_html_e('design by', 'web-to-print-online-designer'); ?></i> <a href="<?php echo esc_url( $store_url ); ?>"><b><?php echo $designer_name; ?></b></a></span>
</p>
<div class="nbd-popup-large-img">
    <img src="<?php echo esc_url( $large_image ); ?>" id="nbd-popup-large-preview"/>
</div>
<div class="nbd-popup-actions <?php echo $type != 'solid' ? '' : 'solid';?>">
    <?php if( $type != 'solid' ): ?>
    <a class="more-about-link" href="<?php echo esc_url( $link_detail_design ); ?>"><?php esc_html_e( 'More about this design', 'web-to-print-online-designer' ); ?></a>
    <?php endif; ?>
    <a class="nbd-popup-start-design" href="<?php echo esc_url( $link_start_design ); ?>"><?php esc_html_e( 'Use this design', 'web-to-print-online-designer' ); ?></a>
</div>
<div class="nbd-popup-list-preview">
    <?php foreach ( $images as $image ): ?>
    <img class="nbd-popup-list-preview-img" src="<?php echo esc_url( $image ); ?>" onclick="changePreviewImage(this)"/>
    <?php endforeach; ?>
</div>