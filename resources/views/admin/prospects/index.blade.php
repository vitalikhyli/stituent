@extends('admin.base')

@section('title')

    Admin Prospects

@endsection

@section('breadcrumb')



@endsection

@section('style')

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.0/dist/Chart.min.js"></script>

@endsection

@section('main')


<div class="w-full mb-4 pb-2">

    <div class="text-xl border-b-4 border-red py-2">

        <a href="/admin/prospects/new">
            <button class="rounded-lg bg-blue-dark float-right text-white px-4 py-2 text-sm">
                Manual Add
            </button>
        </a>

        <a href="/admin/prospects/new">
            <button class="mr-2 rounded-lg bg-blue-darker float-right text-white px-4 py-2 text-sm">
                cf:prospects
            </button>
        </a>

        Prospects
    </div>  


    <!-- <table class="w-full text-sm">

        <tr class="text-xs cursor-pointer border-b-4 bg-grey-lighest">

            <th class="font-normal p-2 w-48 whitespace-no-wrap">
                Source
            </th>

            <th class="font-normal p-2 w-48 whitespace-no-wrap">
                Status
            </th>

            <th class="font-normal p-2 w-48 whitespace-no-wrap">
                Candidate
            </th>

            <th class="font-normal p-2 w-48 whitespace-no-wrap">
                Address
            </th>

            <th class="font-normal p-2 whitespace-no-wrap">
                Office Type
            </th>

            <th class="font-normal p-2 whitespace-no-wrap">
                District
            </th>

            <th class="font-normal p-2 text-blue-dark whitespace-no-wrap">
                Letter?
            </th>

            <th class="font-normal p-2 text-blue-dark whitespace-no-wrap">
                FB Begin?
            </th>

            <th class="font-normal p-2 text-blue-dark whitespace-no-wrap">
                FB End?
            </th>

            <th class="font-normal p-2 text-blue-dark whitespace-no-wrap">
                Called?
            </th>
        </tr>

        <tbody id="accountTable">
            @foreach($prospects as $prospect)
                <tr class="border-b">

                    <td class="p-2 pb-4 align-top whitespace-no-wrap">
                        {{ $prospect->source }}

                         <select>
                            <option>Added Manually</option>
                            <option>Other</option>
                        </select>

                    </td>

                    <td class="p-2 pb-4 align-top whitespace-no-wrap">
                        <select>
                            <option>Running</option>
                            <option>Won / In Office</option>
                            <option>Lost</option>
                            <option>Out of Office</option>
                        </select>
                    </td>

                    <td class="p-2 pb-4 align-top whitespace-no-wrap">
                        {{ $prospect->candidate_name }}
                    </td>

                    <td class="p-2 pb-4 align-top whitespace-no-wrap">
                        {{ $prospect->candidate_address }}
                    </td>

                    <td class="p-2 pb-4 align-top whitespace-no-wrap">
                        {{ $prospect->office_type }}
                    </td>

                    <td class="p-2 pb-4 align-top whitespace-no-wrap">
                        {{ $prospect->district }}
                    </td>

                    <td class="p-2 pb-4 align-top whitespace-no-wrap">
                        @if($prospect->letter_sent_on)
                            {{ \Carbon\Carbon::parse($prospect->letter_sent_on)->toDateString() }}
                        @else
                            None
                        @endif
                    </td>

                    <td class="p-2 pb-4 align-top whitespace-no-wrap">
                        @if($prospect->fb_started_on)
                            {{ \Carbon\Carbon::parse($prospect->fb_started_on)->toDateString() }}
                        @else
                            None
                        @endif
                    </td>

                    <td class="p-2 pb-4 align-top whitespace-no-wrap">
                        @if($prospect->fb_stopped_on)
                            {{ \Carbon\Carbon::parse($prospect->fb_stopped_on)->toDateString() }}
                        @else
                            None
                        @endif
                    </td>

                    <td class="p-2 pb-4 align-top whitespace-no-wrap">
                        @if($prospect->phone_call_on)
                            {{ \Carbon\Carbon::parse($prospect->phone_call_on)->toDateString() }}
                        @else
                            None
                        @endif
                    </td>

                </tr>
            @endforeach
        </tbody>

    </table> -->

