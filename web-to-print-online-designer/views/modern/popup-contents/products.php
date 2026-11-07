<div class="nbd-popup white-popup popup-nbd-products" data-animate="scale">
    <div class="overlay-popup"></div>
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
            <h2><?php esc_html_e('Products','web-to-print-online-designer'); ?></h2>
        </div>
        <div class="body">
            <div class="main-body">
                <div class="tab-scroll">
                    <div class="nbd-search-product-wrap">
                        <input class="nbd-search-product" ng-model="searchProductName" placeholder="<?php esc_html_e('Product name','web-to-print-online-designer'); ?>" />
                        <i class="icon-nbd icon-nbd-fomat-search"></i>
                    </div>
                    <div class="nbd-products-wrap">
                        <div class="nbd-product-wrap" 
                            title="<?php esc_html_e('Select Product','web-to-print-online-designer'); ?>"
                            ng-click="changeProduct( product.product_id )" 
                            ng-repeat="product in resource.products | filter: {name: searchProductName, product_id: '!<?php echo $product_id; ?>'}">
                            <div class="nbd-product-image" ng-style="{'background-image': 'url(' + product.src + ')'}" ></div>
                            <div class="nbd-product-title"><a terget="_blank" href="{{product.url}}">{{product.name}}</a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer"></div>
    </div>
</div>