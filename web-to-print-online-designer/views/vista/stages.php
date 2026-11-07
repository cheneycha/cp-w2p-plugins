<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<div class="nbd-stages" id="nbd-stages">
    <div class="stages-inner">
        <div class="stage" ng-repeat="stage in stages" id='stage-container-{{$index}}' ng-click="onClickStage($event)" ng-class="{'hidden':$index > 0, 'nbd-active':$index==currentStage}" >
            <div style="display: inline-block;position: relative;">
                <div class="stage-main" ng-style="{'width' : calcStyle(stage.config.cwidth * stage.states.scaleRange[stage.states.currentScaleIndex].ratio),
                    'height' : calcStyle(stage.config.cheight * stage.states.scaleRange[stage.states.currentScaleIndex].ratio)}">
                    <div class="stage-background nbd-shadow" ng-style="{'background-color': stage.config.bgType == 'image' ? '#fff' : (stage.config.bgType == 'color' ? stage.config.bgColor : 'transparent')}">
                        <img style="width: 100%; height: 100%;" style="" ng-if="stage.config.bgType == 'image'" ng-src='{{stage.config.bgImage}}'/>
                    </div>
                    <div class="design-wrap" ng-style="{'width' : calcStyle(stage.config.width * stage.states.scaleRange[stage.states.currentScaleIndex].ratio),
                        'height' : calcStyle(stage.config.height * stage.states.scaleRange[stage.states.currentScaleIndex].ratio),
                        'top' : calcStyle(stage.config.top * stage.states.scaleRange[stage.states.currentScaleIndex].ratio),
                        'left' : calcStyle(stage.config.left * stage.states.scaleRange[stage.states.currentScaleIndex].ratio)}">
                        <div class="design-zone">
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
                                <div class="bounding-rect" ng-style="stages[currentStage].states.boundingObject"></div>
                                <div class="bounding-rect" ng-style="stages[currentStage].states.uploadZone" style="background: rgba(255,255,255,0.85); overflow: hidden;display: flex; justify-content: center; align-items: center;flex-direction: column;position: relative;">
                                    <i style="color: rgb(194, 194, 194); position: absolute; font-size: 70px;z-index: 0;" ng-style="{transform: 'rotate(-'+stages[currentStage].states.rotate.angle+'deg)'}" class="icon-nbd icon-nbd-replace-image"></i>
                                    <span style="font-weight: bold; z-index: 1;" ng-style="{transform: 'rotate(-'+stages[currentStage].states.rotate.angle+'deg)'}"><?php _e('Drop to replace'); ?></span>
                                </div>
                                <div class="layer-coordinates" ng-style="stages[currentStage].states.coordinates.style">{{stages[currentStage].states.coordinates.top}} {{stages[currentStage].states.coordinates.left}}</div>
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
                            <img style="width: 100%; height: 100%;" ng-if="stage.config.show_overlay == '1'" ng-src='{{stage.config.img_overlay}}'/>
                        </div>				
                        <div class="stage-guideline">
                            <div style="position: relative; width: 100%; height: 100%;">
                                <div ng-class="stage.config.area_design_type == '2' ? 'nbd-round' : ''" ng-show="settings.bleedLine" class="bleed-line" ng-style="{'width' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.width - 2 * stage.config.bleed_lr)),
                                    'height' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.height - 2 * stage.config.bleed_tb)),
                                    'left' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.bleed_lr)),
                                    'border-radius' : calcStyle(stage.config.bleed_radius ? stage.states.scaleRange[stage.states.currentScaleIndex].ratio * stage.config.bleed_radius : 0),
                                    'top' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.bleed_tb))}" style="border: 1px solid green;box-sizing: border-box;position: absolute;"></div>
                                <div ng-class="stage.config.area_design_type == '2' ? 'nbd-round' : ''" ng-show="settings.bleedLine" class="safe-line" ng-style="{'width' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.width - 2 * stage.config.bleed_lr - 2 * stage.config.margin_lr)),
                                    'height' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.height - 2 * stage.config.bleed_tb - 2 * stage.config.margin_tb)),
                                    'left' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.bleed_lr+stage.config.margin_lr)),
                                    'border-radius' : calcStyle(stage.config.hasOwnProperty( 'safezone_radius' )? ((stage.states.scaleRange[stage.states.currentScaleIndex].ratio * stage.config.safezone_radius > 0) ? (stage.states.scaleRange[stage.states.currentScaleIndex].ratio * stage.config.safezone_radius) : 10) : 0 ),
                                    'top' : calcStyle(stage.states.scaleRange[stage.states.currentScaleIndex].ratio * (stage.config.bleed_tb+stage.config.margin_tb))}"
                                     style="border: 1px solid red;box-sizing: border-box;position: absolute;"></div>                                 
                            </div>
                        </div>
                    </div>
                </div>
                <?php include 'toolbar-page.php';?>
            </div>
        </div>
    </div>
    <div class="nbd-toolbar-zoom fullscreen-stage-nav">
        <div class="zoomer">
            <div class="zoomer-toolbar">
                <ul class="nbd-main-zoomer">
                    <li class="zoomer-item zoomer-fullscreen" ng-click="exitFullscreenMode()"><i class="nbd-icon-vista nbd-icon-vista-fullscreen"></i></li>
                    <li class="zoomer-item" ng-click="switchStage(currentStage, 'prev')" ng-class="currentStage > 0 ? '' : 'nbd-disabled'"><i class="nbd-icon-vista nbd-icon-vista-arrow-upward rotate-90"></i></li>
                    <li class="zoomer-item zoomer-level">{{currentStage+1}}/{{stages.length}}</li>
                    <li class="zoomer-item" ng-click="switchStage(currentStage, 'next')" ng-class="currentStage < (stages.length - 1) ? '' : 'nbd-disabled'"><i class="nbd-icon-vista nbd-icon-vista-arrow-upward rotate90"></i></li>
                </ul>
            </div>
        </div>
    </div>
</div>