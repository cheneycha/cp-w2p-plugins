<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_upload_option">'; ?>
    <div class="nbd-field-info" ng-show="check_depend(field.general, field.general.upload_option)">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Upload file option', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <table class="nbd-table">
                    <tr>
                        <th><?php _e('Min size', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Max size', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Allow type', 'web-to-print-online-designer'); ?></th>
                    </tr>
                    <tr>
                        <td>
                            <input class="nbd-short-ip" type="text" string-to-number ng-model="field.general.upload_option.value.min_size" name="options[fields][{{fieldIndex}}][general][upload_option][min_size]"/> MB
                        </td>
                        <td>
                            <input class="nbd-short-ip" type="text" string-to-number ng-model="field.general.upload_option.value.max_size" name="options[fields][{{fieldIndex}}][general][upload_option][max_size]"/> MB
                        </td>
                        <td>
                            <input class="nbd-short-ip" type="text" ng-model="field.general.upload_option.value.allow_type" name="options[fields][{{fieldIndex}}][general][upload_option][allow_type]"/>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php echo '</script>';