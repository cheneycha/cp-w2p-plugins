<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_placeholder">'; ?>
    <div class="nbd-field-info" ng-show="check_depend(field.general, field.general.text_option)">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Placeholder', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <input type="text" name="options[fields][{{fieldIndex}}][general][placeholder]" ng-model="field.general.placeholder.value">
            </div>
        </div>
    </div>
<?php echo '</script>';