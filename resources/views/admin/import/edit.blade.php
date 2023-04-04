@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')


	{!! Auth::user()->Breadcrumb('Edit', 'edit') !!}


@endsection

@section('main')



<div class="text-xl mb-4 border-b bg-orange-lightest p-2">
    Edit Data Table
</div>





<form class="form-horizontal" action="/admin/import/{{ $theimport->id }}/save" method="post" name="uploadCSV" enctype="multipart/form-data">
    @csrf

<table class="w-full border-t font-normal">

<tr class="border-b">
    <td class="p-2 bg-grey-lighter w-48">
        Name
    </td>
    <td class="p-2">
        @if($theimport->archived)
            <div class="whitespace-no-wrap text-green p-2">
            <i class="fas fa-database"></i> This Table Has Been Archived
            </div>
        @endif
        <input type="text" class="font-bold w-3/4 rounded-lg border p-2" name="name" value="{{ $theimport->name }}" />
        <span class="font-bold ml-4">
            {{ \Carbon\Carbon::parse($theimport->created_at)->format("n.j.y g:i a") }}
        </span> 

    </td>
</tr>     

<tr class="border-b">
    <td class="p-2 bg-grey-lighter w-48">
        Notes
    </td>
    <td class="p-2">
        <input type="text" class="w-full rounded-lg border p-2" name="notes" value="{{ $theimport->notes }}" />
    </td>
</tr>     

<tr class="border-b-2 border-black">
    <td class="p-2 bg-grey-lighter w-48">
        Identifiers
    </td>
    <td class="p-2">
        {!! ($theimport->type == 'v') ? '<i class="fas fa-user-friends mr-2"></i>Voter Table' : '' !!} 
        {!! ($theimport->type == 'hh') ? '<i class="fas fa-home mr-2"></i>Households Table' : '' !!} 
        {!! ($theimport->type == 'e') ? '<i class="fas fa-person-booth mr-2"></i> Election Table' : '' !!} 
        - {{ $theimport->slug }}
        <div class="float-right">
            {{ number_format($theimport->relatedHouseholds()->count,0,'.',',') }} records
        </div>
    </td>
</tr>  

@if($theimport->slice_of_id)
<tr class="border-b">
    <td class="p-2 bg-grey-lighter">
        I am a slice of
    </td>
    <td class="p-2">
        <table class="w-full">
        <tr class="">
            <td class="p-2 px-2">
                <i class="fas fa-table ml-1"></i>
                    <a href="/admin/import/{{ $theimport->sliceOf()->id }}/edit" class="text-blue-dark">
                    {{$theimport->sliceOf()->name }}
                    </a>
            </td>
            <td class="p-2 px-4 w-4">
                <a href="/admin/import/{{ $theimport->sliceOf()->id }}/edit">
                <button type="button" class="px-4 py-2 rounded-full bg-blue text-white"><i class="fas fa-arrow-right"></i>
                </button>
                </a>
            </td>
        </tr>
        </table>
    </td>
</tr>    
<tr class="border-b">
    <td class="p-2 bg-grey-lighter">
        Slice SQL:
    </td>
    <td class="p-2">
        <textarea placeholder="district_house = 133 AND party = D" rows="4" class="w-full rounded-lg border p-2" name="slice_sql">{{ $theimport->slice_sql }}</textarea>
    </td>
</tr>    
@endif

@if($parent)
<tr class="border-b-2 border-t-2 border-black">
    <td class="p-2 bg-grey-lighter">
        Parent table
    </td>
    <td class="pr-2">
        <table class="w-full">
        <tr class="">
            <td class="p-2 px-4">
                I was created 

                as a copy of <i class="fas fa-table ml-1"></i>
                    <a href="/admin/import/{{ $parent->id }}/edit" class="text-blue-dark">
                    {{$parent->name }}
                    </a>
            </td>

            <td class="p-2 px-4 w-4">
                <a href="/admin/import/{{ $parent->id }}/edit">
                <button type="button" class="px-4 py-2 rounded-full bg-blue text-white"><i class="fas fa-arrow-right"></i>
                </button>
                </a>
            </td>
        </tr>
        </table>
    </td>
</tr>  
@endif 


@if($copies->count() > 0)
<tr class="border-b-2 border-t-2 border-black">
    <td class="p-2 bg-grey-lighter">
        Offspring tables
    </td>
    <td class="pr-2">
        <table class="w-full">
        @foreach($copies as $thecopy)

        <tr class="border-b">
            <td class="p-2 px-4">
                <i class="fas fa-table ml-1"></i>
                    <a href="/admin/import/{{ $thecopy->id }}/edit" class="text-blue-dark">
                    {{$thecopy->name }}
                    </a>
            </td>
            <td class="p-2 px-4 text-sm">
                    created from me on

                <span class="text-blue-dark">
                    {{ \Carbon\Carbon::parse($thecopy->created_at)->format("n/j/y @ g:i a") }}
                </span>

            </td>

            <td class="p-2 px-4 w-4">
                <a href="/admin/import/{{ $thecopy->id }}/edit">
                <button type="button" class="px-4 py-2 rounded-full bg-blue text-white"><i class="fas fa-arrow-right"></i>
                </button>
                </a>
            </td>
        </tr>
        
        @endforeach
        </table>

    </td>
</tr>  
@endif

