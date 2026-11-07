var appConfig = {
    ready: false
};
var nbdpbApp = angular.module('nbdpbApp', []);
nbdpbApp.controller('nbpbCtrl', ['$scope', 'FabricWindow', 'NBDDataFactory', '$window', '$timeout',
    function ($scope, FabricWindow, NBDDataFactory, $window, $timeout) {
        $scope.isStartDesign = false;
        $scope.onloadTemplate = false;
        $scope.init = function () {
            $scope.initSettings();
        };
        $scope.defaultStageStates = {
            showAdminTool: false
        };
        $scope.initSettings = function () {
            $scope.settings = {};
            $scope.stages = [];
            $scope.side = [];
            $scope.resource = {
                views: [],
                components: [],
                values: {},
                showValue: false,
                currentComponent: 0,
                currentView: 0,
                jsonDesign: {},
                config: {},
                uploaded: [],
                currentColor: '#ff0000',
                colorOptions: {
                    preferredFormat: 'hex',
                    flat: true,
                    showButtons: true,
                    showInput: true,
                    containerClassName: 'nbd-sp',
                    clickoutFiresChange: false,
                    chooseText: NBPBCONFIG.i18n.choose,
                    cancelText: NBPBCONFIG.i18n.cancel
                }
            };
            var uploaded = localStorage.getItem('nbpb_uploaded');
            if( uploaded ){
                $scope.resource.uploaded = JSON.parse( uploaded );
            }
            angular.copy(NBPBCONFIG, $scope.settings);
            $scope.currentStage = 0;
            $scope.includeExport = ['itemId', 'isLogo', 'evented', 'a_index', 'sa_index'];
            $scope.processProductSettings();
        };
        $scope.processProductSettings = function(){
            $scope.initValues( true );
            _.each($scope.resource.views, function(side, index){
                $scope.stages[index] = {
                    config: {
                    },
                    states: {},
                    canvas: {}
                };
                var _state = $scope.stages[index].states;
                angular.copy($scope.defaultStageStates, _state);
            });
        };
        $scope.initValues = function( init, pro ){
            if( init ){
                angular.copy( nbOption.options.views, $scope.resource.views );
                $scope.resource.components = [];
                _.each( nbOption.options.fields, function(field, index){
                    if( field.nbpb_type == 'nbpb_com' || field.nbpb_type == 'nbpb_text' || field.nbpb_type == 'nbpb_image' ){
                        var _field = {};
                        angular.copy( field, _field );
                        _field.currentSubAtrribute = 0;
                        _field.currentConfig = 0;
                        if( field.nbpb_type == 'nbpb_text' ){
                            _field.currentContent = '';
                            if( _field.general.nbpb_text_configs.allow_all_color == 'n' ){
                                if( _field.general.nbpb_text_configs.colors.length > 0 ){
                                    _field.currentColor = _field.general.nbpb_text_configs.colors[0].color;
                                }else{
                                    _field.currentColor = '#000000';
                                }
                            }else{
                                _field.currentColor = '#000000';
                            }
                            _field.currentFontId = '';
                            if( _field.general.nbpb_text_configs.allow_font_family == 'y' ){
                                if( _field.general.nbpb_text_configs.allow_all_font == 'n' ){
                                    if( angular.isUndefined(_field.general.nbpb_text_configs.custom_fonts) ) _field.general.nbpb_text_configs.custom_fonts = [];
                                    if( angular.isUndefined(_field.general.nbpb_text_configs.google_fonts) ) _field.general.nbpb_text_configs.google_fonts = [];
                                    if( _field.general.nbpb_text_configs.custom_fonts.length > 0 ){
                                        var i = _field.general.nbpb_text_configs.custom_fonts.length - 1;
                                        while (i >= 0) {
                                            if( $scope.settings.custom_fonts[_field.general.nbpb_text_configs.custom_fonts[i]] ){
                                                _field.currentFontId = 'c' + _field.general.nbpb_text_configs.custom_fonts[i];
                                            }
                                            i--;
                                        }
                                        if( _field.currentFontId == '' && _field.general.nbpb_text_configs.google_fonts.length > 0 ){
                                            var i = _field.general.nbpb_text_configs.google_fonts.length - 1;
                                            while (i >= 0) {
                                                if( $scope.settings.google_fonts[_field.general.nbpb_text_configs.google_fonts[i]] ){
                                                    _field.currentFontId = 'g' + _field.general.nbpb_text_configs.google_fonts[i];
                                                }
                                                i--;
                                            }
                                        }
                                    }else if( _field.general.nbpb_text_configs.google_fonts.length > 0 ){
                                        var i = _field.general.nbpb_text_configs.google_fonts.length - 1;
                                        while (i >= 0) {
                                            if( $scope.settings.google_fonts[_field.general.nbpb_text_configs.google_fonts[i]] ){
                                                _field.currentFontId = 'g' + _field.general.nbpb_text_configs.google_fonts[i];
                                            }
                                            i--;
                                        }
                                    }
                                }else{
                                    if( NBPBCONFIG.fonts.length > 0 ){
                                        var prefix = NBPBCONFIG.fonts[0].type == 'google' ? 'g' : 'c';
                                        _field.currentFontId = prefix + '0';
                                    }else{
                                        _field.currentFontId = '';
                                    }
                                }
                            }
                        }
                        $scope.resource.components.push(_field);
                    }
                });
            }
            $scope.resource.values = {};
            angular.copy(nbOption.nbd_fields, $scope.resource.values);
            _.each( $scope.resource.values, function(field, index){
                var component = $scope.getComponentById( index );
                if(component) component.enable = field.enable;
            });
            if( !pro ) $scope.resource.showValue = false;
            if( !init ){
                _.each($scope.stages, function(stage, index){
                    stage.canvas.forEachObject(function(obj, index) {
                        var itemId = obj.get('itemId');
                        if( itemId ){
                            var component = $scope.getComponentById( itemId );
                            if( !component.enable ){
                                obj.set('visible', false);
                            }
                        }
                    });
                    stage.canvas.renderAll();
                });
            }
        };
        $scope.initStageSetting = function( id ){
            $scope.setStageDimension(id);
            $scope.renderStage(id);
            $scope.updateApp();
        };
        $scope.setStageDimension = function(id){
            id = angular.isDefined(id) ? id : $scope.currentStage;
            var _stage = $scope.stages[id];
            $timeout(function () {
                var viewPort = $scope.calcViewport();
                var base_width = (angular.isDefined($scope.resource.views[id].base_width) && $scope.resource.views[id].base_width != '' ) ? parseFloat($scope.resource.views[id].base_width) : viewPort.width,
                base_height = (angular.isDefined($scope.resource.views[id].base_height) && $scope.resource.views[id].base_height != '' ) ? parseFloat($scope.resource.views[id].base_height) : viewPort.height;
                var designViewPort = $scope.fitRectangle(viewPort.width, viewPort.height, base_width, base_height, true);
                _stage['canvas'].setDimensions({'width' : designViewPort.width, 'height' : designViewPort.height});
                _stage.config.width = designViewPort.width;
                _stage.config.height = designViewPort.height;
                _stage.config.top = designViewPort.top;
                _stage.config.left = designViewPort.left;
                if( angular.isUndefined($scope.resource.config.lastViewport) ){
                    $scope.resource.config.lastViewport = viewPort;
                }
            });
        };
        $scope.showAttribute = function( index ){
            $scope.resource.showValue = true;
            if( angular.isDefined( index ) ){
                $scope.resource.currentComponent = index;
                var field = $scope.resource.components[index],
                viewLen = nbOption.options.views.length;
                $scope.resource.currentComponentObj = field;
                if( field.nbpb_type == 'nbpb_com' ){
                    field.current_pb_configs = [];
                    _.each(field.general.pb_config, function(attr, a_index){
                        _.each(attr, function(s_attr, sa_index){
                            var config = [];
                            if( s_attr.views.length > nbOption.options.views.length ){
                                s_attr.views.splice(viewLen, s_attr.views.length - viewLen );
                            }
                            angular.copy(s_attr.views, config);
                            var attribute = field.general.attributes.options[a_index];
                            if(  angular.isDefined(attribute) ){
                                if( angular.isDefined(attribute.enable_subattr) && attribute.enable_subattr == 'on' && angular.isDefined(attribute.sub_attributes) && attribute.sub_attributes.length > 0 ){
                                    config.sattr_name = attribute.sub_attributes[sa_index].name;
                                    config.attr_name = attribute.name;
                                    config.icon_bg = attribute.sub_attributes[sa_index].image_url;
                                    config.a_index = a_index;
                                    config.sa_index = sa_index;
                                    config.level = 2;
                                    config.bg_type = attribute.sub_attributes[sa_index].preview_type;
                                    config.icon_color = attribute.sub_attributes[sa_index].color;
                                }else{
                                    config.icon_bg = attribute.image_url;
                                    config.sattr_name = attribute.name;
                                    config.attr_name = '';
                                    config.a_index = a_index;
                                    config.sa_index = 0;
                                    config.level = 1;
                                    if( attribute.preview_type == 'c' ){
                                        config.bg_type = 'c';
                                        config.icon_color = attribute.color;
                                    }else{
                                        config.bg_type = 'i';
                                    }
                                }
                                field.current_pb_configs.push( config );
                            }
                        });
                    });
                }
                var item = $scope.getLayerById( field.id );
                if( NBPBCONFIG.is_creating_task == 1 ){
                    var _canvas = $scope.stages[$scope.currentStage].canvas;
                    if(item){
                        _canvas.setActiveObject(item);
                        _canvas.renderAll();
                    }
                }
                if( item ){
                    if( field.nbpb_type == 'nbpb_com' ){
                        $scope.resource.components[index].currentConfig = $scope.getCurrentConfig( $scope.resource.components[index].id, item.a_index, item.sa_index );
                    }else if( field.nbpb_type == 'nbpb_text' ){
                        var font = $scope.getFontByAlias(item.fontFamily);
                        if( font ){
                            $scope.resource.components[index].currentFontId = (font.type == 'google' ? 'g' : 'c') + font.id;
                        }
                        $scope.resource.components[index].currentFontFamily = item.fontFamily;
                        $scope.resource.components[index].currentColor = item.fill;
                        $scope.resource.components[index].currentContent = item.text;
                    }
                }
            }
        };
        $scope.saveLayer = function () {
            $scope.resource.showValue = false;
            $scope.deactiveAllLayer();
        };
        $scope.selectAttribute = function (index) {
            var currentComponent = $scope.resource.components[$scope.resource.currentComponent];
            currentComponent.currentConfig = index;
            var statusImages = [], firstView = true;
            function isLoadedAllImages(){
                var check = true;
                _.each(statusImages, function (status, index) {
                    var _status = angular.isDefined(status) ? status : true;
                    check = check && _status;
                });
                return check;
            };
            _.each(currentComponent.current_pb_configs[index], function (view, viewIndex) {
                if( view.display == 'on' ){
                    statusImages[viewIndex] = false;
                }
            });
            var currentStage = -1;
            _.each(currentComponent.current_pb_configs[index], function (view, viewIndex) {
                var _stage = $scope.stages[viewIndex], _canvas = _stage.canvas,
                _item = $scope.getLayerById(currentComponent.id, viewIndex);
                if( view.display == 'on' ){
                    if( currentStage == -1 ){
                        currentStage = viewIndex;
                    }
                    if( firstView ){
                        jQuery('.nbpb-stage-loading').addClass('nbdpb-show');
                        firstView = false;
                    }
                    if( _item ){
                        var element = _item.getElement();
                        element.setAttribute("src", view.image_url);
                    }
                    fabric.Image.fromURL(view.image_url, function (obj) {
                        if( _item ){
                            _item.set({
                                visible: true,
                                dirty: true,
                                width: obj.width,
                                height: obj.height,
                                scaleX: _item.scaleX * _item.width / obj.width,
                                scaleY: _item.scaleY * _item.height / obj.height,
                                a_index: currentComponent.current_pb_configs[index].a_index, 
                                sa_index: currentComponent.current_pb_configs[index].sa_index
                            });
                            _item.setCoords();
                        }else{
                            var max_width = _canvas.width,
                                max_height = _canvas.height,
                                new_width = max_width;
                            new_width = max_width;
                            var width_ratio = new_width / obj.width,
                                new_height = obj.height * width_ratio;
                            if (new_height > max_height) {
                                new_height = max_height;
                                var height_ratio = new_height / obj.height;
                                new_width = obj.width * height_ratio;
                            }
                            obj.set({
                                fill: '#ff0000',
                                scaleX: new_width / obj.width,
                                scaleY: new_height / obj.height,
                                itemId: currentComponent.id,
                                a_index: currentComponent.current_pb_configs[index].a_index,
                                sa_index: currentComponent.current_pb_configs[index].sa_index
                            });
                            _canvas.add(obj);
                            _canvas.viewportCenterObject(obj);
                            if( NBPBCONFIG.is_creating_task == 1 ){
                                _canvas.setActiveObject(obj);
                            }
                        }
                        statusImages[viewIndex] = true;
                        if( isLoadedAllImages() ) jQuery('.nbpb-stage-loading').removeClass('nbdpb-show');
                        _canvas.renderAll();
                    }, {crossOrigin: 'anonymous'});
                }else{
                    if( _item ){
                        _item.set({
                            visible: false
                        });
                        _canvas.renderAll();
                    }
                }
            });
            if( currentComponent.current_pb_configs[index][$scope.currentStage].display != 'on' && currentStage != -1 ){
                appConfig.slider.activeItemByIndex(currentStage);
            }
            $scope.resource.values[currentComponent.id].value = '' + currentComponent.current_pb_configs[index].a_index;
            $scope.resource.values[currentComponent.id].sub_value = '' + currentComponent.current_pb_configs[index].sa_index;
            jQuery(document).triggerHandler( 'update_nbo_options_from_builder', { nbd_fields: $scope.resource.values, pro: true } );
        };
        $scope.selectColor = function(color){
            if( angular.isDefined(color) && $scope.resource.components[$scope.resource.currentComponent].currentColor != color ){
                $scope.resource.components[$scope.resource.currentComponent].currentColor = color;
                $scope.updateText();
            }
        };
        $scope.updateText = function(){
            var  currentComponent = $scope.resource.components[$scope.resource.currentComponent];
                currentComponent.currentFontFamily = 'Arial';
            var font;
            if( currentComponent.currentFontId != '' ){
                var type = currentComponent.currentFontId.slice(0, 1),
                    id = currentComponent.currentFontId.slice(1);
                if( type == 'c' ){
                    font = $scope.getFontByIdAndType(id, 'ttf');
                }else{
                    font = $scope.getFontByIdAndType(id, 'google');
                }
                if( font ){
                    $scope.insertFontScript(font);
                    currentComponent.currentFontFamily = font.alias;
                }
            }
            var font = new FontFaceObserver(currentComponent.currentFontFamily);
            if( currentComponent.general.text_option.max != '' ){
                var maxlen = parseInt(currentComponent.general.text_option.max);
                if( currentComponent.currentContent.length > maxlen ){
                    currentComponent.currentContent = currentComponent.currentContent.slice(0, maxlen - 1);
                }
            }
            font.load( currentComponent.currentContent ).then(function () {
                fabric.util.clearFabricFontCache();
                $scope.addText();
            }, function () {
                $scope.addText();
            });
            $scope.resource.values[currentComponent.id].value = currentComponent.currentContent;
            jQuery(document).triggerHandler( 'update_nbo_options_from_builder', { nbd_fields: $scope.resource.values, pro: true } );
        };
        $scope.insertFontScript = function( font ){
            if( !jQuery('#nbpb' + font.id ).length ){
                if( font.type == 'google' ){
                    jQuery('head').append('<link id="nbpb' + font.id + '" href="https://fonts.googleapis.com/css?family='+ font.alias.replace(/\s/gi, '+') +':400,400i,700,700i" rel="stylesheet" type="text/css">');
                }else{
                    var css = "<style type='text/css' id='nbpb" + font.id  + "' >";
                    _.each(font.file, function (file, index) {
                        var font_url = file;
                        if(! (file.indexOf("http") > -1)) font_url = NBPBCONFIG['font_url'] + file;
                        css += "@font-face {font-family: '" + font.alias + "';";
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
            }
        };
        $scope.getFontByIdAndType = function(id, type){
            var _font;
            _.each(NBPBCONFIG.fonts, function (font, index) {
                if( font['id'] == id && font['type'].toLowerCase() == type ){
                    _font = font;
                }
            });
            return _font;
        };
        $scope.getFontByAlias = function( alias ){
            var _font;
            _.each(NBPBCONFIG.fonts, function (font, index) {
                if( font.alias == alias ){
                    _font = font;
                }
            });
            return _font;
        };
        $scope.addText = function(){
            var  currentComponent = $scope.resource.components[$scope.resource.currentComponent],
                views = currentComponent.general.nbpb_text_configs.views;
            _.each(views, function (view, viewIndex) {
                var item = $scope.getLayerById( currentComponent.id, viewIndex );
                if( view.display == 'on' ){
                    var _canvas = $scope.stages[viewIndex].canvas;
                    if( item ){
                        item.set({
                            text: currentComponent.currentContent,
                            visible: true,
                            fontFamily: currentComponent.general.nbpb_text_configs.allow_font_family == 'y' ? currentComponent.currentFontFamily : item.fontFamily,
                            fill: currentComponent.general.nbpb_text_configs.allow_change_color == 'y' ? currentComponent.currentColor : item.fill
                        });
                    }else{
                        _canvas.add(new FabricWindow['Textbox'](currentComponent.currentContent, {
                            itemId: currentComponent.id,
                            fontFamily: currentComponent.currentFontFamily,
                            fontSize: 19,
                            fill: currentComponent.currentColor,
                            textAlign: 'center'
                        }));
                        _canvas.viewportCenterObject(_canvas.item(_canvas.getObjects().length - 1));
                    }
                }else{
                    if( item ){
                        item.set({
                            visible: false
                        });
                    }
                }
                _canvas.renderAll();
            });
        };
        $scope.getLayerById = function(itemId, stage){
            var _canvas = null;
            if ( angular.isDefined(stage)) {
                _canvas = $scope.stages[stage].canvas;
            }else {
                _canvas = $scope.stages[$scope.currentStage].canvas;
            }
            var _object = null;
            _canvas.forEachObject(function(obj, index) {
                if(obj.get('itemId') == itemId) _object = obj;
            });
            return _object;
        };
        $scope.getLayerIndex = function(itemId, stage){
            var _canvas;
            if ( angular.isDefined(stage)) {
                _canvas = $scope.stages[stage].canvas;
            }else {
                _canvas = $scope.stages[$scope.currentStage].canvas;
            }
            var _index;
            _canvas.forEachObject(function(obj, index) {
                if(obj.get('itemId') == itemId) _index = index;
            });
            return _index;
        };
        $scope.deactiveAllLayer = function(stage_id){
            stage_id = stage_id ? stage_id :  $scope.currentStage;
            $scope.stages[stage_id]['canvas'].discardActiveObject();
            $scope.renderStage();
            $scope.updateApp();
        };
        $scope.clearAllStages = function(){
            _.each($scope.stages, function(stage, index){
                stage.canvas.clear();
                stage.canvas.renderAll();
            });
        };
        $scope.renderStage = function( stage_id ){
            stage_id = stage_id ? stage_id :  $scope.currentStage;
            $scope.stages[stage_id]['canvas'].calcOffset();
            $scope.stages[stage_id]['canvas'].requestRenderAll();
        };
        $scope.updateApp = function(){
            if ($scope.$root.$$phase !== "$apply" && $scope.$root.$$phase !== "$digest") $scope.$apply();
        };
        $scope.setStackPosition = function(command, _item){
            var item = _item ? _item : $scope.stages[$scope.currentStage]['canvas'].getActiveObject();
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
            $scope.renderStage($scope.currentStage);
        };
        $scope.calcViewport = function(){
            var   _width = jQuery('.design-stages').width(),
                _height = jQuery('.design-stages').height();
            return {width: _width, height: _height};
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
        $scope.saveData = function () {
            $scope.toggleAppLoading();
            jQuery('.nbdpb-custom-design').empty().hide();
            $scope.saveDesign();
            $scope.resource.config.views = $scope.resource.views;
            $scope.resource.config.viewport = $scope.calcViewport();
            var dataObj = {};
            dataObj.design = new Blob([JSON.stringify($scope.resource.jsonDesign)], {type: "application/json"});
            _.each($scope.stages, function(stage, index){
                var key = 'frame_' + index;
                dataObj[key] = $scope.makeblob(stage.design);
            });
            ['nbo_cart_item_key', 'is_creating_task', 'oid'].forEach(function(key){
                dataObj[key] = NBPBCONFIG[key];
            });
            dataObj.config = new Blob([JSON.stringify($scope.resource.config)], {type: "application/json"});
            var action = 'nbd_save_product_builder_design';
            NBDDataFactory.get(action, dataObj, function(data){
                data = JSON.parse(data);
                if(data.flag == 'success'){
                    if ($scope.settings.is_creating_task == 1) {
                        if( $scope.settings.redirect_url != '' ) window.location = $scope.settings.redirect_url;
                    }else{
                        $scope.toggleAppLoading();
                        jQuery('.nbdpb-custom-design').show();
                        _.each(data.image, function (image) {
                            image += '?t=' + Math.random();
                            var item = '<div class="item">' +
                                    '<img src="' + image + '" alt="Custom Design"/>' +
                                '</div>';
                            jQuery('.nbdpb-custom-design').append(item);
                        });
                        jQuery('.variations_form, form.cart').append('<input type="hidden" name="nbdpb-folder" value="'+data.folder+'" />');
                        jQuery(document).triggerHandler( 'update_product_image_from_builder', {
                            image_link: data.image[0],
                            image_srcset: data.image[0],
                            full_src: data.image[0],
                            full_src_w: $scope.stages[0].config.width,
                            full_src_h: $scope.stages[0].config.height,
                            image_sizes: [$scope.stages[0].config.width, $scope.stages[0].config.height],
                            image_title: '',
                            image_alt: '',
                            image_caption: ''
                        } );
                        jQuery('.close-popup').triggerHandler('click');
                    }
                }else{
                    $scope.toggleAppLoading();
                    alert(NBPBCONFIG.i18n.can_not_save_design);
                }
            });
        };
        $scope.saveDesign = function () {
            _.each($scope.stages, function (stage, index) {
                $scope.deactiveAllLayer(index);
                var _canvas = stage.canvas;
                $scope.renderStage(index);
                $scope.resource.jsonDesign[index] = _canvas.toJSON($scope.includeExport);
                stage.design = _canvas.toDataURL();
            });
        };
        $scope.onObjectAdded = function (id, options) {
            /* Reindex layers */
            if( NBPBCONFIG.is_creating_task != 1 && angular.isUndefined($scope.settings.pre_builder.design) ){
                _.each($scope.stages, function(stage, sIndex){
                    var layerIndex = 0,
                    _canvas = stage.canvas;
                    _.each($scope.resource.components, function(component, cIndex){
                        var _obj, itemId = component.id;
                        _canvas.forEachObject(function(obj, index) {
                            if(obj.get('itemId') == itemId){
                                _obj = obj;
                            }
                        });
                        if( _obj ){
                            _obj.moveTo( layerIndex);
                            layerIndex++;
                        }
                    });
                });
            }
        };
        $scope.onMouseOver = function (id, options) {
            var _stage = $scope.stages[$scope.currentStage],
                _canvas = _stage['canvas'],
                item = options.target;
            if (item) {
                item.set('opacity', '0.9');
            }
            _canvas.renderAll();
        };
        $scope.onMouseOut = function (id, options) {
            var _stage = $scope.stages[$scope.currentStage],
                _canvas = _stage['canvas'],
                item = options.target, itemId = '', proAttr = null;
            if (item) {
                item.set('opacity', '1');
            }
            _canvas.renderAll();
        };
        $scope.onMouseDown = function (id, options) {
            var _stage = $scope.stages[$scope.currentStage],
                _canvas = _stage['canvas'],
                item = options.target;
            if (item) {
                if ( angular.isDefined(item.get('itemId')) ){
                    var itemId = item.get('itemId');
                    _.each($scope.resource.components, function (component, index) {
                        if( component.id == itemId ){
                            $scope.showAttribute(index);
                            $scope.updateApp();
                        }
                    });
                }
            }else {
                $scope.saveLayer();
            }
        };
        $scope.onSelectionCreated = function (id, options) {
            if (options.target) {
                var item = options.target,
                _stage = $scope.stages[$scope.currentStage];
                _stage.states.scaleX = item.get('scaleX');
                _stage.states.scaleY = item.get('scaleY');
                _stage.states.angle = item.get('angle');
                if( item.type == 'textbox' ){
                    var font = $scope.getFontByAlias(item.fontFamily);
                    if( font ){
                        $scope.resource.components[$scope.resource.currentComponent].currentFontId = (font.type == 'google' ? 'g' : 'c') + font.id;
                    }
                    $scope.resource.components[$scope.resource.currentComponent].currentFontFamily = item.fontFamily;
                    $scope.resource.components[$scope.resource.currentComponent].currentColor = item.fill;
                    $scope.resource.components[$scope.resource.currentComponent].currentContent = item.text;
                }
                _stage.states.showAdminTool = true;
                $scope.updateApp();
            }
        };
        $scope.onSelectionCleared = function (id, options) {
            var _stage = $scope.stages[$scope.currentStage];
            _stage.states.showAdminTool = false;
            $scope.updateApp();
        };
        $scope.updateLayerAttribute = function(type, value){
            if( !appConfig.ready ) return;
            var item = $scope.stages[$scope.currentStage]['canvas'].getActiveObject();
            if(!item) return;
            var ob = {};ob[type] = value;
            $scope.stages[$scope.currentStage].states[type] = value;
            item.set(ob);
            $scope.renderStage();
        };
        var _window = angular.element($window);
        _window.bind('resize', function(){
            $scope.reCalcViewPort();
        });
        $scope.reCalcViewPort = function () {
            var _stages = $scope.stages;
            jQuery('.nbdpb-carousel').nbdpbCarousel();
            _.each(_stages, function (stage, index) {
                $scope.setStageDimension(index);
            });
            $scope.resizeStages($scope.resource.config.lastViewport);
        };
        $scope.resizeStages = function(viewport){
            _.each($scope.stages, function(stage, index){
                var currentViewport = $scope.calcViewport();
                var newFitRec = $scope.fitRectangle(viewport.width, viewport.height, stage.config.width, stage.config.height, true);
                var oldFitRec = $scope.fitRectangle(currentViewport.width, currentViewport.height, stage.config.width, stage.config.height, true);
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
                        obj.scaleX = tempScaleX;
                        obj.scaleY = tempScaleY;
                        obj.left = tempLeft;
                        obj.top = tempTop;
                        obj.setCoords();
                    });
                    stage.canvas.calcOffset();
                    $scope.renderStage(index);
                }
                if( index == $scope.stages.length - 1 ){
                    $scope.resource.config.lastViewport = currentViewport;
                }
            });
        };
        $scope.$on('canvas:created', function(event, id, last){
            /* init canvas parameters */
            $scope.initStageSetting(id);
            var _canvas = $scope.stages[id].canvas;
            /* Listen canvas events */
            _canvas.on('mouse:over', function (options) {
                $scope.onMouseOver(id, options);
            });
            _canvas.on('mouse:out', function (options) {
                $scope.onMouseOut(id, options);
            });
            _canvas.on('mouse:down', function (options) {
                $scope.onMouseDown(id, options);
            });
            _canvas.on('object:added', function (options) {
                $scope.onObjectAdded(id, options);
            });
            _canvas.on('selection:created', function(options){
                $scope.onSelectionCreated(id, options);
            });
            _canvas.on('selection:cleared', function (options) {
                $scope.onSelectionCleared(id, options);
            });
            /* Load template after render canvas */
            if (last == '1') {
                appConfig.ready = true;
                $scope.loadPreBuilder();
            }
        });
        $scope.$on('component:mouseover', function(event, id){
            if(!appConfig.ready) return;
            var _canvas = $scope.stages[$scope.currentStage].canvas;
            var item = $scope.getLayerById( id );
            if(item){
                item.set('opacity', '0.9');
                _canvas.renderAll();
            }
        });
        $scope.$on('component:mouseout', function(event, id){
            if(!appConfig.ready) return;
            var _canvas = $scope.stages[$scope.currentStage].canvas;
            var item = $scope.getLayerById( id );
            if(item){
                item.set('opacity', '1');
                _canvas.renderAll();
            }
        });
        $scope.loadPreBuilder = function () {
            $timeout(function(){
                if( angular.isDefined($scope.settings.pre_builder.design)){
                    $scope.insertTemplate( $scope.settings.pre_builder.design, $scope.settings.pre_builder.config );
                }
            });
        };
        $scope.insertTemplate = function(design, config){
            $scope.onloadTemplate = true;
            var stageIndex = 0,
                viewport = config.viewport;
            $scope.toggleAppLoading();
            function loadStage(stageIndex){
                var stage = $scope.stages[stageIndex],
                    _canvas = stage['canvas'],
                    layerIndex = 0;
                _canvas.clear();
                var objects = [];
                if( angular.isDefined(design[stageIndex]) ) objects = design[stageIndex].objects;
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
                                    $scope.deactiveAllLayer();
                                    $scope.renderStage(index);
                                    $timeout(function () {
                                        $scope.deactiveAllLayer();
                                        $scope.renderStage(index);
                                        if (index == $scope.stages.length -1) {
                                            $scope.resizeStages(viewport);
                                            $scope.toggleAppLoading();
                                            $scope.onloadTemplate = false;
                                        }
                                    });
                                });
                            }
                        }
                    }
                    if( objects.length > 0 ){
                        var item = objects[layerIndex],
                            type = item.type,
                            component = $scope.getComponentById(item.itemId);
                        if( component && component.enable){
                            if( type == 'image' ){
                                fabric.Image.fromObject(item, function(_image){
                                    if( angular.isDefined( _image.isLogo ) && _image.isLogo == 1 ){
                                        if( NBPBCONFIG.nbo_cart_item_key == '' && NBPBCONFIG.is_creating_task == 0 ) _image.set({visible: false});
                                        component.general.nbpb_image_configs.views[stageIndex].width = _image.width * _image.scaleX;
                                        component.general.nbpb_image_configs.views[stageIndex].height = _image.height * _image.scaleY;
                                        component.general.nbpb_image_configs.views[stageIndex].top = _image.top;
                                        component.general.nbpb_image_configs.views[stageIndex].left = _image.left;
                                    }
                                    _canvas.add(_image);
                                    continueLoadLayer();
                                });
                            }else if(type == 'textbox'){
                                function addText(item){
                                    var klass = fabric.util.getKlass(type);
                                    klass.fromObject(item, function(item){
                                        if( NBPBCONFIG.nbo_cart_item_key == '' && NBPBCONFIG.is_creating_task == 0 ) item.set({visible: false, text: ''});
                                        _canvas.add(item);
                                        continueLoadLayer();
                                    });
                                }
                                var font = $scope.getFontByAlias(item.fontFamily);
                                if( font ){
                                    $scope.insertFontScript( font );
                                    var font = new FontFaceObserver(item.fontFamily);
                                    font.load( item.text ).then(function () {
                                        fabric.util.clearFabricFontCache();
                                        addText(item);
                                    }, function () {
                                        addText(item);
                                    });
                                }else{
                                    item.fontFamily = 'Arial';
                                    addText(item);
                                };
                            }
                        }else{
                            continueLoadLayer();
                        }
                    }else{
                        continueLoadLayer();
                    }
                };
                loadLayer(layerIndex);
            };
            loadStage(stageIndex);
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
        $scope.toggleAppLoading = function () {
            var $loading = jQuery('.nbdpb-load-page');
            if ($loading.hasClass('nbdpb-show')) {
                $loading.removeClass('nbdpb-show');
                jQuery('body, html').removeClass('nbdpb-no-overflow');
            }else {
                $loading.addClass('nbdpb-show');
                jQuery('body, html').addClass('nbdpb-no-overflow');
            }
        };
        $scope.uploadImage = function(field_id, files){
            var file = files[0],
            field = $scope.get_field(field_id),
            min_size = parseInt(field.general.upload_option.min_size),
            max_size = parseInt(field.general.upload_option.max_size);
            if( file.type.indexOf("image") === -1 ){
                alert($scope.settings.i18n.only_support_image);
                return;
            }
            if (file.size > max_size * 1024 * 1024 ) {
                alert($scope.settings.i18n.max_file_size + max_size + " MB");
                return;
            }else if(file.size < min_size * 1024 * 1024){
                alert($scope.settings.i18n.min_file_size + min_size + " MB");
                return;
            };
            if( file.type.indexOf("svg") > -1 ){
                var reader = new FileReader();
                reader.onload = function(event){
                    if (event.target.readyState === 2) {
                        var result = reader.result;
                        $scope.addSvgFromString(result);
                    }
                };
                reader.readAsText(file);
            }else{
                NBDDataFactory.get('nbdesigner_customer_upload', {file: file}, function(data){
                    var data = JSON.parse(data);
                    if( data.flag == 1 ){
                        $scope.addImage(data.src);
                        $scope.resource.uploaded.push(data.src);
                        if( $scope.resource.uploaded.length > 10 ){
                            $scope.resource.uploaded.shift();
                        }
                        localStorage.setItem('nbpb_uploaded', JSON.stringify( $scope.resource.uploaded ) );
                    }else{
                        alert(data.mes);
                    }
                });
            }
        };
        $scope.addImage = function( url ){
            var  currentComponent = $scope.resource.components[$scope.resource.currentComponent],
                views = currentComponent.general.nbpb_image_configs.views; 
            var statusImages = [], firstView = true;
            function isLoadedAllImages(){
                var check = true;
                _.each(statusImages, function (status, index) {
                    var _status = angular.isDefined(status) ? status : true;
                    check = check && _status;
                });
                return check;
            };
            _.each(views, function (view, viewIndex) {
                if( view.display == 'on' ){
                    statusImages[viewIndex] = false;
                }
            });
            _.each(views, function (view, viewIndex) {
                var stage = $scope.stages[viewIndex],
                _canvas = stage.canvas,
                _item = $scope.getLayerById(currentComponent.id, viewIndex);
                if( view.display == 'on' ){
                    if( firstView ){
                        jQuery('.nbpb-stage-loading').addClass('nbdpb-show');
                        firstView = false;
                    }
                    fabric.Image.fromURL(url, function(op) {
                        function _addImage( exist ){
                            if( angular.isDefined(view.width) ){
                                //todo resize holder
                                var preViewport = $scope.settings.pre_builder.config.viewport,
                                currentViewport = $scope.calcViewport(),
                                newFitRec = $scope.fitRectangle(preViewport.width, preViewport.height, stage.config.width, stage.config.height, true),
                                oldFitRec = $scope.fitRectangle(currentViewport.width, currentViewport.height, stage.config.width, stage.config.height, true);
                                var factor = oldFitRec.width / newFitRec.width,
                                max_width = view.width * factor,
                                max_height = view.height * factor,
                                left = view.left * factor,
                                top = view.top * factor;
                            }else{
                                var max_width = _canvas.width / 2,
                                max_height = _canvas.height / 2,
                                left = _canvas.width / 2,
                                top = _canvas.height / 2;
                            }
                            var new_width = max_width;
                            if (op.width < max_width) new_width = op.width;
                            var width_ratio = new_width / op.width,
                            new_height = op.height * width_ratio;
                            if (new_height > max_height) {
                                new_height = max_height;
                                var height_ratio = new_height / op.height;
                                new_width = op.width * height_ratio;
                            };
                            if( angular.isDefined(exist) ){
                                var element = _item.getElement();
                                element.setAttribute("src", url);
                                _item.set({
                                    dirty: true,
                                    width: op.width,
                                    height: op.height,
                                    scaleX: new_width / op.width,
                                    scaleY: new_height / op.height,
                                    visible: true
                                });
                                _item.setCoords();
                            }else{
                                op.set({
                                    fill: '#ff0000',
                                    scaleX: new_width / op.width,
                                    scaleY: new_height / op.height,
                                    left: left,
                                    top: top,
                                    itemId: currentComponent.id,
                                    isLogo: 1
                                });
                                _canvas.add(op);
                                if( NBPBCONFIG.is_creating_task == 1 ){
                                    _canvas.setActiveObject(op);
                                }
                            }
                        };
                        if ( _item ) {
                            if( _item.type == 'image' ){
                                _addImage( true );
                            }else{
                                var layerIndex = $scope.getLayerIndex(currentComponent.id, viewIndex);
                                view.width = _item.width * _item.scaleX;
                                view.height = _item.height * _item.scaleY;
                                view.left = _item.left ;
                                view.top = _item.top;
                                _canvas.remove(_item);
                                _addImage( true );
                                op.moveTo(layerIndex);
                            }
                        }else{
                            _addImage();
                        }
                        _canvas.renderAll();
                        statusImages[viewIndex] = true;
                        if( isLoadedAllImages()  ){
                            jQuery('.nbpb-stage-loading').removeClass('nbdpb-show');
                        }
                    }, {crossOrigin: 'anonymous'});
                }
                jQuery('.nbd-upload-loading').removeClass('is-visible');
            });
            if( jQuery('.nbo-fields-wrapper').find('#nbd-upload-hidden-' + currentComponent.id).length > 0 ){
                jQuery('.nbo-fields-wrapper').find('#nbd-upload-hidden-' + currentComponent.id).val(url);
            }else{
                jQuery('.nbo-fields-wrapper').append('<input class="nbd-upload-hidden" id="nbd-upload-hidden-'+currentComponent.id+'" type="hidden" name="nbd-field['+currentComponent.id+']" value="'+url+'" />');
            }
            $scope.resource.values[currentComponent.id].value = url;
            jQuery(document).triggerHandler( 'update_nbo_options_from_builder', { nbd_fields: $scope.resource.values, pro: true } );
        };
        $scope.addSvgFromString = function(svg){
            var  currentComponent = $scope.resource.components[$scope.resource.currentComponent],
                views = currentComponent.general.nbpb_image_configs.views; 
            var statusSvgs = [], firstView = true;
            function isLoadedAllImages(){
                var check = true;
                _.each(statusSvgs, function (status, index) {
                    var _status = angular.isDefined(status) ? status : true;
                    check = check && _status;
                });
                return check;
            };
            _.each(views, function (view, viewIndex) {
                if( view.display == 'on' ){
                    statusSvgs[viewIndex] = false;
                }
            });
            _.each(views, function (view, viewIndex) {
                if( view.display == 'on' ){
                    var _canvas = $scope.stages[viewIndex].canvas;
                    if( firstView ){
                        jQuery('.nbpb-stage-loading').addClass('nbdpb-show');
                        firstView = false;
                    }
                    fabric.loadSVGFromString(svg, function(ob, op) {
                        var object = fabric.util.groupSVGElements(ob, op);
                        function _addSvg( exist ){
                            if( angular.isDefined(exist) ){
                                var new_rect = $scope.fitRectangle(view.width, view.height, op.width, op.height, true),
                                new_width = new_rect.width,
                                new_height = new_rect.height,
                                left = view.left + (view.width - new_width) / 2,
                                top = view.top + (view.height - new_height) / 2;
                                
                            }else{
                                var max_width = _canvas.width,
                                max_height = _canvas.height,
                                new_width = max_width;
                                if (op.width < max_width) new_width = op.width;
                                var width_ratio = new_width / op.width,
                                new_height = op.height * width_ratio;
                                if (new_height > max_height) {
                                    new_height = max_height;
                                    var height_ratio = new_height / op.height;
                                    new_width = op.width * height_ratio;
                                }
                                var top =  _canvas.height / 2,
                                left =  _canvas.width / 2;
                            }
                            object.scaleToWidth(new_width);
                            object.scaleToHeight(new_height);
                            _canvas.add(object);
                            object.set({
                                left: left,
                                top: top,
                                itemId: currentComponent.id
                            });
                        }
                        if ( angular.isDefined(currentComponent.existView) && currentComponent.existView ) {
                            var _item = $scope.getLayerById(currentComponent.id, viewIndex);
                            var layerIndex = $scope.getLayerIndex(currentComponent.id, viewIndex);
                            view.width = _item.width * _item.scaleX;
                            view.height = _item.height * _item.scaleY;
                            view.left = _item.left ;
                            view.top = _item.top;
                            _canvas.remove(_item);
                            _addSvg( true );
                            object.moveTo(layerIndex);
                        }else{
                            _addSvg();
                        }
                        _canvas.renderAll();
                        statusSvgs[viewIndex] = true;
                        if( isLoadedAllImages()  ){
                            jQuery('.nbpb-stage-loading').removeClass('nbdpb-show');
                            currentComponent.existView = true;
                        }
                    });
                }
                jQuery('.nbd-upload-loading').removeClass('is-visible');
            });
        };
        $scope.deleteLayer = function(type){
            var type_confirm  = 'confirm_delete_' + type;
            var con = confirm( $scope.settings.i18n[type_confirm] );
            if( con ){
                var  currentComponent = $scope.resource.components[$scope.resource.currentComponent],
                    views = currentComponent.general['nbpb_' + type + '_configs'].views; 
                _.each(views, function (view, viewIndex) {
                    var layerIndex = $scope.getLayerIndex(currentComponent.id, viewIndex),
                    item = $scope.getLayerById(currentComponent.id, viewIndex),
                    _canvas = $scope.stages[viewIndex].canvas;
                    if( NBPBCONFIG.is_creating_task == 1 ){
                        _canvas.remove(item);
                    }else{
                        item.set({visible: false});
                        if( item.type == 'textbox' ) item.set({text: ''});
                    }
                    _canvas.renderAll();
                });
            }
            if( type == 'image' ){
                jQuery('.nbo-fields-wrapper').find('#nbd-upload-hidden-' + currentComponent.id).remove();
            }else{
                currentComponent.currentContent = '';
            }
            $scope.resource.values[currentComponent.id].value = '';
            jQuery(document).triggerHandler( 'update_nbo_options_from_builder', { nbd_fields: $scope.resource.values, pro: true } );
        };
        $scope.getComponentById = function(id){
            var component = null;
            angular.forEach($scope.resource.components, function(_component){
                if( _component.id == id ) component = _component;
            });
            return component;
        };
        $scope.get_field = function(field_id){
            var _field = null;
            angular.forEach(nbOption.options.fields, function(field){
                if( field.id == field_id ) _field = field;
            });
            return _field;
        };
        $scope.getCurrentConfig = function( component_id, a_index, sa_index ){
            var config_index;
            var component =$scope.getComponentById( component_id );
            if( angular.isDefined( component.current_pb_configs ) && component.current_pb_configs.length > 0 ){
                _.each(component.current_pb_configs, function(config, index){
                    if( config.a_index == a_index && config.sa_index == sa_index ) config_index = index;
                });
            }
            return config_index;
        };
        $scope.init();
    }
]);
nbdpbApp.factory('FabricWindow', ['$window', function($window) {
    fabric.Object.NUM_FRACTION_DIGITS = 10;
    $window.fabric.Object.prototype.set({
        transparentCorners: false,
        borderColor: 'rgba(79, 84, 103, 0.7)',
        cornerStyle: 'circle',
        cornerColor: 'rgba(255, 255, 255, 1)',
        borderDashArray:[2,2],
        cornerStrokeColor: 'rgba(63, 70, 82, 1)',
        hoverCursor: 'pointer',
        borderOpacityWhenMoving: 0,
        selectable: NBPBCONFIG.is_creating_task == 1 ? true : false,
        perPixelTargetFind: NBPBCONFIG.is_creating_task == 1 ? false : true,
        originX: 'center',
        originY: 'center',
        centeredScaling: true,
        _controlsVisibility: {
            tl: true,
            tr: true,
            br: true,
            bl: true,
            ml: false,
            mt: false,
            mr: false,
            mb: false,
            mtr: true
        }
    });
    if( NBPBCONFIG.is_mobile ) $window.fabric.Object.prototype.set({cornerSize: 17});
    $window.fabric.Canvas.prototype.set({
        preserveObjectStacking : true,
        controlsAboveOverlay: true,
        selectionColor: 'rgba(1, 196, 204, 0.3)',
        selectionBorderColor: '#01c4cc',
        selectionLineWidth: 0.5,
        centeredKey: "shiftKey",
        uniScaleKey: "altKey"
    });
    return $window.fabric;
}]);
nbdpbApp.directive('nbdCanvas', ['FabricWindow', '$timeout', '$rootScope', function(FabricWindow, $timeout, $rootScope){
    return {
        restrict: "AE",
        scope: {
            stage: '=stage',
            index: '@',
            last: '@'
        },
        link: function( scope, element, attrs ) {
            $timeout(function() {
                scope.stage.canvas = new FabricWindow.Canvas('canvas-' + scope.index);
                scope.$emit('canvas:created', scope.index, scope.last);
            });
        }
    }
}]);
nbdpbApp.directive('nbpbHover', ['$timeout', function($timeout){
    return {
        restrict: "AE",
        scope: {
            componentId: '@nbpbHover'
        },
        link: function( scope, element, attrs ) {
            $timeout(function() {
                jQuery(element).on('mouseover', function(){
                    scope.$emit('component:mouseover', scope.componentId);
                });
                jQuery(element).on('mouseout', function(){
                    scope.$emit('component:mouseout', scope.componentId);
                });
            });
        }
    }
}]);
nbdpbApp.factory('NBDDataFactory', function($http){
    return {
        get : function(action, data, callback) {
            var formData = new FormData();
            formData.append("action", action);
            formData.append("nonce", NBPBCONFIG['nonce']);
            angular.forEach(data, function (value, key) {
                var keepDefault = ['file', 'design', 'config'];
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
            });
            var config = {
                transformRequest: angular.identity,
                transformResponse: angular.identity,
                headers: {
                    'Content-Type': undefined
                }
            };
            var url = NBPBCONFIG['ajax_url'];
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
nbdpbApp.directive("nbdDndFile", ['$timeout', function($timeout) {
    return {
        restrict: "A",
        scope: {
            fieldId: '@',
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
                    if(e.originalEvent.dataTransfer){
                        if(e.originalEvent.dataTransfer.files.length) {
                            e.preventDefault();
                            e.stopPropagation();
                            handleFiles(e.originalEvent.dataTransfer.files);
                        }
                    }
                };
                dropArea.on('click', function(e){
                    Input.click();
                });
                Input.on('click', function(e){
                    e.stopPropagation();
                });
                Input.on('change', function(){
                    handleFiles(this.files);
                });
                function handleFiles(files) {
                    if(files.length > 0){
                        jQuery(element).find('.nbd-upload-loading').addClass('is-visible');
                        scope.uploadFile({field_id: scope.fieldId, files: files});
                    };
                }
            });
        }
    }
}]);
nbdpbApp.directive('nbpbColorPicker', ['$timeout', function($timeout){
    return {
        restrict: "C",
        scope: {
            onChange: '&',
            options: '=?'
        },
        link: function( scope, element, attrs ) {
            function formatColor(tiny) {
                var formatted = tiny;
                if (formatted) {
                    formatted = tiny.toString(scope.options.preferredFormat);
                }
                return formatted;
            }
            $timeout(function() {
                scope.options.change = function(color){
                    scope.onChange({color: formatColor(color)});
                };
                element.spectrum(scope.options);
            });
            element.on('$destroy', function(){
                element.spectrum('destroy');
            });
        }
    };
}]);
jQuery.fn.nbShowPopup = function () {
    return this.each(function () {
        var sefl = this;
        var $close = jQuery(this).find('.overlay-popup, .close-popup');
        if (!jQuery(this).hasClass('nbdpb-show')) {
            jQuery(this).addClass('nbdpb-show');
        }
        $close.on('click', function () {
            jQuery(sefl).removeClass('nbdpb-show');
            jQuery('body, html').removeClass('nbdpb-no-overflow');
            var $scope = angular.element(document.getElementById("nbpb-container")).scope();
            $scope.updateApp();
        });
    });
};
jQuery.fn.nbdpbCarousel = function () {
    var seflC = this;
    this.itemActive = function ($carousel) {
        var $items = $carousel.find('.nbdpb-carousel-item'), $itemA = $items.filter('.nbdpb-active'),
            $nav = $carousel.closest('.nbdpb-carousel-outer').find('.js-nav-item'),
            $dots = $carousel.closest('.nbdpb-carousel-outer').find('.nbdpb-owl-dots');
        var curT = ($carousel.offset().left - $itemA.offset().left);

        $nav.removeClass('nbdpb-disabled');
        $dots.find('.nbdpb-owl-dot').removeClass('nbdpb-active');
        $dots.find('.nbdpb-owl-dot').filter(function (i) {
            return i === $itemA.index();
        }).addClass('nbdpb-active');
        $carousel.css({
            transform: 'translate3d(' + curT + 'px, 0, 0)'
        });
        var $scope = angular.element(document.getElementById("nbpb-container")).scope(),
            stage = $itemA.find('.stage').data('stage');
        $scope.currentStage = stage;
        $scope.updateApp();
    };
    this.activeItemByIndex = function(index){
        var $items = jQuery(seflC).find('.nbdpb-carousel-item');
        $items.removeClass('nbdpb-active');
        jQuery($items[index]).addClass('nbdpb-active');
        seflC.itemActive(jQuery(seflC));
    };
    return this.each(function () {
        var $sefl = jQuery(this), $items = jQuery(this).find('.nbdpb-carousel-item'),
            $outerCarousel = jQuery(this).closest('.nbdpb-carousel-outer');

        var cWith = 0, total = $items.length, dots = '<div class="nbdpb-owl-dots"></div>';
        var nav = '<div class="nbdpb-owl-nav"></div>',
            navPrev = '<button type="button" role="presentation" class="nbdpb-owl-prev js-nav-item">' +
                '<i aria-label="Previous" class="icon-nbd icon-nbd-chevron-right rotate180"></i>' +
                '</button>',
            navNext = '<button type="button" role="presentation" class="nbdpb-owl-next js-nav-item">' +
                '<i aria-label="Next" class="icon-nbd icon-nbd-chevron-right"></i>' +
                '</button>';
        var $dots = jQuery(dots), $nav = jQuery(nav), $navPrev = jQuery(navPrev), $navNext = jQuery(navNext);
        $outerCarousel.find('.nbdpb-owl-nav').remove();
        $outerCarousel.find('.nbdpb-owl-dots').remove();
        if( $items.length > 1 ) $outerCarousel.append($dots);
        if( $items.length > 1 ) $outerCarousel.append($nav);
        $nav.append($navPrev);
        $nav.append($navNext);
        $items.each(function (i) {
            var dot = '<button role="button" class="nbdpb-owl-dot"><span></span></button>';
            cWith += $outerCarousel.outerWidth();
            jQuery(this).css({
                width: $outerCarousel.outerWidth()
            });
            $dots.append(dot);
        });
        $dots.find('.nbdpb-owl-dot').first().addClass('nbdpb-active');
        jQuery(this).css({
            width: cWith + 'px'
        });
        $dots.find('.nbdpb-owl-dot').on('click', function () {
            var index = jQuery(this).index();

            $dots.find('.nbdpb-owl-dot').removeClass('nbdpb-active');
            jQuery(this).addClass('nbdpb-active');

            $items.removeClass('nbdpb-active');
            $items.filter(function (i) {
                return i === index;
            }).addClass('nbdpb-active');

            seflC.itemActive($sefl);
        });
        $navPrev.on('click', function () {
            var $itemA = $items.filter('.nbdpb-active');
            $itemA.removeClass('nbdpb-active');
            if ($itemA.index() == 0) {
                $items.last().addClass('nbdpb-active');
            }else {
                $itemA.prev().addClass('nbdpb-active');
            }
            seflC.itemActive($sefl);
        });
        $navNext.on('click', function () {
            var $itemA = $items.filter('.nbdpb-active');
            $itemA.removeClass('nbdpb-active');
            if ($itemA.index() == ($items.length - 1)) {
                $items.first().addClass('nbdpb-active');
            }else {
                $itemA.next().addClass('nbdpb-active');
            }
            seflC.itemActive($sefl);
        });
    });
};
function getTransform(el) {
    var results = jQuery(el).css('transform').match(/matrix(?:(3d)\(\d+(?:, \d+)*(?:, (\d+))(?:, (\d+))(?:, (\d+)), \d+\)|\(\d+(?:, \d+)*(?:, (\d+))(?:, (\d+))\))/)
    if(!results) return [0, 0, 0];
    if(results[1] == '3d') return results.slice(2,5);
    results.push(0);
    return results.slice(5, 8);
}
jQuery(document).ready(function () {
    jQuery('#nbdpb-start-design').on('click', function () {
        var $scope = angular.element(document.getElementById("nbpb-container")).scope();
        $scope.updateApp();
        jQuery('body, html').addClass('nbdpb-no-overflow');
        jQuery('.nbdpb-popup.popup-design').nbShowPopup().addClass('nbdpb-no-overflow');
        appConfig.slider = jQuery('.nbdpb-carousel').nbdpbCarousel();
    });
});
jQuery(document).on( 'initialed_nbo_options', function(){
    var nbdpbAppEl = document.getElementById('nbdpb-app');
    angular.element(function() {
        angular.bootstrap(nbdpbAppEl, ['nbdpbApp']);
        if( NBPBCONFIG.is_creating_task == 1 ){
            setTimeout(function(){
                jQuery('body, html').addClass('nbdpb-no-overflow');
                jQuery('.nbdpb-popup.popup-design').nbShowPopup().addClass('nbdpb-no-overflow');
                appConfig.slider = jQuery('.nbdpb-carousel').nbdpbCarousel();
                jQuery('.nbdpb-load-page').removeClass('nbdpb-show');
            });
        }
    });
});
jQuery(document).on( 'update_nbo_options', function(e, data){
    if( !appConfig.ready ) return;
    var $scope = angular.element(document.getElementById("nbpb-container")).scope();
    $scope.initValues( false, data.pro );
});