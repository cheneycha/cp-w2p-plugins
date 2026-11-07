<?php if (!defined('ABSPATH')) exit; ?>
<tr class="nbd-option-field" id="nbo-quantity-option-wrap">
    <td>
        <label for='nbo-quantity'><?php _e('Quantity', 'web-to-print-online-designer'); ?></label>
    </td>
    <td class="nbd-field-content">
        <?php if($options['quantity_type'] == 'r'): ?>
        <input name="nbo-quantity" ng-change="change_quantity()" ng-model="quantity" type="range" <?php if($options['quantity_min'] != '') echo 'min="'.$options['quantity_min'].'" value="'.$options['quantity_min'].'"'; ?> <?php if($options['quantity_max'] != '') echo 'max="'.$options['quantity_max'].'"'; ?> <?php if($options['quantity_step'] != '') echo 'step="'.$options['quantity_step'].'"'; ?>/>
        <span class="nbd-input-range">{{quantity}}</span>
        <?php elseif($options['quantity_type'] == 'd'): ?>
        <select name="nbo-quantity" convert-to-number class="nbo-dropdown" ng-change="change_quantity()" ng-model="quantity">
            <?php 
                $discount_type = $options['quantity_discount_type'] == 'p' ? '%' : '/' . __('1 item', 'web-to-print-online-designer');
                foreach($options['quantity_breaks'] as $break): 
                $discount = '';
                if($break['dis'] != ''){
                    $break['dis'] = $options['quantity_discount_type'] == 'p' ? $break['dis'] : wc_price($break['dis']);
                    $discount = ' ( -'.$break['dis'].$discount_type . ' )';
                }
            ?>
            <option value="<?php echo $break['val']; ?>"><?php echo $break['val']; ?><?php echo $discount; ?></option>
            <?php endforeach; ?>
        </select>
        <?php elseif( $options['quantity_type'] == 's' ): ?>
        <div class="nbd-label-wrap">
        <?php 
            foreach($options['quantity_breaks'] as $key => $break): 
        ?>
            <input convert-to-number ng-change="change_quantity()" ng-model="quantity" name="nbo-quantity" type="radio" value="<?php echo $break['val']; ?>" id="nbo-quantity-<?php echo $key; ?>"/>
            <label class="nbd-label" for='nbo-quantity-<?php echo $key; ?>' style="font-size: 1em; border-radius: 4px;">
                <?php echo $break['val']; ?>
            </label>
        
        <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="nbd-radio">
        <?php 
            foreach($options['quantity_breaks'] as $key => $break): 
        ?>
            <input convert-to-number ng-change="change_quantity()" ng-model="quantity" name="nbo-quantity" type="radio" value="<?php echo $break['val']; ?>" id="nbo-quantity-<?php echo $key; ?>"/>
            <label for='nbo-quantity-<?php echo $key; ?>' style="font-size: 1em; border-radius: 4px; display: block;margin: 0px 4px 5px!important;" ng-class="custom_qty.enable ? 'nbo-hidden-checked' : ''" ng-click="disable_custom_qty()">
                <?php echo $break['val']; ?>
            </label>
        <?php endforeach; ?>
            <input type="radio" ng-model="custom_qty.enable" value="true" id="nbo-custom-quantity"/>
            <label for='nbo-custom-quantity' style="font-size: 1em; border-radius: 4px; display: block;margin: 0px 4px 5px!important;">
                <?php _e('Custom quantity', 'web-to-print-online-designer'); ?>
            </label>
            <div ng-if="custom_qty.enable">
                <input ng-model="custom_qty.value" type="number" step="1" min="1" ng-change="_change_quantity()" />
            </div>
        </div>
        <?php endif; ?>
    </td>
</tr>