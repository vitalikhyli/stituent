@extends(Auth::user()->team->app_type.'.base')

@section('title')
    Edit Partnership
@endsection

@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	<a href="/{{ Auth::user()->team->app_type }}/entities">Organizations</a> >
	<a href="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}">{{ $entity->name }}</a> >
	Edit Partnership

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')



	<div class="text-2xl font-sans border-b-4 border-blue pb-3">

		<span class="text-xl">
		<i class="fas fa-building mr-2"></i>
		{{ $entity->name }}
		</span>
	</div>

	@include('elements.errors')


	@include(Auth::user()->team->app_type.'.entities.partnerships.form')


@endsection

@section('javascript')
<script type="text/javascript">

  function getSearchData_Programs(v) {
    if (v == '') {
      $('#list-programs').addClass('hidden');
    }
    // alert('/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/partnerships/search_programs/'+v);
    $.get('/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/partnerships/search_programs/'+v, function(response) {
      if (response == '') {
      	// alert(response);
        $('#list-programs').addClass('hidden');
      } else {
        $('#list-programs').html(response);
        $('#list-programs').removeClass('hidden');
      }
    });
  }

  $(document).ready(function() {

	// $("#program").focus();
	// getSearchData_Programs('');

      $("#program").focusout(function(){
        window.setTimeout(function() {$('#list-programs').addClass('hidden'); }, 300);
      });
      
      $("#program").keyup(function(){
        $("#program").removeClass('bg-orange-lightest');
        getSearchData_Programs(this.value);
      });

      $(document).on('click', ".clickable-program", function () {
        program = $(this).data("theprogram");
        $("#program").addClass('bg-orange-lightest');
        $("#program").val(program);
        $("html, body").animate({scrollTop: 0}, 250);
      });

      $("#program").keyup(function(){
        getSearchData_Kinds(this.value);
      });

  });

</script>
@endsection