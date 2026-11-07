/* nbdesignerjs
 * @author  netbaseteam
 * @link http://netbaseteam.com
 * @version 2.5.0
 * @created Jun 2016
 * @modified 29, Jun 2019
 * */
var appConfig = {
    localMode: false,
    debugMode: false,
    isModern: true,
    isVisual: false,
    isCuz: false,
    ready: false,
    domainChanged: false,
    init: function(){
        this.mediaUrl = this.localMode ? 'http://localhost/nbmedia/v1' : 'https://studio.cmsmart.net/v1';
        this.domainChanged = NBDESIGNCONFIG.nbdesigner_fix_domain_changed == 'yes' ? true : false;
        this.isModern = NBDESIGNCONFIG.layout == 'modern' ? true : false;
        this.isVisual = NBDESIGNCONFIG.layout == 'visual' ? true : false;
    },
    allowDynamicQty: NBDESIGNCONFIG.nbdesigner_enable_duplicate_design == 'yes' ? true : false,
    autoSave: NBDESIGNCONFIG.nbdesigner_auto_save_draft == 'yes' ? true : false,
    loadAllDesign: NBDESIGNCONFIG.nbdesigner_enable_load_all_design == 'yes' ? true : false
};
function arrayMin(arr) {
    return arr.reduce(function (p, v) {
        p = parseInt(p);
        v = parseInt(v);
        return (p < v ? p : v);
    });
};
/*fabric.Object.prototype._drawControl = function(control, ctx, methodName, left, top) {
    if (!this.isControlVisible(control)) {
        return;
    }
    var size = this.cornerSize, stroke = !this.transparentCorners && this.cornerStrokeColor;
    switch (this.cornerStyle) {
        case 'circle':
            ctx.beginPath();
            ctx.arc(left + size / 2, top + size / 2, size / 2, 0, 2 * Math.PI, false);
            ctx[methodName]();
            if (stroke) {
                ctx.stroke();
            }
            break;
        default:
            this.transparentCorners || ctx.clearRect(left, top, size, size);
            ctx[methodName + 'Rect'](left, top, size, size);
            if (stroke) {
                ctx.strokeRect(left, top, size, size);
            }
    }
    if( control == 'mtr' ){
        var rotateControlIcon = new Image;
        rotateControlIcon.src = "data:image/svg+xml,%3Csvg%20xmlns='http://www.w3.org/2000/svg'%20width='24'%20height='24'%20viewBox='0%200%2024%2024'%3E%3Cpath%20fill='currentColor'%20d='M15.25%2018.48V15a.75.75%200%201%200-1.5%200v4c0%20.97.78%201.75%201.75%201.75h4a.75.75%200%201%200%200-1.5h-2.6a8.75%208.75%200%200%200-2.07-15.53.75.75%200%201%200-.49%201.42%207.25%207.25%200%200%201%20.91%2013.34zM8.75%205.52V9a.75.75%200%200%200%201.5%200V5c0-.97-.78-1.75-1.75-1.75h-4a.75.75%200%200%200%200%201.5h2.6a8.75%208.75%200%200%200%202.18%2015.57.75.75%200%200%200%20.47-1.43%207.25%207.25%200%200%201-1-13.37z'/%3E%3C/svg%3E";
        try {
            ctx.drawImage(rotateControlIcon, left+1, top+1, size-2, size-2);
        } catch (e) {
            //ctx[methodName](left, top, size, size);
        }
    }
};*/
if(NBDESIGNCONFIG['ui_mode'] != 3){
    var nbdApp = angular.module('nbd-app', ["angularSpectrumColorpicker"]);
}else{
    var nbdApp = angular.module('nbdApp', ["angularSpectrumColorpicker"]);
};
nbdApp.constant("_", window._);
nbdApp.config(function( $controllerProvider, $compileProvider, $filterProvider ){
    nbdApp.controller = function( name, constructor ) {
        $controllerProvider.register( name, constructor );
        return( this );
    };
    nbdApp.directive = function( name, factory ) {
        $compileProvider.directive( name, factory );
        return( this );
    };
    nbdApp.filter = function( name, filter ) {
        $filterProvider.register( name, filter );
        return( this );
    };
});
nbdApp.controller('designCtrl', ['$scope', 'FabricWindow', '$window', 'NBDDataFactory', 'filterFontFilter', 'filterArtFilter', '$timeout', '$http', '$document', '$interval',
    function($scope, FabricWindow, $window, NBDDataFactory, filterFontFilter, filterArtFilter, $timeout, $http, $document, $interval){
    $scope.stages = [];
    $scope.defaultStageStates = {
        isActiveLayer: false,
        isLayer: false,
        isGroup: false,
        isNativeGroup: false,
        isText: false,
        isImage: false,
        isPath: false,
        isShape: false,
        isEditing: false,
        isRedoable: false,
        isUndoable: false,         
        elementUpload: false,  
        isShowToolBox: false,
        enableRotate: true,
        enableOpacity: true,
        enableChangePathColor: true,
        oos: false,         
        ilr: false,         
        boundingObject: {},
        boundingRealSize: {},
        coordinates: {},
        rotate: {},
        uploadZone: {},
        opacity: 100,
        snaplines: {},
        itemId: null,
        tempParameters: null,
        usedFonts: [],
        type: null,
        text: {
            fontFamily: {
                alias: NBDESIGNCONFIG.default_font.alias,
                r: NBDESIGNCONFIG.default_font.file.r,
                b: NBDESIGNCONFIG.default_font.file.b,
                i: NBDESIGNCONFIG.default_font.file.i,
                bi: NBDESIGNCONFIG.default_font.file.bi
            },
            fontSize: 14,
            fontFamily: NBDESIGNCONFIG.default_font.alias,
            textAlign: 'left',
            fontWeight: false,
            textDecoration: false,
            fontStyle: '',
            spacing: 0,
            lineHeight: 1.16,
            is_uppercase: false,
            fill: '#06d79c'
        }, 
        svg: {groupPath: {}, currentPath: null},
        image: {},
        scaleRange: [],
        currentScaleIndex: 0,
        fitScaleIndex: 0,
        fillScaleIndex: 0,
        lostCharLayers: []
    };
    $scope.defaultConfig = {
	name: 'Typography',
	width: 200,
	height: 200,
	cheight: 200,
	cwidth: 200,
        left: 0,
        top: 0,
	bleed_lr: 0,
	bleed_tb: 0,
	margin_lr: 0,
	margin_tb: 0,
	bgType: 'tran',
	bgColor: '#ffffff',
	bgImage: 0,
	showBleed: 0,
	showOverlay: 0,
	showSafeZone: 0        
    };
    $scope.initSettings = function(){
        $scope.debugMode = appConfig.debugMode;      
        $scope.the_first_time_load_page = true;
        $scope.printingOptionsAvailable = NBDESIGNCONFIG.show_nbo_option == '1' ? false : true;
//        if( angular.isDefined(NBDESIGNCONFIG.product_data.config ) && angular.isDefined(NBDESIGNCONFIG.product_data.config.variations) ){
//            
//        }
//        if( NBDESIGNCONFIG.ui_mode == 1 ){
//            if( !!nbd_window.NBDESIGNERPRODUCT.variations ){
//                //todo
//            }
//        };
        angular.copy(NBDESIGNCONFIG, $scope.settings);       
        angular.extend($scope.settings, {
            showRuler: checkMobileDevice() ? false : (NBDESIGNCONFIG.nbdesigner_show_ruler == 'yes' ? true : false),
            showGrid: NBDESIGNCONFIG.nbdesigner_show_grid == 'yes' ? true : false,
            showDimensions: NBDESIGNCONFIG.nbdesigner_show_product_dimensions == 'yes' ? true : false,
            bleedLine: NBDESIGNCONFIG.nbdesigner_show_bleed == 'yes' ? true : false,
            snapMode: {status: false, type: 'layer'},
            showWarning: {
                oos: NBDESIGNCONFIG.nbdesigner_show_warning_oos == 'yes' ? true : false,
                ilr: NBDESIGNCONFIG.nbdesigner_show_warning_ilr == 'yes' ? true : false
            }
        });
        $scope.offsetDesignWrap = NBDESIGNCONFIG.nbdesigner_show_design_border == 'yes' ? -1 : 0;
        $scope.rateConvertCm2Px96dpi = 37.795275591;
        $scope.currentStage = 0;
        $scope.showTextColorPicker = false;
        $scope.showBgColorPicker = false;
        $scope.__colorPalette = __colorPalette;
        $scope.currentColor = NBDESIGNCONFIG.nbdesigner_default_color;
        $scope.listAddedColor = [];
        $scope.tempStageDesign = null;
        $scope.listFontSizeInPt = ['6','8','10','12','14','16','18','21','24','28','32','36','42','48','56','64','72','80','88','96','104','120','144','288','576','1152'];
        if( angular.isDefined( NBDESIGNCONFIG.nbdesigner_default_font_sizes ) && NBDESIGNCONFIG.nbdesigner_default_font_sizes != '' ){
            var fontSizes = NBDESIGNCONFIG.nbdesigner_default_font_sizes.replace(/ /g, '');
            $scope.listFontSizeInPt = fontSizes.split(',');
        }
        $scope.forceMinSize = ( angular.isDefined( NBDESIGNCONFIG.nbdesigner_force_min_font_size ) && NBDESIGNCONFIG.nbdesigner_force_min_font_size == 'yes' ) ? true : false;
        $scope.customTemplate = {
            name: '',
            type: '1',
            selectedTags: [],
            selectedColors: [],
            newColor: '#ffffff',
            showPicker: false,
            tags: [],
            tag_ids: [],
            reload: 0,
            template_thumb: null
        };
        if( NBDESIGNCONFIG.task == 'edit' && NBDESIGNCONFIG.design_type == 'template' ){
            if( angular.isDefined( NBDESIGNCONFIG.product_data.template ) ){
                $scope.customTemplate.name = NBDESIGNCONFIG.product_data.template.name;
                $scope.customTemplate.selectedTags = NBDESIGNCONFIG.product_data.template.tags.split(",").map(function( tag ){
                    return parseInt( tag );
                });
                if( NBDESIGNCONFIG.product_data.template.colors != '' ){
                    $scope.customTemplate.selectedColors = NBDESIGNCONFIG.product_data.template.colors.split(",");
                }
            }
        }
        if( angular.isDefined(NBDESIGNCONFIG.product_data.option.listFontSizeInPt) ){
            if( NBDESIGNCONFIG.product_data.option.listFontSizeInPt != '' ){
                NBDESIGNCONFIG.product_data.option.listFontSizeInPt = NBDESIGNCONFIG.product_data.option.listFontSizeInPt.replace(/ /g, '');
                $scope.listFontSizeInPt = NBDESIGNCONFIG.product_data.option.listFontSizeInPt.split(',');
            }
        };
        if( angular.isDefined(NBDESIGNCONFIG.product_data.config ) && angular.isDefined(NBDESIGNCONFIG.product_data.config.dpi) && angular.isDefined(NBDESIGNCONFIG.product_data.config.option_dpi) ){
            $scope.settings.product_data.option.dpi = NBDESIGNCONFIG.product_data.config.dpi;
        }
        if( angular.isDefined(NBDESIGNCONFIG.product_data.config ) && angular.isDefined(NBDESIGNCONFIG.product_data.config.origin_product) ){
            $scope.settings.product_data.origin_product = NBDESIGNCONFIG.product_data.config.origin_product;
        };
        $scope.completedInsertTemplate = false;
        $scope.changePrintingOptions( true );
        $scope.processProductSettings();
        $scope.resource = {
            defaultPalette: [
                ['#ff5c5c', '#ffbd4a', '#fff952', '#99e265', '#35b729', '#44d9e6', '#2eb2ff', '#5271ff', '#b760e6', '#ff63b1'],
                ['#000000', '#666666', '#a8a8a8', '#d9d9d9', '#ffffff']
            ],
            social: {link: '', wa_link: ''},
            //config: {qty: null, qtys: []},
            config: {},
            jsonDesign: {},
            usedFonts: [],
            imageFromUrl: '',
            svgCode: '',
            qrText: '',
            gapi: {},
            tempData: {template: []},
            templates: [],
            myTemplates: [],
            myDesigns: [],
            cartTemplates: [],
            templateCats: [],
            drawMode: {status: false, brushWidth: 1, brushType: 'Pencil', brushColor: NBDESIGNCONFIG.nbdesigner_default_color},
            personal: {status: false, type: ''},
            webcam: {status: false},
            typography: {filter: {perPage: 20, currentPage: 1, total: 0}, data: [], init: true, onload: false},
            clipart: {filter: {perPage: 20, currentPage: 1, total: 0, currentCat: {}}, data: [], onload: false, init: true, filteredArts : []},
            font: {filter: {perPage: 10, currentPage: 1, total: 0}, data: [], filteredFonts : []},
            photo: {filter: {perPage: 20, currentPage: 1, total: 0, totalPage: 1}, data: [], init: true, onload: false, type: '', photoSearch: '', onclick: false},
            dropbox: {filter: {perPage: 10, currentPage: 1, total: 0}, data: [], onload: false, init: true, filteredPhoto : []},
            facebook: {filter: {perPage: 15, currentPage: 1, total: 0}, data: [], onload: false, init: true, nextUrl: '', uid: '', accessToken: ''},
            instagram: {filter: {perPage: 10, currentPage: 1, total: 0}, data: [], onload: false, init: true, token : ''},
            upload: {filter: {perPage: 10, currentPage: 1, total: 0}, data: [], onload: false, init: true, ilr: false, progressBar: 0},
            element: {onclick: false, onload: false, contentSearch: ''},
            shape: {filter: {perPage: 20, currentPage: 1, totalPage: 0}, data: [], init: true, onload: false},
            icon: {filter: {perPage: 20, currentPage: 1, totalPage: 0}, data: [], init: true, onload: false},         
            line: {filter: {perPage: 20, currentPage: 1, totalPage: 0}, data: [], init: true, onload: false},     
            globalTemplate: {filter: {perPage: 20, currentPage: 1, totalPage: 0}, data: [], init: true, onload: false},
            templateType: 'custom',
            mockups: [],
            canAdd: true
        };
//        if( appConfig.allowDynamicQty ){
//            if( NBDESIGNCONFIG.ui_mode == 1 ){
//                $scope.resource.config.qty = nbd_window.NBDESIGNERPRODUCT.qty;
//                if( angular.isDefined( nbd_window.nbOption ) && angular.isDefined( nbd_window.nbOption.variations ) && nbd_window.nbOption.variations.length > 0 ){
//                    $scope.resource.config.variations = [];
//                    angular.copy(nbd_window.nbOption.variations, $scope.resource.config.variations);
//                }else{
//                    $scope.resource.config.variations = null;
//                }
//                if( angular.isDefined(NBDESIGNCONFIG.product_data.config ) && angular.isDefined(NBDESIGNCONFIG.product_data.config.qtys) ){
//                    angular.copy(NBDESIGNCONFIG.product_data.config.qtys, $scope.resource.config.qtys);
//                }else{
//                    $scope.resource.config.qtys = [{variation: -1, value: parseInt( nbd_window.NBDESIGNERPRODUCT.qty )}];
//                }
//            }
//        }
        if( appConfig.allowDynamicQty && NBDESIGNCONFIG.task == 'new' && NBDESIGNCONFIG.task2 == '' ){
            if( NBDESIGNCONFIG.ui_mode == 1 ){
                $scope.resource.config.qty = nbd_window.NBDESIGNERPRODUCT.qty;
            }else{
                $scope.resource.config.qty = 1;
            }
        }
        if( angular.isDefined(NBDESIGNCONFIG.product_data.config ) && angular.isDefined(NBDESIGNCONFIG.product_data.config.qty) ){
            $scope.resource.config.qty = parseInt(NBDESIGNCONFIG.product_data.config.qty);
        }
        if( angular.isDefined(NBDESIGNCONFIG.product_data.config ) && angular.isDefined(NBDESIGNCONFIG.product_data.config.fold) && NBDESIGNCONFIG.product_data.config.fold ){
            $scope.resource.config.fold = NBDESIGNCONFIG.product_data.config.fold;
            $scope.resource.config.foldClass = NBDESIGNCONFIG.product_data.config.foldClass;
            $scope.resource.config.numberPanel = NBDESIGNCONFIG.product_data.config.numberPanel;
        }
        if( appConfig.allowDynamicQty && NBDESIGNCONFIG.task == 'new' && NBDESIGNCONFIG.task2 == '' ){
            if( isNaN($scope.resource.config.qty) ) $scope.resource.config.qty = 1;
        }
        $scope.cuzIncludeExport = ['first_name', 'last_name', 'full_name', 'company', 'address', 'postcode', 'city', 'phone', 'email', 'mobile', 'website', 'vcard', 'title', 'date', 'avatar', 'c_full_name', 'c_title', 'c_mobile', 'c_phone', 'c_email', 'c_avatar'];
        $scope.includeExport = ['objectCaching', 'itemId', 'selectable', 'editable', 'lockMovementX', 'lockMovementY','lockScalingX', 'lockScalingY', 'lockRotation', 'rtl', 'elementUpload', 'forceLock', 'isBg','is_uppercase','available_color','available_color_list','color_link_group','isOverlay', 'isAlwaysOnTop', 'ilr', 'oos', 'evented', 'ptFontSize', 'origin_src', 'reverse', 'spacing', 'radius', 'field_mapping', 'isQrcode', 'qrContent', 'isBarcode', 'barCodeContent', 'v_card'];
        if( appConfig.isCuz ){
            $scope.includeExport = $scope.includeExport.concat( $scope.cuzIncludeExport );
        }
        if( angular.isDefined(NBDESIGNCONFIG.product_data.option.font_cats) ){
            $scope.settings.fonts = $scope.settings.fonts.filter(function(font){
                if (font.cat.length == 0) font.cat = ["0"];
                return !font.cat.some(function(val) { return NBDESIGNCONFIG.product_data.option.font_cats.indexOf(val) === -1 && val != "99" });
            });
        };
        $scope.resource.font.data = $scope.settings.fonts;
        if(!_.filter($scope.resource.font.data, ['alias', $scope.settings.default_font.alias]).length){
            $scope.resource.font.data.push($scope.settings.default_font);
        };
        $scope.resource.templates = $scope.settings.templates;
        if( ($scope.resource.templates.length == 0 || NBDESIGNCONFIG.product_data.option.admindesign != "1") &&  $scope.settings.nbdesigner_enable_text == 'yes' ){
            $scope.getResource('typography', '#tab-typography');
        }
        if( NBDESIGNCONFIG.product_data.option.admindesign != "1" && $scope.settings.nbdesigner_enable_text == 'no' && $scope.settings.nbdesigner_enable_clipart == 'yes' ){
            $scope.getResource('clipart', '#tab-svg', true);
        };
        if(NBDESIGNCONFIG.show_nbo_option){
            $timeout(function(){
                $scope.getPrintingOptions();
            }, 100);
        }
        if( $scope.settings.task != 'create_template' && NBDESIGNCONFIG.product_data.option.admindesign == "1" 
                && NBDESIGNCONFIG.product_data.option.global_template == "1"
                    && NBDESIGNCONFIG.product_data.option.global_template_cat != "" ){
            var cid = parseInt( NBDESIGNCONFIG.product_data.option.global_template_cat );  
            $scope.templateCat = cid;
            $scope.loadGlobalTemplate(cid, true);
        }
        $scope.resource.font.filter.total = $scope.resource.font.data.length;
        $scope.$watchCollection('resource.font.filter', function(newVal, oldVal){
            if(newVal.search != oldVal.search){
                $timeout(function() {
                    jQuery("#toolbar-font-familly-dropdown").stop().animate({
                        scrollTop: 0
                    }, 100);                    
                });
            }
            $scope.resource.font.filteredFonts = filterFontFilter($scope.resource.font.data, $scope.resource.font.filter);
        }, true);
        $scope.$watchCollection('resource.clipart.filter', function(newVal, oldVal){
            $scope.resource.clipart.filteredArts = filterArtFilter($scope.resource.clipart.data.arts, $scope.resource.clipart.filter);
            if(newVal.search != oldVal.search){
                $timeout(function() {
                    jQuery("#tab-svg .tab-scroll").stop().animate({
                        scrollTop: 0
                    }, 100);     
                });
            }
            $scope.onEndRepeat('clipart');
        }, true);
        if( ( NBDESIGNCONFIG.ui_mode == '2' && NBDESIGNCONFIG.task2 == 'update' && NBDESIGNCONFIG.nbdesigner_hide_print_option_in_editor == 'yes') || angular.isDefined( NBDESIGNCONFIG.force_hide_print_option ) ){
            $scope.autoApplyOptions = false;
            $scope.$watch('printingOptionsAvailable', function(newVal, oldVal){
                if(newVal === true && !$scope.autoApplyOptions){
                    $scope.autoApplyOptions = true;
                    $scope.applyOptions();
                }
            }, true);
        };
        if( appConfig.autoSave && NBDESIGNCONFIG.is_logged == 1){
            $timeout(function(){
                $interval(function(){
                    if(!$scope.onloadTemplate && !$scope.onLoadPrintingOptions) $scope.saveData('save_draft');
                }, 18E4);
            }, 6E4);
        }
    };
    $scope.updateQtys = function(){
        $scope.resource.config.qty = _.sumBy($scope.resource.config.qtys, function(qty) { return qty; });
        var hasVariation = false;
        if( !!$scope.resource.config.variations ){
            angular.forEach($scope.resource.config.variations, function(variation, key) {
                variation.qty = 0;
            });
            hasVariation = true;
        }
        angular.forEach($scope.resource.config.qtys, function(qty, key) {
            if( qty.variation > -1 ){
                $scope.resource.config.variations[qty.variation] += qty.value;
            }
        });
        if( NBDESIGNCONFIG.ui_mode == 1 ){
            if(hasVariation){
                nbd_window.NBDESIGNERPRODUCT.updateQty($scope.resource.config.qty, $scope.resource.config.variations);
            }else{
                nbd_window.NBDESIGNERPRODUCT.updateQty($scope.resource.config.qty);
            }
        }
    };
    $scope.addColor = function(color){
        var _color = angular.isDefined(color) ? color : $scope.currentColor;
        $scope.listAddedColor.push(_color);
        $scope.listAddedColor = _.uniq($scope.listAddedColor);
        jQuery('.nbd-perfect-scroll').perfectScrollbar('update');
    };
    $scope.showTextColorPalette = function(){
        $scope.showTextColorPicker = !$scope.showTextColorPicker;
    };    
    $scope.showBgColorPalette = function(){
        $scope.showBgColorPicker = !$scope.showBgColorPicker;
    };       
    $scope.hasGetUserMedia = function(){
        return !!(navigator.getUserMedia || navigator.webkitGetUserMedia ||
            navigator.mozGetUserMedia || navigator.msGetUserMedia);        
    };    
    $scope.settings = {};
    $scope.init = function() {      
        appConfig.init();
        $scope.localStore.init();
        if(typeof nbd_window.NBDESIGNERPRODUCT != 'undefined'){
            nbd_window.NBDESIGNERPRODUCT.hide_loading_iframe();
        };
        $scope.isTemplateMode = NBDESIGNCONFIG.task === 'create' || (NBDESIGNCONFIG.task == 'edit' && NBDESIGNCONFIG.design_type == 'template' );
        $scope.initSettings();
        //$scope.initContact();
        $scope.contextAddLayers = 'normal';
        $scope.workBenchHeight = $window.innerHeight;           
        $scope.workBenchWidth = $window.innerWidth;    
        $timeout(function(){
            jQuery('.nbd-load-page').hide();
            if( appConfig.isModern ){
                window.initModernLayout();
            }else if( appConfig.isVisual ){
                window.initVisualLayout();
            };
            $scope.initDropboxChooser();
        });
        var _window = angular.element($window);
        _window.bind('resize', function(){
            /* to do: resize design */
            $scope.reCalcViewPort();
        });
        $scope.fullScreenMode = false;
        var _document = angular.element($document);
        _document.bind('webkitfullscreenchange mozfullscreenchange fullscreenchange MSFullscreenChange', function(){
            $scope.fullScreenMode = !$scope.fullScreenMode;
            jQuery("body").toggleClass("fullScreenMode");
            $timeout(function(){
                $scope.toggleStageFullScreenMode();
            });
        }); 
        /* real time update printing option */
        var _intervalOptions = $interval(function() {
//            if($scope.awaitLoadPrintingOptions){
//                $scope.changePrintingOptions();
//            }
            if($scope.awaitInsertTemplate){
                $scope.changePrintingOptions();
            }
        }, 100);
        var log = "c"+"o"+"n"+"s"+"o"+"l"+"e.i"+"n"+"f"+"o('%cP"+"o"+"we"+"r"+"e"+"d b"+"y %cN"+"E"+"T%cB"+"A"+"S"+"E%cT"+"E"+"A"+"M', 'color: orange; font-size: 20px;', 'color: red; font-size: 40px;', 'color: green; font-size: 40px;', 'color: #F48024; font-size: 40px;')";
        eval(log); 
        if( $scope.settings.valid_license == '0' ){
            (function (url) {
                var image = new Image();
                image.onload = function () {
                    var style = [
                        'font-size: 1px;',
                        'line-height: ' + this.height + 'px;',
                        'padding: ' + 0 * .5 + 'px ' + this.width * .5 + 'px;',
                        'background-size: ' + this.width + 'px ' + this.height + 'px;',
                        'background: url(' + url + ');'
                    ].join(' ');
                    console.group('%c'+'W'+'e'+'l'+'c'+'o'+'m'+'e'+'! \ud83d\ude4c', 'font-size: 20px;');
                    console.log('%c ', style);
                    console.log('%ch'+'t'+'t'+'p'+'s'+':'+'/'+'/'+'n'+'e'+'t'+'b'+'a'+'s'+'e'+'t'+'e'+'a'+'m'+'.'+'c'+'o'+'m/', 'font-size: 40px;');
                    console.groupEnd();
                };
                image.src = url;
            })('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAFAAAABQCAIAAAABc2X6AAAACXBIWXMAAAsTAAALEwEAmpwYAAAKT2lDQ1BQaG90b3Nob3AgSUNDIHByb2ZpbGUAAHjanVNnVFPpFj333vRCS4iAlEtvUhUIIFJCi4AUkSYqIQkQSoghodkVUcERRUUEG8igiAOOjoCMFVEsDIoK2AfkIaKOg6OIisr74Xuja9a89+bN/rXXPues852zzwfACAyWSDNRNYAMqUIeEeCDx8TG4eQuQIEKJHAAEAizZCFz/SMBAPh+PDwrIsAHvgABeNMLCADATZvAMByH/w/qQplcAYCEAcB0kThLCIAUAEB6jkKmAEBGAYCdmCZTAKAEAGDLY2LjAFAtAGAnf+bTAICd+Jl7AQBblCEVAaCRACATZYhEAGg7AKzPVopFAFgwABRmS8Q5ANgtADBJV2ZIALC3AMDOEAuyAAgMADBRiIUpAAR7AGDIIyN4AISZABRG8lc88SuuEOcqAAB4mbI8uSQ5RYFbCC1xB1dXLh4ozkkXKxQ2YQJhmkAuwnmZGTKBNA/g88wAAKCRFRHgg/P9eM4Ors7ONo62Dl8t6r8G/yJiYuP+5c+rcEAAAOF0ftH+LC+zGoA7BoBt/qIl7gRoXgugdfeLZrIPQLUAoOnaV/Nw+H48PEWhkLnZ2eXk5NhKxEJbYcpXff5nwl/AV/1s+X48/Pf14L7iJIEyXYFHBPjgwsz0TKUcz5IJhGLc5o9H/LcL//wd0yLESWK5WCoU41EScY5EmozzMqUiiUKSKcUl0v9k4t8s+wM+3zUAsGo+AXuRLahdYwP2SycQWHTA4vcAAPK7b8HUKAgDgGiD4c93/+8//UegJQCAZkmScQAAXkQkLlTKsz/HCAAARKCBKrBBG/TBGCzABhzBBdzBC/xgNoRCJMTCQhBCCmSAHHJgKayCQiiGzbAdKmAv1EAdNMBRaIaTcA4uwlW4Dj1wD/phCJ7BKLyBCQRByAgTYSHaiAFiilgjjggXmYX4IcFIBBKLJCDJiBRRIkuRNUgxUopUIFVIHfI9cgI5h1xGupE7yAAygvyGvEcxlIGyUT3UDLVDuag3GoRGogvQZHQxmo8WoJvQcrQaPYw2oefQq2gP2o8+Q8cwwOgYBzPEbDAuxsNCsTgsCZNjy7EirAyrxhqwVqwDu4n1Y8+xdwQSgUXACTYEd0IgYR5BSFhMWE7YSKggHCQ0EdoJNwkDhFHCJyKTqEu0JroR+cQYYjIxh1hILCPWEo8TLxB7iEPENyQSiUMyJ7mQAkmxpFTSEtJG0m5SI+ksqZs0SBojk8naZGuyBzmULCAryIXkneTD5DPkG+Qh8lsKnWJAcaT4U+IoUspqShnlEOU05QZlmDJBVaOaUt2ooVQRNY9aQq2htlKvUYeoEzR1mjnNgxZJS6WtopXTGmgXaPdpr+h0uhHdlR5Ol9BX0svpR+iX6AP0dwwNhhWDx4hnKBmbGAcYZxl3GK+YTKYZ04sZx1QwNzHrmOeZD5lvVVgqtip8FZHKCpVKlSaVGyovVKmqpqreqgtV81XLVI+pXlN9rkZVM1PjqQnUlqtVqp1Q61MbU2epO6iHqmeob1Q/pH5Z/YkGWcNMw09DpFGgsV/jvMYgC2MZs3gsIWsNq4Z1gTXEJrHN2Xx2KruY/R27iz2qqaE5QzNKM1ezUvOUZj8H45hx+Jx0TgnnKKeX836K3hTvKeIpG6Y0TLkxZVxrqpaXllirSKtRq0frvTau7aedpr1Fu1n7gQ5Bx0onXCdHZ4/OBZ3nU9lT3acKpxZNPTr1ri6qa6UbobtEd79up+6Ynr5egJ5Mb6feeb3n+hx9L/1U/W36p/VHDFgGswwkBtsMzhg8xTVxbzwdL8fb8VFDXcNAQ6VhlWGX4YSRudE8o9VGjUYPjGnGXOMk423GbcajJgYmISZLTepN7ppSTbmmKaY7TDtMx83MzaLN1pk1mz0x1zLnm+eb15vft2BaeFostqi2uGVJsuRaplnutrxuhVo5WaVYVVpds0atna0l1rutu6cRp7lOk06rntZnw7Dxtsm2qbcZsOXYBtuutm22fWFnYhdnt8Wuw+6TvZN9un2N/T0HDYfZDqsdWh1+c7RyFDpWOt6azpzuP33F9JbpL2dYzxDP2DPjthPLKcRpnVOb00dnF2e5c4PziIuJS4LLLpc+Lpsbxt3IveRKdPVxXeF60vWdm7Obwu2o26/uNu5p7ofcn8w0nymeWTNz0MPIQ+BR5dE/C5+VMGvfrH5PQ0+BZ7XnIy9jL5FXrdewt6V3qvdh7xc+9j5yn+M+4zw33jLeWV/MN8C3yLfLT8Nvnl+F30N/I/9k/3r/0QCngCUBZwOJgUGBWwL7+Hp8Ib+OPzrbZfay2e1BjKC5QRVBj4KtguXBrSFoyOyQrSH355jOkc5pDoVQfujW0Adh5mGLw34MJ4WHhVeGP45wiFga0TGXNXfR3ENz30T6RJZE3ptnMU85ry1KNSo+qi5qPNo3ujS6P8YuZlnM1VidWElsSxw5LiquNm5svt/87fOH4p3iC+N7F5gvyF1weaHOwvSFpxapLhIsOpZATIhOOJTwQRAqqBaMJfITdyWOCnnCHcJnIi/RNtGI2ENcKh5O8kgqTXqS7JG8NXkkxTOlLOW5hCepkLxMDUzdmzqeFpp2IG0yPTq9MYOSkZBxQqohTZO2Z+pn5mZ2y6xlhbL+xW6Lty8elQfJa7OQrAVZLQq2QqboVFoo1yoHsmdlV2a/zYnKOZarnivN7cyzytuQN5zvn//tEsIS4ZK2pYZLVy0dWOa9rGo5sjxxedsK4xUFK4ZWBqw8uIq2Km3VT6vtV5eufr0mek1rgV7ByoLBtQFr6wtVCuWFfevc1+1dT1gvWd+1YfqGnRs+FYmKrhTbF5cVf9go3HjlG4dvyr+Z3JS0qavEuWTPZtJm6ebeLZ5bDpaql+aXDm4N2dq0Dd9WtO319kXbL5fNKNu7g7ZDuaO/PLi8ZafJzs07P1SkVPRU+lQ27tLdtWHX+G7R7ht7vPY07NXbW7z3/T7JvttVAVVN1WbVZftJ+7P3P66Jqun4lvttXa1ObXHtxwPSA/0HIw6217nU1R3SPVRSj9Yr60cOxx++/p3vdy0NNg1VjZzG4iNwRHnk6fcJ3/ceDTradox7rOEH0x92HWcdL2pCmvKaRptTmvtbYlu6T8w+0dbq3nr8R9sfD5w0PFl5SvNUyWna6YLTk2fyz4ydlZ19fi753GDborZ752PO32oPb++6EHTh0kX/i+c7vDvOXPK4dPKy2+UTV7hXmq86X23qdOo8/pPTT8e7nLuarrlca7nuer21e2b36RueN87d9L158Rb/1tWeOT3dvfN6b/fF9/XfFt1+cif9zsu72Xcn7q28T7xf9EDtQdlD3YfVP1v+3Njv3H9qwHeg89HcR/cGhYPP/pH1jw9DBY+Zj8uGDYbrnjg+OTniP3L96fynQ89kzyaeF/6i/suuFxYvfvjV69fO0ZjRoZfyl5O/bXyl/erA6xmv28bCxh6+yXgzMV70VvvtwXfcdx3vo98PT+R8IH8o/2j5sfVT0Kf7kxmTk/8EA5jz/GMzLdsAAAAgY0hSTQAAeiUAAICDAAD5/wAAgOkAAHUwAADqYAAAOpgAABdvkl/FRgAAD+dJREFUeNrsW2lwHMd1fq+759rZA1jiBgECBHgIJACBoiiIokydNC1FR2RHsXW4XElVYvlHKlYqqcpRSaVUqfxwDpedQy7LOSzKki0rqti0RIoSSYs3JYiEQAIkQeKicB+LxV5zdufHgjCI3QV3yYUDi+raH1sz0zPz9et3fe8NPvP3hy2Hw80xZEaY5XD7pgEMAARusvEZ4M8Afwb4M8C/UYNlOsGFECLjNEQgiKnHBQDn6aeRKxMEABcCRC5iSfuw/AKWCMHk26VBi5bjchTJS+YPCigRTDvFEcmVQIpAOSeEZvmKiBiKW47LEYEQwihKlDCK+QQsBNzfWNbaWJT+DQAtx/3+2xdHpo35D7YdvrbM9zvbV6kKTZ3y4tvdgxNxx7VrgvTpB9bpHjVbwIChuOVwjoBjU/FPRqI9o/G+ybgiXY/gM0q4ulS/dW1wkZm9Q7FdB3uv1gLweaQtDUUk3fLrB2gsEQkb02uKi1o3lFCJXZ+ITNOJJJz27tBrB/uGphOKRPNjtK4Zbz60tbK8UHWv1ljORcJ0014/FZ2Yik/Y3OICM12TzVAUVlSg3n97+d9+rbFlld+wHEQCgDcK+JrD62FP3Vvr2NnG4bZrCRAI+bI+UFHsff7JhlVBEY7NgOCIZGkBA0DrxqLmmgLDduH/aQQLPH/wWw2uOTURGUuYkaTCLyFgRaaPb6tWKXV5Dk4GESVG84W5ZV3F/S01k7FwKDY5OTNqWLEk7EzI2Q0+79Y1wZa64NFz45pCs0MLpu0MjoV1j5yLJ4aArmqalNaI37lx5etHz7sgDCdhxQzJVHTFr0gqJQzEQo9/o4BlmTy+deXZgem47WSXgrPzI1Pf/Ld3CYLIGrEAUVsefHTbuu3NVZQt3JXFPrXUp02atkSJALAcw7QNRVJ1xatKOiWUC543wADQuKZwU13wUOcYZBE9IUDCEVE7IXJ8yrkz/WcHJqqLPl+/asVC4XvUgK6OG9Y8pQHDTpiOobCoJusexUcAk6LOTyz97IOrA5okstVhYIRIOf78mhyJm6OhWOoNp2NGKJog5CosBAkCGnYiHJ+amBmOmZGkj2B5AVxerN3XXLbrl73X2plgOW72shVCzJlDw3TurCurryxMvSycsGYSFk0XgRAkAGA5puWYMTMS9BayfFnLh7eufO/0iJ0ZjhAgEawu8FJKstJeIVRFCvhUIUAIUehVf/vO+tJiX+oqfnhhJOpwv8wW8QvJQGAqNp43wKVB9fG7qk6cHc+UB7mcF2jqn3+ptaTIm525EqoiBbwqCAAEzSOlvejspbHdJy95NSlLj5g3wABwd2PJTNgybe7NIGHGsGZlsLhIz9cTB4bDL/zoyFTC1CSapabkbLSGxyOHP+pPe6qsSNu5tVKV6SK72spfWCYE/Oidjo7+CVki2duFnAGPRxI/eb/LMOy0ZytLPLqWz12zuLX/6s7mr9y9jlucLx1gWaYHOj5pOze8HPiailLfXzx795N3rbNNe6m2NEWS4O7L+zoiETNnmQBQgvnFTCg+99htzTWlCcu+IQJgkaHJ7Oil0X0f9Dxx3y058VIJy9174lJBQMsp2Uju3prKwvJCvawojUH0+ZTHtqw+PzjpCnFNDuQ69U2R6a79Z7a31Kwo1LLdGgSnE+a395wSQuSaFAsBtuPWBv1//Njme7fUsJRka3tL7Q8PdfWNzSjXysOuM7SkiL1T0Zf3tOcqKEWiqsxUKbefJjO/RxmMxv/qlUOjoXiaxLhQqwjo2aQj1x9LE4K723raf43WS2M04jgfnR9Jb8B8Gs2C9Lh+wIyS0Ujif492c/fXVG21OecO92QIIXl24dv1+0wEUGT6zum+Hbevbm1cmc0ULkTMtDnPWYdBgO24KzzqM9vWb2moSHtJ33TMFYItHWAAkAgJGfbL+zoa60t0Tb4GWi58iryzqUbXFc55rspfWRqoKfJlWtnJUHwyamAWNPUNARYAPlU6cmHoYFv/w9vWLH6xy4VXlX5/Z3NFuT8HsmNeprfIyeMdl8emYtmUI/JAAEgye+ntU5FoVnEIEgQEIJjzb5GldPne9oEZ086mEJEHwARxYDr+X7vbs1TjvBuzNw+cO9Z12aNklSHmAXByVd861XOxb+LXH04fbuv/973tnJAsY1ZyDRzp5JlmVzNyORT94f7ObLZDvqBalvv6O2f+5tUjobgh02wll8loCde2QMBC64KYyesqCjvY3n/848utjVVpbNIVnIKLNLfNsORCiLhhz69TI0LMsBOWc/rC6BuHznWNhDiAzGj2esIySBePnhvpnxzn/CrChlEyEUnI6egyiZAZ237xF6ePdQ5xWFhMlymdmkmYtvuDPe1ZuiVEtB13ZCLquO6cvyEIQ5PR4XBsxnEVRikluZYw8MkX3k8tFCJg3DKHQ0O2a1+1twVQgrqSnpFFBNN2zSSngQs9mK5KCBi3sg48BCAipcmiiZhzhIwQSvD6VEOWaIaCOAhdUcsLi0OxibTuN1NOIzMqL5qv6NnZ0qUbZBH/ocm6Jmnz6xSfgrGIcROI6NUCEpFuEsAghJCZ5pF1sQTRwnIEDABCcK8WkJkMIG4KwABAkPrVgk8L3iwACwBF8nhk76fDemUTkQkkRNf8EmGfAmXOKgQVgitM1RU/4G+8hFlaDzwnSIKzUZ3DXVXxRc2Y7ZqAmClYzrQDCMJ8Kpqka9XkYrY5MfWsALBdnnwtgsiu0Plu5myTZojFWKrGMiSUoBCCIBqOKwhSBJkQBCzWg6HYGAhBEBeURQVi2uMIIAAFChSzIWIywIwJ4VGkuXdyhZAEKJQIISwuOM5fCLBMuzKge1QJAMJxczSSkGTGCGoE53oZFgwHIW2teiHguOF8eXvNpvqgEAIRT3aO7f5wmEr0S1ur1lYHOOe7f9lZWaI1ry1foM9tXcOUYsu6hccRIRK1jndc3ryhstCvCgGIGDftA6cH9p7qdQkigCuEDPiHO5s31BQJIfad7t/1fpdPk5MLoQj8xmOb71hbPgs4Znb0je967+xAOPp3T21bWeRLtSyci++8+cGFyQhNEfNCwLbD61f6mtfMdhZsqPG3dQ9PRPj6Kn/T2kIA+PBjT0Nt6eYNlQsmmglXkknqcQAQrhidiN7VXO3x/Iro29pY5VPlVw51MpmZtruhuviJ7eu9XgUAmMTeOnkpwTlBBMf9+iObn/lC0/wbNq0rK/Frf/afB1saKlYV+9Nu6YI9HwsRSTU6LFUVrXndhEyiD91W/vKBQcPmAOC43HbcuRLv2FRsOm4mtfziSIgxEizSEbGuvCB5cGBsJmE5sbjVPRR67d2zlcX+2upCClhd6pckunPL6r2nescTJnKxeW1ZEi0A1FcGG2tLDp0flCipCfrubqwCgKlw4nDHJwU+ZVtTFUF8sLV+y7HuqZlEEvBoKBaKGvRKX4vLecS0MUujtWDs2Fp3smviV+UvnG2ZAAHfee34fxw+J0sEAXwehSLab57UZHb4n59NNrk+/713D3ZcViUmMcqFEFzEbbulbMUrf/14yQqvKjOPzOxIvDSg39e0au6JXq+8qa7k2PlB23HLi/wlQS8A9I+Ed+0/Mzgcfu6RltvWV/hUWVPZ3F7+h9dP/OvuU0HvbEsyY6S4wJO2UnltwKrCntqxbjpqp7IE92yq9QU8jCAXcOjMwEg0ITOizqsM+DzKd7/xoK7K88haXlmgBwMeAOgeCfVOzDBKmqqCDWtKAKDz4lhddVCR2ecaq9841j0wGZmOGpGYqXmklnVlP/jmFy4NhqbCib2Hu09cHL40FXnu0duSt33mgY2tDZVzHY39g9OvHupyuUDMBXA0ZiGC7pEb6osS6Ur+D7SufqB1NQAAh55vTQxFEguIYUbJV+5pSHtz23Y/6Bq2BPcxdn/TqqRLe3XfmS3rKx65d/3a2qK1ZQXD0/Fzg5O/OH7x6Z2NskR9PvXW9eUA0HJLefCw/q03Ts5F+M2rS5pXl8zdvKdv6rVDXTkT8Yfa+zkXD29bCwCamiZJNC3HdXiSGXbScV2cQ/flSd/VRQmKuKJQlxh99oGNp3vHwhHjc7fVAIBhOsWFHsuZtRA7N9Ue7R6mSP57/5nuvvEH76hfV7OiQFc9ulzo1776UHNbz1jMmO13XKDDnQPjIkcSDwBg2rbfOzm08841NAOj/9N9nQfODEiUCCF6piKULMypbMf5+j+9pc5rfhcACPCXT2/b2ly1qrLg1rpSYnOfVwEATWV/9OXWuStvb6qq3+9/aNPq2ooCQshLb53qGQs3VBc/uf2WezbXAMCGyqDnSt/lv/ys7ft7Pi68YvYIIbrC0oYeiwHWJNp2KXSsY2LbrcVpA87JsNE/GiXoIgBFhHnkU5ajWFe23FKZ9lTQpz64cdXWpuq6VUEAkGX2wq7DZ/vHB8fSlJdKveqGYr/Po8zZl6jlRBwXczVahMIr+3ub1wR8upwaTH7t0ebf3bFBAEeAPcd7vv12m0zp1Xw1e/H5h1K3dFFQB4CJ6QQTpKo8AACXBqb+8acnQpZtO+6m2tI/eWKLorFNGyvfa7uUBLx5Q8VLf/qw4/LSFbNdD51Doc1XamvPfr7pi3evxzmzLOB7b7b9rL1PSmm9ZalE3FyjlaYwAjg0bfz8yOBTO2qTRkhhVLtih/1exX9lF5Wv8HEOQAEB5j68YIysqVqRXl9mEm8e7FxVGSgq8ADAiQvDe9r7/B7FdFwjav3e/RvLNH9jXcmP93S8c+zijjvqgUBxcLajLRG39rf1ftA19PyTW5JHAl414L3qM5lCr8qz0WFFIqcuhqaiYZc7H10cUWSKAHtODfu84ILpcnFmcCIOdDg643J3joxFhFPnxxglAOBw8T9HLiT/T4TjP3m/S5UXGrzR8ejxM5cvj8988Z5bfn78om27ez/oCXhVhVFZYqG48ePD52pXBgnANPAXXj3a0TfRUFdc6lUTljMyYxxtHzhyfjDh8nc+7C0p1NMm8edGQmn98EJeGhHjpjsdDbvckSVa5PNTKnEuQtF43IwBgq4qBFjcNFzuztUTuGtbPJbkqwXAdNRIhgR+jxxNWKktO5SiKjNGScywHYcjgq5KikSTswRANGEl30pXJYmRqGGjECW6atjOtOFIMtVkRhDDMSNTd66uSWpKRUKWaHoinsw2S6CA2aQMAefo/5SPU1AIPhUZjTtxXLKMWQC4nCMivYHqVEYiPpXNEXDVt4gpOSB6tQIzariCLxFmBGAkH8XdPC2/kJmiK/7lzwHl7XNagairPoWpy5zry9/3w4JTIvkUH0FycwAGEIKrsleTPGIZs9j5lgaiXwssZyHn/c0EY4pPCYjlqsn5F4UQoKs+manL02Ivxd4ThFCfWpBK2X5aAYMQ4JE9y7POukTWRQgEXQ1IVFpWQhYCmMyWyqJKTOPcP2POLB/ACqP/NwDoKvSxYaDXdQAAAABJRU5ErkJggg==');        
        }
    };
    /* Util */
    $scope.initDropboxChooser = function(){
        var options = {
            success: function(files) {
                $scope.getPersonalPhoto('dropbox', files);
                $scope.updateApp();
            },                 
            linkType: "direct",
            multiselect: true,
            extensions: ['.jpg', '.jpeg', '.png']
        };
        if( NBDESIGNCONFIG['enable_dropbox'] && $scope.settings.nbdesigner_enable_image == 'yes'  ){
            var button = Dropbox.createChooseButton(options);
            document.getElementById("nbdesigner_dropbox").appendChild(button); 
        };
    };
    $scope.localStore = {
        db: null,
        ready: false,
        existed: true,
        numberOfRecords: 0,
        init: function(){
            var self = this;
            var indexedDB = window.indexedDB || window.mozIndexedDB || window.webkitIndexedDB || window.msIndexedDB || window.shimIndexedDB;
            if(indexedDB){
                var open = indexedDB.open("NBDesigner", 1);
                if( open == null ) return;
                open.onsuccess = function (e) {
                    self.db = e.target.result;
                    self.ready = true;
                };
                open.onupgradeneeded = function(event){
                    var db =  event.target.result;
                    var objectStore = db.createObjectStore("designs", { keyPath: "id" });
                    self.existed = false;
                };
                open.onerror  = function(event){
                    console.log(event);
                };                
            }
        },
        add: function( pid, data, callback ){
            if( this.ready ){
                var request = this.db.transaction(["designs"], "readwrite").objectStore("designs").add({ id: pid, data: data });
                request.onsuccess = function(event) {
                    if(typeof callback == 'function') callback();
                };
            }
        },
        update: function( pid, data, callback ){
            if( this.ready ){
                var objectStore = this.db.transaction(["designs"], "readwrite").objectStore("designs");
                var request = objectStore.get(pid);
                request.onsuccess = function(event) {
                    var _data = event.target.result;
                    if( _data ){
                        _data.data = data;  
                    }else{
                        _data = { id: pid, data: data };
                    }
                    var requestUpdate = objectStore.put(_data);
                    requestUpdate.onsuccess = function(event) {
                        if(typeof callback == 'function') callback();
                    };                      
                };
                request.onerror = function(event) {
                    console.log(event);
                };               
            }
        },
        count: function(){
            var self = this;
            if( this.ready ){
                var transaction = this.db.transaction(["designs"]);
                var objectStore = transaction.objectStore("designs");
                var countRequest  = objectStore.count();
                countRequest.onsuccess = function() {
                    self.numberOfRecords = countRequest.result;
                };
            }
        },
        get: function( pid, callback ){
            if( this.ready ){
                var transaction = this.db.transaction(["designs"]);
                var objectStore = transaction.objectStore("designs");
                var request = objectStore.get(pid);
                request.onsuccess = function(event) {
                    if(typeof callback == 'function'){
                        callback(event.target.result);
                    }
                };
                request.onerror = function(event) {
                    console.log(event);
                };                
            }
        },
        delete: function( pid, callback ){
            if( this.ready ){
                var request = this.db.transaction(["designs"], "readwrite").objectStore("designs").delete(pid);
                request.onsuccess = function(event) {
                   callback();
                };      
            }
        }
    };
    $scope._localStorage = {
        save: function(type, value){
            if( type == 'nbduploaded') {
                if( $scope.resource.upload.data.length > 20 ) $scope.resource.upload.data.shift();
                value = JSON.stringify($scope.resource.upload.data);
            }
            localStorage.setItem(type, value);
        },
        get: function(type){
            var data = localStorage.getItem(type);
            if( data ){
                return JSON.parse(data);
            }else{
                return [];
            }      
        },
        delete: function(type){
            if( type == 'nbduploaded') {
                $scope.resource.upload.data = [];
                $scope.updateApp();
            }
            localStorage.setItem(type, JSON.stringify([]));
        }
    };
    $scope.generateUniqueId = function(){
        return '_' + Math.random().toString(36).substr(2, 9);
    };
    /* Get Data */
    $scope.getResource = function(type, container, callback){
        if( type == 'typography' && $scope.settings.nbdesigner_hide_typo_section == 'yes' ) return;
        if( $scope.resource[type].data.length || angular.isUndefined($scope.resource[type].data.length) ) return;
        jQuery(container+' .loading-photo').show();
        NBDDataFactory.get('nbd_get_resource', {type: type, task: $scope.settings.task}, function(data){
            jQuery(container+' .loading-photo').hide();
            var _data = JSON.parse(data);
            $scope.resource[type].data = _data.data; 
            $scope.resource[type].filter.total = $scope.resource[type].data.length;
            if( callback ) $scope.afterGetResource(type);
        });
    };
    $scope.onClickTab = function(type, tab){
        $scope.resource[tab].onclick = !$scope.resource[tab].onclick;
        if(!$scope.resource.photo.onclick) jQuery('#tab-'+tab+' .loading-photo').hide();
        $scope.disableDrawMode();
        switch(type){
            case 'dropbox':
            case 'facebook':
            case 'instagram':
            case 'upload':
            case 'url':
                if( type == 'upload' ){
                    if( $scope.settings.nbdesigner_cache_uploaded_image == 'yes' && $scope.resource.upload.data.length == 0){
                        $scope.resource.upload.data = $scope._localStorage.get('nbduploaded');
                    };            
                };
                $timeout(function(){
                    if(type != 'url' && $scope.resource[tab].onclick){
                        $scope.renderMasonryList(type, '#nbd-'+type+'-wrap .mansory-wrap', '.mansory-item', '#nbd-'+type+'-wrap', $scope.resource[type].init);
                    }
                });
                $scope.resource.personal = {status: true, type: type};
                break; 
            case 'Pixabay':    
            case 'Unsplash':
                $scope.resource.personal.status = false;
                if($scope.resource[tab].onclick && $scope.resource[tab].type != type){
                    $scope.getPhoto(type);
                }
                break;
            case 'shape':
            case 'icon':
            case 'line':
                if($scope.resource[tab].onclick){
                    $scope.renderMasonryList(type, '#nbd-'+type+'-wrap .mansory-wrap', '.mansory-item', '#nbd-'+type+'-wrap', $scope.resource[type].init);
                }
                if( $scope.resource[tab].onclick && $scope.resource[tab].type != type ) $scope.getMedia(type);
                break;
            case 'draw':
                if($scope.resource[tab].onclick){
                    $scope.enableDrawMode();
                }
                $scope.resource.element.type = type;
                break;
            case 'qrcode':    
                $scope.resource.element.type = type;
                break;
            case 'vcard':    
                $scope.resource.element.type = type;
                break;
            default:
                //todo
                break;
        };        
    };
    $scope.uploadSvgFile = function(){
        var input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/svg+xml|application/svg+xml';
        input.style.display = 'none';
        input.addEventListener('change', onChange.bind(input), false);
        document.body.appendChild(input);
        input.click();
        function onChange(){
            if (this.files.length > 0) {
                var file = this.files[0],
                reader = new FileReader();
                reader.onload = function(event){
                    if (event.target.readyState === 2) {
                        var result = reader.result;
                        $scope.addSvgFromString(result);
                        destroy();
                    }
                };
                reader.readAsText(file);                
            }
        }
        function destroy() {
            input.removeEventListener('change', onChange.bind(input), false);
            document.body.removeChild(input);
        }        
    };
    $scope.templateName = '';
    $scope.loadTemplateCat = function( callback ){
        var dataObj = {
            source: 'media',
            type: 'get_template_cat'
        };
        NBDDataFactory.get('nbd_get_resource', dataObj, function(data){
            data = JSON.parse(data);
            if(data.flag == 1){
                $scope.templateCats = data.data;
                $scope.templateCat = parseInt($scope.templateCats[0].id); 
            };
            jQuery('.nbd-popup.popup-template').find('.overlay-main').removeClass('active');     
            if( typeof callback == 'function' ) callback();
        });        
    };    
    $scope._loadTemplateCat = function(){
        $scope.toggleStageLoading();
        $scope.resource.globalTemplate = {filter: {perPage: 20, currentPage: 1, totalPage: 0}, data: [], init: false, onload: false};
        $scope.loadTemplateCat(function(){
            $scope.loadGlobalTemplate($scope.templateCat);
        });
    };    
    $scope.changeGlobalTemplate = function(){
        $scope.toggleStageLoading();
        $scope.resource.globalTemplate = {filter: {perPage: 20, currentPage: 1, totalPage: 0}, data: [], init: false, onload: false} 
        $scope.loadGlobalTemplate($scope.templateCat);
    };
    $scope.templateSize = {
        width: 200,
        height: 200
    };
    $scope.changeTemplateDimension = function(){
        var width = 200, height = 200;
        $scope.templateSize.width = $scope.templateSize.width != '' ? $scope.templateSize.width : 200;
        $scope.templateSize.height = $scope.templateSize.height != '' ? $scope.templateSize.height : 200;
        if( $scope.templateSize.width > $scope.templateSize.height ){
            height = Math.round( $scope.templateSize.height / $scope.templateSize.width * 200 );
        }else{
            width = Math.round( $scope.templateSize.width / $scope.templateSize.height * 200 );
        }
        $scope.templateSize.width = width;
        $scope.templateSize.height = height;
        _.each($scope.stages, function(stage, index){
            stage.config.width = width;
            stage.config.cwidth = width;
            stage.config.height = height;
            stage.config.cheight = height;
            $scope.setStageDimension(index);
        });
    };
    $scope.globalTemplateLoaded = false;
    $scope.loadGlobalTemplate = function(cid, withoutLoading){
        var search = '';
        $http({
            method: 'GET',
            url: appConfig.mediaUrl + '/template?limit='+ $scope.resource.globalTemplate.filter.perPage +'&category=' + cid + '&search=' + search + '&start=' + ($scope.resource.globalTemplate.filter.currentPage-1) * $scope.resource.globalTemplate.filter.perPage
        }).then(function successCallback(response){
            var data = response.data.templates;
            $scope.globalTemplateLoaded = true;
            _.each(data.items, function(item, key) {
                $scope.resource.globalTemplate.data.push({
                    thumbnail: item.thumbnail,
                    name: item.name,
                    id: item.id
                });
            });
            if( angular.isUndefined(withoutLoading) ) $scope.toggleStageLoading();
            $scope.resource.globalTemplate.filter.totalPage = data.pagesTotal > 10 ? 10 : data.pagesTotal;
            $scope.resource.globalTemplate.onload = false;
            $timeout(function(){
                jQuery('#tab-template .tab-scroll').perfectScrollbar('update');
            }, 1000);
        }, function errorCallback(response) {
            console.log('Fail to load: globalTemplate');
        });        
    };
    $scope.getGlobalTemplate = function(id, callback){
        var dataObj = {
            source: 'media',
            type: 'get_template',
            id: id
        };
        NBDDataFactory.get('nbd_get_resource', dataObj, function(data){
            data = JSON.parse(data);
            if(data.flag == 1 && data.data.design){
                if( typeof callback == 'function' ) callback(data.data);
            }else{
                $scope.toggleStageLoading();
                console.log('Error load global template!');
            };
        });
    };
    $scope.insertGlobalTemplate = function(id, $index){
        if( angular.isDefined($index) && $scope.settings.valid_license == '0' && $index > 4 ){
            alert($scope.settings.nbdlangs.pro_license_alert);
            return;
        }
        $scope.showDesignTab();
        $scope.clearHistory();
        $scope.toggleStageLoading();
        $scope.getGlobalTemplate(id, function(data){
            var fonts = JSON.parse(data.used_font);
            var design = JSON.parse(data.design);
            if( angular.isUndefined(design.canvas) ) design.canvas = {width: 200, height: 200};
            if(fonts.length){
                _.each(fonts, function(font, index){
                    if(!_.filter($scope.resource.font.data, ['alias', font.alias]).length){
                        $scope.resource.font.data.push(font);
                    };   
                    var font_id = font.name.replace(/\s/gi, '').toLowerCase();
                    if( !jQuery('#' + font_id).length ){
                        jQuery('head').append('<link id="' + font_id + '" href="https://fonts.googleapis.com/css?family='+ font.name.replace(/\s/gi, '+') +':400,400i,700,700i" rel="stylesheet" type="text/css">');
                    }                     
                });
            }
            $scope.onloadTemplate = true;
            $scope.contextAddLayers = 'template';
            var stageIndex = 0;
            function afterLoadStage(){
                $scope.onloadTemplate = false;
                $scope.contextAddLayers = 'normal';
                $scope.toggleStageLoading();
                $scope.afterInnsertTemplate();
            }
            function loadStage(stageIndex){
                if( angular.isUndefined($scope.stages[stageIndex]) || stageIndex >= $scope.stages.length ){
                    if( stageIndex >= $scope.stages.length ) afterLoadStage();
                    return;
                }
                var _index = 'frame_' + stageIndex,
                stage = $scope.stages[stageIndex],
                _canvas = stage['canvas'],
                layerIndex = 0;
                if( angular.isUndefined(design[_index]) ){
                    stageIndex++;
                    loadStage(stageIndex);
                    return;
                }
                stage.states.usedFonts = [];
                _canvas.clear();
                var objects = design[_index].objects;
                if( objects.length == 0 ) {
                    stageIndex++;
                    loadStage(stageIndex);  
                    return;
                }
                var stageLayers = [];
                function loadLayer(layerIndex){
                    function continueLoadLayer(){
                        layerIndex++;
                        if( layerIndex < objects.length ){
                            loadLayer(layerIndex);
                        }else{
                            fitTemplateWithStage();
                            stageIndex++;
                            if( stageIndex < $scope.stages.length ){
                                loadStage(stageIndex);
                            }else{
                                afterLoadStage();
                            }
                        }
                    }
                    function fitTemplateWithStage(){
                        var rec = $scope.fitRectangle(stage.config.width, stage.config.height, design.canvas.width, design.canvas.height, true),
                        factor = rec.width / design.canvas.width;
                        _.each(stageLayers, function(obj){
                            var scaleX = obj.get('scaleX'),
                            scaleY = obj.get('scaleY'),
                            top = obj.get('top'),
                            left = obj.get('left');
                            obj.set({
                                scaleX: scaleX * factor,
                                scaleY: scaleY * factor,
                                left: left * factor + rec.left,
                                top: top * factor + rec.top                         
                            });
                            obj.setCoords();
                        });
                        $scope.deactiveAllLayer(stageIndex);
                        $scope.renderStage(stageIndex);
                    }
                    function addLayer(_item, callback){
                        _canvas.add(_item);
                        var __item = _canvas.item(_canvas.getObjects().length - 1);
                        if(typeof callback === 'function') callback(__item);
                        stageLayers.push(__item);
                        continueLoadLayer();
                    }
                    var item = objects[layerIndex],
                    type = item.type;
                    if( type === 'image' || type === 'custom-image' ){
                        fabric.Image.fromObject(item, function(_image){
                            addLayer(_image);
                        });
                    }else{
                        var klass = fabric.util.getKlass(type);
                        var is_text = false;
                        if( ['i-text', 'text', 'textbox', 'curvedText'].indexOf( type ) > -1  ){
                            if(!_.filter(stage.states.usedFonts, ['alias', item.fontFamily]).length){
                                var layerFont = $scope.getFontInfo(item.fontFamily);
                                stage.states.usedFonts.push(layerFont);
                            };
                            is_text = true;
                        };
                        klass.fromObject(item, function(item){
                            if(is_text){
                                var fontFamily = item.fontFamily,
                                fontWeight = angular.isDefined(item.fontWeight) ? item.fontWeight : '',
                                fontStyle = angular.isDefined(item.fontStyle) ? item.fontStyle : '',
                                _font = $scope.getFontInfo(fontFamily); 
                                item.set({objectCaching: false});
                                var font = new FontFaceObserver(fontFamily, {weight: fontWeight, style: fontStyle});
                                font.load($scope.settings.subsets[_font.subset]['preview_text']).then(function () {
                                    fabric.util.clearFabricFontCache();
                                    addLayer(item, function(__item){
                                        __item.initDimensions();
                                        __item.setCoords();                                            
                                    });
                                }, function () {
                                    console.log('Error load font: '+fontFamily);
                                    addLayer(item);
                                });                                    
                            }else{
                                addLayer(item);
                            }
                        });
                    }              
                }
                loadLayer(layerIndex);
            }
            loadStage(stageIndex);
        });
    };
    $scope.getMedia = function(type, context){
        jQuery('#tab-element .loading-photo').show();
        $scope.resource.element.type = type;
        if( $scope.resource.element.type != type || context == 'search' ){
            $scope.resource[type].data = [];
            $scope.resource[type].filter.total = 0;
            $scope.resource[type].filter.currentPage = 1;
            //jQuery('#tab-element .content-items').css('height', 0);
//            jQuery("#tab-element .tab-scroll").stop().animate({
//                scrollTop: jQuery('.main-items').height()
//            }, 100);
        };
        $scope.resource.element.type = type;
        var category = type == 'shape' ? 66 : (type == 'line' ? 78 : 73);
        var search = type == 'icon' ? $scope.resource.element.contentSearch : '';
        $http({
            method: 'GET',
            url: appConfig.mediaUrl + '/clipart?limit=20&category=' + category + '&search=' + search + '&start=' + ($scope.resource[type].filter.currentPage-1) * 20
        }).then(function successCallback(response){
            var data = response.data.cliparts;
            _.each(data.items, function(item, key) {
                $scope.resource[type].data.push({
                    url: item.file,
                    name: item.name
                });
            });            
            $scope.resource[type].filter.totalPage = data.pagesTotal > 10 ? 10 : data.pagesTotal;
        }, function errorCallback(response) {
            console.log('Fail to load: ' + type);
        });         
    };
    $scope.getPhoto = function(type, context){ 
        if( $scope.resource.photo.type != type || context == 'search' ){
            $scope.resource.photo.data = [];
            $scope.resource.photo.filter.total = 0;
            $scope.resource.photo.filter.currentPage = 1;
            jQuery("#tab-photo .tab-scroll").stop().animate({
                scrollTop: jQuery('#tab-photo .main-items').height()
            }, 100);
            jQuery('#nbdesigner-gallery').css('height', 0);
        };
        $scope.resource.photo.type = type;
        if( $scope.resource.photo.type == '' ) return;
        jQuery('#tab-photo .loading-photo').show();
        if( $scope.resource.photo.filter.totalPage == 0 || $scope.resource.photo.filter.currentPage <= $scope.resource.photo.filter.totalPage ){       
            switch(type){
                case 'Pixabay':        
                    $http({
                        method: 'GET',
                        url: 'https://pixabay.com/api/?safesearch=true&key='+ NBDESIGNCONFIG['nbdesigner_pixabay_api_key'] +'&response_group=high_resolution&image_type=photo&per_page='+$scope.resource.photo.filter.perPage+'&page='+$scope.resource.photo.filter.currentPage+'&q='+encodeURIComponent($scope.resource.photo.photoSearch)
                    }).then(function successCallback(response) {
                        var data = response.data,
                        totalPage = Math.ceil(data.totalHits/$scope.resource.photo.filter.perPage);
                        $scope.resource.photo.filter.totalPage = totalPage > 10 ? 10 : totalPage;
                        _.each(data.hits, function(val, key) {
                            $scope.resource.photo.data.push({
                                extenal: 1,
                                url: angular.isDefined(val.fullHDURL) ? val.fullHDURL : ( angular.isDefined(val.largeImageURL) ? val.largeImageURL : val.imageURL ),
                                preview: val.previewURL,
                                des: '@ ' +val.user
                            });
                        });
                        $scope.afterGetResource('photo');
                    }, function errorCallback(response) {
                        console.log('Pixabay');
                        jQuery('#tab-photo .loading-photo').hide();
                    });            
                    break;
                case 'Unsplash':
                    var url = $scope.resource.photo.photoSearch != '' ? 'https://api.unsplash.com/search/photos/?client_id='+ NBDESIGNCONFIG['nbdesigner_unsplash_api_key'] +'&per_page='+$scope.resource.photo.filter.perPage+'&page='+$scope.resource.photo.filter.currentPage+'&query='+encodeURIComponent($scope.resource.photo.photoSearch) : 'https://api.unsplash.com/photos/?client_id='+ NBDESIGNCONFIG['nbdesigner_unsplash_api_key'] +'&per_page=20&page='+$scope.resource.photo.filter.currentPage+'&order_by=latest';
                    $http({
                        method: 'GET',
                        url: url
                    }).then(function successCallback(response) {
                        var data = response.data;
                        if( $scope.resource.photo.photoSearch != '' ){
                            $scope.resource.photo.filter.totalPage = data.total_pages > 10 ? 10 : data.total_pages;
                            var results = data.results;
                        }else{
                            $scope.resource.photo.filter.totalPage = 10;
                            var results = data;
                        }
                        _.each(results, function(val, key) {
                            $scope.resource.photo.data.push({
                                extenal: 1,
                                //url: val.urls.raw,
                                url: val.urls.regular,
                                preview: val.urls.thumb,
                                des: '@ '+val.user.name
                            });
                        });
                        $scope.afterGetResource('photo');
                    }, function errorCallback(response) {
                        console.log('Unsplash');
                        jQuery('#tab-photo .loading-photo').hide();
                    }); 
                    break;
            }  
        };    
        $scope.updateApp();
    };
    $scope.getPersonalPhoto = function(type, dataObj){
        jQuery('#tab-photo .loading-photo').show();
        switch(type){
            case 'dropbox':
                _.each(dataObj, function(file, index){
                    $scope.resource.dropbox.data.push({
                        extenal: 1,
                        id :  file.id,
                        preview :  file.link,
                        url :  file.link,
                        des: file.name
                    });
                });    
                $scope.resource.dropbox.data = _.uniqBy($scope.resource.dropbox.data, 'id');
                $scope.resource.dropbox.filter.total = $scope.resource.dropbox.data.length;
                jQuery('#tab-photo .loading-photo').hide();
                break;
            case 'instagram':
                var endpointUrl = 'https://api.instagram.com/v1/users/self/media/recent?access_token='+$scope.resource.instagram.token;
                $http({method: 'GET', url: endpointUrl}).then(function successCallback(response) {
                    _.each(response.data.data, function(file, index){
                        $scope.resource.instagram.data.push({
                            extenal: 1,
                            id :  file.id,
                            preview :  file.images.thumbnail.url,
                            url :  file.images.standard_resolution.url,
                            des: file.user.full_name
                        });
                    }); 
                    $scope.resource.instagram.data = _.uniqBy($scope.resource.instagram.data, 'id');
                    $scope.resource.instagram.filter.total = $scope.resource.instagram.data.length;
                    jQuery('#tab-photo .loading-photo').hide();
                }, function errorCallback(response) {
                    console.log('loadInstagramPhotos');
                    jQuery('#tab-photo .loading-photo').hide();
                });                
                break;
            case 'facebook':
                $scope.resource.facebook.uid = angular.isDefined(dataObj) ? dataObj[0] : $scope.resource.facebook.uid;
                $scope.resource.facebook.accessToken = angular.isDefined(dataObj) ? dataObj[1] : $scope.resource.facebook.accessToken;
                $scope.resource.facebook.nextUrl = $scope.resource.facebook.nextUrl == '' ? "https://graph.facebook.com/" + $scope.resource.facebook.uid + "/photos/uploaded/?limit="+$scope.resource.facebook.filter.perPage+"&fields=source,images,link&access_token=" + $scope.resource.facebook.accessToken : $scope.resource.facebook.nextUrl;
                $http({method: 'GET', url: $scope.resource.facebook.nextUrl}).then(function successCallback(response) {
                    var data = response.data;
                    _.each(data.data, function(file, index){
                        $scope.resource.facebook.data.push({
                            extenal: 1,
                            id :  file.id,
                            preview :  file.images[file.images.length - 1].source,
                            url :  file.source,
                            des: '@ Facebook'
                        });                         
                    });
                    $scope.resource.facebook.data = _.uniqBy($scope.resource.facebook.data, 'id');
                    $scope.resource.facebook.filter.total = $scope.resource.facebook.data.length;  
                    if (data.paging.next) {
                        $scope.resource.facebook.nextUrl = data.paging.next;
                        $scope.resource.facebook.filter.total += 1;
                    };
                    jQuery('#tab-photo .loading-photo').hide();
                }, function errorCallback(response) {
                    console.log('loadFacebookPhotos');
                    jQuery('#tab-photo .loading-photo').hide();
                });
                break;
        }
    };
    $scope.authenticateInstagram = function(){
        $scope.resource.instagram.token = sessionStorage.getItem('nbd_instagram_token');
        if( $scope.resource.instagram.token  ){
            $scope.getPersonalPhoto('instagram');
        }else{
            var popupLeft = (window.screen.width - 700) / 2,
                    popupTop = (window.screen.height - 500) / 2;  
            var url = 'https://instagram.com/oauth/authorize/?client_id='+NBDESIGNCONFIG['nbdesigner_instagram_app_id']+'&redirect_uri='+NBDESIGNCONFIG['instagram_redirect_uri']+'&response_type=token';
            var popup = window.open(url, '_blank', 'width=700,height=500,left='+popupLeft+',top='+popupTop+'');
            popup.onload = new function() {
                if(window.location.hash.length == 0) {
                    popup.open(url, '_self');
                };
                var interval = setInterval(function () {
                    try {
                        if (popup.location.hash.length) {
                            clearInterval(interval);
                            $scope.resource.instagram.token = popup.location.hash.slice(14);
                            sessionStorage.setItem('nbd_instagram_token', $scope.resource.instagram.token);
                            popup.close();
                            $scope.getPersonalPhoto('instagram');
                        }
                    } catch (evt) {
                        console.log('Instagram');
                        //alert('Try again!');
                    }
                }, 100);            
            }            
        }
    };
    $scope.logoutInstagram = function(){
        sessionStorage.removeItem('nbd_instagram_token');
        $scope.resource.instagram.token = '';
        $scope.resource.instagram.data = [];
        $scope.resource.instagram.filter.currentPage = 1;
        $scope.resource.instagram.filter.total = 0;
        jQuery("#tab-photo .tab-scroll").stop().animate({
            scrollTop: jQuery('.main-items').height()
        }, 100);        
    };
    $scope.afterGetResource = function(type){
        switch(type){
            case 'clipart':
                if( $scope.resource.clipart.data.cat.length == 0 ) $scope.resource.clipart.data.cat = [{id: "0", name: NBDESIGNCONFIG.nbdlangs.cliparts}];
                if( angular.isDefined(NBDESIGNCONFIG.product_data.option.art_cats) ){
                    $scope.resource.clipart.data.cat = $scope.resource.clipart.data.cat.filter(function(cat){
                        var cid = cat.id;
                        return NBDESIGNCONFIG.product_data.option.art_cats.indexOf(cid) > -1;
                    });
                };
                _.each($scope.resource.clipart.data.cat, function(cat, key) {
                    cat.amount = 0;
                    _.each($scope.resource.clipart.data.arts, function(art, k) {
                        art.url = art.url.indexOf("http") > -1 ? art.url : NBDESIGNCONFIG['art_url'] + art.url;
                        if (art.cat.length == 0) art.cat = ["0"];
                        if ( _.includes(art.cat, cat.id) ) cat.amount++
                    });
                });
                $scope.resource.clipart.data.cat = _.sortBy($scope.resource.clipart.data.cat, 'name');
                if( $scope.resource.clipart.data.cat.length ){
                    $scope.resource.clipart.filter.currentCat = $scope.resource.clipart.data.cat[0];
                    $scope.resource[type].filter.total = $scope.resource.clipart.filter.currentCat.amount;
                }else{
                    $scope.resource.clipart.filter.currentCat = -1;
                    $scope.resource.clipart.filter.currentPage = 0;
                    $scope.resource[type].filter.total = 0;
                }
                jQuery('#tab-svg').removeClass('nbd-onload');
                break;
            case 'photo':
                if( $scope.resource.photo.data.length == 0 ) jQuery('#tab-photo .loading-photo').hide();
                $scope.resource.photo.filter.total = $scope.resource.photo.filter.totalPage * $scope.resource.photo.filter.perPage;            
                break;
        }
    };
    $scope.changeCat = function(type, cat){
        $scope.resource[type].filter.search = '';
        $scope.resource[type].filter.currentPage = 1;
        $scope.resource[type].filter.currentCat = cat;
        $scope.resource[type].filter.total = cat.amount;
        $scope.updateApp();
        switch(type){
            case 'clipart':
                jQuery("#tab-svg .tab-scroll").stop().animate({
                    scrollTop: 0
                }, 100);
            break;
        };        
    };
    /* Change printing options */
    $scope.onLoadPrintingOptions = false;
    $scope.firstTimeLoadPrintingOptions = true;
    //$scope.awaitLoadPrintingOptions = false;
    $scope.awaitInsertTemplate = false;
    $scope.awaitSubmitForm = false;
    $scope.lastPrintingOptions = {};
    $scope.applyOptions = function(){
        jQuery('.nbd-popup.popup-nbo-options .close-popup').triggerHandler('click');
        if( angular.isDefined( NBDESIGNCONFIG.force_hide_option ) && ( NBDESIGNCONFIG.nbdesigner_display_product_option == '1' || $scope.settings.is_mobile ) ) {
            $scope.awaitSubmitForm = true;
            if( angular.isDefined( nbd_window.nbOption ) ){
                var hasOdOption = false;
                angular.forEach(nbd_window.nbOption.odOption, function(option){
                    hasOdOption = true;
                });
                if( !hasOdOption ){
                    $scope.saveData();
                }
            }else{
                $scope.saveData();
            }
        };
        $scope.changePrintingOptions();
    };
    $scope.changePrintingOptions = function( first, initVariationSetting ){
        if( $scope.onloadVariation ) return;
        jQuery('.cannot-start-design-notice').removeClass('nbd-show');
        if( angular.isDefined( nbd_window.nbOption ) ){console.log($scope.completedInsertTemplate);
            if($scope.firstTimeLoadPrintingOptions){
                if( NBDESIGNCONFIG['ui_mode'] == 1 || ( $scope.settings.nbdesigner_display_product_option == '2' && !$scope.settings.is_mobile ) ){
                    if( $scope.completedInsertTemplate ){
                        $scope.firstTimeLoadPrintingOptions = false;
                        $scope.awaitInsertTemplate = false;
                    }else{
                        $scope.awaitInsertTemplate = true;
                        return;
                    }
                }else{
                    $scope.firstTimeLoadPrintingOptions = false;
                }
            }
//            if( angular.isUndefined(first) ){
//                if( $scope.onLoadPrintingOptions ){
//                    $scope.awaitLoadPrintingOptions = true;
//                    return;
//                }else{
//                    $scope.awaitLoadPrintingOptions = false;
//                    $scope.onLoadPrintingOptions = true;
//                }
//            }
            if( angular.equals( nbd_window.nbOption.odOption, $scope.lastPrintingOptions ) ){
                if( !initVariationSetting ){
                    return;
                }
            }else{
                angular.copy( nbd_window.nbOption.odOption, $scope.lastPrintingOptions );
            }
            var data = nbd_window.nbOption.odOption;
            if( angular.isUndefined( $scope.stored_product ) || initVariationSetting ){
                $scope.stored_product = [];
                angular.copy($scope.settings.product_data.product, $scope.stored_product);
            }else{
                $scope.settings.product_data.product = [];
                angular.copy($scope.stored_product, $scope.settings.product_data.product);
            };
            $scope.settings.product_data.option.option_dpi = false;
            if( angular.isDefined( data.dpi ) ){
                $scope.settings.product_data.option.dpi = data.dpi;
                $scope.settings.product_data.option.option_dpi = true;
            }
            if( angular.isDefined( data.page ) ){
                $scope.currentStage = 0;
                var number_side = $scope.settings.product_data.product.length;
                if( angular.isDefined( data.page.list_page ) ){
                    if( data.page.list_page.length > 0 ){
                        $scope.settings.product_data.product = [];
                        angular.forEach(data.page.list_page, function(side, key){
                            if( angular.isDefined( $scope.stored_product[side] ) ){
                                $scope.settings.product_data.product.push( $scope.stored_product[side] )
                            }else if( angular.isDefined( $scope.settings.product_data.product[number_side - 1] ) ){
                                var temp = angular.copy($scope.settings.product_data.product[number_side - 1], temp);
                                $scope.settings.product_data.product.push(temp);
                            }
                        });
                        if( $scope.settings.product_data.product.length == 0 ){
                            angular.copy($scope.stored_product, $scope.settings.product_data.product);
                        }
                    }else{
                        jQuery('.cannot-start-design-notice').addClass('nbd-show');
                        return;
                    }
                }else{
                    var number_side = $scope.settings.product_data.product.length;
                    var src_side_index = number_side - 1,
                        insert_side_index = number_side - 1,
                        dst_number_side = data.page.number,
                        lastJsonDesign = {},
                        _canvas = $scope.stages[src_side_index]['canvas'];
                    _canvas.requestRenderAll();
                    lastJsonDesign = _canvas.toJSON($scope.includeExport);
                    angular.copy($scope.stored_product, $scope.settings.product_data.product);
                    if( data.page.page_display == '1' ){
                        if( data.page.exclude_page == '2' ){
                            if( number_side > 2 ){
                                src_side_index = number_side - 2;
                                insert_side_index = number_side - 2;
                            }
                            dst_number_side += 2;
                        }
                    } else {
                        if( data.page.exclude_page == '2' ){
                            dst_number_side = Math.ceil( dst_number_side / 2 ) + 1;
                        } else {
                            dst_number_side = Math.ceil( dst_number_side / 2 );
                        }
                    }
                    if( dst_number_side < number_side ){
                        $scope.settings.product_data.product.splice(dst_number_side - 1, number_side - dst_number_side);
                        for(i = dst_number_side; i < number_side; i++){
                            delete $scope.resource.jsonDesign['frame_' + dst_number_side];
                        }
                    }else if( dst_number_side > number_side ){
                        var i;
                        for(i = 0; i < dst_number_side - number_side; i++){
                            var temp = {};
                            angular.copy($scope.settings.product_data.product[src_side_index], temp);
                            if( data.page.page_display == '1' && data.page.exclude_page == '2' ) temp.orientation_name = NBDESIGNCONFIG.nbdlangs.page + ' ' + insert_side_index;
                            $scope.settings.product_data.product.splice(insert_side_index, 0, temp);
                            insert_side_index++;
                        }
                    }
                    $scope.stored_product = [];
                    angular.copy($scope.settings.product_data.product, $scope.stored_product);
                }
            }
            if( angular.isDefined( data.color ) ){
                angular.forEach($scope.settings.product_data.product, function(side, key){
                    if(data.color.bg_type == 'c'){
                        side.bg_type = 'color';
                        side.bg_color_value = data.color.bg_color;
                    }else{
                        side.bg_type = 'image';
                        if( angular.isDefined(data.page) && angular.isDefined( data.page.list_page ) ){
                            if( angular.isDefined( data.page.list_page[key] ) ){
                                var _key = data.page.list_page[key];
                                if( angular.isDefined(data.color.bg_image[_key]) ) side.img_src = data.color.bg_image[_key];
                            }
                        }else{
                            if( angular.isDefined(data.color.bg_image[key]) ) side.img_src = data.color.bg_image[key];
                        }
                    }
                });
            }
            if( angular.isDefined( data.size ) ){
                angular.forEach($scope.settings.product_data.product, function(side, key){
                    side.product_height = data.size.product_height;
                    side.product_width = data.size.product_width;
                    side.real_width = data.size.real_width;
                    side.real_height = data.size.real_height;
                    side.real_left = data.size.real_left;
                    side.real_top = data.size.real_top;
                    var ratio = side.product_width / side.product_height;
                    side.img_src_top = side.img_src_left = 0;
                    if(ratio > 1){
                        side.img_src_width = 500;
                        side.area_design_width = side.real_width / side.product_width * 500;
                        side.area_design_height = side.real_height / side.product_width * 500;
                        side.img_src_height = 500 / ratio;
                        side.img_src_top = (500 - side.img_src_height) / 2;
                        side.area_design_left = side.real_left / side.product_width * 500;
                        side.area_design_top = side.real_top / side.product_width * 500 + side.img_src_top;
                    }else{
                        side.img_src_height = 500;
                        side.area_design_height = side.real_height / side.product_height * 500;
                        side.area_design_width = side.real_width / side.product_height * 500;
                        side.img_src_width = 500 * ratio;
                        side.img_src_left = (500 - side.img_src_width) / 2;
                        side.area_design_left = side.real_left / side.product_height * 500 + side.img_src_left;
                        side.area_design_top = side.real_top / side.product_height * 500;
                    }                    
                });
            }
            if( angular.isDefined( data.dimension ) ){
                angular.forEach($scope.settings.product_data.product, function(side, key){
                    side.product_height = data.dimension.height;
                    side.product_width = data.dimension.width;
                    side.real_width = data.dimension.width;
                    side.real_height = data.dimension.height;
                    side.real_left = side.real_top = side.img_src_top = side.img_src_left = side.area_design_left = side.area_design_top = 0;
                    var ratio = side.product_width / side.product_height;
                    if(ratio > 1){
                        side.img_src_width = 500;
                        side.area_design_width = 500;
                        side.img_src_height = 500 / ratio;
                        side.area_design_height = 500 / ratio;
                        side.img_src_top = (500 - side.img_src_height) / 2;
                        side.area_design_top = side.img_src_top;
                    }else{
                        side.img_src_height = 500;
                        side.area_design_height = 500;
                        side.img_src_width = 500 * ratio;
                        side.area_design_width = 500 * ratio;
                        side.img_src_left = (500 - side.img_src_width) / 2;
                        side.area_design_left = side.img_src_left;
                    }
                });
            }
            if( angular.isDefined( data.orientation ) ){
                angular.forEach($scope.settings.product_data.product, function(side, key){
                    var productWidth = parseFloat(side.product_width), productHeight = parseFloat(side.product_height);
                    if( ( data.orientation == 1 && productWidth < productHeight ) || ( data.orientation == 0 && productHeight < productWidth ) ){
                        var tem = 1 * side.product_width;
                        side.product_width = 1 * side.product_height;
                        side.product_height = tem;
                        tem = 1 * side.img_src_width;
                        side.img_src_width = 1 * side.img_src_height;
                        side.img_src_height = tem;
                        tem = 1 * side.real_width;
                        side.real_width = 1 * side.real_height;
                        side.real_height = tem;
                        tem = 1 * side.img_src_top;
                        side.img_src_top = 1 * side.img_src_left;
                        side.img_src_left = tem;
                        tem = 1 * side.area_design_width;
                        side.area_design_width = 1 * side.area_design_height;
                        side.area_design_height = tem;
                        tem = 1 * side.bleed_top_bottom;
                        side.bleed_top_bottom = 1 * side.bleed_left_right;
                        side.bleed_left_right = tem;
                        tem = 1 * side.margin_top_bottom;
                        side.margin_top_bottom = 1 * side.margin_left_right;
                        side.margin_left_right = tem;
                        if( data.orientation == 1 ){
                            tem = side.real_left;
                            side.real_left = side.real_top;
                            side.real_top = side.product_height - side.real_height - tem;
                        }else{
                            tem = side.real_top;
                            side.real_top = side.real_left;
                            side.real_left = side.product_width - side.real_width - tem;
                        }
                        side.area_design_top = side.img_src_top + side.real_top / side.product_width * side.img_src_width;
                        side.area_design_left = side.img_src_left + side.real_left / side.product_width * side.img_src_width;
                    }
                });
            }
            if( angular.isDefined( data.area ) ){
                angular.forEach($scope.settings.product_data.product, function(side, key){
                    side.area_design_type = data.area;
                });
            }
            if( angular.isDefined( data.padding ) ){
                var padding = parseFloat(data.padding);
                angular.forEach($scope.settings.product_data.product, function(side, key){
                    side.product_height = 2 * padding + parseFloat(side.product_height);
                    side.product_width = 2 * padding + parseFloat(side.product_width);
                    side.real_left = padding + parseFloat(side.real_left);
                    side.real_top = padding + parseFloat(side.real_top);
                    var ratio = side.product_width / side.product_height;
                    if(ratio > 1){
                        side.img_src_width = 500;
                        side.area_design_width = side.real_width / side.product_width * 500;
                        side.area_design_height = side.real_height / side.product_width * 500;
                        side.img_src_height = 500 / ratio;
                        side.img_src_left = 0;
                        side.img_src_top = (500 - side.img_src_height) / 2;
                        side.area_design_left = side.real_left / side.product_width * 500;
                        side.area_design_top = side.real_top / side.product_width * 500 + side.img_src_top;
                    }else{
                        side.img_src_height = 500;
                        side.area_design_height = side.real_height / side.product_height * 500;
                        side.area_design_width = side.real_width / side.product_height * 500;
                        side.img_src_width = 500 * ratio;
                        side.img_src_top = 0;
                        side.img_src_left = (500 - side.img_src_width) / 2;
                        side.area_design_left = side.real_left / side.product_height * 500 + side.img_src_left;
                        side.area_design_top = side.real_top / side.product_height * 500;
                    }
                });
            }
            if( angular.isUndefined(first) ){
                $scope.saveDesign();
                if( angular.isDefined( data.page ) && angular.isUndefined( data.page.list_page ) && data.page.page_display == '1' && data.page.exclude_page == '2' ){
                    if( angular.isDefined( $scope.resource.jsonDesign['frame_' + ( number_side - 1 )] ) ){
                        $scope.resource.jsonDesign['frame_' + ( number_side - 1 )] = {
                            objects: [],
                            version: lastJsonDesign.version
                        };
                    }
                    $scope.resource.jsonDesign['frame_' + ( dst_number_side - 1 )] = {};
                    angular.copy( lastJsonDesign, $scope.resource.jsonDesign['frame_' + ( dst_number_side - 1 )] );
                }
                appConfig.ready = false;
                if( (angular.isDefined( data.color ) && data.color.bg_type == 'c') || angular.isDefined( data.area ) ){
                    $scope.forceInitStage = true;
                }else{
                    $scope.settings.product_data.design = $scope.resource.jsonDesign;
                    $scope.settings.product_data.config = {};
                    $scope.settings.product_data.config.viewport = $scope.calcViewport();
                    $scope.settings.product_data.fonts = $scope.resource.usedFonts;
                };
                $scope.currentStage = 0;
                $scope.processProductSettings();
                $scope.changeExtraOdOptions();
                $scope.updateApp();
            }
        }
    };
    $scope.changeExtraOdOptions = function(){
        if( $scope.onloadVariation ) return;
        if( angular.isDefined( nbd_window.nbOption ) && angular.isDefined( nbd_window.nbOption.extraOdOption ) ){
            var data = nbd_window.nbOption.extraOdOption;
            if( angular.isDefined( data.rounded_corner ) ){
                angular.forEach($scope.settings.product_data.product, function(side, key){
                    side.bleed_radius = parseFloat( data.rounded_corner );
                    side.safezone_radius = parseFloat( data.rounded_corner ) - side.margin_left_right;
                    if( side.safezone_radius < 0 ) side.safezone_radius = 0;
                    if( side.bleed_radius != 0 ){
                        $scope.stages[key].config.bleed_radius = $scope.stages[key].config.cwidth * side.bleed_radius / side.product_width;
                        $scope.stages[key].config.safezone_radius = $scope.stages[key].config.cwidth * side.safezone_radius / side.product_width;
                    }else{
                        delete $scope.stages[key].config.bleed_radius;
                        delete $scope.stages[key].config.safezone_radius;
                    }
                });
            }
            if( angular.isDefined( data.overlay ) ){
                angular.forEach($scope.settings.product_data.product, function(side, key){
                    if( angular.isDefined( data.overlay[key] ) && data.overlay[key] != '' ){
                        side.img_overlay = data.overlay[key];
                        $scope.stages[key].config.img_overlay = data.overlay[key];
                    }
                });
            }
            if( angular.isDefined( data.fold ) ){
                var foldClass = '',
                    fold      = false,
                    numberPanel = 1;
                switch( data.fold ){
                    case 'n':
                        fold = false;
                        break;
                    case 'h':
                        foldClass = 'single f2';
                        numberPanel = 2;
                        fold = true;
                        break;
                    case 't':
                        foldClass = 'single f3';
                        numberPanel = 3;
                        fold = true;
                        break;
                    case 'z':
                        foldClass = 'single f3';
                        numberPanel = 3;
                        fold = true;
                        break;
                    case 's':
                        foldClass = 'single f3_2';
                        numberPanel = 3;
                        fold = true;
                        break;
                    case 'd':
                        foldClass = 'single f4';
                        numberPanel = 4;
                        fold = true;
                        break;
                    case 'dp':
                        foldClass = 'single f4';
                        numberPanel = 4;
                        fold = true;
                        break;
                    case 'dr':
                        foldClass = 'single f4';
                        numberPanel = 4;
                        fold = true;
                        break;
                    case 'r':
                        foldClass = 'single f4';
                        numberPanel = 4;
                        fold = true;
                        break;
                    case 'a':
                        foldClass = 'single f4';
                        numberPanel = 4;
                        fold = true;
                        break;
                    case 'hh':
                        foldClass = 'double f2';
                        numberPanel = 4;
                        fold = true;
                        break;
                    case 'ht':
                        foldClass = 'double f3';
                        numberPanel = 6;
                        fold = true;
                        break;
                }
                $scope.resource.config.fold = fold;
                $scope.resource.config.foldClass = foldClass;
                $scope.resource.config.numberPanel = numberPanel;
            }
        }
    };
    $scope.onloadVariation = false;
    $scope.initVariationSetting = false;
    $scope.preventChangeCurrentDesign = false;
    $scope.changeVariation = function(){
        if( $scope.onloadVariation ) return;
        if( angular.isDefined( NBDESIGNCONFIG.force_hide_option ) && ( NBDESIGNCONFIG.nbdesigner_display_product_option == '1' || $scope.settings.is_mobile ) ) {
            return;
        }
        $scope.onloadVariation = true;
        $scope.toggleStageLoading();
        $scope.saveDesign();
        appConfig.ready = false;
        $scope.preventChangeCurrentDesign = true;
        NBDDataFactory.get('nbdesigner_get_product_info', {product_id: NBDESIGNCONFIG['product_id'], variation_id: NBDESIGNCONFIG['variation_id'], need_templates: 1}, function(data){
            $scope.toggleStageLoading();
            $scope.onloadVariation = false;
            $scope.initVariationSetting = true;
            if( NBDESIGNCONFIG['ui_mode'] == 1 ) nbd_window.NBDESIGNERPRODUCT.nbdesigner_ready();
            $scope.settings.product_data = JSON.parse(data);
            if( angular.isDefined($scope.settings.product_data.templates) ){
                $scope.resource.templates = $scope.settings.product_data.templates;
            }
            $scope.settings.product_data.design = $scope.resource.jsonDesign;
            $scope.settings.product_data.config = {};
            $scope.settings.product_data.config.viewport = $scope.calcViewport();
            $scope.settings.product_data.fonts = $scope.resource.usedFonts;
            //$scope.changePrintingOptions( true );
            if( angular.isDefined( nbd_window.nbOption ) ){
                var und;
                $scope.changePrintingOptions( und, true );
            } else {
                $scope.processProductSettings();
            }
        });
    };
    $scope.loadedPrintingOptions = false;
    $scope.widthoutPrintingOptions = false;
    $scope.getPrintingOptions = function(){
        if( $scope.settings.nbdesigner_display_product_option == '1' || $scope.settings.is_mobile ){
            jQuery('.nbd-popup.popup-nbo-options').nbShowPopup();
        }
        if(!$scope.loadedPrintingOptions){
            if( angular.isDefined( NBDESIGNCONFIG.force_hide_option ) && ( NBDESIGNCONFIG.nbdesigner_display_product_option == '1' || $scope.settings.is_mobile ) ) {
                jQuery('.nbd-popup.popup-nbo-options .close-popup').triggerHandler('click');
            }
            $http({
                method: 'GET',
                url: $scope.settings.link_get_options
            }).then(function(response){
                $scope.loadedPrintingOptions = true;
                var container = jQuery('#nbo-options-wrap');
                container.append(response.data);
                jQuery(document).trigger('nbo_get_option_success');
                /* Compare with other color swatches plugins */
                jQuery('.variation-selector').removeClass('hidden').show();
                jQuery('.nbtcs-swatches').addClass('hidden');                 
                if( jQuery('.nbo-wrapper').length == 0 ){
                    $scope.widthoutPrintingOptions = true;
                    if( jQuery('input[name="variation_id"]').length > 0 && jQuery('input[name="variation_id"]').val() > 0 ){
                        $scope.printingOptionsAvailable = true;
                    }
                    jQuery('.variations_form').on('woocommerce_variation_has_changed wc_variation_form', function(){
                        if( jQuery('input[name="variation_id"]').length > 0 && jQuery('input[name="variation_id"]').val() > 0 ){
                            NBDESIGNCONFIG.variation_id = jQuery('input[name="variation_id"]').val();
                            $scope.printingOptionsAvailable = true;
                            var _interval = $interval(function(){
                                if( $scope.completedInsertTemplate == true ){
                                    $scope.changeVariation();
                                    $interval.cancel( _interval );
                                }
                            }, 100);
                        }else{
                            $scope.printingOptionsAvailable = false;
                        }
                    });
                    jQuery('.variations_form').on('found_variation found_variation.wc-variation-form', function(){
                        setTimeout(function(){
                            if( jQuery('input[name="variation_id"]').length > 0 && jQuery('input[name="variation_id"]').val() > 0 ){
                                NBDESIGNCONFIG.variation_id = jQuery('input[name="variation_id"]').val();
                                $scope.printingOptionsAvailable = true;
                                var _interval = $interval(function(){
                                    if( $scope.completedInsertTemplate == true ){
                                        $scope.changeVariation();
                                        $interval.cancel( _interval );
                                    }
                                }, 100);
                            }else{
                                $scope.printingOptionsAvailable = false;
                            }
                        }, 100);
                    });
                }
                if(NBDESIGNCONFIG.show_nbo_option == "1" && NBDESIGNCONFIG.task == 'new'){
                    container.find('form.variations_form').append('<input name="submit_form_mode2" type="hidden" value="1" />');
                    container.find('form.cart').append('<input name="submit_form_mode2" type="hidden" value="1" />');
                    container.find('form.cart').append('<input name="add-to-cart" type="hidden" value="'+NBDESIGNCONFIG.product_id+'" />');
                }
                if( NBDESIGNCONFIG.task2 != '' ){
                    jQuery('.variations_form').addClass('nbd-disabled');
                    jQuery('form.cart').addClass('nbd-disabled');
                }
                jQuery('.nbd-popup.popup-nbo-options .overlay-popup').addClass('nbo-disable');
                jQuery('.single_add_to_cart_button').addClass('nbd-disabled');
                jQuery('.nbd-popup.popup-nbo-options').find('.overlay-main').removeClass('active');
                jQuery('.tab-product .loaded').hide();
                jQuery('.single_add_to_cart_button').hide();
                var newScope = angular.element(container).scope();
                var compile = angular.element(container).injector().get('$compile');
                compile(jQuery(container).contents())(newScope);
                $timeout(function(){
                    container.perfectScrollbar();
                    if(jQuery('#nbo-options-wrap .variations_form').length){
                        jQuery('#nbo-options-wrap .variations_form').wc_variation_form();
                        jQuery('#nbo-options-wrap .variations_form').trigger( 'wc_variation_form' );
                        jQuery('#nbo-options-wrap .variations_form .variations select').change();
                    }
                });
            });
        }
    };
    /* Upload Image */
    $scope.uploadFile = function(files, indexFile){
        indexFile = angular.isDefined(indexFile) ? indexFile : 0;
        if( files.length <= 0 || indexFile > (files.length - 1) || indexFile > (parseInt($scope.settings.nbdesigner_max_upload_files_at_once) - 1) ) return;
        var file = files[indexFile],
        max_size = parseInt($scope.settings.nbdesigner_maxsize_upload),     
        min_size = parseInt($scope.settings.nbdesigner_minsize_upload);      
        if( file.type.indexOf("image") === -1 ){
            alert('Only support image');
            return;
        }
        if (file.size > max_size * 1024 * 1024 ) {
            alert('Max file size' + max_size + " MB");
            return;            
        }else if(file.size < min_size * 1024 * 1024){
            alert('Min file size' + min_size + " MB");
            return;          
        };
        if( file.type.indexOf("svg") > -1 ){
            var reader = new FileReader();
            reader.onload = function(event){
                if (event.target.readyState === 2) {
                    var result = reader.result;
                    $scope.addSvgFromString(result);
                    $scope.uploadFile(files, indexFile + 1);
                }
            };
            reader.readAsText(file);
        }else{
            $scope.toggleStageLoading();
            $scope.resource.upload.progressBar = 0;
            jQuery('.nbd-progress-bar').addClass('active');
            //jQuery('.loading-workflow').toggleClass('nbd-show');
            NBDDataFactory.get('nbdesigner_customer_upload', {file: file}, function(data){
                //jQuery('.loading-workflow').toggleClass('nbd-show');
                var data = JSON.parse(data);
                if( data.flag == 1 ){
                    if( angular.isDefined(data.ilr) ) $scope.resource.upload.ilr = true;
                    if( $scope.resource.upload.ilr && NBDESIGNCONFIG['nbdesigner_enable_low_resolution_image'] == 'no' ){
                        $scope.toggleStageLoading();
                        alert(data.mes);
                        $scope.uploadFile(files, indexFile + 1);
                        return;
                    };
                    $scope.storeUploadFile(data.src, data.name);
                    $scope.addImage(data.src, false, true );
                    jQuery("#tab-photo .tab-scroll").stop().animate({
                        scrollTop: jQuery("#tab-photo .tab-scroll").prop("scrollHeight")
                    }, 100);
                    localStorage.setItem('uploaded', $scope.resource.upload.data);
                    $scope.onEndRepeat('upload');
                    $scope.uploadFile(files, indexFile + 1);
                }else{
                    $scope.toggleStageLoading();
                    alert(data.mes);
                }
                jQuery('.nbd-progress-bar').removeClass('active');
            }, function( progress ){
                $scope.resource.upload.progressBar = progress.toFixed(0);
            });
        }
    };
    $scope.storeUploadFile = function(src, name){
        $scope.resource.upload.data.push({
            url: src,
            des: name,
            ilr: $scope.resource.upload.ilr
        });        
        if( $scope.settings.nbdesigner_cache_uploaded_image == 'yes' ){
            $scope._localStorage.save('nbduploaded');
        }
    };
    /* Login */
    $scope.login = function(callback){
        if( $scope.settings.is_logged ){
            if(typeof callback == 'function') callback();
            return;
        }else{
            $scope.toggleStageLoading();
            NBDDataFactory.get('nbd_check_use_logged_in', {type: 'check_login'}, function(data){
                data = JSON.parse(data);
                $scope.toggleStageLoading();
                if( data.is_login == 1 ){
                    NBDESIGNCONFIG['nonce_get'] = data.nonce_get;
                    NBDESIGNCONFIG['nonce'] = data.nonce;
                    if(typeof callback == 'function') callback();
                    return;
                }else{
                    var popupLeft = (window.screen.width - 700) / 2,
                        popupTop = (window.screen.height - 500) / 2,
                        popup = window.open(NBDESIGNCONFIG['login_url'], '', 'width=700,height=500,left='+popupLeft+',top='+popupTop+'');
                    popup.onload = new function() {
                        if(window.location.hash.length == 0) {
                            popup.open(NBDESIGNCONFIG['login_url'], '_self');
                        }; 
                        var interval = setInterval(function () {
                            try {
                                if (popup.location.hash.length) {
                                    var hash = popup.location.hash;
                                    var res = hash.split("___");
                                    if(res.length){
                                        NBDESIGNCONFIG['nonce_get'] = res[0].substr(1);
                                        NBDESIGNCONFIG['nonce'] = res[1];                             
                                    }
                                    clearInterval(interval);
                                    $scope.settings['is_logged'] = 1;
                                    popup.close();
                                    $scope.updateApp();
                                    if(typeof callback == 'function') callback();
                                }
                            } catch (evt) {
                                //permission denied
                            }
                        }, 100);   
                    }                      
                }
            });
        }
    };
    /* Extenal Image */
    $scope.addImageFromUrl = function( url, extenal, ilr ){
        if( url == '' ) return;
        $scope.toggleStageLoading();
        $scope.showDesignTab();
        if(url.match(/\.(svg)$/) != null ){
            var art = {url: url, name: ''};
            $scope.addArt(art, false, true);
            return
        };
        if( angular.isUndefined(extenal) || extenal ){
            NBDDataFactory.get('nbdesigner_copy_image_from_url', {url: url, gapi: $scope.resource.gapi}, function(data){
                data = JSON.parse(data);
                if(data['flag'] == 1){
                    $scope.addImage(data['src'], false, true);
                    $scope.storeUploadFile(data.src, '');
                } else{
                    alert('Try to download image and then upload to our server!');
                }
            });
        }else{
            if( ilr ) $scope.resource.upload.ilr = true;
            $scope.addImage(url, false, true);
        }
    };  
    $scope.stageOnload = false;
    $scope.toggleStageLoading = function( timeout ){
        jQuery('.loading-workflow').toggleClass('nbd-show');
        var container = appConfig.isVisual ? '.nbd-mode-vista' : 'body';
        jQuery(container).toggleClass('nbd-onloading');
        var _timeout = timeout ? timeout : 2E4;
        if( jQuery('.loading-workflow').hasClass('nbd-show') ){
            $scope.stageOnload = true;
            promise = $timeout(function(){
                if( $scope.stageOnload ){
                    jQuery('.loading-workflow').toggleClass('nbd-show');
                    jQuery(container).toggleClass('nbd-onloading');                    
                }
            }, _timeout);
        }else{
            $timeout.cancel(promise);
            $scope.stageOnload = false;
        }
    };
    /* Webcam */
    $scope.initWebcam = function(){
        $scope.resource.webcam.status = true;
        Webcam.set({
            width: 400,
            height: 300,
            dest_width: 1280,
            dest_height: 960,
            image_format: 'jpeg',
            jpeg_quality: 100
        });
        Webcam.set("constraints", {
            optional: [{ minWidth: 600 }]
        });        
        Webcam.attach( '#my_camera' );     
        Webcam.setSWFLocation(NBDESIGNCONFIG['assets_url'] + 'webcam.swf');  
    };    
    $scope.pauseWebcam = function(status){
        status == true  && Webcam.freeze() || Webcam.unfreeze();
    };
    $scope.resetWebcam = function(){
        if($scope.resource.webcam.status){
            Webcam.reset();
            $scope.resource.webcam.status = false;
        }else{
            $scope.initWebcam();
        }        
    };     
    $scope.takeSnapshot = function(){
        Webcam.snap( function(data_uri) {
            $scope.resetWebcam();
            var raw_image_data = data_uri.replace(/^data\:image\/\w+\;base64\,/, '');
            NBDDataFactory.get('nbdesigner_save_webcam_image', {image: raw_image_data}, function(data){
                data = JSON.parse(data);
                if(data.flag == 'success'){
                    jQuery('.popup-webcam .close-popup').triggerHandler('click');
                    $scope.toggleStageLoading();
                    $scope.addImage(data.url, false, true);
                }else{
                    alert('Oops! Try again!');
                    $scope.initWebcam();                    
                }
            });
        });
    };
    $scope.onEndRepeat = function(type){
        switch(type){
            case 'typography':
                $scope.renderMasonryList(type, '.nbd-sidebar .typography-items', '.typography-item', '#tab-typography', $scope.resource[type].init);
                break;
            case 'font':
                jQuery('#toolbar-font-familly-dropdown').perfectScrollbar('update');
                break;
            case 'clipart':
                $scope.renderMasonryList(type, '#tab-svg .clipart-wrap', '.clipart-item', '#tab-svg', $scope.resource[type].init);
                break; 
            case 'photo':
                $scope.renderMasonryList(type, '#tab-photo .nbdesigner-gallery', '.nbdesigner-item', '#tab-photo', $scope.resource[type].init);
                break;
            case 'dropbox':
            case 'instagram': 
            case 'facebook':  
            case 'upload':    
            case 'shape':    
            case 'icon':    
            case 'line':    
                $scope.renderMasonryList(type, '#nbd-'+type+'-wrap .mansory-wrap', '.mansory-item', '#nbd-'+type+'-wrap', $scope.resource[type].init);
                break;         
        }
    };
    $scope.updateScrollBar = function( jSelector ){
        $timeout(function(){
            jQuery(jSelector).perfectScrollbar('update');
        });
    };
    $scope.renderMasonryList = function(type, container, item, scrollContainer, init){  
        imagesLoaded( jQuery(container), function() {
            if( !init ) jQuery(container).masonry('destroy');            
            var $grid = jQuery(container).masonry({
                itemSelector: item
            }); 
            //$grid.on( 'layoutComplete', function(){
                jQuery.each(jQuery(container + ' ' +item), function(e) {
                    var animate = Math.floor(Math.random() * 10);
                    animate = (animate + 1) * 100;           
                    if( checkMobileDevice() ){
                        jQuery(this).addClass("in-view");
                    }else{
                        jQuery(this).addClass("in-view slideInDown animated animate"+animate);
                    }
                }); 
                jQuery(scrollContainer+' .tab-scroll').perfectScrollbar('update');
                $timeout(function(){
                    jQuery(scrollContainer + ' .loading-photo').hide();
                    jQuery(scrollContainer + ' .tab-load-more').show();
                }, 100);
                $scope.resource[type].onload = false;
                $scope.resource[type].init = false;
            //});            
        });
    };
    /* Infinite scroll */
    $scope.scrollLoadMore = function(container, type){
        if( $scope.resource[type].onload ) return;
        if( type == 'photo' && $scope.resource.personal.status ){
            var photoType = $scope.resource.personal.type;
            if( photoType == 'url' || photoType == 'upload' ) return;
            if( $scope.resource[photoType].filter.currentPage * $scope.resource[photoType].filter.perPage < $scope.resource[photoType].filter.total ){
                $scope.resource[photoType].filter.currentPage += 1;
            }else{
                jQuery(container + ' .loading-photo').hide();
                return;
            }
            jQuery(container + ' .loading-photo').show();
            $scope.resource[photoType].onload = true;
            if( photoType == 'facebook' ) $scope.getPersonalPhoto('facebook');
            $scope.updateApp();
            return;
        };
        if( type == 'element' ){
            var elementType = $scope.resource.element.type;
            if( ['icon', 'shape', 'line'].indexOf(elementType) > -1 ){
                if( $scope.resource[elementType].filter.currentPage < $scope.resource[elementType].filter.totalPage ){
                    $scope.resource[elementType].filter.currentPage += 1;
                    $scope.getMedia(elementType, 'more');
                }else{
                    jQuery(container + ' .loading-photo').hide();
                    return;
                }
            };
//            jQuery("#tab-element .tab-scroll").stop().animate({
//                scrollTop: jQuery('#tab-element .main-items').height()
//            }, 100);            
            return;
        };
        if( type == 'globalTemplate' ){
            if( $scope.resource[type].filter.currentPage < $scope.resource[type].filter.totalPage ){
                $scope.resource[type].filter.currentPage += 1;
                $scope.toggleStageLoading();
                $scope.resource[type].onload = true;                
                $scope.loadGlobalTemplate($scope.templateCat);
            }else{
                jQuery(container + ' .loading-photo').hide();
                return;
            }         
            return;
        }
        if( $scope.resource[type].filter.currentPage * $scope.resource[type].filter.perPage >= $scope.resource[type].filter.total){
            jQuery(container + ' .loading-photo').hide();
            return;
        }
        jQuery(container + ' .loading-photo').show();
        if( $scope.resource[type].filter.currentPage * $scope.resource[type].filter.perPage < $scope.resource[type].filter.total ){
            $scope.resource[type].filter.currentPage += 1;
        }
        switch(type){
            case 'typography':
            case 'clipart':    
                $scope.resource[type].onload = true;
                break;
            case 'font':
                //todo something
                break;  
            case 'photo':
                $scope.resource[type].onload = true;
                !$scope.resource.personal.status && $scope.getPhoto($scope.resource.photo.type, 'more');
                break;
        }
        $scope.updateApp();
    };
    $scope.generateTypoLink = function(typo){
        if( $scope.settings.task == 'typography' ){
            return NBDESIGNCONFIG['plg_url'] + '/data/typography/img/' + typo.id + '.png';
        }else{
            return '//dpeuzbvf3y4lr.cloudfront.net/typography/' + typo.folder + '/preview.png';
        }
    };
    /* Fonts */
    $scope.loadFontFailAction = function( font ){
        _.remove($scope.settings.gg_fonts, {
            id: font.id
        });
        $scope.resource.font.filteredFonts = filterFontFilter($scope.resource.font.data, $scope.resource.font.filter);
        $scope.updateApp();
    };
    /* Save Data */
    $scope.prepareBeforeSaveForLater = function(){
        $scope.selectedMyDesign = '';
        $scope.login(function(){
            var dataObj = {
                product_id: NBDESIGNCONFIG['product_id'],
                variation_id: NBDESIGNCONFIG['variation_id']
            };
            var action = 'nbd_get_user_designs';
            $scope.toggleStageLoading();
            NBDDataFactory.get(action, dataObj, function(data){
                data = JSON.parse(data);
                if(data.flag == 1){
                    $scope.resource.myTemplates = data.designs;
                    $scope.toggleStageLoading();
                    jQuery('.popup-nbd-my-templates2').nbShowPopup();
                }else{
                    $scope.toggleStageLoading();
                }
            });
        });
    };
    $scope.selectMyDesign = function( id ){
        $scope.selectedMyDesign = id;console.log($scope.selectedMyDesign);
        jQuery('.popup-nbd-my-templates2 .close-popup').triggerHandler('click');
        $scope.saveData('saveforlater');
    };
    $scope.saveForLater = function(){
        $scope.login(function(){
            $scope.saveData('saveforlater');
        });
    };
    $scope.startLogo = 0;
    $scope.cuzz = function(){
        var filterArt = {
            currentCat: { id: '10' },
            currentPage: 1,
            search: '',
            perPage: 1000
        }
        $scope.clearAllStage();
        var filteredArts = filterArtFilter($scope.resource.clipart.data.arts, filterArt);
        if( $scope.startLogo < filteredArts.length - 1 ){
            $scope.addArt(filteredArts[$scope.startLogo], true, true);
            $timeout(function(){
                $scope.saveData();
            }, 1000);
        }
    };
    $scope.downloadMockupPreview = function(){
        jQuery('.nbd-popup.popup-nbd-mockup-preview').find('.overlay-main').addClass('active');
        var mockups = '';
        angular.forEach($scope.resource.mockups, function(mockup, key) {
            if(mockup.select){
                mockups += mockup.path + '|';
            }
        });
        if(mockups.length > 0){ 
            var dataObj = {};
            dataObj.type = 'get_mockup';
            dataObj.folder = $scope.resource.social.folder;
            dataObj.mockups = mockups.slice(0, mockups.length - 1);
            NBDDataFactory.get('nbd_get_resource', dataObj, function(data){
                data = JSON.parse(data);
                if( data.flag == 1 ){
                    var filename = 'designs.zip',
                    a = document.createElement('a');
                    a.setAttribute('href', data.data.url);
                    a.setAttribute('download', filename);
                    a.style.display = 'none';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }else{
                    alert('try again!');
                    console.log(data);
                }
                jQuery('.nbd-popup.popup-nbd-mockup-preview').find('.overlay-main').removeClass('active');
            });
        }else{
            jQuery('.nbd-popup.popup-nbd-mockup-preview').find('.overlay-main').removeClass('active');
            alert('Please choose mockup!');
        }
    };
    $scope.cancelMockupPreview = function(){
        jQuery('.nbd-popup.popup-nbd-mockup-preview .close-popup').triggerHandler('click');
    };
    $scope.prepareSaveTemplate = function(){
        jQuery('.nbd-popup.popup-template-tags').nbShowPopup();
    };
    /* Template tags */
    $scope.reloadTemplateTags = function(){
        $http({
            method: 'GET',
            url: NBDESIGNCONFIG['ajax_url'] + '?action=nbd_get_template_tags&nonce=' + NBDESIGNCONFIG['nonce']
        }).then(function successCallback( response ){
            if( angular.isDefined( response.data.flag ) && response.data.flag == 1 ){
                $scope.customTemplate.reload = 1;
                $scope.customTemplate.tags = response.data.tags;
                $scope.customTemplate.tag_ids = [];
                angular.forEach($scope.customTemplate.tags, function(tag, key) {
                    $scope.customTemplate.tag_ids.push( tag.term_id );
                });
            }
        }, function errorCallback(response) {
            console.log('Fail to load: template tags');
        });
    };
    $scope.addTemplateTag = function( tagId ){
        var _index = _.findIndex($scope.customTemplate.selectedTags, function(tag) { return tag == tagId; });
        if( _index == -1 ){
            $scope.customTemplate.selectedTags.push( tagId );
        }else{
            $scope.customTemplate.selectedTags.splice(_index, 1);
        }
    };
    $scope.isSelectedTags = function( tagId ){
        $scope.customTemplate.tag_ids.push( tagId );
        $scope.customTemplate.tag_ids = _.uniq( $scope.customTemplate.tag_ids );
        return _.includes( $scope.customTemplate.selectedTags, tagId );
    };
    $scope.validateTemplateTags = function(){
        var newSelectedTags = [];
        angular.forEach($scope.customTemplate.selectedTags, function(tag, key) {
            if( _.includes( $scope.customTemplate.tag_ids, tag ) ){
                newSelectedTags.push( tag );
            }
        });
        return newSelectedTags;
    };
    $scope.addTemplateColor = function( color ){
        color = color.substr(1);
        $scope.customTemplate.selectedColors.push(color);
        $scope.customTemplate.selectedColors = _.uniq( $scope.customTemplate.selectedColors );
        $scope.customTemplate.showPicker = false;
    };
    $scope.removeTemplateColor = function( colorIndex ){
        $scope.customTemplate.selectedColors.splice(colorIndex, 1);
    };
    $scope.selectCustomTemplatePreview = function( files ){
        $scope.customTemplate.template_thumb = files[0];
    };
    $scope.saveData = function(type){
        jQuery('.variations_form, form.cart').find('[name="nbo-ignore-design"]').remove();
        if( angular.isUndefined( type ) && angular.isDefined( nbd_window.nbOption ) && angular.isDefined( nbd_window.nbOption.odOption ) 
                && angular.isDefined( nbd_window.nbOption.odOption.page ) && angular.isDefined( nbd_window.nbOption.odOption.page.list_page ) && nbd_window.nbOption.odOption.page.list_page.length == 0 ){
            if(NBDESIGNCONFIG.show_nbo_option == "1" && NBDESIGNCONFIG.task == 'new' && NBDESIGNCONFIG.task2 == '' ){
                nbd_window.jQuery('.variations_form, form.cart').append('<input name="nbo-ignore-design" type="hidden" value="1" />');
                jQuery('.variations_form, form.cart').submit();
                return;
            }
            return;
        }
        if( angular.isUndefined(type) ) type = $scope.settings.task;
        if(type != 'share' && type != 'save_draft' ) $scope.toggleStageLoading( 6E4 );
        if(type == 'typography') $scope.resource.usedFonts = [];
        var excludeType = ['saveforlater', 'share', 'download-pdf', 'preview_mockup', 'save_draft', 'download-jpg'];
        //if(type != 'saveforlater' && type != 'share' && type != 'download-pdf' && type != 'preview_mockup' ) $scope.maybeZoomStage = true;
        if( !_.includes(excludeType, type) ) $scope.maybeZoomStage = true;
        $scope.saveDesign();
        //$scope.resource.config.viewport = $scope.calcViewport(); 
		$scope.resource.config.viewport = $scope.viewPort; 
        /* Backward compatible version 1.x */
        $scope.resource.config.scale =  ($window.innerWidth > ($window.innerHeight - 120) ? $window.innerHeight - 120 : $window.innerWidth) / 500;  
        $scope.resource.config.product = $scope.settings.product_data.product;
        if( angular.isDefined($scope.settings.product_data.origin_product) ){
            $scope.resource.config.origin_product = $scope.settings.product_data.origin_product;
        }
        $scope.resource.config.dpi = $scope.settings.product_data.option.dpi;
        if( $scope.settings.product_data.option.option_dpi ){
            $scope.resource.config.option_dpi = true;
        }
        var dataObj = {};
        dataObj.used_font = new Blob([JSON.stringify($scope.resource.usedFonts)], {type: "application/json"}); 
        if( type == 'template' ) $scope.resource.jsonDesign.canvas = {width: $scope.templateSize.width, height: $scope.templateSize.height};
        dataObj.design = new Blob([JSON.stringify($scope.resource.jsonDesign)], {type: "application/json"});
        _.each($scope.stages, function(stage, index){
            var key = 'frame_' + index,
                svg_key = 'frame_' + index + '_svg'; 
            dataObj[key] = $scope.makeblob(stage.design);
            if(type != 'typography' && type != 'template' ){
                dataObj[svg_key] = new Blob([stage.svg], {type: "image/svg"});  
            }
        });
        switch(type){
            case 'typography':
                dataObj.type = 'save_typography';
                dataObj.id = $scope.resource.currentTypo;
                _.each($scope.stages, function(stage, index){
                    var key = 'frame_' + index;
                    dataObj[key] = $scope.makeblob(stage.design);
                });
                NBDDataFactory.get('nbd_get_resource', dataObj, function(data){
                    $scope.stages[0].states.usedFonts = [];
                    alert('Success!');
                });
                break;
            case 'template':
                dataObj.type = 'save_template';
                dataObj.source = 'media';
                dataObj.tem_name = $scope.templateName;
                dataObj.cid = $scope.templateCat;
                jQuery('.popup-template .close-popup').triggerHandler('click');
                NBDDataFactory.get('nbd_get_resource', dataObj, function(data){
                    data = JSON.parse(data);
                    if( data.flag == 1 ){
                        _.each($scope.stages, function(stage, index){
                            stage.states.usedFonts = [];
                        });
                        $scope.resource.usedFonts = [];
                        $scope.listAddedColor = [];
                        $scope.resetStage();
                        $scope.toggleStageLoading();
                        alert('Success!');
                    }else{
                        alert('Try again!');
                    }
                });
                break;
            case 'saveforlater':    
            default:
                ['product_id', 'variation_id', 'task', 'task2', 'design_type', 'nbd_item_key', 'cart_item_key', 'order_id', 'enable_upload_without_design', 'auto_add_to_cart', 'ui_mode'].forEach(function(key){
                    dataObj[key] = NBDESIGNCONFIG[key];
                });
                if( type == 'share' ) dataObj['share'] = 1;
                dataObj['nbd_file'] = '';
                dataObj.config = new Blob([JSON.stringify($scope.resource.config)], {type: "application/json"}); 
                if( NBDESIGNCONFIG.task == 'new' && NBDESIGNCONFIG.ui_mode == 1 ){
                    //dataObj.auto_add_to_cart = NBDESIGNCONFIG['nbdesigner_auto_add_cart_in_detail_page'];
                };
                if( $scope.resource.config.qty != null ){
                    dataObj.qty = $scope.resource.config.qty;
//                    dataObj.qtys = $scope.resource.config.qtys;
                }else{
                    delete $scope.resource.config.qty;
                }
                $timeout(function(){
                    //var action = ( type != 'preview_mockup' && type != 'share' && type != 'saveforlater' && type != 'download-pdf' && NBDESIGNCONFIG.task == 'new' && NBDESIGNCONFIG.ui_mode == 2 ) ?  'nbd_save_cart_design' : 'nbd_save_customer_design';
                    var action = ( !_.includes(excludeType, type) && NBDESIGNCONFIG.task == 'new' && NBDESIGNCONFIG.ui_mode == 2 ) ?  'nbd_save_cart_design' : 'nbd_save_customer_design';
                    if(NBDESIGNCONFIG.show_nbo_option == "1" && NBDESIGNCONFIG.task == 'new'){
                        action = 'nbd_save_customer_design';
                    }
                    if(type == 'save_draft'){
                        action = 'nbd_save_draft_design';
                        dataObj.save_draft = 1;
                        if( angular.isDefined($scope.resource.draft_folder) && $scope.resource.draft_folder != '' ) dataObj.draft_folder = $scope.resource.draft_folder;
                    }
                    if(type == 'preview_mockup') {
                        dataObj.generate_mockup = 1;
                        jQuery('.nbd-popup.popup-nbd-mockup-preview').find('.overlay-main').addClass('active');
                    }
                    if( NBDESIGNCONFIG.task == 'create' || ( NBDESIGNCONFIG.task == 'edit' && NBDESIGNCONFIG.design_type == 'template' )  ){
                        if( $scope.customTemplate.type == 2 ){
                            dataObj['template_thumb'] = $scope.customTemplate.template_thumb;
                        }
                        dataObj['template_name'] = $scope.customTemplate.name;
                        dataObj['template_type'] = $scope.customTemplate.type;
                        var selectedTags = $scope.validateTemplateTags();
                        dataObj['template_tags'] = selectedTags.join(',');
                        dataObj['template_colors'] = $scope.customTemplate.selectedColors.join(',');
                        $scope.closePopup( '.popup-template-tags' );
                    }
                    NBDDataFactory.get(action, dataObj, function(data){
                        data = JSON.parse(data);
                        if(data.flag == 'success'){
//                            if( NBDESIGNCONFIG.task2 == 'cuzz' ){
//                                $scope.startLogo++;
//                                $scope.toggleStageLoading();
//                                $timeout(function(){
//                                    $scope.cuzz();
//                                }, 100);
//                                return;
//                            }
								
                                jQuery('#designer-controller').attr('data-nbd', data.folder);
								var type_dl = jQuery('#designer-controller').attr('data-dl');
								if(type_dl!='' && typeof type_dl !== "undefined") {
									$scope.saveDesignCustom(type_dl);
									jQuery('#designer-controller').removeAttr('data-dl');
								}
                                
                            if( type == 'save_draft' ){
                                $scope.resource.draft_folder = data.draft_folder;
                                return;
                            }
                            if( type == 'preview_mockup' ){
                                if( data.mockups ){
                                    $scope.resource.mockups = data.mockups;
                                    $scope.resource.social.folder = data.folder;
                                    var origin_url = 'whatsapp://send?text=';
                                    var d = new Date();
                                    var share_url = NBDESIGNCONFIG.nbd_create_own_page + '?product_id=' + NBDESIGNCONFIG.product_id + '&variation_id=' + NBDESIGNCONFIG.variation_id + '&reference=' + $scope.resource.social.folder + '&nbd_share_id=' + $scope.resource.social.folder + '&t=' + d.getTime();
                                    $scope.resource.social.wa_link = origin_url + encodeURIComponent(share_url);
                                    jQuery('.whatsapp_share').attr('href', $scope.resource.social.wa_link);
                                }
                                $timeout(function(){
                                    jQuery('.nbd-simple-slider').nbSimpleSlider();
                                });
                                jQuery('.nbd-popup.popup-nbd-mockup-preview').find('.overlay-main').removeClass('active');
                                $scope.toggleStageLoading();
                                return;
                            }
                            if( type == 'download-pdf' || type == 'download-jpg' ){
                                var _dataObj = {nbd_item_key: data.folder};
                                var action2 = type == 'download-pdf' ? 'nbd_frontend_download_pdf' : 'nbd_frontend_download_jpeg';
                                NBDDataFactory.get(action2, _dataObj, function(_data){
                                    _data = JSON.parse(_data);
                                    if(_data[0].flag == 1){
                                        var filename = type == 'download-pdf' ? 'design.pdf' : 'designs.zip',
                                        a = document.createElement('a');
                                        a.setAttribute('href', _data[0].link);
                                        a.setAttribute('download', filename);
                                        a.style.display = 'none';
                                        document.body.appendChild(a);
                                        a.click();
                                        document.body.removeChild(a);
                                        $scope.toggleStageLoading();
                                    }
                                });
                                return;
                            }
                            if( NBDESIGNCONFIG.ui_mode == 3 ){
                                if( NBDESIGNCONFIG.task == 'new' && NBDESIGNCONFIG.task2 == '' ){
                                    $scope.toggleStageLoading();
                                    jQuery(document).triggerHandler( 'nbd_design_stored', {_type: type} );
                                    return;
                                }else{
                                    if(NBDESIGNCONFIG['redirect_url'] != ""){
                                        window.location = NBDESIGNCONFIG['redirect_url'];
                                        return;
                                    };
                                }
                            }
                            if( type == 'saveforlater' ){
                                var _dataObj = {product_id: NBDESIGNCONFIG.product_id, variation_id: NBDESIGNCONFIG.variation_id, folder: data.folder};
                                if( angular.isDefined( $scope.selectedMyDesign ) && $scope.selectedMyDesign != '' ){
                                    _dataObj.pre_folder = $scope.selectedMyDesign;
                                }
                                NBDDataFactory.get('nbd_save_for_later', _dataObj, function(_data){
                                    _data = JSON.parse(_data);
                                    if( angular.isDefined( $scope.selectedMyDesign ) && $scope.selectedMyDesign != '' ){
                                        if( _data.src ){
                                            _.each($scope.resource.myTemplates, function(template, index){
                                                if( template.id == _data.folder ){
                                                    template.src = _data.src;
                                                }
                                            });
                                        }
                                    };
                                    $scope.toggleStageLoading();
                                }); 
                                return;
                            } if( type == 'share' ) {
                                $scope.resource.social.folder = data.sfolder;
                                jQuery('.nbd-popup.popup-share').find('.overlay-main').removeClass('active');   
                                return;
                            }else{
                                if(NBDESIGNCONFIG.show_nbo_option == "1" && NBDESIGNCONFIG.task == 'new' && NBDESIGNCONFIG.task2 == '' ){
                                    if(NBDESIGNCONFIG.show_nbo_option == "1" && NBDESIGNCONFIG.task == 'new'){
                                        //jQuery('input[name="quantity"]').val($scope.resource.config.qty);
                                    };
                                    jQuery('.variations_form, form.cart').submit();
                                    return;
                                }
                                if(NBDESIGNCONFIG['redirect_url'] != ""){
                                    window.location = NBDESIGNCONFIG['redirect_url'];
                                    return;
                                };
                                if( NBDESIGNCONFIG['nbdesigner_auto_add_cart_in_detail_page'] == "yes" &&  NBDESIGNCONFIG.task == 'new' && NBDESIGNCONFIG.ui_mode == 1 && !( angular.isDefined( NBDESIGNCONFIG.edit_option_mode ) && NBDESIGNCONFIG.edit_option_mode == '1' ) ){
                                    nbd_window.jQuery('.variations_form, form.cart').append('<input name="add-to-cart" type="hidden" value="'+NBDESIGNCONFIG.product_id+'" />');
                                    nbd_window.jQuery('.variations_form, form.cart').append('<input name="nbd-auto-add-to-cart-in-detail-page" type="hidden" value="1" />');
                                    nbd_window.jQuery(nbd_window.document).triggerHandler( 'nbd_design_stored', {_type: type, prevent_ajax: 1} );
                                }else{
                                    nbd_window.NBDESIGNERPRODUCT.product_id = NBDESIGNCONFIG['product_id'];
                                    nbd_window.NBDESIGNERPRODUCT.variation_id = NBDESIGNCONFIG['variation_id'];             
                                    nbd_window.NBDESIGNERPRODUCT.folder = data.folder;
                                    nbd_window.NBDESIGNERPRODUCT.show_design_thumbnail(data.image, NBDESIGNCONFIG['task'], $scope.resource.config);
                                    nbd_window.NBDESIGNERPRODUCT.get_sugget_design(NBDESIGNCONFIG['product_id'], NBDESIGNCONFIG['variation_id']);   
                                    if( NBDESIGNCONFIG.ui_mode == 1 && $scope.resource.config.qty != null ){
                                        //nbd_window.NBDESIGNERPRODUCT.updateQty($scope.resource.config.qty);
                                    }
                                    if( NBDESIGNCONFIG.ui_mode == 1 && angular.isDefined( data.gallery ) ){
                                        nbd_window.jQuery(nbd_window.document).triggerHandler( 'nbd_update_gallery', {gallery: data.gallery, folder: data.folder} );
                                    }
                                    $scope.toggleStageLoading();
                                    $timeout(function(){
                                        _.each($scope.stages, function(stage, index){
                                            $scope.zoomStage(stage.states.fitScaleIndex, index);
                                        });
                                    });                         
                                }
                            }
                        }else{
                            console.log('Oops! Design has not been saved!');
                            if(type != 'save_draft'){ $scope.toggleStageLoading() };
                        }    
                    }); 
                });
                break;
        }
    };
    
    $scope.saveDesignCustom = function( downloadType ){
    var nbd = jQuery('#designer-controller').attr('data-nbd');
    if (downloadType!='') {
        // console.log(nbd);
        if (typeof nbd !== "undefined") {
            var base_url = location.origin;
            if(location.host=="localhost") {
                var getUrl = window.location;
                base_url = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1];
            }
            
                // console.log('oke');
                var link_dl = base_url+'?download_nbd_design_file=1&codeblue=1&nbd_item_key='+nbd+'&type='+downloadType;
                // console.log(link_dl);
                var filename = 'design_'+downloadType+'.zip',
                a = document.createElement('a');
                a.setAttribute('href', link_dl);
                // a.setAttribute('download', filename);
                a.style.display = 'none';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
				jQuery('#designer-controller').removeAttr('data-nbd');
            } else {
                jQuery('#designer-controller').attr('data-dl', downloadType);
                $scope.saveData('saveforlater');
            }
        }
    };
    
    $scope.createShareLink = function(type, origin_url){
        var d = new Date();
        var share_url = NBDESIGNCONFIG.nbd_create_own_page + '?product_id=' + NBDESIGNCONFIG.product_id + '&variation_id=' + NBDESIGNCONFIG.variation_id + '&reference=' + $scope.resource.social.folder + '&nbd_share_id=' + $scope.resource.social.folder + '&t=' + d.getTime();
        if( jQuery('.quick-view form .variations select').length ){
            share_url += '&' + jQuery('.quick-view form .variations select').serialize();
        }else if( $scope.settings.ui_mode == 1 && nbd_window.jQuery('.variations_form .variations select').length ){
            share_url += '&' + nbd_window.jQuery('.variations_form .variations select').serialize();
        }
        $scope.resource.social.link = origin_url + encodeURIComponent(share_url);
        var comment = angular.isDefined($scope.resource.social.comment) ? $scope.resource.social.comment : $scope.settings.nbdlangs.my_design;
        if( type == 'twitter' ) $scope.resource.social.link += '&text=' + comment;
    };
    $scope.updateShareLink = function(){
        var link = $scope.resource.social.link;
        if(link.indexOf('&text=') > -1){
            link = link.substr(0, link.indexOf('&text=') + 6) + $scope.resource.social.comment;
            $scope.resource.social.link = link;
        }
    };
    $scope.maybeZoomStage = false;
    $scope.saveDesign = function( downloadType ){
        _.each($scope.stages, function(stage, index){
            $scope.deactiveAllLayer(index);
            var zoomIndex = stage.states.fillScaleIndex != -1 ? stage.states.fillScaleIndex : stage.states.fitScaleIndex;
            if($scope.maybeZoomStage) $scope.zoomStage(zoomIndex, index);
            var _canvas = stage.canvas,
                key = 'frame_' + index;
            $scope.renderStage(index);
            $scope.resource.jsonDesign[key] = _canvas.toJSON($scope.includeExport);
            stage.svg = _canvas.toSVG();
            stage.design = _canvas.toDataURL();
            $scope.resource.usedFonts = _.concat($scope.resource.usedFonts, stage.states.usedFonts);
        });
        $scope.resource.usedFonts = _.uniqBy($scope.resource.usedFonts, 'alias');
        $scope.maybeZoomStage = false;
        if( downloadType ){
            $scope.toggleStageLoading();
            $timeout(function(){
                $scope.toggleStageLoading();
                _.each($scope.stages, function(stage, index){
                    $timeout(function(){
                        var filename = (index + 1) + ( downloadType == 'png' ? '.png' : '.svg' ),
                        a = document.createElement('a');
                        var _data = stage.design;
                        if( navigator.userAgent.indexOf("Edge") > -1 ){
                            var blob;
                            blob = $scope.makeblob( _data );
                            _data = window.URL.createObjectURL(blob);
                        }
                        if(downloadType == 'png'){
                            a.setAttribute('href', _data); 
                        }else{
                            var url = "data:image/svg+xml;charset=utf-8,"+encodeURIComponent(stage.svg);
                            a.setAttribute('href', url); 
                        }
                        a.setAttribute('download', filename);
                        a.style.display = 'none';
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        if( navigator.userAgent.indexOf("Edge") > -1 ){
                            setTimeout(function() {
                                return window.URL.revokeObjectURL( _data );
                            }, 1000);
                        }
                    }, index * 500);
                });
            }, 300);
        }
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
    $scope.updateApp = function(){
        if ($scope.$root.$$phase !== "$apply" && $scope.$root.$$phase !== "$digest") $scope.$apply(); 
    };
    $scope.$on('nbd:keypress', function(event, e){
        $scope.keypressHandle(e);
    });
    $scope.$on('nbd:keydown', function(event, e){
        $scope.keydownHandle(e);
    });    
    $scope.keydownHandle = function(e){
        //todo
    };
    $scope.wraperClickHandle = function($event){
        var $textPicker = jQuery('#nbd-text-color-picker');
        var $bgPicker = jQuery('#nbd-bg-color-picker');
        var $globalPicker = jQuery('#nbd-global-color-picker');
        var $stageBgPicker = jQuery('#nbd-stage-bg-color-picker');
        if (!jQuery($event.target).hasClass('color-palette-add') && $scope.showTextColorPicker 
                && $textPicker.has($event.target).length == 0 && !$textPicker.is($event.target)){
            $scope.showTextColorPicker = false;
        };
        if( !jQuery($event.target).hasClass('color-palette-add') && $scope.showBgColorPicker && $bgPicker.has($event.target).length == 0 && !$bgPicker.is($event.target) ){
            $scope.showBgColorPicker = false;
        }
        if( !jQuery($event.target).hasClass('color-palette-add') && $scope.stageBgColorPicker.status && $stageBgPicker.has($event.target).length == 0 && !$stageBgPicker.is($event.target) ){
            $scope.stageBgColorPicker.status = false;
        }
        if( !jQuery($event.target).hasClass('color-palette-add') && $scope.globalPicker.active && $globalPicker.has($event.target).length == 0 && !$globalPicker.is($event.target) ){
            $scope.globalPicker.active = false;
        };
    };
    /* Hotkeys */
    $scope.keypressHandle = function(e){
        var targetEl = e.target.tagName.toUpperCase();
//        if($scope.stages[$scope.currentStage].states.isEditing){
//            if( e.which == 13  ){
//                e.preventDefault();
//                $scope.deactiveAllLayer();
//                return;
//            }
//        }
        if( targetEl == 'INPUT' || targetEl == 'TEXTAREA' ||  $scope.stages[$scope.currentStage].states.isEditing ){
            if( !(e.ctrlKey && (e.which == 66 || e.which == 73))  ){
                return;
            }
        }
        var _stage = $scope.stages[$scope.currentStage],
        _states = _stage.states;        
        if(e.ctrlKey || e.metaKey){
            var keepDefault = [67, 116];
            if( _.includes(keepDefault, e.which) ) return;
            e.preventDefault();
            if( e.shiftKey ){
                switch( e.which ) { 
                    case 221:
                        /* Hold Ctrl + Shift + ] Bring layer to front */ 
                        $scope.setStackPosition('bring-front');
                        break;
                    case 219:
                        /* Hold Ctrl + Shift + [ Send layer to back */ 
                        $scope.setStackPosition('send-back');
                        break;   
                    case 73:
                        /* Hold Ctrl + Shift + I → Import Design */ 
                        $scope.importDesign();
                        break;
                    case 69:
                        /* Hold Ctrl + Shift + E → Export Design */ 
                        $scope.exportDesign();
                        break;      
                    case 83:
                        /* Hold Ctrl + Shift + S → Save Design for later*/ 
                        $scope.saveData('saveforlater');
                        break;  
                    case 76:
                        /* Hold Ctrl + Shift + L → clear all stages*/ 
                        $scope.clearAllStage();
                        break;
                    case 79:
                        /* Hold Ctrl + Shift + O → Load My Design in Cart*/ 
                        $scope.loadMyDesign(null, true);
                        break;
                    case 188:
                        /* Hold Ctrl + Shift + < → Decreate font size*/ 
                        if( _states.isText ){
                            _states.text.ptFontSize -= 1;
                            $scope.setTextAttribute('fontSize', _states.text.ptFontSize);
                        }
                        $scope.updateApp();
                        break;  
                    case 190:
                        /* Hold Ctrl + Shift + > → Increate font size*/ 
                        if( _states.isText ){
                            _states.text.ptFontSize += 1;
                            $scope.setTextAttribute('fontSize', _states.text.ptFontSize);
                        }
                        $scope.updateApp();
                        break; 
                    case 71:
                        /* Hold Ctrl + Shift + G → Ungroup */ 
                        if( _states.isNativeGroup ){
                            $scope.unGroupLayers();
                        }
                        $scope.updateApp();
                        break;                         
                }
            }else{
                switch( e.which ) {
                    case 65:
                        /* Hold Ctrl press A → select all layers */
                        $scope.selectAllLayers();
                        break;
                    case 66:
                        /* Hold Ctrl press B → set font weight bold */
                        if( _states.isText 
                                && (_states.text.font.file.b 
                                && ( _states.text.fontStyle != 'italic' || ( _states.text.fontStyle == 'italic'  && _states.text.font.file.bi ) )) ){
                            $scope.setTextAttribute('fontWeight', _states.text.fontWeight == 'bold' ? 'normal' : 'bold');
                        }
                        $scope.updateApp();
                        break;   
                    case 80:
                        /* Hold Ctrl press P → duplicate layers */
                        $scope.copyLayers();
                        break;                 
                    case 73:
                        /* Hold Ctrl press I → set text style italic */
                        if( _states.isText 
                                && (_states.text.font.file.i 
                                && ( _states.text.fontWeight != 'bold' || ( _states.text.fontWeight == 'bold'  && _states.text.font.file.bi ) )) ){                    
                            $scope.setTextAttribute('fontStyle', _states.text.fontStyle == 'italic' ? 'normal' : 'italic');
                        }
                        $scope.updateApp();
                        break; 
                    case 68:
                        /* Hold Ctrl press D → deselect group */
                        $scope.deactiveAllLayer();
                        break;    
                    case 69:
                        /* Hold Ctrl press E → clear stage */
                        $scope.clearStage();
                        break;                      
                    case 90:
                        /* Hold Ctrl press Z → Undo */
                        if( _states.isUndoable ){
                            $scope.undo();
                        }
                        break;    
                    case 89:
                        /* Hold Ctrl press Y → Undo */
                        if( _states.isRedoable ){
                            $scope.redo();
                        }
                        break;    
                    case 71:
                        /* Hold Ctrl press G → Group Layers */
                        if( _states.isGroup ){
                            $scope.groupLayers();
                        }
                        $scope.updateApp();
                        break;   
                    case 76:
                        /* Hold Ctrl press L → Toggle Bleed Line */
                        $scope.settings.bleedLine = !$scope.settings.bleedLine;
                        $scope.updateApp();
                        break;
                    case 82:
                        /* Hold Ctrl press R → Toggle Ruler */
                        $scope.toggleRuler();
                        $scope.updateApp();
                        break;    
                    case 72:
                        /* Hold Ctrl press H → Align layer center horizontal */
                        $scope.translateLayer('horizontal');
                        break;    
                    case 86:
                        /* Hold Ctrl press V → Align layer center vertical */
                        $scope.translateLayer('vertical');
                        break;     
                    case 107:
                        /* Hold Ctrl press + → Zoom In stage */
                        if( _states.currentScaleIndex < _states.scaleRange.length - 1 ){
                            $scope.zoomStage(_states.currentScaleIndex + 1);
                            $scope.updateApp();
                        }
                        break;    
                    case 109:
                        /* Hold Ctrl press - → Zoom out stage */
                        if( _states.currentScaleIndex > 0 ){
                            $scope.zoomStage(_states.currentScaleIndex - 1);
                            $scope.updateApp();
                        }
                        break;
                    case 48:
                    case 96:
                        /* Hold Ctrl press 0 → Resize stage to fit */
                        $scope.zoomStage(_states.fitScaleIndex);
                        $scope.updateApp();                
                        break;
                    case 49:
                    case 97:
                        /* Hold Ctrl press 1 → Resize stage to origin size */
                        if(_states.fillScaleIndex == -1){
                            $scope.zoomStage(_states.fitScaleIndex);
                        }else{
                            $scope.zoomStage(_states.fillScaleIndex);
                        };
                        $scope.updateApp();                
                        break;
                    case 221:
                        /* Hold Ctrl press ] Bring layer forward */ 
                        $scope.setStackPosition('bring-forward');
                        break;
                    case 219:
                        /* Hold Ctrl press [ Bring layer backward */ 
                        $scope.setStackPosition('send-backward');
                        break;  
                    case 79:
                        /* Hold Ctrl press O → Load My Design */ 
                        $scope.loadMyDesign(null, false);
                        break;                     
                }
            }   
        }else if( e.altKey ){
            e.preventDefault();
            switch( e.which ) {
                case 37:
                    /* Hold Alt press left arrow */
                    $scope.moveLayer('left', 'alt');
                    break;
                case 38:
                    /* Hold Alt press up arrow */
                    $scope.moveLayer('up', 'alt');
                    break;       
                case 39:
                    /* Hold Alt press right arrow */
                    $scope.moveLayer('right', 'alt');
                    break;
                case 40:
                    /* Hold Alt press down arrow */
                    $scope.moveLayer('down', 'alt');
                    break;   
                case 85:
                    /* Hold Alt press U */
                    if( _states.isText ) $scope.setTextAttribute('is_uppercase', true);
                    $scope.updateApp();
                    break;   
                case 76:
                    /* Hold Alt press U */
                    if( _states.isText ) $scope.setTextAttribute('is_uppercase', false);
                    $scope.updateApp();
                    break;                
            } 
        } else if( e.shiftKey ){
            switch( e.which ) {
                case 107:
                    /* Hold Shift press + → zoom out layer */
                    $scope.scaleLayer('+');
                    break;
                case 109:
                    /* Hold Shift press - → zoom in layer */
                    $scope.scaleLayer('-');
                    break; 
                case 71:
                    /* Hold Shift + G → Toggle Grid */ 
                    $scope.settings.showGrid = !$scope.settings.showGrid;
                    $scope.updateApp();
                    break;
                case 76:
                    /* Hold Shift + L → clear all guidelines*/ 
                    $scope.clearGuides();
                    break;
                case 68:
                    /* Hold Shift + D → Toggle Dimension */ 
                    $scope.settings.showDimensions = !$scope.settings.showDimensions;
                    $scope.updateApp();
                    break;
            }
        } else {
            switch( e.which ) {
                case 37:
                    /* Press left arrow */
                    if( _states.isActiveLayer )
                        $scope.moveLayer('left');
                    break;
                case 38:
                    /* Press up arrow */
                    if( _states.isActiveLayer )
                        $scope.moveLayer('up');
                    break;
                case 39:
                    /* Press right arrow */
                    if( _states.isActiveLayer )    
                        $scope.moveLayer('right');
                    break;
                case 40:
                    /* Press down arrow */
                    if( _states.isActiveLayer )
                        $scope.moveLayer('down');
                    break; 
                case 46:
                    /* Press "delete" → delete layers */
                    $scope.deleteLayers();
                    break;  
                case 86:
                    /* Press "V" → disable draw mode */
                    if( $scope.resource.drawMode.status ) jQuery('.item[data-type="draw"]').triggerHandler('click');    
                    break;
                case 66:
                    /* Press "B" → enable draw mode */
                    if( !$scope.resource.drawMode.status ) jQuery('.item[data-type="draw"]').triggerHandler('click');                   
                    break;               
            }
        }
    };
    $scope.onClickStage = function( $event ){
        /*
         * Deactive all layer if click outer canvas 
         * Hide context menu
         * Store stages
         */
        if(angular.element($event.target).hasClass('stage')){
            $scope.deactiveAllLayer();
            /* store all stages */    
            if( $scope.settings.nbdesigner_save_latest_design == 'yes' ){
                $scope.saveDesign();
                var json = {config: {}};
                json.config.viewport = $scope.calcViewport();
                json.fonts = $scope.resource.usedFonts;
                json.design = $scope.resource.jsonDesign;
                var pid = NBDESIGNCONFIG['product_id'] + '-' + NBDESIGNCONFIG['variation_id'];
                $scope.localStore.update(pid, json, function(){
                    jQuery('.nbd-toasts').nbToasts();
                });     
            }
        }
        $scope.ctxMenuStyle.visibility = 'hidden';
        $scope.updateApp();
    };   
    $scope.$on('nbd:contextmenu', function(event, e){
        $scope.contextMenu(e);
    });
    $scope.ctxMenuStyle = {
        'top': '17%',
        'left': '33%',
        'visibility': 'hidden'
    };
    $scope.contextMenu = function(e){
        if( $scope.stages[$scope.currentStage].states.isEditing ) return;
        e.preventDefault();
        if($scope.stages[$scope.currentStage].states.isActiveLayer){
            var posX = e.pageX,
                posY = e.pageY;
            var contextEl = angular.element(document.getElementById('nbd-context-menu'))[0],
                height = contextEl.clientHeight,
                width = contextEl.clientWidth; 
            if($scope.workBenchWidth < (posX + width + 15)) posX = $scope.workBenchWidth - width - 15;
            if($scope.workBenchHeight < (posY + height + 15)) posY = $scope.workBenchHeight - height - 15;       
            $scope.ctxMenuStyle = {
                'visibility': 'visible',
                top: posY,
                left: posX
            }
        }else{
            $scope.ctxMenuStyle = {
                'visibility': 'hidden',
                'pointer-events': 'none'
            };
        }
        $scope.updateApp();
    }; 
    $scope.preventLoadDesign = false;
    $scope.globalPicker = {
        color: NBDESIGNCONFIG.nbdesigner_default_color,
        attr: 'text.stroke',
        active: false
    };
    $scope.selectGlobalPicker = function(color){
        $scope.globalPicker.color = color;
        var attr_arr = $scope.globalPicker.attr.split(".");
        if( attr_arr.length == 2 && attr_arr[0] == 'text' ){
            $scope.setTextAttribute(attr_arr[1], $scope.globalPicker.color);
        }
        jQuery('#nbd-global-color-palette').removeClass('show');
    };
    $scope.$on('nbd:picker', function(event, attr, color){
        $scope.globalPicker.attr = attr;
        $scope.globalPicker.color = color;
    });
    $scope.$on('canvas:created', function(event, id, last){
        /* init canvas parameters */
        $scope.initStageSetting( id );
        var _canvas = $scope.stages[id].canvas;
        if(!checkMobileDevice()){
            jQuery('#stage-container-'+id).perfectScrollbar();
            jQuery('#stage-container-'+id).on('drop', function(event){
                $scope.onDrop(event);
            });            
        };
        /* Listen canvas events */
        _canvas.on('mouse:down', function(options) {
            $scope.onMouseDown(id, options);
        });
        _canvas.on("mouse:over", function(options){
            $scope.onMouseOverStage(id, options);
        });
        _canvas.on("mouse:out", function(options){
            $scope.onMouseOutStage(id, options);
        });
        _canvas.on("mouse:move", function(options){
            $scope.onMouseMoveStage(id, options);
        });        
        _canvas.on("mouse:up", function(options){
            $scope.onMouseUpStage(id, options);
        });
        _canvas.on("path:created", function(options){
            $scope.onPathCreated(id, options);
        });
        _canvas.on("object:added", function(options){
            $scope.onObjectAdded(id, options);
        });
        _canvas.on("selection:created", function(options){
            $scope.onSelectionCreated(id, options);
        });
        _canvas.on("object:scaling", function(options){
            $scope.onObjectScaling(id, options);
        });
        _canvas.on("object:scaled", function (options) {
            $scope.onObjectScaled(id, options);
        });
        _canvas.on("object:moving", function(options){
            $scope.onObjectMoving(id, options);
        });
        _canvas.on("object:moved", function (options) {
            $scope.onObjectMoved(id, options);
        });
        _canvas.on("object:rotating", function(options){
            $scope.onObjectRotating(id, options);
        });
        _canvas.on("object:modified", function(options){
            $scope.onObjectModified(id, options);
        });
        _canvas.on("before:render", function(options){
            $scope.onBeforeRender(id, options);
        });
        _canvas.on("after:render", function(options){
            $scope.onAfterRender(id, options);
        });
        _canvas.on("selection:cleared", function(options){
            $scope.onSelectionCleared(id, options);
        });
        _canvas.on("text:editing:entered", function(options){
            $scope.onEditingEntered(id, options);
        });   
        _canvas.on("text:editing:exited", function(options){
            $scope.onEditingExited(id, options);
        });        
        _canvas.on("text:changed", function(options){
            $scope.onTextChanged(id, options);
        });         
        _canvas.on("selection:updated", function(options){
            $scope.onSelectionUpdated(id, options);
        });        
        _canvas.on("text:selection:changed", function(options){
            $scope.onSelectionChanged(id, options);
        });
        _canvas.on("mouse:dblclick", function(options){
            $scope.onDblclick(options);
        });
        /* Load template after render canvas */
        if( last == '1' ){
            appConfig.ready = true;
            if( $scope.preventLoadDesign ){
                $scope.preventLoadDesign = false;
                return;
            }
            $scope.loadTemplateAfterRenderCanvas();
        }
        if( $scope.duplicateStageIndex == id ){
            $scope.duplicateStageIndex = -1;
            $scope.contextAddLayers = 'template';
            $scope.onloadTemplate = true;
            $scope.stages[id].canvas.loadFromJSON($scope.resource.jsonDesign[id], function() {
                $scope.onloadTemplate = false;
                $scope.contextAddLayers = 'normal';
            });
        }
    });
    $scope.loadTemplateAfterRenderCanvas = function(){
        if( $scope.forceInitStage ){
            $scope.initStagesSettingWithoutTemplate();
            $scope.forceInitStage = false;
            return;
        }
        if( !$scope.isTemplateMode || ($scope.isTemplateMode && $scope.settings.task == 'edit') ){
            $timeout(function(){
                function loadTemplate(){
                    if( $scope.the_first_time_load_page && angular.isDefined($scope.settings.product_data.lazy_load_design_folder) && $scope.settings.product_data.lazy_load_design_folder != '' ){
                        $scope.insertTemplate(false, {id: $scope.settings.product_data.lazy_load_design_folder});
                        $scope.the_first_time_load_page = false;
                        delete($scope.settings.product_data.lazy_load_design_folder);
                    }else if( angular.isDefined($scope.settings.product_data.design) ){
                        var config = angular.isDefined($scope.settings.product_data.config_ref) ? $scope.settings.product_data.config_ref : $scope.settings.product_data.config;
                        var viewport = config.viewport;
                        if( angular.isUndefined( config.viewport ) && angular.isDefined( config.scale ) ){
                            viewport = {width: config.scale * 500, height: config.scale * 500};
                        }
                        $scope.insertTemplate(true, {fonts: $scope.settings.product_data.fonts, design: $scope.settings.product_data.design, viewport: viewport});
                    }else{
                        if( NBDESIGNCONFIG.product_data.option.admindesign == "1" && $scope.resource.templates.length == 0 && NBDESIGNCONFIG.product_data.option.global_template == "1" && 
                                NBDESIGNCONFIG.product_data.option.global_template_cat != "" && $scope.settings.nbdesigner_disable_auto_load_template == 'no' ){
                            var interval = setInterval(function () {
                                if ($scope.globalTemplateLoaded) {
                                    if( $scope.resource.globalTemplate.data.length > 0 ){
                                        $scope.insertGlobalTemplate($scope.resource.globalTemplate.data[0].id);
                                    }else{
                                        $scope.initStagesSettingWithoutTemplate();
                                    };
                                    clearInterval(interval);
                                }
                            }, 100);
                        }else{
                            $scope.initStagesSettingWithoutTemplate();
                        }
                    }             
                }
                function loadLocalDesign(){
                    var pid = NBDESIGNCONFIG['product_id'] + '-' + NBDESIGNCONFIG['variation_id'];
                    if( $scope.settings.nbdesigner_save_latest_design == 'yes' ){
                        if($scope.localStore.ready){
                            $scope.localStore.get(pid, function(data){
                                if(data){
                                    $scope.insertTemplate(true, {fonts: data.data.fonts, design: data.data.design, viewport: data.data.config.viewport});
                                }else{
                                    loadTemplate();
                                }
                            });  
                        }else{
                            loadTemplate();
                        }                   
                    }else{
                        loadTemplate();
                    }
                }
                if( $scope.preventChangeCurrentDesign ){
                    loadTemplate();
                    return;
                }
                if( angular.isDefined($scope.settings.product_data.is_reference) ){
                    loadTemplate();
                    return;
                };
                if( angular.isDefined($scope.settings.product_data.is_template) || $scope.settings.task == 'create_template' ){
                    loadLocalDesign();
                }else{
                    loadTemplate();
                }
            });
        }else{
            $scope.initStagesSettingWithoutTemplate();
        }
    };
    $scope.theFirstCalcViewport = true;
    $scope.calcViewport = function(){
        var _offsetWidth = checkMobileDevice() ? 20 : 100,
            _offsetHeight = checkMobileDevice() ? 70 : 100,
            _width = jQuery('.nbd-stages').width() - _offsetWidth,
            _height = jQuery('.nbd-stages').height() - _offsetHeight;
        if(navigator.userAgent.indexOf("Safari")!=-1 && navigator.userAgent.indexOf("CriOS") ==-1 && $scope.theFirstCalcViewport){
            _offsetHeight = checkMobileDevice() ? 60 : 100;
        };
        if( $scope.theFirstCalcViewport ){
            $scope.viewPort = {width: _width, height: _height};
        }
        $scope.theFirstCalcViewport = false;
        return {width: _width, height: _height};
    };
    $scope.reCalcViewPort = function(){

    };
    $scope.calcStyle = function(value){
        return value + 'px';
    };
    $scope.getDPI = function() {
        var div = document.createElement( "div");
        div.style.height = "1in";
        div.style.width = "1in";
        div.style.top = "-200%";
        div.style.left = "-200%";
        div.style.position = "absolute";
        div.style.visibility = "hidden";
        div.style.opacity = "0";
        document.body.appendChild(div);
        var dpi =  div.offsetHeight;
        document.body.removeChild( div );
        return dpi;
    };
    $scope.processProductSettings = function(){
        var unitRatio = $scope.settings.nbdesigner_dimensions_unit == 'mm' ? 0.1 : ($scope.settings.nbdesigner_dimensions_unit == 'in' ? 2.54 : 1);
        $scope.rateConvert2Px = $scope.rateConvertCm2Px96dpi * unitRatio * parseFloat($scope.settings.product_data.option.dpi) / 96;
        var viewPort = $scope.calcViewport();
        var scaleRange = [0.1, 0.25, 0.3, 0.5, 0.75, 1, 1.25, 1.5, 1.75, 2, 3, 4, 5], 
            maxSize = checkMobileDevice() ? 1E3 : 2E3;
        $scope.stages = [];
        _.each($scope.settings.product_data.product, function(side, index){
            var _width = side.product_width * $scope.rateConvert2Px,
                _height = side.product_height * $scope.rateConvert2Px,
                designViewPort = $scope.fitRectangle(viewPort.width, viewPort.height, _width, _height, true),
                fillScale = _width / designViewPort.width,
                //minScale = 200 / Math.max(_width, _height),
                minScale = 200 / Math.max(designViewPort.width, designViewPort.height),
                //maxScale = maxSize / Math.max(_width, _height),
                maxScale = maxSize / Math.max(designViewPort.width, designViewPort.height),
                screenViewPort = $scope.fitRectangle(screen.width, screen.height, _width, _height, true),
                fullScreenScale = screenViewPort.width / designViewPort.width;
            var _scaleRange = scaleRange.filter(function(item){
                return item >= minScale && item <= maxScale;
            });
            $scope.stages[index] = {
                config: {
                    _width: _width,
                    _height: _height,
                    name: side.orientation_name,
                    width: designViewPort.width * side.real_width / side.product_width,
                    height: designViewPort.width * side.real_height / side.product_width,
                    top: designViewPort.width * side.real_top / side.product_width,
                    left: designViewPort.width * side.real_left / side.product_width,
                    cwidth: designViewPort.width,
                    cheight: designViewPort.height,
                    bleed_lr: designViewPort.width * side.bleed_left_right / side.product_width,
                    bleed_tb: designViewPort.width * side.bleed_top_bottom / side.product_width,
                    margin_lr: designViewPort.width * side.margin_left_right / side.product_width,
                    margin_tb: designViewPort.width * side.margin_top_bottom / side.product_width,
                    pWidth: side.product_width,
                    pHeight: side.product_height,
                    bgType: side.bg_type,
                    bgColor: side.bg_color_value,
                    bgImage: side.img_src,
                    showBleed: side.show_bleed,
                    showOverlay: side.show_overlay,
                    showSafeZone: side.show_safe_zone,
                    area_design_type: side.area_design_type,
                    show_overlay: side.show_overlay,
                    img_overlay: side.img_overlay
                },
                states: {},
                undos: [],
                redos: [],
                layers: [],
                canvas: {},
                rulers: [],
                rulerLines: {vers: [], hors: []}
            };
            if( angular.isDefined( side.bleed_radius ) && side.bleed_radius != 0 ){
                $scope.stages[index].config.bleed_radius = designViewPort.width * side.bleed_radius / side.product_width;
                $scope.stages[index].config.safezone_radius = designViewPort.width * side.safezone_radius / side.product_width;
            }
            var _state = $scope.stages[index].states;
            angular.copy($scope.defaultStageStates, _state);
            var factor = $scope.settings.nbdesigner_dimensions_unit == 'mm' ? 645.16 : ($scope.settings.nbdesigner_dimensions_unit == 'in' ? 1 :  6.4516);
            _state.ratioConvertFont = designViewPort.width / (side.product_width / unitRatio * 2.54 * 72) * factor;
            _scaleRange.forEach(function(value){
                value != 1 && _state.scaleRange.push({ratio: value, value: (value * 100).toFixed() + '%', label: (value * 100).toFixed() + '%'});
            });
            _state.scaleRange.push({ratio: 1, value: '100%', label: 'Fit'});
            _state.scaleRange.push({ratio: fullScreenScale, value: (fullScreenScale * 100).toFixed() + '%', label: (fullScreenScale * 100).toFixed() + '%'});
            if( fillScale >= minScale && fillScale <= maxScale  ) _state.scaleRange.push({ratio: fillScale, value: (fillScale * 100).toFixed() + '%', label: 'Fill'});
            var dpi = $scope.getDPI(),
            printSizeWidth = side.product_width / ($scope.settings.nbdesigner_dimensions_unit == 'mm' ? 25.4 : ($scope.settings.nbdesigner_dimensions_unit == 'in' ? 1 : 2.54)) * dpi,
            printSizeHeight = side.product_height / ($scope.settings.nbdesigner_dimensions_unit == 'mm' ? 25.4 : ($scope.settings.nbdesigner_dimensions_unit == 'in' ? 1 : 2.54)) * dpi;
            var printScale = 0;
            if( printSizeWidth < maxSize && printSizeHeight < maxSize && printSizeWidth > minScale && printSizeHeight > minScale ){
                printScale = printSizeWidth / designViewPort.width;
                _state.scaleRange.push({ratio: printScale, value: (printScale * 100).toFixed() + '%', label: 'Print Size'});
            }
            _state.scaleRange = _.sortBy(_state.scaleRange, [function(o) { return o.ratio; }]);
            _state.currentScaleIndex = _.findIndex(_state.scaleRange, function(o) { return o.ratio == 1; });
            _state.fitScaleIndex = _.findIndex(_state.scaleRange, function(o) { return o.ratio == 1; });
            _state.fillScaleIndex = _.findIndex(_state.scaleRange, function(o) { return o.ratio == fillScale; });
            _state.fullScreenScaleIndex = _.findIndex(_state.scaleRange, function(o) { return o.ratio == fullScreenScale; });
            _state.printScaleIndex = _.findIndex(_state.scaleRange, function(o) { return o.ratio == printScale; });
        });   
        if( $scope.settings.task == 'typography' || $scope.settings.task == 'create_template' ){
            $scope.stages = [{
                    config: {},
                    states: {
                        scaleRange: [{ratio: 1, value: '100%', label: 'Fit'}, {ratio: 2, value: '200%', label: '200%'}, {ratio: 3, value: '300%', label: '300%'}],
                        currentScaleIndex: 0,
                        fitScaleIndex: 0,
                        fillScaleIndex: 0
                    },
                    undos: [],
                    redos: [],
                    layers: [],
                    canvas: {}
                }
            ]; 
            $scope.stages[0].states.ratioConvertFont = 1;
            if( $scope.settings.task == 'create_template' ) 
                $scope.stages[0].states.scaleRange = [{ratio: 1, value: '100%', label: 'Fit'}, {ratio: 2, value: '200%', label: '200%'}, {ratio: 3, value: '300%', label: '300%'}, {ratio: 4, value: '400%', label: '400%'}, {ratio: 5, value: '500%', label: '500%'}]
            angular.copy($scope.defaultConfig, $scope.stages[0].config);
            angular.merge($scope.stages[0].states, $scope.defaultStageStates);
        };
        ($scope.settings.showRuler && appConfig.isModern) && $scope.initRuler();
    };
    /* Ruler and ruler guide line */
    $scope.toggleRuler = function(){
        $scope.settings.showRuler = !$scope.settings.showRuler;
        if($scope.settings.showRuler && appConfig.isModern){
            $scope.initRuler();
        }else{
            $scope.clearGuides();
        };
    };
    $scope.initRuler = function(){
        var viewPort = $scope.calcViewport();
        $timeout(function(){
            _.each($scope.settings.product_data.product, function(side, index){
                var stage = $scope.stages[index];
                stage.hozRuler = jQuery('#hoz-ruler-'+index).nbdRuler({layout: 'horizontal', viewPort: viewPort, config: stage.config, zoomRatio: stage.states.scaleRange[stage.states.currentScaleIndex].ratio, unit: $scope.settings.nbdesigner_dimensions_unit});
                stage.verRuler = jQuery('#ver-ruler-'+index).nbdRuler({layout: 'vertical', viewPort: viewPort, config: stage.config, zoomRatio: stage.states.scaleRange[stage.states.currentScaleIndex].ratio, unit: $scope.settings.nbdesigner_dimensions_unit});
                stage.hozRuler.render();
                stage.verRuler.render();
            });
        }, 100);
    };
    $scope.calcRulerPaddingLeft = function(cwidth){
        var viewPort = $scope.calcViewport();
        var additionalPadding = (viewPort.width - cwidth) / 2;
        additionalPadding = additionalPadding > 0 ? (additionalPadding + 50) : 50;
        return additionalPadding + 'px';
    };
    $scope.calcRulerPaddingTop = function(){
        return '40px';
    };
    $scope.addRulerGuideLine = function( $event, direction ){
        var stage = $scope.stages[$scope.currentStage],
        el = jQuery($event.currentTarget),
        stageEl = el.parent('.stage'),
        stagePos = stageEl.offset(),
        top = $event.pageY - stagePos.top,
        left = $event.pageX - stagePos.left;
        stage.rulerLines[direction].push({top: top, left: left});
    };
    $scope.calcStyleGuideline = function(line, cf, ratio, direction){
        var style = {};
        var viewPort = $scope.calcViewport();
        if( direction == 'hor' ){
            style.top = (line.top > 40 ? ( 40 + (line.top - 40) * ratio - 3) : (line.top - 3) ) + 'px';
            style.width = ((cf.cwidth * ratio > viewPort.width ? cf.cwidth * ratio : viewPort.width) + 100) + 'px';
        }else{
            var additionalPadding = (cf.cwidth * ratio - viewPort.width) / 2;
            if(additionalPadding < 0){
                if( line.left <= 50 ){
                    style.left = line.left;
                }else{
                    style.left = viewPort.width / 2 - ( viewPort.width / 2 - (line.left-50) ) * ratio + 50;
                }
            }else{
                style.left = viewPort.width / 2 - ( viewPort.width / 2 - (line.left - 50) ) * ratio + 50 + additionalPadding;
            }
            style.left = ( style.left - 3 )+ 'px';
            style.height = ((cf.cheight * ratio > viewPort.height ? cf.cheight * ratio : viewPort.height) + 90) + 'px';
        }
        return style;
    };
    $scope.clearGuides = function(){
        $scope.stages[$scope.currentStage].rulerLines = {vers: [], hors: []};
    };
    $scope.initStageSetting = function( id ){
        $scope.setStageDimension(id);
        $scope.renderStage(id);
        $scope.updateApp();
    };
    $scope.initStagesSettingWithoutTemplate = function(){
        _.each($scope.stages, function(stage, index){
            var _canvas = stage.canvas;
//            if( stage.config.bgType == 'color' ){
//                _canvas.backgroundColor = stage.config.bgColor;
//            }
            if( stage.config.area_design_type == "2" ){
                $scope.contextAddLayers = 'template';
                var width = _canvas.width,
                height = _canvas.height,
                path = new fabric.Path("M0 0 H"+width+" V"+height+" H0z M "+width/2+" 0 A "+width/2+" "+height/2+", 0, 1, 0, "+width/2+" "+height+" A "+width/2+" "+height/2+", 0, 1, 0, "+width/2+" 0z");
                path.set({strokeWidth: 0, isAlwaysOnTop: true, fill: '#ffffff', selectable: false, evented: false});
                _canvas.add(path);                  
            }
            _canvas.requestRenderAll();
        });
        $scope.afterInnsertTemplate();
    };
    $scope.firstTimeLoadedTemplate = true;
    $scope.firstTimeShowTemplateFiledsPopup = true;
    $scope.afterInnsertTemplate = function(){      
        if( $scope.settings.ui_mode == 1 || $scope.settings.ui_mode == 2 ){
            var showTourGuide = localStorage.getItem('showTourGuide');
            if( !showTourGuide ){
                $scope.startTourGuide();
            }else{
                $scope.showTemplateFiledsPopup();
            }
        }
        $scope.firstTimeLoadedTemplate = false;
        $scope.completedInsertTemplate = true;
        $scope.onLoadPrintingOptions   = false;
        if( $scope.awaitSubmitForm ){
            $scope.saveData();
        }
    };
    $scope.showTemplateFiledsPopup = function( force ){
        if( !$scope.settings.is_mobile && ( $scope.settings.ui_mode == 1 || $scope.settings.ui_mode == 2 ) && NBDESIGNCONFIG.nbdesigner_enable_template_mapping == 'yes' 
                && $scope.firstTimeShowTemplateFiledsPopup && NBDESIGNCONFIG.is_logged != 1 && !$scope.tourGuideShowing && $scope.templateHolderFields.length > 0 ){
            $timeout(function(){
                jQuery( '.popup-template-fields' ).nbShowPopup();
                $scope.firstTimeShowTemplateFiledsPopup = false;
            }, 400);
        }
        if( angular.isDefined( force ) ){
            jQuery( '.popup-template-fields' ).nbShowPopup();
        }
    };
    $scope.updateTemplateFields = function(){
        if( $scope.templateHolderFields.length > 0 ){
            _.each($scope.templateHolderFields, function(field, index){
                if( field.value != '' ){
                    _.each($scope.stages, function(stage, index){
                        var _canvas = stage.canvas;
                        _canvas.forEachObject(function(obj) { 
                            if( angular.isDefined( obj.field_mapping ) && field.key == obj.field_mapping ){
                                obj.text = field.value;
                            }
                        });
                        _canvas.calcOffset();
                        _canvas.requestRenderAll();
                    });
                }
            });
        }
        jQuery( '.popup-template-fields .close-popup' ).triggerHandler('click');
    };
    $scope.duplicateStageIndex = -1;
    $scope.addStage = function( duplicate ){
        var new_stage = {}, new_side_config = {}, stageLen = $scope.stages.length, currentStage = $scope.currentStage;
        var _canvas = $scope.stages[$scope.currentStage].canvas;
        angular.copy($scope.stages[$scope.currentStage], new_stage);
        if( angular.isDefined($scope.settings.product_data.option.new_side_name) ){
            if( angular.isUndefined($scope.settings.product_data.origin_product) ){
                $scope.settings.product_data.origin_product = [];
                angular.copy($scope.settings.product_data.product, $scope.settings.product_data.origin_product);
            }
            new_stage.config.name = $scope.settings.product_data.option.new_side_name;
            var max_number_side = parseInt($scope.settings.product_data.option.max_number_side);
            if( ($scope.settings.product_data.origin_product.length + max_number_side ) ==  $scope.stages.length ){
                $scope.resource.canAdd = false;
                return;
            }
        }
        $scope.preventLoadDesign = true;
        angular.copy($scope.settings.product_data.product[$scope.currentStage], new_side_config);
        $scope.settings.product_data.product.splice($scope.currentStage + 1, 0, new_side_config);
//        var new_qty = {};
//        angular.copy($scope.resource.config.qtys[$scope.currentStage], new_qty);
//        new_qty.value = 1;
//        $scope.resource.config.qtys.splice($scope.currentStage + 1, 0, new_qty);
//        if( $scope.resource.config.qtys[0].value > 1 ) $scope.resource.config.qtys[0].value -= 1;
//        $scope.updateQtys();
        $scope.resource.config.qty++;
        if( duplicate ){
            if(stageLen > ($scope.currentStage + 1)){
                $scope.duplicateStageIndex = $scope.currentStage + 1;
            }
            $scope.resource.jsonDesign[$scope.currentStage + 1] = {};
            angular.copy(_canvas.toJSON($scope.includeExport), $scope.resource.jsonDesign[$scope.currentStage + 1]);
        }
        $scope.stages.splice($scope.currentStage + 1, 0, new_stage);
        $scope.updateApp();
        if( angular.isDefined($scope.settings.product_data.option.max_number_side) ){
            if( ($scope.settings.product_data.origin_product.length + parseInt( $scope.settings.product_data.option.max_number_side ) ) ==  $scope.stages.length ){
                $scope.resource.canAdd = false;
            }
        };
        $timeout(function(){
            $scope.switchStage($scope.currentStage, 'next');
            if(stageLen == (currentStage + 1)){
                $scope.contextAddLayers = 'template';
                $scope.onloadTemplate = true;
                $scope.stages[stageLen].canvas.loadFromJSON($scope.resource.jsonDesign[stageLen], function() {
                    $scope.onloadTemplate = false;
                    $scope.contextAddLayers = 'normal';
                });
            }
        }, 100);
    }; 
    $scope.removeStage = function( index ){
        var currentStage = angular.isDefined(index) ? index : $scope.currentStage;
        if($scope.currentStage > 0){
            $scope.currentStage--;
            $scope.switchStage(currentStage, 'prev', 'top-bottom', true);
        }
        $scope.saveDesign();
        $scope.settings.product_data.product.splice(currentStage, 1);
        $scope.stages.splice(currentStage, 1);
        $scope.updateApp();
        $scope.closePopup('.popup-nbd-delete-stage-alert');
//        $scope.resource.config.qtys[0].value += 1;
//        $scope.updateQtys();
//        $scope.resource.config.qtys.splice(currentStage, 1);
        $scope.resource.config.qty--;
        $scope.resource.canAdd = true;
        $timeout(function(){
            
        }, 100);
    };
    $scope.closePopup = function( popup ){
        jQuery(popup + ' .close-popup').triggerHandler('click');
    };
    $scope.resetStage = function(){
        var new_stage = {};              
        angular.copy($scope.stages[0], new_stage);
        $scope.stages = [];
        $scope.stages.push(new_stage);
        $scope.currentStage = 0;
        $scope.preventLoadDesign = true;        
    };
    $scope.setStageDimension = function(id){
        id = angular.isDefined(id) ? id : $scope.currentStage;
        var _stage = $scope.stages[id];
        var currentWidth = _stage.config.width * _stage.states.scaleRange[_stage.states.currentScaleIndex].ratio,
            currentHeight = _stage.config.height * _stage.states.scaleRange[_stage.states.currentScaleIndex].ratio;
        $scope.stages[id]['canvas'].setDimensions({'width' : currentWidth, 'height' : currentHeight});
    };
    $scope.switchStage = function(id, command, direction, onRemoveStage){
        var idCurrentStage = 'stage-container-' + id,
            next =  parseInt(id) + 1;   
        if(command == 'prev')  {
            next  = parseInt(id) - 1;
        };
        jQuery('.temporary-hidden').addClass('nb-opacity-0');
        $timeout(function(){
            jQuery('.temporary-hidden').removeClass('nb-opacity-0');
        }, 600);
        jQuery('.nbd-stages .ps__scrollbar-x-rail, .nbd-stages .ps__scrollbar-y-rail').addClass('nbd-hiden');
        $timeout(function(){
            jQuery('.nbd-stages .ps__scrollbar-x-rail, .nbd-stages .ps__scrollbar-y-rail').removeClass('nbd-hiden');
        }, 700);
        direction = angular.isDefined(direction) ? direction : 'top-bottom';
        var fadeInNext = 'fadeInUp', fadeInPrev = 'fadeInDown', fadeOutNext = 'fadeOutDown', fadeOutPrev = 'fadeOutUp';
        if( direction == 'left-right' ){
            fadeInNext = 'fadeInRight'; 
            fadeInPrev = 'fadeInLeft';
            fadeOutNext = 'fadeOutRight';
            fadeOutPrev = 'fadeOutLeft';
        };
        //if(appConfig.isModern){            
            var idNextStage = 'stage-container-' + next;
            var currentStage = angular.element(document.getElementById(idCurrentStage)),
            nextStage = angular.element(document.getElementById(idNextStage));
            if( direction == 'left-right' ){
                currentStage.removeClass('fadeInUp').removeClass('fadeInDown').removeClass('fadeOutDown').removeClass('fadeOutUp');
                nextStage.removeClass('fadeInUp').removeClass('fadeInDown').removeClass('fadeOutDown').removeClass('fadeOutUp');
            }else{
                currentStage.removeClass('fadeInLeft').removeClass('fadeInRight').removeClass('fadeOutRight').removeClass('fadeOutLeft');
                nextStage.removeClass('fadeInLeft').removeClass('fadeInRight').removeClass('fadeOutRight').removeClass('fadeOutLeft');
            }
            currentStage.addClass('animated');    
            currentStage.removeClass(fadeInNext);
            currentStage.removeClass(fadeInPrev);  
            nextStage.addClass('animated');
            nextStage.removeClass('hidden');            
            nextStage.removeClass(fadeOutNext);            
            nextStage.removeClass(fadeOutPrev);
            if(command == 'prev'){
                currentStage.removeClass(fadeOutNext);
                currentStage.addClass(fadeOutPrev);
                nextStage.addClass(fadeInPrev);                 
            }else {
                currentStage.removeClass(fadeOutPrev);
                currentStage.addClass(fadeOutNext);    
                nextStage.addClass(fadeInNext);                 
            }; 
        //}
        if(angular.isUndefined(onRemoveStage)) $scope.currentStage = next;
        if(appConfig.isVisual){
            $scope.deactiveAllLayer();
        }
        //$scope.updateApp();
        //if(angular.isUndefined(onRemoveStage)){
        $scope.renderStage();
        $scope.updateLayersList();
    };
    $scope.onDrop = function(event){
        event.preventDefault();
        var src = event.originalEvent.dataTransfer.getData("src"),
        extenal = event.originalEvent.dataTransfer.getData("extenal"),
        type = event.originalEvent.dataTransfer.getData("type");
        switch(type){
            case 'image':
                if(extenal == 'true'){
                    $scope.addImageFromUrl(src, true);
                }else{
                    $scope.addImageFromUrl(src, false);
                };
                break;
            case 'svg':   
                $scope.addImageFromUrl(src, false);
                break;
            case 'typo':   
                $scope.insertTypography({folder: src});
                break;                
        };
    };
    $scope.onMouseDown = function(id, options){
        //angular.merge($scope.stages[$scope.currentStage].states.boundingObject, {visibility: 'hidden'}); 
        $scope.stages[$scope.currentStage].states.isShowToolBox = true;
        $scope.updateApp();
    };
    $scope.onDblclick = function(options){
        var item = options.target;
        if( item && item.type === 'activeSelection' ){
            $scope.deactiveAllLayer()
        }
    };    
    $scope.onMouseOverStage = function(id, options){
        var item = options.target;
        $scope.updateBoundingRect(item);
        $scope.updateApp();
    };   
    $scope.onMouseOutStage = function(id, options){
        if(options.target){  
            angular.merge($scope.stages[$scope.currentStage].states.boundingObject, {visibility: 'hidden'}); 
        }       
        $scope.updateApp();
    }; 
    $scope.onMouseMoveStage = function(id, options){
        if( $scope.settings.showRuler && appConfig.isModern ){
            var _stage = $scope.stages[$scope.currentStage];
            var pointer = _stage.canvas.getPointer(options.e);
            var x = (pointer.x + _stage.config.top) * _stage.states.scaleRange[_stage.states.currentScaleIndex].ratio,
            y = (pointer.y + _stage.config.top) * _stage.states.scaleRange[_stage.states.currentScaleIndex].ratio;
            _stage.hozRuler.updateCursorIndicator({x: x, y: y});
            _stage.verRuler.updateCursorIndicator({x: x, y: y});
        }
    };   
    $scope.onMouseUpStage = function(id, options){
        var _stage = $scope.stages[$scope.currentStage];
        /*
         * Hide bounding rect, coordinates label, snap lines, rotate label
         */
        angular.merge(_stage.states.boundingObject, {visibility: 'hidden'});
        angular.merge(_stage.states.coordinates, {style: {visibility: 'hidden'}}); 
        var position = {
            ht: {top: -9999},
            hb: {top: -9999},
            hc: {top: -9999},
            vl: {left: -9999},
            vr: {left: -9999},
            vc: {left: -9999},
            hcc: {top: -9999},
            vcc: {left: -9999},
            vel: {left: -9999},
            ver: {left: -9999},
            het: {top: -9999},
            heb: {top: -9999}
        };        
        angular.merge(_stage.states.snaplines, position);
        angular.merge(_stage.states.rotate, {style: {visibility: 'hidden'}});       
        /* get layer parameters before modify */
        var _canvas = _stage.canvas,
            objects =  _canvas.getActiveObjects();  
        _stage.tempParameters = null;
        if( objects.length == 1 ){
            _stage.tempParameters = JSON.stringify(objects[0].toJSON());
        }
        /* Delete layer if out of stage fully */
        if( options.target ){
            if(!options.target.isOnScreen()) $scope.deleteLayers();
        }        
        $scope.updateApp(); 
    };  
    $scope.beforeObjectModify = function( item ){
        $scope.setHistory({
            element: item,
            parameters: JSON.stringify(item.toJSON()),
            interaction: 'modify'
        });
    };
    $scope.onPathCreated = function(id, options){
        //todo
    };   
    $scope.onObjectAdded = function(id, options){
        var _stage = $scope.stages[$scope.currentStage],
        _canvas = _stage['canvas'],
        item = options.target,   
        d = new Date(),
        itemId = d.getTime() + Math.floor(Math.random() * 1000);
        if( $scope.contextAddLayers == 'normal' || $scope.contextAddLayers == 'copy' || $scope.contextAddLayers == 'template' ){
            if( angular.isUndefined( item.get('itemId') ) ){
                item.set({"itemId" : itemId});
            }
        };
        if( $scope.contextAddLayers == 'normal' && !$scope.resource.drawMode.status ){
            _stage.states.isShowToolBox = true;
            _canvas.viewportCenterObject(item);
            $scope.toggleTip();
        }
        var type = item.get('type');
        if( $scope.resource.upload.ilr ){
            item.set({ilr: true});
            $scope.resource.upload.ilr = false;
        }
        if(type == 'i-text' || type == 'textbox' || type == 'text' || type == 'curvedText') item.initDimensions();
        item.setCoords();
        if( (['normal', 'copy', 'undo', 'redo'].indexOf($scope.contextAddLayers) > -1) && _stage.config.area_design_type == "2" )  $scope.setStackLayerAlwaysOnTop();
        var top = item.get('top'),
            left = item.get('left');
        if( $scope.contextAddLayers == 'normal' && !$scope.resource.drawMode.status ){
            item.set({top: top - 50});
            item.animate('top', top, {
                duration: 400,
                onChange: function(){
                    $scope.renderStage();
                },
                onComplete: function(){
                    _canvas.setActiveObject(item); 
                    $scope.setHistory({
                        element: item,
                        parameters: JSON.stringify(item.toJSON()),
                        interaction: 'add'
                    });   
                    $scope.stages[$scope.currentStage].tempParameters = JSON.stringify(item.toJSON());   
                    if(type == 'i-text' || type == 'textbox'){
                        item.selectAll();
                        item.enterEditing();
                    }
                    $scope.renderStage();       
                },
                easing: FabricWindow.util.ease['easeInQuad']
            });  
            $scope.stages[$scope.currentStage].states.isActiveLayer = true;
        }else if( $scope.contextAddLayers == 'copy' ){
            item.set({top: top - 20, left: left - 20});
            item.animate({'top': top, 'left': left}, {
                duration: 400,
                onChange: function(){
                    $scope.renderStage();
                },
                onComplete: function(){
                    $scope.setHistory({
                        element: item,
                        parameters: JSON.stringify(item.toJSON()),
                        interaction: 'add'
                    });   
                    $scope.stages[$scope.currentStage].tempParameters = JSON.stringify(item.toJSON());                    
                    $scope.renderStage();       
                },
                easing: FabricWindow.util.ease['easeInQuad']
            });             
        }else{
            $scope.renderStage();
        };
        if(!$scope.onloadTemplate){
            if($scope.onUnloadGroup.status){
                if( angular.isDefined($scope.onUnloadGroup.prevIndex) ){
                    item.moveTo( parseInt( $scope.onUnloadGroup.prevIndex ) + $scope.onUnloadGroup.length - $scope.onUnloadGroup.remain );
                }           
                if(type == 'i-text' || type == 'textbox' || type == 'text' || type == 'curvedText'){
                    item.set({editable: true});
                }
                if( $scope.onUnloadGroup.remain > 1 ){
                    $scope.onUnloadGroup.remain -= 1;
                }else{
                    $scope.onUnloadGroup = {
                        status: false,
                        remain: 0
                    };  
                    $scope.contextAddLayers = 'normal';
                }
            }else{
                $scope.contextAddLayers = 'normal';
            }
        }
        if( $scope.contextAddLayers == 'normal' && !$scope.resource.drawMode.status ){
            $scope.showDesignTab();
        }
        $scope.updateLayersList();
    };
    $scope.showDesignTab = function(){
        if( checkMobileDevice() ){
            jQuery('#design-tab').triggerHandler('click');
        };    
    };
    $scope.onObjectScaling = function(id, options){
        var item = options.target; 
        $scope.updateAssociateLayer(item);
        $scope.stages[$scope.currentStage].states.isShowToolBox = false;
    };    
    $scope.onObjectScaled = function (id, options) {
        //var item = options.target;
        //$scope.updateAssociateLayer(item);
        $scope.stages[$scope.currentStage].states.isShowToolBox = true;
        $scope.setPositionToolbox();
    };
    $scope.onObjectMoving = function(id, options){
        var item = options.target;
        if($scope.settings.snapMode.status){
            //$scope.snapLayer(item);
        }
        $scope.updateAssociateLayer(item); 
        $scope.stages[$scope.currentStage].states.isShowToolBox = false;
    }; 
    $scope.onObjectMoved = function (id, options) {
        //var item = options.target;
        $scope.stages[$scope.currentStage].states.isShowToolBox = true;
        $scope.setPositionToolbox();
        //$scope.updateAssociateLayer(item);
    };
    $scope.onObjectRotating = function(id, options){
        var item = options.target;
        $scope.updateAssociateLayer(item);
        $scope.updateAngleLabel(item);
    };   
    $scope.updateAssociateLayer = function(item){
        if(item){ 
            item.setCoords();
            $scope.updateCoordenatesLabel(item);
            $scope.updateBoundingRect(item);
            $scope.updateSnapLines();
            $scope.updateUploadZone(item);
            $scope.updateApp();     
        }
    };
    $scope.snapLayer = function(item){
        if(item){
            var _canvas = $scope.stages[$scope.currentStage]['canvas'],
            stage = $scope.stages[$scope.currentStage],
            left = item.get('left'),
            top = item.get('top'),
            translateLeft = 0,
            translateTop = 0,
            threshold = 4,
            scale = stage.states.scaleRange[stage.states.currentScaleIndex].ratio;
            var bound = item.getBoundingRect(true);
            switch ($scope.settings.snapMode.type) {
                case 'layer':
                    _canvas.forEachObject(function(obj) {
                        if( obj === item ) return;
                        var _bound = obj.getBoundingRect(true);
                        if(Math.abs(bound.left - _bound.left) < threshold){
                            translateLeft = bound.left - _bound.left;
                            console.log(translateLeft);
                        }
//                        if(Math.abs(bound.left + bound.width - _bound.left) < 1){
//                            if( Math.abs(bound.left + bound.width - _bound.left) < Math.abs(translateLeft)  ) translateLeft = bound.left + bound.width - _bound.left;
//                        }
//                        if(Math.abs(bound.left - _bound.left - _bound.width) < 1){
//                            if( Math.abs(bound.left - _bound.left - _bound.width) < Math.abs(translateLeft)  ) translateLeft = bound.left - _bound.left - _bound.width;
//                        }
//                        if(Math.abs(bound.left + bound.width - _bound.left - _bound.width) < 1){
//                            if( Math.abs(bound.left + bound.width - _bound.left - _bound.width) < Math.abs(translateLeft)  ) translateLeft = bound.left + bound.width - _bound.left - _bound.width;
//                        }
//                        if(Math.abs(bound.left + bound.width / 2 - _bound.left - _bound.width / 2) < 1){
//                            if( Math.abs(bound.left + bound.width / 2 - _bound.left - _bound.width / 2) < Math.abs(translateLeft)  ) translateLeft = bound.left + bound.width / 2 - _bound.left - _bound.width / 2;
//                        }
                        
                        
//                        if(Math.abs(bound.top - _bound.top) < 1)  position.ht.top = _bound.top;
//                        if(Math.abs(bound.top + bound.height - _bound.top) < 1) position.ht.top = _bound.top;
//                        if(Math.abs(bound.top - _bound.top - _bound.height) < 1)  position.hb.top = _bound.top + _bound.height;
//                        
//                        if(Math.abs(bound.top + bound.height - _bound.top - _bound.height) < 1)  position.hb.top = _bound.top + _bound.height;
//                        
//                        if(Math.abs(bound.top + bound.height / 2 - _bound.top - _bound.height / 2) < 1) position.hc.top = _bound.top + _bound.height / 2;  
                    });
                    item.set({
                        left: item.left - translateLeft
                    });                    
                    break;
                case 'bounding':
                    if(  Math.abs( bound.left - _canvas.width ) < threshold ){
                        translateLeft = ( bound.left - _canvas.width ) / scale;
                    }else if( Math.abs( bound.left + bound.width - _canvas.width ) < threshold ){
                        translateLeft = ( _canvas.width - bound.width - bound.left) / scale;
                    }else if( Math.abs( item.left ) < threshold ){
                        translateLeft = item.left;
                    }
//                    if(  Math.abs( bound.top - _canvas.height ) < threshold ){
//                        translateTop = ( bound.top - _canvas.height ) / scale;
//                    }else if( Math.abs( bound.top + bound.height - _canvas.height ) < threshold ){
//                        translateTop = ( _canvas.height - bound.height) / scale;
//                    }else if( Math.abs( item.top ) < threshold ){
//                        translateTop = item.top;
//                    }
                    break;
                case 'grid':
                    var grid = 10;
                    if ( (item.left * scale - Math.round(item.left * scale / grid) * grid) < threshold ){
                        item.set({
                            left: Math.round(item.left * scale / grid) * grid / scale
                        });
                        item.setCoords();
                    }
                    if ( (item.top * scale - Math.round(item.top * scale / grid) * grid) < threshold ){
                        item.set({
                            top: Math.round(item.top * scale / grid) * grid / scale
                        });
                        item.setCoords();
                    }
                    break;
            }
//            if( $scope.settings.snapMode == 'layer' || $scope.settings.snapMode == 'bounding' ){
//                item.set({
//                    left: item.left - translateLeft,
//                    top: item.top - translateTop
//                });
//                item.setCoords();
//                console.log(item.getBoundingRect(true));
//                console.log(item.width * item.scaleX);
//            }
        }
    };
    $scope.updateUploadZone = function(item){
        var type = item.get('type'),
        elementUpload = item.get('elementUpload');
        if( (type == 'image' || type == 'custom-image' ) && elementUpload  ){
            angular.merge($scope.stages[$scope.currentStage].states.uploadZone, {
                visibility: 'visible',
                top: item.oCoords.tl.y - 1,
                left: item.oCoords.tl.x - 1,
                width: Math.sqrt(Math.pow(item.oCoords.tl.x - item.oCoords.tr.x, 2) + Math.pow(item.oCoords.tl.y - item.oCoords.tr.y, 2 )) + 2,
                height: Math.sqrt(Math.pow(item.oCoords.tl.x - item.oCoords.bl.x, 2) + Math.pow(item.oCoords.tl.y - item.oCoords.bl.y, 2 )) + 2,
                transform: "rotate("+item.angle+"deg)"                
            });
        }else{
            angular.merge($scope.stages[$scope.currentStage].states.uploadZone, {visibility: 'hidden'}); 
        }
    };
    $scope.updateAngleLabel = function( item ){
        if(item){
            angular.merge($scope.stages[$scope.currentStage].states.rotate, {
                style: {
                    transform: "rotate("+item.angle+"deg)",
                    top: item.oCoords.mtr.y,
                    left: item.oCoords.mtr.x,
                    visibility: 'visible'                    
                },
                angle: fabric.util.toFixed(item.angle, 0)
            });
        }else{
            angular.merge($scope.stages[$scope.currentStage].states.rotate, {style: {visibility: 'hidden'}});   
        }
    };
    $scope.updateCoordenatesLabel = function(item){
        if(item){
            var bound = item.getBoundingRect();
            var top = item.oCoords.tl.y,
                left = item.oCoords.tl.x;
            if( (item.angle > 315 && item.angle < 360) || (item.angle > 45 && item.angle < 90) 
                    || (item.angle > 135 && item.angle < 180) || (item.angle > 225 && item.angle < 270) ){
                if( item.oCoords.tr.x < left ){
                    top = item.oCoords.tr.y;
                    left = item.oCoords.tr.x;
                }
                if( item.oCoords.br.x < left ){
                    top = item.oCoords.br.y;
                    left = item.oCoords.br.x;
                }   
                if( item.oCoords.bl.x < left ){
                    top = item.oCoords.bl.y;
                    left = item.oCoords.bl.x;
                }            
            }else{
                if( item.oCoords.tr.y < top ){
                    top = item.oCoords.tr.y;
                    left = item.oCoords.tr.x;
                }
                if( item.oCoords.br.y < top ){
                    top = item.oCoords.br.y;
                    left = item.oCoords.br.x;
                }   
                if( item.oCoords.bl.y < top ){
                    top = item.oCoords.bl.y;
                    left = item.oCoords.bl.x;
                }
            }
            angular.merge($scope.stages[$scope.currentStage].states.coordinates, {      
                style: {
                    visibility: 'visible',
                    top: top,
                    left: left                        
                },
                top: parseInt(top),
                left: parseInt(left)
            });  
        }
    };
    $scope.updateBoundingRect = function(item){
        var stage = $scope.stages[$scope.currentStage];
        if(item){
            var bound = item.getBoundingRect();
            angular.merge(stage.states.boundingObject, {
                visibility: 'visible',
                top: item.oCoords.tl.y - 1,
                left: item.oCoords.tl.x - 1,
                width: Math.sqrt(Math.pow(item.oCoords.tl.x - item.oCoords.tr.x, 2) + Math.pow(item.oCoords.tl.y - item.oCoords.tr.y, 2 )) + 2,
                height: Math.sqrt(Math.pow(item.oCoords.tl.x - item.oCoords.bl.x, 2) + Math.pow(item.oCoords.tl.y - item.oCoords.bl.y, 2 )) + 2,
                transform: "rotate("+item.angle+"deg)"
            });
            stage.states.boundingRealSize = {
                size: (item.width * item.scaleX / stage.config.cwidth * stage.config.pWidth).toFixed(2) +'x'+ (item.height * item.scaleY / stage.config.cheight * stage.config.pHeight).toFixed(2) + '' + $scope.settings.nbdesigner_dimensions_unit,
                transform: (item.angle > 270 || item.angle < 90 ) ? "rotate(0deg)" : "rotate(180deg)"
            };
            $scope.updateApp();
        }else{
            angular.merge(stage.states.boundingObject, {visibility: 'hidden'});   
            angular.merge(stage.states.coordinates, {visibility: 'hidden'});   
        }
    };
    $scope.updateSnapLines = function(){
        var _canvas = this.stages[$scope.currentStage]['canvas'],
            item = _canvas.getActiveObject(),
            position = {
                ht: {top: -9999},
                hb: {top: -9999},
                hc: {top: -9999},
                vl: {left: -9999},
                vr: {left: -9999},
                vc: {left: -9999},
                hcc: {top: -9999},
                vcc: {left: -9999},
                vel: {left: -9999},
                ver: {left: -9999},
                het: {top: -9999},
                heb: {top: -9999}                
            };
        if( item ){
            var bound = item.getBoundingRect();
            _canvas.forEachObject(function(obj) {
                if( obj === item ) return;
                var _bound = obj.getBoundingRect();
                if(Math.abs(bound.left - _bound.left) < 1)  position.vl.left = _bound.left;
                if(Math.abs(bound.left + bound.width - _bound.left) < 1) position.vr.left = _bound.left;
                if(Math.abs(bound.left - _bound.left - _bound.width) < 1)  position.vl.left = _bound.left + _bound.width;
                if(Math.abs(bound.top - _bound.top) < 1)  position.ht.top = _bound.top;
                if(Math.abs(bound.top + bound.height - _bound.top) < 1) position.ht.top = _bound.top;
                if(Math.abs(bound.top - _bound.top - _bound.height) < 1)  position.hb.top = _bound.top + _bound.height;
                if(Math.abs(bound.left + bound.width - _bound.left - _bound.width) < 1)  position.vr.left = _bound.left + _bound.width;
                if(Math.abs(bound.top + bound.height - _bound.top - _bound.height) < 1)  position.hb.top = _bound.top + _bound.height;
                if(Math.abs(bound.left + bound.width / 2 - _bound.left - _bound.width / 2) < 1) position.vc.left = _bound.left + _bound.width / 2;
                if(Math.abs(bound.top + bound.height / 2 - _bound.top - _bound.height / 2) < 1) position.hc.top = _bound.top + _bound.height / 2;  
            });
            if(Math.abs(bound.left + bound.width / 2 - _canvas.width / 2) < 1)  position.vcc.left = _canvas.width / 2;
            if(Math.abs(bound.top + bound.height / 2 - _canvas.height / 2) < 1)  position.hcc.top = _canvas.height / 2;
            
            if(Math.abs(bound.left) < 1)  position.vel.left = 0;
            if(Math.abs(bound.top) < 1)  position.het.top = 0;
            if(Math.abs(bound.left + bound.width - _canvas.width) < 1)  position.ver.left = _canvas.width;
            if(Math.abs(bound.top + bound.height - _canvas.height) < 1)  position.heb.top = _canvas.height;
            
            angular.merge($scope.stages[$scope.currentStage].states.snaplines, position);
        }
    };
    $scope.updateWarning = function(item){
        if($scope.settings.showWarning.oos){
            var stage = $scope.stages[$scope.currentStage];
            var _canvas = stage['canvas'],
            bound = item.getBoundingRect();
            if( bound.left < 0 || bound.top < 0 || (bound.left + bound.width) > (_canvas.width)|| (bound.top + bound.height) > (_canvas.height)  ){
                stage.states.oos = true;
            }else{
                stage.states.oos = false;
            }
            $scope.updateApp();
        }
    };
    $scope.onObjectModified = function(id, options){
        var item = options.target;
        var newAngle = item.angle;
        newAngle = (Math.abs(item.angle - 0) <= 1 || Math.abs(item.angle - 360) <= 1 ) ? 0 : ( Math.abs(item.angle - 180) <= 1 ? 180 : newAngle );
        item.set({angle: newAngle, dirty: true});    
        if( $scope.stages[$scope.currentStage].tempParameters ){
            $scope.setHistory({
                element: item,
                parameters: $scope.stages[$scope.currentStage].tempParameters,
                interaction: 'modify'
            });
            $scope.stages[$scope.currentStage].tempParameters = null;
        }
        if( $scope.stages[$scope.currentStage].states.isText && $scope.stages[$scope.currentStage].states.type != 'curvedText' && NBDESIGNCONFIG.nbdesigner_enable_text_free_transform == 'no'){
            var newFontSize = item.fontSize * item.scaleX;
            var lastScaleX = item.scaleX;
            var newPtFontSize = newFontSize / $scope.stages[$scope.currentStage].states.ratioConvertFont;
            newPtFontSize = newPtFontSize.toFixed(2);
            item.set({
                dirty: true,
                scaleX: 1,
                scaleY: 1,
                fontSize: newFontSize,
                ptFontSize: newPtFontSize,
                width: item.width * lastScaleX
            });
            $scope.stages[$scope.currentStage].states.text.fontSize = newFontSize;
            $scope.stages[$scope.currentStage].states.text.ptFontSize = newPtFontSize;
        }
//        if($scope.stages[$scope.currentStage].states.type == 'curvedText'){
//            var newRadius = item.radius * item.scaleX;
//            var newFontSize = item.fontSize * item.scaleX;
//            var newPtFontSize = newFontSize / $scope.stages[$scope.currentStage].states.ratioConvertFont;
//            item.set({
//                scaleX: 1,
//                scaleY: 1,
//                radius: newRadius,
//                fontSize: newFontSize,
//                ptFontSize: newPtFontSize
//            });
//            $scope.stages[$scope.currentStage].states.text.radius = newRadius;
//        }
        $scope.updateWarning(item);
    };  
    $scope.updateTextPtFontSize = function (item) {
        var textTypes = ['i-text', 'text', 'textbox'],
            itemType = item.get('type');
        if (textTypes.indexOf(itemType) > -1) {
            if (NBDESIGNCONFIG.nbdesigner_enable_text_free_transform == 'no') {
                var newFontSize = item.fontSize * item.scaleX;
                var lastScaleX = item.scaleX;
                var newPtFontSize = newFontSize / $scope.stages[$scope.currentStage].states.ratioConvertFont;
                newPtFontSize = newPtFontSize.toFixed(2);
                item.set({
                    dirty: true,
                    scaleX: 1,
                    scaleY: 1,
                    fontSize: newFontSize,
                    ptFontSize: newPtFontSize,
                    width: item.width * lastScaleX
                });
            }
        }
    };
    $scope.onBeforeRender = function(id, options){
        //todo
    };      
    $scope.onAfterRender = function(id, options){
        var stage = $scope.stages[id],
        _canvas = stage['canvas'];
        function checkLayerById( id ){
            var check = false;
            _canvas.forEachObject(function(obj) {
                if(obj.get('itemId') == id) check = true;
            });
            return check;
        }
        $scope.stages[$scope.currentStage].states.lostCharLayers = $scope.stages[$scope.currentStage].states.lostCharLayers.filter(function(layerId){
            return checkLayerById( layerId );
        });
    };    
    $scope.onSelectionCleared = function(id, options){
        $scope.stages[$scope.currentStage].states.isActiveLayer = false;
        $scope.stages[$scope.currentStage].states.itemId = null;
        $scope.stages[$scope.currentStage].states.isEditing = false;
        angular.merge($scope.stages[$scope.currentStage].states.uploadZone, {visibility: 'hidden'});
        $scope.stages[$scope.currentStage].states.elementUpload = false;
        $scope.stages[$scope.currentStage].states.oos = false;
        $scope.stages[$scope.currentStage].states.ilr = false;
    };     
    $scope.onEditingEntered = function(id, options){
        $scope.stages[$scope.currentStage].states.isEditing = true;     
        $scope.updateApp();
    };    
    $scope.onEditingExited = function(id, options){
        $scope.stages[$scope.currentStage].states.isEditing = false;
        var item = options.target;
        if( item ){
            $scope.updateLayersList();
        }
        $scope.updateApp();
    };    
    $scope.onSelectionChanged = function(id, options){
        $scope.getCurrentLayerInfo();
        $scope.updateUploadZone(options.target);
        $scope.updateWarning(options.target);
        if( $scope.stages[$scope.currentStage].states.isGroup ){
            $scope.stages[$scope.currentStage].states.ilr = false;
        }else if( ($scope.stages[$scope.currentStage].states.isShape || $scope.stages[$scope.currentStage].states.isPath) && NBDESIGNCONFIG.nbdesigner_clipart_rotate == '0' ){
            var item = options.target;
            $scope.hideRotateControl( item );
        }
        $scope.scrollToSelectedLayer();
    }; 
    $scope.onSelectionUpdated = function(id, options){
        $scope.getCurrentLayerInfo();
        $scope.updateUploadZone(options.target);
        $scope.updateWarning(options.target);
        if( $scope.stages[$scope.currentStage].states.isGroup ){
            $scope.stages[$scope.currentStage].states.ilr = false;
        }  
        $scope.scrollToSelectedLayer();
        if( ($scope.stages[$scope.currentStage].states.isShape || $scope.stages[$scope.currentStage].states.isPath) && NBDESIGNCONFIG.nbdesigner_clipart_rotate == '0' ){
            var item = options.target;
            $scope.hideRotateControl( item );
        }
    };
    $scope.onSelectionCreated = function(id, options){
        $scope.getCurrentLayerInfo();
        $scope.updateUploadZone(options.target);
        $scope.updateWarning(options.target);
        $scope.toggleTip();
        $scope.scrollToSelectedLayer();
        if( ($scope.stages[$scope.currentStage].states.isShape || $scope.stages[$scope.currentStage].states.isPath) && NBDESIGNCONFIG.nbdesigner_clipart_rotate == '0' ){
            var item = options.target;
            $scope.hideRotateControl( item );
        }
    }; 
    $scope.hideRotateControl = function( item ){
        if( item != null ){
            item.set({
                _controlsVisibility: {
                    tl: true,
                    tr: true,
                    br: true,
                    bl: true,
                    ml: true,
                    mt: true,
                    mr: true,
                    mb: true,
                    mtr: false            
                }
            });
        }
    };
    $scope.scrollToSelectedLayer = function(){
//        if( $scope.stages[$scope.currentStage].states.isLayer ){
//            jQuery('#tab-layer .tab-scroll').stop().animate({
//                scrollTop: jQuery('.menu-layer li[data-id="'+$scope.stages[$scope.currentStage].states.itemId+'"]').position().top
//            }, 100);
//        }
    };
    $scope.onTextChanged = function(id, options){
        var item = options.target;
        if( item ){
//            if( item.get('fixedWidth') ){
//                var largest  = item.getLineWidth(0);
//                _.each(item.__lineWidths, function(line, index){
//                    var _width = item.getLineWidth(index);
//                    if( largest < _width ) largest = _width;
//                });
//                var largest  = item.getBoundingRect().width;
//                if (largest > item._fixedWidth) {
//                    var fontSize = item.get('fontSize');
//                    item.set({
//                        dirty: true,
//                        fontSize: fontSize * item._fixedWidth / (largest  + 1)
//                    });
//                }else{
//                    item.set({
//                        dirty: true,
//                        fontSize: item.get('_fixedFontSize')
//                    });                    
//                }
//                $scope.renderStage();
//            }
            if( angular.isDefined( item.field_mapping ) ){
                _.each($scope.stages, function(stage, index){
                    var _canvas = stage.canvas;
                    _canvas.forEachObject(function(obj) { 
                        if( angular.isDefined( obj.field_mapping ) && item.field_mapping == obj.field_mapping ){
                            obj.text = item.text;
                        }
                    });
                });
            }
            if( $scope.settings.nbdesigner_enable_text_check_lang == 'yes' ){
                $scope.checkCharacter( item );
            }
            $scope.updateLayersList();
        }
        angular.merge($scope.stages[$scope.currentStage].states.boundingObject, {visibility: 'hidden'});   
        $scope.updateApp();
    };
    $scope.checkCharacter = function( object ){
        var font = _.filter($scope.resource.font.data, { alias: object.get('fontFamily') })[0];
        var arr = [], 
            check = true,
            text = object.text.replace(/\r?\n|\r/gm, ''),
            fontWeight = object.get('fontWeight'),
            fontStyle = object.get('fontStyle'),
            itemId = object.get('itemId'),
            font_url = '';
        if( fontWeight == 'bold' ){
            if( fontStyle == 'italic' ){
                font_url = font.file.bi;
            } else {
                font_url = font.file.b;
            }
        } else {
            if( fontStyle == 'italic' ){
                font_url = font.file.i;
            } else {
                font_url = font.file.r;
            }
        }
        if( font_url == 1 ) return;
        if( font.type != 'google' ){
            font_url = NBDESIGNCONFIG['font_url'] + font_url;
        }else{
            font_url = font_url.replace('http:', 'https:');
        }
        opentype.load( font_url, function (err, font) {
            if( err ){
                console.log(err);
            }else{
                var glyphs = font.glyphs.glyphs;
                _.each(glyphs, function(glyph, index){
                    arr['g-' + glyph.unicode] = 1;
                });
                for(var i = 0; i < text.length; i++){
                    var uc_index = 'g-' + text.charCodeAt(i);
                    if( angular.isUndefined( arr[uc_index] ) ){
                        check = false;
                    }
                }
                var _index = _.findIndex($scope.stages[$scope.currentStage].states.lostCharLayers, function(layerId) { return layerId == itemId; });
                if( !check ){
                    object.set({lostChar: 1});
                    if( _index == -1 ) $scope.stages[$scope.currentStage].states.lostCharLayers.push( itemId );
                } else {
                    delete object.lostChar;
                    if( _index > -1 ) $scope.stages[$scope.currentStage].states.lostCharLayers.splice(_index, 1);
                }
                $scope.updateLayersList();
            }
        });
    };
    $scope.onClickDone = function () {
        $scope.stages[$scope.currentStage].states.isShowToolBox = false;
        $scope.updateApp();
        $scope.renderStage();
        jQuery('html,body').animate({
            scrollTop: jQuery("#nbd-vista-app").offset().top
        }, 'slow');
    };
    $scope.setPositionToolbox = function () {
        if( appConfig.isModern ) return;
        $timeout(function(){
            var _canvas = $scope.stages[$scope.currentStage]['canvas'],
                object = _canvas.getActiveObject(),
                objects = _canvas.getActiveObjects();

            var $vista = jQuery('.nbd-vista'),
                $stageActive = jQuery('.nbd-vista .stage.nbd-active'),
                $stageMainActive = jQuery('.nbd-vista .stage.nbd-active .stage-main'),
                $layout = jQuery('.nbd-vista .nbd-layout'),
                $toolMain = jQuery('.nbd-vista .v-toolbox .nbd-nav-tab[data-tab="tab-main-box"]');

            var bound =  object.getBoundingRect(),
                stage = $scope.stages[$scope.currentStage];

            var ratioScale = stage.states.scaleRange[stage.states.currentScaleIndex].ratio,
                paddingL = parseInt($stageActive.css('padding-left').slice(0, -2)),
                paddingT = parseInt($stageActive.css('padding-top').slice(0, -2)),
                paddingB = parseInt($stageActive.css('padding-bottom').slice(0, -2)),
                paddingR = parseInt($stageActive.css('padding-right').slice(0, -2)),
                pLeft = 0,
                pTop = 0,
                transform = 'translateX(-50%)',
                _canvasWidth = _canvas.width,
                _canvasHeight = _canvas.height,
                mTop = stage.config.top * ratioScale,
                mBottom = (stage.config.cheight - stage.config.top - stage.config.height) * ratioScale,
                mRight = (stage.config.cwidth - stage.config.left - stage.config.width) * ratioScale,
                mLeft = stage.config.left * ratioScale,
                boundTop = bound.top + bound.height,
                boundLeft = bound.left + bound.width,
                _boundLeft = bound.left + bound.width / 2,
                scrollXHeight = 5,
                scrollYWidth = 5,
                scrollTop = $stageActive.scrollTop(),
                scrollLeft = $stageActive.scrollLeft();
            $toolMain.each(function (index, value) {
                jQuery(this).triggerHandler('click');
            });    

            if( $stageMainActive.height() > ( $stageActive.height() - scrollXHeight ) ){
                if( ( $stageMainActive.height() - mBottom ) > ($stageActive.outerHeight() + scrollTop - paddingT) ){
                    if( (boundTop + mTop) > ($stageActive.outerHeight() + scrollTop) ){
                        pTop = $stageActive.outerHeight();
                    }else{
                        pTop = boundTop + mTop - scrollTop + paddingT;
                    }
                }else{
                    if( boundTop > _canvas.height ){
                        pTop = $stageMainActive.height() - mBottom - scrollTop + paddingT; 
                    }else{
                        pTop = boundTop + mTop - scrollTop + paddingT; 
                    }
                }
            } else{
                if ( (bound.top + bound.height) > _canvas.height ) {
                    pTop = _canvas.height + paddingT + mTop;
                }else{
                    pTop = boundTop + paddingT + mTop;
                }
            }
            pTop += 7;
            stage.states.toolboxTriangle = 'center';
            if( $stageMainActive.width() > ( $stageActive.width() - scrollYWidth ) ){
                if( (_boundLeft + mLeft - scrollLeft + paddingL) < 0 ){
                    pLeft = 0;
                    transform = 'translateX(0)';
                }else if( (_boundLeft + mLeft - scrollLeft + paddingL - $stageActive.outerWidth() ) > 0 ){
                    pLeft = $stageActive.outerWidth();
                    transform = 'translateX(-100%)';
                }else{
                    pLeft = _boundLeft + mLeft - scrollLeft + paddingL;
                }
            }else{
                if( _boundLeft < 0 ){
                    pLeft = ( $stageActive.outerWidth() - $stageMainActive.width() ) / 2 + mLeft;
                    transform = 'translateX(0)';
                }else if( _boundLeft > _canvas.width ){
                    pLeft = ( $stageActive.outerWidth() - $stageMainActive.width() ) / 2 + mLeft + _canvas.width;
                    transform = 'translateX(-100%)';
                }else{
                    pLeft = ( $stageActive.outerWidth() - $stageMainActive.width() ) / 2 + mLeft + _boundLeft;
                }
            }
            pLeft -= 7;
            if( pLeft + 160 > $stageActive.outerWidth() ){
                pLeft = $stageActive.outerWidth();
                transform = 'translateX(-100%)';
            };
            if( pLeft < 160 ){
                transform = 'translateX(0)';
                pLeft = 0;
            };
            stage.states.toolboxStyle = {
                top: pTop,
                left: pLeft,
                transform: transform
            };
        });
    };
    $scope.getCurrentLayerInfo = function(){
        var _canvas = $scope.stages[$scope.currentStage]['canvas'],
        object = _canvas.getActiveObject(),
        objects = _canvas.getActiveObjects();
        
        $scope.stages[$scope.currentStage].states.isActiveLayer = true;
        $scope.stages[$scope.currentStage].states.isGroup = false;
        $scope.stages[$scope.currentStage].states.isNativeGroup = false;
        $scope.stages[$scope.currentStage].states.isLayer = false;
        $scope.stages[$scope.currentStage].states.isText = false;
        $scope.stages[$scope.currentStage].states.isImage = false;      
        $scope.stages[$scope.currentStage].states.isPath = false;      
        $scope.stages[$scope.currentStage].states.isShape = false;      
        $scope.stages[$scope.currentStage].states.isEditing = false; 
        $scope.stages[$scope.currentStage].states.elementUpload = false; 
        $scope.stages[$scope.currentStage].states.isGroupText = false;
        $scope.stages[$scope.currentStage].states.enableRotate = true;
        $scope.stages[$scope.currentStage].states.enableOpacity = true;
        $scope.stages[$scope.currentStage].states.enableChangePathColor = true;
        
        if(appConfig.isVisual) $scope.setPositionToolbox();
        if( objects.length > 1 ){
            $scope.stages[$scope.currentStage].states.isGroup = true;
            if(appConfig.isVisual){
                //$scope.stages[$scope.currentStage].states.isGroupText = true;
                var textType = ['i-text', 'text', 'textbox', 'curvedText'];
                _.each(objects, function (item, key) {
                    if (!_.includes(textType,item.get('type'))) {
                        $scope.stages[$scope.currentStage].states.isGroupText = false;
                        return false;
                    }
                });
                //$scope.stages[$scope.currentStage].states.isGroup = !$scope.stages[$scope.currentStage].states.isGroupText;
            }
        }else{
            $scope.stages[$scope.currentStage].states.isLayer = true;
            if( object ){
                if(!object.get('itemId')) {
                    var d = new Date(),
                    itemId = d.getTime() + Math.floor(Math.random() * 1000);
                    object.set({"itemId" : itemId});
                }
                $scope.stages[$scope.currentStage].states.ilr  = angular.isDefined(object.get('ilr')) ? object.get('ilr') : false;
                $scope.stages[$scope.currentStage].states.forceLock  = angular.isDefined(object.get('forceLock')) ? object.get('forceLock') : false;
                $scope.stages[$scope.currentStage].states.opacity = fabric.util.toFixed(object.get('opacity') * 100);
                ['type', 'itemId', 'lockMovementX', 'lockMovementY', 'lockScalingX', 'lockScalingY', 'lockRotation', 'lockUniScaling', 'selectable', 'visible', 'angle', 'excludeFromExport', 'field_mapping', 'isQrcode', 'qrContent', 'isBarcode', 'barCodeContent', 'v_card'].forEach(function(key){
                    $scope.stages[$scope.currentStage].states[key] = object.get(key);
                });
                switch(object.type) {
                    case 'i-text':
                    case 'text':
                    case 'textbox':
                    case 'curvedText':
                        angular.copy($scope.defaultStageStates.text, $scope.stages[$scope.currentStage].states.text);
                        $scope.addColor(tinycolor(object.get('fill')).toHexString());
                        $scope.stages[$scope.currentStage].states.isText = true;
                        $scope.stages[$scope.currentStage].states.isEditing = object.isEditing ? object.isEditing : false;
                        $scope.stages[$scope.currentStage].states.fixedWidth = angular.isDefined(object.fixedWidth) ? object.fixedWidth : false;
                        $scope.stages[$scope.currentStage].states.text = {
                            font: $scope.getFontInfo(object.get('fontFamily')),
                            is_uppercase: $scope.isUpperCase(object)
                        };
                        ['fontFamily', 'ptFontSize', 'fontSize', 'editable', 'textAlign', 'fontWeight', 'textDecoration', 'fontStyle', 'spacing', 'lineHeight', 'fill', 'charSpacing', 'textBackgroundColor', 'stroke', 'strokeWidth', 'text'].forEach(function(key){
                            $scope.stages[$scope.currentStage].states.text[key] = object.get(key);
                        });
                        if( angular.isUndefined($scope.stages[$scope.currentStage].states.text.ptFontSize) ){
                            $scope.stages[$scope.currentStage].states.text.ptFontSize = $scope.stages[$scope.currentStage].states.text.fontSize / $scope.stages[$scope.currentStage].states.ratioConvertFont;
                        }
                        if( $scope.stages[$scope.currentStage].states.text.textBackgroundColor == '' ) $scope.stages[$scope.currentStage].states.text.textBackgroundColor = '#ffffff';
                        if( !$scope.stages[$scope.currentStage].states.text.stroke ) $scope.stages[$scope.currentStage].states.text.stroke = '#ffffff';
                        if( object.type == 'curvedText' ){
                            ['reverse', 'spacing', 'radius'].forEach(function(key){
                                $scope.stages[$scope.currentStage].states.text[key] = object.get(key);
                            });
                            $scope.stages[$scope.currentStage].states.text.rtl = angular.isDefined( object.rtl ) ? object.rtl : false;
                            if( $scope.stages[$scope.currentStage].states.text.rtl ){
                                $scope.stages[$scope.currentStage].states.text.text = $scope.stages[$scope.currentStage].states.text.text.split('').reverse().join('');
                            };
                            $timeout(function(){
                                jQuery('.item-curved').addClass('active');
                                if( $scope.settings.is_mobile ) jQuery('.main-toolbar').addClass('overflow-hidden');
                            }, 300);
                        }else{
                            if( $scope.settings.is_mobile ) jQuery('.main-toolbar').removeClass('overflow-hidden');
                        };
                        $scope.stages[$scope.currentStage].states.enableRotate = NBDESIGNCONFIG.nbdesigner_text_rotate == '1' ? true : false;
                        $scope.stages[$scope.currentStage].states.enableOpacity = NBDESIGNCONFIG.nbdesigner_text_opacity == '1' ? true : false;
                        break;
                    case 'image':
                    case 'custom-image':
                        $scope.stages[$scope.currentStage].states.elementUpload  = angular.isDefined(object.get('elementUpload')) ? object.get('elementUpload') : false;
                        $scope.stages[$scope.currentStage].states.ilr  = angular.isDefined(object.get('ilr')) ? object.get('ilr') : false;
                        $scope.stages[$scope.currentStage].states.isImage = true;
                        $scope.stages[$scope.currentStage].states.src = object.getSvgSrc();
                        $scope.stages[$scope.currentStage].states.origin_src = angular.isDefined(object.origin_src) ? object.origin_src :  object.getSvgSrc();
                        ['crop_left', 'crop_top', 'crop_width', 'crop_height', 'crop_scaleX', 'crop_scaleY'].forEach(function(key){
                            if( angular.isDefined(object[key]) ){
                                $scope.stages[$scope.currentStage].states[key] = object[key];
                            }
                        });
                        break;
                    case 'rect':
                    case 'triangle':
                    case 'line':
                    case 'polygon':                    
                    case 'circle':
                    case 'ellipse':
                        $scope.stages[$scope.currentStage].states.isShape = true;
                        if( NBDESIGNCONFIG.nbdesigner_clipart_change_path_color == '1' ){
                            $scope.stages[$scope.currentStage].states.svg.groupPath = $scope.getPathOfSvg(object);
                        }else{
                            $scope.stages[$scope.currentStage].states.svg.groupPath = [];
                        }
                        $scope.stages[$scope.currentStage].states.enableRotate = NBDESIGNCONFIG.nbdesigner_clipart_rotate == '1' ? true : false;
                        $scope.stages[$scope.currentStage].states.enableOpacity = NBDESIGNCONFIG.nbdesigner_clipart_opacity == '1' ? true : false;
                        break;     
                    case 'path-group':                    
                    case 'path':
                    case 'group':
                        $scope.stages[$scope.currentStage].states.isPath = true;
                        if( NBDESIGNCONFIG.nbdesigner_clipart_change_path_color == '1' ){
                            $scope.stages[$scope.currentStage].states.svg.groupPath = $scope.getPathOfSvg(object);
                        }else{
                            $scope.stages[$scope.currentStage].states.svg.groupPath = [];
                        }
                        if( object.type == 'group' ) $scope.stages[$scope.currentStage].states.isNativeGroup = true;
                        $scope.stages[$scope.currentStage].states.enableRotate = NBDESIGNCONFIG.nbdesigner_clipart_rotate == '1' ? true : false;
                        $scope.stages[$scope.currentStage].states.enableOpacity = NBDESIGNCONFIG.nbdesigner_clipart_opacity == '1' ? true : false;
                        break; 
                    default:
                        //todo
                        break;
                }
            }            
        }        
        $scope.updateApp();
    };
    /* Utility functions */
    $scope.toggleTip = function( close ){
        var first_visitor = getCookie("nbdesigner_user");
        if( angular.isDefined( close ) ){
            close == true ? jQuery('.nbd-tip').removeClass('nbd-show') : jQuery('.nbd-tip').addClass('nbd-show');
        }else{
            if (first_visitor == "") {
                setCookie("nbdesigner_user", 'Hello World', 0.5);
                jQuery('.nbd-tip').addClass('nbd-show');
            }             
        }   
    };
    $scope.startCountTime = function(){
        $scope.startTime = new Date();
    };
    $scope.endCountTime = function(){
        var endTime = new Date();
        console.log((endTime - $scope.startTime) + " ms");        
    };    
    $scope.getFontInfo = function(alias){
        var font = _.filter($scope.resource.font.data, { alias: alias })[0],
            _font = angular.copy(font, _font);
        if(_font){    
            _font.file = {r: font.file.r};
            _font.file.i = angular.isDefined(font.file.i) ? font.file.i : 0;
            _font.file.b = angular.isDefined(font.file.b) ? font.file.b : 0;
            _font.file.bi = angular.isDefined(font.file.bi) ? font.file.bi : 0;
        }else{
            _font = {name: 'Roboto', alias: 'Roboto', file: {r: 1, b: 1, i: 1, bi: 1}, cat: ["99"], type:"google",subset:"latin"};
        }
        return _font;
    };
    $scope.getPathOfSvg = function(object){
        var groupPath = [];
        _.each(object._objects, function(path, index){
            if( path.get('fill') != '' ){
                var color = tinycolor(path.get('fill')).toHexString();
                $scope.addColor(color);             
                if(  ( findex = _.findIndex(groupPath, ['color', color]) ) > -1 ){
                    groupPath[findex]['index'].push(index);
                }else{
                    groupPath.push({color: color, index: [index]});
                }
            }
        });
        if(groupPath.length == 0){         
            $scope.addColor(tinycolor(object.get('fill')).toHexString());
            groupPath.push({color: tinycolor(object.get('fill')).toHexString(), index: [-1]});
        }
        return groupPath;
    };  
    $scope.isUpperCase = function( object ){
        var _isUpperCase = angular.isDefined(object.is_uppercase) ? object.is_uppercase : false;
        return _isUpperCase;
    };  
    $scope.fitRectangle = function(x1, y1, x2, y2, fill){
        var rec = {};
        if(x2 < x1 && y2 < y1){
            if(fill){
                if(x1/y1 > x2/y2){
                    rec.width = x2 * y1 / y2;
                    rec.height = y1;
                    rec.top = 0;
                    rec.left = (x1 * y2 - x2 * y1) / y2 / 2;                    
                }else {
                    rec.width = x1;
                    rec.height = x1 * y2 / x2;
                    rec.top = (x2 * y1 - x1 * y2) / x2 / 2;
                    rec.left = 0;                     
                }
            }else {
                rec.top = (x1 - x2) / 2;
                rec.left = (y1 - y2) / 2;
                rec.width = x2;
                rec.height = y2;
            }
        } else if( x1/y1 > x2/y2 ){
            rec.width = x2 * y1 / y2;
            rec.height = y1;
            rec.top = 0;
            rec.left = (x1 * y2 - x2 * y1) / y2 / 2;
        } else {
            rec.width = x1;
            rec.height = x1 * y2 / x2;
            rec.top = (x2 * y1 - x1 * y2) / x2 / 2;
            rec.left = 0;            
        }
        return rec;
    };    
    $scope.insertTemplateFont = function(font_name, callback){
        if(!_.filter($scope.resource.font.data, ['alias', font_name]).length){
            NBDDataFactory.get('nbd_get_resource', {type: 'google_font',font_name: font_name}, function(data){
                data = JSON.parse(data);
                if( data.flag == 1 ){
                    if(!_.filter($scope.resource.font.data, ['alias', font_name]).length){
                        $scope.resource.font.data.push(data.data);
                    };
                    var fontName = data.data.alias;
                    var font_id = fontName.replace(/\s/gi, '').toLowerCase();
                    if( !jQuery('#' + font_id).length ){
                        jQuery('head').append('<link id="' + font_id + '" href="https://fonts.googleapis.com/css?family='+ fontName.replace(/\s/gi, '+') +':400,400i,700,700i" rel="stylesheet" type="text/css">');
                    };
                    var font = new FontFaceObserver(fontName);
                    font.load($scope.settings.subsets[data.data.subset]['preview_text']).then(function () {
                        if( angular.isDefined(callback) ) callback(fontName);
                    }, function () {
                        console.log('Fail to load: '+fontName);
                        if( angular.isDefined(callback) ) callback('Roboto');
                    });
                }else{
                    if( angular.isDefined(callback) ) callback('Roboto');
                }
            });  
        }else{
            var _font = $scope.getFontInfo(font_name);
            var fontName = font_name;
            var font_id = fontName.replace(/\s/gi, '').toLowerCase();
            if( !jQuery('#' + font_id).length ){
                if(_font.type == 'google'){
                    jQuery('head').append('<link id="' + font_id + '" href="https://fonts.googleapis.com/css?family='+ fontName.replace(/\s/gi, '+') +':400,400i,700,700i" rel="stylesheet" type="text/css">');
                }else{
                    if( _font.file.r == '1' ){
                        var font_url = _font.url;
                        if(! (font_url.indexOf("http") > -1)) font_url = NBDESIGNCONFIG['font_url'] + font_url; 
                        var css = "";
                        css = "<style type='text/css' id='" + font_id + "' >";
                        css += "@font-face {font-family: '" + fontName + "';";
                        css += "src: local('\u263a'),";
                        css += "url('" + font_url + "') format('truetype')";
                        css += "}";
                        css += "</style>";
                        jQuery("head").append(css);
                    }else{
                        var css = "<style type='text/css' id='" + font_id + "' >";
                        _.each(_font.file, function (file, index) {
                            if( file != 0 ){
                                var font_url = file;
                                if(! (file.indexOf("http") > -1)) font_url = NBDESIGNCONFIG['font_url'] + file;
                                css += "@font-face {font-family: '" + fontName + "';";
                                css += "src: local('\u263a'), ";
                                css += "url('" + font_url + "') format('truetype');";
                                switch(index){
                                    case "r":
                                        css += "font-weight: normal;font-style: normal;"
                                        break;
                                    case "b":
                                        css += "font-weight: bold;font-style: normal;"
                                        break;
                                    case "i":
                                        css += "font-weight: normal;font-style: italic;"
                                        break;
                                    case "bi":
                                        css += "font-weight: bold;font-style: italic;"
                                        break;
                                };
                                css += "}";
                            }
                        });
                        css += "</style>";
                        jQuery("head").append(css);
                    }
                }
            };
            var font = new FontFaceObserver(fontName);
            font.load($scope.settings.subsets[_font.subset]['preview_text']).then(function () {
                if( angular.isDefined(callback) ) callback(fontName);
            }, function () {
                console.log('Fail to load: '+fontName);
                if( angular.isDefined(callback) ) callback(fontName);
            });          
        };
    };
    $scope.onloadTemplate = false;  
    $scope.currentLocalTempId = 0;
    $scope.templateHolderFields = [];
    $scope.insertTemplate = function(local, temp){
        $scope.currentLocalTempId = temp.id;
        if( angular.isUndefined( temp.doNotShowLoading ) ){
            $scope.toggleStageLoading( 6E4 );
            $scope.showDesignTab();
        }
        $scope.onloadTemplate = true;
        $scope.contextAddLayers = 'template';
        function loadDesign(fonts, design, viewport, konfig){
            var stageIndex = 0;
            function loadStage(stageIndex){
                var _index = 'frame_' + stageIndex,    
                stage = $scope.stages[stageIndex],        
                _canvas = stage['canvas'],
                layerIndex = 0;
                _canvas.clear();
                if( angular.isUndefined(design[_index]) ){
                    design[_index] = {version:"2.3.3",objects:[]};
                };
                if( angular.isDefined(design[_index].background) ){
                    _canvas.backgroundColor = design[_index].background;
                };
                var objects = design[_index].objects;
                function loadLayer(layerIndex){
                    function continueLoadLayer(){
                        layerIndex++;
                        if( objects.length != 0 && layerIndex < objects.length ){
                            loadLayer(layerIndex);
                        }else{
                            stageIndex++;
                            if( stageIndex < $scope.stages.length ){
                                loadStage(stageIndex);
                            }else{
                                _.each($scope.stages, function(_stage, index){
                                    $scope.renderStage(index);
                                    var layers = _stage.canvas.getObjects();
                                    $scope.renderTextAfterLoadFont(layers, function(){
                                        $scope.deactiveAllLayer();
                                        $scope.renderStage(index);
                                        $timeout(function(){
                                            $scope.deactiveAllLayer();
                                            $scope.renderStage(index);
                                            if( index == $scope.stages.length - 1 ){
                                                $scope.onloadTemplate = false;
                                                $scope.contextAddLayers = 'normal';
                                                if( angular.isDefined(viewport) ){
                                                    $scope.resizeStages(viewport);
                                                } else if( angular.isDefined(konfig) &&  angular.isDefined(konfig.scale) ){
                                                    viewport = {width: konfig.scale * 500, height: konfig.scale * 500};
                                                    $scope.resizeStages(viewport);
                                                }else{
                                                    $scope.toggleStageLoading();  
                                                }
                                                $scope.afterInnsertTemplate();
                                            }
                                        }, 500);         
                                    });                                         
                                });
                            }
                        }
                    };    
                    if( objects.length > 0 ){
                        var item = objects[layerIndex],
                        type = item.type;
                        var mustInsert = true;
                        if( type == 'image' || type == 'custom-image' ){
                            if( appConfig.domainChanged ){
//                                if( item.src.indexOf("https") == -1 ){
//                                    item.src = item.src.replace("http", "https");
//                                }
                                var _src = item.src;
                                var src = _src.split('nbdesigner');
                                item.src = NBDESIGNCONFIG['nbd_content_url'] + src[1];
                            };
                            if( angular.isDefined(item.avatar) ){
                                if( angular.isDefined($scope.settings.contact_sheets) ){
                                    item.src = $scope.settings.contact_sheets.avatar;
                                }
                            }
                            if( angular.isDefined(item.c_avatar) ){
                                if( angular.isDefined($scope.settings.contact_sheet) ){
                                    if( angular.isDefined($scope.settings.contact_sheet[item.c_avatar]) ){
                                        item.src = $scope.settings.contact_sheet[item.c_avatar].c_avatar;
                                    }else{
                                        mustInsert = false;
                                    }
                                }
                            }
                            if(mustInsert){
                                fabric.Image.fromObject(item, function(_image){
                                    _canvas.add(_image);
                                    continueLoadLayer();
                                });
                            }else{
                                continueLoadLayer();
                            }
                        }else{
                            var klass = fabric.util.getKlass(type);
                            if( ['i-text', 'text', 'textbox', 'curvedText'].indexOf( type ) > -1  ){
                                if(!_.filter(stage.states.usedFonts, ['alias', item.fontFamily]).length){
                                    stage.states.usedFonts.push($scope.getFontInfo(item.fontFamily));
                                };
                            };
                            ['first_name', 'last_name', 'full_name', 'company', 'address', 'postcode', 'city', 'phone', 'email', 'mobile', 'website', 'title'].forEach(function(val){
                                if( angular.isDefined(item[val]) ){
                                    if( angular.isDefined($scope.settings.user_infos) ){
                                        item.text = $scope.settings.user_infos[val].value;
                                    }
                                }
                            });
                            ['date', 'c_full_name', 'c_title', 'c_mobile', 'c_phone', 'c_email'].forEach(function (val) {
                                if (angular.isDefined(item[val])) {
                                    if (angular.isDefined($scope.settings.contact_sheets)) {
                                        if (val == 'date') {
                                            item.text = $scope.settings.contact_sheets[val];
                                        } else {
                                            if (angular.isDefined($scope.settings.contact_sheet[item[val]])) {
                                                item.text = $scope.settings.contact_sheet[item[val]][val];
                                            } else {
                                                mustInsert = false;
                                            }
                                        }
                                    }
                                }
                            });
                            if( angular.isDefined( item.field_mapping ) && !$scope.isTemplateMode ){
                                var field = _.filter($scope.settings.template_fields, { key: item.field_mapping })[0];
                                if( angular.isDefined( field ) ){
                                    if( field.value != '' ){
                                        if( angular.isDefined( item.is_uppercase ) ){
                                            item.text = item.is_uppercase ? field.value.toUpperCase() : field.value.toLowerCase();
                                        }else{
                                            item.text = field.value;
                                        }
                                    }else{
                                        var mapping_field = _.filter($scope.templateHolderFields, { key: item.field_mapping })[0];
                                        if( angular.isUndefined( mapping_field ) ){
                                            $scope.templateHolderFields.push( JSON.parse( JSON.stringify( field ) ) );
                                        }
                                    }
                                }
                            }
                            if( angular.isDefined(item.v_card) && !$scope.isTemplateMode && $scope.settings.is_logged == '1' ){
                                $scope.generateVcard( function( newObject ){
                                    var config = {
                                        left: item.left,
                                        top: item.top,
                                        scaleX: item.scaleX,
                                        scaleY: item.scaleY
                                    };
                                    newObject.set( config );
                                    newObject.scaleToWidth(item.width * item.scaleX);
                                    newObject.scaleToWidth(item.width * item.scaleX);
                                    _canvas.add( newObject );
                                    continueLoadLayer();
                                });
                            } else {
                                if( angular.isDefined(item.vcard) ){
                                    var config = {
                                        left: item.left,
                                        top: item.top,
                                        vcard: 1
                                    };
                                    $scope.strVcard = '';
                                    if( angular.isDefined($scope.settings.user_infos) ){
                                        var infos = $scope.settings.user_infos;
                                        $scope.strVcard += 'BEGIN:VCARD\nVERSION:3.0\n';
                                        $scope.strVcard += 'N:'+infos.last_name.value+';'+infos.first_name.value+'\n'+ 'FN:'+infos.full_name.value;
                                        $scope.strVcard += '\nADR;TYPE=home:;;'+infos.address.value+';'+infos.city.value+';;'+infos.postcode.value+';'+infos.country.value;
                                        $scope.strVcard += '\nTEL;TYPE=home:'+infos.phone.value;
                                        $scope.strVcard += '\nTEL;TYPE=work:'+infos.mobile.value;
                                        $scope.strVcard += '\nEMAIL;TYPE=internet,work:'+infos.email.value;
                                        $scope.strVcard += '\nURL;TYPE=work:'+infos.website.value;
                                        $scope.strVcard += '\nEND:VCARD';
                                        var qr = qrcode('0', 'M');
                                        qr.addData( $scope.strVcard );
                                        qr.make();
                                        var _qrcode = qr.createSvgTag();
                                        fabric.loadSVGFromString(_qrcode, function(ob, op) {		
                                            var object = fabric.util.groupSVGElements(ob, op);
                                            object.set(config);
                                            object.scaleToWidth(item.width * item.scaleX);
                                            object.scaleToHeight(item.height * item.scaleY);
                                            object.vcard = 1;
                                            _canvas.add(object);
                                            continueLoadLayer();
                                        });                                    
                                    }else{
                                        klass.fromObject(item, function(item){
                                            _canvas.add(item);
                                            continueLoadLayer();
                                        });
                                    }
                                }else{
                                    if(mustInsert){
                                        if( type == 'text' ){
                                            var text = item.text;
                                            var textobj = {};
                                            angular.copy(item, textobj);
                                            delete textobj.text;
                                            delete textobj.type;
                                            var textbox = new fabric.IText(text, textobj);
                                            _canvas.add(textbox);
                                            continueLoadLayer();
                                        }else{
                                            klass.fromObject(item, function(item){
                                                _canvas.add(item);
                                                continueLoadLayer();
                                            });
                                        }
                                    }else{
                                        continueLoadLayer();
                                    }
                                }
                            }
                        }
                    }else{
                        continueLoadLayer();
                    }
                };
                loadLayer(layerIndex);
            };
            if(fonts.length){
                _.each(fonts, function(font, index){
                    if(!_.filter($scope.resource.font.data, ['alias', font.alias]).length){
                        if( angular.isDefined(font.file) && angular.isDefined(font.file.r) )$scope.resource.font.data.push(font);
                    };                     
                    if( index == fonts.length - 1 ){
                        $scope.insertTemplateFont(font.alias, function(){
                            loadStage(stageIndex);
                            if(!_.filter($scope.resource.usedFonts, ['alias', font.alias]).length){
                                $scope.resource.usedFonts.push($scope.getFontInfo(font.alias));
                            };                            
                        });
                    }else{
                        $scope.insertTemplateFont(font.alias, function(){
                            if(!_.filter($scope.resource.usedFonts, ['alias', font.alias]).length){
                                $scope.resource.usedFonts.push($scope.getFontInfo(font.alias));
                            };                            
                        });
                    }
                });                
            }else{
                loadStage(stageIndex);
            }            
        }
        if( local ){
            loadDesign(temp.fonts, temp.design, temp.viewport);
        }else{
            NBDDataFactory.get('nbdesigner_get_product_info', {product_id: NBDESIGNCONFIG['product_id'], variation_id: NBDESIGNCONFIG['variation_id'], template_id: temp.id}, function(data){
                data = JSON.parse(data);
                loadDesign(data.fonts, data.design, data.config.viewport, data.config);
            });
        }
    };
    $scope._currentTempId = 0;
    $scope._currentpartIndex = 0;
    $scope.insertPartTemplate = function(tempId, partIndex){
        var stage = $scope.stages[$scope.currentStage],
        _canvas = stage['canvas'],
        layers = _canvas.getObjects();
        if( layers.length > 0 ){
            $scope._currentTempId = tempId;
            $scope._currentpartIndex = partIndex;
            jQuery('.nbd-popup.insert-part-template-alert').nbShowPopup();
        }else{
            $scope._insertPartTemplate(tempId, partIndex);
        }
    };
    $scope.closePopupInertPartTem = function(){
        jQuery('.insert-part-template-alert .close-popup').triggerHandler('click');
    };
    $scope._insertPartTemplate = function(temp, partIndex){
        $scope.onloadTemplate = true;
        $scope.contextAddLayers = 'template';
        $scope.toggleStageLoading();
        jQuery('.insert-part-template-alert .close-popup').triggerHandler('click');
        function loadDesign(fonts, design, viewport, konfig){
            var _index = 'frame_' + partIndex;
            if( angular.isUndefined( design[_index] ) ){
                return;
            };
            var layerIndex = 0, 
                stage = $scope.stages[$scope.currentStage],
                _canvas = stage['canvas'],
                objects = design[_index].objects;
            function loadStage(){
                _canvas.clear();
                function loadLayer(layerIndex){
                    function continueLoadLayer(){
                        layerIndex++;
                        if( objects.length != 0 && layerIndex < objects.length ){
                            loadLayer(layerIndex);
                        }else{
                            $scope.renderStage($scope.currentStage);
                            var layers = _canvas.getObjects();
                            $scope.renderTextAfterLoadFont(layers, function(){
                                $scope.deactiveAllLayer();
                                $scope.renderStage($scope.currentStage);
                                $timeout(function(){
                                    $scope.deactiveAllLayer();
                                    $scope.renderStage($scope.currentStage);
                                    $scope.onloadTemplate = false;
                                    $scope.contextAddLayers = 'normal';
                                    if( angular.isDefined(viewport) ){
                                        $scope.resizeStage(viewport);
                                    } else if( angular.isDefined(konfig) &&  angular.isDefined(konfig.scale) ){
                                        viewport = {width: konfig.scale * 500, height: konfig.scale * 500};
                                        $scope.resizeStage(viewport);
                                    }else{
                                        $scope.toggleStageLoading();  
                                    }
                                }, 100);
                            });
                        }
                    }
                    if( objects.length > 0 ){
                        var item = objects[layerIndex],
                        type = item.type;
                        if( type == 'image' || type == 'custom-image' ){
                            if( appConfig.domainChanged ){
                                var _src = item.src;
                                var src = _src.split('nbdesigner');
                                item.src = NBDESIGNCONFIG['nbd_content_url'] + src[1];
                            }
                            fabric.Image.fromObject(item, function(_image){
                                _canvas.add(_image);
                                continueLoadLayer();
                            });
                        }else{
                            var klass = fabric.util.getKlass(type);
                            if( ['i-text', 'text', 'textbox', 'curvedText'].indexOf( type ) > -1  ){
                                if(!_.filter(stage.states.usedFonts, ['alias', item.fontFamily]).length){
                                    stage.states.usedFonts.push($scope.getFontInfo(item.fontFamily));
                                };
                            };
                            if( angular.isDefined( item.field_mapping ) && !$scope.isTemplateMode ){
                                var field = _.filter($scope.settings.template_fields, { key: item.field_mapping })[0];
                                if( angular.isDefined( field ) ){
                                    if( field.value != '' ){
                                        if( angular.isDefined( item.is_uppercase ) ){
                                            item.text = item.is_uppercase ? field.value.toUpperCase() : field.value.toLowerCase();
                                        }else{
                                            item.text = field.value;
                                        }
                                    }else{
                                        var mapping_field = _.filter($scope.templateHolderFields, { key: item.field_mapping })[0];
                                        if( angular.isUndefined( mapping_field ) ){
                                            $scope.templateHolderFields.push( JSON.parse( JSON.stringify( field ) ) );
                                        }
                                    }
                                }
                            }
                            if( angular.isDefined(item.v_card) && !$scope.isTemplateMode && $scope.settings.is_logged == '1' ){
                                $scope.generateVcard( function( newObject ){
                                    var config = {
                                        left: item.left,
                                        top: item.top,
                                        scaleX: item.scaleX,
                                        scaleY: item.scaleY
                                    };
                                    newObject.set( config );
                                    newObject.scaleToWidth(item.width * item.scaleX);
                                    newObject.scaleToWidth(item.width * item.scaleX);
                                    _canvas.add( newObject );
                                    continueLoadLayer();
                                });
                            }else{
                                if( type == 'text' ){
                                    var text = item.text;
                                    var textobj = {};
                                    angular.copy(item, textobj);
                                    delete textobj.text;
                                    delete textobj.type;
                                    var textbox = new fabric.IText(text, textobj);
                                    _canvas.add(textbox);
                                    continueLoadLayer();
                                }else{
                                    klass.fromObject(item, function(item){
                                        _canvas.add(item);
                                        continueLoadLayer();
                                    });
                                }
                            }
                        }
                    }else{
                        $scope.onloadTemplate = false;
                        $scope.toggleStageLoading();  
                    }
                }
                loadLayer(layerIndex);
            }
            if(fonts.length){
                _.each(fonts, function(font, index){
                    if(!_.filter($scope.resource.font.data, ['alias', font.alias]).length){
                        if( angular.isDefined(font.file) && angular.isDefined(font.file.r) )$scope.resource.font.data.push(font);
                    };                     
                    if( index == fonts.length - 1 ){
                        $scope.insertTemplateFont(font.alias, function(){
                            loadStage();
                            if(!_.filter($scope.resource.usedFonts, ['alias', font.alias]).length){
                                $scope.resource.usedFonts.push($scope.getFontInfo(font.alias));
                            };                            
                        });
                    }else{
                        $scope.insertTemplateFont(font.alias, function(){
                            if(!_.filter($scope.resource.usedFonts, ['alias', font.alias]).length){
                                $scope.resource.usedFonts.push($scope.getFontInfo(font.alias));
                            };                            
                        });
                    }
                });
            }else{
                loadStage();
            }
        }
        /*$scope.setHistory(true);*/
        NBDDataFactory.get('nbdesigner_get_product_info', {product_id: NBDESIGNCONFIG['product_id'], variation_id: NBDESIGNCONFIG['variation_id'], template_id: temp}, function(data){
            data = JSON.parse(data);
            loadDesign(data.fonts, data.design, data.config.viewport, data.config);
        });
    };
    $scope.resizeStage = function(viewport){
        var stage = $scope.stages[$scope.currentStage];
        var currentViewport = $scope.calcViewport();
        var newFitRec = $scope.fitRectangle(viewport.width, viewport.height, stage.config._width, stage.config._height, true);
        var oldFitRec = $scope.fitRectangle(currentViewport.width, currentViewport.height, stage.config._width, stage.config._height, true);
        var factor = oldFitRec.width / newFitRec.width;
        if (factor != 1) {
            stage.canvas.forEachObject(function (obj) {
                var scaleX = obj.scaleX,
                    scaleY = obj.scaleY,
                    left = obj.left,
                    top = obj.top,
                    tempScaleX = scaleX * factor,
                    tempScaleY = scaleY * factor,
                    tempLeft = left * factor,
                    tempTop = top * factor;
                obj.scaleX = tempScaleX;
                obj.scaleY = tempScaleY;
                obj.left = tempLeft;
                obj.top = tempTop;
                if (obj.clipPath && obj.clipPath.absolutePositioned) {
                    var scaleX = obj.clipPath.scaleX,
                        scaleY = obj.clipPath.scaleY,
                        left = obj.clipPath.left,
                        top = obj.clipPath.top,
                        tempScaleX = scaleX * factor,
                        tempScaleY = scaleY * factor,
                        tempLeft = left * factor,
                        tempTop = top * factor;
                    obj.clipPath.scaleX = tempScaleX;
                    obj.clipPath.scaleY = tempScaleY;
                    obj.clipPath.left = tempLeft;
                    obj.clipPath.top = tempTop;
                }
                obj.setCoords();
            });
            stage.canvas.calcOffset();
            $scope.renderStage($scope.currentStage);
        }
        $scope.toggleStageLoading();
    };
    $scope.resizeStages = function(viewport){
        _.each($scope.stages, function(stage, index){ 
            var currentViewport = $scope.calcViewport();
            var newFitRec = $scope.fitRectangle(viewport.width, viewport.height, stage.config._width, stage.config._height, true);
            var oldFitRec = $scope.fitRectangle(currentViewport.width, currentViewport.height, stage.config._width, stage.config._height, true);
            var factor = oldFitRec.width / newFitRec.width;
            if( factor != 1 ){
                stage.canvas.forEachObject(function(obj) {     
                    var scaleX = obj.scaleX,
                    scaleY = obj.scaleY,
                    left = obj.left,
                    top = obj.top,
                    tempScaleX = scaleX * factor,
                    tempScaleY = scaleY * factor,
                    tempLeft = left * factor,
                    tempTop = top * factor;   
                    //if( ['i-text', 'text', 'textbox', 'curvedText'].indexOf( obj.type ) > -1  ){
                        //var newFontSize = obj.ptFontSize * $scope.stages[index].states.ratioConvertFont / tempScaleX;
                        //obj.fontSize = newFontSize;
                        
                    //}else{
                        obj.scaleX = tempScaleX;
                        obj.scaleY = tempScaleY;
                    //}
                    obj.left = tempLeft;
                    obj.top = tempTop;  
                    if(obj.clipPath && obj.clipPath.absolutePositioned){
                        var scaleX = obj.clipPath.scaleX,
                        scaleY = obj.clipPath.scaleY,
                        left = obj.clipPath.left,
                        top = obj.clipPath.top,
                        tempScaleX = scaleX * factor,
                        tempScaleY = scaleY * factor,
                        tempLeft = left * factor,
                        tempTop = top * factor;   
                        obj.clipPath.scaleX = tempScaleX;
                        obj.clipPath.scaleY = tempScaleY;
                        obj.clipPath.left = tempLeft;
                        obj.clipPath.top = tempTop; 
                    }
                    obj.setCoords();
                });
                stage.canvas.calcOffset();
                $scope.renderStage(index);
            }
            if( index == $scope.stages.length - 1 ){
                $scope.toggleStageLoading();
            }
        });
    };
    $scope.insertTypography = function(typo){
        /*$scope.setHistory(true);*/
        var stage = $scope.stages[$scope.currentStage],
        scale = stage.states.scaleRange[stage.states.currentScaleIndex].ratio,
        _canvas = stage['canvas'];        
        $scope.toggleStageLoading();
        $scope.showDesignTab();
        NBDDataFactory.get('nbd_get_resource', {type: 'get_typo', folder: typo.folder}, function(data){
            data = JSON.parse(data);
            var fonts = data.data.font,
            layers = data.data.design.frame_0.objects;
            $scope.resource.tempData.template = [];
            function loadLayers(){
                _.each(layers, function(layer, _index){
                    if( _index == layers.length - 1 ){                                
                        $scope.loadTemplateLayer(layer, function(){                                    
                            $scope.renderTextAfterLoadFont($scope.resource.tempData.template, function(){
                                var selection = new fabric.ActiveSelection($scope.resource.tempData.template, {
                                  canvas: _canvas
                                });                                        
                                _canvas.setActiveObject(selection);
                                selection.addWithUpdate();
                                if( ( selection.width * scale ) > ( _canvas.width * 0.8 ) ){
                                    var canvasWidth = _canvas.width;
                                    selection.scaleX = selection.scaleX * canvasWidth * 0.8 / ( selection.width * scale );
                                    selection.scaleY = selection.scaleY * canvasWidth * 0.8 / ( selection.width * scale );
                                    selection.width = selection.width * canvasWidth * 0.8 / ( selection.width * scale );
                                    selection.addWithUpdate();
                                    if( ( selection.height * scale ) > ( _canvas.height * 0.8 ) ){
                                        var canvasHeight = _canvas.height;
                                        selection.scaleX = selection.scaleX * canvasHeight * 0.8 / ( selection.height * scale );
                                        selection.scaleY = selection.scaleY * canvasHeight * 0.8 / ( selection.height * scale );
                                        selection.height = selection.height * canvasHeight * 0.8 / ( selection.height * scale );
                                        selection.addWithUpdate();
                                    }
                                }
                                _canvas.viewportCenterObjectH(selection);
                                _canvas.viewportCenterObjectV(selection);
                                $scope.renderStage();
                                $timeout(function(){
                                    $scope.deactiveAllLayer();                                
                                    _canvas.setActiveObject(selection);
                                    selection.addWithUpdate();
                                    _canvas.viewportCenterObjectH(selection);
                                    _canvas.viewportCenterObjectV(selection);
                                    if( stage.config.area_design_type == "2" ) $scope.setStackLayerAlwaysOnTop();
                                    $scope.renderStage();
                                    $scope.toggleStageLoading();  
                                }, 500);                                       
                            });                  
                        });
                    }else{
                        $scope.loadTemplateLayer(layer);
                    }
                });
            };
            if(fonts.length){
                _.each(fonts, function(font, index){
                    if(!_.filter($scope.resource.font.data, ['alias', font.alias]).length){
                        $scope.resource.font.data.push(font);
                    };                    
                    if( index == fonts.length - 1 ){
                        $scope.insertTemplateFont(font.alias, function(){
                            loadLayers();
                            if(!_.filter(stage.states.usedFonts, ['alias', font.alias]).length){
                                stage.states.usedFonts.push($scope.getFontInfo(font.alias));
                            };                                
                        });
                    }else{
                        $scope.insertTemplateFont(font.alias, function(){
                            if(!_.filter(stage.states.usedFonts, ['alias', font.alias]).length){
                                stage.states.usedFonts.push($scope.getFontInfo(font.alias));
                            };                            
                        });
                    }
                });
            }else{
                loadLayers();
            }
        });
    };
    $scope.renderTextAfterLoadFont = function(layers, callback ){
        if( layers.length == 0 ) {
            callback();
            return;
        };
        _.each(layers, function(item, index){
            if( ['text', 'i-text', 'curvedText', 'textbox'].indexOf(item.type) > -1 ){
                var fontFamily = item.get('fontFamily'),
                fontWeight = item.get('fontWeight'),
                fontStyle = item.get('fontStyle'),
                _font = $scope.getFontInfo(fontFamily);
                var opt = {};
                if( fontWeight != '' ) opt.weight = fontWeight;
                if( fontStyle != '' ) opt.style = fontStyle;
                item.set({objectCaching: false});
                fabric.util.clearFabricFontCache();
                var font = new FontFaceObserver(fontFamily, {weight: fontWeight, style: fontStyle});
                font.load($scope.settings.subsets[_font.subset]['preview_text']).then(function () {
                    fabric.util.clearFabricFontCache();
                    item.initDimensions();
                    item.setCoords();
                }, function () {
                    //todo
                });                
            };
            if( index == (layers.length -1) && typeof callback == 'function' ){
                callback();
            }
        });
    };
    $scope.loadTemplateLayer = function(json, callback, stage_id){
        stage_id =  angular.isDefined(stage_id) ? stage_id : $scope.currentStage;
        var stage = $scope.stages[stage_id],
        _canvas = stage['canvas'];        
        var klass = fabric.util.getKlass(json.type);
        klass.fromObject(json, function(item){
            $scope.contextAddLayers = 'template';
            _canvas.add(item);
            var _item = _canvas.item( _canvas.getObjects().length - 1 );
            $scope.resource.tempData.template.push(_item);
            if( typeof callback == 'function' ) callback();
        });        
    };
    $scope.insertCanvaTypo = function(typo){
        if( $scope.settings.task != 'typography' ){
            $scope.insertTypography(typo);
            return;
        };
        $scope.stages[0].states.usedFonts = [];
        $scope.zoomStage(0);
        var url = $scope.generateTypoLink(typo);
        $scope.resource.currentTypo = typo.id;
        var svg_url = url.replace("png", "svg"),
            svg_url = svg_url.replace("/img/", "/svg/"),
        _stages = $scope.stages[0],
        _canvas = _stages['canvas'];
        _canvas.clear();
        $scope.toggleStageLoading();
        function validFontName(fontName){
            fontName = fontName.replace(/'/g, "");
            fontName = fontName.split("-")[0];
            fontName = fontName.replace(/([A-Z])/g, ' $1').trim();
            return fontName;
        };
        fabric.Image.fromURL(url, function(op) {
            _stages.config.width = op.width;
            _stages.config.height = op.height;
            _canvas.setDimensions({'width' : op.width, 'height' : op.height});         
            fabric.loadSVGFromURL(svg_url, function(ob, op) {
                _canvas.add((fabric.util.groupSVGElements(ob, op)).set({
                    scaleX: _stages.config.width / op.width,
                    scaleY: _stages.config.height / op.height                     
                }));       
                $scope.renderStage(0);
                $timeout(function(){
                    var object = _canvas.getActiveObject();
                    var textIndexArr = [],
                        count = 0;
                    _.each(object._objects, function(path, index){
                        if( angular.isDefined(path.text) ){
                            var color = tinycolor(path.get('fill')).toHexString();
                            color = color.toLowerCase() == '#ffffff' ? '#404762' : color;
                            textIndexArr.push({index: index, type: 'text', text: path.text, font: validFontName(path.fontFamily), fill: color});
                        }
                        if( path.get('fill') == '' ){
                            textIndexArr.push({index: index, type: 'empty_rect'});
                        };
                    });    
                    object.set({dirty: true});
                    _.each(textIndexArr, function(el, index){
                        var _index = el.index - count;
                        object._objects.splice(_index, 1);
                        count++;
                        if(el.type == 'text'){
                            var _text = new FabricWindow.IText(el.text, {
                                    radius: 50,
                                    fontSize: 20,
                                    noScaleCache: false,
                                    fill: el.fill,
                                    lockUniScaling: true,
                                    lockRotation: true,
                                    spacing: 0
                                });
                                _text["setControlVisible"]("mtr", false);
                            if( index < _.findLastIndex(textIndexArr, function(o) { return o.type == 'text'; }) ){
                                $scope.insertTemplateFont(el.font, function(fontName){
                                _text.set({fontFamily: fontName});
                                _canvas.add(_text);      
                                if(!_.filter(_stages.states.usedFonts, ['alias', fontName]).length){
                                    _stages.states.usedFonts.push($scope.getFontInfo(fontName));
                                };                        
                                });
                            }else{
                                $scope.insertTemplateFont(el.font, function(fontName){
                                    _text.set({fontFamily: fontName});
                                    _canvas.add(_text);
                                    if(!_.filter(_stages.states.usedFonts, ['alias', fontName]).length){
                                        _stages.states.usedFonts.push($scope.getFontInfo(fontName));
                                    };                                     
                                    $scope.renderStage(0);               
                                    $scope.toggleStageLoading();  
                                    $scope.updateLayersList();
                                });                            
                            }
                        }
                    });                    
                }, 1000);
            });
        });
    };
    /* History */
    $scope.itemToJson = function(item, params){
        if(params){
            return JSON.stringify(params);
        }else{
            return JSON.stringify(item);
        }
    };   
    $scope.setItemParameters = function(parameters, itemId){
        var params = JSON.parse(parameters),
            _canvas = this.stages[this.currentStage]['canvas'],    
            item = _canvas.item(this.getLayerById(itemId));
            if(typeof params.src !== "undefined"){
                fabric.Image.fromURL(params.src, function(op) {
                    item.getElement().setAttribute("src", params.src);  
                });
            } 
        item.set(params);  
        item.setCoords();
        /* In case image layer has filter */
        if( angular.isDefined(params.filters) ){
            _.each(params.filters, function(value, index){
                if(value != null){
                    //$scope.applyFilters(item, index, value, false);
                };
            });
//            item.applyFilters(function() {
//                _canvas.renderAll()
//            });
        }        
    };
    $scope.undo = function(){
        var _stage = this.stages[this.currentStage],
            _canvas = _stage['canvas']; 
        if( _stage.undos.length > 0 ){
            var last = _stage.undos.pop(),
                _parameters = last.parameters;    
            if(last.interaction === 'remove') {
                $scope.contextAddLayers = 'undo';
                _canvas.add(last.element);
                last.interaction = 'add';
            } else if(last.interaction === 'add') {
                var item = _canvas.item($scope.getLayerById(last.element.itemId));
                _canvas.remove(item);
                last.interaction = 'remove';
            } else {
                var item = _canvas.item($scope.getLayerById(last.element.itemId)),
                    parameters = JSON.parse(_parameters);
                _.each(parameters, function(val, key) {
                    parameters[key] = item.get(key);
                });
                _parameters = JSON.stringify(parameters);
                this.setItemParameters(last.parameters, last.element.itemId);  
            } 
            this.setHistory(false, {
                element: last.element,
                parameters: _parameters,
                interaction: last.interaction
            }); 
            $scope.deactiveAllLayer();
            this.renderStage();            
        }
        $scope.updateApp();
    };
    $scope.redo = function(){
        var _stage = this.stages[this.currentStage],
            _canvas = _stage['canvas'];    
        if( _stage.redos.length > 0 ){
            var last = _stage.redos.pop(),
                _parameters = last.parameters;    
            if(last.interaction === 'remove') {
                $scope.contextAddLayers = 'redo';
                _canvas.add(last.element);
                last.interaction = 'add';
            } else if(last.interaction === 'add') {
                var item = _canvas.item($scope.getLayerById(last.element.itemId));
                _canvas.remove(item);
                last.interaction = 'remove';
            } else {
                var item = _canvas.item($scope.getLayerById(last.element.itemId)),
                    parameters = JSON.parse(_parameters);
                _.each(parameters, function(val, key) {
                    parameters[key] = item.get(key);
                });
                _parameters = JSON.stringify(parameters);
                this.setItemParameters(last.parameters, last.element.itemId);  
            } 
            this.setHistory({
                element: last.element,
                parameters: _parameters,
                interaction: last.interaction
            }); 
            $scope.deactiveAllLayer();
            this.renderStage();            
        };
        $scope.updateApp();
    }; 
    $scope._undo = function(){
        var _stage = this.stages[this.currentStage],
            _canvas = _stage['canvas'];
        if( _stage.undos.length > 0 ){
            var json = _stage.undos.pop();
            _canvas.clear();
            $scope.contextAddLayers = 'template';
            $scope.onloadTemplate = true;
            _canvas.loadFromJSON(json, function() {
                $scope.onloadTemplate = false;
                $scope.contextAddLayers = 'normal';
            });
            $scope._setHistory(false, json);
        }
    };
    $scope._redo = function(){
        var _stage = this.stages[this.currentStage],
            _canvas = _stage['canvas'];
        if( _stage.redos.length > 0 ){
            var json = _stage.redos.pop();
            _canvas.clear();
            $scope.contextAddLayers = 'template';
            $scope.onloadTemplate = true;
            _canvas.loadFromJSON(json, function() {
                $scope.onloadTemplate = false;
                $scope.contextAddLayers = 'normal';
            });
            $scope._setHistory(json);
        }
    };
    $scope._setHistory = function(undo, redo){
        var _stage = this.stages[this.currentStage];
        if (undo) {
            if(angular.isUndefined(_stage.undos)) _stage.undos = [];
            _stage.undos.push(undo);
            if(_stage.undos.length > 20) _stage.undos.shift();
            _stage.states.isUndoable = true;
        }else{
            if(angular.isUndefined(_stage.redos)) _stage.redos = [];
            _stage.states.isRedoable = true;
            _stage.redos.push(redo);
        };
        $scope.updateApp();
        $scope.updateStatusHistory();
    };
    $scope.setHistory = function(undo, redo){
        var _stage = this.stages[this.currentStage];
        if (undo) {
            if(angular.isUndefined(_stage.undos)) _stage.undos = [];
            _stage.undos.push(undo);
            if(_stage.undos.length > 50) _stage.undos.shift();
            _stage.states.isUndoable = true;
        }else{
            if(angular.isUndefined(_stage.redos)) _stage.redos = [];
            _stage.states.isRedoable = true;
            _stage.redos.push(redo);
        };
        /* var _canvas = _stage['canvas'];
        if (undo) {
            if(angular.isUndefined(_stage.undos)) _stage.undos = [];
            var json = _canvas.toJSON($scope.includeExport);
            if(angular.isUndefined(_stage.undos)) _stage.undos = [];
            _stage.undos.push(json);
            if(_stage.undos.length > 20) _stage.undos.shift();
            _stage.states.isUndoable = true;
        }*/
        $scope.updateApp();
        $scope.updateStatusHistory();
    };
    $scope.updateStatusHistory = function(){
        var _stage = this.stages[this.currentStage];
        _stage.states.isUndoable = (_stage.undos.length > 0) ? true : false; 
        _stage.states.isRedoable = (_stage.redos.length > 0) ? true : false;
        $scope.updateApp();
    }; 
    $scope.clearHistory = function(){
        var _stage = this.stages[this.currentStage];
        _stage.undos = [];
        _stage.redos = [];
        _stage.states.isRedoable = false;
        _stage.states.isUndoable = false;
        $scope.updateApp();
    };
    $scope.enableFullScreenMode = function(){
        var ele = document.getElementById('nbd-stages');
        requestFullScreen(ele);  
    };
    $scope.exitFullscreenMode = function(){
        exitFullscreen();
    };
    $scope.toggleStageFullScreenMode = function(){
        _.each($scope.stages, function(stage, index){
            var zoomIndex = $scope.fullScreenMode ?  stage.states.fullScreenScaleIndex : stage.states.fitScaleIndex;
            $scope.zoomStage(zoomIndex, index);
            $scope.deactiveAllLayer(index);    
            $scope.renderStage(index);              
        });
    };    
    /* Design tools */
    $scope.debug = function(){
        var _canvas = $scope.stages[$scope.currentStage]['canvas'];
        var object = _canvas.getActiveObject();
                    _canvas.forEachObject(function(obj) {
                        //if( obj === item ) return;
                        var _bound = obj.getBoundingRect();
                        console.log(obj.left);
                    });
        //console.log(object.toObject());
        //var fabricText = new fabric.CurvedText('Text', {top: 100, left: 100, fontSize: 20});
        //_canvas.add(fabricText);
        //$scope.addText();
        
//Add group        
//        var _canvas = this.stages[$scope.currentStage]['canvas'],
//        item1 = _canvas.item(0),
//        item2 = _canvas.item(1);
//var selection = new fabric.ActiveSelection([item1, item2], {
//  canvas: _canvas
//});
//_canvas.setActiveObject(selection);
//$scope.renderStage();
//
//var item3 = _canvas.item(2);
//var selection = _canvas.getActiveObject();
//if (selection.type === 'activeSelection') {
//  selection.addWithUpdate(item3)
//}
//$scope.renderStage();

//Shape design
//        var path = new fabric.Path("M0 0 H20 V20 H0z M 10 0 A 10 10, 0, 1, 0, 10 20 A 10 10, 0, 1, 0, 10 0z");
//        path.set({strokeWidth: 0});
//        _canvas.add(path);

//Add group
//var iText = new fabric.IText('Hello World!', {
//    left: 10,
//    top: 20,
//    fontFamily: 'Roboto',
//    fill: '#333'            
//});
//var circle = new fabric.Circle({
//  radius: 100,
//  fill: '#eef',
//  scaleY: 0.5
//});
//var group = new fabric.Group([ circle, iText ], {
//  left: 150,
//  top: 100,
//  angle: -10
//});      
//this.stages[this.currentStage]['canvas'].add(group);
        

//Copy layer to other stage
//$scope.tempLayer = _canvas.getActiveObject().toJSON($scope.includeExport);
//$scope.tempLayer = _canvas.getActiveObject().toJSON();
//console.log($scope.tempLayer);
        
//console.log($scope.tempLayer1);
//	_canvas.getActiveObject().clone(function(cloned) {
//            $scope.tempLayer = cloned;
//            console.log(typeof $scope.tempLayer);
//	}); 

//console.log(_canvas.getActiveObject().getBoundingRect());

//getScaledWidth
//console.log(_canvas.getActiveObject().getScaledWidth());
//console.log(_canvas.getActiveObject());
//
//$scope.contextAddLayers = 'template';
//_canvas.add(
//  new fabric.LimitedTextbox('text', {
//    top: 10,
//    left: 10,
//    width: 300,
//    maxWidth: 300,
//    maxLines: 2,
//    fontSize: 20
//  })
//);
    
//        $scope.contextAddLayers = 'template';
//        var clipPath = new fabric.Circle({
//            radius: 40,
//            top: -40,
//            left: -40
//        });
//        var rect = new fabric.Rect({
//            width: 200,
//            height: 100,
//            fill: 'red'
//        });
        //rect.clipPath = clipPath;
        //_canvas.add(rect);        

        //$scope.getPrintingOptions();
    };
    $scope.lineConfig = {
        dash1: 5,
        dash2: 5,
        width: 100,
        color: '#ff0000'
    };
    $scope.addLine = function(){
        var _canvas = $scope.stages[$scope.currentStage]['canvas'];
        _canvas.add(
            new fabric.Line([0, 20, $scope.lineConfig.width, 20], {
                strokeDashArray: [$scope.lineConfig.dash1, $scope.lineConfig.dash2],
                stroke: $scope.lineConfig.color
            })        
        );
    };
    $scope.addShape = function( type ){
        var _canvas = $scope.stages[$scope.currentStage]['canvas'];
        var shape = null;
        switch(type){
            case 'rect':
                shape = new fabric.Rect({
                    width: 50,
                    height: 50
                });
                break;
            case 'circle':
                shape = new fabric.Circle({
                    radius: 50
                });            
                break;
            case 'triangle':
                shape = new fabric.Triangle({
                    width: 50,
                    height: 50
                });            
                break;            
        }
        _canvas.add(shape);
    };
    $scope.tempLayer = {};
    $scope.renderStage = function( stage_id ){
        stage_id = stage_id ? stage_id :  $scope.currentStage;
        $scope.stages[stage_id]['canvas'].calcOffset();
        $scope.stages[stage_id]['canvas'].requestRenderAll();
    };
    $scope.deactiveAllLayer = function(stage_id){
        stage_id = stage_id ? stage_id :  $scope.currentStage;
        $scope.stages[stage_id]['canvas'].discardActiveObject();
        angular.merge($scope.stages[$scope.currentStage].states.uploadZone, {visibility: 'hidden'});
        $scope.renderStage();        
        $scope.updateApp();        
    };
    $scope.groupLayers = function(){
        var _canvas = $scope.stages[$scope.currentStage]['canvas'],
        activeObject = _canvas.getActiveObject();
        if (activeObject.type === 'activeSelection') {
            $scope.contextAddLayers = 'template';
            activeObject.toGroup();
            $scope.renderStage();
            $scope.getCurrentLayerInfo();
        }
    };
    $scope.onUnloadGroup = {
        status: false,
        remain: 0,
        length: 0
    };
    $scope.unGroupLayers = function(){
        var _canvas = $scope.stages[$scope.currentStage]['canvas'],
        activeObject = _canvas.getActiveObject();
        if (activeObject.type === 'group') {
            $scope.onUnloadGroup = {
                status: true,
                remain: activeObject._objects.length,
                length: activeObject._objects.length
            };
            if( angular.isDefined(activeObject.itemId) ){
                $scope.onUnloadGroup.prevIndex = $scope.getLayerById(activeObject.itemId);
            }
            $scope.contextAddLayers = 'template';
            activeObject.toActiveSelection();
            $scope.renderStage();
            $scope.getCurrentLayerInfo();            
        }
    };
    /* General */
    $scope.applyFilters = function(item, index, value, addintion){
        //do something
    };
    $scope.clearAllStage = function(){
        _.each($scope.stages, function(stage, index){
            stage.canvas.clear();
        });
        $scope.initStagesSettingWithoutTemplate();
    };
    /* Draw mode */
    $scope.disableDrawMode = function(){
        $scope.resource.drawMode.status = false;
        _.each($scope.stages, function(stage, index){
            stage.canvas.isDrawingMode = false;
        });
        $scope.changeBush();
        $scope.updateApp();
    };
    $scope.enableDrawMode = function(){
        $scope.resource.drawMode.status = true;
        _.each($scope.stages, function(stage, index){
            stage.canvas.isDrawingMode = true;
        });
        $scope.updateApp();
    }; 
    $scope.changeBush = function(color){
        if(color) $scope.resource.drawMode.brushColor = color;
        _.each($scope.stages, function(stage, index){
            stage.canvas.freeDrawingBrush = new fabric[$scope.resource.drawMode.brushType + "Brush"](stage.canvas);
            stage.canvas.freeDrawingBrush.color = $scope.resource.drawMode.brushColor;
            stage.canvas.freeDrawingBrush.width = $scope.resource.drawMode.brushWidth;
        });
    };
    $scope.importDesign = function(){
        var input = document.createElement('input');
        input.type = 'file';
        input.accept = 'text/json|application/json';
        input.style.display = 'none';
        input.addEventListener('change', onChange.bind(input), false);
        document.body.appendChild(input);
        input.click();
        function onChange(){
            if (this.files.length > 0) {
                var file = this.files[0],
                reader = new FileReader();
                reader.onload = function(event){
                    if (event.target.readyState === 2) {
                        var result = JSON.parse(reader.result);
                        $scope.insertTemplate(true, {fonts: result.fonts, design: result.design, viewport: result.config.viewport});
                        destroy();
                    }
                };
                reader.readAsText(file);                
            }            
        }
        function destroy() {
            input.removeEventListener('change', onChange.bind(input), false);
            document.body.removeChild(input);
        }
    };
    $scope.exportDesign = function(){
        $scope.saveDesign();
        var json = {config: {}};
        json.config.viewport = $scope.calcViewport();
        json.fonts = $scope.resource.usedFonts;
        json.design = $scope.resource.jsonDesign;
        var filename = 'design.json',
        text = JSON.stringify(json),
        a = document.createElement('a');
        a.setAttribute('href', 'data:application/json;charset=utf-8,'+ encodeURIComponent(text));
        a.setAttribute('download', filename);
        a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    };   
    $scope.loadMyDesign = function(did, inCart){
        function process(){
            var dataObj = {}, loadAllMyTemplate = true;
            $scope.toggleStageLoading();
            var popup = inCart ? '.popup-nbd-my-designs-in-cart' : '.popup-nbd-my-templates';
            if( did != null ){
                dataObj = { did: did };
                loadAllMyTemplate = false;
                jQuery(popup).removeClass('nb-show');
            }else{
                dataObj = {
                    product_id: NBDESIGNCONFIG['product_id'],
                    variation_id: NBDESIGNCONFIG['variation_id']
                };
                if( appConfig.loadAllDesign ){
                    dataObj = {};
                }
            };
            var action = inCart ? 'nbd_get_designs_in_cart' : 'nbd_get_user_designs';
            NBDDataFactory.get(action, dataObj, function(data){
                data = JSON.parse(data);
                if(data.flag == 1){
                    if( loadAllMyTemplate ){
                        if( inCart ){
                            $scope.resource.cartTemplates = data.designs;
                        }else{
                            $scope.resource.myTemplates = data.designs;
                        }
                        $scope.toggleStageLoading();
                        jQuery(popup).nbShowPopup();
                    }else{
                        $scope.insertTemplate(true, {fonts: data.fonts, design: data.design, doNotShowLoading: true});
                    }
                }
            });
        }
        if( inCart ){
            process();
        }else{
            $scope.login(function(){
                process();
            });
        }
    };
    $scope.tempCanvas = null;
    $scope.storeLayers = function(){
        var _canvas = this.stages[$scope.currentStage]['canvas'];
        var item = _canvas.getActiveObjects();
        if(item){
            var bound = _canvas.getActiveObject().getBoundingRect();
            if( jQuery('#temp-canvas-wrap').length == 0 ){
                var tempCanvasWrap = document.createElement('div');
                tempCanvasWrap.setAttribute('id', 'temp-canvas-wrap');
                var canvasEl = document.createElement('canvas');
                canvasEl.setAttribute('id', 'temp-canvas');
                tempCanvasWrap.appendChild(canvasEl);
                document.body.appendChild(tempCanvasWrap);
                $scope.tempCanvas = new fabric.Canvas("temp-canvas");
            }
            $scope.tempCanvas.setDimensions({'width' : bound.width, 'height' : bound.height});
            $scope.tempCanvas.clear();
            $scope.tempCanvas.renderAll();
            var designData = {
                used_font: [],
                design: null,
                preview: null,
                tempLayers: []
            };
            var layers = []; var currentIndex = 0;
            _canvas.getActiveObjects().forEach(function( obj ){
                if( ['i-text', 'text', 'textbox', 'curvedText'].indexOf( obj.type ) > -1  ){
                    var font = $scope.getFontInfo(obj.fontFamily);
                    designData.used_font.push(font);
                }
                layers.push( JSON.stringify(obj) );
            });
            function importLayerToTempCanvas( currentIndex ){
                var object = JSON.parse(layers[currentIndex]);
                function continueLoadLayer(){
                    var _item = $scope.tempCanvas.item( $scope.tempCanvas.getObjects().length - 1 );
                    designData.tempLayers.push(_item);
                    currentIndex++;
                    if( currentIndex <= (layers.length - 1) ){
                        importLayerToTempCanvas( currentIndex );
                    }else{
                        var selection = new fabric.ActiveSelection(designData.tempLayers, {
                          canvas: $scope.tempCanvas
                        });                                        
                        $scope.tempCanvas.setActiveObject(selection);
                        selection.addWithUpdate();
                        $scope.tempCanvas.viewportCenterObjectH(selection);
                        $scope.tempCanvas.viewportCenterObjectV(selection);
                        $scope.tempCanvas.discardActiveObject().renderAll();
                        $timeout(function(){
                            designData.preview = $scope.tempCanvas.toDataURL();
                            designData.design = $scope.tempCanvas.toJSON($scope.includeExport);
                            var designObj = {
                                used_font: designData.used_font,
                                json: designData.design,
                                width: bound.width,
                                height: bound.height
                            };
                            var _dataObj = {
                                type: 'save_user_design',
                                design: new Blob([JSON.stringify(designObj)], {type: "application/json"}),
                                preview: $scope.makeblob( designData.preview )
                            };
                            $scope.toggleStageLoading();
                            NBDDataFactory.get('nbd_get_resource', _dataObj, function(_data){
                                _data = JSON.parse(_data);
                                if($scope.resource.myDesigns.length == 0){
                                    $scope.needReloadUserDesigns = true;
                                }else{
                                    $scope.resource.myDesigns.splice(0, 0, _data.data);
                                }
                                $scope.toggleStageLoading();
                            });
                        });
                    }
                };
                var klass = fabric.util.getKlass(object.type);
                if( object.type == 'image' || object.type == 'custom-image' ){
                    fabric.Image.fromObject(object, function(_image){
                        $scope.tempCanvas.add(_image);
                        continueLoadLayer();
                    });
                }else{
                    klass.fromObject(object, function(object){
                        $scope.tempCanvas.add(object);
                        continueLoadLayer();
                    });
                }
            };
            importLayerToTempCanvas( currentIndex );
        }
    };
    $scope.needReloadUserDesigns = false;
    $scope.loadUserDesigns = function( template_id ){
        $scope.toggleStageLoading();
        var _dataObj = {
            type: 'load_user_designs'
        };
        if( angular.isDefined(template_id) ){
            _dataObj.type = 'load_user_design';
            _dataObj.template_id = template_id;
        }else{
            if( $scope.resource.myDesigns.length && !$scope.needReloadUserDesigns){
                jQuery('.popup-nbd-user-design').nbShowPopup();
                $scope.toggleStageLoading();
                return;
            }
        }
        NBDDataFactory.get('nbd_get_resource', _dataObj, function(_data){
            _data = JSON.parse(_data);
            if( angular.isDefined(_data.flag) && _data.flag == '1' ){
                if( angular.isDefined(template_id) ){
                    $scope.insertUserDesigns( _data.data.design );
                }else{
                    _data.data.user_designs.forEach(function(design){
                        $scope.resource.myDesigns.push(design);
                    });
                    $scope.needReloadUserDesigns = false;
                    jQuery('.popup-nbd-user-design').nbShowPopup();
                }
            }
            $scope.toggleStageLoading();
        });
    };
    $scope.deleteUserDesign = function( template_id, index ){
        var con = confirm($scope.settings.nbdlangs.confirm_delete_design);
        if( con == true ){
            jQuery('.popup-nbd-user-design .overlay-main').addClass('active');
            var _dataObj = {
                type: 'delete_user_design',
                template_id: template_id
            };
            NBDDataFactory.get('nbd_get_resource', _dataObj, function(_data){
                _data = JSON.parse(_data);
                if( angular.isDefined(_data.flag) && _data.flag == '1' ){
                    $scope.resource.myDesigns.splice(index, 1);
                }
                jQuery('.popup-nbd-user-design .overlay-main').removeClass('active');
            });
        }
    };
    $scope.insertUserDesigns = function( data ){
        var stage = $scope.stages[$scope.currentStage],
        _canvas = stage['canvas'];
        jQuery('.popup-nbd-user-design .overlay-main').addClass('active');
        var fonts = data.used_font,
        layers = data.json.objects,
        _index = 0,
        tempLayers = [];
        function loadLayer(_index){
            if( _index == layers.length ){
                var selection = new fabric.ActiveSelection(tempLayers, {
                  canvas: _canvas
                });
                $timeout(function(){                              
                    _canvas.setActiveObject(selection);
                    selection.addWithUpdate();
                    _canvas.viewportCenterObjectH(selection);
                    _canvas.viewportCenterObjectV(selection);
                    if( stage.config.area_design_type == "2" ) $scope.setStackLayerAlwaysOnTop();
                    $scope.renderStage();
                    //jQuery('.popup-nbd-user-design .close-popup').triggerHandler('click');
                    jQuery('.popup-nbd-user-design').removeClass('nb-show');
                    jQuery('.popup-nbd-user-design .overlay-main').removeClass('active');
                }, 50);
            }else{
                $scope.contextAddLayers = 'template';
                var object = layers[_index],
                    type = object.type;
                _index++;
                function addLayer( _obj ){
                    _canvas.add(_obj);
                    tempLayers.push(_obj);
                    loadLayer(_index);
                };
                var klass = fabric.util.getKlass(type);
                if( type == 'image' || type == 'custom-image' ){
                    fabric.Image.fromObject(object, function(_image){
                        addLayer(_image);
                    });
                }else{
                    klass.fromObject(object, function(object){
                        if( ['i-text', 'text', 'textbox', 'curvedText'].indexOf( object.type ) > -1  ){
                            var fontName = object.fontFamily,
                            content = object.text;
                            var font = new FontFaceObserver(fontName, {weight: object.fontWeight, style: object.fontStyle});
                            font.load(content).then(function () {
                                fabric.util.clearFabricFontCache(fontName);
                                addLayer(object);
                            }, function () {
                                console.log('Fail to load font: '+fontName);
                                addLayer(object);
                            });
                        }else{
                            addLayer(object);
                        }
                    });
                }
            }
        }
        if(fonts.length){
            _.each(fonts, function(font, index){
                if(!_.filter($scope.resource.font.data, ['alias', font.alias]).length){
                    $scope.resource.font.data.push(font);
                };                    
                if( index == fonts.length - 1 ){
                    $scope.insertTemplateFont(font.alias, function(){
                        loadLayer(_index);
                        if(!_.filter(stage.states.usedFonts, ['alias', font.alias]).length){
                            stage.states.usedFonts.push($scope.getFontInfo(font.alias));
                        };                                
                    });
                }else{
                    $scope.insertTemplateFont(font.alias, function(){
                        if(!_.filter(stage.states.usedFonts, ['alias', font.alias]).length){
                            stage.states.usedFonts.push($scope.getFontInfo(font.alias));
                        };                            
                    });
                }
            });
        }else{
            loadLayer(_index);
        }
    };
    $scope.deleteLayers = function(itemIndex){
        var _canvas = this.stages[$scope.currentStage]['canvas'];
        //if(item.isEditing) return;
        var item = angular.isDefined(itemIndex) ? _canvas.item(itemIndex) : _canvas.getActiveObjects();
        if(item){
            if( angular.isDefined(itemIndex) ){
                $scope.setHistory({
                    element: item,
                    parameters: JSON.stringify(item.toJSON()),
                    interaction: 'remove'
                });
                _canvas.remove(item); 
            }else{
                _canvas.getActiveObjects().forEach(function(o){
                    $scope.setHistory({
                        element: o,
                        parameters: JSON.stringify(o.toJSON()),
                        interaction: 'remove'
                    }); 
                    _canvas.remove(o); 
                });
            }
            _canvas.discardActiveObject().requestRenderAll();
        };
        $scope.updateLayersList();
        return;
    };
    $scope.resetLayer = function () {
        var _stage = $scope.stages[$scope.currentStage],
            _canvas = _stage['canvas'];
        var index = 'frame_' + $scope.currentStage;
        var initialItems = null;
        var item = _canvas.getActiveObject();
        if (angular.isDefined($scope.settings.product_data.design)) {
            initialItems = $scope.settings.product_data.design[index].objects;
        }else {
            return;
        }
        var initialItem = _.find(initialItems, {itemId: item.get('itemId')});
        if (angular.isDefined(initialItem)) {
            item.set(initialItem);
            item.setCoords();
            $scope.deactiveAllLayer();
            $scope.renderStage();
        }
        return;
    };
    $scope.activeLayer = function(itemIndex, layerIndex){
        var _stage = $scope.stages[$scope.currentStage],
        _canvas = _stage['canvas'];
        if( !$scope.isTemplateMode && angular.isDefined( _canvas.item(itemIndex).forceLock ) && _canvas.item(itemIndex).forceLock ) return;
        _canvas.setActiveObject(_canvas.item(itemIndex));
        if( angular.isDefined(layerIndex) ) _stage.layers[layerIndex].active = true;
        $scope.renderStage();
    };
    $scope.copyLayers = function(){
        var _canvas = this.stages[$scope.currentStage]['canvas'];
        if( !_canvas.getActiveObject() ) return;
	_canvas.getActiveObject().clone(function(cloned) {
            _clipboard = cloned;
	}); 
        $timeout(function(){
            _clipboard.clone(function(clonedObj) {
                _canvas.discardActiveObject();
                clonedObj.set({
                    left: clonedObj.left + 10,
                    top: clonedObj.top + 10,
                    evented: true
                });
                if (clonedObj.type === 'activeSelection') {
                    clonedObj.canvas = _canvas;
                    clonedObj.forEachObject(function(obj) {
                        $scope.contextAddLayers = 'copy';
                        _canvas.add(obj);
                    });
                    clonedObj.setCoords();
                } else {
                    $scope.contextAddLayers = 'copy';
                    _canvas.add(clonedObj);
                }
                _canvas.setActiveObject(clonedObj);
                _canvas.requestRenderAll();            
            });
        });
    };  
    $scope.alignLayer = function(command){
        var _canvas = this.stages[this.currentStage].canvas,
            group = _canvas.getActiveObject(),   
            items = _canvas.getActiveObjects(),
            _bound = items[0].getBoundingRect(),    
            position = {
                left: _bound.left,
                top: _bound.top,
                right: _bound.left + _bound.width,
                bottom: _bound.top + _bound.height
            };  
        var _leftPosition = [],   
            _topPosition = [],   
            totalWidth = 0,
            totalHeight = 0;  

        items.forEach(function(item, index){
            var bound = item.getBoundingRect();
            if(bound.left < position.left) position.left = bound.left;
            if(bound.top < position.top) position.top = bound.top;
            if(bound.left + bound.width > position.right) position.right = bound.left + bound.width;
            if(bound.top + bound.height > position.bottom) position.bottom = bound.top + bound.height;
            _leftPosition.push({index: index, value: bound.left});
            _topPosition.push({index: index, value: bound.top});
            totalWidth += bound.width;
            totalHeight += bound.height;
        });
        switch(command) {
            case 'horizontal':
                items.forEach(function(item){
                    var bound = item.getBoundingRect();
                    item.set({top: item.get('top') + (position.top + position.bottom) / 2 - bound.top - bound.height / 2});
                    item.setCoords();                       
                });
                break;
            case 'vertical':
                items.forEach(function(item){
                    var bound = item.getBoundingRect();
                    item.set({left: item.get('left') + (position.left + position.right) / 2 - bound.left - bound.width / 2});
                    item.setCoords();
                });                    
                break;
            case 'top':
                items.forEach(function(item){
                    var bound = item.getBoundingRect();
                    item.set({top: item.get('top') + position.top - bound.top });
                    item.setCoords();
                });                    
                break;
            case 'bottom':
                items.forEach(function(item){
                    var bound = item.getBoundingRect();
                    item.set({top: item.get('top') + position.bottom - bound.top - bound.height });
                    item.setCoords();
                });                     
                break;  
            case 'left':
                items.forEach(function(item){
                    var bound = item.getBoundingRect();
                    item.set({left: item.get('left') - bound.left + position.left});
                    item.setCoords();
                });
                break;
            case 'right':
                items.forEach(function(item){
                    var bound = item.getBoundingRect();
                    item.set({left: item.get('left') - bound.left + position.right - bound.width});
                    item.setCoords();
                });                      
                break;
            case 'dis-horizontal':
                leftPosition = _.sortBy(_leftPosition, [function(o) { return o.value; }]);
                var space = (position.right - position.left - totalWidth) / (items.length - 1);
                leftPosition.forEach(function(_item, _index){
                    var index = _item.index;
                    if(_index > 0 && _index < items.length - 1){
                        var item = items[index],
                        previous_item = items[leftPosition[_index-1].index],
                        bound = item.getBoundingRect(),
                        previous_item_bound = previous_item.getBoundingRect();
                        item.set({'left': item.get('left') - bound.left +  previous_item_bound.left + previous_item_bound.width + space });
                        item.setCoords();                            
                    }
                });
                break;
            case 'dis-vertical':
                topPosition = _.sortBy(_topPosition, [function(o) { return o.value; }]);
                var space = (position.bottom - position.top - totalHeight) / (items.length - 1);
                topPosition.forEach(function(_item, _index){
                    var index = _item.index;
                    if(_index > 0 && _index < items.length - 1){
                        var item = items[index],
                        previous_item = items[topPosition[_index-1].index],
                        bound = item.getBoundingRect(),
                        previous_item_bound = previous_item.getBoundingRect();
                        item.set({'top': item.get('top') - bound.top + previous_item_bound.top + previous_item_bound.height + space });
                        item.setCoords();
                    }
                });
                break;                  
        };    
        group.addWithUpdate();
        this.renderStage();
    };    
    $scope.translateLayer = function(command){
        var stage = $scope.stages[$scope.currentStage],
            _canvas = stage.canvas,
            item = _canvas.getActiveObject();
        if(!item) return;
            var  bound = item.getBoundingRect(),
            scale = stage.states.scaleRange[stage.states.currentScaleIndex].ratio,
            left = item.get('left'),
            top = item.get('top'),
            originX = item.get('originX'),
            originY = item.get('originY');
        $scope.beforeObjectModify(item);
        stage.states.isShowToolBox = false;
        switch(command) {
            case 'horizontal':
                _canvas.viewportCenterObjectH(item);
                break;
            case 'vertical':
                _canvas.viewportCenterObjectV(item);
                break;
            case 'center':
                _canvas.viewportCenterObjectH(item);
                _canvas.viewportCenterObjectV(item);
                break;                
            case 'top':
                var _top = originY == 'center' ? bound.height / 2 /scale : 0;
                item.set({top: _top});
                break;                
            case 'top-left':
                var _top = 0, _left = 0;
                if( originX == 'center' ){
                    _left = bound.width / 2 /scale;
                }
                if( originY == 'center' ){
                    _top = bound.height / 2 /scale;
                }
                item.set({top: _top, left: _left});
                break;   
            case 'top-center':
                _canvas.viewportCenterObjectH(item);
                var _top = originY == 'center' ? bound.height / 2 /scale : 0;
                item.set({top: _top});
                break; 
            case 'top-right':
                var _top = originY == 'center' ? bound.height / 2 /scale : 0,
                _left = left + (_canvas.width - bound.width - bound.left)/scale;                
                item.set({top: _top, left: _left});
                break;            
            case 'bottom':
                var _top = top + (_canvas.height - bound.height - bound.top)/scale;
                item.set({top: _top});
                break;  
            case 'bottom-left':
                var _left = originX == 'center' ? bound.width / 2 /scale : 0,
                _top = top + (_canvas.height - bound.height - bound.top)/scale;
                item.set({left: _left, top: _top});
                break; 
            case 'bottom-center':
                _canvas.viewportCenterObjectH(item);
                var _top = top + (_canvas.height - bound.height - bound.top)/scale;
                item.set({top: _top});
                break;
            case 'bottom-right':
                var _left = left + (_canvas.width - bound.width - bound.left)/scale,
                _top = top + (_canvas.height - bound.height - bound.top)/scale;
                item.set({left: _left, top: _top});
                break;             
            case 'left':
                var _left = originX == 'center' ? bound.width / 2 /scale : 0;
                item.set({left: _left});
                break;
            case 'middle-left':
                _canvas.viewportCenterObjectV(item);
                var _left = originX == 'center' ? bound.width / 2 /scale : 0;
                item.set({left: _left});
                break;                
            case 'right':
                var _left = left + (_canvas.width - bound.width - bound.left)/scale;
                item.set({left: _left});
                break;       
            case 'middle-right':
                _canvas.viewportCenterObjectV(item);
                var _left = left + (_canvas.width - bound.width - bound.left)/scale;
                item.set({left: _left});
                break;             
        };
        item.setCoords();
        $scope.renderStage();        
    };
    $scope.fitToStage = function( direction, item ){
        var stage = $scope.stages[$scope.currentStage],
            _canvas = stage.canvas,
            item = angular.isDefined(item) ? item : _canvas.getActiveObject();
        if(!item) return;
        direction = angular.isDefined(direction) ? direction : 'both';
        var originX = item.get('originX'),
        originY = item.get('originY'),
        scale = stage.states.scaleRange[stage.states.currentScaleIndex].ratio;
        switch(direction){
            case 'width':
                var _left = originX == 'center' ? _canvas.width / 2 /scale : 0;
                item.set({
                    left: _left,
                    scaleX: _canvas.width / item.width / scale,
                    scaleY: _canvas.width / item.width / scale
                });
                _canvas.viewportCenterObjectV(item);
                break;
            case 'height':
                var _top = originY == 'center' ? _canvas.height / 2 /scale : 0;
                item.set({
                    top: _top,
                    scaleX: _canvas.height / item.height / scale,
                    scaleY: _canvas.height / item.height / scale
                });
                _canvas.viewportCenterObjectH(item);
                break;
            default:
                var _top = originY == 'center' ? _canvas.height / 2 /scale : 0;
                var _left = originX == 'center' ? _canvas.width / 2 /scale : 0;
                item.set({
                    top: _top,
                    left: _left,
                    scaleX: _canvas.width / item.width / scale,
                    scaleY: _canvas.height / item.height / scale
                });
                _canvas.viewportCenterObjectH(item);
                _canvas.viewportCenterObjectV(item);
        }
        item.setCoords();
        $scope.renderStage(); 
    };
    $scope.getLayerById = function(itemId){
        var _canvas = this.stages[this.currentStage].canvas;
        var _index;
        _canvas.forEachObject(function(obj, index) {
            if(obj.get('itemId') == itemId) _index = index;
        });
        return _index;
    };     
    $scope.closePopupClearStage = function(){
        jQuery('.clear-stage-alert .close-popup').triggerHandler('click');
    };
    $scope.zoomStage = function(index, stage_id){
        stage_id = angular.isDefined(stage_id) ? stage_id : $scope.currentStage;
        var _stage = $scope.stages[stage_id],
           _canvas = _stage['canvas'];
        _stage.states.isShowToolBox = true;
        _stage.states.currentScaleIndex = index;
        $scope.setStageDimension(stage_id);
        _canvas.setZoom(_stage.states.scaleRange[_stage.states.currentScaleIndex].ratio);
        jQuery('#stage-container-'+$scope.currentStage).stop().animate({
            scrollTop: 0,
            scrollLeft: 0
        }, 100);
        jQuery('#stage-container-'+$scope.currentStage).perfectScrollbar('update');
        if( appConfig.isVisual ) $scope.deactiveAllLayer();
        this.renderStage();
        if( $scope.settings.showRuler && appConfig.isModern ){
            _stage.hozRuler.update({config: _stage.config, zoomRatio: _stage.states.scaleRange[_stage.states.currentScaleIndex].ratio});
            _stage.verRuler.update({config: _stage.config, zoomRatio: _stage.states.scaleRange[_stage.states.currentScaleIndex].ratio});
        };
    };
    $scope.clearStage = function(){
        var stage = $scope.stages[$scope.currentStage],
        _canvas = stage['canvas'];
        _canvas.clear();
        jQuery('.clear-stage-alert .close-popup').triggerHandler('click');
        if( stage.config.bgType == 'color' ){
            _canvas.backgroundColor = stage.config.bgColor;
        }
        if( stage.config.area_design_type == "2" ){
            $scope.contextAddLayers = 'template';
            var width = _canvas.width,
            height = _canvas.height,
            path = new fabric.Path("M0 0 H"+width+" V"+height+" H0z M "+width/2+" 0 A "+width/2+" "+height/2+", 0, 1, 0, "+width/2+" "+height+" A "+width/2+" "+height/2+", 0, 1, 0, "+width/2+" 0z");
            path.set({strokeWidth: 0, isAlwaysOnTop: true, fill: '#ffffff', selectable: false, evented: false});
            _canvas.add(path);                  
        }
        $scope.updateLayersList();
        _canvas.requestRenderAll();
    };
    $scope.selectAllLayers = function(){
        var _canvas = this.stages[this.currentStage]['canvas'];
        $scope.deactiveAllLayer();
        var objs = [];
        _canvas.forEachObject(function(obj, index) {
            if( obj.get('selectable') ) objs.push(obj);
        });
        var selection = new fabric.ActiveSelection(objs, {
            canvas: _canvas
        });
        selection.addWithUpdate();
        _canvas.setActiveObject(selection);
        $scope.renderStage();        
    };
    $scope.stageToJson = function(stage_id){
        stage_id = stage_id ? stage_id :  $scope.currentStage;
        $scope.renderStage(stage_id);
        var json = this.stages[stage_id]['canvas'].toJSON($scope.includeExport);
        return json;
    };
    $scope.loadStageFromJson = function(stage_id, json){
        var _canvas = this.stages[stage_id]['canvas'];
        _canvas.loadFromJSON(json, function() {  
            $scope.renderStage(stage_id);
        });
    };
    $scope.copyStage = function( stage_id ){
        stage_id = stage_id ? stage_id :  $scope.currentStage;
        $scope.tempStageDesign = {
            id: stage_id,
            design: $scope.stageToJson()
        };
    };
    $scope.pasteStage = function( dist_stage ){
        dist_stage = dist_stage ? dist_stage :  $scope.currentStage;
        $scope.loadStageFromJson(dist_stage, $scope.tempStageDesign.design);
        $scope.tempStageDesign = null;
    };
    $scope.clearClipboardDesign = function(){
        $scope.tempStageDesign = null;
    };  
    $scope.duplicateDesign = function( stage_id ){
        stage_id = stage_id ? stage_id :  $scope.currentStage;
        var next_stage_id = parseInt( stage_id ) + 1;
        $scope.copyStage( stage_id );
        if( angular.isDefined( $scope.stages[next_stage_id] ) ){
            var _canvas = $scope.stages[next_stage_id].canvas;
            $scope.onloadTemplate = true;
            $scope.contextAddLayers = 'template';
            _canvas.loadFromJSON($scope.tempStageDesign.design, function() {
                $scope.switchStage($scope.currentStage, 'next', 'top-bottom');
                $scope.renderStage(next_stage_id);
                $timeout(function(){
                    $scope.contextAddLayers = 'normal';
                    $scope.onloadTemplate = false;
                });
            });
        }
    };
    $scope.rotateLayer = function(command){
        var _canvas = this.stages[this.currentStage]['canvas'],
        item = _canvas.getActiveObject();
        $scope.beforeObjectModify(item);
        switch(command){
            case 'reflect-hoz':
                item.toggle("flipY");
                break;
            case 'reflect-ver':
                item.toggle("flipX");
                break;       
            case '90cw':
                var angle = item.get('angle') + 90;
                if (angle > 360) angle = angle - 360;
                if (angle < 0) angle = angle + 360;
                item.set({angle: angle});
                break;
            case '90ccw':
                var angle = item.get('angle') - 90;
                if (angle > 360) angle = angle - 360;
                if (angle < 0) angle = angle + 360;
                item.set({angle: angle});
                break;    
            case '180':
                var angle = item.get('angle') + 180;
                if (angle > 360) angle = angle - 360;
                if (angle < 0) angle = angle + 360;
                item.set({angle: angle});
                break;      
            default: 
                var angle = parseInt(command);
                item.set({angle: angle});
        };   
        item.setCoords();
        this.renderStage();
    };
    $scope.scaleLayer = function(command){
        var _stage = this.stages[this.currentStage],
            _canvas = _stage.canvas,
            obj = _canvas.getActiveObject();
        if (!obj) return;
        $scope.beforeObjectModify(obj);
        if( _stage.lockMovementY || _stage.lockMovementX ) return;
        var scaleX = obj.scaleX,
            scaleY = obj.scaleY,
            left = obj.left,
            top = obj.top,
            originX = obj.originX,
            originY = obj.originY,
            width = obj.width * scaleX,
            height = obj.height * scaleY,
            factor = command === "+" ? 1.1 : 0.9;   
        var tempScaleX = scaleX * factor,
            tempScaleY = scaleY * factor,
            tempWidth = width * factor,
            tempHeight = height * factor,
            tempLeft = left + width / 2 - tempWidth / 2,
            tempTop = top + height / 2 - tempHeight / 2;
        if( originX == 'center' ){
            tempLeft = left;
        }
        if( originY == 'center' ){
            tempTop = top;
        } 
        obj.scaleX = tempScaleX;
        obj.scaleY = tempScaleY;
        obj.left = tempLeft;
        obj.top = tempTop;
        obj.setCoords();
        $scope.renderStage();
    };    
    $scope.moveLayer = function( command, alt ){
        var _canvas = this.stages[this.currentStage].canvas,
            items = _canvas.getActiveObjects(),
            selection = _canvas.getActiveObject(),
            step = alt ? 1 : 10;
        if( items.length == 0 ) return;    
        angular.merge($scope.stages[$scope.currentStage].states.boundingObject, {visibility: 'hidden'});
        items.forEach(function( item ){
            $scope.beforeObjectModify(item);
            switch(command) {
                case 'up':
                    if( !item.get("lockMovementY") ) item.set('top', item.get('top') - step);
                    break;
                case 'down':
                    if( !item.get("lockMovementY") ) item.set('top', item.get('top') + step);
                    break;    
                case 'left':
                    if( !item.get("lockMovementX") ) item.set('left', item.get('left') - step);
                    break;
                case 'right':
                    if( !item.get("lockMovementX") ) item.set('left', item.get('left') + step);
                    break;               
            };  
            item.setCoords();
        });
        if( selection.type === 'activeSelection' ){
            selection.addWithUpdate();
        };
        this.updateApp();
        this.renderStage();
    };
    $scope.sortLayer = function(srcIndex, dstIndex){
        var _canvas = this.stages[this.currentStage].canvas;
        _canvas.item(srcIndex).moveTo(dstIndex);
        //$scope.deactiveAllLayer();
        $scope.renderStage();
        $scope.updateLayersList();
        $scope.updateApp();
    };
    $scope.updateLayersList = function(){
        var _stage = $scope.stages[$scope.currentStage],
        _canvas = _stage['canvas'];
        _stage.layers = [];
        _canvas.forEachObject(function(obj, index) {
            var layerInfo = {index: index, visible: obj.get('visible'), selectable: obj.get('selectable'), forceLock: obj.get('forceLock'), itemId: obj.get('itemId')};
            if( !obj.isAlwaysOnTop ){
                switch(obj.type) {
                    case 'i-text':
                    case 'text':
                    case 'textbox':
                    case 'curvedText':
                        layerInfo.type = 'text';
                        layerInfo.icon_class = 'text-fields';
                        layerInfo.text = obj.get('text');
                        layerInfo.editable = obj.get('editable');
                        if( angular.isDefined( obj.field_mapping ) ){
                            var field = _.filter($scope.settings.template_fields, { key: obj.field_mapping })[0];
                            if( angular.isDefined( field ) ){
                                field.value = layerInfo.text;
                            }
                        }
                        if( obj.lostChar == 1 ) layerInfo.lostChar = true;
                        break;
                    case 'image':
                    case 'custom-image':
                        layerInfo.type = 'image';
                        layerInfo.src = obj.getSvgSrc();
                        break;
                    case 'rect':
                    case 'triangle':
                    case 'line':
                    case 'polygon':                    
                    case 'circle':
                    case 'ellipse':
                        var type = obj.type == 'rect' ? 'rectangle' : obj.type;
                        layerInfo.icon_class = 'layer-'+type;
                        layerInfo.type = obj.type;
                        break;     
                    case 'path-group':                    
                    case 'path':
                        layerInfo.icon_class = 'vector';
                        layerInfo.type = 'path';
                        break; 
                    case 'group':
                        layerInfo.icon_class = 'layer-group';
                        layerInfo.type = 'group';
                        break;               
                    default:
                        layerInfo.type = obj.type;
                        break; 
                }
                _stage.layers.push(layerInfo);
            }
        });
        $timeout(function(){
            jQuery('#tab-layer .tab-scroll').perfectScrollbar('update');
        });
    }; 
    $scope.setStackLayerAlwaysOnTop = function(maybeRender){
        var _canvas = $scope.stages[$scope.currentStage]['canvas'];
        _canvas.forEachObject(function(obj) {
            if( obj.isAlwaysOnTop ){
                obj.bringToFront();
            }
        });
        if(maybeRender) $scope.renderStage();
    };
    $scope.setStackPosition = function(command, onLayer, _item){
        var item = _item ? _item : $scope.stages[$scope.currentStage]['canvas'].getActiveObject();
        $scope.beforeObjectModify(item);
        switch(command){
            case 'bring-front':
                item.bringToFront();
                $scope.setStackLayerAlwaysOnTop();
                break;
            case 'bring-forward':
                item.bringForward();
                break;
            case 'send-backward':
                item.sendBackwards();
                break;
            case 'send-back':
                item.sendToBack();
                break;
            default:
                var index = parseInt(command);
                item.moveTo(index);   
        }
        $scope.renderStage();
        if(!onLayer) $scope.updateLayersList();
    };  
    $scope.changeBackground = function(color){
        $scope.stages[$scope.currentStage].config.bgColor = color;
    };
    $scope.stageBgColorPicker = {
        status: false,
        currentColor: '#fff'
    };
    $scope.changeBackgroundCanvas = function (color) {
        var _stage = $scope.stages[$scope.currentStage],
            _canvas = _stage.canvas;
        $scope.showDesignTab();
        _canvas.backgroundColor = color;
        $scope.addColor(color);
        _canvas.renderAll();
    };
    $scope.removeBackgroundCanvas = function(){
        var _stage = $scope.stages[$scope.currentStage],
            _canvas = _stage.canvas;
        _canvas.backgroundColor = null;
        _canvas.renderAll();
    };
    $scope.changeFill = function(color){
        var _stage = $scope.stages[$scope.currentStage],
            _canvas = _stage.canvas;
        if( angular.equals({}, _canvas) ) return;
        var item = _canvas.getActiveObject();
        $scope.beforeObjectModify(item);
        if( !_stage.states.isPath ){
            item.set({fill: color});
            _stage.states.text.fill = color;
        }else{
            item.set({dirty: true});
            _.each(_stage.states.svg.groupPath[_stage.states.svg.currentPath].index, function(path_index){
                if( path_index > -1 ){
                    item._objects[path_index].set({fill: color});
                }else{
                    item.set({fill: color});
                }
            });
            _stage.states.svg.groupPath[_stage.states.svg.currentPath].color = color;
        }
        $scope.renderStage();
    };
    /* Text */
    $scope.addText = function(content, type, additionalObj){
        content = angular.isDefined(content) ? content : $scope.settings.nbdesigner_default_text;
        type = angular.isUndefined(type) ? 'bodytext' : type;
        var textType = 'IText',
        fontSize = 16,    
        fontName = NBDESIGNCONFIG.default_font.alias;
        var state= $scope.stages[$scope.currentStage].states;
        switch(type){
            case 'heading':
                textType = 'Textbox';
                fontSize = 42;
                break;
            case 'subheading':
                textType = 'Textbox';
                fontSize = 36;                
                break;           
        };
        var textObj = {
            fontFamily: fontName,
            radius: 50,
            objectCaching: false,
            fontSize: fontSize,            
//            width: $scope.stages[$scope.currentStage]['canvas'].width * 0.9,
            ptFontSize: fontSize  / state.ratioConvertFont
        };
        //if( textType == 'Textbox' ) angular.extend(textObj, {textAlign: 'center'});
        if( additionalObj ){
            angular.extend(textObj, additionalObj);
        };
        function addText(){
            $scope.stages[$scope.currentStage]['canvas'].add(new FabricWindow[textType](content, textObj));            
        };
        var font = new FontFaceObserver(fontName);
        //font.load($scope.settings.subsets[NBDESIGNCONFIG.default_font.subset]['preview_text']).then(function () {
        font.load(content).then(function () {
            fabric.util.clearFabricFontCache(fontName);
            var container = document.createElement('span'),
            scale = state.scaleRange[state.currentScaleIndex].ratio;
            container.innerHTML = content;
            container.style.cssText = [
                'position:absolute',
                'width:auto',
                'font-size: ' + fontSize  + 'px',
                'font-family: ' + fontName,
                'left:-99999px'
            ].join(' !important;');
            document.body.appendChild(container);
            var textWidth = container.clientWidth + 2;
            document.body.removeChild(container);
            var canvasWidth = $scope.stages[$scope.currentStage]['canvas'].width;
            if( ( textWidth * scale ) > ( canvasWidth * 0.8 ) ){
                textObj.width = canvasWidth * 0.8 / scale;
                textObj.fontSize = canvasWidth * 0.8 / ( textWidth * scale ) * fontSize;
                textObj.ptFontSize =  textObj.fontSize  / state.ratioConvertFont;
            }else{
                textObj.width = textWidth / scale;
            }
            $timeout(function(){
                addText();
            }, 100);
        }, function () {
            console.log('Fail to load font: '+fontName);
            addText();
        });      
    };
    $scope.addCurvedText = function( content ){
        var state= $scope.stages[$scope.currentStage].states;
        function addText(){
            var rtl = false;
            if(NBDESIGNCONFIG.lang_rtl == 'rtl'){
                rtl = true;
                content = content.split('').reverse().join('');
            }
            $scope.stages[$scope.currentStage]['canvas'].add(new fabric.CurvedText(content, {radius: 100, rtl: rtl, top: 100, left: 100, fontFamily: fontName, fontSize: 20, ptFontSize: 20  / state.ratioConvertFont}));    
        };
        var fontName = NBDESIGNCONFIG.default_font.alias,
        font = new FontFaceObserver(fontName);
        font.load($scope.settings.subsets[NBDESIGNCONFIG.default_font.subset]['preview_text']).then(function () {
            fabric.util.clearFabricFontCache(fontName);
            addText();
        }, function () {
            console.log('Fail to load font: '+fontName);
            addText();
        });
    };
    $scope.setLayerAttribute = function(type, value, item_index, layer_index){
        var _canvas = $scope.stages[$scope.currentStage]['canvas'];
        if( !appConfig.ready ) return;
        var item = angular.isDefined(item_index) ? _canvas.item(item_index) : _canvas.getActiveObject();
        if(!item) return;
        if( type == 'selectable' && !$scope.isTemplateMode && angular.isDefined(item.forceLock) && item.forceLock ) return;
        $scope.beforeObjectModify(item);
        var itemType = item.get('type');
        if( (itemType == 'i-text' || itemType == 'textbox') && type == 'forceLock'){
            value == true ? item.set({editable: false}) : item.set({editable: true});
        };
        if( type == 'forceLock'){
            value == true ? item.set({selectable: false}) : item.set({selectable: false});
        };
        var ob = {};ob[type] = value;
        item.set(ob);
        switch(type){
            case 'visible':
            case 'selectable':
                $scope.deactiveAllLayer();
                break;
            case 'lockScalingX':
                var controlVisible = itemType == 'textbox' ? [] : ['tl', 'tr', 'bl', 'br'];
                controlVisible.forEach(function(key){
                    item.setControlVisible(key, !value);
                });
                break;
            case 'lockScalingY':
                var controlVisible = itemType == 'textbox' ? [] : ['tl', 'tr', 'bl', 'br'];
                controlVisible.forEach(function(key){
                    item.setControlVisible(key, !value);
                });
                break;
            case 'lockRotation':
                item.setControlVisible('mtr', !value);	
                break;
        }
        $scope.renderStage();
        if(angular.isDefined(item_index)){
            $scope.stages[$scope.currentStage].layers[layer_index][type] = value;
        }else{
            $scope.stages[$scope.currentStage].states[type] = value;  
        }
        if( type != 'text' ){
            $scope.updateLayersList();
        } else if( $scope.settings.nbdesigner_enable_text_check_lang == 'yes' ){
            $scope.checkCharacter( item );
        }
        $scope.updateApp();
    };
    $scope.setTextAttribute = function(type, value, extra_option){
        var _stage = $scope.stages[$scope.currentStage],
            _states = _stage.states,
            _canvas = _stage['canvas'];
        if( !appConfig.ready ) return;  
        var item = _canvas.getActiveObject();
        var items = _canvas.getActiveObjects();
        if( !item || !items ) return;
        $scope.beforeObjectModify(item);
        var ob = {};ob[type] = value;
        item.set(ob);
        //item.set({[type]: value});
        if( type == 'fontSize' ){
            var minSize = arrayMin($scope.listFontSizeInPt);
            if( $scope.forceMinSize && minSize > value ) value = minSize;
            item.set({'ptFontSize': value, 'fontSize': value * _states.ratioConvertFont});
            _states.text.ptFontSize = value;
            $scope.updateTextPtFontSize( item );
        }
        switch(type){
            case 'is_uppercase':
                value ? item.set({'text': item.get('text').toUpperCase()}) : item.set({'text': item.get('text').toLowerCase()});
                break;
            case 'fontFamily':
                if(!_.filter(_states.usedFonts, ['alias', value]).length){
                    _states.usedFonts.push($scope.getFontInfo(value));
                }
                _states.text.font = $scope.getFontInfo(value);
                if( !_states.text.font.file.b ) {
                    item.set({fontWeight: 'normal'});
                    _states.text.fontWeight = 'normal';
                };
                if( !_states.text.font.file.i ) {
                    item.set({fontStyle: 'normal'});
                    _states.text.fontStyle = 'normal';
                };
                if( !_states.text.font.file.bi && item.get('fontWeight') == 'bold' && item.get('fontStyle') == 'italic' ) {
                    item.set({fontWeight: 'normal', fontStyle: 'normal'});
                    _states.text.fontWeight = 'normal';
                    _states.text.fontStyle = 'normal';
                };
                break;
        }
        _states.text[type] = value;
        if(type == 'rtl'){
            item.set({text: item.text.split('').reverse().join('')}) ;
        };
        if( type.indexOf("font") > -1 ){
            var font = new FontFaceObserver(_states.text.fontFamily, {weight: _states.text.fontWeight, style: _states.text.fontStyle});
            //font.load($scope.settings.subsets[_states.text.font.subset]['preview_text']).then(function () {
            font.load(item.get('text')).then(function () {
                fabric.util.clearFabricFontCache(_states.text.fontFamily);
                $timeout(function(){
                    item.initDimensions();
                    item.setCoords();
                    $scope.renderStage(); 
                });
            }, function () {
                item.setCoords();
                $scope.renderStage();
            });
            if( $scope.settings.nbdesigner_enable_text_check_lang == 'yes' ){
                $scope.checkCharacter( item );
            }
        }else{
            item.setCoords();
            $scope.renderStage();       
        }       
        $scope.updateLayersList();
    };    
    /* Image */
    $scope.addImage = function(url, showLoading, hideLoading, additionalTitle, additionalValue){
        var stage = $scope.stages[$scope.currentStage],
            _canvas = stage['canvas'],
            scale = stage.states.scaleRange[stage.states.currentScaleIndex].ratio;
        if( showLoading ) $scope.toggleStageLoading();
        fabric.Image.fromURL(url, function(op) {  
            if( $scope.stages[$scope.currentStage].states.elementUpload ){
                var object = _canvas.getActiveObject(),
                element = object.getElement();
                $scope.beforeObjectModify(object);
                element.setAttribute("src", url);  
                _canvas.getActiveObject().set({
                    dirty: true,
                    width: op.width,
                    height: op.height,
                    scaleX: object.width * object.scaleX / op.width,
                    scaleY: object.width * object.scaleX / op.width                  
                });
                object.setCoords();  
                $scope.deactiveAllLayer();
                $scope.renderStage();
            }else{
                var _ratio = NBDESIGNCONFIG.nbdesigner_enable_auto_fit_image == 'yes' ? 1 : 0.9;
                var max_width = _canvas.width / scale * _ratio,
                max_height = _canvas.height / scale * _ratio,
                new_width = max_width;
                if (op.width < max_width) new_width = op.width;
                var width_ratio = new_width / op.width,
                new_height = op.height * width_ratio;
                if (new_height > max_height) {
                    new_height = max_height;
                    var height_ratio = new_height / op.height;
                    new_width = op.width * height_ratio;
                };      
                op.set({
                    fill: '#ff0000',
                    scaleX: new_width / op.width,
                    scaleY: new_height / op.height
                });
                if( additionalTitle ) op[additionalTitle] = additionalValue;
                _canvas.add(op);
            }
            if( hideLoading ) $scope.toggleStageLoading();
        });
    };
    $scope.mask = 1;
    $scope.createClippingMask = function(obj){
        var stage = $scope.stages[$scope.currentStage],
        _canvas = stage['canvas'];
        obj = obj ? obj : _canvas.getActiveObject();
        var width = obj.get('width'),
        height = obj.get('height');
        var preItem = _canvas.item(_canvas.getObjects().length - 2),
        preObj = preItem.toObject();
        var klass = fabric.util.getKlass( preObj.type );
        var clipPathObj = {};
        angular.copy(preObj, clipPathObj);
        clipPathObj.absolutePositioned = true;
        clipPathObj.strokeWidth = 0;
        klass.fromObject(clipPathObj, function(clipPath){
            _canvas.remove(preItem);
            obj.set({clipPath: clipPath, perPixelTargetFind: true});
            $scope.renderStage();
        });
    };
    $scope.fixedClippingMask = function(obj, needreRender){
        var stage = $scope.stages[$scope.currentStage],
        _canvas = stage['canvas'];
        obj = obj ? obj : _canvas.getActiveObject();
        var scaleX = obj.get('scaleX'),
        scaleY = obj.get('scaleY'),
        width = obj.get('width'),
        height = obj.get('height'),
        angle = obj.get('angle'),
        scale = stage.states.scaleRange[stage.states.currentScaleIndex].ratio;
        obj.clipPath.set({
            absolutePositioned: true,
            top: obj.oCoords.tl.y / scale,
            left: obj.oCoords.tl.x / scale,
            width: width * scaleX,
            height: height * scaleY,
            angle: angle
        });
        needreRender = angular.isDefined(needreRender) ? needreRender : true;
        needreRender && $scope.renderStage();
    };
    $scope.removeClippingMask = function(obj, needreRender){
        var stage = $scope.stages[$scope.currentStage],
        _canvas = stage['canvas'];
        obj = obj ? obj : _canvas.getActiveObject();
        obj.clipPath = null;
        obj.perPixelTargetFind = false;
        needreRender = angular.isDefined(needreRender) ? needreRender : true;
        needreRender && $scope.renderStage();
    };
    $scope.applyFilter = function(obj, index, filter) {
        var stage = $scope.stages[$scope.currentStage],
        _canvas = stage['canvas'];
        obj = obj ? obj : _canvas.getActiveObject();
        obj.filters[index] = filter;
        obj.applyFilters();
        $scope.renderStage();
    };
    $scope.filterImage = function(){
        var f = fabric.Image.filters;
        $scope.applyFilter(null, 0, new f.Grayscale());
    };
    $scope.cropObj = {status: false};
    $scope.initCrop = function(){
        $scope.cropObj.status = true;
        jQuery('.nbd-popup.popup-nbd-crop').nbShowPopup();
        $scope.cropObj.src = $scope.stages[$scope.currentStage].states.origin_src;
        jQuery('.nbd-popup.popup-nbd-crop .overlay-popup').addClass('nbo-disable');
        jQuery('.nbd-popup.popup-nbd-crop').find('.overlay-main').removeClass('active');
        var img = new Image();
        img.src = $scope.cropObj.src;
        img.onload = function() {
            $scope.cropObj.width = this.width;
            $scope.cropObj.height = this.height;
            $timeout(function(){
                $scope.cropObj.canvas = $scope.cropObj.canvas ? $scope.cropObj.canvas : new FabricWindow.Canvas('crop-handle-wrap');
                var width = jQuery('#crop-source').width(),
                    height = jQuery('#crop-source').height();
                $scope.cropObj.canvas.setDimensions({'width' : width, 'height' : height});
                $scope.cropObj.canvas.clear();
                $scope.cropObj.canvas.on('mouse:down', function(e) {
                    $scope.cropObj.canvas.item(0);
                });            
                $scope.cropObj.canvas.on('object:modified', function( options ){
                    var obj = options.target;
                    var boundingRect = obj.getBoundingRect(true);
                    if (boundingRect.left < 0) obj.left = 0;
                    if (boundingRect.top < 0) obj.top = 0;
                    obj.setCoords();boundingRect = obj.getBoundingRect(true);
                    var left = boundingRect.left > 0 ? boundingRect.left : 0,
                        top = boundingRect.top > 0 ? boundingRect.top : 0;                
                    if (boundingRect.left + boundingRect.width > $scope.cropObj.canvas.getWidth() ){
                        obj.scaleX = obj.scaleX * ( ( $scope.cropObj.canvas.getWidth() - left ) / boundingRect.width );
                    }
                    if( boundingRect.top + boundingRect.height > $scope.cropObj.canvas.getHeight() ){
                        obj.scaleY = obj.scaleY * ( ( $scope.cropObj.canvas.getHeight() - top ) / boundingRect.height );
                    };
                    obj.setCoords();
                });
                var rect = new fabric.Rect({
                    left: 0,
                    top: 0,
                    fill: 'rgba(255,255,255,0.15)',
                    width: 200,
                    height: 200,
                    strokeWidth: 1,
                    stroke: '#ffffff',
                    strokeDashArray: [5, 5]
                });    
                if( angular.isDefined($scope.stages[$scope.currentStage].states.crop_left) ){
                    rect.set({
                        left: $scope.stages[$scope.currentStage].states.crop_left,
                        top: $scope.stages[$scope.currentStage].states.crop_top,
                        width: $scope.stages[$scope.currentStage].states.crop_width,
                        height: $scope.stages[$scope.currentStage].states.crop_height,
                        scaleX: $scope.stages[$scope.currentStage].states.crop_scaleX,
                        scaleY: $scope.stages[$scope.currentStage].states.crop_scaleY
                    });
                };
                $scope.cropObj.canvas.add(rect);
                var item = $scope.cropObj.canvas.item(0);
                item.setControlVisible('mtr', false);
                $scope.cropObj.canvas.setActiveObject(item);
                if( angular.isUndefined($scope.stages[$scope.currentStage].states.crop_left) ){
                    $scope.cropObj.canvas.viewportCenterObject(item);
                }
            }, 100);
        };
    };
    $scope.cancelCrop = function(){
        jQuery('.nbd-popup.popup-nbd-crop').find('.overlay-main').removeClass('active');
        jQuery('.nbd-popup.popup-nbd-crop .close-popup').triggerHandler('click');
    };
    $scope.cropImage = function(){
        function add_image( url ){
            fabric.Image.fromURL(url, function(op) { 
                var stage = $scope.stages[$scope.currentStage],
                    _canvas = stage['canvas'];
                var object = _canvas.getActiveObject(),
                element = object.getElement();
                $scope.beforeObjectModify(object);
                element.setAttribute("src", url);
                object.set({
                    origin_src: $scope.cropObj.src, 
                    crop_left: $scope.cropObj.bound.left,
                    crop_top: $scope.cropObj.bound.top,
                    crop_width: $scope.cropObj.bound.width,
                    crop_height: $scope.cropObj.bound.height,
                    crop_scaleX: $scope.cropObj.bound.scaleX,
                    crop_scaleY: $scope.cropObj.bound.scaleY
                });
                $scope.cropObj.bound = {};
                _canvas.getActiveObject().set({
                    dirty: true,
                    width: op.width,
                    height: op.height,
                    scaleX: object.width * object.scaleX / op.width,
                    scaleY: object.width * object.scaleX / op.width
                });
                object.setCoords();  
                $scope.renderStage();
                $scope.getCurrentLayerInfo();
                jQuery('.nbd-popup.popup-nbd-crop').find('.overlay-main').removeClass('active');
                jQuery('.nbd-popup.popup-nbd-crop .close-popup').triggerHandler('click');
            });
        }
        var bound = $scope.cropObj.canvas.item(0).getBoundingRect(true),
            imgScale = $scope.cropObj.width / $scope.cropObj.canvas.width;
        $scope.cropObj.bound = {
            left: $scope.cropObj.canvas.item(0).get('left'),
            top: $scope.cropObj.canvas.item(0).get('top'),
            width: $scope.cropObj.canvas.item(0).get('width'),
            height: $scope.cropObj.canvas.item(0).get('height'),
            scaleX: $scope.cropObj.canvas.item(0).get('scaleX'),
            scaleY: $scope.cropObj.canvas.item(0).get('scaleY')
        };
        var fd = new FormData();
        fd.append('nonce', NBDESIGNCONFIG['nonce']);
        fd.append('action', 'nbd_crop_image');
        fd.append('url', $scope.cropObj.src);
        fd.append('startX', bound.left * imgScale);
        fd.append('startY', bound.top * imgScale);
        fd.append('width', bound.width * imgScale);
        fd.append('height', bound.height * imgScale);  
        jQuery('.nbd-popup.popup-nbd-crop').find('.overlay-main').addClass('active');
        jQuery.ajax({
            url: NBDESIGNCONFIG['ajax_url'],
            method: "POST",   
            processData: false,
            contentType: false,
            data: fd
        }).done(function(data){
            if( data.flag == 1 ){
                add_image(data.url);
            }else{
                jQuery('.nbd-popup.popup-nbd-crop').find('.overlay-main').removeClass('active');
            }
        });        
    };
    /* SVG */
    $scope.addSvgFromString = function(svg, showLoading){
        if( angular.isUndefined(showLoading) ) $scope.toggleStageLoading();
        fabric.loadSVGFromString(svg, function(ob, op) {
            $scope._addSvg(ob, op, {name: ''}, true);
        });
    };
    $scope.addSvgFromMedia = function(art, $index){
        if( angular.isDefined($index) && $scope.settings.valid_license == '0' && $index > 19 ){
            alert($scope.settings.nbdlangs.pro_license_alert);
            return;
        }
        $scope.showDesignTab();
        $scope.toggleStageLoading();
        $http({
            method: 'GET',
            url: appConfig.mediaUrl + '/clipart?get_svg=' + art.url.replace("//dpeuzbvf3y4lr.cloudfront.net/", "")
        }).then(function successCallback(response){
            var svg = response.data.data;
            $scope.addSvgFromString(svg, false);
        }, function errorCallback(response) {
            console.log('Fail to load: svg');
        });
    };
    $scope.addArt = function(art, showLoading, hideLoading){
        if( showLoading ) $scope.toggleStageLoading();
        if(art.url.match(/\.(jpeg|jpg|gif|png)$/) != null){
            $scope.addImage(art.url, false, hideLoading);
        }else{
            fabric.loadSVGFromURL(art.url, function(ob, op) {
                if(ob){ 
                    $scope._addSvg(ob, op, art, hideLoading);
                }else{
                    alert('Try again!');
                    $scope.toggleStageLoading();
                }
            });
        }
    };
    $scope._addSvg = function(ob, op, art, hideLoading){
        var stage = $scope.stages[$scope.currentStage],
        _canvas = stage['canvas'],
        scale = stage.states.scaleRange[stage.states.currentScaleIndex].ratio,
        max_width = _canvas.width / scale * .9,
        max_height = _canvas.height / scale * .9,
        new_width = max_width;

        if (op.width < max_width) new_width = op.width;
        var width_ratio = new_width / op.width,
        new_height = op.height * width_ratio;
        if (new_height > max_height) {
            new_height = max_height;
            var height_ratio = new_height / op.height;
            new_width = op.width * height_ratio;
        }
        var object = fabric.util.groupSVGElements(ob, op);
        object.scaleToWidth(new_width);
        object.scaleToHeight(new_height);
        if( angular.isDefined( art.type ) && art.type == 'qrcode' ){
            object.set( { qrContent: art.qrContent, isQrcode: 1 } );
        } else if( angular.isDefined( art.type ) && art.type == 'barcode' ){
            object.set( { barCodeContent: art.barCodeContent, isBarcode: 1 } );
        }
        _canvas.add(object);
        if( hideLoading ) $scope.toggleStageLoading();
    };
    $scope.addQrCode = function(){      
        var qr = qrcode(4, 'L', NBDESIGNCONFIG.nbdesigner_default_color);
        qr.addData( $scope.resource.qrText );
        qr.make();
        var _qrcode = qr.createSvgTag();
        fabric.loadSVGFromString(_qrcode, function(ob, op) {
            $scope._addSvg(ob, op, {name: '', type: 'qrcode', qrContent: $scope.resource.qrText}, false);
        });
        jQuery('#barcode').html('');
        jQuery('.main-qrcode').html('').append(_qrcode);
    };
    $scope.updateQrCode = function(){
        var _stage = this.stages[this.currentStage],
            _canvas = _stage.canvas,
            obj = _canvas.getActiveObject();
        if (!obj) return;
        var qr = qrcode(4, 'L', NBDESIGNCONFIG.nbdesigner_default_color),
        content = $scope.stages[$scope.currentStage].states.qrContent;
        qr.addData( content );
        qr.make();
        var _qrcode = qr.createSvgTag();
        fabric.loadSVGFromString(_qrcode, function(ob, op) {
            var object = fabric.util.groupSVGElements(ob, op);
            obj.set({
                dirty: true,
                qrContent: content,
                path: object.path
            });
            $scope.renderStage();
        });
    };
    $scope.addBarCode = function(){
        jQuery('.main-qrcode').html('');
        JsBarcode("#barcode", $scope.resource.qrText, {
            font: NBDESIGNCONFIG.default_font.alias,
            lineColor: NBDESIGNCONFIG.nbdesigner_default_color
        });
        $timeout(function(){
            fabric.loadSVGFromString(jQuery('#barcode').wrap('<div/>').parent().html(), function(ob, op) {
                $scope._addSvg(ob, op, {name: '', barCodeContent: $scope.resource.qrText, type: 'barcode'}, false);
                jQuery('#barcode').unwrap();
            });
        });
    };
    $scope.updateBarCode = function(){
        var _stage = this.stages[this.currentStage],
            _canvas = _stage.canvas,
            obj = _canvas.getActiveObject();
        if (!obj) return;
        var content = $scope.stages[$scope.currentStage].states.barCodeContent;
        jQuery('.main-qrcode').html('');
        JsBarcode("#barcode", content, {
            font: NBDESIGNCONFIG.default_font.alias,
            lineColor: NBDESIGNCONFIG.nbdesigner_default_color
        });
        $timeout(function(){
            fabric.loadSVGFromString(jQuery('#barcode').wrap('<div/>').parent().html(), function(ob, op) {
                var object = fabric.util.groupSVGElements(ob, op);
                object.set({
                    originX: 'center',
                    originY: 'center'
                });
                obj.set({
                    dirty: true,
                    width: object.width,
                    barCodeContent: content,
                    _objects: object._objects
                });
                obj.setCoords();
                $scope.renderStage();
                jQuery('#barcode').unwrap();
            });
        });
    };
    $scope.mapLayerWith = function( key ){
        var _stage = this.stages[this.currentStage],
            _canvas = _stage.canvas,
            obj = _canvas.getActiveObject();
        if (!obj) return;
        if( angular.isDefined( obj.field_mapping ) && obj.field_mapping == key ){
            delete obj.field_mapping;
            delete $scope.stages[$scope.currentStage].states.field_mapping;
        }else{
            obj.set( { field_mapping: key } );
            $scope.stages[$scope.currentStage].states.field_mapping = key;
        }
        jQuery('.second-contexts').addClass('deactive');
    };
    $scope.generateVcard = function( callback ){
        var strVcard = '';
        function getFieldValue( fieldKey ){
            var value = '';
            _.each($scope.settings.vcard_fields, function(field, index){
                if( field.key == fieldKey ) value =  field.value;
            });
            return value;
        }
        strVcard += 'BEGIN:VCARD\nVERSION:3.0\n';
        strVcard += 'N:' + getFieldValue( 'last_name' ) + ';' + getFieldValue( 'first_name' ) + '\n' + 'FN:' + getFieldValue( 'first_name' ) + ' ' + getFieldValue( 'last_name' );
        strVcard += '\nADR;TYPE=home:;;'+ getFieldValue( 'address' ) + ';' + getFieldValue( 'city' ) + ';;' + getFieldValue( 'postcode' ) + ';' + getFieldValue( 'country' );
        strVcard += '\nTEL;TYPE=home:' + getFieldValue( 'phone' );
        strVcard += '\nTEL;TYPE=work:' + getFieldValue( 'mobile' );
        strVcard += '\nEMAIL;TYPE=internet,work:' + getFieldValue( 'email' );
        strVcard += '\nURL;TYPE=work:' + getFieldValue( 'website' );
        strVcard += '\nEND:VCARD';
        var qr = qrcode('0', 'M', NBDESIGNCONFIG.nbdesigner_default_color);
        qr.addData( strVcard );
        qr.make();
        var _qrcode = qr.createSvgTag();
        fabric.loadSVGFromString(_qrcode, function(ob, op) {
            var object = fabric.util.groupSVGElements(ob, op);
            if( typeof callback == 'function' ){
                callback( object );
            }else{
                var stage = $scope.stages[$scope.currentStage],
                _canvas = stage['canvas'],
                scale = stage.states.scaleRange[stage.states.currentScaleIndex].ratio,
                max_width = _canvas.width / scale * .9,
                max_height = _canvas.height / scale * .9,
                new_width = max_width;
                if (op.width < max_width) new_width = op.width;
                var width_ratio = new_width / op.width,
                new_height = op.height * width_ratio;
                if (new_height > max_height) {
                    new_height = max_height;
                    var height_ratio = new_height / op.height;
                    new_width = op.width * height_ratio;
                }
                object.scaleToWidth(new_width);
                object.scaleToHeight(new_height);
                object.v_card = 1;
                _canvas.add(object);
            }
        });
    };
    $scope.strVcard = '';
    $scope.buildVcart = function( config ){
        var infos = $scope.settings.user_infos;
        $scope.strVcard += 'BEGIN:VCARD\nVERSION:3.0\n';
        $scope.strVcard += 'N:'+infos.last_name.value+';'+infos.first_name.value+'\n'+ 'FN:'+infos.full_name.value;
        $scope.strVcard += '\nADR;TYPE=home:;;'+infos.address.value+';'+infos.city.value+';;'+infos.postcode.value+';'+infos.country.value;
        $scope.strVcard += '\nTEL;TYPE=home:'+infos.phone.value;
        $scope.strVcard += '\nTEL;TYPE=work:'+infos.mobile.value;
        $scope.strVcard += '\nEMAIL;TYPE=internet,work:'+infos.email.value;
        $scope.strVcard += '\nURL;TYPE=work:'+infos.website.value;
        $scope.strVcard += '\nEND:VCARD';
        var qr = qrcode('0', 'M');
        qr.addData( $scope.strVcard );
        qr.make();
        var _qrcode = qr.createSvgTag();
        fabric.loadSVGFromString(_qrcode, function(ob, op) {
            var stage = $scope.stages[$scope.currentStage],
            _canvas = stage['canvas'],
            scale = stage.states.scaleRange[stage.states.currentScaleIndex].ratio,
            max_width = _canvas.width / scale * .9,
            max_height = _canvas.height / scale * .9,
            new_width = max_width;
            if (op.width < max_width) new_width = op.width;
            var width_ratio = new_width / op.width,
            new_height = op.height * width_ratio;
            if (new_height > max_height) {
                new_height = max_height;
                var height_ratio = new_height / op.height;
                new_width = op.width * height_ratio;
            }
            var object = fabric.util.groupSVGElements(ob, op);
            if( config ){
                object.set(config);
            }else{
                object.scaleToWidth(new_width);
                object.scaleToHeight(new_height);
            }
            object.vcard = 1;
            _canvas.add(object);
        });        
    };
    $scope.addUserInfo = function( iIndex ){
        var state= $scope.stages[$scope.currentStage].states;
        var ptPontSize = ( ['first_name', 'last_name', 'full_name'].indexOf(iIndex) > -1 ) ? 9 : 7;
        var fontName = ( ['first_name', 'last_name', 'full_name'].indexOf(iIndex) > -1 ) ? 'nbfontf7afb53fe3' : 'nbfont92f8d8c63d';
        //fontName = fontName = NBDESIGNCONFIG.default_font.alias;
        var textObj = {
            fontFamily: fontName,
            radius: 50,
            objectCaching: false,
            fontSize: ptPontSize * state.ratioConvertFont,
            ptFontSize: ptPontSize
        };
        textObj[iIndex] = 1;
        var textType = iIndex == 'title' ? 'Textbox' : 'IText';
        function addText(){
            $scope.stages[$scope.currentStage]['canvas'].add(new FabricWindow[textType]($scope.settings.user_infos[iIndex].value, textObj));            
        };
        state.text.font = $scope.getFontInfo(fontName);
        if(!_.filter(state.usedFonts, ['alias', fontName]).length){
            state.usedFonts.push(state.text.font);
        };        
        var font_id = fontName.replace(/\s/gi, '').toLowerCase();
        if( !jQuery('#' + font_id).length ){
            var font_url =  state.text.font.url;
            if(! ( state.text.font.url.indexOf("http") > -1)) font_url = NBDESIGNCONFIG['font_url'] +  state.text.font.url; 
            var css = "";
            css = "<style type='text/css' id='" + font_id + "' >";
            css += "@font-face {font-family: '" + fontName + "';";
            css += "src: local('\u263a'),";
            css += "url('" + font_url + "') format('truetype')";
            css += "}";
            css += "</style>";
            jQuery("head").append(css);
        }
        var font = new FontFaceObserver(fontName);
        font.load($scope.settings.subsets[state.text.font.subset]['preview_text']).then(function () {
            fabric.util.clearFabricFontCache(fontName);
            addText();
        }, function () {
            console.log('Fail to load font: '+fontName);
            addText();
        }); 
    };
    $scope.initContact = function(){
        $scope.settings.contact_sheet = [];
        $scope.settings.contact_sheets.currentContactIndex = 0;
        angular.copy($scope.settings.contact_sheets.contact[$scope.settings.contact_sheets.first_con], $scope.settings.contact_sheet);
    };
    $scope.changeContact = function(){
        angular.copy($scope.settings.contact_sheets.contact[$scope.settings.contact_sheets.first_con], $scope.settings.contact_sheet);
    };
    $scope.addContactSheet = function(index, cIndex){
        var state= $scope.stages[$scope.currentStage].states;
        if( index != 'contact' ){
            if( index != 'avatar' ){
                var exObj = {textAlign: 'left', ptFontSize: 9, fontSize: 9 * state.ratioConvertFont}; exObj[index] = 1;
                $scope.addText($scope.settings.contact_sheets[index], 'heading', exObj);
            }else{
                $scope.addImage($scope.settings.contact_sheets[index], true, true, index, 1);
            }
        }else{
            if( angular.isDefined($scope.settings.contact_sheet[$scope.settings.contact_sheets.currentContactIndex]) ){
                var field = $scope.settings.contact_sheet[$scope.settings.contact_sheets.currentContactIndex];
                if( cIndex != 'c_avatar' ){
                    var exObj = {textAlign: 'left', ptFontSize: 9, fontSize: 9 * state.ratioConvertFont}; exObj[index] = $scope.settings.contact_sheets.currentContactIndex;
                    $scope.addText(field[cIndex], 'heading', exObj);
                }else{
                    $scope.addImage(field[cIndex], true, true, cIndex, $scope.settings.contact_sheets.currentContactIndex);
                }
            }
        }
    };
    $scope.currentCountryCode = '';
    $scope.contacts = [];
    $scope._contacts = ['','','','','','','',''];
    $scope.getUsersByCountryCode = function( ){
        if($scope.currentCountryCode == '') return;
        $scope.toggleStageLoading();
        $scope.settings.contact_sheet = [];
        $http({
            method: 'GET',
            url: NBDESIGNCONFIG['ajax_url'] + '?action=nbd_get_users_by_country_code&country_code='+$scope.currentCountryCode
        }).then(function successCallback(response){
            $scope.contacts = response.data;
            $scope.toggleStageLoading();
        }, function errorCallback(response) {
            console.log('Fail to load: contacts');
            $scope.toggleStageLoading();
        }); 
    };
    $scope._changeContact = function(i){
        $scope.settings.contact_sheet[i] = {};
        angular.copy($scope.contacts[i], $scope.settings.contact_sheet[i]);
    };
    $scope.updateTemplate = function(){
        $scope.clearAllStage();
        $scope.insertTemplate(false, {id: $scope.currentLocalTempId});
    };
    $scope.tourGuide = {
        firstTime: true,
        currentStep: -1,
        steps: []
    };
    $scope.tourGuideShowing = false;
    $scope.startTourGuide = function(){
        if( $scope.settings.is_mobile ){
            return;
        }
        if( $scope.tourGuide.currentStep == -1 ){
            var steps = [];
            jQuery.each(jQuery('[data-tour]'), function(){
                var el = jQuery(this),
                dataTour = el.attr('data-tour'),
                priority = el.attr('data-tour-priority');
                steps.push({
                    priority: priority,
                    template: 'tour_guide.' + dataTour,
                    element: el
                });
            });
            $scope.tourGuide.steps = _.sortBy(steps, [function(s) { return s.priority; }]);
        };
        $scope.tourGuideShowing = true;
        localStorage.setItem('showTourGuide', 1);
        $scope.tourGuide.currentStep = -1;
        $timeout(function(){
            $scope.nextTour();
        },  300);
    };
    $scope.processTourComponents = function( close ){
        var targetEl = $scope.tourGuide.steps[$scope.tourGuide.currentStep].element;
        if( close ){
            jQuery('.tour-guide').removeClass('active');
            $scope.tourGuideShowing = false;
            $scope.showTemplateFiledsPopup();
            return;
        };
        var tourStepEl = jQuery('.nbd-tourStep'),
            bgTourTop = jQuery('.bgTour-top'),
            bgTourRight = jQuery('.bgTour-right'),
            bgTourBottom = jQuery('.bgTour-bottom'),
            bgTourLeft = jQuery('.bgTour-left'),
            height = targetEl.outerHeight(),
            width = targetEl.outerWidth();
        if($scope.tourGuide.firstTime){
            width = 75;
            $scope.tourGuide.firstTime = false;
        }
        var offset = targetEl.offset(),
            top = offset.top,
            left = offset.left,
            bottom = jQuery(window).outerHeight() - height - top,
            right = jQuery(window).outerWidth() - left - width;
        bgTourTop.css({
            height: top + 'px'
        });
        bgTourBottom.css({
            height: bottom + 'px'
        });
        bgTourRight.css({
            top: top + 'px',
            left: left + width + 'px',
            height: height + 'px'
        });
        bgTourLeft.css({
            top: top + 'px',
            width: left + 'px',
            height: height + 'px'
        });
        if ($(window).width() > (left * 2)) {
            tourStepEl.attr('data-pos', 'left');
            tourStepEl.css({
                top: top + 'px',
                left: left + width + 7 + 'px'
            });
        }else{
            tourStepEl.attr('data-pos', 'right');
            tourStepEl.css({
                top: top + 'px',
                left: left - 7 + 'px'
            });
        }
        jQuery('.tour-guide').addClass('active');
    };
    $scope.nextTour = function(){
        if( $scope.tourGuide.currentStep < ($scope.tourGuide.steps.length - 1) ){
            $scope.tourGuide.currentStep++;
            $scope.processTourComponents();
        }
    };
    $scope.prevTour = function(){
        if( $scope.tourGuide.currentStep > 0 ){
            $scope.tourGuide.currentStep--;
            $scope.processTourComponents();
        }
    };
    jQuery(document).ready(function () {
        $scope.init();
        if( NBDESIGNCONFIG['ui_mode'] != 3){
            jQuery(document).triggerHandler( 'nbd_modern_app_init' );
        }
    });
}]);
nbdApp.factory('FabricWindow', ['$window', function($window) {
    /* Fabric configuration */
    fabric.disableStyleCopyPaste = true;
    fabric.Object.NUM_FRACTION_DIGITS = 10;
    $window.fabric.Object.prototype.set({ 
        transparentCorners: false,
        borderColor: 'rgba(79, 84, 103,0.7)',
        cornerStyle: 'circle',
        cornerColor: 'rgba(255,255,255,1)',
        borderDashArray:[2,2],
        cornerStrokeColor: 'rgba(63, 70, 82,1)',
        fill : NBDESIGNCONFIG.nbdesigner_default_color, 
        hoverCursor: 'pointer',
        borderOpacityWhenMoving: 0
    });
    if( NBDESIGNCONFIG.nbdesigner_object_center_scaling == 'yes' ){
        $window.fabric.Object.prototype.set({centeredScaling: true });
    };
    if( checkMobileDevice() ) $window.fabric.Object.prototype.set({cornerSize: 17});
    $window.fabric.IText.prototype.set({
        cursorWidth: 1,
        cursorColor: '#000',
        selectionColor: "rgba(1, 196, 204, 0.3)",
        cursorDuration: 500   
    });
    if( NBDESIGNCONFIG.nbdesigner_enable_text_free_transform == 'no' ){
        $window.fabric.IText.prototype.set({
            _controlsVisibility: {
                tl: true,
                tr: true,
                br: true,
                bl: true,
                ml: false,
                mt: false,
                mr: false,
                mb: false,
                mtr: NBDESIGNCONFIG.nbdesigner_text_rotate == '1' ? true : false
            }
        });
    }
    $window.fabric.Canvas.prototype.set({
        preserveObjectStacking : true,
        controlsAboveOverlay: true,
        selectionColor: 'rgba(1, 196, 204, 0.3)',
        selectionBorderColor: '#01c4cc',
        selectionLineWidth: 0.5,
        centeredKey: "shiftKey",
        uniScaleKey: "altKey"
    });  
    $window.fabric.Textbox.prototype.set({
        _controlsVisibility: {
            tl: true,
            tr: true,
            br: true,
            bl: true,
            ml: true,
            mt: false,
            mr: true,
            mb: false,
            mtr: NBDESIGNCONFIG.nbdesigner_text_rotate == '1' ? true : false            
        }
    });
    $window.fabric.Image.prototype.set({
        originX: 'center',
        originY: 'center'
    });    
    $window.fabric.CurvedText.prototype.set({
        originX: 'center',
        originY: 'top'
    }); 
    fabric.enableGLFiltering = false;
    fabric.PathGroup = { };
    fabric.PathGroup.fromObject = function (object, callback) {
        var originalPaths = object.paths;
        delete object.paths;
        if (typeof originalPaths === 'string') {
            fabric.loadSVGFromURL(originalPaths, function (elements) {
                var pathUrl = originalPaths;
                var group = fabric.util.groupSVGElements(elements, object, pathUrl);
                group.type = 'group';
                object.paths = originalPaths;
                callback(group);
            });
        } else {
            fabric.util.enlivenObjects(originalPaths, function (enlivenedObjects) {
                enlivenedObjects.forEach(function (obj) {
                    obj._removeTransformMatrix();
                });
                var group = new fabric.Group(enlivenedObjects, object);
                group.type = 'group';
                object.paths = originalPaths;
                callback(group);
            });
        }
    };  
    fabric.LimitedTextbox = fabric.util.createClass(fabric.Textbox, {
        // Override `insertChars` method
        updateFromTextArea: function () {
//            if (this.maxWidth) {
//                var textWidthUnderCursor = this._getLineWidth(this.ctx, this.get2DCursorLocation().lineIndex);
//                if (textWidthUnderCursor + this.ctx.measureText(chars).width > this.maxWidth) {
//                    chars = '\n' + chars;
//                }
//            }
//            if (this.maxLines) {
//                var newLinesLength = this._wrapText(this.ctx, this.text + chars).length;
//                if (newLinesLength > this.maxLines) {
//                    return;
//                }
//            }
            // Call parent class method
            this.callSuper('updateFromTextArea');
        }        
    });
    return $window.fabric;
}]);
nbdApp.directive('nbdCanvas', ['FabricWindow', '$timeout', '$rootScope', function(FabricWindow, $timeout, $rootScope){
    return {
        restrict: "AE",
        scope: {
            stage: '=stage',
            index: '@',
            last: '@'
        },
        link: function( scope, element, attrs ) {
            $timeout(function() {
                scope.stage.canvas = new FabricWindow.Canvas('nbd-stage-'+scope.index);
                scope.$emit('canvas:created', scope.index, scope.last);
                element.parent().children().on("contextmenu", function(e){
                    e.preventDefault();
                    scope.$emit('nbd:contextmenu', e);
                });
            });  
        }
    }
}]);
nbdApp.directive('keypress', ['$window', function($window){
    return {
        restrict: "AE",
        link: function( scope, element, attrs ) {
            $window.document.addEventListener("keydown", function(e){
                scope.$emit('nbd:keypress', e);
            }, false);           
        }
    }
}]);
nbdApp.directive('nbdScroll', ['$timeout', function($timeout){
    return {
        restrict: "AE",
        scope: {
            container: '@',
            type: '@',
            offset: '@',
            currentType: '@',
            action: '&nbdScroll'
        },
        link: function( scope, element, attrs ) {
            $timeout(function() {
                var el = scope.type != 'font' ? jQuery(scope.container + ' .tab-scroll') : jQuery(scope.container),
                    offset = parseInt(scope.offset),
                    elInfo = jQuery(scope.container + ' .info-support');
                el.on('ps-scroll-y', function(){
                    if( scope.type == 'globalTemplate' && angular.isDefined( scope.currentType ) && scope.currentType != 'global' ) return;
                    if(el.prop("clientHeight") != el.prop("scrollHeight") && ((el.prop("scrollTop") + el.prop("clientHeight") - el.prop("scrollHeight") + offset) > 0) ){
                        scope.action({container: scope.container, type: scope.type});
                    };
                    if( elInfo.length ){
                        el.prop("scrollTop") > 1500 && elInfo.addClass('slideInDown animated show') || elInfo.removeClass('slideInDown animated show');
                    }
                });
            });
        }
    }
}]);
nbdApp.directive('nbdLayer', ['$timeout', function($timeout){
    return {
        restrict: "AE",
        scope: {
            action: '&nbdLayer'
        },
        link: function( scope, element, attrs ) {
            $timeout(function() {
                if( !checkMobileDevice() ){
                    jQuery(element).sortable({
                        placeholder: "sortable-placeholder",
                        containment: '#tab-layer',
                        stop: function(event, ui) {
                            var srcIndex = jQuery(this).attr('data-prev-index'),
                                oldIndex = jQuery(this).attr('data-previndex'),
                                newIndex = ui.item.index(),
                                dstIndex = 0;
                            if( oldIndex > newIndex ){
                                dstIndex = jQuery(ui.item).next().attr('data-index')
                            }else {
                                dstIndex = jQuery(ui.item).prev().attr('data-index')
                            };
                            jQuery(this).removeAttr('data-previndex');
                            jQuery(this).removeAttr('data-prev-index');
                            scope.action({srcIndex: srcIndex, dstIndex: dstIndex});
                        },
                        start: function(e, ui) {
                            jQuery(this).attr('data-prev-index', jQuery(ui.item).attr('data-index'));
                            jQuery(this).attr('data-previndex', ui.item.index());
                        },                    
                    });
                }
            });
        }
    };
}]);
nbdApp.directive('endRepeatColorPicker', ['$timeout', function($timeout){
    return {
        restrict: "A",
        link: function( scope, element, attrs ) {
            $timeout(function() {
                jQuery(element).nbdColorPalette();
            });
        }
    }    
}]);
nbdApp.directive('repeatEnd', [ function(){
    return {
        restrict: "AE",    
        link: function( scope, element, attrs ) {  
            if(scope.$last) {
                scope.$eval(attrs.repeatEnd);
            }            
        }
    }
}]);
nbdApp.directive('keyup', ['$window', function($window){
    return {
        restrict: "AE",
        link: function( scope, element, attrs ) {
            $window.document.addEventListener("keyup", function(e){
                scope.$emit('nbd:keyup', e);
            }, false);           
        }
    }
}]);
nbdApp.filter('keyboardShortcut', function($window) {
    return function(str) {
        if (!str)
            return;
        var keys = str.split('-');
        var isOSX = /Mac OS X/.test($window.navigator.userAgent);
        var seperator = (!isOSX || keys.length > 2) ? '+' : '';
        var abbreviations = {
            M: isOSX ? '⌘' : 'Ctrl',
            A: isOSX ? 'Option' : 'Alt',
            S: 'Shift'
        };
        return keys.map(function (key, index) {
            var last = index == keys.length - 1;
            return last ? key : abbreviations[key];
        }).join(seperator);
    };    
});
nbdApp.filter("filterFont", function() {
    return function(fonts, filterFont) {
        var arrFont = [];
        angular.forEach(fonts, function(font, key) {
            var check = [];
            check['limit'] = arrFont.length > ( filterFont.perPage * filterFont.currentPage  - 1 ) ? false : true;
            if( !!filterFont.search ){
                check['name'] = font.name.toLowerCase().indexOf(filterFont.search.toLowerCase()) >= 0 ? true : false;
                if( angular.isDefined( font.display_name ) && font.display_name != '' ){
                    check['name'] = check['name'] || (font.display_name.toLowerCase().indexOf(filterFont.search.toLowerCase()) >= 0 ? true : false);
                }
            }else{
                check['name'] = true;
            };
            if( check['limit'] && check['name'] )arrFont.push(font);
        });
        arrFont = _.sortBy(arrFont, [function(o) { return o.name; }]);
        return arrFont
    }
});
nbdApp.filter("filterArt", function() {
    return function(arts, filterArt) {
        var arrArt = [];
        arts = _.sortBy(arts, [function(o) { return o.name; }]);
        angular.forEach(arts, function(art, key) {
            var check = [];
            check['limit'] = arrArt.length > ( filterArt.perPage * filterArt.currentPage  - 1 ) ? false : true;
            if( !!filterArt.search ){
               check['name'] = art.name.toLowerCase().indexOf(filterArt.search.toLowerCase()) >= 0 ? true : false;
            }else{
                check['name'] = true;
            };
            if( !!filterArt.currentCat.id ){
                check['cat'] = _.includes(art.cat, filterArt.currentCat.id) ? true : false;
            }else{
                check['cat'] = true;
            }
            if( check['limit'] && check['name'] && check['cat'] ) arrArt.push(art);
        });
        return arrArt
    }
});
nbdApp.filter('reverse', function() {
    return function(items) {
        return items.slice().reverse();
    };
});
nbdApp.directive("imageOnLoad", [ function() {
    return {
        restrict: "A",
        scope: {
            src: '=imageOnLoad'
        }, 
        link: function(scope, element) {
            var img = new Image();
            img.onload = function(){
                element.removeClass('image-onload');
            };
            img.src = scope.src;
            element.addClass('image-onload');
        }
    };
}]);    
nbdApp.directive("fontOnLoad", [ function() {
    return {
        restrict: "A",
        scope: {
            font: '=',
            preview: '=',
            loadFontFailAction: '&'
        },
        link: function(scope, element) {
            var fontName = scope.font.alias,
                fontType = scope.font.type;
            if( fontName == '' ) return;
            var font_id = fontName.replace(/\s/gi, '').toLowerCase();
            if( !jQuery('#' + font_id).length ){
                if(fontType == 'google'){
                    jQuery('head').append('<link id="' + font_id + '" href="https://fonts.googleapis.com/css?family='+ fontName.replace(/\s/gi, '+') +':400,400i,700,700i" rel="stylesheet" type="text/css">');
                }else{
                    var css = "<style type='text/css' id='" + font_id + "' >";
                    _.each(scope.font.file, function (file, index) {
                        var font_url = file;
                        if(! (file.indexOf("http") > -1)) font_url = NBDESIGNCONFIG['font_url'] + file;
                        css += "@font-face {font-family: '" + fontName + "';";
                        css += "src: local('\u263a'), ";
                        css += "url('" + font_url + "') format('truetype');";
                        switch(index){
                            case "r":
                                css += "font-weight: normal;font-style: normal;"
                                break;
                            case "b":
                                css += "font-weight: bold;font-style: normal;"
                                break;
                            case "i":
                                css += "font-weight: normal;font-style: italic;"
                                break;
                            case "bi":
                                css += "font-weight: bold;font-style: italic;"
                                break;
                        };
                        css += "}";
                    });
                    css += "</style>";
                    jQuery("head").append(css);
                }
            };
            var font = new FontFaceObserver(fontName);
            font.load(scope.preview, 2E4).then(function () {
                element.removeClass('font-loading');
            }, function () {
                scope.loadFontFailAction({font: scope.font});
				element.removeClass("font-loading")
            }); 
            element.addClass('font-loading');
        }
    }
}]);
nbdApp.directive("nbdUploadFile", ['$timeout', function($timeout) {
    return {
        restrict: "A",
        scope: {
            uploadFile: '&nbdUploadFile'
        },
        link: function(scope, element) {
            $timeout(function() {
                jQuery( element ).on('change', function(){
                    handleFiles(this.files);
                });
                function handleFiles(files) {
                    if(files.length > 0) scope.uploadFile({files: files});
                }
            });
        }
    };
}]);
nbdApp.directive("nbdDndFile", ['$timeout', function($timeout) {
    return {
        restrict: "A",
        scope: {
            uploadFile: '&nbdDndFile'
        },
        link: function(scope, element) {  
            $timeout(function() {
                var dropArea = jQuery(element),
                Input = dropArea.find('input[type="file"]');
                _.each(['dragenter', 'dragover'], function(eventName, key) {
                    dropArea.on(eventName, highlight)
                });
                _.each(['dragleave', 'drop'], function(eventName, key) {
                    dropArea.on(eventName, unhighlight)
                });
                function highlight(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropArea.addClass('highlight');
                };
                function unhighlight(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    dropArea.removeClass('highlight');
                };
                dropArea.on('drop', handleDrop);
                function handleDrop(e) {
                    if( jQuery('#accept-term').length && !jQuery('#accept-term').is(':checked') ) {
                        alert(NBDESIGNCONFIG.nbdlangs.alert_upload_term);
                        return;
                    }else{
                        if(e.originalEvent.dataTransfer){
                            if(e.originalEvent.dataTransfer.files.length) {
                                e.preventDefault();
                                e.stopPropagation();
                                handleFiles(e.originalEvent.dataTransfer.files);
                            }                        
                        }                        
                    }
                };
                dropArea.on('click', function(e){
                    Input.click();
                });
                Input.on('click', function(e){
                    e.stopPropagation();
                    if( jQuery('#accept-term').length && !jQuery('#accept-term').is(':checked') ) {
                        alert(NBDESIGNCONFIG.nbdlangs.alert_upload_term);
                        e.preventDefault();
                        return;
                    }
                });
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
nbdApp.directive("nbdDrag", [ function() {
    return {
        restrict: "AE",
        scope: {
            url: '=nbdDrag',
            extenal: '@extenal',
            type: '@type'
        },
        link: function( scope, element, attrs ) {
            element.attr("draggable", "true");
            element.on('dragstart', function(event) { 
                event.originalEvent.dataTransfer.setData("src",scope.url);                    
                event.originalEvent.dataTransfer.setData("extenal",scope.extenal);             
                event.originalEvent.dataTransfer.setData("type",scope.type);                
            });          
        }
    }        
}]);
nbdApp.directive('rulerGuideline', ['$timeout',  function($timeout){
    return {
       restrict: "AE",
        scope: {
            direction: '@rulerGuideline',
            preventClick: '=preventClick',
            offset: '=offset',
            ratio: '@ratio',
            cwidth: '@cwidth'
        },
        link: function( scope, element, attrs ){
            $timeout(function(){
                var el = jQuery(element);
                var stageEl = el.parents('.stage');
                el.on('mousedown', function(event) {
                    stageEl.find('.guide-backdrop').removeClass('nbd-prevent-event');
                    stageEl.on('mousemove.nbd-move-guideline', function(event){
                        var ratio = parseFloat(scope.ratio);
                        var cwidth = parseFloat(scope.cwidth);
                        var stagePos = stageEl.offset(),
                        top = event.pageY - stagePos.top + stageEl.scrollTop(),
                        left = event.pageX - stagePos.left + stageEl.scrollLeft();
                        top = top <= 40 ? top : (40 + (top - 40) / ratio);
                        var stageElWidth = stageEl.width();
                        var additionalPadding = (cwidth * ratio - stageElWidth) / 2;
                        if( additionalPadding < 0  ){
                            left = left > 50 ? (stageElWidth / 2 - (stageElWidth / 2 + 50 - left)/ratio + 50) : left;
                        }else{
                            left = stageElWidth / 2 - (stageElWidth / 2 + 50 + additionalPadding - left)/ratio + 50;
                        };
                        scope.offset = scope.direction == 'hor' ? top : left;
                    });
                    event.preventDefault();
                }).on('mouseup', function(event){
                    stageEl.off('mousemove.nbd-move-guideline');
                    stageEl.find('.guide-backdrop').addClass('nbd-prevent-event');
                });
                stageEl.on('mouseup', function(){
                    stageEl.off('mousemove.nbd-move-guideline');
                    stageEl.find('.guide-backdrop').addClass('nbd-prevent-event');
                });
                el.trigger('mousedown');
            });
        }
    };
}]);
nbdApp.directive("nbdColorPicker", ['$timeout', function($timeout) {
    return {
        restrict: "C",
        scope: {
            cattr: '@',
            color: '@'
        },
        link: function(scope, element) {
            jQuery(element).on('click', function(){
                $timeout(function() {
                    jQuery('#nbd-global-color-palette').addClass('show');
                });
                scope.$emit('nbd:picker', scope.cattr, scope.color);
            });
        }
    };
}]);
nbdApp.directive("nbdClearStage", function() {
    return {
        restrict: "A",
        link: function(scope, element) {
            jQuery(element).on('click', function(){
                //$timeout(function() {
                    jQuery('.nbd-popup.clear-stage-alert, .v-popup.v-popup-select').nbShowPopup();
                //});   
            });  
        }
    };    
});
if(NBDESIGNCONFIG.nbdesigner_show_all_template_sides == 'yes'){
    nbdApp.directive("nbdTemplateHover", ['$timeout', function($timeout) {
        return {
            restrict: "A",
            scope: {
                idTmpl: '@nbdTemplateHover'
            },
            link: function(scope, element) {
                if (!checkMobileDevice()) {
                    $timeout(function() {
                        jQuery(element).tooltipster({
                            content: jQuery('#tooltip_content_' + scope.idTmpl),
                            interactive: true,
                            side: "right",
                            theme: 'tooltipster-template',
                            functionReady: function(){
                                jQuery('#tooltip_content_' + scope.idTmpl).perfectScrollbar('update');
                            }
                        });
                    }, 500);
                }; 
            }
        };    
    }]);
};
nbdApp.directive("contextSubMenu", ['$timeout', '$window', function($timeout, $window){
    return {
        restrict: "C",
        link: function(scope, element) {
            jQuery( element ).on( 'mouseenter', function(){
                jQuery('.second-contexts').removeClass('deactive');
                $timeout(function(){
                    var workBenchWidth  = $window.innerWidth,
                    workBenchHeight     = $window.innerHeight,
                    position            = jQuery( element ).offset(),
                    subMenu             = jQuery( element ).find( '.second-contexts' ),
                    parentWidth         = jQuery( element ).parent('.contexts').width(),
                    subMenuWidth        = subMenu.width(),
                    subMenuHeight       = subMenu.height();
                    if( workBenchWidth < ( position.left + subMenuWidth + parentWidth + 15 ) ) {
                        subMenu.removeClass( 'left' ).addClass( 'right' );
                    }else{
                        subMenu.removeClass( 'right' ).addClass( 'left' );
                    }
                    if( workBenchHeight < ( position.top + subMenuHeight + 15 )){
                        subMenu.css('bottom', '-40px');
                    }else{
                        subMenu.css('bottom', 'unset');
                    }
                }, 100);
            } );
        }
    };
}]);
nbdApp.directive("nbdPopupTrigger", ['$timeout', function($timeout) {
    return {
        restrict: "A",
        link: function(scope, element) {
            $timeout(function() {
                jQuery(element).on('click', function () {
                    var target = jQuery(this).attr('data-popup');
                    jQuery('.' + target).nbShowPopup();
                });
            });
        }
    };    
}]);
nbdApp.directive("nbdPerfectScroll", function($timeout) {
    return {
        restrict: "A",
        link: function(scope, element) {
            $timeout(function(){
                jQuery(element).perfectScrollbar();
            })
        }
    };    
});
nbdApp.directive('convertToNumber', function() {
    return {
        require: 'ngModel',
        link: function(scope, element, attrs, ngModel) {
            ngModel.$parsers.push(function(val) {
                return val != null ? parseInt(val, 10) : null;
            });
            ngModel.$formatters.push(function(val) {
                return val != null ? '' + val : null;
            });
        }
    };
});
nbdApp.factory('NBDDataFactory', function($http){
    return {
        get : function(action, data, callback, progressCallback) {
            var formData = new FormData();
            formData.append("action", action);
            var nonce = action == 'nbd_get_resource' ? NBDESIGNCONFIG['nonce_get'] : NBDESIGNCONFIG['nonce'];
            formData.append("nonce", nonce);
            var isUploadFile = false;
            angular.forEach(data, function (value, key) {
                var keepDefault = ['file', 'design', 'config', 'product', 'upload', 'used_font', 'option', 'preview', 'template_thumb'];
                if( typeof value != 'object' || _.includes(keepDefault, key) || key.indexOf("frame") > -1 ){
                    formData.append(key, value);
                }else{
                    var keyName;
                    for (var k in value) {
                        if (value.hasOwnProperty(k)) {
                            keyName = [key, '[', k, ']'].join('');
                            formData.append(keyName, value[k]);
                        }
                    }
                }
                if( key == 'file' ) isUploadFile = true;
            });
            var config = {
                transformRequest: angular.identity,
                transformResponse: angular.identity,
                headers: {
                    'Content-Type': undefined
                }
            };
            if( isUploadFile ){
                config.uploadEventHandlers = {
                    progress: function (e) {
                        if (e.lengthComputable) {
                            var progressBar = (e.loaded / e.total) * 100;
                            progressCallback(progressBar);
                        }
                    }
                };
            }
            var url = NBDESIGNCONFIG['ajax_url'];
            if( data.type == 'typography' || data.type == 'get_typo' ) url = appConfig.mediaUrl + '/typo';
            if( data.source == 'media' ) url = appConfig.mediaUrl + '/template';
            $http.post(url, formData, config).then(
                function(response) {
                    callback(response.data);
                },
                function(response) {
                    console.log(response);
                }
            );           
        }
    }
});
nbdApp.filter('html_trusted', ['$sce', function($sce){
    return function(text) {
        var div = document.createElement('div');
        text += '';
        div.innerHTML = text;
        return $sce.trustAsHtml(div.textContent);
    };            
}]);
nbdApp.filter('range', [ function(){
    return function (input, total) {
        total = parseInt(total);
        for (var i = 0; i < total; i++) {
            input.push(i);
        }
        return input;
    };          
}]);
if( NBDESIGNCONFIG['nbdesigner_enable_facebook_photo'] == 'yes' && NBDESIGNCONFIG['fbID'] != ''){
    window.fbAsyncInit = function() {
        FB.init({
            appId      : NBDESIGNCONFIG['fbID'],
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
    var nbdOnFBLogin = function(){
        FB.getLoginStatus(function(response) {
            if (response.status === "connected") {
                var uid = response.authResponse.userID;
                var accessToken = response.authResponse.accessToken;
                var scope = angular.element(document.getElementById("designer-controller")).scope();
                scope.getPersonalPhoto('facebook', [uid, accessToken]);
                scope.updateApp();            
            }
        });
    };
};
function requestFullScreen(element) {
    var requestMethod = element.requestFullScreen || element.webkitRequestFullScreen || element.mozRequestFullScreen || element.msRequestFullScreen;
    if (requestMethod) { 
        requestMethod.call(element);
    } else if (typeof window.ActiveXObject !== "undefined") {
        var wscript = new ActiveXObject("WScript.Shell");
        if (wscript !== null) {
            wscript.SendKeys("{Esc}");
        }
    }
};
function exitFullscreen(){
    var a = document.webkitExitFullscreen || document.mozCancelFullScreen || document.msExitFullscreen || document.exitFullscreen;
    a && a.call(document);
};
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
};
function getCookie(cname) {
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
window.addEventListener("message", receiveMessage, false);
function receiveMessage(event){
    if( event.origin == window.location.origin ){
        var scope = angular.element(document.getElementById("designer-controller")).scope();
        if( event.data == 'change_nbo_options' ){
            //event.source.postMessage("received", event.origin);
            scope.changePrintingOptions();
            //nbdPostMessage();
        };
        if( event.data == 'change_nbd_qty' && NBDESIGNCONFIG['ui_mode'] == 1 ){
            scope.resource.config.qty = nbd_window.NBDESIGNERPRODUCT.qty;
            scope.updateQtys();
        }
    };
};
function nbdPostMessage(){
    nbd_window.postMessage("received", window.location.origin);
};
/* Change variation */
nbd_window.jQuery('input[name="variation_id"]').on('change', function(){
    if( NBDESIGNCONFIG['ui_mode'] == 1 || (NBDESIGNCONFIG['ui_mode'] == 3 && angular.isUndefined(window.nbOption)) ){
        if(appConfig.ready){
            var variation_id = nbd_window.jQuery('input[name="variation_id"]').val();
            if(NBDESIGNCONFIG['variation_id'] == variation_id) return;  
            var scope = angular.element(document.getElementById("designer-controller")).scope();
            if(variation_id != ''){
                nbd_window.NBDESIGNERPRODUCT.nbdesigner_unready();
                if(NBDESIGNCONFIG['nbdesigner_hide_button_cart_in_detail_page'] == 'yes'){
                    nbd_window.jQuery('button[type="submit"].single_add_to_cart_button').hide();
                }
            }
            NBDESIGNCONFIG['variation_id'] = variation_id;
            if(variation_id != ''){
                scope.changeVariation();
            }
        }
    }
});
jQuery(document).on( 'change_nbo_options_without_od_option', function(){
    if(appConfig.ready){
        if( NBDESIGNCONFIG['ui_mode'] == 3 || NBDESIGNCONFIG['ui_mode'] == 2 ){
            if( nbd_window.jQuery('input[name="variation_id"]').length ){
                setTimeout(function(){
                    var variation_id = nbd_window.jQuery('input[name="variation_id"]').val();
                    if(NBDESIGNCONFIG['variation_id'] == variation_id) return;  
                    NBDESIGNCONFIG['variation_id'] = variation_id;
                    if(variation_id > 0){
                        var scope = angular.element(document.getElementById("designer-controller")).scope();
                        scope.changeVariation();
                    }
                });
            }
        }
    }
});
jQuery(document).on( 'change_nbo_options_with_od_option', function(){
    if(appConfig.ready){
        var scope = angular.element(document.getElementById("designer-controller")).scope();
        if( NBDESIGNCONFIG['ui_mode'] == 3 || (NBDESIGNCONFIG['ui_mode'] == 2 && scope.settings.nbdesigner_display_product_option == '2' && !scope.settings.is_mobile ) ){
            if( nbd_window.jQuery('input[name="variation_id"]').length ){
                setTimeout(function(){
                    var variation_id = nbd_window.jQuery('input[name="variation_id"]').val();
                    NBDESIGNCONFIG['variation_id'] = variation_id;
                    scope.printingOptionsAvailable = ( variation_id > 0 ) ? true : false;
                });
            } else {
                scope.printingOptionsAvailable = true;
            }
            scope.changePrintingOptions();
        }
    }
});
jQuery(document).on( 'change_nbo_options', function(){
    if(appConfig.ready){
        var scope = angular.element(document.getElementById("designer-controller")).scope();
        if( jQuery('input[name="variation_id"]').length ){
            setTimeout(function(){
                scope.printingOptionsAvailable = jQuery('input[name="variation_id"]').val() > 0 ? true : false;
                NBDESIGNCONFIG.variation_id = jQuery('input[name="variation_id"]').val();
            });
        }else{
            scope.printingOptionsAvailable = true;
        }
        scope.updateApp();
    }
});
jQuery(document).on( 'invalid_nbo_options', function(){
    if(appConfig.ready){
        var scope = angular.element(document.getElementById("designer-controller")).scope();
        scope.printingOptionsAvailable = false;
        scope.updateApp();
    }
});
jQuery(document).on( 'change_nbo_extra_od_options', function(){
    if(appConfig.ready){
        var scope = angular.element(document.getElementById("designer-controller")).scope();
        scope.changeExtraOdOptions();
        scope.updateApp();
    }
});
if( NBDESIGNCONFIG['ui_mode'] == 1 ) {
    nbd_window.jQuery(nbd_window.document).on( 'change_nbo_extra_od_options', function(){
        if(appConfig.ready){
            var scope = angular.element(document.getElementById("designer-controller")).scope();
            scope.changeExtraOdOptions();
            scope.updateApp();
        }
    });
};
if( NBDESIGNCONFIG['ui_mode'] == 3 ){
    function bootstrapNbdApp(){
        var nbdAppEl = document.getElementById('nbd-vista-app');
        angular.element(function() {
            angular.bootstrap(nbdAppEl, ['nbdApp']);
        });
    };
    if( window.nbOption  ){
        jQuery(document).on( 'initialed_nbo_options', function(){
            bootstrapNbdApp();
        });
    }else{
        bootstrapNbdApp();
    }
};