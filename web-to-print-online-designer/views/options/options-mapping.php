<?php 
    if (!defined('ABSPATH')) exit; 

    function nbo_mapping_return_true() {
        return true;
    }

    global $post, $nbd_fontend_printing_options;

    $post_id = $post->ID;
    $product = wc_get_product( $post_id );
    if( $product->get_type() != 'variable' ) return;

    add_filter( 'woocommerce_variation_is_visible', array($this, 'nbo_mapping_return_true') );

    $nbo_enable_mapping     = $product->get_meta('_enable_nbo_mapping', true);
    $nbo_maps               = $product->get_meta('_nbo_maps', true);
    $nbo_maps               = $nbo_maps ? $nbo_maps : array();
    $print_options_vailable = false;
    $atributes_vailable     = false;
    $print_option_fields    = array();

    $option_id = $nbd_fontend_printing_options->get_product_option( $post_id );
    if( $option_id ){
        $_options = $nbd_fontend_printing_options->get_option( $option_id );
        $options = unserialize( $_options['fields'] );
        if( isset( $options['fields'] ) ){
            $options['fields'] = $nbd_fontend_printing_options->recursive_stripslashes( $options['fields'] );
            if( count( $options['fields'] ) ){
                $count = 0;
                foreach ( $options['fields'] as $key => $field ){
                    if( $field['general']['data_type'] == 'm' && isset( $field['general']['attributes']['options'] ) && count( $field['general']['attributes']['options'] ) > 0 ){
                        if( isset( $options["bulk_fields"] ) && count( $options["bulk_fields"] ) > 1 ){
                            $is_in_bulk = false;
                            foreach( $options["bulk_fields"] as $bkey => $bulk_index ){
                                if( $key == $bulk_index ){
                                    $is_in_bulk = true;
                                    break;
                                }
                            }
                            if( !$is_in_bulk ){
                                $count++;
                                $print_option_fields[] = $field;
                            }
                        }else{
                            $count++;
                            $print_option_fields[] = $field;
                        }
                    }
                }
                if( $count ){
                    $print_options_vailable = true;
                }
            }
        }
    }

    if( $print_options_vailable ){
        $woocommerce_taxonomies     = wc_get_attribute_taxonomies();
        $woocommerce_taxonomy_infos = array();
        foreach ( $woocommerce_taxonomies as $tax ) {
            $woocommerce_taxonomy_infos[wc_attribute_taxonomy_name( $tax->attribute_name )] = $tax;
        }
        $tax        = null;
        $attributes = $product->get_variation_attributes();
        if ( $attributes && count( $attributes ) ){
            $atributes_vailable = true;
            $attribute_names = array_keys( $attributes );
        }
    }
