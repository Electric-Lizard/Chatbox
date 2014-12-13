<?php
session_start();
$expireTime = time() + 30*60*60*24;
setcookie("ip", $_SERVER['REMOTE_ADDR'], $expireTime);
if (empty($_COOKIE['hash-name'])) {
	setcookie("hash-name", uniqid(), time() + 6*30*60*60*24);
} else {
	setcookie("hash-name", $_COOKIE['hash-name'], time() + 6*30*60*60*24);
}
mb_internal_encoding('utf-8');
if (isset($_COOKIE["username"]) && isset($_COOKIE["cryptname"])) {
	$salt = "nx37ncn9384xmyx89YXYyxn9x*&#Yxn39yx9Y#bxywB^xb#&6(XB@74NXn9x3&nxN#*YNxn99#XNrnyx(#Yxn9xn3(xn93ynxYNxn#(Xn39Y3xn9y";
	if ($_COOKIE["cryptname"] == crypt($_COOKIE["username"], $_COOKIE["cryptname"])) {
		$_SESSION["loginStatus"] = "passed";
		$_SESSION["username"] = $_COOKIE["username"];
		//header("Location: http://{$_SERVER['SERVER_NAME']}/chatbox/index.php");
		//die();
	}
}
require_once "includes/database.php";
$database = new Database;
require_once "includes/blacklist.php";
foreach ($blacklist as $ip) {
	if ($_SERVER["REMOTE_ADDR"] == $ip) {
		echo "<h1>The chat is closed! Forever! Spammers win!</h1>";
		die();
	}
}
require_once "includes/posts.php";
$posts = new Posts;