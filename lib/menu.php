<?php

/**
* Menu Item
*/
class MenuItem
{
	public const TABLE = "menu_structure";
	private $data = array('id' => null, 'name' => null, 'endpoint' => null, 'usertype' => null);

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

	private static getMenuItemFromResource($resource){
		$tmp = new MenuItem();
		$tmp->id = $row['id'];
		$tmp->name = $row['name'];
		$tmp->endpoint = $row['endpoint'];
		$tmp->usertype = $row['usertype'];
		return $tmp;
	}

	public static list($usertype){
		$query = "select * from ".MenuItem::TABLE." where usertype = '$usertype'";
		$conn = Db::get_connection();
		$menuItems = array();
		foreach ($conn->query($query) as $row) {
			array_push($menuItems, MenuItem::getMenuItemFromResource($row));
		}
		return $menuItems;
	}

	public static IsUserAuthourized($usertype, $endpoint){
		$query = "select count(id) from ".MenuItem::TABLE.
			" where usertype = $usertype and endpoint = $endpoint";
		$conn = Db::get_connection();
		$count = 0;
		foreach ($conn->query($query) as $row) {
			$count = $row['id'];
		}
		return ($count > 0);
	}
}