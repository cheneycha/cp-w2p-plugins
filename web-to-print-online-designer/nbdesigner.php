<?php
/**
 * @package Nbdesigner
 */
/*
Plugin Name: NBDesigner PHP 8.3
Plugin URI: https://cmsmart.net/wordpress-plugins/woocommerce-online-product-designer-plugin
Description: A Woocommerce printing ecosystem. Custom 05/09/2024
Version: 3.1.1
Author: NetbaseTeam
Author URI: https://cmsmart.net/
License: GPLv2 or later
Text Domain: web-to-print-online-designer
Domain Path: /langs
WC requires at least: 3.0.0
WC tested up to: 8.2.1
*/

if ( !function_exists( 'add_action' ) ) {
esc_html_e('Hi there!  I\'m just a plugin, not much I can do when called directly.', 'web-to-print-online-designer');
    exit;
}
$upload_dir = wp_upload_dir();
$basedir    = $upload_dir['basedir'];
$baseurl    = $upload_dir['baseurl'];
if( is_multisite() ){
    if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
        require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
    }
    if( in_array('wordpress-mu-domain-mapping/domain_mapping.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) )
        || is_plugin_active_for_network( 'wordpress-mu-domain-mapping/domain_mapping.php' ) ){ 
        $dm_domain      = $_SERVER[ 'HTTP_HOST' ];
        $baseurl_arr    = explode( 'wp-content', $baseurl );
        if( isset( $baseurl_arr[1] ) ){
            $protocol   = ( !empty($_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] != 'off' ) ? 'https://' : 'http://';
            $baseurl    = $protocol . $dm_domain . '/wp-content' . $baseurl_arr[1];
        }
    }
}
$nbd_plugin_dir_url = plugin_dir_url( __FILE__ );
if ( function_exists( 'icl_get_home_url' ) ) {
    if ( class_exists( 'SitePress' ) ) {
        global $sitepress;
        $wpml_language_negotiation_type = $sitepress->get_setting('language_negotiation_type');
        if( $wpml_language_negotiation_type == 2 ){
            $nbd_plugin_dir_url = str_replace(untrailingslashit(get_option('home')), untrailingslashit(icl_get_home_url()), $nbd_plugin_dir_url);
        }
    }
}
nbd_define( 'NBDESIGNER_VERSION',                '2.8.3' );
nbd_define( 'NBDESIGNER_NUMBER_VERSION',         283 );
nbd_define( 'NBDESIGNER_MINIMUM_WP_VERSION',     '4.1.1' );
nbd_define( 'NBDESIGNER_MINIMUM_PHP_VERSION',    '5.6.0' );
nbd_define( 'NBDESIGNER_MINIMUM_WC_VERSION',     '3.0.0' );
nbd_define( 'NBDESIGNER_PLUGIN_URL',             $nbd_plugin_dir_url );
nbd_define( 'NBDESIGNER_PLUGIN_DIR',             plugin_dir_path( __FILE__ ) );
nbd_define( 'NBDESIGNER_PLUGIN_BASENAME',        plugin_basename( __FILE__ ) );
nbd_define( 'NBDESIGNER_MODE_DEV',               FALSE );
nbd_define( 'NBDESIGNER_MODE_DEBUG',             FALSE );
nbd_define( 'NBDESIGNER_HIDDEN_LICENSE_KEY',     TRUE );
nbd_define( 'NBDESIGNER_DATA_DIR',               $basedir . '/nbdesigner' );
nbd_define( 'NBDESIGNER_DATA_URL',               $baseurl . '/nbdesigner' );
nbd_define( 'NBDESIGNER_FONT_DIR',               NBDESIGNER_DATA_DIR . '/fonts' );
nbd_define( 'NBDESIGNER_FONT_URL',               NBDESIGNER_DATA_URL . '/fonts' );
nbd_define( 'NBDESIGNER_ART_DIR',                NBDESIGNER_DATA_DIR . '/cliparts' );
nbd_define( 'NBDESIGNER_ART_URL',                NBDESIGNER_DATA_URL . '/cliparts' );
nbd_define( 'NBDESIGNER_DOWNLOAD_DIR',           NBDESIGNER_DATA_DIR . '/download' );
nbd_define( 'NBDESIGNER_DOWNLOAD_URL',           NBDESIGNER_DATA_URL . '/download' );
nbd_define( 'NBDESIGNER_TEMP_DIR',               NBDESIGNER_DATA_DIR . '/temp' );
nbd_define( 'NBDESIGNER_LOG_DIR',                NBDESIGNER_DATA_DIR . '/logs' );
nbd_define( 'NBDESIGNER_TEMP_URL',               NBDESIGNER_DATA_URL . '/temp' );
nbd_define( 'NBDESIGNER_ADMINDESIGN_DIR',        NBDESIGNER_DATA_DIR . '/admindesign' );
nbd_define( 'NBDESIGNER_ADMINDESIGN_URL',        NBDESIGNER_DATA_URL . '/admindesign' );
nbd_define( 'NBDESIGNER_PDF_DIR',                NBDESIGNER_DATA_DIR . '/pdfs' );
nbd_define( 'NBDESIGNER_PDF_URL',                NBDESIGNER_DATA_URL . '/pdfs' );
nbd_define( 'NBDESIGNER_CUSTOMER_DIR',           NBDESIGNER_DATA_DIR . '/designs' );
nbd_define( 'NBDESIGNER_CUSTOMER_URL',           NBDESIGNER_DATA_URL . '/designs' );
nbd_define( 'NBDESIGNER_UPLOAD_DIR',             NBDESIGNER_DATA_DIR . '/uploads' );
nbd_define( 'NBDESIGNER_UPLOAD_URL',             NBDESIGNER_DATA_URL . '/uploads' );
nbd_define( 'NBDESIGNER_SUGGEST_DESIGN_DIR',     NBDESIGNER_DATA_DIR . '/suggest_designs' );
nbd_define( 'NBDESIGNER_SUGGEST_DESIGN_URL',     NBDESIGNER_DATA_URL . '/suggest_designs' );
nbd_define( 'NBDESIGNER_DATA_CONFIG_DIR',        NBDESIGNER_DATA_DIR . '/data' );
nbd_define( 'NBDESIGNER_DATA_CONFIG_URL',        NBDESIGNER_DATA_URL . '/data' );
nbd_define( 'NBDESIGNER_ASSETS_URL',             NBDESIGNER_PLUGIN_URL . 'assets/' );
nbd_define( 'NBDESIGNER_JS_URL',                 NBDESIGNER_PLUGIN_URL . 'assets/js/' );
nbd_define( 'NBDESIGNER_CSS_URL',                NBDESIGNER_PLUGIN_URL . 'assets/css/' );
nbd_define( 'NBDESIGNER_TEMPLATES',              'nbdesigner_templates' );
nbd_define( 'NBDESIGNER_CATEGORY_TEMPLATES',     'nbdesigner_category_templates' );
nbd_define( 'NBDESIGNER_AUTHOR_SITE',            'https://cmsmart.net/' );
nbd_define( 'NBDESIGNER_SKU',                    'WPP1074' );
nbd_define( 'NBDESIGNER_PAGE_STUDIO',            'design-studio' );
nbd_define( 'NBDESIGNER_PAGE_CREATE_YOUR_OWN',   'create-your-own' );

