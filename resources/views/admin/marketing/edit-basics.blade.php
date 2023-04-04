<div class="text-xl border-b-4 border-red py-1">
    Candidate Committee
</div>  

<div class="bg-red-lightest p-2 pb-4">

<div class="font-bold py-1 border-b">
    Contact Rules
</div>

<div class="ml-4 mt-2">
    <div class="pr-1 flex">
        <label for="do_not_contact" class="font-normal">
            <input type="checkbox" id="do_not_contact" name="do_not_contact" 
            class="border-2 px-4 py-2" 
            {{ ($candidate->marketing->do_not_contact) ? 'checked' : '' }} />
            Do Not Send Marketing Emails <i class="fas fa-laptop"></i> 
        </label>

    </div>

    <div class="pr-1 flex mt-2">
        <div class="">
            Loyalty Conflict:
        </div>
        <div class="ml-2">
            <select name="loyalty_conflict_id" class="{{ (!$candidate->marketing->loyalty_conflict_id) ? 'opacity-50' : '' }}">
                <option value="">---- No Loyalty Conflict ----</option>
                @foreach(\App\Account::orderBy('name')->get() as $account)
                    <option value="{{ $account->id }}"
                        {{ ($account->id == $candidate->marketing->loyalty_conflict_id) ? 'selected' : '' }}>{{ $account->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="pr-1 flex mt-2">
        <div class="">
            Is Now a Client:
        </div>
        <div class="ml-2">
            <select name="account_id" class="{{ (!$candidate->account_id) ? 'opacity-50' : '' }}">
                <option value="">---- Not Yet ----</option>
                @foreach(\App\Account::orderBy('name')->get() as $account)
                    <option value="{{ $account->id }}"
                        {{ ($account->id == $candidate->account_id) ? 'selected' : '' }}>{{ $account->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

</div>

</div>

@include('admin.marketing.committee-member', ['type' => 'candidate'])

<div class="mt-2">
    <div class="mb-1 pl-4 text-base">
      <select class="border w-full {{ (!$candidate->office) ? 'opacity-50' : '' }}" name="office">
                    <option value="">-- Office --</option>
                    @foreach(\App\Candidate::select('office')->whereNotNull('office')->groupBy('office')->get() as $office)
                        <option value="{{ $office->office }}" {{ ($candidate->office == $office->office) ? 'selected' : '' }}>{{ $office->office }}</option>
                    @endforeach
                  </select>
    </div>
</div>

<div class="mt-2 flex">
    <div class="mb-1 pl-4 text-base">
     <select class="border w-full {{ (!$candidate->district_id) ? 'opacity-50' : '' }}" name="district_id">
                    <option value=""  value="" {{ (!$candidate->district_id) ? 'selected' : '' }}>-- District --</option>
                    @foreach(\App\District::all() as $district)
                        <option value="{{ $district->id }}" {{ ($candidate->district_id == $district->id) ? 'selected' : '' }}>({{ $district->type }}) {{ $district->name }}</option>
                    @endforeach
                  </select>
    </div>
    <div class="mb-1 pl-4 text-base">
     <select class="border w-full {{ (!$candidate->municipality_id) ? 'opacity-50' : '' }}" name="municipality_id">
                    <option value=""  value="" {{ (!$candidate->municipality_id) ? 'selected' : '' }}>-- Or City --</option>
                    @foreach(\App\Municipality::all() as $city)
                        <option value="{{ $city->id }}" {{ ($candidate->municipality_id == $city->id) ? 'selected' : '' }}>{{ $city->name }}</option>
                    @endforeach
                  </select>
    </div>
</div>

<div class="mt-1">
    <div class="mb-1 pl-4 text-base">
      <select class="border w-1/3" name="party">
                    <option value="" {{ (!$candidate->party) ? 'selected' : '' }}>-- Party --</option>
                    @foreach(['Democratic', 'Republican', 'Unenrolled'] as $party)
                        
                        <option value="{{ $party }}" {{ ($candidate->party == $party) ? 'selected' : '' }}>{{ $party }}</option>
                    @endforeach
                  </select>
    </div>
</div>

@include('admin.marketing.committee-member', ['type' => 'chair'])
@include('admin.marketing.committee-member', ['type' => 'treasurer'])

