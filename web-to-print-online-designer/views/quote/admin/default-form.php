<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
$types = nbdq_get_form_field_type();
?>
<h1><?php echo __( 'Request quote form builder', 'web-to-print-online-designer' ); ?></h1>
<div id="nbdq-add-field">
    <h4><?php echo __( 'Add new field', 'web-to-print-online-designer' ); ?></h4>
    <label for="add-new-name"><?php _e( 'Enter field name', 'web-to-print-online-designer' ); ?></label>
    <input type="text" id="add-new-name" name="add-new-name" value=""/>
    <input type="button" value="<?php _e( 'Add field', 'web-to-print-online-designer' ); ?>" id="add-new" class="button" name="add-new">
</div>
<form method="post" id="nbdq_form_fields_form">
    <div class="fields_table_bulk_actions">
        <select name="bulk_action" id="bulk_actions_select">
            <option value=""><?php _e( 'Select an action', 'web-to-print-online-designer'); ?></option>
            <option value="enable"><?php _e( 'Enable', 'web-to-print-online-designer'); ?></option>
            <option value="disable"><?php _e( 'Disable', 'web-to-print-online-designer'); ?></option>
        </select>
        <input type="submit" id="nbdq_doaction_bulk" class="button action" value="Apply">
    </div>
    <table id="nbdq_form_fields" class="wc_gateways widefat" cellspacing="0">
        <thead>
            <tr>
                <th class="sort is_responsive"></th>
                <th class="check-column is_responsive" style="padding-left:0px !important;"><input type="checkbox" style="margin-left:7px;" /></th>
                <th class="name is_responsive"><?php _e( 'Name', 'web-to-print-online-designer' ) ?></th>
                <th class="type"><?php _e( 'Type', 'web-to-print-online-designer' ); ?></th>
                <th class="label"><?php _e( 'Label', 'web-to-print-online-designer' ); ?></th>
                <th class="placeholder"><?php _e( 'Placeholder', 'web-to-print-online-designer' ); ?></th>
                <th class="validation-rules"><?php _e( 'Validation rules', 'web-to-print-online-designer' ); ?></th>
                <th class="connection-to"><?php _e( 'Connected to', 'web-to-print-online-designer' ); ?></th>
                <th class="status"><?php _e( 'Required', 'web-to-print-online-designer' ); ?></th>
                <th class="actions is_responsive"><?php _e( 'Actions', 'web-to-print-online-designer' ); ?></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th class="sort is_responsive"></th>
                <th class="check-column is_responsive" style="padding-left:0px !important;"><input type="checkbox" style="margin-left:7px;" /></th>
                <th class="name is_responsive"><?php _e( 'Name', 'web-to-print-online-designer' ) ?></th>
                <th class="type"><?php _e( 'Type', 'web-to-print-online-designer' ); ?></th>
                <th class="label"><?php _e( 'Label', 'web-to-print-online-designer' ); ?></th>
                <th class="placeholder"><?php _e( 'Placeholder', 'web-to-print-online-designer' ); ?></th>
                <th class="validation-rules"><?php _e( 'Validation rules', 'web-to-print-online-designer' ); ?></th>
                <th class="connection-to"><?php _e( 'Connected to', 'web-to-print-online-designer' ); ?></th>
                <th class="status"><?php _e( 'Required', 'web-to-print-online-designer' ); ?></th>
                <th class="actions is_responsive"><?php _e( 'Actions', 'web-to-print-online-designer' ); ?></th>
            </tr>
        </tfoot>
        <tbody class="ui-sortable">
    <?php
        $i = 0;
        foreach ( $fields as $name => $field ) :
            $custom = ! in_array( $name, $default_fields_key ) ? true : false;
            $class = array(
                $custom ? 'is_custom' : '',
                ! $field['enabled'] ? 'disabled-row' : ''
            );
            $class = array_filter( $class );
            ?>
            <tr data-row="<?php echo $i; ?>" class="<?php echo implode(' ', $class ) ?>">
                <td width="1%" class="sort ui-sortable-handle is_responsive">
                    <input type="hidden" name="field_name[]" class="field_name" data-name="field_name" value="<?php echo esc_attr( $name ); ?>"/>
                    <input type="hidden" name="field_type[]" data-name="field_type" value="<?php echo $field['type'] ?>"/>
                    <input type="hidden" name="field_label[]" data-name="field_label" value="<?php echo $field['label']; ?>"/>
                    <input type="hidden" name="field_id[]" data-name="field_id" value="<?php echo $field['id']; ?>"/>
                    <input type="hidden" name="field_placeholder[]" data-name="field_placeholder" value="<?php echo $field['placeholder']; ?>"/>
                    <input type="hidden" name="field_description[]" data-name="field_description" value="<?php echo isset( $field['description'] ) ? $field['description']: ''; ?>"/>
                    <input type="hidden" name="field_options[]" data-name="field_options" value="<?php echo $field['options']; ?>"/>
                    <input type="hidden" name="field_position[]" data-name="field_position" value="<?php echo $field['position']; ?>"/>
                    <input type="hidden" name="field_class[]" data-name="field_class" value="<?php echo $field['class']; ?>"/>
                    <input type="hidden" name="field_label_class[]" data-name="field_label_class" value="<?php echo $field['label_class']; ?>"/>
                    <input type="hidden" name="field_required[]" data-name="field_required" value="<?php echo $field['required']; ?>"/>
                    <input type="hidden" name="field_enabled[]" data-name="field_enabled" value="<?php echo $field['enabled'] ?>"/>
                    <input type="hidden" name="field_validate[]" data-name="field_validate" value="<?php echo $field['validate'] ?>"/>
                    <input type="hidden" name="field_connect_to_field[]" data-name="field_connect_to_field" value="<?php echo $field['connect_to_field'] ?>"/>
                    <input type="hidden" name="field_deleted[]" data-name="field_deleted" value="" />
                </td>
                <td class="td_select is_responsive"><input type="checkbox" name="select_field[<?php echo $i; ?>]"/></td>
                <td class="td_field_name is_responsive"><?php echo esc_attr( $name ); ?></td>
                <td class="td_field_type"><?php echo isset( $types[ $field['type'] ] ) ? $types[ $field['type'] ] : $field['type']; ?></td>
                <td class="td_field_label"><?php echo $field['label']; ?></td>
                <td class="td_field_placeholder"><?php echo $field['placeholder']; ?></td>
                <td class="td_field_validate"><?php echo $field['validate']; ?></td>
                <td class="td_field_connect_to_field"><?php echo $field['connect_to_field']; ?></td>
                <td class="td_field_required status"><?php echo( $field['required'] == 1 ? '<span class="status-enabled tips" data-tip="' .  __( 'Yes', 'web-to-print-online-designer' ) . '"></span>' : '-' ) ?></td>
                <td class="td_field_action is_responsive">
                    <button type="button" class="button edit_field"><?php _e( 'Edit', 'web-to-print-online-designer' ); ?></button>
                    <?php
                    $enable_button_label = $field['enabled'] == 1 ? __( 'Disable', 'web-to-print-online-designer' ) : __( 'Enable', 'web-to-print-online-designer' );
                    $enable_button_data  = $field['enabled'] != 1 ? __( 'Disable', 'web-to-print-online-designer' ) : __( 'Enable', 'web-to-print-online-designer' );
                    if( $name != 'email' ):
                    ?>
                    <button type="button" class="button enable_field" data-label="<?php echo $enable_button_data ?>"><?php echo $enable_button_label ?></button>
                    <button type="button" class="button remove_field"><?php _e( 'Remove', 'web-to-print-online-designer' ); ?></button>
                    <?php endif; ?>
                </td>
            </tr>
            <?php $i ++; endforeach; ?>
        </tbody>
    </table>
    <div class="fields_table_bulk_actions">
        <select name="bulk_action_bottom" id="bulk_actions_select_bottom">
            <option value=""><?php _e( 'Select an action', 'web-to-print-online-designer'); ?></option>
            <option value="enable"><?php _e( 'Enable', 'web-to-print-online-designer'); ?></option>
            <option value="disable"><?php _e( 'Disable', 'web-to-print-online-designer'); ?></option>
        </select>
        <input type="submit" id="nbdq_doaction_bulk_bottom" class="button action" value="Apply">
    </div>
    <input type="hidden" name="nbdq-admin-action" value="fields-save" />
    <button style="float: left; margin-right: 10px;" class="button-primary save-form" type="submit"><?php _e( 'Save changes', 'web-to-print-online-designer' )?></button>
</form>