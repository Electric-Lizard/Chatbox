<?php
require_once "process_post.php";
class Posts extends ProcessPost{
	public $database;
	public function callParentConstruct() {
		parent::__construct();
	}
	public function __construct() {
		$this->isAdmin = $this->getAdminStatus();
		$this->rootDir = $_SERVER['DOCUMENT_ROOT'];
		parent::__construct();
		$this->database = new Database;
		if(($_SERVER['REQUEST_METHOD'] === "POST") && isset($_POST['content'])) {
			$this->savePost();
		} elseif (!empty($_GET["deletePost"])) {
			if ($this->isAdmin) {
				try{
					$dbPrepared = $this->database->db->prepare("DELETE FROM posts WHERE id=?");
					$dbPrepared->execute(array($_GET["deletePost"]));
					$this->database->recountId('posts');
				} catch (PDOException $e) {
					echo $e->getMessage;
				}
			}
			header("Location: index.php");
		} elseif (!empty($_GET["deletePosts"])) {
			if ($this->isAdmin) {
				$deletingPosts = explode("-", $_GET["deletePosts"]);
				try{
					$dbPrepared = $this->database->db->prepare("DELETE FROM posts WHERE id>=? AND id<=?");
					$dbPrepared->execute($deletingPosts);
					$this->database->recountId('posts');
				} catch (PDOException $e) {
					echo $e->getMessage;
				}
			}
			header("Location: index.php");
		} elseif(empty($_POST['lastPostId']) && empty($_POST["lowestPostId"])) {
			$this->listPosts();
		}
	}
	public function getAdminStatus() {
		$admins = array("admin");
		if ($_SESSION["loginStatus"] == "passed" && in_array($_SESSION["username"], $admins)) {
			return true;
		} else return false;
	}
	public function listPosts() {
		$posts = [];
		(isset($_GET['loginform']) && $_GET['loginform']) ? $loginTemp = "login_form.php" : $loginTemp = false;
		try{
			$dbprepared = $this->database->db->prepare("SELECT * FROM posts ORDER BY id");
			$dbprepared->execute();
			$row = 0;
			while ($dbfetch = $dbprepared->fetch()) {
				$posts[$row++] = $dbfetch;
			}
			unset($row);
		} catch (PDOException $e){
			echo $e->getMessage();
		}
		$maxPost = count($posts);
		foreach ($posts as $key => $post) {
			if ($key < $maxPost-50) {
				unset($posts[$key]);
			}
		}
		$lastPM = $this->database->db->prepare("SELECT * FROM users WHERE username = ?");
		$lastPM->execute(array($_SESSION["username"]));
		$lastPMUser = $lastPM->fetch();
		$lastPM = $lastPMUser["lastPM"];
		$realLastPM = $this->database->db->prepare("SELECT * FROM PM WHERE receiver = ?");
		$realLastPM->execute(array($_SESSION["username"]));
		while ($someMessage = $realLastPM->fetch()) {
			$allPMs[] = $someMessage;
		}
		$pageTemp = "posts_temp.php";
		require_once "$this->rootDir/chatbox/temps/template.php";
	}
	public function savePost(){
		$this->validateBlank();
		if(isset($_POST['username'])) {
			$username = $_POST['username'];
		} else {
			$username = null;	
		}
		setcookie('rememberedName', $_POST['username'], time()+30*60*60*24);
		isset($_POST['password']) ? $cryptPass = crypt($_POST['password']) : $cryptPass = null;
		isset($_POST['content']) ? $content = $_POST['content'] : $content = null;
		$email = null;
		isset($_SERVER['REMOTE_ADDR']) ? $clientIP = $_SERVER['REMOTE_ADDR'] : $ip = null;
		isset($_POST['title']) ? $title = $_POST['title'] : $title = null;
		$postTime = date("Y-m-d H:i:s", time());
		isset($_COOKIE['hash-name']) ? $hashName = $_COOKIE['hash-name'] : $hashName = null;
		($_SESSION["loginStatus"] == "passed")? $isAuthorised = true : $isAuthorised = false;
		try{
			$dbPrepared = $this->database->db->prepare("INSERT INTO posts (username, content, email, ip, title, time, hashname, isAuthorised) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
			$dbPrepared->execute(array($username, $content, $email, $clientIP, $title, $postTime, $hashName, $isAuthorised));
		} catch (PDOException $e){
			echo $e->getMessage();
		}
		$this->database->db = null;
		header("Location: http://{$_SERVER['SERVER_NAME']}/chatbox");
		die();
	}
}