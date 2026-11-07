<?php
$custom_logo_id = get_theme_mod( 'custom_logo' );
$image          = wp_get_attachment_image_src( $custom_logo_id , 'full' );
$without_logo   = false;
if( !isset( $image['0'] ) ){
    $logo_option    = nbdesigner_get_option('nbdesigner_editor_logo');
    $logo_url       = wp_get_attachment_url( $logo_option );
    if(!$logo_url){
        $without_logo = true;
    }
}else{
    $logo_url = $image['0'];
}
?>
<div class="nbd-main-bar">
    <a href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>" class="logo <?php if( $without_logo ) echo ' logo-without-image'; ?>">
        <?php if( !$without_logo ): ?>
        <img src="<?php echo esc_url( $logo_url );?>" alt="online design">
        <?php else: ?>
        <?php esc_html_e('Home','web-to-print-online-designer'); ?>
        <?php endif; ?>
    </a>
    <i class="icon-nbd icon-nbd-menu menu-mobile"></i>
    <ul class="nbd-main-menu menu-left">
        <li class="menu-item item-edit">
            <span><?php esc_html_e('File','web-to-print-online-designer'); ?></span>
            <div class="sub-menu" data-pos="left">
                <ul>
                    <?php if( is_user_logged_in() ): ?>
                    <li ng-if="false" class="sub-menu-item flex space-between" ng-click="loadUserDesigns()">
                        <span><?php esc_html_e('Open My Logo','web-to-print-online-designer'); ?></span>
                    </li>
                    <li class="sub-menu-item flex space-between item-import-file" ng-click="loadMyDesign(null, false)">
                        <span><?php esc_html_e('Open My Design','web-to-print-online-designer'); ?></span>
                        <small>{{ 'M-O' | keyboardShortcut }}</small>
                    </li>
                    <?php endif; ?>
                    <li class="sub-menu-item flex space-between item-import-file" ng-click="loadMyDesign(null, true)">
                        <span><?php esc_html_e('My Design in Cart','web-to-print-online-designer'); ?></span>
                        <small>{{ 'M-S-O' | keyboardShortcut }}</small>
                    </li>
                    <li class="sub-menu-item flex space-between item-import-file" ng-click="importDesign()">
                        <span><?php esc_html_e('Import Design','web-to-print-online-designer'); ?></span>
                        <small>{{ 'M-S-I' | keyboardShortcut }}</small>
                    </li>
                    <li class="sub-menu-item flex space-between" ng-click="exportDesign()">
                        <span><?php esc_html_e('Export Design','web-to-print-online-designer'); ?></span>
                        <small>{{ 'M-S-E' | keyboardShortcut }}</small>
                    </li>
                    <?php if( $settings['allow_customer_download_design_in_editor'] == 'yes' && ( $settings['nbdesigner_download_design_in_editor_png'] == '1' || $settings['nbdesigner_download_design_in_editor_pdf'] == '1' || $settings['nbdesigner_download_design_in_editor_jpg'] == '1' || $settings['nbdesigner_download_design_in_editor_svg'] == '1' ) ): ?>
                    <li class="sub-menu-item flex space-between hover-menu" data-animate="bottom-to-top">
                        <span class="title-menu"><?php esc_html_e('Download','web-to-print-online-designer'); ?></span>
                        <i class="icon-nbd icon-nbd-arrow-drop-down rotate-90"></i>
                        <div class="hover-sub-menu-item">
                            <ul>
                                <?php if( $settings['nbdesigner_download_design_in_editor_png'] == '1' ): ?>
                                <li ng-click="saveDesignCustom('png')"><span class="title-menu"><?php _e('PNG','web-to-print-online-designer'); ?></span></li>
                                <?php endif; ?>
                                <?php if( $settings['nbdesigner_download_design_in_editor_jpg'] == '1' ): ?>
                                <li ng-click="saveDesignCustom('jpg')"><span class="title-menu"><?php esc_html_e('CMYK JPG','web-to-print-online-designer'); ?></span></li>
                                <?php endif; ?>
                                <?php if( $settings['nbdesigner_download_design_in_editor_svg'] == '1' ): ?>
                                <li ng-click="saveDesignCustom('svg')"><span class="title-menu"><?php esc_html_e('SVG','web-to-print-online-designer'); ?></span></li>
                                <?php endif; ?>
                                <?php if( $settings['nbdesigner_download_design_in_editor_pdf'] == '1' ): ?>
                                <li ng-click="saveDesignCustom('pdf')"><span class="title-menu"><?php _e('PDF','web-to-print-online-designer'); ?></span></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div id="nbd-overlay"></div>
        </li>
        <li class="menu-item item-edit">
            <span><?php esc_html_e('Edit','web-to-print-online-designer'); ?></span>
            <div class="sub-menu" data-pos="left">
                <ul>
                    <li class="sub-menu-item flex space-between" ng-click="_clearAllStage()">
                        <span><?php esc_html_e('Clear all design','web-to-print-online-designer'); ?></span>
                        <small>{{ 'M-E' | keyboardShortcut }}</small>
                    </li>
