<div class="toolbar-image" ng-show="stages[currentStage].states.isImage">
    <ul class="nbd-main-menu">
        <li class="menu-item menu-filter" ng-show="settings.enableImageFilter">
            <i class="icon-nbd icon-nbd-baseline-tune nbd-tooltip-hover" title="<?php esc_html_e('Filter','web-to-print-online-designer'); ?>"></i>
            <div class="sub-menu" data-pos="left">
                <div class="image-filters" ng-if="settings.enableImageFilter && stages[currentStage].states.isImage">
                    <div class="image-filter ng-scope" ng-click="removeImageFilters()" title="<?php esc_html_e('Clear filters','web-to-print-online-designer'); ?>">
                        <img ng-src="{{settings.assets_url + 'images/filter/filter.png'}}" />
                    </div>
                    <div class="image-filter" ng-click="addImageFilter( filter )" ng-class="stages[currentStage].states.filters[filter] ? 'active' : ''" ng-repeat="filter in availableFilters" title="{{filter}}">
                        <img ng-src="{{settings.assets_url + 'images/filter/f_' + filter + '.png'}}" />
                        <i class="icon-nbd icon-nbd-fomat-done" ></i>
                    </div>
                </div>
                <div class="filter-ranges">
                    <ul class="pc-ranges">
                        <li class="pc-range">
                            <div class="pc-range-inner">
                                <label><?php esc_html_e('Brightness','web-to-print-online-designer'); ?></label>
                                <div class="main-track" data-value="{{stages[currentStage].states.baseFilters['brightness']}}">
                                    <input ng-model="stages[currentStage].states.baseFilters['brightness']" ng-model-options="{ debounce: 200 }" ng-change="addImageFilterBase('brightness', stages[currentStage].states.baseFilters['brightness'])" class="slide-input" type="range" step="1" min="-50" max="50">
                                    <span class="range-track"></span>
                                    <span class="snap-guide"></span>
                                </div>
                            </div>
                        </li>
                        <li class="pc-range">
                            <div class="pc-range-inner">
                                <label><?php esc_html_e('Saturation','web-to-print-online-designer'); ?></label>
                                <div class="main-track" data-value="{{stages[currentStage].states.baseFilters['saturation']}}">
                                    <input ng-model="stages[currentStage].states.baseFilters['saturation']" ng-model-options="{ debounce: 200 }" ng-change="addImageFilterBase('saturation', stages[currentStage].states.baseFilters['saturation'])" class="slide-input" type="range" step="1" min="-100" max="100">
                                    <span class="range-track"></span>
                                    <span class="snap-guide"></span>
                                </div>
                            </div>
                        </li>
                        <li class="pc-range">
                            <div class="pc-range-inner">
                                <label><?php esc_html_e('Contrast','web-to-print-online-designer'); ?></label>
                                <div class="main-track" data-value="{{stages[currentStage].states.baseFilters['contrast1']}}">
                                    <input ng-model="stages[currentStage].states.baseFilters['contrast1']" ng-model-options="{ debounce: 200 }" ng-change="addImageFilterBase('contrast1', stages[currentStage].states.baseFilters['contrast1'])" class="slide-input" type="range" step="1" min="-50" max="50">
                                    <span class="range-track"></span>
                                    <span class="snap-guide"></span>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </li>
        <li class="menu-item menu-crop" ng-click="initCrop()">
            <i class="icon-nbd icon-nbd-round-crop nbd-tooltip-hover" title="<?php esc_html_e('Crop','web-to-print-online-designer'); ?>"></i>
        </li>
        <li class="menu-item menu-crop" ng-show="!stages[currentStage].states.isMasked">
            <i class="nbd-tooltip-hover nbd-svg-icon" title="<?php esc_html_e('Create clipping mask','web-to-print-online-designer'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24px" height="24px"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
            </i>
            <div class="sub-menu sub-menu-shape_mask" data-pos="left" >
                <div class="shape_mask-wrapper">
                    <span class="shape_mask shape-type-{{n}}" ng-click="createClippingMask(n)" ng-repeat="n in [] | range:25"></span>
                </div>
                <div class="custom_shape_mask-wrapper">
                    <div><?php esc_html_e('Custom Mask','web-to-print-online-designer'); ?></div>
                    <textarea class="form-control hover-shadow nbdesigner_svg_code" rows="5" ng-change="getPathCommand()" ng-model="svgPath" placeholder="<?php esc_html_e('Enter svg code','web-to-print-online-designer'); ?>"/></textarea>
                    <button ng-class="pathCommand !='' ? '' : 'nbd-disabled'" class="nbd-button" ng-click="createClippingMask(-1)"><?php esc_html_e('Apply Mask','web-to-print-online-designer'); ?></button>
                </div>
            </div>
        </li>
        <?php  if( nbdesigner_get_option('nbdesigner_mask_layer') == 'yes' ): ?>
        <li class="menu-item menu-crop" ng-click="getMaskLayerList()" ng-show="!stages[currentStage].states.isMaskedLayer">
            <i class="nbd-tooltip-hover nbd-svg-icon" title="<?php esc_html_e('Mask layer','web-to-print-online-designer'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="22px" height="22px" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16">
                    <path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z"/>
                </svg>
            </i>
            <div class="sub-menu sub-menu-shape_mask" data-pos="left" >
                <div class="shape_mask_layer-wrapper">
                    <h4><?php esc_html_e('Select mask layer','web-to-print-online-designer'); ?></h4>
                    <div>
                        <ul class="menu-mask-layer">
                            <li class="menu-item item-layer-text" ng-click="createMaskLayer(maskLayer.itemId)" ng-repeat="maskLayer in maskLayerList | reverse">
                                <div class="item-mask-layer-inner">
                                    <i ng-if="maskLayer.type != 'image'" class="icon-nbd item-left" ng-class="'icon-nbd-' + maskLayer.icon_class"></i>
                                    <img ng-if="maskLayer.type == 'image'" ng-src="{{maskLayer.src}}" />
                                    <div class="item-center"><span>{{maskLayer.layerName}}</span></div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </li>
        <?php endif; ?>
        <li class="menu-item menu-crop" ng-click="removeClippingMask()" ng-show="stages[currentStage].states.isMasked && !stages[currentStage].states.lockMask">
            <i class="nbd-tooltip-hover nbd-svg-icon" title="<?php esc_html_e('Remove clipping mask','web-to-print-online-designer'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M3.25 2.75l17 17L19 21l-2-2H5c-1.1 0-2-.9-2-2V7c0-.55.23-1.05.59-1.41L2 4l1.25-1.25zM22 12l-4.37-6.16C17.27 5.33 16.67 5 16 5H8l11 11 3-4z"/></svg>
            </i>
        </li>
        <li class="menu-item menu-crop" ng-click="editMask()" ng-show="stages[currentStage].states.isMasked && !stages[currentStage].states.lockMask && !stages[currentStage].states.maskFixed">
            <i class="nbd-tooltip-hover nbd-svg-icon" title="<?php esc_html_e('Edit mask','web-to-print-online-designer'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" viewBox="0 0 24 24" xml:space="preserve">
                    <path d="M23,7V1h-6v2H7V1H1v6h2v10H1v6h6v-2h10v2h6v-6h-2V7H23z M3,3h2v2H3V3z M5,21H3v-2h2V21z M17,19H7v-2H5V7h2V5h10v2h2v10h-2 V19z M21,21h-2v-2h2V21z M19,5V3h2v2H19z M13.7,14h-3.5l-0.7,2H7.9l3.4-9h1.4l3.4,9h-1.6C14.5,16,13.7,14,13.7,14z M10.7,12.7h2.6 L12,8.9C12,8.9,10.7,12.7,10.7,12.7z"/>
                    <path d="M12,5.4c-3.7,0-6.6,3-6.6,6.6s3,6.6,6.6,6.6s6.6-3,6.6-6.6S15.7,5.4,12,5.4z"/>
                </svg>
            </i>
        </li>
        <li class="menu-item menu-crop" ng-click="_editMask()" ng-show="stages[currentStage].states.isMasked && !stages[currentStage].states.lockMask && stages[currentStage].states.maskFixed">
            <i class="nbd-tooltip-hover nbd-svg-icon" title="<?php esc_html_e('Edit mask','web-to-print-online-designer'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24" height="24" viewBox="0 0 24 24" xml:space="preserve">
                    <path d="M23,7V1h-6v2H7V1H1v6h2v10H1v6h6v-2h10v2h6v-6h-2V7H23z M3,3h2v2H3V3z M5,21H3v-2h2V21z M17,19H7v-2H5V7h2V5h10v2h2v10h-2 V19z M21,21h-2v-2h2V21z M19,5V3h2v2H19z M13.7,14h-3.5l-0.7,2H7.9l3.4-9h1.4l3.4,9h-1.6C14.5,16,13.7,14,13.7,14z M10.7,12.7h2.6 L12,8.9C12,8.9,10.7,12.7,10.7,12.7z"/>
                    <path d="M12,5.4c-3.7,0-6.6,3-6.6,6.6s3,6.6,6.6,6.6s6.6-3,6.6-6.6S15.7,5.4,12,5.4z"/>
                </svg>
            </i>
        </li>
        <li class="menu-item menu-crop" ng-click="replaceMaskedImage()" ng-if="settings['nbdesigner_enable_upload_image'] == 'yes'" ng-show="stages[currentStage].states.isMasked">
            <i class="nbd-tooltip-hover nbd-svg-icon" title="<?php esc_html_e('Replace image','web-to-print-online-designer'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 6v3l4-4-4-4v3c-4.42 0-8 3.58-8 8 0 1.57.46 3.03 1.24 4.26L6.7 14.8c-.45-.83-.7-1.79-.7-2.8 0-3.31 2.69-6 6-6zm6.76 1.74L17.3 9.2c.44.84.7 1.79.7 2.8 0 3.31-2.69 6-6 6v-3l-4 4 4 4v-3c4.42 0 8-3.58 8-8 0-1.57-.46-3.03-1.24-4.26z"/></svg>
            </i>
        </li>
        <li class="menu-item menu-crop" ng-click="detachImage()" ng-show="stages[currentStage].states.isMasked && !stages[currentStage].states.isEmptyMask && !stages[currentStage].states.lockMask">
            <i class="nbd-tooltip-hover nbd-svg-icon" title="<?php esc_html_e('Detach Image','web-to-print-online-designer'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M15.96 10.29l-2.75 3.54-1.96-2.36L8.5 15h11l-3.54-4.71zM3 5H1v16c0 1.1.9 2 2 2h16v-2H3V5zm18-4H7c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V3c0-1.1-.9-2-2-2zm0 16H7V3h14v14z"/></svg>
            </i>
        </li>
    </ul>
</div>
