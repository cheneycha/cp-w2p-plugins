<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    $full_info_link = add_query_arg(
        array( 'mode' => 'all' ), 
        admin_url( 'admin.php?page=nbdesigner_system_info' )
    );
    $system_info = nbd_get_system_info();
?>
<style type="text/css">
    .system-info table {
        margin: 20px 0;
    }
    .system-info table th {
        font-weight: bold;
        width: 25%;
        padding: 20px 12px;
    }
    .system-info table td {
        word-break: break-all;
        padding: 20px 12px;
    }
    .system-info table td span{
        margin-right: 5px;
    }
    .system-info table td.good{
        color: #7ad03a;
    }
    .system-info table td.bad{
        color: #a00
    }
</style>
<div class="system-info">
    <h1><?php esc_html_e('System Info', 'web-to-print-online-designer'); ?></h1>
    <div>
        <table class="widefat striped">
            <tbody>
                <?php foreach( $system_info as $info ): ?>
                <tr>
                    <th><?php echo $info['label']; ?></th>
                    <td class="<?php echo $info['class']; ?>">
                        <?php if( $info['class'] == 'good' ): ?>
                            <span class="dashicons dashicons-yes"></span>
                        <?php elseif( $info['class'] == 'bad'): ?>
                            <span class="dashicons dashicons-no-alt"></span>
                        <?php endif ?>
                        <?php echo $info['value']; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div>
        <a href="<?php echo $full_info_link; ?>"><?php esc_html_e('View full PHP info', 'web-to-print-online-designer'); ?></a>
    </div>
</div>