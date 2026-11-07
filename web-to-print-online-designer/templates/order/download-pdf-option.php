<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} ?>
<div class="nbd-pdf-options nbd-hide" style="display: none;">
    <div class="nbd-pdf-options-inner">
        <h3><?php esc_html_e('Option for PDF files', 'web-to-print-online-designer'); ?></h3>
        <div class="md-checkbox">
            <input id="nbd-show-bleed" type="checkbox">
            <label for="nbd-show-bleed" class=""><?php esc_html_e('Show crop marks and bleed', 'web-to-print-online-designer'); ?></label>
        </div>
        <div class="md-checkbox">
            <input id="nbd-multi-file" type="checkbox">
            <label for="nbd-multi-file" class=""><?php esc_html_e('Save to multiple files', 'web-to-print-online-designer'); ?></label>
        </div>
        <div>
            <a hef="javascript: void(0)" class="nbd-button nbd_download_pdf_type" onclick="NBDESIGNERPRODUCT.change_nbd_download_pdf_type( this )"><?php esc_html_e('Select', 'web-to-print-online-designer'); ?></a>
        </div>
    </div>
</div>	
