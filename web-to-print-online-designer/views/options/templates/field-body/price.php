<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_price">'; ?>
    <div class="nbd-field-info" ng-show="check_depend(field.general, field.general.price)">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Additional Price', 'web-to-print-online-designer'); ?></b> <nbd-tip data-tip="<?php _e('Enter the price for this field or leave it blank for no price.', 'web-to-print-online-designer'); ?>" ></nbd-tip></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <input autocomplete="off" ng-click="initFormulaPrice(field.general.price.value, 0, fieldIndex)" class="nbd-short-ip" type="text" name="options[fields][{{fieldIndex}}][general][price]" ng-model="field.general.price.value">
            </div>
        </div>
    </div>
<?php echo '</script>';