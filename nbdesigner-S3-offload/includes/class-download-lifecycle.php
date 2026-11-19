<?php
/**
 * Download Folder Lifecycle Manager for NBDesigner S3 Offload
 *
 * Purges aged on-demand export ZIP archives inside `wp-content/uploads/nbdesigner/download/`
 * past the configured retention threshold (`download_retention_days`).
 *
 * @package NBD_S3_Offload
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NBD_S3_Download_Lifecycle {

    /**
     * Initialize Action Scheduler / cron hooks.
     */
    public static function init(): void {
        add_action( 'nbd_s3_cron_download_cleanup', array( __CLASS__, 'run_scheduled_download_cleanup' ) );
        add_action( 'nbd_s3_chain_download_sweep', array( __CLASS__, 'run_download_sweep' ), 10, 1 );
    }

    /**
     * Resolve `wp-content/uploads/nbdesigner/download` normalized path.
     */
    public static function get_download_base_dir(): string {
        if ( defined( 'NBDESIGNER_DOWNLOAD_DIR' ) ) {
            return rtrim( str_replace( '\\', '/', NBDESIGNER_DOWNLOAD_DIR ), '/' );
        }
        return NBD_S3_Tier_Lifecycle::get_nbd_base_dir() . '/download';
    }

    /**
     * Daily scheduled entry point.
     */
    public static function run_scheduled_download_cleanup(): void {
        $settings = NBD_S3_Settings::get_all();
        if ( ! empty( $settings['sync_paused'] ) || empty( $settings['enable_download_cleanup'] ) ) {
            return;
        }
        self::run_download_sweep( false );
    }

    /**
     * Sweep `wp-content/uploads/nbdesigner/download/` in safe batches.
     * Deletes export files older than `download_retention_days`.
     */
    public static function run_download_sweep( bool $is_manual = false ): array {
        @set_time_limit( 120 );
        @ini_set( 'max_execution_time', '120' );

        $settings = NBD_S3_Settings::get_all();

        if ( $is_manual && get_transient( 'nbd_s3_download_sweep_cancelled' ) ) {
            delete_transient( 'nbd_s3_sync_status' );
            return array( 'success' => false, 'message' => __( 'Download sweep stopped by Pause.', 'nbd-s3-offload' ), 'purged_count' => 0 );
        }

        if ( ! $is_manual && ! empty( $settings['sync_paused'] ) ) {
            return array( 'success' => false, 'message' => __( 'Skipped: Sync is paused (Safe Mode active).', 'nbd-s3-offload' ), 'purged_count' => 0 );
        }

        if ( ! $is_manual && empty( $settings['enable_download_cleanup'] ) ) {
            return array( 'success' => false, 'message' => __( 'Download folder lifecycle cleanup is disabled.', 'nbd-s3-offload' ), 'purged_count' => 0 );
        }

        $download_dir = self::get_download_base_dir();
        if ( ! is_dir( $download_dir ) ) {
            delete_transient( 'nbd_s3_sync_status' );
            return array( 'success' => true, 'purged_count' => 0, 'message' => __( 'Download directory does not exist.', 'nbd-s3-offload' ) );
        }

        $real_download_dir = realpath( $download_dir );
        if ( false === $real_download_dir ) {
            delete_transient( 'nbd_s3_sync_status' );
            return array( 'success' => false, 'purged_count' => 0, 'message' => __( 'Failed to resolve download directory.', 'nbd-s3-offload' ) );
        }
        $real_download_norm = rtrim( str_replace( '\\', '/', $real_download_dir ), '/' );

        $retention_days = max( 1, (int) ( $settings['download_retention_days'] ?? 7 ) );
        $cutoff_ts      = time() - ( $retention_days * DAY_IN_SECONDS );

        set_transient( 'nbd_s3_sync_status', array(
            'running'    => true,
            'action'     => 'Download Folder Purge (Auto-Chaining Active)',
            'started_at' => time(),
        ), 300 );

        $max_deletes_per_batch = 1000;
        $batch_purged          = 0;
        $batch_kept            = 0;
        $batch_bytes           = 0;
        $has_more              = false;

        try {
            $it = new \DirectoryIterator( $real_download_dir );
            foreach ( $it as $item ) {
                if ( $item->isDot() || ! $item->isFile() ) {
                    continue;
                }
                if ( $is_manual && get_transient( 'nbd_s3_download_sweep_cancelled' ) ) {
                    delete_transient( 'nbd_s3_sync_status' );
                    return array( 'success' => false, 'message' => __( 'Download sweep stopped by Pause.', 'nbd-s3-offload' ), 'purged_count' => $batch_purged );
                }

                $name = $item->getFilename();
                if ( in_array( $name, array( '.htaccess', 'index.php', 'index.html' ), true ) ) {
                    continue;
                }

                if ( $item->getMTime() >= $cutoff_ts ) {
                    $batch_kept++;
                    continue;
                }

                $real_file = realpath( $item->getPathname() );
                if ( false === $real_file ) {
                    continue;
                }
                $real_file_norm = str_replace( '\\', '/', $real_file );
                if ( ! str_starts_with( $real_file_norm, $real_download_norm . '/' ) ) {
                    $batch_kept++;
                    continue;
                }

                if ( $batch_purged >= $max_deletes_per_batch ) {
                    $has_more = true;
                    break;
                }

                $size = (int) $item->getSize();
                if ( @unlink( $real_file ) ) {
                    $batch_purged++;
                    $batch_bytes += $size;
                }
            }
        } catch ( \Throwable $e ) {
            delete_transient( 'nbd_s3_sync_status' );
            NBD_S3_Logger::log( 'DOWNLOAD_PURGE', 'Download sweep error: ' . $e->getMessage(), array(), 'error' );
            return array( 'success' => false, 'purged_count' => $batch_purged, 'message' => $e->getMessage() );
        }

        if ( $has_more && function_exists( 'as_schedule_single_action' ) ) {
            if ( ! NBD_S3_Tier_Lifecycle::has_action_in_queue( 'nbd_s3_chain_download_sweep', array( 'pending' ) ) ) {
                as_schedule_single_action( time() + 10, 'nbd_s3_chain_download_sweep', array( $is_manual ) );
                if ( function_exists( 'spawn_cron' ) ) {
                    spawn_cron();
                }
            }

            NBD_S3_Logger::log(
                'DOWNLOAD_PURGE',
                sprintf( 'Download sweep batch: purged %d aged files (%s freed). Continuing...', $batch_purged, size_format( $batch_bytes ) ),
                array( 'purged' => $batch_purged, 'bytes_freed' => $batch_bytes ),
                'info'
            );

            return array(
                'success'      => true,
                'purged_count' => $batch_purged,
                'kept_count'   => $batch_kept,
                'bytes_freed'  => $batch_bytes,
                'chaining'     => true,
            );
        }

        delete_transient( 'nbd_s3_sync_status' );

        NBD_S3_Logger::log(
            'DOWNLOAD_PURGE',
            sprintf(
                'Download folder sweep complete! Purged %d aged files (%s freed), kept %d recent files (< %dd).',
                $batch_purged,
                size_format( $batch_bytes ),
                $batch_kept,
                $retention_days
            ),
            array(
                'purged'         => $batch_purged,
                'kept'           => $batch_kept,
                'bytes_freed'    => $batch_bytes,
                'retention_days' => $retention_days,
            ),
            'success'
        );

        return array(
            'success'      => true,
            'purged_count' => $batch_purged,
            'kept_count'   => $batch_kept,
            'bytes_freed'  => $batch_bytes,
            'chaining'     => false,
            'message'      => sprintf( __( 'Download folder sweep complete! Purged %d aged files (%s freed).', 'nbd-s3-offload' ), $batch_purged, size_format( $batch_bytes ) ),
        );
    }

    /**
     * Get live stats for `nbdesigner/download/` (total & aged counts/sizes).
     */
    public static function get_stats(): array {
        $settings       = NBD_S3_Settings::get_all();
        $retention_days = max( 1, (int) ( $settings['download_retention_days'] ?? 7 ) );
        $cutoff_ts      = time() - ( $retention_days * DAY_IN_SECONDS );
        $download_dir   = self::get_download_base_dir();

        $total_files = 0;
        $total_bytes = 0;
        $aged_files  = 0;
        $aged_bytes  = 0;

        if ( is_dir( $download_dir ) ) {
            try {
                $it = new \DirectoryIterator( $download_dir );
                foreach ( $it as $item ) {
                    if ( $item->isDot() || ! $item->isFile() ) {
                        continue;
                    }
                    $name = $item->getFilename();
                    if ( in_array( $name, array( '.htaccess', 'index.php', 'index.html' ), true ) ) {
                        continue;
                    }
                    $size = (int) $item->getSize();
                    $total_files++;
                    $total_bytes += $size;

                    if ( $item->getMTime() < $cutoff_ts ) {
                        $aged_files++;
                        $aged_bytes += $size;
                    }
                }
            } catch ( \Throwable $e ) {
                // Ignore directory read errors in stats endpoint
            }
        }

        return array(
            'total_files'          => $total_files,
            'total_bytes'          => $total_bytes,
            'total_size_formatted' => size_format( $total_bytes ),
            'aged_files'           => $aged_files,
            'aged_bytes'           => $aged_bytes,
            'aged_size_formatted'  => size_format( $aged_bytes ),
            'retention_days'       => $retention_days,
        );
    }
}
