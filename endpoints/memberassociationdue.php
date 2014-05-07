<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$memberAssociationDue = $app['controllers_factory'];

$memberAssociationDue->get('/', function(Request $request) use ($app) {
	$args = array();
	$args['index'] = $request->get('index') != null ? addslashes($request->get('index')) : 0;
	$args['limit'] = $request->get('limit') != null ? addslashes($request->get('limit')) : DEFAULT_MAX_RESULT_SIZE;
	$args['removed'] = $request->get('removed') != null ? addslashes($request->get('removed')) : 0;
	$args['member_id'] = $request->get('member_id') != null ? addslashes($request->get('member_id')) : null;
	$args['association_id'] = $request->get('association_id') != null ? addslashes($request->get('association_id')) : null;
	return $app->json(MemberAssociationDue::toJson(MemberAssociationDue::listMemberAssociationDues($args)), 200);
});

$memberAssociationDue->post('/', function(Request $request) use ($app) {
	$args = array();
	$args['member_id'] = $request->get('member_id') != null ? addslashes($request->get('member_id')) : null;
	$args['association_id'] = $request->get('association_id') != null ? addslashes($request->get('association_id')) : null;
	$args['dues'] = $request->get('dues') != null ? addslashes($request->get('dues')) : null;
	$args['month'] = $request->get('month') != null ? addslashes($request->get('month')) : null;
	$args['added_by'] = $request->get('added_by') != null ? addslashes($request->get('added_by')) : null;
	$args['year'] = $request->get('year') != null ? addslashes($request->get('year')) : null;
	$memberassociationdue = new MemberAssociationDue();
	foreach ($args as $key => $value) {
	 	$memberassociationdue->$key=$value;
	 }
	return $app->json(array('result' => ($memberassociationdue->save() > 0 ? true : false)), 200);
});

$memberAssociationDue->get('/{id}', function($id) use ($app) {
	$found = MemberAssociationDue::findMemberAssociationDue(addslashes($id));
	if($found != null){
		return $app->json(MemberAssociationDue::toJson($found), 200);
	}
	return $app->json(array('result' => false), 400);
})->assert('id', '\d+');

$memberAssociationDue->delete('/{id}', function($id) use ($app) {
	$found = MemberAssociationDue::findMemberAssociationDue(addslashes($id));
	if($found != null){
		$found->removed_by = 0;
		return $app->json(array('result' => ($found->delete() > 0 ? true : false)), 200);
	}
	return $app->json(array('result' => false), 400);
})->assert('id', '\d+');

$memberAssociationDue->get('/meta/count', function (Request $request) use ($app) {
	$args = array();
	$args['removed'] = $request->get('removed') != null ? addslashes($request->get('removed')) : 0;
	$args['member_id'] = $request->get('member_id') != null ? addslashes($request->get('member_id')) : null;
	$args['association_id'] = $request->get('association_id') != null ? addslashes($request->get('association_id')) : null;
	$args['month'] = $request->get('month') != null ? addslashes($request->get('month')) : null;
	$args['year'] = $request->get('year') != null ? addslashes($request->get('year')) : null;
	return $app->json(array('count' => MemberAssociationDue::countMemberAssociationDue($args)), 200);
});

$memberAssociationDue->get('/filter', function(Request $request) use ($app) {
	$args = explode(',', $request->get('filters'));
	$filters = array('removed' => 0);
	return $app->json(MemberAssociationDue::getFilters($args, $filters), 200);
});
return $memberAssociationDue;