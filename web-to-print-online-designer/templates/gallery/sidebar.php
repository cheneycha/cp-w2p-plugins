<?php do_action('nbd_before_gallery_sidebar'); ?>
<div class="nbd-category nbd-sidebar-con">
    <p class="nbd-sidebar-h3"><?php esc_html_e('Design Category', 'web-to-print-online-designer'); ?></p>
    <div class="nbd-sidebar-con-inner">
    <?php
        $walker = new NBD_Category();
        echo "<ul>";
        echo call_user_func_array( array( &$walker, 'walk' ), array( $categories, 0, array() ) );
        echo "</ul>";
    ?>
    </div>
</div>
<div class="nbd-designers nbd-sidebar-con">
    <p class="nbd-sidebar-h3"><?php esc_html_e('Designer', 'web-to-print-online-designer'); ?></p>
    <div class="nbd-sidebar-con-inner">
        <?php foreach( $designers as $designer ): 
            $link_designer  = add_query_arg(array('id' => $designer['art_id']), getUrlPageNBD('designer'));
            $artist_name    = $designer['art_name'] != '' ? $designer['art_name'] : esc_html__('Designer', 'web-to-print-online-designer');
        ?>
        <a href="<?php echo esc_url( $link_designer ); ?>" class="nbd-tag"><span><?php echo( $artist_name ); ?></span></a>
        <?php endforeach; ?>
    </div>
</div>
<div class="nbd-designers nbd-sidebar-con">
    <p class="nbd-sidebar-h3"><?php esc_html_e('Products', 'web-to-print-online-designer'); ?></p>
    <div class="nbd-sidebar-con-inner">
        <div class="nbd-tem-list-product-wrap">
            <ul>
            <?php
            foreach( $products as $key => $product ): 
                $link_prodcut_templates = add_query_arg(array('pid' => $product['product_id']), getUrlPageNBD('gallery'));
            ?>
                <li class="nbd-tem-list-product <?php if($key > 14) echo 'nbd-hide'; ?>">
                    <a class="<?php if($pid == $product['product_id']) echo 'active'; ?>" href="<?php echo esc_url( $link_prodcut_templates ); ?>">
                        <svg class="before" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                            <path fill="none" d="M0 0h24v24H0z"/>
                            <path d="M16.01 11H4v2h12.01v3L20 12l-3.99-4z"/>
                        </svg>
                        <span><?php esc_html_e( $product['name'] ); ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
            </ul>
            <?php if(count($products) > 15): ?>
            <a class="nbd-see-all" href="javascript:void(0)" onclick="showAllProduct( this )"><?php esc_html_e('See All', 'web-to-print-online-designer'); ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="nbd-designers nbd-sidebar-con">
    <p class="nbd-sidebar-h3"><?php esc_html_e('Wishlist', 'web-to-print-online-designer'); ?></p>
    <div class="nbd-sidebar-con-inner wishlist">
        <?php foreach( $fts as $t ): ?>
        <div class="wishlist-tem-wrap" data-id="<?php echo( $t['id'] ); ?>">
            <div class="left" onclick="previewTempalte(event, <?php echo( $t['id'] ); ?>)">
                <img src="<?php echo esc_url( $t['img'] ); ?>" class="nbdesigner-img"/>
            </div>
            <div class="right">
                <div><?php esc_html_e('Template for', 'web-to-print-online-designer'); ?></div>
                <div><?php esc_html_e( $t['title'] ); ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<script>
    var showAllProduct = function(e){
        jQuery(e).hide();
        jQuery('.nbd-tem-list-product-wrap').addClass('see-all');
        jQuery('.nbd-tem-list-product-wrap ul li').removeClass('nbd-hide');
    }
</script>