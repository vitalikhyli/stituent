@extends('u.base')

@section('title')
    @lang('Home')
@endsection

@section('breadcrumb')
    &nbsp;<b>@lang('Home')</b>
@endsection

@section('style')

	@include('shared-features.calendar.style')

	<style>

		.red-tooltip + .tooltip > .tooltip-inner {
			font-size:150%;
			padding:20px;
			max-width:350px;
		}

		@keyframes fadeInUp {
		    from {
		        transform: translate3d(0,40px,0);
		        opacity: 0;
		    }
		    to {
		        transform: translate3d(0,0,0);
		        opacity: 1;
		    }
		}

		@-webkit-keyframes fadeInUp {
		    from {
		        transform: translate3d(0,40px,0);
		        opacity: 0;
		    }
		    to {
		        transform: translate3d(0,0,0);
		        opacity: 1
		    }
		}
		.FadeInUp {
		    animation-duration: 1s;
		    animation-name: fadeInUp;
		    -webkit-animation-name: fadeInUp;
		}

	</style>

@endsection



@section('main')
	@if(Auth::user()->team->logo_orient == 'landscape')

	<div class="text-center">
		<div class="w-full FadeInUp">
			<a href="/admin/team_col/logo_orient/bulky">
				@if(strpos(Auth::user()->team->logo_img, "://") !== false)

					<img class="" style="max-height:150px;" src="{{ Auth::user()->team->logo_img }}" />

				@elseif(strpos(Auth::user()->team->logo_img, "/images/logos") !== false)

					<img class="" style="max-height:150px;" src="http://{{ Request::getHost() }}{{ Auth::user()->team->logo_img }}" />
				
				@else

					<img style="max-height:150px;" src="/office/show_logo/{{ Auth::user()->team->id }}" />

				@endif
			</a>
		</div>



		<div class="w-full">
			<center>
			<div style="width:500px;">

				<div class="dropdown my-6">

				<input id="cfbar" type="text" placeholder="Start Here" style="font-family:Arial, Font Awesome\ 5 Free" data-toggle="dropdown" autocomplete="off" class="w-full text-black appearance-none rounded-full px-6 py-3 bg-grey-lightest border-2 border-grey focus:bg-white hover:shadow-outline shadow" />

					<div id="list" class="hidden mt-1 absolute z-10 bg-white border-2 shadow-lg pb-4" style="width:500px;"></div>	

				</div>

			</div>
			</center>
		</div>
	</div>

	@endif
	@if(Auth::user()->team->logo_orient == 'bulky')
		<div class="inline-flex w-full pr-4 items-center">
			<div class="flex-1 flex-initial FadeInUp">
				<a href="/admin/team_col/logo_orient/landscape">
					@if(strpos(Auth::user()->team->logo_img, "://") !== false)

						<img class="" style="max-height:200px;" src="{{ Auth::user()->team->logo_img }}" />

					@elseif(strpos(Auth::user()->team->logo_img, "/images/logos") !== false)

						<img class="" style="max-height:200px;" src="http://{{ Request::getHost() }}{{ Auth::user()->team->logo_img }}" />
					
					@else

						<img style="max-height:200px;" src="/office/show_logo/{{ Auth::user()->team->id }}" />

					@endif
			</a>
			</div>
			<div class="flex-1 ml-6 w-4/5">
			
				<div class="dropdown my-6">

				<input id="cfbar" type="text" placeholder="Start Here" style="font-family:Arial, Font Awesome\ 5 Free" data-toggle="dropdown" autocomplete="off" class="w-full text-black appearance-none rounded-full px-6 py-3 bg-grey-lightest border-2 border-grey focus:bg-white hover:shadow-outline shadow" />

					<div id="list" class="hidden mt-1 absolute z-10 bg-white border-2 shadow-lg pb-4" style="width:500px;"></div>	

				</div>

			</div>
		</div>
	@endif



	<div class="md:flex pb-8 text-grey-dark">

		
		<div class="w-3/5 mr-12 px-8">

			@if(isset($notice) && ($notice))
				<div class="mt-8">

					@include('elements.one-notice', ['notice' => $notice])

				</div>
			@endif
			

            <div class="mt-8 mb-8">
	

		    @if($cases_total >0)
				@if ($case_type == 'user')
					<a href="/office/cases/mine/open" class="hover:text-blue-darker float-right text-sm text-blue rounded-full px-2 py-2">
			        	See All {{ $cases_total }}... 
			        </a>
				@elseif ($case_type == 'team')
					<a href="/office/cases/all/open" class="hover:text-blue-darker float-right text-sm text-blue rounded-full px-2 py-2">
			        	See All {{ $cases_total }}... 
			        </a>
				@endif
        	

				<div class="text-xl font-sans text-black mb-2 font-bold border-b-4 border-blue pb-2">
					<!-- <i class="fa fa-folder-open"></i>&nbsp;  -->
					@if ($case_type == 'user')
						@lang("My Open Cases")
					@elseif ($case_type == 'team')
						@lang("Our Open Cases")
					@endif
				</div>

			@endif

				<table class="w-full">

					@foreach($open_cases as $oc)
					
						<tr class="group align-top text-sm text-grey-dark hover:text-black py-2 w-full border-b">
			                <td class="w-24 pr-2 pt-2 text-sm">
			                	{{ $oc->date->format('n/j/y') }}<br>
			                	
			                </td>
			                <!-- <td class="w-6 py-2 px-1">
			                	<i class="fa fa-folder"></i>
					        </td> -->
					        <td class="w-full pl-2 py-2">
					        	<a class="text-grey-darker hover:text-black" href="/office/cases/{{ $oc->id }}">
					        		<div class="w-full">
							        	<div class="float-right text-grey text-xs whitespace-no-wrap">
					                		{{ $oc->date->diffForHumans() }}
							        	</div>
							        	<span class="font-bold"><i class="far fa-folder-open mr-2"></i>{{ $oc->subject }}</span>
							        	<div class="text-xs text-grey">
								        	{{ $oc->people->implode('full_name', ', ') }}
					                	</div>
					                </div>
				                </a>
					        </td>
				        </tr>

			        @endforeach

			    </table>

		    </div>

		    <div class="mt-8 mb-8">

			@if($contacts_total >0)
        		<a href="/office/contacts" class="hover:text-blue-darker float-right text-sm text-blue rounded-full px-2 py-2">
		        	See All {{ $contacts_total }}... 
		        </a>
		    @endif
				<div class="text-xl font-sans text-black mb-2 font-bold border-b-4 border-blue pb-2">
					<!-- <i class="fa fa-folder-open"></i>&nbsp;  -->
					@lang('Recent Contacts')
				</div>

				<table class="w-full">

					@foreach($recent_contacts as $contact)
					
						<tr class="group align-top text-sm text-grey-dark hover:text-black py-2 w-full border-b">
			                <td class="w-24 pr-2 pt-2 text-sm">
			                	{{ $contact->date->format('n/j/y') }}<br>
			                </td>
					        <td class="w-full pl-2 py-2">
					        	<div class="w-full">

						        	<div class="float-right text-grey text-xs whitespace-no-wrap">
				                		{{ $contact->date->diffForHumans() }}
						        	</div>

									@if($contact->case_id)
										<a class="text-grey-darker hover:text-black" href="/office/cases/{{ $contact->case->id }}">
										<i class="far fa-folder-open mr-2"></i>
						        		<span class="font-bold">{{ $contact->case->subject }}</span>
						        		</a>
						        	@elseif($contact->people()->count() > 0)
						        		<i class="far fa-user mr-2 text-blue"></i>
						        		<span class="font-bold">
						        		@foreach($contact->people as $person)
						        			<a class="text-grey-darker hover:text-black" href="/office/constituents/{{ $person->id }}">{{ $person->full_name }}</a>{{ (!$loop->last) ? ', ' : '' }}
						        		@endforeach
						        		</span>
						        	@else
										<span class="font-bold">{{ $contact->subject }}</span>
						        	@endif

						        	<div class="text-sm text-grey-dark">

					        			<span class="italic">
							        	{{ substr($contact->notes, 0, 200) }}
							        	{!! (strlen($contact->notes) > 200) ? '&hellip;' : '' !!}
							        	</span>

				                	</div>
					            </div>
					        </td>
				        </tr>

			        @endforeach

			    </table>

		    </div>

		</div>

		<div class="w-2/5">
			<div id="calendar-wrapper" class="opacity-0 transition w-full mt-4" style="min-height: 350px;">
				<!-- style="min-height: 700px;" -->
				<div class="h-2"></div>

				<div id="calendar" style=""></div>

				@include('shared-features.calendar.events-by-date', ['date' => \Carbon\Carbon::today()])

				@include('shared-features.calendar.template')

			</div>
			
			<div class="px-4">
				<div class="pt-4 mt-8 text-xl font-sans text-black mb-2 font-bold border-b-4 border-blue pb-2">
					@lang('Contact History')
				</div>

			    <div id="graph" class="overflow-x-scroll">
			        <?php extract($graph_a, EXTR_OVERWRITE); ?>
			        @include('elements.graph_basic')
			    </div>
								    @if(false)
									<div class="text-sm text-grey-dark">
										@include('office.dashboard.chart')
									</div>
									@endif
			</div>
		</div>

	</div>


	<div class="flex w-full px-4">

		<div class="w-1/3">

			<div class="mt-6 text-xl font-sans text-black mb-2 border-b-4 border-blue pb-2">
				@lang('Pending') <b>@lang('Followups')</b>
				@if($followups_total > 0)
					<span class="text-sm text-grey-darker">({{ $followups_total }})</span>
				@endif
			</div>

			<table class="w-full">


			@if($followups->count() <= 0)

				<div class="text-sm text-grey p-2">
					None.
				</div>

			@else
			
				@foreach($followups as $followup)

					<tr class="group align-top text-sm text-grey-dark hover:text-black py-2 w-full">

				        <td class="w-full pr-4 py-2">
			
				        		<div class="w-full">
						        	
						        	<div class="float-left">
						        		@if($followup->case_id)
						        			<i class="far fa-folder-open mr-2"></i>
						        		@else
											<i class="far fa-user mr-2"></i>
										@endif
									</div>

									@if($followup->case_id)
										<a class="text-grey-darker hover:text-black" href="/office/cases/{{ $followup->case->id }}">
						        		<span class="font-bold">{{ $followup->case->subject }}</span>
						        		</a>
						        	@else
						        		<span class="font-bold">
						        		@foreach($followup->people as $person)
						        			<a class="text-grey-darker hover:text-black" href="/office/constituents/{{ $person->id }}">{{ $person->full_name }}</a>{{ (!$loop->last) ? ', ' : '' }}
						        		@endforeach
						        		</span>
						        	@endif

						        	@if($followup->followup_on)
									<span class="text-blue font-semibold text-xs">
				                		scheduled: {{ \Carbon\Carbon::parse($contact->followup_on)->format("m-j-y") }}
						        	</span>
						        	@endif

						        	<div class="text-sm text-grey">
							        	{{ substr($followup->notes, 0, 40) }}
							        	@if (strlen($followup->notes) > 40)
											&hellip;
							        	@endif
				                	</div>
				                </div>
			                
				        </td>
			        </tr>

		        @endforeach

		    @endif
		    </table>

		</div>

		

		<div class="w-1/3">
			<div class="mt-6 text-xl font-sans text-black mb-2 border-b-4 border-blue pb-2">
				@lang('Overdue') <b>@lang('Followups')</b> 
				@if($followups_overdue_total > 0)
					<span class="text-sm text-grey-darker">({{ $followups_overdue_total }})</span>
				@endif
			</div>

			<table class="w-full">

			@if($followups_overdue->count() <= 0)

				<div class="text-sm text-grey p-2">
					Nothing overdue.<br />(Great job, {{ Auth::user()->name }}!)
				</div>

			@else

				@foreach($followups_overdue as $followup)
				
					<tr class="group align-top text-sm text-grey-dark hover:text-black py-2 w-full">

				        <td class="w-full pr-4 py-2">
				        	@if($followup->case_id)
				        		<a class="text-grey-darker hover:text-black" href="/office/cases/{{ $followup->id }}">
				        	@endif
				        		<div class="w-full">
						        	
						        	<div class="float-left">
						        		@if($followup->case_id)
						        			<i class="far fa-folder-open mr-2"></i>
						        		@else
											<i class="far fa-user mr-2"></i>
										@endif
									</div>

									@if($followup->case_id)
										<a class="text-grey-darker hover:text-black" href="/office/cases/{{ $followup->case->id }}">
						        		<span class="font-bold">{{ $followup->case->subject }}</span>
						        		</a>
						        	@else
						        		<span class="font-bold">
						        		@foreach($followup->people as $person)
						        			<a class="text-grey-darker hover:text-black" href="/office/constituents/{{ $person->id }}">{{ $person->full_name }}</a>{{ (!$loop->last) ? ', ' : '' }}
						        		@endforeach
						        		</span>
						        	@endif



									<span class="text-red font-semibold text-xs">
				                		{{ \Carbon\Carbon::parse($followup->followup_on)->diffForHumans() }}
						        	</span>
						        	<div class="text-sm text-grey">
							        	{{ substr($followup->notes, 0, 40) }}
							        	@if (strlen($followup->notes) > 40)
											&hellip;
							        	@endif
				                	</div>
				                </div>
			                </a>
				        </td>
			        </tr>

		        @endforeach

		     @endif
		    </table>
		</div>

		<div class="w-1/3">
			<div class="mt-6 text-xl font-sans text-black mb-2 border-b-4 border-blue pb-2">
				@lang('New') <b>@lang('Constituents')</b>
				@if($people_month_total > 0)
					<span class="text-sm text-grey-darker">(+{{ $people_month_total }} last 30 days)</span>
				@endif
			</div>

			<table class="w-full">

				@foreach($people_recent as $person)

					<tr class="group align-top text-sm text-grey-dark hover:text-black py-2 w-full">

		                	

				        <td class="w-full pr-4 py-2">
				        	<a class="text-grey-darker hover:text-black" href="/office/constituents/{{ $person->id }}">
				        		<div class="w-full">
						        	
						        	<div class="float-left">
										<i class="far fa-user mr-2"></i>
									</div>

						        	<span class="font-bold">{{ $person->full_name }}</span>
						        	<span class="text-blue font-semibold text-xs">
				                		{{ $person->created_at->diffForHumans() }}
						        	</span>
						        	<span class="text-sm text-grey">
						        		<span class="font-bold uppercase">
						        			<!-- {{ $person->street }} -->
						        		</span>
						        		<br>
							        	{{ $person->full_address }}
				                	</span>
				                </div>
			                </a>
				        </td>
			        </tr>

		        @endforeach

		    </table>

		</div>

	</div>



	<div class="flex px-4">
		<div class="w-full my-12">
			<div class="flex mt-8 border-b-4 border-blue pb-2">

				<div class="w-1/6">
					<div class="text-xl font-sans text-black font-bold">
						Activity Map
					</div>
				</div>
				<div class="w-1/2 mt-1 text-grey-dark text-base">
					<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/red-dot.png" />
					Open Case
					
					<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/green-dot.png" />
					Resolved Case
					<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/blue-dot.png" />
					Contact
					<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/yellow-dot.png" />
					Group
  				</div>

				<div class="w-1/3 text-sm text-grey-dark">

					<div id="timeframe-year" class="map-timeframe cursor-pointer px-2 py-1 float-right ml-2">
						12 Months
					</div>
					<div id="timeframe-month" class="map-timeframe cursor-pointer bg-blue text-white px-2 py-1 float-right ml-2">
						30 Days
					</div>
					<div id="timeframe-week" class="map-timeframe cursor-pointer px-2 py-1 float-right ml-2">
						7 Days
					</div>

				</div>
			</div>

			<div id="map" class="w-full border-2 border-t-0" style="height: 400px;"></div>



		</div>
	</div>

	<div class="flex w-full px-4 pb-8">	

		@foreach($categories as $thecat)

			<div class="w-1/3">
				<div class="mt-6 text-xl font-sans text-black mb-2 border-b-4 border-blue pb-2 font-bold">

					{{ $thecat->name }} {{ (($thecat->groups()->count() > 0)) ? "(".$thecat->groups()->count().")" : '' }}
				</div>

				<div class="pr-8">

					<table class="w-full">

						@foreach($thecat->groups()->take(10)->get() as $group)

							<tr class="group align-top text-sm text-grey-dark hover:text-black hover:bg-orange-lightest py-2 w-full">
				                	
						        <td class="w-full pr-4 py-2">
						        	<a class="text-grey-dark hover:text-black" href="/office/groups/{{ $group->id }}">
						        		<div class="">
							        		<div class="float-right">
							        			{{ $group->people()->count() }}
							        		</div>
							        		{{ $group->name }}
							        	</div>
					                </a>
						        </td>
					        </tr>

				        @endforeach

				    </table>
				    <hr>
				    <a class="text-sm float-right" href="/office/groups#{{ $thecat->id }}">See All...</a>
				</div>

			</div>
		@endforeach

	</div>




	@if (request('logtime')) 
		<table class="table">
			@foreach ($logtime['log'] as $note => $length)
				<tr>
					<td>{{ $note }}</td>
					<td><b>{{ $length }}</b></td>
					<td>
						<div class="h-4 bg-blue" style="width: {{ $length/$logtime['max'] * 500 }}px;"></div>
					</td>
				</tr>
			@endforeach
			<tr>
				<td class="text-blue font-bold">TOTAL</td>
				<td class="text-blue font-bold">{{ $logtime['total'] }}</td>
				<td></td>
			</tr>
		</table>
	@endif

	

