@extends(Auth::user()->team->app_type.'.base')

@section('title')
    {{ $group->name }}
@endsection

@section('breadcrumb')

  <a href="/{{ Auth::user()->team->app_type }}">Home</a>
  > <a href="/{{ Auth::user()->team->app_type }}/groups">Groups</a>
  > &nbsp;<b>{{ $group->name }}</b>

@endsection

@section('style')

    <style>

      [x-cloak] { display: none; }
      
    </style>

    @livewireStyles

@endsection

@section('main')

    @livewire('groups.show', ['group' => $group])

@endsection

@section('javascript')

    @livewireScripts

    <script type="text/javascript">

        $(document).ready(function() {

            $("#search").focus();
             
        });

    </script>

@endsection
