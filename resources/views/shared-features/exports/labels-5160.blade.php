@extends('office.base')
<?php if (!defined('dir')) define('dir','/office'); ?>

@section('title')
    Open Cases
@endsection

@section('breadcrumb')

    Labels

@endsection

@section('style')

	<style>
/*		body {
        width: 8.5in;
        margin: 0in .1875in;
        }*/

    .label{
        text-align: center;
        overflow: hidden;
        width: 2.625in;
        height: 1in;
        padding: .125in .3in 0;
        color:black;
        float: left;
        font-weight:normal;
        font-size:11pt;
        border-radius: 15px;
        border:1px solid #F0F0F0;
        margin-right: .125in;
		text-transform: uppercase;
    }

    .page-break  {
        clear: left;
        display:block;
        page-break-after:always;
     }

	</style>

@endsection

@section('main')

	<div class="flex border-b-4 border-blue mb-4 pb-2">
		<div class="w-1/2">
			<div class="text-2xl font-sans">
				Avery Labels 5160
			</div>
		</div>
	</div>

<form method="post" action="/office/labels">
<div class="text-left mb-2 border-b pb-4 w-full flex">


	<button type="submit" formaction="/office/labels/pdf" class="rounded-lg bg-blue text-white px-2 py-1 text-sm ml-4 float-right">
		<i class="fas fa-file-pdf"></i>	Generate PDF
	</button>	

</div>
</form>

@if(isset($output) &&($output->count() >0))
	
	<div class="page-break"></div>

	@foreach($output as $thevoter)
		<div class="label">
		{{ $thevoter->full_name}}<br />
		{{ $thevoter->address_number}} {{ $thevoter->address_street}} {{ $thevoter->address_aptno}}<br />
		{{ $thevoter->address_city}}, {{ $thevoter->address_state }} {{$thevoter->address_zip}}
		</div>
	@endforeach
	<div class="page-break"></div>
@endif

<br />
<br />
@endsection

@section('javascript')


@endsection
