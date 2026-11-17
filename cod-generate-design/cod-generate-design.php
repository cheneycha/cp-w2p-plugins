<?php
/*
Plugin Name: COD Generate File PHP 8.3
Plugin URI: https://codteam.net
Description: Bulk action generate, auto generate & delete files after generate.
Version: 1.1.1
Requires PHP:      8.3
Author: CODTeam
Author URI: https://codteam.net
License: GPLv2 or later
Text Domain: akismet
*/

// Make sure we don't expose any info if called directly
if ( ! function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'COD_GENFILE_VERSION', '1.0.3' );
define( 'COD_GENFILE_DIR', plugin_dir_path( __FILE__ ) );
define( 'COD_GENFILE_URL', plugin_dir_url( __FILE__ ) );
define( 'COD_GENFILE_DELETE', 60 );

if ( ! class_exists( 'COD_GF_Design' ) ) {
	class COD_GF_Design {
		static $bulk_label = 'Accept & Generate Files';

		private function __construct() {}

		static function init_actions() {
			self::include_files();

			add_action( 'admin_head', array( __CLASS__, 'embed_css' ) );
			add_action( 'admin_init', array( __CLASS__, 'save_generate_time_detail_order' ) );
			add_action( 'current_screen', function() {
    			global $title;
    			if ( is_null( $title ) ) {
        			$title = '';
    			}
			}, 0 );
			add_filter( 'bulk_actions-edit-shop_order', array( __CLASS__, 'define_bulk_actions' ), 999, 1 );
			add_filter( 'handle_bulk_actions-edit-shop_order', array( __CLASS__, 'handle_bulk_actions' ), 999, 3 );
			add_filter( 'esc_html', array( __CLASS__, 'esc_html' ), 99, 2 );

			add_filter( 'nbdesigner_settings_blocks', array( __CLASS__, 'add_settings_block' ) );
			add_filter( 'nbdesigner_settings_options', array( __CLASS__, 'add_settings_field' ) );
			add_action( 'nbdesigner_save_options', array( __CLASS__, 'save_settings' ) );

			add_action( 'cod_box_order_metadata', array( __CLASS__, 'box_order_metadata' ), 99, 2 );
			add_action( 'wp_ajax_cod_gdf_order_detail', array( __CLASS__, 'generate_file_order_detail' ) );
			add_action( 'wp_ajax_cod_gdf_order_detail_download', array( __CLASS__, 'download_order_detail_all' ) );
			add_action( 'wp_ajax_cod_gdf_order_detail_download_type', array( __CLASS__, 'download_order_detail_by_format' ) );
			add_action( 'wp_ajax_cod_gdf_generate_all', array( __CLASS__, 'bulk_generate_all' ) );
			
			add_action( 'after_nbd_save_customer_design', array( __CLASS__, 'nbd_save_customer_design' ), 99, 1 );

			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_admin_script' ) );
		}

		static function embed_css() {
			?>
			<style>
				.bulkactions.has-loading {
					display: flex;
    				align-items: center;
				}
				.bulkactions .alt-bulk_status-generate {
					font-weight: 600;
					display: flex;
					height: 30px;
					align-items: center;
				}
				.bulkactions .alt-bulk_status-generate img {
					margin-left: 10px;
				}
				.cod-generate-time-detail:before {
					display: block;
					margin: 10px 0;
					content: '-------';
				}
			</style>
			<?php
		}

		static function save_generate_time_detail_order() {
			if ( ! empty( $_GET['page'] ) && $_GET['page'] === 'nbdesigner_detail_order' && isset( $_GET['download-type'], $_GET['order_id'], $_GET['nbd_item_key'] ) ) {
				global $wpdb;

				$order_id = absint( $_GET['order_id'] );
				$type     = wc_clean( $_GET['download-type'] );
				$item_key = wc_clean( $_GET['nbd_item_key'] );

				if ( empty( $item_key ) || empty( $order_id ) ) {
					return;
				}

				$settings = get_option( 'cod_generate_settings' );
				if ( ! empty( $settings['nbdesigner_codgf_enable_auto'] ) && ! empty( $settings['nbdesigner_codgf_time_clear'] ) ) {
					$days    = absint( $settings['nbdesigner_codgf_time_clear'] );
					$seconds = $days * COD_GENFILE_DELETE;

					$table_name = $wpdb->prefix . 'cod_auto_generates';
					$result     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE item_key = %s", $item_key ) );

					$data = get_post_meta( $order_id, 'cod_generate_time', true );
					if ( ! is_array( $data ) ) {
						$data = array();
					}
					if ( ! isset( $data[ $item_key ] ) || ! is_array( $data[ $item_key ] ) ) {
						$data[ $item_key ] = array();
					}
					$current_time = function_exists( 'cod_time' ) ? cod_time() : time();
					$data[ $item_key ][ $type ] = $current_time;

					update_post_meta( $order_id, 'cod_generate_time', $data );

					if ( empty( $result ) ) {
						$wpdb->insert(
							$table_name,
							array(
								'status'              => 2,
								'order_id'            => $order_id,
								'item_key'            => $item_key,
								'timestamp'           => $current_time,
								'created_at'          => date( 'Y-m-d H:i:s', $current_time ),
								'generated_timestamp' => $current_time + $seconds,
								'generated_at'        => date( 'Y-m-d H:i:s', $current_time + $seconds ),
							),
							array( '%d', '%d', '%s', '%d', '%s', '%d', '%s' )
						);
					} else {
						$wpdb->update(
							$table_name,
							array(
								'status'              => 2,
								'generated_timestamp' => $current_time + $seconds,
								'generated_at'        => date( 'Y-m-d H:i:s', $current_time + $seconds ),
							),
							array( 'item_key' => $item_key ),
							array( '%d', '%d', '%s' ),
							array( '%s' )
						);
					}
				}
			}
		}

		static function add_settings_block( $settings ) {
			$settings['output']['extra-settings'] = esc_html__( 'Extra settings', 'web-to-print-online-designer' );
			return $settings;
		}

		static function add_settings_field( $options ) {
			$options['extra-settings'] = function_exists( 'cod_extra_settings' ) ? cod_extra_settings() : array();
			return $options;
		}

		static function save_settings( $current_tab ) {
			if ( $current_tab === 'output' && function_exists( 'cod_extra_settings' ) ) {
				$data = array();
				foreach ( cod_extra_settings() as $setting ) {
					$setting_id = $setting['id'] ?? '';
					if ( $setting_id && isset( $_POST[ $setting_id ] ) ) {
						$data[ $setting_id ] = wc_clean( $_POST[ $setting_id ] );
					}
				}
				update_option( 'cod_generate_settings', $data );
			}
		}

		private static function include_files() {
			require_once COD_GENFILE_DIR . '/includes/class-log.php';
			require_once COD_GENFILE_DIR . '/includes/helpers.php';
			require_once COD_GENFILE_DIR . '/includes/class-cod-generate-file.php';
			require_once COD_GENFILE_DIR . '/includes/class-cod-delete-file.php';
			require_once COD_GENFILE_DIR . '/includes/class-cronjob.php';
			require_once COD_GENFILE_DIR . '/includes/class-admin-table-listing.php';
		}

		static function define_bulk_actions( $actions ) {
			$actions['generate_files'] = self::$bulk_label;
			return $actions;
		}

		static function bulk_generate_all() {
			$json = array();
			$ids  = isset( $_POST['ids'] ) ? (array) wc_clean( $_POST['ids'] ) : array();

			if ( ! empty( $ids ) ) {
				$changed = 0;
				foreach ( $ids as $id ) {
					$order = wc_get_order( absint( $id ) );
					if ( $order && class_exists( 'COD_Generate_File' ) ) {
						$genFile = new COD_Generate_File( $order );
						$genFile->run();
						$changed++;
					}
				}

				if ( ! empty( $changed ) ) {
					$json['complete'] = true;
				}
			} else {
				$json['message'] = 'Please choose order to generate.';
			}

			wp_send_json( $json );
		}

		static function handle_bulk_actions( $redirect_to, $action, $ids ) {
			if ( $action === 'generate_files' && ! empty( $ids ) && is_array( $ids ) ) {
				$changed = 0;
				foreach ( $ids as $id ) {
					$order = wc_get_order( absint( $id ) );
					if ( $order && class_exists( 'COD_Generate_File' ) ) {
						$genFile = new COD_Generate_File( $order );
						$genFile->run();
						$changed++;
					}
				}

				if ( $changed ) {
					$redirect_to = add_query_arg(
						array(
							'post_type' => 'shop_order',
							'changed'   => $changed,
							'ids'       => join( ',', array_map( 'absint', $ids ) ),
						),
						$redirect_to
					);
				}
			}

			return esc_url_raw( $redirect_to );
		}

		static function esc_html( $safe_text, $text ) {
			if ( isset( $_GET['page'] ) && $_GET['page'] === 'nbdesigner_detail_order' && $safe_text === 'Download' ) {
				$safe_text = 'Generate & Download';
			}
			return $safe_text;
		}

		static function box_order_metadata( $products, $order_id ) {
			$order_id = absint( $order_id );
			printf(
				'<button class="button button-small button-secondary" id="button-generate-order-detail" data-id="%d">%s</button>',
				$order_id,
				'Generate All'
			);

			$filePDF = '';
			if ( ! empty( $products ) && is_iterable( $products ) && defined( 'NBDESIGNER_CUSTOMER_DIR' ) ) {
				foreach ( $products as $order_item_id => $product ) {
					$nbd_item_key = wc_get_order_item_meta( $order_item_id, '_nbd' );
					if ( ! empty( $nbd_item_key ) ) {
						$check_file = sprintf( '%1$s/%2$s/customer-pdfs/%2$s_0.pdf', NBDESIGNER_CUSTOMER_DIR, $nbd_item_key );
						if ( file_exists( $check_file ) ) {
							$filePDF = $check_file;
							break;
						}
					}
				}
			}

			if ( ! empty( $filePDF ) ) {
				printf(
					'<button class="button button-small button-secondary" id="button-download-order-detail" data-id="%d">%s</button>',
					$order_id,
					'Download All'
				);
				echo '<style>.nbdesigner-right.nbd-admin-setting-padding-5 a.button-secondary { display: none; }</style>';
			}

			echo '<style>
			.nbdesigner-right.nbd-admin-setting-padding-5 { display: flex; justify-content: space-between; width: 100%; }
			#button-generate-order-detail { margin-left: 5px; display: inline-flex; align-items: center; }
			#button-generate-order-detail img { margin-left: 10px; }
			</style>';
		}

		static function generate_file_order_detail() {
			$json = array();
			$id   = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

			$order = wc_get_order( $id );
			if ( $order && class_exists( 'COD_Generate_File' ) ) {
				$genFile = new COD_Generate_File( $order );
				$result  = $genFile->run();

				// Fixed: Safe check for PHP 8.3 avoiding "offset on null"
				if ( is_array( $result ) && ! empty( $result['success'] ) ) {
					$json['complete'] = true;
				}
			}

			wp_send_json( $json );
		}

		static function download_order_detail_all() {
			$json           = array();
			$id             = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
			$order_item_ids = isset( $_POST['files'] ) ? (array) $_POST['files'] : array();
			
			$order = wc_get_order( $id );
			if ( $order && defined( 'NBDESIGNER_CUSTOMER_DIR' ) ) {
				$files    = array();
				$products = $order->get_items();
				foreach ( $products as $order_item_id => $product ) {
					$nbd_item_key = wc_get_order_item_meta( $order_item_id, '_nbd' );
		
					if ( ! empty( $nbd_item_key ) ) {
						$path        = sprintf( '%1$s/%2$s/customer-pdfs', NBDESIGNER_CUSTOMER_DIR, $nbd_item_key );
						$list_images = class_exists( 'Nbdesigner_IO' ) ? Nbdesigner_IO::get_list_files_by_type( $path, 1, 'png' ) : array();
							
						if ( empty( $list_images ) && class_exists( 'Nbdesigner_IO' ) ) {
							$list_images = Nbdesigner_IO::get_list_images( NBDESIGNER_CUSTOMER_DIR . '/' . $nbd_item_key, 1 );
						}

						if ( ! empty( $list_images ) && is_array( $list_images ) ) {
							foreach ( $list_images as $image ) {
								$files[] = $image;
							}
						}
					}
				}

				if ( ! empty( $files ) && function_exists( 'cod_zip_files' ) && defined( 'NBDESIGNER_DATA_DIR' ) && defined( 'NBDESIGNER_DATA_URL' ) ) {
					$pathZip = NBDESIGNER_DATA_DIR . '/download/customer-design-' . $order->get_id() . '.zip';
					$nameZip = 'customer-design-' . $order->get_id() . '.zip';

					cod_zip_files( $files, $pathZip, $nameZip );

					$json['complete'] = true;
					$json['url']      = NBDESIGNER_DATA_URL . '/download/' . $nameZip;
				}
			}

			wp_send_json( $json );
		}

		static function download_order_detail_by_format() {
			$json     = array();
			$files    = array();
			$id       = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;
			$type     = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : '';
			$item_key = isset( $_POST['item_key'] ) ? sanitize_text_field( $_POST['item_key'] ) : '';

			$order = wc_get_order( $id );
			if ( $order && $item_key && defined( 'NBDESIGNER_CUSTOMER_DIR' ) ) {
				switch ( $type ) {
					case 'PDF':
						$svg_path = NBDESIGNER_CUSTOMER_DIR . '/' . $item_key . '/customer-pdfs';
						$files    = class_exists( 'Nbdesigner_IO' ) ? Nbdesigner_IO::get_list_files_by_type( $svg_path, 1, 'pdf' ) : array();
						break;
					case 'PNG':
						$svg_path = NBDESIGNER_CUSTOMER_DIR . '/' . $item_key . '/customer-pdfs';
						$files    = class_exists( 'Nbdesigner_IO' ) ? Nbdesigner_IO::get_list_files_by_type( $svg_path, 1, 'png' ) : array();
						break;
					case 'JPG':
						$svg_path = NBDESIGNER_CUSTOMER_DIR . '/' . $item_key . '/customer-pdfs';
						$files    = class_exists( 'Nbdesigner_IO' ) ? Nbdesigner_IO::get_list_files_by_type( $svg_path, 1, 'jpg|jpeg' ) : array();
						break;
					case 'SVG':
						$json_file = NBDESIGNER_CUSTOMER_DIR . '/' . $item_key . '/product.json';
						$product_data = null;
						if ( file_exists( $json_file ) ) {
							$file_raw = file_get_contents( $json_file );
							$product_data = maybe_unserialize( $file_raw );
							if ( ! is_array( $product_data ) ) {
								$product_data = json_decode( $file_raw, true );
							}
						}
						$path        = NBDESIGNER_CUSTOMER_DIR . '/' . $item_key;
						$svg_path    = $path . '/svg';
						$svg_path_fe = $path . '/svg_fe/';

						$files = class_exists( 'Nbdesigner_IO' ) ? Nbdesigner_IO::get_list_files_by_type( $svg_path, 1, 'svg' ) : array();
						if ( ! empty( $product_data ) && is_array( $product_data ) ) {
							foreach ( $product_data as $key => $value ) {
								$file_name         = 'frame_' . $key . '_svg.svg';
								$file_name_convert = 'frame_cv_' . $key . '_svg.svg';

								if ( isset( $value['include_background'] ) && (int) $value['include_background'] === 1 ) {
									$files[ $key ] = $svg_path_fe . $file_name_convert;
								} else {
									$files[ $key ] = $svg_path . '/' . $file_name;
								}
							}
						}
						break;
					default:
						break;
				}

				if ( ! empty( $files ) && function_exists( 'cod_zip_files' ) && defined( 'NBDESIGNER_DATA_DIR' ) && defined( 'NBDESIGNER_DATA_URL' ) ) {
					$nameZip = 'customer-design-' . $item_key . '-' . strtolower( $type ) . '.zip';
					$pathZip = NBDESIGNER_DATA_DIR . '/download/' . $nameZip;

					cod_zip_files( $files, $pathZip, $nameZip );

					$json['complete'] = true;
					$json['url']      = NBDESIGNER_DATA_URL . '/download/' . $nameZip;
				}
			}

			wp_send_json( $json );
		}

		static function nbd_save_customer_design( $result ) {
			if ( ! empty( $_SERVER['HTTP_REFERER'] ) ) {
				$query = parse_url( $_SERVER['HTTP_REFERER'], PHP_URL_QUERY );
				if ( ! empty( $query ) ) {
					parse_str( $query, $params );

					if ( ( ! empty( $params['order_id'] ) || ! empty( $params['oid'] ) ) && ! empty( $result['folder'] ) && class_exists( 'COD_Delete_File' ) ) {
						$order_id = empty( $params['oid'] ) ? absint( $params['order_id'] ) : absint( $params['oid'] );
						$item_key = sanitize_text_field( $result['folder'] );

						$delete = new COD_Delete_File( $item_key, $order_id );
						$delete->run();
					}
				}
			}
		}

		static function enqueue_admin_script() {
			global $wpdb;

			wp_enqueue_style( 'datatables', COD_GENFILE_URL . '/assets/css/datatables.min.css', false, '1.0.0' );
			wp_enqueue_style( 'style', COD_GENFILE_URL . '/assets/css/admin.css', false, '1.0.0' );

			wp_enqueue_script( 'datatables', COD_GENFILE_URL . '/assets/js/datatables.min.js', array( 'jquery' ), '1.0', true );
			wp_enqueue_script( 'cod-generate', COD_GENFILE_URL . '/assets/js/admin.js', array( 'jquery', 'datatables' ), '1.0', true );

			$args = array(
				'ajax_url'     => admin_url( 'admin-ajax.php' ),
				'path_loading' => COD_GENFILE_URL . '/assets/img/loading4.gif',
			);

			if ( isset( $_GET['page'] ) && $_GET['page'] === 'nbdesigner_detail_order' && ! empty( $_GET['order_id'] ) && ! empty( $_GET['nbd_item_key'] ) ) {
				$item_key      = sanitize_text_field( $_GET['nbd_item_key'] );
				$order_id      = absint( $_GET['order_id'] );
				$generate_time = get_post_meta( $order_id, 'cod_generate_time', true );
				$settings      = get_option( 'cod_generate_settings' );

				if ( ! empty( $generate_time ) && is_array( $generate_time ) && ! empty( $generate_time[ $item_key ] ) && is_array( $generate_time[ $item_key ] ) ) {
					$timestamps = array();
					foreach ( $generate_time[ $item_key ] as $time_key => $time ) {
						$timestamps[ $time_key ] = date( 'd/m/Y H:i:s', $time );
					}

					if ( isset( $settings['nbdesigner_codgf_enable_png'] ) && $settings['nbdesigner_codgf_enable_png'] === 'off' && isset( $timestamps['png-hires'] ) ) {
						unset( $timestamps['png-hires'] );
					}

					if ( isset( $settings['nbdesigner_codgf_enable_jpg'] ) && $settings['nbdesigner_codgf_enable_jpg'] === 'off' && isset( $timestamps['jpg-hires'] ) ) {
						unset( $timestamps['jpg-hires'] );
					}

					$args['cod_generate'] = $timestamps;
					$args['order_id']     = $order_id;
					$args['item_key']     = $item_key;

					$table_name = $wpdb->prefix . 'cod_auto_generates';
					$result     = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE item_key = %s AND status = %d", $args['item_key'], 0 ) );

					if ( empty( $result ) ) {
						$args['disable_generate'] = true;
					}
				}
			}
			wp_localize_script( 'cod-generate', 'cod_gdf', $args );
		}

		static function install() {
			global $wpdb;

			$collate = '';
			if ( $wpdb->has_cap( 'collation' ) ) {
				$collate = $wpdb->get_charset_collate();
			}

			$table_name = $wpdb->prefix . 'cod_auto_generates';
			$tables     = "
			CREATE TABLE {$table_name} (
				id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
				status INT(5) NOT NULL DEFAULT 0,
				order_id INT(5) NOT NULL,
				item_key VARCHAR(50) NOT NULL,
				timestamp INT(15) NOT NULL,
				generated_timestamp INT(15) NOT NULL,
				generated_at TIMESTAMP NULL,
				created_at TIMESTAMP NOT NULL,
				PRIMARY KEY  (id)
			) $collate;";

			if ( ! $wpdb->get_var( "SHOW TABLES LIKE '{$table_name}';" ) ) {
				$wpdb->hide_errors();
				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $tables );
			}
		}
	}

	add_action( 'plugins_loaded', array( 'COD_GF_Design', 'init_actions' ) );
	register_activation_hook( __FILE__, array( 'COD_GF_Design', 'install' ) );
}

function cod_error_log( $msg ) {
	if ( ! defined( 'COD_GENFILE_DIR' ) ) return;
	$file = @fopen( COD_GENFILE_DIR . '/debug.log', 'a' );
	if ( $file ) {
		fwrite( $file, date( 'd/m/Y H:i:s', time() ) . ': ' . $msg . "\n" );
		fclose( $file );
	}
}

function cod_write_log( $log ) {
	if ( is_array( $log ) || is_object( $log ) ) {
		cod_error_log( print_r( $log, true ) );
	} else {
		cod_error_log( $log );
	}
}

function cod_error_log_order( $msg ) {
	if ( ! defined( 'COD_GENFILE_DIR' ) ) return;
	$file = @fopen( COD_GENFILE_DIR . '/wc.log', 'a' );
	if ( $file ) {
		fwrite( $file, date( 'd/m/Y H:i:s', time() ) . ': ' . $msg . "\n" );
		fclose( $file );
	}
}

function cod_write_log_order( $log ) {
	if ( is_array( $log ) || is_object( $log ) ) {
		cod_error_log_order( print_r( $log, true ) );
	} else {
		cod_error_log_order( $log );
	}
}