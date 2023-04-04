@extends(Auth::user()->team->app_type.'.base')

@section('title')
    @lang('Constituents')
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	&nbsp;<b>@lang('YOUR Thoughts')</b> 

@endsection

@section('style')

	@livewireStyles

@endsection

@section('main')

<!-- <div class="flex border-b-4 pb-2 border-blue flex">

	<div class="text-2xl font-sans flex-shrink">
		 @lang('YOUR Thoughts')
	</div>

</div> -->

<div class="flex">

	<div class="pr-6 w-2/5 py-2">

		<div class="text-grey-lightest text-xl mb-2 px-2 pt-2 pb-1 bg-blue rounded-t-lg shadow text-center">
			<b>Like</b> Community Fluency?
		</div>
		<div class="text-grey-darker text-sm mb-6 italic">
			Help us reach more users and serve more constituents by relaying your fantastic experiences with the system. Thank you!
		</div>

		@livewire('comments.endorsements')

	</div>

	<div class="pl-6 w-3/5 py-2 border-l border-dashed">

		<div class="text-grey-lightest text-xl mb-2 px-2 pt-2 pb-1 bg-blue rounded-t-lg shadow text-center">
			Community <b>Idea Board</b>
		</div>
		<div class="text-grey-darker text-sm mb-6 italic">
			We are always working to be better, according to the needs of our clients. What improvements or changes would you like to see in Community Fluency? Jot your ideas down here, or vote on the suggestions of others.
		</div>

		@livewire('comments.ideas')

	</div>
</div>


@endsection

@section('javascript')

	@livewireScripts

@endsection
