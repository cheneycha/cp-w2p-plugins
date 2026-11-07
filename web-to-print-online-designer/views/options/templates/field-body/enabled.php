<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_enabled">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Enabled', 'web-to-print-online-designer'); ?></b> <nbd-tip data-tip="<?php _e('Choose whether the option is enabled or not.', 'web-to-print-online-designer'); ?>" ></nbd-tip></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <select name="options[fields][{{fieldIndex}}][general][enabled]" ng-model="field.general.enabled.value">
                    <option ng-repeat="op in field.general.enabled.options" value="{{op.key}}">{{op.text}}</option>
                </select>
            </div>
        </div>
    </div>
<?php echo '</script>';