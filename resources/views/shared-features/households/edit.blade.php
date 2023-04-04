@extends(Auth::user()->team->app_type.'.base')

@section('title')
    	{{ $hh->full_address }}
@endsection

@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	<!-- <a href="/{{ Auth::user()->team->app_type }}/households"> -->
		Households
	<!-- </a>  -->

    > &nbsp;<b>{{ mb_strimwidth($hh->full_address, 0, 30, "...") }}</b>

@endsection

@section('style')


@endsection

@section('main')

@include('elements.errors')


	<div class="text-2xl font-sans border-b-4 border-blue pb-2">

		<i class="fas fa-home ml-1 mr-2"></i>
		<span class="mr-2">
			<b>{{ $hh->addressNoCity }}
			</b>
			<span class="text-grey-dark ml-1">| {{ $hh->address_city }},
			{{ $hh->address_state }} {{ $hh->address_zip }}</span>
		</span>

	</div>

			<form action="/{{ Auth::user()->team->app_type }}/households/{{ $hh->id }}/update"
				  method="post">

				@csrf

				<div class="py-2 flex">

					<div class="font-bold pt-2 p-2 pr-4 text-sm text-blue">
						Line 1
					</div>

					<div>

						<div class="flex">
							<div class="p-2 w-24">
								Number
							</div>
							<div>
								<input type="text"
									   class="text-blue border-b border-grey-light p-2"
									   value="{{ $hh->address_number }}"
									   name="address_number" />
							</div>
						</div>

						<div class="flex">
							<div class="p-2 w-24">
								Fraction
							</div>
							<div>
								<input type="text"
									   class="text-blue border-b border-grey-light p-2"
									   value="{{ $hh->address_fraction }}"
									   name="address_fraction" />
							</div>
						</div>

						<div class="flex">
							<div class="p-2 w-24">
								Street
							</div>
							<div>
								<input type="text"
									   class="text-blue border-b border-grey-light p-2"
									   value="{{ $hh->address_street }}"
									   name="address_street" />
							</div>
						</div>

						<div class="flex">
							<div class="p-2 w-24">
								Apt
							</div>
							<div>
								<input type="text"
									   class="text-blue border-b border-grey-light p-2"
									   value="{{ $hh->address_apt }}"
									   name="address_apt" />
							</div>
						</div>

					</div>

				</div>

				<div class="py-2 flex">

					<div class="font-bold pt-4 p-2 pr-4 text-sm text-blue">
						Line 2
					</div>

					<div>

						<div class="flex mt-2">
							<div class="p-2 w-24">
								City
							</div>
							<div>
								<input type="text"
									   class="text-blue border-b border-grey-light p-2"
									   value="{{ $hh->address_city }}"
									   name="address_city" />
							</div>
						</div>

						<div class="flex">
							<div class="p-2 w-24">
								State
							</div>
							<div>
								<input type="text"
									   class="text-blue border-b border-grey-light p-2"
									   value="{{ $hh->address_state }}"
									   name="address_state" />
							</div>
						</div>

						<div class="flex">
							<div class="bg-gren-lightest p-2 w-24">
								Zip
							</div>
							<div>
								<input type="text"
									   class="text-blue border-b border-grey-light p-2"
									   value="{{ $hh->address_zip }}"
									   name="address_zip" />
							</div>
						</div>

					</div>

				</div>



				<div class="text-center mt-6">

					<button type="submit"
							class="rounded-lg bg-blue text-white px-6 py-2 text-lg shadow border border-grey-darker hover:bg-blue-darker"
							name="save">
						Save
					</button>

					<button type="submit"
							class="rounded-lg bg-blue-darker text-white px-6 py-2 text-lg shadow border border-grey-darker hover:bg-blue-darker"
							formaction="/{{ Auth::user()->team->app_type }}/households/{{ $hh->id }}/update/close">
						Save and Close
					</button>

				</div>

			</form>

	
<br />
<br />
@endsection

@section('javascript')

@endsection