<?php
class Register {
	public function __construct() {
		require_once "database.php";
		$this->database = new Database;
	}
	public function verifyData() {
		if ($_POST["password"] !== $_POST["confirm-password"]) {
			$_SESSION["registerStatus"] = "Error: passwords not match";
			return false;
		} elseif (strlen($_POST["username"]) > 30) {
			$_SESSION["registerStatus"] = "Error: username must be less than 30 characters";
			return false;
		} else {
			$ch = curl_init("http://www.google.com/recaptcha/api/verify");
			$options = array(
				"privatekey" => "6LcqNP8SAAAAAH7fTTlkmSvuuoUcp62TgkKl_GbL",
				"remoteip" => $_SERVER["REMOTE_ADDR"],
				"challenge" => $_POST["recaptcha_challenge_field"],
				"response" => $_POST["recaptcha_response_field"]
			);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $options);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($ch);
			curl_close($ch);
			$responseArray = explode("\n", $response);
			if ($responseArray[0] == "true") {
				return true;
			} else {
				$_SESSION["registerStatus"] = "Capture error: " . $responseArray[1];
				return false;
			}
		}
	}
	public function addUser() {
		$dbAddUser = $this->database->db->prepare("INSERT INTO users (username, password, creationTime) VALUES (?, ?, ?)");
		$dbAddUser->execute(array($_POST["username"], crypt($_POST["password"]), date("Y-m-d H:i:s")));
	}
}
session_start();
$register = new Register;
if ($register->verifyData()) {
	$register->addUser();
	$_SESSION["registerStatus"] = "success";
	header("Location: http://{$_SERVER['SERVER_NAME']}/chatbox/index.php");
	die();
} else {
	header("Location: http://{$_SERVER['SERVER_NAME']}/chatbox/index.php");
	die();
}