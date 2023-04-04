<?php if (!defined('dir')) define('dir','/u'); ?>

<!-- <div id="person_otherinfo" class="person-tab-content {{ ($tab != 'otherinfo') ? 'hidden' : '' }}">          -->

    <div>
@if(!$voterRecord)

This person is not linked to a voterfile record.

@else


<div class="text-xl mt-2">
    Voter Data
</div>

<table class="text-sm mt-2 w-full border-t-4 border-blue">
    <tr class="border-t">
        <td class="p-2 bg-grey-lighter w-1/4">
            State Voter ID
        </td>
        <td class="p-2">
            {{ $voterRecord->id }}
        </td>
    </tr>
    <tr class="border-t">
        <td class="p-2 bg-grey-lighter w-1/4">
            Registered
        </td>
        <td class="p-2">
            {{ $voterRecord->registration_date }}
        </td>
    </tr>
    <tr class="border-t">
        <td class="p-2 bg-grey-lighter w-1/4">
            Election History
        </td>
        <td>

    @if ($voterRecord->elections)
        <table class="w-full">
            <tr>
            
                @foreach($voterRecord->elections as $election)
                    
                    <td class="py-2 border-r text-center">
                        <i class="fas fa-check-square"></i>
                    </td>
                @endforeach

            </tr>

        </table>
    @endif
                                
                            
                </td>
            </tr>
            <tr class="border-t">
                <td class="p-2 bg-grey-lighter w-1/5">
                    Party
                </td>
                <td class="p-2">
                    {{ $voterRecord->party}}
                </td>
            </tr>
            <tr class="border-t">
                <td class="p-2 bg-grey-lighter w-1/5">
                    Ward - Precinct
                </td>
                <td class="p-2">
                    {{ $voterRecord->ward }} - {{ $voterRecord->precinct}} 
                </td>
            </tr>
            <tr class="border-t">
                <td class="p-2 bg-grey-lighter w-1/5">
                    State House Dist
                </td>
                <td class="p-2">
                    {{ $voterRecord->house_district}}
                </td>
            </tr>
            <tr class="border-t">
                <td class="p-2 bg-grey-lighter w-1/5">
                    State Sen Dist
                </td>
                <td class="p-2">
                    {{ $voterRecord->senate_district }}
                </td>
            </tr>
            <tr class="border-t">
                <td class="p-2 bg-grey-lighter w-1/5">
                    Congress Dist
                </td>
                <td class="p-2">
                    {{ $voterRecord->congress_district}}
                </td>
            </tr>
        </table>
    </div>
    @endif

