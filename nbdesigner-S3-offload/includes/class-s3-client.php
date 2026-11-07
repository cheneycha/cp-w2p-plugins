<?php
/**
 * Standalone AWS Signature Version 4 (SigV4) S3 Client for PHP 8.3
 * Compatible with Wasabi, Backblaze B2, Bunny Storage, DigitalOcean Spaces, AWS S3, and MinIO.
 * Zero external Composer dependencies to avoid version conflicts in WordPress.
 *
 * @package NBD_S3_Offload
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class NBD_S3_Client {

    private string $access_key;
    private string $secret_key;
    private string $region;
    private string $bucket;
    private string $endpoint;
    private bool $use_path_style;

    /**
     * Constructor
     */
    public function __construct( array $config = array() ) {
        $this->access_key     = (string) ( $config['access_key'] ?? '' );
        $this->secret_key     = (string) ( $config['secret_key'] ?? '' );
        $this->region         = (string) ( $config['region'] ?? 'us-east-1' );
        $this->bucket         = (string) ( $config['bucket'] ?? '' );
        $this->endpoint       = (string) ( $config['endpoint'] ?? 's3.amazonaws.com' );
        $this->use_path_style = (bool) ( $config['use_path_style'] ?? true );

        // Normalize endpoint (strip http:// or https:// and trailing slashes)
        $this->endpoint = preg_replace( '#^https?://#i', '', rtrim( $this->endpoint, '/' ) );
    }

    /**
     * Test connection by writing, reading, and deleting a probe test object.
     */
    public function test_connection(): array {
        if ( empty( $this->access_key ) || empty( $this->secret_key ) || empty( $this->bucket ) ) {
            return array(
                'success' => false,
                'message' => __( 'Missing required S3 credentials or bucket name.', 'nbd-s3-offload' ),
            );
        }

        $probe_key  = '.nbd-s3-probe-' . gmdate( 'YmdHis' ) . '.txt';
        $probe_data = 'NBDesigner S3 Offload Probe Connection OK: ' . gmdate( 'c' );

        // 1. Put Object
        $put_res = $this->put_object( $probe_key, $probe_data, 'text/plain' );
        if ( is_wp_error( $put_res ) ) {
            return array(
                'success' => false,
                'message' => sprintf( __( 'Upload probe failed: %s', 'nbd-s3-offload' ), $put_res->get_error_message() ),
            );
        }

        // 2. Head Object
        $head_res = $this->head_object( $probe_key );
        if ( is_wp_error( $head_res ) ) {
            // Try cleaning up probe anyway
            $this->delete_object( $probe_key );
            return array(
                'success' => false,
                'message' => sprintf( __( 'Read probe failed: %s', 'nbd-s3-offload' ), $head_res->get_error_message() ),
            );
        }

        // 3. Delete Object
        $del_res = $this->delete_object( $probe_key );
        if ( is_wp_error( $del_res ) ) {
            return array(
                'success' => false,
                'message' => sprintf( __( 'Delete probe failed: %s', 'nbd-s3-offload' ), $del_res->get_error_message() ),
            );
        }

        return array(
            'success' => true,
            'message' => __( 'Connection verified successfully! Bucket read/write/delete permissions confirmed.', 'nbd-s3-offload' ),
        );
    }

    /**
     * Put an object (upload file or string).
     */
    public function put_object( string $key, string $data_or_file_path, string $content_type = '', array $custom_headers = array(), bool $is_file = false ): array|\WP_Error {
        $key = ltrim( $key, '/' );

        if ( $is_file ) {
            if ( ! file_exists( $data_or_file_path ) || ! is_readable( $data_or_file_path ) ) {
                return new \WP_Error( 'file_not_found', sprintf( __( 'Local file not found or not readable: %s', 'nbd-s3-offload' ), $data_or_file_path ) );
            }
            $payload = file_get_contents( $data_or_file_path );
            if ( empty( $content_type ) ) {
                $content_type = $this->detect_mime_type( $data_or_file_path );
            }
        } else {
            $payload = $data_or_file_path;
            if ( empty( $content_type ) ) {
                $content_type = 'application/octet-stream';
            }
        }

        $headers = array_merge(
            array(
                'Content-Type' => $content_type,
            ),
            $custom_headers
        );

        return $this->send_request( 'PUT', $key, array(), $headers, $payload );
    }

    /**
     * Head object to check existence and metadata.
     */
    public function head_object( string $key ): array|\WP_Error {
        $key = ltrim( $key, '/' );
        return $this->send_request( 'HEAD', $key );
    }

    /**
     * Get object content or stream to local file.
     */
    public function get_object( string $key, ?string $save_to_file = null ): string|bool|\WP_Error {
        $key = ltrim( $key, '/' );

        $response = $this->send_request( 'GET', $key );
        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = $response['body'] ?? '';

        if ( ! empty( $save_to_file ) ) {
            $dir = dirname( $save_to_file );
            if ( ! is_dir( $dir ) ) {
                wp_mkdir_p( $dir );
            }
            $written = file_put_contents( $save_to_file, $body );
            if ( false === $written ) {
                return new \WP_Error( 'write_failed', sprintf( __( 'Failed to save downloaded S3 object to %s', 'nbd-s3-offload' ), $save_to_file ) );
            }
            return true;
        }

        return $body;
    }

    /**
     * Delete an object.
     */
    public function delete_object( string $key ): array|\WP_Error {
        $key = ltrim( $key, '/' );
        return $this->send_request( 'DELETE', $key );
    }

    /**
     * List objects with optional prefix.
     */
    public function list_objects( string $prefix = '', int $max_keys = 1000 ): array|\WP_Error {
        $query = array(
            'list-type' => '2',
            'max-keys'  => (string) $max_keys,
        );
        if ( ! empty( $prefix ) ) {
            $query['prefix'] = $prefix;
        }

        $response = $this->send_request( 'GET', '', $query );
        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = $response['body'] ?? '';
        $xml  = @simplexml_load_string( $body );
        if ( false === $xml ) {
            return array();
        }

        $items = array();
        if ( isset( $xml->Contents ) ) {
            foreach ( $xml->Contents as $content ) {
                $items[] = array(
                    'key'           => (string) $content->Key,
                    'size'          => (int) $content->Size,
                    'etag'          => trim( (string) $content->ETag, '"' ),
                    'last_modified' => (string) $content->LastModified,
                );
            }
        }

        return $items;
    }

    /**
     * Execute HTTP request signed with SigV4.
     */
    private function send_request( string $method, string $key = '', array $query = array(), array $headers = array(), ?string $payload = '' ): array|\WP_Error {
        $datetime = gmdate( 'Ymd\THis\Z' );
        $date     = gmdate( 'Ymd' );

        // Host determination
        if ( $this->use_path_style || empty( $this->bucket ) ) {
            $host = $this->endpoint;
            $uri  = '/' . $this->bucket . ( ! empty( $key ) ? '/' . $key : '' );
        } else {
            $host = $this->bucket . '.' . $this->endpoint;
            $uri  = '/' . $key;
        }

        // Canonical URI must be URI encoded, retaining slashes
        $canonical_uri = '/' . implode( '/', array_map( 'rawurlencode', explode( '/', ltrim( $uri, '/' ) ) ) );
        if ( empty( $canonical_uri ) || '/' === $canonical_uri ) {
            $canonical_uri = '/';
        }

        // Canonical query
        ksort( $query );
        $canonical_query_parts = array();
        foreach ( $query as $k => $v ) {
            $canonical_query_parts[] = rawurlencode( (string) $k ) . '=' . rawurlencode( (string) $v );
        }
        $canonical_query = implode( '&', $canonical_query_parts );

        // Payload hash
        $payload_hash = hash( 'sha256', $payload ?? '' );

        // Headers
        $headers['Host']                 = $host;
        $headers['x-amz-date']            = $datetime;
        $headers['x-amz-content-sha256'] = $payload_hash;

        // Canonical headers
        $canonical_headers_arr = array();
        foreach ( $headers as $k => $v ) {
            $canonical_headers_arr[ strtolower( trim( (string) $k ) ) ] = trim( (string) $v );
        }
        ksort( $canonical_headers_arr );

        $canonical_headers = '';
        foreach ( $canonical_headers_arr as $k => $v ) {
            $canonical_headers .= $k . ':' . $v . "\n";
        }
        $signed_headers = implode( ';', array_keys( $canonical_headers_arr ) );

        // Canonical Request
        $canonical_request = implode( "\n", array(
            strtoupper( $method ),
            $canonical_uri,
            $canonical_query,
            $canonical_headers,
            $signed_headers,
            $payload_hash,
        ) );

        // Scope and String to Sign
        $scope          = "{$date}/{$this->region}/s3/aws4_request";
        $string_to_sign = implode( "\n", array(
            'AWS4-HMAC-SHA256',
            $datetime,
            $scope,
            hash( 'sha256', $canonical_request ),
        ) );

        // Signing Key derivation
        $k_date    = hash_hmac( 'sha256', $date, 'AWS4' . $this->secret_key, true );
        $k_region  = hash_hmac( 'sha256', $this->region, $k_date, true );
        $k_service = hash_hmac( 'sha256', 's3', $k_region, true );
        $k_signing = hash_hmac( 'sha256', 'aws4_request', $k_service, true );
        $signature = hash_hmac( 'sha256', $string_to_sign, $k_signing );

        $headers['Authorization'] = "AWS4-HMAC-SHA256 Credential={$this->access_key}/{$scope}, SignedHeaders={$signed_headers}, Signature={$signature}";

        // Request URL
        $url = 'https://' . $host . $canonical_uri;
        if ( ! empty( $canonical_query ) ) {
            $url .= '?' . $canonical_query;
        }

        // Dispatch via wp_remote_request
        $response = wp_remote_request( $url, array(
            'method'  => strtoupper( $method ),
            'headers' => $headers,
            'body'    => $payload,
            'timeout' => 45,
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $code = wp_remote_retrieve_response_code( $response );
        $body = wp_remote_retrieve_body( $response );
        $resp_headers = wp_remote_retrieve_headers( $response );

        if ( $code < 200 || $code >= 300 ) {
            $err_msg = "S3 Error HTTP {$code}";
            if ( ! empty( $body ) ) {
                $xml = @simplexml_load_string( $body );
                if ( $xml && isset( $xml->Message ) ) {
                    $err_msg .= ': ' . (string) $xml->Message;
                } else {
                    $err_msg .= ': ' . substr( strip_tags( $body ), 0, 200 );
                }
            }
            return new \WP_Error( "s3_error_{$code}", $err_msg );
        }

        return array(
            'code'    => $code,
            'headers' => $resp_headers,
            'body'    => $body,
        );
    }

    /**
     * MIME type helper
     */
    private function detect_mime_type( string $file_path ): string {
        $ext = strtolower( pathinfo( $file_path, PATHINFO_EXTENSION ) );
        return match ( $ext ) {
            'pdf'          => 'application/pdf',
            'png'          => 'image/png',
            'jpg', 'jpeg'  => 'image/jpeg',
            'svg'          => 'image/svg+xml',
            'json'         => 'application/json',
            'ttf'          => 'font/ttf',
            'otf'          => 'font/otf',
            'woff'         => 'font/woff',
            'woff2'        => 'font/woff2',
            'eot'          => 'application/vnd.ms-fontobject',
            'zip'          => 'application/zip',
            default        => 'application/octet-stream',
        };
    }
}

