<?php if (!defined('ABSPATH')) exit; ?>
<tr class="nbd-option-field nbd-field-input-wrap <?php echo $class; ?>" ng-if="nbd_fields['<?php echo $field['id']; ?>'].enable">
    <td>
        <label for='nbd-field-<?php echo $field['id']; ?>'>
            <?php echo $field['general']['title']; ?>
            <?php if( $field['general']['required'] == 'y' ): ?>
            <span class="nbd-required">*</span>
            <?php endif; ?>
        </label> 
        <?php if( $field['general']['description'] != '' ): ?>
        <span data-position="<?php echo $tooltip_position; ?>" data-tip="<?php echo $field['general']['description']; ?>" class="nbd-help-tip"></span>
        <?php endif; ?>
    </td>
    <td class="nbd-field-content">
        <input 
            <?php if( isset($field['nbd_type']) && $field['nbd_type'] == 'dpi' ): ?>
            ng-change="update_dpi()"  
            <?php else: ?>
            ng-change="check_valid()" 
            <?php endif; ?>
            <?php if( isset($field['nbd_type']) && $field['nbd_type'] == 'dimension' ): ?>
                ng-hide="true"
            <?php endif; ?>
            ng-model="nbd_fields['<?php echo $field['id']; ?>'].value" class="nbd-input-<?php echo $field['general']['input_type']; ?>" 
            <?php if( $field['general']['required'] == 'y' ) echo 'required'; ?> name="nbd-field[<?php echo $field['id']; ?>]" id="nbd-field-<?php echo $field['id']; ?>"
            <?php if( $field['general']['input_type'] == 't' ): ?>
            type="text" <?php if( $field['general']['text_option']['min'] != '' ): ?>pattern=".{0}|.{<?php echo $field['general']['text_option']['min']; ?>,}"<?php endif; ?> <?php if( $field['general']['text_option']['max'] != '' ): ?>maxlength="<?php echo $field['general']['text_option']['max']; ?>"<?php endif; ?>
                <?php if( isset( $field['general']['placeholder'] ) && $field['general']['placeholder'] != '' ): ?>
                    placeholder="<?php echo $field['general']['placeholder']; ?>"
                <?php endif; ?>
            <?php elseif( $field['general']['input_type'] == 'n' ): ?>
            string-to-number type="number" min="<?php echo $field['general']['input_option']['min']; ?>" max="<?php echo $field['general']['input_option']['max']; ?>" step="<?php echo $field['general']['input_option']['step']; ?>" ng-step="<?php if( $field['general']['input_option']['step'] != '' ) echo $field['general']['input_option']['step']; else echo '0.0001'; ?>"
            <?php elseif( $field['general']['input_type'] == 'r' ): ?>
            string-to-number type="range" min="<?php echo $field['general']['input_option']['min']; ?>" max="<?php echo $field['general']['input_option']['max']; ?>" step="<?php echo $field['general']['input_option']['step']; ?>" ng-step="<?php if( $field['general']['input_option']['step'] != '' ) echo $field['general']['input_option']['step']; else echo '0.0001'; ?>"
            <?php elseif( $field['general']['input_type'] == 'u' ): ?>
            type="file" nbo-input-file="check_valid()" data-field-id="<?php echo $field['id']; ?>" data-types="<?php echo strtolower( trim( $field['general']['upload_option']['allow_type'] ) ); ?>" 
                data-minsize="<?php echo $field['general']['upload_option']['min_size']; ?>" data-maxsize="<?php echo $field['general']['upload_option']['max_size']; ?>"
                <?php 
                    $file_url = '';
                    $filename = '';
                    $uploaded = 0;
                    if( isset($form_values[$field['id']]) ){
                        $file_url = NBDESIGNER_UPLOAD_URL . '/' . $form_values[$field['id']];
                        $filename = explode('/', $form_values[$field['id']])[1];
                        $uploaded = 1;
                    }
                ?>
                data-file="<?php echo $file_url; ?>" data-filename="<?php echo $filename; ?>" data-uploaded="<?php echo $uploaded; ?>"
                <?php 
                    if( $field['general']['upload_option']['allow_type'] != '' ):
                        $allow_type = strtolower( trim( $field['general']['upload_option']['allow_type'] ) );
                        $allow_type_arr = explode(',', $allow_type);
                        $delimiter = '';
                ?>
                accept="<?php foreach( $allow_type_arr as $_type ){ $_type = trim($_type); if($_type == 'jpg' || $_type == 'jpeg'){ $_type = 'jpg,.jpeg'; }; $__type = $delimiter . '.' . $_type; $delimiter = ','; echo $__type; } ?>"
                <?php endif; ?>
            <?php endif; ?>
        />
        <?php 
            if( $field['general']['input_type'] == 'u' && isset($form_values[$field['id']]) ):
        ?>
        <input class="nbd-upload-hidden" id="nbd-upload-hidden-<?php echo $field['id']; ?>" type="hidden" name="nbd-field[<?php echo $field['id']; ?>]" value="<?php echo $form_values[$field['id']]; ?>" />
        <?php endif; ?>
        <?php if( $field['general']['input_type'] == 'u' && $field['general']['upload_option']['min_size'] != '' ): ?>
        <span style="display: block; font-size: 12px;margin-top: 10px;"><?php echo __('Min size: ', 'web-to-print-online-designer') . $field['general']['upload_option']['min_size'] . ' MB'; ?></span>
        <?php endif; ?>
        <?php if( $field['general']['input_type'] == 'u' && $field['general']['upload_option']['max_size'] != '' ): ?>
        <span style="display: block; font-size: 12px;"><?php echo __('Max size: ', 'web-to-print-online-designer') . $field['general']['upload_option']['max_size'] . ' MB'; ?></span>
        <?php endif; ?>
        <?php if($field['general']['input_type'] == 'r'): ?><span class="nbd-input-range">{{nbd_fields['<?php echo $field['id']; ?>'].value}}</span><?php endif; ?>
            
        <?php if( isset($field['nbd_type']) && $field['nbd_type'] == 'dimension' ): ?>
        <span style="display: inline-block; margin-bottom: 5px;">
            <label class="nbo-dimension-label" for="nbd-field-<?php echo $field['id']; ?>-width"><?php _e('Width', 'web-to-print-online-designer'); ?></label> 
            <span class="nbo-dimension-wrap">
                <span class="nbo-updown-dimension" nbo-click-debounce ng-click="update_dimension('<?php echo $field['id']; ?>', 'width', 'minus')">-</span>
                <input class="nbo-dimension" 
                    ng-model-options="{ allowInvalid: true, updateOn: 'blur', debounce: { 'default': 500, 'blur': 0 } }" 
                    ng-change="update_dimensionvalue('<?php echo $field['id']; ?>', 'width')" id="nbd-field-<?php echo $field['id']; ?>-width" 
                    required ng-model="nbd_fields['<?php echo $field['id']; ?>'].width" 
                    type="number" min="<?php echo $field['general']['min_width']; ?>"
                    max="<?php echo $field['general']['max_width']; ?>" 
                    step="<?php echo $field['general']['step_width']; ?>" <?php if(isset($field['general']['default_width']) && $field['general']['default_width'] != '' && !isset( $form_values[$field['id']] ) ) echo 'value="' . $field['general']['default_width'] . '" ng-init="nbd_fields['. "'" . $field['id']. "'" .'].width = ('. $field['general']['default_width'] .' | updateDimension: {curr: current_dimensions.width, min: ' . $field['general']['min_width'] . ', max: ' . $field['general']['max_width'] . ', fid: ' . "'" . $field['id']. "'" . '})"'; ?> ng-step="0.0001" />
                <span class="nbo-updown-dimension" nbo-click-debounce ng-click="update_dimension('<?php echo $field['id']; ?>', 'width', 'plus')">+</span>
            </span>
        </span><br />
        <span style="display: inline-block; margin-bottom: 5px;">
            <label class="nbo-dimension-label" for="nbd-field-<?php echo $field['id']; ?>-height"><?php _e('Height', 'web-to-print-online-designer'); ?></label> 
            <span class="nbo-dimension-wrap">
                <span class="nbo-updown-dimension" nbo-click-debounce ng-click="update_dimension('<?php echo $field['id']; ?>', 'height', 'minus')">-</span>
                <input class="nbo-dimension" 
                    ng-model-options="{ allowInvalid: true, updateOn: 'blur', debounce: { 'default': 500, 'blur': 0 } }" 
                    ng-change="update_dimensionvalue('<?php echo $field['id']; ?>', 'height')" id="nbd-field-<?php echo $field['id']; ?>-height" 
                    required ng-model="nbd_fields['<?php echo $field['id']; ?>'].height" 
                    type="number" min="<?php echo $field['general']['min_height']; ?>" 
                    max="<?php echo $field['general']['max_height']; ?>" step="<?php echo $field['general']['step_height']; ?>" <?php if(isset($field['general']['default_height']) && $field['general']['default_height'] != ''  && !isset( $form_values[$field['id']] ) ) echo 'value="' . $field['general']['default_height'] . '" ng-init="nbd_fields['. "'" . $field['id']. "'" .'].height = ('. $field['general']['default_height'] .' | updateDimension: {curr: current_dimensions.height, min: ' . $field['general']['min_height'] . ', max: ' . $field['general']['max_height'] . ', fid: ' . "'" . $field['id']. "'" . '})"'; ?> ng-step="0.0001" />
                <span class="nbo-updown-dimension" nbo-click-debounce ng-click="update_dimension('<?php echo $field['id']; ?>', 'height', 'plus')">+</span>
            </span>
        </span>
        <span><?php echo $dimension_unit; ?></span>
        <br /><small class="nbo-dimension-width"><?php echo sprintf(__('Width: min %s - max %s', 'web-to-print-online-designer'), $field['general']['min_width'], $field['general']['max_width']); ?></small>
        <br /><small class="nbo-dimension-width"><?php echo sprintf(__('Height: min %s - max %s', 'web-to-print-online-designer'), $field['general']['min_height'], $field['general']['max_height']); ?></small>
        <br /><span class="nbd-invalid-notice nbd-invalid-min nbd-invalid-max"><?php echo __('Invalid value', 'web-to-print-online-designer'); ?></span>
        <?php endif; ?>
        <?php if( !(isset($field['nbd_type']) && $field['nbd_type'] == 'dimension') ): ?>
        <span class="nbd-invalid-notice nbd-invalid-min"><?php echo __('Invalid value, min: ', 'web-to-print-online-designer') . $field['general']['input_option']['min']; ?></span>
        <span class="nbd-invalid-notice nbd-invalid-max"><?php echo __('Invalid value, max: ', 'web-to-print-online-designer') . $field['general']['input_option']['max']; ?></span>
        <?php endif; ?>
    </td>
</tr>