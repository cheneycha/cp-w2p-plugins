
;NBCDESIGNMONITOR = {
    queues: [],
    canvas: [],
    conversation_id: '',
    stage_id: '',
    stages: [],
    loading: false,
    forceDestroy: false,
    firstTime: true,
    initSetting: function(){
        fabric.disableStyleCopyPaste = true;
        fabric.Object.NUM_FRACTION_DIGITS = 10;
        fabric.Image.prototype.set({
            originX: 'center',
            originY: 'center',
            selectable: false, 
        });
        fabric.CurvedText.prototype.set({
            originX: 'center',
            originY: 'top'
        });
    },
    loadDesign: function( data, callback ){
        this.forceDestroy = false;

        if( this.firstTime ){
            this.initSetting();
            this.firstTime = false;
        }

        var _data, datas, stages = jQuery('.nbc-shared-design-stages'), self = this;
        if( data ) this.queues.push( data );
        if( !this.loading || data == null ){
            this.loading = true;
            _data = this.queues.shift();
            datas = JSON.parse( _data.design );
            if( _data.conversation_id != this.conversation_id ){
                this.conversation_id = _data.conversation_id;
                this.resetDesign();
            }

            var stage_id = datas.stage_id;
            if(this.stage_id != stage_id ){
                this.stage_id = stage_id;
                stages.find('[data-stage]').hide();
                if( this.stages.indexOf( stage_id ) == -1 ){
                    this.stages.push( stage_id );
                    this.stages.sort(function(a, b){return a - b});
                }
            }

            if( stages.find('#stage-' + stage_id ).length == 0 ){
                stages.append('<div data-stage="' + stage_id + '"><canvas id="stage-' + stage_id + '" ></canvas></div>');
                setTimeout(function(){
                    self.canvas[stage_id] = new fabric.Canvas( 'stage-' + stage_id );
                    var width = parseFloat( datas.config.width ),
                    height  = parseFloat( datas.config.height ),
                    newWidth = newHeight = 500, zoom, newZoom;
                    if( width > height ){
                        zoom = width / 500;
                        newHeight = height / zoom;
                    }else{
                        zoom = height / 500;
                        newWidth = width / zoom;
                    }
                    newZoom = datas.config.zoom / zoom;
                    self.canvas[stage_id].setDimensions({'width' : newWidth, 'height' : newHeight});
                    self.canvas[stage_id].setZoom( newZoom );
                    self.loadDesignFromJson( stage_id, datas.design, datas.fonts, callback );
                });
            }else{
                stages.find('[data-stage="' + stage_id + '"]').show();
                self.canvas[stage_id].clear();
                self.loadDesignFromJson( stage_id, datas.design, datas.fonts, callback );
            }
        }
    },
    resetDesign: function(){
        jQuery('.nbc-shared-design-stages').html('');
        this.canvas = [];
        this.stages = [];
    },
    destroyDesign: function(){
        this.forceDestroy = true;
        this.resetDesign();
        this.queues = [];
        this.loading = false;
    },
    loadDesignFromJson: function( stage_id, json, fonts, callback ){
        var _canvas = this.canvas[stage_id],
        objects = json.objects,
        layerIndex = 0,
        self = this;

        function completeLoadDesign(){
            if( _canvas ) _canvas.calcOffset();
            if( _canvas ) _canvas.requestRenderAll();
            if( typeof callback == 'function' ) callback( self.stages.length );

            if( self.queues.length ){
                self.loadDesign( null );
            }else{
                self.loading = false;
            }
        }

        function loadLayer(layerIndex){
            if( self.forceDestroy ) return;
            function continueLoadLayer(){
                if( self.forceDestroy ) return;
                layerIndex++;
                if( objects.length != 0 && layerIndex < objects.length ){
                    loadLayer(layerIndex);
                }else{
                    completeLoadDesign();
                }
            }
            function fromObject( item, type ){
                if( self.forceDestroy ) return;
                var klass = fabric.util.getKlass(type);
                klass.fromObject(item, function(item){
                    if( self.forceDestroy ) return;
                    if( _canvas ) _canvas.add(item);
                    continueLoadLayer();
                });
            }

            if( objects.length > 0 ){
                if( self.forceDestroy ) return;
                var item = objects[layerIndex],
                type = item.type;
                if( type == 'image' || type == 'custom-image' ){
                    fabric.Image.fromObject(item, function(_image){
                        if( self.forceDestroy ) return;
                        if( _canvas ) _canvas.add(_image);
                        continueLoadLayer();
                    });
                }else{
                    if( ['i-text', 'text', 'textbox', 'curvedText'].indexOf( type ) > -1  ){
                        var font = new FontFaceObserver( item.fontFamily );
                        font.load(item.text).then(function () {
                            fromObject( item, type )
                        }, function () {
                            fromObject( item, type )
                        });
                    }else{
                        fromObject( item, type );
                    }
                }
            }else{
                completeLoadDesign();
            }
        }

        function loadFonts(){
            fonts.forEach(function(font){
                var fontName = font.alias,
                font_id = fontName.replace(/\s/gi, '').toLowerCase();
                if( !jQuery('#' + font_id).length ){
                    if( font.type == 'google' ){
                        jQuery('head').append('<link id="' + font_id + '" href="https://fonts.googleapis.com/css?family='+ fontName.replace(/\s/gi, '+') +':400,400i,700,700i" rel="stylesheet" type="text/css">');
                    }else{
                        if( font.file.r == '1' ){
                            var font_url = font.url;
                            if(! (font_url.indexOf("http") > -1)) font_url = nbd_live_chat.font_url + font_url; 
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
                            angular.forEach(font.file, function (file, index) {
                                if( file != 0 ){
                                    var font_url = file;
                                    if(! (file.indexOf("http") > -1)) font_url = nbd_live_chat.font_url + file;
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
                }
            });
        };

        loadFonts();
        loadLayer( layerIndex );
    },
    loadPrevSharedStage: function(){
        var stage_id, index = this.stages.indexOf( this.stage_id );
        if( index == 0 ){
            stage_id = this.stages[this.stages.length - 1];
        }else{
            stage_id = this.stages[index - 1];
        }
        this.stage_id = stage_id;
        jQuery('.nbc-shared-design-stages').find('[data-stage]').hide();
        jQuery('.nbc-shared-design-stages').find('[data-stage="' + stage_id + '"]').show();
    },
    loadNextSharedStage: function(){
        var stage_id, index = this.stages.indexOf( this.stage_id );
        if( index == ( this.stages.length - 1 ) ){
            stage_id = this.stages[0];
        }else{
            stage_id = this.stages[index + 1];
        }
        this.stage_id = stage_id;
        jQuery('.nbc-shared-design-stages').find('[data-stage]').hide();
        jQuery('.nbc-shared-design-stages').find('[data-stage="' + stage_id + '"]').show();
    }
};