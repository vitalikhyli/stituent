@extends(Auth::user()->team->app_type.'.base')

@section('title')
    Edit Organization
@endsection

@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	<a href="/{{ Auth::user()->team->app_type }}/organizations">Organizations</a> 

    > <b>New</b>

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/organizations/save">
	@csrf

	<div class="text-2xl font-sans border-b-4 border-blue pb-3">

		<div class="float-right text-base">

			<input type="submit" name="save" value="Save" class="rounded-lg px-4 py-2 border bg-blue text-white text-center"/>
			
			<a href="{{ url()->previous() }}">
				<button type="button" name="update" class="rounded-lg px-4 py-2 border bg-grey-darkest text-white text-center ml-2"/>
					Cancel
				</button>
			</a>

		</div>


		<span class="text-2xl">
		<i class="fas fa-building mr-2"></i>
		New Organization
		</span>
	</div>

	<input type="hidden" name="previous_url" value="{{ base64_encode(url()->previous()) }}" />

	<table class="text-base w-full border-t">
		<tr class="border-b">
			<td class="p-2 bg-grey-lighter text-right align-top w-1/6">
				Name
			</td>
			<td class="p-2">

				<input name="name" placeholder="Name" value="{{ ucfirst($name) }}" class="border-2 rounded-lg px-4 py-2 w-1/3"/>

			</td>
		</tr>
	
	</table>

</form>

<br /><br />


@endsection

@section('javascript')

@endsection