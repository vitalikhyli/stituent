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

        Stats

    </div>  

    
    <div class="w-full mb-4 pb-4 border-t">

        @php
            $date = \Carbon\Carbon::now()->format('Y-m-1');
        @endphp

        @for($i=0; $i <= 12; $i++)

            @include('admin.marketing.monthly-summary', 
                        [$summary = new App\Models\Admin\CandidateStats($date)]
                        )

            @php
                $date = \Carbon\Carbon::parse($date)->subMonths(1);
            @endphp

        @endfor
 

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

