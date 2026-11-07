<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbd-quantity-pricing-table">
    <h2><?php _e('Pricing table', 'web-to-print-online-designer'); ?></h2>
    <div class="nbd-table-pricing-wrap">
        <table>
            <thead>
                <tr>
                    <th><label for="nbd_quantity"><b><?php _e('From', 'web-to-print-online-designer'); ?></b></label></th>
                    <th><label for="nbd_quantity"><b><?php _e('To', 'web-to-print-online-designer'); ?></b></label></th>
                    <th><label for="nbd_quantity"><b><?php _e('Price per unit', 'web-to-print-online-designer'); ?></b></label></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach( $prices['start'] as $key => $start ): ?>
                <tr>
                    <td><?php echo $start; ?></td>
                    <td><?php echo $prices['end'][$key]; ?></td>
                    <td><?php echo wc_price( $prices['price'][$key] ); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>  
    <div class="nbd-pricing-description">
        <?php 
        $pricing_description = nbdesigner_get_option('nbdesigner_quantity_pricing_description');
        if( $pricing_description != '' ): ?>
        <div><?php echo stripslashes( $pricing_description ); ?></div>
        <?php endif; ?>
    </div>
</div>

