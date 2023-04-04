<div class="text-center text-sm text-grey-dark">
	Week Summary <span class="font-bold">(Beta)</span>
</div>

<table class="w-full mb-4">
    <tr>
    @foreach($week->days as $key => $day)
        <td valign="bottom">

            <div style="width:28px;height:{{ $day['num_contacts'] * 24 }}px;" class="text-xs text-center text-xs {{ ($day['today']) ? 'bg-orange-dark' : 'bg-grey' }} cursor-pointer p-1 border-r border-white rounded-t text-white {{ ($day['num_contacts'] == 0) ? 'opacity-0' : '' }}">
                {{ $day['num_contacts'] }}
            </div>

        </td>

		<td valign="bottom">
            <div style="width:28px;height:{{ $day['num_cases'] * 24 }}px;" class="text-xs text-center text-xs {{ ($day['today']) ? 'bg-red-dark' : 'bg-grey' }} cursor-pointer p-1 border-r border-white rounded-t text-white {{ ($day['num_cases'] == 0) ? 'opacity-0' : '' }}">
                {{ $day['num_cases'] }}
            </div>

        </td>
    @endforeach
    </tr>

    <tr>
    @foreach(['sun','mon','tue','wed','thr','fri','sat'] as $keyday)
        <td valign="bottom" colspan="2">

            <div class="border-t border-b w-full text-center p-1 text-xs text-grey-dark text-center text-xs">
                {{ $keyday }}
            </div>

        </td>
    @endforeach
    </tr>
</table>
