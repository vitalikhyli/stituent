<option {{ ($thedirectory->id == $file->directory_id) ? 'selected' : '' }} value="{{ $thedirectory->id }}">{{ str_repeat('--', $level) }} {{ $thedirectory->shortened_name }}</option>
@foreach ($thedirectory->subModels() as $thesub)

   @include('shared-features.files.one-directory-dropdown', ['thedirectory' => $thesub, 'level' => $level+1])

@endforeach