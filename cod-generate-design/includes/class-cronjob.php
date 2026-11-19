<?php
class COD_CronJob {
    private $hook = '';
    
    function __construct() {
        //add_action( 'woocommerce_pre_payment_complete', array( $this, 'woocommerce_pre_payment_complete' ), 100, 1);
        add_action( 'woocommerce_thankyou', array( $this, 'insert_auto_generate' ), 100, 1 );
        add_action( 'woocommerce_payment_complete', array( $this, 'insert_auto_generate_payment_complete' ), 100, 1 );
        //add_action( 'woocommerce_valid_order_statuses_for_payment_complete', array( $this, 'rudr_complete_for_status_processing' ), 100, 1 );
       // add_action( 'woocommerce_payment_complete_order_status_completed', array( $this, 'rudr_complete_for_status_completed' ), 100, 1);


        add_action( 'woocommerce_api_cod_auto_generate', array( $this, 'cod_auto_generate' ) );
        add_action( 'woocommerce_api_cod_auto_delete', array( $this, 'cod_auto_delete' ) );

        add_filter( 'cron_schedules', array( $this, 'add_cron_schedule' ), 80, 1 );
        add_action( 'one_minute_cron_hook', array( $this, 'add_cron_function' ) );

        //Schedule an action if it's not already scheduled
        if ( ! wp_next_scheduled( 'one_minute_cron_hook' ) ) {
            wp_schedule_event( cod_time(), 'every_1_minute', 'one_minute_cron_hook' );
        }
    }

    public function add_cron_schedule( $schedules ) {
        $schedules['every_1_minute'] = array(
            'interval' => MINUTE_IN_SECONDS, // Every 6 hours
            'display'  => __( 'Every 1 minute' ),
        );
        return $schedules;
    }

    public function add_cron_function() {
        $this->cod_auto_generate();
    }

    public function cod_auto_generate() {
        global $wpdb;
        $settings = get_option('cod_generate_settings');
        if( ! empty($settings['nbdesigner_codgf_enable_auto']) && $settings['nbdesigner_codgf_enable_auto'] == 'on' && ! empty($settings['nbdesigner_codgf_time_generate']) ) {
            $mins = absint($settings['nbdesigner_codgf_time_generate']);
            $seconds = $mins * 60;

            $current = cod_time() - $seconds;
            $results = $wpdb->get_results("SELECT *  FROM {$wpdb->prefix}cod_auto_generates WHERE ".cod_time()." > timestamp AND status = 0");

            if( ! empty($results) ) {
                $extra = 0;
                if( ! empty($settings['nbdesigner_codgf_time_clear']) ) {
                    $extra = $settings['nbdesigner_codgf_time_clear']*COD_GENFILE_DELETE; // 86400
                }

                foreach( $results as $result ) {
                    $order = wc_get_order( $result->order_id );
	
                    if ( $order ) {
                        $genFile = new COD_Generate_File($order);
                        $genFile->run();

                        $wpdb->update( $wpdb->prefix . 'cod_auto_generates', array(
                            'status' => 1,
                            'generated_timestamp' => cod_time() + $extra,
                            'generated_at' => date('Y-m-d h:i:s', cod_time() + $extra)
                        ), array(
                            'id' => $result->id
                        ) );
                    }
                }    
            }
        }

        // Only exit when invoked via the WooCommerce API endpoint; WP-Cron must return so subsequent cron events still run.
        if ( doing_action( 'woocommerce_api_cod_auto_generate' ) ) {
            exit();
        }
    }

