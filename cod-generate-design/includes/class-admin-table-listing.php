<?php
class COD_Admin_Table_Listing {
    function __construct() {
        add_action('admin_menu', array( $this, 'admin_menu' ) );
        add_action('wp_ajax_cod_cronjob_listing', array( $this, 'table_listing') );

    }

    public function admin_menu() {
        add_submenu_page( 'nbdesigner', 'Generate Listing', 'Generate Listing', 'manage_options', 'cod_gf_page', array( $this, 'page_callback') ); 
    }

    public function table_listing() {
        global $wpdb;
        
        if( isset($_GET['alt']) ) {
            date_default_timezone_set('Asia/Singapore');
        }
        //

		$data = array();
		$totalRow = $wpdb->get_var($this->raw_sql(true));
        $settings = get_option('cod_generate_settings');


		$results = $wpdb->get_results($this->raw_sql());
		if( ! empty($results) ) {
			foreach( $results as $key => $result ) {

                    $_remain_seconds = $result->timestamp - cod_time();

                    $remain_time = $this->second2time($_remain_seconds);
                    if( $_remain_seconds <= 0 ) {
                        $remain_time = $this->second2time(cod_time() - $result->timestamp) .' ago';
                        if( empty($result->status) ) {
                            $remain_time = 'Generating, please wait...';
                        }
                    }
         

                if( ! empty($settings['nbdesigner_codgf_time_clear']) ) {
                    $_remain_seconds = $result->generated_timestamp - cod_time();

                    $remain_time_delete = $this->second2time($_remain_seconds);
                    if( $_remain_seconds <= 0 ) {
                        $remain_time_delete = $this->second2time(cod_time() - $result->generated_timestampy) .' ago';
                        if( ! empty($result->status) ) {
                            $remain_time_delete = 'Deleting, please wait...';
                        }
                    }
                }

                

                $y = date('Y-m-d h:i:s', cod_time() );

                $status = '<img src="data:image/gif;base64,R0lGODlhKwALAPEAAP///4iIiMTExIiIiCH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAKwALAAACMoSOCMuW2diD88UKG95W88uF4DaGWFmhZid93pq+pwxnLUnXh8ou+sSz+T64oCAyTBUAACH5BAkKAAAALAAAAAArAAsAAAI9xI4IyyAPYWOxmoTHrHzzmGHe94xkmJifyqFKQ0pwLLgHa82xrekkDrIBZRQab1jyfY7KTtPimixiUsevAAAh+QQJCgAAACwAAAAAKwALAAACPYSOCMswD2FjqZpqW9xv4g8KE7d54XmMpNSgqLoOpgvC60xjNonnyc7p+VKamKw1zDCMR8rp8pksYlKorgAAIfkECQoAAAAsAAAAACsACwAAAkCEjgjLltnYmJS6Bxt+sfq5ZUyoNJ9HHlEqdCfFrqn7DrE2m7Wdj/2y45FkQ13t5itKdshFExC8YCLOEBX6AhQAADsAAAAAAAAAAAA=" width="43" height="11">';
                if( $result->status == 1 ) {
                    $status = 'Completed';
                }if( $result->status == 2 ) {
                    $status = 'Manually';
                    $remain_time = 'Never';
                }

                

				$data[] = [
                    'spacer' => '',
					'order_id' => sprintf('<a href="%1$spost.php?post=%2$d&action=edit" target="_blank" data-status="%3$d">%2$d</a>', admin_url(), $result->order_id, $result->status),
                    'status' => ($result->status == 0) ? $status : sprintf('<mark class="trans-status status-%s"><span>%s</span></mark>', strtolower($status), $status),
                    'item_key' => sprintf('<a href="%1$sadmin.php?page=nbdesigner_detail_order&nbd_item_key=%2$s&order_id=%3$d" target="_blank" data-current-date="%4$s">%2$s</a>', admin_url(), $result->item_key, $result->order_id, date('Y-m-d h:i:s', cod_time())),
                    'gemerate_time' => '<div>'. date('Y-m-d H:i:s', $result->timestamp) .'</div><div>('. $remain_time .')</div>',
                    'delete_time' => empty($result->generated_timestamp) ? 'Not ready!' : '<div>'. date('Y-m-d H:i:s', $result->generated_timestamp) .'</div><div>('. $remain_time_delete .')</div>'
				];
			}
		}

		wp_send_json( array(
			'draw' => absint($_POST['draw']),
			'recordsTotal' => absint($totalRow),
			'recordsFiltered' => absint($totalRow),
			'data' => $data
		) );
    }

