<?php 

declare(strict_types=1);

namespace App ;

use Firebase\JWT\JWT as FirebaseJWT;

class JWT
{
    private static $secretKey = "Teledeus_Jwt_Secret_key";
    private static $hash = 'HS512' ;
    private static $iss = 'https://tldstokon.herokuapp.com/' ;
    private static $aud = 'https://tldstokon.herokuapp.com/';
    private static $iat ;
    private static $nbf ;
    private static $exp ;

    public static function generateToken($data=[], $time=null)
    {
      $payload = [];
      $payload['iat'] = time();
      $payload['iss'] = self::$iss;
      $payload['nbf'] = time();
      $payload['exp'] = time() + ( $time ? $time :  (60*60*12) );

      # Extract Data
      foreach ( $data as $key => $value ) {
          $payload["$key"] = $value ;
      }

       try {
            $jwt = FirebaseJWT::encode( $payload, self::$secretKey,  self::$hash ); 
            FirebaseJWT::$leeway = 60;
            return array( 'status' => true, 'token' => $jwt);
        } catch (\Exception $ex) {
            return array('status' => false, 'message' => $ex->getMessage() );
        }
    }


    public static function verifyToken($token = "") {
        try {
            $decoded = FirebaseJWT::decode($token, self::$secretKey, array('HS512'));
            $decoded_array = (array) $decoded;

            extract($decoded_array);

            if( $iss !== self::$iss ||
                $nbf > time() ||
                $exp < self::$exp ) {
               return false ;
            } 
            
            return true ;

        } catch(\Exception $ex) {
           return false ;
        }
    }

    // Verifies token alongside specified payload
    public static function verifyTokenPayload($token = "", $payload = []) { 
        try {
            $decoded = FirebaseJWT::decode($token, self::$secretKey, array('HS512'));
            $decoded_array = (array) $decoded;

            extract($decoded_array);

            if( $iss !== self::$iss ||
                $nbf > time() ||
                $exp < self::$exp ) {
                 return false ;
            } 

            $payloadIsValid  = true ;

            foreach ($payload as $key => $value) {
                 if(isset($decoded_array[$key])){
                     if( $decoded_array[$key] !== $value ) {
                        $payloadIsValid  = false ;
                     }
                 }
            }

          if(!$payloadIsValid) return false ;

         return true ;

        } catch(\Exception $ex) {
           return false ;
        }
    }

}