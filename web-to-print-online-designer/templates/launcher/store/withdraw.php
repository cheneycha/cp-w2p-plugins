<?php
    if (!defined('ABSPATH')) exit;
?>
<h2 class="nbdl-store-dashboard-head"><?php _e('Withdraw', 'web-to-print-online-designer'); ?></h2>
<div class="nbdl-current-balance">
    <div>
        <?php esc_html_e('Current Balance:', 'web-to-print-online-designer'); ?>
        <?php echo $balance_display; ?>
    </div>
    <div>
        <?php esc_html_e('Minimum Withdraw amount:', 'web-to-print-online-designer'); ?>
        <?php echo $min_withdraw; ?>
    </div>
</div>
<div class="nbdl-withdraw-wrap">
    <div class="nbdl-toggle-wrap">
        <a class="nbdl-toggle" data-target="nbdl-withdraw-request"><?php esc_html_e('Withdraw Request', 'web-to-print-online-designer'); ?></a>
        <a class="nbdl-toggle" data-target="nbdl-withdraw-approved"><?php esc_html_e('Approved Requests', 'web-to-print-online-designer'); ?></a>
        <a class="nbdl-toggle" data-target="nbdl-withdraw-cancelled"><?php esc_html_e('Cancelled Requests', 'web-to-print-online-designer'); ?></a>
    </div>
    <div class="nbdl-toggle-panel" id="nbdl-withdraw-request">
        <?php if( ! $has_withdraw_balance ): 
            nbdesigner_render_template("launcher/store/notification.php", array(
                'type'      => 'error',
                'message'   => __( 'You don\'t have sufficient balance for a withdraw request!', 'web-to-print-online-designer' )
            ));
        elseif( $has_pending_request ): 
            $pending_warning = sprintf( '<div>%s</div><div>%s</div>', __( 'You already have pending withdraw request(s).', 'web-to-print-online-designer' ), __( 'Please submit your request after approval or cancellation of your previous request.', 'web-to-print-online-designer' ) );
            nbdesigner_render_template("launcher/store/notification.php", array(
                'type'      => 'error',
                'message'   => $pending_warning
            ));
            nbdesigner_render_template("launcher/store/withdraw-table.php", array(
                'requests'  => $pending_requests
            ));
        else:
            nbdesigner_render_template("launcher/store/withdraw-form.php");
        endif;?>
    </div>
    <div class="nbdl-toggle-panel" id="nbdl-withdraw-approved">
        <?php 
            nbdesigner_render_template("launcher/store/withdraw-table.php", array(
                'requests'  => $approved_requests ? $approved_requests : array()
            ));
        ?>
    </div>
    <div class="nbdl-toggle-panel" id="nbdl-withdraw-cancelled">
        <?php 
            nbdesigner_render_template("launcher/store/withdraw-table.php", array(
                'requests'  => $cancelled_requests ? $cancelled_requests : array()
            ));
        ?>
    </div>
</div>