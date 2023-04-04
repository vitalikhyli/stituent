<head>
<style>

	/*Set the top margin at .5 inches, side margin to .19 inches, vertical pitch to 1 inch, horizontal pitch to 2.75 inches, paper size to 8.5 by 11 inches, label height at 1 inch and label width at 2.63 inches. The number across is three and the number down is 10.*/

	/*https://stackoverflow.com/questions/11649052/print-margins-in-dompdf*/
	/*@page { margin: 0in .1875in; }*/
	/*Margins: Top 0.5", Bottom 0.5", Left 0.21975", Right 0.21975".*/
	/*@page { margin-top:.25in; margin-left:.19in; }*/

	@page { margin-top:.5in; margin-bottom:0in; margin-left:.21975in; margin-right:.21975in; }

	/*body { margin: 0in .1875in;}*/
	body {
		font-family:sans-serif;
	    /*margin: 0in .1875in;*/
	    /*width: 8.5in;*/
	}
</style>
</head>


<body>


	@foreach($constituents->chunk(30) as $page)
		<center>
			<table style="width:8.125in;" cellpadding="0" cellspacing="0">
				@foreach($page as $constituent)

					@if($loop->iteration % 3 == 1)
						<tr>
					@endif

					<td style="height:1.0in;width:33.33%;font-size:9pt;text-align:center;padding:0px 5px;">
						<b>{!! nl2br($constituent->full_name) !!}</b><br />
						{{ $constituent->address_line_street}}<br />
						{{ $constituent->address_city}}, {{ $constituent->address_state }} {{ $constituent->address_zip }}
					</td>

					@if($loop->iteration % 3 == 0)
						</tr>
					@endif

				@endforeach
			</table>
		</center>

		@if(!$loop->last)
			<div style="clear:left;display:block;page-break-after:always;"></div>
		@endif

	@endforeach

	

</body>
