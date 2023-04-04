@if($organizations->first())
	<a href="/u/entities" class="hover:text-blue-darker float-right text-sm text-blue rounded-full px-2 py-2">
		See All {{ $organizations_count }}... 
	</a>
@endif

<div class="text-xl font-sans text-black mb-2 font-bold border-b-4 border-blue pb-2">
	<!-- <i class="fa fa-folder-open"></i>&nbsp;  -->
	@lang('Top') @lang('Organizations')
</div>

<table class="w-full">

	@foreach($organizations as $organization)
	
		<tr class="group align-top text-sm text-grey-dark hover:text-black py-2 w-full border-b">
            <td class="w-24 pr-2 pt-2 text-sm">
            	{{ $organization->created_at->format('n/j/y') }}<br>
            	
            </td>
            <!-- <td class="w-6 py-2 px-1">
            	<i class="fa fa-folder"></i>
	        </td> -->
	        <td class="w-full pl-2 py-2">
	        	<a class="text-grey-darker hover:text-black" href="/u/entities/{{ $organization->id }}">
	        		<div class="w-full">
			        	<div class="float-right text-grey text-xs whitespace-no-wrap">
	                		{{ $organization->created_at->diffForHumans() }}
			        	</div>
			        	<span class="font-bold text-blue">
			        		<i class="far fa-building mr-2"></i>{{ $organization->name }}
			        	</span>
			        	<div class="text-sm text-grey-darker flex">

			        		@if($organization->partnerships()->exists())
			        			<div class="border-r px-2">{{ $organization->partnerships()->count() }} Partnerships</div>
			        		@endif

			        		@if($organization->notes()->exists())
			        			<div class="border-r px-2">{{ $organization->notes()->count() }} Contacts</div>
			        		@endif

		        			@if($organization->people()->exists())
			        			<div class="border-r px-2">{{ $organization->people()->count() }} Linked People</div>
			        		@endif

	                	</div>
	                </div>
                </a>
	        </td>
        </tr>

    @endforeach

</table>