<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="layer-color-option shadow" ng-show="canvas.getActiveObject() && editable.available_color">
    <h2><b>{{(langs['COLORS']) ? langs['COLORS'] : "Colors"}}</b></h2>
    <div class="background-options">
        <div ng-show="canvas.getActiveObject() && (task === 'create' || (task == 'edit' && design_type == 'template' ))">
            <div><label style="font-size: 10px;" for="color_link_group">{{(langs['AVAILABLE_COLORS']) ? langs['AVAILABLE_COLORS'] : "Available Colors"}}</label></div>
            <div>
                <span ng-repeat="color in editable.available_color_list" ng-click="setColorForLayer( color )" class="nbd-list-layer-color hover-shadow" ng-style="{'background': color}">
                    {{color}}
                    <span ng-click="removeColorLayer( $index )">&times;</span>
                </span>
            </div>
            <div class="nbd-bg-colr-picker-wrap">
                <spectrum-colorpicker
                    ng-model="layerColorAvailable" 
                    options="{
                        showPaletteOnly: false, 
                        togglePaletteOnly: false, 
                        showPalette: false, 
                        showInput: true}">
                </spectrum-colorpicker>
                <span class="add-layer-color" ng-click="addColorForLayer()">{{(langs['ADD']) ? langs['ADD'] : "Add"}}</span>
            </div>
            <div class="color_link_group">
                <label for="color_link_group">{{(langs['COLOR_LINK_GROUP']) ? langs['COLOR_LINK_GROUP'] : "Color Link Group"}}</label>
                <input style="width: 100%" id="color_link_group" ng-model="editable.color_link_group" ng-change="setColorLinkGroup()"/>
            </div>
        </div>    
        <div class="pre-color" ng-show="!( canvas.getActiveObject() && (task === 'create' || (task == 'edit' && design_type == 'template' )) )">
            <span ng-repeat="color in editable.available_color_list" ng-click="setColorForLayer( color )" class="nbd-color-item hover-shadow" ng-style="{'background': color}"></span>
        </div>        
    </div>
</div>  