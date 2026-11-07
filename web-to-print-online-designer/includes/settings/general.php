<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( !class_exists('Nbdesigner_Settings_General') ) {
    class Nbdesigner_Settings_General {
        public static function get_options() {
            return apply_filters('nbdesigner_general_settings', array(
                'general-settings' => array(
                    array(
                        'title'         => esc_html__( 'Default design editor layout', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_design_layout',
                        'description'   => __('Choose default layout for design tool, you can choose specify layout for each product.<br /><b>Note</b>: We recommend <b>Modern</b> layout for the best user experience. We have stopped developing new features on Classic layout, consider this if you still want to use it.', 'web-to-print-online-designer'),
                        'default'       => 'm',
                        'type'          => 'radio',
                        'options'       => array(
                            'm' => __('<b>Modern</b> - recommended', 'web-to-print-online-designer'),
                            'v' => esc_html__('Visual', 'web-to-print-online-designer'),
                            'c' => esc_html__('Classic - deprecated', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Default output resolution - DPI', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_default_dpi',
                        'css'           => 'width: 65px',
                        'default'       => '150',
                        'type'          => 'number'
                    ),
                    array(
                        'title'         => esc_html__( 'Default frontend download resolution - DPI', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_default_dpi_fe',
                        'css'           => 'width: 65px',
                        'default'       => '100',
                        'type'          => 'number'
                    ),
                    array(
                        'title'         => esc_html__( 'Dimensions Unit', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_dimensions_unit',
                        'description'   => esc_html__('This controls what unit you will define lengths in.', 'web-to-print-online-designer'),
                        'default'       => 'cm',
                        'type'          => 'radio',
                        'options'       => array(
                            'cm' => esc_html__('cm', 'web-to-print-online-designer'),
                            'in' => esc_html__('in', 'web-to-print-online-designer'),
                            'mm' => esc_html__('mm', 'web-to-print-online-designer'),
                            'ft' => esc_html__('ft', 'web-to-print-online-designer'),
                            'px' => esc_html__('px', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Preview design size', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_thumbnail_width',
                        'description'   => esc_html__('This is size of preview design image which will show in cart or order page.', 'web-to-print-online-designer'),
                        'css'           => 'width: 65px',
                        'default'       => '300',
                        'subfix'        => ' px',
                        'type'          => 'number'
                    ),  
                    array(
                        'title'         => esc_html__( 'Preview template size', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_template_width',
                        'description'   => esc_html__('This is size of preview template image which will show in gallery page.', 'web-to-print-online-designer'),
                        'css'           => 'width: 65px',
                        'default'       => '300',
                        'subfix'        => ' px',
                        'type'          => 'number'
                    ),
                    array(
                        'title'         => esc_html__('Hide design editor on smartphones', 'web-to-print-online-designer'),
                        'description'   => esc_html__('Hide design editor on smartphones and display an information instead.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_disable_on_smartphones',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ),
                    array(
                        'title'         => esc_html__('Allow save design for later', 'web-to-print-online-designer'),
                        'description'   => esc_html__('Allow the customer save their design and continue working on it another time.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_save_for_later',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ),
                    array(
                        'title'         => esc_html__('Allow share design', 'web-to-print-online-designer'),
                        'description'   => esc_html__('Allow the customer share their design via email or social network.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_share_design',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ),
                    array(
                        'title'         => esc_html__('Cache latest design on user browser', 'web-to-print-online-designer'),
                        'description'   => esc_html__('Save customer latest design. When they come back design product, they latest design will be loaded.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_save_latest_design',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ),
                    array(
                        'title'         => esc_html__('Cache customer uploaded image', 'web-to-print-online-designer'),
                        'description'   => esc_html__('Cache customer uploaded image links on browser.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_cache_uploaded_image',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ),
                    array(
                        'title'         => esc_html__('Allow customer re-design or re-upload design after order', 'web-to-print-online-designer'),
                        'description'   => esc_html__('After order, customer can edit they design before it is approved or rejected.', 'web-to-print-online-designer'),
                        'id'            => 'allow_customer_redesign_after_order',
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ),
                    array(
                        'title'         => esc_html__('Allow download design after checkout', 'web-to-print-online-designer'),
                        'description'   => esc_html__('Allow the customer download their designs or upload files after checkout.', 'web-to-print-online-designer'),
                        'id'            => 'allow_customer_download_after_complete_order',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'       => esc_html__('Yes - all order status', 'web-to-print-online-designer'),
                            'complete'  => esc_html__('Complete order', 'web-to-print-online-designer'),
                            'no'        => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Design file type download', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_option_download_type',
                        'default'       => json_encode(array(
                                'nbdesigner_download_design_png'            => 0,
                                'nbdesigner_download_design_pdf'            => 0,
                                'nbdesigner_download_design_svg'            => 0,
                                'nbdesigner_download_design_jpg_cmyk'       => 0,
                                'nbdesigner_download_design_upload_file'    => 0
                            )),
                        'description'   => esc_html__( 'Choose design file type which the customer can download. Design in JPG format require PHP Imagick. ', 'web-to-print-online-designer'),
                        'type'          => 'multicheckbox',
                        'class'         => 'regular-text',
                        'options'       => array(
                            'nbdesigner_download_design_png'            => esc_html__('PNG', 'web-to-print-online-designer'),
                            'nbdesigner_download_design_pdf'            => sprintf(__( 'PDF, detail config for <a target="_blank" href="%s">PDF</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=output'))),
                            'nbdesigner_download_design_svg'            => esc_html__('SVG', 'web-to-print-online-designer'),
                            'nbdesigner_download_design_jpg_cmyk'       => esc_html__('JPG ( require PHP Imagick )', 'web-to-print-online-designer'),
                            'nbdesigner_download_design_upload_file'    => esc_html__('Upload files - The customer upload files', 'web-to-print-online-designer')
                        ),
                        'css' => 'margin: 0 15px 10px 5px;'
                    ),
                    array(
                        'title'         => esc_html__('Allow download design in editor', 'web-to-print-online-designer'),
                        'description'   => esc_html__('Allow the customer download their designs in editor ( Modern layout ).', 'web-to-print-online-designer'),
                        'id'            => 'allow_customer_download_design_in_editor',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Design file type download', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_option_download_type2',
                        'default'       => json_encode(array(
                                'nbdesigner_download_design_in_editor_png' => 0,
                                'nbdesigner_download_design_in_editor_pdf' => 0,
                                'nbdesigner_download_design_in_editor_svg' => 0,
                                'nbdesigner_download_design_in_editor_jpg' => 0
                            )),
                        'description'   => esc_html__( 'Choose design file type which the customer can download. Design in JPG format require PHP Imagick. ', 'web-to-print-online-designer'),
                        'type'          => 'multicheckbox',
                        'class'         => 'regular-text',
                        'options'       => array(
                            'nbdesigner_download_design_in_editor_png' => esc_html__('PNG', 'web-to-print-online-designer'),
                            'nbdesigner_download_design_in_editor_svg' => esc_html__('SVG', 'web-to-print-online-designer'),
                            'nbdesigner_download_design_in_editor_pdf' => sprintf(__( 'PDF, detail config for <a target="_blank" href="%s">PDF</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=output'))),
                            'nbdesigner_download_design_in_editor_jpg' => esc_html__('JPG', 'web-to-print-online-designer')
                        ),
                        'css'           => 'margin: 0 15px 10px 5px;'
                    )
                ),
                'admin-notifications' => array(
                    array(
                        'title'         => esc_html__( 'Admin notifications', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_notifications',
                        'description'   => esc_html__('Send a message to the admin when customer design saved / changed.', 'web-to-print-online-designer'),
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Recurrence', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_notifications_recurrence',
                        'description'   => esc_html__('Choose how many times you want to receive an e-mail.', 'web-to-print-online-designer'),
                        'default'       => 'hourly',
                        'type'          => 'select',
                        'options'       => array(
                            'hourly'        => esc_html__('Hourly', 'web-to-print-online-designer'),
                            'twicedaily'    => esc_html__('Twice a day', 'web-to-print-online-designer'),
                            'daily'         => esc_html__('Daily', 'web-to-print-online-designer')
                        )
                    ),   
                    array(
                        'title'         => esc_html__( 'Recipients', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Enter recipients (comma separated) for this email. Defaults to ', 'web-to-print-online-designer').'<code>'.get_option('admin_email').'</code>',
                        'id'            => 'nbdesigner_notifications_emails',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text',
                        'placeholder'   => 'Enter your email'
                    ),
                    array(
                        'title'         => esc_html__( 'Send mail to admins when approve designs', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_send_mail_when_approve',
                        'description'   => esc_html__('Send mail to admins when approve the customer designs.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Admin emails who receive notification when designs approved', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Enter recipients (comma separated) for admin email. Defaults to ', 'web-to-print-online-designer').'<code>'.get_option('admin_email').'</code>',
                        'id'            => 'nbdesigner_admin_emails',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text',
                        'placeholder'   => 'Enter admin emails'
                    ),
                    array(
                        'title'         => esc_html__( 'Attach custom designs', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Attach custom designs in Admin notifications email.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_attachment_admin_email',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )  
                    ),
                    array(
                        'title'         => esc_html__( 'Attach custom designs type', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_option_attach_type',
                        'default'       => json_encode(array(
                                'nbdesigner_attach_design_png' => 0,
                                'nbdesigner_attach_design_svg' => 0,
                            )),
                        'description'   => esc_html__( 'Choose design file type which attach in email.', 'web-to-print-online-designer'),
                        'type'          => 'multicheckbox',
                        'class'         => 'regular-text',
                        'options'       => array(
                            'nbdesigner_attach_design_png' => esc_html__('PNG', 'web-to-print-online-designer'),
                            'nbdesigner_attach_design_svg' => esc_html__('SVG', 'web-to-print-online-designer')
                        ),
                        'css'           => 'margin: 0 15px 10px 5px;'
                    )
                ),
                'nbd-pages'       => array(
                    array(
                        'title'         => esc_html__( 'Create your own page', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Choose Create your own page.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_create_your_own_page_id',
                        'type'          => 'select',
                        'default'       => nbd_get_page_id( 'create_your_own' ),
                        'options'       => nbd_get_pages()
                    ),
                    array(
                        'title'         => esc_html__( 'Designer page', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Choose designer page.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_designer_page_id',
                        'type'          => 'select',
                        'default'       => nbd_get_page_id( 'designer' ),
                        'options'       => nbd_get_pages()
                    ),    
                    array(
                        'title'         => esc_html__( 'Gallery', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Choose Gallery page.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_gallery_page_id',
                        'type'          => 'select',
                        'default'       => nbd_get_page_id( 'gallery' ),
                        'options'       => nbd_get_pages()
                    ),
                    array(
                        'title'         => esc_html__( 'Redirect login', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Choose login page on design tool.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_logged_page_id',
                        'type'          => 'select',
                        'default'       => nbd_get_page_id( 'logged' ),
                        'options'       => nbd_get_pages()
                    ),
                    array(
                        'title'         => esc_html__( 'Product builder page', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Choose Product builder page.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_product_builder_page_id',
                        'type'          => 'select',
                        'default'       => nbd_get_page_id( 'product_builder' ),
                        'options'       => nbd_get_pages()
                    )
                ),
                'application'       => array(
                    array(
                        'title'         => esc_html__( 'Facebook App ID', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Enter a Facebook App-ID to allow customer use Facebook photos.', 'web-to-print-online-designer').' <a href="#" id="nbdesigner_show_helper">'.__("Where do I get this info?", 'web-to-print-online-designer').'</a>',
                        'id'            => 'nbdesigner_facebook_app_id',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text'
                    ), 
                    array(
                        'title'         => esc_html__( 'Instagram App ID', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Enter a Instagram App-ID to allow customer use Instagram photos.', 'web-to-print-online-designer') . '<br /> <b>Redirect URI: '.NBDESIGNER_PLUGIN_URL.'includes/auth-instagram.php</b>',
                        'id'            => 'nbdesigner_instagram_app_id',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text'
                    ),
                    array(
                        'title'         => esc_html__( 'Instagram App Secret Key', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Enter a Instagram App Secret Key to allow customer use Instagram photos.', 'web-to-print-online-designer') . '<br /> <b>Redirect URI: '.NBDESIGNER_PLUGIN_URL.'includes/auth-instagram.php</b>',
                        'id'            => 'nbdesigner_instagram_app_secret',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text'
                    ),
                    array(
                        'title'         => esc_html__( 'Dropbox Chooser App ID', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Enter a Dropbox App-ID to allow customer use Dropbox photos.', 'web-to-print-online-designer'). '<br /><a href="https://www.dropbox.com/developers/apps/create" target="_blank" >'. esc_html__('Create a new app','web-to-print-online-designer') .'</a><br />'.__('Edit app and set "Chooser/Saver domains" with your domain: <b><code>'.$_SERVER['SERVER_NAME'].'</code></b>','web-to-print-online-designer'),
                        'id'            => 'nbdesigner_dropbox_app_id',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text'
                    ),
                    array(
                        'title'         => esc_html__( 'Google API key', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'The Browser API key obtained from the Google API Console.', 'web-to-print-online-designer').' <a href="#" id="nbdesigner_google_drive_helper">' . esc_html__("Where do I get this info?", 'web-to-print-online-designer').'</a>',
                        'id'            => 'nbdesigner_google_api_key',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text'
                    ),
                    array(
                        'title'         => esc_html__( 'Google Client ID', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'The Client ID obtained from the Google API Console.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_google_client_id',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text'
                    ),
                    array(
                        'title'         => esc_html__( 'Pixabay API key', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'You can use default key or your Pixabay API key. That feature allow the customer serach and use images from Pixabay.', 'web-to-print-online-designer').' <a href="https://pixabay.com/vi/service/about/api/" target="_blank">' . esc_html__("Where do I get this info?", 'web-to-print-online-designer').'</a>',
                        'id'            => 'nbdesigner_pixabay_api_key',
                        'class'         => 'regular-text',
                        'default'       => '27347-23fd1708b1c4f768195a5093b',
                        'type'          => 'text'
                    ),      
                    array(
                        'title'         => esc_html__( 'Unsplash API key', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'You can use default key or your Unsplash API key. That feature allow the customer serach and use images from Pixabay.', 'web-to-print-online-designer').' <a href="https://unsplash.com/developers" target="_blank">' . esc_html__("Where do I get this info?", 'web-to-print-online-designer').'</a>',
                        'id'            => 'nbdesigner_unsplash_api_key',
                        'class'         => 'regular-text',
                        'default'       => '5746b12f75e91c251bddf6f83bd2ad0d658122676e9bd2444e110951f9a04af8',
                        'type'          => 'text'
                    ),
                    array(
                        'title'         => esc_html__( 'Pexels API key', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'You can use default key or your Pexels API key. That feature allow the customer serach and use images from Pexels.', 'web-to-print-online-designer').' <a href="https://www.pexels.com/api/" target="_blank">' . esc_html__("Where do I get the Pexels API Key?", 'web-to-print-online-designer').'</a>',
                        'id'            => 'nbdesigner_pexels_api_key',
                        'class'         => 'regular-text',
                        'default'       => '563492ad6f9170000100000147b95f140fe441b858072ac5940c9ba0',
                        'type'          => 'text'
                    ),
                    array(
                        'title'         => esc_html__( 'Flaticon API key', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'This feature allow the customer search and use icons from Flaticon.', 'web-to-print-online-designer').' <a href="https://developer.flaticon.com/" target="_blank">' . esc_html__("Where do I get the Flaticon API Key?", 'web-to-print-online-designer').'</a>',
                        'id'            => 'nbdesigner_flaticon_api_key',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text'
                    ),
                    array(
                        'title'         => esc_html__( 'Google Maps Static API key', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'This feature allow the customer search and insert Google maps into the design.', 'web-to-print-online-designer').' <a href="https://developers.google.com/maps/documentation/maps-static/intro" target="_blank">' . esc_html__("Where do I get the Google Maps Static API key?", 'web-to-print-online-designer').'</a>',
                        'id'            => 'nbdesigner_static_map_api_key',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text'
                    )
                ),
                'customization' => array(
                    
                ),
                'tools' => array(
                    array(
                        'title'         => esc_html__('Force to upload SVG files', 'web-to-print-online-designer'),
                        'description'   => esc_html__('Some themes or plugins disable upload SVG files feature. This option force to upload SVG files.', 'web-to-print-online-designer'),
                        'id'            => 'nbd_force_upload_svg',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ), 
                    array(
                        'title'         => esc_html__( 'Enable log mode', 'web-to-print-online-designer'),
                        'description'   => sprintf(__( 'Enable log mode for debug. <a href="%s">Logs</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner_tools#nbd-logs'))),
                        'id'            => 'nbdesigner_enable_log',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Fix image URL after change domain', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Fix image urls in templates or order designs after change domain or setup SSL.'),
                        'id'            => 'nbdesigner_fix_domain_changed',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ),    
                    array(
                        'title'         => esc_html__( 'Fix lost images when export design into PDF', 'web-to-print-online-designer'),
                        'description'   => '',
                        'id'            => 'nbdesigner_fix_lost_pdf_image',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Enable NBDesigner cloud api to create PDF', 'web-to-print-online-designer'),
                        'description'   => '',
                        'id'            => 'nbdesigner_enable_cloud2print_api',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Enable NBDesigner cloud api to export design into JPEG and PNG format', 'web-to-print-online-designer'),
                        'description'   => '',
                        'id'            => 'nbdesigner_enable_pdf2img_cloud2print_api',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Cron job flush W3 Total cache', 'web-to-print-online-designer'),
                        'description'   => '',
                        'id'            => 'nbdesigner_cron_job_clear_w3_cache',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Site force login', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_site_force_login',
                        'description'   => esc_html__('Require user log in first.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Redefine K_PATH_FONTS constant', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_redefine_K_PATH_FONTS',
                        'description'   => esc_html__('Redefine K_PATH_FONTS constant if your server does not provide the write permission in the plugins folder.', 'web-to-print-online-designer'),
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Disable NONCE in the submit form', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_disable_nonce',
                        'description'   => esc_html__('Disable NONCE in the submit form if your site has a problem with cache plugins.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Additional upload file types', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_guideline_mimes',
                        'description'   => sprintf(__( 'You can find out mime types of several common file extensions on <a href="%s">this page</a>. Enter extension without dot. Example: ai: application/pdf, psd: image/vnd.adobe.photoshop, eps: application/postscript, indd: application/x-indesign', 'web-to-print-online-designer'), esc_url('https://www.freeformatter.com/mime-types-list.html#mime-types-list')),
                        'default'       => self::serialize_empty_arr(),
                        'col1'          => esc_html__( 'Extension', 'web-to-print-online-designer'),
                        'col2'          => esc_html__( 'MIME Type', 'web-to-print-online-designer'),
                        'type'          => 'nbd-dynamic-list',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    )
                )
            ));
        }
        public static function serialize_empty_arr(){
            $a = array( 0 => array(), 1 => array());
            return serialize( $a );
        }
    }
}