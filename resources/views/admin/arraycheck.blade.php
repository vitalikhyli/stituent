@extends('admin.base')

@section('title')
    Admin Dashboard
@endsection

@section('breadcrumb')


@endsection

@section('style')

@endsection

@section('main')


<div class="w-full mb-4 pb-2 ">


    <div class="text-xl mb-4 border-b-4 border-red py-2">
        Array Checker
    </div>  

<form action="" method="get">
	<input type="text" name="person_id" placeholder="Person ID" class="border shadow p-2" value="{{ $person->id }}" />
	<input type="submit" value="Go" />
</form>

<div class="text-xs border-b-4">
	<div class="font-bold text-blue">Other_Emails</div>
	<div class="">{{ json_encode($person->other_emails) }}</div>
	<pre>{!! print_r($person->other_emails) !!}</pre>
</div>

<div class="text-xs border-b-4">
	<div class="font-bold text-blue">Other_Phones</div>
	<div class="">{{ json_encode($person->other_phones) }}</div>
	<pre>{!! print_r($person->other_phones) !!}</pre>
</div>

@endsection

@section('javascript')



@endsection