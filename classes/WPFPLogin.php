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
            $this->fireplateLoginVerifyIdToken('/login?success');
        }
        if( preg_match('/^\/login\//', $this->uri) ){
            add_action( 'template_redirect', function(){
                die( include(WPFP_DIR.'/login/index.php') );
            } );
        }
    }
    private function fireplateLoginVerifyIdToken($successRedirectUrl='/login')
    {

        $_headers = getallheaders();
        if(!isset($_headers['Authorization'])){
            $this->echoJson( array(
                'success' => false,
                'message' => 'Fatal Error',
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
        
        $this->echoJson(array(
            'success' => true,
            'message' => null,
            'user' => $loaded_user,
            'redirect_url' => $successRedirectUrl
        ));

    }


}