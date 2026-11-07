<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class NBD_Design_API extends WP_REST_Controller {

    protected $namespace    = 'nbdl/v1';
    protected $base         = 'designs';

    public function register_rest_routes() {
        register_rest_route( $this->namespace, '/' . $this->base, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_designs' ),
                'args'                => array_merge( $this->get_collection_params(),  array(
                    'status' => array(
                        'type'        => 'string',
                        'description' => __( 'Design status', 'web-to-print-online-designer' ),
                        'required'    => false,
                    ),
                )),
                'permission_callback' => array( $this, 'permission_check' ),
            )
        ));

        register_rest_route( $this->namespace, '/' . $this->base . '/(?P<id>[\d]+)/', array(
            'args' => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the object.', 'web-to-print-online-designer' ),
                    'type'        => 'integer',
                ),
            ),
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_design' ),
                'args'                => array(
                    'status' => array(
                        'type'        => 'integer',
                        'description' => __( 'Design status', 'web-to-print-online-designer' ),
                        'required'    => false,
                    )
                ),
                'permission_callback' => array( $this, 'permission_check' ),
            ),

            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_design' ),
                'permission_callback' => array( $this, 'permission_check' ),
            ),

        ));

        register_rest_route( $this->namespace, '/' . $this->base . '/batch', array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'batch_update' ),
                'permission_callback' => array( $this, 'permission_check' )
            ),
        ) );
    }

    public function permission_check() {
        return current_user_can( 'manage_options' );
    }

    public function get_designs( $request ){
        $_status        = ! empty( $request['status'] ) ? sanitize_text_field( $request['status'] ) : 'all';
        $status         = $this->get_status( $_status );
        $limit          = (int) $request['per_page'];
        $offset         = (int) ( $request['page'] - 1 ) * $limit;
        $user_id        = ! empty( $request['user_id'] ) ? absint( $request['user_id'] ) : '';
        $product_id     = ! empty( $request['product_id'] ) ? absint( $request['product_id'] ) : '';

        $designs        = nbdl_get_designs( $status, $limit, $offset, $user_id, $product_id );
        $counts         = nbdl_get_design_status_count( $user_id, $product_id );
        $total_count    = $counts[ $_status ];

        $data = array();
        foreach ( $designs as $key => $value ) {
            $resp   = $this->prepare_response_for_object( $value, $request );
            $data[] = $this->prepare_response_for_collection( $resp );
        }

        $response = rest_ensure_response( $data );
        $response->header( 'X-Status-Pending', $counts['pending'] );
        $response->header( 'X-Status-Approved', $counts['approved'] );
        $response->header( 'X-Status-All', $counts['all'] );

        $response = $this->format_collection_response( $response, $request, $total_count );
        return $response;
    }

    function get_status( $_status ){
        switch( $_status ){
            case 'pending':
                $status = 0;
                break;
            case 'approved':
                $status = 1;
                break;
            default:
                $status = '';
                break;
        }
        return $status;
    }

    public function prepare_response_for_object( $object, $request ) {
        $data = array(
            'id'            => $object->id,
            'user'          => nbdl_get_designer_data( $object->user_id ),
            'product'       => nbdl_get_product_data( $object->product_id ),
            'previews'      => nbdl_get_design_preview( $object->folder ),
            'date'          => $object->created_date,
            'folder'        => $object->folder,
            'resource'      => $object->resource,
            'type'          => $object->type,
            'status'        => (int) $object->publish
        );
        if( isset( $object->message ) ){
            $data['message'] = $object->message;
        }

        $response      = rest_ensure_response( $data );
        $response->add_links( $this->prepare_links( $object, $request ) );

        return  $response;
    }

    public function format_collection_response( $response, $request, $total_items ) {
        if ( $total_items === 0 ) {
            return $response;
        }

        $per_page = (int) ( ! empty( $request['per_page'] ) ? $request['per_page'] : 20 );
        $page     = (int) ( ! empty( $request['page'] ) ? $request['page'] : 1 );

        $response->header( 'X-WP-Total', (int) $total_items );

        $max_pages = ceil( $total_items / $per_page );

        $response->header( 'X-WP-TotalPages', (int) $max_pages );
        $base = add_query_arg( $request->get_query_params(), rest_url( sprintf( '/%s/%s', $this->namespace, $this->base ) ) );

        if ( $page > 1 ) {
            $prev_page = $page - 1;

            if ( $prev_page > $max_pages ) {
                $prev_page = $max_pages;
            }

            $prev_link = add_query_arg( 'page', $prev_page, $base );
            $response->link_header( 'prev', $prev_link );
        }

        if ( $max_pages > $page ) {

            $next_page = $page + 1;
            $next_link = add_query_arg( 'page', $next_page, $base );
            $response->link_header( 'next', $next_link );
        }

        return $response;
    }

    protected function prepare_links( $object, $request ) {
        $links = array(
            'self' => array(
                'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->base, $object->id ) ),
            ),
            'collection' => array(
                'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->base ) ),
            ),
        );

        return $links;
    }

    public function delete_design( $request ) {
        global $wpdb;

        $design_id = !empty( $request['id'] ) ? (int) $request['id'] : 0;

        if ( !$design_id ) {
            return new WP_Error( 'no_id', __( 'Invalid ID', 'web-to-print-online-designer' ), array( 'status' => 404 ) );
        }

        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}nbdesigner_templates WHERE id=%d", $design_id
            )
        );

        if ( empty( $result->id ) ) {
            return new WP_Error( 'no_design', __( 'No design found for deleting', 'web-to-print-online-designer' ), array( 'status' => 404 ) );
        }

        $deleted            = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}nbdesigner_templates WHERE id = %d", $design_id ) );
        $result->message    = $deleted ? __( 'Design has been delete successfully.', 'web-to-print-online-designer' ) : __( 'Design has been delete failed', 'web-to-print-online-designer' );
        
        return rest_ensure_response( $this->prepare_response_for_object( $result, $request ) );
    }

    public function batch_update( $request ){
        global $wpdb;

        $params = $request->get_params();

        if ( empty( $params ) ) {
            return new WP_Error( 'no_item_found', __( 'No items found for bulk updating', 'web-to-print-online-designer' ), array( 'status' => 404 ) );
        }
        
        $allowed_status = ['approved', 'pending', 'delete'];
        $response       = array();
        $approved       = array();

        foreach ( $params as $status => $value ) {
            if ( in_array( $status, $allowed_status ) ) {
                foreach ( $value as $design_id ) {
                    $status_code    = $this->get_status( $status );

                    if( $status == 'delete' ){
                        $response[] = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}nbdesigner_templates WHERE id = %d", $design_id ) );
                    }else{
                        $res = $wpdb->query( $wpdb->prepare(
                            "UPDATE {$wpdb->prefix}nbdesigner_templates
                            SET publish = %d WHERE id = %d",
                            (int) $status_code, (int) $design_id
                        ));

                        if( $res && $status_code == 1 ){
                            $approved[] = $design_id;
                        }

                        $response[] = $res;
                    }
                }
            }

            if( $status == 're_generate_preview' ){
                foreach ( $value as $design_id ) {
                    $approved[] = $design_id;
                }
            }
        }

        if( count( $approved ) > 0 ){
            nbdl_generate_color_product_design( $approved );
        }

        $response['message']    = __( 'Design status have been updated!', 'web-to-print-online-designer' );
        return $response;
    }
}