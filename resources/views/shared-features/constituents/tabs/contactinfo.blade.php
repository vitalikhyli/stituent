<!-- <div class="text-xl font-sans mt-4 pb-1 border-b-4 border-blue text-blue-dark">
    Contact
</div>
 -->
<table class="text-sm w-full border-b">

    <tr class="border-t">
        <td class="py-2 px-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
            <div class="whitespace-no-wrap">Master Email List</div>
        </td>
        <td class="p-2">

            @if($person->master_email_list)
               <i class="fas fa-check-circle mr-1"></i> On List |
               <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/masteremail/remove" class="text-sm">Remove</a>
            @else
                <a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/masteremail/add" class="text-sm">Add to List</a>
            @endif
        </td>
    </tr>

    <tr class="border-t">
        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
            Emails
        </td>
        <td class="p-2">

            
        @include('shared-features.constituents.tabs.notes-add-email')    


        @if($person->primary_email)
            <div class="text-blue font-semibold">
                <i class="fas fa-envelope mr-2 w-4 mt-1"></i>
                {{ $person->primary_email }}
                <span class="text-grey-dark text-xs float-right">(Primary)</span>
            </div>
        @endif

        @if($person->work_email)
            <div class="mt-1">
               <i class="fas fa-envelope mr-2 w-4"></i>
               {{ $person->work_email}}
               <span class="text-grey-dark text-xs float-right">(Work)</span>
            </div>
        @endif

        @foreach ($other_emails as $obj)
            <div class="text-green-dark mt-1">
                <i class="fas fa-envelope mr-2 w-4"></i> {{ $obj->contact }}
                <span class="text-grey-dark text-xs font-semibold float-right">{{ $obj->notes }}</span>
            </div>
        @endforeach


        </td>
    </tr>

    <tr class="border-t">
        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
            Phones
        </td>
        <td class="p-2">

        
        @include('shared-features.constituents.tabs.notes-add-phone')


        @if(!$person->primary_phone && !$person->other_phones)
            <span class="text-grey text-sm">No primary set</span>
        @endif

        @if($person->primary_phone)
            <i class="fas fa-phone mr-2 w-4 text-blue"></i>
            <span class="text-blue font-semibold">{{ $person->primary_phone }}</span>
            <span class="text-grey-dark text-xs font-semibold float-right">(Primary)</span>
        @endif

        @foreach ($other_phones as $obj)
            <div class="text-green-dark rounded-lg">
                <i class="fas fa-phone mr-2 w-4"></i> 
                @if (is_array($obj->contact))
                    {{ implode(': ',$obj->contact) }}
                @else
                    {{ $obj->contact }}
                @endif
                <span class="text-grey-dark text-xs font-semibold float-right">
                    @if (is_array($obj->notes))
                        {{ implode(': ',$obj->notes) }}
                    @else
                        {{ $obj->notes }}
                    @endif
                </span>
            </div>
        @endforeach


<!-- where other phones was, code broken
 -->
        </td>
    </tr>

    @if(Auth::user()->permissions->developer)
    <tr class="border-t">
        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
            Social Media
        </td>
        <td class="p-2 text-sm text-blue">

            {!! $person->social_media_linked !!}

        </td>
    </tr>
    @endif

    <tr class="border-t">
        <td class="py-2 pl-3 bg-grey-lightest text-grey-dark uppercase text-xs w-32">
            Business
        </td>
        <td class="p-2">
            @include('shared-features.constituents.tabs.business')
        </td>
    </tr>


    @if($person->social_twitter)
    <tr class="border-t">
        <td class="p-2 bg-grey-lightest w-1/5">
            Twitter
        </td>
        <td class="p-2">
            {{ $person->social_twitter }}
        </td>
    </tr>
    @endif


    @if($person->social_facebook)
    <tr class="border-t">
        <td class="p-2 bg-grey-lightest w-1/5">
            Facebook
        </td>
        <td class="p-2">
            {{ $person->social_facebook }}
        </td>
    </tr>
    @endif

</table>
        