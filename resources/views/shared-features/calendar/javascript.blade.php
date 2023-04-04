<script type="text/javascript">
	$(document).ready(function() {

		

		var cal = $('#calendar').clndr({
		  template: $('#calendar-template').html(),
		  events: JSON.parse('{!! $events_json !!}'),
		  clickEvents: {
			    click: function(target) {

			      if(target.events.length) {
				      	$('.day.selected').removeClass('selected');
				      	$(target.element).addClass('selected');
				      	var date = target.events[0].date;

				      	var clicked_date = moment(date);

				      	getDateInfo(clicked_date);
				    }
			    }
			},
		  adjacentDaysChangeMonth: false
		  // forceSixRows: true
		});

		$('#calendar-wrapper').removeClass('opacity-0');
		$('#calendar-wrapper').addClass('opacity-100');

		function getDateInfo(date) {
			$.get('/events/'+date.format('YYYY-MM-DD'), function(response) {
				$('#events-by-date').replaceWith(response);
			});
		}

		$(document).on('mouseover','#calendar:not(.loaded)', function() {
			$(this).addClass('loaded');
			$.get('calendar/events/full', function(response) {
				cal.addEvents(JSON.parse(response));
			});

		});
	});
</script>