<?php

declare(strict_types=1);

namespace App\Service\_Developer\Auth ;


use App\Service\_Developer\Auth\Repository\AccountRepository;
use App\Http\Session;
use App\Http\Cookie;
use App\Http\Responder ;


class DeveloperAuth {
   private $repository;
   private $isCookieVaild = false ;
   private $defaultLoginPageredirect  = '/developer';
   private const AUTH_INITIATOR = "developer_auth_initiator";
   private const LOGGED_IN_USER = "logged_in_developer";

   public function __construct(){
        $this->repository = new AccountRepository();
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

    public function getLoggedinUserData(){
        if(!$this->getLoggedinUser())
           return null;
           
        $userID = $this->getLoggedinUser();
         return $userData = $this->repository->findById($userID);
    }

    public function unsetLoggedinUser(){
        Session::unset(self::LOGGED_IN_USER);
    }
}