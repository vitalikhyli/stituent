@extends('campaign.base')

@section('title')
   	Questionnaires
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> > &nbsp;<b>Campaign Questionnaires</b>
@endsection

@section('main')

	<div class="text-3xl font-bold border-b-4 pb-2">
		Questionnaires
		@if($questionnaires_count > 0)
			({{$questionnaires_count}})
		@endif
		<span class="text-blue">*</span>
	</div>


	<div class="flex text-grey-dark w-full">
		<div class="w-2/3">
			<div class="text-center m-8">
				<button class="rounded-full bg-blue text-white py-4 text-xl font-thin tracking-wide uppercase px-8 hover:bg-blue-dark" data-toggle="modal" data-target="#add-tag">
						Add a Questionnaire
				</button>
			</div>
		</div>
		<div class="w-1/3">
			<div class="p-2">
				<span class="text-blue text-2xl font-bold">*</span> Use <span class="font-bold text-black">Questionnaires</span> to keep track of the many endorsement surveys you are likely to receive while running for office. Each question can be handled and delegated separately.
			</div>
		</div>
	</div>

	@include('campaign.questionnaires.questionnaire-add-modal')

	@if($questionnaires_todo->first())

		<div class="text-xl font-bold border-b-4 pb-2">
			To Do
			@if($questionnaires_todo_count > 0)
				({{$questionnaires_todo_count}})
			@endif
		</div>

		@include('campaign.questionnaires.questionnaires-table', ['questionnaires' => $questionnaires_todo])
		
	@endif

	@if($questionnaires_done->first())

		<div class="opacity-50 mt-8">

			<div class="text-xl font-bold border-b-4 pb-2">
				Done
				@if($questionnaires_done_count > 0)
					({{$questionnaires_done_count}})
				@endif
			</div>

			@include('campaign.questionnaires.questionnaires-table', ['questionnaires' => $questionnaires_done])

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