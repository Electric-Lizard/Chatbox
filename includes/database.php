<?php
class Database {
	public function __construct() {
		$host = $this->host = "mysql.hostinger.ru";
		$dbname = $this->dbname = "jam";
		$username = $this->username = "jam";
		$password = $this->password = "jam";
		try{
		$this->db = new PDO ("mysql:host={$this->host};dbname={$this->dbname}", $this->username, $this->password);
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->db->query('SET NAMES "utf8"');
		}
	 	catch (PDOException $e) {
			$e->getMessage();
		}
	}
	public function recountId($table) {
		$this->db->query("SET @count = 0; UPDATE `$table` SET `$table`.`id` = @count:= @count + 1;");
	}
}