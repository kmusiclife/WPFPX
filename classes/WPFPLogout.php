<?php

class WPFPLogout extends WPFPRouter
{
    function init(){}
    function execute()
    {
        $_SESSION[WPFP_SESSION_SECRET_KEY] = null;
        session_destroy();
        exit( wp_redirect( home_url('/') ) ); 
    }
}