<?php
    if (!defined('ABSPATH')) exit;
?>
<div class="nbdl-design-wrap">
    <a class="button nbdl-start-btn" id="nbdl-start-btn"><?php esc_html_e( 'Start Designing', 'web-to-print-online-designer' ); ?></a>
    <h2 class="nbdl-store-dashboard-head">
        <?php esc_html_e( 'Designs', 'web-to-print-online-designer' ); ?>
    </h2>
    <div>
        <?php 
            $data = array(
                'designs'       => $designs ? $designs : array(),
                'current_page'  => $current_page
            );

            if( isset( $_GET['edit'] ) && absint( $_GET['edit'] ) > 0 ){
                $design_id  = wc_clean( $_GET['edit'] );
                $design     = nbd_get_design( $design_id );
                if( !empty( $design ) ){
                    $data['nbdl_edit']  = $design_id;
                    $data['product_id'] = $design['product_id'];
                }
            }

            nbdesigner_render_template("launcher/store/design-table.php", $data);
        ?>
    </div>
    <div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination">
        <?php if( $prev_page != '' ): ?>
            <a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button" href="<?php echo $prev_page; ?>" >
                <?php esc_html_e( 'Previous', 'web-to-print-online-designer' ); ?>
            </a>
        <?php endif; ?>
        <?php if( $next_page != '' ): ?>
            <a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--next button" href="<?php echo $next_page; ?>" >
                <?php esc_html_e( 'Next', 'web-to-print-online-designer' ); ?>
            </a>
        <?php endif; ?>
    </div>
</div>