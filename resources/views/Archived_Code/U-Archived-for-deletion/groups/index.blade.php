@extends('u.base')
<?php if (!defined('dir')) define('dir','/u'); ?>

@section('title')
    Constituent Groups
@endsection

@section('breadcrumb')

  <a href="/u">Home</a> > &nbsp;<b>Groups</b>

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

<div class="text-2xl font-sans border-b-4 border-blue pb-2">
	Manage Groups <span class="ml-1 text-xl text-grey-dark">({{ $numgroups }})</span>
</div>


<div class="flex pb-10">

  <div class="flex-1 w-3/4 mt-4">
  @foreach ($categories as $thecategory)
  <form method="POST" action="{{dir}}/groups/new">
  {{ csrf_field() }}

  <a name="{{ $thecategory->id }}"></a>
  <div class="mr-4 p-2 flex-initial cursor-pointer">

    <div class="flex text-sm tracking-wide text-left uppercase pb-1 border-b-2 border-grey">
      <div class="flex-1">
        {{ $thecategory->name }}
        ({{ $thecategory->groups->where('team_id',Auth::user()->team->id)->count() }})
      </div>

      @if(Auth::user()->permissions->creategroups)
        <button type="button" data-cat="{{ $thecategory->id }}" class="add-group-to-cat bg-grey-light px-2 py-1 rounded-lg text-xs ml-2 hover:bg-blue-dark">
          Add
        </button>
      @endif

    </div>

    @if(Auth::user()->permissions->creategroups)
      <div id="{{ $thecategory->id }}" class="hidden text-left uppercase text-center py-2 bg-grey-lighter px-2 border-b">
        <input type="text" placeholder="New" name="name" class="rounded-lg border p-2" />
        <input type="hidden" name="category_id" value="{{ $thecategory->id }}" />
        <input type="submit" class="bg-blue text-white p-2 rounded-lg ml-1" value="Add New" />
      </div>
    @endif

    <div class="mt-2 text-sm">
    @foreach ($thecategory->groups->where('team_id',Auth::user()->team->id)->sortBy('name') as $thegroup)
      <div class="ml-2 mb-2 flex hover:bg-orange-lightest">

        <div class="flex border-b pb-1 w-full">
          <div class="w-12 mr-2 whitespace-no-wrap">
            <a href="{{dir}}/groups/{{ $thegroup->id }}/edit" class="text-grey-darker">
              <button type="button" class="bg-blue text-white px-2 py-1 rounded-lg text-xs ml-2 hover:bg-blue-dark mr-2 ">
                Edit
              </button>
            </a>
          </div>
          <div class="flex-grow mr-2 whitespace-no-wrap">
            {{ $thegroup->name }}
          </div>
          <div class="w-24 mr-2 whitespace-no-wrap text-right">
            @if($thegroup->people->count() > 0)
            <a href="{{dir}}/groups/{{ $thegroup->id }}">
              <button type="button" class="bg-blue text-white px-2 py-1 rounded-lg text-xs hover:bg-blue-dark">
              <i class="fas fa-search"></i> see all {{ $thegroup->people->count() }}  
              </button>
            </a>
            @endif
          </div>

        </div>

      </div>
    @endforeach
    </div>



  </div>
  </form>
  @endforeach
  </div>

  <div class="w-1/4">
    <div id="menu" class="mt-0">
      <div class="text-2xl font-sans mt-4 mb-2 border-b pb-1">
        Categories
      </div>

      @foreach ($categories as $thecategory)
        <a href="#{{ $thecategory->id }}">
        <div class="p-2 uppercase hover:bg-grey-lighter rounded-lg cursor-pointer text-grey-darker">
          <i class="fas fa-tags mr-2"></i> {{ $thecategory->name }}
        </div>
        </a>
      @endforeach
    </div>
  </div>
  
</div>


@endsection

@section('javascript')

<script type="text/javascript">


$(document).ready(function() {

    $(document).on("click", ".add-group-to-cat", function() {
        var id = $(this).attr('data-cat');
        if ($("#"+id).hasClass('hidden')) {
          $("#"+id).removeClass('hidden');
        } else {
          $("#"+id).addClass('hidden');
        }
    });

  $(window).bind('scroll', function () {
      if ($(window).scrollTop() > 200) {
          $('#menu').addClass('fixed');
          $('#menu').css({display: 'block'});
      } else {
          $('#menu').removeClass('fixed');
          $('#menu').css({top: '10px'});
      }
  });


});


</script>

@endsection
