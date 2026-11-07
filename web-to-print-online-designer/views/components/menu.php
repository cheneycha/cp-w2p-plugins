<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div id="main_menu">	
    <ul class="tool_draw">
        <li ng-show="settings['nbdesigner_enable_text'] == 'yes'">
            <a class="add_text shadow nbd-tooltip-i18n" ng-click="addText()" data-lang="ADD_TEXT" data-placement="right">
                <i class="fa fa-font" aria-hidden="true"></i>
            </a>
        </li>
        <li ng-show="settings['nbdesigner_enable_clipart'] == 'yes'">
            <a class="add_art shadow nbd-tooltip-i18n" data-toggle="modal" data-target="#dg-cliparts" ng-click="loadArt()" data-lang="ADD_CLIPART" data-placement="right">
                <i class="fa fa-picture-o" aria-hidden="true"></i>
            </a>
        </li>
        <li ng-show="settings['nbdesigner_enable_image'] == 'yes'">
            <a class="add_image shadow nbd-tooltip-i18n" data-toggle="modal" data-target="#dg-myclipart" ng-click="loadLocalStorageImage()" data-lang="ADD_IMAGE" data-placement="right">
                <i class="fa fa-camera-retro" aria-hidden="true"></i>
            </a>
        </li>
        <li ng-show="settings['nbdesigner_enable_draw'] == 'yes'">
            <a class="draw_free shadow nbd-tooltip-i18n" ng-click="showDrawConfig()" data-lang="FREE_DRAW" data-placement="right">
                <i class="fa fa-paint-brush" aria-hidden="true"></i>
            </a>
        </li>
        <li ng-show="settings['nbdesigner_enable_qrcode'] == 'yes'">
            <a class="add_code shadow nbd-tooltip-i18n" data-toggle="modal" data-target="#dg-qrcode" data-lang="ADD_QRCODE" data-placement="right">
                <i class="fa fa-qrcode" aria-hidden="true"></i>
            </a>
        </li>
    </ul>
    <div class="container_menu shadow hover-shadow">	
        <div id="menu">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <div id="layer" class="shadow hover-shadow nbd-tooltip-i18n" data-lang="LAYERS" data-placement="top">
        <div class="nav_layer">
            <span></span>
            <span></span>
            <span></span>		
        </div>
    </div>
    <div id="gesture" class="fa fa-hand-o-up nbd-tooltip-i18n shadow hover-shadow" data-lang="TOOL" data-placement="top" ng-show="currentLayers.length > 0"></div>	
    <div id="info"  ng-click="<?php if( $task == 'new' && $ui_mode == 2 ) echo 'saveCart()'; else echo 'storeDesign()'; ?>" class="shadow hover-shadow">
        <div class="container_info">
            <p>
                <span class="fa fa-<?php if( $task == 'new' && $ui_mode == 2 ) echo 'shopping-cart'; else echo 'floppy-o';  ?> add-to-cart"></span>
                <?php if( $task == 'new' && $ui_mode == 2 && $task2 == ''): ?>
                {{(langs['ADD_TO_CART']) ? langs['ADD_TO_CART'] : "Add to cart"}}
                <?php else: ?>
                {{(langs['COMPLETE']) ? langs['COMPLETE'] : "Complete"}}
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>