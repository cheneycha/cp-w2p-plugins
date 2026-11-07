<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if( !class_exists('Nbdesigner_Request_quote') ) {
    class Nbdesigner_Request_quote{
        public static function get_options() {
            return apply_filters('nbdesigner_request_quote_settings', array(
                'general' => array(
                    array(
                        'title'         => __( 'Hide "Add to cart" button', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_quote_hide_add_to_cart',
                        'description'   => __('Hide "Add to cart" for all products which enable "Get quote"', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => __( 'Hide the price in product detail page', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_quote_hide_price',
                        'description'   => __('Hide the price for all products which enable "Get quote"', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => __( 'Hide the product price in the quote email', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_quote_hide_price_in_email',
                        'description'   => '',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => __( 'Allow "Request a quote" even if the product out of stock', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_quote_allow_out_of_stock',
                        'description'   => '',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => __( 'Show button request quote in checkout page', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_quote_checkout_button',
                        'description'   => '',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    )
                ),
                'request-form' => array(
                    array(
                        'title'         => __( 'Enable registration on the "Request a Quote" page', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_quote_enable_registration',
                        'description'   => '',
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => __( 'Enable reCAPTCHA in the default form', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_enable_recaptcha_quote',
                        'description'   => sprintf(__( 'To start using reCAPTCHA V2, you need to sign up for an <a target="_blank" href="%s"> API key pair for your site</a>', 'web-to-print-online-designer'), esc_url( 'https://www.google.com/recaptcha/admin' )),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => __( 'reCAPTCHA site key', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_recaptcha_key',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text'
                    ),
                    array(
                        'title'         => __( 'reCAPTCHA secret key', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_recaptcha_secret_key',
                        'class'         => 'regular-text',
                        'default'       => '',
                        'type'          => 'text'
                    ),
                    array(
                        'title'         => __( 'Autocomplete Form', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_quote_autocomplete_form',
                        'description'   => __('Check this option if you want that the fields connected to WooCommerce fields will be filled automatically', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    )
                ),
                'pdf-quote' => array(
                    array(
                        'title'         => __( 'Allow creating PDF document download in My Account page', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_quote_allow_download_pdf',
                        'description'   => __('A button "Download PDF" will be added in the quote detail page', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => __( 'Attach PDF quote to the email', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_quote_attach_pdf',
                        'description'   => __('The quote will be sent as PDF attachment', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => __( 'Remove the list with products from the email', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_quote_remove_list_in_email',
                        'description'   => __('Hide list product in the email if it has been sent as PDF attachment', 'web-to-print-online-designer'),
                        'default'       => 'no',
                        'type'          => 'radio',
                        'options'       => array(
                            'yes'   => __('Yes', 'web-to-print-online-designer'),
                            'no'    => __('No', 'web-to-print-online-designer')
                        )
                    ),
                    array(
                        'title'         => __( 'PDF Logo', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_quote_pdf_logo',
                        'description'   => __('Upload the logo you want to show in the PDF document. Only .png and .jpeg extensions are allowed', 'web-to-print-online-designer'),
                        'default'       => '',
                        'type'          => 'nbd-media'
                    ),
                    array(
                        'title'         => __( 'PDF note', 'web-to-print-online-designer'),
                        'id'            => 'nbdesigner_quote_pdf_note',
                        'type'          => 'textarea',
                        'description'   => '',
                        'default'       => '',
                        'css'           => 'width: 50em; height: 10em;'
                    )
                )
            ));
        }
    }
}