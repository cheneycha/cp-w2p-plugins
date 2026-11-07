<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<?php if ($mode3Task != '') :?>
    <div class="save-template" style="text-align: right; margin-bottom: 30px">
        <?php if($mode3Task == 'create' && current_user_can('edit_nbd_template')): ?>
        <button class="btn v-btn" ng-click="saveData()"><?php _e('Save Template','web-to-print-online-designer'); ?></button>
        <?php else: ?>
        <button class="btn v-btn" ng-click="saveData()"><?php _e('Save','web-to-print-online-designer'); ?></button>
        <?php endif; ?>
    </div>
<?php endif; ?>