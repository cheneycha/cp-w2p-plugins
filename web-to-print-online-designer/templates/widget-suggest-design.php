<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly 
if( count($products) ){
echo '<ul class="nbdesigner_list_widget">';
foreach ($products as $p){
    if( $current_id != $p->ID ):
    $pro = wc_get_product($p->ID);
?>
    <li class="nbdesigner-related-product">
        <div class="nbdesigner-related-product-item">
            <a data-id="<?php echo( $pro->get_id() ); ?>" href="<?php echo esc_url( get_permalink( $pro->get_id() ) ); ?>" class="nbdesigner-related-product-image">
                <?php echo $pro->get_image(); ?>
            </a>
            <a href="<?php echo esc_url( get_permalink( $pro->get_id() ) ); ?>"><span class="nbdesigner-related-product-title"><?php echo esc_attr( $pro->get_title() ); ?></span></a>
            <div class="nbdesigner-overlay">
                <img src="<?php echo NBDESIGNER_PLUGIN_URL . 'assets/images/loading.png' ?>" class="rotating"/>
            </div>
        </div>    
    </li>
<?php
    endif;
}
echo '</ul>';
}else{
    esc_html_e('No related product design', 'web-to-print-online-designer');
}
?>