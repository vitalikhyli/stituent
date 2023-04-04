@extends(Auth::user()->team->app_type.'.base')

@section('title')
   	Paste Voter IDs
@endsection

@section('breadcrumb')
    <a href="/campaign">HQ</a> > &nbsp;<b>Paste Voter IDs</b>
@endsection

@section('styles')
	@livewireStyles
@endsection

@section('main')


	<div class="text-3xl font-bold border-b-4 pb-2">
		Paste into User Uploads

		<span class="text-blue">*</span>
	</div>


	<div class="flex text-grey-dark w-full">
		<div class="w-full">
			<div class="pt-4">

				@livewire('user-upload-to-master.paste')

			</div>
		</div>
		
	</div>

	

@endsection


@section('javascript')

 	@livewireScripts

@endsection