    public function cod_auto_delete() {
        global $wpdb;
        
        //cod_write_log_order("\n\n\n\ncod_auto_delete"); 

        $settings = get_option('cod_generate_settings');

        if( ! empty($settings['nbdesigner_codgf_time_clear']) ) {
            $dayClear = absint($settings['nbdesigner_codgf_time_clear']);

            $current = cod_time() - ($dayClear*COD_GENFILE_DELETE); // 86400
            $results = $wpdb->get_results("SELECT *  FROM {$wpdb->prefix}cod_auto_generates WHERE ". cod_time() ." > generated_timestamp AND status != 0 ORDER BY id DESC");

            if( ! empty($results) ) {
                //cod_write_log_order($results); 
                foreach( $results as $result ) {
                    $order = wc_get_order( $result->order_id );

                    if ( $order ) {

                        foreach( $order->get_items() AS $order_item_id => $product ) {
                            $nbd_item_key = wc_get_order_item_meta( $order_item_id, '_nbd' );

//cod_write_log_order('nbd_item_key:' . $nbd_item_key .'----order_id: ' . $result->order_id); 

                            $delete = new COD_Delete_File($nbd_item_key, $result->order_id );
                            $delete->run();
                        }
                    }
                } 
            }
        }

        // Only exit when invoked via the WooCommerce API endpoint; WP-Cron must return normally.
        if ( doing_action( 'woocommerce_api_cod_auto_delete' ) ) {
            exit();
        }
    }
    
    public function insert_auto_generate_payment_complete($order_id) {
        $this->hook = 'woocommerce_payment_complete';
        //cod_write_log_order('woocommerce_payment_complete');
        
        $this->insert_auto_generate($order_id);
    }

    public function insert_auto_generate($order_id) {
        global $wpdb;

        $order = wc_get_order($order_id);
        
        if( empty($this->hook) ) {
          // cod_write_log_order('woocommerce_thankyou'); 
        }
        
        
        if( ! empty($order) && $order->get_status() == 'processing' ) {
            //cod_write_log_order('ORDER IS STATUS PROCESSING, ORDER ID: ' . $order_id);
    
            $settings = get_option('cod_generate_settings');
            //cod_write_log_order($settings);
            if( ! empty($settings['nbdesigner_codgf_enable_auto']) && $settings['nbdesigner_codgf_enable_auto'] == 'on' && ! empty($settings['nbdesigner_codgf_time_generate']) ) {
                $mins = absint($settings['nbdesigner_codgf_time_generate']);
                $seconds = $mins * 60;
    
                $products = $order->get_items();
                foreach( $products AS $order_item_id => $product ) {
                    $nbd_item_key = wc_get_order_item_meta( $order_item_id, '_nbd' );
                    
                    if( ! empty($nbd_item_key) ) {
                       // cod_write_log_order($nbd_item_key);
    
                        $check = $wpdb->get_row( $wpdb->prepare(
                            "SELECT * FROM {$wpdb->prefix}cod_auto_generates WHERE order_id = %d AND item_key = %s",
                            $order->get_id(),
                            $nbd_item_key
                        ) );
                        /*
                        cod_write_log_order($wpdb->prepare(
                            "SELECT * FROM {$wpdb->prefix}cod_auto_generates WHERE order_id = %d AND item_key = %s",
                            $order->get_id(),
                            $nbd_item_key
                        ));
                        */
                        //cod_write_log_order($check);
    
                        if( empty($check) ) {
                           // cod_write_log_order('THÊM MỚI');
    
                            // $mins = empty($settings['nbdesigner_codgf_time_generate']) ? 60 : absint($settings['nbdesigner_codgf_time_generate']);
                            // $seconds = $mins * 60;
                
                            $wpdb->insert($wpdb->prefix . 'cod_auto_generates', array(
                                'order_id' => $order->get_id(),
                                'item_key' => $nbd_item_key,
                                'timestamp' => cod_time() + $seconds,
                                'created_at' => date('Y-m-d h:i:s', cod_time() + $seconds)
                            ), array(
                                '%d',
                                '%s',
                                '%d',
                                '%s'
                            ));
                        }
                    }else {
                        //cod_write_log_order('SP THƯỜNG');
                    }
                }
    
                //cod_write_log_order('insert_auto_generate' . $order->get_date_paid());
            }
        }
    }
}

$GLOBALS['cod_cronjob'] = new COD_CronJob();