<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function nbdl_get_designers( $args, $get_info = true ){
    $designers  = array();
    $defaults   = array(
        'role__in'   => array( 'designer', 'administrator', 'shop_manager' ),
        'number'     => 10,
        'offset'     => 0,
        'orderby'    => 'registered',
        'order'      => 'ASC',
        'status'     => 'all',
        'featured'   => '',
        'meta_query' => array(),
    );
    $args   = wp_parse_args( $args, $defaults );
    
    if ( in_array( $args['status'], array( 'approved', 'pending' ) ) ) {
        if( $args['status'] != 'approved' ){
            $args['meta_query'][] = array(
                'relation' => 'OR',
                array(
                    'key'     => 'nbd_sell_design',
                    'value'   => 'on',
                    'compare' => '!='
                ),
                array(
                    'key'     => 'nbd_sell_design',
                    'compare' => 'NOT EXISTS'
                )
            );
        }else{
            $args['meta_query'][] = array(
                'key'     => 'nbd_sell_design',
                'value'   => 'on',
                'compare' => '='
            );
        }
    }

    if ( 'yes' == $args['featured'] ) {
        $args['meta_query'][] = array(
            'key'     => 'nbd_feature_designer',
            'value'   => 'on',
            'compare' => '='
        );
    }

    unset( $args['status'] );
    unset( $args['featured'] );

    $user_query  = new WP_User_Query( $args );
    $results     = $user_query->get_results();

    $total_users = $user_query->total_users;

    if( $get_info ){
        foreach ( $results as $key => $result ) {
            $designers[] = nbdl_get_designer( $result );
        }
    }

    return array(
        'designers' => $designers,
        'total'     => $total_users
    );
}

function nbdl_get_designer_status_count() {
    $active_users = new WP_User_Query( array(
        'role__in'   => array( 'designer', 'administrator', 'shop_manager' ),
        'meta_key'   => 'nbd_sell_design',
        'meta_value' => 'on',
        'fields'     => 'ID'
    ));

    $all_users      = new WP_User_Query( array( 'role__in'   => array( 'designer', 'administrator', 'shop_manager' ), 'fields' => 'ID' ) );
    $active_count   = $active_users->get_total();
    $inactive_count = $all_users->get_total() - $active_count;

    return apply_filters( 'nbdl_get_designer_status_count', [
        'total'    => $active_count + $inactive_count,
        'active'   => $active_count,
        'inactive' => $inactive_count,
    ]);
}

function nbdl_get_designer( $designer ){
    return new NBD_Designer( $designer );
}

function nbdl_get_designer_data( $user_id ){
    $designer = new NBD_Designer( $user_id );
    return $designer->to_array();
}

function nbdl_is_user_designer( $user_id ) {
    if ( ! user_can( $user_id, 'become_designer' ) ) {
        return false;
    }
    return true;
}

function nbdl_is_designer_enabled( $user_id ) {
    $selling = get_user_meta( $user_id, 'nbd_sell_design', true );
    if ( $selling == 'on' ) {
        return true;
    }
    return false;
}

function nbd_get_designers_by( $order ){
    if ( ! $order instanceof WC_Order ) {
        $order  = wc_get_order( $order );
    }

    $order_items    = $order->get_items();
    $designers      = array();

    foreach ( $order_items as $item_id => $item ) {
        $design_id = wc_get_order_item_meta( $item_id, '_nbd_design_id' );
        if( $design_id ){
            $designer_id = nbd_get_designer_by_design( $design_id );
            if( $designer_id ){
                $designers[ $designer_id ][$design_id][] = $item;
            }
        }
    }

    return $designers;
}

