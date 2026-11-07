<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="nbd-items-dropdown template-tags-wrap" ng-if="settings.template_tags">
    <div class="main-items">
        <div class="items">
            <div class="item" data-type="tags-{{template_tag.id}}" data-api="false" ng-repeat="template_tag in settings.template_tags" ng-click="resource.templateType = 'custom'">
                <div class="main-item">
                    <div class="item-icon">
                        <span class="template_shadow"></span>
                        <span class="template_shadow"></span>
                        <img ng-src="{{template_tag.thumb}}" style="max-width: 80px; height: 80px;background: #b8c1cc;"/>
                    </div>
                    <div class="item-info">
                        <span class="item-name" ng-bind-html="template_tag.name | html_trusted"></span>
                    </div>
                </div>
            </div>
            <div ng-if="settings.task != 'create_template' && settings.product_data.option.admindesign == '1' && settings.product_data.option.global_template == '1' && settings.product_data.option.global_template_cat != ''" 
                 class="item" data-type="globalTemplate" data-api="false" ng-click="resource.templateType = 'global'">
                <div class="main-item">
                    <div class="item-icon">
                        <span class="template_shadow"></span>
                        <span class="template_shadow"></span>
                        <img src="<?php echo NBDESIGNER_ASSETS_URL . 'images/g_template.png'; ?>" style="max-width: 80px; height: 80px;"/>
                    </div>
                    <div class="item-info">
                        <span class="item-name"><?php _e('Library','web-to-print-online-designer'); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="pointer"></div>
    </div>
    <div class="result-loaded">
        <div class="content-items">
            <div class="content-item" data-type="tags-{{template_tag.id}}" ng-repeat="template_tag in settings.template_tags" >
                <div ng-repeat="temp in template_tag.templates" class="item-img " style="position: relative;" nbd-template-hover="{{temp.id}}" ng-click="insertTemplate(false, temp)">
                    <img ng-src="{{temp.thumbnail}}" alt="{{temp.name}}" style="border-radius: 4px;">
                </div>
            </div>
            <div class="content-item" nbd-scroll="scrollLoadMore(container, type)" data-container="#tab-product-template" data-type="globalTemplate" data-offset="30" data-current-type="{{resource.templateType}}">
                <div class="item-img" style="position: relative;" ng-repeat="temp in resource.globalTemplate.data" ng-click="insertGlobalTemplate(temp.id, $index)">
                    <img ng-src="{{temp.thumbnail}}" alt="{{temp.name}}" style="border-radius: 4px;">
                    <?php if(!$valid_license): ?>
                    <span class="nbd-pro-mark-wrap" ng-if="$index > 4">
                        <svg class="nbd-pro-mark" fill="#F3B600" xmlns="http://www.w3.org/2000/svg" viewBox="-505 380 12 10"><path d="M-503 388h8v1h-8zM-494 382.2c-.4 0-.8.3-.8.8 0 .1 0 .2.1.3l-2.3.7-1.5-2.2c.3-.2.5-.5.5-.8 0-.6-.4-1-1-1s-1 .4-1 1c0 .3.2.6.5.8l-1.5 2.2-2.3-.8c0-.1.1-.2.1-.3 0-.4-.3-.8-.8-.8s-.8.4-.8.8.3.8.8.8h.2l.8 3.3h8l.8-3.3h.2c.4 0 .8-.3.8-.8 0-.4-.4-.7-.8-.7z"></path></svg>
                        <?php _e('Pro','web-to-print-online-designer'); ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>