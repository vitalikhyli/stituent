<!-- <div class="text-xl font-sans mt-4 pb-1 border-b-4 border-blue text-blue-dark">
    General
</div> -->

<table class="text-sm w-full border-b">

    @if ($person->nickname)
    <tr class="border-t">
        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
            Voter File Name
        </td>
        <td class="p-2">
            {{ $person->getAttributes()['first_name'] }} 
            <span class="text-gray-500">
                (preferred name: {{ $person->first_name }})
            </span>
        </td>
    </tr>
    @endif

    <tr class="border-t">
        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
            Address

        </td>
        <td class="p-2">
            <div>
                @if ($person->full_address)
                    <a class="float-right" target="_blank" href="https://www.google.com/maps/place/{{ urlencode($person->full_address) }}">
                        <i class="fa fa-map-pin"></i> map
                    </a>
                @endif

                @if($person->household_id)

                        <div class="mr-2 flex-initial text-sm">
                            
                            {{ $person->full_address }}
                        </div>

                @else
                    <div class="mr-2 flex-initial text-sm">
                        
                        {{ $person->full_address }}
                    </div>
                @endif
                
            </div>
            <div class="text-grey-dark text-sm">
                @if($person->mailing_address && str_replace(',', '', $person->mailing_address) != str_replace(',', '', $person->full_address))
                    Mailing: {{ $person->mailing_address }}
                @endif
            </div>

            @if ($person->voter)
                @if ($person->household_id != $person->voter->household_id)
                    @if (strtoupper($person->address_number) != strtoupper($person->voter->address_number)
                      || strtoupper($person->address_street) != strtoupper($person->voter->address_street)
                      || strtoupper($person->address_city) != strtoupper($person->voter->address_city))

                        <div class="text-red text-sm border-rounded border mt-5 p-2">
                            <div class="-mt-5 bg-white w-3/5 px-2 ml-2">
                                VOTER FILE 
                                @if($person->voter->updated_at)
                                    ({{ $person->voter->updated_at->format('n/j/Y') }})
                                @endif
                            </div>
                            <span class="text-grey-dark">
                                {{ $person->voter->full_address }}
                            </span>

                            <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/sync_voter_address">
                                <button class="float-right rounded-lg bg-grey hover:bg-grey-dark text-xs text-white px-2 py-1">
                                    SYNC
                                </button>
                            </a>

                        </div>


                        

    
                    @endif
                @endif
            @endif
    

        </td>
    </tr>
    <tr class="border-t">
        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
            @if($cohabitors && $cohabitors->first())

                {{ $cohabitors->count() }} Other Residents
                @if($cohabitors->count() > 2)
                    <div id="show-cohabitors" class="text-blue whitespace-no-wrap cursor-pointer">
                        Hide/Show
                    </div>
                @endif

            @else

                Other Residents
                
            @endif 
        </td>
        <td class="p-2">

            <div id="cohabitors" class="{{ ($cohabitors && $cohabitors->count() > 2) ? 'hidden' : '' }}">

            @if (!$cohabitors)
                <i class="text-grey">None Found</i>
            @else

                @foreach($cohabitors as $theperson)

                    @if($theperson->external || $theperson->voter)

                        <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $theperson->id}}">
                            <div class="flex-1 flex-initial m-1 bg-grey-lighter px-2 py-1 text-blue-dark rounded-full mr-2 text-sm">
                                <i class="fas fa-unlink text-sm mr-2"></i>
                                {{ $theperson->full_name }}

                                @if($theperson->mightBeArchived())
                                    <span class="float-right text-grey-dark font-medium text-xs">former resident?</span>
                                @endif

                                @if($theperson->voter && $theperson->voter->archived_at)
                                    <span class="float-right text-grey-dark font-medium text-xs">former resident?</span>
                                @endif

                            </div>
                        </a>

                    @else

                        <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $theperson->id}}">
                            <div class="flex-1 flex-initial m-1 bg-orange-lightest border px-2 py-1 text-blue-dark rounded-full mr-2 text-sm hover:bg-grey-lighter">
                                <i class="fas fa-user-circle mr-2"></i>
                                {{ $theperson->full_name }}
                            </div>
                        </a>

                    @endif
                @endforeach

            @endif

            </div>

        </td>
    </tr>

    <tr class="border-t">
        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
            Born / Gender
        </td>
        <td class="p-2">

            @if ($person->pronouns)
                <div class="float-right">
                    {{ $person->pronouns }}
                </div>
            @endif

            @if($person->dob)
                {{ \Carbon\Carbon::parse($person->dob)->format("M j, Y") }}
                (age {{ \Carbon\Carbon::parse($person->dob)->diffInYears() }})
            @elseif ($person->yob)
                {{ $person->yob }} ({{ $person->age }})
            @elseif ($person->voter)
                {{ $person->voter->yob }} ({{ $person->voter->age }})
            @else
                <span class="text-grey">Unknown</span>
            @endif
            /
            @if($person->gender)
                {{ $person->gender }}
            @endif
        </td>
    </tr>


    <tr class="border-t">
        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs  w-32">
            General Notes
        </td>
        <td class="py-2 pl-3 text-sm">
            @if(!$person->private)
                <span class="text-grey-dark">
                    None
                </span>
            @else
                {!! nl2br($person->private) !!}
            @endif
        </td>
    </tr>


</table>