<div>
    @foreach ($entities as $entity)
    	<div class="">
    		<a href="/{{ Auth::user()->app_type }}/organizations/{{ $entity->id }}">
	    		<i class="fa fa-hotel"></i> {{ $entity->name }}
	    	</a>
    	</div>
    @endforeach
</div>
