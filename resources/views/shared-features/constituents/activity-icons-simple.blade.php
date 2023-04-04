
@for ($i=0; $i<$person->cases->count(); $i++)
	<i class="fa fa-folder"></i>
@endfor
@for ($i=0; $i<$person->contacts->count(); $i++)
	<i class="fa fa-edit"></i>
@endfor
@for ($i=0; $i<$person->groups->count(); $i++)
	<i class="fa fa-tag"></i>
@endfor