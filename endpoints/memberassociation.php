<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$memberAssociation = $app['controllers_factory'];

$memberAssociation->get('/', function(Request $request) use ($app) {
	$args = array();
	$args['index'] = $request->get('index') != null ? addslashes($request->get('index')) : 0;
	$args['limit'] = $request->get('limit') != null ? addslashes($request->get('limit')) : DEFAULT_MAX_RESULT_SIZE;
	$args['removed'] = $request->get('removed') != null ? addslashes($request->get('removed')) : 0;
	$args['member_id'] = $request->get('member_id') != null ? addslashes($request->get('member_id')) : null;
	$args['association_id'] = $request->get('association_id') != null ? addslashes($request->get('association_id')) : null;
	return $app->json(MemberAssociation::toJson(MemberAssociation::listMemberAssociations($args)), 200);
});

$memberAssociation->post('/', function(Request $request) use ($app) {
	$args = array();
	$args['member_id'] = $request->get('member_id') != null ? addslashes($request->get('member_id')) : null;
	$args['association_id'] = $request->get('association_id') != null ? addslashes($request->get('association_id')) : null;
	$args['added_by'] = $request->get('added_by') != null ? addslashes($request->get('added_by')) : null;
	$member_association = new MemberAssociation();
	foreach ($args as $key => $value) {
		$member_association->$key = $value;
	}
	return $app->json(array('result' => ($member_association->save() > 0 ? true : false)), 200);
});

$memberAssociation->delete('/{id}', function(Request $request, $id) use ($app) {
	$foundMemberAssociation = MemberAssociation::findMemberAssociation(addslashes($id));
	if($foundMemberAssociation != null){
		$foundMemberAssociation->removed_by = $request->get('removed_by') != null ? addslashes($request->get('removed_by')) : 0;
		$foundMemberAssociation->reason_removed = $request->get('reason_removed') != null ? addslashes($request->get('reason_removed')) : null;
		return $app->json(array('result' => ($foundMemberAssociation->delete() > 0 ? true : false)), 200);
	}
	return $app->json(array('result' => false), 400);
})->assert('id', '\d+');

$memberAssociation->get('/{member_association_id}', function($member_association_id) use ($app) {
	$foundMemberAssociation = MemberAssociation::findMemberAssociation(addslashes($member_association_id));
	if($foundMemberAssociation != null){
		return $app->json(MemberAssociation::toJson($foundMemberAssociation), 200);
	}
	return $app->json(array('result' => false), 400);
})->assert('member_association_id', '\d+');

$memberAssociation->get('/filter', function(Request $request) use ($app) {
	$args = explode(',', $request->get('filters'));
	$filters = array('removed' => 0);
	return $app->json(MemberAssociation::getFilters($args, $filters), 200);
});

$memberAssociation->get('/meta/count', function(Request $request) use ($app) {
	$args['removed'] = $request->get('removed') != null ? addslashes($request->get('removed')) : 0;
	$args['member_id'] = $request->get('member_id') != null ? addslashes($request->get('member_id')) : null;
	$args['association_id'] = $request->get('association_id') != null ? addslashes($request->get('association_id')) : null;
	return $app->json(array('count' => MemberAssociation::countMemberAssociation($args)), 200);
});

return $memberAssociation;