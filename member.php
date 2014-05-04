<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$member = $app['controllers_factory'];

$member->get('/', function(Request $request) use ($app){
	$args = array();
	$args['removed'] = $request->get('removed') != null ? addslashes($request->get('removed')) : 0;
	$args['index'] = $request->get('index') != null ? addslashes($request->get('index')) : 0;
	$args['limit'] = $request->get('limit') != null ? addslashes($request->get('limit')) : DEFAULT_MAX_RESULT_SIZE;
	$args['firstname'] = $request->get('firstname') != null ? addslashes($request->get('firstname')) : null;
	$args['othernames'] = $request->get('othernames') != null ? addslashes($request->get('othernames')) : null;
	$args['gender'] = $request->get('gender') != null ? addslashes($request->get('gender')) : null;
	$args['phonenumber'] = $request->get('phonenumber') != null ? addslashes($request->get('phonenumber')) : null;
	return $app->json(Member::toJson(Member::listMembers($args)), 200);
});

$member->get("/{memberid}", function($memberid) use ($app){
	$member = Member::findMember($memberid);
	return $app->json(Member::toJson($member), 200);
})->assert('memberid', '\d+');

$member->post('/', function(Request $request) use ($app){
	$args = array();
	$args['firstname'] = $request->get('firstname') != null ? addslashes($request->get('firstname')) : null; 
	$args['othernames'] = $request->get('othernames') != null ? addslashes($request->get('othernames')) : null;
	$args['gender'] = $request->get('gender') != null ? addslashes($request->get('gender')) : null;
	$args['registration_date'] = $request->get('registration_date') != null ? addslashes($request->get('registration_date')): null;
	$args['added_by'] = $request->get('added_by') != null ? addslashes($request->get('added_by')) : null;
	$args['picture_url'] = $request->get('picture_url') != null ? addslashes($request->get('picture_url')) : null;
	$args['phonenumber'] = $request->get('phonenumber') != null ? addslashes($request->get('phonenumber')) : null;
	$member = new Member();
	foreach ($args as $key => $value) {
		$member->$key = $value;
	}
	return $app->json(array('result' => ($member->save() > 0 ? true: false)), 200);
});

$member->delete('/{memberid}', function(Request $request, $memberid) use ($app){
	$reason_removed = $request->get('reason_removed') != null ? addslashes($request->get('reason_removed')) : null;
	$foundMember = Member::findMember($memberid);
	if($reason_removed != null && $foundMember != null){
		$foundMember->reason_removed = $reason_removed;
		return $app->json(array('result' => ($foundMember->remove() > 0 ? true : false)), 200);
	}
	return $app->json(array('result' => false), 400);
})->assert('memberid', '\d+');

$member->put('/{memberid}', function(Request $request, $memberid) use ($app) {
	$foundMember = Member::findMember($memberid);
	if($foundMember != null){
		$args = array();
		$args['firstname'] = $request->get('firstname') != null ? addslashes($request->get('firstname')) : null;
		$args['othernames'] = $request->get('othernames') != null ? addslashes($request->get('othernames')) : null;
		$args['gender'] = $request->get('gender') != null ? addslashes($request->get('gender')) : null;
		$args['phonenumber'] = $request->get('phonenumber') != null ? addslashes($request->get('phonenumber')) : null;
		foreach ($args as $key => $value) {
			$foundMember->$key = $value;
		}
		return $app->json(array('result' => ($foundMember->update() > 0 ? true : false)), 400);
	}
	return $app->json(array('result' => false), 400);
})->assert('memberid', '\d+');

$member->put('/picture/{memberid}/{picture_url}', function($memberid, $picture_url) use ($app){
	$foundMember = Member::findMember($memberid);
	if($foundMember != null){
		$foundMember->picture_url = addslashes($picture_url);
		return $app->json(array('result' => ($foundMember->changePicture() > 0 ? true : false)), 200);
	}
	return $app->json(array('result' => false), 400);
})->assert('memberid', '\d+');

$member->get('/meta/count', function(Request $request) use ($app) {
	$removed = $request->get('removed') != null ? addslashes($request->get('removed')) : 0;
	return $app->json(array('result' => Member::countMember($removed)), 200);
});

$member->get('/filter', function(Request $request) use ($app) {
	$args = explode(',', $request->get('filters'));
	$filters = array('removed' => 0);
	return $app->json(Member::getFilters($args, $filters), 200);
});

return $member;