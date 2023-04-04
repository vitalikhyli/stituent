@extends('admin.base')

@section('title')
    Activity
@endsection

@section('breadcrumb')


@endsection

@section('style')


@endsection

@section('main')


<div class="w-full mb-4 pb-2">

    <div class="text-xl border-b-4 border-red py-2">

        <a href="/admin/accounts/new">
        	<button class="rounded-lg bg-blue float-right text-white px-4 py-2 text-base">Create New Account</button>
        </a>
        @if (request('clicks_only'))
	        <a class="float-right mr-8 text-lg" href="/admin/activity">
	        	Show All Users/Accounts
	        </a>
        @else
			<a class="float-right mr-8 text-lg" href="/admin/activity?clicks_only=true">
	        	Clicks Only
	        </a>
        @endif

        Account Activity
    </div>  




<table class="w-full text-sm">

	

	@foreach ($active_accounts as $account)

		@if (request('clicks_only'))
			@if ($account->clicks < 1)
				@continue
			@endif
		@endif
			
		<tr>
			<td colspan="100">
				<div class="float-right mt-8 pt-2">
					@if ($account->paid_through_date)
	                    @if ($account->paid_through_date > \Carbon\Carbon::today())

	                        <div class="px-2 text-sm text-center text-green">
	                            <i class="fa fa-check-circle"></i>
	                            Paid through 
	                            <b>{{ $account->paid_through_date->format('n/j/Y') }}</b>
	                            ({{ $account->paid_through_date->diffForHumans() }})
	                        </div>
	                    @else
	                        <div class="px-2 text-sm text-center">
	                            Outstanding invoice 
	                            <b>{{ $account->paid_through_date->format('n/j/Y') }}</b>
	                            ({{ $account->paid_through_date->diffForHumans() }})
	                        </div>
	                    @endif
	               @endif
				</div>
				<div class="text-xl font-bold mt-8 border-b-2 pb-2">
					{{ $account->name }}
					
				</div>
			</td>
		</tr>

		<tr class="text-grey-darker border-b-2 bg-grey-lighter">
			<td class="pl-2">Team</td>
			<td>User</td>
			<td class="pr-2">Clicks</td>
			<td>Last Activity</td>
			<td class="text-right pr-2">Mock</td>
		</tr>

		@foreach ($account->teams as $team)

			

			@foreach ($team->users as $user)

				@php
					$calculated_clicks = $user->clicks;
					$calculated_last_activity = $user->last_activity
				@endphp

				@if (request('clicks_only'))
					@if ($calculated_clicks < 1)
						@continue
					@endif
				@endif

				@if ($calculated_clicks < 1)
				<tr class="group hover:bg-grey-lighter text-grey-dark">
				@else
				<tr class="group hover:bg-grey-lighter">
				@endif

					@if ($loop->first)
						<td class="pl-2">
							<div class="text-lg font-bold text-grey-dark">
								{{ ucwords($team->app_type) }}
							</div>
						</td>
					@else
						<td></td>
					@endif

					<td class="p-1">{{ $loop->iteration }}. {{ $user->name }}</td>
					<td class="p-1">
						@if ($calculated_clicks > 0)
							<b>{{ $calculated_clicks }}</b>
						@else
							0
						@endif
					</td>
					<td class="p-1">
						@if ($calculated_last_activity > \Carbon\Carbon::today()->subWeek())
							<span class="bg-yellow">
								{{ $calculated_last_activity->format('D, M jS, g:ia') }}
							</span>
							<i class="text-grey-dark">({{ $calculated_last_activity->diffForHumans() }})</i>
						@else
							@if ($calculated_last_activity)
								{{ $calculated_last_activity->format('D, M jS, g:ia') }}
								<i class="text-grey-dark">({{ $calculated_last_activity->diffForHumans() }})</i>
							@endif
						@endif

					</td>

					<td class="text-right p-1">
						<a  class="group-hover:opacity-100 opacity-25" href="/admin/mock/{{ $user->id }}">
							Login as {{ $user->name }}
						</a>
					</td>

				</tr>

			@endforeach

		@endforeach

	@endforeach

</table>

@endsection