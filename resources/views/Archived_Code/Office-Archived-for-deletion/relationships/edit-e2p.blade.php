@extends('office.base')
<?php if (!defined('dir')) define('dir','/office
'); ?>

@section('title')
@endsection

@section('breadcrumb')

  {!! Auth::user()->Breadcrumb($entity->name, 'Group', 'level_2') !!}

@endsection

@section('style')

	<style>

	</style>

@endsection

@section('main')

@include('elements.errors')


<form method="POST" 
  @if($mode == 'save')
    action="{{dir}}/relationships/{{ $entity->id }}/save/e2p"
  @endif
  @if($mode == 'update')
    action="{{dir}}/relationships/{{ $relationship->id }}/update"
  @endif
>
  @csrf


    
<div class="text-2xl font-sans border-b-4 border-blue pb-2">

    <div class="float-right text-base">

      <input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>

      @if($mode == 'save')
        <a href="{{dir}}/entity/{{ $entity->id }}">
          <button type="button" class="rounded-lg px-4 py-2 border bg-black text-white text-center ml-2"/>
            Cancel
          </button>
        </a>
      @endif

      @if($mode == 'update')
        <input autocomplete="off" type="submit" formaction="{{dir}}/relationships/{{ $relationship->id }}/update/close" name="update" value="Save and Close" class="rounded-lg px-4 py-2 bg-grey-darkest text-white text-center"/>
      @endif

    </div>

    @if($mode == 'save')
	     <i class="fas fa-flag mr-2"></i>Create Relationship
    @endif

    @if($mode == 'update')
           <i class="fas fa-flag mr-2"></i>Edit Relationship
    @endif

</div>



<table class="w-full">

  <tr class="border-b">
    <td class="bg-grey-lighter p-2 font-bold" colspan="3">
        {{ $entity->name }} is...
    </td>
  </tr>

  <tr class="">
    <td class="p-2 pt-4">
      <div class="flex-shrink">

          <input type="text" id="kind_name" name="kind" class="text-blue-darker border w-full rounded-lg p-2 w-full font-bold" placeholder="Name of Relationship" 
            @if($errors->any())
                value="{{ old('kind') }}"
            @else
              @if(isset($relationship))
                  value="{{ $relationship->kind }}"
              @endif
            @endif
          />

      </div>
    </td>
    <td class="p-2 pt-4 text-center">
      of
    </td>
    <td class="p-2 pt-4 w-1/2">
      <div class="flex-shrink">

          <input type="text" id="object_person_name" class="text-blue-darker border font-bold p-2 rounded-lg w-full" placeholder="Person" 
            @if(isset($object))
                value="{{ $object->full_name }}"
            @endif
          />

          <input type="text" id="object_person_id" name="object_id" class="hidden" 
            @if(isset($relationship))
                value="{{ $relationship->object_id }}"
            @endif
          />

      </div>
    </td>
  </tr>




  <tr class="border-b">

    <td class="p-2 align-top">
      <div class="flex-shrink">
             

      </div>
    </td>

    <td class="p-2 align-top">

      <!-- Spacer -->

    </td>

    <td class="p-2 align-top">

      <span id="object_person_noneselected" class="hidden rounded bg-blue text-white text-xs px-2 py-1 mb-4">None selected</span>

    </td>
    
  </tr>


  <tr class="">

    <td class="p-2 align-top">
      <div class="flex-shrink">
             
          <div id="list-kinds" class="flex-shrink"></div>

      </div>
    </td>

    <td class="p-2 align-top">

      <!-- Spacer -->

    </td>

    <td class="p-2 align-top">

      <div id="list" class="flex-shrink"></div>

    </td>

  </tr>

 

</table>

</form>

@if(isset($relationship))
  <div class="float-left pt-2 text-sm">

      <a href="{{dir}}/relationships/{{ $relationship->id }}/delete">
        <button type="button" name="update" class="rounded-lg px-4 py-2 border bg-grey-darkest hover:bg-black text-white text-center ml-2"/>
          <i class="fas fa-exclamation-triangle mr-2"></i> Delete Relationship
        </button>
      </a>

  </div>
@endif

@endsection

@section('javascript')

<script type="text/javascript">

  function getSearchData_People(v) {
    if (v == '') {
      $('#list').addClass('hidden');
    }
    $.get('{{dir}}/relationships/search_people/'+v, function(response) {
      if (response == '') {
        $('#list').addClass('hidden');
      } else {
        $('#list').html(response);
        $('#list').removeClass('hidden');
      }
    });
  }

  function getSearchData_Kinds(v) {
    if (v == '') {
      $('#list-kinds').addClass('hidden');
    }
    $.get('{{dir}}/relationships/search_kinds/e2p/'+v, function(response) {
      if (response == '') {
        $('#list-kinds').addClass('hidden');
      } else {
        $('#list-kinds').html(response);
        $('#list-kinds').removeClass('hidden');
      }
    });
  }

  $(document).ready(function() {

      $("#object_person_name").focusout(function(){
        window.setTimeout(function() {$('#list').addClass('hidden'); }, 300);
      });
      
      $("#object_person_name").keyup(function(){
        $("#object_person_id").val(null);
        $("#object_person_noneselected").removeClass('hidden');
        $("#object_person_name").removeClass('bg-orange-lightest');
        getSearchData_People(this.value);
      });

      $(document).on('click', ".clickable-entity", function () {
        id = $(this).data("theid");
        name = $(this).data("thename");
        $("#object_person_noneselected").addClass('hidden');
        $("#object_person_name").addClass('bg-orange-lightest');
        $("#object_person_name").val(name);
        $("#object_person_id").val(id);
      });


      $("#kind_name").focusout(function(){
        if ($("#kind_name").val() != '') {
          $("#kind_name").addClass('bg-orange-lightest');
        } else {
          $("#kind_name").removeClass('bg-orange-lightest');
        }
        window.setTimeout(function() {$('#list-kinds').addClass('hidden'); }, 300);
      });
      
      $("#kind_name").keyup(function(){
        getSearchData_Kinds(this.value);
      });

      $(document).on('click', ".clickable-kind", function () {
        name = $(this).find('.thename').html().trim();
        $("#kind_name").val(name);
      });

  });

</script>

@endsection
