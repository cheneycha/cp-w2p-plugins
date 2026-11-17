<?php
class AlT_Log {
    private static $path = '';
    private static $file = '';
    private static $line = '';

    function __construct( $path, $file, $line ) {
        self::$path = $path;
        self::$file = $file;
        self::$line = $line;
    }

    static function error_log($msg, $single) {
        date_default_timezone_set('Asia/Ho_Chi_Minh');

        $file = fopen( self::$path .'/debug.log',"a");

        if( ! $single ) {
            fwrite($file, "\n========================================\n");
            fwrite($file, "Method: " . $_SERVER['REQUEST_METHOD'] . "\n" );
            if( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
                fwrite($file, "Data: " .  print_r( $_GET, true ) );
            }else {
                fwrite($file, "Data: " .  print_r( $_POST, true ) );
            }
            
            fwrite($file, "URL: " .  $_SERVER['REQUEST_URI'] . "\n" );
            fwrite($file, "File: " . self::$file . ", line " . self::$line ."\n" );
            fwrite($file, "Date & Time: " . date('d/m/Y h:i:s A', time()) ."\n" );
            fwrite($file, "IP: " .  self::getIP() . "\n\n" );
        }

        fwrite($file, date('d/m/Y h:i:s A', time()) . ': '.  $msg ."\n");
        fclose($file);
    }
    
    static function write( $log, $single = false )  {
        if ( is_array( $log ) || is_object( $log ) ) {
            self::error_log( print_r( $log, true ), $single );
        } else {
            self::error_log( $log, $single );
        }
    }

    static function getIP() {  
        if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
            $ip = $_SERVER['HTTP_CLIENT_IP'];  
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
        } else{  
            $ip = $_SERVER['REMOTE_ADDR'];  
        }

        return $ip;  
    }
}