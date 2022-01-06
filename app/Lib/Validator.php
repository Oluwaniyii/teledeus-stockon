<?php 

declare(strict_types=1);

namespace App\Lib;

class Validator {
   private static $status ;
   private static $errorMessages = [];
   private static $isValidated;

   public static function validate( array $requestInput, array $schemas) {

        self::$isValidated = true;
        self::$errorMessages = [];

       foreach($schemas as $inputkey =>  $inputSchema) {
           $inputValue = $requestInput[$inputkey] ;
           $inputName = $inputSchema['name'];
           $errorOutcome = false ;

           foreach($inputSchema as $condition => $conditionValue) {
               $errorOutcome = self::check($inputName, $inputValue, $condition, $conditionValue) ;
                   // If a certain input-value hits an error. I want you to leave the input and move to the next input; to aid faster execution;
                   //    if($errorOutcome) continue ;
           }
       }

   }

   private static function check($inputName, $inputValue, $condition, $conditionValue) {
       $errorOutcome = false;
       switch($condition) 
       {
           case "required":
            #code...
            if($conditionValue === true && $inputValue === "") {
                 self::addError("$inputName is required");
                 $errorOutcome = true;
            }
            break;

           case "min":
             if(strlen($inputValue) < $conditionValue){
                self::addError("$inputName should be a minimum of $conditionValue characters");
                $errorOutcome = true;
             }
            break;

            case "max":
                if(strlen($inputValue) > $conditionValue){
                   self::addError("$inputName should be a maximum of $conditionValue characters");
                   $errorOutcome = true;
                }
               break;

               case "regex":
               if(!preg_match($conditionValue, $inputValue)){
                   self::addError("invalid Character in $inputName");
                   $errorOutcome = true;
               }
               break;

                break;
       }

       return $errorOutcome ;
    }


    private static function addError($errorMessage) {
       self::$errorMessages[] = $errorMessage;
       self::$isValidated = false;
    }

    public static function validated() { 
        return self::$isValidated;
    } 

 
    public static function errors() {
       return self::$errorMessages;
   } 
}