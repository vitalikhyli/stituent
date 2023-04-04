@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')


@endsection

@section('main')


<div class="w-full mb-4 pb-2 ">

    <div class="text-xl mb-4 border-b-4 border-red py-2">
        Users for {{ $theteam->name }}
    </div>  


    <table id="accountTable" class="w-full">
    @foreach($teams as $theteam)
    <tr class="border-b">


        <td class="p-1 pb-4 align-top w-3/4 whitespace-no-wrap">
            @foreach($theteam->users as $theuser)
                <i class="fas fa-user-circle mr-4"></i>
                    {{ $theuser->name }}
                    <br />
            @endforeach
        </td>
    </tr>
    @endforeach
    </table>

</div>


</div>



@endsection

@section('javascript')

@endsection