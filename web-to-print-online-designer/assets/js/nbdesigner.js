jQuery(document).ready(function () {
    if( navigator.vendor && navigator.vendor.indexOf('Apple') > -1 && navigator.userAgent.indexOf("Safari") != -1 && navigator.userAgent.indexOf("CriOS") == -1 ){
        jQuery('#nbd-chat-app').addClass('safari');
    }
/*    jQuery("head").append("<style type='text/css'>.variation-qty_break_minh { display: none; }</style>");
    if (jQuery('body').hasClass('woocommerce-cart') && jQuery('dl.variation .variation-qty_break_minh').length>0) {
        jQuery('.shop_table tbody tr.cart_item').each(function(index, el) {
            if (jQuery(this).find('dd.variation-qty_break_minh p').length>0) {
                try {
                    var bk = jQuery(this).find('dd.variation-qty_break_minh p').text();
                    if (bk!='') {
                        var res = bk.split(',');
                        if (res.length>1) {
                            jQuery(this).find('input.qty').attr({
                                'min': res[0],
                                'max': res[res.length-1],
                                'data-index': res.indexOf(jQuery(this).find('input.qty').val()),
                                'data-sum': res.length
                            }).css({'pointer-events':'none', 'opacity':'0.5'});
                            jQuery(this).find('.nb-quantity .qty-buttons').after(jQuery(this).find('.nb-quantity .qty-buttons').clone().addClass('qty-buttons-minh'));
                        }
                    }
                } catch(err) {
                  console.log(err.message);
              }
          }
      });
    }
    jQuery('.qty-buttons-minh .quantity-plus').click(function(event) {
        event.preventDefault();
        var quantity_minh = jQuery(this).parent('.qty-buttons-minh').siblings('input.qty');
        var qty = quantity_minh.val();
        var max = quantity_minh.attr('data-sum');
        var index = parseInt(quantity_minh.attr('data-index'))+1;
        if (index<parseInt(max)) {
            try {
                var bk = quantity_minh.parents('tr.cart_item').find('dd.variation-qty_break_minh p').text();
                if (bk!='') {
                    var res = bk.split(',');
                    if (res.length>1) {
                        quantity_minh.val(res[index]).trigger( 'change' );
                        quantity_minh.attr({
                            'data-index': index
                        });
                    }
                }
            } catch(err) {
              console.log(err.message);
          }
      }
    });
    jQuery('.qty-buttons-minh .quantity-minus').click(function(event) {
        event.preventDefault();
        var quantity_minh = jQuery(this).parent('.qty-buttons-minh').siblings('input.qty');
        var qty = quantity_minh.val();
        var index = parseInt(quantity_minh.attr('data-index'))-1;
        if (index>=0) {
            try {
                var bk = quantity_minh.parents('tr.cart_item').find('dd.variation-qty_break_minh p').text();
                if (bk!='') {
                    var res = bk.split(',');
                    if (res.length>1) {
                        quantity_minh.val(res[index]).trigger( 'change' );
                        quantity_minh.attr({
                            'data-index': index
                        });
                    }
                }
            } catch(err) {
              console.log(err.message);
            }
        }
    });
	*/
    if( ( jQuery('#triggerDesign').length > 0 || jQuery('#startDesign').length > 0 || jQuery('#startUpload').length > 0 ) && nbds_frontend.hide_cart_button == 'yes'){
        jQuery('button[type="submit"].single_add_to_cart_button').hide();
    };
    jQuery('#startUpload').on('click', function () {
        jQuery('body, html').addClass('nbd-prevent-scroll');
        jQuery('#container-online-designer').addClass('is-visible');
        jQuery('.nbd-popup-wrap').addClass('is-hidden');
        jQuery('#nbd-m-upload-design-wrap').addClass('is-visible');
    });
    jQuery('#startDesign').on('click', function () {
        jQuery('body, html').addClass('nbd-prevent-scroll');
        jQuery('#container-online-designer').addClass('is-visible');
        jQuery('.nbd-popup-wrap').addClass('is-hidden');
        if( !nbd_append_iframe ){
            var iframe_src = jQuery('#container-online-designer').attr('data-iframe');
            if( jQuery('form input[name="variation_id"]').length ){
                iframe_src = addParameter(iframe_src, 'variation_id', jQuery('form input[name="variation_id"]').val(), false);
            }
            jQuery('#nbd-m-custom-design-wrap').prepend('<iframe id="onlinedesigner-designer"  width="100%" height="100%" scrolling="no" frameborder="0" noresize="noresize" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" src="'+iframe_src+'"></iframe>');
            nbd_append_iframe = true;
        }
        jQuery('#nbd-m-custom-design-wrap').addClass('is-visible');
    });
    var width = jQuery(window).innerWidth();
    var height = jQuery(window).height();
    var w = -width;
    var h = -height;
    var nbd_append_iframe = false,
        enableAdvancedUpload = false;
    var showDesignFrame = function(){
        jQuery('body, html').addClass('nbd-prevent-scroll');
        if( nbd_layout == 'c' ){
            if( !nbd_append_iframe ){
                var iframe_src = jQuery('#container-online-designer').attr('data-iframe');
                if( jQuery('form input[name="variation_id"]').length ){
                    iframe_src = addParameter(iframe_src, 'variation_id', jQuery('form input[name="variation_id"]').val(), false);
                }
                jQuery('#nbd-custom-design-wrap').prepend('<iframe id="onlinedesigner-designer"  width="100%" height="100%" scrolling="no" frameborder="0" noresize="noresize" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" src="'+iframe_src+'"></iframe>');
                nbd_append_iframe = true;
            }
        }else{
            if(is_nbd_upload_without_design){
                jQuery('#nbd-m-upload-design-wrap').addClass('is-visible');
                if( nbu_advanced_upload == '1' ){
                    enableAdvancedUpload = true;
                }
            }else if( is_nbd_upload == 0 && use_our_template == 0 && hire_us_design_for_you == 0 ){
                if( !nbd_append_iframe ){
                    var iframe_src = jQuery('#container-online-designer').attr('data-iframe');
                    if( jQuery('form input[name="variation_id"]').length ){
                        iframe_src = addParameter(iframe_src, 'variation_id', jQuery('form input[name="variation_id"]').val(), false);
                    }
                    jQuery('#nbd-m-custom-design-wrap').prepend('<iframe id="onlinedesigner-designer"  width="100%" height="100%" scrolling="no" frameborder="0" noresize="noresize" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" src="'+iframe_src+'"></iframe>');        
                    nbd_append_iframe = true;
                }
                jQuery('#nbd-m-custom-design-wrap').addClass('is-visible');
            }
        };
        jQuery('#container-online-designer').addClass('is-visible');
    };
    jQuery('#triggerDesign, #startDesign').on('click', function () {
        if(jQuery(this).hasClass('nbdesigner_disable')){
            alert(nbds_frontend.check_invalid_fields);
        }else{
            showDesignFrame();
            var frame = document.getElementById('onlinedesigner-designer');
            if( frame ){
                frame.contentWindow.postMessage('change_nbo_options', window.location.origin);
            }
        }
    });
    jQuery('#closeFrameDesign').on('click', function () {
        if( enableAdvancedUpload ){
            jQuery(document).triggerHandler('close_advanced_upload_popup');
            return;
        }
        hideDesignFrame();
    });
    jQuery('#nbd__content__overlay').on('click', function (event) {
       if(event.target.id == 'nbd__content__overlay'){
           hideDesignFrame();
       }
    });
    jQuery('#open_m-custom-design-wrap').on('click', function (event) {
        jQuery('.nbd-popup-wrap').addClass('is-hidden');
        if( !nbd_append_iframe ){
            var iframe_src = jQuery('#container-online-designer').attr('data-iframe');
            if( jQuery('form input[name="variation_id"]').length ){
                iframe_src = addParameter(iframe_src, 'variation_id', jQuery('form input[name="variation_id"]').val(), false);
            }
            jQuery('#nbd-m-custom-design-wrap').prepend('<iframe id="onlinedesigner-designer"  width="100%" height="100%" scrolling="no" frameborder="0" noresize="noresize" allowfullscreen mozallowfullscreen="true" webkitallowfullscreen="true" src="'+iframe_src+'"></iframe>');        
            nbd_append_iframe = true;
        }
        jQuery('#nbd-m-custom-design-wrap').addClass('is-visible');
    });
    jQuery('#open_m-upload-design-wrap').on('click', function (event) {
        jQuery('.nbd-popup-wrap').addClass('is-hidden');
        jQuery('#nbd-m-upload-design-wrap').addClass('is-visible');
        if( nbu_advanced_upload == '1' ){
            enableAdvancedUpload = true;
        }
    });
    backtoOption = function(){
        jQuery('#nbd-m-upload-design-wrap').removeClass('is-visible');
        jQuery('#nbd-m-custom-design-wrap').removeClass('is-visible');
        jQuery('.nbd-popup-wrap').removeClass('is-hidden');
    };
    hideDesignFrame = function (mes) {
        jQuery('body, html').removeClass('nbd-prevent-scroll');
        jQuery('#container-online-designer').removeClass('is-visible');
        backtoOption();
        if (mes != null) {
            setTimeout(function () {
                alert(mes);
            }, 700);
        }
        enableAdvancedUpload = false;
        NBDESIGNERPRODUCT.scrollToAddToCart();
    };
    hideUploadFrame = function(){
        if( jQuery('.upload-design-preview img').length > 0 ){
            if( nbds_frontend.auto_add_cart == 'yes' && nbds_frontend.page_design_tool == 1 && nbds_frontend.edit_option_mode != 1 ){
                var product_id = jQuery('[name="nbd-add-to-cart"]').attr('value');
                jQuery('.variations_form, form.cart').append('<input name="add-to-cart" type="hidden" value="' + product_id + '" />');
                jQuery('.variations_form, form.cart').append('<input name="nbd-auto-add-to-cart-in-detail-page" type="hidden" value="1" />');
                jQuery('.variations_form, form.cart').submit();
                return;
            }
        }
        hideDesignFrame();
    };
    show_upload_thumb = function( arr ){
        if( arr.length ){
            jQuery('#nbdesigner-upload-title').show();
        }else{
            jQuery('#nbdesigner-upload-title').hide();
        };    
        var html = '';
        var d = new Date();
        jQuery.each(arr, function (key, val) {
            html += '<div class="img-con" style=\"display: inline-block; margin-right: 15px;\"><img src="' + val.src + '?t=' + d.getTime() +'" style=\"width: 100px;\"/><p class="nbd-file-title">'+val.name+'</p></div>'
        });
        jQuery('#nbdesigner_upload_preview').html('').append(html);
    };
    jQuery('#nbdesign-new-template').on('click', function(){
        showDesignFrame();
    });
    jQuery(window).on('resize', function () {
        var width = jQuery(window).width(),
                height = jQuery(window).height();
        //jQuery('#container-online-designer').css({'height': height});
    });  
    jQuery('.nbd-order-download-pdf-action').each(function(index) {
        var download_pdf = jQuery(this),
            loading_src = download_pdf.attr('data-loading-img'),
            download_action = download_pdf.attr('data-action');
        download_pdf.prepend('<span class="nbd-loading"><img class="nbd-pdf-loading hide" src="'+loading_src+'"/></span>');
        download_pdf.on('click', function(e){
            download_action == 'download_pdf_in_order' ? NBDESIGNERPRODUCT.download_pdf_in_order(e, jQuery(this)) : NBDESIGNERPRODUCT.nbd_download_final_pdf(e, jQuery(this));
        });
    });
    jQuery('.nbd-pdf-options').show();
    jQuery.each(jQuery('.nbd-order-item-download-section'), function( index, value ){
        var self = jQuery(this),
            link = self.attr('data-href'),
            download_title = self.attr('data-title'),
            type = JSON.parse( self.attr('data-type') ),
            type_len = 0
            op_el = '<select onchange="NBDESIGNERPRODUCT.change_nbd_download_type( this )">';
        jQuery.each(type, function( index, value ){
            op_el += '<option value="'+index+'">'+value+'</option>';
            type_len++;
        });
        op_el += '</select>';
        if( type_len ) {
            self.append(op_el);
            self.append('<a class="button nbd-order-download-file" href="'+link+'&type='+Object.keys(type)[0]+'">'+download_title+'</a>');
            self.show()
        };
    });
    /* Drag & Drop uplod file */
    var nbdDropArea = jQuery('label[for="nbd-file-upload"]'),
    nbdInput = jQuery('#nbd-file-upload');
    var listFileUpload = [];
    ['dragenter', 'dragover'].forEach(function(eventName){
        nbdDropArea.on(eventName, highlight)
    });
    ['dragleave', 'drop'].forEach(function(eventName){
        nbdDropArea.on(eventName, unhighlight)
    });
    function highlight(e) {
        e.preventDefault();
        e.stopPropagation();
        nbdDropArea.addClass('highlight');
    };
    function unhighlight(e) {
        e.preventDefault();
        e.stopPropagation();
        nbdDropArea.removeClass('highlight');
    };
    nbdDropArea.on('drop', handleDrop);
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
    nbdInput.on('click', function(e){
        e.stopPropagation();
    });
    nbdInput.on('change', function(){
        handleFiles(this.files);
    });
    function resetUploadInput(){
        nbdInput.val('');
    }
    function handleFiles(files) {
        if(files.length > 0) uploadFile(files);
    }
    function uploadFile(files){
        var file = files[0],
        type = file.type.toLowerCase();
        if( listFileUpload.length > (nbd_number-1) ) {
            alert('Exceed number of upload files!');
            return;
        }
        if( type == '' ){
            type = file.name.substring(file.name.lastIndexOf('.')+1).toLowerCase();
        }
        type = type == 'image/jpeg' ? 'image/jpg' : type;
        if( nbd_disallow_type != '' ){
            var nbd_disallow_type_arr = nbd_disallow_type.toLowerCase().split(',');
            var check = false;
            nbd_disallow_type_arr.forEach(function(value){
                value = value == 'jpeg' ? 'jpg' : value;
                if( type.indexOf(value) > -1 ){
                    check = true;
                }
            });
            if( check ){
                resetUploadInput();
                alert('Disallow extensions: ' + nbd_disallow_type);
                return;
            }
        }
        if( nbd_allow_type != '' ){
            var nbd_allow_type_arr = nbd_allow_type.toLowerCase().split(',');
            var check = false;
            nbd_allow_type_arr.forEach(function(value){
                value = value == 'jpeg' ? 'jpg' : value;
                if( type.indexOf(value) > -1 ){
                    check = true;
                }
            });   
            if( !check ){
                resetUploadInput();
                alert('Only support: ' + nbd_allow_type);
                return;
            }
        }
        if (file.size > nbd_maxsize * 1024 * 1024 ) {
            alert('Max file size' + nbd_maxsize + " MB");
            resetUploadInput();
            return;
        }else if(file.size < nbd_minsize * 1024 * 1024){
            alert('Min file size' + nbd_minsize + " MB");
            resetUploadInput();
            return;
        };
        var formData = new FormData;
        formData.append('file', file);
        jQuery('.nbd-upload-loading').addClass('is-visible');
        jQuery('.upload-zone label').addClass('is-loading');
        jQuery('.nbd-m-upload-design-wrap').addClass('is-loading');
        var first_time = listFileUpload.length > 0 ? 2 : 1;
        var product_id = jQuery('[name="nbd-add-to-cart"]').attr('value');
        var variation_id = jQuery('[name="variation_id"]').length > 0 ? jQuery('[name="variation_id"]').attr('value') : 0;
        formData.append('first_time', first_time);
        formData.append('action', 'nbd_upload_design_file');
        formData.append('task', 'new');
        formData.append('product_id', product_id);
        formData.append('variation_id', variation_id);
        formData.append('nonce', nbds_frontend.nonce);
        jQuery.ajax({
            url: nbds_frontend.url,
            method: "POST",
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            complete: function() {
                jQuery('.nbd-upload-loading').removeClass('is-visible');
                jQuery('.upload-zone label').removeClass('is-loading');
                jQuery('.nbd-m-upload-design-wrap').removeClass('is-loading');
            },
            success: function(data) {
                if( data.flag == 1 ){
                    listFileUpload.push( { src : data.src, name : data.name } );
                    buildPreviewUpload();
                }else{
                    alert(data.mes);
                }
                resetUploadInput();
            }
        });
    }
    window.removeUploadFile = function(index){
        listFileUpload.splice(index, 1);
        resetUploadInput();
        buildPreviewUpload();
    };
    function buildPreviewUpload(){
        show_upload_thumb(listFileUpload);
        NBDESIGNERPRODUCT.update_nbu_value(listFileUpload);
        jQuery('input[name="nbu-ignore-update"]').remove();
        jQuery('input[name="nbu-reserve"]').remove();
        var html = '';
        listFileUpload.forEach(function(file, index){
            html += '<div class="nbd-upload-items"><div class="nbd-upload-items-inner"><img src="'+file.src+'" class="shadow nbd-upload-item"/><p class="nbd-upload-item-title">'+file.name+'</p><span class="shadow" onclick="removeUploadFile('+index+')" >&times;</span></div></div>';
        });
        jQuery('.upload-design-preview').html(html);
        if( nbds_frontend.hide_cart_button == 'yes' ) jQuery('button[type="submit"].single_add_to_cart_button').show();
    }
    NBDESIGNERPRODUCT.nbdesigner_ready();
    jQuery('form input[name="variation_id"]').on('change', function(){
        var _this = jQuery(this);
        if( _this.val() > 0 && _this.parents('form').find('#triggerDesign').length ){
            NBDESIGNERPRODUCT.nbdesigner_ready();
        }else{
            _this.parents('form').find('#triggerDesign').addClass('nbdesigner_disable');
        }
    });
    jQuery.fn.showAlert = function () {
        return this.each(function () {
            var sefl = this;
            var $close = jQuery(this).find('.overlay-popup, .close-popup');
            jQuery('body').addClass('nbd-prevent-scroll');
            if (!jQuery(this).hasClass('nb-show')) {
                jQuery(this).addClass('nb-show');
            }
            $close.on('click', function () {
                jQuery(sefl).removeClass('nb-show');
                jQuery('body').removeClass('nbd-prevent-scroll');
            });
        });
    };
    jQuery.fn.showNBDPopup = function () {
        return this.each(function () {
            var sefl = this;
            jQuery('body').addClass('nbd-prevent-scroll');
            if (!jQuery(this).hasClass('nb-show')) {
                jQuery(this).addClass('nb-show');
            }
            jQuery(this).on('click', function (event) {
                var target = jQuery( event.target );
                if( target.is( jQuery(sefl).find( '.popup-inner' ) ) ){
                    jQuery(sefl).removeClass('nb-show');
                    jQuery('body').removeClass('nbd-prevent-scroll');
                }
            });
        });
    };
    jQuery.fn.nbqShowPopup = function () {
        return this.each(function () {
            var sefl = jQuery(this),
            $close = jQuery(this).find('.overlay-popup, .close-popup'),
            main_pop = jQuery(this).find('.main-popup');
            
            if (!jQuery(this).hasClass('nb-show')) {
                jQuery(this).addClass('nb-show');
            }
            sefl.css({
                height: jQuery(document).height(),
                width: jQuery(window).width()
            });
            main_pop.css({
                top: jQuery(document).scrollTop() + 100,
                left: (jQuery(window).width() - main_pop.width()) / 2
            });
            $close.on('click', function () {
                sefl.removeClass('nb-show').css({height: 'auto'});
                main_pop.css({top: 'auto'});
            });
        });
    };
    var _nbd_stored_design = false, _nbd_prevent_ajax = false;
    function nbd_form_submit(e, container){
        var wrapper = ( typeof container != 'undefined' ) ? container + ' ' : '';
        if( window.preventSubmitFormCart && !_nbd_stored_design ){
            e.preventDefault();
            jQuery(jQuery(this).parents('form')).addClass( 'processing' ).block( {
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            }); 
            var scope = angular.element(document.getElementById("designer-controller")).scope();
            scope.saveData();
            return false;
        }else if( nbds_frontend.ajax_cart == 'yes' && !_nbd_prevent_ajax ){
            e.preventDefault();
            var formData =  new FormData();
            cartForm = jQuery(wrapper + 'form.cart, ' + wrapper + 'form.variations_form'),
            cartFormData = cartForm.serializeArray();
            jQuery.each(cartFormData, function (i, val) {
                if (val.name) {
                    var new_name = val.name == 'add-to-cart' ? 'nbd-add-to-cart' : val.name;
                    formData.append(new_name, val.value);
                }
            });
            if( typeof cartFormData['nbd-add-to-cart'] == 'undefined' ){
                formData.append('nbd-add-to-cart', cartForm.find('[name="add-to-cart"]').val());
            }
            jQuery.each(cartForm.find("input[type='file']"), function (i, tag) {
                jQuery.each(jQuery(tag)[0].files, function (i, file) {
                    formData.append(tag.name, file);
                });
            });
            nbo_ajax_cart( formData, function(){
                cartForm.addClass( 'processing' ).block( {
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            }, function(){
                cartForm.removeClass( 'processing' ).unblock();
            } );
            return false;
        }
        return true;
    };
    function nbo_ajax_cart( form, beFunc, comFunc ){
        jQuery.ajax({
            url: nbds_frontend.url + '?action=nbo_ajax_cart',
            type: 'POST',
            dataType: 'json',
            data: form,
            contentType: false,
            processData: false,
            beforeSend: function () {
                if(typeof beFunc == 'function') beFunc();
            },
            complete: function () {
                if(typeof comFunc == 'function') comFunc();
            },
            success: function (response) {
                if ('success' === response.result) {
                    if( typeof response.redirect != 'undefined' ){
                        window.location.href = response.redirect;
                    }else{
                        jQuery('#nbd-ajax-cart-alert-content').html(response.messages);
                        if( response.result == 'success' ){
                            jQuery('#nbd-ajax-cart-alert-title .failure').hide();
                        }else{
                            jQuery('#nbd-ajax-cart-alert-title .success').hide();
                        }
                        if( jQuery('#nbo-quick-view-popup').length ){
                            jQuery('#nbo-quick-view-popup').find('.close-popup').triggerHandler('click');
                        }
                        jQuery('#nbd-ajax-cart-alert').showAlert();
                        if( response.data.fragments ){
                            var fragments = response.data.fragments;
                            jQuery.each(fragments, function (key) {
                                jQuery(key).addClass('updating').fadeTo('400', '0.6').block({
                                    message: null,
                                    overlayCSS: {
                                        opacity: 0.6
                                    }
                                });
                            });
                            jQuery.each(fragments, function (key, value) {
                                jQuery(key).replaceWith(value);
                                jQuery(key).stop(true).css('opacity', '1').unblock();
                            });
                            if( nbds_frontend.show_favicon_badge == 'yes' && nbds_frontend.is_mobile != 1 ){
                                try{
                                    var nbd_favicon = nbd_favicon || new Favico({
                                        animation:'slide'
                                    });
                                    if ( fragments && fragments['nbo_cart_item_count'] ){
                                        var badge = parseInt( fragments['nbo_cart_item_count'] );
                                        nbd_favicon.badge(badge);
                                    }
                                } catch( err ) {
                                    console.log( err );
                                }
                            }
                        }
                    }
                }
                if ('failure' === response.result) {
                    alert( response.messages );
                }
            }
        });
    };
    function nbo_quick_view( product_id, beFunc, comFunc ){
        jQuery.ajax({
            url: nbds_frontend.nbo_qv_url + '&product=' + product_id,
            type: 'GET',
            beforeSend: function () {
                if(typeof beFunc == 'function') beFunc();
            },
            complete: function () {
                if(typeof comFunc == 'function') comFunc();
            },
            success: function (response) {
                if(response){
                    jQuery('#nbo-quick-view-popup .nbdq-popup-body').html(response);
                    jQuery(document).triggerHandler( 'quick-view-displayed' );
                    jQuery('#nbo-quick-view-popup form .single_add_to_cart_button').on('click', function(e){
                        nbd_form_submit( e, '#nbo-quick-view-popup' );
                    });
                    setTimeout(function(){
                        if(jQuery('#nbo-quick-view-popup .variations_form').length){
                            jQuery('#nbo-quick-view-popup .variations_form').wc_variation_form();
                            jQuery('#nbo-quick-view-popup .variations_form').trigger( 'wc_variation_form' );
                            jQuery('#nbo-quick-view-popup .variations_form .variations select').change();
                        }
                        jQuery('#nbo-quick-view-popup').nbqShowPopup();
                    });
                }
            }
        });
    };
    jQuery('form .single_add_to_cart_button').on('click', function(e){
        nbd_form_submit( e );
    });
    jQuery('.variations_form, form.cart').on('submit', function(e){
        nbd_form_submit( e );
    });
    jQuery(document).on('nbd_design_stored', function( e, data ){
        _nbd_stored_design = true;
        if( data._type != 'quote' ){
            if( typeof data.prevent_ajax != 'undefined' ){
                _nbd_prevent_ajax = true;
            }
            jQuery('.variations_form, form.cart').submit();
        }
    });
    NBDESIGNERPRODUCT.qty = jQuery('input[name="quantity"]').val();
    jQuery('input[name="quantity"]').on('change', function(){
        NBDESIGNERPRODUCT.qty = jQuery('input[name="quantity"]').val();
        var frame = document.getElementById('onlinedesigner-designer');
        if( frame ){
            //frame.contentWindow.postMessage('change_nbd_qty', window.location.origin);
        }
    });
    jQuery('.nbo_ajax_add_to_cart').on('click', function(e){
        e.preventDefault();
        var self = jQuery(this),
        product_id = self.attr('data-product_id');
        if( self.hasClass('nbo_need_qv') ){
            nbo_quick_view( product_id, function(){
                self.addClass( 'processing' ).block( {
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            }, function(){
                self.removeClass( 'processing' ).unblock();
            } );
        }else{
            var formData =  new FormData();
            formData.append('nbd-add-to-cart', product_id);
            nbo_ajax_cart( formData, function(){
                self.addClass( 'processing' ).block( {
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            }, function(){
                self.removeClass( 'processing' ).unblock();
            } );
        }
    });
    jQuery('li.nbd-catalog-option img').on('click', function(e){
        e.preventDefault();
        var pid = jQuery(this).parents('.nbd-catalog-option').find('a[data-product_id]').first().attr('data-product_id'),
        permalink = jQuery(this).parents('.nbd-catalog-option').find('a.woocommerce-LoopProduct-link').first().attr('href');
        jQuery('.nbco-option-design-link').attr( 'href', nbds_frontend.create_design_url + '?product_id=' + pid );
        jQuery('.nbco-option-hire-link').attr( 'href', permalink + 'request-design' );
        jQuery('.nbco-option-upload-link').attr( 'href', permalink + 'upload-design' );
        jQuery('#nbd-catalog-option-popup').nbqShowPopup();
    });
    jQuery('.nbo-swatch, .nbo-swatch-label-wrap').on('mouseenter',function(){
        var self = jQuery(this),
        srcset = self.attr('data-srcset'),
        src = self.attr('data-src'),
        wrap = self.parents('.nbo-archive-swatches-wrap');
        if( typeof srcset != 'undefined' && srcset !== false ){
            var image =  self.parents('li.product').find('.woocommerce-LoopProduct-link > img');
            var wrap_src = wrap.attr('data-origin-src');
            if( typeof wrap_src == 'undefined' || wrap_src == false ){
                wrap.attr('data-origin-src', image.attr('src'));
                wrap.attr('data-origin-srcset', image.attr('srcset'));
            }
            image.attr('src', src );
            image.attr( 'data-lazy-srcset', src );
            image.attr( 'srcset', srcset );
            image.attr( 'data-lazy-srcset', srcset );
        }
    });
    jQuery('.nbo-archive-swatches-wrap').on('mouseleave',function(){
        var self = jQuery(this),
        srcset = self.attr('data-origin-srcset'),
        src = self.attr('data-origin-src');
        if( typeof srcset != 'undefined' && srcset !== false ){
            var image =  self.parents('li.product').find('.woocommerce-LoopProduct-link > img');
            image.attr('src', src );
            image.attr( 'data-lazy-srcset', src );
            image.attr( 'srcset', srcset );
            image.attr( 'data-lazy-srcset', srcset );
        }
    });
    jQuery.fn.nbdsSlider = function(){
        return this.each(function () {
            var $sefl   = jQuery(this),
            slider      = $sefl.find('.studio-product-templates-slider'),
            totalWidth  = $sefl.find('.template-item').length * 130,
            wrapWidth   = $sefl.width();
            if( wrapWidth > totalWidth ){
                $sefl.find('.nav').remove();
            }
            $sefl.find('.next').on('click', function(){
                var active = parseInt( $sefl.attr('data-active') );
                if( ( wrapWidth * ( active + 1 )  ) < totalWidth ){
                    slider.css({
                        transform: 'translateX(-' + ( wrapWidth * ( active + 1 ) ) + 'px)'
                    });
                    $sefl.attr('data-active', active + 1);
                    $sefl.find('.prev').removeClass('nbds-disable');
                    if( ( wrapWidth * ( active + 2 )  ) > totalWidth ){
                        jQuery(this).addClass('nbds-disable');
                    }
                }else{
                    jQuery(this).addClass('nbds-disable');
                }
            });
            $sefl.find('.prev').on('click', function(){
                var active = parseInt( $sefl.attr('data-active') );
                if( active > 0 ){
                    slider.css({
                        transform: 'translateX(-' + ( wrapWidth * ( active - 1 ) ) + 'px)'
                    });
                    $sefl.attr('data-active', active - 1);
                    $sefl.find('.next').removeClass('nbds-disable');
                    if( active == 1 ){
                        jQuery(this).addClass('nbds-disable');
                    }
                }else{
                    jQuery(this).addClass('nbds-disable');
                }
            });
        });
    };
    jQuery('.studio-product-wrap .product-img').on('click', function(){
        var self        = jQuery( this ),
        product_el      = self.parents('.studio-product'),
        templates_wrap  = product_el.find('.studio-product-templates'),
        templates_inner = templates_wrap.find('.studio-product-templates-inner'),
        product_id      = product_el.attr('data-id'),
        wraper_el       = product_el.parent( '.studio-product-wrap' );
        wraper_el.find('.studio-product').removeClass( 'open-collapse' );
        if( product_el.attr( 'data-collapse' ) == '1' ){
            product_el.attr( 'data-collapse', '0' );
        }else{
            product_el.addClass( 'open-collapse' );
            wraper_el.find('.studio-product').attr( 'data-collapse', '0' );
            product_el.attr( 'data-collapse', '1' );
            function show_template(){
                templates_wrap.css({
                   'transform': 'translateX(-' + ( product_el.offset().left + 16 ) + 'px)'
                });
                templates_inner.css({
                    'width': wraper_el.width() + 'px'
                });
                jQuery('html, body').animate({
                    scrollTop: templates_wrap.offset().top - 200
                }, 300);
            }
            function render_templates( templates ){
                var has_template = false;
                jQuery.each(templates, function( index, template ){
                    var temp_html   = '<div class="template-item">';
                    temp_html      +=   '<a href="' + template.url + '"><img src="' + template.preview + '"/></a>';
                    temp_html      += '</div>';
                    templates_inner.find('.studio-product-templates-slider').append( temp_html );
                    has_template    = true;
                    templates_inner.find('.no-template').remove();
                });
                templates_wrap.find('.loading-wrap').removeClass('show');
                setTimeout(function(){
                    if( has_template ){
                        jQuery('.nav').show();
                        templates_inner.nbdsSlider();
                    }else{
                        templates_inner.find('.no-template').show();
                    }
                }, 300);
            }
            function get_template( callback ){
                jQuery.ajax({
                    url: nbds_frontend.url,
                    method: "POST",
                    data: {
                        action      : 'nbds_get_product_template',
                        product_id  : product_id,
                        nonce       : nbds_frontend.nonce
                    }            
                }).done(function(data){
                    if( data.templates ){
                        product_el.attr('data-template', '1');
                        render_templates( data.templates );
                    }
                    if( typeof callback == 'function' ){
                        callback();
                    }
                });
            }
            if( product_el.attr('data-template') == '0' ){
                templates_wrap.find('.loading-wrap').addClass('show');
                get_template();
            }
            show_template();
        }
    });
    jQuery('.studio-widget-img-wrap .product-img').on('click', function(){
        var self        = jQuery( this ),
        product_el      = self.parents('.studio-widget-product'),
        product_id      = product_el.attr('data-id'),
        templates_wrap  = self.parents('.studio-widget-product-wrap').next('.studio-widget-templates-wrap'),
        templates_inner = templates_wrap.find('.studio-widget-templates');
        templates_inner.html('');
        templates_wrap.show();
        templates_wrap.find('.loading-wrap').addClass('show');
        function get_template( callback ){
            jQuery.ajax({
                url: nbds_frontend.url,
                method: "POST",
                data: {
                    action      : 'nbds_get_product_template',
                    product_id  : product_id,
                    nonce       : nbds_frontend.nonce
                }            
            }).done(function(data){
                if( data.templates ){
                    templates_wrap.find('.loading-wrap').removeClass('show');
                    if( data.templates.length == 0 ){
                        templates_wrap.find('.no-template').show();
                    }else{
                        templates_wrap.find('.no-template').hide();
                    }
                    render_templates( data.templates );
                }
                if( typeof callback == 'function' ){
                    callback();
                }
            });
        }
        function render_templates( templates ){
            jQuery.each(templates, function( index, template ){
                var temp_html   = '<div class="template-item">';
                temp_html      +=   '<a href="' + template.url + '"><img src="' + template.preview + '"/></a>';
                temp_html      += '</div>';
                templates_inner.append( temp_html );
            });
        }
        get_template();
    });
    jQuery( document).on('click', '#nbrq_checkout_quote_btn', function(e){
        jQuery('#nbrq_checkout_quote').val('1');
    });
    jQuery('.nbd-tag-list-item, .nbd-color-list-item-inner').on( 'click', function( e ){
        var val = jQuery(this).data('value') + '',
        type = jQuery(this).data('type'),
        url = new URL(window.location.href),
        current_val = url.searchParams.get( type );
        if( current_val !== null ){
            current_val_arr = current_val.split(',');
            if( current_val_arr.includes( val ) ){
                val = current_val;
            }else{
                val = current_val + ',' + val;
            }
        }
        var link = addParameter( window.location.href, type, val, false );
        link = removeParam( 'paged', link );
        window.location = link;
        e.preventDefault();
    });
    jQuery('.nbd-filter-tag-remove').on( 'click', function( e ){
        type = jQuery(this).data('type'),
        val = jQuery(this).data('value') + '',
        url = new URL(window.location.href),
        current_val = url.searchParams.get( type );
        if( current_val !== null ){
            current_val_arr = current_val.split(',');
            var paramIndex = current_val_arr.indexOf( val );
            if( paramIndex > -1 ){
                current_val_arr.splice(paramIndex, 1);
                val = current_val_arr.join(',');
            }else{
                val = current_val;
            }
        }
        var link;
        if( val == '' || type == 'search' ){
            link = removeParam( type, window.location.href );
            if( type == 'search' ){
                link = removeParam( 'search_type', link );
            }
        }else{
            link = addParameter( window.location.href, type, val, false );
        }
        link = removeParam( 'paged', link );
        window.location = link;
        e.preventDefault();
    });
    jQuery('.nbd-gallery-filter-clear').on( 'click', function( e ){
        var link = removeParam( 'tag', window.location.href );
        link = removeParam( 'color', link );
        link = removeParam( 'paged', link );
        link = removeParam( 'search', link );
        link = removeParam( 'search_type', link );
        window.location = link;
        e.preventDefault();
    });
    /* NBOO popup */
    jQuery('.nbu_arc_trigger_pop').on('click', function (e) {
        e.preventDefault();
        jQuery('body, html').addClass('nboo-prevent-scroll');
        jQuery('#nboo-option-popup').addClass('is-visible');
        var product_id = jQuery(this).attr('data-product_id'),
        product_name = jQuery(this).parents('.product').find( '.woocommerce-loop-product__title').text(),
        layout = jQuery(this).attr('data-layout'),
        upload_url = jQuery(this).hasClass('nbau') ? nbau_upload_url : nboo_upload_url,
        linkStartDesign = addParameter( nboo_design_url, 'product_id', product_id ),
        linkStartUpload = addParameter( upload_url, 'product_id', product_id );
        if( layout == 'v' ){
            linkStartDesign = jQuery(this).attr('href');
        };
        jQuery('#nboo-product-name').text( product_name );
        jQuery( '#nboo-open_m-custom-design-wrap' ).attr('data-url', linkStartDesign);
        jQuery( '#nboo-open_m-upload-design-wrap' ).attr('data-url', linkStartUpload);
    });
    jQuery('#nboo-option-popup').on('click', function (event) {
        if(event.target.id == 'nboo-option-popup-overlay'){
            jQuery('body, html').removeClass('nboo-prevent-scroll');
            jQuery('#nboo-option-popup').removeClass('is-visible');
        }
    });
    jQuery('#nboo-open_m-custom-design-wrap, #nboo-open_m-upload-design-wrap').on('click', function (event) {
        window.location = jQuery(this).attr('data-url');
    });
    /* End. NBOO popup */

    jQuery.fn.nbd_template = function ( template, obj ) {
        var $template_html = template( obj );

        $template_html = $template_html.replace( '/*<![CDATA[*/', '' );
        $template_html = $template_html.replace( '/*]]>*/', '' );

        return $template_html;
    }

    if( typeof wp == 'object' && typeof wp.template == 'function' && jQuery('script[id="tmpl-nbdl-search-bar"]').length > 0 ){
        jQuery( jQuery.fn.nbd_template( wp.template( 'nbdl-search-bar' ), {} ) ).insertAfter( jQuery( ".nbd-gallery h1.entry-title" ) );
    }

    function nbdlSearchTemplate( type, value ){
        value = typeof value != 'undefined' ? value : jQuery('#nbdl-search-content').val();
        type = typeof type != 'undefined' ? type : jQuery('#nbdl-search-type li.active').attr('data-value');
        var url = new URL( window.location.href ),
        query_string = url.search,
        search_params = new URLSearchParams( query_string );
        if( value != '' ){
            search_params.set('search', value);
            if( type != '' ){
                search_params.set('search_type', type);
            }else{
                search_params.delete('search_type');
            }
        }else{
            search_params.delete('search');
            search_params.delete('search_type');
        }
        search_params.delete('paged');
        url.search = search_params.toString();
        var link = url.toString();
        window.location = link;
    }

    setTimeout(function(){
        jQuery( '.nbdl-search-type-selected, .nbdl-search-type-wrap svg' ).on('click', function(){
            jQuery('#nbdl-search-type').toggleClass('active');
        });

        jQuery('#nbdl-search-type li').on('click', function(){
            var self = jQuery(this);
            jQuery('#nbdl-search-type li').removeClass('active');
            self.addClass('active');
            jQuery('.nbdl-search-type-selected').html( self.html() );
            jQuery('#nbdl-search-type').removeClass('active');
        });

        jQuery('#nbdl-search-content').on("keyup", function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                nbdlSearchTemplate();
            }
        });

        jQuery('.nbdl-search-content-wrap svg').on('click', function(){
            nbdlSearchTemplate();
        });

        document.addEventListener("click", function(event) {
            if ( event.target.closest( '.nbdl-search-type-wrap' ) ) return;
            jQuery('#nbdl-search-type').removeClass('active');
        });
    });

    if( nbds_frontend.show_favicon_badge == 'yes' && nbds_frontend.is_mobile != 1 ){
        var nbd_favicon = nbd_favicon || new Favico({
            animation:'slide'
        });
        jQuery( document.body ).on('wc_fragments_refreshed', function(){
            try{
                var wc_fragments = jQuery.parseJSON( sessionStorage.getItem( wc_cart_fragments_params.fragment_name ) );
                if ( wc_fragments && wc_fragments['nbo_cart_item_count'] ){
                    var badge = parseInt( wc_fragments['nbo_cart_item_count'] );
                    nbd_favicon.badge(badge);
                }
            } catch( err ) {
                console.log( err );
            }
        });
    }

    jQuery('.nbd-faq-head').on('click', function(){
        jQuery(this).parent('.nbd-faq-wrap').toggleClass('active');
    });

    function nbd_remove_design(){
        jQuery('.nbd-cart-item-remove-design, .nbd-cart-item-remove-file').off('click').on('click', function(e){
            e.preventDefault();
            var type = jQuery(this).attr('data-type'),
            cart_item_key = jQuery(this).attr('data-cart-item');
            NBDESIGNERPRODUCT.remove_design(type, cart_item_key);
        });
    }
    nbd_remove_design();
    jQuery( document.body ).on( 'wc_fragments_refreshed', function(){
        nbd_remove_design();
    });

    jQuery('.woocommerce-form-login, .woocommerce-form-register').on('submit', function(){
        window.parent.jQuery( window.parent.document ).triggerHandler( 'nbd_redirect_login' );
        return true;
    });
});
(function ($, window) {
    $.fn.nbdlNav = function (options) {
        var defaults = {};
        var opts = $.extend({}, defaults, options);
        return this.each(function () {
            var $tabs = $(this).find('.nbdl-toggle'),
            $panels = $(this).find('.nbdl-toggle-panel');
            $tabs.on('click', function( e ){
                e.preventDefault();
                var targetId = $(this).attr('data-target');
                $tabs.removeClass('active');
                $panels.removeClass('active');
                $(this).addClass('active');
                $('#' + targetId).addClass('active');
            });
            $tabs.first().addClass('active');
            var firstTargetId = $tabs.first().attr('data-target');
            $('#' + firstTargetId).addClass('active');
        });
    }
    $(document).ready(function(){
        $('.nbdl-withdraw-wrap').nbdlNav();
    });
})(jQuery, window);
jQuery(document).on('quick-view-displayed', function(){
    if( jQuery('.nbd-option-in-wqv').length > 0 ){
        jQuery(".nbd-option-in-wqv").find("script").each(function(i) {
            eval(jQuery(this).text());
        });
    }
});
jQuery(document).on('reset_nbo_options', function(){
    jQuery('#nbdesigner-preview-title').hide();
    jQuery('#nbd-actions').hide();
    jQuery('#nbdesigner_frontend_area').html('');
    jQuery('#nbdesigner-upload-title').hide();
    jQuery('#nbdesigner_upload_preview').html('');
    var product_id = jQuery('[name="nbd-add-to-cart"]').attr('value');
    var variation_id = jQuery('[name="variation_id"]').length > 0 ? jQuery('[name="variation_id"]').attr('value') : 0;
    jQuery.ajax({
        url: nbds_frontend.url,
        method: "POST",
        data: {
            action   : 'nbd_remove_design_and_file',
            product_id : product_id,
            variation_id : variation_id,
            nonce: nbds_frontend.nonce
        }
    }).done(function(data){
        console.log('success!');
    });
});
jQuery(document).on('initialed_nbo_options', function(){
    jQuery('.variations_form input, form.cart input').on('keydown', function( event ){
        if( event.keyCode == 13 ) {
            event.preventDefault();
            return false;
        }
    });
});
var share_image_url = '';
var NBDESIGNERPRODUCT = {
    copy_source: function( e ){
        jQuery(e).prev('.nbd-cart-design-url').select();
        document.execCommand("copy");
    },
    hide_loading_iframe: function(){
        jQuery("#nbd_processing").hide();
    },
    update_request_design: function( _this ){
        var self = jQuery(_this);
        var field_id = self.attr('data-field'),
        cart_item_key = self.attr('data-cart-item'),
        request_val = self.is(':checked') ? self.attr('data-request') : self.attr('data-no-request');
        jQuery('form.woocommerce-cart-form').addClass( 'processing' ).block( {
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        } );
        jQuery.ajax({
            url: nbds_frontend.url,
            method: "POST",
            data: {
                action   :    'nbo_update_request_design',
                field_id :   field_id,
                cart_item_key :   cart_item_key,
                request_val :   request_val,
                nonce: nbds_frontend.nonce
            }
        }).done(function(data){
            jQuery('form.woocommerce-cart-form').removeClass( 'processing' ).unblock();
            if( data.flag == 1 ){
                jQuery( '.woocommerce-cart-form [name="update_cart"]' ).prop( 'disabled', false ).trigger( 'click' );
            }
        });
    },
    save_for_later: function(){
        jQuery('img.nbd-save-loading').removeClass('hide');
        jQuery.ajax({
            url: nbds_frontend.url,
            method: "POST",
            data: {
                action : 'nbd_save_for_later',
                product_id : NBDESIGNERPRODUCT.product_id,
                variation_id : NBDESIGNERPRODUCT.variation_id,
                folder: NBDESIGNERPRODUCT.folder,
                nonce: nbds_frontend.nonce
            }
        }).done(function(data){
            if( data.flag == 1 ){
                jQuery('img.nbd-save-loading').addClass('hide');
                jQuery('a.nbd-save-for-later').addClass('saved');
                jQuery('a.nbd-save-for-later svg').show();

                jQuery.each( jQuery('#nbd-share-group a'), function(){
                    var d = new Date();
                    var href = jQuery(this).attr('data-href');
                    var share_url = nbd_create_own_page + '?product_id=' + NBDESIGNERPRODUCT.product_id + '&variation_id=' + NBDESIGNERPRODUCT.variation_id + '&reference=' + data.folder + '&nbd_share_id=' + data.folder + '&t=' + d.getTime();
                    var _href = href + encodeURIComponent(share_url);
                    if( jQuery(this).attr('id') == 'nbd-pinterest' ) _href += '&media=' + encodeURIComponent(share_image_url) + '&description=' + jQuery(this).attr('data-description');
                    if( jQuery(this).attr('data-text') != undefined ) _href += '&text=' + jQuery(this).attr('data-text');
                    jQuery(this).attr('href', _href);
                });
                jQuery('.saved-design-link').show();
            }else{
                alert('Opps! Error while save design!');
            };
        });
    },
    download_pdf: function(){
        jQuery('img.nbd-pdf-loading').removeClass('hide');
        jQuery.ajax({
            url: nbds_frontend.url,
            method: "POST",
            data: {
                action : 'nbd_frontend_download_pdf',
                nbd_item_key : NBDESIGNERPRODUCT.folder,
                nonce: nbds_frontend.nonce
            }
        }).done(function(data){
            jQuery('img.nbd-pdf-loading').addClass('hide');
            var data = JSON.parse(data);
            var filename = 'design.pdf',
            a = document.createElement('a');
            a.setAttribute('href', data[0].link);
            a.setAttribute('download', filename);
            a.click()
        });
    },
    download_pdf_in_order: function( e, el ){
        e.preventDefault();
        var sefl = el,
            nbd_item_key = sefl.attr('data-nbd-item'),
            order_id = sefl.attr('data-order');
        sefl.find('span').addClass('active');
        jQuery.ajax({
            url: nbds_frontend.url,
            method: "POST",
            data: {
                action : 'nbd_frontend_download_pdf',
                nbd_item_key : nbd_item_key,
                order_id : order_id,
                nonce: nbds_frontend.nonce
            }
        }).done(function(data){
            sefl.find('span').removeClass('active');
            var data = JSON.parse(data);
            var filename = 'design.pdf',
            a = document.createElement('a');
            a.setAttribute('href', data[0].link);
            a.setAttribute('download', filename);
            a.click()
        });
    },
    nbd_download_final_pdf: function( e, el ){
        e.preventDefault();
        var sefl = el,
            nbd_item_key = sefl.attr('data-nbd-item');
        sefl.find('span').addClass('active');
        jQuery.ajax({
            url: nbds_frontend.url,
            method: "POST",
            data: {
                action   :    'nbd_download_final_pdf',
                nbd_item_key :   nbd_item_key,
                nonce: nbds_frontend.nonce
            }
        }).done(function(data){
            sefl.find('span').removeClass('active');
            var data = JSON.parse(data);
            if( data.flag == 1 ){
                data.pdf.forEach(function(item, index){
                    var filename = 'design_' + index + '.pdf',
                    a = document.createElement('a');
                    a.setAttribute('href', item);
                    a.setAttribute('download', filename);
                    a.click()
                });
            }
        });
    },
    insert_customer_design: function (data) {

    },
    hide_iframe_design: function () {
        var height = -jQuery(window).height();
        jQuery('#container-online-designer').removeClass('show');
        jQuery('#container-online-designer').stop().animate({
            top: height,
            opacity: 0
        }, 500);
    },
    //custom charmingprint
    charming_add_custom_color: function(color_used, pt_color) {
        jQuery('#nbd-options-color').html('<input type="hidden" class="nbd-options-color" name="nbd-options-color" value="'+color_used.toString()+'"><input type="hidden" class="nbd-options-pt-color" name="nbd-options-pt-color" value="'+pt_color+'">');
    },
    show_design_thumbnail: function (arr, task, config) {
        if( ( jQuery('#triggerDesign').length > 0 || jQuery('#startDesign').length > 0 ) ){
            jQuery('button[type="submit"].single_add_to_cart_button').show();
        };
        jQuery('#nbdesigner-preview-title').show();
        jQuery('#nbd-actions').show();
        jQuery('#nbdesign-new-template').show();
        if(task == 'create_template' || task == 'edit_template'){
            jQuery('#triggerDesign').text('Edit Template');
        }
        var html = '';
        var d = new Date();
        var count = 1;
        var has_config = false;
        var product = [];
        if( config ){
            has_config = true;
            product = config.product;
        }
        jQuery.each(arr, function (key, val) {
            if(count == 1) share_image_url = val;
            count++;
            var p_index = key.slice(6);
            var data_width = has_config ? product[p_index].img_src_width : '500',
                data_height = has_config ? product[p_index].img_src_height : '500',
                data_title = has_config ? product[p_index].orientation_name : 'Side ' + p_index;
            html += '<div class="img-con" style="cursor: pointer;" onclick="NBDESIGNERPRODUCT.show_lightbox( this )"><img data-title="'+data_title+'" data-width="'+data_width+'" data-height="'+data_height+'" src="' + val + '?t=' + d.getTime() +'" /></div>'
        });
        jQuery.each( jQuery('#nbd-share-group a'), function(){
            var d = new Date();
            var href = jQuery(this).attr('data-href');
            var share_url =nbd_create_own_page + '?product_id=' + NBDESIGNERPRODUCT.product_id + '&variation_id=' + NBDESIGNERPRODUCT.variation_id + '&reference=' + NBDESIGNERPRODUCT.folder + '&nbd_share_id=' + NBDESIGNERPRODUCT.folder + '&t=' + d.getTime();
            var _href = href + encodeURIComponent(share_url);
            if( jQuery(this).attr('id') == 'nbd-pinterest' ) _href += '&media=' + encodeURIComponent(share_image_url) + '&description=' + jQuery(this).attr('data-description');
            if( jQuery(this).attr('data-text') != undefined ) _href += '&text=' + jQuery(this).attr('data-text');
            jQuery(this).attr('href', _href);
        });
        jQuery('#nbdesigner_frontend_area').html('');
        jQuery('#nbdesigner_frontend_area').append(html);
        hideDesignFrame();
        jQuery(document).triggerHandler( 'after_show_design_thumbnail' );
        jQuery('input[name="nbd-reserve"]').remove();

        var flipbook = jQuery("[class*=real3dflipbook]:first");
        /* Integate with real3dflipbook */
        if( flipbook.length > 0 ){
            var _class = flipbook.attr('class'),
                obj = 'real3dflipbook_' + _class.substring(_class.length - 1);  
            var options = window[obj];
            var json_str = options.replace(/&quot;/g, '"');
            json_str = json_str.replace(/“/g, '"');
            json_str = json_str.replace(/”/g, '"');
            json_str = json_str.replace(/″/g, '"');
            json_str = json_str.replace(/„/g, '"');

            json_str = json_str.replace(/«&nbsp;/g, '"');
            json_str = json_str.replace(/&nbsp;»/g, '"');


            options = jQuery.parseJSON(json_str);
            options.assets = {
                preloader: options.rootFolder + "images/preloader.jpg",
                left: options.rootFolder + "images/left.png",
                overlay: options.rootFolder + "images/overlay.jpg",
                flipMp3: options.rootFolder + "mp3/turnPage.mp3",
                shadowPng: options.rootFolder + "images/shadow.png"
            };
            var pages = [];    
            jQuery.each(arr, function (key, val) {
                pages.push({
                    htmlContent: '',
                    src: val,
                    thumb: val,
                    title: key
                });
            });
            options.pages = pages;
            options.pdfjsworkerSrc = options.rootFolder + 'js/pdf.worker.min.js'
            function convertStrings(obj) {
                jQuery.each(obj, function (key, value) {
                    if (typeof (value) == 'object' || typeof (value) == 'array') {
                        convertStrings(value)
                    } else if (!isNaN(value)) {
                        if (obj[key] === "")
                            delete obj[key]
                        else
                            obj[key] = Number(value)
                    } else if (value == "true") {
                        obj[key] = true
                    } else if (value == "false") {
                        obj[key] = false
                    }
                });

            }
            convertStrings(options);
            for (var i = 0; i < options.pages.length; i++) {
                if (typeof (options.pages[i].htmlContent) != 'undefined' && options.pages[i].htmlContent != "" && options.pages[i].htmlContent != "undefined")
                    options.pages[i].htmlContent = unescape(options.pages[i].htmlContent)
                else
                    delete options.pages[i].htmlContent
            }
            options.social = [];
            if (options.facebook == "")
                delete options.facebook
            if (options.twitter == "")
                delete options.twitter
            if (options.google_plus == "")
                delete options.google_plus
            if (options.pinterest == "")
                delete options.pinterest
            if (options.email == "")
                delete options.email
            if (options.pageWidth == "")
                delete options.pageWidth
            if (options.pageHeight == "")
                delete options.pageHeight

            if (typeof (options.btnShare) == 'undefined' || !options.btnShare)
                options.btnShare = {enabled: false}
            if (typeof (options.btnNext) == 'undefined' || !options.btnNext)
                options.btnNext = {enabled: false}
            if (typeof (options.btnPrev) == 'undefined' || !options.btnPrev)
                options.btnPrev = {enabled: false}
            if (typeof (options.btnZoomIn) == 'undefined' || !options.btnZoomIn)
                options.btnZoomIn = {enabled: false}
            if (typeof (options.btnZoomOut) == 'undefined' || !options.btnZoomOut)
                options.btnZoomOut = {enabled: false}
            if (typeof (options.btnToc) == 'undefined' || !options.btnToc)
                options.btnToc = {enabled: false}
            if (typeof (options.btnThumbs) == 'undefined' || !options.btnThumbs)
                options.btnThumbs = {enabled: false}
            if (typeof (options.btnDownloadPages) == 'undefined' || !options.btnDownloadPages)
                options.btnDownloadPages = {enabled: false}
            if (typeof (options.btnDownloadPdf) == 'undefined' || !options.btnDownloadPdf)
                options.btnDownloadPdf = {enabled: false}
            if (typeof (options.btnExpand) == 'undefined' || !options.btnExpand)
                options.btnExpand = {enabled: false}
            if (typeof (options.btnExpandLightbox) == 'undefined' || !options.btnExpandLightbox)
                options.btnExpandLightbox = {enabled: false}
            if (typeof (options.btnSound) == 'undefined' || !options.btnSound)
                options.btnSound = {enabled: false}
            if (typeof (options.btnShare.icon) == 'undefined' || options.btnShare.icon == '')
                options.btnShare.icon = "fa-share";
            if (typeof (options.btnShare.title) == 'undefined' || options.btnShare.title == '')
                options.btnShare.title = "Share";

            if (typeof (options.btnNext.icon) == 'undefined' || options.btnNext.icon == '')
                options.btnNext.icon = "fa-chevron-right";
            if (typeof (options.btnNext.title) == 'undefined' || options.btnNext.title == '')
                options.btnNext.title = "Next page";

            if (typeof (options.btnPrev.icon) == 'undefined' || options.btnPrev.icon == '')
                options.btnPrev.icon = "fa-chevron-left";
            if (typeof (options.btnPrev.title) == 'undefined' || options.btnPrev.title == '')
                options.btnPrev.title = "Previous page";

            if (typeof (options.btnZoomIn.icon) == 'undefined' || options.btnZoomIn.icon == '')
                options.btnZoomIn.icon = "fa-plus";
            if (typeof (options.btnZoomIn.title) == 'undefined' || options.btnZoomIn.title == '')
                options.btnZoomIn.title = "Zoom in";

            if (typeof (options.btnZoomOut.icon) == 'undefined' || options.btnZoomOut.icon == '')
                options.btnZoomOut.icon = "fa-minus";
            if (typeof (options.btnZoomOut.title) == 'undefined' || options.btnZoomOut.title == '')
                options.btnZoomOut.title = "Zoom out";

            if (typeof (options.btnToc.icon) == 'undefined' || options.btnToc.icon == '')
                options.btnToc.icon = "fa-list-ol";
            if (typeof (options.btnToc.title) == 'undefined' || options.btnToc.title == '')
                options.btnToc.title = "Table of content";

            if (typeof (options.btnThumbs.icon) == 'undefined' || options.btnThumbs.icon == '')
                options.btnThumbs.icon = "fa-th-large";
            if (typeof (options.btnThumbs.title) == 'undefined' || options.btnThumbs.title == '')
                options.btnThumbs.title = "Pages";

            if (typeof (options.btnDownloadPages.icon) == 'undefined' || options.btnDownloadPages.icon == '')
                options.btnDownloadPages.icon = "fa-download";
            if (typeof (options.btnDownloadPages.title) == 'undefined' || options.btnDownloadPages.title == '')
                options.btnDownloadPages.title = "Download pages";
            // if(options.downloadPagesUrl)
            // options.btnDownloadPages.url = options.downloadPagesUrl;

            if (typeof (options.btnDownloadPdf.icon) == 'undefined' || options.btnDownloadPdf.icon == '')
                options.btnDownloadPdf.icon = "fa-file";
            if (typeof (options.btnDownloadPdf.title) == 'undefined' || options.btnDownloadPdf.title == '')
                options.btnDownloadPdf.title = "Download PDF";
            // if(options.downloadPdfUrl)
            // options.btnDownloadPdf.url = options.downloadPdfUrl;

            if (typeof (options.btnExpand.icon) == 'undefined' || options.btnExpand.icon == '')
                options.btnExpand.icon = "fa-expand";
            if (typeof (options.btnExpand.iconAlt) == 'undefined' || options.btnExpand.iconAlt == '')
                options.btnExpand.iconAlt = "fa-compress";
            if (typeof (options.btnExpand.title) == 'undefined' || options.btnExpand.title == '')
                options.btnExpand.title = "Toggle fullscreen";

            if (typeof (options.btnExpandLightbox.icon) == 'undefined' || options.btnExpandLightbox.icon == '')
                options.btnExpandLightbox.icon = "fa-expand";
            if (typeof (options.btnExpandLightbox.iconAlt) == 'undefined' || options.btnExpandLightbox.iconAlt == '')
                options.btnExpandLightbox.iconAlt = "fa-compress";
            if (typeof (options.btnExpandLightbox.title) == 'undefined' || options.btnExpandLightbox.title == '')
                options.btnExpandLightbox.title = "Toggle fullscreen";

            if (typeof (options.btnSound.icon) == 'undefined' || options.btnSound.icon == '')
                options.btnSound.icon = "fa-volume-up";
            if (typeof (options.btnSound.title) == 'undefined' || options.btnSound.title == '')
                options.btnSound.title = "Sound";

            if (typeof (options.viewMode) == 'undefined')
                options.viewMode = "webgl"

            if (options.btnDownloadPages.url) {
                options.btnDownloadPages.url = options.btnDownloadPages.url.replace(/\\/g, '/')
                options.btnDownloadPages.enabled = true
            } else
                options.btnDownloadPages.enabled = false

            if (options.btnDownloadPdf.url) {
                options.btnDownloadPdf.url = options.btnDownloadPdf.url.replace(/\\/g, '/')
                options.btnDownloadPdf.enabled = true
            } else
                options.btnDownloadPdf.enabled = false;


            var flipcon = jQuery('.real3dflipbook-1');
            flipcon.flipBook(options);
        }
        
    },
    scrollToAddToCart: function(){
        if( jQuery('button[type="submit"].single_add_to_cart_button').length ){
            var top = jQuery('button[type="submit"].single_add_to_cart_button').offset().top - 250;
            jQuery('html,body').animate({scrollTop: top}, 500);
        }
    },
    get_gallery_items: function(){
        var $slides = jQuery('.img-con'),
                items   = [];
        if ($slides.length > 0) {
            $slides.each(function (i, el) {
                var img = jQuery(el).find('img');
                if (img.length) {
                    var item = {
                        src: img.attr('src'),
                        w: parseInt( img.attr('data-width') ) * 2,
                        h: parseInt( img.attr('data-height') ) * 2,
                        title: img.attr('data-title')
                    };
                    items.push(item);
                }
            });
        }
        return items;
    },
    show_lightbox: function( e ){
        if( typeof PhotoSwipe == 'undefined' ) return;
        var pswpElement = jQuery( '.pswp' )[0],
            items       = this.get_gallery_items(),
            eventTarget = jQuery( e.target ),
            clicked;
        clicked = eventTarget.closest( '.img-con' );
        var options = jQuery.extend( {
                index: jQuery( clicked ).index()
        }, wc_single_product_params.photoswipe_options );

        // Initializes and opens PhotoSwipe.
        var photoswipe = new PhotoSwipe( pswpElement, PhotoSwipeUI_Default, items, options );
        photoswipe.init();
    },
    nbdesigner_ready: function(){
        if(jQuery('form input[name="variation_id"]').length > 0){
            var vid = jQuery('form input[name="variation_id"]').val();
            if( ( "undefined" != typeof is_nbd_bulk_variation) || ( vid != '' &&  parseInt(vid) > 0 ) ) {
                jQuery('#triggerDesign').removeClass('nbdesigner_disable');
            }
        }else{
            jQuery('#triggerDesign').removeClass('nbdesigner_disable');
        }
        jQuery('.nbdesigner-img-loading').addClass('hide');
    },
    nbdesigner_unready: function(){
        jQuery('#triggerDesign').addClass('nbdesigner_disable');
        jQuery('.nbdesigner-img-loading').removeClass('hide');
    },
    get_sugget_design: function(product_id, variation_id){
        if(!jQuery('.nbdesigner-related-product-image').length) return;
        var products = [];
        jQuery.each(jQuery('.nbdesigner-related-product-image'), function(){
            products.push(jQuery(this).attr('data-id'));
            jQuery(this).parent('.nbdesigner-related-product-item').find('.nbdesigner-overlay').addClass('open');
        });
        if( !products.length ) return;
        jQuery.ajax({
            url: nbds_frontend.url,
            method: "POST",
            data: {
                "action": "nbdesigner_get_suggest_design",
                "products": products,
                "product_id" : product_id,
                "variation_id" : variation_id,
                "nonce": nbds_frontend.nonce
            }
        }).done(function(data){
            data = JSON.parse(data);
            jQuery.each(jQuery('.nbdesigner-related-product-image'), function(){
                if(data['flag']){
                    var href = jQuery(this).attr('href'),
                    data_id = jQuery(this).attr('data-id');
                    jQuery(this).attr('href', addParameter(href, 'nbds-ref', data['nbd_item_key'], false));
                    jQuery(this).find('img').attr({'src' : data['images'][data_id], 'srcset' : ''});
                }
                jQuery(this).parent('.nbdesigner-related-product-item').find('.nbdesigner-overlay').removeClass('open');
            });
        });
    },
    update_nbu_value: function( arr ){
        var files = '';
        jQuery.each(arr, function (key, val) {
            files += key == 0 ? val.name : '|' + val.name;
        });
        if( ( jQuery('#triggerDesign').length > 0 || jQuery('#startDesign').length > 0 || jQuery('#startUpload').length > 0 ) ){
            jQuery('button[type="submit"].single_add_to_cart_button').show();
        };
        jQuery('input[name="nbd-upload-files"]').val( files );
    },
    remove_design: function(type, cart_item_key){
        jQuery('form.woocommerce-cart-form').addClass( 'processing' ).block( {
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        } );
        jQuery.ajax({
            url: nbds_frontend.url,
            method: "POST",
            data: {
                "action": "nbd_remove_cart_design",
                "type": type,
                "cart_item_key": cart_item_key,
                "nonce": nbds_frontend.nonce
            }
        }).done(function(data){
            jQuery('form.woocommerce-cart-form').removeClass( 'processing' ).unblock();
            if(data == 'success'){
                var designSection = jQuery('#nbd' + cart_item_key),
                    uploadSection = jQuery('#nbu' + cart_item_key),
                    extraPrice = jQuery('#nbx' + cart_item_key);
                var sections =  designSection.length + uploadSection.length;
                if( type == 'custom' ) {
                    designSection.remove();
                }else {
                    uploadSection.remove();
                }
                if( sections < 2 ) {
                    extraPrice.remove();
                    /* Update cart after remove design and upload files */
                    jQuery( '.woocommerce-cart-form [name="update_cart"]' ).prop( 'disabled', false ).trigger( 'click' );
                }
            }
        });
    },
    delete_my_design: function( e ){
        var con = confirm(nbds_frontend.confirm_delete_design);
        if( con ){
            var sefl = jQuery(e),
            design_id = sefl.attr('data-design'),
            tr_con = sefl.parents('tr.order');
            jQuery('.container-design').addClass( 'processing' ).block( {
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            } );        
            jQuery.ajax({
                url: nbds_frontend.url,
                method: "POST",
                data: {
                    action   :    'nbd_delete_my_design',
                    design_id :   design_id,
                    nonce: nbds_frontend.nonce
                }            
            }).done(function(data){
                jQuery('.container-design').removeClass( 'processing' ).unblock();
                if(data.flag == 1){
                    tr_con.remove();
                    alert(nbds_frontend.delete_success)
                }
            })   
        }
    },
    add_design_to_cart: function(e){
        var sefl = jQuery(e),
        design_id = sefl.attr('data-design');
        jQuery('.container-design').addClass( 'processing' ).block( {
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
            }
        } );
        jQuery.ajax({
            url: nbds_frontend.url,
            method: "POST",
            data: {
                action   : 'nbd_add_design_to_cart',
                design_id : design_id,
                nonce: nbds_frontend.nonce
            }
        }).done(function(data){
            jQuery('.container-design').removeClass( 'processing' ).unblock();
            if(data.flag == 1){
                window.location = nbds_frontend.cart_url;
            }else{
                alert('Opp! Try again later')
            }
        })
    },
    add_variation_bulk_form: function(){
        var variation_wrap = jQuery('.nbd-variation-wrap').first(),
        new_variation_wrap = variation_wrap.clone();
        new_variation_wrap.appendTo('#nbd-variations-wrap');  
        jQuery(new_variation_wrap).find('.nbd-variation-quantity').val(1);
        this.init_nbd_variation_value();
    },
    remove_variation_bulk_form: function(e){
        var self = jQuery(e),
            wrap =  self.closest('.nbd-variation-wrap');  
        wrap.remove(); 
        this.init_nbd_variation_value();
    },
    init_nbd_variation_value: function(){
        var nbd_variation_value = '',
            has_quantity = false;
        jQuery('.nbd-variation-value').val(nbd_variation_value);
        jQuery('.nbd-variation-wrap').each(function(index){
            var variation_id = jQuery(this).find('select').val();
            var quantity = jQuery(this).find('input').val();
            if( quantity > 0 ) has_quantity = true;
            nbd_variation_value += index > 0 ? '|' : '';
            nbd_variation_value += variation_id + '_' + quantity;
        });
        jQuery('.nbd-variation-value').val(nbd_variation_value);
        if( has_quantity ){
            jQuery('.single_add_to_cart_button').removeClass('disabled wc-variation-selection-needed');
        }else{
            jQuery('.single_add_to_cart_button').addClass('disabled wc-variation-selection-needed');
        }
    },
    change_nbd_dokan_format: function( e ){
        var type = jQuery(e).val(),
            el_action = jQuery(e).parents('.nbd-dokan-download-wrap').find('a.nbd-dokan-download'),
            href = el_action.attr('data-href');
            el_action.attr('href', href + '&type=' + type);
    },
    change_nbd_download_type: function( e ){
        var type = jQuery(e).val(),
            parent = jQuery(e).parents('.nbd-order-item-download-section'),    
            el_action = parent.find('a.nbd-order-download-file'),
            link = parent.attr('data-href');
        if( type == 'pdf' ) {
            //jQuery('#nbd-show-bleed')
            jQuery('.nbd-pdf-options').removeClass('nbd-hide');
        }
        el_action.attr('href', link + '&type=' + type);        
    },
    change_nbd_download_pdf_type: function( ){
        jQuery.each(jQuery('.nbd-order-item-download-section'), function(){
            var _bleed = jQuery('#nbd-show-bleed').is(':checked') ? 'yes' : 'no';
            var _multi_file = jQuery('#nbd-multi-file').is(':checked') ? 'yes' : 'no';
            jQuery('.nbd-pdf-options').addClass('nbd-hide');
            jQuery.each( jQuery('.nbd-order-item-download-section'), function(){
                var link = jQuery(this).find('.nbd-order-download-file').attr('href');
                link += '&multi_file=' + _multi_file + '&bleed=' + _bleed;
                jQuery(this).find('.nbd-order-download-file').attr('href', link);
            })
        });
    },
    qty: 1,
    updateQty: function(qty, variations){
        /*jQuery('input[name="quantity"]').val(qty);*/
        /*if( variations ){
            NBDESIGNERPRODUCT.variations = JSON.parse( JSON.stringify(variations) );
            jQuery(document).triggerHandler( 'change_nbo_variations' );
        }*/
    },
    searchTemplate: function (tag) {
        var search = jQuery('#ndb_search_template').val();
        if (search != '' || typeof tag != 'undefined') {
            var url = '';
            if (search != '') url = nbds_frontend.gallery_url + '?search=' + search;
            if (typeof tag != 'undefined') {
                url = nbds_frontend.gallery_url + '?tag=' + tag;
            }
            window.location = url
        }
    },
    submitSearchTemplate: function (e) {
        if (e.keyCode == 13) this.searchTemplate();
    }
};
function addParameter(url, parameterName, parameterValue, atStart/*Add param before others*/) {
    var replaceDuplicates = true;
    var urlhash = '';
    if (url.indexOf('#') > 0) {
        var cl = url.indexOf('#');
        urlhash = url.substring(url.indexOf('#'), url.length);
    } else {
        urlhash = '';
        cl = url.length;
    }
    var sourceUrl = url.substring(0, cl);
    var urlParts = sourceUrl.split("?");
    var newQueryString = "";
    if (urlParts.length > 1){
        var parameters = urlParts[1].split("&");
        for (var i = 0; (i < parameters.length); i++)
        {
            var parameterParts = parameters[i].split("=");
            if (!(replaceDuplicates && parameterParts[0] == parameterName))
            {
                if (newQueryString == "")
                    newQueryString = "?";
                else
                    newQueryString += "&";
                newQueryString += parameterParts[0] + "=" + (parameterParts[1] ? parameterParts[1] : '');
            }
        }
    }
    if (newQueryString == "") newQueryString = "?";
    if (atStart) {
        newQueryString = '?' + parameterName + "=" + parameterValue + (newQueryString.length > 1 ? '&' + newQueryString.substring(1) : '');
    } else {
        if (newQueryString !== "" && newQueryString != '?')
            newQueryString += "&";
        newQueryString += parameterName + "=" + (parameterValue ? parameterValue : '');
    }
    return urlParts[0] + newQueryString + urlhash;
};
function removeParam(key, sourceURL) {
    var rtn = sourceURL.split("?")[0],
        param,
        params_arr = [],
        queryString = (sourceURL.indexOf("?") !== -1) ? sourceURL.split("?")[1] : "";
    if (queryString !== "") {
        params_arr = queryString.split("&");
        for (var i = params_arr.length - 1; i >= 0; i -= 1) {
            param = params_arr[i].split("=")[0];
            if (param === key) {
                params_arr.splice(i, 1);
            }
        }
        rtn = rtn + "?" + params_arr.join("&");
    }
    return rtn;
}
/*!
 * imagesLoaded PACKAGED v4.1.4
 * JavaScript is all like "Your images are done yet or what?"
 * MIT License
 */
!function(e,t){"function"==typeof define&&define.amd?define("ev-emitter/ev-emitter",t):"object"==typeof module&&module.exports?module.exports=t():e.EvEmitter=t()}("undefined"!=typeof window?window:this,function(){function e(){}var t=e.prototype;return t.on=function(e,t){if(e&&t){var i=this._events=this._events||{},n=i[e]=i[e]||[];return n.indexOf(t)==-1&&n.push(t),this}},t.once=function(e,t){if(e&&t){this.on(e,t);var i=this._onceEvents=this._onceEvents||{},n=i[e]=i[e]||{};return n[t]=!0,this}},t.off=function(e,t){var i=this._events&&this._events[e];if(i&&i.length){var n=i.indexOf(t);return n!=-1&&i.splice(n,1),this}},t.emitEvent=function(e,t){var i=this._events&&this._events[e];if(i&&i.length){i=i.slice(0),t=t||[];for(var n=this._onceEvents&&this._onceEvents[e],o=0;o<i.length;o++){var r=i[o],s=n&&n[r];s&&(this.off(e,r),delete n[r]),r.apply(this,t)}return this}},t.allOff=function(){delete this._events,delete this._onceEvents},e}),function(e,t){"use strict";"function"==typeof define&&define.amd?define(["ev-emitter/ev-emitter"],function(i){return t(e,i)}):"object"==typeof module&&module.exports?module.exports=t(e,require("ev-emitter")):e.imagesLoaded=t(e,e.EvEmitter)}("undefined"!=typeof window?window:this,function(e,t){function i(e,t){for(var i in t)e[i]=t[i];return e}function n(e){if(Array.isArray(e))return e;var t="object"==typeof e&&"number"==typeof e.length;return t?d.call(e):[e]}function o(e,t,r){if(!(this instanceof o))return new o(e,t,r);var s=e;return"string"==typeof e&&(s=document.querySelectorAll(e)),s?(this.elements=n(s),this.options=i({},this.options),"function"==typeof t?r=t:i(this.options,t),r&&this.on("always",r),this.getImages(),h&&(this.jqDeferred=new h.Deferred),void setTimeout(this.check.bind(this))):void a.error("Bad element for imagesLoaded "+(s||e))}function r(e){this.img=e}function s(e,t){this.url=e,this.element=t,this.img=new Image}var h=e.jQuery,a=e.console,d=Array.prototype.slice;o.prototype=Object.create(t.prototype),o.prototype.options={},o.prototype.getImages=function(){this.images=[],this.elements.forEach(this.addElementImages,this)},o.prototype.addElementImages=function(e){"IMG"==e.nodeName&&this.addImage(e),this.options.background===!0&&this.addElementBackgroundImages(e);var t=e.nodeType;if(t&&u[t]){for(var i=e.querySelectorAll("img"),n=0;n<i.length;n++){var o=i[n];this.addImage(o)}if("string"==typeof this.options.background){var r=e.querySelectorAll(this.options.background);for(n=0;n<r.length;n++){var s=r[n];this.addElementBackgroundImages(s)}}}};var u={1:!0,9:!0,11:!0};return o.prototype.addElementBackgroundImages=function(e){var t=getComputedStyle(e);if(t)for(var i=/url\((['"])?(.*?)\1\)/gi,n=i.exec(t.backgroundImage);null!==n;){var o=n&&n[2];o&&this.addBackground(o,e),n=i.exec(t.backgroundImage)}},o.prototype.addImage=function(e){var t=new r(e);this.images.push(t)},o.prototype.addBackground=function(e,t){var i=new s(e,t);this.images.push(i)},o.prototype.check=function(){function e(e,i,n){setTimeout(function(){t.progress(e,i,n)})}var t=this;return this.progressedCount=0,this.hasAnyBroken=!1,this.images.length?void this.images.forEach(function(t){t.once("progress",e),t.check()}):void this.complete()},o.prototype.progress=function(e,t,i){this.progressedCount++,this.hasAnyBroken=this.hasAnyBroken||!e.isLoaded,this.emitEvent("progress",[this,e,t]),this.jqDeferred&&this.jqDeferred.notify&&this.jqDeferred.notify(this,e),this.progressedCount==this.images.length&&this.complete(),this.options.debug&&a&&a.log("progress: "+i,e,t)},o.prototype.complete=function(){var e=this.hasAnyBroken?"fail":"done";if(this.isComplete=!0,this.emitEvent(e,[this]),this.emitEvent("always",[this]),this.jqDeferred){var t=this.hasAnyBroken?"reject":"resolve";this.jqDeferred[t](this)}},r.prototype=Object.create(t.prototype),r.prototype.check=function(){var e=this.getIsImageComplete();return e?void this.confirm(0!==this.img.naturalWidth,"naturalWidth"):(this.proxyImage=new Image,this.proxyImage.addEventListener("load",this),this.proxyImage.addEventListener("error",this),this.img.addEventListener("load",this),this.img.addEventListener("error",this),void(this.proxyImage.src=this.img.src))},r.prototype.getIsImageComplete=function(){return this.img.complete&&this.img.naturalWidth},r.prototype.confirm=function(e,t){this.isLoaded=e,this.emitEvent("progress",[this,this.img,t])},r.prototype.handleEvent=function(e){var t="on"+e.type;this[t]&&this[t](e)},r.prototype.onload=function(){this.confirm(!0,"onload"),this.unbindEvents()},r.prototype.onerror=function(){this.confirm(!1,"onerror"),this.unbindEvents()},r.prototype.unbindEvents=function(){this.proxyImage.removeEventListener("load",this),this.proxyImage.removeEventListener("error",this),this.img.removeEventListener("load",this),this.img.removeEventListener("error",this)},s.prototype=Object.create(r.prototype),s.prototype.check=function(){this.img.addEventListener("load",this),this.img.addEventListener("error",this),this.img.src=this.url;var e=this.getIsImageComplete();e&&(this.confirm(0!==this.img.naturalWidth,"naturalWidth"),this.unbindEvents())},s.prototype.unbindEvents=function(){this.img.removeEventListener("load",this),this.img.removeEventListener("error",this)},s.prototype.confirm=function(e,t){this.isLoaded=e,this.emitEvent("progress",[this,this.element,t])},o.makeJQueryPlugin=function(t){t=t||e.jQuery,t&&(h=t,h.fn.imagesLoaded=function(e,t){var i=new o(this,e,t);return i.jqDeferred.promise(h(this))})},o.makeJQueryPlugin(),o});