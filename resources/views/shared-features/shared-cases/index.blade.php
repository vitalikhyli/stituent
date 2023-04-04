@extends(Auth::user()->team->app_type.'.base')

@section('title')
    Shared Cases
@endsection


@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a>
		> <b>Shared Cases</b>

@endsection 

@section('style')

	@livewireStyles

@endsection


@section('main')

	<div class="flex border-b-4 border-blue pb-2">
		<div class="w-1/4">
			<div class="text-3xl font-sans font-bold">
				
				Shared Cases
			</div>
		</div>
		<div class="w-3/4">
		</div>
	</div>

	<div class="flex items-center mt-8 p-4 bg-blue-lightest rounded-lg">
		<div class="w-3/5">
			<div class="text-xl font-bold text-blue-dark">
				How to collaborate with another office
			</div>
			<div class="text-grey-darker">
				You can now share casework with any other subscriber or office using Community Fluency. The steps work like this:
				<ol>
					<li>Go to your <b>case page</b>, or select a case below</li>
					<li>Enter the subscriber's email address in the <b>Sharing section</b></li>
					<li>Choose <b>User or Team</b> for individual or entire office access</li>
					<li>They can now see your shared case on the <b>Shared Cases page</b> (here!)</li>
					<li>Collaborate on <b>contacts, files, notes, priorities</b> &mdash; everything on the case!
				</ol>
			</div>
			
		</div>
		<div class="w-2/5">
			<div class="border-2 m-4 rounded-lg">
				@include('components.video', ['slug' => 'shared-cases'])
			</div>
		</div>
	</div>

	<div class="mt-8">
		<div class="uppercase text-grey-darker font-bold border-b-4 w-full pb-2">
			<span class="text-black">Share a case</span>:
		</div>
		<div class="w-1/2 mt-4 px-4">
			@livewire('case-share', ['allcases' => $allcases])
		</div>
	</div>

	<div class="flex mt-16">
		<div class="w-1/2">
			<div class="uppercase text-grey-darker font-bold border-b-4 mr-8 text-lg pb-2">
				Cases <span class="text-black">I am Sharing</span>:
			</div>
			@foreach ($sharing as $case_id => $scs)
				<div class="py-4">
					@foreach ($scs as $shared_case)
						@if (!$shared_case->case)
							@continue
						@endif
						@if ($loop->iteration == 1)
							<div class="flex">
								<div class="w-1/5 text-sm text-grey-darker">
									{{ $shared_case->case->date->format('n/j/Y') }}
								</div>
								<div class="w-4/5">
									<b>
										@if ($shared_case->case->type)
											{{ strtoupper($shared_case->case->type) }}:
										@endif
										<a href="/office/cases/{{ $shared_case->case_id }}">
										{{ $shared_case->case->subject }}
									</a></b><br>
									@foreach ($shared_case->case->people as $person)
										<div class="text-sm text-gray-dark">
											{{ $person->name }}
										</div>
									@endforeach
									{{ $shared_case->case->description }}
								</div>
							</div>
						@endif
						<div class="flex text-grey-dark">
							<div class="w-1/5">
								
							</div>
							<div class="w-4/5">
								{{ $loop->iteration }}.
								{{ strtoupper($shared_case->shared_type) }}:
								{{ $shared_case->name }}
							</div>
						</div>
					@endforeach
				</div>
			@endforeach
		</div>
		<div class="w-1/2">
			<div class="uppercase text-grey-darker font-bold border-b-4 text-lg pb-2">
				Cases <span class="text-black">Shared with me</span>:
			</div>
			@foreach ($shared as $case_id => $scs)
				<div class="py-4">
					@foreach ($scs as $shared_case)
						@if (!$shared_case->case)
							@continue
						@endif
						@if ($loop->iteration == 1)
							<div class="flex">
								<div class="w-1/5 text-sm text-grey-darker">
									{{ $shared_case->case->date->format('n/j/Y') }}
								</div>
								<div class="w-4/5">
									<b>
										@if ($shared_case->case->type)
											{{ strtoupper($shared_case->case->type) }}:
										@endif
										<a href="/office/cases/{{ $shared_case->case_id }}">
										{{ $shared_case->case->subject }}
									</a></b><br>
									@foreach ($shared_case->case->people as $person)
										<div class="text-sm text-gray-dark">
											{{ $person->name }}
										</div>
									@endforeach
									{{ $shared_case->case->notes }}
								</div>
							</div>
						@endif
						<div class="flex text-grey-dark">
							<div class="w-1/5 pr-2">
								
							</div>
							<div class="w-4/5">
								Shared By: <b>{{ $shared_case->user->name }}, {{ $shared_case->team->name }}</b> 
							</div>
						</div>
					@endforeach
				</div>
			@endforeach
		</div>
	</div>
@endsection

@push('scripts')
	@livewireScripts
@endpush