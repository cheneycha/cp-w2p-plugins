<?php
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
?>
<div id="nbdq-message"><?php do_action( 'nbdq_raq_message' ) ?></div>
<a class="button" href="<?php echo get_permalink( wc_get_page_id( 'shop' ) );?>"><?php _e('Return to shop', 'web-to-print-online-designere'); ?></a>

