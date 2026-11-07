<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<div class="nbo_options_panel" id="nbd-design-guideline" style="display: none;">
    <div class="nbo-form-field">
        <label><b><?php esc_html_e( 'Design guideline files', 'web-to-print-online-designer' ); ?></b></label>
    </div>
    <div class="nbdg_files">
        <div>
            <table class="widefat">
                <thead>
                    <tr>
                        <th class="sort">&nbsp;</th>
                        <th><?php esc_html_e( 'Name', 'web-to-print-online-designer' ); ?> <?php echo wc_help_tip( esc_html__( 'This is the name of the download shown to the customer.', 'web-to-print-online-designer' ) ); ?></th>
                        <th><?php esc_html_e( 'Extension', 'web-to-print-online-designer' ); ?></th>
                        <th colspan="2"><?php esc_html_e( 'File URL', 'web-to-print-online-designer' ); ?> <?php echo wc_help_tip( esc_html__( 'This is the URL or absolute path to the file which customer will get access to. URLs entered here should already be encoded.', 'web-to-print-online-designer' ) ); ?></th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody class="ui-sortable">
                    <?php
                    if ( $guideline_files ) {
                        foreach ( $guideline_files as $file ) {
                            include 'guideline-download.php';
                        }
                    }
                    ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="6">
                            <a href="#" class="button insert" data-row="
                            <?php
                                $file = array(
                                    'file'  => '',
                                    'ext'   => '',
                                    'name'  => ''
                                );
                                ob_start();
                                require 'guideline-download.php';
                                echo esc_attr( ob_get_clean() );
                                ?>
                            "><?php esc_html_e( 'Add File', 'web-to-print-online-designer' ); ?></a>
                        </th>
                    </tr>
                </tfoot>
            </table>
            <div>
                <p>
                    <b><?php esc_html_e( 'Note:', 'web-to-print-online-designer' ); ?></b>
                    <br />
                    <?php esc_html_e( 'For better security, WordPress allows you to only upload the most commonly used file types.', 'web-to-print-online-designer' ); ?>
                    <?php esc_html_e( 'You can upload files via FTP, cPanel or add additional file types ', 'web-to-print-online-designer' ); ?>
                    <a target="_blank" href="<?php echo esc_url(admin_url('admin.php?page=nbdesigner&tab=general#nbdesigner_guideline_mimes')); ?>"><?php esc_html_e( 'here', 'web-to-print-online-designer' ); ?></a>
                </p>
            </div>
        </div>
    </div>
    <div class="nbd-admin-setting-guideline-des">
        <p><b><?php esc_html_e('Description', 'web-to-print-online-designer'); ?></b></p>
    <?php
        $settings = array(
            'textarea_name' => '_nbdg_tab_content',
            'tinymce'       => array(
                'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
                'theme_advanced_buttons2' => '',
            ),
            'editor_height' => 175
        );
        wp_editor( htmlspecialchars_decode( $nbdg_tab_content ), 'nbdg_tab_editor', apply_filters( 'woocommerce_product_short_description_editor_settings', $settings ) );
    ?>
    </div>
    <div class="nbo-form-field">
        <label><b><?php esc_html_e( 'Show as product tab', 'web-to-print-online-designer' ); ?></b></label>
        <div class="nbo-option-val">
            <input type="hidden" value="0" name="_nbdg_tab_enable"/>
            <input type="checkbox" value="1" name="_nbdg_tab_enable" id="_nbdg_tab_enable" <?php checked($nbdg_tab_enable); ?> class="short" />
            <label for="_nbdg_tab_enable"><?php esc_html_e('Enable', 'web-to-print-online-designer'); ?></label>
        </div>
    </div>
</div>