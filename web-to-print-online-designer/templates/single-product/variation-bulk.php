<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>
<div data-nbd-variation="<?php echo json_encode($variations); ?>" class="nbd-variation-bulk-wrap">
    <p class="nbd-variation-bulk-title"><?php _e( 'Order bulk', 'web-to-print-online-designer'); ?>
        <a href="javascript:void(0)" onclick="NBDESIGNERPRODUCT.add_variation_bulk_form()"><?php _e( 'Add', 'web-to-print-online-designer'); ?></a>
    </p>
    <div id="nbd-variations-wrap">
        <div class="nbd-variation-wrap">
            <select onchange="NBDESIGNERPRODUCT.init_nbd_variation_value()">
            <?php foreach ( $variations as $variation ): ?>
                <option value="<?php echo $variation['id']; ?>"><?php echo $variation['name']; ?></option>
            <?php endforeach; ?>
            </select>
            <input type="number" step="1" min="1" class="nbd-variation-quantity"  onchange="NBDESIGNERPRODUCT.init_nbd_variation_value()"/>
            <a href="javascript:void(0)" onclick="NBDESIGNERPRODUCT.remove_variation_bulk_form( this )">
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                    <title>close</title>
                    <path d="M18.984 6.422l-5.578 5.578 5.578 5.578-1.406 1.406-5.578-5.578-5.578 5.578-1.406-1.406 5.578-5.578-5.578-5.578 1.406-1.406 5.578 5.578 5.578-5.578z"></path>
                </svg>                
            </a> 
        </div>
    </div>    
    <input type="hidden" class="nbd-variation-value" name="nbd-variation-value"/>
</div>
<script>
    var is_nbd_bulk_variation = true;
    jQuery(document).ready(function(){
        jQuery('input[name="add-to-cart"]').remove();
    });
</script>    