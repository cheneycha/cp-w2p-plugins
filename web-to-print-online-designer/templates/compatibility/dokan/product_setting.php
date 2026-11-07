<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div class="dokan-other-options dokan-edit-row dokan-clearfix <?php echo( $pro_class ); ?>" id="nbd-config">
    <div class="dokan-section-heading" data-togglehandler="dokan_other_options">
        <h2><i class="fa fa-picture-o" aria-hidden="true"></i> <?php esc_html_e( 'NBDesigner Options', 'web-to-print-online-designer' ); ?></h2>
        <p><?php esc_html_e( 'Configure design for product', 'web-to-print-online-designer' ); ?></p>
        <a href="#" class="dokan-section-toggle">
            <i class="fa fa-sort-desc fa-flip-vertical" aria-hidden="true"></i>
        </a>
        <div class="dokan-clearfix"></div>
    </div>    
    <div class="dokan-section-content">
        <?php include_once(NBDESIGNER_PLUGIN_DIR . 'views/metabox-design-setting.php');  ?>
    </div>  
</div>