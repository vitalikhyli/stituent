@extends(Auth::user()->team->app_type.'.base')

@section('title')
    Edit Business Info
@endsection

@section('breadcrumb')

	<a href="/{{ Auth::user()->team->app_type }}">Home</a> > 
	<a href="/{{ Auth::user()->team->app_type }}/constituents">@lang('Constituents')</a> >
	<a href="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}">{{ $person->name }}</a>
    > &nbsp;<b>Edit</b>

@endsection

@section('style')

	<style>


	</style>

@endsection

@section('main')

@include('elements.errors')

<form method="POST" id="contact_form" action="/{{ Auth::user()->team->app_type }}/constituents/{{ $person->id }}/business/update">
	@csrf

	<div class="text-2xl font-sans border-b-4 border-blue pb-3">

		<span class="text-2xl">
			<i class="fas fa-user-circle mr-2"></i> Edit Work Info
		</span>

	</div>

	<input type="hidden" name="previous_url" value="{{ base64_encode(url()->previous()) }}" />


   <!-- <div class="border-blue border-b-2 py-1 mt-2 text-blue text-xl">Basic</div> -->

    <table class="w-full">

        <tr class="border-t">
            <td class="p-2 bg-grey-lighter w-16 whitespace-no-wrap">
                Occupation
            </td>
            <td class="p-2">
                <input type="text" name="business_occupation" class="rounded-lg border-2 p-2 w-2/3" value="{{ $business->occupation }}" />
            </td>
        </tr>

    </table>


    <div class="flex">

        <div class="w-1/2 pr-2">
            <div class="border-blue border-b-2 py-1 mt-2 text-blue text-xl">Employer</div>

            <table class="w-full">
                <tr class="border-t">
                    <td class="p-2 bg-grey-lighter w-16 whitespace-no-wrap">
                        Name
                    </td>
                    <td class="p-2">
                        <input type="text" name="business_name" class="rounded-lg border-2 p-2 w-full" value="{{ $business->name }}" />
                    </td>
                </tr>

                <tr class="border-t">
                    <td class="p-2 bg-grey-lighter w-16 whitespace-no-wrap">
                        Address 1:
                    </td>
                    <td class="p-2">
                        <input type="text" name="business_address_1" class="rounded-lg border-2 p-2 w-full" value="{{ $business->address_1 }}" />
                    </td>
                </tr>

                <tr class="border-t">
                    <td class="p-2 bg-grey-lighter w-16 whitespace-no-wrap">
                        Address 2:
                    </td>
                    <td class="p-2">
                        <input type="text" name="business_address_2" class="rounded-lg border-2 p-2 w-full" value="{{ $business->address_2 }}" />
                    </td>
                </tr>

                <tr class="border-t">
                    <td class="p-2 bg-grey-lighter w-16 whitespace-no-wrap">
                        City
                    </td>
                    <td class="p-2">
                        <input type="text" name="business_city" class="rounded-lg border-2 p-2 w-full" value="{{ $business->city }}" />
                    </td>
                </tr>

                <tr class="border-t">
                    <td class="p-2 bg-grey-lighter w-16 whitespace-no-wrap">
                        State
                    </td>
                    <td class="p-2">
                        <input type="text" placeholder="MA" name="business_state" class="rounded-lg border-2 p-2 w-16" value="{{ $business->state }}" />
                    </td>
                </tr>

                <tr class="border-t">
                    <td class="p-2 bg-grey-lighter w-16 whitespace-no-wrap">
                       Zip:
                    </td>
                    <td class="p-2">
                        <input type="text" size="5" name="business_zip" class="rounded-lg border-2 p-2" value="{{ $business->zip }}" /> - 
                        <input type="text" size="4" name="business_zip4" class="rounded-lg border-2 p-2" value="{{ $business->zip4 }}" />
                    </td>
                </tr>
            </table>
    </div>

    <div class="w-1/2 pl-2">
        <div class="border-blue border-b-2 py-1 mt-2 text-blue text-xl">Contact Info</div>

        <table class="w-full">
            <tr class="border-t">
                <td class="p-2 bg-grey-lighter w-16 whitespace-no-wrap">
                    Work Phone
                </td>
                <td class="p-2">
                    <input type="text" name="business_work_phone" class="rounded-lg border-2 p-2" value="{{ $business->work_phone }}" />
                    ext: 
                    <input type="text" name="business_work_phone_ext" class="rounded-lg border-2 p-2 w-16" value="{{ $business->work_phone_ext }}" />
                </td>
            </tr>

            <tr class="border-t">
                <td class="p-2 bg-grey-lighter w-16 whitespace-no-wrap">
                    Fax
                </td>
                <td class="p-2">
                    <input type="text" name="business_fax" class="rounded-lg border-2 p-2" value="{{ $business->fax }}" />
                </td>
            </tr>

            <tr class="border-t">
                <td class="p-2 bg-grey-lighter w-16 whitespace-no-wrap">
                    Web
                </td>
                <td class="p-2">
                    <input type="text" name="business_web" class="rounded-lg border-2 p-2" value="{{ $business->web }}" />
                </td>
            </tr>

        </table>
    </div>
</div>

	<div class="text-2xl font-sans pb-3">

        <input type="submit" name="save" value="Save" class="mr-2 rounded-lg bg-blue hover:bg-orange-dark text-white float-right text-base px-8 py-2 mt-1 shadow ml-2" />

		<input type="submit" name="save_and_close" value="Save & Close" class="rounded-lg bg-blue-darker hover:bg-oranger-dark text-white float-right text-base px-8 py-2 mt-1 shadow" />

	</div>


</form>

<br /><br />


@endsection

@section('javascript')

@endsection