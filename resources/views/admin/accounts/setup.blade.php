@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')


@endsection

@section('style')

  @livewireStyles

@endsection

@section('main')


<div class="w-full mb-4 pb-2">

    <div class="text-xl border-b-4 border-red py-2 flex">

		<img class="absolute pin-t pin-r" style="top: 50px; right: 40px;" src="/images/stupid_paperclip.png" />

        <div class="flex-grow pt-2">
            SetUp Monkey&trade;
        </div>

        
    </div>

    <div>

    	@livewire('admin.set-up')

    </div>


</div>


<br />
<br />
<br />

@endsection

@section('javascript')

  @livewireScripts


@endsection