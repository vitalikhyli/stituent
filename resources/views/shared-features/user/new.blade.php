@extends(Auth::user()->team->app_type.'.base')

@section('title')
    New User
@endsection

@section('breadcrumb')

    {!! Auth::user()->Breadcrumb('Edit', 'edit', 'level_1') !!}

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')



	<form method="POST" action="/{{ Auth::user()->team->app_type }}/users/save">

		{{ csrf_field() }}

	<div class="flex border-b mb-4 pb-2">
		<div class="w-full">

			<div class="hidden float-right">
				<input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 border bg-blue text-white text-center"/>

				<a href="/{{ Auth::user()->team->app_type }}/team">
					<button type="button" name="update" class="rounded-lg px-4 py-2 border bg-grey-dark text-white text-center ml-2"/>
						Cancel
					</button>
				</a>
			</div>

			<div class="text-2xl font-sans">
				<i class="fas fa-user mr-4"></i>Create New User
			</div>
		</div>
	</div>

	@include('elements.errors')



		<div class="border-b-4 border-blue text-lg pb-2">
			Basics
		</div>

			<table class="w-full">
				<tr class="border-b text-left p-2">
					<td class="w-1/5 border-b px-2 py-2 text-right">
						Name
					</td>
					<td class="border-b text-left p-2">
						<input type="text" name="name" class="border rounded-lg p-2 w-3/4 font-semibold bg-grey-lightest" value="{{ $errors->any() ? old('name') : '' }}" />
					</td>
				</tr>
				<tr class="border-b text-left p-2">
					<td class="w-1/5 border-b px-2 py-2 text-right">
						Email
					</td>
					<td class="border-b text-left p-2">
						<input type="text" name="email" class="border rounded-lg p-2 w-3/4 font-semibold bg-grey-lightest" value="{{ $errors->any() ? old('email') : '' }}" />
					</td>
				</tr>
			</table>
		

			<div class="p-4 text-center w-full">
				<input type="submit" name="update" value="Save" class="rounded-lg px-4 py-2 border bg-blue text-white text-center"/>

				<a href="/{{ Auth::user()->team->app_type }}/team">
					
					<button type="button" name="update" class="rounded-lg px-4 py-2 border bg-grey-dark text-white text-center ml-2"/>
						Cancel
					</button>
				</a>
			</div>

		</form>

<br />
<br />
@endsection

@section('javascript')

@endsection
