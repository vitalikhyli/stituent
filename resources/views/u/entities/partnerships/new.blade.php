@extends(Auth::user()->team->app_type.'.base')

@section('title')
    New Partnership
@endsection

@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	<a href="/{{ Auth::user()->team->app_type }}/entities">Organizations</a> >
	<a href="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}">{{ $entity->name }}</a> >
	New Partnership

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

	<div class="text-2xl font-sans border-b-4 border-blue pb-3">

		<span class="text-xl">
		<i class="fas fa-building mr-2"></i>
		New Partnership for <b>{{ $entity->name }}</b>
		</span>
	</div>

@include('elements.errors')

<form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}/partnerships/save">
	@csrf

	<table class="text-base w-full border-t">

		<tr class="border-b">
			<td class="pt-4 pr-2 bg-grey-lighter text-right align-top w-1/6">
				Program/Course
			</td>
			<td class="p-2">

				<input name="program" id="program" placeholder="i.e. {{ $example_program }}..." value="" class="border-2 rounded-lg px-4 py-2 w-1/2"/>

			</td>
		</tr>

		<tr class="">
			<td colspan="2">

				<div class="mt-2">
					<button class="bg-blue text-white rounded-lg px-3 py-2 float-right">
						Save
					</button>

					<a href="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}">
					<button type="button" class="bg-grey-darkest mr-2 text-white rounded-lg px-3 py-2 float-right">
						Cancel
					</button>
					</a>
				</div>

			</td>
		</tr>

		<tr class="">
			<td colspan="2">
				<div id="list-programs" class="flex-shrink"></div>
			</td>
		</tr>


	</table>


</form>

<br />
<br />
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

	$("#program").focus();
	getSearchData_Programs('');

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