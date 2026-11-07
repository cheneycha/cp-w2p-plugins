<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<h1><?php esc_html_e('Cliparts', 'web-to-print-online-designer'); ?></h1>
<?php echo $notice; ?>
<div class="wrap nbdesigner-container">
    <div class="nbdesigner-content-full">
        <form name="post" action="" method="post" enctype="multipart/form-data" autocomplete="off">
            <div class="nbdesigner-content-left postbox">
                <div class="inside">
                    <?php wp_nonce_field($this->plugin_id, $this->plugin_id.'_hidden'); ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row" class="titledesc"><?php esc_html_e("Clipart files", 'web-to-print-online-designer'); ?> </th>
                            <td class="forminp-text">
                                <input type="file" name="svg[]" value="" accept=".svg,image/*" multiple/><br />
                                <div class="nbd-admin-font-tip"><?php esc_html_e('Allow extensions: svg, png, jpg, jpeg', 'web-to-print-online-designer'); ?>
                                    <br /><?php esc_html_e('Allow upload multiple files.', 'web-to-print-online-designer'); ?>
                                    <br /><?php esc_html_e('Note: if you export svg file from Illustrator, please choose Fonts type "Convert to outline"', 'web-to-print-online-designer'); ?></div>
                            </td>
                        </tr>
                    </table>
                    <input type="hidden" name="nbdesigner_art_id" value="<?php echo( $art_id ); ?>"/>
                    <p class="submit">
                        <input type="submit" name="Submit" class="button-primary" value="<?php esc_html_e('Save', 'web-to-print-online-designer'); ?>" />
                        <a href="?page=nbdesigner_manager_fonts" class="button-primary" style="<?php $style = (isset($_GET['id'])) ? '' : 'display:none;';echo( $style ); ?>"><?php esc_html_e('Add New', 'web-to-print-online-designer'); ?></a>
                    </p>
                </div>
            </div>
            <div class="nbdesigner-content-side">
                <div class="postbox nbd-admin-padding-bottom-5" >
                    <h3><?php esc_html_e('Categories', 'web-to-print-online-designer'); ?><img src="<?php echo NBDESIGNER_PLUGIN_URL . 'assets/images/loading.gif'; ?>" class="nbdesigner_editcat_loading nbdesigner_loaded nbd-admin-margin-left-15" /></h3>
                    <div class="inside">
                        <ul id="nbdesigner_list_art_cats">
                        <?php if(is_array($cat) && (sizeof($cat) > 0)): ?>
                            <?php foreach($cat as $val): ?>
                                <li id="nbdesigner_cat_art_<?php echo( $val->id ); ?>" class="nbdesigner_action_delete_art_cat">
                                    <label>
                                        <input value="<?php echo( $val->id ); ?>" type="checkbox" name="nbdesigner_art_cat[]" <?php if($update && (sizeof($cats) > 0 )) if(in_array($val->id, $cats)) echo "checked";  ?> />
                                    </label>
                                    <span class="nbdesigner-right nbdesigner-delete-item dashicons dashicons-no-alt" onclick="NBDESIGNADMIN.delete_cat_art(this)"></span>
                                    <span class="dashicons dashicons-edit nbdesigner-right nbdesigner-delete-item" onclick="NBDESIGNADMIN.edit_cat_art(this)"></span>
                                    <a href="<?php echo add_query_arg(array('cat_id' => $val->id), admin_url('admin.php?page=nbdesigner_manager_arts')); ?>" class="nbdesigner-cat-link"><?php esc_html_e(  $val->name ); ?></a>
                                    <input value="<?php echo( $val->name ); ?>" class="nbdesigner-editcat-name" type="text"/>
                                    <span class="dashicons dashicons-yes nbdesigner-delete-item nbdesigner-editcat-name" onclick="NBDESIGNADMIN.save_cat_art(this)"></span>
                                    <span class="dashicons dashicons-no nbdesigner-delete-item nbdesigner-editcat-name" onclick="NBDESIGNADMIN.remove_action_cat_art(this)"></span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?> 
                                <li><?php esc_html_e('You don\'t have any category.', 'web-to-print-online-designer'); ?></li>
                        <?php endif; ?>
                        </ul>
                        <input type="hidden" id="nbdesigner_current_art_cat_id" value="<?php echo( $current_art_cat_id ); ?>"/>
                        <p><a id="nbdesigner_add_art_cat"><?php esc_html_e('+ Add new art category', 'web-to-print-online-designer'); ?></a></p>
                        <div id="nbdesigner_art_newcat" class="category-add"></div>	
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="clear"></div>
    <div class="postbox" id="nbd-list-arts">
        <h3 class="nbd-admin-line-height"><?php esc_html_e('List arts ', 'web-to-print-online-designer'); ?>
            <?php if(is_array($cat) && (sizeof($cat) > 0)): ?>
            <select onchange="if (this.value) window.location.href=this.value+'#nbd-list-arts'">
                <option value="<?php echo admin_url('admin.php?page=nbdesigner_manager_arts'); ?>"><?php esc_html_e('Select a category', 'web-to-print-online-designer'); ?></option>
                <?php foreach($cat as $cat_index => $val): ?>
                <option value="<?php echo add_query_arg(array('cat_id' => $val->id), admin_url('admin.php?page=nbdesigner_manager_arts')) ?>" <?php selected( $cat_index, $current_cat_id ); ?>><?php echo( $val->name ); ?></option>
                <?php endforeach; ?>
            </select>
            <?php  endif; ?>
            <span class="nbdesigner-right">
                <a class="nbd-toggle-art-view" title="<?php esc_html_e('View mode white', 'web-to-print-online-designer');?>" href="javascript:void(0)" onclick="NBDESIGNADMIN.changeModeViewArt()"></a>
                <a class="nbd-toggle-art-view black nbd-admin-margin-right-15" title="<?php esc_html_e('View mode black', 'web-to-print-online-designer');?>" href="javascript:void(0)" onclick="NBDESIGNADMIN.changeModeViewArt()"></a>
                <a href="<?php echo admin_url('admin.php?page=nbdesigner_manager_arts'); ?>"><?php esc_html_e('All arts', 'web-to-print-online-designer'); ?></a>
            </span>
        </h3>
        <div class="nbdesigner-list-fonts inside">
            <div class="nbdesigner-list-arts-container">
                <?php if(is_array($_list) && (sizeof($_list) > 0)): ?>
                    <?php 
                        foreach($_list as $val): 
                        $art_url = ( strpos($val->url, 'http') > -1 ) ? $val->url : NBDESIGNER_ART_URL.$val->url;
                    ?>
                        <span class="nbdesigner_art_link "><img src="<?php echo esc_url( $art_url ); ?>" /><span class="nbdesigner_action_delete_art" data-index="<?php echo( $val->id ); ?>" onclick="NBDESIGNADMIN.delete_art(this)">&times;</span></span>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php esc_html_e('You don\'t have any art.', 'web-to-print-online-designer');?>
                <?php endif; ?>
            </div>
            <div class="tablenav top">
                <div class="tablenav-pages">
                    <span class="displaying-num"><?php echo esc_html( $total ) . ' ' . esc_html__('arts', 'web-to-print-online-designer'); ?></span>
                    <?php echo $paging->html(); ?>
                </div>
            </div>
        </div>
    </div>
</div>