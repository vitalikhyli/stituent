@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
    {{ $group->name }}
@endsection

@section('breadcrumb')

  {!! Auth::user()->Breadcrumb($group->name, 'Group', 'level_2') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" action="{{dir}}/groups/{{ $group->id }}/update">
    {{ csrf_field() }}


<div class="text-2xl font-sans border-b-4 border-blue pb-2">

    <div class="float-right text-base">

      <input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>

      <input type="submit" formaction="{{dir}}/groups/{{ $group->id }}/update/close" "name="update" value="Save and Close" class="rounded-lg px-4 py-2 bg-grey-darker text-white text-center ml-2"/>

    </div>

	<i class="fas fa-tag mr-2"></i><span class="text-blue">Edit - </span> {{ $group->name }}
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
    <td class="bg-grey-lighter p-2 w-32">
      Name
    </td>
    <td class="p-2">
      <input type="text" name="name" class="border rounded-lg p-2 w-3/4 font-semibold bg-grey-lightest" value="{{ $errors->any() ? old('name') : $group->name }}" />
    </td>
  </tr>

  <tr class="border-b">
    <td class="bg-grey-lighter p-2">
      People
    </td>
    <td class="p-2">
      {{ $people_total }}
    </td>
  </tr>


</table>

</form>

  <div class="float-left pt-2 text-sm">
      <a href="{{dir}}/groups/{{ $group->id }}/delete">
        <button type="button" name="update" class="rounded-lg px-4 py-2 border bg-grey-darkest hover:bg-black text-white text-center ml-2"/>
          <i class="fas fa-exclamation-triangle mr-2"></i> Delete Group
        </button>
      </a>
  </div>


@endsection

@section('javascript')


@endsection
