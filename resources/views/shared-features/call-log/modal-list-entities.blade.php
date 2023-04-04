<div id="call_log_search_list_entities" class="text-sm">
	@foreach($entities as $entitity)
		<div class="border-b border-dashed p-2 flex">			
				<button type="button" class="connect_entity_in_modal bg-blue hover:bg-blue-dark rounded-lg px-2 py-1 text-white mr-2" data-entity_address="{{ $entitity->address }}" data-entity_name="{{ $entitity->name }}" data-entity_id="{{ $entitity->id }}" >
				<i class="fas fa-plus-circle"></i>
				</button>
			<div class="font-bold w-48">
			{{ $entitity->name}}
			</div>
			<div class="ml-4">
			{{ $entitity->address }}
			</div>
		</div>
	@endforeach
</div

