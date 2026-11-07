function checkMobileDevice(){
    var isMobile = false;
    if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|ipad|iris|kindle|Android|Silk|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(navigator.userAgent)
        || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(navigator.userAgent.substr(0,4))) isMobile = true;

    var app = document.getElementsByClassName('nbd-mode-vista')[0];
    if (typeof app !== null) {
        if (app.getBoundingClientRect().width < 786) {
            isMobile = true;
        }
    }
    return isMobile;
};


(function ($) {
    /**
     *
     * @desc Component Tab
     * @version 2.0.0
     * @author Netbase Online Design Team
     */
    $.fn.nbVTab = function () {
        return this.each(function () {
            var $tab = $(this).find('.v-tab');
            var $tabContent = $('.v-tab-contents .v-tab-content');
            $tab.on('click', function () {
                var tabId = $(this).attr('data-tab');
                $tab.removeClass('active');
                $(this).addClass('active');
                $tabContent.removeClass('active');
                $('.v-tab-contents #' + tabId).addClass('active');
            });
        });
    };

    $.fn.nbdTab = function () {
        return this.each(function () {
            var $tab = $(this).find('.nbd-nav-tab'), $tabContents = $(this).find('.nbd-tab-contents'),
                $tabContent = $tabContents.find('.nbd-tab-content');
            $tab.on('click', function () {
                var tabId = $(this).data('tab');
                $tab.removeClass('active');
                $(this).addClass('active');
                $tabContent.removeClass('active');
                $tabContents.find('[data-tab=' + tabId + ']').addClass('active');
            });
        });
    };

    /**
     *
     * @desc Component Dropdown
     * @version 2.0.0
     * @author Netbase Online Design Team
     */
    $.fn.nbDropdown = function () {
        return this.each(function () {
            var sefl = this,
                $btn = $(this).find('.v-btn-dropdown'),
                $icon = $(this).find('.v-dropdown-icon');

            $(this).nbClickOutSite({
                'clickE' : $(this),
                'activeE': $(this),
                'removeClass' : 'active'
            });

            $btn.on('click', function () {
                if ($(sefl).hasClass('active')) {
                    $(sefl).removeClass('active');
                    $icon.removeClass('rotate180');
                }else {
                    $(sefl).addClass('active');
                    $icon.addClass('rotate180');
                }
            });
        });
    };

    /**
     *
     * @param options
     * @desc Ele click out
     * @version 2.0.0
     * @author Netbase Online Design Team
     * @option - clickE (object: jquery) : Khi click vào sẽ không removeClass
     *         - activeE (object: jquery): phần tử chứa class remove
     *         - removeClass (string: ''): Class bị remove
     */

    $.fn.nbClickOutSite = function (options) {
        var defaults = {
            'clickE' : null,
            'activeE' : null,
            'removeClass' : '',
        };
        var opts = $.extend({}, $.fn.nbClickOutSite.defaults, options);
        return this.each(function () {
            var sefl = this;
            var $win = $(document);
            if (opts.activeE == null) {
                opts.activeE = $(this);
            }
            $win.on("click", function(event){
                if ($(sefl).has(event.target).length == 0 && !$(sefl).is(event.target)
                    && opts.clickE.has(event.target).length == 0 && !opts.clickE.is(event.target)){
                    opts.activeE.removeClass(opts.removeClass);
                }
            });
        });
    }

    /**
     * @desc Library perfect scroll
     * @version 2.0.0
     * @author Netbase Online Design Team
     */
    $.fn.nbPerfectScrollbar = function () {
        return this.each(function () {
            new PerfectScrollbar(this);
        });
    };

    /**
     *
     * @param options
     * @desc
     * @version 2.0.0
     * @author Netbase Online Design Team
     *
     */
    $.fn.nbElDropdown = function (options) {
        var sefl = this;
        var defaults = {
            'itemInRow': 3,
        };
        var opts = $.extend({}, $.fn.nbDropdown.defaults, options);
        this.initPositionItem = function (items, item, itemInRow, itemDistance) {

            var leftItem = items.width() / itemInRow;
            var topItem = item.height() + itemDistance;
            item.show();
            item.each(function () {
                var index = $(this).index();
                var indexMod = index % itemInRow;
                var indexI = parseInt(index / itemInRow);
                $(this).css({
                    'left': leftItem * indexMod + 'px',
                    'top' : topItem * indexI + 'px'
                });
            });

        };
        this.getItemInRow = function ($grid, $item) {
            var itemInRow = opts.itemInRow;
            if (typeof $grid == null || typeof $item == null) {
                return itemInRow;
            }
            var numberItem = $item.length,
                widthItem = $item.outerWidth(true),
                widthGrid = $grid.width();

            itemInRow = Math.min(parseInt(widthGrid / widthItem), numberItem);
            window.addEventListener('resize', function () {
                widthGrid = $grid.width();
                itemInRow = Math.min(parseInt(widthGrid / widthItem), numberItem);
            });

            return itemInRow;
        };

        return this.each(function () {
            var $items = $(this).find('.items');
            var $item = $(this).find('.item');
            var $mainItems = $(this).find('.main-items');
            var $resultLoaded = $(this).find('.result-loaded');
            var $galleryItem = $(this).find('.nbdesigner-gallery');
            var $contentItem = $(this).find('.result-loaded .content-item');
            var $loadingGif = $(this).find('.loading-photo');
            var $tabScroll = $(this).closest('.tab-scroll');
            var $infoSupport = $(this).closest('.v-content').find('.info-support');

            // ========================= Main================================================
            $item.on('click', function () {
                var indexItem = $(this).index();
                var indexItemRow = parseInt(indexItem / opts.itemInRow) + 1;
                var itemName = $(this).find('.item-name').text();
                var widthItem = $(this).outerWidth();
                var dataType = $(this).attr('data-type');
                var dataApi = $(this).attr('data-api');

                // =============== Canculate the anoubt of flexbox item in row =================
                opts.itemInRow = sefl.getItemInRow($items, $item);

                if (dataType == 'webcam') {
                    // $('.nbd-vista .v-popup-webcam').nbShowPopup();
                    return;
                }
                $infoSupport.find('span').text(itemName);
                $mainItems.find('.pointer').css({
                    // 'left': ((widthItem) * (indexItem % opts.itemInRow + 1) - widthItem / 2)  + 'px'
                    'left': ($(this).offset().left - $mainItems.offset().left + widthItem / 2)  + 'px'
                });

                if (dataApi == 'false') {
                    $resultLoaded.show().addClass('overflow-visible');
                    $contentItem.filter(function (index) {
                        return $(this).attr('data-type') === dataType;
                    }).show();
                    $galleryItem.hide();
                    if (!$mainItems.hasClass('active-expanded')) {
                        $(this).siblings().css({
                            'opacity': '0.5'
                        });
                        $mainItems.addClass('active-expanded');
                        $resultLoaded.addClass('loaded');
                        var nextAllItem = $items.find('.item:nth-child(' + indexItemRow * opts.itemInRow + ')').nextAll();
                        $(nextAllItem).each(function () {
                            $(this).hide();
                        });
                    }else {
                        $(this).css({
                            'opacity': '1'
                        });
                        $(this).siblings().css({
                            'opacity': '1'
                        });
                        $mainItems.removeClass('active-expanded');
                        $contentItem.hide();
                        $resultLoaded.removeClass('loaded');
                        $loadingGif.hide();
                        $item.show();
                    }
                    // return false;
                }else {
                    if (!$mainItems.hasClass('active-expanded')) {
                        $(this).siblings().css({
                            'opacity': '0.5'
                        });
                        var nextAllItem = $items.find('.item:nth-child(' + indexItemRow * opts.itemInRow + ')').nextAll();
                        $(nextAllItem).each(function () {
                            $(this).hide();
                        });

                        $mainItems.addClass('active-expanded');
                        $resultLoaded.addClass('loaded');
                        $resultLoaded.show();
                        $galleryItem.show();
                        $contentItem.hide();
                    }else {
                        $(this).css({
                            'opacity': '1'
                        });
                        $(this).siblings().css({
                            'opacity': '1'
                        });
                        $mainItems.removeClass('active-expanded');
                        $resultLoaded.removeClass('loaded');
                        $resultLoaded.hide();
                        $resultLoaded.removeClass('loaded');
                        $loadingGif.hide();
                        $galleryItem.hide();
                        $item.show();
                    }

                    // return false;
                }

                // Event click in close result
                $infoSupport.find('.close-result-loaded').on('click', function () {
                    $infoSupport.removeClass('nbd-show');
                    $mainItems.removeClass('active-expanded');
                    $resultLoaded.hide();
                    $resultLoaded.removeClass('loaded');
                    $galleryItem.hide();
                    $item.show();
                    $loadingGif.hide();
                    $item.show().css({'opacity' : '1'});
                    $tabScroll.scrollTop(0);
                    $contentItem.hide();
                });

            });
        });

    };

    /**
     *  @author Netbase Online Design Team
     */
    $.fn.nbShowPopup = function () {
        return this.each(function () {
            var sefl = this;
            var $close = $(this).find('.overlay-popup, .close-popup');
            if (!$(this).hasClass('nb-show')) {
                $(this).addClass('nb-show');
            }
            $close.on('click', function () {
                $(sefl).removeClass('nb-show');
            });
        });
    };

    /**
     *
     * @param text
     * @version 2.0.0
     * @author Netbase Online Design Team
     */
    $.fn.nbWarning = function (text) {
        return this.each(function () {
            var $itemWarning = $(this).find('.item');
            $(this).addClass('nbd-show');
            if ($itemWarning.length < 3) {
                var htmlWaring = '<div class="item animate300 animated nbScaleOut main-warning nbd-show">' +
                    '<i class="nbd-icon-vista nbd-icon-vista-warning warning"></i>' +
                    '<span class="title-warning">'+ text +'</span>' +
                    '<i class="nbd-icon-vista nbd-icon-vista-clear close-warning"></i>' +
                    '</div>';
                var $warning = $(htmlWaring);
                var $close = $warning.find('.close-warning');
                $(this).append($warning);
                $close.on('click', function () {
                    $warning.removeClass('nbScaleOut').addClass('nbScaleIn');
                    $warning.bind('webkitAnimationEnd oanimationend msAnimationEnd animationend', function () {
                        $warning.remove();
                    });
                });

                setTimeout(function () {
                    $warning.removeClass('nbScaleOut').addClass('nbScaleIn');
                    $warning.bind('webkitAnimationEnd oanimationend msAnimationEnd animationend', function () {
                        $warning.remove();
                    });
                }, 10000);

            }

        });
    };

    $.fn.nbdColorPalette = function (options) {
        var defaults = {};
        var opts = $.extend({}, $.fn.nbdColorPalette.defaults, options);
        return this.each(function () {
            var sefl = this;
            $(this).on('click', function (e) {
                var $colorPalette = $(this).closest('.toolbox-color-palette').find('.nbd-color-palette'),
                    $triangleBox = $colorPalette.find('.v-triangle-box'),
                    $toolboxPalete = $(this).closest('.toolbox-color-palette'),
                    $closeColorPalette = $colorPalette.find('.close-block');
                var posL = 0;

                $toolboxPalete.addClass('nbd-show-palette');
                if (!$colorPalette.hasClass('show')) {
                    $colorPalette.addClass('show');
                }

                posL = $(this).offset().left - $colorPalette.offset().left + $(this).outerWidth() / 2;
                $triangleBox.css({
                    'left': posL
                });

                $('.v-toolbox-path .toolbox-color-palette').nbClickOutSite({
                    'clickE': $colorPalette,
                    'activeE': $colorPalette,
                    'removeClass': 'show'
                });
                $closeColorPalette.on('click', function () {
                    $toolboxPalete.removeClass('nbd-show-palette');
                    $colorPalette.removeClass('show');
                });
            });
        });
    };

    /**
     *
     * @param text
     * @version 2.0.0
     * @author Netbase Online Design Team
     */
    $.fn.nbToasts = function (text) {
        return this.each(function () {
            var $toast = $(this).find('.toast');
            var sefl = $(this);
            $(this).addClass('nbd-show');
            $toast.addClass('nbSlideInUp');
            $toast.find('.nbd-close-toast').on('click', function () {
                $toast.removeClass('nbSlideInUp').addClass('nbSlideInDown');
                $toast.bind('webkitAnimationEnd oanimationend msAnimationEnd animationend', function () {
                    sefl.removeClass('nbd-show');
                });
            });
            if(t) clearTimeout(t);
            var t = setTimeout(function () {
                if( sefl.hasClass('nbd-show') ){
                    $toast.removeClass('nbSlideInUp').addClass('nbSlideInDown');
                    $toast.bind('webkitAnimationEnd oanimationend msAnimationEnd animationend', function () {
                        sefl.removeClass('nbd-show');
                    });
                }else{
                    clearTimeout(t);
                }
            }, 3000);
        });
    };

    $.fn.nbRipple = function (color) {
        return this.each(function () {
            $(this).on('mousedown', function (e) {
                var $self = $(this), colorWave;

                if (typeof color !== 'undefined') {
                    colorWave = color;
                }else{
                    colorWave = $self.data("ripple");
                }
                if($self.is(".btn-disabled")) {
                    return;
                }
                if($self.closest("[data-ripple]")) {
                    e.stopPropagation();
                }

                var initPos = $self.css("position"),
                    offs = $self.offset(),
                    x = e.pageX - offs.left,
                    y = e.pageY - offs.top,
                    dia = Math.min(this.offsetHeight, this.offsetWidth, 100), // start diameter
                    $ripple = $('<div/>', {class : "nb-ripple",appendTo : $self });

                if(!initPos || initPos==="static") {
                    $self.css({position:"relative"});
                }

                $('<div/>', {
                    class : "nb-rippleWave",
                    css : {
                        background: colorWave,
                        width: dia,
                        height: dia,
                        left: x - (dia/2),
                        top: y - (dia/2),
                    },
                    appendTo : $ripple,
                    one : {
                        animationend : function(){
                            $ripple.remove();
                        }
                    }
                });
            });
        });
    };

    $.fn.nbShowMoreTool = function () {
        return this.each(function () {
            var sefl = $(this);
            var $btnMore = sefl.find('.link-more'), $btnBack = sefl.find('.link-back'), $mainBox = sefl.find('.main-box'), $boxMore = sefl.find('.main-box-more');
            $btnMore.on('click', function () {
                sefl.addClass('show-box-more');
            });
            $btnBack.on('click', function () {
                sefl.removeClass('show-box-more');
            });


        });
    };

    var nbdVista = {
        init : function () {
            $('body').addClass('nbd-designer');

            this.checkTerm();
            this.animate();
            this.hasNbdMobile();
        },
        hasNbdMobile: function () {
            if ($('.nbd-mode-vista').hasClass('nbd-mobile')) {

                var $sideBar = $('.nbd-vista .v-sidebar'), $layout = $('.nbd-vista .v-layout');

                // init
                $('.nbd-vista #design-tab').addClass('active');
                $('.nbd-vista .v-toolbar [data-tab="tab-design"]').removeClass('active');
                $layout.addClass('active');
                $sideBar.removeClass('active');

                $('.nbd-vista .left-toolbar .v-menu-item').on('click', function () {
                    if ($(this).hasClass('v-tab-layer')) {
                        $layout.addClass('active');
                        $sideBar.removeClass('active');
                    }else {
                        $sideBar.addClass('active');
                        $layout.removeClass('active');
                    }
                });
            }
        },
        mobile: function () {

            // swipe
            var $toolbar = $('.nbd-vista .v-toolbar .left-toolbar'), $toolbarItem = $toolbar.find('.v-menu-item'),
                $tabMainContent = $('.nbd-vista .v-sidebar .v-tab-contents .tab-scroll');
            $tabMainContent.on({
                'swiperight': function () {
                    var $currentBar = $toolbarItem.filter('.active'),
                        curSl = $toolbar.scrollLeft(), tabW = $currentBar.width();
                    $currentBar.prev().trigger('click');
                    $toolbar.animate({scrollLeft: curSl - tabW}, 300);
                },
                'swipeleft': function () {
                    var $currentBar = $toolbarItem.filter('.active'),
                        curSl = $toolbar.scrollLeft(), tabW = $currentBar.width();
                    $currentBar.next().trigger('click');
                    $toolbar.animate({scrollLeft: curSl + tabW}, 300);
                }
            });
        },
        checkTerm : function () {
            var $termCheck = $('.type-upload .nbd-term .nbd-checkbox input');
            var $formUpload = $('.type-upload .form-upload');
            var isUpload = false;
            $termCheck.on('click', function () {
                var $typeUpload = $(this).closest('.type-upload');
                if ($(this).is(':checked')) {
                    $typeUpload.addClass('accept');
                    isUpload = true;
                }else {
                    $typeUpload.removeClass('accept');
                    isUpload = false;
                }
            });
            $formUpload.on('click', function () {
                if (!isUpload) {
                    //alert('Please accept the upload term conditions');
                    return false;
                }
            });
        },
        animate: function () {
            // toolbar click
            var $toolbar = $('.nbd-vista .v-toolbar .left-toolbar .tabs-toolbar'),
                $toolbarItem = $toolbar.find('.v-tab'), $selectTab = $toolbar.find('#selectedTab');
            // if ($('.nbd-mode-vista').hasClass('nbd-mobile')) {
            //     $selectTab.hide();
            // }

            // init
            // $selectTab.css({
            //     'width': $toolbarItem.filter('.active').outerWidth()
            // });

            $('.v-tab-contents .v-tab-content.active').prevAll().addClass('nbd-left');
            $('.v-tab-contents .v-tab-content.active').nextAll().addClass('nbd-right');

            $toolbarItem.on('click', function () {
                // $selectTab.css({
                //     'width': $(this).outerWidth(),
                //     'left': $(this).offset().left - $toolbar.offset().left
                // });
                var tabId = $(this).data('tab'), $tabContent = $('.v-tab-contents #' + tabId);
                $('.v-tab-contents .v-tab-content').removeClass('nbd-left nbd-right');
                $tabContent.prevAll().addClass('nbd-left');
                $tabContent.nextAll().addClass('nbd-right');
            });
        }

    };

    //$(document).ready(function () {
    window.initVisualLayout = function(){
        nbdVista.init();
        $('.nbd-vista .v-tabs').nbVTab();
        $('.nbd-vista .nbd-main-tab').nbdTab();
        $('.nbd-vista .v-dropdown').nbDropdown();
        $('.nbd-vista .tab-scroll').perfectScrollbar();

        $('.nbd-vista .v-elements').nbElDropdown({
            'itemInRow' : 3
        });
        $('.nbd-vista .click-reset-design').on('click', function () {
            $('.nbd-vista .v-popup-select').nbShowPopup();
        });
        $('.nbd-vista .nbd-term .term-read').on('click', function () {
            $('.nbd-vista .v-popup-terms').nbShowPopup();
        });
        $('.nbd-color-palette .color-palette-add').on('click', function () {
            var $colorPalette = $(this).closest('.nbd-color-palette');
            var $colorPicker = $colorPalette.find('.nbd-text-color-picker');
            if ($colorPicker.hasClass('active')) {
                $colorPicker.removeClass('active');
            }else {
                $colorPicker.addClass('active');
            }
            $colorPicker.nbClickOutSite({
                'clickE' : $(this),
                'activeE' : null,
                'removeClass' : 'active'
            });
        });

        $('.nbd-vista .v-dropdown').each(function (e) {
            if ($(this).find('.nbd-color-palette').length) {
                $(this).find('.nbd-color-palette').addClass('show');
            }
        });
        //$('[data-ripple],.nbd-vista .color-palette-item, .nbd-vista .v-btn, .nbd-vista .v-assets .v-asset, .nbd-ripple').nbRipple('rgba(0,0,0, 0.1)');
    };
    $(window).on('load', function () {
        $('.nbd-vista .loading-app').removeClass('nbd-show');
        if( !checkMobileDevice() ){
            $('.nbd-vista .v-toolbar .v-menu-item').each(function (i, e) {
                var animateTime = (i + 4) * 100;
                $(this).addClass('slideInDown animated animate' + animateTime);
            });

            $('.nbd-vista .v-sidebar').addClass('animated slideInLeft animate800');
            $('.nbd-vista .v-layout').addClass('animated slideInRight animate800');
        }
    });

})(jQuery);