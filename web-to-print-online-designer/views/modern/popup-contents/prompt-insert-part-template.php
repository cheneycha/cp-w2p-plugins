<div class="nbd-popup popup-select insert-part-template-alert" data-animate="scale">
    <div class="main-popup">
        <i class="icon-nbd icon-nbd-clear close-popup"></i>
        <div class="head">
            <?php esc_html_e('Replace stage content','web-to-print-online-designer'); ?>
        </div>
        <div class="body">
            <div class="main-body">
                <span class="title"><?php esc_html_e('By selecting another template you are deleting all the content on the current stage. To restore, click Undo button.','web-to-print-online-designer'); ?></span>
                <div class="main-select">
                    <button ng-click="closePopupInertPartTem()" class="nbd-button select-no"><i class="icon-nbd icon-nbd-clear"></i> <?php esc_html_e('No','web-to-print-online-designer'); ?></button>
                    <button ng-click="_insertPartTemplate(_currentTempId, _currentpartIndex)" class="nbd-button select-yes"><i class="icon-nbd icon-nbd-fomat-done"></i> <?php esc_html_e('Replace','web-to-print-online-designer'); ?></button>
                </div>
            </div>
        </div>
        <div class="footer"></div>
    </div>
</div>

