@extends(Auth::user()->team->app_type.'.base')

@section('title')
    New Email
@endsection

@section('breadcrumb')

    <a href="/office">Home</a> > <a href="/office/emails">Bulk Emails</a> > New Email

@endsection

@section('style')

<link href="https://cdn.jsdelivr.net/npm/froala-editor@3.0.4/css/froala_editor.pkgd.min.css" rel="stylesheet" type="text/css" />

	<style>
	
		.group-checkbox input:checked ~ .position {
			display: block;
		}
		.group-checkbox .position {
			display: none;
		}

	</style>

@endsection

@section('main')



<form id="bulk-email-form" method="POST" action="/{{ Auth::user()->team->app_type }}/emails/save">
		{{ csrf_field() }}

	<div class="flex border-b-4 border-blue pb-2">
		<div class="w-full">

			<span class="text-2xl">Start New Email</span>
		</div>
	</div>

	<div class="text-grey-darkest">



			<div class="w-1/2 p-2 font-bold align-top">
				Step 1. Give it a name:
			</div>
			<div class="w-1/2 px-2 text-base align-top">

				<input type="text" name="name" class="border rounded-lg p-2 w-full font-semibold bg-grey-lightest text-black" value="" />

			</div>

					

			<div class="float-right">
				<input type="submit" name="update" value="Save and Continue" class="rounded-lg px-4 py-2 border bg-blue text-white text-center"/>

				<button type="button" class="rounded-lg px-4 py-2 border bg-black text-white text-center ml-2">
					Cancel
				</button>
			</div>

	</div>

</form>



<br />
<br />

@endsection

@section('javascript')

@endsection
