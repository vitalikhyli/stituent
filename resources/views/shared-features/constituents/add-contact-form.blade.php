<div class="border shadow relative p-4 m-4 bg-white">



        <div class="absolute pin-r pin-t -mt-4 bg-white p-1 uppercase text-sm text-grey-dark mr-4">
            New Note
        </div>

        <form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/add_contact">

            @csrf


            <table class="text-base w-full">
                <tr>
                    <td></td>
                    <td class="p-1">
                        <div class=" text-grey-dark">
                            {{ $person->name }}
                        </div>
                    </td>
                </tr>
                <tr class="">
                    <td class="p-1 uppercase text-xs text-grey-dark align-top text-right align-middle w-1/6">
                        Type
                    </td>
                    <td class="p-1 flex">
                        <select name="type" class="form-control whitspace-no-wrap w-3/6">

                             @foreach(Auth::user()->team->contactTypes() as $key => $type)

                                <option value="{{ $type }}">{{ ucwords($type) }}</option>
                                
                             @endforeach

                         </select>
                    </td>
                </tr>
                <tr class="">
                    <td class="p-1 uppercase text-xs text-grey-dark align-top text-right align-middle w-1/6">
                        Date
                    </td>
                    <td class="p-1 flex">
                        <input name="date" value="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" class="bg-grey-lightest datepicker border-2 rounded-lg px-4 py-2 w-1/3"/>

                        <a class="px-2 py-1 rounded-full mt-1 text-sm cursor-pointer" id="show_time">Set Time</a>
                        <div id="show_time_div" class="hidden">
                            <input name="time" value="{{ \Carbon\Carbon::now()->format('h:i A') }}" class="border-2 rounded-lg px-4 py-2 w-32"/>
                            <input type="hidden" value="0" name="use_time" id="use_time" />
                        </div>
                    </td>
                </tr>
                <tr class="">
                    <td class="p-1 uppercase text-xs text-grey-dark align-top text-right align-middle w-1/6">
                        Subject
                    </td>
                    <td class="p-1">
                        <input id="contact_subject" autocomplete="off" name="subject" rows="5" type="text" class="bg-grey-lightest border-2 rounded-lg px-4 py-2 w-full"
                        value="{{ $errors->any() ? old('subject') : '' }}" />
                    </td>
                </tr>
                <tr class="">
                    <td class="p-1 uppercase text-xs text-grey-dark align-top text-right align-middle w-1/6">
                        Notes
                    </td>
                    <td class="p-1">
                        <textarea id="contact_notes" name="notes" rows="5" type="text" class="border-2 rounded-lg px-4 py-2 w-full"></textarea>
                    </td>
                </tr>
                <tr class="">
                    <td></td>
                    <td class="align-middle">


                        <div id="set_follow_up_div" class="hidden my-2 p-1">

                            <input type="checkbox" 
                            autocomplete="off"
                            name="person_followup"
                            id="person_followup" />

                            <i class="fas fa-hand-point-right ml-2"></i>

                            <span class="text-sm">Follow Up on this Date</span>

                            <input type="text" 
                            name="person_followup_on" 
                            placeholder="{{ \Carbon\Carbon::now()->format('m/d/Y') }}" 
                            class="datepicker border px-2 py-1 rounded-lg" />

                        </div>



                        <div id="select_people" class="collapse">
                            <div class="flex flex-wrap border-t-2 text-center px-2 py-2 w-full bg-orange-lightest">

                                <label class="hover:bg-orange-lighter cursor-pointer font-normal rounded-full border px-2 py-1 text-sm bg-white">
                                    <input type="checkbox" name="include_people[]" value="{{ $person->id }}" checked disabled readonly />
                                    {{ $person->full_name }}
                                </label>

                                <label class="ml-2 font-normal rounded-full border px-2 py-1 text-sm bg-white hover:bg-orange-lighter cursor-pointer">
                                    <div onclick="">
                                        Include Others
                                    </div>
                                </label>

                            </div>
                        </div>

                        <div class="pt-3 pb-2 mb-1 text-sm">

                            <input type="submit" class=" float-right shadow cursor-pointer hover:bg-blue-dark rounded-full py-2 px-3 text-sm bg-blue text-white" value="Save" />

                            <a id="set_follow_up" class="cursor-pointer">Set Follow-Up</a>

                        </div>

                    </td>
                </tr>
            </table>

            <input type="hidden" value="{{ $person->id }}" name="person_id" />


        </form>

    </div>