<?php

class WPFPLogin extends WPFPRouter
{
    
    public function init()
    {
        $this->servicesInit();
        // JS読み込み
        wp_enqueue_script( 
            'firebase-login-script', 
            WPFP_URI.'/dist/js/login.js',
            null, null, true
        );
        // Style読み込み
        wp_enqueue_style(
            'firebase-login-style', 
            WPFP_URI.'/dist/css/login.css'
        );
    }
    public function execute()
    {
        if( preg_match('/^\/login\/_____firebase_____verifyIdToken/', $this->uri) ){
            $this->loginVerifyIdToken(WPFP_LOGIN_SUCCESS_REDIRECT_URI);
        }
        if( preg_match('/^\/login\//', $this->uri) ){
            add_action( 'template_redirect', function(){
                die( include(WPFP_DIR.'/login/index.php') );
            } );
        }
    }
    private function loginVerifyIdToken($successRedirectUrl='/login')
    {  
        $request_token = isset($_GET['request_token']) ? htmlspecialchars($_GET['request_token']) : null;
        if(!$this->getToken() or !$request_token){
            $this->echoJson( array(
                'success' => false,
                'message' => 'No Access Token',
                'user' => null,
                'redirect_url' => home_url('/error/login?code=NoAccessToken')
            ));            
        }
        if($this->getToken() != $request_token){
            $this->echoJson( array(
                'success' => false,
                'message' => 'Tokens Mismatch',
                'user' => null,
                'redirect_url' => home_url('/error/login?code=TokenError')
            ));            
        }
        $_headers = getallheaders();
        if(!isset($_headers['Authorization'])){
            $this->echoJson( array(
                'success' => false,
                'message' => 'Authentication Information does not exist',
                'user' => null,
                'redirect_url' => home_url('/error/login?code=AuthorizationNone')
            ));
        };
        $auth = $this->factory->createAuth();
        list(,$idToken) = explode(' ', $_headers['Authorization']);
        
        try {
            $verifiedIdToken = $auth->verifyIdToken($idToken);
        } catch (InvalidToken $e) {
            $this->echoJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'user' => null,
                'redirect_url' => home_url('/error/login?code=TokenIsInvalid')
            ));
        } catch (\InvalidArgumentException $e) {
            $this->echoJson(array(
                'success' => false,
                'message' => $e->getMessage(),
                'user' => null,
                'redirect_url' => home_url('/error/login?code=TokenCouldNotBeParsed')
            ));
        }

        $uid = $verifiedIdToken->claims()->get('sub');
        $loaded_user = $this->loadFirebasePayment( $auth->getUser($uid) ); // payment parameter loading
        
        $this->clearToken();
        $this->echoJson(array(
            'success' => true,
            'message' => null,
            'user' => $loaded_user,
            'redirect_url' => $successRedirectUrl
        ));

    }


}