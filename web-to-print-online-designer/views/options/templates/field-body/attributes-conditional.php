<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="field_body_attributes_conditional">'; ?>
    <div>
        <div class="nbd-margin-10"></div>
        <hr />
        <div class="nbd-enable-attribute-con">
            <label><input type="checkbox" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][enable_con]" ng-true-value="'on'" ng-false-value="'off'" ng-model="op.enable_con" ng-checked="op.enable_con" /> <?php _e('Enable Attribute Conditional Logic', 'web-to-print-online-designer'); ?></label>
        </div>
        <div class="nbd-margin-10"></div>
        <div class="nbd-subattributes-wrapper" ng-if="op.enable_con === true || op.enable_con == 'on'">
            <div>
                <select ng-model="op.con_show" style="width: inherit;" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][con_show]">
                    <option value="y"><?php _e('Enable', 'web-to-print-online-designer'); ?></option>
                    <option value="n"><?php _e('Disable', 'web-to-print-online-designer'); ?></option>
                </select>
                <?php _e('this attribute if', 'web-to-print-online-designer'); ?>
                <select ng-model="op.con_logic" style="width: inherit;" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][con_logic]">
                    <option value="a"><?php _e('all', 'web-to-print-online-designer'); ?></option>
                    <option value="o"><?php _e('any', 'web-to-print-online-designer'); ?></option>
                </select>
                <?php _e('of these rules match:', 'web-to-print-online-designer'); ?>
            </div>
            <div class="nbd-margin-10"></div>
            <div>
                <div ng-repeat="(cdIndex, con) in op.depend">
                    <select ng-model="con.id" style="width: 100px;" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][depend][{{cdIndex}}][id]">
                        <option ng-repeat="cf in options.fields | filter: { id: field.id }:excludeField" value="{{cf.id}}">{{cf.general.title.value}}</option>
                        <option value="qty"><?php _e('Quantity', 'web-to-print-online-designer'); ?></option>
                    </select>
                    <select ng-model="con.operator" style="width: 100px;" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][depend][{{cdIndex}}][operator]">
                        <option ng-if="con.id != 'qty'" value="i"><?php _e('is', 'web-to-print-online-designer'); ?></option>
                        <option ng-if="con.id != 'qty'" value="n"><?php _e('is not', 'web-to-print-online-designer'); ?></option>
                        <option ng-if="con.id != 'qty'" value="e"><?php _e('is empty', 'web-to-print-online-designer'); ?></option>
                        <option ng-if="con.id != 'qty'" value="ne"><?php _e('is not empty', 'web-to-print-online-designer'); ?></option>
                        <option ng-if="con.id == 'qty'" value="eq"><?php _e('equal', 'web-to-print-online-designer'); ?></option>
                        <option ng-if="con.id == 'qty'" value="gt"><?php _e('great than', 'web-to-print-online-designer'); ?></option>
                        <option ng-if="con.id == 'qty'" value="lt"><?php _e('less than', 'web-to-print-online-designer'); ?></option>
                    </select>
                    <select ng-if="(con.operator == 'i' || con.operator == 'n') && con.id != 'qty'" ng-model="con.val" ng-repeat="vf in options.fields | filter: {id: con.id}:includeField"  
                        name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][depend][{{cdIndex}}][val]" style="width: 100px;">
                        <option ng-repeat="vop in vf.general.attributes.options" value="{{$index}}">{{vop.name}}</option>
                    </select>
                    <nbo-sub-attr-select ng-if="(con.operator == 'i' || con.operator == 'n') && con.id != 'qty' && con.id != '' && con.val != ''" 
                        find="fieldIndex" oind="opIndex" cind="cdIndex" con="con" fields="options.fields" ></nbo-sub-attr-select>
                    <input ng-if="con.id == 'qty'" type="text" ng-model="con.val" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][depend][{{cdIndex}}][val]" style="width: 100px !important; vertical-align: middle;" />
                    <a class="nbd-field-btn nbd-mini-btn button" ng-click="add_attr_condition(fieldIndex, opIndex)"><span class="dashicons dashicons-plus"></span></a>
                    <a class="nbd-field-btn nbd-mini-btn button" ng-click="delete_attr_condition(fieldIndex, opIndex, cdIndex)"><span class="dashicons dashicons-no-alt"></span></a>
                </div>
            </div>
        </div>
    </div>
<?php echo '</script>';