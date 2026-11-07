<div class="tab <?php if( $active_typos ) echo 'active'; ?> " ng-if="settings['nbdesigner_enable_text'] == 'yes'" id="tab-typography" nbd-scroll="scrollLoadMore(container, type)" data-container="#tab-typography" data-type="typography" data-offset="20">
    <div class="tab-main tab-scroll">
        <div class="typography-head">
            <span class="text-guide" ><?php esc_html_e('Click to add text','web-to-print-online-designer'); ?></span>
            <div class="head-main">
                <span class="text-heading" ng-click='addText("<?php esc_html_e('Heading','web-to-print-online-designer'); ?>", "heading")' ><?php esc_html_e('Add heading','web-to-print-online-designer'); ?></span>
                <span class="text-sub-heading" ng-click="addText('<?php esc_html_e('Subheading','web-to-print-online-designer');?>', 'subheading')" ><?php esc_html_e('Add subheading','web-to-print-online-designer');?></span>
                <span ng-click="addText('<?php echo str_replace( "&#039;", "\'", esc_attr__('Add a little bit of body text','web-to-print-online-designer') ); ?>')" class="text-body" ><?php esc_html_e('Add a little bit of body text','web-to-print-online-designer'); ?></span>
                <span ng-show="settings.nbdesigner_enable_curvedtext == 'yes'" ng-click="addCurvedText('<?php esc_html_e('Curved text','web-to-print-online-designer'); ?>')" class="text-body text-curved"><?php esc_html_e('Add curved text','web-to-print-online-designer'); ?></span>
            </div>
        </div>
        <hr class="seperate" ng-if="settings.nbdesigner_hide_typo_section == 'no'" />
        <div ng-if="settings.nbdesigner_hide_typo_section == 'no'" class="typography-body">
            <ul class="typography-items">
                <li nbd-drag="typo.folder" type="typo" ng-click="insertTypography(typo)" class="typography-item" ng-repeat="typo in resource.typography.data | limitTo: resource.typography.filter.currentPage * resource.typography.filter.perPage" repeat-end="onEndRepeat('typography')">
                    <img ng-src="{{generateTypoLink(typo)}}" alt="Typography" />
                </li>
            </ul>
            <div class="loading-photo" >
                <svg class="circular" viewBox="25 25 50 50">
                    <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                </svg>
            </div>
        </div>
    </div>
</div>