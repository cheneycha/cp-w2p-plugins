<?php if (!defined('ABSPATH')) exit; ?>
<div class="section-container">
    <p class="section-title"><input class="nbd-ip-readonly" value="<?php _e('Sides/Pages', 'web-to-print-online-designer'); ?>" readonly=""></p>
    <div class="nbd-section-wrap">
        <div class="nbd-field-info"> 
            <div class="nbd-field-info-1">
                <label><b><?php _e('Enabled', 'web-to-print-online-designer'); ?></b> <nbd-tip  data-tip="<?php _e('Choose whether the option is enabled or not.', 'web-to-print-online-designer'); ?>" ></nbd-tip></label>
            </div>
            <div class="nbd-field-info-2">
                <select name="options[side][enable]" ng-model="options.side.enable">
                    <option value="y"><?php _e('Yes', 'web-to-print-online-designer'); ?></option>
                    <option value="n"><?php _e('No', 'web-to-print-online-designer'); ?></option>
                </select>
            </div>                                                    
        </div>
        <div class="nbd-field-wrap" ng-show="options.side.enable == 'y'" style="margin-bottom: 0;">
            <div class="nbd-nav">
                <div>
                    <ul nbd-tab>
                        <li class="nbd-field-tab active" data-target="tab-general"><?php _e('General', 'web-to-print-online-designer') ?></li>
                        <li class="nbd-field-tab" data-target="tab-opdesign"><?php _e('Design setting', 'web-to-print-online-designer') ?></li>
                        <li class="nbd-field-tab" data-target="tab-appearance"><?php _e('Appearance', 'web-to-print-online-designer') ?></li>
                    </ul>                                                    
                </div>  
                <div class="clear"></div>                                                    
            </div>
            <div>
                <div class="tab-general nbd-field-content active">
                    <div class="nbd-field-info">
                        <div class="nbd-field-info-1">
                            <label>
                                <b><?php _e('Price type', 'web-to-print-online-designer'); ?></b>
                                <nbd-tip data-tip="<?php _e('1- Fixed amount: This is an flat increase or decrease added to the product price. 2- Percent of the original price: This is a percentage increase or decrease of the initial product price. 3- Percent of the original price + options: This is a percentage increase or decrease of the initial product price plus all of the other options that are not of this type. 4- Current value * price: This will multiply field value by the Price you set.', 'web-to-print-online-designer'); ?>" ></nbd-tip>
                            </label>
                        </div>  
                        <div class="nbd-field-info-2">
                            <select name="options[side][price_type]" ng-model="options.side.price_type">
                                <option value="f"><?php _e('Fixed amount ', 'web-to-print-online-designer'); ?></option>
                                <option value="p"><?php _e('Percent of the original price', 'web-to-print-online-designer'); ?></option>
                                <option value="p+"><?php _e('Percent of the original price + options', 'web-to-print-online-designer'); ?></option>
                                <option value="c"><?php _e('Current value * price', 'web-to-print-online-designer'); ?></option>
                            </select>
                        </div>      
                    </div>
                    <div class="nbd-field-info"> 
                        <div class="nbd-field-info-1">
                            <label><b><?php _e('Depend quantity breaks', 'web-to-print-online-designer'); ?></b></label>
                        </div>  
                        <div class="nbd-field-info-2">
                            <select name="options[side][depend_quantity]" ng-model="options.side.depend_quantity">
                                <option value="y"><?php _e('Yes', 'web-to-print-online-designer'); ?></option>
                                <option value="n"><?php _e('No', 'web-to-print-online-designer'); ?></option>
                            </select>
                        </div> 
                    </div>                                              
                    <div class="nbd-field-info"> 
                        <div class="nbd-field-info-1">
                            <label><b><?php _e('Dynamic number of sides / pages', 'web-to-print-online-designer'); ?></b></label>
                        </div>  
                        <div class="nbd-field-info-2">
                            <select name="options[side][dynamic]" ng-model="options.side.dynamic">
                                <option value="y"><?php _e('Yes', 'web-to-print-online-designer'); ?></option>
                                <option value="n"><?php _e('No', 'web-to-print-online-designer'); ?></option>
                            </select>
                        </div>
                    </div>      
                    <div class="nbd-field-info"> 
                        <div class="nbd-field-info-1">
                            <label><b><?php _e('Number of sides / pages', 'web-to-print-online-designer'); ?></b></label>
                        </div>  
                        <div class="nbd-field-info-2">
                            <div class="nbd-table-wrap" style="overflow: hidden;">
                                <div class="nbd-table-wrap">
                                    <table class="nbd-table">
                                        <tr ng-show="options.quantity_breaks.length > 1 && options.side.depend_quantity == 'y'">
                                            <th></th>
                                            <th></th>
                                            <th ng-repeat="break in options.quantity_breaks">{{break.val}}</th>
                                        </tr>
                                        <tr>
                                            <th><?php _e('Actions', 'web-to-print-online-designer'); ?></th>
                                            <th><?php _e('Side name', 'web-to-print-online-designer'); ?></th>
                                            <th ng-repeat="break in options.quantity_breaks" ng-hide="options.side.depend_quantity != 'y' && $index > 0"><?php _e('Price', 'web-to-print-online-designer'); ?></th>
                                        </tr>                                                                
                                        <tr ng-repeat="op in options.side.options">
                                            <td>
                                                <div style="display: flex;">
                                                    <a style="margin-right: 3px;" class="button nbd-mini-btn"  title="<?php _e('Delete', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
                                                </div>    
                                            </td>
                                            <td>
                                                <input class="nbd-medium-ip" type="text" ng-model="op.name"/>
                                            </td>
                                            <td ng-repeat="break in options.quantity_breaks" ng-hide="options.side.depend_quantity != 'y' && $index > 0">
                                                <input class="nbd-short-ip" type="text" ng-model="op.price[$index]"/>
                                            </td>                                                                            
                                        </tr>
                                    </table>
                                </div>     
                            </div>
                            <div style="margin-top: 5px;"><a class="button" title="<?php _e('Add more', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-plus"></span> <?php _e('Add more', 'web-to-print-online-designer'); ?></a></div>
                        </div> 
                    </div> 
                </div> 
                <div class="tab-opdesign nbd-field-content">
                    opdesign
                </div>
                <div class="tab-appearance nbd-field-content">
                    appearance
                </div>                                                
            </div>  
        </div>     
    </div>        
</div> 