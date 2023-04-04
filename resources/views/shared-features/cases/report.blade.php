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
		<i class="far fa-file mr-1"></i> Cases Report
	</div>

	<div class="text-center mb-8">
		<div class="text-sm italic">Prepared by</div>
		{{ Auth::user()->name }} / {{ \Carbon\Carbon::now()->format("F j, Y") }}
	</div>


	@if(!$cases->first())

		<div class="italic">
			No Cases
		</div>

	@else

		<div class="w-full text-sm">

<!-- 			<div class="w-full bg-grey-lighter flex mb-4">

				<div class="w-1/4 p-2 border-b-2">
					Date, etc.
				</div>

				<div class="w-1/4 p-2 border-b-2">
					Linked People
				</div>

				<div class="w-1/2 p-2 border-b-2 w-1/2">
					Notes
				</div>

			</div>
 -->

				@foreach ($cases as $case)

					<div class="flex border-2 bg-grey-lightest">

						<div class="w-1/4 p-2">
							<span class="whitespace-no-wrap font-bold text-base">
								{{ \Carbon\Carbon::parse($case->date)->format("F d, Y") }}
							</span>

							<div class="italic text-grey-dark whitespace-no-wrap">
								@if($case->user)
									{{ $case->user->short_name }}
								@endif

								<span class="font-bold uppercase text-grey-darker">
									{{ $case->status }}
								</span>

							</div>
						</div>


						<div class="w-1/4 p-2 capitalize">

							<!-- {{ $case->linked_people_concatenated }} -->

							@foreach($case->people as $person)
								<div class="{{ (!$loop->last) ? 'mb-2' : '' }}">

									<div class="font-bold">
										{{ $person->full_name }}
									</div>

									@if($person->full_address)
										<div class="text-grey-darker text-xs pl-4 underline">
											{{ $person->full_address }}
										</div>
									@endif

									@if($person->primary_email)
										<div class="text-grey-darker text-xs pl-4">
											<span class="font-bold">Primary:</span> {{ $person->primary_email }}
										</div>
									@endif

									@if($person->work_email)
										<div class="text-grey-darker text-xs pl-4">
											<span class="font-bold">Work:</span> {{ $person->work_email }}
										</div>
									@endif

									@if($person->other_emails)
										@foreach($person->other_emails as $email)
											<div class="text-grey-darker text-xs pl-4">
												@if(is_array($email))
								                    @if(isset($email[1]))
								                    	<span class="font-bold">{{ $email[1] }}:</span>
								                    @endif
								                     {{ $email[0] }}
								                @else
								                    {{ $email }}
								                @endif
											</div>
										@endforeach
									@endif

									@if($person->primary_phone)
										<div class="text-grey-darker text-xs pl-4">
											<span class="font-bold">Primary:</span> {{ $person->primary_phone }}
										</div>
									@endif

									@if($person->work_phone)
										<div class="text-grey-darker text-xs pl-4">
											<span class="font-bold">Work:</span> {{ $person->work_phone }}
										</div>
									@endif

									@if($person->other_phones)
										@foreach($person->other_phones as $phone)
											<div class="text-grey-darker text-xs pl-4">
												@if(is_array($phone))
								                    @if(isset($phone[1]))
								                    	<span class="font-bold">{{ $phone[1] }}:</span>
								                    @endif
								                     {{ $phone[0] }}
								                @else
								                    {{ $phone }}
								                @endif
											</div>
										@endforeach
									@endif

								</div>

							@endforeach

						</div>

						<div class="w-1/2 p-2">
							@if($case->subject)
								<span class="font-semibold">{{  $case->subject }}</span>
							@endif

							{{  $case->notes }}
						</div>

					</div>

					@if($show_notes)
						<div class="ml-8 mb-8 w-full">
							@foreach($case->contacts->sortByDesc('date') as $note)
								<div class="p-2 flex w-full">
									<div class="font-bold w-16 pr-2 border-r mr-2">
										{{ Carbon\Carbon::parse($note->date)->format('n/j/y') }}
									</div>
									<div>
										@if($note->subject)
											<span class="italic underline mr-2">{{ $note->subject }}</span>
										@endif
										{{ $note->notes }}
									</div>
								</div>
							@endforeach
						</div>
					@else
						<div class="my-4">
							
						</div>
					@endif

			@endforeach

		</div>

	@endif


@endsection
