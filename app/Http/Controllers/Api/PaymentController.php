<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Package;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Stripe\Customer;
use Stripe\Stripe;
use App\Config;
use Braintree\MerchantAccount;
use Illuminate\Support\Facades\Mail;
use Braintree_Transaction;
use Braintree_ClientToken;
use App\PaypalSubscription;
use Validator;
use Stripe\Subscription;
use App\Multiplescreen;
use App\Mail\SendInvoiceMailable;

class PaymentController extends Controller
{ 
  public function stripeprofile(Request $request)
  {
    $auth = Auth::user();
    $sub_id = $request->transaction;
    Stripe::setApiKey(env('STRIPE_SECRET'));
    $txn = Subscription::retrieve($sub_id);
    $input = $request->all();
    $plan = Package::where('plan_id',$txn->plan->id)->first();
    $auth->update([
      'stripe_id' => $input['customer'] != null ? $input['customer'] : $auth->stripe_id,
      'card_brand' => $input['type'] != null ? $input['type'] : $auth->card_brand,
      'card_last_four' => $input['card'] != null ? $input['card'] : $auth->card_last_four
    ]);
    $auth->save();
    $created_subscription = $auth->subscriptions()->create([
      'user_id' => $auth->id,
      'name' => $plan->name,
      'stripe_id' => $txn->id,
      'stripe_plan' => $txn->plan->id,
      'quantity' => $txn->quantity,
      //'currency' => $txn->plan->currency,
      'amount' => $txn->plan->amount/100,
      'trial_ends_at' => $txn->trial_end != null ? Carbon::createFromTimestampUTC($txn->trial_end) : null,
      'ends_at' => null,      
      'subscription_from' => Carbon::createFromTimestamp($txn->current_period_start),
      'subscription_to' => Carbon::createFromTimestamp($txn->current_period_end)
    ]);
    if ($created_subscription) {
      $screen = $plan->screens;
      if($screen > 0){
        $multiplescreen = Multiplescreen::where('user_id',$auth->id)->first();
         if(isset($multiplescreen)){
            $multiplescreen->update([
              'pkg_id' => $plan->id,
              'user_id' => $auth->id,
              'screen1' => $screen >= 1 ? $auth->name :  null,
              'screen2' => $screen >= 2 ? 'NH2-User' :  null,
              'screen3' => $screen >= 3 ? 'NH3-User' :  null,
              'screen4' => $screen >= 4 ? 'NH4-User' :  null,
              'screen5' => $screen >= 5 ? 'NH5-User' :  null,
              'screen6' => $screen >= 6 ? 'NH6-User' :  null,
              'screen7' => $screen >= 7 ? 'NH7-User' :  null,
              'screen8' => $screen >= 8 ? 'NH8-User' :  null,
              'screen9' => $screen >= 9 ? 'NH9-User' :  null,
              'screen10' => $screen >= 10 ? 'NH10-User' :  null,
            ]);
        }
        else{
            $multiplescreen = Multiplescreen::create([
              'pkg_id' => $plan->id,
              'user_id' => $auth->id,
              'screen1' => $screen >= 1 ? $auth->name :  null,
              'screen2' => $screen >= 2 ? 'NH2-User' :  null,
              'screen3' => $screen >= 3 ? 'NH3-User' :  null,
              'screen4' => $screen >= 4 ? 'NH4-User' :  null,
              'screen5' => $screen >= 5 ? 'NH5-User' :  null,
              'screen6' => $screen >= 6 ? 'NH6-User' :  null,
              'screen7' => $screen >= 7 ? 'NH7-User' :  null,
              'screen8' => $screen >= 8 ? 'NH8-User' :  null,
              'screen9' => $screen >= 9 ? 'NH9-User' :  null,
              'screen10' => $screen >= 10 ? 'NH10-User' :  null,
            ]);
         }
      }
      try{
        Mail::to($auth->email)->send(new SendInvoiceMailable());
      }
      catch(\Swift_TransportException $e){
        return response()->json(array('message' => 'Successful but mail not send', 'subscription' => $created_subscription), 200);  
      }
      return response()->json(array('message' => 'Successful', 'subscription' => $created_subscription), 200);
    }
    else{ 
      return response()->json(array('auth' =>$auth, 'message' => 'error in storing data'), 200);
    }
  }
  
  public function stripeupdate($id, $value)
  {
      $auth = Auth::user();
      if($a = $auth->subscribed($id)){
        $flag = $auth->subscription($id)->cancelled();
        if((int)$flag != $value){        
          return response()->json($value, 200);
        }
        elseif($value == 1){
          $auth->subscription($id)->resume();
          return response()->json($value, 200);
        }
       elseif($value == 0){
        $auth->subscription($id)->cancel();
        return response()->json($value, 200);
       }
       else{
        return response()->json('invalid value', 400);
       }
     }        
     return response()->json('Not subscribed to this plan', 400);
  }

