<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div id="tool-top">
    <span class="fa fa-book shadow help first_visitor nbd-tooltip-i18n" data-lang="HELPDESK" data-placement="left"></span>
    <span ng-show="langCategories.length > 1" class="fa fa-language shadow translate nbd-tooltip-i18n" data-lang="LANGUAGE" data-placement="left" style="font-size: 18px;"></span>
<!--    <span id="mobile" ng-show="modeMobile" class="fa fa-eye shadow hover-shadow"></span>-->
    <span id="debug" ng-show="state == 'dev'" class="fa fa-magic shadow hover-shadow" ng-click="debug()"></span>
    <span id="show_grid" ng-hide="modeMobile || state == 'pub'" class="fa fa-search shadow hover-shadow nbd-tooltip-i18n" data-lang="PREVIEW" data-placement="left"  data-toggle="modal" data-target="#dg-preview" ng-click="preview()"></span>   
    <span class="fa fa-plus shadow hover-shadow nbd-tooltip-i18n" aria-hidden="true" data-lang="ZOOM_IN" data-placement="left"  ng-click="zoomIn()"></span>
    <span class="fa fa-minus shadow hover-shadow nbd-tooltip-i18n" aria-hidden="true" data-lang="ZOOM_OUT" data-placement="left"  ng-click="zoomOut()"></span>	   
    <span id="expand_feature" class="fa fa-id-card shadow hover-shadow nbd-tooltip-i18n" data-lang="TEMPLATE" data-placement="left"  data-toggle="modal" data-target="#dg-expand-feature" ng-click="loadAdminListDesign()"></span>
    <span ng-show="settings.task == 'new' && settings.ui_mode == 2" style="font-size: 18px;" class="fa fa-cube shadow hover-shadow nbd-tooltip-i18n" data-lang="PRODUCTS" data-placement="left"  data-toggle="modal" data-target="#dg-load-product" ng-click="loadListProduct()"></span>
    <span ng-show="settings.task == 'new' && settings.ui_mode == 2" style="font-size: 18px;" class="fa fa-info-circle shadow hover-shadow nbd-tooltip-i18n" data-lang="INFORMATION" data-placement="left"  data-toggle="modal" data-target="#dg-product-info"></span> 
    <span class="fa fa-paint-brush shadow hover-shadow nbd-tooltip-i18n" aria-hidden="true" data-lang="DISABLE_DRAW_MODE" data-placement="left" ng-click="disableDrawMode()" ng-show="canvas.isDrawingMode" ng-class="canvas.isDrawingMode ? 'disabledraw' : ''"></span>  
    <span class="fa fa-object-group shadow hover-shadow deactive-group nbd-tooltip-i18n" data-lang="DESELECT_GROUP" data-placement="left" aria-hidden="true"  ng-click="deactiveGroup()" ng-show="showAlignToolbar"></span>
    <span class="shadow hover-shadow nbd-tooltip-i18n custom-dimension active" id="custom-dimension-tg" data-toggle="modal" data-target="#dg-custom-dimension" data-lang="DIMENSION" data-placement="left" ng-show="productOptions.allow_specify_dimension == 1 && settings.task != 'create'">
        <svg xmlns="http://www.w3.org/2000/svg" fit="" height="100%" width="100%" preserveAspectRatio="xMidYMid meet" viewBox="0 0 16 16" focusable="false">
            <g>
                <path d="M3.029 3.417l0.343 0.343-1.404 1.404 1.094 1.094 1.41-1.41 0.321 0.321-1.41 1.411 1.080 1.079 3.208-3.208-3.906-3.883-3.196 3.196 1.057 1.058 1.404-1.405zM11.55 8.329l-3.208 3.209 1.079 1.079 1.411-1.411 0.321 0.321-1.411 1.411 1.094 1.094 1.403-1.404 0.343 0.343-1.403 1.404 1.056 1.057 3.197-3.197-3.883-3.905zM14.911 4.226c0.39-0.391 0.393-1.022 0.006-1.41l-1.756-1.756c-0.387-0.387-1.020-0.385-1.41 0.006l-1.085 1.085 3.16 3.16 1.085-1.085zM1.699 12.971l1.312 1.312 2.642-0.938-3.015-3.015-0.938 2.642zM10.325 2.471l-7.442 7.443 3.184 3.184 7.443-7.443-3.185-3.184zM4.329 9.908l5.99-5.99 0.349 0.349-5.991 5.991-0.348-0.35zM5.027 10.607l5.99-5.99 0.349 0.349-5.99 5.99-0.348-0.348zM5.724 11.304l5.99-5.99 0.348 0.349-5.99 5.99-0.349-0.349zM0.977 15.005l1.533-0.559-0.974-0.975-0.558 1.533z"></path>
            </g>
        </svg>      
    </span>
    <span ng-show="state == 'dev'" class="background-opt shadow hover-shadow nbd-tooltip-i18n" data-lang="BACKGROUND" data-placement="left" aria-hidden="true" ng-click="showBackgroundOption()"></span>    
