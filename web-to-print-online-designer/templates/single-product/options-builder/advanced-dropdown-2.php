<?php if (!defined('ABSPATH')) exit; ?>
<tr nbo-adv-dropdown class="nbd-option-field nbd-field-ad-dropdown-wrap <?php echo $class; ?>" data-id="<?php echo $field['id']; ?>" ng-if="nbd_fields['<?php echo $field['id']; ?>'].enable">
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
        <div>
            <select ng-change="check_valid()" name="nbd-field[<?php echo $field['id']; ?>]{{nbd_fields['<?php echo $field['id']; ?>'].form_name}}" class="nbo-dropdown" ng-model="nbd_fields['<?php echo $field['id']; ?>'].value">
            <?php 
                foreach ($field['general']['attributes']["options"] as $key => $attr): 
            ?>
                <option value="<?php echo $key; ?>"
                    <?php 
                        if( isset($form_values[$field['id']]) ){
                            $fvalue = (is_array($form_values[$field['id']]) && isset($form_values[$field['id']]['value'])) ? $form_values[$field['id']]['value'] : $form_values[$field['id']];
                            selected( $fvalue, $key ); 
                        }else{
                            selected( isset($attr['selected']) ? $attr['selected'] : 'off', 'on' ); 
                        }
                    ?>><?php echo $attr['name']; ?></option>
            <?php endforeach; ?>
            </select> 
            <div class="nbo-ad-result">
                <span class="nbo-ad-result-name">{{nbd_fields['<?php echo $field['id']; ?>'].value_name}}</span>
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
                    <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
                </svg>
            </div>
            <div class="nbo-ad-pseudo-list">
                <?php 
                    foreach ($field['general']['attributes']["options"] as $key => $attr): 
                        $image_url = nbd_get_image_thumbnail( $attr['image'] );
                        $enable_subattr = isset($attr['enable_subattr']) ? $attr['enable_subattr'] : 0;
                        $attr['sub_attributes'] = isset( $attr['sub_attributes'] ) ? $attr['sub_attributes'] : array();
                        $show_subattr = ($enable_subattr == 'on' && count($attr['sub_attributes']) > 0) ? true : false;
                        $field['general']['attributes']["options"][$key]['show_subattr'] = $show_subattr;
                ?>
                <div class="nbo-ad-list-item" 
                     ng-click="select_adv_attr('<?php echo $field['id']; ?>', '<?php echo $key; ?>');updateMapOptions('<?php echo $field['id']; ?>')"
                     ng-class="nbd_fields['<?php echo $field['id']; ?>'].value == '<?php echo $key; ?>' ? 'active' : ''"
                     nbo-disabled="!status_fields['<?php echo $field['id']; ?>'][<?php echo $key; ?>].enable" nbo-disabled-type="class" >
                    <?php if( $attr['preview_type'] == 'i' && $attr['image'] != '0' ): ?>
                    <img src="<?php echo $image_url; ?>" class="nbo-ad-item-thumb"/>
                    <?php elseif( $attr['preview_type'] == 'c' ): ?>
                    <span class="nbo-ad-item-thumb" style="background: <?php echo $attr['color']; ?>"></span>
                    <?php endif; ?>
                    <div class="nbo-ad-item-main <?php if( $show_subattr ) echo 'nbo-shrink'; ?>">
                        <div class="nbo-ad-item-title"><?php echo $attr['name']; ?></div>
                        <div class="nbo-ad-item-description"><?php echo $attr['des']; ?></div>
                    </div>
                    <?php if( isset($attr['selected']) && $attr['selected'] == 'on'  ): ?>
                    <span class="nbo-recomand" title="<?php _e('Recommended', 'web-to-print-online-designer'); ?>">
                        <svg class="octicon octicon-bookmark" viewBox="0 0 10 16" version="1.1" width="10" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M9 0H1C.27 0 0 .27 0 1v15l5-3.09L10 16V1c0-.73-.27-1-1-1zm-.78 4.25L6.36 5.61l.72 2.16c.06.22-.02.28-.2.17L5 6.6 3.12 7.94c-.19.11-.25.05-.2-.17l.72-2.16-1.86-1.36c-.17-.16-.14-.23.09-.23l2.3-.03.7-2.16h.25l.7 2.16 2.3.03c.23 0 .27.08.09.23h.01z"></path></svg>
                    </span>
                    <?php endif; ?>
                    <?php if( $show_subattr ): ?>
                    <span class="nbo-ad-pseudo-sublist-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="24" height="24" viewBox="0 0 24 24">
                            <path d="M16.594 8.578l1.406 1.406-6 6-6-6 1.406-1.406 4.594 4.594z"/>
                        </svg>
                    </span>
                    <div class="nbo-ad-pseudo-sublist">
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
                        <?php foreach ($attr['sub_attributes'] as $skey => $sattr): ?>
                        <div class="nbo-ad-list-item"
                             ng-click="select_adv_subattr('<?php echo $field['id']; ?>', '<?php echo $key; ?>', '<?php echo $skey; ?>')"
                             ng-class="( nbd_fields['<?php echo $field['id']; ?>'].value == '<?php echo $key; ?>' && nbd_fields['<?php echo $field['id']; ?>'].sub_value == '<?php echo $skey; ?>' ) ? 'active' : ''" 
                             nbo-disabled="!status_fields['<?php echo $field['id']; ?>'][<?php echo $key; ?>].sub_attributes[<?php echo $skey; ?>]" nbo-disabled-type="class" >
                            <?php 
                                if( $sattr['preview_type'] == 'i' && $sattr['image'] != '0' ):
                                    $simage_url = nbd_get_image_thumbnail( $sattr['image'] );
                            ?>
                            <img src="<?php echo $simage_url; ?>" class="nbo-ad-item-thumb"/>
                            <?php elseif( $sattr['preview_type'] == 'c' ): ?>
                            <span class="nbo-ad-item-thumb" style="background: <?php echo $sattr['color']; ?>"></span>
                            <?php endif; ?>
                            <div class="nbo-ad-item-main">
                                <div class="nbo-ad-item-title"><?php echo $sattr['name']; ?></div>
                                <div class="nbo-ad-item-description"><?php echo $sattr['des']; ?></div>
                            </div>
                            <?php if( isset($sattr['selected']) && $sattr['selected'] == 'on'  ): ?>
                            <span class="nbo-recomand" title="<?php _e('Recommended', 'web-to-print-online-designer'); ?>">
                                <svg class="octicon octicon-bookmark" viewBox="0 0 10 16" version="1.1" width="10" height="16" aria-hidden="true"><path fill-rule="evenodd" d="M9 0H1C.27 0 0 .27 0 1v15l5-3.09L10 16V1c0-.73-.27-1-1-1zm-.78 4.25L6.36 5.61l.72 2.16c.06.22-.02.28-.2.17L5 6.6 3.12 7.94c-.19.11-.25.05-.2-.17l.72-2.16-1.86-1.36c-.17-.16-.14-.23.09-.23l2.3-.03.7-2.16h.25l.7 2.16 2.3.03c.23 0 .27.08.09.23h.01z"></path></svg>
                            </span>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="nbo-invalid-option" 
                ng-class="nbd_fields['<?php echo $field['id']; ?>'].valid === false ? 'active' : ''"
                ng-if="nbd_fields['<?php echo $field['id']; ?>'].valid === false">{{nbd_fields['<?php echo $field['id']; ?>'].invalidOption}} <?php _e('is not available', 'web-to-print-online-designer'); ?>
            </div>
        </div>
    </td>
</tr>