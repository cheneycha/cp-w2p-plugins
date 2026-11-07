jQuery(document).ready(function ($) {
    "use strict";
    $("table.wc_gateways tbody").sortable({
        items: "tr",
        cursor: "move",
        axis: "y",
        handle: "td.sort",
        scrollSensitivity: 40,
        helper: function(t, e) {
            return e.children().each(function() {
                $(this).width($(this).width())
            }),
            e.css("left", "0"),
            e
        },
        start: function(t, e) {
            e.item.css("background-color", "#f6f6f6")
        },
        stop: function(t, e) {
            e.item.removeAttr("style"),
            e.item.trigger("updateMoveButtons")
        }
    });
    var add_new = $('#add-new'),
    add_new_input = $('#add-new-name'),
    fields_add_edit_form = $("#nbdq_field_add_edit_form"),
    main_table = $('#nbdq_form_fields'),
    init_dialog_form = function (form, title, action, row, is_custom) {
        form.attr('data-row', row);
        form.attr('data-action', action);
        if (!is_custom) {
            var input = form.find('tr.remove_default');
            if (input.length) input.remove();
        }
        form.find('select[name="field_type"]').on('change', function () {
            var input = form.find('tr[data-hide]'),
                value = $(this).val();
            if (!input.length) return;
            $.each(input, function () {
                var deps = $(this).data('hide').split(',');
                if ($.inArray(value, deps) > -1) {
                    $(this).hide();
                }
                else {
                    $(this).show();
                }
            });
        }).trigger('change');
        form.dialog({
            title    : title,
            modal    : true,
            width    : 500,
            resizable: false,
            autoOpen : false,
            buttons  : [{
                text : nbdq_admin.default_form_submit_label,
                click: function () {
                    if ($.edit_add_field(this)) {
                        $(this).dialog("close");
                    }else{
                        alert(nbdq_admin.duplicate);
                    }
                }
            }],
            close    : function (event, ui) {
                form.dialog("destroy");
                form.remove();
            }
        });
    },
    format_name = function (name) {
        name = name.trim();
        name = name.replace(/\s/g, "_");
        return name;
    };
    /* OPEN ADD POPUP */
    add_new_input.on('focus', function () {
        $(this).removeClass('required field-exists');
    });
    add_new.on('click', function () {
        var exists,
            val = add_new_input.val();
        if (val == '') {
            add_new_input.addClass('required');
            return false;
        }
        else {
            val = format_name(val);
            exists = main_table.find('input.field_name[value="' + val + '"]');
            if (exists.length) {
                add_new_input.addClass('field-exists');
                return false;
            }
            else {
                var the_form = fields_add_edit_form.clone();
                init_dialog_form(the_form, nbdq_admin.popup_add_title, 'add', '', true);
                the_form.find('input[name="field_name"]').val(val);
                the_form.dialog('open');
            }
        }
    });
    /* OPEN EDIT POPUP */
    $(document).on('click', 'button.edit_field', function () {
        var tr = $(this).closest('tr'),
            row = tr.data('row'),
            is_email_field = false,
            input = tr.find('input[type="hidden"]');
        var the_form = fields_add_edit_form.clone();
        $.each(input, function (i, hidden) {
            var name = $(hidden).data('name'),
                form_input = the_form.find('td *[name="' + name + '"]');
            if( name == 'field_name' && $(hidden).val() == 'email' ){
                is_email_field = true;
            }
            if (form_input.attr('type') == 'checkbox') {
                var value = $(hidden).val();
                if (value == 0) {
                    form_input.removeAttr('checked');
                }else {
                    form_input.attr('checked', 'checked');
                }
            }
            else {
                form_input.val($(hidden).val());
            }
        });
        if( is_email_field ){
            ['field_name', 'field_validate', 'field_required', 'field_type'].forEach(function(item, index){
                the_form.find('td *[name="' + item + '"]').prop('disabled', true);
            });
        }
        init_dialog_form(the_form, nbdq_admin.popup_edit_title, 'edit', row, tr.hasClass('is_custom'));
        the_form.dialog('open');
    });
    /* EDIT ADD FIELD HANDLER */
    $.edit_add_field = function (form) {
        var fields = main_table.find('tbody tr'),
            action = $(form).data('action'),
            new_field,
            field_name = $(form).find('input[name="field_name"]').val(),
            exists = main_table.find('input.field_name[value="' + field_name + '"]'),
            index;
        if (action == 'edit') {
            index = $(form).data('row');
            new_field = fields.filter('[data-row="' + index + '"]');
            if (exists.length) {
                if( exists.length > 1 ) return false;
                if( jQuery(exists[0]).closest('tr').attr('data-row') != index ) return false;
            }
        }else {
            new_field = fields.filter(':not(.disabled-row)').last().clone();
            index = fields.size();
            new_field.attr('data-row', index);
            new_field.addClass('is_custom');
            if (exists.length) {
                return false;
            }
        }
        $.each(new_field.find('input[type="hidden"]'), function (i, hidden) {
            var name = $(hidden).data('name'),
                form_input = $(form).find('td *[name="' + name + '"]'),
                value = '',
                value_td = '';
            if (form_input.length) {
                if (form_input.attr('type') == 'checkbox') {
                    value = form_input.is(':checked') ? 1 : 0;
                    value_td = value == 1 ? nbdq_admin.enabled : '-';
                }else {
                    value = form_input.val();
                    if (name == 'field_name') {
                        value = format_name(value);
                    }
                    value_td = value;
                }
                $(hidden).val(value);
                if( typeof nbdq_admin[value_td] != 'undefined' ){
                    value_td = nbdq_admin[value_td];
                }
                new_field.find('.td_' + name).html(value_td);
            }
        });
        if (action == 'add') {
            fields.last().after(new_field);
        }
        return true;
    };
    /* BULK ACTION */
    $('.check-column input').on('change', function () {
        var t = $(this),
            fields_check = $('td.td_select input');
        if ($(this).is(':checked')) {
            fields_check.attr('checked', 'checked');
        }else {
            fields_check.removeAttr('checked');
        }
    });
    /* DISABLE/ENABLE FIELDS */
    $(document).on('click', 'button.enable_field', function () {
        var button = $(this),
            row = button.closest('tr'),
            enable_hidden = row.find('input[data-name="field_enabled"]'),
            button_label;
        row.toggleClass('disabled-row');
        if (enable_hidden.length) {
            enable_hidden.val(row.hasClass('disabled-row') ? '0' : '1');
        }
        button_label = button.html();
        button.html(button.data('label'));
        button.data('label', button_label);
    });
    /* REMOVE CUSTOM FIELDS */
    var reindex_row = function () {
        var tr = main_table.find('tbody tr');
        tr.each(function (i) {
            $(this).attr('data-row', i);
        });
    };
    $(document).on('click', 'button.remove_field', function () {
        var button = $(this),
            row = button.closest('tr');
        if (!row.hasClass('is_custom')) {
            return;
        }
        row.fadeOut(400, function () {
            row.addClass('disabled-row').hide();
            row.find('input[data-name="field_deleted"]').val('yes');
        });
    });
});