<!--    <span ng-show="settings.product_data.option.swatch_preview !== undefined" class="background-opt shadow hover-shadow nbd-tooltip-i18n" data-lang="BACKGROUND" data-placement="left" aria-hidden="true" ng-click="showBackgroundOption()"></span>    -->
</div>
<div class="first_message hover-shadow">
    {{(langs['HI_THERE']) ? langs['HI_THERE'] : "Hi there"}}, <br />
    {{(langs['IM_HELPER']) ? langs['IM_HELPER'] : "I'm Helper! If you need any help"}}...
</div>
<div class="translate-switch hover-shadow shadow" ng-show="langCategories.length > 1">
    <ul>
        <li ng-repeat="cat in langCategories" ng-click="loadLanguage(cat.code)" ng-class="{open : currentCatLang === cat.code}">{{cat.name}}</li>
    </ul>
</div>
<p ng-hide="settings.enable_upload == '1' || settings.is_mobile == '1' || settings.task == 'edit' || settings.task == 'create'" class="toggle-upload shadow"  ng-click="changeDesignMode('upload')"><i class="fa fa-cloud-upload" aria-hidden="true"></i> {{(langs['UPLOAD_DESIGN']) ? langs['UPLOAD_DESIGN'] : "Upload design"}}</p>
<!-- Side nav 2 -->
<div class="preview-thumbnail-wrap" ng-show="state == 'dev'">
    <span class="fa fa-chevron-up thumbnail-nav" ng-click="previousOrientation(currentSide.id)" ng-class="currentSide.id > 0 ? 'ready' : '' " title="Previous"></span>
    <div class="preview-thumbnail-item" ng-hide="currentSide.id > $index || $index > (currentSide.id + 1)" ng-class="{active: currentVariant.orientationActive == orientation.name}" ng-repeat="orientation in currentVariant.info" ng-click="changeOrientation(orientation)" ng-click="changeOrientation(orientation)">
        <div class="preview-thumbnail-inner" ng-style="{
            'width': orientation.source['img_src_width'] / 5 + 'px',
            'height': orientation.source['img_src_height'] / 5 + 'px',
            'top':  orientation.source['img_src_top'] / 5 + 'px',
            'left':  orientation.source['img_src_left'] / 5 + 'px'
        }">
            <img ng-show="orientation.source['bg_type'] == 'image'" ng-src="{{orientation.source['img_src']}}" />
            <span ng-show="orientation.source['bg_type'] == 'color'" ng-style="{'background': orientation.source['bg_color_value']}"></span>
            <span ng-show="orientation.source['bg_type'] == 'tran'" class="background-transparent"></span>
        </div>
        <div class="preview-thumbnail-inner" ng-show='existDesign(orientation)' ng-style="{
            'width': orientation.source['area_design_width'] / 5 + 'px',
            'height': orientation.source['area_design_height'] / 5 + 'px',
            'top':  orientation.source['area_design_top'] / 5 + 'px',
            'left':  orientation.source['area_design_left'] / 5 + 'px'        
        }">
            <img ng-src="{{dataCustomerDesign[orientation.name]}}" />
        </div>
    </div>
    <span class="fa fa-chevron-down thumbnail-nav next" ng-click="nextOrientation(currentSide.id)" ng-class="currentSide.id < (currentVariant.numberFrame - 1 ) ? 'ready' : '' " title="Next"></span>
</div>
<div class="printing-line" ng-show="state == 'dev' && (currentVariant.info[currentSide.id].source.show_bleed == 1 || currentVariant.info[currentSide.id].source.show_safe_zone == 1)">
    <div><span class='nbd-bleed-notation'></span> <b>{{(langs['TRIM_LINE']) ? langs['TRIM_LINE'] : "Trim line"}}</b></div>
    <div><span class='nbd-safe-zone-notation-box'></span> <b>{{(langs['SAFE_ZONE']) ? langs['SAFE_ZONE'] : "Trim Safe zone"}}</b></div>
</div>