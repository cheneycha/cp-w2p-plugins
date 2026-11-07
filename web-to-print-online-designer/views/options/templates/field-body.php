<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body">'; ?>
    <?php if( $large_amount_field == 'no' ): ?>
    <div ng-show="field.isExpand">
    <?php else: ?>
    <div ng-if="field.isExpand">
    <?php endif; ?>
        <div class="tab-general nbd-field-content active">
            <ng-include src="'field_body_title'"></ng-include>
            <ng-include src="'field_body_description'"></ng-include>
            <ng-include src="'field_body_data_type'"></ng-include>
            <ng-include src="'field_body_input_type'"></ng-include>
            <ng-include src="'field_body_input_option'"></ng-include>
            <ng-include src="'field_body_text_option'"></ng-include>
            <ng-include src="'field_body_placeholder'"></ng-include>
            <ng-include src="'field_body_upload_option'"></ng-include>
            <ng-include src="'field_body_enabled'"></ng-include>
            <ng-include src="'field_body_published'"></ng-include>
            <ng-include src="'field_body_required'"></ng-include>
            <ng-include src="'field_body_price_type'"></ng-include>
            <ng-include src="'field_body_depend_qty'"></ng-include>
            <ng-include src="'field_body_depend_quantity'"></ng-include>
            <ng-include src="'field_body_price'"></ng-include>
            <ng-include src="'field_body_price_breaks'"></ng-include>
            <ng-include src="'field_body_attributes'"></ng-include>
        </div>
        <div class="tab-conditional nbd-field-content">
            <div class="nbd-field-info">
                <div class="nbd-field-info-1">
                    <div><b><?php _e('Field Conditional Logic', 'web-to-print-online-designer'); ?></b> <nbd-tip data-tip="<?php _e('Enable conditional logic for showing or hiding this field.', 'web-to-print-online-designer'); ?>"></nbd-tip></div>
                </div>  
                <div class="nbd-field-info-2">
                    <div>
                        <select ng-model="field.conditional.enable" style="width: 100px;" name="options[fields][{{fieldIndex}}][conditional][enable]">
                            <option value="n"><?php _e('No', 'web-to-print-online-designer'); ?></option>
                            <option value="y"><?php _e('Yes', 'web-to-print-online-designer'); ?></option>
                        </select>
                    </div>
                    <div ng-if="field.conditional.enable == 'y'">
                        <div style="margin-top: 10px;">
                            <select ng-model="field.conditional.show" style="width: inherit;" name="options[fields][{{fieldIndex}}][conditional][show]">
                                <option value="y"><?php _e('Show', 'web-to-print-online-designer'); ?></option>
                                <option value="n"><?php _e('Hide', 'web-to-print-online-designer'); ?></option>
                            </select>
                            <?php _e('this field if', 'web-to-print-online-designer'); ?>
                            <select ng-model="field.conditional.logic" style="width: inherit;" name="options[fields][{{fieldIndex}}][conditional][logic]">
                                <option value="a"><?php _e('all', 'web-to-print-online-designer'); ?></option>
                                <option value="o"><?php _e('any', 'web-to-print-online-designer'); ?></option>
                            </select>
                            <?php _e('of these rules match:', 'web-to-print-online-designer'); ?>
                        </div>
                        <div style="margin-top: 10px;">
                            <div ng-repeat="(cdIndex, con) in field.conditional.depend">
                                <select ng-change="update_condition_qty( fieldIndex )" ng-model="con.id" style="width: 200px;" name="options[fields][{{fieldIndex}}][conditional][depend][{{cdIndex}}][id]">
