<div class="nbd-popup popup-nbo-options" data-animate="bottom-to-top">
    <div class="overlay-popup"></div>
    <div class="main-popup" >
        <i class="icon-nbd icon-nbd-clear close-popup"></i>
        <div class="overlay-main active">
            <div class="loaded">
                <svg class="circular" viewBox="25 25 50 50" >
                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                </svg>
            </div>
        </div>
        <div class="head">
            <h2>{{settings.task2 == '' ? "<?php esc_html_e('Choose options','web-to-print-online-designer'); ?>" : "<?php esc_html_e('Options preview','web-to-print-online-designer'); ?>"}} <?php if($task2 != ''): ?><a class="edit-options" href="<?php echo $link_edit_option; ?>"><?php esc_html_e('Edit options','web-to-print-online-designer'); ?></a><?php endif; ?> </h2>
        </div>
        <div class="body">
            <div class="main-body" id="nbo-options-wrap">
            </div>
        </div>
        <div class="footer" >
            <span ng-if="!printingOptionsAvailable" class="nbd-invalid-form"><?php esc_html_e('Please choose options before apply to start design!', 'web-to-print-online-designer'); ?></span><a ng-class="printingOptionsAvailable ? '' : 'nbd-disabled'" class="nbd-button nbo-apply" ng-click="applyOptions()">{{settings.task2 == '' ? "<?php esc_html_e('Apply options','web-to-print-online-designer'); ?>" : "<?php esc_html_e('Start design','web-to-print-online-designer'); ?>" }}</a>
        </div>
    </div>
</div>