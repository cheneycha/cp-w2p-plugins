<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
wp_enqueue_media();
?>
<tr class="">
    <th scope="row" valign="top"><label><?php _e('Featured', 'web-to-print-online-designer'); ?></label></th>
    <td>
        <input type="checkbox" name="featured" <?php checked( $featured, 1 ); ?>/>
    </td>
</tr>
<tr class="form-field">
    <th scope="row" valign="top"><label><?php _e('Thumbnail', 'web-to-print-online-designer'); ?></label></th>
    <td>
        <div class="form-field">
            <input type="hidden" id="template_tag_thumbnail_id" name="template_tag_thumbnail_id" value="<?php echo $thumbnail_id; ?>" />
            <button type="button" class="upload_image_button button"><?php _e('Upload/Add image', 'web-to-print-online-designer'); ?></button>
            <button type="button" class="remove_image_button button"><?php _e('Remove image', 'web-to-print-online-designer'); ?></button>
            <img src="<?php echo $image; ?>" class="tags_thumbnail_image" style="width: 50px; max-height: 50px; padding: 2px; border: 2px solid #ccc; border-radius: 4px;" />
        </div>
        <style>
            .column-handle {
                display: none;
            }
        </style>
        <script type="text/javascript">
            jQuery( document ).ready(function(){
                if ( ( parseInt( jQuery('#template_tag_thumbnail_id').val() ) == 0) ) jQuery('.remove_image_button').hide();
                var file_frame;
                jQuery('.upload_image_button').on('click', function (event) {
                    event.preventDefault();
                    if (file_frame) {
                        file_frame.open();
                        return;
                    }
                    file_frame = wp.media.frames.file_frame = wp.media({
                        title: '<?php _e('Choose an image', 'web-to-print-online-designer'); ?>',
                        button: {
                            text: '<?php _e('Use image', 'web-to-print-online-designer'); ?>',
                        },
                        library: {
                            type: [ 'image' ]
                        },
                        multiple: false
                    });
                    file_frame.on('select', function () {
                        var attachment = file_frame.state().get('selection').first().toJSON();
                        var url = attachment.url;
                        if( attachment.sizes && attachment.sizes.thumbnail ){
                            url = attachment.sizes.thumbnail.url;
                        }
                        jQuery('#template_tag_thumbnail_id').val(attachment.id);
                        jQuery('.tags_thumbnail_image').attr('src', url);
                        jQuery('.remove_image_button').show();
                    });
                    file_frame.open();
                });
                jQuery(document).on('click', '.remove_image_button', function (event) {
                    jQuery('#template_tag_thumbnail_id').val(0);
                    jQuery('.tags_thumbnail_image').attr('src', "<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png'; ?>");
                    jQuery('.remove_image_button').show();
                });
            });
            jQuery( document ).ajaxComplete( function( event, request, options ) {
                if ( request && 4 === request.readyState && 200 === request.status
                    && options.data && 0 <= options.data.indexOf( 'action=add-tag' ) ) {
                    var res = wpAjax.parseAjaxResponse( request.responseXML, 'ajax-response' );
                    if ( ! res || res.errors ) {
                        return;
                    }
                    // Clear Thumbnail fields on submit
                    jQuery('.tags_thumbnail_image').attr('src', "<?php echo NBDESIGNER_ASSETS_URL . 'images/placeholder.png'; ?>");
                    jQuery('#template_tag_thumbnail_id').val(0);
                    jQuery( '.remove_image_button' ).hide();
                    jQuery( '#display_type' ).val( '' );
                    return;
                }
            } );
        </script>
    </td>
</tr>