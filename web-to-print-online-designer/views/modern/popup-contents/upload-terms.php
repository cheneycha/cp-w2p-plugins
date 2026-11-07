<div class="nbd-popup popup-term" data-animate="fixed-top">
    <div class="overlay-popup"></div>
    <div class="main-popup">
        <i class="icon-nbd icon-nbd-clear close-popup"></i>
        <div class="head"><?php esc_html_e('Image upload terms','web-to-print-online-designer'); ?></div>
        <div class="body">
            <div class="main-body">
                <?php echo stripslashes(nbdesigner_get_option('nbdesigner_upload_term')); ?>
            </div>
        </div>
        <div class="footer"></div>
    </div>
</div>
