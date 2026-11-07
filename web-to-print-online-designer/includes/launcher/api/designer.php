<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class NBD_Designer_API extends WP_REST_Controller {

    protected $namespace = 'nbdl/v1';
    protected $base = 'designers';

    public function register_rest_routes() {
        register_rest_route( $this->namespace, '/' . $this->base, array(
            array(
                'methods'  => WP_REST_Server::READABLE,
                'callback' => array( $this, 'get_designers' ),
                'args'     => $this->get_collection_params(),
                'permission_callback' => array( $this, 'permission_check' )
            )
        ) );
        register_rest_route( $this->namespace, '/' . $this->base . '/(?P<id>[\d]+)', array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_designer' ),
                'permission_callback' => array( $this, 'permission_check' ),
            ),
            array(
                'methods'               => WP_REST_Server::READABLE,
                'callback'              => array( $this, 'get_design' ),
                'permission_callback'   => array( $this, 'permission_check' )
            )
        ));
        register_rest_route( $this->namespace, '/' . $this->base . '/(?P<id>[\d]+)/status', array(
            'args' => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the object.' ),
                    'type'        => 'integer',
                    'required'    => true
                ),
                'enabled' => array(
                    'description' => __( 'Status enable for the designer object.' ),
                    'type'        => 'string',
                    'required'    => false
                ),
                'featured' => array(
                    'description' => __( 'Status feature for the designer object.' ),
                    'type'        => 'string',
                    'required'    => false
                )
            ),
            array(
                'methods'  => WP_REST_Server::EDITABLE,
                'callback' => array( $this, 'update_designer_status' ),
                'permission_callback' => array( $this, 'permission_check' ),
            ),
        ));
        register_rest_route( $this->namespace, '/' . $this->base . '/batch', array(
            array(
                'methods'  => WP_REST_Server::EDITABLE,
                'callback' => array( $this, 'batch_update' ),
                'permission_callback' => array( $this, 'permission_check' ),
            ),
        ));
        register_rest_route( $this->namespace, '/' . $this->base . '/(?P<id>[\d]+)/stats' , array(
            'args' => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the object.' ),
                    'type'        => 'integer',
                ),
            ),
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_designer_stats' ),
                'args'                => $this->get_collection_params(),
                'permission_callback' => array( $this, 'permission_check' ),
            ),
        ));
        register_rest_route( $this->namespace, '/' . $this->base . '/(?P<id>[\d]+)/email' , array(
            'args' => array(
                'id' => array(
                    'description' => __( 'Unique identifier for the object.' ),
                    'type'        => 'integer',
                ),
                'subject' => array(
                    'description' => __( 'Subject of the email.' ),
                    'type'        => 'string',
                    'required'    => true,
                ),
                'message' => array(
                    'description' => __( 'Body of the email.' ),
                    'type'        => 'string',
                    'required'    => true,
                ),
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'send_email_to_designer' ),
                'args'                => $this->get_collection_params(),
                'permission_callback' => array( $this, 'permission_check' ),
            ),
        ));
    }
    public function permission_check() {
        return current_user_can( 'manage_options' );
    }
    public function get_designers( $request ) {
        $params = $request->get_params();

        $args = array(
            'number' => (int) $params['per_page'],
            'offset' => (int) ( $params['page'] - 1 ) * $params['per_page']
        );

        if ( ! empty( $params['search'] ) ) {
            $args['search']         = '*' . sanitize_text_field( ( $params['search'] ) ) . '*';
            $args['search_columns'] = array( 'user_login', 'user_email', 'display_name' );
        }

        if ( ! empty( $params['status'] ) ) {
            $args['status'] = sanitize_text_field( $params['status'] );
        }

        if ( ! empty( $params['orderby'] ) ) {
            $args['orderby'] = sanitize_sql_orderby( $params['orderby'] );
        }

        if ( ! empty( $params['order'] ) ) {
            $args['order'] = sanitize_text_field( $params['order'] );
        }

        if ( ! empty( $params['featured'] ) ) {
            $args['featured'] = sanitize_text_field( $params['featured'] );
        }

        $args = apply_filters( 'nbdl_rest_get_designers_args', $args, $request );

        $data         = nbdl_get_designers( $args );
        $data_objects = array();

        foreach ( $data['designers'] as $designer ) {
            $designers_data    = $this->prepare_item_for_response( $designer, $request );
            $data_objects[]    = $this->prepare_response_for_collection( $designers_data );
        }

        $response = rest_ensure_response( $data_objects );
        $response = $this->format_collection_response( $response, $request, $data['total'] );

        return $response;
    }
    public function get_designer_stats( $request ){
        $designer_id    = (int) $request['id'];
        $designer       = new NBD_Designer( $designer_id );

        $designs        = nbdl_count_designs( $designer_id );
        $sold           = nbdl_count_design_items( $designer_id );

        $response = array(
            'designs' => array(
                'total'     => $designs->total,
                'publish'   => $designs->publish
            ),
            'revenue'  => array(
                'sold'      => $sold,
                'earning'   => $designer->get_earnings(),
                'balance'   => $designer->get_balance()
            )
        );

        return rest_ensure_response( $response );
    }
    public function prepare_item_for_response( $designer, $request, $additional_fields = [] ) {
        $data       = $designer->to_array();
        $data       = array_merge( $data, $additional_fields );
        $response   = rest_ensure_response( $data );
        $response->add_links( $this->prepare_links( $data, $request ) );
        return $response;
    }
    public function format_collection_response( $response, $request, $total_items ){
        $per_page       = (int) ( ! empty( $request['per_page'] ) ? $request['per_page'] : 20 );
        $page           = (int) ( ! empty( $request['page'] ) ? $request['page'] : 1 );
        $counts         = nbdl_get_designer_status_count();
        $max_pages      = ceil( $total_items / $per_page );
        
        $response->header( 'X-Status-Pending', (int) $counts['inactive'] );
        $response->header( 'X-Status-Approved', (int) $counts['active'] );
        $response->header( 'X-Status-All', (int) $counts['total'] );
        $response->header( 'X-WP-Total', (int) $total_items );
        $response->header( 'X-WP-TotalPages', (int) $max_pages );
        
        if ( $total_items === 0 ) {
            return $response;
        }

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
                'href' => rest_url( sprintf( '/%s/%s/%d', $this->namespace, $this->base, $object['id'] ) ),
            ),
            'collection' => array(
                'href' => rest_url( sprintf( '/%s/%s', $this->namespace, $this->base ) ),
            ),
        );

        return $links;
    }
    public function get( $vendor ) {
        return new NBD_Designer( $vendor );
    }
    public function update_designer( $request ){
        $designer_id = (int) $request->get_param( 'id' );
        $designer    = new NBD_Designer( $designer_id );
        
        if ( empty( $designer->get_id() ) ) {
            return new WP_Error( 'no_designer_found', __( 'No designer found', 'web-to-print-online-designer' ), array( 'status' => 404 ) );
        }
        
        $params         = $request->get_params();
        $designer_id    = $designer->update( $params );

        if ( is_wp_error( $designer_id ) ) {
            return new WP_Error( $designer_id->get_error_code(), $designer_id->get_error_message() );
        }
        
        $designer       = new NBD_Designer( $designer_id );
        $designer_data  = $this->prepare_item_for_response( $designer, $request, array( 'message' => __( 'Designer status has been updated!', 'web-to-print-online-designer' ) ) );
        $response       = rest_ensure_response( $designer_data );

        return $response;
    }
    public function update_designer_status( $request ){
        if ( !( isset( $request['enabled'] ) || isset( $request['featured'] ) ) ) {
            return new WP_Error( 'no_valid_status', __( 'Status parameter must be enabled or featured', 'web-to-print-online-designer' ), array( 'status' => 400 ) );
        }
        
        $designer_id = ! empty( $request['id'] ) ? $request['id'] : 0;
        
        if ( empty( $designer_id ) ) {
            return new WP_Error( 'no_designer_found', __( 'No designer found for updating status', 'web-to-print-online-designer' ), array( 'status' => 400 ) );
        }
        
        $designer = new NBD_Designer( $designer_id );
        
        if( isset( $request['enabled'] ) ) {
            $data = $designer->update_enabled( $request['enabled'] );
        }
        if( isset( $request['featured'] ) ){
            $data = $designer->update_featured( $request['featured'] );
        }
        
        $data['message']    = __( 'Designer status has been updated!', 'web-to-print-online-designer' );
        $response           = rest_ensure_response( $data );
        $response->add_links( $this->prepare_links( $data, $request ) );
        return $response;
    }
    public function batch_update( $request ){
        $params = $request->get_params();

        if ( empty( $params ) ) {
            return new WP_Error( 'no_item_found', __( 'No items found for bulk updating', 'web-to-print-online-designer' ), [ 'status' => 404 ] );
        }
        
        $allowed_status = ['approved', 'pending'];
        $response       = array();
        
        foreach ( $params as $status => $value ) {
            if ( in_array( $status, $allowed_status ) ) {
                switch ( $status ) {
                    case 'approved':
                        foreach ( $value as $designer_id ) {
                            $designer = new NBD_Designer( $designer_id );
                            $response['approved'][] = $designer->update_enabled( 'on' );
                        }
                        break;

                    case 'pending':
                        foreach ( $value as $designer_id ) {
                            $designer = new NBD_Designer( $designer_id );
                            $response['pending'][] = $designer->update_enabled( '' );
                        }
                        break;
                }
            }
        }
        $response['message']    = __( 'Designers status have been updated!', 'web-to-print-online-designer' );
        return $response;
    }
    public function get_design( $request ){
        $designer_id    = (int) $request['id'];
        $designer       = new NBD_Designer( $designer_id );
        
        if ( empty( $designer->id ) ) {
            return new WP_Error( 'no_designer_found', __( 'No designer found', 'web-to-print-online-designer' ), array( 'status' => 404 ) );
        }
        
        $designer_data  = $this->prepare_item_for_response( $designer, $request );
        $response       = rest_ensure_response( $designer_data );

        return $response;
    }
    public function send_email_to_designer( $request ){
        $response       = array( 'success' => true );
        $designer_id    = $request['id'];
        $designer       = new NBD_Designer( $designer_id );

        $subject   = $request['subject'];
        $message   = $request['message'];

        $response['success']    = wp_mail( $designer->get_email(), $subject, $message );
        $response['message']    = $response['success'] ? __( 'Email has been sent successfully.', 'web-to-print-online-designer' ) : __( 'Email has been sent failed', 'web-to-print-online-designer' );

        return rest_ensure_response( $response );
    }
}