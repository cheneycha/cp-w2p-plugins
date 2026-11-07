<div class="nbd-popup popup-nbd-user-design" data-animate="scale">
    <div class="main-popup">
        <div class="overlay-main">
            <div class="loaded">
                <svg class="circular" viewBox="25 25 50 50">
                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                </svg>
            </div>
        </div>
        <i class="icon-nbd icon-nbd-clear close-popup"></i>
        <div class="head">
            <h2><?php esc_html_e('My design','web-to-print-online-designer'); ?></h2>
        </div>
        <div class="body">
            <div class="main-body">
                <div class="tab-scroll">
                    <div class="nbd-user-design" ng-repeat="design in resource.myDesigns" ng-click="loadUserDesigns(design.id)">
                        <img ng-src="{{design.preview}}" />
                        <span class="action-button left" ng-click="loadUserDesigns(design.id); $event.stopPropagation();"><?php esc_html_e('Select','web-to-print-online-designer'); ?></span>
                        <span class="action-button right" ng-click="deleteUserDesign(design.id, $index); $event.stopPropagation();">
                            <i class="icon-nbd icon-nbd-clear" title="<?php esc_html_e('Delete','web-to-print-online-designer'); ?>"></i> <?php esc_html_e('Delete','web-to-print-online-designer'); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer"></div>
    </div>
</div>

