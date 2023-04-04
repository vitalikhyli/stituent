@extends(Auth::user()->team->app_type.'.base')
<?php if (!defined('dir')) define('dir',Auth::user()->team->app_type); ?>

@section('title')
    {{ $group->name }}
@endsection

@section('breadcrumb')

  <a href="/{{ Auth::user()->team->app_type }}">Home</a>
  > <a href="/{{ Auth::user()->team->app_type }}/groups">Groups</a>
  > &nbsp;<b>{{ $group->name }}</b>

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

<div class="legislation text-2xl font-sans border-b-4 border pb-2">

  @if($group->cat->has_position)
      <div class="flex float-right text-base mt-1">

        <a class="mx-1" href="/{{dir}}/groups/{{ $group->id }}">
        <div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ (preg_match("/\/groups\/[0-9]+$/",request()->path())) ? 'bg-blue-darker text-white' : '' }}">
            All
        </div>
        </a>        

        <a class="mx-1" href="/{{dir}}/groups/{{ $group->id }}/position/supports">
        <div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ (strpos(request()->path(), 'supports') != false) ? 'bg-blue-darker text-white' : '' }}">
            Supports
        </div>
        </a>

        <a class="mx-1" href="/{{dir}}/groups/{{ $group->id }}/position/undecided">
        <div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ (strpos(request()->path(), 'undecided') != false) ? 'bg-blue-darker text-white' : '' }}">
            Undecided
        </div>
        </a>

        <a class="mx-1" href="/{{dir}}/groups/{{ $group->id }}/position/concerned">
        <div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ (strpos(request()->path(), 'concerned') != false) ? 'bg-blue-darker text-white' : '' }}">
            Concerned
        </div>
        </a>

        <a class="mx-1" href="/{{dir}}/groups/{{ $group->id }}/position/opposed">
        <div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ (strpos(request()->path(), 'opposed') != false) ? 'bg-blue-darker text-white' : '' }}">
            Opposed
        </div>
        </a>
      </div>
  @endif


  	<i class="fas fa-tag mr-2"></i>{{ $group->name }} <span class="text-grey-dark">({{ $people_total }} people)</span>

    @if(Auth::user()->permissions->developer)
      <a href="?livewire=true">
        <button class="rounded-lg bg-red text-white px-4 text-base py-2 mt-1 float-right">(Dev) New Livewire Mode</button>
      </a>
    @endif

<!--     <input type="text" id="filter-input" onkeyup="filterTable()" class="float-right text-base border-2 shadow mr-2 rounded-lg p-2 font-bold" placeholder="Filter People" /> -->

