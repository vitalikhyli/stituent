@extends(Auth::user()->team->app_type.'.base')

@section('title')
    {{ $file->name }}
@endsection

@section('breadcrumb')

 <a href="/{{ Auth::user()->team->app_type }}">Home</a>
  > <a href="/{{ Auth::user()->team->app_type }}/files">Files</a>
  > &nbsp;<b>Edit File</b>

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')


<div class="float-right text-sm">
    <button type="button" data-toggle="modal" data-target="#deleteModal" id="delete" class="rounded-lg px-4 py-2 bg-white hover:bg-red hover:text-white text-red text-center ml-2"/>
      <i class="fas fa-exclamation-triangle mr-2"></i> Delete File From System
    </button>
</div>



<div class="text-2xl font-sans border-b-4 border-blue pb-2">

	<i class="fas fa-file mr-2"></i><span class="text-blue">Edit - </span> {{ $file->name }}

</div>


<form method="POST" action="/{{ Auth::user()->team->app_type }}/files/{{ $file->id }}/update/0/{{ $return_string }}">
    
    @csrf


   <input type="hidden" name="return_string " value="{{ $return_string }}" class="w-full text-lg"/>

   @if(Auth::user()->permissions->developer)
    <div class="my-2 p-2 bg-blue-lighter">
        <span class="font-bold">Dev Only - This Page Returns to:</span> {{ base64_decode($return_string) }}
    </div>
   @endif

<table class="w-full">

  <tr class="border-b">
    <td class="bg-grey-lighter p-2 w-32">
      Name
    </td>
    <td class="p-2">
       {{ $file->name }}

    </td>
  </tr>

  <tr class="border-b">
    <td class="bg-grey-lighter p-2 w-32">
      Description
    </td>
    <td class="p-2">
      <input type="text" name="description" class="border rounded-lg p-2 w-3/4 font-semibold bg-grey-lightest" value="{{ $errors->any() ? old('description') : $file->description }}" />
    </td>
  </tr>

  <tr class="border-b">
    <td class="bg-grey-lighter p-2 w-32">
      Folder
    </td>
    <td class="p-2">

       <select name="directory_id">

          @foreach ($top_level_directories as $thesub)

             @include('shared-features.files.one-directory-dropdown', ['thedirectory' => $thesub, 'level' => 0])

          @endforeach

       </select>
    </td>
  </tr>

  <tr class="border-b">
    <td class="bg-grey-lighter p-2 w-32">
      Cases
    </td>
    <td class="p-2">
      @if($file->cases->first())
          @foreach($file->cases as $thecase)
          <div class="mb-2">
            <a href="/{{ Auth::user()->team->app_type }}/files/{{ $file->id }}/unlink_case/{{ $thecase->id }}/{{ $return_string }}" class="rounded-lg bg-grey-light px-2 py-1 text-xs mr-2">Remove</a>
            <a href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}">{{ $thecase->subject }}</a>
          </div>
          @endforeach
      @endif

      <div class="py-1 mt-2">
        Link to:
          <select name="case_id" id="category_id">
            <option value="">-- None --</option>
            @foreach($cases as $thecase)
              <option value="{{ $thecase->id }}">{{ \Carbon\Carbon::parse($thecase->date)->toDateString() }} - {{ $thecase->subject }}</option>
            @endforeach
         </select>
       </div>

    </td>
  </tr>

  <tr class="border-b">
    <td class="bg-grey-lighter p-2 w-32">
      Groups
    </td>
    <td class="p-2">
      @if($file->groups->first())
        @foreach($file->groups as $thegroup)
          <div class="mb-2">
            <a href="/{{ Auth::user()->team->app_type }}/files/{{ $file->id }}/unlink_group/{{ $thegroup->id }}/{{ $return_string }}" class="rounded-lg bg-grey-light px-2 py-1 text-xs mr-2">Remove</a>
            <a href="/{{ Auth::user()->team->app_type }}/groups/{{ $thegroup->id }}">{{ $thegroup->name }}</a>
          </div>
        @endforeach
      @endif

      <div class="py-1 mt-2">
        Link to:
          <select name="category_id" id="category_id">
            <option value="">-- None --</option>

            @foreach ($categories->where('parent_id',null) as $thesub)

               @include('shared-features.groups.one-category-dropdown', ['thecategory' => $thesub, 'level' => 0])

            @endforeach

         </select>
       </div>

       <div class="py-1" id="groups_checkboxes">
       </div>

    </td>
  </tr>

  <tr class="border-b">
    <td class="bg-grey-lighter p-2 w-32">
      People
    </td>
    <td class="p-2">
      @if($file->people->first())
          @foreach($file->people as $theperson)
          <div class="mb-2">
            <a href="/{{ Auth::user()->team->app_type }}/files/{{ $file->id }}/unlink_person/{{ $theperson->id }}/{{ $return_string }}" class="rounded-lg bg-grey-light px-2 py-1 text-xs mr-2">Remove</a>
            <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $theperson->id }}">{{ $theperson->full_name }}</a>
          </div>
          @endforeach
      @endif

      
      <div id="addothers_form" class="text-sm w-1/2 mt-2 mr-1">
       
        <input id="cfbar" type="text" placeholder="Link People to this File" data-toggle="dropdown" autocomplete="off" class="w-2/3 text-black rounded-lg px-2 py-2 bg-grey-lightest border border-grey focus:bg-white font-bold" />

        <div id="existing_checkboxes_interior">
        </div>

        <div id="addother_form_save" class="hidden py-2">
          <button class="bg-blue text-white rounded-lg text-sm px-2 py-1 text-base">
            Save People
          </button>
        </div>

      </div>

      <div id="people_checkboxes">
      </div>

      <div id="list">
      </div>


    </td>
  </tr>

