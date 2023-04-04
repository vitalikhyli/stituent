@extends(Auth::user()->team->app_type.'.base')

@section('title')
  Merge Groups
@endsection

@section('breadcrumb')

  <a href="/{{ Auth::user()->team->app_type }}/groups">Groups</a> >
  Merge Groups

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" action="/{{ Auth::user()->team->app_type }}/groups/merge_confirm">
    {{ csrf_field() }}


<div class="text-2xl font-sans border-b-4 border-blue pb-2">
	<i class="fas fa-coins mr-2"></i>Merge Groups
</div>


<input type="hidden" value="{{ $group->id }}" name="group_id_main" />

<div class="flex w-full p-2 border-b mb-2">


  <div class="flex w-2/3">

    <div class="">
      <select name="category_id" class="category_id" data-merge-order="2" id="category_id_2">
        <option value="">-- Select Category --</option>

        @foreach ($categories->where('parent_id',null) as $thesub)

           @include('shared-features.groups.one-category-dropdown', ['thecategory' => $thesub, 'level' => 0])

        @endforeach

       </select>
     
        <div id="groups_radios_2">
        </div>

     </div>

    <i class="fas fa-arrow-right text-blue text-3xl mx-4"></i>


    <input type="radio" id="group_1_{{ $group->id }}" name="group_id_1" value="{{ $group->id }}" class="mr-1" checked />
    <label for="group_1_{{ $group->id }}" class="font-normal"> {{ $group->name }}</label>

  </div>



   <div class="pl-2 w-1/3">
    

      <div class="p-2 bg-grey-lighter border-b-2 font-bold">
        Save as...
      </div>
      
      <div class="px-2 mt-2">
        <label for="create_new_yes" class="font-normal">
          <input type="radio" checked id="create_new_yes" class="create_new" name="create_new" value="0" />
           Everyone Goes into <span class="font-bold">"{{ $group->name }}"</span>
          <div class="ml-6 text-grey-dark text-sm">
            Description
          </div>
        </label>

        <label for="create_new_no" class="font-normal">
          <input type="radio" id="create_new_no" class="create_new" name="create_new" value="1" />
           Create a New Group
          <div class="ml-6 text-grey-dark text-sm">
            Description
          </div>
        </label>

        <div class="ml-4 hidden" id="create_new_options">
          <select required name="save_category_id" id="save_category_id">
            <option value="">-- Select Category --</option>

            @foreach ($categories->where('parent_id',null) as $thesub)

               @include('shared-features.groups.one-category-dropdown', ['thecategory' => $thesub, 'level' => 0])

            @endforeach

           </select>

           <input type="text" placeholder="New Group Name" id="save_name" name="save_name" class="rounded-lg p-2 border mt-2" />
        </div>

        <div class="px-2 mt-2">
          <label for="keep_originals" class="font-normal">
            <input type="checkbox" id="keep_originals" name="keep_originals" value="true" />
             Retain original groups
            <div class="ml-6 text-grey-dark text-sm">
              Do not delete when done.
            </div>
          </label>
        </div>


      </div>


      <div class="p-2 bg-grey-lighter border-b font-bold mt-4">
        Individual Group-Person Data:
      </div>
      
      <div class="p-2">

        <div class="px-2 mt-2">
          <label for="primary_only" class="font-normal">
            <input required checked type="checkbox" id="primary_only" name="primary_only" value="true" />
             Keep primary
            <div class="ml-6 text-grey-dark text-sm">
              Use the primary group's info for the new group.
            </div>
          </label>
        </div>

        <div class="px-2 mt-2">
          <label for="use_both" class="font-normal">
            <input required checked type="checkbox" id="use_both" name="use_both" value="true" />
             Keep secondary also
            <div class="ml-6 text-grey-dark text-sm">
             When primary info is blank, use secondary info for the new group.
            </div>
          </label>
        </div>

        <div class="px-2 mt-2">
          <label for="combine_notes" class="font-normal">
            <input required checked type="checkbox" id="combine_notes" name="combine_notes" value="true" />
             Combine notes
            <div class="ml-6 text-grey-dark text-sm">
              Combine notes from both primary and secondary
            </div>
          </label>
        </div>


        <div class="px-2 mt-2">
          <label for="archive_secondary" class="font-normal">
            <input required checked type="checkbox" id="archive_secondary" name="archive_secondary" value="true" />
             Archive replaced info
            <div class="ml-6 text-grey-dark text-sm">
              When new info overwrites old, archive the old info in the notes.
            </div>
          </label>
        </div>



          <div class="text-center text-base mt-4">

            <input type="submit" name="update" value="Merge" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>

            <a href="/{{ Auth::user()->team->app_type }}/groups/{{ $group->id }}">
              <button type="button" name="update" class="rounded-lg px-4 py-2 border bg-grey-dark text-white text-center ml-2"/>
                Cancel
              </button>
            </a>

          </div>

      </div>


</div>

 


</form>

@endsection

@section('javascript')
<script type="text/javascript">
$(document).ready(function() {
             
    $(document).on("change", ".category_id", function() {

        cat_id = $(this).val();
        merge_column = $(this).data('merge-order');
        group_id = {!! $group->id !!};

        var url = '/{{ Auth::user()->team->app_type }}/categories/'+cat_id+'/groups_as_radios/'+merge_column+'/'+group_id;

        $.get(url, function(response) {

            $('#groups_radios_' + merge_column).html(response);
        }); 

      });

    $(document).on("change", "input[name='which_primary']", function() {
      which = $(this).val();
      cat_id = $('#category_id_'+which).val();
      $('#save_category_id').val(cat_id);
      name = $('[name="group_id_1"]').next().html();
      $('#save_name').val('NEW'+name);
    });

    $(document).on("change", ".category_id", function() {
      cat_id = $(this).val();     
      $('#save_category_id').val(cat_id);
    });

    $(document).on("change", "input[name='group_id_1']", function() {
      name = $(this).next().html();
      $('#save_name').val('NEW'+name);
    });

    $(document).on("click", ".create_new", function() {
      $('#create_new_options').toggleClass('hidden');
    });

 });
</script>
@endsection
