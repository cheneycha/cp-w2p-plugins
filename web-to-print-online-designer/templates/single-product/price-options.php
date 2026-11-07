<?php if (!defined('ABSPATH')) exit; ?>
<?php 
    if(isset($option['price_base_quantity']) && $option['price_base_quantity'] == 1): 
    $prices =  $option['price_base_quantity_prices']   
?>
<div>
    <table data-nbd-prices="" id="nbd-quantity-pricing">
        <tbody>
            <tr>
                <td>
                    <label for="nbd_quantity"><?php _e('Quantity', 'web-to-print-online-designer'); ?></label>
                </td>
                <td>
                    <input type="text" min="1" step="1" name="nbd_quantity" id="nbd_quantity" class="nbd_quantity" value="1">
                </td> 
            </tr>   
            <tr>
                <td>
                    <label><?php _e('Total price', 'web-to-print-online-designer'); ?></label>
                </td>
                <td>
                    <span class="product_price"></span>
                </td> 
            </tr>
        </tbody>
    </table>
</div>
<?php if( nbdesigner_get_option('nbdesigner_position_pricing_in_detail_page') == '2' ): ?>
<div class="nbd-quantity-pricing-table">
    <table>
        <thead>
            <tr>
                <th><label for="nbd_quantity"><?php _e('From', 'web-to-print-online-designer'); ?></label></th>
                <th><label for="nbd_quantity"><?php _e('To', 'web-to-print-online-designer'); ?></label></th>
                <th><label for="nbd_quantity"><?php _e('Price per unit', 'web-to-print-online-designer'); ?></label></th>
            </tr>             
        </thead>
        <tbody>
            <?php foreach( $prices['start'] as $key => $start ): ?>
            <tr>
                <td><?php echo $start; ?></td>          
                <td><?php echo $prices['end'][$key]; ?></td>
                <td><?php echo wc_price( $prices['price'][$key] ); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
<script>
    var nbd_price_params = {
        woocommerce_currency_symbol: "<?php echo get_woocommerce_currency_symbol(); ?>",
        woocommerce_price_num_decimals: <?php echo wc_get_price_decimals(); ?>,
        woocommerce_currency_pos: "<?php echo get_option( 'woocommerce_currency_pos', 'left' ); ?>",
        woocommerce_price_decimal_sep: "<?php echo stripslashes( wc_get_price_decimal_separator() ); ?>",
        woocommerce_price_thousand_sep: "<?php echo stripslashes( wc_get_price_thousand_separator() ); ?>",
        woocommerce_price_trim_zeros: "<?php echo get_option( 'woocommerce_price_trim_zeros' ); ?>",
        prices : '<?php echo json_encode($option['price_base_quantity_prices']); ?>'          
    };
    jQuery(document).ready(function(){
        var nbd_prices = JSON.parse(nbd_price_params.prices);
        jQuery('#nbd_quantity').on('change paste keyup', function(){
            var nbd_qty = parseInt( jQuery(this).val() ),
                first_match = false,
                price_per_unit = 1;
            if( nbd_qty > 0 ){
                jQuery.each(nbd_prices.start, function(index, value){
                    var start = value != '' ? parseInt( value ) : 0,
                        end = nbd_prices.end[index] != '' ? parseInt(nbd_prices.end[index]) : 0,
                        _price = convert_wc_price_to_float(nbd_prices.price[index]);
                    if( !first_match && start <= nbd_qty && ( end >= nbd_qty || end == 0 ) ) {
                        price_per_unit = _price; 
                        first_match = true;
                    }
                    if( !first_match && index == nbd_prices.start.length - 1 ) price_per_unit = _price; 
                });
                var price = price_per_unit * nbd_qty;
                jQuery('#nbd-quantity-pricing .product_price').html(convert_to_wc_price(price));
            }
        });
        var convert_wc_price_to_float = function( price ){
            var c = jQuery.trim(nbd_price_params.woocommerce_price_thousand_sep).toString(), 
                d = jQuery.trim(nbd_price_params.woocommerce_price_decimal_sep).toString();
            return price = price.replace(/ /g, ""), price = "." === c ? price.replace(/\./g, "") : price.replace(new RegExp(c,"g"), ""), price = price.replace(d, "."), price = parseFloat(price)
        };
        var convert_to_wc_price = function( price ){
            price = price.toFixed(nbd_price_params.woocommerce_price_num_decimals);
            __price = price.toString().split('.');
            if( __price[0].length > 3 ){
                __price[0] = __price[0].replace(/\B(?=(\d{3})+(?!\d))/g, nbd_price_params.woocommerce_price_thousand_sep);
                price = ( __price[1] || '' ).length > 1 ? __price[0] + nbd_price_params.woocommerce_price_decimal_sep + __price[1] : __price[0];
            }
            var _price = '';
            switch( nbd_price_params.woocommerce_currency_pos ){
                case "left":
                    _price = '<span class="amount">' + nbd_price_params.woocommerce_currency_symbol + price + "</span>";
                    break;
                case "right":
                    _price = '<span class="amount">' + price + nbd_price_params.woocommerce_currency_symbol + "</span>";
                    break;
                case "left_space":
                    _price = '<span class="amount">' + nbd_price_params.woocommerce_currency_symbol + "&nbsp;" + price + "</span>";
                    break;
                case "right_space":
                    _price = '<span class="amount">' + price + "&nbsp;" + nbd_price_params.woocommerce_currency_symbol + "</span>"                
            }
            return _price;
        }
    });
</script>
<?php endif; ?>
