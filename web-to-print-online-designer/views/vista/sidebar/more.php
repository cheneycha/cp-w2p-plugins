<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<div id="tab-element" class="v-tab-content v-more-toolbar" nbd-scroll="scrollLoadMore(container, type)" data-container="#tab-element" data-type="element" data-offset="20">
    <span class="v-title"><?php _e('More','web-to-print-online-designer'); ?></span>
    <div class="nbd-search v-action">
        <input ng-class="(resource.element.type != 'icon' || !resource.element.onclick) ? 'nbd-disabled' : ''" ng-keyup="$event.keyCode == 13 && getMedia(resource.element.type, 'search')" type="search" name="search" placeholder="<?php _e('Search cliparts','web-to-print-online-designer'); ?>" ng-model="resource.element.contentSearch"/>
        <i class="nbd-icon-vista nbd-icon-vista-search" ng-click="getMedia(resource.element.type, 'search')"></i>
    </div>
    <div class="v-content">
        <div class="tab-scroll">
            <div class="main-scrollbar">
                <div class="v-elements">
                    <div class="main-items">
                        <div class="items">
                            <div class="item"
                                 data-type="draw"
                                 data-api="false"
                                 ng-click="onClickTab('draw', 'element')"
                                 ng-if="settings['nbdesigner_enable_draw'] == 'yes' && !settings.is_mobile">
                                <div class="item-icon"><i class="nbd-icon-vista nbd-icon-vista-drawing"></i></div>
                                <div class="item-info">
                                    <span class="item-name" title="Draw"><?php _e('Draw','web-to-print-online-designer'); ?></span>
                                </div>
                            </div>
                            <div ng-if="settings['nbdesigner_enable_clipart'] == 'yes'" data-api="false" class="item" data-type="shapes" data-api="false" ng-click="onClickTab('shape', 'element')">
                                <div class="item-icon"><i class="nbd-icon-vista nbd-icon-vista-shapes"></i></div>
                                <div class="item-info">
                                    <span class="item-name" title="Shapes"><?php _e('Shapes','web-to-print-online-designer'); ?></span>
                                </div>
                            </div>
                            <div ng-if="settings['nbdesigner_enable_clipart'] == 'yes'" data-api="false" ng-click="onClickTab('icon', 'element')" class="item" data-type="icons" data-api="false">
                                <div class="item-icon"><i class="nbd-icon-vista nbd-icon-vista-diamond"></i></div>
                                <div class="item-info">
                                    <span class="item-name" title="Icons"><?php _e('Icons','web-to-print-online-designer'); ?></span>
                                </div>
                            </div>
                            <div ng-if="settings['nbdesigner_enable_qrcode'] == 'yes'" class="item" data-type="qr-code" data-api="false" ng-click="onClickTab('qrcode', 'element')">
                                <div class="item-icon"><i class="nbd-icon-vista nbd-icon-vista-qrcode"></i></div>
                                <div class="item-info">
                                    <span class="item-name" title="QR Code"><?php _e('QR Code','web-to-print-online-designer'); ?></span>
                                </div>
                            </div>
                            <div class="item" data-type="none"></div>
                        </div>
                        <div class="pointer"></div>
                    </div>
                    <div class="result-loaded">
                        <div class="content-items">
                            <div class="content-item type-draw" data-type="draw">
                                <div class="main-type">
                                    <span class="heading-title"><?php _e('Free Drawing Mode','web-to-print-online-designer'); ?></span>
                                    <div class="brush v-dropdown">
                                        <button class="v-btn v-btn-dropdown">
                                            <?php _e('Brush','web-to-print-online-designer'); ?>
                                            <i class="nbd-icon-vista nbd-icon-vista-arrow-drop-down v-dropdown-icon"></i>
                                        </button>
                                        <div class="v-dropdown-menu" data-pos="left">
                                            <ul class="tab-scroll">
                                                <li
                                                    ng-class="resource.drawMode.brushType == 'Pencil' ? 'active' : ''"
                                                    ng-click="resource.drawMode.brushType = 'Pencil';changeBush()">
                                                    <span><?php _e('Pencil','web-to-print-online-designer'); ?></span>
                                                </li>
                                                <li
                                                    ng-class="resource.drawMode.brushType == 'Circle' ? 'active' : ''"
                                                    ng-click="resource.drawMode.brushType = 'Circle';changeBush()">
                                                    <span><?php _e('Circle','web-to-print-online-designer'); ?></span>
                                                </li>
                                                <li
                                                    ng-class="resource.drawMode.brushType == 'Spray' ? 'active' : ''"
                                                    ng-click="resource.drawMode.brushType = 'Spray';changeBush()">
                                                    <span><?php _e('Spray','web-to-print-online-designer'); ?></span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <ul class="v-ranges">
                                        <li class="range range-brightness">
                                            <label><?php _e('Brush Width','web-to-print-online-designer'); ?></label>
                                            <div class="main-track">
                                                <input class="slide-input" type="range" step="1" min="1" max="100" value="50"
                                                   ng-model="resource.drawMode.brushWidth"
                                                   ng-change="changeBush()">
                                                <span class="range-track"></span>
                                            </div>
                                            <span class="value-display">{{resource.drawMode.brushWidth}}</span>
                                        </li>
                                    </ul>

                                    <div class="draw-color">
                                        <div class="nbd-color-palette show">
                                            <div class="nbd-color-palette-inner">
                                                <div class="working-palette" ng-if="settings['nbdesigner_show_all_color'] == 'yes'">
                                                    <h3 class="color-palette-label"><?php _e('Set color','web-to-print-online-designer'); ?></h3>
                                                    <ul class="main-color-palette tab-scroll" style="max-height: 150px">
                                                        <li class="color-palette-add"
                                                            ng-init="showBrushColorPicker = false"
                                                            ng-click="showBrushColorPicker = !showBrushColorPicker;"
                                                            ng-style="{'background-color': currentColor}">
                                                        </li>
                                                        <li ng-repeat="color in listAddedColor track by $index"
                                                            class="color-palette-item" data-color="{color}"
                                                            ng-click="resource.drawMode.brushColor=color; changeBush()"
                                                            title="{{color}}" ng-style="{'background-color': color}"></li>
                                                    </ul>
                                                </div>
                                                <div class="pinned-palette default-palette" ng-if="settings['nbdesigner_show_all_color'] == 'yes'">
                                                    <h3 class="color-palette-label"><?php _e('Default color','web-to-print-online-designer'); ?></h3>
                                                    <ul class="main-color-palette" ng-repeat="palette in resource.defaultPalette" style="margin-bottom: 10px">
                                                        <li ng-class="{'first-left': $first, 'last-right': $last}"
                                                            ng-repeat="color in palette track by $index"
                                                            ng-click="resource.drawMode.brushColor=color; changeBush()"
                                                            class="color-palette-item"
                                                            data-color="{{color}}"
                                                            title="{{color}}"
                                                            ng-style="{'background': color}"></li>
                                                    </ul>
                                                </div>
                                                <div class="working-palette" ng-if="settings['nbdesigner_show_all_color'] == 'no'">
                                                    <h3 class="color-palette-label"><?php _e('Set color','web-to-print-online-designer'); ?></h3>
                                                    <ul class="main-color-palette tab-scroll" style="max-height: 150px">
                                                        <li ng-repeat="color in __colorPalette track by $index"
                                                            class="color-palette-item" data-color="{color}"
                                                            ng-click="resource.drawMode.brushColor=color; changeBush()"
                                                            title="{{color}}" ng-style="{'background-color': color}"></li>
                                                    </ul>
                                                </div>
                                                <div class="nbd-text-color-picker" id="nbd-text-color-picker" style="z-index: 999;">
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
                                                        <button class="v-btn" ng-click="addColor();changeBush()"><?php _e('Choose','web-to-print-online-designer'); ?></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="content-item type-shapes" data-type="shapes" id="nbd-shape-wrap">
                                <div class="mansory-wrap">
                                    <div nbd-drag="art.url" extenal="true" type="svg" class="mansory-item" ng-click="addSvgFromMedia(art, $index)" ng-repeat="art in resource.shape.data" repeat-end="onEndRepeat('shape')"><img ng-src="{{art.url}}"><span class="photo-desc">{{art.name}}</span></div>
                                </div>
                            </div>
                            <div class="content-item type-icons" data-type="icons" id="nbd-icon-wrap">
                                <div class="mansory-wrap">
                                    <div nbd-drag="art.url" extenal="true" type="svg" class="mansory-item" ng-click="addSvgFromMedia(art, $index)" ng-repeat="art in resource.icon.data" repeat-end="onEndRepeat('icon')">
                                        <img ng-src="{{art.url}}"><span class="photo-desc">{{art.name}}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="content-item type-qrcode" data-type="qr-code">
                                <div class="main-type">
                                    <div class="main-input">
                                        <input ng-model="resource.qrText" type="text" class="nbd-input input-qrcode" name="qr-code" placeholder="https://yourcompany.com">
                                    </div>
                                    <button ng-class="resource.qrText != '' ? '' : 'nbd-disabled'" class="v-btn" ng-click="addQrCode()">
                                        <?php _e('Create QRCode','web-to-print-online-designer'); ?>
                                    </button>
                                    <div class="main-qrcode">

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="nbdesigner-gallery" id="nbdesigner-gallery"></div>
                    </div>
                    <div class="loading-photo" style="display: none; width: 40px; height: 40px;">
                        <svg class="circular" viewBox="25 25 50 50">
                            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="info-support">
            <span><?php _e('Facebook','web-to-print-online-designer'); ?></span>
            <i class="nbd-icon-vista nbd-icon-vista-clear close-result-loaded" ng-click="onClickTab('', 'element')"></i>
        </div>
    </div>

</div>