  public function paypalupdate($id,$value)
  {
    $auth = Auth::user();
    $data = $auth->paypal_subscriptions->where('payment_id', $id)->first();
    if($data){
      $data->status = $value;
      $data->save();
      return response()->json($data->status, 200);
    }
    else{
      return response()->json('Not subscribed to this plan', 400);
    }
  }

  public function stripedetail()
  {
    $auth = Auth::user();
    if($auth){
      $stripekey =  env('STRIPE_KEY');
      $stripepass = env('STRIPE_SECRET');
      $paystackkey = env('PAYSTACK_PUBLIC_KEY');
      if(isset($stripekey) && isset($stripepass) && isset($paystackkey)){
        return response()->json(array('key' => $stripekey, 'pass' => $stripepass, 'paystack' => $paystackkey), 200);
      }
      return response()->json('error', 400);
    }
    return response()->json('please login first', 400);
  }

  public function btpayment(Request $request)
  {
    $customer = Auth::user();
    $currency = Config::findOrFail(1)->currency_code;
    $account = env('BTREE_MERCHANT_ACCOUNT_ID');
    $acc = MerchantAccount::find($account);
    if(isset($acc) && ($acc->currencyIsoCode == $currency)){
      $result = Braintree_Transaction::sale([
        'amount' =>  $request->amount,
        'paymentMethodNonce' => $request->nonce,
        'options' => [
          'submitForSettlement' => True
         ]
      ]);     
      if ($result->success || !is_null($result->transaction)) {
        $txn = $result->transaction;
        $paypal = null;
        if($txn->paymentInstrumentType == 'paypal_account'){
          $paypal = $txn->paypal;
        }
        $plan = Package::findOrFail($request->plan_id);
        $user_email = $customer->email;
        $com_email = Config::findOrFail(1)->w_email;
        $current_date = Carbon::now();
        $end_date = null;

        if ($plan->interval == 'month') {
          $end_date = Carbon::now()->addMonths($plan->interval_count);
        } else if ($plan->interval == 'year') {
          $end_date = Carbon::now()->addYears($plan->interval_count);
        } else if ($plan->interval == 'week') {
          $end_date = Carbon::now()->addWeeks($plan->interval_count);
        } else if ($plan->interval == 'day') {
          $end_date = Carbon::now()->addDays($plan->interval_count);
        }

        $created_subscription = PaypalSubscription::create([
          'user_id'    => $customer->id,
          'payment_id' => isset($paypal) ? $paypal['paymentId'] : $txn->id,
          'user_name' => $customer->name,
          'package_id' => $request->plan_id,
          'price'      => $txn->amount,
         // 'currency' => $plan->currency,
          'status'     => '1',
          'method'     => isset($paypal) != null ? 'paypal' : 'braintree',
          'subscription_from' => $current_date,
          'subscription_to' => $end_date
        ]);
        if ($created_subscription) {
          $screen = $plan->screens;
          if($screen > 0){
            $multiplescreen = Multiplescreen::where('user_id',$customer->id)->first();
             if(isset($multiplescreen)){
                $multiplescreen->update([
                  'pkg_id' => $plan->id,
                  'user_id' => $customer->id,
                  'screen1' => $screen >= 1 ? $customer->name :  null,
                  'screen2' => $screen >= 2 ? 'NH2-User' :  null,
                  'screen3' => $screen >= 3 ? 'NH3-User' :  null,
                  'screen4' => $screen >= 4 ? 'NH4-User' :  null,
                  'screen5' => $screen >= 5 ? 'NH5-User' :  null,
                  'screen6' => $screen >= 6 ? 'NH6-User' :  null,
                  'screen7' => $screen >= 7 ? 'NH7-User' :  null,
                  'screen8' => $screen >= 8 ? 'NH8-User' :  null,
                  'screen9' => $screen >= 9 ? 'NH9-User' :  null,
                  'screen10' => $screen >= 10 ? 'NH10-User' :  null,
                ]);
            }
            else{
                $multiplescreen = Multiplescreen::create([
                  'pkg_id' => $plan->id,
                  'user_id' => $customer->id,
                  'screen1' => $screen >= 1 ? $customer->name :  null,
                  'screen2' => $screen >= 2 ? 'NH2-User' :  null,
                  'screen3' => $screen >= 3 ? 'NH3-User' :  null,
                  'screen4' => $screen >= 4 ? 'NH4-User' :  null,
                  'screen5' => $screen >= 5 ? 'NH5-User' :  null,
                  'screen6' => $screen >= 6 ? 'NH6-User' :  null,
                  'screen7' => $screen >= 7 ? 'NH7-User' :  null,
                  'screen8' => $screen >= 8 ? 'NH8-User' :  null,
                  'screen9' => $screen >= 9 ? 'NH9-User' :  null,
                  'screen10' => $screen >= 10 ? 'NH10-User' :  null,
                ]);
              }
            }
          try{
            Mail::send('user.invoice', ['paypal_sub' => $created_subscription, 'invoice' => null], function($message) use ($com_email, $user_email) {
              $message->from($com_email)->to($user_email)->subject('Invoice');
            });
          }catch(\Swift_TransportException $e){
            return response()->json(array('message' => 'Successful but mail not send', 'subscription' => $created_subscription), 200);          
          }
        }     
        return response()->json(array('message' => 'Successful', 'subscription' => $created_subscription), 200);
      } else {      
        return response()->json('Payment error', 401);
      }
    }
    else {      
        return response()->json('Currency Not Supported', 401);
    }
  }