function nbdl_count_designs( $designer_id ){
    global $wpdb;

    $cache_group = 'nbdl_designer_design_data_' . $designer_id;
    $cache_key   = 'nbdl-count-designs-' . $designer_id;
    $counts      = wp_cache_get( $cache_key, $cache_group );

    if ( false === $counts ) {
        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT publish, COUNT( * ) AS num_designs FROM {$wpdb->prefix}nbdesigner_templates WHERE user_id = %d GROUP BY publish",
                $designer_id
            ),
            ARRAY_A
        );

        $total  = 0;
        $counts = array(
            'publish'   => 0,
            'unpublish' => 0
        );

        foreach ( $results as $row ) {
            if( $row['publish'] == 1 ){
                $counts['publish'] = (int) $row['num_designs'];
            }else{
                $counts['unpublish'] = (int) $row['num_designs'];
            }
            $total += (int) $row['num_designs'];
        }

        $counts['total'] = $total;
        $counts = (object) $counts;

        wp_cache_set( $cache_key, $counts, $cache_group, 3600 * 6 );
    }

    return $counts;
}

function nbdl_count_design_items( $designer_id ){
    global $wpdb;

    $qty = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT SUM( qty ) AS qty FROM {$wpdb->prefix}nbdesigner_orders AS od 
            LEFT JOIN {$wpdb->prefix}nbdesigner_templates AS template ON od.design_id = template.folder
            WHERE template.user_id = %d",
            $designer_id
        )
    );

    return (int) $qty;
}

function nbdl_get_order_status_for_withdraw( $in_comma = false ){
    $status = array();

    if( nbdesigner_get_option( 'nbdesigner_order_status_for_withdraw_wc-completed', 1 ) == 1 ){
        $status[] = 'wc-completed';
    }
    if( nbdesigner_get_option( 'nbdesigner_order_status_for_withdraw_wc-processing', 0 ) == 1 ){
        $status[] = 'wc-processing';
    }
    if( nbdesigner_get_option( 'nbdesigner_order_status_for_withdraw_wc-on-hold', 0 ) == 1 ){
        $status[] = 'wc-on-hold';
    }

    if( !count( $status ) ){
        $status = array( 'wc-completed', 'wc-processing', 'wc-on-hold' );
    }

    return $in_comma ? ( "'" . implode("', '", $status ) . "'" ) : $status;
}

function nbdl_get_designer_balance( $user_id, $formatted ){
    $designer = new NBD_Designer( $user_id );
    return $designer->get_balance( false );
}

function nbdl_get_withdraw_status_count( $user_id = '' ) {
    global $wpdb;

    $cache_key = 'nbdl_withdraw_count-' . $user_id;
    $counts    = wp_cache_get( $cache_key );

    if ( false === $counts ) {
        $counts     = array( 'pending' => 0, 'approved' => 0, 'cancelled' => 0 );

        if ( ! empty( $user_id ) ) {
            $result  = $wpdb->get_results( $wpdb->prepare( "SELECT COUNT(id) as count, status FROM {$wpdb->prefix}nbdesigner_withdraw WHERE user_id=%d GROUP BY status", $user_id ) );
        } else {
            $result  = $wpdb->get_results( "SELECT COUNT(id) as count, status FROM {$wpdb->prefix}nbdesigner_withdraw WHERE 1=1 GROUP BY status" );
        }

        if ( $result ) {
            foreach ($result as $row) {
                if ( $row->status == '0' ) {
                    $counts['pending'] = (int) $row->count;
                } elseif ( $row->status == '1' ) {
                    $counts['approved'] = (int) $row->count;
                } elseif ( $row->status == '2' ) {
                    $counts['cancelled'] = (int) $row->count;
                }
            }
        }
    }

    return $counts;
}

function nbdl_get_designs( $status = '', $limit = 10, $offset = 0, $user_id = '', $product_id = '' ){
    global $wpdb;

    if( $status === '' ){
        $sql = "SELECT * FROM {$wpdb->prefix}nbdesigner_templates WHERE 1 = 1";
    }else{
        $sql = "SELECT * FROM {$wpdb->prefix}nbdesigner_templates WHERE publish = {$status}";
    }
    if( !empty( $user_id ) ){
        $sql .= " AND user_id = {$user_id}";
    }
    if( !empty( $product_id ) ){
        $sql .= " AND product_id = {$product_id}";
    }
    $sql .= " ORDER BY created_date DESC LIMIT {$offset}, {$limit}";

    $result = $wpdb->get_results( $sql );
    return $result;
}

