@extends('stripe.base')

@section('title')
	Secure Stripe Payments
@endsection

@section('content')

	<div class="p-8">
		<div class="font-bold font-sans text-4xl border-b-4">
			<i class="fa fa-check-circle text-green-500"></i> Payment Successful!
		</div>
		<div class="mt-4">
			Your payments are handled securely through a partnership between <a href="https://stripe.com">Stripe</a> and Community Fluency. Community Fluency does not have access to your payment methods.
			<br><br>

			<div class="bg-red-100 p-4 w-full">
				Note: Your account balance will not be updated to reflect this payment immediately. We will review the payment and update your account status manually.
			</div>

			<table class="text-sm text-gray-800">
				<tr>
					<td class="p-2 text-gray-500 border-t">Vendor</td>
					<td class="p-2 border-t">Community Fluency</td>
				</tr>
				<tr>
					<td class="p-2 text-gray-500 border-t">Account</td>
					<td class="p-2 border-t">{{ $account->name }}</td>
				</tr>
				<tr>
					<td class="p-2 text-gray-500 border-t">Amount</td>
					<td class="p-2 border-t font-bold">${{ number_format($session_info->display_items[0]->amount / 100) }}</td>
				</tr>

				<tr>
					<td class="p-2 text-gray-500 border-t">Item</td>
					<td class="p-2 border-t">{{ $session_info->display_items[0]->custom->name }}</td>
				</tr>
				<tr>
					<td class="p-2 text-gray-500 border-t">Description</td>
					<td class="p-2 border-t">{{ $session_info->display_items[0]->custom->description }}</td>
				</tr>
			</table>

			<div class="text-4xl text-blue-500 m-8 text-center font-bold handwriting">
				Thank you for your business!
			</div>
			
			<div class="w-1/3 mx-auto py-4">
				<img src="/images/divider1.svg" />
			</div>

			<i class="text-gray-700 text-sm">
				Please contact Community Fluency at 617.888.0545 for any questions regarding secure payments, our <a href="/terms">terms of service</a>, and our <a href="/privacy">privacy policy</a>.
			</i>
		</div>
	</div>

@endsection



