

<div class="text-xl font-sans pb-2 border-b-4 font-black h-16 w-full mt-8">
    GROUPS
    <div class="text-sm font-normal text-grey-dark">
        Your office has <b>{{ $person->groups()->count() }}</b> groups associated {{ $person->name }}.
    </div>
</div>
   

<div>
    @php
        if (is_numeric($person->id)) {
            $gp = \App\Person::find($person->id);
        } else {
            $gp = \App\Voter::find($person->id);   
        }
    @endphp
    @livewire('groups.constituent', ['group_person' => $gp])
</div>


@if(isset($groupcats))


    <div class="">

        @foreach($groupcats as $thecat)

            <div class="w-full">
                <div class="text-grey-darkest">

                    

                    @if (!($person->groups() instanceof \Illuminate\Support\Collection))

                    @foreach($person->groups()->where('category_id', $thecat->id)->orderBy('name')->get() as $thegroup)

                        @if ($loop->first)
                        <div class="text-base py-1 pl-1 cursor-pointer border-b bg-grey-lighter">

                            <span class="text-xs pl-2 uppercase">{{ $thecat->name }}</span>

                        </div>
                        @endif

                        
                        <div class="p-2 {{ ($thegroup->archived_at) ? 'opacity-25' : '' }} {{ (!$loop->last) ? 'border-b' : '' }}">

                            <a href="/{{ Auth::user()->team->app_type }}/groups/instance/{{ $thegroup->pivot->id }}">
                                <div class="float-right text-sm">
                                    Edit
                                </div>
                            </a>

                            @if ($thegroup->pivot->created_at > \Carbon\Carbon::today())
                                <div class="float-right mr-2 text-sm text-green-400">
                                    Added Today!
                                </div>
                            @endif

                            <a class="legislation font-bold text-base" href="/{{ Auth::user()->team->app_type }}/groups/{{ $thegroup->id }}"
                                year="{{ $thegroup->created_at->format('Y') }}">
                                
                                @if(!$thegroup->archived_at)

                                    <i class="fas fa-tag mr-2 w-4 text-blue-400"></i> {{ $thegroup->name }}

                                @else

                                    <i class="fas fa-times mr-2 w-4"></i> <span class="text-red">{{ $thegroup->name }} (Archived)</span>

                                @endif

                            </a>
                            <div class="text-grey-dark p-2">
                                @if(isset($thegroup->pivot->position))
                                    <div class="border-l-2 pl-6">
                                        Position: <span class="font-bold capitalize">{{ $thegroup->pivot->position }}</span>
                                    </div>
                                @endif

                                @if(isset($thegroup->pivot->title))
                                    <div class="border-l-2 pl-6">
                                        Title: <span class="font-bold capitalize">{{ $thegroup->pivot->title }}</span>
                                    </div>
                                @endif

                                @if(isset($thegroup->pivot->notes))
                                    <div class="border-l-2 pl-6">
                                        {{ $thegroup->pivot->notes }}
                                    </div>
                                @endif
                            </div>

                        </div>

                     
                        

                    @endforeach
            
                    @endif
                    


                </div>
            </div>
        @endforeach

    </div>
@else

    <!-- No groups yet. -->
    
@endif




