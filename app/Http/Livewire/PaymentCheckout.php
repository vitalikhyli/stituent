<?php

namespace App\Http\Livewire;

use Auth;
use Livewire\Component;

class PaymentCheckout extends Component
{
    public $account;
    public $current_balance;

    public $custom_amount;
    public $amount;

    public $checkout_session_id;
    public $stripe_name;
    public $stripe_description;

    public function mount($account, $current_balance)
    {
        $this->account = $account;
        $this->current_balance = $current_balance;
        $this->checkout_session_id = '';
        $this->stripe_name = '';
        $this->stripe_description = '';
    }

    public function render()
    {
        if (is_numeric($this->custom_amount)) {
            $this->amount = $this->custom_amount;
            $this->stripe_name = $this->account->name.' constituent database payment';
            $this->stripe_description = 'A custom payment in the amount of $'.number_format($this->amount).' to Community Fluency for constituent database services.';
        }

        if ($this->amount > 0) {
            $this->updateCheckoutSessionId();
        }

        return view('livewire.payment-checkout');
    }

    public function setToBalance($balance)
    {
        $this->amount = $balance;
        $this->stripe_name = $this->account->name.' constituent database balance';
        $this->stripe_description = 'The total current outstanding balance of your account, including regular and overdue balances.';
        $this->custom_amount = null;
    }

    public function setToAnnual($annual)
    {
        $this->amount = $annual;
        $this->stripe_name = $this->account->name.' Annual constituent database billing';
        $this->stripe_description = 'Pay for the entire year, and receive a 10% discount.';
        $this->custom_amount = null;
    }

    public function updateCheckoutSessionId()
    {
        \Stripe\Stripe::setApiKey(env('STRIPE_KEY'));

        $customer_id = null;
        if ($this->account->stripe_id) {
            $customer_id = $this->account->stripe_id;
        } else {
            $stripe = new \Stripe\StripeClient(env('STRIPE_KEY'));
            if (Auth::user()) {
                $customer = $stripe->customers->create([
                    'description' => $this->account->name,
                    'email' => Auth::user()->email,
                    'name' => Auth::user()->name,
                ]);
            } else {
                $customer = $stripe->customers->create([
                    'description' => $this->account->name,
                ]);
            }
            $customer_id = $customer->id;
            $this->account->stripe_id = $customer_id;
            $this->account->save();
        }

        $stripe_session = \Stripe\Checkout\Session::create([
          'payment_method_types' => ['card'],
          'line_items' => [[
            'amount' => $this->amount * 100,
            'quantity' => 1,
            'currency' => 'usd',
            'name' => $this->stripe_name,
            'description' => $this->stripe_description,
          ]],
          'mode' => 'payment',
          'success_url' => config('app.url').'/stripe/success/'.$this->account->uuid.'?session_id={CHECKOUT_SESSION_ID}',
          'cancel_url' => config('app.url').'/stripe/payment-options/'.$this->account->uuid,
        ]);

        //dd($stripe_session);
        $this->checkout_session_id = $stripe_session->id;
    }
}
