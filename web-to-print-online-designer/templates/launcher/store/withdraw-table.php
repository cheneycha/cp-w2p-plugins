<?php
    if (!defined('ABSPATH')) exit;
?>
<div>
    <table class="shop_table shop_table_responsive my_account_orders">
        <thead>
            <tr>
                <th><?php esc_html_e('Amount', 'web-to-print-online-designer'); ?></th>
                <th><?php esc_html_e('Status', 'web-to-print-online-designer'); ?></th>
                <th><?php esc_html_e('Date ', 'web-to-print-online-designer'); ?></th>
                <th><?php esc_html_e('Action ', 'web-to-print-online-designer'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $requests as $request ) { ?>
            <tr class="order">
                <td data-title="<?php esc_html_e('Amount', 'web-to-print-online-designer'); ?>"><?php echo wc_price( $request->amount ); ?></td>
                <td data-title="<?php esc_html_e('Status', 'web-to-print-online-designer'); ?>">
                    <?php
                        if ( $request->status == 0 ) {
                            echo '<span class="label label-danger">' . esc_html__( 'Pending', 'web-to-print-online-designer' ) . '</span>';
                        } elseif ( $request->status == 1 ) {
                            echo '<span class="label label-warning">' . esc_html__( 'Approved', 'web-to-print-online-designer' ) . '</span>';
                        } else {
                            echo '<span class="label label-warning">' . esc_html__( 'Cancelled', 'web-to-print-online-designer' ) . '</span>';
                        }
                    ?>
                </td>
                <td data-title="<?php esc_html_e('Date', 'web-to-print-online-designer'); ?>"><?php echo esc_html( nbd_format_time( $request->date ) ); ?></td>
                <td data-title="<?php esc_html_e('Action', 'web-to-print-online-designer'); ?>">
                    <?php 
                        if( $request->status == 0 ):
                            $cancel_url = add_query_arg( array(
                                'tab'       => 'withdraw',
                                'id'        => $request->id,
                                'action'    => 'nbdl_cancel_withdrow'
                            ), wc_get_endpoint_url( 'my-store', '', wc_get_page_permalink( 'myaccount' ) ));
                            $cancel_url = wp_nonce_url( $cancel_url, 'nbdl_cancel_withdrow' );
                    ?>
                    <a href="<?php echo esc_url( $cancel_url ); ?>"><?php esc_html_e( 'Cancel', 'web-to-print-online-designer' ); ?></a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>