(function($){
	$.fn.tipTip = function(options) {
		var defaults = {
			activation: "hover",
			keepAlive: false,
			maxWidth: "200px",
			edgeOffset: 3,
			defaultPosition: "bottom",
			delay: 400,
			fadeIn: 200,
			fadeOut: 200,
			attribute: "title",
			content: false, // HTML or String to fill TipTIp with
		  	enter: function(){},
		  	exit: function(){}
	  	};
	 	var opts = $.extend(defaults, options);

	 	// Setup tip tip elements and render them to the DOM
	 	if($("#tiptip_holder").length <= 0){
	 		var tiptip_holder = $('<div id="tiptip_holder" style="max-width:'+ opts.maxWidth +';"></div>');
			var tiptip_content = $('<div id="tiptip_content"></div>');
			var tiptip_arrow = $('<div id="tiptip_arrow"></div>');
			$("body").append(tiptip_holder.html(tiptip_content).prepend(tiptip_arrow.html('<div id="tiptip_arrow_inner"></div>')));
		} else {
			var tiptip_holder = $("#tiptip_holder");
			var tiptip_content = $("#tiptip_content");
			var tiptip_arrow = $("#tiptip_arrow");
		}

		return this.each(function(){
			var org_elem = $(this);
			if(opts.content){
				var org_title = opts.content;
			} else {
				var org_title = org_elem.attr(opts.attribute);
			}
			if(org_title != ""){
				if(!opts.content){
					org_elem.removeAttr(opts.attribute); //remove original Attribute
				}
				var timeout = false;

				if(opts.activation == "hover"){
					org_elem.hover(function(){
						active_tiptip();
					}, function(){
						if(!opts.keepAlive){
							deactive_tiptip();
						}
					});
					if(opts.keepAlive){
						tiptip_holder.hover(function(){}, function(){
							deactive_tiptip();
						});
					}
				} else if(opts.activation == "focus"){
					org_elem.focus(function(){
						active_tiptip();
					}).blur(function(){
						deactive_tiptip();
					});
				} else if(opts.activation == "click"){
					org_elem.click(function(){
						active_tiptip();
						return false;
					}).hover(function(){},function(){
						if(!opts.keepAlive){
							deactive_tiptip();
						}
					});
					if(opts.keepAlive){
						tiptip_holder.hover(function(){}, function(){
							deactive_tiptip();
						});
					}
				}

				function active_tiptip(){
					opts.enter.call(this);
					tiptip_content.html(org_title);
					tiptip_holder.hide().removeAttr("class").css("margin","0");
					tiptip_arrow.removeAttr("style");

					var top = parseInt(org_elem.offset()['top']);
					var left = parseInt(org_elem.offset()['left']);
					var org_width = parseInt(org_elem.outerWidth());
					var org_height = parseInt(org_elem.outerHeight());
					var tip_w = tiptip_holder.outerWidth();
					var tip_h = tiptip_holder.outerHeight();
					var w_compare = Math.round((org_width - tip_w) / 2);
					var h_compare = Math.round((org_height - tip_h) / 2);
					var marg_left = Math.round(left + w_compare);
					var marg_top = Math.round(top + org_height + opts.edgeOffset);
					var t_class = "";
					var arrow_top = "";
					var arrow_left = Math.round(tip_w - 12) / 2;

                    if(opts.defaultPosition == "bottom"){
                    	t_class = "_bottom";
                   	} else if(opts.defaultPosition == "top"){
                   		t_class = "_top";
                   	} else if(opts.defaultPosition == "left"){
                   		t_class = "_left";
                   	} else if(opts.defaultPosition == "right"){
                   		t_class = "_right";
                   	}

					var right_compare = (w_compare + left) < parseInt($(window).scrollLeft());
					var left_compare = (tip_w + left) > parseInt($(window).width());

					if((right_compare && w_compare < 0) || (t_class == "_right" && !left_compare) || (t_class == "_left" && left < (tip_w + opts.edgeOffset + 5))){
						t_class = "_right";
						arrow_top = Math.round(tip_h - 13) / 2;
						arrow_left = -12;
						marg_left = Math.round(left + org_width + opts.edgeOffset);
						marg_top = Math.round(top + h_compare);
					} else if((left_compare && w_compare < 0) || (t_class == "_left" && !right_compare)){
						t_class = "_left";
						arrow_top = Math.round(tip_h - 13) / 2;
						arrow_left =  Math.round(tip_w);
						marg_left = Math.round(left - (tip_w + opts.edgeOffset + 5));
						marg_top = Math.round(top + h_compare);
					}

					var top_compare = (top + org_height + opts.edgeOffset + tip_h + 8) > parseInt($(window).height() + $(window).scrollTop());
					var bottom_compare = ((top + org_height) - (opts.edgeOffset + tip_h + 8)) < 0;

					if(top_compare || (t_class == "_bottom" && top_compare) || (t_class == "_top" && !bottom_compare)){
						if(t_class == "_top" || t_class == "_bottom"){
							t_class = "_top";
						} else {
							t_class = t_class+"_top";
						}
						arrow_top = tip_h;
						marg_top = Math.round(top - (tip_h + 5 + opts.edgeOffset));
					} else if(bottom_compare | (t_class == "_top" && bottom_compare) || (t_class == "_bottom" && !top_compare)){
						if(t_class == "_top" || t_class == "_bottom"){
							t_class = "_bottom";
						} else {
							t_class = t_class+"_bottom";
						}
						arrow_top = -12;
						marg_top = Math.round(top + org_height + opts.edgeOffset);
					}

					if(t_class == "_right_top" || t_class == "_left_top"){
						marg_top = marg_top + 5;
					} else if(t_class == "_right_bottom" || t_class == "_left_bottom"){
						marg_top = marg_top - 5;
					}
					if(t_class == "_left_top" || t_class == "_left_bottom"){
						marg_left = marg_left + 5;
					}
					tiptip_arrow.css({"margin-left": arrow_left+"px", "margin-top": arrow_top+"px"});
					tiptip_holder.css({"margin-left": marg_left+"px", "margin-top": marg_top+"px"}).attr("class","tip"+t_class);

					if (timeout){ clearTimeout(timeout); }
					timeout = setTimeout(function(){ tiptip_holder.stop(true,true).fadeIn(opts.fadeIn); }, opts.delay);
				}

				function deactive_tiptip(){
					opts.exit.call(this);
					if (timeout){ clearTimeout(timeout); }
					tiptip_holder.fadeOut(opts.fadeOut);
				}
			}
		});
	}
})(jQuery);
var AD_NBD_OPTIONS = {
    render_price_matrix: function(){
        
    }
};
angular.module('optionApp', []).controller('optionCtrl', function( $scope, $timeout, pmFieldFilter, bulkFieldFilter, allFieldsFilter, manualPMFieldFilter ) {
    /* init parameters */
    $scope.showPreview = false;
    $scope.previewWide = false;
    $scope.jsonFields  = '';
    $scope.formula     = {
        active: false,
        price: '',
        brIndex: null,
        fieldIndex: null,
        opIndex: null,
        saIndex: null,
        currentLinkField: '0'
    };
    /* end init parameters */
    /* quantity */
    $scope.validate_quantity_break = function(){
        
    };
    $scope.excludeField = function(actual, expected){
        var _field = null;
        angular.forEach($scope.options.fields, function(field){
            if( field.id == actual ) _field = field;
        });
        if( _field.nbd_type == 'page' || _field.nbd_type == 'page2' || _field.nbd_type == 'dimension' ) return false;
        if( _field.general.enabled.value == 'n' ) return false;
        return actual != expected;
    };
    $scope.includeField = function(actual, expected){
        return actual == expected;
    };
    $scope.add_price_break = function(){
        var last =  $scope.options.quantity_breaks.length > 0 ? $scope.options.quantity_breaks[$scope.options.quantity_breaks.length - 1].val : 0; 
        $scope.options.quantity_breaks.push({ val: parseInt( last ) + 1 });
    };
    $scope.remove_price_break = function( index ){
        if( $scope.options.quantity_breaks.length == 1 ) return;
        $scope.options.quantity_breaks.splice(index, 1);
    }; 
    $scope.update_default_quantity = function( index ){
        angular.forEach($scope.options.quantity_breaks, function(qty){
            qty.default = 0;
        });
        $scope.options.quantity_breaks[index].default = 1;
    };
    /* end. quantity */
    $scope.add_field = function(type, ftype){
        var field = {};
        angular.copy(NBDOPTION_FIELD, field);
        var d = new Date();
        field['id'] = 'f' + d.getTime();
        field.isExpand = true;
        var extra_options = ['delivery', 'actions', 'frame', 'number_file'];
        if(  angular.isDefined( type ) && extra_options.indexOf( type ) != -1){
            field.nbd_template = 'nbd.' + type;
            field.nbe_type = type;
            field.general.title.value = nbd_options.nbd_options_lang[type];
            switch( type ){
                case 'delivery':
                    field.general.attributes.options[0].name = nbd_options.nbd_options_lang.delivery_3_days;
                    break;
                case 'actions':
                    field.general.attributes.options[1] = {};
                    field.general.attributes.options[2] = {};
                    field.general.attributes.options[3] = {};
                    angular.copy(field.general.attributes.options[0], field.general.attributes.options[1]); 
                    angular.copy(field.general.attributes.options[0], field.general.attributes.options[2]); 
                    angular.copy(field.general.attributes.options[0], field.general.attributes.options[3]); 
                    field.general.attributes.options[0].name = nbd_options.nbd_options_lang.no_thank;
                    field.general.attributes.options[0].action = 'n';
                    field.general.attributes.options[1].name = nbd_options.nbd_options_lang.upload_design;
                    field.general.attributes.options[1].action = 'u';
                    field.general.attributes.options[2].name = nbd_options.nbd_options_lang.create_your_own;
                    field.general.attributes.options[2].action = 'c';
                    field.general.attributes.options[3].name = nbd_options.nbd_options_lang.hire_designer;
                    field.general.attributes.options[3].action = 'h';
                    break;
            }
        }
        if( angular.isDefined( type ) && extra_options.indexOf( type ) == -1 ){
            field.general.title.value = nbd_options.nbd_options_lang[type];
            field.nbd_template = 'nbd.' + type;
            if( angular.isUndefined( ftype ) ){
                if( angular.isDefined($scope.nbd_options[type]) && type != 'builder' && $scope.nbd_options[type] == 1 ){
                    //alert(nbd_options.nbd_options_lang.option_exist);
                    //return;
                }else{
                    $scope.nbd_options[type] = 1;
                }
                field.nbd_type = type;
                switch(type){
                    case 'dpi': 
                        field.general.input_option.value.min = 72;
                        field.general.input_option.value.max = 600;
                        field.general.data_type.value = 'i';
                        field.general.data_type.hidden = true;
                        field.general.input_type.value = 'r';
                        //field.general.input_type.hidden = true;
                        field.general.description.value = nbd_options.nbd_options_lang.dpi_description;
                        break;
                    case 'page': 
                        field.general.data_type.value = 'i';
                        //field.general.data_type.hidden = true;
                        field.general.input_type.value = 'n';
                        field.general.input_type.hidden = true;
                        field.general.price_type.value = 'c';
                        //field.general.price_type.hidden = true;
                        //field.general.required.value = 'y';
                        //field.general.required.hidden = true;
                        field.general.page_display = '1';
                        field.general.exclude_page = '0';
                        field.general.attributes.options[1] = {};
                        //field.general.attributes.options[2] = {};
                        angular.copy(field.general.attributes.options[0], field.general.attributes.options[1]); 
                        //angular.copy(field.general.attributes.options[0], field.general.attributes.options[2]); 
                        field.general.attributes.options[0].name = nbd_options.nbd_options_lang.front;
                        field.general.attributes.options[1].name = nbd_options.nbd_options_lang.back;
                        //field.general.attributes.options[2].name = nbd_options.nbd_options_lang.both;
                        //field.general.attributes.add_att = false;
                        //field.general.attributes.remove_att = false;
                        break;
                    case 'page1': 
                        field.general.data_type.value = 'i';
                        field.general.data_type.hidden = true;
                        field.general.input_type.value = 'n';
                        field.general.input_type.hidden = true;
                        field.general.price_type.value = 'c';
                        field.general.price_type.hidden = true;
                        //field.general.required.value = 'y';
                        //field.general.required.hidden = true;
                        field.general.page_display = '1';
                        field.general.exclude_page = '0';
                        field.general.price_depend_no = 'n';
                        field.general.price_no_range = [];
                        break;
                    case 'page2': 
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        field.general.price_type.value = 'f';
                        //field.general.price_type.hidden = true;
                        //field.general.required.value = 'y';
                        //field.general.required.hidden = true;
                        field.general.auto_select_page = 'y';
                        field.general.attributes.options[1] = {};
                        angular.copy(field.general.attributes.options[0], field.general.attributes.options[1]); 
                        field.general.attributes.options[0].name = nbd_options.nbd_options_lang.front;
                        field.general.attributes.options[1].name = nbd_options.nbd_options_lang.back;
                        break;
                    case 'page3': 
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        field.general.price_type.value = 'f';
                        //field.general.price_type.hidden = true;
                        //field.general.required.value = 'y';
                        //field.general.required.hidden = true;
                        field.general.attributes.add_att = false;
                        field.general.attributes.remove_att = false;
                        field.general.attributes.options[1] = {};
                        field.general.attributes.options[2] = {};
                        angular.copy(field.general.attributes.options[0], field.general.attributes.options[1]); 
                        angular.copy(field.general.attributes.options[0], field.general.attributes.options[2]); 
                        field.general.attributes.options[0].name = nbd_options.nbd_options_lang.front;
                        field.general.attributes.options[1].name = nbd_options.nbd_options_lang.back;
                        field.general.attributes.options[2].name = nbd_options.nbd_options_lang.both;
                        break;
                    case 'color':
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        field.general.attributes.bg_type = 'i';
                        field.general.attributes.number_of_sides = 4;
                        angular.forEach(field.general.attributes.options, function(op){
                            op.bg_image = [];
                            op.bg_image_url = [];
                        });
                        if( angular.isUndefined( field.general.attributes.show_as_pt ) ){
                            field.general.attributes.show_as_pt = 'n';
                        }
                        break;
                    case 'orientation':
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        field.general.attributes.options[1] = {};
                        angular.copy(field.general.attributes.options[0], field.general.attributes.options[1]); 
                        field.general.attributes.options[0].name = nbd_options.nbd_options_lang.vertical;
                        field.general.attributes.options[1].name = nbd_options.nbd_options_lang.horizontal;
                        //field.general.attributes.add_att = false;
                        //field.general.attributes.remove_att = false;
                        break;
                    case 'area':
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        field.general.attributes.options[1] = {};
                        angular.copy(field.general.attributes.options[0], field.general.attributes.options[1]); 
                        field.general.attributes.options[0].name = nbd_options.nbd_options_lang.rectangle;
                        field.general.attributes.options[1].name = nbd_options.nbd_options_lang.ellipse;
                        field.general.attributes.add_att = false;
                        field.general.attributes.remove_att = false;
                        break;
                    case 'shape':
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        field.general.attributes.options[0].name = nbd_options.nbd_options_lang.shape_name;
                        break;
                    case 'size':
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        field.general.attributes.same_size = 'y';
                        break;
                    case 'dimension':
                        field.general.data_type.value = 'i';
                        field.general.data_type.hidden = true;
                        field.general.input_type.value = 't';
                        field.general.input_type.hidden = true;
                        field.general.mesure = 'n';
                        field.general.mesure_type = 'u';
                        field.general.mesure_min_area = 0;
                        field.general.mesure_base_pages = 'y';
                        field.general.mesure_base_qty = 'n';
                        field.general.mesure_range = [];
                        break;
                    case 'padding':
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        break;
                    case 'rounded_corner':
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        break;
                    case 'overlay':
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        break;
                    case 'fold':
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        field.general.attributes.options[0].fold = 'n';
                        break;
                    case 'pt_color':    // custom charming
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        field.general.attributes.add_att = false;
                        field.general.attributes.remove_att = true;
                        // field.general.attributes.options[1] = {};
                        // field.general.attributes.options[2] = {};
                        // angular.copy(field.general.attributes.options[0], field.general.attributes.options[1]); 
                        // angular.copy(field.general.attributes.options[0], field.general.attributes.options[2]); 
                        // field.general.attributes.options[0].name = nbd_options.nbd_options_lang.pt_color1;
                        // field.general.attributes.options[1].name = nbd_options.nbd_options_lang.pt_color2;
                        // field.general.attributes.options[2].name = nbd_options.nbd_options_lang.pt_color3;
                        // field.general.attributes.options[0].key  = 'pt_color1';
                        // field.general.attributes.options[1].key  = 'pt_color2';
                        // field.general.attributes.options[2].key  = 'pt_color3';
                        // field.general.attributes.options[0].sg_color  = '#000';
                        // field.general.attributes.options[0].mul_color = [ '#000' ];
                        field.general.attributes.options[0] = {
                            name: nbd_options.nbd_options_lang.pt_color1,
                            des: '',
                            selected: 0,
                            preview_type: 'i',
                            image:  0,
                            price: [],
                            image_url: '',
                            color:  '#ffffff',
                            bg_image: [],
                            bg_image_url: [],
                            isExpand: true,
                            enable_con: 0,
                            con_show: 'n',
                            con_logic: 'a',
                            key : 'pt_color1',
                            pt_color: {
                                sg_color: {
                                    name: 'Black', // 30/11
                                    color : '#000',
                                    qty_price: [ 
                                        {   qty : 1,
                                            price : 0, 
                                        }
                                    ],
                                },
                                color : '',
                            }
                        };
                        field.general.attributes.options[1] = {
                            name: nbd_options.nbd_options_lang.pt_color2,
                            des: '',
                            selected: 1,
                            preview_type: 'i',
                            image:  0,
                            price: [],
                            image_url: '',
                            color:  '#ffffff',
                            bg_image: [],
                            bg_image_url: [],
                            isExpand: false,
                            enable_con: 0,
                            con_show: 'n',
                            con_logic: 'a',
                            key : 'pt_color2',
                            pt_color: {
                                limit: '',
                                sg_color: {
                                	name: 'Black', // 30/11
                                    color : '#000',
                                },
                                mul_color : [
                                    {	
                                    	name: 'Black', // 30/11
                                        color : '#000',
                                        isExpand: false,
                                        selected: false,
                                        qty_price: [ 
                                            {   qty : 1,
                                                price : 0, 
                                            }
                                        ],
                                    }
                                ],
                                color : '',
                            }
                        };
                        field.general.attributes.options[2] = {
                            name: nbd_options.nbd_options_lang.pt_color3,
                            des: '',
                            selected: 0,
                            preview_type: 'i',
                            image:  0,
                            price: [],
                            image_url: '',
                            color:  '#ffffff',
                            bg_image: [],
                            bg_image_url: [],
                            isExpand: false,
                            enable_con: 0,
                            con_show: 'n',
                            con_logic: 'a',
                            key : 'pt_color3',
                            pt_color: { 
                                sg_color: {
                                	name: 'Black', // 30/11
                                    color : '#000',
                                    qty_price: [ 
                                        {   qty : 1,
                                            price : 0, 
                                        }
                                    ],
                                }, 
                                color : '',
                            }
                        };
                        break;
                }
                angular.forEach(field.general.attributes, function(attr, a_key){
                    attr.enable_subattr = 0;
                });
            }else{
                field.nbpb_type = type;
                if( angular.isUndefined($scope.options.views) ) $scope.options.views = [{name: nbd_options.nbd_options_lang.view_name, base: 0}];
                switch(type){
                    case 'nbpb_com':
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        field.general.component_icon = 0;
                        break;
                    case 'nbpb_text':
                        field.general.data_type.value = 'i';
                        field.general.input_type.value = 't';
                        field.general.data_type.hidden = true;
                        field.general.input_type.hidden = true;
                        field.general.nbpb_text_configs = angular.isDefined(field.general.nbpb_text_configs) ? field.general.nbpb_text_configs : {
                            default_text: '',
                            allow_all_font: 'y',
                            custom_fonts: [],
                            google_fonts: [],
                            allow_all_color: 'y',
                            colors: [],
                            allow_change_color: 'y',
                            allow_font_family: 'y',
                            views: []
                        };
                        break;
                    case 'nbpb_image':
                        field.general.data_type.value = 'i';
                        field.general.input_type.value = 'u';
                        field.general.data_type.hidden = true;
                        field.general.input_type.hidden = true;
                        field.general.nbpb_image_configs = angular.isDefined(field.general.nbpb_image_configs) ? field.general.nbpb_image_configs : {
                            views: []
                        };
                        break;
                }
            }
        }
        $scope.options.fields.push( field );
        $timeout(function(){
            jQuery('html,body').animate({
                scrollTop: jQuery("#" + field['id']).offset().top
            }, 'slow');
        });
        $scope.initfieldValue();
    };
    $scope.addView = function(){
        $scope.options.views.push({
            name: nbd_options.nbd_options_lang.view_name,
            base: 0
        });
        $scope.initfieldValue();
    };
    $scope.removeView = function( vIndex ){
        if( $scope.options.views.length == 1 ){
            return;
        }
        $scope.options.views.splice(vIndex, 1);
        $scope.initfieldValue();
    };
    $scope.set_view_base = function(vIndex){
        var file_frame;
        if ( file_frame ) {
            file_frame.open();
            return;
        };
        file_frame = wp.media.frames.file_frame = wp.media({
            title: nbd_options.nbd_options_lang.choose_image,
            button: {
                text: nbd_options.nbd_options_lang.choose_image
            },
            library: {
                type: [ 'image' ]
            },
            multiple: false
        });
        file_frame.on( 'select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            $scope.options.views[vIndex].base = attachment.id;
            $scope.options.views[vIndex].base_width = attachment.width;
            $scope.options.views[vIndex].base_height = attachment.height;
            var url = attachment.url;
            if( angular.isDefined(attachment.sizes) && angular.isDefined(attachment.sizes.thumbnail) ){
                url = attachment.sizes.thumbnail.url;
            }
            $scope.options.views[vIndex].base_url = url;
            if ($scope.$root.$$phase !== "$apply" && $scope.$root.$$phase !== "$digest") $scope.$apply();
        });
        file_frame.open();
    };
    $scope.remove_view_base = function(vIndex){
        $scope.options.views[vIndex].base = 0;
        $scope.options.views[vIndex].base_url = '';
    };
    $scope.set_view_config_image = function(fieldIndex, attr_index, sattr_index, $index){
        var file_frame;
        if ( file_frame ) {
            file_frame.open();
            return;
        };
        file_frame = wp.media.frames.file_frame = wp.media({
            title: nbd_options.nbd_options_lang.choose_image,
            button: {
                text: nbd_options.nbd_options_lang.choose_image
            },
            library: {
                type: [ 'image' ]
            },
            multiple: false
        });
        file_frame.on( 'select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            $scope.options['fields'][fieldIndex]['general']['pb_config'][attr_index][sattr_index].views[$index].image = attachment.id;
            var url = attachment.url;
            if( angular.isDefined(attachment.sizes) && angular.isDefined(attachment.sizes.thumbnail) ){
                url = attachment.sizes.thumbnail.url;
            }
            $scope.options['fields'][fieldIndex]['general']['pb_config'][attr_index][sattr_index].views[$index].image_url = url;
            if ($scope.$root.$$phase !== "$apply" && $scope.$root.$$phase !== "$digest") $scope.$apply();
        });
        file_frame.open(); 
    };
    $scope.remove_view_config_image = function(fieldIndex, attr_index, sattr_index, $index){
        $scope.options['fields'][fieldIndex]['general']['pb_config'][attr_index][sattr_index].views[$index].image = 0;
        $scope.options['fields'][fieldIndex]['general']['pb_config'][attr_index][sattr_index].views[$index].image_url = '';
    };
    $scope.get_field_class = function(type){
        var klass = 'default';
        switch(type){
            case 'page':
            case 'page1':
            case 'page2':
            case 'page3':
            case 'color':
            case 'size':
            case 'dimension':
            case 'dpi':
            case 'area':
            case 'orientation':
            case 'padding':
            case 'rounded_corner':
            case 'overlay':
            case 'fold':
            case 'pt_color':  // custom charming
            case 'shape':
                klass = 'wod';
                break;
            case 'nbpb_com':
            case 'nbpb_text':
            case 'nbpb_image':
                klass = 'wpo';
                break;
            case 'frame':
            case 'number_file':
                klass = 'wpu';
                break;
            default:
                klass = 'default';
                break
        }
        return klass;
    };
    $scope.get_field_type = function(type){
        type = angular.isDefined(type) ? type : '';
        var type_number;
        switch(type){
            case 'page':
                type_number = 2;
                break;
            case 'page1':
                type_number = 2.1;
                break;
            case 'page2':
                type_number = 2.2;
                break;
            case 'page3':
                type_number = 2.3;
                break;
            case 'color':
                type_number = 3;
                break;
            case 'size':
                type_number = 4;
                break;
            case 'dimension':
                type_number = 5;
                break;
            case 'dpi':
                type_number = 6;
                break;
            case 'area':
                type_number = 7;
                break;
            case 'shape':
                type_number = 7.1;
                break;
            case 'orientation':
                type_number = 8;
                break;
            case 'padding':
                type_number = 9;
                break;
            case 'rounded_corner':
                type_number = 10;
                break;
             case 'pt_color':
                type_number = 13;   // custom charming
                break;
            case 'nbpb_com':
                type_number = 20;
                break;
            case 'nbpb_text':
                type_number = 21;
                break;
            case 'nbpb_image':
                type_number = 22;
                break;
            case 'delivery':
                type_number = 30;
                break;
            case 'actions':
                type_number = 31;
                break;
            case 'overlay':
                type_number = 11;
                break;
            case 'fold':
                type_number = 12;
                break;
            case 'frame':
                type_number = 40;
                break;
            case 'number_file':
                type_number = 41;
                break;
            default:
                type_number = 1;
                break
        }
        return type_number;
    };
    $scope.add_measurement_range = function(fieldIndex){
        $scope.options['fields'][fieldIndex].general.mesure_range.push([]);
    };
    $scope.delete_measurement_ranges = function(fieldIndex, $event){
        var mesure_range = $scope.options['fields'][fieldIndex].general.mesure_range;
        if( mesure_range.length ){
            var need_delete = [];
            angular.forEach(mesure_range, function(mr, mr_index){
                if( mr[3] ){
                    need_delete.push(mr_index);
                };
            });
            for (var i = need_delete.length -1; i >= 0; i--) mesure_range.splice(need_delete[i],1);
        }
        angular.element($event.currentTarget).parents('table.nbo-measure-range').find('input.nbo-measure-range-select-all').prop('checked', false);
    };
    $scope.select_all_measurement_range = function(fieldIndex, $event){
        var mesure_range = $scope.options['fields'][fieldIndex].general.mesure_range;
        var el = angular.element($event.target),
        check = el.prop('checked') ? true : false;
        if( mesure_range.length ){
            angular.forEach(mesure_range, function(mr, mr_index){
                mr[3] = check;
            });
        }
    };
    $scope.copy_field = function( index ){
//        if(angular.isDefined($scope.options.fields[index].nbd_type) && $scope.options.fields[index].nbd_type != 'builder'){
//            //alert(nbd_options.nbd_options_lang.can_not_copy);
//            return;
//        }
        var field = {};
        angular.copy($scope.options.fields[index], field);
        var d = new Date();
        field['id'] = 'f' + d.getTime();
        field['general']['title']['value'] = field['general']['title']['value'] + ' - Copy';
        $scope.options.fields.push( field );
        $scope.initfieldValue();
    };
    $scope.delete_field = function(index){
        var con = confirm(nbd_options.nbd_options_lang.want_to_delete);
        if( con ){
            var field = $scope.options.fields[index];
            if( angular.isDefined(field.nbd_type) ){
                $scope.nbd_options[field.nbd_type] = 0;
            }
            $scope.options.fields.splice(index, 1);
            $scope.initfieldValue();
        }
    }; 
    $scope.clear_all_fields = function(index){
        var con = confirm(nbd_options.nbd_options_lang.want_to_delete_all);
        if( con ){
            $scope.options.fields = [];
            angular.forEach($scope.nbd_options, function(option, key){
                option = 0;
            });
            $scope.initfieldValue();
        }
    };
    $scope.sort_field= function(field_index, direction){
        var dest_index = field_index - 1;
        if( direction == 'up' ){
            if( field_index == 0 ) return;
        }else{
            if( field_index == ( $scope.options.fields.length - 1 ) ) return;
            dest_index = field_index + 1;
        }
        jQuery('.nbp-loading-wrap').addClass('nbp-show');
        $timeout(function() {
            var temp_field = {};
            angular.copy($scope.options.fields[field_index], temp_field);
            angular.copy($scope.options.fields[dest_index ], $scope.options.fields[field_index ]);
            angular.copy(temp_field, $scope.options.fields[dest_index ]);
            $scope.initfieldValue();
            $timeout(function() {
                jQuery('.nbp-loading-wrap').removeClass('nbp-show');
                jQuery.each( jQuery('[nbd-tab]').find('.nbd-field-tab'), function(){
                    jQuery(this).on('click', function(){
                        var target = jQuery(this).data('target');
                        jQuery(this).parents('.nbd-field-wrap').find('.nbd-field-content').removeClass('active');
                        jQuery(this).parent('ul').find('li').removeClass('active');
                        jQuery(this).parents('.nbd-field-wrap').find('.'+target).addClass('active');
                        jQuery(this).addClass('active');
                    });
                });
            }, 400);
        }, 100);
    };
    $scope.toggleExpandField =  function(index, $event){
        var parent = jQuery($event.target).parents('.nbd-field-wrap');
        function _toggleExpandField(){
            $scope.options.fields[index].isExpand = !$scope.options.fields[index].isExpand;
            $timeout(function() {
                jQuery('html,body').animate({ scrollTop: parent.offset().top - 50}, 200);
            }, 0);
        }

        if( large_amount_field == 'yes' ){
            jQuery('.nbp-loading-wrap').addClass('nbp-show');
            $timeout(function() {
                _toggleExpandField();
                angular.forEach($scope.options.fields, function(field, key){
                    if( key != index ){
                        $scope.options.fields[key].isExpand = false;
                    }
                });
                parent.find('.nbd-tab-nav li').removeClass('active');
                parent.find('.nbd-tab-nav li:first-child').addClass('active');
                jQuery('.nbp-loading-wrap').removeClass('nbp-show');
            }, 100);
        }else{
            _toggleExpandField();
        }
    }; 
    $scope.initfieldValue = function(){

        angular.forEach($scope.options.fields, function(field, key){
            $scope.option_values[key] = angular.isDefined($scope.option_values[key]) ? $scope.option_values[key] : '';
            if(field.general.data_type.value == 'i'){
                $scope.option_values[key] = '';
            }else{
                if( field.general.attributes.options.length == 0 ){
                    $scope.option_values[key] = '';
                }else{
                    $scope.option_values[key] = 0;
                    angular.forEach(field.general.attributes.options, function(op, k){
                        if( op.selected ) $scope.option_values[key] = k;
                    });
                }
            }
            if( angular.isDefined(field.nbd_type) ){
                $scope.nbd_options[field.nbd_type] = 1;
                switch(field.nbd_type){
                    case 'dpi': 
                        field.general.data_type.hidden = true;
                        //field.general.input_type.hidden = true;
                        break;
                    case 'page': 
                        field.general.input_type.hidden = true;
                        //field.general.price_type.hidden = true;
                        field.general.required.value = 'y';
                        //field.general.required.hidden = true; 
                        if( field.general.data_type.value == 'i' ){
                            field.general.attributes.options[1] = {};
                            //field.general.attributes.options[2] = {};
                            angular.copy(field.general.attributes.options[0], field.general.attributes.options[1]); 
                            //angular.copy(field.general.attributes.options[0], field.general.attributes.options[2]); 
                            field.general.attributes.options[0].name = nbd_options.nbd_options_lang.front;
                            field.general.attributes.options[1].name = nbd_options.nbd_options_lang.back;
                            //field.general.attributes.options[2].name = nbd_options.nbd_options_lang.both;
                        }
                        //field.general.attributes.add_att = false;
                        //field.general.attributes.remove_att = false;
                        if( angular.isUndefined( field.general.auto_select_page ) ){
                            field.general.auto_select_page = 'y';
                        }
                        break;
                    case 'page1': 
                        field.general.data_type.hidden = true;
                        field.general.input_type.hidden = true;
                        field.general.price_type.hidden = true;
                        //field.general.required.hidden = true;
                        if( angular.isUndefined( field.general.price_depend_no ) ){
                            field.general.price_depend_no = 'n';
                        }
                        if( angular.isUndefined( field.general.price_no_range ) ){
                            field.general.price_no_range = [];
                        }
                        field.general.price_type.value = 'c';
                        break;
                    case 'page2': 
                        field.general.data_type.hidden = true;
                        //field.general.price_type.hidden = true;
                        //field.general.required.hidden = true;
                        break;
                    case 'page3': 
                        field.general.data_type.hidden = true;
                        //field.general.price_type.hidden = true;
                        //field.general.required.hidden = true;
                        field.general.attributes.add_att = false;
                        field.general.attributes.remove_att = false;
                        break;
                    case 'color':
                        field.general.data_type.hidden = true;
                        if( field.general.attributes.options.bg_type == 'c' ){
                            angular.forEach(field.general.attributes.options, function(op){
                                op.bg_image = [];
                                op.bg_image_url = [];
                            });
                        }
                        if( angular.isUndefined( field.general.attributes.show_as_pt ) ){
                            field.general.attributes.show_as_pt = 'n';
                        }
                        break;
                    case 'orientation':
                        field.general.data_type.hidden = true;
                        field.general.attributes.add_att = false;
                        field.general.attributes.remove_att = false;
                        break;
                    case 'area':
                        field.general.data_type.hidden = true;
                        field.general.attributes.add_att = false;
                        field.general.attributes.remove_att = false;
                        break;
                    case 'size':
                        field.general.data_type.hidden = true;
                        break;
                    case 'dimension':
                        field.general.data_type.hidden = true;
                        field.general.input_type.hidden = true;
                        field.general.text_option.hidden = true;
                        if( angular.isUndefined( field.general.mesure_type ) ){
                            field.general.mesure_type = 'u';
                        }
                        break;
                    case 'padding':
                    case 'shape':
                        field.general.data_type.hidden = true;
                        break;
                    case 'rounded_corner':
                        field.general.data_type.hidden = true;
                        break;
                    case 'overlay':
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        angular.forEach(field.general.attributes.options, function(op, k){
                            if( angular.isUndefined( op.overlay_image ) ){
                                op.overlay_image = [];
                                op.overlay_image_url = [];
                            }
                        });
                        break;
                    case 'fold':
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        angular.forEach(field.general.attributes.options, function(op, k){
                            if( angular.isUndefined( op.fold ) ) op.fold = 'n';
                        });
                        break;
                     case 'pt_color':    // custom charming
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        field.general.attributes.add_att = false;
                        field.general.attributes.remove_att = true;
                        // field.general.attributes.options[1] = {};
                        // field.general.attributes.options[2] = {};
                        // angular.copy(field.general.attributes.options[0], field.general.attributes.options[1]); 
                        // angular.copy(field.general.attributes.options[0], field.general.attributes.options[2]); 
                        // field.general.attributes.options[0].name = nbd_options.nbd_options_lang.pt_color1;
                        // field.general.attributes.options[1].name = nbd_options.nbd_options_lang.pt_color2;
                        // field.general.attributes.options[2].name = nbd_options.nbd_options_lang.pt_color3;
                        // field.general.attributes.options[0].key  = 'pt_color1';
                        // field.general.attributes.options[1].key  = 'pt_color2';
                        // field.general.attributes.options[2].key  = 'pt_color3';
                        // angular.forEach(field.general.attributes.options, function(op, k){
                        //     if( angular.isUndefined( op.fold ) ) op.fold = 'n';
                        // });
                        // field.general.attributes.options[1].mul_color[0] = '#000';
                        // field.general.attributes.options[2].mul_color[0] = '#000';
                        break;

                }
            }
            if( angular.isDefined(field.nbpb_type) ){
                if( angular.isUndefined($scope.options.views) ) $scope.options.views = [];
                $scope.has_product_builder_field = true;
                switch(field.nbpb_type){
                    case 'nbpb_com':
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        field.general.data_type.hidden = true;
                        field.general.component_icon = angular.isDefined(field.general.component_icon) ? field.general.component_icon : 0;
                        $scope.buildPbConfigFlat( field );
                        break;
                    case 'nbpb_text':
                        field.general.data_type.value = 'i';
                        field.general.input_type.value = 't';
                        field.general.data_type.hidden = true;
                        field.general.input_type.hidden = true;
                        field.general.nbpb_text_configs = angular.isDefined(field.general.nbpb_text_configs) ? field.general.nbpb_text_configs : {
                            default_text: '',
                            allow_all_font: 'y',
                            custom_fonts: [],
                            google_fonts: [],
                            allow_all_color: 'y',
                            colors: [],
                            allow_change_color: 'y',
                            allow_font_family: 'y',
                            views: []
                        };
                        field.general.nbpb_text_configs.colors = angular.isDefined(field.general.nbpb_text_configs.colors) ? field.general.nbpb_text_configs.colors : [];
                        field.general.nbpb_text_configs.custom_fonts = angular.isDefined(field.general.nbpb_text_configs.custom_fonts) ? field.general.nbpb_text_configs.custom_fonts : [];
                        field.general.nbpb_text_configs.google_fonts = angular.isDefined(field.general.nbpb_text_configs.google_fonts) ? field.general.nbpb_text_configs.google_fonts : [];
                        field.general.nbpb_text_configs.views = angular.isDefined(field.general.nbpb_text_configs.views) ? field.general.nbpb_text_configs.views : [];
                        break;
                    case 'nbpb_image':
                        field.general.data_type.value = 'i';
                        field.general.input_type.value = 'u';
                        field.general.required.value = 'n';
                        field.general.data_type.hidden = true;
                        field.general.input_type.hidden = true;
                        field.general.required.hidden = true;
                        field.general.nbpb_image_configs = angular.isDefined(field.general.nbpb_image_configs) ? field.general.nbpb_image_configs : {
                            views: []
                        };
                        field.general.nbpb_image_configs.views = angular.isDefined(field.general.nbpb_image_configs.views) ? field.general.nbpb_image_configs.views : [];
                        break;
                }
            }
            if( angular.isDefined(field.nbe_type) ){
                switch(field.nbe_type){
                    case 'delivery':
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        break;
                    case 'actions':
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        break;
                    case 'frame':
                        field.general.data_type.value = 'm';
                        field.general.data_type.hidden = true;
                        break;
                    case 'number_file':
                        field.general.data_type.value = 'i';
                        field.general.input_type.value = 'n';
                        field.general.price_type.value = 'c';
                        field.general.data_type.hidden = true;
                        field.general.input_type.hidden = true;
                        field.general.price_type.hidden = true;
                        break;
                }
            }
        });
        $timeout(function(){
            $scope.current_input_vars = jQuery('[name]').length;
            if( $scope.current_input_vars > $scope.max_input_vars ){
                jQuery('html,body').animate({
                    scrollTop: jQuery("#notice-max-input-vars").offset().top - 100
                }, 'slow');
                alert(nbd_options.nbd_options_lang.max_input_var + ' ' + $scope.max_input_vars + '. ' + nbd_options.nbd_options_lang.max_input_notice);
            }
        }, 2000);

        $scope.maybeUpdateManualPm();
    };
    $scope.buildPbConfigFlat = function( field ){
        var options = field.general.attributes.options;
        field.general.pb_config_flat = [];
        if( angular.isUndefined(field.general.pb_config) ) field.general.pb_config = [];
        function build_config(attr_index, attr_rowspan, sattr_index, has_sattr){
            if( angular.isUndefined( field.general.pb_config[attr_index] ) ) field.general.pb_config[attr_index] = [];
            if( angular.isUndefined( field.general.pb_config[attr_index][sattr_index] ) ) field.general.pb_config[attr_index][sattr_index] = {};
            field.general.pb_config[attr_index][sattr_index].attr_rowspan = attr_rowspan;
            field.general.pb_config[attr_index][sattr_index].attr_index = attr_index;
            field.general.pb_config[attr_index][sattr_index].sattr_index = sattr_index;
            field.general.pb_config[attr_index][sattr_index].has_sattr = has_sattr;
            if( angular.isUndefined( field.general.pb_config[attr_index][sattr_index].views ) ) field.general.pb_config[attr_index][sattr_index].views = [];
            angular.forEach($scope.options.views, function(view, vkey){
                if( angular.isUndefined( field.general.pb_config[attr_index][sattr_index].views[vkey] ) ) field.general.pb_config[attr_index][sattr_index].views[vkey] = {
                    image: 0,
                    image_url: '',
                    display: true
                };
            });
        };
        var configIndex = 0;
        angular.forEach(options, function(op, key){
            if( angular.isDefined( op.enable_subattr ) && (op.enable_subattr === true || op.enable_subattr === 'on' || op.enable_subattr === 1) ){
                if( angular.isDefined( op.sub_attributes ) && op.sub_attributes.length > 0 ){
                    angular.forEach(op.sub_attributes, function(sop, skey){
                        var attr_rowspan = skey == 0 ? op.sub_attributes.length : 0;
                        build_config(key, attr_rowspan, skey, true);
                        configIndex++;
                    });
                }else{
                    build_config(key, 1, 0, false);
                    configIndex++;
                }
            }else{
                build_config(key, 1, 0, false);
                configIndex++;
            }
        });
        var flatConfigIndex = 0;
        angular.forEach(field.general.pb_config, function(op_config, key){
            var op = options[key];
            if( op ){
                if( angular.isDefined( op.enable_subattr ) && (op.enable_subattr === true || op.enable_subattr === 'on' || op.enable_subattr === 1) ){
                    angular.forEach(op_config, function(sop_config, skey){
                        if( angular.isDefined( op.sub_attributes ) && angular.isDefined( op.sub_attributes[skey] ) ){
                            field.general.pb_config_flat[flatConfigIndex] = {};
                            angular.copy( sop_config, field.general.pb_config_flat[flatConfigIndex] );
                            flatConfigIndex++;
                        }
                    });
                }else{
                    field.general.pb_config_flat[flatConfigIndex] = {};
                    angular.copy( op_config[0], field.general.pb_config_flat[flatConfigIndex] );
                    flatConfigIndex++;
                }
            }else{
                field.general.pb_config.splice( key, 1 );
            }
        });
    };
    $scope.init = function( options ){
        $scope.nbd_options = {};
        $scope.options = NBDOPTIONS;
        $scope.current_input_vars = 1;
        $scope.max_input_vars = max_input_vars;
        if( angular.isDefined(options) ){
            $scope.options = options;
            if ($scope.$root.$$phase !== "$apply" && $scope.$root.$$phase !== "$digest") $scope.$apply(); 
        }
        $scope.option_values = [];
        angular.forEach($scope.options.fields, function(field, key){
            field.isExpand = false;
            if (field.general.attributes.options.length) {
                angular.forEach(field.general.attributes.options, function (attr, a_key) {
                    attr.isExpand = false;
                    if (attr.enable_subattr) {
                        if (attr.sub_attributes.length) {
                            angular.forEach(attr.sub_attributes, function (sattr, s_key) {
                                sattr.isExpand = false;
                            });
                        }
                    }
                });
            }
        });
        if( angular.isDefined( $scope.options.groups ) ){
            angular.forEach($scope.options.groups, function(group, gkey){
                group.isExpand = false;
            });
        };
        $scope.has_product_builder_field = false;
        $scope.initfieldValue();
        $scope.options.pm_ver = $scope.options.pm_ver ? $scope.options.pm_ver : [];
        $scope.options.pm_hoz = $scope.options.pm_hoz ? $scope.options.pm_hoz : [];
        $scope.$watchCollection('options.fields', function(newVal, oldVal){
            $scope.availablePmHozFileds = pmFieldFilter($scope.options.fields, $scope.options.pm_ver);
            $scope.availablePmVerFileds = pmFieldFilter($scope.options.fields, $scope.options.pm_hoz);
            $scope.availableBulkFileds = bulkFieldFilter($scope.options.fields);
            $scope.allFields = allFieldsFilter($scope.options.fields);
            $scope.manualPMFields = manualPMFieldFilter($scope.options.fields, $scope.manualPMVerFields.concat( $scope.manualPMHozFields ));
        }, true);
        $scope.$watchCollection('options.pm_ver', function(newVal, oldVal){
            $scope.availablePmHozFileds = pmFieldFilter($scope.options.fields, $scope.options.pm_ver);
        }, true);
        $scope.$watchCollection('options.pm_hoz', function(newVal, oldVal){
            $scope.availablePmVerFileds = pmFieldFilter($scope.options.fields, $scope.options.pm_hoz);
        }, true);
        $scope.updatePopupTriggerIndex( true );

        $scope.options.manual_build_pm = $scope.options.manual_build_pm == 'on' ? true : false;
        $scope.maybeUpdateManualPm( true );
    };
    $scope.export = function(){
        jQuery('.nbp-loading-wrap').addClass('nbp-show');
        $scope.get_media_full_size_url(function( images ){
            var new_options = $scope.merge_new_media( $scope.options, images );
            var filename = 'options.json',
            options = JSON.stringify( new_options, function(name, val){
                if( name == '$$hashKey' ){
                    return undefined;
                }else{
                    return val;
                }
            });
            jQuery('.nbp-loading-wrap').removeClass('nbp-show');
            var a = document.createElement('a');
            a.setAttribute('href', 'data:application/json;charset=utf-8,'+ encodeURIComponent(options));
            a.setAttribute('download', filename);
            a.style.display = 'none';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        });
    };
    $scope.get_media_full_size_url = function( callback ){
        var mediaObject = $scope.get_media_from_options( $scope.options, 'id' );
        jQuery.ajax({
            url: ajax_url,
            method: "POST",
            data: {action: 'nbd_get_media_full_size_url', nonce: nbnonce, images: JSON.stringify( mediaObject )}
        }).done(function (data) {
            var res = JSON.parse(data);
            if( res.flag == 1 ){
                callback( res.images );
            }else{
                jQuery('.nbp-loading-wrap').removeClass('nbp-show');
                alert('Error, Try again later!');
            }
        });
    };
    $scope.merge_new_media = function( options, medias, type ){
        new_options = {};
        var new_options = angular.copy( options, new_options );
        var postfix = angular.isUndefined( type ) ? '_url' : '';
        angular.forEach(medias, function(media, key){
            var key_arr = key.split("-"); var media_key = key_arr[key_arr.length - 1] + postfix;
            if( ! /[image|icon|base]$/.test(key) ){
                media_key = key_arr[key_arr.length - 1];
                //key_arr[key_arr.length - 2] = 'bg_image' + postfix;
                key_arr[key_arr.length - 2] = key_arr[key_arr.length - 2] + postfix;
            }
            key_arr.splice(key_arr.length - 1, 1);
            function getTargetMedia(new_options, key_arr){
                return key_arr.reduce(function(obj, __key){
                    return (obj && obj[__key] !== 'undefined') ? obj[__key] : undefined;
                }, new_options);
            };
            var targetMedia = getTargetMedia(new_options, key_arr);
            if( false != media ) targetMedia[media_key] = media;
        });
        return new_options;
    };
    $scope.import = function(){
        var input = document.createElement('input');
        input.type = 'file';
        input.accept = 'text/json|application/json';
        input.style.display = 'none';
        input.addEventListener('change', onChange.bind(input), false);
        document.body.appendChild(input);
        input.click();
        function onChange(){
            if (this.files.length > 0) {
                var file = this.files[0],
                reader = new FileReader();
                reader.onload = function(event){
                    if (event.target.readyState === 2) {
                        jQuery('.nbp-loading-wrap').addClass('nbp-show');
                        $timeout(function() {
                            var result = JSON.parse(reader.result);
                            $scope.update_options_media( result );
                            destroy();
                        }, 100);
                    }
                };
                reader.readAsText(file);
            }
        }
        function destroy() {
            input.removeEventListener('change', onChange.bind(input), false);
            document.body.removeChild(input);
        }
    };
    $scope.get_media_from_options = function( options, type ){
        var mediaObject = {}; var _key;
        if( angular.isDefined(options.views) ){
            angular.forEach(options.views, function(view, key){
                if( view.base != '0' ){
                    _key = 'views-' + key + '-base';
                    mediaObject[_key] = type == 'url' ? view.base_url : view.base;
                }
            });
        }
        if( angular.isDefined(options.groups) ){
            angular.forEach(options.groups, function(group, key){
                if( group.image != '0' ){
                    _key = 'groups-' + key + '-image';
                    mediaObject[_key] = type == 'url' ? group.image_url : group.image;
                }
            });
        }
        angular.forEach(options.fields, function(field, fkey){
            if( angular.isDefined(field.general.attributes.options) ){
                angular.forEach(field.general.attributes.options, function(option, okey){
                    if( option.image != '0' ){
                        _key = 'fields-' + fkey + '-general-attributes-options-' + okey + '-image';
                        mediaObject[_key] = type == 'url' ? option.image_url : option.image;
                    }
                    if( angular.isDefined(option.bg_image) && option.bg_image.length > 0 ){
                        angular.forEach(option.bg_image, function(obi, obikey){
                            if( obi != '0' && obi != null ){
                                _key = 'fields-' + fkey + '-general-attributes-options-' + okey + '-bg_image-' + obikey;
                                mediaObject[_key] = type == 'url' ? option.bg_image_url[obikey] : obi;
                            }
                        });
                    }
                    if( angular.isDefined(option.product_image) && option.product_image != '0' ){
                        _key = 'fields-' + fkey + '-general-attributes-options-' + okey + '-product_image';
                        mediaObject[_key] = type == 'url' ? option.product_image_url : option.product_image;
                    }
                    if( angular.isDefined(option.sub_attributes) ){
                        angular.forEach(option.sub_attributes, function(sub_attr, skey){
                            if( sub_attr.image != '0' ){
                                _key = 'fields-' + fkey + '-general-attributes-options-' + okey + '-sub_attributes-' + skey + '-image';
                                mediaObject[_key] = type == 'url' ? sub_attr.image_url : sub_attr.image;
                            }
                        });
                    }
                    if( angular.isDefined(option.overlay_image) && option.overlay_image.length > 0 ){
                        angular.forEach(option.overlay_image, function(obi, obikey){
                            if( obi != '0' && obi != null ){
                                _key = 'fields-' + fkey + '-general-attributes-options-' + okey + '-overlay_image-' + obikey;
                                mediaObject[_key] = type == 'url' ? option.overlay_image_url[obikey] : obi;
                            }
                        });
                    }
                    if( angular.isDefined(option.frame_image) && option.frame_image != '0' ){
                        _key = 'fields-' + fkey + '-general-attributes-options-' + okey + '-frame_image';
                        mediaObject[_key] = type == 'url' ? option.frame_image_url : option.frame_image;
                    }
                });
            }
            if( angular.isDefined( field.general.component_icon ) ){
                if( field.general.component_icon != '0' ){
                    _key = 'fields-' + fkey + '-general-component_icon';
                    mediaObject[_key] = type == 'url' ? field.general.component_icon_url : field.general.component_icon;
                }
            }
            if( angular.isDefined( field.general.pb_config ) ){
                angular.forEach(field.general.pb_config, function(attr, akey){
                    angular.forEach(attr, function(sattr, sakey){
                        angular.forEach(sattr.views, function(cview, vkey){
                            if( cview.image != '0' ){
                                _key = 'fields-' + fkey + '-general-pb_config-' + akey + '-' + sakey + '-views-' + vkey + '-image';
                                mediaObject[_key] = type == 'url' ? cview.image_url : cview.image;
                            }
                        });
                    });
                });
            }
        });
        return mediaObject;
    };
    $scope.update_options_media = function( options ){
        jQuery('#nbp-processing').show();
        var mediaObject = $scope.get_media_from_options( options, 'url' ),
        newMediaObject = {},
        keys = Object.keys(mediaObject),
        total = keys.length,
        index = 0;
        jQuery('#nbp-process-loaded').html(index);
        jQuery('#nbp-process-total').html(total);
        function update_media_false(){
            jQuery('.nbp-loading-wrap').removeClass('nbp-show');
            jQuery('#nbp-processing').hide();
            alert('Error, Try again later!');
        }
        function merge_new_media(){
            var new_options = $scope.merge_new_media( options, newMediaObject, 'id' );
            $scope.init( new_options );
            jQuery('.nbp-loading-wrap').removeClass('nbp-show');
            jQuery('#nbp-processing').hide();
        }
        function update_remote_media( mediaObject, index ){
            if( index < total ){
                $scope.download_import_image( mediaObject[keys[index]], function(data){
                    var res = JSON.parse(data);
                    if( angular.isDefined(res.flag) && res.flag == '1' ){
                        if( res.image.current_site == 0 ){
                            newMediaObject[keys[index]] = res.image.id;
                        }
                        index++;
                        jQuery('#nbp-process-loaded').html(index);
                        update_remote_media( mediaObject, index);
                    }else{
                        update_media_false();
                    }
                });
            }else{
                merge_new_media();
            }
        }
        update_remote_media( mediaObject, index);
    };
    $scope.download_import_image = function( image, callack ){
        jQuery.ajax({
            url: ajax_url,
            method: "POST",
            data: {action: 'nbd_download_option_image', nonce: nbnonce, image: image}
        }).done(function (data) {
            callack( data );
        });
    };
    $scope.check_depend = function( fields, data ){
        if( angular.isDefined(data.hidden) ) return false;
        if( angular.isUndefined(data.depend) ) return true;
        var check = [], total_check = true;
        angular.forEach(data.depend, function(f, _key){
            check[_key] = f.operator == '=' ? false : true;
            angular.forEach(fields, function(field, key){
                var val_arr = f.value.split(',');
                if( val_arr.length > 1 ){
                    angular.forEach(val_arr, function(val, vkey){
                        if( key == f.field && field.value == val ){
                            check[_key] = f.operator == '=' ? true : false;
                        }
                    });
                }else{
                    if( key == f.field && field.value == f.value ){
                        check[_key] = f.operator == '=' ? true : false;
                    }
                }
            });
        });
        angular.forEach(check, function(c, k){
            total_check = total_check && c;
        });
        return total_check;
    };
    $scope.check_option_depend = function(fieldIndex, depends){
        if( angular.isUndefined(depends) ) return true;
        var check = [], total_check = true;
        angular.forEach(depends, function(depend, _key){
            check[_key] = false;
            if( depend.operator  == '=' ){
                if($scope.options['fields'][fieldIndex]['general'][depend.field].value == depend.value) check[_key] = true;
            }else{
                if($scope.options['fields'][fieldIndex]['general'][depend.field].value != depend.value) check[_key] = true;
            }
        });
        angular.forEach(check, function(c, k){
            total_check = total_check && c;
        });
        return total_check;
    };
    $scope.remove_attribute = function(fieldIndex, key, $index){
        if( $scope.options['fields'][fieldIndex]['general'][key].options.length == 1 ){
            return;
        }
        if( angular.isDefined( $scope.options['fields'][fieldIndex]['general'][key].remove_att ) && !$scope.options['fields'][fieldIndex]['general'][key].remove_att ){
            alert(nbd_options.nbd_options_lang.can_not_remove_att);
            return;
        }
        $scope.options['fields'][fieldIndex]['general'][key]['options'].splice($index, 1);
        $scope.initfieldValue();
    };
    $scope.remove_sub_attribute = function(fieldIndex, opIndex, sopIndex){
        $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['sub_attributes'].splice(sopIndex, 1);
        $scope.initfieldValue();
    };
    $scope.add_text_configs_color = function(fieldIndex){
        $scope.options['fields'][fieldIndex]['general']['nbpb_text_configs']['colors'].push({
            name: 'White',
            code: '#ffffff'
        });
    };
    $scope.remove_text_configs_color = function(fieldIndex, clIndex){
        $scope.options['fields'][fieldIndex]['general']['nbpb_text_configs']['colors'].splice(clIndex, 1);
    };
    $scope.sort_attribute = function(fieldIndex, opIndex, direction){
        var options = $scope.options['fields'][fieldIndex]['general']['attributes']['options'];
        var dest_index = opIndex - 1;
        if( direction == 'up' ){
            if( opIndex == 0 ) return;
        }else{
            if( opIndex == ( options.length - 1 ) ) return;
            dest_index = opIndex + 1;
        }
        var temp_op = {};
        angular.copy(options[opIndex], temp_op);
        angular.copy(options[dest_index ], options[opIndex]);
        angular.copy(temp_op, options[dest_index]);
        $scope.initfieldValue();
    };
    $scope.sort_sub_attribute = function(fieldIndex, opIndex, sopIndex, direction){
        var sub_attributes = $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['sub_attributes'];
        var dest_index = sopIndex - 1;
        if( direction == 'up' ){
            if( sopIndex == 0 ) return;
        }else{
            if( sopIndex == ( sub_attributes.length - 1 ) ) return;
            dest_index = sopIndex + 1;
        }
        var temp_sop = {};
        angular.copy(sub_attributes[sopIndex], temp_sop);
        angular.copy(sub_attributes[dest_index ], sub_attributes[sopIndex]);
        angular.copy(temp_sop, sub_attributes[dest_index]);
        $scope.initfieldValue();
    };
    $scope.toggle_expand_attribute = function(fieldIndex, opIndex){
        $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['isExpand'] = !$scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['isExpand'];
    };
    $scope.toggle_expand_sub_attribute = function(fieldIndex, opIndex, sopIndex){
        $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['sub_attributes'][sopIndex]['isExpand'] = !$scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['sub_attributes'][sopIndex]['isExpand'];
    };
    $scope.seleted_attribute = function(fieldIndex, key, $index){
        angular.forEach($scope.options['fields'][fieldIndex]['general'][key]['options'], function(field, _key){
            $scope.options['fields'][fieldIndex]['general'][key]['options'][_key]['selected'] = 0;
        });
        $scope.options['fields'][fieldIndex]['general'][key]['options'][$index]['selected'] = 1;
        $scope.initfieldValue();
    };
    $scope.seleted_sub_attribute = function(fieldIndex, key, opIndex, sopIndex){
        angular.forEach($scope.options['fields'][fieldIndex]['general'][key]['options'][opIndex]['sub_attributes'], function(field, _key){
            $scope.options['fields'][fieldIndex]['general'][key]['options'][opIndex]['sub_attributes'][_key]['selected'] = 0;
        });
        $scope.options['fields'][fieldIndex]['general'][key]['options'][opIndex]['sub_attributes'][sopIndex]['selected'] = 1;
        $scope.initfieldValue();
    };

    //custom charmingprint
    $scope.toggleAttrColor =  function(fieldIndex, opIndex , colorIndex , $event){
        $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color']['mul_color'][colorIndex].isExpand = !$scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color']['mul_color'][colorIndex].isExpand;
        var parent = jQuery($event.target).parents('.nbd-attribute-color');
        $timeout(function() {
            jQuery('html,body').animate({ scrollTop: parent.offset().top - 50}, 200);
        }, 0);
    };
    $scope.deleteAttrColor = function(fieldIndex, opIndex , colorIndex){
        if ( $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color']['mul_color'].length == 1 ) {
            return;
        }
        var con = confirm(nbd_options.nbd_options_lang.want_to_delete);
        if( con ){
            var field = $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color']['mul_color'][colorIndex];
            $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color']['mul_color'].splice(colorIndex, 1);
        }
    }; 
    $scope.copyAttrcolor = function( fieldIndex, opIndex , colorIndex ){
        var field = {};
        angular.copy($scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color']['mul_color'][colorIndex], field);
        if( $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color']['mul_color'].length < 10 ) {
        	$scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color']['mul_color'].push( field );
        }
    };
    $scope.add_qty_price = function( type , fieldIndex , opIndex , colorIndex  ) {
        var field = {  qty : 1,  price : 0, }
        if( type == 'mul_color' ) {
        	if( angular.isDefined( $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color'][type][colorIndex]['qty_price'] ) ) {
	        	$scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color'][type][colorIndex]['qty_price'].push(field);
	        }
        } else {
        	if( angular.isDefined( $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color'][type]['qty_price'] ) ) {
	        	$scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color'][type]['qty_price'].push(field);
	        }
        }
        
    }
    $scope.delete_qty_price = function( type , fieldIndex , opIndex , qpIndex , colorIndex ) {
    	if( type == 'mul_color' ) {
    		if( angular.isDefined( $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color'][type][colorIndex]['qty_price'] ) ) {   
	            var len_qty = $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color'][type][colorIndex]['qty_price'].length;
	            if( len_qty > 1 ) {
	            	$scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color'][type][colorIndex]['qty_price'].splice( qpIndex , 1 );  
	            }    
	        }
    	} else {
    		if( angular.isDefined( $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color'][type]['qty_price'] ) ) {   
	            var len_qty = $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color'][type]['qty_price'].length;
	            if( len_qty > 1 ) {
	            	$scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color'][type]['qty_price'].splice( qpIndex , 1 );  
	            }    
	        }
    	}
        
    }
    $scope.add_pt_color = function( fieldIndex , opIndex , color) {
    	color = '#000';
    	name = 'Black'; // 30/11
        if( angular.isDefined($scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color']['mul_color']) && $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color']['mul_color'].length < 20 ) {
            $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color']['mul_color'].push( { color: color ,name: name, qty_price : [ { qty : 1 , price : 0 } ] } ); // 30/11
        }
    }
    $scope.remove_pt_color = function( fieldIndex , opIndex , colorIndex) {
        if( angular.isDefined($scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color']['mul_color'][colorIndex]) ) {   
            $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color']['mul_color'].splice( colorIndex , 1 );     
        }
    }
    $scope.edit_pt_color = function( fieldIndex , opIndex , colorIndex , color) {
        if( angular.isDefined($scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color']['mul_color']) && $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color']['mul_color'].length < 20 ) {
            $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['pt_color']['mul_color'].push( { color: color , price : 0} );
        }
    }
   
    // end

    $scope.add_attribute = function(fieldIndex, key){
        if( angular.isDefined( $scope.options['fields'][fieldIndex]['general'][key].add_att ) && !$scope.options['fields'][fieldIndex]['general'][key].add_att ){
            alert(nbd_options.nbd_options_lang.can_not_add_att);
            return;
        }
        $scope.options['fields'][fieldIndex]['general'][key]['options'].push(
            {
                name: nbd_options.nbd_options_lang.attribute_name,
                des: '',
                price: [],
                selected: 0,
                preview_type: 'i',
                image:  0,
                image_url: '',
                color:  '#ffffff',
                bg_image: [],
                bg_image_url: [],
                isExpand: true,
                enable_con: 0,
                con_show: 'n',
                con_logic: 'a',
                depend: [
                    {
                        id: '',
                        operator: 'i',
                        val: '',
                        subval: ''
                    }
                ],
                implicit_value: ''
            }
        );
        $scope.initfieldValue();
    };
    $scope.add_sub_attribute = function(fieldIndex, opIndex){
        if( angular.isUndefined($scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['sub_attributes']) ){
            $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['sub_attributes'] = [];
        }
        $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['sub_attributes'].push(
            {
                name: nbd_options.nbd_options_lang.sub_attribute_name,
                des: '',
                price: [],
                selected: 0,
                preview_type: 'i',
                image:  0,
                image_url: '',
                color: '#ffffff',
                isExpand: true,
                enable_con: 0,
                con_show: 'n',
                con_logic: 'a',
                depend: [
                    {
                        id: '',
                        operator: 'i',
                        val: '',
                        subval: ''
                    }
                ],
                implicit_value: ''
            }
        );
        $scope.initfieldValue();
    };
    $scope.toggle_enable_subattr = function(fieldIndex, opIndex){
        var field = $scope.options['fields'][fieldIndex];
        if( angular.isUndefined(field['general']['attributes']['options'][opIndex]['sattr_display_type']) ){
            field['general']['attributes']['options'][opIndex]['sattr_display_type'] = 's';
        }
        if( angular.isDefined(field.nbpb_type) && field.nbpb_type == 'nbpb_com' ){
            $scope.buildPbConfigFlat( field );
        }
    };
    $scope.set_attribute_image = function(fieldIndex, $index, type, type_url, $bg_index){
        var file_frame;
        if ( file_frame ) {
            file_frame.open();
            return;
        };
        file_frame = wp.media.frames.file_frame = wp.media({
            title: nbd_options.nbd_options_lang.choose_image,
            button: {
                text: nbd_options.nbd_options_lang.choose_image
            },
            library: {
                    type: [ 'image' ]
            },
            multiple: false
        });
        file_frame.on( 'select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            if( angular.isDefined($bg_index) ){
                $scope.options['fields'][fieldIndex]['general']['attributes']['options'][$index][type][$bg_index] = attachment.id;
            }else{
                $scope.options['fields'][fieldIndex]['general']['attributes']['options'][$index][type] = attachment.id;
            }
            var url = attachment.url;
            if( angular.isDefined(attachment.sizes) && angular.isDefined(attachment.sizes.thumbnail) ){
                url = attachment.sizes.thumbnail.url;
            }
            if( angular.isDefined($bg_index) ){
                $scope.options['fields'][fieldIndex]['general']['attributes']['options'][$index][type_url][$bg_index] = url;
            }else{
                $scope.options['fields'][fieldIndex]['general']['attributes']['options'][$index][type_url] = url;
            }
            if ($scope.$root.$$phase !== "$apply" && $scope.$root.$$phase !== "$digest") $scope.$apply();
        });
        file_frame.open(); 
    };
    $scope.set_component_icon = function(fieldIndex){
        var file_frame;
        if ( file_frame ) {
            file_frame.open();
            return;
        };
        file_frame = wp.media.frames.file_frame = wp.media({
            title: nbd_options.nbd_options_lang.choose_image,
            button: {
                text: nbd_options.nbd_options_lang.choose_image
            },
            library: {
                    type: [ 'image' ]
            },
            multiple: false
        });
        file_frame.on( 'select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            $scope.options['fields'][fieldIndex]['general']['component_icon'] = attachment.id;
            var url = attachment.url;
            if( angular.isDefined(attachment.sizes) && angular.isDefined(attachment.sizes.thumbnail) ){
                url = attachment.sizes.thumbnail.url;
            }
            $scope.options['fields'][fieldIndex]['general']['component_icon_url'] = url;
            if ($scope.$root.$$phase !== "$apply" && $scope.$root.$$phase !== "$digest") $scope.$apply();
        });
        file_frame.open();
    };
    $scope.remove_component_icon = function(fieldIndex){
        $scope.options['fields'][fieldIndex]['general']['component_icon'] = 0;
        $scope.options['fields'][fieldIndex]['general']['component_icon_url'] = '';
    }; 
    $scope.set_sub_attribute_image = function(fieldIndex, opIndex, sopIndex){
        var file_frame;
        if ( file_frame ) {
            file_frame.open();
            return;
        };
        file_frame = wp.media.frames.file_frame = wp.media({
            title: nbd_options.nbd_options_lang.choose_image,
            button: {
                text: nbd_options.nbd_options_lang.choose_image
            },
            library: {
                type: [ 'image' ]
            },
            multiple: false
        });
        file_frame.on( 'select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['sub_attributes'][sopIndex]['image'] = attachment.id;
            var url = attachment.url;
            if( angular.isDefined(attachment.sizes) && angular.isDefined(attachment.sizes.thumbnail) ){
                url = attachment.sizes.thumbnail.url;
            }
            $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['sub_attributes'][sopIndex]['image_url'] = url;
            if ($scope.$root.$$phase !== "$apply" && $scope.$root.$$phase !== "$digest") $scope.$apply();
        });
        file_frame.open(); 
    };
    $scope.remove_attribute_image = function(fieldIndex, $index, type, type_url){
        $scope.options['fields'][fieldIndex]['general']['attributes']['options'][$index][type] = 0;
        $scope.options['fields'][fieldIndex]['general']['attributes']['options'][$index][type_url] = '';
    }; 
    $scope.remove_sub_attribute_image= function(fieldIndex, opIndex, sopIndex){
        $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['sub_attributes'][sopIndex]['image'] = 0;
        $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['sub_attributes'][sopIndex]['image_url'] = '';
    };
    $scope.add_remove_second_color = function(fieldIndex, opIndex){
        if( angular.isUndefined( $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['color2'] ) ){
            $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex]['color2'] = '#ffffff';
        }else{
            delete $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex].color2;
        }
    };
    $scope.add_condition = function(fieldIndex){
        $scope.options['fields'][fieldIndex]['conditional'].depend.push({
            id: '',
            operator: 'i',
            val: ''
        });
    };
    $scope.delete_condition = function(fieldIndex, cdIndex){
        if( $scope.options['fields'][fieldIndex]['conditional'].depend.length == 1 ) return;
        $scope.options['fields'][fieldIndex]['conditional'].depend.splice(cdIndex, 1);
    };
    $scope.add_attribute_condition = function( fieldIndex, opIndex ){
        $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex].depend.push({
            id:  '',
            operator:  'i',
            val:  '' 
        });
    };
    $scope.delete_attribute_condition = function( fieldIndex, opIndex, ocIndex ){
        if( $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex].depend.length == 1 ) return;
        $scope.options['fields'][fieldIndex]['general']['attributes']['options'][opIndex].depend.splice(ocIndex, 1);
    };
    $scope.update_price_type = function(fieldIndex){
        if( $scope.options['fields'][fieldIndex]['general'].data_type.value == 'm' && $scope.options['fields'][fieldIndex]['general'].price_type.value == 'c' ){
            $scope.options['fields'][fieldIndex]['general'].price_type.value = 'f';
        }
    };
    $scope.update_condition_qty = function(fieldIndex){
        angular.forEach($scope.options['fields'][fieldIndex]['conditional'].depend, function(con, _key){
            if( con.id != '' ){
                if( con.id == 'qty' && [ 'i', 'ne', 'e', 'ne' ].indexOf( con.operator ) > -1  ) con.operator = 'eq';
            }
        });
    };
    $scope.check_option_visible = function(fieldIndex){
        if( $scope.options['fields'][fieldIndex]['conditional'].enable == 'n' ) return true;
        if( angular.isUndefined( $scope.options['fields'][fieldIndex]['conditional'].depend ) ) return true;
        if( $scope.options['fields'][fieldIndex]['conditional'].depend.length == 0 ) return true;
        var show = $scope.options['fields'][fieldIndex]['conditional']['show'],
        logic = $scope.options['fields'][fieldIndex]['conditional']['logic'],
        check = [];
        var total_check = logic == 'a' ? true : false;
        function get_field(fieldId){
            var field = null;
            $scope.options.fields.forEach(function(_field, key){
                if( _field.id == fieldId){
                    field = _field;
                    field.index = key;
                }
            });
            return field;
        };
        angular.forEach($scope.options['fields'][fieldIndex]['conditional'].depend, function(con, _key){
            if( con.id != '' ){
                var field = get_field(con.id);
                switch(con.operator){
                    case 'i':
                        check[_key] = $scope.option_values[field.index] == con.val ? true : false;
                        break;
                    case 'n':
                        check[_key] = $scope.option_values[field.index] != con.val ? true : false;
                        break;  
                    case 'e':
                        check[_key] = $scope.option_values[field.index] == '' ? true : false;
                        break;
                    case 'ne':
                        check[_key] = $scope.option_values[field.index] != '' ? true : false;
                        break; 
                    case 'eq':
                    case 'gt':
                    case 'lt':
                        check[_key] = true;
                        break;
                }
            }else{
                check[_key] = true;
            }
        });
        angular.forEach(check, function(c, k){
            total_check = logic == 'a' ? (total_check && c) : (total_check || c);
        });
        return show == 'y' ? total_check : !total_check;
    };
    $scope.add_attr_condition = function( fieldIndex, opIndex ){
        $scope.options.fields[fieldIndex].general.attributes.options[opIndex].depend.push({
            id: '',
            operator: 'i',
            val: ''
        });
    };
    $scope.delete_attr_condition = function( fieldIndex, opIndex, cdIndex ){
        if( $scope.options.fields[fieldIndex].general.attributes.options[opIndex].depend.length == 1 ) return;
        $scope.options.fields[fieldIndex].general.attributes.options[opIndex].depend.splice(cdIndex, 1);
    };
    $scope.add_sub_attr_condition = function( fieldIndex, opIndex, sopIndex ){
        $scope.options.fields[fieldIndex].general.attributes.options[opIndex].sub_attributes[sopIndex].depend.push({
            id: '',
            operator: 'i',
            val: ''
        });
    };
    $scope.delete_sub_attr_condition = function( fieldIndex, opIndex, sopIndex, cdIndex ){
        if( $scope.options.fields[fieldIndex].general.attributes.options[opIndex].sub_attributes[sopIndex].depend.length == 1 ) return;
        $scope.options.fields[fieldIndex].general.attributes.options[opIndex].sub_attributes[sopIndex].depend.splice(cdIndex, 1);
    };
    $scope.set_group_image = function( gIndex ){
        var file_frame;
        if ( file_frame ) {
            file_frame.open();
            return;
        };
        file_frame = wp.media.frames.file_frame = wp.media({
            title: nbd_options.nbd_options_lang.choose_image,
            button: {
                text: nbd_options.nbd_options_lang.choose_image
            },
            library: {
                type: [ 'image' ]
            },
            multiple: false
        });
        file_frame.on( 'select', function() {
            var attachment = file_frame.state().get('selection').first().toJSON();
            $scope.options.groups[gIndex]['image'] = attachment.id;
            var url = attachment.url;
            if( angular.isDefined(attachment.sizes) && angular.isDefined(attachment.sizes.thumbnail) ){
                url = attachment.sizes.thumbnail.url;
            }
            $scope.options.groups[gIndex]['image_url'] = url;
            if ($scope.$root.$$phase !== "$apply" && $scope.$root.$$phase !== "$digest") $scope.$apply();
        });
        file_frame.open();
    };
    $scope.remove_group_image = function( gIndex ){
        $scope.options.groups[gIndex]['image'] = 0;
        $scope.options.groups[gIndex]['image_url'] = '';
    };
    $scope.sort_group = function( gIndex, direction ){
        var dest_index = gIndex - 1;
        if( direction == 'up' ){
            if( gIndex == 0 ) return;
        }else{
            if( gIndex == ( gIndex.length - 1 ) ) return;
            dest_index = gIndex + 1;
        }
        var temp_gr = {};
        angular.copy($scope.options.groups[gIndex], temp_gr);
        angular.copy($scope.options.groups[dest_index ], $scope.options.groups[gIndex]);
        angular.copy(temp_gr, $scope.options.groups[dest_index]);
    };
    $scope.remove_group = function( $index ){
        $scope.options.groups.splice( $index, 1 );
    };
    $scope.add_group = function(){
        $scope.options.groups.push({
            title: nbd_options.nbd_options_lang.group_title,
            des: nbd_options.nbd_options_lang.group_des,
            note: nbd_options.nbd_options_lang.group_note,
            image: 0,
            cols: 1,
            fields: [],
            isExpand: true
        });
    };
    $scope.clear_group = function( $index ){
        $scope.options.groups[$index].fields = [];
    };
    $scope.get_field_group_name = function( fieldIndex ){
        var  group_name = '';
        if( angular.isDefined( $scope.options.groups ) && $scope.options.groups.length ){
            angular.forEach($scope.options.groups, function(group, _gIndex){
                angular.forEach(group.fields, function( f ){
                    if( fieldIndex == f ) group_name = group.title;
                });
            });
        }
        return group_name != '' ? ' - ' + group_name : '';
    };
    $scope.availableGroupField = function(actual, gIndex){
        var _field = null, available = true;
        angular.forEach($scope.options.fields, function(field){
            if( field.id == actual ) _field = field;
        });
        angular.forEach($scope.options.groups, function(group, _gIndex){
            angular.forEach(group.fields, function( f ){
                if( _field.id == f && gIndex != _gIndex ) available = false;
            });
        });
        if( angular.isDefined( _field.nbe_type ) &&  _field.nbe_type == 'delivery' ){
            available = false;
        }
        return available;
    };
    $scope.toggle_expand_group = function( gIndex ){
        $scope.options.groups[ gIndex ].isExpand = !$scope.options.groups[ gIndex ].isExpand;
    };
    $scope.updatePopupTriggerIndex = function( init ){
        $scope.options.popup_trigger_index = 0;
        angular.forEach($scope.options.fields, function(field, field_index){
            if( $scope.options.popup_trigger_field == field.id ){
                $scope.options.popup_trigger_index = field_index;
                if( !init ) $scope.options.popup_trigger_value = field.general.data_type.value == 'i' ? '' : '0';
            }
        });
        $scope.updateApp();
    };
    $scope.add_more_nop_break = function( fieldIndex ){
        if( angular.isUndefined( $scope.options['fields'][fieldIndex].general.price_no_range ) ){
            $scope.options['fields'][fieldIndex].general.price_no_range = [];
        }
        var len = $scope.options['fields'][fieldIndex].general.price_no_range.length,
        lastIndex = len > 0 ? len - 1 : 0,
        newQty = len > 0 ? parseInt( $scope.options['fields'][fieldIndex].general.price_no_range[lastIndex] ) + 1 : 1;
        $scope.options['fields'][fieldIndex].general.price_no_range.push([newQty, '']);
    };
    $scope.delete_nop_break = function( fieldIndex, pnrIndex ){
        $scope.options['fields'][fieldIndex].general.price_no_range.splice(pnrIndex, 1);
    };
    $scope.updateApp = function(){
        if ($scope.$root.$$phase !== "$apply" && $scope.$root.$$phase !== "$digest") $scope.$apply(); 
    };
    $scope.updateJsonFields = function(e){
        if( large_amount_field != 'yes' ){
            return true;
        }
        e.preventDefault();
        $scope.getJsonFields();
        return false;
    }
    $scope.getJsonFields = function(){
        var fields = [];
        angular.forEach($scope.options.fields, function(field, fieldIndex){
            fields[fieldIndex] = {
                id: field.id,
                general: {
                    title: field.general.title.value,
                    description: field.general.description.value,
                    data_type: field.general.data_type.value,
                    input_type: field.general.input_type.value,
                    input_option: field.general.input_option.value,
                    text_option: field.general.text_option.value,
                    placeholder: !!field.general.placeholder ? field.general.placeholder.value : '',
                    upload_option: field.general.upload_option.value,
                    enabled: field.general.enabled.value,
                    published: !!field.general.published ? field.general.published.value : 'y',
                    required: field.general.required.value,
                    price_type: field.general.price_type.value,
                    depend_qty: !!field.general.depend_qty ? field.general.depend_qty.value : 'y',
                    depend_quantity: field.general.depend_quantity.value,
                    price: field.general.price.value,
                    price_breaks: field.general.price_breaks.value,
                    attributes: {}
                },
                conditional: {
                    enable: field.conditional.enable
                },
                appearance: {}
            };
            if( field.general.attributes.options.length > 0 ){
                fields[fieldIndex].general.attributes.options = [];
                angular.forEach(field.general.attributes.options, function(op, opIndex){
                    fields[fieldIndex].general.attributes.options[opIndex] = {
                        preview_type: op.preview_type,
                        image: op.image,
                        color: op.color,
                        name: op.name,
                        des: op.des,
                        price: op.price,
                        pt_color: op.pt_color,      // custom charmingprint
                        key: op.key,                // custom charmingprint
                        implicit_value: angular.isDefined( op.implicit_value ) ? op.implicit_value : ''
                    };

                    if( field.general.depend_quantity.value == 'n' ){
                        if( op.price.length == 0 ) {
                            fields[fieldIndex].general.attributes.options[opIndex].price = [""];
                        }
                    }else{
                        angular.forEach($scope.options.quantity_breaks, function(br, brIndex){
                            fields[fieldIndex].general.attributes.options[opIndex].price[brIndex] = typeof op.price[brIndex] != 'undefined' ? op.price[brIndex] : "";
                        });
                    }

                    if( op.color2 ){
                        fields[fieldIndex].general.attributes.options[opIndex].color2 = op.color2;
                    }
                    if( field.appearance.change_image_product.value == 'y' ){
                        fields[fieldIndex].general.attributes.options[opIndex].product_image = op.product_image;
                    }
                    if( op.selected ){
                        fields[fieldIndex].general.attributes.options[opIndex].selected = 'on';
                    }
                    if( op.enable_subattr && op.enable_subattr != 'off' ){
                        fields[fieldIndex].general.attributes.options[opIndex].enable_subattr = 'on';
                        fields[fieldIndex].general.attributes.options[opIndex].sattr_display_type = op.sattr_display_type;
                        if( op.sub_attributes.length > 0 ){
                            fields[fieldIndex].general.attributes.options[opIndex].sub_attributes = op.sub_attributes;
                            fields[fieldIndex].general.attributes.options[opIndex].sub_attributes.forEach(function(sa, saIndex){
                                fields[fieldIndex].general.attributes.options[opIndex].sub_attributes[saIndex].implicit_value = angular.isDefined( sa.implicit_value ) ? sa.implicit_value : '';
                                if( sa.selected ){
                                    fields[fieldIndex].general.attributes.options[opIndex].sub_attributes[saIndex].selected = 'on';
                                }

                                if( field.general.depend_quantity.value == 'n' ){
                                    if( sa.price.length == 0 ) {
                                        fields[fieldIndex].general.attributes.options[opIndex].sub_attributes[saIndex].price = [""];
                                    }
                                }else{
                                    angular.forEach($scope.options.quantity_breaks, function(br, brIndex){
                                        fields[fieldIndex].general.attributes.options[opIndex].sub_attributes[saIndex].price[brIndex] = typeof sa.price[brIndex] != 'undefined' ? sa.price[brIndex] : "";
                                    });
                                }
                            });
                        }
                    }
                    if( op.enable_con && op.enable_con != 'off' ){
                        ['enable_con', 'con_show', 'con_logic', 'depend'].forEach(function( prop ){
                            fields[fieldIndex].general.attributes.options[opIndex][prop] = prop != 'enable_con' ? op[prop] : 'on';
                        });
                    }
                });
            }

            if( field.conditional.enable == 'y' ){
                fields[fieldIndex].conditional.show = field.conditional.show;
                fields[fieldIndex].conditional.logic = field.conditional.logic;

                if( field.conditional.depend.length > 0 ){
                    fields[fieldIndex].conditional.depend = field.conditional.depend;
                }
            }

            angular.forEach(field.appearance, function(data, key){
                fields[fieldIndex].appearance[key] = data.value;
            });

            if( field.nbd_type ){
                fields[fieldIndex].nbd_type = field.nbd_type;
                switch( field.nbd_type ){
                    case 'page':
                        fields[fieldIndex].general.auto_select_page = field.general.auto_select_page;
                        fields[fieldIndex].general.page_display = field.general.page_display;
                        fields[fieldIndex].general.exclude_page = field.general.exclude_page;
                        break;
                    case 'page1':
                        fields[fieldIndex].general.page_display = field.general.page_display;
                        fields[fieldIndex].general.exclude_page = field.general.exclude_page;
                        if( field.general.depend_quantity.value == 'n' ){
                            fields[fieldIndex].general.price_depend_no = field.general.price_depend_no;
                        }
                        if( field.general.price_depend_no == 'y' ){
                            fields[fieldIndex].general.price_no_range = field.general.price_no_range;
                        }
                        break;
                    case 'page2':
                        fields[fieldIndex].general.auto_select_page = field.general.auto_select_page;
                        break;
                    case 'color':
                        fields[fieldIndex].general.attributes.bg_type = field.general.attributes.bg_type;
                        fields[fieldIndex].general.attributes.number_of_sides = field.general.attributes.number_of_sides;
                        fields[fieldIndex].general.attributes.show_as_pt = field.general.attributes.show_as_pt;
                        if( field.general.attributes.options.length > 0 ){
                            if( field.general.attributes.bg_type == 'c' ){
                                angular.forEach(field.general.attributes.options, function(op, opIndex){
                                    fields[fieldIndex].general.attributes.options[opIndex].bg_color = op.bg_color;
                                });
                            }else{
                                angular.forEach(field.general.attributes.options, function(op, opIndex){
                                    fields[fieldIndex].general.attributes.options[opIndex].bg_image = op.bg_image;
                                });
                            }
                        }
                        break;
                    case 'size':
                        fields[fieldIndex].general.attributes.same_size = field.general.attributes.same_size;
                        if( field.general.attributes.same_size == 'n' && field.general.attributes.options.length > 0 ){
                            angular.forEach(field.general.attributes.options, function(op, opIndex){
                                ['product_width', 'product_height', 'real_width', 'real_height', 'real_top', 'real_left'].forEach(function( prop ){
                                    fields[fieldIndex].general.attributes.options[opIndex][prop] = field.general.attributes.options[opIndex][prop];
                                });
                            });
                        }
                        break;
                    case 'dimension':
                        ['min_width', 'max_width', 'step_width', 'default_width', 'min_height', 'max_height', 'step_height', 'default_height', 'mesure'].forEach(function(prop){
                            fields[fieldIndex].general[prop] = field.general[prop];
                        });
                        if( field.general.mesure == 'y' ){
                            ['mesure_type', 'mesure_min_area', 'mesure_range', 'mesure_base_pages', 'mesure_base_qty'].forEach(function( prop ){
                                fields[fieldIndex].general[prop] = field.general[prop];
                            });
                        }
                        break;
                    case 'padding':
                        if( field.general.attributes.options.length > 0 ){
                            angular.forEach(field.general.attributes.options, function(op, opIndex){
                                fields[fieldIndex].general.attributes.options[opIndex].padding = op.padding;
                            });
                        }
                        break;
                    case 'rounded_corner':
                        if( field.general.attributes.options.length > 0 ){
                            angular.forEach(field.general.attributes.options, function(op, opIndex){
                                fields[fieldIndex].general.attributes.options[opIndex].radius = op.radius;
                            });
                        }
                        break;
                    case 'overlay':
                        fields[fieldIndex].general.attributes.number_of_sides = field.general.attributes.number_of_sides;
                        if( field.general.attributes.options.length > 0 ){
                            angular.forEach(field.general.attributes.options, function(op, opIndex){
                                fields[fieldIndex].general.attributes.options[opIndex].overlay_image = op.overlay_image;
                            });
                        }
                        break;
                    case 'fold':
                        if( field.general.attributes.options.length > 0 ){
                            angular.forEach(field.general.attributes.options, function(op, opIndex){
                                fields[fieldIndex].general.attributes.options[opIndex].fold = op.fold;
                            });
                        }
                        break;
                    case 'shape':
                        if( field.general.attributes.options.length > 0 ){
                            angular.forEach(field.general.attributes.options, function(op, opIndex){
                                fields[fieldIndex].general.attributes.options[opIndex].shape = op.shape;
                            });
                        }
                        break;
                    case 'pt_color':                // custom charmingprint
                        if( field.general.attributes.options.length > 0 ){
                            angular.forEach(field.general.attributes.options, function(op, opIndex){
                                fields[fieldIndex].general.attributes.options[opIndex].pt_color = op.pt_color;
                            });
                        }
                        break;
                }
            }

            if( field.nbpb_type ){
                fields[fieldIndex].nbpb_type = field.nbpb_type;
                switch( field.nbpb_type ){
                    case 'nbpb_com':
                        fields[fieldIndex].general.component_icon = field.general.component_icon;
                        if( field.general.pb_config_flat.length > 0 && $scope.options.views.length > 0 ){
                            fields[fieldIndex].general.pb_config = field.general.pb_config;
                        }
                        break;
                    case 'nbpb_image':
                        if( $scope.options.views.length > 0 ){
                            fields[fieldIndex].general.nbpb_image_configs = field.general.nbpb_image_configs;
                        }
                        break;
                    case 'nbpb_text':
                        fields[fieldIndex].general.nbpb_text_configs = field.general.nbpb_text_configs;
                        break;
                }
            }

            if( field.nbe_type ){
                fields[fieldIndex].nbe_type = field.nbe_type;
                switch( field.nbe_type ){
                    case 'frame':
                        if( field.general.attributes.options.length > 0 ){
                            angular.forEach(field.general.attributes.options, function(op, opIndex){
                                fields[fieldIndex].general.attributes.options[opIndex].frame_image = op.frame_image;
                            });
                        }
                        break;
                    case 'actions':
                        if( field.general.attributes.options.length > 0 ){
                            angular.forEach(field.general.attributes.options, function(op, opIndex){
                                fields[fieldIndex].general.attributes.options[opIndex].action = op.action;
                            });
                        }
                        break;
                    case 'delivery':
                        if( field.general.attributes.options.length > 0 ){
                            angular.forEach(field.general.attributes.options, function(op, opIndex){
                                fields[fieldIndex].general.attributes.options[opIndex].delivery = op.delivery;
                                fields[fieldIndex].general.attributes.options[opIndex].max_qty = op.max_qty;
                            });
                        }
                        break;
                }
            }
        });
        function cleanse(obj, path){
            Object.keys(obj).forEach( function( key ){
                var value = obj[key];
                var type = typeof value;
                if ( type === "object" ) {
                    cleanse( value );
                    if ( !Object.keys( value ).length ){
                        //delete obj[key]
                    }
                } else {
                    if( type === "undefined" || key == '$$hashKey' ) {
                        delete obj[key];
                    }else{
                        if( ( key == 'display' || key == 'selected' ) ){
                            if( value === true || value === 'on' ){
                                obj[key] = 'on';
                            }else{
                                delete obj[key];
                            }
                        }
                    }
                }
            });
        }

        cleanse( fields );
        $scope.jsonFields = JSON.stringify( fields );
        setTimeout(function(){
            jQuery('form[name="nboForm"]').submit();
        });
    };
    $scope.initFormulaPrice = function(price, brIndex, fieldIndex, opIndex, saIndex){
        var field = $scope.options.fields[fieldIndex];
        if( field.general.price_type.value != 'mf' ) return;
        $scope.formula     = {
            active: true,
            price: angular.isDefined( price ) ? price : '',
            brIndex: brIndex,
            fieldIndex: angular.isDefined( fieldIndex ) ? fieldIndex : null,
            opIndex: angular.isDefined( opIndex ) ? opIndex : null,
            saIndex: angular.isDefined( saIndex ) ? saIndex : null,
            currentLinkField: '0'
        };
    };
    $scope.saveFormulaPrice = function(){
        var field = $scope.options.fields[$scope.formula.fieldIndex];
        if( $scope.formula.saIndex !== null ){
            field.general.attributes.options[$scope.formula.opIndex].sub_attributes[$scope.formula.saIndex].price[$scope.formula.brIndex] = $scope.formula.price;
        }else{
            if( $scope.formula.opIndex !== null ){
                field.general.attributes.options[$scope.formula.opIndex].price[$scope.formula.brIndex] = $scope.formula.price;
            }else{
                if( field.general.depend_quantity.value == 'n' ){
                    field.general.price.value = $scope.formula.price;
                }else{
                    field.general.price_breaks.value[$scope.formula.brIndex] = $scope.formula.price;
                }
            }
        }
        $scope.cancelFormulaPrice();
    };
    $scope.cancelFormulaPrice = function(){
        $scope.formula     = {
            active: false,
            price: '',
            brIndex: null,
            fieldIndex: null,
            opIndex: null,
            saIndex: null,
            currentLinkField: '0'
        };
    };
    $scope.addFormulaVariable = function( variable ){
        var priceInput = jQuery('#nbo-formula-price')[0],
        caretPos = priceInput.selectionStart,
        front = $scope.formula.price.substring(0, caretPos),
        back = $scope.formula.price.substring(priceInput.selectionEnd, $scope.formula.price.length);

        $scope.formula.price = front + variable + back;
        caretPos = caretPos + variable.length;
        $timeout(function(){
            priceInput.selectionStart = caretPos;
            priceInput.selectionEnd = caretPos;
            priceInput.focus();
        });
    }
    $scope._cancelFormulaPrice = function($event){
        if( jQuery($event.target).is( jQuery('.nbo-formula-popup-wrap') ) ) $scope.cancelFormulaPrice();
    };
    $scope.validateSvgShape = function( fieldIndex, opIndex ){
        $scope.options.fields[fieldIndex].general.attributes.options[opIndex].shape = $scope.options.fields[fieldIndex].general.attributes.options[opIndex].shape.replace(/<!--[\s\S]*?-->/g, "").replace(/<\?xml[\s\S]*?\?>/g, "").replace(/<style[\s\S]*?style>/g, "").replace(/\n|\r/gm, " ").replace( /  /g, " " ).replace(/^ /g, "").replace( / $/g, "" ).replace(/(?:class|id|style|xmlns\:xlink|xml\:space|fill)=['\"][^'\"]*['\"]/g, "" ).replace(/<\/?g>/g , "").replace(/\s{2,}/g, ' ').replace(/(\/?>) </g, '$1<').replace(/<path/g, '<path fill="rgba(0,0,0,0.001)"');
        var reg = /d=["'](.*?)["']/g,
        match = reg.exec( $scope.options.fields[fieldIndex].general.attributes.options[opIndex].shape ),
        path, absPath;
        if( match ){
            path = match[1];
            absPath = Snap.path.toAbsolute(path);
            $scope.options.fields[fieldIndex].general.attributes.options[opIndex].shape = $scope.options.fields[fieldIndex].general.attributes.options[opIndex].shape.replace(reg, 'd="' + absPath + '"');
        }
    };
    $scope.manualPMFields = [];
    $scope.manualPMHozFields = [];
    $scope.manualPMVerFields = [];
    $scope.manualPMCells = [];
    $scope.manualPMCols = 1;
    $scope.manualPMRows = 1;
    $scope.$on('mpm:drop', function(event, dir, fieldId){
        var dirFields = dir == 'hoz' ? $scope.manualPMHozFields : $scope.manualPMVerFields;
        angular.forEach($scope.options.fields, function(field, field_index){
            if( field.id == fieldId ) {
                var _field = {};
                angular.copy( field, _field );
                _field.field_index = field_index;
                dirFields.push(_field);
            }
        });
        $scope.updateManualPm();
    });
    $scope.removePmFiled = function( dir, fieldId ){
        var dirFields = dir == 'hoz' ? $scope.manualPMHozFields : $scope.manualPMVerFields;
        if( dir == 'hoz' ){
            $scope.manualPMHozFields = dirFields.filter(function(field, index){
                return field.id != fieldId;
            });
        } else {
            $scope.manualPMVerFields = dirFields.filter(function(field, index){
                return field.id != fieldId;
            });
        }
        $scope.updateManualPm();
    };
    $scope.$on('mpm:sort', function(event, dir, ids){
        $scope.getPmFieldByIds( dir, ids );
        $scope.updateManualPm();
    });
    $scope.maybeUpdateManualPm = function( init ){
        if( ( $scope.options.manual_build_pm === 'on' || $scope.options.manual_build_pm === true ) && $scope.options.manual_pm != '' ){
            if( init ) $scope.getPmFields( $scope.options.manual_pm );
            $scope.updateManualPm();
        }
    };
    $scope.updateManualPmIndex = function(){
        var manualPMHozFields = [], manualPMVerFields = [];
        angular.forEach($scope.options.fields, function(field, field_index){
            angular.forEach($scope.manualPMHozFields, function(_field, _field_index){
                if( field.id == _field.id ) {
                    var __field = {};
                    angular.copy( field, __field );
                    __field.field_index = field_index;
                    manualPMHozFields.push(__field);
                }
            });
            angular.forEach($scope.manualPMVerFields, function(_field, _field_index){
                if( field.id == _field.id ) {
                    var __field = {};
                    angular.copy( field, __field );
                    __field.field_index = field_index;
                    manualPMVerFields.push(__field);
                }
            });
        });
        $scope.manualPMHozFields = manualPMHozFields;
        $scope.manualPMVerFields = manualPMVerFields;
    };
    $scope.updateManualPm = function(){
        $scope.manualPMFields = manualPMFieldFilter($scope.options.fields, $scope.manualPMVerFields.concat( $scope.manualPMHozFields ));
        $scope.updateManualPmIndex();
        $scope.manual_pm = '';
        if( $scope.manualPMHozFields.length ){
            angular.forEach($scope.manualPMHozFields, function(field){
                $scope.manual_pm += field.id + ',';
            });
            $scope.manual_pm = $scope.manual_pm.slice(0, -1);
        }
        $scope.manual_pm += '|';

        if( $scope.manualPMVerFields.length ){
            angular.forEach($scope.manualPMVerFields, function(field){
                $scope.manual_pm += field.id + ',';
            });
            $scope.manual_pm = $scope.manual_pm.slice(0, -1);
        }
        $scope.manual_pm += '|';

        var cols = 1, rows = 1, i, j, index, colspan = 1, rowspan = 1, looptimes = 1;
        angular.forEach($scope.manualPMHozFields, function(hField, hIndex){
            cols *= hField.general.attributes.options.length;
            looptimes = 1, colspan = 1;
            angular.forEach($scope.manualPMHozFields, function(hField2, hIndex2){
                if( hIndex2 < hIndex ){
                    looptimes *= hField2.general.attributes.options.length;
                }else if( hIndex2 > hIndex ){
                    colspan *= hField2.general.attributes.options.length;
                }
            });
            hField.looptimes = looptimes;
            hField.colspan = colspan;
        });
        angular.forEach($scope.manualPMVerFields, function(vField, vIndex){
            rows *= vField.general.attributes.options.length;
            looptimes = 1, rowspan = 1;
            angular.forEach($scope.manualPMVerFields, function(vField2, vIndex2){
                if( vIndex2 < vIndex ){
                    looptimes *= vField2.general.attributes.options.length;
                }else if( vIndex2 > vIndex ){
                    rowspan *= vField2.general.attributes.options.length;
                }
            });
            vField.looptimes = looptimes;
            vField.rowspan = rowspan;
        });
        if( $scope.manualPMHozFields.length == 0 || $scope.manualPMVerFields.length == 0 ){
            if( $scope.manualPMHozFields.length == 0 ){
                if( $scope.manualPMVerFields.length == 0 ){
                    $scope.manual_pm += ',';
                } else {
                    for( j = 0; j < rows; j++ ){
                        if( angular.isUndefined( $scope.manualPMCells[j] ) ){
                            $scope.manualPMCells[j] = '';
                        }
                        $scope.manual_pm += $scope.manualPMCells[j] + ',';
                    }
                }
            }else{
                for( i = 0; i < cols; i++ ){
                    if( angular.isUndefined( $scope.manualPMCells[i] ) ){
                        $scope.manualPMCells[i] = '';
                    }
                    $scope.manual_pm += $scope.manualPMCells[i] + ',';
                }
                $scope.manualPMCells = $scope.manualPMCells.slice(0, i * j);
            }
        }else{
            for( j = 0; j < rows; j++ ){
                for( i = 0; i < cols; i++ ){
                    index = j * cols + i;
                    if( angular.isUndefined( $scope.manualPMCells[index] ) ){
                        $scope.manualPMCells[index] = '';
                    }
                    $scope.manual_pm += $scope.manualPMCells[index] + ',';
                }
            }
        }
        $scope.manualPMCells = $scope.manualPMCells.slice(0, rows * cols);
        $scope.manual_pm = $scope.manual_pm.slice(0, -1);
        $scope.manualPMCols = cols;
        $scope.manualPMRows = rows;

        $scope.updateApp();
    };
    $scope.getPmFieldByIds = function( dir, ids ){
        var dirFields = dir == 'hoz' ? 'manualPMHozFields' : 'manualPMVerFields';
        if( ids.length > 0 ){
            $scope[dirFields] = [];
            ids.map(function( fieldId ){
                angular.forEach($scope.options.fields, function(field, field_index){
                    if( field.id == fieldId ) {
                        var _field = {};
                        angular.copy( field, _field );
                        _field.field_index = field_index;
                        $scope[dirFields].push(_field);
                    }
                });
            });
        }
    };
    $scope.getPmFields = function( manualPM ){
        var arr = manualPM.split("|"),
        hozIds = arr[0].split(","),
        verIds = arr[1].split(","),
        prices = arr[2].split(",");

        $scope.getPmFieldByIds( 'hoz', hozIds );
        $scope.getPmFieldByIds( 'ver', verIds );

        $scope.manualPMCells = [];
        var cols = 1, rows = 1, i, j, index;

        angular.forEach($scope.manualPMHozFields, function(hField, hIndex){
            cols *= hField.general.attributes.options.length;
        });
        angular.forEach($scope.manualPMVerFields, function(vField, hIndex){
            rows *= vField.general.attributes.options.length;
        });

        if( $scope.manualPMHozFields.length == 0 || $scope.manualPMVerFields.length == 0 ){
            if( $scope.manualPMHozFields.length == 0 ){
                if( $scope.manualPMVerFields.length != 0 ){
                    for( j = 0; j < rows; j++ ){
                        $scope.manualPMCells[j] = angular.isDefined( prices[j] ) ? prices[j] : '';
                    }
                }
            }else{
                for( i = 0; i < cols; i++ ){
                    $scope.manualPMCells[i] = angular.isDefined( prices[i] ) ? prices[i] : '';
                }
            }
        }else{
            for( j = 0; j < rows; j++ ){
                for( i = 0; i < cols; i++ ){
                    index = j * cols + i;
                    $scope.manualPMCells[index] = angular.isDefined( prices[index] ) ? prices[index] : '';
                }
            }
        }
    };
    $scope.clearPm = function(){
        $scope.manualPMCells = [];
        var cols = 1, rows = 1, i, j, index;

        angular.forEach($scope.manualPMHozFields, function(hField, hIndex){
            cols *= hField.general.attributes.options.length;
        });
        angular.forEach($scope.manualPMVerFields, function(vField, hIndex){
            rows *= vField.general.attributes.options.length;
        });

        if( $scope.manualPMHozFields.length == 0 || $scope.manualPMVerFields.length == 0 ){
            if( $scope.manualPMHozFields.length == 0 ){
                if( $scope.manualPMVerFields.length != 0 ){
                    for( j = 0; j < rows; j++ ){
                        $scope.manualPMCells[j] = '';
                    }
                }
            }else{
                for( i = 0; i < cols; i++ ){
                    $scope.manualPMCells[i] = '';
                }
            }
        }else{
            for( j = 0; j < rows; j++ ){
                for( i = 0; i < cols; i++ ){
                    index = j * cols + i;
                    $scope.manualPMCells[index] = '';
                }
            }
        }
    };
    $scope.init();
}).directive('stringToNumber', function() {
    return {
        require: 'ngModel',
        link: function(scope, element, attrs, ngModel) {
            ngModel.$parsers.push(function(value) {
                return '' + value;
            });
            ngModel.$formatters.push(function(value) {
                return parseFloat(value);
            });
        }
    };
}).directive('convertToNumber', function() {
    return {
        require: 'ngModel',
        link: function(scope, element, attrs, ngModel) {
            ngModel.$parsers.push(function(val) {
                return val != null ? parseInt(val, 10) : null;
            });
            ngModel.$formatters.push(function(val) {
                return val != null ? '' + val : null;
            });
        }
    };    
}).directive( 'nbdColorPicker', function() {
    return {
        restrict: 'A',
        scope: {
            value: '=nbdColorPicker'
        },
        link: function( scope, element ) {
            function init(){
                jQuery(element).val(scope.value);
                jQuery(element).wpColorPicker({
                    change: function (evt, ui) {
                        var $input = jQuery(this);
                        setTimeout(function () {
                            if ($input.wpColorPicker('color') !== $input.data('tempcolor')) {
                                $input.change().data('tempcolor', $input.wpColorPicker('color'));
                                $input.val($input.wpColorPicker('color'));
                            }
                        }, 10);
                    }
                });
            };
            scope.$watch('value', function(newValue, oldValue) {
                if (newValue != oldValue){
                    jQuery(element).wpColorPicker('color', newValue);
                }
            }, true);
            scope.$on("$destroy", function(){
                jQuery(element).parents('.wp-picker-container').remove();
            });
            init();
        }
    };
}).directive( 'nbdSelect2', function($timeout) {
    return {
        restrict: 'A',
        link: function( scope, element ) {
            $timeout(function() {
                jQuery(element).selectWoo();
            });
        }
    };
}).directive( 'nbdTab', function($timeout) {
    return {
        restrict: 'A',
        link: function( scope, element ) {
            $timeout(function() {
                jQuery.each( jQuery(element).find('.nbd-field-tab'), function(){
                    jQuery(this).on('click', function(){
                        var target = jQuery(this).data('target');
                        jQuery(this).parents('.nbd-field-wrap').find('.nbd-field-content').removeClass('active');
                        jQuery(this).parent('ul').find('li').removeClass('active');
                        jQuery(this).parents('.nbd-field-wrap').find('.'+target).addClass('active');
                        jQuery(this).addClass('active');
                    });
                });
            });
        }
    };
}).directive( 'nbdTip', function($timeout) {
    return {
        restrict: 'E',
        scope: {
            dataTip: '@tip'
        },
        template: '<span class="woocommerce-help-tip" data-tip="{{dataTip}}" ></span>',
        link: function( scope, element, attrs ) {
            var tiptip_args = {
                'attribute': 'data-tip',
                'fadeIn': 50,
                'fadeOut': 50,
                'delay': 200
            };
            $timeout(function() {
                jQuery(element).find('.woocommerce-help-tip').tipTip( tiptip_args );
            }, 0);
        }
    };
}).directive( 'nboSubAttrSelect', function($timeout) {
    return {
        restrict: 'E',
        scope: {
            find: '=',
            oind: '=',
            cind: '=',
            sind: '=',
            con: '=',
            fields: '='
        },
        template: '<select class="nbd-w-100i" name="options[fields][{{find}}][general][attributes][options][{{oind}}]{{sind_name}}[depend][{{cind}}][subval]" ng-if="available" ng-model="con.subval"><option ng-repeat="attr in attributes" value="{{$index}}">{{attr.name}}</option></select>',
        link: function( scope, element, attrs ) {
            var selectedField, selectedOption;
            if( scope.sind ){
                scope.sind_name = '[sub_attributes]['+ scope.sind +']';
            }else{
                scope.sind_name = '';
            }

            function updateSubAttrs(){
                scope.attributes = [];
                scope.available = true;

                scope.fields.forEach(function(field){
                    if( field.id == scope.con.id ){
                        selectedField = field;
                        if( field.general.data_type.value != 'm' ){
                            scope.available = false;
                        }else{
                            if( field.general.attributes.options.length == 0 ){
                                scope.available = false;
                            }else{
                                field.general.attributes.options.forEach(function(op, opIndex){
                                    if( opIndex == scope.con.val ){
                                        selectedOption = op;
                                        if( op.enable_subattr && op.enable_subattr != 'off' ){
                                            if( op.sub_attributes.length == 0 ){
                                                scope.available = false;
                                            }else{
                                                scope.attributes = op.sub_attributes;
                                            }
                                        }else{
                                            scope.available = false;
                                        }
                                    }
                                });
                            }
                        }
                    }
                });

                if( !selectedField || !selectedOption ) scope.available = false;
                if( !scope.available ){
                    scope.con.subval = '';
                }
            };

            scope.$watchCollection('con.id', function(newValue, oldValue) {
                if (newValue) {
                    updateSubAttrs();
                }
            }, true);
            scope.$watchCollection('con.val', function(newValue, oldValue) {
                if (newValue != oldValue ) {
                    updateSubAttrs();
                }
            }, true);
        }
    };
}).filter('range', function() {
    return function (input, total) {
        total = parseInt(total);
        for (var i = 0; i < total; i++) {
            input.push(i);
        }
        return input;
    };
}).directive( 'nbdPmDroppable', function($timeout) {
    return {
        restrict: 'A',
        scope: {
            dataDir: '@dir'
        },
        link: function( scope, element, attrs ) {
            $timeout(function() {
                jQuery( element ).droppable({
                    hoverClass: 'nbd-dropzone-hover',
                    accept: '.nbd-darg-pm-field',
                    drop: function(evt, ui) {
                        scope.$emit('mpm:drop', scope.dataDir, ui.helper.data('id'));
                    }
                }).sortable({
                    items: '> .nbd-pm-field',
                    scroll: true,
                    placeholder: "ui-sortable-placeholder",
                    update: function() {
                        var ids = jQuery( element ).children('.nbd-pm-field').map(function(id, elem) {
                            return jQuery(elem).data('id');
                        }).get();
                        scope.$emit('mpm:sort', scope.dataDir, ids);
                        $timeout(function(){
                            jQuery( element ).sortable('refreshPositions');
                        });
                    }
                })
            });
        }
    };
}).directive( 'nbdPmDraggable', function($timeout) {
    return {
        restrict: 'A',
        link: function( scope, element, attrs ) {
            $timeout(function() {
                jQuery( element ).draggable({
                    helper: 'clone',
                    cursor: "move"
                });
            });
        }
    };
}).filter('pmField', function(){
    return function(fields, usedFields){
        var filtered_fileds = [];
        angular.forEach(fields, function(field, field_index){
            if( usedFields.indexOf(''+field_index) < 0 && field.general.data_type.value == 'm' && field.general.enabled.value == 'y' && field.general.attributes.options.length > 0 && field.conditional.enable == 'n' ){
                if( !((angular.isDefined(field.nbd_type) && ( field.nbd_type == 'page' || field.nbd_type == 'page2' )) || angular.isDefined(field.nbpb_type) || ( angular.isDefined(field.nbe_type) && ( field.nbe_type == 'delivery' || field.nbe_type == 'actions' ) )) ){
                    var _field = {};
                    angular.copy(field, _field);
                    _field.field_index = field_index;
                    filtered_fileds.push(_field);
                }
            }
        });
        return filtered_fileds;
    }
}).filter('bulkField', function(){
    return function(fields){
        var filtered_fileds = [];
        angular.forEach(fields, function(field, field_index){
            var exclude = ['dpi', 'page', 'page1', 'page2', 'page3', 'orientation', 'area', 'dimension', 'size', 'shape'];
            var check_od = true;
            if( angular.isDefined(field.nbd_type) && exclude.indexOf(field.nbd_type) > -1 ){
                check_od = false;
            }
            if( angular.isDefined(field.nbpb_type) ){
                check_od = false;
            }
            if( angular.isDefined(field.nbe_type) && ( field.nbe_type == 'delivery' || field.nbe_type == 'actions' ) ){
                check_od = false;
            }
            if( check_od && field.general.data_type.value == 'm' && field.general.enabled.value == 'y' && field.general.attributes.options.length > 0 && field.conditional.enable == 'n' ){
                var _field = {};
                angular.copy(field, _field);
                _field.field_index = field_index;
                filtered_fileds.push(_field);
            }
        });
        return filtered_fileds;
    };
}).filter('manualPMField', function(){
    return function(fields, excludeFields){
        var filtered_fileds = [];
        angular.forEach(fields, function(field, field_index){
            var exclude = ['dpi', 'page', 'page1', 'page2', 'orientation', 'area', 'dimension', 'size', 'shape'];
            var check_od = true, include = true;
            if( excludeFields.length > 0 ){
                angular.forEach(excludeFields, function(excludeField){
                    if( excludeField.id == field.id ) include = false;
                });
            }
            if( angular.isDefined(field.nbd_type) && exclude.indexOf(field.nbd_type) > -1 ){
                check_od = false;
            }
            if( angular.isDefined(field.nbpb_type) ){
                check_od = false;
            }
            if( angular.isDefined(field.nbe_type) && ( field.nbe_type == 'delivery' || field.nbe_type == 'actions' ) ){
                check_od = false;
            }
            if( check_od && include && field.general.data_type.value == 'm' && field.general.enabled.value == 'y' && field.general.attributes.options.length > 0 && field.conditional.enable == 'n' ){
                var _field = {};
                angular.copy(field, _field);
                _field.field_index = field_index;
                filtered_fileds.push(_field);
            }
        });
        return filtered_fileds;
    };
}).filter('allFields', function(){
    return function(fields){
        var filtered_fileds = [];
        angular.forEach(fields, function(field, field_index){
            var _field = {};
            angular.copy(field, _field);
            _field.field_index = field_index;
            filtered_fileds.push(_field);
        });
        return filtered_fileds;
    };
});
jQuery( document ).ready(function($){
    $(".nbo-dates input:not(.hasDatepicker)").datepicker({
        defaultDate: "",
        dateFormat: "yy-mm-dd",
        numberOfMonths: 1,
        showButtonPanel: true,
        showOn: "button",
        buttonImage: nbd_options.calendar_image,
        buttonImageOnly: true,
        onSelect: function (selectedDate) {
            var option = $(this).is('.date_from') ? "minDate" : "maxDate";
            var instance = $(this).data("datepicker"),
                date = $.datepicker.parseDate(
                    instance.settings.dateFormat ||
                    $.datepicker._defaults.dateFormat,
                    selectedDate, instance.settings);
            var dates = $(this).parents('.nbo-dates').find('input');
            dates.not(this).datepicker("option", option, date);
        }
    });
    $('.nbo-toggle-nav').on('click', function(){
        $('.nbo-toggle').removeClass('active');
        if( $(this).is(':checked') ){
            $($(this).data('toggle')).addClass('active');
        }
    });
});