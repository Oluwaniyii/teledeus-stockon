<?php 

declare(strict_types=1);

require __DIR__ . '/../bootstrap/index.php';

// nobody said we can't directly put headers :)

//############################/

// This is important and should be open to registered apps only
// $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Origin', $origin);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: GET');

header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type, Accept, Origin, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

// // ->withAddedHeader('Cache-Control', 'post-check=0, pre-check=0')
header('Pragma: no-cache');


