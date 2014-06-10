<?php

/**
* User class
*/
require_once('member.php');

class User
{
	const TABLE = "user";

	private $data = array("id" => null, 'firstname' => null, 'othernames' => null,
		"username" => null, "memberid" => 0, "password" => null, "usertype" => null, 
		"removed" => null);

	public function __get($field){
		if(!array_key_exists($field, $this->data)){
			throw new Exception($field." is not a valid member of ".get_class());
		}
		return $this->data[$field];
	}

	public function __set($field, $value){
		if(!array_key_exists($field, $this->data)){
			throw new Exception($field." is not a valid member of ".get_class());
		}
		$this->data[$field] = $value;
	}

	public function remove(){
		$query = "update ".User::TABLE." set removed = 1 where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function update(){
		$query = "update ".User::TABLE." set username = '$this->username', ".
			"usertype = '$this->usertype', password = md5('$this->password') where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function save(){
		$query = "insert into ".User::TABLE." (memberid, username, password, usertype) ".
			"values ($this->memberid, '$this->username',md5('$this->password'), '$this->usertype')";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	private static function returnUserFromResource($resource){
		return returnObjectFromResource(get_class(), $resource, 
			array("id", "firstname", "othernames", "memberid", "username", 
				"usertype", "removed"));
	}

	public static function listUsers($args = array("usertype" => null, "removed" => 0, 
		"index" => 0, "limit" => 100)){
		$query = "select u.*,m.firstname, m.othernames from ".User::TABLE.
			" as u join ".Member::TABLE." as m on (u.memberid = m.id)";
		$whereSet = 0;

		if(array_key_exists("usertype", $args) && isset($args["usertype"])){
			$query .= " where u.usertype = '".$args["usertype"]."'";
			$whereSet = 1;
		}

		if(array_key_exists("removed", $args)){
			if($whereSet == 1){
				$query .= " and u.removed = ".$args["removed"];
			}else{
				$query .= " where u.removed = ".$args["removed"];
			}
		}

		$query .= " order by u.id desc";
		
		$query .= check_list_limits($args);
		$users = array();

		$conn = Db::get_connection();
		foreach ($conn->query($query) as $row) {
			$user = User::returnUserFromResource($row);
			array_push($users, $user);
		}
		return $users;
	}

	public static function findUser($id){
		$query = "select * from ".User::TABLE." where id = $id limit 1";
		$user = null;
		$conn = DB::get_connection();
		foreach ($conn->query($query) as $row) {
			$user = User::returnUserFromResource($row);
		}
		return $user;
	}

	public static function login($username, $password){
		$query = "select * from ".User::TABLE." where username = '$username'".
			" and password = md5('$password') and removed = 0 limit 1";
		$user = null;
		$conn = DB::get_connection();
		foreach ($conn->query($query) as $row) {
			$user = User::returnUserFromResource($row);
		}
		return $user;
	}

	public static function toJson($args){
		return ToJson(get_class(), $args,  array("id", "firstname", "othernames",
			"memberid", "usertype", "username", "password", "removed"));
	}

	public static function countUser($args = array('removed' => 0, 'usertype' => 'attendant')){
		$query = "select count(id) as record_count from ".User::TABLE.
			" where usertype = '".$args['usertype']."'";
		$args['and'] = true;
		$query = queryBuilder($query, $args);
		$conn = Db::get_connection();
		$usercount = 0;
		foreach ($conn->query($query) as $row) {
			$usercount = $row["record_count"];
		}
		return $usercount;
	}

	public static function getFilters($args = array('username', 'usertype'), 
		$filters = array('removed' => 0)){
		return getFilters(User::TABLE, $args, $filters);
	}
}