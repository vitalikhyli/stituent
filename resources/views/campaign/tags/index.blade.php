@extends('campaign.base')

@section('title')
    Campaign Tags
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> > &nbsp;<b>Campaign Tags</b>
@endsection

@section('main')

	<div class="text-3xl font-bold border-b-4 pb-2">
		Campaign Tags
		@if($tags_count > 0)
			({{$tags_count}})
		@endif
		<span class="text-blue">*</span>
	</div>


	<div class="flex text-grey-dark w-full">
		<div class="w-2/3">
			<div class="text-center m-8">
				<button class="rounded-full bg-blue text-white py-4 text-xl font-thin tracking-wide uppercase px-8 hover:bg-blue-dark" data-toggle="modal" data-target="#add-tag">
						Add a Tag
				</button>
			</div>
		</div>
		<div class="w-1/3">
			<div class="p-2">
				<span class="text-blue text-2xl font-bold">*</span> Use <span class="font-bold text-black">Campaign Tags</span> to quickly organize your participants. Create custom tags, add them to your voters, and then build reports or lists from the tags.
			</div>
		</div>
	</div>

	@include('campaign.tags.tag-add-modal')

	@if($tags)
		<div class="flex mt-2 w-full">
			@foreach($tags as $tag)

				@if($loop->iteration % round($tags->count() /2 ,0) == 1 || $loop->first)
					<div class="w-1/2 pr-4">
						<!-- START -->
				@endif

				<div class="uppercase py-2 border-b {{ ($tag->is_new) ? 'bg-orange-lightest border' : '' }} w-full">
					<span class="mr-2 text-grey-dark text-sm">{{ $loop->iteration }}</span>
					<i class="text-center fa fa-tag text-sm mr-2 text-grey-darkest"></i> 
					<a href="/{{ Auth::user()->team->app_type }}/tags/{{ $tag->id }}">
						{{ $tag->name }}
						({{ $tag->participants_count }})
						@if($tag->is_new)
							<span class="font-bold text-grey-darker rounded-lg bg-grey-lightest border p-1 text-xs uppercase">Just Created</span>
						@endif
						<!-- {{ $loop->iteration % round($tags->count() /2 ,0) }} -->
					
					<a class="float-right" href="/{{ Auth::user()->team->app_type }}/tags/{{ $tag->id }}">
						Edit
					</a>
				</div>

				@if($loop->iteration % round($tags->count() /2 ,0) == 0 || $loop->last)
				<!-- END -->
					</div>
				@endif

			@endforeach
		</div>
	@endif

@endsection


@section('javascript')

	<script type="text/javascript">
		
		$(document).ready(function() {

			$('#add-tag').on('shown.bs.modal', function () {
			    $('#name').focus();
			})  

		});

	</script>

@endsection