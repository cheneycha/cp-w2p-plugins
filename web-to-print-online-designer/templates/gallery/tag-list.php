<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<?php if( count($tags) > 0 ): ?>
<div class="nbd-category nbd-sidebar-con">
    <p class="nbd-sidebar-h3"><?php esc_html_e('Tags', 'web-to-print-online-designer'); ?></p>
    <div class="nbd-sidebar-con-inner">
        <ul>
            <?php foreach( $tags as $tag ): ?>
            <li class="nbd-gallery-filter-item">
                <a data-type="tag" data-value="<?php echo( $tag['term_id'] ); ?>" href="#" class="nbd-tag-list-item <?php if( in_array( $tag['term_id'], $filter_tags ) ) echo 'active'; ?>">
                    <svg class="before" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                        <path fill="none" d="M0 0h24v24H0z"/>
                        <path d="M16.01 11H4v2h12.01v3L20 12l-3.99-4z"/>
                    </svg> 
                    <span><?php esc_html_e( $tag['name'] ); ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif; ?>
<?php if( count($colors) > 0 ): ?>
<div class="nbd-category nbd-sidebar-con">
    <p class="nbd-sidebar-h3"><?php esc_html_e('Colors', 'web-to-print-online-designer'); ?></p>
    <div class="nbd-sidebar-con-inner">
        <ul>
            <?php foreach( $colors as $c ): ?>
            <li class="nbd-color-list-item">
                <span data-type="color" style="background: #<?php echo( $c ); ?>" data-value="<?php echo( $c ); ?>" class="nbd-color-list-item-inner <?php if( in_array( $c, $filter_colors ) ) echo 'active'; ?>"></span>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php endif;