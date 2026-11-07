<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_title">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Option name', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <input required type="text" name="options[fields][{{fieldIndex}}][general][title]" ng-model="field.general.title.value">
            </div>
        </div>
    </div>
<?php echo '</script>';