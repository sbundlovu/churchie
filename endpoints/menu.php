<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

session_start();

$menu = $app['controllers_factory'];

$menu->get('/', function() use ($app) {
	if(!isLogin()){
		return $app->json(array('message' => "No user is logged in"), 400);
	}
	$user = $_SESSION['user'];
	return $app->json(
		array(
			'result' => MenuItem::toJson(MenuItem::listMenu($user->usertype))), 200);
});
return $menu;