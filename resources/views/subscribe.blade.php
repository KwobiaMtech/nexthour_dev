@extends('layouts.theme')
@section('title',__('staticwords.subscribe'))
@section('main-wrapper')
<section id="main-wrapper" class="main-wrapper user-account-section stripe-content">
    <div class="container-fluid">
         <h4 class="heading"><a href="{{url('account')}}">{{ __('staticwords.Pay') }} &nbsp;<i class="{{ $currency_symbol }}"></i> {{ $plan->amount }} {{ __('staticwords.pay_method') }}</a></h4>

        <div class="panel-setting-main-block pad-lt-50">
          <div class="panel-setting">
            <div class="row">
             
                 @if (isset($stripe_payment) && $stripe_payment == 1)
                  <div class="col-md-12">
                   <h3 class="heading">{{ __('staticwords.CheckoutWith') }} Stripe</h3>
                   <hr>
                   {!! Form::open(['method' => 'POST', 'action' => 'UserAccountController@subscribe', 'id' => 'payment-form']) !!}
                      {{csrf_field()}}
                      <div class="form-row">
                        <div class="form-group">
                          <label for="coupon">{{__('staticwords.applycoupon')}}</label>
                          <input id="coupon" class="form-control" type="text" name="coupon" placeholder="Enter Your Coupon Code">
                        </div>
                        <input type="hidden" name="plan" value="{{$plan->id}}">
                        <label for="card-element">
                          {{__('staticwords.creditordebitcard')}}
                        </label>
                        <div id="card-element">
                          <!-- a Stripe Element will be inserted here. -->
                        </div>
                        <!-- Used to display form errors -->
                        <div id="card-errors" role="alert"></div>
                      </div>
                      <button class="payment-btn stripe"><i class="fa fa-credit-card"></i> {{__('staticwords.submitpayment')}}</button>
                    {!! Form::close() !!}
                    </div>
                 @endif
              

               @if (isset($paypal_payment) && $paypal_payment == 1)
                <div class="col-md-4">
                 
                    <h3 class="heading">{{ __('staticwords.CheckoutWith') }} Paypal !</h3>
                    <hr>
                    {!! Form::open(['method' => 'POST', 'action' => 'PaypalController@postPaymentWithpaypal']) !!}
                      <input type="hidden" name="plan_id" value="{{$plan->id}}">
                      <button class="payment-btn paypal-btn"><i class="fa fa-credit-card"></i> {{__('staticwords.payvia')}} Paypal</button>
                    {!! Form::close() !!}
                  
                </div>
              @endif

              @if(isset($braintree) && $braintree==1)
                <div class="col-md-4">
                  <h3 class="heading">{{ __('staticwords.CheckoutWith') }} Braintree</h3>
                  <hr>
                   <div id="paypal-errors" role="alert"></div>
                   <a href="javascript:void(0);" class="payment-btn bt-btn"><i class="fa fa-credit-card"></i> {{__('staticwords.payvia')}} Card / Paypal</a>
                  <div class="braintree">
                    <form method="POST" id="bt-form" action="{{ url('payment/braintree') }}">
                      {{ csrf_field() }} 
                      <div class="form-group">
                       
                        <label for="amount">{{__('staticwords.amount')}}</label>                       
                        <input type="text" class="form-control"name="amount" readonly="" value="{{$plan->amount}}">  
                      </div>
                      <div class="bt-drop-in-wrapper">
                          <div id="bt-dropin"></div>
                      </div>
                      <input type="hidden" name="plan_id" value="{{$plan->id}}"/>
                      <input id="nonce" name="payment_method_nonce" type="hidden" />
                      <div id="pay-errors" role="alert"></div>
                      <button class="payment-btn" type="submit"><span>{{__('staticwords.paynow')}}</span></button>
                    </form>
                  </div>
                </div>
              @endif

               @if($currency_code == "INR")

                 @if (isset($payu_payment) && $payu_payment == 1)
                    <div class="col-md-4">
                      <div class="payu">
                        <h3 class="heading">{{ __('staticwords.CheckoutWith') }} PayUmoney !</h3>
                        <hr>
                        {!! Form::open(['method' => 'POST', 'action' => 'PayuController@payment']) !!}
                          <input type="hidden" name="plan_id" value="{{$plan->id}}">
                          <button class="payment-btn payu-btn"><i class="fa fa-credit-card"></i> {{__('staticwords.payvia')}} Payu</button>
                        {!! Form::close() !!}
                      </div>
                    </div>
                 @endif
                 
                 @if (isset($paytm_payment) && $paytm_payment == 1)
                  <div class="col-md-4">
                    <h3 class="heading">{{ __('staticwords.CheckoutWith') }} Paytm</h3>
                    <hr>
                      <div class="paytm">
                     
                        {!! Form::open(['method' => 'POST', 'action' => 'PaytemController@store']) !!}
                          <input type="hidden" name="plan_id" value="{{$plan->id}}">
                          <button class="payment-btn paytm-btn"><i class="fa fa-credit-card"></i> {{__('staticwords.payvia')}} Paytm</button>
                        {!! Form::close() !!}
                      </div>
                  </div>
                 @endif
                
        
                 <div class="col-md-4">
                   <h3 class="heading">{{ __('staticwords.CheckoutWith') }} Razorpay</h3>
                   <hr>
                   <form action="{{ route('paysuccess',$plan->id) }}" method="POST">
                     <script src="https://checkout.razorpay.com/v1/checkout.js"
                                      data-key="{{ env('RAZOR_PAY_KEY') }}"
                                      data-amount="{{ $plan->amount*100 }}"
                                      data-buttontext="Pay {{ $plan->amount }} {{$plan->currency}}"
                                      data-name="{{ config('app.name') }}"
                                      data-description="Payment For Order {{ uniqid() }}"
                                      data-image="{{url('images/logo/'.$logo)}}"
                                      data-prefill.name="{{ Auth::user()->name }}"
                                      data-prefill.email="{{ Auth::user()->email }}"
                                data-theme.color="#111111">
                      </script>
                      <input type="hidden" name="_token" value="{!!csrf_token()!!}">
                      <input type="hidden" custom="Hidden Element" name="hidden">
                    </form>
                 </div>

               @endif

               @if($currency_code == "NGN")
                  @if(isset($paystack) && $paystack == 1) 
                    <h3 class="heading">{{ __('staticwords.CheckoutWith') }} Paystack</h3>
                    <hr>
                    <div class="paystack">
                      @php
                        $amount = $plan->amount*100;
                      @endphp
                      <form method="POST" action="{{ url('payment/paystack') }}" accept-charset="UTF-8" class="form-horizontal" role="form">
                        <input type="hidden" name="email" value="{{$auth->email}}"> 
                        <input type="hidden" name="amount" value="{{$amount}}"> 
                        <input type="hidden" name="currency" value="{{$plan->currency}}"> 
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="metadata" value="{{ json_encode($array = ['plan_id' => $plan->plan_id,]) }}" > 
                        <input type="hidden" name="reference" value="{{ Paystack::genTranxRef() }}">
                        <input type="hidden" name="key" value="{{ config('paystack.secretKey') }}"> 
                        {{ csrf_field() }}
                        <button class="payment-btn paystack-btn"><i class="fa fa-credit-card"></i>{{__('staticwords.payvia')}} Paystack</button>
                      </form>
                    </div>
                  @endif
               @endif

               @if(isset($coinpay) && $coinpay==1)
                 <div class="col-md-4">
                    <h3 class="heading">{{ __('staticwords.CheckoutWith') }} CoinPayment</h3>
                    <hr>
                     <div class="coinpayment">
                        <form method="POST" id="coinpayment-form" action="{{ url('payment/coinpayment') }}">
                          {{ csrf_field() }} 
                          <div class="form-group"> 
                            <label for="amount">{{__('staticwords.amount')}}</label>                       
                            <input type="text" class="form-control"name="amount" readonly="" value="{{$plan->amount}}">
                             <label for="amount">{{__('staticwords.currency')}}</label> 
                            <select style="padding: 0px; " class="form-control" name="currency">
                              <option value="BTC">BTC</option>
                               <option value="LTC">LTC</option>
                                <option value="ETH">ETH</option>
                                 <option value="LOKI">LOKI</option>
                                  <option value="XZC">XZC</option>
                            </select>
                               <input type="hidden" name="plan_id" value="{{$plan->id}}"/>
                          </div>
                        
                        
                         
                          <button class="payment-btn" type="submit"><span>{{ __('staticwords.paynow') }}</span></button>
                        </form>
                      </div> 
                 </div>
                
               @endif

                @if (isset($bankdetails) && $bankdetails == 1) 
                  <div class="col-md-12">
                    <h3 class="heading">{{ __('staticwords.CheckoutWith') }} {{ __('staticwords.BankTransfer') }}</h3>
                    <hr>
                  <button class="payment-btn" id="bankbutton">{{ __('staticwords.DirectBankTransfer') }}</button>
               <div id="bankdetail" style="display: none;">
                <div class="row">
                  <div class="col-md-2">
                    <p style="font-size: 17px;">{{__('staticwords.AccountName')}} :</p>
                  </div>
                  <div class="col-md-2">
                     <p style="font-size: 16px;">{{$account_name}}</p>
                  </div>
                  <div class="col-md-2">
                    <p style="font-size: 17px;">{{__('staticwords.accountnumber')}} :</p>
                  </div>
                     <div class="col-md-2">
                     <p style="font-size: 16px;">{{$account_no}}</p>
                    </div>
                   </div> 
                    <div class="row">
                  <div class="col-md-2">
                    <p style="font-size: 17px;">{{__('staticwords.BankName')}} :</p>
                  </div>
                     <div class="col-md-2">
                     <p style="font-size: 16px;">{{$bank}}</p>
                    </div>
                     <div class="col-md-2">
                    <p style="font-size: 17px;">{{__('staticwords.IFSCCode')}}:</p>
                  </div>
                     <div class="col-md-2">
                     <p style="font-size: 16px;">{{$ifsc_code}}</p>
                    </div>
                   </div> 
                   <div class="col-md-9">
                     <p style="color: #d63031;">* {{__('staticwords.BankNote')}} <a href="{{url('contactus')}}" style="color: #00b894;">{{__('ContactHere')}}</a></p>
                     </div>
                   </div>
              </div>
                @endif

            </div>
          </div>
        </div>
    </div>
