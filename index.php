<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

// Settings
$settings = require 'app/settings.php';

// Instance
$app = new \Slim\App($settings);

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