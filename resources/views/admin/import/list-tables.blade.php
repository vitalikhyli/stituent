<div id="display">


@include('admin.import.list-status')


@foreach($folders as $thefolder)

<div class="p-1 border-b bg-grey-lighter">
<i class="fas fa-folder"></i> {{ $thefolder->name }}
</div>

    <div class="text-sm font-normal mb-4">
    @foreach($imports->where('data_folder_id',$thefolder->id)
                     ->pluck('team_id')->unique() as $theteam_id)


        <a data-toggle="collapse" href="#{{ $theteam_id }}_main" role="button" aria-expanded="false" aria-controls="{{ $theteam_id }}_main" class="text-black">
        <div class="ml-6 p-1 {{ (!$loop->first) ? 'border-t' : '' }} bg-grey-lightest hover:bg-orange-lightest">
            @if(\App\Team::find($theteam_id)->admin)
            <i class="fas fa-user-circle text-blue"></i> 
                <button class="bg-blue text-white px-2 rounded-lg">
                    Admin
                </button>
            @else
                <i class="fas fa-user-circle"></i>
            @endif
             {{ \App\Team::find($theteam_id)->name }}
        </div>
        </a>


        <div class="ml-12 p-1 collapse.show" id="{{ $theteam_id }}_main">
            <table class="w-full">

            @foreach($imports->where('data_folder_id',$thefolder->id)
                         ->where('team_id', $theteam_id)
                         ->where('type', 'v')
                         ->where('ready', 0)
                         ->where('archived', 0) as $theimport)

                <tr>
                    <td class="" colspan="9">
                   
                    @if(($theimport->count > 0) && ($unfinished_workers > 0))
                    <div class="bg-blue-lightest border-2 m-1 mb-2 rounded-lg p-1 shadow">

                        <i class="fas fa-cog fa-spin text-blue" style="font-size:24px"></i>

                        Processing <b>{{ $theimport->name }}</b>

                        {!! $theimport->jobReport() !!}

                    </div>
                    @else
                    <div class="bg-orange-lightest border-2 m-1 mb-2 rounded-lg p-1 shadow">

                        <i class="fas fa-cog fa-spin text-orange" style="font-size:24px"></i>

                        Waiting to process <b>{{ $theimport->name }}</b>
                    </div>
                    @endif
                       
                    </td>
                </tr>

            @endforeach

            @foreach($imports->where('data_folder_id',$thefolder->id)
                         ->where('team_id', $theteam_id)
                         ->where('type', 'v')
                         ->where('ready', 1)
                         ->where('archived', 0) as $theimport)

                
                <tr>
                    <td class="w-1/3 border-r">


                        <i class="fas fa-table mr-1"></i>

                        @if($theimport->slice_of_id)
                        <span data-toggle="tooltip" data-placement="top" title="Slice of Table {{ $theimport->SliceOf()->slug }} ({{ $theimport->SliceOf()->name }})" class="whitespace-no-wrap rounded-lg px-2 py-1 bg-green-lighter  cursor-pointer hover:bg-blue-lighter"><i class="fas fa-pizza-slice"></i></span>
                        @endif

                        <a href="/admin/import/{{ $theimport->id }}/edit">{{ $theimport->name }}</a>
                    </td>
                    <td class="border-r w-12 text-xs text-right px-2 text-black whitespace-no-wrap">
                        {{ \Carbon\Carbon::parse($theimport->created_at)->format("n.j.y g:i a") }}
                    </td>
                    <td class="border-r w-18 text-xs text-right px-2">
                        ({{ number_format($theimport->realCount(),0,'.',',')}})
                    </td>
                    <td class="border-r w-48 whitespace-no-wrap text-grey-dark text-xs ml-2 px-2">
                        {{ substr($theimport->table_bench,2) }}
                    </td>
                    <td class="border-r w-48 whitespace-no-wrap text-blue text-xs p-2">
                        @if($theimport->deployed)
                            <i class="fas fa-check-circle"></i>
                            {{ substr($theimport->table_deploy,2)  }}
                        @else
                            <a href="/admin/import/{{ $theimport->id }}/deploy">
                            <button data-toggle="tooltip" data-placement="top" title="Rename tables to deploy" class="text-xs uppercase border text-grey-darker rounded-lg px-2 py-1">
                                Deploy
                            </button>
                            </a>
                        @endif
                    </td>

                    <td class="w-10 pl-1">
                        @if($slices->where('slice_of_id',$theimport->id)->count() >0)
                        <a href="/admin/import/{{ $theimport->id }}/edit" class="text-black hover:text-black">
                        <div data-toggle="tooltip" data-placement="top" title="{{ $slices->where('slice_of_id',$theimport->id)->count() }} slices are based on this table." class="rounded-lg px-2 py-1 bg-green-lighter cursor-pointer hover:bg-blue-lighter whitespace-no-wrap text-xs">
                            <i class="fas fa-pizza-slice"></i>
                            x {{ $slices->where('slice_of_id',$theimport->id)->count() }}
                        </div>
                        </a>
                        @endif
                    </td>

                    <td class="w-8 pl-1">
                            <a href="/admin/import/{{ $theimport->id }}/edit" class="text-white hover:text-white">
                            <button data-toggle="tooltip" data-placement="top" title="Edit" class="rounded-lg px-2 py-1 bg-blue cursor-pointer text-xs">
                                 <i class="fas fa-pencil-alt"></i>
                            </button>
                            </a>
                    </td>
                    <td class="w-8 pl-1">
                            <a href="/admin/import/{{ $theimport->id }}/copy" class="text-white hover:text-white">
                            <button data-toggle="tooltip" data-placement="top" title="Duplicate" class="rounded-lg px-2 py-1 bg-blue cursor-pointer text-xs">
                                 <i class=" fas fa-copy"></i>
                            </button>
                            </a>
                    </td>
                    <td class="w-8 pl-1">
                            @if(!$theimport->deployed)
                                <a href="/admin/import/{{ $theimport->id }}/archive" class="text-white hover:text-white">
                                <button data-toggle="tooltip" data-placement="top" title="Send to Archive DB" class="rounded-lg px-2 py-1 bg-blue cursor-pointer text-xs">
                                     <i class="fas fa-database"></i>
                                </button>
                                </a>
                            @endif
                    </td>

            </tr>
                <tr class="border-b">
                    <td class="border-r text-grey-dark text-sm">
                        <!-- &rarr; <i class="fas fa-table ml-1"></i> -->
                        <i class="ml-6 fas fa-table pr-2"></i>
                        Households
                    </td>
                    <td class="border-r text-xs text-right px-2 text-black whitespace-no-wrap">
                        {{ \Carbon\Carbon::parse($theimport->created_at)->format("n.j.y g:i a") }}
                    </td>
                    <td class="border-r text-xs text-right px-2">
                        ({{ number_format($theimport->relatedHouseholds()->realCount(),0,'.',',') }})
                    </td>

                    <td class="border-r text-grey-dark text-xs ml-2 px-2">
                        {{ substr($theimport->relatedHouseholds()->table_bench,2) }}
                    </td>
                    <td class="border-r text-blue text-xs p-2">
                        @if($theimport->relatedHouseholds()->deployed)
                            <i class="fas fa-check-circle"></i>
                            {{ substr($theimport->relatedHouseholds()->table_deploy,2) }}
                        @endif
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                    <td>
                    </td>
                </tr>
                

            @endforeach
            </table>








            @if($imports->where('data_folder_id',$thefolder->id)
                         ->where('team_id', $theteam_id)
                         ->where('type', 'v')
                         ->where('archived', 1)->count() > 0)
            <a data-toggle="collapse" href="#{{ $theteam_id }}_archive" role="button" aria-expanded="false" aria-controls="{{ $theteam_id }}_archive" class="text-black">
                <div>
                    <i class="fas fa-database mt-2 mr-2 w-4"></i> 
                    <!-- <i class="fas fa-folder mt-2"></i> --> Archive
                </div>
            </a>
            <div class="collapse ml-6 p-1" id="{{ $theteam_id }}_archive">
                @foreach($imports->where('data_folder_id',$thefolder->id)
                         ->where('team_id', $theteam_id)
                         ->where('type', 'v')
                         ->where('archived', 1) as $theimport)

                
                <div class="flex w-full">
                    <div class="w-1/3">
                        <i class="fas fa-table"></i>
                        <a class="hover:bg-grey-light rounded-lg px-2" href="/admin/data/table/{{ $theimport->id }}">{{ $theimport->name }}</a>
                    </div>
                    <div class="w-12 text-xs text-right pl-2">
                        {{ number_format($theimport->count,0,'.',',')}}
                    </div>
                    <div class="w-1/3 text-grey-darker text-xs ml-2">
                        {{ substr($theimport->table_bench,2) }}
                    </div>
                    
                </div>
                <div class="flex w-full">
                    <div class="w-1/3 text-grey-dark">
                        &rarr; <i class="fas fa-table ml-1"></i>
                        (Households)
                    </div>
                    <div class="w-12 text-xs text-right pl-2">
                        {{ number_format($theimport->relatedHouseholds()->count,0,'.',',') }}
                    </div>
                    <div class="w-1/3 text-grey-darker text-xs ml-2">
                        {{ substr($theimport->relatedHouseholds()->table_bench,2) }}
                    </div>
                </div>
                

            @endforeach
            </div>
            @endif








            @if(false)
            @if($theteam_id == 1)
            <a data-toggle="collapse" href="#{{ $theteam_id }}_elections" role="button" aria-expanded="false" aria-controls="{{ $theteam_id }}_elections" class="text-black">
                <div>
                    <i class="fas fa-folder mt-2 mr-2   "></i> Elections
                </div>
            </a>
            <div class="collapse ml-6 p-1" id="{{ $theteam_id }}_elections">
                <div class="flex w-full">
                    <div class="w-48">
                        <i class="fas fa-table"></i>
                        <a href="">General 2016</a>
                    </div>
                </div>
                <div class="flex w-full">
                    <div class="w-48">
                        <i class="fas fa-table"></i>
                        <a href="">General 2014</a>
                    </div>
                </div>
                <div class="flex w-full">
                    <div class="w-48">
                        <i class="fas fa-table"></i>
                        <a href="">General 2012</a>
                    </div>
                </div>
                <div class="flex w-full">
                    <div class="w-48">
                        <i class="fas fa-table"></i>
                        <a href="">Local 2011</a>
                    </div>
                </div>
            </div>
            @endif
            @endif


        </div>
       
    @endforeach
    </div>

@endforeach
</div>