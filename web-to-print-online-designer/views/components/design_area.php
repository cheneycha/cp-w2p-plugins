<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div style="max-height: 100%; width: 100%;position: relative; overflow: hidden;" id="nbd-viewport">
<div class="viewport" ng-style="{'width': designerWidth, 'height': designerHeight, 'left': offset,
                                           'min-width' : '320px',
                                           'min-height' : '320px'}">
    <div class="view_container">
        <div class="design-image" >
            <div class="container-image" ng-repeat="img in currentVariant.activeImages"
                 ng-style="{
                    'width' : calcDimension(img.img_src_width),
                    'height' : calcDimension(img.img_src_height),
                    'left' : calcLeft(img.img_src_left),
                    'top' : calcLeft(img.img_src_top),
                    'background' : (img.bg_type == 'color') ? img.bg_color_value : ''
                 }"
            >
                <img ng-if="img.bg_type == 'image'" ng-src="{{img.img_src}}"  spinner-on-load ng-style="{'width': '100%', 'height': '100%'}" />
            </div>                               
        </div>
        <div class="grid-area">
            <canvas id="grid"></canvas>
        </div>	
        
        <div class="design-aria" ng-class="currentVariant.designArea['area_design_type'] == '2' ? 'nbd-rounded' : ''"  oncontextmenu="return false"
             ng-style="{'width': currentVariant.designArea['area_design_width'] * zoom * designScale,
					   'height' : currentVariant.designArea['area_design_height'] * zoom * designScale,
					   'top' : calcLeft(currentVariant.designArea['area_design_top']),
					   'left' : calcLeft(currentVariant.designArea['area_design_left'])
				}"
             >
            <canvas id="designer-canvas" width="500" height="500"></canvas> 
        </div>
        
        <div class="designer-overlay" style="position: absolute; pointer-events: none;" ng-if="currentVariant.info[currentSide.id]['source']['show_overlay'] == '1' && currentVariant.info[currentSide.id]['source']['include_overlay'] == '0'"
             ng-style="{'width': currentVariant.designArea['area_design_width'] * zoom * designScale,
					   'height' : currentVariant.designArea['area_design_height'] * zoom * designScale,
					   'top' : calcLeft(currentVariant.designArea['area_design_top']),
					   'left' : calcLeft(currentVariant.designArea['area_design_left'])
				}"
             >
            <img style="width: 100%; height: 100%; margin: 0;" ng-src="{{currentVariant.info[currentSide.id]['source']['img_overlay']}}"/>
        </div>        
        
        <div class="nbd-bleed" ng-if="currentVariant.info[currentSide.id].source.show_bleed == 1" ng-class="currentVariant.designArea['area_design_type'] == '2' ? 'nbd-rounded' : ''"
             ng-style="{'width': ( currentVariant.designArea['area_design_width']  - 2 * currentVariant.designArea['bleed_left_right'] * currentVariant.designArea['ratio']  ) * zoom * designScale,
                        'height' : ( currentVariant.designArea['area_design_height']  - 2 * currentVariant.designArea['bleed_top_bottom'] * currentVariant.designArea['ratio']  ) * zoom * designScale,
                        'top' : calcBleedSize('top', currentVariant.designArea['area_design_top'], currentVariant.designArea['bleed_top_bottom'], currentVariant.designArea['ratio']),
                        'left' : calcBleedSize('left', currentVariant.designArea['area_design_left'], currentVariant.designArea['bleed_left_right'], currentVariant.designArea['ratio'])
			}">
            
        </div>
        <div class="nbd-safe-zone" ng-if="currentVariant.info[currentSide.id].source.show_safe_zone == 1" ng-class="currentVariant.designArea['area_design_type'] == '2' ? 'nbd-rounded' : ''"
             ng-style="{'width': calcSafeZone('width', currentVariant.designArea['area_design_width'], currentVariant.designArea['bleed_left_right'], currentVariant.designArea['margin_left_right'], currentVariant.designArea['ratio']),
                        'height' : calcSafeZone('height', currentVariant.designArea['area_design_height'], currentVariant.designArea['bleed_top_bottom'], currentVariant.designArea['margin_top_bottom'], currentVariant.designArea['ratio']),
                        'top' : calcSafeZone('top', currentVariant.designArea['area_design_top'], currentVariant.designArea['bleed_top_bottom'], currentVariant.designArea['margin_top_bottom'], currentVariant.designArea['ratio']),
                        'left' : calcSafeZone('left', currentVariant.designArea['area_design_left'], currentVariant.designArea['bleed_left_right'], currentVariant.designArea['margin_left_right'], currentVariant.designArea['ratio'])
			}">
            
        </div>        
        <div id="replace-element-upload">
            <i class="fa fa-share-square-o nbd-tooltip-i18n" aria-hidden="true" data-lang="REPLACE_IMAGE" ng-click="preReplaceImage()"></i>
        </div>        
    </div>
