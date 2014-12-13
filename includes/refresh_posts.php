<?php
session_start();
require_once "database.php";
$database = new Database;
require_once "posts.php";
$Posts = new Posts;
$Posts->callParentConstruct();
$lastPostId = $_POST["lastPostId"];
$dbPrepared = $database->db->prepare("SELECT MAX(id) AS id FROM posts");
$dbPrepared->execute();
$currentLastPost = $dbPrepared->fetch();
if ($_POST["lastPostId"] == $currentLastPost["id"]) {
echo "none";
} else {
	$dbPrepared = $database->db->prepare("SELECT * FROM `posts` WHERE id > $lastPostId");
	$dbPrepared->execute();
	while ($dbFetchedRow = $dbPrepared->fetch()) {
		$newPosts[] = $dbFetchedRow;
	}
	require_once "../temps/new_posts.php";
}