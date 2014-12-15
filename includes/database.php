<?php
class Database {
	public function __construct() {
		$host = $this->host = "mysql.hostinger.ru";
<<<<<<< HEAD
		$dbname = $this->dbname = "jam";
		$username = $this->username = "jam";
		$password = $this->password = "jam";
=======
		$dbname = $this->dbname = "foo";
		$username = $this->username = "bar";
		$password = $this->password = "baz";
>>>>>>> 5b0a89a0f27f39168d9cd4782fa215d49f56d785
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
