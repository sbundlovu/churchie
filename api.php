<?php

require_once __DIR__."/lib/entities.php";

require_once __DIR__.'/vendor/autoload.php';

error_reporting(E_ALL);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;

CONST DEFAULT_MAX_RESULT_SIZE = 100;

// $app->get("/{customer_id}", function(Request $request, $customer_id) use ($app){
// 	$args = array();
// 	$args['customer_id'] = $customer_id;
// 	$args['index'] = $request->get('index') != null ? $request->get('index') : 0;
// 	$args['limit'] = $request->get('limit') != null ? $request->get('limit') : DEFAULT_MAX_RESULT_SIZE;

// 	return $app->json(
// 		Attendance::toJson(Attendance::listAttendance($args)), 200);
// });

// $app->get("/meta/count", function(Request $request) use ($app){
// 	return $app->json(array("count" => Attendance::countAttendance(1)),200);
// });

$app->get('/user', function(Request $request) use ($app){
	$args = array();
	$args['index'] = $request->get('index') != null ? addslashes($request->get('index')) : 0;
	$args['limit'] = $request->get('limit') != null ? addslashes($request->get('limit')) : DEFAULT_MAX_RESULT_SIZE;
	$args['removed'] = $request->get('removed') != null ? addslashes($request->get('removed')) : 0;
	$args['usertype'] = $request->get('usertype') != null ? addslashes($request->get('usertype')) : "attendant";
	$results = User::toJson(User::listUsers($args));
	return $app->json(User::toJson(User::listUsers($args)), 200);
});

$app->get('/user/meta/count', function(Request $request) use ($app){
	$args['removed'] = $request->get('removed') != null ? addslashes($request->get('removed')) : 0;
	$args['usertype'] = $request->get('usertype') != null ? addslashes($request->get('usertype')) : 'attendant';
	return $app->json(array('count' => User::countUser($args)));
});

$app->run();