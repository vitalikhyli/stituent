<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC-IZEP4hINlLnKuxlLHaOEy6C5pgt8tlc&libraries=drawing">
</script>


<!--  <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC-IZEP4hINlLnKuxlLHaOEy6C5pgt8tlc">
</script> -->


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

			drawingManager = new google.maps.drawing.DrawingManager({
    drawingMode: google.maps.drawing.OverlayType.MARKER,
    drawingControl: true,
    drawingControlOptions: {
      position: google.maps.ControlPosition.TOP_CENTER,
      drawingModes: [
        google.maps.drawing.OverlayType.MARKER,
        // google.maps.drawing.OverlayType.CIRCLE,
        google.maps.drawing.OverlayType.POLYGON,
        // google.maps.drawing.OverlayType.POLYLINE,
        // google.maps.drawing.OverlayType.RECTANGLE,
      ],
    },
    markerOptions: {
      icon:
        "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
    },
    // circleOptions: {
    //   fillColor: "#ffff00",
    //   fillOpacity: 1,
    //   strokeWeight: 5,
    //   clickable: false,
    //   editable: true,
    //   zIndex: 1,
    // },
        polygonOptions: {
      // fillColor: "#ffff00",
      fillOpacity: .2,
      strokeWeight: 5,
      clickable: true,
      editable: true,
      zIndex: 1,
    },
  });
  drawingManager.setMap(map);
google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event) {
    if (event.type == 'polygon') {
      alert("Polygon Completed");
      listOfPolygons.push(new google.maps.Polygon({
        paths: event.overlay.getPath().getArray(),
        strokeColor: '#FF0000',
        strokeOpacity: 0.8,
        strokeWeight: 3,
        fillColor: '#FF0000',
        fillOpacity: 0.35
      }));
      listOfPolygons[listOfPolygons.length - 1].setMap(map);
      listOfPolygons[listOfPolygons.length - 1].addListener('click', showArrays);
      alert(listOfPolygons.length);
    }
  });
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
function showArrays(event) {
  // Since this polygon has only one path, we can call getPath() to return the
  // MVCArray of LatLngs.

  alert(vertices);
  var vertices = this.getPath();

  var contentString = '<b>Bermuda Triangle polygon</b><br>' +
      'Clicked location: <br>' + event.latLng.lat() + ',' + event.latLng.lng() +
      '<br>';

  // Iterate over the vertices.
  for (var i =0; i < vertices.getLength(); i++) {
    var xy = vertices.getAt(i);
    contentString += '<br>' + 'Coordinate ' + i + ':<br>' + xy.lat() + ',' +
      xy.lng();
  }

  // Replace the info window's content and position.
  infoWindow.setContent(contentString);
  infoWindow.setPosition(event.latLng);

  infoWindow.open(map);
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