@extends('campaign.base')

@section('title')
    Edit Questionnaire
@endsection

@section('style')


@endsection

@section('main')

<div class="w-full">


	<div class="text-3xl font-bold border-b-4 pb-2">

		<i class="text-center fa fa-poll-h mr-2"></i> 
		{{ $questionnaire->name}}

		<span class="text-blue">: Edit Questionnaire</span>

	 	<div class="float-right text-sm mt-2">
		    <button type="button" data-toggle="modal" data-target="#deleteModal" id="delete" class="rounded-lg py-2 px-4 text-red text-center ml-2 bg-grey-lighter font-normal"/>
		    	<i class="fas fa-exclamation-triangle mr-2"></i> Delete Questionnaire
		    </button>
	 	</div>

	</div>

	<form id="update_questionnaire-form" action="/{{ Auth::user()->team->app_type }}/questionnaires/{{ $questionnaire->id }}/update" method="POST">

     	@csrf

		@if(session('errors'))
			<div class="p-2">
				@foreach(session('errors')->messages() as $field => $message)
					<div class="text-red">{{ $message[0] }}</div>
				@endforeach
			</div>
		@endif
			
		<div class="p-1">

			<div class="flex -my-1">
				<div class="w-1/6  text-sm uppercase text-right p-2 pt-4 whitespace-no-wrap">
					Name
				</div>
				<div class="p-2 w-2/3">
					<input required name="name" id="name" type="text" class="w-full border rounded-lg p-2" placeholder="Tag Name" value="{{ (old('name')) ? old('name') : $questionnaire->name }}" />
				</div>
			</div>

			<div class="flex -my-1">
				<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
					Due on
				</div>
				<div class="p-2 w-5/6">
					<input required autocomplete="off" name="due" size="10" type="text" class="datepicker border rounded-lg p-2" placeholder="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" value="{{ (old('due')) ? old('due') : \Carbon\Carbon::parse($questionnaire->due)->format('m/d/Y') }}" />
				</div>
			</div>

			<div class="flex -my-1">
				<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
					Done?
				</div>
				<div class="p-2 w-5/6">
					<input type="checkbox" name="done" id="done" {{ ($questionnaire->done) ? 'checked' : '' }} />
				</div>
			</div>


			<div class="flex -my-1">
				<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
					Assigned To:
				</div>
				<div class="p-2 w-5/6">
					<select name="user_id">
						@foreach(Auth::user()->team->users as $user)
							<option value="{{ $user->id }}" {{ ($questionnaire->user_id == $user->id) ? 'selected' : '' }}>
								{{ $user->name }}
							</option>
						@endforeach
					</select>
				</div>
			</div>


			<div class="flex mt-2 w-full border-t-2 pt-2">
				<div class="w-1/6 text-sm uppercase text-right p-2 pt-4">
					Add a Question:
				</div>
				<div class="p-2 w-5/6">
					<input name="new_question" id="new_question" type="text" class="w-full border rounded-lg p-2" placeholder="New Question Here" />
				</div>
			</div>

		</div>



		@if($questionnaire->questions->first())		

			<div class="my-4">

				<div class="text-xl font-bold border-b-4 pb-2">

					Questions & Answers

