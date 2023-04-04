@extends('business.base')

@section('title')
    @lang('Clients')
@endsection

@section('breadcrumb')
    &nbsp;<b>@lang('Home')</b> >
    Clients
@endsection

@section('style')

@endsection



@section('main')

<div class="text-2xl font-sans text-black border-b-4 border-blue pb-2 font-bold">
	Existing Clients




<!-- 	<div class="float-right font-normal">
		<form action="/{{ Auth::user()->team->app_type }}/prospects/" method="post">
			@csrf
			<input type="text" name="type" class="hidden" value="">
			<input type="text" name="name" class="border px-2 py-1 text-sm" placeholder="New Name">
			<button class="rounded-lg bg-blue text-sm text-white px-3 py-1">
				New Prospect
			</button>
		</form>
	</div> -->



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
			<span class="font-bold text-black">Clients</span> are just SalesEntities with boolean client set to true. They have a field cf_id which links to an Account.
		</div>
	</div>
</div>


<div class="flex-1 text-grey-dark">


	<div class="mt-4 text-xl font-sans text-black border-b-4 pb-1">


		@php
			$import_type = 'CF Political';
		@endphp

		@if($unlinked_accounts->first())
<!-- 
			<a href="/{{ Auth::user()->team->app_type }}/clients/import/{{ base64_encode($import_type) }}">
				<button class="rounded-lg bg-blue text-white px-3 py-1 float-right text-sm">
					Import All as {{ $import_type }}
				</button>
			</a>
 -->
		@endif

		Unlinked CF Accounts

		@if($unlinked_accounts->first())

			<span class="text-blue">({{ $unlinked_accounts->count() }})</span>

			<span id="unlinked" class="toggle_control uppercase text-xs rounded-lg p-1 bg-grey-lightest text-blue border cursor-pointer" data-id="unlinked">show</span>

		@endif

		<div class="text-grey-dark text-sm italic">
			Community Fluency Accounts that do not yet have corresponding SalesEntities in this Marketing system
		</div>


	</div>

	<div id="toggle_unlinked" class="{{ ($unlinked_accounts->first()) ? 'hidden' : '' }}">
		@if(!$unlinked_accounts->first())

			<div class="text-grey-dark text-sm italic">
				None found.
			</div>
		

		@else

			<div class="table w-full">
				<div class="table-row text-xs w-full mb-1 bg-grey-lightest uppercase">

					<div class="table-cell p-1 align-middle border-b">
						
					</div>

					<div class="table-cell p-1 align-middle border-b">
						Account
					</div>

					<div class="table-cell p-1 align-middle border-b">
						Teams
					</div>

					<div class="table-cell p-1 align-middle border-b">
						Users
					</div>

				</div>

				@foreach($unlinked_accounts as $account)

					<div class="table-row text-sm w-full mb-1 text-grey-darker">

						<div class="table-cell p-1 align-middle border-b">
							{{ $loop->iteration }}.
						</div>

						<div class="table-cell p-1 align-middle border-b">
							{{ $account->name }}
						</div>

						<div class="table-cell p-1 align-middle border-b">
							{{ implode(', ', $account->teams->pluck('app_type')->toArray()) }}
						</div>

						<div class="table-cell p-1 align-middle border-b">
							{{ $account->users()->count() }}
						</div>

						<div class="table-cell p-1 align-middle border-b text-right">
							<a href="/{{ Auth::user()->team->app_type }}/clients/import/{{ base64_encode($import_type) }}/{{ $account->id }}">
								<button class="rounded-lg bg-blue text-white px-3 py-1 text-xs">
									Import as {{ $import_type }}
								</button>
							</a>
						</div>



					</div>

				@endforeach
			
			</div>

		@endif
	</div>

</div>


<div class="flex-1 pb-8 text-grey-dark">

	@foreach($prospect_types as $type)
		<div class="mt-4 text-xl font-sans text-black border-b-4 pb-1">
			@if(!$type)
				(No Type)
			@else
				{{ $type }} <span class="text-blue">({{ $prospects->where('type', $type)->count() }})</span>
			@endif

			<span class="toggle_control uppercase text-xs rounded-lg p-1 bg-grey-lightest text-blue border cursor-pointer" data-id="{{ $loop->iteration }}">show</span>

<!-- 			<div class="float-right font-normal">
				<form action="/{{ Auth::user()->team->app_type }}/prospects/" method="post">
					@csrf
					<input type="text" name="type" class="hidden" value="{{ $type }}">
					<input type="text" name="name" class="border px-2 py-1 text-sm" placeholder="New Name">
					<button class="rounded-lg bg-blue text-sm text-white px-3 py-1">
						New Prospect
					</button>
				</form>
			</div> -->

		</div>

		<div class="table hidden" id="toggle_{{ $loop->iteration }}">

			<div class="table-row text-xs w-full">

				<div class="table-cell p-1 bg-grey-lighter uppercase border-b w-8">
					
				</div>

				<div class="table-cell p-1 bg-grey-lighter uppercase border-b w-12">
					
				</div>

				<div class="table-cell p-1 bg-grey-lighter uppercase border-b">
					Client
				</div>

				<div class="table-cell p-1 bg-grey-lighter uppercase border-b">
					People
				</div>

				<div class="table-cell p-1 bg-grey-lighter uppercase border-b w-1/5">
					User
				</div>

			</div>


			@foreach($prospects->where('type', $type) as $opportunity)
				<div class="table-row text-sm w-full mb-1">

					<div class="table-cell p-1 align-middle border-b">
						{{ $loop->iteration }}.
					</div>


					<div class="table-cell p-1 align-middle border-b ">
						<a href="/{{ Auth::user()->team->app_type }}/prospects/{{ $opportunity->id }}">
							<button class="rounded-lg bg-blue text-xs uppercase text-white px-3 py-1">
								Go
							</button>
						</a>
					</div>

					<div class="table-cell p-1 align-middle border-b w-1/4 truncate">
						{{ $opportunity->entity->name }}
					</div>


					<div class="table-cell p-1 align-middle border-b truncate">
						@if($opportunity->entity->people->first())
							{{ $opportunity->entity->people->count() }}
						@endif
					</div>


					<div class="table-cell p-1 align-middle border-b text-xs text-blue">
						{{ $opportunity->user->name }}
					</div>

				</div>
			@endforeach

		</div>

	@endforeach


</div>






@endsection

@section('javascript')
<script type="text/javascript">
	
	$(document).ready(function() {

		$("#search").focus();

		$(document).on('click', ".toggle_control", function () {
			id = '#toggle_' + $(this).data("id");

			$(id).toggleClass('hidden');
			if ($(this).html() == 'show') {
				$(this).html('hide');
			} else {
				$(this).html('show');
			}
		});
	});

</script>

@endsection
