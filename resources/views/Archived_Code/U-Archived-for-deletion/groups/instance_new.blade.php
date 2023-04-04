@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
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

<form method="POST" action="{{dir}}/constituents/{{ $person->id }}/instance/save">
    {{ csrf_field() }}


<div class="text-2xl font-sans border-b-4 border-blue pb-2">

    <div class="float-right text-base">

      <input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>

      <a href="{{ url()->previous() }}">
        <button type="button" name="update" class="rounded-lg px-4 py-2 border bg-black text-white text-center ml-2"/>
          Cancel
        </button>
      </a>


    </div>

	<i class="fas fa-flag mr-2"></i>Assign Group to {{ $person->full_name }}
</div>



<table class="w-full">

  <tr class="border-b">
    <td class="bg-grey-lighter p-2 w-32">
      Category
    </td>
    <td class="p-2">
      <span class="capitalize">{{ $category->name }}</span>
    </td>
  </tr>

  <tr class="border-b">
    <td class="bg-grey-lighter p-2  align-top w-32">
      Choose Group
    </td>
    <td class="p-2 align-top">

        @foreach($groups as $thegroup)
            <div id="{{ $thegroup->id }}_line" class="group_option_line {{ ($loop->last) ? '' : 'border-b' }} p-2">
                <label for="{{ $thegroup->id }}">
                  <input type="radio" id="{{ $thegroup->id }}" class="group_option" name="group" value="{{ $thegroup->id }}" />
                  <span class="ml-4 font-normal">{{ $thegroup->name }}</span>
              </label>
            </div>
        @endforeach

    </td>
  </tr>

 

</table>

 
    <div class="float-right text-base pt-2">

      <input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 bg-blue text-white text-center"/>

      <a href="{{ url()->previous() }}">
        <button type="button" name="update" class="rounded-lg px-4 py-2 border bg-black text-white text-center ml-2"/>
          Cancel
        </button>
      </a>


    </div>

</form>

@endsection

@section('javascript')

<script type="text/javascript">

    $(document).on("click", ".group_option", function() {

        var id = $(this).attr('id');

            $('.group_option_line').removeClass('bg-orange-lightest');


        if ($("[id="+id+"]").is(":checked")) {

            $('#'+id+'_line').addClass('bg-orange-lightest');

        }
    });

</script>

@endsection
