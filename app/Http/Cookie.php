<?php 

declare(strict_types=1);

namespace App\Http;

class Cookie {

    public static function set($name, $value, $expire){
       
    }

    public static function unset($name){
        setcookie($name, "", time()-3600);
    }


    public static function get($name){
        if( self::$check($name) )
           return $_COOKIE[$name];
        else 
          return null;
    }


    public static function check($name){
        if( !isset($_COOKIE[$name]) || 
             empty($_COOKIE[$name]) )
            return false;
        else 
            return true;
    }
}