function nbdl_get_design_status_count( $user_id = '', $product_id = '' ) {
    global $wpdb;

    $counts = array( 'all' => 0, 'pending' => 0, 'approved' => 0 );

    $sql = "SELECT COUNT(id) as count, publish FROM {$wpdb->prefix}nbdesigner_templates WHERE 1 = 1";
    if( !empty( $user_id ) ){
        $sql .= " AND user_id = {$user_id}";
    }
    if( !empty( $product_id ) ){
        $sql .= " AND product_id = {$product_id}";
    }
    $sql .= " GROUP BY publish";

    $result = $wpdb->get_results( $sql );

    if ( $result ) {
        foreach ($result as $row) {
            if ( $row->publish == '0' ) {
                $counts['pending'] = (int) $row->count;
            } elseif ( $row->publish == '1' ) {
                $counts['approved'] = (int) $row->count;
            }
        }
        $counts['all'] = $counts['pending'] + $counts['approved'];
    }

    return $counts;
}

function nbdl_get_design_preview( $folder ){
    $list_design    = array();
    $listThumb      = Nbdesigner_IO::get_list_images(NBDESIGNER_CUSTOMER_DIR. '/' . $folder . '/preview/', 1);
    asort($listThumb);
    if( count( $listThumb ) ){
        foreach ( $listThumb as $img ){
            $name           = basename($img);
            $url            = Nbdesigner_IO::wp_convert_path_to_url($img) . '?&t=' . round(microtime(true) * 1000);
            $list_design[]  = $url;
        }
    }
    return $list_design;
}

function nbdl_get_product_data( $product_id, $variation_id = 0 ){
    $product    = wc_get_product( $product_id );
    $data       = array(
        'product_id'    => $product_id,
        'variation_id'  => $variation_id,
        'link'          => '#',
        'name'          => ''
    );

    if( is_object( $product ) ){
        $data['link'] = get_edit_post_link( $product_id );
        $data['name'] = $product->get_name();
    }

    return $data;
}

function nbdl_get_summary_design( $from = null, $to = null, $user_id = null ) {
    $this_month_designs = nbdl_count_designs_in_period( $user_id, array(
        'year'  => date( 'Y' ),
        'month' => date( 'm' )
    ) );

    $last_month_designs = nbdl_count_designs_in_period( $user_id, array(
        'year'  => date( 'Y', strtotime( 'last month' ) ),
        'month' => date( 'm', strtotime( 'last month' ) )
    ) );

    $pending_designs = nbdl_get_design_status_count( $user_id, '' )['pending'];

    if ( $from && $to ) {
        $prepared_date = nbdl_prepare_date_query( $from, $to );

        if( is_array( $prepared_date ) ){
            $this_period = nbdl_count_designs_in_period( $user_id, array(
                'after' => array(
                    'year'  => $prepared_date['from_year'],
                    'month' => $prepared_date['from_month'],
                    'day'   => $prepared_date['from_day']
                ),
                'before' => array(
                    'year'  => $prepared_date['to_year'],
                    'month' => $prepared_date['to_month'],
                    'day'   => $prepared_date['to_day']
                )
            ) );

            $last_period = nbdl_count_designs_in_period( $user_id, array(
                'after' => array(
                    'year'  => $prepared_date['last_from_year'],
                    'month' => $prepared_date['last_from_month'],
                    'day'   => $prepared_date['last_from_day'],
                ),
                'before' => array(
                    'year'  => $prepared_date['last_to_year'],
                    'month' => $prepared_date['last_to_month'],
                    'day'   => $prepared_date['last_to_day'],
                )
            ) );
        } else {
            $percent = array(
                'value' => '--',
                'class' => ''
            );
            $this_period = null;
        }

        $percent = nbdl_get_percentage( $this_period, $last_period );
    }else{
        $percent = nbdl_get_percentage( $this_month_designs, $last_month_designs );
    }

    return array(
        'this_month'        => $this_month_designs,
        'last_month'        => $last_month_designs,
        'pending_designs'   => $pending_designs,
        'this_period'       => $from && $to && $this_period ? $this_period : null,
        'class'             => $percent['class'],
        'percent'           => $percent['value']
    );
}

