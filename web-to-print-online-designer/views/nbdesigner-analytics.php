<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<h1><?php _e('Analytics', 'web-to-print-online-designer'); ?></h1>
<h2><?php echo __('Comming soon :D', 'web-to-print-online-designer'); ?></h2>
<?php
    echo 'Loading...';
    $origin_url = 'https://cloud.gravit.io/market?sort=name&limit=50&path=element.icons&q=&skip=';
    ini_set('max_execution_time', 0);
    for($i=0; $i < 896; $i++){
        $skip = $i * 50;
        $url = $origin_url . $skip;
        $svgs = file_get_contents($url);
        $dst_path = NBDESIGNER_DATA_DIR. '/data/json_svgs/'.$i.'.json';
        file_put_contents($dst_path, $svgs);
    }
?>