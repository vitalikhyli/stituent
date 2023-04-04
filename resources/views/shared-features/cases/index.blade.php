@extends(Auth::user()->team->app_type.'.base')

@section('title')
    Cases
@endsection


@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a>
	> <b>Cases</b>

@endsection 

@section('style')

	@livewireStyles

	<style>
		[x-cloak] {
			display: none;
		}
	</style>
	
@endsection


@section('main')

      @livewire('cases.index')


@endsection

@section('javascript')

	@livewireScripts

	<script type="text/javascript">
		
		$(document).ready(function() {
			$("#search").focus();
		});

	</script>

@endsection
