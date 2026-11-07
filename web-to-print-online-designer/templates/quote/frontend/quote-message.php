<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if( isset($message) && $message != ''):
?>
<p><?php echo $message ?></p>
<?php 
endif; 
if( isset($confirm) && $confirm == 'no' ):
?>
<p><?php printf( __('Are you sure you want to reject quote No. %d?' , 'web-to-print-online-designere'), $quote_id ) ?></p>
<form method="post" >
    <input type="hidden" name="action" value="reject" />
    <input type="hidden" name="raq_nonce" value="<?php echo $raq_nonce ?>" />
    <input type="hidden" name="quote_id" value="<?php echo $quote_id ?>" />
    <input type="hidden" name="confirm" value="yes" />
    <p>
        <label for="reason"><?php _e('Please, feel free to send us your feedback/reasons:', 'web-to-print-online-designere'); ?> </label>
        <textarea name="reason" id="" cols="10" rows="3"></textarea>
    </p>
    <input type="submit" class="button" value="<?php _e('Yes, I want to reject the quote', 'web-to-print-online-designere' ); ?>" />
</form>
<?php endif;