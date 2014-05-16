<?php

/**
* Member
*/
class Member
{
	const TABLE = "member";

	private $data = array("id" => null, "firstname" => null,
		"othernames" => null, "gender" => null, 
		"registration_date" => null, "added_by" => null, 
		"picture_url" => null, "removed" => null, 
		"phonenumber" => null, "reason_removed" => null, 
		"date_removed" => null);

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

	public function save(){
		$query = "insert into ".Member::TABLE." (firstname,othernames,gender,".
			"registration_date,added_by,picture_url, phonenumber) values ('$this->firstname',".
			"'$this->othernames','$this->gender','$this->registration_date',".
			"'$this->added_by','$this->picture_url','$this->phonenumber')";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function update(){
		$query = "update ".Member::TABLE." set firstname = '$this->firstname',".
			" othernames = '$this->othernames', gender = '$this->gender', ".
			"phonenumber = '$this->phonenumber' where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function changePicture(){
		$query = "update ".Member::TABLE." set picture_url = '$this->picture_url'".
			" where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function remove(){
		$query = "update ".Member::TABLE." set removed = 1, reason_removed".
				 " = '$this->reason_removed', date_removed = '".date('d-m-Y')."'".
				 " where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	private static function returnMemberFromResource($resource){
		return returnObjectFromResource(get_class(), $resource, 
			array("id", "firstname", "othernames", "gender", "registration_date", "added_by", 
				"picture_url", "removed", "phonenumber", "reason_removed", "date_removed"));
	}

	public static function listMembers($args = array("removed" => 0, "index" => 0, "limit" => 100)){
		$query = "select * from ".Member::TABLE." where removed = ". $args['removed'];
		if($args['firstname'] != null){
			$query .= " and firstname = '".$args['firstname']."'";
		}
		if($args['othernames'] != null){
			$query .= " and othernames = '".$args['othernames']."'";
		}
		if($args['gender'] != null){
			$query .= " and gender = '".$args['gender']."'";
		}
		if($args['phonenumber'] != null){
			$query .= " and phonenumber = '".$args['phonenumber']."'";
		}
		$query .= " order by registration_date desc ";
		$conn = Db::get_connection();
		$members = array();
		foreach ($conn->query($query) as $row) {
			array_push($members, Member::returnMemberFromResource($row));
		}
		return $members;
	}

	public static function findMember($Member_id){
		$query = "select * from ".Member::TABLE." where id = $Member_id limit 1";
		$conn = Db::get_connection();
		$member = null;
		foreach ($conn->query($query) as $row) {
			$member = Member::returnMemberFromResource($row);
		}
		return $member;
	}

	public static function countMember($removed = 0){
		$query = "select count(id) as record_count from ".Member::TABLE.
			" where removed = $removed";
		$conn = Db::get_connection();
		$count = 0;
		foreach ($conn->query($query) as $row) {
			$count = $row["record_count"];
		}
		return $count;
	}

	public static function toJson($args){
		return ToJson(get_class(), $args, array("id", "firstname", "othernames", "gender", 
			"registration_date", "added_by", "picture_url", "removed", "phonenumber", 
			"reason_removed", "date_removed"));
	}

	public static function getFilters($args = array('id', 'firstname', 'othernames', 'gender', 'phonenumber'),
		$filters = array('removed' => 0)){
		return getFilters(Member::TABLE, $args, $filters);
	}
}