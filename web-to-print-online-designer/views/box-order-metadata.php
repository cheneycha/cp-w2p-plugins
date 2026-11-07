<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<?php if( 1 ) :  
    $count_img_design = 0;
?>
<div id="nbdesigner_order_info">
    <?php foreach($products AS $order_item_id => $product): ?>
        <?php 
            $nbd_item_key = wc_get_order_item_meta( $order_item_id, '_nbd' );
            $nbu_item_key = wc_get_order_item_meta( $order_item_id, '_nbu' );
            if( is_woo_v305() ){
                $item_meta = new WC_Order_Item_Product( $product );
            }else {
                $item_meta = new WC_Order_Item_Meta( $product );
            }
            if( $nbd_item_key || $nbu_item_key ): 
            $index_accept = 'nbds_' . $order_item_id;
            $variation = '';
            if( !is_woo_v3() ){
                $variation = $item_meta->display($flat=true,$return=true);
            }

            $synchronized = false;
            if( $nbd_item_key ){
                if( wc_get_order_item_meta( $order_item_id, '_nbd_synchronized' ) ){
                    $synchronized = true;
                }
            }
        ?>
        <div>
            <h4 class="nbdesigner_order_product_name">
                <?php esc_html_e( $product['name'] ); ?>
                <?php echo (!empty($variation))?'<span> - '.$variation.'</span>':''; ?>
                <?php if( $synchronized ): ?>
                    <a class="button-secondary button button-small nbd-synchronized"><?php esc_attr_e('Synchronized','web-to-print-online-designer') ?> <span class="dashicons dashicons-yes"></span></a>
                <?php endif; ?>
            </h4>
            <?php if($nbd_item_key): ?>
            <p><b><?php esc_html_e('Custom design','web-to-print-online-designer') ?></b></p>
            <div class="nbdesigner_container_item_order <?php if(isset($data_designs[$index_accept])) { $status = ($data_designs[$index_accept] == 'accept') ? 'approved' : 'declined'; echo( $status );}; ?>">
            <?php 
                $list_images = Nbdesigner_IO::get_list_images(NBDESIGNER_CUSTOMER_DIR .'/'. $nbd_item_key .'/preview', 1);
                asort( $list_images );
                if(count($list_images) > 0):
            ?>
                <input type="checkbox" name="_nbdesigner_design_file[]" class="nbdesigner_design_file" value="<?php echo( $order_item_id ); ?>" />
                <?php foreach($list_images as $key => $image): 
                    $count_img_design++;
                    $src = Nbdesigner_IO::convert_path_to_url($image);
                ?>
                        <img class="nbdesigner_order_image_design" src="<?php echo esc_url( $src ); ?>" />
                <?php endforeach; ?>
                <?php
                    $product_id = $item_meta->get_product_id();
                    $product_id = get_wpml_original_id( $product_id );
                    $arr        = array(
                        'nbd_item_key'  => $nbd_item_key, 
                        'order_id'      => $order_id, 
                        'product_id'    => $product_id, 
                        'item_id'       => $order_item_id, // custom charming
                        'variation_id'  => $item_meta->get_variation_id()
                    );
                    $link_view_detail = add_query_arg( $arr, admin_url( 'admin.php?page=nbdesigner_detail_order' ) );
                ?>
            <?php  endif; ?>
                <a class="nbdesigner-right button button-small button-secondary" href="<?php echo esc_url( $link_view_detail ); ?>"><?php esc_html_e('View detail', 'web-to-print-online-designer'); ?></a>
            </div>
            <?php endif; ?>
            <?php
                if( $nbu_item_key ):
                $files = Nbdesigner_IO::get_list_files( NBDESIGNER_UPLOAD_DIR .'/'. $nbu_item_key );
            ?>
            <p><b><?php esc_html_e('Upload design','web-to-print-online-designer') ?></b></p>
            <div class="nbdesigner_container_item_order <?php if(isset($data_uploads[$index_accept])) { $status = ($data_uploads[$index_accept] == 'accept') ? 'approved' : 'declined'; echo( $status );}; ?>">
                <input type="checkbox" name="_nbdesigner_upload_file[]" class="nbdesigner_design_file" value="<?php echo( $order_item_id ); ?>" />
                <?php foreach( $files as $key => $file ): $count_img_design++; ?>
                <?php if( $key > 0 ) echo ' | '; ?><span><?php echo basename( $file ); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endforeach;?>
    <br />
    <div class="nbdesigner-left nbd-admin-setting-padding-5" >
        <input type="checkbox" class="" id="nbdesigner_order_design_check_all" />
        <label for="nbdesigner_order_design_check_all"><small><?php esc_html_e('Check all', 'web-to-print-online-designer'); ?></small></label>
    </div>
    <div class="nbdesigner-clearfix"></div>
    <div>
        <?php esc_html_e('With selected:', 'web-to-print-online-designer'); ?>
        <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif'; ?>" class="nbdesigner_loaded nbd-admin-setting-order-loading" id="nbdesigner_order_submit_loading" />
        <div class="nbdesigner-right">
            <select name="nbdesigner_order_file_approve" class="">
                <option value="accept"><?php esc_html_e('Accept', 'web-to-print-online-designer'); ?></option>
                <option value="decline"><?php esc_html_e('Decline', 'web-to-print-online-designer'); ?></option>
            </select>
            <a href="#" class="button button-primary" id="nbdesigner_order_file_submit"><?php esc_html_e('GO', 'web-to-print-online-designer'); ?></a>
        </div>
    </div>
    <input type="hidden" name="nbdesigner_design_order_id" value="<?php echo( $order_id ); ?>" />
    <?php wp_nonce_field( 'approve-designs', '_nbdesigner_approve_nonce' ); ?>
    <div class="nbdesigner-clearfix"></div><br />
    <div class="nbdesigner-right nbd-admin-setting-padding-5" >
        <?php do_action('cod_box_order_metadata', $products, $order_id);?>
        <?php if( $count_img_design > 0 ): ?>
            <a href="<?php echo add_query_arg(array('download-all' => 'true', 'order_id' => $order_id), admin_url('admin.php?page=nbdesigner_detail_order')); ?>" class="button button-small button-secondary"><?php esc_html_e('Download all', 'web-to-print-online-designer'); ?></a>
        <?php else: ?>
            <span class="button button-small button-disabled nbd-admin-setting-order-download-all" ><?php esc_html_e('Download all', 'web-to-print-online-designer'); ?></span>
        <?php endif; ?>
    </div>
    <div class="nbdesigner-clearfix"></div>
