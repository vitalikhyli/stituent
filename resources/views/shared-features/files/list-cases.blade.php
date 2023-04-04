@foreach($cases as $case)

	<div class="w-full mb-2">

		<div class="p-1 border-b-2 w-full border-blue text-blue flex">
			
			<div class="w-4/5">
				<a href="/{{ Auth::user()->team->app_type }}/cases/{{ $case->id }}">
					<i class="w-6 fa fa-folder text-xl mr-2 text-center"></i>
					<span class="font-bold">{{ $case->subject }}</span>
					<span class="text-grey-darker text-sm"> - {{ \Carbon\Carbon::parse($case->date)->toDateString() }}</span>
				</a>
				| <span class="text-sm">{{ $case->assignedto()->name }}</span>
			</div>
			
		</div>

		<div class="mt-1 ml-1 table">
			@foreach($case->files as $thefile)

				@include('shared-features.files.one-file', compact($thefile))

			@endforeach
		</div>

	</div>

@endforeach