<div>


    <x-modal-base-component wire:model="show">

        <div class="px-4 py-2 mb-4">
            
            @if($contact)

                @if($edit)

                    <div class="font-bold text-sm text-grey-dark">
                        Date
                    </div>
                    <div class="py-1">
                        <input type="text"
                               wire:model="contact_date"
                               class="p-2 border-2 w-full"
                               />
                    </div>

                    <div class="font-bold text-sm text-grey-dark">
                        Subject
                    </div>
                    <div class="py-1">
                        <input type="text"
                               wire:model="contact_subject"
                               class="p-2 border-2 w-full"
                               />
                    </div>

                    <div class="font-bold text-sm text-grey-dark">
                        Notes
                    </div>
                    <div class="py-1">
                        <textarea
                               wire:model="contact_notes"
                               class="p-2 border-2 w-full h-32"></textarea>
                    </div>

                    <div class="font-bold text-sm text-grey-dark">
                        Connected People
                    </div>

                    @if(!$contact->people->first())
                        None
                    @endif

                    @foreach($contact->people as $person)
                        <div class="py-1 border-b cursor-pointer">
                            <i class="fas fa-user-circle mr-2"></i>{{ $person->full_name }}

                            <div class="py-1 flex text-sm hover:bg-blue-lightest float-right"
                                 wire:click="disconnectPerson('{{ $person->id }}')">

                                <div class="text-gray text-sm">
                                    {{ $person->full_address }}
                                </div>

                                <div>
                                    <i class="fas fa-minus-circle text-red ml-2"></i>
                                </div>

                            </div>



                        </div>
                    @endforeach

                    <div class="py-2">

                        <input type="text"
                               wire:model="person_search"
                               class="border py-2 px-4 w-full text-lg"
                               placeholder="Search for People"
                               />

                    </div>

                    @if($person_search)

                        <div class="pb-4">

                            @foreach($people_options as $option)

                                <div class="cursor-pointer border-b py-1 flex hover:bg-blue-lightest"
                                     wire:click="connectPerson('{{ $option->id }}')">
                                    <div>
                                        <i class="fas fa-plus mr-2 text-blue"></i>{{ $option->full_name }}

                                        <div class="text-grey-darker text-sm ml-6">
                                            {{ $option->full_address }}
                                        </div>
                                    </div>
                                </div>

                            @endforeach

                        </div>

                    @endif

                    <button wire:click="updateContact()"
                            class="rounded-lg bg-blue text-white px-4 py-1 text-base">
                        Save
                    </button>


                @else

                    <div class="flex">
                        <div class="flex-grow text-blue py-1">
                            @if($contact_just_updated_at && $contact_just_updated_at->DiffInSeconds() < 5)
                                <div wire:poll.5000ms>
                                    <i class="fas fa-check-circle"></i> Updated!
                                </div>
                            @endif
                        </div>
                        <div wire:click="$set('edit', true)"
                             class="cursor-pointer text-right flex-shrink">
                                <button class="rounded-lg bg-blue hover:font-bold text-white px-4 py-1">
                                    Edit
                                </button>
                        </div>
                    </div>

                    <div class="py-1">
                        {{ $contact->date->format('j F Y') }}
                    </div>

                    <div class="text-2xl font-bold py-2 text-blue">
                        {{ $contact->subject }}
                    </div>

                    <div class="py-1">
                        {{ $contact->notes }}
                    </div>

                    <div class="font-bold text-sm text-grey-dark">
                        Connected People
                    </div>

                    @if(!$contact->people->first())
                        None
                    @endif

                    @foreach($contact->people as $person)
                        <div class="py-1 border-b">
                            <i class="fas fa-user-circle mr-2"></i>{{ $person->full_name }}
                        </div>
                    @endforeach

                @endif

            @endif

        </div>

    </x-modal-base-component>

</div>