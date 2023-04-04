@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')

    Admin

@endsection


@section('style')

<!-- <script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.0/dist/Chart.min.js"></script> -->

<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">


@endsection

@section('main')


<div class="w-full mb-4 pb-4">


    <div class="text-xl mb-4 border-b-4 border-red py-2">
        Edit Terms
    </div> 

    <form action="/admin/terms/{{ $term->id }}/update" method="post">

    @csrf

    @if($term->signers->count() > 0)
        <div class="p-4 bg-black text-grey-lightest -mt-4 mb-4">
            These terms have {{ $term->signers->count() }} signature(s), and therefore it cannot be edited.


            <input type="submit" formaction="/admin/terms/{{ $term->id }}/update/close-no-update" class="rounded-lg bg-red text-sm text-white px-2 py-1 float-right" value="Close without Updating" />

        </div>
    @endif

    <div class="flex">

        <div class="w-1/2">

            <div class="flex p-2 border-b w-full">
                <div class="px-2 w-32 uppercase text-sm text-grey-darker">
                    Effective At
                </div>
                <div class="px-2">
                    <input name="effective_at" value="{{ $term->effective_at }}" type="text" class="border p-2">
                </div>
            </div>

            <div class="flex p-2 border-b w-full">
                <div class="px-2">
                    <label for="publish" class="font-normal">
                        <input id="publish" name="publish" {{ ($term->publish) ? 'checked' : '' }} type="checkbox" class="border p-2"/> <span class="ml-2 uppercase text-sm text-grey-darker">Ready to Publish (make available for signing)</span>
                    </label>
                </div>
            </div>

        </div>

    </div>

    <div class="flex p-2 border-b w-full">
        <div class="px-2 w-32 uppercase text-sm text-grey-darker">
            Title (for Internal Reference)
        </div>
        <div class="px-2 w-full">
            <input name="title" value="{{ $term->title }}" type="text" value="{{ $term->title }}" class="border p-2 font-bold text-lg w-full"/>
        </div>
    </div>

    <div class="flex p-2 border-b w-full">
        <div class="px-2 w-32 uppercase text-sm text-grey-darker">
            Text
        </div>
        <div class="px-2 w-full">
            <textarea name="text" class="border p-2 w-5/6" id="summernote">{!! $term->text !!}</textarea>
        </div>
    </div>

    <div class="py-2">

        @if($term->signers->count() == 0)

            <input type="submit" class="rounded-lg bg-blue text-sm text-white px-2 py-1" value="Update" />

            <input type="submit" formaction="/admin/terms/{{ $term->id }}/update/close" class="rounded-lg bg-blue-dark text-sm text-white px-2 py-1" value="Update and Close" />

        @endif
        
    </div>

</form>

<div class="text-xl border-b-4 border-red py-2 mt-4">
    Signers ({{ number_format($term->signers->count()) }})
</div> 

<div class="table text-sm">

    <div class="table-row bg-grey-lighter">
        <div class="table-cell p-1 border-b">
            Accepted at
        </div>
        <div class="table-cell p-1 border-b">
            User Name
        </div>
        <div class="table-cell p-1 border-b">
            User Email
        </div>
        <div class="table-cell p-1 border-b">
            Belonging to Teams
        </div>
    </div>
    @foreach($term->signers as $signer)

        <div class="table-row">
            <div class="table-cell p-1 border-b">
                {{ \Carbon\Carbon::parse($signer->accepted_at)->format('n/d/y @ g:i a') }}
            </div>
            <div class="table-cell p-1 border-b">
                {{ $signer->user_name }}
            </div>
            <div class="table-cell p-1 border-b">
                {{ $signer->user_email }}
            </div>
            <div class="table-cell p-1 border-b">
                @foreach($signer->teams() as $team)

                    <li>{{ $team->name }}</li>
               
                @endforeach
            </div>
        </div>

    @endforeach

</div>



<br />
<br />

@endsection

@section('javascript')

<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>

<script type="text/javascript">
    
    $('#summernote').summernote({
      toolbar: [
        // [groupName, [list of button]]
        ['codeview'],
        ['style', ['bold', 'italic', 'underline', 'clear']],
        ['font', ['strikethrough', 'superscript', 'subscript']],
        ['fontsize', ['fontsize']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['height', ['height']],
        ['insert', ['link', 'picture', 'hr']]
      ],
      height:200,
      callbacks: {

          onImageUpload : function(files, editor, welEditable) {
    
                 for(var i = files.length - 1; i >= 0; i--) {
                         sendFile(files[i], this);
                }
            }
        }
    });

</script>


@endsection

