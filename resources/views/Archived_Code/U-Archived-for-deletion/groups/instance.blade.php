@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
    {{ $instance->name }}
@endsection

@section('breadcrumb')

  {!! Auth::user()->Breadcrumb($person->name, 'Group', 'level_2') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" action="{{dir}}/groups/instance/{{ $instance->id }}/update">
    {{ csrf_field() }}


<div class="text-2xl font-sans border-b-4 border-blue pb-2">

    <div class="float-right text-base">

      <input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>

      <input type="submit" formaction="{{dir}}/groups/instance/{{ $instance->id }}/update/close" "name="update" value="Save and Close" class="rounded-lg px-4 py-2 bg-blue-darker text-white text-center ml-2"/>

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

  <tr class="border-b">
    <td class="bg-grey-lighter p-2">
      Group
    </td>
    <td class="p-2">
        <a href="{{dir}}/groups/{{ $group->id }}">
            <button type="button" class="bg-blue text-white px-2 py-1 rounded text-sm">
              Go to all people in "{{ $group->name }}"
            </button>
        </a>
    </td>
  </tr>

  @if(array_key_exists('position', $data))
    <tr class="border-b">
      <td class="bg-grey-lighter p-2 w-32">
        Position
      </td> 
      <td class="p-2 flex">
        <div class="border-r px-4">
          <label class="font-normal" for="data_1">
        <input type="radio" name="position" value="" {{ (strtolower($data['position']) == '') ? 'checked' : '' }} id="data_1" /> None</label>
        </div>
        <div class="border-r px-4">
          <label class="font-normal" for="data_2">
        <input type="radio" name="position" value="support" {{ (strtolower($data['position']) == 'support') ? 'checked' : '' }} id="data_2" /> Support</label>
       </div>
        <div class="border-r px-4">
          <label class="font-normal" for="data_3">
        <input type="radio" name="position" value="undecided" {{ (strtolower($data['position']) == 'undecided') ? 'checked' : '' }} id="data_3" /> Undecided</label>
        </div>
        <div class="border-r px-4">
          <label class="font-normal" for="data_4">
        <input type="radio" name="position" value="oppose" {{ (strtolower($data['position']) == 'oppose') ? 'checked' : '' }} id="data_4" /> Oppose</label>
      </div>
      </td>
    </tr>
  @endif

  <tr class="border-b">
    <td class="bg-grey-lighter p-2 w-32">
      Notes
    </td>
    <td class="p-2">
      <textarea name="notes" rows="4" class="w-full border rounded-lg p-2 bg-grey-lightest" />{{ $errors->any() ? old('name') : $data['notes'] }}</textarea>
    </td>
  </tr>

</table>

  <div class="float-left pt-2 text-sm">

      <a href="{{dir}}/groups/instance/{{ $instance->id }}/delete">
        <button type="button" name="update" class="rounded-lg px-4 py-2 border bg-black text-white text-center ml-2"/>
          <i class="fas fa-exclamation-triangle mr-2"></i> Remove Group from {{ $person->full_name }}
        </button>
      </a>

  </div>

</form>

@endsection

@section('javascript')


@endsection
