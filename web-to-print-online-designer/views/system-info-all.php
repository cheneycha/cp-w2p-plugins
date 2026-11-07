<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
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
    .phpinfo img {
        float: right;
        border: 0;
    }
</style>
<div class="system-info">
    <h1><?php esc_html_e('PHP Info', 'web-to-print-online-designer'); ?></h1>
<?php
    $full_info_link = admin_url( 'admin.php?page=nbdesigner_system_info' );

    ob_start();
    phpinfo();
    $pinfo = ob_get_contents();
    ob_end_clean();

    $pinfo = preg_replace( '%^.*<div class="center">(.*)</div>.*$%ms', '$1', $pinfo );
    $pinfo = preg_replace( '%(^.*)<a name=\".*\">(.*)</a>(.*$)%m', '$1$2$3', $pinfo );
    $pinfo = str_replace( '<table>', '<table class="widefat striped phpinfo">', $pinfo );
    $pinfo = str_replace( '<td class="e">', '<th class="e">', $pinfo );
    echo $pinfo;
?>
</div>