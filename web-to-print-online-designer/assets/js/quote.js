var rqa_captcha;
function nbdq_recaptcha(){
    var raq_recaptcha = jQuery('form[name="nbdq-form"]').find('.g-recaptcha');
    if (typeof grecaptcha != "undefined" &&  raq_recaptcha.length > 0 ) {
        rqa_captcha = grecaptcha.render('recaptcha_quote', {'sitekey' : raq_recaptcha.data('sitekey')});
        jQuery('.raq-send-request').addClass('nbdq-disabled2');
    }
}
var nbdqRecaptchaCallback = function(){
    jQuery('.raq-send-request').removeClass('nbdq-disabled2');
};
var nbd_stored_design = false;
!function (i) {
    i.fn.timepicki = function (t) {
        var e = {
            format_output: function (i, t, e) {
                return n.show_meridian ? i + " : " + t + " : " + e : i + " : " + t
            },
            increase_direction: "down",
            custom_classes: "",
            min_hour_value: 1,
            max_hour_value: 12,
            show_meridian: !0,
            step_size_hours: "1",
            step_size_minutes: "1",
            overflow_minutes: !1,
            disable_keyboard_mobile: !1,
            reset: !1,
            on_change: null
        }, n = i.extend({}, e, t);
        return this.each(function () {
            function t(t) {
                return i.contains(m[0], t[0]) || m.is(t)
            }

            function e(i, t) {
                var e = f.find(".ti_tx input").val(), a = f.find(".mi_tx input").val(), r = "";
                n.show_meridian && (r = f.find(".mer_tx input").val()), 0 === e.length || 0 === a.length || n.show_meridian && 0 === r.length || (l.attr("data-timepicki-tim", e), l.attr("data-timepicki-mini", a), n.show_meridian ? (l.attr("data-timepicki-meri", r), l.val(n.format_output(e, a, r))) : l.val(n.format_output(e, a))), null !== n.on_change && n.on_change(l[0]), t && s()
            }

            function a() {
                r(n.start_time), f.fadeIn();
                var t = f.find("input:visible").first();
                t.focus();
                var e = function (n) {
                    if (9 === n.which && n.shiftKey) {
                        t.off("keydown", e);
                        var a = i(":input:visible:not(.timepicki-input)"), s = a.index(l), r = a.get(s - 1);
                        r.focus()
                    }
                };
                t.on("keydown", e)
            }

            function s() {
                f.fadeOut()
            }

            function r(i) {
                var t, e, a, s;
                l.is("[data-timepicki-tim]") ? (e = Number(l.attr("data-timepicki-tim")), a = Number(l.attr("data-timepicki-mini")), n.show_meridian && (s = l.attr("data-timepicki-meri"))) : "object" == typeof i ? (e = Number(i[0]), a = Number(i[1]), n.show_meridian && (s = i[2])) : (t = new Date, e = t.getHours(), a = t.getMinutes(), s = "AM", e > 12 && n.show_meridian && (e -= 12, s = "PM")), 10 > e ? f.find(".ti_tx input").val("0" + e) : f.find(".ti_tx input").val(e), 10 > a ? f.find(".mi_tx input").val("0" + a) : f.find(".mi_tx input").val(a), n.show_meridian && (10 > s ? f.find(".mer_tx input").val("0" + s) : f.find(".mer_tx input").val(s))
            }

            function o(i, t) {
                var e = "time", a = Number(f.find("." + e + " .ti_tx input").val()), s = Number(n.min_hour_value),
                    r = Number(n.max_hour_value), o = Number(n.step_size_hours);
                if (i && i.hasClass("action-next") || "next" === t) if (a + o > r) {
                    var d = s;
                    d = 10 > d ? "0" + d : String(d), f.find("." + e + " .ti_tx input").val(d)
                } else a += o, 10 > a && (a = "0" + a), f.find("." + e + " .ti_tx input").val(a); else if (i && i.hasClass("action-prev") || "prev" === t) {
                    var u = Number(n.min_hour_value);
                    if (u > a - o) {
                        var l = r;
                        l = 10 > l ? "0" + l : String(l), f.find("." + e + " .ti_tx input").val(l)
                    } else a -= o, 10 > a && (a = "0" + a), f.find("." + e + " .ti_tx input").val(a)
                }
            }

            function d(i, t) {
                var e = "mins", a = Number(f.find("." + e + " .mi_tx input").val()), s = 59,
                    r = Number(n.step_size_minutes);
                i && i.hasClass("action-next") || "next" === t ? a + r > s ? (f.find("." + e + " .mi_tx input").val("00"), n.overflow_minutes && o(null, "next")) : (a += r, 10 > a ? f.find("." + e + " .mi_tx input").val("0" + a) : f.find("." + e + " .mi_tx input").val(a)) : (i && i.hasClass("action-prev") || "prev" === t) && (-1 >= a - r ? (f.find("." + e + " .mi_tx input").val(s + 1 - r), n.overflow_minutes && o(null, "prev")) : (a -= r, 10 > a ? f.find("." + e + " .mi_tx input").val("0" + a) : f.find("." + e + " .mi_tx input").val(a)))
            }

            function u(i, t) {
                var e = "meridian", n = null;
                n = f.find("." + e + " .mer_tx input").val(), i && i.hasClass("action-next") || "next" === t ? "AM" == n ? f.find("." + e + " .mer_tx input").val("PM") : f.find("." + e + " .mer_tx input").val("AM") : (i && i.hasClass("action-prev") || "prev" === t) && ("AM" == n ? f.find("." + e + " .mer_tx input").val("PM") : f.find("." + e + " .mer_tx input").val("AM"))
            }

            var l = i(this), c = l.outerHeight();
            c += 10, i(l).wrap("<div class='time_pick'>");
            var m = i(this).parents(".time_pick"),
                v = "down" === n.increase_direction ? "<div class='prev action-prev'></div>" : "<div class='prev action-next'></div>",
                p = "down" === n.increase_direction ? "<div class='next action-next'></div>" : "<div class='next action-prev'></div>",
                _ = i("<div class='timepicker_wrap " + n.custom_classes + "'><div class='arrow_top'></div><div class='time'>" + v + "<div class='ti_tx'><input type='text' class='timepicki-input'" + (n.disable_keyboard_mobile ? "readonly" : "") + "></div>" + p + "</div><div class='mins'>" + v + "<div class='mi_tx'><input type='text' class='timepicki-input'" + (n.disable_keyboard_mobile ? "readonly" : "") + "></div>" + p + "</div>");
            n.show_meridian && _.append("<div class='meridian'>" + v + "<div class='mer_tx'><input type='text' class='timepicki-input' readonly></div>" + p + "</div>"), n.reset && _.append("<div><a href='#' class='reset_time'>Reset</a></div>"), m.append(_);
            var f = i(this).next(".timepicker_wrap"), h = (f.find("div"), m.find("input"));
            i(".reset_time").on("click", function (i) {
                l.val(""), s()
            }), i(".timepicki-input").keydown(function (t) {
                var e = i(this).val().length;
                -1 !== i.inArray(t.keyCode, [46, 8, 9, 27, 13, 110, 190]) || 65 == t.keyCode && t.ctrlKey === !0 || t.keyCode >= 35 && t.keyCode <= 39 || ((t.shiftKey || t.keyCode < 48 || t.keyCode > 57) && (t.keyCode < 96 || t.keyCode > 105) || 2 == e) && t.preventDefault()
            }), i(document).on("click", function (n) {
                if (!i(n.target).is(f) && "block" == f.css("display") && !i(n.target).is(i(".reset_time"))) if (i(n.target).is(l)) {
                    var s = 0;
                    f.css({top: c + "px", left: s + "px"}), a()
                } else e(n, !t(i(n.target)))
            }), l.on("focus", a), h.on("focus", function () {
                var t = i(this);
                t.is(l) || t.select()
            }), h.on("keydown", function (t) {
                var e, a = i(this);
                38 === t.which ? e = "down" === n.increase_direction ? "prev" : "next" : 40 === t.which && (e = "down" === n.increase_direction ? "next" : "prev"), a.closest(".timepicker_wrap .time").length ? o(null, e) : a.closest(".timepicker_wrap .mins").length ? d(null, e) : a.closest(".timepicker_wrap .meridian").length && n.show_meridian && u(null, e)
            }), h.on("blur", function () {
                setTimeout(function () {
                    var n = i(document.activeElement);
                    n.is(":input") && !t(n) && (e(), s())
                }, 0)
            });
            var x = f.find(".action-next"), k = f.find(".action-prev");
            i(k).add(x).on("click", function () {
                var t = i(this);
                "time" == t.parent().attr("class") ? o(t) : "mins" == t.parent().attr("class") ? d(t) : n.show_meridian && u(t)
            })
        })
    }
}(jQuery);
jQuery(document).ready(function ($) {
    $('.nbdq-add-a-quote-button').on('click', function (e) {
        e.preventDefault();
        if( $(this).hasClass('nbdesigner_disable') || $(this).hasClass('nbo-disabled') ){
            alert(nbds_frontend.check_invalid_fields);
            return;
        }else{
            $('#nbdq-form-popup').nbqShowPopup();
        }
    });
    var nbdq_form_init = function () {
        var select = $('.nbd-multiselect, .nbdq-popup select'),
        datepicker = $('.nbd-datepicker'),
        timepicker = $('.nbd-timepicker'),
        nbdq_form = $('form[name="nbdq-form"]'),
        submit_button = $('.raq-send-request'),
        error = '<span class="nbdq_error"></span>',
        is_email = function (val) {
            var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
            return re.test(val);
        },
        show_error = function (elem, msg) {
            if (!elem.next('.nbdq_error').length) {
                elem.after(error);
            }
            elem.next('.nbdq_error').html(msg);
        },
        validate_field = function () {
            var t = $(this),
                parent = t.closest('p.form-row'),
                value = t.val();
            if (!value && parent.hasClass('validate-required')) {
                show_error(t, nbdq_form_obj.err_msg);
            } else if (value && parent.hasClass('validate-email') && !is_email(value)) {
                show_error(t, nbdq_form_obj.err_msg_mail);
            } else {
                t.next('.nbdq_error').remove();
            }
            validate_form();
        },
        validate_form = function () {
            var check = true;
            $.each( nbdq_form.find('select, input:checkbox, .input-text'), function(){
                var t = $(this);
                var parent = t.closest('p.form-row'),
                    value = t.val();
                if( !(( t.attr('id') == 'account_password' || t.attr('id') == 'account_username' ) && $('#createaccount').length > 0 && !$('#createaccount').is(':checked')) ){
                    if (!value && parent.hasClass('validate-required')) {
                        check = false;
                    } else if (value && parent.hasClass('validate-email') && !is_email(value)) {
                        check = false;
                    }
                }
            });
            if( check ){
                $('.raq-send-request').removeClass('nbdq-disabled');
            }else{
                $('.raq-send-request').addClass('nbdq-disabled');
            }
        },
        scroll_to_notices = function () {
//            var scrollElement = $('.woocommerce-error, .woocommerce-message'),
//                scrollElementWrap = nbdq_form_obj.show_popup == '1' ? nbdq_form : $('html, body');
            $('html, body').animate({
                scrollTop: (jQuery('#nbdq-form-popup .main-popup').position().top - 100)
            }, 1000);
        },
        submit_form = function (e) {
            e.preventDefault();
            var formData =  new FormData(),
            cartForm = jQuery('form.cart, form.variations_form'),
            cartFormData = cartForm.serializeArray(),
            requestFormData = nbdq_form.serializeArray();
            $.each(cartFormData, function (i, val) {
                if (val.name) {
                    var new_name = val.name == 'add-to-cart' ? 'nbd-add-to-cart' : val.name;
                    formData.append(new_name, val.value);
                }
            });   
            if( typeof cartFormData['nbd-add-to-cart'] != 'undefined' ){
                if (cartForm.find('[name="add-to-cart"]').length) {
                    formData.append('nbd-add-to-cart', cartForm.find('[name="add-to-cart"]').val());
                } else {
                    formData.append('nbd-add-to-cart', jQuery('#nbdq-quote-btn').attr('data-id'));
                }
            }
            $.each(cartForm.find("input[type='file']"), function (i, tag) {
                $.each($(tag)[0].files, function (i, file) {
                    formData.append(tag.name, file);
                });
            });
            $.each(requestFormData, function (i, val) {
                if (val.name) {
                    formData.append(val.name, val.value);
                }
            });
            $.ajax({
                url: nbdq_form_obj.ajaxurl + '?action=nbdq_submit_raq_form',
                type: 'POST',
                dataType: 'json',
                data: formData,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    nbdq_form.addClass( 'processing' ).block( {
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });
                },
                complete: function () {
                    submit_button.prop('disabled', false).next().remove();
                },
                success: function (response) {
                    nbdq_form.unblock();
                    if ('success' === response.result) {
                        $('.nbdq-popup').find('.close-popup').trigger('click');
                        if( response.redirect ){
                            //window.location.href = response.redirect;
                            $('#nbdq-alert-detail-link').show().attr('href', response.redirect);
                        }else{
                            $('#nbdq-alert-detail-link').hide();
                        }
                        $('#nbdq-alert-popup').showAlert();
                    }
                    if ('failure' === response.result) {
                        $('.woocommerce-error, .woocommerce-message').remove();
                        if (response.messages) {
                            nbdq_form.prepend('<div class="woocommerce-error woocommerce-message">' + response.messages + '</div>');
                        }
                        nbdq_form.find('.input-text, select, input:checkbox').trigger('validate').blur();
                        scroll_to_notices();
                        if (typeof grecaptcha != "undefined") {
                            grecaptcha.reset( rqa_captcha );
                        }
                    }
                }
            });
            return false;
        },
        toggle_create_account = function () {
            $('div.create-account').hide();
            if ($(this).is(':checked')) {
                $('#account_password').val('').change();
                $('div.create-account').slideDown();
            }
        };
        if (select && typeof $.fn.select2 != 'undefined') {
            $.each(select, function () {
                var s = $(this),
                    sid = s.attr('id');
                if ($('#s2id_' + sid).length) {
                    return;
                }
                s.select2({
                    placeholder: s.data('placeholder'),
                    dropdownCssClass: 'nbdq-select2'
                });
            });
        }
        if (typeof $.fn.datepicker != 'undefined' && datepicker) {
            $.each(datepicker, function () {
                var self = $(this);
                $(this).datepicker({
                    dateFormat: $(this).data('format') || "dd-mm-yy",
                    beforeShow: function () {
                        setTimeout(function () {
                            var dptop = self.parent('.form-row').position().top + $('#nbdq-form-popup .main-popup').position().top;
                            $('#ui-datepicker-div').wrap('<div class="nbdq_datepicker"></div>').css({
                                'z-index': 99999999999999,
                                'top': dptop
                            });
                            $('#ui-datepicker-div').show();
                        }, 0);
                    },
                    onClose: function () {
                        $('#ui-datepicker-div').hide();
                        $('#ui-datepicker-div').unwrap();
                    }
                });
            });
        }
        if (typeof $.fn.timepicki != 'undefined' && timepicker) {
            $.each(timepicker, function () {
                $(this).timepicki({
                    reset: true,
                    disable_keyboard_mobile: true,
                    show_meridian: nbdq_form_obj.time_format,
                    max_hour_value: nbdq_form_obj.time_format ? '12' : '23',
                    min_hour_value: nbdq_form_obj.time_format ? '1' : '0',
                    overflow_minutes: true,
                    increase_direction: 'up'
                });
            });
            $(document).on('click', '.reset_time', function (ev) {
                ev.preventDefault();
            });
        }
        nbdq_form.on('blur', '.input-text', validate_field);
        nbdq_form.on('change', 'select, input:checkbox', validate_field);
        nbdq_form.on('submit', function(e){
            if( window.preventSubmitFormCart && !nbd_stored_design ){
                e.preventDefault();
                nbdq_form.addClass( 'processing' ).block( {
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
                var scope = angular.element(document.getElementById("designer-controller")).scope();
                scope.saveData('quote');
                return false;
            }
            submit_form( e );
        });
        $(document).find('input#createaccount').on('change', toggle_create_account).change();
        validate_form();
    };
    nbdq_form_init(false);
    if( jQuery('form.cart input[name="variation_id"]').length > 0 ){
        if(jQuery('form.cart input[name="variation_id"]').val() > 0){
            jQuery('#nbdq-quote-btn').removeClass('nbdesigner_disable');
        }else{
            jQuery('#nbdq-quote-btn').addClass('nbdesigner_disable');
        }
        jQuery('form.cart input[name="variation_id"]').on('change', function(){
            if(jQuery('form.cart input[name="variation_id"]').val() > 0){
                jQuery('#nbdq-quote-btn').removeClass('nbdesigner_disable');
            }else{
                jQuery('#nbdq-quote-btn').addClass('nbdesigner_disable');
            }
        });
    }
    jQuery(document).on('nbd_design_stored', function( e, data ){
        nbd_stored_design = true;
        if( data._type == 'quote' ){
            $('form[name="nbdq-form"]').submit();
        }
    });
});
jQuery(document).on('nbo_valid_form', function(){
    jQuery('#nbdq-quote-btn').removeClass('nbo-disabled');
});
jQuery(document).on('nbo_invalid_form', function(){
    jQuery('#nbdq-quote-btn').addClass('nbo-disabled');
});