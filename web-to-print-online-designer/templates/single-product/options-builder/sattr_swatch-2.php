<?php if (!defined('ABSPATH')) exit; ?>
<?php 
    foreach ($attr['sub_attributes'] as $skey => $sattr): 
        $image_url = nbd_get_image_thumbnail( $sattr['image'] );
?>
    <input ng-change="check_valid()" value="<?php echo $skey; ?>" ng-model="nbd_fields['<?php echo $field['id']; ?>'].sub_value" name="nbd-field[<?php echo $field['id']; ?>][sub_value]" 
           type="radio" id='nbd-field-<?php echo $field['id'].'-'.$key.'-'.$skey; ?>' 
        <?php 
            if( isset($form_values[$field['id']]) && isset($form_values[$field['id']]['sub_value']) ){
                checked( $form_values[$field['id']]['sub_value'], $skey ); 
            }else{
                checked( isset($sattr['selected']) ? $sattr['selected'] : 'off', 'on' ); 
            }
        ?> />
    <label class="nbd-swatch" style="<?php if( $sattr['preview_type'] == 'i' ){echo 'background: url('.$image_url . ') 0% 0% / cover';}else{ echo 'background: '.$sattr['color']; }; ?>" 
        title="<?php echo $sattr['name']; ?>" for='nbd-field-<?php echo $field['id'].'-'.$key.'-'.$skey; ?>'><span class="nbd-swatch-tooltip"><?php echo $sattr['name']; ?></span>
    </label>
<?php endforeach;