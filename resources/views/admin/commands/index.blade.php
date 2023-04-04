@extends('admin.base')

@section('title')
    Commands
@endsection

@section('breadcrumb')


@endsection

@section('style')

@endsection

@section('main')


    <div class="text-xl border-b-4 border-red py-2">
        <span class="font-bold">Run Commands</span> as Defined in Controllers/Admin/CommandsController
    </div>  


    @if($running)

        <div class="py-2 border-b h-10">

            @if($reload)

                <span id="count">
                    5
                </span>

                <script type="text/javascript">

                    var x = setInterval(function() {
                        
                        var t = document.getElementById('count').innerHTML;

                        document.getElementById('count').innerHTML = t - 1;

                        if (t < 1) {
                            location.reload();
                        }

                    }, 1000);

                </script>

                <span class="float-right"><a href="?reload=">Stop Reloading: {{ $running }}</a></span>

            @else

                <span class="float-right"><a href="?reload=true">Start Auto Reloading: {{ $running }}</a></span>

            @endif

        </div>

    @endif


    <div class="py-4">

        <ul class="list-disc ml-6 font-mono">

            @foreach($commands as $command)

                <li class="hover:underline">

                    <a href="/admin/commands/{{ base64_encode($command) }}{{ ($reload) ? '?reload=true' : '' }}" class="text-blue">
                        {{ $command }}
                    </a>

                </li>

            @endforeach

        </ul>

    </div>


    @if($messages !== null)

        <div class="py-4">

            @foreach($messages as $message)

                <div class="px-4 py-6 border-4 border-blue mt-4 text-blue">

                    <span class="w-24 text-black font-bold">
                        {{ \Carbon\Carbon::now()->toDateTimeString() }} | 
                    </span>

                    {{ $message }}
                </div>

            @endforeach

        </div>

    @endif

    <div class="text-xl border-b-4 border-red py-2 mt-4">
        Counts
    </div> 

    <div class="py-4">

        <span class="w-24 text-grey font-bold">
            \App\ElectionProfile::whereNull('year_count')->count() : 
        </span>

        {{ number_format(\App\ElectionProfile::whereNull('year_count')->count()) }}

    </div>

@endsection



@section('javascript')


<script type="text/javascript">


</script>

@endsection