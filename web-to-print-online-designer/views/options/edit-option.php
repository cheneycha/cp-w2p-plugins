<?php if (!defined('ABSPATH')) exit; ?>
<?php
    $link = add_query_arg(array(
            'paged'    => $_GET['paged']
        ), admin_url('admin.php?page=nbd_printing_options')); 
    $link_update = add_query_arg(array(
            'action'    => 'update',
            'id'        => $options['id'],
        ), admin_url('admin.php?page=nbd_printing_options')); 
    $link_unpublish = add_query_arg(array(
            'id'        => $_GET['id'],
            'action'    => 'unpublish'
        ), $link);
    $link_create_option = add_query_arg(array(
            'action'    => 'edit',
            'paged'     => 1,
            'id'        => 0
        ),
        admin_url('admin.php?page=nbd_printing_options'));
    wp_enqueue_media();
    $current_url = add_query_arg($_GET, admin_url('admin.php?page=nbd_printing_options'));
    $link_create_pre_builder = add_query_arg(array(
            'oid'   => $_GET['id'],
            'paged' => $_GET['paged'],
            'rd'    => 'print_option'
        ), getUrlPageNBD('product_builder')); 
    $max_input_vars = nbd_get_max_input_var();
    $large_amount_field = nbdesigner_get_option( 'nbdesigner_print_option_large_amount', 'no' );
?>
<script type="text/javascript">
    var NBDOPTIONS = <?php echo json_encode($options); ?>;
    var NBDOPTION_FIELD = <?php echo json_encode($default_field); ?>;
    var ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>",
    nbnonce = "<?php echo wp_create_nonce('save-design'); ?>",
    large_amount_field = "<?php echo $large_amount_field; ?>",
    max_input_vars = parseInt(<?php echo $max_input_vars; ?>);
</script>
<div class="wrap">
    <h2>
        <?php _e('Edit Options', 'web-to-print-online-designer'); ?>
        <a class="nbd-page-title-action" href="<?php echo $link_create_option; ?>"><?php _e('Add new', 'web-to-print-online-designer'); ?></a>
    </h2>
</div>
<div class="message">
    <?php if( isset($message['flag']) ){
        $message = nbd_custom_notices($message['flag'], $message['content']);
        echo $message;
    } ?>
