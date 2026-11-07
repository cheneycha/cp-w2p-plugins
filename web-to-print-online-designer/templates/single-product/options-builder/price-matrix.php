<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbo-price-matrix" <?php if( isset($_REQUEST['wc-api']) && $_REQUEST['wc-api'] == 'NBO_Quick_View' && $nbd_qv_type == '2') echo 'nbd-perfect-scroll'; ?> >
    <table>
        <tbody>
            <?php 
                $number_col = 1; 
                $hoz_fields = array();
                foreach ($options['pm_hoz'] as $key => $hoz_index): 
            ?>
            <tr>
                <?php if( $key == 0 && count($options['pm_ver']) > 0): ?>
                <td class="nbo-pm-empty" rowspan="<?php echo count($options['pm_hoz']); ?>" colspan="<?php echo count($options['pm_ver']); ?>"></td>
                <?php endif; ?>
                <?php 
                    $hoz_field = $options["fields"][$hoz_index];
                    $number_col *= count($hoz_field['general']['attributes']["options"]);
                    $colspan = 1;
                    $looptimes = 1;
                    foreach ($options['pm_hoz'] as $_key => $_hoz_index){
                        $_hoz_field = $options["fields"][$_hoz_index];
                        if( $_key > $key ){
                            $colspan *= count($_hoz_field['general']['attributes']["options"]);
                        }elseif( $_key < $key ){
                            $looptimes *= count($_hoz_field['general']['attributes']["options"]);
                        }
                    }
                    $hoz_fields[] = array(
                        'colspan'   =>  $colspan,
                        'looptimes'  =>   $looptimes,
                        'number_col'   =>  count($hoz_field['general']['attributes']["options"])
                    );
                    for($i = 0; $i < $looptimes; $i++){
                        foreach ($hoz_field['general']['attributes']["options"] as $hoz_key => $hoz_op): 
                ?>
                <th <?php echo 'colspan="'.$colspan.'"' ?>><?php echo $hoz_op['name']; ?></th>
                    <?php endforeach; } ?>
            </tr>
            <?php endforeach; ?>
            <?php
                $number_row = 1;
                $ver_fields = array();
                foreach ($options['pm_ver'] as $ver_index){
                    $ver_field = $options["fields"][$ver_index];
                    $number_row *= count($ver_field['general']['attributes']["options"]);
                }
                for( $i=0; $i < $number_row; $i++ ){ ?>
            <tr>
            <?php foreach ($options['pm_ver'] as $key => $ver_index): ?>
                <?php 
                    $ver_field = $options["fields"][$ver_index];
                    $rowspan = 1;
                    $looptimes = 1;
                    foreach ($options['pm_ver'] as $_key => $_ver_index){
                        $_ver_field = $options["fields"][$_ver_index];
                        if( $_key > $key ){
                            $rowspan *= count($_ver_field['general']['attributes']["options"]);
                        }elseif( $_key < $key ){
                            $looptimes *= count($_ver_field['general']['attributes']["options"]);
                        }
                    }
                    if( !isset($ver_fields[$key]) ){
                        $ver_fields[$key] = array(
                            'rowspan'   =>  $rowspan,
                            'looptimes'  =>   $looptimes,
                            'number_row'   =>  count($ver_field['general']['attributes']["options"])
                        );
                    }
                    if(($i % $rowspan) == 0){
                        $ver_op_index = ($i / $rowspan) % count($ver_field['general']['attributes']["options"]);
                        $ver_op = $ver_field['general']['attributes']["options"][$ver_op_index];
                ?>
                <th <?php echo 'rowspan="'.$rowspan.'"' ?>><?php echo $ver_op['name']; ?></th>
                    <?php } ?>
            <?php endforeach; ?>
            <?php 
                for( $j=0; $j < $number_col; $j++ ){
                    $data_hoz = '';
                    $_h_index = $j;
                    foreach( $hoz_fields as $h_index => $h_field ){
                        $seperate = $h_index > 0 ? ',' : '';
                        $data_hoz .= $seperate . floor( $_h_index / $h_field['colspan'] );
                        $_h_index = $_h_index % $h_field['colspan'];
                    }
                    $data_ver = '';
                    $_v_index = $i;
                    foreach( $ver_fields as $v_index => $v_field ){
                        $seperate = $v_index > 0 ? ',' : '';
                        $data_ver .= $seperate . floor( $_v_index / $v_field['rowspan'] );
                        $_v_index = $_v_index % $v_field['rowspan'];
                    }
            ?>
                <td  ng-click="select_price_matrix(<?php echo $i; ?>, <?php echo $j; ?>)" class="{{options.price_matrix[<?php echo $i; ?>][<?php echo $j; ?>].class}}" title="<?php _e('Choose', 'web-to-print-online-designer'); ?>">
                    <span ng-bind-html="options.price_matrix[<?php echo $i; ?>][<?php echo $j; ?>].price | to_trusted"></span>
                </td>
            <?php } ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>