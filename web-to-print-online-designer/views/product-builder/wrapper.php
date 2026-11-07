<?php
    if( !(isset($is_nbpb_creating_task) && $is_nbpb_creating_task) ){
        $is_creating_task = 0;
        include 'js_config.php';
    }
    if($is_creating_task == 1):
?>
<div class="nbdpb-load-page nbdpb-show">
    <div class="nbpb-loader">
        <svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
        </svg>
    </div>
</div>
<?php endif; ?>
<div class="nbdpb-popup popup-design <?php echo ($is_creating_task == 0 && is_admin_bar_showing()) ? 'is-admin-bar' : ''; ?>" data-animate="scale">
    <?php if( $is_creating_task == 0 ): ?>
    <div class="nbdpb-load-page">
        <div class="nbpb-loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
            </svg>
        </div>
    </div>
    <?php endif; ?>
    <?php if( $is_creating_task == 0 ): ?><i class="icon-nbd icon-nbd-clear close-popup"></i><?php endif; ?>
    <div id="nbdpb-app" class="nbdpb-product-builder nbdpb-full-contain">
        <div ng-controller="nbpbCtrl" class="nbdpb-full-contain">
            <div id="nbpb-container" class="nbdpb-full-contain">
                <div class="design-main nbdpb-full-contain">
                    <div class="design-layer">
                        <div class="design-stages nbdpb-carousel-outer">
                            <div class="nbdpb-carousel">
                                <div ng-repeat="stage in stages" ng-class="{'nbdpb-active': $index == 0}" class="nbdpb-carousel-item nbdpb-full-contain">
                                    <div class="stage nbdpb-full-contain" id='stage-{{$index}}' data-stage="{{$index}}">
                                        <div class="stage-main">
                                            <div class="nbpb-background">
                                                
                                            </div>
                                            <div class="design-zone nbdpb-full-contain" ng-style="{'width': stage.config.width,
                                            'height': stage.config.height,
                                            'top': stage.config.top + 'px',
                                            'left': stage.config.left + 'px',
                                            'background-image': 'url(' + resource.views[$index].base_url + ')'
                                            }">
                                                <canvas nbd-canvas class="nbdpb-full-contain" stage="stage" index="{{$index}}" id="canvas-{{$index}}" last="{{$last ? 1 : 0}}"></canvas>
                                                <div class="nbpb-overlay">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="attr-name" style="display: none"><span>{{resource.components[resource.currentComponent].name}}</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="nbpb-stage-loading">
                                <div class="nbpb-loader">
                                    <svg class="circular" viewBox="25 25 50 50">
                                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="design-finish">
                            <button class="nbdpb-btn btn-finish" style="font-weight: bold;" ng-click="saveData()">Done</button>
                        </div>
                        <div class="design-admin-tool nbdpb-show" ng-if="stages[currentStage].states.showAdminTool">
                            <div class="tools">
                                <div class="tool-item" title="<?php _e('Bring Forward', 'web-to-print-online-designer'); ?>" ng-click="setStackPosition('bring-forward')"><i class="icon-nbd icon-nbd-bring-forward"></i></div>
                                <div class="tool-item" title="<?php _e('Send To Backward', 'web-to-print-online-designer'); ?>" ng-click="setStackPosition('send-backward')"><i class="icon-nbd icon-nbd-sent-to-backward"></i></div>
                                <div class="tool-item" title="<?php _e('Zoom', 'web-to-print-online-designer'); ?>">
                                    <i class="icon-nbd nbpb-zoom-icon">
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <path fill="#666" d="M15.504 13.616l-3.79-3.223c-0.392-0.353-0.811-0.514-1.149-0.499 0.895-1.048 1.435-2.407 1.435-3.893 0-3.314-2.686-6-6-6s-6 2.686-6 6 2.686 6 6 6c1.486 0 2.845-0.54 3.893-1.435-0.016 0.338 0.146 0.757 0.499 1.149l3.223 3.79c0.552 0.613 1.453 0.665 2.003 0.115s0.498-1.452-0.115-2.003zM6 10c-2.209 0-4-1.791-4-4s1.791-4 4-4 4 1.791 4 4-1.791 4-4 4z"></path>
                                        </svg>
                                    </i>
                                    <div class="nbpb-config-panel">
                                        <span style="margin-right: 10px;line-height: 30px;"><?php _e('Zoom', 'web-to-print-online-designer'); ?></span>
                                        <span class="nbpb-zoom-act" ng-click="updateLayerAttribute('scaleX', stages[currentStage].states.scaleX * 0.9);updateLayerAttribute('scaleY', stages[currentStage].states.scaleY * 0.9)" title="<?php _e('Zoom out', 'web-to-print-online-designer'); ?>">-</span>
                                        <span class="nbpb-zoom-act" ng-click="updateLayerAttribute('scaleX', stages[currentStage].states.scaleX * 1.11111111111);updateLayerAttribute('scaleY', stages[currentStage].states.scaleY * 1.11111111111)" title="<?php _e('Zoom in', 'web-to-print-online-designer'); ?>">+</span>
                                    </div>
                                </div>
                                <div class="tool-item" title="<?php _e('Rotate', 'web-to-print-online-designer'); ?>">
                                    <i class="icon-nbd">
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <path fill="#666" d="M12.984 4.078c3.938 0.469 7.031 3.844 7.031 7.922s-3.094 7.453-7.031 7.922v-2.016c2.859-0.469 5.016-2.953 5.016-5.906s-2.156-5.438-5.016-5.906v3.891l-4.547-4.453 4.547-4.547v3.094zM7.078 18.328l1.453-1.453c0.75 0.563 1.594 0.891 2.484 1.031v2.016c-1.406-0.188-2.766-0.703-3.938-1.594zM6.094 12.984c0.141 0.891 0.469 1.734 0.984 2.484l-1.406 1.406c-0.891-1.172-1.406-2.484-1.594-3.891h2.016zM7.125 8.531c-0.516 0.75-0.891 1.594-1.031 2.484h-2.016c0.188-1.406 0.75-2.719 1.641-3.891z"></path>
                                        </svg>
                                    </i>
                                    <div class="nbpb-config-panel">
                                        <span style="margin-right: 10px;"><?php _e('Angle', 'web-to-print-online-designer'); ?></span>
                                        <input ng-change="updateLayerAttribute('angle', stages[currentStage].states.angle)" style="cursor: pointer;" ng-model="stages[currentStage].states.angle" type="range" min="0" max="360" step="0.1" />
                                        <span style="margin-left: 10px;">{{stages[currentStage].states.angle}}</span>
                                    </div>
                                </div>
                                <div class="tool-item" title="<?php _e('Clear all layer', 'web-to-print-online-designer'); ?>" ng-click="clearAllStages()"><i class="icon-nbd icon-nbd-clear"></i></div>
                            </div>
                        </div>
                    </div>
                    <div class="design-sidebar">
                        <div ng-class="(!resource.showValue) ? 'nbdpb-show' : ''" class="sidebar-item sidebar-attribute nbdpb-full-contain nbdpbSlide">
                            <div class="attribute-main nbdpb-full-contain">
                                <div class="nbdpb-scroll">
                                    <div class="product-attr">
                                        <div nbpb-hover="{{component.id}}" ng-show="component.enable" ng-repeat="component in resource.components" ng-click="showAttribute($index)" ng-class="component.nbpb_type == 'nbpb_com' ? '' : 'attr-without-image'" class="attr-item">
                                            <div class="attr-img" ng-if="component.nbpb_type == 'nbpb_com'">
                                                <img ng-src="{{component.general.component_icon_url}}" alt="{{component.general.title}}">
                                            </div>
                                            <span class="attr-name">{{component.general.title}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="design-finish" ng-click="saveData()">
                                <span><?php _e('Done', 'web-to-print-online-designer'); ?></span>
                            </div>
                        </div>
                        <div ng-class="(resource.showValue) ? 'nbdpb-show' : ''" class="sidebar-item sidebar-value nbdpb-full-contain nbdpbSlide">
                            <div class="product-value nbdpb-full-contain">
                                <div class="attr-name">
                                    <span>{{resource.components[resource.currentComponent].general.title}}</span>
                                </div>
                                <div class="product-value-main">
                                    <div class="nbdpb-scroll">
                                        <div class="values" ng-if="resource.components[resource.currentComponent].nbpb_type == 'nbpb_com'">
                                            <div ng-repeat="sattr in resource.components[resource.currentComponent].current_pb_configs" ng-click="selectAttribute($index)" ng-class="($index == resource.components[resource.currentComponent].currentConfig) ? 'active' : ''" class="value-item">
                                                <div class="value-color" ng-style="{'background': sattr.bg_type == 'i' ? 'url(' + sattr.icon_bg + ')' : sattr.icon_color}"></div>
                                                <span class="value-name">{{sattr.sattr_name}}</span>
                                                <span class="value-name">{{sattr.attr_name}}</span>
                                            </div>
                                        </div>
                                        <div class="values nbpb-component-text" ng-if="resource.components[resource.currentComponent].nbpb_type == 'nbpb_text'">
                                            <div class="nbpb-text-config-wrap">
                                                <div class="nbpb-text-config">
                                                <b>{{resource.components[resource.currentComponent].general.description}}</b>
                                                </div>
                                                <div class="nbpb-text-config">
                                                    <label><?php _e('Content', 'web-to-print-online-designer'); ?></label>
                                                    <div>
                                                        <input ng-change="updateText()" maxlength="{{resource.components[resource.currentComponent].general.text_option.max}}" placeholder="{{resource.components[resource.currentComponent].general.nbpb_text_configs.default_text}}" ng-model="resource.components[resource.currentComponent].currentContent" />
                                                    </div>
                                                </div>
                                                <div class="nbpb-text-config" ng-if="resource.currentComponentObj.general.nbpb_text_configs.allow_font_family == 'y' || settings.is_creating_task == 1">
                                                    <label><?php _e('Font family', 'web-to-print-online-designer'); ?></label>
                                                    <div ng-if="resource.currentComponentObj.general.nbpb_text_configs.allow_all_font == 'y'">
                                                        <select class="nbpb-dropdown" ng-change="updateText()" ng-model="resource.components[resource.currentComponent].currentFontId">
                                                            <?php foreach($fonts as $font): ?>
                                                            <option value="<?php if($font->type == 'google') echo 'g' . $font->id; else echo 'c' . $font->id;  ?>" ><?php echo $font->name; ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div ng-if="resource.currentComponentObj.general.nbpb_text_configs.allow_all_font == 'n'">
                                                        <select class="nbpb-dropdown" ng-change="updateText()" ng-model="resource.components[resource.currentComponent].currentFontId">
                                                            <option ng-if="settings.custom_fonts[font]" ng-repeat="font in resource.currentComponentObj.general.nbpb_text_configs.custom_fonts" value="{{'c' + font }}">{{settings.custom_fonts[font].name}}</option>
                                                            <option ng-if="settings.google_fonts[font]" ng-repeat="font in resource.currentComponentObj.general.nbpb_text_configs.google_fonts" value="{{'g' + font }}">{{settings.google_fonts[font].name}}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="nbpb-text-config" ng-if="resource.currentComponentObj.general.nbpb_text_configs.allow_change_color == 'y' || settings.is_creating_task == 1">
                                                    <label><?php _e('Color', 'web-to-print-online-designer'); ?></label>
                                                    <div ng-if="resource.currentComponentObj.general.nbpb_text_configs.allow_all_color == 'n'">
                                                        <span class="nbpb-swatch" ng-click="resource.components[resource.currentComponent].currentColor = color.color;updateText()" ng-class="resource.components[resource.currentComponent].currentColor == color.color ? 'active' : ''" ng-repeat="color in resource.currentComponentObj.general.nbpb_text_configs.colors" ng-style="{'background': color.color}">
                                                            <span class="nbpb-swatch-tooltip">{{color.name}}</span>
                                                        </span>
                                                    </div>
                                                    <div ng-show="resource.currentComponentObj.general.nbpb_text_configs.allow_all_color == 'y' || settings.is_creating_task == 1">
                                                        <input class="nbpb-color-picker"  on-change="selectColor(color)" options="resource.colorOptions" />
                                                    </div>
                                                </div>
                                                <div class="nbpb-text-config">
                                                    <button ng-click="deleteLayer('text')" class="nbdpb-btn  nbdpb-btn-delete" style="width: 100%;"><?php _e('Delete', 'web-to-print-online-designer'); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="values nbpb-component-image" ng-if="resource.components[resource.currentComponent].nbpb_type == 'nbpb_image'">
                                            <div class="nbpb-image-config-wrap">
                                                <div class="nbpb-image-config">
                                                    <b>{{resource.components[resource.currentComponent].general.description}}</b>
                                                </div>
                                                <div class="upload-zone nbpb-image-config" data-field-id="{{resource.currentComponentObj.id}}" nbd-dnd-file="uploadImage(field_id, files)">
                                                    <input type="file" autocomplete="off" class="inputfile" accept=".png,.jpg,.jpeg"/> 
                                                    <label>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17">
                                                            <path fill="#666" d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"></path>
                                                        </svg>
                                                        <span style="margin-bottom: 10px; margin-top: 10px;"><?php _e('Click or drop file here', 'web-to-print-online-designer'); ?></span>
                                                        <span class="upload-note" ng-if="resource.currentComponentObj.general.upload_option.allow_type != ''"><small><?php _e('Allow extensions', 'web-to-print-online-designer'); ?>: {{resource.currentComponentObj.general.upload_option.allow_type}}</small></span>
                                                        <span class="upload-note" ng-if="resource.currentComponentObj.general.upload_option.min_size != ''"><small><?php _e('Min size', 'web-to-print-online-designer'); ?> {{resource.currentComponentObj.general.upload_option.min_size}} MB</small></span>
                                                        <span class="upload-note" ng-if="resource.currentComponentObj.general.upload_option.max_size != ''"><small><?php _e('Max size', 'web-to-print-online-designer'); ?> {{resource.currentComponentObj.general.upload_option.max_size}} MB</small></span>
                                                    </label>
                                                    <svg class="nbd-upload-loading" xmlns="http://www.w3.org/2000/svg" width="50px" height="50px" viewBox="0 0 50 50"><circle fill="none" opacity="0.05" stroke="#000000" stroke-width="3" cx="25" cy="25" r="20"/><g transform="translate(25,25) rotate(-90)"><circle  style="stroke:#48B0F7; fill:none; stroke-width: 3px; stroke-linecap: round" stroke-dasharray="110" stroke-dashoffset="0"  cx="0" cy="0" r="20"><animate attributeName="stroke-dashoffset" values="360;140" dur="2.2s" keyTimes="0;1" calcMode="spline" fill="freeze" keySplines="0.41,0.314,0.8,0.54" repeatCount="indefinite" begin="0"/><animateTransform attributeName="transform" type="rotate" values="0;274;360" keyTimes="0;0.74;1" calcMode="linear" dur="2.2s" repeatCount="indefinite" begin="0"/><animate attributeName="stroke" values="#10CFBD;#48B0F7;#ff0066;#48B0F7;#10CFBD" fill="freeze" dur="3s" begin="0" repeatCount="indefinite"/></circle></g></svg>
                                                </div>
                                                <div class="nbpb-image-config">
                                                    <button ng-click="deleteLayer('image')" class="nbdpb-btn nbdpb-btn-delete" style="width: 100%;"><?php _e('Delete', 'web-to-print-online-designer'); ?></button>
                                                </div>
                                                <div class="nbpb-uploaded">
                                                    <img ng-click="addImage(img)" ng-repeat="img in resource.uploaded" ng-src="{{img}}" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="product-value-act">
                                    <div class="value-act-finish value-act-item" ng-click="saveLayer()"><i class="icon-nbd icon-nbd-fomat-done"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>