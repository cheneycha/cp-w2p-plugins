<?php 
    if (!defined('ABSPATH')) exit;
    $btn_id = 'open_m-upload-design-wrap';
    if( isset( $nboo_archive_mode ) && $nboo_archive_mode ){
        $btn_id = 'nboo-open_m-upload-design-wrap';
    }
?>
<div class="layout__item">
    <div class="layout__item__inner" id="<?php echo $btn_id; ?>">
        <div class="item__layout upload_design">
            <div class="tile__media-wrap">
                <div class="tile-action__image-wrap">
                    <svg viewBox="0 0 49 30" width="100%" height="100%"><g stroke-width=".8" fill="none" fill-rule="evenodd"><path d="M39.793 8.384c.001-.053.004-.106.004-.16 0-2.56-2.061-4.637-4.603-4.637-1.226 0-2.339.485-3.164 1.273A13.124 13.124 0 0 0 22.726 1C16.135 1 10.672 5.86 9.673 12.217c-.055 0-.11-.004-.164-.004-4.7 0-8.509 3.838-8.509 8.572 0 4.59 3.58 8.336 8.08 8.56v.012h28.362c5.822 0 10.542-4.755 10.542-10.62 0-5.052-3.502-9.276-8.191-10.353z" stroke="#4B4F54" stroke-dasharray="2,2" fill="#FFF"></path><path d="M26.597 14.366l-2.054-2.022v11.691h-.749V12.343l-2.053 2.023-.53-.522 2.429-2.39.529-.522.53.522 2.427 2.39-.53.522zm-2.453-.974h.05l-.025-.024-.025.024z" stroke="#52575C" stroke-linejoin="round" fill="#52575C"></path></g></svg>
                </div>
            </div>
            <div class="tile__text-wrap">
                <div class="tile__text-wrap-inner">
                    <h3 class="h__block"><?php esc_html_e('Upload a full design', 'web-to-print-online-designer'); ?></h3>
                    <ul>
                        <li>- <?php esc_html_e('Have a complete design', 'web-to-print-online-designer'); ?></li>
                        <li>- <?php esc_html_e('Have your own designer', 'web-to-print-online-designer'); ?></li>
                    </ul>
                </div>
                <svg class="tile--horizontal__chevron" viewBox="0 0 24 24" width="100%" height="100%"><path d="M10.5 18.5a1 1 0 0 1-.71-1.71l4.8-4.79-4.8-4.79A1 1 0 0 1 11.2 5.8l6.21 6.2-6.21 6.21a1 1 0 0 1-.7.29z"></path></svg>
            </div>
        </div>
    </div>
</div>