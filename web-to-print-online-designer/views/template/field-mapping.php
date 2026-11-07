<?php if (!defined('ABSPATH')) exit; ?>
<h1><?php _e( 'Template fields', 'web-to-print-online-designer' ); ?></h1>
<div class="postbox nbtm-wrap">
    <div class="nbtm-col-6">
        <h2><?php _e( 'Template fields mapping', 'web-to-print-online-designer' ); ?></h2>
        <p><?php _e( 'Map template fields( layer contents ) with other or the customer information. The fields connected with the logged user information will be filled automatically. Maybe show the quick edit fields popup for the guest.', 'web-to-print-online-designer' ); ?></p>
        <p><?php echo sprintf(__( 'To enable <a target="_blank" href="%s">template mapping</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=frontend#nbdesigner_enable_template_mapping'))); ?></p>
        <form method="post">
            <table class="widefat" id="nbtm-fields">
                <thead>
                    <tr>
                        <th><?php _e( 'Field Name', 'web-to-print-online-designer' ); ?></th>
                        <th><?php _e( 'Connect field to', 'web-to-print-online-designer' ); ?></th>
                        <th><?php _e( 'Action', 'web-to-print-online-designer' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if( count($fields) ):
                        $index = 0;
                        foreach( $fields as $k => $field ): 
                    ?>
                    <tr data-index="<?php echo $index; ?>" >
                        <td><input maxlength="50" class="field_name" name="field_name[]" style="width: 200px;" type="text" value="<?php echo $field['name']; ?>"/></td>
                        <td>
                            <select class="field_connect_to" name="field_connect_to[]" style="width: 200px;" value="<?php echo $field['connect_to']; ?>">
                                <?php 
                                    foreach( $connect_fields as $key => $connect_field ): 
                                        $selected = selected($key, $field['connect_to'], false);
                                ?>
                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $connect_field['label']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><span onclick="NBDESIGNADMIN.removeTemplateMappingField( this )" class="button deletion"><span class="dashicons dashicons-no-alt"></span></span></td>
                    </tr>
                    <?php 
                        $index++;
                        endforeach; 
                        else:
                    ?>
                    <tr data-index="0">
                        <td><input maxlength="50" class="field_name" name="field_name[]" style="width: 200px;" type="text" value="<?php _e( 'Field Name', 'web-to-print-online-designer' ); ?>" /></td>
                        <td>
                            <select class="field_connect_to" name="field_connect_to[]" style="width: 200px;" value="">
                                <?php foreach( $connect_fields as $key => $connect_field ): ?>
                                <option value="<?php echo $key; ?>"><?php echo $connect_field['label']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><span onclick="NBDESIGNADMIN.removeTemplateMappingField( this )" class="button deletion"><span style="margin-top: 3px;" class="dashicons dashicons-no-alt"></span></span></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td style="text-align: right;" colspan="3">
                            <input onclick="NBDESIGNADMIN.addTemplateMappingField()" type="button" class="button button-primary" value="<?php _e( '+ Add field', 'web-to-print-online-designer' ); ?>" />
                        </td>
                    </tr>
                </tfoot>
            </table>
            <button type="submit" style="margin-top: 15px;" class="button button-primary"><?php _e( 'Save fields', 'web-to-print-online-designer' ); ?></button>
            <input type="hidden" name="nbtn-admin-action" value="fields-save" />
        </form>
    </div>
    <div class="nbtm-col-6">
        <h2><?php _e( 'vCard', 'web-to-print-online-designer' ); ?></h2>
        <p><?php _e( 'Generate vCard (Virtual Contact File) through QR code.', 'web-to-print-online-designer' ); ?></p>
        <p><?php echo sprintf(__( 'To enable <a target="_blank" href="%s">vCard</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=frontend#nbdesigner_enable_vcard'))); ?></p>
        <form method="post">
            <table class="widefat" id="nbvc-fields">
                <thead>
                    <tr>
                        <th><?php _e( 'Field Name', 'web-to-print-online-designer' ); ?></th>
                        <th><?php _e( 'Connect field to', 'web-to-print-online-designer' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach( $vcard_field_options as $vkey => $vcard_field ): ?>
                    <tr>
                        <td><?php echo $vcard_field['label']; ?></td>
                        <td>
                            <select name="<?php echo $vkey; ?>" style="width: 200px;">
                            <?php 
                                foreach( $vcard_field['options'] as $key => $option ): 
                                    $selected = selected($key, isset( $vcard_fields[ $vkey ] ) ? $vcard_fields[ $vkey ] : '', false);
                            ?>
                                <option value="<?php echo $key; ?>" <?php echo $selected; ?>><?php echo $option['label']; ?></option>
                            <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td style="text-align: right;" colspan="2">
                            <button type="submit" class="button button-primary"><?php _e( 'Save vCard', 'web-to-print-online-designer' ); ?></button>
                        </td>
                    </tr>
                </tfoot>
            </table>
            <input type="hidden" name="nbtn-admin-action" value="vcard-save" />
        </form>
   </div>
</div>