<?php

require_once __DIR__."/lib/entities.php";

require_once __DIR__.'/vendor/autoload.php';

error_reporting(E_ALL);

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app['debug'] = true;

$app->get("/{customer_id}", function(Request $request, $customer_id) use ($app){
	$args = array();
	$args['customer_id'] = $customer_id;
	$args['index'] = $request->get('index') != null ? $request->get('index') : null;
	$args['limit'] = $request->get('limit') != null ? $request->get('limit') : null;

	return $app->json(
		Attendance::toJson(Attendance::listAttendance($args)), 200);
});

$app->get("/meta/count", function(Request $request) use ($app){
	return $app->json(array("count" => Attendance::countAttendance(1)),200);
});

$app->run();