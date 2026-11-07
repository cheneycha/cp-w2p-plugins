<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly   ?>
<div class="nbdg-guideline-wrap">
    <div class="nbdg-guideline-desc">
        <?php echo wpautop( htmlspecialchars_decode( $description ) ); ?>
    </div>
    <div class="nbdg-guideline-files">
        <?php if ( $files ): ?>
        <div class="nbdg-guideline-title"><?php esc_html_e( 'Download a Design Guideline', 'web-to-print-online-designer' ); ?></div>
        <div class="nbdg-guideline">
            <div class="nbdg-guideline-inner">
                <ul class="file-types__list">
                <?php foreach ( $files as $file ): ?>
                    <li class="file-types__item">
                        <a href="<?php echo esc_url( $file['file'] ); ?>" class="file-types__link" download>
                            <div class="file-types__file <?php echo '-' . $file['ext']; ?>" data-file-type="<?php echo( $file['ext'] ); ?>">
                                <div class="file-types__icon-mask">
                                    <svg viewBox="0 0 48 48" class="svg-icon" role="presentation" aria-hidden="true" ><path d="M10 42h28v2H10zM23 4.008v28.649L11.844 21.97l-1.384 1.444L24 36.385l13.54-12.971-1.384-1.444L25 32.657V4.008h-2z"></path></svg>
                                </div>
                                <div class="file-types__label"><?php esc_html_e( $file['name'] ); ?></div>
                            </div>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>