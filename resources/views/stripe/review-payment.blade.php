@extends('stripe.base')

@section('title')
	Secure Stripe Payments
@endsection

@section('content')

	<div class="p-8">
		<div class="font-bold font-sans text-4xl border-b-4">
			Secure Payments
		</div>
		<div class="mt-4">
			Your payments are handled securely through a partnership between <a href="https://stripe.com">Stripe</a> and Community Fluency. Community Fluency does not have access to your payment methods.
			<br><br>
			
			<button onclick="checkout()" class="px-4 py-2 rounded-full bg-blue text-white">
				<i class="fa fa-stripe"></i> Checkout
			</button>

			<i class="text-gray-700 text-sm">
				Please contact Community Fluency at 617.888.0545 for any questions regarding secure payments, our <a href="/terms">terms of service</a>, and our <a href="/privacy">privacy policy</a>.
			</i>
		</div>
	</div>

@endsection



