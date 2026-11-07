<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
class Nbdesigner_Download_Image{
    private $url = '';
    private $attachment_data = array();
    private $attachment_id = false;
    public function __construct( $url, $attachment_data = array() ) {
        $this->url = $this->format_url( $url );
        if ( is_array( $attachment_data ) && $attachment_data ) {
            $this->attachment_data = array_map( 'sanitize_text_field', $attachment_data );
        }
    }
    private function format_url( $url ){
        if ( $this->has_valid_scheme( $url ) ) {
            return $url;
        }
        if ( $this->does_string_start_with_substring( $url, '//' ) ) {
            return "http:{$url}";
        }
        return "http://{$url}";
    }
    private function has_valid_scheme( $url ) {
        return $this->does_string_start_with_substring( $url, 'https://' ) || $this->does_string_start_with_substring( $url, 'http://' );
    }
    private function does_string_start_with_substring( $string, $substring ) {
        return 0 === strpos( $string, $substring );
    }
    public function download() {
        if ( ! $this->is_url_valid() ) {
            return false;
        }
        // Download remote file and sideload it into the uploads directory.
        $file_attributes = $this->sideload();
        if ( ! $file_attributes ) {
            return false;
        }
        // Insert the image as a new attachment.
        $this->insert_attachment( $file_attributes['file'], $file_attributes['type'] );
        if ( ! $this->attachment_id ) {
            return false;
        }
        $this->update_metadata();
        $this->update_post_data();
        $this->update_alt_text();
        return $this->attachment_id;
    }
    private function is_url_valid() {
        $parsed_url = wp_parse_url( $this->url );
        return $this->has_valid_scheme( $this->url ) && $parsed_url && isset( $parsed_url['host'] );
    }
    private function sideload() {
        // Gives us access to the download_url() and wp_handle_sideload() functions.
        require_once ABSPATH . 'wp-admin/includes/file.php';
        // Download file to temp dir.
        $temp_file = download_url( $this->url, 10 );
        if ( is_wp_error( $temp_file ) ) {
            return false;
        }
        $mime_type = mime_content_type( $temp_file );
        if ( ! $this->is_supported_image_type( $mime_type ) ) {
            return false;
        }
        // An array similar to that of a PHP `$_FILES` POST array
        $file = array(
            'name'     => $this->get_filename( $mime_type ),
            'type'     => $mime_type,
            'tmp_name' => $temp_file,
            'error'    => 0,
            'size'     => filesize( $temp_file ),
        );
        $overrides = array(
            // This tells WordPress to not look for the POST form
            // fields that would normally be present. Default is true.
            // Since the file is being downloaded from a remote server,
            // there will be no form fields.
            'test_form'   => false,
            // Setting this to false lets WordPress allow empty files – not recommended.
            'test_size'   => true,
            // A properly uploaded file will pass this test.
            // There should be no reason to override this one.
            'test_upload' => true,
        );
        // Move the temporary file into the uploads directory.
        $file_attributes = wp_handle_sideload( $file, $overrides );
        if ( $this->did_a_sideloading_error_occur( $file_attributes ) ) {
            return false;
        }
        return $file_attributes;
    }
    private function is_supported_image_type( $mime_type ) {
        return in_array( $mime_type, array( 'image/jpeg', 'image/gif', 'image/png', 'image/x-icon', 'image/svg+xml' ), true );
    }
    private function get_filename($mime_type) {
        if (empty($this->attachment_data['title'])) {
            return basename($this->url);
        }
        $filename = sanitize_title_with_dashes($this->attachment_data['title']);
        $extension = $this->get_extension_from_mime_type($mime_type);
        return $filename . $extension;
    }
    private function get_extension_from_mime_type($mime_type) {
        $extensions = array(
            'image/jpeg'    => '.jpg',
            'image/gif'     => '.gif',
            'image/png'     => '.png',
            'image/x-icon'  => '.ico',
        );
        return isset($extensions[$mime_type]) ? $extensions[$mime_type] : '';
    }
    private function did_a_sideloading_error_occur($file_attributes) {
        return isset($file_attributes['error']);
    }
    private function insert_attachment($file_path, $mime_type) {
        // Get the path to the uploads directory.
        $wp_upload_dir = wp_upload_dir();
        // Prepare an array of post data for the attachment.
        $attachment_data = array(
            'guid'              => $wp_upload_dir['url'] . '/' . basename($file_path),
            'post_mime_type'    => $mime_type,
            'post_title'        => preg_replace('/\.[^.]+$/', '', basename($file_path)),
            'post_content'      => '',
            'post_status'       => 'inherit',
        );
        $attachment_id = wp_insert_attachment($attachment_data, $file_path);
        if (!$attachment_id) {
            return;
        }
        $this->attachment_id = $attachment_id;
    }
    private function update_metadata() {
        $file_path = get_attached_file($this->attachment_id);
        if (!$file_path) {
            return;
        }
        // Gives us access to the wp_generate_attachment_metadata() function.
        require_once ABSPATH . 'wp-admin/includes/image.php';
        // Generate metadata for the attachment and update the database record.
        $attach_data = wp_generate_attachment_metadata($this->attachment_id, $file_path);
        wp_update_attachment_metadata($this->attachment_id, $attach_data);
    }
    private function update_post_data() {
        if (empty($this->attachment_data['title']) && empty($this->attachment_data['caption']) && empty($this->attachment_data['description'])) {
            return;
        }
        $data = array(
            'ID' => $this->attachment_id,
        );
        // Set image title (post title)
        if (!empty($this->attachment_data['title'])) {
            $data['post_title'] = $this->attachment_data['title'];
        }
        // Set image caption (post excerpt)
        if (!empty($this->attachment_data['caption'])) {
            $data['post_excerpt'] = $this->attachment_data['caption'];
        }
        // Set image description (post content)
        if (!empty($this->attachment_data['description'])) {
            $data['post_content'] = $this->attachment_data['description'];
        }
        wp_update_post($data);
    }
    private function update_alt_text() {
        if (empty($this->attachment_data['alt_text']) && empty($this->attachment_data['title'])) {
            return;
        }
        // Use the alt text string provided, or the title as a fallback.
        $alt_text = !empty($this->attachment_data['alt_text']) ? $this->attachment_data['alt_text'] : $this->attachment_data['title'];
        update_post_meta($this->attachment_id, '_wp_attachment_image_alt', $alt_text);
    }
}