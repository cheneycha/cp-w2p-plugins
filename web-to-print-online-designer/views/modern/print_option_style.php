<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<style>
    div.quick-view {
        overflow: hidden;
        zoom: 1;
    }
    div.quick-view .product:before,
    div.quick-view .product:after {
        content: " ";
        display: table;
    }
    div.quick-view .product:after {
        clear: both;
    }
    div.quick-view div.quick-view-image {
        margin: 0;
        width: 38% !important;
        float: left;
        box-sizing: border-box;
    }
    div.quick-view div.quick-view-image img {
        display: block;
        margin: 0 0 20px;
        border: 1px solid #eee;
        width: 100%;
        height: auto;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.2);
        -webkit-box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.2);
        padding: 8px;
        background: #fff;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;
        border-radius: 4px;                
    }
    div.quick-view div.quick-view-image a.button {
        display: block;
        text-align: center;
        padding: 1em;
        margin: 0;
    }
    div.quick-view div.quick-view-content {
        overflow: auto;
        width: 56%;
        float: right;
        overflow: unset;
    }
    .post-type-archive-product .pp_woocommerce_quick_view .pp_description,
    .tax-product_cat .pp_woocommerce_quick_view .pp_description,
    .post-type-archive-product .pp_woocommerce_quick_view .pp_social,
    .tax-product_cat .pp_woocommerce_quick_view .pp_social,
    .post-type-archive-product .pp_woocommerce_quick_view .pp_close,
    .tax-product_cat .pp_woocommerce_quick_view .pp_close {
        display: none !important;
    }
    .post-type-archive-product .pp_content,
    .tax-product_cat .pp_content {
        overflow: auto;
        height: auto !important;
    }
    .quick-view-button span {
        margin-right: .875em;
        display: inline-block;
        width: 1em;
        height: 1em;
        background: #000;
        position: relative;
        border-radius: 65% 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }
    .quick-view-button span:before,
    .quick-view-button span:after {
        content: "";
        position: absolute;
        display: block;
        top: 50%;
        left: 50%;
        border-radius: 100%;
    }
    .quick-view-button span:before {
        height: .5em;
        width: .5em;
        background: #fff;
        margin-top: -0.25em;
        margin-left: -0.25em;
    }
    .quick-view-button span:after {
        height: .25em;
        width: .25em;
        background: #000;
        margin-top: -0.125em;
        margin-left: -0.125em;
    }
    .quick-view-detail-button {
        font-size: 100%;
        margin: 0;
        line-height: 1em;
        text-align: center;
        cursor: pointer;
        position: relative;
        font-family: inherit;
        text-decoration: none;
        overflow: visible;
        padding: 6px 10px;
        font-weight: bold;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        left: auto;
        text-shadow: 0 1px 0 #ffffff;
        color: #5e5e5e;
        text-shadow: 0 1px 0 rgba(255, 255, 255, 0.8);
        border: 1px solid #c7c0c7;
        background: #f7f6f7;
        background: -webkit-gradient(linear, left top, left bottom, from(#f7f6f7), to(#dfdbdf));
        background: -webkit-linear-gradient(#f7f6f7, #dfdbdf);
        background: -moz-linear-gradient(center top, #f7f6f7 0%, #dfdbdf 100%);
        background: -moz-gradient(center top, #f7f6f7 0%, #dfdbdf 100%);
        white-space: nowrap;
        display: block;
        -webkit-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.075), inset 0 1px 0 rgba(255, 255, 255, 0.3), 0 1px 2px rgba(0, 0, 0, 0.1);
        -moz-box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.075), inset 0 1px 0 rgba(255, 255, 255, 0.3), 0 1px 2px rgba(0, 0, 0, 0.1);
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.075), inset 0 1px 0 rgba(255, 255, 255, 0.3), 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    .quick-view-button span {
        display: none;
    }
    div.quick-view div.quick-view-image a.button {
        border: 0;
        background: none;
        background-color: #404762;
        border-color: #43454b;
        color: #fff;
        cursor: pointer;
        padding: 0.6180469716em 1.41575em;
        text-decoration: none;
        font-weight: 600;
        text-shadow: none;
        display: inline-block;
        outline: none;
        -webkit-appearance: none;
        border-radius: 2px;
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.3);
        line-height: inherit;
        display: block; 
    }    
    div.quick-view .quantity .screen-reader-text {
        margin-right: 15px;
    }
    div.quick-view .input-text.qty {
        padding: 0.418047em;
        background-color: #f2f2f2;
        color: #43454b;
        outline: 0;
        border: 0;
        -webkit-appearance: none;
        box-sizing: border-box;
        font-weight: 400;
        box-shadow: inset 0 1px 1px rgba(0,0,0,.125);
        width: 4.235801032em;
        text-align: center;      
        height: 36px;
    }
    div.quick-view table td, div.quick-view table th{
        padding: 1em 1.41575em;
        text-align: left;              
    }
    div.quick-view table th{
        background-color: #f8f8f8;
    }
    div.quick-view table  td {
        background-color: #fdfdfd;
    }    
    div.quick-view table tr:nth-child(2n) td {
        background-color: #fbfbfb;
    }            
    div.quick-view h1.product_title {
        margin: 0;
        font-size: 2em;                
    }
    div.quick-view table .label {
        color: #404762;
        font-size: 100%;                
    }
    div.quick-view .single_add_to_cart_button, div.quick-view .reset_variations {
        color: #fff;
        background: #404762;
        display: inline-block;
        position: relative;
        min-height: 36px;
        min-width: 88px;
        line-height: 36px;
        vertical-align: middle;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        box-sizing: border-box;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        outline: 0;
        border: 0;
        padding: 0 12px;
        margin: 0px 8px;
        white-space: nowrap;
        text-transform: uppercase;
        font-weight: 500;
        font-size: 14px;
        font-style: inherit;
        font-variant: inherit;
        font-family: inherit;
        text-decoration: none;
        overflow: hidden;
        text-align: center;
        -webkit-transition: box-shadow .4s cubic-bezier(.25,.8,.25,1),background-color .4s cubic-bezier(.25,.8,.25,1);
        -webkit-transition: background-color .4s cubic-bezier(.25,.8,.25,1),-webkit-box-shadow .4s cubic-bezier(.25,.8,.25,1);
        transition: background-color .4s cubic-bezier(.25,.8,.25,1),-webkit-box-shadow .4s cubic-bezier(.25,.8,.25,1);
        transition: box-shadow .4s cubic-bezier(.25,.8,.25,1),background-color .4s cubic-bezier(.25,.8,.25,1);
        transition: box-shadow .4s cubic-bezier(.25,.8,.25,1),background-color .4s cubic-bezier(.25,.8,.25,1),-webkit-box-shadow .4s cubic-bezier(.25,.8,.25,1);                
    }
    div.quick-view .variations select {
        border: 1px solid #EEE;
        height: 36px;
        padding: 3px 36px 3px 8px;
        background-color: transparent;
        line-height: 100%;
        outline: 0;
        background-image: url(<?php echo NBDESIGNER_PLUGIN_URL.'assets/images/arrow.png'; ?>);
        background-position: right;
        background-repeat: no-repeat;
        position: relative;
        cursor: pointer;
        -webkit-appearance: none;
        -moz-appearance: none;
    }
    div.quick-view .nbd-swatch-wrap .nbd-field-content {
        font-size: 14px;
    }
    #nbo-options-wrap {
        -webkit-transition: all .3s;
        -moz-transition: all .3s;        
        transition: all .3s; 
    }
    .nbd-swatch-wrap input[type="radio"]:checked + label:after {
        top: 9px !important;
        left: 13px !important;             
    }
    div.quick-view .variations {
        margin-bottom: 15px;
    }
    div.quick-view .variations td {
        display: table-cell;
        vertical-align: middle;                
    }
    .nbo-summary-title, .nbo-table-pricing-title, .nbo-bulk-title {
        margin-top: 15px;
    }
    .nbd-field-input-wrap input[type="number"] {
        height: 36px;
    }
    .nbd-field-content input[type="range"] {
        border: none;
    }
    .nbo-disable {
        pointer-events: none;
    }
    .popup-nbo-options .nbd-button:hover {
        color: #fff;
        text-decoration: none;
    }
    .nbd-popup.popup-nbo-options .icon-nbd-clear {
        display: none;
    }
    .nbo-apply {
        float: right;
        margin-right: 0;
    }
    .nbd-popup.popup-nbo-options:after {
        content: '';
        clear: both;
    }
    .woocommerce-variation-price {
        margin-bottom: 15px;
    }
    .price del {
        opacity: 0.5;
    }
    ins .woocommerce-Price-amount {
        margin-left: 10px;
    }
    .nbo-summary-table {
        margin-bottom: 10px;
    }
    .nbo-dimension {
        width: 7em !important;
    }
    div.quick-view .quantity {
        display: inline-block;
    }
    div.quick-view .single_variation_wrap{
        padding-bottom: 15px;
    }
    .item-nbo-options span{
        border: 2px solid #ef5350;
        line-height: 32px;
        display: inline-block;
        padding: 0 14px;
        box-sizing: border-box;
        border-radius: 18px;
        color: #ef5350 !important;
    }
    .woocommerce-variation-price ins {
        font-weight: bold;
        text-decoration: none;                
    }
    .nbo-bulk-variation tfoot button {
        border: 0;
        background: none;
        background-color: #404762;
        border-color: #43454b;
        color: #fff;
        cursor: pointer;
        padding: 0.6180469716em 1.41575em;
        text-decoration: none;
        font-weight: 600;
        text-shadow: none;
        display: inline-block;
        outline: none;
        -webkit-appearance: none;
        border-radius: 2px;
        box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.3);
        line-height: inherit;
        display: inline-block;                
    }
    .blockUI::before{
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        display: inline-block;
        font-style: normal;
        font-weight: normal;
        line-height: 1;
        vertical-align: -.125em;
        font-family: online-design!important;
        vertical-align: baseline;
        content: "\e954";
        -webkit-animation: fa-spin 0.75s linear infinite;
        animation: fa-spin 0.75s linear infinite;
        height: 30px;
        width: 30px;
        line-height: 30px;
        font-size: 30px;
        position: absolute;
        top: 50%;
        left: 50%;
        margin-left: -15px;
        margin-top: -15px;
        color: #404762;
    }
    @-webkit-keyframes fa-spin {
        0% {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg); }
        100% {
            -webkit-transform: rotate(360deg);
            transform: rotate(360deg); } 
    }
    @keyframes fa-spin {
        0% {
            -webkit-transform: rotate(0deg);
            transform: rotate(0deg); }
        100% {
            -webkit-transform: rotate(360deg);
            transform: rotate(360deg); }
    }
    .quick-view .sku_wrapper, .quick-view  .posted_in {
        display: block;
    }
    @media only screen and (max-width: 768px) {
        div.quick-view div.quick-view-image,
        div.quick-view div.quick-view-content {
            float: none !important;
            width: 100% !important;
            position: unset;
        }
        div.quick-view h1.product_title {
            margin-top: 15px;
        }
        .popup-nbo-options .footer .nbd-invalid-form {
            line-height: unset !important;
        }
        .popup-nbo-options .footer .nbo-apply.nbd-disabled {
            display: none;
        }
    }            
</style>