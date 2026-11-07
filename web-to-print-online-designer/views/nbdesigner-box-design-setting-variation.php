<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div class="nbdesigner-setting-variation">
    <h3><?php __('Setting Design', 'web-to-print-online-designer'); ?></h3>
    <div class="nbdesigner-left">
        <input type="hidden" value="0" name="_nbdesigner_enable<?php echo( $vid ); ?>"/>
        <label for="_nbdesigner_enable<?php echo( $vid ); ?>" class="nbdesigner-setting-box-label"><?php esc_html_e('Enable Design for this variation', 'web-to-print-online-designer'); ?></label>
        <input type="checkbox" value="1" name="_nbdesigner_enable<?php echo( $vid ); ?>" <?php checked($enable); ?> class="short nbdesigner_variation_enable" onchange="NBDESIGNADMIN.show_variation_config(this)"/>
    </div>    
    <div class="nbdesigner-right add_more" style="<?php if( !$enable ) echo 'display: none;'; ?>">
        <a class="button button-primary" onclick="NBDESIGNADMIN.addOrientation(<?php echo( $vid ); ?>)"><?php esc_html_e('Add More', 'web-to-print-online-designer'); ?></a>
        <a class="button button-primary" onclick="NBDESIGNADMIN.collapseAll(<?php echo( $vid ); ?>)"><?php esc_html_e('Collapse All', 'web-to-print-online-designer'); ?></a>
    </div>
    <div class="nbdesigner-clearfix"></div>
    <div id="nbdesigner-boxes<?php echo( $vid ); ?>" class="<?php if (!$enable) echo 'nbdesigner-disable'; ?> nbdesigner-variation-setting nbdesigner-boxes">
        <?php foreach ($designer_setting as $k => $v): ?>
            <div class="nbdesigner-box-container">
                <div class="nbdesigner-box">
                    <label class="nbdesigner-setting-box-label"><?php esc_html_e('Name', 'web-to-print-online-designer'); ?></label>
                    <div class="nbdesigner-setting-box-value">
                        <input name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][orientation_name]" class="short orientation_name" value="<?php echo( $v['orientation_name'] ); ?>" type="text" required/>
                    </div>
                    <div class="nbdesigner-right">
                        <a class="button nbdesigner-collapse" onclick="NBDESIGNADMIN.collapseBox(this)"><span class="dashicons dashicons-arrow-down"></span><?php esc_html_e('More setting', 'web-to-print-online-designer'); ?></a>
                        <a class="button nbdesigner-delete delete_orientation" data-index="<?php echo( $k ); ?>" data-variation="<?php echo( $vid ); ?>" onclick="NBDESIGNADMIN.deleteOrientation(this)">&times;</a>
                    </div>
                </div>
                <div class="nbdesigner-box nbdesigner-box-collapse" data-variation="<?php echo( $vid ); ?>" style="display: none;">
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
                                <?php if($v['bg_type'] == 'color') echo 'background: ' . esc_attr( $v['bg_color_value'] ); ?>"
                            >    
                                <?php
                                    $img_src = is_numeric( $v['img_src'] ) ? wp_get_attachment_url( $v['img_src'] ) : $v['img_src'];
                                ?>
                                <img src="<?php if ($v['img_src'] != '') {echo esc_url( $img_src );} else {echo NBDESIGNER_PLUGIN_URL . 'assets/images/default.png';} ?>"
                                    <?php if($v['bg_type'] != 'image') echo ' style="display: none;"' ?>
                                     class="designer_img_src "
                                    />
                            </div>   
                            <?php $overlay_style = 'none'; if($v['show_overlay']) $overlay_style = 'block'; ?>
                            <div class="nbdesigner-image-overlay"
                                style="width: <?php echo( $v['area_design_width'] ); ?>px;
                                       height: <?php echo( $v['area_design_height'] ); ?>px;
                                       left: <?php echo( $v['area_design_left'] ); ?>px;
                                       top: <?php echo( $v['area_design_top'] ); ?>px;
                                       display: <?php echo( $overlay_style ); ?>"
                            >
                                <?php
                                    $img_overlay = is_numeric( $v['img_overlay'] ) ? wp_get_attachment_url( $v['img_overlay'] ) : $v['img_overlay'];
                                ?>
                                <img src="<?php if ($v['img_overlay'] != '') {echo esc_url( $img_overlay );} else {echo NBDESIGNER_PLUGIN_URL . 'assets/images/overlay.png';} ?>" class="img_overlay"/>
                            </div>
                            <div class="nbd-bleed <?php if (!$v['show_bleed']) echo 'nbdesigner-disable'; ?>"
                                style="width: <?php echo round( $ratio * ($v['real_width'] - 2 * $v['bleed_left_right']))  ?>px;
                                        height: <?php echo round( $ratio * ($v['real_height'] - 2 * $v['bleed_top_bottom']))  ?>px;
                                        top: <?php echo round( $ratio * ($top + $v['real_top'] + $v['bleed_top_bottom']))  ?>px;
                                        left: <?php echo round( $ratio * ($left + $v['real_left'] + $v['bleed_left_right']))  ?>px;"> 
                            </div>
                            <div class="nbd-safe-zone <?php if (!$v['show_safe_zone']) echo 'nbdesigner-disable'; ?>"
                                style="width: <?php echo round( $ratio * ($v['real_width'] - 2 * $v['bleed_left_right'] - 2 * $v['margin_left_right']))  ?>px;
                                        height: <?php echo round( $ratio * ($v['real_height'] - 2 * $v['bleed_top_bottom'] - 2 * $v['margin_top_bottom']))  ?>px;
                                        top: <?php echo round( $ratio * ($top + $v['real_top'] + $v['bleed_top_bottom'] + $v['margin_top_bottom']))  ?>px;
                                        left: <?php echo round( $ratio * ($left + $v['real_left'] + $v['bleed_left_right'] + $v['margin_left_right']))  ?>px;">
                            </div>
                            <div class="nbdesigner-area-design" id="nbdesigner-area-design-<?php echo( $k ); ?>" 
                                 style="width: <?php echo( $v['area_design_width'] . 'px' ); ?>; 
                                        height: <?php echo( $v['area_design_height'] . 'px' ); ?>; 
                                        left: <?php echo( $v['area_design_left'] . 'px' ); ?>; 
                                        top: <?php echo( $v['area_design_top'] . 'px' ); ?>;"> </div>
                        </div>
                        <input type="hidden" class="hidden_img_src" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][img_src]" value="<?php echo( $v['img_src'] ); ?>" >
                        <input type="hidden" class="hidden_img_src_top" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][img_src_top]" value="<?php echo( $v['img_src_top'] ); ?>" >
                        <input type="hidden" class="hidden_img_src_left" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][img_src_left]" value="<?php echo( $v['img_src_left'] ); ?>">
                        <input type="hidden" class="hidden_img_src_width" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][img_src_width]" value="<?php echo( $v['img_src_width'] ); ?>">
                        <input type="hidden" class="hidden_img_src_height" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][img_src_height]" value="<?php echo( $v['img_src_height'] ); ?>">
                        <input type="hidden" class="hidden_overlay_src" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][img_overlay]" value="<?php echo( $v['img_overlay'] ); ?>">
                        <input type="hidden" class="hidden_nbd_version" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][version]" value="<?php echo( $v['version'] ); ?>">
                        <input type="hidden" class="hidden_nbd_ratio" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][ratio]" value="<?php echo( $ratio ); ?>">
                        <div>	
                            <a class="button nbdesigner_move nbdesigner_move_left" data-index="<?php echo( $k ); ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'left')">&larr;</a>
                            <a class="button nbdesigner_move nbdesigner_move_right" data-index="<?php echo( $k ); ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'right')">&rarr;</a>
                            <a class="button nbdesigner_move nbdesigner_move_up" data-index="<?php echo( $k ); ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'up')">&uarr;</a>
                            <a class="button nbdesigner_move nbdesigner_move_down" data-index="<?php echo( $k ); ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'down')">&darr;</a>
                            <a class="button nbdesigner_move nbdesigner_move_center" data-index="<?php echo( $k ); ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'center')">&frac12;</a>
                            <a class="button nbdesigner_move nbdesigner_move_center nbd-admin-setting-fit-btn" data-index="<?php echo( $k ); ?>" onclick="NBDESIGNADMIN.nbdesigner_move(this, 'fit')"><i class="mce-ico mce-i-dfw nbd-admin-setting-fit-icon" ></i></a>
                        </div>
                        <div>
                            <p>
                                <label class="nbdesigner-setting-box-label"><?php esc_html_e('Background type'); ?>:</label>
                                <label class="nbdesigner-lbl-setting"><input type="radio" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][bg_type]" value="image" 
                                    <?php checked($v['bg_type'], 'image', true); ?> class="bg_type"
                                    onclick="NBDESIGNADMIN.change_background_type(this)"   /><?php esc_html_e('Image', 'web-to-print-online-designer'); ?></label>
                                <label class="nbdesigner-lbl-setting"><input type="radio" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][bg_type]" value="color" 
                                    <?php checked($v['bg_type'], 'color', true); ?> class="bg_type"
                                    onclick="NBDESIGNADMIN.change_background_type(this)"   /><?php esc_html_e('Color', 'web-to-print-online-designer'); ?></label>
                                <label class="nbdesigner-lbl-setting"><input type="radio" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][bg_type]" value="tran" 
                                    <?php checked($v['bg_type'], 'tran', true); ?> class="bg_type"
                                    onclick="NBDESIGNADMIN.change_background_type(this)"   /><?php esc_html_e('Transparent', 'web-to-print-online-designer'); ?></label>
                            </p>
                        </div>
                        <div class="nbdesigner_bg_image" <?php if($v['bg_type'] != 'image') echo ' style="display: none;"' ?>>
                            <a class="button nbdesigner-button nbdesigner-add-image" onclick="NBDESIGNADMIN.loadImage(this)" data-index="<?php echo( $k ); ?>"><?php esc_html_e('Set image', 'web-to-print-online-designer'); ?></a>     
                        </div>
                        <div class="nbdesigner_bg_color" <?php if($v['bg_type'] != 'color') echo ' style="display: none;"' ?>>
                            <input type="text" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][bg_color_value]" value="<?php echo( $v['bg_color_value'] ); ?>" class="nbd-color-picker" />
                        </div>
                        <div class="nbdesigner_overlay_box">
                            <label class="nbdesigner-setting-box-label"><?php esc_html_e('Overlay', 'web-to-print-online-designer'); ?></label>
                            <input type="hidden" value="0" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][show_overlay]" class="show_overlay"/>                   
                            <input type="checkbox" value="1" 
                                name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][show_overlay]" id="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][bg_type]" <?php checked($v['show_overlay']); ?> 
                                class="show_overlay" onchange="NBDESIGNADMIN.toggleShowOverlay(this)"/>  
                            <a class="button overlay-toggle" onclick="NBDESIGNADMIN.loadImageOverlay(this)" style="display: <?php if($v['show_overlay']) {echo 'inline-block';} else {echo 'none';} ?>">
                                <?php esc_html_e('Set image', 'web-to-print-online-designer'); ?>
                            </a>
                            <img style="display: <?php if($v['show_overlay']) {echo 'inline-block';} else {echo 'none';} ?>"
                                 src="<?php if ($v['img_overlay'] != '') {echo esc_url( $img_overlay );} else {echo NBDESIGNER_PLUGIN_URL . 'assets/images/overlay.png';} ?>" class="img_overlay"/>                            
                            <p class="overlay-toggle" style="display: <?php if($v['show_overlay']) {echo 'block';} else {echo 'none';} ?>">
                                <input type="hidden" value="0" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][include_overlay]" class="include_overlay"/> 
                                <input type="checkbox" value="1"  class="include_overlay"
                                    name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][include_overlay]"  <?php checked($v['include_overlay']); ?>   
                                    />
                                <span><?php  esc_html_e('Include in final design', 'web-to-print-online-designer'); ?></span>
                            </p>
                        </div>
                        <?php $include_background = isset( $v['include_background'] ) ? $v['include_background'] : 1; ?>
                        <div class="nbd-admin-setting-include-background-wrap">
                            <label class="nbd-label nbdesigner-setting-box-label"><?php  esc_html_e('Include background image', 'web-to-print-online-designer'); ?></label>
                            <input class="include_background" type="hidden" value="0" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][include_background]"/>
                            <input class="include_background" type="checkbox" value="1" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][include_background]"  <?php checked($include_background); ?>/>
                            &nbsp;<small><?php esc_html_e('Include background image in final design if background type is image.', 'web-to-print-online-designer'); ?></small>
                        </div>
                    </div>
                    <hr class="nbd-admin-setting-hr-clear"/>
                    <div class="nbdesigner-info-box">
                        <p class="nbd-setting-section-title"><?php esc_html_e('Product size:', 'web-to-print-online-designer'); ?></p>
                        <div class="nbdesigner-info-box-inner notice-width nbd-has-notice">
                            <label class="nbdesigner-setting-box-label"><?php esc_html_e('Width', 'web-to-print-online-designer'); ?></label>
                            <div>
                                <input type="number" step="any" min="0" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][product_width]" 
                                       value="<?php echo( $v['product_width'] ); ?>" class="short product_width" 
                                       onchange="NBDESIGNADMIN.change_dimension_product(this, 'width')"> <span class="nbd-unit"><?php echo( $unit ); ?></span>
                            </div>
                        </div>
                        <div class="nbdesigner-info-box-inner notice-height nbd-has-notice">
                            <label class="nbdesigner-setting-box-label"><?php esc_html_e('Height', 'web-to-print-online-designer'); ?></label>
                            <div>
                                <input type="number" step="any" min="0" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][product_height]" 
                                       value="<?php echo( $v['product_height'] ); ?>" class="short product_height"  
                                       onchange="NBDESIGNADMIN.change_dimension_product(this, 'height')"> <span class="nbd-unit"><?php echo( $unit ); ?></span>
                            </div>
                        </div>
                        <p class="nbd-setting-section-title"><?php esc_html_e('Design area size:', 'web-to-print-online-designer'); ?></p>
                        <div class="nbdesigner-info-box-inner notice-width nbd-has-notice">
                            <label class="nbdesigner-setting-box-label"><?php esc_html_e('Width', 'web-to-print-online-designer'); ?></label>
                            <div>
                                <input type="number" step="any" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][real_width]" 
                                       value="<?php echo( $v['real_width'] ); ?>" class="short real_width" 
                                       onchange="NBDESIGNADMIN.updateRelativePosition(this, 'width')"> <span class="nbd-unit"><?php echo( $unit ); ?></span> 
                            </div>
                        </div>
                        <div class="nbdesigner-info-box-inner notice-height nbd-has-notice">
                            <label class="nbdesigner-setting-box-label"><?php esc_html_e('Height', 'web-to-print-online-designer'); ?></label>
                            <div>
                                <input type="number" step="any" min="0" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][real_height]" 
                                       value="<?php echo( $v['real_height'] ); ?>" class="short real_height"  
                                       onchange="NBDESIGNADMIN.updateRelativePosition(this, 'height')"> <span class="nbd-unit"><?php echo( $unit ); ?></span> 
                            </div>
                        </div>
                        <div class="nbdesigner-info-box-inner notice-height nbd-has-notice">
                            <label class="nbdesigner-setting-box-label"><?php esc_html_e('Top', 'web-to-print-online-designer'); ?></label>
                            <div>
                                <input type="number" step="any" min="0" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][real_top]" 
                                       value="<?php echo( $v['real_top'] ); ?>" class="short real_top"  
                                       onchange="NBDESIGNADMIN.updateRelativePosition(this, 'top')"> <span class="nbd-unit"><?php echo( $unit ); ?></span> 
                            </div>
                        </div>
                        <div class="nbdesigner-info-box-inner notice-width nbd-has-notice">
                            <label class="nbdesigner-setting-box-label"><?php esc_html_e('Left', 'web-to-print-online-designer'); ?></label>
                            <div>
                                <input type="number" step="any" min="0" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][real_left]" 
                                       value="<?php echo( $v['real_left'] ); ?>" class="short real_left"  
                                       onchange="NBDESIGNADMIN.updateRelativePosition(this, 'left')"> <span class="nbd-unit"><?php echo( $unit ); ?></span> 
                            </div>
                        </div>
                        <p class="nbd-setting-section-title">
                            <?php esc_html_e('Relative position:', 'web-to-print-online-designer'); ?>
                            <span class="dashicons dashicons-update nbdesiger-update-area-design" onclick="NBDESIGNADMIN.updateDesignAreaSize(this)"></span>
                        </p>
                        <div class="nbdesigner-info-box-inner">
                            <label class="nbdesigner-setting-box-label"><?php esc_html_e('Width', 'web-to-print-online-designer'); ?></label>
                            <div>
                                <input type="number" step="any"  min="0" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][area_design_width]" 
                                       value="<?php echo( $v['area_design_width'] ); ?>" class="short area_design_dimension area_design_width" data-index="width" 
                                       onchange="NBDESIGNADMIN.updatePositionDesignArea(this)">&nbsp;px
                            </div>
                        </div>
                        <div class="nbdesigner-info-box-inner">
                            <label class="nbdesigner-setting-box-label"><?php esc_html_e('Height', 'web-to-print-online-designer'); ?></label>
                            <div>
                                <input type="number"  step="any" min="0" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][area_design_height]" 
                                       value="<?php echo( $v['area_design_height'] ); ?>" class="short area_design_dimension area_design_height" data-index="height" 
                                       onchange="NBDESIGNADMIN.updatePositionDesignArea(this)">&nbsp;px
                            </div>
                        </div>
                        <div class="nbdesigner-info-box-inner">
                            <label class="nbdesigner-setting-box-label"><?php esc_html_e('Left', 'web-to-print-online-designer'); ?></label>
                            <div>
                                <input type="number" step="any"  min="0" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][area_design_left]" 
                                       value="<?php echo( $v['area_design_left'] ); ?>" class="short area_design_dimension area_design_left" data-index="left" 
                                       onchange="NBDESIGNADMIN.updatePositionDesignArea(this)">&nbsp;px
                            </div>
                        </div>
                        <div class="nbdesigner-info-box-inner">
                            <label class="nbdesigner-setting-box-label"><?php esc_html_e('Top', 'web-to-print-online-designer'); ?></label>
                            <div>
                                <input type="number" step="any"  min="0" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][area_design_top]" 
                                       value="<?php echo( $v['area_design_top'] ); ?>" class="short area_design_dimension area_design_top" data-index="top" 
                                       onchange="NBDESIGNADMIN.updatePositionDesignArea(this)">&nbsp;px
                            </div>
                        </div>
                        <p class="nbd-setting-section-title"><?php esc_html_e('For paper/card', 'web-to-print-online-designer'); ?></p>
                        <div class="nbdesigner-info-box-inner">
                            <label class="nbdesigner-setting-box-label"><?php esc_html_e('Show cut line', 'web-to-print-online-designer'); ?> <span class="nbd-bleed-notation"></span></label>
                            <div>
                                <input type="hidden" value="0" class="show_bleed" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][show_bleed]"/>
                                <input type="checkbox" value="1" class="show_bleed" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][show_bleed]" <?php checked( $v['show_bleed'] ); ?> class="short nbd-dependence" data-target="#nbd-bleed<?php echo( $vid ); ?><?php echo( $k ) ?>" onchange="NBDESIGNADMIN.toggleBleed(this)"/> 
                            </div>
                        </div> 
                        <div id="nbd-bleed<?php echo( $vid ); ?><?php echo( $k ) ?>" class="nbd-bleed-con <?php if (!$v['show_bleed']) echo 'nbdesigner-disable'; ?> nbd-independence">
                            <div class="nbdesigner-info-box-inner">
                                <label class="nbdesigner-setting-box-label"><?php esc_html_e('Bleed top-bottom', 'web-to-print-online-designer'); ?></label>
                                <div>
                                    <input type="number" step="any" min="0" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][bleed_top_bottom]" value="<?php echo( $v['bleed_top_bottom'] ); ?>" class="short bleed_top_bottom" onchange="NBDESIGNADMIN.updateBleed(this)">
                                </div>
                            </div>
                            <div class="nbdesigner-info-box-inner">
                                <label class="nbdesigner-setting-box-label"><?php esc_html_e('Bleed left-right', 'web-to-print-online-designer'); ?></label>
                                <div>
                                    <input type="number" step="any"  min="0" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][bleed_left_right]" value="<?php echo( $v['bleed_left_right'] ); ?>" class="short bleed_left_right" onchange="NBDESIGNADMIN.updateBleed(this)">
                                </div>
                            </div>
                        </div>
                        <div class="nbdesigner-info-box-inner">
                            <label class="nbdesigner-setting-box-label"><?php esc_html_e('Show safe zone', 'web-to-print-online-designer'); ?> <span class="nbd-safe-zone-notation"></span></label>
                            <div>
                                <input type="hidden" value="0" class="show_safe_zone" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][show_safe_zone]"/>
                                <input type="checkbox" value="1" class="show_safe_zone" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][show_safe_zone]"  <?php checked( $v['show_safe_zone'] ); ?> class="short nbd-dependence" data-target="#nbd-safe-zone<?php echo( $vid ); ?><?php echo( $k ) ?>" onchange="NBDESIGNADMIN.toggleSafeZone(this)"/> 
                            </div>
                        </div>
                        <div id="nbd-safe-zone<?php echo( $vid ); ?><?php echo( $k ) ?>" class="nbd-safe-zone-con <?php if (!$v['show_safe_zone']) echo 'nbdesigner-disable'; ?> nbd-independence">
                            <div class="nbdesigner-info-box-inner">
                                <label class="nbdesigner-setting-box-label"><?php esc_html_e('Magin top-bottom', 'web-to-print-online-designer'); ?></label>
                                <div>
                                    <input type="number" step="any"  min="0" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][margin_top_bottom]" value="<?php echo( $v['margin_top_bottom'] ); ?>" class="short  margin_top_bottom" onchange="NBDESIGNADMIN.updateSafeZone(this)">
                                </div>
                            </div>
                            <div class="nbdesigner-info-box-inner">
                                <label class="nbdesigner-setting-box-label"><?php esc_html_e('Magin left-right', 'web-to-print-online-designer'); ?></label>
                                <div>
                                    <input type="number" step="any"  min="0" name="_designer_setting<?php echo( $vid ); ?>[<?php echo( $k ); ?>][margin_left_right]" value="<?php echo( $v['margin_left_right'] ); ?>" class="short  margin_left_right" onchange="NBDESIGNADMIN.updateSafeZone(this)">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>