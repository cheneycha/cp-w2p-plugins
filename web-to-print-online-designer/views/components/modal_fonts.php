<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div class="modal fade nbdesigner_modal" id="dg-fonts">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button style="margin-top: 0;" type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>	
                <div class="nbdesigner_art_modal_header">
                    <span>{{(langs['FONTS']) ? langs['FONTS'] : "Fonts"}}</span>
                    <input type="search" class="form-control hover-shadow" placeholder="{{(langs['SEARCH_FONT']) ? langs['SEARCH_FONT'] : 'Search Font'}}" ng-model="fontName"/>
                    <div class="btn-group">
                        <button class="btn btn-primary dropdown-toggle shadow hover-shadow" type="button" data-toggle="dropdown">{{currentCatFontName}}&nbsp;<span class="caret"></span></button>
                        <ul class="dropdown-menu dropup  shadow hover-shadow">
                            <li ><a ng-click="loadGoogleFont()">{{(langs['GOOGLE_FONT']) ? langs['GOOGLE_FONT'] : "Google Font"}}</a></li>
                            <li role="separator" class="divider"></li>
                            <li ng-repeat="cat in fontCat">
                                <a ng-click="changeFontCat(cat)">{{cat.name}}</a>
                            </li>                            
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-body">
                <div id="nbdesigner_font_container">
                    <div class="gg-font-preview-wrap-inner">
                        <div ng-click="changeFont(font)" class="gg-font-preview disable" ng-repeat="font in AllFonts | filterCat : curentCatFont | filter : fontName| limitTo : fontPageSize">
                            <div class="gg-font-preview-inner-wrap">
                                <div class="gg-font-preview-inner">
                                    <p class="gg-font-name">{{font.name}}</p>
                                    <p font-on-loading data-loading="Loading..." data-preview="subsets[font.subset]['preview_text']" data-font="font" ><span style="font-family: '{{font.alias}}',-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;display: none; font-size: 20px;" class="font-preview" contenteditable="false">{{subsets[font.subset]['preview_text']}}</span></p>
                                </div>
                            </div>    
                        </div>  
                    </div>      
                </div>
                <div>
                    <button ng-show="(countFont > 10) && (countFont > fontPageSize)" style="margin-right: 15px; margin-top: 10px;" id="font-load-more" type="button" class="btn btn-primary shadow nbdesigner_upload" ng-click="changeFontPageSize(false)">{{(langs['MORE']) ? langs['MORE'] : "More"}}</button>
                    <img id="loading_font_upload" class="hidden" src="<?php echo NBDESIGNER_PLUGIN_URL .'assets/css/images/ajax-loader.gif'; ?>" />
                </div>
            </div>
        </div>
    </div>
</div>