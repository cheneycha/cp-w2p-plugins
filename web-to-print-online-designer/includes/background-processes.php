<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function nbd_init_background_processes(){
    global $nbd_processors;

    if ( ! class_exists( 'WP_Async_Request', false ) ) {
        include_once dirname( WC_PLUGIN_FILE ) . '/includes/libraries/wp-async-request.php';
    }

    if ( ! class_exists( 'WP_Background_Process', false ) ) {
        include_once dirname( WC_PLUGIN_FILE ) . '/includes/libraries/wp-background-process.php';
    }

    require_once(NBDESIGNER_PLUGIN_DIR . 'includes/background-processes/export-pdf.php');
    $nbd_processors['export_pdf'] = new NBD_Export_PDF_Process();

    do_action('nbd_background_processes');
}
add_action( 'woocommerce_loaded', 'nbd_init_background_processes' );