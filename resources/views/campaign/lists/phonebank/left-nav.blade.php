
<div id="left-nav" class="md:w-1/5 items-center text-lg font-sans md:pr-8 pr-4 md:pl-0 pl-4">

    <div>

        <div class="text-lg uppercase mb-1 rounded bg-blue text-white px-2 py-1">
            Welcome!
        </div>

        <div class="truncate  text-base font-medium">
            {{ Auth::user()->name }}
        </div>

        <div class="truncate text-base text-grey-darker">
            {{ Auth::user()->email }}
        </div>

    </div>

<!--     <div class="mt-4">

        <div class="text-lg uppercase mb-1 rounded bg-blue text-white px-2 py-1">
            Your Stats
        </div>
        <div class="text-4xl text-bold">
            Coming Soon
        </div>
    </div> -->

    <div class="mt-6">

        <div class="text-lg uppercase mb-1 rounded bg-blue text-white px-2 py-1">
            Script / Instructions
        </div>
   
        <div class="text-base text-gray-500">

            @if(!$list->script)

                No script loaded.

            @else

                {!! $list->scriptFormatted !!}
                
                <!-- <a href="text-blue">Full Script</a> -->

            @endif

        </div>

    </div>

    <div class="mt-6">

        <div class="text-lg uppercase mb-1 rounded bg-blue text-white px-2 py-1">
            My Lists
        </div>
   
        <div class="my-2">
            @foreach(\App\CampaignListUser::where('user_id', Auth::user()->id)->get() as $assign)

                @if (!$assign->list)
                    @continue
                @endif
                <div class="text-base flex {{ (!$loop->last) ? 'border-b' : '' }} border-grey">



                    @if(\Carbon\Carbon::parse($assign->expires_at)->isFuture())


                        <div class="flex-grow truncate">
                            <a href="/{{ Auth::user()->team->app_type }}/phonebank/{{ $assign->list->id }}">
                                {{ $assign->list->name }}
                            </a>
                        </div>

                        <div class="text-xs">
                            <button class="text-xs rounded-lg bg-grey-lightest text-grey-darker px-2 py-1">
                                Open
                            </button>
                        </div>

                    @else

                        <div class="flex-grow truncate line-through opacity-75">
                            {{ $assign->list->name }}
                        </div>

                        <div class="text-xs">
                            <button class="rounded-lg bg-grey-darker text-grey px-2 py-1">
                                Closed
                            </button>
                        </div>
                            
                    @endif
                </div>
            @endforeach
        </div>

    </div>



</div>