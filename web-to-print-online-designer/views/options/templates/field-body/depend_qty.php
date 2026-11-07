<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_depend_qty">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Depend quantity', 'web-to-print-online-designer'); ?></b> <nbd-tip data-tip="<?php _e('If choose No additional price will be apply as cart fee or fixed amount which independently with the quantity.', 'web-to-print-online-designer'); ?>" ></nbd-tip></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <select name="options[fields][{{fieldIndex}}][general][depend_qty]" ng-model="field.general.depend_qty.value">
                    <option ng-repeat="op in field.general.depend_qty.options" value="{{op.key}}">{{op.text}}</option>
                </select>
            </div>
        </div>
    </div>
<?php echo '</script>';