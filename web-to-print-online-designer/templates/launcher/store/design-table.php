<?php
    if (!defined('ABSPATH')) exit;
?>
<?php if( count( $designs ) ): ?>
<?php if( isset( $nbdl_edit ) ): ?>
<div data-design-id="<?php echo $nbdl_edit; ?>" data-product-id="<?php echo $product_id; ?>" class="nbdl-table-wrapper">
<?php else: ?>
<div>
<?php endif; ?>
    <table class="shop_table shop_table_responsive my_account_orders">
        <thead>
            <tr>
                <th><?php esc_html_e('Preview', 'web-to-print-online-designer'); ?></th>
                <th><?php esc_html_e('Status', 'web-to-print-online-designer'); ?></th>
                <th><?php esc_html_e('Product', 'web-to-print-online-designer'); ?></th>
                <th><?php esc_html_e('Date', 'web-to-print-online-designer'); ?></th>
                <th><?php esc_html_e('Action', 'web-to-print-online-designer'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $designs as $design ) { ?>
                <tr class="order">
                    <td data-title="<?php esc_html_e('Preview', 'web-to-print-online-designer'); ?>">
                        <?php foreach( $design['previews'] as $preview ): ?>
                            <img src="<?php echo $preview; ?>" class="nbd-preview" />
                        <?php endforeach; ?>
                    </td>
                    <td data-title="<?php esc_html_e('Status', 'web-to-print-online-designer'); ?>">
                        <?php
                            if ( $design['status'] == 0 ) {
                                echo '<span class="label label-danger">' . esc_html__( 'Pending', 'web-to-print-online-designer' ) . '</span>';
                            } elseif ( $design['status'] == 1 ) {
                                echo '<span class="label label-warning">' . esc_html__( 'Approved', 'web-to-print-online-designer' ) . '</span>';
                            }
                        ?>
                    </td>
                    <td data-title="<?php esc_html_e('Date', 'web-to-print-online-designer'); ?>">
                        <a href="<?php echo get_permalink( $design['product']['product_id'] ); ?>"><?php echo esc_html( $design['product']['name'] ); ?></a>
                    </td>
                    <td data-title="<?php esc_html_e('Date', 'web-to-print-online-designer'); ?>"><?php echo esc_html( nbd_format_time( $design['date'] ) ); ?></td>
                    <td data-title="<?php esc_html_e('Action', 'web-to-print-online-designer'); ?>">
                        <?php 
                            $link_edit_design = $design['type'] == 'solid' ? '#' : add_query_arg(array(
                                'product_id'        => $design['product']['product_id'],
                                'nbd_item_key'      => $design['folder'],
                                'current_page'      => $current_page,
                                'task'              => 'edit',
                                'design_type'       => 'template',
                                'rd'                => 'my_store_design'
                            ), getUrlPageNBD('create'));
                        ?>
                        <a class="woocommerce-button button edit <?php echo $design['type'] == 'solid' ? 'nbdl-edit' : ''; ?>" data-design-id="<?php echo $design['id']; ?>" data-product-id="<?php echo $design['product']['product_id']; ?>" href="<?php echo $link_edit_design; ?>"><?php esc_html_e('Edit', 'web-to-print-online-designer'); ?></a>
                        <?php 
                            $delete_url = add_query_arg( array(
                                'tab'       => 'design',
                                'id'        => $design['id'],
                                'action'    => 'nbdl_delete_design'
                            ), wc_get_endpoint_url( 'my-store', '', wc_get_page_permalink( 'myaccount' ) ));
                            $delete_url = wp_nonce_url( $delete_url, 'nbdl_delete_design' );
                        ?>
                        <a class="woocommerce-button button nbdl-delete-design delete" href="<?php echo $delete_url; ?>"><?php esc_html_e('Delete', 'web-to-print-online-designer'); ?></a>
                        <?php 
                            $design_url = add_query_arg(array(
                                'design_id' => nbd_encode_design_id( $design['id'] )
                            ), get_permalink( $design['product']['product_id'] ) );
                        ?>
                        <a class="woocommerce-button button nbdl-delete-design delete" href="<?php echo $design_url; ?>"><?php esc_html_e('View', 'web-to-print-online-designer'); ?></a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <script>
        jQuery( document ).ready(function(){
            jQuery('.nbdl-delete-design').on('click', function () {
                return confirm('<?php esc_html_e('Are you sure?', 'web-to-print-online-designer'); ?>');
            });
        });
    </script>
</div>
<?php else: ?>
<div class="woocommerce-message woocommerce-message--info woocommerce-Message woocommerce-Message--info woocommerce-info">
    <?php esc_html_e( 'No design has been made yet.', 'web-to-print-online-designer' ); ?>
</div>
<?php endif;