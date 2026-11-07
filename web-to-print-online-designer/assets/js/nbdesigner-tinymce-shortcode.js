var nbds_open_editor = false;
if(typeof(tinymce) !== 'undefined'){
    tinymce.PluginManager.add('nbdesigner_button', function (editor, url) {
        editor.addButton('nbdesigner_button', {
            title: 'NBDesigner Shortcode Creator',
            icon: 'icon nbdesigner-icon',
            type: 'menubutton',
            menu: [
                {
                    text : 'Gallery',
                    onclick: function () {
                        editor.windowManager.open(
                            {
                                id       : 'nbdesiger-tiny-mce-dialog',
                                title	 : 'Shortcode Creator',
                                width    : 500,
                                height   : 'auto',
                                wpDialog : true
                            },
                            {
                                plugin_url : url // Plugin absolute URL
                            }
                        );
                    }
                },
                {
                    text : 'Template',
                    onclick: function () {
                        editor.windowManager.open(
                            {
                                id       : 'nbdesiger-tiny-mce-dialog-template',
                                title	 : 'Shortcode Creator',
                                width    : 500,
                                height   : 'auto',
                                wpDialog : true
                            },
                            {
                                plugin_url : url // Plugin absolute URL
                            }
                        );
                    }
                }
            ]
        });
        nbds_open_editor = editor;
    });
}
jQuery('body').on('change', '#nbdesigner-pagination', function(){
    jQuery('#nbdesigner-number-row').slideToggle();
});
jQuery('body').on('click', '#nbdesigner-shortcode-create', function(){
    var per_row = jQuery('#nbdesigner-shortcode-number').val(),
    row = jQuery('#nbdesigner-shortcode-number-row').val(),
    pagination = jQuery('#nbdesigner-pagination').prop('checked');
    var content = '[nbdesigner_gallery row="'+row+'" pagination="'+pagination+'" per_row="'+per_row+'" ][/nbdesigner_gallery]';
    tinyMCE.activeEditor.selection.setContent( content );
    if(nbds_open_editor !== false){
        tinyMCE.activeEditor.windowManager.close();
    }    
});
jQuery('body').on('click', '#nbdesigner-shortcode-template-create', function(){
    var per_row = jQuery('#nbdesigner-shortcode-number').val(),
        limit = jQuery('#nbdesigner-shortcode-limit-template').val();
        // pagination = jQuery('#nbdesigner-pagination').prop('checked');
    var content = '[nbd_template limit="'+limit+'" per_row="'+per_row+'" ][/nbd_template]';
    tinyMCE.activeEditor.selection.setContent( content );
    if(nbds_open_editor !== false){
        tinyMCE.activeEditor.windowManager.close();
    }
});