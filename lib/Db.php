<?php

$CONFIG = array('DB_TYPE' => "mysql");

abstract class Db{

	public static function get_connection(){
		$db = null;
		global $CONFIG;
		if(strcmp($CONFIG["DB_TYPE"], "sqlite") == 0){
			$db = new SqliteDb();
		}

		if(strcmp($CONFIG["DB_TYPE"], "mysql") == 0){
			$db = new MysqlDb();
		}
		
		return $db->get_connection(); 
	}
}

/**
* Sqlite db class
*/
class SqliteDb implements DbInterface
{
	private $pdo;

	public function __construct()
	{
		$this->pdo = new PDO("sqlite:lib/test.db");
	}

	public function get_connection(){
		return $this->pdo;
	}
}

/**
* MySql db class
*/
class MysqlDb implements DbInterface
{
	private $pdo;

	function __construct()
	{
		$dsn = "mysql:dbname=church_manager;host=127.0.0.1";
		$username = "root";
		$password = "edem";
		try {
			$this->pdo = new PDO($dsn, $username, $password);	
		} catch (PDOException $e) {
			echo "Connection error ".$e->getMessage();
		}
	}

	public function get_connection(){
		return $this->pdo;
	}
}

interface DbInterface{
	public function get_connection();
}