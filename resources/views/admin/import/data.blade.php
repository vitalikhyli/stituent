@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')


	{!! Auth::user()->Breadcrumb('Import', 'import', 'level_1') !!}


@endsection

@section('style')

@endsection

@section('main')



<div class="text-xl mb-4 border-b bg-orange-lightest p-2 ">
    Data Tables
</div>

<div id="display">
    {!! $include !!}
</div>




@endsection



@section('javascript')


<script type="text/javascript">

    function getLatest() {
        $.get('/admin/data/list-tables', function(response) {
            $('#display').replaceWith(response);
            $('[data-toggle="tooltip"]').tooltip();
        }); 
    };

    function startWorker() {
        $.ajax({
            url: '/admin/data/startworker'
        });
    }

    function stopWorker() {
        $.ajax({
            url: '/admin/data/stopworker'
        });
    }

    $(document).ready(function() {

        // getLatest();

        setInterval(function(){ getLatest(); }, 5000);

        var downloadTimer = setInterval(function(){
          remaining = document.getElementById("untilReload").innerHTML.length;
          remaining -= 1;
          var newstring = '*'.repeat(remaining);
          if(remaining == 5){
            clearInterval(downloadTimer);
          }
          document.getElementById("untilReload").innerHTML = newstring;
        }, 1000);

        @if(session('startworker'))
            // alert('Starting worker...');
            startWorker();
            setTimeout(
                function() {
                    getLatest();
                }, 1000); //Allow worker to get set up before displaying
        @endif

        $(document).on("click", "#startworker", function() {
            startWorker();
            setTimeout(
                function() {
                    getLatest();
                }, 1000);
        });

        $(document).on("click", "#stopworker", function() {
            stopWorker();
            setTimeout(
                function() {
                    getLatest();
                }, 1000);
        });

    });

</script>

@endsection