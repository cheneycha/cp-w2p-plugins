<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbd-upload-inner">
    <h2><?php _e('Upload design', 'web-to-print-online-designer'); ?></h2>
    <?php
        $login_required = ( nbdesigner_get_option( 'nbdesigner_upload_file_php_logged_in', 'no' ) !== 'no' && !is_user_logged_in() ) ? 1 : 0;
        if( $login_required ):
            $login_url      = get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) . '?nbu_redirect=' . $product->get_permalink();
    ?>
    <div class="nbu-require-login">
        <p><?php esc_html_e( 'You need to be logged in to upload design!', 'web-to-print-online-designer' ); ?></p>
        <a class="nbu-login-btn" href="<?php echo $login_url; ?>"><?php esc_html_e( 'Login', 'web-to-print-online-designer' ); ?></a>
    </div>
    <?php else: ?>
    <div class="nbu-upload-zone">
        <input type="file" id="nbd-file-upload" autocomplete="off" class="nbu-inputfile"/> 
        <label for="nbd-file-upload">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="17" viewBox="0 0 20 17">
                <path d="M10 0l-5.2 4.9h3.3v5.1h3.8v-5.1h3.3l-5.2-4.9zm9.3 11.5l-3.2-2.1h-2l3.4 2.6h-3.5c-.1 0-.2.1-.2.1l-.8 2.3h-6l-.8-2.2c-.1-.1-.1-.2-.2-.2h-3.6l3.4-2.6h-2l-3.2 2.1c-.4.3-.7 1-.6 1.5l.6 3.1c.1.5.7.9 1.2.9h16.3c.6 0 1.1-.4 1.3-.9l.6-3.1c.1-.5-.2-1.2-.7-1.5z"> 
                </path>
            </svg>
            <span style="margin-bottom: 10px; margin-top: 10px;"><?php _e('Click or drop file here', 'web-to-print-online-designer'); ?></span>
            <?php if( $option['allow_type'] != '' ): ?><span><small><?php _e('Allow extensions', 'web-to-print-online-designer' ); ?>: <?php echo $option['allow_type']; ?></small></span><?php endif; ?>
            <?php if( $option['disallow_type'] != '' ): ?><span><small><?php _e( 'Disallow extensions', 'web-to-print-online-designer' ); ?>: <?php echo $option['disallow_type']; ?></small></span><?php endif; ?>
            <span><small><?php _e( 'Min size', 'web-to-print-online-designer' ); ?> <?php echo $option['minsize']; ?> MB</small></span>
            <span><small><?php _e( 'Max size', 'web-to-print-online-designer' ); ?> <?php echo $option['maxsize']; ?> MB</small></span>
        </label>
        <svg class="nbd-upload-loading" xmlns="http://www.w3.org/2000/svg" width="50px" height="50px" viewBox="0 0 50 50"><circle fill="none" opacity="0.05" stroke="#000000" stroke-width="3" cx="25" cy="25" r="20"/><g transform="translate(25,25) rotate(-90)"><circle  style="stroke:#48B0F7; fill:none; stroke-width: 3px; stroke-linecap: round" stroke-dasharray="110" stroke-dashoffset="0"  cx="0" cy="0" r="20"><animate attributeName="stroke-dashoffset" values="360;140" dur="2.2s" keyTimes="0;1" calcMode="spline" fill="freeze" keySplines="0.41,0.314,0.8,0.54" repeatCount="indefinite" begin="0"/><animateTransform attributeName="transform" type="rotate" values="0;274;360" keyTimes="0;0.74;1" calcMode="linear" dur="2.2s" repeatCount="indefinite" begin="0"/><animate attributeName="stroke" values="#10CFBD;#48B0F7;#ff0066;#48B0F7;#10CFBD" fill="freeze" dur="3s" begin="0" repeatCount="indefinite"/></circle></g></svg>
    </div>
    <div class="upload-design-preview"></div>
    <div class="submit-upload-design" onclick="hideUploadFrame()"><span><?php _e('Complete', 'web-to-print-online-designer'); ?></span></div>
    <?php endif; ?>
    <?php if( isset( $_enable_upload_without_design ) && $_enable_upload_without_design == '0' ): ?>
    <p style="margin-top: 15px;margin-bottom: 0;color: #2a6496;cursor: pointer;" onclick="backtoOption()">← <?php _e('Back to option', 'web-to-print-online-designer'); ?></p>
    <?php endif; ?>
</div>