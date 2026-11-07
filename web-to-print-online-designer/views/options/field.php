<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbd-field-wrap" ng-repeat="(fieldIndex, field) in options.fields" id="{{field.id}}">
    <div class="nbd-nav">
        <div ng-dblclick="toggleExpandField($index, $event)" style="cursor: pointer;" title="<?php _e('Double click to expand option', 'web-to-print-online-designer') ?>">
            <ul nbd-tab ng-class="field.isExpand ? '' : 'left'" class="nbd-tab-nav">
                <li class="nbd-field-tab active" data-target="tab-general"><?php _e('General', 'web-to-print-online-designer') ?></li>
                <li class="nbd-field-tab" data-target="tab-conditional"><?php _e('Conditional', 'web-to-print-online-designer') ?></li>
                <li class="nbd-field-tab" data-target="tab-appearance"><?php _e('Appearance', 'web-to-print-online-designer') ?></li>
                <li ng-if="field.nbd_type" class="nbd-field-tab" data-target="tab-online-design"><?php _e('Online design', 'web-to-print-online-designer'); ?></li>
                <li ng-if="field.nbpb_type" class="nbd-field-tab" data-target="tab-product-builder"><?php _e('Product builder', 'web-to-print-online-designer'); ?></li>
                <li ng-if="field.nbe_type" class="nbd-field-tab" data-target="tab-extra-options"><?php _e('Extra options', 'web-to-print-online-designer'); ?></li>
            </ul>
            <input ng-hide="true" ng-model="field.id" name="options[fields][{{fieldIndex}}][id]"/>
            <span class="nbd-field-name" ng-class="[{true: '', false: 'left'}[field.isExpand], {'n': 'nbo_blur'}[field.general.enabled.value]]">
                <span>{{field.general.title.value}}</span>
                <span style="color: #0085ba;">{{get_field_group_name( field.id )}}</span>
            </span>
            <span class="nbdesigner-right field-action">
                <span class="nbo-type-label-wrap"><span class="nbo-type-label" ng-class="get_field_class((field.nbd_type != '' && field.nbd_type != null) ? field.nbd_type : field.nbpb_type)">{{get_field_type( (field.nbd_type != '' && field.nbd_type != null) ? field.nbd_type : ( (field.nbpb_type != '' && field.nbpb_type != null) ? field.nbpb_type : field.nbe_type ) )}}</span></span>
                <span class="nbo-sort-group">
                    <span ng-click="sort_field($index, 'up')" class="dashicons dashicons-arrow-up nbo-sort-up nbo-sort" title="<?php _e('Up', 'web-to-print-online-designer') ?>"></span>
                    <span ng-click="sort_field($index, 'down')" class="dashicons dashicons-arrow-down nbo-sort-down nbo-sort" title="<?php _e('Down', 'web-to-print-online-designer') ?>"></span>
                </span>
                <a class="nbd-field-btn nbd-mini-btn button" ng-click="delete_field($index)" title="<?php _e('Delete', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
                <a class="nbd-field-btn nbd-mini-btn button" ng-click="copy_field($index)" title="<?php _e('Copy', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-admin-page"></span></a>
                <a class="nbd-field-btn nbd-mini-btn button" ng-click="toggleExpandField($index, $event)" title="<?php _e('Expand', 'web-to-print-online-designer'); ?>"><span ng-show="!field.isExpand" class="dashicons dashicons-arrow-down"></span><span ng-show="field.isExpand" class="dashicons dashicons-arrow-up"></span></a>
            </span>
        </div>
        <div class="clear"></div>
    </div>
    <ng-include src="'field_body'"></ng-include>
</div>
<div style="display: flex; justify-content: space-between;">
    <a style="background: rgba(170, 0, 0, 0.75);color: #fff;border-color: rgba(170, 0, 0, 0.75);" class="button" ng-click="clear_all_fields()"><span class="dashicons dashicons-no-alt"></span> <?php _e('Clear All Fields', 'web-to-print-online-designer'); ?></a>
    <a class="button button-primary" ng-click="add_field()"><span class="dashicons dashicons-plus"></span> <?php _e('Add Field', 'web-to-print-online-designer'); ?></a>
</div>
<?php
    include 'templates/field-body.php';
    include 'templates/page.php';
    include 'templates/page1.php';
    include 'templates/page2.php';
    include 'templates/page3.php';
    include 'templates/color.php';
    include 'templates/size.php';
    include 'templates/dpi.php';
    include 'templates/area.php';
    include 'templates/shape.php';
    include 'templates/orientation.php';
    include 'templates/dimension.php';
    include 'templates/padding.php';
    include 'templates/rounded_corner.php';
    include 'templates/nbpb_com.php';
    include 'templates/nbpb_text.php';
    include 'templates/nbpb_image.php';
    include 'templates/delivery.php';
    include 'templates/actions.php';
    include 'templates/overlay.php';
    include 'templates/fold.php';
    include 'templates/frame.php';
    include 'templates/number_file.php';
    include 'templates/pt_color.php';  // custom charmingprint

    include 'templates/field-body/title.php';
    include 'templates/field-body/description.php';
    include 'templates/field-body/data_type.php';
    include 'templates/field-body/input_type.php';
    include 'templates/field-body/input_option.php';
    include 'templates/field-body/text_option.php';
    include 'templates/field-body/placeholder.php';
    include 'templates/field-body/upload_option.php';
    include 'templates/field-body/enabled.php';
    include 'templates/field-body/published.php';
    include 'templates/field-body/required.php';
    include 'templates/field-body/price_type.php';
    include 'templates/field-body/depend_qty.php';
    include 'templates/field-body/depend_quantity.php';
    include 'templates/field-body/price.php';
    include 'templates/field-body/price_breaks.php';
    include 'templates/field-body/attributes.php';
    include 'templates/field-body/attributes-conditional.php';
    include 'templates/field-body/sub-attributes-conditional.php';