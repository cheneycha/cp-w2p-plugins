<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<div class="nbdesigner-opt-inner">
    <label class="nbdesigner-option-label"><?php echo _e('Price base on design area', 'web-to-print-online-designer'); ?> <?php echo wc_help_tip( __( 'Require enable "Allow user define demension"', 'web-to-print-online-designer' ) ); ?></label>
    <?php $price_base_area = isset($option['price_base_area']) ? $option['price_base_area'] : 0; ?>
    <input name="_nbdesigner_option[price_base_area]" value="1" type="radio" <?php checked( $price_base_area, 1); ?> /><?php _e('Yes', 'web-to-print-online-designer'); ?>   
    <input name="_nbdesigner_option[price_base_area]" value="0" type="radio" <?php checked( $price_base_area,0); ?> /><?php _e('No', 'web-to-print-online-designer'); ?>
</div> 
<div class="nbdesigner-opt-inner nbd-independence <?php if( !$price_base_area ) echo 'nbdesigner-disable'; ?>" id="nbd_area_pricing_table_wrap">
    <table id="nbd_area_pricing_table" class="nbd_pricing_table">
        <thead>
            <tr>
                <th class="check-column">
                    <input type="checkbox" >
                </th>
                <th class="range-column">
                    <span class="column-title" data-text="<?php esc_attr_e( 'Measurement Range', 'web-to-print-online-designer' ); ?>"><?php esc_html_e( 'Measurement Range', 'web-to-print-online-designer' ); ?></span>
                    <?php echo wc_help_tip( __( 'Configure the starting-ending range, inclusive, of measurements to match this rule.  The first matched rule will be used to determine the price.  The final rule can be defined without an ending range to match all measurements greater than or equal to its starting range.', 'web-to-print-online-designer' ) ); ?>
                </th>
                <th class="price-column">
                    <span><?php echo _e('Price per Unit', 'web-to-print-online-designer'); ?> <?php echo ' ('.get_woocommerce_currency_symbol() . '/' . nbdesigner_get_option('nbdesigner_dimensions_unit').'<sup>2</sup>)'; ?></span>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $price_base_area_prices = isset($option['price_base_area_prices']) ? $option['price_base_area_prices'] : array('start' => array(), 'end' => array(), 'price' => array());
                $starts = $price_base_area_prices['start'];
                $ends = $price_base_area_prices['end'];
                $prices = $price_base_area_prices['price'];
                foreach($starts as $key => $start): 
            ?>
            <tr>
                <td>
                    <input type="checkbox" >
                </td>                
                <td>
                    <span>
                        <span class="nbd-table-price-label"><?php echo _e('From', 'web-to-print-online-designer'); ?></span>
                        <input type="number" min="0" step="1" name="_nbdesigner_option[price_base_area_prices][start][]" value="<?php echo $start; ?>" class="nbd-price-table-input short">
                    </span>   
                    <span>
                        <span class="nbd-table-price-label" style="margin-left: 10px;"><?php echo _e('To', 'web-to-print-online-designer'); ?></span>
                        <input type="number" min="0" step="1" name="_nbdesigner_option[price_base_area_prices][end][]" value="<?php echo $ends[$key]; ?>" class="nbd-price-table-input short">
                    </span>                       
                </td>
                <td>
                    <input type="text" name="_nbdesigner_option[price_base_area_prices][price][]" value="<?php echo $prices[$key]; ?>" class="short nbd-price-table-input wc_input_price">
                </td>                
            </tr> 
            <?php  endforeach; ?>
        </tbody>
	<tfoot>
            <tr>
                <th colspan="3">
                    <button type="button" data-type="area" class="button button-primary nbd-pricing-table-add-rule"><?php esc_html_e( 'Add Rule', 'web-to-print-online-designer' ); ?></button>
                    <button type="button" class="button button-secondary nbd-pricing-table-delete-rules"><?php esc_html_e( 'Delete Selected', 'web-to-print-online-designer' ); ?></button>
                </th>
            </tr>
	</tfoot>        
    </table>
