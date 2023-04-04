@extends('print')

@section('title')
    Call Log Report {{ date('n/j/Y') }}
@endsection


@section('style')

	<style>


	</style>

@endsection

@section('main')


	<div class="pb-2 text-center tracking-widest mb-2">

		@if(Auth::user()->team->logo_img)
			<div class="text-center mb-4">
		    	<img class="inline-block" style="max-height:200px;"
		    		  src="{{ config('app.url').'/storage/user_uploads/logos_'.str_pad(Auth::user()->team->id, 5, '0', STR_PAD_LEFT).'/'.Auth::user()->team->logo_img }}" />
		    </div>

		@else

			<div>OFFICE OF</div>
			<span class="text-2xl">{{ Auth::user()->team->name }}</span>
		
    	@endif

	</div>


	<div class="text-xl px-2 py-1 font-semibold text-center mb-2 tracking-widest">
		<i class="far fa-file mr-1"></i> Contact Log Report
	</div>

	<div class="text-center mb-8">
		<div class="text-sm italic">Prepared by</div>
		{{ Auth::user()->name }} / {{ \Carbon\Carbon::now()->format("F j, Y") }}
	</div>


	<div class="table">

		<div class="table-row bg-grey-lighter">

			<div class="table-cell p-2 border-b">
				Date
			</div>

<!-- 			<div class="table-cell p-2 border-b">
				Source
			</div>
 -->

			<div class="table-cell p-2 border-b">
				Assigned
			</div>

			<div class="table-cell p-2 border-b">
				Title
			</div>

			<div class="table-cell p-2 border-b">
				Type
			</div>

			<div class="table-cell p-2 border-b w-64">
				Linked People
			</div>

			<div class="table-cell p-2 border-b">
				Notes
			</div>

			<div class="table-cell p-2 border-b">
				Followup
			</div>

		</div>

	@foreach ($calls as $thecall)

			<div class="table-row">

				<div class="table-cell p-2 border-b">
					<span class="whitespace-no-wrap">{{ \Carbon\Carbon::parse($thecall->date)->format("F d, Y") }}</span>
				</div>

<!-- 				<div class="table-cell p-2 border-b">
					{{  $thecall->source }}
				</div>
 -->

				<div class="table-cell p-2 border-b">
					@if($thecall->user)
						{{  $thecall->user->name }}
					@endif
				</div>

				<div class="table-cell p-2 border-b">
					@if($thecall->user->permissions)
						{{ $thecall->user->permissions->title }}
					@endif
				</div>


				<div class="table-cell p-2 border-b">
					{{  $thecall->type }}
				</div>

				<div class="table-cell p-2 border-b">
					{{ $thecall->linked_people_concatenated }}
				</div>

				<div class="table-cell p-2 border-b">
					@if($thecall->subject)
						<span class="font-semibold">{{  $thecall->subject }}</span>
					@endif

					{{  $thecall->notes }}
				</div>

				<div class="table-cell p-2 border-b">
					{{  ($thecall->followup) ? 'Follow up' : '' }}
					{{  ($thecall->followup_on) ? ' on '.$thecall->followup_on : '' }}
					{!!  ($thecall->followup_done) ? '<i class="fas fa-check-circle"></i> Done' : '' !!}
				</div>

			</div>

	@endforeach

	</div>



@endsection
