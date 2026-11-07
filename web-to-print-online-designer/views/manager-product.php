<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div class="nbd-header-with-form">
    <h2 class="nbd-title-page"><?php esc_html_e('Manager NBDesigner Products', 'web-to-print-online-designer'); ?></h2>
    <form action="#" method="POST" class="nbd-header-form">
        <input type="search" name="q" value="<?php echo( $q ); ?>"/>
        <input type="submit" value="<?php esc_attr_e('Search products', 'web-to-print-online-designer'); ?>" class="button" />
    </form>
</div>
<?php add_thickbox(); ?>
<div class="wrap postbox nbdesigner-manager-product">
    <?php if( count($pro) ): ?>
    <div>
    <?php
            global $wpdb;
            foreach($pro as $key => $val):
            $id         = $val["id"];
            $priority   = 'extra';
            $primary    = get_post_meta($id, '_nbdesigner_admintemplate_primary', true);
            if(!$primary) $priority = 'primary';
            $link_manager_template = add_query_arg(array(
                'pid'   => $id,
                'view'  => 'templates'),
                 admin_url('admin.php?page=nbdesigner_manager_product'));
            $link_create_template = add_query_arg(array(
                    'product_id'    => $id,
                    'task'          => 'create',
                    'rd'            => 'admin_templates'
                ), getUrlPageNBD('create'));
            $layout = nbd_get_product_layout($id);
            if( $layout == 'v' ){
                $link_create_template = add_query_arg(array(
                        'nbdv-task'     => 'create',
                        'product_id'    => $id,
                        'task'          => 'create',
                        'rd'            => 'admin_templates'
                    ), get_permalink( $id ) );
            }
            $link_manager_template = add_query_arg(array('pid' => $id, 'view' => 'templates'), admin_url('admin.php?page=nbdesigner_manager_product'));
            $ajax_product = add_query_arg( 
                array( 
                    'action'        => 'nbd_get_product_config',
                    'product_id'    => $id
                ),
                admin_url( 'admin-ajax.php' )
            );
        ?>
        <div class="nbdesigner-product">
            <a class="nbdesigner-product-title"><span><?php esc_html_e( $val['name'] ); ?></span></a>
            <div class="nbdesigner-product-inner">
                <a href="<?php echo esc_url( $val['url'] ); ?>" class="nbdesigner-product-link"><?php echo $val['img']; ?></a> 
            </div>
            <p class="nbdesigner-product-link">
                    <a href="<?php echo esc_url( $val['url'].'#nbdesigner_setting' ); ?>" title="<?php esc_attr_e('Edit product', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-edit"></span></a> 
<!--                        <a href="javascript: void(0)" onclick="openProductConfig( '<?php esc_attr_e('Edit product', 'web-to-print-online-designer'); ?>', '<?php echo esc_url( $ajax_product ); ?>' )" title="<?php esc_attr_e('Edit product', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-edit"></span></a>-->
                <a href="<?php echo get_permalink( $val['id'] ); ?>" title="<?php esc_attr_e( 'View product', 'web-to-print-online-designer' ); ?>"><span class="dashicons dashicons-visibility"></span></a>
                <?php 
                    $product    = wc_get_product( $id );
                    $variations = get_nbd_variations( $id );
                    if( count( $variations ) == 0 ):
                ?>
                <a href="<?php echo esc_url( $link_create_template ); ?>" target="_blank" title="<?php esc_attr_e('Create template', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-admin-customizer"></span></a>
                <?php  
                    else: 
                ?>
                <a href="#TB_inline?width=300&height=160&inlineId=nbd-<?php echo( $id ); ?>" class="thickbox"><span class="dashicons dashicons-admin-customizer"></span></a>
                <?php endif; ?>
                <a href="<?php echo esc_url( $link_manager_template ); ?>" title="<?php esc_attr_e('Manage template', 'web-to-print-online-designer'); ?>">
                    <span class="dashicons dashicons-images-alt"></span>
                    <span class="count" title="<?php echo( $val['number_template'] ); ?> <?php esc_attr_e('templates', 'web-to-print-online-designer'); ?>"><?php echo esc_html_e( $val['number_template'] < 100 ? $val['number_template'] : "99+" ); ?></span>
                </a>
            </p>
            <?php 
                if( count($variations) > 0 ):
            ?>
            <div id="nbd-<?php echo( $id ); ?>" style="display:none;">
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row" class="titledesc"><?php esc_html_e("Choose variation", 'web-to-print-online-designer'); ?></th>
                        <td class="forminp-text">
                            <select onchange="changeLink(this)" class="nbd-admin-setting-padding-0">
                                <option value="0">Template apply for all variations</option>
                            <?php foreach ( $variations as $variation ): ?>
                                <option value="<?php echo( $variation['id'] ); ?>"><?php esc_html_e( $variation['name'] ); ?></option>
                            <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
                <p class="nbd-admin-setting-text-align-center"><a class="button button-primary nbd-create" href="<?php echo esc_url( $link_create_template ); ?><?php echo '&variation_id=0';  ?>" data-href="<?php echo esc_url( $link_create_template ); ?>"><?php esc_html_e("Create template", 'web-to-print-online-designer'); ?></a></p>
            </div>
            <?php endif; ?>
		</div>		
	<?php endforeach;?>
    </div>
    <div class="tablenav top">
        <div class="tablenav-pages">
            <span class="displaying-num"><?php esc_html_e( $number_pro . ' ' . esc_html__('Products', 'web-to-print-online-designer') ); ?></span>
            <?php echo $paging->html(); ?>
        </div>
    </div>
    <?php else: ?>
    <?php echo sprintf(__('No product, <a href="%s" target="_blank">view user guide</a>', 'web-to-print-online-designer'), 'https://cmsmart.net/community/woocommerce-online-product-designer-plugin/videos'); ?>
    <?php endif; ?>
</div>
<script>
    changeLink = function(e){
        var vid = jQuery(e).val(),
        btn = jQuery(e).parents('table').siblings('p').find('a.nbd-create'),
        origin_fref = btn.data('href'),
        new_href = origin_fref + '&variation_id=' + vid;
        btn.attr('href', new_href);
    }
    openProductConfig = function( title, url ){
        tb_show(title, url ); 
    }
</script>