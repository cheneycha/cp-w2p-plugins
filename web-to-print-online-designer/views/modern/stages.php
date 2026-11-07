<div class="nbd-stages" id="nbd-stages" nbd-drop-zone="uploadFile(files)">
    <div class="stages-inner">
        <div class="stage" ng-repeat="(stageIndex, stage) in stages" id='stage-container-{{$index}}' ng-mousedown="onClickStage($event)" ng-class="{'_nbd_hidden':$index > 0}" >
            <?php do_action('nbd_modern_before_stage'); ?>
            <div ng-click="addRulerGuideLine( $event, 'hors' );$event.stopPropagation();" ng-style="{'padding-left': calcRulerPaddingLeft(stage.config.cwidth * stage.states.scaleRange[stage.states.currentScaleIndex].ratio)}" ng-class="settings.showRuler ? '' : 'nbd-hide nbd-disable-event'" class="nbd-hoz-ruler temporary-hidden">
                <svg class="nbd-prevent-select" ng-style="{'width': calcStyle(stage.config.cwidth * stage.states.scaleRange[stage.states.currentScaleIndex].ratio + 30)}" id="hoz-ruler-{{$index}}" xmlns="http://www.w3.org/2000/svg"></svg>
            </div>
            <div ng-click="addRulerGuideLine( $event, 'vers' ); $event.stopPropagation()" ng-style="{'padding-top': calcRulerPaddingTop(stage.config)}" ng-class="settings.showRuler ? '' : 'nbd-hide nbd-disable-event'" class="nbd-ver-ruler temporary-hidden">
                <svg class="nbd-prevent-select" ng-style="{'height': calcStyle(stage.config.cheight * stage.states.scaleRange[stage.states.currentScaleIndex].ratio + 30)}" id="ver-ruler-{{$index}}" xmlns="http://www.w3.org/2000/svg"></svg>
            </div>
            <div ng-if="settings.showDimensions && !settings.is_mobile" class="nbd-dimension x-dimension" ng-style="{'left': calcRulerPaddingLeft(stage.config.cwidth * stage.states.scaleRange[stage.states.currentScaleIndex].ratio), 'width': calcStyle(stage.config.cwidth * stage.states.scaleRange[stage.states.currentScaleIndex].ratio + 1)}"><span>{{settings.product_data.product[$index].product_width + ' ' + settings.nbdesigner_dimensions_unit}}</span></div>
            <div ng-if="settings.showDimensions && !settings.is_mobile" class="nbd-dimension y-dimension" ng-style="{'top': calcRulerPaddingTop(stage.config), 'height': calcStyle(stage.config.cheight * stage.states.scaleRange[stage.states.currentScaleIndex].ratio + 1)}"><span class="dimension-number-wrap"><span class="dimension-number">{{settings.product_data.product[$index].product_height + ' ' + settings.nbdesigner_dimensions_unit}}</span></span></div>
            <div class="stage-main-wrap">
                <div class="stage-main" ng-class="stage.config.bgType == 'image' ? 'nbd-without-shadow' : ''" ng-style="{
                        'width' : calcStyle(stage.config.cwidth * stage.states.scaleRange[stage.states.currentScaleIndex].ratio),
                        'height' : calcStyle(stage.config.cheight * stage.states.scaleRange[stage.states.currentScaleIndex].ratio)
                    }">
                    <div class="stage-background" ng-style="{'background-color': stage.config.bgType == 'image' ? '#fff' : (( stage.config.bgType == 'color' && ( ( stage.config.show_overlay == '1' && stage.config.img_overlay != '' ) || !areaDesignShapes[$index] ) )  ? stage.config.bgColor : 'transparent')}">
                        <img ng-if="stage.config.bgType == 'image'" ng-src='{{stage.config.bgImage}}'/>
                    </div>
                    <?php do_action('nbd_modern_before_design_wrap'); ?>
                    <div class="design-wrap" ng-class="settings.nbdesigner_show_design_border == 'yes' ? 'has-border' : ''" ng-style="{
                        'width' : calcStyle(stage.config.width * stage.states.scaleRange[stage.states.currentScaleIndex].ratio),
                        'height' : calcStyle(stage.config.height * stage.states.scaleRange[stage.states.currentScaleIndex].ratio),
                        'top' : calcStyle(stage.config.top * stage.states.scaleRange[stage.states.currentScaleIndex].ratio + offsetDesignWrap),
                        'left' : calcStyle(stage.config.left * stage.states.scaleRange[stage.states.currentScaleIndex].ratio + offsetDesignWrap),
                        'background-color': ( stage.config.bgType == 'color' && !!areaDesignShapes[$index] ) ? stage.config.bgColor : 'transparent'
                    }">
                        <?php if( $enable_sticker_preview ): ?>
                        <div class="sticker-contour-pattern-wrap" ng-class="{'active': stickerCutline.active}" ></div>
                        <?php endif; ?>
                        <div class="design-zone" ng-style="{
                            '-webkit-clip-path': !!areaDesignShapes[$index] ? 'url(#area-design-shape-' + $index + ')' : 'none',
                            'clip-path': !!areaDesignShapes[$index] ? 'url(#area-design-shape-' + $index + ')' : 'none'
                        }">
                            <canvas nbd-canvas stage="stage" ctx="ctxMenuStyle" index="{{$index}}" id="nbd-stage-{{$index}}" last="{{$last ? 1 : 0}}"></canvas>
                        </div>
                        <div class="stage-grid">
                            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" ng-show="settings.showGrid">
                                <defs>
                                    <pattern id="grid10-{{$index}}" width="10" height="10" patternUnits="userSpaceOnUse">
                                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="gray" stroke-width="0.5"/>
                                    </pattern>
                                    <pattern id="grid100-{{$index}}" width="100" height="100" patternUnits="userSpaceOnUse">
                                        <rect width="100" height="100" fill="url(#grid10-{{$index}})"/>
                                        <path d="M 100 0 L 0 0 0 100" fill="none" stroke="gray" stroke-width="1"/>
                                    </pattern>
                                </defs>
                                <rect width="100%" height="100%" fill="url(#grid100-{{$index}})" />
                            </svg>
                        </div>
                        <div class="bounding-layers">
                            <div class="bounding-layers-inner">
                                <div class="bounding-rect" ng-style="stages[currentStage].states.boundingObject">
                                    <span ng-if="settings.nbdesigner_show_layer_size == 'yes'" class="bounding-rect-real-size" ng-style="{'transform': stages[currentStage].states.boundingRealSize.transform}">{{stages[currentStage].states.boundingRealSize.size}}</span>
                                </div>
                                <div class="bounding-corner" ng-class="corner.type != '' ? 'corner-action' : 'bounding-holder'" data-type="corner.type" data-stage="{{stageIndex}}" ng-repeat="corner in stage.states.corners" ng-style="corner.style" ></div>
                                <div class="bounding-rect bounding-rect-upload-zone" ng-style="stages[currentStage].states.uploadZone" >
                                    <i ng-style="{transform: 'rotate(-'+stages[currentStage].states.rotate.angle+'deg)'}" class="icon-nbd icon-nbd-replace-image"></i>
                                    <span class="upload-zone-title" ng-style="{transform: 'rotate(-'+stages[currentStage].states.rotate.angle+'deg)'}"><?php esc_html_e('Drop to replace', 'web-to-print-online-designer'); ?></span>
                                </div>
                                <div class="layer-coordinates" ng-style="stages[currentStage].states.coordinates.style">{{stages[currentStage].states.coordinates.left}} {{stages[currentStage].states.coordinates.top}}</div>
                                <div class="layer-angle" ng-style="stages[currentStage].states.rotate.style"><span ng-style="{transform: 'rotate(-'+stages[currentStage].states.rotate.angle+'deg)'}">{{stages[currentStage].states.rotate.angle}}</span></div>
                            </div>
                        </div>
                        <div class="stage-snapLines">
                            <div class="stage-snapLines-inner">
                                <div class="snapline h-snapline" ng-style="stages[currentStage].states.snaplines.ht"></div>
                                <div class="snapline h-snapline" ng-style="stages[currentStage].states.snaplines.hc"></div>
                                <div class="snapline h-snapline" ng-style="stages[currentStage].states.snaplines.hb"></div>
                                <div class="snapline v-snapline" ng-style="stages[currentStage].states.snaplines.vl"></div>
                                <div class="snapline v-snapline" ng-style="stages[currentStage].states.snaplines.vc"></div>
                                <div class="snapline v-snapline" ng-style="stages[currentStage].states.snaplines.vr"></div>
                                <div class="snapline h-snapline" ng-style="stages[currentStage].states.snaplines.hcc"></div>
                                <div class="snapline v-snapline" ng-style="stages[currentStage].states.snaplines.vcc"></div>
                                <div class="snapline v-snapline" ng-style="stages[currentStage].states.snaplines.vel"></div>
                                <div class="snapline v-snapline" ng-style="stages[currentStage].states.snaplines.ver"></div>
                                <div class="snapline h-snapline" ng-style="stages[currentStage].states.snaplines.het"></div>
                                <div class="snapline h-snapline" ng-style="stages[currentStage].states.snaplines.heb"></div>
                            </div>
                        </div>
                        <div class="stage-overlay">
                            <div class="stage-fold-wrap" ng-style="{'width' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.width - 2 * stage.config.bleed_lr)),
                                'height' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.height - 2 * stage.config.bleed_tb)),
                                'left' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.bleed_lr)),
                                'border-radius' : calcStyle(stage.config.bleed_radius ? stage.states.scaleRange[stage.states.currentScaleIndex].ratio * stage.config.bleed_radius : 0),
                                'top' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.bleed_tb))}">
                                <div class="stage-fold-marks" ng-if="resource.config.fold" ng-class="resource.config.foldClass">
                                    <div class="stage-fold-mark" ng-repeat="n in [] | range: resource.config.numberPanel"></div>
                                </div>
                            </div>
                        </div>
                        <div class="stage-area-design-shape" ng-style="{
                            'width' : calcStyle(stage.config.width * stage.states.scaleRange[stage.states.currentScaleIndex].ratio),
                            'height' : calcStyle(stage.config.height * stage.states.scaleRange[stage.states.currentScaleIndex].ratio)
                        }"></div>
                        <div class="stage-guideline">
                            <div class="stage-guideline-inner">
                                <div ng-class="{'nbd-round': stage.config.area_design_type == '2', 'shaped': !!areaDesignShapes[$index]}" ng-show="settings.bleedLine" class="bleed-line" 
                                    ng-style="{'width' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.width - 2 * stage.config.bleed_lr)),
                                    'height' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.height - 2 * stage.config.bleed_tb)),
                                    'left' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.bleed_lr)),
                                    'border-radius' : calcStyle(stage.config.bleed_radius ? stage.states.scaleRange[stage.states.currentScaleIndex].ratio * stage.config.bleed_radius : 0),
                                    'top' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.bleed_tb))}" ></div>
                                <div ng-class="{'nbd-round': stage.config.area_design_type == '2', 'shaped': !!areaDesignShapes[$index]}" ng-show="settings.bleedLine" class="safe-line" ng-style="{'width' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.width - 2 * stage.config.bleed_lr - 2 * stage.config.margin_lr)),
                                    'height' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.height - 2 * stage.config.bleed_tb - 2 * stage.config.margin_tb)),
                                    'left' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.bleed_lr+stage.config.margin_lr)),
                                    'border-radius' : calcStyle(stage.config.hasOwnProperty( 'safezone_radius' )? ((stage.states.scaleRange[stage.states.currentScaleIndex].ratio * stage.config.safezone_radius > 0) ? (stage.states.scaleRange[stage.states.currentScaleIndex].ratio * stage.config.safezone_radius) : 10) : 0 ),
                                    'top' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.bleed_tb+stage.config.margin_tb))}" ></div>
                                <?php if( $enable_sticker_preview ): ?>
                                <div class="sticker-contour-wrap" ng-class="{'active': stickerCutline.active}" ></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="stage-overlay">
                        <img style="width: 100%; height: 100%;" ng-if="stage.config.show_overlay == '1'" ng-src='{{stage.config.img_overlay}}'/>
                        <div style="position: absolute;" ng-style="{'width' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.width - 2 * stage.config.bleed_lr)),
                            'height' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.height - 2 * stage.config.bleed_tb)),
                            'left' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.bleed_lr)),
                            'border-radius' : calcStyle(stage.config.bleed_radius ? stage.states.scaleRange[stage.states.currentScaleIndex].ratio * stage.config.bleed_radius : 0),
                            'top' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.bleed_tb))}">
                            <div class="stage-fold-marks" ng-if="resource.config.fold" ng-class="resource.config.foldClass">
                                <div class="stage-fold-mark" ng-repeat="n in [] | range: resource.config.numberPanel"></div>
                            </div>
                        </div>
                    </div>
                    <?php do_action('nbd_modern_after_design_wrap'); ?>
                </div>
                <div class="page-toolbar">
                    <div class="page-main">
                        <ul>
                            <li ng-class="$index == 0 ? 'disabled' : ''" ng-click="switchStage($index, 'prev')"><i class="icon-nbd icon-nbd-arrow-upward" title="<?php esc_html_e('Prev Page','web-to-print-online-designer'); ?>"></i></li>
                            <li><span class="nbd-font-size-14">{{$index + 1}}/{{stages.length}}</span></li>
                            <li ng-class="$index == (stages.length - 1) ? 'disabled' : ''" ng-click="switchStage($index, 'next')"><i class="icon-nbd icon-nbd-arrow-upward rotate-180" title="<?php esc_html_e('Next Page','web-to-print-online-designer'); ?>"></i></li>
                            <?php if( $task == 'create_template' ): ?>
                            <li ng-click="addStage()"><i class="icon-nbd icon-nbd-content-copy" title="<?php esc_html_e('Add page','web-to-print-online-designer'); ?>"></i></li>
                            <?php endif; ?>
                            <li><i nbd-clear-stage class="icon-nbd icon-nbd-refresh" title="<?php esc_html_e('Clear Design','web-to-print-online-designer'); ?>"></i></li>
                            <!-- <li ng-click="duplicateDesign()"><i class="icon-nbd icon-nbd-content-copy" title="<?php esc_html_e('Duplicate Design','web-to-print-online-designer'); ?>"></i></li> -->
                            <?php do_action('nbd_modern_extra_page_toolbar'); ?>
                        </ul>
                    </div>
                </div>
            </div> 
            <div class="nbd-prevent-event guide-backdrop"></div>
            <div ng-repeat="line in stage.rulerLines.hors" ng-style="calcStyleGuideline(line, stage.config, stage.states.scaleRange[stage.states.currentScaleIndex].ratio, 'hor')" data-cwidth="{{stage.config.cwidth}}" data-ratio="{{stage.states.scaleRange[stage.states.currentScaleIndex].ratio}}" data-offset="line.top" class="ruler-guideline-hor" ruler-guideline="hor"></div>
            <div ng-repeat="line in stage.rulerLines.vers" ng-style="calcStyleGuideline(line, stage.config, stage.states.scaleRange[stage.states.currentScaleIndex].ratio, 'ver')" data-cwidth="{{stage.config.cwidth}}" data-ratio="{{stage.states.scaleRange[stage.states.currentScaleIndex].ratio}}" data-offset="line.left" class="ruler-guideline-ver" ruler-guideline="ver"></div>            
            <?php do_action('nbd_modern_after_stage'); ?>
        </div>
    </div>
    <div class="nbd-guidelines-notice">
        <div ng-show="settings.bleedLine && !settings.is_mobile" class="nbd-guideline-bleedline" >
            <span class="nbd-popup-trigger" data-popup="popup-nbd-bleedline-popup"><?php esc_html_e('Cut line','web-to-print-online-designer'); ?> <i class="icon-nbd icon-nbd-info-circle"></i></span>
        </div>
        <div ng-show="settings.bleedLine && !settings.is_mobile"class="nbd-guideline-safezone" >
            <span class="nbd-popup-trigger" data-popup="popup-nbd-bleedline-popup"><?php esc_html_e('Safe zone','web-to-print-online-designer'); ?> <i class="icon-nbd icon-nbd-info-circle"></i></span>
        </div>
        <div ng-show="stages[currentStage].states.lostCharLayers.length > 0" class="nbd-guideline-warning" ng-click="alertLostChar()">
            <span class="nbd-warning-inner" nbd-popup-trigger data-popup="popup-nbd-warning-font"><?php esc_html_e('Warning','web-to-print-online-designer'); ?> <i class="icon-nbd icon-nbd-baseline-warning"></i></span>
        </div>
    </div>
    <div class="nbd-toolbar-zoom fullscreen-stage-nav">
        <div class="zoomer">
            <div class="zoomer-toolbar">
                <ul class="nbd-main-menu">
                    <li class="menu-item zoomer-item zoomer-fullscreen" ng-click="exitFullscreenMode()"><i class="icon-nbd icon-nbd-fullscreen"></i></li>
                    <li class="menu-item" ng-click="switchStage(currentStage, 'prev')" ng-class="currentStage > 0 ? '' : 'nbd-disabled'"><i class="icon-nbd icon-nbd-arrow-upward rotate-90"></i></li>
                    <li class="menu-item zoomer-item zoomer-level nbd-prevent-select">{{currentStage+1}}/{{stages.length}}</li>
                    <li class="menu-item" ng-click="switchStage(currentStage, 'next')" ng-class="currentStage < (stages.length - 1) ? '' : 'nbd-disabled'"><i class="icon-nbd icon-nbd-arrow-upward rotate90"></i></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="nbd-stages-nav toggle-down" ng-show="!settings.is_mobile">
        <div class="nbd-stages-nav-wrapper">
            <div class="nbd-stages-nav-inner">
                <span class="nbd-stage-thumb stage-nav">
                    <span ng-class="currentStage == 0 ? 'nbd-disabled' : ''" ng-click="switchStage(currentStage, 'prev', 'left-right')">
                        <i class="icon-nbd icon-nbd-fomat-top-left rotate-135 tooltipstered"></i>
                    </span>
                </span>
                <span class="nbd-stage-thumb stage-name">{{stages[currentStage].config.name}}</span>
                <span class="nbd-stage-thumb stage-nav">
                    <span ng-class="currentStage == (stages.length - 1) ? 'nbd-disabled' : ''" ng-click="switchStage(currentStage, 'next', 'left-right')">
                        <i class="icon-nbd icon-nbd-fomat-top-left rotate45 tooltipstered"></i>
                    </span>
                </span>
            </div>
            <div class="nbd-stages-nav-toggle">
                <div class="nbd-stages-nav-toggle-inner">
                    <svg viewBox="0 0 93 15.9" width="93" xmlns="http://www.w3.org/2000/svg"><path fill="#d0d6dd" d="M91.3,15.7c-2.1-0.4-4-2-5.6-4.6l-1.1-1.9c-3.5-6-8.2-9.4-13.2-9.4H21.5c-4.9,0-9.7,3.4-13.2,9.4l-1.1,1.9 c-1.6,2.7-3.7,4.4-5.9,4.6v0.1L91.3,15.7L91.3,15.7z"/></svg>
                    <svg class="toggle-direction" id="toggle-direction" version="1.1" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24">
                        <path d="M7.7 11.7l4.3-4.3 4.3 4.3c0.2 0.2 0.5 0.3 0.7 0.3s0.5-0.1 0.7-0.3c0.4-0.4 0.4-1 0-1.4l-5-5c-0.4-0.4-1-0.4-1.4 0l-5 5c-0.4 0.4-0.4 1 0 1.4s1 0.4 1.4 0z"></path>
                        <path d="M12.7 12.3c-0.4-0.4-1-0.4-1.4 0l-5 5c-0.4 0.4-0.4 1 0 1.4s1 0.4 1.4 0l4.3-4.3 4.3 4.3c0.2 0.2 0.5 0.3 0.7 0.3s0.5-0.1 0.7-0.3c0.4-0.4 0.4-1 0-1.4l-5-5z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
    <?php do_action('nbd_modern_extra_stages'); ?>
</div>