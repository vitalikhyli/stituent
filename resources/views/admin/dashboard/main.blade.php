@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')

    Admin

@endsection


@section('style')

<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.0/dist/Chart.min.js"></script> -->

@endsection

@section('main')


<div class="w-full mb-4 pb-4 flex">

<!-- <div class="p-2">
    <a href="/tos">Terms of Service + Privacy Policy Draft</a><br />
    <a href="/privacy">Privacy Policy Draft</a><br />
</div>
 -->
       

   

    <div class="text-sm w-1/3 pr-8">

        <div class="">
            Twitter Count: {{ number_format(\App\Person::whereNotNull('social_twitter')->count()) }}
        </div>
        <div class="">
            Facebook Count: {{ number_format(\App\Person::whereNotNull('social_facebook')->count()) }}
        </div>

        <div class="text-lg mb-4 border-b-4 border-red pt-2 pb-1">
            Stats
        </div> 

        <div class="flex">
            <div class="uppercase p-1 w-1/2 text-grey-dark text-left border-b">
                Accounts
            </div>
            <div class="p-1 font-bold text-right border-b text-lg text-right flex-grow">
                {{ number_format(\App\Account::count()) }}
            </div>
        </div>

        <div class="flex">
            <div class="uppercase p-1 w-1/2 text-grey-dark text-left border-b">
                Teams from Accounts with BillyGoat IDs
            </div>
            <div class="p-1 font-bold text-right border-b text-lg text-right flex-grow">
                @php
                    $num_teams = \App\Team::whereIn('account_id', \App\Account::whereNotNull('billygoat_id')->pluck('id'))->count();
                @endphp
                {{ number_format($num_teams) }}
            </div>
        </div>

        <div class="flex">
            <div class="uppercase p-1 w-1/2 text-grey-dark text-left border-b">
                ...with Logos
            </div>
            <div class="p-1 font-bold text-right border-b text-lg text-right flex-grow">
                @php
                    $num_logos = \App\Team::whereNotNull('logo_img')->count();
                @endphp
                {{ number_format($num_logos) }}
                @if ($num_teams > 0)
                    <span class="text-blue text-base ml-2">{{ number_format($num_logos/$num_teams * 100) }}%</span>
                @endif
            </div>
        </div>

        <div class="flex">
            <div class="uppercase p-1 w-1/2 text-grey-dark text-left border-b">
                User
            </div>
            <div class="p-1 font-bold text-right border-b text-lg text-right flex-grow">
                {{ number_format(\App\User::count()) }}
            </div>
        </div>

        <div class="flex">
            <div class="uppercase p-1 w-1/2 text-grey-dark text-left border-b">
                Clicks Today
            </div>
            <div class="p-1 font-bold text-right border-b text-lg text-right flex-grow">
                {{ number_format(\App\UserLog::whereNull('type')
                                ->where('created_at', '>=', Carbon\Carbon::today())->count()) }}
                                            
            </div>
        </div>

        <div class="flex">
            <div class="uppercase p-1 w-1/2 text-grey-dark text-left  border-b">
                People
            </div>
            <div class="p-1 font-bold text-right border-b text-lg text-right flex-grow">
                @if(isset($_GET['all']))
                    @php
                        $num_people = \App\Person::count()
                    @endphp
                    {{ number_format($num_people) }}
                @else
                    <a href="?all=true" class="text-xs uppercase">Show all</a>
                @endif
            </div>
        </div>

        <div class="flex">
            <div class="uppercase p-1 w-1/2 text-grey-dark text-left  border-b">
                Unlinked People
            </div>
            <div class="p-1 font-bold text-right border-b text-lg text-right flex-grow">
                @if(isset($_GET['all']))
                    @php
                        $num_unlinked = \App\Person::whereNull('voter_id')->count();
                        $linked_percentage = $num_unlinked / $num_people *100;
                    @endphp
                    {{ number_format($num_unlinked) }}
                    <span class="text-blue text-base ml-2">{{ number_format($linked_percentage) }}%</span>
                @else
                    <a href="?all=true" class="text-xs uppercase">Show all</a>
                @endif
            </div>
        </div>

        <div class="flex">
            <div class="uppercase p-1 w-1/2 text-grey-dark text-left  border-b">
                Entities
            </div>
            <div class="p-1 font-bold text-right border-b text-lg text-right flex-grow">
                @if(isset($_GET['all']))
                    {{ number_format(\App\Entity::count()) }}
                @else
                    <a href="?all=true" class="text-xs uppercase">Show all</a>
                @endif
            </div>
        </div>

        <div class="flex">
            <div class="uppercase p-1 w-1/2 text-grey-dark text-left  border-b">
                Groups
            </div>
            <div class="p-1 font-bold text-right border-b text-lg text-right flex-grow">
                @if(isset($_GET['all']))
                    {{ number_format(\App\Group::count()) }}
                @else
                    <a href="?all=true" class="text-xs uppercase">Show all</a>
                @endif
            </div>
        </div>

        <div class="flex">
            <div class="uppercase p-1 w-1/2 text-grey-dark text-left  border-b">
                Cases
            </div>
            <div class="p-1 font-bold text-right border-b text-lg text-right flex-grow">
                @if(isset($_GET['all']))
                    {{ number_format(\App\WorkCase::count()) }}
                @else
                    <a href="?all=true" class="text-xs uppercase">Show all</a>
                @endif
            </div>
        </div>

        <div class="flex">
            <div class="uppercase p-1 w-1/2 text-grey-dark text-left  border-b">
                Contacts
            </div>
            <div class="p-1 font-bold text-right border-b text-lg text-right flex-grow">
                @if(isset($_GET['all']))
                    {{ number_format(\App\Contact::count()) }}
                @else
                    <a href="?all=true" class="text-xs uppercase">Show all</a>
                @endif
            </div>
        </div>

        <div class="flex">
            <div class="uppercase p-1 w-1/2 text-grey-dark text-left  border-b">
                Participants
            </div>
            <div class="p-1 font-bold text-right border-b text-lg text-right flex-grow">
                @if(isset($_GET['all']))
                    @php
                        $num_participants = \App\Participant::count()
                    @endphp
                    {{ number_format($num_participants) }}
                @else
                    <a href="?all=true" class="text-xs uppercase">Show all</a>
                @endif
            </div>
        </div>

        <div class="flex">
            <div class="uppercase p-1 w-1/2 text-grey-dark text-left  border-b">
                Unlinked Participants
            </div>
            <div class="p-1 font-bold text-right border-b text-lg text-right flex-grow">
                @if(isset($_GET['all']))
                    @php
                        $num_unlinked = \App\Participant::whereNull('voter_id')->count();
                        $linked_percentage = $num_unlinked / $num_participants *100;
                    @endphp
                    {{ number_format($num_unlinked) }}
                    <span class="text-blue text-base ml-2">{{ number_format($linked_percentage) }}%</span>
                @else
                    <a href="?all=true" class="text-xs uppercase">Show all</a>
                @endif
            </div>
        </div>

        <div class="flex">
            <div class="uppercase p-1 w-1/2 text-grey-dark text-left  border-b">
                Tags
            </div>
            <div class="p-1 font-bold text-right border-b text-lg text-right flex-grow">
                @if(isset($_GET['all']))
                    {{ number_format(\App\Tag::count()) }}
                @else
                    <a href="?all=true" class="text-xs uppercase">Show all</a>
                @endif
            </div>
        </div>

    </div>

<div class="w-2/3">
    <div class="text-lg border-b-4 border-red pt-2 pb-1">
        New Candidates
    </div> 

    @include('admin.marketing.candidates-table-simple', 
                ['candidates' => \App\Candidate::orderBy('organized_at', 'desc')->take(5)->get()
                ])


<!--     <div class="text-xl mb-4 border-b-4 border-red py-2 mt-8">
        User Permissions Check
    </div>  

    @if(isset($problems))
    <ol>
        @foreach($problems as $theproblem)
            <li>{!! $theproblem !!}</li>
        @endforeach
    </ol>
    @endif -->

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

