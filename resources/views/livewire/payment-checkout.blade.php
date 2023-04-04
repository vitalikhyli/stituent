<div class="p-8">
		<div class="font-bold font-sans text-4xl border-b-4">
			<div class="float-right font-normal text-xl mt-4">
				Call <b>617.888.0545</b> with questions
			</div>
			Secure Payments
		</div>
		<div class="mt-4">
			Your payments are handled securely through a partnership between <a href="https://stripe.com">Stripe</a> and Community Fluency. Community Fluency does not have access to your payment methods.
			<br><br>
			
			<div class="text-2xl text-blue text-center">
				Your current balance is: 
				<span class="text-black font-bold">
				@if ($current_balance)
				
					${{ number_format($current_balance) }}
				
				@else

					<i>No Current Balance</i>

				@endif
				</span>
			</div>

			<div class="text-gray-700 mt-4">
				You have 3 options for making your online payment. Clicking any of these will redirect you to our secure payments processor, Stripe, to verify and complete the transaction.
			</div>

			<div class="flex mt-8 text-gray-700">
				<div class="w-1/3 px-4 
								@if ($amount != $current_balance)
									text-gray-500
								@endif
								text-center">
					<div class="font-bold text-2xl mb-4">
						<div class="mx-auto text-center w-10 rounded-full bg-blue-light px-3 text-white text-xl py-1">1</div> Existing Balance
					</div>
					This is the current amount your account owes, based on your payment frequency as we have recorded and shown above.
				</div>

				<div class="w-1/3 px-4 
								@if ($amount != $account->annual_price)
									text-gray-500
								@endif
								text-center border-l-2 border-r-2 border-t-2 rounded-t">
					<div class="font-bold text-2xl mb-4">
						<div class="mx-auto text-center w-10 rounded-full bg-blue-light px-3 text-white text-xl py-1 -mt-5">2</div> Full Year
					</div>
					This will cover one year of your subscription, giving you a <b class="text-black">10% discount</b>.
				</div>

				<div class="w-1/3 px-4 
								@if ($amount != $custom_amount)
									text-gray-500
								@endif
								text-center">
					<div class="font-bold text-2xl mb-4">
						<div class="mx-auto text-center w-10 rounded-full bg-blue-light px-3 text-white text-xl py-1">3</div> Custom Amount
					</div>
					This allows you to enter the exact amount you would like to pay.
				</div>


			</div>

			<div class="flex">
				<div class="w-1/3 text-center 
								@if ($amount != $current_balance)
									text-gray-500
								@endif
								pt-8">
					
					@if ($current_balance > 0)
						<div class="font-bold 
									
									text-3xl">
							<span class="absolute text-lg -ml-3">$</span>{{ number_format($current_balance) }}
						</div>
						<button wire:click="setToBalance('{{ $current_balance }}')" class="rounded bg-gray-600 text-white hover:text-white hover:bg-blue-dark px-3 py-2">Select Existing Balance</button>
					@else
						<i class="text-gray-600 text-sm">
							Our records do not indicate a current balance.
						</i>
					@endif
				</div>

				<div class="w-1/3 text-center 
								@if ($amount != $account->annual_price)
									text-gray-500
								@endif
								border-b-2 border-l-2 border-r-2 pb-8 pt-8">

					@if ($account->annual_price)
						<div class="font-bold 
									
									text-3xl">
							<span class="absolute text-lg -ml-3">$</span>
							{{ number_format($account->annual_price) }}
						</div>
						<button wire:click="setToAnnual('{{ $account->annual_price }}')" class="rounded bg-gray-600 text-white hover:text-white hover:bg-blue-dark px-3 py-2">Select Annual</button>
					@else

						<i class="text-gray-600 text-sm">Please contact us at 617.888.0545 for details about your Annual Billing.</i>

					@endif
					
					
				</div>

				<div class="w-1/3 text-center 
								@if ($amount != $custom_amount)
									text-gray-500
								@endif
								pt-8">

					<div class="font-bold text-2xl">
						<div class="font-bold 
									
									text-3xl">
							<span class="absolute text-lg -ml-3">$</span>
							{{ number_format($custom_amount) }}
						</div>
						<input autocomplete="off" wire:model.debounce.500ms="custom_amount" class="px-2 w-32 text-center border-2 border-blue" type="text" name="payment-amount" placeholder="Amount" />
					</div>
					

				</div>

			</div>

			<div wire:loading.class="opacity-50" class="text-gray-600 text-center text-lg p-8">
				@if ($amount > 0) 
					<b>{{ $stripe_name }}</b><br>
					{{ $stripe_description }}<br>
					<button onclick="checkout('{{ $checkout_session_id }}')" class="mx-auto text-2xl rounded-full bg-blue text-white px-4 py-2 hover: text-white hover:bg-blue-dark hover:shadow w-64 text-center mt-4">
						<i class="fa fa-cc-stripe"></i> Checkout - ${{ $amount }}
					</button>
				@else 
					
					Choose from the options above to set your payment amount.
					
				@endif
			</div>

			<div class="w-1/3 mx-auto py-4">
				<img src="/images/divider1.svg" />
			</div>

			<div class="italic text-gray-700 text-sm mt-8">
				Please contact Community Fluency at 617.888.0545 for any questions regarding secure payments, our <a href="/terms">terms of service</a>, and our <a href="/privacy">privacy policy</a>.
			</div>
		</div>
	</div>