<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class NBD_Export_PDF_Process extends WP_Background_Process {
    protected $action = 'nbd_export_pdf';

    public function __construct() {
        parent::__construct();
    }

    protected function task( $order_id ) {
        try{
            $order  = wc_get_order( $order_id );
            if( $order ){
                $order_items    = $order->get_items();

                foreach( $order_items AS $order_item_id => $order_item ){
                    $nbd_item_key = wc_get_order_item_meta( $order_item_id, '_nbd' );
                    if( $nbd_item_key ){
                        $extra = array(
                            'order_id'      => $order_id,
                            'order_item_id' => $order_item_id
                        );
                        nbd_export_pdfs( $nbd_item_key, false, false, 'no', $extra );
                        do_action( 'nbd_synchronize_output', $nbd_item_key, $order_id, $order_item_id );
                    }
                }
            }
        }catch( Exception $e ){

        }

        return false;
    }

    protected function complete() {
        parent::complete();
    }
}