<?php
if (!defined('ABSPATH')) exit;

class NBD_Designer {
    public $id              = 0;
    public $data            = null;
    private $stored_data    = array();
    private $changes        = array();
    
    public function __construct( $designer = null ) {
        if ( is_numeric( $designer ) ) {

            $the_user = get_user_by( 'id', $designer );

            if ( $the_user ) {
                $this->id   = $the_user->ID;
                $this->data = $the_user;
            }

        } elseif ( is_a( $designer, 'WP_User' ) ) {
            $this->id   = $designer->ID;
            $this->data = $designer;
        }
    }
    public function __call( $name, $param ) {
        if ( strpos( $name, 'get_' ) === 0 ) {
            $function_name  = str_replace('get_', '', $name );

            if ( empty( $this->stored_data ) ) {
                $this->popluate_stored_data();
            }

            return ! empty( $this->stored_data[$function_name] ) ? $this->stored_data[$function_name] : null;
        }
    }
    public function to_array() {
        $data = array(
            'id'                        => $this->get_id(),
            'create_design'             => $this->get_create_design(),
            'sell_design'               => $this->get_sell_design(),
            'auto_approve_design'       => $this->get_auto_approve_design(),
            'artist_name'               => $this->get_artist_name(),
            'first_name'                => $this->get_first_name(),
            'last_name'                 => $this->get_last_name(),
            'email'                     => $this->get_email(),
            'gravatar'                  => $this->get_avatar(),
            'gravatar_id'               => $this->get_avatar_id(),
            'artist_phone'              => $this->get_artist_phone(),
            'artist_banner'             => $this->get_artist_banner(),
            'artist_banner_id'          => $this->get_artist_banner_id(),
            'artist_address'            => $this->get_artist_address(),
            'artist_facebook'           => $this->get_artist_facebook(),
            'artist_twitter'            => $this->get_artist_twitter(),
            'artist_linkedin'           => $this->get_artist_linkedin(),
            'artist_youtube'            => $this->get_artist_youtube(),
            'artist_instagram'          => $this->get_artist_instagram(),
            'artist_flickr'             => $this->get_artist_flickr(),
            'payment'                   => $this->get_artist_payment(),
            'artist_commission_type'    => $this->get_artist_commission_type(),
            'artist_commission'         => $this->get_artist_commission(),
            'artist_commission_display' => $this->get_artist_commission_display(),
            'artist_commission2'        => $this->get_artist_commission2(),
            'artist_description'        => $this->get_artist_description(),
            'enabled'                   => $this->is_enabled(),
            'featured'                  => $this->is_featured(),
            'registered'                => $this->get_register_date()
        );
        return $data;
    }
    public function get_value( $key ) {
        return ! empty( $key ) ? $key : '';
    }
    public function is_designer() {
        return nbdl_is_designer( $this->id );
    }
    public function is_enabled() {
        return nbdl_is_designer_enabled( $this->id );
    }
    public function is_featured() {
        return 'on' == get_user_meta( $this->id, 'nbd_feature_designer', true );
    }
    public function get_id() {
        return $this->id;
    }
    public function get_name() {
        if ( $this->id ) {
            return $this->get_value( $this->data->display_name );
        }
    }
    public function get_register_date() {
        if ( $this->id ) {
            return $this->get_value( $this->data->user_registered );
        }
    }
    public function get_artist_name() {
        return $this->get_info_part( 'nbd_artist_name' );
    }
    public function get_email() {
        if ( $this->id ) {
            return $this->get_value( $this->data->user_email );
        }
    }
    public function get_first_name() {
        if ( $this->id ) {
            return $this->get_value( $this->data->first_name );
        }
    }
    public function get_last_name() {
        if ( $this->id ) {
            return $this->get_value( $this->data->last_name );
        }
    }
    public function get_artist_phone() {
        return $this->get_info_part( 'nbd_artist_phone' );
    }
    public function get_artist_banner_id() {
        $banner_id = (int) $this->get_info_part( 'nbd_artist_banner' );
        return $banner_id ? $banner_id : 0;
    }
    public function get_artist_banner() {
        $banner_id = $this->get_artist_banner_id();
        return $banner_id ? wp_get_attachment_url( $banner_id ) : '';
    }
    public function get_create_design() {
        return $this->get_info_part( 'nbd_create_design' );
    }
    public function get_sell_design() {
        return $this->get_info_part( 'nbd_sell_design' );
    }
    public function get_auto_approve_design() {
        return $this->get_info_part( 'nbd_auto_approve_design' );
    }
    public function get_artist_address() {
        return $this->get_info_part( 'nbd_artist_address' );
    }
    public function get_artist_twitter() {
        return $this->get_info_part( 'nbd_artist_twitter' );
    }
    public function get_artist_facebook() {
        return $this->get_info_part( 'nbd_artist_facebook' );
    }
    public function get_artist_linkedin() {
        return $this->get_info_part( 'nbd_artist_linkedin' );
    }
    public function get_artist_youtube() {
        return $this->get_info_part( 'nbd_artist_youtube' );
    }
    public function get_artist_instagram() {
        return $this->get_info_part( 'nbd_artist_instagram' );
    }
    public function get_artist_flickr() {
        return $this->get_info_part( 'nbd_artist_flickr' );
    }
    public function get_artist_payment() {
        return $this->get_info_part( 'nbd_payment' );
    }
    public function get_artist_commission_type() {
        return $this->get_info_part( 'nbd_artist_commission_type' );
    }
    public function get_artist_commission_display(){
        $type               = $this->get_info_part( 'nbd_artist_commission_type' );
        $commission         = $this->get_info_part( 'nbd_artist_commission' );
        $commission2        = $this->get_artist_commission2();
        $commission_display = '';
        switch( $type ){
            case 'flat':
                $commission_display = wc_price( $commission );
                break;
            case 'percentage':
                $commission_display = $commission . ' %';
                break;
            case 'combine':
                $commission_display = $commission2[0] . ' % + ' . wc_price( $commission2[1] );
                break;
        }
        return $commission_display;
    }
    public function get_artist_commission() {
        return $this->get_info_part( 'nbd_artist_commission' );
    }
    public function get_artist_commission2() {
        $commission = $this->get_info_part( 'nbd_artist_commission2' );
        return explode('|', $commission);
    }
    public function get_artist_description() {
        return $this->get_info_part( 'nbd_artist_description' );
    }
    public function get_avatar_id() {
        $avatar_id = (int) $this->get_info_part( 'gravatar' );
        return $avatar_id ? $avatar_id : 0;
    }
    public function get_avatar() {
        $avatar_id = $this->get_avatar_id();
        if ( ! $avatar_id && ! empty( $this->data->user_email ) ) {
            return get_avatar_url( $this->data->user_email, 96 );
        }
        return wp_get_attachment_url( $avatar_id );
    }
    public function popluate_stored_data() {
        $defaults = array(
            'nbd_artist_name'               => '',
            'nbd_artist_phone'              => '',
            'nbd_artist_banner'             => 0,
            'nbd_artist_address'            => '',
            'nbd_artist_facebook'           => '',
            'nbd_artist_twitter'            => '',
            'nbd_artist_linkedin'           => '',
            'nbd_artist_youtube'            => '',
            'nbd_artist_instagram'          => '',
            'nbd_artist_flickr'             => '',
            'nbd_artist_commission_type'    => nbdesigner_get_option( 'nbdesigner_commission_type', 'percentage' ),
            'nbd_artist_commission'         => nbdesigner_get_option( 'nbdesigner_default_commission', 0 ),
            'nbd_artist_commission2'        => nbdesigner_get_option( 'nbdesigner_default_commission2', '0|0' ),
            'nbd_artist_description'        => '',
            'nbd_create_design'             => '',
            'nbd_sell_design'               => '',
            'nbd_auto_approve_design'       => '',
            'nbd_payment'                   => '',
            'gravatar'                      => 0
        );
        
        $shop_info = get_user_meta( $this->id, 'nbd_designer_profile', true );

        if( !is_array( $shop_info ) ){
            $shop_info = nbd_get_artist_info( $this->id );
        }

        $shop_info = is_array( $shop_info ) ? $shop_info : array();
        $shop_info = wp_parse_args( $shop_info, $defaults );

        $this->stored_data = $shop_info;
    }
    public function get_store_info() {
        if ( $this->stored_data ) {
            return $this->stored_data;
        }
        $this->popluate_stored_data();
        return $this->stored_data;
    }
    public function get_info_part( $item ) {
        $info = $this->get_store_info();

        if ( array_key_exists( $item, $info ) ) {
            return $info[ $item ];
        }
    }
    public function update_enabled( $status ){
        if( $status == 'on' ){
            $this->update_meta( 'nbd_sell_design', 'on' );
            do_action( 'nbdl_designer_enabled', $this->get_id() );
        } else {
            $this->update_meta( 'nbd_sell_design', '' );
            do_action( 'nbdl_designer_disabled', $this->get_id() );
        }
        return $this->to_array();
    }
    public function update_featured( $status ){
        if( $status == 'on' ){
            $this->update_meta( 'nbd_feature_designer', 'on' );
        } else {
            $this->update_meta( 'nbd_feature_designer', '' );
        }
        return $this->to_array();
    }
    public function update_meta( $key, $value ) {
        update_user_meta( $this->get_id(), $key, wc_clean( $value ) );
    }
    public function update( $data ){
        if ( ! empty( $data['email'] ) ) {

            if ( ! is_email( $data['email'] ) ) {
                return new WP_Error( 'invalid_email', __( 'Email is not valid', 'web-to-print-online-designer' ) );
            }

            wp_update_user(
                array(
                    'ID'         => $this->id,
                    'user_email' => sanitize_email( $data['email'] ),
                )
            );
            $this->changes['email'] = sanitize_email( $data['email'] );
        }
        
        if ( isset( $data['enabled'] ) ){
            $value = ( $data['enabled'] == 'on' ||  $data['enabled'] == true ) ? 'on' : '';
            $this->update_meta( 'nbd_sell_design', $value );
            $this->changes['enabled'] = $value;
        }
        if ( isset( $data['featured'] ) ){
            $value = ( $data['featured'] == 'on' ||  $data['featured'] == true ) ? 'on' : '';
            $this->update_meta( 'nbd_feature_designer', $value );
            $this->changes['featured'] = $value;
        }
        
        $infos = array(
            'artist_name',
            'artist_phone',
            'artist_address',
            'artist_facebook',
            'artist_twitter',
            'artist_linkedin',
            'artist_youtube',
            'artist_instagram',
            'artist_flickr',
            'artist_description',
            'artist_commission_type',
            'artist_commission',
            'artist_commission2',
            'payment',
            'create_design',
            'sell_design',
            'auto_approve_design',
            'artist_banner'
        );
        foreach( $infos as $info ){
            if ( isset( $data[ $info ] ) ){
                $need_update = true;

                if( $info == 'sell_design' && isset( $data['enabled'] ) ){
                    $need_update = false;
                }elseif( $info == 'artist_commission2' ){
                    $value = $data[ $info ][0] . '|' . $data[ $info ][1];
                } elseif( $info == 'artist_banner' && isset( $data['artist_banner_id'] ) ) {
                    $need_update = false;
                }else {
                    $value = $data[ $info ];
                }

                if( $need_update ){
                    $this->update_meta( 'nbd_' . $info, $value );
                    $this->changes[ 'nbd_' . $info ] = $value;
                }
            }
        }
        
        if( isset( $data['artist_banner_id'] ) ){
            $value = absint( $data['artist_banner_id'] );
            $this->update_meta( 'nbd_artist_banner', $value );
            $this->changes['nbd_artist_banner'] = $value;
        }

        if( isset( $data['gravatar_id'] ) ){
            $value = absint( $data['gravatar_id'] );
            $this->update_meta( 'gravatar', $value );
            $this->changes['gravatar'] = $value;
        }
        
        $this->save();
        return $this->id;
    }
    public function save(){
        $this->popluate_stored_data();
        $this->update_meta( 'nbd_designer_profile', array_replace_recursive( $this->stored_data, $this->changes ) );
        $this->changes = [];
    }
    public function get_balance( $formatted = true, $on_date = '' ){
        global $wpdb;

        $status     = nbdl_get_order_status_for_withdraw( true );
        $on_date    = $on_date ? date( 'Y-m-d', strtotime( $on_date ) ) : current_time( 'mysql' );

        $result = $wpdb->get_row( $wpdb->prepare(
            "SELECT SUM(debit) as earnings,
            ( SELECT SUM(credit) FROM {$wpdb->prefix}nbdesigner_balance WHERE user_id = %d AND DATE(balance_date) <= '%s' ) as withdraw
            from {$wpdb->prefix}nbdesigner_balance
            WHERE user_id = '%d' AND DATE(balance_date) <= '%s' AND status IN($status)",
            $this->id, $on_date, $this->id, $on_date
        ));

        $balance = round( (float) $result->earnings - (float) $result->withdraw, wc_get_rounding_precision() );

        return $formatted ? wc_price( $balance ) : $balance;
    }
    public function get_earnings( $formatted = true, $on_date = '' ){
        global $wpdb;

        $status     = nbdl_get_order_status_for_withdraw( true );
        $on_date    = $on_date ? date( 'Y-m-d', strtotime( $on_date ) ) : current_time( 'mysql' );

        $result  = $wpdb->get_row( $wpdb->prepare(
            "SELECT SUM(debit) AS earnings
            FROM {$wpdb->prefix}nbdesigner_balance
            WHERE user_id = %d AND DATE(balance_date) <= %s AND status IN ($status) AND transaction_type = 'new_order'",
            $this->id, $on_date 
        ));

        $earning = (float)$result->earnings;

        return $formatted ? wc_price( $earning ) : $earning;
    }
}