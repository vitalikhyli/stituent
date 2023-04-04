@extends('stripe.base')

@section('title')
	Secure Stripe Payments
@endsection

@section('content')

	<div class="p-16 text-center text-2xl w-full">
		Payment Account <br>
		<b class="text-3xl">{{ $simple_payment_words }}</b> <br>
		Not Found

		<div class="italic text-gray-500 mt-8">
			Please call Community Fluency at <b class="text-blue-500">617.888.0545</b> <br>
			for assistance
		</div>
	</div>

@endsection



