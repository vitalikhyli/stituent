@extends('campaign.base')

@section('title')
    Id'd Household Members
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> 
    > <a href="/campaign/special">Special Pages</a> 
    > &nbsp; <b>Id'd Household Members</b>
@endsection

@push('styles')
	@livewireStyles
@endpush


@section('main')

	<div class="text-3xl font-bold border-b-4 pb-2">
		Household Members of ID'd voters
	</div>

	<div class="flex">
		<div class="text-gray-500 p-4">
			This page shows all households with:
			<ul class="list-disc pl-12">
				<li>More than 1 voter in household</li>
				<li>At least one person in the household has been ID'd by the campaign.</li>
			</ul>
		</div>
		<div class="p-2 text-gray-600">
			You can <b>cross-reference</b> by any of your lists here: 
			<form action="" method="GET">
				<select name="list" class="border-2 rounded p-2">
					<option value="">~ Filter By List ~</option>
					@foreach (Auth::user()->campaignLists->sortBy('name') as $list)
						<option value="{{ $list->id }}"
								@if (request('list') == $list->id)
									selected
								@endif
								>
							{{ $list->name }} ({{ count($list->cached_voters) }})
						</option>
					@endforeach
				</select>
				<button class="bg-blue-400 hover:bg-blue-500 text-white px-4 py-2 rounded" type="submit">Go</button>
			</form>
			
		</div>
	</div>

	<table wire:loading.class="opacity-50" class="table text-grey-dark text-sm mt-8">

		<tr>
			<th></th>
			<th colspan="">Name/Phone/Email</th>
			<th>Address</th>
			<th class="text-left">Actions</th>
			<th class="text-center">
				<div class="w-5/6">
					Support (1=YES, 5=NO)
				</div>
			</th>
		</tr>
		@foreach ($grouped_by_household as $household_id => $voters)

			<tr>
				<td class="pt-8 font-bold text-black border-b-2 pb-2 text-2xl" colspan="100">
					{{ $voters->first()->full_address }}
				</td>
			</tr>



			@foreach ($voters as $voter)
				@livewire('participant-details', 
					[
						'voter_or_participant' => $voter,
						'iteration' => $loop->iteration,
						'edit' => false,
						'tag_with_id' => 0
					], 
					key($voter->id.'_'.$loop->iteration.'_'.Str::random(10)
				))
			@endforeach

		@endforeach

	</table>

@endsection

@push('scripts')
	@livewireScripts
@endpush

@section('javascript')


@endsection