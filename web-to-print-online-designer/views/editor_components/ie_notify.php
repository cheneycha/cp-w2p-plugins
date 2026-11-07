<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<style>
    .detect-ie-title {
        width: 50%;
        float: left;
        padding: 30px;
        border: 3px double #ddd;
        margin-top: 50px;
        top: 50%;
    }
    .detect-ie-img {
        margin-left: 15px; 
        float: left;
    }
</style>
<p class="detect-ie-title">
    <?php esc_html_e("We have detected that you are using Internet Explorer which isn't compatible with design editor. Please use one of the modern web browsers: Chrome, Firefox, Microsoft Edge, Safari...", 'web-to-print-online-designer'); ?>
</p>
<img class="detect-ie-img" src='<?php echo NBDESIGNER_ASSETS_URL."images/robot.png"; ?>' />