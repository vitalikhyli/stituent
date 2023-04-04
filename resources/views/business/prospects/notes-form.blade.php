<form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/prospects/{{ $opportunity->id }}/add_contact">

    @csrf

    <table class="text-base w-full">

        <tr class="border-b">
            <td class="p-2 bg-grey-lightest text-right align-middle w-1/5">
                Date/Step
            </td>
            <td class="p-2">

                <input name="date" value="{{ \Carbon\Carbon::now()->toDateString() }}" class="border-2 rounded-lg p-2 text-sm mr-2" size="10" />

                @if($opportunity->pattern)

                    <select name="step">

                            <option value="">

                                -- None --

                            </option>

                        @foreach($opportunity->pattern->steps->sortBy('the_order') as $step)

                            <option value="{{ $step->name }}">

                                {{ $step->the_order }}. {{ $step->name }}

                            </option>

                        @endforeach
                    </select>
                    
                @endif

            </td>
        </tr>


        <tr class="border-b">
            <td class="p-2 bg-grey-lightest text-right align-middle w-1/5">
                Checkin?
            </td>
            <td class="p-2 flex">

                <label for="check_in" class="font-normal p-2">
                    <input type="checkbox" name="check_in" id="check_in" value="1" />
                    This counts as a check-in
                </label>

                <input name="amount_secured" class="ml-4 border-2 rounded-lg p-2 text-sm" size="12" placeholder="Amt Secured?" />

            </td>
        </tr>

        <tr class="border-b">
            <td class="p-2 bg-grey-lightest text-right align-middle w-1/5">
                Note
            </td>
            <td class="p-2">
                <textarea id="contact_notes" name="notes" rows="3" type="text" class="border-2 rounded-lg px-4 py-2 w-full"></textarea>
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
                            <input type="checkbox" name="include_people[]" value="{{ $opportunity->entity->id }}" checked disabled readonly />
                            {{ $opportunity->entity->name }}
                        </label>

                        <label class="ml-2 font-normal rounded-lg border px-2 py-1 text-sm bg-white hover:bg-orange-lighter cursor-pointer">
                            <div onclick="">
                                Include Others
                            </div>
                        </label>
                        
                    </div>
                </div>

                <div class="p-2 text-sm text-right">

                    <input type="submit" class="ml-4 shadow cursor-pointer hover:bg-blue-dark rounded-lg py-1 px-3 text-xs bg-blue text-white" value="Add Note" />

                    <!-- <a id="set_follow_up" class="cursor-pointer">Set Follow-Up</a> -->

                </div>
                
            </td>
        </tr>
    </table>

<input type="hidden" value="{{ $opportunity->entity->id }}" name="person_id" />

</form>