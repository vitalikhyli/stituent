<div class="modal fade" 
     id="edit-relationship-modal" 
     tabindex="-1" 
     role="dialog" 
     aria-labelledby="edit-relationship-modal" 
     aria-hidden="true">

	<div class="modal-dialog">

		@if (Auth::user()->team->app_type == 'office')
			<form method="post" action="/{{ Auth::user()->team->app_type }}/organizations/{{ $entity->id }}/update_person">
		@else
			<form method="post" action="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/update_person">
		@endif
		

		@csrf

		<div class="modal-content">

			<div class="modal-header bg-red text-white">

				<span id="edit_relationship_modal_full_name"></span>

			  	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  		<span aria-hidden="true">&times;</span>
			  	</button>
			</div>

			<div class="modal-body">

				<div class="py-2 font-medium">Type of Relationship:</div>

				<input type="text" id="edit_relationship_modal_type" name="relationship_type" placeholder="eg. President, Employee, etc." class="rounded-lg border p-2 w-full" />

				<input type="text" id="edit_relationship_modal_person_id" name="relationship_person_id" class="hidden" />

			</div>

			<div class="modal-footer">



				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Cancel
				</button>

				<button type="submit" class="btn btn-primary bg-blue">
			  		Save
				</button>

				@if (Auth::user()->team->app_type == 'office')
				<button formaction="/{{ Auth::user()->team->app_type }}/organizations/{{ $entity->id }}/delete_person" type="submit" class="btn btn-secondary float-left bg-red text-white">
					Delete
				</button>
				@else
					<button formaction="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/delete_person" type="submit" class="btn btn-secondary float-left bg-red text-white">
					Delete
				</button>
				@endif

			</div>

		</div>

		</form>

	</div>

</div>