<?php
session_start();
require_once "database.php";
$database = new Database;
require_once "posts.php";
$Posts = new Posts;
$Posts->callParentConstruct();
if (!empty($_POST["lowestPostId"])) {
	if ($_POST["lowestPostId"] > 100) {
		$postRestriction = $_POST["lowestPostId"] - 100;
	} else {
		$postRestriction = 0;
	}
	require_once "database.php";
	$database = new Database;
	$dbOlderPosts = $database->db->prepare("SELECT * FROM posts WHERE id<? AND id>? ORDER BY id");
	$dbOlderPosts->execute(array($_POST["lowestPostId"], $postRestriction));
	$posts = array();
	$newPosts = [];
	while ($dbPost = $dbOlderPosts->fetch()) {
		$newPosts[] = $dbPost;
	}
	require_once "../temps/new_posts.php";
}
