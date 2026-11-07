<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( !class_exists('Nbdesigner_Launcher') ) {
    class Nbdesigner_Launcher{
        public static function get_options() {
            return apply_filters('nbdesigner_laucher_settings', array(
                'general' => array(
                    array(
                        'title'         => __( 'Enable designer store', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_designer_store',
                        'description'   => sprintf(__( 'Allow customers become designers who create and sell their designs. After "Save options" go to <a target="_blank" href="%s">Permalink</a> choose pretty permalinks and "Save changes". Default permalinks will not work.', 'web-to-print-online-designer'), esc_url(admin_url('options-permalink.php'))),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    )
                ),
                'admin' => array(
                    array(
                        'title'         => __( 'Commission Type', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_commission_type',
                        'description'   => __('Select a commission type for designer.', 'web-to-print-online-designer'),
                        'default'       => 'percentage',
                        'type'          => 'select',
                        'class'         => 'depend_trigger',
                        'options'       => array(
                            'percentage'    => __('Percentage', 'web-to-print-online-designer'),
                            'flat'          => __('Flat', 'web-to-print-online-designer'),
                            'combine'       => __('Combine', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => __( 'Default commission', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_default_commission',
                        'default'       => 0,
                        'description'   => __( 'Amount designers get from each sale has their designs.', 'web-to-print-online-designer'),
                        'type'          => 'text',
                        'class'         => 'regular-text',
                        'css'           => 'width: 85px',
                        'depend_on'     => array(
                            'id'        => 'nbdesigner_commission_type',
                            'value'     => 'combine',
                            'operator'  => '#'
                        )
                    ),
                    array(
                        'title'         => __( 'Default commission', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_default_commission2',
                        'description'   => __( 'Amount designers will get from sales has their designs in both percentage and fixed fee', 'web-to-print-online-designer' ),
                        'css'           => 'width: 85px',
                        'default'       => '0|0',
                        'type'          => 'multivalues',
                        'options'       => array(
                            0           => '',
                            1           => __( '%  +', 'web-to-print-online-designer')
                        ),
                        'depend_on'     => array(
                            'id'        => 'nbdesigner_commission_type',
                            'value'     => 'combine',
                            'operator'  => '='
                        )
                    ),
                    array(
                        'title'         => __( 'Minimum Withdraw Limit', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_minimum_withdraw',
                        'default'       => 0,
                        'description'   => __( 'Minimum balance required to make a withdraw request. Leave blank to set no minimum limits.', 'web-to-print-online-designer'),
                        'type'          => 'text',
                        'css'           => 'width: 85px',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => __( 'Withdraw Threshold', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_withdraw_threshold',
                        'default'       => 0,
                        'description'   => __( 'Days, ( Delay time to active order designer earning )', 'web-to-print-online-designer'),
                        'type'          => 'text',
                        'css'           => 'width: 85px',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'Order Status for Withdraw', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_order_status_for_withdraw',
                        'default'       => json_encode(array(
                                'nbdesigner_order_status_for_withdraw_wc-completed'     => 1,
                                'nbdesigner_order_status_for_withdraw_wc-processing'    => 0,
                                'nbdesigner_order_status_for_withdraw_wc-on-hold'       => 0
                            )),
                        'description'   => '',
                        'type'          => 'multicheckbox',
                        'class'         => 'regular-text',
                        'options'       => array(
                            'nbdesigner_order_status_for_withdraw_wc-completed'     => esc_html__('Completed', 'web-to-print-online-designer'),
                            'nbdesigner_order_status_for_withdraw_wc-processing'    => esc_html__('Processing', 'web-to-print-online-designer'),
                            'nbdesigner_order_status_for_withdraw_wc-on-hold'       => esc_html__('On-hold', 'web-to-print-online-designer')
                        ),
                        'css' => 'margin: 0 15px 10px 5px;'
                    )
                ),
                'designer' => array(
                    array(
                        'title'         => __( 'Designer page banner width', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_designer_banner_width',
                        'default'       => 1050,
                        'description'   => '',
                        'type'          => 'number',
                        'class'         => 'regular-text',
                        'css'           => 'width: 85px',
                        'subfix'        => ' px'
                    ),
                    array(
                        'title'         => __( 'Designer page banner height', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_designer_banner_height',
                        'default'       => 200,
                        'description'   => '',
                        'type'          => 'number',
                        'class'         => 'regular-text',
                        'css'           => 'width: 85px',
                        'subfix'        => ' px'
                    )
                ),
                'design' => array(
                    array(
                        'title'         => __( 'Generate preview for product has print option color automatically', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_auto_generate_color_product_preview',
                        'description'   => __( 'Beware this option turn on the process which consumes a lot of system resources', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    )
                )
            ));
        }
    }
}