@if(isset($slices_of_parent))
<tr class="">
    <td class="p-2 bg-grey-lighter">
        Pointer check
    </td>
    <td class="">
        <div class="mb-2 p-2 bg-orange-lightest border-b">
        The parent table I am based on has slices <b>{{ $slices_of_parent->count() }}</b> based on it.
        

        @if($slices_of_parent->count() > 0)
        <a href="/admin/import/{{ $theimport->id }}/moveSlicePointers">
        <button type="button" class="text-sm px-4 py-2 rounded-full bg-blue text-white m-2">
            Move Slice Pointers to Me
        </button>
        </a>
        @else
<!--         <a href="/admin/import/{{ $theimport->parent_id }}/moveSlicePointers">
        <button type="button" class="text-sm px-4 py-2 rounded-full bg-blue text-white m-2">
            Move My Slice Pointers to Back to Parent
        </button>
        </a>    -->  
        @endif

        </div>
    </td>
</tr>
@endif



<tr class="border-b-2 border-black">
    <td class="p-2 bg-grey-lighter">
        Slices based on me
    </td>
    <td class="pr-2">

        <table class="w-full">
        @if(!$theimport->archived)
        <tr class="border-b bg-orange-lightest">
            <td class="p-2 px-4" valign="top">
            
            <div class="p-1 pb-2">
                <select name="new_slice_team_id" class="font-normal border">
                    @foreach(\App\Team::all()->sortBy('name')->sortBy('account_id') as $theteam)
                        <option value="{{ $theteam->id }}" {{ ($theimport->team_id == $theteam->id) ? 'selected' : '' }}>
                            {{ $theteam->name }}
                        </option>
                    @endforeach
                </select>
            </div>
                <input type="text" class="w-full rounded-lg border p-2" name="new_slice_name" placeholder="New Slice Name" />
                 
            </td>
            <td class="p-2 px-4 text-sm" colspan="2">
                <textarea placeholder="WHERE SQL" rows="3" class="w-full rounded-lg border p-2" name="new_slice_sql"></textarea>
            </td>
            <td class="p-2 px-4 w-4">
                <a href="/admin/import/{{ $theimport->id }}/addslice">
                <button type="submit" class="px-4 py-2 rounded-full bg-blue text-white whitespace-no-wrap">
                <i class="fas fa-pizza-slice mr-2"></i> New</button>
                </a>
            </td>
        </tr>
        @endif
        </table>


        <div id="display">
            {!! $include !!}
        </div>


    </td>
</tr>  
</table>


<input type="hidden" value="{{ $theimport->team_id }}" name="team_id" />

<button formaction="/admin/import/{{ $theimport->id }}/save/close" type="submit" id="submit" name="import" class="px-4 py-2 rounded-full bg-blue text-white m-4">Save and Close
</button>

<button type="submit" id="submit" name="import" class="px-4 py-2 rounded-full bg-blue text-white m-4">Save
</button>

</form>

<table class="w-full border-t-2 border-black">
 <tr class="border-b">
    <td class="p-2 bg-grey-lighter">
        Merge This
    </td>
    <td class="p-2">

        @if($into_mergeable->count() <= 0)

            <span class="text-grey-dark">(There are no other undeployed, unarchived tables in this team.)</span>

        @else
            <form action="/admin/import/merge/" method="post">

            @csrf

            Merge {{ $theimport->slug }} - "{{ $theimport->name }}" into &rarr; 

            <input type="hidden" name="merge_this_id" value="{{ $theimport->id }}" />
            <input type="hidden" name="team_id" value="{{ $theimport->team_id }}" />

            <select name="merge_into_id" class="border text-lg">
                <option value="">--</option>
                @foreach($into_mergeable as $thetable)
                    <option value="{{ $thetable->id }}">{{ $thetable->slug }} - {{ $thetable->name }}</option>
                @endforeach
            </select>
            <button type="submit" id="submit" name="import" class="px-4 py-2 rounded-full bg-blue text-white m-4">Merge
            </button>

            </form>
        @endif

    </td>
</tr>  

 <tr class="border-b-2 border-black">
    <td class="p-2 bg-grey-lighter">
        Merge Reports
    </td>
    <td class="p-2">
        @if(!$merged_history)
            None
        @else
        <table class="w-full text-sm font-normal">
             <tr class="bg-grey-lighter font-bold">
                <td class="font-bold p-2">
                    ID
                </td>
                <td class="p-2">
                    Previous Values
                </td>
            </tr>           
            @foreach($merged_history as $therecord)

                <tr class="border-b">
                    <td class="font-bold p-2 align-top w-1/4">
                        {{ $therecord->full_name }}
                    </td>
                    <td class="p-2 align-top">
                       {{ $therecord->merge_report }}
                    </td>
                </tr>
            @endforeach
        </table>
        @endif
    </td>
 </tr>

</table>





@endsection


@section('javascript')


<script type="text/javascript">

    function getLatest() {
        $.get('/admin/data/'+{!! $theimport->id !!}+'/list-slices', function(response) {
            $('#display').replaceWith(response);
        }); 
    };

    function startWorker() {
        $.ajax({
            url: '/admin/data/startworker'
        });
    }

    $(document).ready(function() {

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
            startWorker();
            setTimeout(
                function() {
                    getLatest();
                }, 1000); //Allow worker to get set up before displaying
        @endif

        // $('#startworker').click(function() {
        $(document).on("click", "#startworker", function() {
            startWorker();
            getLatest();
        });

    });

</script>

@endsection