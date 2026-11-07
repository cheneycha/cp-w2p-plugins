<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  
$layout = nbdesigner_get_option('nbdesigner_design_layout');
?>
<div id="nbdesigner-setting-container">
    <?php wp_nonce_field('nbdesigner_setting_box', 'nbdesigner_setting_box_nonce'); ?>
    <div>
        <?php 
            $license = nbd_get_license_key();
            if( $license['key'] == '' ):
        ?>
        <div>
            <p><span class="nbd-admin-setting-lite-version-warning"><?php esc_html_e('All your configurations has not been saved. Please activate the license key, ', 'web-to-print-online-designer'); ?></span> <a target="_blank" href="<?php echo esc_url(admin_url('admin.php?page=nbdesigner&tab=general')); ?> "><?php esc_html_e('here', 'web-to-print-online-designer'); ?></a></p>
        </div>
        <?php endif; ?>
        <p class="nbd-option-top">
            <input type="hidden" value="0" name="_nbdesigner_enable"/>
            <label for="_nbdesigner_enable"><?php esc_html_e('Enable Design', 'web-to-print-online-designer'); ?></label>
            <input type="checkbox" value="1" name="_nbdesigner_enable" id="_nbdesigner_enable" <?php checked($enable); ?> class="short" />
        </p>
        <p class="nbd-option-top <?php if (!$enable) echo 'nbdesigner-disable'; ?>" id="nbd_upload_status">
            <input type="hidden" value="0" name="_nbdesigner_enable_upload"/>
            <label for="_nbdesigner_enable_upload"><?php esc_html_e('Enable Upload Design', 'web-to-print-online-designer'); ?></label>
            <input type="checkbox" value="1" name="_nbdesigner_enable_upload" id="_nbdesigner_enable_upload" <?php checked($enable_upload); ?> class="short" /> 
        </p>
        <p class="nbd-option-top <?php if (! ($enable && $enable_upload ) ) echo 'nbdesigner-disable'; ?>" id="nbd_upload_without_design_status">
            <input type="hidden" value="0" name="_nbdesigner_enable_upload_without_design"/>
            <label for="_nbdesigner_upload_without_design"><?php esc_html_e('Enable Upload Without Design', 'web-to-print-online-designer'); ?></label>
            <input type="checkbox" value="1" name="_nbdesigner_enable_upload_without_design" id="_nbdesigner_upload_without_design" <?php checked($upload_without_design); ?> class="short" /> 
        </p>
    </div>
    <div id="nbd-setting-container" class="<?php if (!$enable) echo 'nbdesigner-disable'; ?>">
        <ul class="nbd-tabs">
            <li class="nbd-tabber selected nbd-design" data-target="#nbd-custom-design">
                <span class="dashicons dashicons-art"></span>
                <?php esc_html_e('Cutom Design', 'web-to-print-online-designer'); ?>
            </li>
            <li class="nbd-tabber nbd-upload <?php if ( !$enable_upload ) echo 'nbdesigner-disable'; ?>" data-target="#nbd-upload-design">
                <span class="dashicons dashicons-upload"></span>
                <?php esc_html_e( 'Upload Design', 'web-to-print-online-designer' ); ?>
            </li>
        </ul>
        <div id="nbd-custom-design" class="nbd-options-tab selected">
            <?php 
                $max_file_uploads   = function_exists( 'ini_get' ) ? ini_get( 'max_file_uploads' ) : 9999;
                $min_num_files      = count( $designer_setting ) * 2 + 6;
            ?>
            <div class="nbd_max_file_uploads_warning <?php if( $max_file_uploads > $min_num_files ) echo 'nbdesigner-disable'; ?>">
                <h3 style="color: #ff4136;">
                    <span style="vertical-align: bottom; margin-top: 0;" class="dashicons dashicons-warning"></span>
                    <?php esc_html_e('Warning', 'web-to-print-online-designer'); ?>
                </h3>
                <div class="nbd_max_file_uploads_warning_content">
                    <p><?php esc_html_e('PHP max_file_uploads:', 'web-to-print-online-designer'); ?> <?php echo $max_file_uploads; ?></p>
                    <p><?php esc_html_e('Please increase PHP max_file_uploads to be saved design!', 'web-to-print-online-designer'); ?></p>
                </div>
            </div>
            <div class="nbdesigner-right add_more" style="display: none;">
                <a class="button button-primary" onclick="NBDESIGNADMIN.addOrientation('com')"><?php esc_html_e('Add More', 'web-to-print-online-designer'); ?></a>
                <a class="button button-primary" onclick="NBDESIGNADMIN.collapseAll('com')"><?php esc_html_e('Collapse All', 'web-to-print-online-designer'); ?></a>
            </div>
            <div class="nbdesigner-clearfix"></div>
            <div id="nbdesigner-boxes" class="nbdesigner-boxes">
                <?php $count = 0;
                foreach ( $designer_setting as $k => $v ): ?>
                    <div class="nbdesigner-box-container">
                        <div class="nbdesigner-box">
                            <label class="nbdesigner-setting-box-label"><?php esc_html_e('Name', 'web-to-print-online-designer'); ?></label>
                            <div class="nbdesigner-setting-box-value">
                                <input name="_designer_setting[<?php echo( $k ); ?>][orientation_name]" class="short orientation_name" 
                                       value="<?php echo( $v['orientation_name'] ); ?>" type="text" required/>
                            </div>
                            <div class="nbdesigner-right">
                                <a class="button nbdesigner-collapse" onclick="NBDESIGNADMIN.collapseBox(this)"><span class="dashicons dashicons-arrow-up"></span><?php esc_html_e('Less setting', 'web-to-print-online-designer'); ?></a>
                                <a class="button nbdesigner-delete delete_orientation" data-index="<?php echo( $k ); ?>" data-variation="com" onclick="NBDESIGNADMIN.deleteOrientation(this)">&times;</a>
                            </div>
                        </div>
                        <div class="nbdesigner-box nbdesigner-box-collapse" data-variation="com">
                            <div class="nbdesigner-image-box">
                                <div class="nbdesigner-image-inner">
                                    <?php 
                                        if($v['product_width'] >= $v['product_height']){
                                            $ratio          = 500 / $v['product_width'];
                                            $style_width    = 500;
                                            $style_height   = round($v['product_height'] * $ratio);
                                            $style_left     = 0;
                                            $style_top      = round((500 - $style_height) / 2);
                                            $left           = 0;
                                            $top            = ( $v['product_width'] - $v['product_height']) / 2;
                                        } else {
                                            $ratio          = 500 / $v['product_height'];
                                            $style_height   = 500;
                                            $style_width    = round($v['product_width'] * $ratio);
                                            $style_top      = 0;
                                            $style_left     = round((500 - $style_width) / 2);
                                            $top            = 0;
                                            $left           = ( $v['product_height'] - $v['product_width']) / 2;
                                        }
                                    ?>
                                    <div class="nbdesigner-image-original <?php if($v['bg_type'] == 'tran') echo "background-transparent"; ?>"
                                        style="width: <?php echo( $style_width ); ?>px;
                                               height: <?php echo( $style_height ); ?>px;
                                               left: <?php echo( $style_left ); ?>px;
                                               top: <?php echo( $style_top ); ?>px;
                                        <?php if($v['bg_type'] == 'color') echo 'background: ' .$v['bg_color_value']?>"
                                    >
                                        <?php
                                            $img_src = is_numeric( $v['img_src'] ) ? wp_get_attachment_url( $v['img_src'] ) : $v['img_src'];
                                        ?>
                                        <img src="<?php echo esc_url( $img_src ); ?>" 
                                            <?php if($v['bg_type'] != 'image') echo ' style="display: none;"' ?>
                                             class="designer_img_src "
                                            />
                                    </div>
                                    <?php $overlay_style = 'none'; if($v['show_overlay']) $overlay_style = 'block'; ?>
                                    <div class="nbdesigner-image-overlay"
                                        style="width: <?php echo( $style_width ); ?>px;
                                               height: <?php echo( $style_height ); ?>px;
                                               left: <?php echo( $style_left ); ?>px;
                                               top: <?php echo( $style_top ); ?>px;
                                               display: <?php echo( $overlay_style ); ?>"
                                    >
                                        <?php
                                            $img_overlay = is_numeric( $v['img_overlay'] ) ? wp_get_attachment_url( $v['img_overlay'] ) : $v['img_overlay'];
                                        ?>
                                        <img src="<?php echo esc_url( $img_overlay ); ?>" class="img_overlay"/>
                                    </div>
                                    <div class="nbd-bleed <?php if (!$v['show_bleed']) echo 'nbdesigner-disable'; ?> <?php if( $v['area_design_type'] == 2 ) echo 'nbd-rounded' ?>"
                                        style="width: <?php echo round( $ratio * ($v['real_width'] - 2 * (float) $v['bleed_left_right']))  ?>px;
                                                height: <?php echo round( $ratio * ($v['real_height'] - 2 * (float) $v['bleed_top_bottom']))  ?>px;
                                                top: <?php echo round( $ratio * ($top + $v['real_top'] + (float) $v['bleed_top_bottom']))  ?>px;
                                                left: <?php echo round( $ratio * ($left + $v['real_left'] + (float)  $v['bleed_left_right']))  ?>px;"> 
                                    </div>
                                    <div class="nbd-safe-zone <?php if (!$v['show_safe_zone']) echo 'nbdesigner-disable'; ?> <?php if( $v['area_design_type'] == 2 ) echo 'nbd-rounded' ?>"
                                        style="width: <?php echo round( $ratio * ($v['real_width'] - 2 * (float)$v['bleed_left_right'] - 2 * (float) $v['margin_left_right']))  ?>px;
                                                height: <?php echo round( $ratio * ($v['real_height'] - 2 * (float) $v['bleed_top_bottom'] - 2 * (float) $v['margin_top_bottom']))  ?>px;
                                                top: <?php echo round( $ratio * ($top + $v['real_top'] + (float) $v['bleed_top_bottom'] + (float) $v['margin_top_bottom']))  ?>px;
                                                left: <?php echo round( $ratio * ($left + $v['real_left'] + (float) $v['bleed_left_right'] +  (float) $v['margin_left_right']))  ?>px;">
                                    </div>
                                    <div class="nbdesigner-area-design <?php if( $v['area_design_type'] == 2 ) echo 'nbd-rounded' ?>" id="nbdesigner-area-design-<?php echo( $k ); ?>" 
                                         style="width: <?php echo( $v['area_design_width'] . 'px' ); ?>; 
                                                height: <?php echo( $v['area_design_height'] . 'px' ); ?>; 
                                                left: <?php echo( $v['area_design_left'] . 'px' ); ?>; 
                                                top: <?php echo( $v['area_design_top'] . 'px' ); ?>;"> </div>
                                </div>
                                <input type="hidden" class="hidden_img_src" name="_designer_setting[<?php echo( $k ); ?>][img_src]" value="<?php echo( $v['img_src'] ); ?>" >
                                <input type="hidden" class="hidden_img_src_top" name="_designer_setting[<?php echo( $k ); ?>][img_src_top]" value="<?php echo( $v['img_src_top'] ); ?>">
                                <input type="hidden" class="hidden_img_src_left" name="_designer_setting[<?php echo( $k ); ?>][img_src_left]" value="<?php echo( $v['img_src_left'] ); ?>">
                                <input type="hidden" class="hidden_img_src_width" name="_designer_setting[<?php echo( $k ); ?>][img_src_width]" value="<?php echo( $v['img_src_width'] ); ?>">
                                <input type="hidden" class="hidden_img_src_height" name="_designer_setting[<?php echo( $k ); ?>][img_src_height]" value="<?php echo( $v['img_src_height'] ); ?>">
                                <input type="hidden" class="hidden_overlay_src" name="_designer_setting[<?php echo( $k ); ?>][img_overlay]" value="<?php echo( $v['img_overlay'] ); ?>">
                                <input type="hidden" class="hidden_nbd_version" name="_designer_setting[<?php echo( $k ); ?>][version]" value="<?php echo( $v['version'] ); ?>">
                                <input type="hidden" class="hidden_nbd_ratio" name="_designer_setting[<?php echo( $k ); ?>][ratio]" value="<?php echo( $ratio ); ?>">
                                <div>   
                                    <a class="button nbdesigner_move nbdesigner_move_left" data-index="<?php echo( $k ); ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'left')">&larr;</a>
                                    <a class="button nbdesigner_move nbdesigner_move_right" data-index="<?php echo( $k ); ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'right')">&rarr;</a>
                                    <a class="button nbdesigner_move nbdesigner_move_up" data-index="<?php echo( $k ); ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'up')">&uarr;</a>
                                    <a class="button nbdesigner_move nbdesigner_move_down" data-index="<?php echo( $k ); ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'down')">&darr;</a>
                                    <a class="button nbdesigner_move nbdesigner_move_center" data-index="<?php echo( $k ); ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'center')">&frac12;</a>
                                    <a class="button nbdesigner_move nbdesigner_move_center nbd-admin-setting-fit-btn" data-index="<?php echo( $k ); ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'fit')"><i class="mce-ico mce-i-dfw nbd-admin-setting-fit-icon" ></i></a>
                                </div>
                                <div class="nbb-background-group">
                                    <div>
                                        <p>
                                            <label for="nbdesigner_bg_type" class="nbd-label nbdesigner-setting-box-label"><?php esc_html_e('Background type'); ?></label>
                                            <label class="nbdesigner-lbl-setting"><input type="radio" name="_designer_setting[<?php echo( $k ); ?>][bg_type]" value="image" 
                                                <?php checked($v['bg_type'], 'image', true); ?> class="bg_type"
                                                onclick="NBDESIGNADMIN.change_background_type(this)"   /><?php esc_html_e('Image', 'web-to-print-online-designer'); ?></label>
                                            <label class="nbdesigner-lbl-setting"><input type="radio" name="_designer_setting[<?php echo( $k ); ?>][bg_type]" value="color" 
                                                <?php checked($v['bg_type'], 'color', true); ?> class="bg_type"
                                                onclick="NBDESIGNADMIN.change_background_type(this)"   /><?php esc_html_e('Color', 'web-to-print-online-designer'); ?></label>
                                            <label class="nbdesigner-lbl-setting"><input type="radio" name="_designer_setting[<?php echo( $k ); ?>][bg_type]" value="tran" 
                                                <?php checked($v['bg_type'], 'tran', true); ?> class="bg_type"
                                                onclick="NBDESIGNADMIN.change_background_type(this)"   /><?php esc_html_e('Transparent', 'web-to-print-online-designer'); ?></label>
                                        </p>
                                    </div> 
                                    <div class="nbdesigner_bg_image" <?php if($v['bg_type'] != 'image') echo ' style="display: none;"' ?>>
                                        <a class="button nbdesigner-button nbdesigner-add-image" onclick="NBDESIGNADMIN.loadImage(this)" data-index="<?php echo( $k ); ?>"><?php esc_html_e('Set image', 'web-to-print-online-designer'); ?></a>
                                        <?php 
                                            if( is_available_imagick() ): 
                                                $origin_bg_pdf = isset( $v['origin_bg_pdf'] ) ? $v['origin_bg_pdf'] : '';
                                        ?>
                                            <span><?php esc_html_e(' - or - ', 'web-to-print-online-designer'); ?></span>
                                            <input type="file" class="nbdesigner-add-image nbdesigner-add-pdf-input" accept=".pdf" />
                                            <input type="hidden" class="nbd_origin_bg_pdf" value="<?php echo( $v['origin_bg_pdf'] ); ?>" name="_designer_setting[<?php echo( $k ); ?>][origin_bg_pdf]" />
                                            <a class="button nbdesigner-button nbdesigner-add-image" onclick="NBDESIGNADMIN.uploadPdfAsBackground(this)" data-index="<?php echo( $k ); ?>"><?php esc_html_e('Set PDF as background image', 'web-to-print-online-designer'); ?></a>
                                            <img class="nbd-upload-pdf-loading" src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" />
                                        <?php endif; ?>
                                    </div>
                                    <div class="nbdesigner_bg_color" <?php if($v['bg_type'] != 'color') echo ' style="display: none;"' ?>>
                                        <input type="text" name="_designer_setting[<?php echo( $k ); ?>][bg_color_value]" value="<?php echo( $v['bg_color_value'] ); ?>" class="nbd-color-picker" />
                                    </div>
                                </div>
                                <div class="nbdesigner-clearfix"></div>
                                <?php $include_background = isset( $v['include_background'] ) ? $v['include_background'] : 1; ?>
                                <div class="nbd-admin-setting-include-background-wrap">
                                    <label class="nbd-label nbdesigner-setting-box-label"><?php  esc_html_e('Include background image', 'web-to-print-online-designer'); ?></label>
                                    <input class="include_background" type="hidden" value="0" name="_designer_setting[<?php echo( $k ); ?>][include_background]"/>
                                    <input class="include_background" type="checkbox" value="1" name="_designer_setting[<?php echo( $k ); ?>][include_background]"  <?php checked($include_background); ?>/>
                                    &nbsp;<small><?php esc_html_e('Include background image in final design if background type is image.', 'web-to-print-online-designer'); ?></small>
                                </div>
                                <div class="nbdesigner-clearfix"></div>
                                <div class="nbdesigner_overlay_box nbd-admin-setting-text-align-left" >
                                    <label class="nbd-label nbdesigner-setting-box-label"><?php  esc_html_e('Overlay', 'web-to-print-online-designer'); ?></label>
                                    <input type="hidden" value="0" name="_designer_setting[<?php echo( $k ); ?>][show_overlay]" class="show_overlay"/>
                                    <input type="checkbox" value="1" 
                                        name="_designer_setting[<?php echo( $k ); ?>][show_overlay]" id="_designer_setting[<?php echo( $k ); ?>][show_overlay]" <?php checked($v['show_overlay']); ?> 
                                        class="show_overlay" onchange="NBDESIGNADMIN.toggleShowOverlay(this)"/>  
                                    <a class="button overlay-toggle" onclick="NBDESIGNADMIN.loadImageOverlay(this)" style="display: <?php if($v['show_overlay']) {echo 'inline-block';} else {echo 'none';} ?>">
                                        <?php esc_html_e('Set image', 'web-to-print-online-designer'); ?>
                                    </a>
                                    <img style="display: <?php if($v['show_overlay']) {echo 'inline-block';} else {echo 'none';} ?>"
                                         src="<?php if ($v['img_overlay'] != '') {echo esc_url( $img_overlay );} else {echo NBDESIGNER_PLUGIN_URL . 'assets/images/overlay.png';} ?>" class="img_overlay"/>                            
                                    <p class="overlay-toggle" style="display: <?php if($v['show_overlay']) {echo 'block';} else {echo 'none';} ?>">
                                        <input type="hidden" value="0" name="_designer_setting[<?php echo( $k ); ?>][include_overlay]" class="include_overlay"/> 
                                        <input type="checkbox" value="1"  class="include_overlay"
                                            name="_designer_setting[<?php echo( $k ); ?>][include_overlay]"  <?php checked($v['include_overlay']); ?>   
                                            />
                                        <span><?php  esc_html_e('Include in final design', 'web-to-print-online-designer'); ?></span>
                                    </p>
                                </div>
                                <div class="nbd_area_design_type">
                                    <label class="nbd-label nbdesigner-setting-box-label"><?php  esc_html_e('Area design shape', 'web-to-print-online-designer'); ?></label>
                                    <label class="nbdesigner-lbl-setting"><input type="radio" name="_designer_setting[<?php echo( $k ); ?>][area_design_type]" value="1" 
                                        <?php checked($v['area_design_type'], 1, true); ?> class="bg_type"
                                        onclick="NBDESIGNADMIN.changeAreaDesignShape(this, 1)"   /><?php esc_html_e('Rectangle', 'web-to-print-online-designer'); ?> </label>
                                    <label class="nbdesigner-lbl-setting"><input type="radio" name="_designer_setting[<?php echo( $k ); ?>][area_design_type]" value="2" 
                                        <?php checked($v['area_design_type'], 2, true); ?> class="bg_type"
                                        onclick="NBDESIGNADMIN.changeAreaDesignShape(this, 2)"   /><?php esc_html_e('Circle/Ellipse', 'web-to-print-online-designer'); ?></label>                                    
                                </div>
                            </div>
                            <div class="nbdesigner-info-box">
                                <?php if($k ==0): ?>
                                <p class="nbd-admin-setting-no-margin">
                                    <small class="nbd-helper"><?php esc_html_e('(Click', 'web-to-print-online-designer'); ?>  <span class="dashicons dashicons-editor-help"></span><?php esc_html_e('to know how to setting product design)', 'web-to-print-online-designer'); ?></small><br />
                                    <span class="nbd-admin-setting-product-area-title"></span>&nbsp;<?php esc_html_e('Product area', 'web-to-print-online-designer'); ?>&nbsp;
                                    <span class="nbd-admin-setting-design-area-title"></span>&nbsp;<?php esc_html_e('Design area', 'web-to-print-online-designer'); ?><br />
                                    <span class="nbd-admin-setting-bounding-box-title"></span>&nbsp;<?php esc_html_e('Bounding box', 'web-to-print-online-designer'); ?><small> (<?php esc_html_e('product always align vertical/horizontal center bounding box', 'web-to-print-online-designer'); ?>)</small>
                                </p>
                                <?php endif; ?>
                                <p class="nbd-setting-section-title">
                                    <?php esc_html_e('Product size', 'web-to-print-online-designer'); ?>
                                    <?php if($k ==0): ?>
                                    <span class="nbdesign-config-size-tooltip dashicons dashicons-editor-help nbd-helper"></span>
                                    <?php endif; ?>
                                </p>
                                <div class="nbdesigner-info-box-inner notice-width nbd-has-notice">
                                    <label class="nbdesigner-setting-box-label"><?php esc_html_e('Width', 'web-to-print-online-designer'); ?><br /><small>(W<sub>p</sub>)</small></label>
                                    <div>
                                        <input type="number" step="any" min="0" name="_designer_setting[<?php echo( $k ); ?>][product_width]" 
                                               value="<?php echo( $v['product_width'] ); ?>" class="short product_width" 
                                               onchange="NBDESIGNADMIN.change_dimension_product(this, 'width')"> <span class="nbd-unit"><?php echo( $unit ); ?></span>
                                    </div>
                                </div>
                                <div class="nbdesigner-info-box-inner notice-height nbd-has-notice">
                                    <label class="nbdesigner-setting-box-label"><?php esc_html_e('Height', 'web-to-print-online-designer'); ?><br /><small>(H<sub>p</sub>)</small></label>
                                    <div>
                                        <input type="number" step="any" min="0" name="_designer_setting[<?php echo( $k ); ?>][product_height]" 
                                               value="<?php echo( $v['product_height'] ); ?>" class="short product_height"  
                                               onchange="NBDESIGNADMIN.change_dimension_product(this, 'height')"> <span class="nbd-unit"><?php echo( $unit ); ?></span> 
                                    </div>
                                </div> 
                                <p class="nbd-setting-section-title">
                                    <?php esc_html_e('Design area size', 'web-to-print-online-designer'); ?>
                                    <?php if($k ==0): ?>
                                    <span class="nbdesign-config-realsize-tooltip dashicons dashicons-editor-help nbd-helper"></span>
                                    <?php endif; ?>
                                </p>
                                <div class="nbdesigner-info-box-inner notice-width nbd-has-notice">
                                    <label class="nbdesigner-setting-box-label"><?php esc_html_e('Width', 'web-to-print-online-designer'); ?><br /><small>(W<sub>d</sub>)</small></label>
                                    <div>
                                        <input type="number" step="any" name="_designer_setting[<?php echo( $k ); ?>][real_width]" value="<?php echo( $v['real_width'] ); ?>" class="short real_width" 
                                               onchange="NBDESIGNADMIN.updateRelativePosition(this, 'width')"> <span class="nbd-unit"><?php echo( $unit ); ?></span> 
                                    </div>
                                </div>
                                <div class="nbdesigner-info-box-inner notice-height nbd-has-notice">
                                    <label class="nbdesigner-setting-box-label"><?php esc_html_e('Height', 'web-to-print-online-designer'); ?><br /><small>(H<sub>d</sub>)</small></label>
                                    <div>
                                        <input type="number" step="any" min="0" name="_designer_setting[<?php echo( $k ); ?>][real_height]" value="<?php echo( $v['real_height'] ); ?>" class="short real_height"  
                                               onchange="NBDESIGNADMIN.updateRelativePosition(this, 'height')"> <span class="nbd-unit"><?php echo( $unit ); ?></span> 
                                    </div>
                                </div>   
                                <div class="nbdesigner-info-box-inner notice-height nbd-has-notice">
                                    <label class="nbdesigner-setting-box-label"><?php esc_html_e('Top', 'web-to-print-online-designer'); ?><br /><small>(T<sub>d</sub>)</small></label>
                                    <div>
                                        <input type="number" step="any" min="0" name="_designer_setting[<?php echo( $k ); ?>][real_top]" value="<?php echo( $v['real_top'] ); ?>" class="short real_top"  
                                               onchange="NBDESIGNADMIN.updateRelativePosition(this, 'top')"> <span class="nbd-unit"><?php echo( $unit ); ?></span> 
                                    </div>
                                </div> 
                                <div class="nbdesigner-info-box-inner">
                                    <label class="nbdesigner-setting-box-label notice-width nbd-has-notice"><?php esc_html_e('Left', 'web-to-print-online-designer'); ?><br /><small>(L<sub>d</sub>)</small></label>
                                    <div>
                                        <input type="number" step="any" min="0" name="_designer_setting[<?php echo( $k ); ?>][real_left]" value="<?php echo( $v['real_left'] ); ?>" class="short real_left"  
                                               onchange="NBDESIGNADMIN.updateRelativePosition(this, 'left')"> <span class="nbd-unit"><?php echo( $unit ); ?></span> 
                                    </div>
                                </div>
                                <p class="nbd-setting-section-title">
                                    <?php esc_html_e('Relative position', 'web-to-print-online-designer'); ?>&nbsp;
                                    <?php if($k == 0): ?> 
                                    <span class="nbdesign-config-tooltip dashicons dashicons-editor-help nbd-helper"></span>
                                    <?php endif; ?>
                                    <span class="dashicons dashicons-update nbdesiger-update-area-design" onclick="NBDESIGNADMIN.updateDesignAreaSize(this)"></span>
                                </p>
                                <div class="nbdesigner-info-box-inner">
                                    <label class="nbdesigner-setting-box-label"><?php esc_html_e('Width', 'web-to-print-online-designer'); ?></label>
                                    <div>
                                        <input type="number" step="any"  min="0" name="_designer_setting[<?php echo( $k ); ?>][area_design_width]" 
                                               value="<?php echo( $v['area_design_width'] ); ?>" class="short area_design_dimension area_design_width" data-index="width" 
                                               onchange="NBDESIGNADMIN.updatePositionDesignArea(this)">&nbsp;px
                                    </div>
                                </div>
                                <div class="nbdesigner-info-box-inner">
                                    <label class="nbdesigner-setting-box-label"><?php esc_html_e('Height', 'web-to-print-online-designer'); ?></label>
                                    <div>
                                        <input type="number"  step="any" min="0" name="_designer_setting[<?php echo( $k ); ?>][area_design_height]" value="<?php echo( $v['area_design_height'] ); ?>" class="short area_design_dimension area_design_height" data-index="height" onchange="NBDESIGNADMIN.updatePositionDesignArea(this)">&nbsp;px
                                    </div>
                                </div>  
                                <div class="nbdesigner-info-box-inner">
                                    <label class="nbdesigner-setting-box-label"><?php esc_html_e('Left', 'web-to-print-online-designer'); ?></label>
                                    <div>
                                        <input type="number" step="any"  min="0" name="_designer_setting[<?php echo( $k ); ?>][area_design_left]" value="<?php echo( $v['area_design_left'] ); ?>" class="short area_design_dimension area_design_left" data-index="left" onchange="NBDESIGNADMIN.updatePositionDesignArea(this)">&nbsp;px
                                    </div>
                                </div>
                                <div class="nbdesigner-info-box-inner">
                                    <label class="nbdesigner-setting-box-label"><?php esc_html_e('Top', 'web-to-print-online-designer'); ?></label>
                                    <div>
                                        <input type="number" step="any"  min="0" name="_designer_setting[<?php echo( $k ); ?>][area_design_top]" value="<?php echo( $v['area_design_top'] ); ?>" class="short area_design_dimension area_design_top" data-index="top" onchange="NBDESIGNADMIN.updatePositionDesignArea(this)">&nbsp;px
                                    </div>
                                </div>  
                                <p class="nbd-setting-section-title"><?php esc_html_e('For paper/card', 'web-to-print-online-designer'); ?></p>
                                <div class="nbdesigner-info-box-inner">
                                    <label class="nbdesigner-setting-box-label"><?php esc_html_e('Show cut line', 'web-to-print-online-designer'); ?> <span class="nbd-bleed-notation"></span></label>
                                    <div>
                                        <input type="hidden" value="0" class="show_bleed" name="_designer_setting[<?php echo( $k ); ?>][show_bleed]"/>
                                        <input type="checkbox" value="1" class="show_bleed" name="_designer_setting[<?php echo( $k ); ?>][show_bleed]" <?php checked( $v['show_bleed'] ); ?> class="short nbd-dependence" data-target="#nbd-bleed<?php echo( $k );?>" onchange="NBDESIGNADMIN.toggleBleed(this)"/> 
                                    </div>
                                </div> 
                                <div id="nbd-bleed<?php echo( $k );?>" class="nbd-bleed-con <?php if (!$v['show_bleed']) echo 'nbdesigner-disable'; ?> nbd-independence">
                                    <div class="nbdesigner-info-box-inner">
                                        <label class="nbdesigner-setting-box-label"><?php esc_html_e('Bleed top-bottom', 'web-to-print-online-designer'); ?></label>
                                        <div>
                                            <input type="number" step="any" min="0" name="_designer_setting[<?php echo( $k ); ?>][bleed_top_bottom]" value="<?php echo( $v['bleed_top_bottom'] ); ?>" class="short bleed_top_bottom" onchange="NBDESIGNADMIN.updateBleed(this)">
                                        </div>
                                    </div>
                                    <div class="nbdesigner-info-box-inner">
                                        <label class="nbdesigner-setting-box-label"><?php esc_html_e('Bleed left-right', 'web-to-print-online-designer'); ?></label>
                                        <div>
                                            <input type="number" step="any"  min="0" name="_designer_setting[<?php echo( $k ); ?>][bleed_left_right]" value="<?php echo( $v['bleed_left_right'] ); ?>" class="short bleed_left_right" onchange="NBDESIGNADMIN.updateBleed(this)">
                                        </div>
                                    </div>
                                </div>    
                                <div class="nbdesigner-info-box-inner">
                                    <label class="nbdesigner-setting-box-label"><?php esc_html_e('Show safe zone', 'web-to-print-online-designer'); ?> <span class="nbd-safe-zone-notation"></span></label>
                                    <div>
                                        <input type="hidden" value="0" class="show_safe_zone" name="_designer_setting[<?php echo( $k ); ?>][show_safe_zone]"/>
                                        <input type="checkbox" value="1" class="show_safe_zone" name="_designer_setting[<?php echo( $k ); ?>][show_safe_zone]" <?php checked( $v['show_safe_zone'] ); ?> class="short nbd-dependence" data-target="#nbd-safe-zone<?php echo( $k );?>" onchange="NBDESIGNADMIN.toggleSafeZone(this)"/> 
                                    </div>
                                </div>
                                <div id="nbd-safe-zone<?php echo( $k );?>" class="nbd-safe-zone-con <?php if (!$v['show_safe_zone']) echo 'nbdesigner-disable'; ?> nbd-independence">
                                    <div class="nbdesigner-info-box-inner">
                                        <label class="nbdesigner-setting-box-label"><?php esc_html_e('Magin top-bottom', 'web-to-print-online-designer'); ?></label>
                                        <div>
                                            <input type="number" step="any"  min="0" name="_designer_setting[<?php echo( $k ); ?>][margin_top_bottom]" value="<?php echo( $v['margin_top_bottom'] ); ?>" class="short  margin_top_bottom" onchange="NBDESIGNADMIN.updateSafeZone(this)">
                                        </div>
                                    </div>
                                    <div class="nbdesigner-info-box-inner">
                                        <label class="nbdesigner-setting-box-label"><?php esc_html_e('Magin left-right', 'web-to-print-online-designer'); ?></label>
                                        <div>
                                            <input type="number" step="any"  min="0" name="_designer_setting[<?php echo( $k ); ?>][margin_left_right]" value="<?php echo( $v['margin_left_right'] ); ?>" class="short  margin_left_right" onchange="NBDESIGNADMIN.updateSafeZone(this)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            <?php $count++; endforeach; ?>
                <input type="hidden" value="<?php echo( $count ); ?>" id="nbdesigner-count-box"/>
            </div>
            <div id="nbdesigner-option" class="nbdesigner-option">
                <?php do_action('nbd_before_option_product_design', $post_id, $option, $designer_setting); ?>
                <div class="nbdesigner-opt-inner">
                    <?php $editor_layout = isset($option['layout']) ? $option['layout'] : $layout; ?>
                    <label class="nbdesigner-option-label"><?php esc_html_e('Design editor layout', 'web-to-print-online-designer'); ?></label>
                    <input name="_nbdesigner_option[layout]" id="nbd_layout_modern" value="m" type="radio" <?php checked( $editor_layout, 'm'); ?> /><label for="nbd_layout_modern" class="nbd-admin-setting-layout-label" ><?php esc_html_e('Modern ( recommended )', 'web-to-print-online-designer'); ?></label>
                    <input name="_nbdesigner_option[layout]" id="nbd_layout_visual" value="v" type="radio" <?php checked( $editor_layout, 'v'); ?> /><label for="nbd_layout_visual" class="nbd-admin-setting-layout-label" ><?php esc_html_e('Visual', 'web-to-print-online-designer'); ?></label>
                    <input name="_nbdesigner_option[layout]" id="nbd_layout_classic" value="c" type="radio" <?php checked( $editor_layout, 'c'); ?> /><label for="nbd_layout_classic" class="nbd-admin-setting-layout-label" ><?php esc_html_e('Classic ( deprecated )', 'web-to-print-online-designer'); ?></label>
                </div>
                <div class="nbdesigner-opt-inner">
                    <input type="hidden" value="0" name="_nbdesigner_option[admindesign]"/>
                    <label for="_nbdesigner_admindesign" class="nbdesigner-option-label"><?php esc_html_e('Use templates', 'web-to-print-online-designer'); ?></label>
                    <input type="checkbox" value="1" name="_nbdesigner_option[admindesign]" id="_nbdesigner_admindesign" <?php checked( $option['admindesign'] ); ?> class="short nbd-dependence" data-target="#nbd-global-template"/>
                    <?php 
                    if($enable && $option['admindesign']):
                        $link_manager_template = add_query_arg(array(
                            'pid'   => $post_id, 
                            'view'  => 'templates'), 
                             admin_url('admin.php?page=nbdesigner_manager_product'));
                        if( !is_admin() ) $link_manager_template = add_query_arg(array('id' => get_current_user_id()), getUrlPageNBD('designer'));
                        $link_create_template = add_query_arg(array(
                                'product_id'    => $post_id,
                                'task'          => 'create',
                                'aid'           => get_current_user_id(),
                                'rd'            => is_admin() ? 'admin_templates' : 'designer'
                            ), getUrlPageNBD('create'));
                        $variations = get_nbd_variations( $post_id );
                    ?>
                        <?php
                            if( count($variations) > 0 ):
                        ?>
                            <a class="button nbd-admin-tem-link thickbox" href="#TB_inline?width=300&height=160&inlineId=nbd-thickbox-setting">
                                <span class="dashicons dashicons-art"></span>
                                <?php esc_html_e('Create Template', 'web-to-print-online-designer'); ?>
                            </a>   
                            <div id="nbd-thickbox-setting" style="display:none;">
                                <table class="form-table">
                                    <tr valign="top">
                                        <th scope="row" class="titledesc"><?php esc_html_e("Choose variation", 'web-to-print-online-designer'); ?></th>
                                        <td class="forminp-text">
                                            <select onchange="changeLink(this)">
                                            <?php foreach ($variations as $variation): ?>
                                                <option value="<?php echo( $variation['id'] ); ?>"><?php echo( $variation['name'] ); ?></option>
                                            <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                </table>
                                <p class="nbd-admin-setting-text-align-center"><a class="button button-primary nbd-create" href="<?php echo( $link_create_template ); ?><?php echo '&variation_id='.$variations[0]['id'];  ?>" data-href="<?php echo( $link_create_template ); ?>"><?php esc_html_e("Create template", 'web-to-print-online-designer'); ?></a></p>
                            </div>  
                            <script>
                                changeLink = function(e){
                                    var vid = jQuery(e).val(),
                                    btn = jQuery(e).parents('table').siblings('p').find('a.nbd-create'),
                                    origin_fref = btn.data('href'),
                                    new_href = origin_fref + '&variation_id=' + vid;
                                    btn.attr('href', new_href);
                                }
                            </script>
                        <?php else: ?>
                            <a class="button nbd-admin-tem-link" href="<?php echo esc_url( $link_create_template ); ?>">
                                <span class="dashicons dashicons-art"></span>
                                <?php esc_html_e('Create Template', 'web-to-print-online-designer'); ?>
                            </a>
                        <?php endif; ?>
                        <a href="<?php echo esc_url( $link_manager_template ); ?>" class="button nbd-admin-tem-link">
                            <span class="dashicons dashicons-images-alt"></span>
                            <?php esc_html_e('Manage Templates', 'web-to-print-online-designer'); ?>
                        </a>
                    <?php endif; ?>
                </div>
                <?php 
                    $valid_license = nbd_check_license(); 
                    $valid_license = true;
                    if( $valid_license ):
                ?>
                <div  id="nbd-global-template" class="nbdesigner-opt-inner nbd-independence <?php if (!$option['admindesign']) echo 'nbdesigner-disable'; ?>">
                    <label for="_nbdesigner_option[global_template]" class="nbdesigner-option-label"><?php esc_html_e('Use global template store', 'web-to-print-online-designer'); ?></label>
                    <input type="hidden" value="0" name="_nbdesigner_option[global_template]"/>
                    <input type="checkbox" value="1" name="_nbdesigner_option[global_template]" id="_nbdesigner_option[global_template]" <?php checked( $option['global_template'] ); ?> class="short nbd-dependence" data-target="#nbd-global-template-cat"/>
                </div>
                <div id="nbd-global-template-cat" class="nbdesigner-opt-inner nbd-independence <?php if (!$option['global_template']) echo 'nbdesigner-disable'; ?>">
                    <label for="_nbdesigner_option[global_template_cat]" class="nbdesigner-option-label"><?php esc_html_e('Template type', 'web-to-print-online-designer'); ?></label>
                    <select name="_nbdesigner_option[global_template_cat]">
                    <?php
                        $cats = nbd_get_global_template_cat();
                        foreach ($cats as $cat):
                    ?>
                    <option value="<?php echo( $cat->id ); ?>" <?php selected( $option['global_template_cat'], $cat->id ); ?>><?php echo( $cat->name ); ?></option>
                    <?php endforeach; ?>
                    </select>
                    <a target="_blank" href="https://studio.cmsmart.net/index.php?option=com_nbmedia&view=media&cat=template"><?php esc_html_e('View list template', 'web-to-print-online-designer'); ?></a>
                </div>
                <?php else: ?>
                <div style="display: none;" id="nbd-global-template" class="nbdesigner-opt-inner nbd-independence <?php if (!$option['admindesign']) echo 'nbdesigner-disable'; ?>">
                    <label for="_nbdesigner_option[global_template]" class="nbdesigner-option-label"><?php esc_html_e('Use global template store', 'web-to-print-online-designer'); ?></label>
                    <input type="hidden" value="0" name="_nbdesigner_option[global_template]" checked/>
                </div>
                <?php endif; ?>
                <div class="nbdesigner-opt-inner">
                    <div>
                        <label for="nbdesigner_dpi" class="nbdesigner-option-label"><?php esc_html_e('DPI ( Dots Per Inch )', 'web-to-print-online-designer'); ?></label>
                        <input name="_nbdesigner_option[dpi]" id="nbdesigner_dpi" value="<?php echo( $option['dpi'] );?>" type="number"  min="72" class="nbd-admin-setting-dpi">&nbsp;
                    </div>
                </div>
                <!-- custom charmingprint -->
                <div class="nbdesigner-opt-inner">
                    <div>
                        <label for="nbdesigner_dpi_fe" class="nbdesigner-option-label"><?php esc_html_e('Frontend download resolution - DPI', 'web-to-print-online-designer'); ?></label>
                        <input name="_nbdesigner_option[dpi_fe]" id="nbdesigner_dpi_fe" value="<?php echo( $option['dpi_fe'] );?>" type="number" class="nbd-admin-setting-dpi">&nbsp;
                    </div>
                </div>
                <div class="nbdesigner-opt-inner">
                    <div>
                        <label for="nbdesigner_unit" class="nbdesigner-option-label"><?php esc_html_e('Dimensions unit', 'web-to-print-online-designer'); ?></label>
                        <select id="nbdesigner_unit" name="_nbdesigner_option[unit]" onchange="NBDESIGNADMIN.change_dimension_unit(this)">
                            <option value="cm" <?php selected( $unit, 'cm' ); ?>>cm</option>
                            <option value="in" <?php selected( $unit, 'in' ); ?>>in</option>
                            <option value="mm" <?php selected( $unit, 'mm' ); ?>>mm</option>
                            <option value="ft" <?php selected( $unit, 'ft' ); ?>>ft</option>
                            <option value="px" <?php selected( $unit, 'px' ); ?>>px</option>
                        </select>
                    </div>
                </div>
                <div class="nbdesigner-opt-inner" style="display: none;">
                    <label for="_nbdesigner_customprice" class="nbdesigner-option-label"><?php esc_html_e('Extra price', 'web-to-print-online-designer'); ?></label>
                    <input type="number" step="any" class="short nbdesigner-short-input wc_input_price" id="_nbdesigner_customprice" name="_nbdesigner_option[extra_price]" value="<?php echo( $option['extra_price'] ); ?>"/>
                    &nbsp;<input name="_nbdesigner_option[type_price]" value="1" type="radio" <?php checked( $option['type_price'], 1); ?> /><?php esc_html_e('Fixed', 'web-to-print-online-designer'); ?>   
                    &nbsp;<input name="_nbdesigner_option[type_price]" value="2" type="radio" <?php checked( $option['type_price'], 2); ?> /><?php esc_html_e('Percentage', 'web-to-print-online-designer'); ?>                   
                </div>
                <div class="nbdesigner-opt-inner" style="display: none;">
                    <input type="hidden" value="0" name="_nbdesigner_option[request_quote]"/>
                    <label for="_nbd_request_quote" class="nbdesigner-option-label"><?php esc_html_e('Get a quote', 'web-to-print-online-designer'); ?></label>
                    <input type="checkbox" value="1" name="_nbdesigner_option[request_quote]" id="_nbd_request_quote" <?php checked( $option['request_quote'] ); ?> class="short"/> 
                    <?php esc_html_e('Set product price to ', 'web-to-print-online-designer') . wc_price(0); ?>
                </div>
                <div class="nbdesigner-opt-inner">
                    <div>
                        <label class="nbdesigner-option-label"><?php esc_html_e('Dynamic side', 'web-to-print-online-designer'); ?></label>
                        <input type="hidden" value="0" name="_nbdesigner_option[dynamic_side]"/>
                        <input type="checkbox" value="1" name="_nbdesigner_option[dynamic_side]" id="_nbd_request_quote" <?php checked( $option['dynamic_side'] ); ?> class="short"/>
                    </div>
                    <div>
                        <label class="nbdesigner-option-label"><?php esc_html_e('Price per additional side', 'web-to-print-online-designer'); ?></label>
                        <input type="number" step="any" class="short nbdesigner-short-input wc_input_price" name="_nbdesigner_option[additional_price]" value="<?php if(isset($option['additional_price'])) echo $option['additional_price']; else echo '0'; ?>"/>
                    </div>
                </div>
                <?php
                    if( $layout == 'c' ){
                ?>
                <div class="nbdesigner-opt-inner">
                    <input type="hidden" value="0" name="_nbdesigner_option[allow_specify_dimension]"/>
                    <label for="_nbd_allow_specify_dimension" class="nbdesigner-option-label"><?php esc_html_e('Allow user define demension', 'web-to-print-online-designer'); ?></label>
                    <input type="checkbox" value="1" name="_nbdesigner_option[allow_specify_dimension]" id="_nbd_allow_specify_dimension" <?php checked( $option['allow_specify_dimension'] ); ?> class="short nbd-dependence" data-target="#nbd-custom-size"/>                    
                </div>
                <div id="nbd-custom-size" class="<?php if (!$option['allow_specify_dimension']) echo 'nbdesigner-disable'; ?> nbd-independence nbdesigner-opt-inner">
                    <label for="_nbdesigner_customprice" class="nbdesigner-option-label"><?php esc_html_e('Allow', 'web-to-print-online-designer'); ?></label>
                    <input name="_nbdesigner_option[type_dimension]" value="1" type="radio" <?php checked( $option['type_dimension'], 1); ?> class="nbd-dependence" data-target="#nbd-custom-size-free" /><?php esc_html_e('All dimensions', 'web-to-print-online-designer'); ?>   
                    &nbsp;<input name="_nbdesigner_option[type_dimension]" value="2" type="radio" <?php checked( $option['type_dimension'], 2); ?> class="nbd-dependence" data-target="#nbd-custom-size-defined" /><?php esc_html_e('Predefined dimensions', 'web-to-print-online-designer'); ?>                     
                    <div id="nbd-custom-size-free" class="<?php if ($option['type_dimension'] != 1) echo 'nbdesigner-disable'; ?> nbd-untarget">
                        <div class="nbdesigner-opt-inner">
                            <label class="nbdesigner-option-label"><?php esc_html_e('Min', 'web-to-print-online-designer'); ?> (<span class="nbd-unit"><?php echo( $unit ); ?></span>)</label>
                            <?php esc_html_e('Width', 'web-to-print-online-designer'); ?>: <input type="number" step="any" class="short nbdesigner-short-input" name="_nbdesigner_option[min_width]" value="<?php echo( $option['min_width'] ); ?>"/>
                            <?php esc_html_e('Height', 'web-to-print-online-designer'); ?>: <input type="number" step="any" class="short nbdesigner-short-input" name="_nbdesigner_option[min_height]" value="<?php echo( $option['min_height'] ); ?>"/>
                        </div>
                        <div class="nbdesigner-opt-inner">
                            <label class="nbdesigner-option-label"><?php esc_html_e('Max', 'web-to-print-online-designer'); ?> (<span class="nbd-unit"><?php echo( $unit ); ?></span>)</label>
                            <?php esc_html_e('Width', 'web-to-print-online-designer'); ?>: <input type="number" step="any" class="short nbdesigner-short-input" name="_nbdesigner_option[max_width]" value="<?php echo( $option['max_width'] ); ?>"/>
                            <?php  esc_html_e('Height', 'web-to-print-online-designer'); ?>: <input type="number" step="any" class="short nbdesigner-short-input" name="_nbdesigner_option[max_height]" value="<?php echo( $option['max_height'] ); ?>"/>
                        </div>
                    </div>
                    <div id="nbd-custom-size-defined" class="<?php if ($option['type_dimension'] != 2) echo 'nbdesigner-disable'; ?> nbd-untarget">
                        <?php foreach ($option['defined_dimension'] as $key => $dim): ?>
                            <div class="nbdesigner-opt-inner nbd-defined-size">
                                <?php esc_html_e('Width', 'web-to-print-online-designer'); ?>: <input type="number" step="any" class="short nbdesigner-short-input nbd-defined-width" name="_nbdesigner_option[defined_dimension][<?php echo( $key ); ?>][width]" value="<?php echo( $dim['width'] ); ?>"/>
                                <?php esc_html_e('Height', 'web-to-print-online-designer'); ?>: <input type="number" step="any" class="short nbdesigner-short-input nbd-defined-height" name="_nbdesigner_option[defined_dimension][<?php echo( $key ); ?>][height]" value="<?php echo( $dim['height'] ); ?>"/>
                                <?php esc_html_e('Extra price', 'web-to-print-online-designer'); ?>: <input type="number" step="any" class="short nbdesigner-short-input nbd-defined-price" name="_nbdesigner_option[defined_dimension][<?php echo( $key ); ?>][price]" value="<?php echo( $dim['price'] ); ?>"/>
                                <a class="button nbdesigner-delete" onclick="NBDESIGNADMIN.deleteDefinedDimension(this)">&times;</a>
                            </div>
                        <?php endforeach; ?>
                        <div class="nbd-admin-setting-margin-top-15" id="nbd-duplicate-size-con">
                            <p><small>(<?php esc_html_e('Extra price for each variation, ex: + 5 or - 5', 'web-to-print-online-designer'); ?>)</small></p>
                            <a class="button button-primary" onclick="NBDESIGNADMIN.duplicateDefinedDimension(this)"><?php esc_html_e('Add', 'web-to-print-online-designer'); ?></a>
                        </div>
                    </div>
                    <div class="nbdesigner-opt-inner">
                        <label class="nbdesigner-option-label"><?php esc_html_e('Dynamic page / side', 'web-to-print-online-designer'); ?></label>
                        <input name="_nbdesigner_option[dynamic_side]" value="1" type="radio" <?php checked( $option['dynamic_side'], 1); ?> /><?php esc_html_e('Yes', 'web-to-print-online-designer'); ?>   
                        &nbsp;<input name="_nbdesigner_option[dynamic_side]" value="0" type="radio" <?php checked( $option['dynamic_side'],0); ?> /><?php esc_html_e('No', 'web-to-print-online-designer'); ?>  
                    </div>
                    <div class="nbdesigner-opt-inner nbd-price-per-page <?php if ($option['dynamic_side'] != 1) echo 'nbdesigner-disable'; ?>">
                        <label class="nbdesigner-option-label"><?php esc_html_e('Price per page/side', 'web-to-print-online-designer'); ?></label>
                        <input type="number" step="any" class="short nbdesigner-short-input wc_input_price" name="_nbdesigner_option[price_per_page]" value="<?php if(isset($option['price_per_page'])) echo( $option['price_per_page'] ); else echo '0'; ?>"/>
                    </div>
                </div>  
                <div class="nbdesigner-opt-inner">
                    <input type="hidden" value="0" name="_nbdesigner_option[bulk_variation]"/>
                    <?php $bulk_variation = isset($option['bulk_variation']) ? $option['bulk_variation'] : 0; ?>
                    <label for="_nbd_bulk_variation" class="nbdesigner-option-label"><?php esc_html_e('Bulk variation', 'web-to-print-online-designer'); ?></label>
                    <input type="checkbox" value="1" name="_nbdesigner_option[bulk_variation]" id="_nbd_bulk_variation" <?php checked( $bulk_variation ); ?> class="short"/> 
                    <?php esc_html_e('Enable bulk variation form - add to cart multiple variation with same design', 'web-to-print-online-designer'); ?>
                </div>
                <?php }; ?>
                <?php do_action('nbd_after_option_product_design', $post_id, $option, $designer_setting); ?>
            </div>
        </div>
        <div id="nbd-upload-design" class="nbd-options-tab nbd-admin-setting-padding-10" >
            <div class="nbdr-opt-inner">
                <label for="_nbdesigner_allow_upload" class="nbdesigner-option-label"><?php esc_html_e('Allowed file types', 'web-to-print-online-designer'); ?></label>
                <input type="text" id="_nbdesigner_allow_upload" name="_designer_upload[allow_type]" value="<?php echo( $upload_setting['allow_type'] ); ?>" class="regular-text"/>
                <p class="nbd-admin-setting-upload-extentsion-note"><?php esc_html_e('Extensions seperated by a comma. Don not use dots or spaces. Example: ', 'web-to-print-online-designer'); ?><code>jpg,bmp,pdf,ps,ai,iddd</code></p>
            </div>
            <div class="nbdr-opt-inner">
                <label for="_nbdesigner_disallow_upload" class="nbdesigner-option-label"><?php esc_html_e('Disallowed file types', 'web-to-print-online-designer'); ?></label>
                <input type="text" id="_nbdesigner_disallow_upload" name="_designer_upload[disallow_type]" value="<?php echo( $upload_setting['disallow_type'] ); ?>" class="regular-text"/>
                <p class="nbd-admin-setting-upload-extentsion-note"><?php esc_html_e('Extensions seperated by a comma. Don not use dots or spaces. Example: ', 'web-to-print-online-designer'); ?><code>png,gif</code></p>
            </div>
            <div class="nbdr-opt-inner">
                <label for="_nbdesigner_number_upload" class="nbdesigner-option-label"><?php esc_html_e('Number of uploads', 'web-to-print-online-designer'); ?></label>
                <input type="number" step="any" class="short nbdesigner-short-input" id="_nbdesigner_number_upload" name="_designer_upload[number]" value="<?php echo( $upload_setting['number'] ); ?>"/>
            </div>
            <div class="nbdesigner-opt-inner">
                <label for="_nbdesigner_min_size_upload" class="nbdesigner-option-label"><?php esc_html_e('Min size', 'web-to-print-online-designer'); ?></label>
                <input type="number" step="any" class="short nbdesigner-short-input" id="_nbdesigner_min_size_upload" name="_designer_upload[minsize]" value="<?php echo( $upload_setting['minsize'] ); ?>"/>&nbsp;MB
            </div>
            <div class="nbdesigner-opt-inner">
                <label for="_nbdesigner_max_size_upload" class="nbdesigner-option-label"><?php esc_html_e('Max size', 'web-to-print-online-designer'); ?></label>
                <input type="number" step="any" class="short nbdesigner-short-input" id="_nbdesigner_max_size_upload" name="_designer_upload[maxsize]" value="<?php echo( $upload_setting['maxsize'] ); ?>"/>&nbsp;MB
            </div>
            <div class="nbdesigner-opt-inner">
                <label for="_nbd_mindpi_upload" class="nbdesigner-option-label"><?php esc_html_e('Min. resolution DPI for JPG image', 'web-to-print-online-designer'); ?></label>
                <input type="number" step="any" class="short nbdesigner-short-input" id="_nbd_mindpi_upload" name="_designer_upload[mindpi]" value="<?php echo( $upload_setting['mindpi'] ); ?>"/>
            </div>
            <?php do_action('nbu_after_upload_settings', $post_id, $upload_setting, $unit); ?>
        </div>
    </div>
    <div>
        <?php do_action('nbd_after_option_product', $post_id, $option, $designer_setting); ?>
    </div>
