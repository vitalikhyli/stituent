<?php

namespace App\Http\Controllers;

use App\Account;
use Auth;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function redirectAccountOptions()
    {
        $account_uuid = Auth::user()->account->uuid;

        return redirect('/stripe/payment-options/'.$account_uuid);
    }

    public function simplePaymentLink($simple_payment_words)
    {
        $account = Account::where('payment_simple', $simple_payment_words)->first();
        if (! $account) {
            return view('stripe.payment-not-found', compact('simple_payment_words'));
        }

        return redirect('/stripe/payment-options/'.$account->uuid);
    }

    public function lookupByBillygoat($bgid)
    {
        $account = Account::where('billygoat_id', $bgid)->first();
        if (! $account) {
            return null;
        }

        return $account->payment_simple;
    }

    public function options($account_uuid)
    {
        $account = Account::where('uuid', $account_uuid)->first();

        if (! $account) {
            abort(401);
        }
        $current_balance = $account->billyGoatOutstandingBal();
        if (! is_numeric($current_balance)) {
            $current_balance = 0;
        }
        $current_balance = $current_balance / 100;

        return view('stripe.payment-options', compact('current_balance', 'account'));
    }

    public function success($account_uuid)
    {
        $account = Account::where('uuid', $account_uuid)->first();

        $stripe = new \Stripe\StripeClient(env('STRIPE_KEY'));

        $session_info = $stripe->checkout->sessions->retrieve(
          request('session_id'),
          []
        );
        //dd($session_info);

        return view('stripe.success', compact('account', 'session_info'));
    }
}
