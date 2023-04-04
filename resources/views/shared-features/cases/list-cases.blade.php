<table class="w-full">

@foreach($cases as $oc)

	<tr class="clickable cursor-pointer {{ ($oc->resolved) ? 'opacity-50' : '' }} align-top text-sm text-grey-dark hover:bg-blue-lightest w-full {{ (!$loop->last) ? 'border-b border-grey-light' : '' }}" data-href="/{{ Auth::user()->team->app_type }}/cases/{{ $oc->id }}">


        <td class="whitespace-no-wrap py-1 pr-4 text-xs">
			@if(!$oc->resolved)
				@if($oc->priority == "High")
					<div class="text-center uppercase text-xs ml-2 bg-red text-white rounded px-2 py-1 whitespace-no-wrap">High</div>
				@elseif($oc->priority == "Medium")
					<div class="text-center uppercase text-xs ml-2 bg-blue-light text-white rounded px-2 py-1 whitespace-no-wrap">Medium</div>
				@elseif($oc->priority)
					<div class="text-center uppercase text-xs ml-2 bg-green-light text-white rounded px-2 py-1 whitespace-no-wrap">{{ $oc->priority }}</div>
				@endif
			@endif
		</td>

        <td class="whitespace-no-wrap pt-1 pr-4 text-base">

        	<div class="text-black text-sm text-left">

        		@if($oc->date->format('Y') != \Carbon\Carbon::now()->year)
        			{{ \Carbon\Carbon::parse($oc->date)->toDateString() }}
        		@else
        			{{ $oc->date->format('F j') }}
        		@endif

        	</div>

        </td>



        <td class="w-2/3 p-1">

        	<a class="text-grey-darker hover:text-black" href="/{{ Auth::user()->team->app_type }}/cases/{{ $oc->id }}">
		        	<span class="text-sm">
		        		@if($oc->resolved)

		        			<span class="text-grey-darkest">
		        			<i class="fas fa-check-circle mr-2 w-6"></i> 
		        			
		        			@if(isset($v))
		        				{!! preg_replace("/".preg_quote($v)."/i", '<b class="bg-orange-lighter">$0</b>', $oc->subject) !!}
		        			@else
								{{ $oc->subject }}
		        			@endif

		        			</span>

		        		@else
		        			<span class="text-grey-darkest"><i class="fa fa-folder-open mr-2 w-6"></i>

		        			@if(isset($v))
		        				{!! preg_replace("/".preg_quote($v)."/i", '<b class="bg-orange-lighter">$0</b>', $oc->subject) !!}
		        			@else
								{{ $oc->subject }}
		        			@endif

		        			</span>
		        		@endif
		        		
		        	</span>

					@if($oc->private)
						<span class="cursor-pointer text-blue ml-1 float-right">
							<i class="fa fa-lock"></i>
							<span class="text-xs">Private</span>
						</span>
					@endif

		        </a>

    			@if(isset($v) && ($v))
    				<div class="flex">
    					<i class="mr-2 w-6"></i> <!-- Spacer -->
    						<div class="ml-1">
		    					{!! preg_replace("/".preg_quote($v)."/i", '<b class="bg-orange-lighter">$0</b>', $oc->notes) !!}
		    				</div>
    				</div>
    			@endif

			</td>
			<td class="w-full p-1">

		        	<div class="ml-6 text-sm text-grey-darker mt-1">

		        			@if(isset($v) && ($v))

		        				<ul>
			        				@foreach($oc->people as $linked_person)
			        					<li>
			        						{!! preg_replace("/".preg_quote($v)."/i", '<b class="bg-orange-lighter">$0</b>', $linked_person->full_name) !!}
			        					</li>
			        				@endforeach
		        				</ul>

		        			@else
								{{ $oc->people->implode('full_name', ', ') }}

		        			@endif

			        	
                	</div>

                </td>

             <!--    <td class="w-full p-1">
                	<div class="ml-6 text-sm text-grey-dark">
                		{{ $oc->notes }}
                	</div>
                </td> -->
<!-- 	<div class="flex-1 w-full flex-initial pl-4 text-right text-grey-dark text-xs">
		@if($oc->contacts->count() > 0)
			<i class="text-center far fa-comments mr-2 text-base"></i> ({{ $oc->contacts->count() }})
		@endif
		@if($oc->files->count() >0)
			<i class="w-6 text-center far fa-file"></i>({{ $oc->files->count() }})
		@endif
	</div> -->
   
    </tr>
@endforeach
</table>
