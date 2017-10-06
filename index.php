<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

// Environment variables
$settings = require 'app/.env';

// Session
session_start();

// Settings
$settings = require 'app/settings.php';

// Instance
$app = new \Slim\App($settings);

// Session expiration middleware
$app->add(function (Request $request, Response $response, $next){
    if($_SESSION['IS_LOGGED'] == true && time() - $_SESSION['ACTIVE'] < getenv('SESSION_EXPIRATION')){
        $_SESSION['ACTIVE'] = time();
    }else{
        $_SESSION['IS_LOGGED'] = false;
        session_unset();
    }
    $response = $next($request, $response);
    return $response;
});

// Redirect paths with a trailing slash to their non-trailing counterpart
$app->add(function (Request $request, Response $response, callable $next) {
    $uri = $request->getUri();
    $path = $uri->getPath();
    if ($path != '/' && substr($path, -1) == '/') {
        $uri = $uri->withPath(substr($path, 0, -1));
        
        if($request->getMethod() == 'GET') {
            return $response->withRedirect((string)$uri, 301);
        }
        else {
            return $next($request->withUri($uri), $response);
        }
    }

    return $next($request, $response);
});

// Dependencies
require 'app/dependencies.php';

// ROUTES
require 'app/routes.php';

$app->run();