</div>
<div class="nbdesigner-opt-inner">
    <label class="nbdesigner-option-label"><?php echo _e('Price base on quantity', 'web-to-print-online-designer'); ?> <?php echo wc_help_tip( __('Overrides extra price for custom design', 'web-to-print-online-designer') ); ?></label>
    <?php $price_base_quantity = isset($option['price_base_quantity']) ? $option['price_base_quantity'] : 0; ?>
    <input name="_nbdesigner_option[price_base_quantity]" value="1" type="radio" <?php checked( $price_base_quantity, 1); ?> /><?php _e('Yes', 'web-to-print-online-designer'); ?>   
    &nbsp;<input name="_nbdesigner_option[price_base_quantity]" value="0" type="radio" <?php checked( $price_base_quantity, 0); ?> /><?php _e('No', 'web-to-print-online-designer'); ?>  
</div> 
<div class="nbdesigner-opt-inner nbd-independence <?php if( !$price_base_quantity ) echo 'nbdesigner-disable'; ?>" id="nbd_quantity_pricing_table_wrap">
    <table id="nbd_quantity_pricing_table" class="nbd_pricing_table">
        <thead>
            <tr>
                <th class="check-column">
                    <input type="checkbox" >
                </th>
                <th class="range-column">
                    <span class="column-title" data-text="<?php esc_attr_e( 'Quantity Range', 'web-to-print-online-designer' ); ?>"><?php esc_html_e( 'Quantity Range', 'web-to-print-online-designer' ); ?></span>
                    <?php echo wc_help_tip( __( 'Configure the starting-ending range, inclusive, of quantities to match this rule.  The first matched rule will be used to determine the price.  The final rule can be defined without an ending range to match all quantities greater than or equal to its starting range.', 'web-to-print-online-designer' ) ); ?>
                </th>
                <th class="price-column">
                    <span><?php echo _e('Price per unit', 'web-to-print-online-designer'); ?></span>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $price_base_quantity_prices = isset($option['price_base_quantity_prices']) ? $option['price_base_quantity_prices'] : array('start' => array(), 'end' => array(), 'price' => array());
                $starts = $price_base_quantity_prices['start'];
                $ends = $price_base_quantity_prices['end'];
                $prices = $price_base_quantity_prices['price'];
                foreach($starts as $key => $start): 
            ?>            
            <tr>
                <td>
                    <input type="checkbox" >
                </td>                
                <td>
                    <span>
                        <span class="nbd-table-price-label"><?php echo _e('From', 'web-to-print-online-designer'); ?></span>
                        <input type="number" min="0" step="1" name="_nbdesigner_option[price_base_quantity_prices][start][]" value="<?php echo $start; ?>" class="nbd-price-table-input short">
                    </span>     
                    <span>
                        <span class="nbd-table-price-label" style="margin-left: 10px;"><?php echo _e('To', 'web-to-print-online-designer'); ?></span>
                        <input type="number" min="0" step="1" name="_nbdesigner_option[price_base_quantity_prices][end][]" value="<?php echo $ends[$key]; ?>" class="nbd-price-table-input short">
                    </span>                       
                </td>
                <td>
                    <input type="text" name="_nbdesigner_option[price_base_quantity_prices][price][]" value="<?php echo $prices[$key]; ?>" class="short nbd-price-table-input wc_input_price">
                </td>                
            </tr> 
            <?php endforeach; ?>
        </tbody>
	<tfoot>
            <tr>
                <th colspan="3">
                    <button type="button" data-type="quantity" class="button button-primary nbd-pricing-table-add-rule"><?php esc_html_e( 'Add Rule', 'web-to-print-online-designer' ); ?></button>
                    <button type="button" class="button button-secondary nbd-pricing-table-delete-rules"><?php esc_html_e( 'Delete Selected', 'web-to-print-online-designer' ); ?></button>
                </th>
            </tr>
	</tfoot>
    </table>  
