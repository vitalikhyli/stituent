@extends('campaign.base')

@section('title')
    {{ $list->name }}
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> 
    > <a href="/campaign/lists">Campaign Lists</a>
    > &nbsp;<b>{{ $list->name }}</b>
@endsection

@section('styles')

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tailwindcss/ui@latest/dist/tailwind-ui.min.css">


	@livewireStyles

@endsection

@section('main')

	@livewire('list-display', ['list' => $list])

@endsection

@section('javascript')

	@livewireScripts

@endsection