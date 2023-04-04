@extends('welcome.auth-base-campaign')

@section('title')
    Login
@endsection



@section('auth')


    <div id="login-left" class="p-8 md:w-7/12" style="">

        <div class="flex">
            <i class="fa fa-check text-green text-lg pr-2 text-xl"></i>
            <div>
                <div class="text-black font-bold">
                    The fastest engine
                </div>
                <div class="text-sm text-grey-dark">
                    Fine-tuned for speed. You and your team can work in real-time across the state.
                </div>
            </div>
        </div>
        <div class="flex mt-4">
            <i class="fa fa-check text-green text-lg pr-2 text-xl"></i>
            <div>
                <div class="text-black font-bold">
                    You own your data
                </div>
                <div class="text-sm text-grey-dark">
                    Community Fluency is <b>non-partisan</b> so your contacts, cases, and donor lists are your own.
                </div>
            </div>
        </div>
        <div class="flex mt-4">
            <i class="fa fa-check text-green text-lg pr-2 text-xl"></i>
            <div>
                <div class="text-black font-bold">
                    Fully mobile
                </div>
                <div class="text-sm text-grey-dark">
                    Quick access to every feature is available on your smartphone.
                </div>
            </div>
        </div>

        <div class="text-center border-t-2 mt-4 pt-4">
            <span class="uppercase text-blue text-sm font-bold">Get Started! Call </span><b>617.699.4553</b>
        </div>

    </div>

    

    <div id="login-right" class="bg-grey-lighter p-8 rounded-r-lg md:w-5/12" style="">



        <div id="login" class="text-center">
       

            <form method="POST" action="{{ route('login') }}" class="">
                @csrf



                <div class="font-bold text-blue uppercase">{{ __('Log In') }}</div><br>

                <div class="">
                    <!-- type="email"  -->
                    <input id="email" class="border-2 w-full px-3 py-2 text-lg text-black" name="email"  placeholder="Email (or Username)" value="{{ old('email') }}" required autofocus>

                    @if ($errors->has('email'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                </div>


                

                <div class="mt-2">
                    <input id="password" type="password" class="border-2 w-full px-3 py-2 text-lg text-black" name="password" placeholder="Password" required>

                    @if ($errors->has('password'))
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>

                <div class="flex mt-4">
                    <div class="w-full text-center">
                        <button class="w-5/6 bg-blue text-grey-lightest no-underline hover:bg-blue-dark hover:text-white hover:border-white no-underline px-3 py-3 w-full text-sm tracking-wide uppercase" type="submit">
                            Sign In
                        </button>
                    </div>
                </div>

                
                <div class="text-center w-full text-sm mt-4">
                    <label for="keep-me-logged-in">
                        <input type="checkbox"
                               id="keep-me-logged-in"
                               name="keep-me-logged-in"
                               value="1"
                               checked
                               />
                        Keep Me Logged In
                    </label>
                </div>

                <div class="text-center w-full text-sm mt-8">
                    <a class="text-center hover:text-blue-dark text-grey-dark" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                </div>

                <div class="text-center w-full text-sm mt-8">
                    <a class="text-center hover:text-blue-dark text-grey-dark" href="/request-demo">
                        {{ __('Request Demo') }}
                    </a>
                </div>
            </form>
        </div>
    </div>


@endsection



