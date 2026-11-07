<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<?php 
    $heading = sprintf( __('The customer design in order %s has been approved!', 'web-to-print-online-designer'), $order_id );
    do_action('woocommerce_email_header', $heading); 
?>
<p><?php esc_html__('Please review order below:', 'web-to-print-online-designer'); ?></p>
<p><?php esc_html__('View order', 'web-to-print-online-designer'); ?>: <a href="<?php echo admin_url("post.php?post=$order_id&action=edit"); ?>"><?php esc_html_e( $order_number ); ?></a></p>
<?php
    $order = new WC_Order($order_id);
    $products = $order->get_items();   
    foreach($products AS $order_item_id => $product){
    $nbd_item_key = wc_get_order_item_meta($order_item_id, '_nbd');
    $nbu_item_key = wc_get_order_item_meta($order_item_id, '_nbu');
    if( $nbd_item_key || $nbu_item_key ): ?>
<p><b><?php esc_html_e( $product['name'] ); ?></b></p>
<?php
    if ( $nbd_item_key ){
        $list_images = Nbdesigner_IO::get_list_images(NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key, 1);
?>
    <p><?php esc_html__('Custom designs', 'web-to-print-online-designer'); ?></p>
    <div>
    <?php
        foreach( $list_images as $image ):
        $image_url = Nbdesigner_IO::wp_convert_path_to_url($image);
    ?>
        <img style="width: 200px; margin-right: 15px;" src="<?php echo esc_url( $image_url ); ?>" alt="design"/>
    <?php endforeach; ?>
    </div>
<?php } ?>
<?php
    if($nbu_item_key){ 
    $files = Nbdesigner_IO::get_list_files( NBDESIGNER_UPLOAD_DIR .'/'. $nbu_item_key );
?>
    <p><?php esc_html__('Upload designs', 'web-to-print-online-designer'); ?></p>
    <?php
        foreach( $files as $file ):
        $file_url = Nbdesigner_IO::wp_convert_path_to_url($file);
    ?>
    <a href="<?php echo esc_url( $file_url ); ?>" download><?php echo basename($file); ?></a>
    <?php endforeach; ?>
<?php }endif;}; ?>
<?php do_action('woocommerce_email_footer');