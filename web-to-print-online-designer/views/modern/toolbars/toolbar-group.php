<div  ng-show="stages[currentStage].states.isGroup" class="toolbar-group">
    <ul class="nbd-main-menu">
        <li ng-click="groupLayers()" class="menu-item menu-group" ng-show="stages[currentStage].states.isGroup">
            <i class=" tooltipstered nbd-tooltip-hover" title="<?php esc_html_e('Group layers','web-to-print-online-designer'); ?>"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="23" height="20" viewBox="0 0 23 20">
                <title><?php esc_html_e('Group layers','web-to-print-online-designer'); ?></title>
                <path d="M22.857 4.286h-1.429v11.429h1.429v4.286h-4.286v-1.429h-14.286v1.429h-4.286v-4.286h1.429v-11.429h-1.429v-4.286h4.286v1.429h14.286v-1.429h4.286v4.286zM20 1.429v1.429h1.429v-1.429h-1.429zM1.429 1.429v1.429h1.429v-1.429h-1.429zM2.857 18.571v-1.429h-1.429v1.429h1.429zM18.571 17.143v-1.429h1.429v-11.429h-1.429v-1.429h-14.286v1.429h-1.429v11.429h1.429v1.429h14.286zM21.429 18.571v-1.429h-1.429v1.429h1.429zM14.286 7.143h4.286v8.571h-10v-2.857h-4.286v-8.571h10v2.857zM5.714 11.429h7.143v-5.714h-7.143v5.714zM17.143 14.286v-5.714h-2.857v4.286h-4.286v1.429h7.143z"></path>
            </svg></i>
        </li>
        <li ng-show=" pt_color.key == 'pt_color2' && pt_color.limit == 1 " class="menu-item item-color-fill nbd-show-color-palette">
            <span ng-style="{'background': color_used[0]}" class="nbd-tooltip-hover color-fill nbd-color-picker-preview" title="<?php esc_html_e('Color','web-to-print-online-designer'); ?>" ></span>
        </li>
    </ul>
</div>