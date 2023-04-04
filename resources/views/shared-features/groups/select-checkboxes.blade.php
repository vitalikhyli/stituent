@foreach($groups as $thegroup)
  <div class="pt-1">
      <input type="checkbox" id="group_{{ $thegroup->id }}" name="group_{{ $thegroup->id }}" value="{{ $thegroup->id }}" class="mr-1" />
      <label for="group_{{ $thegroup->id }}" class="font-normal"> {{ $thegroup->name }}</label>
  </div>
@endforeach