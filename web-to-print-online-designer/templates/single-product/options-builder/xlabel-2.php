<?php if (!defined('ABSPATH')) exit; ?>
<tr nbo-adv-dropdown class="nbd-option-field nbd-field-xlabel-wrap <?php echo $class; ?>" data-id="<?php echo $field['id']; ?>" ng-if="nbd_fields['<?php echo $field['id']; ?>'].enable">
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
        <div class="nbd-xlabel-wrapper nbo-clearfix">
            <?php 
                foreach ($field['general']['attributes']["options"] as $key => $attr): 
                    $image_url = nbd_get_image_thumbnail( $attr['image'] );
                    $enable_subattr = isset($attr['enable_subattr']) ? $attr['enable_subattr'] : 0;
                    $attr['sub_attributes'] = isset( $attr['sub_attributes'] ) ? $attr['sub_attributes'] : array();
                    $show_subattr = ($enable_subattr == 'on' && count($attr['sub_attributes']) > 0) ? true : false;
                    $field['general']['attributes']["options"][$key]['show_subattr'] = $show_subattr;
            ?>
            <div class="nbd-xlabel-wrap">
                <div class="nbd-xlabel-value">
                    <div class="nbd-xlabel-value-inner" title="<?php echo $attr['name']; ?>">
                        <input ng-change="check_valid();updateMapOptions('<?php echo $field['id']; ?>')" value="<?php echo $key; ?>" ng-model="nbd_fields['<?php echo $field['id']; ?>'].value" name="nbd-field[<?php echo $field['id']; ?>]<?php if($show_subattr) echo '[value]'; ?>" 
                               type="radio" id='nbd-field-<?php echo $field['id'].'-'.$key; ?>' 
                            <?php 
                                if( isset($form_values[$field['id']]) ){
                                    $fvalue = (is_array($form_values[$field['id']]) && isset($form_values[$field['id']]['value'])) ? $form_values[$field['id']]['value'] : $form_values[$field['id']];
                                    checked( $fvalue, $key ); 
                                }else{
                                    checked( isset($attr['selected']) ? $attr['selected'] : 'off', 'on' ); 
                                }
                            ?> />
                        <label class="nbd-xlabel" style="<?php if( $attr['preview_type'] == 'i' ){echo 'background: url('.$image_url . ') 0% 0% / cover';}else{echo 'background: '.$attr['color'];}?>" 
                             for='nbd-field-<?php echo $field['id'].'-'.$key; ?>'
                             nbo-disabled="!status_fields['<?php echo $field['id']; ?>'][<?php echo $key; ?>].enable" nbo-disabled-type="class" >
                            <?php if(isset($attr['des']) && $attr['des'] != ''): ?>
                            <span class="nbd-help-tip" data-tip="<?php echo $attr['des']; ?>"></span>
                            <?php endif; ?>
                            <?php if( isset($attr['selected']) && $attr['selected'] == 'on'  ): ?>
                            <span class="nbo-recomand" title="<?php _e('Recommended', 'web-to-print-online-designer'); ?>">
                                <svg class="octicon octicon-bookmark" viewBox="0 0 10 16" version="1.1" width="10" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M9 0H1C.27 0 0 .27 0 1v15l5-3.09L10 16V1c0-.73-.27-1-1-1zm-.78 4.25L6.36 5.61l.72 2.16c.06.22-.02.28-.2.17L5 6.6 3.12 7.94c-.19.11-.25.05-.2-.17l.72-2.16-1.86-1.36c-.17-.16-.14-.23.09-.23l2.3-.03.7-2.16h.25l.7 2.16 2.3.03c.23 0 .27.08.09.23h.01z"></path></svg>
                            </span>
                            <?php endif; ?>
                        </label>
                    </div>
                </div>
                <label for='nbd-field-<?php echo $field['id'].'-'.$key; ?>'>
                    <b><?php echo $attr['name']; ?></b>
                </label>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="nbo-invalid-option" 
            ng-class="nbd_fields['<?php echo $field['id']; ?>'].valid === false ? 'active' : ''"
            ng-if="nbd_fields['<?php echo $field['id']; ?>'].valid === false">{{nbd_fields['<?php echo $field['id']; ?>'].invalidOption}} <?php _e('is not available', 'web-to-print-online-designer'); ?>
        </div>
        <?php 
            foreach ($field['general']['attributes']["options"] as $key => $attr): 
                if( $attr['show_subattr'] ):
                    $sattr_display_type = isset( $attr['sattr_display_type'] ) ? $attr['sattr_display_type'] : 's';
                    switch($sattr_display_type){
                        case 's':
                            $tempalte = $currentDir .'/options-builder/sattr_swatch'.$prefix.'.php';
                            $wrap_class = 'nbd-swatch-wrap';
                            break;
                        case 'l':
                            $tempalte = $currentDir .'/options-builder/sattr_label.php';
                            $wrap_class = 'nbd-label-wrap';
                            break;            
                        case 'r':
                            $tempalte = $currentDir .'/options-builder/sattr_radio.php';
                            $wrap_class = 'nbd-radio';
                            break;
                        default:
                            $tempalte = $currentDir .'/options-builder/sattr_dropdown.php';
                            $wrap_class = '';
                            break;            
                    }
        ?>
        <div ng-if="nbd_fields['<?php echo $field['id']; ?>'].value == '<?php echo $key; ?>'" class="nbo-sub-attr-wrap <?php echo $wrap_class; ?>">
        <?php include($tempalte); ?>
        </div>
        <?php endif; endforeach; ?>
    </td>
</tr>