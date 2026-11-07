<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_published">'; ?>
    <div class="nbd-field-info" ng-show="check_depend(field.general, field.general.published)">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Published', 'web-to-print-online-designer'); ?></b> <nbd-tip data-tip="<?php _e('Choose whether the option show in summary options or not.', 'web-to-print-online-designer'); ?>" ></nbd-tip></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <select name="options[fields][{{fieldIndex}}][general][published]" ng-model="field.general.published.value">
                    <option ng-repeat="op in field.general.published.options" value="{{op.key}}">{{op.text}}</option>
                </select>
            </div>
        </div>
    </div>
<?php echo '</script>';