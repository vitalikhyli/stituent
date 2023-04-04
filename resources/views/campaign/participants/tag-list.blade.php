<div class="flex-grow flex text-xs pt-1">

	@if(\App\Tag::thisTeam()->first())

		<div class="text-grey-dark pt-2 mt-1 italic">
			Apply tag:
		</div>

		@php
			$query = \Request::query();
			unset($query['tag_with']);
			$query = http_build_query($query);
		@endphp

		@foreach(\App\Tag::thisTeam()->get() as $tag)

			@if(isset($tag_with) && $tag_with->id == $tag->id)
			<a href="?{{ $query }}&tag_with=">
				<div class="bg-blue text-grey-lightest p-2 mt-1 uppercase">
					<i class="text-center fa fa-tag ml-1"></i> {{ $tag->name }}
				</div>
			</a>
			@else
			<a href="?{{ $query }}&tag_with={{ $tag->id }}">
				<div class="p-2 mt-1 uppercase">
					<i class="text-center fa fa-tag ml-1"></i> {{ $tag->name }}
				</div>
			</a>
			@endif

		@endforeach

	@endif

</div>

