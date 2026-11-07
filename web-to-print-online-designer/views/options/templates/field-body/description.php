<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_description">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Description', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <textarea name="options[fields][{{fieldIndex}}][general][description]" ng-model="field.general.description.value"></textarea>
            </div>
        </div>
    </div>
<?php echo '</script>';