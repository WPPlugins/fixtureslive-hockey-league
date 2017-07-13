jQuery(document).ready(function($) {
	// -- Init Tabs
	$("#tabs").tabs();

	jQuery(".cup_flexi").flexslider({
	    animation: "slide", 
	    selector: ".slides > li", 
	    slideshow: false, 
	    controlNav: true,
	    directionNav: false,
	    randomize: false,
	    slideshowSpeed: 5000,
	    manualControls: '.slide_indexes li',
	    controlsContainer: '.cup_viewer',
	    smoothHeight: true
	});

	// -- Venue Map
	var venue_map = $('#venue-map');
	if(venue_map.length) {

		var map = new google.maps.Map(document.getElementById('venue-map'), {
	      zoom: 16,
	      center: new google.maps.LatLng($(venue_map).attr('data-lat'),$(venue_map).attr('data-lng')),
	      mapTypeId: google.maps.MapTypeId.ROADMAP
	   });


		 marker = new google.maps.Marker({
	        position: new google.maps.LatLng($(venue_map).attr('data-lat'),$(venue_map).attr('data-lng')),
	        map: map,
	        animation: google.maps.Animation.DROP,
			clickable: true,
			icon: 'https://maps.gstatic.com/mapfiles/ms2/micons/green-dot.png'
	      });

	}

	// -- Archvies
	$('.archive-toggler').each(function(index,e){	
		var o = $(this);
		// -- Get All Of the <th> Underneath
		if(index>0) {
			$(this).nextUntil('tr.archive-toggler').toggle();	
		} else {
			$(this).addClass('active');
		}

		o.css({'cursor':'pointer'});

		o.bind('click',function(e) {

			if(	$(this).hasClass('active') ) {
				$(this).removeClass('active')
			} else {
				$(this).addClass('active');
			}
			
			$(this).nextUntil('tr.archive-toggler').toggle();
		});
	})




})



