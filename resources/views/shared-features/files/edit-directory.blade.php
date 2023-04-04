@extends(Auth::user()->team->app_type.'.base')

@section('title')
    {{ $directory->name }}
@endsection

@section('breadcrumb')

 <a href="/{{ Auth::user()->team->app_type }}">Home</a>
  > Files
  > &nbsp;<b>Edit Directory</b>

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')


@if($directory->parent_id)
  <div class="float-right text-sm">
      <button type="button" data-toggle="modal" data-target="#deleteModal" id="delete" class="rounded-lg px-4 py-2 bg-white hover:bg-red hover:text-white text-red text-center ml-2"/>
        <i class="fas fa-exclamation-triangle mr-2"></i> Delete Directory
      </button>
  </div>
@else
  <!-- Cannot delete top level folder -->
@endif


<div class="text-2xl font-sans border-b-4 border-blue pb-2">

	<i class="fas fa-tag mr-2"></i><span class="text-blue">Edit - </span> {{ $directory->name }}

</div>


<form method="POST" action="/{{ Auth::user()->team->app_type }}/files/directories/{{ $directory->id }}/update">
    
    @csrf


<table class="w-full">



  <tr class="border-b">
    <td class="bg-grey-lighter p-2 w-32">
      Name
    </td>
    <td class="p-2">
      <input type="text" name="name" class="border rounded-lg p-2 w-3/4 font-semibold bg-grey-lightest" value="{{ $errors->any() ? old('name') : $directory->name }}" />
    </td>
  </tr>

 

</table>



  <div class="text-right pt-2 text-sm mt-1">
    
    <div class="float-right text-base">

        <input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>

        <input type="submit" formaction="/{{ Auth::user()->team->app_type }}/files/directories/{{ $directory->id }}/update/close" name="update" value="Save and Close" class="rounded-lg px-4 py-2 bg-blue-darker text-white text-center ml-2"/>

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
            Are you sure you want to delete this directory?
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
          <a href="/{{ Auth::user()->team->app_type }}/files/directories/{{ $directory->id }}/delete" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, DELETE IT</a> </div>
      </div>
    </div>
  </div>

<!-- END MODAL -->


@endsection

@section('javascript')


@endsection
