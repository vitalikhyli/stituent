@extends('campaign.base')

@section('title')
    {{ $tag->name }}
@endsection

@section('style')

	<link href="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.23.0/slimselect.min.css" rel="stylesheet"></link>
	@livewireStyles


@endsection


@section('breadcrumb')
    <a href="/campaign">HQ</a>
     > &nbsp;<a href="/campaign/tags">Tags</a>
     > <b>{{ $tag->name }}</b>
@endsection

@section('main')


<div class="w-full">

	<div class="text-3xl font-bold border-b-4 pb-2 mb-3 flex">
		Tag: {{ $tag->name }}

		<!-- <span class="text-blue">*</span> -->
	</div>



		<!-------------------------------------------------------->


		<table wire:loading.class="opacity-50" class="table text-grey-dark text-sm mt-8">

			<tr>
				@if(request('tag_with'))
					<th>Tag</th>
				@endif
				<th colspan="2" class="">
					
				</th>

				<th>Address</th>

				<th>Phone</th>

				<th class="text-center">
					<div class="w-5/6">
						Support 
						<!-- (1=YES, 5=NO) -->
					</div>
				</th>

			</tr>



			@foreach ($tag->participants as $participant)


				@livewire('participant-details', ['voter_or_participant' => $participant, 
												  'iteration' => $loop->iteration, 
												  'edit' => false, 
												  'tag_with_id' => request('tag_with'),
												  ])

			@endforeach

		</table>



	

</div>


@endsection


@section('javascript')

	@livewireScripts


@endsection
