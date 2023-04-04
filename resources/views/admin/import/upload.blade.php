@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')


	{!! Auth::user()->Breadcrumb('Import', 'import', 'level_1') !!}


@endsection

@section('main')


<div class="text-xl mb-4 border-b bg-orange-lightest p-2">
    Import Standard Voter File
</div>

<div class="pb-4 text-grey-darkest font-normal">
    Use this to upload mini files from town clerks, etc.
</div>


@if($step == 1)
    <div class="p-2 text-lg">
        <i class="fas fa-star"></i>
         Step 1 - Pick Team
    </div>

    <form class="form-horizontal" action="/admin/upload/step/2" method="post" name="uploadCSV" enctype="multipart/form-data" accept-charset="utf-8">
        @csrf
            <select name="team_id" class="font-normal">
                @foreach(\App\Team::all()->sortBy('name')->sortBy('account_id') as $theteam)
                    <option value="{{ $theteam->id }}" {{ ($theteam->id == 1) ? 'selected' : '' }}>
                        {{ $theteam->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" id="submit" name="import" class="bg-blue rounded-lg text-white px-4 py-2">Pick Team</button>
    </form>
@endif

@if($step == 2)
    <div class="p-2 text-lg text-grey">
        <i class="fas fa-star"></i>
        Step 1 - Pick Team <b>{{ \App\Team::find($team_id)->name }}</b>
    </div>

    <div class="p-2 text-lg">
        <i class="fas fa-star"></i>
        Step 2 - Upload file
    </div>

    <form class="form-horizontal" action="/admin/upload/step/3" method="post" name="uploadCSV" enctype="multipart/form-data">
        @csrf


            <input type="hidden" value="2" name="step" />

            <input type="hidden" value="{{ $team_id }}" name="team_id" />

            <input class="rounded-l-lg border p-2" type="file" name="file" id="file" accept=".csv, .txt">

            <input type="type" placeholder="Short name" id="shortname" name="import_name" class="rounded-lg border-2 font-normal p-2 m-2" />

            <button type="submit" id="submit" name="import" class="bg-blue text-white rounded-lg px-4 py-2">Upload</button>

    </form>
@endif 
 
@if($step == 3)
    <div class="p-2 text-lg text-grey-darkest">
        <i class="fas fa-star"></i>
        Step 1 - Pick Team <b>{{ \App\Team::find($team_id)->name }}</b>
    </div>

    <div class="p-2 text-lg text-grey-darkest">
        <i class="fas fa-star"></i>
        Step 2 - Upload file
    </div>

    <div class="font-normal text-grey-darkest">
    - Upload Name: {{ $import->name }} <br />
    - File Path: {{ $import->file_path }} <br />
    - File Hash: {{ $import->file_hash}} <br />
    - Detected Delimiter: <span class="bg-grey-lighter p-1 font-bold border border-black rounded-lg">{{ $import->delimiter }}</span><br />
    </div>


    <div class="p-2 text-lg">
        <i class="fas fa-star"></i>
        Step 3 - Headers and Delimiter
    </div>


    <form class="form-horizontal" action="/admin/upload/step/4" method="post" name="uploadCSV" enctype="multipart/form-data">
        @csrf

    <input type="hidden" value="{{ $team_id }}" name="team_id" />

    <input type="hidden" value="{{ $import->id }}" name="import_id" />




<div class="p-2 mt-2 text-right text-sm font-normal text-blue">
    OR -- Re-use Override Info From:
    <select name="reuse_extra">
        <option value="">--</option>
        @foreach($previous_extras as $previous))
            <option value="{{ $previous->id }}">{{ $previous->name }}</option>
        @endforeach
    </select>
</div>

    <table class="font-normal mt-4 border text-sm w-full">
        <tr class="border-b border-black">
            <td class="p-2 text-right bg-grey-lighter">
                Override Value
            </td>
            <td class="p-2 w-1/4 bg-grey-lighter">
                In this Column
            </td>

        </tr>

        <tr class="border-b">
            <td class="p-2 text-right">
                <input type="text" name="extra_address_city" class="w-1/3 uppercase rounded-lg border p-2" placeholder="Slothskatchewan" />
            </td>
            <td class="p-2 w-1/4">
                address_city
            </td>

        </tr>

        <tr class="border-b">
            <td class="p-2 text-right">
                <input type="text" name="extra_address_state" class="w-1/3 uppercase rounded-lg border p-2" placeholder="MA"/>
            </td>
            <td class="p-2 -1/4">
                address_state
            </td>

        </tr>

        <tr class="border-b">
            <td class="p-2 text-right">
                <input type="text" name="extra_state" class="w-1/3 uppercase rounded-lg border p-2" placeholder="MA"/>
            </td>
            <td class="p-2 w-1/4">
                state
            </td>

        </tr>
    </table>




<div class="p-2 mt-2 text-right text-sm font-normal text-blue">
    OR -- Re-use Header Info From:
    <select name="reuse_header">
        <option value="">--</option>
        @foreach($previous_headers as $previous))
            <option value="{{ $previous->id }}">{{ $previous->name }}</option>
        @endforeach
    </select>
</div>


