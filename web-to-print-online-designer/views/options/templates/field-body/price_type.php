<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_price_type">'; ?>
    <div class="nbd-field-info" ng-show="check_depend(field.general, field.general.price_type)">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Price type', 'web-to-print-online-designer'); ?></b> <nbd-tip data-tip="<?php _e('Here you can choose how the price is calculated. Depending on the field there various types you can choose.', 'web-to-print-online-designer'); ?>" ></nbd-tip></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <select name="options[fields][{{fieldIndex}}][general][price_type]" ng-model="field.general.price_type.value">
                    <option ng-repeat="op in field.general.price_type.options" ng-if="check_option_depend(fieldIndex, op.depend)" value="{{op.key}}">{{op.text}}</option>
                </select>
            </div>
        </div>
    </div>
<?php echo '</script>';