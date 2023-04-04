@extends('campaign.base')

@section('title')
    Web Forms
@endsection

@section('style')
	@livewireStyles

@endsection

@section('main')

	<div class="text-3xl font-bold border-b-4 pb-2">
		Web Signups
		@if($websignups->count() > 0)
			({{ $websignups->count() }})
		@endif
		<span class="text-blue">*</span>
	</div>


	<div class="flex text-grey-dark w-full">
		<div class="w-2/3">
			<div class="text-center m-8">
				<button class="rounded-full bg-blue text-white py-4 text-xl font-thin tracking-wide uppercase px-8 hover:bg-blue-dark" data-toggle="modal" data-target="#add-web-form">
						Add a Web Form
				</button>
			</div>
		</div>
		<div class="w-1/3">
			<div class="p-2">
				<span class="text-blue text-2xl font-bold">*</span> Use <span class="font-bold text-black">Web Forms</span> to add code to your personal website so users can sign up themselves to be a part of your campaign.
			</div>
		</div>
	</div>

	<div class="">
		@foreach ($webforms as $webform)
			<div class="">
				@livewire('web-signups', ['webform' => $webform])
			</div>
		@endforeach
	</div>

	@include('campaign.web-forms.web-form-add-modal')

@endsection

@push('scripts')

	@livewireScripts

@endpush