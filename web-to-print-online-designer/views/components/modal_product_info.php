<?php if (!defined('ABSPATH')) exit; ?>
<div class="modal fade" id="dg-product-info">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button style="margin-top: 0;" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>	
                <ul role="tablist" class="nbdesigner_modal_tab">
                    <li class="active" ><a href="#product-information" role="tab" data-toggle="tab"><i class="fa fa-cube visible-xs" aria-hidden="true"></i><span class="hidden-xs">{{(langs['INFORMATION']) ? langs['INFORMATION'] : "Information"}}</span></a></li>	
                    <li class=" " ng-show="settings.product_type == 'variable'"><a href="#product-variation" role="tab" data-toggle="tab"><i class="fa fa-info visible-xs" aria-hidden="true"></i><span class="hidden-xs">{{(langs['VARIATIONS']) ? langs['VARIATIONS'] : "Variations"}}</span></a></li>
                </ul>                  
            </div>
            <div class="modal-body" style="padding: 15px;">
                <div class="tab-content">
                    <div id="product-information" class="tab-pane active" >
                        <div id="product-info-wrap" class="product-info-wrap">
                            <div id="product-info-wrap-inner">
                                <?php
                                    $image = get_the_post_thumbnail_url($product_id, 'post-thumbnail');
                                    $image = $image ? $image : wc_placeholder_img_src();  
                                    nbdesigner_get_template('product-info.php', array(
                                        'id' =>  $product_id,
                                        'title' =>  $_product->get_title(),
                                        'description' =>  $_product->get_description(),
                                        'short_description' =>  $_product->get_short_description(),
                                        'price' =>  $_product->get_price(),
                                        'permalink' =>  $_product->get_permalink(),
                                        'image' =>  $image
                                    ));                                                                   
                                ?>
                            </div>
                        </div> 
                    </div> 
                    <div id="product-variation" class="tab-pane" ng-show="settings.product_type == 'variable'">
                        <div id="product-variation-wrap">
                            <div id="variation-form-wrap">
                            <?php                        
                            if( $product_type == 'variable' ){
                                $attributes = $_product->get_variation_attributes();
                                $available_variations = $_product->get_available_variations();
                                $selected_attributes = $_product->get_default_attributes();
                                $attribute_keys = array_keys( $attributes );
                                nbdesigner_get_template('product-variation-form.php', array(
                                    'attributes' => $attributes,
                                    'available_variations' => $available_variations,
                                    'selected_attributes' => $selected_attributes,
                                    'attribute_keys' => $attribute_keys,
                                    '_product' => $_product
                                 )); 
                            }  ?>  
                            </div> 
                        </div>    
                        <div style="padding-top: 15px;">
                            <button class="btn btn-primary shadow  nbdesigner_upload" ng-click="changeVariation()">{{(langs['CHOOSE']) ? langs['CHOOSE'] : "Choose"}}</button>
                            <button class="btn btn-primary shadow  nbdesigner_upload" ng-click="cancelChangeVariation()">{{(langs['CANCEL']) ? langs['CANCEL'] : "Cancel"}}</button>
                        </div>                           
                    </div>
                </div>    
            </div>
        </div>
    </div>
</div>