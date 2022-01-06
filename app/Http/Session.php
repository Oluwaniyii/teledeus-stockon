<?php 

declare(strict_types=1);

namespace App\Http ;

class Session {
    private static $variables;
    private static $message; // Flash Message
    private const ALLOWED_SESSION_INDEXES = [];
    private const ALLOWED_SESSION_VARIABLES_INDEXES = [];

    public static function init(){
        if(!isset($_SESSION))
            session_start(); // Start session

        if( !isset($_SESSION['variables']) ) 
            $_SESSION['variables'] = [];
        
        self::load(); // Load common set sessions
    }

    public static function set($namedIndex, $value=null){
        if($value)
            $_SESSION['variables'][$namedIndex] =  $value;

        self::load(); 
    }

    public static function unset($namedIndex){
        if(self::check($namedIndex))
            unset( $_SESSION['variables'][$namedIndex] );

        self::load(); 
    }

    public static function get($namedIndex){
        self::load();

        if(self::check($namedIndex))
            return ( self::$variables[$namedIndex] );
    }


    public static function check($namedIndex){
        self::load();

        if( !isset(self::$variables[$namedIndex]) || 
             empty(self::$variables[$namedIndex]) )
            return false;
        else 
            return true;
    }


    public static function flash(){
        // if flash is empty assign else return
        if(self::check("flash_message")){
            return self::get('flash_message');
        }
        else{
            self::set('flash_message');
        }
    }

    private static function load(){
        self::$variables = $_SESSION['variables'];
    } 
}