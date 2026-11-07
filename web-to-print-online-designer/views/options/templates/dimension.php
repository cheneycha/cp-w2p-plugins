<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="nbd.dimension">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Dimension range:', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <table class="nbd-table">
                <thead>
                    <tr>
                        <th></th>
                        <th><?php _e('Min', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Max', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Step', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Default value', 'web-to-print-online-designer'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th><?php _e('Width', 'web-to-print-online-designer'); ?></th>
                        <td><input string-to-number class="nbd-short-ip" ng-model="field.general.min_width" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][min_width]" /></td>
                        <td><input string-to-number class="nbd-short-ip" ng-model="field.general.max_width" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][max_width]" /></td>
                        <td><input string-to-number class="nbd-short-ip" ng-model="field.general.step_width" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][step_width]" /></td>
                        <td><input string-to-number class="nbd-short-ip" ng-model="field.general.default_width" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][default_width]" /></td>
                    </tr>
                    <tr>
                        <th><?php _e('Height', 'web-to-print-online-designer'); ?></th>
                        <td><input string-to-number class="nbd-short-ip" ng-model="field.general.min_height" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][min_height]" /></td>
                        <td><input string-to-number class="nbd-short-ip" ng-model="field.general.max_height" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][max_height]" /></td>
                        <td><input string-to-number class="nbd-short-ip" ng-model="field.general.step_height" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][step_height]" /></td>
                        <td><input string-to-number class="nbd-short-ip" ng-model="field.general.default_height" type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][default_height]" /></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="nbd-field-info" style="margin-top: 10px;">
        <div class="nbd-field-info-1">
            <div>
                <label>
                    <b><?php _e('Enable measure price base on design area', 'web-to-print-online-designer'); ?></b>
                    <nbd-tip data-tip="<?php _e('Measure price base on design area.', 'web-to-print-online-designer'); ?>" ></nbd-tip>
                </label>
            </div>
        </div>
        <div class="nbd-field-info-2">
            <select name="options[fields][{{fieldIndex}}][general][mesure]" ng-model="field.general.mesure">
                <option value="y"><?php _e('Yes', 'web-to-print-online-designer'); ?></option>
                <option value="n"><?php _e('No', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
    <div class="nbd-field-info" style="margin-top: 10px;" ng-if="field.general.mesure == 'y'">
        <div class="nbd-field-info-1">
            <div>
                <label>
                    <b><?php _e('Calculate additional price base on ', 'web-to-print-online-designer'); ?></b>
                </label>
            </div>
        </div>
        <div class="nbd-field-info-2">
            <select name="options[fields][{{fieldIndex}}][general][mesure_type]" ng-model="field.general.mesure_type">
                <option value="u"><?php _e('Price per Unit', 'web-to-print-online-designer'); ?></option>
                <option value="r"><?php _e('Area breaks ( area range )', 'web-to-print-online-designer'); ?></option>
                <option value="ur"><?php _e('Unit price base on area breaks', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
    <div class="nbd-field-info" style="margin-top: 10px;" ng-if="field.general.mesure == 'y'">
        <div class="nbd-field-info-1">
            <div>
                <label>
                    <b><?php _e('Base design area', 'web-to-print-online-designer'); ?></b>
                    <nbd-tip data-tip="<?php _e('Minimum design area to start calculate additional price.', 'web-to-print-online-designer'); ?>" ></nbd-tip>
                </label>
            </div>
        </div>
        <div class="nbd-field-info-2">
            <input class="nbd-short-ip" type="text" ng-model="field.general.mesure_min_area" name="options[fields][{{fieldIndex}}][general][mesure_min_area]">
        </div>
    </div>
    <div class="nbd-field-info" ng-if="field.general.mesure == 'y'">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Price base on design area:', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <table class="nbd-table nbo-measure-range">
                <thead>
                    <tr>
                        <th class="check-column">
                            <input class="nbo-measure-range-select-all" type="checkbox" ng-click="select_all_measurement_range(fieldIndex, $event)">
                        </th>
                        <th class="range-column" style="padding-right: 30px;">
                            <span class="column-title" data-text="<?php esc_attr_e( 'Measurement Range', 'web-to-print-online-designer' ); ?>"><?php _e( 'Measurement Range', 'web-to-print-online-designer' ); ?></span>
                            <nbd-tip ng-show="field.general.mesure_type == 'u' || field.general.mesure_type == 'r'" data-tip="<?php _e( 'Configure the starting-ending range, inclusive, of measurements to match this rule. The first matched rule will be used to determine the price. The final rule can be defined without an ending range to match all measurements greater than or equal to its starting range.', 'web-to-print-online-designer'); ?>" ></nbd-tip>
                            <nbd-tip ng-show="field.general.mesure_type == 'ur'" data-tip="<?php _e('Configure the starting-ending range, inclusive, of measurements to match this rule. The measurement price is total price by area ranges ( area in range * unit price base on area range ).', 'web-to-print-online-designer'); ?>" ></nbd-tip>
                        </th>
                        <th class="price-column">
                            <?php
                                $woocommerce_currency_symbol    = get_woocommerce_currency_symbol();
                                $dimensions_unit                = nbdesigner_get_option( 'nbdesigner_dimensions_unit', 'cm' )
                            ?>
                            <span ng-show="field.general.mesure_type == 'u' || field.general.mesure_type == 'ur'"><?php _e('Price per Unit', 'web-to-print-online-designer'); ?> <?php echo ' ('. $woocommerce_currency_symbol . '/' . $dimensions_unit . '<sup>2</sup>)'; ?></span>
                            <span ng-show="field.general.mesure_type == 'r'"><?php _e('Fixed amount', 'web-to-print-online-designer'); ?></span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="(mrIndex, mr) in field.general.mesure_range">
                        <td>
                            <input type="checkbox" class="nbo-measure-range-checkbox" ng-model="mr[3]">
                        </td>
                        <td>
                            <span>
                                <span class="nbd-table-price-label"><?php echo _e('From', 'web-to-print-online-designer'); ?></span>
                                <input string-to-number type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][mesure_range][{{mrIndex}}][0]" ng-model="mr[0]" class="nbd-short-ip">
                            </span>
                            <span>
                                <span class="nbd-table-price-label" style="margin-left: 10px;"><?php echo _e('To', 'web-to-print-online-designer'); ?></span>
                                <input string-to-number type="number" min="0" step="any" name="options[fields][{{fieldIndex}}][general][mesure_range][{{mrIndex}}][1]" ng-model="mr[1]" class="nbd-short-ip">
                            </span>
                        </td>
                        <td>
                            <input string-to-number type="number" step="any" name="options[fields][{{fieldIndex}}][general][mesure_range][{{mrIndex}}][2]" ng-model="mr[2]" class="nbd-short-ip">
                        </td>
                    </tr> 
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">
                            <button ng-click="add_measurement_range(fieldIndex)" style="float: left;" type="button" class="button button-primary nbd-pricing-table-add-rule"><?php _e( 'Add Rule', 'web-to-print-online-designer' ); ?></button>
                            <button ng-click="delete_measurement_ranges(fieldIndex, $event)" style="float: right;" type="button" class="button button-secondary nbd-pricing-table-delete-rules"><?php _e( 'Delete Selected', 'web-to-print-online-designer' ); ?></button>
                        </th>
                    </tr>
                </tfoot> 
            </table>
        </div>
    </div>
    <div class="nbd-field-info" style="margin-top: 10px;" ng-if="field.general.mesure == 'y'">
        <div class="nbd-field-info-1">
            <div>
                <label>
                    <b><?php _e('Calculate price based on number of sides/pages', 'web-to-print-online-designer'); ?></b>
                </label>
            </div>
        </div>
        <div class="nbd-field-info-2">
            <select name="options[fields][{{fieldIndex}}][general][mesure_base_pages]" ng-model="field.general.mesure_base_pages">
                <option value="y"><?php _e('Yes', 'web-to-print-online-designer'); ?></option>
                <option value="n"><?php _e('No', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
    <div class="nbd-field-info" style="margin-top: 10px;" ng-if="field.general.mesure == 'y'">
        <div class="nbd-field-info-1">
            <div>
                <label>
                    <b><?php _e('Calculate product area range based on cumulative cart item quantity', 'web-to-print-online-designer'); ?></b>
                </label>
            </div>
        </div>
        <div class="nbd-field-info-2">
            <select name="options[fields][{{fieldIndex}}][general][mesure_base_qty]" ng-model="field.general.mesure_base_qty">
                <option value="y"><?php _e('Yes', 'web-to-print-online-designer'); ?></option>
                <option value="n"><?php _e('No', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
<?php echo '</script>';