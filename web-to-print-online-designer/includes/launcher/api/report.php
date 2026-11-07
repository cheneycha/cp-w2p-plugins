<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class NBD_Report_API extends WP_REST_Controller {

    protected $namespace    = 'nbdl/v1';
    protected $base         = 'report';

    public function register_rest_routes() {

        register_rest_route( $this->namespace, '/' . $this->base . '/summary', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_summary' ),
                'permission_callback' => array( $this, 'permission_check' )
            )
        ) );

        register_rest_route( $this->namespace, '/' . $this->base . '/overview', array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_overview' ),
                'permission_callback' => array( $this, 'permission_check' ),
            )
        ) );
    }

    public function permission_check() {
        return current_user_can( 'manage_options' );
    }

    public function get_summary( $request ){
        $params = $request->get_params();

        $from           = isset( $params['from'] ) ? sanitize_text_field( $params['from'] ) : null;
        $to             = isset( $params['to'] ) ? sanitize_text_field( $params['to'] ) : null;
        $designer_id    = isset( $params['designer_id'] ) ? (int) $params['designer_id'] : 0;

        $data = array(
            'designs'   => nbdl_get_summary_design( $from, $to, $designer_id ),
            'withdraw'  => nbdl_get_withdraw_status_count(),
            'designers' => nbdl_get_summary_designer( $from, $to ),
            'sales'     => nbdl_get_summary_sale( $from, $to, $designer_id )
        );

        return rest_ensure_response( $data );
    }

    public function get_overview( $request ){
        $params = $request->get_params();

        $from           = isset( $params['from'] ) ? sanitize_text_field( $params['from'] ) : 'first day of this month';
        $to             = isset( $params['to'] ) ? sanitize_text_field( $params['to'] ) : '';
        $designer_id    = isset( $params['designer_id'] ) ? (int) $params['designer_id'] : 0;

        if( $from != 'first day of this month' && !DateTime::createFromFormat('Y-m-d', $from) ){
            $start_date = new DateTime();
        }else{
            $start_date = new DateTime( $from );
        }
        if( !DateTime::createFromFormat('Y-m-d', $to) ){
            $end_date   = new DateTime();
        }else{
            $end_date   = new DateTime( $to );
        }

        $date_modifier  = $start_date->diff( $end_date )->m > 1 ? '+1 month' : '+1 day';
        $group_by       = $date_modifier === '+1 month' ? 'month' : 'day';

        $labels         = array();
        $design_counts  = array();
        $sale_counts    = array();

        $design_data    = nbdl_get_design_report( $group_by, $start_date->format( 'Y-m-d' ), $end_date->format( 'Y-m-d' ), $designer_id );
        $sale_data      = nbdl_get_sale_report( $group_by, $start_date->format( 'Y-m-d' ), $end_date->format( 'Y-m-d' ), $designer_id );

        for ( $i = $start_date; $i <= $end_date; $i->modify( $date_modifier ) ){
            $date                     = $i->format( 'Y-m-d' );
            $labels[ $date ]          = $date;
            $design_counts[ $date ]   = 0;
            $sale_counts[ $date ]     = 0;
        }

        foreach ( $design_data as $row ) {

            if ( 'month' == $group_by ) {
                $date = new DateTime( $row->created_date );
                $date->modify( 'first day of this month' );
                $date = $date->format( 'Y-m-d' );
            } else {
                $date = date( 'Y-m-d', strtotime( $row->created_date ) );
            }

            $design_counts[ $date ] = (int) $row->total;
        }

        foreach ( $sale_data as $row ) {

            if ( 'month' == $group_by ) {
                $date = new DateTime( $row->created_date );
                $date->modify( 'first day of this month' );
                $date = $date->format( 'Y-m-d' );
            } else {
                $date = date( 'Y-m-d', strtotime( $row->created_date ) );
            }

            $sale_counts[ $date ] = (int) $row->total;
        }

        $data = array(
            'labels'   => array_values( $labels ),
            'datasets' => array(
                array(
                    'label'           => __( 'Created designs', 'web-to-print-online-designer' ),
                    'borderColor'     => '#3498db',
                    'fill'            => false,
                    'data'            => array_values( $design_counts ),
                    'tooltipLabel'    => __( 'Total', 'web-to-print-online-designer' )
                ),
                array(
                    'label'           => __( 'Sold designs', 'web-to-print-online-designer' ),
                    'borderColor'     => '#1abc9c',
                    'fill'            => false,
                    'data'            => array_values( $sale_counts ),
                    'tooltipLabel'    => __( 'Total', 'web-to-print-online-designer' )
                )
            )
        );

        return rest_ensure_response( $data );
    }
}