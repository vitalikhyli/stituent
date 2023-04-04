<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', "BillyGoat, the World's Most Stubborn Billing App")</title>

    <link rel="icon" type="image/png" href="/images/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="57x57" href="/images/favicon/favicon.ico">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">
   
    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="{{ asset('css/tailwind_local.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">

    <!-- Icons -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">  
   
      <script src="{{ asset('js/app.js') }}"></script>

  </head>
  <body>



<div class="p-4 text-center mb-4 font-bold text-2xl text-green-dark">Stituent ADMIN Drafts:</div>

<div class="ml-8">
<b>TERMINOLOGY?</b><br /><br />
<ul>
  <li class="mb-4"><b>Pie</b> - An enriched statewide voterfile created by Stituent, including "household" column and "JSON election history" column, etc.</li>
  <li class="mb-4"><b>Ingredients</b> - Basic voter tables from the state including election history tables.</li>
  <li class="mb-4"><b>To Bake</b> - To combine ingredients (voter data) into a complete source table (a Pie).</li>
  <li class="mb-4"><b>Slice</b> - A segment of the pie.
    </li>

      <ul>
      <li class="mb-4"><b>To Cut</b> - To create a smaller Slice subtable from a Pie table.</li>
      <li class="mb-4"><b>To Serve</b> - To copy a Slice table to a state-specific database and rename it for use by a specific user/account.</li>
      </ul>

</ul>
</div>

<div class="p-4 text-center mb-4">Screen 1:</div>


<div class="m-4 rounded-lg bg-green-lightest border border-green p-6">
<center><h2 class="text-green-dark">Dashboard</h2></center>
<h3 class="mb-4 mt-4 text-blue">Sources
<span class="text-red"> ("Pies")</span>
</h3>
<table class="w-full border">

  <tr class="bg-green-lighter">
    <td colspan="2" class="p-2">
      Source
    </td>
    <td class="p-2">
      Latest Consolidated Table
    </td>
    <td class="p-2">
      Records
    </td>
    <td class="p-2">
      Hash
    </td>
  </tr>


  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer">
    <td colspan="2" class="p-2">
      Massachusetts Voter File
    </td>
    <td class="p-2">
      <i class="fas fa-table"></i> __ex_26_mavoters_20190401
    </td>
    <td class="p-2">
      4,101,088
    </td>
    <td class="p-2">
      <?php echo substr(hash('sha1', '__ma_voters_20190401'),0,6); ?>
    </td>
  </tr>

  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer">
    <td colspan="2" class="p-2">
      Connecticut Voter File
    </td>
    <td class="p-2">
      <i class="fas fa-table"></i> __ex_25_ctvoters_20181230
    </td>
    <td class="p-2">
      3,401,068
    </td>
    <td class="p-2">
      <?php echo substr(hash('sha1', '__ct_voters_20190401'),0,6); ?>
    </td>
  </tr>

  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer">
    <td colspan="2" class="p-2">
      Rhode Island Voter File
    </td>
    <td class="p-2">
      <i class="fas fa-table"></i> __ex_24_rivoters_20181231
    </td>
    <td class="p-2">
      1,901,083
    </td>
    <td class="p-2">
      <?php echo substr(hash('sha1', '__ri_voters_20190401'),0,6); ?>
    </td>
  </tr>

  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer">
    <td colspan="2" class="p-2">
      University Student and Faculty Directory
    </td>
    <td class="p-2">
      <i class="fas fa-table"></i> __ex_23_university_20181231
    </td>
    <td class="p-2">
      31,009
    </td>
    <td class="p-2">
      <?php echo substr(hash('sha1', '__ri_universirt_20190401'),0,6); ?>
    </td>
  </tr>

