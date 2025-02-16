<?php
use Slim\Factory\AppFactory;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
# $app->setBasePath($_ENV['BASE_PATH']);
$app->addBodyParsingMiddleware();


$routes = require __DIR__ . '/../src/routes/routes.php';
$routes($app);

$app->run();
