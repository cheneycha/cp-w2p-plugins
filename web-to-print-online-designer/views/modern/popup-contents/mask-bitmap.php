<div class="nbd-popup popup-mask-bitmap" data-animate="scale">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <div class="overlay-main active">
            <div class="loaded">
                <svg class="circular" viewBox="25 25 50 50" >
                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                </svg>
            </div>
        </div>
        <i class="icon-nbd icon-nbd-clear close-popup"></i>
        <div class="head">
            <h2><?php esc_html_e('Convert images to vector files','web-to-print-online-designer'); ?></h2>
        </div>
        <div class="body">
           	<div class="svg-wrap">
        		<img/>
           	</div>
        </div>
        <div class="footer">
        	<div class="mask-bitmap-btn">
        		<p>After import, please use the layer mask function again.</p>
	            <a href="#" ng-click="pcAddSvgFromString($event)" data-current-layer="0" class="nbd-button nbd-hover-shadow"><?php esc_html_e('Import vector file','web-to-print-online-designer'); ?></a>
            </div>
        </div>
    </div>
</div>