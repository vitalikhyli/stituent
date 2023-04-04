@extends('admin.base')

@section('title')
    Upload City Data
@endsection

@section('breadcrumb')
	<a href="/admin">Admin</a>
    &nbsp;> Upload Data
@endsection

@section('style')
    
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.js" defer></script>
    
    @livewireStyles

@endsection

@section('main')

    <div class="tracking-normal">

        <div class="text-3xl font-bold border-b-4 pb-3 mt-6 flex">

            <div class="w-2/3">
                Upload Standalone Voter Files
            </div>

            <div class="flex-grow text-base text-grey-dark w-64 font-normal">
                <span class="text-black font-bold mr-1">Note:</span> This page lets us upload voter files that are <u>not</u> slices of state Master files.
            </div>


        </div>

        

    </div>

@endsection

@section('javascript')

    @livewireScripts

@endsection
