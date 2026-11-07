<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div class="nbd-warning-permission">
    <p><img src="<?php echo NBDESIGNER_PLUGIN_URL . 'assets/images/dinosaur.png'; ?>" /></p>
    <p><?php esc_html_e('You do not have permission to access this page!', 'web-to-print-online-designer'); ?> </p>
    <p><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e('Back', 'web-to-print-online-designer') ?></a></p>
</div>
