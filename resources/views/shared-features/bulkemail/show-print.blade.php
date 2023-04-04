@extends('print')

@section('title')
    Bulk Email: {{ $email->subject }}
@endsection


@section('style')

	<style>


	</style>

@endsection

@section('above-main')
	<div class="font-bold text-lg border-b-4 text-left w-2/3 mt-4">
		Email Summary
	</div>

	<div class="table mt-2 text-left w-2/3">

		<div class="table-row">
			<div class="table-cell font-bold w-1/6">
				Date
			</div>
			<div class="table-cell">
				{{ $email->send_date }}
			</div>
		</div>

		@if($email->completed_at)
			<div class="table-row">
				<div class="table-cell font-bold w-1/6">
					Finished sending
				</div>
				<div class="table-cell">
					{{ $email->completed_at }}
				</div>
			</div>
		@endif

		<div class="table-row">
			<div class="table-cell font-bold w-1/6">
				Name
			</div>
			<div class="table-cell">
				{{ $email->name }}
			</div>
		</div>

		<div class="table-row">
			<div class="table-cell font-bold w-1/6">
				Subject
			</div>
			<div class="table-cell">
				{{ $email->subject }}
			</div>
		</div>

		<div class="table-row">
			<div class="table-cell font-bold w-1/6">
				From
			</div>
			<div class="table-cell">
				{{ $email->sent_from }} 
				@if($email->sent_from_email)
					<{{ $email->sent_from_email }}>
				@endif
			</div>
		</div>

		<div class="table-row">
			<div class="table-cell font-bold w-1/6">
				Count
			</div>
			<div class="table-cell">
				{{ ($email->expected_count) ? $email->expected_count : 0 }}
			</div>
		</div>


		@if($email->search && $email->search->form)
			<div class="table-row">
				<div class="table-cell font-bold w-1/6">
					Recipient Criteria
				</div>
				<div class="table-cell">
					@foreach ($email->search->formEnglish as $key => $values)

						<div>
							- <span class="italic">{{ $key }}</span>

							@if(is_array($values))
								@foreach($values as $thevalue)
									<span class="text-grey-darker">
										{{ $thevalue }}
										{{ (!$loop->last) ? ',' : '' }}
									</span>
								@endforeach
							@else
								{{ $values }}
							@endif
									
						</div>
					@endforeach
				</div>
			</div>
	 	@endif

	</div>
@endsection

@section('main')

	{!! $email->content !!}

@endsection
