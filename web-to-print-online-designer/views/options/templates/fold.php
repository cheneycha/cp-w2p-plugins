<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="nbd.fold">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Folding Styles', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <table class="nbd-table" style="text-align: center;">
                <tbody>
                    <tr ng-repeat="(opIndex, op) in field.general.attributes.options">
                        <th style="text-align: left;">{{op.name}}</th>
                        <td>
                            <select ng-model="op.fold" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][fold]" >
                                <option value="n"><?php _e('No fold', 'web-to-print-online-designer'); ?></option>
                                <option value="h"><?php _e('Half fold', 'web-to-print-online-designer'); ?></option>
                                <option value="t"><?php _e('Tri fold', 'web-to-print-online-designer'); ?></option>
                                <option value="z"><?php _e('Z fold', 'web-to-print-online-designer'); ?></option>
                                <option value="s"><?php _e('Single gate fold', 'web-to-print-online-designer'); ?></option>
                                <option value="d"><?php _e('Double gate fold', 'web-to-print-online-designer'); ?></option>
                                <option value="dp"><?php _e('Double parallel fold', 'web-to-print-online-designer'); ?></option>
                                <option value="dr"><?php _e('Double parallel reverse fold', 'web-to-print-online-designer'); ?></option>
                                <option value="r"><?php _e('Roll fold ( 4 panels )', 'web-to-print-online-designer'); ?></option>
                                <option value="a"><?php _e('Accordion fold ( 4 panels )', 'web-to-print-online-designer'); ?></option>
                                <option value="hh"><?php _e('Half fold then half fold', 'web-to-print-online-designer'); ?></option>
                                <option value="ht"><?php _e('Half fold then tri fold', 'web-to-print-online-designer'); ?></option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div>
                <img style="margin-top: 15px;" src="<?php echo NBDESIGNER_ASSETS_URL . 'images/folding-style.png' ?>"/>
            </div>
        </div>
    </div>
<?php echo '</script>';