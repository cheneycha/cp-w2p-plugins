<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
if( count( $templates ) ):
    $UrlPageNBD = getUrlPageNBD('create');
?>
    <div class="nbd-gallery-item-templates">
    <?php
        foreach ($templates as $key => $temp):
        $link_template = add_query_arg(array(
            'product_id'    => $temp['product_id'],
            'variation_id'  => $temp['variation_id'],
            'reference'     => $temp['folder']
        ), $UrlPageNBD);
    ?>
        <div class="template nbd-col-<?php echo( $atts['per_row'] ); ?>">
            <div class="main">
                <a href="<?php echo esc_url( $link_template ); ?>">
                    <img src="<?php echo esc_url( $temp['image'] ); ?>" class="nbdesigner-img"/>
                    <span><?php esc_html_e('Start design', 'web-to-print-online-designer'); ?></span>
                </a>
                <p><?php esc_html_e( $temp['title'] ); ?></p>
            </div>
        </div>
    <?php endforeach;?>
    </div>
    <?php

else:
    esc_html_e('No template', 'web-to-print-online-designer');
endif;
