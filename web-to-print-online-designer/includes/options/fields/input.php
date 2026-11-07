<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly 
if(!class_exists('NBD_Printing_Input_Field')){
    class NBD_Printing_Input_Field{
        public function __construct() {
            //todo
        }
        public static function get_options() {
            return apply_filters('nbd_printing_options_input', array(
                'general' => array(                          
                    array(
                        'title' => __( 'Title', 'web-to-print-online-designer'),
                        'field' => 'title',
                        'description'   =>  '',
                        'class' => '',
                        'css'         => '',
                        'value'	=> 'Title',
                        'type' 		=> 'input'
                    ),  
                    array(
                        'title' => __( 'Description', 'web-to-print-online-designer'),
                        'field' => 'description',
                        'class' => '',
                        'description'   =>  '',
                        'css'         => '',
                        'value'	=> 'Description',
                        'type' 		=> 'textarea'
                    ),     
                    array(
                        'title' => __( 'Data type', 'web-to-print-online-designer'),
                        'field' => 'type',
                        'class' => '',
                        'description'   =>  '',
                        'css'         => '',
                        'value'	=> 't',
                        'type' 		=> 'dropdown',
                        'options' =>    array(
                            't'   =>  __( 'Text', 'web-to-print-online-designer'),
                            'n'   =>  __( 'Number', 'web-to-print-online-designer'),
                            'e'   =>  __( 'Email', 'web-to-print-online-designer'),
                        )
                    ),                     
                    array(
                        'title' => __( 'Enabled', 'web-to-print-online-designer'),
                        'field' => 'enabled',
                        'class' => '',
                        'description'   =>  'Choose whether the option is enabled or not.',
                        'css'         => '',
                        'value'	=> 'y',
                        'type' 		=> 'radio',
                        'options' =>    array(
                            'y'   =>  __( 'Yes', 'web-to-print-online-designer'),
                            'n'   =>  __( 'No', 'web-to-print-online-designer')
                        )
                    ),   
                    array(
                        'title' => __( 'Required', 'web-to-print-online-designer'),
                        'field' => 'required',
                        'class' => '',
                        'description'   =>  'Choose whether the option is enabled or not.',
                        'css'         => '',
                        'value'	=> 'y',
                        'type' 		=> 'radio',
                        'options' =>    array(
                            'y'   =>  __( 'Yes', 'web-to-print-online-designer'),
                            'n'   =>  __( 'No', 'web-to-print-online-designer')
                        )
                    ), 
                    array(
                        'title' => __( 'Price type', 'web-to-print-online-designer'),
                        'field' => 'price_type',
                        'class' => '',
                        'description'   =>  '',
                        'css'         => '',
                        'value'	=> 'f',
                        'type' 		=> 'dropdown',
                        'options' =>    array(
                            'f'   =>  __( 'Fixed amount', 'web-to-print-online-designer'),
                            'p'   =>  __( 'Percent of the original price', 'web-to-print-online-designer'),
                            'p+'   =>  __( 'Percent of the original price + options', 'web-to-print-online-designer'),
                            'c'   =>  __( 'Current value * price', 'web-to-print-online-designer'),
                            'd'   =>  __( 'Price depend quantity', 'web-to-print-online-designer'),
                        )
                    ),                      
                    array(
                        'title' => __( 'Price', 'web-to-print-online-designer'),
                        'field' => 'price',
                        'description'   =>  'Enter the price for this field or leave it blank for no price.',
                        'class' => '',
                        'css'         => '',
                        'value'	=> '',
                        'type' 		=> 'number'
                    ),     
                    array(
                        'title' => __( 'Sale Price', 'web-to-print-online-designer'),
                        'field' => 'sale_price',
                        'description'   =>  'Enter the sale price for this field or leave it blankto use the default price.',
                        'class' => '',
                        'css'         => '',
                        'value'	=> '',
                        'type' 		=> 'number'
                    )                                      
                ),
                'conditional' => array(                          
                    array(
                        'title' => __( 'Title', 'web-to-print-online-designer'),
                        'class' => '',
                        'css'         => '',
                        'default'	=> '',
                        'type' 		=> 'input'
                    ),  
                ),
                'appearance' => array(                          
                    array(
                        'title' => __( 'Title', 'web-to-print-online-designer'),
                        'class' => '',
                        'css'         => '',
                        'default'	=> '',
                        'type' 		=> 'input'
                    ),   
                )                 
            ));
        }
    }
}