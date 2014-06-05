<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$user = $app['controllers_factory'];

session_start();

$user->get('/', function(Request $request) use ($app){
	$args = array();
	$args['index'] = $request->get('index') != null ? addslashes($request->get('index')) : 0;
	$args['limit'] = $request->get('limit') != null ? addslashes($request->get('limit')) : DEFAULT_MAX_RESULT_SIZE;
	$args['removed'] = $request->get('removed') != null ? addslashes($request->get('removed')) : 0;
	$args['usertype'] = $request->get('usertype') != null ? addslashes($request->get('usertype')) : "attendant";
	$results = User::toJson(User::listUsers($args));
	return $app->json(User::toJson(User::listUsers($args)), 200);
});

$user->get('/{userid}', function($userid) use ($app){
	return $app->json(User::toJson(User::findUser($userid)), 200);
})->assert('userid', '\d+');

$user->get('/meta/count', function(Request $request) use ($app){
	$args = array();
	$args['removed'] = $request->get('removed') != null ? addslashes($request->get('removed')) : 0;
	$args['usertype'] = $request->get('usertype') != null ? addslashes($request->get('usertype')) : 'attendant';
	return $app->json(array('count' => User::countUser($args)), 200);
});

$user->get('/filter', function(Request $request) use ($app){
	$args = explode(',', $request->get('filters'));
	$filters = array('removed' => 0, 'usertype' => "'attendant'");
	return $app->json(User::getFilters($args, $filters), 200);
});

$user->post('/', function(Request $request) use ($app){
	$args = array();
	$args['username'] = $request->get('username') != null ? addslashes($request->get('username')) : 0;
	$args['usertype'] = $request->get('usertype') != null ? addslashes($request->get('usertype')) : "attendant";
	$args['password'] = $request->get('password') != null ? addslashes($request->get('password')) : null;
	$user = new User();
	foreach ($args as $key => $value) {
		$user->$key = $value;
	}
	return $app->json(array('result' => ($user->save() > 0 ? true: false)), 200);
});

$user->put('/{userid}', function(Request $request, $userid) use ($app){
	$foundUser = User::findUser(addslashes($userid));
	$args = array();
	$args['username'] = $request->get('username') != null ? addslashes($request->get('username')) : 0;
	$args['usertype'] = $request->get('usertype') != null ? addslashes($request->get('usertype')) : "attendant";
	$args['password'] = $request->get('password') != null ? addslashes($request->get('password')) : null;
	foreach ($args as $key => $value) {
	 	$foundUser->$key = $value;
	}
	return $app->json(array('result' => ($foundUser->update() > 0 ? true : false)), 200);
})->assert('userid', '\d+');

$user->post('/login', function(Request $request) use ($app){
	$username = $request->get('username') != null ? addslashes($request->get('username')) : null;
	$password = $request->get('password') != null ? addslashes($request->get('password')) : null;
	if($username != null && $password != null){
		$user = User::login($username, $password);
		if($user != null){
			$_SESSION['user'] = $user;
			return $app->json(array('result' => true), 200);
		}
	}
	return $app->json(array('result' => false), 200);
});

$user->post('/login/state', function() use ($app){
	$app->json(array('result' => isLogin()), 200);
});

$user->get('/logout', function(Request $request) use ($app){
	$_SESSION['user'] = null;
	session_destroy();
	return $app->json(array('result' => true), 200);
});

$user->delete('/{userid}', function($userid) use ($app){
	$user = User::findUser($userid);
	if($user != null){
		return $app->json(array('result' => ($user->remove() > 0 ? true : false)), 200);
	}
	return $app->json(array('result' => false), 400);
})->assert('userid', '\d+');

return $user;