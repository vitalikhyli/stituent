@if(session('errors'))
    <script type="text/javascript">
        $( document ).ready(function() {
            $('#add-web-form').modal('show');
        });
    </script>
@endif


<div id="add-web-form" class="modal fade" role="dialog">

	<form id="add-web-form" action="/campaign/web-forms/" method="POST">

     	@csrf

		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content">

				<div class="modal-header {{ (session('errors')) ? 'bg-red' : 'bg-blue-dark' }} text-white">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title"><i class="fas fa-window-restore mr-2"></i> Add a Web Form</h4>
				</div>

				<!-- Modal body-->

				@if(session('errors'))
					<div class="p-2">
						@foreach(session('errors')->messages() as $field => $message)
							<div class="text-red">{{ $message[0] }}</div>
						@endforeach
					</div>
				@endif
					
				<div class="p-1">

					<div class="flex -my-1">
						<div class="w-1/4 text-sm uppercase text-right p-2 pt-4">
							Web Form Name
						</div>
						<div class="p-2 w-3/4">
							<input required name="name" id="name" type="text" class="w-full border rounded-lg p-2" placeholder="My Campaign" value="{{ old('name') }}" />
						</div>
					</div>
				</div>

	


				<!-- Modal footer -->

				<div class="modal-footer bg-grey-light text-white">

					<button type="button" class="btn btn-default" data-dismiss="modal">
						Close
					</button>

					<button type="submit" class="btn btn-primary">
						Save
					</button>

				</div>
			</div>
		</div>

	</form>
	
</div>
