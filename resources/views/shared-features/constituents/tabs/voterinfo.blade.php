


@if(!$voterRecord)

    <div class="text-xl font-sans pb-2 border-b-4 font-bold h-16 text-grey-darker w-full mt-8 italic">
        NO VOTER ID
        <div class="text-sm font-normal text-grey-dark">
            This person was added to the database by staff.
        </div>
    </div>

@else

    <div class="text-xl font-sans pb-2 border-b-4 font-black h-16 w-full mt-8">
        VOTER DATA
        @if (!$voterRecord->elections)
            <div class="text-sm font-normal text-grey-dark">
                Offical state registration information.
            </div>
        @else
            <div class="text-sm font-normal text-grey-dark">
                {{ $person->name }} has voted in <b>{{ count($voterRecord->elections) }}</b> elections in our records.
            </div>
        @endif
    </div>

    <table class="text-sm w-full">
        <tr class="border-t font-semibold bg-orange-lightest">
            <td class="p-2 bg-grey-lighter uppercase text-xs w-1/4">
                State Voter ID
            </td>
            <td class="p-2">
                {{ $voterRecord->id }}
            </td>
        </tr>
        <tr class="border-t">
            <td class="p-2 bg-grey-lighter uppercase text-xs w-1/4">
                Registered
            </td>
            <td class="p-2">
                @if ($voterRecord->registration_date)
                    {{ $voterRecord->registration_date->format('n/j/Y') }}
                @endif
            </td>
        </tr>
        <tr class="border-t">
            <td class="p-2 bg-grey-lighter uppercase text-xs w-1/4 align-top">
                Election History
            </td>
            <td>

            @if ($voterRecord->elections)
                <table class="w-full">
                    
                    @foreach($voterRecord->elections_Pretty as $data)
                        <tr class="{{ ($data['off_year']) ? 'bg-grey-lighter' : '' }}">
                            <td class="p-1">
                                <i class="fas fa-check-square mr-1 text-blue"></i> 
                                <span class="font-bold">{{ $data['jurisdiction'] }}</span>
                                {{ $data['type'] }}
                            </td>
                            <td>
                                <span class="text-blue ml-1">{{ $data['date'] }}</span>
                            </td>
                            <td class="">
                                @if($data['registered_as'] == 'D')
                                <span class="text-blue">
                                @elseif($data['registered_as'] == 'R')
                                <span class="text-red">
                                @else
                                <span class="text-grey-dark">
                                @endif
                                    @if(!$data['registered_as'])
                                        -
                                    @else
                                        ({{ $data['registered_as'] }})
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span class="text-grey-dark">
                                    {{ $data['location'] }}
                                </span>
                            </td>
                            <td>
                                @if($data['voted_as'])

                                    @if($data['voted_as'] == 'D')
                                    <span class="text-blue">
                                    @elseif($data['voted_as'] == 'R')
                                    <span class="text-red">
                                    @else
                                    <span class="text-grey-dark">
                                    @endif
                                    
                                    Took {{ $data['voted_as'] }} Ballot

                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                </table>

            @endif
                                
                            
            </td>
        </tr>

        <tr class="border-t">
            <td class="p-2 bg-grey-lighter uppercase text-xs w-1/5">
                Party
            </td>
            <td class="p-2">
                {{ $voterRecord->partyFull }}
            </td>
        </tr>
        <tr class="border-t">
            <td class="p-2 bg-grey-lighter uppercase text-xs w-1/5">
                {{ ($voterRecord->ward) ? 'Ward' : '' }} 
                {{ ($voterRecord->ward && $voterRecord->precinct) ? ' - ' : '' }} 
                {{ ($voterRecord->precinct) ? 'Precinct' : '' }} 
            </td>
            <td class="p-2">
                {{ ($voterRecord->ward) ? $voterRecord->ward : '' }} 
                {{ ($voterRecord->ward && $voterRecord->precinct) ? ' - ' : '' }} 
                {{ ($voterRecord->precinct) ? $voterRecord->precinct : '' }} 
            </td>
        </tr>

    <!------------------------------------/ DISTRICT /------------------------------------>

        <tr class="border-t">
            <td class="p-2 bg-grey-lighter uppercase text-xs w-1/5">
                State House
            </td>
            <td class="p-2">  

                    @if($voterRecord->house_district == $person->house_district)


                        @if ($voterRecord->houseDistrict)

                            <div class="">

                                <div class="float-right">
                                    <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/district/house">Change</a>
                                </div>

                                <b>{{ $voterRecord->houseDistrict->name }}</b><br>
                                <span class="text-grey-dark">
                                    {{ $voterRecord->houseDistrict->elected_official_name }}
                                    ({{ $voterRecord->houseDistrict->elected_official_party_short }}
                                    - {{ $voterRecord->houseDistrict->elected_official_residence }})
                                    <i>since {{ $voterRecord->houseDistrict->elected_official_started }}</i>
                                </span>

                            </div>
                        @endif


                    @else

 
                        @if($person->houseDistrict)
                            <div class="">

                                <div class="float-right">
                                    <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/district/house">Change</a>
                                </div>

                                <b>{{ $person->houseDistrict->name }}</b><br>
                                <span class="text-grey-dark">
                                    {{ $person->houseDistrict->elected_official_name }}
                                    ({{ $person->houseDistrict->elected_official_party_short }}
                                    - {{ $person->houseDistrict->elected_official_residence }})
                                    <i>since {{ $person->houseDistrict->elected_official_started }}</i>
                                </span>

                            </div>
                        @endif


                        <div class="text-grey mt-2 border-t pt-1">

                            <div class="float-right">
                                <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/districtRevert/house">Use Voter File</a>
                            </div>

                            @if ($voterRecord->houseDistrict)
                                {{ $voterRecord->houseDistrict->name }}<br />
                                <span class="text-grey">
                                    {{ $voterRecord->houseDistrict->elected_official_name }}
                                    ({{ $voterRecord->houseDistrict->elected_official_party_short }}
                                    - {{ $voterRecord->houseDistrict->elected_official_residence }})
                                    <i>since {{ $voterRecord->houseDistrict->elected_official_started }}</i>
                                </span>
                            @endif

                        </div>

                    @endif

            </td>
        </tr>

    <!------------------------------------/ DISTRICT /------------------------------------>
    
        <tr class="border-t">
            <td class="p-2 bg-grey-lighter uppercase text-xs w-1/5">
                State Senate
            </td>
            <td class="p-2">  

                    @if($voterRecord->senate_district == $person->senate_district)


                        @if ($voterRecord->senateDistrict)
                            <div class="">

                                <div class="float-right">
                                    <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/district/senate">Change</a>
                                </div>

                                <b>{{ $voterRecord->senateDistrict->name }}</b><br />
                                <span class="text-grey-dark">
                                    {{ $voterRecord->senateDistrict->elected_official_name }}
                                    ({{ $voterRecord->senateDistrict->elected_official_party_short }}
                                    - {{ $voterRecord->senateDistrict->elected_official_residence }})
                                    <i>since {{ $voterRecord->senateDistrict->elected_official_started }}</i>
                                </span>

                            </div>
                        @endif


                    @else

 
                        @if($person->senateDistrict)
                            <div class="">

                                <div class="float-right">
                                    <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/district/senate">Change</a>
                                </div>

                                <b>{{ $person->senateDistrict->name }}</b><br />
                                <span class="text-grey-dark">
                                    {{ $person->senateDistrict->elected_official_name }}
                                    ({{ $person->senateDistrict->elected_official_party_short }}
                                    - {{ $person->senateDistrict->elected_official_residence }})
                                    <i>since {{ $person->senateDistrict->elected_official_started }}</i>
                                </span>

                            </div>
                        @endif


                        <div class="text-grey mt-2 border-t pt-1">

                            <div class="float-right">
                                <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/districtRevert/senate">Use Voter File</a>
                            </div>

                            @if ($voterRecord->senateDistrict)
                                {{ $voterRecord->senateDistrict->name }}<br>
                                <span class="text-grey">
                                    {{ $voterRecord->senateDistrict->elected_official_name }}
                                    ({{ $voterRecord->senateDistrict->elected_official_party_short }}
                                    - {{ $voterRecord->senateDistrict->elected_official_residence }})
                                    <i>since {{ $voterRecord->senateDistrict->elected_official_started }}</i>
                                </span>
                            @endif

                        </div>

                    @endif

            </td>
        </tr>

    <!------------------------------------/ DISTRICT /------------------------------------>
    
        <tr class="border-t">
            <td class="p-2 bg-grey-lighter uppercase text-xs w-1/5">
                Congress
            </td>
            <td class="p-2">  

                    @if($voterRecord->congress_district == $person->congress_district)


                        @if ($voterRecord->congressDistrict)
                            <div class="">

                                <div class="float-right">
                                    <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/district/congress">Change</a>
                                </div>

                                <b>{{ $voterRecord->congressDistrict->name }}</b><br>
                                <span class="text-grey-dark">
                                    {{ $voterRecord->congressDistrict->elected_official_name }}
                                    ({{ $voterRecord->congressDistrict->elected_official_party_short }}
                                    - {{ $voterRecord->congressDistrict->elected_official_residence }})
                                </span>

                            </div>
                        @endif


                    @else

 
                        @if($person->congressDistrict)
                            <div class="">

                                <div class="float-right">
                                    <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/district/congress">Change</a>
                                </div>

                                <b>{{ $person->congressDistrict->name }}</b><br>
                                <span class="text-grey-dark">
                                    {{ $person->congressDistrict->elected_official_name }}
                                    ({{ $person->congressDistrict->elected_official_party_short }}
                                    - {{ $person->congressDistrict->elected_official_residence }})
                                </span>

                            </div>
                        @endif


                        <div class="text-grey mt-2 border-t pt-1">

                            <div class="float-right">
                                <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/districtRevert/congress">Use Voter File</a>
                            </div>

                            @if ($voterRecord->congressDistrict)
                                {{ $voterRecord->congressDistrict->name }}<br>
                                <span class="text-grey">
                                    {{ $voterRecord->congressDistrict->elected_official_name }}
                                    ({{ $voterRecord->congressDistrict->elected_official_party_short }}
                                    - {{ $voterRecord->congressDistrict->elected_official_residence }})
                                </span>
                            @endif

                        </div>

                    @endif

            </td>
        </tr>


    </table>

@endif