</div></div>
<a ng-hide="settings['ui_mode'] == 1" href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>" class="shadow hover-shadow back-to-main-site nbd-tooltip-i18n"  data-placement="right" data-lang="RETURN_TO_SHOP"><i class="fa fa-home"></i></a>
<div class="side-navigator" ng-show="settings.is_mobile != '1'">
    <span class="fa fa-chevron-left side-nav" ng-click="previousOrientation(currentSide.id)" ng-class="currentSide.id > 0 ? 'ready' : '' " title="Previous"></span>
    <span class="side-name shadow">{{currentVariant.designArea.orientation_name}}</span>
    <span class="fa fa-chevron-right side-nav" ng-click="nextOrientation(currentSide.id)" ng-class="currentSide.id < (currentVariant.numberFrame - 1 ) ? 'ready' : '' " title="Next"></span>
</div>
<div class="top-center-menu" ng-style="{'width': designerWidth, 'left': offset}">   
    <i class="toolbar-menu undo-redo nbd-icon-undo2 nbd-tooltip-i18n shadow" data-placement="bottom" data-lang="UNDO" ng-click="undoDesign()" ng-class="orientationActiveUndoStatus ? 'ready' : ''"></i>
    <i class="toolbar-menu undo-redo nbd-icon-redo2 nbd-tooltip-i18n shadow" data-placement="bottom" data-lang="REDO" ng-click="redoDesign()" ng-class="orientationActiveRedoStatus ? 'ready' : ''"></i>
    
    <span class="toolbar-menu fa fa-arrows nbd-tooltip nbd-tooltip-i18n shadow" aria-hidden="true" data-tooltip-content="#tooltip_group_align" data-lang="ALIGN" data-placement="bottom" ng-show="showAlignToolbar"></span>
    <span class="toolbar-menu fa fa-cloud-download nbd-tooltip nbd-tooltip-i18n shadow" aria-hidden="true" data-tooltip-content="#tooltip_download_preview" data-lang="DOWNLOAD" data-placement="bottom" ng-show="state == 'dev'"></span>
    <span class="toolbar-menu fa fa-th nbd-tooltip-i18n shadow" aria-hidden="true"  data-lang="SNAP_GRID" data-placement="bottom" ng-click="snapGrid()"></span>
    <span class="fa fa-mouse-pointer toolbar-menu nbd-tooltip-i18n shadow" aria-hidden="true" data-lang="DESELECT_ALL" data-placement="bottom"  ng-click="deselectAll()"></span>	
    <span ng-show="currentVariant.info[currentSide.id].source.show_bleed == 1 || currentVariant.info[currentSide.id].source.show_safe_zone == 1" class="fa fa-bars toolbar-menu nbd-tooltip-i18n shadow" aria-hidden="true" data-lang="SHOW_BLEED" data-placement="bottom"  ng-click="showBleed()"></span>	
    <span class="toolbar-menu fa fa-lock nbd-tooltip nbd-tooltip-i18n shadow" aria-hidden="true" data-tooltip-content="#tooltip_lock_param"  data-lang="LOCK" data-placement="bottom" ng-click="getStatusItem()" ng-show="canvas.getActiveObject() && (task === 'create' || (task == 'edit' && design_type == 'template' ))"></span>
    <span class="toolbar-menu fa fa-cloud-upload nbd-tooltip-i18n shadow" aria-hidden="true"  data-lang="ELEMENT_UPLOAD" data-placement="bottom" ng-show="editableItem !== null && (editable.type === 'image' || editable.type === 'custom-image') && (task === 'create' || (task == 'edit' && design_type == 'template' ))" ng-click="setElementUpload()"></span>
    <span class="toolbar-menu toolbar-menu-handle fa fa-arrows nbd-tooltip-i18n shadow" aria-hidden="true" id="toolbar-menu-handle"></span>    
    <div style="display: none;">
        <div id="tooltip_group_align">
            <i class="toolbar-menu align-group nbd-icon-align-left nbd-tooltip-i18n" data-lang="ALIGN_LEFT" data-placement="top" ng-click="alignGroupLeft()"></i>
            <i class="toolbar-menu align-group nbd-icon-align-right nbd-tooltip-i18n" data-lang="ALIGN_RIGHT" data-placement="top" ng-click="alignGroupRight()"></i>
            <i class="toolbar-menu align-group nbd-icon-align-top nbd-tooltip-i18n" data-lang="ALIGN_TOP" data-placement="top" ng-click="alignGroupTop()"></i>
            <i class="toolbar-menu align-group  nbd-icon-align-bottom nbd-tooltip-i18n" data-lang="ALIGN_BOTTOM" data-placement="top"  ng-click="alignGroupBottom()"></i>
            <i class="toolbar-menu align-group  nbd-icon-align-vertical-middle nbd-tooltip-i18n" data-lang="ALIGN_MIDDLE_VERTICAL" data-placement="top" ng-click="alignGroupVer()"></i>
            <i class="toolbar-menu align-group  nbd-icon-align-horizontal-middle nbd-tooltip-i18n" data-lang="ALIGN_MIDDLE_HORIZONTAL" data-placement="top" data-container="body" ng-click="alignGroupHor()"></i>
        </div>
        <div id="tooltip_download_preview">
            <i class="toolbar-menu download-file nbd-icon-document-file-png nbd-tooltip-i18n" data-lang="PNG" data-placement="top" ng-click="downloadPNG()"></i>
            <i class="toolbar-menu download-file nbd-icon-document-file-jpg nbd-tooltip-i18n" data-lang="JPG" data-placement="top" ng-click="downloadJPG()"></i>
            <i class="toolbar-menu download-file nbd-icon-document-file-pdf nbd-tooltip-i18n" data-lang="PDF" data-placement="top" ng-click="downloadPDF()"></i>
        </div>
        <div id="tooltip_lock_param">
            <i class="toolbar-menu fa fa-lock nbd-tooltip-i18n" data-lang="LOCK_ALL_ADJUSMENT" data-placement="top" ng-click="lockItem('a')" ng-class="!editable.selectable ? 'active' : ''"></i>
            <i class="toolbar-menu fa fa-arrows-h nbd-tooltip-i18n" data-lang="LOCK_HORIZONTAL_MOVEMENT" data-placement="top" ng-click="lockItem('x')" ng-class="editable.lockMovementX ? 'active' : ''"></i>
            <i class="toolbar-menu fa fa-arrows-v nbd-tooltip-i18n" data-lang="LOCK_VERTITAL_MOVEMENT" data-placement="top" ng-click="lockItem('y')" ng-class="editable.lockMovementY ? 'active' : ''"></i>
            <i class="toolbar-menu fa fa-expand nbd-tooltip-i18n" data-lang="LOCK_HORIZONTAL_SCALING" data-placement="top" ng-click="lockItem('sx')" ng-class="editable.lockScalingX ? 'active' : ''"><sub>x</sub></i>
            <i class="toolbar-menu fa fa-expand nbd-tooltip-i18n" data-lang="LOCK_VERTITAL_SCALING" data-placement="top" ng-click="lockItem('sy')" ng-class="editable.lockScalingY ? 'active' : ''"><sub>y</sub></i>
            <i class="toolbar-menu fa fa-undo nbd-tooltip-i18n" data-lang="LOCK_ROTATION" data-placement="top" ng-click="lockItem('r')" ng-class="editable.lockRotation ? 'active' : ''"></i>
        </div>
    </div>      
