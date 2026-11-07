<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<input type="hidden" id="nbdq_action" name="nbdq[action]" value=""/>
<div class="nbd-metabox clearfix">
    <label for="nbdq_customer_name" class="nbd-metabox-label"><?php _e( 'Customer\'s name', 'web-to-print-online-designer' ); ?></label>
    <div class="nbd-metabox-value">
        <input type="text" class="short" id="nbdq_customer_name" name="nbdq[_raq_customer_name]" value="<?php echo $customer_name;?>"/>
    </div>
</div>
<div class="nbd-metabox clearfix">
    <label for="nbdq_customer_email" class="nbd-metabox-label"><?php _e( 'Customer\'s email', 'web-to-print-online-designer' ); ?></label>
    <div class="nbd-metabox-value">
        <input type="text" class="short" id="nbdq_customer_email" name="nbdq[_raq_customer_email]" value="<?php echo $customer_email;?>"/>
    </div>
</div>
<div class="nbd-metabox clearfix">
    <label for="nbdq_customer_message" class="nbd-metabox-label"><?php _e( 'Customer\'s message', 'web-to-print-online-designer' ); ?></label>
    <div class="nbd-metabox-value">
        <textarea cols="50" rows="5" type="text" id="nbdq_customer_message" name="nbdq[_raq_customer_message]" ><?php echo $customer_message; ?></textarea>
    </div>
</div>
<div class="nbd-metabox clearfix">
    <label for="nbdq_admin_message" class="nbd-metabox-label"><?php _e( 'Admin\'s message', 'web-to-print-online-designer' ); ?></label>
    <div class="nbd-metabox-value">
        <textarea cols="50" rows="5" type="text" id="nbdq_admin_message" name="nbdq[_raq_admin_message]" ><?php echo $admin_message; ?></textarea>
    </div>
</div>
<div class="nbd-metabox clearfix">
    <label for="nbdq_expired" class="nbd-metabox-label"><?php _e( 'Expire date (optional)', 'web-to-print-online-designer' ); ?></label>
    <div class="nbd-metabox-value">
        <input type="text" class=" nbd-date-picker" id="nbdq_expired" name="nbdq[_raq_expired]" value="<?php echo $expired; ?>"/>
    </div>
</div>
<div class="nbd-metabox clearfix">
    <label for="nbdq_raq_pay" class="nbd-metabox-label"><?php _e( 'Pay for Quote', 'web-to-print-online-designer' ); ?></label>
    <div class="nbd-metabox-value">
        <input type="hidden" class="short" name="nbdq[_raq_pay]" value="0"/>
        <input type="checkbox" class="short" id="nbdq_raq_pay" name="nbdq[_raq_pay]" value="1" <?php checked( $raq_pay, 1 ); ?> />
        <span><?php _e('Send the customer to <b>"Pay for Quote"</b>', 'web-to-print-online-designer'); ?></span>
    </div>
</div>
<div class="nbd-metabox clearfix">
    <input type="button" class="button button-secondary" id="nbdq_send_quote" value="<?php _e( 'Send Quote', 'web-to-print-online-designer' ); ?>" />
</div>
<hr />
<div class="nbd-metabox clearfix">
    <?php 
        foreach ($raq_request as $key => $value):
            if(is_array($value) ){
    ?>
    <label class="nbd-metabox-label" for="nbdq_customer_<?php echo $value['id'];?>"><?php echo $value['label'];?></label>
    <div class="nbd-metabox-value">
        <?php 
            if(is_array($value['value'])):
                $val = implode('|', $value['value'])
        ?>
        <p id="nbdq_customer_<?php echo $value['id'];?>"><?php echo $val;?></p>
        <?php else: ?>
        <p id="nbdq_customer_<?php echo $value['id'];?>"><?php echo urldecode( $value['value'] );?></p>
        <?php endif; ?>
        <div class="clear"></div>
    </div>
    <?php }; endforeach; ?>
</div>