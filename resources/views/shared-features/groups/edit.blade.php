@extends(Auth::user()->team->app_type.'.base')

@section('title')
    {{ $group->name }}
@endsection

@section('breadcrumb')

 <a href="/{{ Auth::user()->team->app_type }}">Home</a>
  > <a href="/{{ Auth::user()->team->app_type }}/groups">Groups</a>
  > <a href="/{{ Auth::user()->team->app_type }}/groups/{{ $group->id }}">{{ $group->name }}</a>
  > &nbsp;<b>Edit</b>

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')



<div class="float-right text-sm">


    <span class="w-32 text-sm pt-1 mr-2">
      <a href="/{{ Auth::user()->team->app_type }}/groups/{{ $group->id }}/merge"><i class="fas fa-coins mr-1"></i> Merge Group</a>
    </span>


  @if(!$group->archived_at)
      <a href="/{{ Auth::user()->team->app_type }}/groups/{{ $group->id }}/archive">
        <button type="button" id="archive" class="rounded-lg px-4 py-2 bg-blue-dark hover:bg-black text-white text-center ml-2"/>
          <i class="fas fa-database mr-2"></i> Archive this Group
        </button>
      </a>
  @else
      <a href="/{{ Auth::user()->team->app_type }}/groups/{{ $group->id }}/archive/reverse">
        <button type="button" id="unarchive" class="rounded-lg px-4 py-2 bg-orange-dark hover:bg-black text-white text-center ml-2"/>
          <i class="fas fa-star mr-2"></i> Un-Archive this Group
        </button>
      </a>
  @endif
</div>


  <div class="float-right text-sm">
      <button type="button" data-toggle="modal" data-target="#deleteModal" id="delete" class="rounded-lg px-4 py-2 bg-white hover:bg-red hover:text-white text-red text-center ml-2"/>
        <i class="fas fa-exclamation-triangle mr-2"></i> Delete Group
      </button>
  </div>


<div class="text-2xl font-sans border-b-4 border-blue pb-2">

    

	<i class="fas fa-tag mr-2"></i><span class="text-blue">Edit - </span> {{ $group->name }}
</div>


<form method="POST" action="/{{ Auth::user()->team->app_type }}/groups/{{ $group->id }}/update">
    {{ csrf_field() }}


<table class="w-full">

  <tr class="border-b">
    <td class="bg-grey-lighter p-2">
      Category
    </td>
    <td class="p-2 flex">

         <select name="category_id" id="category_id">
            <option value="">-- None --</option>

            @foreach ($categories->where('parent_id',null) as $thesub)

               @include('shared-features.groups.one-category-dropdown', ['thecategory' => $thesub, 'level' => 0, 'selected_id' => $group->cat->id])

            @endforeach

         </select>

          @if($group->cat)

            @if($group->cat->has_notes)
              <div class="ml-4 py-2 text-blue">
                <i class="fas fa-check-square"></i> Has Notes
              </div>
            @endif

            @if($group->cat->has_position)
              <div class="ml-4 py-2 text-blue">
                <i class="fas fa-check-square"></i> Has Position
              </div>
            @endif

            @if($group->cat->has_title)
              <div class="ml-4 py-2 text-blue">
                <i class="fas fa-check-square"></i> Has Title
              </div>
            @endif

          @endif
    </td>
  </tr>


  <tr class="border-b">
    <td class="bg-grey-lighter p-2 w-32">
      Name
    </td>
    <td class="p-2">
      <input type="text" name="name" class="border rounded-lg p-2 w-3/4 font-semibold bg-grey-lightest" value="{{ $errors->any() ? old('name') : $group->name }}" />
    </td>
  </tr>

  <tr class="border-b">
    <td class="bg-grey-lighter p-2 w-32">
      Notes
    </td>
    <td class="p-2">
      <textarea name="notes" class="border rounded-lg p-2 w-full bg-grey-lightest">{{ $errors->any() ? old('notes') : $group->notes }}</textarea>
    </td>
  </tr>

  <tr class="border-b">
    <td class="bg-grey-lighter p-2">
      People
    </td>
    <td class="p-2">
      @if($people_total > 0)
        {{ $people_total }}
      @else
        None
      @endif
    </td>
  </tr>


</table>



  <div class="text-right pt-2 text-sm mt-1">
    
    <div class="float-right text-base">

        <input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>

        <input type="submit" formaction="/{{ Auth::user()->team->app_type }}/groups/{{ $group->id }}/update/close" name="update" value="Save and Close" class="rounded-lg px-4 py-2 bg-blue-darker text-white text-center ml-2"/>

      </div>
      
  </div>

</form>

<!-- START MODAL -->

  <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
        </div>
        <div class="modal-body">
          <div class="text-lg text-left text-red font-bold">
            Are you sure you want to delete this group?
          </div>
          <div class="text-left font-bold py-2 text-base">
            This will delete the group and untag all {{ ($people_total > 1) ? number_format($people_total,0,'.',',') : '' }} constituents in your database linked to this group (they will not be removed).
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
          <a href="/{{ Auth::user()->team->app_type }}/groups/{{ $group->id }}/delete" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, DELETE IT</a> </div>
      </div>
    </div>
  </div>

<!-- END MODAL -->


@endsection

@section('javascript')


@endsection
