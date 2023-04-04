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
        Edit Notice
    </div> 

    

    <form action="/admin/notices/{{ $notice->id }}/update" method="post">

        @csrf

    <div class="flex">

        <div class="w-1/2">

            <div class="flex p-2 border-b" w-full>
                <div class="px-2 w-32 uppercase text-sm text-grey-darker">
                    app_type
                </div>
                <div class="px-2">
                    <select name="app_type">

                        @foreach(App\Team::all()->pluck('app_type')->unique()->toArray() as $app_type)
                            <option {{ ($notice->app_type == $app_type) ? 'selected' : '' }} value="{{ $app_type }}">{{ $app_type }}</option>
                        @endforeach

                    </select>
                </div>
            </div>



            <div class="flex p-2 border-b" w-full>
                <div class="px-2 w-32 uppercase text-sm text-grey-darker">
                    bg_color
                </div>
                <div class="px-2">
                    <input name="bg_color" value="{{ $notice->bg_color }}" type="text" class="border p-2">
                </div>
            </div>

            <div class="flex p-2 border-b w-full">
                <div class="px-2 w-32 uppercase text-sm text-grey-darker">
                    publish_at
                </div>
                <div class="px-2">
                    <input name="publish_at" value="{{ $notice->publish_at }}" type="text" class="border p-2">
                </div>
            </div>

            <div class="flex p-2 border-b w-full">
                <div class="px-2">
                    <label for="approved" class="font-normal">
                        <input id="approved" name="approved" {{ ($notice->approved) ? 'checked' : '' }} type="checkbox" class="border p-2"/> <span class="ml-2 uppercase text-sm text-grey-darker">Approved for Publishing?</span>
                    </label>
                </div>
            </div>

            <div class="flex p-2 border-b w-full">
                <div class="px-2">
                    <label for="archived_at" class="font-normal">
                        <input id="archived_at" name="archived_at" {{ ($notice->archived_at) ? 'checked' : '' }} type="checkbox" class="border p-2"/> <span class="ml-2 uppercase text-sm text-grey-darker">Archived_at <span class="text-blue ml-2">{{ $notice->archived_at }}</span></span>
                    </label>
                </div>
            </div>


        </div>

        <div class="w-1/2">
<!-- 
            <div class="py-2 font-bold text-center text-grey-dark uppercase text-sm">
                Preview
            </div> -->
            <div class="pr-2">
                @include('elements.one-notice', ['notice' => $notice])
            </div>

        </div>

    </div>

    <div class="flex p-2 border-b w-full">
        <div class="px-2 w-32 uppercase text-sm text-grey-darker">
            headline
        </div>
        <div class="px-2 w-full">
            <input name="headline" value="{{ $notice->headline }}" type="text" value="{{ $notice->headline }}" class="border p-2 font-bold text-lg w-full"/>
        </div>
    </div>

    <div class="flex p-2 border-b w-full">
        <div class="px-2 w-32 uppercase text-sm text-grey-darker">
            body
        </div>
        <div class="px-2 w-full">
            <textarea name="body" class="border p-2 w-5/6" rows="4" id="summernote">{!! $notice->body !!}</textarea>


                    <!-- <textarea id="summernote" name="content" rows="6" id="email-content"> -->
                    <!-- </textarea> -->

        </div>
    </div>

    <div class="py-2">

        <input type="submit" class="rounded-lg bg-blue text-sm text-white px-2 py-1" value="Save" />

        <input type="submit" formaction="/admin/notices/{{ $notice->id }}/update/close" class="rounded-lg bg-blue-dark text-sm text-white px-2 py-1" value="Save and Close" />
        
    </div>

</form>




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

