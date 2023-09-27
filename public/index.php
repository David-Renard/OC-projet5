<?php

declare(strict_types=1);
require_once  '../vendor/autoload.php';

use App\Service\Http\Request;
use App\Service\Router;
use Symfony\Component\Dotenv\Dotenv;
use App\Service\Environment;

$dotenv = new Dotenv();
$getEnv = new Environment($dotenv);

$env = $getEnv->getEnv('APP_ENV');
if ($env === 'DEV')
{
    $whoops = new \Whoops\Run();
    $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
    $whoops->register();
}

$request = new Request($_GET,$_POST,$_FILES,$_SERVER);
$router = new Router($request);
$response=$router->run();
$response->send();
