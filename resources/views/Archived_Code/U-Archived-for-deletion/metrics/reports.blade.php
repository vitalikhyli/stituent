@extends('u.base')

@section('title')
    Reports
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('Reports', 'reports_index', 'level_1') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

	<div class="flex border-b mb-4 pb-2">
		<div class="w-1/2">
			<div class="text-2xl font-sans">
				Generate Reports
			</div>
		</div>
	</div>



<br />
<br />
@endsection

@section('javascript')


@endsection
