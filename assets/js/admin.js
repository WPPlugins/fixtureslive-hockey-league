jQuery(document).ready(function($){
	$("#tabs").tabs();
	$('.color-picker').wpColorPicker();
});


jQuery(document).ready(function($) {


	var comps = $("#comps");
	var wp_title = $("input#title");
	var fl_div_id = $("#FLID")
	var comps_list = $("ul",comps);
	var menu_order = $("input#menu_order")

	$("#find_comps_init").bind('click', function(){
		var data = {
			action: 'flajaxfindcomps',
			comp_type: $(this).attr('data-comp_type')
		};

		

		// We can also pass the url value separately from ajaxurl for front end AJAX implementations
		jQuery.post(ajaxurl, data, function(response) {
			result = JSON.parse(response);
			if(result) {
				$('li',comps_list).remove();
				for(var k in result) {
					var comp_details = result[k];
					var comp_listing = $('<li data-menu-order="' + comp_details.display_order +'" data-league-name="' + k + '" data-season-name="' + comp_details.season + '" data-fl_id="' + comp_details.divison_id + '"><a href="#">' + k + ' ' + comp_details.season + '</a></li>')
					comp_listing.bind('click', function(){
						wp_title.attr('value', $(this).attr('data-league-name') + ' ' + $(this).attr('data-season-name'));
						fl_div_id.attr('value', $(this).attr('data-fl_id'));
						$('#title-prompt-text').toggle()
						menu_order.attr('value', $(this).attr('data-menu-order'));
					});
					comps_list.append(comp_listing);
				}
			}
		});
		return false;
	});
});

