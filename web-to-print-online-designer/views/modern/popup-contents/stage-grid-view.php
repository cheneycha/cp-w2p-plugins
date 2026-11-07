<div class="nbd-popup white-popup popup-nbd-stage-grid-view" data-animate="scale">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <div class="overlay-main">
            <div class="loaded">
                <svg class="circular" viewBox="25 25 50 50">
                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                </svg>
            </div>
        </div>
        <i class="icon-nbd icon-nbd-clear close-popup" ng-click="settings.gridViewMode = !settings.gridViewMode;"></i>
        <div class="head">
            <h2><?php esc_html_e('Grid View','web-to-print-online-designer'); ?></h2>
        </div>
        <div class="body">
            <div class="main-body">
                <div class="tab-scroll" >
                    <stage-cell ng-repeat="stage in stages">
                        <div title="<?php esc_html_e('Add','web-to-print-online-designer'); ?>" class="stage-space" stage-space data-position="{{$index}}" ng-click="maybeAddStage($index)" ng-class="($index > 0 && settings.dynamicStage) ? 'addable' : 'not-addable'">
                            <span><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><rect fill="#404762" height="15" width="10" x="7" y="4"/><rect fill="#404762" height="11" width="2" x="3" y="6"/><rect fill="#404762" height="11" width="2" x="19" y="6"/></svg></span>
                        </div>
                        <div class="stage-cell-wrap" ng-style="{'animation': settings.gridViewMode ? 'fadeInLeft ' + ($index > 5 ? 1.5 : $index * 0.3) + 's ease' : 'none', '-webkit-animation': settings.gridViewMode ? 'fadeInLeft ' + ($index > 5 ? 1.5 : $index * 0.3) + 's ease' : 'none'}">
                            <div class="stage-cell-inner" stage-cell-inner draggable="true" data-position="{{$index}}">
                                <div class="stage-cell-background" 
                                    ng-style="{
                                        'background-color': stage.config.bgType == 'image' ? '#fff' : (( stage.config.bgType == 'color' && ( ( stage.config.show_overlay == '1' && stage.config.img_overlay != '' ) || !areaDesignShapes[$index] ) )  ? stage.config.bgColor : 'transparent'),
                                        'top': settings.product_data.product[$index].img_src_top * 150 / 500 + 'px',
                                        'left': settings.product_data.product[$index].img_src_left * 150 / 500 + 'px',
                                        'width': settings.product_data.product[$index].img_src_width * 150 / 500 + 'px',
                                        'height': settings.product_data.product[$index].img_src_height * 150 / 500 + 'px'
                                    }">
                                    <img ng-if="stage.config.bgType == 'image'" ng-src='{{stage.config.bgImage}}'/>
                                </div>
                                <div class="stage-cell-design"
                                    ng-style="{
                                        'top': settings.product_data.product[$index].area_design_top * 150 / 500 + 'px',
                                        'left': settings.product_data.product[$index].area_design_left * 150 / 500 + 'px',
                                        'width': settings.product_data.product[$index].area_design_width * 150 / 500 + 'px',
                                        'height': settings.product_data.product[$index].area_design_height * 150 / 500 + 'px'
                                    }"
                                >
                                    <img ng-if="!!stage.design" ng-src="{{stage.design}}" />
                                </div>
                                <div class="stage-cell-overlay"
                                    ng-style="{
                                        'top': settings.product_data.product[$index].img_src_top * 150 / 500 + 'px',
                                        'left': settings.product_data.product[$index].img_src_left * 150 / 500 + 'px',
                                        'width': settings.product_data.product[$index].img_src_width * 150 / 500 + 'px',
                                        'height': settings.product_data.product[$index].img_src_height * 150 / 500 + 'px'
                                    }">
                                    <img ng-if="stage.config.show_overlay == '1'" ng-src='{{stage.config.img_overlay}}'/>
                                </div>
                            </div>
                            <div class="stage-cell-actions" ng-if="settings.dynamicStage">
                                <i class="icon-nbd icon-nbd-24 stage-cell-action" ng-click="duplicateStage($index)" title="<?php esc_html_e('Duplicate Stage','web-to-print-online-designer'); ?>" >
                                    <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path fill="#888" d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm-1 4l6 6v10c0 1.1-.9 2-2 2H7.99C6.89 23 6 22.1 6 21l.01-14c0-1.1.89-2 1.99-2h7zm-1 7h5.5L14 6.5V12z"/></svg>
                                </i>
                                <span class="stage-cell-index">{{$index + 1}}</span>
                                <i class="icon-nbd icon-nbd-delete stage-cell-action" title="<?php esc_html_e('Delete Stage','web-to-print-online-designer'); ?>" ng-click="confirmDeleteStage($index)"></i>
                            </div>
                        </div>
                    </stage-cell>
                    <div class="stage-space" stage-space data-position="{{stages.length}}" >
                        <span><svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><rect fill="#404762" height="15" width="10" x="7" y="4"/><rect fill="#404762" height="11" width="2" x="3" y="6"/><rect fill="#404762" height="11" width="2" x="19" y="6"/></svg></span>
                    </div>
                    <div title="<?php esc_html_e('Add More','web-to-print-online-designer'); ?>" class="stage-cell-wrap add-more-stage" ng-show="settings.dynamicStage" ng-click="_addStage( stages.length - 1 )">
                        <i class="icon-nbd icon-nbd-add-black"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>