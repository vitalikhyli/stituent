@extends(Auth::user()->team->app_type.'.base')

@section('title')
    @lang('Groups Maps')
@endsection

@section('style')

    

@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a>
    > <a href="/{{ Auth::user()->team->app_type }}/maps">Maps</a>
    > &nbsp;<b>Groups</b>

@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue">
  <div class="text-2xl font-sans w-full font-bold">
    Group Maps
  </div>

  @include('shared-features.maps.links')

</div>


@endsection

@section('javascript')


@endsection