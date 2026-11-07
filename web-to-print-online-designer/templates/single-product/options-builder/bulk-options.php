<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<p class="nbo-bulk-title"><b><?php _e('Bulk variation', 'web-to-print-online-designer'); ?></b></p>
<div class="nbo-bulk-variation-wrap nbo-table-wrap" <?php if( isset($_REQUEST['wc-api']) && $_REQUEST['wc-api'] == 'NBO_Quick_View' && $nbd_qv_type == '2') echo 'nbd-perfect-scroll'; ?>>
    <?php if( count($options["bulk_fields"]) > 1): ?>
    <table class="nbo-bulk-variation">
        <thead>
            <tr>
                <th class="check-column" ng-click="select_all_variation( $event )">
                    <input type="checkbox" class="nbo-bulk-checkbox" >
                </th>
            <?php 
                foreach($options["bulk_fields"] as $key => $bulk_index): 
                   $field = $options["fields"][$bulk_index]; 
            ?>
                <th><?php echo $field['general']['title']; ?> <?php if( $field['general']['required'] == 'y' ): ?><span class="nbd-required">*</span><?php endif; ?></th>
            <?php endforeach; ?>
                <th><?php _e('Quantity', 'web-to-print-online-designer'); ?> <span class="nbd-required">*</span></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><input type="checkbox" class="nbo-bulk-checkbox" ></td>
            <?php 
                foreach($options["bulk_fields"] as $key => $bulk_index): 
                    $field = $options["fields"][$bulk_index];
            ?>
                <td>
                    <?php 
                        if($field['general']['data_type'] == 'i'): 
                        $input_type = $field['general']['input_type'] == 't' ? 'text' : 'number';
                    ?>
                    <input name="nbb-fields[<?php echo $field['id']; ?>][]" type="<?php echo $input_type; ?>"/>
                    <?php else: ?>
                    <select name="nbb-fields[<?php echo $field['id']; ?>][]" class="nbo-dropdown" onchange="nbOption.updateBulkPrice()">
                        <?php foreach ($field['general']['attributes']["options"] as $key => $attr): ?>
                        <option value="<?php echo $key; ?>" <?php selected( isset($attr['selected']) ? $attr['selected'] : 'off', 'on' ); ?>><?php echo $attr['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php endif; ?>
                </td>
            <?php endforeach; ?>
                <td><input class="nbb-qty-field" name="nbb-qty-fields[]" type="number" min="0" step="1" value="1" style="width: 4em" pattern="[0-9]*" onchange="nbOption.updateBulkPrice()"/></td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="<?php echo count($options["bulk_fields"]) + 2; ?>" style="text-align: left;">
                    <button ng-click="add_variaion($event)" type="button" class="button button-primary nbd-setting-table-add-rule"><?php _e( 'Add Variation', 'web-to-print-online-designer' ); ?></button>
                    <button ng-click="delete_variaions($event)" type="button" class="button button-secondary nbd-setting-table-delete-rules"><?php _e( 'Delete Selected', 'web-to-print-online-designer' ); ?></button>
                </th>
            </tr>
        </tfoot>
    </table>
    <?php else: 
        $bulk_index = reset($options["bulk_fields"]);
        $field = $options["fields"][$bulk_index];
    ?>
    <table class="nbo-bulk-variation">
        <tbody>
            <tr>
                <th></th>
                <?php foreach ($field['general']['attributes']["options"] as $key => $attr): ?>
                <th>
                    <?php echo $attr['name']; ?>
                    <input type="hidden" name="nbb-fields[<?php echo $field['id']; ?>][]" value="<?php echo $key; ?>" />
                </th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <th><?php echo $field['general']['title']; ?> <?php if( $field['general']['required'] == 'y' ): ?><span class="nbd-required">*</span><?php endif; ?></th>
                <?php foreach ($field['general']['attributes']["options"] as $key => $attr): ?>
                <td><input class="nbb-qty-field" name="nbb-qty-fields[]" type="number" min="0" step="1" value="1" style="width: 4em" pattern="[0-9]*"  onchange="nbOption.updateBulkPrice()"/></td>
                <?php endforeach; ?>
            </tr>
        </tbody>
    </table>
    <?php endif; ?>
</div>