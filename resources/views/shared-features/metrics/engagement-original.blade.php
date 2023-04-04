@extends(Auth::user()->team->app_type.'.base')

@section('title')
    @lang('Engagement')
@endsection

@section('style')

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.0/dist/Chart.min.js"></script>

@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a>
    > Metrics > <b>Engagement</b>

@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue">
  <div class="text-2xl font-sans w-full">
    Metrics
  </div>

</div>

    @include('elements.graph')

    <div class="mt-8 pt-8">
    	<div class="text-center text-2xl font-bold">
    		How many @lang('constituents') has your office <span class="text-blue-dark">engaged with</span> over time?
    	</div>

    </div>

@endsection

@section('javascript')


@endsection