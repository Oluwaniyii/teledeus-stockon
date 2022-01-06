<?php

declare(strict_types=1);

namespace App\Service\Auth;

use App\Http\Session;
use App\Http\Cookie;
use App\Service\User\Repository\User as UserRepository;
use App\Service\User\Repository\UserAuthToken as UserAuthTokenRepository;
use App\Http\Responder ;


/**
 * This is the class to handle all authenticated user related tasks like :
 * Setting user session,
 * Pulling logged in user session details
 * Handling user cookies and all
 * Redirecting after login attempt
 *  DO NOT confuse this with Login class, Login class is only to verify details for authentication
 *  Yeah I agree my OOP skills is on development,, Now Leave me! lol.
 *
 */

class Auth {
   private $UserAuthTokenRepository;
   private $isCookieVaild = false ;
   private $defaultLoginPageredirect  = '/';
   private const AUTH_INITIATOR = "auth_initiator";
   private const LOGGED_IN_USER = "logged_in_user";
   private const COOKIE_MEMBER_LOGIN = "member_login";
   private const COOKIE_RANDOM_PASSWORD = "random_password";
   private const COOKIE_RANDOM_SELECTOR = "random_selector";
   

   public function __construct(){
        $this->UserAuthTokenRepository = new UserAuthTokenRepository();
        $this->UserRepository = new UserRepository();
        Session::init();
   }

   /**
    * @desc Allows a page to request login if user is not logged in
    * @param string referer @desc specifies the path to be redirected to after successful login i.e patg of the requireLogin caller
    */

   public function requireLogin($referer = null){
      // Store referer
      if(is_null($referer))
          $referer = $this->defaultLoginPageredirect;

      $referer = urlencode($referer);
      Session::set(self::AUTH_INITIATOR, $referer);

      // redirect to login auth page
      return Responder::redirect($referer);
   }

    public function isUserLoggedIn(): bool {
        return session::check(self::LOGGED_IN_USER);
    }

    public function setLoggedinUser($memberLogin){
        Session::set(self::LOGGED_IN_USER, $memberLogin);
    }

    public function getLoggedinUser(){
        if($this->isUserLoggedIn()){
            return Session::get(self::LOGGED_IN_USER);
        }
        else 
            return null ;
    }

    public function getLoggedInUserData($email){
        $email = $email ? $email : $this->getLoggedinUser();
        return $this->userRepository->findByEmail($email);
    }


    // User
   public function deleteUserAuthCookies(){}
   
   public function setNewUserCookies($userEmail){}


    public function isValidCookie(): bool {
       return $this->isCookieValid;
    }
   
    public function checkUserBrowserCookies(){
         $status = true ;
        // Auth Cookie
        if ( !Cookie::check(self::COOKIE_MEMBER_LOGIN) ) $status = false ;
        if ( !Cookie::check(self::COOKIE_RANDOM_PASSWORD) ) $status = false ;
        if ( !Cookie::check(self::COOKIE_RANDOM_SELECTOR) ) $status = false ;

        return $status;
    } 

    public function getUserBrowserCookies(){
        $browser_auth_cookie = [] ;

        $browser_auth_cookie[self::COOKIE_MEMBER_LOGIN] = Cookie::get(self::COOKIE_MEMBER_LOGIN);
        $browser_auth_cookie[self::COOKIE_RANDOM_PASSWORD] = Cookie::get(self::COOKIE_RANDOM_PASSWORD);
        $browser_auth_cookie[self::COOKIE_RANDOM_SELECTOR] = Cookie::get(self::COOKIE_RANDOM_SELECTOR);

        return $browser_auth_cookie;
    }


    public function setNewUserBrowserCookies($userEmail){
        // use userId to set new user cookie
    }

    public function checkUserDbCookies($userEmail){
        if ( empty ($this->UserAuthTokenRepository->findCookies($userEmail)) )
           return false;
        else 
           return true;
    }

    public function getUserDbCookies($userEmail){
        return $this->UserAuthTokenRepository->findCookies( $userEmail ) ;
    }

    public function setNewUserDbCookies($userEmail){}
    
 
    public function unsetLoggedinUser(){
        Session::unset(self::LOGGED_IN_USER);
    }

    public function validateUserCookie(){
        if ($this->validateCookies()){
            // set userLoggedIn
            $memberLogin =  Cookie::get(self::COOKIE_MEMBER_LOGIN);
            $this->setLoggedinUser($memberLogin);
            // set new cookie
            $this->setNewUserCookies($memberLogin);
        }

        return $this->isCookieVaild;
    }

    private function validateCookies(){
        $current_time = time();
        $current_date = date("Y-m-d H:i:s", $current_time);

         // check browser cookie
        if( !$this->checkUserBrowserCookies() ) return ;

        // get db cookie with browser member login
        $browser_cookie = $this->getUserBrowserCookies();
        $userEmail = $browser_cookie[self::COOKIE_MEMBER_LOGIN] ;

        if( !$this->checkUserDbCookies($userEmail) ) return ;

        $db_cookie = $this->getUserDbCookies($userEmail);

        //  validate database cookie expiry
        if($db_cookie['expiry'] < $current_date) // cookie has expired
            return;

        // compare cookies match
        if (!password_verify($browser_cookie[self::COOKIE_RANDOM_PASSWORD], $db_cookie['password'])) 
            return;

        if (!password_verify($browser_cookie[self::COOKIE_RANDOM_PASSWORD], $db_cookie['selector'])) 
           return;

        // All tests passed
        $this->isCookieValid = true;
        return $this->isCookieValid;
    }
    
}