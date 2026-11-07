<?php if (!defined('ABSPATH')) exit; ?>
<div class="nbo-detail-popup-wrap" id="nbo-detail-popup-wrap">
    <div class="popup-inner">
        <div class="main-popup">
            <div class="nbo-detail-popup-header">
                <?php esc_html_e('Choose options', 'web-to-print-online-designer'); ?>
            </div>
            <div class="nbo-detail-popup-body">
            <?php 
                foreach( $popup_fields as $field ){
                    $class = $field['class'];
                    if( $field['general']['enabled'] == 'y' && $field['need_show'] ) include( $field['template'] );
                }
            ?>
            </div>
            <div class="nbo-detail-popup-footer">
                <a class="button nbdesign-button" id="nbo-sumit-popup-action"><?php esc_html_e('Submit', 'web-to-print-online-designer'); ?></a>
            </div>
        </div>
    </div>
</div>