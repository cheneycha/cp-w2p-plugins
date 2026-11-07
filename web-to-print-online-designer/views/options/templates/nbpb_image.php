<?php if (!defined('ABSPATH')) exit; ?>
<?php echo '<script type="text/ng-template" id="nbd.nbpb_image">'; ?>
    <div class="nbd-field-info">
        <div class="nbd-field-info-1">
            <div><b><?php _e('Show in view', 'web-to-print-online-designer'); ?></b></div>
        </div>
        <div class="nbd-field-info-2">
            <div class="nbd-table-wrap">
                <table class="nbd-table" style="text-align: center;">
                    <thead>
                        <tr>
                            <th ng-repeat="view in options.views">{{view.name}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td ng-repeat="view in options.views">
                                <input ng-model="field.general.nbpb_image_configs.views[$index].display" name="options[fields][{{fieldIndex}}][general][nbpb_image_configs][views][{{$index}}][display]" type="checkbox" />
                            </td>
                        </tr>    
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php echo '</script>';