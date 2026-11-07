<?php
    if ( !defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
?>
<div class="nbo_options_panel" id="nbd-faq" style="display: none;">
    <div class="nbd-faqs">
        <div class="nbo-form-field">
            <label><b><?php esc_html_e( 'Show as product tab', 'web-to-print-online-designer' ); ?></b></label>
            <div class="nbo-option-val">
                <input type="hidden" value="0" name="_nbd_faq[enable]"/>
                <input type="checkbox" value="1" name="_nbd_faq[enable]" id="_nbd_faq_enable" <?php checked($nbd_faq['enable']); ?> class="short" />
                <label for="_nbd_faq_enable"><?php esc_html_e('Enable', 'web-to-print-online-designer'); ?></label>
            </div>
        </div>
        <div style="padding: 20px;">
            <div><?php esc_html_e('Select FAQs to display in FAQs tab:', 'web-to-print-online-designer'); ?></div>
            <div class="nbf-wrap">
                <div class="nbf-float">
                    <div>
                        <select id="nbf-categories">
                        <?php 
                            $walker = new NBF_Dropdown_Category();
                            echo call_user_func_array( array( &$walker, 'walk' ), array( $categories, 0, array() ) );
                        ?>
                        </select>
                    </div>
                    <div>
                        <div class="nbf-table-wrap nbf-table-wrapper" >
                            <?php include( NBDESIGNER_PLUGIN_DIR . 'views/faq/faq-table.php' ); ?>
                        </div>
                        <a class="button button-primary" id="nbf-add-faqs"><?php esc_html_e( 'Add FAQs', 'web-to-print-online-designer' ); ?></a>
                    </div>
                </div>
                <div class="nbf-float">
                    <div><?php esc_html_e('Selected FAQs', 'web-to-print-online-designer'); ?></div>
                    <div class="nbf-table-wrapper">
                        <table class="wp-list-table widefat fixed posts faqs faqs-selected">
                            <thead>
                                <tr>
                                    <th class="sort">&nbsp;</th>
                                    <th class="check">&nbsp;</th>
                                    <th><?php esc_html_e('Title', 'web-to-print-online-designer'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach( $selected_faqs as $faq ): ?>
                                <tr data-id="<?php echo $faq['id']; ?>">
                                    <td class="sort"></td>
                                    <th class="check">
                                        <input type="checkbox" value="<?php echo $faq['id']; ?>" />
                                        <input type="hidden" value="<?php echo $faq['id']; ?>" name="_nbd_faq[faqs][]"/>
                                    </th>
                                    <td><a href="<?php echo $faq['url'];?>"><?php echo $faq['name']; ?></a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="faqs-selected-actions">
                        <a class="button" id="nbf-remove-faqs"><?php esc_html_e('Remove', 'web-to-print-online-designer'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>