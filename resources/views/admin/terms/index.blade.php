@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')

    Admin

@endsection


@section('style')

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.0/dist/Chart.min.js"></script>

@endsection

@section('main')


<div class="w-full mb-4 pb-4">

<!-- <div class="p-2">
    <a href="/tos">Terms of Service + Privacy Policy Draft</a><br />
    <a href="/privacy">Privacy Policy Draft</a><br />
</div>
 -->
       

    <div class="text-xl border-b-4 border-red py-2">

        <div class="float-right">
            <a href="/admin/terms/new">
                <button class="rounded-lg bg-blue-dark text-white px-2 py-1 text-sm border border-transparent">
                    Create New Terms
                </button>
            </a>
        </div>

        Terms of Service Manager
    </div> 

    

    <div class="table">

        @foreach($terms as $term)

            <div class="table-row w-full">

                <div class="p-2 border-b table-cell">
                    <a href="/admin/terms/{{ $term->id }}/edit">
                        <button class="rounded-lg bg-grey-lighter text-grey-darker border px-2 py-1 text-sm hover:text-white hover:bg-black">
                            Detail
                        </button>
                    </a>
                </div>

                <div class="p-2 border-b table-cell">

                    <div class="whitespace-no-wrap">
                        @if($term->publish)
                            <span class="text-grey-darker">Published / Viewable <i class="fas fa-check-circle text-blue"></i></span>
                        @else
                            <span class="text-grey-darker">Unpublished <i class="fas fa-times text-red"></i></span>
                        @endif
                    </div>

                    <div class="text-blue cursor-pointer">
                        {{ number_format($term->signers->count()) }} have signed
                    </div>

                </div>

                <div class="p-2 border-b table-cell">

                    <div class="text-grey-darker">
                        {{ Carbon\Carbon::parse($term->effective_at)->format('F n, Y') }}
                    </div>

                    <div class="font-medium">
                        {{ $term->title }}
                    </div>

                    <div class="mt-2 text-grey-dark text-sm">
                        @if(!$term->text)
                            No text
                        @else
                            {{ substr(strip_tags($term->text), 0, 400) }} ...
                        @endif
                    </div>

                </div>


            </div>


        @endforeach

    </div>

</div>

<br />
<br />

@endsection

@section('javascript')

@endsection

