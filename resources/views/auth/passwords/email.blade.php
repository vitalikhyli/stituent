@extends('welcome.auth-base-office')

@section('title')
    Reset Password
@endsection


@section('auth')

 <div class="p-8">

        <div class="font-bold text-xl">{{ __('Forgot Your Password?') }}</div>

        <div class="text-sm text-grey-dark">
            Enter your email address below and we will send you a secure link to create a new password.
        </div>

        <div class="mt-8">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="">
                    <label for="email" class="uppercase text-sm">Email Address</label>

                    <div class="">
                        <input id="email" type="email" class="border-2 w-full px-3 py-2 text-lg text-black {{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required placeholder="Email">

                        @if ($errors->has('email'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('email') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mt-2">
                    <div class="text-center mx-auto">
                        <button class="w-full text-grey-lightest no-underline hover:text-white hover:bg-blue-dark bg-blue hover:border-white no-underline px-6 py-3 text-sm tracking-wide" type="submit">
                            Send Reset Link
                        </button>
                    </div>
                </div>
            </form>

            <div class="text-center w-full text-sm mt-8">


                    <a class="text-center hover:text-blue text-grey-dark" href="/">
                        Back to Login
                    </a>


            </div>

        </div>
    </div>


@endsection
