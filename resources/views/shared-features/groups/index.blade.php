@extends(Auth::user()->team->app_type.'.base')


@section('title')
    Groups
@endsection

@section('breadcrumb')

  <a href="/{{ Auth::user()->team->app_type }}">Home</a> > &nbsp;<b>Groups</b>

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

<div class="flex border-b-4 pb-2 border-blue">
  <div class="text-2xl font-sans w-full">
    Groups
  </div>

  <input type="text" id="filter-input" onkeyup="filterGroups()" class="border-2 mr-2 rounded-lg p-2" placeholder="Filter Groups" />

  @include('shared-features.groups.index-nav')

</div>

  


<div class="flex pb-10">

  <div class="flex-1 w-3/4 mt-4 pr-6">

   @foreach ($categories->where('parent_id',null) as $thecategory)
   
      @include('shared-features.groups.one-category', ['thecategory' => $thecategory])

   @endforeach


  </div>

  <!------------------------------------ SIDEBAR -------------------------------------->

  <div class="w-1/4">
    <div id="menu" class="mt-0">
      <div class="text-2xl font-sans mt-4 mb-2 border-b pb-1">
        Categories
      </div>

      <div class="table w-full">
      @foreach ($categories->where('parent_id',null) as $thecategory)
         <div class="table-row">
            <div class="table-cell pr-2">
              <a href="#{{ $thecategory->id }}">
                <!-- <i class="fas fa-tags mr-2"></i> -->
                {{ $thecategory->name }}
              </a>
            </div>

         </div>
      @endforeach
      </div>

      <div class="text-center">
        
          <button type="button" id="add-category" class="add-group-to-cat bg-grey-light px-4 py-2 rounded-lg text-base">
            Add Category
          </button>

           <div id="add-category-form" class="hidden">
               <form action="/{{ Auth::user()->team->app_type }}/categories/add" method="post">
                     @csrf
                     <div class="mt-2 border rounded-lg bg-grey-lightest">

                       <input required type="type" name="new_category_name" id="new-category-form-name" class="rounded-lg px-2 py-1 border font-bold m-2 w-5/6" placeholder="New Category Name" />

                        <div class="">
                           Subcategory Of
                        </div>
                        <div class="mt-1 mb-3">
                           <select name="parent_id">
                              <option value="">-- None --</option>

                              @foreach ($categories->where('parent_id',null) as $thesub)

                                 @include('shared-features.groups.one-category-dropdown', ['thecategory' => $thesub, 'level' => 0])

                              @endforeach

                           </select>
                        </div>
                     </div>

                     <button type="submit" class="rounded-lg bg-blue text-white px-2 py-1 mt-2">
                        Save Category
                     </button>
               </form>
           </div>

      </div>
    </div>
  </div>
  
</div>


@endsection

@section('javascript')

<script type="text/javascript">


function filterGroups()
{
  input = document.getElementById('filter-input');
  filter_string = input.value.toUpperCase();
  lines = document.getElementsByClassName('group-div');
  group_names = document.getElementsByClassName('group-name-div');
  categories = document.getElementsByClassName('category-div');

  var visible_categories = [];

  for (i = 0; i < lines.length; i++) {

    group_name = group_names[i].innerHTML.trim().toUpperCase();

    if (group_name.indexOf(filter_string) > -1) {

      lines[i].style.display = "";

      the_category = lines[i].getAttribute('data-category');

      if (!visible_categories.includes(the_category)) {
        visible_categories.push(the_category);
      } 
      
    } else {

      lines[i].style.display = "none";

    }
  }

  for (j = 0; j < categories.length; j++) {

    id = categories[j].getAttribute('category-id');

    if (visible_categories.includes(id) || filter_string == '') {
      categories[j].style.display = "";
    } else {
      categories[j].style.display = "none";
    }

  }

}


$(document).ready(function() {

    $('#filter-input').focus();

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

  $(document).on("click", "#add-category", function() {
      $(this).toggleClass('hidden');
      $('#add-category-form').toggleClass('hidden');
      $('#new-category-form-name').focus();
  });

});


</script>

@endsection
