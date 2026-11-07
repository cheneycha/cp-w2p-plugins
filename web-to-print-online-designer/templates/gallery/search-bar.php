<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
$search_type    = isset( $_GET['search_type'] ) && $_GET['search_type'] != '' ? $_GET['search_type'] : '';
$selected_type  = $search_type == 'design' ? __( 'Design name', 'web-to-print-online-designer') : ( $search_type == 'artist' ? __( 'Artist', 'web-to-print-online-designer') : __( 'All', 'web-to-print-online-designer') );
?>
<script type="text/template" id="tmpl-nbdl-search-bar">
    <div class="nbdl-search-bar">
        <label class="nbdl-search-content-wrap">
            <input id="nbdl-search-content" placeholder="<?php esc_attr_e( 'Search design name or artist', 'web-to-print-online-designer'); ?>"/>
            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"/><path d="M0 0h24v24H0z" fill="none"/></svg>
        </label>
        <div class="nbdl-search-type-wrap">
            <span class="nbdl-search-type-selected"><?php echo $selected_type; ?></span>
            <svg xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/><path d="M0 0h24v24H0V0z" fill="none"/></svg>
            <ul id="nbdl-search-type">
                <li data-value="" class="<?php echo $search_type == '' ? 'active' : ''; ?>" ><?php esc_html_e( 'All', 'web-to-print-online-designer'); ?></li>
                <li data-value="design" class="<?php echo $search_type == 'design' ? 'active' : ''; ?>" ><?php esc_html_e( 'Design name', 'web-to-print-online-designer'); ?></li>
                <li data-value="artist" class="<?php echo $search_type == 'artist' ? 'active' : ''; ?>" ><?php esc_html_e( 'Artist', 'web-to-print-online-designer'); ?></li>
            </ul>
        </div>
    </div>
</script>