<?php if (!defined('ABSPATH')) exit; ?>
<div class="modal fade" id="dg-product-info-preview">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom: 15px;">
                <b>{{(langs['INFORMATION']) ? langs['INFORMATION'] : "Information"}}</b>
                <button style="margin-top: 0;" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>	                
            </div>
            <div class="modal-body" style="padding: 15px;">
                <div id="product-info-preview-wrap" class="product-info-wrap">
                    <div id="product-info-preview-wrap-inner">
                        
                    </div>
                </div>    
                <div>
                    <button class="btn btn-primary shadow  nbdesigner_upload" ng-click="_switchProduct()">{{(langs['CHOOSE']) ? langs['CHOOSE'] : "Choose"}}</button>
                </div>
            </div>
        </div>
    </div>
</div>