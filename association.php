<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$association = $app['controllers_factory'];

$association->get('/', function() use ($app) {
	return "Hello world";
});

$association->post('/', function(Request $request) use ($app) {
	$args = array();
	$args['name'] = $request->get('name') != null ? addslashes($request->get('name')) : null;
	$args['description'] = $request->get('description') != null ? addslashes($request->get('description')) : null;
	$args['date_added'] = $request->get('date_added') != null ? addslashes($request->get('date_added')) : null;
	$args['added_by'] = $request->get('added_by') != null ? addslashes($request->get('added_by')) : null;
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
		$args['reason_removed'] = $request->get('reason_removed') != null ? addslashes($request->get('reason_removed')) : null;
		$args['removed_by'] = $request->get('removed_by') != null ? addslashes($request->get('removed_by')) : 0;
		foreach ($args as $key => $value) {
			$foundAssociation->$key = $value;
		}
		return $app->json(array('result' => ($foundAssociation->delete() > 0 ? true : false)), 200);
	}
	return $app->json(array('result' => false), 400);
});

return $association;