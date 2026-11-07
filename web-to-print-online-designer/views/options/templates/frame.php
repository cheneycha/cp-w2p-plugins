<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="nbd.frame">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Frame', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <div class="nbd-table-wrap">
                <table class="nbd-table" style="text-align: center;">
                    <thead>
                        <tr>
                            <th><?php _e('Option', 'web-to-print-online-designer'); ?></th>
                            <th><?php _e('Frame image', 'web-to-print-online-designer'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="(opIndex, op) in field.general.attributes.options">
                            <td>{{op.name}}</td>
                            <td>
                                <input ng-hide="true" ng-model="op.frame_image" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][frame_image]"/>
                                <img class="bg_od_preview" title="<?php _e('Click to change image', 'web-to-print-online-designer'); ?>" 
                                     ng-click="set_attribute_image(fieldIndex, opIndex, 'frame_image', 'frame_image_url')" 
                                     ng-src="{{op.frame_image != undefined ? op.frame_image_url : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php echo '</script>';