<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbd-option-field nbd-field-input-wrap <?php echo $class; ?>" ng-if="nbd_fields['<?php echo $field['id']; ?>'].enable">
    <?php include( $currentDir .'/options-builder/field-header.php' ); ?>
    <div class="nbd-field-content">
        <textarea name="nbd-field[<?php echo $field['id']; ?>]" class="nbd-field-textarea" rows="3"
            ng-model="nbd_fields['<?php echo $field['id']; ?>'].value" ng-change="check_valid()" 
            <?php if( isset( $field['general']['placeholder'] ) && $field['general']['placeholder'] != '' ): ?>
                placeholder="<?php echo $field['general']['placeholder']; ?>"
            <?php endif; ?> 
            <?php if( $field['general']['text_option']['min'] != '' ): ?>pattern=".{0}|.{<?php echo $field['general']['text_option']['min']; ?>,}"<?php endif; ?> <?php if( $field['general']['text_option']['max'] != '' ): ?>maxlength="<?php echo $field['general']['text_option']['max']; ?>"<?php endif; ?> ></textarea>
    </div>
</div>