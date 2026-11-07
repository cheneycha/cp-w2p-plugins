<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="nbd.size">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div>
                <label>
                    <b><?php _e('Use a same online design config', 'web-to-print-online-designer'); ?></b>
                    <nbd-tip data-tip="<?php _e('All attributes have a same online design config ( product width, height, area design width, height, left, top ).', 'web-to-print-online-designer'); ?>" ></nbd-tip>
                </label>
            </div>
        </div>
        <div class="nbd-field-info-2">
            <select name="options[fields][{{fieldIndex}}][general][attributes][same_size]" ng-model="field.general.attributes.same_size">
                <option value="y"><?php _e('Yes', 'web-to-print-online-designer'); ?></option>
                <option value="n"><?php _e('No', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
    <div class="nbd-field-info" ng-if="field.general.attributes.same_size == 'n'">
        <div><b><?php _e('Online design config:', 'web-to-print-online-designer'); ?></b></div>
        <div class="nbd-table-wrap">
            <table class="nbd-table" style="text-align: center;">
                <thead>
                    <tr>
                        <th></th>
                        <th><?php _e('Product width', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Product height', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Design width', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Design height', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Design top', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Design left', 'web-to-print-online-designer'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="(opIndex, op) in field.general.attributes.options">
                        <th>{{op.name}}</th>
                        <td><input string-to-number required class="nbd-short-ip" ng-model="op.product_width" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][product_width]" /></td>
                        <td><input string-to-number required class="nbd-short-ip" ng-model="op.product_height" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][product_height]" /></td>
                        <td><input string-to-number required class="nbd-short-ip" ng-model="op.real_width" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][real_width]" /></td>
                        <td><input string-to-number required class="nbd-short-ip" ng-model="op.real_height" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][real_height]" /></td>
                        <td><input string-to-number required class="nbd-short-ip" ng-model="op.real_top" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][real_top]" /></td>
                        <td><input string-to-number required class="nbd-short-ip" ng-model="op.real_left" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][real_left]" /></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php echo '</script>';