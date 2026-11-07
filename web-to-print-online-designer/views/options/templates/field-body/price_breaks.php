<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_price_breaks">'; ?>
    <div class="nbd-field-info" ng-show="check_depend(field.general, field.general.price_breaks)">
        <div class="nbd-field-info-1">
            <div><label><b><?php _e('Price depend quantity breaks', 'web-to-print-online-designer'); ?></b></label></div>
        </div>
        <div class="nbd-field-info-2">
            <div class="nbd-table-wrap">
                <table class="nbd-table">
                    <tr>
                        <th><?php _e('Quantity break', 'web-to-print-online-designer'); ?></th>
                        <th ng-repeat="break in options.quantity_breaks">{{break.val}}</th>
                    </tr>
                    <tr>
                        <td><?php _e('Additional Price', 'web-to-print-online-designer'); ?></td>
                        <td ng-repeat="break in options.quantity_breaks">
                            <input autocomplete="off" ng-click="initFormulaPrice(field.general.price_breaks.value[$index], $index, fieldIndex)" class="nbd-short-ip" type="text" ng-model="field.general.price_breaks.value[$index]" name="options[fields][{{fieldIndex}}][general][price_breaks][{{$index}}]" />
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php echo '</script>';