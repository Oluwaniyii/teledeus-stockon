<?php

declare(strict_types=1);

namespace App\Http ;

use Slim\Psr7\Response;
use Twig\Loader\FilesystemLoader as TwigLoader;
use Twig\Environment as TwigEnv;

class Responder {

 
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

    public static function view($template, array $data=[], $status=200){
        $output = self::loadView($template, $data);

        $response = new Response();
        $response->getBody()->write($output);
        return $response 
        ->withStatus($status)
        ->withHeader('Content-Type', 'text/html');
    }

    public static function redirect($location, $status=302){
        $response = new Response();

        return $response->withHeader("Location", "$location")->withStatus($status);
    }


    // Abstractions

    public static function respond(string $payload, $status=200) {
        $response = new Response();

        $response->getBody()->write($payload);
        return $response 
        ->withStatus($status)
        ->withHeader("Content-Type", "application/json");
    }


    private static function loadView($template, array $data=[]) {
        // Twig
        $viewsFolder = __DIR__ . '/../../views';
        $cacheFolder = __DIR__ . '/../../cache';
  
        $loader = new TwigLoader($viewsFolder);
        $twig = new TwigEnv($loader, ['cache'=>false]);
  
        return $twig->render($template, $data);
      }
  
}