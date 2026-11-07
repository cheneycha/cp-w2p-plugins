<div class="nbd-sidebar">
    <?php
        $active_design      = wp_is_mobile() ? true : false;
        $active_product     = ($show_nbo_option && !$active_design && $settings['nbdesigner_display_product_option'] == '2' && !wp_is_mobile()) ? true : false;
        $active_template    = (!$active_product && !$active_design && $product_data["option"]['admindesign'] == "1") ? true : false;
        if( $active_template && ( !( $task == 'create' || ( $task == 'edit' && $design_type == 'template' ) ) && isset( $settings['nbdesigner_hide_template_tab'] ) && $settings['nbdesigner_hide_template_tab'] == 'yes' ) ){
            $active_template    = false;
        }
        $active_typos       = (!$active_product && !$active_design && !$active_template && $settings["nbdesigner_enable_text"] == "yes") ? true : false;
        $active_cliparts    = (!$active_product && !$active_design && !$active_typos && $settings["nbdesigner_enable_clipart"] == "yes" && !$active_template) ? true : false;
        $active_photos      = (!$active_product && !$active_design && !$active_typos && !$active_cliparts && !$active_template && $settings["nbdesigner_enable_image"] == "yes") ? true : false;
        $show_elements_tab  = (!($settings["nbdesigner_enable_clipart"] == "yes" || $settings["nbdesigner_enable_qrcode"] == "yes" || $settings["nbdesigner_enable_draw"] == "yes") || $settings["nbdesigner_hide_element_tab"] == "yes" ) ? false: true;
        $active_elements    = (!$active_product && !$active_design && !$active_typos && !$active_cliparts && !$active_photos && $show_elements_tab && !$active_template && $settings["nbdesigner_hide_element_tab"] == "no") ? true : false;
        $active_layers      = (!$active_product && !$active_design && !$active_typos && !$active_cliparts && !$active_photos && !$active_elements && !$active_template && $settings["nbdesigner_hide_layer_tab"] != "no") ? true : false;
        // custom charmingprint
        $nbav_active_tabs = array();
        $nbav_active_tabs = apply_filters('nbod_extra_active_tabs',$nbav_active_tabs,$active_product, $active_design ,$active_typos ,$active_cliparts ,$active_photos ,$active_elements ,$active_template ,$active_layers);
    ?>
    <?php include 'sidebars/tab-nav.php'; ?>
    <?php include 'sidebars/tab-content.php'; ?>
    <?php include 'sidebars/preview.php'?>
</div>