<!--                                    <option ng-repeat="cf in options.fields | filter: { id: '!' + field.id }" value="{{cf.id}}">{{cf.general.title.value}}</option>-->
                                    <option ng-repeat="cf in options.fields | filter: { id: field.id }:excludeField" value="{{cf.id}}">{{cf.general.title.value}}</option>
                                    <option value="qty"><?php _e('Quantity', 'web-to-print-online-designer'); ?></option>
                                </select>
                                <select ng-model="con.operator" style="width: 120px;" name="options[fields][{{fieldIndex}}][conditional][depend][{{cdIndex}}][operator]">
                                    <option ng-if="con.id != 'qty'" value="i"><?php _e('is', 'web-to-print-online-designer'); ?></option>
                                    <option ng-if="con.id != 'qty'" value="n"><?php _e('is not', 'web-to-print-online-designer'); ?></option>
                                    <option ng-if="con.id != 'qty'" value="e"><?php _e('is empty', 'web-to-print-online-designer'); ?></option>
                                    <option ng-if="con.id != 'qty'" value="ne"><?php _e('is not empty', 'web-to-print-online-designer'); ?></option>
                                    <option ng-if="con.id == 'qty'" value="eq"><?php _e('equal', 'web-to-print-online-designer'); ?></option>
                                    <option ng-if="con.id == 'qty'" value="gt"><?php _e('great than', 'web-to-print-online-designer'); ?></option>
                                    <option ng-if="con.id == 'qty'" value="lt"><?php _e('less than', 'web-to-print-online-designer'); ?></option>
                                </select>
                                <select ng-if="(con.operator == 'i' || con.operator == 'n') && con.id != 'qty'" ng-model="con.val" ng-repeat="vf in options.fields | filter: {id: con.id}:includeField"  
                                    name="options[fields][{{fieldIndex}}][conditional][depend][{{cdIndex}}][val]" style="width: 200px;">
                                    <option ng-repeat="vop in vf.general.attributes.options" value="{{$index}}">{{vop.name}}</option>
                                </select> 
                                <input ng-if="con.id == 'qty'" type="text" ng-model="con.val" name="options[fields][{{fieldIndex}}][conditional][depend][{{cdIndex}}][val]" style="width: 200px !important; vertical-align: middle;"/>
                                <a class="nbd-field-btn nbd-mini-btn button" ng-click="add_condition(fieldIndex)"><span class="dashicons dashicons-plus"></span></a>
                                <a class="nbd-field-btn nbd-mini-btn button" ng-click="delete_condition(fieldIndex, cdIndex)"><span class="dashicons dashicons-no-alt"></span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-appearance nbd-field-content">
            <div class="nbd-field-info" ng-repeat="(key, data) in field.appearance">
                <div class="nbd-field-info-1">
                    <div><label><b>{{data.title}}</b> <nbd-tip ng-if="data.description != ''" data-tip="{{data.description}}" ></nbd-tip></label></div>
                </div> 
                <div class="nbd-field-info-2">
                    <div ng-if="data.type == 'dropdown'">
                        <select name="options[fields][{{fieldIndex}}][appearance][{{key}}]" ng-model="data.value">
                            <option ng-repeat="op in data.options" value="{{op.key}}">{{op.text}}</option>
                        </select>
                    </div>
                    <div ng-if="data.type == 'dropdown_group'">
                        <select name="options[fields][{{fieldIndex}}][appearance][{{key}}]" ng-model="data.value">
                            <optgroup ng-repeat="gr in data.options" label={{gr.title}}>
                                <option ng-repeat="op in gr.value" value="{{op.key}}">{{op.text}}</option>
                            </optgroup>
                        </select>
                    </div>
                    <div ng-if="data.type == 'text'">
                        <input type="text" name="options[fields][{{fieldIndex}}][appearance][{{key}}]" ng-model="data.value">
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-online-design nbd-field-content" ng-if="field.nbd_type">
            <input ng-hide="true" name="options[fields][{{fieldIndex}}][nbd_type]" ng-model="field.nbd_type">
            <ng-include src="field.nbd_template"></ng-include>
        </div>
        <div class="tab-product-builder nbd-field-content" ng-if="field.nbpb_type">
            <input ng-hide="true" name="options[fields][{{fieldIndex}}][nbpb_type]" ng-model="field.nbpb_type">
            <ng-include src="field.nbd_template"></ng-include>
        </div>
        <div class="tab-extra-options nbd-field-content" ng-if="field.nbe_type">
            <input ng-hide="true" name="options[fields][{{fieldIndex}}][nbe_type]" ng-model="field.nbe_type">
            <ng-include src="field.nbd_template"></ng-include>
        </div>
    </div>
<?php echo '</script>';