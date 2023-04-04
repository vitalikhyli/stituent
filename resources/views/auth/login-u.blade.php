@extends('welcome.auth-base-u')

@section('title')
    Login
@endsection



@section('auth')

    <div id="login-left" class="p-8 md:w-7/12" style="">

        <div class="flex">
            <i class="fa fa-check text-green text-lg pr-2 text-xl"></i>
            <div>
                <div class="text-black font-bold">
                    Remote Work Ready
                </div>
                <div class="text-sm text-grey-dark">
                    Your whole team can collaborate online, with easy access to cases and files.
                </div>
            </div>
        </div>

        <div class="flex mt-4">
            <i class="fa fa-check text-green text-lg pr-2 text-xl"></i>
            <div>
                <div class="text-black font-bold">
                    Track Inquiries and Problems
                </div>
                <div class="text-sm text-grey-dark">
                    Respond to casework faster, more efficiently and with less stress.
                </div>
            </div>
        </div>

        <div class="flex mt-4">
            <i class="fa fa-check text-green text-lg pr-2 text-xl"></i>
            <div>
                <div class="text-black font-bold">
                    PILOTs and Communtiy Benefits
                </div>
                <div class="text-sm text-grey-dark">
                    Generate reports about the value your insitution provides to the community.
                </div>
            </div>
        </div>

        <div class="flex mt-4">
            <i class="fa fa-check text-green text-lg pr-2 text-xl"></i>
            <div>
                <div class="text-black font-bold">
                    Personal Support
                </div>
                <div class="text-sm text-grey-dark">
                    We're a small, dedicated company of experts who are always available to you.
                </div>
            </div>
        </div>

        <div class="text-center border-t-2 mt-6 pt-4">

            <div class="uppercase text-blue text-sm font-bold">
                Get Started Today
            </div>

            <div class="mt-2">
                Call us at 617.699.4553 or <a class="text-blue-dark font-medium" href="/request-demo">
                    Request a Demo
                </a>
            </div>

            <div class="mt-2">
                
            </div>

        </div>

        <div class="text-center border-t-2 mt-4 pt-4">

            <div class="mt-2">

                <div class="uppercase text-blue text-sm font-bold">
                    Try it Out
                </div>

                <button data-target="#human-modal" data-toggle="modal" class="mt-2 rounded-lg bg-grey-dark hover:bg-blue-dark text-white px-4 py-2 shadow">
                    Test Drive an Example Account
                </button>

                <div class="mt-2">
                    No registration or sign-up required
                </div>

            </div>

        </div>
<!-- 
        
        <div class="text-center border-t-2 mt-6 pt-4">

            <div class="uppercase text-blue text-sm font-bold">
                Learn More
            </div>

            <div class="mt-2">
                <a class="" href="/docs/u">
                    <button class="rounded-lg bg-grey-dark hover:bg-blue-dark text-white px-4 py-2 shadow">
                        Community Fluency's Open Docs
                    </button>
                </a>
            </div>

        </div>
         -->

    </div>

    

    <div id="login-right" class="bg-grey-lighter p-8 md:pt-16 rounded-r-lg md:w-5/12 flex items-top" style="">


        <div id="login" class="text-center w-full">
       

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

                <div class="text-center w-full text-sm mt-8">
                    <a class="text-center hover:text-blue-dark text-grey-dark" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                </div>

            </form>
        </div>
    </div>



@endsection



