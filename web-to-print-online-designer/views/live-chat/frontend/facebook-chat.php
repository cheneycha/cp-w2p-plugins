<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<script>
    window.fbAsyncInit = window.fbAsyncInit || function() {
        FB.init({
            xfbml            : true,
            version          : 'v7.0'
        });
    };

    (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = 'https://connect.facebook.net/en_US/sdk/xfbml.customerchat.js';
    fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
</script>
<div class="fb-customerchat"
    attribution=setup_tool
    page_id="<?php echo $nbc_fb_page_id; ?>"
    theme_color="<?php echo $nbc_fb_page_theme_color; ?>"
    logged_in_greeting="<?php echo $nbc_fb_page_login_greeting; ?>"
    logged_out_greeting="<?php echo $nbc_fb_page_logout_greeting; ?>">
</div>