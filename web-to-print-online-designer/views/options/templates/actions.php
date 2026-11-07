<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="nbd.actions">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Artwork actions', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <div class="nbd-table-wrap">
                <table class="nbd-table" style="text-align: center;">
                    <thead>
                        <tr>
                            <th><?php _e('Option', 'web-to-print-online-designer'); ?></th>
                            <th><?php _e('Mapping action', 'web-to-print-online-designer'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="(opIndex, op) in field.general.attributes.options">
                            <td>{{op.name}}</td>
                            <td>
                                <select ng-model="op.action" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][action]">
                                    <option value="n"><?php _e('No action', 'web-to-print-online-designer'); ?></option>
                                    <option value="u"><?php _e('Upload design', 'web-to-print-online-designer'); ?></option>
                                    <option value="c"><?php _e('Create design online', 'web-to-print-online-designer'); ?></option>
                                    <option value="h"><?php _e('Hire designer', 'web-to-print-online-designer'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php echo '</script>';