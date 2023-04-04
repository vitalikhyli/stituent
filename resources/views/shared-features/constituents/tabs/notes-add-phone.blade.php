@if(!$person->primary_phone)

    @if (isset($detected_phones))
        @if($detected_phones->first())

            <div class="px-4 py-2 bg-red-lightest text-sm my-1">

                <div class="mb-2">
                    <span class="font-medium text-red">{{ $person->first_name }} has no primary phone</span> ...But, we found ({{ $detected_phones->count() }}) phone number{{ ($detected_phones->count() > 1) ? 's' : '' }} in the notes. Click on one to make it {{ $person->first_name }}'s primary phone.
                </div>

                <div class="flex flex-wrap">

                    @foreach($detected_phones as $phone)

                        <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/set_primary_phone/{{ base64_encode($phone) }}">
                            <div class="px-3 py-1 rounded-full text-xs border bg-white mr-2 uppercase hover:bg-blue hover:text-white cursor-pointer mb-1">
                                {{ $phone }}
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