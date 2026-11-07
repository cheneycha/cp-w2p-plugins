<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<h1><?php esc_html_e('Fonts', 'web-to-print-online-designer'); ?></h1>
<h2><?php esc_html_e('Custom Fonts', 'web-to-print-online-designer'); ?></h2>
<?php echo $notice; ?>
<style>
    .showbox{position:absolute;top:0;bottom:0;left:0;right:0;z-index: 999;background: #fdfdfd;}.loader{position:relative;margin:-50px auto 0 -50px;width:100px;top:50%;left:50%}.loader:before{content:'';display:block;padding-top:100%}.circular{-webkit-animation:rotate 2s linear infinite;-moz-animation:rotate 2s linear infinite;-ms-animation:rotate 2s linear infinite;animation:rotate 2s linear infinite;height:100%;-webkit-transform-origin:center center;transform-origin:center center;width:100%;position:absolute;top:0;bottom:0;left:0;right:0;margin:auto}.path{stroke-dasharray:1,200;stroke-dashoffset:0;-webkit-animation:dash 1.5s ease-in-out infinite,color 6s ease-in-out infinite;-moz-animation:dash 1.5s ease-in-out infinite,color 6s ease-in-out infinite;-ms-animation:dash 1.5s ease-in-out infinite,color 6s ease-in-out infinite;animation:dash 1.5s ease-in-out infinite,color 6s ease-in-out infinite;stroke-linecap:round}@-webkit-keyframes rotate{100%{-webkit-transform:rotate(360deg);-moz-transform:rotate(360deg);-ms-transform:rotate(360deg);transform:rotate(360deg)}}@keyframes rotate{100%{-webkit-transform:rotate(360deg);-moz-transform:rotate(360deg);-ms-transform:rotate(360deg);transform:rotate(360deg)}}@-webkit-keyframes dash{0%{stroke-dasharray:1,200;stroke-dashoffset:0}50%{stroke-dasharray:89,200;stroke-dashoffset:-35px}100%{stroke-dasharray:89,200;stroke-dashoffset:-124px}}@keyframes dash{0%{stroke-dasharray:1,200;stroke-dashoffset:0}50%{stroke-dasharray:89,200;stroke-dashoffset:-35px}100%{stroke-dasharray:89,200;stroke-dashoffset:-124px}}@-webkit-keyframes color{0%,100%{stroke:#d62d20}40%{stroke:#0057e7}66%{stroke:#008744}80%,90%{stroke:#ffa700}}@keyframes color{0%,100%{stroke:#d62d20}40%{stroke:#0057e7}66%{stroke:#008744}80%,90%{stroke:#ffa700}}     
</style>
<div class="wrap nbdesigner-container">
    <div class="nbdesigner-content-full">
        <form name="post" action="" method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="nbdesigner-content-left postbox">
                <h3><?php  esc_html_e('Add font', 'web-to-print-online-designer'); ?></h3>
                <div class="inside nbdesigner_font_info">
                    <?php wp_nonce_field($this->plugin_id, $this->plugin_id.'_hidden'); ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row" class="titledesc"><label for="font_name"><?php  esc_html_e("Font name", 'web-to-print-online-designer'); ?></label></th>
                            <td class="forminp-text">
                                <input id="font_name" class="nbdesigner_font_name" type="text" name="nbdesigner_font_name" placeholder="Henny Penny" value="<?php $name = isset($font_data->name) ?  $font_data->name : ''; echo( $name ); ?>" />
                                <?php if( !isset($font_data->name) ): ?>
                                <div class="nbd-admin-font-tip"><?php esc_html_e('Open font file to get the font name, keep the name as in the font file and do not change it ( very important ), Ex:', 'web-to-print-online-designer'); ?><br />
                                    <img class="nbd-admin-sample-font-name-img" src="<?php echo NBDESIGNER_PLUGIN_URL . 'assets/images/view_font_name.png'; ?>" />
                                </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc"><label for="display_name"><?php  esc_html_e("Display name", 'web-to-print-online-designer'); ?></label></th>
                            <td class="forminp-text">
                                <input id="display_name" class="nbdesigner_font_name" type="text" name="display_name" value="<?php $display_name = isset($font_data->display_name) ?  $font_data->display_name : ''; echo( $display_name ); ?>" />
                                <p class="nbd-admin-font-tip"><?php esc_html_e('The name will show on design editor.', 'web-to-print-online-designer'); ?></p>
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc"><?php  esc_html_e("Font files", 'web-to-print-online-designer'); ?> </th>
                            <td class="forminp-text">
                                <?php 
                                    $font_data_file = array();
                                    $current_subset = 'all';
                                    $subsets        = nbd_font_subsets();
                                    $preview_text   = 'Abc Xyz';
                                    if(isset($font_data)){
                                        $current_subset     = $font_data->subset;
                                        $font_data_file     = (array)$font_data->file; 
                                        $preview_text       = $subsets[$current_subset]['preview_text'] != '' ? $subsets[$current_subset]['preview_text'] : $preview_text;
                                    }
                                ?>
                                <div class="nbd-font-file-wrap">
                                    <p><label for="regular_font"><?php  esc_html_e("Regular", 'web-to-print-online-designer'); ?></label></p>
                                    <input id="regular_font" type="file" name="font[]" value="" accept=".ttf" /><br />
                                    <?php if( isset($font_data_file['r']) ):
                                        $url = NBDESIGNER_FONT_URL . $font_data_file['r'];
                                    ?>
                                    <style type='text/css'>
                                        @font-face {font-family: <?php echo( $font_data->alias ); ?>;src: local('☺'), url('<?php echo esc_url( $url ); ?>')}
                                    </style>
                                    <p style="font-family: <?php echo "'".$font_data->alias."', sans-serif"; ?>;font-size: 30px;">
                                        <a download href="<?php echo esc_url( $url ); ?>"><?php echo( $preview_text ); ?></a>
                                    </p>
                                    <?php endif; ?>
                                </div>
                                <div class="nbd-font-file-wrap">
                                    <p><label for="italic_font" style="font-style: italic;"><?php  esc_html_e("Italic", 'web-to-print-online-designer'); ?></label></p>
                                    <input id="italic_font" type="file" name="font[]" value="" accept=".ttf" /><br />
                                    <?php if( isset($font_data_file['i']) ):
                                        $url = NBDESIGNER_FONT_URL . $font_data_file['i'];
                                    ?>
                                    <style type='text/css'>
                                        @font-face {font-family: <?php echo( $font_data->alias ); ?>;src: local('☺'), url('<?php echo esc_url( $url ); ?>'); font-style: italic}
                                    </style>
                                    <p style="font-family: <?php echo "'".$font_data->alias."', sans-serif"; ?>;font-size: 30px;font-style: italic">
                                        <a download href="<?php echo esc_url( $url ); ?>"><?php echo( $preview_text ); ?></a>
                                    </p>
                                    <?php endif; ?>
                                </div> 
                                <div class="nbd-font-file-wrap">
                                    <p><label for="bold_font" style="font-weight: bold;"><?php  esc_html_e("Bold", 'web-to-print-online-designer'); ?></label></p>
                                    <input id="bold_font" type="file" name="font[]" value="" accept=".ttf" /><br />
                                    <?php if( isset($font_data_file['b']) ):
                                        $url = NBDESIGNER_FONT_URL . $font_data_file['b'];
                                    ?>
                                    <style type='text/css'>
                                        @font-face {font-family: <?php echo( $font_data->alias ); ?>;src: local('☺'), url('<?php echo esc_url( $url ); ?>'); font-weight: bold}
                                    </style>
                                    <p style="font-family: <?php echo "'".$font_data->alias."', sans-serif"; ?>;font-size: 30px;font-weight: bold">
                                        <a download href="<?php echo esc_url( $url ); ?>"><?php echo( $preview_text ); ?></a>
                                    </p>
                                    <?php endif; ?>
                                </div>
                                <div class="nbd-font-file-wrap">
                                    <p><label for="bold_italic_font" style="font-weight: bold;font-style: italic;"><?php  esc_html_e("Bold italic", 'web-to-print-online-designer'); ?></label></p>
                                    <input id="bold_italic_font" type="file" name="font[]" value="" accept=".ttf" /><br />
                                    <?php if( isset($font_data_file['bi']) ):
                                        $url = NBDESIGNER_FONT_URL . $font_data_file['bi'];
                                    ?>
                                    <style type='text/css'>
                                        @font-face {font-family: <?php echo( $font_data->alias ); ?>;src: local('☺'), url('<?php echo esc_url( $url ); ?>'); font-style: italic; font-weight: bold}
                                    </style>
                                    <p style="font-family: <?php echo "'".$font_data->alias."', sans-serif"; ?>;font-size: 30px;font-style: italic; font-weight: bold">
                                        <a download href="<?php echo esc_url( $url ); ?>"><?php echo( $preview_text ); ?></a>
                                    </p>
                                    <?php endif; ?>
                                </div>
                                <div class="nbd-admin-font-tip"><?php esc_html_e('Only allow extensions: ttf', 'web-to-print-online-designer'); ?><br />
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc"><label for="subset"><?php  esc_html_e("Font Language Subsets", 'web-to-print-online-designer'); ?></label></th>
                            <td class="forminp-text">
                                <select name="subset" id="subset">
                                <?php
                                    foreach( $subsets as $key => $subset ):
                                ?>
                                    <option value="<?php echo( $key ); ?>" <?php selected( $key, $current_subset ); ?>><?php echo( $subset['name'] ); ?></option>
                                <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" name="nbdesigner_font_id" value="<?php echo( $font_id ); ?>"/>
                    <p class="submit">
                        <input type="submit" name="Submit" class="button-primary" value="<?php esc_html_e('Save', 'web-to-print-online-designer'); ?>" />
                        <a href="?page=nbdesigner_manager_fonts" class="button-primary" style="<?php $style = (isset($_GET['id'])) ? '' : 'display:none;';echo( $style ); ?>"><?php esc_html_e('Add New', 'web-to-print-online-designer'); ?></a>
                    </p>
                </div>
            </div>
            <div class="nbdesigner-content-side">
                <div class="postbox nbd-admin-padding-bottom-5">
                    <h3><?php esc_html_e('Categories', 'web-to-print-online-designer'); ?><img src="<?php echo NBDESIGNER_PLUGIN_URL . 'assets/images/loading.gif'; ?>" class="nbdesigner_editcat_loading nbdesigner_loaded nbd-admin-margin-left-15" /></h3>
                    <div class="inside">
                        <ul id="nbdesigner_list_cats">
                        <?php if(is_array($cat) && (sizeof($cat) > 0)): ?>
                            <?php foreach($cat as $val): ?>
                                <li id="nbdesigner_cat_font_<?php echo( $val->id ); ?>" class="nbdesigner_action_delete_cf">
                                    <label>
                                        <input value="<?php echo( $val->id ); ?>" type="checkbox" name="nbdesigner_font_cat[]" <?php if($update && (sizeof($cats) > 0 )) if(in_array($val->id, $cats)) echo "checked";  ?> />
                                    </label>
                                    <span class="nbdesigner-right nbdesigner-delete-item dashicons dashicons-no-alt" onclick="NBDESIGNADMIN.delete_cat_font(this)"></span>
                                    <span class="dashicons dashicons-edit nbdesigner-right nbdesigner-delete-item" onclick="NBDESIGNADMIN.edit_cat_font(this)"></span>
                                    <a href="<?php echo add_query_arg(array('cat_id' => $val->id), admin_url('admin.php?page=nbdesigner_manager_fonts')) ?>" class="nbdesigner-cat-link"><?php echo( $val->name ); ?></a>
                                    <input value="<?php echo( $val->name ); ?>" class="nbdesigner-editcat-name" type="text"/>
                                    <span class="dashicons dashicons-yes nbdesigner-delete-item nbdesigner-editcat-name" onclick="NBDESIGNADMIN.save_cat_font(this)"></span>
                                    <span class="dashicons dashicons-no nbdesigner-delete-item nbdesigner-editcat-name" onclick="NBDESIGNADMIN.remove_action_cat_font(this)"></span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?> 
                            <li><?php esc_html_e('You don\'t have any category.', 'web-to-print-online-designer'); ?></li>
                        <?php endif; ?>
                        </ul>
                        <input type="hidden" id="nbdesigner_current_font_cat_id" value="<?php echo( $current_font_cat_id ); ?>"/>
                        <p><a id="nbdesigner_add_font_cat"><?php esc_html_e('+ Add new font category', 'web-to-print-online-designer'); ?></a></p>
                        <div id="nbdesigner_font_newcat" class="category-add"></div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="clear"></div>
    <div class="postbox" id="nbd-list-fonts">
        <h3><?php  esc_html_e('List fonts ', 'web-to-print-online-designer'); ?>
            <?php if(is_array($cat) && (sizeof($cat) > 0)): ?>
            <select onchange="if (this.value) window.location.href=this.value+'#nbd-list-fonts'">
                <option value="<?php echo admin_url('admin.php?page=nbdesigner_manager_fonts'); ?>"><?php esc_html_e('Select a category', 'web-to-print-online-designer'); ?></option>
                <?php foreach($cat as $cat_index => $val): ?>
                <option value="<?php echo add_query_arg(array('cat_id' => $val->id), admin_url('admin.php?page=nbdesigner_manager_fonts')) ?>" <?php selected( $cat_index, $current_cat_id ); ?>><?php echo( $val->name ); ?></option>
                <?php endforeach; ?>
            </select>
            <?php  endif; ?>
            <!-- <a class="nbdesigner-right" href="<?php echo admin_url('admin.php?page=nbdesigner_manager_fonts'); ?>"><?php esc_html_e('All fonts', 'web-to-print-online-designer'); ?></a> -->
        </h3>
        <div class="nbdesigner-list-fonts inside">
            <div class="nbdesigner-list-fonts-container">
                <?php if(is_array($list) && (sizeof($list) > 0)): ?>
                    <?php foreach($list as $val): ?>
                        <span class="nbdesigner_google_link ">
                            <a href="?page=nbdesigner_manager_fonts&id=<?php echo( $val->id ); ?><?php if(isset($current_cat)) echo '&cat_id='.$current_cat; ?>">
                                <span><?php echo( $val->name ); ?></span>
                                <?php if( isset( $val->display_name ) && $val->display_name != $val->name ): ?>
                                    - <?php echo( $val->display_name ); ?>
                                <?php  endif; ?>
                            </a>
                            <span class="nbdesigner_action_delete_cfont" data-index="<?php echo( $val->id ); ?>" onclick="NBDESIGNADMIN.delete_font('custom',this)">&times;</span>
                        </span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php esc_html_e('You don\'t have any custom font.', 'web-to-print-online-designer');?>
                <?php  endif; ?>
            </div>
        </div>
	</div>
</div>
<h2><?php esc_html_e('Google Fonts', 'web-to-print-online-designer'); ?></h2>
<div class="wrap nbdesigner-container">
    <div class="postbox" ng-app='font-app' ng-controller="fontCtrl" ng-cloak>
        <div class="inside">
            <div class="showbox" style="display: none;">
                <div class="loader">
                    <svg class="circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                    </svg>
                </div>
            </div>
            <div class="gg-font-option">
                <input type="text" ng-model="filterFont.name" placeholder="Font name" ng-change="resetCurentPage()">
                <select ng-model="filterFont.category" ng-change="resetCurentPage()">
                    <option value=""><?php esc_html_e('All Categories', 'web-to-print-online-designer'); ?></option>
                    <option value="serif">Serif</option>
                    <option value="sans-serif">Sans Serif</option>
                    <option value="display">Display</option>
                    <option value="handwriting">Handwriting</option>
                    <option value="monospace">Monospace</option>
                </select>
                <select ng-model="filterFont.subset" ng-change="resetCurentPage()">
                    <option value=""><?php esc_html_e('All subsets', 'web-to-print-online-designer'); ?></option>
                    <?php
                        foreach( $subsets as $key => $subset ):
                    ?>
                        <option value="<?php echo( $key ); ?>" <?php selected( $key, $current_subset ); ?>><?php echo( $subset['name'] ); ?></option>
                    <?php endforeach; ?>
                </select>
                <a class="button button-primary" ng-click="updateGoogleFont($event)"><?php esc_html_e('Update', 'web-to-print-online-designer');?></a>
            </div>
            <div class="gg-font-preview-wrap">
                <div class="nbd-pagesize-wrap">
                    <b><?php esc_html_e('Total', 'web-to-print-online-designer');?> {{fonts.length}} <?php esc_html_e('fonts', 'web-to-print-online-designer');?></b>
                    <a class="button" ng-click="selectAll()"><?php esc_html_e('Select All', 'web-to-print-online-designer');?></a>
                    <a class="button" ng-click="unselectAll()"><?php esc_html_e('Unselect All', 'web-to-print-online-designer');?></a>
                    <div style="display: inline-block; float: right;">
                        <label for='nbd-selected'><?php esc_html_e('Display ', 'web-to-print-online-designer');?></label>
                        <select id='nbd-selected' ng-model="filterFont.select" ng-change="resetCurentPage()">
                            <option value=""><?php esc_html_e('All', 'web-to-print-online-designer');?></option>
                            <option value="selected"><?php esc_html_e('Selected', 'web-to-print-online-designer');?></option>
                            <option value="unselected"><?php esc_html_e('Unselected', 'web-to-print-online-designer');?></option>
                        </select>
                        <label for='nbd-page-size'><?php esc_html_e('Display ', 'web-to-print-online-designer');?></label>
                        <select id='nbd-page-size' ng-model="filterFont.pageSize" ng-change="resetCurentPage()">
                            <option ng-value="5">4</option>
                            <option ng-value="10">12</option>
                            <option ng-value="20">20</option>
                            <option ng-value="30">36</option>
                            <option ng-value="50">56</option>
                        </select>
                    </div>
                </div>
                <p><small><?php esc_html_e('Click check mark to select/unselect font', 'web-to-print-online-designer');?></small></p>
                <p class="nbd-admin-font-warning"><?php esc_html_e('Please remove unused fonts to make the design editor loads faster', 'web-to-print-online-designer');?></p>
                <div class="gg-font-preview-wrap-inner">
                    <div class="gg-font-preview" ng-repeat="font in fonts | startFrom:filterFont.currentPage*filterFont.pageSize | limitTo:filterFont.pageSize">
                        <div class="gg-font-preview-inner-wrap" style="font-family: '{{font.family}}',-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif">
                            <div class="gg-font-preview-inner">
                                <p class="gg-font-name">{{font.family}}</p>
                                <p font-on-load data-preview="fSubsets[font.subsets[0]]['preview_text']" data-font="font.family" ><span class="font-preview" style="display: none;" contenteditable="true">{{fSubsets[font.subsets[0]]['preview_text']}}</span></p>
                                <span title="{{font.selected ? '<?php esc_html_e('Unselect', 'web-to-print-online-designer');?>' : '<?php esc_html_e('Select', 'web-to-print-online-designer');?>'}}" ng-class="font.selected ? '' : 'uncheck'" class="action dashicons dashicons-yes disable" ng-click="selectFont( font, $event )"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="gg-font-pagination" font-pagination data-filter-font="filterFont" data-total="fonts.length"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    <?php 
        $path = NBDESIGNER_DATA_DIR. '/googlefonts.json';
        $selected_fonts = file_get_contents($path);
        if( $selected_fonts == '' ) $selected_fonts = '[]';
    ?>
    var selected_fonts= <?php echo $selected_fonts; ?>;
    var ggFonts = <?php echo file_get_contents(NBDESIGNER_PLUGIN_DIR. '/data/google-fonts-ttf.json'); ?>;
    jQuery(document).ready(function($){
        var google_font  = <?php echo $list_all_google_font; ?>;
        $( "#nbdesigner_google_font_seach" ).autocomplete({
            source: google_font,
            select: function(event, ui){
                $('.nbdesigner_google_preview').show();
                $('#nbdesigner_google_preview').html('');
                $('#nbdesigner_head').remove();
                var _name = ui.item.value;
                name1 = _name.replace(' ', '+');
                var head    = '<link id="nbdesigner_head" href="https://fonts.googleapis.com/css?family='+ name1 +'" rel="stylesheet" type="text/css">';
                var html    = '<span style="font-family: \''+ _name +'\', sans-serif ;font-size: 30px;">Abc Xyz</span>';
                $('head').append(head);
                $('#nbdesigner_google_preview').append(html);
            }
        });
    });
    var fontApp = angular.module('font-app', []);
    fontApp.controller('fontCtrl', ['$scope', 'fontObject', 'filterFontFilter', function($scope, fontObject, filterFontFilter){
        $scope.init = function(){
            angular.forEach(selected_fonts, function(_font, k) {    
                $scope.selectedFonts.push({name: _font.name});
            }); 
            $scope.updateSelectedFont();
        };
        $scope.selectedFonts = [];
        $scope.allFonts = ggFonts.items
        $scope.fSubsets = <?php echo json_encode(nbd_font_subsets()); ?>;
        $scope.filterFont = {};
        $scope.filterFont.currentPage = 0;
        $scope.filterFont.pageSize = 20;
        $scope.fonts = filterFontFilter($scope.allFonts, $scope.filterFont);
        $scope.$watchCollection('filterFont', function(newVal, oldVal){
            $scope.fonts = filterFontFilter($scope.allFonts, $scope.filterFont);
        }, true);   
        $scope.$watchCollection('selectedFonts', function(newVal, oldVal){
            $scope.fonts = filterFontFilter($scope.allFonts, $scope.filterFont);
        }, true); 
        $scope.resetCurentPage = function(){
            $scope.filterFont.currentPage = 0;
        };
        $scope.updateSelectedFont = function(){
            angular.forEach($scope.allFonts, function(font, key) {   
                $scope.allFonts[key].selected = false;
                angular.forEach($scope.selectedFonts, function(_font, k) {    
                    if( font.family == _font.name ) $scope.allFonts[key].selected = true;
                });
            });
        };
        $scope.selectAll = function(){
            $scope.selectedFonts = [];
            angular.forEach($scope.allFonts, function(font, key) {     
                $scope.selectedFonts.push({name: font.family});
            });
            $scope.updateSelectedFont();
        };
        $scope.unselectAll = function(){
            $scope.selectedFonts = [];
            $scope.updateSelectedFont();
        };
        $scope.selectFont = function( font, $event ){
            if( !font.selected ){
                $scope.selectedFonts.push({name: font.family});
            }else{
                var index = 0;
                angular.forEach($scope.selectedFonts, function(_font, k) {    
                    if( font.family == _font.name ) index = k;
                });
                $scope.selectedFonts.splice(index, 1);
            }
            $scope.updateSelectedFont();
        };
        $scope.updateGoogleFont = function( $event ){
            jQuery.ajax({
                url: admin_nbds.url,
                method: "POST",
                data: {'action': 'nbdesigner_add_google_font', 'fonts': JSON.stringify($scope.selectedFonts), 'nonce': admin_nbds.nonce},
                beforeSend: function () {
                    jQuery('.showbox').show();
                },
                complete: function () {
                    jQuery('.showbox').hide();
                }
            }).done(function (data) {
                data = JSON.parse(data);
                swal(admin_nbds.nbds_lang.complete, data.mes, "success");
            });
        }; 
        $scope.init();
    }]);
    fontApp.factory('fontObject', function($http) { 
        return {
            fn: function(callback) {
                $http({
                    method: "GET",
                    url: font_path
                }).then(function (response){
                    callback(response.data.items)
                },function (error){

                });
            }
        }
    });
    fontApp.filter('pageRange', function() {
        return function(input, total) {
            total = parseInt(total);
            for (var i=0; i<total; i++)
                input.push(i);
            return input;
        };
    });   
    fontApp.directive('stringToNumber', function() {
        return {
            require: 'ngModel',
            link: function(scope, element, attrs, ngModel) {
                ngModel.$parsers.push(function(value) {
                    return '' + value;
                });
                ngModel.$formatters.push(function(value) {
                    return parseFloat(value);
                });
            }
        }
    });
    fontApp.directive('fontPagination', function(){
        return {
            restrict: 'A',
            scope: {
                filterFont: '=filterFont',
                total: '=total'
            },
            template: '{{filterFont.totalPages}}<span ng-if="filterFont.currentPage > 0" ng-click="changePage(0)"><?php esc_html_e('First', 'web-to-print-online-designer');?></span><span ng-if="filterFont.currentPage > 1" ng-click="changePage(filterFont.currentPage-1)">{{filterFont.currentPage}}</span><span ng-click="changePage(filterFont.currentPage)" class="active">{{filterFont.currentPage + 1}}</span><span ng-if="filterFont.currentPage < (totalPages - 2)" ng-click="changePage(filterFont.currentPage+1)">{{filterFont.currentPage + 2}}</span><span ng-if="filterFont.currentPage < (totalPages - 1)" ng-click="changePage(totalPages-1)"><?php esc_html_e('Last', 'web-to-print-online-designer');?></span>',
            link: {
                
            },
            controller: function ($scope) {
                $scope.pages = 1;
                $scope.$watch('total', function() {
                    $scope.totalPages = Math.ceil( $scope.total / $scope.filterFont.pageSize);
                });
                $scope.$watchCollection('filterFont', function() {
                    $scope.totalPages = Math.ceil( $scope.total / $scope.filterFont.pageSize);
                });    
                $scope.changePage = function( $index ){
                    $scope.filterFont.currentPage = $index;
                }
            }
        }
    });
    fontApp.filter('startFrom', function() {
        return function(input, start) {
            start = +start;
            return input.slice(start);
        }
    });
    fontApp.filter("filterFont", function() {
        return function(fonts, filterFont) {
            var arrFont = [];
            angular.forEach(fonts, function(font, key) {
                if( !angular.isDefined(filterFont) ){
                    arrFont.push(font);
                }else{
                    var check = [];
                    if( !!filterFont.subset ){
                        check['subset'] = false;
                        angular.forEach(font.subsets, function(subset, key) {    
                            if( subset == filterFont.subset ) check['subset'] = true;
                        })
                    }else{
                        check['subset'] = true;
                    };     
                    if( !!filterFont.category ){ 
                        check['category'] = font.category == filterFont.category ? true : false;
                    }else{
                        check['category'] = true;
                    }; 
                    if( !!filterFont.name ){ 
                       check['name'] = font.family.toLowerCase().indexOf(filterFont.name.toLowerCase()) >= 0 ? true : false;
                    }else{
                        check['name'] = true;
                    };
                    check['select'] = true;
                    if( !!filterFont.select ){
                       check['select'] = filterFont.select == 'selected' ? ( font.selected ? true : false ) : ( font.selected ? false : true );
                    };
                    if( check['subset'] && check['category'] && check['name'] && check['select'] ) arrFont.push(font);
                }
            });
            return arrFont
        }
    });
    fontApp.directive("fontOnLoad", ['$interval', function($interval) {
        return {
            restrict: "A",
            scope: {
                font: '=',
                preview: '='
            },
            link: function(scope, element) {
                var font_id = scope.font.replace(/\s/gi, '').toLowerCase();
                if( !jQuery('#' + font_id).length ){
                    jQuery('head').append('<link id="' + font_id + '" href="https://fonts.googleapis.com/css?family='+ scope.font.replace(/\s/gi, '+') +'" rel="stylesheet" type="text/css">');
                }
                var font = new FontFaceObserver(scope.font);             
                font.load(scope.preview, 1E4).then(function () {
                    element.find(".font-loading").remove();
                    element.find(".font-preview").show();
                    element.parent('.gg-font-preview-inner').find('span.action ').removeClass('disable');
                }, function () {
                    console.log('Font '+scope.font+' is not available');
                });
                element.append('<span class="font-loading"><?php esc_html_e('Loading...', 'web-to-print-online-designer');?></span>')
            }
        }
    }]);
</script>