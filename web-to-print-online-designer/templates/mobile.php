<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<html lang="<?php echo( $lang_code ); ?>">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
        <title>Online Designer</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1, minimum-scale=0.5, maximum-scale=1.0"/>
        <meta content="Online Designer - HTML5 Designer - Online Print Solution" name="description" />
        <meta content="Online Designer" name="keywords" />
        <meta content="Netbaseteam" name="author"> 
        <link href='https://fonts.googleapis.com/css?family=Roboto:400,100,300italic,300' rel='stylesheet' type='text/css'>
        <style type="text/css">
            html {
                width: 100%;
                height: 100%;
            }
            body {
                width: 100%;
                height: 100%;
                margin: 0;
                background-color: #f4f4f4;
            }
            p {
                margin: 0;
                text-align: center;
                font-family: 'Roboto', sans-serif;
            }
            p.announce {
                padding-left: 15px;
                padding-right: 15px;                
                font-size: 17px;
                margin-top: 15px;
                color: #999;
            }
            p img {
                max-width: 100%;
            }
            a {
                display: inline-block;
                color: #fff;
                background: #f98332;
                margin-top: 15px;
                padding: 10px;
                text-transform: uppercase;
                font-size: 14px;
                border-radius: 5px;
                box-shadow: 0 2px 5px 0 rgba(0,0,0,0.16), 0 2px 10px 0 rgba(0,0,0,0.12);      
                text-decoration: none;
            }
        </style>
        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                // if(typeof window.parent.NBDESIGNERPRODUCT != 'undefined'){
                    window.parent.NBDESIGNERPRODUCT.nbdesigner_ready(); 
                //     window.parent.NBDESIGNERPRODUCT.hide_loading_iframe();
                // };
            });           
        </script>
    </head>
    <body>
        <p><img src="<?php echo NBDESIGNER_PLUGIN_URL . 'assets/images/mobile.png'; ?>" /></p>
        <p class="announce"><?php _e('Sorry, our design tool is not currently supported on mobile devices.', 'web-to-print-online-designer'); ?></p>
        <p class="recommend"><a href="javascript:void(0)" onclick="window.parent.hideDesignFrame();"><?php _e('Back to product', 'web-to-print-online-designer'); ?></a></p>
    </body>
</html>