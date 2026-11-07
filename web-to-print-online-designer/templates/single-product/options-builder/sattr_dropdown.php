<?php if (!defined('ABSPATH')) exit; ?>
<select ng-change="check_valid()" name="nbd-field[<?php echo $field['id']; ?>][sub_value]" class="nbo-dropdown" ng-model="nbd_fields['<?php echo $field['id']; ?>'].sub_value">
<?php foreach ($attr['sub_attributes'] as $skey => $sattr): ?>
    <option value="<?php echo $skey; ?>" 
        <?php 
            if( isset($form_values[$field['id']]) && isset($form_values[$field['id']]['sub_value']) ){
                selected( $form_values[$field['id']]['sub_value'], $skey ); 
            }else{
                selected( isset($sattr['selected']) ? $sattr['selected'] : 'off', 'on' ); 
            }
        ?>><?php echo $sattr['name']; ?></option>
<?php endforeach; ?>
</select>

