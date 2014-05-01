<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$user = $app['controllers_factory'];

$user->get('/', function(Request $request) use ($app){
	$args = array();
	$args['index'] = $request->get('index') != null ? addslashes($request->get('index')) : 0;
	$args['limit'] = $request->get('limit') != null ? addslashes($request->get('limit')) : DEFAULT_MAX_RESULT_SIZE;
	$args['removed'] = $request->get('removed') != null ? addslashes($request->get('removed')) : 0;
	$args['usertype'] = $request->get('usertype') != null ? addslashes($request->get('usertype')) : "attendant";
	$results = User::toJson(User::listUsers($args));
	return $app->json(User::toJson(User::listUsers($args)), 200);
});

$user->get('/meta/count', function(Request $request) use ($app){
	$args = array();
	$args['removed'] = $request->get('removed') != null ? addslashes($request->get('removed')) : 0;
	$args['usertype'] = $request->get('usertype') != null ? addslashes($request->get('usertype')) : 'attendant';
	return $app->json(array('count' => User::countUser($args)));
});

$user->get('/filter', function(Request $request) use ($app){
	$args = explode(',', $request->get('filters'));
	$filters = array('removed' => 0, 'usertype' => "'attendant'");
	return $app->json(User::getFilters($args, $filters));
});

return $user;