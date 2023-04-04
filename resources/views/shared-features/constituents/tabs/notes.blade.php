<div class="text-xl font-sans pb-2 border-b-4 font-black h-16 w-full mt-8">
    NOTES
    @isset($contacts)
        <div class="text-sm font-normal text-grey-dark">
            Your office has logged <b>{{ $contacts->count() }}</b> notes with {{ $person->name }}.
        </div>
    @else
        <div class="text-sm font-normal text-grey-dark">
            No Notes yet.
        </div>
    @endisset
</div>

<div class="w-full flex">


    <div class="w-1/2 p-4">



        @if((isset($contacts)) && ($contacts->first()))

            @foreach($contacts as $thecontact)


                <div class="legislation mb-4" 
                     year="
                        @if ($thecontact->created_at)
                            {{ $thecontact->created_at->format('Y') }}
                        @endif
                        ">
                    @include('shared-features.constituents.one-note')
                </div>

            @endforeach

        @else


        @endif
        
    </div>
    <div class="w-1/2">

        <div class="-mt-12">
            @include('shared-features.constituents.add-contact-form')
        </div>
        
    </div>



</div>