  public function bttoken()
  {
    $customer = Auth::user(); 
    if(!$customer->braintree_id){
      $response = \Braintree_Customer::create([
         'id' => 'BT0'.$customer->id
      ]);
      if( $response->success) {
        $customer->braintree_id = $response->customer->id;
        $customer->save();
      }
    }
    $client = Braintree_ClientToken::generate(["customerId" => $customer->braintree_id]);
    return response()->json(array('client' => $client), 200);
  }

public function paystack(Request $request)
  {
    $auth = Auth::user();
    $payment = $request->all();
    $plan = Package::findorfail($payment['plan_id']);
    $user_email = $auth->email;
    $com_email = Config::findOrFail(1)->w_email;
    $current_date = Carbon::now();
    $end_date = null;

    if ($plan->interval == 'month') {
      $end_date = Carbon::now()->addMonths($plan->interval_count);
    } else if ($plan->interval == 'year') {
      $end_date = Carbon::now()->addYears($plan->interval_count);
    } else if ($plan->interval == 'week') {
      $end_date = Carbon::now()->addWeeks($plan->interval_count);
    } else if ($plan->interval == 'day') {
      $end_date = Carbon::now()->addDays($plan->interval_count);
    }

    $created_subscription = PaypalSubscription::create([
      'user_id'    => $auth->id,
      'payment_id' => $payment['reference'],
      'user_name' => $auth->name,
      'package_id' => $plan->id,
      //'currency' => $plan->currency,
      'price'      => $payment['amount'],
      'status'     => '1',
      'method'     => 'paystack',
      'subscription_from' => $current_date,
      'subscription_to' => $end_date
    ]);
    if ($created_subscription) {
      $screen = $plan->screens;
      if($screen > 0){
        $multiplescreen = Multiplescreen::where('user_id',$auth->id)->first();
        if(isset($multiplescreen)){
            $multiplescreen->update([
              'pkg_id' => $plan->id,
              'user_id' => $auth->id,
              'screen1' => $screen >= 1 ? $auth->name :  null,
              'screen2' => $screen >= 2 ? 'NH2-User' :  null,
              'screen3' => $screen >= 3 ? 'NH3-User' :  null,
              'screen4' => $screen >= 4 ? 'NH4-User' :  null,
              'screen5' => $screen >= 5 ? 'NH5-User' :  null,
              'screen6' => $screen >= 6 ? 'NH6-User' :  null,
              'screen7' => $screen >= 7 ? 'NH7-User' :  null,
              'screen8' => $screen >= 8 ? 'NH8-User' :  null,
              'screen9' => $screen >= 9 ? 'NH9-User' :  null,
              'screen10' => $screen >= 10 ? 'NH10-User' :  null,
            ]);
        }
        else{
            $multiplescreen = Multiplescreen::create([
              'pkg_id' => $plan->id,
              'user_id' => $auth->id,
              'screen1' => $screen >= 1 ? $auth->name :  null,
              'screen2' => $screen >= 2 ? 'NH2-User' :  null,
              'screen3' => $screen >= 3 ? 'NH3-User' :  null,
              'screen4' => $screen >= 4 ? 'NH4-User' :  null,
              'screen5' => $screen >= 5 ? 'NH5-User' :  null,
              'screen6' => $screen >= 6 ? 'NH6-User' :  null,
              'screen7' => $screen >= 7 ? 'NH7-User' :  null,
              'screen8' => $screen >= 8 ? 'NH8-User' :  null,
              'screen9' => $screen >= 9 ? 'NH9-User' :  null,
              'screen10' => $screen >= 10 ? 'NH10-User' :  null,
            ]);
         }
      }
      try{
      Mail::send('user.invoice', ['paypal_sub' => $created_subscription, 'invoice' => null], function($message) use ($com_email, $user_email) {
        $message->from($com_email)->to($user_email)->subject('Invoice');
      });
      }catch(\Swift_TransportException $e){
         return response()->json(array('message' => 'Successful but mail not send', 'subscription' => $created_subscription), 200);          
      }   
      return response()->json(array('message' => 'Successful', 'subscription' => $created_subscription), 200);
    } else {      
      return response()->json('error in storing data', 200);
    }
  }

}

   