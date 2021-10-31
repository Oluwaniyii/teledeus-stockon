<?php 

use Psr\Http\Message\ResponseInterface as Response ;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

if(!function_exists('base_path')) {
    function base_path($path="")
    {
        # code...
        return __DIR__ . "/../{$path}";
    }
}

if(!function_exists('routes_path')) {
    function routes_path($path="")
    {
        # code...
        return base_path("routes/{$path}");
    }
}