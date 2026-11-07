<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div class=" shadow popup" id="container_layer">
    <h3>{{(langs['LAYERS']) ? langs['LAYERS'] : "Layers"}}
        <span class="fa fa-refresh pull-right  nbd-tooltip-i18n" data-placement="top" data-lang="DELETE_ALL_LAYERS" ng-click="deleteAllItem()"></span>
        <span id="_close_popover" class="fa fa-backward"></span>
    </h3>
    <div id="dg-layers" class="has-croll">
        <ul id="layers">
            <li class="shadow layer"  id="layer-{{layer.index}}" ng-repeat="layer in currentLayers| reverse" ng-class="{active: currentLayerActive === layer.index, 'lock' : layer.class === 'lock', 'static': layer.isBg}" ng-click="activeLayer(layer)">
                <i class="fa fa-text-width" aria-hidden="true" ng-show="layer.type === 'text'"></i>  		
                <img alt="image uploaded" ng-src="{{layer.src}}" ng-hide="layer.type === 'text'" width="20" height="20" class="layer_thumb"/>
                <span>{{layer.name}}</span>
                <span class="pull-right" ng-hide="layer.isBg != undefined || layer.isOverlay != undefined">
                    <a style="margin-right: 10px;" class="nbdesigner_visible_layer" href="javascript:void(0)" ng-click="toggleVisibleLayer(layer)" title="{{(langs['TOGGLE_VISIBLE']) ? langs['TOGGLE_VISIBLE'] : 'Toggle visible'}}"><i class="fa" aria-hidden="true" ng-class="layer.class === 'lock' ? 'fa-eye-slash' : 'fa-eye'"></i></a>
                    <a style="margin-right: 10px;" class="nbdesigner_lock_layer" href="javascript:void(0)" ng-click="toggleLockLayer(layer, $event)" title="{{(langs['TOGGLE_LOCK']) ? langs['TOGGLE_LOCK'] : 'Toggle lock'}}"><i class="fa" aria-hidden="true" ng-class="layer.class === 'lock' ? 'fa-lock' : 'fa-unlock-alt'"></i></a> 
                </span>
            </li>                                               
        </ul>
    </div>
    <div class="container-dg-slider" ng-show="currentLayers.length > 5"><div class="dg-slider" id="scroll-layer-slider"></div></div>
</div>