</div>
<div id="frame" ng-style="{'top': designerWidth + 5, 'width': calcWidthThumb(_.size(currentVariant.info)) * 50, 'margin-left': -(calcWidthThumb(_.size(currentVariant.info)) * 50)/2}">
    <div class="container_frame">
        <span class="fa fa-angle-left left shadow" aria-hidden="true" ng-show="currentVariant.numberFrame > 4"></span>
        <span class="fa fa-angle-right right shadow" aria-hidden="true" ng-show="currentVariant.numberFrame > 4"></span>
        <div class="container-inner-frame">
            <div class="container_item">
                <a class="box-thumb nbd-tooltip-frame" data-placement="top" data-lang="{{orientation.source.orientation_name}}" ng-class="{active: currentVariant.orientationActive == orientation.name}" ng-repeat="orientation in currentVariant.info" ng-click="changeOrientation(orientation)">
                    <p class="box-thumb-inner"  ng-hide='state == "dev" && existDesign(orientation)'>
                        <img width="40" height="40" ng-if="orientation.source['bg_type'] == 'image'" ng-src="{{orientation.source['img_src']}}"  spinner-on-load/>
                        <i ng-show="orientation.source['bg_type'] == 'color'" 
                           ng-style="{'background': orientation.source['bg_color_value']}" ></i>
                        <i ng-show="orientation.source['bg_type'] == 'tran'" 
                           class="background-transparent" ></i>
                    </p>
                    <p class="box-thumb-inner" ng-show='state == "dev" && existDesign(orientation)'>
                        <img ng-src="{{dataCustomerDesign[orientation.name]}}" />
                    </p>
                </a>
            </div>
        </div>
    </div>
</div>