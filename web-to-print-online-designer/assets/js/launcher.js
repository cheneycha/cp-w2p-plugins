(function ($, window, document) {
    $(document).ready(function () {
        window.URL = window.URL || window.webkitURL;

        var triggerBtn      = $('#nbdl-start-btn'),
        closePopupBtn       = $('#nbdl-popup-close'),
        formWrap            = $('#nbdl-upload-form'),
        optionsInnerWrap    = $('.nbdl-options-inner'),
        optionsWrap         = $('.nbdl-options-wrap'),
        product             = $('.nbdl-product-wrapper'),
        actionBtn           = $('.nbdl-action'),
        uploadForm          = $('.nbdl-upload-form'),
        progressIndicatorEl = $('#nbdl-uf-progress-indicator'),
        backLinkBtn         = $('#nbdl-uf-back'),
        saveDesignBtn       = $('#nbdl-uf-continue'),
        loadingEl           = $('#nbdl-loading'),
        relatedLoadingEl    = $('#nbdl-related-loading'),
        glSection           = $('.nbdl-guidelines-section'),
        glSectionDivider    = $('.nbdl-guidelines-section-divider'),
        sidePreviewElWrap   = $('.nbdl-uf-side-preview-upload-wrap'),
        guidelineElWrap     = $('.nbdl-uf-side-download-guidelines'),
        sideTags            = $('#nbdl-side-tags'),
        stageSwitcherWrap   = $('.nbdl-stage-switcher'),
        previewStageWrap    = $('.nbdl-stage-preview-area'),
        relatedProdcutWrap  = $('.nbdl-realated-product-list'),
        relatedProdcutPanel = $('.nbdl-realated-product-panel'),
        noRelatedProEl      = $('.nbdl-no-realated'),
        sideBodyPhase1      = $('.nbdl-phase-1'),
        sideBodyPhase2      = $('.nbdl-phase-2'),
        sidebarHeader       = $('.nbdl-uf-sidebar-header-title'),
        submitIndicator     = $('.nbdl-submit-indicator'),
        editDesignBtn       = $('.nbdl-edit'),
        tableWrapper        = $('.nbdl-table-wrapper'),
        popupWrap           = $('#nbdl-popup'),
        currentNbdlProduct  = 0,
        currentNbdlDesign   = 0,
        sidePreviewEl       = null,
        guidelineEl         = null,
        stageSwitcherEl     = null,
        previewStageEl      = null,
        realatedProductEl   = null,
        currentData         = null,
        realatedListEl      = null,
        currentPhase        = 1,
        maxDimension        = parseFloat( nbdl.max_preview_dimension ),
        checkFiles          = {
            product_preview: false,
            design: false,
            side_previews: []
        },
        relatedProductData  = [];
        checkedRelatedProduct = [];

        var isInViewport = function (elem) {
            var bounding = elem.getBoundingClientRect();
            return (
                bounding.top >= 0 &&
                bounding.left >= 0 &&
                bounding.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                bounding.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        };

        function is_url(str){
            var regexp =  /^(?:(?:https?|ftp):\/\/)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/\S*)?$/;
            if ( regexp.test( str ) ){
                return true;
            }else{
                return false;
            }
        }

        function cleanUrl(){
            var url = new URL( window.location.href ),
            query_string = url.search;
            search_params = new URLSearchParams( query_string );
            search_params.delete('edit');
            url.search = search_params.toString();
            var new_url = url.toString();
            history.replaceState(null, null, new_url);
        }

        var init = function(){
            $('.nbdl-max-dimension').text(maxDimension);
            if(  Number.isInteger( parseInt( tableWrapper.attr('data-design-id') ) ) ){
                var design_id = parseInt( tableWrapper.attr('data-design-id') ),
                product_id = parseInt( tableWrapper.attr('data-product-id') );
                currentNbdlProduct = product_id;
                currentNbdlDesign = design_id;
                initLauncher();
                showUploadForm(true);
                cleanUrl();
            }
        };

        var initLauncher = function(){
            popupWrap.addClass('active');
            $('body').addClass('nbd-prevent-scroll');
        }

        var toggleLoading = function(){
            loadingEl.toggleClass('active');
        }

        var toggleRelatedProductPanelLoading = function(){
            relatedLoadingEl.toggleClass('active');
        }

        var getProductInfo = function( edit ){
            submitIndicator.html('');
            var formData = new FormData;
            formData.append('action', 'nbdl_get_product_info');
            formData.append('product_id', currentNbdlProduct);
            formData.append('nonce', nbdl.nonce);
            if( edit ){
                formData.append('task', 'edit');
                formData.append('design_id', currentNbdlDesign);
            }
            toggleLoading();
            $.ajax({
                url: nbdl.ajax_url,
                method: "POST",
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                complete: function() {
                    toggleLoading();
                },
                success: function(data) {
                    if( data.flag == '1' ){
                        resetData();
                        initData( data );
                    }else{
                        alert( data.message )
                    }
                }
            });
        };

        var resetData = function(){
            currentPhase = 1;
            formWrap.find('input[type="file"]').val('');
            formWrap.find('input.edit-value').remove();
            glSection.removeClass('active');
            glSectionDivider.removeClass('active');
            $('.nbdl-uf-sidebar-section-upload').removeClass('active');
            progressIndicatorEl.css('width', '0%');
            sideBodyPhase1.removeClass('nbdl-hidden');
            sideBodyPhase2.addClass('nbdl-hidden');

            var sidePreviewEls = $('.nbdl-uf-side-preview-upload'),
            guidelineEls       = $('a.nbdl-guideline'),
            stageSwitcherBtns  = $('.nbdl-stage-switcher-btn');

            sidePreviewEl = sidePreviewEls.length ? sidePreviewEls.removeClass('active').first().clone() : sidePreviewEl;
            sidePreviewEls.remove();

            guidelineEl = guidelineEls.length ? guidelineEls.first().clone() : guidelineEl;
            guidelineEls.remove();

            stageSwitcherEl = stageSwitcherBtns.length ? stageSwitcherBtns.removeClass('active').first().clone() : stageSwitcherEl;
            stageSwitcherBtns.remove();

            var previewStageElFirst = $('.nbdl-stage-design-base-wrap').first(),
            defaultSrc = previewStageElFirst.find('.nbdl-product-base').attr('data-default');
            previewStageElFirst.find('.nbdl-product-base .nbdl-product-base-img, .nbdl-product-overlay .nbdl-product-overlay-img').attr('src', defaultSrc).removeClass('active');
            previewStageElFirst.find('.nbdl-product-base, .nbdl-product-overlay').addClass('default').removeClass('active');
            previewStageElFirst.find('.nbdl-product-color-base').removeClass('active');
            previewStageElFirst.find('.nbdl-content-design-area').find('img').remove();
            previewStageEl = previewStageElFirst.length ? previewStageElFirst.clone() : previewStageEl;
            $('.nbdl-stage-design-base-wrap').remove();

            var continueText = saveDesignBtn.attr('data-text-continue');
            saveDesignBtn.addClass('nbdl-inactive').find('span').text(continueText);

            var headerText = sidebarHeader.attr('data-text-phase-1');
            sidebarHeader.html(headerText);

            checkFiles = {
                product_preview: false,
                design: false,
                side_previews: []
            };
        };

        var initFunc = function(){
            stageSwitcherWrap.find('.nbdl-stage-switcher-btn').on('click', function(){
                var index = $(this).attr('data-index');
                stageSwitcherWrap.find('.nbdl-stage-switcher-btn').removeClass('active');
                $(this).addClass('active');
                previewStageWrap.find('.nbdl-stage-design-base-wrap').removeClass('active');
                $.each(previewStageWrap.find('.nbdl-stage-design-base-wrap'), function(){
                    if( $(this).attr('data-index') == index ){
                        $(this).addClass('active');
                    }
                });
            });

            $.each($('.nbdl-product-base-img, .nbdl-product-overlay-img'), function(){
                var loadImg = $(this).next(),
                self = $(this),
                url = $(this).attr('src'),
                img = new Image;
                img.onload = function() {
                    self.addClass('active');
                    loadImg.removeClass('active');
                    if( self.hasClass('nbdl-product-overlay-img') ){
                        self.parents('.nbdl-stage-design-base').find('.nbdl-product-color-base').removeClass('nbdl-hidden');
                    }
                };
                img.src = url
            });

            $.each($('input[type="file"]'), function(){
                var accepts = $(this).attr('accept').replace(/[\s\.]/g, '').split(','),
                self = $(this),
                isSidePreview = self.attr('name') == 'nbdl-side-preview[]' ? true : false,
                maxSize = parseFloat( self.attr( 'data-max-size' ) ) * 1024 * 1024,
                checkType = self.attr('data-check');
                $(this).off('change').on('change', function( e ){
                    self.next('input.edit-value').remove();
                    var files = e.target.files;
                    if( files.length > 0 ){
                        var file = files[0],
                        ext = file.name.substring( file.name.lastIndexOf('.') + 1 ).toLowerCase(),
                        mediaType = file.type.toLowerCase(),
                        sidePreviewAccept = ['image/jpg', 'image/jpeg', 'image/png'],
                        size = file.size,
                        check = false;
                        accepts.forEach( function( accept ){
                            if( ext.indexOf( accept ) > -1 ){
                                check = true;
                            }
                        } );
                        if( size > maxSize ) check = false;

                        var updateWarning = function( check ){
                            if( !check ){
                                self.next('.nbdl-upload-warning').addClass('active');
                            }else{
                                self.next('.nbdl-upload-warning').removeClass('active');
                            }
                            updateProgress();
                        }

                        if( isSidePreview ){
                            var index = self.parents('.nbdl-uf-side-preview-upload').attr('data-index');
                            if( sidePreviewAccept.indexOf( mediaType ) == -1 ){
                                check = false;
                                checkFiles[checkType][index] = check;
                                updateWarning( check );
                            }else{
                                processSidePreview( self, file ).then(function( check ){
                                    checkFiles[checkType][index] = check;
                                    updateWarning( check );
                                });
                            }
                        }else{
                            checkFiles[checkType] = check;
                            updateWarning( check );
                        }
                    }else{
                        if( isSidePreview ){
                            var index = self.parents('.nbdl-uf-side-preview-upload').attr('data-index'),
                            designArea = $('.nbdl-stage-design-base-wrap[data-index="' + index + '"]').find('.nbdl-content-design-area');
                            checkFiles[checkType][index] = false;
                            designArea.find('img').remove();
                        }else{
                            checkFiles[checkType] = false;
                        }
                        updateProgress();
                    }
                });
            });
        }

        var processSidePreview = async function( input, file ){
            var index = input.parents('.nbdl-uf-side-preview-upload').attr('data-index'),
            designArea = $('.nbdl-stage-design-base-wrap[data-index="' + index + '"]').find('.nbdl-content-design-area'),
            check = true,
            setting = currentData.setting[index],
            areaWidth = parseFloat( setting.area_design_width ),
            areaHeight = parseFloat( setting.area_design_height ),
            url = is_url( file ) ? file : URL.createObjectURL( file ),
            img = document.createElement("img");

            check = await new Promise(function(resolve, reject){
                img.onload = function() {
                    var width = img.width,
                    height = img.height;
                    if( width > maxDimension || height > maxDimension ){
                        check = false;
                    }else{
                        var css = calcSizePreviewPosition( width, height, areaWidth, areaHeight );
                        $( img ).css( css );
                        designArea.find('img').remove(); designArea.append(img);
                        stageSwitcherWrap.find('.nbdl-stage-switcher-btn[data-index="' + index + '"]').trigger('click');
                        check =  true;
                        currentData.setting[index].design_preview_url = url;
                        currentData.setting[index].design_preview_width = width;
                        currentData.setting[index].design_preview_height = height;
                    }
                    resolve( check );
                };
                img.onerror = reject;
                img.src = url;
            });

            return check;
        };

        var calcSizePreviewPosition = function( width, height, areaWidth, areaHeight ){
            var cssObj = {
                left: 0,
                top: 0,
                width: areaWidth,
                height: areaHeight
            },
            ratio;
            if( areaWidth / areaHeight > width / height ){
                ratio = areaHeight / height;
                var new_width = width * ratio;
                cssObj.left = ( areaWidth - new_width ) / 2;
                cssObj.width = new_width;
            }else{
                ratio = areaWidth / width;
                var new_height = height * ratio;
                cssObj.top = ( areaHeight - new_height ) / 2;
                cssObj.height = new_height;
            }
            return cssObj;
        }

        var updateProgress = function(){
            var progress = 0,
            sideNum = currentData.setting.length > 0 ? currentData.setting.length : 1;
            progress += checkFiles.product_preview ? 100 / 3 : 0;
            progress += checkFiles.design ? 100 / 3 : 0;
            checkFiles.side_previews.map(function(side){
                progress += side ? 100 / 3 / sideNum : 0;
            });
            progress = Math.ceil(progress);
            progress = progress > 100 ? 100 : progress;
            progressIndicatorEl.css('width', progress + '%');

            if( checkAvailableForSubmit() ){
                saveDesignBtn.removeClass('nbdl-inactive');
            }else{
                saveDesignBtn.addClass('nbdl-inactive');
            }
        };

        var initData = function( data ){
            currentData = data;

            if( typeof data.guidelines != 'undefined' ){
                glSection.addClass('active');
                glSectionDivider.addClass('active');
                data.guidelines.map(function( guideline ){
                    var guidelineEl2 = guidelineEl.clone();
                    guidelineEl2.attr({title: guideline.name, href: guideline.file});
                    guidelineElWrap.append( guidelineEl2 );
                });
            }

            data.setting.map(function( side, index ){
                var sidePreviewEl2 = sidePreviewEl.clone();
                sidePreviewEl2.find('.nbdl-uf-side-preview-name').html( side.orientation_name );
                sidePreviewEl2.attr('data-index', index);
                sidePreviewElWrap.append( sidePreviewEl2 );

                var stageSwitcherEl2 = stageSwitcherEl.clone();
                stageSwitcherEl2.html( side.orientation_name );
                stageSwitcherEl2.attr('data-index', index);
                stageSwitcherWrap.append( stageSwitcherEl2 );

                var previewStageEl2 = previewStageEl.clone();
                previewStageEl2.attr('data-index', index);
                previewStageEl2.find('.nbdl-product-base .nbdl-product-base-loading, .nbdl-product-overlay .nbdl-product-overlay-loading').addClass('active');
                previewStageEl2.find('.nbdl-product-base, .nbdl-product-overlay, .nbdl-product-color-base').css({
                    top: side.img_src_top + 'px',
                    left: side.img_src_left + 'px',
                    width: side.img_src_width + 'px',
                    height: side.img_src_height + 'px'
                });
                previewStageEl2.find('.nbdl-product-base .nbdl-product-base-img, .nbdl-product-overlay .nbdl-product-overlay-img').css({
                    width: side.img_src_width + 'px',
                    height: side.img_src_height + 'px'
                });
                if( side.show_overlay == 1 ){
                    previewStageEl2.find('.nbdl-product-overlay').addClass('active');
                }
                if( side.bg_type != 'image' ){
                    previewStageEl2.find('.nbdl-product-base').addClass('nbdl-hidden');
                    previewStageEl2.find('.nbdl-product-color-base').addClass('active');
                    previewStageEl2.find('.nbdl-stage-design-base').css( 'background', '#fff' );
                    previewStageEl2.find('.nbdl-product-base').attr('data-image', '0');
                }else{
                    previewStageEl2.find('.nbdl-stage-design-base').css( 'background', '' );
                    previewStageEl2.find('.nbdl-product-base').attr('data-image', '1');
                }
                if( side.bg_type == 'color' ){
                    previewStageEl2.find('.nbdl-product-color-base').css( 'background', side.bg_color_value );
                    if( side.show_overlay == 1 ) previewStageEl2.find('.nbdl-product-color-base').addClass('nbdl-hidden');
                }else{
                    previewStageEl2.find('.nbdl-product-color-base').css( 'background', '' );
                }
                previewStageEl2.find('.nbdl-content-design-area').css({
                    top: ( parseFloat( side.area_design_top ) - 1 ) + 'px',
                    left: ( parseFloat( side.area_design_left ) - 1 ) + 'px',
                    width: side.area_design_width + 'px',
                    height: side.area_design_height + 'px'
                }).find('.nbdl-design-area-width').text( side.real_width );
                previewStageEl2.find('.nbdl-content-design-area .nbdl-design-area-height').text( side.real_height );
                previewStageEl2.find('.nbdl-product-base .nbdl-product-base-img').attr('src', side.img_src);
                previewStageEl2.find('.nbdl-product-overlay .nbdl-product-overlay-img').attr('src', side.img_overlay);
                previewStageWrap.append( previewStageEl2 );

                checkFiles.side_previews.push( false );
            });

            setTimeout(function(){
                var tags = [];

                if( currentNbdlDesign != 0 && typeof data.design != 'undefined' ){
                    if( !!data.design.thumbnail ){
                        $('<input type="hidden" class="edit-value" value="'+ data.design.thumbnail +'" />').insertAfter( 'input[name="nbdl-preview"]' );
                        checkFiles.product_preview = true;
                    }

                    if( !!typeof data.design.resource ){
                        $('<input type="hidden" class="edit-value" value="'+ data.design.resource +'" />').insertAfter( 'input[name="nbdl-design-file"]' );
                        checkFiles.design = true;
                    }
    
                    if( !!data.design.side_previews ){
                        $.each( $('input[data-check="side_previews"]'), function( index ){
                            var that = $(this);
                            if( typeof data.design.side_previews[index] != 'undefined' ){
                                $('<input type="hidden" class="edit-value" value="1" />').insertAfter( that );
                                checkFiles.side_previews[index] = true;
                                processSidePreview( that, data.design.side_previews[index] );
                            }
                        });
                    }

                    if( !!data.design.name ){
                        $('input[name="nbdl-design-name"]').val(data.design.name);
                    }

                    if( !!data.design.tags ){
                        tags = data.design.tags.split(",");
                    }

                    updateProgress();
                }

                stageSwitcherWrap.find('.nbdl-stage-switcher-btn').first().addClass('active');
                previewStageWrap.find('.nbdl-stage-design-base-wrap').first().addClass('active');
                $('.nbdl-upload-warning').removeClass('active');

                initFunc();

                sideTags.val( tags ).selectWoo({
                    placeholder: sideTags.attr( 'data-placeholder' ),
                    width: '100%'
                }).trigger('change');
            });
        };

        var showUploadForm = function( edit ){
            popupWrap.addClass('nbd-prevent-scroll');
            uploadForm.addClass('active');
            var productName = '';
            $.each($('.nbdl-product-wrapper'), function(){
                if( $(this).attr('data-id') == currentNbdlProduct ){
                    productName = $(this).find('.nbdl-product-name').html();
                }
            });
            $('.nbdl-selected-product-row.nbdl-origin .nbdl-selected-product-name').html( productName );
            getProductInfo( edit );
        }

        var closeUploadForm = function(){
            popupWrap.removeClass('nbd-prevent-scroll');
            uploadForm.removeClass('active');
        }

        var checkAvailableForSubmit = function(){
            var check = checkFiles.product_preview && checkFiles.design;
            checkFiles.side_previews.map(function(side, index){
                check = check && side;

                var sectionUpload = $('.nbdl-uf-side-preview-upload[data-index="' + index + '"]').find('.nbdl-uf-sidebar-section-upload');
                if( side ){
                    sectionUpload.addClass('active');
                }else{
                    sectionUpload.removeClass('active');
                }
            });

            if( checkFiles.product_preview ){
                $('.nbdl-uf-sidebar-section-upload.__product-preview').addClass('active');
            }else{
                $('.nbdl-uf-sidebar-section-upload.__product-preview').removeClass('active');
            }
            if( checkFiles.design ){
                $('.nbdl-uf-sidebar-section-upload.__design').addClass('active');
            }else{
                $('.nbdl-uf-sidebar-section-upload.__design').removeClass('active');
            }

            return check;
        };

        var getRelatedProducts = function(){
            openRelatedProductsPanel();
            if( relatedProductData[currentNbdlProduct] ){
                setTimeout(function(){
                    initRelatedProduct( relatedProductData[currentNbdlProduct] );
                }, 500);
            }else{
                var formData = new FormData;
                formData.append('action', 'nbdl_get_related_products');
                formData.append('product_id', currentNbdlProduct);
                formData.append('nonce', nbdl.nonce);
                toggleRelatedProductPanelLoading();
                $.ajax({
                    url: nbdl.ajax_url,
                    method: "POST",
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    complete: function() {
                        toggleRelatedProductPanelLoading();
                    },
                    success: function(data) {
                        if( typeof data.products != 'undefined' ){
                            relatedProductData[currentNbdlProduct] = data;
                            initRelatedProduct( data );
                        }
                    }
                });
            }
        };

        var initRelatedProduct = function( data ){
            checkedRelatedProduct = [];
            var realatedProductEls = $('.nbdl-realated-product');
            realatedProductEl = realatedProductEls.length ? realatedProductEls.first().clone() : realatedProductEl;
            realatedProductEl.attr('data-loaded', '0').find('.nbdl-realated-product-base .nbdl-realated-product-base-img').remove();
            realatedProductEl.find('.nbdl-realated-product-base .nbdl-realated-product-base-loading').removeClass('.nbdl-hidden');
            realatedProductEl.find('.nbdl-realated-product-overlay .nbdl-realated-product-overlay-img').remove();
            realatedProductEl.find('.nbdl-realated-product-base .nbdl-realated-product-overlay-loading').removeClass('.nbdl-hidden');
            noRelatedProEl.removeClass('active');
            realatedProductEls.remove();

            var realatedListEls = $('.nbdl-selected-product-row.nbdl-related');
            realatedListEl = realatedListEls.length ? realatedListEls.first().clone() : realatedListEl;
            realatedListEl.addClass('nbdl-hidden').find('.nbdl-selected-product-name').html('');
            realatedListEl.find('input[type="checkbox"]').prop('checked', true);
            realatedListEls.remove();

            if( data.products.length == 0 ){
                noRelatedProEl.addClass('active');
            } else {
                data.products.map(function(product){
                    var realatedProductEl2 = realatedProductEl.clone(),
                    setting = product.setting;
                    realatedProductEl2.find('input').val( product.product_id );
                    realatedProductEl2.find('.nbdl-realated-product-name').html( product.name );

                    realatedProductEl2.find('.nbdl-realated-product-base, .nbdl-realated-product-overlay, .nbdl-realated-product-color-base').css({
                        top: setting.img_src_top / 2 + 'px',
                        left: setting.img_src_left / 2 + 'px',
                        width: setting.img_src_width / 2 + 'px',
                        height: setting.img_src_height / 2 + 'px'
                    });

                    realatedProductEl2.find('.nbdl-realated-content-design-area').css({
                        top: ( setting.area_design_top / 2 ) + 'px',
                        left: ( setting.area_design_left / 2 ) + 'px',
                        width: setting.area_design_width / 2 + 'px',
                        height: setting.area_design_height / 2 + 'px'
                    }).attr({'data-width': setting.area_design_width / 2, 'data-height': setting.area_design_height / 2, 'data-insert': '0'}).html('');

                    if( setting.show_overlay != 1 ){
                        realatedProductEl2.find('.nbdl-realated-product-overlay').attr( 'data-show', '0' ).addClass('nbdl-hidden');
                    }else{
                        realatedProductEl2.find('.nbdl-realated-product-overlay').attr( 'data-show', '1' );
                    }
                    if( setting.bg_type != 'image' ){
                        realatedProductEl2.find('.nbdl-realated-product-base').attr( 'data-image', '0' ).addClass('nbdl-hidden');
                        realatedProductEl2.find('.nbdl-realated-product-color-base').removeClass('nbdl-hidden');
                        realatedProductEl2.find('.nbdl-realated-product-preview').css( 'background', '#fff' );

                        if( setting.show_overlay != 1 ){
                            var designArea = realatedProductEl2.find('.nbdl-realated-content-design-area'),
                            img = document.createElement("img"),
                            css = calcSizePreviewPosition( currentData.setting[0].design_preview_width, currentData.setting[0].design_preview_height, setting.area_design_width / 2, setting.area_design_height / 2 );
                            img.src = currentData.setting[0].design_preview_url;
                            $( img ).css( css );
                            designArea.append( img ).attr('data-insert', '1');
                        }
                    }else{
                        realatedProductEl2.find('.nbdl-realated-product-preview').css( 'background', '' );
                        realatedProductEl2.find('.nnbdl-realated-product-base').attr( 'data-image', '1' );
                    }
                    if( setting.bg_type == 'color' ){
                        realatedProductEl2.find('.nbdl-realated-product-color-base').attr('data-color', '1').css( 'background', setting.bg_color_value );
                    }else{
                        realatedProductEl2.find('.nbdl-realated-product-color-base').attr('data-color', '0').css( 'background', '' );
                    }
                    if( setting.show_overlay == 1 ){
                        realatedProductEl2.find('.nbdl-realated-product-color-base').addClass('nbdl-hidden');
                    }

                    realatedProductEl2.find('.nbdl-realated-product-base').attr('data-src', setting.img_src);
                    realatedProductEl2.find('.nbdl-realated-product-overlay').attr('data-src', setting.img_overlay);

                    relatedProdcutWrap.append( realatedProductEl2 );
                });
            }
            setTimeout(function(){
                initRelatedProductFunc();
            });
        };

        var initRelatedProductFunc = function(){
            $('.nbdl-realated-product .nbdl-releted-btn').off('click').on('click', function(){
                $(this).parents('.nbdl-realated-product-inner').find('input[type="checkbox"]').prop('checked', true);
                $(this).parents('.nbdl-realated-product-inner').find('input[type="checkbox"]').trigger('change');
            });

            $('.nbdl-realated-product input[type="checkbox"]').off('change').on('change', function(){
                var id = $(this).val(),
                index = checkedRelatedProduct.indexOf( id );

                if( $(this).prop('checked') ){
                    if( index ) checkedRelatedProduct.push( id );
                }else{
                    checkedRelatedProduct.splice( index, 1 );
                }

                updateRelatedProductList();
            });

            processRelatedProducts();
        };

        var updateRelatedProductList = function(){
            var getProduct = function( id ){
                var _product;
                relatedProductData[currentNbdlProduct].products.map(function(product){
                    if( product.product_id == id ) _product = product;
                });
                return _product;
            };

            $('.nbdl-selected-product-row.nbdl-related').remove();
            checkedRelatedProduct.map(function(id){
                var product = getProduct(id);
                var realatedListEl2 = realatedListEl.clone();
                realatedListEl2.removeClass('nbdl-hidden').find('.nbdl-selected-product-name').html( product.name );
                realatedListEl2.find('input[type="checkbox"]').val(product.product_id);
                sideBodyPhase2.append(realatedListEl2);
            });

            setTimeout(function(){
                $('.nbdl-selected-product-row.nbdl-related input[type="checkbox"]').off('change').on('change', function(){
                    var id = $(this).val();
                    $.each($('.nbdl-realated-product-inner input[type="checkbox"]'), function(){
                        if( $(this).val() == id ){
                            $(this).prop('checked', false).trigger('change');
                        }
                    });
                });
            });
        };

        var processRelatedProduct = function( el ){
            var self = $(el),
            processed = self.attr('data-loaded');
            if( processed != 1 ){
                self.attr('data-loaded', '1');

                var productBase = self.find('.nbdl-realated-product-base'),
                overlayProduct = self.find('.nbdl-realated-product-overlay');

                if( productBase.attr('data-image') == '1' ){
                    var src = productBase.attr('data-src');
                    productBasef.prepend('<img class="nbdl-realated-product-base-img nbdl-hidden" src="' + src + '"/>');
                }else{
                    productBase.find('.nbdl-realated-product-base-loading').addClass('nbdl-hidden');
                }

                if( overlayProduct.attr('data-show') == '1' ){
                    var src = overlayProduct.attr('data-src');
                    overlayProduct.prepend('<img class="nbdl-realated-product-overlay-img nbdl-hidden" src="' + src + '"/>');
                }

                setTimeout(function(){
                    $.each(self.find('.nbdl-realated-product-base-img, .nbdl-realated-product-overlay-img'), function(){
                        var loadImg = $(this).next(),
                        _self = $(this),
                        url = _self.attr('src'),
                        img = new Image;
                        _self.css({
                            width: _self.parent().css('width'),
                            height: _self.parent().css('height')
                        })
                        img.onload = function() {
                            _self.removeClass('nbdl-hidden');
                            loadImg.addClass('nbdl-hidden');
                            if( _self.hasClass('nbdl-realated-product-overlay-img') ){
                                _self.parents('.nbdl-realated-product-image-wrap').find('.nbdl-realated-product-color-base').removeClass('nbdl-hidden');
                            }

                            var designArea = _self.parents('.nbdl-realated-product-preview').find('.nbdl-realated-content-design-area');
                            if( designArea.attr('data-insert') == '0' ){
                                var img = document.createElement("img"),
                                areaWidth = parseFloat( designArea.attr('data-width') ),
                                areaHeight = parseFloat( designArea.attr('data-height') ),
                                css = calcSizePreviewPosition( currentData.setting[0].design_preview_width, currentData.setting[0].design_preview_height, areaWidth, areaHeight );
                                img.src = currentData.setting[0].design_preview_url;
                                $( img ).css( css );
                                designArea.append( img ).attr('data-insert', '1');
                            }
                        };
                        img.src = url
                    });
                });
            }

        };

        var processRelatedProducts = function( el ){
            var realatedProducts = document.querySelectorAll('.nbdl-realated-product');
            [].forEach.call(realatedProducts, function(productEl) {
                if( isInViewport( productEl ) ){
                    processRelatedProduct(productEl);
                }
            });
        };

        $( '.nbdl-upload-form-stage' ).on('scroll', function(){
            processRelatedProducts();
        });

        var submitProduct = function(){
            toggleLoading();
            var formData = new FormData;
            formData.append('action', 'nbdl_submit_product');
            formData.append('product_id', currentNbdlProduct);
            formData.append('related_product_ids', checkedRelatedProduct.join(','));
            formData.append('tags', $('#nbdl-side-tags').val());
            formData.append('name', $('#nbdl-design-name').val());
            formData.append('nonce', nbdl.nonce);
            $.each($('input[type="file"]'), function(){
                var self = $(this),
                checkType = self.attr('data-check'),
                isSidePreview = self.attr('name') == 'nbdl-side-preview[]' ? true : false,
                editValue = self.next('input.edit-value'),
                val;
                if( currentNbdlDesign != 0 && editValue.length ){
                    val = editValue.val();
                }else{
                    val = self[0].files[0];
                }
                if( isSidePreview ){
                    var index = self.parents('.nbdl-uf-side-preview-upload').attr('data-index');
                    formData.append(checkType + '__' + index, val);
                }else{
                    formData.append(checkType, val);
                }
            });
            if( currentNbdlDesign != 0 ){
                formData.append('design_id', currentNbdlDesign);
                formData.append('task', 'edit');
            }
            $.ajax({
                xhr: function () {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener("progress", function (evt) {
                        if (evt.lengthComputable) {
                            var percent = ((evt.loaded / evt.total) * 100).toFixed();
                            submitIndicator.html(percent);
                        }
                    }, false);
                    return xhr;
                },
                url: nbdl.ajax_url,
                method: "POST",
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: formData,
                complete: function() {
                    toggleLoading();
                },
                success: function(data) {
                    if( data.flag == 1 ){
                        location.reload();
                    }else if( data.flag == 0 ){
                        alert( data.message );
                    }
                },
                error: function( jqXHR ){
                    console.log( jqXHR );
                }
            });
        };

        var openRelatedProductsPanel = function(){
            relatedProdcutPanel.addClass('active');
            sideBodyPhase2.removeClass('nbdl-hidden');
            sideBodyPhase1.addClass('nbdl-hidden');

            var headerText = sidebarHeader.attr('data-text-phase-2');
            sidebarHeader.html(headerText);
        }

        var closeRelatedProductsPanel = function(){
            currentPhase = 1;
            relatedProdcutPanel.removeClass('active');
            sideBodyPhase1.removeClass('nbdl-hidden');
            sideBodyPhase2.addClass('nbdl-hidden');

            var headerText = sidebarHeader.attr('data-text-phase-1');
            sidebarHeader.html(headerText);
        }

        triggerBtn.on('click', function(e){
            e.preventDefault();
            initLauncher();
        });

        closePopupBtn.on('click', function(){
            popupWrap.removeClass('active');
            $('body').removeClass('nbd-prevent-scroll');
        });

        optionsInnerWrap.on('click', function(e){
            e.stopPropagation();
        });

        optionsWrap.on('click', function(e){
            $(this).removeClass('active');
        });

        product.on('click', function(e){
            var product_id = $(this).attr('data-id'),
            allow_upload = $(this).attr('data-upload');
            currentNbdlProduct = product_id;
            currentNbdlDesign = 0;
            if( allow_upload != "1" ){
                window.location = nbdl.create_design_url + '&product_id=' + product_id;
            }else{
                jQuery('#nbdl-popup').animate({
                    scrollTop: 0
                }, 300);
                optionsWrap.addClass('active');
            }
        });

        actionBtn.on('click', function(e){
            e.stopPropagation();
            if( $(this).hasClass('nbdl-design-action') ){
                window.location = nbdl.create_design_url + '&product_id=' + currentNbdlProduct;
            }else{
                showUploadForm();
            }
        });

        backLinkBtn.on('click', function(e){
            if( currentPhase == 1 ){
                closeUploadForm();
            }else{
                closeRelatedProductsPanel();
            }
        });

        editDesignBtn.on('click', function(e){
            e.preventDefault();
            var product_id = jQuery(this).attr('data-product-id'),
            design_id = jQuery(this).attr('data-design-id');
            currentNbdlProduct = product_id;
            currentNbdlDesign = design_id;
            initLauncher();
            showUploadForm(true);
        });

        saveDesignBtn.on('click', function(e){
            e.preventDefault();
            if( checkAvailableForSubmit() ){
                if( currentPhase == 1 ){
                    getRelatedProducts();
                    currentPhase = 2;
                }else{
                    var text = saveDesignBtn.attr('data-text-submit');
                    saveDesignBtn.find('span').text(text);
                    submitProduct();
                }
            }else{
                alert(nbdl.msg_alert_missing_file);
            }
        });

        init();
    });
})(jQuery, window, document);