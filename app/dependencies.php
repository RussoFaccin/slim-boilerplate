<?php
// DIC configuration
$container = $app->getContainer();

// Flash Message
$container['flash_message'] = new FlashMessage();

// PDO
$container['db'] = function ($c) {
	$db = $c['settings']['db'];
	try{
		$dbh = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'] . ";charset=UTF8", $db['user'], $db['pass']);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
	}
	catch(PDOException $e) {
		echo $e->getMessage();
	}
    return $dbh;
};

//Override the default Not Found Handler
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['view']->render($response->withStatus(404), 'notfound.html');
    };
};

// Twig-View
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig(['app/views', 'app/views/admin'], ['cache' => false]);
    $basePath = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($container['router'], $basePath));

    return $view;
};

// Protected Routes
$container['protectedRoutes'] = array(
	'admin-home',
	'admin-portfolio'
);
