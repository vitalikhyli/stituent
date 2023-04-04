@extends(Auth::user()->team->app_type.'.base')

@section('title')
    IMAP Access
@endsection

@section('breadcrumb')

    <a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	&nbsp;<b>IMAP Access</b> 

@endsection

@section('style')

	@livewireStyles

@endsection

@section('main')

	<div class="flex border-b-4 pb-2 border-blue mb-2">

	  <div class="text-2xl font-sans w-full flex">
	    <div class="font-bold flex-grow">{{ env('LAZ_EMAIL_USERNAME') }}</div>
	    <div class="font-grey-dark text-right">since {{ \Carbon\Carbon::today()->subMonth(1)->format('n M Y') }}</div>
	  </div>

	</div>

  

	@foreach($messages as $message)

		<div class="table-row">

			<div class="table-cell border-b py-1 font-semibold">
				{{ $message['headers']->fromaddress }}
			</div>

			<div class="table-cell border-b py-1 text-grey-dark">
				{{ $message['headers']->Subject }}
			</div>

			<div class="table-cell border-b py-1">
				{{ $message['date']->format('n/d/y') }}
			</div>

		</div>

	@endforeach

@endsection

@section('javascript')

	@livewireScripts

@endsection
