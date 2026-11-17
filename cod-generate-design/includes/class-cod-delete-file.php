<?php
class COD_Delete_File {
    private $order_id;
    private $item_key;

    function __construct( $item_key, $order_id = 0 ) {
        $this->item_key = $item_key;
        $this->order_id = $order_id;

        if( empty($order_id) ) {
            $result = $this->get_queue( $this->item_key );

            if( ! empty($result) ) {
                $this->order_id  = $result->order_id;
            }
        }
    }

    function run() {
        global $wpdb;
        
        //cod_write_log_order('CLASS DELETE FILE');
        //cod_write_log_order($this->item_key);
        //cod_write_log_order($this->order_id);

        if( ! empty($this->item_key) ) {
            $path = NBDESIGNER_CUSTOMER_DIR .'/'. $this->item_key;
            $this->listDirs($path);

            $this->downloadAll();
        }

        $where = ['item_key' => $this->item_key];
        $where_format = ['%s'];
        if ( ! empty( $this->order_id ) ) {
            $where['order_id'] = $this->order_id;
            $where_format[]    = '%d';
        }

        $wpdb->delete(
            $wpdb->prefix . 'cod_auto_generates',
            $where,
            $where_format
        );

        $wpdb->delete(
            $wpdb->postmeta,
            [
                'post_id' => $this->order_id,
                'meta_key' => 'cod_generate_time'
            ],
            ['%d', '%s'],
        );

        $this->downloadOrder($this->order_id);
    }

    /** 
     * Remove all file
     */
    private function downloadAll() {
        $array = array(
            'pdf',
            'svg',
            'png-hires',
            'jpg-hires'
        );
    
        $path = NBDESIGNER_DOWNLOAD_DIR . '/customer-design-%s-%s.zip';
        foreach( $array as $format ) {
            $pathFile = sprintf( $path, $this->item_key, $format);

            if( file_exists($pathFile) ) {
                unlink($pathFile);
            }
        }
    }

    /**
     * Remove file from folder download
     */
    private function downloadOrder($order_id) {
        $pathFile = sprintf(NBDESIGNER_DOWNLOAD_DIR . '/customer-design-%d.zip', $order_id);
        
        if( file_exists($pathFile) ) {
            unlink($pathFile);
        }
    }

    /**
     * Remove all file from desginer
     */
    private function listDirs($path) {
        $paths = [
            $path . '/customer-pdfs',
            $path . '/svg',
            $path . '/svg_fe'
        ];

        foreach( $paths as $dir ) {
            $this->_deleteDir($dir);
        }
    }

    private function _deleteDir($dir) {
        try {
            $it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new RecursiveIteratorIterator($it,
                        RecursiveIteratorIterator::CHILD_FIRST);
            foreach($files as $file) {
                if ($file->isDir()){
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
    
            rmdir($dir);
        }catch( UnexpectedValueException $e) {
            //cod_write_log( $e->getMessage() );
        }
    }

    private function get_queue( $item_key ) {
        global $wpdb;

        return $wpdb->get_row( $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cod_auto_generates WHERE item_key = %s",
            $item_key
        ));
    }
    
}