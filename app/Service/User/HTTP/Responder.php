<?php

declare(strict_types=1);

namespace App\Service\User\Http ;

use Slim\Psr7\Response;

class Responder {

    private static function respond(string $payload, $status=200) {
        $response = new Response();

        $response->getBody()->write($payload);
        return $response 
        ->withStatus($status)
        ->withHeader("Content-Type", "application/json");
    }

    public static function respondSuccess($data, $status=200) {
        $responseData = [];
        $responseData['status'] = "Success";
        $responseData['data'] = $data;
        
        return self::respond(json_encode($responseData), $status);
    }

    public static function respondErr($message, $status) {
        $responseData = [];
        $responseData['status'] = "Error";
        $responseData['message'] = $message;
        
        return self::respond(json_encode($responseData), $status);
    }
}