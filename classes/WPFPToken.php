<?php

class WPFPToken extends WPFP
{

    var $request_token;

    public function init(){
    }
    public function execute(){
        if( preg_match('/\/token\//', $this->uri) ){
            $this->generateToken();
            $this->echoJson(array(
                'request_token' => $this->getToken()
            ));
        }
    }
    public function generateToken(){
        $this->request_token = sha1(uniqid());
        $_SESSION[WPFP_SESSION_SECRET_KEY.'S'] = $this->request_token;
        return $this->request_token;
    }
    public function getToken(){
        if(!isset($_SESSION[WPFP_SESSION_SECRET_KEY.'S'])) return null;
        return $_SESSION[WPFP_SESSION_SECRET_KEY.'S'];
    }
    public function clearToken(){
        $this->request_token = null;
        $_SESSION[WPFP_SESSION_SECRET_KEY.'S'] = null;
    }

}