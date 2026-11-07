<?php if (!defined('ABSPATH')) exit; // Exit if accessed directly  ?>
<script type="text/javascript">
    showNBDLicensePopup = function($event){
       $event.stopPropagation();
       jQuery('.nbd-license-popup').addClass('active');
    };
    hideNBDLicensePopup = function(event, force){
        if(event.target.id == 'nbd-license-popup' || force ){
            jQuery('.nbd-license-popup').removeClass('active');
        }
    }
</script>
<span class="nbd-notice-license-footer" onclick="showNBDLicensePopup(event)" title="<?php esc_attr_e('Get NBDesigner Premium', 'web-to-print-online-designer'); ?>">
    <svg class="nbd-pro" fill="#F3B600" xmlns="http://www.w3.org/2000/svg" viewBox="-505 380 12 10"><path d="M-503 388h8v1h-8zM-494 382.2c-.4 0-.8.3-.8.8 0 .1 0 .2.1.3l-2.3.7-1.5-2.2c.3-.2.5-.5.5-.8 0-.6-.4-1-1-1s-1 .4-1 1c0 .3.2.6.5.8l-1.5 2.2-2.3-.8c0-.1.1-.2.1-.3 0-.4-.3-.8-.8-.8s-.8.4-.8.8.3.8.8.8h.2l.8 3.3h8l.8-3.3h.2c.4 0 .8-.3.8-.8 0-.4-.4-.7-.8-.7z"></path></svg>
</span>
<div class="nbd-license-popup" id="nbd-license-popup" onclick="hideNBDLicensePopup(event)">
    <div class="nbd-license-popup-inner">
        <a href="https://cmsmart.net/wordpress-plugins/woocommerce-online-product-designer-plugin?utm_source=backend&utm_medium=cpc&utm_campaign=wpol&utm_content=popup" target="_blank">
            <img src="<?php echo NBDESIGNER_ASSETS_URL . 'images/lite_version_popup.png'; ?>" alt="Lite version"/>
        </a>
        <span onclick="hideNBDLicensePopup(event, true)" class="nbd-license-popup-close">&times;</span>
    </div>
</div>
<style type="text/css">
    .nbd-license-popup-inner {
        width: 60%;
        position: relative;
    }
    .nbd-license-popup-inner img {
        max-width: 100%;
    }
    .nbd-license-popup-inner a {
        display: inline-block;
    }
    .nbd-license-popup-inner a.active {
        outline: none;
    }
    .nbd-license-popup-close {
        position: absolute;
        top: -15px;
        right: -15px;
        height: 30px;
        width: 30px;
        background: #03b591;
        color: #fff;
        text-align: center;
        line-height: 24px;
        cursor: pointer;
        font-size: 30px;
        border-radius: 50%;
        -webkit-box-shadow: 0px 20px 40px 0px rgba(0, 0, 0, 0.3);
        -moz-box-shadow: 0px 20px 40px 0px rgba(0, 0, 0, 0.3);
        box-shadow: 0px 20px 40px 0px rgba(0, 0, 0, 0.3);
    }
    .nbd-license-popup {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        z-index: -1;
        -webkit-transition: 0.4s;
        -moz-transition: 0.4s;
        transition: 0.4s;
        transition: 0.4s;
        text-align: center;
        justify-content: center;
        align-items: center;
        display: flex;
    }
    .nbd-license-popup.active{
        z-index: 99999999;
        opacity: 1;
        background: rgba(0,0,0,0.7);
    }
    .nbd-license-notice img {
        width: 100%;
    }
    .nbd-license-notice{
        display: inline-block;
    }
    .nbd-notice-license-footer {
        position: fixed;
        right: 0;
        top: 100px;
        z-index: 9999;
        width: 30px;
        height: 30px;
        display: inline-block;
        background: #03b591;
        border: 2px solid #03b591;
        line-height: 40px;
        text-align: center;
        display: flex;
        cursor: pointer;
        -webkit-box-shadow: 0px 20px 40px 0px rgba(0, 0, 0, 0.3);
        -moz-box-shadow: 0px 20px 40px 0px rgba(0, 0, 0, 0.3);
        box-shadow: 0px 20px 40px 0px rgba(0, 0, 0, 0.3);
        border-top-left-radius: 4px;
        border-bottom-left-radius: 4px;
    }
    .rtl .nbd-notice-license-footer {
        left: 0;
        right: unset;
    }
    .rtl .nbd-license-popup-close {
        right: unset;
        left: -15px;
        line-height: 30px;
    }
    .nbd-notice-license-footer svg{
        width: 20px;
        margin: 0 auto;
    }
</style>