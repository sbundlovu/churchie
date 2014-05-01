<?php

require_once __DIR__."/lib/entities.php";

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;

CONST DEFAULT_MAX_RESULT_SIZE = 100;

$app->get('/', function() use($app){
	return "Hello world";
});

$app->mount('/user', include 'user.php');

$app->run();