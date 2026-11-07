<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
$template_tags = get_terms( 'template_tag', 'hide_empty=0' );
?>
<div class="nbd-popup popup-template-tags" data-animate="scale">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <i class="icon-nbd icon-nbd-clear close-popup"></i>
        <div class="head">
            <?php esc_html_e('Template','web-to-print-online-designer'); ?>
        </div>
        <div class="body">
            <div class="main-body tab-scroll">
                <div class="template-name template-field-wrap">
                    <label class="template-label"><?php esc_html_e('Name','web-to-print-online-designer'); ?></label>
                    <div>
                        <input type="text" ng-model="customTemplate.name"/>
                    </div>
                </div>
                <div class="template-preview template-field-wrap">
                    <label class="template-label"><?php esc_html_e('Thumbnail','web-to-print-online-designer'); ?></label>
                    <div class="nb-radio">
                        <input name="template_preview_type" value="1" ng-model="customTemplate.type" type="radio" id="template_preview_type_1"/> <label for='template_preview_type_1'><?php esc_html_e('Default ( the first side )','web-to-print-online-designer'); ?></label>
                    </div>
                    <div class="nb-radio">
                        <input name="template_preview_type" value="2" ng-model="customTemplate.type" type="radio" id="template_preview_type_2"/> <label for='template_preview_type_2'><?php esc_html_e('Custom','web-to-print-online-designer'); ?></label>
                    </div>
                    <div class="template-preview-file" ng-if="customTemplate.type == 2">
                        <input type="file" accept="image/*" nbd-upload-file="selectCustomTemplatePreview(files)" />
                    </div>
                </div>
                <div class="template-tags template-field-wrap">
                    <label class="template-label"><?php esc_html_e('Tags','web-to-print-online-designer'); ?> <span ng-click="reloadTemplateTags()" class="template-tags-reload"><?php esc_html_e('Reload','web-to-print-online-designer'); ?></span></label>
                    <div ng-if="customTemplate.reload == 0">
                    <?php if ( ! empty( $template_tags ) && ! is_wp_error( $template_tags ) ): ?>
                        <?php foreach( $template_tags as $tag ): ?>
                        <span ng-class="isSelectedTags( <?php echo( $tag->term_id ); ?> ) ? 'selected' : '' " class="nbd-tag" ng-click="addTemplateTag( <?php echo( $tag->term_id ); ?> )"><span><?php echo( $tag->name ); ?></span></span>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if ( count($template_tags) == 0 ): ?>
                            <a><?php esc_html_e('No ','web-to-print-online-designer'); ?></a>
                        <?php endif; ?>
                    </div>
                    <div ng-if="customTemplate.reload == 1">
                        <span ng-class="isSelectedTags( tag.term_id ) ? 'selected' : '' " ng-repeat="tag in customTemplate.tags" class="nbd-tag" ng-click="reloadTemplateTags(tag.term_id)"><span>{{tag.name}}</span></span>
                    </div>
                </div>
                <div class="template-tags template-field-wrap">
                    <label class="template-label"><?php esc_html_e('Primary colors','web-to-print-online-designer'); ?></label>
                    <div>
                        <span class="nbd-gallery-filter-tag" ng-repeat="(colorIndex, color) in customTemplate.selectedColors">
                            <span class="nbd-filter-color" ng-style="{background: '#' + color}"></span>
                            <span class="nbd-filter-tag-remove" ng-click="removeTemplateColor(colorIndex)">
                                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                    <path d="M18.984 6.422l-5.578 5.578 5.578 5.578-1.406 1.406-5.578-5.578-5.578 5.578-1.406-1.406 5.578-5.578-5.578-5.578 1.406-1.406 5.578 5.578 5.578-5.578z"></path>
                                </svg>
                            </span>
                        </span>
                        <span class="nbd-gallery-filter-tag add-filter-color-wrap">
                            <span class="nbd-filter-color add-filter-color" ng-style="{background: '#ddd'}" ng-click="customTemplate.showPicker = !customTemplate.showPicker;"><i class="icon-nbd icon-nbd-add-black"></i></span>
                            <span class="__add"><?php esc_html_e('Add','web-to-print-online-designer'); ?></span>
                            <div class="nbd-gallery-filter-picker" ng-class="customTemplate.showPicker ? 'active' : ''">
                                <spectrum-colorpicker
                                    ng-model="customTemplate.newColor"
                                    options="{
                                        preferredFormat: 'hex',
                                        flat: true,
                                        showButtons: false,
                                        showInput: true,
                                        containerClassName: 'nbd-sp'
                                }">
                                </spectrum-colorpicker>
                                <div>
                                    <button class="nbd-button" ng-click="addTemplateColor(customTemplate.newColor);"><?php esc_html_e('Choose','web-to-print-online-designer'); ?></button>
                                </div>
                            </div>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer">
            <div class="share-btn">
                <span class="nbd-button" ng-click="saveData()"><?php esc_html_e('Save template','web-to-print-online-designer'); ?></span>
            </div>
        </div>
    </div>
</div>