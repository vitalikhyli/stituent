@extends('office.base')
<?php if (!defined('dir')) define('dir',Auth::user()->team->app_type); ?>

@section('title')
    {{ $category->name }}
@endsection

@section('breadcrumb')

 <a href="/{{ Auth::user()->team->app_type }}">Home</a>
  > <a href="/{{ Auth::user()->team->app_type }}/categories">Category</a>
  > <a href="/{{ Auth::user()->team->app_type }}/categories/{{ $category->id }}">{{ $category->name }}</a>
  > &nbsp;<b>Edit</b>

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')


<div class="float-right text-sm">
    <button type="button" data-toggle="modal" data-target="#deleteModal" id="delete" class="rounded-lg px-4 py-2 bg-white hover:bg-red hover:text-white text-red text-center ml-2"/>
      <i class="fas fa-exclamation-triangle mr-2"></i> Delete Category      </button>
</div>


<div class="text-2xl font-sans border-b-4 border-blue pb-2">

	<i class="fas fa-tag mr-2"></i><span class="text-blue">Edit - </span> {{ $category->name }}

</div>


<form method="POST" action="/{{dir}}/categories/{{ $category->id }}/update">
    
    @csrf


<table class="w-full">



  <tr class="border-b">
    <td class="bg-grey-lighter p-2 w-32">
      Name
    </td>
    <td class="p-2">
      <input type="text" name="name" class="border rounded-lg p-2 w-3/4 font-semibold bg-grey-lightest" value="{{ $errors->any() ? old('name') : $category->name }}" />
    </td>
  </tr>

  <tr class="border-b">
    <td class="bg-grey-lighter p-2 w-32">
      Data
    </td>
    <td class="p-2">

      <div class="">

        <label for="has_title" class="font-normal ml-2 w-full">

          <div class="flex">
            <input type="radio" 
                   id="has_title" 
                   name="title_or_position" 
                   class="border rounded-lg font-semibold bg-grey-lightest" 
                   value="title" 
                   {{ ($category->has_title) ? 'checked' : '' }} />

          
            <div class="w-32 ml-2">Has Title</div>
            <div class="text-grey-dark ml-4">example: "President," "Director," etc.</div>
            <div class="text-blue ml-4 flex-grow text-right">(Generally for Organizations, Associations, etc.)</div>
          </div>

        </label>

      </div>

      <div class="">

        <label for="has_position" class="font-normal ml-2 w-full">

          <div class="flex">
            <input type="radio" 
                   id="has_position" 
                   name="title_or_position" 
                   class="border rounded-lg font-semibold bg-grey-lightest"
                   value="position"
                   {{ ($category->has_position) ? 'checked' : '' }} />

          
            <div class="w-32 ml-2">Has Position</div>
            <div class="text-grey-dark ml-4">example: "Supports," "Opposed," etc.</div>
            <div class="text-blue ml-4 flex-grow text-right">(Generally for Issues, Legislation, etc.)</div>
          </div>

        </label>

      </div>

      <div class="">

        <label for="has_neither" class="font-normal ml-2">

           <div class="flex"> 
            <input type="radio" 
                   id="has_neither" 
                   name="title_or_position" 
                   class="border rounded-lg font-semibold bg-grey-lightest"
                   value=""
                   {{ (!$category->has_position && !$category->has_title) ? 'checked' : '' }} />

            <div class="w-32 ml-2">Neither</div>

          </div>

        </label>

      </div>

      <div class="border-t mt-1 pt-1">

        <label for="has_notes" class="font-normal ml-2">

          <input type="checkbox" 
                 id="has_notes" 
                 name="has_notes" 
                 class="border rounded-lg font-semibold bg-grey-lightest" 
                 value="1" 
                 {{ ($category->has_notes) ? 'checked' : '' }} />

          Has Notes

        </label>

      </div>

    </td>
  </tr>



</table>



  <div class="text-right pt-2 text-sm mt-1">
    
    <div class="float-right text-base">

        <input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>

        <input type="submit" formaction="/{{dir}}/categories/{{ $category->id }}/update/close" name="update" value="Save and Close" class="rounded-lg px-4 py-2 bg-blue-darker text-white text-center ml-2"/>

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
            Are you sure you want to delete this category?
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">CANCEL</button>
          <a href="/{{dir}}/categories/{{ $category->id }}/delete" id="modal-confirm-delete-button" class="btn btn-primary bg-red">YES, DELETE IT</a> </div>
      </div>
    </div>
  </div>

<!-- END MODAL -->


@endsection

@section('javascript')


@endsection
