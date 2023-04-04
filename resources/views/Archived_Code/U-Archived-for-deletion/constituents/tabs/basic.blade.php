<?php if (!defined('dir')) define('dir','/u'); ?>

<table class="text-base w-full border-b">
    <tr class="border-t">
        <td class="p-2 bg-grey-lighter w-32">
            Address
        </td>
        <td class="p-2">
            <a href="{{dir}}/households/{{ $person->household_id }}">
                <div class="inline-block mb-1 border bg-orange-lightest hover:bg-blue hover:text-white rounded-lg mr-2 px-2 py-1 flex-initial cursor-pointer">
                    <i class="fas fa-home text-lg mr-2"></i>
                    {{ $person->full_address }}
                </div>
            </a>
        </td>
    </tr>
    <tr class="border-t">
        <td class="p-2 bg-grey-lighter">
            Household:
        </td>
        <td class="p-2">
            <div class="inline-flex flex-wrap">
            @if($cohabitors)
            @if($cohabitors->count() <= 0)
                Alone
            @else
            @foreach ($cohabitors as $theperson)
                @if($theperson->external)
                    <a href="{{dir}}/constituents/{{ $theperson->id}}">
                        <div class="flex-1 flex-initial m-1 bg-grey-lighter px-2 py-1 text-blue-dark rounded-full mr-2 text-sm">
                            <i class="fas fa-unlink text-sm mr-2"></i>
                            {{ $theperson->full_name }}
                        </div>
                    </a>
                @else
                    <a href="{{dir}}/constituents/{{ $theperson->id}}">
                        <div class="flex-1 flex-initial m-1 bg-orange-lightest border px-2 py-1 text-blue-dark rounded-full mr-2 text-sm hover:bg-grey-lighter">
                            <i class="fas fa-user-circle mr-2"></i>
                            {{ $theperson->full_name }}
                        </div>
                    </a>
                @endif
            @endforeach
            @endif
            @endif
            </div>
        </td>
    </tr>

    <tr class="border-t">
        <td class="p-2 bg-grey-lighter w-32">
            Born
        </td>
        <td class="p-2">
            @if($person->dob)
                {{ \Carbon\Carbon::parse($person->dob)->format("M j, Y") }}
                (age {{ \Carbon\Carbon::parse($person->dob)->diffInYears() }})
            @else
                <span class="text-grey">Unknown</span>
            @endif
        </td>
    </tr>

    <tr class="border-t">
        <td class="p-2 bg-grey-lighter  w-32">
            Gender
        </td>
        <td class="p-2">
            @if($person->gender)
                {{ $person->gender }}
            @endif
        </td>
    </tr>

    <tr class="border-t">
        <td class="p-2 bg-grey-lighter  w-32">
            General Notes
        </td>
        <td class="p-2 text-sm">
            @if(!$person->private)
                <span class="text-grey-dark p-2">
                    None
                </span>
            @else
                {!! nl2br($person->private) !!}
            @endif
        </td>
    </tr>


</table>