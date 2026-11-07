<?php if (!defined('ABSPATH')) { exit; }; ?>
<h2><?php esc_html_e('Select a product', 'web-to-print-online-designer'); ?></h2>
<div class="studio-product-wrap">
    <?php foreach( $products as $product ): ?>
    <div class="studio-product" data-id="<?php echo( $product['product_id'] ); ?>" data-template="0" data-collapse="0">
        <div class="studio-product-inner">
            <div class="studio-product-img-wrap">
                <img class="product-img" src="<?php echo esc_url( $product['src'] ); ?>" alt="<?php echo( $product['name'] ); ?>" />
            </div>
            <a href="<?php echo esc_url( $product['url'] ); ?>" target="_blank"><?php echo( $product['name'] ); ?></a>
        </div>
        <div class="point-active"></div>
        <div class="studio-product-templates">
            <div class="loading-wrap">
                <div class="loader">
                    <svg class="circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"></circle>
                    </svg>
                </div>
            </div>
            <div class="studio-product-templates-inner" data-active="0">
                <h3><?php esc_html_e('Select a template', 'web-to-print-online-designer'); ?></h3>
                <span class="nav prev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path fill="currentColor" d="M6.47 4.29l3.54 3.53c.1.1.1.26 0 .36L6.47 11.7a.75.75 0 1 0 1.06 1.06l3.54-3.53c.68-.69.68-1.8 0-2.48L7.53 3.23a.75.75 0 0 0-1.06 1.06z"></path></svg>
                </span>
                <div class="studio-product-templates-slider">
                    <p class="no-template"><?php esc_html_e('No template', 'web-to-print-online-designer'); ?></p>
                </div>
                <span class="nav next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"><path fill="currentColor" d="M6.47 4.29l3.54 3.53c.1.1.1.26 0 .36L6.47 11.7a.75.75 0 1 0 1.06 1.06l3.54-3.53c.68-.69.68-1.8 0-2.48L7.53 3.23a.75.75 0 0 0-1.06 1.06z"></path></svg>
                </span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php
if( $total > $per_page ): 
    require_once NBDESIGNER_PLUGIN_DIR . 'includes/class.nbdesigner.pagination.php';
    $paging = new Nbdesigner_Pagination();
    $url    = getUrlPageNBD( 'studio' );
    $config = array(
        'current_page'  => isset($page) ? $page : 1,
        'total_record'  => $total,
        'limit'         => $per_page,
        'link_full'     => add_query_arg(array('paged' => '{p}'), $url),
        'link_first'    => $url
    );
    $paging->init($config); 
?>
<div class="tablenav top nbdesigner-pagination-con" id="nbd-pagination">
    <div class="tablenav-pages">
        <div>
            <span class="displaying-num"><?php printf( _n( '%s Product', '%s Products', $total, 'web-to-print-online-designer'), number_format_i18n( $total ) ); ?></span>
            <?php echo $paging->html();  ?>
        </div>
        <div class="spacer"></div>
    </div>
</div>  
<?php endif;