<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
Welcome
<script type="text/javascript">
    <?php if( is_user_logged_in() ): ?>
        var nonce_get = "<?php echo wp_create_nonce('nbdesigner-get-data'); ?>",
        nonce = "<?php echo wp_create_nonce('save-design'); ?>";
        window.location.hash = nonce_get+ '___' +nonce;
    <?php endif; ?>
</script>