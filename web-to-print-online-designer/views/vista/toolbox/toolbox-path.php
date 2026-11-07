<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<div class="v-toolbox-path v-toolbox-item nbd-main-tab nbd-shadow" ng-class="stages[currentStage].states.isShowToolBox ? 'nbd-show' : ''" ng-show="stages[currentStage].states.isPath || stages[currentStage].states.isShape" ng-style="stages[currentStage].states.toolboxStyle">
    <div class="v-triangle" data-pos="{{stages[currentStage].states.toolboxTriangle}}">
        <div class="header-box has-box-more">
            <span ng-if="stages[currentStage].states.isPath"><?php _e('Path','web-to-print-online-designer'); ?></span>
            <span ng-if="stages[currentStage].states.isShape"><?php _e('Shape','web-to-print-online-designer'); ?></span>
            <ul class="link-breadcrumb">
                <li class="link-item nbd-nav-tab nbd-ripple active" data-tab="tab-main-box" title="<?php _e('Setting','web-to-print-online-designer'); ?>"><i class="nbd-icon-vista nbd-icon-vista-cog"></i></li>
                <li class="link-item nbd-nav-tab nbd-ripple" data-tab="tab-box-position" title="<?php _e('Position','web-to-print-online-designer'); ?>"><i class="nbd-icon-vista nbd-icon-vista-apps"></i></li>
                <li class="link-item nbd-nav-tab nbd-ripple" data-tab="tab-box-opacity" title="<?php _e('Opacity','web-to-print-online-designer'); ?>"><i class="nbd-icon-vista nbd-icon-vista-opacity"></i></li>
            </ul>
        </div>
        <div class="nbd-tab-contents">
            <div class="main-box nbd-tab-content active" data-tab="tab-main-box">
                <?php include __DIR__ . '/../toollock.php'?>
                <div class="toolbox-row toolbox-second toolbox-color-palette">
                    <ul class="items" style="display: block;">
                        <li style="display: inline-block;margin-right: 5px;" class="item v-asset v-asset-margin item-color-palette nbdColorPalette" ng-click="stages[currentStage].states.svg.currentPath = $index" ng-repeat="path in stages[currentStage].states.svg.groupPath" end-repeat-color-picker>
                            <span ng-click="currentColor = path.color" style="width: 40px; height: 40px;display: inline-block;border: 1px solid #04b591; border-radius: 4px;cursor: pointer;" ng-style="{'background': path.color}" class="color-fill nbd-color-picker-preview" title="<?php _e('Color','web-to-print-online-designer'); ?>"></span>
                        </li>
                    </ul>
                    <div class="nbd-color-palette">
                        <div class="nbd-color-palette-inner">
                            <div class="working-palette" ng-if="settings['nbdesigner_show_all_color'] == 'yes'" style="margin-bottom: 10px">
                                <h3 class="color-palette-label">Set color</h3>
                                <ul class="main-color-palette tab-scroll">
                                    <li class="color-palette-add" ng-style="{'background-color': currentColor}"></li>
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
                                    <button class="v-btn"
                                            ng-click="addColor();changeFill(currentColor);">
                                        <?php _e('Choose','web-to-print-online-designer'); ?>
                                    </button>
                                </div>
                            </div>
                            <div class="v-triangle-box"></div>
                            <div class="close-block"><i class="nbd-icon-vista nbd-icon-vista-clear"></i></div>
                        </div>
                    </div>
                </div>
                <div class="toolbox-row toolbox-delete">
                    <ul class="items v-assets">
                        <li class="item v-asset item-delete"
                            ng-click="deleteLayers()"
                            title="<?php _e('Delete path','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-delete"></i>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="main-box-more nbd-tab-content" data-tab="tab-box-position">
                <div class="toolbox-row toolbox-first toolbox-align">
                    <ul class="items v-assets">
                        <li class="item v-asset item-align-left"
                            ng-click="translateLayer('vertical')"
                            title="<?php _e('Position center horizontal','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-vertical-align-center"></i>
                        </li>
                        <li class="item v-asset item-align-left"
                            ng-click="translateLayer('top-left')"
                            title="<?php _e('Position top right','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-top-left rotate-90"></i>
                        </li>
                        <li class="item v-asset item-align-left"
                            ng-click="translateLayer('top-center')"
                            title="<?php _e('Position top center','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-top-left rotate-45"></i>
                        </li>
                        <li class="item v-asset item-align-left"
                            ng-click="translateLayer('top-right')"
                            title="<?php _e('Position top left','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-top-left"></i>
                        </li>
                        <li class="item v-asset item-align-left" ng-click="setStackPosition('bring-front')"
                            title="<?php _e('Bring to front','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-bring-to-front"></i>
                        </li>
                    </ul>
                </div>
                <div class="toolbox-row toolbox-second toolbox-align">
                    <ul class="items v-assets">
                        <li class="item v-asset item-align-left"
                            ng-click="translateLayer('horizontal')"
                            title="<?php _e('Position center vertical','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-vertical-align-center rotate90"></i>
                        </li>
                        <li class="item v-asset item-align-left"
                            ng-click="translateLayer('middle-left')"
                            title="<?php _e('Position middle right','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-top-left rotate-135"></i>
                        </li>
                        <li class="item v-asset item-align-left"
                            ng-click="translateLayer('center')"
                            title="<?php _e('Position middle center','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-center"></i>
                        </li>
                        <li class="item v-asset item-align-left"
                            ng-click="translateLayer('middle-right')"
                            title="<?php _e('Position middle left','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-top-left rotate45"></i>
                        </li>
                        <li class="item v-asset item-align-left" ng-click="setStackPosition('bring-forward')"
                            title="<?php _e('Bring forward','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-bring-forward"></i>
                        </li>
                    </ul>
                </div>
                <div class="toolbox-row toolbox-three toolbox-align">
                    <ul class="items v-assets">
                        <li class="item v-asset item-align-left" ng-click="rotateLayer('90cw')" title="<?php _e('Rotate','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-rotate-right"></i>
                        </li>
                        <li class="item v-asset item-align-left"
                            ng-click="translateLayer('bottom-left')"
                            title="<?php _e('Position bottom left','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-top-left rotate-180"></i>
                        </li>
                        <li class="item v-asset item-align-left"
                            ng-click="translateLayer('bottom-center')"
                            title="<?php _e('Position bottom center','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-top-left rotate135"></i>
                        </li>
                        <li class="item v-asset item-align-left"
                            ng-click="translateLayer('bottom-right')"
                            title="<?php _e('Position bottom right','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-top-left rotate90"></i>
                        </li>
                        <li class="item v-asset item-align-left" ng-click="setStackPosition('send-backward')"
                            title="<?php _e('Sent to backward','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-sent-to-backward"></i>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="main-box-more nbd-tab-content" data-tab="tab-box-opacity">
                <div class="toolbox-row toolbox-first toolbox-align">
                    <div style="display: flex;justify-content: space-between; align-items: center">
                        <div>Opacity</div>
                        <div class="v-ranges">
                            <div class="main-track" style="display: flex">
                                <input class="slide-input" type="range" step="1" min="0" max="100" ng-change="setTextAttribute('opacity', stages[currentStage].states.opacity / 100)" ng-model="stages[currentStage].states.opacity">
                                <span class="range-track"></span>
                            </div>
                        </div>
                        <div class="v-range-model">{{stages[currentStage].states.opacity}}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-box">
            <div class="main-footer" title="<?php _e('Done','web-to-print-online-designer'); ?>" ng-click="onClickDone()">
                <i class="nbd-icon-vista nbd-icon-vista-done"></i>
            </div>
        </div>
    </div>
</div>