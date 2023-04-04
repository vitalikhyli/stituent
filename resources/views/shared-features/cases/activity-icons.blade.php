@php
	$count_shared_cases = $thecase->sharedCases()->count();
	$count_people = $thecase->people()->where('is_household', false)->count();
	$count_households = $thecase->people()->where('is_household', true)->count();
	$count_contacts = $thecase->contacts()->count();
	$count_files = $thecase->files()->count();
@endphp

@for ($i=0; $i<$count_shared_cases; $i++)
	<i class="fa fa-handshake"></i>
@endfor
@for ($i=0; $i<$count_people; $i++)
	<i class="fa fa-user"></i>
@endfor
@for ($i=0; $i<$count_households; $i++)
	<i class="fa fa-home"></i>
@endfor
@for ($i=0; $i<$count_contacts; $i++)
	<i class="fa fa-comment"></i>
@endfor
@for ($i=0; $i<$count_files; $i++)
	<i class="fa fa-file"></i>
@endfor