function nbdl_count_designs_in_period( $user_id = null, $args = array(), $status = '' ){
    global $wpdb;

    $count  = 0;
    $sql    = "SELECT * FROM {$wpdb->prefix}nbdesigner_templates WHERE 1 = 1";

    if( isset( $args['after'] ) ){
        $after  = sprintf( '%04d-%02d-%02d %02d:%02d:%02d', $args['after']['year'], $args['after']['month'], $args['after']['day'], 0, 0, 0 );
        $before = sprintf( '%04d-%02d-%02d %02d:%02d:%02d', $args['before']['year'], $args['before']['month'], $args['before']['day'], 23, 59, 59 );
        $sql   .= " AND created_date > '$after' AND created_date <= '$before'";
    }else{
        if( isset( $args['year'] ) ){
            $year = absint( $args['year'] );
            $sql .= " AND YEAR(created_date) = $year";
        }
        if( isset( $args['month'] ) ){
            $month  = absint( $args['month'] );
            $sql   .= " AND MONTH(created_date) = $month";
        }
        if( isset( $args['day'] ) ){
            $day    = absint( $args['day'] );
            $sql   .= " AND DAYOFMONTH(created_date) = $day";
        }
    }

    if( $user_id ){
        $sql .= " AND user_id = {$user_id}";
    }

    if( !empty( $status ) ){
        $status_code = $status == 'approved' ? 1 : 0;
        $sql .= " AND publish = {$status_code}";
    }

    $result = $wpdb->get_results( $sql );

    if ( $result ) {
        $count = count( $result );
    }

    return $count;
}

function nbdl_get_summary_designer( $from, $to ){
    
    $inactive_designers = nbdl_get_designers( array(
        'number'    => -1,
        'status'    => 'pending',
        'role__in'  => array( 'designer' )
    ), false );

    $active_designers = nbdl_get_designers( array(
        'number'   => -1,
        'status'   => 'approved',
        'role__in' => array( 'designer' )
    ), false );

    $this_month = nbdl_get_designers( array(
        'date_query'    => array(
            array(
                'year'  => date('Y'),
                'month' => date('m')
            ),
        ),
        'role__in'      => array( 'designer' ),
        'status'        => 'all',
        'number'        => -1
    ), false );

    $last_month = nbdl_get_designers( array(
        'date_query'    => array(
            array(
                'year'  => date( 'Y', strtotime( 'last month' ) ),
                'month' => date( 'm', strtotime( 'last month' ) )
            ),
        ),
        'role__in'      => array( 'designer' ),
        'status'        => 'all',
        'number'        => -1
    ), false );

    if ( $from && $to ) {
        $prepared_date = nbdl_prepare_date_query( $from, $to );

        if( is_array( $prepared_date ) ){
            $this_period = nbdl_get_designers(
                array(
                    'date_query' => array(
                        array(
                            'after' => array(
                                'year'  => $prepared_date['from_year'],
                                'month' => $prepared_date['from_month'],
                                'day'   => $prepared_date['from_day']
                            ),
                            'before' => array(
                                'year'  => $prepared_date['to_year'],
                                'month' => $prepared_date['to_month'],
                                'day'   => $prepared_date['to_day']
                            )
                        )
                    ),
                    'role__in'      => array( 'designer' ),
                    'status'        => 'all',
                    'number'        => -1
                ), false
            );

            $last_period = nbdl_get_designers(
                array(
                    'date_query'    => array(
                        array(
                        'after' => array(
                                'year'  => $prepared_date['last_from_year'],
                                'month' => $prepared_date['last_from_month'],
                                'day'   => $prepared_date['last_from_day'],
                            ),
                            'before' => array(
                                'year'  => $prepared_date['last_to_year'],
                                'month' => $prepared_date['last_to_month'],
                                'day'   => $prepared_date['last_to_day'],
                            )
                        )
                    ),
                    'role__in'      => array( 'designer' ),
                    'status'        => 'all',
                    'number'        => -1
                ), false
            );

            $percent = nbdl_get_percentage( $this_period['total'], $last_period['total'] );
        }else{
            $percent = array(
                'value' => '--',
                'class' => ''
            );
            $this_period = null;
        }
    }else{
        $percent = nbdl_get_percentage( $this_month['total'], $last_month['total'] );
    }
    
    return array(
        'inactive'    => $inactive_designers['total'],
        'active'      => $active_designers['total'],
        'this_month'  => $this_month['total'],
        'last_month'  => $last_month['total'],
        'this_period' => $from && $to && $this_period ? $this_period['total'] : null,
        'class'       => $percent['class'],
        'percent'     => $percent['value']
    );
}

