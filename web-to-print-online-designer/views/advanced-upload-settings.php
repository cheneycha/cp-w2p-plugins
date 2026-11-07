<?php if (!defined('ABSPATH')) exit; ?>
<?php 
    $advanced_upload = isset( $upload_setting['advanced_upload'] ) ? $upload_setting['advanced_upload'] : 0;
    $u_product_width = isset( $upload_setting['product_width'] ) ? $upload_setting['product_width'] : 10;
    $u_product_height = isset( $upload_setting['product_height'] ) ? $upload_setting['product_height'] : 10;
    $u_min_width = isset( $upload_setting['min_width'] ) ? $upload_setting['min_width'] : 250;
    $u_max_width = isset( $upload_setting['max_width'] ) ? $upload_setting['max_width'] : 1000;
    $u_min_height = isset( $upload_setting['min_height'] ) ? $upload_setting['min_height'] : 250;
    $u_max_height = isset( $upload_setting['max_height'] ) ? $upload_setting['max_height'] : 1000;

?>
<hr style="margin-top: 20px;"/>
<div style="font-weight: bold;font-size: 16px;text-align: center;"><?php _e('Advanced upload', 'web-to-print-online-designer'); ?></div>
<div style="text-align: center;"><small><?php _e('(Leave empty below inputs to dismiss settings)', 'web-to-print-online-designer'); ?></small></div>
<div class="nbdesigner-opt-inner">
    <input type="hidden" value="0" name="_designer_upload[advanced_upload]"/>
    <label for="_nbd_advanced_upload_upload" class="nbdesigner-option-label"><?php _e('Enable advanced upload', 'web-to-print-online-designer'); ?></label>
    <input type="checkbox" value="1" name="_designer_upload[advanced_upload]" id="_nbd_advanced_upload_upload" <?php checked( $advanced_upload, 1 ); ?> class="short nbd-dependence" data-target="#nbu-advanced-upload-settings"/>
    <span><?php _e('Apply for photo frame, wallpaper. Only support images.', 'web-to-print-online-designer'); ?></span>
</div>
<div id="nbu-advanced-upload-settings" class="nbd-dependence <?php if ( !$advanced_upload ) echo 'nbdesigner-disable'; ?>">
    <div class="nbdesigner-opt-inner">
        <label for="_nbd_product_width_upload" class="nbdesigner-option-label"><?php _e('Product width', 'web-to-print-online-designer'); ?></label>
        <input type="number" step="any" class="short nbdesigner-short-input" id="_nbd_product_width_upload" name="_designer_upload[product_width]" value="<?php echo $u_product_width; ?>"/> <?php echo $unit; ?>
    </div> 
    <div class="nbdesigner-opt-inner">
        <label for="_nbd_product_height_upload" class="nbdesigner-option-label"><?php _e('Product height', 'web-to-print-online-designer'); ?></label>
        <input type="number" step="any" class="short nbdesigner-short-input" id="_nbd_product_height_upload" name="_designer_upload[product_height]" value="<?php echo $u_product_height; ?>"/> <?php echo $unit; ?>
    </div>
    <div class="nbdesigner-opt-inner">
        <label class="nbdesigner-option-label"><?php _e('Upload photo width', 'web-to-print-online-designer'); ?></label>
        <?php _e('Min', 'web-to-print-online-designer'); ?> <input type="number" step="1" class="short nbdesigner-short-input" id="_nbd_min_width_upload" name="_designer_upload[min_width]" value="<?php echo $u_min_width; ?>"/> 
        <?php _e('Max', 'web-to-print-online-designer'); ?> <input type="number" step="1" class="short nbdesigner-short-input" id="_nbd_max_width_upload" name="_designer_upload[max_width]" value="<?php echo $u_max_width; ?>"/> px
    </div>
    <div class="nbdesigner-opt-inner">
        <label class="nbdesigner-option-label"><?php _e('Upload photo height', 'web-to-print-online-designer'); ?></label>
        <?php _e('Min', 'web-to-print-online-designer'); ?> <input type="number" step="1" class="short nbdesigner-short-input" id="_nbd_min_height_upload" name="_designer_upload[min_height]" value="<?php echo $u_min_height; ?>"/> 
        <?php _e('Max', 'web-to-print-online-designer'); ?> <input type="number" step="1" class="short nbdesigner-short-input" id="_nbd_max_height_upload" name="_designer_upload[max_height]" value="<?php echo $u_max_height; ?>"/> px
    </div>
</div>