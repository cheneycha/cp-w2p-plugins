<?php if (!defined('ABSPATH')) exit;
    $fbID       = nbdesigner_get_option('nbdesigner_facebook_app_id', '');
    $insID      = nbdesigner_get_option('nbdesigner_instagram_app_id', '');
    $insUrl     = NBDESIGNER_PLUGIN_URL . 'includes/auth-instagram.php';
    $gaKey      = nbdesigner_get_option('nbdesigner_google_api_key', '');
    $ggID       = nbdesigner_get_option('nbdesigner_google_client_id', '');
    $dbID       = nbdesigner_get_option('nbdesigner_dropbox_app_id', '');
    $enbleFb    = nbdesigner_get_option('nbdesigner_enable_facebook_file_upload', 'yes');
    $enbleIns   = nbdesigner_get_option('nbdesigner_enable_instagram_file_upload', 'yes');
    $enbleGG    = nbdesigner_get_option('nbdesigner_enable_drive_file_upload', 'yes');
    $enbleDB    = nbdesigner_get_option('nbdesigner_enable_dropbox_file_upload', 'yes');
    $lang_code  = str_replace('-', '_', get_locale());
    $locale     = substr($lang_code, 0, 2);
    $unit       = isset( $design_option['unit'] ) ? $design_option['unit'] : nbdesigner_get_option( 'nbdesigner_dimensions_unit', 'cm' );
    if( $enbleFb == 'yes' && $fbID != '' ){
        $currentSv  = 'facebook';
    } else {
        if( $enbleIns == 'yes' && $insID != '' ){
            $currentSv  = 'instagram';
        } else {
            if( $enbleGG == 'yes' && $gaKey != '' && $ggID != '' ){
                $currentSv  = 'drive';
            } else {
                if( $enbleDB == 'yes' && $dbID != '' ){
                    $currentSv  = 'dropbox';
                }
            }
        }
    }
?>
<style type="text/css">
    .nbu-advanced-upload-inner {
        background: #f2f2f2;
        width: 100%;
        height: 100%;
        justify-content: center;
        display: flex;
        text-align: center;
        align-items: center;
        position: relative;
    }
    .nbu-upload-ctrl {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .nbu-upload-wrap {
        justify-content: center;
        text-align: center;
        width: 100%;
        display: flex;
        position: relative;
        height: 100%;
        padding-bottom: 80px;
        overflow: auto;
    }
    .nbu-upload-item-wrap {
        position: relative;
    }
    .nbu-upload-item-wrap {
        display: flex;
        width: 85%;
        flex-wrap: wrap;
    }
    .nbu-upload-item-inner-wrap {
        height: 250px;
        padding: 10px;
        background: #fff;
        margin-right: 25px;
        margin-bottom: 25px;
        box-shadow: 0 4px 16px rgba(0,0,0,.12);
        box-sizing: content-box;
        background-repeat: no-repeat;
        background-size: 100% 100%;
        background-position: center;
    }
    .nbu-upload-item-inner {
        width: 250px;
        height: 250px;
        cursor: pointer;
        z-index: 2;
        position: relative;
    }
    .nbu-upload-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 0;
    }
    .nbu-upload-shadow {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 2;
        pointer-events: none;
        box-shadow: inset 0px 0px 10px rgb(0, 0, 0, 0.3);
    }
    .nbu-upload-image.nbu-loading{
        box-shadow: none;
        max-width: 100%;
        max-height: 100%;
        width: unset;
        height: unset;
        margin: 0 auto;
        top: calc(50% - 25px);
        left: calc(50% - 25px);
    }
    .nbu-upload-zone {
        border: 4px dashed #b0b0b0;
        width: 270px;
        height: 270px;
        box-sizing: border-box;
        margin-bottom: 25px;
        border-radius: 14px;
        cursor: pointer;
        background: #fff; 
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0,0,0,.12);
    }
    .nbu-upload-zone.nbu-before{
        -webkit-animation: invisible .6s,fade-in .8s .6s,left-slide-in 1.2s;
        animation: invisible .6s,fade-in .8s .6s,left-slide-in 1.2s;
    }
    @keyframes fade-in {
        0% { opacity: 0; }
        100% { opacity: 1; } 
    }
    @-webkit-keyframes left-slide-in {
        0% {
            -webkit-transform: translateX(120%);
            transform: translateX(120%)
        }
        66% {
            -webkit-transform: translateX(120%);
            transform: translateX(120%)
        }
        to {
            -webkit-transform: translateX(0);
            transform: translateX(0)
        }
    }
    @keyframes left-slide-in {
        0% {
            -webkit-transform: translateX(120%);
            transform: translateX(120%)
        }
        66% {
            -webkit-transform: translateX(120%);
            transform: translateX(120%)
        }
        to {
            -webkit-transform: translateX(0);
            transform: translateX(0)
        }
    }
    .nbu-upload-zone-inner {
        width: 100%;
        height: 100%;
        position: relative;
    }
    .nbu-upload-zone-top {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 50%;
        display: flex;
        text-align: center;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        border-bottom: 1px solid #eee;
        opacity: 0;
    }
    .nbu-upload-zone-bottom {
        position: absolute;
        top: 50%;
        left: 0;
        width: 100%;
        height: 50%;
        display: flex;
        text-align: center;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        opacity: 0;
    }
    .nbu-upload-zone-inner:hover .nbu-upload-zone-top,
    .nbu-upload-zone-inner:hover .nbu-upload-zone-bottom{
        opacity: 1;
    }
    .nbu-upload-zone-top:hover, .nbu-upload-zone-bottom:hover {
        background: rgba(12, 142, 167, 0.15);
    }
    .nbu-plus-shape {
        display: inline-block;
        top: calc(50% - 20px);
        left: calc(50% - 20px);
        position: absolute;
        z-index: 1;
    }
    .nbu-upload-zone-inner:hover .nbu-plus-shape{
        opacity: 0;
        z-index: -1;
    }
    .nbu-text {
        font-weight: bold;
    }
    .nbd-upload-action-wrap {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: #fff;
        box-shadow: 0 0 8px rgba(0,0,0,.3);
        z-index: 2;
        padding: 15px;
    }
    .nbd-upload-action {
        position: relative;
        background-color: #404762;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 50px;
        border-radius: 4px;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
        -webkit-transition: box-shadow .3s,background-color .3s;
        transition: box-shadow .3s,background-color .3s;
        max-width: 400px;
        margin-right: auto;
        margin-left: auto;
    }
    .nbu-top-bar {
        box-shadow: 0 0 10px rgba(0,0,0,.3);
        background-color: #fff;
        display: flex;
        justify-content: center;
        flex-direction: column;
        z-index: 2;
    }
    .nbu-upload-exist {
        flex-wrap: wrap;
        align-content: flex-start;
        width: 80%;
        overflow: hidden;
        height: unset;
        margin: auto;
        padding-bottom: 30px;
        padding-top: 30px;
        display: flex;
        justify-content: center;
    }
    .nbu-mobile {
        display: none;
    }
    .nbu-drop-upload-zone,
    .nbu-processing,
    .nbu-popup {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        z-index: -1;
        opacity: 0;
        -webkit-transition: opacity 225ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        -moz-transition: opacity 225ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        transition: opacity 225ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
    }
    .nbu-processing {
        background: rgba(255,255,255,0.9);
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .nbu-popup.active {
        opacity: 1;
        z-index: 2;
    }
    .nbu-processing.active {
        opacity: 1;
        z-index: 6;
    }
    .nbu-processing img {
        width: 40px;
        height: 40px;
    }
    .nbu-drop-upload-zone{
        background: rgba(64, 71, 98, 0.9);
    }
    .nbu-drop-upload-zone.nbu-highlight {
        z-index: 5;
        opacity:1;
    }
    .nbu-drop-upload-zone .nbu-drop-upload-zone-inner {
        position: absolute;
        top: 10px;
        left: 10px;
        right: 10px;
        bottom: 10px;
        border: 1px dashed #fff;
        pointer-events: none;
    }
    .nbu-drop-upload-zone .nbu-drop-upload-zone-inner h2{
        margin: -0.5em 0 0;
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        transform: translateY( -50% );
        font-size: 40px;
        color: #fff;
        padding: 0;
    }
    .nbu-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        background-color: rgba(0, 0, 0, 0.5);
    }
    .nbu-popup-inner {
        height: 100%;
        width: 100%;
        position: relative;
    }
    #nbd-upload-actions {
        position: absolute;
        top: auto;
        left: 0;
        right: 0;
        bottom: 0;
        height: auto;
        max-height: 100%;
        margin-right: auto;
        margin-left: auto;
        width: 500px;
        display: flex;
        -webkit-transition: opacity 225ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        -moz-transition: opacity 225ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        transition: transform 225ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        -webkit-transform: translate(0px, 50px);
        -moz-transform: translate(0px, 50px);
        transform: translate(0px, 50px);
        opacity: 0;
        z-index: -1;
    }
    .nbu-popup.active #nbd-upload-actions.active {
        -webkit-transform: translate(0px, 0px);
        -webkit-transform: translate(0px, 0px);
        transform: translate(0px, 0px);
        opacity: 1;
        z-index: 1;
    }
    #nbd-upload-actions .nbd-upload-actions-inner{
        background-color: #fff;
        border-radius: 10px;
        margin: 0 10px 10px;
        overflow: hidden;
        box-shadow: 0 8px 10px -5px rgba(0,0,0,.15), 0 16px 24px 2px rgba(0,0,0,.1), 0 6px 30px 5px rgba(0,0,0,.09);
        width: 100%;
    }
    #nbd-upload-actions.active .nbd-upload-actions-inner{
        opacity: 1;
    }
    .nbu-action {
        width: 100%;
        text-align: center;
        font-size: 18px;
        font-weight: 700;
        padding: 13px 0;
        cursor: pointer;
        display: inline-block;
    }
    .nbu-action:not(:last-child) {
        border-bottom: 2px solid #e8e8e8;
    }
    .nbu-action.nbd-dismiss {
        color: #8c8c8c;
        background-color: #f2f2f2;
    }
    .nbu-action.nbd-remove {
        color: #db133b;
    }
    #nbd-upload-services, #nbd-upload-adjust, #nbu-upload-warning, #nbu-upload-nbo-options {
        position: absolute;
        left: 0;
        right: 0;
        top: 15px;
        height: auto;
        max-height: 100%;
        width: 750px;
        height: 600px;
        background: #fff;
        -webkit-transition: opacity 225ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        -moz-transition: opacity 225ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        transition: transform 225ms cubic-bezier(0.4, 0, 0.2, 1) 0ms;
        -webkit-transform: translate(0px, 50px);
        -moz-transform: translate(0px, 50px);
        transform: translate(0px, 50px);
        opacity: 0;
        z-index: -1;
        box-shadow: 0px 11px 15px -7px rgba(0,0,0,0.2), 0px 24px 38px 3px rgba(0,0,0,0.14), 0px 9px 46px 8px rgba(0,0,0,0.12);
        border-radius: 10px;
        margin-right: auto;
        margin-left: auto;
        background-color: #f5f5f5;
    }
    #nbu-upload-warning {
        top: auto;
        bottom: 0;
        background-color: transparent;
        box-shadow: none;
        height: auto;
    }
    .nbu-popup.active #nbd-upload-services.active,
    .nbu-popup.active #nbu-upload-warning.active,
    .nbu-popup.active #nbu-upload-nbo-options.active,
    .nbu-popup.active #nbd-upload-adjust.active {
        -webkit-transform: translate(0px, 0px);
        -webkit-transform: translate(0px, 0px);
        transform: translate(0px, 0px);
        opacity: 1;
        z-index: 1;
    }
    .nbu-active {
        color: #404762;
    }
    .nbu-adjust-header, .nbu-services-header, .nbu-options-header {
        box-shadow: 0 0 10px rgba(0,0,0,.3);
        background-color: #fff;
        height: 50px;
        display: flex;
        justify-content: space-between;
        align-content: center;
        text-align: center;
        align-items: center;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        flex-direction: row;
        padding: 0 15px;
    }
    .nbu-adjust-header span, .nbu-services-header span{
        display: inline-flex;
        font-weight: bold;
    }
    .nbu-services-header {
        background: transparent;
        box-shadow: none;
        border-bottom: 1px solid #ddd;
    }
    .nbd-services-inner {
        position: relative;
        width: 100%;
        height: 100%;
    }
    .nbu-services-nav {
        position: absolute;
        bottom: 0;
        left: 0;
        z-index: 3;
        overflow: hidden;
        background: #fff;
        -webkit-transition: all 0.15s cubic-bezier(0.84, 0.02, 0.37, 0.74);
        transition: all 0.15s cubic-bezier(0.84, 0.02, 0.37, 0.74);
        top: 0;
        width: 60px;
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
    }
    .nbu-services-nav:hover {
        width: 240px;
        -webkit-box-shadow: 0 0 16px 0 rgba(0, 0, 0, 0.18), 0 16px 16px 0 rgba(0, 0, 0, 0.24);
        box-shadow: 0 0 16px 0 rgba(0, 0, 0, 0.18), 0 16px 16px 0 rgba(0, 0, 0, 0.24);
    }
    .nbu-services-wrap {
        width: calc(100% - 60px);
        height: 100%;
        position: absolute;
        overflow: hidden;
        top: 0;
        right: 0;
        bottom: 0;
    }
    .nbu-services-wrap-inner {
        height: 100%;
    }
    .nbu-dialog-content {
        border-radius: 10px;
        margin: 10px;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 14px;
        background-color: #f5f5f5;
        overflow: hidden;
    }
    .nbu-dialog-title {
        text-align: center;
        font-weight: 700;
        font-size: 20px;
        padding: 0 24px 18px;
    }
    .nbu-dialog-image img {

        height: 220px;
        display: inline-flex;
        margin-bottom: 17px;
        box-shadow: 0 1px 7px rgba(0,0,0,.32);
    }
    .nbu-dialog-text {
        text-align: center;
        color: #888;
        padding: 0 24px 20px;
        font-weight: 700;
        white-space: pre-line;
    }
    .nbu-dialog-action {
        box-shadow: 0px 11px 15px -7px rgba(0,0,0,0.2), 0px 24px 38px 3px rgba(0,0,0,0.14), 0px 9px 46px 8px rgba(0,0,0,0.12);
        background-color: #fff;
        border-radius: 10px;
        margin: 0 10px 10px;
    }
    .nbu-dialog-button {
        width: 100%;
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        padding: 13px 0;
        cursor: pointer;
    }
    .nbu-dialog-button.nbu-remove {
        border-bottom: 2px solid #e8e8e8;
        color: #db133b;
    }
    .nbu-services-images {
        height: calc(100% - 50px);
        overflow-y: auto;
        overflow-x: hidden;
    }
    .nbu-service-img-grid {
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        box-sizing: border-box;
        padding: 10px;
        margin-bottom: 50px;
    }
    .nbu-service-img-wrap {
        width: calc(25% - 4px);
        min-width: calc(25% - 4px);
        position: relative;
        margin: 2px;
        height: 160px;
        padding: 0px;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-orient: vertical;
        -webkit-box-direction: normal;
        -ms-flex-direction: column;
        flex-direction: column;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-box-pack: center;
        -ms-flex-pack: center;
        justify-content: center;
        padding: 5px;
        cursor: pointer;
        background: #FCFCFC;
        box-shadow: 0 1px 1px 0 #DDDDDD;
        border-radius: 2px;
        overflow: hidden;
        box-sizing: border-box;
    }
    .nbu-service-img {
        -o-object-fit: contain;
        object-fit: contain;
        border-radius: 2px;
        width: auto;
        height: auto;
        max-width: 100%;
        max-height: 100%;
    }
    .nbu-service-img:hover {
        opacity: 0.9;
    }
    .nbu-services-list-item {
        position: relative;
        cursor: pointer;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        -ms-flex-negative: 0;
        flex-shrink: 0;
        background-color: #EEE;
        -webkit-transition: all 0.2s ease-in;
        transition: all 0.2s ease-in;
        height: 50px;
        font-size: 13px;
        width: 240px;
    }
    .nbu-services-list-item-icon {
        width: 38px;
        height: 38px;
        margin: 0 16px 0 11px;
    }
    .nbu-services-list-item-icon svg{
        width: 38px;
        height: 38px;
    }
    .nbu-services-list-item.active {
        background: #c2ebf3;
    }
    .nbu-services-list-item:hover {
        color: #404762;
    }
    .nbu-services-list-ite-logout {
        position: absolute;
        top: 50%;
        right: 10px;
        -webkit-transform: translateY(-50%);
        transform: translateY(-50%);
        color: #404762;
        font-size: 13px;
    }
    .nbu-services-footer {
        position: absolute;
        display: -webkit-box;
        display: -ms-flexbox;
        display: flex;
        -webkit-box-align: center;
        -ms-flex-align: center;
        align-items: center;
        right: 0;
        bottom: 0;
        left: 0;
        padding: 10px;
        background: #fff;
        box-shadow: 0 -2px 3px rgba(0,0,0,.1);
        -webkit-transform: translateY(100%);
        transform: translateY(100%);
        height: 54px;
        z-index: 10005;
        -webkit-transition: all 0.2s cubic-bezier(0.84, 0.02, 0.37, 0.74);
        transition: all 0.2s cubic-bezier(0.84, 0.02, 0.37, 0.74);
    }
    .nbu-services-footer.active {
        -webkit-transform: translateY(0);
        transform: translateY(0);
    }
    .nbu-services-footer-inner {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .nbu-services-select {
        font-weight: 400;
        height: 38px;
        line-height: 37px;
        padding: 0 30px;
        border-radius: 4px;
        border-width: 1px;
        border-style: solid;
        border-color: transparent;
        outline: none;
        cursor: pointer;
        -webkit-user-select: none;
        -ms-user-select: none;
        user-select: none;
        -moz-user-select: none;
        -webkit-transition: all 0.2s cubic-bezier(0.84, 0.02, 0.37, 0.74);
        transition: all 0.2s cubic-bezier(0.84, 0.02, 0.37, 0.74);
        background: #404762;
        color: #fff;
        display: inline-flex;
    }
    .nbu-mark-icon-selected {
        display: none;
        width: 26px;
        height: 26px;
        position: absolute;
        top: 14px;
        right: 14px;
        z-index: 2;
    }
    .nbu-mark-selected{
        background: rgba(12,142,167,.17);
        border: 5px solid #404762;
        position: absolute;
        display: none;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        box-sizing: border-box;
    }
    .nbu-service-img-wrap.active .nbu-mark-icon-selected,
    .nbu-service-img-wrap.active .nbu-mark-selected {
        display: block;
    }
    .nbu-services-title {
        color: #444;
        font-size: 20px;  
    }
    .nbu-services-des {
        color: #929292;
        line-height: 20px;
        font-size: 13px;
        margin: 0;
    }
    .nbu-facebook-login-wrap {
        text-align: center;
        padding-top: 50px;
        width: 100%;
        position: absolute;
        top: 50%;
        left: 50%;
        -webkit-transform: translate(-50%, -50%);
        transform: translate(-50%, -50%);
    }
    .nbu-connect-btn {
        color: #FCFCFC;
        background: #404762;
        margin: auto;
        line-height: 48px;
        height: 48px;
        padding-left: 30px;
        padding-right: 30px;
        -webkit-transition: none;
        transition: none;
        display: inline-block;
        cursor: pointer;
        border-radius: 4px;
    }
    .nbu-close {
        cursor: pointer;
    }
    .picker-dialog {
        z-index: 9999999999;
    }
    @keyframes shrink-grow {
        0% {
            transform: scale(0.95);
        }
        50% {
            transform: scale(1);
        }
        100% {
            transform: scale(0.95);
        }
    }
    @keyframes cyan-highlight {
        50% {
            border-color: #404762;
            fill: #404762;
        }
    }
    @-webkit-keyframes shrink-grow {
        0% {
            transform: scale(0.95);
        }
        50% {
            transform: scale(1);
        }
        100% {
            transform: scale(0.95);
        }
    }
    @-webkit-keyframes cyan-highlight {
        50% {
            border-color: #404762;
            fill: #404762;
        }
    }
    .nbu-no-image .nbu-upload-zone.nbu-after{
        -webkit-animation: fade-in .8s,shrink-grow 2s ease-out infinite,cyan-highlight 2s ease-out infinite;
        animation: fade-in .8s,shrink-grow 2s ease-out infinite,cyan-highlight 2s ease-out infinite;
        background-color: hsla(0,0%,100%,0.85);
        box-shadow: 0 4px 16px rgba(0,0,0,.12);
    }
    .nbu-no-image .nbu-upload-zone.nbu-after .nbu-plus-shape path{
        -webkit-animation: cyan-highlight 2s ease-out infinite;
        animation: cyan-highlight 2s ease-out infinite;
    }
    .nbu-no-image {
        background: url('<?php echo NBDESIGNER_ASSETS_URL.'images/background/30.png'; ?>');
        flex-direction: column;
        align-content: center;
    }
    .nbu-no-image-title {
        text-transform: uppercase;
        font-weight: 700;
        font-size: 14px;
        letter-spacing: 1.6px;
        color: #8c8c8c;
        position: absolute;
        width: 100%;
        text-align: center;
        top: 30px;
        left: 0;
    }
    .nbu-upload-header-text {
        text-align: center;
        flex-grow: 1;
        font-size: 19px;
        font-weight: bold;
        height: 50px;
        line-height: 50px;
        margin: 0;
    }
    .nbu-upload-options {
        display: flex;
        justify-content: center;
    }
    .nbu-upload-options-inner {
        display: flex;
        overflow-y: auto;
        height: 100px;
        margin-top: -10px;
    }
    .nbu-upload-option {
        cursor: pointer;
        width: 85px;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        flex: none;
        animation: 0.4s ease 0s 1 normal none running fade-scale-in;
        display: flex;
        justify-content: center;
        font-weight: bold;
    }
    .nbu-upload-option.active {
        background-image: linear-gradient(0deg, rgb(167, 225, 236), rgba(197, 227, 230, 0));
        color: #404762;
    }
    .nbu-upload-option-thumb {
        width: 65px;
        height: 65px;
        background: #eee;
        box-shadow: 1px 1px 0px #ddd, 2px 2px 0px #ddd, 3px 3px 0px #ddd;
    }
    .nbu-upload-option-title {
        font-size: 12px;
        font-weight: normal;
        white-space: nowrap;
        margin-top: 5px;
    }
    .nbu-adjust-body {
        height: calc(100% - 50px);
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
        border-bottom-left-radius: 10px;
        border-bottom-right-radius: 10px;
        position: relative;
    }
    .nbu-adjust-con {
        box-sizing: content-box;
        position: relative;
    }
    .nbu-upload-origin-wrap {
        width: 400px;
        height: 400px;
        position: absolute;
        top: -50px;
        left: -50px;
    }
    .nbu-adjust-con img.nbu-upload-origin-img {
        border-radius: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }
    .nbu-adjust-frame-wrap:before {
        content: '';
        position: absolute;
        display: block;
        top: -4px;
        left: -4px;
        width: calc(100% + 8px);
        height: calc(100% + 8px);
        pointer-events: none;
        box-sizing: border-box;
        box-shadow: 0 4px 16px rgba(0,0,0,.12);
    }
    .nbu-adjust-frame-wrap {
        position: absolute;
        top: -4px;
        left: -4px;

        cursor: move;
        border: 4px solid #fff;
        box-shadow: 0px 0px 0px 9999em rgba(255, 255, 255, 0.75);
        box-sizing: border-box;
    }
    .nbu-adjust-title {
        position: absolute;
        top: 30px;
        text-transform: uppercase;
        font-size: 14px;
        color: rgb(140, 140, 140);
        font-weight: bold;
        letter-spacing: 1.6px;
        z-index: 3;
        pointer-events: none;
    }
    .nbu-not-allowed {
        opacity: 0.5;
        cursor: not-allowed;
    }
    .reset_variations {
        background-color: #2c2d33;
        color: #ffffff;
        height: 40px;
        border: none;
        display: inline-block;
        vertical-align: top;
        line-height: 40px;
        padding: 0 15px;
        text-decoration: none;
    }
    .variations select {
        height: 40px;
    }
    .nbau-trigger-options {
        display: inline-block;
        vertical-align: middle;
        cursor: pointer;
        background: #ace2ed;
        border-radius: 4px;
        margin-left: 10px;
    }
    .nbu-popup-inner .nbd-tb-options {
        width: 100%;
    }
    @media (max-width: 768px){
        .nbu-upload-wrap {
            height: calc(100% - 140px);
        }
        .nbu-service-img-wrap {
            width: calc(50% - 4px);
            height: 120px;
        }
        #nbd-upload-actions { 
            width: calc(100% - 30px);
        }
        #nbd-upload-services, #nbd-upload-adjust, #nbu-upload-warning, #nbu-upload-nbo-options {
            width: calc(100% - 30px);
            height: calc(100% - 60px);
            top: 45px;
        }
        .nbu-mobile {
            display: inline-flex;
        }
        .nbu-upload-wrap {
            overflow: hidden;
        }
        .nbu-upload-exist {
            overflow: unset;
            overflow-y: auto;
            flex-wrap: unset;
            height: 100%;
            align-self: center;
            flex-direction: row;
            width: unset;
            align-items: center;
            padding: 0;
            justify-content: unset;
        }
        .nbu-upload-item-inner-wrap:first-child {
            margin-left: 25px;
        }
        .nbu-upload-item-inner, .nbu-upload-zone {
            flex-shrink: 0;
        }
        .nbu-upload-zone.nbu-before {
            margin-right: 25px;
        }
        .nbu-upload-exist:after {
            content: "";
            width: 25px;
            height: 100%;
            flex-shrink: 0;
        }
        .nbu-no-image .nbu-upload-exist:after {
            width: 0;
        }
        .nbu-before {
            margin-left: 25px;
        }
        .nbu-adjust-body {
            overflow: auto;
            padding: 15px;
        }
        .nbu-no-image-title {
            top: 20px;
        }
    }
