<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_input_type">'; ?>
    <div class="nbd-field-info" ng-show="check_depend(field.general, field.general.input_type)">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Input type', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <select name="options[fields][{{fieldIndex}}][general][input_type]" ng-model="field.general.input_type.value" >
                    <option ng-repeat="op in field.general.input_type.options" value="{{op.key}}">{{op.text}}</option>
                </select>
            </div>
        </div>
    </div>
<?php echo '</script>';