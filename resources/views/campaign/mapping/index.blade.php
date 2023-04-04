@extends('campaign.base')

@section('title')
    Lawn Signs
@endsection

@section('style')
	@livewireStyles

@endsection

@section('main')

<div>
    <div class="w-full flex text-lg font-bold pb-2">


    	<div class="w-1/5 text-3xl">
    		{{ $list->name }}
    	</div>
		<div class="w-1/5 mt-3 text-center">
			<!-- <img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/green-dot.png" />
			<span class="text-green-dark">Yes</span>
			<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/yellow-dot.png" />
			<span class="text-yellow-dark">Lean Yes</span>
			<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/orange-dot.png" />
			<span class="text-orange">Undecided</span>
			<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/red-dot.png" />
			<span class="text-red">No</span> -->
		</div>
		<div class="w-3/5 text-blue mt-3 text-right">
			<img class="w-6 ml-4 -mt-2" src="http://maps.google.com/mapfiles/ms/icons/green-dot.png" />
			{{ $voters->count() }} ID'd Voters
			@if ($extra_participants->count() > 0)
				({{ $extra_participants->count() }} others)
			@endif
		</div>
		
		
		<!-- <i class="fa fa-info-circle text-grey hover:text-blue transition cursor-pointer pl-2"></i> -->
		<!-- <span class="text-blue">*</span> -->
	</div>
	<div class="w-full">

		<div id="map" class="w-full border-4" style="height: 400px;"></div>

	</div>

	<div class="h-16"></div>

	<div class="flex">
		<div class="w-1/3">
			<div class="cursor-pointer inline">

			</div>
		</div>
		<div class="w-1/3 text-center">

			

		</div>
		<div class="w-1/3">

			<div class="float-right">
				@include('campaign.lists.export-button', ['list' => $list, 'title' => 'Export Voters'])
			</div>
			<div class="float-right text-xs mr-4 mt-1">
				<a href="/campaign/lists/{{ $list->id }}/edit">Edit List</a>
			</div>
		</div>
	</div>

	

	<table wire:loading.class="opacity-50" class="table text-grey-dark text-sm mt-8">

		<tr>
			<th colspan="2"></th>
			<th>Address</th>
			<th class="text-center">Phone/Email</th>
			<th class="text-center">
				<div class="w-5/6">
					Support (1=YES, 5=NO)
				</div>
			</th>
		</tr>

		@php($edit_mode = false)


		@foreach ($voters as $voter)


			@livewire('participant-details', ['voter_or_participant' => $voter, 'iteration' => $loop->iteration, 'edit' => $edit_mode, 'tag_with_id' => request('tag_with')], key($voter->id.Str::random(10)))

		@endforeach

		<tr>
			<th colspan="100">
				<div class="text-2xl font-bold pt-8 text-black">
					No Voter ID
				</div>
			</th>
		</tr>


		@foreach ($extra_participants as $participant)


			@livewire('participant-details', ['voter_or_participant' => $participant, 'iteration' => $loop->iteration, 'edit' => $edit_mode, 'tag_with_id' => request('tag_with')], key($participant->id.Str::random(10)))

		@endforeach

	</table>

</div>

@include('campaign.lists.export-modal')



@endsection

@push('scripts')

	@livewireScripts

 	@include('campaign.lists.map')

 	@include('campaign.lists.export-modal-js')

@endpush