<div class="toolbar-text" ng-show="stages[currentStage].states.isText">
    <ul class="nbd-main-menu menu-left">
        <li class="menu-item item-font-familly" ng-if="settings.nbdesigner_text_change_font == 1" ng-click="updateScrollBar('#toolbar-font-familly-dropdown')">
            <button class="toolbar-bottom">
                <span class="toolbar-label toolbar-label-font" ng-style="{'font-family': stages[currentStage].states.text.font.alias}">{{(stages[currentStage].states.text.font.display_name && stages[currentStage].states.text.font.display_name != '') ? stages[currentStage].states.text.font.display_name : stages[currentStage].states.text.font.name}}</span>
                <i class="icon-nbd icon-nbd-dropdown-arrows"></i>
            </button>
            <div class="sub-menu" data-pos="left">
                <div class="toolbar-font-search">
                    <input type="search" name="font-search" ng-model="resource.font.filter.search" placeholder="<?php esc_html_e('Search in','web-to-print-online-designer'); ?> {{resource.font.data.length}} <?php esc_html_e('fonts','web-to-print-online-designer'); ?>"/>
                    <i ng-show="resource.font.filter.search.length > 0" ng-click="resource.font.filter.search = ''" class="icon-nbd icon-nbd-clear"></i>
                </div>
                <div id="toolbar-font-familly-dropdown" nbd-scroll="scrollLoadMore(container, type)" data-container="#toolbar-font-familly-dropdown" data-type="font" data-offset="40">
                    <div class="group-font" ng-show="stages[currentStage].states.usedFonts.length > 0">
                        <div class="toolbar-menu-header">
                            <div class="toolbar-header-line"></div>
                            <div class="toolbar-separator"><?php esc_html_e('Document Fonts','web-to-print-online-designer'); ?></div>
                            <div class="toolbar-header-line"></div>
                        </div>
                        <ul>
                            <li ng-click="setTextAttribute('fontFamily', font.alias)" class="sub-menu-item" ng-repeat="font in stages[currentStage].states.usedFonts">
                                <span class="font-name-wrap" style="font-family: '{{font.alias}}',-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;"><span class="font-name">{{(font.display_name && font.display_name != '') ? font.display_name : font.name}}</span><span ng-if="['all', 'latin', 'latin-ext', 'vietnamese'].indexOf(font.subset) < 0"> {{settings.subsets[font.subset]['preview_text']}}</span></span>
                            </li>
                        </ul>
                    </div>
                    <div class="group-font">
                        <div class="toolbar-menu-header">
                            <div class="toolbar-header-line"></div>
                            <div class="toolbar-separator"><?php esc_html_e('All Fonts','web-to-print-online-designer'); ?></div>
                            <div class="toolbar-header-line"></div>
                        </div>
                        <ul>
                            <li class="sub-menu-item" ng-class="font.alias == stages[currentStage].states.text.fontFamily ? 'chosen' : ''" ng-click="setTextAttribute('fontFamily', font.alias)" ng-repeat="font in resource.font.filteredFonts" repeat-end="onEndRepeat('font')" data-font="font" font-on-load load-font-fail-action="loadFontFailAction(font)" data-preview="settings.subsets[font.subset]['preview_text']" >
                                <span class="font-name-wrap" style="font-family: '{{font.alias}}',-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen-Sans, Ubuntu, Cantarell, 'Helvetica Neue', sans-serif;"><span class="font-name">{{(font.display_name && font.display_name != '') ? font.display_name : font.name}}</span><span ng-if="['all', 'latin', 'latin-ext', 'vietnamese'].indexOf(font.subset) < 0"> {{settings.subsets[font.subset]['preview_text']}}</span></span>
                                <i ng-if="font.alias == stages[currentStage].states.text.fontFamily" class="icon-nbd icon-nbd-fomat-done font-selected"></i>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </li>
        <li class="menu-item item-font-size" ng-if="settings.nbdesigner_text_font_size == 1" ng-click="updateScrollBar('#toolbar-font-size-dropdown')">
            <div class="toolbar-bottom">
                <input class="toolbar-input" type="text" ng-keyup="$event.keyCode == 13 && setTextAttribute('fontSize', stages[currentStage].states.text.ptFontSize)" name="font-size" ng-model="stages[currentStage].states.text.ptFontSize"/>
                <span class="font-unit">pt</span><i class="icon-nbd icon-nbd-dropdown-arrows"></i>
                <div class="sub-menu" data-pos="left">
                    <div id="toolbar-font-size-dropdown">
                        <ul>
                            <li class="sub-menu-item" ng-click="setTextAttribute('fontSize', fontsize)" ng-class="stages[currentStage].states.text.ptFontSize == fontsize ? 'chosen' : ''" ng-repeat="fontsize in listFontSizeInPt">
                                <span>{{fontsize}}</span>
                                <i class="icon-nbd icon-nbd-fomat-done" ng-if="stages[currentStage].states.text.ptFontSize == fontsize"></i>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </li> 
        <li class="menu-item item-align item-edit-text">
            <i class="icon-nbd icon-nbd-text-fields nbd-tooltip-hover" title="<?php esc_html_e('Edit Text','web-to-print-online-designer'); ?>"></i>
            <div class="sub-menu sub-menu-box-curved" data-pos="center" >
                <div class="box-curved" >
                    <textarea ng-change="setTextAttribute('text', stages[currentStage].states.text.text)" ng-model="stages[currentStage].states.text.text" placeholder="<?php esc_html_e('Enter your text','web-to-print-online-designer'); ?>"></textarea>
                </div>
            </div>
        </li>
    </ul>
    <ul class="nbd-main-menu menu-center" ng-if="settings.nbdesigner_text_color == 1">

        <!-- custom charmingprint -->
        <li ng-show=" pt_color.key == 'pt_color1' " class="menu-item item-color-fill nbd-show-color-palette" ng-click="currentColor = pt_color.sg_color.color">
            <span ng-style="{'background':  pt_color.sg_color.color}" class="nbd-tooltip-hover color-fill nbd-color-picker-preview" title="<?php esc_html_e('Color','web-to-print-online-designer'); ?>" ></span>
        </li>
        <li ng-show=" pt_color.key != 'pt_color1' " class="menu-item item-color-fill nbd-show-color-palette" ng-click="currentColor = stages[currentStage].states.text.fill">
            <span ng-style="{'background': stages[currentStage].states.text.fill}" class="nbd-tooltip-hover color-fill nbd-color-picker-preview" title="<?php esc_html_e('Color','web-to-print-online-designer'); ?>" ></span>
        </li>
        <!-- end -->
        
    </ul>
    <ul class="nbd-main-menu menu-right">
        <li class="menu-item item-align" ng-if="settings.nbdesigner_text_align_left == 1 || settings.nbdesigner_text_align_center == 1 || settings.nbdesigner_text_align_right == 1">
            <i class="icon-nbd icon-nbd-format-align-center nbd-tooltip-hover" title="<?php esc_html_e('Text align','web-to-print-online-designer'); ?>"></i>
            <div class="sub-menu" data-pos="center">
                <ul>
                    <li ng-click="setTextAttribute('textAlign', 'left')" class="sub-menu-item"
                        ng-class="stages[currentStage].states.text.textAlign == 'left' ? 'selected' : ''"
                        ng-if="settings['nbdesigner_text_align_left'] == '1'"><i class="icon-nbd icon-nbd-format-align-left nbd-tooltip-hover" title="<?php esc_html_e('Left','web-to-print-online-designer'); ?>"></i></li>
                    <li ng-click="setTextAttribute('textAlign', 'center')" class="sub-menu-item" 
                        ng-class="stages[currentStage].states.text.textAlign == 'center' ? 'selected' : ''"
                        ng-if="settings['nbdesigner_text_align_center'] == '1'"><i class="icon-nbd icon-nbd-format-align-center nbd-tooltip-hover" title="<?php esc_html_e('Center','web-to-print-online-designer'); ?>"></i></li>
                    <li ng-click="setTextAttribute('textAlign', 'right')" class="sub-menu-item"
                        ng-class="stages[currentStage].states.text.textAlign == 'right' ? 'selected' : ''"
                        ng-if="settings['nbdesigner_text_align_right'] == '1'"><i class="icon-nbd icon-nbd-format-align-right nbd-tooltip-hover" title="<?php esc_html_e('Right','web-to-print-online-designer'); ?>"></i></li>
                    <li ng-click="setTextAttribute('textAlign', 'justify')" class="sub-menu-item"
                        ng-class="stages[currentStage].states.text.textAlign == 'justify' ? 'selected' : ''"
                        ng-if="settings['nbdesigner_text_align_center'] == '1'"><i class="icon-nbd icon-nbd-format-align-justify nbd-tooltip-hover" title="<?php esc_html_e('Justify','web-to-print-online-designer'); ?>"></i></li>
                </ul>
            </div>
        </li>
        <li ng-click="setTextAttribute('is_uppercase', stages[currentStage].states.text.is_uppercase ? false : true)"
            ng-if="settings.nbdesigner_text_case == 1"
            ng-class="stages[currentStage].states.text.is_uppercase ? 'selected' : ''" class="menu-item item-transform"><i class="icon-nbd icon-nbd-uppercase nbd-tooltip-hover" title="<?php esc_html_e('Uppercase','web-to-print-online-designer'); ?>"></i></li>
        <li ng-click="setTextAttribute('fontWeight', stages[currentStage].states.text.fontWeight == 'bold' ? 'normal' : 'bold')" 
            ng-class="{'selected': stages[currentStage].states.text.fontWeight == 'bold', 'nbd-disabled': !(stages[currentStage].states.text.font.file.b && ( stages[currentStage].states.text.fontStyle != 'italic' || ( stages[currentStage].states.text.fontStyle == 'italic' && stages[currentStage].states.text.font.file.bi ) ))}" class="menu-item item-text-bold"             
            ng-if="settings['nbdesigner_text_bold'] == '1'"><i class="icon-nbd icon-nbd-format-bold nbd-tooltip-hover" title="<?php esc_html_e('Bold','web-to-print-online-designer'); ?>"></i></li>
        <li ng-click="setTextAttribute('fontStyle', stages[currentStage].states.text.fontStyle == 'italic' ? 'normal' : 'italic')" 
            ng-class="{'selected': stages[currentStage].states.text.fontStyle == 'italic','nbd-disabled' : !(stages[currentStage].states.text.font.file.i && ( stages[currentStage].states.text.fontWeight != 'bold' || ( stages[currentStage].states.text.fontWeight == 'bold' && stages[currentStage].states.text.font.file.bi ) ))}" class="menu-item item-text-italic" 
            ng-if="settings['nbdesigner_text_italic'] == '1'"><i class="icon-nbd icon-nbd-format-italic nbd-tooltip-hover" 
            title="<?php esc_html_e('Italic','web-to-print-online-designer'); ?>"></i></li>
        <li class="menu-item menu-item-none"><i class="icon-nbd icon-nbd-format-underlined nbd-tooltip-hover" title="Underline"></i></li>
    </ul>
    <ul class="nbd-main-menu menu-right">
        <li class="menu-item item-spacing nbd-tooltip-hover" ng-if="settings.nbdesigner_text_spacing == 1 || settings.nbdesigner_text_line_height == 1" data-range="true" title="<?php esc_html_e('Line height and spacing','web-to-print-online-designer'); ?>">
            <i class="icon-nbd icon-nbd-line_spacing"></i>
            <div class="sub-menu" data-pos="center">
                <div class="main-ranges" >
                    <div class="range range-spacing" ng-if="settings.nbdesigner_text_spacing == 1">
                        <label><?php esc_html_e('Spacing','web-to-print-online-designer'); ?></label>
                        <div class="main-track">
                            <input class="slide-input" ng-change="setTextAttribute('charSpacing', stages[currentStage].states.text.charSpacing)" ng-model="stages[currentStage].states.text.charSpacing" type="range" step="1" min="0" max="1000">
                            <span class="range-track"></span>
                        </div>
                        <span class="value-display">{{stages[currentStage].states.text.charSpacing}}</span>
                    </div>
                    <div class="range range-line-height" ng-if="settings.nbdesigner_text_line_height == 1">
                        <label><?php esc_html_e('Line height','web-to-print-online-designer'); ?></label>
                        <div class="main-track">
                            <input class="slide-input" ng-change="setTextAttribute('lineHeight', stages[currentStage].states.text.lineHeight)" ng-model="stages[currentStage].states.text.lineHeight" type="range" step="0.01" min="0" max="3">
                            <span class="range-track"></span>
                        </div>
                        <span class="value-display">{{stages[currentStage].states.text.lineHeight}}</span>
                    </div>
                </div>
            </div>
        </li>
        <li class="menu-item item-spacing item-stroke nbd-tooltip-hover" ng-if="settings.nbdesigner_text_outline == 1" data-range="true" title="<?php esc_html_e('Stroke','web-to-print-online-designer'); ?>">
            <i class="icon-nbd">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <title>outline</title>
                    <path fill="#888" d="M23.292 12.134c0.138-0.445 0.208-0.91 0.208-1.384 0-2.619-2.131-4.75-4.75-4.75-1.396 0-2.685 0.61-3.573 1.632-0.021-0.021-0.035-0.046-0.056-0.067-0.973-0.974-2.349-1.533-3.776-1.533-1.422 0-2.794 0.556-3.77 1.525-0.264-0.431-0.644-0.813-1.122-1.108-0.474-0.294-1.032-0.449-1.613-0.449-0.482 0-0.955 0.109-1.369 0.316l-1.406 0.747c-1.442 0.721-2.051 2.526-1.313 4.002 0.272 0.543 0.714 0.982 1.248 1.272v4.663c0 1.654 1.346 3 3 3 0.766 0 1.458-0.297 1.989-0.771 0.54 0.487 1.25 0.771 2.011 0.771h5c0.778 0 1.479-0.305 2.010-0.795 0.796 0.5 1.731 0.795 2.74 0.795 2.895 0 5.25-2.355 5.25-5.25 0-0.922-0.25-1.825-0.708-2.616zM6 17c0 0.552-0.448 1-1 1s-1-0.448-1-1v-6.382c-0.144 0.072-0.306 0.106-0.471 0.106-0.401 0-0.813-0.203-0.988-0.553-0.247-0.494-0.031-1.095 0.463-1.342l1.361-0.724c0.141-0.070 0.307-0.105 0.475-0.105 0.199 0 0.4 0.050 0.561 0.149 0.294 0.183 0.599 0.504 0.599 0.851v8zM14 18h-5c-0.404 0-0.769-0.244-0.924-0.617-0.155-0.374-0.069-0.804 0.217-1.090l4-4c0.254-0.254 0.394-0.591 0.394-0.95s-0.14-0.695-0.394-0.949-0.601-0.381-0.949-0.381-0.696 0.127-0.952 0.382c-0.252 0.252-0.392 0.589-0.392 0.948 0 0.552-0.448 1-1 1s-1-0.448-1-1c0-0.894 0.348-1.733 0.98-2.364s1.498-0.947 2.364-0.947 1.731 0.316 2.363 0.948c0.632 0.631 0.979 1.471 0.979 2.363 0 0.893-0.348 1.733-0.979 2.364l-2.293 2.293h2.586c0.552 0 1 0.448 1 1s-0.448 1-1 1zM18.75 18c-1.792 0-3.25-1.458-3.25-3.25 0-0.552 0.448-1 1-1s1 0.448 1 1c0 0.689 0.561 1.25 1.25 1.25s1.25-0.561 1.25-1.25-0.561-1.25-1.25-1.25c-0.552 0-1-0.448-1-1s0.448-1 1-1c0.414 0 0.75-0.336 0.75-0.75s-0.336-0.75-0.75-0.75c-0.281 0-0.536 0.155-0.665 0.404-0.178 0.343-0.527 0.54-0.889 0.54-0.155 0-0.312-0.036-0.459-0.112-0.491-0.254-0.682-0.857-0.428-1.348 0.475-0.915 1.41-1.484 2.441-1.484 1.516 0 2.75 1.233 2.75 2.75 0 0.611-0.207 1.17-0.545 1.627 0.639 0.594 1.045 1.434 1.045 2.373 0 1.792-1.458 3.25-3.25 3.25z"></path>
                </svg>
            </i>
            <div class="sub-menu" data-pos="center">
                <div class="main-ranges" >
                    <div class="range range-line-height range-stroke">
                        <label><?php esc_html_e('Color','web-to-print-online-designer'); ?></label>
                        <span cattr="text.stroke" color="{{stages[currentStage].states.text.stroke}}" class="nbd-color-picker-preview nbd-color-picker" ng-style="{'background': stages[currentStage].states.text.stroke}"></span>
                        <span class="stroke-title">{{stages[currentStage].states.text.stroke}}</span>
                    </div>
                    <div class="range range-spacing">
                        <label><?php esc_html_e('Width','web-to-print-online-designer'); ?></label>
                        <div class="main-track">
                            <input class="slide-input" ng-change="setTextAttribute('strokeWidth', stages[currentStage].states.text.strokeWidth)" ng-model="stages[currentStage].states.text.strokeWidth" type="range" step="0.01" min="0" max="5">
                            <span class="range-track"></span>
                        </div>
                        <span class="value-display">{{stages[currentStage].states.text.strokeWidth}}</span>
                    </div>
                </div>
            </div>
        </li>
        <li class="menu-item nbd-color-picker" ng-if="settings.nbdesigner_text_background == 1" cattr="text.textBackgroundColor" 
            ng-dblclick="setTextAttribute('textBackgroundColor', null)" title="<?php esc_html_e('Double click to remove background','web-to-print-online-designer'); ?>">
            <i class="icon-nbd nbd-tooltip-hover" title="<?php esc_html_e('Background','web-to-print-online-designer'); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24">
                    <path d="M0 0h24v24H0z" fill="none"/>
                    <path fill="#888" d="M16.56 8.94L7.62 0 6.21 1.41l2.38 2.38-5.15 5.15c-.59.59-.59 1.54 0 2.12l5.5 5.5c.29.29.68.44 1.06.44s.77-.15 1.06-.44l5.5-5.5c.59-.58.59-1.53 0-2.12zM5.21 10L10 5.21 14.79 10H5.21zM19 11.5s-2 2.17-2 3.5c0 1.1.9 2 2 2s2-.9 2-2c0-1.33-2-3.5-2-3.5z"/>
                    <path d="M0 20h24v4H0z" fill="{{stages[currentStage].states.text.textBackgroundColor}}"/>
                </svg>
            </i>
        </li>
        <li  ng-show="stages[currentStage].states.type == 'curvedText'"  ng-class="stages[currentStage].states.type == 'curvedText' && curvedText.active ? 'active' : ''" class="menu-item item-curved nbd-tooltop-hover" data-range="true" title="<?php esc_html_e('Curved Text','web-to-print-online-designer'); ?>">
            <i ng-click="curvedText.active = !curvedText.active; $event.stopPropagation();" class="icon-nbd icon-nbd-vector" ></i>
            <div class="sub-menu" data-pos="center" >
                <i class="icon-nbd icon-nbd-clear close-submenu" ng-click="curvedText.active = !curvedText.active; $event.stopPropagation();"></i>
                <div class="box-curved" >
                    <textarea ng-change="setTextAttribute('text', stages[currentStage].states.text.text)" ng-model="stages[currentStage].states.text.text" placeholder="<?php esc_html_e('Enter your text','web-to-print-online-designer'); ?>"></textarea>
                </div>
                <div class="main-ranges">
                    <div class="range range-radius">
                        <label for="" class="nbd-text-align-left"><?php esc_html_e('Radius','web-to-print-online-designer'); ?></label>
                        <div class="main-track">
                            <input class="slide-input" ng-change="setTextAttribute('radius', stages[currentStage].states.text.radius)" ng-model="stages[currentStage].states.text.radius" type="range" step="1" min="50" max="400">
                            <span class="range-track"></span>
                        </div>
                        <span class="value-display">{{stages[currentStage].states.text.radius}}</span>
                    </div>
                    <div class="range range-spacing">
                        <label for="" class="nbd-text-align-left"><?php esc_html_e('Spacing','web-to-print-online-designer'); ?></label>
                        <div class="main-track">
                            <input class="slide-input" ng-change="setTextAttribute('spacing', stages[currentStage].states.text.spacing)" ng-model="stages[currentStage].states.text.spacing" type="range" step="1" min="0" max="40">
                            <span class="range-track"></span>
                        </div>
                        <span class="value-display">{{stages[currentStage].states.text.spacing}}</span>
                    </div>
                </div>
                <div class="box-curved-reverse">
                    <span class="nbd-font-size-12"><?php esc_html_e('Reverse','web-to-print-online-designer'); ?></span>
                    <div class="nbd-checkbox-group">
                        <input type="checkbox" id="curved-reverse-input" ng-change="setTextAttribute('reverse', stages[currentStage].states.text.reverse)" ng-model="stages[currentStage].states.text.reverse">
                        <label for="curved-reverse-input"></label>
                    </div>
                    <span class="nbd-font-size-12 rtl-title" ><?php esc_html_e('RTL','web-to-print-online-designer'); ?></span>
                    <div class="nbd-checkbox-group">
                        <input type="checkbox" id="curved-rtl" ng-change="setTextAttribute('rtl', stages[currentStage].states.text.rtl)" ng-model="stages[currentStage].states.text.rtl">
                        <label for="curved-rtl"></label>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</div>