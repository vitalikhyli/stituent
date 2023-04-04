@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')


@endsection

@section('style')

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.0/dist/Chart.min.js"></script>

@endsection

@section('main')


<div class="w-full mb-4 pb-2 ">



    <div class="text-xl mb-4 border-b-4 border-red py-2">
        Total Non-AJAX, Non-Livewire Clicks
    </div>  

@include('admin.userlogs.nav')


<!-- <div class="py-2">
  <form action="/admin/userlogs/dates" method="post">
    @csrf
    <input type="text" name="from_date" placeholder="From Date" value="{{ (isset($from_date)) ? $from_date : $min_time }}" class="font-bold rounded-lg px-2 py-1 mx-1 border" />
      <input type="text" name="to_date" placeholder="To Date" value="{{ (isset($to_date)) ? $to_date : $max_time }}" class="font-bold rounded-lg px-2 py-1 mx-1 border" />
      <input type="submit" name="Filter" value="filter" class="rounded-lg bg-blue text-white px-2 py-1 mx-1 border" />
  </form>
</div> -->


<canvas id="myChart" width="400" height="150" class="mt-4 mb-4"></canvas>

  <div class="table w-1/3">
    <div class="table-row border-b-2 border-blue bg-grey-lighter text-sm">
      <div class="table-cell border-r whitespace-no-wrap px-1">Date</div>
      <div class="table-cell border-r whitespace-no-wrap px-1">Day of Week</div>
      <div class="table-cell pl-1">Count</div>
    </div>

  @foreach($userlogs->reverse() as $log)
    <div class="table-row border-b text-sm {{ (\Carbon\Carbon::parse($log->day)->format('D') == 'Mon') ? 'bg-blue-lighter' : '' }}">
      <div class="table-cell border-r border-b whitespace-no-wrap px-1">{{ $log->day }}</div>
      <div class="table-cell border-r border-b whitespace-no-wrap px-1">{{ \Carbon\Carbon::parse($log->day)->format('D') }}</div>
      <div class="table-cell border-r border-b whitespace-no-wrap px-1 text-right">{{ number_format(round($log->thecount),0,'.',',') }}</div>
    </div>
  @endforeach

  </div>

</div>



@endsection

@section('javascript')
<script type="text/javascript">
var ctx = document.getElementById('myChart').getContext('2d');
var myChart = new Chart(ctx, {
    type: '{!! $graph_type !!}',
    data: {
        datasets: [{
            label: '{!! (isset($graph_label)) ? $graph_label : '' !!}',
            data: {!! $graph_data !!},
        }]
    },
    options: {
      elements: {
          // line: {
          //     // tension: 0,
          //     stepped: true
          // }
          rectangle: {
            borderWidth: 1,
            backgroundColor: [
                'rgba(0, 0, 255, 0.2)'
            ]
            // borderColor: [
            //     'rgba(255, 99, 132, 1)'
            // ],
          }
      },
        scales: {

            xAxes: [{
              type: 'time',
              time: {
                  unit: 'day'
              }
            }],

            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});
</script>
@endsection