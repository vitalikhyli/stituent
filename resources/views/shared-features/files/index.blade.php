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


<div class="w-full id="paginate-div">
	@if($files instanceof \Illuminate\Pagination\LengthAwarePaginator)
		<div class="w-full flex text-grey-dark">
			<div class="w-1/4 text-left p-2">
				Showing <span class="font-bold text-black">{{ $files->firstItem() }}-{{ $files->lastItem() }}</span> of 
				<span class="font-bold text-black">{{ $files->total() }}</span>
			</div>
			<div class="w-1/2 text-center">
				{{ $files->links() }}
			</div>
			<div class="w-1/4 text-right p-2">
				Page <span class="text-black font-bold">{{ $files->currentPage() }}</span> of <span class="text-black font-bold">{{ $files->lastPage() }}</span>
			</div>
		</div>
	@endif
</div>


	@include('shared-features.files.search-bar', ['models' => $files])


	@if(!$files->first())

		<div class="p-4 font-bold w-full">No files to show.</div>

	@else

		<div id="list" class="mt-4 mb-4 w-full">

			@include('shared-features.files.list')

		</div>

	@endif




@endsection

@section('javascript')
<script type="text/javascript">

	function getSearchData(v) {

		if (v.trim() == '') {
			$v = '';
			$('#paginate-div').removeClass('hidden');
		} else {
			$('#paginate-div').addClass('hidden');
		}

		return_string = '{!! $return_string !!}';

		$.get('/{{ Auth::user()->team->app_type }}/files/search/all/'+v+'/'+return_string, function(response) {

			$('#list').html(response);
		});
	}


	$(document).ready(function() {

	    $(document).on("click", "#main_upload_file", function() {
	        $("#upload_form").toggleClass('hidden');
	    });

		$("#search").focus();

		$("#search").keyup(function(){
			getSearchData(this.value);
		});

		$("#search").blur(function(){
			getSearchData(this.value);
		});

	});

</script>
@endsection
