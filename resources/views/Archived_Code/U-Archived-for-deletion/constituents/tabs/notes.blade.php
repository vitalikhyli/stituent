<?php if (!defined('dir')) define('dir','/u'); ?>

<!-- <div id="person_notes" class="border-t person-tab-content text-base w-full  {{ ($tab != 'notes') ? 'hidden' : '' }}"> -->


        <div class="mt-2 w-full">

            <div class="text-xl font-sans mt-4 pb-1">
                History
            </div>

                <form method="POST" id="contact_form" action="{{dir}}/constituents/{{ $person->id }}/add_contact">

                    @csrf


    <table class="text-base w-full border-t-4 border-blue">
        <tr class="border-b">
            <td class="p-2 bg-grey-lightest text-right align-middle w-1/6">
                Date
            </td>
            <td class="p-2 flex">
                <input name="date" value="{{ \Carbon\Carbon::now()->toDateString() }}" class="border-2 rounded-lg px-4 py-2 w-1/3"/>

                <a class="px-2 py-1 rounded-full mt-1 text-sm cursor-pointer" id="show_time">Set Time</a>
                <div id="show_time_div" class="hidden">
                    <input name="time" value="{{ \Carbon\Carbon::now()->format("h:i A") }}" class="border-2 rounded-lg px-4 py-2 w-32"/>
                    <input type="hidden" value="0" name="use_time" id="use_time" />
                </div>
            </td>
        </tr>
        <tr class="border-b">
            <td class="p-2 bg-grey-lightest text-right align-middle w-1/6">
                Desc
            </td>
            <td class="p-2">
                <textarea id="contact_notes" name="notes" rows="5" type="text" class="border-2 rounded-lg px-4 py-2 w-full"></textarea>
            </td>
        </tr>
        <tr class="">
            <td class="align-middle" colspan="2">


                <div id="set_follow_up_div" class="hidden my-2 p-2">

                    <input type="checkbox" 
                           autocomplete="off"
                           name="person_followup"
                           id="person_followup" />

                    <i class="fas fa-hand-point-right ml-2"></i>

                    <span class="text-sm">Follow Up on this Date</span>

                    <input type="text" 
                           name="person_followup_on" 
                           placeholder="{{ \Carbon\Carbon::now()->format("Y-m-d") }}" 
                           class="border px-2 py-1 rounded-lg" />

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

                <div class="pt-3 pb-2 mb-1 text-sm border-b">

                    <input type="submit" class=" -mt-1 ml-4 float-right shadow cursor-pointer hover:bg-blue-dark rounded-lg py-1 px-3 text-sm bg-blue text-white" value="Add Note" />

                    <a id="set_follow_up" class="cursor-pointer">Set Follow-Up</a>

                </div>
                
            </td>
        </tr>
    </table>

                    <input type="hidden" value="{{ $person->id }}" name="person_id" />


                </form>



                @if((isset($contacts)) && ($contacts->count() > 0))

                    @foreach($contacts as $thecontact)

                        @if($thecontact->type == 'bulk_email')
                            <div class="cursor-pointer">
                                @include('office.constituents.one-bulkemail')
                            </div>
                        @else
                            <div class="cursor-pointer">
                                @include('office.constituents.one-contact')
                            </div>
                        @endif

                    @endforeach

                @else
                    
                    
                @endif



            </div>


