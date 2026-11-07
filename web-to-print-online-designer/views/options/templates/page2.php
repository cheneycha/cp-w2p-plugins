<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="nbd.page2">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div>
                <b><?php _e('Auto select all pages/sides', 'web-to-print-online-designer'); ?></b>
                <nbd-tip data-tip="<?php _e('Automatically select all pages/sides if choose Yes. In other side, the Default page/side or the first page/side will be selected.', 'web-to-print-online-designer'); ?>" ></nbd-tip>
            </div>
        </div>
        <div class="nbd-field-info-2">
            <select name="options[fields][{{fieldIndex}}][general][auto_select_page]" ng-model="field.general.auto_select_page">
                <option value="y"><?php _e('Yes', 'web-to-print-online-designer'); ?></option>
                <option value="n"><?php _e('No', 'web-to-print-online-designer'); ?></option>
            </select>
        </div>
    </div>
<?php echo '</script>';