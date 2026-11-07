<div class="nbd-popup popup-select clear-all-stage-alert" data-animate="scale">
    <div class="main-popup">
        <i class="icon-nbd icon-nbd-clear close-popup"></i>
        <div class="head">
            <?php esc_html_e('Clear All Stages','web-to-print-online-designer'); ?>
        </div>
        <div class="body">
            <div class="main-body">
                <span class="title"><?php esc_html_e('Are you sure you want to clear all stages?','web-to-print-online-designer'); ?></span>
                <div class="main-select">
                    <button ng-click="closePopupClearAllStages()" class="nbd-button select-no"><i class="icon-nbd icon-nbd-clear"></i> <?php esc_html_e('No','web-to-print-online-designer'); ?></button>
                    <button ng-click="clearAllStage()" class="nbd-button select-yes"><i class="icon-nbd icon-nbd-fomat-done"></i> <?php esc_html_e('Yes','web-to-print-online-designer'); ?></button>
                </div>
            </div>
        </div>
        <div class="footer"></div>
    </div>
</div>