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

$app->mount('/users', include 'endpoints/user.php');

$app->mount('/members', include 'endpoints/member.php');

$app->mount('/associations', include 'endpoints/association.php');

$app->mount('/association_dues', include 'endpoints/associationdue.php');

$app->mount('/member_associations', include 'endpoints/memberassociation.php');

$app->mount('/member_association_dues', include 'endpoints/memberassociationdue.php');

$app->mount('/menus', include 'endpoints/menu.php');

$app->mount('/usertypes', include 'endpoints/usertype.php');

$app->run();

function isLogin(){
	$user = $_SESSION['user'];
	return ($user != null);
}