</table>

  @if(Auth::user()->permissions->developer)

    <table class="w-full mt-8">

      <tr class="border-b border-t-4 border-blue">
        <td class="p-2 bg-black text-grey-lightest" colspan="2">
          Developer Area
        </td>
      </tr>

      <tr class="border-b">
        <td class="bg-grey-lighter p-2 w-32">
          Path
        </td>
        <td class="p-2">
          {{ $file->path }}
        </td>
      </tr>

      <tr class="border-b">
        <td class="bg-grey-lighter p-2 w-32">
          Created at
        </td>
        <td class="p-2">
          {{ $file->created_at }}
        </td>
      </tr>

    </table>

  @endif

 





  <div class="text-right pt-2 text-sm mt-1">
    
    <div class="float-right text-base">

        <input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>

        <input type="submit" formaction="/{{ Auth::user()->team->app_type }}/files/{{ $file->id }}/update/close/{{ $return_string }}" name="update" value="Save and Close" class="rounded-lg px-4 py-2 bg-blue-darker text-white text-center ml-2"/>

      </div>
    </div>

</form>

<br />
<br />

<!-- START MODAL -->

  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        </div>
        <div class="modal-body">
          <div class="text-lg text-left text-red font-bold">
            Are you sure you want to delete this File?
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
          <a href="/{{ Auth::user()->team->app_type }}/files/{{ $file->id }}/delete/{{ $return_string }}" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, DELETE IT</a> </div>
      </div>
    </div>
  </div>

<!-- END MODAL -->


@endsection

@section('javascript')
<script type="text/javascript">

var ajax=null;

function getSearchData(v) {
  if (v == '') { $('#list').addClass('hidden'); }

  // Prevents an earlier request that takes longer from replacing subsequent ones
  if (ajax!=null) { ajax.abort(); }

  ajax = $.get('/{{ Auth::user()->team->app_type }}/files/'+{!! $file->id !!}+'/searchpeople/'+v,function(response) {
      if (response == '') {
        $('#list').addClass('hidden');
      } else {
        $('#list').html(response);
        $('#list').removeClass('hidden');
      }
  });
  
}

$(document).ready(function() {
            
    $("#cfbar").focusout(function(){
      window.setTimeout(function() {$('#list').addClass('hidden'); }, 300);
    });
    

    $("#cfbar").keyup(delay(function(){
      getSearchData(this.value);
    },500));


    $(document).on("change", "#category_id", function() {

        cat_id = $(this).val();

        var url = '/{{ Auth::user()->team->app_type }}/categories/'+cat_id+'/groups_as_checkboxes';

          $.get(url, function(response) {
              $('#groups_checkboxes').html(response);
          }); 
    });


    $(document).on("click", ".clickable-select-person", function() {

        person_id = $(this).attr('data-theid');
        person_name= $(this).attr('data-thename');

        new_checkbox = '<div><label for="person_' + person_id + '"><input type="checkbox" checked name="person_' + person_id + '" id="person_' + person_id + '" /> ' + person_name + '</label></div>';

        $('#people_checkboxes').append(new_checkbox);
    });

 });


</script>

@endsection
