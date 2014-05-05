<?php
require_once("Db.php");
require_once("utils.php");

/**
* User class
*/
class User
{
	const TABLE = "user";

	private $data = array(
		"id" => null, "username" => null, 
		"password" => null, "usertype" => null, 
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
		$query = "insert into ".User::TABLE." (username, password, usertype) ".
			"values ('$this->username',md5('$this->password'), '$this->usertype')";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	private static function returnUserFromResource($resource){
		return returnObjectFromResource(get_class(), $resource, 
			array("id", "username", "usertype", "removed"));
	}

	public static function listUsers($args = array("usertype" => null, "removed" => 0, 
		"index" => 0, "limit" => 100)){
		$query = "select * from ".User::TABLE;
		$whereSet = 0;

		if(array_key_exists("usertype", $args) && isset($args["usertype"])){
			$query .= " where usertype = '".$args["usertype"]."'";
			$whereSet = 1;
		}

		if(array_key_exists("removed", $args)){
			if($whereSet == 1){
				$query .= " and removed = ".$args["removed"];
			}else{
				$query .= " where removed = ".$args["removed"];
			}
		}

		$query .= " order by id desc";
		
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
		return ToJson(get_class(), $args,array("id", "usertype", "username", "password", "removed"));
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

/**
* Association
*/
class Association
{
	const TABLE = "association";

	private $data = array("id" => null, "name" => null, "description" => null, 
		"date_added" => null, "added_by" => null, "removed" => null, 
		"removed_by" => null, "reason_removed" => null, "date_removed" => null);

	public function __get($field){
		if(!array_key_exists($field, $this->data)){
			throw new Exception($field." is not a member of ".get_class());
		}
		return $this->data[$field];
	}

	public function __set($field, $value){
		if(!array_key_exists($field, $this->data)){
			throw new Exception($field." is not a member of ".get_class());
		}
		$this->data[$field] = $value;
	}

	public function save(){
		$query = "insert into ".Association::TABLE." (name, description, date_added,".
			" added_by) values('$this->name', '$this->description', '".date("d-m-Y").
			"', $this->added_by);";

		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function update(){
		$query = "update ".Association::TABLE." set name = '$this->name', description =".
			" '$this->description' where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function delete(){
		$query = "update ".Association::TABLE." set removed = 1, removed_by = ".
			"$this->removed_by, reason_removed = '$this->reason_removed', ".
			"date_removed = '".date("d-m-Y")."' where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	private static function returnAssociationFromResource($resource){
		return returnObjectFromResource(get_class(), $resource, 
			array("id", "name", "description", "date_added", "added_by", "removed", "removed_by", 
				"reason_removed", "date_removed"));
	}

	public static function listAssociations($args = array("index" => 0, "limit" => 100, 
		"removed" => 0)){
		
		$query = "select * from ".Association::TABLE;
		$query = queryBuilder($query, $args);
		$conn = Db::get_connection();
		$associations = array();
		foreach ($conn->query($query) as $row) {
			array_push($associations, Association::returnAssociationFromResource($row));
		}
		return $associations;
	}

	public static function findAssociation($id){
		$conn = Db::get_connection();
		$query = "select * from ".Association::TABLE." where id = $id limit 1";
		$result = null;
		foreach ($conn->query($query) as $row) {
			$result = Association::returnAssociationFromResource($row);
		}
		return $result;
	}

	public static function toJson($args){
		return ToJson(get_class(), $args, array("id", "name", "description", "date_added", 
			"added_by", "removed", "removed_by", "reason_removed", "date_removed"));
	}

	public static function countAssociation($removed = 0){
		$query = "select count(id) as row_count from ".Association::TABLE.
			" where removed = $removed limit 1";
		$count = 0;
		$conn = Db::get_connection();
		foreach ($conn->query($query) as $row) {
			$count = $row['row_count'];
		}
		return $count;
	}

	public static function getFilters($args = array('id', 'name', 'description'), 
		$filters = array('removed' => 0)){
		return getFilters(Association::TABLE, $args, $filters);
	}
}

/**
* Association Due
*/
class AssociationDue
{
	const TABLE = "association_due";
	private $data = array("id" => null, "association_id" => null, "date_added" => null, 
		"removed" => null, "removed_by" => null, "dues" => null, "added_by" => null, 
		'date_removed' => null);

	public function __set($field, $value){
		if(!array_key_exists($field, $this->data)){
			throw new Exception($field." is not a valid member of ".get_class());
		}
		$this->data[$field] = $value;
	}

	public function __get($field){
		if(!array_key_exists($field, $this->data)){
			throw new Exception($field." is not a valid member of ".get_class());
		}
		return $this->data[$field];
	}

	public function save(){
		$query = "insert into ".AssociationDue::TABLE." (association_id, dues, date_added, added_by) ".
			"value ($this->association_id, $this->dues,'".date("d-m-Y")."',$this->added_by)";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function update(){
		$query = "update ".AssociationDue::TABLE." set dues = $this->dues where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function delete(){
		$query = "update ".AssociationDue::TABLE." set removed = 1, removed_by = $this->removed_by".
			", date_removed = '".date('d-m-Y')."' where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	private static function returnAssociationDueFromResource($resource){
		return returnObjectFromResource(get_class(), $resource, array("id", "association_id",
			"dues", "date_added", "removed_by", "removed", 'date_removed'));
	}

	public static function toJson($args){
		return ToJson(get_class(), $args, array("id", "association_id", "dues", "date_added", 
			"removed", "removed_by", 'date_removed'));
	}

	public static function listAssociationDues($args = array("index" => 0, "limit" => 100, 
		"removed" => 0)){
		
		$query = "select * from ".AssociationDue::TABLE;
		if(array_key_exists("association_id", $args) && $args['association_id'] != 0){
			$query .= " where association_id = ".$args['association_id'];
			$args['and'] = true;
		}

		$query = queryBuilder($query, $args);
		$conn = Db::get_connection();
		$associationDues = array();
		foreach ($conn->query($query) as $row) {
			array_push($associationDues, 
				AssociationDue::returnAssociationDueFromResource($row));
		}
		return $associationDues;
	}

	public static function findAssociationDue($association_id){
		$conn = Db::get_connection();
		$query = "select * from ".AssociationDue::TABLE." where id = $association_id and removed = 0 limit 1";
		$result = null;
		foreach ($conn->query($query) as $row) {
			$result = AssociationDue::returnAssociationDueFromResource($row);
		}
		return $result;
	}

	public static function countAssociationDue($removed = 0){
		$query = "select count(id) as row_count from ".AssociationDue::TABLE.
			" where removed = $removed limit 1";
		$conn = Db::get_connection();
		$count = 0;
		foreach ($conn->query($query) as $row) {
			$count = $row['row_count'];
		}
		return $count;
	}
}

/**
* Member Association
*/
class MemberAssociation
{
	const TABLE = "member_association";
	private $data = array("id" => null, "member_id" => null, "association_id" => null, 
		"added_by" => null, "date_added" => null, "removed" => null, "removed_by" => null,
		"reason_removed" => null, "date_removed" => null);

	public function __set($field, $value){
		if (!array_key_exists($field, $this->data)) {
			throw new Exception($field." is not a valid member of ".get_class());
		}
		$this->data[$field] = $value;
	}

	public function __get($field){
		if(!array_key_exists($field, $this->data)){
			throw new Exception($field." is not a valid member of ".get_class());
		}
		return $this->data[$field];
	}

	public function save()
	{
		$query = "insert into ".MemberAssociation::TABLE." (member_id, association_id,".
			" added_by, date_added) values ($this->member_id, $this->association_id, ".
			"$this->added_by, '".date("d-m-Y")."')";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function update()
	{
		$query = "update ".MemberAssociation::TABLE." set association_id = $this->association_id".
			" where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function delete(){
		$query = "update ".MemberAssociation::TABLE." set removed_by = $this->removed_by, removed = 1".
			", reason_removed = '$this->reason_removed' date_removed = '".date('d-m-Y')."' where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	private static function returnMemberAssociationFromResource($resource){
		return returnObjectFromResource(get_class(), $resource, array("id", "member_id",
			"association_id", "added_by", "date_added", "removed", "removed_by", 
			"reason_removed", "date_removed"));
	}

	public static function toJson($args){
		return ToJson(get_class(), $args, array("id", "member_id", "association_id", 
			"added_by", "date_added", "removed", "removed_by", "reason_removed", 
			"date_removed"));
	}

	public static function listMemberAssociations($args = array("index" => 0, 
		"limit" => 100, "removed" => 0)){

		$query = "select * from ".MemberAssociation::TABLE;
		$query = queryBuilder($query, $args);
		$conn = Db::get_connection();
		$memberAssociations = array();
		foreach ($conn->query($query) as $row) {
			array_push($memberAssociations, 
				MemberAssociation::returnMemberAssociationFromResource($row));
		}
		return $memberAssociations;
	}

	public static function listAssociationsForMember($args = array("member_id" => 0, 
		"index" => 0, "limit" => 100, "removed" => 0)){

		$query = "select * from ".MemberAssociation::TABLE." where member_id = ".$args["member_id"];
		$query = queryBuilder($query, $args);
		$conn = Db::get_connection();
		$memberAssociations = array();
		foreach ($conn->query($query) as $row) {
			array_push($memberAssociations, 
				MemberAssociation::returnMemberAssociationFromResource($row));
		}
		return $memberAssociations;
	}

	public static function findMemberAssociation($id){
		$query = "select * from ".MemberAssociation::TABLE." where id = $id limit 1";
		$conn = Db::get_connection();
		$result = null;
		foreach ($conn->query as $row) {
			$result = returnMemberAssociationFromResource($row);
		}
		return $result;
	}

	public static function countMemberAssociation($removed = 0){
		$query = "select count(id) as row_count from ".MemberAssociation::TABLE.
			" where removed = $removed limit 1";
		$conn = Db::get_connection();
		$count = 0;
		foreach ($conn->query($query) as $row) {
			$count = $row['row_count'];
		}
		return $count;
	}
}

/**
*  Member Association Due
*/
class MemberAssociationDue
{
	
	const TABLE = "member_association_due";

	private $data = array("id" => null, "member_id" => null, "association_id" => null, 
		"dues" => null, "month" => null, "date_added" => null, "added_by" => null, 
		"removed" => null, "removed_by" => null, 'date_removed' => null);

	public function __get($field)
	{
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
		$query = "insert into ".MemberAssociationDue::TABLE." (association_id, dues, month, date_added,"+
			" added_by) values ($this->association_id, $this->dues, $this->month,'".date("d-m-Y")."')";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function update(){
		$query = "update ".MemberAssociationDue::TABLE." set association_id = $this->association_id, "+
			"dues = $this->dues, month = $this->month where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}

	public function delete(){
		$query = "update ".MemberAssociationDue::TABLE." set removed_by = $this->removed_by, removed "+
			"= $this->removed, date_removed = '".date('d-m-Y')."' where id = $this->id";
		$conn = Db::get_connection();
		return $conn->exec($query);
	}
	
	private static function returnMemberAssociationDueFromResource($resource){
		return returnObjectFromResource(get_class(), $resource, array("id", 
			"member_id", "association_id", "dues", "month", "date_added", 
			"added_by", "removed", "removed_by", 'date_removed'));
	}

	public static function toJson($args){
		return ToJson(get_class(), $args, array("id", "member_id", "association_id", 
			"dues", "month", "date_added", "added_by", "removed", "removed_by", 
			'date_removed'));
	}

	public static function listMemberAssociationDues($args = array("member_id" => 0, "index" => 0, 
		"limit" => 100, "removed" => 0)){
		$query = "select * from ".MemberAssociationDue::TABLE;

		if(array_key_exists("member_id", $args) && $args["member_id"] != 0){
			$query .= " where member_id = ".$args['member_id'];
		}

		$args['and'] = true;
		$query = queryBuilder($query, $args);
		$conn = Db::get_connection();
		$memberAssociationDues = array();
		foreach ($conn->query($query) as $row) {
			array_push($memberAssociationDues, returnMemberAssociationDueFromResource($row));
		}
		return $memberAssociationDues;
	}

	public static function findMemberAssociationDue($id){
		$query = "select * from ".MemberAssociationDue::TABLE." where id = $id limit 1";
		$result = null;
		$conn = Db::get_connection();
		foreach ($conn->query($query) as $row) {
			$result = returnMemberAssociationDueFromResource($row);
		}
		return $result;
	}

	public static function countMemberAssociationDue($removed = 0){
		$query = "select count(id) as row_count from ".MemberAssociationDue::TABLE.
			" where removed = $removed limit 1";
		$count = 0;
		$conn = Db::get_connection();
		foreach ($conn->query($query) as $row) {
			$count = $row['row_count'];
		}
		return $count;
	}
}