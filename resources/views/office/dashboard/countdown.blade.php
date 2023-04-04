@if(\Carbon\Carbon::parse($datetime)->isFuture())

	<div class="text-3xl text-center text-grey-darker border-t border-b p-8">
		{{ $title }}
		<div id="demo" class="h-32 font-bold text-blue font-mono" style="font-size:300%;"></div>
	</div>

	<script type="text/javascript">
		var countDownDate = new Date("{!! $datetime  !!}").getTime();
		var x = setInterval(function() {
		  var now = new Date().getTime();
		  var distance = countDownDate - now;
		  var days = Math.floor(distance / (1000 * 60 * 60 * 24));
		  var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
		  var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
		  var seconds = Math.floor((distance % (1000 * 60)) / 1000);
		  if (minutes < 10) {
		  	minutes = '0' + minutes;
		  }
		  if (seconds < 10) {
		  	seconds = '0' + seconds;
		  }
		  hours = hours + (days * 24);
		  document.getElementById("demo").innerHTML = hours + ":" + minutes + ":" + seconds;
		}, 1000);
	</script>

@else



@endif