</div>
<hr />
<div class="nbdesigner_container_order_email" id="nbdesigner_order_email_info">
    <h4><?php esc_html_e('Send notify mail','web-to-print-online-designer'); ?></h4>
    <?php wp_nonce_field('approve-design-email', '_nbdesigner_design_email_nonce'); ?>
    <input type="hidden" name="nbdesigner_design_email_order_id" value="<?php echo( $order_id ); ?>" />
    <div id="nbdesigner_order_email_error" class="nbdesigner_order_email_message hidden"></div>
    <div id="nbdesigner_order_email_success" class="nbdesigner_order_email_message hidden"></div>	
    <div>
        <label for="nbdesigner_design_email_order_content"><?php esc_html_e('Reason accepted / declined:', 'web-to-print-online-designer'); ?></label>
        <textarea name="nbdesigner_design_email_order_content" id="nbdesigner_design_email_order_content" rows="3" class="nbd-admin-setting-width-100"></textarea>
    </div>
    <div class="nbdesigner-right">
        <img src="<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/loading.gif' ?>" class="nbdesigner_loaded nbd-admin-setting-order-loading" id="nbdesigner_order_mail_loading" />
        <select name="nbdesigner_design_email_reason" class="">
            <option value="approved"><?php esc_html_e('Files accepted', 'web-to-print-online-designer'); ?></option>
            <option value="declined"><?php esc_html_e('Files rejected', 'web-to-print-online-designer'); ?></option>
        </select>
        <a href="#" class="button button-primary" id="nbdesigner_uploads_email_submit"><?php esc_html_e('Send mail','web-to-print-online-designer'); ?></a>
    </div>
    <div class="nbdesigner-clearfix"></div>
</div>
<hr />
<div>
    <a href="javascript:void(0)" class="button" id="nbdesigner_delete_order_design"><?php esc_html_e('Delete order design','web-to-print-online-designer'); ?></a>
</div>
<?php else: ?>
<p><?php esc_html_e('No design or uplod file in this order', 'web-to-print-online-designer'); ?></p>
<?php endif;