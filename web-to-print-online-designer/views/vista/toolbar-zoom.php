<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<div class="nbd-toolbar-zoom">
    <div class="zoomer">
        <div class="zoomer-toolbar">
            <ul class="nbd-main-zoomer">
                <li class="zoomer-item zoomer-fullscreen ng-hide" ng-click="enableFullScreenMode()" title="<?php _e('Full Screen','web-to-print-online-designer'); ?>"><i class="nbd-icon-vista nbd-icon-vista-fullscreen"></i></li>
                <li class="zoomer-item zoomer-out" ng-click="zoomStage(stages[currentStage].states.currentScaleIndex - 1)" ng-class="stages[currentStage].states.currentScaleIndex > 0 ? '' : 'nbd-disabled'" title="<?php _e('Zoon in','web-to-print-online-designer'); ?>"><i class="nbd-icon-vista nbd-icon-vista-remove"></i></li>
                <li class="zoomer-item zoomer-level v-dropdown">
                    <span class="v-btn-dropdown">{{stages[currentStage].states.scaleRange[stages[currentStage].states.currentScaleIndex].value}}</span>
                    <div class="v-dropdown-menu zoomer-popover" data-pos="center">
                        <ul class="zoomer-popover-list">
                            <li ng-hide="s.label == 'Fit' || s.label == 'Fill'" ng-click="zoomStage($index)" ng-class="stages[currentStage].states.currentScaleIndex == $index ? 'active' : ''" ng-repeat="s in stages[currentStage].states.scaleRange" class="zoomer-popover-item">{{s.label}}</li>
                            <li ng-click="zoomStage(stages[currentStage].states.fitScaleIndex)" ng-class="stages[currentStage].states.fitScaleIndex == stages[currentStage].states.currentScaleIndex ? 'active' : ''" class="zoomer-popover-item"><?php _e('Fit','web-to-print-online-designer'); ?></li>
                            <li ng-show="stages[currentStage].states.fillScaleIndex > -1" ng-click="zoomStage(stages[currentStage].states.fillScaleIndex)" ng-class="stages[currentStage].states.fillScaleIndex == stages[currentStage].states.currentScaleIndex ? 'active' : ''" class="zoomer-popover-item"><?php _e('Fill','web-to-print-online-designer'); ?></li>
                        </ul>
                    </div>
                </li>
                <li ng-click="zoomStage(stages[currentStage].states.currentScaleIndex + 1)" ng-class="stages[currentStage].states.currentScaleIndex < (stages[currentStage].states.scaleRange.length - 1) ? '' : 'nbd-disabled'" class="menu-item zoomer-item zoomer-in"><i class="nbd-icon-vista nbd-icon-vista-add-black" title="<?php _e('Zoom out','web-to-print-online-designer'); ?>"></i></li>
            </ul>
        </div>
    </div>
</div>