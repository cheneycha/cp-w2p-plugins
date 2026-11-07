<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<div class="v-toolbox-group v-toolbox-item nbd-main-tab nbd-shadow" ng-class="stages[currentStage].states.isShowToolBox ? 'nbd-show' : ''" ng-show="stages[currentStage].states.isGroup" ng-style="stages[currentStage].states.toolboxStyle">
    <div class="v-triangle" data-pos="{{stages[currentStage].states.toolboxTriangle}}">
        <div class="header-box has-box-more">
            <span><?php _e('Group','web-to-print-online-designer'); ?></span>
            <ul class="link-breadcrumb">
                <li class="link-item nbd-nav-tab nbd-ripple active" data-tab="tab-main-box" title="<?php _e('Setting','web-to-print-online-designer'); ?>"><i class="nbd-icon-vista nbd-icon-vista-cog"></i></li>
                <li class="link-item nbd-nav-tab nbd-ripple" data-tab="tab-box-position" title="<?php _e('Position','web-to-print-online-designer'); ?>"><i class="nbd-icon-vista nbd-icon-vista-apps"></i></li>
                <li class="link-item nbd-nav-tab nbd-ripple" data-tab="tab-box-opacity" title="<?php _e('Opacity','web-to-print-online-designer'); ?>"><i class="nbd-icon-vista nbd-icon-vista-opacity"></i></li>
            </ul>
        </div>
        <div class="nbd-tab-contents">
            <div class="main-box nbd-tab-content active" data-tab="tab-main-box">
                <?php include __DIR__ . '/../toollock.php'?>
                <div class="toolbox-row toolbox-second toolbox-general">
                    <ul class="items v-assets">
                        <li class="item v-asset item-align-vertical-center"
                            ng-click="alignLayer('vertical')"
                            title="<?php _e('Align Vertical Center','web-to-print-online-designer'); ?>">
                            <i class="rotate90 nbd-icon-vista nbd-icon-vista-vertical-align-center"></i>
                        </li>
                        <li class="item v-asset item-align-horizontal-center"
                            ng-click="alignLayer('horizontal')"
                            title="<?php _e('Align Horizontal Center','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-vertical-align-center"></i>
                        </li>
                        <li class="item v-asset item-align-left"
                            ng-click="alignLayer('left')"
                            title="<?php _e('Align Left','web-to-print-online-designer'); ?>">
                            <i class="rotate-90 nbd-icon-vista nbd-icon-vista-vertical-align-top"></i>
                        </li>
                        <li class="item v-asset item-align-right"
                            ng-click="alignLayer('right')"
                            title="<?php _e('Align Right','web-to-print-online-designer'); ?>">
                            <i class="rotate90 nbd-icon-vista nbd-icon-vista-vertical-align-top"></i>
                        </li>
                        <li class="item v-asset item-align-top"
                            ng-click="alignLayer('top')"
                            title="<?php _e('Align Top','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-vertical-align-top"></i>
                        </li>
                    </ul>
                </div>

                <div class="toolbox-row">
                    <ul class="items v-assets">
                        <li class="item v-asset item-align-bottom"
                            ng-click="alignLayer('bottom')"
                            title="<?php _e('Align Bottom','web-to-print-online-designer'); ?>">
                            <i class="rotate180 nbd-icon-vista nbd-icon-vista-vertical-align-top"></i>
                        </li>
                        <li class="item v-asset item-distribute-horizontal"
                            ng-click="alignLayer('dis-horizontal')"
                            title="<?php _e('Distribute Horizontal','web-to-print-online-designer'); ?>">
                            <i class="rotate180 nbd-icon-vista nbd-icon-vista-dis-horizontal"></i>
                        </li>
                        <li class="item v-asset item-distribute-vertical"
                            ng-click="alignLayer('dis-vertical')"
                            title="<?php _e('Distribute-vertical','web-to-print-online-designer'); ?>">
                            <i class="rotate180 nbd-icon-vista nbd-icon-vista-dis-vertical"></i>
                        </li>
                        <li class="item v-asset" style="visibility: hidden"></li>
                        <li class="item v-asset" style="visibility: hidden"></li>
                    </ul>
                </div>

                <div class="toolbox-row toolbox-delete">
                    <ul class="items v-assets">
                        <li class="item v-asset item-delete"
                            ng-click="deleteLayers()"
                            title="<?php _e('Delete all layer','web-to-print-online-designer'); ?>">
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
                        <li class="item v-asset item-align-left" ng-click="rotateLayer('90cw')" title="Rotate">
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