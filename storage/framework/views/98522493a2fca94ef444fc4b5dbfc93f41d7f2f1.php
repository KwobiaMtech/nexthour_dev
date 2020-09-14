
<?php $__env->startSection('title',__('staticwords.subscribe')); ?>
<?php $__env->startSection('main-wrapper'); ?>
<section id="main-wrapper" class="main-wrapper user-account-section stripe-content">
    <div class="container-fluid">
         <h4 class="heading"><a href="<?php echo e(url('account')); ?>"><?php echo e(__('staticwords.Pay')); ?> &nbsp;<i class="<?php echo e($currency_symbol); ?>"></i> <?php echo e($plan->amount); ?> <?php echo e(__('staticwords.pay_method')); ?></a></h4>

        <div class="panel-setting-main-block pad-lt-50">
          <div class="panel-setting">
            <div class="row">
             
                 <?php if(isset($stripe_payment) && $stripe_payment == 1): ?>
                  <div class="col-md-12">
                   <h3 class="heading"><?php echo e(__('staticwords.CheckoutWith')); ?> Stripe</h3>
                   <hr>
                   <?php echo Form::open(['method' => 'POST', 'action' => 'UserAccountController@subscribe', 'id' => 'payment-form']); ?>

                      <?php echo e(csrf_field()); ?>

                      <div class="form-row">
                        <div class="form-group">
                          <label for="coupon"><?php echo e(__('staticwords.applycoupon')); ?></label>
                          <input id="coupon" class="form-control" type="text" name="coupon" placeholder="Enter Your Coupon Code">
                        </div>
                        <input type="hidden" name="plan" value="<?php echo e($plan->id); ?>">
                        <label for="card-element">
                          <?php echo e(__('staticwords.creditordebitcard')); ?>

                        </label>
                        <div id="card-element">
                          <!-- a Stripe Element will be inserted here. -->
                        </div>
                        <!-- Used to display form errors -->
                        <div id="card-errors" role="alert"></div>
                      </div>
                      <button class="payment-btn stripe"><i class="fa fa-credit-card"></i> <?php echo e(__('staticwords.submitpayment')); ?></button>
                    <?php echo Form::close(); ?>

                    </div>
                 <?php endif; ?>
              

               <?php if(isset($paypal_payment) && $paypal_payment == 1): ?>
                <div class="col-md-4">
                 
                    <h3 class="heading"><?php echo e(__('staticwords.CheckoutWith')); ?> Paypal !</h3>
                    <hr>
                    <?php echo Form::open(['method' => 'POST', 'action' => 'PaypalController@postPaymentWithpaypal']); ?>

                      <input type="hidden" name="plan_id" value="<?php echo e($plan->id); ?>">
                      <button class="payment-btn paypal-btn"><i class="fa fa-credit-card"></i> <?php echo e(__('staticwords.payvia')); ?> Paypal</button>
                    <?php echo Form::close(); ?>

                  
                </div>
              <?php endif; ?>

              <?php if(isset($braintree) && $braintree==1): ?>
                <div class="col-md-4">
                  <h3 class="heading"><?php echo e(__('staticwords.CheckoutWith')); ?> Braintree</h3>
                  <hr>
                   <div id="paypal-errors" role="alert"></div>
                   <a href="javascript:void(0);" class="payment-btn bt-btn"><i class="fa fa-credit-card"></i> <?php echo e(__('staticwords.payvia')); ?> Card / Paypal</a>
                  <div class="braintree">
                    <form method="POST" id="bt-form" action="<?php echo e(url('payment/braintree')); ?>">
                      <?php echo e(csrf_field()); ?> 
                      <div class="form-group">
                       
                        <label for="amount"><?php echo e(__('staticwords.amount')); ?></label>                       
                        <input type="text" class="form-control"name="amount" readonly="" value="<?php echo e($plan->amount); ?>">  
                      </div>
                      <div class="bt-drop-in-wrapper">
                          <div id="bt-dropin"></div>
                      </div>
                      <input type="hidden" name="plan_id" value="<?php echo e($plan->id); ?>"/>
                      <input id="nonce" name="payment_method_nonce" type="hidden" />
                      <div id="pay-errors" role="alert"></div>
                      <button class="payment-btn" type="submit"><span><?php echo e(__('staticwords.paynow')); ?></span></button>
                    </form>
                  </div>
                </div>
              <?php endif; ?>

               <?php if($currency_code == "INR"): ?>

                 <?php if(isset($payu_payment) && $payu_payment == 1): ?>
                    <div class="col-md-4">
                      <div class="payu">
                        <h3 class="heading"><?php echo e(__('staticwords.CheckoutWith')); ?> PayUmoney !</h3>
                        <hr>
                        <?php echo Form::open(['method' => 'POST', 'action' => 'PayuController@payment']); ?>

                          <input type="hidden" name="plan_id" value="<?php echo e($plan->id); ?>">
                          <button class="payment-btn payu-btn"><i class="fa fa-credit-card"></i> <?php echo e(__('staticwords.payvia')); ?> Payu</button>
                        <?php echo Form::close(); ?>

                      </div>
                    </div>
                 <?php endif; ?>
                 
                 <?php if(isset($paytm_payment) && $paytm_payment == 1): ?>
                  <div class="col-md-4">
                    <h3 class="heading"><?php echo e(__('staticwords.CheckoutWith')); ?> Paytm</h3>
                    <hr>
                      <div class="paytm">
                     
                        <?php echo Form::open(['method' => 'POST', 'action' => 'PaytemController@store']); ?>

                          <input type="hidden" name="plan_id" value="<?php echo e($plan->id); ?>">
                          <button class="payment-btn paytm-btn"><i class="fa fa-credit-card"></i> <?php echo e(__('staticwords.payvia')); ?> Paytm</button>
                        <?php echo Form::close(); ?>

                      </div>
                  </div>
                 <?php endif; ?>
                
        
                 <div class="col-md-4">
                   <h3 class="heading"><?php echo e(__('staticwords.CheckoutWith')); ?> Razorpay</h3>
                   <hr>
                   <form action="<?php echo e(route('paysuccess',$plan->id)); ?>" method="POST">
                     <script src="https://checkout.razorpay.com/v1/checkout.js"
                                      data-key="<?php echo e(env('RAZOR_PAY_KEY')); ?>"
                                      data-amount="<?php echo e($plan->amount*100); ?>"
                                      data-buttontext="Pay <?php echo e($plan->amount); ?> <?php echo e($plan->currency); ?>"
                                      data-name="<?php echo e(config('app.name')); ?>"
                                      data-description="Payment For Order <?php echo e(uniqid()); ?>"
                                      data-image="<?php echo e(url('images/logo/'.$logo)); ?>"
                                      data-prefill.name="<?php echo e(Auth::user()->name); ?>"
                                      data-prefill.email="<?php echo e(Auth::user()->email); ?>"
                                data-theme.color="#111111">
                      </script>
                      <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                      <input type="hidden" custom="Hidden Element" name="hidden">
                    </form>
                 </div>

               <?php endif; ?>

               <?php if($currency_code == "NGN"): ?>
                  <?php if(isset($paystack) && $paystack == 1): ?> 
                    <h3 class="heading"><?php echo e(__('staticwords.CheckoutWith')); ?> Paystack</h3>
                    <hr>
                    <div class="paystack">
                      <?php
                        $amount = $plan->amount*100;
                      ?>
                      <form method="POST" action="<?php echo e(url('payment/paystack')); ?>" accept-charset="UTF-8" class="form-horizontal" role="form">
                        <input type="hidden" name="email" value="<?php echo e($auth->email); ?>"> 
                        <input type="hidden" name="amount" value="<?php echo e($amount); ?>"> 
                        <input type="hidden" name="currency" value="<?php echo e($plan->currency); ?>"> 
                        <input type="hidden" name="quantity" value="1">
                        <input type="hidden" name="metadata" value="<?php echo e(json_encode($array = ['plan_id' => $plan->plan_id,])); ?>" > 
                        <input type="hidden" name="reference" value="<?php echo e(Paystack::genTranxRef()); ?>">
                        <input type="hidden" name="key" value="<?php echo e(config('paystack.secretKey')); ?>"> 
                        <?php echo e(csrf_field()); ?>

                        <button class="payment-btn paystack-btn"><i class="fa fa-credit-card"></i><?php echo e(__('staticwords.payvia')); ?> Paystack</button>
                      </form>
                    </div>
                  <?php endif; ?>
               <?php endif; ?>

               <?php if(isset($coinpay) && $coinpay==1): ?>
                 <div class="col-md-4">
                    <h3 class="heading"><?php echo e(__('staticwords.CheckoutWith')); ?> CoinPayment</h3>
                    <hr>
                     <div class="coinpayment">
                        <form method="POST" id="coinpayment-form" action="<?php echo e(url('payment/coinpayment')); ?>">
                          <?php echo e(csrf_field()); ?> 
                          <div class="form-group"> 
                            <label for="amount"><?php echo e(__('staticwords.amount')); ?></label>                       
                            <input type="text" class="form-control"name="amount" readonly="" value="<?php echo e($plan->amount); ?>">
                             <label for="amount"><?php echo e(__('staticwords.currency')); ?></label> 
                            <select style="padding: 0px; " class="form-control" name="currency">
                              <option value="BTC">BTC</option>
                               <option value="LTC">LTC</option>
                                <option value="ETH">ETH</option>
                                 <option value="LOKI">LOKI</option>
                                  <option value="XZC">XZC</option>
                            </select>
                               <input type="hidden" name="plan_id" value="<?php echo e($plan->id); ?>"/>
                          </div>
                        
                        
                         
                          <button class="payment-btn" type="submit"><span><?php echo e(__('staticwords.paynow')); ?></span></button>
                        </form>
                      </div> 
                 </div>
                
               <?php endif; ?>

                <?php if(isset($bankdetails) && $bankdetails == 1): ?> 
                  <div class="col-md-12">
                    <h3 class="heading"><?php echo e(__('staticwords.CheckoutWith')); ?> <?php echo e(__('staticwords.BankTransfer')); ?></h3>
                    <hr>
                  <button class="payment-btn" id="bankbutton"><?php echo e(__('staticwords.DirectBankTransfer')); ?></button>
               <div id="bankdetail" style="display: none;">
                <div class="row">
                  <div class="col-md-2">
                    <p style="font-size: 17px;"><?php echo e(__('staticwords.AccountName')); ?> :</p>
                  </div>
                  <div class="col-md-2">
                     <p style="font-size: 16px;"><?php echo e($account_name); ?></p>
                  </div>
                  <div class="col-md-2">
                    <p style="font-size: 17px;"><?php echo e(__('staticwords.accountnumber')); ?> :</p>
                  </div>
                     <div class="col-md-2">
                     <p style="font-size: 16px;"><?php echo e($account_no); ?></p>
                    </div>
                   </div> 
                    <div class="row">
                  <div class="col-md-2">
                    <p style="font-size: 17px;"><?php echo e(__('staticwords.BankName')); ?> :</p>
                  </div>
                     <div class="col-md-2">
                     <p style="font-size: 16px;"><?php echo e($bank); ?></p>
                    </div>
                     <div class="col-md-2">
                    <p style="font-size: 17px;"><?php echo e(__('staticwords.IFSCCode')); ?>:</p>
                  </div>
                     <div class="col-md-2">
                     <p style="font-size: 16px;"><?php echo e($ifsc_code); ?></p>
                    </div>
                   </div> 
                   <div class="col-md-9">
                     <p style="color: #d63031;">* <?php echo e(__('staticwords.BankNote')); ?> <a href="<?php echo e(url('contactus')); ?>" style="color: #00b894;"><?php echo e(__('ContactHere')); ?></a></p>
                     </div>
                   </div>
              </div>
                <?php endif; ?>

            </div>
          </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('custom-script'); ?>

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
    var stripe = Stripe('<?php echo e(env('STRIPE_KEY')); ?>');
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
          
          url: "<?php echo e(url('bttoken')); ?>",
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


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.theme', \Illuminate\Support\Arr::except(get_defined_vars(), array('__data', '__path')))->render(); ?>