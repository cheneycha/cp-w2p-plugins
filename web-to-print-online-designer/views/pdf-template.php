<html>
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="Content-type" content="text/html; charset=utf-8">
        <title>NBDesigner</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=1, minimum-scale=0.5, maximum-scale=1.0"/>
        <link rel="stylesheet" type="text/css" href="https://cloud2print.s3.amazonaws.com/normalize.css" />
        <?php echo $font_css['google_font_link']; ?>
        <?php echo $font_css['custom_font_style']; ?>
        <style type="text/css">
            @page {
                margin: 0;
                padding: 0;
                size: <?php echo $page_settings['width']; ?> <?php echo $page_settings['height']; ?>;
            }
            body {
                width: <?php echo $page_settings['width']; ?>;
                height: <?php echo $page_settings['height']; ?>;
                position: relative;
                <?php if( $page_settings['include_bg'] && $page_settings['bg_type'] == 'color' ): ?>
                background-color: <?php echo $page_settings['bg_color']; ?>;
                <?php endif; ?>
                font-size: 0;
                font-family: sans-serif;
            }
            svg {
                position: absolute;
                width: <?php echo $page_settings['design_width']; ?>;
                height: <?php echo $page_settings['design_height']; ?>;
                top: <?php echo $page_settings['design_top']; ?>;
                left: <?php echo $page_settings['design_left']; ?>;
                z-index: 2;
                max-width: 100%;
                max-height: 100%;
            }
            #background {
                z-index: 1;
            }
            #overlay, #background {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
            }
            #overlay {
                z-index: 3;
            }
            #watermark {
                position: absolute;
                top: 50%;
                left: 50%;
                max-width: 50%;
                max-height: 50%;
                -webkit-transform: translate(-50%, -50%) rotate(-45deg);
                transform: translate(-50%, -50%) rotate(-45deg);
                opacity: 0.7;
                z-index: 4;
            }
            #watermark.text {
                transform-origin: center;
                font-size: 20pt;
                margin: 0;
                color: #ddd;
            }
        </style>
    </head>
    <body>
        <?php if( $page_settings['include_bg'] && $page_settings['bg_type'] == 'image' ): ?>
        <img id="background" src="<?php echo $page_settings['bg_src']; ?>" />
        <?php endif; ?>
        <?php echo $svg_string; ?>
        <?php if( $page_settings['include_ov'] ): ?>
        <img id="overlay" src="<?php echo $page_settings['ov_src']; ?>" />
        <?php endif; ?>
        <?php if( isset( $page_settings['contour'] ) ) echo $page_settings['contour']; ?>
        <?php if( $page_settings['watermark'] && $page_settings['watermark_type'] == 1 ): ?>
        <img id="watermark" src="<?php echo $page_settings['wm_src']; ?>" />
        <?php endif; ?>
        <?php if( $page_settings['watermark'] && $page_settings['watermark_type'] != 1 ): ?>
        <p id="watermark" class="text"><?php echo $page_settings['wm_text']; ?></p>
        <?php endif; ?>
    </body>
</html>