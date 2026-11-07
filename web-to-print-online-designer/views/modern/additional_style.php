<?php if ( ! defined( 'ABSPATH' ) ) { exit;} ?>
<style type="text/css">
    .nbd-sidebar .tabs-nav ul.main-tabs .tab svg{
        margin-bottom: 5px;
    }
    .nbd-sidebar .tabs-nav ul.main-tabs #nav-templates.active svg path {
        fill: #404762;
    }
    .nbd-sidebar .tabs-nav ul.main-tabs #nav-templates svg path {
        fill: #fff;
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
    }
    .nbd-toolbar .toolbar-text .nbd-main-menu.menu-left .menu-item .toolbar-input[name="font-size"] {
        font-size: 12px;
        width: 40px;
    }
    .nbd-toolbar .toolbar-text .nbd-main-menu.menu-left .menu-item.item-font-size .toolbar-bottom i.icon-nbd{
        font-size: 12px;
        margin-left: 2px;
    }
    .nbd-remove-stage {
        color: #ef5350 !important;
    }
    .nbd-add-stage {
        border: 2px solid #ddd;
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        height: 30px;
        border-radius: 4px;
        line-height: 25px;
        padding: 0 30px;
        font-size: 12px;
        cursor: pointer;
        background: #fafafa;
    }
    .nbd-add-stage:hover {
        border-color: rgba(14,19,24,.45);
    }
    .nbd-qty {
        border: none;
        border-bottom: 1px dashed #ddd;
        width: 30px;
        text-align: center;
        background: transparent;
        font-size: 12px;
    }
    .nbd-qty-wrap {
        position: absolute;
        top: 3px;
        left: 15px;
        font-size: 12px;
        background: #fff;
        padding: 0px 10px;
        z-index: 2;
        box-shadow: 0 1px 3px 0 rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 2px 1px -1px rgba(0,0,0,.12);
        height: 30px;
        border-radius: 4px;
    }
    .nbd-qty-variation {
        max-width: 75px;
        margin-top: 5px;
        border-radius: 4px;
        font-size: 10px;
        cursor: pointer;
    }
    .nbd-qty-variation:focus {
        outline: none;
    }
    .nb-opacity-0 {
        opacity: 0;
    }
    .mockup-preview-wrap {
        position: relative;
        border-radius: 4px;
        border: 1px solid #ddd;
        box-shadow: 0 1px 3px 0 rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 2px 1px -1px rgba(0,0,0,.12);
        height: 100%;
        flex-direction: column;
        display: flex;
        justify-content: space-between;
    }
    .mockup-preview-wrap .nbd-checkbox {
        position: absolute;
        top: 5px;
        left: 5px;
    }
    .mockup-preview-wrap .mockup-name {
/*        position: absolute;*/
        bottom: 0px;
        left: 0;
        width: 100%;
        line-height: 30px;
    }
    .popup-nbd-mockup-preview .main-popup{
        width: 60%;
        text-align: center;
    }
    .mockup-wrap {
        border: 2px dashed #404762;
        padding: 15px;
        height: 400px;
        position: relative;
        border-radius: 4px;
    }
    .mockup-wrap ul {
        display: flex;
        flex-wrap: wrap;
        list-style: none;
        margin: 0;
    }
    .mockup-wrap .mockup-preview {
        padding: 10px;
        padding: 10px;
        flex: 1 0 25%;
        max-width: 25%;
        cursor: pointer;
    }
    .mockup-wrap .mockup-preview img{
        max-width: 100%;
    }
    .design-wrap.has-border {
        border: 1px dashed #404762;
        box-sizing: content-box;
    }
    .nbd-tooltip_templates {
        display: none;
    }
    .nbd-tooltip-template {
        height: 500px;
        width: 400px;
    }
    .tooltipster-template .tooltipster-box {
        background-color: #fff;
        border: 1px solid #ccc;
    }
    .tooltipster-template.tooltipster-right .tooltipster-arrow-border {
        left: 3px !important;
        top: 2px !important;
        border: 8px solid transparent !important;
        border-right-color: #ccc !important;
    }
    .tooltipster-template.tooltipster-right .tooltipster-arrow-background {
        border-right-color: #fff;
    }
    .tooltipster-template .tooltipster-content {
        padding: 10px 0;
    }
    .nbd-tooltip-template .nbd-img-container {
        margin-bottom: 20px
    }
    .nbd-tooltip-template .nbd-img-container img {
        margin-bottom: 10px;
    }
    .nbd-tooltip-template .nbd-img-container.nbd-img-last{
        margin-bottom: 0;
    }    
    .x-dimension {
        position: absolute;
        top: 10px;
        height: 25px;
        pointer-events: none;
        box-sizing: border-box;
        border-left: 1px dashed #ccc;
        border-right: 1px dashed #ccc;
    }
    .x-dimension:after {
        position: absolute;
        top: 12px;
        left: 0;
        display: block;
        content: '';
        height: 0;
        width: 100%;
        border-bottom: 1px dashed #ccc;
        z-index: 1;
    }
    .x-dimension span{
        height: 25px;
        background: #fff;
        border-radius: 12px;
        padding: 0px 20px;
        line-height: 25px;
        border: solid 1px #b4bdc5;
        box-sizing: border-box;
        display: inline-block;
        z-index: 2;
        position: relative;
        font-size: 12px;
    }
    .y-dimension {
        position: absolute;
        left: 15px;
        top: 40px;
        width: 25px;
        pointer-events: none;
        box-sizing: border-box;
        border-top: 1px dashed #ccc;
        border-bottom: 1px dashed #ccc;
        white-space: nowrap;
    }
    .y-dimension:after {
        position: absolute;
        left: 12px;
        top: 0;
        display: block;
        content: '';
        height: 100%;
        width: 0;
        border-right: 1px dashed #ccc;
        z-index: 1;
    }
    .y-dimension .dimension-number-wrap{
        display: inline-block;
        z-index: 2;
        position: relative;
        top: 50%;
        transform: translate(calc(-50% + 12px), -50%);
    }
    .y-dimension .dimension-number-wrap .dimension-number{
        transform: rotate(90deg);
        height: 25px;
        background: #fff;
        border-radius: 12px;
        padding: 0px 20px;
        line-height: 25px;
        border: solid 1px #b4bdc5;
        box-sizing: border-box;
        display: inline-block;
        font-size: 12px;
    }
    .nbd-user-design {
        box-shadow: 0 1px 3px 0 rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 2px 1px -1px rgba(0,0,0,.12);
        width: calc(25% - 10px);
        cursor: pointer;
        display: inline-block;
        margin-right: 10px;
        margin-bottom: 10px;
        vertical-align: top;
        position: relative;
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
        min-height: 40px;
        overflow: hidden;
    }
    .nbd-user-design .action-button {
        left: 50%;
        position: absolute;
        top: 50%;
        height: 30px;
        line-height: 30px;
        box-shadow: 0 5px 6px -3px rgba(0,0,0,.2), 0 9px 12px 1px rgba(0,0,0,.14), 0 3px 16px 2px rgba(0,0,0,.12);
        cursor: pointer;
        display: inline-block;
        background: #404762;
        border-radius: 4px;
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
        padding: 0 15px;
        color: #fff;
    }
    .nbd-user-design .action-button.left {
        transform: translate(calc(-100% - 200px), -50%);
    }
    .nbd-user-design .action-button.right {
        transform: translate(calc(200px), -50%);
    }
    .nbd-user-design:hover .action-button.left {
        transform: translate(calc(-100% - 10px), -50%);
    }
    .nbd-user-design:hover .action-button.right {
        transform: translate(calc(10px), -50%);
    }
    .nbd-user-design:hover img{
        -webkit-filter: brightness(0.5);
        -moz-filter: brightness(0.5);
        filter: brightness(0.5);
    }
    .popup-nbd-user-design .tab-scroll {
        position: relative;
        height: 100%;
        padding: 5px;
    }
    .popup-nbd-user-design .tab-scroll .ps__scrollbar-x-rail {
        display: none;
    }
    #temp-canvas-wrap {
        position: fixed;
        left: -9999px;
        top: -9999px;
        z-index: -1;
    }
    .nbd-stages-nav {
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
        z-index: 3;
    }
    .nbd-stages-nav.toggle-down {
        bottom: -40px;
    }
    .nbd-stages-nav-wrapper {
        position: relative;
    }
    .nbd-stages-nav-inner {
        background: #fff;
        height: 40px;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        font-size: 0;
        box-shadow: 0 5px 6px -3px rgba(0,0,0,.2), 0 9px 12px 1px rgba(0,0,0,.14), 0 3px 16px 2px rgba(0,0,0,.12);
        z-index: 2;
        position: relative;
    }
    .nbd-stages-nav-inner .nbd-stage-thumb {
        line-height: 40px;
        height: 40px;
        width: 40px;
        display: inline-block;
        text-align: center;
        font-size: 14px;
        color: #404762;
        vertical-align: top;
    }
    .nbd-stages-nav-inner .nbd-stage-thumb.stage-nav{
        padding-top: 5px;
    }
    .nbd-stages-nav-inner .nbd-stage-thumb.stage-nav span{
        width: 30px;
        height: 30px;
        display: inline-block;
        border-radius: 50%;
        background: rgba(221,221,221,0.35);
        cursor: pointer;
    }
    .nbd-stages-nav-inner .nbd-stage-thumb.stage-nav span:hover {
        background: #ddd;
    }
    .nbd-stages-nav-inner .nbd-stage-thumb.stage-name{
        width: unset;
        padding: 0 25px;
    }
    .nbd-stages-nav-inner .nbd-stage-thumb.stage-name.active{
        background: #404762;
        color: #fff;
    }
    .nbd-stages-nav-inner .nbd-stage-thumb i {
        color: #404762;
        font-size: 24px;
        width: 30px;
        height: 30px;
        line-height: 30px;
    }
    .nbd-stages-nav-toggle {
        position: absolute;
        top: 0;
        left: 50%;
        transform: translate(-50%, -100%);
        z-index: 1;
        font-size: 0;
    }
    .nbd-stages-nav-toggle-inner {
        position: relative;
        cursor: pointer;
    }
    .nbd-stages-nav-toggle-inner .toggle-direction {
        position: absolute;
        fill: #888;
        left: 50%;
        transform: translateX(-50%) rotate(0deg);
    }
    .nbd-stages-nav-toggle-inner .toggle-direction.toggle-down {
        transform: translateX(-50%) rotate(180deg);
        top: 3px;
    }
    .nbd-guidelines-notice {
        position: absolute;
        bottom: 15px;
        left: 0;
        font-size: 0;
    }
    .nbd-guideline-bleedline, .nbd-guideline-safezone {
        background: rgb(224,224,224);
        position: relative;
        border-radius: 16px;
        padding: 0px 30px 0px 12px;
        font-size: 12px;
        height: 32px;
        line-height: 32px;
        margin-left: 10px;
        float: left;
    }
    .nbd-guideline-bleedline .nbd-popup-tigger, .nbd-guideline-safezone .nbd-popup-tigger{
        font-size: 12px;
    }
    .nbd-guideline-bleedline .nbd-popup-tigger i, .nbd-guideline-safezone .nbd-popup-tigger i{
        color: #fff;
        vertical-align: middle;
        height: 24px;
        width: 24px;
        line-height: 24px;
        text-align: center;
        border-radius: 50%;
        border: none;
        box-shadow: none;
        padding: 0;
        margin: 0;
        position: absolute;
        right: 4px;
        top: 4px;
        margin-right: 0;
        height: 24px;
    }
    .nbd-guideline-bleedline .nbd-popup-tigger i{
        background: rgba(255, 0, 0, 0.4);
    }
    .nbd-guideline-safezone .nbd-popup-tigger i{
        background: rgba(0, 128, 0, 0.4);
    }
    .bleed-note {
        color: red;
    }
    .bleed-note:after {
        border-bottom: 2px solid red;
        content: '';
        height: 0;
        width: 50px;
        display: block;
    }
    .safezone-note {
        color: green;
    }
    .safezone-note:after {
        border-bottom: 2px solid green;
        content: '';
        height: 0;
        width: 50px;
        display: block;
    }
    .nbd-progress-bar {
        width: calc(100% - 20px);
        height: 8px;
        position: relative;
        margin: 0 10px;
        border: 1px solid #404762;
        border-radius: 4px;
        z-index: 3;
        margin-top: 5px;
        display: none;
    }
    .nbd-progress-bar.active {
        display: block;
    }
    .nbd-progress-bar-inner{
        height: 100%;
        background: #404762;
        border-radius: 4px;
    }
    .nbd-progress-bar .indicator {
        position: absolute;
        top: -19px;
        background: #404762;
        color: #fff;
        font-size: 10px;
        padding: 0 5px;
        border-radius: 2px;
        width: 30px;
        height: 14px;
        line-height: 14px;
    }
    .nbd-progress-bar .indicator:after {
        display: block;
        content: '';
        width: 0;
        height: 0;
        border-top: 5px solid #404762;
        border-left: 4px solid transparent;
        border-right: 4px solid transparent;
        border-bottom: none;
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-4px);
    }
    .nbd-sidebar #tab-photo .result-loaded .content-items div[data-type=image-upload] .form-upload {
        margin-top: 10px;
    }
    .tour-guide {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
/*        pointer-events: none;*/
        z-index: -1;
        opacity: 0;
        visibility: hidden;
    }
    .nbd-tourStep {
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
    }
    .tour-guide.active {
        z-index: 9999999;
        opacity: 1;
        visibility: visible;
    }
    .tooltipster-sidetip.tooltipster-right .tooltipster-arrow-border {
        border-right-color: #404762;
        left: 7px;
        top: 5px;
    }
    .tour_start span{
        width: 18px;
        border: 1px solid #404762;
        display: inline-block;
        height: 18px;
        text-align: center;
        border-radius: 50%;
        line-height: 18px;
    }
    .swatch-control > :not(select){
        display: none;
    }
    .nbd-pro-mark-wrap {
        position: absolute;
        bottom: 5px;
        right: 5px;
        display: inline-block;
        background: #15171b;
        height: 15px;
        border-radius: 3px;
        color: #fff !important;
        text-transform: uppercase;
        line-height: 15px;
        font-size: 10px !important;
        padding: 0 2px;
    }
    .nbd-pro-mark {
        height: 12px;
        width: 13px;
        display: inline-block;
        vertical-align: middle;
    }
    .nbd-hoz-ruler {
        position: absolute;
        top: 0;
        left: 0;
        height: 40px;
        text-align: left;
        cursor: pointer;
    }
    .nbd-button:hover {
        color: #fff;
        text-decoration: none;
    }
    .nbd-ver-ruler {
        position: absolute;
        top: 0;
        left: 0;
        width: 40px;
        text-align: left;
        margin-left: 5px;
        cursor: pointer;
    } 
    .nbd-hoz-ruler svg{
        height: 100%;
        pointer-events: none;
    }
    .nbd-ver-ruler svg {
        width: 100%;
        pointer-events: none;
    }
    .rulez-text {
        font-size: 10px;
        fill: #888;
    }
    .rulez-rect{
        fill:grey
    }
    .ruler-guideline-hor {
        border-bottom: 1px solid #3BB7C7;
        z-index: 99;
        height: 3px;
        background: transparent;
        position: absolute;
        left: 0;
        cursor: ns-resize;
    }
    .ruler-guideline-ver {
        border-right: 1px solid #3BB7C7;
        z-index: 99;
        width: 3px;
        position: absolute;
        top: 0;
        cursor: ew-resize;
    }
    .guide-backdrop {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    .nbd-prevent-event {
        pointer-events: none;
        z-index: 98;
    }
    .nbd-disable-event {
        pointer-events: none;
    }
    .nbd-hide {
        opacity: 0;
    }
    .stage-background, .design-zone, .stage-grid, .bounding-layers, .stage-snapLines, .stage-overlay, .stage-guideline {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    .stage-background, .stage-grid, .bounding-layers, .stage-snapLines, .stage-overlay, .stage-guideline {
        pointer-events: none;
    }
    .nbd-stages .stage {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
    }
    .stage.hidden {
        opacity: 0;
        z-index: 0;
        -webkit-transform: translate3d(0, -100%, 0);
        transform: translate3d(0, -100%, 0);
    }  
    .nbd-stages .stage .page-toolbar .page-main ul li.disabled {
        opacity: .3;
        pointer-events: none;
    }  
    .bounding-layers-inner,
    .stage-snapLines-inner {
        position: relative;
        width: 100%;
        height: 100%;
    }
    .bounding-rect {
        position: absolute;
        display: inline-block;
        visibility: hidden;
        top: -20px;
        left: -20px;
        width: 10px;
        height: 10px;
        border: 1px dashed #ddd;
        transform-origin: 0% 0%;
    }
    .nbd-sidebar #tab-typography .tab-main .typography-body .typography-item {
        cursor: pointer;
    }
    .text-heading {
        font-size: 40px;
        font-weight: 700;        
    }
    .text-sub-heading {
        font-size: 24px;
        font-weight: 500;        
    } 
    .text-heading, .text-sub-heading, .text-body {
        color: #4F5467;
        cursor: pointer;
        display: block;
    }

    .nbd-input {
        border: none;
    }
    .nbd-sidebar #tab-photo .result-loaded .content-items div[data-type=image-upload] .form-upload i {
        vertical-align: middle;
        margin-right: 5px;     
        color: #404762;
    }
    .nbd-sidebar #tab-photo .result-loaded .content-items div[data-type=image-upload] .form-upload   {
        border: 2px dashed #fff;
        padding: 30px 10px;        
    } 
    .nbd-sidebar #tab-photo .result-loaded .content-items div[data-type=image-upload] .form-upload i:before,
    .nbd-sidebar #tab-photo .result-loaded .content-items div[data-type=image-upload] .form-upload{
        color: #404762;
        font-weight: bold;
    }
    .layer-coordinates {
        position: absolute;
        display: inline-block;
        font-size: 9px;
        font-family: monospace;
        color: #404762;   
        visibility: hidden;
        transform: translate(calc(-100% - 10px), calc(-100% + 5px));
        text-shadow: 1px 1px #fff;
    }
    .layer-angle {
        position: absolute;
        display: inline-block;
        font-family: monospace;
        color: #404762;   
        visibility: hidden;
        text-shadow: 1px 1px #fff;        
    }
    .layer-angle span {
        font-size: 9px !important;
        display: inline-block;
    }
    .snapline {
        position: absolute;
    }
    .h-snapline {
        top: -9999px;
        left: -20px;
        width: calc(100% + 40px);
        height: 3px !important;
        background-image: linear-gradient(to right,rgba(214,96,96,.95) 1px,transparent 1px);
        background-size: 2px 1px;
        background-repeat: repeat-x;     
    }
    .v-snapline {
        left: -9999px;
        top: -20px;
        height: calc(100% + 40px);
        width: 3px!important;
        background-image: linear-gradient(to bottom,rgba(214,96,96,.95) 1px,transparent 1px);
        background-size: 1px 2px;
        background-repeat: repeat-y;
    }   
    .nbd-main-menu button.menu-item.disabled, .nbd-main-menu li.menu-item.disabled {
        pointer-events: none;
        opacity: 0.3;
    }
    .nbd-disabled {
        pointer-events: none;
        opacity: 0.3;
    }
    .color-palette-add {
        position: relative;
    }
    .color-palette-add:after {
        position: absolute;
        top: 0;
        left:0;
        width: 40px;
        height: 40px;  
        display: inline-block;
        line-height: 40px;
        content: "\e908";
        text-align: center;
        color: #404762;
        font-family: online-design!important;
        font-size: 20px;
        text-shadow: 1px 1px 1px #fff;
    }
    .nbd-text-color-picker {
        position: absolute; 
        left: 40px; 
        top: 50px;
        -webkit-transform: scale(.8);
        -ms-transform: scale(.8);
        transform: scale(.8); 
        visibility: hidden;
        opacity: 0;
        -webkit-transition: all .3s;
        -moz-transition: all .3s;        
        transition: all .3s; 
        -webkit-box-shadow: 1px 0 15px rgba(0,0,0,.2);    
        box-shadow: 1px 0 15px rgba(0,0,0,.2);    
        background-color: #fff;
        overflow: hidden;
    }
    .nbd-text-color-picker.active {
        opacity: 1;
        visibility: visible;
        -webkit-transform: scale(1);
        -ms-transform: scale(1);
        transform: scale(1);        
    }
    .nbd-color-palette {
        opacity: 0;
        display: block !important;
        visibility: hidden;
        -webkit-transform: scale(0.8);
        -ms-transform: scale(0.8);
        transform: scale(0.8);  
        -webkit-transition: all .4s;
        -moz-transition: all .4s;        
        transition: all .4s;         
    }
    .nbd-color-palette-inner .nbd-perfect-scroll{
        max-height: 185px;
    }
    .nbd-color-palette.show {
        opacity: 1;
        visibility: visible;
        -webkit-transform: scale(1);
        -ms-transform: scale(1);
        transform: scale(1);           
    }    
    .nbd-sp.sp-container {
        box-shadow: none;
    }
    .nbd-text-color-picker .nbd-button {
        margin-top: 0;
        margin-left: 11px;
        margin-bottom: 10px;        
    }
    .nbd-workspace .main {
        overflow: hidden;
    }
    .tab-main .loading-photo {
        position: absolute;
        z-index: 99;
        left: 50%;
        -webkit-transform: translateX(-50%);
        -ms-transform: translateX(-50%);
        transform: translateX(-50%);
    }    
    .nbd-sidebar #tab-typography .tab-main .typography-body .typography-item {
        opacity: 0;
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        -ms-transition: all 0.4s;
        transition: all 0.4s;
    }
    .nbd-sidebar #tab-typography .tab-main .typography-body .typography-item.in-view {
        opacity: 1;
    }
    .nbd-sidebar #tab-typography .tab-main .typography-body .typography-item img {
        background: none;
    }
    .popup-share.nbd-popup .overlay-main {
        background: rgba(255,255,255,0.85);
    }
    .nbd-tool-lock {
        top: 50px;
    }
    .nbd-toolbar .toolbar-text .nbd-main-menu.menu-left .menu-item .sub-menu>div#toolbar-font-size-dropdown {
        max-height: 240px;
    } 
    .nbd-toolbar .toolbar-text .nbd-main-menu.menu-right .sub-menu ul li.selected {
        background-color: rgba(158,158,158,.2);
    }
    .design-wrap {
        position: absolute;
    }
    .design-wrap:hover {
        box-shadow: 0 1px 3px 0 rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 2px 1px -1px rgba(0,0,0,.12);
    }
    .nbd-toasts .toast {
        -webkit-box-shadow: 0 5px 5px -3px rgba(0,0,0,.2), 0 8px 10px 1px rgba(0,0,0,.14), 0 3px 14px 2px rgba(0,0,0,.12);
        box-shadow: 0 5px 5px -3px rgba(0,0,0,.2), 0 8px 10px 1px rgba(0,0,0,.14), 0 3px 14px 2px rgba(0,0,0,.12);
    }
    .nbd-context-menu .main-context .contexts .context-item i {
        width: 21px;
    }
    .nbd-context-menu .main-context .contexts .context-item i sub{
        right: 5px;
    }
    .nbd-context-menu .main-context .contexts .context-item.active i {
        color: red;
    }            
    @keyframes timeline {
        0% {
            background-position: -350px 0;
        }
        100% {
            background-position: 400px 0;
        }
    }
    .font-loading {
        animation: timeline;
        animation-duration: 1s;
        animation-timing-function: linear;
        animation-iteration-count: infinite;
        background: linear-gradient(to right, #eeeeee 8%, #dddddd 18%, #eeeeee 33%);
        background-size: 800px auto;
        background-position: 100px 0;
        pointer-events: none;
        opacity: 0.7;
    }
    .group-font li {
        cursor: pointer;
    }
    .nbd-main-menu .sub-menu li span.font-name-wrap {
        line-height: 40px;
        width: 100%;
        display: flex;
        justify-content: space-between;
    }
    .nbd-main-menu .sub-menu li span.font-name {
        margin-right: 10px;
        font-size: 18px;
    }
    .nbd-main-menu .sub-menu li .font-selected {
        line-height: 40px;
        margin-left: 5px;
        color: #404762;
    }
    .toolbar-font-search i.icon-nbd-clear {
        position: absolute;
        right: 15px;
        top: 10px;
        width: 24px;
        height: 33px;
        line-height: 33px;
        cursor: pointer;
    }
    .clipart-wrap .clipart-item,
    .mansory-wrap .mansory-item {
        visibility: visible !important; 
        width: 33.33%;
        padding: 2px;
        opacity: 0;
        z-index: 3;
        cursor: pointer;
    }
    .mansory-wrap{
        margin-top: 15px;
    }
    .clipart-wrap .clipart-item img {
        border: 4px solid rgba(64, 71, 98, 0.08);
        background: #d0d6dd;
        width: 100%;
        min-height: 30px;
        border-radius: 4px;
    }
    .mansory-wrap .mansory-item.in-view,
    .clipart-wrap .clipart-item.in-view {
        opacity: 1;
    }
    .mansory-wrap .mansory-item .photo-desc {
        position: absolute;
        opacity: 0;
        visibility: hidden;
        -webkit-transform: translateY(50%);
        -ms-transform: translateY(50%);
        transform: translateY(50%);
        -webkit-transition: all .2s;
        transition: all .2s;
        bottom: 2px;
        left: 2px;
        padding: 2px 10px;
        display: block;
        width: -webkit-calc(100% - 4px);
        width: calc(100% - 4px);
        text-align: left;
        background: rgba(0,0,0,.3);
        color: #fff;
        font-size: 10px;        
    }
    .mansory-wrap .mansory-item:hover .photo-desc {
        opacity: 1;
        visibility: visible;
        -webkit-transform: translateY(0);
        -ms-transform: translateY(0);
        transform: translateY(0);        
    }
    .mansory-wrap .mansory-item 
    .nbd-sidebar #tab-svg .cliparts-category {
        margin-top: 70px;
        padding: 0px 10px 10px;        
    }
    .nbd-perfect-scroll {
        position: relative;
        overflow: hidden;        
    }
    .nbd-onload {
        pointer-events: none;
        opacity: 0.7;
    }
    .nbd-color-picker-preview {
        width: 24px;
        height: 24px;
        border-radius: 4px;
        display: inline-block;
        box-shadow: rgba(0, 0, 0, 0.15) 1px 1px 6px inset, rgba(255, 255, 255, 0.25) -1px -1px 0px inset;        
    }
    .nbd-toolbar .main-toolbar .tool-path li.menu-item.item-color-fill {
        margin: 0;
        padding: 2px;
    }
    .nbd-sidebar #tab-photo .nbd-items-dropdown .main-items .items .item[data-type="pixabay"] .main-item .item-icon {
        padding: 10px 20px;
    }
    .nbd-sidebar #tab-photo .nbd-items-dropdown .main-items .items .item[data-type="pixabay"] .main-item .item-icon i {
        font-size: 60px;
    }
    .nbd-sidebar .nbd-items-dropdown .result-loaded .nbdesigner-gallery .nbdesigner-item .photo-desc {
        font-size: 10px;
    }
    .nbd-sidebar #tab-photo .nbd-items-dropdown .loading-photo {
        width: 40px;
        height: 40px;
        position: unset;
        margin-left: 50%;
        margin-top: 20px;        
    }
    .nbd-sidebar .nbd-items-dropdown .info-support {
        left: unset;
    }
    .nbd-sidebar .nbd-items-dropdown .info-support i.close-result-loaded {
        right: 0;
    }
    .nbd-sidebar .nbd-items-dropdown .info-support i, .nbd-sidebar .nbd-items-dropdown .info-support span {
        background: #404762;
    }    
    #tab-photo .ps-scrollbar-x-rail {
        display: none;
    }
    .nbd-sidebar .tabs-content .nbd-search input {
        border-radius: 4px;
    }
    .type-instagram.button-login {
        display: flex;
        margin: auto;
        -webkit-box-pack: center;
        -webkit-justify-content: center;
        -ms-flex-pack: center;
        justify-content: center;
        -webkit-box-align: center;
        -webkit-align-items: center;
        -ms-flex-align: center;
        align-items: center;        
    }
    .type-instagram.button-login .icon-nbd {
        color: #fff;
        vertical-align: middle;
        font-size: 20px;
        margin-right: 5px;        
    }
    .type-instagram.button-login span {
        color: #fff;
    }
    .popup-term .head {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;        
    }
    .form-control:focus {
        border-color: rgba(64, 71, 98, 1);
        outline: 0;
        box-shadow: none;
    }    
    .nbd-dnd-file {
        cursor: pointer;
    }
    .nbd-dnd-file.highlight {
        border: 2px dashed rgba(64, 71, 98, 1) !important;
    }
    .nbd-sidebar .tab-scroll{
        -ms-overflow-style:none;
    }
    .nbd-onloading {
        pointer-events: none;
    }  
    .nbd-stages .stage {
        padding: 40px 50px 50px;
        overflow: hidden;
        height: 100%;
        width: 100%;
        position: absolute;
        display: block;
        text-align: center;
        box-sizing: border-box;
    }  
    .nbd-toolbar-zoom {
        bottom: 15px;
    }
    .nbd-toolbar-zoom .zoomer-toolbar .nbd-main-menu {
        box-shadow: 0 1px 3px 0 rgba(0,0,0,.2), 0 1px 1px 0 rgba(0,0,0,.14), 0 2px 1px -1px rgba(0,0,0,.12); 
    }
    .bleed-line, .safe-line {
        box-sizing: border-box;
        position: absolute;
    }
    .bleed-line {
        border: 1px solid red;
    }
    .safe-line {
        border: 1px solid green;
    }   
    .fullScreenMode .design-zone {
        pointer-events: none;
    }
    .fullScreenMode .page-toolbar {
        display: none;
    }
    .fullScreenMode .stage{
        padding: 0;
    }
    .nbd-sidebar #tab-element .main-items .item[data-type=draw] .item-icon i {
        color: #404762;
    }   
    .nbd-sidebar #tab-layer .inner-tab-layer .menu-layer .menu-item.active {
        border: 1px solid #404762;
    }
    .sortable-placeholder {
        border: 3px dashed #aaa;
        height: 50px;
        display: flex;
        margin: 4px;
    }
    .nbd-toolbar .toolbar-text .nbd-main-menu.menu-left .menu-item .toolbar-bottom span {
        line-height: 24px;
    }
    .nbd-sidebar #tab-element .nbd-items-dropdown .content-items .content-item.type-qrcode .main-input input {
        padding: 10px;
    }
    .nbd-hiden {
        visibility: hidden;
    }
    .main-qrcode svg{
        transform: scale(2) translateY(25%);
    }
    .main-qrcode svg path{
        fill: #404762;
    }
    .tab-scroll .ps__scrollbar-x-rail {
        display: none;
    }
    .main-color-palette {
        display: -webkit-box;
        display: -webkit-flex;
        display: -ms-flexbox;
        display: flex;
        -webkit-flex-wrap: wrap;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        -webkit-box-pack: start;
        -webkit-justify-content: flex-start;
        -ms-flex-pack: start;
        justify-content: flex-start;
    }    
    .main-color-palette li {
        list-style: none;
        cursor: pointer;
        width: 40px;
        height: 40px;
        -webkit-box-shadow: inset 1px 1px 0 rgba(0,0,0,.05), inset -1px -1px 0 rgba(0,0,0,.05);
        box-shadow: inset 1px 1px 0 rgba(0,0,0,.05), inset -1px -1px 0 rgba(0,0,0,.05);
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        color: transparent;
        background-color: currentColor;
        display: inline-block;
    }    
    .nbd-sidebar #tab-product-template #tab-template {
        padding: 0;
    }
    [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak {
      display: none !important;
    }
    .fullScreenMode .nbd-stages {
        width: 100%;
        background: #000;
    }
    .fullScreenMode .nbd-stages .ps__scrollbar-x-rail,
    .fullScreenMode .nbd-stages .ps__scrollbar-y-rail {
        display: none;
    }
    .fullscreen-stage-nav {
        position: absolute;
        bottom: 40px;
        right: 40px;
        display: none;
    }
    .fullScreenMode .fullscreen-stage-nav {
        display: inline-block;
    }
    .nbd-templates {

    }
    .nbd-templates .item .item-img {
        height: auto;
        text-align: center;
    }
    .nbd-templates .item .main-item.global .item-img {
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;                
    }
    .nbd-templates .item .item-img img {
        vertical-align: top;
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;   
        border-radius: 4px;
    }
    .nbd-sidebar #tab-element .nbd-items-dropdown .main-items .items .item .main-item .item-icon, 
    .nbd-sidebar #tab-photo .nbd-items-dropdown .main-items .items .item .main-item .item-icon {
        border-radius: 4px; 
    }
    .nbd-templates .items .item .main-item {
        cursor: pointer;
        -webkit-transition: all 0.4s;
        -moz-transition: all 0.4s;
        transition: all 0.4s;
    }  
    .nbd-templates .items .item .main-item.global {
        position: relative;
        width: 100%;
        padding-top: 100%;                 
    }
    .nbd-templates .items .item .main-item.image-onload {                
        animation: timeline;
        animation-duration: 1s;
        animation-timing-function: linear;
        animation-iteration-count: infinite;
        background: linear-gradient(to right, #eeeeee 8%, #dddddd 18%, #eeeeee 33%);
        background-size: 800px auto;
        background-position: 100px 0;
        pointer-events: none;
        opacity: 0.7;                  
    }
    .nbd-templates .items .item .main-item.image-onload img {
        opacity: 0;
    }
    .nbd-templates .items .item .main-item:hover img {
        -webkit-box-shadow: 0 3px 5px -1px rgba(0,0,0,.2), 0 5px 8px 0 rgba(0,0,0,.14), 0 1px 14px 0 rgba(0,0,0,.12);
        box-shadow: 0 3px 5px -1px rgba(0,0,0,.2), 0 5px 8px 0 rgba(0,0,0,.14), 0 1px 14px 0 rgba(0,0,0,.12);
    }            
    .nbd-templates .items .item {
        width: 50%;
        box-sizing: border-box;
        display: inline-block;
        padding: 5px;
    }
    .nbd-mode-1 .nbd-main-bar .logo {
        visibility: hidden;
        width: 0;
    }
    .nbd-popup.popup-share .main-popup .body .share-btn .nbd-button:focus,
    .nbd-popup.popup-share .main-popup .body .share-btn .nbd-button:hover {
        color: #fff;
        text-decoration: none;
    }
    .nbd-sidebar #tab-element .nbd-items-dropdown .content-items .content-item.type-draw .brush .nbd-sub-dropdown ul li.active {
        background-color: #404762;
    }
    .nbd-sidebar #tab-element .nbd-items-dropdown .content-items .content-item.type-draw .brush .nbd-sub-dropdown ul li.active span {
        color: #fff;
    }
    .default-palette .first-left {
        border-top-left-radius: 4px;
    }
    .default-palette .first-right {
        border-top-right-radius: 4px;
    }
    .default-palette .last-left {
        border-bottom-left-radius: 4px;
    }
    .default-palette .last-right {
        border-bottom-right-radius: 4px;
    }   
    .nbd-signal .signal-logo {
        opacity: 0.7;
    }    
    .nbd-sidebar #tab-element .nbd-items-dropdown {
        margin-top: 0;
    }
    .nbd-warning {
        position: absolute;
        top: 60px;                
    }
    .nbd-warning .main-warning {
        box-shadow: 0 5px 5px -3px rgba(0,0,0,.2), 0 8px 10px 1px rgba(0,0,0,.14), 0 3px 14px 2px rgba(0,0,0,.12);
        background: #404762;
        -webkit-transform: unset;
        transform: unset;
    }
    .nbd-warning .main-warning i,
    .nbd-warning .main-warning span {
        color: #fff;
    }
    .nbd-tip {
        position: fixed;
        -webkit-border-radius: 2px;
        border-radius: 2px;
        -webkit-box-shadow: 1px 0 10px rgba(0,0,0,.15);
        box-shadow: 1px 0 10px rgba(0,0,0,.15);
        top: 110px;
        right: 0;
        background: #fff;
        display: flex;
        max-width: 265px;
        -webkit-transition: all 1s;
        -moz-transition: all 1s;
        transition: all 1s;
        transform: translateX(calc(100% - 3px));
        border-left: 3px solid #404762;
        cursor: pointer;                
    }
    .nbd-tip:hover {
        transform: translateX(calc(100% - 70px));
        border-left: none;
    }
    .tip-icon {
        width: 70px;
        padding: 10px;    
        display: flex;
        flex-direction: column;
        justify-content: center;                
    }
    .tip-icon svg{
        width: 50px;
        height: 50px;
    }
    .tip-content p {
        font-size: 12px;
        margin: 0;
    }
    .nbd-tip.nbd-show {
        z-index: 99999999;
        border-left: none;
        transform: translateX(0);
    }
    .tip-content-inner {
        position: relative;
        padding: 10px 10px 10px 0;
    }
    .nbd-tip .icon-nbd-clear {
        cursor: pointer;
        position: absolute;
        font-size: 20px;
        top: 0;
        right: 17px;
    }
    .nbd-sidebar #tab-photo .result-loaded .content-items div[data-type=image-upload] .form-upload i {
        cursor: pointer;
    }
    .nbd-round {
        border-radius: 50%;
        overflow: hidden;
    }
    .nbd-mode-1 .nbd-main-bar .menu-mobile.icon-nbd-menu {
        padding-left: 45px;
    }
    .nbd-sidebar #tab-product-template .tab-main {
        margin-top: 10px;
        height: calc(100% - 10px);
    }    
    .nbd-template-head{
        margin: 0;
        padding: 10px;
        text-align: left;
        font-size: 23px;                
    }
    .tab-scroll .ps__scrollbar-y-rail {
        display: none;
    }
    .nbd-main-bar .logo img {
        min-width: 40px;
        max-width: 140px;
        max-height: 40px;
        width: unset;
    }       
    .nbd-popup.popup-share .main-popup .body .share-with ul.socials li.social {
        opacity: 0.5;
    } 
    .nbd-popup.popup-share .main-popup .body .share-with ul.socials li.social.active {
        opacity: 1;
    }
    .nbd-color-palette .nbd-color-palette-inner .main-color-palette li:hover {
        -webkit-box-shadow: inset 1px 1px 0 rgba(0,0,0,.05), inset -1px -1px 0 rgba(0,0,0,.05);
        box-shadow: inset 1px 1px 0 rgba(0,0,0,.05), inset -1px -1px 0 rgba(0,0,0,.05);
    } 
    .nbd-sidebar #tab-layer .inner-tab-layer .menu-layer .menu-item.active {
        border: 2px solid #0e9dde;
    }   
    .nbd-load-page {
        width: 100%;
        height: 100%;                
    }
    .nbd-toolbar .toolbar-text .nbd-main-menu.menu-left .menu-item.item-font-size .sub-menu ul li {
        cursor: pointer;
    }
    .nbd-global-color-palette {
        top: 110px;
        left: 50%;
        margin-left: -110px;
        z-index: 10000002;
    }
    .nbd-global-color-palette.nbd-color-palette .nbd-color-palette-inner:before,
    .nbd-global-color-palette.nbd-color-palette .nbd-color-palette-inner:after {
        display: none !important;
    }
    .nbd-main-bar .logo{
        color: #404762;
    }
    .logo-without-image {
        border: 2px solid #404762;
        border-radius: 4px;
        padding: 5px;
    }
    .nbd-sidebar #tab-layer .inner-tab-layer .menu-layer .menu-item {
        border: 2px solid #fff;
