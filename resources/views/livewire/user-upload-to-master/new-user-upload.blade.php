<div class="w-full">

    <form action="/{{ Auth::user()->team->app_type  }}/useruploads/upload" method="post" enctype="multipart/form-data" class="w-3/4 items-center w-full">

    @csrf

    <div class="flex">

        <div class="w-1/2">
            <input wire:change="fileChosen" type="file" name="fileToUpload" id="fileToUpload">
            <!-- <input type="hidden" name="import_id" value="" /> -->
        </div>

        @if($file_chosen)
            <div class="w-1/2 text-center pl-6">
                <input type="submit" value="Upload New" name="submit" class="bg-blue text-white cursor-pointer rounded-full py-2 px-4">
            </div>
        @endif

    </div>


    </form>

</div>