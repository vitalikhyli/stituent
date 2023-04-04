@extends('campaign.base')

@section('title')
    New Campaign Lists
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> 
    > <a href="/campaign/lists">Campaign Lists</a>
    > &nbsp;<b>New List</b>
@endsection

@section('style')
	
	<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.js" defer></script>
	
	<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />


	@livewireStyles

	<style>
		
		table.table > thead > tr > th {
			border-top: 0px;
		}
	</style>

@endsection

@section('main')

	@livewire('list-builder')

@endsection

@section('javascript')

	<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/slim-select/1.23.0/slimselect.min.js"></script> -->

	<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

	

	@livewireScripts

	

@endsection