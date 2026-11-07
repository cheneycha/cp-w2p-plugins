<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class NBD_Withdraw_API extends WP_REST_Controller {
    protected $namespace    = 'nbdl/v1';
    protected $base         = 'withdraws';

    public function register_rest_routes() {
        register_rest_route( $this->namespace, '/' . $this->base, array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_withdraws' ),
                'args'                => array_merge( $this->get_collection_params(),  array(
                    'status' => array(
                        'type'        => 'string',
                        'description' => __( 'Withdraw status', 'web-to-print-online-designer' ),
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
                'callback'            => array( $this, 'update_withdraw' ),
                'args'                => array(
                    'status' => array(
                        'type'        => 'integer',
                        'description' => __( 'Withdraw status', 'web-to-print-online-designer' ),
                        'required'    => false,
                    ),
                    'note' => array(
                        'type'        => 'string',
                        'description' => __( 'Withdraw note', 'web-to-print-online-designer' ),
                        'required'    => false,
                    )
                ),
                'permission_callback' => array( $this, 'permission_check' ),
            ),

            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_withdraw' ),
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
    public function get_withdraws( $request ){
        $_status    = ! empty( $request['status'] ) ? sanitize_text_field( $request['status'] ) : 'pending';
        $status     = $this->get_status( $_status );
        $limit      = (int) $request['per_page'];
        $offset     = (int) ( $request['page'] - 1 ) * $request['per_page'];

        $withdraws      = NBDL_Withdraw()->get_withdraw_requests( '', $status, $limit, $offset );
        $counts         = nbdl_get_withdraw_status_count();
        $total_count    = $counts[ $_status ];

        $data = array();
        foreach ( $withdraws as $key => $value ) {
            $resp   = $this->prepare_response_for_object( $value, $request );
            $data[] = $this->prepare_response_for_collection( $resp );
        }

        $response = rest_ensure_response( $data );
        $response->header( 'X-Status-Pending', $counts['pending'] );
        $response->header( 'X-Status-Approved', $counts['approved'] );
        $response->header( 'X-Status-Cancelled', $counts['cancelled'] );

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
            case 'cancelled':
                $status = 2;
                break;
        }
        return $status;
    }
    public function prepare_response_for_object( $object, $request ) {
        $data = array(
            'id'           => $object->id,
            'user'         => $this->get_user_data( $object->user_id ),
            'amount'       => wc_price( $object->amount ),
            'date'         => $object->date,
            'status'       => (int) $object->status,
            'note'         => $object->note,
            'ip'           => $object->ip
        );
        if( isset( $object->message ) ){
            $data['message'] = $object->message;
        }

        $response      = rest_ensure_response( $data );
        $response->add_links( $this->prepare_links( $object, $request ) );

        return  $response;
    }
    public function get_user_data( $user_id ) {
        $designer = new NBD_Designer( $user_id );
        return $designer->to_array();
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
    public function update_withdraw( $request ){
        global $wpdb;

        $request_id = (int) $request['id'];
        $result     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}nbdesigner_withdraw WHERE id=%d", $request_id ) );
        $user_id    = $result->user_id;

        if( isset( $request['status'] ) ){
            $status     = $request['status'];
            if( $status == 1 ){
                if ( round( nbdl_get_designer_balance( $result->user_id, false ), 2 ) < $result->amount ) {
                    return new WP_Error( 'not_approve_withdraw', __( 'Can not approve this withdraw request because the balance is less than the withdraw amount.', 'web-to-print-online-designer' ), array( 'status' => 400 ) );
                }

                $balance_result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}nbdesigner_balance WHERE transaction_id=%d AND transaction_type = %s", $request_id, 'withdraw' ) );
            
                if ( empty( $balance_result ) ) {
                    $wpdb->insert( $wpdb->prefix . 'nbdesigner_balance',
                        array(
                            'user_id'               => $user_id,
                            'transaction_id'        => $request_id,
                            'transaction_type'      => 'withdraw',
                            'note'                  => 'Approve withdraw request',
                            'debit'                 => 0,
                            'credit'                => $result->amount,
                            'status'                => 'approved',
                            'transaction_date'      => current_time( 'mysql' ),
                            'balance_date'          => current_time( 'mysql' )
                        ),
                        array(
                            '%d',
                            '%d',
                            '%s',
                            '%s',
                            '%f',
                            '%f',
                            '%s',
                            '%s',
                            '%s'
                        )
                    );
                }
            }

            NBDL_Withdraw()->update_status( $request_id, $user_id, $status );
            $response = $result;
            $response = $this->prepare_response_for_object( $response, $request );

            if ( $status === 1 ) {
                do_action( 'nbdl_withdraw_request_approved', $user_id, $result );
            } elseif ( $status === 2 ) {
                do_action( 'nbdl_withdraw_request_cancelled', $user_id, $result );
            }
        }elseif( isset( $request['note'] ) ){
            $note       = sanitize_textarea_field( $request['note'] );
            $table_name = $wpdb->prefix . 'nbdesigner_withdraw';
            $update     = $wpdb->update( $table_name, array( 'note' => $note ), array( 'id' => $request_id ) );
    
            if ( ! $update ) {
                return new WP_Error( 'note_not_udpated', __( 'Something wrong, Note not updated', 'web-to-print-online-designer' ), array( 'status' => 404 ) );
            }

            $withdraw           = $wpdb->get_row( $wpdb->prepare("SELECT * from {$wpdb->prefix}nbdesigner_withdraw WHERE id = %d", $request_id ) );
            $withdraw->message  = __( 'Withdraw note has been updated!', 'web-to-print-online-designer' );
            $response = $this->prepare_response_for_object( $withdraw, $request );
        }

        return $response;
    }
    public function delete_withdraw( $request ) {
        global $wpdb;

        $withdraw_id = !empty( $request['id'] ) ? (int) $request['id'] : 0;

        if ( !$withdraw_id ) {
            return new WP_Error( 'no_id', __( 'Invalid ID', 'web-to-print-online-designer' ), array( 'status' => 404 ) );
        }

        $result = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}nbdesigner_withdraw WHERE id=%d", $withdraw_id
            )
        );

        if ( empty( $result->id ) ) {
            return new WP_Error( 'no_withdraw', __( 'No withdraw found for deleting', 'web-to-print-online-designer' ), array( 'status' => 404 ) );
        }

        $deleted            = $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}nbdesigner_withdraw WHERE id = %d", $withdraw_id ) );
        $result->message    = $deleted ? __( 'Withdraw has been delete successfully.', 'web-to-print-online-designer' ) : __( 'Withdraw has been delete failed', 'web-to-print-online-designer' );
        
        return rest_ensure_response( $this->prepare_response_for_object( $result, $request ) );
    }
    public function batch_update( $request ){
        global $wpdb;

        $params = $request->get_params();

        if ( empty( $params ) ) {
            return new WP_Error( 'no_item_found', __( 'No items found for bulk updating', 'web-to-print-online-designer' ), [ 'status' => 404 ] );
        }
        
        $allowed_status = ['approved', 'pending', 'cancelled'];
        $response       = array();

        foreach ( $params as $status => $value ) {
            if ( in_array( $status, $allowed_status ) ) {
                foreach ( $value as $withdraw_id ) {
                    $status_code    = $this->get_status( $status );
                    $user           = $wpdb->get_row( $wpdb->prepare("SELECT user_id, amount FROM {$wpdb->prefix}nbdesigner_withdraw WHERE id = %d", (int) $withdraw_id ) );

                    if( $status == 'approved' ){
                        if ( nbdl_get_designer_balance( $user->user_id, false ) < $user->amount ) {
                            continue;
                        }

                        $balance_result = $wpdb->get_row(
                            $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}nbdesigner_balance WHERE transaction_id=%d AND transaction_type = 'withdraw'", (int) $withdraw_id )
                        );

                        if ( empty( $balance_result ) ) {
                            $wpdb->insert( $wpdb->prefix . 'nbdesigner_balance',
                                array(
                                    'user_id'           => (int) $user->user_id,
                                    'transaction_id'    => (int) $withdraw_id,
                                    'transaction_type'  => 'withdraw',
                                    'note'              => 'Approve withdraw request',
                                    'debit'             => 0,
                                    'credit'            => (float) $user->amount,
                                    'status'            => 'approved',
                                    'transaction_date'  => current_time( 'mysql' ),
                                    'balance_date'      => current_time( 'mysql' )
                                ),
                                array(
                                    '%d',
                                    '%d',
                                    '%s',
                                    '%s',
                                    '%f',
                                    '%f',
                                    '%s',
                                    '%s',
                                    '%s'
                                )
                            );
                        }
                    }

                    $response[] = $wpdb->query( $wpdb->prepare(
                        "UPDATE {$wpdb->prefix}nbdesigner_withdraw
                        SET status = %d WHERE id = %d",
                        (int) $status_code, (int) $withdraw_id
                    ));
                }
            }
        }

        $response['message']    = __( 'Withdraw status have been updated!', 'web-to-print-online-designer' );
        return $response;
    }
}