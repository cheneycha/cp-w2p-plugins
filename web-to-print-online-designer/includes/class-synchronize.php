<?php
use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;
use League\Flysystem\Adapter\Ftp as FtpAdapter;
use League\Flysystem\Sftp\SftpAdapter;
use Google\Cloud\Storage\StorageClient;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

include NBDESIGNER_PLUGIN_DIR . 'vendor/autoload.php';

class NBD_Synchronize_Output {
    protected $filesystem = null;
    public $files = array();
    public $root_path = '';

    public function __construct( $place ) {
        $this->get_root_path( $place );
        $function = "init_{$place}_adapter";
        $this->$function();
    }

    public function init_dropbox_adapter(){
        $token  = nbdesigner_get_option( 'nbdesigner_dropbox_token', '' );
        if( $token != '' ){
            $client             = new DropboxClient( $token );
            $adapter            = new DropboxAdapter( $client );
            $this->filesystem   = new Filesystem( $adapter, ['case_sensitive' => false] );
        }
    }

    public function init_ftp_adapter(){
        $host       = nbdesigner_get_option( 'nbdesigner_ftp_host', '' );
        $username   = nbdesigner_get_option( 'nbdesigner_ftp_username', '' );
        $password   = nbdesigner_get_option( 'nbdesigner_ftp_password', '' );
        $passive    = nbdesigner_get_option( 'nbdesigner_ftp_passive_mode', 'no' ) == 'yes' ? true : false;

        $settings   = array(
            'host'      => $host,
            'username'  => $username,
            'password'  => $password
        );

        if( $passive ){
            $settings['passive'] = true;
        }
        
        if( $host != '' && $username != '' ){
            $this->filesystem = new Filesystem(new FtpAdapter( $settings ));
        }
    }

    public function init_sftp_adapter(){
        $host       = nbdesigner_get_option( 'nbdesigner_sftp_host', '' );
        $username   = nbdesigner_get_option( 'nbdesigner_sftp_username', '' );
        $password   = nbdesigner_get_option( 'nbdesigner_sftp_password', '' );
        $privateKey = nbdesigner_get_option( 'nbdesigner_sftp_key', '' );
        $port       = absint( nbdesigner_get_option( 'nbdesigner_sftp_port', 22 ) );

        if( $host != '' ){
            $this->filesystem = new Filesystem(new SftpAdapter([
                'host'          => $host,
                'port'          => $port,
                'username'      => $username,
                'password'      => $password,
                'privateKey'    => $privateKey
            ]));
        }
    }

    public function init_awss3_adapter(){
        $key       = nbdesigner_get_option( 'nbdesigner_awss3_credentials_key', '' );
        $secret    = nbdesigner_get_option( 'nbdesigner_awss3_credentials_secret', '' );
        $region    = nbdesigner_get_option( 'nbdesigner_awss3_region', '' );
        $bucket    = nbdesigner_get_option( 'nbdesigner_awss3_bucket', '' );

        $client = new S3Client([
            'credentials' => [
                'key'    => $key,
                'secret' => $secret,
            ],
            'region'    => $region,
            'version'   => 'latest',
        ]);
        if( $key != '' && $secret != '' && $region != '' && $bucket != '' ){
            $adapter = new AwsS3Adapter( $client, $bucket );
            $this->filesystem = new Filesystem( $adapter );
        }
    }

    public function init_gcs_adapter(){
        $project_id = nbdesigner_get_option( 'nbdesigner_gcs_project_id', '' );
        $bucket     = nbdesigner_get_option( 'nbdesigner_gcs_bucket', '' );
        $jsonKey    = nbdesigner_get_option( 'nbdesigner_gcs_keyfile', '' );

        if( $project_id != '' && $bucket != '' && $jsonKey != '' ){
            $keyFile = json_decode( stripslashes( $jsonKey ), true );
            if( isset( $keyFile['type'] ) ){
                $storageClient = new StorageClient([
                    'projectId' => $project_id,
                    'keyFile'   => $keyFile
                ]);
                $bucket     = $storageClient->bucket( $bucket );
                $adapter    = new GoogleStorageAdapter( $storageClient, $bucket );
                $this->filesystem = new Filesystem( $adapter );
            }
        }
    }

    public function get_root_path( $place ){
        switch( $place ){
            case 'ftp':
                $this->root_path = preg_replace( '/\s+/', '', trim( nbdesigner_get_option( 'nbdesigner_ftp_remote_path', '' ), '/' ) );
                break;
            case 'sftp':
                $this->root_path = preg_replace( '/\s+/', '', trim( nbdesigner_get_option( 'nbdesigner_sftp_remote_path', '' ), '/' ) );
                break;
            case 'dropbox':
                $this->root_path = preg_replace( '/\s+/', '', trim( nbdesigner_get_option( 'nbdesigner_dropbox_directory_path', '' ), '/' ) );
                break;
            case 'awss3':
                $this->root_path = preg_replace( '/\s+/', '', trim( nbdesigner_get_option( 'nbdesigner_awss3_directory_path', '' ), '/' ) );
                break;
            case 'gcs':
                $this->root_path = preg_replace( '/\s+/', '', trim( nbdesigner_get_option( 'nbdesigner_gcs_directory_path', '' ), '/' ) );
                break;
        }
        $this->root_path = $this->root_path != '' ? $this->root_path . '/' : '';
    }

    public function get_upload_files( $nbd_item_key, $order_id, $order_item_id ){
        $path               = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/customer-pdfs';
        if( !file_exists( $path ) ) $path = NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key . '/customer-pdfs-force';
        $destination_path   = $this->root_path . $order_id . '/' . $order_item_id;
        $files              = Nbdesigner_IO::get_list_files_by_type( $path, 1, 'pdf' );
        foreach( $files as $file ){
            $_file  = array(
                'src'   => $file,
                'oid'   => $order_item_id
            );
            if( count( $files ) == 1 ){
                $_file['dst'] = $destination_path . '/design.pdf';
            }else{
                $basename       = pathinfo( $file, PATHINFO_BASENAME );
                $_file['dst']   = $destination_path . '/' . $basename;
            }
            $this->files[] = $_file;
        }
        $this->files = apply_filters( 'nbd_synchronize_files', $this->files, $this->root_path, $nbd_item_key, $order_id, $order_item_id );
    }

    public function upload(){
        if( !is_null( $this->filesystem ) ){
            foreach( $this->files as $file ){
                if( file_exists( $file['src'] ) ){
                    try {
                        $stream = fopen( $file['src'], 'r+' );
                        $result = $this->filesystem->putStream(
                            $file['dst'],
                            $stream,
                            [
                            'visibility' => 'public'
                            ]
                        );
                        if( $result ){
                            wc_update_order_item_meta( $file['oid'], '_nbd_synchronized', true );
                        }
                    } catch (Exception $e) {
                        ob_start();
                        var_dump( $e );
                        error_log( ob_get_clean() );
                    }
                }
            }
        }
    }

    public function is_connected(){
        $file   = NBDESIGNER_DATA_DIR . '/index.html';
        $dst    = $this->root_path . 'test/index.html';
        try {
            $stream = fopen( $file, 'r+' );
            $result = $this->filesystem->putStream(
                $dst,
                $stream,
                [
                'visibility' => 'public'
                ]
            );
            if( $result ){
                return true;
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
}