</div>
<div class="wrap" ng-app="optionApp" ng-cloak>
    <div ng-controller="optionCtrl">
        <form name="nboForm" action="" method="post" id="post">
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <div id="titlediv">
                            <div id="titlewrap">
                                <label class="screen-reader-text" id="title-prompt-text" for="title"><?php _e('Enter title here', 'web-to-print-online-designer'); ?></label>
                                <input required="required" ng-model="options.title" type="text" name="title" size="30" value="<?php echo $options['title']; ?>" id="title" autocomplete="off">
                                <span style="color: red;" ng-show="nboForm.title.$invalid">* <small><i><?php _e('required', 'web-to-print-online-designer'); ?></i></small></span>
                                <input type="hidden" name="options[version]" value="<?php echo NBDESIGNER_NUMBER_VERSION; ?>" />
                            </div>
                        </div>
                    </div>
                    <div id="postbox-container-1" class="postbox-container">
                        <div id="submitdiv" class="postbox ">
                            <h2 class="hndle ui-sortable-handle"><span><?php _e('Publish', 'web-to-print-online-designer'); ?></span></h2>
                            <div class="inside">
                                <div class="submitbox" id="submitpost">
                                    <div id="minor-publishing">
                                        <div id="misc-publishing-actions">
                                            <div class="misc-pub-section misc-pub-priority" id="priority">
                                                <?php _e('Priority', 'web-to-print-online-designer'); ?>
                                                <input type="number" value="<?php echo $options['priority']; ?>" maxlength="3" max="127"
                                                    id="nbo_meta_priority" name="priority" class="meta-priority" min="1"
                                                    step="1"/>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>  
                                    <div class="minor-publishing">
                                        <div class="misc-publishing-actions nbo-dates" >
                                            <div style="margin-bottom: 15px;">
                                                <label for="date_from"><?php _e('From', 'web-to-print-online-designer'); ?></label>
                                                <input type="text" class="date_from" id="date_from" name="date_from" value="<?php echo $options['date_from']; ?>" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" placeholder="<?php _e('YYYY-MM-DD', 'web-to-print-online-designer'); ?>" title="<?php _e( 'Leave both fields blank to not restrict this options to a date range', 'web-to-print-online-designer' ); ?>"/>
                                            </div>
                                            <div>
                                                <label for="date_to"><?php _e('To', 'web-to-print-online-designer'); ?></label>
                                                <input class="date_to" id="date_to" name="date_to" value="<?php echo $options['date_to']; ?>" pattern="[0-9]{4}-(0[1-9]|1[012])-(0[1-9]|1[0-9]|2[0-9]|3[01])" placeholder="<?php _e('YYYY-MM-DD', 'web-to-print-online-designer'); ?>" title="<?php _e( 'Leave both fields blank to not restrict this options to a date range', 'web-to-print-online-designer' ); ?>"/>
                                            </div>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                    <div id="major-publishing-actions">
                                        <div id="delete-action">
                                            <?php if($options['published'] == 1): ?>
                                            <a class="submitdelete deletion"
                                               href="<?php echo $link_unpublish; ?>"><?php _e('Move to Trash', 'web-to-print-online-designer'); ?></a>
                                            <?php endif; ?>
                                        </div>   
                                        <div id="publishing-action">
                                            <input ng-disabled="!nboForm.$valid" name="save" type="submit" class="button button-primary button-large" id="publish" ng-click="updateJsonFields($event)"
                                                accesskey="p" value="<?php if($id != 0){ if($options['published'] == 1) esc_attr_e( 'Update' ); else esc_attr_e( 'Publish' ); }else{ esc_attr_e( 'Publish' ); }; ?>"/>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="product_catdiv" class="postbox">
                            <h2 class="hndle ui-sortable-handle"><span><?php _e('Apply for', 'web-to-print-online-designer'); ?></span></h2>
                            <div class="inside">
                                <label for="apply_product"><?php _e('Products', 'web-to-print-online-designer'); ?>
                                    <input class="nbo-toggle-nav" data-toggle="#nbo-products-wrap" type="radio" id="apply_product" name="apply_for" value="p" <?php checked($options['apply_for'], 'p') ?>/>
                                </label>
                                <label for="apply_categories"><?php _e('Categories', 'web-to-print-online-designer'); ?>
                                    <input class="nbo-toggle-nav" data-toggle="#nbo-categories-wrap" type="radio" id="apply_categories" name="apply_for" value="c" <?php checked($options['apply_for'], 'c') ?>/>  
                                </label>
                            </div>
                            <div class="inside nbo-toggle <?php if($options['apply_for'] == 'p') echo 'active'; ?>" id="nbo-products-wrap">
                                <label for="product_ids" style="display: inline-block;margin-bottom: 10px;"><?php _e('Select the Products to apply the options', 'web-to-print-online-designer') ?></label>
                                <select name="product_ids[]" id="product_ids" class="wc-product-search"
                                    multiple="multiple" style="width: 100%;" data-placeholder="<?php _e( 'Search for a product&hellip;', 'web-to-print-online-designer' ); ?>"
                                    data-action="woocommerce_json_search_products" >
                                    <?php 
                                        foreach ( $options['product_ids'] as $product_id ) {
                                            $product = wc_get_product( $product_id );
                                            if ( is_object( $product ) ) {
                                                echo '<option value="' . esc_attr( $product_id ) . '"' . selected( TRUE, TRUE, FALSE ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="inside nbo-toggle <?php if($options['apply_for'] == 'c') echo 'active'; ?>" id="nbo-categories-wrap">
                                <label><?php _e('Select the Categories to apply the options', 'web-to-print-online-designer') ?></label>
                                <ul id="nbo-categories" style="padding: 10px; border: 1px solid #ddd;max-height: 300px;overflow: auto;">
                            <?php 
                                $terms = get_terms( 'product_cat', array('hierarchical' => false, 'hide_empty' => false, 'parent' => 0) );
                                if ( !is_wp_error( $terms ) && !empty( $terms ) ){
                                    function build_category_tree($terms, $indent, $product_cats){
                                        foreach ( $terms as $item_id => $item ) :
                                        $checked = in_array( $item->term_id, $product_cats ) ? 'checked="checked"' : '';
                            ?>
                                    <li>
                                        <label for="product_cat<?php echo $item->term_id; ?>"><input id="product_cat<?php echo $item->term_id; ?>" <?php echo $checked; ?> name="product_cats[]" type="checkbox" value="<?php echo $item->term_id; ?>"/> <strong><?php echo $item->name; ?></strong></label>                            
                            <?php 
                                        $child_terms = get_terms( 'product_cat', array('hierarchical' => false, 'hide_empty' => false, 'parent' => $item->term_id) );
                                        if ( !is_wp_error( $child_terms ) && !empty( $child_terms ) ){
                                            echo '<ul class="children">';
                                            build_category_tree( $child_terms, $indent + 1, $product_cats );
                                            echo '</ul>';
                                        }
                                        echo '</li>';
                                        endforeach; 
                                    }
                                    build_category_tree($terms, 0, $options['product_cats']);
                                }
                            ?>
                                </ul>
                            </div>
                        </div>
                        <div id="notice-max-input-vars" class="postbox" ng-show="current_input_vars > max_input_vars">
                            <h2 style="color: #ff4136;" class="hndle ui-sortable-handle"><span style="vertical-align: bottom; margin-top: 0;" class="dashicons dashicons-warning"></span> <span><?php _e('Notice', 'web-to-print-online-designer'); ?></span></h2>
                            <div class="inside">
                                <p><?php _e('PHP max input vars:', 'web-to-print-online-designer'); ?> <?php echo $max_input_vars; ?></p>
                                <p><?php _e('Current input vars:', 'web-to-print-online-designer'); ?> <span>{{current_input_vars}}</span></p>
                                <p><?php _e('Please increase "PHP max input vars"!', 'web-to-print-online-designer'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div id="postbox-container-2" class="postbox-container">
                        <div class="postbox">
                            <div class="inside">
                                <div class="nbd-option-actions">
                                    <a ng-click="import()" class="button-primary"><span class="dashicons dashicons-migrate nbd-r180"></span> <?php _e('Import', 'web-to-print-online-designer'); ?></a>
                                    <a ng-click="export()" class="button-primary"><span class="dashicons dashicons-migrate"></span> <?php _e('Export', 'web-to-print-online-designer'); ?></a>
                                </div>
                            </div>
                        </div>
                        <div class="postbox nbd-fields-wrap"> 
                            <h2 style="border-bottom: 1px solid #ddd;"><?php _e('Printing fields', 'web-to-print-online-designer'); ?></h2>
                            <div class="inside">
                                <div>
                                    <p class="section-title"><input class="nbd-ip-readonly" value="<?php _e('Default field', 'web-to-print-online-designer'); ?>" readonly=""></p>
                                    <div class="nbd-section-wrap">
                                        <a title="<?php _e('Add fields', 'web-to-print-online-designer'); ?>" class="nbd-field-btn button" ng-click="add_field()"><?php _e('Default field', 'web-to-print-online-designer'); ?> <span class="nbo-type-label default">1</span></a>
                                        <a title="<?php _e('Add fields', 'web-to-print-online-designer'); ?>" class="nbd-field-btn button" ng-click="add_field('delivery')"><?php _e('Delivery date', 'web-to-print-online-designer'); ?> <span class="nbo-type-label default">30</span></a>
                                        <a title="<?php _e('Artwork actions', 'web-to-print-online-designer'); ?>" class="nbd-field-btn button" ng-click="add_field('actions')"><?php _e('Artwork actions', 'web-to-print-online-designer'); ?> <span class="nbo-type-label default">31</span></a>
                                    </div>
                                </div>
                                <div style="margin-top: 10px;">
                                    <p class="section-title"><input class="nbd-ip-readonly" value="<?php _e('Online design fields', 'web-to-print-online-designer'); ?>" readonly=""></p>
                                    <div class="nbd-section-wrap">
                                        <a class="nbd-field-btn button" ng-click="add_field('page')"><?php _e('Sides/Pages', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wod">2</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('page1')"><?php _e('Number of Pages', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wod">2.1</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('page2')"><?php _e('Side list', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wod">2.2</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('page3')"><?php _e('Front/Back Sides', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wod">2.3</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('color')"><?php _e('Color/Material', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wod">3</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('size')"><?php _e('Size', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wod">4</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('dimension')"><?php _e('Custom dimension', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wod">5</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('dpi')"><?php _e('DPI', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wod">6</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('area')"><?php _e('Area design shape', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wod">7</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('shape')"><?php _e('Custom area design shape', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wod">7.1</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('orientation')"><?php _e('Orientation', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wod">8</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('padding')"><?php _e('Padding', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wod">9</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('rounded_corner')"><?php _e('Rounded corners', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wod">10</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('overlay')"><?php _e('Overlay', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wod">11</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('fold')"><?php _e('Folding Styles', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wod">12</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('pt_color')"><?php _e('Printing Color', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wod">13</span></a> <?php // custom charming ?>
                                    </div>
                                </div>
                                <div style="margin-top: 10px;">
                                    <p class="section-title"><input class="nbd-ip-readonly" value="<?php _e('Product builder fields', 'web-to-print-online-designer'); ?>" readonly=""></p>
                                    <div class="nbd-section-wrap">
                                        <a class="nbd-field-btn button" ng-click="add_field('nbpb_com', 'nbpb_com')"><?php _e('Component', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wpo">20</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('nbpb_text', 'nbpb_text')"><?php _e('Text', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wpo">21</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('nbpb_image', 'nbpb_image')"><?php _e('Image', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wpo">22</span></a>
                                    </div>
                                </div>
                                <div style="margin-top: 10px;">
                                    <p class="section-title"><input class="nbd-ip-readonly" value="<?php _e('Upload design file fields', 'web-to-print-online-designer'); ?>" readonly=""></p>
                                    <div class="nbd-section-wrap">
                                        <a class="nbd-field-btn button" ng-click="add_field('frame')"><?php _e('Frame styles', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wpu">40</span></a>
                                        <a class="nbd-field-btn button" ng-click="add_field('number_file')"><?php _e('Number of upload files', 'web-to-print-online-designer'); ?> <span class="nbo-type-label wpu">41</span></a>
                                    </div>
                                </div>
                                <div>
                                    <p>
                                        <span class="nbo-type-label default">1</span> 
                                        <span class="nbo-type-label default">30</span> 
                                        <span class="nbo-type-label default">31</span> 
                                        <?php _e('Default fields', 'web-to-print-online-designer'); ?></p>
                                    <p>
                                        <span class="nbo-type-label wod">2</span>
                                        <span class="nbo-type-label wod">3</span>
                                        <span class="nbo-type-label wod">4</span>
                                        <span class="nbo-type-label wod">5</span>
                                        <span class="nbo-type-label wod">6</span>
                                        <span class="nbo-type-label wod">7</span>
                                        <span class="nbo-type-label wod">7.1</span>
                                        <span class="nbo-type-label wod">8</span> 
                                        <span class="nbo-type-label wod">9</span> 
                                        <span class="nbo-type-label wod">10</span> 
                                        <span class="nbo-type-label wod">11</span> 
                                        <span class="nbo-type-label wod">12</span> 
                                        <span class="nbo-type-label wod">13</span> 
                                        <?php _e('Online design fields which effect custom design configuaration.', 'web-to-print-online-designer'); ?>
                                    </p>
                                    <p>
                                        <span class="nbo-type-label wpo">20</span>
                                        <span class="nbo-type-label wpo">21</span>
                                        <span class="nbo-type-label wpo">22</span> 
                                        <?php _e('Product builder fields', 'web-to-print-online-designer'); ?>
                                    </p>
                                    <p>
                                        <span class="nbo-type-label wpu">40</span>
                                        <span class="nbo-type-label wpu">41</span>
                                        <?php _e('Upload design file fields', 'web-to-print-online-designer'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="postbox">
                            <h2 style="border-bottom: 1px solid #ddd;"><?php _e('Printing options', 'web-to-print-online-designer'); ?></h2>
                            <div class="inside">
                                <div class="nbd-fields-builder">
                                    <?php include_once('quantity.php'); ?>
                                    <?php include_once('field.php'); ?>
                                    <?php include_once('formula-popup.php'); ?>
                                </div>
                                <div ng-repeat="view in options.views">
                                    <input ng-hide="true" ng-model="view.name" name="options[views][{{$index}}][name]"/>
                                    <input ng-hide="true" ng-model="view.base" name="options[views][{{$index}}][base]"/>
                                    <input ng-hide="true" ng-model="view.base_width" name="options[views][{{$index}}][base_width]"/>
                                    <input ng-hide="true" ng-model="view.base_height" name="options[views][{{$index}}][base_height]"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="postbox-container-3" class="postbox-container">
                        <div class="postbox">
                            <h2 style="border-bottom: 1px solid #ddd;"><?php _e('Appearance', 'web-to-print-online-designer'); ?></h2>
                            <div class="inside">
                                <?php include_once('appearance.php'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <?php if( $large_amount_field == 'yes' ): ?>
                <textarea style="display: none;" name="options[jsonFields]" ng-model="jsonFields"></textarea>
            <?php endif; ?>
        </form>
        <?php include_once('preview.php'); ?>
    </div>
</div>
<div class="nbp-loading-wrap">
    <div class="nbp-loading-spinner">
        <div class="nbp-loading-ball"></div>
        <p id="nbp-processing" style="display: none;font-weight: bold;white-space: nowrap;"><?php _e('Processing', 'web-to-print-online-designer'); ?> <span id="nbp-process-loaded"></span> / <span id="nbp-process-total"></span></p>
    </div>
</div>