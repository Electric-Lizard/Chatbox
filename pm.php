<?php
session_start();
require_once "includes/private_messages.php";
$pm = new PM;
if (empty($_SESSION["loginStatus"]) || $_SESSION["loginStatus"] !== "passed") {
	echo "<p>Private messages avaliable only for authorised users</p> <p><a href='index.php'>Login here</a></p>";
} else {
	if (!empty($_POST["receiver"]) && !empty($_POST["message"])) {
		$wasSent = $pm->sendMessage();
		if ($wasSent) {
			$_SESSION["SendStatus"] = "Message was Sent";
		} else {
			$_SESSION["SentStatus"] = "Such receiver does not exist";
		}
		header("Location: pm.php");
		die;
	} else {
		switch ($_GET['which']) {
			case '1': $messages = $pm->getMessages(1);
			break;
			case '2': $messages = $pm->getMessages(2);
			break;
			case '0':
			default: 
			$messages = $pm->getMessages(0);
			if (!empty($messages)) {
				$dbLastMessage = $pm->database->db->prepare("UPDATE `users` SET `lastPM` = ? WHERE `username`= ?");
				$dbLastMessage->execute(array(count($messages), $_SESSION["username"]));
			}
			break;
		}
		$pageTemp = "PM_temp.php";
		require_once "./temps/template.php";
	}
}