</style>
<script type="text/javascript">
    var nbuApp = angular.module('nbuApp', []);
</script>
<div class="nbu-advanced-upload-inner" id="nbu-advanced-upload">
    <div class="nbu-upload-ctrl" ng-controller="uploadCtrl" ng-cloak id="nbu-advanced-upload-ctrl">
        <div class="nbu-top-bar">
            <?php if( ! isset( $nbau_task ) || $nbau_task == 'new' ): ?>
            <h2 class="nbu-upload-header-text">
                <?php _e('Upload photos', 'web-to-print-online-designer'); ?>
                <?php if( $nbu_ui_mode == 2 && isset( $show_nbo_option ) && $show_nbo_option ): ?>
                    <svg class="nbau-trigger-options" ng-click="_showPopup( 'options' )" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><g><path fill="#404762" d="M19.14,12.94c0.04-0.3,0.06-0.61,0.06-0.94c0-0.32-0.02-0.64-0.07-0.94l2.03-1.58c0.18-0.14,0.23-0.41,0.12-0.61 l-1.92-3.32c-0.12-0.22-0.37-0.29-0.59-0.22l-2.39,0.96c-0.5-0.38-1.03-0.7-1.62-0.94L14.4,2.81c-0.04-0.24-0.24-0.41-0.48-0.41 h-3.84c-0.24,0-0.43,0.17-0.47,0.41L9.25,5.35C8.66,5.59,8.12,5.92,7.63,6.29L5.24,5.33c-0.22-0.08-0.47,0-0.59,0.22L2.74,8.87 C2.62,9.08,2.66,9.34,2.86,9.48l2.03,1.58C4.84,11.36,4.8,11.69,4.8,12s0.02,0.64,0.07,0.94l-2.03,1.58 c-0.18,0.14-0.23,0.41-0.12,0.61l1.92,3.32c0.12,0.22,0.37,0.29,0.59,0.22l2.39-0.96c0.5,0.38,1.03,0.7,1.62,0.94l0.36,2.54 c0.05,0.24,0.24,0.41,0.48,0.41h3.84c0.24,0,0.44-0.17,0.47-0.41l0.36-2.54c0.59-0.24,1.13-0.56,1.62-0.94l2.39,0.96 c0.22,0.08,0.47,0,0.59-0.22l1.92-3.32c0.12-0.22,0.07-0.47-0.12-0.61L19.14,12.94z M12,15.6c-1.98,0-3.6-1.62-3.6-3.6 s1.62-3.6,3.6-3.6s3.6,1.62,3.6,3.6S13.98,15.6,12,15.6z"/></g></svg>
                <?php endif; ?>
            </h2>
            <?php do_action( 'nbu_upload_header', $pid ); ?>
            <div class="nbu-upload-options" ng-show="config.uploadOptions.length > 0">
                <div class="nbu-upload-options-inner">
                    <div class="nbu-upload-option" ng-repeat="option in config.uploadOptions" ng-class="option.selected == 'on' ? 'active' : '' " ng-click="selectUploadOption( $index )">
                        <img class="nbu-upload-option-thumb" ng-src="{{option.image_url}}"/>
                        <div class="nbu-upload-option-title">{{option.name}}</div>
                    </div>
                </div>
            </div>
            <div class="nbu-upload-options" ng-show="config.uploadOptions.length == 0">
                <ng-include src="'nbad.extension'"></ng-include>
            </div>
            <?php else: ?>
            <h2 class="nbu-upload-header-text"><?php _e('Upload photos', 'web-to-print-online-designer'); ?></h2>
            <div class="nbu-upload-options">
                <ng-include src="'nbad.extension'"></ng-include>
            </div>
            <?php endif; ?>
        </div>
        <div class="nbu-upload-wrap" nbu-dnd-file="uploadFile(files)" ng-class="uploadedImages.length == 0 ? 'nbu-no-image' : ''">
            <div class="nbu-upload-exist">
                <div class="nbu-no-image-title" ng-if="uploadedImages.length == 0"><?php _e('Pick some photos!', 'web-to-print-online-designer'); ?></div>
                <div class="nbu-upload-item-inner-wrap" ng-repeat="img in uploadedImages" 
                    ng-style="{
                        width: config.productInFrameWidth + 'px',
                        'background-image': ( config.selectedOptions && config.selectedOptions.frame && config.selectedOptions.frame.frameUrl != '' ) ? ( 'url(' + config.selectedOptions.frame.frameUrl + ')' ) : 'none'
                    }">
                    <div class="nbu-upload-item-inner" ng-style="{width: config.productInFrameWidth + 'px'}">
                        <img 
                            class="nbu-upload-image {{img.styleClass}}"
                            ng-click="showAction($index)"
                            ng-src="{{img.preview}}" />
                        <div class="nbu-upload-shadow" ></div>
                    </div>
                </div>
                <div class="nbu-upload-zone nbu-after" ng-hide="uploadedImages.length >= config.max">
                    <div class="nbu-upload-zone-inner">
                        <div class="nbu-upload-zone-top" ng-click="triggerUpload()">
                            <div>
                                <svg height="24px" width="24px" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path fill="#404762" d="m130.5 182.066406-28.285156-28.28125 153.785156-153.785156 153.785156 153.785156-28.285156 28.28125-105.5-105.5v355.71875h-40v-355.71875zm-130.5 289.21875v40h512v-40zm0 0"/></svg>
                            </div>
                            <div class="nbu-text"><?php _e('Upload photos', 'web-to-print-online-designer'); ?></div>
                        </div>
                        <div class="nbu-upload-zone-bottom" ng-click="_showPopup( 'services' )">
                            <div>
                                <?php if( $enbleFb == 'yes' && $fbID != '' ): ?>
                                <svg version="1.1" width="24px" height="24px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 408.788 408.788" style="enable-background:new 0 0 408.788 408.788;" xml:space="preserve"><path style="fill:#475993;" d="M353.701,0H55.087C24.665,0,0.002,24.662,0.002,55.085v298.616c0,30.423,24.662,55.085,55.085,55.085 h147.275l0.251-146.078h-37.951c-4.932,0-8.935-3.988-8.954-8.92l-0.182-47.087c-0.019-4.959,3.996-8.989,8.955-8.989h37.882 v-45.498c0-52.8,32.247-81.55,79.348-81.55h38.65c4.945,0,8.955,4.009,8.955,8.955v39.704c0,4.944-4.007,8.952-8.95,8.955 l-23.719,0.011c-25.615,0-30.575,12.172-30.575,30.035v39.389h56.285c5.363,0,9.524,4.683,8.892,10.009l-5.581,47.087 c-0.534,4.506-4.355,7.901-8.892,7.901h-50.453l-0.251,146.078h87.631c30.422,0,55.084-24.662,55.084-55.084V55.085 C408.786,24.662,384.124,0,353.701,0z"/></svg>
                                <?php endif; ?>
                                <?php if( $enbleIns == 'yes' && $insID != '' ): ?>
                                <svg height="24px" width="24px" viewBox="0 0 512 512.00038"  xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><radialGradient id="a" cx="104.9571229418" cy="447.4474330468" gradientUnits="userSpaceOnUse" r="564.64588968"><stop offset="0" stop-color="#fae100"/><stop offset=".0544" stop-color="#fadc04"/><stop offset=".1167" stop-color="#fbce0e"/><stop offset=".1829" stop-color="#fcb720"/><stop offset=".2514" stop-color="#fe9838"/><stop offset=".3054" stop-color="#ff7950"/><stop offset=".4922" stop-color="#ff1c74"/><stop offset="1" stop-color="#6c1cd1"/></radialGradient><linearGradient id="b" gradientUnits="userSpaceOnUse" x1="196.3726539866" x2="-671.0159540134" y1="222.4596783332" y2="-265.4464136668"><stop offset="0" stop-color="#a1b5d8" stop-opacity="0"/><stop offset=".3094" stop-color="#90a2bd" stop-opacity=".309804"/><stop offset=".7554" stop-color="#7c8b9c" stop-opacity=".756863"/><stop offset="1" stop-color="#748290"/></linearGradient><linearGradient id="c" gradientUnits="userSpaceOnUse" x1="256.0003" x2="256.0003" y1="451.9660859688" y2="531.773969673"><stop offset="0" stop-color="#fae100" stop-opacity="0"/><stop offset=".3068" stop-color="#fca800" stop-opacity=".305882"/><stop offset=".6275" stop-color="#fe7300" stop-opacity=".627451"/><stop offset=".8685" stop-color="#ff5200" stop-opacity=".866667"/><stop offset="1" stop-color="#ff4500"/></linearGradient><linearGradient id="d"><stop offset="0" stop-color="#833ab4" stop-opacity="0"/><stop offset="1" stop-color="#833ab4"/></linearGradient><linearGradient id="e" gradientUnits="userSpaceOnUse" x1="226.8724066998" x2="100.1606848024" xlink:href="#d" y1="226.147987369" y2="99.4361650794"/><linearGradient id="f" gradientUnits="userSpaceOnUse" x1="350.899540777" x2="287.6555669352" xlink:href="#d" y1="468.287448276" y2="170.1375727138"/><linearGradient id="g" gradientUnits="userSpaceOnUse" x1="374.965057" x2="120.9410670648" xlink:href="#d" y1="374.9649673922" y2="120.9408770648"/><linearGradient id="h" gradientUnits="userSpaceOnUse" x1="393.806665096" x2="309.8058007666" xlink:href="#d" y1="221.2631037014" y2="137.2623397642"/><linearGradient id="i" gradientUnits="userSpaceOnUse" x1="357.6582448576" x2="150.5426107646" y1="155.0495285836" y2="362.1651626766"><stop offset="0" stop-color="#833ab4"/><stop offset=".0922" stop-color="#9c3495"/><stop offset=".2927" stop-color="#dc2546"/><stop offset=".392" stop-color="#fd1d1d"/><stop offset=".5589" stop-color="#fc6831"/><stop offset=".6887" stop-color="#fc9b40"/><stop offset=".7521" stop-color="#fcaf45"/><stop offset=".7806" stop-color="#fdb750"/><stop offset=".8656" stop-color="#fecb6a"/><stop offset=".9415" stop-color="#ffd87a"/><stop offset="1" stop-color="#ffdc80"/></linearGradient><path d="m503.234375 91.578125c-4.660156-43.664063-39.144531-78.15625-82.8125-82.8125-109.507813-11.6875-219.335937-11.6875-328.839844 0-43.667969 4.660156-78.15625 39.148437-82.816406 82.8125-11.6875 109.503906-11.6875 219.335937 0 328.839844 4.660156 43.667969 39.148437 78.15625 82.8125 82.816406 109.503906 11.6875 219.335937 11.6875 328.84375 0 43.667969-4.660156 78.152344-39.148437 82.8125-82.816406 11.6875-109.503907 11.6875-219.332031 0-328.839844zm0 0" fill="url(#a)"/><path d="m475.386719 110.097656c-4.132813-38.746094-34.734375-69.351562-73.484375-73.488281-97.171875-10.367187-194.632813-10.367187-291.804688 0-38.746094 4.136719-69.351562 34.742187-73.488281 73.488281-10.367187 97.171875-10.367187 194.632813 0 291.800782 4.136719 38.75 34.742187 69.355468 73.488281 73.488281 97.171875 10.371093 194.632813 10.371093 291.800782 0 38.75-4.132813 69.355468-34.738281 73.488281-73.488281 10.371093-97.167969 10.371093-194.628907 0-291.800782zm0 0" fill="url(#b)"/><path d="m7.671875 409.804688c.351563 3.539062.714844 7.078124 1.09375 10.617187 4.660156 43.664063 39.148437 78.152344 82.816406 82.8125 109.503907 11.6875 219.335938 11.6875 328.839844 0 43.667969-4.660156 78.152344-39.148437 82.8125-82.8125.378906-3.539063.742187-7.078125 1.097656-10.617187zm0 0" fill="url(#c)"/><path d="m503.234375 420.417969c6.28125-58.839844 9.179687-117.773438 8.710937-176.699219l-117.03125-117.03125c-14.621093-16.691406-35.976562-27.109375-61.070312-28.011719-51.605469-1.859375-103.375-1.765625-154.988281.007813-42.867188 1.476562-72.84375 30.289062-80.53125 72.636718-1.355469 7.476563-2.167969 15.050782-3.234375 22.582032v124.148437c.589844 4.023438 1.457031 8.027344 1.726562 12.074219 1.71875 25.757812 12.304688 47.820312 29.253906 62.746094l119.09375 119.089844c58.445313.410156 116.894532-2.496094 175.257813-8.726563 43.667969-4.660156 78.152344-39.148437 82.8125-82.816406zm0 0" fill="url(#e)"/><path d="m503.234375 420.421875c-4.65625 43.660156-39.152344 78.15625-82.8125 82.8125-58.355469 6.226563-116.816406 9.136719-175.253906 8.726563l-118.914063-118.914063c13.785156 12.066406 31.753906 19.414063 52.605469 20.199219 51.601563 1.9375 103.382813 1.886718 154.984375.027344 46.671875-1.6875 80.445312-36.230469 81.902344-82.902344 1.554687-49.554688 1.554687-99.238282 0-148.792969-.664063-21.53125-8.222656-40.476563-20.753906-54.8125l116.957031 116.957031c.460937 58.917969-2.4375 117.859375-8.714844 176.699219zm0 0" fill="url(#f)"/><path d="m316.414062 200.558594c-14.992187-16.324219-36.503906-26.566406-60.414062-26.566406-45.289062 0-82.007812 36.71875-82.007812 82.007812 0 23.910156 10.242187 45.421875 26.566406 60.414062l189.738281 189.738282c10.042969-.875 20.085937-1.847656 30.121094-2.917969 43.667969-4.660156 78.15625-39.148437 82.816406-82.816406 1.070313-10.035157 2.042969-20.078125 2.917969-30.121094zm0 0" fill="url(#g)"/><path d="m511.007812 311.152344-152.703124-152.699219c-3.5625-4.675781-9.175782-7.710937-15.507813-7.710937-10.773437 0-19.511719 8.734374-19.511719 19.511718 0 6.332032 3.035156 11.945313 7.710938 15.507813l177.28125 177.285156c1.203125-17.292969 2.113281-34.59375 2.730468-51.894531zm0 0" fill="url(#h)"/><path d="m95.089844 193.902344c1.066406-7.53125 1.878906-15.105469 3.234375-22.582032 7.683593-42.347656 37.664062-71.160156 80.53125-72.636718 51.613281-1.773438 103.382812-1.867188 154.988281-.007813 46.65625 1.679688 80.445312 36.226563 81.902344 82.898438 1.550781 49.558593 1.550781 99.238281 0 148.796875-1.457032 46.671875-35.234375 81.214844-81.898438 82.898437-51.605468 1.863281-103.386718 1.910157-154.988281-.027343-46.664063-1.753907-78.921875-36.378907-82.042969-83.121094-.269531-4.042969-1.136718-8.050782-1.726562-12.074219 0-41.382813 0-82.765625 0-124.144531zm160.953125 191.707031c23.617187 0 47.257812.707031 70.84375-.164063 36.980469-1.371093 59.726562-23.441406 60.589843-60.386718 1.070313-46.035156 1.070313-92.132813 0-138.171875-.863281-36.9375-23.625-59.523438-60.589843-60.308594-46.917969-.992187-93.886719-.984375-140.804688 0-36.683593.769531-59.496093 22.898437-60.492187 59.429687-1.265625 46.617188-1.265625 93.316407 0 139.933594.996094 36.527344 23.808594 58.144532 60.496094 59.503906 23.289062.867188 46.636718.164063 69.957031.164063zm0 0" fill="url(#i)"/><g fill="#fff"><path d="m95.089844 193.902344c1.066406-7.53125 1.878906-15.105469 3.234375-22.582032 7.683593-42.347656 37.664062-71.160156 80.53125-72.636718 51.613281-1.773438 103.382812-1.867188 154.988281-.007813 46.65625 1.679688 80.445312 36.226563 81.902344 82.898438 1.550781 49.558593 1.550781 99.238281 0 148.796875-1.457032 46.671875-35.234375 81.214844-81.898438 82.898437-51.605468 1.863281-103.386718 1.910157-154.988281-.027343-46.664063-1.753907-78.921875-36.378907-82.042969-83.121094-.269531-4.042969-1.136718-8.050782-1.726562-12.074219 0-41.382813 0-82.765625 0-124.144531zm160.953125 191.707031c23.617187 0 47.257812.707031 70.84375-.164063 36.980469-1.371093 59.726562-23.441406 60.589843-60.386718 1.070313-46.035156 1.070313-92.132813 0-138.171875-.863281-36.9375-23.625-59.523438-60.589843-60.308594-46.917969-.992187-93.886719-.984375-140.804688 0-36.683593.769531-59.496093 22.898437-60.492187 59.429687-1.265625 46.617188-1.265625 93.316407 0 139.933594.996094 36.527344 23.808594 58.144532 60.496094 59.503906 23.289062.867188 46.636718.164063 69.957031.164063zm0 0"/><path d="m256 173.996094c-45.289062 0-82.007812 36.714844-82.007812 82.003906 0 45.292969 36.71875 82.007812 82.007812 82.007812 45.292969 0 82.007812-36.714843 82.007812-82.007812 0-45.289062-36.714843-82.003906-82.007812-82.003906zm0 135.777344c-29.699219 0-53.773438-24.074219-53.773438-53.773438s24.074219-53.773438 53.773438-53.773438 53.773438 24.074219 53.773438 53.773438-24.074219 53.773438-53.773438 53.773438zm0 0"/><path d="m362.304688 170.253906c0 10.773438-8.734376 19.507813-19.507813 19.507813s-19.511719-8.734375-19.511719-19.507813c0-10.777344 8.738282-19.511718 19.511719-19.511718s19.507813 8.734374 19.507813 19.511718zm0 0"/></g></svg>
                                <?php endif; ?>
                                <?php if( $enbleGG == 'yes' && $gaKey != '' && $ggID != '' ): ?>
                                <svg version="1.1" width="24px" height="24px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 291.136 291.136" style="enable-background:new 0 0 291.136 291.136;" xml:space="preserve"><path style="fill:#EFC75E;" d="M206.199,180.832l0.81,1.384h1.62l77.709-0.337l4.798-0.036l-2.412-4.142L196.768,19.414 l-0.801-1.384h-1.62l-77.728,0.319l-4.798,0.018l2.422,4.142C114.243,22.509,206.199,180.832,206.199,180.832z"/><path style="fill:#3DB39E;" d="M138.413,100.619l-0.819-1.393L97.583,30.793l-2.422-4.133l-2.394,4.142L0.819,189.681L0,191.055 l0.819,1.384l40.011,68.46l2.422,4.124l2.403-4.142l91.966-158.878C137.621,102.003,138.413,100.619,138.413,100.619z"/><path style="fill:#26A6D1;" d="M103.509,199.922h-1.602l-0.801,1.366l-39.437,67.659l-2.385,4.16h189.212l0.801-1.402 l39.446-67.659l2.385-4.124H103.509z"/></svg>
                                <?php endif; ?>
                                <?php if( $enbleDB == 'yes' && $dbID != '' ): ?>
                                <svg width="24px" height="24px" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 447.232 447.232" style="enable-background:new 0 0 447.232 447.232;" xml:space="preserve"><path style="fill:#1587EA;" d="M207.527,251.676L92.903,177.758c-3.72-2.399-8.559-2.145-12.007,0.63L3.833,240.403 c-5.458,4.392-5.015,12.839,0.873,16.636l114.624,73.918c3.72,2.399,8.559,2.145,12.007-0.63l77.063-62.014 C213.858,263.92,213.415,255.473,207.527,251.676z"/><path style="fill:#1587EA;" d="M238.833,268.312l77.063,62.014c3.449,2.775,8.287,3.029,12.007,0.63l114.624-73.918 c5.888-3.797,6.331-12.244,0.873-16.636l-77.063-62.014c-3.449-2.775-8.287-3.029-12.007-0.63l-114.624,73.918 C233.819,255.473,233.375,263.92,238.833,268.312z"/><path style="fill:#1587EA;" d="M208.4,74.196l-77.063-62.014c-3.449-2.775-8.287-3.029-12.007-0.63L4.706,85.47 c-5.888,3.797-6.331,12.244-0.873,16.636l77.063,62.014c3.449,2.775,8.287,3.029,12.007,0.63l114.624-73.918 C213.415,87.035,213.858,78.588,208.4,74.196z"/><path style="fill:#1587EA;" d="M442.527,85.47L327.903,11.552c-3.72-2.399-8.559-2.145-12.007,0.63l-77.063,62.014 c-5.458,4.392-5.015,12.839,0.873,16.636l114.625,73.918c3.72,2.399,8.559,2.145,12.007-0.63l77.063-62.014 C448.858,97.713,448.415,89.266,442.527,85.47z"/><path style="fill:#1587EA;" d="M218,279.2l-86.3,68.841c-3.128,2.495-7.499,2.715-10.861,0.547L99.568,334.87 c-6.201-3.999-14.368,0.453-14.368,7.831v7.416c0,3.258,1.702,6.28,4.488,7.969l128.481,77.884c2.969,1.8,6.692,1.8,9.661,0 l128.481-77.884c2.786-1.689,4.488-4.71,4.488-7.969v-6.619c0-7.378-8.168-11.83-14.368-7.831l-20.024,12.913 c-3.368,2.172-7.748,1.947-10.876-0.559l-85.893-68.809C226.238,276.489,221.405,276.484,218,279.2z"/></svg>
                                <?php endif; ?>
                            </div>
                            <div class="nbu-text"><?php _e('Choose from online services', 'web-to-print-online-designer'); ?></div>
                        </div>
                        <svg class="nbu-plus-shape" height="40px" width="40px" viewBox="0 0 37.76 38.93"><path fill="rgb(176,176,176)" d="M21.22,0V17.2H37.76v4.39H21.22V38.93H16.54V21.59H0V17.2H16.54V0Z"></path></svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="nbd-upload-action-wrap">
            <span class="nbd-upload-action" ng-class="( imageUploading || ( config.min != '' && uploadedImages.length < config.min ) ) ? 'nbu-not-allowed' : ''" ng-click="submitFiles()"><?php _e('Complete', 'web-to-print-online-designer'); ?></span>
        </div>
        <div class="nbu-popup" ng-class="showPopup ? 'active' : ''">
            <div class="nbu-popup-inner">
                <div class="nbu-backdrop" ng-click="closePopup()"></div>
                <div id="nbd-upload-services" ng-class="showPopupServices ? 'active' : ''">
                    <div class="nbd-services-inner">
                        <div class="nbu-services-nav">
                            <?php if( $enbleFb == 'yes' && $fbID != '' ): ?>
                            <div class="nbu-services-list-item" ng-class="currentService == 'facebook' ? 'active' : ''" ng-click="switchServiceTab('facebook')">
                                <span class="nbu-services-list-item-icon">
                                    <svg height="36" viewBox="0 0 36 36" width="36" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><circle cx="18" cy="18" fill="#3b5998" r="18"/><path d="m20.956 15.345h-2.116v-1.524c.155-1.44 2.353-1.167 2.353-1.167v-2.476h-.002c-.076-.02-4.372-1.124-5.226 2.417v.002l-.006.024c-.134.442-.125 2.47-.123 2.724h-1.836v2.71h1.948v7.374h2.838v-7.373h2.17l.244-2.71h-.244z" fill="#fff"/></g></svg>
                                </span>
                                <span><?php _e('Facebook', 'web-to-print-online-designer'); ?></span>
                                <span ng-show="resource.facebook.logged" class="nbu-services-list-ite-logout" ng-click="signOut('facebook', $event)"><?php _e('Sign Out', 'web-to-print-online-designer'); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if( $enbleIns == 'yes' && $insID != '' ): ?>
                            <div class="nbu-services-list-item" ng-class="currentService == 'instagram' ? 'active' : ''" ng-click="switchServiceTab('instagram')">
                                <span class="nbu-services-list-item-icon">
                                    <svg height="56" viewBox="0 0 56 56" width="56" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><linearGradient id="a1095" x1="18.848%" x2="-13.344%" y1="-60.439%" y2="100%"><stop offset="0" stop-color="#2d18ff"/><stop offset=".50059" stop-color="#ff177a"/><stop offset="1" stop-color="#ffcf37"/></linearGradient><g fill="none"><path d="m28 56c-15.464 0-28-12.536-28-28s12.536-28 28-28 28 12.536 28 28-12.536 28-28 28z" fill="url(#a1095)"/><path d="m27.737 13c4.002 0 4.504.017 6.076.089 1.568.071 2.64.32 3.577.685.97.376 1.79.88 2.61 1.7a7.224 7.224 0 0 1 1.7 2.61c.364.937.613 2.008.685 3.577.072 1.572.089 2.074.089 6.076s-.017 4.504-.089 6.076c-.072 1.568-.32 2.64-.685 3.577-.377.97-.88 1.79-1.7 2.61a7.224 7.224 0 0 1 -2.61 1.7c-.937.364-2.009.613-3.577.685-1.572.072-2.074.089-6.076.089s-4.504-.017-6.076-.089c-1.569-.072-2.64-.32-3.577-.685a7.224 7.224 0 0 1 -2.61-1.7 7.223 7.223 0 0 1 -1.7-2.61c-.365-.937-.614-2.009-.685-3.577-.072-1.572-.089-2.074-.089-6.076s.017-4.504.089-6.076c.071-1.569.32-2.64.685-3.577.376-.97.88-1.791 1.7-2.61a7.223 7.223 0 0 1 2.61-1.7c.937-.365 2.008-.614 3.577-.685 1.572-.072 2.074-.089 6.076-.089zm0 2.655c-3.935 0-4.401.015-5.955.086-1.437.066-2.217.306-2.737.508a4.565 4.565 0 0 0 -1.694 1.102 4.565 4.565 0 0 0 -1.102 1.694c-.202.52-.442 1.3-.508 2.737-.07 1.554-.086 2.02-.086 5.955s.015 4.4.086 5.955c.066 1.437.306 2.217.508 2.736a4.566 4.566 0 0 0 1.102 1.695 4.565 4.565 0 0 0 1.694 1.102c.52.202 1.3.442 2.737.507 1.554.071 2.02.086 5.955.086s4.401-.015 5.955-.086c1.437-.065 2.217-.305 2.736-.507a4.565 4.565 0 0 0 1.695-1.102 4.565 4.565 0 0 0 1.102-1.695c.202-.52.442-1.3.507-2.736.071-1.554.086-2.02.086-5.955s-.015-4.401-.086-5.955c-.065-1.437-.305-2.217-.507-2.737a4.565 4.565 0 0 0 -1.102-1.694 4.565 4.565 0 0 0 -1.695-1.102c-.52-.202-1.3-.442-2.736-.508-1.554-.07-2.02-.086-5.955-.086zm0 4.514a7.568 7.568 0 1 1 0 15.135 7.568 7.568 0 0 1 0-15.135zm0 12.48a4.912 4.912 0 1 0 0-9.824 4.912 4.912 0 0 0 0 9.824zm9.635-12.779a1.768 1.768 0 1 1 -3.537 0 1.768 1.768 0 0 1 3.537 0z" fill="#fff"/></g></svg>
                                </span>
                                <span><?php _e('Instagram', 'web-to-print-online-designer'); ?></span>
                                <span ng-show="resource.instagram.logged" class="nbu-services-list-ite-logout" ng-click="signOut('instagram', $event)"><?php _e('Sign Out', 'web-to-print-online-designer'); ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if( $enbleGG == 'yes' && $gaKey != '' && $ggID != '' ): ?>
                            <div class="nbu-services-list-item" ng-class="currentService == 'drive' ? 'active' : ''" ng-click="switchServiceTab('drive')">
                                <span class="nbu-services-list-item-icon">
                                    <svg height="36" viewBox="0 0 36 36" width="36" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><circle cx="18" cy="18" fill="#fff" r="18"/><path d="m27 20h-6.286l-5.714-10h6.286z" fill="#ffd04d"/><path d="m13 26h10.571l3.429-5h-10.857z" fill="#4688f4"/><path d="m9 20.808 3.194 5.192 5.806-9.23-3.484-5.77z" fill="#1da362"/></g></svg>
                                </span>
                                <span><?php _e('Google Drive', 'web-to-print-online-designer'); ?></span>
                                <span class="nbu-services-list-ite-logout"></span>
                            </div>
                            <?php endif; ?>
                            <?php if( $enbleDB == 'yes' && $dbID != '' ): ?>
                            <div class="nbu-services-list-item" ng-class="currentService == 'dropbox' ? 'active' : ''" ng-click="switchServiceTab('dropbox')">
                                <span class="nbu-services-list-item-icon">
                                    <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 291.319 291.319" style="enable-background:new 0 0 291.319 291.319;" xml:space="preserve"><path style="fill:#3D9AE8;" d="M145.659,0c80.44,0,145.66,65.219,145.66,145.66c0,80.45-65.219,145.659-145.66,145.659 S0,226.109,0,145.66C0,65.219,65.219,0,145.659,0z"/><path style="fill:#FFFFFF;" d="M54.631,154.672l53.557,33.729l37.48-30.161l-54.003-32.173L54.631,154.672z M108.179,63.717 l-53.548,33.72l37.034,28.631l54.003-32.173C145.669,93.896,108.179,63.717,108.179,63.717z M236.697,97.437l-53.557-33.72 l-37.48,30.17l54.003,32.173C199.663,126.059,236.697,97.437,236.697,97.437z M145.66,158.241l37.48,30.161l53.557-33.729 l-37.034-28.613C199.663,126.059,145.659,158.241,145.66,158.241z M145.623,181.983l-38.254,26.401l-16.368-8.913v19.09 l54.622,27.238l54.613-27.238v-19.09l-16.368,8.913L145.623,181.983z"/></svg>
                                </span>
                                <span><?php _e('Dropbox', 'web-to-print-online-designer'); ?></span>
                                <span class="nbu-services-list-ite-logout"></span>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="nbu-services-wrap">
                            <div class="nbu-services-wrap-inner">
                                <div class="nbu-services-header">
                                    <span>&nbsp;</span>
                                    <span>{{serviceNames[currentService]}}</span>
                                    <span ng-click="closePopup()" class="nbu-close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#888" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/><path d="M0 0h24v24H0z" fill="none"/></svg></span>
                                </div>
                                <?php if( $enbleFb == 'yes' && $fbID != '' ): ?>
                                <div class="nbu-services-images" id="nbu-services-facebook" ng-show="currentService == 'facebook'" on-load-more data-type="facebook">
                                    <div class="nbu-facebook-login-wrap" ng-hide="resource.facebook.logged">
                                        <div><svg height="66" viewBox="0 0 36 36" width="56" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><circle cx="18" cy="18" fill="#3b5998" r="18"/><path d="m20.956 15.345h-2.116v-1.524c.155-1.44 2.353-1.167 2.353-1.167v-2.476h-.002c-.076-.02-4.372-1.124-5.226 2.417v.002l-.006.024c-.134.442-.125 2.47-.123 2.724h-1.836v2.71h1.948v7.374h2.838v-7.373h2.17l.244-2.71h-.244z" fill="#fff"/></g></svg></div>
                                        <p class="nbu-services-title"><?php _e('Select Files from Facebook', 'web-to-print-online-designer'); ?></p>
                                        <p class="nbu-services-des"><?php _e('You need to authenticate with Facebook.', 'web-to-print-online-designer'); ?></p>
                                        <p class="nbu-services-des"><?php _e('We only extract images and never modify or delete them.', 'web-to-print-online-designer'); ?></p>
                                        <div style="margin: 15px 0;">
                                            <div id="fb-root"></div>	
                                            <div class="fb-login-button" data-max-rows="1" data-size="medium" data-show-faces="false" data-auto-logout-link="false" data-scope="user_photos" onlogin="nbuOnFBLogin()"></div>
                                        </div>
                                        <p class="nbu-services-des"><?php _e('A new page will open to connect your account.', 'web-to-print-online-designer'); ?></p>
                                        <p class="nbu-services-des"><?php _e('To disconnect from Facebook click "Sign out" button in the menu.', 'web-to-print-online-designer'); ?></p>
                                    </div>
                                    <div class="nbu-service-img-grid" ng-show="resource.facebook.logged">
                                        <div ng-repeat="img in resource.facebook.images" class="nbu-service-img-wrap" ng-click="selectServiceImage( 'facebook', $index, $event )">
                                            <img class="nbu-service-img" ng-src="{{img.preview}}" />
                                            <span class="nbu-mark-icon-selected">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path fill="#404762" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                            </span>
                                            <div class="nbu-mark-selected"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if( $enbleIns == 'yes' && $insID != '' ): ?>
                                <div class="nbu-services-images" id="nbu-services-instagram" ng-show="currentService == 'instagram'">
                                    <div class="nbu-facebook-login-wrap" ng-hide="resource.instagram.logged">
                                        <div><svg height="56" viewBox="0 0 56 56" width="56" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><linearGradient id="a1096" x1="18.848%" x2="-13.344%" y1="-60.439%" y2="100%"><stop offset="0" stop-color="#2d18ff"/><stop offset=".50059" stop-color="#ff177a"/><stop offset="1" stop-color="#ffcf37"/></linearGradient><g fill="none"><path d="m28 56c-15.464 0-28-12.536-28-28s12.536-28 28-28 28 12.536 28 28-12.536 28-28 28z" fill="url(#a1096)"/><path d="m27.737 13c4.002 0 4.504.017 6.076.089 1.568.071 2.64.32 3.577.685.97.376 1.79.88 2.61 1.7a7.224 7.224 0 0 1 1.7 2.61c.364.937.613 2.008.685 3.577.072 1.572.089 2.074.089 6.076s-.017 4.504-.089 6.076c-.072 1.568-.32 2.64-.685 3.577-.377.97-.88 1.79-1.7 2.61a7.224 7.224 0 0 1 -2.61 1.7c-.937.364-2.009.613-3.577.685-1.572.072-2.074.089-6.076.089s-4.504-.017-6.076-.089c-1.569-.072-2.64-.32-3.577-.685a7.224 7.224 0 0 1 -2.61-1.7 7.223 7.223 0 0 1 -1.7-2.61c-.365-.937-.614-2.009-.685-3.577-.072-1.572-.089-2.074-.089-6.076s.017-4.504.089-6.076c.071-1.569.32-2.64.685-3.577.376-.97.88-1.791 1.7-2.61a7.223 7.223 0 0 1 2.61-1.7c.937-.365 2.008-.614 3.577-.685 1.572-.072 2.074-.089 6.076-.089zm0 2.655c-3.935 0-4.401.015-5.955.086-1.437.066-2.217.306-2.737.508a4.565 4.565 0 0 0 -1.694 1.102 4.565 4.565 0 0 0 -1.102 1.694c-.202.52-.442 1.3-.508 2.737-.07 1.554-.086 2.02-.086 5.955s.015 4.4.086 5.955c.066 1.437.306 2.217.508 2.736a4.566 4.566 0 0 0 1.102 1.695 4.565 4.565 0 0 0 1.694 1.102c.52.202 1.3.442 2.737.507 1.554.071 2.02.086 5.955.086s4.401-.015 5.955-.086c1.437-.065 2.217-.305 2.736-.507a4.565 4.565 0 0 0 1.695-1.102 4.565 4.565 0 0 0 1.102-1.695c.202-.52.442-1.3.507-2.736.071-1.554.086-2.02.086-5.955s-.015-4.401-.086-5.955c-.065-1.437-.305-2.217-.507-2.737a4.565 4.565 0 0 0 -1.102-1.694 4.565 4.565 0 0 0 -1.695-1.102c-.52-.202-1.3-.442-2.736-.508-1.554-.07-2.02-.086-5.955-.086zm0 4.514a7.568 7.568 0 1 1 0 15.135 7.568 7.568 0 0 1 0-15.135zm0 12.48a4.912 4.912 0 1 0 0-9.824 4.912 4.912 0 0 0 0 9.824zm9.635-12.779a1.768 1.768 0 1 1 -3.537 0 1.768 1.768 0 0 1 3.537 0z" fill="#fff"/></g></svg></div>
                                        <p class="nbu-services-title"><?php _e('Select Files from Instagram', 'web-to-print-online-designer'); ?></p>
                                        <p class="nbu-services-des"><?php _e('You need to authenticate with Instagram.', 'web-to-print-online-designer'); ?></p>
                                        <p class="nbu-services-des"><?php _e('We only extract images and never modify or delete them.', 'web-to-print-online-designer'); ?></p>
                                        <div style="margin: 15px 0;">
                                            <span ng-click="authenticateInstagram()" class="nbu-connect-btn"><?php _e('Connect Instagram','web-to-print-online-designer'); ?></span>
                                        </div>
                                        <p class="nbu-services-des"><?php _e('A new page will open to connect your account.', 'web-to-print-online-designer'); ?></p>
                                        <p class="nbu-services-des"><?php _e('To disconnect from Instagram click "Sign out" button in the menu.', 'web-to-print-online-designer'); ?></p>
                                    </div>
                                    <div class="nbu-service-img-grid" ng-show="resource.instagram.logged" on-load-more>
                                        <div ng-repeat="img in resource.instagram.images | limitTo : resource.instagram.currentPage * 20" class="nbu-service-img-wrap" ng-click="selectServiceImage( 'instagram', $index, $event )">
                                            <img class="nbu-service-img" ng-src="{{img.preview}}" />
                                            <span class="nbu-mark-icon-selected">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path fill="#404762" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                            </span>
                                            <div class="nbu-mark-selected"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if( $enbleGG == 'yes' && $gaKey != '' && $ggID != '' ): ?>
                                <div class="nbu-services-images" id="nbu-services-drive" ng-show="currentService == 'drive'">
                                    <div style="margin: 15px 0;">
                                        <p class="nbu-services-title"><?php _e('Select Files from Google Drive', 'web-to-print-online-designer'); ?></p>
                                        <p class="nbu-services-des"><?php _e('You need to authenticate with Google Drive.', 'web-to-print-online-designer'); ?></p>
                                        <div style="margin: 15px 0;">
                                            <span ng-click="initGoogleDrive()" class="nbu-connect-btn">
                                                <?php _e('Pick From Google Drive', 'web-to-print-online-designer'); ?>
                                            </span> 
                                        </div>
                                        <p class="nbu-services-des"><?php _e('A new page will open to connect your account.', 'web-to-print-online-designer'); ?></p>
                                        <script type="text/javascript" src="https://apis.google.com/js/api.js" gapi_processed="true"></script>
                                    </div>
                                    <div class="nbu-service-img-grid">
                                        <div ng-repeat="img in resource.drive.images" class="nbu-service-img-wrap" ng-click="selectServiceImage( 'drive', $index, $event )">
                                            <img class="nbu-service-img" ng-src="{{img.preview}}" />
                                            <span class="nbu-mark-icon-selected">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path fill="#404762" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                            </span>
                                            <div class="nbu-mark-selected"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <?php if( $enbleDB == 'yes' && $dbID != '' ): ?>
                                <div class="nbu-services-images" id="nbu-services-drive" ng-show="currentService == 'dropbox'">
                                    <div style="margin: 15px 0;">
                                        <p class="nbu-services-title"><?php _e('Select Files from Dropbox', 'web-to-print-online-designer'); ?></p>
                                        <p class="nbu-services-des"><?php _e('You need to authenticate with Dropbox.', 'web-to-print-online-designer'); ?></p>
                                        <div style="margin: 15px 0;">
                                            <div id="nbu_upload_dropbox"></div>
                                        </div>
                                        <p class="nbu-services-des"><?php _e('A new page will open to connect your account.', 'web-to-print-online-designer'); ?></p>
                                        <script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="<?php echo $dbID; ?>"></script>
                                    </div>
                                    <div class="nbu-service-img-grid">
                                        <div ng-repeat="img in resource.dropbox.images" class="nbu-service-img-wrap" ng-click="selectServiceImage( 'dropbox', $index, $event )">
                                            <img class="nbu-service-img" ng-src="{{img.preview}}" />
                                            <span class="nbu-mark-icon-selected">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M0 0h24v24H0z" fill="none"/><path fill="#404762" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                                            </span>
                                            <div class="nbu-mark-selected"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="nbu-services-footer" ng-class="serviceSelectedImages.length > 0 ? 'active' : ''">
                                    <div class="nbu-services-footer-inner">
                                        <span ><?php _e('Selected Photos:', 'web-to-print-online-designer'); ?> {{serviceSelectedImages.length}}</span>
                                        <span class="nbu-services-select" ng-click="prepareUploadImages()"><?php _e('Next', 'web-to-print-online-designer'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="nbd-upload-actions" ng-class="showPopupAction ? 'active' : ''">
                    <div class="nbd-upload-actions-inner" ng-show="adjustAction">
                        <span ng-click="adjustImage()" class="nbu-action"><?php _e('Adjust', 'web-to-print-online-designer'); ?></span>
                        <span ng-click="removeFile()" class="nbu-action nbd-remove"><?php _e('Remove', 'web-to-print-online-designer'); ?></span>
                        <span ng-click="closePopup()" class="nbu-action nbd-dismiss"><?php _e('Dismiss', 'web-to-print-online-designer'); ?></span>
                    </div>
                    <div class="nbd-upload-actions-inner" ng-show="closePopupAction">
                        <span ng-click="closePopup()" class="nbu-action"><?php _e('Continue Upload', 'web-to-print-online-designer'); ?></span>
                        <span ng-click="dismissUpload()" class="nbu-action nbd-remove"><?php _e('Dismiss Uploaded Files', 'web-to-print-online-designer'); ?></span>
                    </div>
                </div>
                <div id="nbd-upload-adjust" ng-class="showPopupAdjust ? 'active' : ''">
                    <div class="nbu-adjust-header">
                        <span ng-click="closePopup()" class="nbu-close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#888" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/><path d="M0 0h24v24H0z" fill="none"/></svg></span>
                        <span><?php _e('Adjust', 'web-to-print-online-designer'); ?></span>
                        <span class="nbu-active" ng-click="updateAdjust()" style="cursor: pointer;"><?php _e('Done', 'web-to-print-online-designer'); ?></span>
                    </div>
                    <div class="nbu-adjust-body">
                        <div class="nbu-adjust-title"><?php _e('Drag to adjust', 'web-to-print-online-designer'); ?></div>
                        <div class="nbu-adjust-con" ng-style="adjustmentData.wrapStyle">
                            <div class="nbu-upload-origin-wrap" ng-style="{
                                'width': adjustmentData.style.width * adjustmentData.zoom + 'px',
                                'height': adjustmentData.style.height * adjustmentData.zoom + 'px',
                                'top': adjustmentData.style.top + 'px',
                                'left': adjustmentData.style.left + 'px'
                            }">
                                <img class="nbu-upload-origin-img" src="{{adjustmentData.src}}"/>
                            </div>
                            <div class="nbu-adjust-frame-wrap" ng-style="adjustmentData.adjustWrapStyle" nbu-adjust-frame></div>
                        </div>
                    </div>
                </div>
                <div id="nbu-upload-warning" ng-class="showPopupWarning ? 'active' : ''">
                    <div class="nbu-dialog-content">
                        <div class="nbu-dialog-title"><?php _e('Low Image Quality', 'web-to-print-online-designer'); ?></div>
                        <div class="nbu-dialog-image">
                            <img ng-src="{{currentFilePreview}}"/>
                        </div>
                        <div class="nbu-dialog-text"><?php _e('This photo is actually pretty small. It will probably make a blurry tile!', 'web-to-print-online-designer'); ?></div>
                    </div>
                    <div class="nbu-dialog-action">
                        <div class="nbu-dialog-button nbu-remove" ng-click="keepLowQualityImage()"><?php _e('Keep Anyway', 'web-to-print-online-designer'); ?></div>
                        <div class="nbu-dialog-button" ng-click="removeLowQualityImage()"><?php _e('Remove From Order', 'web-to-print-online-designer'); ?></div>
                    </div>
                </div>
                <?php if( isset( $nbau_task ) && $nbau_task == 'new'  ): ?>
                <div id="nbu-upload-nbo-options" ng-class="showPopupOptions ? 'active' : ''">
                    <div class="nbu-options-header">
                        <span style="font-weight: bold;"><?php _e('Choose options', 'web-to-print-online-designer'); ?></span>
                        <span ng-click="closePopup()" class="nbu-close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#888" d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/><path d="M0 0h24v24H0z" fill="none"/></svg></span>
                    </div>
                    <div class="nbu-options-nbo-wrapper">
                        <?php woocommerce_template_single_add_to_cart(); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="nbu-drop-upload-zone" id="nbu-drop-upload-zone">
            <div class="nbu-drop-upload-zone-inner">
                <h2><?php _e('Drop file to upload', 'web-to-print-online-designer'); ?></h2>
                <input type="file" accept=".jpeg,.jpg,.png" style="display: none;" />
            </div>
        </div>
        <div class="nbu-processing" id="nbu-processing">
            <img src="<?php echo NBDESIGNER_ASSETS_URL.'images/spinner.svg'; ?>" />
        </div>
        <script type="text/ng-template" id="nbad.extension">
            <div class="nbu-upload-options-inner" style="flex-direction: column;justify-content: center;">
                <?php if( $option['allow_type'] != '' ): ?>
                <div><small><?php _e('Allow extensions', 'web-to-print-online-designer'); ?>: <?php echo $option['allow_type']; ?></small></div>
                <?php endif; ?>
                <?php if( $option['disallow_type'] != '' ): ?>
                <div><small><?php _e('Disallow extensions', 'web-to-print-online-designer'); ?>: <?php echo $option['disallow_type']; ?></small></div>
                <?php endif; ?>
            </div>
        </script>
    </div>
