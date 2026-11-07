<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="nbd.overlay">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Number of sides', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <input class="nbd-short-ip" name="options[fields][{{fieldIndex}}][general][attributes][number_of_sides]" string-to-number type="number" min="1" step="1" ng-model="field.general.attributes.number_of_sides" />
        </div>
    </div>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Overlay', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <div class="nbd-table-wrap">
                <table class="nbd-table" style="text-align: center;">
                    <tbody>
                        <tr ng-repeat="(opIndex, op) in field.general.attributes.options">
                            <th>{{op.name}}</th>
                            <td ng-repeat="n in [] | range:field.general.attributes.number_of_sides">
                                <input ng-hide="true" ng-model="op.overlay_image[n]" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][overlay_image][{{n}}]"/>
                                <img class="bg_od_preview" title="<?php _e('Click to change image', 'web-to-print-online-designer'); ?>" ng-click="set_attribute_image(fieldIndex, opIndex, 'overlay_image', 'overlay_image_url', n)" ng-src="{{op.overlay_image[n] != undefined ? op.overlay_image_url[n] : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php echo '</script>';