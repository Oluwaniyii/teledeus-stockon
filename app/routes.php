<?php 

use Slim\App ;


return function (App $app)
{
    $user_routes = require( __DIR__ . '/./routes/user_routes.php');
    $user_routes($app); 

  
    $oauth_routes = require( __DIR__ . '/./routes/oauth_routes.php');
    $oauth_routes($app); 


    $api_routes = require( __DIR__ . '/./routes/api_routes.php');
    $api_routes($app); 


    //Developer Console
    $developer_route = require( __DIR__ . '/./routes/developer_routes.php');
    $developer_route($app); 
};