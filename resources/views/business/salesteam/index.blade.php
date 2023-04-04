@extends('business.base')

@section('title')
    @lang('Prospects')
@endsection

@section('breadcrumb')
    &nbsp;<b>@lang('Home')</b> >
    Prospects
@endsection

@section('style')

@endsection



@section('main')

<div class="text-2xl font-sans text-black border-b-4 border-blue pb-2 font-bold">
	Sales Teams
	
</div>

<div class="flex text-grey-darker mb-8">
	<div class="w-2/3">
		<div class="font-normal mr-2 pt-2">
			<span class="font-bold text-blue text-sm">Search</span>
			<input type="text" name="search" id="seach" class="border px-2 py-1 text-sm" placeholder="Search">
		</div>
	</div>
	<div class="w-1/3 border-l-2 pl-2 italic text-sm text-darkest">
		<div class="p-2">
			<span class="font-bold text-black">Sales Teams</span> are the users who can access each type of Prospect, so there can be the right overlap.
		</div>
	</div>
</div>



<form action="/{{ Auth::user()->team->app_type }}/salesteams/update" method="post">

	@csrf

<div class="flex-1 pb-8 text-grey-dark">		


	<div class="table">

		<div class="table-row text-xs w-full">

			<div class="table-cell p-1 bg-grey-lighter uppercase border-b w-8">
				
			</div>


			<div class="table-cell p-1 bg-grey-lighter uppercase border-b">
				Type
			</div>

			<div class="table-cell p-1 bg-grey-lighter uppercase border-b">
				# Prospects
			</div>

			<div class="table-cell p-1 bg-grey-lighter uppercase border-b">
				Users with Access
			</div>

		</div>


		@foreach($types as $type)
			<div class="table-row text-sm w-full mb-1">

				<div class="table-cell p-1 align-middle border-b">
					{{ $loop->iteration }}.
				</div>


				<div class="table-cell p-1 align-middle border-b w-1/4 truncate font-bold text-lg">
					{{ $type }}
				</div>

				<div class="table-cell p-1 align-middle border-b w-1/4 truncate">
					{{ $salesteams->where('type', $type)->count() }}
				</div>

				<div class="table-cell p-1 align-middle border-b text-xs text-blue">

						@foreach($salesteams->where('type', $type) as $member)

							<div>
								<input type="hidden" name="remove_{{ $member->user->id }}_{{ base64_encode($type) }}" value="1" /> 
								<label id="add_{{ $member->user->id }}_{{ base64_encode($type) }}" class="font-normal">
									<input type="checkbox" checked name="add_{{ $member->user->id }}_{{ base64_encode($type) }}" value="1" /> {{ $member->user->name }}
								</label>
							</div>

						@endforeach



						@foreach(Auth::user()->team->usersAll->whereNotIn('id', $salesteams->where('type', $type)->pluck('user_id')->toArray()) as $user)
							<div>
								<label id="add_{{ $user->id }}_{{ base64_encode($type) }}" class="font-normal">
									<input type="checkbox" name="add_{{ $user->id }}_{{ base64_encode($type) }}" value="1" /> {{ $user->name }}
								</label>
							</div>
						@endforeach



				</div>

			</div>
		@endforeach

	</div>


</div>

<button class="rounded-lg bg-blue text-white px-2 py-1">Save</button>

</form>





@endsection

@section('javascript')
	

@endsection
