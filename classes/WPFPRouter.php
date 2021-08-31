<?php

class WPFPRouter extends WPFPServices
{
    var $controller;

    function init(){

        $uri = $_SERVER['REQUEST_URI'];

        if( preg_match('/^\/token\//', $uri) ){
            $this->controller = new WPFPToken();
        }
        if( preg_match('/^\/login\//', $uri) ){
            $this->controller = new WPFPLogin();
        }
        if( preg_match('/^\/logout\//', $uri) ){
            $this->controller = new WPFPLogout();
        }
        if( preg_match('/^\/payment\//', $uri) ){
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