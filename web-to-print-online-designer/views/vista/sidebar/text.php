<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<div ng-if="settings['nbdesigner_enable_text'] == 'yes'" id="tab-text" class="v-tab-content">
    <span class="v-title">Text</span>
    <div class="v-action">
        <span class="v-btn waves-effect" ng-click="addText('Heading','heading')" style="width: calc(100%)">Add New Text Field</span>
    </div>
    <div class="v-content" data-action="yes">
        <div class="tab-scroll">
            <div class="main-scrollbar">
                <div class="text-editor" ng-repeat="layer in stages[currentStage].layers" ng-click="activeLayer(layer.index)" ng-class="{'active' : stages[currentStage].states.isLayer && stages[currentStage].states.itemId == layer.itemId}">
                    <input class="text-field" type="text" ng-if="layer.type == 'text'" ng-change="setLayerAttribute('text', layer.text, layer.index, $index)" ng-model="layer.text" />
                </div>
            </div>
        </div>
    </div>
</div>