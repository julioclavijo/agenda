<?php
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$routes = require __DIR__ . '/../src/routes/routes.php';
$routes($app);

$app->run();
