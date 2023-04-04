@extends('blank')

@section('title')
    
@endsection

@section('style')

<link href="https://fonts.googleapis.com/css?family=Dancing+Script&display=swap" rel="stylesheet">

<style>

	.fancy {
		font-family: 'Dancing Script', cursive;
	}
	#statehouse {
		background-size: 250%;
		background-position: bottom;
		background-repeat: no-repeat;
		background-image: url(/images/statehouse.svg);
		height: 450px;
	}
	@media (min-width: 500px) { 

		#statehouse {
			background-size: 210%;
			height: 500px;
		}
	}
	@media (min-width: 768px) { 

		#statehouse {
			background-size: 180%;
			height: 600px;
		}
		#login-left {
			width: 55%;
		}
		#login-right {
			width: 45%;
		}
	}
	@media (min-width: 1280px) { 

		#statehouse {
			background-size: 120%;
			height: 750px;
		}
	}
	@media (min-width: 1440px) { 

		#statehouse {
			background-size: 120%;
			height: 900px;
		}
	}

</style>
	
@endsection

@section('main')


<div class="bg-black h-screen text-center">
        
    @if (Auth::user())
        @include('welcome.user-logged-in')
    @else

		<div id="login-right" class="mt-24 inline-block shadow bg-grey-lighter p-8 rounded-lg md:w-5/12" style="">

	        <div id="login" class="text-center">
	       

	            <form method="POST" action="{{ route('login') }}" class="">
	                @csrf

	                <div class="font-bold text-blue uppercase">Campaign Fluency Login</div><br>

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


	            </form>
	        </div>
	    </div>

	@endif


</div>



@endsection

@section('javascript')

	<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v1.9.2/dist/alpine.js" defer></script>

	<script type="text/javascript">
		
		document.getElementById("email").focus();
		document.getElementById("name").focus();

	</script>

@endsection


