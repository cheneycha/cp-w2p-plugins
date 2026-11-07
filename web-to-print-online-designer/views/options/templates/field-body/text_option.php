<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_text_option">'; ?>
    <div class="nbd-field-info" ng-show="check_depend(field.general, field.general.text_option)">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Text option', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div>
                <table class="nbd-table">
                    <tr>
                        <th><?php _e('Min length', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Max length', 'web-to-print-online-designer'); ?></th>
                    </tr>
                    <tr>
                        <td>
                            <input class="nbd-short-ip" type="text" string-to-number ng-model="field.general.text_option.value.min" name="options[fields][{{fieldIndex}}][general][text_option][min]"/>
                        </td>
                        <td>
                            <input class="nbd-short-ip" type="text" string-to-number ng-model="field.general.text_option.value.max" name="options[fields][{{fieldIndex}}][general][text_option][max]"/>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php echo '</script>';