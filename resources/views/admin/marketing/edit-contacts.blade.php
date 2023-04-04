<div class="text-xl border-b-4 border-red py-1">
    Contact History
</div>

@if(!$candidate->contacts->first())

    <div class="p-2">
        None
    </div>

@endif

@foreach($candidate->contacts()->orderBy('created_at')->get() as $contact)

<div x-data="{ 'open': false }">

    <div class="flex mt-4 {{ (!$loop->last) ? 'border-b' : '' }} border-dashed pb-4">
        <div class="w-1/4">
            <div class="text-left align-middle font-bold text-sm">
                {{ Carbon\Carbon::parse($contact->created_at)->format("F j, Y") }}
            </div>
            <div class="text-grey-dark text-sm whitespace-no-wrap">
                @if(!$contact->user_id)
                    Automatic
                    <i class="fas fa-laptop"></i> 
                @else
                    {{ $contact->user->shortName }}
                @endif
            </div>
        </div>

        <div class="w-3/4">

            <span class="float-right text-grey-dark cursor-pointer uppercase text-xs"
                    @click="open = !open">
                Edit
            </span>

            @if($contact->sequence)
                <div class="mb-2">
                    Step <span class="shadow text-sm py-1 rounded-full text-white bg-blue px-2 mr-1">{{ ($contact->step) ? $contact->step : '?' }}</span> of Sequence <span class="shadow text-sm py-1 rounded-full text-white bg-blue px-2">{{ $contact->sequence }}</span>
                </div>
            @endif

            @if($contact->mailable)
                <div class="mb-2 text-blue">
                    <i class="fas fa-envelope"></i>  "{{ $contact->mailable }}"
                </div>
            @endif



            <div class="text-left align-middle">
                {{ $contact->notes }}
            </div>
            
        </div>


    </div>

    <div x-show="open" class="w-full bg-red-lightest p-2">
        <div class="text-lg font-bold mb-2">Edit This <i class="fas fa-hand-point-up"></i></div>

        @include('admin.marketing.new-edit-contact', ['edit' => $contact])

    </div>

</div>

@endforeach

<div class="border-b-4 border-red py-2 mt-2 text-base">
    Manually Add a Contact:
</div>


<div class="p-2 px-3 bg-grey-lightest">

    @include('admin.marketing.new-edit-contact', ['edit' => null])

</div>

    
