<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<div class="nbd-color-palette">
    <div class="nbd-color-palette-inner">
        <div class="working-palette" ng-if="settings['nbdesigner_show_all_color'] == 'yes'" style="margin-bottom: 10px">
            <h3 class="color-palette-label">Set color</h3>
            <ul class="main-color-palette tab-scroll">
                <li class="color-palette-add" ng-style="{'background-color': globalPicker.color}"></li>
                <li
                    ng-repeat="color in listAddedColor track by $index"
                    ng-click="changeFill(color)"
                    class="color-palette-item"
                    data-color="{{color}}"
                    title="{{color}}"
                    ng-style="{'background-color': color}">
                </li>
            </ul>
        </div>
        <div class="pinned-palette default-palette" ng-if="settings['nbdesigner_show_all_color'] == 'yes'">
            <h3 class="color-palette-label"><?php _e('Default palette','web-to-print-online-designer'); ?></h3>
            <ul class="main-color-palette tab-scroll" ng-repeat="palette in resource.defaultPalette" style="margin-bottom: 5px; max-height: 80px">
                <li ng-class="{'first-left': $first, 'last-right': $last, 'first-right': $index == 4,'last-left': $index == (palette.length - 5)}"
                    ng-repeat="color in palette track by $index"
                    ng-click="changeFill(color)"
                    class="color-palette-item"
                    data-color="{{color}}"
                    title="{{color}}"
                    ng-style="{'background': color}">
                </li>
            </ul>
        </div>
        <div class="pinned-palette default-palette" ng-if="settings['nbdesigner_show_all_color'] == 'no'">
            <h3 class="color-palette-label"><?php _e('Color palette','web-to-print-online-designer'); ?></h3>
            <ul class="main-color-palette" style="margin-bottom: 15px;">
                <li ng-repeat="color in __colorPalette track by $index" ng-class="{'first-left': $first, 'last-right': $last, 'first-right': $index == 4,'last-left': $index == (palette.length - 5)}" ng-click="changeFill(color)" class="color-palette-item" data-color="{{color}}" title="{{color}}" ng-style="{'background': color}"></li>
            </ul>   
        </div> 
        <div class="nbd-text-color-picker"
             ng-class="showTextColorPicker ? 'active' : ''"
             id="nbd-text-color-picker">
            <spectrum-colorpicker
                ng-model="globalPicker.color"
                options="{
                    preferredFormat: 'hex',
                    flat: true,
                    showButtons: false,
                    showInput: true,
                    containerClassName: 'nbd-sp'
                }">
            </spectrum-colorpicker>
            <div>
                <button class="v-btn" ng-click="addColor();changeFill(globalPicker.color);">
                    <?php _e('Choose','web-to-print-online-designer'); ?>
                </button>
            </div>
        </div>
    </div>
</div>