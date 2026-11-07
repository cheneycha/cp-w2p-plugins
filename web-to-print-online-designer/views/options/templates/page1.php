<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="nbd.page1">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Page display', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <select name="options[fields][{{fieldIndex}}][general][page_display]" ng-model="field.general.page_display">
                <option value="1"><?php _e('Each page on a design stage', 'web-to-print-online-designer'); ?></option>
                <option value="2"><?php _e('Two pages on a design stage', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Exclude page', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <select name="options[fields][{{fieldIndex}}][general][exclude_page]" ng-model="field.general.exclude_page">
                <option value="0"><?php _e('None', 'web-to-print-online-designer'); ?></option>
                <option value="2"><?php _e('Cover pages', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
    <div class="nbd-field-info" ng-if="field.general.depend_quantity.value == 'n'">
        <div class="nbd-field-info-1">
            <div>
                <label>
                    <b><?php _e('Additional price depend number of extra page breaks', 'web-to-print-online-designer'); ?></b>
                    <nbd-tip data-tip="<?php _e('Number of extra pages = ( Number of pages ) - ( Default number of pages )', 'web-to-print-online-designer'); ?>" ></nbd-tip>
                </label>
            </div>
        </div>
        <div class="nbd-field-info-2">
            <select name="options[fields][{{fieldIndex}}][general][price_depend_no]" ng-model="field.general.price_depend_no">
                <option value="n"><?php _e('No', 'web-to-print-online-designer'); ?></option>
                <option value="y"><?php _e('Yes', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
    <div class="nbd-field-info" ng-if="field.general.price_depend_no == 'y'">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Number of page breaks:', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <table class="nbd-table">
                <thead>
                    <tr>
                        <th><?php _e('No of Page break', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Additional price', 'web-to-print-online-designer'); ?></th>
                        <th><?php _e('Action', 'web-to-print-online-designer'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr ng-repeat="(pnrIndex, pnr) in field.general.price_no_range">
                        <td><input type="number" string-to-number class="nbd-short-ip" min="1" step="1" name="options[fields][{{fieldIndex}}][general][price_no_range][{{pnrIndex}}][0]" ng-model="pnr[0]" /></td>
                        <td><input class="nbd-short-ip" type="text" name="options[fields][{{fieldIndex}}][general][price_no_range][{{pnrIndex}}][1]" ng-model="pnr[1]" /></td>
                        <td><a class="button nbd-mini-btn" ng-click="delete_nop_break(fieldIndex, pnrIndex)" title="<?php _e('Delete', 'web-to-print-online-designer'); ?>"><span class="dashicons dashicons-no-alt"></span></a></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">
                            <button ng-click="add_more_nop_break(fieldIndex)" style="float: left;" type="button" class="button button-primary"><span class="dashicons dashicons-plus"></span> <?php _e( 'Add more', 'web-to-print-online-designer' ); ?></button>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<?php echo '</script>';