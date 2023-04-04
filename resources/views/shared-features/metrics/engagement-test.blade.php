@extends('office.no-auth')


@section('title')
    @lang('Engagement TEST')
@endsection

@section('style')

   <script type="text/javascript">
      if (Number.MAX_SAFE_INTEGER === undefined) {
        Number.MAX_SAFE_INTEGER = 9007199254740991;
      }
      if (Number.MIN_SAFE_INTEGER === undefined) {
        Number.MIN_SAFE_INTEGER = -9007199254740991;
      }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.0/dist/Chart.min.js"></script>

@endsection

@section('breadcrumb')

    <a href="/office">Home</a>
    > Metrics > <b>Engagement</b>

@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue">
  <div class="text-2xl font-sans w-full">
    Metrics
  </div>


</div>

<canvas id="myChart" width="400" height="150" class="mt-4 border"></canvas>


    <div class="mt-8 pt-8">
    	<div class="text-center text-2xl font-bold">
    		How many @lang('constituents') has your office <span class="text-blue-dark">engaged with</span> over time?
    	</div>

    </div>

@endsection

@section('javascript')
<script type="text/javascript">
var ctx = document.getElementById('myChart').getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        datasets: [{
            label: '# of Votes',
            data: [12, 19, 3, 5, 2, 3],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        animation: {
            duration: 0
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero: true
                }
            }]
        }
    }
});

// window.onload = function() {

//     if (Number.MAX_SAFE_INTEGER === undefined) {
//         Number.MAX_SAFE_INTEGER = 9007199254740991;
//     }
//     if (Number.MIN_SAFE_INTEGER === undefined) {
//         Number.MIN_SAFE_INTEGER = -9007199254740991;
//     }
//     var ctx = document.getElementById('myChart').getContext('2d');
//     var myChart = new Chart(ctx, {
//         type: '{!! $type !!}',
//         data: {
//             datasets: [{
//                 label: '{!! (isset($label)) ? $label : '' !!}',
//                 data: {!! $data !!},
//                 backgroundColor: [
//                     'rgba(54, 162, 235, 0.2)'
//                 ],
//                 borderColor: [
//                     'rgba(255, 99, 132, 1)'
//                 ],
//                 borderWidth: 1
//             }
//             @if(isset($data_2))
//             ,{
//                 label: '{!! (isset($label_2)) ? $label_2 : '' !!}',
//                 data: {!! $data_2 !!},
//                 backgroundColor: [
//                     'rgba(255, 159, 64, 0.2)'
//                 ],
//                 borderColor: [
//                     'rgba(255, 159, 64, 1)'
//                 ],
//                 borderWidth: 1
//             }
//             @endif
//             ]
//         },
//         options: {
            // animation: {
            //     duration: 0
            // },
//             elements: {
//                 line: {
//                     tension: 0
//                     // stepped: true
//                 }
//             },
//             scales: {
//                 xAxes: [{
//                   type: 'time'
//                 }],
//                 yAxes: [{
//                     ticks: {
//                         beginAtZero: true
//                     }
//                 }]
//             }
//         }
//     });
// }
</script>

@endsection