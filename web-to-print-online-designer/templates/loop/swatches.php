<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbo-archive-swatches-wrap">
    <?php foreach( $archive_options as $swatches ): ?>
    <div class="nbo-swatches-wrap">
    <?php foreach( $swatches as $swatch ): ?>
        <?php if( $swatch['display_type'] == 's' ): ?>
        <div class="nbo-swatch-wrap">
            <span class="nbo-swatch" 
                style="<?php if( $swatch['preview_type'] == 'i' ){echo 'background: url('.$swatch['preview'] . ') 0% 0% / cover';}else{ echo 'background: '.$swatch['color']; }; ?>" 
                <?php if( isset($swatch['srcset']) ): ?>
                data-srcset="<?php echo $swatch['srcset']; ?>"
                data-src="<?php echo $swatch['src']; ?>"
                <?php endif; ?>>
                <?php 
                    if( $swatch['preview_type'] == 'c' && isset( $swatch['color2'] ) ):
                    $style = "border-bottom-color:{$swatch['color']};border-left-color:{$swatch['color2']}";
                ?>
                <span class="nbo-swatch-bicolor" style="<?php echo $style; ?>"></span>
                <?php endif; ?>
            </span>
            <span class="nbo-swatch-tooltip">
                <span><?php echo $swatch['name']; ?></span>
            </span>
        </div>
        <?php else: ?>
        <div class="nbo-swatch-label-wrap"
            <?php if( isset($swatch['srcset']) ): ?>
            data-srcset="<?php echo $swatch['srcset']; ?>"
            data-src="<?php echo $swatch['src']; ?>"
            <?php endif; ?>>
            <span class="nbo-swatch-label" >
                <?php echo $swatch['name']; ?>
            </span>
        </div>
        <?php endif; ?>
    <?php endforeach; ?>  
    </div>
    <?php endforeach; ?>
</div>
