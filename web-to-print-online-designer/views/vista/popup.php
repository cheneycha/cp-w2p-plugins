<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<div class="v-popup" data-animate="scale">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <i class="nbd-icon-vista nbd-icon-vista-clear close-popup"></i>
        <div class="head">
        </div>
        <div class="body"></div>
        <div class="footer">
            <div class="nbd-list-button">
                <button class="nbd-button"><?php _e('Pause','web-to-print-online-designer'); ?></button>
                <button class="nbd-button"><?php _e('Stop Webcam','web-to-print-online-designer'); ?></button>
                <button class="nbd-button"><?php _e('Capture','web-to-print-online-designer'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="v-popup v-popup-terms" data-animate="fixed-top">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <i class="nbd-icon-vista nbd-icon-vista-clear close-popup"></i>
        <div class="head">Image upload terms</div>
        <div class="body">
            {{settings['nbdesigner_upload_term']}}
        </div>
        <div class="footer"></div>
    </div>
</div>

<div class="v-popup v-popup-webcam" data-animate="scale">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <i class="nbd-icon-vista nbd-icon-vista-clear close-popup"></i>
        <div class="head">
        </div>
        <div class="body"></div>
        <div class="footer">
            <div class="nbd-list-button">
                <button class="v-btn"><?php _e('Pause','web-to-print-online-designer'); ?></button>
                <button class="v-btn">Stop <?php _e('Webcam','web-to-print-online-designer'); ?></button>
                <button class="v-btn"><?php _e('Capture','web-to-print-online-designer'); ?></button>
            </div>
        </div>
    </div>
</div>

<div class="v-popup v-popup-select clear-stage-alert" data-animate="scale">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <i class="nbd-icon-vista nbd-icon-vista-clear close-popup"></i>
        <div class="head">
            <?php _e('Delete All Layers','web-to-print-online-designer'); ?>
        </div>
        <div class="body">
            <div class="main-body">
                <span class="title"><?php _e('Are you sure you want to delete all layers?','web-to-print-online-designer'); ?></span>
                <div class="main-select">
                    <button ng-click="closePopupClearStage()" class="v-btn select-no"><i class="nbd-icon-vista nbd-icon-vista-clear"></i> <?php _e('No','web-to-print-online-designer'); ?></button>
                    <button ng-click="clearStage()" class="v-btn select-yes"><i class="nbd-icon-vista nbd-icon-vista-done"></i> <?php _e('Yes','web-to-print-online-designer'); ?></button>
                </div>
            </div>
        </div>
        <div class="footer"></div>
    </div>
</div>