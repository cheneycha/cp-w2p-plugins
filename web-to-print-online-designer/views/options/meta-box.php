<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div id="nbo-wraper">
    <?php wp_nonce_field('nbo_box', 'nbo_box_nonce'); ?>
    <div style="overflow: hidden;">
        <ul class="nbo-tabs">
            <?php do_action('nbo_options_before_meta_box_tabs'); ?>
            <li><a href="#nbo-options" class="active"><span class="dashicons dashicons-forms"></span> <?php _e('Printing Option', 'web-to-print-online-designer'); ?></a></li>
            <li><a href="#nbpb-options"><span class="dashicons dashicons-admin-generic"></span> <?php _e('Product Builder', 'web-to-print-online-designer'); ?></a></li>
            <?php do_action('nbo_options_meta_box_tabs'); ?>
            <li><a href="#nbpt-options"><span class="dashicons dashicons-feedback"></span> <?php _e('Printing Info Tab', 'web-to-print-online-designer'); ?></a></li>
        </ul>
        <?php do_action('nbo_options_before_meta_box_panels', $post_id); ?>
        <div class="nbo_options_panel" id="nbo-options">
            
            <p class="nbo-form-field">
                <label for="_nbo_enable"><?php _e('Enable Printing option', 'web-to-print-online-designer'); ?></label>
                <span class="nbo-option-val">
                    <input type="hidden" value="0" name="_nbo_enable"/>
                    <input type="checkbox" value="1" name="_nbo_enable" id="_nbo_enable" <?php checked($enable); ?> class="short" />
                </span>
            </p>
            <p class="nbo-form-field">
                <label>
                    <a href="<?php echo $link_edit_option; ?>" target="_blank" class="button">
                        <?php if( $option_id != 0 ){ _e('Edit option', 'web-to-print-online-designer'); }else{ _e('Create option', 'web-to-print-online-designer'); }; ?>
                    </a>
                </label>
            </p>
            <?php 
                if( nbdesigner_get_option( 'nbdesigner_enbale_rich_snippet_price', 'no' ) == 'yes' ): 
                    $snippet_price = get_post_meta($post_id, '_nbo_snippet_price', true);
                    $snippet_price = $snippet_price ? $snippet_price : '';
            ?>
            <hr />
            <p class="nbo-form-field">
                <label for="_nbo_enable"><?php _e('Default rich snippet price', 'web-to-print-online-designer'); ?>(<?php echo get_woocommerce_currency_symbol(); ?>)</label>
                <span class="nbo-option-val">
                    <input type="text" value="<?php echo $snippet_price; ?>" name="_nbo_snippet_price" class="short wc_input_price" />
                    <br />
                    <span><i><?php _e('Leave empty this input to dismiss this setting', 'web-to-print-online-designer'); ?></i></span>
                </span>
            </p>
            <?php endif; ?>
        </div>
        <div class="nbo_options_panel" id="nbpb-options" style="display: none;">
            <?php if( $option_id != 0 ): ?>
            <p class="nbo-form-field">
                <input type="hidden" value="0" name="_nbdpb_enable"/>
                <label for="_nbdpb_enable"><?php _e('Enable Product builder', 'web-to-print-online-designer'); ?></label>
                <span class="nbo-option-val">
                    <input type="checkbox" value="1" name="_nbdpb_enable" id="_nbdpb_enable" <?php checked($nbdpb_enable); ?> class="short" />
                </span>
            </p>
            <?php else: ?>
            <p class="nbo-form-field">
            <?php echo sprintf(__( 'Please enable "Printing option" and create a <a target="_blank" href="%s">Print option</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbd_printing_options'))); ?>
            </p>
            <?php endif; ?>
        </div>
        <?php do_action('nbo_options_meta_box_panels', $post_id); ?>
        <div class="nbo_options_panel" id="nbpt-options" style="display: none;">
            <p class="nbo-form-field">
                <label for="_nbpt_title"><?php _e('Title', 'web-to-print-online-designer'); ?></label>
                <span class="nbo-option-val">
                    <input style="width: 100%;" type="text" value="<?php echo $nbpt_title; ?>" name="_nbpt_title" id="_nbpt_title" />
                </span>
            </p>
            <div style="padding: 10px;">
                <p><?php _e('Content', 'web-to-print-online-designer'); ?></p>
            <?php
                $settings = array(
                    'textarea_name' => '_nbpt_content',
                    'tinymce'       => array(
                        'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
                        'theme_advanced_buttons2' => '',
                    ),
                    'editor_height' => 175
                );
                wp_editor( htmlspecialchars_decode( $nbpt_content ), 'nbo_print_info_tab_editor', apply_filters( 'woocommerce_product_short_description_editor_settings', $settings ) );
            ?>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</div>