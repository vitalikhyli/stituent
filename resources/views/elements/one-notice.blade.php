@if(isset($notice) && ($notice))

    <div class="{{ $notice->bg_color }} px-4 pt-3 pb-4 mb-2">

        <div class="text-black text-xl font-sans mb-2 font-bold pb-2">

            <span class="text-base float-right">
                {{ Carbon\Carbon::parse($notice->publish_at)->format("n/j") }}
            </span>

            <div class="border-b">
                {!! $notice->headline !!}
            </div>

        </div>

        <div class="text-sm text-black">

            {!! $notice->body !!}

        </div>

    </div>

@endif


<!--     <div class="bg-green-lightest p-4 flex mb-2">
        <div class="text-black text-xl font-sans mb-2 font-bold pb-2">
            New!<br>
            <span class="text-base">
                3/3
            </span>
        </div>

        <div class="text-sm">
            <ul>
                <li class="mb-1">
                    <b class="text-black">Master Email List</b> Bulk edit the master email list by searching for constituents and selecting who you would like to add and remove.
                </li>
                <li class="mb-1">
                    <b class="text-black">Cases Report</b> Run a full report summarizing all your cases of the desired type for the selected time period.
                </li>
            </ul>
        </div>

    </div>
 -->