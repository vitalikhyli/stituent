       <div class="flex">


            <div class="w-full pr-1 table">
<!-- 
                <div class="border-b-2 border-black text-sm table-row text-xs uppercase">

                    <div class="border-r p-1 px-2 table-cell border-b">
                        Orgzd
                    </div>

                    <div class="border-r p-1 px-2 table-cell border-b">
                        Name
                    </div>

                    <div class="border-r p-1 px-2 table-cell border-b">
                        Office
                    </div>

                    <div class="border-r p-1 px-2 table-cell border-b text-center">
                        FYI
                    </div>

                    <div class="p-1 px-2 table-cell border-b text-center">
                        OCPF
                    </div>


                </div> -->

                @foreach($candidates as $candidate)
                    <div class="border-b text-sm table-row cursor-pointer {{ ($loop->iteration % 2) ? '' : 'bg-grey-lightest' }}">

                        <div class="border-r p-1 px-2 table-cell border-b text-right ">

                            @if($candidate->organized_at)
                                {{ \Carbon\Carbon::parse($candidate->organized_at)->format("n/j/y") }}
                            @else
                                {{ \Carbon\Carbon::parse($candidate->created_at)->format("n/j/y") }}
                            @endif

                        </div>

                                

                        <div class="border-r table-cell border-b">

                            @if($candidate->account_id)
                                <div class="text-xl pr-4 float-right text-blue-dark">
                                    <span class="text-sm font-medium upper pb-1">Client</span>
                                    <i class="fas fa-award"></i>
                                </div>
                            @endif

                            <div class="p-1 px-2
                            @if ($candidate->marketing->do_not_contact)
                                line-through
                                @if ($candidate->marketing->do_not_contact && !isset($_GET['no_voter_ids']))
                                    line-through opacity-50
                                @endif
                            @endif
                            ">

                                <span class="text-black">
                                    {{ $candidate->first_name }}
                                    {{ $candidate->last_name }}
                                </span>

                            </div>

                            <div class="flex flex-wrap mt-1 bg-grey-light">

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

                        <div class="border-r p-1 px-2 table-cell border-b">
                             {{ $candidate->districtOrCity }}

                            <span class="text-grey-darkest font-bold">
                                {{ $candidate->office }}
                            </span>

                             @if($candidate->theDistrict)
                                <span class="rounded bg-orange-lightest px-2 py-1 border text-xs">
                                    Current: {{ $candidate->theDistrict->elected_official_name }}
                                </span>
                             @endif

                        </div>


                        <div class="border-r p-1 px-2 table-cell border-b text-center">
                            <a href="http://www.candidatefyi.com/candidates/{{ $candidate->id }}" target="new">
                                <img src="https://candidatefyi.com/images/logo-2.svg" class="w-8" />
                            </a>
                        </div>


                        <div class="p-1 px-2 table-cell border-b text-center">
                            @if($candidate->form_link)
                                <a href="{{ $candidate->form_link }}" target="new">
                                    <div class="pt-2">
                                        <i class="w-8 far fa-file-pdf text-black text-xl"></i>
                                    </div>
                                </a>
                            @endif
                        </div>


                    </div>
                @endforeach

            </div>



        </div>
