       <div class="flex">


            <div class="w-full pr-1 table">

                <div class="border-b-2 border-black text-sm table-row text-xs uppercase">



                    <div class="border-r p-1 table-cell border-b">
                        Orgzd
                    </div>

                    <div class="border-r p-1 table-cell border-b">
                        Name
                    </div>

                    <div class="border-r p-1 table-cell border-b">
                        Office
                    </div>

                    <div class="border-r p-1 table-cell border-b">
                        Email?
                    </div>

                    <div class="p-1 table-cell border-b">
                        OCPF
                    </div>


                </div>

                @foreach($candidates as $candidate)
                    <div class="border-b text-sm table-row cursor-pointer {{ ($loop->iteration % 2) ? '' : 'bg-grey-lightest' }}">

                        <div class="border-r p-1 table-cell border-b text-right 
                            @if ($candidate->marketing->do_not_contact)
                                line-through
                                @if ($candidate->marketing->do_not_contact && !isset($_GET['no_voter_ids']))
                                    line-through opacity-50
                                @endif
                            @endif
                         ">

                            @if($candidate->organized_at)
                                {{ \Carbon\Carbon::parse($candidate->organized_at)->format("n/j/y") }}
                            @else
                                {{ \Carbon\Carbon::parse($candidate->created_at)->format("n/j/y") }}
                            @endif

                            <div>
                                <a href="/admin/marketing/{{ $candidate->id }}/edit" class="font-medium text-xs">
                                    EDIT
                                </a>
                            </div>

                        </div>


                                

                        <div class="border-r table-cell border-b">

                            @if($candidate->account_id)
                                <div class="text-xl pr-4 float-right text-blue-dark">
                                    <span class="text-sm font-medium upper pb-1">Client</span>
                                    <i class="fas fa-award"></i>
                                </div>
                            @endif

                            <div class="p-1
                            @if ($candidate->marketing->do_not_contact)
                                line-through
                                @if ($candidate->marketing->do_not_contact && !isset($_GET['no_voter_ids']))
                                    line-through opacity-50
                                @endif
                            @endif
                            ">



                                <span class="font-bold text-black">
                                    {{ $candidate->first_name }}
                                    {{ $candidate->last_name }}
                                </span>

                                @if($candidate->party)
                                    <span class="underline">
                                        ({{ $candidate->shortParty }})
                                    </span>
                                @endif

                                <div class="text-grey-dark">
                                    {{ $candidate->full_address }}
                                </div>

                                @if($candidate->voter)
                                    <div class="py-1 text-blue ml-2 font-mono text-sm hover:underline">
                                        <i class="fas fa-user-check"></i> 
                                        @if(Auth::user()->team->app_type == 'campaign')
                                        <a href="/campaign/participants/{{ $candidate->voter_id }}"
                                           target="new">
                                        @else
                                        <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $candidate->voter_id }}"
                                           target="new">
                                        @endif
                                            {{ $candidate->voter->id }}
                                            {{ $candidate->voter->gender }}
                                            {{ $candidate->voter->age }}
                                            {{ $candidate->voter->party }}
                                        </a>
                                    </div>

                                @else

                                    @if(isset($_GET['no_voter_ids']))
                                        <div class="py-2">
                                            @foreach($candidate->votersWithThisName() as $voter)

                                                <div class="flex">

                                                <div class="p-2 w-10 font-mono">
                                                    {{ $voter->match_score }}%
                                                </div>

                                                <a href="/admin/marketing/link_candidate/{{ $candidate->id }}/{{ $voter->id }}">
                                                    <button class="font-mono hover:bg-blue-darker rounded-lg bg-blue text-white px-3 py-2 shadow ml-2 mb-2">
                                                        
                                                       <div class="flex">
                                                            <div class="pr-4 font-normal border-r border-white text-xs">
                                                                Click to Link:
                                                            </div>
                                                            <div class="pl-4 text-left">
                                                                <div class="font-bold flex">
                                                                    {{ $voter->full_name }} ({{ $voter->age }}) {{ $voter->gender }}
                                                                </div>
                                                                {{ $voter->full_address }}
                                                            </div>

                                                        </div>
                                                        
                                                    </button>
                                                </a>

                                                </div>

                                            @endforeach
                                        </div>

                                    @endif

                                @endif

                            </div>

                            <div class="flex flex-wrap mt-1 bg-grey-light
                            @if ($candidate->marketing->do_not_contact)
                                line-through
                                @if ($candidate->marketing->do_not_contact && !isset($_GET['no_voter_ids']))
                                    line-through opacity-50
                                @endif
                            @endif
                            ">

                                @foreach($candidate->contacts()->orderBy('created_at')->get() as $contact)
                                    <div class="inline text-center w-10 text-xs bg-blue rounded border-grey-dark px-1 text-white border-r">
                                        {{ \Carbon\Carbon::parse($contact->created_at)->format('n/j') }}
                                    </div>
                                @endforeach

                                @if($candidate->next_email)
                                    <div class="inline text-center text-xs bg-orange rounded border-grey-dark px-1 text-white border-r">
                                        Next: {{ \Carbon\Carbon::parse($candidate->next_email)->format('n/j') }}
                                    </div>
                                @endif

                            </div>

                        </div>

                        <div class="border-r p-1 table-cell border-b
                            @if ($candidate->marketing->do_not_contact)
                                line-through
                                @if ($candidate->marketing->do_not_contact && !isset($_GET['no_voter_ids']))
                                    line-through opacity-50
                                @endif
                            @endif
                        ">
                             {{ $candidate->districtOrCity }}
                             @if($candidate->theDistrict)
                                <span class="rounded bg-orange-lightest px-2 py-1 border text-xs">
                                    Current: {{ $candidate->theDistrict->elected_official_name }}
                                </span>
                             @endif
                            <div class="text-grey-dark">{{ $candidate->office }}</div>

                        </div>


                        <div class="border-r p-1 table-cell border-b 
                            @if ($candidate->marketing->do_not_contact)
                                line-through
                                @if ($candidate->marketing->do_not_contact && !isset($_GET['no_voter_ids']))
                                    line-through opacity-50
                                @endif
                            @endif
                        ">
                            @if($candidate->anyEmail)
                                <div class="pt-2 text-center text-lg text-blue whitespace-no-wrap">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            @else
                                <div class="pt-2 text-center text-sm text-red whitespace-no-wrap">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </div>
                            @endif
                        </div>


                        <div class="p-1 table-cell border-b text-center 
                            @if ($candidate->marketing->do_not_contact)
                                line-through
                                @if ($candidate->marketing->do_not_contact && !isset($_GET['no_voter_ids']))
                                    line-through opacity-50
                                @endif
                            @endif
                        ">
                            @if($candidate->form_link)
                                <a href="{{ $candidate->form_link }}" target="new">
                                    <button class="rounded-lg text-black px-2 py-1 text-xl">
                                        <i class="far fa-file-pdf"></i>
                                    </button>
                                </a>
                            @endif
                        </div>


                    </div>
                @endforeach

            </div>



        </div>
