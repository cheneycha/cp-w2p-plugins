<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( !class_exists('Nbdesigner_Settings_Output') ) {    
    class Nbdesigner_Settings_Output{
        public static function get_options() {
            return apply_filters('nbdesigner_output_settings', array(
                'output-settings' => array(
                    array(
                        'title'         => esc_html__( 'Watermark', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_pdf_watermark',
                        'description'   => esc_html__('Enable watermark if allow customer download PDFs', 'web-to-print-online-designer'),
                        'default'       => 'yes',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'       => esc_html__('Always', 'web-to-print-online-designer'),
                            'before'    => esc_html__('Before complete order', 'web-to-print-online-designer'),
                            'no'        => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Watermark type', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_pdf_watermark_type',
                        'default'       => '2',
                        'type'          => 'radio',
                        'options'       => array(
                            '1' => esc_html__('Image', 'web-to-print-online-designer'),
                            '2' => esc_html__('Text', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Watermark image', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_pdf_watermark_image',
                        'description'   => esc_html__('Choose a watermark image', 'web-to-print-online-designer'),
                        'default'       => '',
                        'type'          => 'nbd-media'
                    ),
                    array(
                        'title'         => esc_html__( 'Watermark text', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Branded watermark text', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_pdf_watermark_text',
                        'class'         => 'regular-text',
                        'default'   => get_bloginfo('name'),
                        'type'      => 'text'
                    ),
                    array(
                        'title'         => esc_html__( 'Enable PDF password for customer', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_pdf_password',
                        'description'   => esc_html__('Enable PDF protected password for customer when they download PDF file from editor.', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'PDF password', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'PDF password to edit file', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_pdf_password',
                        'class'         => 'regular-text',
                        'default'   => '',
                        'type'      => 'text'
                    ),
                    array(
                        'title'         => esc_html__( 'Show bleed', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'If the product include bleed line, show it below/above the content design.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_bleed_stack',
                        'default'       => '1',
                        'type'          => 'radio',
                        'options'       => array(
                            '1' => esc_html__('Below the content design.', 'web-to-print-online-designer'),
                            '2' => esc_html__('Above the content design.', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'Truetype fonts', 'web-to-print-online-designer'),
                        'description'   => esc_html__( 'Each font in a separate line', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_truetype_fonts',
                        'class'         => 'regular-text',
                        'placeholder'   => 'Abel&#x0a;Abril Fatface&#x0a;Aguafina Script',
                        'css'           => 'height: 10em;',
                        'default'       => '',
                        'type'          => 'textarea'
                    )
                ),
                'synchronize' => array(
                    array(
                        'title'         => esc_html__('Auto export design to PDF', 'web-to-print-online-designer'),
                        'description'   => esc_html__('Auto export design to PDF when order status changed.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_auto_export_pdf',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ),
                    array(
                        'title'         => esc_html__('Order status for auto export design', 'web-to-print-online-designer'),
                        'description'   => '',
                        'id'            => 'nbdesigner_order_status_auto_export',
                        'default'       => 'completed',
                        'type'          => 'radio',
                        'options'       => array(
                            'completed'     => esc_html__('Completed', 'web-to-print-online-designer'),
                            'processing'    => esc_html__('Processing', 'web-to-print-online-designer'),
                            'on-hold'       => esc_html__('On Hold', 'web-to-print-online-designer'),
                        )
                    ),
                    array(
                        'title'         => esc_html__('Automatic synchronize output file to', 'web-to-print-online-designer'),
                        'description'   => esc_html__('Automatic synchronize output file to another place after auto export design.', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_synchronize_to',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'no'        => esc_html__('No', 'web-to-print-online-designer'),
                            'ftp'       => esc_html__('FTP', 'web-to-print-online-designer'),
                            'sftp'      => esc_html__('SFTP', 'web-to-print-online-designer'),
                            'dropbox'   => esc_html__('Dropbox', 'web-to-print-online-designer'),
                            'awss3'     => esc_html__('Amazon S3', 'web-to-print-online-designer'),
                            'gcs'       => esc_html__('Google Cloud Storage', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'FTP host', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_ftp_host',
                        'description'   => '',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'FTP Username', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_ftp_username',
                        'description'   => '',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'FTP Password', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_ftp_password',
                        'description'   => '',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'FTP remote path', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_ftp_remote_path',
                        'description'   => '',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text',
                        'placeholder'   => '/public_html/orders'
                    ),
                    array(
                        'title'         => esc_html__( 'FTP passive mode', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_ftp_passive_mode',
                        'description'   => '<span class="nbd-check-connection-wrap"><a class="button button-secondary nbd-check-connection" data-place="ftp">' . esc_html__( 'Test connect', 'web-to-print-online-designer') . '</a> <span class="nbd-con-checking ftp-checking">' . esc_html__( 'checking...', 'web-to-print-online-designer') . '</span></span>',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => esc_html__('Yes', 'web-to-print-online-designer'),
                            'no'    => esc_html__('No', 'web-to-print-online-designer'),
                        )
                    ),
                    array(
                        'title'         => esc_html__( 'SFTP host', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_sftp_host',
                        'description'   => '',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'SFTP port', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_sftp_port',
                        'description'   => '',
                        'default'       => '22',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'SFTP Username', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_sftp_username',
                        'description'   => '',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'SFTP Password', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_sftp_password',
                        'description'   => '',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'SFTP Key', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_sftp_key',
                        'description'   => esc_html__( 'Private key: .pem', 'web-to-print-online-designer'),
                        'default'       => '',
                        'type'          => 'textarea',
                        'css'           => 'width: 50em; height: 15em;',
                        'placeholder'   => '-----BEGIN RSA PRIVATE KEY-----'
                    ),
                    array(
                        'title'         => esc_html__( 'SFTP directory path', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_sftp_remote_path',
                        'description'   => '<span class="nbd-check-connection-wrap"><a class="button button-secondary nbd-check-connection" data-place="sftp">' . esc_html__( 'Test connect', 'web-to-print-online-designer') . '</a> <span class="nbd-con-checking sftp-checking">' . esc_html__( 'checking...', 'web-to-print-online-designer') . '</span></span>',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text',
                        'placeholder'   => '/public_html/orders'
                    ),
                    array(
                        'title'         => esc_html__( 'Dropbox token', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_dropbox_token',
                        'description'   => '',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'Dropbox directory path', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_dropbox_directory_path',
                        'description'   => '<span class="nbd-check-connection-wrap"><a class="button button-secondary nbd-check-connection" data-place="dropbox">' . esc_html__( 'Test connect', 'web-to-print-online-designer') . '</a> <span class="nbd-con-checking dropbox-checking">' . esc_html__( 'checking...', 'web-to-print-online-designer') . '</span></span>',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text',
                        'placeholder'   => 'orders'
                    ),
                    array(
                        'title'         => esc_html__( 'Amazon S3 Credentials Key', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_awss3_credentials_key',
                        'description'   => '',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'Amazon S3 Credentials Secret', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_awss3_credentials_secret',
                        'description'   => '',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'Amazon S3 bucket', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_awss3_bucket',
                        'description'   => '',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'Amazon S3 region', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_awss3_region',
                        'description'   => '',
                        'default'       => 'us-east-1',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'Amazon S3 directory path', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_awss3_directory_path',
                        'description'   => '<span class="nbd-check-connection-wrap"><a class="button button-secondary nbd-check-connection" data-place="awss3">' . esc_html__( 'Test connect', 'web-to-print-online-designer') . '</a> <span class="nbd-con-checking awss3-checking">' . esc_html__( 'checking...', 'web-to-print-online-designer') . '</span></span>',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text',
                        'placeholder'   => 'orders'
                    ),
                    array(
                        'title'         => esc_html__( 'Google Cloud Storage Project ID', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_gcs_project_id',
                        'description'   => '',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'Google Cloud Storage Bucket', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_gcs_bucket',
                        'description'   => '',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text'
                    ),
                    array(
                        'title'         => esc_html__( 'Google Cloud service account key', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_gcs_keyfile',
                        'description'   => sprintf(__( 'Make json api key from <a href="%s">here</a>', 'web-to-print-online-designer'), 'https://console.cloud.google.com/apis/credentials/serviceaccountkey'),
                        'default'       => '',
                        'type'          => 'textarea',
                        'css'           => 'width: 50em; height: 15em;',
                        'placeholder'   => '{"type": "service_account,"&#x0a;"project_id": "project_id",&#x0a;"private_key_id":"private_key_id",&#x0a;"private_key": "-----BEGIN PRIVATE KEY...'
                    ),
                    array(
                        'title'         => esc_html__( 'Google Cloud Storage directory path', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_gcs_directory_path',
                        'description'   => '<span class="nbd-check-connection-wrap"><a class="button button-secondary nbd-check-connection" data-place="gcs">' . esc_html__( 'Test connect', 'web-to-print-online-designer') . '</a> <span class="nbd-con-checking gcs-checking">' . esc_html__( 'checking...', 'web-to-print-online-designer') . '</span></span>',
                        'default'       => '',
                        'type'          => 'text',
                        'class'         => 'regular-text',
                        'placeholder'   => 'orders'
                    )
                ),
                'jpeg-settings' => array(
                    array(
                        'title'         => esc_html__( 'Default ICC profile', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_default_icc_profile',
                        'description'   => __('Set default ICC profile for jpg image. <br/><b>This feature require your server support Imagemagick with lcms2.</b>', 'web-to-print-online-designer'),
                        'type'          => 'select',
                        'default'       => 1,
                        'options'       => nbd_get_icc_cmyk_list()
                    )
                ),
                'svg-settings' => array(
                    array(
                        'title'         => esc_html__( 'Convert font to outlines', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_font_to_outlines',
                        'description'   => esc_html__('Convert text to path ( Beta )', 'web-to-print-online-designer'),
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