<div>

    <div class="border-b border-dashed border-blue flex pb-1">

        <div class="flex-shrink text-3xl font-thin text-blue">
            Contact Log
        </div>


        <div class="text-right flex-grow flex">

            <div class="flex-grow text-blue text-sm py-2 pr-2 cursor-pointer"
                 wire:click="toggleLogReport()">
                <i class="far fa-file-alt mr-2"></i> Create a Log Report
            </div>

        </div>

    </div>

    <div class="flex">

        <div class="w-1/3 p-4 bg-grey-lightest">

            <input type="text"
                   wire:model.debounce="search"
                   class="px-2 py-1 border w-full text-xl font-bold"
                   placeholder="Search..." />

        </div>

        <div class="w-2/3 p-4 text-right flex-grow bg-grey-lightest
                    @if($showAddNew || $showLogReport)
                        hidden
                    @endif
                    ">

            <button class="rounded-lg bg-blue text-white px-4 py-2 text-xl"
                    wire:click="$set('showAddNew', true)">Add New</button>

        </div>

        <div class="w-2/3 p-4 border-2 shadow bg-white -mt-2 -mb-4 pl-6
                    @if(!$showLogReport)
                        hidden
                    @endif
                    ">

            <button class="relative float-right text-grey-dark pl-4 text-4xl font-bold"
                        wire:click="$set('showLogReport', false)">&times;</button>

            @livewire('call-log.report')

        </div>

        <div class="w-2/3 p-4 border-2 shadow bg-white -mt-2 -mb-4 pl-6
                    @if(!$showAddNew)
                        hidden
                    @endif
                    ">

            <button class="relative float-right text-grey-dark pl-4 text-4xl font-bold"
                        wire:click="$set('showAddNew', false)">&times;</button>

            <div class="flex">

                <div class="bg-grey-light rounded-lg w-1/2 flex p-2 px-3">

                    <div class="pr-2">

                        <select wire:model="newType"
                                class="border border-grey-dark">
                            @foreach($typeOptions as $option)
                                <option value="{{ $option }}">
                                    {{ $option }}
                                </option>
                            @endforeach
                        </select>

                    </div>

                    <div>

                        <input type="text"
                               name="addType"
                               class="px-2 py-1 border w-full text-sm"
                               placeholder="Add Type" />

                    </div>

                </div>

                <div class="w-1/2 flex p-2">

                    <div>

                        <input type="text"
                               name=""
                               class="px-2 py-1 border w-full text-sm datepicker"
                               id="datepicker_date"
                               placeholder="Date"
                               wire:model.debounce="newDate" />

                    </div>

                    <div>

                        @if($newDate && $newDateValidated)
                            <input type="text"
                                   name=""
                                   class="ml-2 px-2 py-1 border w-full text-sm"
                                   placeholder="Time" />
                        @endif

                    </div>

                </div>


            </div>

            <div class="flex py-2">

                <input type="text"
                       class="border-b-4 py-2 px-4 text-base font-bold w-full"
                       placeholder="Who"
                       wire:model="newWho" />

            </div>

            <div class="">

                <div>
                    <textarea type="text"
                           class="border-b-4 py-2 px-4 text-base font-bold w-full h-32"
                           placeholder="Notes"
                           wire:model="newNotes"></textarea>
                </div>

                <div class="text-sm text-blue text-right">
                    * Phone numbers and emails will be automatically detected in notes
                </div>
                
            </div>

            <div class="pt-6 flex">

                <div class="text-grey-darker text-sm flex-grow">
                    
                    <div>
                        <label for="private" class="font-normal">
                            <input type="checkbox"
                                   id="private"
                                   class="mr-1"
                                   />
                            <i class="fa fa-lock ml-2"></i> Only {{ Auth::user()->name }} (and admins)
                        </label>
                    </div>

                    <div class="py-1">

                        <label for="follow_up" class="font-normal">
                            <input type="checkbox"
                                   id="follow_up"
                                   class="mr-1"
                                   wire:model="newFollowUp"
                                   />
                            <i class="fas fa-hand-point-right text-red ml-2"></i> Follow-up
                        </label>

                        <div class="pl-2 inline-block">
                            
                            <input type="text"
                                   name="follow_up_on"
                                   class="px-2 py-1 border-b-2 border-red w-full text-sm w-1/4 datepicker
                                   @if(!$newFollowUp)
                                        hidden
                                    @endif
                                    "
                                    id="datepicker_follow_up_on"
                                    placeholder="Follow Up on Date"
                                    wire:model="newFollowUpOn" />

                        </div>

                    </div>

                </div>

                <div class="text-right flex-shrink align-bottom pt-4">

                    <button class="rounded-lg bg-green hover:bg-green-dark text-white px-4 py-2 text-base
                            @if(!$newDate || !$newWho || !$newNotes)
                                opacity-50
                            @endif
                            "
                            wire:click="storeCase()"
                            @if(!$newDate || !$newWho || !$newNotes)
                                disabled
                            @endif
                            >
                        Add as Case
                    </button>

                    <button class="rounded-lg bg-blue hover:bg-blue-dark text-white px-4 py-2 text-base
                            @if(!$newDate || !$newWho || !$newNotes)
                                opacity-50
                            @endif
                            "
                            wire:click="storeContact()"
                            @if(!$newDate || !$newWho || !$newNotes)
                                disabled
                            @endif
                            >
                        Add as Contact
                    </button>

                </div>

            </div>

        </div>

    </div>

    <script type="text/javascript">
        //Need this here because modal
        $('.datepicker').datepicker();
        
        //Trigger Livewire model update if Datepicker JS channges content
        $( ".datepicker" ).change(function(event) {
          element = document.getElementById(event.target.id);
          element.dispatchEvent(new Event('input'));
        });
    </script>
        


</div>