function nbdl_get_percentage( $this_period = 0, $last_period = 0 ){

    if( 0 == $this_period || 0 == $last_period ){
        $percent    = '--';
        $class      = '';
    }else{
        $percent    = ( $this_period - $last_period ) / $last_period * 100;
        $percent    = round( $percent, 2 );
        $class      = $percent > 0 ? 'up' : 'down';
    }

    return array(
        'value' => $percent,
        'class' => $class
    );
}

function nbdl_prepare_date_query( $from, $to ) {

    if ( ! $from || ! $to ) {
        return false;
    }

    $from_date     = date_create( $from );
    $raw_from_date = date_create( $from );
    $to_date       = date_create( $to );
    $raw_to_date   = date_create( $to );

    if ( ! $from_date || ! $to_date ) {
        return false;
    }

    $from_year  = $from_date->format( 'Y' );
    $from_month = $from_date->format( 'm' );
    $from_day   = $from_date->format( 'd' );

    $to_year    = $to_date->format( 'Y' );
    $to_month   = $to_date->format( 'm' );
    $to_day     = $to_date->format( 'd' );

    $date_diff      = date_diff( $from_date, $to_date );
    $last_from_date = $from_date->sub( $date_diff );
    $last_to_date   = $to_date->sub( $date_diff );

    $last_from_year  = $last_from_date->format( 'Y' );
    $last_from_month = $last_from_date->format( 'm' );
    $last_from_day   = $last_from_date->format( 'd' );

    $last_to_year    = $last_to_date->format( 'Y' );
    $last_to_month   = $last_to_date->format( 'm' );
    $last_to_day     = $last_to_date->format( 'd' );

    $prepared_data = array(
        'from_year'           => $from_year,
        'from_month'          => $from_month,
        'from_day'            => $from_day,
        'to_year'             => $to_year,
        'to_month'            => $to_month,
        'to_day'              => $to_day,
        'from_full_date'      => $raw_from_date->format( 'Y-m-d' ),
        'to_full_date'        => $raw_to_date->format( 'Y-m-d' ),
        'last_from_year'      => $last_from_year,
        'last_from_month'     => $last_from_month,
        'last_from_day'       => $last_from_day,
        'last_from_full_date' => $last_from_date->format( 'Y-m-d' ),
        'last_to_year'        => $last_to_year,
        'last_to_month'       => $last_to_month,
        'last_to_day'         => $last_to_day,
        'last_to_full_date'   => $last_to_date->format( 'Y-m-d' )
    );

    return $prepared_data;
}

