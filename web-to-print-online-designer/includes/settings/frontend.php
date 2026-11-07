<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if(!class_exists('Nbdesigner_Settings_Frontend')){
    class Nbdesigner_Settings_Frontend {
        public static function get_options() {
            return apply_filters('nbdesigner_design_tool_settings', array(
                'tool-text' => array(
                    array(
                        'title'         => esc_html__( 'Enable tool Add Text', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_text',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Notice Emoji Warning!', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_detect_emoji',
                        'default'       => 'Not support Emoji, please dont use it for design',
                        'description'   => esc_html__( 'Notice For customer about not support Emoji', 'web-to-print-online-designer'),
                        'type'          => 'textarea'
                    ),
                    array(
                        'title'         => esc_html__( 'Warning Lower Dimension Image', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_detect_low_dimension',
                        'default'       => 'Warning Lower Dimension Image',
                        'description'   => esc_html__( 'Notice For Lower Dimension Image ', 'web-to-print-online-designer'),
                        'type'          => 'textarea'
                    ),
                    array(
                        'title'         => esc_html__( 'Default font subset', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Choose your language font subset.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_default_font_subset',
                        'type'          => 'select',
                        'default'       => 'all',
                        'options'       => _nbd_font_subsets(),
                        'local'         => false
                    ),
                    array(
                        'title'         => esc_html__( 'Enable check text language', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_text_check_lang',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'description'   =>  esc_html__('Show warning when the font can not show some of the customer added text character.', 'web-to-print-online-designer'),
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ),
                        'local'         => false
                    ),
                    array(
                        'title'         => esc_html__( 'Default text', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_default_text',
                        'default'       => 'Text here',
                        'description'   => esc_html__( 'Default text when user add text', 'web-to-print-online-designer'),
                        'type'          => 'text',
                        'layout'        => 'c',
                        'class'         => 'regular-text',
                    ),
                    array(
                        'title'         => esc_html__( 'Default color', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_default_color',
                        'default'       => '#cc324b',
                        'description'   => sprintf(__( 'Default color text when user add text. If you\'re using limited color palette, make sure <a href="%s">this color</a> has been defined', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=color'))),
                        'type'          => 'colorpicker'
                    ),
                    array(
                        'title'         => esc_html__( 'Enable Curved Text', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_curvedtext',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'description'   =>  '',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Enable Text transform unproportionally', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_text_free_transform',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'description'   =>  esc_html__('When true, Text can be transformed unproportionally', 'web-to-print-online-designer'),
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Default font sizes( pt )', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_default_font_sizes',
                        'default'       => '6,8,10,12,14,16,18,21,24,28,32,36,42,48,56,64,72,80,88,96,104,120,144,288,576,1152',
                        'description'   => esc_html__( 'Increment font sizes in pt seperated by a comma. Do not use dots or spaces. Example: 6,8,10,12,14,16', 'web-to-print-online-designer'),
                        'type'          => 'text',
                        'class'         => 'regular-text',
                        'css'           => 'width: 45em;'
                    ),
                    array(
                        'title'         => esc_html__( 'Force min font size', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_force_min_font_size',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'description'   => esc_html__('Disallow text font size less than min value in list font sizes.', 'web-to-print-online-designer'),
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Force max font size', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_force_max_font_size',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'description'   =>  esc_html__('Disallow text font size greater than max value in list font sizes.', 'web-to-print-online-designer'),
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Only use font size in list', 'web-to-print-online-designer' ),
                        'id'            => 'nbdesigner_force_font_size_list',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'description'   => esc_html__( 'Turn on this option, the text will be locked scalability. Change font size in pre-defined list to scale the text.', 'web-to-print-online-designer' ),
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Minimum font size( pt )', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_min_font_size',
                        'default'       => '',
                        'description'   => esc_html__( 'Leave empty this input to dismiss this setting.', 'web-to-print-online-designer'),
                        'type'          => 'text',
                        'class'         => 'regular-text',
                        'css'           => 'width: 65px;'
                    ),
                    array(
                        'title'         => esc_html__( 'Maximum font size( pt )', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_max_font_size',
                        'default'       => '',
                        'description'   => esc_html__( 'Leave empty this input to dismiss this setting.', 'web-to-print-online-designer'),
                        'type'          => 'text',
                        'class'         => 'regular-text',
                        'css'           => 'width: 65px;'
                    ),
                    array(
                        'title'         => esc_html__( 'Enable Text pattern', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_textpattern',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'layout'        => 'c',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Show/hide features', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_option_text',
                        'default'       => json_encode(array(
                                'nbdesigner_text_change_font'   => 1,
                                'nbdesigner_text_italic'        => 1,
                                'nbdesigner_text_bold'          => 1,
                                'nbdesigner_text_underline'     => 0,
                                'nbdesigner_text_through'       => 0,
                                'nbdesigner_text_overline'      => 0,
                                'nbdesigner_text_case'          => 1,
                                'nbdesigner_text_align_left'    => 1,
                                'nbdesigner_text_align_right'   => 1,
                                'nbdesigner_text_align_center'  => 1,
                                'nbdesigner_text_color'         => 1,
                                'nbdesigner_text_background'    => 1,
                                'nbdesigner_text_shadow'        => 0,
                                'nbdesigner_text_line_height'   => 1,
                                'nbdesigner_text_spacing'       => 1,
                                'nbdesigner_text_font_size'     => 1,
                                'nbdesigner_text_opacity'       => 1,
                                'nbdesigner_text_outline'       => 1,
                                'nbdesigner_text_proportion'    => 1,
                                'nbdesigner_text_rotate'        => 1
                            )),
                        'description'   => esc_html__( 'Show/hide features in frontend', 'web-to-print-online-designer'),
                        'type'          => 'multicheckbox',
                        'class'         => 'regular-text',
                        'enable_select' => 1,
                        'options'       => array(
                            'nbdesigner_text_change_font'   => esc_html__('Change font', 'web-to-print-online-designer'),
                            'nbdesigner_text_italic'        => esc_html__('Italic', 'web-to-print-online-designer'),
                            'nbdesigner_text_bold'          => esc_html__('Bold', 'web-to-print-online-designer'),
                            'nbdesigner_text_underline'     => esc_html__('Underline', 'web-to-print-online-designer'),
                            'nbdesigner_text_through'       => esc_html__('Line-through', 'web-to-print-online-designer'),
                            'nbdesigner_text_overline'      => esc_html__('Overline', 'web-to-print-online-designer'),
                            'nbdesigner_text_case'          => esc_html__('Text Case', 'web-to-print-online-designer'),
                            'nbdesigner_text_align_left'    => esc_html__('Align left', 'web-to-print-online-designer'),
                            'nbdesigner_text_align_right'   => esc_html__('Align right', 'web-to-print-online-designer'),
                            'nbdesigner_text_align_center'  => esc_html__('Align center', 'web-to-print-online-designer'),
                            'nbdesigner_text_color'         => esc_html__('Text color', 'web-to-print-online-designer'),
                            'nbdesigner_text_background'    => esc_html__('Text background', 'web-to-print-online-designer'),
                            'nbdesigner_text_shadow'        => esc_html__('Text shadow', 'web-to-print-online-designer'),
                            'nbdesigner_text_line_height'   => esc_html__('Line height', 'web-to-print-online-designer'),
                            'nbdesigner_text_spacing'       => esc_html__('Spacing', 'web-to-print-online-designer'),
                            'nbdesigner_text_font_size'     => esc_html__('Font size', 'web-to-print-online-designer'),
                            'nbdesigner_text_opacity'       => esc_html__('Opacity', 'web-to-print-online-designer'),
                            'nbdesigner_text_outline'       => esc_html__('Outline', 'web-to-print-online-designer'),
                            'nbdesigner_text_proportion'    => esc_html__('Unlock proportion', 'web-to-print-online-designer'),
                            'nbdesigner_text_rotate'        => esc_html__('Rotate', 'web-to-print-online-designer')
                        ),
                        'css'           => 'margin: 0 15px 10px 5px;'
                    )
                ),
                'tool-clipart' => array(
                    array(
                        'title'         => esc_html__( 'Enable tool Add Clipart', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_clipart',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Show/hide clipart features', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_option_clipart',
                        'default'       => json_encode(array(
                            'nbdesigner_clipart_change_path_color' => 1,
                            'nbdesigner_clipart_rotate' => 1,
                            'nbdesigner_clipart_opacity' => 1
                        )),
                        'description'   => esc_html__( 'Show/hide features in frontend', 'web-to-print-online-designer'),
                        'type'          => 'multicheckbox',
                        'class'         => 'regular-text',
                        'options'       => array(
                            'nbdesigner_clipart_change_path_color'  => esc_html__( 'Change color path', 'web-to-print-online-designer'),      
                            'nbdesigner_clipart_rotate'             => esc_html__( 'Rotate', 'web-to-print-online-designer'),      
                            'nbdesigner_clipart_opacity'            => esc_html__( 'Opacity', 'web-to-print-online-designer')
                        ),
                        'css'           => 'margin: 0 15px 10px 5px;'
                    ),
                    array(
                        'title'         => esc_html__( 'Enable elements shapes', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_shapes',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Enable elements icons', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_icons',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Enable Flaticon', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_flaticon',
                        'description'   => sprintf(__( 'Require <a href="%s" target="_blank" >Flaticon API Key</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=general#nbdesigner_flaticon_api_key'))),
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Enable Storyset', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_storyset',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Enable grid photo frame', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_frame',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Enable photo frame', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_photo_frame',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Enable Google Maps Static', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_google_maps',
                        'description'   => sprintf(__( 'Require <a href="%s" target="_blank" >Google Maps Static API key</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=general#nbdesigner_static_map_api_key'))),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    )
                ),
                'tool-image' => array(
                    array(
                        'title'         => esc_html__( 'Enable tool Add Image', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_image',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ), 
                    array(
                        'title'         => esc_html__( 'Enable upload image', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_upload_image',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),  
                    array(
                        'title'         => esc_html__( 'Auto fit uploaded image with the stage', 'web-to-print-online-designer'),
                        'id' 		=> 'nbdesigner_enable_auto_fit_image',
                        'default'	=> 'no',
                        'type' 		=> 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__('Login Required', 'web-to-print-online-designer'),
                        'description'   => esc_html__('Users must create an account in your Wordpress site and need to be logged-in to upload images.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_upload_designs_php_logged_in',
                        'default'       => 'no',
                        'type'          => 'checkbox'
                    ), 
                    array(
                        'title'         => esc_html__('Allow upload multiple images', 'web-to-print-online-designer'),
                        'description'   => esc_html__('Allow the customer upload multiple images.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_upload_multiple_images',
                        'default'       => 'no',
                        'type'          => 'checkbox'
                    ),    
                    array(
                        'title'         => esc_html__('Max upload files at once', 'web-to-print-online-designer'),
                        'description'   => '',
                        'id'            => 'nbdesigner_max_upload_files_at_once',
                        'default'       => 5,
                        'css'           => 'width: 65px',
                        'type'          => 'number'
                    ),
                    array(
                        'title'         => esc_html__( 'Max upload size', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_maxsize_upload',
                        'css'           => 'width: 65px',
                        'default'       => nbd_get_max_upload_default(),
                        'subfix'        => ' MB',
                        'type'          => 'number'
                    ),    
                    array(
                        'title'         => esc_html__( 'Min upload size', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_minsize_upload',
                        'css'           => 'width: 65px',
                        'default'       => '0',
                        'subfix'        => ' MB',
                        'type'          => 'number'
                    ),
                    array(
                        'title'         => esc_html__( 'Min image upload resolution', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_mindpi_upload',
                        'css'           => 'width: 65px',
                        'default'       => '0',
                        'subfix'        => ' DPI',
                        'type'          => 'number'
                    ),  
                    array(
                        'title'         => esc_html__( 'Enable low resolution image', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_low_resolution_image',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'description'   =>  esc_html__( 'Alert a message to the customer', 'web-to-print-online-designer'),
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Enable images from url', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_image_url',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ), 
                    array(
                        'title'         => esc_html__( 'Enable get images from Google Drive', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_google_drive',
                        'description'   => sprintf(__( 'Require <a href="%s" target="_blank" >Google API key and Google Client ID</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=general#nbdesigner_google_api_key'))),
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ), 
                    array(
                        'title'         => esc_html__( 'Enable images from Pixabay', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_pixabay',
                        'description'   => sprintf(__( 'Require <a href="%s" target="_blank" >Pixabay API Key</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=general#nbdesigner_pixabay_api_key'))),
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ), 
                    array(
                        'title'         => esc_html__( 'Enable images from Unsplash', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_unsplash',
                        'description'   => sprintf(__( 'Require <a href="%s" target="_blank" >Unsplash API Key</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=general#nbdesigner_unsplash_api_key'))),
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Enable images from Pexels', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_pexels',
                        'description'   => sprintf(__( 'Require <a href="%s" target="_blank" >Pexels API Key</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=general#nbdesigner_pexels_api_key'))),
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Enable images from Freepik', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_freepik',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Enable SVG code', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_svg_code',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Enable capture images by webcam', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_image_webcam',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Enable Facebook photos', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_facebook_photo',
                        'description'   => sprintf(__( 'Require <a href="%s" target="_blank" >Facebook App ID</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=general#nbdesigner_facebook_app_id'))),
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Enable Instagram photos', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_instagram_photo',
                        'description'   => sprintf(__( 'Require <a href="%s" target="_blank" >Instagram App ID</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=general#nbdesigner_instagram_app_id'))),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Enable Dropbox photos', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_dropbox_photo',
                        'description'   => sprintf(__( 'Require <a href="%s" target="_blank" >Dropbox App ID</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=general#nbdesigner_dropbox_app_id'))),
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__('Enable image filters on modern layout', 'web-to-print-online-designer'),
                        'description'   => sprintf(__( 'Require <a href="%s" target="_blank" >NBDesigner PDF cloud api</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=general#nbdesigner_enable_cloud2print_api'))),
                        'id'            => 'nbdesigner_modern_layout_image_filter',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Enable mask layer', 'web-to-print-online-designer' ),
                        'id'            => 'nbdesigner_mask_layer',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Alert if the mask layer is on the outside', 'web-to-print-online-designer' ),
                        'id'            => 'nbdesigner_alert_mask_layer_outside',
                        'default'       => 'Layer not inside',
                        'type'          => 'textarea',
                    ),
                    array(
                        'title'         => esc_html__( 'Alert if the mask layer is invalid', 'web-to-print-online-designer' ),
                        'id'            => 'nbdesigner_alert_mask_layer_invalid',
                        'default'       => 'Layer can not mask',
                        'type'          => 'textarea',
                    ),
                    array(
                        'title'         => esc_html__( 'Enable generate thumbnail for uploaded photo', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_generate_photo_thumb',
                        'description'   => esc_html__('This option makes the photo upload process slower and consumes more system resources. But the design editor will be reduced process photo time.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__('Show terms and conditions', 'web-to-print-online-designer'),
                        'description'   => esc_html__('Show term and conditions upload image.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_upload_show_term',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Terms and conditions upload image', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_upload_term',
                        'default'       => 'Your term',
                        'type'          => 'textarea',
                        'description'   => esc_html__('HTML Tags Supported', 'web-to-print-online-designer'),
                        'css'           => 'width: 50em; height: 15em;'
                    ),
                    array(
                        'title'         => esc_html__( 'Show/hide features', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_option_image',
                        'default'       => json_encode(array(
                                'nbdesigner_image_unlock_proportion'    => 1,
                                'nbdesigner_image_shadow'               => 0,
                                'nbdesigner_image_opacity'              => 1,
                                'nbdesigner_image_grayscale'            => 1,
                                'nbdesigner_image_invert'               => 1,
                                'nbdesigner_image_sepia'                => 1,
                                'nbdesigner_image_sepia2'               => 1,
                                'nbdesigner_image_remove_white'         => 1,
                                'nbdesigner_image_transparency'         => 1,
                                'nbdesigner_image_tint'                 => 1,
                                'nbdesigner_image_blend'                => 1,
                                'nbdesigner_image_brightness'           => 1,
                                'nbdesigner_image_noise'                => 1,
                                'nbdesigner_image_pixelate'             => 1,
                                'nbdesigner_image_multiply'             => 1,
                                'nbdesigner_image_blur'                 => 1,
                                'nbdesigner_image_sharpen'              => 1,
                                'nbdesigner_image_emboss'               => 1,
                                'nbdesigner_image_edge_enhance'         => 1,
                                'nbdesigner_image_rotate'               => 1,
                                'nbdesigner_image_crop'                 => 1,
                                'nbdesigner_image_shapecrop'            => 1
                            )),
                        'description'   => esc_html__( 'Show/hide features in frontend', 'web-to-print-online-designer'),
                        'type'          => 'multicheckbox',
                        'layout'        => 'c',
                        'class'         => 'regular-text',
                        'options'       => array(
                            'nbdesigner_image_unlock_proportion'    => esc_html__( 'Unlock proportion', 'web-to-print-online-designer'),
                            'nbdesigner_image_shadow'               => esc_html__( 'Shadow', 'web-to-print-online-designer'),
                            'nbdesigner_image_opacity'              => esc_html__( 'Opacity', 'web-to-print-online-designer'),
                            'nbdesigner_image_grayscale'            => esc_html__( 'Grayscale', 'web-to-print-online-designer'),
                            'nbdesigner_image_invert'               => esc_html__( 'Invert', 'web-to-print-online-designer'),
                            'nbdesigner_image_sepia'                => esc_html__( 'Sepia', 'web-to-print-online-designer'),
                            'nbdesigner_image_sepia2'               => esc_html__( 'Sepia 2', 'web-to-print-online-designer'),
                            'nbdesigner_image_remove_white'         => esc_html__( 'Remove white', 'web-to-print-online-designer'),
                            'nbdesigner_image_transparency'         => esc_html__( 'Transparency', 'web-to-print-online-designer'),
                            'nbdesigner_image_tint'                 => esc_html__( 'Tint', 'web-to-print-online-designer'),
                            'nbdesigner_image_blend'                => esc_html__( 'Blend mode', 'web-to-print-online-designer'),
                            'nbdesigner_image_brightness'           => esc_html__( 'Brightness', 'web-to-print-online-designer'),
                            'nbdesigner_image_noise'                => esc_html__( 'Noise', 'web-to-print-online-designer'),
                            'nbdesigner_image_pixelate'             => esc_html__( 'Pixelate', 'web-to-print-online-designer'),
                            'nbdesigner_image_multiply'             => esc_html__( 'Multiply', 'web-to-print-online-designer'),
                            'nbdesigner_image_blur'                 => esc_html__( 'Blur', 'web-to-print-online-designer'),
                            'nbdesigner_image_sharpen'              => esc_html__( 'Sharpen', 'web-to-print-online-designer'),
                            'nbdesigner_image_emboss'               => esc_html__( 'Emboss', 'web-to-print-online-designer'),
                            'nbdesigner_image_edge_enhance'         => esc_html__( 'Edge enhance', 'web-to-print-online-designer'),
                            'nbdesigner_image_rotate'               => esc_html__( 'Rotate', 'web-to-print-online-designer'),
                            'nbdesigner_image_crop'                 => esc_html__( 'Crop image', 'web-to-print-online-designer'),
                            'nbdesigner_image_shapecrop'            => esc_html__( 'Shape crop', 'web-to-print-online-designer'),
                        ),
                        'css'           => 'margin: 0 15px 10px 5px;'
                    )
                ),
                'tool-draw' => array(
                    array(
                        'title'         => esc_html__( 'Enable Free Draw', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_draw',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Show/hide features', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_option_clipart',
                        'default'       => json_encode(array(
                                'nbdesigner_draw_brush' => 1,
                                   'nbdesigner_draw_brush_pencil'   => 1,
                                   'nbdesigner_draw_brush_circle'   => 1,
                                   'nbdesigner_draw_brush_spray'    => 1,
                                   'nbdesigner_draw_brush_pattern'  => 0,
                                   'nbdesigner_draw_brush_hline'    => 0,
                                   'nbdesigner_draw_brush_vline'    => 0,
                                   'nbdesigner_draw_brush_square'   => 0,
                                   'nbdesigner_draw_brush_diamond'  => 0,
                                   'nbdesigner_draw_brush_texture'  => 0,
                               'nbdesigner_draw_shape' => 1,
                                   'nbdesigner_draw_shape_rectangle' => 1,
                                   'nbdesigner_draw_shape_circle'   => 1,
                                   'nbdesigner_draw_shape_triangle' => 1,
                                   'nbdesigner_draw_shape_line'     => 1,
                                   'nbdesigner_draw_shape_polygon'  => 1,
                                   'nbdesigner_draw_shape_hexagon'  => 1
                            )),
                        'description'   => esc_html__( 'Show/hide features in frontend', 'web-to-print-online-designer'),
                        'type'          => 'multicheckbox',
                        'class'         => 'regular-text',
                        'depend'        =>  array(
                            'nbdesigner_draw_brush' => 'nbdesigner_draw_brush',
                                'nbdesigner_draw_brush_pencil'      => 'nbdesigner_draw_brush',
                                'nbdesigner_draw_brush_circle'      => 'nbdesigner_draw_brush',
                                'nbdesigner_draw_brush_spray'       => 'nbdesigner_draw_brush',
                                'nbdesigner_draw_brush_pattern'     => 'nbdesigner_draw_brush',
                                'nbdesigner_draw_brush_hline'       => 'nbdesigner_draw_brush',
                                'nbdesigner_draw_brush_vline'       => 'nbdesigner_draw_brush',
                                'nbdesigner_draw_brush_square'      => 'nbdesigner_draw_brush',
                                'nbdesigner_draw_brush_diamond'     => 'nbdesigner_draw_brush',
                                'nbdesigner_draw_brush_texture'     => 'nbdesigner_draw_brush',
                            'nbdesigner_draw_shape' => 'nbdesigner_draw_shape',
                                'nbdesigner_draw_shape_rectangle'   => 'nbdesigner_draw_shape',
                                'nbdesigner_draw_shape_circle'      => 'nbdesigner_draw_shape',
                                'nbdesigner_draw_shape_triangle'    => 'nbdesigner_draw_shape',
                                'nbdesigner_draw_shape_line'        => 'nbdesigner_draw_shape',
                                'nbdesigner_draw_shape_polygon'     => 'nbdesigner_draw_shape',
                                'nbdesigner_draw_shape_hexagon'     => 'nbdesigner_draw_shape'
                        ),
                        'options'   => array(
                            'nbdesigner_draw_brush' => esc_html__('Brush', 'web-to-print-online-designer'),
                                'nbdesigner_draw_brush_pencil'      => esc_html__('Pencil', 'web-to-print-online-designer'),
                                'nbdesigner_draw_brush_circle'      => esc_html__('Circle', 'web-to-print-online-designer'),
                                'nbdesigner_draw_brush_spray'       => esc_html__('Spray', 'web-to-print-online-designer'),
                                'nbdesigner_draw_brush_pattern'     => esc_html__('Pattern', 'web-to-print-online-designer'),
                                'nbdesigner_draw_brush_hline'       => esc_html__('Hline', 'web-to-print-online-designer'),
                                'nbdesigner_draw_brush_vline'       => esc_html__('Vline', 'web-to-print-online-designer'),
                                'nbdesigner_draw_brush_square'      => esc_html__('Square', 'web-to-print-online-designer'),
                                'nbdesigner_draw_brush_diamond'     => esc_html__('Diamond', 'web-to-print-online-designer'),
                                'nbdesigner_draw_brush_texture'     => esc_html__('Texture', 'web-to-print-online-designer'),
                            'nbdesigner_draw_shape' => esc_html__('Geometrical shape', 'web-to-print-online-designer'),
                                'nbdesigner_draw_shape_rectangle'   => esc_html__('Rectangle', 'web-to-print-online-designer'),
                                'nbdesigner_draw_shape_circle'      => esc_html__('Circle', 'web-to-print-online-designer'),
                                'nbdesigner_draw_shape_triangle'    => esc_html__('Triangle', 'web-to-print-online-designer'),
                                'nbdesigner_draw_shape_line'        => esc_html__('Line', 'web-to-print-online-designer'),
                                'nbdesigner_draw_shape_polygon'     => esc_html__('Polygon', 'web-to-print-online-designer'),
                                'nbdesigner_draw_shape_hexagon'     => esc_html__('Hexagon', 'web-to-print-online-designer')
                        ),
                        'css'           => 'margin: 0 15px 10px 5px;'
                    )
                ),
                'tool-qrcode' => array(
                    array(
                        'title'         => esc_html__( 'Enable QRCode', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_qrcode',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Default text', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_default_qrcode',
                        'default'       => 'example.com',
                        'description'   => esc_html__( 'Default text for QRCode', 'web-to-print-online-designer'),
                        'type'          => 'text',
                        'class'         => 'regular-text',
                    )
                ),
                'misc' => array(
                    array(
                        'title'         => esc_html__( 'Enable scale object from center', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_object_center_scaling',
                        'description'   => esc_html__('Change the default object scale origin from corner to center.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Always show layer corner actions', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_always_show_layer_action',
                        'description'   => esc_html__('Show layer corner actions even they are outside stage.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Disable auto load primary tempalte.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_disable_auto_load_template',
                        'description'   => esc_html__('Always show blank design pages and only load templates until the customer choose them.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Lazy load default template', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_lazy_load_template',
                        'description'   => esc_html__('Lazy load default template if the product has template and the default templates is a very large objects.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Auto fill template photo placeholders', 'web-to-print-online-designer' ),
                        'id'            => 'nbdesigner_auto_fill_template_masks',
                        'description'   => esc_html__( 'Selected image will be auto fill template photo placeholder.', 'web-to-print-online-designer' ),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Limit number of photos by number of template photo placeholders', 'web-to-print-online-designer' ),
                        'id'            => 'nbdesigner_limit_photo_by_masks',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Prevent delete template layers', 'web-to-print-online-designer' ),
                        'id'            => 'nbdesigner_prevent_delete_template_layer',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Prevent add more layer', 'web-to-print-online-designer' ),
                        'id'            => 'nbdesigner_prevent_add_more_layer',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Enable new loading method to reduce time to load template', 'web-to-print-online-designer' ),
                        'id'            => 'nbdesigner_boosting_load_template',
                        'description'   => esc_html__( 'This feature is open beta for testing, leave your feedback in our support ticket.', 'web-to-print-online-designer' ),
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