@php
	$video = App\Video::where('slug', $slug)->first();
	$data = file_get_contents("http://vimeo.com/api/v2/video/".$video->vimeo_id.".json");
    $data = json_decode($data);
    $thumb = $data[0]->thumbnail_large;
@endphp

@if ($video)


<div x-data="{ open: false }">
	<div class="rounded-lg bg-cover cursor-pointer" style="padding-top:56.25%;position:relative;background-image:url({{ $thumb }})" @click="open = true">

		<div class="absolute pin-b pin-r px-3 py-1 font-bold text-blue bg-white border-l border-t rounded-tl-lg rounded-br-lg">
			{{ $video->length }}
		</div>
	</div>

    <template x-if="open" class="">
    	<div class="fixed pin z-50 overflow-auto md:flex items-center" style="background: rgba(0, 0, 0, 0.75);">

			<iframe @click.away="open=false" src="https://player.vimeo.com/video/{{ $video->vimeo_id }}" width="75%" height="75%" frameborder="0" allow="autoplay; fullscreen" allowfullscreen class="rounded-lg mx-auto" id="video"></iframe>
		</div>
	</template>

</div>

@endif