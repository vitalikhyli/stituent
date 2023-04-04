@extends('admin.base')

@section('title')
    Upload City Data
@endsection

@section('breadcrumb')
	<a href="/admin">Admin</a>
    &nbsp;> Paste Data
@endsection

@section('style')
    
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.js" defer></script>
    

    @livewireStyles

@endsection

@section('main')

    <div class="tracking-normal">

        <div class="text-3xl font-bold border-b-4 pb-3 mt-6 flex">

            <div class="w-1/2">

                Paste Data into an Import
                
            </div>

        </div>

        <div class="py-2">

            @livewire('admin-upload-to-master.paste')

        </div>


@endsection

@section('javascript')

    @livewireScripts

@endsection
