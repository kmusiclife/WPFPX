<?php

class WPFPRouter extends WPFPServices
{
    var $uri;
    var $controller;

    function __construct()
    {
        $this->uri = $_SERVER['REQUEST_URI'];
    }
    function init(){

        if( preg_match('/^\/login\//', $this->uri) ){
            $this->controller = new WPFPLogin();
        }
        if( preg_match('/^\/logout\//', $this->uri) ){
            $this->controller = new WPFPLogout();
        }
        if( preg_match('/^\/payment\//', $this->uri) ){
            $this->controller = new WPFPPayment();
        }
        if($this->controller){
            $this->sessionStart();
            $this->controller->init();
        }
    }
    function execute(){
        if($this->controller){
            $this->controller->execute();
        }
    }
}