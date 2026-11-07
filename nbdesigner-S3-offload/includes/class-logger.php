<?php
/**
 * Activity Logger for NBDesigner S3 Offload
 *
 * Automatically tracks all offload, restore, sync, and configuration events.
 * Implements a FIFO rolling buffer to strictly avoid unlimited disk bloat.
 *
 * @package NBD_S3_Offload
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NBD_S3_Logger {

    public const OPTION_KEY = 'nbd_s3_activity_logs';

    /**
     * Log an activity event.
     *
     * @param string $action  Action type (e.g. OFFLOAD, RESTORE, SWEEP, PAUSE, RESUME, CONNECT).
     * @param string $message Human-readable message.
     * @param array  $details Additional metadata (folder, file count, bytes, etc).
     * @param string $status  Status: success, error, warning, info.
     */
    public static function log( string $action, string $message, array $details = array(), string $status = 'info' ): void {
        $settings = NBD_S3_Settings::get_all();
        if ( empty( $settings['enable_activity_log'] ) ) {
            return;
        }

        $logs = get_option( self::OPTION_KEY, array() );
        if ( ! is_array( $logs ) ) {
            $logs = array();
        }

        $entry = array(
            'id'             => wp_generate_uuid4(),
            'timestamp'      => time(),
            'formatted_time' => wp_date( 'Y-m-d H:i:s' ),
            'action'         => strtoupper( sanitize_text_field( $action ) ),
            'status'         => in_array( $status, array( 'success', 'error', 'warning', 'info' ), true ) ? $status : 'info',
            'message'        => sanitize_text_field( $message ),
            'details'        => $details,
        );

        // Prepend new entry
        array_unshift( $logs, $entry );

        // Strict auto-pruning to guarantee no disk bloat
        $max_entries = max( 50, min( 500, (int) ( $settings['max_log_entries'] ?? 200 ) ) );
        if ( count( $logs ) > $max_entries ) {
            $logs = array_slice( $logs, 0, $max_entries );
        }

        update_option( self::OPTION_KEY, $logs, false );
    }

    /**
     * Retrieve activity logs.
     *
     * @param int    $limit  Maximum number of entries to return.
     * @param string $status Filter by status ('all', 'success', 'error', 'warning', 'info').
     * @return array
     */
    public static function get_logs( int $limit = 100, string $status = 'all' ): array {
        $logs = get_option( self::OPTION_KEY, array() );
        if ( ! is_array( $logs ) ) {
            return array();
        }

        if ( 'all' !== $status ) {
            $logs = array_filter( $logs, function( $item ) use ( $status ) {
                return ( $item['status'] ?? '' ) === $status;
            } );
        }

        return array_slice( array_values( $logs ), 0, $limit );
    }

    /**
     * Clear all activity logs.
     *
     * @return bool
     */
    public static function clear_logs(): bool {
        return update_option( self::OPTION_KEY, array(), false );
    }
}

