<div>


        <div class="bg-white">


            <div class="">
                <div class="text-black font-bold p-2">
                    Quick Report
                </div>
                <table class="table text-sm text-grey-darker">
                    <tr>
                        <td>This Week</td>
                        <td>{{ $call_log->this_week }} {{ Str::plural('Entry', $call_log->this_week) }}</td>
                        @if($call_log->this_week <= 0)
                            <td width="30%" colspan="2" class="text-center">
                                --
                            </td>
                        @else
                            <td width="15%" class="text-center">
                                <a target="_blank" href="/call-log/reports?format=pdf&start={{ $call_log->this_week_start }}&end={{ $call_log->this_week_end }}">
                                    PDF
                                </a>
                            </td>
                            <td width="15%" class="text-center">
                                <a href="/call-log/reports?format=csv&start={{ $call_log->this_week_start }}&end={{ $call_log->this_week_end }}">
                                    CSV
                                </a>
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td>Last 7 Days</td>
                        <td>{{ $call_log->last_7 }} {{ Str::plural('Entry', $call_log->last_7) }}</td>
                        @if($call_log->last_7 <= 0)
                            <td width="30%" colspan="2" class="text-center">
                                --
                            </td>
                        @else
                            <td width="15%" class="text-center">
                                <a target="_blank" href="/call-log/reports?format=pdf&start={{ $call_log->last_7_start }}&end={{ $call_log->today }}">
                                    PDF
                                </a>
                            </td>
                            <td width="15%" class="text-center">
                                <a href="/call-log/reports?format=csv&start={{ $call_log->last_7_start }}&end={{ $call_log->today }}">
                                    CSV
                                </a>
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td>This Month</td>
                        <td>{{ $call_log->this_month }} {{ Str::plural('Entry', $call_log->this_month) }}</td>
                        @if($call_log->this_month <= 0)
                            <td width="30%" colspan="2" class="text-center">
                                --
                            </td>
                        @else
                            <td width="15%" class="text-center">
                                <a target="_blank" href="/call-log/reports?format=pdf&start={{ $call_log->this_month_start }}&end={{ $call_log->this_month_end }}">
                                    PDF
                                </a>
                            </td>
                            <td width="15%" class="text-center">
                                <a href="/call-log/reports?format=csv&start={{ $call_log->this_month_start }}&end={{ $call_log->this_month_end }}">
                                    CSV
                                </a>
                            </td>
                        @endif
                    </tr>
                    <tr>
                        <td>Last 30 Days</td>
                        <td>{{ $call_log->last_30 }} {{ Str::plural('Entry', $call_log->last_30) }}</td>
                        @if($call_log->last_30 <= 0)
                            <td width="30%" colspan="2" class="text-center">
                                --
                            </td>
                        @else
                            <td width="15%" class="text-center">
                                <a target="_blank" href="/call-log/reports?format=pdf&start={{ $call_log->last_30_start }}&end={{ $call_log->today }}">
                                    PDF
                                </a>
                            </td>
                            <td width="15%" class="text-center">
                                <a href="/call-log/reports?format=csv&start={{ $call_log->last_30_start }}&end={{ $call_log->today }}">
                                    CSV
                                </a>
                            </td>
                        @endif
                    </tr>
                </table>

                <div class="">
                    <div class="text-black font-bold p-2">
                        Custom Report
                    </div>
                    <form id="custom-date-report-form" method="get" action="/call-log/reports">

                        @csrf

                    <table class="table">
                        <tr>

                            <td class="border-b pt-2">
                                By Date
                            </td>

                            <td class="border-b">
                                <input type="text" required autocomplete="off" name="start" class="form-control datepicker" placeholder="From" />
                            </td>
                            <td class="border-b">
                                <input type="text" required autocomplete="off" name="end" class="form-control datepicker" placeholder="To" />
                            </td>

                        </tr>

                        <tr>

                            <td class="pt-2">
                                By User
                            </td>

                            <td class="" colspan="2">

                                <select name="user" class="form-control border-transparent">

                                    <option value="">
                                        -- ANY --
                                    </option>

                                    @foreach(Auth::user()->team
                                                         ->usersAll()
                                                         ->orderBy('name')
                                                         ->get() as $user)

                                        <option value="{{ $user->id }}">
                                            {{ $user->name }}
                                        </option>

                                    @endforeach

                                </select>

                            </td>

                        </tr>

                        <tr>

                            <td class="">
                                &nbsp;
                            </td>

                            <td class="" colspan="2">

                                <div class="float-right">
                                    
                                    <button type="submit" name="format" value="pdf" class="btn btn-primary">PDF</button>

                                    <button type="submit" name="format" value="csv" class="btn btn-primary">CSV</button>

                                </div>

                            </td>

                        </tr>


                    </table>
                    </form>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                
            </div>
        </div>


</div>
