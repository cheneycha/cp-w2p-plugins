<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<p>
    <?php if( is_user_logged_in() ): ?>
    <a href="javascript:void(0)" onclick="NBDESIGNERPRODUCT.save_for_later()" class="button alt nbd-save-for-later" id="nbd-save-for-later">
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14">
            <title>check2</title>
            <path fill="#0085ba" d="M13.055 4.422c0 0.195-0.078 0.391-0.219 0.531l-6.719 6.719c-0.141 0.141-0.336 0.219-0.531 0.219s-0.391-0.078-0.531-0.219l-3.891-3.891c-0.141-0.141-0.219-0.336-0.219-0.531s0.078-0.391 0.219-0.531l1.062-1.062c0.141-0.141 0.336-0.219 0.531-0.219s0.391 0.078 0.531 0.219l2.297 2.305 5.125-5.133c0.141-0.141 0.336-0.219 0.531-0.219s0.391 0.078 0.531 0.219l1.062 1.062c0.141 0.141 0.219 0.336 0.219 0.531z"></path>
        </svg>
        <img class="nbd-save-loading hide" src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>"/> 
        <?php _e('Save for later', 'web-to-print-online-designer'); ?>
    </a>
    <?php endif; ?>
    <?php
        $allow_donwload_pdf = false;
        if( $allow_donwload_pdf ):
    ?>
    <a href="javascript:void(0)" onclick="NBDESIGNERPRODUCT.download_pdf()" class="button alt nbd-download-pdf">
        <img class="nbd-pdf-loading hide" src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>"/> 
        <?php _e('Download PDF', 'web-to-print-online-designer'); ?>
    </a>
    <?php endif; ?>
</p>