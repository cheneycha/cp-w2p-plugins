<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="nbd.color">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Background type', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <select name="options[fields][{{fieldIndex}}][general][attributes][bg_type]" ng-model="field.general.attributes.bg_type">
                <option value="i"><?php _e('Image', 'web-to-print-online-designer'); ?></option>
                <option value="c"><?php _e('Color', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Number of sides', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <input class="nbd-short-ip" name="options[fields][{{fieldIndex}}][general][attributes][number_of_sides]" string-to-number type="number" min="1" step="1" ng-model="field.general.attributes.number_of_sides" />
        </div>
    </div>
    <div class="nbd-field-info" ng-if="field.general.attributes.bg_type == 'c'">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Backgrund sides', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <div class="nbd-table-wrap">
                <table class="nbd-table" style="text-align: center;">
                    <tbody>
                        <tr ng-repeat="(opIndex, op) in field.general.attributes.options">
                            <th>{{op.name}}</th>
                            <td>
                                <input type="text" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][bg_color]" ng-model="op.bg_color" class="nbd-color-picker" nbd-color-picker="op.bg_color"/>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="nbd-field-info" ng-if="field.general.attributes.bg_type == 'i'">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Sides background', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <div class="nbd-table-wrap">
                <table class="nbd-table" style="text-align: center;">
                    <tbody>
                        <tr ng-repeat="(opIndex, op) in field.general.attributes.options">
                            <th>{{op.name}}</th>
                            <td ng-repeat="n in [] | range:field.general.attributes.number_of_sides">
                                <input ng-hide="true" ng-model="op.bg_image[n]" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][bg_image][{{n}}]"/>
                                <img class="bg_od_preview" title="<?php _e('Click to change image', 'web-to-print-online-designer'); ?>" ng-click="set_attribute_image(fieldIndex, opIndex, 'bg_image', 'bg_image_url', n)" ng-src="{{op.bg_image[n] != undefined ? op.bg_image_url[n] : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />  
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Use as pattern', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <select name="options[fields][{{fieldIndex}}][general][attributes][show_as_pt]" ng-model="field.general.attributes.show_as_pt">
                <option value="n"><?php _e('No', 'web-to-print-online-designer'); ?></option>
                <option value="y"><?php _e('Yes', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
<?php echo '</script>';