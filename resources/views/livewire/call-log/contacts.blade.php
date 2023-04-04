<div>
    
    <div class="border-t border-dashed border-blue pt-4">

        @if(!$loaded)
                
            <i class="fa fa-spinner fa-spin mr-2"></i> Loading...

        @else

            @if($contacts instanceof \Illuminate\Pagination\LengthAwarePaginator )
                {{ $contacts->links() }}
            @endif


            <div class="text-sm" wire:loading.class="opacity-25 transition ease-in-out">

                @php($times = [])

                @foreach($contacts as $contact)

                    @if($contact->date->diffInDays() == 0)
                        @if(!in_array('today', $times))
                            @php($times[] = 'today')
                            <div class="text-2xl pt-4 rounded-lg px-4 py-1 text-center text-blue">
                                Today
                            </div>
                        @endif
                    @elseif($contact->date->diffInWeeks() == 0)
                        @if(!in_array('week', $times))
                            @php($times[] = 'week')
                            <div class="text-2xl pt-4 rounded-lg px-4 py-1 text-center text-blue">
                                This Week
                            </div>
                        @endif
                    @elseif($contact->date->diffInMonths() == 0)
                        @if(!in_array('month', $times))
                            @php($times[] = 'month')
                            <div class="text-2xl pt-4 rounded-lg px-4 py-1 text-center text-blue">
                                This Month
                            </div>
                        @endif
                    @else
                        @if(!empty($times) && !in_array('earlier', $times))
                            @php($times[] = 'earlier')
                            <div class="text-2xl pt-4 rounded-lg px-4 py-1 text-center text-blue">
                                Earlier
                            </div>
                        @endif
                    @endif


                    <div class="p-2 mb-2 flex w-full
                                @if($contact->created_at->diffInSeconds() < 60)
                                    bg-yellow-lightest
                                @endif
                                "
                                wire:key="main_{{ $contact->created_at }}_{{ $contact->id }}">

                        <div class="w-1/6 text-right pr-4 border-r-4 mr-4">

                            <div class="font-bold text-base">
                                {{ $contact->date->format('n.j.y') }}
                            </div>

                            <div>
                                {{ $contact->date->format('g:i a') }}
                            </div>

                            <div class="font-medium text-grey-dark">
                                @if ($contact->private)
                                    <span class="cursor-pointer">
                                        <i class="fa fa-lock text-blue mr-1"></i>
                                    </span>
                                @endif

                                @if (isset($contact->user))
                                    {{ $contact->user->name }}
                                @endif
                            </div>


                        </div>

                        <div class="w-5/6">

                            <div class="flex">

                                <div class="flex w-2/3">

                                    <div class="flex-grow">

                                        <!-- {{ $contact->date->diffForHumans() }} -->
                                        
                                        <div class="text-lg text-blue cursor-pointer"
                                             wire:click="showContact({{ $contact->id }})">
                                            @if($contact->type)
                                                <span class="text-black font-bold">
                                                    {{ $contact->type }} >
                                                </span>
                                            @endif
                                            @if(!$search_term)
                                                {{ $contact->subject }}
                                            @else
                                                {!! str_ireplace($search_term, '<span class="bg-yellow-light text-black">'.$search_term.'</span>', $contact->subject) !!}
                                           
                                            @endif
                                        </div>

                                        <div class="py-2"
                                             wire:click="showContact({{ $contact->id }})">
                                            {!! $contact->notesRegex !!}
                                        </div>

                                    </div>

                                </div>

                                <div class="w-1/3">

                                    <div class="px-3 py-1">
                                        @if($contact->people->first())
                                            @foreach($contact->people as $theperson)
                                                <div class="ml-2 py-1 whitespace-nowrap">
                                                    <a href="/{{ $theperson->team->app_type }}/constituents/{{ $theperson->id }}" class="hover:font-bold">
                                                        <div class="flex">
                                                            <i class="fas fa-link mr-1 text-grey"></i><i class="fas fa-user mr-2 text-grey-dark"></i> 
                                                            <div>
                                                                {{ $theperson->full_name }}
                                                                <div class="text-blue text-xs">
                                                                    {{ $theperson->primary_email }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-grey ml-2">Not linked to a constituent</span>
                                        @endif
                                    </div>

                                </div>

                                <div class="py-2 w-10 text-right">

                                    <button
                                       class="border hover:bg-blue-dark hover:border-blue-dark rounded-full bg-white hover:text-white px-4 py-1 text-xs mb-2"
                                       wire:click="showContact({{ $contact->id }})">
                                            <i class="fas fa-edit"></i>
                                    </button>

                                    @if(!$contact->case)
                                        <a data-toggle="tooltip"
                                           data-placement="top"
                                           title="Covert to Case"
                                           href="/{{ Auth::user()->team->app_type }}/contacts/{{ $contact->id }}/convert_to_case"
                                           class="border hover:bg-blue-dark hover:border-blue-dark rounded-full bg-white hover:text-white px-4 py-1 text-xs">
                                                <i class="fas fa-folder"></i> 
                                        </a>
                                    @else
                                        <a data-toggle="tooltip"
                                           data-placement="top"
                                           title="Go to Case to Case"
                                           href="/{{ Auth::user()->team->app_type }}/cases/{{ $contact->case->id }}"
                                           class="bg-blue text-white border hover:text-white hover:bg-blue-dark rounded-full px-4 py-1 text-xs">
                                                <i class="fas fa-folder"></i>
                                        </a>
                                    @endif

                                </div>

                            </div>

                            <div class="">
                                
                                <!---------- LINK EMAILS ---------------------->

                                @if($contact->emails && count($contact->emails) > 0 && $contact->people()->first())
                                    <div class="rounded-lg mt-2 py-1 w-full
                                                @if(!$contact->emails && count($contact->emails) <= 0)
                                                    hidden
                                                @endif
                                               "
                                               wire:key="emails_{{ $contact->created_at }}_{{ $contact->id }}">

                                        <div class="text-blue">
                                            Set as primary email?
                                        </div>

                                        <div class="w-full mt-2">

                                            @foreach($contact->emails as $email)
                                                <div class="text-xs mr-2 uppercase cursor-pointer flex {{ (!$loop->last) ? 'border-b' : '' }}"
                                                    wire:key="{{ $contact->id.'_'.$email }}">

                                                    <div class="bg-grey-lightest border-l-2 border-r-2 px-2 py-2 text-xs truncate w-1/4 mr-2">
                                                        {{ $email }}
                                                    </div>

                                                    @if($email_owner = App\Person::where('primary_email', $email)->where('team_id', Auth::user()->team->id)->first())

                                                        <i class="fas fa-check-circle mt-2 text-sm text-blue"></i>

                                                        <div class="font-bold px-2 py-2 text-blue">
                                                            {{ $email_owner->full_name }}
                                                        </div>

                                                    @else

                                                        <i class="fas fa-chevron-right mt-2"></i>
                                                        <div class="flex flex-wrap">

                                                            @foreach($contact->people()
                                                                             ->where('primary_email', '!=', $email)
                                                                             ->get()
                                                                             as $match)

                                                                <div class="bg-white hover:shadow-lg rounded-lg px-2 text-xs border ml-2 inline-block my-1 pt-1"
                                                                wire:click="linkEmail('{{ $email }}', '{{ $match->id }}')">
                                                                    {{ $match->full_name }}
                                                                </div>

                                                            @endforeach

                                                        </div>

                                                    @endif

                                                </div>

                                            @endforeach

                                        </div>

                                    </div>
                                @endif

                                <!---------- END LINK EMAILS ---------------------->

                            </div>

                        </div>


                    </div>


                @endforeach

            </div>

        @endif

    </div>

    <div wire:init="loadCallLog()" wire:loading.remove class="font-bold">
        <!-- This part of page is not held up while the rest is loading -->
    </div>

</div>
