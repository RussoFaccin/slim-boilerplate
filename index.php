<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

// UTILS - FUNCTIONS
require __DIR__ .'/app/utils-functions.php';

// Environment variables
$settings = require 'app/.env';

// Session
session_start();

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

// Protected Routes - Middleware
$app->add(function (Request $request, Response $response, callable $next){
	$route = $request->getAttribute('route');
	$name = $route->getName();

	if(in_array($name, $this->protectedRoutes)){
		// Check user authentication
		if (!isset($_SESSION['USERNAME']) OR time() - $_SESSION['LAST_ACTIVITY'] > getenv('SESSION_EXPIRATION')) {
			$_SESSION['USERNAME'] = NULL;
			$path = $this->router->pathFor('login');
			return $response->withStatus(401)->withHeader('Location', "$path");
		}else{
			$_SESSION['LAST_ACTIVITY'] = time();
		}
	}
	return $next($request, $response);
});

// Dependencies
require 'app/dependencies.php';

// ROUTES
require 'app/routes.php';

$app->run();
