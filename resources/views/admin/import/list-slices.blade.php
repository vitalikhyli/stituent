<div id="display">

@include('admin.import.list-status')

<table class="w-full">

@foreach($slices->where('ready',0) as $theslice)
<tr class="{{ (!$loop->last) ? 'border-b' : '' }}">
    <td class="p-2 px-4" colspan="4">
        @if($theslice->count > 0)
        <div class="bg-blue-lightest border-2 m-1 mb-2 rounded-lg p-1">

            <i class="fas fa-cog fa-spin text-blue" style="font-size:24px"></i>

            Processing <b>{{ $theslice->name }}</b>

            {!! $theslice->jobReport() !!}

        </div>
        @else
        <div class="bg-orange-lightest border-2 m-1 mb-2 rounded-lg p-1">

            <i class="fas fa-cog fa-spin text-orange" style="font-size:24px"></i>

            Waiting to process <b>{{ $theslice->name }}</b>
        </div>
        @endif
    </td>
</tr>
@endforeach

@foreach($slices->where('ready',1) as $theslice)
<tr class="{{ (!$loop->last) ? 'border-b' : '' }}">
    <td class="p-2 px-4">
        <i class="fas fa-pizza-slice ml-1 w-6"></i>{{ $theslice->name }}
        <div class="text-xs text-grey-dark">populated: {{ \Carbon\Carbon::parse($theslice->updated_at)->format("n/j/y h:i:s") }}</div>
    </td>
    <td class="p-2 px-4 text-right text-sm">
         {{ number_format($theslice->count,0,'.',',') }}
    </td>
    <td class="p-2 px-4 text-sm">
        {{$theslice->slice_sql}}
    </td>
    <td class="p-2 px-4 w-4">
        <a href="/admin/import/{{ $theslice->id }}/edit">
        <button type="button" class="px-4 py-2 rounded-full bg-blue text-white"><i class="fas fa-arrow-right"></i>
        </button>
        </a>
    </td>
</tr>
@endforeach
</table>


@if($slices->count() > 0)
    <a href="/admin/import/{{ $theimport->id }}/repopulateSlices">
    <button type="button" id="submit" name="import" class="px-4 py-2 rounded-full bg-blue text-white m-4">Repopulate Slices
    </button>
    </a>
@endif


</div>

