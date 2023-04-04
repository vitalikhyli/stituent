<table class="mx-auto" style="height:400px;">
        <tr>

        @foreach($chart_data['bars'] as $bar)


            <td valign="bottom" class="bg-white border-b">

                <div class="text-xs mb-2 z-10" style="top:0px;">

                    <div data-toggle="tooltip" data-placement="top" title="" style="width:20px;height:{{ ($bar['value']/$chart_data['max']) * 350 }}px;" class="graphbar-vertical text-xs text-right hover:bg-orange-dark cursor-pointer p-1 pt-4 text-right border-r border-white {{ ($loop->last) ? 'bg-blue-darkest shadow-lg font-bold' : 'bg-blue' }} rounded-t text-white">
                        <div class="text-right" style="transform: rotate(-90deg);">

                                {{ $bar['value'] }}
     
                        </div>
                    </div>

                    @if ($bar['label']) 
                        <div class="absolute h-3 w-2 border-l ml-2 mt-2"></div>
                        <div class="absolute mt-6 -ml-4" style="transform: rotate(0deg);">
                            {{ $bar['label'] }}
                        </div>
                    @endif
                </div>

            </td>

        @endforeach
        </tr>
    </table>