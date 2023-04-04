<div class="text-xl font-sans pb-2 border-b-4 font-black h-16 w-full mt-8">
    BULK EMAILS
    @isset($bulk_emails)
        <div class="text-sm font-normal text-grey-dark">
            Your office has sent <b>{{ $bulk_emails->count() }}</b> bulk emails to {{ $person->name }}.
        </div>
    @else
        <div class="text-sm font-normal text-grey-dark">
            No Bulk Emails yet.
        </div>
    @endisset
</div>

<div class="w-full flex">


    <div class="w-full p-4">

        @if((isset($bulk_emails)) && ($bulk_emails->first()))

            @foreach($bulk_emails as $thecontact)


                <div class="">
                    @include('shared-features.constituents.one-bulkemail')
                </div>
                

            @endforeach

        @endif
        
    </div>



</div>