function nbdl_get_summary_sale( $from = null, $to = null, $user_id = null ) {
    $this_month = nbdl_count_sales_in_period( $user_id, array(
        'year'  => date( 'Y' ),
        'month' => date( 'm' )
    ) );

    $last_month = nbdl_count_sales_in_period( $user_id, array(
        'year'  => date( 'Y', strtotime( 'last month' ) ),
        'month' => date( 'm', strtotime( 'last month' ) )
    ) );

    if ( $from && $to ) {
        $prepared_date = nbdl_prepare_date_query( $from, $to );

        if( is_array( $prepared_date ) ){
            $this_period = nbdl_count_sales_in_period( $user_id, array(
                'after' => array(
                    'year'  => $prepared_date['from_year'],
                    'month' => $prepared_date['from_month'],
                    'day'   => $prepared_date['from_day']
                ),
                'before' => array(
                    'year'  => $prepared_date['to_year'],
                    'month' => $prepared_date['to_month'],
                    'day'   => $prepared_date['to_day']
                )
            ) );

            $last_period = nbdl_count_sales_in_period( $user_id, array(
                'after' => array(
                    'year'  => $prepared_date['last_from_year'],
                    'month' => $prepared_date['last_from_month'],
                    'day'   => $prepared_date['last_from_day'],
                ),
                'before' => array(
                    'year'  => $prepared_date['last_to_year'],
                    'month' => $prepared_date['last_to_month'],
                    'day'   => $prepared_date['last_to_day'],
                )
            ) );
        } else {
            $percent = array(
                'value' => '--',
                'class' => ''
            );
            $this_period = null;
        }

        $percent = nbdl_get_percentage( $this_period, $last_period );
    }else{
        $percent = nbdl_get_percentage( $this_month, $last_month );
    }

    return array(
        'this_month'  => $this_month,
        'last_month'  => $last_month,
        'this_period' => $from && $to && $this_period ? $this_period : null,
        'class'       => $percent['class'],
        'percent'     => $percent['value']
    );
}

function nbdl_count_sales_in_period( $user_id = null, $args = array() ){
    global $wpdb;

    $count  = 0;
    $sql    = "SELECT COUNT(oi.order_item_id)";
    $sql   .= " FROM {$wpdb->prefix}woocommerce_order_items oi";
    $sql   .= " INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oim.order_item_id = oi.order_item_id";
    $sql   .= " INNER JOIN $wpdb->posts p ON oi.order_id = p.ID";
    $sql   .= " INNER JOIN {$wpdb->prefix}nbdesigner_templates t ON t.folder = oim.meta_value";
    $sql   .= " WHERE oim.meta_key = '_nbd_design_id' AND p.post_status IN ('wc-on-hold', 'wc-completed', 'wc-processing')";

    if( isset( $args['after'] ) ){
        $after  = sprintf( '%04d-%02d-%02d %02d:%02d:%02d', $args['after']['year'], $args['after']['month'], $args['after']['day'], 0, 0, 0 );
        $before = sprintf( '%04d-%02d-%02d %02d:%02d:%02d', $args['before']['year'], $args['before']['month'], $args['before']['day'], 23, 59, 59 );
        $sql   .= " AND p.post_date > '$after' AND p.post_date <= '$before'";
    }else{
        if( isset( $args['year'] ) ){
            $year = absint( $args['year'] );
            $sql .= " AND YEAR(p.post_date) = $year";
        }
        if( isset( $args['month'] ) ){
            $month  = absint( $args['month'] );
            $sql   .= " AND MONTH(p.post_date) = $month";
        }
        if( isset( $args['day'] ) ){
            $day    = absint( $args['day'] );
            $sql   .= " AND DAYOFMONTH(p.post_date) = $day";
        }
    }

    if( $user_id ){
        $sql .= " AND t.user_id = {$user_id}";
    }

    $result = $wpdb->get_var( $sql );

    if ( $result ) {
        $count = $result;
    }

    return $count;
}

