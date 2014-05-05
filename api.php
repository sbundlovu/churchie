<?php

require_once __DIR__."/lib/entities.php";

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;

session_start();

CONST DEFAULT_MAX_RESULT_SIZE = 100;

$app->get('/', function() use($app){
	return "Hello world";
});

$app->mount('/user', include 'endpoints/user.php');

$app->mount('/member', include 'endpoints/member.php');

$app->mount('/association', include 'endpoints/association.php');

$app->mount('/association_due', include 'endpoints/associationdue.php');

$app->mount('/member_association', include 'endpoints/memberassociation.php');

$app->run();