<div class="mb-6">

    <div class="text-xl font-sans mt-4 pb-1 border-b-4 border-blue text-blue-dark">
        Organizations
    </div>

    @if ($person->entities)
    @foreach($person->entities as $entity)
        <div id="relationship_{{ $person->id }}" class="w-full {{ (!$loop->last) ? 'border-b' : '' }} py-1">



            <a href="/{{ Auth::user()->team->app_type }}/entities/{{ $entity->id }}">
                <div class="flex w-full">
                    <div class="">
                        <i class="fas fa-building mr-1 text-grey"></i>
                    </div>
                    <div class="ml-2">

                        <div class="font-bold flex-initial text-black capitalize">
                            {{ $entity->pivot->relationship }}
                        </div>

                        {{ $entity->name }}
                    </div>
                </div>
            </a>

      

        </div>
    @endforeach
    @endif

</div>