function nbdl_get_sale_status_count( $user_id = null ){
    global $wpdb;

    $sql    = "SELECT COUNT(oi.order_item_id)";
    $sql   .= " FROM {$wpdb->prefix}woocommerce_order_items oi";
    $sql   .= " INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oim.order_item_id = oi.order_item_id";
    $sql   .= " INNER JOIN $wpdb->posts p ON oi.order_id = p.ID";
    $sql   .= " INNER JOIN {$wpdb->prefix}nbdesigner_templates t ON t.folder = oim.meta_value";

    if( $user_id ){
        $sql .= " AND t.user_id = {$user_id}";
    }

    $sql   .= " WHERE oim.meta_key = '_nbd_design_id' AND p.post_status IN ('wc-on-hold', 'wc-completed', 'wc-processing')";

    $data = $wpdb->get_var( $sql );

    return absint( $data );
}

function nbdl_get_design_report( $group_by = 'day', $start_date = '', $end_date = '', $user_id = '' ) {
    global $wpdb;

    $sql    = "SELECT COUNT(id) as total, created_date FROM {$wpdb->prefix}nbdesigner_templates WHERE 1 = 1";
    $sql   .= " AND DATE(created_date) >= '$start_date' AND DATE(created_date) <= '$end_date'";

    if( !empty( $user_id ) ){
        $sql .= " AND user_id = {$user_id}";
    }

    if ( 'day' == $group_by ) {
        $sql    .= ' GROUP BY YEAR(created_date), MONTH(created_date), DAY(created_date)';
    } else {
        $sql    .= ' GROUP BY YEAR(created_date), MONTH(created_date)';
    }

    $data = $wpdb->get_results( $sql );

    return $data;
}

function nbdl_get_sale_report( $group_by = 'day', $start_date = '', $end_date = '', $user_id = '' ) {
    global $wpdb;

    $sql    = "SELECT COUNT(oi.order_item_id) as total, p.post_date as created_date";
    $sql   .= " FROM {$wpdb->prefix}woocommerce_order_items oi";
    $sql   .= " INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim ON oim.order_item_id = oi.order_item_id";
    $sql   .= " INNER JOIN $wpdb->posts p ON oi.order_id = p.ID";
    $sql   .= " INNER JOIN {$wpdb->prefix}nbdesigner_templates t ON t.folder = oim.meta_value";
    $sql   .= " WHERE oim.meta_key = '_nbd_design_id' AND p.post_status IN ('wc-on-hold', 'wc-completed', 'wc-processing')";
    $sql   .= " AND DATE(p.post_date) >= '$start_date' AND DATE(p.post_date) <= '$end_date'";

    if( !empty( $user_id ) ){
        $sql .= " AND user_id = {$user_id}";
    }

    if ( 'day' == $group_by ) {
        $sql    .= ' GROUP BY YEAR(p.post_date), MONTH(p.post_date), DAY(p.post_date)';
    } else {
        $sql    .= ' GROUP BY YEAR(p.post_date), MONTH(p.post_date)';
    }

    $data = $wpdb->get_results( $sql );

    return $data;
}

function nbdl_init_background_process(){
    global $nbdl_processor;
    include_once( NBDESIGNER_PLUGIN_DIR . 'includes/launcher/class.generate.preview.process.php' );
    $nbdl_processor = new NBDL_Generate_Preview_Process();
}
add_action( 'woocommerce_loaded', 'nbdl_init_background_process' );

function nbdl_generate_color_product_design( $approved ){
    if( nbdesigner_get_option( 'nbdesigner_auto_generate_color_product_preview', 'no' ) == 'no' ){
        return;
    }

    global $nbdl_processor;
    
    foreach( $approved as $design_id ){
        $nbdl_processor->push_to_queue( $design_id );
    }
    $nbdl_processor->save()->dispatch();
}