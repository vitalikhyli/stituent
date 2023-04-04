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
    
    <style type="text/css">
        .fade-in {
            animation:fadeIn 1s;
        }
        @keyframes fadeIn {
          0% {
            opacity:0;
          }
          100% {
            opacity:1;
          }
        }
    </style>
    
    @livewireStyles

@endsection

@section('main')

    <div class="tracking-normal">
        <div class="text-3xl font-bold border-b-4 pb-3 mt-6">
            Upload Data File
        </div>

        @livewire('admin-upload-to-master.new-upload', ['import_id' => $import->id])


    </div>


@endsection

@section('javascript')

    @livewireScripts

@endsection
