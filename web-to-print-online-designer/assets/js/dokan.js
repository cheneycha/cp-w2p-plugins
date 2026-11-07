var NBD_STAGE = {
    'width' : 500,
    'height' : 500
};
var _round = function(num, dec){
    return Number((num).toFixed(dec)); 
};  
jQuery(document).ready(function ($) {
    NBDESIGNADMIN.loopConfigAreaDesign();
    NBDESIGNADMIN.init_color_picker();
    if($('#_nbdesigner_enable').prop("checked")){
        $('.nbdesigner-right.add_more').show();
    };
    NBDESIGNADMIN.collapseAll('com');
    $('#_nbdesigner_enable').change(function() {
        $('#nbd-setting-container').toggleClass('nbdesigner-disable');    
        $('#nbd_upload_status').toggleClass('nbdesigner-disable');    
        if($('#_nbdesigner_enable').prop("checked")){
            $('.nbdesigner-right.add_more').show();
        };        
    });
    $('#_nbdesigner_enable_upload').change(function() {
        $('.nbd-tabber.nbd-upload').toggleClass('nbdesigner-disable');   
        if( $('.nbd-tabber.nbd-upload').hasClass( 'selected' ) ){
            $('.nbd-tabber.nbd-design').triggerHandler( 'click' );
        }
        $('#nbd_upload_without_design_status').toggleClass('nbdesigner-disable');   
        if( !$('#_nbdesigner_enable_upload').prop("checked") && $('#_nbdesigner_upload_without_design').prop("checked") ){
            $('#_nbdesigner_upload_without_design').prop("checked", false);
        }
    });    
    $('.nbd-dependence').change(function() {
        var t = $(this);
        $(t.data('target')).toggleClass('nbdesigner-disable');   
        $.each(t.parent().find('.nbd-untarget'), function(index, el){
            var untarget = '#' + $(el).attr('id');
            if(untarget != t.data('target')) $(el).toggleClass('nbdesigner-disable');   
        });
    });
    $('#woocommerce-product-data').on('woocommerce_variations_loaded', function(event) {
        NBDESIGNADMIN.loopConfigAreaDesign();
    });    
    jQuery('input[name="_nbdesigner_option[dynamic_side]"]').on('change', function(){
        jQuery('.nbd-price-per-page').removeClass('nbdesigner-disable');
        var status = parseInt( jQuery(this).val() );
        if(!status){
            jQuery('.nbd-price-per-page').addClass('nbdesigner-disable');
        };
    });
    $('.nbd-tabber').click(function() {
        var t = $(this),
            s = $('.nbd-tabber.selected');

        s.removeClass("selected");
        t.addClass("selected");
        $(s.data('target')).fadeOut(0);
        $(t.data('target')).fadeIn(200);
    });   
    $('.dokan-form-container .dokan_tabs li').on('click', function(){
        if( $(this).hasClass('variations_options') ){
            jQuery('.dokan-form-container .dokan-product-edit-left').addClass('full-width');
            jQuery('.dokan-form-container .dokan-product-edit-right').addClass('fixed');            
        }else{
            jQuery('.dokan-form-container .dokan-product-edit-left').removeClass('full-width');
            jQuery('.dokan-form-container .dokan-product-edit-right').removeClass('fixed');        
        }
    });
});
var NBDESIGNADMIN = {
    loadImage: function (e) {
        var upload;
        if (upload) {
            upload.open();
            return;
        }
        var self = this;
        var index = jQuery(e).data('index'),
            _img = jQuery(e).parents('.nbdesigner-box-collapse').find('.designer_img_src'),
            _input = jQuery(e).parents('.nbdesigner-box-collapse').find('.hidden_img_src');
        upload = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
        upload.on('select', function () {
            attachment = upload.state().get('selection').first().toJSON();
            _img.attr('src', attachment.url);
            _img.show();
            //_input.val(attachment.url);
            _input.val(attachment.id);
        });
        upload.open();
    },
    loadImageOverlay: function(e){
        var upload;
        if (upload) {
            upload.open();
            return;
        }  
        var ip_image = jQuery(e).parents('.nbdesigner-box-collapse').find('.hidden_overlay_src'),  
            image = jQuery(e).parents('.nbdesigner-box-collapse').find('.img_overlay');  
        upload = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            multiple: false
        });
        upload.on('select', function () {
            attachment = upload.state().get('selection').first().toJSON();
            image.attr('src', attachment.url);
            image.show();
            ip_image.val(attachment.id);
            //ip_image.val(attachment.url);
        });
        upload.open();        
    },
    deleteOrientation: function (e) {
        var variantion = jQuery(e).data('variation');
        if((jQuery(e).parents('.nbdesigner-boxes').find('.nbdesigner-box-container').length) > 1){
            jQuery(e).parents('.nbdesigner-box-container').remove();
            this.resetBoxes(variantion);            
        }else{
            jQuery(e).parents('.nbdesigner-box-container').hide();
        };
    },
    resetBoxes: function (command) {
        if(command == 'com') command = '';
        var index = '#nbdesigner-boxes' + command,
        name = '_designer_setting' + command;
        jQuery.each(jQuery(index + ' .nbdesigner-box-container'), function (key, val) {
            jQuery(this).find('.orientation_name').attr('name', name + '[' + key + '][orientation_name]');
            jQuery(this).find('.delete_orientation').attr('data-index', key);
            jQuery(this).find('.hidden_img_src').attr('name', name + '[' + key + '][img_src]');
            jQuery(this).find('.hidden_img_src_top').attr('name', name + '[' + key + '][img_src_top]');
            jQuery(this).find('.hidden_img_src_left').attr('name', name + '[' + key + '][img_src_left]');
            jQuery(this).find('.hidden_img_src_width').attr('name', name + '[' + key + '][img_src_width]');
            jQuery(this).find('.hidden_img_src_height').attr('name', name + '[' + key + '][img_src_height]');
            jQuery(this).find('.nbdesigner_move').attr('data-index', key);
            jQuery(this).find('.nbdesigner-add-image').attr('data-index', key);
            jQuery(this).find('.real_width').attr('name', name + '[' + key + '][real_width]');
            jQuery(this).find('.real_height').attr('name', name + '[' + key + '][real_height]');
            jQuery(this).find('.real_top').attr('name', name + '[' + key + '][real_top]');
            jQuery(this).find('.real_left').attr('name', name + '[' + key + '][real_left]');
            jQuery(this).find('.area_design_top').attr('name', name + '[' + key + '][area_design_top]');
            jQuery(this).find('.area_design_left').attr('name', name + '[' + key + '][area_design_left]');
            jQuery(this).find('.area_design_width').attr('name', name + '[' + key + '][area_design_width]');
            jQuery(this).find('.area_design_height').attr('name', name + '[' + key + '][area_design_height]');
            jQuery(this).find('.product_width').attr('name', name + '[' + key + '][product_width]');
            jQuery(this).find('.product_height').attr('name', name + '[' + key + '][product_height]');
            jQuery(this).find('.nbd-color-picker').attr('name', name + '[' + key + '][bg_color_value]');
            jQuery(this).find('.bg_type').attr('name', name + '[' + key + '][bg_type]');
            jQuery(this).find('.area_design_type').attr('name', name + '[' + key + '][area_design_type]');
            jQuery(this).find('.hidden_overlay_src').attr('name', name + '[' + key + '][img_overlay]');
            jQuery(this).find('.show_overlay').attr('name', name + '[' + key + '][show_overlay]');
            jQuery(this).find('.include_overlay').attr('name', name + '[' + key + '][include_overlay]');
            jQuery(this).find('.hidden_nbd_version').attr('name', name + '[' + key + '][version]');
            jQuery(this).find('.hidden_nbd_ratio').attr('name', name + '[' + key + '][ratio]');
            jQuery(this).find('.margin_left_right').attr('name', name + '[' + key + '][margin_left_right]');
            jQuery(this).find('.margin_top_bottom').attr('name', name + '[' + key + '][margin_top_bottom]');
            jQuery(this).find('.bleed_top_bottom').attr('name', name + '[' + key + '][bleed_top_bottom]');
            jQuery(this).find('.bleed_left_right').attr('name', name + '[' + key + '][bleed_left_right]');
            jQuery(this).find('.show_bleed').attr('name', name + '[' + key + '][show_bleed]');
            jQuery(this).find('.show_safe_zone').attr('name', name + '[' + key + '][show_safe_zone]');
            jQuery(this).find('.show_safe_zone').attr('name', name + '[' + key + '][show_safe_zone]');
            jQuery(this).find('.nbd-safe-zone-con').attr('id', 'nbd-safe-zone' + command + key );
            jQuery(this).find('.nbd-bleed-con').attr('id', 'nbd-bleed' + command + key );
            jQuery(this).find('.show_safe_zone').attr('data-target', '#nbd-safe-zone' + command + key );
            jQuery(this).find('.show_bleed').attr('data-target', '#nbd-bleed' + command + key );
        });
        this.loopConfigAreaDesign();
    },
    calcPositionImg: function (e) {
        setTimeout(function(){
            var p = e.parent(),
            top = e.offset().top - jQuery(p).offset().top,
            left = e.offset().left - jQuery(p).offset().left,
            width = e.width(),
            height = e.height();
            e.parents('.nbdesigner-image-box').find('.hidden_img_src_top').val(top);
            e.parents('.nbdesigner-image-box').find('.hidden_img_src_left').val(left);
            e.parents('.nbdesigner-image-box').find('.hidden_img_src_width').val(width);
            e.parents('.nbdesigner-image-box').find('.hidden_img_src_height').val(height);
        },0);       
    },
    loopConfigAreaDesign: function () {
        var parent = this;
        jQuery('.nbdesigner-area-design').each(function (key, val) {
            var self = this;
            jQuery(this).on('click', function () {
                jQuery('.nbdesigner-area-design').removeClass('selected');
                jQuery(this).addClass('selected');
            });
            jQuery(this).resizable({
                handles: "ne, se, sw, nw",
                aspectRatio: false,
                maxWidth: NBD_STAGE.width,
                maxHeight: NBD_STAGE.height,
                resize: function (event, ui) {
                    parent.updateDimension(self, ui.size.width, ui.size.height, ui.position.left, ui.position.top);
                },
                start: function (event, ui) {
                    /*TODO*/
                }
            }).draggable({
                drag: function (event, ui) {
                    parent.updateDimension(self, null, null, ui.position.left, ui.position.top);
                }
            });
        });
        this.init_color_picker();
    },
    calcMargin: function (w, h, _img) {
        setTimeout(function(){
            var h_d = _img.parent().height();
            if ((w < h) && (h >= h_d)) {
                _img.css('margin-top', '0');
            };
            if ((w <= h_d) && (h <= h_d)) {
                var offset = (h_d - h) / 2;
                _img.css('margin-top', offset + 'px');
            };
            if ((w >= h) && (w > h_d)) {
                h = h * h_d / w;
                var offset = (h_d - h) / 2;
                _img.css('margin-top', offset + 'px');
            };
        },0);      
    },
    nbdesigner_move: function (e, command) {
        var parent = jQuery(e).parents('.nbdesigner-box-collapse'),
        area = parent.find('.nbdesigner-area-design'),
        overlay = parent.find('.nbdesigner-image-overlay'),
        left = area.css('left'),
        top = area.css('top'),
        w = area.width(),
        h = area.height(),
        ip_left = parent.find('.area_design_left'),
        ip_top = parent.find('.area_design_top'),
        ip_width = parent.find('.area_design_width'),
        ip_height = parent.find('.area_design_height');
        switch (command) {
            case 'left':
                area.css('left', parseFloat(left) - 1);
                overlay.css('left', parseFloat(left) - 1);
                ip_left.val(parseFloat(left) - 1);
                break;
            case 'right':
                area.css('left', parseFloat(left) + 1);
                overlay.css('left', parseFloat(left) + 1);
                ip_left.val(parseFloat(left) + 1);
                break;
            case 'down':
                area.css('top', parseFloat(top) + 1);
                overlay.css('top', parseFloat(top) + 1);
                ip_top.val(parseFloat(top) + 1);
                break;
            case 'up':
                area.css('top', parseFloat(top) - 1);
                overlay.css('top', parseFloat(top) - 1);
                ip_top.val(parseFloat(top) - 1);
                break;
            case 'center':
                left = (NBD_STAGE.width - w) / 2;
                top = (NBD_STAGE.height - h) / 2;
                area.css({'top': top + 'px', 'left': left + 'px'});
                overlay.css({'top': top + 'px', 'left': left + 'px'});
                ip_left.val(left);
                ip_top.val(top);
                break;
            case 'fit':             
                var width = parent.find('.nbdesigner-image-original').width(),
                height = parent.find('.nbdesigner-image-original').height(),
                p_width = parent.find('.product_width').val(),
                p_height = parent.find('.product_height').val();
                parent.find('.real_width').val(p_width);
                parent.find('.real_height').val(p_height);
                parent.find('.real_top').val(0);
                parent.find('.real_left').val(0);
                left = (NBD_STAGE.width - width) / 2;
                top = (NBD_STAGE.height - height) / 2;
                area.css({'top': top + 'px', 'left': left + 'px', 'width': width + 'px',  'height': height + 'px'});
                overlay.css({'top': top + 'px', 'left': left + 'px', 'width': width + 'px',  'height': height + 'px'});
                ip_left.val(left);
                ip_top.val(top);                
                ip_width.val(width);                
                ip_height.val(height);   
                break;
        }
        parent.find('.nbdesiger-update-area-design').addClass('active');
        this.updateBleed(e);
        this.updateSafeZone(e);        
    },
    ajustImage: function () {
        var self = this;   
        jQuery.each(jQuery('.designer_img_src'), function () {
            var _img = jQuery(this),
            w = jQuery(this).width(),
            h = jQuery(this).height();
            self.calcMargin(w, h, _img);
            self.calcPositionImg(_img);
        });      
    },
    updateDimension: function (e, width, height, left, top) {
        var parent = jQuery(e).parents('.nbdesigner-box-collapse');
        var ip_left = parent.find('.area_design_left'),
            ip_top = parent.find('.area_design_top'),
            ip_width = parent.find('.area_design_width'),
            ip_height = parent.find('.area_design_height');
        if (left) ip_left.val(left);
        if (top) ip_top.val(top);
        if (width) ip_width.val(width);
        if (height) ip_height.val(height);
        parent.find('.nbdesiger-update-area-design').addClass('active');
        var area = parent.find('.nbdesigner-area-design');
        parent.find('.nbdesigner-image-overlay').css({
                'width': area.css('width'),           
                'height': area.css('height'),           
                'left': area.css('left'),           
                'top': area.css('top')           
            });
        this.updateBleed(e);
        this.updateSafeZone(e);            
    },
    updatePositionDesignArea: function (e) {
        var att = jQuery(e).data('index'),
        value = jQuery(e).val(),
        parent = jQuery(e).parents('.nbdesigner-box-collapse').find('.nbdesigner-info-box'),              
        area = jQuery(e).parents('.nbdesigner-box-collapse').find('.nbdesigner-area-design'),
        overlay = jQuery(e).parents('.nbdesigner-box-collapse').find('.nbdesigner-image-overlay'),
        height = parent.find('.area_design_height').val(),
        width = parent.find('.area_design_width').val(),
        left = parent.find('.area_design_left').val(),
        top = parent.find('.area_design_top').val(),
        sefl = jQuery(e);
        if(att == 'width'){
            if(value < 0) value = 0;
            if(value > (NBD_STAGE.width - left)) value = NBD_STAGE.width - left;
        } else if(att == 'height'){
            if(value < 0) value = 0;
            if(value > (NBD_STAGE.height - top)) value = NBD_STAGE.height - top;         
        } else if(att == 'left'){
            if(value < 0) value = 0;
            if(value > (NBD_STAGE.width - width)){
                if(value > NBD_STAGE.width) value = NBD_STAGE.width;
                parent.find('.area_design_width').val(NBD_STAGE.width -value);
                area.css('width', (NBD_STAGE.width - value) + 'px');
            }            
        } else if(att == 'top'){
            if(value < 0) value = 0;  
            if(value > (NBD_STAGE.height - height)){
                if(value > NBD_STAGE.height) value = NBD_STAGE.height;
                parent.find('.area_design_height').val(NBD_STAGE.height -value);
                area.css('height', (NBD_STAGE.height - value) + 'px');
            }              
        }
        parent.find('.nbdesiger-update-area-design').addClass('active');
        area.css(att, value + 'px');
        overlay.css(att, value + 'px');
        sefl.val(value);
        this.updateBleed(e);        
        this.updateSafeZone(e);
    },
    updateDimensionRealOutputImage: function(e, command){
        var parent = jQuery(e).parents('.nbdesigner-box-collapse').find('.nbdesigner-info-box'),
        width = parent.find('.area_design_width'),
        height = parent.find('.area_design_height'),
        sefl = jQuery(e);
        switch (command) {
            case 'width':
                var w = sefl.val(),
                original_val = parseInt(parent.find('.real_width_hidden').html()),        
                h = parent.find('.real_height').val(),
                _h = parseInt(h * w / original_val);
                parent.find('.real_height').val(_h);
                break;
            case 'height':
                var h = sefl.val(),
                original_val = parseInt(parent.find('.real_height_hidden').html()),        
                w = parent.find('.real_width').val(),
                _w = parseInt(w * h / original_val);
                parent.find('.real_width').val(_w);  
                break;
        }
    },
    updateBleed: function(e){
        var bleedEl = jQuery(e).parents('.nbdesigner-box-container').find('.nbd-bleed'),
            config = this.initParameter(e);
        bleedEl.css({width: config.bleedSize.width + 'px',
                    height: config.bleedSize.height + 'px',
                    top: config.bleedSize.top + 'px',
                    left: config.bleedSize.left + 'px'
                });
    },
    updateSafeZone: function(e){
        var zoneEl = jQuery(e).parents('.nbdesigner-box-container').find('.nbd-safe-zone'),
            config = this.initParameter(e);
        zoneEl.css({width: config.safeZone.width + 'px',
                    height: config.safeZone.height + 'px',
                    top: config.safeZone.top + 'px',
                    left: config.safeZone.left + 'px'
                });        
    },
    addOrientation: function (command) {
        var _command = command;
        if(command == 'com') _command = '';
        var id = '#nbdesigner-boxes' + _command;
        var old_box = jQuery(id+' .nbdesigner-box-container').last();
        if(old_box.css('display') == 'none'){
            old_box.show();
        }else{
            var checked = old_box.find('.bg_type:checked').val();
            var new_box = old_box.clone();
            new_box.appendTo(id);           
            jQuery(new_box).find('.bg_type').each(function(index, attr) { 
                jQuery(this).attr('name', 'bg_type_clone');
            });
            jQuery(old_box).find('.bg_type[value="'+checked+'"]').prop('checked', true);
            jQuery(new_box).find('.bg_type[value="'+checked+'"]').prop('checked', true);
            new_box.find('.ui-resizable-handle').remove();
            new_box.find('.nbd-helper').remove();
            new_box.find('.nbdesigner_bg_color').html("");
            new_box.find('.nbdesigner_bg_color').append('<input type="text" name="_designer_setting[0][bg_color_value]" value="#ffffff" class="nbd-color-picker" />');
            this.resetBoxes(command);            
        };
        
    },
    collapseBox: function (e) {
        var clicked_element = jQuery(e);
        var toggle_element = jQuery(e).parents('.nbdesigner-box-container').find('.nbdesigner-box-collapse');
        toggle_element.slideToggle(function () {
            if (toggle_element.is(':visible')) {
                clicked_element.html('<span class="dashicons dashicons-arrow-up"></span> Less setting');
            } else {
                clicked_element.html('<span class="dashicons dashicons-arrow-down"></span> More setting');
            }
        });                   
    },
    show_variation_config : function(e){
        var self = this;
        var parent = jQuery(e).parents('.nbdesigner-setting-variation');
        parent.find('.nbdesigner-variation-setting').toggleClass('nbdesigner-disable');    
        if(jQuery(e).prop("checked")){
            parent.find('.nbdesigner-right.add_more').show();     
            parent.find('.nbdesigner-variation-setting').show();     
            jQuery.each(parent.find('.nbdesigner-area-design'), function (key, val) {
                var _this = this;
                jQuery(this).resizable({
                    handles: "ne, se, sw, nw",
                    aspectRatio: false,
                    maxWidth: NBD_STAGE.width,
                    maxHeight: NBD_STAGE.height,
                    resize: function (event, ui) {
                        self.updateDimension(_this, ui.size.width, ui.size.height, ui.position.left, ui.position.top);
                    },
                    start: function (event, ui) {
                        /*TODO*/
                    }
                }).draggable({containment: "parent",
                    drag: function (event, ui) {
                        self.updateDimension(_this, null, null, ui.position.left, ui.position.top);
                    }
                });                  
            })
          
        }else{
            parent.find('.nbdesigner-right.add_more').hide();
            parent.find('.nbdesigner-variation-setting').hide();            
        }
    },
    init_color_picker: function(){
        jQuery.each(jQuery('.nbd-color-picker'), function () {
            jQuery(this).wpColorPicker({
                change: function (evt, ui) {
                    var $input = jQuery(this);
                    setTimeout(function () {
                        if ($input.wpColorPicker('color') !== $input.data('tempcolor')) {
                            $input.change().data('tempcolor', $input.wpColorPicker('color'));
                            $input.val($input.wpColorPicker('color'));
                            $input.parents('.nbdesigner-box-collapse').find('.nbdesigner-image-original').css("background", ""); 
                            $input.parents('.nbdesigner-box-collapse').find('.nbdesigner-image-original').css("background", $input.wpColorPicker('color')); 
                        }
                    }, 10);
                }
            });            
        })
    },
    toggleShowOverlay : function(e){
        jQuery(e).parents('.nbdesigner-box-collapse').find('.nbdesigner-image-overlay').toggle();
        jQuery(e).parents('.nbdesigner-box-collapse').find('.overlay-toggle').toggle();
    },
    toggleBleed: function(e){
        jQuery(e).parents('.nbdesigner-box-collapse').find('.nbd-bleed-con').toggleClass('nbdesigner-disable');
        jQuery(e).parents('.nbdesigner-box-collapse').find('.nbd-bleed').toggleClass('nbdesigner-disable');
    },
    toggleSafeZone: function(e){
        jQuery(e).parents('.nbdesigner-box-collapse').find('.nbd-safe-zone-con').toggleClass('nbdesigner-disable');
        jQuery(e).parents('.nbdesigner-box-collapse').find('.nbd-safe-zone').toggleClass('nbdesigner-disable');
    },    
    change_background_type : function(e){
        var value = jQuery(e).val();
        if(value == 'image'){
            jQuery(e).parents('.nbdesigner-box-collapse').find('.nbdesigner_bg_image').show();
            jQuery(e).parents('.nbdesigner-box-collapse').find('.nbdesigner_bg_color').hide();   
            jQuery(e).parents('.nbdesigner-box-collapse').find('.designer_img_src').show();
            jQuery(e).parents('.nbdesigner-box-collapse').find('.nbdesigner-image-original').removeClass("background-transparent");  
        }else if(value == 'color'){
            jQuery(e).parents('.nbdesigner-box-collapse').find('.nbdesigner_bg_image').hide();
            jQuery(e).parents('.nbdesigner-box-collapse').find('.nbdesigner_bg_color').show();      
            var color = jQuery(e).parents('.nbdesigner-box-collapse').find('.nbd-color-picker').val();
            jQuery(e).parents('.nbdesigner-box-collapse').find('.nbdesigner-image-original').removeClass("background-transparent"); 
            jQuery(e).parents('.nbdesigner-box-collapse').find('.nbdesigner-image-original').css("background", color);      
            jQuery(e).parents('.nbdesigner-box-collapse').find('.designer_img_src').hide();
        }else{
            jQuery(e).parents('.nbdesigner-box-collapse').find('.nbdesigner_bg_image').hide();
            jQuery(e).parents('.nbdesigner-box-collapse').find('.nbdesigner_bg_color').hide();    
            jQuery(e).parents('.nbdesigner-box-collapse').find('.designer_img_src').hide();
            jQuery(e).parents('.nbdesigner-box-collapse').find('.nbdesigner-image-original').css("background", "");      
            jQuery(e).parents('.nbdesigner-box-collapse').find('.nbdesigner-image-original').addClass("background-transparent");      
        }
    },
    change_dimension_product: function(e){
        var parent = jQuery(e).parents('.nbdesigner-box-collapse'),
        ip_left = parent.find('.hidden_img_src_left'),
        ip_top = parent.find('.hidden_img_src_top'),
        ip_width = parent.find('.hidden_img_src_width'),
        ip_height = parent.find('.hidden_img_src_height'),
        ip_ratio = parent.find('.hidden_nbd_ratio');
        var config = this.initParameter(e);
        ip_left.val(config.proSize.left);
        ip_top.val(config.proSize.top);
        ip_width.val(config.proSize.width);
        ip_height.val(config.proSize.height);
        ip_ratio.val(config.ratio);
        parent.find('.nbdesigner-image-original').css({
            'width' : config.proSize.width,
            'height' : config.proSize.height,
            'left' : config.proSize.left,
            'top' : config.proSize.top
        });
        this.updateRelativePosition(e, 'width');
        this.updateRelativePosition(e, 'height');
        this.updateRelativePosition(e, 'top');
        this.updateRelativePosition(e, 'left');
    },
    updateRelativePosition: function(e, command){
        var parent = jQuery(e).parents('.nbdesigner-box-collapse');
        parent.find('.nbd-has-notice').removeClass('nbd-notice');
        var config = this.initParameter(e),
            new_value = 0;
        switch (command) {
            case 'width':
                new_value =  Math.round(config.ratio *  config.vRealWidth);
                config.iRelWidth.val(new_value);
                break;
            case 'height':
                new_value =  Math.round(config.ratio *  config.vRealHeight);
                config.iRelHeight.val(new_value);
                break;
            case 'top':             
                new_value =  Math.round(config.ratio *  (config.vRealTop + config.offset.top));
                config.iRelTop.val(new_value);             
                break;
            case 'left':
                new_value =  Math.round(config.ratio *  (config.vRealLeft + config.offset.left));
                config.iRelLeft.val(new_value);               
                break;
        }
        config.design_area.css(command, new_value);
        config.overlay_area.css(command, new_value);
        if((config.vRealWidth + config.vRealLeft) > config.vProWidth) parent.find('.notice-width').addClass('nbd-notice');
        if((config.vRealHeight + config.vRealTop) > config.vProHeight) parent.find('.notice-height').addClass('nbd-notice');
        this.updateBleed(e);
        this.updateSafeZone(e);        
    },
    collapseAll : function(command){
        if(command == 'com') command = '';
        var id = '#nbdesigner-boxes' + command;
        jQuery.each(jQuery(id + ' .nbdesigner-collapse'), function(){
            var self = jQuery(this),
            toggle_element = self.parents('.nbdesigner-box-container').find('.nbdesigner-box-collapse');
            if (toggle_element.is(':visible')) {
                self.html('<span class="dashicons dashicons-arrow-down"></span> More setting');
                toggle_element.slideToggle();
            }          
        });
        return false;
    },
    updateDesignAreaSize : function(e){
        var config = this.initParameter(e);
        var vRealWidth = _round(config.vRelWidth / config.ratio, 2),
            vRealHeight = _round(config.vRelHeight / config.ratio, 2),
            vRealLeft = _round(config.vRelLeft / config.ratio - config.offset.left, 2),
            vRealTop = _round(config.vRelTop / config.ratio - config.offset.top, 2);
        config.iRealWidth.val(vRealWidth) ;   
        config.iRealHeight.val(vRealHeight) ;   
        config.iRealLeft.val(vRealLeft) ;   
        config.iRealTop.val(vRealTop) ;   
        config.updateRealSizeButton.removeClass('active');
        var config = this.initParameter(e);
        this.updateBleed(e);
        this.updateSafeZone(e);          
    },
    duplicateDefinedDimension: function(e){
        var new_size = jQuery('#nbd-custom-size-defined .nbd-defined-size').last().clone();
        new_size.insertBefore('#nbd-duplicate-size-con');
        this.resetDefinedDimension();
    },
    deleteDefinedDimension: function(e){
        if(jQuery(e).parents('#nbd-custom-size-defined').find('.nbd-defined-size').length == 1) return;
        jQuery(e).parent('.nbd-defined-size').remove();
        this.resetDefinedDimension();
    },    
    resetDefinedDimension: function(e){
        jQuery.each( jQuery('#nbd-custom-size-defined').find('.nbd-defined-size'), function(key, val){
            jQuery(this).find('.nbd-defined-width').attr('name', '_nbdesigner_option[defined_dimension][' + key + '][width]');
            jQuery(this).find('.nbd-defined-height').attr('name', '_nbdesigner_option[defined_dimension][' + key + '][height]');
            jQuery(this).find('.nbd-defined-price').attr('name', '_nbdesigner_option[defined_dimension][' + key + '][price]');
        });
    },
    initParameter: function(e){
        var parent = jQuery(e).parents('.nbdesigner-box-collapse'),
            iProWidth = parent.find('.product_width'),
            iProHeight = parent.find('.product_height'),
            vProWidth = parseFloat(iProWidth.val()),
            vProHeight = parseFloat(iProHeight.val()),
            iRealWidth = parent.find('.real_width'),
            iRealHeight = parent.find('.real_height'),
            iRealLeft = parent.find('.real_left'),
            iRealTop = parent.find('.real_top'),
            vRealWidth = parseFloat(iRealWidth.val()),
            vRealHeight = parseFloat(iRealHeight.val()),
            vRealLeft = parseFloat(iRealLeft.val()),
            vRealTop = parseFloat(iRealTop.val()),
            iRelWidth = parent.find('.area_design_width'),
            iRelHeight = parent.find('.area_design_height'),
            iRelLeft = parent.find('.area_design_left'),
            iRelTop = parent.find('.area_design_top'),
            vRelWidth = parseFloat(iRelWidth.val()),
            vRelHeight = parseFloat(iRelHeight.val()),
            vRelLeft = parseFloat(iRelLeft.val()),
            vRelTop = parseFloat(iRelTop.val()),   
            iBleedTopBottom = parent.find('.bleed_top_bottom'),
            vBleedTopBottom = parseFloat(iBleedTopBottom.val()),  
            iBleedLeftRight = parent.find('.bleed_left_right'),
            vBleedLeftRight = parseFloat(iBleedLeftRight.val()),
            iMarginTopBottom = parent.find('.margin_top_bottom'),
            vMarginTopBottom = parseFloat(iMarginTopBottom.val()),
            iMarginLeftRight = parent.find('.margin_left_right'),
            vMarginLeftRight = parseFloat(iMarginLeftRight.val()),            
            design_area = parent.find('.nbdesigner-area-design'),
            overlay_area = parent.find('.nbdesigner-image-overlay'),
            updateRealSizeButton = parent.find('.nbdesiger-update-area-design'),
            offset = {'left' : parseFloat(vProHeight - vProWidth)/2, 'top' : 0},
            ratio = NBD_STAGE.height / vProHeight,
            proSize = {
                'height' : NBD_STAGE.height,
                'width'  : Math.round(vProWidth * ratio),
                'left'   : Math.round((NBD_STAGE.width - vProWidth * ratio) / 2),
                'top'    : 0
            };
            if(vProWidth/vProHeight > NBD_STAGE.width/NBD_STAGE.height) {
                ratio = NBD_STAGE.width / vProWidth;
                offset = {'left' : 0, 'top' : parseFloat(vProWidth - vProHeight)/2};
                proSize = {
                    'width' : NBD_STAGE.width,
                    'height'  : Math.round(vProHeight * ratio),
                    'top'   : Math.round((NBD_STAGE.height - vProHeight * ratio) / 2),
                    'left'    : 0
                };                
            };
        var bleedSize = {
            width :  vRelWidth - 2 *  vBleedLeftRight * ratio,
            height :  vRelHeight - 2 *  vBleedTopBottom * ratio,
            left: vRelLeft + vBleedLeftRight * ratio,
            top: vRelTop + vBleedTopBottom * ratio
        };
        var safeZone = {
            width :  vRelWidth - 2 *  vBleedLeftRight * ratio  - 2 *  vMarginLeftRight * ratio,
            height :  vRelHeight - 2 *  vBleedTopBottom * ratio  - 2 *  vMarginTopBottom * ratio,
            left: vRelLeft + vBleedLeftRight * ratio + vMarginLeftRight * ratio,
            top: vRelTop + vBleedTopBottom * ratio + vMarginTopBottom * ratio            
        };
        return {
            iProWidth : iProWidth,
            iProHeight : iProHeight,
            vProWidth : vProWidth,
            vProHeight : vProHeight,
            iRealWidth : iRealWidth,
            iRealHeight : iRealHeight,
            iRealLeft : iRealLeft,
            iRealTop : iRealTop,
            vRealWidth : vRealWidth,
            vRealHeight : vRealHeight,
            vRealLeft : vRealLeft,
            vRealTop : vRealTop,
            iRelWidth : iRelWidth,
            iRelHeight : iRelHeight,
            iRelLeft : iRelLeft,
            iRelTop : iRelTop,
            vRelWidth : vRelWidth,
            vRelHeight : vRelHeight,
            vRelLeft : vRelLeft,
            vRelTop : vRelTop,
            design_area : design_area,
            overlay_area : overlay_area,
            ratio : ratio,
            offset : offset,
            updateRealSizeButton : updateRealSizeButton,
            proSize : proSize,
            bleedSize : bleedSize,
            safeZone : safeZone
        };
    },
    changeAreaDesignShape: function( e, type ){
        var parent = jQuery(e).parents('.nbdesigner-box-collapse'),
            design_area = parent.find('.nbdesigner-area-design'),
            overlay_area = parent.find('.nbdesigner-image-overlay'),
            bleed_area = parent.find('.nbd-bleed'),
            safe_zone = parent.find('.nbd-safe-zone');
        switch( type ){
            case 2:
                design_area.addClass('nbd-rounded');
                overlay_area.addClass('nbd-rounded');
                bleed_area.addClass('nbd-rounded');
                safe_zone.addClass('nbd-rounded');
                break;
            default: 
                design_area.removeClass('nbd-rounded');
                overlay_area.removeClass('nbd-rounded');
                bleed_area.removeClass('nbd-rounded');
                safe_zone.removeClass('nbd-rounded');
        }
    },
    selectSettingMedia: function(e){
        var file_frame, 
            self = jQuery(e);
        if ( file_frame ) {
            file_frame.open();
            return;
        }      
        file_frame = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image',
            button: {
                text: 'Choose Image'
            },
            library: {
                    type: [ 'image' ]
            },
            multiple: false
        }); 
        file_frame.on( 'select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            var wrap = self.closest('.nbd-media-wrap');
            wrap.find('input.nbd-media-value').val(attachment.id);
            wrap.find('img.nbd-media-img').attr('src', attachment.url);
            wrap.find('.nbd-reset-media').removeClass('nbdesigner-disable');
        });
        file_frame.open();        
    },
    resetSettingMedia: function(e){
        var self = jQuery(e),
        wrap = self.closest('.nbd-media-wrap');
        wrap.find('input.nbd-media-value').val('');
        wrap.find('img.nbd-media-img').attr('src', '');
        wrap.find('.nbd-reset-media').addClass('nbdesigner-disable');
    },
    show_option_variation: function( e ){
        e.preventDefault();
        jQuery('#nbd-thickbox-setting').show();
    },
    download_dokan_product_pdfs: function(e){
        var nbd_item_key = jQuery(e).attr('nbd_item_key');
        jQuery.ajax({
            url: nbds_frontend.url,
            method: "POST",
            data: {
                action   :    'download_dokan_product_pdfs',
                nbd_item_key :   nbd_item_key,
                nonce: nbds_frontend.nonce
            }            
        }).done(function(data){
            
        })      
    }
};