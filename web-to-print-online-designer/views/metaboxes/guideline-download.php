<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<tr>
    <td class="sort"></td>
    <td class="file_name">
        <input type="text" class="input_text" placeholder="<?php esc_html_e('File name', 'web-to-print-online-designer'); ?>" name="_nbdg_file_names[]" value="<?php echo esc_attr( $file['name'] ); ?>" />
    </td>
    <td class="file_ext">
        <input type="text" class="input_text" placeholder="<?php esc_html_e('ext', 'web-to-print-online-designer'); ?>" name="_nbdg_file_exts[]" value="<?php echo esc_attr( $file['ext'] ); ?>" />
    </td>
    <td class="file_url"><input type="text" class="input_text" placeholder="<?php esc_html_e('http://', 'web-to-print-online-designer'); ?>" name="_nbdg_file_urls[]" value="<?php echo esc_attr( $file['file'] ); ?>" /></td>
    <td class="file_url_choose" width="1%"><a href="#" class="button nbdg_upload_file_button" data-choose="<?php esc_html_e('Choose file', 'web-to-print-online-designer'); ?>" data-update="<?php esc_html_e('Insert file URL', 'web-to-print-online-designer'); ?>"><?php esc_html_e('Choose file', 'web-to-print-online-designer'); ?></a></td>
    <td width="1%"><a href="#" class="delete"><?php esc_html_e('Delete', 'web-to-print-online-designer'); ?></a></td>
</tr>