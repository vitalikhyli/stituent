@extends('office.base')
<?php if (!defined('dir')) define('dir',Auth::user()->team->app_type); ?>

@section('title')
    {{ $instance->name }}
@endsection

@section('breadcrumb')

  <a href="/{{ Auth::user()->team->app_type }}">Home</a>
  > <a href="/{{ Auth::user()->team->app_type }}/groups">Groups</a>
  > <a href="/{{ Auth::user()->team->app_type }}/groups/{{ $group->id }}">{{ $group->name }}</a>
  > &nbsp;<b>{{ $person->name }}</b>

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" action="/{{dir}}/groups/instance/{{ $instance->id }}/update">
    {{ csrf_field() }}


<div class="text-2xl font-sans border-b-4 border-blue pb-2">

    <div class="float-right text-base">

      
      <button type="button" data-toggle="modal" data-target="#deleteModal" name="remove" id="remove" class="rounded-lg px-4 py-2 text-red text-center ml-2 text-sm">
        <i class="fas fa-exclamation-triangle mr-2"></i> Remove Group
      </button>

    </div>

	<i class="fas fa-flag mr-2"></i>{{ $person->full_name }} <i class="fas fa-arrow-right mx-2"></i>

 {{ $group->name }}
</div>





<table class="w-full">

  <tr class="border-b">
    <td class="bg-grey-lighter p-2">
      Category
    </td>
    <td class="p-2">
      <span class="capitalize">{{ $group->cat->name }}</span>
    </td>
  </tr>


  @if($group->cat->has_position)
    <tr class="border-b">
      <td class="bg-grey-lighter p-2 w-32">
        Position
      </td> 
      <td class="p-2 flex">

        <div class="border-r px-4">
          <label class="font-normal" for="data_1">
          <input id="data_1" type="radio" name="position" value="" {{ (strtolower($instance->position) == '') ? 'checked' : '' }} /> None</label>
        </div>

        <div class="border-r px-4">
          <label class="font-normal" for="data_2">
        <input id="data_2" type="radio" name="position" value="supports" {{ (strtolower($instance->position) == 'supports') ? 'checked' : '' }} /> Supports</label>
        </div>

        <div class="border-r px-4">
          <label class="font-normal" for="data_3">
          <input id="data_3" type="radio" name="position" value="undecided" {{ (strtolower($instance->position) == 'undecided') ? 'checked' : '' }} /> Undecided</label>
        </div>

        <div class="border-r px-4">
          <label class="font-normal" for="data_4">
          <input id="data_4" type="radio" name="position" value="concerned" {{ (strtolower($instance->position) == 'concerned') ? 'checked' : '' }} /> Concerned</label>
        </div>

        <div class="border-r px-4">
          <label class="font-normal" for="data_5">
          <input id="data_5" type="radio" name="position" value="opposed" {{ (strtolower($instance->position) == 'opposed') ? 'checked' : '' }} /> Opposed</label>
        </div>

      </td>
    </tr>
  @endif

  @if($group->cat->has_title)
    <tr class="border-b">
      <td class="bg-grey-lighter p-2 w-32">
        Title
      </td>
      <td class="p-2">
        <input type="text" name="title" class="w-full border rounded-lg p-2 bg-grey-lightest" value="{{ $errors->any() ? old('title') : $instance->title }}" />
      </td>
    </tr>
  @endif

  @if($group->cat->has_notes)
    <tr class="border-b">
      <td class="bg-grey-lighter p-2 w-32">
        Notes
      </td>
      <td class="p-2">
        <textarea name="notes" rows="4" class="w-full border rounded-lg p-2 bg-grey-lightest" />{{ $errors->any() ? old('notes') : $instance->notes }}</textarea>
      </td>
    </tr>
  @endif

</table>

  <div class="float-right pt-2">

     <input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>

      <input type="submit" formaction="/{{dir}}/groups/instance/{{ $instance->id }}/update/close" name="update" value="Save and Close" class="rounded-lg px-4 py-2 bg-blue-darker text-white text-center ml-2"/>

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
            Are you sure you want to remove this group?
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
          <a href="/{{dir}}/groups/instance/{{ $instance->id }}/delete" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, REMOVE IT</a> </div>
      </div>
    </div>
  </div>

<!-- END MODAL -->

@endsection

@section('javascript')


@endsection
