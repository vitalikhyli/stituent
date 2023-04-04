<div class="font-bold py-1 uppercase mt-2 border-b capitalize">
    {{ $type }}
</div>

@if($type == 'candidate')
    <div class="flex ml-4 mt-2">

        <div class="w-1/2 pr-1">
            <div class="font-medium text-grey-darker">
                First
            </div>
            <div class="">
                <input name="first_name" value="{{ $candidate->first_name }}" class="border-2 px-2 py-2 w-full"/>
            </div>
        </div>

        <div class="w-24 pl-1">
            <div class="font-medium text-grey-darker">
                Mid
            </div>
            <div class="">
                <input name="middle_name" value="{{ $candidate->middle_name }}" class="border-2 px-2 py-2 w-full"/>
            </div>
        </div>

        <div class="w-1/2 pl-1">
            <div class="font-medium text-grey-darker">
                Last
            </div>
            <div class="">
                <input name="last_name" value="{{ $candidate->last_name }}" class="border-2 px-2 py-2 w-full"/>
            </div>
        </div>

    </div>

    <div class="flex ml-4 mt-2">

        <div class="w-full pr-1">
            <div class="font-medium text-grey-darker">
                Voter ID
            </div>
            <div class="">
                <input name="voter_id" value="{{ $candidate->voter_id }}" class="text-blue font-mono border-2 px-2 py-2 w-full" />
            </div>
        </div>

    </div>

@else
    <div class="ml-4 mt-2">
        <div class="font-medium text-grey-darker">
            Name
        </div>
        <div class="">
            <input name="{{ $type }}_name" value="{{ $candidate->{$type.'_name'} }}" class="border-2 px-2 py-2 w-full"/>
        </div>
    </div>
@endif

<div class="ml-4 mt-2">
    <div class="font-medium text-grey-darker">
        Email
    </div>
    <div class="">
        <input name="{{ $type }}_email" value="{{ $candidate->{$type.'_email'} }}" class="border-2 px-2 py-2 w-1/2 mr-1"/>
        <label for="ok_email_{{ $type }}" class="font-normal">
        <input type="checkbox" name="ok_email_{{ $type }}" id="ok_email_{{ $type }}" 
                class="border-2 px-2 py-2" 
                {{ ($candidate->marketing->{ 'ok_email_'.$type }) ? 'checked' : '' }} /> OK to use this email
        </label>
    </div>
</div>

<div class="ml-4 mt-2">
    <div class="font-medium text-grey-darker">
        Phone
    </div>
    <div class="">
        <input name="{{ $type }}_phone" value="{{ $candidate->{$type.'_phone'} }}" class="border-2 px-2 py-2 w-1/2 mr-1"/>
    </div>
</div>