<?php
class UserLogin {
	public function __construct() {
		require_once "database.php";
		$this->database = new Database;
	}
	public function verifyLogin() {
		try{
			$dbUsers = $this->database->db->prepare("SELECT * FROM users");
			$dbUsers->execute();
			while ($dbUser = $dbUsers->fetch()) {
				if ($dbUser["password"] == crypt($_POST["login-password"], $dbUser["password"])) {
					$isPassed[] = $dbUser;
				}
			}
			return $isPassed;
		} catch (PDOException $e) {
			echo $e->getMessage();
		}
	}
}
session_start();
$userLogin = new UserLogin;
if (!empty($_POST["login-username"]) && !empty($_POST["login-password"])) {
	$isPassed = $userLogin->verifyLogin();
	if (!empty($isPassed)) {
		$_SESSION["loginStatus"] = "passed";
		$_SESSION["username"] = $isPassed[0]["username"];
		if (isset($_POST["keep-logged"]) && $_POST["keep-logged"] == "true") {
			$salt = "nx37ncn9384xmyx89YXYyxn9x*&#Yxn39yx9Y#bxywB^xb#&6(XB@74NXn9x3&nxN#*YNxn99#XNrnyx(#Yxn9xn3(xn93ynxYNxn#(Xn39Y3xn9y";
			setcookie("username", $isPassed[0]["username"], time() + 60 * 60 * 60 * 24, "/chatbox/");
			setcookie("cryptname", crypt($isPassed[0]["username"], $salt), time() + 60 * 60 * 60 * 24, "/chatbox/");
		}
		
		header("Location: http://{$_SERVER['SERVER_NAME']}/chatbox/index.php");
		die();
	} else {
		$_SESSION["loginStatus"] = "failed";
		header("Location: http://{$_SERVER['SERVER_NAME']}/chatbox/index.php");
		die();
	}
} elseif (isset($_GET["logout"])) {
	$_SESSION["loginStatus"] = "logout";
	setcookie("username", null, -1, "/chatbox/");
	setcookie("cryptname", null, -1, "/chatbox/");
	unset($_COOKIE["username"]);
	unset($_COOKIE["cryptname"]);
	header("Location: http://{$_SERVER['SERVER_NAME']}/chatbox/index.php");
	die();
}