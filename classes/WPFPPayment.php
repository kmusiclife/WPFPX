<?php

class WPFPPayment extends WPFPRouter
{

    var $products;

    public function init(){

        $this->servicesInit();
        $this->requireUserAuth();
        
        // JS読み込み
        wp_enqueue_script( 
            'firebase-login-script', 
            WPFP_URI.'/dist/js/payment.js',
            null, null, true
        );
    }
    public function execute()
    {

        if( preg_match('/^\/payment\/_____stripe_____createSubscription\//', $this->uri) ){
            $this->paymentCreateSubscription('/?success');
            die();
        }
        if( preg_match('/^\/payment\//', $this->uri) ){
            $this->paymentIndex();
        }

    }
    private function paymentIndex(){
        
        if($this->user == null) exit( wp_redirect( home_url('/login') ) ); 
        $stripe_products = $this->stripe->products->all();
        $this->products = array();

        foreach($stripe_products as $stripe_product){
            
            $stripe_prices = $this->stripe->prices->all(['product' => $stripe_product]);
            if(isset($stripe_prices->data)){
                array_push($this->products, array(
                    'product' => $stripe_product,
                    'prices' => $stripe_prices->data
                ) );
            }
        }
        add_action( 'template_redirect', function(){
            die( include(WPFP_DIR.'/payment/index.php') );
        } );
    }
    private function paymentCreateSubscription($successRedirectUrl='/'){

        $params = json_decode(file_get_contents('php://input'), true);
        
        if( !$this->user ){
            $this->echoJson(array(
                'success' => false,
                'redirect_url' => '/error/500?code=Authentication'
            ));
        }

        $price_id = isset($params['priceId']) ? $params['priceId'] : null;
        $stripe_token_id = isset($params['token']['id']) ? $params['token']['id'] : null;
        $stripe_customer_id = isset($this->user['stripe_customer_id']) ? $this->user['stripe_customer_id'] : null;

        if(!$price_id){
            $this->echoJson(array(
                'success' => false,
                'redirect_url' => '/error/500?code=NoPriceId'
            ));
        }
        if(!$stripe_token_id){
            $this->echoJson(array(
                'success' => false,
                'redirect_url' => '/error/500?code=NoTokenId'
            ));
        }
        if(!$stripe_customer_id){
            $this->echoJson(array(
                'success' => false,
                'redirect_url' => '/error/500?code=NoCustomerId'
            ));
        }

        $subscription = $this->stripe->subscriptions->create([
            'customer' => $stripe_customer_id,
            'items' => [[
                'price' => $price_id,
            ]],
            'payment_behavior' => 'default_incomplete',
            'expand' => ['latest_invoice.payment_intent'],
        ]);
        $stripe_subscription_id = isset($subscription->id) ? $subscription->id : null;
        if(!$stripe_subscription_id){
            $this->echoJson(array(
                'success' => false,
                'redirect_url' => '/error/500?code=NoSubscriptionId'
            ));
        }

        $client_secret = $subscription->latest_invoice->payment_intent->client_secret;
        $payment_params = array(
            'stripe_subscription_id' => $stripe_subscription_id, 
            'stripe_token_id' => $stripe_token_id
        );
        $this->setFirebaseDatabase($payment_params, 'payment');

        // Success to subscription
        $this->echoJson(array(
            'success' => true,
            'client_secret' => $client_secret,
            'redirect_url' => $successRedirectUrl
        ));

    }

}