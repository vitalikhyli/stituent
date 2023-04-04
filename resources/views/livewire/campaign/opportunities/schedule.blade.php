<div>

	<div class="border-b-4 border-blue-darker flex flex-wrap mb-4 mt-2">

		<div class="bg-white bg-grey-lighter text-lg mr-4 px-4 py-1 rounded-t-lg">
			Edit
		</div>

		<div class="bg-white bg-blue-darker text-white text-lg mr-4 px-4 py-1 rounded-t-lg">
			Invite Volunteers
		</div>

	</div>


	<table class="table-row">

    	<div class="table-row">

	    		<div class="table-cell p-1">

	    			<div class="p-1">
	    				&nbsp;
	    			</div>

	    		</div>

			@foreach(collect($opp->matrix)->first() as $location)

	    		<div class="table-cell p-1">

	    			<div class="border-2 p-1">
	    				{{ $location }}
	    			</div>

	    		</div>

	    	@endforeach

			<div class="table-cell p-1">

				<div class="border-2 p-1">
					+ Add New:
				</div>

			</div>

    	</div>

	    @foreach($opp->matrix as $y => $xs)

	    	<div class="table-row">

	    		<div class="table-cell p-1">

	    			<div class="border-b-4 p-1">
	    				{{ $time }}
	    			</div>

	    		</div>

				@foreach($xs as $x)

		    		<div class="table-cell p-1">

		    			<div class="border-2 p-1">
		    				

		    			</div>

		    		</div>

		    	@endforeach

	    	</div>

	    @endforeach

		<div class="table-row">

			<div class="table-cell p-1">

				<div class="border-b-4 p-1">
					+ Add New:
				</div>

			</div>

		</div>

	</table>

</div>