</div>
<script type="text/javascript">
    function nbauSetCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
        var expires = "expires=" + d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
    };
    function nbauGetCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    };
    var nbauUiMode = <?php echo $nbu_ui_mode; ?>;
    jQuery( document ).ready(function(){
        nbuApp.controller('uploadCtrl', ['$scope', '$http', '$timeout', function( $scope, $http, $timeout ){
            $scope.config = {
                uiMode: <?php echo $nbu_ui_mode; ?>,
                hasNboOptions: false,
                mustShowNboOptions: false,
                productWidth: <?php echo $option['product_width']; ?>,
                productHeight: <?php echo $option['product_height']; ?>,
                productInFrameWidth: 250,
                min: 1,
                max: parseInt(<?php echo $option['number']; ?>),
                mindpi: <?php echo $option['mindpi']; ?>,
                maxsize: parseInt(<?php echo $option['maxsize']; ?>),
                minsize: parseInt(<?php echo $option['minsize']; ?>),
                allowType: "<?php echo $option['allow_type']; ?>",
                disallowType: "<?php echo $option['disallow_type']; ?>",
                minWidth: <?php echo $option['min_width']; ?>,
                maxWidth: <?php echo $option['max_width']; ?>,
                minHeight: <?php echo $option['min_height']; ?>,
                maxHeight: <?php echo $option['max_height']; ?>,
                fbID: '<?php echo $fbID; ?>',
                insID: '<?php echo $insID; ?>',
                insUrl: '<?php echo $insUrl; ?>',
                ggKey: '<?php echo $gaKey; ?>',
                ggID: '<?php echo $ggID; ?>',
                dbID: '<?php echo $dbID; ?>',
                enbleFb: '<?php echo $enbleFb; ?>',
                enbleIns: '<?php echo $enbleIns; ?>',
                enbleGG: '<?php echo $enbleGG; ?>',
                enbleDB: '<?php echo $enbleDB; ?>',
                locale: '<?php echo $locale; ?>',
                unit: '<?php echo $unit; ?>',
                ins_redirect_url: '<?php echo NBDESIGNER_PLUGIN_URL.'includes/auth-instagram.php'; ?>',
                loading_img: '<?php echo NBDESIGNER_ASSETS_URL.'images/spinner.svg'; ?>',
                msg_exceed: '<?php _e('Exceed number of upload files!', 'web-to-print-online-designer'); ?>',
                msg_disallow: '<?php _e('Disallow extensions: ', 'web-to-print-online-designer'); ?>',
                msg_support: '<?php _e('Only support: ', 'web-to-print-online-designer'); ?>',
                msg_minsize: '<?php _e('Max file size', 'web-to-print-online-designer'); ?>',
                msg_maxsize: '<?php _e('Min file size', 'web-to-print-online-designer'); ?>',
                msg_min_no_file: '<?php _e('Please upload at least', 'web-to-print-online-designer'); ?>',
                msg_waiting: '<?php _e('Please try again later!', 'web-to-print-online-designer'); ?>',
                msg_files: '<?php _e('files', 'web-to-print-online-designer'); ?>',
                uploadOptions: [],
                selectedOptions: {},
                optionClass: '',
                warringContext: 'service'
            };
            $scope.serviceNames = {
                facebook: 'Facebook',
                instagram: 'Instagram',
                drive: 'Google Drive',
                dropbox: 'Dropbox'
            };
            $scope.serviceSelected = 0;
            $scope.serviceSelectedImages = [];
            $scope.currentService = "<?php echo $currentSv; ?>";
            $scope.currentFileIndex = 0;
            $scope.currentServiceImage = {};
            $scope.resource = {
                facebook: { logged: false, next: true, images: [] },
                instagram: { logged: false, images: [], data: [], currentPage: 1 },
                drive: { images: [] },
                dropbox: { images: [] }
            };
            $scope.uploadedImages = [];
            $scope.adjustmentData = {
                start: false,
                startZoom: false,
                style: {
                    top: 0,
                    left: 0,
                    width: 250,
                    height: 250
                },
                zoom: 1
            };
            $scope.selectServiceImage = function( type, $index, $event ){
                var index = type + '_' + $index,
                _index = $scope.serviceSelectedImages.indexOf( index );
                if( _index > -1 ){
                    $scope.serviceSelectedImages.splice(_index, 1);
                }else{
                    $scope.serviceSelectedImages.push( index );
                }
                var target = jQuery($event.target);
                if( jQuery($event.target).parents('.nbu-service-img-wrap').length > 0  ){
                    target = jQuery($event.target).parents('.nbu-service-img-wrap');
                }
                target.toggleClass('active');
            };
            $scope.prepareUploadImages = function(){
                if( $scope.serviceSelectedImages.length == 0 ){
                    $scope.serviceSelectedImages = [];
                    $scope.updateApp();
                    return;
                }
                jQuery( '.nbu-service-img-wrap' ).removeClass('active');
                $scope.closePopup();
                var image = $scope.serviceSelectedImages[0];
                $scope.serviceSelectedImages.shift();
                var arr = image.split('_');
                var resource_image = $scope.resource[ arr[0] ].images[ arr[1] ];
                $scope.currentServiceImage = {};
                angular.copy( resource_image, $scope.currentServiceImage );
                $scope.currentServiceImage.type = arr[0];
                if( ( $scope.config.minWidth != '' && angular.isDefined( resource_image.width ) )
                        || ( $scope.config.minHeight != '' && angular.isDefined( resource_image.height ) ) ){
                    if( ( ( $scope.config.minWidth != '' && angular.isDefined( resource_image.width ) ) && ( $scope.config.minWidth > resource_image.width ) )
                        || ( ( $scope.config.minHeight != '' && angular.isDefined( resource_image.height ) ) && ( $scope.config.minHeight > resource_image.height ) ) ){
                        $scope.currentFilePreview = resource_image.preview;
                        $scope.config.warringContext = 'service';
                        $scope._showPopup( 'warning' );
                    } else {
                        $scope.uploadServiceImages();
                    }
                } else {
                    $scope.uploadServiceImages();
                }
            };
            $scope.uploadServiceImages = function(  ){
                $scope.resource.imageFromUrl = $scope.currentServiceImage.url;
                $scope.uploadImageFromUrl( $scope.currentServiceImage.type, null, function(){
                    $scope.prepareUploadImages();
                });
            };
            $scope.keepLowQualityImage = function(){
                if( $scope.config.warringContext == 'service' ){
                    $scope.uploadServiceImages();
                } else {
                    $scope.hideLoadingImage( $scope.currentFileIndex, $scope.uploadedImages[ $scope.currentFileIndex ].tempData );
                }
                $scope.closePopup();
            };
            $scope.removeLowQualityImage = function(){
                if( $scope.config.warringContext == 'service' ){
                    $scope.prepareUploadImages();
                } else {
                    $scope.uploadedImages.splice($scope.currentFileIndex, 1);
                    $scope.getLoadingStatus();
                }
                $scope.closePopup();
            };
            $scope.showPopup = false;
            $scope.resetPopup = function(){
                $scope.showPopupAction = false;
                $scope.showPopupServices = false;
                $scope.showPopupAdjust = false;
                $scope.showPopupWarning = false;
                $scope.showPopupOptions = false;
                $scope.adjustAction = true;
                $scope.closePopupAction = false;
            };
            $scope._showPopup = function( type, action ){
                $scope.showPopup = true;
                $scope.resetPopup();
                switch( type ){
                    case 'action':
                        $scope.showPopupAction = true;
                        if( angular.isDefined( action ) ){
                            $scope.adjustAction = false;
                            $scope.closePopupAction = true;
                        }
                        break;
                    case 'adjust':
                        $scope.showPopupAdjust = true;
                        break;
                    case 'services':
                        $scope.showPopupServices = true;
                        break;
                    case 'warning':
                        $scope.showPopupWarning = true;
                        break;
                    case 'options':
                        $scope.showPopupOptions = true;
                        if( $scope.uploadedImages.length ){
                            jQuery('[name="add-to-cart"]').show();
                        }else{
                            jQuery('[name="add-to-cart"]').hide();
                        }
                        break;
                }
                $scope.updateApp();
            };
            $scope.closePopup = function(){
                $scope.showPopup = false;
                $scope.resetPopup();
            };
            $scope.removeFile = function(){
                if( $scope.imageUploading ){
                    alert( $scope.config.msg_waiting );
                    return;
                }
                var blob_url = $scope.uploadedImages[$scope.currentFileIndex].preview;
                $scope.revokeObjectURL( blob_url );
                $scope.uploadedImages.splice($scope.currentFileIndex, 1);
                $scope.closePopup();
            };
            $scope.adjustImage = function(){
                var currentImage = $scope.uploadedImages[ $scope.currentFileIndex ];
                $scope.adjustmentData.src = currentImage.src;
                $scope.adjustmentData.wrapStyle = {
                    width: $scope.config.productPreviewWidth + 'px',
                    height: $scope.config.productPreviewHeight + 'px'
                };
                $scope.adjustmentData.adjustWrapStyle = {
                    width: ( $scope.config.productPreviewWidth + 8 ) + 'px',
                    height: ( $scope.config.productPreviewHeight + 8 ) + 'px'
                };
                $scope.adjustmentData.scaleRatio = currentImage.width / currentImage.previewOriginWidth;
                $scope.adjustmentData.style.top = currentImage.previewTop * currentImage.zoom;
                $scope.adjustmentData.style.left = currentImage.previewLeft * currentImage.zoom;
                $scope.adjustmentData.style.width = currentImage.previewWidth;
                $scope.adjustmentData.style.height = currentImage.previewHeight;
                $scope.adjustmentData.zoom = currentImage.zoom;
                if( currentImage.width > $scope.config.productWidthInPx && currentImage.height > $scope.config.productHeightInPx ){
                    if( currentImage.width / currentImage.height > $scope.config.productWidthInPx / $scope.config.productHeightInPx ){
                        currentImage.maxZoom = currentImage.height / $scope.config.productHeightInPx;
                    }else{
                        currentImage.maxZoom = currentImage.width / $scope.config.productWidthInPx;
                    }
                    $scope.adjustmentData.maxZoom = currentImage.maxZoom;
                }else{
                    $scope.adjustmentData.maxZoom = false;
                }
                $scope._showPopup( 'adjust' );
            };
            $scope.$on('nbu:adjust:mousedown', function(event, e){
                $scope.onStartAdjust( e );
            });
            $scope.$on('nbu:adjust:mousemove', function(event, e){
                if( $scope.adjustmentData.start ) $scope.onAdjust( e );
            });
            $scope.$on('nbu:adjust:mouseup', function(event, e){
                if( $scope.adjustmentData.start ) $scope.onStopAdjust( e );
            });
            $scope.$on('nbu:adjust:wheel', function(event, e){
                $scope.onStartZoom( - e.deltaY / 1000 );
            });
            $scope.$on('nbu:adjust:pinchstart', function(event, touches){
                $scope.onPinchStart( touches );
            });
            $scope.$on('nbu:adjust:pinchmove', function(event, touches){
                $scope.onPinchMove( touches );
            });
            $scope.onStartAdjust = function( e ){
                var x = Number(e.clientX),
                    y = Number(e.clientY);
                if (x === undefined || y === undefined) return;
                $scope.adjustmentData.point = {
                    x: x,
                    y: y
                };
                $scope.adjustmentData.start = true;
            };
            $scope.onPinchStart = function( touches ){
                var pointA = {
                    x: Number( touches[0].clientX ),
                    y: Number( touches[0].clientY )
                },
                pointB = {
                    x: Number( touches[1].clientX ),
                    y: Number( touches[1].clientY )
                };
                $scope.adjustmentData.pinchDistance = Math.sqrt( Math.pow( pointA.y - pointB.y, 2 ) + Math.pow( pointA.x - pointB.x, 2 ) );
                $scope.adjustmentData.startZoom = true;
            };
            $scope.onPinchMove = function( touches ){
                var pointA = {
                    x: Number( touches[0].clientX ),
                    y: Number( touches[0].clientY )
                },
                pointB = {
                    x: Number( touches[1].clientX ),
                    y: Number( touches[1].clientY )
                };
                newPinchDistance = Math.sqrt( Math.pow( pointA.y - pointB.y, 2 ) + Math.pow( pointA.x - pointB.x, 2 ) );
                if ( $scope.zoomTimeout ) window.cancelAnimationFrame( $scope.zoomTimeout );
                $scope.zoomTimeout = window.requestAnimationFrame(function(){
                    if( !$scope.adjustmentData.startZoom ) return;
                    $scope.onStartZoom( newPinchDistance / $scope.adjustmentData.pinchDistance, true );
                    $scope.adjustmentData.pinchDistance = newPinchDistance;
                });
            };
            $scope.onStartZoom = function( zoomRatio, pinch ){
                if( !$scope.adjustmentData.maxZoom ) return;
                var newZoom = angular.isDefined( pinch ) ? $scope.adjustmentData.zoom * zoomRatio : $scope.adjustmentData.zoom + zoomRatio;
                if( newZoom < 1 ) newZoom = 1;
                if( newZoom > $scope.adjustmentData.maxZoom ) newZoom = $scope.adjustmentData.maxZoom;
                var lastZoom = $scope.adjustmentData.zoom;
                $scope.adjustmentData.zoom = newZoom;
                var newTop = $scope.adjustmentData.style.top - $scope.adjustmentData.style.height / 2 * ( newZoom - lastZoom ),
                    newLeft = $scope.adjustmentData.style.left - $scope.adjustmentData.style.width / 2 * ( newZoom - lastZoom );
                if( newTop > 0 ) newTop = 0;
                if( newLeft > 0 ) newLeft = 0;
                if( newTop <= ( $scope.config.productPreviewHeight - $scope.adjustmentData.style.height * newZoom ) ){
                    newTop = $scope.config.productPreviewHeight - $scope.adjustmentData.style.height * newZoom;
                }
                if( newLeft <= ( $scope.config.productPreviewWidth - $scope.adjustmentData.style.width * newZoom ) ){
                    newLeft = $scope.config.productPreviewWidth - $scope.adjustmentData.style.width * newZoom;
                } 
                $scope.adjustmentData.style.top = newTop;
                $scope.adjustmentData.style.left = newLeft;
                $scope.updateApp();
            };
            $scope.onAdjust = function( e ){
                var x = Number(e.clientX),
                    y = Number(e.clientY);
                if ( $scope.dragTimeout ) window.cancelAnimationFrame( $scope.dragTimeout );
                $scope.dragTimeout = window.requestAnimationFrame(function(){
                    if (x === undefined || y === undefined) return;
                    if ( !$scope.adjustmentData.start ) return;
                    var offsetX = x - $scope.adjustmentData.point.x,
                        offsetY = y - $scope.adjustmentData.point.y;
                    $scope.adjustmentData.point.x = x;
                    $scope.adjustmentData.point.y = y;
                    $scope.updateApp();
                    $scope.onAdjustImage( offsetX, offsetY );
                });
            };
            $scope.onStopAdjust = function( e ){
                $scope.adjustmentData.start = false;
                $scope.adjustmentData.startZoom = false;
                $scope.adjustmentData.point = {
                    x: undefined,
                    y: undefined
                };
            };
            $scope.onAdjustImage = function( offsetX, offsetY ){
                var newTop = $scope.adjustmentData.style.top + offsetY,
                    newLeft = $scope.adjustmentData.style.left + offsetX;
                if( newTop > 0 ) newTop = 0;
                if( newLeft > 0 ) newLeft = 0;
                if( newTop <= ( $scope.config.productPreviewHeight - $scope.adjustmentData.style.height * $scope.adjustmentData.zoom ) ){
                    newTop = $scope.config.productPreviewHeight - $scope.adjustmentData.style.height * $scope.adjustmentData.zoom;
                }
                if( newLeft <= ( $scope.config.productPreviewWidth - $scope.adjustmentData.style.width * $scope.adjustmentData.zoom ) ){
                    newLeft = $scope.config.productPreviewWidth - $scope.adjustmentData.style.width * $scope.adjustmentData.zoom;
                }
                $scope.adjustmentData.style.top = newTop;
                $scope.adjustmentData.style.left = newLeft;
                $scope.updateApp();
            };
            $scope.updateAdjust = function(){
                var currentImage = $scope.uploadedImages[ $scope.currentFileIndex ];
                currentImage.zoom = $scope.adjustmentData.zoom;
                currentImage.previewLeft = $scope.adjustmentData.style.left / currentImage.zoom;
                currentImage.previewTop = $scope.adjustmentData.style.top / currentImage.zoom;
                currentImage.previewCropLeft = 0 - currentImage.previewLeft / currentImage.ratio;
                currentImage.previewCropTop = 0 - currentImage.previewTop / currentImage.ratio;
                $scope.updateApp();
                currentImage.preview = $scope.config.loading_img;
                currentImage.styleClass = 'nbu-loading';
                var img = new Image(),
                canvas = document.createElement('canvas'),
                ctx = canvas.getContext('2d');
                canvas.width = $scope.config.productPreviewWidth;
                canvas.height = $scope.config.productPreviewHeight;
                img.onload = function(){
                    ctx.drawImage( img, currentImage.previewCropLeft, currentImage.previewCropTop, currentImage.previewCropWidth / currentImage.zoom , currentImage.previewCropHeight / currentImage.zoom, 0, 0, $scope.config.productPreviewWidth, $scope.config.productPreviewHeight );
                    var uri = canvas.toDataURL(),
                    blob = $scope.makeblob( uri );
                    currentImage.preview = window.URL.createObjectURL( blob );
                    currentImage.styleClass = $scope.config.optionClass;
                    $scope.updateApp();
                };
                img.src = currentImage.src;
                currentImage.cropLeft = currentImage.previewCropLeft * currentImage.previewRatio;
                currentImage.cropTop = currentImage.previewCropTop * currentImage.previewRatio;
                currentImage.cropWidth = currentImage.previewCropWidth / currentImage.zoom * currentImage.previewRatio;
                currentImage.cropHeight = currentImage.previewCropHeight / currentImage.zoom * currentImage.previewRatio;
                $scope.getAdjustedImage( $scope.currentFileIndex );
                $scope.closePopup();
            };
            $scope.updatePreviewUploadedImages = function(){
                angular.forEach( $scope.uploadedImages, function( data, index ){
                    $scope.processImage( data, function( newData ){
                        $scope.uploadedImages[index].styleClass = $scope.config.optionClass;
                        angular.merge($scope.uploadedImages[index], newData);
                        $scope.updateApp();
                    });
                });

                $scope.getLoadingStatus();
                $scope.updateApp();
            };
            $scope.showAction = function( $index ){
                $scope.currentFileIndex = $index;
                $scope._showPopup('action');
            };
            $scope.signOut = function(type, event ){
                event.preventDefault();
                event.stopPropagation();
                switch( type ){
                    case 'facebook':
                        FB.logout(function(response) {
                            $scope.resource.facebook.logged = false;
                            $scope.resource.facebook.images = [];
                            delete $scope.resource.facebook.nextUrl;
                            $scope.resource.facebook.next = true;
                            $scope.updateApp();
                        });
                        break;
                    case 'instagram':
                        $scope.resource.instagram.logged = false;
                        nbauSetCookie( 'nbd_instagram_token', '', -1/25 );
                        $scope.resource.instagram.token = '';
                        $scope.resource.instagram.images = [];
                        $scope.resource.instagram.data = [];
                        $scope.updateApp();
                        break;
                }
            };
            $scope.switchServiceTab = function( type ){
                $scope.currentService = type;
            };
            $scope.initFacebook = function(){
                window.fbAsyncInit = function() {
                    FB.init({
                        appId      : $scope.config.fbID,
                        status     : true, 
                        cookie     : true,      
                        xfbml      : true,
                        autoLogAppEvents       : true,
                        version    : 'v3.0'
                    });
                };
                (function(d, s, id){
                      var js, fjs = d.getElementsByTagName(s)[0];
                      if (d.getElementById(id)) {return;}
                      js = d.createElement(s); js.id = id;
                      js.src = "https://connect.facebook.net/en_US/sdk.js";
                      fjs.parentNode.insertBefore(js, fjs);
                 }(document, 'script', 'facebook-jssdk')); 
            };
            $scope.getFacebookPhoto = function( uid, accessToken ){
                if( !$scope.resource.facebook.logged ) return;
                $scope.resource.facebook.uid = angular.isDefined( uid ) ? uid : $scope.resource.facebook.uid;
                $scope.resource.facebook.accessToken = angular.isDefined( accessToken ) ? accessToken : $scope.resource.facebook.accessToken;
                $scope.resource.facebook.nextUrl = angular.isUndefined( $scope.resource.facebook.nextUrl ) ? "https://graph.facebook.com/" + $scope.resource.facebook.uid + "/photos/uploaded/?limit=20&fields=source,images,link&access_token=" + $scope.resource.facebook.accessToken : $scope.resource.facebook.nextUrl;
                if( $scope.resource.facebook.next ){
                    $http({method: 'GET', url: $scope.resource.facebook.nextUrl}).then(function successCallback(response) {
                        $scope.serviceImageLoading = false;
                        var data = response.data;
                        if( angular.isDefined( data.data ) && data.data.length > 0 ){
                            angular.forEach(data.data, function( file, index ){
                                $scope.resource.facebook.images.push({
                                    extenal: 1,
                                    preview :  file.images[file.images.length - 1].source,
                                    width: file.images[0].width,
                                    height: file.images[0].height,
                                    url :  file.images[0].source
                                });
                                if (data.paging.next) {
                                    $scope.resource.facebook.nextUrl = data.paging.next;
                                    $scope.resource.facebook.next = true;
                                }else{
                                    $scope.resource.facebook.next = false;
                                }
                            });
                        }
                        $scope.updateApp();
                    }, function errorCallback(response) {
                        $scope.serviceImageLoading = false;
                        console.log('loadFacebookPhotos');
                    });
                }
                $scope.updateApp();
            };
            $scope.getInstagramAccessToken = function(){
                var formData = new FormData();
                formData.append( "action", 'nbd_get_instagram_token' );
                if( $scope.config.uiMode == 1 ){
                    var ajax_url = nbds_frontend.url,
                        nonce = nbds_frontend.nonce;
                } else {
                    var ajax_url = nbauObject.ajax_url,
                        nonce = nbauObject.nonce;
                }
                formData.append( "nonce", nonce );
                formData.append( "code", $scope.resource.instagram.code );
                var config = {
                    transformRequest: angular.identity,
                    transformResponse: angular.identity,
                    headers: {
                        'Content-Type': undefined
                    }
                };
                $http.post(ajax_url, formData, config).then(
                    function(response) {
                        var data = JSON.parse(response.data);
                        if( data.flag == 1 ){
                            $scope.resource.instagram.token = data.access_token;
                            $scope.getInstagramPhoto();
                            nbauSetCookie( 'nbd_instagram_token', $scope.resource.instagram.token, 1/25 );
                        }
                    },
                    function(response) {
                        console.log(response);
                    }
                );
            };
            $scope.authenticateInstagram = function(){
                $scope.resource.instagram.token = nbauGetCookie( 'nbd_instagram_token' ); 
                if( $scope.resource.instagram.token != '' ){
                    $scope.getInstagramPhoto();
                }else{
                    var popupLeft = (window.screen.width - 700) / 2,
                        popupTop = (window.screen.height - 500) / 2;
                    var url = 'https://api.instagram.com/oauth/authorize?client_id=' + $scope.config.insID + '&scope=user_profile,user_media&redirect_uri=' + $scope.config.insUrl + '&response_type=code';
                    var popup = window.open( url, '_blank', 'width=700,height=500,left=' + popupLeft + ',top=' + popupTop + '' );
                    popup.onload = new function() {
                        if( window.location.hash.length == 0 ) {
                            popup.open(url, '_self');
                        };
                        var interval = setInterval(function () {
                            try {
                                var url = new URL( popup.location.href ),
                                code = url.searchParams.get("code");
                                if ( code ) {
                                    clearInterval( interval );
                                    $scope.resource.instagram.code = code;
                                    popup.close();
                                    $scope.getInstagramAccessToken();
                                }
                            } catch (evt) {}
                        }, 100);
                    }
                }
            };
            $scope.getInstagramPhoto = function(){
                $scope.resource.instagram.logged = true;
                var endpointUrl = 'https://graph.instagram.com/me/media/?fields=id,media_type,media_url' + '&access_token=' + $scope.resource.instagram.token;
                $http({method: 'GET', url: endpointUrl}).then(function successCallback(response) {
                    var data = response.data;
                    if( angular.isDefined( data.data ) && data.data.length > 0 ){
                        $scope.resource.instagram.images = [];
                        angular.forEach(data.data, function(file, index){
                            if( file.media_type == 'IMAGE' ){
                                $scope.resource.instagram.images.push({
                                    extenal: 1,
                                    preview : file.media_url,
                                    url :  file.media_url
                                });
                            }
                        }); 
                        $scope.updateApp();
                    }
                }, function errorCallback(response) {
                    console.log('loadInstagramPhotos');
                });
            };
            $scope.serviceImageLoading = false;
            $scope.$on('nbu:load:more', function(event, type){
                $scope.onScrollLoadMore( type );
            });
            $scope.onScrollLoadMore = function( type ){
                if( $scope.serviceImageLoading ) return;
                switch( type ){
                    case 'facebook':
                        if( $scope.resource.facebook.next ){
                            $scope.serviceImageLoading = true;
                            $scope.getFacebookPhoto();
                        }
                        break;
                    case 'instagram':
                        if( !$scope.resource.instagram.logged ) return;
                        if( ( $scope.resource.instagram.currentPage * 20 ) < $scope.resource.instagram.images.length ){
                            $scope.resource.instagram.currentPage++;
                        }
                        $scope.updateApp();
                        break;
                }
            };
            $scope.updateApp = function(){
                if ($scope.$root.$$phase !== "$apply" && $scope.$root.$$phase !== "$digest") $scope.$apply(); 
            };
            $scope.initGoogleDrive = function(){
                function onApiLoad() {
                    if( $scope.ggOauthToken ){
                        createPicker();
                    }else{
                        gapi.load('auth', {'callback': onAuthApiLoad});
                        gapi.load('picker', {'callback': onPickerApiLoad});                                               
                    }
                }
                function onAuthApiLoad() {
                    window.gapi.auth.authorize({
                            'client_id': $scope.config.ggID,
                            'scope': ['https://www.googleapis.com/auth/drive.readonly'],
                            'immediate': false
                        },
                        handleAuthResult
                    );
                }
                function onPickerApiLoad() {
                    $scope.ggPickerApiLoaded = true;
                    createPicker();
                }
                function handleAuthResult(authResult) {
                    if (authResult && !authResult.error) {
                       $scope.ggOauthToken = authResult.access_token;
                       createPicker();
                    }
                }
                function createPicker() {
                    if ($scope.ggPickerApiLoaded && $scope.ggOauthToken) {
                    var picker = new google.picker.PickerBuilder().
                        addViewGroup(
                            new google.picker.ViewGroup(google.picker.ViewId.DOCS_IMAGES).
                            addView(google.picker.ViewId.DOCS_IMAGES)).
                        setLocale( $scope.config.locale ).    
                        setOAuthToken( $scope.ggOauthToken ).
                        setDeveloperKey( $scope.config.ggKey ).
                        setCallback(pickerCallback).
                        build();
                        picker.setVisible(true);
                    }
                }
                function pickerCallback(data) {
                    var url = '';
                    if (data[google.picker.Response.ACTION] == google.picker.Action.PICKED) {
                        var doc = data[google.picker.Response.DOCUMENTS][0];
                        $scope.resource.imageFromUrl = doc[google.picker.Document.URL];
                        var gapi = { 'fileId': doc.id, 'oAuthToken': $scope.ggOauthToken, 'name': doc.name };
                        $scope.closePopup();
                        $scope.uploadImageFromUrl( 'google', gapi );
                        $scope.updateApp();
                    }
                }
                onApiLoad();
            };
            $scope.uploadImageFromUrl = function( type, gapi, callback ){
                var first_time = $scope.uploadedImages.length > 0 ? 2 : 1;
                if( $scope.config.uiMode == 1 ){
                    var product_id = jQuery('[name="nbd-add-to-cart"]').attr('value'),
                    variation_id = jQuery('[name="variation_id"]').length > 0 ? jQuery('[name="variation_id"]').attr('value') : 0,
                    ajax_url = nbds_frontend.url,
                    nonce = nbds_frontend.nonce,
                    task = 'new',
                    cart_item_key = '',
                    nbu_item_key = '';
                } else {
                    var product_id = nbauObject.product_id,
                    variation_id = nbauObject.variation_id,
                    ajax_url = nbauObject.ajax_url,
                    nonce = nbauObject.nonce,
                    task = nbauObject.task,
                    cart_item_key = nbauObject.cart_item_key,
                    nbu_item_key = nbauObject.nbu_item_key;
                }
                var fd = new FormData();
                fd.append('nonce', nonce);
                fd.append('action', 'nbdesigner_copy_image_from_url');
                fd.append('url', $scope.resource.imageFromUrl);
                fd.append('nbu_adu', 1);
                fd.append('first_time', first_time);
                fd.append('task', task);
                fd.append('product_id', product_id);
                fd.append('variation_id', variation_id);
                if( nbu_item_key != '' ) fd.append('nbu_item_key', nbu_item_key);
                if( cart_item_key != '' ) fd.append('cart_item_key', cart_item_key);
                var indexAddedImage = $scope.uploadedImages.length;
                $scope.showLoadingImage();
                switch( type ){
                    case 'google':
                        for (var prop in gapi) {
                            if ( gapi.hasOwnProperty( prop ) ) {
                                var keyName = ['gapi[', prop, ']'].join('');
                                fd.append(keyName, gapi[prop]);
                            }
                        }
                        break;
                }
                jQuery.ajax({
                    url: ajax_url,
                    method: "POST",
                    processData: false,
                    contentType: false,
                    data: fd
                }).done(function( data ){
                    data = JSON.parse( data );
                    if( angular.isDefined( data.flag ) && data.flag == 1 ){
                        $scope.resource.drive.images.push( {preview: data.src, gapi: gapi} );
                        $scope.hideLoadingImage( indexAddedImage, data );
                    }else{
                        $scope.uploadedImages.splice(indexAddedImage, 1);
                    }
                    $scope.updateApp();
                    if( typeof callback == 'function' ) callback();
                });
            };
            $scope.initDropboxChooser = function(){
                var options = {
                    success: function(files) {
                        $scope.getDropboxImage(files);
                        $scope.updateApp();
                    },                 
                    linkType: "direct",
                    multiselect: true,
                    extensions: ['.jpg', '.jpeg', '.png']
                };
                var button = Dropbox.createChooseButton(options);
                document.getElementById("nbu_upload_dropbox").appendChild(button);
            };
            $scope.getDropboxImage = function( files ){
                angular.forEach(files, function( file ){
                    $scope.resource.dropbox.images.push({
                        extenal: 1,
                        preview :  file.thumbnailLink,
                        url :  file.link
                    });
                });
                $scope.updateApp();
            };
            $scope.showLoadingImage = function(){
                $scope.imageUploading = true;
                $scope.uploadedImages.push({
                    preview: $scope.config.loading_img,
                    styleClass: 'nbu-loading'
                });
            };
            $scope.imageUploading = false;
            $scope.hideLoadingImage = function( index, data ){
                $scope.processImage( data, function( newData ){
                    angular.merge($scope.uploadedImages[index], data);
                    $scope.uploadedImages[index].styleClass = $scope.config.optionClass;
                    angular.merge($scope.uploadedImages[index], newData);
                    $scope.getLoadingStatus();
                    $scope.updateNboOptions();
                    $scope.updateApp();
                });
            };
            $scope.getLoadingStatus = function(){
                var checkLoading = false;
                angular.forEach( $scope.uploadedImages, function(img, index){
                    if( img.styleClass == 'nbu-loading' ) checkLoading = true;
                });
                if( !checkLoading ) $scope.imageUploading = false;
                $scope.updateApp();
            };
            $scope.showProcessing = function(){
                jQuery('#nbu-processing').addClass('active');
            };
            $scope.hideProcessing = function(){
                jQuery('#nbu-processing').removeClass('active');
            };
            $scope.triggerUpload = function(){
                jQuery('input[type="file"]').click();
            };
            $scope.uploadFile = function( files ){
                function resetUploadInput(){
                    jQuery('input[type="file"]').val('');
                }
                var file = files[0],
                type = file.type.toLowerCase();
                if( $scope.uploadedImages.length > ( $scope.config.max - 1 ) ) {
                    alert( $scope.config.msg_exceed );
                    return;
                }
                if( type == '' ){
                    type = file.name.substring(file.name.lastIndexOf('.')+1).toLowerCase();
                }
                type = type == 'image/jpeg' ? 'image/jpg' : type;
                if( $scope.config.disallowType != '' ){
                    var nbd_disallow_type_arr = $scope.config.disallowType.toLowerCase().split(',');
                    var check = false;
                    nbd_disallow_type_arr.forEach(function(value){
                        value = value == 'jpeg' ? 'jpg' : value;
                        if( type.indexOf(value) > -1 ){
                            check = true;
                        }
                    });
                    if( check ){
                        resetUploadInput();
                        alert($scope.config.msg_disallow + $scope.config.disallowType);
                        return;
                    }
                }
                if( $scope.config.allowType != '' ){
                    var nbd_allow_type_arr = $scope.config.allowType.toLowerCase().split(',');
                    var check = false;
                    nbd_allow_type_arr.forEach(function(value){
                        value = value == 'jpeg' ? 'jpg' : value;
                        if( type.indexOf(value) > -1 ){
                            check = true;
                        }
                    });   
                    if( !check ){
                        resetUploadInput();
                        alert($scope.config.msg_support + $scope.config.allowType);
                        return;
                    }
                }
                if (file.size > $scope.config.maxsize * 1024 * 1024 ) {
                    alert($scope.config.msg_maxsize + $scope.config.maxsize + " MB");
                    resetUploadInput();
                    return;
                }else if(file.size < $scope.config.minsize * 1024 * 1024){
                    alert($scope.config.msg_minsize + $scope.config.minsize + " MB");
                    resetUploadInput();
                    return;
                };
                var formData = new FormData;
                formData.append('file', file);
                var first_time = $scope.uploadedImages.length > 0 ? 2 : 1;
                if( $scope.config.uiMode == 1 ){
                    var product_id = jQuery('[name="nbd-add-to-cart"]').attr('value'),
                    variation_id = jQuery('[name="variation_id"]').length > 0 ? jQuery('[name="variation_id"]').attr('value') : 0,
                    ajax_url = nbds_frontend.url,
                    nonce = nbds_frontend.nonce,
                    task = 'new',
                    cart_item_key = '',
                    nbu_item_key = '';
                } else {
                    var product_id = nbauObject.product_id,
                    variation_id = nbauObject.variation_id,
                    ajax_url = nbauObject.ajax_url,
                    nonce = nbauObject.nonce,
                    task = nbauObject.task,
                    cart_item_key = nbauObject.cart_item_key,
                    nbu_item_key = nbauObject.nbu_item_key;
                }
                formData.append('first_time', first_time);
                formData.append('action', 'nbd_upload_design_file');
                formData.append('task', task);
                formData.append('product_id', product_id);
                formData.append('variation_id', variation_id);
                formData.append('nonce', nonce);
                formData.append('nbu_adu', 1);
                if( nbu_item_key != '' ) formData.append('nbu_item_key', nbu_item_key);
                if( cart_item_key != '' ) formData.append('cart_item_key', cart_item_key);
                var indexAddedImage = $scope.uploadedImages.length;
                $scope.showLoadingImage();
                $scope.updateApp();
                jQuery.ajax({
                    url: ajax_url,
                    method: "POST",
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    complete: function() {
                        //todo
                    },
                    success: function(data) {
                        if( data.flag == 1 ){
                            var width = 1 * data.width, height = 1 * data.height;
                            if( ( $scope.config.minWidth != '' && $scope.config.minWidth > width ) || ( $scope.config.minHeight != '' && $scope.config.minHeight > height ) ){
                                $scope.uploadedImages[indexAddedImage].tempData = {};
                                angular.copy( data, $scope.uploadedImages[indexAddedImage].tempData );
                                //if( $scope.imageUploading ){
                                    //todo
                                //}else{
                                    $scope.currentFilePreview = data.src;
                                    $scope.currentFileIndex = indexAddedImage;
                                    $scope.config.warringContext = 'normal';
                                    $scope._showPopup( 'warning' );
                                //}
                            } else {
                                $scope.hideLoadingImage( indexAddedImage, data );
                            }
                        }else{
                            $scope.uploadedImages.splice(indexAddedImage, 1);
                            alert(data.mes);
                        }
                        $scope.updateApp();
                        resetUploadInput();
                    }
                });
            };
            $scope.getAdjustedImage = function( index ){
                var currentImage = $scope.uploadedImages[ index ];
                var formData = new FormData;
                if( $scope.config.uiMode == 1 ){
                    var ajax_url = nbds_frontend.url,
                        nonce = nbds_frontend.nonce;
                } else {
                    var ajax_url = nbauObject.ajax_url,
                        nonce = nbauObject.nonce;
                }
                formData.append('action', 'nbu_crop_image');
                formData.append('nonce', nonce);
                formData.append('url', currentImage.origin);
                formData.append('startX', currentImage.cropLeft);
                formData.append('startY', currentImage.cropTop);
                formData.append('width', currentImage.cropWidth);
                formData.append('height', currentImage.cropHeight);
                formData.append('previewRatio', currentImage.previewRatio);
                currentImage.cropping = true;
                jQuery.ajax({
                    url: ajax_url,
                    method: "POST",
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    complete: function() {
                        //todo
                    },
                    success: function(data) {
                        if( angular.isDefined( data.flag ) && data.flag == '1' ){
                            currentImage.cropped = true;
                            currentImage.cropped_url = data.url;
                            currentImage.cropped_preview_url = data.preview_url;
                        }
                        currentImage.cropping = false;
                    }
                });
            };
            $scope.getAdjustedImages = function( callback ){
                var formData = new FormData;
                if( $scope.config.uiMode == 1 ){
                    var ajax_url = nbds_frontend.url,
                        nonce = nbds_frontend.nonce;
                } else {
                    var ajax_url = nbauObject.ajax_url,
                        nonce = nbauObject.nonce;
                }
                formData.append('action', 'nbu_crop_images');
                formData.append('nonce', nonce);
                var index = 0;
                angular.forEach( $scope.uploadedImages, function( image, key ){
                    if( !image.cropped ){
                        formData.append('files[' + index + '][key]', key);
                        formData.append('files[' + index + '][url]', image.origin);
                        formData.append('files[' + index + '][startX]', image.cropLeft);
                        formData.append('files[' + index + '][startY]', image.cropTop);
                        formData.append('files[' + index + '][width]', image.cropWidth);
                        formData.append('files[' + index + '][height]', image.cropHeight);
                        formData.append('files[' + index + '][previewRatio]', image.previewRatio);
                        index++;
                    }
                });
                if( index > 0 ){
                    jQuery.ajax({
                        url: ajax_url,
                        method: "POST",
                        dataType: 'json',
                        cache: false,
                        contentType: false,
                        processData: false,
                        data: formData,
                        success: function(data) {
                            if( angular.isDefined( data.flag ) && data.flag == '1' ){
                                if( typeof callback == 'function' ) callback( data );
                            }
                            if( $scope.config.uiMode == 1 ) $scope.hideProcessing();
                        }
                    });
                } else {
                    if( $scope.config.uiMode == 1 ) $scope.hideProcessing();
                    if( typeof callback == 'function' ) callback( {files: []} );
                }
            };
            $scope.processImage = function( data, callback ){
                var img = new Image(),
                canvas = document.createElement('canvas'),
                ctx = canvas.getContext('2d');
                canvas.width = $scope.config.productPreviewWidth;
                canvas.height = $scope.config.productPreviewHeight;
                img.onload = function(){
                    var newData = {},
                    originalWidth = img.naturalWidth || img.width,
                    originalHeight = img.naturalHeight || img.height,
                    res = $scope.fitInRec( $scope.config.productPreviewWidth, $scope.config.productPreviewHeight, originalWidth, originalHeight );
                    ctx.drawImage( img, res.left, res.top, res.width, res.height, 0, 0, $scope.config.productPreviewWidth, $scope.config.productPreviewHeight );
                    var uri = canvas.toDataURL(),
                    blob = $scope.makeblob( uri );
                    newData.preview = window.URL.createObjectURL( blob );
                    newData.previewOriginWidth = originalWidth;
                    newData.previewOriginHeight = originalHeight;
                    newData.previewWidth = originalWidth * res.ratio;
                    newData.previewHeight = originalHeight * res.ratio;
                    newData.previewLeft = 0 -( res.left * res.ratio );
                    newData.previewTop = 0 -( res.top * res.ratio );
                    newData.previewCropWidth = res.width;
                    newData.previewCropHeight = res.height;
                    newData.previewCropLeft = res.left;
                    newData.previewCropTop = res.top;
                    newData.zoom = 1;
                    newData.ratio = res.ratio;
                    newData.previewRatio = data.width / newData.previewOriginWidth;
                    newData.cropLeft = res.left * newData.previewRatio;
                    newData.cropTop = res.top * newData.previewRatio;
                    newData.cropWidth = res.width * newData.previewRatio;
                    newData.cropHeight = res.height * newData.previewRatio;
                    callback( newData );
                    /* Edge, Mobile does not support canvas.toBlob
                    canvas.toBlob(function(blob) {
                        new_src = window.URL.createObjectURL( blob );
                        callback( new_src );
                    });
                    */
                };
                img.src = data.src;
            };
            $scope.revokeObjectURL = function( url ){
                setTimeout(function() {
                    return window.URL.revokeObjectURL( url );
                });
            };
            $scope.fitInRec = function( dst_width, dst_height, src_width, src_height ){
                var res = {
                    top: 0,
                    left: 0,
                    width: src_width,
                    height: src_height,
                    ratio: 1
                };
                if( dst_width / dst_height > src_width / src_height ){
                    res.ratio = dst_width / src_width;
                    var new_height = src_height * res.ratio;
                    res.top = ( new_height - dst_height ) / 2 / res.ratio;
                    res.height = dst_height / res.ratio;
                } else {
                    res.ratio = dst_height / src_height;
                    var new_width = src_width * dst_height / src_height;
                    res.left = ( new_width - dst_width ) / 2 / res.ratio;
                    res.width = dst_width / res.ratio;
                }
                return res;
            };
            $scope.makeblob = function (dataURL) {
                var BASE64_MARKER = ';base64,';
                if (dataURL.indexOf(BASE64_MARKER) == -1) {
                    var parts = dataURL.split(',');
                    var contentType = parts[0].split(':')[1];
                    var raw = decodeURIComponent(parts[1]);
                    return new Blob([raw], { type: contentType });
                }
                var parts = dataURL.split(BASE64_MARKER);
                var contentType = parts[0].split(':')[1];
                var raw = window.atob(parts[1]);
                var rawLength = raw.length;
                var uInt8Array = new Uint8Array(rawLength);
                for (var i = 0; i < rawLength; ++i) {
                    uInt8Array[i] = raw.charCodeAt(i);
                }
                return new Blob([uInt8Array], { type: contentType });
            };
            $scope.submitFiles = function(){
                var checkLoading = false;
                angular.forEach( $scope.uploadedImages, function(img, index){
                    if( img.styleClass == 'nbu-loading' ) checkLoading = true;
                });
                if( checkLoading ) return;
                if( $scope.config.min != '' && $scope.uploadedImages.length < $scope.config.min ){
                    alert( $scope.config.msg_min_no_file + ' ' + $scope.config.min + ' ' + $scope.config.msg_files );
                    return;
                }
                $scope.showProcessing();
                var cropping = false;
                angular.forEach( $scope.uploadedImages, function( image, key ){
                    if( image.cropping ) cropping = true;
                });
                if( cropping ){
                    $timeout(function(){
                        $scope.submitFiles();
                    }, 100);
                    return;
                }
                $scope.getAdjustedImages( function( data ){
                    data.files.forEach(function( file ){
                        $scope.uploadedImages[ file.key ].cropped = true;
                        $scope.uploadedImages[ file.key ].cropped_url = file.url;
                        $scope.uploadedImages[ file.key ].cropped_preview_url = file.preview_url;
                    });
                    $scope.updateUploadData();
                    if( $scope.config.uiMode == 1 ) hideDesignFrame();
                });
            };
            $scope.updateUploadData = function( dismiss ){
                var preview = [],
                    nbuData = [],
                    uploadedImages = angular.isUndefined( dismiss ) ? $scope.uploadedImages : [];
                $scope.updateNboOptions();
                uploadedImages.forEach(function( image, key ){
                    if( image.cropped ){
                        preview.push( {src: image.cropped_preview_url, name: image.name} );
                        nbuData.push({
                            name: image.name,
                            startX: image.cropLeft,
                            startY: image.cropTop,
                            width: image.cropWidth,
                            height: image.cropHeight,
                            zoom: image.zoom,
                            productWidth: $scope.config.productWidth,
                            productHeight: $scope.config.productHeight
                        });
                    }
                });
                if( $scope.config.uiMode == 1 ){
                    show_upload_thumb( preview );
                    NBDESIGNERPRODUCT.update_nbu_value( preview );
                    if( jQuery('form.cart, form.variations_form').find('.nbu-advanced-upload-data').length == 0 ){
                        jQuery('form.cart, form.variations_form').append('<input name="nbu_advanced_upload_data" class="nbu-advanced-upload-data" type="hidden" value="" />');
                    }
                    jQuery('form.cart, form.variations_form').find('.nbu-advanced-upload-data').val( JSON.stringify( nbuData ) );
                } else {
                    var files = '';
                    preview.forEach(function(val, key){
                        files += key == 0 ? val.name : '|' + val.name;
                    });
                    if( nbauObject.task == 'reup' || nbauObject.task == 'upload' ){
                        var action = nbauObject.task == 'reup' ? "nbd_update_customer_upload" : "nbu_save_upload_files";
                        jQuery.ajax({
                            url: nbauObject.ajax_url,
                            method: "POST",
                            data: {
                                "action": action,
                                "nonce": nbauObject.nonce,
                                "cart_item_key": nbauObject.cart_item_key,
                                "nbu_item_key": nbauObject.nbu_item_key,
                                "nbau": JSON.stringify( nbuData ),
                                "design_type": nbauObject.design_type,
                                "order_id": nbauObject.order_id,
                                "order_item_id": nbauObject.order_item_id,
                                "nbd_file": files
                            }
                        }).done(function(data){
                            if(data != 'success') {
                                alert(data);
                            }
                            window.location = nbauObject.redirect_url;
                            return;
                        });
                    } else if( nbauObject.task == 'new' ){
                        if( jQuery('form.cart, form.variations_form').find('.nbu-advanced-upload-data').length == 0 ){
                            jQuery('form.cart, form.variations_form').append('<input name="nbu_advanced_upload_data" class="nbu-advanced-upload-data" type="hidden" value="" />');
                        }
                        jQuery('form.cart, form.variations_form').find('.nbu-advanced-upload-data').val( JSON.stringify( nbuData ) );
                        jQuery('form.cart, form.variations_form').append('<input name="submit_form_mode2" type="hidden" value="1" />');
                        jQuery('form.cart, form.variations_form').append('<input name="add-to-cart" type="hidden" value="' + nbauObject.product_id + '" />');
                        if( $scope.config.mustShowNboOptions ){
                            $scope.hideProcessing();
                            $scope._showPopup( 'options' );
                        } else {
                            jQuery('.variations_form, form.cart').submit();
                        }
                    }
                }
            };
            $scope.$on("trigger_nbo_options_changed", function(event, data) {
                $scope.config.mustShowNboOptions = false;
                $scope.updateUploadOption( data );
                angular.forEach( data, function(field, index){
                    if( angular.isUndefined( $scope.config.selectedOptions.number_file ) && angular.isUndefined( $scope.config.selectedOptions.frame ) ){
                        $scope.config.mustShowNboOptions = true;
                    } else {
                        if( angular.isUndefined( $scope.config.selectedOptions.number_file ) ){
                            if( $scope.config.selectedOptions.frame.field_id != index ) $scope.config.mustShowNboOptions = true;
                        } else {
                            if( angular.isUndefined( $scope.config.selectedOptions.frame ) ){
                                if( $scope.config.selectedOptions.number_file.field_id != index ) $scope.config.mustShowNboOptions = true;
                            } else {
                                if( $scope.config.selectedOptions.frame.field_id != index && $scope.config.selectedOptions.number_file.field_id != index ) $scope.config.mustShowNboOptions = true;
                            }
                        }
                    }
                });
                if( $scope.config.uiMode == 1 && jQuery('form.cart, form.variations_form').find('.nbu-advanced-upload-data').length ){
                    var jsonStr = jQuery('form.cart, form.variations_form').find('.nbu-advanced-upload-data').val();
                    datas = JSON.parse( jsonStr );
                    if( $scope.config.productWidth != datas[0].productWidth || $scope.config.productHeight != datas[0].productHeight ){
                        datas.forEach(function(data){
                            data.productWidth = $scope.config.productWidth;
                            data.productHeight = $scope.config.productHeight;
                        });
                        jQuery('form.cart, form.variations_form').find('.nbu-advanced-upload-data').val( JSON.stringify( datas ) );
                    }
                }
            });
            $scope.procesBeforeClosePopup = function(){
                if( $scope.config.min != '' && $scope.uploadedImages.length < $scope.config.min ){
                    $scope._showPopup('action', true);
                    $scope.updateApp();
                    return;
                } else {
                    $scope.submitFiles();
                }
            };
            $scope.dismissUpload = function(){
                $scope.updateUploadData( true );
                $scope.closePopup();
                hideDesignFrame();
            };
            $scope.updateUploadOption = function( data ){
                $scope.config.hasNboOptions = true;
                $scope.config.selectedOptions = {};

                function getOriginField( id ){
                    var field;
                    angular.forEach( nbOption.options.fields, function(f, index){
                        if( f.id == id ) field = f;
                    });
                    return field;
                }

                angular.forEach( data, function(field, index){
                    var originField = getOriginField( index );
                    if( originField.nbe_type == 'number_file' && field.enable ){
                        $scope.config.min = originField.general.input_option.default !== '' ? parseInt( originField.general.input_option.default ) : 1;
                        $scope.config.max = originField.general.input_option.max !== '' ? parseInt( originField.general.input_option.max ) : $scope.config.max;
                        $scope.config.selectedOptions.number_file = {
                            field_id: index,
                            value: $scope.uploadedImages.length
                        }
                    }
                    if( originField.nbe_type == 'frame' && field.enable ){
                        angular.copy( originField.general.attributes.options, $scope.config.uploadOptions );
                        if( $scope.config.uploadOptions.length ){
                            var frameUrl = $scope.config.uploadOptions[field.value].frame_image_url;
                            $scope.config.uploadOptions[field.value].selected = 'on';
                            $scope.config.selectedOptions.frame = {
                                field_id: index,
                                value: '' + field.value,
                                frameUrl: frameUrl
                            }
                        }
                    }
                    if( originField.nbd_type == 'size' && field.enable ){
                        var fieldOptions = originField.general.attributes.options;
                        if( fieldOptions.length ){
                            if( $scope.config.productWidth != fieldOptions[field.value].product_width || $scope.config.productHeight != fieldOptions[field.value].product_height ){
                                $scope.config.productWidth = fieldOptions[field.value].product_width;
                                $scope.config.productHeight = fieldOptions[field.value].product_height;
                                $scope.initSizeInPx();
                                $scope.updatePreviewUploadedImages();
                            }
                        }
                    }
                });
                $scope.updateApp();
            };
            $scope.selectUploadOption = function( $index ){
                angular.forEach( $scope.uploadedImages, function( image, key ){
                    var isLoading = image.styleClass.indexOf('nbu-loading') > -1;
                    image.styleClass = $scope.config.optionClass;
                    if( isLoading ) image.styleClass += ' nbu-loading';
                });
                angular.forEach( $scope.config.uploadOptions, function( option, key ){
                    $scope.config.uploadOptions[key].selected = '';
                });
                $scope.config.uploadOptions[$index].selected = 'on';
                $scope.config.selectedOptions.frame.value = '' + $index;
                $scope.config.selectedOptions.frame.frameUrl = $scope.config.uploadOptions[$index].frame_image_url;
                $scope.updateNboOptions();
            };
            $scope.updateNboOptions = function(){
                if( angular.isDefined( $scope.config.selectedOptions.number_file ) ){
                    $scope.config.selectedOptions.number_file.value = $scope.uploadedImages.length;
                }
                jQuery(document).triggerHandler( 'update_nbo_options_from_advenced_upload', { options: $scope.config.selectedOptions, pro: true } );
            };
            $scope.initSizeInPx = function(){
                $scope.config.productWidthInPx = $scope.config.productWidth * $scope.config.ratio;
                $scope.config.productHeightInPx = $scope.config.productHeight * $scope.config.ratio;
                $scope.config.productPreviewHeight = 250;
                $scope.config.productPreviewWidth = 250 / $scope.config.productHeightInPx * $scope.config.productWidthInPx;
                $scope.config.productInFrameWidth = 250 / $scope.config.productHeightInPx * $scope.config.productWidthInPx;
                $scope.updateApp();
            };
            $scope.init = function(){
                ['productWidth', 'productHeight', 'minWidth', 'maxWidth', 'minHeight', 'maxHeight', 'mindpi'].forEach(function(prop){
                    if( $scope.config[prop] != '' ){
                        $scope.config[prop] = $scope.config[prop] * 1;
                    }
                });
                $scope.config.mindpi = $scope.config.mindpi != 0 ? $scope.config.mindpi : 72;
                switch( $scope.config.unit ){
                    case 'mm':
                        $scope.config.ratio = $scope.config.mindpi / 25.4;
                        break;
                    case 'cm':
                        $scope.config.ratio = $scope.config.mindpi / 2.54;
                        break;
                    case 'ft':
                        $scope.config.ratio = $scope.config.mindpi * 12;
                        break;
                    case 'px':
                        $scope.config.ratio = 1;
                        break;
                    default:
                        $scope.config.ratio = $scope.config.mindpi;
                        break;
                };
                $scope.initSizeInPx();
                $scope.resetPopup();
                if( $scope.config.enbleFb == 'yes' && $scope.config.fbID != '' ) $scope.initFacebook();
                if( $scope.config.enbleDB == 'yes' && $scope.config.dbID != '' ) $scope.initDropboxChooser();

                function initUploadedImages( datas ){
                    datas.forEach(function(data){
                        data.cropped = true;
                        res = $scope.fitInRec( $scope.config.productPreviewWidth, $scope.config.productPreviewHeight, data.previewOriginWidth, data.previewOriginHeight );
                        data.previewWidth = data.previewOriginWidth * res.ratio;
                        data.previewHeight = data.previewOriginHeight * res.ratio;
                        data.previewCropWidth = res.width;
                        data.previewCropHeight = res.height;
                        data.ratio = res.ratio;
                        data.previewCropLeft = data.cropLeft / data.previewRatio;
                        data.previewCropTop = data.cropTop / data.previewRatio;
                        data.previewLeft = 0 - data.previewCropLeft * data.ratio;
                        data.previewTop = 0 - data.previewCropTop * data.ratio;
                        $scope.uploadedImages.push(data);
                    });
                    if( $scope.config.productWidth != datas[0].productWidth || $scope.config.productHeight != datas[0].productHeight ){
                        $scope.config.productWidth = datas[0].productWidth;
                        $scope.config.productHeight = datas[0].productHeight;
                        $scope.initSizeInPx();
                        $scope.updatePreviewUploadedImages();
                    }
                }

                if( typeof nbauObject !== 'undefined' ){
                    if( nbauObject.upload_datas.length > 0 ){
                        initUploadedImages( nbauObject.upload_datas );
                        if( nbauObject.order_id != '' ){
                            $scope.config.max = nbauObject.upload_datas.length;
                            $scope.config.min = nbauObject.upload_datas.length;
                        }
                        $scope.config.selectedOptions.frame = {
                            frameUrl: nbauObject.frame_image_url
                        }
                    }
                    if( angular.isDefined( nbauObject.product_type ) && nbauObject.product_type == 'variable' ){
                        $scope.config.mustShowNboOptions = true;
                    }
                }

                if( typeof cartItemNbau != 'undefined' ){
                    if( cartItemNbau.length > 0 ){
                        initUploadedImages( cartItemNbau );
                    }
                }
            };
            $scope.init();
        }]);
        nbuApp.directive("onLoadMore", ['$timeout', function($timeout) {
            return {
                restrict: "A",
                scope: {
                    type: '@'
                },
                link: function(scope, element) {
                    $timeout(function() {
                        jQuery( element ).on('scroll', function(){
                            var that = jQuery( this );
                            if( ( that.scrollTop() + that.innerHeight() ) >= ( that[0].scrollHeight - 60 )) {
                                scope.$emit('nbu:load:more', scope.type );
                            }
                        });
                    });
                }
            };
        }]);
        nbuApp.directive("nbuAdjustFrame", ['$timeout', function($timeout) {
            return {
                restrict: "A",
                scope: {
                    type: '@'
                },
                link: function(scope, element) {
                    $timeout(function() {
                        function _mousemove( e ){
                            e.stopPropagation();
                            e.preventDefault();
                            scope.$emit('nbu:adjust:mousemove', e );
                        }
                        function _mouseup( e ){
                            e.stopPropagation();
                            e.preventDefault();
                            scope.$emit('nbu:adjust:mouseup', e );
                            jQuery( document ).off('mousemove', _mousemove);
                            jQuery( document ).off('mouseup', _mouseup);
                        }
                        jQuery( element ).on('mousedown', function( e ){
                            e.stopPropagation();
                            e.preventDefault();
                            scope.$emit('nbu:adjust:mousedown', e );
                            jQuery( document ).on('mousemove', _mousemove);
                            jQuery( document ).on('mouseup', _mouseup);
                        });
                        jQuery( element ).on('wheel', function( e ){
                            e.preventDefault();
                            scope.$emit('nbu:adjust:wheel', e.originalEvent );
                        });
                        function _touchmove( e ){
                            e.stopPropagation();
                            e.preventDefault();
                            var touches = e.originalEvent.touches;
                            if( touches.length === 1 ){
                                scope.$emit('nbu:adjust:mousemove', touches[0] );
                            } else if( touches.length === 2 ){
                                scope.$emit('nbu:adjust:pinchmove', touches );
                            }
                        }
                        function _touchend( e ){
                            e.stopPropagation();
                            e.preventDefault();
                            var touches = e.originalEvent.touches;
                            if( touches.length === 1 ){
                                scope.$emit('nbu:adjust:mouseup', touches[0] );
                            }
                            jQuery( element ).off('touchmove', _touchmove);
                            jQuery( element ).off('touchmove', _touchend);
                        }
                        jQuery( element ).on('touchstart', function( e ){
                            e.stopPropagation();
                            e.preventDefault();
                            var touches = e.originalEvent.touches;
                            if( touches.length === 1 ){
                                scope.$emit('nbu:adjust:mousedown', touches[0] );
                            } else if( touches.length === 2 ){
                                scope.$emit('nbu:adjust:pinchstart', touches );
                            }
                            jQuery( element ).on('touchmove', _touchmove);
                            jQuery( element ).on('touchend', _touchend);
                        });
                    });
                }
            };
        }]);
        nbuApp.directive("nbuDndFile", ['$timeout', function($timeout) {
            return {
                restrict: "A",
                scope: {
                    uploadFile: '&nbuDndFile'
                },
                link: function(scope, element) {
                    $timeout(function() {
                        var dropArea = jQuery(element).parent('#nbu-advanced-upload-ctrl'),
                        Input = dropArea.find('input[type="file"]');
                        ['dragenter', 'dragover'].forEach(function(eventName, key) {
                            dropArea.on(eventName, highlight)
                        });
                        ['dragleave', 'drop'].forEach(function(eventName, key) {
                            dropArea.find('#nbu-drop-upload-zone').on(eventName, unhighlight)
                        });
                        function highlight(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            dropArea.find('#nbu-drop-upload-zone').addClass('nbu-highlight');
                        };
                        function unhighlight(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            dropArea.find('#nbu-drop-upload-zone').removeClass('nbu-highlight');
                        };
                        dropArea.find('#nbu-drop-upload-zone').on('drop', handleDrop);
                        function handleDrop(e) {
                            if(e.originalEvent.dataTransfer){
                                if(e.originalEvent.dataTransfer.files.length) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    handleFiles(e.originalEvent.dataTransfer.files);
                                }
                            }
                        };
                        Input.on('change', function(){
                            handleFiles(this.files);
                        });
                        function handleFiles(files) {
                            if(files.length > 0) scope.uploadFile({files: files});
                        }
                    });
                }
            }
        }]);
        var nbuAppEl = document.getElementById('nbu-advanced-upload');
        var nbauLoaded = false;
        angular.element(function() {
            angular.bootstrap(nbuAppEl, ['nbuApp']);
            nbauLoaded = true;
        });
        jQuery(document).on( 'close_advanced_upload_popup', function(){
            var scope = angular.element(document.getElementById("nbu-advanced-upload-ctrl")).scope();
            scope.procesBeforeClosePopup();
        });
        jQuery(document).on( 'trigger_nbo_options_changed', function(e, data){
            function _updateUploadOption(){
                var scope = angular.element(document.getElementById("nbu-advanced-upload-ctrl")).scope();
                if( !data.pro ) scope.$emit( "trigger_nbo_options_changed", data.fields );
            }
            if( nbauLoaded ){
                _updateUploadOption();
            }else{
                var _inteval = setInterval(() => {
                    if( nbauLoaded ){
                        _updateUploadOption();
                        clearInterval( _inteval );
                    }
                }, 100);
            }
        });
    });
    var nbuOnFBLogin = function(){
        FB.getLoginStatus(function(response) {
            if (response.status === "connected") {
                var uid = response.authResponse.userID;
                var accessToken = response.authResponse.accessToken;
                var scope = angular.element(document.getElementById("nbu-advanced-upload-ctrl")).scope();
                scope.resource.facebook.logged = true;
                scope.getFacebookPhoto(uid, accessToken);
            }
        });
    };
</script>