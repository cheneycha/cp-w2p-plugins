<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<div class="nbo_options_panel" id="nbd-local-settings" style="display: none;">
    <div class="postbox nbd-admin-setting-margin-10" >
        <button type="button" class="handlediv" aria-expanded="false">
            <span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Local settings', 'web-to-print-online-designer' ); ?></span><span class="toggle-indicator" aria-hidden="false"></span>
        </button>
        <h2 class="nbd-admin-setting-local-setting-title"><span><b><?php esc_html_e( 'Local settings', 'web-to-print-online-designer' ); ?></b></span></h2>
        <div class="inside">
            <div class="nbo-form-field">
                <label><b><?php esc_html_e( 'Override global settings', 'web-to-print-online-designer' ); ?></b></label>
                <div class="nbo-option-val">
                    <input type="hidden" value="0" name="_nbls_enable"/>
                    <input type="checkbox" value="1" name="_nbls_enable" id="_nbls_enable" data-ls-toggle="nbls-main-settings" <?php checked( $nbls_enable ); ?> class="short" />
                    <label for="_nbls_enable"><?php esc_html_e('Enable', 'web-to-print-online-designer'); ?></label>
                </div>
            </div>
            <div class="nbd-admin-setting-local-setting-note">
                <b><?php esc_html_e( 'Note:', 'web-to-print-online-designer' ); ?></b>
                <br />
                <i><?php esc_html_e( 'Bellow settings will be override ', 'web-to-print-online-designer' ); echo sprintf(__( '<a target="_blank" href="%s">global settings</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=frontend'))) ?>.</i>
            </div>
            <hr />
            <div class="nbls-main-settings" id="nbls-main-settings">
                <div class="nbd-admin-setting-local-setting-note">
                    <select id="nbls_setting_id">
                        <option value=""><?php esc_html_e( '-- Choose setting --', 'web-to-print-online-designer' ); ?></option>
                        <?php foreach( $settings['group_titles'] as $gkey => $gtext ): ?>
                        <optgroup label="<?php echo( $gtext ); ?>">
                            <?php 
                                foreach( $settings['option_groups'][$gkey] as $option ):
                                    $show = true;
                                    if( isset( $option['layout'] ) ){
                                        $show = nbd_check_availaible_option( $option['layout'] );
                                    }
                                    if( isset( $option['local'] ) && $option['local'] == false ){
                                        $show = false;
                                    }
                                    if( $show ){
                            ?>
                            <option value="<?php echo( $option['id'] ); ?>"><?php echo( $option['title'] ); ?></option>
                            <?php } endforeach; ?>
                        </optgroup>
                        <?php endforeach; ?>
                    </select>
                    <input type="button" class="button " value="<?php esc_html_e( 'Add', 'web-to-print-online-designer' ); ?>" id="nbls_add_setting">
                </div>
                <hr />
                <div class="nbd-admin-setting-padding-10">
                    <input type="hidden" value="<?php echo implode( "|", array_keys( $local_settings ) ); ?>" id="nbls_keys_string" name="_nbls_keys_string"/>
                    <input type="hidden" value="" id="nbls_options_string" name="_nbls_options_string"/>
                    <table class="form-table" id="nbls_settings_table">
                        <thead>
                            <tr>
                                <th class="nbd-admin-setting-width-30"><?php esc_html_e( 'Option name', 'web-to-print-online-designer' ); ?></th>
                                <th class="nbd-admin-setting-width-60"><?php esc_html_e( 'Option value', 'web-to-print-online-designer' ); ?></th>
                                <th class="nbd-admin-setting-width-10"><?php esc_html_e( 'Action', 'web-to-print-online-designer' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                foreach( $keys_arr as $key_setting ){
                                    $this->build_option_output( $key_setting, $settings, $post_id );
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="postbox closed nbd-admin-setting-margin-10" >
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Extra settings', 'web-to-print-online-designer' ); ?></span><span class="toggle-indicator" aria-hidden="false"></span>
        </button>
        <h2 class="nbd-admin-setting-local-setting-title"><span><b><?php esc_html_e( 'Extra settings', 'web-to-print-online-designer' ); ?></b></span></h2>
        <div class="inside">
            <div class="nbo-form-field">
                <label><b><?php esc_html_e( 'Clipart settings', 'web-to-print-online-designer' ); ?></b></label>
                <div class="nbo-option-val">
                    <input type="hidden" value="0" name="_nbes_enable_settings[clipart]"/>
                    <input type="checkbox" value="1" name="_nbes_enable_settings[clipart]" id="_nbes_clipart_category_enable" data-ls-toggle="nbes_clipart_cats" <?php checked( $nbes_enable_settings['clipart'] ); ?> class="short" />
                    <label for="_nbes_clipart_category_enable"><?php esc_html_e('Enable', 'web-to-print-online-designer'); ?></label>
                </div>
            </div>
            <div class="nbo-form-field nbes-depend" id="nbes_clipart_cats">
                <label><b><?php esc_html_e( 'Select clipart category', 'web-to-print-online-designer' ); ?></b></label>
                <div class="nbo-option-val">
                    <select name="_nbes_settings[clipart_cats][]" multiple class="nbes-slect-woo">
                        <?php 
                            foreach( $art_cats as $art_cat ): 
                                $selected = ( $select_all_clipart_cat || in_array( $art_cat->id, $nbes_settings['clipart_cats'] ) ) ? ' selected="selected" ' : '';
                        ?>
                        <option value="<?php echo( $art_cat->id ); ?>" <?php echo ( $selected ); ?>><?php echo( $art_cat->name ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr />
            <div class="nbo-form-field">
                <label><b><?php esc_html_e( 'Font settings', 'web-to-print-online-designer' ); ?></b></label>
                <div class="nbo-option-val">
                    <input type="hidden" value="0" name="_nbes_enable_settings[font]"/>
                    <input type="checkbox" value="1" name="_nbes_enable_settings[font]" id="_nbes_font_category_enable" data-ls-toggle="nbes_font_cats" <?php checked( $nbes_enable_settings['font'] ); ?> class="short" />
                    <label for="_nbes_font_category_enable"><?php esc_html_e('Enable', 'web-to-print-online-designer'); ?></label>
                </div>
            </div>
            <div class="nbo-form-field nbes-depend" id="nbes_font_cats">
                <label><b><?php esc_html_e( 'Select font category', 'web-to-print-online-designer' ); ?></b></label>
                <div class="nbo-option-val">
                    <select name="_nbes_settings[font_cats][]" multiple class="nbes-slect-woo">
                        <?php 
                            foreach( $font_cats as $font_cat ): 
                                $selected = ( $select_all_font_cat || in_array( $font_cat->id, $nbes_settings['font_cats'] ) ) ? ' selected="selected" ' : '';
                        ?>
                        <option value="<?php echo( $font_cat->id ); ?>" <?php echo( $selected ); ?>><?php echo( $font_cat->name ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <hr />
            <div class="nbo-form-field">
                <label><b><?php esc_html_e( 'Background settings', 'web-to-print-online-designer' ); ?></b></label>
                <div class="nbo-option-val">
                    <input type="hidden" value="0" name="_nbes_enable_settings[background]"/>
                    <input type="checkbox" value="1" name="_nbes_enable_settings[background]" id="_nbes_background_color_enable" data-ls-toggle="nbes_background_color" <?php checked( $nbes_enable_settings['background'] ); ?> class="short" />
                    <label for="_nbes_background_color_enable"><?php esc_html_e('Enable', 'web-to-print-online-designer'); ?></label>
                </div>
            </div>
            <div class="nbo-form-field nbes-depend" id="nbes_background_color">
                <label><b><?php esc_html_e( 'Select background colors', 'web-to-print-online-designer' ); ?></b></label>
                <div class="nbo-option-val">
                    <table class="nbd_nbes" data-row="<tr><td class='sort'></td><td><input type='checkbox'></td><td><input type='text' name='_nbes_settings[background_colors][codes][]' value='#ffffff' class='nbes-color-picker'></td><td><input type='text' name='_nbes_settings[background_colors][names][]' value='' class='short'></td></tr>">
                        <thead>
                            <tr>
                                <th class='sort'>&nbsp;</th>
                                <th class="check-column">
                                    <input type="checkbox" >
                                </th>
                                <th>
                                    <span class="column-title" data-text="<?php esc_html_e( 'Color', 'web-to-print-online-designer' ); ?>"><?php esc_html_e( 'Color', 'web-to-print-online-designer' ); ?></span>
                                </th>
                                <th>
                                    <span><?php esc_html_e('Color name', 'web-to-print-online-designer'); ?></span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach( $nbes_settings['background_colors']['codes'] as $index => $code ): ?>
                            <tr>
                                <td class='sort'></td>
                                <td><input type='checkbox'></td>
                                <td><input type='text' name='_nbes_settings[background_colors][codes][]' value="<?php echo( $code ); ?>" class="nbes-color-picker"></td>
                                <td><input type="text" name='_nbes_settings[background_colors][names][]' value="<?php echo( $nbes_settings['background_colors']['names'][$index] ); ?>" class="short"></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">
                                    <button type="button" data-type="quantity" class="button button-primary nbd_nbes-add-rule"><?php esc_html_e( 'Add Color', 'web-to-print-online-designer' ); ?></button>
                                    <button type="button" class="button button-secondary nbd_nbes-delete-rules"><?php esc_html_e( 'Delete Selected', 'web-to-print-online-designer' ); ?></button>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <hr />
            <div class="nbo-form-field">
                <label><b><?php esc_html_e( 'Foreground settings', 'web-to-print-online-designer' ); ?></b></label>
                <div class="nbo-option-val">
                    <input type="hidden" value="0" name="_nbes_enable_settings[foreground]"/>
                    <input type="checkbox" value="1" name="_nbes_enable_settings[foreground]" id="_nbes_foreground_color_enable" data-ls-toggle="nbes_foreground_color" <?php checked( $nbes_enable_settings['foreground'] ); ?> class="short" />
                    <label for="_nbes_foreground_color_enable"><?php esc_html_e('Enable', 'web-to-print-online-designer'); ?></label>
                </div>
            </div>
            <div class="nbo-form-field nbes-depend" id="nbes_foreground_color">
                <label><b><?php esc_html_e( 'Select foreground colors', 'web-to-print-online-designer' ); ?></b></label>
                <div class="nbo-option-val">
                    <table class="nbd_nbes" data-row="<tr><td class='sort'></td><td><input type='checkbox'></td><td><input type='text' name='_nbes_settings[foreground_colors][codes][]' value='#ffffff' class='nbes-color-picker'></td><td><input type='text' name='_nbes_settings[foreground_colors][names][]' value='' class='short'></td></tr>">
                        <thead>
                            <tr>
                                <th class='sort'>&nbsp;</th>
                                <th class="check-column">
                                    <input type="checkbox" >
                                </th>
                                <th>
                                    <span class="column-title" data-text="<?php esc_html_e( 'Color', 'web-to-print-online-designer' ); ?>"><?php esc_html_e( 'Color', 'web-to-print-online-designer' ); ?></span>
                                </th>
                                <th>
                                    <span><?php esc_html_e('Color name', 'web-to-print-online-designer'); ?></span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach( $nbes_settings['foreground_colors']['codes'] as $index => $code ): ?>
                            <tr>
                                <td class='sort'></td>
                                <td><input type='checkbox'></td>                         
                                <td><input type='text' name='_nbes_settings[foreground_colors][codes][]' value="<?php echo( $code ); ?>" class="nbes-color-picker"></td>
                                <td><input type="text" name='_nbes_settings[foreground_colors][names][]' value="<?php echo( $nbes_settings['foreground_colors']['names'][$index] ); ?>" class="short"></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">
                                    <button type="button" data-type="quantity" class="button button-primary nbd_nbes-add-rule"><?php esc_html_e( 'Add Color', 'web-to-print-online-designer' ); ?></button>
                                    <button type="button" class="button button-secondary nbd_nbes-delete-rules"><?php esc_html_e( 'Delete Selected', 'web-to-print-online-designer' ); ?></button>
                                </th>
                            </tr>
                        </tfoot>            
                    </table>
                    <div class="nbd-admin-setting-margin-top-15">
                        <input type="hidden" value="0" name="_nbes_settings[force_fg]"/>
                        <input type="checkbox" value="1" name="_nbes_settings[force_fg]" id="_nbes_settings_force_fg" <?php checked( $nbes_settings['force_fg'] ); ?> class="short" />
                        <label for="_nbes_settings_force_fg"><?php esc_html_e('Force foreground color for all layers', 'web-to-print-online-designer'); ?></label>
                    </div>
                </div>
            </div>
            <hr />
            <div class="nbo-form-field">
                <label><b><?php esc_html_e( 'Color combination settings', 'web-to-print-online-designer' ); ?></b> <?php echo wc_help_tip( esc_html__( 'This setting will be force color of all layers to foreground color, override background and foreground settings.', 'web-to-print-online-designer' ) ); ?></label>
                <div class="nbo-option-val">
                    <input type="hidden" value="0" name="_nbes_enable_settings[combination]"/>
                    <input type="checkbox" value="1" name="_nbes_enable_settings[combination]" id="_nbes_combination_color_enable" data-ls-toggle="nbes_combination_color" <?php checked( $nbes_enable_settings['combination'] ); ?> class="short" />
                    <label for="_nbes_combination_color_enable"><?php esc_html_e('Enable', 'web-to-print-online-designer'); ?></label>
                </div>
            </div>
            <div class="nbo-form-field nbes-depend" id="nbes_combination_color">
                <label><b><?php esc_html_e( 'Select combination colors', 'web-to-print-online-designer' ); ?></b></label>
                <div class="nbo-option-val">
                    <table class="nbd_nbes" data-row="<tr><td class='sort'></td><td><input type='checkbox'></td><td><input type='text' name='_nbes_settings[combination_colors][bg_codes][]' value='#ffffff' class='nbes-color-picker'><input type='text' name='_nbes_settings[combination_colors][bg_names][]' value='' class='short'></td><td><input type='text' name='_nbes_settings[combination_colors][fg_codes][]' value='' class='nbes-color-picker'><input type='text' name='_nbes_settings[combination_colors][fg_names][]' value='' class='short'></td></tr>">
                        <thead>
                            <tr>
                                <th class='sort'>&nbsp;</th>
                                <th class="check-column">
                                    <input type="checkbox" >
                                </th>
                                <th>
                                    <span class="column-title" data-text="<?php esc_html_e( 'Background Color', 'web-to-print-online-designer' ); ?>"><?php esc_html_e( 'Background Color', 'web-to-print-online-designer' ); ?></span>
                                </th>
                                <th>
                                    <span><?php esc_html_e('Foreground color', 'web-to-print-online-designer'); ?></span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach( $nbes_settings['combination_colors']['bg_codes'] as $index => $bg_code ): ?>
                            <tr>
                                <td class='sort'></td>
                                <td><input type='checkbox'></td>
                                <td>
                                    <input type='text' name='_nbes_settings[combination_colors][bg_codes][]' value="<?php echo( $bg_code ); ?>" class="nbes-color-picker">
                                    <input type='text' name='_nbes_settings[combination_colors][bg_names][]' value="<?php echo( $nbes_settings['combination_colors']['bg_names'][$index] ); ?>" class="short">
                                </td>
                                <td>
                                    <input type="text" name='_nbes_settings[combination_colors][fg_codes][]' value="<?php echo( $nbes_settings['combination_colors']['fg_codes'][$index] ); ?>" class="nbes-color-picker">
                                    <input type="text" name='_nbes_settings[combination_colors][fg_names][]' value="<?php echo( $nbes_settings['combination_colors']['fg_names'][$index] ); ?>" class="short">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4">
                                    <button type="button" data-type="quantity" class="button button-primary nbd_nbes-add-rule"><?php esc_html_e( 'Add', 'web-to-print-online-designer' ); ?></button>
                                    <button type="button" class="button button-secondary nbd_nbes-delete-rules"><?php esc_html_e( 'Delete Selected', 'web-to-print-online-designer' ); ?></button>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php do_action( 'after_nbes_settings', $post_id ); ?>
        </div>
    </div>
    <div class="postbox closed nbd-admin-setting-margin-10" >
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: 3D preview', 'web-to-print-online-designer' ); ?></span><span class="toggle-indicator" aria-hidden="false"></span>
        </button>
        <h2 class="nbd-admin-setting-local-setting-title"><span><b><?php esc_html_e( '3D preview', 'web-to-print-online-designer' ); ?></b></span></h2>
        <div class="inside">
            <div class="nbo-form-field">
                <label ><b><?php esc_html_e( 'Enable 3D preview', 'web-to-print-online-designer' ); ?></b></label>
                <div class="nbo-option-val">
                    <input type="hidden" value="0" name="_nbes_settings[td_preview]"/>
                    <input type="checkbox" value="1" name="_nbes_settings[td_preview]" id="_nbes_settings_td_preview" <?php checked( $nbes_settings['td_preview'] ); ?> class="short" />
                    <label for="_nbes_settings_td_preview"><?php esc_html_e('Enable', 'web-to-print-online-designer'); ?></label>
                </div>
            </div>
            <div class="nbo-form-field">
                <label for="_nbes_settings_td_folder_name"><b><?php _e('3D model folder', 'web-to-print-online-designer'); ?></b></label>
                <span class="nbo-option-val">
                    <input type="text" value="<?php echo $nbes_settings['td_folder_name']; ?>" id="_nbes_settings_td_folder_name" name="_nbes_settings[td_folder_name]" class="short" />
                    <br />
                    <a target="_blank" href="https://nbdesigner.cmsmart.net/3d-preview/"><i><?php _e('View document', 'web-to-print-online-designer'); ?></i></a>
                </span>
            </div>
            <div class="nbo-form-field">
                <label for="_nbes_settings_td_custom_mesh_name"><b><?php _e('Custom mesh name', 'web-to-print-online-designer'); ?></b></label>
                <span class="nbo-option-val">
                    <input type="text" value="<?php echo $nbes_settings['td_custom_mesh_name']; ?>" id="_nbes_settings_td_custom_mesh_name" name="_nbes_settings[td_custom_mesh_name]" class="short" />
                </span>
            </div>
        </div>
    </div>
    <div class="postbox closed nbd-admin-setting-margin-10" >
        <button type="button" class="handlediv" aria-expanded="true">
            <span class="screen-reader-text"><?php esc_html_e( 'Toggle panel: Sticker cutline preview', 'web-to-print-online-designer' ); ?></span><span class="toggle-indicator" aria-hidden="false"></span>
        </button>
        <h2 class="nbd-admin-setting-local-setting-title"><span><b><?php esc_html_e( 'Sticker cutline preview', 'web-to-print-online-designer' ); ?></b></span></h2>
        <div class="inside">
            <div class="nbo-form-field">
                <label ><b><?php esc_html_e( 'Enable sticker cutline preview', 'web-to-print-online-designer' ); ?></b></label>
                <div class="nbo-option-val">
                    <input type="hidden" value="0" name="_nbes_settings[sticker_preview]"/>
                    <input type="checkbox" value="1" name="_nbes_settings[sticker_preview]" id="_nbes_settings_sticker_preview" <?php checked( $nbes_settings['sticker_preview'] ); ?> class="short" />
                    <label for="_nbes_settings_sticker_preview"><?php esc_html_e('Enable', 'web-to-print-online-designer'); ?></label>
                    <br />
                    <i><?php _e('Please set "Bleed top-bottom" and "Bleed left-right" for each side.', 'web-to-print-online-designer'); ?></i>
                </div>
            </div>
        </div>
    </div>
</div>