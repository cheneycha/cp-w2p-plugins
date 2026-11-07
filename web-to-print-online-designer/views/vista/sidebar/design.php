<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<div id="tab-design" class="v-tab-content active nbd-main-tab">
    <ul class="nbd-nav-tabs">
        <?php if( $product_data["option"]['admindesign'] != "0" ): ?>
        <li class="nbd-nav-tab active" data-tab="nbd-tab-design-workflow"><span class="v-title"><?php _e('Template','web-to-print-online-designer'); ?></span></li>
        <?php endif; ?>
        <li  class="nbd-nav-tab <?php if( $product_data["option"]['admindesign'] == "0" ) echo 'active' ?>" data-tab="nbd-tab-bg-color"><span class="v-title"><?php _e('Background','web-to-print-online-designer'); ?></span></li>
    </ul>

    <div class="v-content nbd-tab-contents" data-action="no">
        <?php if( $product_data["option"]['admindesign'] != "0" ): ?>
        <div style="margin-bottom: 20px; height: 100%;" class="layout nbd-tab-content active" id="nbd-tab-design-workflow" data-tab="nbd-tab-design-workflow"  nbd-scroll="scrollLoadMore(container, type)" data-container="#nbd-tab-design-workflow" data-type="globalTemplate" data-offset="30">
            <div class="tab-scroll" style="height: 100%;">
                <div class="main-scrollbar">
                    <div class="short-design" style="display: none">
                        <button class="v-btn btn-svg-upload"><?php _e('Upload Svg','web-to-print-online-designer'); ?></button>
                        <button class="v-btn btn-add-text"><?php _e('Add Text','web-to-print-online-designer'); ?></button>
                    </div>
                    <div class="items">
                        <div class="item" ng-repeat="temp in resource.templates" ng-click="insertTemplate(false, temp)">
                            <img style="cursor: pointer;" ng-src="{{temp.thumbnail}}" alt="<?php _e('Template','web-to-print-online-designer'); ?>">
                        </div>
                        <div class="item" ng-repeat="temp in resource.globalTemplate.data" ng-click="insertGlobalTemplate(temp.id, $index)">
                            <img style="cursor: pointer;" ng-src="{{temp.thumbnail}}" alt="<?php _e('Template','web-to-print-online-designer'); ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <div  class="design-color nbd-tab-content <?php if( $product_data["option"]['admindesign'] == "0" ) echo 'active' ?>" id="nbd-tab-bg-color" data-tab="nbd-tab-bg-color">
            <div class="tab-scroll bg-color">
                <div class="main-scrollbar">
                    <div class="main-color">
                        <div class="nbd-color-palette show">
                            <div class="nbd-color-palette-inner">
                                <div class="working-palette" ng-if="settings['nbdesigner_show_all_color'] == 'yes'" style="margin-bottom: 10px">
                                    <h3 class="color-palette-label"><?php _e('Set color','web-to-print-online-designer'); ?></h3>
                                    <ul class="main-color-palette tab-scroll">
                                        <li class="color-palette-add" ng-style="{'background-color': currentColor}"></li>
                                        <li ng-repeat="color in listAddedColor track by $index"
                                            ng-click="changeBackgroundCanvas(color)"
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
                                            ng-click="changeBackgroundCanvas(color)"
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
                                        <li ng-repeat="color in __colorPalette track by $index" ng-class="{'first-left': $first, 'last-right': $last, 'first-right': $index == 4,'last-left': $index == (palette.length - 5)}" ng-click="changeBackgroundCanvas(color)" class="color-palette-item" data-color="{{color}}" title="{{color}}" ng-style="{'background': color}"></li>
                                    </ul>   
                                </div>
                                <div class="nbd-text-color-picker"
                                     ng-class="showTextColorPicker ? 'active' : ''"
                                     id="nbd-text-color-picker">
                                    <spectrum-colorpicker
                                        ng-model="bgCurrentColor"
                                        options="{
                                            preferredFormat: 'hex',
                                            color: '#fff',
                                            flat: true,
                                            showButtons: false,
                                            showInput: true,
                                            containerClassName: 'nbd-sp'
                                        }">
                                    </spectrum-colorpicker>
                                    <div>
                                        <button class="v-btn"
                                                ng-click="changeBackgroundCanvas(bgCurrentColor);">
                                            <?php _e('Choose','web-to-print-online-designer'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>