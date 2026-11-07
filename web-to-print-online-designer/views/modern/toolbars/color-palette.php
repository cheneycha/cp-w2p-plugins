<div class="nbd-color-palette" id="nbd-color-palette" style="box-shadow: 1px 1px 3px 3px #dfdfdf;">
    <div ng-if="pt_color.key != 'pt_color1' && pt_color.key != 'pt_color2' && pt_color.key != 'pt_color3'" class="nbd-color-palette-inner">
        <div class="working-palette" ng-hide="settings.hideColorPalette">
            <h3 class="color-palette-label"><?php esc_html_e('Document colors','web-to-print-online-designer'); ?></h3>
            <ul class="main-color-palette nbd-perfect-scroll">
                <li class="color-palette-add" ng-click="showTextColorPalette()" ng-if="settings['nbdesigner_show_all_color'] == 'yes'"></li>
                <li class="color-eyedropper" ng-click="initEyeDropper2($event)" title="<?php esc_html_e('Eyedropper','web-to-print-online-designer'); ?>" ng-if="settings['nbdesigner_enable_eyedropper'] == 'yes' && !settings.is_mobile">
                    <span class="eyedropper-loading"></span>
                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 508.8 508.8" xml:space="preserve"><g><path d="M508.324,61.2c0-16.4-6.4-31.6-18-43.2c-11.6-11.6-26.8-18-43.2-18c-16.4,0-31.6,6.4-43.2,18l-96.8,97.6 c-4.8-2.8-10-4-15.6-4c-8.4,0-16.8,3.2-22.8,9.2l-4,4c-12,12-12.4,30.8-1.6,43.6l-242,242c-13.2,13.2-16,33.6-6.8,49.6l-10.4,14 c-5.2,6.8-4.4,17.6,1.6,24l5.6,5.6c3.2,3.2,8,5.2,13.2,5.2c4,0,8-1.2,10.8-3.6l14-10.8c6,3.2,12.8,5.2,20,5.2 c11.2,0,21.6-4.4,29.2-12l241.6-241.6c6,5.2,13.2,8,21.2,8c8.4,0,16.8-3.2,22.8-9.2l4-4c10.4-10.4,12.4-26,5.2-38.4l97.2-98 C501.924,92.8,508.324,77.6,508.324,61.2z M87.124,476c-4.8,4.8-11.2,7.6-18,7.6c-4.4,0-8.8-1.2-12.4-3.2c-2.4-1.2-5.2-2-7.6-2 c-3.6,0-6.8,1.2-9.6,3.2l-14,10.8c0,0-0.8,0.4-1.2,0.4c-0.8,0-1.6-0.4-1.6-0.4l-6-6c-0.8-0.8-0.8-2.4-0.4-3.2l10.4-14 c4-5.2,4.4-12,1.2-17.6c-5.6-10-4-22.4,4-30.4l242.4-241.6l54.4,54.8L87.124,476z"/></g></svg>
                </li>
                <li ng-repeat="color in listAddedColor track by $index" ng-click="changeFill(color)" class="color-palette-item" data-color="{{color}}" title="{{color}}" ng-style="{'background-color': color}"></li>
            </ul>
        </div>
        <div class="pinned-palette default-palette" ng-if="settings['nbdesigner_show_all_color'] == 'yes'">
            <h3 class="color-palette-label"><?php esc_html_e('Default palette','web-to-print-online-designer'); ?></h3>
            <ul class="main-color-palette" ng-repeat="palette in resource.defaultPalette" >
                <li ng-class="{'first-left': $first, 'last-right': $last, 'first-right': $index == 4,'last-left': $index == (palette.length - 5)}" ng-repeat="color in palette track by $index" ng-click="changeFill(color)" class="color-palette-item" data-color="{{color}}" title="{{color}}" ng-style="{'background': color}"></li>
            </ul>   
        </div>
        <div class="pinned-palette default-palette" ng-if="settings['nbdesigner_show_all_color'] == 'no'">
            <h3 class="color-palette-label"><?php esc_html_e('Color palette','web-to-print-online-designer'); ?></h3>
            <ul class="main-color-palette" >
                <li ng-repeat="color in __colorPalette track by $index" ng-class="{'first-left': $first, 'last-right': $last, 'first-right': $index == 4,'last-left': $index == (palette.length - 5)}" ng-click="changeFill(color);addColor(color)" class="color-palette-item" data-color="{{color}}" title="{{color}}" ng-style="{'background': color}"></li>
            </ul>
        </div>
        <div class="nbd-text-color-picker" id="nbd-text-color-picker" ng-class="showTextColorPicker ? 'active' : ''">
            <spectrum-colorpicker
                ng-model="currentColor"
                options="{
                    preferredFormat: 'hex',
                    flat: true,
                    showButtons: false,
                    showInput: true,
                    containerClassName: 'nbd-sp'
            }">
            </spectrum-colorpicker>
            <div>
                <button class="nbd-button" ng-click="addColor();changeFill(currentColor);"><?php esc_html_e('Choose','web-to-print-online-designer'); ?></button>
            </div>
        </div>
    </div>
    <div ng-if="pt_color.key == 'pt_color1' || pt_color.key == 'pt_color3'" class="nbd-color-palette-inner">
        <div class="working-palette" ng-hide="settings.hideColorPalette">

        </div>
        <div>
            <b style="color: #f00;white-space: pre-wrap;"><?php esc_html_e('You cannot change color in this color option!','web-to-print-online-designer'); ?></b>
        </div>
    </div>
    <!-- Custom charmingprint -->
    <div ng-if="pt_color.key == 'pt_color2'" class="nbd-color-palette-inner" style="background: #ffffff; border-radius: 2px;">
        <div class="pinned-palette default-palette">
            <h3 class="color-palette-label"><?php esc_html_e('Default palette','web-to-print-online-designer'); ?></h3>
            <ul class="main-color-palette" >
                <li ng-class="{'first-left': $first, 'last-right': $last, 'first-right': $index == 4,'last-left': $index == (palette.length - 5)}" ng-repeat="color in pt_color.mul_color track by $index" ng-click="changeFill(color.color)" class="color-palette-item" data-color="{{color.color}}" title="Price (+{{color.priceSelected}} <?php echo get_woocommerce_currency() ?>)" ng-style="{'background': color.color}"><span></span></li>
            </ul> 
        </div>
        <div class="mess_usable_color" style="margin-top: 10px;white-space: normal;">
            <div ng-show="color_used_name != ''" style="margin-bottom: 5px;"><?php esc_html_e('- Color used: ','web-to-print-online-designer'); ?><span ng-repeat="item in color_used_name" style="color: {{item.color}};">{{item.name}} </span></div> <!-- 30/11 add color name -->
            <div ng-if="pt_color.limit >= 1">
                <div style="margin-bottom: 5px;"><?php esc_html_e('- You have only ','web-to-print-online-designer'); ?><b>{{color_usable_qty}}</b><?php esc_html_e(' color left to use.','web-to-print-online-designer'); ?></div>
                <div style="margin-bottom: 5px;" ng-if="color_usable_qty == 0" ><b style="color:#f00"><?php esc_html_e('- Please deselect one to choose other color!','web-to-print-online-designer'); ?></b></div>

                <div style="margin-bottom: 5px;"><?php esc_html_e('- You can select maximum ','web-to-print-online-designer'); ?><b>{{pt_color.limit}}</b><?php esc_html_e(' color to use.','web-to-print-online-designer'); ?></div>
            </div>
        </div>
    </div>
</div>