</div>

  <div class="py-2">

    @if($group->notes)
        <div class="border-b pb-2 mb-2 text-grey-dark">
            <span class="font-medium">Notes:</span> {{ $group->notes }}
        </div>
    @endif


    <div class="text-xl font-sans border-b flex">

        <div class="w-3/4">

            @if($group->files->first() )
                <div class="mt-1 text-base w-full">
                    @foreach($group->files as $thefile)

                        <div class="flex cursor-pointer border-grey-lighter rounded-lg py-1 w-full">

                            <div class="w-6 mr-8">
                                <a href="/{{ Auth::user()->team->app_type }}/files/{{ $thefile->id }}/edit/{{ base64_encode(request()->path()) }}">
                                    <button class="rounded-lg bg-grey-lighter text-xs text-black px-2 py-1">
                                        Edit
                                    </button>
                                </a>
                            </div>

                            <div class="w-2/3">
                                <a href="/{{ Auth::user()->team->app_type }}/files/{{ $thefile->id }}/download" class="text-grey-darkest" target="_new">
                                <i class="w-6 text-center far fa-file"></i>
                                {{ $thefile->name }}
                                </a>
                            </div>

                            <div class="w-1/5 text-sm text-grey-dark whitespace-no-wrap">
                                {{ $thefile->user->short_name }}
                            </div>

                            <div class="w-20 text-sm text-grey-darkest whitespace-no-wrap text-right px-2">
                                {{ \Carbon\Carbon::parse($thefile->created_at)->format("n/j/y") }}
                            </div>

                            <div class="w-20 text-sm text-grey-darkest whitespace-no-wrap text-right pr-3">
                                {{ \Carbon\Carbon::parse($thefile->created_at)->format("g:i a") }}
                            </div>

                         </div>
                    @endforeach
                </div>
            @else
                <span class="text-sm">This group has no files</span>
            @endif
        </div>

        <div class="text-center w-1/4 text-sm border-l">

            <a class="p-2 rounded-lg" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                <button class="mb-2 bg-blue hover:bg-blue-dark rounded-lg text-white mt-2 px-4 py-1">
                    Add Files
                </button>
            </a>

            <div class="collapse mt-4 p-4 rounded-lg bg-grey-lighter" id="collapseExample">

                <form id="file_upload_form" action="/{{ Auth::user()->team->app_type }}/files/upload/{{ base64_encode(json_encode(['group_id' => $group->id])) }}" method="post" enctype="multipart/form-data">
                  
                  @csrf

                    <div class="m-2">
                      
                      <input type="file" name="fileToUpload" id="fileToUpload" class="opacity-0 z-99 float-right absolute">

                      <label for="fileToUpload">
                        <button type="button" class="bg-orange-dark rounded-lg text-white px-4 py-2">
                          Choose File
                        </button>
                      </label>

                    </div>

                    <button name="submit" class="bg-blue rounded-lg text-white px-4 py-2">
                    Upload to Group<span id="file_selected" class="font-bold"></span><i class="fas fa-file-upload ml-4"></i>
                    </button>
                </form>


            </div>

        </div>

    </div>




    <div id="existing_checkboxes" class="mt-8 mb-2">

      <form method="post" action="/{{ Auth::user()->team->app_type }}/groups/{{ $group->id }}/sync">
        @csrf

        <div class="flex">

          <div id="addothers_form" class="text-sm w-1/2 mt-2 mr-1">
           
            <input id="cfbar" type="text" placeholder="Add People to this Group" data-toggle="dropdown" autocomplete="off" class="w-full text-black rounded-lg px-2 py-2 bg-grey-lightest border-2 border-grey focus:bg-white font-bold" />

            <div id="existing_checkboxes_interior">
            </div>

            <div id="addother_form_save" class="hidden py-2">
              <button class="bg-blue text-white rounded-lg text-sm px-2 py-1 text-base">
                Save People
              </button>
            </div>

          </div>

          <div id="addothers_form" class="text-sm w-1/2 mt-2 ml-1">
           
            <div class="flex">
                <input id="cfbar_notes" name="search_v" type="text" placeholder="Search People and Notes" data-toggle="dropdown" autocomplete="off" class="text-black rounded-lg px-2 py-2 bg-grey-lightest border-2 border-grey focus:bg-white font-bold w-full" value="{{ (isset($search_v)) ? $search_v : '' }}" />

                <button type="submit" formaction="/{{ Auth::user()->team->app_type }}/groups/{{ $group->id }}/searchinstance" class="bg-blue text-white rounded-lg text-sm px-2 py-1 ml-1 text-base">
                  Search
                </button>
            </div>

          </div>
        </div>



      </form>

    </div>

    <div id="list" class="hidden mt-1 absolute z-10 bg-white border-2 shadow-lg pb-4" style="width:600px;"></div> 

  </div>

  <div class="text-right w-full">

    
    <span class="w-32 text-sm pt-1">
      <a href="/{{ Auth::user()->team->app_type }}/groups/{{ $group->id }}/export"><i class="fas fa-file-csv mr-1"></i> Export Full CSV</a>
    </span>
   

    <span class="w-32 text-sm pt-1 pl-8">
      <a data-toggle="modal" data-target="#group-email-modal" href="#">
        <i class="fas fa-envelope mr-1"></i> 
        Group Emails ({{ $people->count() }})
      </a>
    </span>

  </div>
  @if($people_total <= 0)

    <!-- <div class="p-4">None</div> -->

  @else

      @if($people instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="text-right">
          {{ $people->links() }}
        </div>
      @endif

      <table id="the_table" class="mt-2 w-full text-sm">
          <tr class="bg-grey-lighter border-b">

            <td class="p-1 w-1/4">
              Name
            </td>

            @if($group->cat->has_title)
              <td class="p-1">
                Title
              </td>
            @endif


            @if($group->cat->has_position)
                <td class="p-1 w-1/4">
                  Position
                </td>
            @endif

           
            <td class="p-1 w-18">
              Notes
            </td>   
            

            @if(!isset($search_v))
   
              <td class="p-1">
                Full Address
              </td>

            @else

              <td class="p-1 w-1/2">
                Search Results
              </td>

            @endif

            <td class="p-1 w-10">
              Added
            </td>

          </tr>

        @foreach($people as $theperson)

          <tr class="group-div border-b cursor-pointer">


            @if(isset($search_v))
              <td class="group-div-name clickable p-1 pr-4 whitespace-no-wrap align-top" data-href="/{{dir}}/constituents/{{ $theperson->id }}">
                  {!! preg_replace("/".preg_quote($search_v)."/i", '<b class="bg-orange-lighter">$0</b>', $theperson->full_name) !!}
              </td>
            @else

              <td class="group-div-name clickable p-1 pr-4 whitespace-no-wrap" data-href="/{{dir}}/constituents/{{ $theperson->id }}">
                {{ $theperson->full_name }}
              </td>

            @endif

            @if($group->cat->has_title)
              <td class="p-1">
                {{ $theperson->group_title }}
              </td>
            @endif


            @if($group->cat->has_position)
                <td class="clickable p-1 pr-4 text-grey-darkest font-bold capitalize" data-href="/{{dir}}/constituents/{{ $theperson->id }}">
                  {{ $theperson->group_position }}
                </td>
            @endif

            
              <td class="instance-info p-1 pr-4 whitespace-no-wrap align-top" data-person_id="{{ $theperson->id }}" data-group_id="{{ $group->id }}">

                @if($theperson->groupPivot($group->id)->notes)
                  
                  <div class="edit_note py-1" data-pivot_id="{{ $theperson->groupPivot($group->id)->id }}">
                      <i class="fas fa-info-circle text-blue-dark hover:shadow"></i>
                  </div>

                @else

                  <div class="edit_note text-xs py-1 text-blue uppercase" data-pivot_id="{{ $theperson->groupPivot($group->id)->id }}">
                      Add
                  </div>

                @endif

              </td>


            @if(isset($search_v))

              <td class="group-div-name clickable p-1 pr-4" data-href="/{{dir}}/constituents/{{ $theperson->id }}">

                @if($theperson->group_notes)
                  {!! nl2br(preg_replace("/".preg_quote($search_v)."/i", '<b class="bg-orange-lighter">$0</b>', $theperson->group_notes)) !!}
                @else

                  <span class="text-grey">None</span>

                @endif

              </td>

            @else

              <td class="clickable p-1 pr-4 text-grey-darker text-sm overflow-x-hidden whitespace-no-wrap" data-href="/{{dir}}/constituents/{{ $theperson->id }}">
                {{ $theperson->full_address }}
              </td>

            @endif

            <td class="clickable p-1 pr-4 text-grey-darker text-sm overflow-x-hidden whitespace-no-wrap text-right" data-href="/{{dir}}/constituents/{{ $theperson->id }}">
              @if($theperson->user_who)
                <span class="text-grey-dark">{{ $theperson->user_who }}</span> - 
              @endif
                <span class="text-grey">
                  {{ \Carbon\Carbon::parse($theperson->user_when)->format("n/j/y") }}
                </span>
            </td>

          </tr>
        @endforeach

      </table>
  @endif


<!-- START MODAL -->

  <div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">

    <form method="POST" action="/{{ Auth::user()->team->app_type }}/groups/{{ $group->id }}/savenote">
    @csrf

        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">

              <div class="text-lg text-left">
                Edit Note
              </div>

              <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">

                <input type="hidden" name="modal_person_id" id="modal_person_id" />

                <input type="hidden" name="modal_group_id" id="modal_group_id" />

                <textarea name="modal_note_content" id="modal_note_content" rows="8" class="text-left p-2 text-base border w-full rounded-lg"></textarea>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
              <button type="submit" class="btn btn-primary">SAVE</button>
            </div>
          </div>
        </div>

    </form>
</div>

<!-- END MODAL -->

<!-- START MODAL -->

  <div class="modal fade" id="group-email-modal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">

          <div class="text-lg text-left font-bold">
            {{ $group->name }} - Emails
          </div>

          <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        </div>
        <div class="modal-body">

          <div id="" class="text-left py-2 text-base">
            @foreach ($people as $person)
              @if ($person->email)
                {{ $person->email }};
              @endif 
            @endforeach
          </div>

          <div class="text-red mt-4">
            Missing emails:
          </div>
          <div id="" class="text-left py-2 text-sm text-grey">
            @foreach ($people as $person)
              @if (!$person->email)
                {{ $person->name }},
              @endif 
            @endforeach
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">CLOSE</button>
      </div>
    </div>
  </div>
</div>


<!-- END MODAL -->


@endsection

@section('javascript')

<script type="text/javascript">

function filterTable()
{
  input         = document.getElementById('filter-input');
  filter_string = input.value.toUpperCase();
  rows          = document.getElementsByClassName('group-div');
  table         = document.getElementById("the_table");

  for (i = 1; i <= rows.length; i++) {

    full_name  = table.rows[i].cells[2].innerHTML.trim().toUpperCase();

    if (full_name.indexOf(filter_string) > -1) {

      table.rows[i].style.display = "";
      
    } else {

      table.rows[i].style.display = "none";

    }
  }

}


var ajax=null;

function getSearchData(v) {
  if (v == '') { $('#list').addClass('hidden'); }

  // Prevents an earlier request that takes longer from replacing subsequent ones
  if (ajax!=null) { ajax.abort(); }

  ajax = $.get('/{{ Auth::user()->team->app_type }}/groups/'+{!! $group->id !!}+'/searchpeople/'+v,function(response) {
      if (response == '') {
        $('#list').addClass('hidden');
      } else {
        $('#list').html(response);
        $('#list').removeClass('hidden');
      }
  });
  
}


$(document).ready(function() {

    $(".clickable").click(function() {
        window.location = $(this).data("href");
    });

    $(document).on("click", ".add-group-to-cat", function() {
        var id = $(this).attr('data-href');
        $("#"+id).removeClass('hidden');
    });

             
    // $(document).on("click", ".toggle-group", function() {
    //     var person_id = $(this).attr('data-person_id');
    //     var group_id = $(this).attr('data-group_id');
    //     var action = $(this).attr('data-action');
    //     var team_id = {!! Auth::user()->team->id !!};

    //     var url = '/{{dir}}/bulkgroups/'+action+'/'+group_id+'/'+person_id+'/'+team_id;
    //       $.get(url, function(response) {
    //           $('.toggle-group[data-person_id="'+person_id+'"]').replaceWith(response);
    //       }); 
    //   });

    // ================================================================================ //


      $("#cfbar").focusout(function(){
        window.setTimeout(function() {$('#list').addClass('hidden'); }, 300);
      });
      
      $("#cfbar").keyup(delay(function(){
        getSearchData(this.value);
      },500));

      $(document).on('click', ".clickable-select-person", function () {
          id = $(this).data("theid");
            
        $.get('/{{ Auth::user()->team->app_type }}/groups/'+{!! $group->id !!}+'/addperson/'+id, function(response) {
          if (response != '') {
            $('#existing_checkboxes_interior').append(response);
            $('#addother_form_save').removeClass('hidden');
            $("#cfbar").focus();
          }
        });

      });

    // ================================================================================ //
     
      $(document).on('click', ".instance-info", function () {

          group_id = $(this).data("group_id");
          person_id = $(this).data("person_id");

          $.get('/{{ Auth::user()->team->app_type }}/groups/'+group_id+'/person/'+person_id+'/getnotes', function(response) {

            if (response != '') {
              
              $('#modal_person_id').val(person_id);
              $('#modal_group_id').val(group_id);
              $('#modal_note_content').html(response);
              $('#noteModal').modal('show');

            }
            
          });

      });

     
  });
</script>

@endsection
