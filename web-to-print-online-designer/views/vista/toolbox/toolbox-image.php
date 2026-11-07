<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<div class="v-toolbox-image v-toolbox-item nbd-main-tab nbd-shadow" ng-class="stages[currentStage].states.isShowToolBox ? 'nbd-show' : ''" ng-show="stages[currentStage].states.isImage" ng-style="stages[currentStage].states.toolboxStyle">
    <div class="v-triangle" data-pos="{{stages[currentStage].states.toolboxTriangle}}">
        <div class="header-box has-box-more" style="margin-bottom: 20px">
            <span><?php _e('Image edit','web-to-print-online-designer'); ?></span>
            <ul class="link-breadcrumb">
                <li class="link-item nbd-nav-tab nbd-ripple active" data-tab="tab-main-box" title="<?php _e('Setting','web-to-print-online-designer'); ?>"><i class="nbd-icon-vista nbd-icon-vista-cog"></i></li>
                <li class="link-item nbd-nav-tab nbd-ripple" data-tab="tab-box-position" title="<?php _e('Position','web-to-print-online-designer'); ?>"><i class="nbd-icon-vista nbd-icon-vista-apps"></i></li>
                <li class="link-item nbd-nav-tab nbd-ripple" data-tab="tab-box-opacity" title="<?php _e('Opacity','web-to-print-online-designer'); ?>"><i class="nbd-icon-vista nbd-icon-vista-opacity"></i></li>
            </ul>
        </div>
        <div class="nbd-tab-contents">
            <div class="main-box nbd-tab-content active" data-tab="tab-main-box">
                <div class="toolbox-row toolbox-first">
                    <div class="toolbox-general">
                        <ul class="v-assets items">
                            <li class="item" title="<?php _e('Rotate','web-to-print-online-designer'); ?>" ng-click="rotateLayer('90cw')">
                                <div class="v-asset"><i class="nbd-icon-vista nbd-icon-vista-rotate-right"></i></div>
                                <span class="v-asset-title"><?php _e('rotate','web-to-print-online-designer'); ?></span>
                            </li>
                            <li class="item" ng-click="resetLayer()" title="<?php _e('Reset','web-to-print-online-designer'); ?>" ng-class="stages[currentStage].states.hasReset ? '' : 'nbd-disabled'">
                                <div class="v-asset"><i class="nbd-icon-vista nbd-icon-vista-refresh"></i></div>
                                <span class="v-asset-title"><?php _e('reset','web-to-print-online-designer'); ?></span>
                            </li>
                            <?php if ($mode3Task == 'create' && current_user_can('edit_nbd_template')) :?>
                                <li
                                        ng-class="stages[currentStage].states.elementUpload ? 'active' : ''"
                                        ng-click="setLayerAttribute('elementUpload', !stages[currentStage].states.elementUpload)"
                                        class="item"
                                        ng-show="stages[currentStage].states.isImage && isTemplateMode"
                                        title="<?php _e('Replace image','web-to-print-online-designer'); ?>">
                                    <div class="v-asset"><i class="nbd-icon-vista nbd-icon-vista-replace-image"></i></div>
                                    <span class="v-asset-title"><?php _e('replace','web-to-print-online-designer'); ?></span>
                                </li>
                            <?php endif; ?>
                            <li class="item" title="<?php _e('Remove','web-to-print-online-designer'); ?>" ng-click="deleteLayers()">
                                <div class="v-asset"><i class="nbd-icon-vista nbd-icon-vista-delete"></i></div>
                                <span class="v-asset-title"><?php _e('remove','web-to-print-online-designer'); ?></span>
                            </li>
                        </ul>
                    </div>

                    <div class="toolbox-directional">
                        <div class="item-direct rotate180" ng-click="moveLayer('up',1)"><i class="nbd-icon-vista nbd-icon-vista-expand-more"></i></div>
                        <div class="item-direct rotate-90" ng-click="moveLayer('right',1)"><i class="nbd-icon-vista nbd-icon-vista-expand-more"></i></div>
                        <div class="item-direct" ng-click="moveLayer('down',1)"><i class="nbd-icon-vista nbd-icon-vista-expand-more"></i></div>
                        <div class="item-direct rotate90" ng-click="moveLayer('left',1)"><i class="nbd-icon-vista nbd-icon-vista-expand-more"></i></div>
                    </div>
                </div>

                <?php if ($mode3Task == 'create' && current_user_can('edit_nbd_template')) :?>
                    <div class="toolbox-row toolbox-second toolbox-lock" ng-if="!stages[currentStage].states.isGroup">
                        <ul class="items v-assets" style="width: calc(100% - 88px)">
                            <li class="item v-asset item-lock-horizontal-movement"
                                ng-class="!stages[currentStage].states.lockMovementX ? '' : 'active'"
                                ng-click="setLayerAttribute('lockMovementX', !stages[currentStage].states.lockMovementX)"
                                ng-show="stages[currentStage].states.isLayer && isTemplateMode"
                                title="<?php _e('Lock horizontal movement','web-to-print-online-designer'); ?>">
                                <i class="nbd-icon-vista nbd-icon-vista-arrows-h"></i>
                            </li>
                            <li class="item v-asset item-lock-vertical-movement"
                                ng-class="!stages[currentStage].states.lockMovementY ? '' : 'active'"
                                ng-click="setLayerAttribute('lockMovementY', !stages[currentStage].states.lockMovementY)"
                                ng-show="stages[currentStage].states.isLayer && isTemplateMode"
                                title="<?php _e('Lock vertical movement','web-to-print-online-designer'); ?>">
                                <i class="nbd-icon-vista nbd-icon-vista-arrows-v"></i>
                            </li>
                            <li class="item v-asset item-lock-horizontal-scaling"
                                ng-class="!stages[currentStage].states.lockScalingX ? '' : 'active'"
                                ng-click="setLayerAttribute('lockScalingX', !stages[currentStage].states.lockScalingX)"
                                ng-show="stages[currentStage].states.isLayer && isTemplateMode"
                                title="<?php _e('Lock horizontal scaling','web-to-print-online-designer'); ?>">
                                <i class="nbd-icon-vista nbd-icon-vista-expand horizontal horizontal-x"><sub>x</sub></i>
                            </li>
                            <li class="item v-asset item-lock-vertical-scaling"
                                ng-class="!stages[currentStage].states.lockScalingY ? '' : 'active'"
                                ng-click="setLayerAttribute('lockScalingY', !stages[currentStage].states.lockScalingY)"
                                ng-show="stages[currentStage].states.isLayer && isTemplateMode"
                                title="<?php _e('Lock vertical scaling','web-to-print-online-designer'); ?>">
                                <i class="nbd-icon-vista nbd-icon-vista-expand horizontal horizontal-y"><sub>y</sub></i>
                            </li>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="toolbox-row toolbox-last toolbox-zoom">
                    <div class="v-assets">
                        <div class="v-asset" title="<?php _e('Zoom out','web-to-print-online-designer'); ?>" ng-click="scaleLayer('-')"><i class="nbd-icon-vista nbd-icon-vista-zoom-out"></i></div>
                        <div class="v-ranges" style="display: none">
                            <div class="main-track">
                                <input class="slide-input" type="range" step="1" min="0" max="100">
                                <span class="range-track"></span>
                            </div>
                        </div>
                        <div class="v-asset" title="<?php _e('Zoom in','web-to-print-online-designer'); ?>" ng-click="scaleLayer('+')"><i class="nbd-icon-vista nbd-icon-vista-zoom-in"></i></div>
                    </div>
                </div>
            </div>
            <div class="main-box-more nbd-tab-content" data-tab="tab-box-position">
                <div class="toolbox-row toolbox-align">
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
                        <li class="item v-asset item-align-left" style="visibility: hidden">
                            <i class="nbd-icon-vista nbd-icon-vista-top-left"></i>
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
                <div class="toolbox-row toolbox-align">
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