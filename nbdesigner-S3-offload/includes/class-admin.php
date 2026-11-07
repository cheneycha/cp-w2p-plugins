<?php
/**
 * Admin Screen & Asset Loader for NBDesigner S3 Offload
 *
 * @package NBD_S3_Offload
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NBD_S3_Admin {

    public const MENU_SLUG = 'nbd-s3-offload';

    /**
     * Initialize admin hooks.
     */
    public static function init(): void {
        add_action( 'admin_menu', array( __CLASS__, 'register_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_assets' ) );
        add_filter( 'plugin_action_links_' . NBD_S3_PLUGIN_BASENAME, array( __CLASS__, 'add_action_links' ) );
    }

    /**
     * Register admin submenu page under WooCommerce and Settings.
     */
    public static function register_admin_menu(): void {
        $parent = 'woocommerce';

        add_submenu_page(
            $parent,
            __( 'NBDesigner S3 Offload Settings', 'nbd-s3-offload' ),
            __( 'S3 Offload', 'nbd-s3-offload' ),
            'manage_woocommerce',
            self::MENU_SLUG,
            array( __CLASS__, 'render_admin_page' )
        );
    }

    /**
     * Add direct Settings link to Plugins screen.
     */
    public static function add_action_links( array $links ): array {
        $settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=' . self::MENU_SLUG ) ) . '">' . __( 'Settings', 'nbd-s3-offload' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    /**
     * Enqueue compiled React + Vite bundle on S3 Offload settings page.
     */
    public static function enqueue_assets( string $hook ): void {
        if ( ! str_contains( $hook, self::MENU_SLUG ) ) {
            return;
        }

        $dist_dir = NBD_S3_PLUGIN_DIR . 'admin/dist';
        $dist_url = NBD_S3_PLUGIN_URL . 'admin/dist';

        $js_file  = '';
        $css_file = '';

        if ( is_dir( $dist_dir . '/assets' ) ) {
            $files = glob( $dist_dir . '/assets/*.js' );
            if ( ! empty( $files ) ) {
                $js_file = $dist_url . '/assets/' . basename( $files[0] );
            }
            $css_files = glob( $dist_dir . '/assets/*.css' );
            if ( ! empty( $css_files ) ) {
                $css_file = $dist_url . '/assets/' . basename( $css_files[0] );
            }
        }

        if ( ! empty( $css_file ) ) {
            wp_enqueue_style( 'nbd-s3-admin-css', $css_file, array(), NBD_S3_VERSION );
        }

        if ( ! empty( $js_file ) ) {
            wp_enqueue_script( 'nbd-s3-admin-js', $js_file, array(), NBD_S3_VERSION, true );
        } else {
            // Fallback development/standalone inline script
            wp_enqueue_script( 'nbd-s3-admin-fallback', NBD_S3_PLUGIN_URL . 'assets/admin-fallback.js', array(), NBD_S3_VERSION, true );
        }

        wp_localize_script( ! empty( $js_file ) ? 'nbd-s3-admin-js' : 'nbd-s3-admin-fallback', 'nbdS3OffloadConfig', array(
            'restUrl'   => rest_url( NBD_S3_REST_API::NAMESPACE ),
            'nonce'     => wp_create_nonce( 'wp_rest' ),
            'pluginUrl' => NBD_S3_PLUGIN_URL,
            'strings'   => array(
                'saving'      => __( 'Saving...', 'nbd-s3-offload' ),
                'testing'     => __( 'Testing Connection...', 'nbd-s3-offload' ),
                'syncing'     => __( 'Syncing Fonts to S3...', 'nbd-s3-offload' ),
                'saveSuccess' => __( 'Settings saved successfully.', 'nbd-s3-offload' ),
            ),
        ) );
    }

    /**
     * Render container for React mount.
     */
    public static function render_admin_page(): void {
        ?>
        <div class="wrap" id="nbd-s3-admin-wrap">
            <div id="nbd-s3-offload-root">
                <div style="padding: 40px 20px; text-align: center; color: #64748b; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;">
                    <div style="font-size: 28px; font-weight: 600; color: #0f172a; margin-bottom: 8px;">NBDesigner S3 Offload</div>
                    <p style="font-size: 14px;"><?php esc_html_e( 'Loading dashboard...', 'nbd-s3-offload' ); ?></p>
                </div>
            </div>
        </div>
        <?php
    }
}

