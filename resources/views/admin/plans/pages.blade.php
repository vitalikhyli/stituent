@extends('admin.base')


@section('title')
    Pages Checklist
@endsection

@section('breadcrumb')
    
    {!! Auth::user()->Breadcrumb('Checklist', 'checklist_index', 'level_1') !!}

@endsection

@section('style')

    <style>


    </style>

@endsection

@section('main')

<?php
    function drawBar($w) {
        if ($w == 0) { return ''; }
        return '<div class="w-full flex"><div style="width:'.$w.'%;"class="graphbar-horizontal bg-blue rounded-lg text-white text-xs text-right pr-1">'.$w.'</div></div>';
    }

    $app = 'office';
    $ok = '<i class="fa fa-check text-green"></i>';
    $need = '<i class="fa fa-times text-red"></i>';
?>

 

    <div class="text-2xl font-sans">
        Status of every page in Capital Connections
    </div>

    <table class="table mt-8 text-center">
        <tr>
            <th>Page</th>
            <th class="text-center">Our Url</th>
            <th class="text-center">Layout Draft</th>
            <th class="text-center">Faker Data</th>
            <th class="text-center">Real Data</th>
            <th class="text-center">~ % Actually Works</th>
        </tr>

        <tr>
            <td class="text-left">Dashboard</td>
            <td class="text-sm text-left"><a href="{{$app}}">/{app}</a></td>
            <td>{!! $ok !!}</td>
            <td>{!! $ok !!}</td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(25) !!}</td>
        </tr>

        <tr class="border-t-2 border-black">
            <td class="text-left">Admin - Set Working Campaign</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Admin - Campaign Setup</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Admin - User Setup</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

<!--         <tr class="bg-red text-white hidden">
            <td class="text-left">URGENT ---- MAKE WILLY PHOTOSHOP</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr> -->

        <tr>
            <td class="text-left">Admin - Custom Logo Upload</td>
            <td class="text-sm text-left"><a href="/{{$app}}/settings">/{app}/settings</a></td>
            <td>{!! $ok !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(90) !!}</td>
        </tr>

        <tr class="border-t-2 border-black">
            <td class="text-left">Constituents - My Constituents</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Constituents - Data Exchange Constituents</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Constituents - Master Mail List</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Constituents - Sure Vote Constituents</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Constituents - Merge Constituent Data</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Constituent Contact - Open Case Files</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Constituent Contact - Call Back Schedule</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Constituent Contact - Recent Contact</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr class="border-t-2 border-black">
            <td class="text-left">Groupings - Constituent Groups</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Groupings - Constituent Issue Groups</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Groupings - Group By Street</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

<!--         <tr class="bg-red text-white hidden">
            <td class="text-left">URGENT ---- FIND AND CHASE GOOSE</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr> -->

        <tr>
            <td class="text-left">Groupings - Issue Group Archives</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Groupings - Issue Group Archival</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr class="border-t-2 border-black">
            <td class="text-left">Bulk Email - Bulk Emailer</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Bulk Email - Bulk Email Templates</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Bulk Email - Bulk Email Tracker History</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Bulk Email - Bulk Email Job Queue</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr class="border-t-2 border-black">
            <td class="text-left">Misc - Events</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Misc - Enter Call Results</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Misc - Web Form Builder</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Misc - Match CSV Data File with Voter Codes</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr class="border-t-2 border-black">
            <td class="text-left">Export - Constituent Groups</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Export - Issue Groups</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>
        <tr>
            <td class="text-left">Export - Constituent Export</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Export - Master Mail List</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Export - Email List</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Export - Voting Status</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Export - Constituents by Election</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Export - Constituents by Single Election</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Export - Event Data Export</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Export - Mail List & Donation Summary Export</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

<!--         <tr class="bg-red text-white hidden">
            <td class="text-left">URGENT ---- LORD GUVNA LYRICS, 6 VERSES</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr> -->

        <tr class="border-t-2 border-black">
            <td class="text-left">Reports - Case File Report</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Reports - Issue Group Report</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Reports - Constituents by Street</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Reports - Constituents by Street w/Bar Code</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Reports - Constituents by Ward/Precinct</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Reports - Constituents by Ward/Precinct w/Bar Code</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Reports - Polling List Report</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Reports - Volunteer Report</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Reports - Donation Report</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr>
            <td class="text-left">Reports - Event Report</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr class="border-t-2 border-black">
            <td class="text-left">Import - Import Voting Status</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

        <tr class="border-t-2 border-black">
            <td class="text-left">OCPF</td>
            <td class="text-sm text-left"><a href=""></a></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! $need !!}</i></td>
            <td>{!! drawBar(0) !!}</td>
        </tr>

    </table>

@endsection


 <!-- <div class="text-2xl font-sans">
        Ideas For Transfer from Capital Connections:
    </div>
    <ol class="mb-4 leading-loose">
        <li>Capital Connections accounts table: date_transfer, transfer_complete</li>
        <li>3 accounts transfer / day x 5 days/week x 3 weeks = approx 45 clients (not sure how many?)</li>
       <li>3/day Reason= Fluency team able to respond to problems, provide customer support and guidance about totally new interface</li>
       <li>System emails users in advance to expect changeover: 30 days, 14, 7, 3, 1</li>
       <li>Start with clients known to be the least horrible</li>
       <li>Transfer program runs early morning</li>
       <li>When transfer_complete == 1, suspend Capital Connections account, login page redirects to FluencyBase.com</li>
       <li>On login, users agree new TOS</li>
    </ol>

    <ol class="mb-4 line-through text-grey">
        <li>How does Stitch-Chat change in campaign mode? Different channels for campaign work? Should limit users? 
            <div class="font-bold">Yeah, rooms should be flagged with campaign or regular (possible exception for @direct rooms?)</div></li>
        <li>Perhaps Call Log should be off in campaign mode. 
            <div class="font-bold">I think this actually should still be there, but again, I think we need a campaign boolean on each note</div></li>
        <li>Must differentiate between Campaign Email and Public Email (bulk). 
            <div class="font-bold">Agreed</div></li>
        <li>Therefore must flag the email source to differentiate.</li>
        <li>What is a polling list?
            <div class="font-bold">As best I can tell it is for breaking down voters by ward and precinct, but it doesnt seem to work</div></li>
        </li>
    </ol>

     -->
<!--

    Home

Admin
    Set Working Campaign
    Campaign Setup
    User Setup
    Custom Logo Upload

Constituents
    My Constituents
    Data Exchange Constituents
    Master Mail List
    Sure Vote Constituents
    Merge Constituent Data

Constituent Contact
    Open Case Files
    Call Back Schedule
    Recent Contact

Groupings
    Constituent Groups
    Constituent Issue Groups
    Group By Street
    Issue Group Archives
    Issue Group Archival

Bulk Email
    Bulk Emailer
    Bulk Email Templates
    Bulk Email Tracker History
    Bulk Email Job Queue

Misc
    Events
    Enter Call Results
    Web Form Builder
    Match CSV Data File with Voter Codes

Export
    Constituent Groups
    Issue Groups
    Constituent Export
    Master Mail List
    Email List
    Voting Status
    Constituents by Election
    Constituents by Single Election
    Event Data Export
    Mail List & Donation Summary Export

Reports
    Case File Report
    Issue Group Report
    Constituents by Street
    Constituents by Street w/Bar Code
    Constituents by Ward/Precinct
    Constituents by Ward/Precinct w/Bar Code
    Polling List Report
    Volunteer Report
    Donation Report
    Event Report

Import
    Import Voting Status

    OCPF

-->