<!--                    <li ng-if="settings.nbdesigner_save_for_later == 'yes'" class="sub-menu-item flex space-between" ng-click="saveForLater()">
                        <span><?php esc_html_e('Save for later','web-to-print-online-designer'); ?></span>
                        <small>{{ 'M-S-S' | keyboardShortcut }}</small>
                    </li>  -->
                    <li ng-if="settings.nbdesigner_save_for_later == 'yes'" class="sub-menu-item flex space-between" ng-click="prepareBeforeSaveForLater()">
                        <span><?php esc_html_e('Save for later','web-to-print-online-designer'); ?></span>
                        <small>{{ 'M-S-S' | keyboardShortcut }}</small>
                    </li> 
                    <li ng-if="settings.nbdesigner_enable_template_mapping == 'yes' && templateHolderFields.length > 0" class="sub-menu-item flex space-between" ng-click="showTemplateFieldsPopup( true )">
                        <span><?php esc_html_e('Fill out with your information','web-to-print-online-designer'); ?></span>
                    </li>
                </ul>
            </div>
            <div id="nbd-overlay"></div>
        </li>
        <li class="menu-item item-view">
            <span><?php esc_html_e('View','web-to-print-online-designer'); ?></span>
            <ul class="sub-menu" data-pos="left">
                <li ng-show="!settings.is_mobile" class="sub-menu-item flex space-between" ng-click="toggleRuler()" ng-class="settings.showRuler ? 'active' : ''">
                    <span class="title-menu"><?php esc_html_e('Ruler','web-to-print-online-designer'); ?></span>
                    <small>{{ 'M-R' | keyboardShortcut }}</small>
                </li>
                <li class="sub-menu-item flex space-between" ng-click="settings.showGrid = !settings.showGrid" ng-class="settings.showGrid ? 'active' : ''">
                    <span class="title-menu"><?php esc_html_e('Show grid','web-to-print-online-designer'); ?></span>
                    <small>{{ 'S-G' | keyboardShortcut }}</small>
                </li>
                <li class="sub-menu-item flex space-between" ng-click="settings.bleedLine = !settings.bleedLine" ng-class="settings.bleedLine ? 'active' : ''">
                    <span class="title-menu"><?php esc_html_e('Show bleed line','web-to-print-online-designer'); ?></span>
                    <small>{{ 'M-L' | keyboardShortcut }}</small>
                </li>
                <li ng-show="!settings.is_mobile" class="sub-menu-item flex space-between" ng-click="settings.showDimensions = !settings.showDimensions" ng-class="settings.showDimensions ? 'active' : ''">
                    <span class="title-menu"><?php esc_html_e('Show dimensions','web-to-print-online-designer'); ?></span>
                    <small>{{ 'S-D' | keyboardShortcut }}</small>
                </li>
                <li class="sub-menu-item flex space-between" ng-click="clearGuides()" ng-class="!(stages[currentStage].rulerLines.hors.length > 0 || stages[currentStage].rulerLines.vers.length > 0) ? 'nbd-disabled' : ''">
                    <span class="title-menu"><?php esc_html_e('Clear Guides','web-to-print-online-designer'); ?></span>
                    <small>{{ 'S-L' | keyboardShortcut }}</small>
                </li>
                <!--<li class="sub-menu-item flex space-between hover-menu" data-animate="bottom-to-top" ng-click="settings.snapMode.status = !settings.snapMode.status;" ng-class="settings.snapMode.status ? 'active' : ''">
                    <span class="title-menu"><?php esc_html_e('Snap to','web-to-print-online-designer'); ?></span>
                    <i class="icon-nbd icon-nbd-arrow-drop-down rotate-90" ng-show="settings.snapMode.status"></i>
                    <div class="hover-sub-menu-item" ng-show="settings.snapMode.status">
                        <ul>
                            <li ng-click="settings.snapMode.type = 'layer'; $event.stopPropagation();" ng-class="settings.snapMode.type == 'layer' ? 'active' : ''"><span class="title-menu"><?php esc_html_e('Layer','web-to-print-online-designer'); ?></span></li>
                            <li ng-click="settings.snapMode.type = 'bounding'; $event.stopPropagation();" ng-class="settings.snapMode.type == 'bounding' ? 'active' : ''"><span class="title-menu"><?php esc_html_e('Bounding','web-to-print-online-designer'); ?></span></li>
                            <li ng-click="settings.snapMode.type = 'grid'; $event.stopPropagation();" ng-class="settings.snapMode.type == 'grid' ? 'active' : ''"><span class="title-menu"><?php esc_html_e('Grid','web-to-print-online-designer'); ?></span></li>
                        </ul>
                    </div>
                </li>-->
                <li class="sub-menu-item flex space-between hover-menu" data-animate="bottom-to-top">
                    <span class="title-menu"><?php esc_html_e('Show warning','web-to-print-online-designer'); ?></span>
                    <i class="icon-nbd icon-nbd-arrow-drop-down rotate-90"></i>
                    <div class="hover-sub-menu-item">
                        <ul>
                            <li ng-click="settings.showWarning.oos = !settings.showWarning.oos" ng-class="settings.showWarning.oos ? 'active' : ''"><span class="title-menu"><?php esc_html_e('Out of stage','web-to-print-online-designer'); ?></span></li>
                            <li ng-click="settings.showWarning.ilr = !settings.showWarning.ilr" ng-class="settings.showWarning.ilr ? 'active' : ''"><span class="title-menu"><?php esc_html_e('Image low resolution','web-to-print-online-designer'); ?></span></li>
                        </ul>
                    </div>
                </li>
            </ul>
            <div id="nbd-overlay"></div>
        </li>
        <?php if( $show_nbo_option && $settings['nbdesigner_display_product_option'] == '1' && !(isset( $_GET['src'] ) && $_GET['src'] == 'studio') ): ?>
        <li class="menu-item item-nbo-options" ng-click="getPrintingOptions()">
            <span><?php esc_html_e('Options','web-to-print-online-designer'); ?></span>
        </li>
        <?php endif; ?> 
        <li class="menu-item tour_start" ng-if="!settings.is_mobile" ng-click="startTourGuide()">
            <span class="nbd-tooltip-hover-right" title="<?php esc_html_e('Quick Help','web-to-print-online-designer'); ?>">?</span>
        </li>
        <?php do_action('nbd_modern_extra_menu'); ?>
    </ul>
    <ul class="nbd-main-menu menu-center">
        <li class="menu-item undo-redo" ng-click="undo()" ng-class="stages[currentStage].states.isUndoable ? 'in' : 'nbd-disabled'">
            <i class="icon-nbd-baseline-undo" ></i>
            <span class="nbd-font-size-12"><?php esc_html_e('Undo','web-to-print-online-designer'); ?></span>
        </li>
        <li class="menu-item undo-redo" ng-click="redo()" ng-class="stages[currentStage].states.isRedoable ? 'in' : 'nbd-disabled'">
            <i class="icon-nbd-baseline-redo" ></i>
            <span class="nbd-font-size-12"><?php esc_html_e('Redo','web-to-print-online-designer'); ?></span>
        </li>
    </ul>
    <ul class="nbd-main-menu menu-right">
        <li class="menu-item item-title animated slideInDown animate600 ipad-mini-hidden">
            <input type="text" name="title" class="title" placeholder="Title" ng-model="stages[currentStage].config.name"/>
        </li>
        <?php if( $enable_sticker_preview ): ?>
        <li title="<?php esc_html_e('Sticker cutline preview','web-to-print-online-designer'); ?>" ng-click="generateStickerCutline()" class="menu-item nbd-show-3d-preview nbd-change-product animated slideInDown animate700 main-menu-action" >
            <i class="nbd-svg-icon" style="margin-left: 0;" >
                <svg version="1.1" height="24" width="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" ><path d="M511.653,195.545h0.018c-11.444-45.606-34.042-86.166-67.982-119.27v-0.097C394.902,27.098,329.298,0.021,259.891,0.021 c-69.406,0-134.689,27.005-183.769,76.086C27.043,125.186,0,190.427,0,259.834s27.474,134.654,76.554,183.735 c33.107,33.106,74.409,56.647,120.015,68.089v-0.017c0,0.22,1.288,0.338,2.158,0.338c2.776,0,5.26-1.09,7.275-3.107L509,205.71 C511.647,203.062,512.575,198.727,511.653,195.545z M179.969,439.104l-0.021-0.051c-21.899-9.701-41.643-23.277-58.851-40.485 c-76.502-76.501-76.502-200.981,0-277.483c38.255-38.256,88.49-57.377,138.742-57.377c50.239,0,100.495,19.13,138.741,57.377 c17.209,17.209,30.783,36.953,40.485,58.852l0.072,0.021c-68.393,0.168-134.341,27.402-183.049,76.118 C207.375,304.779,180.142,370.728,179.969,439.104z M204.581,480.067c-1.461-8.557-2.461-17.209-2.983-25.878 c-4.137-68.081,21.19-134.824,69.491-183.115c44.875-44.884,105.674-69.928,168.688-69.928c4.797,0,9.615,0.145,14.433,0.439 c8.665,0.523,17.315,1.522,25.873,2.984L204.581,480.067z M462.426,180.93c-10.888-28.139-27.292-53.294-48.845-74.847 c-84.775-84.774-222.71-84.771-307.483,0c-84.772,84.773-84.772,222.709,0,307.482c21.554,21.552,46.709,37.957,74.848,48.846 c0.695,7.955,1.75,15.883,3.17,23.732c-34.877-11.654-66.769-31.329-93.02-57.582C46.021,383.49,21.198,323.564,21.198,259.822 S46.021,136.155,91.094,91.081c45.074-45.074,105-69.897,168.741-69.897c63.742,0,123.669,24.823,168.742,69.896 c26.251,26.251,45.926,58.144,57.582,93.02C478.308,182.679,470.38,181.624,462.426,180.93z"/></svg>
            </i>
        </li>
        <?php endif; ?>
        <?php if( $enable_3d_preview ): ?>
        <li ng-click="show3DPreview()" class="menu-item nbd-show-3d-preview nbd-change-product animated slideInDown animate700 main-menu-action" >
            <i class="nbd-svg-icon" style="margin-left: 0;" >
                <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M7.52 21.48C4.25 19.94 1.91 16.76 1.55 13H.05C.56 19.16 5.71 24 12 24l.66-.03-3.81-3.81-1.33 1.32zm.89-6.52c-.19 0-.37-.03-.52-.08-.16-.06-.29-.13-.4-.24-.11-.1-.2-.22-.26-.37-.06-.14-.09-.3-.09-.47h-1.3c0 .36.07.68.21.95.14.27.33.5.56.69.24.18.51.32.82.41.3.1.62.15.96.15.37 0 .72-.05 1.03-.15.32-.1.6-.25.83-.44s.42-.43.55-.72c.13-.29.2-.61.2-.97 0-.19-.02-.38-.07-.56-.05-.18-.12-.35-.23-.51-.1-.16-.24-.3-.4-.43-.17-.13-.37-.23-.61-.31.2-.09.37-.2.52-.33.15-.13.27-.27.37-.42.1-.15.17-.3.22-.46.05-.16.07-.32.07-.48 0-.36-.06-.68-.18-.96-.12-.28-.29-.51-.51-.69-.2-.19-.47-.33-.77-.43C9.1 8.05 8.76 8 8.39 8c-.36 0-.69.05-1 .16-.3.11-.57.26-.79.45-.21.19-.38.41-.51.67-.12.26-.18.54-.18.85h1.3c0-.17.03-.32.09-.45s.14-.25.25-.34c.11-.09.23-.17.38-.22.15-.05.3-.08.48-.08.4 0 .7.1.89.31.19.2.29.49.29.86 0 .18-.03.34-.08.49-.05.15-.14.27-.25.37-.11.1-.25.18-.41.24-.16.06-.36.09-.58.09H7.5v1.03h.77c.22 0 .42.02.6.07s.33.13.45.23c.12.11.22.24.29.4.07.16.1.35.1.57 0 .41-.12.72-.35.93-.23.23-.55.33-.95.33zm8.55-5.92c-.32-.33-.7-.59-1.14-.77-.43-.18-.92-.27-1.46-.27H12v8h2.3c.55 0 1.06-.09 1.51-.27.45-.18.84-.43 1.16-.76.32-.33.57-.73.74-1.19.17-.47.26-.99.26-1.57v-.4c0-.58-.09-1.1-.26-1.57-.18-.47-.43-.87-.75-1.2zm-.39 3.16c0 .42-.05.79-.14 1.13-.1.33-.24.62-.43.85-.19.23-.43.41-.71.53-.29.12-.62.18-.99.18h-.91V9.12h.97c.72 0 1.27.23 1.64.69.38.46.57 1.12.57 1.99v.4zM12 0l-.66.03 3.81 3.81 1.33-1.33c3.27 1.55 5.61 4.72 5.96 8.48h1.5C23.44 4.84 18.29 0 12 0z"/></svg>
            </i>
        </li>
        <?php endif; ?>
        <?php if( $task == 'new' && !wp_is_mobile() && $ui_mode == '2' ): ?>
        <li ng-if="settings.nbdesigner_show_button_change_product == 'yes'" ng-click="showProducts()" class="menu-item nbd-change-product nbd-show-popup-products animated slideInDown animate700 main-menu-action" >
            <?php esc_html_e('Change Product','web-to-print-online-designer'); ?> 
            <i class="nbd-svg-icon" >
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 6v3l4-4-4-4v3c-4.42 0-8 3.58-8 8 0 1.57.46 3.03 1.24 4.26L6.7 14.8c-.45-.83-.7-1.79-.7-2.8 0-3.31 2.69-6 6-6zm6.76 1.74L17.3 9.2c.44.84.7 1.79.7 2.8 0 3.31-2.69 6-6 6v-3l-4 4 4 4v-3c4.42 0 8-3.58 8-8 0-1.57-.46-3.03-1.24-4.26z"/></svg>
            </i>
        </li>
        <?php endif; ?>
        <li ng-if="settings.nbdesigner_share_design == 'yes'" class="menu-item item-share nbd-show-popup-share animated slideInDown animate800 main-menu-action" ng-click="saveData('share')"><i class="icon-nbd icon-nbd-share2"></i></li>
        <?php if( $task == 'create_template' ): ?>
        <li class="menu-item item-process animated slideInDown animate900" id="save-template" ng-click="loadTemplateCat()">
            <span><?php esc_html_e('Save Template','web-to-print-online-designer'); ?></span><i class="icon-nbd icon-nbd-arrow-upward rotate90"></i>
        </li>
        <?php elseif( $show_nbo_option && ($settings['nbdesigner_display_product_option'] == '1' || wp_is_mobile() ) && isset( $_GET['src'] ) && $_GET['src'] == 'studio' ): ?>
        <li class="menu-item item-process animated slideInDown animate900" id="save-template" ng-click="getPrintingOptions()">
            <span><?php esc_html_e('Process','web-to-print-online-designer'); ?></span><i class="icon-nbd icon-nbd-arrow-upward rotate90"></i>
        </li>
        <?php 
            else: 
            $process_action = 'saveData()';
            if( $task == 'create' || ( $task == 'edit' && ( isset( $_GET['design_type'] ) && $_GET['design_type'] == 'template' ) ) ){
                $process_action = 'prepareSaveTemplate()';
            }
            $process_action = apply_filters('nbd_editor_process_action', $process_action);
        ?>
        <li ng-class="printingOptionsAvailable ? '' : 'nbd-disabled'" class="menu-item item-process animated slideInDown animate900 save-data" data-overlay="overlay" 
            ng-click="<?php echo $process_action; ?>" 
            data-tour="process" data-tour-priority="7">
            <span><?php esc_html_e('Process','web-to-print-online-designer'); ?></span><i class="icon-nbd icon-nbd-arrow-upward rotate90"></i>
        </li>
        <?php endif; ?>
    </ul>
</div>