<!-- <div id="call_log_search_list" class="text-sm"> -->
	@foreach($people as $theperson)
		<div class="border-b border-dashed p-2 flex">			

			<button type="button" class="connect_suggested_in_modal bg-blue hover:bg-blue-dark rounded-lg px-2 py-1 text-white mr-2" data-person_address="{{ $theperson->full_address }}" data-person_name="{{ $theperson->full_name }}" data-person_id="{{ $theperson->id }}" >
				<i class="fas fa-plus-circle"></i>
			</button>

			<div class="font-bold w-48">
				{{ $theperson->full_name}}
			</div>

			<div class="ml-4">
				{{ $theperson->full_address }}
			</div>
		</div>
	@endforeach
<!-- </div> -->

