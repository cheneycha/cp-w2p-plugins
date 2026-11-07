<div class="nbd-popup popup-nbd-crop" data-animate="bottom-to-top">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <i class="icon-nbd icon-nbd-clear close-popup"></i>
        <div class="overlay-main active">
            <div class="loaded">
                <svg class="circular" viewBox="25 25 50 50">
                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                </svg>
            </div>
        </div>
        <div class="head">
            <h2><?php esc_html_e('Crop image','web-to-print-online-designer'); ?></h2>
        </div>
        <div class="body">
            <div class="main-body">
                <div class="main-body-inner">
                    <img id="crop-source" ng-if="cropObj.status" ng-src="{{cropObj.src}}" />
                </div>
                <div class="main-body-inner">
                    <div class="canvas-wrap">
                        <canvas id="crop-handle-wrap"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <button ng-click="cropImage()" class="nbd-button"><?php esc_html_e('Crop','web-to-print-online-designer'); ?> <i class="icon-nbd icon-nbd-fomat-done"></i></button>
        </div>
    </div>
</div>

