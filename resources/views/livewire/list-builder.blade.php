@push('styles')

	<style>
		select.select2 {
			opacity:0;
			height: 30px;
		}
		.select2-container--default.select2-container--focus .select2-selection--multiple {
		    border: solid gray 1px;
		    outline: 0;
		}
		.select2-container--default .select2-selection--multiple .select2-selection__choice {
		    background-color: #5897fb;
		    border: 0;
		    float: left;
		    margin-right: 5px;
		    margin-top: 5px;
		    padding: 0 5px;
		    color: white;
		}
		.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
		    color: #eee;
		    cursor: pointer;
		    display: inline-block;
		    font-weight: bold;
		    margin-right: 2px;
		}
	</style>

@endpush
<div>

	@if(request('debug'))
		<div class="text-xs">
			{{ print_r($input) }}
		</div>
	@endif
	

    <div class="text-xl font-bold border-b-4 pb-2">
		@if(isset($edit) && $edit)
			Edit List: <span class="text-blue">{{ $list_name }}</span>

			@if(!$ask_delete)

				<button wire:click="setAskDelete(true)" class="rounded-lg bg-red text-white px-4 py-1 float-right font-normal text-base">
					Delete
				</button>

			@else

				<div class="border-red border-4 rounded-lg shadow relative bg-white z-10 float-right px-8 py-8 text-white font-normal">

					<div class="font-bold text-xl text-black">
						Are you sure you want to delete this list?
					</div>

					<a href="/{{ Auth::user()->team->app_type }}/lists/{{ $this->list_id }}/delete">
						<button wire:click="askDelete()" class="rounded-lg bg-red text-white px-4 py-1 font-normal text-base">
							Yes
						</button>
					</a>

					<button wire:click="setAskDelete(false)" class="rounded-lg bg-grey text-white px-4 py-1 font-normal text-base">
						Cancel
					</button>

				</div>

			@endif

		@else
			New Campaign List 
			<!-- <span class="text-blue">*</span> -->
		@endif
	</div>
		
	@isset($edit)

	@else
		<div class="flex text-grey-dark items-center">

			<div class="w-1/2 pl-6 pt-6">

				<div>
					<span class="text-blue font-bold">*</span> Every <b class="text-black">Campaign List</b> starts with your entire district, and you use the options below to narrow it down to the specific list of constituents you want to work with. 
					<br><br>These are <b>dynamic lists</b>, meaning that if the voter data changes, these lists will be always be automatically updated with the latest data.
				</div>
				
			</div>

			<div class="w-1/6 text-center">

			
			</div>

			<div class="w-1/3 text-center">

				<!-- <div class="pt-6">
					Current Count:
				</div> -->
				<div id="list-count" wire:loading.class="opacity-25" class="text-blue text-5xl font-bold">
					{{ number_format($current_count) }} 
				</div>
				<div class="text-xl uppercase -mt-2 font-bold">Voters</div>

			</div>
		</div>
	@endif

	<div id="voter-count" class="">

		<div class="m-8 bg-white px-8 py-4 text-center">
			<div id="list-count" wire:loading.class="opacity-25" class="-mt-2 text-blue text-5xl font-bold">
				{{ number_format($current_count) }} 
			</div>
			<div class="text-xl uppercase -mt-2 font-bold text-grey-dark">Voters</div>
		</div>

	</div>

	<div class="text-grey-darker">

		@php
			$section_num = 1
		@endphp

		<div class="px-8 pb-8">

			


			<div class="w-full border-b-2 mt-8 flex">
				<div class="w-1/4 uppercase text-lg font-bold">
					<span class="text-grey text-base pr-1">{{ $section_num++ }}.</span> LOCATION
				</div>
				<div class="w-1/2 text-sm mt-1 text-grey-darker">
					Districts, Towns, Neighborhoods, Streets
				</div>
				<div class="w-1/4"></div>
			</div>


			@include('livewire.list-builder-location')


			<div class="w-full border-b-2 mt-8 flex">
				<div class="w-1/4 uppercase text-lg font-bold">
					<span class="text-grey text-base pr-1">{{ $section_num++ }}.</span> DEMOGRAPHICS
				</div>
				<div class="w-1/2 text-sm mt-1 text-grey-darker">
					Age, Gender, Party
				</div>
				<div class="w-1/4"></div>
			</div>

			@include('livewire.list-builder-demographics')


			<div class="w-full border-b-2 mt-8 flex">
				<div class="w-1/4 uppercase text-lg font-bold">
					<span class="text-grey text-base pr-1">{{ $section_num++ }}.</span> VOTING HISTORY
				</div>
				<div class="w-1/2 text-sm mt-1 text-grey-darker">
					Regular voters, Super voters, New voters, Local elections

				</div>
				<div class="w-1/4"></div>
			</div>

			@include('livewire.list-builder-voting')



			<div class="w-full border-b-2 mt-8 flex">
				<div class="w-1/4 uppercase text-lg font-bold">
					<span class="text-grey text-base pr-1">{{ $section_num++ }}.</span> CAMPAIGN DATA
				</div>
				<div class="w-1/2 text-sm mt-1 text-grey-darker">
					Support level, Tags, Volunteers, Past campaigns
				</div>
				<div class="w-1/4"></div>
			</div>

			@include('livewire.list-builder-campaign')

			<div class="w-full border-b-2 mt-8 flex">
				<div class="w-1/4 uppercase text-lg font-bold">
					<span class="text-grey text-base pr-1">{{ $section_num++ }}.</span> IMPORTS
				</div>
				<div class="w-1/2 text-sm mt-1 text-grey-darker">
					Build queries from Custom Imports by your team
				</div>
				<div class="w-1/4"></div>
			</div>

			@include('livewire.list-builder-imports')


			@if(Auth::user()->team->access_office || true)

				<div class="w-full border-b-2 mt-8 flex">
					<div class="w-1/4 uppercase text-lg font-bold">
						<span class="text-grey text-base pr-1">{{ $section_num++ }}.</span> OFFICE
					</div>
					<div class="w-1/2 text-sm mt-1 text-grey-darker">
						Groups, Cases, Contacts
					</div>
					<div class="w-1/4 text-right">
					</div>
				</div>

				@include('livewire.list-builder-office')

			@else

				<div class="opacity-50">

					<div class="w-full border-b-2 mt-8 flex">
						<div class="w-1/4 uppercase text-lg font-bold">
							<span class="text-grey text-base pr-1">{{ $section_num++ }}.</span> OFFICE
						</div>
						<div class="w-1/2 text-sm mt-1 text-grey-darker">
							Groups, Cases, Contacts
						</div>
						<div class="w-1/4 text-right">
						</div>
					</div>

					<div class="py-2 text-grey-dark">
						Access Not Enabled 
					</div>

				</div>

			@endif

			<div class="w-full border-b-2 mt-8 flex">
				<div class="w-1/4 uppercase text-xl font-bold text-orange">
					<span class="text-grey text-base pr-1">{{ $section_num++ }}.</span> CF+
				</div>
				<div class="w-1/2 text-sm mt-1 text-grey-darker">
					Additional Data your Campaign has Purchased
				</div>
				<div class="w-1/4"></div>
			</div>


			@include('livewire.list-builder-cf-plus')


		</div>
	</div>


	<div class="border-b-2 mx-8 mt-8 mb-4 flex">
		<div class="w-1/4 uppercase text-lg font-bold">
			<span class="text-grey text-base pr-1">{{ $section_num++ }}.</span> SAVE
		</div>
	</div>
		


	<div class="flex items-center pb-8">

		<div class="w-1/3 text-right pr-4">

			List Name:
			
		</div>
		<div class="w-1/3 text-left">
			
			<input class="border-2 px-4 py-2 bg-grey-lighter" type="text" wire:model.debounce.1000ms="list_name" placeholder="List Name" />

		</div>
		<div class="w-1/3 text-center">

			
			
		
			<button class="w-2/3 mx-auto rounded-full text-white 
							@if (!$list_name)
								hidden
							@endif
							bg-blue px-4 py-3 uppercase tracking-wide hover:bg-blue-dark" wire:click="save">
				@if ($this->list_id)
					Update
				@else
					Save New
				@endif
			</button>

			<button class="w-2/3 mx-auto rounded-full 
							@if ($list_name)
								hidden
							@endif
							text-white bg-grey-dark px-4 py-3 uppercase tracking-wide">
				Add Name to Save
			</button>

		</div>
	</div>

	@if (request('debug'))
		<div class="text-sm text-grey-dark p-6">
			{{ $debug }}
		</div>
	@endif

	@if ($show_preview)
				
		<button wire:click="toggleShowPreview" class="text-sm">
			<i class="fa fa-times text-red-light"></i> Hide Preview
		</button>

	@else

	<div class="w-full text-center">
		<button wire:click="toggleShowPreview" class="rounded-full border text-lg px-6 py-4">
			Show Preview
		</button>
	</div>
	@endif

	@if ($show_preview)



		<div class="p-8 border-t-4">

			
			<div class="flex">
				<div class="w-1/4 text-right">			

					<select wire:model="preview_per_page" class="form-control w-32">
						<option value="50">Show first 50</option>
						<option value="100">Show first 100</option>
						<option value="500">Show first 500</option>
					</select>
				</div>

				<div class="w-1/2 text-center">

				</div>

				<div class="w-1/4 text-right">
					
				</div>
			</div>


			<div class="transition pt-4" wire:loading.class="opacity-50">
				<table class="table text-sm text-grey-dark">
					<tr>
						<th>
							
						</th>
						<th class="uppercase font-bold">
							<span class="text-blue font-bold">
								{{ number_format($current_count) }} 
							</span> Voters
						</th>
						<th>Name</th>
						<th>Address</th>
						<th>City</th>
						<th>Age</th>
						<th>Gender</th>
						<th>Party</th>
						<th class="text-right">Ward</th>
						<th class="text-right">Precinct</th>
						@if($input['support'])
							<th>Support</th>
						@endif
						@if($input['tags'])
							<th>Tags</th>
						@endif
					</tr>

					@if ($current_count > 0)
						@foreach ($preview_voters as $voter)
							<tr>
								<td class="text-grey">{{ $loop->iteration }}.</td>
								<td>{{ $voter->id }}</td>
								<td>{{ $voter->name }}</td>
								<td>{{ $voter->address_line_street }}</td>
								<td>{{ $voter->address_city_zip }}</td>
								<td>{{ $voter->age }}</td>
								<td>{{ $voter->gender }}</td>
								<td>{{ $voter->party }}</td>
								<td class="text-right">{{ $voter->ward }}</td>
								<td class="text-right">{{ $voter->precinct }}</td>
								@if($input['support'])
									<td>
										@if ($support = $voter->support)
										<span class="px-2 py-1 {{ getSupportClass($support) }} text-white rounded-full">
											{{ $support }}
										</span>
										
										@endif
									</td>
								@endif
								@if($input['tags'] || $input['all_tags'])
									<td>
										@php
											$participant = getParticipant($voter);
										@endphp
										@if ($participant)
											<div class="text-xs uppercase whitespace-no-wrap text-blue-dark">
												@foreach ($participant->tags as $tag)
													
													<i class="fa fa-tag"></i>
													{{ $tag->name }}<br>
													
												@endforeach
											</div>
										@endif
									</td>
								@endif
								@if ($input['cf_plus']['cell_phones'] 
										|| $input['cf_plus']['ethnicities'])
									<td class="text-orange font-bold whitespace-no-wrap">
							
										{!! $voter->cf_plus_data !!}
									</td>
								@endif
							</tr>
						@endforeach
					@endif
				</table>
			</div>

		
		@endif
	</div>



</div>

@push('scripts')


<script>
    $(document).ready(function() {
    	// alert();
        $('.select2').addClass('w-full');
        $('.select2').show();
        $('.select2').select2();
        
        
        $('.select2').on('change', function (e) {
            var field = $(this).attr('wire-select-model');
            console.log(field);
            console.log($(this).val());
            if (field === undefined) {
            	return;
            }
	        @this.set(field, $(this).val());
        });

        // var voter_count = document.getElementById('voter-count');
        // document.addEventListener('mousemove', function(e) {
        // 	var x = e.clientX;
        // 	var y = e.clientY;
        // 	voter_count.style.left = (x+100) + "px";
        // 	voter_count.style.top  = (y-200) + "px";
        // });
    });
</script>
@endpush
