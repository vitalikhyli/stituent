@extends('campaign.base-phonebank')

@section('title')
    {{ $list->name }}
@endsection

@section('breadcrumb')
@endsection

@section('styles')

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tailwindcss/ui@latest/dist/tailwind-ui.min.css">

	@livewireStyles

@endsection

@section('main')

	@livewire('lists.phonebank', ['list' => $list])

@endsection

@section('javascript')

	@livewireScripts

@endsection