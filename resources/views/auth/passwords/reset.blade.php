@extends('welcome.auth-base-office')

@section('title')
    New Password
@endsection



@section('auth')
 <div class=" w-3/5 mx-auto">

        <div class="font-bold text-xl">Choose a New Password</div>

        <div class="text-sm text-grey-light">
            Enter your new password twice to make sure you got it right.
        </div>

        <div class="mt-8">


                    <form method="POST" action="{{ route('password.update') }}">
                        @csrf

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="">
                            <label for="email" class="text-xs uppercase -mb-2">{{ __('E-Mail Address') }}</label>

                            <div class="">
                                <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ $email ?? old('email') }}" required>

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
                                <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" autofocus required>

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
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="mt-2">
                            <div class="w-5/6 text-center mx-auto">
                                <button class="w-full text-grey-lighter no-underline hover:text-maroon hover:bg-white hover:text-blue hover:border-white no-underline border-2  px-6 py-3 rounded-full text-sm tracking-wide" type="submit">
                                    Reset Password and Login
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
            </div>
        </div>
    </div>
</div>
@endsection
