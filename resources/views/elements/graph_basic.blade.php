<div id="hidden-at-first-group">
<table class="border-b" style="height:{{ $max_height +25}}px;" class="w-full">
    <tr>
    @foreach($items as $key => $item)
        <td valign="bottom">
            <div class="hidden-at-first text-center p-1 text-xs text-grey-dark" style="width:{{$col_width}}px;">
                {{ $key }}
            </div>
            <div style="width:{{$col_width}}px;height:{{ $item/$max_y*$max_height }}px;" class=" hidden-at-first text-xs text-center text-sm hover:bg-orange-dark cursor-pointer p-1 border-r border-white {{ ($loop->last) ? 'bg-blue-darkest' : 'bg-blue' }} rounded-t text-white {{ ($item == 0) ? 'opacity-0' : '' }}">
                {{ number_format($item,0,'.',',') }}
            </div>
        </td>
    @endforeach
    </tr>
</table>
</div>