<!-- 
  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer">
    <td class="w-8" class="p-2">
      <div class="ml-2 border-l border-b border-dashed border-black">&nbsp;</div>
      <div class="ml-2 border-l border-dashed border-black">&nbsp;</div>
    </td>
    <td class="p-2">
      <i class="fas fa-table"></i> General Election Participants 2016-11-08
    </td>
    <td class="p-2">
      __2_ma_participants_20161108
    </td>
    <td class="p-2">
      <?php echo substr(hash('sha1', '__ma_participants_20161108'),0,6); ?>
    </td>
  </tr>

  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer">
    <td class="w-8" class="p-2">
      <div class="ml-2 border-l border-b border-dashed border-black">&nbsp;</div>
      <div class="ml-2 border-l border-dashed border-black">&nbsp;</div>
    </td>
    <td class="p-2">
      <i class="fas fa-table"></i> Lynn Municipal Election Participants 2013-11-08
    </td>
    <td class="p-2">
      __3_lynn_20161108
    </td>
    <td class="p-2">
      <?php echo substr(hash('sha1', '__ma_participants_20161108'),0,6); ?>
    </td>
  </tr>

  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer">
    <td class="w-8" class="p-2">
      <div class="ml-2 border-l border-b border-dashed border-black">&nbsp;</div>
      <div class="ml-2">&nbsp;</div>
    </td>
    <td class="p-2">
      <i class="fas fa-table"></i> State Democrartic Primary 2012-09-06
    </td>
    <td class="p-2">
      __4_ma_participants_20120906
    </td>
    <td class="p-2">
      <?php echo substr(hash('sha1', '__ma_participants_20120906'),0,6); ?>
    </td>
  </tr>


  <tr class="border-t hover:bg-blue-darker hover:text-white cursor-pointer">
    <td colspan="2" class="p-2">
      <i class="fas fa-table"></i> Connecticut Voter File
    </td>
    <td class="p-2">
      __5_ct_voters_20190401 <?php echo substr(hash('sha1', '__ct_voters_20190401'),0,6); ?>
    </td>
    <td class="p-2">
      <?php echo substr(hash('sha1', '__ct_voters_20190401'),0,6); ?>
    </td>
  </tr>

  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer">
    <td class="w-8" class="p-2">
      <div class="ml-2 border-l border-b border-dashed border-black">&nbsp;</div>
      <div class="border-dashed border-black">&nbsp;</div>
    </td>
    <td class="p-2">
      <i class="fas fa-table"></i> General Election Participants 2016-11-08
    </td>
    <td class="p-2">
      __6_ct_participants_20161108
    </td>
    <td class="p-2">
      <?php echo substr(hash('sha1', '__ct_participants_20161108'),0,6); ?>
    </td>
  </tr> -->

</table>

</div>




<div class="p-4 text-center mb-4">Screen 2:</div>

<div class="m-4 rounded-lg bg-green-lightest border border-green p-6">
  <div class="float-right p-4 border rounded-lg border-green bg-green-lighter text-black cursor-pointer"><i class="far fa-edit"></i>Edit Basic Info</div>

<center><h2 class="text-green-dark">Upload Data for: <span class="text-green-darker">Massachusetts Voter File</span></h2></center>

<h3 class="mb-4 mt-4">
  <span class="text-blue">Principal Source</span>
  <span class="text-red"> ("Main Ingredient")</span>
</h3>

  <table class="border w-full border-green">
