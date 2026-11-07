<?php 
    include 'popup-contents/share.php';
    include 'popup-contents/webcam.php';
?>
<div class="nbd-popup popup-fileType" data-animate="bottom-to-top">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <i class="icon-nbd icon-nbd-clear close-popup"></i>
        <div class="head"></div>
        <div class="body">
            <div class="main-body"></div>
        </div>
        <div class="footer"></div>
    </div>
</div>
<?php 
    include 'popup-contents/hotkeys.php';
    include 'popup-contents/upload-terms.php';
    include 'popup-contents/confirm-delete-layers.php';
    include 'popup-contents/confirm-delete-stage.php';
    include 'popup-contents/confirm-clear-all.php';
    include 'popup-contents/prompt-insert-part-template.php';
    if( $task == 'create_template' ) {include 'popup-contents/global-template-category.php';};
    if( $show_nbo_option && ($settings['nbdesigner_display_product_option'] == '1' || wp_is_mobile() ) ) include 'popup-contents/printing-options.php';
    include 'popup-contents/crop-image.php';
    include 'popup-contents/guidelines.php';
    include 'popup-contents/user-design.php';
    include 'popup-contents/my-templates2.php';
    include 'popup-contents/my-templates.php';
    include 'popup-contents/login.php';
    include 'popup-contents/my-cart-designs.php';
    include 'popup-contents/stage-grid-view.php';
    include 'popup-contents/mask-bitmap.php';
    if( $task == 'create' || ( $task == 'edit' && ( isset( $_GET['design_type'] ) && $_GET['design_type'] == 'template' ) ) ){
        include 'popup-contents/template-tags.php';
    }
    if( nbdesigner_get_option('nbdesigner_enable_text_check_lang', 'no') == 'yes' ) include 'popup-contents/warning-font.php';
    if( isset( $settings['nbdesigner_show_button_change_product'] ) && $settings['nbdesigner_show_button_change_product'] == 'yes' && !wp_is_mobile() ) include 'popup-contents/products.php';
    do_action('nbd_modern_extra_popup', $task, $settings);