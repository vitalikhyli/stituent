@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')


@endsection

@section('style')

@endsection

@section('main')


<div class="w-full mb-4 pb-2 ">


    <div class="text-xl mb-4 border-b-4 border-red py-2">
        Average Time All Users
    </div>  

@include('admin.userlogs.nav')


<div class="py-2">
  <form action="/admin/userlogs/dates" method="post">
    @csrf
    <input type="text" name="from_date" placeholder="From Date" value="{{ (isset($from_date)) ? $from_date : $min_time }}" class="font-bold rounded-lg px-2 py-1 mx-1 border" />
      <input type="text" name="to_date" placeholder="To Date" value="{{ (isset($to_date)) ? $to_date : $max_time }}" class="font-bold rounded-lg px-2 py-1 mx-1 border" />
      <input type="submit" name="Filter" value="filter" class="rounded-lg bg-blue text-white px-2 py-1 mx-1 border" />
  </form>
</div>

    <div class="flex border-b-2 border-blue bg-grey-lighter text-sm">
      <div class="w-16 border-r whitespace-no-shrink px-1">AVG</div>
      <div class="w-16 border-r whitespace-no-shrink px-1">COUNT</div>
      <div class="w-16 border-r whitespace-no-shrink px-1">type</div>
      <div class="pl-1">URL</div>
    </div>

  @foreach($userlogs as $log)
    <div class="flex border-b {{ ($log->avgtime >= 2) ? 'text-red' : '' }} text-sm">
      <div class="w-16 border-r whitespace-no-shrink px-1">{{ round($log->avgtime,2) }}</div>
      <div class="w-16 border-r whitespace-no-shrink px-1">{{ round($log->thecount) }}</div>
      <div class="w-16 border-r whitespace-no-shrink px-1">{{ $log->type }}</div>
      <div class="pl-1">{{ substr($log->url,0,100) }}</div>
    </div>
  @endforeach


</div>



@endsection

@section('javascript')



@endsection