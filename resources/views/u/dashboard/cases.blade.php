
		    @if($cases_total >0)
				@if ($case_type == 'user')
					<a href="/u/cases/mine/open" class="hover:text-blue-darker float-right text-sm text-blue rounded-full px-2 py-2">
			        	See All {{ $cases_total }}... 
			        </a>
				@elseif ($case_type == 'team')
					<a href="/u/cases/all/open" class="hover:text-blue-darker float-right text-sm text-blue rounded-full px-2 py-2">
			        	See All {{ $cases_total }}... 
			        </a>
				@endif
        	@endif

				<div class="text-xl font-sans text-black mb-2 font-bold border-b-4 border-blue pb-2">
					<!-- <i class="fa fa-folder-open"></i>&nbsp;  -->
					@if ($case_type == 'user')
						@lang("My Open Cases")
					@elseif ($case_type == 'team')
						@lang("Our Open Cases")
					@endif
				</div>

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
					        	<a class="text-grey-darker hover:text-black" href="/u/cases/{{ $oc->id }}">
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