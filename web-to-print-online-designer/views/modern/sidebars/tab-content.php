<div class="tabs-content">
    <i class="fa fa-times" aria-hidden="true"></i>
    <span class="hide-tablet"><i class="icon-nbd icon-nbd-fomat-top-left rotate-135"></i></span>
    <?php if( $show_nbo_option && $settings['nbdesigner_display_product_option'] == '2' ): ?>
    <?php include 'tab-product.php'; ?>
    <?php endif; ?>
    <?php if( $product_data["option"]['admindesign'] != "0" && !( !( $task == 'create' || ( $task == 'edit' && $design_type == 'template' ) ) && isset( $settings['nbdesigner_hide_template_tab'] ) && $settings['nbdesigner_hide_template_tab'] == 'yes' ) ): ?>
    <?php include 'tab-templates.php'; ?>
    <?php endif; ?>
    <?php include 'tab-typography.php'; ?>
    <?php 
          do_action('nbod_after_tab_typography',$nbav_active_tabs);  // custom charmingprint
    ?>
    <?php include 'tab-clipart.php'; ?>
    <?php include 'tab-photo.php'; ?>
    <?php if($show_elements_tab) include 'tab-elements.php'; ?>
    <?php do_action( 'nbd_editor_extra_tab_content' ); ?>
    <?php if($settings["nbdesigner_hide_layer_tab"] == "no") include 'tab-layer.php'; ?>
</div>