    function raw_sql($count = false) {
		global $wpdb;

		$query = array();

		$query['select'] = "SELECT * FROM {$wpdb->prefix}cod_auto_generates";
		if( $count ) {
			$query['select'] = "SELECT COUNT(*) FROM {$wpdb->prefix}cod_auto_generates";
		}

		$query['where'] = 'WHERE 1=1';

		if( ! empty($_POST['search']['value']) ) {
			$keyword = sanitize_text_field($_POST['search']['value']);
			$query['where'] = "WHERE order_id = '{$keyword}' OR item_key = '{$keyword}'";
		}

		if( ! empty($_POST['network']) ) {
			$network = sanitize_text_field($_POST['network']);
			$query['where'] .= " AND network_id = '{$network}'";
		}

		if( ! empty($_POST['domain']) ) {
			$domain = sanitize_text_field($_POST['domain']);
			$query['where'] .= " AND link LIKE '%{$domain}%'";
		}

		$query['orderby'] = "ORDER BY id DESC";

		if( ! $count ) {
			$start = absint($_POST['start']);
			$limit = absint($_POST['length']);

			$query['limit'] = "LIMIT {$limit} OFFSET " . $start;
		}

		return implode(" ", $query);
	}

    public function page_callback() {
   
        ?>
        <div class="table-wrapper">
            <table id="table-cronjob-listing" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th style="width: 100px;">Order ID</th>
                        <th style="width: 100px;">Item Key</th>
                        <th>&nbsp;</th>
                        <th style="width: 250px; text-align: left; padding-left: 10px;">Scheduled Generate Date</th>
                        <th style="width: 200px; text-align: left; padding-left: 10px;">Status</th>
                        <th style="width: 200px; text-align: left; padding-left: 10px;">Scheduled Delete Date</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <?php $settings = get_option('cod_generate_settings');
        if( ! empty($settings) ) { ?>
        <h4 style="margin-bottom: 0;">Note:</h4>
        <ul style="margin-top: 5px;">
            <?php
            if( ! empty($settings['nbdesigner_codgf_enable_auto']) && $settings['nbdesigner_codgf_enable_auto'] == 'on' ) {
                printf('<li>Time auto generate: %s minutes.</li>', sprintf("%02d", $settings['nbdesigner_codgf_time_generate']));
            }
            
            ?>
            <li>Time auto delete: <?php echo empty($settings['nbdesigner_codgf_time_clear']) ? 'Never' : sprintf("%02d", $settings['nbdesigner_codgf_time_clear']);?> minutes</li>
            <li>Time zone UTC+<?php echo get_option('gmt_offset');?></li>
        </ul>
        <?php }?>

        <style>
            #table-cronjob-listing tbody td:nth-child(1),
            #table-cronjob-listing tbody td:nth-child(2) {
                text-align: center;
            }
        .trans-status {
            display: inline-flex;
            line-height: 2.5em;
            color: #777;
            background: #e5e5e5;
            border-radius: 4px;
            border-bottom: 1px solid rgba(0,0,0,.05);
            margin: -0.25em 0;
            cursor: inherit!important;
            white-space: nowrap;
            max-width: 100%;
        }

        .trans-status.status-completed {
            background: #c8d7e1;
            color: #2e4453;
        }

        .trans-status>span {
            margin: 0 1em;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        /* .trans-status.status-manually {
            background: #c6e1c6;
            color: #5b841b;
        } */

        .lds-facebook {
            display: inline-block;
            position: relative;
            width: 80px;
            height: 28px;
        }
        .lds-facebook div {
            display: inline-block;
            position: absolute;
            left: 8px;
            width: 16px;
            background: #3b50b4;
            animation: lds-facebook 1.2s cubic-bezier(0, 0.5, 0.5, 1) infinite;
        }
            .lds-facebook div:nth-child(1) {
            left: 8px;
            animation-delay: -0.24s;
            }
            .lds-facebook div:nth-child(2) {
            left: 32px;
            animation-delay: -0.12s;
            }
            .lds-facebook div:nth-child(3) {
            left: 56px;
            animation-delay: 0;
            }
        @keyframes lds-facebook {
            0% {
                top: 8px;
                height: 24px;
            }
            50%, 100% {
                top: 24px;
                height: 12px;
            }
        }

        </style>
        <?php
    }

    private function second2time($_remain_seconds) {
        $hours = gmdate("H", $_remain_seconds);
        $minutes = gmdate("i", $_remain_seconds);
        $seconds = gmdate("s", $_remain_seconds);

        $remain_time = '';
        if( $hours == '00' ) {
            $remain_time = '';
        }else {
            $remain_time .= sprintf('%s %s ', $hours, $this->add_s($hours, 'hour'));
        }

        if( $minutes == '00' ) {
            $remain_time .= '';
        }else {
            $remain_time .= sprintf('%s %s ', $minutes, $this->add_s($minutes, 'minute'));
        }

        if( $seconds == '00' ) {
            $remain_time .= '';
        }else {
            $remain_time .= sprintf('%s %s ', $seconds, $this->add_s($seconds, 'second'));
        }

        return trim($remain_time);

    }

    private function add_s( $number, $string ) {
        if( $number != '01' ) {
            $string .= 's';
        }

        return $string;
    }
}

new COD_Admin_Table_Listing();