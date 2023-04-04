<form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/organizations/{{ $entity->id }}/add_contact">

    @csrf

    <table class="text-base w-full">
        <tr class="border-b">
            <td class="p-2 bg-grey-lightest text-right align-middle w-1/6">
                Date
            </td>
            <td class="p-2 flex">
                <input name="date" value="{{ \Carbon\Carbon::now()->toDateString() }}" class="border-2 rounded-lg px-4 py-2 w-1/3 text-sm"/>

                <a class="px-2 py-1 rounded-lg mt-1 text-sm cursor-pointer" id="show_time">Set Time</a>
                <div id="show_time_div" class="hidden">
                    <input name="time" value="{{ \Carbon\Carbon::now()->format("h:i A") }}" class="border-2 rounded-lg px-4 py-2 w-32 text-sm"/>
                    <input type="hidden" value="0" name="use_time" id="use_time" />
                </div>
            </td>
        </tr>
        <tr class="border-b">
            <td class="p-2 bg-grey-lightest text-right align-middle w-1/6">
                Note
            </td>
            <td class="p-2">
                <textarea id="contact_notes" name="notes" rows="5" type="text" class="border-2 rounded-lg px-4 py-2 w-full"></textarea>
            </td>
        </tr>
        <tr class="">
            <td class="align-middle" colspan="2">


                <div id="set_follow_up_div" class="hidden my-2 bg-orange-lightest p-2 border rounded-lg">

                    <input type="checkbox" 
                           autocomplete="off"
                           name="person_followup"
                           id="person_followup" />

                    <i class="fas fa-hand-point-right text-red ml-2"></i>

                    <span class="text-sm">Follow Up on this Date</span>

                    <input type="text" 
                           name="person_followup_on" 
                           placeholder="{{ \Carbon\Carbon::now()->format("Y-m-d") }}" 
                           class="border px-2 py-1 rounded-lg" />

                </div>



                <div id="select_people" class="collapse">
                    <div class="flex flex-wrap border-t-2 text-center px-2 py-2 w-full bg-orange-lightest">

                        <label class="hover:bg-orange-lighter cursor-pointer font-normal rounded-lg border px-2 py-1 text-sm bg-white">
                            <input type="checkbox" name="include_people[]" value="{{ $entity->id }}" checked disabled readonly />
                            {{ $entity->name }}
                        </label>

                        <label class="ml-2 font-normal rounded-lg border px-2 py-1 text-sm bg-white hover:bg-orange-lighter cursor-pointer">
                            <div onclick="">
                                Include Others
                            </div>
                        </label>
                        
                    </div>
                </div>

                <div class="pt-3 pb-2 mb-1 text-sm">

                    <input type="submit" class=" -mt-1 ml-4 float-right shadow cursor-pointer hover:bg-blue-dark rounded-lg py-1 px-3 text-xs bg-blue text-white" value="Add Note" />

                    <a id="set_follow_up" class="cursor-pointer">Set Follow-Up</a>

                </div>
                
            </td>
        </tr>
    </table>

<input type="hidden" value="{{ $entity->id }}" name="person_id" />


</form>