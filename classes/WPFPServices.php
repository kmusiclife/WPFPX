<?php

require ABSPATH.'../composer/vendor/autoload.php';
use Kreait\Firebase\Factory;
use Firebase\Auth\Token\Exception\InvalidToken;

class WPFPServices extends WPFP
{    
    var $stripe;
    var $factory;
    var $user;
    var $session_id;
    
    //
    // Service Instances
    //
    public function servicesInit()
    {
        $this->factory = (new Factory)->withServiceAccount(WPFP_FIREBASE_ADMIN_JSON);
        $this->stripe = new \Stripe\StripeClient(WPFP_STRIPE_SECRET);
        $this->user = $this->getFirebaseUser();
        $this->session_id = session_id();
    }
    //
    // ユーザ認証されていないものはトップページへ
    //
    public function requireUserAuth()
    {
        if( !$this->user ) {
            exit( wp_redirect( home_url('/') ) ); 
        }
    }
    //
    // Sessionのユーザ情報取得
    // 
    public function getFirebaseUser($name=null){
        
        if($name){
            $this->user = isset($_SESSION[WPFP_SESSION_SECRET_KEY]) ? $_SESSION[WPFP_SESSION_SECRET_KEY] : null;
            return $this->user[$name];
        } else {
            $this->user = isset($_SESSION[WPFP_SESSION_SECRET_KEY]) ? $_SESSION[WPFP_SESSION_SECRET_KEY] : null;
            return $this->user;
        }
    }
    //
    // Firebase Storageに保存する
    // 
    public function setFirebaseDatabase($params=array(), $document=null){
        
        // reload firebase user
        $user = $this->getFirebaseUser();
        if(!$user or !$params or !$document) return null;
        
        $firestore = $this->factory->createFirestore();
        $db = $firestore->database();
        $snapshot = $db->collection($user['uid'])->document($document)->snapshot();
        if($snapshot){
            $data = $snapshot->data();
            foreach($params as $name=>$value){
                $data[$name] = $value;
            }
            $db->collection($user['uid'])->document($document)->set($data);
        }
        $user[$name] = $value;
        $_SESSION[WPFP_SESSION_SECRET_KEY] = $user;

        return $user;
    }
    //
    // Firebase firestrageからdocument=paymentを読み込みsessionに埋め込む
    // 
    public function loadFirebasePayment($auth_user){

        $user = null;
        $user['uid'] = $auth_user->uid;
        $user['emailVerified'] = $auth_user->emailVerified;
        $user['email'] = $auth_user->email;
        $user['displayName'] = $auth_user->displayName;
        $user['photoUrl'] = $auth_user->photoUrl;
        $user['phoneNumber'] = $auth_user->phoneNumber;

        $firestore = $this->factory->createFirestore();
        $db = $firestore->database();
        $snapshot_payment = $db->collection($auth_user->uid)->document('payment')->snapshot();
        if($snapshot_payment){
            $payment_array = $snapshot_payment->data();
            $payment_params = array(
                'stripe_customer_id', 
                'stripe_token_id',
                'stripe_subscription_id'
            );
            foreach($payment_params as $payment_param){
                $user[$payment_param] = isset($payment_array[$payment_param]) ? $payment_array[$payment_param] : null;
            }
            $_SESSION[WPFP_SESSION_SECRET_KEY] = $user;
        }
        return $user;

    }
}