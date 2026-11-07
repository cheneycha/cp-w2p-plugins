<div class="nbd-toolbar-zoom">
    <div class="zoomer">
        <div class="zoomer-toolbar">
            <ul class="nbd-main-menu">
                <li class="menu-item zoomer-item zoomer-fullscreen" ng-click="enableFullScreenMode()"><i class="icon-nbd icon-nbd-fullscreen"></i></li>
                <li class="menu-item zoomer-item zoomer-out" ng-click="zoomStage(stages[currentStage].states.currentScaleIndex - 1)" ng-class="stages[currentStage].states.currentScaleIndex > 0 ? '' : 'nbd-disabled'"><i class="icon-nbd icon-nbd-remove"></i></li>
                <li class="menu-item zoomer-item zoomer-level">
                    <span>{{stages[currentStage].states.scaleRange[stages[currentStage].states.currentScaleIndex].value}}</span>
                    <div class="sub-menu zoomer-popover" data-pos="center">
                        <ul class="zoomer-popover-list">
                            <li ng-hide="s.label == 'Fit' || s.label == 'Fill' || s.label == 'Print Size'" ng-click="zoomStage($index)" ng-class="stages[currentStage].states.currentScaleIndex == $index ? 'active' : ''" ng-repeat="s in stages[currentStage].states.scaleRange" class="zoomer-popover-item">{{s.label}}</li>
                            <li ng-show="stages[currentStage].states.printScaleIndex > -1" ng-click="zoomStage(stages[currentStage].states.printScaleIndex)" ng-class="stages[currentStage].states.printScaleIndex == stages[currentStage].states.currentScaleIndex ? 'active' : ''" class="zoomer-popover-item"><?php esc_html_e('Print Size','web-to-print-online-designer'); ?></li>
                            <li ng-click="zoomStage(stages[currentStage].states.fitScaleIndex)" ng-class="stages[currentStage].states.fitScaleIndex == stages[currentStage].states.currentScaleIndex ? 'active' : ''" class="zoomer-popover-item"><?php esc_html_e('Fit','web-to-print-online-designer'); ?></li>
                            <li ng-show="stages[currentStage].states.fillScaleIndex > -1" ng-click="zoomStage(stages[currentStage].states.fillScaleIndex)" ng-class="stages[currentStage].states.fillScaleIndex == stages[currentStage].states.currentScaleIndex ? 'active' : ''" class="zoomer-popover-item"><?php esc_html_e('Fill','web-to-print-online-designer'); ?></li>
                        </ul>
                    </div>
                </li>
                <li ng-click="zoomStage(stages[currentStage].states.currentScaleIndex + 1)" ng-class="stages[currentStage].states.currentScaleIndex < (stages[currentStage].states.scaleRange.length - 1) ? '' : 'nbd-disabled'" class="menu-item zoomer-item zoomer-in"><i class="icon-nbd icon-nbd-add-black"></i></li>
            </ul>
        </div>
    </div>
</div>