<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbd-info-wrap" id="nbd-info-wrap">
    <div class="nbd-info-wrap-inner">
        <div class="nbd-info">
            <a href="<?php echo esc_url( $permalink ); ?>" target="_blank" title="<?php echo( $title ); ?>">
                <img src="<?php echo esc_url( $image ); ?>" alt="<?php echo( $title ); ?>"/>
            </a>
        </div> 
        <div class="nbd-info">
            <h3><a href="<?php echo esc_url( $permalink ); ?>" target="_blank" ><?php echo( $title ); ?></a></h3>
            <p><?php echo $short_description; ?></p>
        </div> 
    </div>
    <div class="pro-description">
        <p class="nbd-description-title"><b><?php esc_html_e('Description', 'web-to-print-online-designer'); ?></b></p>
        <p><?php echo $description; ?></p>
    </div>
</div>    