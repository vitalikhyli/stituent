@extends('admin.base')

@section('title')

    Admin Prospects

@endsection

@section('breadcrumb')



@endsection

@section('style')

    <!-- Alpine -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>

    <!-- New version Tailwind: -->
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet" />

      <style>
        [x-cloak] {
            display: none;
        }

        .duration-300 {
            transition-duration: 300ms;
        }

        .ease-in {
            transition-timing-function: cubic-bezier(0.4, 0, 1, 1);
        }

        .ease-out {
            transition-timing-function: cubic-bezier(0, 0, 0.2, 1);
        }

        .scale-90 {
            transform: scale(.9);
        }

        .scale-100 {
            transform: scale(1);
        }
    </style>

@endsection

@section('main')


<div class="w-full mb-4 pb-2" x-data="{ 'open': false }" @keydown.escape="open = false" x-cloak>


    <div class="text-xl border-b-4 border-red py-2 pb-3">

        <button class="rounded-lg bg-blue-dark float-right text-white px-4 py-2 text-sm"
                @click="open = true">
            Manual Add
        </button>

        @if(Auth::user()->permissions->developer)
<!--             <a href="/admin/marketing/commands/master">
                <button class="mr-2 rounded-lg bg-grey-dark float-right text-white px-4 py-2 text-sm">
                    <i class="fas fa-redo"></i> marketing:master
                </button>
            </a>

            <a href="/admin/marketing/commands/voters">
                <button class="mr-2 rounded-lg bg-grey-darker float-right text-white px-4 py-2 text-sm">
                    <i class="fas fa-redo"></i> marketing:voters
                </button>
            </a> -->
        @endif

        @if(isset($_GET['no_voter_ids']))
            Candidates with No Voter IDs
        @else
            Candidates
        @endif

        ({{ $candidates->count() }})

        @if($candidates->where('voter_id', null)->count() > 0)
            @if(!isset($_GET['no_voter_ids']))
                <a href="?no_voter_ids=1">
                    <button class="mr-2 rounded-lg bg-blue-darker float-right text-white px-4 py-2 text-sm">
                        Show Missing Voter IDs ({{ $candidates->where('voter_id', null)->count() }})
                    </button>
                </a>
            @else
                <a href="/admin/marketing">
                    <button class="mr-2 rounded-lg bg-blue-darker float-right text-white px-4 py-2 text-sm">
                        Show All Candidates
                    </button>
                </a>
            @endif
        @endif
        
    </div>  
    
    @php
        $colors = ['red', 'blue', 'pink', 'orange', 'green', 'indigo', 'teal'];
    @endphp

    @if(session()->has('linked'))
        <div class="p-4 bg-{{ $colors[rand(0,6)] }} text-white mb-2 shadow rounded-b-lg font-medium text-center font-mono">
            {{ session()->get('linked') }}
        </div>
    @endif
    
    <div class="w-full mb-4 pb-4 border-t">


        @include('admin.marketing.candidates-table', ['candidates' => $candidates])
 



    </div>

@include('admin.marketing.new-modal')

</div>






@endsection

@section('javascript')

<script type="text/javascript">
    
    $(document).ready(function() {


    });

</script>

@endsection

