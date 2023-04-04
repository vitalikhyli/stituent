 <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC-IZEP4hINlLnKuxlLHaOEy6C5pgt8tlc">
</script>


<script type="text/javascript">
// ==================================================> DASHBOARD MAP
	$(document).ready(function() {
		var map;
		var json = "/campaign/lists/{{ $list->id }}/map";
		var infowindow = new google.maps.InfoWindow();

		// initializeMap(json);

		function initializeMap(json) {

		    var mapProp = {
		        mapTypeId: google.maps.MapTypeId.ROADMAP
		    };

		    map = new google.maps.Map(document.getElementById("map"), mapProp);

		    loadJson(json, map);

		}

		function loadJson(json, map) {
			$.getJSON(json, function(json1) {

				var southWest = new google.maps.LatLng(json1.bounds.min_lat, json1.bounds.min_lng);
				var northEast = new google.maps.LatLng(json1.bounds.max_lat, json1.bounds.max_lng);
				var bounds = new google.maps.LatLngBounds(southWest,northEast);
				map.fitBounds(bounds);
		    
			    $.each(json1.households, function (key, data) {

			        var latLng = new google.maps.LatLng(data.lat, data.lng);
			        let iconurl = "http://maps.google.com/mapfiles/ms/icons/";
						iconurl += data.color + "-dot.png";

			        var marker = new google.maps.Marker({
			            position: latLng,
			            map: map,
			            // label: "" + data.contacts,
			            icon: {
					      url: iconurl
					    },
			            title: data.name
			        });

			        var details = "<div class='p-2'>";
			        details += "<b>" + data.name + "</b><br>";
			        details += data.address + "<br>";
			        if (data.phone) {
				        details += data.phone;
				    }
				    details += "<hr>";
			        details += "<a class='btn btn-default mt-2 w-full' href='" + data.url + "'>View "+data.name+"</a>";
			        details += "</div>";

			        bindInfoWindow(marker, map, infowindow, details);

			        //    });

			    });
			});
		}

		function bindInfoWindow(marker, map, infowindow, strDescription) {
		    google.maps.event.addListener(marker, 'click', function () {
		    	//alert();
		        infowindow.setContent(strDescription);
		        infowindow.open(map, marker);
		    });
		}

		google.maps.event.addDomListener(window, 'load', initializeMap(json));

		// google.maps.event.addDomListener(document.getElementById('timeframe-week'), 'click', function() {
		// 	initializeMap("/office/dashboard/activity-map?timeframe=7", map);
		// 	$('.map-timeframe').removeClass('bg-blue text-white');
		// 	$('#timeframe-week').addClass('bg-blue text-white');
		// });
		// google.maps.event.addDomListener(document.getElementById('timeframe-month'), 'click', function() {
		// 	initializeMap("/office/dashboard/activity-map?timeframe=30");
		// 	$('.map-timeframe').removeClass('bg-blue text-white');
		// 	$('#timeframe-month').addClass('bg-blue text-white');
		// });
		// google.maps.event.addDomListener(document.getElementById('timeframe-year'), 'click', function() {
		// 	initializeMap("/office/dashboard/activity-map?timeframe=365");
		// 	$('.map-timeframe').removeClass('bg-blue text-white');
		// 	$('#timeframe-year').addClass('bg-blue text-white');
		// });
	});

</script>