<div class="w-full mb-4 pb-4">

    <div class="border-b-2 border-blue text-base flex py-2">

        <div class="p-1">
            www.ocpf.us/Filers/RecentlyOrganized
        </div>

        <div class="ml-2 mt-1">
            <a href="https://www.ocpf.us/Filers/RecentlyOrganized" target="new">
                <button class="rounded-lg bg-blue text-white px-2 py-1 text-xs">
                    Go to Webpage
                </button>
            </a>
        </div>

    </div>

    <div class="text-xl mb-4 border-b-4 border-red py-2 flex">

        <div class="w-1/2">
        </div>

        <div class="w-1/2">

            <form action="/admin/prospects" method="post">
                @csrf
                <div class="text-blue pb-2 text-sm italic text-right">
                    <b>Paste in text</b> from page such as:
                    <div>
                        www.ocpf.us/Filers/?q=17459
                    </div>
                </div>

                <div class="text-right">
                    <textarea rows="10" name="dump" id="dump" class="rounded-lg p-2 border w-full text-xs"></textarea>

                    <input type="submit" value="Add" name="submit" class="rounded border text-sm px-4 py-2 bg-grey-lighter uppercase" />
                </div>

            </form>

        </div>



    </div>  

    <div class="flex">


        <div class="w-full pr-1 table">

            <div class="border-b text-sm table-row bg-grey-lighter text-xs uppercase">

                <div class="p-1 table-cell border-b">
                    
                </div>

                <div class="border-r p-1 table-cell border-b">
                    Orgzd
                </div>

                <div class="border-r p-1 table-cell border-b">
                    Name
                </div>

                <div class="border-r p-1 table-cell border-b">
                    Type
                </div>

                <div class="border-r p-1 table-cell border-b">
                    City
                </div>

                <div class="border-r p-1 table-cell border-b">
                    Party
                </div>

                <div class="border-r p-1 table-cell border-b bg-orange-lightest text-center text-xs">
                    1.<br/>
                    Email<br />
                    <i class="fas fa-envelope"></i>
                </div>

                <div class="border-r p-1 table-cell border-b bg-orange-lightest text-center text-xs">
                    2.<br/>
                    Call<br />
                    <i class="fas fa-phone"></i>
                </div>

                <div class="border-r p-1 table-cell border-b bg-orange-lightest text-center text-xs">
                    3.<br/>
                    Demo<br />
                    <i class="fas fa-laptop"></i>
                </div>

                <div class="border-r p-1 table-cell border-b bg-orange-lightest text-center text-xs">
                    4.<br/>
                    Then<br />
                    <i class="fas fa-phone"></i>
                </div>

                <div class="border-r p-1 table-cell border-b bg-orange-lightest text-center text-xs">
                    5.<br/>
                    Get?<br />
                    <i class="fas fa-user"></i>
                </div>

            </div>

            @foreach($prospects as $prospect)
                <div class="border-b text-sm table-row">

                    <div class="p-1 table-cell border-b">

                        <span class="text-xs mr-2 text-grey-dark">{{ $loop->iteration }}.</span>

                        <button class="rounded-lg bg-blue text-white px-2 py-1 text-xs">
                            Edit
                        </button>
                    </div>

                    <div class="border-r p-1 table-cell border-b">
                        {{ \Carbon\Carbon::parse($prospect->ocpf_organized_on)->format("n/j/y") }}
                    </div>

                    <div class="border-r p-1 table-cell border-b">
                        <a href="https://www.ocpf.us/Filers/?q={{ $prospect->ocpf_id }}" target="new">
                            {{ $prospect->candidate_name }}


                        </a>

                            @if($prospect->voter_id)
                                <span class="text-xs text-blue float-right">
                                    ({{ $prospect->voter_id }})
                                </span>
                            @else
                               <span class="text-xs text-grey float-right">
                                    (no voter id)
                                </span>
                            @endif

                        <div class="text-xs text-grey-dark">
                            {{ $prospect->candidate_address }}
                        </div>
                    </div>

                    <div class="border-r p-1 table-cell border-b">
                        {{ $prospect->district_type }}
                    </div>

                    <div class="border-r p-1 table-cell border-b">
                        @if($prospect->city)
                            {{ $prospect->city->name }}
                            <div class="text-xs text-blue">
                                (id# {{ $prospect->city_code }})
                            </div>
                        @endif
                    </div>

                    <div class="border-r p-1 table-cell border-b">
                        {{ substr($prospect->party,0,1) }}
                    </div>

                    <div class="border-r p-1 table-cell border-b">
                        {{ ($prospect->letter_sent_on) ? 'Yes' : null }}
                    </div>

                    <div class="border-r p-1 table-cell border-b">
                        {{ ($prospect->phone_call_on) ? 'Yes' : null }}
                    </div>

                    <div class="border-r p-1 table-cell border-b">
                    </div>


                    <div class="border-r p-1 table-cell border-b">
                    </div>


                    <div class="border-r p-1 table-cell border-b">
                    </div>

                </div>
            @endforeach

        </div>



    </div>




</div>



@endsection

@section('javascript')

<script type="text/javascript">
    
    $(document).ready(function() {

        var placeholder = '{!! $placeholder !!}';

        $('#dump').attr('value', placeholder);

        $('#dump').focus(function(){
            if($(this).val() === placeholder){
                $(this).attr('value', '');
            }
        });

        $('#dump').blur(function(){
            if($(this).val() ===''){
                $(this).attr('value', placeholder);
            }    
        });

    });

</script>

@endsection

