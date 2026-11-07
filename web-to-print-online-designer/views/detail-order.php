<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly ?>
<?php 
    $output_format = array(
        'ISO 216 A Series + 2 SIS 014711 extensions' => array('A0'=>'A0 (841x1189 mm ; 33.11x46.81 in)','A1'=>'A1 (594x841 mm ; 23.39x33.11 in)','A2'=>'A2 (420x594 mm ; 16.54x23.39 in)','A3'=>'A3 (297x420 mm ; 11.69x16.54 in)','A4'=>'A4 (210x297 mm ; 8.27x11.69 in)','A5'=>'A5 (148x210 mm ; 5.83x8.27 in)','A6'=>'A6 (105x148 mm ; 4.13x5.83 in)','A7'=>'A7 (74x105 mm ; 2.91x4.13 in)','A8'=>'A8 (52x74 mm ; 2.05x2.91 in)','A9'=>'A9 (37x52 mm ; 1.46x2.05 in)','A10'=>'A10 (26x37 mm ; 1.02x1.46 in)','A11'=>'A11 (18x26 mm ; 0.71x1.02 in)','A12'=>'A12 (13x18 mm ; 0.51x0.71 in)'),
        'ISO 216 B Series + 2 SIS 014711 extensions' => array('B0'=>'B0 (1000x1414 mm ; 39.37x55.67 in)','B1'=>'B1 (707x1000 mm ; 27.83x39.37 in)','B2'=>'B2 (500x707 mm ; 19.69x27.83 in)','B3'=>'B3 (353x500 mm ; 13.90x19.69 in)','B4'=>'B4 (250x353 mm ; 9.84x13.90 in)','B5'=>'B5 (176x250 mm ; 6.93x9.84 in)','B6'=>'B6 (125x176 mm ; 4.92x6.93 in)','B7'=>'B7 (88x125 mm ; 3.46x4.92 in)','B8'=>'B8 (62x88 mm ; 2.44x3.46 in)','B9'=>'B9 (44x62 mm ; 1.73x2.44 in)','B10'=>'B10 (31x44 mm ; 1.22x1.73 in)','B11'=>'B11 (22x31 mm ; 0.87x1.22 in)','B12'=>'B12 (15x22 mm ; 0.59x0.87 in)'),
        'ISO 216 C Series + 2 SIS 014711 extensions + 2 EXTENSION' => array('C0'=>'C0 (917x1297 mm ; 36.10x51.06 in)','C1'=>'C1 (648x917 mm ; 25.51x36.10 in)','C2'=>'C2 (458x648 mm ; 18.03x25.51 in)','C3'=>'C3 (324x458 mm ; 12.76x18.03 in)','C4'=>'C4 (229x324 mm ; 9.02x12.76 in)','C5'=>'C5 (162x229 mm ; 6.38x9.02 in)','C6'=>'C6 (114x162 mm ; 4.49x6.38 in)','C7'=>'C7 (81x114 mm ; 3.19x4.49 in)','C8'=>'C8 (57x81 mm ; 2.24x3.19 in)','C9'=>'C9 (40x57 mm ; 1.57x2.24 in)','C10'=>'C10 (28x40 mm ; 1.10x1.57 in)','C11'=>'C11 (20x28 mm ; 0.79x1.10 in)','C12'=>'C12 (14x20 mm ; 0.55x0.79 in)','C76'=>'C76 (81x162 mm ; 3.19x6.38 in)','DL'=>'DL (110x220 mm ; 4.33x8.66 in)'),
        'SIS 014711 E Series' => array('E0'=>'E0 (879x1241 mm ; 34.61x48.86 in)','E1'=>'E1 (620x879 mm ; 24.41x34.61 in)','E2'=>'E2 (440x620 mm ; 17.32x24.41 in)','E3'=>'E3 (310x440 mm ; 12.20x17.32 in)','E4'=>'E4 (220x310 mm ; 8.66x12.20 in)','E5'=>'E5 (155x220 mm ; 6.10x8.66 in)','E6'=>'E6 (110x155 mm ; 4.33x6.10 in)','E7'=>'E7 (78x110 mm ; 3.07x4.33 in)','E8'=>'E8 (55x78 mm ; 2.17x3.07 in)','E9'=>'E9 (39x55 mm ; 1.54x2.17 in)','E10'=>'E10 (27x39 mm ; 1.06x1.54 in)','E11'=>'E11 (19x27 mm ; 0.75x1.06 in)','E12'=>'E12 (13x19 mm ; 0.51x0.75 in)'),
        'SIS 014711 G Series' => array('G0'=>'G0 (958x1354 mm ; 37.72x53.31 in)','G1'=>'G1 (677x958 mm ; 26.65x37.72 in)','G2'=>'G2 (479x677 mm ; 18.86x26.65 in)','G3'=>'G3 (338x479 mm ; 13.31x18.86 in)','G4'=>'G4 (239x338 mm ; 9.41x13.31 in)','G5'=>'G5 (169x239 mm ; 6.65x9.41 in)','G6'=>'G6 (119x169 mm ; 4.69x6.65 in)','G7'=>'G7 (84x119 mm ; 3.31x4.69 in)','G8'=>'G8 (59x84 mm ; 2.32x3.31 in)','G9'=>'G9 (42x59 mm ; 1.65x2.32 in)','G10'=>'G10 (29x42 mm ; 1.14x1.65 in)','G11'=>'G11 (21x29 mm ; 0.83x1.14 in)','G12'=>'G12 (14x21 mm ; 0.55x0.83 in)'),
        'ISO Press' => array('RA0'=>'RA0 (860x1220 mm ; 33.86x48.03 in)','RA1'=>'RA1 (610x860 mm ; 23.02x33.86 in)','RA2'=>'RA2 (430x610 mm ; 16.93x23.02 in)','RA3'=>'RA3 (305x430 mm ; 12.01x16.93 in)','RA4'=>'RA4 (215x305 mm ; 8.46x12.01 in)','SRA0'=>'SRA0 (900x1280 mm ; 35.43x50.39 in)','SRA1'=>'SRA1 (640x900 mm ; 25.20x35.43 in)','SRA2'=>'SRA2 (450x640 mm ; 17.72x25.20 in)','SRA3'=>'SRA3 (320x450 mm ; 12.60x17.72 in)','SRA4'=>'SRA4 (225x320 mm ; 8.86x12.60 in)'),
        'German DIN 476' => array('4A0'=>'4A0 (1682x2378 mm ; 66.22x93.62 in)','2A0'=>'2A0 (1189x1682 mm ; 46.81x66.22 in)'),
        'Variations on the ISO Standard' => array('A2_EXTRA'=>'A2_EXTRA (445x619 mm ; 17.52x24.37 in)','A3+'=>'A3+ (329x483 mm ; 12.95x19.02 in)','A3_EXTRA'=>'A3_EXTRA (322x445 mm ; 12.68x17.52 in)','A3_SUPER'=>'A3_SUPER (305x508 mm ; 12.01x20.00 in)','SUPER_A3'=>'SUPER_A3 (305x487 mm ; 12.01x19.17 in)','A4_EXTRA'=>'A4_EXTRA (235x322 mm ; 9.25x12.68 in)','A4_SUPER'=>'A4_SUPER (229x322 mm ; 9.02x12.68 in)','SUPER_A4'=>'SUPER_A4 (227x356 mm ; 8.94x13.02 in)','A4_LONG'=>'A4_LONG (210x348 mm ; 8.27x13.70 in)','F4'=>'F4 (210x330 mm ; 8.27x12.99 in)','SO_B5_EXTRA'=>'SO_B5_EXTRA (202x276 mm ; 7.95x10.87 in)','A5_EXTRA'=>'A5_EXTRA (173x235 mm ; 6.81x9.25 in)'),
        'ANSI Series' => array('ANSI_E'=>'ANSI_E (864x1118 mm ; 33.00x43.00 in)','ANSI_D'=>'ANSI_D (559x864 mm ; 22.00x33.00 in)','ANSI_C'=>'ANSI_C (432x559 mm ; 17.00x22.00 in)','ANSI_B'=>'ANSI_B (279x432 mm ; 11.00x17.00 in)','ANSI_A'=>'ANSI_A (216x279 mm ; 8.50x11.00 in)'),
        'Traditional "Loose" North American Paper Sizes' => array('LEDGER, USLEDGER'=>'LEDGER, USLEDGER (432x279 mm ; 17.00x11.00 in)','TABLOID, USTABLOID, BIBLE, ORGANIZERK'=>'TABLOID, USTABLOID, BIBLE, ORGANIZERK (279x432 mm ; 11.00x17.00 in)','LETTER, USLETTER, ORGANIZERM'=>'LETTER, USLETTER, ORGANIZERM (216x279 mm ; 8.50x11.00 in)','LEGAL, USLEGAL'=>'LEGAL, USLEGAL (216x356 mm ; 8.50x13.00 in)','GLETTER, GOVERNMENTLETTER'=>'GLETTER, GOVERNMENTLETTER (203x267 mm ; 8.00x10.50 in)','JLEGAL, JUNIORLEGAL'=>'JLEGAL, JUNIORLEGAL (203x127 mm ; 8.00x5.00 in)'),
        'Other North American Paper Sizes' => array('QUADDEMY'=>'QUADDEMY (889x1143 mm ; 35.00x45.00 in)','SUPER_B'=>'SUPER_B (330x483 mm ; 13.00x19.00 in)','QUARTO'=>'QUARTO (229x279 mm ; 9.00x11.00 in)','FOLIO, GOVERNMENTLEGAL'=>'FOLIO, GOVERNMENTLEGAL (216x330 mm ; 8.50x13.00 in)','EXECUTIVE, MONARCH'=>'EXECUTIVE, MONARCH (184x267 mm ; 7.25x10.50 in)','MEMO, STATEMENT, ORGANIZERL'=>'MEMO, STATEMENT, ORGANIZERL (140x216 mm ; 5.50x8.50 in)','FOOLSCAP'=>'FOOLSCAP (210x330 mm ; 8.27x13.00 in)','COMPACT'=>'COMPACT (108x171 mm ; 4.25x6.75 in)','ORGANIZERJ'=>'ORGANIZERJ (70x127 mm ; 2.75x5.00 in)'),
        'Canadian standard CAN 2-9.60M' => array('P1'=>'P1 (560x860 mm ; 22.05x33.86 in)','P2'=>'P2 (430x560 mm ; 16.93x22.05 in)','P3'=>'P3 (280x430 mm ; 11.02x16.93 in)','P4'=>'P4 (215x280 mm ; 8.46x11.02 in)','P5'=>'P5 (140x215 mm ; 5.51x8.46 in)','P6'=>'P6 (107x140 mm ; 4.21x5.51 in)'),
        'North American Architectural Sizes' => array('ARCH_E'=>'ARCH_E (914x1219 mm ; 36.00x48.00 in)','ARCH_E1'=>'ARCH_E1 (762x1067 mm ; 30.00x42.00 in)','ARCH_D'=>'ARCH_D (610x914 mm ; 23.00x36.00 in)','ARCH_C, BROADSHEET'=>'ARCH_C, BROADSHEET (457x610 mm ; 18.00x23.00 in)','ARCH_B'=>'ARCH_B (305x457 mm ; 12.00x18.00 in)','ARCH_A'=>'ARCH_A (229x305 mm ; 9.00x12.00 in)'),
        'Announcement Envelopes' => array('ANNENV_A2'=>'ANNENV_A2 (111x146 mm ; 4.37x5.75 in)','ANNENV_A6'=>'ANNENV_A6 (121x165 mm ; 4.75x6.50 in)','ANNENV_A7'=>'ANNENV_A7 (133x184 mm ; 5.25x7.25 in)','ANNENV_A8'=>'ANNENV_A8 (140x206 mm ; 5.50x8.12 in)','ANNENV_A10'=>'ANNENV_A10 (159x244 mm ; 6.25x9.62 in)','ANNENV_SLIM'=>'ANNENV_SLIM (98x225 mm ; 3.87x8.87 in)'),
        'Commercial Envelopes' => array('COMMENV_N6_1/4'=>'COMMENV_N6_1/4 (89x152 mm ; 3.50x6.00 in)','COMMENV_N6_3/4'=>'COMMENV_N6_3/4 (92x165 mm ; 3.62x6.50 in)','COMMENV_N8'=>'COMMENV_N8 (98x191 mm ; 3.87x7.50 in)','COMMENV_N9'=>'COMMENV_N9 (98x225 mm ; 3.87x8.87 in)','COMMENV_N10'=>'COMMENV_N10 (105x241 mm ; 4.12x9.50 in)','COMMENV_N11'=>'COMMENV_N11 (114x263 mm ; 4.50x10.37 in)','COMMENV_N12'=>'COMMENV_N12 (121x279 mm ; 4.75x11.00 in)','COMMENV_N14'=>'COMMENV_N14 (127x292 mm ; 5.00x11.50 in)'),
        'Catalogue Envelopes' => array('CATENV_N1'=>'CATENV_N1 (152x229 mm ; 6.00x9.00 in)','CATENV_N1_3/4'=>'CATENV_N1_3/4 (165x241 mm ; 6.50x9.50 in)','CATENV_N2'=>'CATENV_N2 (165x254 mm ; 6.50x10.00 in)','CATENV_N3'=>'CATENV_N3 (178x254 mm ; 7.00x10.00 in)','CATENV_N6'=>'CATENV_N6 (191x267 mm ; 7.50x10.50 in)','CATENV_N7'=>'CATENV_N7 (203x279 mm ; 8.00x11.00 in)','CATENV_N8'=>'CATENV_N8 (210x286 mm ; 8.25x11.25 in)','CATENV_N9_1/2'=>'CATENV_N9_1/2 (216x267 mm ; 8.50x10.50 in)','CATENV_N9_3/4'=>'CATENV_N9_3/4 (222x286 mm ; 8.75x11.25 in)','CATENV_N10_1/2'=>'CATENV_N10_1/2 (229x305 mm ; 9.00x12.00 in)','CATENV_N12_1/2'=>'CATENV_N12_1/2 (241x318 mm ; 9.50x12.50 in)','CATENV_N13_1/2'=>'CATENV_N13_1/2 (254x330 mm ; 10.00x13.00 in)','CATENV_N14_1/4'=>'CATENV_N14_1/4 (286x311 mm ; 11.25x12.25 in)','CATENV_N14_1/2'=>'CATENV_N14_1/2 (292x368 mm ; 11.50x14.50 in)'),
        'Japanese' => array('Japanese'=>'Japanese (JIS P 0138-61) Standard B-Series','JIS_B0'=>'JIS_B0 (1030x1456 mm ; 40.55x57.32 in)','JIS_B1'=>'JIS_B1 (728x1030 mm ; 28.66x40.55 in)','JIS_B2'=>'JIS_B2 (515x728 mm ; 20.28x28.66 in)','JIS_B3'=>'JIS_B3 (364x515 mm ; 14.33x20.28 in)','JIS_B4'=>'JIS_B4 (257x364 mm ; 10.12x14.33 in)','JIS_B5'=>'JIS_B5 (182x257 mm ; 7.17x10.12 in)','JIS_B6'=>'JIS_B6 (128x182 mm ; 5.04x7.17 in)','JIS_B7'=>'JIS_B7 (91x128 mm ; 3.58x5.04 in)','JIS_B8'=>'JIS_B8 (64x91 mm ; 2.52x3.58 in)','JIS_B9'=>'JIS_B9 (45x64 mm ; 1.77x2.52 in)','JIS_B10'=>'JIS_B10 (32x45 mm ; 1.26x1.77 in)','JIS_B11'=>'JIS_B11 (22x32 mm ; 0.87x1.26 in)','JIS_B12'=>'JIS_B12 (16x22 mm ; 0.63x0.87 in)'),
        'PA Series' => array('PA0'=>'PA0 (840x1120 mm ; 33.07x43.09 in)','PA1'=>'PA1 (560x840 mm ; 22.05x33.07 in)','PA2'=>'PA2 (420x560 mm ; 16.54x22.05 in)','PA3'=>'PA3 (280x420 mm ; 11.02x16.54 in)','PA4'=>'PA4 (210x280 mm ; 8.27x11.02 in)','PA5'=>'PA5 (140x210 mm ; 5.51x8.27 in)','PA6'=>'PA6 (105x140 mm ; 4.13x5.51 in)','PA7'=>'PA7 (70x105 mm ; 2.76x4.13 in)','PA8'=>'PA8 (52x70 mm ; 2.05x2.76 in)','PA9'=>'PA9 (35x52 mm ; 1.38x2.05 in)','PA10'=>'PA10 (26x35 mm ; 1.02x1.38 in)'),
        'Standard Photographic Print Sizes' => array('PASSPORT_PHOTO'=>'PASSPORT_PHOTO (35x45 mm ; 1.38x1.77 in)','E'=>'E (82x120 mm ; 3.25x4.72 in)','3R, L'=>'3R, L (89x127 mm ; 3.50x5.00 in)','4R, KG'=>'4R, KG (102x152 mm ; 3.02x5.98 in)','4D'=>'4D (120x152 mm ; 4.72x5.98 in)','5R, 2L'=>'5R, 2L (127x178 mm ; 5.00x7.01 in)','6R, 8P'=>'6R, 8P (152x203 mm ; 5.98x7.99 in)','8R, 6P'=>'8R, 6P (203x254 mm ; 7.99x10.00 in)','S8R, 6PW'=>'S8R, 6PW (203x305 mm ; 7.99x12.01 in)','10R, 4P'=>'10R, 4P (254x305 mm ; 10.00x12.01 in)','S10R, 4PW'=>'S10R, 4PW (254x381 mm ; 10.00x15.00 in)','11R'=>'11R (279x356 mm ; 10.98x13.02 in)','S11R'=>'S11R (279x432 mm ; 10.98x17.01 in)','12R'=>'12R (305x381 mm ; 12.01x15.00 in)','S12R'=>'S12R (305x456 mm ; 12.01x17.95 in)'),
        'Common Newspaper Sizes' => array('NEWSPAPER_BROADSHEET'=>'NEWSPAPER_BROADSHEET (750x600 mm ; 29.53x23.62 in)','NEWSPAPER_BERLINER'=>'NEWSPAPER_BERLINER (470x315 mm ; 18.50x12.40 in)','NEWSPAPER_COMPACT, NEWSPAPER_TABLOID'=>'NEWSPAPER_COMPACT, NEWSPAPER_TABLOID (430x280 mm ; 16.93x11.02 in)'),
        'Business Cards' => array('CREDIT_CARD, BUSINESS_CARD, BUSINESS_CARD_ISO7810'=>'CREDIT_CARD, BUSINESS_CARD, BUSINESS_CARD_ISO7810 (54x86 mm ; 2.13x3.37 in)','BUSINESS_CARD_ISO216'=>'BUSINESS_CARD_ISO216 (52x74 mm ; 2.05x2.91 in)','BUSINESS_CARD_IT, BUSINESS_CARD_UK, BUSINESS_CARD_FR, BUSINESS_CARD_DE, BUSINESS_CARD_ES'=>'BUSINESS_CARD_IT, BUSINESS_CARD_UK, BUSINESS_CARD_FR, BUSINESS_CARD_DE, BUSINESS_CARD_ES (55x85 mm ; 2.17x3.35 in)','BUSINESS_CARD_US, BUSINESS_CARD_CA'=>'BUSINESS_CARD_US, BUSINESS_CARD_CA (51x89 mm ; 2.01x3.50 in)','BUSINESS_CARD_JP'=>'BUSINESS_CARD_JP (55x91 mm ; 2.17x3.58 in)','BUSINESS_CARD_HK'=>'BUSINESS_CARD_HK (54x90 mm ; 2.13x3.54 in)','BUSINESS_CARD_AU, BUSINESS_CARD_DK, BUSINESS_CARD_SE'=>'BUSINESS_CARD_AU, BUSINESS_CARD_DK, BUSINESS_CARD_SE (55x90 mm ; 2.17x3.54 in)','BUSINESS_CARD_RU, BUSINESS_CARD_CZ, BUSINESS_CARD_FI, BUSINESS_CARD_HU, BUSINESS_CARD_IL'=>'BUSINESS_CARD_RU, BUSINESS_CARD_CZ, BUSINESS_CARD_FI, BUSINESS_CARD_HU, BUSINESS_CARD_IL (50x90 mm ; 1.97x3.54 in)')
    );

    // Safely extract and sanitize GET parameters
    $nbd_item_key = isset($_GET['nbd_item_key']) ? sanitize_text_field($_GET['nbd_item_key']) : '';
    $order_id     = isset($_GET['order_id']) ? absint($_GET['order_id']) : 0;
    $product_id   = isset($_GET['product_id']) ? absint($_GET['product_id']) : 0;
    $variation_id = isset($_GET['variation_id']) ? absint($_GET['variation_id']) : 0;
    $item_id      = isset($_GET['item_id']) ? sanitize_text_field($_GET['item_id']) : '';

    $unit = isset( $option['unit'] ) ? $option['unit'] : (function_exists('nbdesigner_get_option') ? nbdesigner_get_option( 'nbdesigner_dimensions_unit', 'cm' ) : 'cm');
    $unitRatio = 10;
    $cm2Px = 37.795275591;
    $mm2Px = $cm2Px / 10;
    $dpi   = isset($option['dpi']) ? (float)$option['dpi'] : 300;
    $dpi   = $dpi > 0 ? $dpi : 300;

    switch ($unit) {
        case 'mm':
            $unitRatio = 1;
            break;
        case 'in':
            $unitRatio = 25.4;
            break;
        case 'ft':
            $unitRatio = 304.8;
            break;
        case 'px':
            $unitRatio = 25.4 / $dpi;
            break;
        default:
            $unitRatio = 10;
            break;
    }
