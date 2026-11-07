<?php
    if ( !defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
?>
<h1><?php esc_html_e('FAQs management', 'web-to-print-online-designer'); ?></h1>
<div class="wrap">
    <div class="nbf-box">
        <div class="nbf-header"><?php esc_html_e('Latest Printing FAQs', 'web-to-print-online-designer'); ?></div>
        <div class="nbf-body">
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Title', 'web-to-print-online-designer'); ?></th>
                        <th><?php esc_html_e('Categories', 'web-to-print-online-designer'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach( $new_faqs as $faq ): ?>
                    <tr>
                        <td><a href="post.php?post=<?php echo $faq->ID;?>&action=edit"><?php echo $faq->post_title; ?></a></td>
                        <td><?php echo get_the_term_list( $faq->ID, 'nbd_faq_category', '', ', ', '' ) . PHP_EOL; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="nbf-footer">
            <a class="button button-primary button-large" href="edit.php?post_type=nbd_faq"><?php esc_html_e('View all', 'web-to-print-online-designer'); ?></a>
            <a class="button button-primary button-large" href="post-new.php?post_type=nbd_faq"><?php esc_html_e('Add New', 'web-to-print-online-designer'); ?></a>
        </div>
    </div>
    <div class="nbf-box">
        <div class="nbf-header"><?php esc_html_e('FAQ Categories', 'web-to-print-online-designer'); ?></div>
        <div class="nbf-body">
            <table class="wp-list-table widefat fixed striped posts">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Title', 'web-to-print-online-designer'); ?></th>
                        <th><?php esc_html_e('Count', 'web-to-print-online-designer'); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    $walker = new NBF_List_Category();
                    echo call_user_func_array( array( &$walker, 'walk' ), array( $categories, 0, array() ) );
                ?>
                </tbody>
            </table>
        </div>
        <div class="nbf-footer">
            <a class="button button-primary button-large" href="edit-tags.php?taxonomy=nbd_faq_category&post_type=nbd_faq"><?php esc_html_e('View all', 'web-to-print-online-designer'); ?></a>
        </div>
    </div>
    <div class="nbf-box large">
        <div class="nbf-header"><?php esc_html_e('FAQ - Helper in live chat', 'web-to-print-online-designer'); ?></div>
        <div class="nbf-body">
            <div><?php esc_html_e('Select FAQs and categories to display in live chat helper:', 'web-to-print-online-designer'); ?></div>
            <div class="nbf-wrap">
                <div class="nbf-float">
                    <div>
                        <select id="nbf-categories">
                        <?php 
                            $walker = new NBF_Dropdown_Category();
                            echo call_user_func_array( array( &$walker, 'walk' ), array( $categories, 0, array() ) );
                        ?>
                        </select>
                        <a class="button button-primary" id="nbf-add-cat"><?php esc_html_e('Add Category', 'web-to-print-online-designer'); ?></a>
                    </div>
                    <div>
                        <div class="nbf-table-wrap nbf-table-wrapper" >
                            <?php include( NBDESIGNER_PLUGIN_DIR . 'views/faq/faq-table.php' ); ?>
                        </div>
                        <a class="button button-primary" id="nbf-add-faqs"><?php esc_html_e( 'Add FAQs', 'web-to-print-online-designer' ); ?></a>
                    </div>
                </div>
                <div class="nbf-float">
                    <div><?php esc_html_e('Selected FAQs and categories', 'web-to-print-online-designer'); ?></div>
                    <div class="nbf-table-wrapper">
                        <table class="wp-list-table widefat fixed posts faqs faqs-selected">
                            <thead>
                                <tr>
                                    <th class="sort">&nbsp;</th>
                                    <th class="check">&nbsp;</th>
                                    <th><?php esc_html_e('Title', 'web-to-print-online-designer'); ?></th>
                                    <th><?php esc_html_e('Type', 'web-to-print-online-designer'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach( $selected_faqs as $faq ): ?>
                                <tr data-id="<?php echo $faq['type']; ?>_<?php echo $faq['id']; ?>">
                                    <td class="sort"></td>
                                    <th class="check"><input type="checkbox" data-type="<?php echo $faq['type']; ?>" value="<?php echo $faq['id']; ?>"/></th>
                                    <td><a href="<?php echo $faq['url'];?>"><?php echo $faq['name']; ?></a></td>
                                    <td><?php echo $faq['type_name']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="faqs-selected-actions">
                        <a class="button" id="nbf-remove-faqs"><?php esc_html_e('Remove', 'web-to-print-online-designer'); ?></a>
                        <a class="button button-primary button-large" id="nbf-update-helper-list" ><?php esc_html_e('Update', 'web-to-print-online-designer'); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var admin_ajax = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
    jQuery(document).ready(function(){
        nbfObj.initSortable();
        
        jQuery('#nbf-categories').on('change', function(){
            var cat_id = jQuery(this).val(),
            data = 'cat_id=' + cat_id + '&action=nbf_get_faqs_of_category';

            jQuery.post(admin_ajax, data, function(response) {
                jQuery('.nbf-table-wrap').html('').append( response );
            });
        });

        jQuery('#nbf-add-cat').on('click', function(e){
            e.preventDefault();

            var id = jQuery('#nbf-categories').val();
            if( !nbfObj.isExist( id, 'cat' ) ){
                var name = jQuery('#nbf-categories option:selected' ).text().replace('—', '').trim();
                nbfObj.addRow( id, 'cat', name );
            }
        });

        jQuery('#nbf-add-faqs').on('click', function(e){
            e.preventDefault();
            var ids = [];

            jQuery('.faqs-availabled input[type="checkbox"]').each(function() {
                if ( jQuery(this).is( ':checked' ) ) ids.push( jQuery( this ).val() );
                jQuery(this).prop('checked', false);
            });

            ids.forEach(function( id ){
                if( !nbfObj.isExist( id, 'faq' ) ){
                    var name = jQuery('.faqs-availabled tr[data-id="' + id + '"] .nbf-title' ).text();
                    nbfObj.addRow( id, 'faq', name );
                }
            });
        });

        jQuery('#nbf-remove-faqs').on('click', function(e){
            e.preventDefault();

            var ids = [];
            jQuery('.faqs-selected input[type="checkbox"]').each(function() {
                if ( jQuery(this).is( ':checked' ) ) ids.push( jQuery( this ).val() );
                jQuery(this).prop('checked', false);
            });

            ids.forEach(function( id ){
                var el = jQuery('.faqs-selected input[value="' + id + '"]'),
                type = el.attr('data-type');

                nbfObj.removeRow( type + '_' + id );
            });
        });

        jQuery('#nbf-update-helper-list').on('click', function(e){
            e.preventDefault();

            var ids = [];
            jQuery('.faqs-selected input[type="checkbox"]').each(function() {
                var id = jQuery( this ).val(),
                type = jQuery( this ).attr('data-type');
                ids.push( type + '_' + id );
            });

            var data = 'action=nbf_update_live_chat_helper&ids=' + JSON.stringify( ids );
            jQuery.post(admin_ajax, data, function(response) {
                console.log(response);
                alert('Successfully!');
            });
        });
    });
    var nbfObj = {
        initSortable: function(){
            jQuery( '.faqs-selected tbody' ).sortable({
                items: 'tr',
                cursor: 'move',
                axis: 'y',
                handle: 'td.sort',
                scrollSensitivity: 40,
                forcePlaceholderSize: true,
                helper: 'clone',
                opacity: 0.65
            });
        },
        isExist: function( id, type ){
            var _id = type + '_' + id;
            return jQuery('.faqs-selected tbody tr[data-id="' + _id + '"]').length > 0;
        },
        addRow: function( id, type, name ){
            var _type = type == 'cat' ? '<?php esc_html_e('Category', 'web-to-print-online-designer'); ?>' : '<?php esc_html_e('FAQ', 'web-to-print-online-designer'); ?>';
            jQuery('.faqs-selected tbody').append('<tr data-id="' + type + '_' + id + '"><td class="sort"></td><th class="check"><input type="checkbox" data-type="' + type + '" value="' + id + '"/></th><td>' + name + '</td><td>' + _type + '</td></tr>');
            nbfObj.initSortable();
        },
        removeRow: function( _id ){
            jQuery('.faqs-selected tbody tr[data-id="' + _id + '"]').remove();
            nbfObj.initSortable();
        }
    };
</script>