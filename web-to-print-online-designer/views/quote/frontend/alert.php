<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<div class="nbd-alert" id="nbdq-alert-popup" data-animate="scale">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <div class="nbd-alert-head">
            <h3 class=""><?php _e( 'Successfully!', 'web-to-print-online-designer' ); ?></h3>
            <i class="close-popup">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <title>close</title>
                    <path d="M18.984 6.422l-5.578 5.578 5.578 5.578-1.406 1.406-5.578-5.578-5.578 5.578-1.406-1.406 5.578-5.578-5.578-5.578 1.406-1.406 5.578 5.578 5.578-5.578z"></path>
                </svg>
            </i>
        </div>
        <div class="nbd-alert-body">
            <div class="nbd-alert-wrapper">
                <p><?php _e( 'Your request for a quote has been sent to admin. Please wait the admin proposal via email!', 'web-to-print-online-designer' ); ?></p>
                <div class="nbd-alert-action">
                    <a class="button" href="<?php echo get_permalink( wc_get_page_id( 'shop' ) );?>"><?php _e('Return to shop', 'web-to-print-online-designer'); ?></a>
                    <a id="nbdq-alert-detail-link" class="button" href="#"><?php _e('View quote', 'web-to-print-online-designer'); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