//nbd_define('PCLZIP_TEMPORARY_DIR', NBDESIGNER_DATA_DIR);
function nbd_define( $name, $value ) {
    if ( ! defined( $name ) ) {
        define( $name, $value );
    }
}

require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-util.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-template-loader.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-settings.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-debug.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-helper.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-import-export-product.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-update-data.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class.category.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/table/class.product.templates.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-install.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class.nbdesigner.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class.my.design.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class.vista.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class.resource.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-compatibility.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class.product-builder.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class.request-quote.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class.template-tags.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class.template-mapping.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-updates.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-shortcodes.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class.appearance.customize.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-api.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class.artwork.actions.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class.design.guideline.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class.advanced.upload.php' );

require_once( NBDESIGNER_PLUGIN_DIR . 'includes/launcher/class.designer.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/launcher/util.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/launcher/class.withdraw.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/launcher/class.design.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/launcher/class.launcher.php' );

require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-live-chat.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-faq.php' );

require_once(NBDESIGNER_PLUGIN_DIR . 'includes/background-processes.php' );

if ( ! empty( $_GET['page'] ) ) {
    if ( !function_exists('wp_get_current_user') ) {
        include(ABSPATH . "wp-includes/pluggable.php");
    }
}
if( isset($_GET['page']) && $_GET['page'] == 'nbd-setup' && current_user_can('administrator')){
		require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-setup-wizard.php' );
}
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/options/admin-options.php' );
require_once( NBDESIGNER_PLUGIN_DIR . 'includes/options/frontend-options.php' );

