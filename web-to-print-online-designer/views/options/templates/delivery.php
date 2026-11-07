<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="nbd.delivery">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Delivery', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <div class="nbd-table-wrap">
                <table class="nbd-table" style="text-align: center;">
                    <thead>
                        <tr>
                            <th><?php _e('Option', 'web-to-print-online-designer'); ?></th>
                            <th><?php _e('Days', 'web-to-print-online-designer'); ?></th>
                            <th><?php _e('Max quantity', 'web-to-print-online-designer'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="(opIndex, op) in field.general.attributes.options">
                            <td>{{op.name}}</td>
                            <td>
                                <input type="text" class="nbd-short-ip" ng-model="op.delivery" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][delivery]"/>
                            </td>
                            <td>
                                <input type="text" class="nbd-short-ip" ng-model="op.max_qty" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][max_qty]"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php echo '</script>';