<table class="font-normal mt-2 border text-sm w-full">
    <tr class="border-b border-black">
        <td class="p-2 text-right bg-grey-lighter">
            Line 1
        </td>
        <td class="p-2 text-right bg-grey-lighter">
            Line 2
        </td>
        <td class="p-2 text-right bg-grey-lighter">
            Line 3
        </td>
        <td class="p-2 text-left bg-grey-lighter">
           Guess
        </td>
        <td class="p-2 text-left bg-grey-lighter w-1/4">
            FluencyBase Column
        </td>

    </tr>

    <?php $r = 0; ?>

    @if(!$first_lines)
        Error -- No first lines
    @else
    @foreach($first_lines as $col)

        @if($col)
        <tr class="border-b {{ ($header[$r] == '{SKIP}') ? 'bg-grey-light' : '' }}">
            <td class="p-2 text-right">
                @if(!$col[0])
                    <span class="text-grey">null</span>
                @else
                    {{ $col[0] }}
                @endif
            </td>
            <td class="p-2 text-right">
                @if(!$col[1])
                    <span class="text-grey">null</span>
                @else
                    {{ $col[1] }}
                @endif
            </td>
            <td class="p-2 text-right">
                @if(!$col[2])
                    <span class="text-grey">null</span>
                @else
                    {{ $col[2] }}
                @endif
            </td>


             <td class="p-2 text-left text-red">
                @if(false)
                @if(($col == 'A') || ($col == 'A'))
                    Active
                @endif
                @if(($loop->first) && (is_numeric($col)))
                    Order
                @endif
                @if(
                    (preg_match('/[A-Za-z].*[0-9]|[0-9].*[A-Za-z]/', $col)) &&
                    ($loop->iteration <= 2)
                    )
                    ID (VoterID)
                @endif
                @if(($col == 'M') || ($col == 'F'))
                    Gender
                @endif
                @endif
            </td>

            
            <td class="p-2 text-left">

                <select name="header_{{ $loop->iteration -1 }}" class="w-full">

                @foreach($available_columns as $thecol)
                    <option {{ ($header[$r] == $thecol) ? 'selected' : '' }} value="{{ $thecol }}">{{ $thecol }}</option>
                @endforeach

                </select>
            </td>

        </tr>
        @endif
        <?php $r++; ?>
   @endforeach
   @endif

        <input type="hidden" name="total_headers" value="{{ count($first_lines)-1 }}" />
    </table>

        <div class="p-4 text-center border-b">
            <input type="checkbox" id="skip_first" name="skip_first" value="1" /> 
            <label for="skip_first" class="font-normal">
                Skip first row
            </label>

            <button type="submit" id="submit" name="import" class="bg-blue text-white rounded-lg px-4 py-2">Assign Headers and Import
            </button>
        </div>

    </form>

@endif












@if((isset($uploads)) && ($uploads->count() > 0))

    @if(!isset($team_id))
        <div class="text-xl mb-4 border-b bg-orange-lightest p-2 mt-6">
            All Uploads
        </div>
        <div class="text-center">
            <input id="accountInput" type="text" class="rounded-lg border p-4 text-lg w-1/2 mb-4 font-bold" placeholder="Type Here to Filter" />
        </div>
    @else
        <div class="text-xl mb-4 border-b bg-orange-lightest p-2 mt-6">
            Uploads for {{ \App\Team::find($team_id)->name }}
        </div>
    @endif



    <table class="font-normal w-full text-xs">

    <thead>
    <tr class="border-b border-black bg-grey-lighter">
        <td class="p-1">
            Folder
        </td>
        <td class="p-1">
            ID
        </td>
        <td class="p-1 {{ (isset($team_id)) ? 'hidden' : '' }}">
            Team
        </td>
        <td class="p-1">
            Name
        </td>
        <td class="p-1">
            Created
        </td>
        <td class="p-1">
            Slug
        </td>
        <td class="p-1">
            Count
        </td>
        <td class="p-1">
            File Path
        </td>
        <td class="p-1">
            Header
        </td>
        <td class="p-1">
            Override
        </td>
    </tr>
    </thead>

        <tbody id="accountTable">
        @foreach($uploads as $theimport)
        <tr class="border-b">
            <td class="p-2">
                {{ $theimport->data_folder_id }}
            </td>
            <td class="p-2">
                {{ $theimport->id }}
            </td>
            <td class="p-2 {{ (isset($team_id)) ? 'hidden' : '' }}">
                {{ \App\Team::find($theimport->team_id)->shortname }}
            </td>
            <td class="p-2 font-bold">
                {{ $theimport->name }}
            </td>
            <td class="p-2">
                {{ \Carbon\Carbon::parse($theimport->created_at)->format("n/j/y g:i") }}
            </td>

            <td class="p-2 {{ ($theimport->table_deploy) ? 'text-grey-dark' : 'text-blue' }}">
                    {{ $theimport->slug}}
            </td>

            <td class="p-2">
                {{ number_format($theimport->count,0,'.',',') }}
            </td>

            <td class="p-2 text-center whitespace-pre-wrap">
                
            </td>

            <td class="p-2 text-center break-words">
                {{ $theimport->header_columns}}
            </td>

            <td class="p-2 text-center break-words">
                {{ $theimport->extra_columns}}
            </td>

        </tr>
        @endforeach
        </tbody>

    </table>
@endif






@endsection

@section('javascript')
<script type="text/javascript">

    $(document).ready(function(){
      $("#accountInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#accountTable tr").filter(function() {
          $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
      });
    });


    // $('input[type=file]').val()

    $("input[type=file]").change(function(e){
        var fileName = e.target.files[0].name;
        $('#shortname').val(fileName);
    });


    $("select").change(function(){
        $("option:selected",this).text().trim().toLowerCase() == "{skip}" ? $(this).closest("tr").addClass("bg-grey-light") : $(this).closest("tr").removeClass("bg-grey-light");
    })

</script>
@endsection

