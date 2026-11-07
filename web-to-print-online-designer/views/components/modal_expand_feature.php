<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div id="dg-expand-feature"  class="modal fade nbdesigner_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button style="margin-top: 0;" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <ul role="tablist" id="upload-tabs" class="nbdesigner_modal_tab">
                    <li class="active" ><a href="#template-designs" role="tab" data-toggle="tab"><i class="fa fa-folder visible-xs" aria-hidden="true"></i><span class="hidden-xs">{{(langs['TEMPLATE_DESIGNS']) ? langs['TEMPLATE_DESIGNS'] : "Template designs"}}</span></a></li>					
                    <li ><a href="#my-designs" role="tab" data-toggle="tab" ng-click="loadUserDesign()"><i class="fa fa-user visible-xs" aria-hidden="true"></i><span class="hidden-xs">{{(langs['MY_DESIGNS']) ? langs['MY_DESIGNS'] : "My designs"}}</span></a></li>
                    <li ><a href="#my-cart" role="tab" data-toggle="tab" ng-click="loadDesignInCart()"><i class="fa fa-shopping-cart visible-xs" aria-hidden="true"></i><span class="hidden-xs">{{(langs['MY_DESIGNS_CART']) ? langs['MY_DESIGNS_CART'] : "My designs in cart"}}</span></a></li>
                </ul>                
            </div>            
            <div class="modal-body" >
                <div class="tab-content">
                    <div class="tab-pane active" id="template-designs">
                        <span ng-click="resetAdminDesign()" class="nbd-flat-btn hover-shadow feature-button" data-toggle="tooltip" data-original-title="{{(langs['RESET_TEMPLATE']) ? langs['RESET_TEMPLATE'] : 'Reset Template'}}"><i class="fa fa-refresh" aria-hidden="true"></i></span>
                        <span ng-click="loadAdminListDesign()" class="nbd-flat-btn hover-shadow feature-button" data-toggle="tooltip" data-original-title="{{(langs['LOAD_TEMPLATE']) ? langs['LOAD_TEMPLATE'] : 'Load Template'}}"><i class="fa fa-search" aria-hidden="true"></i></span>
                        <span ng-click="exportDesign()" class="nbd-flat-btn hover-shadow feature-button" data-toggle="tooltip" data-original-title="{{(langs['EXPORT_DESIGN_TO_JSON']) ? langs['EXPORT_DESIGN_TO_JSON'] : 'Export design to JSON'}}"><i class="fa fa-cloud-download" aria-hidden="true"></i></span>
                        <span ng-click="toggleImportArea()" class="nbd-flat-btn hover-shadow feature-button" data-toggle="tooltip" data-original-title="{{(langs['IMPORT_DESIGN_FROM_JSON']) ? langs['IMPORT_DESIGN_FROM_JSON'] : 'Import design from JSON'}}"><i class="fa fa-cloud-upload" aria-hidden="true"></i></span>
                        <div class="design-editor-container" id="design-editor-container">
                            <textarea class="design-editor" rows="10" id="design-json-content" placeholder="{{(langs['PASTE_CONTENT_JSON_FILE']) ? langs['PASTE_CONTENT_JSON_FILE'] : 'Paste content in file design.json'}}"></textarea>
                            <button ng-click="importDesign()" class="btn btn-primary shadow nbdesigner_upload btn-dialog">{{(langs['APPLY']) ? langs['APPLY'] : "Apply"}}</button>
                        </div>
                        <div class="nbdesigner-list-template" id="nbdesigner-list-template">  
                            <h3>Have {{_.size(adminListTemplate)}} {{(_.size(adminListTemplate) > 1) ? 'templates' : 'template'}}</h3>
                            <div class="nbd-template-wrap" ng-repeat="tem in adminListTemplate | limitTo : adminTemplatePageSize" >
                                <img ng-src="{{tem['src']}}" class="" ng-click="loadExtraAdminDesign(tem['id'])"/>
                            </div>
                            <div ng-show="_.size(adminListTemplate) > 8 && _.size(adminListTemplate) > adminTemplatePageSize">
                                <button class="btn btn-primary shadow nbdesigner_upload btn-dialog" ng-click="adminTemplatePageSize = adminTemplatePageSize + 8">{{(langs['MORE']) ? langs['MORE'] : "More"}}</button>
                                &nbsp;{{(langs['DISPLAYED']) ? langs['DISPLAYED'] : "Displayed"}} {{(adminTemplatePageSize < _.size(adminListTemplate)) ? adminTemplatePageSize : _.size(adminListTemplate)}}/{{_.size(adminListTemplate)}}
                            </div>  
                        </div>
                    </div> 
                    <div class="tab-pane" id="my-designs">
                        <div ng-show="!settings['is_logged']">
                        <a class="btn btn-primary shadow nbdesigner_upload" ng-click="login()">Login</a>
                        </div>
                        <div id="nbd-list-my-design" class="nbd-list-my-design" ng-show="settings['is_logged']">
                            <div class="nbd-template-wrap" ng-repeat="tem in listUserDesigns | limitTo : listUserDesignsPageSize" >
                                <img ng-src="{{tem['src']}}" class="" data-placement="right" ng-click="loadMyDesign(tem['id'])"/>                        
                            </div>
                        </div>
                        <div ng-show="_.size(listUserDesigns) > 8 && _.size(listUserDesigns) > listUserDesignsPageSize">
                            <button class="btn btn-primary shadow nbdesigner_upload btn-dialog" ng-click="loadMoreUserDesigns()">{{(langs['MORE']) ? langs['MORE'] : "More"}}</button>
                            &nbsp;{{(langs['DISPLAYED']) ? langs['DISPLAYED'] : "Displayed"}} {{(listUserDesignsPageSize < _.size(listUserDesigns)) ? listUserDesignsPageSize : _.size(listUserDesigns)}}/{{_.size(listUserDesigns)}}
                        </div>                          
                    </div>
                    <div class="tab-pane" id="my-cart">
                        <p>{{(langs['CHOOSE_DESIGN_FROM_CART']) ? langs['CHOOSE_DESIGN_FROM_CART'] : "Choose designs from cart"}} 
                            <span ng-click="loadDesignInCart(true)" class="nbd-flat-btn hover-shadow feature-button" data-placement="right" 
                                  style="margin-bottom: 0; margin-left: 15px;"
                                  data-toggle="tooltip" data-original-title="{{(langs['RELOAD_CART']) ? langs['RELOAD_CART'] : 'Reload Cart'}}">
                                <i class="fa fa-refresh" aria-hidden="true"></i>
                            </span>
                        </p>
                        <div class="nbd-list-cart-design" id="nbd-list-cart-design"> 
                            <img ng-repeat="tem in listDesignsInCart | limitTo : listDesignsInCartPageSize" ng-src="{{tem['src']}}" class="nbdesigner-template shadow hover-shadow" ng-click="loadCartDesign(tem['id'])"/>
                            <div ng-show="_.size(listDesignsInCart) > 8 && _.size(listDesignsInCart) > listDesignsInCartPageSize">
                                <button class="btn btn-primary shadow nbdesigner_upload btn-dialog" ng-click="loadMoreDesignsInCart()">{{(langs['MORE']) ? langs['MORE'] : "More"}}</button>
                                &nbsp;{{(langs['DISPLAYED']) ? langs['DISPLAYED'] : "Displayed"}} {{(listDesignsInCartPageSize < _.size(listDesignsInCart)) ? listDesignsInCartPageSize : _.size(listDesignsInCart)}}/{{_.size(listDesignsInCart)}}
                            </div>                             
                        </div>     
                    </div>      
                </div>    
            </div>
        </div>
    </div>
</div>