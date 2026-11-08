<?php
/**
 * Settings Manager for NBDesigner S3 Offload
 *
 * @package NBD_S3_Offload
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NBD_S3_Settings {

    public const OPTION_KEY = 'nbd_s3_offload_settings';

    /**
     * Get default settings.
     */
    public static function get_defaults(): array {
        return array(
            'provider'                   => 'wasabi',
            'access_key'                 => '',
            'secret_key'                 => '',
            'region'                     => 'us-east-1',
            'bucket'                     => '',
            'endpoint'                   => 's3.wasabisys.com',
            'use_path_style'             => true,
            'cdn_url'                    => '',
            'enable_font_offload'        => true,
            'enable_cold_order_offload'  => true,
            'enable_template_offload'    => false, // Kept local by default for Tier 1 Whitelist
            'enable_saved_design_offload'=> false,
            'cold_retention_days'        => 14,
            'purge_mode'                 => 'smart', // 'smart' (deletes heavy PDFs/PNGs, keeps JSON) or 'full'
            'cleanup_abandoned_drafts'   => true,
            'abandoned_drafts_days'      => 7,
            'protect_active_carts'       => true, // Active Cart Protection Shield: immune to purge if in active cart
            'default_object_acl'         => 'public-read', // Object ACL: 'public-read' (DigitalOcean Spaces/CDN), 'private', or 'none' (AWS Bucket-Owner Enforced)
            'delete_remote_on_restore'   => true, // Delete remote S3 copy after verified local restore (frees S3 storage)
            'font_cache_max_age'         => 2592000, // 30 days
            // Customer Saved Designs Lifecycle Policy:
            'enable_saved_designs_lifecycle'   => false, // Master switch for Customer Saved Designs auto-purge
            'saved_designs_retention_days'     => 90, // Purge customer saved designs after X days (default: 90 days)
            'saved_designs_warning_days'       => 7,  // Display warning & send email Y days before purge (default: 7 days)
            'saved_designs_send_warning_email' => true, // Dispatch warning email to customer Y days prior
            'saved_designs_send_purge_email'   => true, // Dispatch notification email when design is purged
            // Safety & Sync Master Controls:
            'sync_paused'                => true, // Master Kill-Switch: Paused by default on activation
            'sync_schedule_time'         => '03:00', // Daily sync scheduled run time (24h HH:MM)
            'enable_activity_log'        => true,
            'max_log_entries'            => 200, // FIFO auto-pruned to prevent disk bloat
        );
    }

    /**
     * Provider presets for common S3 providers.
     */
    public static function get_provider_presets(): array {
        return array(
            'wasabi'       => array(
                'name'           => 'Wasabi Cloud Storage',
                'default_region' => 'us-east-1',
                'endpoint'       => 's3.wasabisys.com',
                'use_path_style' => true,
            ),
            'backblaze'    => array(
                'name'           => 'Backblaze B2 (S3 Compatible)',
                'default_region' => 'us-east-005',
                'endpoint'       => 's3.us-east-005.backblazeb2.com',
                'use_path_style' => true,
            ),
            'bunny'        => array(
                'name'           => 'Bunny Storage / BunnyCDN',
                'default_region' => 'de',
                'endpoint'       => 'storage.bunnycdn.com',
                'use_path_style' => true,
            ),
            'digitalocean' => array(
                'name'           => 'DigitalOcean Spaces',
                'default_region' => 'nyc3',
                'endpoint'       => 'nyc3.digitaloceanspaces.com',
                'use_path_style' => true,
            ),
            'aws'          => array(
                'name'           => 'Amazon Web Services S3',
                'default_region' => 'us-east-1',
                'endpoint'       => 's3.amazonaws.com',
                'use_path_style' => false,
            ),
            'custom'       => array(
                'name'           => 'Custom S3 Compatible Endpoint',
                'default_region' => 'us-east-1',
                'endpoint'       => '',
                'use_path_style' => true,
            ),
        );
    }

    /**
     * Retrieve all settings with defaults applied.
     */
    public static function get_all(): array {
        $saved = get_option( self::OPTION_KEY, array() );
        if ( ! is_array( $saved ) ) {
            $saved = array();
        }
        $merged = array_merge( self::get_defaults(), $saved );

        // Auto-heal: Ensure activity logging remains enabled by default
        if ( ! isset( $merged['enable_activity_log'] ) || false === $merged['enable_activity_log'] ) {
            $merged['enable_activity_log'] = true;
        }

        return $merged;
    }

    /**
     * Retrieve a specific setting value.
     */
    public static function get( string $key, mixed $default = null ): mixed {
        $settings = self::get_all();
        return $settings[ $key ] ?? $default;
    }

    /**
     * Save settings with sanitization.
     */
    public static function update( array $data ): bool {
        $current = self::get_all();

        $sanitized = array(
            'provider'                   => sanitize_text_field( $data['provider'] ?? $current['provider'] ),
            'access_key'                 => sanitize_text_field( $data['access_key'] ?? $current['access_key'] ),
            'region'                     => sanitize_text_field( $data['region'] ?? $current['region'] ),
            'bucket'                     => sanitize_text_field( $data['bucket'] ?? $current['bucket'] ),
            'endpoint'                   => sanitize_text_field( $data['endpoint'] ?? $current['endpoint'] ),
            'use_path_style'             => ! empty( $data['use_path_style'] ),
            'cdn_url'                    => esc_url_raw( rtrim( $data['cdn_url'] ?? $current['cdn_url'], '/' ) ),
            'enable_font_offload'        => ! empty( $data['enable_font_offload'] ),
            'enable_cold_order_offload'  => ! empty( $data['enable_cold_order_offload'] ),
            'enable_template_offload'    => ! empty( $data['enable_template_offload'] ),
            'enable_saved_design_offload'=> ! empty( $data['enable_saved_design_offload'] ),
            'cold_retention_days'        => max( 1, absint( $data['cold_retention_days'] ?? $current['cold_retention_days'] ) ),
            'purge_mode'                 => in_array( $data['purge_mode'] ?? '', array( 'smart', 'full' ), true ) ? $data['purge_mode'] : 'smart',
            'cleanup_abandoned_drafts'   => ! empty( $data['cleanup_abandoned_drafts'] ),
            'abandoned_drafts_days'      => max( 1, min( 180, absint( $data['abandoned_drafts_days'] ?? $current['abandoned_drafts_days'] ) ) ),
            'protect_active_carts'       => ! empty( $data['protect_active_carts'] ),
            'default_object_acl'         => in_array( $data['default_object_acl'] ?? '', array( 'public-read', 'private', 'none' ), true ) ? $data['default_object_acl'] : 'public-read',
            'delete_remote_on_restore'   => ! empty( $data['delete_remote_on_restore'] ),
            'font_cache_max_age'         => max( 3600, absint( $data['font_cache_max_age'] ?? $current['font_cache_max_age'] ) ),
            'enable_saved_designs_lifecycle'   => ! empty( $data['enable_saved_designs_lifecycle'] ),
            'saved_designs_retention_days'     => max( 1, min( 3650, absint( $data['saved_designs_retention_days'] ?? $current['saved_designs_retention_days'] ?? 90 ) ) ),
            'saved_designs_warning_days'       => max( 1, min( 60, absint( $data['saved_designs_warning_days'] ?? $current['saved_designs_warning_days'] ?? 7 ) ) ),
            'saved_designs_send_warning_email' => ! empty( $data['saved_designs_send_warning_email'] ),
            'saved_designs_send_purge_email'   => ! empty( $data['saved_designs_send_purge_email'] ),
            'sync_paused'                => isset( $data['sync_paused'] ) ? ! empty( $data['sync_paused'] ) : ( $current['sync_paused'] ?? true ),
            'sync_schedule_time'         => sanitize_text_field( $data['sync_schedule_time'] ?? $current['sync_schedule_time'] ?? '03:00' ),
            'enable_activity_log'        => isset( $data['enable_activity_log'] ) ? ! empty( $data['enable_activity_log'] ) : ( $current['enable_activity_log'] ?? true ),
            'max_log_entries'            => max( 50, min( 500, absint( $data['max_log_entries'] ?? $current['max_log_entries'] ?? 200 ) ) ),
        );

        // Keep existing secret key if incoming value is masked or empty
        if ( isset( $data['secret_key'] ) && ! empty( $data['secret_key'] ) && ! str_contains( $data['secret_key'], '••••' ) ) {
            $sanitized['secret_key'] = sanitize_text_field( $data['secret_key'] );
        } else {
            $sanitized['secret_key'] = $current['secret_key'];
        }

        return update_option( self::OPTION_KEY, $sanitized );
    }

    /**
     * Get settings sanitized for frontend display (masking secret key).
     */
    public static function get_for_client(): array {
        $settings = self::get_all();
        if ( ! empty( $settings['secret_key'] ) ) {
            $len = strlen( $settings['secret_key'] );
            $settings['secret_key'] = ( $len > 4 ) ? str_repeat( '•', $len - 4 ) . substr( $settings['secret_key'], -4 ) : '••••••••';
        }
        $settings['presets'] = self::get_provider_presets();
        return $settings;
    }
}

