@extends('campaign.base')

@section('title')
   	Questionnaires
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> > &nbsp;<b>Campaign Questionnaires</b>
@endsection

@section('main')

	<div class="text-3xl font-bold border-b-4 pb-2">
		<i class="text-center fas fa-poll-h mr-2"></i> {{ $questionnaire->name }}

		<a href="/{{ Auth::user()->team->app_type }}/questionnaires/{{ $questionnaire->id }}/edit">
			<button type="button" class="btn btn-default float-right mt-2">
				Return to Questionnaire
			</button>
		</a>

	</div>


	@if($questionnaire->questions->first())

		<div class="mt-6">

			@foreach($questionnaire->questions as $question)

				<div class="pb-8">

					<div class="text-blue font-bold border-b-2 border-blue pb-2 flex">

						<div class="mr-2">{{ $question->the_order }}.</div>

						@if(!$question->done)
							<div class="mr-2"><i class="fas fa-check-circle text-blue opacity-100"></i></div>
						@endif

						<div>{{ $question->question }}</div>
					</div>

					<div class="italic pl-4 pt-2 text-grey-darkest">
						{{ $question->description }}
					</div>

					<div class="text-grey-darker ml-4 pl-4 border-l-4 mt-2">

						@if($question->answer)
							<div class="font-bold text-black">Answer:</div> 
							{{ $question->answer }}
						@endif

						<div class="uppercase text-sm py-2 text-grey-dark text-right">
							{{ $question->assignedToUser()->name }}
						</div>

					</div>

				</div>

			@endforeach
		</div>
	@endif

@endsection

@section('javascript')

	<script type="text/javascript">
		
		$(document).ready(function() {

			$('#add-tag').on('shown.bs.modal', function () {
			    $('#name').focus();
			})  

		});

	</script>

@endsection