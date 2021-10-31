<?php

declare(strict_types=1);

use App\Http\Responder ;
use App\Library\Validator ;

if(!user.exist()) {
    respondWithError(404, "User Not Found");
}
else {
    if(!updateUser($userData)) {
        respondWithError(500, "Could not Update user at the moment");
    }
    else {
        respondWithSuccess(200,  "User Updates");
    }
};


// Validaator
Validator::validate($request->requestBody(), [
  "name" => [
      "name" => "Username",
      "required" => true,
      "max-length" => 21,
      "min-length" => 6,
  ],
  "password" => [
     "name" => "Password",
      "required" => true,
      "match-case" => '/[a-zA-Z\W]+/',
      "min-length" => 6,
   ],
  ]);


  function updateUser($userData){
      if((!Validator::validate($userData))->status()){
           return Validator;
      }
      return true ;
  }

  UserController::getUser() ;

//   getUsers($request, $response) {
//       // Grab necessary Data : id
//       // Include Domain with Data : id
//       // get response 
//       // return response
//   }

//   public function getUser(Request $request, Response $response, $id=null) {
//              return App\Service\User\Domain\getUser($request, $response) }