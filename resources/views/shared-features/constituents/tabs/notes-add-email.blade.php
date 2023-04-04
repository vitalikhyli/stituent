@if(!$person->primary_email)

    @if (isset($detected_emails))
        @if($detected_emails->first())

            <div class="px-4 py-2 bg-red-lightest text-sm my-1">

                <div class="mb-2">
                    <span class="font-medium text-red">{{ $person->first_name }} has no primary email</span> ...But, we found ({{ $detected_emails->count() }}) email address{{ ($detected_emails->count() > 1) ? 'es' : '' }} in the notes. Click on one to make it {{ $person->first_name }}'s primary email.
                </div>

                <div class="flex flex-wrap">

                    @foreach($detected_emails as $email)

                        <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/set_primary_email/{{ base64_encode($email) }}">
                            <div class="px-3 py-1 rounded-full text-xs border bg-white mr-2 uppercase hover:bg-blue hover:text-white cursor-pointer mb-1">
                                {{ $email }}
                            </div>
                        </a>

                    @endforeach

                </div>

            </div>

        @else

            <span class="text-grey text-sm">No primary set</span>

        @endif
    @endif

@endif