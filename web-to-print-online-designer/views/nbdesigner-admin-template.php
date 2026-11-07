<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div class="wrap">
    <?php
        $link_manager_template = add_query_arg(array(
            'pid'   => $pid,
            'view'  => 'templates'),
             admin_url('admin.php?page=nbdesigner_manager_product'));
        $layout = nbd_get_product_layout($pid);
        $link_add_template = add_query_arg(array(
            'product_id'    => $pid,
            'task'          => 'create',
            'rd'            => 'admin_templates'
        ), getUrlPageNBD('create'));
        if( $layout == 'v' ){
            $link_add_template = add_query_arg(array(
                'nbdv-task'     => 'create',
                'product_id'    => $pid,
                'task'          => 'create',
                'rd'            => 'admin_templates'
            ), get_permalink( $pid ) );
        }
    ?>  
    <div class="wrap">
        <h1 class="nbd-title">
            <?php esc_html_e('Templates for', 'web-to-print-online-designer'); ?>: <a class="nbd-product-url" href="<?php echo get_edit_post_link($pid); ?>"><?php esc_html_e( $pro->get_title() ); ?></a>
            <?php 
                $variations = get_nbd_variations( $pid );
                if( count($variations) > 0 ):
            ?>   
            <?php add_thickbox(); ?>
            <a class="button thickbox" href="#TB_inline?width=300&height=160&inlineId=nbd-<?php echo( $pid ); ?>"><?php esc_html_e('Add Template'); ?></a>
            <?php else: ?>
            <a class="button" href="<?php echo esc_url( $link_add_template ); ?>"><?php esc_html_e('Add Template'); ?></a>
            <?php endif; ?>
            <a href="<?php echo admin_url('admin.php?page=nbdesigner_manager_product') ?>" class="button-primary nbdesigner-right"><?php esc_html_e('Back', 'web-to-print-online-designer'); ?></a>
        </h1>
        <?php 
            if( count($variations) > 0 ):
        ?>
        <div id="nbd-<?php echo( $pid ); ?>" style="display:none;">
            <table class="form-table">
                <tr valign="top">
                    <th scope="row" class="titledesc"><?php esc_html_e("Choose variation", 'web-to-print-online-designer'); ?></th>
                    <td class="forminp-text">
                        <select onchange="changeLink(this)">
                            <option value="0"><?php esc_html_e("Template apply for all variations", 'web-to-print-online-designer'); ?></option>
                        <?php foreach ($variations as $variation): ?>
                            <option value="<?php echo( $variation['id'] ); ?>"><?php esc_html_e( $variation['name'] ); ?></option>
                        <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>
            <p class="nbd-admin-setting-text-align-center"><a class="button button-primary nbd-create" href="<?php echo esc_url( $link_add_template ); ?><?php echo '&variation_id=0';  ?>" data-href="<?php echo esc_url( $link_add_template ); ?>"><?php esc_html_e("Create template", 'web-to-print-online-designer'); ?></a></p>
        </div>
        <?php endif; ?>
        <div id="poststuff">
            <div id="post-body" class="metabox-holder">
                <div id="post-body-content">
                    <div class="meta-box-sortables ui-sortable">
                        <form method="post">
                        <?php
                            $templates_obj->prepare_items();
                            $templates_obj->display();
                        ?>
                        </form>
                    </div>
                </div>
            </div>
            <br class="clear">
        </div>
    </div>  
</div>
<script>
    changeLink = function(e){
        var vid = jQuery(e).val(),
        btn = jQuery(e).parents('table').siblings('p').find('a.nbd-create'),
        origin_fref = btn.data('href'),
        new_href = origin_fref + '&variation_id=' + vid;
        btn.attr('href', new_href);
    }
</script>