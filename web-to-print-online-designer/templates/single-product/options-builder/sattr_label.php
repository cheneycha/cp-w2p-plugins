<?php if (!defined('ABSPATH')) exit; ?>
<?php foreach ($attr['sub_attributes'] as $skey => $sattr): ?>
<input ng-change="check_valid()" value="<?php echo $skey; ?>" ng-model="nbd_fields['<?php echo $field['id']; ?>'].sub_value" 
       name="nbd-field[<?php echo $field['id']; ?>][sub_value]" type="radio" id='nbd-field-<?php echo $field['id'].'-'.$key.'-'.$skey; ?>' 
    <?php
        if( isset($form_values[$field['id']]) && isset($form_values[$field['id']]['sub_value']) ){
            checked( $form_values[$field['id']]['sub_value'], $skey );
        }else{
            checked( isset($sattr['selected']) ? $sattr['selected'] : 'off', 'on' ); 
        }
    ?> />
<label class="nbd-label" for='nbd-field-<?php echo $field['id'].'-'.$key.'-'.$skey; ?>' >
    <?php echo $sattr['name']; ?> 
</label>
<?php endforeach;