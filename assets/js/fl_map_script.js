	jQuery(document).ready(function($) {

					var startLong = 151.25;
					var startLat = -33.92;
	   				var map = new google.maps.Map(document.getElementById('map'), {
				      zoom: 7,
				      center: new google.maps.LatLng(51.512161,-0.132051),
				      mapTypeId: google.maps.MapTypeId.ROADMAP
				   });

				    var infowindow = new google.maps.InfoWindow();
				    var bounds = new google.maps.LatLngBounds();
				    
				     var marker, i;
					 for (i = 0; i < map_points.length; i++) {  

					      marker = new google.maps.Marker({
					        position: new google.maps.LatLng(map_points[i].lat, map_points[i].lng),
					        map: map,
					        animation: google.maps.Animation.DROP,
							clickable: true,
							icon: 'https://maps.gstatic.com/mapfiles/ms2/micons/green-dot.png'
					      });

					      bounds.extend(marker.position);

					      marker.html_info = '<img width="50" src="http://www.fixtureslive.com/uploads/logos/' + map_points[i].logo + '" /><br/><b>' + map_points[i].club + '</b><br/>Postcode: ' + map_points[i].zip + '<br/>Contact: ' + map_points[i].contact_name + '<br/>Website: <a targert="_blank" href="' + map_points[i].url + '">Visit Website</a>'

					      google.maps.event.addListener(marker, 'click', (function(marker, i) {
					        return function() {
					          infowindow.setContent('<div class="map_info">' + marker.html_info + '</div>');
					          infowindow.open(map, marker);
					        }
					      })(marker, i));
				  }
				
				  map.fitBounds(bounds);

	
			function setGlobalPos(pos) {
				var user = new google.maps.Marker({
			        position: new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude),
			        map: map,
					clickable: false
			      });
				//map.setCenter(new google.maps.LatLng(pos.coords.latitude, pos.coords.longitude));
				map.setCenter(bounds.getCenter());
				map.setZoom(10);
			}

			google.maps.event.addListener(map,'center_changed',function() { checkBounds(); });

			function checkBounds() {    
			    if(! bounds.contains(map.getCenter())) {
			      var C = map.getCenter();
			      var X = C.lng();
			      var Y = C.lat();

			      var AmaxX = bounds.getNorthEast().lng();
			      var AmaxY = bounds.getNorthEast().lat();
			      var AminX = bounds.getSouthWest().lng();
			      var AminY = bounds.getSouthWest().lat();

			      if (X < AminX) {X = AminX;}
			      if (X > AmaxX) {X = AmaxX;}
			      if (Y < AminY) {Y = AminY;}
			      if (Y > AmaxY) {Y = AmaxY;}

			      map.setCenter(new google.maps.LatLng(Y,X));
			    }
			}

	});