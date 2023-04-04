<div class="p-2 font-normal text-sm text-right ">

      <span id="untilReload">*****</span>

      <span class="mr-1 text-grey-dark">{{ \Carbon\Carbon::now()->format("g:i:s a") }}</span>

        @if(session('startworker'))

            <button id="working" class="rounded-lg text-blue px-2 py-1">
                <i class="fas fa-hard-hat text-xl w-6 mr-2"></i> Starting worker...
            </button>

        @elseif($unfinished_workers > 0)

            @if($last_ping >= 5)
                <button id="working" class="border-red border rounded-lg text-red px-2 py-1">
                    Stalled? ...{{ $last_ping }} sec ago
                </button>
            @else
                <button id="working" class="border rounded-lg text-grey-dark px-2 py-1">
                    Working ...{{ $last_ping }} sec ago
                </button>
            @endif

            <button id="stopworker" class="bg-red-lighter hover:bg-orange-dark rounded-lg text-white px-2 py-1">
                Stop Worker
            </button>

        @else


            @if($jobs_to_do > 0)
                <button id="startworker" class="bg-blue hover:bg-orange-dark rounded-lg text-white px-2 py-1">
                    Start Worker
                    ({{ $jobs_to_do }} jobs to do)
                </button>

            @else
                 <button id="working" class="border rounded-lg text-grey-dark px-2 py-1">
                    No jobs waiting
                </button>
            @endif

        @endif
    </div>