?>
<div id="nbo_mapping" class="panel nbo_mapping woocommerce_options_panel wc-metaboxes-wrapper hidden">
    <div class="options_group">
        <p class="form-field">
            <label for="_enable_nbo_mapping"><?php _e('Enable', 'web-to-print-online-designer'); ?></label>
            <input type="checkbox" value="1" <?php checked( $nbo_enable_mapping ); ?> name="_enable_nbo_mapping" id="_enable_nbo_mapping" />
            <span class="description"><?php _e('Enable map print option fields with product attributes.', 'web-to-print-online-designer'); ?></span>
        </p>
    </div>
    <div class="options_group" id="nbo_maps_wrap">
        <?php if( $print_options_vailable && $atributes_vailable ) :?>
        <div class="nbo_maps_table_wrap">
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php _e('Product Attribute', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Print Options Field', 'web-to-print-online-designer'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        foreach ( $attribute_names as $name ) : 
                            $key                    = md5( sanitize_title( $name ) );
                            $current_is_taxonomy    = taxonomy_exists( $name );
                            $current_options        = false;
                            $attribute_terms        = array();

                            if( isset( $nbo_maps[ $key ] ) ){
                                $current_options        = $nbo_maps[ $key ];
                            }

                            if ( taxonomy_exists( $name ) ){
                                $tax                    = get_taxonomy( $name );
                                $woocommerce_taxonomy   = $woocommerce_taxonomy_infos[$name];
                                $current_label          = isset( $woocommerce_taxonomy->attribute_label ) && !empty( $woocommerce_taxonomy->attribute_label ) ? $woocommerce_taxonomy->attribute_label : $woocommerce_taxonomy->attribute_name;
                            
                                $terms                  = get_terms( $name, array('hide_empty' => false) );
                                $selected_terms         = isset( $attributes[$name] ) ? $attributes[$name] : array();
                                foreach ( $terms as $term ) {
                                    if ( in_array( $term->slug, $selected_terms ) ) {
                                        $attribute_terms[] = array('id' => md5( $term->slug ), 'label' => $term->name);
                                    }
                                }
                            }else{
                                $current_label = esc_html( $name );
                                foreach ( $attributes[$name] as $term ) {
                                    $attribute_terms[] = array('id' => ( md5( sanitize_title( strtolower( $term ) ) ) ), 'label' => esc_html( $term ));
                                }
                            }
                    ?>
                    <tr>
                        <td class="nbo_map_attribute" data-term="<?php echo count( $attribute_terms ); ?>" style="font-weight: bold;"><?php echo $current_label; ?></td>
                        <td>
                            <select name="_nbo_maps[<?php echo $key; ?>]" class="nbo_map_field_options">
                                <option value=""><?php _e('Choose Print Options Field', 'web-to-print-online-designer'); ?></option>
                                <?php
                                    foreach( $print_option_fields as $print_option_field ): 
                                        $selected = ( $current_options && $current_options == $print_option_field['id'] ) ? 'selected="selected"' : '';
                                ?>
                                <option data-option="<?php echo count( $print_option_field['general']['attributes']['options'] ); ?>" 
                                    value="<?php echo $print_option_field['id']; ?>" <?php echo $selected; ?> ><?php echo $print_option_field['general']['title']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align: right;">
                            <button class="button nbo_map_reset"><?php _e('Reset', 'web-to-print-online-designer'); ?></button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <?php else: ?>
        <p><?php _e( 'Add at least one attribute / variation combination to this product that has been mapped with "Print options" fields. Enable print options for this product <a href="#nbo-options">here</a>. After you add the attributes from the "Attributes" tab and create a variation, save the product, maybe create new "Printing options" and comeback here you will see the print options mapping here.', 'web-to-print-online-designer' ); ?></p>
        <?php endif; ?>
    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function(){
        (function ($, window, document, undefined) {
            var field_options = $('.nbo_map_field_options');
            var resetOptions = function(){
                $.each( field_options, function(){
                    var self = $(this),
                    selfVal = self.val();

                    $.each( self.find('option'), function(){
                        var val = $(this).attr('value'), check = false;
                        $.each( field_options, function(){
                            if( $(this).val() == val ) check = true;
                        });
                        if( !check ){
                            $(this).removeAttr('disabled');
                        }
                    });
                });
            };
            var checkOptions = function(){
                $.each( field_options, function(){
                    var self = $(this),
                    val = self.val(),
                    option = self.find('option[value="' + val + '"]'),
                    noOfField = option.attr('data-option'),
                    noOfAttr = self.parents('tr').find('.nbo_map_attribute').attr('data-term');
                    if( val != '' && noOfField != noOfAttr ){
                        self.val('');
                    }
                });

                $.each( field_options, function(){
                    var self = $(this),
                    val = self.val();

                    if( val != '' ){
                        $.each( field_options, function(){
                            var that = $(this);
                            if( !that.is( self ) ){
                                var option = that.find('option[value="' + val + '"]');
                                option.attr('disabled', 'disabled');
                            }
                        } );
                    }
                });

                resetOptions();
            };
            checkOptions();

            if( $('#_enable_nbo_mapping').prop('checked') ){
                $('#nbo_maps_wrap').show();
            }else{
                $('#nbo_maps_wrap').hide();
            }

            $('#_enable_nbo_mapping').on('change', function(){
                if( $(this).prop('checked') ){
                    $('#nbo_maps_wrap').show();
                }else{
                    $('#nbo_maps_wrap').hide();
                }
            });

            field_options.on('change', function(){
                var self = $(this),
                    val = self.val(),
                    _option = self.find('option[value="' + val + '"]'),
                    noOfField = _option.attr('data-option'),
                    noOfAttr = self.parents('tr').find('.nbo_map_attribute').attr('data-term');
                    
                if( val != '' ){
                    if( noOfField == noOfAttr ){
                        $.each( field_options, function(){
                            var that = $(this);
                            if( !that.is( self ) ){
                                var option = that.find('option[value="' + val + '"]');
                                option.attr('disabled', 'disabled');
                            }
                        } );
                    }else{
                        self.val('');
                        alert('<?php _e('Number of attribute values must equal number of print option field options.', 'web-to-print-online-designer'); ?>')
                    }
                }
                resetOptions();
            });

            $('.nbo_map_reset').on('click', function(e){
                e.preventDefault();
                field_options.val('');
            });
        })(jQuery, window, document);
    });
</script>
<?php
    remove_filter( 'woocommerce_variation_is_visible', 'nbo_mapping_return_true' );