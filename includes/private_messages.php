<?php
class PM {
	public function __construct() {
		require_once "includes/database.php";
		$this->database = new Database;
	}
	public function sendMessage() {
		try {
			$dbUsers = $this->database->db->prepare("SELECT * FROM users WHERE `username`=?");
			$dbUsers->execute(array($_POST["receiver"]));
			while ($user = $dbUsers->fetch()) {
				$correspondence[] = $user;
			}
			if (empty($correspondence)) {
				return false;
			}
			$dbPrepared = $this->database->db->prepare("INSERT INTO PM (receiver, message, sender, ip, sendingTime) VALUES (?, ?, ?, ?, ?)");
			$dbPrepared->execute(array($_POST["receiver"], $_POST["message"], $_SESSION["username"], $_SERVER["REMOTE_ADDR"], date("Y-m-d H:i:s")));
		} catch (PDOException $e){
			echo $e->getMessage();
		}
		return true;
	}
	public function getMessages($which = 0) {
		try{
			switch ($which) {
				case 0:
				default:
				$dbMessages = $this->database->db->prepare("SELECT * FROM PM WHERE `receiver`=?");
				$dbMessages->execute(array($_SESSION["username"]));
				break;
				case 1:
				$dbMessages = $this->database->db->prepare("SELECT * FROM PM WHERE `sender`=?");
				$dbMessages->execute(array($_SESSION["username"]));
				break;
				case 2:
				$dbMessages = $this->database->db->prepare("SELECT * FROM PM WHERE `receiver`=? OR `sender`=?");
				$dbMessages->execute(array($_SESSION["username"], $_SESSION["username"]));
				break;
			}
			while ($message = $dbMessages->fetch()) {
				$messages[] = $message;
			}
		} catch (PDOException $e){
			echo $e->getMessage();
		}
		return $messages;
	}
}