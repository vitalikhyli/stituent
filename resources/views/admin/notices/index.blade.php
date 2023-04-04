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
            <a href="/admin/notices/new">
                <button class="rounded-lg bg-blue-dark text-white px-2 py-1 text-sm border border-transparent">
                    Create New Notice
                </button>
            </a>
        </div>

        Notices
    </div> 

    <div class="flex mb-4 border-l border-b">
        
        @if(!isset($_GET['app_type']) || (!$_GET['app_type']))
            <div class="p-2 bg-black text-white border-r w-32 text-center uppercase text-sm">
                all
            </div>
        @else
            <a href="?app_type=">
                <div class="p-2 bg-grey-lightest border-r w-32 text-center uppercase text-sm">
                    all
                </div>
            </a>
        @endif

        @foreach(App\Team::all()->pluck('app_type')->unique()->toArray() as $app_type)
            
            
            @if(isset($_GET['app_type']) && ($_GET['app_type']) == $app_type)
                <div class="p-2 bg-black text-white border-r w-32 text-center uppercase text-sm">
                    {{ $app_type }}
                </div>
            @else
                <a href="?app_type={{ $app_type }}">
                    <div class="p-2 bg-grey-lightest border-r w-32 text-center uppercase text-sm">
                        {{ $app_type }}
                    </div>
                </a>
            @endif

        @endforeach
    </div>

    @foreach($notices as $notice)

        <div class="flex w-full mb-4">

            <div class="py-2 mr-4 text-sm w-1/5">
                <div class="">
                    <div class="font-bold pr-4">
                        Publish At:
                    </div>
                    <div class="text-grey-darker">
                        {{ Carbon\Carbon::parse($notice->publish_at)->format('F j, Y @ h:i A') }}
                    </div>

                    @if(!$notice->approved)
                        <div class="text-red font-bold text-lg">
                            Not Approved
                        </div>
                    @endif
                </div>

                 <div class="flex">
                    <div class="font-bold pr-4">
                        App_type
                    </div>
                    <div>
                        {{ $notice->app_type }}
                    </div>
                </div>

            </div>


            <div class="w-1/2">

                @include('elements.one-notice', ['notice' => $notice])

            </div>

            <div class="pl-4 w-14">

                <a href="/admin/notices/{{ $notice->id }}/edit">
                    <button class="rounded-lg bg-grey-lighter text-grey-darker border px-2 py-1 text-sm">
                        Edit
                    </button>
                </a>

            </div>

            <div class="pl-4 w-24">

                @if(!$notice->approved)
                    <a href="/admin/notices/{{ $notice->id }}/approve">
                        <button class="rounded-lg bg-grey-lighter text-grey-darker border px-2 py-1 text-sm">
                            Approve
                        </button>
                    </a>
                @else
                    <a href="/admin/notices/{{ $notice->id }}/unapprove">
                        <button class="rounded-lg bg-blue text-white px-2 py-1 text-sm border border-transparent">
                            Unapprove
                        </button>
                    </a>
                @endif

            </div>



            <div class="pl-4 w-12">

                @if(!$notice->archived_at)
                    <a href="/admin/notices/{{ $notice->id }}/archive">
                        <button class="rounded-lg bg-grey-lighter text-grey-darker border px-2 py-1 text-sm">
                            Archive
                        </button>
                    </a>
                @else
                    <a href="/admin/notices/{{ $notice->id }}/unarchive">
                        <button class="rounded-lg bg-red-dark text-white px-2 py-1 text-sm border border-transparent">
                            Unarchive
                        </button>
                    </a>
                @endif

            </div>

        </div>

    @endforeach

</div>



@if(false)
 <div class="text-xl mb-4 border-b bg-orange-lightest p-2">
            Stats
        </div>
        
        <table class="ml-8">
            <tr>
                <td class="">
                    Active Accounts
                </td>
                <td class="pl-2">
                    {{ number_format(\App\Account::count(),0,'.',',') }}
                </td>
            </tr>
            <tr>
                <td class="">
                    Active Teams
                </td>
                <td class="pl-2">
                    {{ number_format(\App\Team::count(),0,'.',',') }}
                </td>
            </tr>
            <tr>
                <td class="">
                    Active Users
                </td>
                <td class="pl-2">
                    {{ number_format(\App\User::count(),0,'.',',') }}
                </td>
            </tr>
            <tr>
                <td class="">
                    People
                </td>
                <td class="pl-2">
                     {{ number_format(\App\Person::count(),0,'.',',') }}
                </td>
            </tr>
            <tr>
                <td class="">
                    Voters
                </td>
                <td class="pl-2">
                    {{ number_format(\App\Voter::count(),0,'.',',') }}
                </td>
            </tr>
            <tr>
                <td class="">
                    Doors
                </td>
                <td class="pl-2">
                    {{ number_format(\App\VotingHousehold::count(),0,'.',',') }}
                </td>
            </tr>
        </table>
@endif

<br />
<br />

@endsection

@section('javascript')

@endsection

