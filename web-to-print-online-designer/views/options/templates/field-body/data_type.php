<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_data_type">'; ?>
    <div class="nbd-field-info" ng-show="check_depend(field.general, field.general.data_type)">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Data type', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <select name="options[fields][{{fieldIndex}}][general][data_type]" ng-model="field.general.data_type.value" ng-change="update_price_type(fieldIndex)" >
                    <option ng-repeat="op in field.general.data_type.options" value="{{op.key}}">{{op.text}}</option>
                </select>
            </div>
        </div>
    </div>
<?php echo '</script>';