<!--     <tr>
      <td class="border-b border-r bg-green-lighter p-2 border-green">Export As:</td>
      <td class="border-b p-2 border-green">__ex_[id#]_<input type="text" value="mavoters" class="rounded border-black border p-2 text-lg w-32" />_[date]
      </td>
    </tr> -->
    <tr>
      <td class="border-b border-r bg-green-lighter p-2 border-green">Required Columns:</td>
      <td class="border-b p-2 border-green">VoterID, FirstName, LastName, City, State, Zip, MailingCity, MailingState, MailingZip, Gender, Party, Active, Ward, Precinct, etc.
      </td>
    </tr>
    <tr>
      <td class="border-b border-r bg-green-lighter p-2 border-green">Description:</td>
      <td class="border-b p-2 border-green">This is the source we update regularly to reflect the Massachusetts statewide voterfile.
      </td>
    </tr>
  </table>


<table class="w-full border mt-4">

  <tr class="bg-green-lighter">
    <td class="p-1">
      Uploaded On
    </td>
    <td class="p-1">
      Name
    </td>
    <td class="p-1">
      Import Tbl
    </td>
    <td class="p-1">
      Status
    </td>
    <td class="p-1">
      Hash
    </td>
  </tr>


  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer">
    <td class="p-1">
      4/1/19
    </td>
    <td class="p-1">
      Latest One!
    </td>
    <td class="p-1">
      <i class="fas fa-table"></i> 
      <?php $f = '__import_9'; echo $f; ?>
    </td>
    <td class="p-1">
      Uploaded/Verified &nbsp;
      <button class="bg-blue-dark rounded-lg text-white px-2">
        Use
      </button> 
      <button class="bg-grey-dark rounded-lg text-white px-2">
        Zip/Archive
      </button>
    </td>
    <td class="p-">
      <?php echo substr(hash('sha1', $f),0,6); ?>
    </td>
  </tr>

  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer">
    <td class="p-1">
      4/1/18
    </td>
    <td class="p-1">
      Annual Update
    </td>
    <td class="p-1">
      <i class="fas fa-table"></i> 
      <?php $f = '__import_8'; echo $f; ?>
    </td>
    <td class="p-1">
      Uploaded/Verified &nbsp;
      <button class="bg-red-dark px-2 rounded-lg text-white">
        Currently Using
      </button>
    </td>
    <td class="p-1">
      <?php echo substr(hash('sha1', $f),0,6); ?>
    </td>
  </tr>

  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer text-grey-dark">
    <td class="p-1">
      4/1/17
    </td>
    <td class="p-1">
      Found This Somewhere
    </td>
    <td class="p-1">
      <i class="fas fa-table"></i> 
      <?php $f = '__import_2'; echo $f; ?>
    </td>
    <td class="p-1">
      Zipped/Archived
    </td>
    <td class="p-1">
      <?php echo substr(hash('sha1', $f),0,6); ?>
    </td>
  </tr>

  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer text-grey-dark">
    <td class="p-1">
      4/1/16
    </td>
    <td class="p-1">
      Original voter file
    </td>
    <td class="p-1">
      <i class="fas fa-table"></i> 
      <?php $f = '__import_1'; echo $f; ?>
    </td>
    <td class="p-1">
      Zipped/Archived
    </td>
    <td class="p-1">
      <?php echo substr(hash('sha1', $f),0,6); ?>
    </td>
  </tr>

</table>

    <div class="p-2 text-center mt-2">
    Upload New Version: <input type="text" class="rounded-lg p-2 border border-green" value="Give it a Name" />
    <input type="submit" class="rounded-lg p-2 bg-green-light border border-green" value="Begin Upload Process" />
    </div>



<!-- <center>
<div class="w-2/3 font-bold text-xl text-center mt-12 rounded-lg pb-4 border-black">
  <i style="font-size:200%" class="fas fa-arrow-circle-down -mt-10"></i>
</div>
</center> -->

<h3 class="mb-4 mt-8">
<span class="text-blue">Related Source Tables</span> 
<span class="text-red">("Other Ingredients")</span>
</h3>

  <table class="border w-full border-green">
<!--     <tr>
      <td class="border-b border-r bg-green-lighter p-2 border-green">Export As:</td>
      <td class="border-b p-2 border-green">__ex_[id#]_<input type="text" value="mahistory" class="rounded border-black border p-2 text-lg w-32" />_[code]
      </td>
    </tr> -->
    <tr>
      <td class="border-b border-r bg-green-lighter p-2 border-green">Required Columns:</td>
      <td class="border-b p-2 border-green">ElectionCode, VoterID
      </td>
    </tr>
    <tr>
      <td class="border-b border-r bg-green-lighter p-2 border-green">Description:</td>
      <td class="border-b p-2 border-green">These are imports the system incorporates into the main source by placing the code into json_election_history if the voterID exists
      </td>
    </tr>
  </table>


<div class="mb-4"></div>
<table class="w-full border">

  <tr class="bg-green-lighter">
    <td class="p-2 w-4">
      Archive?
    </td>
    <td class="p-2">
      Uploaded
    </td>
    <td class="p-2">
      Name
    </td>
    <td class="p-2">
      Tbl
    </td>
    <td class="p-2">
      Code
    </td>
    <td class="p-2">
      Notes
    </td>
    <td class="p-2">
      Status
    </td>
    <td class="p-2">
      Hash
    </td>
  </tr>

  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer">
        <td class="p-2">
      <i class="fas fa-trash-alt"></i>
    </td>
    <td class="p-2">
      4/1/19
    </td>
    <td class="p-2">
      <i class="fas fa-sitemap"></i>&nbsp;
      <span class="truncate">
        Participants 2017-11-04 Ashburnham
      </span>
    </td>
    <td class="p-2">
      <i class="fas fa-table"></i>&nbsp;
      __import_80
    </td>
    <td class="p-2">
      M16
    </td>
    <td class="p-2">
      Ashburnham only
    </td>
    <td class="p-2 text-sm">
      Uploaded/Verified
    </td>
    <td class="p-2">
      <?php echo substr(hash('sha1', 'Data table information'),0,6); ?>
    </td>
  </tr>


  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer">
        <td class="p-2">
      <i class="fas fa-trash-alt"></i>
    </td>
    <td class="p-2">
      4/1/19
    </td>
    <td class="p-2">
      <i class="fas fa-sitemap"></i>&nbsp;
      <span class="truncate">
        Participants 2016-11-04 Statewide General
      </span>
    </td>
    <td class="p-2">
      <i class="fas fa-table"></i>&nbsp;
      __import_79
    </td>
    <td class="p-2">
      G16
    </td>
    <td class="p-2">
      
    </td>
        <td class="p-2 text-sm">
      Uploaded/Verified
    </td>
    <td class="p-2">
      <?php echo substr(hash('sha1', 'Data table information'),0,6); ?>
    </td>
  </tr>

  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer">
    <td class="p-2">
      <i class="fas fa-trash-alt"></i>
    </td>
        <td class="p-2">
      4/1/19
    </td>
    <td class="p-2">
      <i class="fas fa-sitemap"></i>&nbsp;
      <span class="truncate">
        Participants 2010-11-04 Statewide General
      </span>
    </td>
    <td class="p-2">
      <i class="fas fa-table"></i>&nbsp;
      __import_78
    </td>
    <td class="p-2">
      G10
    </td>
    <td class="p-2">
      
    </td>
        <td class="p-2 text-sm">
      Uploaded/Verified
    </td>
    <td class="p-2">
      <?php echo substr(hash('sha1', 'Data table information'),0,6); ?>
    </td>
  </tr>

    <tr class="hover:bg-blue-darker hover:text-white cursor-pointer text-grey-dark">
    <td class="p-2">
    </td>
        <td class="p-2">
      4/1/19
    </td>
    <td class="p-2">
      <i class="fas fa-sitemap"></i>&nbsp;
      <span class="truncate">
        Participants 2010-11-04 Statewide General
      </span>
    </td>
    <td class="p-2">
      <i class="fas fa-table"></i>&nbsp;
      __import_77
    </td>
    <td class="p-2">
      G10
    </td>
    <td class="p-2">
      Error, so re-uploading it
    </td>
        <td class="p-2 text-sm">
      Zipped/Archived
    </td>
    <td class="p-2">
      <?php echo substr(hash('sha1', 'Data table information'),0,6); ?>
    </td>
  </tr>

</table>

    <div class="p-2 text-center mt-2">
    Upload New Related Table: <input type="text" class="rounded-lg p-2 border border-green" value="Give it a Name" />
    <input type="submit" class="rounded-lg p-2 bg-green-light border border-green" value="Begin Upload Process" />
    </div>


</div>




<div class="p-4 text-center mb-4">Screen 3:</div>

<div class="m-4 rounded-lg bg-green-lightest border border-green p-6">
  <center><h2 class="text-green-dark">Generate Source</h2></center>


<h3 class="mb-4 mt-4">
<span class="text-blue">Current Source</span> 
<span class="text-red">("Curent Pie")</span>
</h3>


  <table class="border w-3/5 border-green">
    <tr>
      <td class="border-b border-r bg-green-lighter p-2 border-green">Source Name:</td>
      <td class="border-b p-2 border-green font-bold">Massachusetts Voter File
      </td>
    </tr>
    <tr>
      <td class="border-b border-r bg-green-lighter p-2 border-green">Table:</td>
      <td class="border-b p-2 border-green">__ex_26_mavoters_20190401
      </td>
    </tr>
    <tr>
      <td class="border-b border-r bg-green-lighter p-2 border-green">Tbl Built On:</td>
      <td class="border-b p-2 border-green">2019-04-01
      </td>
    </tr>
    <tr>
      <td class="border-b border-r bg-green-lighter p-2 border-green"># Rows</td>
      <td class="border-b p-2 border-green">4,101,088
      </td>
    </tr>
    <tr>
      <td class="border-b border-r bg-green-lighter p-2 border-green">Spot-Checked:</td>
      <td class="border-b p-2 border-green"><i class="fas fa-check-circle"></i> Verified (99% confidence at +/- 1%) 16,572 records confirmed - <a href="#">Log</a>
      </td>
    </tr>
</table>


<h3 class="mb-4 mt-12">
<span class="text-blue">Build Consolidated Source for Export</span>
<span class="text-red"> ("Bake New Pie")</span>
</h3>


  <table class="border border-green">
      <tr>
      <td class="border-b border-r bg-green-lighter p-2 border-green">Notes:</td>
      <td class="border-b p-2 border-green">In other words, use the above tables to programatically create the streamlined table formatted as we want and including election history in a JSON column, household concatenations, etc.
      </td>
    </tr>
    <tr>
      <td class="border-b border-r bg-green-lighter p-2 border-green">Save_As:</td>
      <td class="border-b p-2 border-green">__ex_[id#]_<input type="text" value="mavoters" class="rounded border-black border p-2 text-lg w-32" />_[date]
      </td>
    </tr>
  </table>

<center>
<div class="w-2/3 font-bold text-xl text-center mt-12 rounded-lg pb-4 border-black">
  <i style="font-size:200%" class="fas fa-arrow-circle-down -mt-10"></i><!-- <br /><br />
Current Consolidated Source Table: <span class="text-blue">__ex_26_mavoters_20190401</span>
  <br /> -->
  <br />
  <button class="bg-blue text-white px-4 py-2 rounded-lg mt-4">
    Create New From Latest Data
  </button>
</div>
</center>

</div>




<div class="p-4 text-center mb-4">Screen 4:</div>

<div class="m-4 rounded-lg bg-green-lightest border border-green p-6">
  <center><h2 class="text-green-dark">Generate SubSource Tables</h2></center>


<h3 class="mb-4 mt-12">
<span class="text-blue">SubSources of Consolidated Source File</span> 
<span class="text-red">("Cut Slices")</span>
</h3>

<div class="mb-4">Note: Accounts in different states are kept in different databases. The admin section is a different database from all states/accounts.</div>

<table class="w-full border">

  <tr class="bg-green-lighter">
    <td class="p-2" colspan="2">
      Name
    </td>
    <td class="p-2">
      To_DB
    </td>
    <td class="p-2">
      To_Account
    </td>
    <td class="p-2">
      SQL Select
    </td>
    <td class="p-2 text-sm border-l border-black">
      Saved Table
    </td>
    <td class="p-2 text-sm w-4 border-r border-black">
      ...on
    </td>
    <td class="p-2 text-sm w-4 text-center">
      Cut
    </td>
  </tr>


  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer">
    <td class="p-2">
      <i class="fas fa-chart-pie"></i>
    </td>
    <td class="p-2">
      <span class="truncate">
      Ashburnham Local
      </span>
    </td>
    <td class="p-2">
      MA
    </td>
    <td class="p-2">
      Selectman Morrison
    </td>
    <td class="p-2 text-blue">
      <i>Select * from [PIE] where city="Ashburnham"</i>
    </td>
    <td class="p-2 text-sm text-center border-r border-l border-black" colspan="2">
      <div>No Tbl Created</div>
    </td>
    <td class="p-2 text-sm text-center">
      <button class="bg-blue text-white px-2 py-1 rounded-lg whitespace-no-wrap">
        Cut
      </button>
    </td>
  </tr>

  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer">
    <td class="p-2">
      <i class="fas fa-chart-pie"></i>
    </td>
    <td class="p-2">
      <span class="truncate">
      Second Worcester
      </span>
    </td>
    <td class="p-2">
      MA
    </td>
    <td class="p-2">
      Rep. Slothe
    </td>
    <td class="p-2 text-blue">
      <i>Select * from [PIE] where housedist="2W"</i>
    </td>
    <td class="p-2 text-sm border-l border-black">
      <i class="fas fa-table"></i>&nbsp;
      __ex_982
    </td>
    <td class="p-2 text-sm text-center border-r border-black">
      1/1/18
    </td>
    <td class="p-2 text-sm text-center">
      <button class="bg-red-dark text-white px-2 py-1 rounded-lg whitespace-no-wrap">
        Recut
      </button>
    </td>
  </tr>

  <tr class="hover:bg-blue-darker hover:text-white cursor-pointer">
    <td class="p-2">
      <i class="fas fa-chart-pie"></i>
    </td>
    <td class="p-2">
      Worcester, Hampden, Hampshire and Middlesex
    </td>
    <td class="p-2">
      MA
    </td>
    <td class="p-2">
      Senator "The Streets"
    </td>
    <td class="p-2 text-blue">
      <i>Select * from [PIE] where sendist="WHHM"</i>
    </td>
    <td class="p-2 text-sm border-l border-black">
      <i class="fas fa-table"></i>&nbsp;
      __ex_981
    </td>
    <td class="p-2 text-sm border-r border-black text-center">
      1/1/18
    </td>
    <td class="p-2 text-sm text-center">
      <button class="bg-red-dark text-white px-2 py-1 rounded-lg whitespace-no-wrap">
        Recut
      </button>
    </td>
  </tr>

</table>


   <div class="p-2 text-center mt-2">
    Create New Sub Source <input type="text" class="rounded-lg p-2 border border-green" value="Give it a Name" />
    <input type="submit" class="rounded-lg p-2 bg-green-light border border-green" value="Begin" />
    


  <br /><br />
  Generate SubSources <span class="text-red font-bold">("Cut Slices")</span>
  <button class="bg-blue text-white px-4 py-2 rounded-lg mt-4">
    Only New Slices
  </button>
  <button class="bg-red-dark text-white px-4 py-2 rounded-lg mt-4">
    All Slices (This will re-cut slices)
  </button>
  using latest Pie

</div>

</div>




<div class="p-4 text-center mb-4">Screen 5:</div>


<div class="m-4 rounded-lg bg-green-lightest border border-green p-6">
<center><h2 class="text-green-dark">Put Into Production</h2></center>



<h3 class="mb-4 mt-8">
<span class="text-blue">Copy SubSources</span> 
<span class="text-red">("Serve Slices")</span>
</h3>


  <table class="border border-green w-full">
    <tr class="bg-green-lighter text-sm">
      <td class="border-b border-green p-2">Slice Name</td>
      <td class="border-b border-green p-2">#Rows</td>
      <td class="border-b border-green p-2">Spot Checked</td>
      <td class="border-b border-green p-2">To DB</td>
      <td class="border-b border-green p-2">To Account</td>
      <td class="border-b border-green p-2 whitespace-no-wrap">Cut Date</td>
      <td class="border-b border-green p-2">Table</td>
      <td class="border-b border-green p-2"><i class="fas fa-arrow-right"></i> Rename in DB:</td>
      <td class="border-b border-green p-2">Last Successful Serve to DB</td>
    </tr>

    <tr>
      <td class="border-b border-green p-2">Ashburnham Local</td>
      <td class="border-b border-green p-2">900</td>
      <td class="border-b border-green p-2"><i class="fas fa-check-circle">
        <a href="#">Log</a>
      </td>
      <td class="border-b border-green p-2">MA</td>
      <td class="border-b border-green p-2">Selectman Morrison</td>
      <td class="border-b border-green p-2 text-sm">4/1/19</td>
      <td class="border-b border-green p-2">__ex_983</td>
      <td class="border-b border-green p-2 whitespace-no-wrap"><i class="fas fa-arrow-right"></i> __acct_106_voters</td>
      <td class="border-b border-green p-2">
        <button class="bg-blue text-white px-2 py-1 rounded-lg">
         Serve Now
        </button>
      </td>
    </tr>

    <tr>
      <td class="border-b border-green p-2">Second Worcester</td>
      <td class="border-b border-green p-2">34,000</td>
      <td class="border-b border-green p-2"><i class="fas fa-check-circle">
        <a href="#">Log</a>
      </td>
      <td class="border-b border-green p-2">MA</td>
      <td class="border-b border-green p-2">Rep. Slothe</td>
      <td class="border-b border-green p-2 text-sm">4/1/19</td>
      <td class="border-b border-green p-2">__ex_982</td>
      <td class="border-b border-green p-2 whitespace-no-wrap"><i class="fas fa-arrow-right"></i> __acct_27_voters</td>
      <td class="border-b border-green p-2"><i class="fas fa-check-circle"></i> 2018-02-02</td>
    </tr>


    <tr>
      <td class="border-b border-green p-2">Worcester, Hampden, Hampshire and Middlesex</td>
      <td class="border-b border-green p-2">104,000</td>
      <td class="border-b border-green p-2 whitespace-no-wrap"><i class="fas fa-check-circle">
        <a href="#">Log</a>
      </td>
      <td class="border-b border-green p-2">MA</td>
      <td class="border-b border-green p-2">Senator "The Streets"</td>
      <td class="border-b border-green p-2 text-sm">4/1/19</td>
      <td class="border-b border-green p-2">__ex_981</td>
      <td class="border-b border-green p-2"><i class="fas fa-arrow-right"></i> __acct_99_voters</td>
      <td class="border-b border-green p-2"><i class="fas fa-check-circle"></i> 2018-02-02</td>
    </tr>

  </table>






    <br /><br />
  Generate &amp; Copy Over <span class="text-red font-bold">("Serve Slices")</span> to Production Database(s):

  <button class="bg-blue text-white px-4 py-2 rounded-lg mt-4">
    Only New Slices
  </button>

  <button class="bg-blue-dark text-white px-4 py-2 rounded-lg mt-4">
    All Slices
  </button>

  </div>

</div>




<!-- <hr />

<center>
<h1>Stituent Test Site</h1>
</center>
<br />

<div style="border:1px solid black;padding:10px;">
<span style="font-size:200%;">Bob Antil Campaign / Account Info Page</span>
  <div id="app">
    <ul>
      <li><b>Finances</b> - <i style="color:blue;">comes from BillyGoat's API</i>
        <ul>
          <li>Next bill scheduled: <b>@{{ json_next }}</b></li>
          <li>Outstanding balance: <b>$ @{{ json_outbal *.01  }}</b></li>
          <li>This account has paid: <b>$ @{{ json_total *.01 }}</b> total.</li>
        </ul>
      </li>
      <br />

        <li><b>Data</b> - <i>Comes from Stituent DB</i>
        <ul>
          <li>Voter Records - <b>12,000</b></li>
          <li>Constituent Records- <b>340</b></li>
        </ul>
      </li>

    </ul>
     
  </div>

</div> -->



<script type="text/javascript">
var app = new Vue({
    el: '#app',
    data: {
        json_next: null,
        json_outbal: null,
        json_total: null,
    }
});

bg_api_key = 'dk09fknr49s3fja14ff39a';

// $.getJSON('http://billygoat.test/api/'+api_key+'/client/3/nextbill', function (data) {
//     app.json = data;
// });

var nextBill, outBal;
$.when(
    $.getJSON('http://billygoat.test/api/'+bg_api_key+'/client/3/nextbill', function(data) {
        nextBill = data;
    }),
    $.getJSON('http://billygoat.test/api/'+bg_api_key+'/client/3/outbal', function(data) {
        outBal = data;
    }),
    $.getJSON('http://billygoat.test/api/'+bg_api_key+'/client/3/totalpaid', function(data) {
        totalPaid = data;
    }),
).then(function() {
      app.json_next = nextBill;
      app.json_outbal = outBal;
      app.json_total = totalPaid;
    // if (nextBill) {
    //     app.json = nextBill;
    // }
    // else {
    //     alert('nextbill failed');
    // }
    // if (outBal) {
    //     app.json2 = outBal;
    // }
    // else {
    //     alert('outbal failed');
    // }
    // if (totalPaid) {
    //     alert(totalPaid);
    //     app.json3 = totalPaid;
    // }
    // else {
    //     alert('totalpaid failed');
    // }
});



</script>




  </body>
</html>