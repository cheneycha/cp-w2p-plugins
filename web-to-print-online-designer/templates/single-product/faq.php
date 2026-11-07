
<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly   ?>
<div class="nbd-faqs-wrap">
    <?php foreach( $faqs as $key => $faq ): ?>
    <div class="nbd-faq-wrap <?php echo $key == 0 ? 'active' : ''; ?>">
        <div class="nbd-faq-head">
         <?php echo $faq['title']; ?>
        </div>
        <div class="nbd-faq-body">
            <div class="nbd-faq-body-inner">
                <?php echo wpautop( htmlspecialchars_decode( $faq['content'] ) ); ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>