</div>
<?php
function add_js_code(){
?><script>
    jQuery(document).ready( function($) {
        $('.nbd-tabber').click(function() {
            var t = $(this),
                s = $('.nbd-tabber.selected');

            s.removeClass("selected");
            t.addClass("selected");
            $(s.data('target')).fadeOut(0);
            $(t.data('target')).fadeIn(200);
        });
        var direction = "<?php if(is_rtl()) echo 'right'; else echo 'left'; ?>";
        var options = {
            "content":"<h3>" + "<?php esc_html_e('Notice', 'web-to-print-online-designer'); ?>" + "<\/h3>" +
                       "<p>" + "<?php esc_html_e('Bellow values must in range from 0 to 500px', 'web-to-print-online-designer'); ?>" + "<\/p>" + 
                       "<p>" + "<?php esc_html_e('There are relative position of design area in bounding box.', 'web-to-print-online-designer'); ?>" + "<\/p>" +
                       "<p><img class='nbd-admin-setting-max-width-100' src='"+"<?php echo NBDESIGNER_PLUGIN_URL .'assets/images/bounding-box.png'; ?>"+"' /><br /><a href='"+"<?php echo NBDESIGNER_PLUGIN_URL .'assets/images/bounding-box.png'; ?>"+"' target='_blank'>" + "<?php  esc_html_e('See detail', 'web-to-print-online-designer'); ?>" + "</a></p>",
            "position": {"edge":direction, "align":"center"}
        };
        if ( ! options ) return;
        options = $.extend( options, {
            close: function() {
                //to do
            }
        });
        $('.nbdesign-config-tooltip').first().pointer( options );
        $('.nbdesign-config-tooltip').first().on('click', function(){
            $(this).pointer("open")
        });
        var size_options = {
            "content" : "<h3>" + "<?php esc_html_e('Notice', 'web-to-print-online-designer'); ?>" + "<\/h3>" +
                        "<p>"+"<?php esc_html_e('Please upload background image with aspect ratio', 'web-to-print-online-designer'); ?>"+": W<sub>p</sub>&timesH<sub>p</sub>.</p>" +
                        "<p>" + "<?php esc_html_e('Make sure setting', 'web-to-print-online-designer'); ?>" + " <span class='nbd-admin-setting-notice-product-size'>" + "<?php esc_html_e('Product size', 'web-to-print-online-designer'); ?>" + "</span> " + "<?php esc_html_e('must always be the top priority!', 'web-to-print-online-designer'); ?>" + "</p>" +
                        "<p>" + "<?php esc_html_e('You have two order setting options', 'web-to-print-online-designer'); ?>" + 
                        ": <br /><strong>1</strong> - <span class='nbd-admin-setting-notice-product-size'>" + "<?php esc_html_e('Product size', 'web-to-print-online-designer'); ?>" + "</span> →"+
                        " <span class='nbd-admin-setting-notice-design-area'>" + "<?php esc_html_e('Design area size', 'web-to-print-online-designer'); ?>" + "</span> "+
                        " (<span class='nbd-admin-setting-notice-relative-position'>" + "<?php esc_html_e('Relative position', 'web-to-print-online-designer'); ?>" + "</span> "+"<?php esc_html_e('will automatic update', 'web-to-print-online-designer'); ?>"+")" +
                        "<br /><strong>2</strong> - <span class='nbd-admin-setting-notice-product-size'>" + "<?php esc_html_e('Product size', 'web-to-print-online-designer'); ?>" + "</span> →"+
                        " <span class='nbd-admin-setting-notice-relative-position'>" + "<?php esc_html_e('Relative position', 'web-to-print-online-designer'); ?>" + "</span> → "+  
                        "<?php esc_html_e('click', 'web-to-print-online-designer'); ?>" + "<span class='dashicons dashicons-update'></span> "+"<?php esc_html_e('to update', 'web-to-print-online-designer'); ?>"+" <span class='nbd-admin-setting-notice-design-area'>" + "<?php esc_html_e('Design area size', 'web-to-print-online-designer'); ?>" + "</span>"+ 
                        "</p>",
            "position": {"edge":direction, "align":"center"}
        };
        $('.nbdesign-config-size-tooltip').first().pointer( size_options );
        $('.nbdesign-config-size-tooltip').first().on('click', function(){
            $(this).pointer("open")
        });
        var da_option = {
            "content" : "<h3>" + "<?php esc_html_e('Notice', 'web-to-print-online-designer'); ?>" + "<\/h3>" +
                        "<p>"+"<?php esc_html_e('After change bellow', 'web-to-print-online-designer'); ?>"+" <span class='nbd-admin-setting-notice-design-area'>"+"<?php esc_html_e('values', 'web-to-print-online-designer'); ?>"+"</span>, "+"<span class='nbd-admin-setting-notice-relative-position'>"+"<?php esc_html_e('relative position', 'web-to-print-online-designer'); ?>"+"</span> "+"<?php esc_html_e('of design area in bounding box will automatic update.', 'web-to-print-online-designer'); ?>"+"</p>" +
                        "<p>" + "<?php esc_html_e('Notice', 'web-to-print-online-designer'); ?>" + ": W<sub>p</sub> &gt;= W<sub>d</sub> + L<sub>d</sub>" +
                        " | H<sub>p</sub> &gt;= H<sub>d</sub> + T<sub>d</sub>" +
                        "<br />"+"<?php esc_html_e('If color labels change to ', 'web-to-print-online-designer'); ?>"+"<span class='nbd-admin-setting-notice-color-red'>"+"<?php esc_html_e('red', 'web-to-print-online-designer'); ?>"+"</span>, "+"<?php esc_html_e('check values again.', 'web-to-print-online-designer'); ?>"+"</p>" +
                        "<p>"+"<?php esc_html_e('There', 'web-to-print-online-designer'); ?>"+" <span class='nbd-admin-setting-notice-design-area'>"+"<?php esc_html_e('values', 'web-to-print-online-designer'); ?>"+"</span> "+"<?php esc_html_e('will decide dimensions of output images.', 'web-to-print-online-designer'); ?>"+"</p>" +
                        "<p>"+"<?php esc_html_e('If you modify', 'web-to-print-online-designer'); ?>"+" <span class='nbd-admin-setting-notice-relative-position'>"+"<?php esc_html_e('relative position', 'web-to-print-online-designer'); ?>"+"</span>, "+"<?php esc_html_e('click button', 'web-to-print-online-designer'); ?>"+" <span class='dashicons dashicons-update'></span> "+"<?php esc_html_e('to update', 'web-to-print-online-designer'); ?>"+"<span class='nbd-admin-setting-notice-design-area'> "+"<?php esc_html_e('Design area.', 'web-to-print-online-designer'); ?>"+"</span>"+"</p>" ,
            "position": {"edge":direction, "align":"center"}
        };
        $('.nbdesign-config-realsize-tooltip').first().pointer( da_option );
        $('.nbdesign-config-realsize-tooltip').first().on('click', function(){
            $(this).pointer("open")
        });
    });
</script>
<?php
}
add_action("admin_footer", "add_js_code");