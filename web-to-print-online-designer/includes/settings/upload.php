<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( !class_exists('Nbdesigner_Settings_Upload') ) {
    class Nbdesigner_Settings_Upload {
        public static function get_options() {
            return apply_filters('nbdesigner_upload_settings', array(
                'upload-settings' => array(
                    array(
                        'title'         => esc_html__('Login Required', 'web-to-print-online-designer'),
                        'description'   => esc_html__('Users must create an account in your Wordpress site and need to be logged-in to upload files.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_upload_file_php_logged_in',
                        'default'       => 'no',
                        'type'          => 'checkbox'
                    ),
                    array(
                        'title'         => esc_html__( 'Allowed file types', 'web-to-print-online-designer'),
                        'description'   => __( 'Extensions seperated by a comma. Don not use dots or spaces. Example: <code>jpg,bmp,pdf,ps,ai,iddd</code>... Set empty input to allow all extensions except disallowed extensions.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_allow_upload_file_type',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text',
                        'placeholder'   => 'jpg,bmp,pdf,ps'
                    ),
                    array(
                        'title'         => esc_html__( 'Disallowed file types', 'web-to-print-online-designer'),
                        'description'   => __( 'Extensions seperated by a comma. Don not use dots or spaces. Example: <code>png,gif,... </code>', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_disallow_upload_file_type',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text',
                        'placeholder'   => 'png,gif'
                    ),
                    array(
                        'title'         => esc_html__('Max files uploads', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_number_file_upload',
                        'css'           => 'width: 65px',
                        'default'       => '1',
                        'description'   => esc_html__( 'Number of files allow user upload.', 'web-to-print-online-designer'),
                        'type'          => 'number'
                    ),
                    array(
                        'title'         => esc_html__('Min files uploads', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_min_file_upload',
                        'css'           => 'width: 65px',
                        'default'       => '0',
                        'description'   => esc_html__( 'Minimum number of file uploads.', 'web-to-print-online-designer'),
                        'type'          => 'number'
                    ),
                    array(
                        'title'         => esc_html__( 'Max upload size', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_maxsize_upload_file',
                        'css'           => 'width: 65px',
                        'default'       => nbd_get_max_upload_default(),
                        'subfix'        => ' MB',
                        'type'          => 'number'
                    ), 
                    array(
                        'title'         => esc_html__( 'Min upload size', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_minsize_upload_file',
                        'css'           => 'width: 65px',
                        'default'       => '0',
                        'subfix'        => ' MB',
                        'type'          => 'number'
                    ), 
                    array(
                        'title'         => esc_html__('Create Preview for images', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_create_preview_image_file_upload',
                        'default'       => 'no',
                        'description'   => esc_html__( 'Be careful, it may be slow down your server if uploaded images are very large.', 'web-to-print-online-designer'),
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__('Preview image width', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_file_upload_preview_width',
                        'css'           => 'width: 65px',
                        'default'       => '200',
                        'subfix'        => ' px',
                        'description'   => esc_html__( 'Preview image width in pixel.', 'web-to-print-online-designer'),
                        'type'          => 'number'
                    ),
                    array(
                        'title'         => esc_html__( 'Min. resolution DPI for JPG/JPEG image', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_mindpi_upload_file',
                        'css'           => 'width: 65px',
                        'default'       => '0',
                        'type'          => 'number'
                    ),
                    array(
                        'title'         => esc_html__('Upload file mode', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_upload_popup_style',
                        'description'   => esc_html__('Choose style for upload poopup.', 'web-to-print-online-designer'),
                        'default'       => 's',
                        'type'          => 'radio',
                        'options'       => array(
                            's' => esc_html__('Simple', 'web-to-print-online-designer'),
                            'a' => esc_html__('Advanced ( apply for photo frame, wallpaper )', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__('Enable upload file via facebook', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_facebook_file_upload',
                        'default'       => 'yes',
                        'description'   => sprintf(__( 'Setting Facebook App ID <a target="_blank" href="%s">here</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=general#nbdesigner_facebook_app_id'))),
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__('Enable upload file via Instagram', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_instagram_file_upload',
                        'default'       => 'yes',
                        'description'   => sprintf(__( 'Setting Instagram App ID <a target="_blank" href="%s">here</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=general#nbdesigner_instagram_app_id'))),
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__('Enable upload file via Google Drive', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_drive_file_upload',
                        'default'       => 'yes',
                        'description'   => sprintf(__( 'Setting Google API key and Client ID <a target="_blank" href="%s">here</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=general#nbdesigner_google_client_id'))),
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__('Enable upload file via Dropbox', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_dropbox_file_upload',
                        'default'       => 'yes',
                        'description'   => sprintf(__( 'Setting Dropbox App ID <a target="_blank" href="%s">here</a>', 'web-to-print-online-designer'), esc_url(admin_url('admin.php?page=nbdesigner&tab=general#nbdesigner_dropbox_app_id'))),
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        ) 
                    ),
                    array(
                        'title'         => esc_html__( 'Delete upload files after X days', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_long_time_retain_upload_fies',
                        'description'   => esc_html__('Choose how long to retain upload files. Leave the above input blank to retain upload file indefinitely.', 'web-to-print-online-designer'),
                        'css'           => 'width: 65px',
                        'default'       => '',
                        'type'          => 'number'
                    )
                ),
                'images-settings' => array(
                    array(
                        'title'         => esc_html__( 'Max. resolution (px)', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_max_res_upload_file',
                        'description'   => esc_html__( 'Set empty input to allow any resolution', 'web-to-print-online-designer'),
                        'css'           => 'width: 65px',
                        'default'       => '',
                        'type'          => 'multivalues',
                        'options'       => array(
                            'width'     => 0,
                            'height'    => 0
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Min. resolution (px)', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_min_res_upload_file',
                        'css'           => 'width: 65px',
                        'default'       => '',
                        'type'          => 'multivalues',
                        'options'       => array(
                            'width'     => 0,
                            'height'    => 0
                        )
                    )
                )
            ));
        }
    }
}