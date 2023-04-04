@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')

@endsection

@section('main')


<div class="w-full mb-4 pb-2 ">

    <div class="text-xl mb-4 border-b-4 border-red py-2">
        New Account
    </div>  


<form method="POST" id="contact_form" action="/admin/accounts/save">
    
    @csrf




<table id="accountTable" class="w-full border-t">

    <tr class="border-b">
    
        <td class="p-2 bg-grey-lighter text-right align-middle w-1/5">
            New Account Name:
        </td>
        <td class="p-2">
            <input name="name" autocomplete="off" class="border-2 rounded-lg px-4 py-2 w-1/2"/>
        </td>

    </tr>

</table>

<div class="mt-4 flex float-right">
    <input type="submit" name="save" value="Save" class="flex-1 flex-initial mr-2 rounded-lg bg-blue text-white text-sm px-8 py-2 mt-1 shadow ml-2" />

    <a href="/admin/accounts">
        <button type="button" class="rounded-lg bg-grey-dark text-white px-2 py-2">Cancel</button>
    </a>
</div>


</form>

</div>


</div>



@endsection