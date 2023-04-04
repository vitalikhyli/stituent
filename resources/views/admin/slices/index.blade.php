@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')


	{!! Auth::user()->Breadcrumb('Import', 'import', 'level_1') !!}


@endsection

@section('style')

@endsection

@section('main')



<div class="text-xl border-b-4 border-red py-2">

    <div class="float-right text-base">

        <form action="/admin/slices"
              method="post">

            @csrf

            <button class="rounded-lg bg-blue text-white px-4 py-1">
                New Slice
            </button>

        </form>

    </div>

    VoterFiles in Database ({{ $slices->count() }})
</div>  

<div class="py-2 italic mb-4 text-right text-sm">
    The "slices" table is a listing of voter slices / voter files.<br />
    The actual slices/files are each their own separate tables.
</div>


<div class="w-full text-sm">

    @foreach($slices->groupBy('state') as $state => $thetables)

        <div x-data="{ open: false }" class="p-2">

            <div class="flex border-b cursor-pointer hover:bg-orange-lightest"
                 @click="open = !open">

                <button type="button"
                        class="text-white rounded-lg px-2 py-1 bg-blue text-sm mr-2">
                    <span x-show="open">Close</span>
                    <span x-show="!open">Open</span>
                </button>

                <div class="w-32 text-2xl font-bold p-2">
                    {{ $state }}
                </div>

                <div class="w-32 text-2xl text-grey-darker p-2">
                    {{ $thetables->count() }}
                </div>


            </div>

            <div
                x-show="open"
                @click.away="open = false"
                class="mt-2 pl-8 border-l-4 border-blue ml-10">

                <div class="border-b flex bg-grey-lighter border-b border-grey-dark uppercase text-sm text-grey-darkest"> 

                    <div class="p-1 w-16">
                        
                    </div>

                    <div class="p-1 w-1/4">
                        Name
                    </div>

                    <div class="p-1 w-24">
                        Master
                    </div>

                    <div class="p-1 w-64">
                        SQL
                    </div>

                    <div class="p-1 text-right w-12">
                        Teams
                    </div>

                    <div class="p-1 text-right w-24">
                        Voters
                    </div>

                    <div class="p-1 flex-grow text-right">
                        Updated
                    </div>

                </div>

                @foreach($thetables->sortBy('name') as $thetable)

                    @if(!$thetable->orphaned)

                        <div class="border-b flex"> 

                            <div class="p-1 w-16 text-grey-dark whitespace-no-wrap text-xs">
                                <a href="/admin/slices/{{ $thetable->id }}/edit">
                                    <button class="p-1 hover:text-white hover:bg-blue rounded-lg">
                                        <i class="fas fa-search"></i> <span class="mt-1 uppercase">Edit</span>
                                    </button>
                                </a>
                            </div>

                            <div class="p-1 w-1/4 truncate">

                                @if(!$thetable->table_exists)

                                    <div class="bg-red text-white p-1 truncate">
                                        "{{ $thetable->name }}" not found in DB
                                    </div>

                                @else

                                    {{ $thetable->name }}

                                @endif

                            </div>


                            <div class="p-1 w-24">
                                @if($thetable->master)
                                    <span class="text-blue">{{  str_replace('x_voters_', '', $thetable->master) }}</span>
                                @else
                                    <span class="text-red">None</span>
                                @endif
                            </div>

                            <div class="p-1 w-64">
                                @if($thetable->sql)
                                    <input type="text"
                                           value="{{ $thetable->sql }}"
                                           class="w-full p-1 border" />
                                @else
                                    <span class="text-red">None</span>
                                @endif
                            </div>

                            <div class="p-1 whitespace-no-wrap">
                                @if($thetable->teams->first())
                                    @foreach ($thetable->teams as $t)
                                        {{ $t->name }}<br>
                                    @endforeach
                                @else
                                    <span class="text-red">None</span>
                                @endif
                            </div>

                            <div class="p-1 text-right w-24">
                                @if($thetable->voters_count < 1000)
                                    {{ number_format($thetable->voters_count) }}
                                @else
                                    {{ number_format($thetable->voters_count/1000) }} k
                                @endif
                            </div>

                            <div class="p-1 flex-grow text-grey-dark text-xs text-right">
                                {{ \Carbon\Carbon::parse($thetable->updated_at)->format('n/j/y') }}
                            </div>

                        </div>

                    @else

                        <div class="border-b bg-red text-white flex">

                            <div class="p-1 w-16 text-grey-dark whitespace-no-wrap text-xs">

                            </div>

                            <div class="p-1">
                                {{ $thetable->name }} -- Table exists but voter_slices has no corresponding row
                            </div>

                        </div>

                    @endif

                @endforeach

            </div>

        </div>

        

    @endforeach

</div>



@endsection



@section('javascript')


<script type="text/javascript">


</script>

@endsection