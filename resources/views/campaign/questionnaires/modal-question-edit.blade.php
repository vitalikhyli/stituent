<div class="modal fade" id="editQuestionModal" tabindex="-1" role="dialog" aria-labelledby="editQuestionModalLabel" aria-hidden="true">

	<form method="post" action="/{{ Auth::user()->team->app_type }}/questionnaires/{{ $questionnaire->id }}/questions/update">

		@csrf

		<div class="modal-dialog rounded-t-lg" role="document">
			<div class="modal-content rounded-t-lg">

				<div class="modal-header bg-blue text-white rounded-t-lg">
				  <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>

				    Edit Question

				</div>

				<div class="modal-body">

				  <div class="text-left font-bold py-2 text-base">

				  	<input class="hidden" name="question_id" id="modal_question_question_id" />
				  	<input class="hidden" name="questionnaire_id" id="modal_question_questionnaire_id" />

				  	<div class="mb-2">
						<span class="text-blue mb-2">Question #</span> <input name="the_order" class="border p-2 rounded-lg font-bold" id="modal_question_the_order" size="3" />

					  	<span class="text-blue mb-2 ml-2">Assigned to:</span>

						<select name="assigned_to" id="modal_question_assigned_to" class="font-normal">
							@foreach(Auth::user()->team->users as $user)
								<option value="{{ $user->id}}">
									{{ $user->name }}
								</option>
							@endforeach
						</select>

					 </div>

				  	<div class="text-blue my-2">Question Title</div>
				  	<input name="question" class="border w-full p-2 rounded-lg font-bold" id="modal_question_question" />

				  	<div class="text-blue my-2">Question Details</div>
				  	<textarea name="description" rows="6" class="border w-full p-2 rounded-lg font-normal" id="modal_question_description"></textarea>

				  	<div class="text-blue my-2">
				  		Answer

				  		<span class="ml-2 text-black float-right bg-blue-lightest p-2">
				  			<input type="checkbox" name="done" id="modal_question_done" /><label for="modal_question_done" class="ml-2">Mark Question as Done</label>
				  		</span>

				  	</div>
				  	<textarea name="answer" rows="10" class="border w-full p-2 rounded-lg font-normal" id="modal_question_answer"></textarea>

				  </div>

				</div>

				<div class="modal-footer">

				  <button type="button" class="float-left btn btn-default text-red" id="delete-question">Delete</button>

				  <button data-id="" type="button" class="hidden float-left btn btn-default text-white bg-red" id="delete-question-confirm">Confirm Delete Question</button>

				  <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
				  <button type="submit" class="btn btn-primary">Save</button>

				</div>
			</div>
			
		</div>

	</form>

</div>