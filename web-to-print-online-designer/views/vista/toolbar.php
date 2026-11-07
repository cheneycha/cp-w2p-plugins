<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<div class="v-toolbar nbd-shadow">
    <div class="main-toolbar">
        <div class="v-toolbar-item left-toolbar">
            <ul class="v-tabs tabs-toolbar v-main-menu">
                <div id="selectedTab" style="display: none"></div>
                <li class="v-tab v-menu-item v-tab-layer" id="design-tab">
                    <span><?php _e('Design','web-to-print-online-designer'); ?></span>
                </li>
                <li class="v-tab v-menu-item active" data-tab="tab-design" ng-click="disableDrawMode();">
                    <span><?php _e('Template','web-to-print-online-designer'); ?></span>
                </li>
                <li class="v-tab v-menu-item" data-tab="tab-text" ng-if="settings['nbdesigner_enable_text'] == 'yes'" ng-click="disableDrawMode();">
                    <span><?php _e('Text','web-to-print-online-designer'); ?></span>
                </li>
                <li ng-if="settings['nbdesigner_enable_image'] == 'yes'" class="v-tab v-menu-item" data-tab="tab-photo" ng-click="disableDrawMode();">
                    <span><?php _e('Image','web-to-print-online-designer'); ?></span>
                </li>
                <li class="v-tab v-menu-item" data-tab="tab-element" ng-click="disableDrawMode();">
                    <span><?php _e('More','web-to-print-online-designer'); ?></span>
                </li>
            </ul>
        </div>
        <div class="v-toolbar-item right-toolbar">
            <ul class="v-main-menu">
                <li data-ripple="rgba(0,0,0, 0.1)" class="v-menu-item" ng-click="undo()" ng-class="stages[currentStage].states.isUndoable ? 'in' : 'nbd-disabled'">
                    <i class="nbd-icon-vista nbd-icon-vista-undo"></i>
                    <span><?php _e('Undo','web-to-print-online-designer'); ?></span>
                </li>
                <li data-ripple="rgba(0,0,0, 0.1)" class="v-menu-item" ng-click="redo()" ng-class="stages[currentStage].states.isRedoable ? 'in' : 'nbd-disabled'">
                    <i class="nbd-icon-vista nbd-icon-vista-redo"></i>
                    <span><?php _e('Redo','web-to-print-online-designer'); ?></span>
                </li>
<!--                <li class="v-menu-item"><i class="nbd-icon-vista nbd-icon-vista-proof"></i><span>proof</span></li>-->
<!--                <li class="v-menu-item" ng-click="debug()"><i class="nbd-icon-vista nbd-icon-vista-save"></i><span>Debug</span></li>-->
            </ul>
        </div>
    </div>
</div>
