@extends('stripe.base')

@section('title')
	Secure Stripe Payments
@endsection

@section('content')

	@livewire('payment-checkout', ['account' => $account, 'current_balance' => $current_balance])

@endsection

@push('scripts')

	<script src="https://js.stripe.com/v3/"></script>

	<script type="text/javascript">

		var checkout_session_id = '';

	    // Get the value of the "count" property
	        
        
        function checkout(checkout_session_id)
		{
			//alert(checkout_session_id);
			
			var stripe = Stripe('{{ env('STRIPE_PUB') }}');

			stripe.redirectToCheckout({
			  // Make the id field from the Checkout Session creation API response
			  // available to this file, so you can provide it as parameter here
			  // instead of the CHECKOUT_SESSION_ID placeholder.
			  sessionId: checkout_session_id
			}).then(function (result) {
				alert(result.error.message);
			  // If `redirectToCheckout` fails due to a browser or network
			  // error, display the localized error message to your customer
			  // using `result.error.message`.
			});	
			
		}


	    

		
		
	</script>

@endpush


