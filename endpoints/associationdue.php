<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$associationDue = $app['controllers_factory'];

$associationDue->get('/', function(Request $request) use ($app) {
	$args = array();
	$args['removed'] = $request->get('removed') != null ? addslashes($request->get('removed')) : 0;
	$args['index'] = $request->get('index') != null ? addslashes($request->get('index')) : 0;
	$args['limit'] = $request->get('limit') != null ? addslashes($request->get('limit')) : DEFAULT_MAX_RESULT_SIZE;
	return $app->json(AssociationDue::toJson(AssociationDue::listAssociationDues($args)), 200);
});

$associationDue->post('/', function(Request $request) use ($app) {
	$args = array();
	$args['association_id'] = $request->get('association_id') != null ? addslashes($request->get('association_id')) : null;
	$args['dues'] = $request->get('dues') != null ? addslashes($request->get('dues')) : null;
	$args['added_by'] = $request->get('added_by') != null ? addslashes($request->get('added_by')) : null;
	$association_due = new AssociationDue();
	foreach ($args as $key => $value) {
		$association_due->$key = $value;
	}
	return $app->json(array('result' => ($association_due->save() > 0 ? true : false)), 200);
});

$associationDue->get('/{associationDueId}', function($associationDueId) use ($app) {
	$association_due = AssociationDue::findAssociationDue(addslashes($associationDueId));
	if($association_due != null){
		return $app->json(AssociationDue::toJson($association_due), 200);
	}
	return $app->json(array('result' => false), 400);
});

$associationDue->put('/{associationDueId}', function(Request $request, $associationDueId) use ($app) {
	$association_due = AssociationDue::findAssociationDue(addslashes($associationDueId));
	if($association_due != null){
		$association_due->dues = $request->get('dues') != null ? addslashes($request->get('dues')) : $association_due->dues;
		return $app->json(array('result' => ($association_due->update() > 0 ? true : false)), 200);
	}
	return $app->json(array('result' => false), 400);
});

$associationDue->delete('/{associationDueId}', function(Request $request, $associationDueId) use ($app) {
	$association_due = AssociationDue::findAssociationDue(addslashes($associationDueId));
	if($association_due != null){
		$args = array();
		$association_due->removed_by = $request->get('removed_by') != null ? addslashes($request->get('removed_by')) : 0;
		return $app->json(array('result' => ($association_due->delete() > 0 ? true : false)), 200);
	}
	return $app->json(array('result' => false), 400);
});

$associationDue->get('/meta/count', function(Request $request) use ($app) {
	$removed = $request->get('removed') != null ? addslashes($request->get('removed')) : 0;
	return $app->json(array('count' => AssociationDue::countAssociationDue($removed)), 200);
});

return $associationDue;