</section>
@endsection
@section('custom-script')

  <script>
     
    $(function(){
      $('.paypal-btn').on('click', function(){
        $('.paypal-btn').addClass('load');
      });

      $('.paystack-btn').on('click', function(){
        $('.paystack-btn').addClass('load');
      });  
      $('.payu-btn').on('click', function(){
        $('.payu-btn').addClass('load');
      }); 
      $('.braintree').hide();
    });
    // Create a Stripe client
    var stripe = Stripe('{{ env('STRIPE_KEY') }}');
    // Create an instance of Elements
    var elements = stripe.elements();
    // Custom styling can be passed to options when creating an Element.
    // (Note that this demo uses a wider set of styles than the guide below.)
    var style = {
      base: {
        color: '#32325d',
        lineHeight: '18px',
        fontFamily: '"Lato", sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
          color: '#aab7c4'
        }
      },
      invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
      }
    };
    // Create an instance of the card Element
    var card = elements.create('card', {
      style: style,
      hidePostalCode: true
    });
    // Add an instance of the card Element into the `card-element` <div>
    card.mount('#card-element');
    // Handle real-time validation errors from the card Element.
    card.addEventListener('change', function(event) {
      var displayError = document.getElementById('card-errors');
      if (event.error) {
        displayError.textContent = event.error.message;
      } else {
        displayError.textContent = '';
      }
    });
    // Handle form submission
    var stripeform = document.getElementById('payment-form');
    stripeform.addEventListener('submit', function(event) {
      event.preventDefault();
      stripe.createToken(card).then(function(result) {
        if (result.error) {
          // Inform the user if there was an error
          var errorElement = document.getElementById('card-errors');
          errorElement.textContent = result.error.message;
        } else {
          // Send the token to your server
          $('.payment-btn.stripe').addClass('load');
          stripeTokenHandler(result.token);
        }
      });
    });
    function stripeTokenHandler(token) {
      // Insert the token ID into the form so it gets submitted to the server
      var stripeform = document.getElementById('payment-form');
      var hiddenInput = document.createElement('input');
      hiddenInput.setAttribute('type', 'hidden');
      hiddenInput.setAttribute('name', 'stripeToken');
      hiddenInput.setAttribute('value', token.id);
      stripeform.appendChild(hiddenInput);
      // Submit the form
      stripeform.submit();
    }
  </script>
  <script src="https://js.braintreegateway.com/web/dropin/1.20.0/js/dropin.min.js"></script>
 <script>  
    var client_token = null;   
    $(function(){
      $('.bt-btn').on('click', function(){
        $('.bt-btn').addClass('load');
        $.ajax({
          headers: {
              "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
          },
          type: "POST",
          
          url: "{{ url('bttoken') }}",
          success: function(t) {   
              if(t.client != null){
                client_token = t.client;
                btform(client_token);
                console.log(client_token);
              }
          },
          error: function(XMLHttpRequest, textStatus, errorThrown) {
            console.log(XMLHttpRequest);
            $('.bt-btn').removeClass('load');
            alert('Payment error. Please try again later.');
          }
        });
      });
    });
    function btform(token){
      var payform = document.querySelector('#bt-form'); 
      braintree.dropin.create({
        authorization: client_token,
        selector: '#bt-dropin',  
        paypal: {
          flow: 'vault'
        },
      }, function (createErr, instance) {
        if (createErr) {
          console.log('Create Error', createErr);
          alert('Payment error. Please try again later.');
          return;
        }
        else{
          $('.bt-btn').hide();
          $('.braintree').show();
        }
        payform.addEventListener('submit', function (event) {
        event.preventDefault();
        instance.requestPaymentMethod(function (err, payload) {
          if (err) {
            console.log('Request Payment Method Error', err);
            alert('Payment error. Please try again later.');
            return;
          }
          // Add the nonce to the form and submit
          document.querySelector('#nonce').value = payload.nonce;
          payform.submit();
        });
      });
    });
    }
    $('#bankbutton').click(function () {$('#bankdetail').toggle();});
  </script>


@endsection