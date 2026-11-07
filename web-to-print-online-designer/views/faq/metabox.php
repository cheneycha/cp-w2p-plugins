<?php
    if ( !defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly
    }
?>
<style>
    .nbf-form {
        padding: 5px 20px 5px 162px!important;
        margin: 9px 0;
    }
    .nbf-form label{
        float: left;
        width: 150px;
        padding: 0;
        margin: 0 0 0 -150px;
        vertical-align: middle;
    }
</style>
<?php wp_nonce_field('nbf_setting_box', 'nbf_setting_box_nonce'); ?>
<div class="nbf-form">
    <label><?php esc_html_e('Up Vote', 'web-to-print-online-designer'); ?></label>
    <input type="text" name="_nbf[up_vote]" value="<?php echo $nbf['up_vote']; ?>" />
</div>
<div class="nbf-form">
    <label><?php esc_html_e('Down Vote', 'web-to-print-online-designer'); ?></label>
    <input type="text" name="_nbf[down_vote]" value="<?php echo $nbf['down_vote']; ?>" />
</div>