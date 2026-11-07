<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="nbd.nbpb_com">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Views', 'web-to-print-online-designer'); ?></b><nbd-tip data-tip="<?php _e('Add product view/side, example: Front, Back, Top, Inside... and use them for all product components.', 'web-to-print-online-designer'); ?>" ></nbd-tip></div>
        </div>
        <div class="nbd-field-info-2">
            <div class="nbd-table-wrap">
                <table class="nbd-table" style="text-align: center;">
                    <thead>
                        <tr>
                            <th><?php _e('View name', 'web-to-print-online-designer'); ?></th>
                            <th><?php _e('View base', 'web-to-print-online-designer'); ?></th>
                            <th><?php _e('Action', 'web-to-print-online-designer'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="(vIndex, view) in options.views">
                            <td><input style="width: 150px;" ng-model="view.name" type="text"/></td>
                            <td>
                                <div class="image-icon-wrap">
                                    <span class="dashicons dashicons-no remove-image-icon" ng-click="remove_view_base(vIndex)"></span>
                                    <img ng-click="set_view_base(vIndex)" ng-src="{{view.base != 0 ? view.base_url : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
                                </div>
                            </td>
                            <td>
                                <a class="button btn-primary nbd-mini-btn" ng-click="removeView(vIndex)" title="<?php _e('Delete View', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr><td colspan="3"><a class="button btn-primary" ng-click="addView()"><?php _e('Add View', 'web-to-print-online-designer'); ?></a></td></tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Component icon', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <div class="image-icon-wrap">
                <span class="dashicons dashicons-no remove-image-icon" ng-click="remove_component_icon(fieldIndex)"></span>
                <input ng-hide="true" ng-model="field.general.component_icon" name="options[fields][{{fieldIndex}}][general][component_icon]"/>
                <img ng-click="set_component_icon(fieldIndex)" ng-src="{{field.general.component_icon != 0 ? field.general.component_icon_url : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
            </div>
        </div>
    </div>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div>
                <b><?php _e('Component configurations', 'web-to-print-online-designer'); ?></b>
                <nbd-tip data-tip="<?php _e('All images in the same view must have the same size.', 'web-to-print-online-designer'); ?>" ></nbd-tip>
            </div>
        </div>
        <div class="nbd-field-info-2">
            <div class="nbd-table-wrap">
                <table class="nbd-table" style="text-align: center;">
                    <thead>
                        <tr>
                            <th rowspan="2"><?php _e('Attribute', 'web-to-print-online-designer'); ?></th>
                            <th rowspan="2"><?php _e('Sub attribute', 'web-to-print-online-designer'); ?></th>
                            <th colspan="{{options.views.length}}"><?php _e('View', 'web-to-print-online-designer'); ?></th>
                        </tr>
                        <tr>
                            <th ng-repeat="view in options.views">{{view.name}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr ng-repeat="pbcon in field.general.pb_config_flat">
                            <td ng-if="pbcon.attr_rowspan > 0" rowspan="{{pbcon.attr_rowspan}}">{{field.general.attributes.options[pbcon.attr_index].name}}</td>
                            <td>{{pbcon.has_sattr ? field.general.attributes.options[pbcon.attr_index].sub_attributes[pbcon.sattr_index].name : ''}}</td>
                            <td ng-repeat="view in options.views" style="text-align: left;">
                                <label class="view-config">
                                    <?php _e('Show in view', 'web-to-print-online-designer'); ?>
                                    <input ng-model="field.general.pb_config[pbcon.attr_index][pbcon.sattr_index].views[$index].display" name="options[fields][{{fieldIndex}}][general][pb_config][{{pbcon.attr_index}}][{{pbcon.sattr_index}}][views][{{$index}}][display]" type="checkbox" />
                                </label>
                                <label class="view-config view-config-image">
                                    <?php _e('Image', 'web-to-print-online-designer'); ?>
                                    <div class="image-icon-wrap">
                                        <input ng-model="field.general.pb_config[pbcon.attr_index][pbcon.sattr_index].views[$index].image" name="options[fields][{{fieldIndex}}][general][pb_config][{{pbcon.attr_index}}][{{pbcon.sattr_index}}][views][{{$index}}][image]" ng-hide="true" />
                                        <span class="dashicons dashicons-no remove-image-icon" ng-click="remove_view_config_image(fieldIndex, pbcon.attr_index, pbcon.sattr_index, $index)"></span>
                                        <img ng-click="set_view_config_image(fieldIndex, pbcon.attr_index, pbcon.sattr_index, $index)" ng-src="{{field.general.pb_config[pbcon.attr_index][pbcon.sattr_index].views[$index].image != 0 ? field.general.pb_config[pbcon.attr_index][pbcon.sattr_index].views[$index].image_url : '<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png' ?>'}}" />
                                    </div>
                                </label>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php echo '</script>';