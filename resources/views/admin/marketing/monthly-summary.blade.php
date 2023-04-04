@if($summary->summary['total'] > 0)


<div class="w-1/2 my-2 font-mono text-sm">
    <div class="text-lg font-bold border-b-2 border-red p-2">
        {{ \Carbon\Carbon::parse($summary->date)->format("F Y") }}
    </div>  
    <div>

       

        <table class="w-full">
            <tr>
                <td class="text-right border-b p-1 bg-grey-lightest">
                    New Candidates
                </td>
                <td class="w-10 text-right border-b p-1">
                    {{ $summary->summary['total'] }}
                </td>
            </tr>
            <tr>
                <td class="text-right border-b p-1 bg-grey-lightest">
                    Democrats
                </td>
                <td class="w-10 text-right border-b p-1">
                    {{ $summary->summary['dem'] }}
                </td>
            </tr>
            <tr>
                <td class="text-right border-b p-1 bg-grey-lightest">
                   Republicans
                </td>
                <td class="w-10 text-right border-b p-1">
                    {{ $summary->summary['gop'] }}
                </td>
            </tr>
            <tr>
                <td class="text-right border-b p-1 bg-grey-lightest">
                    Unenrolled
                </td>
                <td class="w-10 text-right border-b p-1">
                    {{ $summary->summary['ind'] }}
                </td>
            </tr>
            <tr>
                <td class="text-right border-b p-1 bg-grey-lightest">
                    Men
                </td>
                <td class="w-10 text-right border-b p-1">
                    {{ $summary->summary['men'] }}
                </td>
            </tr>
            <tr>
                <td class="text-right border-b p-1 bg-grey-lightest">
                    Women
                </td>
                <td class="w-10 text-right border-b p-1">
                    {{ $summary->summary['women'] }}
                </td>
            </tr>
            <tr>
                <td class="text-right border-b p-1 bg-grey-lightest">
                    Average Age
                </td>
                <td class="w-10 text-right border-b p-1">
                    {{ $summary->summary['age'] }}
                </td>
            </tr>
        </table>

    </div>
</div>

@endif
