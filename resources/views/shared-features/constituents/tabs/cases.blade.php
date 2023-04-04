<div class="text-xl font-sans pb-2 border-b-4 font-black h-16 w-full mt-8">
    CASES & FILES
    @isset ($cases)
        <div class="text-sm font-normal text-grey-dark">
            Your office has <b>{{ $cases->count() }}</b> cases involving {{ $person->name }}.
        </div>
    @else
        <div class="text-sm font-normal text-grey-dark">
            No Cases yet.
        </div>
    @endisset 
   
</div>

<div class="pr-8">

<div class="w-full text-right m-2">
 <a href="/{{ Auth::user()->team->app_type }}/cases/new/{{ $person->id }}">
        <button class="text-sm rounded-full border m-1 px-3 py-2">
            New Case
        </button>
    </a>

    <button id="upload-form-toggle" class="mr-1 text-sm rounded-full border m-1 px-3 py-2">
        Upload File
    </button>
</div>

<div class="bg-grey-lighter p-2 mr-2 hidden" id="upload-form">

    <form action="/{{ Auth::user()->team->app_type }}/files/upload/{{ base64_encode(json_encode(['person_id' => $person->id])) }}" method="post" enctype="multipart/form-data">
        
        @csrf

        <input type="file" name="fileToUpload" id="fileToUpload" class="opacity-0 z-99 float-right absolute" />

        <label for="fileToUpload" class="font-normal">
            <button type="button" class="bg-grey rounded-lg text-black px-4 py-1">
                Choose File
            </button>
        </label>


        <span id="file_name_display" class="text-black font-bold px-2">
        </span>

        <button type="submit" class="bg-blue rounded-lg text-white px-4 py-1 float-right">
        Go <span id="file_selected" class="font-bold"></span><i class="fas fa-file-upload ml-2"></i>
        </button>

    </form>

</div>

@if(isset($cases))
    @if ($cases)
        @if ($cases->first())

            @foreach($cases->where('resolved',false) as $thecase)
                <a href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}">
                    <div class="mb-2 pt-2">
                        <span class="text-grey-darkest">
                        <i class="fas fa-folder-open text-base mr-2 w-6 text-center"></i>
                        </span>{{ $thecase->subject}}
                        <span class="float-right text-sm text-grey-dark">
                        ({{$thecase->contacts->count() }} notes)
                        </span>
                    </div>
                </a>
            @endforeach
          
            @foreach($cases->where('resolved',true) as $thecase)
                <a href="/{{ Auth::user()->team->app_type }}/cases/{{ $thecase->id }}">
                    <div class="opacity-50 mb-2 pt-2">
                        <span class="text-grey-darkest text-sm">
                        <i class="fas fa-folder-open text-base mr-2 w-6 text-center"></i>
                        </span>{{ $thecase->subject}}
                        <span class="float-right text-sm text-grey-dark">
                        ({{$thecase->contacts->count() }} notes)
                        </span>
                    </div>
                </a>
            @endforeach
        @else
            <div class="text-grey-dark p-2">
                No cases
            </div>

        @endif
    @endif
@else
 
@endif

@if($shared_cases->count() > 0)

    <div class="text-xl font-sans mt-4 pb-1 border-b-4 font-black uppercase text-black">
        
        Shared Cases
        <span class="bg-red text-white px-2 py-1 text-xs uppercase">New!</span>
    </div>

    @foreach($shared_cases as $sc)
        <a href="/{{ Auth::user()->team->app_type }}/cases/{{ $sc->case->id }}">
            <div class="mb-2 pt-2 text-sm">
                <span class="text-grey-darkest">
                <i class="fas fa-folder-open text-base mr-2 w-6 text-center"></i>
                </span>{{ $sc->case->subject}} ({{ $sc->team->name }})
                <span class="float-right text-sm text-grey-dark">
                ({{$sc->case->contacts->count() }} notes)
                </span>
            </div>
        </a>
    @endforeach
          
@endif


@if ($person->files)
    @if($person->files->first())

        @foreach($person->files as $thefile)
            <a href="/{{ Auth::user()->team->app_type }}/files/{{ $thefile->id }}/edit">
                <button class="rounded-lg bg-grey-lighter text-grey-darkest px-2 py-1 text-xs float-right">Edit</button>
            </a>
            <a href="/{{ Auth::user()->team->app_type }}/files/{{ $thefile->id }}/download" target="new">
                <div class="{{ (\Carbon\Carbon::parse($thefile->created_at)->diffInSeconds() < 30) ? 'bg-orange-lightest' : '' }} mb-1 pt-1">
                    <span class="text-grey-darkest">
                    <i class="far fa-file mr-2 w-6 text-center"></i>
                    </span>{{ $thefile->name }}
                </div>
            </a>
        @endforeach

    @endif
@endif

</div>
