<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="nbdq-popup" id="nbdq-form-popup" data-animate="scale">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <div class="nbdq-popup-head">
            <h3 class="nbdq-head"><?php _e( 'Send the request', 'web-to-print-online-designer' ); ?></h3>
            <i class="close-popup">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <title>close</title>
                    <path d="M18.984 6.422l-5.578 5.578 5.578 5.578-1.406 1.406-5.578-5.578-5.578 5.578-1.406-1.406 5.578-5.578-5.578-5.578 1.406-1.406 5.578 5.578 5.578-5.578z"></path>
                </svg>
            </i>
        </div>
        <div class="nbdq-popup-body">
            <div class="nbdq-notification">

            </div>
            <div class="nbdq-form-wrapper">
                <?php include_once(NBDESIGNER_PLUGIN_DIR . 'views/quote/frontend/quote-form.php'); ?>
            </div>
        </div>
    </div>
</div>
