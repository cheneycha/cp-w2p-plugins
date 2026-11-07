<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div id="dg-load-product"  class="modal fade nbdesigner_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom: 15px;">
                <b>{{(langs['CHOOSE_PRODUCT']) ? langs['CHOOSE_PRODUCT'] : "Choose product"}}</b>
                <button style="margin-top: 0;" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>  
            </div>            
            <div class="modal-body" style="padding: 15px;">                
                <div class="product-search">
                    <input class="nbd-product-search" ng-model="productName" placeholder="{{(langs['SEARCH']) ? langs['SEARCH'] : 'Search'}}"/>
                </div>
                <div id="nbd-list-product" class="nbd-list-product">
                    <div ng-repeat="product in listProducts | filter : {name: productName} | limitTo : listProductsPageSize" class="nbd-product-wrap">
                        <div class="nbd-product-img" ng-click="switchProduct(product)">
                            <img style="max-width: 100%;" ng-src="{{product.src}}" />
                        </div>  
                        <div class="nbd-product-info" id="">
                            <p class="product-name">{{product.name}}</p>
                            <div class="product-action">
                                <span class="fa fa-spinner fa-pulse" aria-hidden="true"></span>
                                <span class="fa fa-info-circle active" aria-hidden="true" ng-click="loadProductDescription( $event, product )"></span>
                            </div>    
                        </div>
                    </div> 
                </div>    
                <div ng-show=" _.size(listProducts) > listProductsPageSize">
                    <button class="btn btn-primary shadow nbdesigner_upload btn-dialog" ng-click="loadMoreProduct()">{{(langs['MORE']) ? langs['MORE'] : "More"}}</button>
                    &nbsp;{{(langs['DISPLAYED']) ? langs['DISPLAYED'] : "Displayed"}} {{(listProductsPageSize < _.size(listProducts)) ? listProductsPageSize : _.size(listProducts)}}/{{_.size(listProducts)}}
                </div>      
            </div>
        </div>
    </div>
</div>
