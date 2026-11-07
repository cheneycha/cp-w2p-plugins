<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<form id="nbdq-form" name="nbdq-form" >
    <?php
        foreach ( $fields as $key => $field ) {
            if ( isset( $field['enabled'] ) && $field['enabled'] ) {
                woocommerce_form_field( $key, $field, NBD_Request_Quote()->get_form_value( $key, $field ) );
            }
        }
        if ( ! is_user_logged_in() && 'yes' == $enable_registration ) :
    ?>
    <div class="woocommerce-account-fields">
        <p class="form-row form-row-wide create-account">
            <label class="woocommerce-form__label woocommerce-form__label-for-checkbox checkbox">
                <input class="woocommerce-form__input woocommerce-form__input-checkbox input-checkbox" id="createaccount" type="checkbox" name="createaccount" value="1"/>
                <span><?php _e( 'Create an account?', 'web-to-print-online-designer' ); ?></span>
            </label>
        </p>
    </div>
    <div class="create-account">
        <?php foreach ( $account_fields as $key => $field ) : ?>
            <?php woocommerce_form_field( $key, $field, '' ); ?>
        <?php endforeach; ?>
        <div class="clear"></div>
    </div>
    <?php 
        endif; 
        if( nbdesigner_get_option('nbdesigner_enable_recaptcha_quote', 'no') == 'yes' && nbdesigner_get_option('nbdesigner_recaptcha_key', '') != '' && nbdesigner_get_option('nbdesigner_recaptcha_secret_key', '') != '' ):
    ?>
    <p class="form-row form-row form-row-wide">
        <div class="g-recaptcha" id="recaptcha_quote" data-callback="nbdqRecaptchaCallback" data-sitekey="<?php echo nbdesigner_get_option('nbdesigner_recaptcha_key'); ?>"></div>
    </p>
    <?php endif; ?>
    <p class="form-row form-row-wide" style="display: flex; align-items: center">
        <input type="hidden" id="nbdq-mail-wpnonce" name="nbdq_mail_wpnonce" value="<?php echo wp_create_nonce( 'nbdq-form-request' ) ?>">
        <input class="button raq-send-request" type="submit" style="width: 100%;color: #fff;border-color: #04b591;background-color: #04b591;" value="<?php _e( 'Send quote', 'web-to-print-online-designer' ); ?>">
    </p>
</form>