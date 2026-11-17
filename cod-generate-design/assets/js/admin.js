jQuery(document).ready(function($) {
    var idButton = null;
    $(document).on('click', 'input[type="submit"]', function(e) {
        idButton = jQuery(this).attr('id');
    })

    $(document).on('submit', 'body.post-type-shop_order #posts-filter', function(e) {
        var $this = $(this),
            val = $this.find('#bulk-action-selector-top').val();

        if(idButton == 'doaction') {
            if( val == 'generate_files' ) {
                e.preventDefault();
            }

            var $btn = $this.find('#doaction');
            var ids = [];
            $('#the-list th.check-column :checkbox:checked').each(function(i){
                ids[i] = $(this).val();
            });

            if( ids.length <= 0 ) {
                alert('Please choose order to generate.');
                return;
            }

            jQuery.ajax({
                type: 'POST',
                url: cod_gdf.ajax_url,
                data: {
                    action: 'cod_gdf_generate_all',
                    ids: ids
                },
                beforeSend: function (response) {
                    $btn.prop('disabled', true).css('opacity', 0.5);
                    $this.find('.bulkactions').append('<div class="alt-bulk_status-generate">Generating, please wait...<img src="' + cod_gdf.path_loading + '" /></div>');
                },
                complete: function (response) {
                    $btn.prop('disabled', false).css('opacity', 1);
                    $('.alt-bulk_status-generate').remove();
                },
                success: function (response) {
                    $btn.closest('tr').remove();
                },
            });
        }
    });

    $(document).on('click', '#button-generate-order-detail', function(e) {
        e.preventDefault();

        var $btn = $(this),
            orderId = parseInt( $(this).attr('data-id') );

        var text = $btn.text();
        

        jQuery.ajax({
            type: 'POST',
            url: cod_gdf.ajax_url,
            data: {
                action: 'cod_gdf_order_detail',
                id: orderId
            },
            beforeSend: function (response) {
                $btn.prop('disabled', true).css('opacity', 0.5);
                $btn.html('Generating...<img src="' + cod_gdf.path_loading + '" />');
            },
            complete: function (response) {
                $btn.prop('disabled', false).css('opacity', 1);
                
                $btn.text(text);
            },
            success: function (response) {
                $btn.closest('tr').remove();
            },
        });
    });


    $(document).on('click', '#button-download-order-detail', function(e) {
        e.preventDefault();

        var $btn = $(this),
            orderId = parseInt( $(this).attr('data-id') ),
            val = [];

        $('.nbdesigner_design_file:checkbox:checked').each(function(i){
            val[i] = $(this).val();
        });

        jQuery.ajax({
            type: 'POST',
            url: cod_gdf.ajax_url,
            data: {
                action: 'cod_gdf_order_detail_download',
                id: orderId,
                files: val
            },
            beforeSend: function (response) {
                $btn.prop('disabled', true).css('opacity', 0.5);
            },
            complete: function (response) {
                $btn.prop('disabled', false).css('opacity', 1);
            },
            success: function (response) {
                $btn.closest('tr').remove();

                if( typeof response.complete != 'undefined'  ) {
                    window.location.href =response.url;;
                }
            },
        });
    });

    $(document).on('click', '.col-button-detail-download', function(e) {
        e.preventDefault();


        var $btn = $(this),
            orderId = parseInt( $(this).attr('data-id') ),
            type = $(this).attr('data-type');

        jQuery.ajax({
            type: 'POST',
            url: cod_gdf.ajax_url,
            data: {
                action: 'cod_gdf_order_detail_download_type',
                id: cod_gdf.order_id,
                type: type,
                item_key: cod_gdf.item_key
            },
            beforeSend: function (response) {
                $btn.prop('disabled', true).css('opacity', 0.5);
            },
            complete: function (response) {
                $btn.prop('disabled', false).css('opacity', 1);
            },
            success: function (response) {
                $btn.closest('tr').remove();

                if( typeof response.complete != 'undefined'  ) {
                    window.location.href =response.url;;
                }

            },
        });
    });

    if( $('#nbd-convert-section .nbd-convert-con').length > 0 ) {
        if( typeof cod_gdf.disable_generate != 'undefined' ) {
            $(document).on('click', '.nbd-convert-con a.button-primary', function(e) {
                if( $(this).closest('.nbd-convert-con-inner').find('.cod-generate-time-detail').length > 0 ) {
                    if (confirm("Design is not change. Do you want to regenerate file?") != true) {
                        e.preventDefault();
                    }
                }
            });
        }

        if( typeof cod_gdf.cod_generate != 'undefined') {
            $('#nbd-convert-section .nbd-convert-con').each(function(index) {
                var timestamp = null;
    
                var label = '';
                if( index == 0 ) {
                    label = 'SVG';
                    $(this).addClass('nbd-convert-svg');

                    if( typeof cod_gdf.cod_generate['svg'] != 'undefined' ) {
                        timestamp = cod_gdf.cod_generate['svg'];
                    }
                }
    
                if( index == 1 ) {
                    label = 'PNG';
                    $(this).addClass('nbd-convert-png');

                    if( typeof cod_gdf.cod_generate['png-hires'] != 'undefined' ) {
                        timestamp = cod_gdf.cod_generate['png-hires'];
                    }
                }
    
                if( index == 2 ) {
                    label = 'JPG';
                    $(this).addClass('nbd-convert-jpg');

                    if( typeof cod_gdf.cod_generate['jpg-hires'] != 'undefined' ) {
                        timestamp = cod_gdf.cod_generate['jpg-hires'];
                    }
                }
    
                if( index == 3 ) {
                    label = 'PDF';
                    $(this).addClass('nbd-convert-pdf');

                    if( typeof cod_gdf.cod_generate['pdf'] != 'undefined' ) {
                        timestamp = cod_gdf.cod_generate['pdf'];
                    }
                }
    
                
                if( timestamp ) {
                    var html = '';
                    html += '<div class="cod-generate-time-detail">';
                        html += '<button type="button" class="button-primary col-button-detail-download" data-type="' + label + '">Download</button><p>' + label + ' file generated at ' + timestamp + '</p>';
                    html += '</div>';
        
                    $(this).find('.nbd-convert-con-inner').append(html);
                }
    
            });
        }

    }


    var _columns = [
        { "data": "order_id" },
        { "data": "item_key" },
        { "data": "spacer" },
        { "data": "gemerate_time" },
        { "data": "status" },
        { "data": "delete_time" }
    ];

    $('#table-cronjob-listing').DataTable({
        searching: true,
        // "responsive": true,
        "scrollX": true,
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": cod_gdf.ajax_url,
            "type": "POST",
            "data": function ( d ) {
                var laraForm = {
                    'action': 'cod_cronjob_listing'
                };

                return $.extend({}, laraForm, d);
            },
            "dataSrc": function ( json ) {
                $('.table-body').removeClass('loaded');
                $('.has-dataTable').removeClass('first-dataTable');

                return json.data;
            },
        },
        "columns": _columns
    });

});