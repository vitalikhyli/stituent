@extends('campaign.base')

@section('title')
    New Participant
@endsection

@section('style')


@endsection

@section('main')

<div class="w-full">



	<div class="border-b-4 border-blue text-xl py-1">


		<span class="text-2xl font-bold">
			Create New Participant <span class="text-blue text-2xl font-bold">*</span> 
		</span>

	</div>


	<div class="flex text-grey-dark w-full">
		<div class="w-1/2">
			<div class="">
				

				<form action="/{{ Auth::user()->team->app_type }}/participants/" method="post">

					@csrf
					<div class="py-2 flex">

						<div class="py-2 mt-1 text-grey-darker">

							<input name="new_name" type="text" class="border p-2 rounded text-blue-dark" placeholder="New name" /> 

						</div>

						<div class="py-2">
							<button class="rounded-lg bg-blue text-white px-4 py-2 mt-1 ml-2">
								Add and Edit
							</button>
						</div>

					</div>


				</form>




			</div>
		</div>
		<div class="w-1/2">
			<div class="p-2">
				<span class="text-blue text-2xl font-bold">*</span> <span class="font-bold text-black">Create a Participant</span> who is not linked to a voter file record this way. When possible, first look up a voter and edit them, which will create a Participant and link to the voter record.
			</div>
		</div>
	</div>



</div>



@endsection


@section('javascript')
<script type="text/javascript">
	
	$(document).ready(function() {

        $(document).on('click', "#show-all-elections", function () {
        	$('#remainder-of-elections').toggleClass('hidden');
        	$('#show-all-elections-div').toggleClass('hidden');
		});

        $(document).on('click', "#show-fewer-elections", function () {
        	$('#remainder-of-elections').toggleClass('hidden');
        	$('#show-all-elections-div').toggleClass('hidden');
		});

	});

</script>
@endsection