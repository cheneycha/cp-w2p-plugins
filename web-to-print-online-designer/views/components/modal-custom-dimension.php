<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div id="dg-custom-dimension"  class="modal fade nbdesigner_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom: 15px;">
                <b>{{(langs['CUSTOM_DIMENSION']) ? langs['CUSTOM_DIMENSION'] : "Custom dimension"}}</b>
                <button style="margin-top: 0;" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>	
            </div>            
            <div class="modal-body" style="padding: 15px;">
                <form name="dimensionForm">
                    <div ng-show="productOptions.type_dimension == 1">
                        <div class="form-group">
                            <label style="min-width: 200px;">{{(langs['WIDTH']) ? langs['WIDTH'] : "Width"}} <small>({{settings['nbdesigner_dimensions_unit']}})</small></label>
                            <input class="form-control hover-shadow dimensions nbdesigner_image_url" step="any" type="number" min="{{productOptions.min_width}}" name="customWidth" ng-model="customWidth"  placeholder="{{currentVariant.info[0].source.real_width}}" required/>
                            &nbsp;<small>Min: {{productOptions.min_width}}</small><small ng-if="productOptions.max_width > 0">, Max: {{productOptions.max_width}}</small>
                        </div>
                        <div class="form-group">
                            <label style="min-width: 200px;">{{(langs['HEIGHT']) ? langs['HEIGHT'] : "Height"}} <small>({{settings['nbdesigner_dimensions_unit']}})</small></label>
                            <input class="form-control hover-shadow dimensions nbdesigner_image_url" step="any" type="number" min="{{productOptions.min_height}}" name="customHeight" ng-model="customHeight"  placeholder="{{currentVariant.info[0].source.real_height}}" required/>
                            &nbsp;<small>Min: {{productOptions.min_height}}</small><small ng-if="productOptions.max_height > 0">, Max: {{productOptions.max_height}}</small>
                        </div> 
                        <div class="form-group">
                            <label style="min-width: 200px;">{{(langs['NUMBER_OF_SIDE']) ? langs['NUMBER_OF_SIDE'] : "Number of sides/pages"}} </label>
                            <input type="number" step="1" min="1" class="form-control hover-shadow dimensions nbdesigner_image_url" name="customSide" ng-model="customSide" ng-disabled="productOptions.dynamic_side == 0"  placeholder="{{currentVariant.info.length}}" required/>
                        </div>  
                        <div class="form-group">
                            <button ng-disabled="!(dimensionForm.customWidth.$valid && dimensionForm.customHeight.$valid && dimensionForm.customSide.$valid)" type="button" class="btn btn-primary shadow nbdesigner_upload"  ng-click="changeDimension()"><i class="fa fa-check" aria-hidden="true"></i> {{(langs['OK']) ? langs['OK'] : "OK"}}</button>
                            <button ng-show="originProduct.length > 0" type="button" class="btn btn-primary shadow nbdesigner_upload" ng-click="reverseDimension()"><i class="fa fa-undo" aria-hidden="true"></i> {{(langs['REVERSE']) ? langs['REVERSE'] : "Reverse"}}</button>
                            <button ng-show="customDefaultReady" class="btn btn-primary shadow nbdesigner_upload"  ng-click="cancelDimension()"><i class="fa fa-times" aria-hidden="true"></i> {{(langs['DEFAULT']) ? langs['DEFAULT'] : "Default"}}</button>
                            <button style="background: #fff; border-radius: 0;" type="button" class="btn hover-shadow shadow"  ng-click="cancelDimension()"><i class="fa fa-times" aria-hidden="true"></i> {{(langs['CANCEL']) ? langs['CANCEL'] : "Cancel"}}</button>
                        </div>  
                    </div>    
                    <div ng-show="productOptions.type_dimension == 2" style="margin-bottom: 15px;">
                        <div class="form-group">
                            <label style="min-width: 200px;">{{(langs['SIZE']) ? langs['SIZE'] : "Size"}} </label>
                            <div class="btn-group nbd-dropdown-option" style="margin-left: 15px;">
                                <button class="btn btn-primary dropdown-toggle shadow hover-shadow" type="button" data-toggle="dropdown">{{currentDimension}}&nbsp;&nbsp;<span class="caret"></span></button>
                                <ul class="dropdown-menu dropup  shadow hover-shadow">
                                    <li ng-repeat="dim in productOptions.defined_dimension"><a href="javascript:void(0);" ng-click="_changeDimension(dim)">{{dim.width + ' x ' + dim.height + ' ' + settings['nbdesigner_dimensions_unit']}}</a></li>                                    
                                </ul>  
                            </div> 
                            <div class="form-group" style="margin-top: 15px;">
                                <label style="min-width: 200px;">{{(langs['NUMBER_OF_SIDE']) ? langs['NUMBER_OF_SIDE'] : "Number of sides/pages"}} </label>
                                <input type="number" step="1" min="1" class="form-control hover-shadow dimensions nbdesigner_image_url" name="customSide" ng-model="customSide" ng-disabled="productOptions.dynamic_side == 0"  placeholder="{{currentVariant.info.length}}" required/>
                            </div>                              
                        </div>
                        <div class="form-group">
                            <button ng-show='customReadyToChagne' type="button" class="btn btn-primary shadow nbdesigner_upload"  ng-click="changeDimension()"><i class="fa fa-check" aria-hidden="true"></i> {{(langs['OK']) ? langs['OK'] : "OK"}}</button>
                            <button ng-show="originProduct.length > 0" class="btn btn-primary shadow nbdesigner_upload" ng-click="reverseDimension()"><i class="fa fa-undo" aria-hidden="true"></i> {{(langs['REVERSE']) ? langs['REVERSE'] : "Reverse"}}</button>
                            <button ng-show="customDefaultReady" class="btn btn-primary shadow nbdesigner_upload"  ng-click="cancelDimension()"><i class="fa fa-cog" aria-hidden="true"></i> {{(langs['DEFAULT']) ? langs['DEFAULT'] : "Default"}}</button>
                            <button style="background: #fff; border-radius: 0;" type="button" class="btn hover-shadow shadow"  ng-click="cancelDimension()"><i class="fa fa-times" aria-hidden="true"></i> {{(langs['CANCEL']) ? langs['CANCEL'] : "Cancel"}}</button>
                        </div>                         
                    </div> 
                    <div class="form-group" ng-hide="productOptions.dynamic_side == 0 || state != 'dev'">
                        <hr style="border-top: 1px solid #ddd;"/>
                        <div class="form-group">
                            <label style="min-width: 200px; vertical-align: top;">{{(langs['INSERT_PAGE']) ? langs['INSERT_PAGE'] : "Insert page"}} </label>
                            <div style="display: inline-block; margin-left: 15px;">
                                <div class="md-radio" style="margin-top: 0;">
                                    <input id="pageSourceType1" type="radio" name="pageSourceType" ng-model="pageSourceType" value="1" >
                                    <label for="pageSourceType1">{{(langs['COPY_FROM']) ? langs['COPY_FROM'] : "Copy from"}}</label>
                                    <div class="btn-group nbd-dropdown-option" ng-show="pageSourceType == 1">
                                        <button class="btn btn-primary dropdown-toggle shadow hover-shadow" type="button" data-toggle="dropdown">{{currentPageSource.orientation_name}}&nbsp;&nbsp;<span class="caret"></span></button>
                                        <ul class="dropdown-menu dropup  shadow hover-shadow">
                                            <li ng-repeat="variation in varaints.areaDesign"><a href="javascript:void(0);" ng-click="changePageSource(variation)">{{variation.orientation_name}}</a></li>                                    
                                        </ul>  
                                    </div>                                    
                                </div>
                                <div class="md-radio">
                                    <input id="pageSourceType2" type="radio" name="pageSourceType" ng-model="pageSourceType" value="2">
                                    <label for="pageSourceType2">{{(langs['CUSTOM_DIMENSION']) ? langs['CUSTOM_DIMENSION'] : "Custom dimension"}}</label>
                                    <div ng-show="pageSourceType == 2 && productOptions.type_dimension == 2" style="display: inline-block;">
                                        <div class="btn-group nbd-dropdown-option">
                                            <button class="btn btn-primary dropdown-toggle shadow hover-shadow" type="button" data-toggle="dropdown">Choose&nbsp;&nbsp;<span class="caret"></span></button>
                                            <ul class="dropdown-menu dropup  shadow hover-shadow">
                                                <li ng-repeat="dim in productOptions.defined_dimension"><a href="javascript:void(0);" ng-click="_changePageDimension(dim)">{{dim.width + ' x ' + dim.height + ' ' + settings['nbdesigner_dimensions_unit']}}</a></li>                                    
                                            </ul>  
                                        </div>      
                                    </div>
                                    <div ng-show="pageSourceType == 2 && productOptions.type_dimension == 1" style="display: block;">
                                        <div ng-show="productOptions.type_dimension == 1" style="display:block;">
                                            <div style="margin: 15px 0;">
                                                <span style="min-width: 100px;display: inline-block; color: #000;">{{(langs['WIDTH']) ? langs['WIDTH'] : "Width"}} <small>({{settings['nbdesigner_dimensions_unit']}})</small></span>
                                                <input style="width: 100px;" class="form-control hover-shadow dimensions nbdesigner_image_url" step="any" type="number" min="{{productOptions.min_width}}" name="customWidth2" ng-model="customWidth2"  placeholder="{{currentVariant.info[0].source.real_width}}"/>
                                            </div>
                                            <div style="margin: 15px 0;">
                                                <span style="min-width: 100px;display: inline-block; color: #000;">{{(langs['HEIGHT']) ? langs['HEIGHT'] : "Height"}} <small>({{settings['nbdesigner_dimensions_unit']}})</small></span>
                                                <input style="width: 100px;" class="form-control hover-shadow dimensions nbdesigner_image_url" step="any" type="number" min="{{productOptions.min_height}}" name="customHeight2" ng-model="customHeight2"  placeholder="{{currentVariant.info[0].source.real_height}}"/>
                                            </div>                                            
                                        </div>                                         
                                    </div>      
                                </div>    
                            </div>
                        </div>   
                        <div class="form-group">
                            <label style="min-width: 200px;">{{(langs['POSITION']) ? langs['POSITION'] : "Position"}} </label>
                            <div class="btn-group nbd-dropdown-option nbd-insert-page-type" style="margin-left: 15px;">
                                <button class="btn btn-primary dropdown-toggle shadow hover-shadow" type="button" data-toggle="dropdown">{{positionPageType == 1 ? langs['BEFORE'] : langs['AFTER'] }}&nbsp;&nbsp;<span class="caret"></span></button>
                                <ul class="dropdown-menu dropup  shadow hover-shadow">                                  
                                    <li><a href="javascript:void(0);" ng-click="positionPageType = 1">{{(langs['BEFORE']) ? langs['BEFORE'] : "Before"}}</a></li>                                    
                                    <li selected="selected"><a href="javascript:void(0);" ng-click="positionPageType = 2">{{(langs['AFTER']) ? langs['AFTER'] : "After"}}</a></li>                                    
                                </ul>  
                            </div> 
                            <span style="margin-left: 15px;">Of</span>
                            <div class="btn-group nbd-dropdown-option nbd-insert-page-position" style="margin-left: 15px;">
                                <button class="btn btn-primary dropdown-toggle shadow hover-shadow" type="button" data-toggle="dropdown">{{currentPagePosition.orientation_name}}&nbsp;&nbsp;<span class="caret"></span></button>
                                <ul class="dropdown-menu dropup  shadow hover-shadow">
                                    <li ng-repeat="variation in varaints.areaDesign"><a href="javascript:void(0);" ng-click="changePagePosition(variation, $index)">{{variation.orientation_name}}</a></li>                                    
                                </ul>  
                            </div>                            
                        </div> 
                        <div>
                            <button ng-show="pageSourceType == 1 || (customWidth2 != undefined && customHeight2 != undefined)" type="button" class="btn btn-primary shadow nbdesigner_upload"  ng-click="insertPage()"><i class="fa fa-check" aria-hidden="true"></i> {{(langs['OK']) ? langs['OK'] : "OK"}}</button>
                        </div>    
                    </div>                      
                </form>    
            </div>
        </div>
    </div>
</div>