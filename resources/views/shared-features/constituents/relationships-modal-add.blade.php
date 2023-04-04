<div class="modal fade" 
     id="add-relationship-modal" 
     tabindex="-1" 
     role="dialog" 
     aria-labelledby="add-relationship-modal" 
     aria-hidden="true">
	
	<div class="modal-dialog">

		@if (Auth::user()->team->app_type == 'office')
			<form method="post" action="/{{ Auth::user()->team->app_type }}/organizations/{{ $entity->id }}/link_person">
		@else
			<form method="post" action="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/link_person">
		@endif

		@csrf

		<div class="modal-content">

			<div class="modal-header bg-blue-dark text-white">

				Create a Relationship:

			 	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
			  		<span aria-hidden="true">&times;</span>
			 	</button>

			</div>

			<div class="modal-body">

				<div class="py-2 font-medium">
					Person Lookup:
				</div>

				<input type="text" id="relationship_person" name="relationship_person" placeholder="{{ Auth::user()->name }}" class="rounded-lg border p-2 w-full" />

				<input type="hidden" id="relationship_person_id" name="relationship_person_id" />

				<div class="hidden" id="modal-list">
					
				</div>

				<div class="py-2 font-medium">Type of Relationship:</div>
				<input type="text" id="relationship_type" name="relationship_type" placeholder="President, Employee, etc." class="rounded-lg border p-2 w-full" />

				<div class="py-2 font-medium">Often Used Titles:</div>

				@if($common_relationships->first())
					<div class="inline-flex flex-wrap">

						@foreach($common_relationships as $pivot)

							<div class="common_relationship_button rounded-lg bg-grey-lighter hover:bg-blue hover:text-white p-1 px-2 mr-1 uppercase text-xs cursor-pointer">
								{{ $pivot->relationship }}
							</div>

						@endforeach

					</div>
				@endif



			</div>

			<div class="modal-footer">

				<button type="button" class="btn btn-secondary" data-dismiss="modal">
					Cancel
				</button>

				<button type="submit" class="btn btn-primary bg-blue">
			  		Link Person
				</button>

			</div>
			
		</div>

		</form>

	</div>

</div>