@if(!$edit)

   <div class="py-2 pr-1 text-left align-middle">
        Date: <input type="text" class="datepicker border p-1 rounded" name="add_date" placeholder="{{ \Carbon\Carbon::now()->toDateString() }}" />

    </div>

    Part of Sequence:
    <select name="add_sequence">
        <option value="">-- None --</option>
        <option value="NewCandidate">NewCandidate</option>
    </select>

    Step:
    <select name="add_step">
        @foreach([null, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] as $step)
            <option value="{{ $step }}">{{ ($step == null) ? '----' : $step }}</option>
        @endforeach
    </select>

    <div class="py-2 pr-1 text-left align-middle">
        <textarea class="h-24 text-black border p-2 rounded w-full" name="add_notes" placeholder="Notes" /></textarea>
    </div>

    <div class="w-full text-right">
        <input type="submit" name="save" value="Add" class="inline mr-2 rounded-lg bg-blue hover:bg-grey-dark text-white text-sm px-8 py-2 mt-1 shadow ml-2" />
    </div>

@else

    <div>
        Date: <input type="text" value="{{ $contact->created_at }}" class="datepicker border p-1 rounded" name="edit_contact_{{ $contact->id }}_date" placeholder="{{ \Carbon\Carbon::now()->toDateString() }}" />

    </div>

    Part of Sequence:
    <select name="edit_contact_{{ $contact->id }}_sequence">
        <option {{ ($contact->sequence == '') ? 'selected' : '' }} value="">-- None --</option>
        <option {{ ($contact->sequence == 'NewCandidate') ? 'selected' : '' }} value="NewCandidate">NewCandidate</option>
    </select>

    Step:
    <select name="edit_contact_{{ $contact->id }}_step">
        @foreach([null, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10] as $step)
            <option {{ ($contact->step == $step) ? 'selected' : '' }} value="{{ $step }}">{{ ($step == null) ? '----' : $step }}</option>
        @endforeach
    </select>

    <div class="py-2 pr-1 text-left align-middle">
        <textarea class="h-24 text-black border p-2 rounded w-full" name="edit_contact_{{ $contact->id }}_notes" placeholder="Notes" />{{ $contact->notes }}</textarea>
    </div>

    <div class="w-full text-right">
        <input type="submit" name="submit_edit_contact_{{ $contact->id }}" value="Update This" class="inline mr-2 rounded-lg bg-blue hover:bg-grey-dark text-white text-sm px-8 py-2 mt-1 shadow ml-2" />
    </div>


@endif