<div class="<?php if( $active_cliparts ) echo 'active'; ?> tab nbd-onload" ng-if="settings['nbdesigner_enable_clipart'] == 'yes'" id="tab-svg" nbd-scroll="scrollLoadMore(container, type)" data-container="#tab-svg" data-type="clipart" data-offset="20">
    <div class="nbd-search">
        <input type="text" name="search" placeholder="<?php esc_html_e('Search clipart', 'web-to-print-online-designer'); ?>" ng-model="resource.clipart.filter.search"/>
        <i class="icon-nbd icon-nbd-fomat-search"></i>
    </div>
    <div class="cliparts-category" ng-class="resource.clipart.data.cat.length > 0 ? '' : 'nbd-hiden'">
        <div class="nbd-button nbd-dropdown">
            <span>{{resource.clipart.filter.currentCat.name}}</span>
            <i class="icon-nbd icon-nbd-chevron-right rotate90"></i>
            <div class="nbd-sub-dropdown" data-pos="center">
                <ul class="nbd-perfect-scroll">
                    <li ng-click="changeCat('clipart', cat)" ng-repeat="cat in resource.clipart.data.cat"><span>{{cat.name}}</span><span>{{cat.amount}}</span></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="tab-main tab-scroll">
        <div class="nbd-items-dropdown" >
            <div>
                <div class="clipart-wrap">
                    <div class="clipart-item" nbd-drag="art.url" extenal="false" type="svg"  ng-repeat="art in resource.clipart.filteredArts | limitTo: resource.clipart.filter.currentPage * resource.clipart.filter.perPage" repeat-end="onEndRepeat('clipart')">
                        <img  ng-src="{{art.url}}" ng-click="addArt(art, true, true)" alt="{{art.name}}">
                    </div>
                </div>
                <div class="loading-photo" >
                    <svg class="circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                    </svg>
                </div>
                <div class="tab-load-more" style="display: none;" ng-show="!resource.clipart.onload && resource.clipart.filteredArts.length && resource.clipart.filter.currentPage * resource.clipart.filter.perPage < resource.clipart.filter.total">
                    <a class="nbd-button" ng-click="scrollLoadMore('#tab-svg', 'clipart')"><?php esc_html_e('Load more','web-to-print-online-designer'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
