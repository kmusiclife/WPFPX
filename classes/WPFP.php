<?php

class WPFP
{
    var $uri;
    function __construct() {
        $this->uri = $_SERVER['REQUEST_URI'];
    }
    public function echoJson($variables){
        header("Content-Type: application/json; charset=utf-8");
        die( json_encode($variables) );
    }
    public function sessionStart()
    {
        if(session_status() !== PHP_SESSION_ACTIVE){
            session_name(WPFP_SESSION_SECRET_KEY);
            session_start(array(
                'name' => WPFP_SESSION_SECRET_KEY,
                'cookie_lifetime' => WPFP_SESSION_LIFETIME,
            ));
        }
    }
}