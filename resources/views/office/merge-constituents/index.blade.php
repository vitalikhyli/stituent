@extends('office.base')

@section('title')
	Merge Constituents
@endsection

@section('style')
    
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.js" defer></script>
    

@endsection

@section('main')

	@livewire('merge-constituents', ['one' => $one])

	<!-- This is the old version -->

@endsection

@section('javascript')



@endsection