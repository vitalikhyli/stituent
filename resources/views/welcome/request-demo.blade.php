@extends('welcome.auth-base-office')

@section('title')
    Request a Demo
@endsection

@section('javascript')



@endsection

@section('auth')

     <div class="p-8">

        <div class="font-bold text-3xl text-center">Request a Demo</div>

        <div class="text-2xl text-grey-dark text-center mt-4">
            Fill out this form or call us at <span class="font-bold text-black">(617) 699-4553</span> to schedule a full demo for you and your team.
        </div>
    

        <form method="POST" action="/request-demo/submit" class="mt-8">
            @csrf

            <div class="hidden">
                <label for="first_name" class="text-base py-2 uppercase -mb-1 text-blue-dark">Your First Name*</label>

                <div class="">
                    <input id="first_name" type="text" autocomplete="off" class="border-2 w-full px-3 py-2 text-lg text-black {{ $errors->has('first_name') ? ' is-invalid' : '' }}" name="first_name" value="{{ old('first_name') }}">

                </div>
            </div>

            <div class="hidden">
                <label for="last_name" class="text-base py-2 uppercase -mb-1 text-blue-dark">Your Last Name*</label>

                <div class="">
                    <input id="last_name" type="text" autocomplete="off" class="border-2 w-full px-3 py-2 text-lg text-black {{ $errors->has('last_name') ? ' is-invalid' : '' }}" name="last_name" value="{{ old('last_name') }}">

                </div>
            </div>


            <div class="">
                <label for="name" class="text-base py-2 uppercase -mb-1 text-blue-dark">{{ __('Your Name') }}*</label>

                <div class="">
                    <input id="name" type="text" autocomplete="off" class="border-2 w-full px-3 py-2 text-lg text-black {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required>

                    @if ($errors->has('name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="">
                <label for="email" class="text-base py-2 uppercase -mb-1 text-blue-dark">{{ __('E-Mail Address') }}*</label>

                <div class="">
                    <input id="email" type="email" autocomplete="off" class="border-2 w-full px-3 py-2 text-lg text-black {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autocomplete="off">

                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="">
                <label for="notes" class="text-base py-2 uppercase -mb-1 text-blue-dark">Notes & Questions (i.e. Phone number)</label>

                <div class="">
                    <textarea name="notes" class="w-full border-2 p-4" rows="4"></textarea>

                </div>
            </div>

            <div class="flex mt-2">
                <div class="w-full text-center">
                    <button class="mt-4 shadow rounded-lg text-grey-lighter bg-blue no-underline hover:text-maroon hover:bg-blue-dark hover:text-white no-underline px-6 py-3 text-lg font-medium tracking-wide" type="submit">
                        Let's Schedule it!
                    </button>
                </div>
            </div>
        </form>

        <div class="text-center w-full text-sm mt-8">
            <a class="text-center hover:text-white text-grey" href="/">
                Back to Login
            </a>
        </div>
    </div>

@endsection
