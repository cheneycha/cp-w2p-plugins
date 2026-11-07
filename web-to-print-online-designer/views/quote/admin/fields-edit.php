<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
global $wpdb;
?>
<div id="nbdq_field_add_edit_form" style="display: none;">
    <form>
        <table>
            <tr class="remove_default">
                <td class="label"><?php _e( 'Name', 'web-to-print-online-designer' ) ?></td>
                <td><input type="text" name="field_name"/></td>
            </tr>
            <tr class="remove_default">
                <td class="label"><?php _e( 'Type', 'web-to-print-online-designer' ) ?></td>
                <td>
                    <select name="field_type">
                        <?php foreach( $field_types as $value => $label ): ?>
                            <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr class="remove_default" data-hide="text,password,tel,textarea,radio,checkbox,select,nbdq_multiselect,nbdq_datepicker,nbdq_timepicker,nbdq_heading,nbdq_acceptance,country">
                <td class="label"><?php _e( 'ID', 'web-to-print-online-designer' ) ?></td>
                <td>
                    <select name="field_id">
                        <option value="billing_state">billing_state</option>
                        <option value="shipping_state">shipping_state</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td class="label"><?php _e( 'Label', 'web-to-print-online-designer' ) ?></td>
                <td><input type="text" name="field_label"/></td>
            </tr>
            <tr data-hide="nbdq_heading,checkbox,radio,heading,state,country,nbdq_acceptance">
                <td class="label"><?php _e( 'Placeholder', 'web-to-print-online-designer' ) ?></td>
                <td><input type="text" name="field_placeholder"/></td>
            </tr>
            <tr data-hide="text,password,tel,textarea,radio,checkbox,select,nbdq_multiselect,nbdq_datepicker,nbdq_timepicker,nbdq_heading,state,country">
                <td class="label"><?php _e( 'Description', 'web-to-print-online-designer' ) ?></td>
                <td><textarea  name="field_description" columns="10" rows="5"></textarea>
                    <small><?php _e('You can use the shortcode [terms] and [privacy_policy] (from WooCommerce 3.4.0)', 'web-to-print-online-designer')?></small></td>
            </tr>
            <tr class="remove_default" data-hide="text,password,tel,textarea,nbdq_datepicker,checkbox,nbdq_heading,nbdq_timepicker,state,country,nbdq_acceptance">
                <td class="label"><?php _e( 'Options', 'web-to-print-online-designer' ) ?></td>
                <td><input type="text" name="field_options" placeholder="" />
                    <small><?php _e( 'Separate options with pipes (|) and key from value using (::). Es. key::value|', 'web-to-print-online-designer' ); ?></small></td>
            </tr>
            <tr>
                <td class="label"><?php _e( 'Class', 'web-to-print-online-designer' ) ?></td>
                <td><input type="text" name="field_class" placeholder=""/>
                    <small><?php _e( 'Separate classes with commas, ex: form-row-first, form-row-last, form-row-wide', 'web-to-print-online-designer' ); ?></small></td>
            </tr>
            <tr data-hide="nbdq_heading">
                <td class="label"><?php _e( 'Label class', 'web-to-print-online-designer' ) ?></td>
                <td><input type="text" name="field_label_class" placeholder=""/>
                    <small><?php _e( 'Separate classes with commas', 'web-to-print-online-designer' ); ?></small></td>
            </tr>
            <tr data-hide="nbdq_heading,nbdq_acceptance">
                <td class="label"><?php _e( 'Connect field to', 'web-to-print-online-designer' ) ?></td>
                <td>
                    <select name="field_connect_to_field"/>
                <?php foreach ( $connect_to_fields as $connect_to_field ): ?>
                        <option value="<?php echo $connect_to_field ?>"><?php echo $connect_to_field ?></option>
                <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <?php if( isset( $validation ) && is_array( $validation ) ) : ?>
                <tr data-hide="nbdq_heading,nbdq_acceptance">
                    <td class="label"><?php _e( 'Validation', 'web-to-print-online-designer' ) ?></td>
                    <td>
                        <select name="field_validate"/>
                        <?php foreach( $validation as $valid_rule => $valid_label ): ?>
                            <option value="<?php echo $valid_rule ?>"><?php echo $valid_label ?></option>
                        <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            <?php endif; ?>
            <tr data-hide="nbdq_heading">
                <td>&nbsp;</td>
                <td>
                    <input type="checkbox" name="field_required" value="1" checked/>
                    <label for="field_required"><?php _e( 'Required', 'web-to-print-online-designer' ) ?></label>
                </td>
            </tr>

        </table>
    </form>
</div>