register_activation_hook( __FILE__, array( 'Nbdesigner_Plugin', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'Nbdesigner_Plugin', 'plugin_deactivation' ) );
$prefix = is_network_admin() ? 'network_admin_' : '';
add_filter( $prefix.'plugin_action_links_' . NBDESIGNER_PLUGIN_BASENAME, array('Nbdesigner_Plugin', 'nbdesigner_add_action_links') );
add_filter( 'plugin_row_meta', array( 'Nbdesigner_Plugin', 'nbdesigner_plugin_row_meta' ), 10, 2 );
if ( ! function_exists( 'is_plugin_active_for_network' ) ) {
    require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

add_filter('site_transient_update_plugins', 'nb_remove_update_notification');
function nb_remove_update_notification($value) {
     unset($value->response[ plugin_basename(__FILE__) ]);
     return $value;
}

$nb_designer = new Nbdesigner_Plugin();
$nb_designer->init();

$nb_design_endpoint = new My_Design_Endpoint();
$nb_design_endpoint->init();

$nb_compatibility = new Nbdesigner_Compatibility();
$nb_compatibility->init();

require_once( NBDESIGNER_PLUGIN_DIR . 'includes/class-widget.php' );

/**
 * With the upgrade to WordPress 4.7.1, some non-image files fail to upload on certain server setups. 
 * This will be fixed in 4.7.3, see the Trac ticket: https://core.trac.wordpress.org/ticket/39550
 * 
 */
if ( version_compare( $GLOBALS['wp_version'], '4.7.2', '<=' ) || nbdesigner_get_option( 'nbd_force_upload_svg' ) == 'yes' ) {
    add_filter( 'wp_check_filetype_and_ext', 'wp39550_disable_real_mime_check', 10, 4 );
}
add_action( 'before_woocommerce_init', function() {

    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {

        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );

    }

} );
function wp39550_disable_real_mime_check( $data, $file, $filename, $mimes ) {
    $wp_filetype        = wp_check_filetype( $filename, $mimes );
    $ext                = $wp_filetype['ext'];
    $type               = $wp_filetype['type'];
    $proper_filename    = $data['proper_filename'];
    return compact( 'ext', 'type', 'proper_filename' );
}
if( nbdesigner_get_option( 'nbdesigner_redefine_K_PATH_FONTS', 'yes' ) == 'yes' ){
    nbd_define( 'K_PATH_FONTS', NBDESIGNER_DATA_DIR . '/php-fonts/' );
}
if( nbdesigner_get_option( 'nbdesigner_disable_nonce', 'no' ) == 'yes' ){
    nbd_define( 'NBDESIGNER_ENABLE_NONCE', FALSE );
}else{
    nbd_define( 'NBDESIGNER_ENABLE_NONCE', TRUE );
}
do_action( 'nbd_loaded' );