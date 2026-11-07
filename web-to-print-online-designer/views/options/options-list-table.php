<?php 
    if (!defined('ABSPATH')) exit; 
    $link_create_option = add_query_arg(array(
            'action'    => 'edit',
            'paged'     => 1,
            'id'        => 0
        ),
        admin_url('admin.php?page=nbd_printing_options'));
?>
<div class="wrap">
    <h1>
        <?php _e('Printing options', 'web-to-print-online-designer'); ?>
        <a class="nbd-page-title-action" href="<?php echo $link_create_option; ?>"><?php _e('Add new', 'web-to-print-online-designer'); ?></a>
    </h1>
    <div id="poststuff">
        <div id="post-body" class="metabox-holder">
            <div id="post-body-content">
                <div class="meta-box-sortables ui-sortable">
                    <?php ob_start(); ?>
                    <form method="post">
                    <?php
                        $nbd_options->prepare_items();
                        $nbd_options->display();
                    ?>
                    </form>
                    <?php echo apply_filters('nbd_change_table_list_printing',ob_get_clean());?>
                </div>
            </div>
        </div>
        <br class="clear">
    </div>
</div>