@endsection

@section('javascript')

<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyC-IZEP4hINlLnKuxlLHaOEy6C5pgt8tlc">
    </script>

    @include('shared-features.calendar.javascript')

	<script type="text/javascript">
		$(document).ready(function() {
			

			// ==================================================> DASHBOARD MAP
			var map;
			var json = "/office/dashboard/activity-map";
			var infowindow = new google.maps.InfoWindow();

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
				        details += "Contacts: " + data.contacts + "<br>";
				        if (data.groups) {
					        details += "<b>Groups:</b><br> " + data.groups;
					    }
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

			google.maps.event.addDomListener(document.getElementById('timeframe-week'), 'click', function() {
				initializeMap("/office/dashboard/activity-map?timeframe=7", map);
				$('.map-timeframe').removeClass('bg-blue text-white');
				$('#timeframe-week').addClass('bg-blue text-white');
			});
			google.maps.event.addDomListener(document.getElementById('timeframe-month'), 'click', function() {
				initializeMap("/office/dashboard/activity-map?timeframe=30");
				$('.map-timeframe').removeClass('bg-blue text-white');
				$('#timeframe-month').addClass('bg-blue text-white');
			});
			google.maps.event.addDomListener(document.getElementById('timeframe-year'), 'click', function() {
				initializeMap("/office/dashboard/activity-map?timeframe=365");
				$('.map-timeframe').removeClass('bg-blue text-white');
				$('#timeframe-year').addClass('bg-blue text-white');
			});

		});
	</script>
	
<script type="text/javascript">

	$(document).ready(function() {
		$("#cfbar").focus();

		$("#cfbar").focusout(function(){
			window.setTimeout(function() {$('#list').addClass('hidden'); }, 300);
		});
		
		$("#cfbar").keyup(function(){
			getSearchData(this.value);
		});

	});

	function getSearchData(v) {
		if (v == '') {
			$('#list').addClass('hidden');
		}
		$.get('/office/dashboard_search/'+v, function(response) {
			if (response == '') {
				$('#list').addClass('hidden');
			} else {
				$('#list').html(response);
				$('#list').removeClass('hidden');
			}
		});
	}
	
</script>

@endsection
