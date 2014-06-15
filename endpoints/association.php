<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$association = $app['controllers_factory'];

$association->get('/', function(Request $request) use ($app) {
	$args = array();
	$args['index'] = $request->get('index') != null ? addslashes($request->get('index')) : 0;
	$args['limit'] = $request->get('limit') != null ? addslashes($request->get('limit')) : DEFAULT_MAX_RESULT_SIZE;
	$args['name'] = $request->get('name') != null ? addslashes($request->get('name')) : null;
	$args['removed'] = $request->get('removed') != null ? addslashes($request->get('removed')) : 0;
 	return $app->json(Association::toJson(Association::listAssociations($args)), 200);
});

$association->post('/', function(Request $request) use ($app) {
	$args = array();
	$user = $_SESSION['user'];
	$args['name'] = $request->get('name') != null ? addslashes($request->get('name')) : null;
	$args['description'] = $request->get('description') != null ? addslashes($request->get('description')) : null;
	$args['date_added'] = $request->get('date_added') != null ? addslashes($request->get('date_added')) : null;
	$args['added_by'] = $user->id;
	$association = new Association();
	foreach ($args as $key => $value) {
		$association->$key = $value;
	}
	return $app->json(array('result' => ($association->save() > 0 ? true : false)), 200);
});

$association->get('/{associationid}', function($associationid) use ($app) {
	$foundAssociation = Association::findAssociation(addslashes($associationid));
	if($foundAssociation != null){
		return $app->json(Association::toJson($foundAssociation), 200);
	}
	return $app->json(array('result' => false), 400);
})->assert('associationid', '\d+');

$association->get('/filter', function(Request $request) use ($app) {
	$args = explode(',', $request->get('filters'));
	$filters = array('removed' => 0);
	return $app->json(Association::getFilters($args, $filters), 200);
});

$association->get('/meta/count', function(Request $request) use ($app) {
	$removed = $request->get('removed') != null ? addslashes($request->get('removed')) : 0;
	return $app->json(array('count' => Association::countAssociation($removed)), 200);
});

$association->put('/{associationid}', function(Request $request, $associationid) use ($app) {
	$foundAssociation = Association::findAssociation(addslashes($associationid));
	if($foundAssociation != null){
		$args = array();
		$args['name'] = $request->get('name') != null ? addslashes($request->get('name')) : null;
		$args['description'] = $request->get('description') != null ? addslashes($request->get('description')) : null;
		foreach ($args as $key => $value) {
			$foundAssociation->$key = $value;
		}
		return $app->json(array('result' => ($foundAssociation->update() > 0 ? true : false)), 200);
	}
	return $app->json(array('result' => false), 400);
})->assert('associationid', '\d+');

$association->delete('/{associationid}', function(Request $request, $associationid) use ($app) {
	$foundAssociation = Association::findAssociation(addslashes($associationid));
	if($foundAssociation != null){
		$args = array();
		$user = $_SESSION['user'];
		$args['reason_removed'] = $request->get('reason_removed') != null ? addslashes($request->get('reason_removed')) : null;
		$args['removed_by'] = $user->id;
		foreach ($args as $key => $value) {
			$foundAssociation->$key = $value;
		}
		return $app->json(array('result' => ($foundAssociation->delete() > 0 ? true : false)), 200);
	}
	return $app->json(array('result' => false), 400);
});

return $association;