<?php if (!defined('dir')) define('dir','/u'); ?>

<div class="flex w-full">


	<div class="pr-4 w-2/3">

	@if($cases instanceof \Illuminate\Pagination\LengthAwarePaginator )
		<div class="text-right w-full text-grey-dark text-sm">
			Showing {{ $cases->firstItem() }} - {{ $cases->lastItem() }} of {{ $cases->total() }}
		</div>
		<div class="text-left">
			<nav aria-label="Page navigation example">
				{{ $cases->links() }} 
			</nav>
		</div>
	@endif


		<table class="w-full">



		@foreach($cases as $oc)
		
			<tr class="{{ ($oc->resolved) ? 'opacity-50' : '' }} align-top text-sm text-grey-dark hover:bg-blue-lightest w-full {{ (!$loop->last) ? 'border-b-4 border-grey-light' : '' }}">
	            <td class="whitespace-no-wrap p-2 text-base">
	            	<div class="font-bold text-black text-sm text-left w-16">
	            		@if($oc->date->format('Y') != \Carbon\Carbon::now()->year)
	            			{{ $oc->date->format('M j, Y') }}
	            		@else
	            			{{ $oc->date->format('M j') }}
	            		@endif
	            	</div>
	            </td>
		        <td class="w-full p-2">
		        	<a class="text-grey-darker hover:text-black" href="{{dir}}/cases/{{ $oc->id }}">
		        		<div class="w-full">
				        	<div class="float-right text-grey text-sm whitespace-no-wrap">
		                		{{ $oc->date->diffForHumans() }}
				        	</div>
				        	<span class="font-bold text-base">
				        		@if($oc->resolved)
				        			<span class="text-grey-darkest line-through">
				        			<i class="fas fa-check-circle mr-2"></i> Resolved - 
				        			{{ $oc->subject }}</span>
				        		@else
				        			<span class="text-grey-darkest"><i class="fa fa-folder-open mr-2"></i>{{ $oc->subject }}</span>
				        		@endif
				        		
				        	</span>
				        	<div class="ml-6 text-sm text-grey-darker mt-1">
					        	{{ $oc->people->implode('full_name', ', ') }}
		                	</div>
		                	<div class="ml-6 text-sm text-grey-dark">
		                		{{ $oc->notes }}
		                	</div>

			<div class="flex-1 w-full flex-initial pl-4 text-right text-grey-dark text-xs">
				@if($oc->contacts->count() > 0)
					<i class="text-center far fa-comments mr-2 text-base"></i> ({{ $oc->contacts->count() }})
				@endif
				@if($oc->files->count() >0)
					<i class="w-6 text-center far fa-file"></i>({{ $oc->files->count() }})
				@endif
			</div>
		                </div>
	                </a>
		        </td>
	        </tr>
	    @endforeach
		</table>
	</div>



	<div class="ml-8 w-1/3 pr-4">

		@if(isset($files) && ($files->count() >0 ))

			<div class="text-lg font-sans">
				Files
			</div>

			<div class="mb-4 pb-4 border-t mt-2">
			@foreach($files as $thefile)
				<a href="{{dir}}/download_file/{{ $thefile->id }}" target="_new" class="text-black">
				<div class="mb-1 w-full hover:bg-orange-lightest cursor-pointer border-b">
					<div class="flex w-full">
						<div class="flex-1 flex-initial p-1 w-8">
							<i class="text-center fa fa-file mr-4"></i>
						</div>
						<div class="flex-1 flex-grow p-1">
							 {!! str_ireplace($v, '<b class="font-bold">'.$v.'</b>',$thefile->name) !!}
						</div>
						<div class="flex-1 flex-initial p-1 w-1/5">
							{{ \Carbon\Carbon::parse($thefile->date)->format("n/j/y") }}
						</div>
					</div>
					<div class="ml-10 flex-initial w-full text-grey-dark text-sm pb-1">
						{{ $thefile->user->name }}
					</div>
				</a>
				</div>
	
			@endforeach
			</div>

		@endif

		<div class="text-lg font-sans">
			Stats
		</div>
		<table class="mt-2 border-t w-full text-sm">
			<tr class="border-b">
				<td class="p-2 bg-grey-lighter text-right w-4/5">
					Total
				</td>
				<td class="p-2 text-left w-1/5">
					{{ $cases_count }}
				</td>

			</tr>
		</table>

		@if($cases_count > 0)
		<div class="text-lg font-sans mt-4">
			By User
		</div>
		<table class="mt-2 border-t w-full text-sm">
			@foreach($cases->groupBy('user_id')->sortBy('id') as $usercases)
			<tr class="border-b">
				<td class="p-2 bg-grey-lighter text-right w-4/5">
					{{ \App\User::find($usercases->first()->user_id)->name }}
				</td>
				<td class="p-2 w-1/5">
					{{ $usercases->count() }}
				</td>
			</tr>
			@endforeach
		</table>

		<div class="text-lg font-sans mt-4">
			Oldest
		</div>
		<table class="mt-2 border-t w-full">
			@foreach($cases->sortByDesc('date')->splice(0,3) as $case)
			<tr class="border-b text-sm">
				<td class="py-2">
					<a href="{{dir}}/cases/{{ $case->id }}">
						@if($case->resolved)
						<div class="line-through">
						@else
						<div class="">
						@endif
							<div class="text-grey-dark">
							{{ \Carbon\Carbon::parse($case->created_at)->diffForHumans() }}
							</div>
							{{ $case->subject }}
						</div>
					</a>
				</td>
			</tr>
			@endforeach
		</table>
		@endif


	</div>


</div>