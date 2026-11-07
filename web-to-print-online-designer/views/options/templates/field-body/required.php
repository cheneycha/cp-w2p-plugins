<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_required">'; ?>
    <div class="nbd-field-info" ng-show="check_depend(field.general, field.general.required)">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Required', 'web-to-print-online-designer'); ?></b> <nbd-tip data-tip="<?php _e('Choose whether the option is required or not.', 'web-to-print-online-designer'); ?>" ></nbd-tip></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <select name="options[fields][{{fieldIndex}}][general][required]" ng-model="field.general.required.value">
                    <option ng-repeat="op in field.general.required.options" value="{{op.key}}">{{op.text}}</option>
                </select>
            </div>
        </div>
    </div>
<?php echo '</script>';