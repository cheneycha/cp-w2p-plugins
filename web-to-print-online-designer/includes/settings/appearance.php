<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( !class_exists('Nbdesigner_Appearance_Settings') ) {
    class Nbdesigner_Appearance_Settings {
        public static function get_options() {
            return apply_filters('nbdesigner_appearance_settings', array(
                'editor'  =>  array(
                    array(
                        'title'         => esc_html__('Show bleed', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_show_bleed',
                        'description'   => esc_html__( 'Hide/show bleed, safe zone as default in design editor.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    )
                ),
                'modern' => array( 
                    array(
                        'title'         => esc_html__('Show ruler', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_show_ruler',
                        'description'   => esc_html__( 'Hide/show ruler as default in design editor.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__('Show product dimensions', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_show_product_dimensions',
                        'description'   => esc_html__( 'Hide/show product dimensions as default in design editor.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__('Show grid', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_show_grid',
                        'description'   => esc_html__( 'Hide/show grid as default in design editor.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),   
                    array(
                        'title'         => esc_html__('Show layer dimension', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_show_layer_size',
                        'description'   => esc_html__( 'Show/hide layer dimension in design editor.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ), 
                    array(
                        'title'         => esc_html__('Show warning out of stage', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_show_warning_oos',
                        'description'   => esc_html__( 'Hide/show warning out of stage as default in design editor.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__('Show warning image low resolution', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_show_warning_ilr',
                        'description'   => esc_html__( 'Hide/show warning image low resolution as default in design editor.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__('Show design area border', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_show_design_border',
                        'description'   => esc_html__( 'Show/hide design area border.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Hide Templates tab', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_hide_template_tab',
                        'description'   => esc_html__( 'Hide Templates tab in modern layout', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Hide Elements tab', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_hide_element_tab',
                        'description'   => esc_html__( 'Hide Elements tab in modern layout', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Hide Typography section', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_hide_typo_section',
                        'description'   => esc_html__( 'Hide Typography section in modern layout', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Hide manage layers tab', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_hide_layer_tab',
                        'description'   => esc_html__( 'Hide manage layers tab in modern layout', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Show button change product', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_show_button_change_product',
                        'description'   => '',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Show all template sides when hover on the template', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_show_all_template_sides',
                        'description'   => '',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Display template mode', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_display_template_mode',
                        'description'   => '',
                        'default'       => '1',
                        'type'          => 'radio',
                        'local'         => false,
                        'options'       => array(
                            '1' => esc_html__('Flatlist', 'web-to-print-online-designer'),
                            '2' => esc_html__('Categories', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Show button link to browse product templates gallery', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_button_link_product_template',
                        'description'   => esc_html__( 'This button will show in start design popup', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'local'         => false,
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Logo', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_editor_logo',
                        'description'   => esc_html__('Choose design editor logo if site logo not showing', 'web-to-print-online-designer'),
                        'default'       => '',
                        'type'          => 'nbd-media',
                        'local'         => false,
                    )
                ),
                'product' => array( 
                    array(
                        'title'         => esc_html__('Show design tool', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_page_design_tool',
                        'default'       => '1',
                        'description'   => esc_html__( 'Show design tool in product detail page or open new page', 'web-to-print-online-designer'),
                        'type'          => 'radio',
                        'options'       => array(
                            '1' => esc_html__('In product detail page', 'web-to-print-online-designer'),
                            '2' => esc_html__('Open new page', 'web-to-print-online-designer')
                        )
                    ),     
                    array(
                        'title'         => esc_html__('Auto add to cart and redirect', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_auto_add_cart_in_detail_page',
                        'description'   => esc_html__( 'Auto add to cart and redirect to cart page after save design in product detail page, depend option "Show design tool: In product detail page".', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__('Position of design button', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_position_button_product_detail',
                        'default'       => '1',
                        'description'   => esc_html__( 'The position of the product button designer in the product page', 'web-to-print-online-designer'),
                        'type'          => 'radio',
                        'options'       => array(
                            '1' => esc_html__('Before add to cart button and after variantions option', 'web-to-print-online-designer'),
                            '2' => esc_html__('Before variantions option', 'web-to-print-online-designer'),
                            '3' => esc_html__('After add to cart button', 'web-to-print-online-designer'),
                            '4' => __('Custom Hook, <code>echo do_shortcode( \'[nbdesigner_button]\' );</code> in product page', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__('Separate artwork action buttons', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_separate_design_buttons',
                        'description'   => esc_html__( 'Show artwork actions as buttons directly on the product page instead of wrap them on the popup.', 'web-to-print-online-designer' ),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Class for "Start design" button in product page', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Enter your class to show "Start design" button with your style.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_class_design_button_detail',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text',
                        'placeholder'   => 'nbd-btn'
                    ),
                    array(
                        'title'         => esc_html__('Hide button Add to cart before complete design', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_hide_button_cart_in_detail_page',
                        'description'   => esc_html__( 'Only show button Add to cart after customer complete they design.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    )
                ),
                'category' => array( 
                    array(
                        'title'         => esc_html__('Position of "Start design" button in the catalog', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_position_button_in_catalog',
                        'default'       => '1',
                        'description'   => esc_html__( 'The position of the button in the catalog listing.', 'web-to-print-online-designer'),
                        'type'          => 'radio',
                        'options'       => array(
                            '1' => esc_html__('Replace Add-to-Cart button', 'web-to-print-online-designer'),
                            '2' => esc_html__('End of catalog item', 'web-to-print-online-designer'),
                            '3' => esc_html__('Do not show', 'web-to-print-online-designer')
                        )
                    ),  
                    array(
                        'title'         => esc_html__( 'Class for "Start design" button in catalog page', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Enter your class to show "Start design" button with your style.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_class_design_button_catalog',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text',
                        'placeholder'   => 'nbd-btn'
                    ),
                    array(
                        'title'         => esc_html__('Show options popup', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_show_popup_od_option_in_cat',
                        'description'   => esc_html__( 'Show/hide options popup when click start design button in archive page.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    )
                ),
                'cart-checkout-order' => array( 
                    array(
                        'title'         => esc_html__( 'Show customer design in cart, checkout page', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_show_in_cart',
                        'description'   => esc_html__('Show the thumbnail of the customized product in the cart, checkout page.', 'web-to-print-online-designer'),
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Show button edit design, reupload file in cart, checkout page', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_show_button_edit_design_in_cart',
                        'description'   => '',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Show customer design in order', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_show_in_order',
                        'description'   => esc_html__('Show the thumbnail of the customized product in the order.', 'web-to-print-online-designer'),
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    )
                ),
                'misc' => array( 
                    array(
                        'title'         => esc_html__( 'Enable service "Lets design your artword for you"', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_button_hire_designer',
                        'description'   => esc_html__('Allow the customer hire you design the artword for them.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Show popup design option in category page', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_show_popup_design_option',
                        'description'   => '',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    )
                )
            ));
        }
    }
}