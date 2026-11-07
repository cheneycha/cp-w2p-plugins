<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="nbd.shape">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Custom area design shape', 'web-to-print-online-designer'); ?></b> <nbd-tip data-tip="<?php _e('Only support SVG code', 'web-to-print-online-designer'); ?>" ></nbd-tip></div>
        </div>
        <div class="nbd-field-info-2">
            <table class="nbd-table" style="text-align: center;">
                <tbody>
                    <tr ng-repeat="(opIndex, op) in field.general.attributes.options">
                        <th style="text-align: left;">{{op.name}}</th>
                        <td>
                            <textarea placeholder="<?php _e('SVG code here...', 'web-to-print-online-designer'); ?>" ng-change="validateSvgShape(fieldIndex, opIndex)" name="options[fields][{{fieldIndex}}][general][attributes][options][{{opIndex}}][shape]" ng-model="op.shape"/></textarea>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php echo '</script>';