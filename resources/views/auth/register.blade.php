@extends('welcome.auth-base')

@section('title')
    Create a New Account
@endsection

@section('auth')

     <div class="w-3/5 mx-auto" style="margin-top: -80px;">

        <div class="text-2xl mt-8 mb-6 pl-8 relative">
                        <img src="/images/cf_logo_white.svg" class="w-12 absolute pin-l pin-t -mt-2 -ml-1" />
                        <span class="ml-2 font-thin tracking-wide">community</span><span class="">fluency</span>
                    </div>

        <div class="font-bold text-xl">Create a Trial Account</div>

        <div class="text-sm text-grey">
            You can get started today with a free demo. All fields required.
        </div>


        <form method="POST" action="{{ route('register') }}" class="mt-8">
            @csrf

            <div class="">
                <label for="team_name" class="text-xs uppercase -mb-2">{{ __('Team Name') }}</label>

                <div class="">
                    <input id="team_name" 
                           type="text" 
                           placeholder="Rep. Jane Doe"
                           autocomplete="off" class="border-2 w-full text-grey-light px-3 py-2 text-lg bg-transparent text-black {{ $errors->has('team_name') ? ' is-invalid' : '' }}" name="team_name" value="{{ old('team_name') }}" required autofocus>

                    @if ($errors->has('team_name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('team_name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="">
                <label for="name" class="text-xs uppercase -mb-2">{{ __('Your Name') }}</label>

                <div class="">
                    <input id="name" type="text" autocomplete="off" class="border-2 w-full text-grey-light px-3 py-2 text-lg bg-transparent text-black {{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') }}" required>

                    @if ($errors->has('name'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('name') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="">
                <label for="email" class="text-xs uppercase -mb-2">{{ __('E-Mail Address') }}</label>

                <div class="">
                    <input id="email" type="email" autocomplete="off" class="border-2 w-full text-grey-light px-3 py-2 text-lg bg-transparent text-black {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autocomplete="off">

                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="">
                <label for="password" class="text-xs uppercase -mb-2">{{ __('Password') }}</label>

                <div class="">
                    <input id="password" type="password" autocomplete="off" class="border-2 w-full text-grey-light px-3 py-2 text-lg bg-transparent text-black {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>

                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
            </div>

            <div class="">
                <label for="password-confirm" class="text-xs uppercase -mb-2">{{ __('Confirm Password') }}</label>

                <div class="">
                    <input id="password-confirm" type="password" autocomplete="off" class="border-2 w-full text-grey-light px-3 py-2 text-lg bg-transparent text-black " name="password_confirmation" required>
                </div>
            </div>

            <div class="flex mt-2">
                <div class="w-full text-center">
                    <button class="w-5/6 text-grey-lighter no-underline hover:text-maroon hover:bg-white hover:text-blue hover:border-white no-underline border-2  px-6 py-3 rounded-full text-sm tracking-wide" type="submit">
                        Start Free Trial
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
