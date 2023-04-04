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

<div class="text-2xl font-sans border-b-4 border-blue pb-2">

  @if($show_positions == true)
      <div class="flex float-right text-base">

        <a class="mx-1" href="{{dir}}/groups/{{ $group->id }}">
        <div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ (preg_match("/\/groups\/[0-9]+$/",request()->path())) ? 'bg-blue-darker text-white' : '' }}">
            All
        </div>
        </a>        

        <a class="mx-1" href="{{dir}}/groups/{{ $group->id }}/position/support">
        <div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ (strpos(request()->path(), 'support') != false) ? 'bg-blue-darker text-white' : '' }}">
            Support
        </div>
        </a>

        <a class="mx-1" href="{{dir}}/groups/{{ $group->id }}/position/undecided">
        <div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ (strpos(request()->path(), 'undecided') != false) ? 'bg-blue-darker text-white' : '' }}">
            Undecided
        </div>
        </a>

        <a class="mx-1" href="{{dir}}/groups/{{ $group->id }}/position/oppose">
        <div class="flex-1 flex-initial rounded-lg px-2 py-1 text-grey-darker {{ (strpos(request()->path(), 'oppose') != false) ? 'bg-blue-darker text-white' : '' }}">
            Oppose
        </div>
        </a>
      </div>
  @endif


	<i class="fas fa-tag mr-2"></i>{{ $group->name }} <span class="text-grey-dark ml-2">({{ $people_total }} people)</span>
</div>

  @if($people_total <= 0)

    <div class="p-4">None</div>

  @else

      @if($people instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div class="text-right">
          {{ $people->links() }}
        </div>
      @endif

      <table class="mt-2 w-full text-sm">
          <tr class="bg-grey-lighter border-b">
            <td class="p-1">
              Remove
            </td>
            <td class="p-1 w-1/4">
              Name
            </td>
            <td class="p-1 w-1/5">
              City
            </td>

            @if($show_positions == true)
                <td class="p-1 w-1/4">
                  Position
                </td>
            @endif

            <td class="p-1">
              Full Address
            </td>
          </tr>
        @foreach($people as $theperson)

          <tr class="border-b cursor-pointer hover:bg-orange-lighter">
            <td class="p-1 pr-4">
              
            @if($theperson->memberOfGroup($group->id))
              <button data-action="remove" data-group_id="{{ $group->id }}" data-person_id="{{ $theperson->id }}" class="toggle-group bg-blue rounded-lg text-white px-2 py-1 text-sm whitespace-no-wrap">
                {{ substr($group->name,0,15) }}
              </button>
            @else
              <button data-action="add" data-group_id="{{ $group->id }}" data-person_id="{{ $theperson->id }}" class="toggle-group bg-grey rounded-lg text-white px-2 py-1 text-sm whitespace-no-wrap">
                {{ substr($group->name,0,15) }}
              </button>
            @endif

            </td>
            <td class="clickable p-1 pr-4 whitespace-no-wrap" data-href="{{dir}}/constituents/{{ $theperson->id }}">
              {{ $theperson->full_name}}
            </td>
            <td class="clickable p-1 pr-4" data-href="{{dir}}/constituents/{{ $theperson->id }}">
              {{ $theperson->address_city }}
            </td>

            @if($show_positions == true)
                <td class="clickable p-1 pr-4 text-grey-darkest font-bold capitalize" data-href="{{dir}}/constituents/{{ $theperson->id }}">
                  {{ json_decode($theperson->data,true)['position'] }}
                </td>
            @endif

            <td class="clickable p-1 pr-4 text-grey-darker text-sm overflow-x-hidden whitespace-no-wrap" data-href="{{dir}}/constituents/{{ $theperson->id }}">
              {{ $theperson->full_address }}
            </td>
          </tr>
        @endforeach
      </table>
  @endif

<br />
<br />
@endsection

@section('javascript')

<script type="text/javascript">


  $(document).ready(function() {

    $(".clickable").click(function() {
        window.location = $(this).data("href");
    });

    $(document).on("click", ".add-group-to-cat", function() {
        var id = $(this).attr('data-href');
        $("#"+id).removeClass('hidden');
    });

             
    $(document).on("click", ".toggle-group", function() {
        var person_id = $(this).attr('data-person_id');
        var group_id = $(this).attr('data-group_id');
        var action = $(this).attr('data-action');
        var team_id = {!! Auth::user()->team->id !!};

        var url = '{{dir}}/bulkgroups/'+action+'/'+group_id+'/'+person_id+'/'+team_id;
          $.get(url, function(response) {
              $('[data-person_id="'+person_id+'"]').replaceWith(response);
          }); 
      });



    });
</script>

@endsection
