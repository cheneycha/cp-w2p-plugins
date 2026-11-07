<div class="nbd-popup popup-share" data-animate="scale">
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
            <h2><?php esc_html_e('Share this design','web-to-print-online-designer'); ?></h2>
        </div>
        <div class="body">
            <div class="share-with">
                <span><?php esc_html_e('Share with','web-to-print-online-designer'); ?>:</span>
                <ul class="socials">
                    <li ng-click="createShareLink('facebook', 'https://facebook.com/sharer/sharer.php?display=popup&u=')" class="social facebook"><i class="icon-nbd icon-nbd-facebook-circle nbd-hover-shadow"></i></li>
                    <li ng-click="createShareLink('twitter', 'https://twitter.com/share?url=')" class="social twitter"><i class="icon-nbd icon-nbd-twitter-circle nbd-hover-shadow"></i></li>
                    <li ng-click="createShareLink('link', '')" class="social link">
                        <i class="icon-nbd nbd-hover-shadow">
                            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path fill="#888" d="M3.9 12c0-1.71 1.39-3.1 3.1-3.1h4V7H7c-2.76 0-5 2.24-5 5s2.24 5 5 5h4v-1.9H7c-1.71 0-3.1-1.39-3.1-3.1zM8 13h8v-2H8v2zm9-6h-4v1.9h4c1.71 0 3.1 1.39 3.1 3.1s-1.39 3.1-3.1 3.1h-4V17h4c2.76 0 5-2.24 5-5s-2.24-5-5-5z"/></svg>
                        </i>
                    </li>
                </ul>
            </div>
            <div ng-show="resource.social.type != 'link'">
                <div class="share-content">
                    <textarea ng-change="updateShareLink()" placeholder="<?php esc_html_e('Write a comment'); ?>" ng-model="resource.social.comment"></textarea>
                </div>
                <div class="share-btn">
                    <a href="{{resource.social.link}}" target="_blank" ng-class="resource.social.link != '' ? '' : 'nbd-disabled'" class="nbd-button nbd-hover-shadow"><?php esc_html_e('Share now','web-to-print-online-designer'); ?></a>
                </div>
            </div>
            <div ng-show="resource.social.type == 'link'">
                <div class="share-content share-design-link">
                    <b><?php esc_html_e('Design link','web-to-print-online-designer'); ?></b>
                </div>
                <div class="share-content share-design-link">
                    <input value="{{resource.social.design_link}}" /><i title="<?php esc_html_e('Click to copy','web-to-print-online-designer'); ?>" ng-click="copyShareLink($event)" class="icon-nbd icon-nbd-content-copy"></i>
                </div>
                <div class="share-content share-design-link">
                    <b><?php esc_html_e('Photo links','web-to-print-online-designer'); ?></b>
                </div>
                <div class="share-content share-design-link" ng-repeat="image in resource.social.images">
                    <input value="{{image}}" /><i title="<?php esc_html_e('Click to copy','web-to-print-online-designer'); ?>" ng-click="copyShareLink($event)" class="icon-nbd icon-nbd-content-copy"></i>
                </div>
            </div>
        </div>
        <div class="footer"></div>
    </div>
</div>