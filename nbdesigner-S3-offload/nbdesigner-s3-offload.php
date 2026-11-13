<?php
/**
 * Plugin Name: NBDesigner S3 Offload
 * Description: Offload NBDesigner design assets, print PDFs, and large fonts to S3-compatible object storage (Wasabi, Backblaze B2, Bunny, DigitalOcean, AWS) with CDN delivery and tiered lifecycle archiving.
 * Version: 0.2.13
 * Author: Cheney Chang
 * Text Domain: nbd-s3-offload
 * Domain Path: /languages
 * Requires at least: 6.2
 * Requires PHP: 8.3
 * WC requires at least: 8.0
 * WC tested up to: 11.2.1
 *
 * @package NBD_S3_Offload
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define Constants
define( 'NBD_S3_VERSION', '0.2.13' );
define( 'NBD_S3_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'NBD_S3_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'NBD_S3_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Main Plugin Bootstrap
 */
final class NBD_S3_Offload_Plugin {

    private static ?self $instance = null;

    public static function instance(): self {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Include required PHP classes.
     */
    private function load_dependencies(): void {
        require_once NBD_S3_PLUGIN_DIR . 'includes/class-s3-client.php';
        require_once NBD_S3_PLUGIN_DIR . 'includes/class-settings.php';
        require_once NBD_S3_PLUGIN_DIR . 'includes/class-logger.php';
        require_once NBD_S3_PLUGIN_DIR . 'includes/class-tier-lifecycle.php';
        require_once NBD_S3_PLUGIN_DIR . 'includes/class-rest-api.php';
        require_once NBD_S3_PLUGIN_DIR . 'includes/class-admin.php';
    }

    /**
     * Initialize hooks.
     */
    private function init_hooks(): void {
        // Load plugin textdomain for translations (Loco Translate compatible)
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

        // REST API routes
        add_action( 'rest_api_init', array( 'NBD_S3_REST_API', 'register_routes' ) );

        // Tiered lifecycle & hydration engine
        NBD_S3_Tier_Lifecycle::init();

        // Admin dashboard & menus
        if ( is_admin() ) {
            NBD_S3_Admin::init();
        }

        // Declare HPOS (High-Performance Order Storage) compatibility for WooCommerce
        add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );

        // Admin dependency checks
        add_action( 'admin_notices', array( $this, 'check_dependencies' ) );
    }

    /**
     * Load translation textdomain for Loco Translate / WordPress i18n.
     */
    public function load_textdomain(): void {
        load_plugin_textdomain( 'nbd-s3-offload', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     * Declare WooCommerce High-Performance Order Storage (HPOS) compatibility.
     */
    public function declare_hpos_compatibility(): void {
        if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
        }
    }

    /**
     * Display admin notices if required plugins or PHP version are missing.
     */
    public function check_dependencies(): void {
        if ( version_compare( PHP_VERSION, '8.3.0', '<' ) ) {
            ?>
            <div class="notice notice-error">
                <p><strong><?php esc_html_e( 'NBDesigner S3 Offload', 'nbd-s3-offload' ); ?></strong>: <?php printf( esc_html__( 'Requires PHP 8.3 or higher. Your server is running PHP %s.', 'nbd-s3-offload' ), esc_html( PHP_VERSION ) ); ?></p>
            </div>
            <?php
        }

        if ( ! class_exists( 'WooCommerce' ) ) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p><strong><?php esc_html_e( 'NBDesigner S3 Offload', 'nbd-s3-offload' ); ?></strong>: <?php esc_html_e( 'WooCommerce is not active. Some background order offload features may be disabled.', 'nbd-s3-offload' ); ?></p>
            </div>
            <?php
        }
    }
}

// Bootstrap plugin instance immediately so constants can be defined before dependent plugins
NBD_S3_Offload_Plugin::instance();

// Activation and Deactivation
register_activation_hook( __FILE__, function() {
    require_once NBD_S3_PLUGIN_DIR . 'includes/class-settings.php';

    // Ensure default settings exist
    if ( ! get_option( NBD_S3_Settings::OPTION_KEY ) ) {
        update_option( NBD_S3_Settings::OPTION_KEY, NBD_S3_Settings::get_defaults() );
    }
} );

register_deactivation_hook( __FILE__, function() {
    // Clear scheduled actions if any
    if ( function_exists( 'as_unschedule_all_actions' ) ) {
        as_unschedule_all_actions( 'nbd_s3_cron_cold_sweep' );
        as_unschedule_all_actions( 'nbd_s3_chain_cold_sweep' );
        as_unschedule_all_actions( 'nbd_s3_async_offload_item' );
        as_unschedule_all_actions( 'nbd_s3_cron_cleanup_drafts' );
        as_unschedule_all_actions( 'nbd_s3_chain_cleanup_drafts' );
        as_unschedule_all_actions( 'nbd_s3_cron_saved_designs_lifecycle' );
    }
} );

