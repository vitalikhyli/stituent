@extends(Auth::user()->team->app_type.'.base')

@section('title')
    @lang('Master Email List')
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	&nbsp;<b>Master Email List</b> 

@endsection

@section('style')


@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue">
	<div class="text-2xl font-sans w-full">
		 @lang('Master Email List')
	</div>

	

</div>

<div class="p-6">
		There are currently <b>{{ number_format(Auth::user()->team->people()->where('master_email_list', true)->count()) }} people</b> on your Master Email List, and <b>{{ $people_to_add->count() }} voters</b> with email addresses that are not on your Master List.
	</div>

<form action="/{{ Auth::user()->team->app_type }}/emails/master/update" method="post">

@csrf

	<div class="float-right px-4 pt-2">
		<input type="submit" class="bg-blue text-white px-4 py-2 rounded-lg w-full shadow hover:bg-blue-dark" value="Update Your Master Email List" />
	</div>

	<div class="flex w-full mt-2">

		<div class="w-full text-sm">

			@if($people_to_add->first())

				<div class="border-b-4 border-grey font-bold text-xl mt-4">
					Who would you like to add to your Master List?
				</div>

				<table class="w-full">
					@foreach($people_to_add as $person)
						<tr>
							<td class="align-top p-1 border-b w-10 text-grey text-sm text-right">{{ $loop->iteration }}.</td>

							<td class="border-b">

							<label for="person_{{ $person->id }}" class="font-normal flex w-3/4">

								<div class="w-4">
									<input type="checkbox" checked value="{{ $person->id }}" name="person_{{ $person->id }}" id="person_{{ $person->id }}" />
								</div>
							</label>
							</td>
							<td class="mx-2 align-top p-1 border-b">
								<label for="person_{{ $person->id }}" class="font-normal flex w-3/4">{{ $person->full_name }}
								</label>
							</td>
							<td class="p-1 align-top p-1 border-b text-grey-dark">{{ $person->full_address }}</td>
							<td class="p-1 align-top p-1 border-b">{{ $person->email }}</td>
								
							<td class="border-b text-blue">
								@if ($person->is_person)
									<i class="fa fa-link"></i> Linked
								@endif
							</td>
						</tr>

					@endforeach
				</table>

			@endif


			@if($people_already_on->first())

				<div class="border-b-4 border-grey font-bold text-xl mt-4">
					People <span class="text-blue">Already On List</span>:

					<div class="float-right text-xs text-grey-dark font-normal pt-2 italic">
						Unselect to remove
					</div>
				</div>
				<table class="w-full">

				@foreach($people_already_on as $person)
						<tr>
							<td class="align-top p-1 border-b w-10 text-grey text-sm text-right">{{ $loop->iteration }}.</td>

							<td class="align-top p-1 border-b">
								<input type="hidden" value="remove" name="person_{{ $person->id }}" id="person_{{ $person->id }}_remove" />

								<div class="w-4">
									<input type="checkbox" checked value="{{ $person->id }}" name="person_{{ $person->id }}" id="person_{{ $person->id }}" />
								</div>
		
							</td>
							<td class="mx-2 align-top p-1 border-b">
								<label for="person_{{ $person->id }}" class="font-normal flex w-3/4">{{ $person->full_name }}
								</label>
							</td>
							<td class="p-1 align-top p-1 border-b text-grey-dark">{{ $person->full_address }}</td>
							<td class="p-1 align-top p-1 border-b">{{ $person->email }}</td>
								
							<td class="border-b text-blue">
								@if ($person->is_person)
									<i class="fa fa-link"></i> Linked
								@endif
							</td>
						</tr>

					@endforeach

				
				</table>
			@endif


			@if($master_email_existing->first())

				<div class="border-b-4 border-grey font-bold text-xl mt-6">
					Others Already on List

					<div class="float-right text-xs text-grey-dark font-normal pt-2 italic">
						Unselect to remove
					</div>

				</div>

				@foreach($master_email_existing as $person)
					<div class="border-b py-1 flex w-full">
						<div class="w-10 text-grey text-sm text-right pr-2">{{ $loop->iteration }}.</div>

						<input type="hidden" value="remove" name="person_{{ $person->id }}" id="person_{{ $person->id }}_remove" />

						<label for="person_{{ $person->id }}" class="font-normal flex w-3/4">
							<div class="w-4">
								<input type="checkbox" checked value="{{ $person->id }}" name="person_{{ $person->id }}" id="person_{{ $person->id }}" />
							</div>
							<div class="mx-2 w-1/2">{{ $person->full_name }}</div>
							<div class="mx-2">{{ $person->email }}</div>
						</label>

					</div>
				@endforeach

			@endif

		</div>

		

		

	</div>
</form>

@endsection

@section('javascript')



<script type="text/javascript">


</script>
@endsection
