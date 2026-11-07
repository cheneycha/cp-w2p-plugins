<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<div class="v-toolbox-text v-toolbox-item nbd-main-tab nbd-shadow"
     ng-class="stages[currentStage].states.isShowToolBox ? 'nbd-show' : ''"
     ng-show="stages[currentStage].states.isText || stages[currentStage].states.isGroupText"
     ng-style="stages[currentStage].states.toolboxStyle">
    <div class="v-triangle" data-pos="{{stages[currentStage].states.toolboxTriangle}}">
        <div class="header-box has-box-more">
            <span><?php _e('Format Text','web-to-print-online-designer'); ?></span>
            <ul class="link-breadcrumb">
               <li class="link-item nbd-nav-tab nbd-ripple active" data-tab="tab-main-box" title="<?php _e('Setting','web-to-print-online-designer'); ?>"><i class="nbd-icon-vista nbd-icon-vista-cog"></i></li>
               <li class="link-item nbd-nav-tab nbd-ripple" data-tab="tab-box-position" title="<?php _e('Position','web-to-print-online-designer'); ?>"><i class="nbd-icon-vista nbd-icon-vista-apps"></i></li>
                <li class="link-item nbd-nav-tab nbd-ripple" data-tab="tab-box-opacity" title="<?php _e('Opacity','web-to-print-online-designer'); ?>"><i class="nbd-icon-vista nbd-icon-vista-opacity"></i></li>
            </ul>
        </div>
        <div class="nbd-tab-contents">
            <div class="main-box nbd-tab-content active" data-tab="tab-main-box">
                <div class="toolbox-row" ng-if="settings.is_mobile">
                    <div><?php _e('Text content','web-to-print-online-designer'); ?></div>
                    <input style="font-size: 16px;width: 100%;border: 1px solid #ebebeb !important;padding: 5px;background: #fff;color: #04b591;display: block;line-height: 28px;border-radius: 4px;" ng-model="stages[currentStage].states.text.text" ng-change="setTextAttribute('text', stages[currentStage].states.text.text)" />
                </div>
                <div class="toolbox-row toolbox-first toolbox-font-family">
                    <div class="v-dropdown">
                        <div ng-if="settings.is_mobile"><?php _e('Font','web-to-print-online-designer'); ?></div>
                        <button class="v-btn btn-font-family v-btn-dropdown" title="<?php _e('Font family','web-to-print-online-designer'); ?>">
                            <span ng-style="{'font-family': stages[currentStage].states.text.font.alias}">{{stages[currentStage].states.text.font.name}}</span>
                            <i class="nbd-icon-vista nbd-icon-vista-expand-more v-dropdown-icon"></i></button>
                        <div class="v-dropdown-menu">

                            <div class="toolbar-font-search">
                                <input type="search" name="font-search" ng-model="resource.font.filter.search" placeholder="<?php _e('Search in','web-to-print-online-designer'); ?> {{resource.font.data.length}} <?php _e('fonts','web-to-print-online-designer'); ?>"/>
                                <i ng-show="resource.font.filter.search.length > 0" ng-click="resource.font.filter.search = ''" class="nbd-icon-vista nbd-icon-vista-clear"></i>
                            </div>

                            <ul class="items tab-scroll"
                                id="toolbar-font-familly-dropdown"
                                nbd-scroll="scrollLoadMore(container, type)"
                                data-container="#toolbar-font-familly-dropdown"
                                data-type="font" data-offset="40">
                                <div class="toolbar-menu-header">
                                    <div class="toolbar-header-line"></div>
                                    <div class="toolbar-separator">All Fonts</div>
                                    <div class="toolbar-header-line"></div>
                                </div>
                                <li class="item"
                                    ng-class="font.alias == stages[currentStage].states.text.fontFamily ? 'active' : ''"
                                    ng-click="setTextAttribute('fontFamily', font.alias)"
                                    ng-repeat="font in resource.font.filteredFonts"
                                    repeat-end="onEndRepeat('font')"
                                    data-font="font"
                                    font-on-load
                                    load-font-fail-action="loadFontFailAction(font)"
                                    data-preview="settings.subsets[font.subset]['preview_text']" >
                                <span class="font-left"
                                      style="font-family: '{{font.alias}}',-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif; font-size: 14px">
                                    {{font.name}}
                                </span>
                                    <span class="font-right" ng-if="['all', 'latin', 'latin-ext', 'vietnamese'].indexOf(font.subset) < 0">{{settings.subsets[font.subset]['preview_text']}}</span>
                                    <i class="nbd-icon-vista nbd-icon-vista-done checked"></i>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <?php include __DIR__ . '/../toollock.php'?>
                <div class="toolbox-row toolbox-second toolbox-align">
                    <ul class="items v-assets">
                        <li class="item v-asset item-align-left"
                            ng-click="setTextAttribute('textAlign', 'left')"
                            ng-class="stages[currentStage].states.text.textAlign == 'left' ? 'active' : ''"
                            ng-if="settings['nbdesigner_text_align_left'] == '1'"
                            title="<?php _e('Align left','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-align-left"></i>
                        </li>
                        <li class="item v-asset item-align-left"
                            ng-click="setTextAttribute('textAlign', 'center')"
                            ng-class="stages[currentStage].states.text.textAlign == 'center' ? 'active' : ''"
                            ng-if="settings['nbdesigner_text_align_center'] == '1'"
                            title="<?php _e('Align center','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-align-center"></i>
                        </li>
                        <li class="item v-asset item-align-left"
                            ng-click="setTextAttribute('textAlign', 'right')"
                            ng-class="stages[currentStage].states.text.textAlign == 'right' ? 'active' : ''"
                            ng-if="settings['nbdesigner_text_align_right'] == '1'"
                            title="<?php _e('Align right','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-align-right"></i>
                        </li>
                        <li class="item v-asset item-align-left"
                            ng-click="setTextAttribute('fontWeight', stages[currentStage].states.text.fontWeight == 'bold' ? 'normal' : 'bold')"
                            ng-class="{'active': stages[currentStage].states.text.fontWeight == 'bold', 'nbd-disabled': !(stages[currentStage].states.text.font.file.b && ( stages[currentStage].states.text.fontStyle != 'italic' || ( stages[currentStage].states.text.fontStyle == 'italic' && stages[currentStage].states.text.font.file.bi ) ))}"
                            title="<?php _e('Text bold','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-bold"></i>
                        </li>
                        <li class="item v-asset item-align-left"
                            ng-click="setTextAttribute('fontStyle', stages[currentStage].states.text.fontStyle == 'italic' ? 'normal' : 'italic')"
                            ng-class="{'active': stages[currentStage].states.text.fontStyle == 'italic','nbd-disabled' : !(stages[currentStage].states.text.font.file.i && ( stages[currentStage].states.text.fontWeight != 'bold' || ( stages[currentStage].states.text.fontWeight == 'bold' && stages[currentStage].states.text.font.file.bi ) ))}"
                            title="<?php _e('Text italic','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-italic"></i>
                        </li>
                    </ul>
                </div>
                <div class="toolbox-row toolbox-second toolbox-general">
                    <ul class="items v-assets">
                        <!--                    <li class="item v-asset item-reset" title="Reset" ng-click="addLine()"></li>-->
                        <li class="item v-asset item-reset" ng-click="resetLayer()" title="<?php _e('Reset','web-to-print-online-designer'); ?>" ng-class="stages[currentStage].states.hasReset ? '' : 'nbd-disabled'">
                            <i class="nbd-icon-vista nbd-icon-vista-refresh"></i>
                        </li>
                        <li class="item v-asset item-delete"
                            ng-click="deleteLayers()"
                            title="<?php _e('Delete layer','web-to-print-online-designer'); ?>">
                            <i class="nbd-icon-vista nbd-icon-vista-delete"></i>
                        </li>
                        <li class="item v-asset" style="visibility: hidden"></li>
                        <li class="item v-asset" style="visibility: hidden"></li>
                        <li class="item v-asset" style="visibility: hidden"></li>
                    </ul>
                </div>
                <div class="toolbox-row toolbox-last">
                    <div class="toolbox-font-size">
                        <div class="v-dropdown">
                            <button class="v-btn btn-font-size v-btn-dropdown" title="<?php _e('Font size','web-to-print-online-designer'); ?>">
                                <input class="toolbar-input" type="text" name="font-size" value="12"
                                       ng-keyup="$event.keyCode == 13 && setTextAttribute('fontSize', stages[currentStage].states.text.ptFontSize)"
                                       ng-model="stages[currentStage].states.text.ptFontSize"/>
                                <i class="nbd-icon-vista nbd-icon-vista-arrows"></i>
                            </button>
                            <div class="v-dropdown-menu">
                                <ul class="items tab-scroll">
                                    <li class="item"
                                        ng-click="setTextAttribute('fontSize', fontsize)"
                                        ng-class="stages[currentStage].states.text.ptFontSize == fontsize ? 'active' : ''"
                                        ng-repeat="fontsize in listFontSizeInPt">
                                        <span>{{fontsize}}</span>
                                        <i class="nbd-icon-vista nbd-icon-vista-done checked"></i>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="toolbox-color-palette">
                        <div class="v-dropdown">
                            <button class="v-btn btn-color v-btn-dropdown" ng-click="globalPicker.color = stages[currentStage].states.text.fill" title="<?php _e('Text color','web-to-print-online-designer'); ?>">
                                <span class="color-selected" ng-style="{'background-color': stages[currentStage].states.text.fill}"></span>
                                <i class="nbd-icon-vista nbd-icon-vista-expand-more v-dropdown-icon"></i>
                            </button>
                            <div class="v-dropdown-menu">
                                <?php include __DIR__ . '/../color-palette.php'?>
                            </div>
                        </div>
                    </div>
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
                            title="Bring to front">
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
            <div class="main-footer">
                <ul class="items">
<!--                    <li class="item item-reset" title="Reset">-->
<!--                        <i class="nbd-icon-vista nbd-icon-vista-refresh"></i>-->
<!--                    </li>-->
                    <li class="item item-done" title="<?php _e('Done','web-to-print-online-designer'); ?>" ng-click="onClickDone()">
                        <i class="nbd-icon-vista nbd-icon-vista-done"></i>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>