@extends('admin.base')

@section('title')
    Admin Dashboard - Audit Teams
@endsection

@section('breadcrumb')


@endsection

@section('style')

  @livewireStyles

@endsection

@section('main')


<div class="w-full mb-4 pb-2">

  @livewire('admin.teams-audit')

</div>


@endsection

@section('javascript')

  @livewireScripts

@endsection