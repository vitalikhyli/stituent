<div class="flex border-b w-full text-grey-dark text-sm p-1">

    <div class="w-1/2 flex">
        <div class="w-1/6 font-bold text-sm">

            {{ \Carbon\Carbon::parse($thecontact->date)->format("n/j/y") }}

        </div>

        <div class="w-1/6 text-grey-dark">
            @if(substr($thecontact->date,-8) != '00:00:00')
                {{ \Carbon\Carbon::parse($thecontact->date)->format("g:i a") }}
            @endif
        </div>

        <div class="w-4/6">
            {{ $thecontact->name }}
        </div>
    </div>
    <div class="w-1/2 flex">
        
        <div class="w-5/6 text-sm">
            <i>Subject:</i> <b class="text-grey-darkest">{{ $thecontact->subject }}</b>
        </div>

        <div class="w-1/6 text-right">
            <a href="/{{ Auth::user()->team->app_type }}/emails/{{ $thecontact->id }}/edit">
            <button class=" text-blue text-xs hover:bg-blue hover:text-white px-2 py-1">
                View
            </button>
            </a>
        </div>
    </div>

</div>