?>
<div id="nbdesign-order-tabs">
    <h2>
        <?php 
            $arr = array(
                'nbd_item_key' => $nbd_item_key,
                'order_id'     => $order_id,
                'product_id'   => $product_id,
                'variation_id' => $variation_id,
                'item_id'      => $item_id
            );
            $redirect_link    = add_query_arg($arr, admin_url('admin.php?page=nbdesigner_detail_order'));
            $layout           = function_exists('nbd_get_product_layout') ? nbd_get_product_layout( $product_id ) : '';
            $create_url       = function_exists('getUrlPageNBD') ? getUrlPageNBD('create') : '';
            $edit_design_link = add_query_arg(
                array(
                    'task'          => 'edit',
                    'view'          => $layout,
                    'nbd_item_key'  => $nbd_item_key,
                    'design_type'   => 'edit_order',
                    'order_id'      => $order_id,
                    'item_id'       => $item_id,
                    'product_id'    => $product_id,
                    'variation_id'  => $variation_id,
                    'rd'            => 'admin_order'
                ),
                $create_url
            );

            if($layout === 'v'){
                $edit_design_link = add_query_arg(
                    array(
                        'nbdv-task'     => 'edit',
                        'task'          => 'edit',
                        'view'          => $layout,
                        'nbd_item_key'  => $nbd_item_key,
                        'design_type'   => 'edit_order',
                        'order_id'      => $order_id,
                        'item_id'       => $item_id,
                        'product_id'    => $product_id,
                        'variation_id'  => $variation_id,
                        'rd'            => 'admin_order'
                    ),
                    get_permalink($product_id)
                );
            }
            esc_html_e('Detail product design', 'web-to-print-online-designer'); 
        ?>
        <a class="button-primary" href="<?php echo esc_url( $edit_design_link ); ?>"><?php esc_html_e('Edit design', 'web-to-print-online-designer'); ?></a>
        <a class="button-primary nbdesigner-right" href="<?php echo admin_url('post.php?post=' . absint($order_id) . '&action=edit'); ?>"><?php esc_html_e('Back to order', 'web-to-print-online-designer'); ?></a>
    </h2>
        <ul class="nbd-nav-tab">
            <li><a href="#customer-design"><?php esc_html_e('Resource', 'web-to-print-online-designer') ?></a></li>
            <li><a href="#save-to-pdf"><?php esc_html_e('Create PDF', 'web-to-print-online-designer') ?></a></li>
            <li><a href="#convert-download"><?php esc_html_e('Download design', 'web-to-print-online-designer') ?></a></li>
            <li><a href="#pdf-layout"><?php esc_html_e('PDF Layout', 'web-to-print-online-designer') ?></a></li>
        </ul>
        <div id="pdf-layout">
            <div>
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label><?php esc_html_e('Paper Size', 'web-to-print-online-designer'); ?></label>
                            </th>
                            <td class="forminp forminp-text">
                                <input placeholder="<?php esc_html_e('width', 'web-to-print-online-designer'); ?>" name="layout[size][width]" type="text" class="nbd-order-input-60" value=""> 
                                <input placeholder="<?php esc_html_e('height', 'web-to-print-online-designer'); ?>" name="layout[size][height]" type="text" class="nbd-order-input-60" value=""> mm
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label><?php esc_html_e('Paper Padding', 'web-to-print-online-designer'); ?></label>
                            </th>
                            <td class="forminp forminp-text">
                                <input placeholder="<?php esc_html_e('top', 'web-to-print-online-designer'); ?>" name="layout[padding][top]" type="text" class="nbd-order-input-60" value=""> 
                                <input placeholder="<?php esc_html_e('right', 'web-to-print-online-designer'); ?>" name="layout[padding][right]" type="text" class="nbd-order-input-60" value=""> 
                                <input placeholder="<?php esc_html_e('bottom', 'web-to-print-online-designer'); ?>" name="layout[padding][bottom]" type="text" class="nbd-order-input-60" value=""> 
                                <input placeholder="<?php esc_html_e('left', 'web-to-print-online-designer'); ?>" name="layout[padding][left]" type="text" class="nbd-order-input-60" value=""> mm
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label><?php esc_html_e('Page Margin', 'web-to-print-online-designer'); ?></label>
                            </th>
                            <td class="forminp forminp-text">
                                <input placeholder="<?php esc_html_e('top', 'web-to-print-online-designer'); ?>" name="layout[margin][top]" type="text" class="nbd-order-input-60" value=""> 
                                <input placeholder="<?php esc_html_e('right', 'web-to-print-online-designer'); ?>" name="layout[margin][right]" type="text" class="nbd-order-input-60" value=""> 
                                <input placeholder="<?php esc_html_e('bottom', 'web-to-print-online-designer'); ?>" name="layout[margin][bottom]" type="text" class="nbd-order-input-60" value=""> 
                                <input placeholder="<?php esc_html_e('left', 'web-to-print-online-designer'); ?>" name="layout[margin][left]" type="text" class="nbd-order-input-60" value=""> mm
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label><?php esc_html_e('Page Order', 'web-to-print-online-designer'); ?></label>
                            </th>
                            <td class="forminp forminp-text">
                                <select name="layout[order]">
                                    <option value="sequentially" selected><?php esc_html_e('Sequentially', 'web-to-print-online-designer'); ?></option>
                                    <option value="offset"><?php esc_html_e('Offset', 'web-to-print-online-designer'); ?></option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <?php wp_nonce_field( 'nbdesigner_pdf_nonce'); ?>
                                <input type="hidden" name="layout[order_id]" value="<?php echo esc_attr( $order_id ); ?>" />
                                <input type="hidden" name="layout[nbd_item_key]" value="<?php echo esc_attr( $nbd_item_key ); ?>" />
                                <a href="javascript:void(0)" class="button-primary" id="create_layout_pdf"><?php esc_html_e('Create PDF', 'web-to-print-online-designer'); ?></a>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div id="customer-design">
            <div>
                <div>  
                    <p><span class="dashicons dashicons-images-alt2"></span><b><?php esc_html_e('Images', 'web-to-print-online-designer') ?></b></p>
                    <table class="nbd-resource-text">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Link', 'web-to-print-online-designer') ?></th>
                                <th><?php esc_html_e('Download', 'web-to-print-online-designer') ?></th>
                                <th><?php esc_html_e('Filter', 'web-to-print-online-designer') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                <?php if ( ! empty($resources) && is_iterable($resources) ): ?>
                    <?php foreach( $resources as $resource ): ?>
                        <?php 
                            if ( empty($resource->objects) || ! is_iterable($resource->objects) ) continue;
                            foreach( $resource->objects as $layer ):
                            if( isset($layer->type) && ($layer->type === 'image' || $layer->type === 'custom-image') ){
                                if( $layer->type === 'image' ){
                                    $src = ( isset( $layer->origin_url ) && $layer->origin_url ) ? $layer->origin_url : ( $layer->src ?? '' );
                                }else{
                                    $src = ( isset( $layer->origin_src ) && $layer->origin_src ) ? $layer->origin_src : ( $layer->src ?? '' );
                                }
                        ?>
                        <tr>
                            <td><a href="<?php echo esc_url( $src ); ?>" target="_blank" title="<?php esc_html_e('View', 'web-to-print-online-designer') ?>"><img style="width: 100px; height: auto" src="<?php echo esc_url( $src ); ?>" /></a></td>
                            <td><a class="nbd-download-resource-link" href="<?php echo esc_url( $src ); ?>" download title="<?php esc_html_e('Download', 'web-to-print-online-designer') ?>"><span class="dashicons dashicons-arrow-down-alt"></span></a></td>
                            <td>
                                <ul>
                                <?php 
                                if(isset($layer->filters) && is_array($layer->filters)) {
                                    foreach ($layer->filters as $filter) {
                                        if(isset($filter->jOptions)) {
                                            $jOptions = json_decode($filter->jOptions);
                                            $type   = $jOptions->type ?? '';
                                            $value  = isset($jOptions->value) ? (float) $jOptions->value : 0;
                                            $_ratio = 1;
                                            if($type === 'contrast1') {
                                                $type = 'contrast';
                                                $_ratio = 50;
                                            } else if($type === 'saturation') {
                                                $_ratio = 100;
                                            } else if($type === 'brightness') {
                                                $_ratio = 50;
                                            }
                                            echo '<li><b style="text-transform: capitalize;">' . esc_html($type) . ': </b><span>' . esc_html($value * $_ratio) . '</span></li>';
                                        }
                                    }
                                }
                                ?>
                                </ul>
                            </td>
                        </tr>
                        <?php } endforeach; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="nbd-tabbe-wrap">
                    <p><span class="dashicons dashicons-editor-textcolor"></span><b><?php esc_html_e('Texts', 'web-to-print-online-designer') ?></b></p>
                    <table class="nbd-resource-text">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Content', 'web-to-print-online-designer') ?></th>
                                <th><?php esc_html_e('Color', 'web-to-print-online-designer') ?></th>
                                <th><?php esc_html_e('Font Size', 'web-to-print-online-designer') ?></th>
                                <th><?php esc_html_e('Font Family', 'web-to-print-online-designer') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ( ! empty($resources) && is_iterable($resources) ): ?>
                            <?php foreach( $resources as $resource ): ?>
                                <?php 
                                    if ( empty($resource->objects) || ! is_iterable($resource->objects) ) continue;
                                    foreach( $resource->objects as $layer ): 
                                    if( isset($layer->type) && in_array($layer->type, array('text', 'i-text', 'curvedText', 'textbox')) ){ 
                                        $alias = $fontname = $layer->fontFamily ?? '';
                                        $fonturl = 'https://fonts.google.com/specimen/'.$fontname;
                                        $is_google_font = true;
                                        if ( ! empty($fonts) && is_iterable($fonts) ) {
                                            foreach ( $fonts as $font ){
                                                if( isset($font->alias) && $font->alias === $fontname ) {
                                                    $fontname = $font->name;
                                                    $fonturl  = ( strpos($font->url, 'http') !== false ) ? $font->url : NBDESIGNER_FONT_URL . $font->url;
                                                    $is_google_font = false;
                                                    break;
                                                }
                                            }
                                        }
                                        $fontSize = isset( $layer->ptFontSize ) ? $layer->ptFontSize . ' pt' : ($layer->fontSize ?? '');
                                ?>
                                <tr>
                                    <style type='text/css'>
                                        <?php if( !$is_google_font ): ?>
                                        @font-face {font-family: <?php echo esc_attr( $alias ); ?>;src: local('☺'), url('<?php echo esc_url( $fonturl ); ?>')}
                                        <?php else: ?>
                                        @import url(https://fonts.googleapis.com/css?family=<?php echo str_replace(' ', '+', esc_attr($alias)) ?>);
                                        <?php endif; ?>
                                    </style>
                                    <td style="font-family: <?php echo esc_attr( $alias ); ?>;"><?php echo esc_html( $layer->text ?? '' ); ?></td>
                                    <td>
                                        <?php if( isset($layer->fill) && is_string($layer->fill) ): ?>
                                        <span class="nbd-color-wrap"><span class="nbd-color" style="background: <?php echo esc_attr( $layer->fill ); ?>"></span><span class="nbd-color-value"><?php echo esc_html( $layer->fill ); ?></span></span>
                                        <?php elseif( isset($layer->fill) && is_object($layer->fill) && isset($layer->fill->source) ): ?>
                                        <span>
                                            <img class="nbd-order-text-pattern" src="<?php echo esc_url( $layer->fill->source ); ?>" />
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo esc_html( $fontSize ); ?></td>
                                    <td><a href="<?php echo esc_url( $fonturl ); ?>" <?php if( $is_google_font ) echo 'target="_blank"'; else echo 'download'; ?> title="<?php esc_html_e('Download', 'web-to-print-online-designer'); ?>"><?php echo esc_html( $fontname ); ?> <span class="dashicons dashicons-external nbd-order-font-download"></span></a></td>
                                </tr>
                                <?php } endforeach; ?>
                            <?php endforeach; ?> 
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div> 
        <div id="convert-download">
            <div id="nbd-convert-section">
                <h3><?php esc_html_e('Download design', 'web-to-print-online-designer'); ?></h3>
                <div class="nbd-convert-con">
                    <p class="nbd-convert-con-header"><?php esc_html_e('SVG', 'web-to-print-online-designer'); ?></p>
                    <div class="nbd-convert-con-inner">
                        <a href="<?php echo esc_url( add_query_arg(array('download-type' => 'svg', 'nbd_item_key' => $nbd_item_key, 'order_id' => $order_id, 'item_id' => $item_id, 'product_id' => $product_id, 'variation_id' => $variation_id), admin_url('admin.php?page=nbdesigner_detail_order')) ); ?>" class="button-primary" id="download-svg"><?php esc_html_e('Download', 'web-to-print-online-designer'); ?></a>
                    </div>
                </div>
                <div class="nbd-convert-con">
                    <div id="conver-png-to-jpg">
                        <p class="nbd-convert-con-header"><?php esc_html_e('PNG', 'web-to-print-online-designer'); ?> <?php if( function_exists('nbd_check_license') && nbd_check_license() ) esc_html_e('( High quality images )', 'web-to-print-online-designer'); ?></p>
                        <div class="nbd-convert-con-inner">
                            <?php if( ! function_exists('nbd_check_license') || ! nbd_check_license() ): ?>
                            <a href="<?php echo esc_url( add_query_arg(array('download-type' => 'png', 'nbd_item_key' => $nbd_item_key, 'order_id' => $order_id, 'item_id' => $item_id, 'product_id' => $product_id, 'variation_id' => $variation_id), admin_url('admin.php?page=nbdesigner_detail_order')) ); ?>" class="button-primary" id="download-png"><?php esc_html_e('Download PNG', 'web-to-print-online-designer'); ?></a>
                            <?php else: ?>
                            <a class="button-primary" href="<?php echo esc_url( add_query_arg(array('download-type' => 'png-hires', 'nbd_item_key' => $nbd_item_key, 'order_id' => $order_id, 'product_id' => $product_id, 'item_id' => $item_id, 'variation_id' => $variation_id), admin_url('admin.php?page=nbdesigner_detail_order')) ); ?>" id="download-png"><?php esc_html_e('Download', 'web-to-print-online-designer'); ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="nbd-convert-con">
                    <p class="nbd-convert-con-header"><?php esc_html_e('JPG ( CMYK with ICC profile )', 'web-to-print-online-designer'); ?></p>
                    <div class="nbd-convert-con-inner">
                        <?php if( ! function_exists('nbd_check_license') || ! nbd_check_license() ): ?>
                        <span class="pro-medal">PRO</span>
                        <?php endif; ?>
                        <a class="button-primary" 
                           <?php if( function_exists('nbd_check_license') && nbd_check_license() ): ?>
                           href="<?php echo esc_url( add_query_arg(array('download-type' => 'jpg-hires', 'nbd_item_key' => $nbd_item_key, 'order_id' => $order_id, 'item_id' => $item_id, 'product_id' => $product_id, 'variation_id' => $variation_id), admin_url('admin.php?page=nbdesigner_detail_order')) ); ?>"
                           <?php else: ?>
                           href="#"
                           <?php endif; ?>
                           id="download-jpg"><?php esc_html_e('Download', 'web-to-print-online-designer'); ?></a>
                        <p><?php esc_html_e('Change ICC profile', 'web-to-print-online-designer'); ?> <a target="_blank" href="<?php echo esc_url(admin_url('admin.php?page=nbdesigner&tab=output#nbdesigner_default_icc_profile')); ?>"><?php esc_html_e('here', 'web-to-print-online-designer'); ?></a></p>
                    </div>
                </div>
                <div class="nbd-convert-con">
                    <div id="download-pdf">
                        <p class="nbd-convert-con-header"><?php esc_html_e('PDF', 'web-to-print-online-designer'); ?></p>
                        <div class="nbd-convert-con-inner">
                            <?php if( ! function_exists('nbd_check_license') || ! nbd_check_license() ): ?>
                            <span class="pro-medal">PRO</span>
                            <?php endif; ?>
                            <a 
                                <?php if( function_exists('nbd_check_license') && nbd_check_license() ): ?>
                                href="<?php echo esc_url( add_query_arg(array('download-type' => 'pdf', 'nbd_item_key' => $nbd_item_key, 'order_id' => $order_id, 'item_id' => $item_id, 'product_id' => $product_id, 'variation_id' => $variation_id), admin_url('admin.php?page=nbdesigner_detail_order')) ); ?>" 
                                <?php else: ?>
                                href="#"
                                <?php endif; ?>
                                class="button-primary" id="download-pdf-btn"><?php esc_html_e('Download', 'web-to-print-online-designer'); ?></a>
                            <p><a href="javascript:void(0)" onclick="activeTabPdf()"><?php esc_html_e('Export PDF with config', 'web-to-print-online-designer'); ?></a></p>
                        </div>
                    </div>
                </div>
                <?php if( function_exists('is_available_imagick') && ! is_available_imagick() ): ?>
                <p><span class="nbd-order-warning-imagick">*</span><?php esc_html_e('Required PHP imagick API', 'web-to-print-online-designer'); ?></p>
                <?php endif; ?>
            </div> 
        </div>
        <div id="save-to-pdf">
            <div class="save-to-pdf-inner">
                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row" class="titledesc">
                                <label><?php esc_html_e('Force all side in the same format', 'web-to-print-online-designer'); ?></label>
                            </th>
                            <td class="forminp forminp-text">
                                <input name="force_same_format" type="hidden" value="0">
                                <input name="force_same_format" type="checkbox" value="1">
                                <input name="order_id" type="hidden" value="<?php echo esc_attr( $order_id ); ?>">
                                <input name="nbd_item_key" type="hidden" value="<?php echo esc_attr( $nbd_item_key ); ?>">
                                <input type="hidden" value="<?php echo esc_attr( $dpi ); ?>" name="dpi">
                                <br /><small><?php esc_html_e('Create a PDF file contain all page in the same page size. By default, each page correspond a PDF file.', 'web-to-print-online-designer'); ?></small>
                            </td>
                        </tr>
                        <tr valign="top" style="display: none;">
                            <th scope="row" class="titledesc">
                                <label><?php esc_html_e('Create PDF from', 'web-to-print-online-designer'); ?></label>
                            </th>
                            <td class="forminp forminp-text">
                                <select name="from_type">
                                    <option value="0">PNG</option>
                                    <option value="3" selected="selected">SVG</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <hr />
            </div>
            <div>
                <span class="pro-medal">PRO</span>
                <ul class="nbd-nav-tab">
                <?php if ( ! empty($datas) && is_iterable($datas) ): ?>
                    <?php foreach( $datas as $key => $data ): ?>
                        <li><a href="#side-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $data['orientation_name'] ?? '' ); ?></a></li>
                    <?php endforeach; ?>
                <?php endif; ?>
                </ul>
            </div>
        <?php
            if ( ! empty($datas) && is_iterable($datas) ):
                foreach($datas as $key => $data):
                    $contentImage = $list_design[ $key ] ?? '';
                    $proWidth           = isset( $data['product_width'] ) ? (float) $data['product_width'] : 0;
                    $proHeight          = isset( $data['product_height'] ) ? (float) $data['product_height'] : 0;
                    $bleed_top_bottom   = $unitRatio * ( isset( $data['bleed_top_bottom'] ) ? (float) $data['bleed_top_bottom'] : 0 );
                    $bleed_left_right   = $unitRatio * ( isset( $data['bleed_left_right'] ) ? (float) $data['bleed_left_right'] : 0 );

                    if( ! isset( $data['include_background'] ) ){
                        $data['include_background'] = '1';
                    }
                    if( ! isset( $data['origin_bg_pdf'] ) ){
                        $data['origin_bg_pdf'] = '';
                    }
        ?>
        <div class="inner side" id="side-<?php echo esc_attr( $key ); ?>">
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label><?php esc_html_e('Name', 'web-to-print-online-designer'); ?></label>
                        </th>
                        <td class="forminp forminp-text">
                            <input name="pdf[<?php echo esc_attr( $key ); ?>][name]" class="regular-text" type="text" value="<?php echo esc_attr( $data['orientation_name'] ?? '' ); ?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="format-<?php echo esc_attr($key); ?>"><?php esc_html_e('Paper Size', 'web-to-print-online-designer'); ?></label>
                        </th>
                        <td class="forminp forminp-text">
                        <select name="pdf[<?php echo esc_attr( $key ); ?>][format]" id="format-<?php echo esc_attr($key); ?>" class="regular-text">
                            <option value="-1"><?php printf(esc_html__('As product size %s x %s (exclude margin)', 'web-to-print-online-designer'), round($proWidth * $unitRatio, 2),  round($proHeight * $unitRatio, 2)); ?></option>
                    <?php
                    foreach ( $output_format as $group => $group_values ) {
                        ?><optgroup label="<?php echo esc_attr( $group ); ?>"><?php
                            foreach ( $group_values as $k => $val ) {
                            ?>
                                <option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $val ); ?></option>
                            <?php
                            }
                        ?></optgroup><?php
                    }
                    ?>
                        </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label><?php esc_html_e('Product size', 'web-to-print-online-designer'); ?></label>
                        </th>
                        <td class="forminp forminp-text">
                            <?php esc_html_e('Width', 'web-to-print-online-designer'); ?>: <input name="pdf[<?php echo esc_attr( $key ); ?>][product-width]" type="text" readonly class="nbd-order-input-60" value="<?php echo esc_attr( $proWidth * $unitRatio ); ?>"> 
                            <?php esc_html_e('Height', 'web-to-print-online-designer'); ?>: <input name="pdf[<?php echo esc_attr( $key ); ?>][product-height]" type="text" readonly class="nbd-order-input-60" value="<?php echo esc_attr( $proHeight * $unitRatio ); ?>"> 
                            <small><?php esc_html_e('In mm', 'web-to-print-online-designer'); ?></small> <br /> ( <?php echo esc_html( $proWidth . ' × ' . $proHeight . ' ' . $unit ); ?> )
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="orientation-<?php echo esc_attr($key); ?>"><?php esc_html_e('Orientation', 'web-to-print-online-designer'); ?></label>
                        </th>
                        <td class="forminp forminp-text">
                            <select name="pdf[<?php echo esc_attr( $key ); ?>][orientation]" id="orientation-<?php echo esc_attr($key); ?>">
                                <option value="P" selected="selected"><?php esc_html_e('Portrait', 'web-to-print-online-designer');?></option>
                                <option value="L"><?php esc_html_e('Landscape', 'web-to-print-online-designer');?></option>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label><?php esc_html_e('Margin', 'web-to-print-online-designer'); ?></label>
                        </th>
                        <td class="forminp forminp-text">
                            <input name="pdf[<?php echo esc_attr( $key ); ?>][margin-top]" class="margin-top-val nbd-order-input-60" type="number" min="0" value="0">
                            <input name="pdf[<?php echo esc_attr( $key ); ?>][margin-right]" class="margin-right-val nbd-order-input-60" type="number" min="0" value="0">
                            <input name="pdf[<?php echo esc_attr( $key ); ?>][margin-bottom]" class="margin-bottom-val nbd-order-input-60" type="number" min="0" value="0">
                            <input name="pdf[<?php echo esc_attr( $key ); ?>][margin-left]" class="margin-left-val nbd-order-input-60" type="number" min="0" value="0">
                            <p><small><?php esc_html_e('Margin top, right, bottom, left in mm', 'web-to-print-online-designer'); ?></small></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label><?php esc_html_e('Show Bleed Line', 'web-to-print-online-designer'); ?></label>
                        </th>
                        <td class="forminp forminp-text">
                            <p>
                                <input name="pdf[<?php echo esc_attr( $key ); ?>][show-bleed-line]" type="radio" value="yes">
                                    <?php esc_html_e('Yes', 'web-to-print-online-designer'); ?>
                            </p>
                            <p>
                                <input name="pdf[<?php echo esc_attr( $key ); ?>][show-bleed-line]" type="radio" value="no" checked="checked">
                                    <?php esc_html_e('No', 'web-to-print-online-designer'); ?>
                            </p>
                        </td>
                    </tr>   
                    <tr valign="top" style="display: none;" class="setting-bleed">
                        <th scope="row" class="titledesc">
                            <label><?php esc_html_e('Margin bleed', 'web-to-print-online-designer'); ?></label>
                        </th>
                        <td class="forminp forminp-text">
                            <input name="pdf[<?php echo esc_attr( $key ); ?>][bleed-top]" type="number" min="0" class="nbd-order-input-60" value="<?php echo esc_attr( $bleed_top_bottom ); ?>" onchange="changeBleed(this, 'top');">
                            <input name="pdf[<?php echo esc_attr( $key ); ?>][bleed-right]" type="number" min="0" class="nbd-order-input-60" value="<?php echo esc_attr( $bleed_left_right ); ?>" onchange="changeBleed(this, 'right');">
                            <input name="pdf[<?php echo esc_attr( $key ); ?>][bleed-bottom]" type="number" min="0" class="nbd-order-input-60" value="<?php echo esc_attr( $bleed_top_bottom ); ?>" onchange="changeBleed(this, 'bottom');">
                            <input name="pdf[<?php echo esc_attr( $key ); ?>][bleed-left]" type="number" min="0" class="nbd-order-input-60" value="<?php echo esc_attr( $bleed_left_right ); ?>" onchange="changeBleed(this, 'left');">
                            <p><small><?php esc_html_e('Margin bleed top, right, bottom, left in mm', 'web-to-print-online-designer'); ?></small></p>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label><?php esc_html_e('Background', 'web-to-print-online-designer'); ?></label>
                        </th>
                        <td class="forminp forminp-text">
                            <?php $bg_type = $data['bg_type'] ?? ''; ?>
                            <?php if($bg_type === 'image'): ?>
                            <a class="button" onclick="changeBackground(this)"><?php esc_html_e('Change image', 'web-to-print-online-designer'); ?></a>
                            <a class="button" href="<?php echo esc_url( $data['img_src'] ?? '' ); ?>" target="_blank"><?php esc_html_e('View', 'web-to-print-online-designer'); ?></a><br />
                            <small><?php esc_html_e('Change image with heigh resolution if you want. Support: jpeg, jpg, png, svg', 'web-to-print-online-designer'); ?></small>
                            <?php elseif($bg_type === 'color'): ?>
                            <span style="width: 30px; height: 30px; vertical-align: top; display: inline-block; background: <?php echo esc_attr( $data['bg_color_value'] ?? '' ); ?>"></span>
                            <?php else: ?>
                            <span class="background-transparent"></span>
                            <?php esc_html_e('Transparent', 'web-to-print-online-designer'); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label><?php esc_html_e('Include background image', 'web-to-print-online-designer'); ?></label>
                        </th>
                        <td class="forminp forminp-text">
                            <input name="pdf[<?php echo esc_attr( $key ); ?>][include_background]" type="hidden" value="0" />
                            <input name="pdf[<?php echo esc_attr( $key ); ?>][include_background]" type="checkbox" value="1" <?php checked( $data['include_background'] ?? '0' ); ?> />
                            &nbsp;<small><?php esc_html_e('Include background image if background type is image.', 'web-to-print-online-designer'); ?></small>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div>
                <input type="hidden" value="<?php echo esc_attr( $data['img_src'] ?? '' ); ?>" name="pdf[<?php echo esc_attr( $key ); ?>][background]" class="bg_src_hidden">
                <input type="hidden" value="<?php echo esc_attr( $data['bg_type'] ?? '' ); ?>" name="pdf[<?php echo esc_attr( $key ); ?>][bg_type]">
                <input type="hidden" value="<?php echo esc_attr( $data['origin_bg_pdf'] ?? '' ); ?>" name="pdf[<?php echo esc_attr( $key ); ?>][origin_bg_pdf]">
                <input type="hidden" value="<?php echo esc_attr( $data['bg_color_value'] ?? '' ); ?>" name="pdf[<?php echo esc_attr( $key ); ?>][bg_color_value]">
                <input type="hidden" value="<?php echo esc_attr( ($data['real_top'] ?? 0) * $unitRatio ); ?>" name="pdf[<?php echo esc_attr( $key ); ?>][cd-top]">
                <input type="hidden" value="<?php echo esc_attr( ($data['real_left'] ?? 0) * $unitRatio ); ?>" name="pdf[<?php echo esc_attr( $key ); ?>][cd-left]"> 
                <input type="hidden" value="<?php echo esc_attr( ($data['real_width'] ?? 0) * $unitRatio ); ?>" name="pdf[<?php echo esc_attr( $key ); ?>][cd-width]">
                <input type="hidden" value="<?php echo esc_attr( ($data['real_height'] ?? 0) * $unitRatio ); ?>" name="pdf[<?php echo esc_attr( $key ); ?>][cd-height]">
                <input type="hidden" value="<?php echo esc_attr( $contentImage ); ?>" name="pdf[<?php echo esc_attr( $key ); ?>][customer-design]">
            </div>
        </div>
        <?php 
                endforeach; 
            endif;
        ?>
        <p class="result-pdf">
            <?php wp_nonce_field( 'nbdesigner_pdf_nonce'); ?>
            <a href="javascript:void(0)" class="button-primary" id="create_pdf"><?php esc_html_e('Create PDF', 'web-to-print-online-designer'); ?></a>
            <?php if ( ! empty($list_pdfs) && is_iterable($list_pdfs) ): ?>
                <?php foreach ($list_pdfs as $_pdf): ?>
                    <a class="pdf-file" href="<?php echo esc_url( ($_pdf['url'] ?? '') . '?t=' . time() ); ?>" download>
                        <img src="<?php echo esc_url( NBDESIGNER_PLUGIN_URL . 'assets/images/file/pdf.png' ); ?>"/><br /><?php echo esc_html( $_pdf['title'] ?? '' ); ?>
                    </a>
                <?php endforeach; ?> 
            <?php endif; ?>
        </p>
        </div>
        <script>
            var _round = function(num){
                return Number((num).toFixed(0)); 
            };
            jQuery(document).ready(function() {
                jQuery( "#nbdesign-order-tabs" ).tabs({
                    active: 2
                });  
                jQuery( "#save-to-pdf" ).tabs({});
                var ajaxurl = "<?php echo esc_url( admin_url('admin-ajax.php') ); ?>";
                jQuery('#create_pdf').on('click', function(e){
                    e.preventDefault();
                    <?php $valid_license = function_exists('nbd_check_license') && nbd_check_license(); if(!$valid_license): ?>
                    swal({
                      title: "Oops!",
                      text: "This feature only available in PRO version.",
                      imageUrl: "<?php echo esc_url( NBDESIGNER_PLUGIN_URL . 'assets/images/dinosaur.png' ); ?>"
                    });   
                    <?php else: ?>
                    var formdata = jQuery('#save-to-pdf').find('input, select').serialize();
                    formdata = formdata + '&action=nbdesigner_save_design_to_pdf';
                    swal({
                        title: "<?php esc_html_e('Create PDFs', 'web-to-print-online-designer'); ?>",
                        text: "Submit to create pdfs",
                        type: "info",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    },
                    function(){
                        jQuery.post(ajaxurl, formdata, function(data) {
                            if(typeof data == 'object'){
                                var pdf_img = "<?php echo esc_url( NBDESIGNER_PLUGIN_URL . 'assets/images/file/pdf.png' ); ?>";
                                swal("Complete!", "Click to download!", "success");
                                jQuery('.result-pdf .pdf-file').remove();
                                jQuery.each(data, function(index, element){
                                    var item = '<a class="pdf-file" href="'+element["link"]+'" download><img src="'+pdf_img+'"/><br />'+element["title"]+'</a>';
                                    jQuery('.result-pdf').append(item);
                                });
                            }
                        }, 'json');
                    });  
                    <?php endif; ?>
                });
                jQuery('#create_layout_pdf').on('click', function(e){
                    var formdata = jQuery('#pdf-layout').find('input, select').serialize();
                    formdata = formdata + '&action=nbdesigner_save_design_to_pdf_with_layout';
                    swal({
                        title: "<?php esc_html_e('Create PDF Layout', 'web-to-print-online-designer'); ?>",
                        text: "Submit to create PDF Layout",
                        type: "info",
                        showCancelButton: true,
                        closeOnConfirm: false,
                        showLoaderOnConfirm: true
                    },
                    function(){
                        jQuery.post(ajaxurl, formdata, function(data) {
                            if(typeof data == 'object'){
                                var pdf_img = "<?php echo esc_url( NBDESIGNER_PLUGIN_URL . 'assets/images/file/pdf.png' ); ?>";
                                swal("Complete!", "Click to download!", "success");
                                var a = document.createElement('a');
                                a.setAttribute('download', 'design.pdf');
                                a.setAttribute('href', data.link);
                                a.style.display = 'none';
                                document.body.appendChild(a);
                                a.click();
                                document.body.removeChild(a);
                            }
                        }, 'json');
                    });
                });
            });
            var changeBackground = function(e){
                var upload;
                if (upload) {
                    upload.open();
                    return;
                }
                var parent = jQuery(e).parents('.inner.side');
                var _img = parent.find('.bg_src'),
                    _input = parent.find('.bg_src_hidden'),
                    bg = parent.find('.pdf-layer.bg img');
                upload = wp.media.frames.file_frame = wp.media({
                    title: 'Choose Image',
                    button: {
                        text: 'Choose Image'
                    },
                    multiple: false
                });
                upload.on('select', function () {
                    var attachment = upload.state().get('selection').first().toJSON();
                    _img.attr('src', attachment.url);
                    bg.attr('src', attachment.url);
                    _img.show();
                    bg.show();
                    _input.val(attachment.url);
                });
                upload.open();
            };
            var convertPng2Jpg = function(e){
                jQuery(e).addClass('nbd-prevent');
                jQuery(e).find('span').addClass('rotating');
                var formdata = jQuery('#conver-png-to-jpg').find('input, select').serialize();
                formdata += '&action=nbd_convert_files&type=jpg';
                jQuery.post(ajaxurl, formdata, function(data) {
                    var data = JSON.parse(data);
                    if(data['flag'] == 1){
                        jQuery(e).removeClass('nbd-prevent');
                        jQuery(e).find('span').removeClass('rotating');
                        jQuery('.download-jpg, .convert-cmyk-btn').removeClass('nbd-unavailable');
                        jQuery("select[name='from_type'] option[value='1']").removeAttr("disabled");
                    }
                });
            };
            var convertRgb2Cmyk = function(e){
                jQuery(e).addClass('nbd-prevent');
                jQuery(e).find('span').addClass('rotating');
                var formdata = jQuery('#convert-rgb-to-cmyk').find('input, select').serialize();
                formdata += '&action=nbd_convert_files&type=cmyk';
                jQuery.post(ajaxurl, formdata, function(data) {
                    var data = JSON.parse(data);
                    if(data['flag'] == 1){
                        jQuery(e).removeClass('nbd-prevent');
                        jQuery(e).find('span').removeClass('rotating');
                        jQuery('.download-cmyk').removeClass('nbd-unavailable');
                        jQuery("select[name='from_type'] option[value='2']").removeAttr("disabled");
                    }
                });
            };
            var gotoConvert = function(){
                jQuery('a[href="#convert-download"]').click();
                jQuery('html, body').animate({
                    scrollTop: jQuery("#nbd-convert-section").offset().top
                }, 1000);
            };
            var activeTabPdf = function(){
                jQuery('a[href="#save-to-pdf"]').click();
                jQuery('html, body').animate({
                    scrollTop: jQuery("#save-to-pdf").offset().top
                }, 1000);
            };
        </script>
</div>