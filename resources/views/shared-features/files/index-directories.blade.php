@extends(Auth::user()->team->app_type.'.base')

@section('title')
    Files
@endsection


@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a>

	> <b>Files</b>

@endsection 

@section('style')

	<style>

		.pagination {
			margin: 0px;
		}
		.pagination>li>a, 
		.pagination>li>span {
			border: 0;
		}

	</style>

@endsection


@section('main')


<div class="flex border-b-4 pb-2 border-blue">
  <div class="text-2xl font-sans w-full">
    Files
  </div>

  @include('shared-features.files.index-nav')

</div>


<div class="w-full" id="paginate-div">
	@if($directories instanceof \Illuminate\Pagination\LengthAwarePaginator)
		<div class="w-full flex text-grey-dark">
			<div class="w-1/4 text-left p-2">
				Showing <span class="font-bold text-black">{{ $directories->firstItem() }}-{{ $directories->lastItem() }}</span> of 
				<span class="font-bold text-black">{{ $directories->total() }}</span>
			</div>
			<div class="w-1/2 text-center">
				{{ $directories->links() }}
			</div>
			<div class="w-1/4 text-right p-2">
				Page <span class="text-black font-bold">{{ $directories->currentPage() }}</span> of <span class="text-black font-bold">{{ $directories->lastPage() }}</span>
			</div>
		</div>
	@endif
</div>

<div class="mb-4" id="spacer">
</div>

	@if(!$directories->first())

		<div class="p-4 font-bold w-full">No files to show.</div>

	@else

		<div id="list" class="mt-4 mb-4 w-full">

			@foreach($directories->where('parent_id',null) as $dir)

				@include('shared-features.files.one-directory', ['dir' => $dir])

			@endforeach

		</div>

	@endif




@endsection

@section('javascript')
<script type="text/javascript">

	$(document).ready(function() {

	    $(document).on("click", ".add-submodel", function() {
	        var id = $(this).attr('data-id');
	        $("#add-submodel-form-"+id).toggleClass('hidden');
	        $("#add-submodel-form-input-"+id).focus();
	    });

	    $(document).on("click", ".upload-file", function() {
	        var id = $(this).attr('data-id');
	        $("#upload-form-"+id).toggleClass('hidden');
	    });

	    $(document).on("click", ".select-mode", function() {
	        $('.file-select').each(function() {
			  $(this).toggleClass('hidden');
			});
	    });

		$('input:checkbox').change(
		function(){
			if ($("input:checkbox:checked.file-select").length > 0) {

			    if ($(this).is(':checked')) {

			        $('.move-here').each(function() {
					  $(this).removeClass('hidden');
					});
			    }

			} else {

		        $('.move-here').each(function() {
				  $(this).addClass('hidden');
				});
			}
		});

	    $(document).on("click", ".move-here", function() {
	        dir = $(this).attr('data-id');
			var file_ids = $("input:checkbox:checked.file-select").map(function(){
		      return $(this).val();
		    }).get();
		    url = '/{!! Auth::user()->team->app_type !!}/files/directories/'+dir+'/move/'+file_ids;
		    location.href = url;
	    });


	});

</script>
@endsection
