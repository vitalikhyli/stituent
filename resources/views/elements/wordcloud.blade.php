<div class="p-2 px-8 text-grey-dark text-center overflow-y-scroll">

@php
	$groupsize=10;
	$colors = ['grey font-light',
			   'grey font-medium',
			   'grey-dark font-light',
			   'grey font-light',
			   'blue',
			   'blue-dark',
			   'blue-darker font-medium',
			   'blue-darker font-black italic'];
	$new = [];
@endphp

@foreach($thecloud->shuffle() as $chunk)

    @foreach($chunk as $key => $size)
    
    <?php
    $new[] ='
        <span style="font-size:'.$groupsize.'px" class="text-'.current($colors).' uppercase mr-2">'.$key.'</span>';
    ?>

    @endforeach

	<?php
		$groupsize = $groupsize + 4;
		next($colors);
	?>
@endforeach

@foreach(collect($new)->shuffle() as $word)
	{!! $word !!}
@endforeach


</div>
