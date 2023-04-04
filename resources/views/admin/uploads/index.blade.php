@extends('admin.base')

@section('title')
    Upload City Data
@endsection

@section('breadcrumb')
	<a href="/admin">Admin</a>
    &nbsp;> Upload Data
@endsection

@section('style')
    
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.js" defer></script>
    

    @livewireStyles

@endsection

@section('main')

    <div class="tracking-normal">

        <div class="text-3xl font-bold border-b-4 pb-3 mt-6 flex">

            <div class="w-1/2">
                Upload Voter Files to Master
            </div>

            <div class="flex-grow text-base text-grey-dark w-64 font-normal">
                <span class="text-black font-bold mr-1">Note:</span> The purpose of this page is to upload voter files we receive and intregrate them with a state Master Voter Table.
            </div>


        </div>

        @livewire('admin-upload-to-master.new-upload', ['import_id' => null])

<!--         <div class="text-2xl font-bold border-b-4 pb-2 mt-6">
            Previous Uploads
        </div> -->

        @foreach ($uploads->groupBy('state') as $state => $imports_in_state)

        <div class="border-b-2 text-lg py-1 font-bold">
            @if(!$state)
                (No State) <a href="?nullStatesToMA=true" class="text-blue text-sm cursor-pointer hover:bg-yellow">Click to Change Nulls to MA</a>
            @else
                {{ $state }}
            @endif
        </div>

        <table class="table text-sm">
            <tr class="bg-grey-lightest uppercase text-xs text-grey-darker">
                <td class="w-32">
                 
                </td>
                <td>Created At</td>
                <td>File Count</td>
                <td>Started At</td>
                <td>Completed At</td>
                <td># New</td>
                <td># Replaced</td>
                <td># Changed</td>
            </tr>
            @foreach ($imports_in_state as $import)
                <tr>
                    <td class="truncate">
                        <a href="/admin/uploads/{{ $import->id }}/edit">
                            @if ($import->municipality)
                                @if(!$import->municipality->name)
                                    (No Municipality)
                                @else
                                    {{ $import->municipality->name }}
                                @endif
                            @else
                                (No Municipality)
                            @endif
                        </a>
                    </td>
                    <td>{{ $import->created_at }}</td>
                    <td>{{ number_format($import->file_count) }}</td>
                    <td>{{ $import->started_at }}</td>
                    <td>{{ $import->completed_at }}</td>
                    <td>
                        @if($import->updated_count)
                            {{ number_format($import->new_count) }}
                        @else
                            <span class="text-grey">-</span>
                        @endif
                    </td>
                    <td>
                        @if($import->updated_count)
                            {{ number_format($import->updated_count - $import->changed_count) }}
                        @else
                            <span class="text-grey">-</span>
                        @endif
                    </td>
                    <td>
                        @if($import->changed_count)
                            {{ number_format($import->changed_count) }}
                        @else
                            <span class="text-grey">-</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>

        @endforeach

    </div>


@endsection

@section('javascript')

    @livewireScripts

    <script type="text/javascript">
        
        $(document).ready(function() {

            $("#municipality_lookup").focus();

        });

    </script>

@endsection