<!-- 						 	<div class="float-right text-sm">
				    	<button type="button" class="rounded-lg py-2 px-4 text-blue text-center ml-2 bg-grey-lighter font-normal"/>
				    		<i class="fas fa-download mr-2"></i> Export Answers
				    	</button>
				 	</div> -->

				 	<div class="float-right text-sm">
				 		<a href="/{{ Auth::user()->team->app_type }}/questionnaires/{{ $questionnaire->id }}/answers">
					    	<button type="button" class="rounded-lg py-2 px-4 text-blue text-center ml-2 bg-grey-lighter font-normal"/>
					    		<i class="fas fa-print mr-2"></i> Display Answers
					    	</button>
					    </a>
				 	</div>


				</div>

				@foreach($questionnaire->questions->sortBy('the_order') as $question)

					<div class="border-b flex cursor-pointer edit-question" data-question_id="{{ $question->id }}" data-questionnaire_id="{{ $questionnaire->id }}">

						<div class="p-2 w-6 text-grey {{ ($question->done) ? 'opacity-50' : '' }}">
							{{ $question->the_order }}
						</div>

						<div class="p-2 w-8 text-sm">
							@if(!$question->done)
								<!-- Pending -->
							@else
								<i class="fas fa-check-circle text-blue opacity-100 text-xl"></i>
							@endif
						</div>

						<div class="p-2 text-sm {{ ($question->done) ? 'opacity-50' : '' }}">
							@if($question->assigned_to)
								{{ $question->assignedToUser()->name }}
							@endif
						</div>

						<div class="p-2 w-2/5 text-sm border-l {{ ($question->done) ? 'opacity-50' : '' }}">

							@if($question->question)
								<span class="font-bold text-blue mr-1">
									{{ $question->question }}
								</span>
							@endif

							@if(!$question->description)
								<span class="text-grey-dark">No question text yet</span>
							@else
								{{ substr($question->description,0,200) }} ...
							@endif
						</div>

						<div class="p-2 w-2/5 text-sm border-l {{ ($question->done) ? 'opacity-50' : '' }}">
							@if(!$question->answer)
								<span class="text-red">No answer yet</span>
							@else
								{{ substr($question->answer,0,200) }} ...
							@endif
						</div>

					</div>

				@endforeach

			</div>
			
		@endif


		<div class="modal-footer bg-grey-lightest text-white mt-8">

			<button type="submit" class="btn btn-primary">
				Save
			</button>

			<button type="submit" formaction="/{{ Auth::user()->team->app_type }}/questionnaires/{{ $questionnaire->id }}/update/close" class="btn btn-default">
				Save & Close
			</button>

		</div>

	</form>

</div>

<!---------------------------- MODALS ---------------------------->

@include('campaign.questionnaires.modal-question-edit', ['questionnaire' => $questionnaire])

<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
		<div class="modal-header">
		  <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
		</div>
		<div class="modal-body">
		  <div class="text-lg text-left text-red font-bold">
		    Are you sure you want to delete this questionnaire?
		  </div>
		  <div class="text-left font-bold py-2 text-base">
		    This will delete the questionnaire and all related questions.
		  </div>
		</div>
		<div class="modal-footer">
		  <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
		  <a href="/{{ Auth::user()->team->app_type }}/questionnaires/{{ $questionnaire->id }}/delete" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, DELETE IT</a> </div>
		</div>
	</div>
</div>


<!-------------------------- END MODALS -------------------------->

@endsection


@section('javascript')

	<script type="text/javascript">
		
		$(document).ready(function() {

			$('#name').focus();

	        $(document).on('click', ".edit-question", function () {

	       		question_id = $(this).data("question_id");
	        	questionnaire_id = $(this).data("questionnaire_id");

	        	$.get('/{{ Auth::user()->team->app_type }}/questionnaires/'+questionnaire_id+'/questions/'+question_id+'/getAJAX', function(response) {

	            if (response != '') {

	            	var data = JSON.parse(response);
	              
	              	$('#modal_question_questionnaire_id').val(data.questionnaire_id);
	              	$('#modal_question_question_id').val(data.question_id);
	              	$('#delete-question-confirm').data('id', data.question_id);
	              	$('#modal_question_question').val(data.question);
	              	$('#modal_question_description').val(data.description);
	              	$('#modal_question_answer').val(data.answer);
	              	$('#modal_question_the_order').val(data.the_order);
	              	$("#modal_question_done"). prop("checked", data.done);
	              	$('#modal_question_assigned_to').val(data.assigned_to);
	              	$('#editQuestionModal').modal('show');

	            }
	            
	        	});

	    	});

	    	$(document).on('click', "#delete-question", function () {
	    		$('#delete-question-confirm').toggleClass('hidden');
	    	});

	    	$(document).on('click', "#delete-question-confirm", function () {
	    		id = $('#delete-question-confirm').data('id');
	    		url = '/{{ Auth::user()->team->app_type }}/questionnaires/{{ $questionnaire->id }}/questions/'+id+'/delete';
	    		window.location.href = url;
	    	});

	    });

	</script>

@endsection