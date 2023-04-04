@foreach($groups as $thegroup)
  <div class="pt-1">
      <input required type="radio" id="group_{{ $merge_order }}_{{ $thegroup->id }}" name="group_id_{{ $merge_order }}" value="{{ $thegroup->id }}" class="mr-1" {{ ($group_id == $thegroup->id) ? 'checked' : '' }} />
      <label for="group_{{ $merge_order }}_{{ $thegroup->id }}" class="font-normal"> {{ $thegroup->name }}</label>
  </div>
@endforeach