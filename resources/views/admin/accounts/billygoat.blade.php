@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')


@endsection

@section('style')

<!--     <link href="//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"> 
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
 -->
@endsection

@section('main')


<div class="w-full mb-4 pb-2 ">

    <div class="text-xl mb-4 border-b-4 border-red py-2">
        BillyGoat
    </div>  

<!-- 
<div class="text-center">
<input id="accountInput" type="text" class="rounded-lg border p-4 text-lg w-1/2 mb-4 font-bold" placeholder="Type Here to Filter" />
</div> -->

<div class="font-medium text-lg mb-2">
    In Community Fluency
</div>

<table class="w-full text-sm">

    <tr class="text-xs cursor-pointer border-b-4 bg-grey-lighter">
        <th class="font-normal w-12 p-1">
            id
        </th>
        <th class="font-normal p-1 w-1/4">
            Linked?
        </th>
        <th class="font-normal p-1" >
            CF Account
        </th>
        <th class="font-normal p-1" >
            Edit
        </th>
        <th class="font-normal p-1 text-right w-24">
            Outstanding_Bal
        </th>

    </tr>

    <tbody id="accountTable">
    @foreach($accounts as $theaccount)
    <tr class="border-b">
        <td class="p-1 align-top whitespace-no-wrap">
            {{ $theaccount->id }}
        </td>
        <td class="p-1 align-top whitespace-no-wrap">
            @if(!$theaccount->billygoat_id)

                    <form method="post" action="/admin/billygoat/{{ $theaccount->id }}/link">
                        @csrf

                        <input type="text" name="billygoat_id" class="rounded border border-grey-dark px-2 py-1 w-24" placeholder="ID #" />
                        <button class="opacity-50 mr-1 bg-blue-dark hover:bg-blue-darker text-xs font-medium text-white rounded-lg px-2 py-2">
                        Manual BG link
                        </button>
                    </form>
               
            @else

                @if(config('app.env') == 'local')
                    <a target="_new" href="{{ config('app.billygoat_local') }}/client/{{ $theaccount->billygoat_id }}">
                @else
                    <a target="_new" href="{{ config('app.billygoat_url') }}/client/{{ $theaccount->billygoat_id }}">
                @endif
                    <button class="mr-1 text-xs font-medium text-blue-dark rounded-lg px-2 py-1">
                    <i class="fas fa-check w-4 mr-2"></i> Linked -- BG client ID # {{ $theaccount->billygoat_id }}
                    </button>
                 </a>

            @endif
        </td>
        <td class="p-1 align-top whitespace-no-wrap">
            <i class="fas fa-user-circle mr-4"></i>
            {{ $theaccount->name }}
        </td>

        <td class="p-1 align-top whitespace-no-wrap">
            <a href="/admin/accounts/{{ $theaccount->id }}/edit">Edit Account</a>
        </td>

        <td class="p-1 align-top whitespace-no-wrap text-right">
            <div class="mr-2 text-sm">
                {{ $theaccount->billyGoatOutstandingBal('formatted') }}
            </div>
        </td>
    </tr>
    @endforeach
    </tbody>

</table>



<div class="font-medium text-lg mb-2 mt-6">
    In BillyGoat but not Community Fluency
</div>

@if($bg_accounts == false)

    <div class="bg-red-lightest border-red border p-4">
        Error in reading API data
    </div>

@else

<table class="w-full text-sm">

    <tr class="text-xs cursor-pointer border-b-4 bg-grey-lighest">
        <th class="font-normal w-12">
            BG_ID
        </th>
        <th class="font-normal w-1/4">
            
        </th>
        <th class="font-normal p-1">
            BillyGoat Account
        </th>
        <th class="font-normal p-1 text-right">
            Outstanding_Bal
        </th>
    </tr>

    <tbody id="accountTable">
    @foreach($bg_accounts as $theaccount)
        <tr class="border-b">
            <td class="p-1 align-top whitespace-no-wrap">
                {{ $theaccount->id }}

            <td class="p-1 align-top whitespace-no-wrap">
            </td>
            <td class="p-1 align-top whitespace-no-wrap">
                @if(config('app.env') == 'local')
                    <a target="_new" href="{{ config('app.billygoat_local') }}/client/{{ $theaccount->id }}">
                @else
                    <a target="_new" href="{{ config('app.billygoat_url') }}/client/{{ $theaccount->id }}">
                @endif
                <i class="fas fa-dollar-sign mr-2 w-4"></i> {{ $theaccount->business_name }}
                </a>
            </td>
            <td class="p-1 align-top whitespace-no-wrap text-right">
                 {{ number_format(($theaccount->outstanding_balance/100),2,'.',',') }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

@endif

</div>


</div>



@endsection

@section('javascript')

<script>
$(document).ready(function(){
  $("#accountInput").on("keyup", function() {
    var value = $(this).val().toLowerCase();
    $("#accountTable tr").filter(function() {
      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
  });
});

// $(document).ready( function () {
//     $('#accountTable').DataTable();
// } );

</script>

@endsection