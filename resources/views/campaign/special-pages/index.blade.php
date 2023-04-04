@extends('campaign.base')

@section('title')
    Special Pages
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> 
    > &nbsp; <b>Special Pages</b>
@endsection

@push('styles')
	@livewireStyles
@endpush

@section('main')

	<div class="text-3xl font-bold border-b-4 pb-2">
		Special Pages
		<!-- <i class="fa fa-info-circle text-grey hover:text-blue transition cursor-pointer pl-2"></i> -->
		<span class="text-blue">*</span>
	</div>

	<div class="flex text-grey-dark w-full">
		<div class="w-2/3 p-4">

			@foreach (\App\SpecialPage::where('app', 'campaign')->whereNotNull('live_link')->get() as $sp)
				<div class="p-2 text-gray-500">
					<div class="font-bold text-blue-500 text-lg">
						<a href="{{ $sp->live_link }}">
							{{ $loop->iteration }}. {{ $sp->name }}
						</a>
					</div>
					<div class="">

						<span class="text-sm text-gray-400">
							{{ $sp->created_at->format('n/j/y') }}
						</span> - {{ $sp->description }}
						
					</div>
				</div>
			@endforeach

		</div>
		<div class="w-1/3">
			<div class="p-2">
				<span class="text-blue text-2xl font-bold">*</span> <span class="font-bold text-black">Special Pages</span> are one-off ideas and custom pages requested by users. Use this page here to see what exists, suggest your own, and star your favorite ideas.
			</div>
		</div>
	</div>

	@livewire('special-pages', ['app' => 'campaign'])

@endsection

@push('scripts')
	@livewireScripts
@endpush