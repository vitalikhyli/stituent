<div class="my-4 w-full">

  <div class="w-full">

     <div class="ml-1">
        <label for="linked_{{ $person_id }}"><input type="checkbox" value="{{ $person_id }}" checked name="linked[]" id="linked_{{ $person_id }}" />
        <span class="ml-2">{{ $full_name }}</span></label>
     </div>
     
  </div>

  <div class="w-full flex">

     @if($group->cat->has_position)
      <div class="ml-1 flex-grow">
          <select name="position_{{ $person_id }}" class="border border-grey-darker">
            <option value="">-- No Position --</option>
            <option value="supports">Supports</option>
            <option value="undecided">Undecided</option>
            <option value="concerned">Concerned</option>
            <option value="opposed">Opposed</option>
          </select>
      </div>
     @endif

    @if($group->cat->has_title)
     <div class="ml-1 flex-grow">
        <input type="text" name="title_{{ $person_id }}" class="w-full border-b-2 border-blue  border-grey-darker p-2" value="" placeholder="Title"/>
     </div>
    @endif

    @if($group->cat->has_notes)
     <div class="ml-1 flex-grow">
        <input type="text" name="notes_{{ $person_id }}" class="w-full border-b-2 border-blue  border-grey-darker p-2" placeholder="Notes" />
     </div>
    @endif

  </div>

</div>