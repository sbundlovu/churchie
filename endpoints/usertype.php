<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$usertype = $app['controllers_factory'];

$usertype->get('/', function() use ($app){
	return $app->json(UserType::toJson(UserType::listUserType()), 200);
});
return $usertype;