/*        -moz-transition: all 0.4s;
        -webkit-transition: all 0.4s;
        transition: all 0.4s;*/
        border-radius: 4px;
    }
    .nbd-sidebar .nbd-items-dropdown .result-loaded .nbdesigner-gallery .nbdesigner-item img {
        border-radius: 4px;
    }
    .nbd-sidebar .tabs-content .nbd-search input {
        -webkit-box-shadow: 1px 0 21px rgba(0,0,0,.15);
        box-shadow: 1px 0 21px rgba(0,0,0,.15);
    }
    @media screen and (min-width: 1500px) {
        .mockup-preview .nbd-popup-tigger {
            transform: rotate(-90deg) translateX(100%) !important;
        }
    }
    @media screen and (min-width: 768px) {
        .nbd-stages .stage .page-toolbar {
            top: 50%;                
        }
        .page-toolbar .icon-nbd-arrow-upward{
            background: #ddd;
            border-radius: 12px;
            padding: 8px 0;
        }
    }
    .nbd-stages .stage .stage-main.nbd-without-shadow {
        -webkit-box-shadow: none !important;
        box-shadow: none !important;
        background: transparent !important;
    }
/*            .stage-background {
        display: flex;
        justify-content: center;
        align-items: center;
    }*/
    #selectedTab span {
        position: absolute;
        right: 0;
        top: -7px;
        width: 7px;
        height: 7px;
        background-color: #d0d6dd;
    }
    #selectedTab span:last-child {
        top: auto;
        bottom: -7px;
    }
    #selectedTab span:after {
        content: '';
        position: absolute;
        right: 0;
        bottom: 0;
        width: 14px;
        height: 14px;
        border-radius: 7px;
        background-color: #404762;
    }
    #selectedTab span:last-child:after {
        top: 0;
        bottom: auto;
    }
    .nbd-modern-rtl #selectedTab span, .nbd-modern-rtl #selectedTab span:after {
        left: 0;
    }
    .nbd-button {
        border-radius: 4px;
    }
    .bounding-rect-real-size {
        position: absolute;
        top: -15px;
        width: 100%;
        left: 0;
        color: #404762;
        height: 15px;
        line-height: 15px;
        font-size: 9px;
        font-family: monospace;
        text-shadow: 1px 1px #fff;
    }
    .nbd-prevent-select{
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -o-user-select: none;
        user-select: none;
    }
    .nbd-context-menu {
        z-index: 100;
    }
    .nbd-sidebar #tab-layer .inner-tab-layer .menu-layer .menu-item .item-right i{
        opacity: 0.7;
    }
    .nbd-sidebar #tab-layer .inner-tab-layer .menu-layer .menu-item .item-right i.icon-visibility,
    .nbd-sidebar #tab-layer .inner-tab-layer .menu-layer .menu-item .item-right i.icon-lock,
    .nbd-sidebar #tab-layer .inner-tab-layer .menu-layer .menu-item .item-right i.icon-close {
        color: #888;
    }
    .nbd-sidebar #tab-layer .inner-tab-layer .menu-layer .menu-item .item-right i.icon-visibility:hover {
        color: #06d79c;
    }
    .nbd-sidebar #tab-layer .inner-tab-layer .menu-layer .menu-item .item-right i.icon-lock:hover {
        color: #ffb22b;
    }
    .nbd-sidebar #tab-layer .inner-tab-layer .menu-layer .menu-item .item-right i.icon-close:hover {
        color: #ef5350;
    }  
    .nbd-sidebar .hide-tablet {
        display: none;
    }
    .nbd-checkbox-group input{
        position: absolute;
        margin-left: -9999px;
        visibility: hidden;
    }
    .nbd-checkbox-group input:checked+label:before{
        background-color: #4F5467;
    }
    .nbd-checkbox-group input:checked+label:after{
        margin-left: 20px;
    }
    .box-curved-reverse {
        display: flex;
    }
    .box-curved-reverse span{
        margin-right: 10px;
    }
    .nbd-checkbox-group label{
        padding: 2px;
        width: 40px;
        height: 20px;
        background-color: #ddd;
        -webkit-border-radius: 10px;
        -moz-border-radius: 10px;
        -ms-border-radius: 10px;
        -o-border-radius: 10px;
        border-radius: 10px;
        display: block;
        position: relative;
        cursor: pointer;
        outline: 0;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .nbd-checkbox-group label:before,.nbd-checkbox-group label:after{
        display: block;
        position: absolute;
        top: 1px;
        left: 1px;
        bottom: 1px;
        content: "";
    }
    .nbd-checkbox-group label:before {
        right: 1px;
        background-color: #f1f1f1;
        -webkit-border-radius: 10px;
        -moz-border-radius: 10px;
        -ms-border-radius: 10px;
        -o-border-radius: 10px;
        border-radius: 10px;
        -webkit-transition: background .4s;
        -moz-transition: background .4s;
        -o-transition: background .4s;
        transition: background .4s;
    }
    .nbd-checkbox-group label:after {
        width: 18px;
        background-color: #fff;
        -webkit-border-radius: 100%;
        -moz-border-radius: 100%;
        -ms-border-radius: 100%;
        -o-border-radius: 100%;
        border-radius: 100%;
        -webkit-box-shadow: 0 2px 5px rgba(0,0,0,.3);
        -moz-box-shadow: 0 2px 5px rgba(0,0,0,.3);
        box-shadow: 0 2px 5px rgba(0,0,0,.3);
        -webkit-transition: margin .4s;
        -moz-transition: margin .4s;
        -o-transition: margin .4s;
        transition: margin .4s;
    }
    .show-inline-on-mobile {
        display: none;
    }
    @media screen and (max-width: 767px) {
        .mockup-wrap {
            border: none;
        }
        .nbs-slide-nav {
            
        }
        .hidden-on-mobile {
            display: none;
        }
        .show-inline-on-mobile {
            display: inline-block;
        }
        .mockup-wrap {
            height: 350px;
            padding: 0;
        }
        .nbd-simple-slider {
            position: relative;
        }
        .nbs-slide-nav {
            position: absolute;
            top: calc(50% - 15px);
            width: 30px;
            height: 30px;
            z-index: 2;
            border: 2px solid #404762;
            color: #404762;
            border-radius: 50%;
            line-height: 26px;
            font-size: 30px;
            box-sizing: border-box;
            background: #fff;
        }
        .nbs-slide-nav.prev {
            left: -5px;
        }
        .nbs-slide-nav.next {
            right: -5px;
        }
        .mockup-preview-wrap {
            border: none;
            box-shadow: none;
        }
        .mockup-wrap ul {
            display: block;
            white-space: nowrap;
        }
        .mockup-wrap .mockup-preview {
            display: inline-block;
            width: 100%;
            max-width: 100%;
            vertical-align: top;
        }
        .popup-nbd-mockup-preview .main-popup{
            width: 95%;
        }
        .nbd-user-design {
            width: calc(50% - 10px);
        }
        .nbd-main-bar ul.menu-left .menu-item.item-nbo-options {
            padding: 5px 15px;
        }
        .nbd-global-color-palette {
            margin-left: 0;
        }
        .nbd-toolbar .toolbar-common .nbd-main-menu li.menu-item.active > i {
            color: #404762;
        }
        .nbd-toolbar .toolbar-text .nbd-main-menu.menu-left .menu-item .toolbar-input {
            width: 50px;
        }
        .nbd-toolbar .toolbar-text .nbd-main-menu.menu-left .menu-item .toolbar-bottom {
            padding: 0px 10px;
        }
        .nbd-stages .stage {
            padding: 10px;
            padding-bottom: 60px;
            padding-top: 0;
        }
        .nbd-stages .stage .stage-main {
            margin: 0;
        }
        .nbd-stages .stage .page-toolbar {
            bottom: -44px;
        }
        .nbd-tip {
            display: none;
        }  
        .nbd-main-bar ul.menu-left .item-view>.sub-menu {
            -webkit-transform: translateX(-40%);
            -moz-transform: translateX(-40%);
            transform: translateX(-40%);
        }
        .nbd-popup.nb-show {
            z-index: 999999999999;
        }
/*        .android .nbd-workspace .main {
            height: -webkit-calc(100vh - 192px);
            height: calc(100vh - 192px);                    
        }*/
        .nbd-mode-1 .nbd-main-bar .menu-mobile.icon-nbd-clear {
            padding-left: 45px;
        }   
        .android input[name="search"]:focus {
            top: 70px;
            left: 10px;
            width: calc(100% - 20px);
            position: fixed;
            z-index: 100000004;
        }
        html, body,#design-container, #designer-controller{
            min-height: 100% !important;
            height: 100%;
        }
        .nbd-workspace {
            height: 100% !important;
        }
        .nbd-workspace .main {
            height: calc(100% - 114px) !important;
        }
        /*page category*/
        .nbd-mode-2.safari .nbd-sidebar .tabs-content {
            height: calc(100vh - 190px);
        }
        .nbd-mode-2.android .nbd-sidebar .tabs-content {
            height: calc(100vh - 170px);
        }  
    }
</style>