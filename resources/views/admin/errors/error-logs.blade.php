@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')


@endsection

@section('style')

<!--     <link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"> 
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
 -->
@endsection

@section('main')


<div class="w-full mb-4 pb-2 ">




<!-- <div class="text-center">
<input id="accountInput" type="text" class="rounded-lg border p-4 text-lg w-1/2 mb-4 font-bold" placeholder="Type Here to Filter" />
</div>
 -->
<?php $last_day = null; ?>

    @foreach($lines as $key => $thedate)

    @if(\Carbon\Carbon::parse($key)->toDateString() != $last_day)
        <div class="text-xl my-2 border-b bg-grey-darkest text-white p-2">
            {{ \Carbon\Carbon::parse($key)->format("l F d, Y") }}
        </div>
        <?php $last_day = Carbon\Carbon::parse($key)->toDateString(); ?>
    @endif

      <div class="border-b ml-4">
        @foreach($thedate as $k => $theline)

            @if(substr($theline,0,11) == 'QUERY TIMER')

                @continue

                <div class="p-2 border-b bg-blue-dark text-white border-2 shadow rounded-lg border-grey-darker mt-2 whitespace-break-words">
                    {{ $theline }}
                </div>
                
            @endif

            @if($loop->first)
              <div class="p-2 border-b bg-red-lightest border-2 shadow rounded-lg border-grey-darker mt-2">
                {{ substr($theline,0,250) }}...
                <button class="toggleShow rounded-lg text-white bg-blue px-2 py-1 shadow float-right" data-key="{{ str_replace(':', '-', str_replace(' ', '_', $key)) }}">
                    Show
                </button>
              </div>

                <div id="{{ str_replace(':', '-', str_replace(' ', '_', $key)) }}" class="hidden ml-8">

                  <div class="p-2 border-b">
                    {{ $theline,0,250 }}
                  </div>
            @else
                @if($loop->iteration == 2)

                @endif

                      <div class="p-2 border-b pl-8r">
                        {{ substr($theline,0,250) }}...
                      </div>

                @if($loop->last)
                    </div>
                @endif
            @endif
        @endforeach
      </div>
    @endforeach


</div>



@endsection

@section('javascript')

<script>
$(document).ready(function(){

    $(".toggleShow").click(function(){
        $("#"+$(this).data('key')).toggleClass('hidden');
    });

  $("#accountInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#accountTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

// $(document).ready( function () {
//     $('#accountTable').DataTable();
// } );

</script>

@endsection