</div>
<style>
    .nbd_pricing_table {
        border: 1px solid #ddd;
        border-collapse: collapse;
        background: #fff;        
    }  
    .nbd_pricing_table td, .nbd_pricing_table th {
        padding: 8px 10px;
        text-align: left;
        border: 1px solid #ddd;
    }
    .nbd_pricing_table th {
        border-bottom: 1px solid #ddd;
    }
    .nbd_pricing_table tfoot th {
        border-top: 1px solid #ddd;
    }
    .nbd_pricing_table .nbd-table-price-label {
        margin-right: 10px;
        display: inline-block;
    }
    .nbd_pricing_table .nbd-pricing-table-add-rule {
        float: left;
    }
    .nbd_pricing_table .nbd-pricing-table-delete-rules {
        float: right;
    }    
    .nbd_pricing_table .nbd-price-table-input{
        width: 100px;
    }
</style>
<script>
    var nbd_area_price_row = "<tr><td><input type='checkbox' ></td><td><span><span class='nbd-table-price-label'>" + "<?php echo _e('From', 'web-to-print-online-designer'); ?>" +"</span><input type='number' min='0' step='1' name='_nbdesigner_option[price_base_area_prices][start][]' class='nbd-price-table-input short'></span><span><span class='nbd-table-price-label' style='margin-left: 10px;'>" + "<?php echo _e('To', 'web-to-print-online-designer'); ?>" + "</span><input type='number' min='0' step='1' name='_nbdesigner_option[price_base_area_prices][end][]' class='nbd-price-table-input short'></span></td><td><input type='text' name='_nbdesigner_option[price_base_area_prices][price][]' class='short nbd-price-table-input wc_input_price'></td></tr>",
        nbd_quantity_price_row = "<tr><td><input type='checkbox' ></td><td><span><span class='nbd-table-price-label'>" + "<?php echo _e('From', 'web-to-print-online-designer'); ?>" +"</span><input type='number' min='0' step='1' name='_nbdesigner_option[price_base_quantity_prices][start][]' class='nbd-price-table-input short'></span><span><span class='nbd-table-price-label' style='margin-left: 10px;'>" + "<?php echo _e('To', 'web-to-print-online-designer'); ?>" + "</span><input type='number' min='0' step='1' name='_nbdesigner_option[price_base_quantity_prices][end][]' class='nbd-price-table-input short'></span></td><td><input type='text' name='_nbdesigner_option[price_base_quantity_prices][price][]' class='short nbd-price-table-input wc_input_price'></td></tr>";
    jQuery(document).ready(function(){
        jQuery('table thead input').change(function(){
            var _prices = jQuery(this).parents('table.nbd_pricing_table').find('tbody input'),
            _check = this.checked ? true : false;
            jQuery.each(_prices, function(){
                jQuery(this).prop('checked', _check);
            });            
        });
        jQuery('table .nbd-pricing-table-add-rule').on('click', function(){
            var type = jQuery(this).attr('data-type'),
                tb = jQuery(this).parents('table.nbd_pricing_table').find('tbody'),
                row = type == 'area' ? nbd_area_price_row : nbd_quantity_price_row;
            tb.append(row);
        });
        jQuery('table .nbd-pricing-table-delete-rules').on('click', function(){
            var tb = jQuery(this).parents('table.nbd_pricing_table').find('tbody');
            jQuery.each(tb.find('input:checked'), function(){
                jQuery(this).parents('tr').remove();
            });       
            jQuery(this).parents('table.nbd_pricing_table').find('thead input').prop('checked', false);
        });
        jQuery('input[name="_nbdesigner_option[price_base_area]"]').on('change', function(){
            if(jQuery(this).is(':checked') && jQuery(this).val() == 1) {
                jQuery('#nbd_area_pricing_table_wrap').removeClass('nbdesigner-disable');
            }else{
                jQuery('#nbd_area_pricing_table_wrap').addClass('nbdesigner-disable');
            }
        });
        jQuery('input[name="_nbdesigner_option[price_base_quantity]"]').on('change', function(){
            if(jQuery(this).is(':checked') && jQuery(this).val() == 1) {
                jQuery('#nbd_quantity_pricing_table_wrap').removeClass('nbdesigner-disable');
            }else{
                jQuery('#nbd_quantity_pricing_table_wrap').addClass('nbdesigner-disable');
            }
        });        
    });
</script>